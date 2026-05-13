<?php
/**
 * File Service file: where you can have utilities about files handling
 *
 * @package Export_Import_Acf_Menu_Data
 */

namespace ExportImportMenuAcfData\Services;

/**
 * FileService class
 *
 * Handles file
 *
 * @since 1.0.0
 */
class FileService {

    /**
     * The file_type_is_allowed function.
     *
     * Check if a given file is allowed
     *
     * @param $file Temp file from $_FILE
     * @return bool
     */
    public static function file_type_is_allowed( $file ): bool {
        if ( is_null( $file ) ) {
            return false;
        }

        $allowded      = array( 'application/json', 'text/json' ); // Accepting json files only for now.
        $imported_file = $file;
        $finfo         = new \finfo( FILEINFO_MIME_TYPE );
        $mime_type     = $finfo->file( $imported_file['tmp_name'] );

        return in_array( $mime_type, $allowded, true );
    }
}
