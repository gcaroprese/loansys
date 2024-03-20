<?php
/*

Form to submit a Loan Request

*/
?>
<?php
    $showForm = true;
    
    //I load scripts / CSS
    wp_enqueue_style( 'ik_loansys_form_css' );
    
    //I make sure the form was submited and there's no cookie   	
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['amount_request']) && !isset($_COOKIE['loansyssubmited'])){
        $message = "";
  
        $amount_request = sanitize_text_field( $_POST['amount_request']);
        $fistname = sanitize_text_field( $_POST['fistname']);
        $lastname = sanitize_text_field( $_POST['lastname']);
        $birthdate = date('Y-m-d', strtotime( $_POST['birthdate']));
        $language_pref = sanitize_text_field( $_POST['language_pref']);
        $phone = sanitize_text_field( $_POST['phone']);
        $mobilephone = sanitize_text_field( $_POST['mobilephone']);
        $fax = sanitize_text_field( $_POST['fax']);
        $email = sanitize_text_field( $_POST['email']);
        $address = sanitize_text_field( $_POST['address']);
        $address2 = sanitize_text_field( $_POST['address2']);
        $codepostal = sanitize_text_field( $_POST['zip_code']);
        $province = sanitize_text_field( $_POST['province']);
        $city = sanitize_text_field( $_POST['city']);
        $country = sanitize_text_field( $_POST['country']);
        $data_moved = date('Y-m-d', strtotime( $_POST['data_moved']));

        //I assign a guid code and IP
        $guid = ik_loansys_guid_generator();
        $ip_address = $_SERVER['REMOTE_ADDR'];

        //references
	  	$ref1_prenom = sanitize_text_field( $_POST['ref1_name']);
	  	$ref1_nom = sanitize_text_field( $_POST['ref1_lastname']);
	  	$ref1_telephone = sanitize_text_field( $_POST['ref1_telephone']);
	  	$ref1_courriel = sanitize_text_field( $_POST['ref1_email']);
	  	$ref1_lien = sanitize_text_field( $_POST['ref1_relationship']);
	  	$ref2_prenom = sanitize_text_field( $_POST['ref2_name']);
	  	$ref2_nom = sanitize_text_field( $_POST['ref2_lastname']);
	  	$ref2_telephone = sanitize_text_field( $_POST['ref2_telephone']);
	  	$ref2_courriel = sanitize_text_field( $_POST['ref2_email']);
	  	$ref2_lien = sanitize_text_field( $_POST['ref2_relationship']);
	  	$ref3_prenom = sanitize_text_field( $_POST['ref3_name']);
	  	$ref3_nom = sanitize_text_field( $_POST['ref3_lastname']);
	  	$ref3_telephone = sanitize_text_field( $_POST['ref3_telephone']);
	  	$ref3_courriel = sanitize_text_field( $_POST['ref3_email']);
	  	$ref3_lien = sanitize_text_field( $_POST['ref3_relationship']);
	  	
	  	
	  	$type_of_income = sanitize_text_field( $_POST['type_of_income_field']);
	  	$employer_company = sanitize_text_field($_POST['employeur_nom_compagnie']);
	  	$employer_phone = sanitize_text_field( $_POST['employeur_telephone']);
	  	$employeur_telephone_ext = sanitize_text_field( $_POST['employeur_telephone_ext']);
	  	$employer_zip = sanitize_text_field( $_POST['employeur_poste']);
	  	$employer_supervisor = sanitize_text_field( $_POST['employeur_superviseur']);
	  	$your_occupation = sanitize_text_field( $_POST['employeur_occupation']);
	  	$payment_freq = sanitize_text_field( $_POST['employeur_freq_paie']);
	  	$payment_day = date('Y-m-d', strtotime( $_POST['employeur_jour_paie']));
	  	$hiring_date = date('Y-m-d', strtotime( $_POST['hiring_date']));
	  	$income = sanitize_text_field( $_POST['income']);
	  	$comments_request = sanitize_text_field( $_POST['comments']);


        if(isset($amount_request) && isset($fistname) && isset($lastname) && isset($birthdate) 
        && isset($language_pref) && isset($phone) && isset($email) && isset($address) && isset($province) && isset($city) && isset($country) && isset($data_moved) && isset($ip_address)){
            
            if (isset($_POST['validate'])){
                // No recaptcha  
                $responseKeys["success"] = true;
            } else {
                if(isset($_POST['g-recaptcha-response'])){
                  $captcha=$_POST['g-recaptcha-response'];
                }
                if(!$captcha){
                  echo '<h4 class="ik-billalert">'.__( 'Please check the captcha form.', 'loansys' ).'</h4></br>';
                }
                global $wpdb;
                $querycaptchskey = "SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'loansys_recaptcha_s' ORDER BY option_id DESC";
                $captchaskey = $wpdb->get_results($querycaptchskey);
                $secretKey = $captchaskey[0]->option_value;
                $ip = $_SERVER['REMOTE_ADDR'];
                // post request to server
                $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
                $response = file_get_contents($url);
                $responseKeys = json_decode($response,true);
                // should return JSON with success as true
            }
            if($responseKeys["success"]) {
            // if(true) {
				
					global $wpdb;
					 $data_loan  = array (
    					    'guid' => $guid,
                    	    'firstname' => $fistname,
                    	    'lastname' => $lastname,
                    		'birthdate' => $birthdate,
                    	    'amount_requested' => $amount_request,
                    	    'language' => $language_pref,
                    	  	'address' => $address,
                    	  	'adresse2' => $address2,
                    	  	'province' => $province,
                    	  	'city' => $city,
                    	  	'codepostal' => $codepostal,
                    	  	'country' => $country,
                    	  	'phone' => ik_loansys_format_phone($phone),
                    	  	'mobilephone' => ik_loansys_format_phone($mobilephone),
                    	  	'fax' => ik_loansys_format_phone($fax),
                    	  	'email' => $email,
                    	  	'datemoved' => $data_moved,
                    	  	'ref1_prenom' => $ref1_prenom,
                    	  	'ref1_nom' => $ref1_nom,
                    	  	'ref1_telephone' => ik_loansys_format_phone($ref1_telephone),
                    	  	'ref1_courriel' => $ref1_courriel,
                    	  	'ref1_lien' => $ref1_lien,
                    	  	'ref2_prenom' => $ref2_prenom,
                    	  	'ref2_nom' => $ref2_nom,
                    	  	'ref2_telephone' => ik_loansys_format_phone($ref2_telephone),
                    	  	'ref2_courriel' => $ref2_courriel,
                    	  	'ref2_lien' => $ref2_lien,
                    	  	'ref3_prenom' => $ref3_prenom,
                    	  	'ref3_nom' => $ref3_nom,
                    	  	'ref3_telephone' => ik_loansys_format_phone($ref3_telephone),
                    	  	'ref3_courriel' => $ref3_courriel,
                    	  	'type_of_income' => $type_of_income,
                    	  	'employer_company' => $employer_company,
                    	  	'employer_phone' => ik_loansys_format_phone($employer_phone),
                    	  	'employeur_telephone_ext' => $employeur_telephone_ext,
                    	  	'employer_zip' => $employer_zip,
                    	  	'employer_supervisor' => $employer_supervisor,
                    	  	'your_occupation' => $your_occupation,
                    	  	'payment_freq' => $payment_freq,
                    	  	'payment_day' => $payment_day,
                    	  	'hiring_date' => $hiring_date,
                    	  	'income' => $income,
                    	  	'comments_request' => $comments_request,
                    	  	'IP_address' => $ip_address,
                    	  	'status' => 'Waiting',
                    	  	'modification_datetime' => current_time('mysql', 1),
                    	  	'request_datetime' => current_time('mysql', 1),
    					);
                     
						$tableInsert = $wpdb->prefix.'loansys_requests';
						$rowResult = $wpdb->insert($tableInsert,  $data_loan , $format = NULL);

				    // if form succesfully sent info to DB
					if ($rowResult == 1){
					    
					   //I create a cookie to block user to submit again
                        echo '<script>
                        function setCookie(name,value,days) {
                            var expires = "";
                            if (days) {
                                var date = new Date();
                                date.setTime(date.getTime() + (days*24*60*60*1000));
                                expires = "; expires=" + date.toUTCString();
                            }
                            document.cookie = name + "=" + (value || "")  + expires + "; path=/";
                        }
                        setCookie("loansyssubmited","submited",7);
                        </script>';
												
						// Send email to Admin
						$to = ik_loansys_get_adminemail();
						$subject = __( 'Loansys / New Form Submited', 'loansys' );
						$body = '<p>'.__( 'Hello, a new form was submited. Go to .', 'loansys' ).get_site_url().__( ' for more information.', 'loansys' ).'</p>';
						$headers = array('Content-Type: text/html; charset=UTF-8');
						 
						wp_mail( $to, $subject, $body, $headers );  
						
						$message = '<div style="padding: 15px;z-index: 999999999999;text-align: center;">'.__( 'Thank you for your inquiry. Your request will be processed immediately.', 'loansys' ).'</div>';
						 $showForm = false;
                    } else {
                        $message .= '<p>Server Error</p>';
                    }
            } else {
                $message ='<p>Captcha Error</p>';
            }
        } else {
            $message ='<p>Error</p>';
        }
    } else {
        $message = '';
    }


?>
<?php
    if (!isset($_COOKIE['loansyssubmited'])) {
    // I check if recaptcha is enabled
    global $wpdb;
    $querycaptchakey = "SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'loansys_recaptcha_use' ORDER BY option_id DESC";
    $captchause = $wpdb->get_results($querycaptchakey);
    global $wpdb;
    $querycaptchakey = "SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'loansys_recaptcha_k' ORDER BY option_id DESC";
    $captchakey = $wpdb->get_results($querycaptchakey);
    if (isset($captchause[0]->option_value)){
        if($captchause[0]->option_value == 1){
    		$recaptcha_enabled= true;
            // I define recaptcha script 
            $recaptcha = "<script src='https://www.google.com/recaptcha/api.js' async defer></script>
            <div class='g-recaptcha' data-sitekey='".$captchakey[0]->option_value."'></div>";
        } else {
    		$recaptcha_enabled= false;
		$recaptcha='<input type="hidden" name="validate" value="1">';        
        } 
    } else {
		$recaptcha_enabled= false;
		$recaptcha='<input type="hidden" name="validate" value="1">';        
    }
    
// form
?>
<div class="loan-submit">
    <?php 
    if ($showForm == true){
        echo '
        <form action="" method="post" name="loans_form" id="loans_form" enctype="multipart/form-data" onsubmit = "return validateForm()">
            <div class="loan_amountrequested">
                <h3>'.__( 'Amount of Request', 'loansys' ).'</h3>
                <select required name="amount_request">
                    <option selected disabled value="">'.__( 'Select An Amount', 'loansys' ).'</option>
                    <option value="350">CA$ 350</option>
                    <option value="500">CA$ 500</option>
                    <option value="750">CA$ 750</option>
                    <option value="1000">CA$ 1000</option>
                </select>
            </div>
            <div class="loan_personaldata">
                <h3>'.__( 'Your Information', 'loansys' ).'</h3>
                <label class="loan_firstname">
                    <input required type="text" name="fistname" placeholder="'.__( 'First Name', 'loansys' ).'">
                </label>
                <label class="loan_lastname">
                    <input required type="text" name="lastname" placeholder="'.__( 'Last Name', 'loansys' ).'">
                </label>
                <label class="loan_datebirth">
                    <input required type="text" class="walcf7-datepicker" id="birthdate_field" name="birthdate" placeholder="'.__( 'Date of Birth', 'loansys' ).'">
                </label>
                <select required name="language_pref">
                    <option selected disabled value="">'.__( 'Select Preferred Language', 'loansys' ).'</option>
                    <option value="English">'.__( 'English', 'loansys' ).'</option>
                    <option value="French">'.__( 'French', 'loansys' ).'</option>
                </select>
                <label class="loan_fulladdress">
                    <input required type="text" name="address" placeholder="'.__( 'Address 1', 'loansys' ).'">
                </label>            
                <label class="loan_address2">
                    <input type="text" name="address2" placeholder="'.__( 'Address 2', 'loansys' ).'">
                </label>
                <label class="loan_postal_code">
                    <input required type="text" name="zip_code" placeholder="'.__( 'Postal Code', 'loansys' ).'">
                </label> 
                <label class="loan_province">
                    <input required type="text" name="province" placeholder="'.__( 'Province', 'loansys' ).'">
                </label>            
                <label class="loan_city">
                    <input required type="text" name="city" placeholder="'.__( 'City', 'loansys' ).'">
                </label>            
                <label class="loan_country">
                    <input required type="text" name="country" placeholder="'.__( 'Country', 'loansys' ).'">
                </label> 
                <label class="loan_phone">
                    <input type="text" name="phone" placeholder="'.__( 'Phone', 'loansys' ).'" onblur="ik_loansys_format_phone(this)">
                </label> 
                <label class="loan_mobilephone">
                    <input required type="text" name="mobilephone" placeholder="'.__( 'Mobile Phone', 'loansys' ).'" onblur="ik_loansys_format_phone(this)">
                </label> 
                <label class="loan_fax">
                    <input type="text" name="fax" placeholder="'.__( 'Fax', 'loansys' ).'" onblur="ik_loansys_format_phone(this)">
                </label> 
                <label class="loan_email">
                    <input required type="text" name="email" placeholder="'.__( 'Email', 'loansys' ).'">
                </label> 
                <label class="loan_datemoved">
                    <input required type="text" class="walcf7-datepicker" id="data_moved_field" name="data_moved" placeholder="'.__( 'Date Moved To This Address', 'loansys' ).'">
                </label>
                
                <h3>'.__( 'Family Reference', 'loansys' ).' 1</h3>
                <label class="loan_ref1_name">
                    <input required type="text" name="ref1_name" placeholder="'.__( 'Name of Reference', 'loansys' ).' 1">
                </label>
                <label class="loan_ref1_lastname">
                    <input required type="text" name="ref1_lastname" placeholder="'.__( 'Last Name of Reference', 'loansys' ).' 1">
                </label>
                <label class="loan_ref1_telephone">
                    <input required type="text" name="ref1_telephone" placeholder="'.__( 'Phone of Reference', 'loansys' ).' 1" onblur="ik_loansys_format_phone(this)">
                </label>
                <label class="loan_ref1_email">
                    <input type="email" name="ref1_email" placeholder="'.__( 'Email of Reference', 'loansys' ).' 1">
                </label>
                <label class="loan_ref1_relationship">
                    <input required type="text" name="ref1_relationship" placeholder="'.__( 'Relationship with Reference', 'loansys' ).' 1">
                </label>
                
                <h3>'.__( 'Family Reference', 'loansys' ).' 2</h3>
                <label class="loan_ref2_name">
                    <input required type="text" name="ref2_name" placeholder="'.__( 'Name of Reference', 'loansys' ).' 2">
                </label>
                <label class="loan_ref2_lastname">
                    <input required type="text" name="ref2_lastname" placeholder="'.__( 'Last Name of Reference', 'loansys' ).' 2">
                </label>
                <label class="loan_ref2_telephone">
                    <input required type="text" name="ref2_telephone" placeholder="'.__( 'Phone of Reference', 'loansys' ).' 2" onblur="ik_loansys_format_phone(this)">
                </label>
                <label class="loan_ref2_email">
                    <input type="email" name="ref2_email" placeholder="'.__( 'Email of Reference', 'loansys' ).' 2">
                </label>
                <label class="loan_ref2_relationship">
                    <input required type="text" name="ref2_relationship" placeholder="'.__( 'Relationship with Reference', 'loansys' ).' 2">
                </label>
                
                <h3>'.__( 'Family Reference', 'loansys' ).' 3</h3>
                <label class="loan_ref3_name">
                    <input required type="text" name="ref3_name" placeholder="'.__( 'Name of Reference', 'loansys' ).' 3">
                </label>
                <label class="loan_ref3_lastname">
                    <input required type="text" name="ref3_lastname" placeholder="'.__( 'Last Name of Reference', 'loansys' ).' 3">
                </label>
                <label class="loan_ref3_telephone">
                    <input required type="text" name="ref3_telephone" placeholder="'.__( 'Phone of Reference', 'loansys' ).' 3" onblur="ik_loansys_format_phone(this)">
                </label>
                <label class="loan_ref3_email">
                    <input type="email" name="ref3_email" placeholder="'.__( 'Email of Reference', 'loansys' ).' 3">
                </label>
                <label class="loan_ref3_relationship">
                    <input required type="text" name="ref3_relationship" placeholder="'.__( 'Relationship with Reference', 'loansys' ).' 3">
                </label>
                
                <h3>'.__( 'Other Informaton', 'loansys' ).'</h3>
                <p class="warning-loan">Warning!<br />If you declare bankruptcy or a consumir proposal before the end of the contract, you may be charged for fraud under the bankruptcy and insolvency law.</p>
                
                <label class="loan_bankrupcy">
                    <p>'.__( 'Are you planning to go bankrupt or make a consumer proposal in the next 6 months?', 'loansys' ).'</p>
                    <select required name="loan_bankrupcy" id="loan_bankrupcy_field" onchange="select_ik_bankrupt(this);">
                        <option disabled selected value="">'.__( 'Choose', 'loansys' ).'</option>
                        <option value="yes">'.__( 'Yes', 'loansys' ).'</option>
                        <option value="no">'.__( 'No', 'loansys' ).'</option>
                    </select>
                </label>
                
                <label class="loan_type_of_income">
                    <select required name="type_of_income_field" id="type_of_income_field">
                        <option disabled selected value="">'.__( 'Type of income?', 'loansys' ).'</option>
                        <option value="Employé">'.__( 'Employee', 'loansys' ).'</option>
                        <option value="Assurance Emploi">'.__( 'Employment Insurance (ODSP)', 'loansys' ).'</option>
                        <option value="Travailleur autonome">'.__( 'Self-employed worker', 'loansys' ).'</option>
                        <option value="Congé Paternité">'.__( 'Paternity leave', 'loansys' ).'</option>
                        <option value="Congé Maternité">'.__( 'Maternity leave', 'loansys' ).'</option>
                        <option value="Retraité">'.__( 'Retired', 'loansys' ).'</option>
                        <option value="Assurance Sociale">'.__( 'Wellfair', 'loansys' ).'</option>
                        <option value="Sans emploi, ni revenu">'.__( 'Unemployed, no income', 'loansys' ).'</option>  
                    </select>
                </label>
                
                <h3>Your Employer</h3>
                <label class="loan_employeur_nom_compagnie">
                    <input required type="text" name="employeur_nom_compagnie" placeholder="'.__( 'Employer Company', 'loansys' ).'">
                </label>
                <label class="loan_employeur_telephone">
                    <input required type="text" name="employeur_telephone" placeholder="'.__( 'Employer Phone', 'loansys' ).'" onblur="ik_loansys_format_phone(this)">
                </label>
                <label class="loan_employeur_telephone_ext">
                    <input type="text" name="employeur_telephone_ext" placeholder="'.__( 'Phone Extension', 'loansys' ).'"
                </label>
                <label class="loan_employeur_poste">
                    <input required type="text" name="employeur_poste" placeholder="'.__( 'Employer Postal Code', 'loansys' ).'">
                </label>
                <label class="loan_employeur_superviseur">
                    <input required type="text" name="employeur_superviseur" placeholder="'.__( 'Supervisor', 'loansys' ).'">
                </label>
                <label class="loan_employeur_occupation">
                    <input required type="text" name="employeur_occupation" placeholder="'.__( 'Your Ocupation', 'loansys' ).'">
                </label>
                <label class="loan_employeur_freq_paie">
                    <select required name="employeur_freq_paie">
                        <option disabled selected value="">'.__( 'Pay frequency', 'loansys' ).'</option>
                        '.constant("IK_LOANSYS_PAYMENT_FREQ").'                
                    </select>
                </label>
                <label class="loan_employeur_jour_paie">
                    <input required type="text" class="walcf7-datepicker" id="employeur_jour_paie_field" name="employeur_jour_paie" placeholder="'.__( 'Day of Payment', 'loansys' ).'">
                </label>
                <label class="loan_employeur_hiring_date">
                    <input required type="text" class="walcf7-datepicker" id="hiring_date_field" name="hiring_date" placeholder="'.__( 'Hiring Date', 'loansys' ).'">
                </label>
                <label class="loan_income">
                    <input required type="number" name="income" placeholder="'.__( 'Your Income', 'loansys' ).'">
                </label>
                <textarea name="comments" placeholder="'.__( 'Comments', 'loansys' ).'"></textarea>
                
                <h3>Terms and Conditions</h3>
                <input required type="checkbox" name="confirmterms" value="confirm">
                <span>'.__( "I'm confirming that all the informations I have provided is current and accurate and that I understand and accept the terms and conditions.", 'loansys' ).'</span>
            </div>
                <p class="recaptcha-form">
                </p>
            <div class="recaptcha-form">
                '.$recaptcha.'
                </div>
            </div>
            <p>
                <input type="submit" id="ik-submit-button" value="'.__( 'Send', 'loansys' ).'">
            </p>
            <div class="message-qform"></div>
        </form>';
?>
<script>
jQuery( ".loan-submit input[type=submit]" ).on( "click", function() {

	var requiredIncomplete = 0;
    
    jQuery(".loan-submit input").each(function() {
    	var runnedValidator = 0;
        if (jQuery(this).val() == undefined){
       		jQuery(this).removeClass("redborder");
        } else {
        	jQuery(this).addClass("redborder");
        	var requiredIncomplete = requiredIncomplete + 1;
            jQuery( "#translator_signup_form" ).attr("fieldsWrong", requiredIncomplete);
        }
	});
	
    jQuery(".loan-submit select").each(function() {
    	var runnedValidator = 0;
        if (jQuery(this).val() == undefined){
       		jQuery(this).removeClass("redborder");
        } else {
        	jQuery(this).addClass("redborder");
        	var requiredIncomplete = requiredIncomplete + 1;
            jQuery( "#translator_signup_form" ).attr("fieldsWrong", requiredIncomplete);
        }
	});
    
    if (parseInt(jQuery("#loans_form").attr("fieldsWrong")) == 0){
        jQuery("#translator_signup_form").removeAttr("novalidate");
    	return true; 
    } else {
    	jQuery( "#loans_form" ).removeAttr("fieldsWrong");
    	event.preventDefault();
    	return false;
    }
});

//function if bankrupcy
function select_ik_bankrupt(that) {
    if (that.value == "yes") {
        jQuery('#ik-submit-button').attr('disabled', 'disabled');
        jQuery('#ik-submit-button').attr('style', 'pointer-events: none;');
    } else {
        jQuery('#ik-submit-button').removeAttr('disabled');
        jQuery('#ik-submit-button').attr('style', 'pointer-events: all;');
    }
}

if (jQuery(window).width() < 800 || typeof datetimepicker !== 'function') {
    jQuery('.walcf7-datepicker').attr('type', 'date')
    jQuery('.walcf7-datepicker').addClass('ik_loansys_rawdate')
    
    jQuery('.ik_loansys_rawdate').each(function() {
        if (!jQuery(this).parent().hasClass('ik_loansys_date_field')){
            jQuery(this).removeClass('walcf7-datepicker')
            jQuery(this).wrap('<div class="ik_loansys_date_field"></div>')
            jQuery('<span>'+jQuery(this).attr('placeholder')+'</span>').insertBefore(jQuery(this));
        }
    });
}

//Function to format phone number
function ik_loansys_format_phone(phoneNumberInput){
    var phoneNumber = jQuery(phoneNumberInput).val();
    if (phoneNumber != ''){
        var phoneNumber = phoneNumber.replace(/[^0-9]+/g, "");
        var phoneNumber = phoneNumber.replace("-", "");
        if (phoneNumber.length > 6 && phoneNumber.length < 14){
            if (phoneNumber.length == 7){ //4102721
                var first3Numbers = phoneNumber.substr(0, 3);
                var last4Numbers = phoneNumber.substr(3, 4);
                var phone_formated = first3Numbers+'-'+last4Numbers; // 410-2721
            } else if (phoneNumber.length == 10){ // 3524102721
                var first3Numbers = phoneNumber.substr(0, 3);
                var second3Numbers = phoneNumber.substr(3, 3);
                var last4Numbers = phoneNumber.substr(6, 4);
                var phone_formated = '('+first3Numbers+') '+second3Numbers+'-'+last4Numbers; // (352) 410-2721
            } else if (phoneNumber.length == 12){ //543764616757
                var first2Numbers = phoneNumber.substr(0, 2);
                var areaCode = phoneNumber.substr(2, 3);
                var threeNumbers = phoneNumber.substr(5, 3);
                var lastDigits = phoneNumber.substr(8, 4);
                var phone_formated = '+'+first2Numbers+' ('+areaCode+') '+threeNumbers+'-'+lastDigits; //+54 (376) 461-6757
            } else if (phoneNumber.length == 13){
                var first2Numbers = phoneNumber.substr(0, 2);
                var thirdDigit = phoneNumber.substr(2, 1);
                var areaCode = phoneNumber.substr(3, 3);
                var $threeNumbers = phoneNumber.substr(6, 3);
                var lastDigits = phoneNumber.substr(9, 4);
                var phone_formated = '+'+first2Numbers+' '+thirdDigit+' ('+areaCode+') '+$threeNumbers+'-'+lastDigits; //+54 9 (376) 461-6757
            } else {
                jQuery(phoneNumberInput).val('Error');
                setTimeout(function(){
                    jQuery(phoneNumberInput).val('');
                }, 1500);
            }
            
            jQuery(phoneNumberInput).val(phone_formated);
            
        } else {
            jQuery(phoneNumberInput).val('Error');
            setTimeout(function(){
                jQuery(phoneNumberInput).val('');
            }, 1500);
        }
    }
}
</script>
<?php
    }
?>
</div>    
<span><?php echo $message; ?></span>

<?php
} else {
    echo '<div class="just-submited">'.__( 'You already submited this form.', 'loansys' ).'</div>';
}
?>