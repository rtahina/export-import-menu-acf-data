<?php
/**
 * Export Import Menu Acf Data File
 *
 * @package Export_Import_Acf_Menu_Data
 */

namespace ExportImportMenuAcfData;

/**
 * ExportImportMenuAcfData class
 *
 * Handles the hooks of the plugin
 *
 * @since 1.0.0
 */
class ExportImportMenuAcfData {

    private static $instance = NULL;

    // phpcs:disable Squiz.Commenting.FunctionComment.Missing
    public function __construct() {
        // phpcs:enable
    }

    /**
     * The get_instance function.
     *
     * Return an instance of ExportImportMenuAcfData
     *
     * @return self.
     */
    public static function get_instance(): self {
        if ( NULL !==  static::$instance ) return static::$instance;
        else static::$instance = new ExportImportMenuAcfData();

        return static::$instance;
    }

    /**
     * The run function.
     *
     * Runs all needed functions
     *
     * @return void.
     */
    public function run() {
        $this->load_language( EXPORT_IMPORT_MENU_ACF_DATA_TEXT_DOMAIN );
        $this->hooks();
    }

    /**
     * The hooks function.
     *
     * Fires hooks
     *
     * @return void.
     */
    protected function hooks() {
        // Runs once when the plugin is first activated.
        register_activation_hook(
            EXPORT_IMPORT_MENU_ACF_DATA_PLUGIN_PATH . 'main.php',
            function () {
                if ( 
                    is_plugin_active( 'advanced-custom-fields/acf.php' ) 
                    || is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) 
                ) {
                    printf(
                        '<div class="notice notice-error"><p>%s</p></div>',
                        esc_html( 'The ACF plugin is required.' )
                    );
                }
            }
        );

        // 1. Register a submenu page
        add_action(
            'admin_menu',
            function () {
                add_submenu_page(
                    'tools.php',
                    'Export/Import Menu ACF Data',
                    'Export/Import Menu ACF Data',
                    'manage_options',
                    'export-import-menu-acf-data',
                    array( $this, 'admin_page' )
                );
            }
        );
    }

    /**
     * The admin_page function.
     *
     * Retrieve the admin page template
     *
     * @return void.
     */
    public function admin_page() {
        // Verify if we are in the admin area and the current user has permission.
        if ( is_admin() && ! Utilities::current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have permission to access this page.' );
        }

        $template = plugin_dir_path( __FILE__ ) . 'admin/settings-page.php';
        
        if ( file_exists( $template ) ) {
            include $template;
        } else {
            printf(
                '<div class="notice notice-error"><p>%s <code>%s</code></p></div>',
                esc_html( 'Admin template not found:' ),
                esc_html( $template )
            );
        }
    }

    /**
	 * Loads translation file.
	 *
	 * Accessible to other classes to load different language files (admin and
	 * front-end for example).
	 *
	 * @return  void
	 */
	public function load_language( $domain ) {
		load_plugin_textdomain(
			$domain,
			FALSE,
			EXPORT_IMPORT_MENU_ACF_DATA_PLUGIN_PATH . 'languages'
		);
	}
}
