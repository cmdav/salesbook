<?php

trait pageMainTrait{

    
	//
	// Page main
	//
	function Page_Main() {
		global $UserTableConn, $Security, $Language, $gsFormError, $objForm, $UserProfile;  // $UserProfile added by Masino Sinaga, November 6, 2011;
		global $Breadcrumb;

		// Set up Breadcrumb
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1); // v11.0.4
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("register", "RegisterPage", $url, "", "", TRUE); // v11.0.4
		$bUserExists = FALSE;
		if (@$_POST["a_register"] <> "") {
			//called by term and conditions
			// Get action
			$this->CurrentAction = $_POST["a_register"];
			$this->LoadFormValues(); // Get form values

			// Validate form
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->setFailureMessage($gsFormError);
			}

			// Check the password strength
			if (!$this->CheckPasswordStrength() && $this->CurrentAction != "I") {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->setFailureMessage($gsFormError);
			}
		} else {
			// die("blank");
			// $this->CurrentAction = "I"; // Display blank record // in order to display Terms and Condition page as the initial page.
			$this->LoadDefaultValues(); // Load default values
		}
		if (MS_SHOW_CAPTCHA_ON_REGISTRATION_PAGE == TRUE) { 

		/*

		// CAPTCHA checking
		if ($this->CurrentAction == "I" || $this->CurrentAction == "C") {
			$this->ResetCaptcha();
		} elseif (ew_IsHttpPost()) {
			$objForm->Index = -1;
			$this->captcha = $objForm->GetValue("captcha");
			if (!$this->ValidateCaptcha()) { // CAPTCHA unmatched
				$this->setFailureMessage($Language->Phrase("EnterValidateCode"));
				$this->CurrentAction = "I"; // Reset action, do not insert
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
			} else {
				if ($this->CurrentAction == "A")
					$this->ResetCaptcha();
			}
		}
		*/

		// CAPTCHA checking
		if ($this->CurrentAction <> "I" && $this->CurrentAction <> "C") {
			if ( (($this->CurrentAction == "F") || ($this->CurrentAction == "I") || ($this->CurrentAction == "A") || ($this->CurrentAction == "X")) && (MS_SHOW_CAPTCHA_ON_REGISTRATION_PAGE == TRUE) ) {
				$objForm->Index = -1;
				$this->captcha = $objForm->GetValue("captcha");
				if (!$this->ValidateCaptcha()) { // CAPTCHA unmatched
					$this->setFailureMessage($Language->Phrase("EnterValidateCode"));
					$this->CurrentAction = "I"; // Reset action, do not insert
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values
				} else {
					if ($this->CurrentAction == "A")
						$this->ResetCaptcha();
				}
			}
		} elseif ($this->CurrentAction == "I" || $this->CurrentAction == "C") {
			$this->ResetCaptcha();
		}
		}

		// Handle email activation
		if (@$_GET["action"] <> "") {
			$sAction = $_GET["action"];
			$sEmail = @$_GET["email"];
			$sCode = @$_GET["token"];
			@list($sApprovalCode, $sUsr, $sPwd) = explode(",", $sCode, 3);
			$sApprovalCode = ew_Decrypt($sApprovalCode);
			$sUsr = ew_Decrypt($sUsr);
			$sPwd = ew_Decrypt($sPwd);
			if ($sEmail == $sApprovalCode) {
				if (strtolower($sAction) == "confirm") { // Email activation
					if ($this->ActivateEmail($sEmail)) { // Activate this email
						if ($this->getSuccessMessage() == "")
							$this->setSuccessMessage($Language->Phrase("ActivateAccount")); // Set up message acount activated
						$this->Page_Terminate("login.php"); // Go to login page
					}
				}
			}
			if ($this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("ActivateFailed")); // Set activate failed message
			$this->Page_Terminate("login.php"); // Go to login page
		}
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "A": // Add
				
				// Check for duplicate User ID
				$sFilter = str_replace("%u", ew_AdjustSql($this->Username->CurrentValue), EW_USER_NAME_FILTER);

				// Set up filter (SQL WHERE clause) and get return SQL
				// SQL constructor in users class, usersinfo.php

				$this->CurrentFilter = $sFilter;
				$sUserSql = $this->SQL();
				//515, 596
				// if ($rs = $conn->Execute($sUserSql)) {
				// 	if (!$rs->EOF) {
				// 		$bUserExists = TRUE;
				// 		$this->RestoreFormValues(); // Restore form values
				// 		$this->setFailureMessage($Language->Phrase("UserExists")); // Set user exist message
				// 	}
				// 	$rs->Close();
				// }
				if (!$bUserExists) {
					$this->SendEmail = TRUE; // Send email on add success
					if ($this->AddRow()) { // Add record source: is addRowTrait.php
							
						// Load user email
						$sReceiverEmail = $this->_Email->CurrentValue;
						if ($sReceiverEmail == "") { // Send to recipient directly
							$sReceiverEmail = EW_RECIPIENT_EMAIL;
							$sBccEmail = "";
						} else { // Bcc recipient
							$sBccEmail = EW_RECIPIENT_EMAIL;
						}

						// Set up email content
						if ($sReceiverEmail <> "") {
							$Email = new cEmail;

							// Begin of modification Email Template based on Selected Language, by Masino Sinaga, May 4, 2012
                            // Begin of modification Activate User Account by Admin, by Masino Sinaga, March 3, 2014

                            if (MS_SUSPEND_NEW_USER_ACCOUNT==TRUE) {

                              // Using the different email template if admin will activate user account
                              $Email->Load('phptxt/registerpending'.$GLOBALS["Language"]->LanguageId.'.txt');
                            } else {

                              // Begin of modification Email Template based on Selected Language, by Masino Sinaga, May 4, 2012
                              $Email->Load('phptxt/register'.$GLOBALS["Language"]->LanguageId.'.txt');

                              // End of modification Email Template based on Selected Language, by Masino Sinaga, May 4, 2012
                            }

                            // End of modification Activate User Account by Admin, by Masino Sinaga, March 3, 2014
                            // End of modification Email Template based on Selected Language, by Masino Sinaga, May 4, 2012
							// Begin of modification Displaying Application Name in Email Template, by Masino Sinaga, June 5, 2012

                            $Email->ReplaceSubject($Language->Phrase("SubjectRegistrationInformation").' '.$Language->ProjectPhrase("BodyTitle"));

                            // End of modification Displaying Application Name in Email Template, by Masino Sinaga, June 5, 2012
                            $Email->ReplaceSender(EW_SENDER_EMAIL); // Replace Sender
                            $Email->ReplaceRecipient($sReceiverEmail); // Replace Recipient
                            if ($sBccEmail <> "") $Email->AddBcc($sBccEmail); // Add Bcc
							$Email->ReplaceContent('<!--FieldCaption_Username-->', $this->Username->FldCaption());
							$Email->ReplaceContent('<!--Username-->', strval($this->Username->FormValue));
							$Email->ReplaceContent('<!--FieldCaption_Password-->', $this->Password->FldCaption());
							$Email->ReplaceContent('<!--Password-->', strval($this->Password->FormValue));
							$Email->ReplaceContent('<!--FieldCaption_First_Name-->', $this->First_Name->FldCaption());
							$Email->ReplaceContent('<!--First_Name-->', strval($this->First_Name->FormValue));
							$Email->ReplaceContent('<!--FieldCaption_Last_Name-->', $this->Last_Name->FldCaption());
							$Email->ReplaceContent('<!--Last_Name-->', strval($this->Last_Name->FormValue));
							$Email->ReplaceContent('<!--FieldCaption_Email-->', $this->_Email->FldCaption());
							$Email->ReplaceContent('<!--Email-->', strval($this->_Email->FormValue));

						// Begin of modification Activate User Account by Admin, by Masino Sinaga, March 3, 2014
                        if (MS_SUSPEND_NEW_USER_ACCOUNT==TRUE) {

                            // there is no activation link if admin will activate the user account
                        } else {
							$sActivateLink = ew_FullUrl() . "?action=confirm";
							$sActivateLink .= "&email=" . $this->_Email->CurrentValue;
							$sToken = ew_Encrypt($this->_Email->CurrentValue) . "," .
								ew_Encrypt($this->Username->CurrentValue) . "," .
								ew_Encrypt($this->Password->FormValue);
							$sActivateLink .= "&token=" . $sToken;
							$Email->ReplaceContent("<!--ActivateLink-->", $sActivateLink);
						}

                        // End of modification Activate User Account by Admin, by Masino Sinaga, March 3, 2014
							$Email->Charset = EW_EMAIL_CHARSET;

							// Get new recordset
							$this->CurrentFilter = $this->KeyFilter();
							$sSql = $this->SQL();
							// $rsnew = $conn->Execute($sSql);
							// $Args = array();
							// $Args["rs"] = $rsnew->fields;
							// $bEmailSent = FALSE;
							// if ($this->Email_Sending($Email, $Args))
							// 	$bEmailSent = $Email->Send();

							// Send email failed
							if (!$bEmailSent)
								$this->setFailureMessage($Email->SendErrDescription);
						}

						// Begin of modification by Masino Sinaga, for saving the registered date time, November 6, 2011
						$UserProfile->Profile[MS_USER_PROFILE_REGISTERED_DATE_TIME] = ew_StdCurrentDateTime();
						$UserProfile->SaveProfileToDatabase($sUsr);

						// End of modification by Masino Sinaga, for saving the registered date time, November 6, 2011		
						// Begin of modification Activate User Account by Admin, by Masino Sinaga, December 6, 2012

                        if ($this->getSuccessMessage() == "") {
                            if (MS_SUSPEND_NEW_USER_ACCOUNT==TRUE) {
                               $this->setSuccessMessage($Language->Phrase("RegisterSuccessPending")); // Activate success
                            } else {
                               $this->setSuccessMessage($Language->Phrase("RegisterSuccessActivate")); // Activate success
                            }
                        }

                        // End of modification Activate User Account by Admin, by Masino Sinaga, December 6, 2012
						$this->Page_Terminate("login.php"); // Return
					} else {
						$this->RestoreFormValues(); // Restore form values
					}
				}
		}

		// Render row
		if ($this->CurrentAction == "F") { // Confirm page
			$this->RowType = EW_ROWTYPE_VIEW; // Render view
		} else {
			$this->RowType = EW_ROWTYPE_ADD; // Render add
		}
		$this->ResetAttrs();
		$this->RenderRow();
	}
}