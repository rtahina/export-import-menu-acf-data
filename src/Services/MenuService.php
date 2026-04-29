<?php
/**
 * Menu Service file: where you can have utilities about menus in general
 *
 * @package Export_Import_Acf_Menu_Data
 */

namespace ExportImportMenuAcfData\Services;

use ExportImportMenuAcfData\Models\Menu;

/**
 * MenuService class
 *
 * Handles the hooks of the plugin
 *
 * @since 1.0.0
 */
class MenuService {
    /**
     * The get_all_menus function.
     *
     * Get all the menus.
     *
     * @return Menu[]
     */
    public static function get_all_menus(): array {
        $menus = [];
        $all_menus = wp_get_nav_menus();
        foreach ( $all_menus as $menu ) {
            $menus[] = new Menu( $menu );
        }
        return $menus;
    }

    /**
     * The display_menu_select_list function.
     *
     * Display all naivgation menus in a select box.
     *
     * @param string $name The selectbox "name" attribute
     * @param string $id The selectbox "id" attribute
     * @param array $css An array that contains one or multiple CSS classes. It will be used as "class" attribute
     * @return void
     */
    public static function display_menu_select_list( 
        string $name = '',
        string $id = '',
        array $css = []
    ) {
        $menus = self::get_all_menus();
        $css_classes = join( ' ', $css );
        // $required = 
        ?>
        <select 
            name="<?php echo esc_attr( $name ) ?>" 
            id="<?php echo esc_attr( $id ) ?>" 
            class="<?php echo esc_attr( $css_classes ) ?>"
            required
        >
            <option value="0">Select a menu</option>
            <?php foreach ( $menus as $menu ) { ?>
                <option value="<?php echo $menu->id ?>">
                    <?php echo $menu->name ?>
                </option>
            <?php } ?>
        </select>
    <?php
    }

    /**
     * The export function.
     *
     * Export a menu with all its ACF data into a JSON file.
     *
     * @return void
     */
    public static function export() {
        $menu_id = $_POST[ EIMAD_SELECTBOX_MENU_NAME ] ?? 0;
        $menu_id = (int) $menu_id;

        if( $menu_id === 0 ) 
            return;

        $menu = get_term_by('id', $menu_id, 'nav_menu');
        $menu_name = $menu->slug ?? '';
        $nav_items = wp_get_nav_menu_items( $menu_id );
        if( is_array( $nav_items ) && !empty( $nav_items )) {
            $data = array();
            $count = 0;
            foreach ($nav_items as $nav) {
               $nav_metas = get_post_meta( $nav->ID);
               $data[$count]['post'] = $nav;
               $data[$count]['post_metas'] = $nav_metas;
               $count++;
            }
            $data = json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
            $filename = 'export_wp_menus_' . $menu_name . '_' . date( 'd-m-Y-G-i-s' ) . '.json';
            ob_clean();
            header( 'Content-Description: File Transfer' );
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate' );
            header( 'Pragma: public' );
            header( 'Content-Length: ' . strlen( $data ) );
            echo $data;
            exit;
        }
    }

    /**
     * The import function.
     *
     * Import a menu with all its ACF data from a JSON file.
     *
     * @return array
     */
    public static function import(): array {
        if ( 
            ! isset( $_FILES[ EIMAD_INPUT_IMPORT_FILE_NAME ] )
            ||  $_FILES[ EIMAD_INPUT_IMPORT_FILE_NAME ][ 'tmp_name' ] === ''
        ) {
            wp_admin_notice(
                __( 'Please, choose a file to import.', 'export-import-acf-menu-data' ),
                [
                    'type' => 'error',
                    'dismissible' => true
                ]
            );

            return [];
        }
        
        $file = ! empty ( $_FILES[ EIMAD_INPUT_IMPORT_FILE_NAME ] ) ? $_FILES[ EIMAD_INPUT_IMPORT_FILE_NAME ] : null;
        $menu_name = ! empty ( $_POST[ EIMAD_INPUT_NEW_MENU_NAME ] ) 
                    ? sanitize_text_field ( $_POST[ EIMAD_INPUT_NEW_MENU_NAME ] ) 
                    : 'New Menu';
        $override = isset( $_POST[ 'override_menu' ] );
        $is_allowed = FileService::file_type_is_allowed( $file );
        
        if ( FALSE === $is_allowed ) {
            wp_admin_notice(
                __( 'This file type is not allowed. Please choose a JSON file to import.', 'export-import-acf-menu-data' ),
                [
                    'type' => 'error',
                    'dismissible' => true
                ]
            );

            return [];
        }
        
        if ( TRUE === $is_allowed ) {
            $json_data = file_get_contents( $file[ 'tmp_name' ] );
            $data = json_decode( $json_data, true );

            if (json_last_error() !== JSON_ERROR_NONE) {
            
            return [
                    'type' => 'error',
                    'message' => 'Invalid JSON data: ' . json_last_error_msg()
                ];
            }
            
            // Check JSON format
            if ( ! self::check_json_format( $json_data ) ) {
                return [
                    'type' => 'error',
                    'message' => 'Invalid JSON format'
                ];
            }

            // Check if menu already exists and override is requested.
            if ( 
                is_nav_menu( $menu_name ) 
                && FALSE === $override 
            ) {
                return [
                    'success' => 'error',
                    'message' => 'This menu already exists. Choose another name or allow the override option.'
                ];
            }

            // Do the actual import.
            $response = self::do_import( $data );

            return $response;
        }
    }

    public function check_json_format( string $json_data ): bool {
        $data = json_decode( $json_data, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) return false;

        if ( ! is_array( $data ) ) return false;

        if ( count($data) === 0 ) return false;

        $array_data = $data[ 0 ];
        if ( ! array_key_exists( 'post', $array_data ) ) return false;

        return true;
    }

    public function do_import( array $data ): array {
        $data = ( array ) $data;

        foreach ( $data as $row ) {
            $post = $row->post;
            $post_meta = $row->post_metas;


        }

        return [
            'success' => true,
            'message' => 'Navigation menu imported'
        ];
    }
}
