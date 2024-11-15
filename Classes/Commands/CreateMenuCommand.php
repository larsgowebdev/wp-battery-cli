<?php

namespace Larsgowebdev\WpBatteryCli\Commands;

use Larsgowebdev\WPBattery\Utility\PathUtility;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Command for creating blocks
 */
class CreateMenuCommand extends AbstractBlueprintCommand
{
    public function __construct()
    {
        $this->blueprintType = 'menus';
        $this->blueprintTypeName = 'Menu';
        $this->markers = [
            'menu-name' => 'name'
        ];
        $this->targetDirectory = PathUtility::getThemeDirectory() . '/wpb/menus';
        $this->createSubfolder = false;
    }
}