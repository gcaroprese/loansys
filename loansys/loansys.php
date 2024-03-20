<?php
/*
Plugin Name: LoanSys
Plugin URI: https://inforket.com
Description: System to complete a form and manage content about loans. Use the shortcode [loansys_form] 
Version: 1.2.3
Author: Gabriel Caroprese / Inforket
Author URI: https://inforket.com
*/ 

/*
    Remember to use shortcode [loansys_form] 
*/

// I create the loansys manager user profile role
function ik_add_loansys_manager_role() {
    add_role( 'loansys_manager', 'Forms Manager', array( 'read' => true, 'edit_posts' => true, 'manage_options'=> true ) );
    $role= get_role('loansys_manager');
   }
register_activation_hook( __FILE__, 'ik_add_loansys_manager_role' );


//function to modify Loansys Manager page
function ik_loansys_manage_style(){
    if( current_user_can('loansys_manager') ) {  
        echo "<style>#wpwrap{ display: none}</style>
        <script>
        //jQuery('#wpwrap').remove();
        </script>";
    }
}


// I create a redirection for Service Provider User Profile
function ik_loansys_login_redirect( $redirect_to, $request, $user ) {
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        if ( in_array( 'administrator', $user->roles ) ) {
            return get_site_url().'/wp-admin/admin.php?page=loansys_admin';
        } else {
            return $redirect_to;
        }
    } else {
        return $redirect_to;
    }
}
add_filter( 'login_redirect', 'ik_loansys_login_redirect', 10, 3 );


// I disable the wp admin bar for admin of loans
add_action('after_setup_theme', 'ik_loansys_remove_wpadminbar');
function ik_loansys_remove_wpadminbar() {
    if (current_user_can('loansys_manager')) {
      show_admin_bar(false);
    }
}

// I add menus on WP-admin
add_action('admin_menu', 'ik_loansys_wpmenu');
function ik_loansys_wpmenu(){
    add_menu_page('Loansys Admin', 'Loansys Admin', 'manage_options', 'loansys_admin', 'loansys_admin', plugin_dir_url( __FILE__ ) . 'img/loansys-plugin-icon.png' );
    add_submenu_page('loansys_admin', 'Loansys Admin Recaptcha', 'Recpatcha', 'manage_options', 'loansys_admin_recaptcha', 'loansys_admin_recaptcha');
    
}

// I load the content of the Loansys Admin page

function loansys_admin(){
    $showDataMenu = 1;
    include ('templates/loansys-admin.php');
}


//function to load recaptcha config page
function loansys_admin_recaptcha(){
    $showDataMenu = 1;
    include ('templates/recaptcha.php');
}

// I add CSS code to WP dashboard
add_action('admin_head', 'ik_loansys_css_admin_wp');
function ik_loansys_css_admin_wp() {
    echo '<style>
    #toplevel_page_loansys_admin .wp-menu-image {
    position: relative;
    top: -3.5px;
    }
    </style>';
}


// I create a DB table to manage submissions
register_activation_hook( __FILE__, 'ik_loansys_create_db' );
function ik_loansys_create_db() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'loansys_requests';

	$sql = "
	CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		guid varchar(36) NOT NULL,
	    firstname varchar(255) NOT NULL, 
	    lastname varchar(255) NOT NULL, 
		birthdate date DEFAULT '0000-00-00' NOT NULL,
	    amount_requested decimal(10,2) NOT NULL,
	    language varchar(7) NOT NULL, 
	  	address tinytext NOT NULL, 
	  	adresse2 tinytext NOT NULL, 
	  	province tinytext NOT NULL, 
	    codepostal varchar(10) NOT NULL, 
	  	city varchar(150) NOT NULL, 
	  	country varchar(150) NOT NULL, 
	  	phone varchar(25) NOT NULL, 
	  	mobilephone varchar(25) NOT NULL, 
	  	fax varchar(25) NOT NULL, 
	  	ref1_prenom varchar(25) NOT NULL, 	
	  	ref1_nom varchar(255) NOT NULL, 
	  	ref1_telephone varchar(25) NOT NULL, 
	  	ref1_courriel varchar(255) NOT NULL, 
	  	ref1_lien varchar(255) NOT NULL, 
	  	ref2_prenom varchar(255) NOT NULL, 	
	  	ref2_nom varchar(255) NOT NULL, 
	  	ref2_telephone varchar(25) NOT NULL, 
	  	ref2_courriel varchar(25) NOT NULL, 
	  	ref2_lien varchar(255) NOT NULL,
	  	ref3_prenom varchar(255) NOT NULL, 	
	  	ref3_nom varchar(255) NOT NULL, 
	  	ref3_telephone varchar(25) NOT NULL, 
	  	ref3_courriel varchar(25) NOT NULL, 
	  	ref3_lien varchar(255) NOT NULL,
	  	type_of_income varchar(40) NOT NULL,
	  	employer_company varchar(255) NOT NULL,
	  	employer_phone varchar(25) NOT NULL,
	  	employeur_telephone_ext varchar(25) NOT NULL,
	  	employer_zip varchar(10) NOT NULL, 
	  	employer_supervisor varchar(255) NOT NULL,
	  	your_occupation varchar(255) NOT NULL,
	  	payment_freq varchar(50) NOT NULL,
	  	payment_day date DEFAULT '0000-00-00' NOT NULL,
	  	hiring_date date DEFAULT '0000-00-00' NOT NULL,
	  	income decimal(10,2) NOT NULL,
	  	email varchar(255) NOT NULL,
	  	datemoved date DEFAULT '0000-00-00' NOT NULL,
	  	client_associated bigint(20) NOT NULL,
	  	rejection_reason bigint(20) NOT NULL,
	  	IP_address varchar(15) NOT NULL,
	  	IBV tinytext NOT NULL,
	  	status varchar(15) NOT NULL,
	  	comments_request longtext NOT NULL,
	  	comments_admin longtext NOT NULL,
	    modification_datetime datetime DEFAULT '0000-00-00' NOT NULL,
	    request_datetime datetime DEFAULT '0000-00-00' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

//Definition of payment frequency

define('IK_LOANSYS_PAYMENT_FREQ', '
<option value="Every week">'.__( 'Every week', 'loansys' ).'</option>
<option value="Every two weeks">'.__( 'Every two weeks', 'loansys' ).'</option>
<option value="Twice per month">'.__( 'Twice per month', 'loansys' ).'</option>
<option value="Once per month">'.__( 'Once per month', 'loansys' ).'</option>');      


// Function to get Translax Admin Email
function ik_loansys_get_adminemail(){
    global $wpdb;
    $translax_admin_email = "SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'admin_email'";
    $translax_get_email = $wpdb->get_results($translax_admin_email);
    $ik_admin_email = $translax_get_email[0]->option_value;
    
    return $ik_admin_email;
}

//Function to generate a guid
function ik_loansys_guid_generator(){
    $guid = '';
    $namespace = rand(11111, 99999);
    $uid = uniqid('', true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = substr($hash,  0,  8) . '-' .
            substr($hash,  8,  4) . '-' .
            substr($hash, 12,  4) . '-' .
            substr($hash, 16,  4) . '-' .
            substr($hash, 20, 12);
    return $guid;
}

//Function to format phone number
function ik_loansys_format_phone($phoneNumber){
    $phoneFormatting = str_replace(',', '', $phoneNumber);
    $phoneFormatting = str_replace('.', '', $phoneFormatting);
    $phoneFormatting = str_replace('-', '', $phoneFormatting);
    $phoneFormatting = str_replace(')', '', $phoneFormatting);
    $phoneFormatting = str_replace('(', '', $phoneFormatting);
    $phoneFormatting = str_replace('+', '', $phoneFormatting);
    $phoneFormatting = str_replace('_', '', $phoneFormatting);
    $phoneFormatting = str_replace(' ', '', $phoneFormatting);
    $phone = intval($phoneFormatting);

    if (strlen((string)$phone) < 7 && strlen((string)$phone) > 13){
        $phone_formated = '-';
    } else {
        if (strlen((string)$phone) == 7){ //4102721
            $first3Numbers = substr($phone, 0, 3);
            $last4Numbers = substr($phone, 3, 4);
            $phone_formated = $first3Numbers.'-'.$last4Numbers; // 410-2721
        } else if (strlen((string)$phone) == 10){ // 3524102721
            $first3Numbers = substr($phone, 0, 3);
            $second3Numbers = substr($phone, 3, 3);
            $last4Numbers = substr($phone, 6, 4);
            $phone_formated = '('.$first3Numbers.') '.$second3Numbers.'-'.$last4Numbers; // (352) 410-2721
        } else if (strlen((string)$phone) == 12){ //543764616757
            $first2Numbers = substr($phone, 0, 2);
            $areCode = substr($phone, 2, 3);
            $threeNumbers = substr($phone, 5, 3);
            $lastDigits = substr($phone, 8, 4);
            $phone_formated = '+'.$first2Numbers.' ('.$areCode.') '.$threeNumbers.'-'.$lastDigits; //+54 (376) 461-6757
        } else if (strlen((string)$phone) == 13){
            $first2Numbers = substr($phone, 0, 2);
            $thirdDigit = substr($phone, 2, 1);
            $areaCode = substr($phone, 3, 3);
            $threeNumbers = substr($phone, 6, 3);
            $lastDigits = substr($phone, 9, 4);
            $phone_formated = '+'.$first2Numbers.' '.$thirdDigit.' ('.$areaCode.') '.$threeNumbers.'-'.$lastDigits; //+54 9 (376) 461-6757
        } else {
            $phone_formated = '-';
        }
    }
    return $phone_formated;
}


// Shortcode to show form
function ik_loansys_form() {
                                    
// turn on output buffering to capture script output
ob_start();
include('templates/loansys-form.php');

$content = ob_get_clean();
return $content;
}
add_shortcode('loansys_form', 'ik_loansys_form');

//I add scripts for frontend form
function ik_loansys_register_cssscripts_frontend() {
    //select 2
    wp_register_style( 'ik_loansys_form_css', plugin_dir_url( __FILE__ ) . 'css/style-form.css', false, '1.0', 'all' );
}
add_action( 'wp_enqueue_scripts', 'ik_loansys_register_cssscripts_frontend' );


//I add scripts and CSS for admin panel
function ik_loansys_register_scriptscss_backend() {
    wp_register_style( 'ik_loansys_adminpanel_css', plugin_dir_url( __FILE__ ) . 'css/panel-stylesheet.css', false, '1.0', 'all' );

    //select 2
    wp_register_style( 'ik_loansys_select2css', plugin_dir_url( __FILE__ ) . 'css/select2.css', false, '1.0', 'all' );
    wp_register_script( 'ik_loansys_select2', plugin_dir_url( __FILE__ ) . 'js/select2.js', array( 'jquery' ), '1.0', true );
    wp_register_script( 'ik_loansys_convert_to_csv', plugin_dir_url( __FILE__ ) . 'js/convert-to-csvfile.js', array( 'jquery' ), '1.0', true );
}
add_action( 'admin_enqueue_scripts', 'ik_loansys_register_scriptscss_backend' );


// I get data from applications DB table
function ik_loansys_get_data($numberLimit, $offset, $orderBy, $where) {
    $numberLimit = intval($numberLimit);
    $offset = intval($offset);
    $orderBy = sanitize_text_field($orderBy);
    
    //to avoid errors if numbers are wrong
    if ($numberLimit < 1){
        $numberLimit = 1;
    }
    if ($offset < 0){
        $offset = 0;
    }
    
    if ($where == NULL){
        $where = '';
    }
    
    global $wpdb;
    $getLoanRequest = "SELECT * FROM ".$wpdb->prefix."loansys_requests ".$where." ORDER BY ".$orderBy." LIMIT ".$numberLimit." offset ".$offset;
    $loanRequest = $wpdb->get_results($getLoanRequest);
    
    // I check if value is not null 
    if (isset($loanRequest[0]->id)){
         $loanRequestData = $loanRequest;
    } else{
        $loanRequestData = false;
    }
    return $loanRequestData;
}


//Count amount of entries in query from listings
function ik_loansys_get_data_count($where) {
    
    if ($where == NULL){
        $where = '';
    }
    
    global $wpdb;
    $getLoanRequest_qty = "SELECT id FROM ".$wpdb->prefix."loansys_requests ".$where;
    $loanRequest_qty = $wpdb->get_results($getLoanRequest_qty);
    
    // I check if value is not null 
    if (isset($loanRequest_qty[0]->id)){
         $loanRequestQty = count($loanRequest_qty);
    } else{
        $loanRequestQty = 0;
    }
    return $loanRequestQty;
}




// I retrieve a dash instead of 0 or any other value
function ik_loansys_data_retrieve_checker($applicationValue) {
    if ($applicationValue == '0'){
        $applicationValue = "-";
    }
    return $applicationValue;
    
}

// I retrieve a option value
function ik_loansys_data_retrieve_selectoption($applicationValue) {
    if ($applicationValue != '0' && $applicationValue != ''){
        $applicationValue = "<option checked value='".$applicationValue."'>".$applicationValue."</option>";
        return $applicationValue;
    } else {
        return;
    }
}


// I add var for $_get to show listings
function ik_loansys_add_getvars( $vars ){
    $vars[] = "offset";
    $vars[] = "listpage";
    $vars[] = "searchdata";
    $vars[] = "orderby_list";
    $vars[] = "orderdata_list";
    return $vars;
}
add_filter( 'query_vars', 'ik_loansys_add_getvars' );


//Ajax to save data from listing admin
add_action( 'wp_ajax_ik_loansys_ajax_save_data_listing', 'ik_loansys_ajax_save_data_listing');
function ik_loansys_ajax_save_data_listing() {
	if(isset($_POST['listing_id'])){
	    $listing_id = intval($_POST['listing_id']);
	    
	    if ($listing_id != 0){

				global $wpdb;
				$where = [ 'id' => $listing_id ];
					
				$timeModified = current_time('mysql');
				
				$dataToUpdate  = array (
								'IP_address'=> sanitize_text_field($_POST['IP_address']),
                                'address'=> sanitize_text_field($_POST['address']),
                                'adresse2'=> sanitize_text_field($_POST['adresse2']),
                                'amount_requested'=> date('Y-m-d H:i:s', strtotime($_POST['amount_requested'])),
                                'birthdate'=> date('Y-m-d', strtotime( $_POST['birthdate'])),
                                'city'=> sanitize_text_field($_POST['city']),
                                'client_Associated'=> sanitize_text_field($_POST['client_Associated']),
                                'codepostal'=> sanitize_text_field($_POST['codepostal']),
                                'comments_admin'=> sanitize_text_field($_POST['comments_admin']),
                                'country'=> sanitize_text_field($_POST['country']),
                                'email'=> sanitize_text_field($_POST['email']),
                                'employer_company'=> sanitize_text_field($_POST['employer_company']),
                                'employer_phone'=> sanitize_text_field($_POST['employer_phone']),
                                'employer_supervisor'=> sanitize_text_field($_POST['employer_supervisor']),
                                'employeur_telephone_ext'=> sanitize_text_field($_POST['employeur_telephone_ext']),
                                'fax'=> sanitize_text_field($_POST['fax']),
                                'firstname'=> sanitize_text_field($_POST['firstname']),
                                'hiring_date'=> date('Y-m-d', strtotime( $_POST['hiring_date'])),
                                'language'=> sanitize_text_field($_POST['language']),
                                'lastname'=> sanitize_text_field($_POST['lastname']),
                                'mobilephone'=> sanitize_text_field($_POST['mobilephone']),
                                'payment_day'=> date('Y-m-d', strtotime( $_POST['payment_day'])),
                                'payment_freq'=> sanitize_text_field($_POST['payment_freq']),
                                'phone'=> sanitize_text_field($_POST['phone']),
                                'province'=> sanitize_text_field($_POST['province']),
                                'ref1_courriel'=> sanitize_text_field($_POST['ref1_courriel']),
                                'ref1_lien'=> sanitize_text_field($_POST['ref1_lien']),
                                'ref1_nom'=> sanitize_text_field($_POST['ref1_nom']),
                                'ref1_prenom'=> sanitize_text_field($_POST['ref1_prenom']),
                                'ref1_telephone'=> sanitize_text_field($_POST['ref1_telephone']),
                                'ref2_courriel'=> sanitize_text_field($_POST['ref2_courriel']),
                                'ref2_lien'=> sanitize_text_field($_POST['ref2_lien']),
                                'ref2_nom'=> sanitize_text_field($_POST['ref2_nom']),
                                'ref2_prenom'=> sanitize_text_field($_POST['ref2_prenom']),
                                'ref2_telephone'=> sanitize_text_field($_POST['ref2_telephone']),
                                'ref3_courriel'=> sanitize_text_field($_POST['ref3_courriel']),
                                'ref3_nom'=> sanitize_text_field($_POST['ref3_nom']),
                                'ref3_prenom'=> sanitize_text_field($_POST['ref3_prenom']),
                                'ref3_telephone'=> sanitize_text_field($_POST['ref3_telephone']),
                                'rejection_reason'=> sanitize_text_field($_POST['rejection_reason']),
                                'request_datetime'=> date('Y-m-d', strtotime( $_POST['request_datetime'])),
                                'status'=> sanitize_text_field($_POST['status']),
                                'modification_datetime'=> $timeModified,
						);
				$onOffRetenciones = $wpdb->update($wpdb->prefix . 'loansys_requests',  $dataToUpdate , $where);    
            
    		echo json_encode($timeModified);
    		wp_die();
			
	    } else {
	        echo json_encode(false);				
	        wp_die();    
	    }
	} else {
	    echo json_encode(false);				
	    wp_die();    
	}
}


//Ajax to delete record from listing admin
add_action( 'wp_ajax_ik_loansys_ajax_remove_data_listing', 'ik_loansys_ajax_remove_data_listing');
function ik_loansys_ajax_remove_data_listing() {
	if(isset($_POST['listing_id'])){
	    $listing_id = intval($_POST['listing_id']);
	    
	    if ($listing_id != 0){

        	global $wpdb;
        	$tablaDB = $wpdb->prefix.'ik_ebanicobros_ganancias_meta';
        	$rowResult = $wpdb->delete($wpdb->prefix . 'loansys_requests', array( 'id' => $listing_id ) );
			
			echo json_encode(true);
        	wp_die();
			
	    } else {
	        echo json_encode(false);				
	        wp_die();    
	    }
	} else {
	    echo json_encode(false);				
	    wp_die();    
	}
}

//I add a text domain for translations
function ik_loansys_textdomain_init() {
    load_plugin_textdomain( 'loansys', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'ik_loansys_textdomain_init' );

?>