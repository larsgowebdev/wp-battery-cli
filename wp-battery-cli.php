<?php
/*
Plugin Name: WP-Battery CLI Commands
Description: A plugin which registers WP-CLI commands to generate blocks, etc.
Version: 0.6.4
Author: larsgowebdev
*/

// Ensure the code is only executed in the context of WordPress.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register the custom command with WP-CLI
if ( defined('WP_CLI') && WP_CLI ) {
    define( 'WP_BATTERY_CLI_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

    \WP_CLI::add_command('init-wpb', [\Larsgowebdev\WPBatteryCli\Commands\InitWpbCommand::class, 'run']);
    \WP_CLI::add_command('create-wpb-block', [\Larsgowebdev\WPBatteryCli\Commands\CreateBlockCommand::class, 'run']);
    \WP_CLI::add_command('create-wpb-page', [\Larsgowebdev\WPBatteryCli\Commands\CreatePageCommand::class, 'run']);
    \WP_CLI::add_command('create-wpb-menu', [\Larsgowebdev\WPBatteryCli\Commands\CreateMenuCommand::class, 'run']);
    \WP_CLI::add_command('create-wpb-options', [\Larsgowebdev\WPBatteryCli\Commands\CreateOptionsCommand::class, 'run']);
}