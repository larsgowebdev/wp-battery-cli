<?php

namespace Larsgowebdev\WpBatteryCli\Commands;

use Larsgowebdev\WPBattery\Utility\PathUtility;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Command for creating blocks
 */
class CreateBlockCommand extends AbstractBlueprintCommand
{
    public function __construct()
    {
        $this->blueprintType = 'block';
        $this->requiredArgs = ['name', 'title'];
        $this->markers = [
            'block-name' => 'name',
            'block-title' => 'title'
        ];
        $this->targetDirectory = PathUtility::getBlocksDirectory();
        $this->createSubfolder = true;
    }
}