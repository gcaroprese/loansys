<?php

// Content Loans Admin Page
if (isset($showDataMenu)){
    //I add loansys scripts and CSS
    wp_enqueue_style( 'ik_loansys_adminpanel_css' );
    wp_enqueue_style( 'ik_loansys_select2css' );
    wp_enqueue_script( 'ik_loansys_select2' );
    wp_enqueue_script( 'ik_loansys_convert_to_csv' );
    
// Amount of listings I'm gonna show per page
$limitListings = 100;

//If search was made
if ($_SERVER['REQUEST_METHOD'] == 'POST' ){
    $id_demande = sanitize_text_field($_POST['id_demande']); 
    $statut = sanitize_text_field($_POST['statut']); 
    $prenom = sanitize_text_field($_POST['prenom']); 
    $nom = sanitize_text_field($_POST['nom']); 
    $telephone = sanitize_text_field($_POST['telephone']); 
    $cellulaire = sanitize_text_field($_POST['cellulaire']); 
    $courriel = sanitize_text_field($_POST['courriel']); 
    $raison_refus = sanitize_text_field($_POST['raison_refus']); 
    $request_datetime_from = date('Y-m-d', strtotime( $_POST['request_datetime_from']));
    $request_datetime_to = date('Y-m-d', strtotime( $_POST['request_datetime_to']));
    
    if ($id_demande != '' || $statut != '' || $prenom != '' || $nom != '' || $telephone != '' || $cellulaire != '' || $courriel != '' || $raison_refus != ''){
        
        $whereSearch = "WHERE ";
        
        if ($id_demande != ""){
            $whereSearch .= "guid='".$id_demande."' AND ";
        }
        if ($statut != ""){
            $whereSearch .= "status='".$statut."' AND ";
        }
        if ($prenom != ""){
            $whereSearch .= "firstname='".$prenom."' AND ";
        }
        if ($nom != ""){
            $whereSearch .= "lastname='".$nom."' AND ";
        }
        if ($telephone != ""){
            $whereSearch .= "phone='".$telephone."' AND ";
        }
        if ($cellulaire != ""){
            $whereSearch .= "mobilephone='".$cellulaire."' AND ";
        }
        if ($courriel != ""){
            $whereSearch .= "fax='".$courriel."' AND ";
        }
        if ($raison_refus != ""){
            $whereSearch .= "rejection_reason='".$raison_refus."' AND ";
        }
        
        //I delete the last AND
        $whereSearch = substr($whereSearch, 0, -5);;
        
    }
    
    if (($request_datetime_to != '1970-01-01' && $request_datetime_to != NULL && $request_datetime_to != '0000-00-00') || ($request_datetime_from != '1970-01-01' && $request_datetime_from != NULL && $request_datetime_from != '0000-00-00')){
        if (!isset($whereSearch)){
            $whereSearch = "WHERE ";
        } else {
            $whereSearch .= " AND ";
        }
        if ($request_datetime_to != '1970-01-01' && $request_datetime_to != NULL && $request_datetime_to != '0000-00-00'){
            $whereSearch .= "request_datetime < '".$request_datetime_to."'";
            $request_datetimevalue_to = date('Y-m-d', strtotime( $_POST['request_datetime_to']));
        } else {
            $request_datetimevalue_to = '';
        }
        if ($request_datetime_from != '1970-01-01' && $request_datetime_from != NULL && $request_datetime_from != '0000-00-00'){
            $whereSearch .= " AND request_datetime > '".$request_datetime_from."'";
                $request_datetimevalue_from = date('Y-m-d', strtotime( $_POST['request_datetime_from']));
        } else {
            $request_datetimevalue_from = '';
        }
    } else {
        $request_datetimevalue_to = '';
        $request_datetimevalue_from = '';
    }
} else {
    //I define the variables to show as search fields value
    $id_demande = '';
    $statut = '';
    $statutText = 'Status';
    $prenom = '';
    $nom = '';
    $telephone = '';
    $cellulaire = '';
    $courriel = '';
    $raison_refus = '';
    $raison_refusText = 'Reason of Rejection';
    $request_datetimevalue_from = '';
    $request_datetimevalue_to = '';
}

if (isset($_GET['searchdata'])){
    $keywordSearch = sanitize_text_field($_GET['searchdata']);
    $searchValue = 'value="'.$keywordSearch.'"';

} else{
    $searchValue = '';
}

// I get the value from the pagination number from URL if that exists
if (isset($_GET["listpage"])){
    // I check if value is integer to avoid errors
    if (strval($_GET["listpage"]) == strval(intval($_GET["listpage"])) && $_GET["listpage"] > 0){
        $paged = intval($_GET["listpage"]);
    } else {
        $paged = 1;
    }
} else {
     $paged = 1;
}


// I get the value from the order data if that exists
if (isset($_GET["orderby_list"])){
    //I sanitize order 
    $orderFilter = sanitize_text_field($_GET["orderby_list"]);
    // I check if value is integer to avoid errors
    if ($orderFilter == 'request_datetime' || $orderFilter == 'firstname' || $orderFilter == 'lastname' || $orderFilter == 'phone' || $orderFilter == 'mobilephone' || $orderFilter == 'codepostal' || $orderFilter == 'amount_requested' || $orderFilter == 'client_associated' || $orderFilter == 'modification_datetime'){
        $orderFilter = $orderFilter;
    } else {
        $orderFilter = "request_datetime";
    }
} else {
     $orderFilter = "request_datetime";
}

// I get the value from the order data if that exists
if (isset($_GET["orderdata_list"])){
    //I sanitize order 
    $order = sanitize_text_field($_GET["orderdata_list"]);
    // I check if value is integer to avoid errors
    if ($order == "ASC"){
        $orderby = $orderFilter." ASC";
    } else {
        $orderby = $orderFilter." DESC";
    }
} else {
    $order = "DESC";
    $orderby = $orderFilter." ".$order;
}
?>
<div id="ik-loansys-admin-panel">
    <div id="ik-loansys-searchbox">
        <form action="" method="post" name="ik_loansys_searchdata_form" id="ik_loansys_searchdata_form" enctype="multipart/form-data">
            <label class="ik-search-input-width">
    			<span>Request ID</span>
    			<input type="text" name="id_demande" placeholder="Request ID" value="<?php echo $id_demande; ?>" />
    		</label>
            <label class="ik-search-input-width">
    			<span>Status</span>
    			<select name="statut">
                    <option value="
                    <?php echo $statut; ?>">
                        <?php echo $statutText; ?>
                    </option>
                    <option value="New">New</option>
                    <option value="Waiting">Waiting</option>
                    <option value="Accepted">Accepted</option>
                    <option value="Refused">Refused</option>
                    <option value="In collection">In collection</option>
                    <option value="Fraudster">Fraudster</option>
                    <option value="Renewal possible">Renewal possible</option>
                    <option value="Consumer proposal">Consumer proposal</option>
                    <option value="Bankruptcy">Bankruptcy</option>
                    <option value="Duplicates">Duplicates</option>
                </select>
    		</label>
            <label class="ik-search-input-width">
    			<span>First Name</span>
    			<input type="text" name="prenom" placeholder="First Name" value="<?php echo $prenom; ?>" />
    		</label>
            <label class="ik-search-input-width">
    			<span>Last Name</span>
    			<input type="text" name="nom" placeholder="Last Name" value="<?php echo $nom; ?>" />
    		</label>
            <label class="ik-search-input-width">
    			<span>Phone</span>
    			<input type="text" name="telephone" placeholder="Phone" value="<?php echo $telephone; ?>" />
    		</label>
            <label class="ik-search-input-width">
    			<span>Mobile Phone</span>
    			<input type="text" name="cellulaire" placeholder="Mobile Phone" value="<?php echo $cellulaire; ?>" />
    		</label>
            <label class="ik-search-input-width">
    			<span>Email</span>
    			<input type="email" name="courriel" placeholder="Email" value="<?php echo $courriel; ?>" />
    		</label>
            <label class="ik-search-input-width">
    			<span>Reason of Rejection</span>
		        <select name="rejection_reason">
                    <option value="
                    <?php echo $raison_refus; ?>">
                        <?php echo $raison_refusText; ?>
                    </option>
                    <option value="New">New</option>
                    <option value="Waiting">Waiting</option>
                    <option value="Accepted">Accepted</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Missing Information">Missing Information</option>
                    <option value="Missing Information (IBV)">Missing Information (IBV)</option>
                    <option value="Awaiting contract">Awaiting contract</option>
                    <option value="Duplicates">Duplicates</option>
                    <option value="Client canceled the request">Client canceled the request</option>
                    <option value="Active Payment Agreement">Active Payment Agreement</option>
                    <option value="Clone of demand">Clone of demande</option>
                    <option value="Avertisement">Avertisement</option>
                    <option value="Bankruptcy">Bankruptcy</option>
                </select>
    		</label>
            
            <label class="ik-search-input-shorter">
    			<span>Between this date</span>
    			<input type="text" class="walcf7-datepicker" name="request_datetime_from" placeholder="Entre cette date" value="<?php echo $request_datetimevalue_from; ?>" />		
    		</label>
            <label class="ik-search-input-shorter">
    			<span>And this date</span>
    			<input type="text" class="walcf7-datepicker" name="request_datetime_to" placeholder="Et cette date" value="<?php echo $request_datetimevalue_to; ?>" />		
    		</label>
            
            <span class="ik-loansys-search-submit"><button class="button button-primary ik-loansys-reset-search">Reset</button></span>            
            <span class="ik-loansys-search-submit"><input type="submit" class="button button-primary" value="Search" /></span>
            <div class="panelvisible"></div>
        </form>
        <div class="ik-loansys-panel-name">Search</div>
    </div>
    <div id="ik-loansys-application-list" filter_type="<?php echo $orderFilter; ?>" order_direction="<?php echo $order; ?>">
        <div class="ik-loansys-panel-list">
            <div class="ik-loansys-title">
                <h3>List of Requests</h3>
            </div>
            <div class="ik-loansys-export-csv">
                <button id="ik_exportar_csv" class="button button-primary" onclick="exportTableToCSV('loans-request-list-<?php echo date('Y-m-d'); ?>.csv')">Export to CSV</button>
            </div>
        </div>
        <table>
          <thead class="filter-by-<?php echo $orderFilter.'-'.$order; ?>">
            <tr>
                <th>
                    <input id="ik-loansys-list-checkbox-main" class="ik-loansys-list-checkbox" type="checkbox" name="bulk_applications[]" value="0">
                </th>
                <th id="ik_loansys_request_datetime" class="ik_loansys_datahead_list ik_loansys_data_to_export">Request Date</th>
                <th id="ik_loansys_firstname" class="ik_loansys_datahead_list ik_loansys_data_to_export">First Name</th>
                <th id="ik_loansys_lastname" class="ik_loansys_datahead_list ik_loansys_data_to_export">Last Name</th>
                <th id="ik_loansys_phone" class="ik_loansys_datahead_list ik_loansys_data_to_export">Phone</th>
                <th id="ik_loansys_mobilephone" class="ik_loansys_datahead_list ik_loansys_data_to_export">Mobile Phone</th>
                <th id="ik_loansys_codepostal" class="ik_loansys_datahead_list ik_loansys_data_to_export">Fax</th>
                <th id="ik_loansys_amount_requested" class="ik_loansys_datahead_list ik_loansys_data_to_export">Amount</th>
                <th id="ik_loansys_client_associated" class="ik_loansys_datahead_list ik_loansys_data_to_export">Associate Client</th>
                <th id="ik_loansys_modification_datetime" class="ik_loansys_datahead_list ik_loansys_data_to_export">Date Modified</th>
                <th class="ik_loansys_data_to_export">IBV</th>
                <th>Actions</th>
            </tr>
          </thead>
          <tbody>
<?php 

$offsetPage = $paged - 1;

//In case where sentence is not set
if (!isset($whereSearch)){
    $whereSearch = NULL;
}

// I check the total of pages
$total_records_to_show = ik_loansys_get_data_count($whereSearch);

//I load the array of listing results
$loanApplications = ik_loansys_get_data($limitListings, $offsetPage, $orderby, $whereSearch);

if ($loanApplications != false){
    foreach ($loanApplications as $applicantData){

?>
            <tr id="ik-loansys-application-<?php echo $applicantData->id; ?>" data-list="<?php echo $applicantData->id; ?>">
                <td>
                    <input class="ik-loansys-list-checkbox-select ik-loansys-list-checkbox" type="checkbox" name="bulk_applications[]" value="<?php echo $applicantData->id; ?>">
                </td>
                <td  class="ik_loansys_data_to_export"class="ik_loansys_data_to_export"><?php echo $applicantData->request_datetime; ?></td>
                <td class="ik_loansys_data_to_export"><?php echo $applicantData->firstname; ?></td>
                <td class="ik_loansys_data_to_export"><?php echo $applicantData->lastname; ?></td>
                <td class="ik_loansys_data_to_export"><?php echo $applicantData->phone; ?></td>
                <td class="ik_loansys_data_to_export"><?php echo $applicantData->mobilephone; ?></td>
                <td class="ik_loansys_data_to_export" class="ik_loansys_data_to_export"><?php echo $applicantData->fax; ?></td>
                <td class="ik_loansys_data_to_export">$<?php echo $applicantData->amount_requested; ?></td>
                <td class="ik_loansys_data_to_export"><?php echo ik_loansys_data_retrieve_checker($applicantData->client_associated); ?></td>
                <td class="ik_loansys_date_modified ik_loansys_data_to_export"><?php echo $applicantData->modification_datetime; ?></td>
                <td>
                    <a href="<?php echo $applicantData->IBV; ?>" target="_blank">
                      <span class="dashicons dashicons-admin-page"></span>
                    </a>
                </td>
              <td><span class="dashicons dashicons-edit-page ik-loansys-edit-list-id"  data-list="<?php echo $applicantData->id; ?>"></span><span class="dashicons dashicons-trash ik-loansys-delete-list-id"  data-list="<?php echo $applicantData->id; ?>"></span></td>
            </tr>
            <div id="ik-loansys-popup-application-<?php echo $applicantData->id; ?>" data-list="<?php echo $applicantData->id; ?>" class="ik-loansys-popup-edit-list">
                <span class="dashicons dashicons-dismiss ik-loansys-closepopup"></span>
                <div class="ik-loansys-title-popup">
                    <span><?php echo $applicantData->firstname.' '.$applicantData->lastname; ?> - Date: <?php echo $applicantData->request_datetime; ?></span>
                </div>
                <div class="ik-loansys-tab-navigation">
                    <ul>
                        <li class="ik-loansys-menu-tab active" tab="1">Credit application</li>
                        <li class="ik-loansys-menu-tab" tab="2">Personal information</li>
                        <li class="ik-loansys-menu-tab" tab="3">References</li>
                        <li class="ik-loansys-menu-tab" tab="4">Employer</li>
                        <li class="ik-loansys-menu-tab" tab="5">Requests on this site</li>
                        <li class="ik-loansys-menu-tab" tab="6">Requests on other sites</li>
                        <li class="ik-loansys-menu-tab" tab="7">Creditbook</li>
                    </ul>
                </div>
                <div class="ik-loansys-popup-changes-fields">
                    <div tab="1" class="ik-loansys-tab-edit active">
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Amount requested (CA$)</span>
			                <input type="text" name="amount_requested" placeholder="Amount requested" value="<?php echo $applicantData->amount_requested; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Status</span>
			                <select name="status">
			                    <?php echo ik_loansys_data_retrieve_selectoption($applicantData->status); ?>
			                    <option value="New">New</option>
			                    <option value="Waiting">Waiting</option>
			                    <option value="Accepted">Accepted</option>
			                    <option value="Refused">Refused</option>
			                    <option value="In collection">In collection</option>
			                    <option value="Fraudster">Fraudster</option>
			                    <option value="Renewal possible">Renewal possible</option>
			                    <option value="Consumer proposal">Consumer proposal</option>
			                    <option value="Bankruptcy">Bankruptcy</option>
			                    <option value="Duplicates">Duplicates</option>
			                </select>
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Current IBV URL</span>
			                <input type="text" name="IBV" placeholder="IBV URL" value="<?php echo $applicantData->IBV; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Associate Client</span>
			                <input type="text" name="client_Associated" placeholder="Associate Client" value="<?php echo ik_loansys_data_retrieve_checker($applicantData->client_associated); ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reason of Rejection</span>
			                <select name="rejection_reason">
			                    <?php echo ik_loansys_data_retrieve_selectoption($applicantData->rejection_reason); ?>
			                    <option value="New">New</option>
			                    <option value="Waiting">Waiting</option>
			                    <option value="Accepted">Accepted</option>
			                    <option value="Rejected">Rejected</option>
			                    <option value="Missing Information">Missing Information</option>
			                    <option value="Missing Information (IBV)">Missing Information (IBV)</option>
			                    <option value="Awaiting contract">Awaiting contract</option>
			                    <option value="Duplicates">Duplicates</option>
			                    <option value="Client canceled the request">Client canceled the request</option>
			                    <option value="Active Payment Agreement">Active Payment Agreement</option>
			                    <option value="Request Cloned">Request Cloned</option>
			                    <option value="Avertisement">Avertisement</option>
			                    <option value="Bankruptcy">Bankruptcy</option>
			                </select>
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Request Date</span>
			                <input type="text" class="walcf7-datepicker" name="request_datetime" placeholder="Request Date" value="<?php echo $applicantData->request_datetime; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Request IP Address</span>
			                <input type="text" name="IP_address" placeholder="IP Address" value="<?php echo $applicantData->IP_address; ?>" />		
		                </label>
		                <textarea name="comments_admin" placeholder="Comments" class="ik-loansys-edit-info-entry ik-loansys-edit-info-textarea-field"><?php echo $applicantData->comments_admin; ?></textarea>
                    </div>
                    <div tab="2" class="ik-loansys-tab-edit">
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>First Name</span>
			                <input type="text" name="firstname" placeholder="First Name" value="<?php echo $applicantData->firstname; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Last Name</span>
			                <input type="text" name="lastname" placeholder="Last Name" value="<?php echo $applicantData->lastname; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Birth Date</span>
			                <input type="text" class="walcf7-datepicker" name="birthdate" placeholder="Birth Date" value="<?php echo $applicantData->birthdate; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Moving Date</span>
			                <input type="text" class="walcf7-datepicker" name="request_datetime" placeholder="Moving Date" value="<?php echo $applicantData->datemoved; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Address (Line 1)</span>
			                <input type="text" name="address" placeholder="Address (Line 1)" value="<?php echo $applicantData->address; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Address (Line 2)</span>
			                <input type="text" name="adresse2" placeholder="Address (Line 2)" value="<?php echo $applicantData->adresse2; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>City</span>
			                <input type="text" name="city" placeholder="City" value="<?php echo $applicantData->city; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Province</span>
			                <select name="province">
			                    <?php echo ik_loansys_data_retrieve_selectoption($applicantData->province); ?>
			                    <option value="Alberta">Alberta</option>		
			                    <option value="British Columbia">British Columbia</option>		
			                    <option value="Manitoba">Manitoba</option>		
			                    <option value="New Brunswick">New Brunswick</option>		
			                    <option value="Newfoundland and Labrador">Newfoundland and Labrador</option>		
			                    <option value="Nova Scotia">Nova Scotia</option>
			                    <option value="Ontario">Ontario</option>		
			                    <option value="Prince Edward Island">Prince Edward Island</option>		
			                    <option value="Quebec">Quebec</option>		
			                    <option value="Saskatchewan">Saskatchewan</option>		
			                    <option value="Northwest Territories"Northwest Territories</option>		
			                    <option value="Nunavut">Nunavut</option>		
			                    <option value="Yukon">Yukon</option>		
			                </select>
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Country</span>
			                <input type="text" name="country" placeholder="Country" value="<?php echo $applicantData->country; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Code Postal</span>
			                <input type="text" name="codepostal" placeholder="Code Postal" value="<?php echo $applicantData->codepostal; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Phone</span>
			                <input type="text" name="phone" placeholder="Phone" value="<?php echo $applicantData->phone; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Mobile Phone</span>
			                <input type="text" name="mobilephone" placeholder="Mobile Phone" value="<?php echo $applicantData->mobilephone; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Fax</span>
			                <input type="text" name="fax" placeholder="Fax" value="<?php echo $applicantData->fax; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Courriel</span>
			                <input type="text" name="email" placeholder="Courriel" value="<?php echo $applicantData->email; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Language</span>
			                <select name="language">
			                    <?php echo ik_loansys_data_retrieve_selectoption($applicantData->language); ?>
			                    <option value="English">English</option>
			                    <option value="French">French</option>
			                </select>
		                </label>
                    </div>
                    <div tab="3" class="ik-loansys-tab-edit">
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 1 (First Name)</span>
			                <input type="text" name="ref1_prenom" placeholder="Reference 1 (First Name)" value="<?php echo $applicantData->ref1_prenom; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 1 (Last Name)</span>
			                <input type="text" name="ref1_nom" placeholder="Reference 1 (Last Name)" value="<?php echo $applicantData->ref1_nom; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 1 (Phone)</span>
			                <input type="text" name="ref1_telephone" placeholder="Reference 1 (Phone)" value="<?php echo $applicantData->ref1_telephone; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 1 (Email)</span>
			                <input type="text" name="ref1_courriel" placeholder="Reference 1 (Email)" value="<?php echo $applicantData->ref1_courriel; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 1 (Relationship)</span>
			                <input type="text" name="ref1_lien" placeholder="Reference 1 (Relationship)" value="<?php echo $applicantData->ref1_lien; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 2 (First Name)</span>
			                <input type="text" name="ref2_prenom" placeholder="Reference 2 (First Name)" value="<?php echo $applicantData->ref2_prenom; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 2 (Last Name)</span>
			                <input type="text" name="ref2_nom" placeholder="Reference 2 (Last Name)" value="<?php echo $applicantData->ref2_nom; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 2 (Phone)</span>
			                <input type="text" name="ref2_telephone" placeholder="Reference 2 (Phone)" value="<?php echo $applicantData->ref2_telephone; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 2 (Email)</span>
			                <input type="text" name="ref2_courriel" placeholder="Reference 2 (Email)" value="<?php echo $applicantData->ref2_courriel; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 2 (Relationship)</span>
			                <input type="text" name="ref2_lien" placeholder="Reference 2 (Relationship)" value="<?php echo $applicantData->ref2_lien; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 3 (First Name)</span>
			                <input type="text" name="ref3_prenom" placeholder="Reference 3 (First Name)" value="<?php echo $applicantData->ref3_prenom; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 3 (Last Name)</span>
			                <input type="text" name="ref3_nom" placeholder="Reference 3 (Last Name)" value="<?php echo $applicantData->ref3_nom; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 3 (Phone)</span>
			                <input type="text" name="ref3_telephone" placeholder="Reference 3 (Phone)" value="<?php echo $applicantData->ref3_telephone; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 3 (Email)</span>
			                <input type="text" name="ref3_courriel" placeholder="Reference 3 (Email)" value="<?php echo $applicantData->ref3_courriel; ?>" />		
		                </label>
                         <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Reference 3 (Relationship)</span>
			                <input type="text" name="ref3_lien" placeholder="Reference 3 (Relationship)" value="<?php echo $applicantData->ref3_lien; ?>" />		
		                </label>
                     </div>
                    <div tab="4" class="ik-loansys-tab-edit">
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Company Name</span>
			                <input type="text" name="employer_company" placeholder="Company Name" value="<?php echo $applicantData->employer_company; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Company Phone</span>
			                <input type="text" name="employer_phone" placeholder="Company Phone" value="<?php echo $applicantData->employer_phone; ?>" />	
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Company Phone Extension</span>
			                <input type="text" name="employeur_telephone_ext" placeholder="Company Phone Extension" value="<?php echo $applicantData->employeur_telephone_ext; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Supervisor</span>
			                <input type="text" name="employer_supervisor" placeholder="Supervisor" value="<?php echo $applicantData->employer_supervisor; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Pay frequency</span>
			                <select name="payment_freq">
			                    <?php echo ik_loansys_data_retrieve_selectoption($applicantData->payment_freq); ?>
			                 <?php echo constant("IK_LOANSYS_PAYMENT_FREQ"); ?>
			                </select>
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Payday</span>
			                <input type="text" name="payment_day" placeholder="Payday" value="<?php echo $applicantData->payment_day; ?>" />		
		                </label>
                        <label class="ik-loansys-edit-info-entry ik-loansys-edit-info-input-field">
			                <span>Hiring date</span>
			                <input type="text" name="hiring_date" placeholder="Hiring date" value="<?php echo $applicantData->hiring_date; ?>" />		
		                </label>
                    </div>
                    <div tab="5" class="ik-loansys-tab-edit">
                    	<div class="ik-loansys-tab-inner-title">
                    		<h4>Requests on this site</h4>
                    	</div>
                    	<div class="ik-loansys-datatable">
                    		<div class="ik-loansys-dataheadRow">
                    			<div class="ik-loansys-datacell" align="center">Note</div>
                    			<div class="ik-loansys-datacell" align="center">Site</div>
                    			<div class="ik-loansys-datacell" align="center">Date</div>
                    			<div class="ik-loansys-datacell" align="center">ID</div>
                    			<div class="ik-loansys-datacell" align="center">First Name</div>
                    			<div class="ik-loansys-datacell" align="center">Last Name</div>
                    			<div class="ik-loansys-datacell" align="center">Phone</div>
                    			<div class="ik-loansys-datacell" align="center">Cell Phone</div>
                    			<div class="ik-loansys-datacell" align="center">Amount</div>
                    			<div class="ik-loansys-datacell" align="center">statut-msg</div>
                    			<div class="ik-loansys-datacell" align="center">IP</div>
                    			<div class="ik-loansys-datacell" align="center">Info</div>
                    		</div>
                    		<div class="ik-loansys-datarow">
                    			  <div class="ik-loansys-datacell"></div>
                    			<div class="ik-loansys-datacell"></div>
                    			<div class="ik-loansys-datacell"></div>
                    		</div>
                    	</div>
                    </div>
                    <div tab="6" class="ik-loansys-tab-edit">
                    	<div class="ik-loansys-tab-inner-title">
                    		<h4>Requests on other sites</h4>
                    	</div>
                    	<div class="ik-loansys-datatable">
                    		<div class="ik-loansys-dataheadRow">
                    			<div class="ik-loansys-datacell" align="center">Note</div>
                    			<div class="ik-loansys-datacell" align="center">Site</div>
                    			<div class="ik-loansys-datacell" align="center">Date</div>
                    			<div class="ik-loansys-datacell" align="center">ID</div>
                    			<div class="ik-loansys-datacell" align="center">First Name</div>
                    			<div class="ik-loansys-datacell" align="center">Last Name</div>
                    			<div class="ik-loansys-datacell" align="center">Phone</div>
                    			<div class="ik-loansys-datacell" align="center">Cell Phone</div>
                    			<div class="ik-loansys-datacell" align="center">Amount</div>
                    			<div class="ik-loansys-datacell" align="center">statut-msg</div>
                    			<div class="ik-loansys-datacell" align="center">IP</div>
                    			<div class="ik-loansys-datacell" align="center">Info</div>
                    		</div>
                    		<div class="ik-loansys-datarow">
                    			  <div class="ik-loansys-datacell"></div>
                    			<div class="ik-loansys-datacell"></div>
                    			<div class="ik-loansys-datacell"></div>
                    		</div>
                    	</div>
                    </div>
                    <div tab="7" class="ik-loansys-tab-edit">
                    	<div class="ik-loansys-tab-inner-title">
                    		<h4>Creditbook</h4>
                    	</div>
                    	<div class="ik-loansys-datatable">
                    		<div class="ik-loansys-dataheadRow">
                    			<div class="ik-loansys-datacell" align="center">Note</div>
                    			<div class="ik-loansys-datacell" align="center">Site</div>
                    			<div class="ik-loansys-datacell" align="center">Date</div>
                    			<div class="ik-loansys-datacell" align="center">ID</div>
                    			<div class="ik-loansys-datacell" align="center">First Name</div>
                    			<div class="ik-loansys-datacell" align="center">Last Name</div>
                    			<div class="ik-loansys-datacell" align="center">Phone</div>
                    			<div class="ik-loansys-datacell" align="center">Cell Phone</div>
                    			<div class="ik-loansys-datacell" align="center">Amount</div>
                    			<div class="ik-loansys-datacell" align="center">statut-msg</div>
                    			<div class="ik-loansys-datacell" align="center">IP</div>
                    			<div class="ik-loansys-datacell" align="center">Info</div>
                    		</div>
                    		<div class="ik-loansys-datarow">
                    			  <div class="ik-loansys-datacell"></div>
                    			<div class="ik-loansys-datacell"></div>
                    			<div class="ik-loansys-datacell"></div>
                    		</div>
                    	</div>
                    </div>    
                    <div class="ik-loansys-panel-list-save-buttons" data-list="<?php echo $applicantData->id; ?>">
                	    <button class="button button-primary ik-loansys-savebutton-cancel">Cancel</button>
                	    <button class="button button-primary ik-loansys-savebutton-save">
                	        <span class="ik-loansys-loading-button" style="display:none">
                	            <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/img/loading.gif" alt="loading gif">
                	            </span>Save</button>
                	</div>
                </div>
            <?php
            }
            ?>
            </div>
        </tbody>
    </table>
            <?php
        } else {
        ?>
            </tbody>
        </table>
        <div class="ik-loansys-nothing-to-show">Nothing to show</div>
        <?php
        }
        ?>
        <?php
    //I add the pagination
    
    //I get the total of pages to list based on $alunosListados
    $total_pages = $total_records_to_show / $limitListings;
	if ($total_pages != intval($total_pages)){
    	$total_pages = intval($total_pages) + 1;
	} else {
		$total_pages = intval($total_pages);
	}
    
    if ($total_records_to_show > $limitListings && $paged <= $total_pages){
		// I only want to show the last five and first 6
        echo '<div class="ik_loansys_panel_list_pagination">';
        for ($i = 1; $i <= $total_pages; $i++) {
				if ($paged == $i){
					$selectedPageN = ' button-primary';
				} else {
					$selectedPageN = "";
				}
				echo '<a class="button button-secondary'.$selectedPageN.'" href="'.basename($_SERVER['REQUEST_URI']).'&listpage='.$i.'">'.$i.'</a>';
        }
        echo '</div>';
    }

?>
    </div>
</div>
<script>
jQuery(document).ready(function ($) {
    jQuery('#ik-loansys-application-list select').select2();
    setInterval(function(){ 
        if (typeof datetimepicker === 'function'){
    			$('.walcf7-datepicker').datetimepicker({
    				lang:'fr',
    				timepicker:false,
    				format:'Y-m-d',
    				formatDate:'Y-m-d',
    			});
        }
    },750);
    setInterval(function(){ 
    var totalWidthInput = parseInt(jQuery('#ik_loansys_searchdata_form .ik-search-input-width input').width())+17;
    jQuery('#ik_loansys_searchdata_form .ik-search-input-width select').attr('style', 'min-width: '+totalWidthInput+'px');
    },70);
});
jQuery( "#ik-loansys-admin-panel #ik-loansys-searchbox" ).on( "click", ".panelvisible", function() {
    if (jQuery(this).hasClass('hiddenpanel')){
        jQuery(this).removeClass('hiddenpanel');
        jQuery('#ik-loansys-admin-panel #ik-loansys-searchbox .ik-loansys-panel-name').fadeOut(400);
        jQuery(this).fadeOut(600);
        jQuery(this).appendTo('#ik-loansys-admin-panel #ik-loansys-searchbox #ik_loansys_searchdata_form');
        setTimeout(function(){
            jQuery('#ik_loansys_searchdata_form').fadeIn(400);
        }, 400);
            jQuery(this).fadeIn(400);
    } else {
        jQuery('#ik_loansys_searchdata_form').fadeOut(300);
        jQuery(this).addClass('hiddenpanel');
        jQuery(this).appendTo('#ik-loansys-admin-panel #ik-loansys-searchbox');
        setTimeout(function(){
            jQuery('#ik-loansys-admin-panel #ik-loansys-searchbox .ik-loansys-panel-name').fadeIn(400);
        }, 400);
    }
});
jQuery( "#ik-loansys-application-list .ik-loansys-popup-edit-list .ik-loansys-tab-navigation ul" ).on( "click", "li.ik-loansys-menu-tab", function() {
    if (jQuery(this).is('[tab]')){
        jQuery('#ik-loansys-application-list .ik-loansys-popup-edit-list .ik-loansys-tab-navigation ul li').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('#ik-loansys-application-list .ik-loansys-popup-changes-fields .ik-loansys-tab-edit').removeClass('active');
        jQuery('#ik-loansys-application-list .ik-loansys-popup-changes-fields .ik-loansys-tab-edit[tab='+jQuery(this).attr('tab')+']').addClass('active');
    }
});

jQuery( "#ik-loansys-application-list" ).on( "click", ".ik-loansys-edit-list-id", function() {
    jQuery('.ik-loansys-popup-edit-list').fadeOut(500);
    jQuery('#ik-loansys-popup-application-'+jQuery(this).attr("data-list")).fadeIn(500);
    jQuery('#ik-loansys-popup-application-'+jQuery(this).attr("data-list")).attr("style", "display: block; z-index: 9999999;");

});

jQuery( "#ik-loansys-application-list .ik-loansys-popup-edit-list" ).on( "click", ".ik-loansys-closepopup", function() {
    jQuery('#ik-loansys-application-list .ik-loansys-popup-edit-list').fadeOut(600);
});
jQuery( "#ik-loansys-application-list .ik-loansys-popup-edit-list" ).on( "click", ".ik-loansys-savebutton-cancel", function() {
    location.reload();
});
jQuery( "#ik-loansys-searchbox" ).on( "click", ".ik-loansys-reset-search", function() {
    document.getElementById("ik_loansys_searchdata_form").reset();
    jQuery('#ik_loansys_searchdata_form input').val('');
    jQuery('#ik_loansys_searchdata_form select').val('');
    jQuery('#ik_loansys_searchdata_form textarea').val('');
    document.getElementById("ik_loansys_searchdata_form").submit();
});
jQuery( "#ik-loansys-application-list" ).on( "click", "table th.ik_loansys_datahead_list", function() {
    var order_direction = jQuery('#ik-loansys-application-list').attr('order_direction');
    var filter_type = jQuery('#ik-loansys-application-list').attr('filter_type');
    var data_to_filter = jQuery(this).attr('id').replace('ik_loansys_', '');
    if (order_direction == 'ASC'){
        order_contrast = 'DESC';
    } else {
        order_contrast = 'ASC';
    }
        if (filter_type == data_to_filter){
            filter_order = order_contrast;
        } else {
            filter_order = order_direction;
        }
        window.location.replace("<?php echo basename($_SERVER['REQUEST_URI']);
; ?>&orderby_list="+data_to_filter+"&orderdata_list="+filter_order);
});

jQuery('#ik-loansys-application-list .ik-loansys-list-checkbox').on('change', function() { 
    if (jQuery(this).attr('id') == 'ik-loansys-list-checkbox-main'){
        
        if (jQuery(this).hasClass('ik-loansys-list-checkbox-selected')){
            jQuery(this).removeClass('ik-loansys-list-checkbox-selected');
            var propertyCheckbox = false;
            jQuery('#ik-loansys-application-list tbody tr').removeClass('ik-loansys-application-id-selected');

        } else {
            jQuery(this).addClass('ik-loansys-list-checkbox-selected');
            jQuery('#ik-loansys-application-list tbody tr').addClass('ik-loansys-application-id-selected');
            var propertyCheckbox = true;
        }
        
        jQuery('#ik-loansys-application-list tbody .ik-loansys-list-checkbox-select').prop('checked', propertyCheckbox);
        jQuery('#ik-loansys-application-list tbody .ik-loansys-list-checkbox-select').prop('checked', propertyCheckbox);
        
    } else {
        if (jQuery(this).hasClass('ik-loansys-list-checkbox-selected')){
            jQuery(this).removeClass('ik-loansys-list-checkbox-selected');
            jQuery(this).parent().parent().removeClass('ik-loansys-application-id-selected');
        } else {
            jQuery(this).addClass('ik-loansys-list-checkbox-selected');
            jQuery(this).parent().parent().addClass('ik-loansys-application-id-selected');
        }        
        jQuery('#ik-loansys-list-checkbox-main').removeClass('ik-loansys-list-checkbox-selected');
        jQuery('#ik-loansys-list-checkbox-main').prop('checked', false);
    }


});

jQuery( "#ik-loansys-application-list .ik-loansys-popup-edit-list" ).on( "click", ".ik-loansys-savebutton-save", function() {
    
    var button_save_data = jQuery(this);

    //I show loading image
    button_save_data.find('.ik-loansys-loading-button').attr('style', 'position:relative; top: 5px');
    
    var listing_id = jQuery(this).parent().attr('data-list');
    var data_values = new Array(); 
    
    // I take data from all the fields
    jQuery('#ik-loansys-application-list #ik-loansys-popup-application-'+listing_id+' input').each(function() {
        data_values[jQuery(this).attr('name')] = jQuery(this).val();
    });
    jQuery('#ik-loansys-application-list #ik-loansys-popup-application-'+listing_id+' select').each(function() {
        data_values[jQuery(this).attr('name')] = jQuery(this).val();
    });
    jQuery('#ik-loansys-application-list #ik-loansys-popup-application-'+listing_id+' textarea').each(function() {
        data_values[jQuery(this).attr('name')] = jQuery(this).val();
    });

    var data = {
		    action: "ik_loansys_ajax_save_data_listing",
			"post_type": "post",
			"listing_id": listing_id,
            "IP_address":	data_values.IP_address,
            "address":	data_values.address,
            "adresse2":	data_values.adresse2,
            "amount_requested":	data_values.amount_requested,
            "birthdate":	data_values.birthdate,
            "city":	data_values.city,
            "client_Associated":	data_values.client_Associated,
            "codepostal":	data_values.codepostal,
            "comments_admin":	data_values.comments_admin,
            "country":	data_values.country,
            "email":	data_values.email,
            "employer_company":	data_values.employer_company,
            "employer_phone":	data_values.employer_phone,
            "employer_supervisor":	data_values.employer_supervisor,
            "employeur_telephone_ext":	data_values.employeur_telephone_ext,
            "fax":	data_values.fax,
            "firstname":	data_values.firstname,
            "hiring_date":	data_values.hiring_date,
            "language":	data_values.language,
            "lastname":	data_values.lastname,
            "mobilephone":	data_values.mobilephone,
            "payment_day":	data_values.payment_day,
            "payment_freq":	data_values.payment_freq,
            "phone":	data_values.phone,
            "province":	data_values.province,
            "ref1_courriel":	data_values.ref1_courriel,
            "ref1_lien":	data_values.ref1_lien,
            "ref1_nom":	data_values.ref1_nom,
            "ref1_prenom":	data_values.ref1_prenom,
            "ref1_telephone":	data_values.ref1_telephone,
            "ref2_courriel":	data_values.ref2_courriel,
            "ref2_lien":	data_values.ref2_lien,
            "ref2_nom":	data_values.ref2_nom,
            "ref2_prenom":	data_values.ref2_prenom,
            "ref2_telephone":	data_values.ref2_telephone,
            "ref3_courriel":	data_values.ref3_courriel,
            "ref3_nom":	data_values.ref3_nom,
            "ref3_prenom":	data_values.ref3_prenom,
            "ref3_telephone":	data_values.ref3_telephone,
            "rejection_reason":	data_values.rejection_reason,
            "request_datetime":	data_values.request_datetime,
            "status":	data_values.status,			
		};  

		// when the task was done
		jQuery.post( "<?php echo admin_url('admin-ajax.php'); ?>", data, function(response) {
			if (response){
			    
			    jQuery('#ik-loansys-application-'+listing_id+' .ik_loansys_date_modified').html(response);
			    
                button_save_data.find('.ik-loansys-loading-button').attr('style', 'display: none');
			}
			
		}, "json");
});

jQuery( "#ik-loansys-application-list" ).on( "click", ".ik-loansys-delete-list-id", function() {
    var confirmDelete = confirm("Vous allez supprimer un enregistrement. Êtes-vous sûr?");
	if (confirmDelete == true) {
        var button_delete_data = jQuery(this);
        
        var listing_id = button_delete_data.attr('data-list');
        
    
        var data = {
    		    action: "ik_loansys_ajax_remove_data_listing",
    			"post_type": "post",
    			"listing_id": listing_id,
    		};  
    
    		// when the task was done
    		jQuery.post( "<?php echo admin_url('admin-ajax.php'); ?>", data, function(response) {
    			if (response){
    			    jQuery('#ik-loansys-application-list #ik-loansys-popup-application-'+listing_id).fadeOut(600);
                    jQuery('#ik-loansys-application-list #ik-loansys-popup-application-'+listing_id).remove();
                    jQuery('#ik-loansys-application-list tr[data-list='+listing_id+']').fadeOut(600);
                    jQuery('#ik-loansys-application-list tr[data-list='+listing_id+']').remove();
    			}
    			
    		}, "json");
	}
});
</script>
<?php
}
?>