<?php
/**
 * Created by PhpStorm.
 * User: mmcmurray
 * Date: 12/22/18
 * Time: 5:35 AM
 */

//conditional includes

if ( !is_admin() ) {
    //include_once('public/public.php');
}else if ( defined( 'DOING_AJAX' )) {
    include_once('src/ajax.php');

    //Stats
    add_action( 'wp_ajax_go_stats_lightbox', 'go_stats_lightbox' ); //OK
    add_action( 'wp_ajax_go_stats_task_list', 'go_stats_task_list' ); //OK
    add_action( 'wp_ajax_go_stats_store_list', 'go_stats_store_list' ); //OK
    add_action( 'wp_ajax_go_stats_activity_list', 'go_stats_activity_list' ); //OK
    add_action( 'wp_ajax_go_stats_messages', 'go_stats_messages' ); //OK
    add_action( 'wp_ajax_go_stats_single_task_activity_list', 'go_stats_single_task_activity_list' ); //OK
    add_action( 'wp_ajax_go_stats_badges_list', 'go_stats_badges_list' ); //OK
    add_action( 'wp_ajax_go_stats_groups_list', 'go_stats_groups_list' ); //OK
    //add_action( 'wp_ajax_go_stats_lite', 'go_stats_lite' ); //OK
    add_action( 'wp_ajax_go_stats_about', 'go_stats_about' ); //OK
    add_action( 'wp_ajax_go_activity_dataloader_ajax', 'go_activity_dataloader_ajax');
    add_action( 'wp_ajax_go_messages_dataloader_ajax', 'go_messages_dataloader_ajax');
    add_action( 'wp_ajax_go_stats_store_item_dataloader', 'go_stats_store_item_dataloader');
    add_action( 'wp_ajax_go_tasks_dataloader_ajax', 'go_tasks_dataloader_ajax');
    add_action( 'wp_ajax_go_print_grade_scales', 'go_print_grade_scales');
    add_action( 'wp_ajax_go_update_badges_page', 'go_update_badges_page');


}else{
    //include_once('admin/admin.php');
}

//always include
include_once('src/functions.php');