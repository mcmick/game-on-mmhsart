<?php
/**
 * Created by PhpStorm.
 * User: mmcmurray
 * Date: 10/13/18
 * Time: 8:41 PM
 */

//Uses the hidden footer that is in the core of GO.

/**
 * @param $blog_post_id
 * @param $suffix
 * @param $go_blog_task_id
 * @param $i
 * @param $bonus
 * @param $check_for_understanding
 * @param $all_content
 */
function go_blog_form($blog_post_id, $suffix, $go_blog_task_id, $i, $bonus, $check_for_understanding, $all_content = false){
    //save draft button for drafts
    //print saved info for all
    ob_start();
    $text_toggle = true;
    $content ='';
    $title ='';
    $custom_fields = null;
    $url_content = null;
    $video_content = null;
    $media_content = null;
    $min_words = null;
    $post_status = null;




    if (!empty($blog_post_id)) {
        $post = get_post($blog_post_id, OBJECT, 'edit');
        $content = $post->post_content;
        $title = get_the_title($blog_post_id);
        $blog_meta = get_post_meta($blog_post_id);
        $post_status = get_post_status($blog_post_id);

       $go_blog_task_id = (isset($blog_meta['go_blog_task_id'][0]) ? $blog_meta['go_blog_task_id'][0] : null); //for posts created before v4.6
        if (empty($go_blog_task_id)) {
            $go_blog_task_id = wp_get_post_parent_id($blog_post_id);//for posts created after v4.6
        }

        if($all_content){
            $content = '';
        }
    }
    if($go_blog_task_id != 0) {
        $custom_fields = go_post_meta($go_blog_task_id);

        if ($bonus == true ) {
            $blog_title = (isset($custom_fields['go_bonus_stage_blog_options_v5_bonus_title'][0]) ? $custom_fields['go_bonus_stage_blog_options_v5_bonus_title'][0] : false);
            $text_toggle = (isset($custom_fields['go_bonus_stage_blog_options_v5_bonus_blog_text_toggle'][0]) ? $custom_fields['go_bonus_stage_blog_options_v5_bonus_blog_text_toggle'][0] : true);
            $min_words = (isset($custom_fields['go_bonus_stage_blog_options_v5_blog_text_minimum_length'][0]) ? $custom_fields['go_bonus_stage_blog_options_v5_blog_text_minimum_length'][0] : null);
            $is_private = (isset($custom_fields['go_bonus_stage_blog_options_v5_bonus_private'][0]) ?  $custom_fields['go_bonus_stage_blog_options_v5_bonus_private'][0] : false);
            $num_elements = (isset($custom_fields['go_bonus_stage_blog_options_v5_blog_elements'][0]) ?  $custom_fields['go_bonus_stage_blog_options_v5_blog_elements'][0] : false);

            if (!$blog_title){
                $blog_title = "Bonus";
            }
            for($x = 0; $x < $num_elements; $x++){//if this post has elements assigned, loop through them
                $uniqueid = (isset($custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_uniqueid'][0]) ?  $custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_uniqueid'][0] : 0);
                $type = (isset($custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_element'][0]) ?  $custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_element'][0] : null);

                if ($type =='URL'){
                   $required_string = (isset($custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_requirements_url_validation'][0]) ?  $custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_requirements_url_validation'][0] : '');

                    $url_content = (isset($blog_meta[$uniqueid][0]) ? $blog_meta[$uniqueid][0] : null);
                    if($all_content){
                        $url_content = '';
                    }


                    echo "<hr><h3>Submit a URL</h3>";

                    go_url_check_blog ('Enter URL', 'go_result_url'.$suffix , $url_content, 'URL', $required_string, $uniqueid);
                    if (!empty($required_string)){
                        echo " (url must contain \"".$required_string."\")";
                    }

                }

                if ($type =='File') {
                    $mime_types = (isset($custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_requirements_allowed_types'][0]) ? $custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_requirements_allowed_types'][0] : 0);
                    if (is_serialized($mime_types)) {
                        $mime_types = unserialize($mime_types);
                        $mime_types_array = is_array($mime_types) ? $mime_types : array();
                        $mime_types = implode(",", $mime_types_array);
                        $mime_types_pretty = implode(", ", $mime_types_array);
                        $mime_types_count = count($mime_types_array);

                    } else {
                        $mime_types = '';
                        $mime_types_pretty = '';
                        $mime_types_count = 0;
                    }
                    $media_content = (isset($blog_meta[$uniqueid][0]) ? $blog_meta[$uniqueid][0] : null);
                    if($all_content){
                        $media_content = '';
                    }


                    $post_type = get_post_type($media_content);
                    if($post_type != 'attachment'){
                        $media_content = null;
                    }


                    echo "<hr><h3>Add a File</h3><div>";

                    go_upload_check_blog ($media_content, $uniqueid, $mime_types,  $uniqueid);
                    echo "</div>";
                    echo "<div>";

                    if (!empty($mime_types_pretty))
                    {
                        if ($mime_types_count > 1) {
                            echo " (Allowed file types: " . $mime_types_pretty . ")";
                        }else if ($mime_types_count == 1){
                            echo " (Allowed file type: " . $mime_types_pretty . ")";
                        }
                    }
                    echo "</div>";

                }

                if ($type =='Video'){

                    $video_content = (isset($blog_meta[$uniqueid][0]) ? $blog_meta[$uniqueid][0] : null);
                    if($all_content){
                        $video_content = '';
                    }
                    if ($video_content === null){
                        $video_content = (isset($blog_meta['go_blog_video'][0]) ? $blog_meta['go_blog_video'][0] : null);//previously saved content v4
                    }
                    echo "<hr><h3>Submit a Video</h3><div>Video Link:<div>";
                    go_url_check_blog ('URL of Video', 'go_result_video'.$suffix, $video_content, 'video', '', $uniqueid);
                    echo "</div> </div>";
                }
            }
            if ($num_elements > 0){
                echo "<hr>";
            }
        }
        //Not a Bonus stage
        else{
            $blog_title = (isset($custom_fields['go_stages_' . $i . '_blog_options_v5_title'][0]) ? $custom_fields['go_stages_' . $i . '_blog_options_v5_title'][0] : false);
            $text_toggle = (isset($custom_fields['go_stages_' . $i . '_blog_options_v5_blog_text_toggle'][0]) ? $custom_fields['go_stages_' . $i . '_blog_options_v5_blog_text_toggle'][0] : true);
            $min_words = (isset($custom_fields['go_stages_' . $i . '_blog_options_v5_blog_text_minimum_length'][0]) ? $custom_fields['go_stages_' . $i . '_blog_options_v5_blog_text_minimum_length'][0] : null);
            $is_private = (isset($custom_fields['go_stages_'.$i.'_blog_options_v5_private'][0]) ?  $custom_fields['go_stages_'.$i.'_blog_options_v5_private'][0] : false);
            $num_elements = (isset($custom_fields['go_stages_'.$i.'_blog_options_v5_blog_elements'][0]) ?  $custom_fields['go_stages_'.$i.'_blog_options_v5_blog_elements'][0] : false);
            $s = $i + 1;
            for($x = 0; $x < $num_elements; $x++){//if this post has elements assigned, loop through them
                $type = go_post_meta($go_blog_task_id, 'go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_element');
                $uniqueid = (isset($custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_uniqueid'][0]) ?  $custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_uniqueid'][0] : 0);

                if ($type =='URL'){
                    //$required_string = get_post_meta($go_blog_task_id, 'go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_requirements_url_validation');
                    $required_string = (isset($custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_requirements_url_validation'][0]) ?  $custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_requirements_url_validation'][0] : 0);
                    $url_content = (isset($blog_meta[$uniqueid][0]) ? $blog_meta[$uniqueid][0] : null);
                    if($all_content){
                        $url_content = '';
                    }

                    echo "<hr><h3>Submit a URL</h3>";
                    go_url_check_blog ('Enter URL', 'go_result_url'.$suffix , $url_content, 'URL', $required_string, $uniqueid);
                    if (!empty($required_string)){
                        echo " (url must contain \"".$required_string."\")";
                    }
                }

                if ($type =='File') {
                    $mime_types = (isset($custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_requirements_allowed_types'][0]) ? $custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_requirements_allowed_types'][0] : 0);
                    if (is_serialized($mime_types)) {
                        $mime_types = unserialize($mime_types);
                        $mime_types_array = is_array($mime_types) ? $mime_types : array();
                        $mime_types = implode(",", $mime_types_array);
                        $mime_types_pretty = implode(", ", $mime_types_array);
                        $mime_types_count = count($mime_types_array);

                    } else {
                        $mime_types = '';
                        $mime_types_pretty = '';
                        $mime_types_count = 0;
                    }
                    $media_content = (isset($blog_meta[$uniqueid][0]) ? $blog_meta[$uniqueid][0] : null);
                    if($all_content){
                        $media_content = '';
                    }



                    echo "<hr><h3>Add a File</h3><div>";

                    go_upload_check_blog ($media_content, $uniqueid, $mime_types,  $uniqueid);
                    echo "</div>";
                    echo "<div>";

                    if (!empty($mime_types_pretty))
                    {
                        if ($mime_types_count > 1) {
                            echo " (Allowed file types: " . $mime_types_pretty . ")";
                        }else if ($mime_types_count == 1){
                            echo " (Allowed file type: " . $mime_types_pretty . ")";
                        }
                    }
                    echo "</div>";
                }

                if ($type =='Video'){
                    $video_content = (isset($blog_meta[$uniqueid][0]) ? $blog_meta[$uniqueid][0] : null);
                    if($all_content){
                        $video_content = '';
                    }
                    if ($video_content === null){
                        $video_content = (isset($blog_meta['go_blog_video'][0]) ? $blog_meta['go_blog_video'][0] : null);//previously saved content v4
                    }
                    echo "<hr><h3>Submit a Video</h3><div>Video Link:<div>";
                    //go_url_check($custom_fields, $i, $i, $go_actions_table_name, $user_id, $post_id, $bonus, $bonus_status, null, "URL of Video", 'go_result_video', $video_content);
                    go_url_check_blog ('URL of Video', 'go_result_video'.$suffix, $video_content, 'video', '', $uniqueid);
                    echo "</div> </div>";

                }
            }
            if ($num_elements > 0){
                echo "<hr>";
            }

            if (!$blog_title){
                $blog_title = "Stage ". (intval($i) +1) ;
            }
        }
        //set the title on blog post forms attached to quests
        $title = get_the_title($go_blog_task_id);
        $title = $title . " - " . $blog_title;
    }else{
        $is_private = get_post_meta($blog_post_id, 'go_blog_private_post', true) ? get_post_meta($blog_post_id, 'go_blog_private_post', true) : false;
    }

    $buffer = ob_get_contents();

    ob_end_clean();

    echo "<div class='go_blog_div'>";
    if( !empty($go_blog_task_id) && $is_private) {
        echo "<div ><h3>This post is private. Only you and the site administrators/instructors will be able to see it.</h3></div>";
    }
    if($go_blog_task_id) {
        echo "<div><h3 style='width: 100%;' data-blog_post_title='fixed' data-blog_post_id ='{$blog_post_id}' id='go_blog_title{$suffix}'>" . $title . "</h3> </div>";
    }else{
        echo "<div>Title:<div><input style='width: 100%;' data-blog_post_title='custom' id='go_blog_title".$suffix."' type='text' placeholder='' value ='{$title}' data-blog_post_id ='{$blog_post_id}' ></div> </div>";

    }
    echo $buffer;


    if($text_toggle) {
        $settings = array(//'tinymce'=> array( 'menubar'=> true, 'toolbar1' => 'undo,redo', 'toolbar2' => ''),
            'tinymce'=>true,
            //'wpautop' =>false,
            'textarea_name' => 'go_result'.$suffix, 'media_buttons' => true, //'teeny' => true,
             'menubar' => false, 'drag_drop_upload' => true);

        //echo "<button id='go_save_button' class='progress left'  check_type='blog' button_type='save'  admin_lock='true' >Save Draft</button> ";

        //$id = $_POST['editorID'];
        //$content = $_POST['content'];

        //wp_editor( $content, $id );
        wp_editor($content, 'go_blog_post'.$suffix, $settings);

        //add stuff below the mce window if it is shown

        //Private Post Toggle
        if (empty($go_blog_task_id)) {//only if not attached to quest
            if ($is_private) {
                $checked = 'checked';
            } else {
                $checked = '';
            }
            echo "<div style='width: 100%;text-align: right;'><input type='checkbox' id='go_private_post{$suffix}' value='go_private_post{$suffix}' {$checked}> Private Post</div>";
        }
        //word Count
        if ($min_words > 0) {
            echo "<div id='go_blog_min' style='text-align:right'><span class='char_count'>" . $min_words . "</span> Words Required</div>";
        }
    }
    if ( (is_user_member_of_blog() || go_user_is_admin()) && !$all_content) {
        echo "<p id='go_blog_stage_error_msg' class='go_error_msg' style='display: none; color: red;'></p>";

        echo "<div class='go_blog_form_footer {$suffix}' style='background-color: #b3b3b3;'>";
        $current_user = get_current_user_id();
        $is_admin = go_user_is_admin();
        if ($suffix != '_lightbox') {
            go_blog_status($blog_post_id, $is_admin, true);
            $button_class = "right";
        } else {
            $button_class = "left";
        }

        if ($text_toggle) {
            //show save button if this is a draft, reset, trashed or new post
            $allow_drafts = array("draft", "reset", "trash", null);
            if (in_array($post_status, $allow_drafts)) {
                echo "<span id='go_save_button{$suffix}' class='go_button_round go_save_button progress {$button_class}'  status='{$i}' data-bonus_status='{$bonus}' check_type='skip' button_type='save{$suffix}'  admin_lock='true' blog_post_id='{$blog_post_id}' blog_suffix='{$suffix}' task_id='{$go_blog_task_id}' data-check_for_understanding ='{$check_for_understanding}'><span class='go_round_inner'><i class='fas fa-save'></i></span></span>";

            }
            if ($suffix == '_lightbox'){
                echo "<button id='go_blog_submit' style='display:block;' check_type='blog_lightbox' button_type='submit' blog_post_id ={$blog_post_id} blog_suffix ='_lightbox'  task_id='{$go_blog_task_id}' min_words='{$min_words}' blog_suffix ='' text_toggle='{$text_toggle}' data-check_for_understanding ='{$check_for_understanding}'>Submit</button>";

            }
        }
        echo "</div>";
    }
    /*
    if($suffix !='_lightbox') {
        if ($blog_post_id) {
            //do_action('go_blog_template_after_post', $blog_post_id, false);
        }
    }
    */

    echo "</div>";

    //Save Draft Button
    if($suffix =='_lightbox') {
        ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery('#go_save_button_lightbox').one("click", function (e) {
                        go_blog_submit( this, true );
                    });
                });

            </script>

        <?php
    }

}

//add_filter( 'option_page_capability_' . ot_options_id(), create_function( '$caps', "return '$caps';" ), 999 );

//add_filter( 'option_page_capability_' . ot_options_id(), function($caps) {return $caps;},999);

/**
 * @param $blog_post_id
 * @param bool $check_for_understanding
 * @param bool $with_feedback
 * @param bool $is_reader
 * @param bool $show_edit
 * @param bool $bonus_status //only needed for old style URL and File print outs (v4)
 * @param bool $status
 */
function go_blog_post($blog_post_id, $go_blog_task_id = null, $check_for_understanding = false, $with_feedback = false, $show_author = false, $show_edit = false, $task_stage_num = null, $bonus_stage_num = null, $is_revision = false, $is_archive  = false)
{
    $current_user = get_current_user_id();
    $is_admin = go_user_is_admin();

    $text_toggle = true;
    $url_content = null;
    $video_content = null;
    $media_content = null;
    $min_words = null;

    //Get post info
    //get the post object for this post
    $post = get_post($blog_post_id, OBJECT, 'edit');
    //get content from the object
    $author_id = $post->post_author;
    if (intval($current_user) === intval($author_id)) {
        $is_current_user = true;
    } else {
        $is_current_user = false;
    }
    $content = $post->post_content;
    $post_date = $post->post_date;
    $post_modified = $post->post_modified;
    //apply the text filters
    $text_content = apply_filters('go_awesome_text', $content);
    //get info from the post_id
    $title = get_the_title($blog_post_id);
    if (isset($blog_post_id)) {
        $blog_meta = get_post_meta($blog_post_id);
    }

    //if the task that this post is attached to was not sent, try to get the task_id
    if (!isset($go_blog_task_id)) {
        $go_blog_task_id = (isset($blog_meta['go_blog_task_id'][0]) ? $blog_meta['go_blog_task_id'][0] : null); //for posts created before v4.6
        if (empty($go_blog_task_id)) {
            $go_blog_task_id = wp_get_post_parent_id($blog_post_id);//for posts created after v4.6
        }
    }

    echo "<script>console.log('task id: {$go_blog_task_id}')</script>";

    //if the task_id is not 0, get some info about it
    if ($go_blog_task_id != 0) {
        $task_title = get_the_title($go_blog_task_id);
        $task_url = get_permalink($go_blog_task_id);
        if (empty($title)) {
            $title = $task_title;
        }
    }

    ob_start();
    echo "<div class='go_blog_post_wrapper go_blog_post_wrapper_$blog_post_id' style='padding: 20px;margin: 10px; background-color: white; border: 1px solid;' data-postid ='{$blog_post_id}'>";

    $status = get_post_status($blog_post_id);
    if ($status == 'draft') {
        echo "<span style='color: red;'>DRAFT</span>";
    }

    echo "<div class='go_post_title'>";
    if (!empty($task_url) && !$is_archive) {
        echo "<h2><a href='{$task_url}'>" . $title . "</a></h2>";
    } else {
        echo "<h2>" . $title . "</a></h2>";
    }
    echo "</div>";

    echo "<div class='go_blog_meta' style='font-size: .9em;'>";
    if ($show_author) {
        $user_data = get_userdata($author_id);
        $blogURL = get_site_url() . "/user/" . $user_data->user_login;

        $user_display_name = go_get_user_display_name($author_id);

        echo "<span id='go-name'>Author: {$user_data->first_name} {$user_data->last_name} (<a href='{$blogURL}'>{$user_display_name}</a>)</span><br>";
    }

    echo "post date: " . date("M d, Y H:i a", strtotime($post_date));
    if($post_modified != $post_date){
        echo "<br> modified date: " . date("M d, Y H:i a", strtotime($post_modified));
    }
    echo "</div>";
    //for each number of elements
        //get the type
        //get the uniqueID
        //get the data by UniqueID
        //if no data, get it by old key
        //if no data, get it by actions table
        //verify data
    echo "<div class='go_blog_elements'>";

    //if this post was submitted from a task, then add the task required fields
    if($go_blog_task_id != 0) {
        if (!isset($task_stage_num)) {
            //get the stage number from actions or in meta--
            $task_stage_num = (isset($blog_meta['go_blog_task_stage'][0]) ? $blog_meta['go_blog_task_stage'][0] : null);
        }

        echo "<script>console.log('tsn: {$task_stage_num}')</script>";
        $i = $task_stage_num;
        echo "<script>console.log('i: {$i}')</script>";
        //if $i (task stage) is not set, then this must be a bonus stage
        $custom_fields = go_post_meta($go_blog_task_id);
        //variables for retrieving v4 content not in blog
        global $wpdb;
        $go_actions_table_name = "{$wpdb->prefix}go_actions";
        $user_id = get_current_user_id();

        if ($i !== null) {//regular stage
            $num_elements = (isset($custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements'][0]) ? $custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements'][0] : false);
            $text_toggle = (isset($custom_fields['go_stages_' . $i . '_blog_options_v5_blog_text_toggle'][0]) ? $custom_fields['go_stages_' . $i . '_blog_options_v5_blog_text_toggle'][0] : true);
        } else {//bonus stage
            $num_elements = (isset($custom_fields['go_bonus_stage_blog_options_v5_blog_elements'][0]) ? $custom_fields['go_bonus_stage_blog_options_v5_blog_elements'][0] : false);
            $text_toggle = (isset($custom_fields['go_bonus_stage_blog_options_v5_bonus_blog_text_toggle'][0]) ? $custom_fields['go_bonus_stage_blog_options_bonus_blog_text_toggle'][0] : true);
        }

        echo "<script>console.log('#: {$num_elements}')</script>";
        for ($x = 0; $x < $num_elements; $x++) {
            if ($i !== null) {//regular stage
                $bonus = false;
                $type = (isset($custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_element'][0]) ? $custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_element'][0] : 0);
                $uniqueid = (isset($custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_uniqueid'][0]) ? $custom_fields['go_stages_' . $i . '_blog_options_v5_blog_elements_' . $x . '_uniqueid'][0] : 0);
            } else {//bonus stage
                $bonus = true;
                $type = (isset($custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_element'][0]) ? $custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_element'][0] : 0);
                $uniqueid = (isset($custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_uniqueid'][0]) ? $custom_fields['go_bonus_stage_blog_options_v5_blog_elements_' . $x . '_uniqueid'][0] : 0);
                //$task_stage_num = $bonus_stage_num - 1;
            }
            echo "<script>console.log('type: {$type}')</script>";
            //get the content by UniqueID
            $content = (isset($blog_meta[$uniqueid][0]) ? $blog_meta[$uniqueid][0] : null);
            if ($type == 'URL') {
                echo "<script>console.log('1: {$content}')</script>";
                if ($content === null) {
                    $content = (isset($blog_meta['go_blog_url'][0]) ? $blog_meta['go_blog_url'][0] : null);//v4 data
                    echo "<script>console.log('2: {$content}')</script>";
                }
                if (!empty($content )) {
                    go_print_URL_check_result($content);

                }



            }
            else if ($type == 'File') {
                //if null, check for v4 data
                if ($content === null) {
                    $content = (isset($blog_meta['go_blog_media'][0]) ? $blog_meta['go_blog_media'][0] : null);//v4 data
                }
                //if v5 or v4 data found, print the result
                if (!empty($content)) {
                    go_print_upload_check_result($content);
                }

            }
            else if ($type == 'Video') {
                if ($content === null) {
                    $content = (isset($blog_meta['go_blog_video'][0]) ? $blog_meta['go_blog_video'][0] : null);//v4 data
                }
                if (!empty($content)) {
                    echo "<div class='go_required_blog_content width100'>";
                    $video_content = apply_filters('go_awesome_text', $content);
                    echo "$video_content";
                    echo "</div>";
                }
            }

        }
    }

    $buffer = ob_get_contents();
    ob_end_clean();
    echo $buffer;

    echo "</div>";

    if($text_toggle) {
        echo "<div class='go_blog_content'>". $text_content . "</div>";
    }

    if ( $is_current_user || $is_admin ) {

        if (!$is_revision) {
            echo "<div class='go_blog_form_footer'>";
            go_blog_status($blog_post_id, $is_admin, false, $is_archive);
            echo "<div><div class='go_blog_actions'>";

            if(!$is_archive) {
                if (intval($current_user) == intval($author_id) && $show_edit) {//if current user then show edit and maybe trash
                    echo "<div class='go_blog_opener go_blog_opener_round go_button_round' blog_post_id ='{$blog_post_id}' data-check_for_understanding ='{$check_for_understanding}'><span class='go_round_inner'><i class='fas fa-pencil-alt'></i></span></div>";
                }
                if (($current_user == $author_id || $is_admin) && $check_for_understanding == false && empty($go_blog_task_id) && ($status !='trash')) {
                    echo '<div class="go_blog_trash go_button_round" blog_post_id ="' . $blog_post_id . '"><span class="go_round_inner"><i class="fas fa-trash"></i></span></div>';
                } else if ($is_admin) {
                    echo '<div data-uid="" data-task="' . $blog_post_id . '" class="go_reset_task_clipboard go_button_round go_blog_reset" ><span class="go_round_inner"><i class="fas fa-times-circle"></i></span></div>';
                    //echo '<span class="go_blog_trash" blog_post_id ="' . $blog_post_id . '"><i class="fa fa-times-circle fa-2x"></i></span>';
                }
            }

            echo "</div></div></div>";
        }

        if ($with_feedback && ($is_current_user || $is_admin)) {
            do_action('go_blog_template_after_post', $blog_post_id, true, $is_archive);
        }
    }

    echo "</div>";

    if($is_revision){
        $parent = wp_get_post_parent_id($blog_post_id);
        ?>
        <div>
            <button class="go_restore_revision" style="float:right;" data-post_id="<?php echo $blog_post_id; ?>"
                    data-parent_id="<?php echo $parent; ?>">Restore this post
            </button>
        </div>
        <?php

    }
}

/**
 * @param $blog_post_id
 * @param $is_admin
 */
function go_blog_status($blog_post_id, $is_admin, $is_form = false, $is_archive = false){
    $status = go_post_status_icon($blog_post_id, $is_archive);
    $private = go_blog_is_private($blog_post_id);
    if (!$is_form) {
        $favorite = go_blog_favorite($blog_post_id, $is_admin, $is_archive);
    }else{
        $favorite = '';
    }
    $percent = get_post_meta( $blog_post_id, 'go_feedback_percent', true );

    $direction = (($percent > 0) ? '+' : '');
    $class = (($percent > 0) ? 'up' : 'down');
    if ($percent == '' || empty($percent)) {
        $percent_hide = " style='display:none;' ";
    }else{
        $percent_hide = '';
    }
        $percent =  '<div class="go_status_percent '.$class.'"'.$percent_hide.' ><strong>'.$direction.$percent.'%</strong></div>';

    if (!empty($status) || !empty($private) || !empty($favorite) ) {

        echo "
            <div class='go_blog_status'>
            <div class='go_blog_status_icons'>";

            echo $status . $private . $favorite . $percent;
            echo "</div></div>";
    }else{
        echo "<div></div>";
    }

}

// Register Custom Post Type
/**
 *
 */
function go_blogs() {

    $labels = array(
        'name'                  => _x( 'User Blog Posts', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'User Blog Post', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'User Blog Posts', 'text_domain' ),
        'name_admin_bar'        => __( 'User Blog Post', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All Items', 'text_domain' ),
        'add_new_item'          => __( 'Add New Item', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),

    );
    $rewrite = array(
        'slug'                  => 'blogs',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __( 'User Blog Post', 'text_domain' ),
        'description'           => __( 'User Blog Posts', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'author', 'revisions' ),
        'taxonomies'            => array(  ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => false,
        'menu_position'         => 20,
        'show_in_admin_bar'     => false,
        'show_in_nav_menus'     => false,
        'can_export'            => false,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        //'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'go_blogs', $args );

}
add_action( 'init', 'go_blogs', 0 );

// Register custom post status
/**
 *
 */
function go_custom_post_status(){
    register_post_status( 'unread', array(
        'label'                     => _x( 'Unread', 'post' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Unread <span class="count">(%s)</span>', 'Unread <span class="count">(%s)</span>' ),
    ) );

    register_post_status( 'read', array(
        'label'                     => _x( 'Read', 'post' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Read <span class="count">(%s)</span>', 'Read <span class="count">(%s)</span>' ),
    ) );

    register_post_status( 'reset', array(
        'label'                     => _x( 'Reset', 'post' ),
        'public'                    => false,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Reset <span class="count">(%s)</span>', 'Reset <span class="count">(%s)</span>' ),
    ) );

    register_post_status( 'revise', array(
        'label'                     => _x( 'Revise', 'post' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Revise <span class="count">(%s)</span>', 'Revise <span class="count">(%s)</span>' ),
    ) );
}
add_action( 'init', 'go_custom_post_status', 0 );

/**
 * Creates new rewrite rules.
 * Only needs to to be run on activation/flushing of rewrite rules
 */
function go_blogs_rewrite() {
    // we are telling wordpress that if somebody access yoursite.com/all-post/user/username
    // wordpress will do a request on this query var yoursite.com/index.php?query_type=user_blog&uname=username
    //flush_rewrite_rules();

    add_rewrite_rule( "^user/([^/]*)/page/(.*)/?", 'index.php?query_type=user_blog&uname=$matches[1]&paged=$matches[2]', "top");
    add_rewrite_rule( "^user/(.*)", 'index.php?query_type=user_blog&uname=$matches[1]', "top");

}
add_action( 'init', 'go_blogs_rewrite' );

/**
 * @param $vars
 * @return array
 */
function go_custom_query($vars ) {
    // we will register the two custom query var on wordpress rewrite rule
    $vars[] = 'query_type';
    $vars[] = 'uname';
    $vars[] = 'paged';
    return $vars;
}
// Then add those two functions on their appropriate hook and filter
add_filter( 'query_vars', 'go_custom_query' );

/**
 * @param $template
 * @return string
 */
function go_template_loader($template){

    // get the custom query var we registered
    $query_var = get_query_var('query_type');

    // load the custom template if ?query_type=all_post is  found on wordpress url/request
    if( $query_var == 'user_blog' ){
        $directory = plugin_dir_path( __FILE__ ) . '/templates/go_user_blog_template.php';
        //$directory = plugin_dir_path( __FILE__ ) . '/templates/go_save_blog.php';
        return $directory;
    }
    return $template;
}
add_filter('template_include', 'go_template_loader');



