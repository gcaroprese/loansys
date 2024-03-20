<?php
// Content for Recaptcha V2 template on Wordpress Dashboard WP-ADMIN
?>

<?php
if(isset($showDataMenu)){ 
    
    // I check if there's a recaptcha already set in order to know if I have to insert or update and also show the value in the input form
    global $wpdb;
    $querycaptchakey = "SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'loansys_recaptcha_k' ORDER BY option_id DESC";
    $captchakey = $wpdb->get_results($querycaptchakey);
    $querycaptchakey = "SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'loansys_recaptcha_use' ORDER BY option_id DESC";
    $captchause = $wpdb->get_results($querycaptchakey);


if ($captchakey != NULL){ 
    $formaction = "update";
    // I get captcha secret key
    global $wpdb;
    $querycaptchakey = "SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'loansys_recaptcha_s' ORDER BY option_id DESC";
    $captchaskey = $wpdb->get_results($querycaptchakey);
    $recaptchakey = $captchakey[0]->option_value;
    $recaptchasecret = $captchaskey[0]->option_value;
        // if $captchause = 1 I check input checkbox below in the form
        if ($captchause[0]->option_value == 1){
            $recapchacheck = "checked";
        } else {
            $recapchacheck = "";
        }
} else {
    $formaction = "insert";
    $recaptchakey = "";
    $recaptchasecret = "";
    $recapchacheck = "";
}

    
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $loansys_recaptcha_k = sanitize_text_field($_POST['recapkey']);
        $loansys_recaptcha_s = sanitize_text_field($_POST['recapseckey']);
        if (!empty($_POST['use-recaptcha'])){
            $checkbox = "1";
        } else {
            $checkbox = "0";
        }
        global $wpdb;
        $data_recapkey  = array (
                'option_id' => NULL,
                'option_name'=> 'loansys_recaptcha_k', 
                'option_value'=>$loansys_recaptcha_k,
                'autoload' => 'yes',
        );
        $data_recapseckey  = array (
                'option_id' => NULL,
                'option_name'=> 'loansys_recaptcha_s', 
                'option_value'=>$loansys_recaptcha_s,
                'autoload' => 'yes',
        );
        $data_recapsecuse  = array (
                'option_id' => NULL,
                'option_name'=> 'loansys_recaptcha_use', 
                'option_value'=>$checkbox,
                'autoload' => 'yes',
        );
        
        $tableInsert = $wpdb->prefix.'options';
        
        // I check with $formaction if I have to an insert or update
        if ($formaction == "insert"){
            $rowResultckey = $wpdb->insert($tableInsert,  $data_recapkey , $format = NULL);
            $rowResultcsec = $wpdb->insert($tableInsert,  $data_recapseckey , $format = NULL);
            $rowResultcuse = $wpdb->insert($tableInsert,  $data_recapsecuse , $format = NULL);
        } else {
            $rowResultckey = $wpdb->get_results("UPDATE " . $tableInsert . " SET option_value = '".$loansys_recaptcha_k."' WHERE option_name LIKE 'loansys_recaptcha_k'");
            $rowResultcsec = $wpdb->get_results("UPDATE " . $tableInsert . " SET option_value = '".$loansys_recaptcha_s."' WHERE option_name LIKE 'loansys_recaptcha_s'");
            $rowResultcuse = $wpdb->get_results("UPDATE " . $tableInsert . " SET option_value = '".$checkbox."' WHERE option_name LIKE 'loansys_recaptcha_use'");
        }
        echo "<script type='text/javascript'>
           window.location = '".get_site_url()."/wp-admin/admin.php?page=loansys_admin_recaptcha&data=1'
      </script>";
    }
?>
<style>
    .error{display: none;}
    .recaptcha-loansys-body{
        padding: 40px 3%;
    }
    label, input{
        display: block;
    }
    input{
        margin-bottom: 20px;
    }
    input[type=text], input[type=password]{
        padding: 0 8px;
        line-height: 2;
        min-height: 30px;
        box-shadow: 0 0 0 transparent;
        border-radius: 4px;
        border: 1px solid #7e8993;
        background: #fff;
    }
    input[type=submit]{
        padding: 6px 12px;
        background: #000;
        color: #fff;
        border: 0;
        border-radius: 12px;
    }
    @media (min-width: 767px){
        input[type=text], input[type=password]{
            width: 340px;
        }
    }
</style>
<div class="recaptcha-loansys-body">
    <h2>Recaptcha V2 - Loansys</h2>
    <p>More info about <a href="https://www.google.com/recaptcha/admin" target="_blank">Google Recaptcha</a></p>
    <form action="" method="post" class="veranschlagung-form" enctype="multipart/form-data" autocomplete="no">
        <label for="recaptcha-key">
            <span>Site Key</span>
            <input type="text" name="recapkey" value="<?php echo $recaptchakey; ?>" />
        </label>
        <label for="recaptcha-secret-key">
            <span>Secret Key</span>
            <input type="password" readonly="readonly" onfocus="this.removeAttribute('readonly');" name="recapseckey" value="<?php echo $recaptchasecret; ?>" />
        </label>
        <label>
            <input type="checkbox" name="use-recaptcha" <?php echo $recapchacheck; ?> value="1">
            <span>Enable Recaptcha.</span>
        </label></p>

        <input type="submit" value="Save">
    </form>
    <?php 
        if (isset($_GET['data'])) {
            if (intval($_GET['data']) == 1){
                echo "Updated.";
            }
        }
    ?>
</div>
<?php

}


?>