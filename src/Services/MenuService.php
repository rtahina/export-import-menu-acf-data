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
        $menus     = array();
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
     * @param string $name The selectbox "name" attribute.
     * @param string $id The selectbox "id" attribute.
     * @param array  $css An array that contains one or multiple CSS classes. It will be used as "class" attribute.
     * @return void
     */
    public static function display_menu_select_list(
        string $name = '',
        string $id = '',
        array $css = array()
    ) {
        $menus       = self::get_all_menus();
        $css_classes = join( ' ', $css );
        // $required =
        ?>
        <select 
            name="<?php echo esc_attr( $name ); ?>" 
            id="<?php echo esc_attr( $id ); ?>" 
            class="<?php echo esc_attr( $css_classes ); ?>"
            required
        >
            <option value="0">Select a menu</option>
            <?php foreach ( $menus as $menu ) { ?>
                <option value="<?php echo esc_attr( $menu->id ); ?>">
                    <?php echo esc_html( $menu->name ); ?>
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
        $menu_id = ! empty( $_POST[ EIMAD_SELECTBOX_MENU_NAME ] ) ? (int) $_POST[ EIMAD_SELECTBOX_MENU_NAME ] : 0;
        $menu_id = (int) $menu_id;

        if ( 0 === $menu_id ) {
            return;
        }

        $menu      = get_term_by( 'id', $menu_id, 'nav_menu' );
        $menu_name = $menu->slug ?? '';
        $nav_items = wp_get_nav_menu_items( $menu_id );
        if ( is_array( $nav_items ) && ! empty( $nav_items ) ) {
            $data  = array();
            $count = 0;

            foreach ( $nav_items as $nav ) {
                $nav_metas                    = get_post_meta( $nav->ID );
                $data[ $count ]['post']       = $nav;
                $data[ $count ]['post_metas'] = $nav_metas;
                ++$count;
            }

            $data = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
            // TODO: current user time.
            $filename = 'export_wp_menus_' . $menu_name . '_' . gmdate( 'd-m-Y-G-i-s' ) . '.json';
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
    public static function import(): mixed {
        if ( ! isset( $_FILES[ EIMAD_INPUT_IMPORT_FILE_NAME ] )
            || $_FILES[ EIMAD_INPUT_IMPORT_FILE_NAME ]['tmp_name'] === ''
        ) {
            return array(
                'success' => false,
                'message' => __( 'Please, choose a file to import.', 'export-import-acf-menu-data' ),
            );
        }

        $file       = ! empty( $_FILES[ EIMAD_INPUT_IMPORT_FILE_NAME ] ) ? $_FILES[ EIMAD_INPUT_IMPORT_FILE_NAME ] : null;
        $menu_name  = ! empty( $_POST[ EIMAD_INPUT_NEW_MENU_NAME ] )
                    ? sanitize_text_field( $_POST[ EIMAD_INPUT_NEW_MENU_NAME ] )
                    : 'New Menu';
        $is_allowed = FileService::file_type_is_allowed( $file );

        if ( false === $is_allowed ) {
            return array(
                'success' => false,
                'message' => __( 'This file type is not allowed. Please choose a JSON file to import.', 'export-import-acf-menu-data' ),
            );
        }

        if ( true === $is_allowed ) {
            $json_data = file_get_contents( $file['tmp_name'] );
            $data      = json_decode( $json_data, true );

            if ( json_last_error() !== JSON_ERROR_NONE ) {
                return array(
                    'type'    => 'error',
                    'message' => sprintf( __( 'Invalid JSON data: %s', 'export-import-acf-menu-data' ), json_last_error_msg() ),
                );
            }

            // Check JSON format
            if ( ! self::check_json_format( $json_data ) ) {
                return array(
                    'type'    => 'error',
                    'message' => __( 'Invalid JSON format', 'export-import-acf-menu-data' ),
                );
            }

            // Check if menu already exists and override if requested.
            if ( is_nav_menu( $menu_name )
            ) {
                return array(
                    'success' => 'error',
                    'message' => sprintf( __( 'The menu "%s" already exists. Please, choose another name.', 'export-import-acf-menu-data' ), $menu_name ),
                );
            }

            // Create menu
            $menu_id = wp_create_nav_menu( $menu_name );

            // Do the actual import.
            $response = ( new self() )->do_import( $menu_id, $data );

            return $response;
        }
    }

    public static function check_json_format( string $json_data ): bool {
        $data = json_decode( $json_data, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return false;
        }

        if ( ! is_array( $data ) ) {
            return false;
        }

        if ( count( $data ) === 0 ) {
            return false;
        }

        $array_data = $data[0];
        if ( ! array_key_exists( 'post', $array_data ) ) {
            return false;
        }

        return true;
    }

    public function do_import( int $menu_id, array $data ): array {
        $new_ids = array();
        $data    = (array) $data;
        $count   = 0;

        foreach ( $data as $row ) {
            $post        = (array) $row['post'];
            $post_meta   = (array) $row['post_metas'];
            $tmp_post_id = $post['ID'];
            $post['ID']  = 0; // So that a new nav menu item is created.
            $title       = '' !== $post['post_title'] ? $post['post_title'] : $post['title'];
            $post_parent = 0;

            // Handle post parent.
            if ( $post['menu_item_parent'] > 0 ) {
                $post_id_key = (string) $post['menu_item_parent'];
                $post_parent = ! empty( $new_ids[ $post_id_key ] )
                                ? $new_ids[ $post_id_key ]
                                : 0;
            }

            // Create a new menu item.
            $post_id = wp_insert_post( $post, true );

            if ( $post_id instanceof \WP_Error ) {
                return array(
                    'success' => 'error',
                    'message' => sprintf( __( 'There was an error creating "%s" menu item.', 'export-import-acf-menu-data' ), $title ),
                );
            }

            // Save new ID.
            $old_id_key             = (string) $tmp_post_id;
            $new_ids[ $old_id_key ] = $post_id;

            // Post metas.
            $menu_item_data = array();
            foreach ( $post_meta as $key => $value ) {
                $val = $value[0];

                // Menu metas.
                if ( str_contains( $key, '_menu' ) ) {
                    $key = trim( $key, '_' );
                    $key = str_replace( '_', '-', $key );

                    if ( 'menu-item-menu-item-parent' === $key ) {
                        $menu_item_data['menu-item-parent-id'] = (int) $post_parent;
                    } else {
                        $menu_item_data[ $key ] = $val;
                    }
                }

                // ACF fields.
                if ( AcfService::is_acf_field( $key ) ) {
                    AcfService::update_field( $key, $val, $post_id );
                }
            }

            // Update post meta.
            $menu_item_data['menu-item-title']       = $title;
            $menu_item_data['menu-item-description'] = $post['description'];

            wp_update_nav_menu_item( $menu_id, $post_id, $menu_item_data );

            ++$count;
        }

        return array(
            'success' => true,
            'message' => sprintf( __( '%d navigation menu items successfully imported.', 'export-import-acf-menu-data' ), $count ),
        );
    }
}
