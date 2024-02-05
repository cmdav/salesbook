<?php

trait RenderRowTrait {
    function RenderRow() {
        global $Security, $Language, $gsLanguage;

        // Initialize URLs
        // Call Row_Rendering event

        $this->Row_Rendering();

        // Common render codes for all row types
        // Username
        // Password
        // First_Name
        // Last_Name
        // Email
        // User_Level
        // Report_To
        // Activated
        // Locked
        // Profile
        // Current_URL
        // Theme
        // Menu_Horizontal
        // Table_Width_Style
        // Scroll_Table_Width
        // Scroll_Table_Height
        // Rows_Vertical_Align_Top
        // Language
        // Redirect_To_Last_Visited_Page_After_Login
        // Font_Name
        // Font_Size

        if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

        // Username
        $this->Username->ViewValue = $this->Username->CurrentValue;
        $this->Username->ViewCustomAttributes = "";

        // Password
        $this->Password->ViewValue = $Language->Phrase("PasswordMask");
        $this->Password->ViewCustomAttributes = "";

        // First_Name
        $this->First_Name->ViewValue = $this->First_Name->CurrentValue;
        $this->First_Name->ViewCustomAttributes = "";

        // Last_Name
        $this->Last_Name->ViewValue = $this->Last_Name->CurrentValue;
        $this->Last_Name->ViewCustomAttributes = "";

        // Email
        $this->_Email->ViewValue = $this->_Email->CurrentValue;
        $this->_Email->ViewCustomAttributes = "";

        // Activated
        if (ew_ConvertToBool($this->Activated->CurrentValue)) {
            $this->Activated->ViewValue = $this->Activated->FldTagCaption(2) <> "" ? $this->Activated->FldTagCaption(2) : "Yes";
        } else {
            $this->Activated->ViewValue = $this->Activated->FldTagCaption(1) <> "" ? $this->Activated->FldTagCaption(1) : "No";
        }
        $this->Activated->ViewCustomAttributes = "";

        // Locked
        if (ew_ConvertToBool($this->Locked->CurrentValue)) {
            $this->Locked->ViewValue = $this->Locked->FldTagCaption(1) <> "" ? $this->Locked->FldTagCaption(1) : "Yes";
        } else {
            $this->Locked->ViewValue = $this->Locked->FldTagCaption(2) <> "" ? $this->Locked->FldTagCaption(2) : "No";
        }
        $this->Locked->ViewCustomAttributes = "";

            // Username
            $this->Username->LinkCustomAttributes = "";
            $this->Username->HrefValue = "";
            $this->Username->TooltipValue = "";

            // Password
            $this->Password->LinkCustomAttributes = "";
            $this->Password->HrefValue = "";
            $this->Password->TooltipValue = "";

            // First_Name
            $this->First_Name->LinkCustomAttributes = "";
            $this->First_Name->HrefValue = "";
            $this->First_Name->TooltipValue = "";

            // Last_Name
            $this->Last_Name->LinkCustomAttributes = "";
            $this->Last_Name->HrefValue = "";
            $this->Last_Name->TooltipValue = "";

            // Email
            $this->_Email->LinkCustomAttributes = "";
            $this->_Email->HrefValue = "";
            $this->_Email->TooltipValue = "";
        } elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

            // Username
            $this->Username->EditAttrs["class"] = "form-control";
            $this->Username->EditCustomAttributes = "";
            $this->Username->EditValue = ew_HtmlEncode($this->Username->CurrentValue);
            $this->Username->PlaceHolder = ew_RemoveHtml($this->Username->FldCaption());

            // Password
            $this->Password->EditAttrs["class"] = "form-control ewPasswordStrength";
            $this->Password->EditCustomAttributes = "";
            $this->Password->EditValue = ew_HtmlEncode($this->Password->CurrentValue);
            $this->Password->PlaceHolder = ew_RemoveHtml($this->Password->FldCaption());

            // First_Name
            $this->First_Name->EditAttrs["class"] = "form-control";
            $this->First_Name->EditCustomAttributes = "";
            $this->First_Name->EditValue = ew_HtmlEncode($this->First_Name->CurrentValue);
            $this->First_Name->PlaceHolder = ew_RemoveHtml($this->First_Name->FldCaption());

            // Last_Name
            $this->Last_Name->EditAttrs["class"] = "form-control";
            $this->Last_Name->EditCustomAttributes = "";
            $this->Last_Name->EditValue = ew_HtmlEncode($this->Last_Name->CurrentValue);
            $this->Last_Name->PlaceHolder = ew_RemoveHtml($this->Last_Name->FldCaption());

            // Email
            $this->_Email->EditAttrs["class"] = "form-control";
            $this->_Email->EditCustomAttributes = "";
            $this->_Email->EditValue = ew_HtmlEncode($this->_Email->CurrentValue);
            $this->_Email->PlaceHolder = ew_RemoveHtml($this->_Email->FldCaption());

            // Add refer script
            // Username

            $this->Username->LinkCustomAttributes = "";
            $this->Username->HrefValue = "";

            // Password
            $this->Password->LinkCustomAttributes = "";
            $this->Password->HrefValue = "";

            // First_Name
            $this->First_Name->LinkCustomAttributes = "";
            $this->First_Name->HrefValue = "";

            // Last_Name
            $this->Last_Name->LinkCustomAttributes = "";
            $this->Last_Name->HrefValue = "";

            // Email
            $this->_Email->LinkCustomAttributes = "";
            $this->_Email->HrefValue = "";
        }
        if ($this->RowType == EW_ROWTYPE_ADD ||
            $this->RowType == EW_ROWTYPE_EDIT ||
            $this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
            $this->SetupFieldTitles();
        }

        // Call Row Rendered event
        if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
            $this->Row_Rendered();
    }
}