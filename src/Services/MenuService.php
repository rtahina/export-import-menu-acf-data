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
     * @return void
     */
    public static function display_menu_select_list() {
        $menus = self::get_all_menus();
        ?>
        <select name="" id="">
            <option value="0">Select a menu</option>
            <?php foreach ( $menus as $menu ) { ?>
                <option value="<?php echo $menu->id ?>">
                    <?php echo $menu->name ?>
                </option>
            <?php } ?>
        </select>
    <?php
    }
}
