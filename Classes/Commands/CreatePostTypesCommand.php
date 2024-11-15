<?php

namespace Larsgowebdev\WpBatteryCli\Commands;

use Larsgowebdev\WPBattery\Utility\PathUtility;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Command for creating options
 */
class CreatePostTypesCommand extends AbstractBlueprintCommand
{
    public function __construct()
    {
        $this->blueprintType = 'post-types';
        $this->blueprintTypeName = 'Post Type';
        $this->requiredArgs = ['name', 'namespace'];
        $this->markers = [
            'post-type-name' => 'name',
            'theme-domain' => 'namespace',
        ];
        $this->targetDirectory = PathUtility::getThemeDirectory() . '/wpb/post-types';
        $this->createSubfolder = false;
    }
}