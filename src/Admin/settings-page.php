<?php
/**
 * The admin page file
 *
 * @package Export_Import_Acf_Menu_Data
 */

namespace ExportImportMenuAcfData\Services;

// Selectbox CSS
$css = [];
$tab = $_GET[ 'tab' ] ?? 'default';

// Show error message when no menu is selected
if ( isset ( $_POST[ EIMAD_SELECTBOX_MENU_NAME ] ) && $_POST[ EIMAD_SELECTBOX_MENU_NAME ] === '0' ) {
    wp_admin_notice(
        __( 'Please choose a menu in the dropdown list.', 'export-import-acf-menu-data' ),
        [
            'type' => 'error',
            'dismissible' => true
        ]
    );
}

// Import the JSON file when a file is selected
if ( 
    isset ( $_FILES[ EIMAD_INPUT_IMPORT_FILE_NAME ] ) 
    && $_FILES[ EIMAD_INPUT_IMPORT_FILE_NAME ][ 'tmp_name' ] !== '' 
) {
    MenuService::import();
}
?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
   
    <nav class="nav-tab-wrapper">  
        <a 
            href="?page=export-import-menu-acf-data&tab=export" 
            class="nav-tab <?php if( $tab === 'default' || $tab === 'export' ) : ?>nav-tab-active<?php endif; ?>"
        >
            Export
        </a>  
        <a 
            href="?page=export-import-menu-acf-data&tab=import" 
            class="nav-tab <?php if( $tab === 'import' ) : ?>nav-tab-active<?php endif; ?>">Settings</a>  
    </nav> 
    <div class="eimad_tab-content">
        <?php if ( $tab === 'export' || $tab === 'default' ) { ?>
            <p>Choose a menu from the dropdown below and export it as JSON file. You will be able to import it in the "Import tab". The export will grab all the ACF fields' values attached to menu items.</p>
            <form action="<?php echo admin_url( 'admin.php?page=export-import-menu-acf-data&tab=export' ); ?>" method="POST">
                <input type="hidden" name="action" value="<?php echo EIMAD_EXPORT_ACTION_HOOK_NAME ?>">
                <?php wp_nonce_field( EIMAD_NONCE_ACTION, EIMAD_NONCE_NAME ); ?>
                <div class="row">
                    <label for="eimad_menu">
                        Choose a menu
                    </label>
                    <?php MenuService::display_menu_select_list( EIMAD_SELECTBOX_MENU_NAME, EIMAD_SELECTBOX_MENU_ID ); ?>
                    <input type="submit" name="eimad_export-menu" class="button button-primary" value="Export Menu">
                </div>
            </form>
        <?php } else if ( $tab === 'import' ) { ?>
            <p>Choose a JSON file to import a menu and all its ACF field data.</p>
            <form action="<?php echo admin_url( 'admin.php?page=export-import-menu-acf-data&tab=import' ); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo EIMAD_IMPORT_ACTION_HOOK_NAME ?>">
                <?php wp_nonce_field( EIMAD_NONCE_ACTION, EIMAD_NONCE_NAME ); ?>
                <div class="row">
                    <label for="menu-name">
                        <span>Menu name*</span>
                        <input type="text" id="new-menu-name" name="new-menu-name" required>
                    </label>
                    <label for="menu-file">
                        Choose a file*
                        <input type="file" id="menu-file" name="<?php echo EIMAD_INPUT_IMPORT_FILE_NAME ?>" required>
                    </label>
                    <label for="menu-override" class="reverse">
                        Override if menu already exists
                        <input type="checkbox" id="menu-override" name="override-menu">
                    </label>
                    <input type="submit" name="eimad_import-menu" class="button button-primary" value="Import Menu">
                </div>
                <small class="required">*Required fields</small>
            </form>    
        <?php } ?>
    </div>
</div>
