<?php

trait passwordStrengthTrait{

    
	function CheckPasswordStrength() {
        global $Language, $gsFormError, $gsLanguage;

        // Check if validation required
        if (!EW_SERVER_VALIDATE)
            return TRUE;

        // Initialize form error message
        $gsFormError = "";
		if (MS_PASSWORD_POLICY_FROM_MASINO_REGISTER == TRUE) { 

			// Begin of modification Strong Password Policies/Rules, by Masino Sinaga, June 12, 2012
			if (MS_PASSWORD_MUST_COMPLY_WITH_MIN_LENGTH==TRUE) {
				if( strlen($npwd) < MS_PASSWORD_MINIMUM_LENGTH ) {

					//$this->setFailureMessage(str_replace("%n", MS_PASSWORD_MINIMUM_LENGTH, $Language->Phrase("ErrorPassTooShort")));
					//$isError = TRUE;

					ew_AddMessage($gsFormError, str_replace("%n", MS_PASSWORD_MINIMUM_LENGTH, $Language->Phrase("ErrorPassTooShort")));
				}
			}
			if (MS_PASSWORD_MUST_COMPLY_WITH_MAX_LENGTH==TRUE) {
				if( strlen($npwd) > MS_PASSWORD_MAXIMUM_LENGTH ) {

					//$this->setFailureMessage(str_replace("%n", MS_PASSWORD_MAXIMUM_LENGTH, $Language->Phrase("ErrorPassTooLong")));
					//$isError = TRUE;

					ew_AddMessage($gsFormError, str_replace("%n", MS_PASSWORD_MAXIMUM_LENGTH, $Language->Phrase("ErrorPassTooLong")));
				}
			}
			if (MS_PASSWORD_MUST_INCLUDE_AT_LEAST_ONE_NUMBER==TRUE) {
				if( !preg_match("#[0-9]+#", $npwd) ) {

					//$this->setFailureMessage($Language->Phrase("ErrorPassDoesNotIncludeNumber"));
					//$isError = TRUE;

					ew_AddMessage($gsFormError, $Language->Phrase("ErrorPassDoesNotIncludeNumber"));
				}
			}
			if (MS_PASSWORD_MUST_INCLUDE_AT_LEAST_ONE_LETTER==TRUE) {
				if( !preg_match("#[a-z]+#", $npwd) ) {

					//$this->setFailureMessage($Language->Phrase("ErrorPassDoesNotIncludeLetter"));
					//$isError = TRUE;

					ew_AddMessage($gsFormError, $Language->Phrase("ErrorPassDoesNotIncludeLetter"));
				}
			}
			if (MS_PASSWORD_MUST_INCLUDE_AT_LEAST_ONE_CAPS==TRUE) {
				if( !preg_match("#[A-Z]+#", $npwd) ) {

					//$this->setFailureMessage($Language->Phrase("ErrorPassDoesNotIncludeCaps"));
					//$isError = TRUE;

					ew_AddMessage($gsFormError, $Language->Phrase("ErrorPassDoesNotIncludeCaps"));
				}
			}
			if (MS_PASSWORD_MUST_INCLUDE_AT_LEAST_ONE_SYMBOL==TRUE) {
				if( !preg_match("#\W+#", $npwd) ) {

					//$this->setFailureMessage($Language->Phrase("ErrorPassDoesNotIncludeSymbol"));
					//$isError = TRUE;

					ew_AddMessage($gsFormError, $Language->Phrase("ErrorPassDoesNotIncludeSymbol"));
				}
			}
			if (MS_PASSWORD_MUST_DIFFERENT_OLD_AND_NEW==TRUE) {
				if ($opwd==$npwd) {

					//$this->setFailureMessage($Language->Phrase("ErrorPassCouldNotBeSame"));
					//$isError = TRUE;

					ew_AddMessage($gsFormError, $Language->Phrase("ErrorPassCouldNotBeSame"));
				}
			}

			// End of modification Strong Password Policies/Rules, by Masino Sinaga, June 12, 2012
		} else {
		}

        // Return validate result
        $valid = ($gsFormError == "");
        return $valid;
    }
}