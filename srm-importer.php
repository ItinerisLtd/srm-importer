<?php
/**
 * Plugin Name:     SRM Importer
 * Plugin URI:      https://www.itineris.co.uk/
 * Description:     Web UI for 10up/safe-redirect-manger importer
 * Version:         0.1.0
 * Author:          Itineris Limited
 * Author URI:      https://www.itineris.co.uk/
 * Text Domain:     srm-importer
 */

declare(strict_types=1);

namespace Itineris\SRMImporter;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Begins execution of the plugin.
 *
 * @return void
 */
function run(): void
{
    $plugin = new Plugin();
    $plugin->run();
}

run();
