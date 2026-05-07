<?php
/**
 * ACF Service file
 *
 * @package Export_Import_Acf_Menu_Data
 */

namespace ExportImportMenuAcfData\Services;

/**
 * AcfService class
 *
 * @since 1.0.0
 */
class AcfService {
    /**
     * The get_all_menu_field_groups function.
     *
     * Get all the ACF field groups that have "Menu item is equal to All" as location.
     *
     * @return array
     */
    public function get_all_menu_acf_field_groups(): array {
        $menu_field_groups = array();

        if ( function_exists( 'acf_get_field_groups' ) ) {
            $field_groups = acf_get_field_groups();

            foreach ( $field_groups as $group ) {
                if ( empty( $group['location'] ) ) {
                    continue;
                }

                foreach ( $group['location'] as $locations ) {
                    foreach ( $locations as $rule ) {
                        if (
                            isset( $rule['param'], $rule['operator'], $rule['value'] )
                            && 'nav_menu_item' === $rule['param']
                            && '==' === $rule['operator']
                            && 'all' === $rule['value']
                        ) {
                            $menu_field_groups[] = $group;
                            break 2;
                        }
                    }
                }
            }
        }

        return $menu_field_groups;
    }

    /**
     * The get_all_menu_acf_fields function.
     *
     * Get all the ACF field attached to all navigation menu.
     *
     * @return array
     */
    public static function get_all_menu_acf_fields(): array {
        $menu_field_groups = ( new self() )->get_all_menu_acf_field_groups();
        $acf_fields        = array();

        if ( function_exists( 'acf_get_fields' ) ) {
            foreach ( $menu_field_groups as $field_group ) {
                $fields = acf_get_fields( $field_group['ID'] );
                foreach ( $fields as $field ) {
                    $acf_fields[] = $field['name'];
                }
            }
        }

        return $acf_fields;
    }

    /**
     * The is_acf_field function.
     *
     * Check if $field is a ACF field
     *
     * @return bool
     */
    public static function is_acf_field( string $field ): bool {
        $acf_fields = self::get_all_menu_acf_fields();
        return in_array( $field, $acf_fields, true );
    }

    /**
     * The update_field function.
     *
     * A wrapper for the actual ACF update_field()
     *
     * @return bool
     */
    public static function update_field( string $key, string $value, int $post_id ): mixed {
        if ( function_exists( 'update_field' ) ) {
            return update_field( $key, $value, $post_id );
        }

        return false;
    }
}
