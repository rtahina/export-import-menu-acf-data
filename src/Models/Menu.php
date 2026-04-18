<?php
/**
 * Menu model file
 *
 * @package Export_Import_Acf_Menu_Data
 */

namespace ExportImportMenuAcfData\Models;

/**
 * Menu class
 *
 * Menu model
 *
 * @since 1.0.0
 */
class Menu {
    /**
     * Menu ID.
     *
     * @var int $id Menu ID.
     */
    public int $id;

    /**
     * Menu name.
     *
     * @var string $name Menu name (label).
     */
    public string $name;

    /**
     * Menu slug.
     *
     * @var string $slug Menu slug. All lower case and sanitized with dash.
     */
    public string $slug;

    /**
     * Menu parent ID.
     *
     * @var int $parent Menu parent ID.
     */
    public int $parent;

    /**
     * Menu children number.
     *
     * @var int $count The menu's number of children.
     */
    public int $count;

    // phpcs:disable Squiz.Commenting.FunctionComment.Missing
    public function __construct( \WP_Term $menu ) {
        // phpcs:enable
        $this->id     = $menu->term_id;
        $this->name   = $menu->name;
        $this->slug   = $menu->slug;
        $this->parent = $menu->parent;
        $this->count  = $menu->count;
    }
}
