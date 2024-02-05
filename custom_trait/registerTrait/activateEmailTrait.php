<?php

trait activateEmailTrait{

  // Activate account based on email
	function ActivateEmail($email) {
		global $conn, $Language;
		$sFilter = str_replace("%e", ew_AdjustSql($email), EW_USER_EMAIL_FILTER);
		$sSql = $this->GetSQL($sFilter, "");
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"]; // v11.0.4
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if (!$rs)
			return FALSE;
		if (!$rs->EOF) {
			$rsnew = $rs->fields;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
			$rsact = array('Activated' => "Y"); // Auto register
			$this->CurrentFilter = $sFilter;
			$res = $this->Update($rsact);
			if ($res) { // Call User Activated event
				$rsnew['Activated'] = "Y";
				$this->User_Activated($rsnew);
			}
			return $res;
		} else {
			$this->setFailureMessage($Language->Phrase("NoRecord"));
			$rs->Close();
			return FALSE;
		}
	}
}