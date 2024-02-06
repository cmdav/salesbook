<?php

trait validateUserTrait{

    
	function ValidateUser(&$usr, &$pwd, $autologin, $encrypted = FALSE) {
		global $Language;
		global $UserTable, $UserTableConn;
		global $UserProfile;
		$ValidateUser = FALSE;
		$CustomValidateUser = FALSE;

		// Call User Custom Validate event
		if (EW_USE_CUSTOM_LOGIN) {
			$CustomValidateUser = $this->User_CustomValidate($usr, $pwd);
			if ($CustomValidateUser) {
				$_SESSION[EW_SESSION_STATUS] = "login";
				$this->setCurrentUserName($usr); // Load user name
			}
		}

		// Check hard coded admin first
		if (!$ValidateUser) {
				
			if (EW_CASE_SENSITIVE_PASSWORD) {
				$ValidateUser = (!$CustomValidateUser && EW_ADMIN_USER_NAME == $usr && EW_ADMIN_PASSWORD == $pwd) ||
								($CustomValidateUser && EW_ADMIN_USER_NAME == $usr);
			} else {
				//Validate admin login EW_ADMIN_USER_NAME is set from ewcfg12.php
				$ValidateUser = (!$CustomValidateUser && strtolower(EW_ADMIN_USER_NAME) == strtolower($usr)
								&& strtolower(EW_ADMIN_PASSWORD) == strtolower($pwd)) ||
								($CustomValidateUser && strtolower(EW_ADMIN_USER_NAME) == strtolower($usr));
			}
			
			if ($ValidateUser) {
				// Update admin level
				$_SESSION[EW_SESSION_STATUS] = "login";
				$_SESSION[EW_SESSION_SYS_ADMIN] = 1; // System Administrator
				$this->setCurrentUserName($Language->Phrase("UserAdministrator")); // Load user name
				$this->setSessionUserLevelID(-1); // System Administrator
				$this->SetUpUserLevel();
				$UserProfile->SetValue(EW_USER_PROFILE_LAST_PASSWORD_CHANGED_DATE, ew_StdCurrentDate());
				
				
			}
		}

		// Check other users
		if (!$ValidateUser) {

			// die('other users');
			$sFilter = str_replace("%u", ew_AdjustSql($usr, EW_USER_TABLE_DBID), EW_USER_NAME_FILTER);

			$sFilter .= " AND " . EW_USER_ACTIVATE_FILTER;

			// Set up filter (SQL WHERE clause) and get return SQL
			// SQL constructor in <UserTable> class, <UserTable>info.php
			
            //GetSQL is called from  usersinfo.php return sql query
			$sSql = $UserTable->GetSQL($sFilter, "");
			
			if ($rs = $UserTableConn->Execute($sSql)) {
				
				
				if (!$rs->EOF) {
					// validate password
					$ValidateUser = $CustomValidateUser || ew_ComparePassword($rs->fields('Password'), $pwd, $encrypted);
				
					
					// Set up retry count from manual login
					if (!$autologin) {
						
						$UserProfile->LoadProfileFromDatabase($usr);
						if (!$ValidateUser) {
							$retrycount = $UserProfile->GetValue(EW_USER_PROFILE_LOGIN_RETRY_COUNT);
							$retrycount++;
							$UserProfile->SetValue(EW_USER_PROFILE_LOGIN_RETRY_COUNT, $retrycount);
							$UserProfile->SetValue(EW_USER_PROFILE_LAST_BAD_LOGIN_DATE_TIME, ew_StdCurrentDateTime());
						} else {
							$UserProfile->SetValue(EW_USER_PROFILE_LOGIN_RETRY_COUNT, 0);
						}
						$UserProfile->SaveProfileToDatabase($usr); // Save profile
					}

					// Check concurrent user login
					if ($ValidateUser) {
						
						// Begin of modification How to Overcome "User X already logged in" Issue, by Masino Sinaga, July 22, 2014
						
						$sCookieSessionID = @$_COOKIE[EW_PROJECT_NAME][EW_USER_PROFILE_SESSION_ID];
						
						if (EW_USER_PROFILE_CONCURRENT_SESSION_COUNT == 1) { // allowed 1 user for concurrent user (<= v10)
							if ($UserProfile->IsValidUser($usr, session_id())) {
								//reached;
								$UserProfile->SaveProfileToDatabase($usr); // Save profile
							} elseif ($UserProfile->IsValidUser($usr, $sCookieSessionID)) { // Login from same user
							
								$UserProfile->SetValue(EW_USER_PROFILE_SESSION_ID, session_id()); // Use current Session ID
								$UserProfile->SaveProfileToDatabase($usr); // Save profile
								setcookie(EW_PROJECT_NAME . '[' . EW_USER_PROFILE_SESSION_ID . ']', session_id(), EW_COOKIE_EXPIRY_TIME); // Save current Session ID to Cookie
							} else {
								
								$_SESSION[EW_SESSION_FAILURE_MESSAGE] = str_replace("%u", $usr, $Language->Phrase("UserAlreadyLoggedIn"));

								// Begin of modification How to Overcome "User X already logged in" Issue, by Masino Sinaga, February 16, 2013
								$UserProfile->SetValue(EW_USER_PROFILE_SESSION_ID, session_id()); // Use current Session ID
								$UserProfile->SaveProfileToDatabase($usr); // Save profile
								setcookie(EW_PROJECT_NAME . '[' . EW_USER_PROFILE_SESSION_ID . ']', session_id(), EW_COOKIE_EXPIRY_TIME); // Save current Session ID to Cookie
								$ValidateUser = FALSE;

								// End of modification How to Overcome "User X already logged in" Issue, by Masino Sinaga, February 16, 2013
							}
						} else { 
						
							// this is EW_USER_PROFILE_CONCURRENT_SESSION_COUNT > 1 (>= v11)
							if ($UserProfile->IsValidUser($usr, session_id())) {
							} else {
							
								$_SESSION[EW_SESSION_FAILURE_MESSAGE] = str_replace("%u", $usr, $Language->Phrase("UserLoggedIn"));
								$ValidateUser = FALSE;
							}
						} // end EW_USER_PROFILE_CONCURRENT_SESSION_COUNT checking

						// End of modification How to Overcome "User X already logged in" Issue, by Masino Sinaga, July 22, 2014
					}

					// Password expiry checking
					if ($ValidateUser && !$autologin && $UserProfile->PasswordExpired($usr)) {
							$this->SetSessionPasswordExpired();
							$row = $rs->fields;
							$this->User_PasswordExpired($row);
							if (IsPasswordExpired()) {
								$rs->Close();
								return FALSE;
							}
					}
					if ($ValidateUser) {
						
						$_SESSION[EW_SESSION_STATUS] = "login";
						$_SESSION[EW_SESSION_SYS_ADMIN] = 0; // Non System Administrator
						$this->setCurrentUserName($rs->fields('Username')); // Load user name
						if (is_null($rs->fields('User_Level'))) {
							$this->setSessionUserLevelID(-1);
						} else {
							$this->setSessionUserLevelID(intval($rs->fields('User_Level'))); // Load User Level
						}
						$this->SetUpUserLevel();

						// Call User Validated event
						$row = $rs->fields;
						$ValidateUser = $this->User_Validated($row) !== FALSE; // For backward compatibility
					}
				}
				$rs->Close();
			}
		}
		if ($CustomValidateUser)
			return $CustomValidateUser;
		if (!$ValidateUser && !IsPasswordExpired())
		
		
			$_SESSION[EW_SESSION_STATUS] = ""; // Clear login status
		return $ValidateUser;
	}
}