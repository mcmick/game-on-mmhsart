<?php
/**
 * Created by PhpStorm.
 * User: mcmurray
 * Date: 2019-06-02
 * Time: 20:32
 */

function go_delete_temp_archive($dirPath = false){
    //check_ajax_referer( 'go_clipboard_activity_' . get_current_user_id() );
    if ( ! wp_verify_nonce( $_REQUEST['_ajax_nonce'], 'go_delete_temp_archive' ) ) {
        echo "refresh";
        die( );
    }

    go_delete_temp_archive_helper($dirPath);
}

function go_delete_temp_archive_helper($dirPath = false){

    if (!$dirPath) {
        $current_user_id = get_current_user_id();
        $dirPath = plugin_dir_path(__FILE__) . 'archive_temp/' . $current_user_id . '/';
    }
    if (is_file($dirPath)){
        unlink($dirPath);
    }

    if(is_dir($dirPath)) {

        $it = new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dirPath);
    }
}