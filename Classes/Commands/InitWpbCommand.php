<?php

namespace Larsgowebdev\WPBatteryCli\Commands;

use Larsgowebdev\WPBattery\Utility\PathUtility;

if (!defined('ABSPATH')) {
    exit;
}

class InitWpbCommand
{
    /**
     * Base directories that should be created
     */
    private array $directories = [
        'acf-sync',
        'blocks',
        'cf7-templates',
        'menus',
        'options',
        'post-types',
        'taxonomies',
        'pages',
        'template-parts',
    ];

    /**
     * Command to initialize WP-Battery folder structure
     * @when after_wp_load
     */
    public function run($args, $assoc_args): void
    {
        if (!defined('WP_BATTERY_CLI_PLUGIN_PATH')) {
            \WP_CLI::error("Plugin Path Constant WP_BATTERY_CLI_PLUGIN_PATH not defined");
            exit;
        }

        try {
            $this->createFolderStructure();
            \WP_CLI::success("WP-Battery folder structure has been initialized!");
        } catch (\Exception $e) {
            \WP_CLI::error($e->getMessage());
        }
    }

    /**
     * Creates the folder structure for WP-Battery
     * @throws \Exception
     */
    private function createFolderStructure(): void
    {
        // Get base WPB directory
        $baseDir = PathUtility::getThemeDirectory() . DIRECTORY_SEPARATOR . 'wpb';

        // Create base directory if it doesn't exist
        if (!is_dir($baseDir)) {
            if (!mkdir($baseDir, 0755, true)) {
                throw new \Exception("Failed to create base directory: $baseDir");
            }
            \WP_CLI::log("Created base directory: wpb/");
        }

        // Create each subdirectory
        foreach ($this->directories as $directory) {
            $fullPath = $baseDir . DIRECTORY_SEPARATOR . $directory;

            // Skip if directory already exists
            if (is_dir($fullPath)) {
                \WP_CLI::log("Directory already exists: wpb/$directory/");
                continue;
            }

            // Create directory
            if (!mkdir($fullPath, 0755, true)) {
                throw new \Exception("Failed to create directory: wpb/$directory/");
            }

            // Add .gitkeep file
            $gitkeepPath = $fullPath . DIRECTORY_SEPARATOR . '.gitkeep';
            if (!file_put_contents($gitkeepPath, '')) {
                throw new \Exception("Failed to create .gitkeep in: wpb/$directory/");
            }

            \WP_CLI::log("Created directory: wpb/$directory/");
        }
    }
}