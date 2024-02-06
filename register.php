<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ;
 include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ;
 include_once "phpfn12.php" ;
 include_once "usersinfo.php" ;
 include_once "userfn12.php" ;
 include_once "custom_trait/registerTrait/renderRowTrait.php" ;
 include_once "custom_trait/registerTrait/showMessageTrait.php" ;
 include_once "custom_trait/registerTrait/pageMainTrait.php" ;
 include_once "custom_trait/registerTrait/pageInitTrait.php" ;
 include_once "custom_trait/registerTrait/passwordStrengthTrait.php" ;
 include_once "custom_trait/registerTrait/validateFormTrait.php" ;
 include_once "custom_trait/registerTrait/addRowTrait.php" ;
 include_once "custom_trait/registerTrait/dbLoadTrait.php" ;
 include_once "custom_trait/registerTrait/pageTerminateTrait.php" ;
 include_once "custom_trait/registerTrait/activateEmailTrait.php" ;
 include_once "custom_trait/registerTrait/mainClass.php"; //hold new cregister() class
 
 ?> 

<?php

$register = NULL; // Initialize page object first


?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($register)) $register = new cregister();


// Page init
$register->Page_Init();

// Page main
//	switch ($this->CurrentAction) inside page_main handles form submission while usersinfo.php contains fields to be submitted
$register->Page_Main();

// Begin of modification Displaying Breadcrumb Links in All Pages, by Masino Sinaga, May 4, 2012
getCurrentPageTitle(ew_CurrentPage());

// End of modification Displaying Breadcrumb Links in All Pages, by Masino Sinaga, May 4, 2012
// Global Page Rendering event (in userfn*.php)

Page_Rendering();

// Global auto switch table width style (in userfn*.php), by Masino Sinaga, January 7, 2015
AutoSwitchTableWidthStyle();

// Page Rendering event
$register->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "register";
var CurrentForm = fregister = new ew_Form("fregister", "register");

// Validate form
fregister.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_Username");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterUserName"));
			elm = this.GetElements("x" + infix + "_Password");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterPassword"));
			if ($(fobj.x_Password).hasClass("ewPasswordStrength") && !$(fobj.x_Password).data("validated"))
				return this.OnError(fobj.x_Password, ewLanguage.Phrase("PasswordTooSimple"));
			if (fobj.c_Password.value != fobj.x_Password.value)
				return this.OnError(fobj.c_Password, ewLanguage.Phrase("MismatchPassword"));
			elm = this.GetElements("x" + infix + "__Email");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $users->_Email->FldCaption(), $users->_Email->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "__Email");
			if (elm && !ew_CheckEmail(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($users->_Email->FldErrMsg()) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}
		if (fobj.captcha && !ew_HasValue(fobj.captcha))
			return this.OnError(fobj.captcha, ewLanguage.Phrase("EnterValidateCode"));
	return true;
}

// Form_CustomValidate event
fregister.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php 
	
	if (EW_CLIENT_VALIDATE) {

?>
fregister.ValidateRequired = true;
<?php 
} else {
	
?>
fregister.ValidateRequired = false; 
<?php 

} ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php if (MS_SHOW_BREADCRUMBLINKS_ON_REGISTER_PAGE) { ?>
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
<?php $register->ShowPageHeader(); ?>
<?php
$register->ShowMessage();

?>

<form name="fregister" id="fregister" class="form-horizontal ewForm ewRegisterForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php
	if (MS_REGISTER_WINDOW_TYPE == "default" || MS_REGISTER_WINDOW_TYPE == "") {
		echo '<div class="col-sm-8 col-sm-offset-2">';
		echo '<div class="panel ' . MS_REGISTER_FORM_PANEL_TYPE . '">';
		echo '<div class="panel-heading"><strong>' . $Language->Phrase("RegisterPage") . '</strong>';
		if (@MS_SHOW_HELP_ONLINE) {
			echo '&nbsp;<a href=\'javascript:void(0);\' id=\'helponline\' onclick=\'msHelpDialogShow()\'><span class=\'glyphicon glyphicon-question-sign ewIconHelp\'></span></a>';
		}
		echo '</div>';
		echo '<div class="panel-body"><br>';
	}
	 else
	 {
		echo '<div id="msRegisterDialog" class="modal fade">';
		echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">x</span></button><h4 class="modal-title">' . $Language->Phrase("RegisterPage") . '';
		if (@MS_SHOW_HELP_ONLINE) {
			echo '&nbsp;<a href=\'javascript:void(0);\' id=\'helponline\' onclick=\'msHelpDialogShow()\'><span class=\'glyphicon glyphicon-question-sign ewIconHelp\'></span></a>';
		}
		echo '</h4></div>';
		echo '<div class="modal-body"><br>';
	}
?>

<?php if ($register->CheckToken) 
	{
		?>
		<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $register->Token ?>">
		<?php 
	} ?>
<?php // Begin of modification Terms and Conditions, by Masino Sinaga, July 14, 2014 ?>
<?php 
if ( ($users->CurrentAction == "F") ||($users->CurrentAction == "I") ||($users->CurrentAction == "A") ||($users->CurrentAction == "X") ||
   (MS_SHOW_TERMS_AND_CONDITIONS_ON_REGISTRATION_PAGE == FALSE) ) 
   { 
		// Render term and conditions
		echo "<input type=\"hidden\" name=\"t\" value=\"users\">";
		echo "<input type=\"hidden\" name=\"a_register\" id=\"a_register\" value=\"A\">";

			if ($users->CurrentAction == "F") 
				{ // Confirm page ?>
					<input type="hidden" name="a_confirm" id="a_confirm" value="F">
					<?php 
				} 
			elseif ($users->CurrentAction == "T") 
			{
				?>
				<input type="hidden" name="a_confirm" id="a_confirm" value="T">
				<?php 
			} 
		?>
		<?php // End of modification Terms and Conditions, by Masino Sinaga, July 14, 2014 ?>
		<div>
		<?php 
		if ($users->Username->Visible) 
			{ 
			
				?>
				<div id="r_Username" class="form-group">
					<label id="elh_users_Username" for="x_Username" class="col-sm-4 control-label ewLabel">
						<?php echo $users->Username->FldCaption() ?>
						<?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
					<div class="col-sm-8"><div<?php echo $users->Username->CellAttributes() ?>>
				<?php 
				if ($users->CurrentAction <> "F") 
				{
					?>
					<span id="el_users_Username">
					<input type="text" data-table="users" data-field="x_Username" name="x_Username" id="x_Username" size="30"
					 maxlength="50" placeholder="<?php echo ew_HtmlEncode($users->Username->getPlaceHolder()) ?>"
					  value="<?php echo $users->Username->EditValue.time() ?>"
					  <?php echo $users->Username->EditAttributes() ?>>
					</span>
					<?php 
				} else 
				{
					?>
					<span id="el_users_Username">
					<span<?php echo $users->Username->ViewAttributes() ?>>
					<p class="form-control-static"><?php echo $users->Username->ViewValue ?></p></span>
					</span>
					<input type="hidden" data-table="users" data-field="x_Username" name="x_Username" id="x_Username" value="<?php echo ew_HtmlEncode($users->Username->FormValue) ?>">
					<?php 
				} ?>
				<?php echo $users->Username->CustomMsg ?></div></div>
					</div>
				<?php
			} 

		if ($users->Password->Visible) 
			{ // Password ?>
				<?php 
				if (MS_PASSWORD_POLICY_FROM_MASINO_REGISTER == TRUE)
				 { 
					 if ($users->CurrentAction <> "F")
						{ ?>
								<div id="r_Password" class="form-group">
									<label id="elh_users_Password" for="x_Password" class="col-sm-4 control-label ewLabel"><?php echo $users->Password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
									<div class="col-sm-8">
										<div <?php echo $users->Password->CellAttributes() ?>>
											<span id="el_users_Password">
												//password
											<input type="text" name="x_Password" id="x_Password" size="30" maxlength="50" value="<?php echo ew_HtmlEncode($users->Password->FormValue) ?>" placeholder="<?php echo $users->Password->FldCaption() ?>" onkeyup="passwordStrength(this.value, c_Password.value)" <?php echo $users->Password->EditAttributes() ?>>
											</span>
											<div id="passwordDescription"><?php echo $Language->Phrase("empty"); ?></div>
											<div class="password-meter-bg">
												<div id="passwordStrength" class="strength0"></div>
											</div>              
									<?php echo $users->Password->CustomMsg ?>
										</div>
									</div>
								</div>
							<?php 
						} else 
						{ // hidden ?>
							<div id="r_Password" class="form-group">
								<label id="elh_users_Password" for="x_Password" class="col-sm-4 control-label ewLabel"><?php echo $users->Password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
								<div class="col-sm-8"> 
									*****************<?php echo $users->Password->ViewValue ?><input type="hidden" name="x_Password" id="x_Password" value="<?php echo ew_HtmlEncode($users->Password->FormValue) ?>">
								<?php echo $users->Password->CustomMsg ?>
								</div>
							</div>
							<?php
						} 
				} 
				else 
					{ // Begin of Password from PHPMaker built-in 
						// show password field
					if ($users->Password->Visible) 
					{ // Password ?>
						<div id="r_Password" class="form-group">
							<label id="elh_users_Password" for="x_Password" class="col-sm-4 control-label ewLabel">
							<?php echo $users->Password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
							<div class="col-sm-8"><div<?php echo $users->Password->CellAttributes() ?>>
							<?php 
							if ($users->CurrentAction <> "F") 
							{ ?>
								<span id="el_users_Password">
								<div class="input-group" id="ig_x_Password">
									
								<input type="password" data-password-strength="pst_x_Password" data-password-generated="pgt_x_Password"
									value ="*123github*123github"
									data-table="users" data-field="x_Password" name="x_Password" id="x_Password" size="30" maxlength="64" placeholder="<?php echo ew_HtmlEncode($users->Password->getPlaceHolder()) ?>"<?php echo $users->Password->EditAttributes() ?>>
								<span class="input-group-btn">
									<button type="button" class="btn btn-default ewPasswordGenerator" title="<?php echo ew_HtmlTitle($Language->Phrase("GeneratePassword")) ?>" data-password-field="x_Password" data-password-confirm="c_Password" data-password-strength="pst_x_Password" data-password-generated="pgt_x_Password"><?php echo $Language->Phrase("GeneratePassword") ?></button>
								</span>
								</div>
								<span class="help-block" id="pgt_x_Password" style="display: none;"></span>
								<div class="progress ewPasswordStrengthBar" id="pst_x_Password" style="display: none;">
									<div class="progress-bar" role="progressbar"></div>
								</div>
								</span>
								<?php 
							}
							else 
							{	 ?>
									<span id="el_users_Password">
									<span<?php echo $users->Password->ViewAttributes() ?>>
									<p class="form-control-static"><?php echo $users->Password->ViewValue ?></p></span>
									</span>
									<input type="hidden" data-table="users" data-field="x_Password" name="x_Password" id="x_Password" value="<?php echo ew_HtmlEncode($users->Password->FormValue) ?>">
									<?php 
							} 
						 $users->Password->CustomMsg ?></div></div>
							</div>
						<?php
						} 
				} // End of Password from PHPMaker built-in 
 		} 
 ?>


<?php if ($users->Password->Visible) { // Password ?>
	<?php if (MS_PASSWORD_POLICY_FROM_MASINO_REGISTER == TRUE) { ?>
	<?php if ($users->CurrentAction <> "F") { 
		
		?>
		<div id="r_c_Password" class="form-group">
			<label id="elh_c_users_Password" for="c_Password" class="col-sm-4 control-label ewLabel"><?php echo $Language->Phrase("Confirm") ?> <?php echo $users->Password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
			<div class="col-sm-8">
				<div <?php echo $users->Password->CellAttributes() ?>>
					<span id="el_users_c_Password">
						//password
					<input type="text" name="c_Password" id="c_Password" size="30" maxlength="50" value="<?php echo ew_HtmlEncode($users->Password->FormValue) ?>" placeholder="<?php echo $Language->Phrase("Confirm") ?>&nbsp;<?php echo $users->Password->FldCaption() ?>" onkeyup="passwordConfirmation(x_Password.value, this.value)" <?php echo $users->Password->EditAttributes() ?>>
					</span>
						<div id="passconfDescription"><?php echo $Language->Phrase("match"); ?></div>
						<div class="password-meter-bg">        
							<div id="passconfConfirmation" class="conf1"></div>
						</div>          
			  <?php echo $users->Password->CustomMsg ?>
				</div>
			</div>
		</div>
	<?php 
	} else 
	{
			// hidden ?>
			<div id="r_c_Password" class="form-group">
				<label id="elh_c_users_Password" for="c_Password" class="col-sm-4 control-label ewLabel"><?php echo $Language->Phrase("Confirm") ?> <?php echo $users->Password->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
				<div class="col-sm-8"> 
					*****************<?php echo $users->Password->ViewValue ?><input type="hidden" name="c_Password" id="c_Password" value="<?php echo ew_HtmlEncode($users->Password->FormValue) ?>">
				<?php echo $users->Password->CustomMsg ?>
				</div>
			</div>
		<?php 
	} ?>
	<?php 
} 
else 
	{
		
 ?>
	<div id="r_c_Password" class="form-group">
		<label id="elh_c_users_Password" for="c_Password" class="col-sm-4 control-label ewLabel">
	<?php echo $Language->Phrase("Confirm") ?> <?php echo $users->Password->FldCaption() ?>
	<?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-8"><div<?php echo $users->Password->CellAttributes() ?>>
<?php if ($users->CurrentAction <> "F") { ?>
<span id="el_c_users_Password">
	
<input type="password" data-field="c_Password" name="c_Password" id="c_Password" size="30" 
		maxlength="64" 
		value ="*123github*123github"
		placeholder="<?php echo ew_HtmlEncode($users->Password->getPlaceHolder()) ?>
		"<?php echo $users->Password->EditAttributes() ?>>
</span>
<?php } else { 
	
	?>
<span id="el_c_users_Password">
<span<?php echo $users->Password->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $users->Password->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="users" data-field="c_Password" name="c_Password" id="c_Password" value="<?php echo ew_HtmlEncode($users->Password->FormValue) ?>">
<?php } ?>
</div></div>
	</div>
	<?php } // End of Password Policy from PHPMaker built-in  ?>
<?php } ?>








<?php 
if ($users->First_Name->Visible) 
{ // First_Name 
	
?>
		<div id="r_First_Name" class="form-group">
			<label id="elh_users_First_Name" for="x_First_Name" class="col-sm-4 control-label ewLabel"><?php echo $users->First_Name->FldCaption() ?></label>
			<div class="col-sm-8"><div<?php echo $users->First_Name->CellAttributes() ?>>
	<?php 
	if ($users->CurrentAction <> "F")
	{ ?>
		<span id="el_users_First_Name">
		<input type="text" data-table="users" data-field="x_First_Name" name="x_First_Name" id="x_First_Name" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($users->First_Name->getPlaceHolder()) ?>" value="<?php echo $users->First_Name->EditValue ?>"<?php echo $users->First_Name->EditAttributes() ?>>
		</span>
		<?php 
	}
	 else 
	{ 
		 ?>
		<span id="el_users_First_Name">
		<span<?php echo $users->First_Name->ViewAttributes() ?>>
		<p class="form-control-static"><?php echo $users->First_Name->ViewValue ?></p></span>
		</span>
		<input type="hidden" data-table="users" data-field="x_First_Name" name="x_First_Name" id="x_First_Name" value="<?php echo ew_HtmlEncode($users->First_Name->FormValue) ?>">
		<?php 
	} ?>
	<?php echo $users->First_Name->CustomMsg ?></div></div>
		</div>
	<?php 
} ?>
<?php 
if ($users->Last_Name->Visible) 
{ // Last_Name ?>
	<div id="r_Last_Name" class="form-group">
		<label id="elh_users_Last_Name" for="x_Last_Name" class="col-sm-4 control-label ewLabel"><?php echo $users->Last_Name->FldCaption() ?></label>
		<div class="col-sm-8"><div<?php echo $users->Last_Name->CellAttributes() ?>>
	<?php 
	if ($users->CurrentAction <> "F") 
	{ ?>
		<span id="el_users_Last_Name">
		<input type="text" data-table="users" data-field="x_Last_Name" name="x_Last_Name" id="x_Last_Name" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($users->Last_Name->getPlaceHolder()) ?>" value="<?php echo $users->Last_Name->EditValue ?>"<?php echo $users->Last_Name->EditAttributes() ?>>
		</span>
		<?php 
	} 
	else {
		?>
		<span id="el_users_Last_Name">
		<span<?php echo $users->Last_Name->ViewAttributes() ?>>
		<p class="form-control-static"><?php echo $users->Last_Name->ViewValue ?></p></span>
		</span>
		<input type="hidden" data-table="users" data-field="x_Last_Name" name="x_Last_Name" id="x_Last_Name" value="<?php echo ew_HtmlEncode($users->Last_Name->FormValue) ?>">
		<?php 
	} ?>
	<?php echo $users->Last_Name->CustomMsg ?></div></div>
		</div>
	<?php 
} ?>
<?php 
if ($users->_Email->Visible) 
{ // Email ?>
	<div id="r__Email" class="form-group">
		<label id="elh_users__Email" for="x__Email" class="col-sm-4 control-label ewLabel"><?php echo $users->_Email->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-8"><div<?php echo $users->_Email->CellAttributes() ?>>
		<?php 
		if ($users->CurrentAction <> "F") 
		{ ?>
				<span id="el_users__Email">
				<input type="text" data-table="users" data-field="x__Email" name="x__Email" id="x__Email" size="30" maxlength="100"
				 placeholder="<?php echo ew_HtmlEncode($users->_Email->getPlaceHolder()) ?>" 
				 value="<?php echo $users->_Email->EditValue.time()."@gmail.com" ?>"
				 <?php echo $users->_Email->EditAttributes() ?>>
				</span>
		<?php 
		} 
		else 
		{ ?>
				<span id="el_users__Email">
				<span<?php echo $users->_Email->ViewAttributes() ?>>
				<p class="form-control-static"><?php echo $users->_Email->ViewValue ?></p></span>
				</span>
				<input type="hidden" data-table="users" data-field="x__Email" name="x__Email" id="x__Email"
				 value="<?php echo ew_HtmlEncode($users->_Email->FormValue) ?>">
				<?php 
		} ?>
				<?php echo $users->_Email->CustomMsg ?></div></div>
					</div>
		<?php 
} 
?>
	</div>
	<?php 
	if (MS_SHOW_CAPTCHA_ON_REGISTRATION_PAGE == TRUE) 
	{ 
		
			if ($users->CurrentAction <> "F") 
				{ ?>
					<!-- captcha html (begin) -->
					<div class="form-group">
						<div class=" col-sm-offset-4 col-sm-8 ">
						<!-- <img src="ewcaptcha.php" alt="Security Image" style="width: 200px; height: 50px;"><br><br> -->
						<input type="hidden" name="captcha" id="captcha" value ="22" class="form-control" size="30" placeholder="<?php echo $Language->Phrase("EnterValidateCode") ?>">
						</div>
					</div>
				<?php 
				}
			else 
				{ ?>
					<input type="hidden" name="captcha" id="captcha" value="<?php echo $register->captcha ?>">
					<?php 
				} 
			
	
	} ?>
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
	<?php 
		if ($users->CurrentAction <> "F") 
		{ //This is the button  registers the form?>
		
				<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit" onclick="this.form.a_register.value='F';">
					<?php echo $Language->Phrase("RegisterBtn")?>
				</button>
			<?php 
		} 
	else { 
			// This is the button that confirm and send the form to the database
		?>
		
			<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit">
				<?php echo $Language->Phrase("ConfirmBtn")?></button>
			<button class="btn btn-danger ewButton" name="btnCancel" id="btnCancel" type="submit" onclick="this.form.a_register.value='X';">
				<?php echo $Language->Phrase("CancelBtn") ?></button>
			<?php 
		} ?>
		</div>
	</div>
	<?php 
} 
else
{ // Terms and Conditions page ?>
	<?php
		if (@MS_USE_CONSTANTS_IN_CONFIG_FILE == FALSE) 
		{
			//Fetch term and conditions from the database
			$sSql = "SELECT Terms_And_Condition_Text FROM ".MS_LANGUAGES_TABLE."
					WHERE Language_Code = '".$gsLanguage."'";              
			$rs = ew_Execute($sSql);
			$tactitle = $Language->Phrase("TaCTitle");
			if ($rs && $rs->RecordCount() > 0) {
				$taccontent = $rs->fields("Terms_And_Condition_Text");
				$taccontent = str_replace("<br>", "\n", $taccontent);
				$taccontent = str_replace("<br>", "\n", $taccontent);
				$taccontent = str_replace("<strong>", "", $taccontent);
				$taccontent = str_replace("</strong>", "", $taccontent);
			} 
			else 
			{
				$taccontent = $Language->Phrase("TaCContent");
			}
		}else {
			$tactitle = $Language->Phrase("TaCTitle");
			$taccontent = $Language->Phrase("TACNotAvailable");
		}
   ?>
	<div class="form-group" id="r_TAC">
		<div class="col-sm-10">
			<textarea class="form-control ewControl" id="tactextarea" cols="50" rows="10" readonly style="max-width:430px; min-width:200px; max-height:300px; min-height:200px;"><?php echo $taccontent ?></textarea>
		</div>
	</div>
	<?php 
		if (MS_TERMS_AND_CONDITION_CHECKBOX_ON_REGISTER_PAGE == TRUE) 
		{ ?>

			<div class="form-group">
				<div class="col-sm-10">
					<label class="checkbox-inline ewCheckBox" style="white-space: nowrap;">
					<?php $selwrk = (@isset($_POST["chktac"])) ? " checked='checked'" : ""; ?>
					<input type="checkbox" name="chktac" id="chktac" value="<?php echo @$_POST["chktac"]; ?>" <?php echo $selwrk; ?>>&nbsp;
						<?php echo $Language->Phrase("IAgreeWith"); ?>&nbsp;<?php echo $Language->Phrase("TaCTitle"); ?>&nbsp;<a href="printtac.php">
							<?php echo Language()->Phrase("Print"); ?></a>
					</label>
				</div>
			</div>
			<?php 
		} 
	?>
	<div class="form-group" id="r_RegisterButton">
		<div class="col-sm-10">
			<input type="hidden" name="a_register" id="a_register" value="I">
			<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"  onclick="this.form.a_register.value='I';">
					<?php echo $Language->Phrase("IAgree"); ?></button>
		</div>
	</div>
	<?php 
} // Terms and Conditions page ?>
</div>
<?php 
	if (MS_REGISTER_WINDOW_TYPE=="default" || MS_REGISTER_WINDOW_TYPE=="") {	 
?>
			<div class="panel-footer <?php echo MS_REGISTER_FORM_PANEL_TYPE; ?>">
				<div>
					<a class="ewLink ewLinkSeparator" href="login.php"><?php echo $Language->Phrase("Login") ?></a>
					<a class="ewLink ewLinkSeparator" href="forgotpwd.php"><?php echo $Language->Phrase("ForgotPwd") ?></a>
				</div>
			</div>
			</div>
			</div>
			<?php 
		} 
	else 
		{ ?>
		<div class="modal-footer">
			<div class="pull-left">
				<a class="ewLink ewLinkSeparator" href="login.php"><?php echo $Language->Phrase("Login") ?></a>
				<a class="ewLink ewLinkSeparator" href="forgotpwd.php"><?php echo $Language->Phrase("ForgotPwd") ?></a>
			</div>
		</div>
		</div>
		</div>
		</div>
		<?php 
	} 
?>
</form>










<script type="text/javascript" src="phpjs/register_dialog.js"></script>
<script type="text/javascript">
fregister.Init();
$(document).ready(function(){
<?php if (MS_REGISTER_WINDOW_TYPE=="popup") { ?>
msRegisterDialogShow();
<?php } ?>
<?php if (MS_TERMS_AND_CONDITION_CHECKBOX_ON_REGISTER_PAGE == TRUE) { ?>
  if ($('#chktac').attr('checked')) {
	$('#btnsubmit').removeAttr('disabled');
  } else {
	$('#btnsubmit').attr('disabled', 'disabled');
  }
  $("#chktac").click(function() {
    var checked_status = this.checked;
    if (checked_status == true) {
	  $('#btnsubmit').removeAttr('disabled');
	} else {
	  $('#btnsubmit').attr('disabled', 'disabled');
	}
  });
<?php } ?>
});
</script>
<?php
$register->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<!-- <?php // Begin of modification Password Strength Meter, by Masino Sinaga, June 9, 2012 ?>
<style>
.password-meter{position:relative;width:180px}.password-meter-message{text-align:right;font-weight:bold;color:#676767}.password-meter-bg,.password-meter-bar{height:5px;width:100px}.password-meter-bg{top:8px;background:#ccc}#passconfConfirmation{height:5px;display:block;float:left}.conf0{width:50px;background:red}.conf1{background:#256800;width:100px}#passwordStrength{height:5px;display:block;float:left}.strength0{background:#ccc;width:100px}.strength1{background:red;width:20px}.strength2{background:#ff5f5f;width:40px}.strength3{background:#56e500;width:60px}.strength4{background:#4dcd00;width:80px}.strength5{background:#399800;width:90px}.strength6{background:#256800;width:100px}
</style>
<?php // End of modification Password Strength Meter, by Masino Sinaga, June 9, 2012 ?>
<script type="text/javascript">

// Write your startup script here
// document.write("page loaded");

</script>
<?php if (MS_ENTER_MOVING_CURSOR_TO_NEXT_FIELD) { ?>
<script type="text/javascript">
$(document).ready(function(){$("#fregister:first *:input[type!=hidden]:first").focus(),$("input").keydown(function(i){if(13==i.which){var e=$(this).closest("form").find(":input:visible:enabled"),n=e.index(this);n==e.length-1||(e.eq(e.index(this)+1).focus(),i.preventDefault())}else 113==i.which&&$("#btnAction").click()}),$("select").keydown(function(i){if(13==i.which){var e=$(this).closest("form").find(":input:visible:enabled"),n=e.index(this);n==e.length-1||(e.eq(e.index(this)+1).focus(),i.preventDefault())}else 113==i.which&&$("#btnAction").click()}),$("radio").keydown(function(i){if(13==i.which){var e=$(this).closest("form").find(":input:visible:enabled"),n=e.index(this);n==e.length-1||(e.eq(e.index(this)+1).focus(),i.preventDefault())}else 113==i.which&&$("#btnAction").click()})});
</script>
<?php } ?>
<script type="text/javascript">
$(document).ready(function(){
$('#fregister:first *:input[type!=hidden]:first').focus();
});
$(document).ready(function(){
var password = document.getElementById("x_Password").value;
var password2 = document.getElementById("c_Password").value;
passwordStrength(password, password2);
});
</script>
<script type="text/javascript">

function passwordConfirmation(pass1, pass2)
{
    var desc = new Array();
    desc[0] = "<?php echo $Language->Phrase("mismatch"); ?>";
    desc[1] = "<?php echo $Language->Phrase("match"); ?>";

    // var score = 0;
    if (pass1 != pass2) {
      score = 0;  
    } else {
      score = 1;
    }
     document.getElementById("passconfDescription").innerHTML = desc[score];
     document.getElementById("passconfConfirmation").className = "conf" + score;
}

function passwordStrength(password, password2)
{
    var desc = new Array();
    desc[0] = "<?php echo $Language->Phrase("empty"); ?>";
    desc[1] = "<?php echo $Language->Phrase("veryweak"); ?>";
    desc[2] = "<?php echo $Language->Phrase("weak"); ?>";
    desc[3] = "<?php echo $Language->Phrase("better"); ?>";
    desc[4] = "<?php echo $Language->Phrase("good"); ?>";
    desc[5] = "<?php echo $Language->Phrase("strong"); ?>";
    desc[6] = "<?php echo $Language->Phrase("strongest"); ?>";
    var descc = new Array();
    descc[0] = "<?php echo $Language->Phrase("mismatch"); ?>";
    descc[1] = "<?php echo $Language->Phrase("match"); ?>";
    var score = 1;

    //if password is empty, reset the score
    if (password.length == 0) score=0;

    //if password bigger than 6 give 1 point
    if (password.length > 6) score++;

    //if password has both lower and uppercase characters give 1 point
    if ( ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) ) score++;

    //if password has at least one number give 1 point
    if (password.match(/\d+/)) score++;

    //if password has at least one special caracther give 1 point
    if ( password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) ) score++;

    //if password bigger than 12 give another 1 point
    if (password.length > 12) score++;
     document.getElementById("passwordDescription").innerHTML = desc[score];
     document.getElementById("passwordStrength").className = "strength" + score;
    var scorec = 0;
    if (password != password2) {
      scorec = 0;  
    } else {
      scorec = 1;
    }
     document.getElementById("passconfDescription").innerHTML = descc[scorec];
     document.getElementById("passconfConfirmation").className = "conf" + scorec;
}
</script> -->
<?php include_once "footer.php" ?>
<?php
$register->Page_Terminate();
?>
