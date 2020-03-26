<?php
/**
 * Created by PhpStorm.
 * User: mcmurray
 * Date: 3/29/18
 * Time: 10:03 PM
 */


/**
 * @param $field
 * @return mixed
 * Loads the seating chart from the options page in various ACF fields
 */

function acf_load_seat_choices( $field ) {

    // reset choices
    //$field['choices'] = array();
    $field['choices'] = null;
    $field['choices'][ null ] = "Select";
    $name = get_option('options_go_seats_name');
    $number = get_option('options_go_seats_number');
    $field['placeholder'] = 'Select';

    if ($number > 0){
        $i = 0;
        while ($i < $number){
            $i++;

            // vars
            $text = $name . " " . $i;


            // append to choices
            $field['choices'][ $i ] = $text;

        }
    }
    // return the field
    return $field;

}
add_filter('acf/load_field/name=user-seat', 'acf_load_seat_choices');

function acf_load_xp_levels( $field ) {

    // reset choices
    //$field['choices'] = array();
    $field['choices'] = null;
    $field['choices'][ null ] = "Select";
    $num_levels = get_option('options_go_loot_xp_levels_level');
    $number = get_option('options_go_seat_number');
    $field['placeholder'] = 'Select';

    for ($i = 0; $i < $num_levels; $i++) {
        $num = $i+1;
        $xp = get_option('options_go_loot_xp_levels_level_' . $i . '_xp');
        $level_name = get_option('options_go_loot_xp_levels_level_' . $i . '_name');
        $xp_abbr = get_option( "options_go_loot_xp_abbreviation" );

        $name = "Level" . $num . " - " . "$level_name" . " : " . $xp . " " . $xp_abbr;

        $field['choices'][ $xp ] = $name;
    }



    // return the field
    return $field;

}
add_filter('acf/load_field/key=field_5b23676184648', 'acf_load_xp_levels');
add_filter('acf/load_field/key=field_5b52731ddd4f7', 'acf_load_xp_levels');


/**
 * Flushes the rewrite rules when options page is saved.
 * It does this by setting a option to true (1) and then another action
 * actually does the update later and sets the flag back to false.
 * @param $post_id
 * @return string
 * Modified From : https://wordpress.stackexchange.com/questions/182798/flush-rewrite-rules-on-save-post-does-not-work-on-first-post-save
 * Modified From: https://support.advancedcustomfields.com/forums/topic/when-using-save_post-action-how-do-you-identify-which-options-page/
 *
 * This is needed because the options page can change some rewrite rules
 */
function acf_flush_rewrite_rules( $post_id ) {
    if ($post_id == 'options') {
        update_option( 'go-flush-rewrite-rules', 1 );
        //flush_rewrite_rules(true);
    }
}
add_action('acf/save_post', 'acf_flush_rewrite_rules', 2);

function go_late_init_flush() {
    if ( ! $option = get_option( 'go-flush-rewrite-rules' ) ) {
        return false;
    }
    if ( $option == 1 ) {
        flush_rewrite_rules();
        update_option( 'go-flush-rewrite-rules', 0 );
    }
    return true;
}
add_action( 'init', 'go_late_init_flush', 999999 );

/**
 *Loads the default options in bonus loot
 * Default is set in options and loaded on tasks
 */
function default_value_field_5b526d2e7957e($value, $post_id, $field) {
    if ($value === false) {
        $row_count = get_option('options_go_loot_bonus_loot');
        $value = array();
        if(!empty($row_count)){
            for ($i = 0; $i < $row_count; $i++) {
                $title = "options_go_loot_bonus_loot_" . $i . "_title";
                $title = get_option($title);
                $message = "options_go_loot_bonus_loot_" . $i . "_message";
                $message = get_option($message);
                $xp = "options_go_loot_bonus_loot_" . $i . "_defaults_xp";
                $xp = get_option($xp);
                $gold = "options_go_loot_bonus_loot_" . $i . "_defaults_gold";
                $gold = get_option($gold);
                $health = "options_go_loot_bonus_loot_" . $i . "_defaults_health";
                $health = get_option($health);
                $drop = "options_go_loot_bonus_loot_" . $i . "_defaults_drop_rate";
                $drop = get_option($drop);

                $loot_val = array(
                    'field_5b526d2e79583' => $xp,
                    'field_5b526d2e79584' => $gold ,
                    'field_5b526d2e79585' => $health,
                    'field_5b526d2e79588' => $drop
                );
                $row_val = array(
                    'field_5b526d2e7957f' => $title,
                    'field_5b526d2e79580' => $message,
                    'field_5b526d2e79582' => $loot_val
                );

                $value[] = $row_val;

            }

        }
    }
    return $value;
}
add_filter('acf/load_value/key=field_5b526d2e7957e', 'default_value_field_5b526d2e7957e', 10, 3);


/**
 *Loads the child/parent option in terms
 */
function load_parent_or_child($value, $post_id, $field) {
    global $pagenow;
    if($pagenow != 'edit-tags.php') {
        if (empty($value) || ($value != 'parent' && $value != 'child')) {
            $term_id = (isset($_REQUEST['tag_ID']) ? $_REQUEST['tag_ID'] : '');
            $term = get_term($term_id);
            $parent = (isset($term->parent)) ? $term->parent : 0;
            if (!empty($parent)) {
                $value = 'child';
            } else {
                $value = 'parent';
            }
        }
    }
    return $value;
}
add_filter('acf/load_value/key=field_5e35979c47071', 'load_parent_or_child', 10, 3);
add_filter('acf/load_value/key=field_5e37bb8f5b17c', 'load_parent_or_child', 10, 3);
add_filter('acf/load_value/key=field_5e37cdfe1f357', 'load_parent_or_child', 10, 3);
add_filter('acf/load_value/key=field_5e389128e24ab', 'load_parent_or_child', 10, 3);

/**
 *Loads the value for pod/chain radio--just in case empty on existing map column it sets to chain
 */
function load_field_5e35981e47072($value, $post_id, $field) {
    if (empty($value)) {
        $term_id = (isset($_REQUEST['tag_ID']) ?  $_REQUEST['tag_ID'] : '');
        if(!empty($term_id)) {
            $value = 0;
        }
    }
    return $value;
}
add_filter('acf/load_value/key=field_5e35981e47072', 'load_field_5e35981e47072', 10, 3);


/**
 *Loads the value in the select parent term dropdown
 */
function load_term_dropdown_parents_only($value, $post_id, $field) {
    if (empty($value)) {
        $term_id = (isset($_REQUEST['tag_ID']) ?  $_REQUEST['tag_ID'] : '');
        $term = get_term($term_id);
        $value = ( isset( $term->parent ) ) ? $term->parent : '';
    }
    return $value;
}
add_filter('acf/load_value/key=field_5e35987e47073', 'load_term_dropdown_parents_only', 10, 3);
add_filter('acf/load_value/key=field_5e37bbe75b17d', 'load_term_dropdown_parents_only', 10, 3);
add_filter('acf/load_value/key=field_5e37ce341f358', 'load_term_dropdown_parents_only', 10, 3);
add_filter('acf/load_value/key=field_5e389128e24d3', 'load_term_dropdown_parents_only', 10, 3);


function load_term_description($value, $post_id, $field) {
    // if (empty($value)) {
    $term_id = (isset($_REQUEST['tag_ID']) ?  $_REQUEST['tag_ID'] : '');
    if(!empty($term_id)) {
        $term = get_term($term_id);
        if($term) {
            $value = $term->description;
        }
    }
    // }
    return $value;
}
add_filter('acf/load_value/key=field_5e37d3b0b2037', 'load_term_description', 10, 3);
add_filter('acf/load_value/key=field_5e389128e24e3', 'load_term_description', 10, 3);

function update_term_description($value, $post_id, $field) {
    $_POST['description'] = $value;

    if (!empty($value)) {
        $term_id = (isset($_REQUEST['tag_ID']) ?  $_REQUEST['tag_ID'] : '');
        //$taxonomy = $field['taxonomy'];
        $taxonomy = $_POST['taxonomy'];
        //$taxonomy = 'go_badges';
        wp_update_term($term_id, $taxonomy, array('description' => $value));
    }
    return $value;
}
add_filter('acf/update_value/key=field_5e37d3b0b2037', 'update_term_description', 10, 3);
add_filter('acf/update_value/key=field_5e389128e24e3', 'update_term_description', 10, 3);



/**
 *Updates the value in the select parent term dropdown
 */

//function update_term_parents($valid, $value, $field, $input) {
function update_term_parents( $value, $post_id, $field  ) {
    $term_id = substr($post_id, strpos($post_id, "_") + 1);
    $_POST['parent'] = $value;
    $_REQUEST['parent'] = $value;

    if (!empty($value)) {
        $taxonomy = $field['taxonomy'];
        $term = get_term($term_id, $taxonomy);
        $termParent = $term->parent;


        $name = $_POST['name'];
        if(empty($name)){
            $name = (isset($_REQUEST['tag-name']) ?  $_REQUEST['tag-name'] : '');
        }
        //$term_id = (isset($_REQUEST['tag_ID']) ?  $_REQUEST['tag_ID'] : '');

        $args = array('parent' => $value, 'name' => $name);
        wp_update_term( $term_id, $taxonomy, $args );
        if($termParent && $termParent != $value) {
            go_last_in_order($term_id, null, $taxonomy);
        }
    }
    return $value;
}

add_filter('acf/update_value/key=field_5e35987e47073', 'update_term_parents', 10, 4);//task_chains
add_filter('acf/update_value/key=field_5e37bbe75b17d', 'update_term_parents', 10, 4);//store cats
add_filter('acf/update_value/key=field_5e37ce341f358', 'update_term_parents', 10, 4);//badges
add_filter('acf/update_value/key=field_5e389128e24d3', 'update_term_parents', 10, 4);//groups



/**
 * Only show the top level terms for a taxonomy
 */
//I don;t think this is used anymore
function go_top_terms( $args, $field, $post_id  ){
    $args['parent'] = 0;
    return $args;
}
add_filter('acf/fields/taxonomy/query/key=field_5b017d76920ec', 'go_top_terms', 10, 3);

function go_top_terms_dropdown( $args, $field, $post_id  ){
    $args['parent'] = 0;
    $args['orderby'] = 'meta_value_num';
    $args['order'] = 'ASC';
    $args['meta_query'] = array(
        'relation' => 'OR',
        array(
            'key' => 'go_order',
            'compare' => 'NOT EXISTS'
        ),
        array(
            'key' => 'go_order',
            'value' => 0,
            'compare' => '>='
        )
    );
    $args['hide_empty'] = false;


    return $args;
}


add_filter('acf/fields/taxonomy/query/key=field_5e35987e47073', 'go_top_terms_dropdown', 10, 3);
add_filter('acf/fields/taxonomy/query/key=field_5e37bbe75b17d', 'go_top_terms_dropdown', 10, 3);
add_filter('acf/fields/taxonomy/query/key=field_5e37ce341f358', 'go_top_terms_dropdown', 10, 3);
add_filter('acf/fields/taxonomy/query/key=field_5e389128e24d3', 'go_top_terms_dropdown', 10, 3);



add_filter('acf/load_value/key=field_5d34f488b13ff', 'go_load_sections', 10, 3);
function go_load_sections($value, $post_id, $field){

    $terms = go_get_terms_ordered('user_go_sections');
    $value = array();
    $i = 0;
    foreach ($terms as $term){
        $name = $term->name;
        $term_id = $term->term_id;
        $value[$i] = array('field_5d34f49cb1400'=>$name, 'field_5d35279fb1592'=>$term_id);
        $i++;
    }

    return $value;
}

add_filter('acf/update_value/key=field_5d34f488b13ff', 'go_save_sections', 10, 3);
function go_save_sections($value, $post_id, $field){
    //global $go_section_order;
   // if(empty($go_section_order)){
        //$go_section_order = 1;
    //}
    $section_fields = $_REQUEST['acf']['field_5d34f488b13ff'];
    $order = 0;
    $terms = go_get_terms_ordered('user_go_sections');
    $old_terms = array();
    foreach ($terms as $term) {
        $old_terms[] = $term->term_id;
    }
    $new_terms = array();
    if(is_array($section_fields)) {
        foreach ($section_fields as $section_field) {
            $section_name = $section_field['field_5d34f49cb1400'];
            $section_id = $section_field['field_5d35279fb1592'];
            $new_terms[] = $section_id;
            $args = array(
                'name' => $section_name,
            );
            if (empty($section_id)) {
                $term_id = wp_insert_term($section_name, 'user_go_sections');
            } else {
                $term_id = wp_update_term($section_id, 'user_go_sections', $args);
            }
            $order++;
            update_term_meta($term_id['term_id'], 'go_order', $order);

        }
    }

    $deleted_terms = array_diff($old_terms, $new_terms);
    foreach ($deleted_terms as $deleted_term){
        wp_delete_term($deleted_term, 'user_go_sections');
    }


    return '';
}


/**
 * FOR THE CHANGE TO THE CALL TO ACTION
 */

/**
 * Checks the title checkbox
 * If this post has a custom title value, but no value on the checkbox
 * then check the checkbox.
 */
function go_checkbox_options($value, $post_id, $field) {
    $custom_fields = get_post_meta($post_id);
    $name = $field["name"];
    $saved_value = (isset($custom_fields[$name][0]) ?  $custom_fields[$name][0] : false);
    if ($saved_value === false) {
        $title_field_name = str_replace("_opts","_title",$name);
        $blog_title = (isset($custom_fields[$title_field_name][0]) ?  $custom_fields[$title_field_name][0] : false);
        if(!empty($blog_title)){
            $value[]='title';
        }

        $private_field_name = str_replace("_opts","_private",$name);
        $private = (isset($custom_fields[$private_field_name][0]) ?  $custom_fields[$private_field_name][0] : false);
        //$private = get_field('field_5e3dfab5b4380');
        if($private){
            $value[]='private';
        }

        $show_posts_field_name = str_replace("_opts","_view_posts_button_show",$name);
        $show_posts = (isset($custom_fields[$show_posts_field_name][0]) ?  $custom_fields[$show_posts_field_name][0] : false);
        //$show_posts = get_field('field_5e3dfab5b9e96');
        if($show_posts){
            $value[]='show_posts';
        }
    }
    return $value;
}
add_filter('acf/load_value/key=field_5e3d8b7907ca2', 'go_checkbox_options', 10, 3);
add_filter('acf/load_value/key=field_5e3f2d616c25b', 'go_checkbox_options', 10, 3);


/**
 * Checks the directions checkbox
 * If this post has directions value, but no value on the checkbox
 * then check the checkbox.
 */
function go_checkbox_directions($value, $post_id, $field) {
    $custom_fields = get_post_meta($post_id);
    $name = $field["name"];
    $saved_value = (isset($custom_fields[$name][0]) ?  $custom_fields[$name][0] : false);
    if ($saved_value === false) {
        $directions_field_name = str_replace("_add_instructions","_instructions",$name);
        $directions = (isset($custom_fields[$directions_field_name][0]) ?  $custom_fields[$directions_field_name][0] : false);
        //$directions = get_field('field_5e3dfab571523');
        if(!empty($directions)){
            $value = 1;
        }

    }
    return $value;
}
add_filter('acf/load_value/key=field_5e3eecfae9849', 'go_checkbox_directions', 10, 3);
add_filter('acf/load_value/key=field_5e3f36078a0bf', 'go_checkbox_directions', 10, 3);

/**
 * Checks if an extra element needs to be added
 * This is for the old blog post formats.
 */
function go_add_text_element($value, $post_id, $field) {
    $custom_fields = get_post_meta($post_id);
    $name = $field["name"];
    $opts = str_replace("_blog_elements","_opts",$name);
    $saved_value = (isset($custom_fields[$opts][0]) ?  $custom_fields[$opts][0] : false);//if this exists, you don't have to add anything because this has been saved in new format
    if ($saved_value === false) {
        //does it need a new field
        $blog_element_name = str_replace("_blog_elements","_blog_text_toggle",$name);
        $needs_blog_element = (isset($custom_fields[$blog_element_name][0]) ?  $custom_fields[$blog_element_name][0] : false);
        if($needs_blog_element){
            //prompt
            $prompt_element_name = str_replace("_blog_elements","_blog_text_prompt",$name);
            $prompt = (isset($custom_fields[$prompt_element_name][0]) ?  $custom_fields[$prompt_element_name][0] : false);

            //min_words
            $min_words_element_name = str_replace("_blog_elements","_blog_text_minimum_length",$name);
            $min_words = (isset($custom_fields[$min_words_element_name][0]) ?  $custom_fields[$min_words_element_name][0] : false);

            $key = $field['key'];
            if($key == 'field_5cba48899f588') {
                $value[] =
                    array(
                        'field_5d95984389354' => $prompt,
                        'field_5cba489d9f589' => 'Text',
                        'field_5cba487b9f57e' => '',
                        'field_5cba487b9f582' => 'all',
                        'field_5e3c70c8f90cc' => $min_words,
                        'field_5e0a1ec8aebcd' => '1'
                    );
            }else if($key == 'field_5cbad9e8eb50f') {
                $value[] =
                    array(
                        'field_5d95ac15a2712' => $prompt,
                        'field_5cbad9e8eb510' => 'Text',
                        'field_5cbad9e8eb512' => '',
                        'field_5cbad9e8eb513' => 'all',
                        'field_5e3f3d4e4929a' => $min_words
                    );
            }
        }

    }
    return $value;
}
add_filter('acf/load_value/key=field_5cba48899f588', 'go_add_text_element', 10, 3);
add_filter('acf/load_value/key=field_5cbad9e8eb50f', 'go_add_text_element', 10, 3);

/**
 * Removes the Show All Posts checkbox if off globally.
 */
function go_all_blog_options($field) {
    $all_posts_toggle = get_option('options_go_tasks_show_all_posts');
    $post_type = $GLOBALS['post_type'];
    if($post_type !== 'acf-field-group') {
        if (!$all_posts_toggle) {
            unset($field['choices']['show_posts']);
        }
    }
    return $field;
}
add_filter('acf/load_field/key=field_5e3d8b7907ca2', 'go_all_blog_options', 10, 1);


/**
 * Checks the blog text editor options to the defaults
 */
function go_blog_editor_options($value, $post_id, $field) {
    $name = $field['name'] . '_toggle';
    $is_default = get_post_meta($post_id, $name);

    if($is_default === 'default') {
        $value = get_option('options_go_text_editor_defaults');
    }

    return $value;
}
add_filter('acf/load_value/key=field_5e3f5754c36aa', 'go_blog_editor_options', 10, 3);