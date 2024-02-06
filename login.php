<?php

if (session_id() == "") session_start(); // Initialize Session data
if(isset($_SESSION["EW_CAPTCHA_CODE"]))
// echo $_SESSION["EW_CAPTCHA_CODE"];
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "usersinfo.php" ?>
<?php 
	include_once "userfn12.php";
	include_once "custom_trait/loginTrait/showMessageTrait.php" ;
	include_once "custom_trait/loginTrait/pageInitTrait.php" ;
	include_once "custom_trait/loginTrait/pageMainTrait.php" ;
	include_once "custom_trait/loginTrait/validateFormTrait.php" ;
	include_once "custom_trait/loginTrait/pageTerminateTrait.php" ;
	include_once "custom_trait/loginTrait/mainClass.php";// initalize login 
?>

<?php
// Page class
//

$login = NULL; // Initialize page object first


?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($login)) $login = new clogin(); // called from main claass

// Page init
$login->Page_Init();

// Page main

$login->Page_Main(); // handles the user login using if (isset($_POST["username"])) along side with if ($bValidate) 

// Begin of modification Displaying Breadcrumb Links in All Pages, by Masino Sinaga, May 4, 2012
getCurrentPageTitle(ew_CurrentPage());

// End of modification Displaying Breadcrumb Links in All Pages, by Masino Sinaga, May 4, 2012
// Global Page Rendering event (in userfn*.php)

Page_Rendering();

// Global auto switch table width style (in userfn*.php), by Masino Sinaga, January 7, 2015
AutoSwitchTableWidthStyle();

// Page Rendering event
$login->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<script type="text/javascript" src="phpjs/login_dialog.js"></script>
<script type="text/javascript">
var flogin = new ew_Form("flogin");

// Validate function
flogin.Validate = function()
{
	var fobj = this.Form;
	if (!this.ValidateRequired)
		return true; // Ignore validation
	if (!ew_HasValue(fobj.username))
		return this.OnError(fobj.username, ewLanguage.Phrase("EnterUid"));
	if (!ew_HasValue(fobj.password))
		return this.OnError(fobj.password, ewLanguage.Phrase("EnterPwd"));
<?php if (MS_SHOW_CAPTCHA_ON_LOGIN_PAGE == TRUE) { ?>
		if (fobj.captcha && !ew_HasValue(fobj.captcha))
			return this.OnError(fobj.captcha, ewLanguage.Phrase("EnterValidateCode"));
<?php } ?>

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj)) return false;
	return true;
}

// Form_CustomValidate function
flogin.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Requires js validation
<?php if (EW_CLIENT_VALIDATE) { ?>
flogin.ValidateRequired = true;
<?php } else { ?>
flogin.ValidateRequired = false;
<?php } ?>
</script>
<div class="ewToolbar">
<?php if (MS_SHOW_BREADCRUMBLINKS_ON_LOGIN_PAGE) { ?>
<?php if (MS_SHOW_PHPMAKER_BREADCRUMBLINKS) { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if (MS_SHOW_MASINO_BREADCRUMBLINKS) { ?>
<?php echo MasinoBreadcrumbLinks(); ?>
<?php } ?>
<?php } ?>
<?php if (@MS_LANGUAGE_SELECTOR_VISIBILITY == "belowheader") { ?>
<?php if (MS_LANGUAGE_SELECTOR_VISIBILITY=="belowheader") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php $login->ShowPageHeader(); ?>
<?php
$login->ShowMessage();

?>
<form name="flogin" id="flogin" class="form-horizontal ewForm ewLoginForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if (MS_LOGIN_WINDOW_TYPE=="default" || MS_LOGIN_WINDOW_TYPE=="") { ?>
<div class="col-sm-8 col-sm-offset-2">
<div class="panel <?php echo MS_LOGIN_FORM_PANEL_TYPE; ?>">
<div class="panel-heading"><strong><?php echo $Language->Phrase("Login") ?></strong><?php if (@MS_SHOW_HELP_ONLINE) { ?> &nbsp;<a href='javascript:void(0);' id='helponline' onclick='msHelpDialogShow()'><span class='glyphicon glyphicon-question-sign ewIconHelp'></span></a> <?php } ?></div>
<div class="panel-body">
<br>
<?php if ($login->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $login->Token ?>">
<?php } ?>
	<div class="form-group">
		<label class="col-sm-4 control-label ewLabel" for="username"><?php echo $Language->Phrase("Username") ?></label>
		<div class="col-sm-8"><input type="text" name="username" id="username" class="form-control ewControl" value="<?php echo ew_HtmlEncode($login->Username) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Username")) ?>"></div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label ewLabel" for="password"><?php echo $Language->Phrase("Password") ?></label>
		<!--password-->
		<div class="col-sm-8"><input type="password" name="password" id="password" class="form-control ewControl" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Password")) ?>"></div>
	</div>
	<!-- <div class="form-group">
		<label class="col-sm-4 control-label ewLabel" for="code">Organisation code</label>
		<div class="col-sm-8"><input type="text" name="code" id="code" class="form-control ewControl" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("code")) ?>"></div>
	</div> -->
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<a id="ewLoginOptions" class="collapsed" data-toggle="collapse" data-target="#flogin_options"><?php echo $Language->Phrase("LoginOptions") ?> <span class="icon-arrow"></span></a>
			<div id="flogin_options" class="collapse">
					<div class="radio ewRadio">
					<label for="type1"><input type="radio" name="type" id="type1" value="a"<?php if ($login->LoginType == "a") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AutoLogin") ?></label>
					</div>
					<div class="radio ewRadio">
					<label for="type2"><input type="radio" name="type" id="type2" value="u"<?php if ($login->LoginType == "u") { ?>  checked="checked"<?php } ?>><?php echo $Language->Phrase("SaveUserName") ?></label>
					</div>
					<div class="radio ewRadio">
					<label for="type3"><input type="radio" name="type" id="type3" value=""<?php if ($login->LoginType == "") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AlwaysAsk") ?></label>
					</div>
			</div>
		</div>
	</div>
<?php if (MS_SHOW_CAPTCHA_ON_LOGIN_PAGE == TRUE) { ?>
<!-- captcha html (begin) -->
<div class="form-group">
	<div class=" col-sm-offset-4 col-sm-8 ">
	<!-- <img src="ewcaptcha.php" alt="Security Image" style="width: 200px; height: 50px;"><br><br> -->
	<input type="hidden" name="captcha" value ="22" id="captcha" class="form-control" size="30" placeholder="<?php echo $Language->Phrase("EnterValidateCode") ?>">
	</div>
</div>
<!-- captcha html (end) -->
<?php } ?>
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("Login") ?></button>
			<button class="btn btn-danger ewButton" name="btnreset" id="btnreset" type="reset"><?php echo $Language->Phrase("Reset") ?></button>
		</div>
	</div>
</div>
<div class="panel-footer <?php echo MS_LOGIN_FORM_PANEL_TYPE; ?>">
	<div>
		<a class="ewLink ewLinkSeparator" href="forgotpwd.php"><?php echo $Language->Phrase("ForgotPwd") ?></a>
		<?php if (@MS_USER_REGISTRATION) { ?>
		<a class="ewLink ewLinkSeparator" href="register.php"><?php echo $Language->Phrase("Register") ?></a>
		<?php } ?>
	</div>
</div>
</div>
</div>
<?php } else { // else for Window Type, this is for "popup" ?>
<div id="msLoginDialog" class="modal fade">
<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">x</span></button><h4 class="modal-title"><?php echo $Language->Phrase("Login") ?><?php if (@MS_SHOW_HELP_ONLINE) { ?> &nbsp;<a href='javascript:void(0);' id='helponline' onclick='msHelpDialogShow()'><span class='glyphicon glyphicon-question-sign ewIconHelp'></span></a> <?php } ?></h4></div>
<div class="modal-body">
<br>
<?php if ($login->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $login->Token ?>">
<?php } ?>
	<div class="form-group">
		<label class="col-sm-4 control-label ewLabel" for="username"><?php echo $Language->Phrase("Username") ?></label>
		<div class="col-sm-8"><input type="text" name="username" id="username" class="form-control ewControl" value="<?php echo ew_HtmlEncode($login->Username) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Username")) ?>"></div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label ewLabel" for="password"><?php echo $Language->Phrase("Password") ?></label>
		<!--password-->
		<div class="col-sm-8"><input type="password" name="password" id="password" class="form-control ewControl" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Password")) ?>"></div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<a id="ewLoginOptions" class="collapsed" data-toggle="collapse" data-target="#flogin_options"><?php echo $Language->Phrase("LoginOptions") ?> <span class="icon-arrow"></span></a>
			<div id="flogin_options" class="collapse">
					<div class="radio ewRadio">
					<label for="type1"><input type="radio" name="type" id="type1" value="a"<?php if ($login->LoginType == "a") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AutoLogin") ?></label>
					</div>
					<div class="radio ewRadio">
					<label for="type2"><input type="radio" name="type" id="type2" value="u"<?php if ($login->LoginType == "u") { ?>  checked="checked"<?php } ?>><?php echo $Language->Phrase("SaveUserName") ?></label>
					</div>
					<div class="radio ewRadio">
					<label for="type3"><input type="radio" name="type" id="type3" value=""<?php if ($login->LoginType == "") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AlwaysAsk") ?></label>
					</div>
			</div>
		</div>
	</div>
<?php if (MS_SHOW_CAPTCHA_ON_LOGIN_PAGE == TRUE) { ?>
<!-- captcha html (begin) -->
<div class="form-group">
	<div class=" col-sm-offset-4 col-sm-8 ">
	<img src="ewcaptcha.php" alt="Security Image" style="width: 200px; height: 50px;"><br><br>
	<input type="text" name="captcha" id="captcha" class="form-control" size="30" placeholder="<?php echo $Language->Phrase("EnterValidateCode") ?>">
	</div>
</div>
<!-- captcha html (end) -->
<?php } ?>
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("Login") ?></button>
			<button class="btn btn-danger ewButton" name="btnreset" id="btnreset" type="reset"><?php echo $Language->Phrase("Reset") ?></button>
		</div>
	</div>
</div>
<div class="modal-footer">
	<div class="pull-left">
		<a class="ewLink ewLinkSeparator" href="forgotpwd.php"><?php echo $Language->Phrase("ForgotPwd") ?></a>
		<?php if (@MS_USER_REGISTRATION) { ?>
		<a class="ewLink ewLinkSeparator" href="register.php"><?php echo $Language->Phrase("Register") ?></a>
		<?php } ?>
	</div>
</div>
</div>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
flogin.Init();
$(document).ready(function(){
	$("#btnsubmit").button().click(function(){
		if (flogin.Validate() == true ) {

			alertify.success("<?php echo Language()->Phrase("AlertifyProcessing"); ?>");
			$('#msLoginDialog').slideUp(800);
		}
	});
<?php if (MS_LOGIN_WINDOW_TYPE=="popup") { ?>
  msLoginDialogShow(); 
  $('#msLoginDialog').on('shown.bs.modal', function () {
    $('#username').focus();
  });
<?php } else { ?>
  $("#username").focus();
<?php } ?>
});
</script>
<?php
$login->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your startup script here
// document.write("page loaded");

$(document).ready(function() {
	$('#username').val('');
	$('#password').val('');

	// $('#username').val('admin');
	// $('#password').val('master');
	
});
</script>
<?php include_once "footer.php" ?>
<?php
$login->Page_Terminate();
?>
