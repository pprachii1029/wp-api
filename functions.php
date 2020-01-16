
<?php

/* =======================================
 *  JobCareer Functions - Child Theme
 */

// register theme menu
function cs_jobcareer_child_my_menus() {
    register_nav_menus(
            array(
                'main-menu' => __('Main Menu', 'jobcareer'),
                'footer-menu' => __('Footer Menu', 'jobcareer'),
            )
    );
}
add_action('init', 'cs_jobcareer_child_my_menus');

if (!get_option('cs_jobcareer_child')) {
    update_option('cs_jobcareer_child', 'jobcareer_child_theme');
    $theme_mod_val = array();
    $term_exists = term_exists('main-menu', 'nav_menu');
    if (!$term_exists) {
        $wpdb->insert(
                $wpdb->terms, array(
            'name' => 'Main Menu',
            'slug' => 'main-menu',
            'term_group' => 0
                ), array(
            '%s',
            '%s',
            '%d'
                )
        );
        $insert_id = $wpdb->insert_id;
        $theme_mod_val['main-menu'] = $insert_id;
        $wpdb->insert(
                $wpdb->term_taxonomy, array(
            'term_id' => $insert_id,
            'taxonomy' => 'nav_menu',
            'description' => '',
            'parent' => 0,
            'count' => 0
                ), array(
            '%d',
            '%s',
            '%s',
            '%d',
            '%d'
                )
        );
    } else {
        $theme_mod_val['main-menu'] = $term_exists['term_id'];
    }
    $term_exists = term_exists('top-menu', 'nav_menu');
    if (!$term_exists) {
        $wpdb->insert(
                $wpdb->terms, array(
            'name' => 'Top Menu',
            'slug' => 'top-menu',
            'term_group' => 0
                ), array(
            '%s',
            '%s',
            '%d'
                )
        );
        $insert_id = $wpdb->insert_id;
        $theme_mod_val['top-menu'] = $insert_id;
        $wpdb->insert(
                $wpdb->term_taxonomy, array(
            'term_id' => $insert_id,
            'taxonomy' => 'nav_menu',
            'description' => '',
            'parent' => 0,
            'count' => 0
                ), array(
            '%d',
            '%s',
            '%s',
            '%d',
            '%d'
                )
        );
    } else {
        $theme_mod_val['top-menu'] = $term_exists['term_id'];
    }

    $term_exists = term_exists('footer-menu', 'nav_menu');
    if (!$term_exists) {
        $wpdb->insert(
                $wpdb->terms, array(
            'name' => 'Footer Menu',
            'slug' => 'footer-menu',
            'term_group' => 0
                ), array(
            '%s',
            '%s',
            '%d'
                )
        );
        $insert_id = $wpdb->insert_id;
        $theme_mod_val['footer-menu'] = $insert_id;
        $wpdb->insert(
                $wpdb->term_taxonomy, array(
            'term_id' => $insert_id,
            'taxonomy' => 'nav_menu',
            'description' => '',
            'parent' => 0,
            'count' => 0
                ), array(
            '%d',
            '%s',
            '%s',
            '%d',
            '%d'
                )
        );
    } else {
        $theme_mod_val['footer-menu'] = $term_exists['term_id'];
    }
    set_theme_mod('nav_menu_locations', $theme_mod_val);
}

add_shortcode( 'Usertemp', 'DisplaySolidForm_shortcode');

function DisplaySolidForm_shortcode() {
ob_start();
require 'templates/usertem.php';
$return_string = ob_get_clean();
return $return_string;
   
}



add_action( 'admin_bar_menu', 'wp_admin_bar_my_custom_account_menu', 11 );
 
function wp_admin_bar_my_custom_account_menu( $wp_admin_bar ) {
$user_id = get_current_user_id();
$current_user = wp_get_current_user();
$profile_url = get_edit_profile_url( $user_id );
 
if ( 0 != $user_id ) {
/* Add the "My Account" menu */
$avatar = get_avatar( $user_id, 28 );
$howdy = sprintf( __('Hola, %1$s'), $current_user->display_name );
$class = empty( $avatar ) ? '' : 'with-avatar';
 
$wp_admin_bar->add_menu( array(
'id' => 'my-account',
'parent' => 'top-secondary',
'title' => $howdy . $avatar,
'href' => $profile_url,
'meta' => array(
'class' => $class,
),
) );
 
}
}






add_action('after_setup_theme', 'remove_admin_bar');
 
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}

add_action('wp_logout','auto_redirect_after_logout');
function auto_redirect_after_logout(){
wp_redirect(get_site_url() . "/jefes/register");
exit();
}

add_action( 'template_redirect', 'redirect_to_specific_page' );
function redirect_to_specific_page() {
if ( (is_page(10492) && !is_user_logged_in())) {
    flush_rewrite_rules();
    wp_redirect( get_site_url() . "/jefes/register"); 
    exit();
}
if ( (is_page(10461) && !is_user_logged_in())) {
    flush_rewrite_rules();
    wp_redirect( get_site_url() . "/jefes/register"); 
    exit();
}
}


// --------------api function--------------- 

function api_authentication(){
    $job_id = $_POST['job_id'];
    get_job_detail($job_id);
    return count_usermeta('cs-user-jobs-applied-list', serialize(strval($job_id)), 'LIKE', true);
}
function dateDiffInDays($date1, $date2)  
{ 
	//return $date2;
	$date1 	= strtotime($date1);  
	$date2 	= strtotime($date2);  
	  
	return $diff 	= (int)(abs($date2 - $date1)/86400);  
	  

}
function user_profile(){
    global $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path     = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';
    $ID            = $_POST['user_id'];
    $device_token  = $_POST['device_token'];
    $user          = get_user_by( 'id', $ID );
    $profile_data  = array();
    $roles         = array("cs_candidate","cs_employer");

    if (!isset($ID) || empty($ID)) {
     return new WP_Error('missing_params:', __('Username is not found'),array('status'=>400) );
    }
    if ($user == false) {
     return new WP_Error( 'Invalid_user_id:', __('Invalid user id'), array('status' => 401) );
    }
    $user_role = $user->roles;
    $user_role = $user_role[0];
    $ab_img    = get_usermeta( $ID, 'user_img' );
    if ( !in_array($user_role, $roles)) {
        return new WP_Error( 'Invalid_user_role:', __('not allowed'), array('status' => 401) );
    }
    if ($ab_img == '') {
        $ab_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
        //bloginfo('stylesheet_directory').'/images/user_image.png';
    }else{
        $ab_img = $imge_path.$ab_img;
    }

    $profile_data['data']->ID                     = $ID;
    $profile_data['data']->user_login             = $user->user_login;
    $profile_data['data']->display_name           = $user->display_name;
    $profile_data['data']->meta->user_email       = $user->user_email;
    $profile_data['data']->meta->user_url         = $user->user_url;
    $profile_data['data']->meta->wp_capabilities  = get_usermeta( $ID,'wp_capabilities' );
    $profile_data['data']->meta->cs_job_title     = get_usermeta( $ID,'cs_job_title' );
    $profile_data['data']->meta->cs_allow_search  = get_usermeta( $ID,'cs_allow_search' );
    $profile_data['data']->meta->cs_specialisms   = get_usermeta( $ID,'cs_specialisms' );
    $profile_data['data']->meta->description      = get_usermeta( $ID,'description');
    $profile_data['data']->meta->cs_facebook      = get_usermeta( $ID,'cs_facebook' );
    $profile_data['data']->meta->cs_twitter       = get_usermeta( $ID,'cs_twitter' );
    $profile_data['data']->meta->cs_google_plus   = get_usermeta( $ID,'cs_google_plus' );
    $profile_data['data']->meta->cs_linkedin      = get_usermeta( $ID,'cs_linkedin' );
    $profile_data['data']->meta->cs_phone_number  = get_usermeta( $ID,'cs_phone_number' );
    $profile_data['data']->meta->user_img         = $ab_img;
    $profile_data['role']                         = $user->roles;
    if ( $user_role == "cs_candidate") {
        $profile_data['data']->meta->companyworkfor  = get_usermeta( $ID,'companyworkfor' );
        $profile_data['data']->meta->lookingfor      = get_usermeta( $ID,'lookingfor' );
        $profile_data['data']->meta->Worklocation    = get_usermeta( $ID,'Worklocation' );
        $profile_data['data']->meta->cs_candidate_cv = get_usermeta( $ID,'cs_candidate_cv' );
        $profile_data['data']->meta->cs_cover_letter = get_usermeta( $ID,'cs_cover_letter' );
    }
    if ( $user_role == "cs_employer") {
        $profile_data['data']->meta->type            = get_usermeta( $ID,'type' );
    }
    $response = new WP_REST_Response(array('message'=> 'Selected user deatils','data'=>array('status'=> 200,'params'=>$profile_data) ) );
    return $response;

}
// Uplod media
function user_profile_pic($f_name,$f_path,$user_id){
   global $plugin_user_images_directory;
   // $f_name = $_FILES['profile_pic']['name'];
   // $f_path = $_FILES['profile_pic']['tmp_name'];
   // $f_type = $_FILES['profile_pic']['type'];
   // $user_id = $_POST['user_id'];
   $file_name = $f_name;
   $wp_upload_dir  = wp_upload_dir();
   $patth  = $wp_upload_dir['basedir'].'/'.$plugin_user_images_directory.'/'.$f_name;
   $extra  = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/'.$f_name;
   $ext    = pathinfo($f_name, PATHINFO_EXTENSION); 
   //return $ext;
   require_once ABSPATH . 'wp-admin/includes/file.php';
   require_once ABSPATH . 'wp-admin/includes/media.php';
   if (file_exists($patth)){

       $file_name  = date('d'.'m'.'y'.'H'.'i'.'s',time()).'.'.substr(strrchr($f_name,'.'), 1);
       $patth      = $wp_upload_dir['basedir'].'/'.$plugin_user_images_directory.'/'.$file_name;
       $extra      = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/'.$file_name;
       $uploded_file   = move_uploaded_file( $f_path, $patth );
   }else{
       $uploded_file   = move_uploaded_file( $f_path, $patth );
   }
   if ( $uploded_file == true) {
       $rawBaseName = pathinfo($file_name, PATHINFO_FILENAME );
       $fileurl =  ABSPATH.'wp-content/uploads/'.$plugin_user_images_directory.'/'.$file_name;
       $img = wp_get_image_editor(ABSPATH.'wp-content/uploads/'.$plugin_user_images_directory.'/'.$file_name);
       if ( ! is_wp_error( $img ) ) {
           $sizes_array = array(
               array('width' => 270, 'height' => 203, 'crop' => true),
               array('width' => 236, 'height' => 168, 'crop' => true),
               array('width' => 200, 'height' => 200, 'crop' => true),
               array('width' => 180, 'height' => 135, 'crop' => true),
               array('width' => 150, 'height' => 113, 'crop' => true),
           );
           $resize = $img->multi_resize($sizes_array, true);
           //$img->resize( 270, 203, true );
           // $saved_img = $img->save( $wp_upload_dir['basedir'].'/'.$plugin_user_images_directory.'/' . $rawBaseName.'-270X203.'.$ext );
           $pass_img = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/'.$resize[0]['file'];
           $attachment = array(
            'guid'           => $pass_img,
               'post_mime_type' => $f_type,
               'post_title'     => '/'.$plugin_user_images_directory.'/'.$saved_img['file'],
               'post_content'   => '',
               'post_status'    => 'inherit',
               'post_author'    => $user_id
           );
           $attach_id   = wp_insert_attachment( $attachment, $pass_img );

           require_once(ABSPATH . 'wp-admin/includes/image.php');
           $attach_data = wp_generate_attachment_metadata( $attach_id, $fileurl );
           update_option('job_image_path', $wp_upload_dir['baseurl']);
           wp_update_attachment_metadata( $attach_id, $attach_data );
           //wp_get_attachment_url( $attach_id, $size = 'medium_large');
           return $resize[0]['file'];

       }  
   }else{
       return '';
   }
}//end uplod media


// Uplod media
function uplod_media($f_name,$f_path){
    $upload         = wp_upload_bits( $f_name, null, file_get_contents( $f_path ) );
    $wp_filetype    = wp_check_filetype( basename( $upload['file'] ), null );
    $wp_upload_dir  = wp_upload_dir();

    $attachment = array(
        'guid'           => $wp_upload_dir['baseurl'] . _wp_relative_upload_path( $upload['file'] ),
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => preg_replace('/\.[^.]+$/', '', basename( $upload['file'] )),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );
    $attach_id   = wp_insert_attachment( $attachment, $upload['file'] );

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
    wp_update_attachment_metadata( $attach_id, $attach_data );
    $attachment_url = wp_get_attachment_url( $attach_id, $size = 'medium_large');
    return $attachment_url;
}//end uplod media

// have child function
function has_comment_children_wpse( $comment_id ) {
    return get_comments( [ 'parent' => $comment_id, 'count' => true ] ) > 0;
}
//end have child

// start function
function get_user_meta_value($object, $field_name, $request) {
    global $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';
    // print_r(get_user_meta($object['id']));
    // die();
    $meta_result = array(
        'user_img'          => get_user_meta ($object['id'],'user_img',true),
        'Worklocation'      =>  get_user_meta ($object['id'],'Worklocation',true),
        'cs_allow_search'   =>  get_user_meta ($object['id'],'cs_allow_search',true)
        );
    return $meta_result;
} //end function

// start function
function get_user_details($object, $field_name, $request) {
    $user_result  = array(
        'user_role'  => get_userdata($object['id'])->roles,
        'user_name'  => get_userdata($object['id'])->user_login,
        'user_email' => get_userdata($object['id'])->user_email
    );
  return $user_result;
  
}//end function

function user_registration(){
    $username           = $_POST['username'];
    $email              = $_POST['email'];
    $password           = $_POST['password'];
    $wp_capabilities    = $_POST['wp_capabilities']; 
    $organization       = $_POST['organization']; 
    $phone_number       = $_POST['phone_number'];
    $allow              = $_POST['allow_in_search'];
    $fields             = $_POST['selector_fields'];
    $device_token       = $_POST['device_token'];
    $selector_fields    = explode( "," , $fields );
    //$selector_fields = array( 'apple', 'banana', 'orange' );
    //$selector_field_value= serialize($selector_fields);
    $error = array();
    $data  = array();

    // validation
    if ( $username == '') {
        $error['username']  = "User name can't be empty";
    }
    if ( !preg_match('/^[a-zA-Z0-9._]*$/', $username) ) {
        $error['username']  = "Enter a valid user name without using special character";
    }
    if(!is_email($email)){
        $error['email']     = "Enter a valid email address";
    }
    if($password == ''){
        $error['password'] = "Password can't be empty";
    }
    if($wp_capabilities == ''){
        $error['user_role'] = "Select user role";
    }
    // end validation

    //error message
    if (!empty($error)){
        return new WP_Error( 'params_error', __('parameters error'), array( 'status' => 400 ,'params'=>$error) );
    }else{

        $user_exist  = username_exists( $username );
        $email_exist = email_exists($email);

        if ( $user_exist ) {
            $error['username'] = "user name already exist.";
        }
        if ( $email_exist ) {
            $error['email'] = "user email already exist.";
        }  
        // if start
        if (!empty($error)){
            return new WP_Error( 'already_exist', __('already exist'), array( 'status' => 400 ,'params'=>$error) );
        }else{
            $user_id = wp_create_user( $username, $password, $email );
            if( !is_wp_error($user_id) ) {
                $user = get_user_by( 'id', $user_id );
                if($wp_capabilities == 'cs_employer'){

                    $user->set_role( 'cs_employer' );
                    // update meta
                    //add_user_meta($user_id , 'description' ,'');
                    add_user_meta( $user_id, 'dismissed_wp_pointers', 'wp496_privacy' );
                    if(!empty($allow)){
                        add_user_meta($user_id, 'cs_allow_search', $allow);
                    }else{
                        add_user_meta($user_id, 'cs_allow_search', 'yes');
                    }
                    add_user_meta($user_id , 'cs_user_status', 'active');
                    if(!empty($phone_number)){
                        add_user_meta ($user_id, 'cs_phone_number', $phone_number);
                    }
                    if(!empty($fields)){
                        add_user_meta( $user_id, 'cs_specialisms', $selector_fields );
                    }
                    add_user_meta($user_id , 'cs_cover_employer_img' ,'');
                    add_user_meta($user_id , 'cs_facebook' ,'');
                    add_user_meta($user_id , 'cs_google_plus' ,'');
                    add_user_meta($user_id , 'cs_twitter' ,'');
                    add_user_meta($user_id , 'cs_linkedin' ,'');
                    add_user_meta($user_id , 'cs_post_loc_country' ,'');
                    add_user_meta($user_id , 'cs_post_loc_city' ,'');
                    add_user_meta($user_id , 'cs_post_comp_address' ,'');
                    add_user_meta($user_id , 'cs_post_loc_latitude' ,'');
                    add_user_meta($user_id , 'cs_post_loc_longitude' ,'');
                    add_user_meta($user_id , 'type' ,'');
                    add_user_meta($user_id , 'cs_user' ,$user_id);
                    add_user_meta($user_id , 'user_img' ,'');
                    add_user_meta($user_id , 'cover_user_img' ,'');
                    add_user_meta($user_id , 'device_token','');
                    
                    if(!empty($organization)){
                        wp_update_user( array ('ID' => $user_id, 'display_name' => $organization)); 
                    }
                    wp_update_user( array ('ID' => $user_id, 'user_status' => $active));
                    $error['data'] = "successfully user created";
                    $response = new WP_REST_Response(array('message'=> 'successfully user created','data'=>array('status'=> 201,'params'=>$user) ) );
                    return $response;

                }else{

                    $user->set_role( 'cs_candidate' );

                    add_user_meta($user_id , 'user_img' ,'');
                    add_user_meta($user_id , 'cs_specialisms' ,'');
                    //add_user_meta($user_id , 'description' ,'');
                    add_user_meta($user_id , 'cs_facebook' ,'');
                    add_user_meta($user_id , 'cs_google_plus' ,'');
                    add_user_meta($user_id , 'cs_twitter' ,'');
                    add_user_meta($user_id , 'cs_linkedin' ,'');
                    add_user_meta($user_id , 'cs_phone_number' ,'');
                    add_user_meta($user_id , 'type' ,'');
                    add_user_meta($user_id , 'cs_job_title' ,'');
                    add_user_meta($user_id , 'companyworkfor' ,'');
                    add_user_meta($user_id , 'lookingfor' ,'');
                    add_user_meta($user_id , 'Worklocation' ,'');
                    add_user_meta($user_id , 'cover_user_img' ,'');
                    add_user_meta($user_id , 'cs_candidate_skills_percentage' ,'');
                    add_user_meta($user_id , 'cs_user' ,$user_id);
                    add_user_meta($user_id , 'cs_candidate_cv', '');
                    add_user_meta($user_id , 'cs_cover_candidate_img', '');
                    add_user_meta($user_id , 'dismissed_wp_pointers', 'wp496_privacy' );
                    add_user_meta($user_id , 'cs_user_status', 'active');
                    add_user_meta($user_id , 'device_token', '');
                    if(!empty($allow)){
                        add_user_meta($user_id, 'cs_allow_search', $allow);
                    }else{
                        add_user_meta($user_id, 'cs_allow_search', 'yes');
                    }
                    wp_update_user( array ('ID' => $user_id, 'user_status' => $active));
                    $response = new WP_REST_Response(array('message'=> 'successfully user created','data'=>array('status'=> 201,'params'=>$user) ) );
                    return $response;
                }
                
            }else{
                return new WP_Error( 'somthing_wrong', __('something went worng '), array( 'status' => 401 ) );
            }
        }// end here

    } //end here
       
}// end register function

// start login function
function user_login(){
    global $wpdb, $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $login_type     = $_POST['login_type'];
    $user_login     = $_POST['username'];
    $pass_login     = $_POST['password'];
    $email          = $_POST['user_email'];
    $device_token   = $_POST['device_token'];
    $username       = trim($user_login);
    $password       = trim($pass_login);
    $error_messagge = array();
    $data           = array();
    if( $login_type == 'social_login'){
        $user = get_user_by( 'email', $email );
        if($email == ''){

            $error_messagge['email'] = "Empty email.";
            return new WP_Error( 'login_fail', __('Login Fail!'), array( 'status' => 412 ,'params'=>$error_messagge) );
        }
        if($user){
            $expiration = time() + apply_filters('auth_cookie_expiration', 1209600, $user->ID, true);
            $cookie     = wp_generate_auth_cookie($user->ID, $expiration, 'logged_in');
            $user->cookie = $cookie;
            $user_roles             = $user->roles;
            //$all_meta_for_user      = get_user_meta( $user->ID );
            $data['data']->ID                     = $user->ID;
            $data['data']->user_login             = $user->user_login;
            $data['data']->display_name           = $user->display_name;
            $data['role']                         = $user->roles;
            $data['data']->cookie                 = $cookie;
            $data['data']->meta->wp_capabilities  = get_usermeta( $user->ID, $meta_key = 'wp_capabilities' );
            $data['data']->meta->cs_job_title     = get_usermeta( $user->ID, $meta_key = 'cs_job_title' );
            $data['data']->meta->cs_allow_search  = get_usermeta( $user->ID, $meta_key = 'cs_allow_search' );
            $data['data']->meta->cs_specialisms   = get_usermeta( $user->ID, $meta_key = 'cs_specialisms' );
            $data['data']->meta->description      = get_usermeta( $user->ID, $meta_key = 'description' );
            
            $data['data']->meta->cs_facebook      = get_usermeta( $user->ID, $meta_key = 'cs_facebook' );
            $data['data']->meta ->cs_twitter      = get_usermeta( $user->ID, $meta_key = 'cs_twitter' );
            $data['data']->meta->cs_google_plus   = get_usermeta( $user->ID, $meta_key = 'cs_google_plus' );
            $data['data']->meta->cs_linkedin      = get_usermeta( $user->ID, $meta_key = 'cs_linkedin' );
            $data['data']->meta->cs_phone_number  = get_usermeta( $user->ID, $meta_key = 'cs_phone_number' );
            $data['data']->meta->user_email       = $user->user_email;
            $data['data']->meta->user_url         = $user->user_url;
            //$imge_path.get_usermeta( $user->ID, $meta_key = 'user_img' )
            $ab_img = get_usermeta( $user->ID, $meta_key = 'user_img' );
            if ($ab_img == '') {
                $ab_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
            }else{
                $ab_img = $imge_path.get_usermeta( $user->ID, $meta_key = 'user_img' );
            }
            $data['data']->meta->user_img         = $ab_img;

            // candidate
            if( in_array('cs_candidate', $user_roles) ){
                $data['data']->meta->companyworkfor     = get_usermeta( $user->ID, $meta_key = 'companyworkfor' );
                $data['data']->meta->lookingfor         = get_usermeta( $user->ID, $meta_key = 'lookingfor' );
                $data['data']->meta->Worklocation       = get_usermeta( $user->ID, $meta_key = 'Worklocation' );
                $data['data']->meta->cs_candidate_cv    = get_usermeta( $user->ID, $meta_key = 'cs_candidate_cv' );
                $data['data']->meta->cs_cover_letter    = get_usermeta( $user->ID, $meta_key = 'cs_cover_letter' );
            }elseif( in_array('cs_employer', $user_roles) ){
                // employer
                $data['data']->meta->type                = get_usermeta( $user->ID, $meta_key = 'type' );
            }
            $tokenExist = $wpdb->get_results("SELECT * FROM wp_usermeta WHERE user_id = $user->ID and meta_key = 'device_token'");
                                
            if ( empty($tokenExist) ) {
                add_user_meta($user->ID , 'device_token',array($device_token));
            }else{
                $tokens =  get_user_meta($user->ID , 'device_token',true );
                if ( !empty( $tokens )) {
                    $dataToken = array_merge($tokens , array($device_token));
                }else{
                    $dataToken = array($device_token);
                }
                update_user_meta($user->ID , 'device_token', $dataToken );
            }   
            $response = new WP_REST_Response(array('message'=> 'Login successfully!','data'=>array('status'=> 202,'params'=>$data) ) );
            return $response;
        }else{
            return new WP_Error( 'login_fail', __('Login Fail!'), array( 'status' => 412 ) );
        }
    }else{
        $user_exists = false;
        $_user_type  = 'user';
        if(username_exists($username)){ $user_exists = true; } 
        elseif (email_exists($username)){ $_user_type  = 'email'; $user_exists = true; }  
        else{ $error_messagge['username'] = "Invalid User name.";}

        if (!empty($error_messagge)){
                return new WP_Error( 'invalid_username', __('Invalid username'), array( 'status' => 401 ,'params'=>$error_messagge) );
        }else{

            if ($user_exists == true)
            {
                if ($password == '') {
                    $error_messagge['password'] = "Please Enter your password.";
                    return new WP_Error( 'invalid_password', __('Invalid password'), array( 'status' => 401 ,'params'=>$error_messagge) );
                }else{
                   
                        if($_user_type  == 'user'){
                            $user = get_user_by( 'login', $username );
                        }elseif($_user_type == 'email' ){
                            $user = get_user_by( 'email', $username );
                        }
                        
                        if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID) ){

                            if(wp_login( $username, $password )){
                                $expiration = time() + apply_filters('auth_cookie_expiration', 1209600, $user->ID, true);
                                $cookie                 = wp_generate_auth_cookie($user->ID, $expiration, 'logged_in');
                                $user_roles             = $user->roles;
                                //$all_meta_for_user      = get_user_meta( $user->ID );
                                $data['data']->ID                     = $user->ID;
                                $data['data']->user_login             = $user->user_login;
                                $data['data']->display_name           = $user->display_name;
                                $data['role']                         = $user->roles;
                                $data['data']->cookie                 = $cookie;
                                $data['data']->meta->wp_capabilities  = get_usermeta( $user->ID, $meta_key = 'wp_capabilities' );
                                $data['data']->meta->cs_job_title     = get_usermeta( $user->ID, $meta_key = 'cs_job_title' );
                                $data['data']->meta->cs_allow_search  = get_usermeta( $user->ID, $meta_key = 'cs_allow_search' );
                                $data['data']->meta->cs_specialisms   = get_usermeta( $user->ID, $meta_key = 'cs_specialisms' );
                                $data['data']->meta->description      = get_usermeta( $user->ID, $meta_key = 'description' );
                                
                                $data['data']->meta->cs_facebook      = get_usermeta( $user->ID, $meta_key = 'cs_facebook' );
                                $data['data']->meta ->cs_twitter      = get_usermeta( $user->ID, $meta_key = 'cs_twitter' );
                                $data['data']->meta->cs_google_plus   = get_usermeta( $user->ID, $meta_key = 'cs_google_plus' );
                                $data['data']->meta->cs_linkedin      = get_usermeta( $user->ID, $meta_key = 'cs_linkedin' );
                                $data['data']->meta->cs_phone_number  = get_usermeta( $user->ID, $meta_key = 'cs_phone_number' );
                                $data['data']->meta->user_email       = $user->user_email;
                                $data['data']->meta->user_url         = $user->user_url;
                                //$imge_path.get_usermeta( $user->ID, $meta_key = 'user_img' )
                                $ab_img = get_usermeta( $user->ID, $meta_key = 'user_img' );
                                if ($ab_img == '') {
                                    $ab_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
                                }else{
                                    $ab_img = $imge_path.get_usermeta( $user->ID, $meta_key = 'user_img' );
                                }
                                $data['data']->meta->user_img         = $ab_img;

                                // candidate
                                if( in_array('cs_candidate', $user_roles) ){
                                    $data['data']->meta->companyworkfor     = get_usermeta( $user->ID, $meta_key = 'companyworkfor' );
                                    $data['data']->meta->lookingfor         = get_usermeta( $user->ID, $meta_key = 'lookingfor' );
                                    $data['data']->meta->Worklocation       = get_usermeta( $user->ID, $meta_key = 'Worklocation' );
                                    $data['data']->meta->cs_candidate_cv    = get_usermeta( $user->ID, $meta_key = 'cs_candidate_cv' );
                                    $data['data']->meta->cs_cover_letter    = get_usermeta( $user->ID, $meta_key = 'cs_cover_letter' );
                                }elseif( in_array('cs_employer', $user_roles) ){
                                    // employer
                                    $data['data']->meta->type                = get_usermeta( $user->ID, $meta_key = 'type' );
                                }
                                //$device_token = 'fdgfdg';
                                $tokenExist = $wpdb->get_results("SELECT * FROM wp_usermeta WHERE user_id = $user->ID and meta_key = 'device_token'");
                                
                                if ( empty($tokenExist) ) {
                                    add_user_meta($user->ID , 'device_token',array($device_token));
                                }else{
                                    $tokens =  get_user_meta($user->ID , 'device_token',true );
                                    if ( !empty( $tokens )) {
                                        $dataToken = array_merge($tokens , array($device_token));
                                    }else{
                                        $dataToken = array($device_token);
                                    }
                                    update_user_meta($user->ID , 'device_token', $dataToken );
                                }        
                                $response = new WP_REST_Response(array('message'=> 'Login successfully!','data'=>array('status'=> 202,'params'=>$data) ) );

                                return $response;
                            }else{
                                return new WP_Error( 'login_fail', __('Login Fail'), array( 'status' => 412 ) );
                            }
                            
                        }else{
                            $error_messagge['password'] = "Invalid Password";
                            return new WP_Error( 'invalid_password', __('Invalid Password'), array( 'status' => 401 ,'params'=>$error_messagge) );
                        }
                    
                }// end pass check
            }else{
                return new WP_Error( 'invalid_user', __('Invalid user'), array( 'status' => 401 ,'params'=>$error_messagge) );
            }//end user check
        }
    }// check end 
}//end login function
function signOut(){
    $user_id        = $_POST['user_id'];
    $userExist      = get_user_by('id',$user_id);
    $device_token   = $_POST['device_token'];

    if ( !isset( $user_id ) || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $userExist ) || empty( $userExist )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    if ( !isset( $device_token ) || empty( $device_token )) {
        return new WP_Error( 'no_token_found', __('No Token Found'), array( 'status' => 400 ) );
    }

    $tokens = get_user_meta($user_id , 'device_token',true );
    foreach ($tokens as $key => $token) {
        if ( $device_token == $token ) {
            unset($tokens[$key]);
        }
    }
    $tokens = array_values($tokens);
    update_user_meta($user_id , 'device_token',$tokens );
    $response = new WP_REST_Response(array('message'=> 'Logout successfully!','data'=>array('status'=> 202)));
    return $response;
}

// upadte user data
function user_update_data(){
    //update type
    global $wpdb;
    global $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $update_type        = $_POST['update_type'];
    $user_id            = $_POST['user_id'];
    $user               = get_user_by( 'id', $user_id );
    $error              = array();
    $updated            = array();
    //company_profile
    $fullname           =  $_POST['fullname'];
    $cs_job_title       =  $_POST['cs_job_title'];
    $profile_pic        =  $_FILES['profile_pic'];
    $organization       =  $_POST['organization'];
    $allow              =  $_POST['allow_in_search'];
    $fields             =  $_POST['selector_fields'];
    $selector_fields    = explode( "," , $fields );
    $description        =  $_POST['description'];
    $facebook           =  $_POST['facebook'];
    $google_plus        =  $_POST['google_plus'];
    $twitter            =  $_POST['twitter'];
    $linkedIn           =  $_POST['linkedIn'];
    $phone_number       =  $_POST['phone_number'];
    $email              =  $_POST['email'];
    $website            =  $_POST['website'];
    $password           =  $_POST['password'];
    $old_password       =  $_POST['old_password'];
    //candidate
    $candidate_cv       =  $_FILES['candidate_cv'];
    $companyworkfor     =  $_POST['companyworkfor'];
    $looking            =  $_POST['lookingfor'];
    $lookingfor         = explode( "," , $looking );
    $Worklocation       =  $_POST['Worklocation'];
    $cs_cover_letter    =  $_POST['cs_cover_letter'];
    //employee
    $business_type      =  $_POST['business_type'];
    $dataResp           = array();
    //return $selector_fields;
    if($user){
        if($update_type == 'user_profile' ){
            $user_meta=get_userdata($user_id);
            $user_roles=$user_meta->roles;

            if (isset( $fullname ) && !empty( $fullname )) {
                $updated['fullname'] = $wpdb->get_results("UPDATE wp_users SET display_name='$fullname' WHERE ID= $user_id");
                $updated['fullname'] = true;
                //$updated['fullname']    = wp_update_user($user_id , 'display_name' ,$fullname);
            }//end
            if (isset( $cs_job_title )) {
                $updated['cs_job_title'] = update_user_meta($user_id , 'cs_job_title' ,$cs_job_title);
            }//end
            if (isset($profile_pic)) {
                if ($_FILES['profile_pic']['size'] != 0 && $_FILES['profile_pic']['error'] == 0) {
                    $f_name = $_FILES['profile_pic']['name'];
                    $f_path = $_FILES['profile_pic']['tmp_name'];
                    $media_url = user_profile_pic($f_name,$f_path,$user_id);
                    $updated['profile_pic'] = update_user_meta($user_id ,'user_img' ,$media_url);
                }//end
            }
            if (isset( $allow )) {
                $updated['allow_in_search'] = update_user_meta($user_id ,'cs_allow_search' ,$allow);
            }//end
            if (!empty( $fields )) {
                $updated['selector_fields'] = update_user_meta($user_id , 'cs_specialisms' ,$selector_fields);
            }//end
            if (isset( $description )) {
                $updated['description'] = update_user_meta($user_id ,'description',$description);
            }//end
            if (isset( $facebook )) {
                $updated['facebook']    = update_user_meta($user_id , 'cs_facebook' ,$facebook);
            }//end
            if (isset( $google_plus )) {
               $updated['google_plus']  = update_user_meta($user_id , 'cs_google_plus' ,$google_plus);
            }//end
            if (isset( $twitter )) {
                $updated['twitter']     = update_user_meta($user_id , 'cs_twitter' ,$twitter);
            }//end
            if (isset( $linkedIn )) {
                $updated['linkedIn']    = update_user_meta($user_id , 'cs_linkedin' ,$linkedIn);
            }//end
            if (isset( $phone_number )) {
                $updated['phone_number'] = update_user_meta($user_id , 'cs_phone_number' ,$phone_number);
            }//end
            if (isset( $email )) {
                $updated['email'] = wp_update_user( array( 'ID' => $user_id, 'user_email' => $email ) );
            }//end
            if (isset( $organization )) {
                $updated['organization'] = wp_update_user( array( 'ID' => $user_id, 'display_name' => $organization ) );  
            }//end
            if (isset( $website )) {
                $updated['website'] = wp_update_user( array( 'ID' => $user_id, 'user_url' => $website ) );  
            }//end
            $user                      = get_user_by( 'id', $user_id );
            $name      = $wpdb->get_results("SELECT display_name FROM wp_users WHERE ID = $user_id");
            $dataResp['fullname'] = $name[0]->display_name;
            $dataResp['cs_job_title']  = get_user_meta( $user_id , 'cs_job_title',true);
            $value1 = get_user_meta( $user_id , 'user_img' );
            if (empty($value1[0])) {
                $img_val1 = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
            }else{
                $img_val1 = $imge_path.$value1[0];
            }
            $dataResp['profile_pic']     = $img_val1;
            $dataResp['allow_in_search'] = get_user_meta( $user_id , 'cs_allow_search',true );
            $dataResp['selector_fields'] = get_user_meta( $user_id , 'cs_specialisms',true);
            $dataResp['description']     = get_user_meta( $user_id ,'description',true);
            $dataResp['facebook']        = get_user_meta( $user_id , 'cs_facebook',true );;
            $dataResp['google_plus']     = get_user_meta( $user_id , 'cs_google_plus',true );
            $dataResp['twitter']         = get_user_meta( $user_id , 'cs_twitter',true ); 
            $dataResp['linkedIn']        = get_user_meta( $user_id , 'cs_linkedin',true ); 
            $dataResp['phone_number']    = get_user_meta( $user_id , 'cs_phone_number',true );
            $dataResp['email']           = $user->user_email;
            $dataResp['organization']    = $user->display_name;
            $dataResp['website']         = $user->website;
            //
            if( in_array('cs_candidate', $user_roles) ){
                // candidate
                if (isset( $companyworkfor )) {
                    $updated['companyworkfor'] = update_user_meta($user_id , 'companyworkfor' ,$companyworkfor);
                }
                if (isset( $looking ) && !empty( $looking )) {
                    $updated['lookingfor'] = update_user_meta($user_id , 'lookingfor' ,$lookingfor);
                }
                if (isset( $Worklocation )) {
                    $updated['Worklocation'] = update_user_meta($user_id , 'Worklocation' ,$Worklocation);
                }

                if ($_FILES['candidate_cv']['size'] != 0 && $_FILES['candidate_cv']['error'] == 0) {

                    $f_name = $_FILES['candidate_cv']['name'];
                    $f_path = $_FILES['candidate_cv']['tmp_name'];
                    $cv_url = uplod_media($f_name,$f_path);
                    $updated['candidate_cv'] = update_user_meta($user_id , 'cs_candidate_cv' ,$cv_url);
                }else{
                    $media_url = '';
                    $updated['candidate_cv'] = update_user_meta($user_id , 'cs_candidate_cv' ,$cv_url);
                }//end 

                if (isset( $cs_cover_letter )) {
                    $updated['cover_letter'] = update_user_meta($user_id , 'cs_cover_letter' ,$cs_cover_letter);
                } 
                $dataResp['companyworkfor'] = get_user_meta( $user_id , 'companyworkfor' ,true); 
                $dataResp['lookingfor']     = get_user_meta( $user_id , 'lookingfor' ,true);   
                $dataResp['Worklocation']   = get_user_meta( $user_id , 'Worklocation' ,true); 
                $dataResp['candidate_cv']   = get_user_meta( $user_id , 'cs_candidate_cv' ,true);
                $dataResp['cover_letter']   = get_user_meta( $user_id , 'cs_cover_letter' ,true); 
            }elseif( in_array('cs_employer', $user_roles) ){
                // employer
                if (isset( $business_type )) {
                    $updated['business_type'] = update_user_meta($user_id , 'type' ,$business_type);
                }//end
                $dataResp['business_type']  = get_user_meta( $user_id , 'type' ,true); 
            }

            if (!empty($updated)) {
                $response = new WP_REST_Response(array('message'=> 'updated successfully!','data'=>array('status'=> 202,'params'=>$dataResp) ));
                return $response;
            }else{
                $response = new WP_REST_Response(array('message'=> 'No update Found!','data'=>array('status'=> 200) ));
                return $response;
            }

        }elseif ($update_type == 'change_password') {

            if(empty($old_password)){
                $error['password'] = "Invalid Entry";
            }elseif(empty($password) ){
                $error['password'] = "Invalid Entry";
            }
            //code
            if(empty($error)){
                if ( $user && wp_check_password( $old_password, $user->data->user_pass, $user->ID) ){
                    wp_update_user( array ('ID' => $user_id, 'user_pass' => $password));

                    $response = new WP_REST_Response(array('Reset password successfully!','data'=>array('status'=> 202) ));
                    return $response;
                    
                }else{
                    
                    $error['password'] = "Invalid old password";
                    return new WP_Error( 'invalid_params:', __('Invalid parameters'), array( 'status' => 400 ,'params'=>$error) );
                }
            }else{

                return new WP_Error( 'invalid_params:', __('Invalid parameters'), array( 'status' => 400 ,'params'=>$error) );
            }
                
        }else{

            return new WP_Error( 'missing_params:', __('please pass the correct update type'), array( 'status' => 400 ) );
        }
    }else{
        return new WP_Error( 'invalid_params:', __('unauthorize user'), array( 'status' => 400 ) );
    } //authorize user 
    
}//end update data

//delete user
function remove_logged_in_user() {
    $user_id    = $_POST['user_id'];
    $error      = array();
    $user       = get_user_by( 'id', $user_id );
    if(!$user){ 
        $error['username'] = "user not exist";
    } 

    if(empty($error)){
        require_once(ABSPATH.'wp-admin/includes/user.php' );
        if(wp_delete_user( $user_id )){

            $response = new WP_REST_Response(array('User successfully deleted','data'=>array('status'=> 200) ));
            return $response;

        }else{
            return new WP_Error( 'somthing_wrong', __('somthing went worng!'), array( 'status' => 400 ) );
        }

    }else{
        return new WP_Error( 'exist', __('user not exist'), array( 'status' => 406 ) );
    }
}//end delete user

//all posts
function all_posts($request){
    global $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $posts_per_page = $request['per_page'];
    $page           = $request['pages'];

    // if($post_per_page == ''){
    //    $post_per_page = -1; 
    //    $page = 1;
    // }
    // if($page == ''){
    //    $page = 1; 
    // }

    $args = array(
        'posts_per_page'    => $posts_per_page,
        'paged'             => $page,
        'orderby'           => 'date',
        'order'             => 'desc',
    );
    $query = new WP_Query( $args ); 
    $post_data = array();

    // if no posts found return 
    if( empty($query->posts) ){
        return new WP_Error( 'no_posts', __('No post found'), array( 'status' => 200 ) );
    }
    $max_pages = $query->max_num_pages;
    $total     = $query->found_posts;
    $posts     = $query->posts;
    $count_posts = wp_count_posts();
    $published_posts = $count_posts->publish;
    $post_data = array();
    $controller = new WP_REST_Posts_Controller('post');
    // print_r($posts);
    foreach ( $posts as $key=>$post ) {
            $post_data[$key]->post_id     = $post->ID;
            $post_data[$key]->post_name   = $post->post_name;
            $post_data[$key]->author      = $post->post_author;
            $post_data[$key]->title       = $post->post_title;
            $post_data[$key]->content     = strip_shortcodes($post->post_content);
            $post_data[$key]->post_date   = $post->post_date;
            $post_data[$key]->post_status = $post->post_status;
            $post_data[$key]->have_reply  = has_comment_children_wpse( $comment->comment_ID );
            if (has_post_thumbnail($post->ID)){
                $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'single-post-thumbnail' );
                $post_data[$key]->featured_image_src = $image[0];
            }else{
                 $post_data[$key]->featured_image_src = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
            }
            $args = array('post_id' => $post->ID,'parent' => '0','order' => 'ASC');
            $all_comments = get_comments( $args );
            foreach($all_comments as $k=>$comment) :
                $user_image = get_usermeta( $comment->user_id, $meta_key = 'user_img' );
                if ( $user_image == '') {
                   $user_image = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg'; 
                }else{
                    $user_image = $imge_path.$user_image;
                }
                $post_data[$key]->comments[$k]->comment_ID       = $comment->comment_ID ;
                $post_data[$key]->comments[$k]->comment_parent   = $comment->comment_parent ;
                $post_data[$key]->comments[$k]->user_id          = $comment->user_id;
                $post_data[$key]->comments[$k]->comment_author   = $comment->comment_author;
                $post_data[$key]->comments[$k]->comment_content  = $comment->comment_content;
                $post_data[$key]->comments[$k]->comment_approved = $comment->comment_approved;
                $post_data[$key]->comments[$k]->comment_date     = $comment->comment_date;
                $post_data[$key]->comments[$k]->user_img         = $user_image; 
                $post_data[$key]->comments[$k]->have_reply       = has_comment_children_wpse( $comment->comment_ID );
                
            endforeach; 

            $args_rep = array('post_id' => $post->ID,'order' => 'ASC');
            $all_replies = get_comments( $args_rep );
            $data_reply = array();
            foreach($all_replies as $ky=>$replies) :
                $parent_comment_id = $replies->comment_parent ;
                if($parent_comment_id != 0):
                    $re_image = get_usermeta( $replies->user_id, $meta_key = 'user_img' );
                    if ( $re_image == '') {
                       $re_image = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg'; 
                    }else{
                        $re_image = $imge_path.$re_image;
                    }
                    $data_reply[$ky]->reply_ID       = $replies->comment_ID ;
                    $data_reply[$ky]->reply_parent   = $parent_comment_id;
                    $data_reply[$ky]->user_id        = $replies->user_id;
                    $data_reply[$ky]->reply_author   = $replies->comment_author;
                    $data_reply[$ky]->reply_content  = $replies->comment_content;
                    $data_reply[$ky]->reply_approved = $replies->comment_approved;
                    $data_reply[$ky]->reply_date     = $replies->comment_date;
                    $data_reply[$ky]->user_img       = $re_image;//get_avatar_url( $replies->user_id, 32 );
                    $data_reply[$ky]->have_reply     = has_comment_children_wpse( $comment->comment_ID );
                endif;
            endforeach;
            $post_data[$key]->reply = array_values($data_reply);
    }   
    $response = new WP_REST_Response(array('message'=> 'all data','data'=>array('status'=> 200,'params'=>$post_data) ));
    $response->header( 'X-WP-Total', $total ); 
    $response->header( 'X-WP-TotalPages', $max_pages );

    return $response;
        
}
//end posts

// all Job post 
function post_comments($request){
    global $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $posts_per_page = $request['per_page'];
    $page           = $request['pages'];
    $post_id        = $_POST['post_id'];
    $comment_id     = $_POST['comment_id'];
    $comments       = array();
    $error          = array();

    if( $comment_id == ''){
        $comment_id = '0';
    }
    $args = array(
        'number'            => $posts_per_page,
        'offset'            => (($page-1) * $posts_per_page),
        'post_id'           => $post_id,
        'parent'            => $comment_id,
        'order'             => 'ASC',
        //'status'            => 'all'
    );
    //$count          = wp_count_comments( $post_id ); 
    $all_comments   = get_comments( $args );
    $comment_count  = count($all_comments);

    //return $comment_count;
    if( $post_id == ''){
        $error['post_id'] = "Empty post id";
        return new WP_Error( 'missing_params:', __('Missing parameters'), array( 'status' => 400 ,'params'=>$error) );
    }
    if( $comment_count === 0){
        return new WP_Error( 'no_comment:', __('No Comment found'), array( 'status' => 200 ) );
    }

    foreach ($all_comments as $key => $comment) {
        $image = get_user_meta( $comment->user_id , 'user_img' );
        if ($image == '') {
            $img_vval = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
        }else{
            $img_vval = $imge_path.$image[0];
        }
        $comments[$key]->comment_ID       = $comment->comment_ID ;
        $comments[$key]->comment_parent   = $comment->comment_parent ;
        $comments[$key]->user_id          = $comment->user_id;
        $comments[$key]->comment_author   = $comment->comment_author;
        $comments[$key]->comment_content  = $comment->comment_content;
        $comments[$key]->comment_approved = $comment->comment_approved;
        $comments[$key]->comment_date     = $comment->comment_date;
        $comments[$key]->comment_status   = wp_get_comment_status($comment->comment_ID);
        $comments[$key]->user_img         = $img_vval;//get_avatar_url($comment->user_id, 32 );
        $comments[$key]->have_reply       = has_comment_children_wpse($comment->comment_ID);
    }
    $comments = array_values($comments);
    $response = new WP_REST_Response(array('message'=> 'comments','data'=>array('status'=> 200,'params'=>$comments) ));
    return $response;
        
}// end job post
function  user_comments(){
    global $wpdb;
    $time                   = current_time('mysql');
    $comment_post_ID        = $_POST['post_id'];
    $post_ID                = get_post_status($comment_post_ID);
    $user_id                = $_POST['user_id'];
    $user                   = get_user_by( 'id', $user_id );
    $comment_author_email   = $_POST['comment_author_email'];
    $comment_content        = $_POST['comment_content'];
    $comment_parent         = $_POST['comment_parent'];
    $comment_type           = $_POST['comment_type'];
    $user_name              = $user->data->display_name;
    if( $comment_parent == 0){
        $authorId    = get_post_field( 'post_author', $comment_post_ID );
    }else{
        $authorId    = get_comment_author($comment_parent);
    }

    $addNotificationIn      = $authorId;
    $addNotifierId          = $comment_post_ID;
    $action_parent          = '';
    $action_parent          = $comment_parent;
    $message                = $user_name.' replied on your comment.';
    
    

    if ( !isset( $post_ID) || empty( $post_ID )) {
        return new WP_Error( 'no_post_found', __('No post found'), array( 'status' => 400 ) );
    }
    if($post_ID  != 'publish'){
        return new WP_Error( 'not_published', __('Enter a published post'), array( 'status' => 200 ) );
    }

    if ( !isset($user_id) && empty($user_id) ) {
        return new WP_Error( 'invalid_user_id', __('Invalid user id'), array( 'status' => 400 ) );
    }
    if (!$user) {
        return new WP_Error( 'unauthorize', __('Unauthorize user'), array( 'status' => 401 ) );
    }
    if ( !isset($comment_author_email) && empty($comment_author_email) ) {
        return new WP_Error( 'not_found', __('Email id not found'), array( 'status' => 400 ) );
    }
    if ($user->user_email != $comment_author_email) {
        return new WP_Error( 'invalid_email', __('This email is not blongs to the this user id'), array( 'status' => 400 ) );
    }
    if ( !isset($comment_content) && empty($comment_content) ) {
        return new WP_Error( 'invalid_content', __('Invalid content'), array( 'status' => 400 ) );
    }
    if ( !isset($comment_type) && empty( $comment_type )) {
        $comment_type = '';
    }
    $comment_approved = 1;
    if ( isset($comment_parent) && !empty($comment_parent)) {
        $comment_parent = $comment_parent;
        $comment_approved = 0;
    }else{
        $comment_parent = 0;
    }
    if ( $comment_type != 'anspress') { $comment_approved = 1; }
    if ( $user_id  == 1) {
       $comment_approved = 1; 
    }
    $comment_author = $user->user_login;
    $data = array(
        'comment_post_ID'       => $comment_post_ID,
        'comment_author_email'  => $comment_author_email,
        'comment_content'       => $comment_content,
        'comment_parent'        => $comment_parent,
        'user_id'               => $user_id,
        'comment_date'          => $time,
        'comment_approved'      => $comment_approved,
        'comment_author'        => $user->user_login,
        'comment_author_IP'     => $_SERVER['REMOTE_ADDR'],
        'comment_agent'         => $_SERVER['HTTP_USER_AGENT'],
        'comment_type'          => $comment_type,
        'comment_approved'      => $comment_approved,
    );

    $dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND comment_approved != 'trash' AND ( comment_author = '$comment_author' ";
    if ( $comment_author_email ){
        $dupe .= "OR comment_author_email = '$comment_author_email' ";
    }
    $dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
    if ( $wpdb->get_var($dupe) ) {
        do_action( 'comment_duplicate_trigger', $data );
        if ( defined('DOING_AJAX') )
            return new WP_Error( 'duplicate_comment', __('Duplicate comment detected; it looks as though you’ve already said that!'), array( 'status' => 400 ) );
            
        return new WP_Error( 'duplicate_comment', __('Duplicate comment detected; it looks as though you’ve already said that!'), array( 'status' => 400 ) );
    }

    $comment_id = wp_insert_comment($data);
    $value = get_comment($comment_id);
    if ($comment_type == 'anspress') {
        $action_type    = 'answer';
    }else{
        $action_type    = 'comment';
    }
    
    if ( $addNotificationIn != $user_id) {
        $notificationData =  notification_data( $action_type,$addNotifierId );
        $tokens           = get_user_meta($user_id, 'device_token',true);
        if ( !empty($tokens)) {
            foreach ($tokens as $key => $token) {
                iospushnotification($token,$message,$action_type,$notificationData);
            }
        }
        send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id ); 
    }
    
    // if ( $comment_type == 'anspress') {
    //     $post_parent_data   = $wpdb->get_results("SELECT post_parent FROM wp_posts WHERE ID = $comment_post_ID");
    //     $parent = $post_parent_data[0]->post_parent;
    //     if ( $parent != 0) {
    //         $authorId               = get_post_field( 'post_author', $parent );
    //         $addNotificationIn      = $authorId;
    //         $message                = $user_name.' commented on your post.';
    //         $action_type            = 'answer';
    //         if ( $addNotificationIn != $user_id) {
    //             $notificationData   =  notification_data( $action_type,$addNotifierId );
    //             $tokens             = get_user_meta($user_id, 'device_token',true);
    //             if ( !empty($tokens)) {
    //                 foreach ($tokens as $key => $token) {
    //                     iospushnotification($token,$message,$action_type,$notificationData);
    //                 }
    //             }
    //             send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id );
    //         }
              
    //     }
    // }
    // if ( isset($comment_parent) && !empty($comment_parent) ) {
    //     $authorIdData           = get_comment( $comment_parent );
    //     $authorId               = $authorIdData->user_id;
    //     $addNotificationIn      = $authorId;
    //     $message                = $user_name.' replied on your comment.';
    //     $action_type            = 'comment';
    //     if ( $addNotificationIn != $user_id) {
    //         $notificationData   =  notification_data( $action_type,$addNotifierId );
    //         $tokens             = get_user_meta($user_id, 'device_token',true);
    //         if ( !empty($tokens)) {
    //             foreach ($tokens as $key => $token) {
    //                 iospushnotification($token,$message,$action_type,$notificationData);
    //             }
    //         }
    //         send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent ,$user_id); 
    //     }
        
    // }
    $response = new WP_REST_Response(array('message'=> 'successfully! comment posted','data'=>array('status'=> 200,'params'=>$value) ));
    return $response;


}//eo user comments
// delete comment
function  delete_comment(){
    $comment_id   = $_POST['comment_id'];
    $commentExist = get_comment($comment_id);

    if ( !isset( $comment_id ) || empty( $comment_id )) {
        return new WP_Error( 'missing_params:', __('no comment id found'), array( 'status' => 400 ) );
    }
    if ( $commentExist == null) {
        return new WP_Error( 'not_exist:', __('comment does not exist.'), array( 'status' => 412 ) );
    }
    wp_delete_comment($comment_id);
    $response = new WP_REST_Response(array('message'=> 'Deleted successfully!','data'=>array('status'=> 200) ));
    return $response;
}
// eo
//job search
function search_jobs($request){
    global $wpdb,$plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    $keyword            = $_POST['keyword'];
    $current_date       = strtotime(current_time('d-m-Y H:i:s'));
    $post_data          = array();
    if ( isset($keyword) && $keyword != '') {
        $totalRows = $wpdb->get_results("SELECT * from wp_posts where id in (SELECT post_id  FROM wp_postmeta WHERE meta_key = 'cs_application_closing_date' and meta_value > $current_date and post_id IN ( SELECT ID FROM wp_posts WHERE (post_title LIKE '%$keyword%' OR post_content LIKE '%$keyword%') AND post_type = 'jobs'))");
    }else{
        $totalRows = $wpdb->get_results("SELECT * from wp_posts where id in (SELECT post_id  FROM wp_postmeta WHERE meta_key = 'cs_application_closing_date' and meta_value > $current_date and post_id IN ( SELECT ID FROM wp_posts WHERE post_type = 'jobs'))");;
    }
    if ( empty($totalRows)) {
        return new WP_Error( 'no_result_found', __('No search appear for the job.'), array( 'status' => 200 ) );
    }
    if ($page == '') {
        $page = 1;
    }
    if ($posts_per_page == '') {
        $posts_per_page = 50;
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);

    $total_records      = count($totalRows);
    $total_pages        = ceil($total_records / $posts_per_page);
    if ( $current_page_record > $total_records) {
        $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
        for($i = $offset; $i < $current_page_record; $i++) {
            $ID  = $totalRows[$i]->ID;
            $user_meta                    = get_userdata($totalRows[$i]->post_author);
            $user_role                    = $user_meta->roles;
            $user_name                    = $user_meta->user_login;
            $user_email                   = $user_meta->user_email;
            
            $application_closing_date     = get_post_meta($ID,'cs_application_closing_date');
            $experien                     = get_post_meta($ID , 'experiencetotal');
            $job_status                   = get_post_meta($ID , 'cs_job_status');
            $job_type                     = wp_get_post_terms( $ID, 'job_type' );
            $specialisms                  = wp_get_post_terms( $ID, 'specialisms' );
            $closing_date                 = $application_closing_date[0];
            $current_date                 = strtotime(current_time('d-m-Y H:i:s'));
            
            if ( $want_expiry_jobs == "yes") {
                $check_date = true;
            }else{
                $check_date   = $closing_date > $current_date;
            }
            if( $check_date ){
                foreach ($specialisms as $k=>$spec) {
                $specification[$k] = $spec->name;
                }
                $user_img = get_usermeta( $totalRows[$i]->post_author, $meta_key = 'user_img' );
                if (!$user_img) {
                    $user_img =home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
                }else{
                    $user_img =$imge_path.get_usermeta( $totalRows[$i]->post_author, $meta_key = 'user_img' );
                }
                //$post_data[$key]->test        = $user_meta;
                $post_data[$i]->post_id     = $ID;
                $post_data[$i]->post_title  = $totalRows[$i]->post_title;
                $post_data[$i]->author      = $totalRows[$i]->post_author;
                $post_data[$i]->content     = strip_shortcodes($totalRows[$i]->post_content);
                $post_data[$i]->user_name   = $user_name;
                $post_data[$i]->user_email  = $user_email;
                $post_data[$i]->post_date   = $totalRows[$i]->post_date;
                $post_data[$i]->end_date    = date('d-m-Y', $application_closing_date[0]);
                $post_data[$i]->user_experience  =  $experien[0];        
                $post_data[$i]->specialisms = $specification; 
                $post_data[$i]->job_type    = $job_type[0]->name;
                $post_data[$i]->job_modified= $totalRows[$i]->post_modified; 
                $post_data[$i]->job_posted_date = $totalRows[$i]->post_date;
                $post_data[$i]->job_status  = $job_status[0];
                $post_data[$i]->job_image   = $user_img;
                $post_data[$i]->check_end_data   = $check_end_data;


                //jobs based on user
                $all_applicants = array();
                $all_shortlist  = array();
                $applicants = count_usermeta('cs-user-jobs-applied-list', serialize(strval($totalRows[$i]->ID)), 'LIKE', true);
                $shortlist  = count_usermeta('cs-user-jobs-wishlist', serialize(strval($totalRows[$i]->ID)), 'LIKE', true);
                foreach ($applicants as $ky => $applicant) {
                    $all_applicants[$ky] = $applicant->data->ID;
                }
                foreach ($shortlist as $ky => $shorted) {
                    $all_shortlist[$ky] = $shorted->data->ID;
                }
                // if(empty($all_applicants)){
                //     $all_applicants = false;
                // }
                
                $count_applicants    = count($all_applicants);
                if ( isset($author_id) || !empty($author_id)) {
                    // if ($count_applicants > 0){
                        foreach ($all_applicants as $k => $applicant_id) {
                            $applicant_user   = get_user_by( 'id', $applicant_id );
                            $user_image       = get_user_meta ($applicant_id,'user_img');
                            $job_applied_date = cs_find_other_field_user_meta_list($totalRows[$i]->ID, 'post_id', 'cs-user-jobs-applied-list', 'date_time', $applicant_id);
                            $posted_date = date('j F, Y', $job_applied_date);
                            if( $job_applied_date == false){
                                $posted_date = '';
                            }
                            if($user_image != ''){ 
                                $application_img = $imge_path.$user_image[0]; 
                            }else{ 
                                $application_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
                            }
                            $cs_candidate_cv_check = get_user_meta($applicant_id, 'cs_candidate_cv_' . $totalRows[$i]->ID . ' ', true);
                            if ($cs_candidate_cv_check != '') {
                            $cs_candidate_cv = get_user_meta($applicant_id, 'cs_candidate_cv_' . $totalRows[$i]->ID . ' ', true);
                            } else {
                            $cs_candidate_cv = get_user_meta($applicant_id, "cs_candidate_cv", true);
                            }

                            $cs_updated_cover_letter_check = get_user_meta($applicant_id, 'cs_updated_cover_letter_' . $totalRows[$i]->ID . ' ', true);
                            if ($cs_updated_cover_letter_check != '') {
                            $cs_updated_cover_letter= get_user_meta($applicant_id, 'cs_updated_cover_letter_' . $totalRows[$i]->ID . ' ', true);
                            } else {
                            $cs_updated_cover_letter = get_user_meta($applicant_id, "cs_updated_cover_letter_", true);
                            }

                            $post_data[$i]->applicats_data[$k]->ID     = $applicant_id;
                            $post_data[$i]->applicats_data[$k]->applicant_name   = $applicant_user->display_name;
                            $post_data[$i]->applicats_data[$k]->job_applied_date = $posted_date;
                            $post_data[$i]->applicats_data[$k]->applicant_image  = $application_img;
                            $post_data[$i]->applicats_data[$k]->applicant_cv     = $cs_candidate_cv;
                            $post_data[$i]->applicats_data[$k]->applicant_cover_letter = $cs_updated_cover_letter;

                        }
                    // }else{
                    //     $post_data[$key]->applicats_data = $all_applicants;
                    // }
                    
                }
                $post_data[$i]->job_applied = $all_applicants;
                $post_data[$i]->job_shortlist = $all_shortlist;
                $post_data[$i]->real_check = ($closing_date > $current_date);

            }// eo expire job condition
        }
    }else{
        return new WP_Error( 'no_job_found', __('No job found'), array( 'status' => 200 ) );
    }
    $post_data = array_values($post_data);
    $response = new WP_REST_Response(array('message'=> 'all jobs','data'=>array('status'=> 200,'params'=>$post_data) ));
    return $response;
}
//eo
// jobs function 
function jobs($request){
    global $wpdb , $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $posts_per_page = $request['per_page'];
    $page           = $request['pages'];
    $author_id      = $_POST['author_id'];
    $want_expiry_jobs = $_POST['want_expiry_jobs'];
    $filter_by_date = $_POST['filter_by_date'];
    $filter_taxonomy= $_POST['filter_taxonomy']; 
    $filter_spec    = $_POST['filter_specialisms'];
    $filter_jt      = $_POST['filter_job_type'];
    $filter_job_status = $_POST['filter_job_status'];
    //$keyword        = $_POST['keyword'];
    $array_values   = array();
    $arguments      = array();

    $count = 0; 


    if($post_per_page == ''){
       $post_per_page = -1; 
    }
    if ( isset($filter_by_date) && !empty($filter_by_date)) {
        $array_values['date_query'] = array(
            array(
                'after' => $filter_by_date,//'1 hour ago',
                // 'before'    => '2019-05-1','inclusive' => true,
                ),
                // limit to posts before 17:10 (not tested)
                // array('hour'      => 12,  'minute'    => 23, 'compare'   => '<=', ),
                //  // limit to posts after 08:30
                // array(
                //     'hour'      => 1, 'minute'    => 00,'compare'   => '>=', ),
            );
    }
    if ( isset($filter_job_status) && !empty($filter_job_status)) {
        $array_values['meta_query'] = array(
            array(
              'key'     => 'cs_job_status',
              'value'   => $filter_job_status,
            )
        );
    }
    if( isset($filter_spec) && !empty($filter_spec)){
            $arguments[$count] = array(
            'taxonomy' => 'specialisms',
            'field' => 'name',
            'terms' => $filter_spec );
            $count++;
    }
    if( isset($filter_jt) && !empty($filter_jt)){
            $arguments[$count] = array(
            'taxonomy' => 'job_type',
            'field' => 'name',
            'terms' => $filter_jt );
            $count++;
    }
    $argument_count = count($arguments);
    if ( isset($filter_taxonomy) && $filter_taxonomy == 'true') {

        if ($argument_count > 1) {
            $arguments[$count] = array('relation' => 'OR');
        }
        $array_values['tax_query'] = $arguments;
    }
    
 
    $array_values['posts_per_page'] = $posts_per_page;
    $array_values['paged']          = $page;
    $array_values['orderby']        = 'date';
    $array_values['order']          = 'desc';
    $array_values['post_type']      = 'jobs';
    if ( isset($author_id) || !empty($author_id)) {
        $array_values['author'] = $author_id;
    }

    $query = new WP_Query( $array_values ); 
    $post_data = array();

    // if no posts found return 
    if( empty($query->posts) ){
        return new WP_Error( 'no_posts', __('No post found'), array( 'status' => 200 ) );
    }
    $max_pages       = $query->max_num_pages;
    $total           = $query->found_posts;
    $posts           = $query->posts;
    $count_posts     = wp_count_posts();
    $published_posts = $count_posts->publish;
    $specification   = array();
    $meta            = array();

    foreach ( $posts as $key=>$post ) {
        //return get_post_meta($post->ID);
        $user_meta                    = get_userdata($post->post_author);
        $user_role                    = $user_meta->roles;
        $user_name                    = $user_meta->user_login;
        $user_email                   = $user_meta->user_email;
        
        $application_closing_date     = get_post_meta($post->ID,'cs_application_closing_date');
        $experien                     = get_post_meta($post->ID , 'experiencetotal');
        $job_status                   = get_post_meta($post->ID , 'cs_job_status');
        $job_type                     = wp_get_post_terms( $post->ID, 'job_type' );
        $specialisms                  = wp_get_post_terms( $post->ID, 'specialisms' );
        $closing_date                 = $application_closing_date[0];
        $current_date                 = strtotime(current_time('d-m-Y H:i:s'));
        
        if ( $want_expiry_jobs == "yes") {
            $check_date = true;
        }else{
            $check_date   = $closing_date > $current_date;
        }
        if( $check_date ){
            foreach ($specialisms as $k=>$spec) {
            $specification[$k] = $spec->name;
            }
            $user_img = get_usermeta( $post->post_author, $meta_key = 'user_img' );
            if (!$user_img) {
                $user_img =home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
            }else{
                $user_img =$imge_path.get_usermeta( $post->post_author, $meta_key = 'user_img' );
            }
            //$post_data[$key]->test        = $user_meta;
            $post_data[$key]->post_id     = $post->ID;
            $post_data[$key]->post_title  = $post->post_title;
            $post_data[$key]->author      = $post->post_author;
            $post_data[$key]->content     = strip_shortcodes($post->post_content);
            $post_data[$key]->user_name   = $user_name;
            $post_data[$key]->user_email  = $user_email;
            $post_data[$key]->post_date   = $post->post_date;
            $post_data[$key]->end_date    = date('d-m-Y', $application_closing_date[0]);
            $post_data[$key]->user_experience  =  $experien[0];        
            $post_data[$key]->specialisms = $specification; 
            $post_data[$key]->job_type    = $job_type[0]->name;
            $post_data[$key]->job_modified= $post->post_modified; 
            $post_data[$key]->job_posted_date = $post->post_date;
            $post_data[$key]->job_status  = $job_status[0];
            $post_data[$key]->job_image   = $user_img;
            $post_data[$key]->check_end_data   = $check_end_data;


            //jobs based on user
            $all_applicants = array();
            $all_shortlist  = array();
            $applicants = count_usermeta('cs-user-jobs-applied-list', serialize(strval($post->ID)), 'LIKE', true);
            $shortlist  = count_usermeta('cs-user-jobs-wishlist', serialize(strval($post->ID)), 'LIKE', true);
            foreach ($applicants as $ky => $applicant) {
                $all_applicants[$ky] = $applicant->data->ID;
            }
            foreach ($shortlist as $ky => $shorted) {
                $all_shortlist[$ky] = $shorted->data->ID;
            }
            // if(empty($all_applicants)){
            //     $all_applicants = false;
            // }
            
            $count_applicants    = count($all_applicants);
            if ( isset($author_id) || !empty($author_id)) {
                // if ($count_applicants > 0){
                    foreach ($all_applicants as $k => $applicant_id) {
                        $applicant_user   = get_user_by( 'id', $applicant_id );
                        $user_image       = get_user_meta ($applicant_id,'user_img');
                        $job_applied_date = cs_find_other_field_user_meta_list($post->ID, 'post_id', 'cs-user-jobs-applied-list', 'date_time', $applicant_id);
                        $posted_date = date('j F, Y', $job_applied_date);
                        if( $job_applied_date == false){
                            $posted_date = '';
                        }
                        if($user_image != ''){ 
                            $application_img = $imge_path.$user_image[0]; 
                        }else{ 
                            $application_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
                        }
                        $cs_candidate_cv_check = get_user_meta($applicant_id, 'cs_candidate_cv_' . $post->ID . ' ', true);
                        if ($cs_candidate_cv_check != '') {
                        $cs_candidate_cv = get_user_meta($applicant_id, 'cs_candidate_cv_' . $post->ID . ' ', true);
                        } else {
                        $cs_candidate_cv = get_user_meta($applicant_id, "cs_candidate_cv", true);
                        }

                        $cs_updated_cover_letter_check = get_user_meta($applicant_id, 'cs_updated_cover_letter_' . $post->ID . ' ', true);
                        if ($cs_updated_cover_letter_check != '') {
                        $cs_updated_cover_letter= get_user_meta($applicant_id, 'cs_updated_cover_letter_' . $post->ID . ' ', true);
                        } else {
                        $cs_updated_cover_letter = get_user_meta($applicant_id, "cs_updated_cover_letter_", true);
                        }

                        $post_data[$key]->applicats_data[$k]->ID     = $applicant_id;
                        $post_data[$key]->applicats_data[$k]->applicant_name   = $applicant_user->display_name;
                        $post_data[$key]->applicats_data[$k]->job_applied_date = $posted_date;
                        $post_data[$key]->applicats_data[$k]->applicant_image  = $application_img;
                        $post_data[$key]->applicats_data[$k]->applicant_cv     = $cs_candidate_cv;
                        $post_data[$key]->applicats_data[$k]->applicant_cover_letter = $cs_updated_cover_letter;

                    }
                // }else{
                //     $post_data[$key]->applicats_data = $all_applicants;
                // }
                
            }
            $post_data[$key]->job_applied = $all_applicants;
            $post_data[$key]->job_shortlist = $all_shortlist;
            $post_data[$key]->real_check = ($closing_date > $current_date);

        }// eo expire job condition
        //return date('d-m-Y', strtotime($application_closing_date[0]));
    }   
    $post_data = array_values($post_data);
    $response = new WP_REST_Response(array('message'=> 'all jobs','data'=>array('status'=> 200,'params'=>$post_data) ));
    $response->header( 'X-WP-Total', $total ); 
    $response->header( 'X-WP-TotalPages', $max_pages );

    return $response;
        
}

function post_job(){
   
    // $terms = get_terms([
    // 'taxonomy' => 'job_type'
    // ]);
    // return $terms;
   $job_author      = $_POST['user_id'];
   $user_id         = $job_author;
   $user            = get_userdata( $user_id );
   $user_name       = $user->data->display_name;
   $p_title         = $_POST['post_title'];
   $post_content    = $_POST['content'];
   $job_type        = $_POST['job_type'];
   $job_type        = explode( "," , $job_type );
   $post_spec       = $_POST['specialisms'];
   $terms_spec      = explode( "," , $post_spec );
   $user_experience = $_POST['user_experience'];
   $application_end = $_POST['application_end_date'];
   $post_expiry     = date('Y-m-d H:i:s', strtotime('+5 year'));
   $post_cuurent    = current_time('Y-m-d H:i:s',1);
   $friends         = get_user_meta( $user_id, 'friends', true );

   
    // $term = get_term( $_POST['term_id'] );
    // return $term->name;

   if ( !isset($job_author) || empty($job_author) ) {
       return new WP_Error( 'authorized user', __('enter a authorized user'), array( 'status' => 400 ) );
   }
   if ( !isset($p_title) || empty($p_title) ) {
       return new WP_Error( 'no_title', __('No title found'), array( 'status' => 400 ) );
   }
   if (!isset($terms_spec) || empty($terms_spec)) {
      return new WP_Error( 'no_specialisms', __('No specifications found'), array( 'status' => 400 ) );
   }
   if (!isset($user_experience) || empty($user_experience)) {
      return new WP_Error( 'no_experience', __('No experience found'), array( 'status' => 400 ) );
   }

   $job_data = array(
          'post_author'     => $job_author, 
          'post_title'      => $p_title,
          'post_content'    => $post_content, 
          'post_status'     => 'publish',
          'post_type'       => 'jobs'
        );

    $post_id = wp_insert_post($job_data);

    if( !is_wp_error($post_id) ) {

        $data = array();
        $resposne = array();
        $user_meta              = get_userdata($job_author);
        $user_name              = $user_meta->user_login;
        $user_email             = $user_meta->user_email;
        $resposne['post_id']    = $post_id;
        $resposne['post_title'] = get_the_title($post_id);
        $resposne['author']     = $job_author;
        $resposne['content']    = apply_filters('the_content', get_post_field('post_content', $post_id));
        $resposne['user_name']  = $user_name;
        $resposne['user_email'] = $user_email;

        add_post_meta( $post_id, 'cs_job_username', $job_author, true );
        $data['cs_job_username'] = $job_author;

        add_post_meta( $post_id, 'cs_job_posted', strtotime( $post_cuurent ), true );
        $data['cs_job_posted'] = strtotime( $post_cuurent );

        add_post_meta( $post_id, 'cs_job_expired', strtotime( $post_expiry ), true );
        $data['cs_job_expired'] = strtotime( $post_expiry );

        add_post_meta( $post_id , 'cs_application_closing_date', strtotime( $application_end ), true  );
        $resposne['user_email'] = $application_end;

        add_post_meta( $post_id, 'cs_job_status', 'awaiting-activation');
        $data['cs_job_status'] = 'awaiting-activation';

        update_post_meta( $post_id, 'cs_array_data', $data );

        $experien = add_post_meta( $post_id , 'experiencetotal', $user_experience  );
        $job_exp = get_post_meta($post_id , 'experiencetotal');
        $resposne['user_experience'] = $job_exp[0];

        $data['specialisms'] = wp_set_object_terms( $post_id, $terms_spec, 'specialisms', FALSE);
        foreach ($data['specialisms'] as $key => $spec) {
            $term = get_term( $spec );
            $resposne['specialisms'][$key] = $term->name; 
        }
        
        $data['job_type']    = wp_set_object_terms( $post_id, $job_type , 'job_type', FALSE );
        foreach ($data['job_type'] as $j_type) {
            $type = get_term( $j_type );
            $type_value = $type->name;
            $resposne['job_type'] = $type_value; 
        }

        $job_status             = get_post_meta($post_id , 'cs_job_status');
        $resposne['job_status'] = $job_status[0];

        $addNotifierId  = $post_id;
        $action_parent  = '';
        $action_type  = 'job';
        $message        = $user_name.' posted a job.';
        foreach ($friends as $key => $value) {
            $addNotificationIn  = $value;
            $notificationData   =  notification_data( $action_type,$addNotifierId );
            $tokens             = get_user_meta($user_id, 'device_token',true);
            if ( !empty($tokens)) {
                foreach ($tokens as $key => $token) {
                    iospushnotification($token,$message,$action_type,$notificationData);
                }
            }
            send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id );
        }
        
        $resp    = new WP_REST_Response(array('message'=> 'Successfully! Job posted','data'=>array('status'=> 201, 'params' => $resposne ) ));
        return $resp;

    }else{
        return 'error';
    }
}

function update_job(){

    $post_id        = $_POST['post_id'];
    $post_title     = $_POST['post_title'];
    $post_content   = $_POST['content'];
    $job_ty         = $_POST['job_type'];
    $job_type       = $_POST['job_type'];
    $job_type       = explode( "," , $job_type );
    $post_spec      = $_POST['specialisms'];
    $terms_spec     = explode( "," , $post_spec );
    $experien       = $_POST['user_experience'];
    $end_date       = $_POST['application_end_date'];
    $job_status     = $_POST['job_status'];
    $job_upadte     =  array();

    
    if ( !isset($post_id) || empty($post_id) ) {
       return new WP_Error( 'no_post_id', __('No post id found'), array( 'status' => 400 ) );
    }

    $update = array();
    $update['ID']     = $post_id;
    $job_upadte['ID'] = $post_id;
    if ( isset($post_title) || !empty($post_title) ) {
        $update['post_title'] = $post_title;
        $job_upadte['post_title'] = true;
    }
    if ( isset($post_content) || !empty($post_content) ) {
        $update['post_content'] = $post_content;
        $job_upadte['post_content'] = true;
    }
    $update_count  = count($update);
    if($update_count > 1) {
        $update_status = wp_update_post($update,true);
        if ( !is_wp_error($update_status)) {
            if ($job_upadte['post_title'] == true) {
                $job_upadte['post_title'] = get_the_title($post_id);
            }
            if ($job_upadte['post_content'] == true) {
                $job_upadte['post_content'] = apply_filters('the_content', get_post_field('post_content', $post_id));
            }
        }else{
            return new WP_Error( 'invalid_post_id', __('Invalid post id'), array( 'status' => 400 ) );
        }
    }

    if ( isset($end_date) || !empty($end_date) ) {
        $job_upadte['application_closing_date'] = update_post_meta( $post_id, 'cs_application_closing_date', strtotime( $end_date ) );
    }
    if ( isset($experien) ) {
        $job_upadte['experience'] = update_post_meta( $post_id , 'experiencetotal', $experien);
    }
    if ( isset($post_spec) || !empty( $post_spec ) ) {
        $job_upadte['specialisms'] = wp_set_object_terms( $post_id, $terms_spec, 'specialisms', FALSE);
        foreach ($job_upadte['specialisms'] as $key => $spec) {
            $term = get_term( $spec );
            $job_upadte['specialisms'][$key] = $term->name; 
        }
    }
    if ( isset($job_ty) || !empty( $job_ty ) ) {
        $job_upadte['job_type']    = wp_set_object_terms( $post_id, $job_type , 'job_type', FALSE );
        foreach ($job_upadte['job_type'] as $j_type) {
            $type = get_term( $j_type );
            $type_value = $type->name;
            $job_upadte['job_type'] = $type_value; 
        }
    }
    if ( isset($job_status) || !empty($job_status) ) {
        //return 'in this';
        $update_job_status = update_post_meta($post_id , 'cs_job_status', $job_status);
        if ( $update_job_status == true) {
            $job_upadte['job_status'] = $job_status;
        }
    }
    
    $count = count($job_upadte);
    //return ( !empty($job_upadte) && $count > 1);
    if ( !empty($job_upadte) && $count > 1) {
        $content_post = get_post($post_id);
        $job_upadte['post_last_modification_date'] = $content_post->post_modified;
        
        $response = new WP_REST_Response(array('message'=> 'job successfully! updated','data'=>array('status'=> 200,'params'=>$job_upadte) ));
        return $response;
    }else{
        $response = new WP_REST_Response(array('message'=> 'No update found','data'=>array('status'=> 200) ));
        return $response;
    }
    


}

function delete_job(){

    $job_id = $_POST['job_id'];
    $post_exists    = get_post_status($job_id);

    if ( !isset($job_id) || empty($job_id) ) {
       return new WP_Error( 'no_post_id', __('No post id found'), array( 'status' => 400 ) );
    }
    if ( $post_exists == false) {
        return new WP_Error( 'not_exists', __('Post does not exists', array('status' => 406 )) );
    }
    $status = wp_delete_post($job_id);
    if($status)
    {
        $resp = new WP_REST_Response(array('message'=> 'Successfully! Job Deleted','data'=>array('status'=> 200) ));
        return $resp;
    }
    else {
        return new WP_Error( 'failed', __('failed to delete', array('status' => 406 )) );
    }

}

function applied_jobs($request){
    global $plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';
    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    if ($page == '') {
        $page = 1;
    }
    if ($posts_per_page == '') {
        $posts_per_page = 50;
    }
    $job_id             = $_POST['job_id'];
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);
    $all_applicants     = array();
    $applicants_data    = array();
    
    if ( !isset($job_id) || empty($job_id)) {
       return new WP_Error('job_id_no_found', __('job_id_not_found', array('status' => 400 )));
    }
    $applicants = count_usermeta('cs-user-jobs-applied-list', serialize(strval($job_id)), 'LIKE', true);
    $total_records = count($applicants);
    $total_pages        = ceil($total_records / $posts_per_page);
    if ( $current_page_record > $total_records) {
        $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
        //start loop
        for($i = $offset; $i < $current_page_record; $i++) {    
            $applicant_id     = $applicants[$i]->data->ID;
            $user             = get_user_by( 'id', $applicant_id );
            $user_image       = get_user_meta ($applicant_id,'user_img');

            if($user_image != ''){ $cs_jobs_thumb_url = $imge_path.$user_image[0]; }else{ $cs_jobs_thumb_url = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';}
            $job_applied_date = cs_find_other_field_user_meta_list($job_id, 'post_id', 'cs-user-jobs-applied-list', 'date_time', $applicant_id);
            $value = date('j F, Y', $job_applied_date);
            if( $job_applied_date == false){
                $value = '';
            }
            $cs_candidate_cv_check = get_user_meta($applicant_id, 'cs_candidate_cv_' . $job_id . ' ', true);
            if ($cs_candidate_cv_check != '') {
            $cs_candidate_cv = get_user_meta($applicant_id, 'cs_candidate_cv_' . $job_id . ' ', true);
            } else {
            $cs_candidate_cv = get_user_meta($applicant_id, "cs_candidate_cv", true);
            }

            $cs_updated_cover_letter_check = get_user_meta($applicant_id, 'cs_updated_cover_letter_' . $job_id . ' ', true);
            if ($cs_updated_cover_letter_check != '') {
            $cs_updated_cover_letter= get_user_meta($applicant_id, 'cs_updated_cover_letter_' . $job_id . ' ', true);
            } else {
            $cs_updated_cover_letter = get_user_meta($applicant_id, "cs_updated_cover_letter_", true);
            }

            $applicants_data[$i]->ID                  = $applicants[$i]->data->ID;
            $applicants_data[$i]->applicant_name      = $user->display_name;
            $applicants_data[$i]->job_applied_date    = $value;
            $applicants_data[$i]->applicant_image     = $cs_jobs_thumb_url;
            $applicants_data[$i]->applicant_cv        = $cs_candidate_cv;
            $applicants_data[$i]->applicant_cover_letter = $cs_updated_cover_letter;
        } 
        
    }else{
        return new WP_Error( 'No_post_found', __('No data found'), array( 'status' => 200 ) );
    }
    //eo loop
    $response = new WP_REST_Response(array('message' => 'Applicant', 'data' => array('status' => 200,'param'=>$applicants_data)));
        return $response;
}
function  apply_for_job(){
    global $wpdb;
    $job_id             = $_POST['job_id'];
    $user_id            = $_POST['user_id'];
    $apply_type         = $_POST['apply_type'];
    $closing_date       = get_post_meta($job_id ,'cs_application_closing_date',true);
    $current_date       = strtotime(current_time('d-m-Y H:i:s'));
    $all_apply_type     = array('apply_job', 'shortlist_job');
    $user               = get_user_by('id' , $user_id);
    $user_name          = $user->data->display_name;
    $user_role          = $user->roles; 
    $applied_users      = array();
    $existJob           =  $wpdb->get_results("SELECT * FROM wp_posts WHERE ID = $job_id and post_type = 'jobs'");
    $authorId           = get_post_field( 'post_author', $job_id );
    $addNotificationIn  = $authorId;
    $addNotifierId      = $job_id;
    $action_type        = 'job';

    if( !isset($job_id) || empty($job_id)) {
        return new WP_Error( 'Job_id', __('No job id found'), array( 'status' => 400 ) );
    }
    if ( empty($existJob)) {
       return new WP_Error( 'invalid_job_id', __('Job id is not valid'), array( 'status' => 400 ) );
    }
    if( !isset($user_id) || empty($user_id)) {
        return new WP_Error( 'User_id', __('No user id found'), array( 'status' => 400 ) );
    }
    if ( !isset($apply_type) || empty($apply_type) ) {
        return new WP_Error( 'apply_type_missing', __('Apply type not found'), array( 'status' => 400 ) );
    }
    if( !in_array($apply_type, $all_apply_type)){
        return new WP_Error( 'Invalid_apply_type', __('Enter a valid apply type'), array( 'status' => 400 ) );
    }

    if ( $user_role[0] == 'cs_candidate' ) {
        if ($closing_date > $current_date){
            if( $apply_type == 'apply_job' ){
                if ( (isset($_POST['job_id']) && $_POST['job_id'] <> '' ) ) {
                    $job_expired = cs_check_expire_job($_POST['job_id']);
                    if ( $job_expired ) {
                        return new WP_Error( 'Expire_job', __("You can't apply because this job has been expired"), array( 'status' => 200 ) );
                    }
                }

                $check_applied_users            = count_usermeta('cs-user-jobs-applied-list', serialize(strval($job_id)), 'LIKE', true);
                $deadline                       = get_post_meta($job_id , 'cs_application_closing_date');
                $cs_candidate_cv_check         = get_user_meta($user_id, 'cs_candidate_cv', true);
                $cs_updated_cover_letter_check = get_user_meta($user_id, 'cs_updated_cover_letter', true);

                if ($cs_candidate_cv_check != '') {
                    $cs_candidate_cv = $cs_candidate_cv_check;
                } else {
                    return new WP_Error( 'Resume_not_found', __('Before apply please update your resume in your profile.'), array( 'status' => 200 ) ); 
                }
                if ($cs_updated_cover_letter_check != '') {
                    $cs_updated_cover_letter= $cs_updated_cover_letter_check;
                }else {
                    $cs_updated_cover_letter = '';
                }

                $job_cv             = 'cs_candidate_cv_' . $job_id . ' ';
                $job_cover_letter   = 'cs_updated_cover_letter_' . $job_id . ' ';
                $jobs_applied       = get_user_meta($user_id, 'cs-jobs-applied', true);
                $jobs_applied_list  = get_user_meta($user_id, 'cs-user-jobs-applied-list', true);

                //return $check_applied_users;
                if (!empty($check_applied_users)) {
                    foreach ($check_applied_users as $ky => $applied_user) {
                        $applied_users[$ky] = $applied_user->data->ID;
                    }
                    $message = $user_name.' applied for your posted job.';
                    $action_parent      = '';
                    $notificationData   =  notification_data( $action_type,$addNotifierId );
                    $tokens             = get_user_meta($user_id, 'device_token',true);
                    if ( !empty($tokens)) {
                        foreach ($tokens as $key => $token) {
                            iospushnotification($token,$message,$action_type,$notificationData);
                        }
                    }
                    send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id );
                    if (in_array($user_id, $applied_users)){
                        return new WP_Error( 'Already_applied', __('Current user is alredy applied for this job'), array( 'status' => 200 ) );        
                    }
                    //$applied_job_id         = array();
                    //$applied_job_data       = array();
                    $applied_id_arr = get_user_meta($user_id , 'cs-jobs-applied');
                    $listed_arr     = get_user_meta($user_id , 'cs-user-jobs-applied-list');
                    $id_key                 = count($applied_id_arr[0]);
                    $listed_key             = count($listed_arr[0]);
                    
                    $applied_id_arr[0][$id_key]              = $job_id;
                    $listed_arr[0][$listed_key]['post_id']   = $job_id;
                    $listed_arr[0][$listed_key]['date_time'] = strtotime(current_time('d-m-Y H:i:s'));
                    // echo "<pre>";
                    //  print_r($applied_id_arr[0]);
                    //  return $applied_id_arr;
                    update_user_meta($user_id , 'cs-jobs-applied' , $applied_id_arr[0] );
                    add_user_meta($user_id , $job_cv , $cs_candidate_cv_check );
                    add_user_meta($user_id , $job_cover_letter , $cs_updated_cover_letter );
                    update_user_meta($user_id , 'cs-user-jobs-applied-list' ,$listed_arr[0] );

                    $message = $user_name.' applied for your posted job.';
                    $action_parent      = '';
                    $notificationData   =  notification_data( $action_type,$addNotifierId );
                    $tokens             = get_user_meta($user_id, 'device_token',true);
                    if ( !empty($tokens)) {
                        foreach ($tokens as $key => $token) {
                            iospushnotification($token,$message,$action_type,$notificationData);
                        }
                    }
                    send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id );
                    $response = new WP_REST_Response(array('message' => 'Successfully Applied for the job', 'data' => array('status' => 200)));
                    return $response;
                    
                }
                if ( empty($check_applied_users)) {
                    $applied_job_id = array();
                    $applied_job_data = array();
                    $applied_job_id[0] = $job_id;
                    $applied_job_data[0]['post_id']   = $job_id;
                    $applied_job_data[0]['date_time'] = strtotime(current_time('d-m-Y H:i:s'));
                    //return $applied_job_data;
                    add_user_meta($user_id , 'cs-jobs-applied' , $applied_job_id );
                    add_user_meta($user_id , $job_cv , $cs_candidate_cv_check );
                    add_user_meta($user_id , $job_cover_letter , $cs_updated_cover_letter );
                    add_user_meta($user_id , 'cs-user-jobs-applied-list' ,$applied_job_data );
                    $message = $user_name.' applied for your posted job.';
                    $action_parent      = '';
                    $notificationData   =  notification_data( $action_type,$addNotifierId );
                    $tokens             = get_user_meta($user_id, 'device_token',true);
                    if ( !empty($tokens)) {
                        foreach ($tokens as $key => $token) {
                            iospushnotification($token,$message,$action_type,$notificationData);
                        }
                    }
                    send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id );
                    $response = new WP_REST_Response(array('message' => 'Successfully Applied for the job', 'data' => array('status' => 200)));
                    return $response;
                }
            }elseif ( $apply_type == 'shortlist_job' ) {
                $check_shortlisted_jobs  = get_user_meta($user_id , 'cs-user-jobs-wishlist',true);
                //return $check_shortlisted_jobs;
                // $check_shortlisted_jobs  = count_usermeta('cs-user-jobs-wishlist', serialize(strval($job_id)), 'LIKE', true);
                // return $check_shortlisted_jobs;
                $existWishlistMeta = $wpdb->get_results("SELECT * FROM wp_usermeta WHERE user_id = $user_id and meta_key = 'cs-user-jobs-wishlist'");
                if (empty($existWishlistMeta)) {
                    $shortlisted_job_data = array();
                    $shortlisted_job_data[0]['post_id']   = $job_id;
                    $shortlisted_job_data[0]['date_time'] = strtotime(current_time('d-m-Y H:i:s'));
                    $result = add_user_meta($user_id , 'cs-user-jobs-wishlist',$shortlisted_job_data);
                    if ($result == true) {
                        $message = $user_name.' shortlisted your posted job.';
                        $action_parent      = '';
                        $notificationData   =  notification_data( $action_type,$addNotifierId );
                        $tokens             = get_user_meta($user_id, 'device_token',true);
                        if ( !empty($tokens)) {
                            foreach ($tokens as $key => $token) {
                                iospushnotification($token,$message,$action_type,$notificationData);
                            }
                        }
                        send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id );
                        $response = new WP_REST_Response(array('message' => 'Job successfully shortlisted!', 'data' => array('status' => 200)));
                        return $response;
                    }else{
                        return new WP_Error( 'fail_to_select', __('Failed!'), array( 'status' => 412 ) );
                    }
                }else{
                    $shortlisted_arr     = get_user_meta($user_id , 'cs-user-jobs-wishlist',true);
                    $shortlisted_ids  = array();
                    //print_r($shortlisted_arr);
                    foreach ($shortlisted_arr as $key => $shortlisted_a) {
                        $shortlisted_ids[$key] = $shortlisted_a['post_id'];
                    }
                   // return ( in_array($job_id, $shortlisted_ids ));
                    if ( in_array($job_id, $shortlisted_ids )) {

                        cs_remove_from_user_meta_list($job_id, 'cs-user-jobs-wishlist', $user_id);
                        if ($result == null) {
                            $response = new WP_REST_Response(array('message' => 'Job successfully unlist!', 'data' => array('status' => 200)));
                            return $response;
                        }else{
                            return new WP_Error( 'fail_to_select', __('Failed!'), array( 'status' => 412 ) );
                        }
                    }else{
                        $shortlisted_key     = count($shortlisted_arr);
                        $shortlisted_arr[$shortlisted_key]['post_id']   = $job_id;
                        $shortlisted_arr[$shortlisted_key]['date_time'] = strtotime(current_time('d-m-Y H:i:s'));
                        $result = update_user_meta($user_id , 'cs-user-jobs-wishlist' ,$shortlisted_arr);
                        if ($result == true) {
                            $message = $user_name.' shortlisted your posted job.';
                            $action_parent      = '';
                            $notificationData   =  notification_data( $action_type,$addNotifierId );
                            $tokens             = get_user_meta($addNotifierId, 'device_token',true);
                            if ( !empty($tokens)) {
                                foreach ($tokens as $key => $token) {
                                    iospushnotification($token,$message,$action_type,$notificationData);
                                }
                            }
                            send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id );
                            $response = new WP_REST_Response(array('message' => 'Job successfully shortlisted!', 'data' => array('status' => 200)));
                            return $response;
                        }else{
                            return new WP_Error( 'fail_to_select', __('Failed!'), array( 'status' => 412 ) );
                        }
                    }
                    
                    
                }
            }
        }else{
            if ($apply_type == 'shortlist_job') {
                $check_shortlist_jobs  = get_user_meta($user_id ,'cs-user-jobs-wishlist',true);
                $applicant_post_ids = array_column_by_two_dimensional($check_shortlist_jobs, 'post_id');                
                if (empty($check_shortlist_jobs)) {
                   return new WP_Error( 'expired_post', __('This job is expired'), array( 'status' => 200 ) ); 
                }
                if ( in_array($job_id, $applicant_post_ids)) {
                    cs_remove_from_user_meta_list($job_id, 'cs-user-jobs-wishlist', $user_id);
                    if ($result == null) {
                        $response = new WP_REST_Response(array('message' => 'Job successfully unlist!', 'data' => array('status' => 200)));
                        return $response;
                    }else{
                        return new WP_Error( 'fail_to_select', __('Failed!'), array( 'status' => 412 ) );
                    }
                }else{
                    return new WP_Error( 'expired_post', __('This job is expired'), array( 'status' => 200 ) );
                }
               return $applicant_shortlisted_post_ids;
            }
            //return 'here in else';
            return new WP_Error( 'expired_post', __('This job is expired'), array( 'status' => 200 ) );
        }
        
    }else{
        //return 'here in else';
        return new WP_Error( 'not_applicable', __('Only Candidate user can do this'), array( 'status' => 200 ) );
    }// end here
    

}

function shortlisted_jobs($request){
    global $plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';
    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    if ($page == '') {
        $page = 1;
    }
    if ($posts_per_page == '') {
        $posts_per_page = 50;
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);
    $user_id            = $_POST['user_id'];
    $user               = get_user_by('id' , $user_id);
    $user_role          = $user->roles;
    $user_email         = $user->user_email;
    $user_name          = $user->display_name;
    $user_image         = get_user_meta ($user_id,'user_img',true);
    $shortlist_jobs     = array();
    $list_jobs          = array();
    $count = 0;
    //return $offset;
    if( !isset($user_id) || empty($user_id)) {
        return new WP_Error( 'User_id', __('No user id found'), array( 'status' => 400 ) );
    }
    if($user_image != ''){ 
        $cs_jobs_thumb_url = $imge_path.$user_image; 
    }else{ 
        $cs_jobs_thumb_url = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
    }
    //return $on_count;
    if ( $user_role[0] == 'cs_candidate' ) {
        $shortlisted_jobs = get_user_meta( $user_id ,'cs-user-jobs-wishlist',true);
        $total_records = count($shortlisted_jobs);
        $total_pages        = ceil($total_records / $posts_per_page);

        if( !empty($shortlisted_jobs)){
            $cs_shortlist      = array_column_by_two_dimensional($shortlisted_jobs, 'post_id');
            $cs_shortlist_date = array_column_by_two_dimensional($shortlisted_jobs,'date_time');
            if ( $current_page_record > $total_records) {
                $current_page_record = $total_records;
            }
            if ( $total_pages >= $page ) {
                for($i = $offset; $i < $current_page_record; $i++) {
                    $job_applied = false;
                    $post_data   = get_post($cs_shortlist[$i]);
                    $author      = $post_data->post_author;
                    $author_data = get_userdata( $author );
                    $author_email= $author_data->user_email;
                    $author_name = $author_data->display_name;
                    $applicants  = count_usermeta('cs-user-jobs-applied-list', serialize(strval($cs_shortlist[$i])), 'LIKE', true);
                    $application_closing_date  = get_post_meta($cs_shortlist[$i],'cs_application_closing_date');
                    $closing_date = date('d-m-Y', $application_closing_date[0]);
                    $experience   = get_post_meta($cs_shortlist[$i] , 'experiencetotal');
                    $job_status   = get_post_meta($cs_shortlist[$i] , 'cs_job_status');
                    $job_type     = wp_get_post_terms( $cs_shortlist[$i], 'job_type' );
                    $specialisms  = wp_get_post_terms( $cs_shortlist[$i], 'specialisms' );
                    foreach ($specialisms as $k=>$spec) {
                        $specification[$k] = $spec->name;
                    }
                    foreach ($applicants as $ky => $applicant) {
                        if ($user_id==$applicant->data->ID) {$job_applied = true;
                        }else{ $job_applied = false;}
                    }
                    
                    $list_jobs[$count]->post_id         = (int)$cs_shortlist[$i];
                    $list_jobs[$count]->author          = $author;
                    $list_jobs[$count]->user_email      = $author_email;
                    $list_jobs[$count]->user_name       = $author_name;
                    $list_jobs[$count]->candidate_name  = $user_name;
                    $list_jobs[$count]->job_image       = $cs_jobs_thumb_url;
                    $list_jobs[$count]->post_title      = get_the_title($cs_shortlist[$i]);
                    $list_jobs[$count]->content         = strip_shortcodes(get_post_field('post_content', $cs_shortlist[$i]) );
                    $list_jobs[$count]->user_experience = $experience[0];
                    $list_jobs[$count]->specialisms     = $specification;
                    $list_jobs[$count]->job_type        = $job_type[0]->name;
                    $list_jobs[$count]->job_status      = $job_status[0];
                    $list_jobs[$count]->end_date        = $closing_date;
                    $list_jobs[$count]->has_applied     = $job_applied;
                    $list_jobs[$count]->number_of_applicants    = count($applicants);
                    $list_jobs[$count]->job_shortlisted_date = date('Y-m-d', $cs_shortlist_date[$i]);
                    $list_jobs[$count]->job_posted_date = get_the_date("Y-m-d H:i:s",$cs_shortlist[$i]);
                    $count++; 
                }
            }else{
                return new WP_Error( 'No_shortlisted_job_found', __('No shortlisted job found'), array( 'status' => 200 ) );
            }
            
            //$shortlist_jobs[0]->shortlisted_jobs = $list_jobs;
            $response = new WP_REST_Response(array('message' => 'current user all shortlisted jobs', 'data' => array('status' => 200,'params' => $list_jobs)  ));
                return $response;
        }
        
    }
    return new WP_Error( 'not_applicable', __('User is not a condidate user'), array( 'status' => 200 ) );
}//eo

function jobs_taxonomies(){
    $terms = array();
    $taxonomy_objects = array('specialisms','job_type');
    //$taxonomy_objects = get_object_taxonomies( 'jobs','objects' );
    foreach ($taxonomy_objects as $key=>$taxonomy) {
        $terms[$key]->name  = $taxonomy;
        $terms[$key]->child =  get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);
    }
    $response = new WP_REST_Response(array('message' => 'All Filters', 'data' => array('status' => 200,'params'=>$terms)));
    return $response;
}
function conversation_post_comments($request){
    global $wpdb,$plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    $post_id            = $_POST['post_id'];
    $responseArr        = array();
    $totalRows          = $wpdb->get_results("SELECT * FROM wp_comments WHERE comment_post_ID = '$post_id' ");
    $total_records      = count($totalRows);

    //return $comment_count;
    if( $post_id == ''){
        $error['post_id'] = "Empty post id";
        return new WP_Error( 'missing_params:', __('Missing parameters'), array( 'status' => 400 ,'params'=>$error) );
    }
    if( $total_records === 0){
        return new WP_Error( 'no_comment:', __('No Comment found'), array( 'status' => 200 ) );
    }

    if ($page == '') {
        $page = 1;
    }
    if ($posts_per_page == '') {
        $posts_per_page = 50;
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);

    $total_pages        = ceil($total_records / $posts_per_page);
    if ( $current_page_record > $total_records) {
        $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
        for($i = $offset; $i < $current_page_record; $i++) {

            $image = get_user_meta( $totalRows[$i]->user_id , 'user_img' );
            if ($image == '') {
                $img_vval = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
            }else{
                $img_vval = $imge_path.$image[0];
            }
            $responseArr[$i]->comment_ID       = $totalRows[$i]->comment_ID ;
            $responseArr[$i]->comment_parent   = $totalRows[$i]->comment_parent ;
            $responseArr[$i]->user_id          = $totalRows[$i]->user_id;
            $responseArr[$i]->comment_author   = $totalRows[$i]->comment_author;
            $responseArr[$i]->comment_content  = $totalRows[$i]->comment_content;
            $responseArr[$i]->comment_approved = $totalRows[$i]->comment_approved;
            $responseArr[$i]->comment_date     = $totalRows[$i]->comment_date;
            $responseArr[$i]->comment_status   = wp_get_comment_status($totalRows[$i]->comment_ID);
            $responseArr[$i]->user_img         = $img_vval;//get_avatar_url($comment->user_id, 32 );
            $responseArr[$i]->have_reply       = has_comment_children_wpse($totalRows[$i]->comment_ID);
        }
    }else{
        return new WP_Error( 'no_comment_found', __('No comment found'), array( 'status' => 200 ) );
    }
    $comments = array_values($responseArr);
    $response = new WP_REST_Response(array('message'=> 'comments','data'=>array('status'=> 200,'params'=>$comments) ));
    return $response;
}
function conversations($request) {
    global $wpdb,$plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';
    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    $user_id            = $_POST['user_id'];
    $exist_user         =  get_user_by('id',$user_id);
    $question = array();

    if ( !isset( $user_id ) || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $exist_user ) || empty( $exist_user )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    $question['offset']         = (($page-1) * $posts_per_page);
    $question['posts_per_page'] = $posts_per_page;
    $question['post_type']      = 'question';
    $question['post_status']    = 'publish,private_post';

    $conversations = get_posts( $question);
    $conversationsArr = array();
    foreach ($conversations as $key => $value) {
        $content = str_replace("\"", "'", $value->post_content);
        $content = str_replace(" />", ">", $content);
        $conversationsArr[$key]->question_id        =  $value->ID;
        $id = $conversationsArr[$key]->question_id;
        $conversationsArr[$key]->question_Title     = $value->post_title;
        $conversationsArr[$key]->question_content   = $content;
        $conversationsArr[$key]->question_Date      = $value->post_date;
        $question_meta  = $wpdb->get_results ( "SELECT * FROM wp_ap_qameta WHERE post_id=$id");
        $curr_votes          = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id= $id AND vote_user_id = $user_id AND vote_type = 'vote'" );
        $curr_flags          = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id= $id AND vote_user_id = $user_id AND vote_type = 'flag'" );
        if (!empty($curr_flags)) {
            $curr_flags = '1';
        }else{ $curr_flags = '';} 
        $conversationsArr[$key]->currect_vote      = $curr_votes[0]->vote_value != null ? $curr_votes[0]->vote_value : '';
        $conversationsArr[$key]->currect_flag      = $curr_flags;
        foreach ($question_meta as $k => $meta) {
            $questionnaire_image  = get_usermeta( $meta->roles, 'user_img' );
            if ($questionnaire_image != '') {
                $questionnaire_image = $imge_path.$questionnaire_image;
            }else{
                $questionnaire_image = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
            }
            $conversationsArr[$key]->question_views         = $meta->views;
            $conversationsArr[$key]->question_subscribers   = $meta->subscribers;
            $conversationsArr[$key]->question_answers       = $meta->answers;
            $conversationsArr[$key]->question_flags         = $meta->flags;
            $conversationsArr[$key]->question_votes_up      = $meta->votes_up;
            $conversationsArr[$key]->question_votes_down    = $meta->votes_down;
            $conversationsArr[$key]->questionnaire_id       = $meta->roles;
            $user_data = get_user_by( 'id',$meta->roles ); 
            $conversationsArr[$key]->author_name            = $user_data->display_name;
            $conversationsArr[$key]->author_image           = $questionnaire_image;
            $conversationsArr[$key]->question_status        = get_post_status($value->ID);

        }
    }
   $response = new WP_REST_Response(array('message' => 'All Conversations Data', 'data' => array('status' => 200,'param'=>$conversationsArr)));
        return $response;
}
function conversations_search($request){

    global $wpdb,$plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    $user_id            = $_POST['user_id'];
    $question_id        = $_POST['question_id'];
    $conversation_type  = $_POST['conversation_type'];
    $keyword            = $_POST['keyword'];
    $filterBy           = $_POST['filter_by'];
    $filter             = $_POST['filter'];
    $conversation_value = array('question','answer'); 
    $conversationsArr   = array();
    $type               = '';

    if ( !isset($conversation_type) || empty($conversation_type)) {
        return new WP_Error( 'missing_conversations_type', __('Conversation type not found.'), array( 'status' => 400 ) );
    }
    if ( !in_array($conversation_type, $conversation_value)) {
        return new WP_Error( 'invalid_type', __('Invalid Conversation Type.'), array( 'status' => 400 ) );
    }
    if ( $conversation_type == 'answer') {
        if ( !isset( $question_id) || empty( $question_id)) {
            return new WP_Error( 'not_found', __('Question ID not found.'), array( 'status' => 400 ) );
        }
    }
    if ($conversation_type == 'answer') {
        $type .= "post_parent = $question_id ";
    }else{
        $type  .= "post_type = '$conversation_type' "; 
    }
    //post_parent = $questionId
    
    $query  = "SELECT * FROM wp_ap_qameta INNER JOIN wp_posts ON wp_posts.ID=wp_ap_qameta.post_id ";
    if ( isset($keyword) && $keyword != '' ) {
        $query .= "WHERE (post_title LIKE '%$keyword%' OR post_content LIKE '%$keyword%') AND $type AND (post_status = 'publish' OR post_status = 'private_post') ";
    }else{
        $query .= "WHERE $type AND (post_status = 'publish' OR post_status = 'private_post') ";
    }
    if ( isset($filterBy) && $filterBy != '' ){ 
        if ( $filterBy == 'votes') {
            $query .= " ORDER BY (votes_up + votes_down) ";
        }else{
            $query .= "ORDER BY $filterBy ";
        }
        if ( isset( $filter) && !empty( $filter )) {
            $query .= "$filter";
        }
    }
    //return $query;
    $totalRows = $wpdb->get_results($query);
    
    if ( empty($totalRows)) {
        return new WP_Error( 'no_result_found', __('No search appear for the job.'), array( 'status' => 200 ) );
    }
    if ($page == '') {
        $page = 1;
    }
    if ($posts_per_page == '') {
        $posts_per_page = 50;
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);

    $total_records      = count($totalRows);
    $total_pages        = ceil($total_records / $posts_per_page);
    if ( $current_page_record > $total_records) {
        $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
       for($i = $offset; $i < $current_page_record; $i++){
            if ( $conversation_type == 'answer' ) {
                $ID                 = $totalRows[$i]->ID;
                $user_data          = get_user_by( 'id',$totalRows[$i]->roles );
                $image              = get_usermeta( $totalRows[$i]->roles, 'user_img' );
                $comments           =  wp_count_comments( (int)$ID );
                if ($image != '') {
                    $image = $imge_path.$image;
                }else{
                    $image = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
                }
                $conversationsArr[$i]->question_id        = $question_id;
                $conversationsArr[$i]->answer_id          = $ID;
                $conversationsArr[$i]->answer_content     = $totalRows[$i]->post_content;
                $conversationsArr[$i]->question_Date      = $totalRows[$i]->post_date;
                $conversationsArr[$i]->answer_author      = $totalRows[$i]->roles;
                $conversationsArr[$i]->answer_author_img  = $image;
                $conversationsArr[$i]->answer_author_name = $user_data->display_name;
                $conversationsArr[$i]->flags              = $totalRows[$i]->flags;
                $conversationsArr[$i]->answer_votes_up    = $totalRows[$i]->votes_up;
                $conversationsArr[$i]->answer_votes_down  = $totalRows[$i]->votes_down;
                $conversationsArr[$i]->answer_status      = $totalRows[$i]->post_status;
                $conversationsArr[$i]->comments           = $comments->all;
            }
            if ( $conversation_type == 'question') {
                $ID                 = $totalRows[$i]->ID;
                $user_data          = get_user_by( 'id',$totalRows[$i]->roles );
                $image              = get_usermeta( $totalRows[$i]->roles, 'user_img' );
                if ($image != '') {
                    $image = $imge_path.$image;
                }else{
                    $image = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
                }
                $conversationsArr[$i]->question_id        = (int)$ID;
                $conversationsArr[$i]->question_Title     = $totalRows[$i]->post_title;
                $conversationsArr[$i]->question_content   = $totalRows[$i]->post_content;
                $conversationsArr[$i]->question_Date      = $totalRows[$i]->post_date;
                $conversationsArr[$i]->questionnaire_id   = $totalRows[$i]->roles;
                $conversationsArr[$i]->author_image       = $image;
                $conversationsArr[$i]->author_name        = $user_data->display_name;
                $conversationsArr[$i]->question_flags     = $totalRows[$i]->flags;
                $conversationsArr[$i]->question_votes_up  = $totalRows[$i]->votes_up;
                $conversationsArr[$i]->question_votes_down= $totalRows[$i]->votes_down;
                $conversationsArr[$i]->question_subscribers= $totalRows[$i]->subscribers;
                $conversationsArr[$i]->question_answers   = $totalRows[$i]->answers;
                $conversationsArr[$i]->question_views     = $totalRows[$i]->views;
                $conversationsArr[$i]->question_status    = $totalRows[$i]->post_status;
            }
            if ( isset( $user_id ) && !empty( $user_id )) {
                $curr_votes          = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id = $ID AND vote_user_id = $user_id AND vote_type = 'vote'" );
                $curr_flags          = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id = $ID AND vote_user_id = $user_id AND vote_type = 'flag'" );
                if (!empty($curr_flags)){ $curr_flags = '1'; }else{ $curr_flags = '';} 
                if (!empty($curr_votes)){ $curr_votes = $curr_votes[0]->vote_value; }else{ $curr_votes =''; }
                $conversationsArr[$i]->currect_vote = $curr_votes; 
                $conversationsArr[$i]->currect_flag = $curr_flags;
            }
        } 
    }else{
        return new WP_Error( 'no_job_found', __('No job found'), array( 'status' => 200 ) );
    }
    $post_data = array_values($post_data);
    $response = new WP_REST_Response(array('message'=> 'all jobs','data'=>array('status'=> 200,'param'=>$conversationsArr) ));
    return $response;
}
//eo
function question_answers($request){
    global $wpdb,$plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    $questionId         = $_POST['question_id'];
    $user_id            = $_POST['user_id'];
    $exist_user         = get_user_by('id',$user_id);
    $Question           = $wpdb->get_results("SELECT post_title FROM wp_posts WHERE ID = $questionId");
    $answersId          = array();

    if ( !isset( $user_id ) || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $exist_user ) || empty( $exist_user )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if( !isset($questionId) || empty($questionId)) {
        return new WP_Error( 'question_id_not_found', __('No question id found.'), array( 'status' => 400 ) );
    }
    if (empty($Question)) {
        return new WP_Error( 'question_does_not_exist', __('This question is not found.'), array( 'status' => 400 ) );
    }

    if ($page == '') {
        $page = 1;
    }
    if ($posts_per_page == '') {
        $posts_per_page = 50;
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);
    $query              = $wpdb->get_results("SELECT ID FROM wp_posts WHERE post_parent = $questionId AND (post_status = 'publish' OR post_status = 'private_post')");
    $total_records      = count($query);
    $total_pages        = ceil($total_records / $posts_per_page);
    //condition start
    if ( $current_page_record > $total_records) {
        $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
        for ($i = $offset; $i < $current_page_record; $i++) { 
            //$posts = get_posts( $query[$i]->activity_a_id );
            //$answersId[$i] = $posts;
            $ID                            = $query[$i]->ID;
            $post                          = get_post( $ID );
            $comments                      =  wp_count_comments( (int)$ID );
            $content = str_replace("\"", "'", $post->post_content);
            $content = str_replace(" />", ">", $content);
            $questionnaire_image  = get_usermeta( $post->post_author, 'user_img' );
            if ($questionnaire_image != '') {
                $questionnaire_image = $imge_path.$questionnaire_image;
            }else{
                $questionnaire_image = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
            }
            $answer_meta  = $wpdb->get_results ( "SELECT * FROM wp_ap_qameta WHERE post_id=$ID" );
            $curr_votes          = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id= $ID AND vote_user_id = $user_id AND vote_type = 'vote'" );
            $curr_flags          = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id= $ID AND vote_user_id = $user_id AND vote_type = 'flag'" );
            if (!empty($curr_flags)) {
                $curr_flags = '1';
            }else{ $curr_flags = '';}
            $answersId[$i]->question_id         = $questionId;
            $answersId[$i]->answer_id           = $ID;
            $answersId[$i]->answer_content      = $content;
            $answersId[$i]->answer_date         = $post->post_date != null ? $post->post_date :'';
            $answersId[$i]->answer_author       = $post->post_author != null ? $post->post_author :'';
            $answersId[$i]->answer_author_img   = $questionnaire_image ;
            $answersId[$i]->currect_vote        = $curr_votes[0]->vote_value != null ? $curr_votes[0]->vote_value : '';
            $answersId[$i]->currect_flag        = $curr_flags;
            $answersId[$i]->answer_author_name  = get_usermeta((int)$post->post_author , 'display_name');
            $answersId[$i]->flags               = $answer_meta[0]->flags!=''? $answer_meta[0]->flags:'0';
            $answersId[$i]->answer_votes_up     = $answer_meta[0]->votes_up!=''? $answer_meta[0]->votes_down:'0';
            $answersId[$i]->answer_votes_down   = $answer_meta[0]->votes_down!=''? $answer_meta[0]->votes_down:'0';
            $answersId[$i]->answer_status       = $post->post_status!= null ? $post->post_status :'';
            $answersId[$i]->comments            = $comments->all;
 
        }
        //return $answersId;
        $resp = new WP_REST_Response(array('message'=> 'Successfull!','data'=>array('status'=> 200,'params'=> $answersId) ));
        return $resp;
    }else{
        return new WP_Error( 'no_answer_found', __('No answer found'), array( 'status' => 200 ) );
    }
    //end

}
function ask_question(){
    global $wpdb,$plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $user_id        = $_POST['user_id'];
    $exist_user     =  get_user_by('id',$user_id);
    $post_title     = $_POST['post_title'];
    $post_content   = $_POST['post_content'];
    $post_status    = $_POST['post_status'];
    $content_img    = $_FILES['content_img'];
    $time           = current_time('Y-m-d H:i:s',1);
    $curret_time    = $time;
    $conversationsArr = array();
    $upload_content_img = array();

    // if ( !isset($job_id) || empty($job_id) ) {
    //    return new WP_Error( 'no_post_id', __('No post id found'), array( 'status' => 400 ) );
    // }
    if ( !isset( $exist_user ) || empty( $exist_user )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $post_title ) || empty( $post_title )) {
        return new WP_Error( 'no_post_title_found', __('No post title found'), array( 'status' => 400 ) );
    }
    if ( !isset( $post_content ) || empty( $post_content )) {
        return new WP_Error( 'no_post_content_found', __('No post content found'), array( 'status' => 400 ) );
    }
    if ( !isset( $post_status ) || empty( $post_status )) {
        return new WP_Error( 'no_post_status_found', __('No post status found'), array( 'status' => 400 ) );
    }
    if ( ( isset($content_img) && !empty($content_img)) ) {
        $count_images = count($content_img['name']);
        for ($i=0; $i <$count_images ; $i++) { 
            if ($content_img['size'][$i] != 0 && $content_img['error'][$i] == 0){
                $f_name         = $content_img['name'][$i];
                $f_path         = $content_img['tmp_name'][$i];
                $img_url        = uplod_media($f_name,$f_path);
                $post_content  .= '<img src="'.$img_url.'" />\n';
            }
        }
    }

    if ($post_status == 'publish') {
        $post_status == 'publish';
    }elseif ( $post_status == 'private_post') {
        $post_status == 'private_post';
    }else{
        $post_status == 'publish';
    }
    $conversations_data['post_author']      = $user_id;
    $conversations_data['post_title']       = $post_title;
    $conversations_data['post_content']     = $post_content;
    $conversations_data['post_status']      = $post_status;//'private_post';
    $conversations_data['post_type']        = 'question';
    //return $conversations_data;
    $ID = wp_insert_post($conversations_data);
    $question_id  = $ID; 
    $result = $wpdb->insert( 'wp_ap_activity', array( 
        'activity_id'       => '', 
        'activity_action'   => 'new_q',
        'activity_q_id'     => $ID,
        'activity_a_id'     => 0,
        'activity_c_id'     => 0,
        'activity_user_id'  => $user_id,
        'activity_date'     => $curret_time,
    ));

    $question_meta = $wpdb->get_results ( "SELECT * FROM wp_ap_qameta WHERE post_id=$ID" );
    $content = str_replace("\"", "'", $post_content);
    $content = str_replace(" />", ">", $content);
    $curr_votes = $wpdb->get_results( "SELECT * FROM wp_ap_votes WHERE vote_post_id= $ID AND vote_user_id = $user_id AND vote_type = 'vote'" );
    $curr_flags  = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id= $ID AND vote_user_id = $user_id AND vote_type = 'flag'" );
    if (!empty($curr_flags)){ $curr_flags = '1'; }else{ $curr_flags = '';} 
    if (!empty($curr_votes)){ $curr_votes = $curr_votes[0]->vote_value; }else{ $curr_votes =''; }
    $conversationsArr[0]->question_id            = $question_id;
    $conversationsArr[0]->questionnaire_id       = $user_id;
    $conversationsArr[0]->question_Title         = $post_title;
    $conversationsArr[0]->question_content       = $content;
    $conversationsArr[0]->question_Date          = $curret_time;
    $conversationsArr[0]->currect_vote           = $curr_votes;
    $conversationsArr[0]->curr_flags             = $curr_flags;


    foreach ($question_meta as $k => $meta) {
        $questionnaire_image  = get_usermeta( $meta->roles, 'user_img' );
        if ($questionnaire_image != '') {
            $questionnaire_image = $imge_path.$questionnaire_image;
        }else{
            $questionnaire_image = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
        }
        $conversationsArr[0]->question_views         = $meta->views;
        $conversationsArr[0]->question_subscribers   = $meta->subscribers;
        $conversationsArr[0]->question_answers       = $meta->answers;
        $conversationsArr[0]->question_flags         = $meta->flags != null ? $meta->flags : '0';
        $conversationsArr[0]->question_votes_up      = $meta->votes_up;
        $conversationsArr[0]->question_votes_down    = $meta->votes_down != null ? $meta->votes_down : '0' ;
        $conversationsArr[0]->questionnaire_id       = $meta->roles;
        $user_data = get_user_by( 'id',$meta->roles ); 
        $conversationsArr[0]->author_name            = $user_data->display_name;
        $conversationsArr[0]->author_image           = $questionnaire_image;
        $conversationsArr[0]->question_status        = get_post_status($ID);

    }

    if($result)
    {
        $response = new WP_REST_Response(array('message' => 'Question is successfully sunmitted.', 'data' => array('status' => 200,'param'=>$conversationsArr)));
        return $response;
    }
    else {
        return new WP_Error( 'failed', __('failed', array('status' => 406,'param' => $ID )) );
    } //global $wpdb;
}//eo jobs function

//reply_answer
function reply_answer(){
    global $wpdb,$plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $user_id    	= $_POST['user_id'];
    $questionId 	= $_POST['question_id'];
    $questionAuthorID =$_POST['question_author_id'];
    $answerContent 	= $_POST['answer'];
    $content_img    = $_FILES['content_img'];
    $post_status    = $_POST['post_status'];
    $exist_user 	=  get_user_by('id',$user_id);
    $user_name      = $exist_user->display_name;
    $time       	= current_time('Y-m-d H:i:s',1);
    $current_time 	= $time;
    $answerArray    = array();
    $answersId      = array();
    $questionStatus = get_post_status($questionId);

    $authorId       = get_post_field( 'post_author', $questionId );
    $addNotificationIn  = $authorId;
    $addNotifierId  = $questionId;
    $action_type    = 'question';
    $action_parent  = $questionId;
    $questionData   = $wpdb->get_results("SELECT ptype FROM wp_ap_qameta WHERE post_id = $questionId");
    $questionExist  = $questionData[0]->ptype;

    if ( $questionStatus == 'private_post') {
        return new WP_Error( 'private_post', __('This is private post', array('status' => 200)) );
    }else{
        if ( !isset( $questionAuthorID ) || empty( $questionAuthorID )) {
            return new WP_Error( 'question_author_id_not_found', __('question author id not found'), array( 'status' => 400 ) );
        }
        if ( $authorId == $user_id ) {
            return new WP_Error( "can't_answer_own_post", __(" You can't answer on your question."), array( 'status' => 400 ) );
        }
        if ( $questionExist != 'question') {
            return new WP_Error( "not_a_question", __("You can comment on answer but can't reply "), array( 'status' => 400 ) );
        }

       $Question        = $wpdb->get_results("SELECT post_title FROM wp_posts WHERE ID = $questionId");
        if ( !isset( $exist_user ) || empty( $exist_user )) {
            return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
        }
        if ( !isset( $Question ) || empty($Question)) {
            return new WP_Error( 'question_does_not_exist', __('Question not found'), array( 'status' => 400 ) );
        }
        if ( !isset( $answerContent ) || empty( $answerContent )) {
            return new WP_Error( 'answer_not_found', __('No Answer found'), array( 'status' => 400 ) );
        }
        if ( !isset( $post_status ) || empty( $post_status )) {
        return new WP_Error( 'no_post_status_found', __('No post status found'), array( 'status' => 400 ) );
        }
        if ($post_status == 'publish') {
            $post_status == 'publish';
        }elseif ( $post_status == 'private_post') {
            $post_status == 'private_post';
        }else{
            $post_status == 'publish';
        }
        if ( ( isset($content_img) && !empty($content_img)) ) {
            $count_images = count($content_img['name']);
            for ($i=0; $i <$count_images ; $i++) { 
                if ($content_img['size'][$i] != 0 && $content_img['error'][$i] == 0){
                    $f_name         = $content_img['name'][$i];
                    $f_path         = $content_img['tmp_name'][$i];
                    $img_url        = uplod_media($f_name,$f_path);
                    $answerContent  .= '<img src="'.$img_url.'" >';
                }
            }
        }
        $quest = $Question[0]->post_title;
        $answerArray['post_author']      = $user_id;
        $answerArray['post_title']       = $quest;
        $answerArray['post_content']     = $answerContent;
        $answerArray['post_name']        = $questionId;
        $answerArray['post_parent']      = $questionId;
        $answerArray['post_status']      = $post_status;
        $answerArray['post_type']        = 'answer';
        $ID = wp_insert_post($answerArray);

        $activityTable = $wpdb->insert( 'wp_ap_activity', array( 
            'activity_id'       => '', 
            'activity_action'   => 'new_a',
            'activity_q_id'     => $questionId,
            'activity_a_id'     => $ID,
            'activity_c_id'     => 0,
            'activity_user_id'  => $user_id,
            'activity_date'     => $current_time
        ));
        $post                          = get_post( $ID );
        $comments                      =  wp_count_comments( (int)$ID );
        $content = str_replace("\"", "'", $post->post_content);
        $content = str_replace(" />", ">", $content);
        $questionnaire_image  = get_usermeta( $post->post_author, 'user_img' );
        if ($questionnaire_image != '') {
            $questionnaire_image = $imge_path.$questionnaire_image;
        }else{
            $questionnaire_image = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
        }
        $answer_meta  = $wpdb->get_results ( "SELECT * FROM wp_ap_qameta WHERE post_id=$ID" );
        $curr_votes          = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id= $ID AND vote_user_id = $user_id AND vote_type = 'vote'" );
        $curr_flags          = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id= $ID AND vote_user_id = $user_id AND vote_type = 'flag'" );
        
        $answersId[0]->question_id         = $questionId;
        $answersId[0]->answer_id           = (string)$ID;
        $answersId[0]->answer_content      = $content;
        $answersId[0]->answer_date         = $post->post_date != null ? $post->post_date :'';
        $answersId[0]->answer_author       = $post->post_author != null ? $post->post_author :'';
        $answersId[0]->answer_author_img   = $questionnaire_image ;
        $answersId[0]->currect_vote        = $curr_votes[0]->vote_value != null ? $curr_votes[0]->vote_value : '';
        $answersId[0]->currect_flag        = $curr_flags[0]->vote_value != null ? $curr_flags[0]->vote_value : '';
        $answersId[0]->answer_author_name  = get_usermeta((int)$post->post_author , 'display_name');
        $answersId[0]->flags               = $answer_meta[0]->flags!=''? $answer_meta[0]->flags:'0';
        $answersId[0]->answer_votes_up     = $answer_meta[0]->votes_up!=''? $answer_meta[0]->votes_down:'0';
        $answersId[0]->answer_votes_down   = $answer_meta[0]->votes_down!=''? $answer_meta[0]->votes_down:'0';
        $answersId[0]->answer_status       = $post->post_status!= null ? $post->post_status :'';
        $answersId[0]->comments            = $comments->all;

        if($ID)
        {
        	$message            = $user_name.' replied on your question';
            $notificationData   =  notification_data( $action_type,$addNotifierId );
            $tokens             = get_user_meta($user_id, 'device_token',true);
            if ( !empty($tokens)) {
                foreach ($tokens as $key => $token) {
                    iospushnotification($token,$message,$action_type,$notificationData);
                }
            }
            send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id );
            $resp = new WP_REST_Response(array('message'=> 'Successfull!','data'=>array('status'=> 200,'params'=> $answersId) ));
            return $resp;
        }
        else {
            return new WP_Error( 'failed', __('failed', array('status' => 406,'param' => $ID )) );
        } 
    }

    
}
// update conversation
function update_conversation(){
    global $wpdb,$plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $user_id                = $_POST['user_id'];
    $conversation_id        = $_POST['conversation_id'];
    $conversation_title     = $_POST['conversation_title'];
    $conversation_desc      = $_POST['conversation_desc'];
    $conversation_status    = $_POST['conversation_status'];
    $conversations_type     = $_POST['conversations_type'];
    $content_img            = $_FILES['content_img'];
    $status_value           = array('private_post', 'publish');
    $exist_user             = get_user_by('id',$user_id);
    $postExist              = get_post($conversation_id,true);
    $post_author            = $postExist['post_author'];
    $ID                     = $conversation_id;
    $post                   = get_post( $ID );
    $post_type              =  $post->post_type;
    $updates                = array();
    $conversation           = array();

    if ( !isset( $user_id ) || empty( $user_id )) {
        return new WP_Error( 'missing_params:', __('user id not found.'), array( 'status' => 400 ) );
    }
    if ( !isset( $exist_user ) || empty( $exist_user )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 401 ) );
    }
    if ( !isset( $conversation_id ) || empty( $conversation_id )) {
        return new WP_Error( 'missing_params:', __('Conversation id not found.'), array( 'status' => 400 ) );
    }
    if ( $postExist == null) {
        return new WP_Error( 'not_found:', __('Post not found.'), array( 'status' => 400 ) );
    }
    if ( !isset( $conversations_type ) || empty( $conversations_type )) {
        return new WP_Error( 'missing_param:', __('Conversation type is not found.'), array( 'status' => 400 ) );
    }
    if ( $post_type != $conversations_type ) {
        return new WP_Error( 'invalid_params:', __('Enter a valid conversation type .'), array( 'status' => 400 ) );
    }
    if ($post_author != $user_id) {
        return new WP_Error( 'invalid_author:', __('Invalid author.'), array( 'status' => 400 ) );
    }
    if ( isset( $conversation_title) ) {
        if ( !empty($conversation_title)) {
            $updates['post_title'] = $conversation_title;
        }else{
          return new WP_Error( 'missing_params:', __("You can't pass the empty title."), array( 'status' => 400 ) );  
        }
    }
    if ( isset( $conversation_desc) ) {
        if ( !empty($conversation_desc)) {
            if ( ( isset($content_img) && !empty($content_img)) ) {
                $count_images = count($content_img['name']);
                for ($i=0; $i <$count_images ; $i++) { 
                    if ($content_img['size'][$i] != 0 && $content_img['error'][$i] == 0){
                        $f_name         = $content_img['name'][$i];
                        $f_path         = $content_img['tmp_name'][$i];
                        $img_url        = uplod_media($f_name,$f_path);
                        $conversation_desc  .= '<img src="'.$img_url.'" />\n';
                    }
                }
            }
            $updates['post_content'] = $conversation_desc;
        }else{
          return new WP_Error( 'missing_params:', __("You can't pass the empty title."), array( 'status' => 400 ) );  
        }
    }
    if ( isset( $conversation_status) ) {
        if ( !empty($conversation_status)) {
            if ( !in_array($conversation_status, $status_value)) {
                return new WP_Error( 'pass_correct_status:', __("Please pass correct status."), array( 'status' => 400 ) );
            }
            $updates['post_status'] = $conversation_status;
        }else{
          return new WP_Error( 'missing_params:', __("You can't pass the empty title."), array( 'status' => 400 ) );  
        }
    }
    if (empty($updates)) {
        return new WP_Error( 'no_update:', __("No update request found."), array( 'status' => 200 ) ); 
    }else{
        $updates['ID']           = $conversation_id;
        $updates['post_author '] = $user_id;
        // if ( $updates['post_title'] || $updates['post_content']) {
        //     return wp_update_post( $updates ,true );
        //     $updated_post = get_post($conversation_id,true);
        //     $new_arr['post_author']     = $updated_post['post_author'];
        //     $new_arr['post_title']      = $updated_post['post_title'];
        //     $new_arr['post_content']    = $updated_post['post_content'];
        //     $new_arr['post_status']     = 'inherit';
        //     $new_arr['comment_status']  = 'closed';
        //     $new_arr['ping_status']     = 'closed';                
        //     $new_arr['parent_post']     = 'revision';
        //     $new_arr['parent_post']     = $conversation_id;
        //     //return wp_insert_post($new_arr);
        // }
        //return $updates;
        wp_update_post( $updates ,true );
        $post           = get_post( $ID );
        $comments       = wp_count_comments( (int)$ID );
        $content        = str_replace("\"", "'", $post->post_content);
        $content        = str_replace(" />", ">", $content);
        $q_image        = get_usermeta( $post->post_author, 'user_img' );
        $userdata       = get_user_by('id',$user_id);
        $conv_meta      = $wpdb->get_results ( "SELECT * FROM wp_ap_qameta WHERE post_id=$ID");
        $curr_votes  = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id= $ID AND vote_user_id = $user_id AND vote_type = 'vote'" );
        $curr_flags  = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id= $ID AND vote_user_id = $user_id AND vote_type = 'flag'" );
        if (!empty($curr_flags)) { $curr_flags = '1'; }else{ $curr_flags = '';}
        if ($q_image != '') {
            $q_image = $imge_path.$q_image;
        }else{
            $q_image = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
        }
        if ( $post_type == 'question') {
            $conversation[0]->question_id        = (int)$ID;
            $conversation[0]->question_Title     = $post->post_title;
            $conversation[0]->question_content   = $content;
            $conversation[0]->author_image       = $q_image ;
            $conversation[0]->question_Date      = $post->post_date;
            $conversation[0]->currect_vote       = $curr_votes[0]->vote_value != null ? $curr_votes[0]->vote_value : '';
            $conversation[0]->currect_flag       = $curr_flags;
            $conversation[0]->question_views         = $conv_meta[0]->views;
            $conversation[0]->question_subscribers   = $conv_meta[0]->subscribers;
            $conversation[0]->question_answers       = $conv_meta[0]->answers;
            $conversation[0]->question_flags         = $conv_meta[0]->flags;
            $conversation[0]->question_votes_up      = $conv_meta[0]->votes_up;
            $conversation[0]->question_votes_down    = $conv_meta[0]->votes_down;
            $conversation[0]->questionnaire_id       = $conv_meta[0]->roles;
            $conversation[0]->author_name            = $userdata->display_name;
            $conversation[0]->author_image           = $q_image;
            $conversation[0]->question_status        = get_post_status($ID);
        }
        if ( $post_type == 'answer') {

            $conversation[0]->answer_id          = $ID;
            $conversation[0]->answer_content     = $content;
            $conversation[0]->answer_date        = $post->post_date != null ? $post->post_date :'';
            $conversation[0]->answer_author      = $post->post_author != null ? $post->post_author :'';
            $conversation[0]->answer_author_img  = $q_image ;
            $conversation[0]->currect_vote       = $curr_votes[0]->vote_value != null ? $curr_votes[0]->vote_value : '';
            $conversation[0]->currect_flag       = $curr_flags;
            $conversation[0]->answer_author_name = get_usermeta((int)$post->post_author , 'display_name');
            $conversation[0]->flags              = $conv_meta[0]->flags!=''? $conv_meta[0]->flags:'0';
            $conversation[0]->answer_votes_up    = $conv_meta[0]->votes_up!=''? $conv_meta[0]->votes_down:'0';
            $conversation[0]->answer_votes_down  = $conv_meta[0]->votes_down!=''? $conv_meta[0]->votes_down:'0';
            $conversation[0]->answer_status      = $post->post_status!= null ? $post->post_status :'';
            $conversation[0]->comments           = $comments->all;
        }
        $response = new WP_REST_Response(array('message' => 'Post updated!', 'data' => array('status' => 200,'params'=>$conversation)));
            return $response;
    }
 
}
// eo update conversation
// Delete conversation
function delete_conversation(){
    $conversation_id        = $_POST['conversation_id'];
    $postExist              = get_post($conversation_id,true);

    if ( !isset( $conversation_id ) || empty( $conversation_id )) {
        return new WP_Error( 'missing_params:', __('Conversation id not found.'), array( 'status' => 400 ) );
    }
    if ( $postExist == null) {
        return new WP_Error( 'not_found:', __('Post not found.'), array( 'status' => 400 ) );
    }

    wp_trash_post($conversation_id);
    $response = new WP_REST_Response(array('message' => 'Deleted successfully!', 'data' => array('status' => 200)));
    return $response; 

}
//eo delete
//vote function
function vote_and_flag(){
    global $wpdb;
    $user_id        = $_POST['user_id'];
    $questionId     = $_POST['conversation_id'];
    $authorId       = get_post_field( 'post_author', $questionId );
    $activity_type  = $_POST['activity_type'];
    $vote           = $_POST['vote'];
    $exist_user     =  get_user_by('id',$user_id);
    $user_name      = $exist_user->display_name;
    $time           = current_time('Y-m-d H:i:s',1);
    $activity_value = array('flag','vote');
    $vote_value     = array('like','unlike');
    $addNotificationIn  = $authorId;
    $addNotifierId  = $questionId;
    $action_type    = 'question';
    $action_parent  = '';
    $all_users      = array();
    $Question       = $wpdb->get_results("SELECT post_title FROM wp_posts WHERE ID = $questionId");

    if ( !isset( $exist_user ) || empty( $exist_user )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $questionId ) || empty($questionId)) {
        return new WP_Error( 'no_question_found', __('Question not found'), array( 'status' => 400 ) );
    }
    if ( !isset( $activity_type ) || empty($activity_type)) {
        return new WP_Error( 'activity_type_not_found', __('enter a activity type'), array( 'status' => 400 ) );
    }
    if ( !in_array($activity_type, $activity_value) ) {
        return new WP_Error( 'invalid_activity_type', __('Invalid activity type'), array( 'status' => 400 ) );
    }
    
    $author = $wpdb->get_results("SELECT * FROM wp_ap_qameta WHERE post_id = $questionId");
    if ( empty($author) ) {
        return new WP_Error( 'no_post_found', __('No post found'), array( 'status' => 400 ) );
    }
    if ( !empty( $author)) {
        $author_id          = $author[0]->roles;
        $total_flags        = $author[0]->flags;
        $total_votes_up     = $author[0]->votes_up;
        $total_votes_down   = $author[0]->votes_down;
    }

    if ( $activity_type == 'flag' ) {
        $cs_vote_value  = '';
        $update         = 'flags';
        $total_acts     = $total_flags;
        $status 		= 'Thank you for reporting this post.';
        $message 		= $user_name.' reported on your post.';
        
    }
    if ($activity_type == 'vote') {
        if ( !isset( $vote ) || empty($vote)) {
        return new WP_Error( "can't_find_type_of_vote", __("please define is vote."), array( 'status' => 400 ) );
        }
        if ( !in_array($vote, $vote_value)) {
           return new WP_Error( "invalid_vote", __("Enter a valid vote either like or unlike"), array( 'status' => 400 ) ); 
        }
        if( $vote == 'like'){
            $cs_vote_value = 1;
            $update = 'votes_up';
            $total_acts     = $total_votes_up;
        }else{
            $cs_vote_value = -1;
            $update = 'votes_down';
            $total_acts     = $total_votes_down;
        }
        $cs_vote_value = (int)$cs_vote_value;
    }

    $query = "SELECT * FROM wp_ap_votes WHERE vote_post_id = $questionId AND vote_user_id = $user_id AND vote_type= '$activity_type'";
    if($activity_type == 'vote'){
        $query .= "AND vote_value = $cs_vote_value ";
    }
    $value = $wpdb->get_results($query);
    $vote_id = $value[0]->vote_id;
    //activity area
    if ( $activity_type == 'flag' ) {
        if ( !empty($value) ) {
            return new WP_Error( 'already_reported', __('you have already reported.'), array( 'status' => 200 ) );
        }else{
            $set_value = 1;
            $vote_rec_user = 0;
        }
    }
    if ( $activity_type == 'vote' ) {
        if ( $user_id == $author_id ) {
            return new WP_Error( "author_can't_answer", __("You can't answer own your post."), array( 'status' => 200 ) );
        }else{
            $vote_rec_user  = $author_id;
            $status 		= 'Thank you for voting.';
            $message 		= $user_name.' voted on your post.';
        }
    }
    if ( empty($value)) {
        $activityTable = $wpdb->insert( 'wp_ap_votes', array( 
            'vote_id'       => '', 
            'vote_post_id'  => $questionId,
            'vote_type'     => $activity_type,
            'vote_date'     => $time,
            'vote_user_id'  => $user_id,
            'vote_rec_user' => $vote_rec_user,
            'vote_value'    => $cs_vote_value
        ));
        $total_acts = (int)$total_acts + 1;    
    }else{

        $activityTable = $wpdb->get_results("DELETE FROM wp_ap_votes WHERE vote_id = $vote_id ");
        $total_acts = (int)$total_acts - 1;
        $status 	= 'Your vote has been removed.';
        $message    = $user_name.' removed the vote on your post.';
    }

    
        $updateTable = $wpdb->get_results("UPDATE wp_ap_qameta set $update = $total_acts WHERE post_id = $questionId");
        $notificationData   =  notification_data( $action_type,$addNotifierId );
        $sendNotificationToUser = get_post($questionId)->post_author;
        $tokens             = get_user_meta($sendNotificationToUser, 'device_token',true);
        if ( !empty($tokens)) {
            foreach ($tokens as $key => $token) {
                iospushnotification($token,$message,$action_type,$notificationData);
            }
        }
        send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id );
        $resp = new WP_REST_Response(array('message'=> $status,'data'=>array('status'=> 200,'params'=> $activityTable) ));
        return $resp;
    
}
function update_comment(){
    global $wpdb,$plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';
    $commentID          = $_POST['comment_id'];
    $commentPostID      = $_POST['comment_post_ID'];
    $commentContent     = $_POST['comment_content'];
    $commentAuthorID    = $_POST['comment_author_id']; 
    $commentExist       = get_comment($commentID);
    $comment            = array();

    if ( !isset( $commentAuthorID ) || empty($commentAuthorID)) {
        return new WP_Error("not_found", __("Author id not found."), array('status' => 400) );
    }
    if ( !isset( $commentID ) || empty($commentID)) {
        return new WP_Error("not_found", __("Comment id not found."), array('status' => 400) );
    }
    if ( !isset( $commentExist ) || empty($commentExist)) {
        return new WP_Error("not_exist", __("Comment id does not exist."), array('status' => 400));
    }
    if ( $commentExist->comment_type != 'anspress') {
        return new WP_Error("not_valid", __("Comment does not belongs to the conversation."), array('status' => 401));
    }
    if ( !isset( $commentContent ) || empty($commentContent)) {
        return new WP_Error("not_found", __("Updated comment not found."), array('status' => 400) );
    }
    if ( !isset( $commentPostID ) || empty($commentPostID)) {
        return new WP_Error("not_found", __("Post id not found."), array('status' => 400) );
    }
    $comment['comment_ID']      = $commentID;
    $comment['comment_content'] = $commentContent;
    //return $comment;
    $duplicate_check = $wpdb->get_results("SELECT comment_ID FROM wp_comments WHERE comment_post_ID= $commentPostID and comment_content = '$commentContent'");
    if ( !empty($duplicate_check) ) {
            return new WP_Error( 'duplicate_comment', __('Duplicate comment detected; it looks as though you’ve already said that!'), array( 'status' => 400 ) );
    }
    $status = wp_update_comment( $comment );
    if ($status) {
        $value = get_comment($commentID);
        $response = new WP_REST_Response(array('message'=> 'successfully! comment posted','data'=>array('status'=> 205,'params'=>$value) ));
        return $response;
    }else{
        return new WP_Error("failed!", __("Fail! to update."), array('status' => 412) );
    }
}

function views(){
    global $wpdb;
    $post_id    = $_POST['post_id'];
    $postStatus = get_post($post_id,true);
    if ( $postStatus == null) {
        return new WP_Error("invalid_post", __("Enter a valid post id."), array('status' => 400) );
    }
    if ( $postStatus['post_status'] != 'publish' || $postStatus['post_type'] != 'question') {
        return new WP_Error("invalid_post", __("Invalid post"), array('status' => 400) );
    }
    if ( isset( $post_id ) && !empty($post_id)) {
        $wpdb->get_results("UPDATE wp_ap_qameta SET views = views+1 WHERE post_id = $post_id AND ptype = 'question'");

        $response = new WP_REST_Response(array('message'=> 'successfully! viewed','data'=>array('status'=> 205) ));
        return $response;
    }else{
        return new WP_Error("not_found", __("Post id not found."), array('status' => 400) );
    }
}
// Friends API

function send_request(){
    global $wpdb;
    $user_id        = $_POST['sender_id'];
    $to_user        = $_POST['reciver_id'];
    $user_status    = get_user_by('id',(int)$user_id);
    $user_name      = $user_status->data->display_name;
    $to_user_status = get_user_by('id',(int)$to_user);
    $rolesAllowed   = array('cs_candidate','cs_employer');
    $sender_id      = array(array('user_id'=> $to_user, 'action'=>'request','status'=>'send'));
    $reciver_id     = array(array('user_id'=> $user_id, 'action'=>'request','status'=>'recive'));
    $all_requests   = array();
    $all_reciver_requests = array();
    $all_ids        = array();
    $all_reciver_ids= array();

    $user_havemeta       = $wpdb->get_results("SELECT * FROM wp_usermeta WHERE user_id = $user_id AND meta_key = 'requests'");
    $to_user_havemeta       = $wpdb->get_results("SELECT * FROM wp_usermeta WHERE user_id = $to_user AND meta_key = 'requests'");
    if ( !isset( $user_status ) || empty( $user_status )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $to_user_status ) || empty( $to_user_status )) {
        return new WP_Error( 'no_send_user_found', __('No Send User found'), array( 'status' => 400 ) );
    }
    if ( !in_array($user_status->roles[0], $rolesAllowed)) {
    	return new WP_Error( 'not_allowed', __('Your are not allowed to send the request'), array( 'status' => 400 ) );
    }
    if ( !in_array($to_user_status->roles[0], $rolesAllowed)) {
    	return new WP_Error( 'not_allowed', __('This user is not allowed for request '), array( 'status' => 400 ) );
    }
    if ( empty( $user_havemeta ) ) {
        add_user_meta( (int)$user_id, 'requests', $sender_id  );
        add_user_meta( (int)$user_id, 'send_requests', array($to_user)  );
        add_user_meta( (int)$user_id, 'recive_requests', ''  );
    }else{
        //return $reciver_id;
        $all_requests = get_usermeta( $user_id,'requests',true );
        if (empty($all_requests)) {
            update_user_meta( (int)$user_id , 'requests' ,$sender_id);
            update_user_meta( (int)$user_id, 'send_requests', array($to_user)  );

        }else{
            foreach ($all_requests as $key => $value) {
                $all_ids[$key] = $value['user_id'];
                if ( $all_ids[$key] == $to_user) {
                	$status = $value['status'];
                }
            }
            if (!in_array($to_user,$all_ids)) {
                $allSendReq       = get_user_meta ($user_id,'send_requests',true);
                if ( empty( $allSendReq) ) {
                    $sendReq = array($to_user);
                }else{
                    $sendReq          = array_merge($allSendReq,array($to_user));
                }
                $totalSendrequest = array_merge($all_requests,$sender_id);
                
                update_user_meta( (int)$user_id, 'requests', $totalSendrequest  );
                update_user_meta( (int)$user_id, 'send_requests', $sendReq  );

            }else{
            	if ( $status == 'send') {
			    	$message = 'You have already sent the connection request.';
				}else{
					$message = 'You have already received the connection request.';
				}
               return new WP_Error( $status.'_request', __($message), array( 'status' => 200 ) ); 
            }
        }
    }
    if ( empty( $to_user_havemeta ) ) {
        add_user_meta( (int)$to_user, 'requests', $reciver_id );
        add_user_meta( (int)$to_user, 'send_requests', '' );
        add_user_meta( (int)$to_user, 'recive_requests', array($user_id) );
    }else{
        $all_reciver_requests = get_usermeta( $to_user,'requests',true );
        if (empty($all_reciver_requests)) {
            update_user_meta($to_user , 'requests' ,$reciver_id);
            update_user_meta( (int)$to_user, 'recive_requests', array($user_id) );
        }else{
            foreach ($all_reciver_requests as $key => $value) {
                $all_reciver_ids[$key] = $value['user_id'];
                if ( $all_reciver_ids[$key] == $user_id) {
                	$status = $value['status'];
                }
            }
            if (!in_array($user_id,$all_reciver_ids)) {
                $allRecvReq       = get_user_meta ($to_user,'recive_requests',true);
                if ( empty( $allRecvReq) ) {
                    $recvReq = array($user_id);
                }else{
                    $recvReq = array_merge($allRecvReq,array($user_id));
                }
               $totalReciveRequests = array_merge($all_reciver_requests,$reciver_id);
               
               update_user_meta($to_user , 'requests' ,$totalReciveRequests);
               update_user_meta( (int)$to_user, 'recive_requests', $recvReq );
            }else{
            	if ( $status == 'send') {
			    	$message = 'You have already sent the connection request.';
				}else{
					$message = 'You have already received the connection request.';
				}
               return new WP_Error( $status.'_request', __($message), array( 'status' => 200 ) ); 
            }
        }
    }

    //notifications
    $message = $user_name.' sent you connection request.';
    $addNotificationIn  = $to_user;
    $addNotifierId      = $user_id;
    $action_parent      = '';
    $action_type        = 'user_profile';
    $notificationData   =  notification_data( $action_type,$addNotifierId );
    $tokens             = get_user_meta($to_user, 'device_token',true);
    if ( !empty($tokens)) {
        foreach ($tokens as $key => $token) {
            iospushnotification($token,$message,$action_type,$notificationData);
        }
    }
    send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id );
    //eo notifications
    $response = new WP_REST_Response(array('message'=> 'successfully! sent frient request','data'=>array('status'=> 201) ));
    return $response;

}
function action_on_request(){
    global $wpdb;
    $action             = $_POST['action'];
    $actions            = array('accept','reject','delete');
    $user_id            = $_POST['user_id'];
    $on_user            = $_POST['on_user'];
    $user_status        = get_user_by('id',(int)$user_id);
    $user_name          = $user_status->data->display_name;
    $on_user_status     = get_user_by('id',(int)$on_user);
    $user_havemeta      = get_usermeta( $user_id,'requests',true );
    $on_user_havemeta   = get_usermeta( $on_user,'requests',true );
    $findArr            = array();
    $recvArr            = array();
    $find_key           = '';

    if ( !isset( $action ) || empty( $action )) {
        return new WP_Error( 'no_action_found', __('No Action found'), array( 'status' => 400 ) );
    }else{
        if ( !in_array( $action, $actions)) {
            return new WP_Error( 'not_matching', __('action does not match'), array( 'status' => 400 ) );
        }
    }
    if ( !isset( $user_status ) || empty( $user_status )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $on_user_status ) || empty( $on_user_status )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    foreach ($user_havemeta as $key => $value) {
        $find_id = $value['user_id'];
        if ( $find_id == $on_user ) {
            $findArr['key']    = $key;
            $findArr['user_id']= $find_id;
            $findArr['action'] = $value['action'];
            $findArr['status'] = $value['status'];
        }   
    }
    foreach ($on_user_havemeta as $key => $value) {
        $find_id = $value['user_id'];
        if ( $find_id == $user_id ) {
            $recvArr['key']    = $key;
            $recvArr['user_id']= $find_id;
            $recvArr['action'] = $value['action'];
            $recvArr['status'] = $value['status'];
        }   
    }
    $user_action_table      = $findArr['status'].'_requests';
    $on_user_action_table   = $recvArr['status'].'_requests'; 
    if ( $action == 'delete') {
        $user_action_table      = $findArr['status'];
        $on_user_action_table   = $recvArr['status'];
    }
    $userData   = get_usermeta($user_id , $user_action_table , true);
    $onUserData = get_usermeta($on_user , $on_user_action_table , true);
    foreach ($userData as $key => $value) {
        if ( $value == $on_user ) {
           $findArr['tableKey'] = $key;
        }
    }
    foreach ($onUserData as $key => $value) {
        if ( $value == $user_id ) {
            $recvArr['tableKey'] = $key;
           
        }
    }
    if ( $action == 'delete') {
        if ( $findArr['action'] != 'active') {
            return new WP_Error( 'invalid_action', __('Action is not valid'), array( 'status' => 400 ) );
      } 
    }else{
      if ( $findArr['action'] != 'request') {
        return new WP_Error( 'invalid_action', __('Action is not valid'), array( 'status' => 400 ) );
      }  
    }
    
    if ( empty($findArr) ) {
        return new WP_Error( 'no_request_found', __('No request found'), array( 'status' => 400 ) );
    }
    if ( $action == 'reject' || $action == 'delete') {

        unset($userData[$findArr['tableKey']]);
        unset($onUserData[$recvArr['tableKey']]);
        unset($user_havemeta[$findArr['key']]);
        unset($on_user_havemeta[$recvArr['key']]);

        $user_havemeta          = array_values($user_havemeta);
        $on_user_havemeta       = array_values($on_user_havemeta);
        $userData               = array_values($userData);
        $onUserData             = array_values($onUserData);

        update_user_meta($user_id , 'requests' ,$user_havemeta);
        update_user_meta($on_user , 'requests' ,$on_user_havemeta);
        update_user_meta($user_id , $user_action_table ,$userData);
        update_user_meta($on_user , $on_user_action_table ,$onUserData);

        $response = new WP_REST_Response(array('message'=> 'successfully! '.$findArr['status'].' request deleted','data'=>array('status'=> 201) ));
        return $response;
    }
    if ( $action == 'accept') {
        if ( $findArr['status'] != 'recive') {
            return new WP_Error( 'invalid_action', __('Action is not valid'), array( 'status' => 400 ) );
        }
        $userFriendMeta       = $wpdb->get_results("SELECT * FROM wp_usermeta WHERE user_id = $user_id AND meta_key = 'friends'");
        $onUserFriendMeta     = $wpdb->get_results("SELECT * FROM wp_usermeta WHERE user_id = $on_user AND meta_key = 'friends'");

        $user_havemeta[$findArr['key']]['action']       = 'active';
        $user_havemeta[$findArr['key']]['status']       = 'friends';
        $on_user_havemeta[$recvArr['key']]['action']    = 'active';
        $on_user_havemeta[$recvArr['key']]['status']    = 'friends';

        unset($userData[$findArr['tableKey']]);
        unset($onUserData[$recvArr['tableKey']]);
        $userData               = array_values($userData);
        $onUserData             = array_values($onUserData);

        update_user_meta($user_id , 'requests' ,$user_havemeta);
        update_user_meta($on_user , 'requests' ,$on_user_havemeta);
        update_user_meta($user_id , $user_action_table ,$userData);
        update_user_meta($on_user , $on_user_action_table ,$onUserData);

        if ( empty($userFriendMeta) ) {
            add_user_meta( $user_id, 'friends', array($on_user) );
        }else{
            $userFrnd = get_user_meta ($user_id, 'friends' ,true);
            update_user_meta( $user_id, 'friends', array_merge($userFrnd,array($on_user)) );
        }

        if ( empty($onUserFriendMeta) ) {
            add_user_meta( $on_user, 'friends', array($user_id) );
        }else{
            $onUserFrnd = get_user_meta ($on_user, 'friends' ,true);
            update_user_meta( $on_user, 'friends', array_merge($onUserFrnd,array($user_id)) );
        }
        //notifications
        $message = $user_name.' accepted your connection request.';
        $addNotificationIn  = $on_user;
        $addNotifierId      = $user_id;
        $action_type        = 'user_profile';
        $action_parent      = '';
        $notificationData   =  notification_data( $action_type,$addNotifierId );
        $tokens  = get_user_meta($on_user, 'device_token',true);
        if ( !empty($tokens)) {
            foreach ($tokens as $key => $token) {
                iospushnotification($token,$message,$action_type,$notificationData);
            }
        }
        send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id );
        //eo notifications
        $response = new WP_REST_Response(array('message'=> 'Friend request accepted','data'=>array('status'=> 201) ));
        return $response;
    }

}// eo action on request
// users
function get_user_data($user_id){
    global $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path     = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $ID           = $user_id;
    $user         = get_user_by( 'id', $ID );
    $profile_data = array();
    $roles        = array("cs_candidate","cs_employer");

    $user_role = $user->roles;
    $user_role = $user_role[0];
    $ab_img    = get_usermeta( $ID, 'user_img' );
    // if ( !in_array($user_role, $roles)) {
    //     return true;
    // }
    if ($ab_img == '') {
        $ab_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
    }else{
        $ab_img = $imge_path.$ab_img;
    }

    $profile_data['ID']                     = $ID;
    $profile_data['user_login']             = $user->user_login;
    $profile_data['display_name']           = $user->display_name;
    $profile_data['user_email']      = $user->user_email;
    $profile_data['user_url']         = $user->user_url;
    $profile_data['wp_capabilities']  = get_usermeta( $ID,'wp_capabilities' );
    $profile_data['cs_job_title']     = get_usermeta( $ID,'cs_job_title' );
    $profile_data['cs_allow_search']  = get_usermeta( $ID,'cs_allow_search' );
    $cs_specialisms   				  = get_usermeta( $ID,'cs_specialisms' );
    $profile_data['cs_specialisms']   = implode (", ", $cs_specialisms);
    $profile_data['description']      = get_usermeta( $ID,'description');
    $profile_data['cs_facebook']      = get_usermeta( $ID,'cs_facebook' );
    $profile_data['cs_twitter']       = get_usermeta( $ID,'cs_twitter' );
    $profile_data['cs_google_plus']   = get_usermeta( $ID,'cs_google_plus' );
    $profile_data['cs_linkedin']      = get_usermeta( $ID,'cs_linkedin' );
    $profile_data['cs_phone_number']  = get_usermeta( $ID,'cs_phone_number' );
    $profile_data['user_img']         = $ab_img;
    $profile_data['role']             = $user->roles;
    if ( $user_role == "cs_candidate") {
        $profile_data['companyworkfor']  = get_usermeta( $ID,'companyworkfor' );
        $lookingfor      				 = get_usermeta( $ID,'lookingfor' );
        $profile_data['lookingfor']      = implode (", ", $lookingfor);
        $profile_data['Worklocation']    = get_usermeta( $ID,'Worklocation' );
        $profile_data['cs_candidate_cv'] = get_usermeta( $ID,'cs_candidate_cv' );
        $profile_data['cs_cover_letter'] = get_usermeta( $ID,'cs_cover_letter' );
    }
    if ( $user_role == "cs_employer") {
        $profile_data['type']            = get_usermeta( $ID,'type' );
    }
    return $profile_data;

}//eo get users
function all_userslist($request){
    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    $user_id            = $_POST['user_id'];
    $user_status        = get_user_by('id',(int)$user_id);
    $userArr            = array();
    $allusers           = array();
    $excludeUsers       = array();

    if ( !isset( $user_id ) || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $user_status ) || empty( $user_status )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    $userConnections    = get_user_meta ($user_id, 'requests' ,true);
    foreach ($userConnections as $key => $connections) {
    	$excludeUsers[$key] = $connections['user_id'];
    }

    $userArr['role__in']= array('cs_candidate','cs_employer');
    $userArr['number']  = $posts_per_page;
    $userArr['offset']  = (($page-1) * $posts_per_page);
    $userArr['exclude'] = $excludeUsers;

    $getUsers           = get_users( $userArr ); 
    foreach ($getUsers as $key => $user) {
        $user_id        = $user->data->ID;
        $allusers[$key] = get_user_data($user_id);
    }
    $response = new WP_REST_Response(array('message'=> 'All Users','data' => array('status' => 200,'params'=>$allusers)));
    return $response;
}
function get_request($request){
    global $wpdb;
    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    $user_id            = $_POST['user_id'];
    $request_type       = $_POST['request_type'];
    $type               = array('send' , 'recive', 'friends' );
    $user_status        = get_user_by('id',(int)$user_id);
    $post_data          = array();

    if ( !isset( $user_id ) || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $user_status ) || empty( $user_status )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    if ( !isset( $request_type ) || empty( $request_type )) {
        return new WP_Error( 'no_request_type_found', __('No Request Type found'), array( 'status' => 400 ) );
    }
    if ( !in_array($request_type,$type)) {
        return new WP_Error( 'invalid_type', __('invalid request type'), array( 'status' => 400 ) );
    }
    if ( $request_type == 'friends') {
        $totalRows = get_user_meta ($user_id, $request_type ,true);
    }else{
    	$totalRows = get_user_meta ($user_id, $request_type.'_requests' ,true);
    }

    if ( empty($totalRows)) {
        return new WP_Error( 'no_result_found', __('No request found.'), array( 'status' => 200 ) );
    }
    if ($page == '') {
        $page = 1;
    }
    if ($posts_per_page == '') {
        $posts_per_page = 50;
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);

    $total_records      = count($totalRows);
    $total_pages        = ceil($total_records / $posts_per_page);
    if ( $current_page_record > $total_records) {
        $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
        for($i = $offset; $i < $current_page_record; $i++) {
            $user_id        = $totalRows[$i];
            $post_data[$i] = get_user_data($user_id);
        }
    }else{
        return new WP_Error( 'no_request_found', __('No request found'), array( 'status' => 200 ) );
    }
    $post_data = array_values($post_data);
    $response = new WP_REST_Response(array('message'=> 'all jobs','data'=>array('status'=> 200,'params'=>$post_data) ));
    return $response;
}

function users_serach($request){
	global $wpdb;
    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    $user_id            = $_POST['user_id'];
    $keyword            = $_POST['keyword'];
    $user_status        = get_user_by('id',(int)$user_id);
    $post_data          = array();
    $excludeUsers       = array();
    $totalRows          = array();


    if ( !isset( $user_id ) || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $user_status ) || empty( $user_status )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    if ( !isset( $keyword ) || empty( $keyword )) {
        return new WP_Error( 'no_keyword_found', __('No keyword found'), array( 'status' => 400 ) );
    }
	$userConnections    = get_user_meta ($user_id, 'requests' ,true);
	$excludeUsers[$key] = '1';
    foreach ($userConnections as $key => $connections) {
    	$excludeUsers[$key] = $connections['user_id'];
    }
    $excl 		= implode (", ", $excludeUsers);
    $resultIds 	= $wpdb->get_results("SELECT ID FROM wp_users WHERE display_name LIKE '$keyword%' AND wp_users.id NOT IN ($excl)");
    foreach ($resultIds as $key => $ids) {
    	$totalRows[$key] = $ids->ID;
    }
    if ( empty($totalRows)) {
        return new WP_Error( 'no_result_found', __('No request found.'), array( 'status' => 200 ) );
    }
    if ($page == '') {
        $page = 1;
    }
    if ($posts_per_page == '') {
        $posts_per_page = 50;
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);

    $total_records      = count($totalRows);
    $total_pages        = ceil($total_records / $posts_per_page);
    if ( $current_page_record > $total_records) {
        $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
        for($i = $offset; $i < $current_page_record; $i++) {
            $user_id        = $totalRows[$i];
            $post_data[$i] = get_user_data($user_id);
        }
    }else{
        return new WP_Error( 'no_request_found', __('No request found'), array( 'status' => 200 ) );
    }
    $post_data = array_values($post_data);
    $response = new WP_REST_Response(array('message'=> 'all jobs','data'=>array('status'=> 200,'params'=>$post_data) ));
    return $response;
}
function connection_search($request){
global $wpdb;
    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    $user_id            = $_POST['user_id'];
    $search_type        = $_POST['search_type'];
    $type               = array('send','recive','friends' );
    $keyword            = $_POST['keyword'];
    $user_status        = get_user_by('id',(int)$user_id);
    $post_data          = array();
    $excludeUsers       = array();
    $totalRows          = array();


    if ( !isset( $user_id ) || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $user_status ) || empty( $user_status )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    if ( !isset( $keyword ) || empty( $keyword )) {
        return new WP_Error( 'no_keyword_found', __('No keyword found'), array( 'status' => 400 ) );
    }
    if ( !isset( $search_type ) || empty( $search_type )) {
        return new WP_Error( 'no_search_type_found', __('No Search Type found'), array( 'status' => 400 ) );
    }
    if ( !in_array($search_type, $type)) {
    	return new WP_Error( 'invalid_search', __('Invalid Search'), array( 'status' => 400 ) );
    }
    $meta_key = $search_type.'_requests';
    if ( $search_type == 'friends') {
    	$meta_key = $search_type;
    }
	$userConnections    = get_user_meta ($user_id, $meta_key ,true);
    foreach ($userConnections as $key => $connections) {
    	$excludeUsers[$key] = $connections;
    }
    $incl 		= implode (", ", $excludeUsers);
    $resultIds  = $wpdb->get_results("SELECT ID FROM wp_users WHERE wp_users.id IN ($incl) AND display_name LIKE '$keyword%'");

    foreach ($resultIds as $key => $ids) {
    	$totalRows[$key] = $ids->ID;
    }
    if ( empty($totalRows)) {
        return new WP_Error( 'no_result_found', __('No request found.'), array( 'status' => 200 ) );
    }
    if ($page == '') {
        $page = 1;
    }
    if ($posts_per_page == '') {
        $posts_per_page = 50;
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);

    $total_records      = count($totalRows);
    $total_pages        = ceil($total_records / $posts_per_page);
    if ( $current_page_record > $total_records) {
        $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
        for($i = $offset; $i < $current_page_record; $i++) {
            $user_id        = $totalRows[$i];
            $post_data[$i] = get_user_data($user_id);
        }
    }else{
        return new WP_Error( 'no_request_found', __('No request found'), array( 'status' => 200 ) );
    }
    $post_data = array_values($post_data);
    $response = new WP_REST_Response(array('message'=> 'all jobs','data'=>array('status'=> 200,'params'=>$post_data) ));
    return $response;
}
function comment_data($comment_id){
    global $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path  = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $comments   = array();
    $comment    = get_comment($comment_id);
    $image      = get_user_meta( $comment->user_id , 'user_img' );
    if ($image == '') {
        $img_vval = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
    }else{
        $img_vval = $imge_path.$image[0];
    }
    $comments['comment_ID']       = $comment->comment_ID ;
    $comments['comment_parent']   = $comment->comment_parent ;
    $comments['user_id']          = $comment->user_id;
    $comments['comment_author']   = $comment->comment_author;
    $comments['comment_content']  = $comment->comment_content;
    $comments['comment_approved'] = $comment->comment_approved;
    $comments['comment_date']     = $comment->comment_date;
    $comments['comment_status']   = wp_get_comment_status($comment->comment_ID);
    $comments['user_img']         = $img_vval;//get_avatar_url($comment->user_id, 32 );
    $comments['have_reply']       = has_comment_children_wpse($comment->comment_ID);
    $all_replies = get_comments( [ 'parent' => $comment->comment_ID ] );
    foreach ($all_replies as $key => $value) {
            $reply_id = $all_replies[$key]->comment_ID;
            $comment_reply    = get_comment($reply_id);
            $image      = get_user_meta( $comment_reply->user_id , 'user_img' );
            if ($image == '') {
                $img_vval = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
            }else{
                $img_vval = $imge_path.$image[0];
            } 
            //$all_replies = get_comments( [ 'parent' => $comment->comment_parent ] );
            $data[$key]->comment_ID       = $comment_reply->comment_ID ;
            $data[$key]->comment_parent   = $comment_reply->comment_parent ;
            $data[$key]->user_id          = $comment_reply->user_id;
            $data[$key]->comment_author   = $comment_reply->comment_author;
            $data[$key]->comment_content  = $comment_reply->comment_content;
            $data[$key]->comment_approved = $comment_reply->comment_approved;
            $data[$key]->comment_date     = $comment_reply->comment_date;
            $data[$key]->comment_status   = wp_get_comment_status($comment_reply->comment_ID);
            $data[$key]->user_img         = $img_vval;//get_avatar_url($comment->user_id, 32 );
            $data[$key]->have_reply       = has_comment_children_wpse($comment_reply->comment_ID);
        }
    $comments['data']       = $data;

    $comment_data =  array();
    $comment_data[0] = $comment->comment_post_ID;
    $comment_data[1] = $comments;
    // $data[1]->comments = $comments
    return $comment_data;
}
function single_post($post_id){
    global $wpdb;
    global $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path     = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $ID                     = $post_id;
    $answersId              = array();
    $post                   = get_post( $ID );
    $comments               = wp_count_comments( (int)$ID );
    $content                = str_replace("\"", "'", $post->post_content);
    $content                = str_replace(" />", ">", $content);
    $questionnaire_image    = get_usermeta( $post->post_author, 'user_img' );
    if ($questionnaire_image != '') {
        $questionnaire_image = $imge_path.$questionnaire_image;
    }else{
        $questionnaire_image = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
    }

    $post_parent_data     = $wpdb->get_results("SELECT post_parent FROM wp_posts WHERE ID =$ID");
    $answer_meta          = $wpdb->get_results( "SELECT * FROM wp_ap_qameta WHERE post_id=$ID" );
    $curr_votes           = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id=$ID AND vote_user_id = $user_id AND vote_type = 'vote'" );
    $curr_flags           = $wpdb->get_results ( "SELECT * FROM wp_ap_votes WHERE vote_post_id=$ID AND vote_user_id = $user_id AND vote_type = 'flag'" );
    if (!empty($curr_flags)) {
        $curr_flags = '1';
    }else{ $curr_flags = '';}

    $answersId['parent_id']   = $post_parent_data[0]->post_parent;
    $answersId['ID']          = $ID;
    $answersId['title']       = $post->post_title;
    $answersId['content']     = $content;
    $answersId['date']        = $post->post_date != null ? $post->post_date :'';
    $answersId['author']      = $post->post_author != null ? $post->post_author :'';
    $answersId['img']         = $questionnaire_image ;
    $answersId['currect_vote']= $curr_votes[0]->vote_value != null ? $curr_votes[0]->vote_value : '';
    $answersId['currect_flag']= $curr_flags;
    $answersId['views']       = $answer_meta[0]->views;
    $answersId['subscribers'] = $answer_meta[0]->subscribers;
    $answersId['answers']     = $answer_meta[0]->subscribers;
    $answersId['author_id']   = $answer_meta[0]->role;
    $answersId['author_name'] = get_usermeta((int)$post->post_author , 'display_name');
    $answersId['flags']       = $answer_meta[0]->flags!=''? $answer_meta[0]->flags:'0';
    $answersId['votes_up']    = $answer_meta[0]->votes_up!=''? $answer_meta[0]->votes_down:'0';
    $answersId['votes_down']  = $answer_meta[0]->votes_down!=''? $answer_meta[0]->votes_down:'0';
    $answersId['status']      = $post->post_status!= null ? $post->post_status :'';
    $answersId['comments']    = $comments->all;
    return $answersId;
}
function job_data($job_id){
    $ID                           = $job_id;
    $totalRows[$i]                = get_post($ID);
    $user_meta                    = get_userdata($totalRows[$i]->post_author);
    $user_role                    = $user_meta->roles;
    $user_name                    = $user_meta->user_login;
    $user_email                   = $user_meta->user_email;
    
    $application_closing_date     = get_post_meta($ID,'cs_application_closing_date');
    $experien                     = get_post_meta($ID , 'experiencetotal');
    $job_status                   = get_post_meta($ID , 'cs_job_status');
    $job_type                     = wp_get_post_terms( $ID, 'job_type' );
    $specialisms                  = wp_get_post_terms( $ID, 'specialisms' );
    $closing_date                 = $application_closing_date[0];
    $current_date                 = strtotime(current_time('d-m-Y H:i:s'));

    $check_date = true;
    if( $check_date ){
        foreach ($specialisms as $k=>$spec) {
        $specification[$k] = $spec->name;
        }
        $user_img = get_usermeta( $totalRows[$i]->post_author, $meta_key = 'user_img' );
        if (!$user_img) {
            $user_img =home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
        }else{
            $user_img =$imge_path.get_usermeta( $totalRows[$i]->post_author, $meta_key = 'user_img' );
        }
        //$post_data[$key]->test        = $user_meta;
        $post_data[0]->post_id     = (int)$ID;
        $post_data[0]->post_title  = $totalRows[$i]->post_title;
        $post_data[0]->author      = $totalRows[$i]->post_author;
        $post_data[0]->content     = strip_shortcodes($totalRows[$i]->post_content);
        $post_data[0]->user_name   = $user_name;
        $post_data[0]->user_email  = $user_email;
        $post_data[0]->post_date   = $totalRows[$i]->post_date;
        $post_data[0]->end_date    = date('d-m-Y', $application_closing_date[0]);
        $post_data[0]->user_experience  =  $experien[0];        
        $post_data[0]->specialisms = $specification; 
        $post_data[0]->job_type    = $job_type[0]->name;
        $post_data[0]->job_modified= $totalRows[$i]->post_modified; 
        $post_data[0]->job_posted_date = $totalRows[$i]->post_date;
        $post_data[0]->job_status  = $job_status[0];
        $post_data[0]->job_image   = $user_img;
        $post_data[0]->check_end_data   = $check_end_data;


        //jobs based on user
        $all_applicants = array();
        $all_shortlist  = array();
        $applicants = count_usermeta('cs-user-jobs-applied-list', serialize(strval($totalRows[$i]->ID)), 'LIKE', true);
        $shortlist  = count_usermeta('cs-user-jobs-wishlist', serialize(strval($totalRows[$i]->ID)), 'LIKE', true);
        foreach ($applicants as $ky => $applicant) {
            $all_applicants[$ky] = $applicant->data->ID;
        }
        foreach ($shortlist as $ky => $shorted) {
            $all_shortlist[$ky] = $shorted->data->ID;
        }
        // if(empty($all_applicants)){
        //     $all_applicants = false;
        // }
        
        $count_applicants    = count($all_applicants);
        if ( isset($author_id) || !empty($author_id)) {
            // if ($count_applicants > 0){
                foreach ($all_applicants as $k => $applicant_id) {
                    $applicant_user   = get_user_by( 'id', $applicant_id );
                    $user_image       = get_user_meta ($applicant_id,'user_img');
                    $job_applied_date = cs_find_other_field_user_meta_list($totalRows[$i]->ID, 'post_id', 'cs-user-jobs-applied-list', 'date_time', $applicant_id);
                    $posted_date = date('j F, Y', $job_applied_date);
                    if( $job_applied_date == false){
                        $posted_date = '';
                    }
                    if($user_image != ''){ 
                        $application_img = $imge_path.$user_image[0]; 
                    }else{ 
                        $application_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
                    }
                    $cs_candidate_cv_check = get_user_meta($applicant_id, 'cs_candidate_cv_' . $totalRows[$i]->ID . ' ', true);
                    if ($cs_candidate_cv_check != '') {
                    $cs_candidate_cv = get_user_meta($applicant_id, 'cs_candidate_cv_' . $totalRows[$i]->ID . ' ', true);
                    } else {
                    $cs_candidate_cv = get_user_meta($applicant_id, "cs_candidate_cv", true);
                    }

                    $cs_updated_cover_letter_check = get_user_meta($applicant_id, 'cs_updated_cover_letter_' . $totalRows[$i]->ID . ' ', true);
                    if ($cs_updated_cover_letter_check != '') {
                    $cs_updated_cover_letter= get_user_meta($applicant_id, 'cs_updated_cover_letter_' . $totalRows[$i]->ID . ' ', true);
                    } else {
                    $cs_updated_cover_letter = get_user_meta($applicant_id, "cs_updated_cover_letter_", true);
                    }

                    $post_data[0]->applicats_data[$k]->ID     = $applicant_id;
                    $post_data[0]->applicats_data[$k]->applicant_name   = $applicant_user->display_name;
                    $post_data[0]->applicats_data[$k]->job_applied_date = $posted_date;
                    $post_data[0]->applicats_data[$k]->applicant_image  = $application_img;
                    $post_data[0]->applicats_data[$k]->applicant_cv     = $cs_candidate_cv;
                    $post_data[0]->applicats_data[$k]->applicant_cover_letter = $cs_updated_cover_letter;

                }
            // }else{
            //     $post_data[$key]->applicats_data = $all_applicants;
            // }
            
        }
        $post_data[0]->job_applied = $all_applicants;
        $post_data[0]->job_shortlist = $all_shortlist;
        $post_data[0]->real_check = ($closing_date > $current_date);

    }// eo expire job condition
    return $post_data[0];
} 
function send_notifications($addNotificationIn,$addNotifierId,$message,$action_type,$action_parent,$user_id ){
	global $wpdb;

	$notifications          	    = array();
	$notifications['action_id'] 	= $addNotifierId;
    $notifications['user_id']       = $user_id;
    $notifications['action_parent'] = $action_parent;
	$notifications['action_type']	= $action_type;
	$notifications['time']      	= strtotime(current_time('mysql'));
	$notifications['message']   	= $message;

    
	$userNotificationMeta   		= $wpdb->get_results("SELECT * FROM wp_usermeta WHERE user_id = $addNotificationIn AND meta_key = 'notifications'");

	if ( empty( $userNotificationMeta)) {
		add_user_meta( $addNotificationIn, 'notifications', array($notifications) );
	}else{
		$storeNotifications = get_user_meta( $addNotificationIn, 'notifications', true);
		$notificationFeed   = array_merge($storeNotifications,array($notifications));
		update_user_meta($addNotificationIn , 'notifications' , $notificationFeed);
	}
	return true;
}
function single_user($user_id){

    global $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path     = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';
    $ID            = $user_id;
    $user          = get_user_by( 'id', $ID );
    $profile_data  = array();
    $roles         = array("cs_candidate","cs_employer");

    if (!isset($ID) || empty($ID)) {
     return new WP_Error('missing_params:', __('Username is not found'),array('status'=>400) );
    }
    if ($user == false) {
     return new WP_Error( 'Invalid_user_id:', __('Invalid user id'), array('status' => 401) );
    }
    $user_role = $user->roles;
    $user_role = $user_role[0];
    $ab_img    = get_usermeta( $ID, 'user_img' );
    if ( !in_array($user_role, $roles)) {
        return new WP_Error( 'Invalid_user_role:', __('not allowed'), array('status' => 401) );
    }
    if ($ab_img == '') {
        $ab_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
    }else{
        $ab_img = $imge_path.$ab_img;
    }

    $profile_data['ID']                    = $ID;
    // $profile_data['data']->user_login             = $user->user_login;
    // $profile_data['data']->display_name           = $user->display_name;
    // $profile_data['data']->meta->user_email       = $user->user_email;
    // $profile_data['data']->meta->user_url         = $user->user_url;
    // $profile_data['data']->meta->wp_capabilities  = get_usermeta( $ID,'wp_capabilities' ) != null ? get_usermeta( $ID,'wp_capabilities' ) : '';
    // $profile_data['data']->meta->cs_job_title     = get_usermeta( $ID,'cs_job_title' )!= null ? get_usermeta( $ID,'cs_job_title' ) : '';
    // $profile_data['data']->meta->cs_allow_search  = get_usermeta( $ID,'cs_allow_search' ) != null ? get_usermeta( $ID,'cs_allow_search' ): '';
    // $profile_data['data']->meta->cs_specialisms   = get_usermeta( $ID,'cs_specialisms' ) != null ? get_usermeta( $ID,'cs_specialisms' ): '';
    // $profile_data['data']->meta->description      = get_usermeta( $ID,'description') != null ? get_usermeta( $ID,'description'): '';
    // $profile_data['data']->meta->cs_facebook      = get_usermeta( $ID,'cs_facebook' ) != null ? get_usermeta( $ID,'cs_facebook' ): '';
    // $profile_data['data']->meta->cs_twitter       = get_usermeta( $ID,'cs_twitter' ) != null ? get_usermeta( $ID,'cs_twitter' ): '';
    // $profile_data['data']->meta->cs_google_plus   = get_usermeta( $ID,'cs_google_plus' ) != null ? get_usermeta( $ID,'cs_google_plus' ): '';
    // $profile_data['data']->meta->cs_linkedin      = get_usermeta( $ID,'cs_linkedin' ) != null ? get_usermeta( $ID,'cs_linkedin' ): '';
    // $profile_data['data']->meta->cs_phone_number  = get_usermeta( $ID,'cs_phone_number' ) != null ? get_usermeta( $ID,'cs_phone_number' ): '';
    // $profile_data['data']->meta->user_img         = $ab_img;
    // $profile_data['role']                         = $user->roles;
    // if ( $user_role == "cs_candidate") {
    //     $profile_data['data']->meta->companyworkfor  = get_usermeta( $ID,'companyworkfor' ) != null ? get_usermeta( $ID,'companyworkfor' ): '';
    //     $profile_data['data']->meta->lookingfor      = get_usermeta( $ID,'lookingfor' ) != null ? get_usermeta( $ID,'lookingfor' ): '';
    //     $profile_data['data']->meta->Worklocation    = get_usermeta( $ID,'Worklocation' ) != null ? get_usermeta( $ID,'Worklocation' ): '';
    //     $profile_data['data']->meta->cs_candidate_cv = get_usermeta( $ID,'cs_candidate_cv' ) != null ? get_usermeta( $ID,'cs_candidate_cv' ): '';
    //     $profile_data['data']->meta->cs_cover_letter = get_usermeta( $ID,'cs_cover_letter' ) != null ? get_usermeta( $ID,'cs_cover_letter' ): '';
    // }
    // if ( $user_role == "cs_employer") {
    //     $profile_data['data']->meta->type            = get_usermeta( $ID,'type' ) != null ? get_usermeta( $ID,'type' ) : '';
    // }
    return $ID;

}
function get_single_message($message_id){
    global $wpdb,$plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $totalRows          = $wpdb->get_results("SELECT * FROM wp_message_data WHERE message_id = $message_id");
    $image              = get_user_meta ($totalRows[0]->sender_id,'user_img');
    $user_image         = $image[0];
    $group_id           = $totalRows[0]->group_id;
    
    
    if ($user_image == '') {
        $ab_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
    }else{
        $ab_img = $imge_path.$user_image;
    }
    if( $group_id != 0 ){
        $post_data[0]->group_id    = $totalRows[0]->group_id; 
    }else{
        $post_data[0]->receiver_id     = $totalRows[0]->receiver_id;
    }

    $post_data[0]->message_id      = $totalRows[0]->message_id;
    $post_data[0]->sender_id       = $totalRows[0]->sender_id;
    $post_data[0]->sender_img      = $ab_img;
    $post_data[0]->date            = $totalRows[0]->date;
    $post_data[0]->message         = $totalRows[0]->message;
    $post_data[0]->upload_media    = $totalRows[0]->upload_media;
    $post_data[0]->message_type    = $totalRows[0]->message_type;
    $post_data[0]->status          = 1;
    return $post_data;
}
function notification_data( $action_type,$addNotifierId ){
    $data = array();
    if($action_type == 'answer'){
            $post = single_post( $addNotifierId );
            $data['question_id']            = $post['parent_id'];
            $data['answer_id']              = $post['ID'];
            $data['question_Title']         = $post['title'];
            $data['answer_content']         = $post['content'];
            $data['answer_date']            = $post['date'];
            $data['currect_vote']           = $post['currect_vote'];
            $data['currect_flag']           = $post['currect_flag'];
            $data['flags']                  = $post['currect_vote'];
            $data['answer_votes_up']        = $post['votes_up'];
            $data['answer_votes_down']      = $post['votes_down'];
            $data['answer_author']          = $post['author'];
            $data['author_name']            = $post['author_name'];
            $data['answer_author_img']      = $post['img'];
            $data['answer_author_name']     = $post['author_name'];
            $data['answer_status']          = $post['status'];
            $data['comments']               = $post['comments'];
        } 
        if($action_type == 'question'){
            $post = single_post( $addNotifierId );
            $data['question_id']            = (int)$post['ID'];
            $data['question_Title']         = $post['title'];
            $data['question_content']       = $post['content'];
            $data['question_Date']          = $post['date'];
            $data['currect_vote']           = $post['currect_vote'];
            $data['currect_flag']           = $post['currect_flag'];
            $data['question_views']         = $post['view'] != null ? $post['view'] : '';
            $data['question_subscribers']   = $post['subscribers'];
            $data['question_answers']       = $post['answers'];
            $data['question_flags']         = $post['currect_vote'];
            $data['question_votes_up']      = $post['votes_up'];
            $data['question_votes_down']    = $post['votes_down'];
            $data['questionnaire_id']       = $post['author'];
            $data['author_name']            = $post['author_name'];
            $data['author_image']           = $post['img'];
            $data['question_author_name']   = $post['author_name'];
        }
        // if ( $action_type  == 'user_profile') {
        //     $data = user_profile($addNotifierId);
        // }
        if ( $action_type  == 'job') {
            $data = job_data($addNotifierId);
        }
        if ( $action_type  == 'user_profile') {
            $data = single_user($addNotifierId);
        }
        if ( $action_type == 'comment' ) {
            $data = comment_data($addNotifierId);
        }
        if ( $action_type == 'message') {
            $data = get_single_message($addNotifierId);
        }
        return $data;
}   
function notifications($request){
    global $plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path     = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';

    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    $user_id            = $_POST['user_id'];
    $user_status        = get_user_by('id',(int)$user_id);
    $post_data          = array();

    if ( !isset( $user_id ) || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $user_status ) || empty( $user_status )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }

    $totalRows = get_user_meta($user_id , 'notifications' , true);

    if ( empty($totalRows)) {
        return new WP_Error( 'no_result_found', __('No request found.'), array( 'status' => 200 ) );
    }
    if ($page == '') {
        $page = 1;
    }
    if ($posts_per_page == '') {
        $posts_per_page = 50;
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);

    $total_records      = count($totalRows);
    $total_pages        = ceil($total_records / $posts_per_page);
    if ( $current_page_record > $total_records) {
        $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
        for($i = $offset; $i < $current_page_record; $i++) {
            $image      = get_usermeta( $totalRows[$i]['user_id'], $meta_key = 'user_img',true );
            if ($image == '') {
                $user_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
            }else{
                $user_img = $imge_path.$image;
            }
            $addNotifierId = $totalRows[$i]['action_id'];
            if ( $totalRows[$i]['action_type'] == 'comment' ) {
                $addNotifierId = $totalRows[$i]['action_parent'];
            }
            if ( $totalRows[$i]['action_type'] != '') {
                $data = notification_data( $totalRows[$i]['action_type'],$addNotifierId );
                $all_notifications[$i]->user_img = $user_img;
                $all_notifications[$i]->message  = $totalRows[$i]['message'];
                $all_notifications[$i]->type     = $totalRows[$i]['action_type'];
                $all_notifications[$i]->time     = date('d-m-Y H:i:s',$totalRows[$i]['time']);
                $all_notifications[$i]->actionUser= $totalRows[$i]['user_id'];
                if ( $totalRows[$i]['action_type'] == 'comment' ){
                    if( !empty($data[0]) ){
                         $all_notifications[$i]->post_id     = $data[0];
                    }
                     
                }
                
                if($totalRows[$i]['action_type'] == 'answer'){
                    $all_notifications[$i]->answer     = $data; 
                }
                if($totalRows[$i]['action_type'] == 'question'){
                    $all_notifications[$i]->question     = $data; 
                }
                if ( $totalRows[$i]['action_type']  == 'user_profile'){
                    $all_notifications[$i]->user_profile     = $data; 
                }
                if ( $totalRows[$i]['action_type']  == 'job'){
                    $all_notifications[$i]->job     = $data; 
                }
                // if ( $totalRows[$i]['action_type']  == 'user_profile'){
                //     $all_notifications[$i]->user_profile     = $data; 
                // }
                if ( $totalRows[$i]['action_type'] == 'comment' ){
                    if( !empty($data[1]) ){
                        $all_notifications[$i]->comment     = $data[1];
                    }
                     
                }
                if ( $totalRows[$i]['action_type'] == 'message'){
                    $all_notifications[$i]->message     = $data; 
                }
            }
            
        }
    }else{
        return new WP_Error( 'no_request_found', __('No request found'), array( 'status' => 200 ) );
    }
    $post_data = array_values($all_notifications);
    $response = new WP_REST_Response(array('message'=> 'all jobs','data'=>array('status'=> 200,'params'=>$post_data) ));
    return $response;
}
//push notifications
function iospushnotification($token,$msg,$type,$data) {

      $url = 'https://fcm.googleapis.com/fcm/send';
      $notification = [
          "sound" => 'Default',
          "body" => $msg,
          "type" => $type,
          "data"=>$data
      ];
      $fields = array(
          'to' => $token,
          'notification' => $notification,
          
      );
      $fields = json_encode($fields);
      $headers = array(
          'Authorization: key=' . "AAAA9fLAOv8:APA91bEaG2bL-DiKcKCCAd_lgcqnycr8wGelcpeijo1uf4Nq_2zwQgqGYqO_wzAKP-z78IpN7RslEYz_NL2YFD9g2xA4gt1aAJv4_rM4N8vpC9AidvxZ6kX1aAOl1sUBmEMthWx6vCOg",
          'Content-Type: application/json'
      );
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
      $result = curl_exec($ch);
      curl_close($ch);
      //print_r($result);die;
      return $result;
  }

function check_user($user_id){
    $userExist        = get_user_by('id',(int)$user_id);
    if ( !isset( $user_id ) || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $userExist ) || empty( $userExist )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    return true;
}
function check_chat_status($sender_id,$receiver_id){
    global $wpdb;
    $metaKey    = 'chat_'.$receiver_id;
    $result     = $wpdb->get_results("SELECT meta_value FROM wp_usermeta WHERE user_id = $sender_id and meta_key = '$metaKey'");
    $metaValue  = $result[0]->meta_value;
    if ( empty($result) ) {
        return true;
    }
    return $metaValue;

}
function check_group_chat_status($group_id,$user_id){
    global $wpdb;
    $metaKey    = 'group__'.$group_id;
    $result     = $wpdb->get_results("SELECT meta_value FROM wp_usermeta WHERE user_id = $user_id and meta_key = '$metaKey'");
    $metaValue  = $result[0]->meta_value;
    if ( empty($result) ) {
        $metaValue = 0;
    }
    return $metaValue;

}
// message api
function initiate_messaging(){
    global $wpdb;
    $sender_id      = $_POST['sender_id'];
    $receiver_id    = $_POST['receiver_id'];
    $chat_status    = $_POST['chat_status'];
    $s_key          = 'chat_'.$sender_id;
    $r_key          = 'chat_'.$receiver_id;
    $sendExist      = get_user_by('id',(int)$sender_id);
    $recvExist      = get_user_by('id',(int)$receiver_id);
    $sender_status  = check_chat_status((int)$sender_id,(int)$receiver_id);
    $receiver_status= check_chat_status((int)$receiver_id,(int)$sender_id); 
    $chatlist       = $wpdb->get_results("SELECT messaged_users FROM wp_message_record WHERE user = $sender_id ");

    if ( !isset( $sender_id )   || empty( $sender_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $sendExist )   || empty( $sendExist )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    if ( !isset( $receiver_id ) || empty( $receiver_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $recvExist )   || empty( $recvExist )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users.'), array( 'status' => 400 ) );
    }
    if ( !isset( $chat_status ) || $chat_status == '') {
        return new WP_Error( 'invalid_status', __('status is not valid.'), array( 'status' => 400 ) );
    }
    if ( $sender_status == true) {
        add_user_meta( $sender_id, $r_key , 1);
    }
    if( $sender_status != 1){
        update_user_meta( $sender_id, $r_key , 1);
    }
    if ( $receiver_status == true) {
        add_user_meta( $receiver_id, $s_key , 0);
    }
    if( $receiver_status != 1){
        update_user_meta( $receiver_id, $s_key , 0);
    }
    $sData  = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user = $sender_id");
    $rData  = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user = $receiver_id");

    if ( empty($sData)) {
        $wpdb->get_results("INSERT INTO wp_message_record( user, messaged_users, count) VALUES ($sender_id,$receiver_id,$chat_status)");
    }else{
        $msg_users = explode(',', $sData[0]->messaged_users);
        foreach ($msg_users as $key => $msg_user) {
            if ( $msg_user == $receiver_id) {
                return new WP_Error( 'already_initiate', __('you have already initiate'), array( 'status' => 400 ) );
            }
        }
        $sCount = $sData[0]->count;
        $mUsers = $sData[0]->messaged_users;
        $sCount = (int)$sCount + 1;
        if ( $mUsers == '' ) {
            $mUsers = $receiver_id;
        }else{
            $mUsers = $mUsers.','.$receiver_id;
        }
        $wpdb->get_results("UPDATE wp_message_record SET messaged_users = '$mUsers',count = $sCount WHERE user = $sender_id");
    }
    if ( empty($rData)) {
        $wpdb->get_results("INSERT INTO wp_message_record( user, messaged_users, count) VALUES ($receiver_id,$sender_id,$chat_status)");
    }else{
        $msg_users = explode(',', $rData[0]->messaged_users);
        foreach ($msg_users as $key => $msg_user) {
            if ( $msg_user == $sender_id) {
                return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
            }
        }
        $rCount = $rData[0]->count;
        $mUsers = $rData[0]->messaged_users;
        $rCount = (int)$rCount + 1;
        if ( $mUsers == '' ) {
            $mUsers = $sender_id;
        }else{
            $mUsers = $mUsers.','.$sender_id;
        }
        $wpdb->get_results("UPDATE wp_message_record SET messaged_users = '$mUsers',count = $rCount WHERE user = $receiver_id");
    }
    $response = new WP_REST_Response(array('message'=> 'users active for chat','data'=>array('status'=> 200,'params'=>$post_data) ));
    return $response;
    
}
function messaging(){
    global $wpdb;
    $sender_id      = $_POST['sender_id'];
    $receiver_id    = $_POST['receiver_id'];
    $sender_name    = get_usermeta( (int)$sender_id, 'display_name' );
    $message        = $_POST['message'];
    $content_img    = $_FILES['upload_media'];
    $group_id       = $_POST['group_id'];
    $status_type    = array(0, 1);
    $sendExist      = get_user_by('id',(int)$sender_id);
    $recvExist      = get_user_by('id',(int)$receiver_id);
    $action_type    = 'message';
    $uploaded_media = array();
    $action_parent  = '';
    if ( !isset( $sender_id )   || empty( $sender_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $sendExist )   || empty( $sendExist )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    if( !isset( $message ) && !isset( $uploaded_media )){
       return new WP_Error( 'no_update_found', __("No update found."), array( 'status' => 400 ) ); 
    }
    // if( empty( $message ) && empty() ){
    //    return new WP_Error( 'no_update_found', __("No update found."), array( 'status' => 400 ) ); 
    // }
    if ( ( isset($content_img) && !empty($content_img)) ) {
    	$count_images = count($content_img['name']);
	    for ($i=0; $i <$count_images ; $i++) { 
	        if ($content_img['size'][$i] != 0 && $content_img['error'][$i] == 0){
	            $f_name         = $content_img['name'][$i];
	            $f_path         = $content_img['tmp_name'][$i];
	            $img_url        = uplod_media($f_name,$f_path);
	            $uploaded_media[$i] = $img_url;
	        }
	    }
	    $all_media = implode(",", $uploaded_media);
    }
  
    
    if ( $group_id == '') {
    	if ( !isset( $receiver_id )   || empty( $receiver_id )) {
        	return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
	    }
	    if ( !isset( $recvExist )   || empty( $recvExist )) {
	        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
	    }

    	$status = check_chat_status($receiver_id,$sender_id);
	    if( !empty($all_media) ){
			$query = "INSERT INTO wp_message_data( sender_id, receiver_id, message,upload_media, status) VALUES ( $sender_id, $receiver_id,'$message','$all_media', $status)";
		}elseif( $message != ''){
			$query = "INSERT INTO wp_message_data( sender_id, receiver_id, message, status) VALUES ( $sender_id, $receiver_id,'$message', $status)";
        }else{
            return new WP_Error( 'no_param_found', __("atleast one parameter is required."), array( 'status' => 400 ) ); 
        }
		$wpdb->get_results($query);
        $insertRow = $wpdb->get_results("SELECT LAST_INSERT_ID()");
        $wpdb->get_results("UPDATE wp_message_data SET status= 1 WHERE receiver_id = $sender_id and sender_id IN ( $sender_id, $receiver_id) " );
        $Mid       = '';
        foreach ($insertRow[0] as $key => $value) {
            $Mid = (int)$value;  
        }
        $notificationData = notification_data( $action_type,$Mid );
        if( !empty($all_media) ){ $m = $sender_name." sent you a message";}else{ $m = $message;}
        $tokens           = get_user_meta($receiver_id, 'device_token',true);
        if ( !empty($tokens)) {
            foreach ($tokens as $key => $token) {
                //return $notificationData;
                iospushnotification($token,$m,$action_type,$notificationData);
            }
        }
    	
	}else{
		$group_data = $wpdb->get_results("SELECT * FROM wp_message_group WHERE Gid = $group_id");
    	if(empty($group_data[0]) ){
            return new WP_Error( 'no_group_found', __("No group found."), array( 'status' => 400 ) );
        }else{
        	$Gid            = $group_data[0]->Gid; 
            $group_members  = $group_data[0]->group_members; 
            $group_admin    = $group_data[0]->group_admin;
            $members        = explode( "," , $group_members );
    		$total_members  = array_unique( array_merge($members , array($group_admin)) );
            if( $message != '' ){
            	$query = "INSERT INTO wp_message_data( group_id, sender_id, receiver_id, message) VALUES ($Gid, $sender_id,0,'$message')";
            }elseif( !empty($all_media) ){
            	$query = "INSERT INTO wp_message_data( group_id, sender_id, message, upload_media) VALUES ($Gid, $sender_id,'$message','$all_media')";
            }else{
                return new WP_Error( 'no_param_found', __("atleast one parameter is required."), array( 'status' => 400 ) ); 
            }
        
	        $wpdb->get_results($query);
	        $insertRow = $wpdb->get_results("SELECT LAST_INSERT_ID()");
	        $Mid       = '';
	        foreach ($insertRow[0] as $key => $value) {
	        	$Mid = $value;	
	        }
	        foreach ($total_members as $key => $value) {
                $tokens           = get_user_meta($value, 'device_token',true);
		    	$status           = check_group_chat_status($group_id,$value);
                $notificationData = notification_data( $action_type,$Mid );
                if( !empty($all_media) ){ $m = $sender_name." sent a message";}else{ $m = $message;}
		    	if( $sender_id == $value ){
		    		$wpdb->get_results("INSERT INTO wp_group_status(group_id, mesage_id, user_id, status) VALUES ($Gid,$Mid,$value,1)");
		    	}else{
		    		if( $status == ''){ $status = 0 ;}
		    		$wpdb->get_results("INSERT INTO wp_group_status(group_id, mesage_id, user_id, status) VALUES ($Gid,$Mid,$value,$status)");
		    	}
                if ( !empty($tokens)) {
                    foreach ($tokens as $key => $token) {
                        iospushnotification($token,$m,$action_type,$notificationData);
                    }
                }
		    }// eo for
		}//else eo
	}//eo
    
    // $notificationData = $wpdb->get_results("SELECT * FROM wp_message_data ORDER BY message_id DESC LIMIT 1");
    // $action_type = 'message';

    // $tokens           = get_user_meta($receiver_id, 'device_token',true);
    // if ( !empty($tokens)) {
    //     foreach ($tokens as $key => $token) {
    //         iospushnotification($token,$message,$action_type,$notificationData);
    //     }
    // }
    $response = new WP_REST_Response(array('message'=> 'message delivered successfully!','data'=>array('status'=> 200) ));
    return $response;

}
function get_messages($request){
    global $wpdb ,$plugin_user_images_directory;
    $wp_upload_dir  = wp_upload_dir();
    $imge_path      = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';
    $page           = $request['pages'];
    $posts_per_page = $request['per_page'];
    $sender_id      = $_POST['user_id'];
    $receiver_id    = $_POST['chatted_user'];
    $group_id       = $_POST['group_id'];
    $sendExist      = get_user_by('id',(int)$sender_id);
    $recvExist      = get_user_by('id',(int)$receiver_id);

    if ( !isset( $sender_id )   || empty( $sender_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $sendExist )   || empty( $sendExist )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    if( $group_id != ''){

    	$totalRows = $wpdb->get_results("SELECT * FROM wp_message_data INNER JOIN wp_group_status ON wp_group_status.group_id = wp_message_data.group_id and wp_group_status.mesage_id = wp_message_data.message_id where user_id = $sender_id and wp_group_status.group_id = $group_id ORDER BY message_id DESC");
    }else{
    	if ( !isset( $receiver_id )   || empty( $receiver_id )) {
	        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
	    }
	    if ( !isset( $recvExist )   || empty( $recvExist )) {
	        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
	    }
	    $totalRows = $wpdb->get_results("SELECT * FROM wp_message_data WHERE sender_id IN ( $sender_id, $receiver_id) and receiver_id IN ( $sender_id, $receiver_id) and remove = ''or remove = $receiver_id ORDER BY message_id DESC");
    }

    if ( empty($totalRows)) {
       return new WP_Error( 'no_result_found', __('No request found.'), array( 'status' => 200 ) );
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);

    $total_records      = count($totalRows);
    $total_pages        = ceil($total_records / $posts_per_page);
    if ( $current_page_record > $total_records) {
       $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
       for($i = $offset; $i < $current_page_record; $i++) {
           	//$post_data[$i]        			= $totalRows[$i];
       		if( $group_id != ''){
       			$post_data[$i]->group_id	= $totalRows[$i]->group_id;	
       		}else{
       			$post_data[$i]->receiver_id	    = $totalRows[$i]->receiver_id;
       		}
            $image       = get_user_meta ($totalRows[$i]->sender_id,'user_img');
            $user_image  = $image[0];
            if ($user_image == '') {
                $ab_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
            }else{
                $ab_img = $imge_path.$user_image;
            }

           	$post_data[$i]->message_id		= $totalRows[$i]->message_id;
           	$post_data[$i]->sender_id		= $totalRows[$i]->sender_id;
            $post_data[$i]->sender_img      = $ab_img;
           	$post_data[$i]->date			= date('Y-m-d H:i:s',strtotime($totalRows[$i]->message_date));
           	$post_data[$i]->message			= $totalRows[$i]->message;
            $post_data[$i]->upload_media    = $totalRows[$i]->upload_media;
            $post_data[$i]->message_type    = $totalRows[$i]->message_type;
           	if ( $totalRows[$i]->sender_id 	== $sender_id) {
           		$post_data[$i]->status      = 1;
            }else{
           		$post_data[$i]->status		= $totalRows[$i]->status;
            }
           
       }
    }else{
       return new WP_Error( 'no_request_found', __('No request found'), array( 'status' => 200 ) );
    }
   $post_data = array_values($post_data);
   $response = new WP_REST_Response(array('message'=> 'all jobs','data'=>array('status'=> 200,'params'=>$post_data) ));
   return $response;

}
function get_chatlist( $request){
    global $wpdb ,$plugin_user_images_directory;
    $wp_upload_dir 	= wp_upload_dir();
    $imge_path     	= $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';
    $page           = $request['pages'];
    $posts_per_page = $request['per_page'];
    $user           = $_POST['user'];
    $userExist      = get_user_by('id',(int)$user);

    if ( !isset( $user )   || empty( $user )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $userExist )   || empty( $userExist )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    $data = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user = $user");
    $arr1  = explode(",",$data[0]->messaged_users);
    $arr2  = explode(",",$data[0]->group_id);
    $totalRows = array_merge($arr1,$arr2);
    if ( empty($arr1[0])) {
        $totalRows = $arr2;
    }
    if ( empty($arr2[0])) {
        $totalRows = $arr1;
    }
    if(empty( $totalRows[0] )){ return new WP_Error( 'no_result_found', __('No request found.'), array( 'status' => 200 ) );}
    foreach ($totalRows as $i => $totalRow){
        $get_user        = $totalRows[$i];
        $unread          = $wpdb->get_results("SELECT * FROM wp_message_data WHERE sender_id = $get_user and receiver_id  = $user and status = 0");
        $unreadCount     = count($unread);
        $userDetails     = get_user_by('id',(int)$get_user);
        $group_id        = (int) $last_msg[0]->group_id;
        $is_group        = in_array($get_user, $arr2);
        if ( !$is_group) {
            $last_msg        = $wpdb->get_results("SELECT * FROM wp_message_data WHERE sender_id IN ( $user, $get_user) and receiver_id IN ( $user, $get_user) ORDER BY message_id DESC LIMIT 1");
        	if( !empty($last_msg )){
            	$image       = get_usermeta( $get_user, $meta_key = 'user_img',true );
                if ($image == '') {
                    $p_data[$i]->user_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
                }else{
                    $p_data[$i]->user_img = $imge_path.$image;
                }
                $p_data[$i]->unreadCount  	= $unreadCount;
            	$p_data[$i]->user_name      = $userDetails->display_name != '' ? $userDetails->display_name : $userDetails->user_login;
            	$p_data[$i]->message_id 	= $last_msg[0]->message_id != null ? $last_msg[0]->message_id : '';
            	$p_data[$i]->user_id    	= $user;
            	$p_data[$i]->chatted_user  	= $get_user;
                $p_data[$i]->date 		 	= $last_msg[0]->message_date;
                $p_data[$i]->message 	 	= $last_msg[0]->message != null ? $last_msg[0]->message : '';
                $p_data[$i]->upload_media   = $last_msg[0]->upload_media;
                if ( $p_data[0]->sender_id == $user) {
                	$p_data[$i]->status   	= 1;
                }else{
                	$p_data[$i]->status   	= $last_msg[0]->status;
                }
            }
        }else{
        	$group_id = (int)$get_user;
            $last_msg        = $wpdb->get_results("SELECT * FROM wp_message_data WHERE group_id = $group_id ORDER BY message_id DESC LIMIT 1");
            	$group_details				= $wpdb->get_results("SELECT * FROM wp_message_group WHERE Gid = $group_id");

            	if ( $group_details[0]->sender_id != $user ) {
            		$unread = $wpdb->get_results("SELECT * FROM wp_group_status WHERE user_id = $user and group_id = $group_id and status = 0");
            		$unreadCount     = count($unread);
            		$p_data[$i]->unreadCount  	= $unreadCount;
            	}else{
            		$p_data[$i]->unreadCount  	= 0;
            	}
            	if ($group_details[0]->group_icon == '') {
                    $p_data[$i]->group_icon = home_url().'/wp-content/themes/jobcareer-child-theme/images/group_icon.png';
                }else{
                    $p_data[$i]->group_icon = $imge_path.$group_details[0]->group_icon;
                }
                $curr_group_members         = explode(',', $group_details[0]->group_members);
                foreach ($curr_group_members as $key => $value) {
                    $image       = get_user_meta ($value,'user_img');
                    $user_image  = $image[0];
                    if ($user_image == '') {
                        $ab_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
                    }else{
                        $ab_img = $imge_path.$user_image;
                    }
                    $name_login = get_usermeta( $value, 'display_name' );
                    $memeber_data = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = $value ");
                    $p_data[$i]->group_members[$key]->ID   = $value;
                    $p_data[$i]->group_members[$key]->user_login = $memeber_data[0]->display_name != '' ? $memeber_data[0]->display_name : $memeber_data[0]->user_login;
                    $p_data[$i]->group_members[$key]->user_img  = $ab_img;


                }
                $p_data[$i]->group_name     = $group_details[0]->group_name;
                $p_data[$i]->message_id 	= $last_msg[0]->message_id != null ? $last_msg[0]->message_id : '' ;
                $p_data[$i]->group_id    	= $group_details[0]->Gid;
                $p_data[$i]->group_admin  	= $group_details[0]->group_admin;
                //$p_data[$i]->group_members	= $group_details[0]->group_members;
                $p_data[$i]->date	        = $group_details[0]->created_date;
                $p_data[$i]->message 	 	= $last_msg[0]->message ;
                $p_data[$i]->upload_media   = $last_msg[0]->upload_media == '' ? $last_msg[0]->upload_media : $imge_path.$last_msg[0]->upload_media ;
        }
    }//eo


    $totalRows = $p_data;
    if ( empty($totalRows)) {
       return new WP_Error( 'no_result_found', __('No request found.'), array( 'status' => 200 ) );
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);

    $total_records      = count($totalRows);
    $total_pages        = ceil($total_records / $posts_per_page);
    if ( $current_page_record > $total_records) {
       $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
       for($i = $offset; $i < $current_page_record; $i++) {
            $post_data[$i] = $totalRows[$i];
       }
    }else{
       return new WP_Error( 'no_request_found', __('No request found'), array( 'status' => 200 ) );
    }
    $post_data = array_values($post_data);
    if ( $post_data == null) {
   		return new WP_Error( 'no_request_found', __('No request found'), array( 'status' => 200 ) );
    }
    $response = new WP_REST_Response(array('message'=> 'all jobs','data'=>array('status'=> 200,'params'=>$post_data) ));
    return $response;
}
function update_user_availability(){
    global $wpdb;
	$sender_id      = $_POST['user_id'];
    $receiver_id    = $_POST['chatted_user'];
    $group_id       = $_POST['group_id'];
    $status    		= $_POST['status'];
    $sendExist      = get_user_by('id',(int)$sender_id);
    $recvExist      = get_user_by('id',(int)$receiver_id);

    if ( !isset( $sender_id )   || empty( $sender_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $sendExist )   || empty( $sendExist )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    if ( $group_id == '') {
    	if ( !isset( $receiver_id )   || empty( $receiver_id )) {
	        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
	    }
	    if ( !isset( $recvExist )   || empty( $recvExist )) {
	        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
	    }
	    if ( !isset( $status )   || $status == '') {
        	return new WP_Error( 'no_status', __('status not found.'), array( 'status' => 400 ) );
	    }
	    $r_key          = 'chat_'.$receiver_id;
	    $status         = (int)$status;
	    update_user_meta( $sender_id, $r_key , $status);

	    if ( $status == 1) {
	    	$wpdb->get_results("UPDATE wp_message_data SET status= 1 WHERE receiver_id = $sender_id and sender_id = $reciver_id");
	    }
    }else{

    	$group_data = $wpdb->get_results("SELECT * FROM wp_message_group WHERE Gid = $group_id");
    	if(empty($group_data[0]) ){
            return new WP_Error( 'no_group_found', __("No group found."), array( 'status' => 400 ) );
        }else{
    		$meta_key       = 'group__'.$group_id;
    		$status         = (int)$status;
	    	update_user_meta( $sender_id, $meta_key , $status);
	    	if ( $status == 1) {
		    	$wpdb->get_results("UPDATE wp_group_status SET status=1 WHERE group_id= $group_id and user_id = $sender_id");
		    }
    	}
    }
    
    $response = new WP_REST_Response(array('message'=> 'status updated!','data'=>array('status'=> 200) ));
    return $response;
}
function delete_chat(){
	global $wpdb;
	$sender_id      = $_POST['user_id'];
    $receiver_id    = $_POST['chatted_user'];
    $status    		= $_POST['status'];
    $sendExist      = get_user_by('id',(int)$sender_id);
    $recvExist      = get_user_by('id',(int)$receiver_id);
    $chatlist       = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user = $sender_id ");
    $messaged_users = $chatlist[0]->messaged_users;
    $list           =  explode(",", $chatlist[0]->messaged_users);

    if ( !isset( $sender_id )   || empty( $sender_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $sendExist )   || empty( $sendExist )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    if ( !isset( $receiver_id )   || empty( $receiver_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $recvExist )   || empty( $recvExist )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }

    if ( !in_array($receiver_id , $list)) {
       return new WP_Error( 'invalid_user', __('this user is not found in chatlist'), array( 'status' => 400 ) );
    }

    $wpdb->get_results("UPDATE wp_message_data SET remove = $sender_id WHERE sender_id IN ($sender_id,$receiver_id) and receiver_id IN ($sender_id,$receiver_id) and remove = '' ");
    $wpdb->get_results("DELETE FROM wp_message_data WHERE sender_id IN ($sender_id,$receiver_id) and receiver_id IN ($sender_id,$receiver_id) and remove = $receiver_id");
    
    // $list = array_unique( array_diff($list, array($receiver_id) ));
    // $all_list  = implode(",", $list);
    // $count = (int)$chatlist[0]->count - 1;
    // $wpdb->get_results("UPDATE wp_message_record SET messaged_users = '$all_list',count = $count WHERE user = $sender_id");
    $response = new WP_REST_Response(array('message'=> 'chat deleted successfully!','data'=>array('status'=> 200) ));
    return $response;

}
function exit_group(){
	global $wpdb;
	$user_id        = $_POST['user_id'];
	$group_id       = $_POST['group_id'];
    $user_name      = get_usermeta((int)$user_id , 'display_name');
    $action_type    = 'message';

	if ( !isset( $user_id )   || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $group_id )   || empty( $group_id )) {
        return new WP_Error( 'no_group_id_found', __('Group id not found.'), array( 'status' => 400 ) );
    }

    $group_details = $wpdb->get_results("SELECT * FROM wp_message_group WHERE Gid = $group_id");
    $group_members = $group_details[0]->group_members;
    $group_admin   = $group_details[0]->group_admin;
    $memebers 	   = explode(",", $group_members);
    $list          = array_unique(array_merge($memebers,array($group_admin)));
    if ( !in_array($user_id, $list)) {
        return new WP_Error( 'no_a_memeber', __('This user is not the memeber of this group.'), array( 'status' => 400 ) );
    }
    $flag          = false;
    if (!empty($group_details)) {
    	$group_admin  = $group_details[0]->group_admin;
    	if ( $group_admin != $user_id ) {
    		$user_record = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user = $user_id");
		   	$Gid   = explode(",", $user_record[0]->group_id);
		   	
    		foreach ($list as $key => $value) {
		        if ( $value == $user_id ) {
		    		unset($list[$key]);
		    	}
		   	}
	       	foreach ($Gid as $key => $value) {
	        	if ( $value == $group_id ) {
		    		unset($Gid[$key]);
		    	}
	       	}
	       	$group_record  = array_values($Gid);
            $G_rec         = implode(',', $group_record);
		   	$member_record = array_values($list);
            $M_rec         = implode(',', $member_record);
            $message       = $user_name.' left the group.';
		   	$wpdb->get_results("UPDATE wp_message_group SET group_members = '$M_rec' where Gid = $group_id");
		   	$wpdb->get_results("UPDATE wp_message_record SET group_id = '$G_rec' WHERE user = $user_id");
            $wpdb->get_results("DELETE FROM wp_group_status WHERE group_id= $group_id and user_id=$user_id");
            $wpdb->get_results("INSERT INTO wp_message_data( group_id, sender_id, receiver_id, message,message_type) VALUES ($group_id, $user_id,0,'$message','notification')");
            $insertRow = $wpdb->get_results("SELECT LAST_INSERT_ID()");
            $Mid       = '';
            foreach ($insertRow[0] as $key => $value) {
                $Mid = $value;  
            }
            foreach ($list as $key => $value) {
                $status           = check_group_chat_status($group_id,$value);
                $tokens           = get_user_meta($value, 'device_token',true);
                $notificationData = notification_data( $action_type,$Mid );
                if( $user_id == $value ){
                    $wpdb->get_results("INSERT INTO wp_group_status(group_id, mesage_id, user_id, status) VALUES ($group_id,$Mid,$value,1)");
                }else{
                    if( $status == ''){ $status = 0 ;}
                    $wpdb->get_results("INSERT INTO wp_group_status(group_id, mesage_id, user_id, status) VALUES ($group_id,$Mid,$value,$status)");
                }

                if ( !empty($tokens)) {
                    foreach ($tokens as $key => $token) {
                        iospushnotification($token,$message,$action_type,$notificationData);
                    }
                }
            }// eo 
            $response = new WP_REST_Response(array('message'=> 'successfully Exit The Group.','data'=>array('status'=> 200)  )); 
            return $response;

    	}else{
		    foreach ($list as $key => $value) {
               $curr_user = $value;
		       $user_record = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user = $value");
		       $Gid   = explode(",", $user_record[0]->group_id);
		       foreach ($Gid as $key => $value) {
		        	if ( $value == $group_id ) {
			    		unset($Gid[$key]);
			    	}
		       }
		       $group_record = array_values($Gid);
               $G_rec         = implode(',', $group_record);
		       $wpdb->get_results("UPDATE wp_message_record SET group_id = '$G_rec' WHERE user = $curr_user");
		   	}
    		$wpdb->get_results("DELETE FROM wp_message_group WHERE Gid = $group_id");
            $wpdb->get_results("DELETE FROM wp_message_data WHERE group_id = $group_id");
            $wpdb->get_results("DELETE FROM wp_group_status WHERE group_id = $group_id");
    		$response = new WP_REST_Response(array('message'=> 'successfully Group deletd.','data'=>array('status'=> 200)  )); 
            return $response;
    	}

    }else{
    	return new WP_Error( 'no_group_found', __('No such Group found.'), array( 'status' => 400 ) );	
    }

}
function upadte_delete_members(){
    global $wpdb;
    $admin_user     = $_POST['admin_user'];
    $admin_name     = get_usermeta((int)$admin_user , 'display_name');
    $user_id        = $_POST['user_id'];
    $group_id       = $_POST['group_id'];
    $action         = $_POST['action'];
    $valid_action   = array('update','delete' );
    $user_name      = get_usermeta((int)$user_id , 'display_name');
    $group_details  = $wpdb->get_results("SELECT * FROM wp_message_group WHERE Gid = $group_id");
    $group_members  = $group_details[0]->group_members;
    $group_admin    = $group_details[0]->group_admin;
    $users          = explode(",", $user_id);
    $memebers       = explode(",", $group_members);
    $list           = array_unique(array_merge($memebers,array($group_admin)));

    if ( !isset( $user_id )   || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $admin_user )   || empty( $admin_user )) {
        return new WP_Error( 'no_admin_found', __('No Admin found'), array( 'status' => 400 ) );
    }
    if ( !isset( $group_id )   || empty( $group_id )) {
        return new WP_Error( 'no_group_id_found', __('Group id not found.'), array( 'status' => 400 ) );
    }
    if ( !isset( $group_id )   || empty( $group_id ) ) {
        return new WP_Error( 'pass_action', __('Action not found.'), array( 'status' => 400 ) );
    }
    if ( !in_array($action, $valid_action)) {
        return new WP_Error( 'not_valid_action', __('This is not a valid action.'),array( 'status'=> 400 ) );
    }
    if (empty($group_details)) {
        return new WP_Error( 'no_group_found', __('No such Group found.'), array( 'status' => 400 ) ); 
    }
    if ( $group_admin != $admin_user) {
        return new WP_Error( 'user_is_not_a_admin', __('Only Admin have a authority to add a group memeber.'), array( 'status' => 400 ) );
    }
    if ( in_array($group_admin, $users)) {
        return new WP_Error( "admin_can't_in ", __('Admin can not be include in this action'), array( 'status' => 200 ) );
    }
    if ( $action == 'update') {
        $all_users          =  array_unique(array_merge($memebers,$users));
        foreach ($users as $key => $value) {
            $user_id            = $value;
            $user_name          = get_usermeta((int)$user_id , 'display_name');
            $current_user_data  = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user =$user_id");

                $user_group     = $current_user_data[0]->group_id;
                $user_count     = $current_user_data[0]->count;
                $count          = (int)$user_count +1;
                if ( $user_group == '') {
                    $all_groups = $group_id;
                }else{
                    $current_groups = explode(',', $user_group);
                    $G_array        = array_unique(array_merge($current_groups,array($group_id)));
                    $all_groups     = implode(',', $G_array);
                }
                $message            = $admin_name.' added '.$user_name;
                $mGroup             = array_unique(array_merge($memebers , array($user_id) ));
                $memebers_in_group  = array_unique(array_merge($mGroup , array($admin_user) ));
                $group_members_now  = implode(",", $mGroup);
                $wpdb->get_results("UPDATE wp_message_group SET group_members='$group_members_now' WHERE Gid = $group_id");
                if (empty($current_user_data)) {
                    $wpdb->get_results("INSERT INTO wp_message_record( user, group_id, count) VALUES ($user_id,'$group_id',1)");
                }else{
                    $wpdb->get_results("UPDATE wp_message_record SET group_id = '$all_groups',count = $count WHERE user = $user_id");
                }
                
                $wpdb->get_results("INSERT INTO wp_message_data( group_id, sender_id, receiver_id, message,message_type) VALUES ($group_id, $user_id,0,'$message','notification')");
                $insertRow = $wpdb->get_results("SELECT LAST_INSERT_ID()");
                $Mid       = '';
                foreach ($insertRow[0] as $key => $value) {
                    $Mid   = $value;  
                }
                foreach ($memebers_in_group as $key => $value) {
                    $status = check_group_chat_status($group_id,$value);
                    if( $admin_user == $value ){
                        $wpdb->get_results("INSERT INTO wp_group_status(group_id, mesage_id, user_id, status) VALUES ($group_id,$Mid,$value,1)");
                    }else{
                        if( $status == ''){ $status = 0 ;}
                        $wpdb->get_results("INSERT INTO wp_group_status(group_id, mesage_id, user_id, status) VALUES ($group_id,$Mid,$value,$status)");
                    }
                    $tokens           = get_user_meta($value, 'device_token',true);
                    $action_type      = 'message';
                    $notificationData = notification_data( $action_type,$Mid );

                    if ( !empty($tokens)) {
                        foreach ($tokens as $key => $token) {
                            iospushnotification($token,$message,$action_type,$notificationData);
                        }
                    }
                }// eo for

            
            $memebers = $mGroup;
        }
        $response = new WP_REST_Response(array('message'=> 'Group member is Successfully addeded.','data'=>array('status'=> 200)  )); 
            return $response;
    }
    if ( $action == 'delete') {
        $all_users = array_unique(array_diff($memebers,$users));
        foreach ($users as $key => $value) {
            $user_id            = $value;
            $user_name          = get_usermeta((int)$user_id , 'display_name');
            $current_user_data  = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user =$user_id");
            $user_group         = $current_user_data[0]->group_id;
            $user_count         = $current_user_data[0]->count;
            $count              = (int)$user_count - 1;
            $current_groups     = explode(',', $user_group);
            foreach ($current_groups as $key => $value) {
                if ( $value == $group_id) {
                    unset($current_groups[$key]);
                }
            }
            $message            = $admin_name.' removed '.$user_name;
            $all_groups         = implode(',', $current_groups);
            $mGroup             = array_unique(array_diff($memebers , array($user_id) ));
            $memebers_in_group  = array_unique(array_merge($mGroup , array($admin_user) ));
            $group_members_now  = implode(",", $mGroup);
            $gM                 = implode(",", $mGroup );
            $wpdb->get_results("UPDATE wp_message_group SET group_members='$gM' WHERE Gid = $group_id");
            $wpdb->get_results("UPDATE wp_message_record SET group_id = '$all_groups',count = $count WHERE user = $user_id");
            $wpdb->get_results("INSERT INTO wp_message_data( group_id, sender_id, receiver_id, message,message_type) VALUES ($group_id, $user_id,0,'$message','notification')");
            $insertRow = $wpdb->get_results("SELECT LAST_INSERT_ID()");
            $Mid       = '';
            foreach ($insertRow[0] as $key => $value) {
                $Mid   = $value;  
            }
            foreach ($memebers_in_group as $key => $value) {
                $status = check_group_chat_status($group_id,$value);
                if( $admin_user == $value ){
                    $wpdb->get_results("INSERT INTO wp_group_status(group_id, mesage_id, user_id, status) VALUES ($group_id,$Mid,$value,1)");
                }else{
                    if( $status == ''){ $status = 0 ;}
                    $wpdb->get_results("INSERT INTO wp_group_status(group_id, mesage_id, user_id, status) VALUES ($group_id,$Mid,$value,$status)");
                }
                $tokens           = get_user_meta($value, 'device_token',true);
                $action_type      = 'message';
                $notificationData = notification_data( $action_type,$Mid );

                if ( !empty($tokens)) {
                    foreach ($tokens as $key => $token) {
                        iospushnotification($token,$message,$action_type,$notificationData);
                    }
                }
            }// eo for
            $memebers = $mGroup;
        }
        $response = new WP_REST_Response(array('message'=> 'Group member is Successfully deleted.','data'=>array('status'=> 200)  )); 
            return $response;
    }  
}
function replace_and_delete_old_admin(){
    global $wpdb;
    $admin_user     = $_POST['admin_user'];
    $user_id        = $_POST['user_id'];
    $group_id       = $_POST['group_id'];
    $user_name      = get_usermeta((int)$user_id , 'display_name');
    $group_details = $wpdb->get_results("SELECT * FROM wp_message_group WHERE Gid = $group_id");
    $group_members = $group_details[0]->group_members;
    $group_admin   = $group_details[0]->group_admin;
    $memebers      = explode(",", $group_members);
    $list          = array_unique(array_merge($memebers,array($group_admin)));

    if ( !isset( $user_id )   || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $admin_user )   || empty( $admin_user )) {
        return new WP_Error( 'no_admin_found', __('No Admin found'), array( 'status' => 400 ) );
    }
    if ( !isset( $group_id )   || empty( $group_id )) {
        return new WP_Error( 'no_group_id_found', __('Group id not found.'), array( 'status' => 400 ) );
    }
    if ( $group_admin != $admin_user) {
            return new WP_Error( 'not_a_admin', __('This admin user is not the admin of this group.'), array( 'status' => 200 ) );
        }

    if ( !in_array($user_id, $memebers)) {
        return new WP_Error( 'not_a_group_member', __('The Use should blongs to this group.'), array( 'status' => 200 ) );
    }
    if (!empty($group_details)) {
        foreach ($memebers as $key => $value) {
            if ( $value == $user_id ) {
                unset($memebers[$key]);
            }
        }
        $all_group_members     = implode(',', $memebers);

        $prev_user_data = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user = $group_admin");
        $current_user_data = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user = $user_id");
        if ( $user_id == $group_admin ) {
            return new WP_Error( 'select_new_member', __('Select the group member not admin'), array( 'status' => 200 ) );
        } 
        $prev_user_group = $prev_user_data[0]->group_id;
        $prev_user_count = $prev_user_data[0]->count;
        $count      = $prev_user_count - 1;
        $current_groups = explode(',', $prev_user_group);
        foreach ($current_groups as $key => $value) {
            if ( $value == $group_id ) {
                unset($current_groups[$key]);
            }
        }
        $prev_all_groups     = implode(',', $current_groups);

        if (empty($current_user_data)) {
            $wpdb->get_results("INSERT INTO wp_message_record( user, group_id, count) VALUES ($user_id,'$group_id',1)");
        }else{
            $user_group = $current_user_data[0]->group_id;
            $user_count = $current_user_data[0]->count;
            $count      = $user_count++;
            if ( $user_group == '') {
                $all_groups = $group_id;
            }else{
                $current_groups = explode(',', $user_group);
                $G_array        = array_unique(array_merge($current_groups,array($group_id)));
                $all_groups     = implode(',', $G_array);
            }
            $wpdb->get_results("UPDATE wp_message_record SET group_id = '$all_groups',count = $count WHERE user = $user_id");
        }
        $message = $user_name.' is the new admin of the group.';
        $wpdb->get_results("INSERT INTO wp_message_data( group_id, sender_id, receiver_id, message,message_type) VALUES ($group_id, $user_id,0,'$message','notification')");
        foreach ($list as $key => $value) {
            $status = check_group_chat_status($group_id,$value);
            if( $group_admin != $value ){
                if( $status == ''){ $status = 0 ;}
                $wpdb->get_results("INSERT INTO wp_group_status(group_id, mesage_id, user_id, status) VALUES ($group_id,$message,$value,$status)");
                $tokens           = get_user_meta($value, 'device_token',true);
                $action_type      = 'message';
                $notificationData = notification_data( $action_type,$Mid );

                if ( !empty($tokens)) {
                    foreach ($tokens as $key => $token) {
                        iospushnotification($token,$message,$action_type,$notificationData);
                    }
                }
            }
        }// eo for
        $wpdb->get_results("UPDATE wp_message_record SET group_id = '$prev_all_groups',count = $count WHERE user = $group_admin");
        $wpdb->get_results("UPDATE wp_message_group SET group_admin = $user_id , group_members = '$all_group_members' WHERE Gid = $group_id");
        $response = new WP_REST_Response(array('message'=> 'Group Admin changed ans you are no longer  belongs to this group.','data'=>array('status'=> 200)  )); 
            return $response;
    }
    return new WP_Error( 'no_group_found', __('No such Group found.'), array( 'status' => 400 ) ); 
}
function message_media(){
    $message_image = $_FILES['media_upload'];
    if ($message_image == null) {
        return new WP_Error( 'no_data', __('No data found.'), array( 'status' => 400 ) );
    }
    $f_name = $_FILES['media_upload']['name'];
    $f_path = $_FILES['media_upload']['tmp_name'];
    $cv_url = uplod_media($f_name,$f_path);
    $response = new WP_REST_Response(array('message'=> 'Uploaded image url.','data'=>array('status'=> 200,'params'=>array('image_url'=>$cv_url))  ));
    return $response;
}

// group API
function change_group_icon(){
    global $wpdb ,$plugin_user_images_directory;
    $wp_upload_dir = wp_upload_dir();
    $imge_path     = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';
    $user_id        = $_POST['user_id'];
    $user_name      = get_usermeta((int)$user_id , 'display_name');
    $group_id       = $_POST['group_id'];
    $group_icon     = $_FILES['group_profile'];
    $group_name     = $_POST['group_name'];
    $group_details  = $wpdb->get_results("SELECT * FROM wp_message_group WHERE Gid = $group_id");
    $group_members  = $group_details[0]->group_members;
    $group_admin    = $group_details[0]->group_admin;
    $memebers       = explode(",", $group_members);
    $list           = array_unique(array_merge($memebers,array($group_admin)));
    $arr            = array();

    if ( !isset( $user_id )   || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( empty($group_details) ) {
        return new WP_Error( 'no_group_found', __('No Such Group found.'), array( 'status' => 400 ) );
    }
    if (!isset( $group_icon ) &&  !isset( $group_name ) ) {
        return new WP_Error( 'update_not_found', __('No update found.'), array( 'status' => 200 ) );
    }
    if ( !in_array($user_id, $list)) {
        return new WP_Error( 'not_a_member', __('Only group members can change the group profile.'), array( 'status' => 200 ) );
    }

    if ($_FILES['group_profile']['size'] != 0 && $_FILES['group_profile']['error'] == 0) {
        $f_name             = $_FILES['group_profile']['name'];
        $f_path             = $_FILES['group_profile']['tmp_name'];
        $icon               = user_profile_pic($f_name,$f_path,$user_id);
        $arr['group_icon']  = $imge_path.$icon;
    }
    if ($group_name != '' ) {
         $arr['group_name'] = $group_name;
    }
    if ( !empty($arr)) {
        if ($arr['group_icon'] != '') {
            $wpdb->get_results("UPDATE wp_message_group SET group_icon='$icon' WHERE Gid=$group_id ");
            $message =$user_name." changed the group icon.";
        }
        if ($arr['group_name'] != '') {
            $wpdb->get_results("UPDATE wp_message_group SET group_name='$group_name' WHERE Gid=$group_id ");
            $message =$user_name." changed the group name.";
        }
    }else{
       return new WP_Error( 'update_not_found', __('No update found.'), array( 'status' => 200 ) ); 
    }

    
    $wpdb->get_results("INSERT INTO wp_message_data( group_id, sender_id, receiver_id, message,message_type) VALUES ($group_id, $user_id,0,'$message','notification')");
    $insertRow = $wpdb->get_results("SELECT LAST_INSERT_ID()");
    $Mid       = '';
    foreach ($insertRow[0] as $key => $value) {
        $Mid = $value;  
    }
    foreach ($list as $key => $value) {
        $status = check_group_chat_status($group_id,$value);
        if( $user_id == $value ){
            $wpdb->get_results("INSERT INTO wp_group_status(group_id, mesage_id, user_id, status) VALUES ($group_id,$Mid,$value,1)");
        }else{
            if( $status == ''){ $status = 0 ;}
            $wpdb->get_results("INSERT INTO wp_group_status(group_id, mesage_id, user_id, status) VALUES ($group_id,$Mid,$value,$status)");
            $tokens           = get_user_meta($value, 'device_token',true);
            $action_type      = 'message';
            $notificationData = notification_data( $action_type,$Mid );

            if ( !empty($tokens)) {
                foreach ($tokens as $key => $token) {
                    iospushnotification($token,$message,$action_type,$notificationData);
                }
            }
        }
        
    }// eo for
    $response = new WP_REST_Response(array('message'=> 'successfully Group profile Changed.','data'=>array('status'=> 200,'params'=>$arr ))  );
    return $response;

}
function chatlist_user_search($request){
    global $wpdb,$plugin_user_images_directory;
    $wp_upload_dir      = wp_upload_dir();
    $imge_path          = $wp_upload_dir['baseurl'].'/'.$plugin_user_images_directory.'/';
    $page               = $request['pages'];
    $posts_per_page     = $request['per_page'];
    $user_id            = $_POST['user_id'];
    $keyword            = $_POST['keyword'];
    $user_status        = get_user_by('id',(int)$user_id);
    $get_chatlist       = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user = $user_id ");
    $totalRows          = array();
    $group_search       = array();

    if ( !isset( $user_id ) || empty( $user_id )) {
        return new WP_Error( 'no_user_found', __('No User found'), array( 'status' => 400 ) );
    }
    if ( !isset( $user_status ) || empty( $user_status )) {
        return new WP_Error( 'invalid_user', __('user is not a valid users'), array( 'status' => 400 ) );
    }
    if ( !isset( $keyword ) || empty( $keyword )) {
        return new WP_Error( 'no_keyword_found', __('No keyword found'), array( 'status' => 400 ) );
    }
    if ( empty($get_chatlist)) {
        return new WP_Error( 'no_chatlist_found', __('No Chatlist found'), array( 'status' => 400 ) );
    }
    if ( $get_chatlist[0]->messaged_users != '' ) {
        $chatlist_user      = $get_chatlist[0]->messaged_users;
        $arr1 = $wpdb->get_results("SELECT ID FROM wp_users WHERE ID In($chatlist_user) and display_name LIKE '%$keyword%'"); 
    }
    if ( $get_chatlist[0]->group_id != '' ) {
        $chatlist_group     = $get_chatlist[0]->group_id; 
        $arr2 = $wpdb->get_results("SELECT Gid FROM wp_message_group WHERE Gid In($chatlist_group) and group_name LIKE '%$keyword%'"); 
    }
    $count = 0;
    $group_count = 0;
    if( !empty( $arr1)){
        foreach ($arr1 as $key => $value) {
            $totalRows[$count] = $value->ID;
            $count++;
        } 
    }
    if( !empty( $arr2)){
        foreach ($arr2 as $key => $value) {
            $totalRows[$count]              = $value->Gid;
            $group_search[$group_count]     = $value->Gid;
            $group_count++;
            $count++;
        } 
    };
    if(empty( $totalRows )){ return new WP_Error( 'no_result_found', __('No request found.'), array( 'status' => 200 ) );}
    foreach ($totalRows as $i => $totalRow){
        $get_user        = $totalRows[$i];
        $last_msg        = $wpdb->get_results("SELECT * FROM wp_message_data WHERE sender_id IN ( $user, $get_user) and receiver_id IN ( $user, $get_user) or group_id = $get_user ORDER BY message_id DESC LIMIT 1");
        $unread          = $wpdb->get_results("SELECT * FROM wp_message_data WHERE sender_id = $get_user and receiver_id  = $user and status = 0");
        $unreadCount     = count($unread);
        $userDetails     = get_user_by('id',(int)$get_user);
        $group_id        = (int) $last_msg[0]->group_id;
        $is_group        = in_array($get_user, $group_search);
        if ( !$is_group) {
            
            $image       = get_usermeta( $get_user, $meta_key = 'user_img',true );
            if ($image == '') {
                $p_data[$i]->user_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
            }else{
                $p_data[$i]->user_img = $imge_path.$image;
            }
            $p_data[$i]->unreadCount    = $unreadCount;
            $p_data[$i]->user_name      = $userDetails->display_name;
            $p_data[$i]->message_id     = $last_msg[0]->message_id != null ? $last_msg[0]->message_id : '';
            $p_data[$i]->user_id        = $user;
            $p_data[$i]->chatted_user   = $get_user;
            $p_data[$i]->date           = $last_msg[0]->message_date;
            $p_data[$i]->message        = $last_msg[0]->message != null ? $last_msg[0]->message : '';
            if ( $p_data[0]->sender_id == $user) {
                $p_data[$i]->status     = 1;
            }else{
                $p_data[$i]->status     = $last_msg[0]->status;
            }
            
        }else{
            $group_id = (int)$get_user;
            $group_details              = $wpdb->get_results("SELECT * FROM wp_message_group WHERE Gid = $group_id");

            if ( $group_details[0]->sender_id != $user ) {
                $unread = $wpdb->get_results("SELECT * FROM wp_group_status WHERE user_id = $user and group_id = $group_id and status = 0");
                $unreadCount     = count($unread);
                $p_data[$i]->unreadCount    = $unreadCount;
            }else{
                $p_data[$i]->unreadCount    = 0;
            }
            if ($group_details[0]->group_icon == '') {
                $p_data[$i]->group_icon = home_url().'/wp-content/themes/jobcareer-child-theme/images/group_icon.png';
            }else{
                $p_data[$i]->group_icon = $imge_path.$group_details[0]->group_icon;
            }
            $curr_group_members         = explode(',', $group_details[0]->group_members);
            foreach ($curr_group_members as $key => $value) {
                $image       = get_user_meta ($value,'user_img');
                $user_image  = $image[0];
                if ($user_image == '') {
                    $ab_img = home_url().'/wp-content/plugins/wp-jobhunt/assets/images/img-not-found16x9.jpg';
                }else{
                    $ab_img = $imge_path.$user_image;
                }
                $p_data[$i]->group_members[$key]->user_id   = $value;
                $p_data[$i]->group_members[$key]->user_name = get_usermeta( $value, 'display_name' );
                $p_data[$i]->group_members[$key]->user_img  = $ab_img;


            }
            $p_data[$i]->group_name     = $group_details[0]->group_name;
            $p_data[$i]->message_id     = $last_msg[0]->message_id != null ? $last_msg[0]->message_id : '' ;
            $p_data[$i]->group_id       = $group_details[0]->Gid;
            $p_data[$i]->group_admin    = $group_details[0]->group_admin;
            //$p_data[$i]->group_members    = $group_details[0]->group_members;
            $p_data[$i]->date           = $group_details[0]->created_date;
            $p_data[$i]->message        = $last_msg[0]->message != null ? $last_msg[0]->message : '';
        }
    }//eo


    $totalRows = $p_data;
    if ( empty($totalRows)) {
       return new WP_Error( 'no_result_found', __('No request found.'), array( 'status' => 200 ) );
    }
    $offset             = (($page-1) * $posts_per_page);
    $current_page_record= ($page * $posts_per_page);

    $total_records      = count($totalRows);
    $total_pages        = ceil($total_records / $posts_per_page);
    if ( $current_page_record > $total_records) {
       $current_page_record = $total_records;
    }
    if ( $total_pages >= $page ) {
       for($i = $offset; $i < $current_page_record; $i++) {
            $post_data[$i] = $totalRows[$i];
       }
    }else{
       return new WP_Error( 'no_request_found', __('No request found'), array( 'status' => 200 ) );
    }
    $post_data = array_values($post_data);
    if ( $post_data == null) {
        return new WP_Error( 'no_request_found', __('No request found'), array( 'status' => 200 ) );
    }
    $response = new WP_REST_Response(array('message'=> 'all jobs','data'=>array('status'=> 200,'params'=>$post_data) ));
    return $response;
}
function create_group(){
	global $wpdb;
	$user_id 		= $_POST['user_id'];
    $user_name      = get_usermeta((int)$user_id , 'display_name');
	$group_members  = $_POST['group_members'];
	$group_name 	= $_POST['group_name'];
	$group_icon     = $_FILES['group_profile'];
    $created_date   = current_time('mysql');
    $userExist      = get_user_by('id',(int)$user_id);
    $members        = explode( "," , $group_members );
    $total_members  = array_unique(array_merge($members , array($user_id)));

    if ( !isset( $userExist ) || empty( $userExist )) {
        return new WP_Error( 'invalid_user', __('admin user is not found users'), array( 'status' => 400 ) );
    }
    if ( !isset( $members ) || empty( $members )) {
        return new WP_Error( 'members_not_found', __('group members are not found.'), array( 'status' => 400 ) );
    }
    if ( !isset( $group_name ) || empty( $group_name )) {
        return new WP_Error( 'group_name_not_found', __('group name are not found.'), array( 'status' => 400 ) );
    }
    if ( !isset( $members ) || empty( $members )) {
        $group_icon = 'group_icon.png';
    }else{
        $f_name = $_FILES['group_profile']['name'];
        $f_path = $_FILES['group_profile']['tmp_name'];
        $group_icon = user_profile_pic($f_name,$f_path,$user_id);
    }

    $wpdb->get_results("INSERT INTO wp_message_group( group_name, group_icon, group_admin, group_members, created_date) VALUES ('$group_name','$group_icon','$user_id','$group_members','$created_date')");
    $insertRow = $wpdb->get_results("SELECT LAST_INSERT_ID()");
    $Mid       = '';
    foreach ($insertRow[0] as $key => $value) {
        $Mid = (int)$value;  
    }
    $Grouop = $wpdb->get_results("SELECT * FROM wp_message_group WHERE Gid ORDER BY Gid DESC LIMIT 1");
    $Gid    = $Grouop[0]->Gid;

    foreach ($total_members as $key => $value) {
        $sData  = $wpdb->get_results("SELECT * FROM wp_message_record WHERE user = $value");
        if ( empty($sData)) {
            $wpdb->get_results("INSERT INTO wp_message_record( user, group_id, count) VALUES ($value,'$Gid', 1)");
        }else{
             $msg_users = explode(',', $sData[0]->group_id);
             $sCount = 0;
            if( !empty($msg_users[0]) ){
                foreach ($msg_users as $key => $msg_user) {
                    if ( $msg_user != $Gid) {
                        $sCount = $sData[0]->count;
                        $mUsers = $sData[0]->group_id;
                        $sCount = (int)$sCount + 1;
                        if ( $mUsers == '' ) {
                            $mUsers = $Gid;
                        }else{
                            $mUsers = $mUsers.','.$Gid;
                        }
                        $wpdb->get_results("UPDATE wp_message_record SET group_id = '$mUsers',count = $sCount WHERE user = $value");
                    }
                }
                
            }else{
                $mUsers = $sData[0]->group_id;
                $sCount = (int)$sCount + 1;
                if ( $mUsers == '' ) {
                    $mUsers = $Gid;
                }else{
                    $mUsers = $mUsers.','.$Gid;
                }
                $wpdb->get_results("UPDATE wp_message_record SET group_id = '$mUsers',count = $sCount WHERE user = $value");
            }//eo
            
            
        }//else eo
        $metaKey    = 'group__'.$Gid;
        if( $user_id == $value ){
        	add_user_meta($value , $metaKey , 1 );
        }else{
        	add_user_meta($value , $metaKey , 0 );
        }

        $tokens           = get_user_meta($value, 'device_token',true);
        $action_type      = 'message';
        $notificationData = notification_data( $action_type,$Mid );
        $message          = $user_name.' create a group.';
        if ( !empty($tokens)) {
            foreach ($tokens as $key => $token) {
                iospushnotification($token,$message,$action_type,$notificationData);
            }
        }
        
    }

	$response = new WP_REST_Response(array('message'=> 'Group successfully Created','data'=>array('status'=> 200)  ));
    return $response;
}
//eo
//change pass
function send_otp(){
    global $wpdb;
	$email  = $_POST['email'];
	if ( !isset( $email )   || empty( $email )) {
        return new WP_Error( 'no_email_found', __('Email address not found.'), array( 'status' => 400 ) );
    }
    $exists = email_exists( $email );
    if ( $exists ) {
        $user_id    = get_user_by_email( $email )->ID;
        $user_name  = get_usermeta((int)$user_id , 'display_name');
        $date       = current_time('mysql');
        $otp        = substr(wp_rand(), 0,5);
        $check_row  = $wpdb->get_results("SELECT * FROM wp_otp WHERE user_id= $user_id");
        $subject = "Reset your Jefes password";
        $to      = $email;
        $message.='<!DOCTYPE html>';
        $message.='<html lang="en">';
        $message.='<head><title></title><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>';
        $message.='<body><table style="width: 100%;padding:0; width: 100%; max-width: 600px; margin: 0 auto;"cellspacing="0" cellpadding="0" border="0">';
        $message.='<tbody>';
        $message.='<tr>';
        $message.='<td style="border-collapse:collapse">';
        $message.='<table style="width: 100%; max-width: 600px; margin: 0 auto; " cellspacing="0" cellpadding="0" border="0">';
        $message.='<tbody>';
        $message.='<tr style="text-align: left;">';
        $message.='<td style="border-collapse:collapse; text-align: left;"><span><a href="http://3.216.71.113/jefes" target="_self"><img src="jefeslogo.png"></a></span></td>';
        $message.='</tr>';
        $message.='<tr>';
        $message.='<td><span style="font-size: 20px;display: block;margin: 30px 0px 15px 0px;">Hello <strong>'.$user_name.'</strong></span></td>';
        $message.='</tr>';
        $message.='<tr>';
        $message.='<td><span style="font-size: 16px;display: block;">We received a request to reset your jefes password. <br> Enter the following password to reset the code';
        $message.='</span></td>';
        $message.='</tr>';
        $message.='<tr>';
        $message.='<td>';
        $message.='<span style="font-size: 16px;display: inline-block;background-color: #f4f4f4;color: #333;padding: 10px 20px;margin-top: 20px;font-weight: 700;">'.$otp;
        $message.='</span>';
        $message.='</td>';
        $message.='</tr>';
        $message.='</tbody>';
        $message.='</table></td></tr></tbody></table>';
        $message.='</body></html>';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $email, $subject, $message, $headers);

        if ( empty($check_row)) {
            $wpdb->get_results("INSERT INTO wp_otp(user_id, otp) VALUES ($user_id,$otp)");
        }else{
            $wpdb->get_results("UPDATE wp_otp SET otp = $otp, sent_date = '$date' WHERE user_id = $user_id");
        }
        $response = new WP_REST_Response(array('message'=> 'Please Check your email','data'=>array('status'=> 200) ));
        return $response;
    } else {
        return new WP_Error( 'invalid_email', __('Invalid Email Address'), array( 'status' => 400 ) );
    }
}
function check_otp(){
	global $wpdb;
	$email  = $_POST['email'];
	$otp    = $_POST['OTP'];
	if ( !isset( $email )   || empty( $email )) {
        return new WP_Error( 'no_email_found', __('Email address not found.'), array( 'status' => 400 ) );
    }
    if ( !isset( $otp )   || empty( $otp )) {
        return new WP_Error( 'no_otp_found', __('No OTP found.'), array( 'status' => 400 ) );
    }
    $exists = email_exists( $email );
    if ( $exists ) {
        $user_id    = get_user_by_email( $email )->ID;
        $user_name  = get_usermeta((int)$user_id , 'display_name');
        $result     = $wpdb->get_results("SELECT * FROM wp_otp WHERE user_id = $user_id");
        if( empty($result)){
        	return new WP_Error( 'inavlid_code', __('Invalid OTP.'), array( 'status' => 400 ) );
        }else{
        	$original_otp = $result[0]->otp;
        	$otp_date     = $result[0]->sent_date;
        	$current_date = current_time('mysql');
        	$expire_time  = dateDiffInDays($current_date,$otp_date);
        	if( $expire_time == 0){
        		if( $original_otp == $otp ){
        			$wpdb->get_results("DELETE FROM wp_otp where user_id = $user_id ");
        			$response = new WP_REST_Response(array('message'=> 'the code is verified','data'=>array('status'=> 200) ));
        			return $response;
	        	}else{
	        		return new WP_Error( 'invalid_code', __('Invalid code.'), array( 'status' => 400 ) );
	        	}
        	}else{
        		return new WP_Error( 'expired_code', __('This code is expired.'), array( 'status' => 400 ) );
        	}
        }
    }else{
    	return new WP_Error( 'invalid_code', __('Invalid code for this email.'), array( 'status' => 400 ) );
    }
}
function change_password(){
	global $wpdb;
	$email  	= $_POST['email'];
	$password  	= $_POST['password'];
	$user_id    = get_user_by_email( $email )->ID;
	if ( !isset( $email )   || empty( $email )) {
        return new WP_Error( 'no_email_found', __('No Email address found.'), array( 'status' => 400 ) );
    }
    if ( !isset( $password )   || empty( $password )) {
        return new WP_Error( 'no_password', __('No password found.'), array( 'status' => 400 ) );
    }
    wp_update_user( array ('ID' => $user_id, 'user_pass' => $password));
    $response = new WP_REST_Response(array('message'=> 'Password is chnaged.','data'=>array('status'=> 200) ));
    return $response;
}
function test_iospushnotification($token,$msg,$type,$data) {
      $url = 'https://fcm.googleapis.com/fcm/send';
      $notification = [
          "sound" => 'Default',
          "body" => $msg,
          "type" => $type,
          "data"=>$data
      ];
      $fields = array(
          'to' => $token,
          'notification' => $notification,
          
      );
      $fields = json_encode($fields);
      $headers = array(
          'Authorization: key=' . "AAAA9fLAOv8:APA91bEaG2bL-DiKcKCCAd_lgcqnycr8wGelcpeijo1uf4Nq_2zwQgqGYqO_wzAKP-z78IpN7RslEYz_NL2YFD9g2xA4gt1aAJv4_rM4N8vpC9AidvxZ6kX1aAOl1sUBmEMthWx6vCOg",
          'Content-Type: application/json'
      );
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

      $result = curl_exec($ch);
      curl_close($ch);
      //print_r($result);die;
      return $result;
  }

function test(){
    global $wpdb;
    $memeber_data = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = 134 ");
    return $memeber_data[0]->display_name;
 //    return get_single_message(259);
	// return check_group_chat_status(35,123);
    // $f_name = $_FILES['group_icon']['name'];
    // $f_path = $_FILES['group_icon']['tmp_name'];
    // $media_url = user_profile_pic($f_name,$f_path,$user_id);
   
    $user_id = 119;
    $tokens  = get_user_meta($user_id, 'device_token',true);
    $msg     = 'test notification'; 
    $data    = array($user_id);
    $type    = 'test';
    //return 'hkjl';
    foreach ($tokens as $key => $token) {
        $data = iospushnotification($token,$msg,$type,$data) ;
    }
    return $data;
    
    //uplod_media($f_name,$f_path);
}
// -----------------------------route hook start---------------------------------------
add_action('rest_api_init', function() {

    // add fiels in user get api
    register_rest_field('user', 'meta', array(
        'get_callback' => 'get_user_meta_value',
        'update_callback' => null,
        'schema' => array(
          'type' => 'array'
        )
    ));
    register_rest_field('user', 'user_data', array(
        'get_callback' => 'get_user_details',
        'update_callback' => null,
        'schema' => array(
          'type' => 'array'
        )
    ));

    //custom routes
    register_rest_route( 'wp/v2/', 'users/signup/',array(
        'methods'  => 'POST',
        'callback' => 'user_registration'
    ));

    register_rest_route( 'wp/v2/', 'users/signin/',array(
        'methods'  => 'POST',
        'callback' => 'user_login'
    ));

    register_rest_route( 'wp/v2/', 'users/signout/',array(
        'methods'  => 'POST',
        'callback' => 'signOut'
    ));

    register_rest_route( 'wp/v2/', 'users/update/',array(
        'methods'  => 'POST',
        'callback' => 'user_update_data'
    ));

    register_rest_route( 'wp/v2/', 'users/delete/',array(
        'methods'  => 'POST',
        'callback' => 'remove_logged_in_user'
    ));
    register_rest_route( 'wp/v2/', 'create_comment/',array(
        'methods'  => 'POST',
        'callback' => 'user_comments'
    ));
    register_rest_route( 'wp/v2/', 'delete_comment/',array(
        'methods'  => 'POST',
        'callback' => 'delete_comment'
    ));
    register_rest_route( 'wp/v2/', 'posts/active-post-comments/',array(
        'methods'  => 'POST',
        'callback' => 'post_comments',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'posts/active-post-comments/all-reply/',array(
        'methods'  => 'POST',
        'callback' => 'all_reply',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'posts-data/',array(
        'methods'  => 'GET',
        'callback' => 'all_posts',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'search_jobs/',array(
        'methods'  => 'POST',
        'callback' => 'search_jobs',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'conversations_search/',array(
        'methods'  => 'POST',
        'callback' => 'conversations_search',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'jobs/',array(
        'methods'  => 'POST',
        'callback' => 'jobs',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'conversations/',array(
        'methods'  => 'POST',
        'callback' => 'conversations',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'question_answers/',array(
        'methods'  => 'POST',
        'callback' => 'question_answers',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'ask_question/',array(
        'methods'  => 'POST',
        'callback' => 'ask_question'
    ));
    register_rest_route( 'wp/v2/', 'reply_answer/',array(
        'methods'  => 'POST',
        'callback' => 'reply_answer'
    ));
    register_rest_route( 'wp/v2/', 'questions/',array(
        'methods'  => 'POST',
        'callback' => 'questions'
    ));
    register_rest_route( 'wp/v2/', 'update_conversation/',array(
        'methods'  => 'POST',
        'callback' => 'update_conversation'
    ));
    register_rest_route( 'wp/v2/', 'delete_conversation/',array(
        'methods'  => 'POST',
        'callback' => 'delete_conversation'
    ));
    register_rest_route( 'wp/v2/', 'conversation_post_comments/',array(
        'methods'  => 'POST',
        'callback' => 'conversation_post_comments',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'post_jobs/',array(
        'methods'  => 'POST',
        'callback' => 'post_job'
    ));
    register_rest_route( 'wp/v2/', 'update_job/',array(
        'methods'  => 'POST',
        'callback' => 'update_job'
    ));
    register_rest_route( 'wp/v2/', 'delete_job/',array(
        'methods'  => 'POST',
        'callback' => 'delete_job'
    ));
    register_rest_route( 'wp/v2/', 'applied_jobs/',array(
        'methods'  => 'POST',
        'callback' => 'applied_jobs',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));

    register_rest_route( 'wp/v2/', 'apply_for_job/',array(
        'methods'  => 'POST',
        'callback' => 'apply_for_job'
    ));
    register_rest_route( 'wp/v2/', 'shortlisted_jobs/',array(
        'methods'  => 'POST',
        'callback' => 'shortlisted_jobs',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'campany_posted_jobs/',array(
        'methods'  => 'POST',
        'callback' => 'campany_posted_jobs'
    ));
    register_rest_route( 'wp/v2/', 'job_filters/',array(
        'methods'  => 'GET',
        'callback' => 'jobs_taxonomies'
    ));
    register_rest_route( 'wp/v2/', 'user_profile/',array(
        'methods'  => 'POST',
        'callback' => 'user_profile'
    ));
    register_rest_route( 'wp/v2/', 'vote_and_flag/',array(
        'methods'  => 'POST',
        'callback' => 'vote_and_flag'
    ));
    register_rest_route( 'wp/v2/', 'update_comment/',array(
        'methods'  => 'POST',
        'callback' => 'update_comment'
    ));
    register_rest_route( 'wp/v2/', 'views/',array(
        'methods'  => 'POST',
        'callback' => 'views'
    ));
    register_rest_route( 'wp/v2/', 'send_request/',array(
        'methods'  => 'POST',
        'callback' => 'send_request'
    ));
    register_rest_route( 'wp/v2/', 'action_on_request/',array(
        'methods'  => 'POST',
        'callback' => 'action_on_request'
    ));
    register_rest_route( 'wp/v2/', 'all_userslist/',array(
        'methods'  => 'POST',
        'callback' => 'all_userslist',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'get_request/',array(
        'methods'  => 'POST',
        'callback' => 'get_request',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'users_serach/',array(
        'methods'  => 'POST',
        'callback' => 'users_serach',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'connection_search/',array(
        'methods'  => 'POST',
        'callback' => 'connection_search',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'send_notifications/',array(
        'methods'  => 'POST',
        'callback' => 'send_notifications'
    ));
    register_rest_route( 'wp/v2/', 'notifications/',array(
        'methods'  => 'POST',
        'callback' => 'notifications',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'initiate_messaging/',array(
        'methods'  => 'POST',
        'callback' => 'initiate_messaging'
    ));
    register_rest_route( 'wp/v2/', 'messaging/',array(
        'methods'  => 'POST',
        'callback' => 'messaging'
    ));
    register_rest_route( 'wp/v2/', 'update_user_availability/',array(
        'methods'  => 'POST',
        'callback' => 'update_user_availability'
    ));
    register_rest_route( 'wp/v2/', 'delete_chat/',array(
        'methods'  => 'POST',
        'callback' => 'delete_chat'
    ));
    register_rest_route( 'wp/v2/', 'get_messages/',array(
        'methods'  => 'POST',
        'callback' => 'get_messages',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'get_chatlist/',array(
        'methods'  => 'POST',
        'callback' => 'get_chatlist',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'message_media/',array(
        'methods'  => 'POST',
        'callback' => 'message_media'
    ));
    register_rest_route( 'wp/v2/', 'change_group_icon/',array(
        'methods'  => 'POST',
        'callback' => 'change_group_icon'
    ));
    register_rest_route( 'wp/v2/', 'create_group/',array(
        'methods'  => 'POST',
        'callback' => 'create_group'
    ));
    register_rest_route( 'wp/v2/', 'exit_group/',array(
        'methods'  => 'POST',
        'callback' => 'exit_group'
    ));
    register_rest_route( 'wp/v2/', 'upadte_delete_members/',array(
        'methods'  => 'POST',
        'callback' => 'upadte_delete_members'
    ));
    register_rest_route( 'wp/v2/', 'replace_and_delete_old_admin/',array(
        'methods'  => 'POST',
        'callback' => 'replace_and_delete_old_admin'
    ));
    register_rest_route( 'wp/v2/', 'chatlist_user_search/',array(
        'methods'  => 'POST',
        'callback' => 'chatlist_user_search',
        'args' => array(
            'pages' => array (
                'required' => true
            ),
            'per_page' => array (
                'required' => true
            ),
            
        )
    ));
    register_rest_route( 'wp/v2/', 'send_otp/',array(
        'methods'  => 'POST',
        'callback' => 'send_otp'
    ));
    register_rest_route( 'wp/v2/', 'check_otp/',array(
        'methods'  => 'POST',
        'callback' => 'check_otp'
    ));
    register_rest_route( 'wp/v2/', 'change_password/',array(
        'methods'  => 'POST',
        'callback' => 'change_password'
    ));
    register_rest_route( 'wp/v2/', 'test/',array(
        'methods'  => 'POST',
        'callback' => 'test'
    ));//end custom routes


}); //end hook


