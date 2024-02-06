<?php

trait pageMainTrait{

    
	function Page_Main() {
		global $Security, $Language, $UserProfile, $gsFormError;
		global $Breadcrumb;
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1); // v11.0.4
		$Breadcrumb = new cBreadcrumb;
		$Breadcrumb->Add("login", "LoginPage", $url, "", "", TRUE); // v11.0.4
		$sPassword = "";
		$sLastUrl = $Security->LastUrl(); // Get last URL
		if ($sLastUrl == "")
			$sLastUrl = "index.php";

		// If session expired, show session expired message
		if (@$_GET["expired"] == "1")
			$this->setFailureMessage($Language->Phrase("SessionExpired"));
		if (IsLoggingIn()) {
			$this->Username = @$_SESSION[EW_SESSION_USER_PROFILE_USER_NAME];
			$sPassword = @$_SESSION[EW_SESSION_USER_PROFILE_PASSWORD];
			$this->LoginType = @$_SESSION[EW_SESSION_USER_PROFILE_LOGIN_TYPE];
			$bValidPwd = $Security->ValidateUser($this->Username, $sPassword, FALSE);
			if ($bValidPwd) {
				$_SESSION[EW_SESSION_USER_PROFILE_USER_NAME] = "";
				$_SESSION[EW_SESSION_USER_PROFILE_PASSWORD] = "";
				$_SESSION[EW_SESSION_USER_PROFILE_LOGIN_TYPE] = "";
			}
		} else {
			if (!$Security->IsLoggedIn())
				$Security->AutoLogin();
			$Security->LoadUserLevel(); // Load user level
			$this->Username = ""; // Initialize
			$encrypted = FALSE; // v12
			if (isset($_POST["username"])) {
				// handle authentication along side with if ($bValidate)
               
				$this->Username = ew_RemoveXSS(ew_StripSlashes($_POST["username"]));
				$sPassword = ew_RemoveXSS(ew_StripSlashes(@$_POST["password"]));;
				$this->LoginType = strtolower(ew_RemoveXSS(@$_POST["type"]));
				
			} else if (EW_ALLOW_LOGIN_BY_URL && isset($_GET["username"])) {
               
				$this->Username = ew_RemoveXSS(ew_StripSlashes($_GET["username"]));
				$sPassword = ew_RemoveXSS(ew_StripSlashes(@$_GET["password"]));
				$this->LoginType = strtolower(ew_RemoveXSS(@$_GET["type"]));
				$encrypted = !empty($_GET["encrypted"]);
			} // v12
			if ($this->Username <> "") {
                
				$bValidate = $this->ValidateForm($this->Username, $sPassword);
				if (!$bValidate)
					$this->setFailureMessage($gsFormError);
					$_SESSION[EW_SESSION_USER_LOGIN_TYPE] = $this->LoginType; // Save user login type
					$_SESSION[EW_SESSION_USER_PROFILE_USER_NAME] = $this->Username; // Save login user name
					$_SESSION[EW_SESSION_USER_PROFILE_LOGIN_TYPE] = $this->LoginType; // Save login type

				// Max login attempt checking
				if ($UserProfile->ExceedLoginRetry($this->Username)) {
					$bValidate = FALSE;

					// $this->setFailureMessage(str_replace("%t", EW_USER_PROFILE_RETRY_LOCKOUT, $Language->Phrase("ExceedMaxRetry")));
					// Begin of modification How Long User Should be Allowed Login in the Messages When Failed Login Exceeds the Maximum, by Masino Sinaga, May 12, 2012

                    $this->setFailureMessage(str_replace("%t", Duration( date("Y-m-d H:i:s"), CurrentDateTime_Add_Minutes( $UserProfile->getValue( EW_USER_PROFILE_LAST_BAD_LOGIN_DATE_TIME), EW_USER_PROFILE_RETRY_LOCKOUT)), $Language->Phrase("ExceedMaxRetryNew")));

					// End of modification How Long User Should be Allowed Login in the Messages When Failed Login Exceeds the Maximum, by Masino Sinaga, May 12, 2012
				}
			} else {
                // die('username not entered');
				if ($Security->IsLoggedIn()) {
					if ($this->getFailureMessage() == "")
						$this->Page_Terminate($sLastUrl); // Return to last accessed page
				}
				$bValidate = FALSE;

				// Restore settings
				if (@$_COOKIE[EW_PROJECT_NAME]['Checksum'] == strval(crc32(md5(EW_RANDOM_KEY))))
					$this->Username = ew_Decrypt(@$_COOKIE[EW_PROJECT_NAME]['Username']);
				if (@$_COOKIE[EW_PROJECT_NAME]['AutoLogin'] == "autologin") {
					$this->LoginType = "a";
				} elseif (@$_COOKIE[EW_PROJECT_NAME]['AutoLogin'] == "rememberusername") {
					$this->LoginType = "u";
				} else {
					$this->LoginType = "";
				}
			}
			$bValidPwd = FALSE;
			if (MS_SHOW_CAPTCHA_ON_LOGIN_PAGE) {

                // CAPTCHA checking
                if (ew_IsHttpPost()) {
                    $this->captcha = @$_POST["captcha"];
                    if (!$this->ValidateCaptcha()) { // CAPTCHA unmatched
                        $this->setFailureMessage($Language->Phrase("EnterValidateCode")); // Set message
                        $bValidate = FALSE;
                    }
                }
                if (!$bValidate) {
                    $this->ResetCaptcha();
                }
			}
			if ($bValidate) {
                 //die('user validated');
				// Call Logging In event
				$bValidate = $this->User_LoggingIn($this->Username, $sPassword);

				if ($bValidate) {
					//validateUser is called from phpfn12.php via validateUserTrait
					$bValidPwd = $Security->ValidateUser($this->Username, $sPassword, FALSE, $encrypted); // Manual login v12

					if (!$bValidPwd) {
						// die('invalid');
						// Password expired, force change password
						if (IsPasswordExpired()) {
							$this->setFailureMessage($Language->Phrase("PasswordExpired"));
							$this->Page_Terminate("changepwd.php");
						}
						if ($this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("InvalidUidPwd")); // Invalid user id/password

					// Password changed date not initialized, set as today
					} elseif ($UserProfile->EmptyPasswordChangedDate($this->Username)) {
						// die('cancelled e');
						$UserProfile->SetValue(EW_USER_PROFILE_LAST_PASSWORD_CHANGED_DATE, ew_StdCurrentDate());
						$UserProfile->SaveProfileToDatabase($this->Username);
					}
				} else {
			
					 
					if ($this->getFailureMessage() == "")
						$this->setFailureMessage($Language->Phrase("LoginCancelled")); // Login cancelled
				}
			}
		}
		if ($bValidPwd) {
			// Write cookies
			if ($this->LoginType == "a") { // Auto login
				setcookie(EW_PROJECT_NAME . '[AutoLogin]',  "autologin", EW_COOKIE_EXPIRY_TIME); // Set autologin cookie
				setcookie(EW_PROJECT_NAME . '[Username]', ew_Encrypt($this->Username), EW_COOKIE_EXPIRY_TIME); // Set user name cookie
				setcookie(EW_PROJECT_NAME . '[Password]', ew_Encrypt($sPassword), EW_COOKIE_EXPIRY_TIME); // Set password cookie
				setcookie(EW_PROJECT_NAME . '[Checksum]', crc32(md5(EW_RANDOM_KEY)), EW_COOKIE_EXPIRY_TIME);
			} elseif ($this->LoginType == "u") { // Remember user name
				setcookie(EW_PROJECT_NAME . '[AutoLogin]', "rememberusername", EW_COOKIE_EXPIRY_TIME); // Set remember user name cookie
				setcookie(EW_PROJECT_NAME . '[Username]', ew_Encrypt($this->Username), EW_COOKIE_EXPIRY_TIME); // Set user name cookie
				setcookie(EW_PROJECT_NAME . '[Checksum]', crc32(md5(EW_RANDOM_KEY)), EW_COOKIE_EXPIRY_TIME);
			} else {
				setcookie(EW_PROJECT_NAME . '[AutoLogin]', "", EW_COOKIE_EXPIRY_TIME); // Clear auto login cookie
			}

			// Begin of modification by Masino Sinaga, for saving the last login date time, November 6, 2011
			$UserProfile->Profile[MS_USER_PROFILE_LAST_LOGIN_DATE_TIME] = ew_StdCurrentDateTime();

			$UserProfile->SaveProfileToDatabase($this->Username);

			// End of modification by Masino Sinaga, for saving the last login date time, November 6, 2011
			// Call loggedin event

			$this->User_LoggedIn($this->Username);
			
			// Begin of modification Load Sessions for Application Settings and User Preferences, by Masino Sinaga, September 22, 2014
			// LoadApplicationSettings();
			// LoadUserPreferences();
			// End of modification Load Sessions for Application Settings and User Preferences, by Masino Sinaga, September 22, 2014

			$this->Page_Terminate($sLastUrl); // Return to last accessed URL
		} elseif ($this->Username <> "" && $sPassword <> "") {

			// Call user login error event
			$this->User_LoginError($this->Username, $sPassword);
		}
	}
}