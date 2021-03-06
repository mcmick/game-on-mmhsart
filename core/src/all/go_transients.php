<?php
/**
 * Created by PhpStorm.
 * User: mmcmurray
 * Date: 9/9/18
 * Time: 9:32 PM
 */


function go_set_global_transient($key, $data, $expiration){
    if (  extension_loaded( 'apc' ) && ini_get( 'apc.enabled' )  ) {
        apc_store($key, $data, $expiration);
    }else{
        set_transient($key, $data, $expiration );
    }
}

function go_get_global_transient($key){
    if (  extension_loaded( 'apc' ) && ini_get( 'apc.enabled' )  ) {
        $data = apc_fetch( $key );
    }else {

        $data = get_transient($key);
    }
    return $data;
}

function go_delete_global_transient($key){
    if (  extension_loaded( 'apc' ) && ini_get( 'apc.enabled' )  ) {
        apc_delete( $key );
    }else {

        delete_transient($key);
    }
}

function go_set_transient($key, $data, $expiration){
    if(is_gameful()){
        $blog_id = get_current_blog_id();
        $key = $blog_id . "_" . $key;
    }
    // set_transient($key, $data, $expiration );
    // return;

    //wp_cache_set($key, $data);
    if (  extension_loaded( 'apc' ) && ini_get( 'apc.enabled' )  ) {
        //set_transient($key, $data, $expiration );
        //echo ('setTRUE');
        // wp_cache_set( $key, $data, 'transient', $expiration );
        apc_store($key, $data, $expiration);
        // set_transient($key, $data, $expiration );
    }else{
        set_transient($key, $data, $expiration );
        //echo ('setFLASE');
    }
}

function go_get_transient($key){
    if(is_gameful()){
        $blog_id = get_current_blog_id();
        $key = $blog_id . "_" . $key;
    }

    // $data = get_transient($key);
    // return $data;
    if (  extension_loaded( 'apc' ) && ini_get( 'apc.enabled' )  ) {
        //set_transient($key, $data, $expiration );
        //echo ('getTRUE');
        //wp_cache_set( $key, $data, 'transient', $expiration );
        $data = apc_fetch( $key );
        //$data = get_transient($key);
    }else {
        //try to get from
        // echo ('getFALSE');
        $data = get_transient($key);
    }
    return $data;
}

function go_delete_transient($key){
    if(is_gameful()){
        $blog_id = get_current_blog_id();
        $key = $blog_id . "_" . $key;
    }

    // $data = get_transient($key);
    // return $data;
    if (  extension_loaded( 'apc' ) && ini_get( 'apc.enabled' )  ) {
        //set_transient($key, $data, $expiration );
        //echo ('getTRUE');
        //wp_cache_set( $key, $data, 'transient', $expiration );
        apc_delete( $key );
        //$data = get_transient($key);
    }else {
        //try to get from
        // echo ('getFALSE');
        delete_transient($key);
    }
    //return $data;
}
/**
 * Get/set transient of user_id totals
 *
 * Reset on:
 * update_totals OK
 *
 * @param $user_id
 * @return mixed
 */
function go_get_loot($user_id){
    global $wpdb;
    $key = 'go_get_loot_' . $user_id;

    $data = go_get_transient($key);
    if ($data === false){

        $go_loot_table_name = "{$wpdb->prefix}go_loot";
        $loot = $wpdb->get_results("SELECT * FROM {$go_loot_table_name} WHERE uid = {$user_id}");
        if (!empty($loot)){
            $loot = $loot[0];
        }else{
            go_add_user_to_totals_table($user_id);
            $loot = $wpdb->get_results("SELECT * FROM {$go_loot_table_name} WHERE uid = {$user_id}");
            if (!empty($loot)){
                $loot = $loot[0];
            }
        }
        $data = json_decode(json_encode($loot), True);

        go_set_transient($key, $data, 3600 );
    }

    return $data;
}


/**
 * Get/set transient of term_ids of chains on a map by map term_id
 *
 * Reset on:
 * change to term (could now be a pod, etc.)        OK
 * change to term order --includes term-order.php   OK
 *
 * @param $term_id
 * @param $taxonomy
 * @return mixed
 */
function go_get_child_term_ids($term_id, $taxonomy) {
    //global $wpdb;

    $key = 'go_get_child_term_ids_' . $term_id;


    $data = go_get_transient($key);
    //$data = false;
    if (empty($data) || isset($data['errors'])) {



        $data = go_get_terms_ordered($taxonomy, $term_id);
        //$data = get_terms($taxonomy,$args); //query 1 --get the chains
        $data = wp_list_pluck( $data, 'term_id' );

        go_set_transient($key, $data, 3600 * 24);
    }

    return $data;

}

function go_reset_map_transient($term_id, $taxonomy){

    $term = get_term($term_id, $taxonomy);
    //Get the parent object
    if($term) {
        $termParent = ($term->parent == 0) ? $term : get_term($term->parent, $taxonomy);
        //GET THE ID FROM THE MAP OBJECT
        $term_id = $termParent->term_id;
    }
    $key = 'go_get_child_term_ids_' . $term_id;

    go_delete_transient($key);

}

/**
 * @param $term_id
 * @param $taxonomy
 * @return mixed
 *
 * Delete on save or update of term OK
 */
function go_get_parent_term_id($term_id, $taxonomy){
    $key = 'go_get_parent_term_id_' . $term_id;
    $data = go_get_transient($key);
    //$data = false;
    if ($data === false) {
        //find if term is a map
        //if not a map, get map_id
        $term = get_term($term_id, $taxonomy);
        //Get the parent object, if needed
        $termParent = ($term->parent == 0) ? $term : get_term($term->parent, $taxonomy);
        //GET THE ID FROM THE MAP OBJECT
        $data = $termParent->term_id;

        go_set_transient($key, $data, 3600 * 24);
    }

    return $data;

}

/**
 * @return mixed
 *
 * Delete on save or update of any term OK
 */
function go_get_parent_term_ids($taxonomy = 'task_chains'){
    $key = 'go_get_parent_term_ids_'.$taxonomy;

    $data = go_get_transient($key);
    //$data = false;
    if ($data === false) {
        //$args = array('hide_empty' => false, 'orderby' => 'order', 'order' => 'ASC', 'parent' => 0, 'fields' => 'ids');
        //get all parent maps (chains with no parents)
        //$data = get_terms('task_chains', $args);
        $data = go_get_terms_ordered($taxonomy, '0');

        go_set_transient($key, $data, 3600 * 24);
    }

    return $data;

}

/**
 * Gets/sets transient of the term data
 *
 * Reset on Term save                               OK
 *
 * @param $term_id
 * @return array
 */
function go_term_data($term_id){
    $key = 'go_term_data_' . $term_id;
    $data = go_get_transient($key);
    //$data = false;
    if ($data !== false){
        if($data[0] === null){
            $term_data = array();
        }else {
            $term_data = $data;
        }

    }else {
        $term_data = array();
        $term = get_term($term_id);
        if(is_object($term)) {
            $term_name = $term->name;
            $term_data[] = $term_name;
            $term_custom = get_term_meta($term_id, '', true);
            $term_data[] = $term_custom;
            go_set_transient($key, $term_data, 3600 * 24);
        }
    }
    return $term_data;

}

/**
 * gets/sets transient of post_ids assigned to a term, in order set on map
 * If run from map and also sets the transient data for each task if needed
 *
 * Reset on:
 * new task assigned --post saved
 * task removed --post saved
 * order changed -- post saved (any save)
 *
 * @param $term_id
 * @param $taxonomy
 * @param $is_map
 * @return mixed
 */
function go_get_chain_posts($term_id, $taxonomy, $is_map = false ){
    //global $wpdb;

    $key = 'go_get_chain_posts_' . $term_id;

    $data = go_get_transient($key);
    //$data = false;
    //$data = false;
    if ($data !== false){
        $data_ids = $data;

    }else {
        $data = go_get_ordered_posts($term_id, $taxonomy);

        /*
        $args=array(
            'post_type'        => 'tasks',
            'tax_query' => array(
                array(
                    'taxonomy' => 'task_chains',
                    'field' => 'term_id',
                    'terms' => $term_id,
                )
            ),
            'orderby'          => 'meta_value_num',
            'order'            => 'ASC',
            'posts_per_page'   => -1,
            'meta_key'         => 'go-location_map_order_item',
            'post_status'      => 'publish',
            'suppress_filters' => true,
            'meta_query' => array(
                array(
                    'key'     => 'go-location_map_toggle',
                    'value'   => 1,
                )
            ),
        );


        $data = get_posts($args);*/

        if ($is_map) {
            foreach ($data as $task) {
                $post_id = $task->ID;
                go_post_data($post_id);
            }
        }

        $data_ids = wp_list_pluck( $data, 'ID' );
        go_set_transient($key, $data_ids, 3600 * 24);
        foreach ($data_ids as $post_id){
            $key = 'go_post_task_chain_' . $post_id;
            update_option( $key, $term_id, false );
        }

    }
    return $data_ids;
}

/**
 * Gets/sets transient of the post data for tasks and store items
 * [0]-title
 * [1]-status [published]
 * [2]-permalink
 * [3]-metadata
 * Reset on:
 * post save                                        OK
 * or 24 Hours
 *
 * @param $post_id
 * @return array
 */
function go_post_data($post_id){
    $key = 'go_post_data_' . $post_id;
    $data = go_get_transient($key);

    if ($data !== false){
        $post_data = $data;

    }else {
        $post_data = array();
        if ($post_id != false) {
            $post = get_post($post_id);
            $post_title = $post->post_title;
            $post_data[] = $post_title;//0
            $post_status = $post->post_status;
            $post_data[] = $post_status;//1
            $post_permalink = get_permalink($post);
            $post_data[] = $post_permalink;//2
            $post_custom = get_post_meta($post_id);
            $post_data[] = $post_custom;//3
        }
        go_set_transient($key, $post_data, 3600 * 24);
    }
    return $post_data;

}

function go_the_title($post_id){
    $post_data = go_post_data( $post_id );
    //$post_title = $post_data[0];
    $post_title= (isset($post_data[0]) ? $post_data[0] : null);
    return $post_title;
}

function go_post_status($post_id){
    $post_data = go_post_data( $post_id );
    $post_status = (isset($post_data[1]) ? $post_data[1] : null);
    return $post_status;
}

function go_post_permalink($post_id){
    $post_data = go_post_data( $post_id );
    $post_permalink = (isset($post_data[2]) ? $post_data[2] : null);

    return $post_permalink;
}

function go_post_meta($post_id, $key = '', $single = 'false'){
    $post_data = go_post_data( $post_id );
    ///$custom_fields = $post_data[3];
    $custom_fields = (isset($post_data[3]) ? $post_data[3] : null);
    if (null === $custom_fields){
        return null;
    }
    if (empty($key)) {
        $post_meta = $custom_fields;
    }else{
        $post_meta = (isset($custom_fields[$key]) ? $custom_fields[$key] : null);
        if ( null !== $post_meta ) {
            if ( $single && is_array( $post_meta ) ) {
                return $post_meta[0];
            } else {
                return $post_meta;
            }
        }
    }

    return $post_meta;
}

function go_task_loot($post_id){
    $key = 'go_task_loot_' . $post_id;
    $data = go_get_transient($key);

    if ($data !== false){
        $loot = $data;

    }else {
        $custom_fields = go_post_meta($post_id);
        //$stage_count = $custom_fields['go_stages'][0];
        $stage_count = (isset($custom_fields['go_stages'][0]) ?  $custom_fields['go_stages'][0] : 0);

        $xp_toggle = get_option( 'options_go_loot_xp_toggle' );
        $gold_toggle = get_option( 'options_go_loot_gold_toggle' );
        $health_toggle = get_option( 'options_go_loot_health_toggle' );
        $badges_toggle = get_option( 'options_go_badges_toggle' );
        $groups_toggle = get_option('options_go_groups_toggle');

        $xp_loot = 0;
        $gold_loot = 0;
        $health_loot = 0;
        $badges = array();
        $groups = array();

        //entry loot
        if ($xp_toggle){
            $xp_loot = (isset($custom_fields['go_entry_rewards_xp'][0]) ?  $custom_fields['go_entry_rewards_xp'][0] : 0);
        }
        if ($gold_toggle){
            $gold_loot = (isset($custom_fields['go_entry_rewards_gold'][0]) ?  $custom_fields['go_entry_rewards_gold'][0] : 0);
        }
        if ($health_toggle){
            $health_loot = (isset($custom_fields['go_entry_rewards_health'][0]) ?  $custom_fields['go_entry_rewards_health'][0] : 0);
        }
        if ($badges_toggle){
            $badges = (isset($custom_fields['go_badges'][0]) ?  $custom_fields['go_badges'][0] : null);
        }
        if ($groups_toggle){
            $groups = (isset($custom_fields['go_groups'][0]) ?  $custom_fields['go_groups'][0] : null);
        }

        $i = 0;

        //add all the loot from the stages
        while ( $stage_count > $i ) {
            if (get_option( 'options_go_loot_xp_toggle' )) {
                $opt_key = 'go_stages_' . $i . '_rewards_xp';
                //$xp = $custom_fields[$opt_key][0];
                $xp = (isset($custom_fields[$opt_key][0]) ?  $custom_fields[$opt_key][0] : 0);
                $xp_loot = $xp + $xp_loot;
            }

            if(get_option( 'options_go_loot_gold_toggle' )) {
                $opt_key = 'go_stages_' . $i . '_rewards_gold';
                $gold = (isset($custom_fields[$opt_key][0]) ?  $custom_fields[$opt_key][0] : 0);
                $gold_loot = $gold + $gold_loot;
            }

            if(get_option( 'options_go_loot_health_toggle' )) {
                $opt_key = 'go_stages_' . $i . '_rewards_health';
                $health = (isset($custom_fields[$opt_key][0]) ?  $custom_fields[$opt_key][0] : 0);
                $health_loot = $health + $health_loot;
                if($health_loot > 200){
                    $health_loot = 200;
                }
            }

            $i++;
        }

        $loot['xp'] = $xp_loot;
        $loot['gold'] = $gold_loot;
        $loot['health'] = $health_loot;
        $loot['badges'] = $badges;
        $loot['groups'] = $groups;

        go_set_transient($key, $loot, 3600 * 24);
    }
    return $loot;

}


/**
 * Update transients on post save, delete or trash
 * @param  integer $post_id
 */
function go_update_task_post_save( $post_id ) {
    $post_type = (isset($_POST['post_type']) ?  $_POST['post_type'] : null);
    if(empty($post_type)){
        $post_type = get_post_type( $post_id );
    }


    // Check for post type.
    if ( 'tasks' !== $post_type ) {
        return;
    }

    update_post_meta( $post_id, 'go_task_version', 2);

    //delete task chain transient for old and new task chain
    //delete old task chain transient
    //this is the original task_chain for this post
    //there is an option created/updated when the transient is created
    /*
    $key = 'go_post_task_chain_' . $post_id;
    $term_id = get_option($key);
    //delete the original task chain post_ids transient
    $key = 'go_get_chain_posts_' . $term_id;
    go_delete_transient($key);*/

    //delete OLD task chain transient
    $term_id = go_post_meta($post_id, 'go-location_map_loc');//can we get this from the taxonomy relationship
    //$term_id = (isset($custom_fields['go-location_map_loc'][0]) ? $custom_fields['go-location_map_loc'][0] : null);
    if(!empty($term_id)) {
        $key = 'go_get_chain_posts_' . $term_id;
        go_delete_transient($key);
    }

    //delete new task chain transient
    //$term_id = go_post_meta($post_id, 'location_map_loc');
    //$custom_fields = get_post_meta($post_id);
    //$term_id = (isset($custom_fields['go-location_map_loc'][0]) ? $custom_fields['go-location_map_loc'][0] : null);
    //$term_id = $_POST['acf']['field_5a960f458bf8c']['field_5ab197179d24a']['field_5a960f468bf8e'];
    $term_id = (isset($_POST['acf']['field_5a960f458bf8c']['field_5ab197179d24a']['field_5a960f468bf8e']) ?  $_POST['acf']['field_5a960f458bf8c']['field_5ab197179d24a']['field_5a960f468bf8e'] : null);

    if(!empty($term_id)){
        $key = 'go_get_chain_posts_' . $term_id;
        go_delete_transient($key);
    }

    //delete task data transient
    $key = 'go_post_data_' . $post_id;
    go_delete_transient($key);

    //delete task loot transient
    $key = 'go_task_loot_' . $post_id;
    go_delete_transient($key);

}

add_action( 'wp_trash_post', 'go_update_task_post_save' );//before sent to trash
add_action( 'delete_post', 'go_update_task_post_save' );//before delete
add_action( 'deleted_post', 'go_update_task_post_save' );//after delete
add_action( 'save_post', 'go_update_task_post_save' );//after save

/**
 * Update map on map/chain term
 * @param  integer $term_id
 */
/*
function go_update_task_chain_term_save( $term_id ) {

    $key = 'go_get_child_term_ids_' . $term_id;
    go_delete_transient( $key );

    $key = 'go_term_data_' . $term_id;
    go_delete_transient( $key );

    $key = 'go_get_parent_map_id_' . $term_id;
    go_delete_transient( $key );

    $key = 'go_get_parent_term_ids_'.'task_chains';
    go_delete_transient( $key );

    $key = 'go_get_chain_posts_' . $term_id;
    go_delete_transient( $key );

    go_reset_map_transient($term_id, 'task_chains');

    $parent_id[] = go_get_parent_map_id($term_id);//the existing parent ID
    if (!empty($parent_id)) {
        $key = 'go_get_child_term_ids_' . $parent_id;
        go_delete_transient($key);

        $key = 'go_term_data_' . $parent_id;
        go_delete_transient($key);

        $key = 'go_get_parent_map_id_' . $parent_id;
        go_delete_transient($key);

        $key = 'go_get_chain_posts_' . $parent_id;
        go_delete_transient($key);

        go_reset_map_transient($parent_id, 'task_chains');
    }
}

add_action( "delete_task_chains", 'go_update_task_chain_term_save', 10, 4 );
//add_action( "delete_term_taxonomy", 'go_update_task_chain_term_save', 10, 4 );
add_action( "create_task_chains", 'go_update_task_chain_term_save', 10, 4 );
add_action( "edit_task_chains", 'go_update_task_chain_term_save', 10, 4 );
*/

/**
 * Update map on map/chain term
 * @param  integer $term_id
 */
/*
function go_update_store_chain_term_save( $term_id ) {

    $key = 'go_get_child_term_ids_' . $term_id;
    go_delete_transient( $key );

    $key = 'go_term_data_' . $term_id;
    go_delete_transient( $key );

    $key = 'go_get_parent_map_id_' . $term_id;
    go_delete_transient( $key );

    $key = 'go_get_parent_term_ids_'.'store_types';
    go_delete_transient( $key );

    $key = 'go_get_chain_posts_' . $term_id;
    go_delete_transient( $key );

    go_reset_map_transient($term_id, 'store_types');

    $parent_id[] = go_get_parent_map_id($term_id);//the existing parent ID
    if (!empty($parent_id)) {
        $key = 'go_get_child_term_ids_' . $parent_id;
        go_delete_transient($key);

        $key = 'go_term_data_' . $parent_id;
        go_delete_transient($key);

        $key = 'go_get_parent_map_id_' . $parent_id;
        go_delete_transient($key);

        $key = 'go_get_chain_posts_' . $parent_id;
        go_delete_transient($key);

        go_reset_map_transient($parent_id, 'store_types');
    }
}
//add_action( "delete_store_types", 'go_update_store_chain_term_save', 10, 4 );
//add_action( "create_store_types", 'go_update_store_chain_term_save', 10, 4 );
//add_action( "edit_store_types", 'go_update_store_chain_term_save', 10, 4 );
add_action( "delete_term", 'go_update_store_chain_term_save', 10, 4 );
add_action( "create_term", 'go_update_store_chain_term_save', 10, 4 );
add_action( "edit_term", 'go_update_store_chain_term_save', 10, 4 );*/

function go_update_term_save( $term_id, $tt_id, $taxonomy ) {

    $key = 'go_get_child_term_ids_' . $term_id;
    go_delete_transient( $key );

    $key = 'go_term_data_' . $term_id;
    go_delete_transient( $key );

    $key = 'go_get_parent_term_id_' . $term_id;
    go_delete_transient( $key );

    $key = 'go_get_parent_term_ids_'.$taxonomy;
    go_delete_transient( $key );

    $key = 'go_get_chain_posts_' . $term_id;
    go_delete_transient( $key );

    go_reset_map_transient($term_id, $taxonomy);

   $parent_id = go_get_parent_term_id($term_id, $taxonomy);//the parent ID
    if (!empty($parent_id)) {
        $key = 'go_get_child_term_ids_' . $parent_id;
        go_delete_transient($key);

        $key = 'go_term_data_' . $parent_id;
        go_delete_transient($key);

        $key = 'go_get_parent_term_id_' . $parent_id;
        go_delete_transient($key);

        $key = 'go_get_chain_posts_' . $parent_id;
        go_delete_transient($key);

        go_reset_map_transient($parent_id, $taxonomy);
    }
}
//add_action( "delete_store_types", 'go_update_store_chain_term_save', 10, 4 );
//add_action( "create_store_types", 'go_update_store_chain_term_save', 10, 4 );
//add_action( "edit_store_types", 'go_update_store_chain_term_save', 10, 4 );
add_action( "delete_term", 'go_update_term_save', 10, 4 );
add_action( "create_term", 'go_update_term_save', 10, 4 );
add_action( "edit_term", 'go_update_term_save', 10, 4 );


//make sure map order transient is clear on the new and old chains
add_action( 'update_post_meta', 'go_delete_order_transient', 10, 4 );
add_action( 'updated_post_meta', 'go_delete_order_transient', 10, 4 );
function go_delete_order_transient( $meta_id, $post_id, $meta_key, $meta_value )
{
    if ( 'go-location_map_loc' == $meta_key ) {
        $key = 'go_get_chain_posts_' . $meta_value;
        go_delete_transient($key);
    }

}