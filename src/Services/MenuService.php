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
    public static function display_menu_select_list( string $name = '', string $id = '', array $css = [] ) {
        $menus = self::get_all_menus();
        $css_classes = join( ' ', $css );
        ?>
        <select name="<?php echo esc_attr( $name ) ?>" id="<?php echo esc_attr( $id ) ?>" class="<?php echo esc_attr( $css_classes ) ?>">
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
     * @param string $menu_name The name of the navigation menu to export.
     * @return void
     */
    public static function export( int $menu_id ) {
        if( $menu_id === 0 ) {
            wp_admin_notice(
                __( 'Please choose a menu.', 'export-import-acf-menu-data' ),
                [
                    'type' => 'error',
                    'dismissible' => true
                ]
            );
        }
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
}
