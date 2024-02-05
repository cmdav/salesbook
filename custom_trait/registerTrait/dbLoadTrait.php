<?php

trait dbLoadTrait{

    
	// Load default values
	function LoadDefaultValues() {
		$this->Username->CurrentValue = NULL;
		$this->Username->OldValue = $this->Username->CurrentValue;
		$this->Password->CurrentValue = NULL;
		$this->Password->OldValue = $this->Password->CurrentValue;
		$this->First_Name->CurrentValue = NULL;
		$this->First_Name->OldValue = $this->First_Name->CurrentValue;
		$this->Last_Name->CurrentValue = NULL;
		$this->Last_Name->OldValue = $this->Last_Name->CurrentValue;
		$this->_Email->CurrentValue = NULL;
		$this->_Email->OldValue = $this->_Email->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Username->FldIsDetailKey) {
			$this->Username->setFormValue($objForm->GetValue("x_Username"));
		}
		if (!$this->Password->FldIsDetailKey) {
			$this->Password->setFormValue($objForm->GetValue("x_Password"));
		}
		$this->Password->ConfirmValue = $objForm->GetValue("c_Password");
		if (!$this->First_Name->FldIsDetailKey) {
			$this->First_Name->setFormValue($objForm->GetValue("x_First_Name"));
		}
		if (!$this->Last_Name->FldIsDetailKey) {
			$this->Last_Name->setFormValue($objForm->GetValue("x_Last_Name"));
		}
		if (!$this->_Email->FldIsDetailKey) {
			$this->_Email->setFormValue($objForm->GetValue("x__Email"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->Username->CurrentValue = $this->Username->FormValue;
		$this->Password->CurrentValue = $this->Password->FormValue;
		$this->First_Name->CurrentValue = $this->First_Name->FormValue;
		$this->Last_Name->CurrentValue = $this->Last_Name->FormValue;
		$this->_Email->CurrentValue = $this->_Email->FormValue;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->Username->setDbValue($rs->fields('Username'));
		$this->Password->setDbValue($rs->fields('Password'));
		$this->First_Name->setDbValue($rs->fields('First_Name'));
		$this->Last_Name->setDbValue($rs->fields('Last_Name'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->User_Level->setDbValue($rs->fields('User_Level'));
		$this->Report_To->setDbValue($rs->fields('Report_To'));
		$this->Activated->setDbValue($rs->fields('Activated'));
		$this->Locked->setDbValue($rs->fields('Locked'));
		$this->Profile->setDbValue($rs->fields('Profile'));
		$this->Current_URL->setDbValue($rs->fields('Current_URL'));
		$this->Theme->setDbValue($rs->fields('Theme'));
		$this->Menu_Horizontal->setDbValue($rs->fields('Menu_Horizontal'));
		$this->Table_Width_Style->setDbValue($rs->fields('Table_Width_Style'));
		$this->Scroll_Table_Width->setDbValue($rs->fields('Scroll_Table_Width'));
		$this->Scroll_Table_Height->setDbValue($rs->fields('Scroll_Table_Height'));
		$this->Rows_Vertical_Align_Top->setDbValue($rs->fields('Rows_Vertical_Align_Top'));
		$this->_Language->setDbValue($rs->fields('Language'));
		$this->Redirect_To_Last_Visited_Page_After_Login->setDbValue($rs->fields('Redirect_To_Last_Visited_Page_After_Login'));
		$this->Font_Name->setDbValue($rs->fields('Font_Name'));
		$this->Font_Size->setDbValue($rs->fields('Font_Size'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Username->DbValue = $row['Username'];
		$this->Password->DbValue = $row['Password'];
		$this->First_Name->DbValue = $row['First_Name'];
		$this->Last_Name->DbValue = $row['Last_Name'];
		$this->_Email->DbValue = $row['Email'];
		$this->User_Level->DbValue = $row['User_Level'];
		$this->Report_To->DbValue = $row['Report_To'];
		$this->Activated->DbValue = $row['Activated'];
		$this->Locked->DbValue = $row['Locked'];
		$this->Profile->DbValue = $row['Profile'];
		$this->Current_URL->DbValue = $row['Current_URL'];
		$this->Theme->DbValue = $row['Theme'];
		$this->Menu_Horizontal->DbValue = $row['Menu_Horizontal'];
		$this->Table_Width_Style->DbValue = $row['Table_Width_Style'];
		$this->Scroll_Table_Width->DbValue = $row['Scroll_Table_Width'];
		$this->Scroll_Table_Height->DbValue = $row['Scroll_Table_Height'];
		$this->Rows_Vertical_Align_Top->DbValue = $row['Rows_Vertical_Align_Top'];
		$this->_Language->DbValue = $row['Language'];
		$this->Redirect_To_Last_Visited_Page_After_Login->DbValue = $row['Redirect_To_Last_Visited_Page_After_Login'];
		$this->Font_Name->DbValue = $row['Font_Name'];
		$this->Font_Size->DbValue = $row['Font_Size'];
	}

	
}