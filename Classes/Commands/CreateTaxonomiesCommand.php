<?php

namespace Larsgowebdev\WpBatteryCli\Commands;

use Larsgowebdev\WPBattery\Utility\PathUtility;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Command for creating options
 */
class CreateTaxonomiesCommand extends AbstractBlueprintCommand
{
    public function __construct()
    {
        $this->blueprintType = 'taxonomies';
        $this->blueprintTypeName = 'Taxonomy';
        $this->requiredArgs = ['name', 'namespace', 'post-type'];
        $this->markers = [
            'taxonomy-name' => 'name',
            'theme-domain' => 'namespace',
            'post-type' => 'post-type'
        ];
        $this->targetDirectory = PathUtility::getThemeDirectory() . '/wpb/taxonomies';
        $this->createSubfolder = false;
    }
}