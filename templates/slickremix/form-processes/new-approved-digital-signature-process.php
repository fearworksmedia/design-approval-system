<html>
<head>
<?php require_once( '../../../../../../wp-load.php' );?>
</head>
<body>
<?php
require_once('class.phpmailer.php');
// Retrieve form data
$custom_client_approved_signature = ($_GET['custom_client_approved_signature']) ?$_GET['custom_client_approved_signature'] : $_POST['custom_client_approved_signature'];
// Submit Yes to create approved design stars for project board
$submitApprovedYes = ($_GET['submitApprovedYes']) ?$_GET['submitApprovedYes'] : $_POST['submitApprovedYes'];

$email1 = ($_GET['email1']) ?$_GET['email1'] : $_POST['email1'];
$designerEmail = ($_GET['designer_email']) ?$_GET['designer_email'] : $_POST['designer_email'];
$companyname4 = ($_GET['companyname4']) ?$_GET['companyname4'] : $_POST['companyname4'];
$designtitle = ($_GET['designtitle']) ?$_GET['designtitle'] : $_POST['designtitle'];
$version4 = ($_GET['version4']) ?$_GET['version4'] : $_POST['version4'];
$customNameOfDesign = ($_GET['customNameOfDesign']) ?$_GET['customNameOfDesign'] : $_POST['customNameOfDesign'];
// Company Info
$das_settings_company_name = ($_GET['dasSettingsCompanyName']) ?$_GET['dasSettingsCompanyName'] : $_POST['dasSettingsCompanyName'];
$das_settings_company_email = ($_GET['dasSettingsCompanyEmail']) ?$_GET['dasSettingsCompanyEmail'] : $_POST['dasSettingsCompanyEmail'];
// Messages
$das_settings_approved_dig_sig_message_to_designer = ($_GET['dasSettingsApprovedDigSigMessageToDesigner']) ?$_GET['dasSettingsApprovedDigSigMessageToDesigner'] : $_POST['dasSettingsApprovedDigSigMessageToDesigner'];

$das_settings_approved_dig_sig_message_to_clients = ($_GET['dasSettingsApprovedDigSigMessageToClients']) ?$_GET['dasSettingsApprovedDigSigMessageToClients'] : $_POST['dasSettingsApprovedDigSigMessageToClients'];

$approvedComments = ($_GET['approvedComments']) ?$_GET['approvedComments'] : $_POST['approvedComments'];

$human1 = ($_GET['human1']) ?$_GET['human1'] : $_POST['human1'];

// added this for the gq theme, sends the post meta info for the template das-gq-theme-main.php so we can check against it for the jquery approve messges.
$templateName = ($_GET['templateName']) ?$_GET['templateName'] : $_POST['templateName'];
?>
<style type="text/css">
input.hightlight1, input.hightlight2, input.hightlight3  {
	background-color:#FFFEBE !important;
	-webkit-transition: all 0.30s ease-in-out;
	-moz-transition: all 0.30s ease-in-out;
	-o-transition: all 0.30s ease-in-out;
	transition: all 0.30s ease-in-out;
	color:#222;
	}
</style>
<?php
//flag to indicate which method it uses. If POST set it to 1
if ($_POST) $post=1;

if (!$human1 ['human1'] == '') {$errors[count($errors)] = 'It appears you may be trying to submit spam. Please disreguard this notice and try again if we made a mistake.'; }

//if the errors array is empty, send the mail
$recipients = array('designer', 'client');

if (!$errors) {
 
	//sender this will always be the same
	$from = $das_settings_company_email;
	
	 ////////////////////////////
	///////// Designer /////////
   ////////////////////////////

 	// to designer
	$to_designer = $das_settings_company_email;
	
	//subject to designer 
	$subject_designer = $customNameOfDesign  . ' - ' . $version4 .  ' - ' . $companyname4 . ' Approved this Design';
	
	// message to designer
	$message_designer = nl2br(''.$das_settings_approved_dig_sig_message_to_designer.'
	
	From: ' . $email1 . '
	Digital Signature: ' . $custom_client_approved_signature . '
	
	Design Approved, '. $submitApprovedYes . ': ' . $designtitle.'
	
	'. $approvedComments.'
	
	');
	
	 ////////////////////////////
	///////// Client ///////////
   ////////////////////////////
	
	// to client
	$to_client = $email1;
	
	//subject to client
	$subject_client = $customNameOfDesign  . ' - ' . $version4  . ' - Design Approval Confirmation';
	
	// message to client
	$message_client = nl2br(''.$das_settings_approved_dig_sig_message_to_clients.'
	
	From: ' . $email1 . '
	Digital Signature: ' . $custom_client_approved_signature . '
	
	Design Approved, '. $submitApprovedYes . ': ' . $designtitle.'
	
	'. $approvedComments.'
	
  	Sincerely,
	'.$das_settings_company_name.'
	
	');
	
foreach	($recipients as $recipient)	{
  $mail = new PHPMailer();
	  
$dasSettingsSmtp = get_option( 'das-settings-smtp' );
	  
  if ($dasSettingsSmtp == '1') {
	  
	  $das_smtp_checkbox_authenticate = get_option('das-smtp-checkbox-authenticate');
	  	  
	  //SMTP Authenticate?
	  if ($das_smtp_checkbox_authenticate == '1') {
		  $das_smtp_checkbox_authenticate_final = true;
	  }
	  else{
		  $das_smtp_checkbox_authenticate_final = false;
	  }
	  
	  $mail->IsSMTP();  // telling the class to use SMTP
	  $mail->SMTPDebug  = 1;
	  $mail->SMTPAuth   = $das_smtp_checkbox_authenticate_final;
	  $mail->SMTPSecure = get_option( 'das-settings-das-ssl-or-tls-option');
	  $mail->Port       = get_option( 'das-smtp-port' ); 
	  $mail->Host       = get_option( 'das-smtp-server' ); 
	  $mail->Username   = get_option( 'das-smtp-authenticate-username' ); 
	  $mail->Password   = get_option( 'das-smtp-authenticate-password' );
  }
  else {
	$mail->IsSendmail(); // telling the class to use SendMail transport
  }
  
  if ($recipient== 'designer'){
	$mail->AddReplyTo = $from;
	$mail->FromName   = $das_settings_company_name;
	$mail->From       = $das_settings_company_email;
	$mail->AddAddress($to_designer, $das_settings_company_name);
	$mail->AddCC($designerEmail);
	$mail->Subject  = $subject_designer;
	$mail->MsgHTML($message_designer);
  }
  
  if ($recipient== 'client'){
	$mail->AddReplyTo = $from;
	$mail->FromName   = $das_settings_company_name;
	$mail->From       = $das_settings_company_email;
	$mail->AddAddress($to_client, $companyname4);
	$mail->Subject  = $subject_client;
	$mail->MsgHTML($message_client);
  }
  
  if(!$myresult = $mail->Send()) {
	  
	
  } else {
  }
}

//if POST was used, display the message straight away
?>
<script>jQuery(document).ready(function(){
<?php
	if ($_POST) {
		if ($myresult) {
				global $post; 	?>  
						jQuery('.status-area1, .proofing-popup-options-wrap').hide();
						jQuery('.das-approval-header-popup-approval').hide();
						jQuery('.das-approval-header-popup-approved').show();
						
						jQuery('.approved-form-wrap').hide();
					 jQuery('.approved-js-action-after-submit').hide();	
						jQuery('.approved-thankyou-form-wrap').fadeIn();
					<?php	}
					
				else echo "alert('".$mail->ErrorInfo."');";

	//else if GET was used, return the boolean value so that 
	//ajax script can react accordingly
	//1 means success, 0 means failed
	} else {
	 echo "alert('Sorry, unexpected error. Please try again!');";
	}
	?>});</script>
<?php
} 
?>
</body>
</html>