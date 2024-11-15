<?php

namespace Larsgowebdev\WpBatteryCli\Commands;

use FilesystemIterator;
use Larsgowebdev\WPBattery\Utility\PathUtility;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Base class for blueprint-based commands
 */
abstract class AbstractBlueprintCommand
{
    protected string $blueprintType;
    protected string $blueprintTypeName;
    protected array $requiredArgs = ['name'];
    protected array $markers = [];
    protected string $targetDirectory;
    protected bool $createSubfolder = true;

    /**
     * Validates that a string is a valid URL slug
     *
     * @param string $name The name to validate
     * @return bool|string Returns true if valid, or error message if invalid
     */
    protected function validateSlugName(string $name): bool|string
    {
        // Convert to lowercase
        $slug = strtolower($name);

        // Check if the name contains any invalid characters
        if (!preg_match('/^[a-z0-9-_]+$/', $slug)) {
            return "Name must contain only lowercase letters, numbers, hyphens, and underscores.";
        }

        // Check if the name starts or ends with a hyphen or underscore
        if (preg_match('/^[-_]|[-_]$/', $slug)) {
            return "Name cannot start or end with a hyphen or underscore.";
        }

        // Check if the name contains consecutive hyphens or underscores
        if (preg_match('/[-_]{2,}/', $slug)) {
            return "Name cannot contain consecutive hyphens or underscores.";
        }

        // Check length (optional, adjust min/max as needed)
        if (strlen($slug) < 2 || strlen($slug) > 50) {
            return "Name must be between 2 and 50 characters long.";
        }

        return true;
    }

    /**
     * Sanitizes a string to a valid URL slug
     *
     * @param string $name The name to sanitize
     * @return string The sanitized slug
     */
    protected function sanitizeSlugName(string $name): string
    {
        // Convert to lowercase
        $slug = strtolower($name);

        // Replace spaces and multiple hyphens/underscores with a single hyphen
        $slug = preg_replace('/[\s-_]+/', '-', $slug);

        // Remove all characters that aren't letters, numbers, hyphens, or underscores
        $slug = preg_replace('/[^a-z0-9-_]/', '', $slug);

        // Remove leading and trailing hyphens/underscores
        $slug = trim($slug, '-_');

        return $slug;
    }

    /**
     * Sanitizes a string to a valid PHP function name
     *
     * @param string $name The name to sanitize
     * @return string The sanitized function name
     */
    protected function sanitizeFunctionName(string $name): string
    {
        // Convert to lowercase
        $name = strtolower($name);

        // Replace any non-alphanumeric characters (except underscores) with underscore
        $name = preg_replace('/[^a-z0-9_]/', '_', $name);

        // Remove consecutive underscores
        $name = preg_replace('/_+/', '_', $name);

        // Remove leading/trailing underscores
        $name = trim($name, '_');

        // Ensure it doesn't start with a number
        if (preg_match('/^[0-9]/', $name)) {
            $name = 'func_' . $name;
        }

        return $name;
    }

    /**
     * Common run method for all blueprint commands
     */
    public function run($args, $assoc_args): void
    {
        if (!defined('WP_BATTERY_CLI_PLUGIN_PATH')) {
            \WP_CLI::error("Plugin Path Constant WP_BATTERY_CLI_PLUGIN_PATH not defined");
            exit;
        }

        // Validate required arguments
        foreach ($this->requiredArgs as $arg) {
            if (!isset($assoc_args[$arg])) {
                \WP_CLI::error("Required parameter missing: --$arg");
                exit;
            }
        }

        // Validate and sanitize the name
        $nameValidation = $this->validateSlugName($assoc_args['name']);
        if ($nameValidation !== true) {
            $sanitized = $this->sanitizeSlugName($assoc_args['name']);
            \WP_CLI::error(
                $nameValidation . "\n" .
                "Suggestion: Use this valid name instead: " . $sanitized . "\n" .
                "Example usage: wp create-wpb-" . $this->blueprintType . " --name=" . $sanitized
            );
            exit;
        }

        // Ensure name is lowercase
        $assoc_args['name'] = strtolower($assoc_args['name']);

        // Check if component already exists
        if ($this->componentExists($assoc_args['name'])) {
            $componentType = ucfirst($this->blueprintType);
            if ($this->createSubfolder) {
                \WP_CLI::error(
                    "{$componentType} '{$assoc_args['name']}' already exists at:\n" .
                    $this->targetDirectory . DIRECTORY_SEPARATOR . $assoc_args['name']
                );
            } else {
                \WP_CLI::error(
                    "{$componentType} '{$assoc_args['name']}' already exists in:\n" .
                    $this->targetDirectory
                );
            }
            exit;
        }

        try {
            // Ensure name is lowercase
            $assoc_args['name'] = strtolower($assoc_args['name']);

            // Prepare the directories
            $blueprintSource = WP_BATTERY_CLI_PLUGIN_PATH . 'blueprints/' . $this->blueprintType;
            $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('blueprint_', true);

            // Process the blueprint
            $this->copyBlueprintToTempDir($blueprintSource, $tempDir);
            $this->processBlueprint($tempDir, $assoc_args);

            if ($this->createSubfolder) {
                $this->moveToTarget($tempDir, $assoc_args['name']);
            } else {
                $this->moveFilesToTarget($tempDir);
            }

            // Cleanup
            $this->removeDir($tempDir);

            \WP_CLI::success(ucfirst($this->blueprintTypeName) . " has been created!");
        } catch (\Exception $e) {
            \WP_CLI::error($e->getMessage());
        }
    }

    /**
     * Checks if the component already exists
     *
     * @param string $name Component name
     * @return bool
     */
    protected function componentExists(string $name): bool
    {
        if ($this->createSubfolder) {
            // For blocks and pages, check if directory exists
            $targetPath = $this->targetDirectory . DIRECTORY_SEPARATOR . $name;
            return is_dir($targetPath);
        } else {
            // For menus and options, check if any target files would exist
            $blueprintDir = WP_BATTERY_CLI_PLUGIN_PATH . 'blueprints/' . $this->blueprintType;
            $dirIterator = new RecursiveDirectoryIterator($blueprintDir, FilesystemIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    $relativePath = str_replace($blueprintDir . DIRECTORY_SEPARATOR, '', $item->getPathname());
                    // Replace blueprint markers in filename
                    foreach ($this->markers as $marker => $argKey) {
                        $relativePath = str_replace("__" . $marker . "__", $name, $relativePath);
                    }
                    // Remove .blueprint extension
                    $relativePath = preg_replace('/\.blueprint$/', '', $relativePath);

                    $targetFile = $this->targetDirectory . DIRECTORY_SEPARATOR . $relativePath;
                    if (file_exists($targetFile)) {
                        return true;
                    }
                }
            }
            return false;
        }
    }

    /**
     * Copy blueprint to temporary directory
     */
    protected function copyBlueprintToTempDir($blueprintDir, $tempDir): void
    {
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $dirIterator = new RecursiveDirectoryIterator($blueprintDir, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            $destPath = $tempDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            if ($item->isDir()) {
                mkdir($destPath);
            } else {
                copy($item, $destPath);
            }
        }
    }

    /**
     * Process blueprint files by replacing markers
     */
    protected function processBlueprint($dir, $args): void
    {
        $dirIterator = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            if ($item->isFile()) {
                // Replace content markers
                $content = file_get_contents($item);

                // First, handle function name replacements
                foreach ($this->markers as $marker => $argKey) {
                    $value = $args[$argKey] ?? '';
                    $functionName = $this->sanitizeFunctionName($value);
                    $content = str_replace("###" . $marker . "_function_name###", $functionName, $content);
                }

                // Then handle regular replacements
                foreach ($this->markers as $marker => $argKey) {
                    $value = $args[$argKey] ?? '';
                    $content = str_replace("###" . $marker . "###", $value, $content);
                }

                file_put_contents($item, $content);
            }

            $path = $item->getPathname();

            // Replace filename markers
            $newPath = $path;
            foreach ($this->markers as $marker => $argKey) {
                $value = $args[$argKey] ?? '';
                $newPath = str_replace("__" . $marker . "__", $value, $newPath);
            }

            // Remove .blueprint extension if present
            if (str_ends_with($newPath, '.blueprint')) {
                $newPath = substr($newPath, 0, -10);
            }

            // Rename if path has changed
            if ($path !== $newPath) {
                rename($path, $newPath);
            }
        }
    }

    /**
     * Move processed files to target directory
     */
    protected function moveToTarget($tempDir, $name): void
    {
        $targetPath = $this->targetDirectory . DIRECTORY_SEPARATOR . $name;

        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }

        $dirIterator = new RecursiveDirectoryIterator($tempDir, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            $destPath = $targetPath . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            if ($item->isDir()) {
                mkdir($destPath);
            } else {
                copy($item, $destPath);
            }
        }
    }

    /**
     * Move processed files directly to target directory without creating a subfolder
     */
    protected function moveFilesToTarget($tempDir): void
    {
        if (!is_dir($this->targetDirectory)) {
            mkdir($this->targetDirectory, 0755, true);
        }

        $dirIterator = new RecursiveDirectoryIterator($tempDir, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $relativePath = str_replace($tempDir . DIRECTORY_SEPARATOR, '', $item->getPathname());
                $destPath = $this->targetDirectory . DIRECTORY_SEPARATOR . $relativePath;

                // Create parent directories if they don't exist
                $destDir = dirname($destPath);
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }

                // Don't overwrite existing files
                if (!file_exists($destPath)) {
                    copy($item, $destPath);
                } else {
                    \WP_CLI::warning("File already exists, skipping: " . basename($destPath));
                }
            }
        }
    }

    /**
     * Clean up temporary directory
     */
    protected function removeDir($dir): void
    {
        $dirIterator = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item);
            } else {
                unlink($item);
            }
        }

        rmdir($dir);
    }
}