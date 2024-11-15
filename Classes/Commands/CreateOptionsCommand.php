<?php

namespace Larsgowebdev\WpBatteryCli\Commands;

use Larsgowebdev\WPBattery\Utility\PathUtility;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Command for creating options
 */
class CreateOptionsCommand extends AbstractBlueprintCommand
{
    public function __construct()
    {
        $this->blueprintType = 'options';
        $this->blueprintTypeName = 'Options';
        $this->markers = [
            'option-name' => 'name'
        ];
        $this->targetDirectory = PathUtility::getThemeDirectory() . '/wpb/options';
        $this->createSubfolder = false;
    }
}