<?php
/**
 * Utilities class File
 *
 * @package Export_Import_Acf_Menu_Data
 */

namespace ExportImportMenuAcfData;

/**
 * Utilities class
 *
 * Class of Utility functions.
 *
 * @since 1.0.0
 */
class Utilities {
    /**
     * The current_user_can function.
     *
     * Checks if current user has capabilities
     *
     * @param string $capability Capability that we want to check current user against.
     * @return bool True if current user has the capability, false otherwise.
     */
    public static function current_user_can( string $capability ): bool {
        if ( ! function_exists( 'wp_get_current_user' ) ) {
            include ABSPATH . 'wp-includes/pluggable.php';
        }
        return current_user_can( $capability );
    }
}
