<?php

namespace Larsgowebdev\WPBatteryCli\Commands;

use Larsgowebdev\WPBattery\Utility\PathUtility;

/**
 * Command for creating pages
 */
class CreatePageCommand extends AbstractBlueprintCommand
{
    public function __construct()
    {
        $this->blueprintType = 'pages';
        $this->blueprintTypeName = 'Page';
        $this->requiredArgs = ['name'];
        $this->markers = [
            'page-name' => 'name'
        ];
        $this->targetDirectory = PathUtility::getThemeDirectory() . '/wpb/pages';
        $this->createSubfolder = true;  // Pages should have their own subfolder
    }
}