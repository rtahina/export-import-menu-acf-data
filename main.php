<?php
/**
 * Plugin Name:     Export / Import ACF Menu Data
 * Requires Plugins: advanced-custom-fields
 * Plugin URI:      https://tahina.dev/
 * Description:     Exports and Imports the ACF data of a specific navigation menu. This plugin requires ACF or ACF Pro plugin to be installed and active.
 * Author:          Tahina R.
 * Author URI:      https://tahina.dev/
 * Text Domain:     export-import-acf-menu-data
 * Domain Path:     /languages
 * Version:         0.1.0
 * Requires PHP:    8.2
 *
 * @category WordPress_Plugin
 * @package  Export_Import_Acf_Menu_Data
 * @author   Tahina R
 * @license  https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @version  1.0.0
 * @link     https://tahina.dev/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use ExportImportMenuAcfData\ExportImportMenuAcfData;

define( 'EXPORT_IMPORT_MENU_ACF_DATA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EXPORT_IMPORT_MENU_ACF_DATA_TEXT_DOMAIN', 'export-import-acf-menu-data' );

// Autoload file.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Bootstrap the plugin
function eimad_bootstrap() {
    $app = ExportImportMenuAcfData::get_instance();
    $app->run();
}

eimad_bootstrap();
