<?php
    class clogin extends cusers {

        use showMessageTrait, pageInitTrait, pageMainTrait, validateFormTrait, pageTerminateTrait;
        // Page ID
        var $PageID = 'login';
    
        // Project ID
        var $ProjectID = "{B36B93AF-B58F-461B-B767-5F08C12493E9}";
    
        // Page object name
        var $PageObjName = 'login';
    
        // Page name
        function PageName() {
            return ew_CurrentPage();
        }
    
        // Page URL
        function PageUrl() {
            $PageUrl = ew_CurrentPage() . "?";
            return $PageUrl;
        }
    
        // Message
        function getMessage() {
            return @$_SESSION[EW_SESSION_MESSAGE];
        }
    
        function setMessage($v) {
            ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
        }
    
        function getFailureMessage() {
            return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
        }
    
        function setFailureMessage($v) {
            ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
        }
    
        function getSuccessMessage() {
            return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
        }
    
        function setSuccessMessage($v) {
            ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
        }
    
        function getWarningMessage() {
            return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
        }
    
        function setWarningMessage($v) {
            ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
        }
    
        // Methods to clear message
        function ClearMessage() {
            $_SESSION[EW_SESSION_MESSAGE] = "";
        }
    
        function ClearFailureMessage() {
            $_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
        }
    
        function ClearSuccessMessage() {
            $_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
        }
    
        function ClearWarningMessage() {
            $_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
        }
    
        function ClearMessages() {
            $_SESSION[EW_SESSION_MESSAGE] = "";
            $_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
            $_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
            $_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
        }
    
        // Show message:moved to trait
        
        var $PageHeader;
        var $PageFooter;
    
        // Show Page Header
        function ShowPageHeader() {
            $sHeader = $this->PageHeader;
            $this->Page_DataRendering($sHeader);
            if ($sHeader <> "") { // Header exists, display
                echo "<p>" . $sHeader . "</p>";
            }
        }
    
        // Show Page Footer
        function ShowPageFooter() {
            $sFooter = $this->PageFooter;
            $this->Page_DataRendered($sFooter);
            if ($sFooter <> "") { // Footer exists, display
                echo "<p>" . $sFooter . "</p>";
            }
        }
    
        // Validate page request
        function IsPageRequest() {
            return TRUE;
        }
        var $Token = "";
        var $TokenTimeout = 0;
        var $CheckToken = EW_CHECK_TOKEN;
        var $CheckTokenFn = "ew_CheckToken";
        var $CreateTokenFn = "ew_CreateToken";
    
        // Valid Post
        function ValidPost() {
            if (!$this->CheckToken || !ew_IsHttpPost())
                return TRUE;
            if (!isset($_POST[EW_TOKEN_NAME]))
                return FALSE;
            $fn = $this->CheckTokenFn;
            if (is_callable($fn))
                return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
            return FALSE;
        }
    
        // Create Token
        function CreateToken() {
            global $gsToken;
            if ($this->CheckToken) {
                $fn = $this->CreateTokenFn;
                if ($this->Token == "" && is_callable($fn)) // Create token
                    $this->Token = $fn();
                $gsToken = $this->Token; // Save to global variable
            }
        }
    
        //
        // Page class constructor
        //
        function __construct() {
            global $conn, $Language;
            global $UserTable, $UserTableConn;
            $GLOBALS["Page"] = &$this;
            $this->TokenTimeout = 48 * 60 * 60; // 48 hours for login
    
            // Language object
            if (!isset($Language)) $Language = new cLanguage();
    
            // Parent constuctor
            parent::__construct();
    
            // Table object (users)
            if (!isset($GLOBALS["users"]) || get_class($GLOBALS["users"]) == "cusers") {
                $GLOBALS["users"] = &$this;
                $GLOBALS["Table"] = &$GLOBALS["users"];
            }
            if (!isset($GLOBALS["users"])) $GLOBALS["users"] = &$this;
    
            // Page ID
            if (!defined("EW_PAGE_ID"))
                define("EW_PAGE_ID", 'login');
    
            // Start timer
            if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();
    
            // Open connection
            if (!isset($conn)) $conn = ew_Connect($this->DBID);
    
            // User table object (users)
            if (!isset($UserTable)) {
                $UserTable = new cusers();
                $UserTableConn = Conn($UserTable->DBID);
            }
        }
    
         
        //  Page_Init:move to trait
        //
        // Page_Terminate:move to trait
        //
        
    
        // CAPTCHA
        var $captcha;
    
        // Validate Captcha
        function ValidateCaptcha() {
            return true;
            // return ($this->captcha == @$_SESSION["EW_CAPTCHA_CODE"]);
        }
    
        // Reset Captcha
        function ResetCaptcha() {
            $_SESSION["EW_CAPTCHA_CODE"] = ew_Random();
        }
        var $Username;
        var $LoginType;
    
        //
        // Page main:moved to trait
        //
        // Validate form:moved to trait
        
    
        // Page Load event
        function Page_Load() {
    
            //echo "Page Load";
        }
    
        // Page Unload event
        function Page_Unload() {
    
            //echo "Page Unload";
        }
    
        // Page Redirecting event
        function Page_Redirecting(&$url) {
    
            // Example:
            //$url = "your URL";
    
        }
    
        // Message Showing event
        // $type = ''|'success'|'failure'
        function Message_Showing(&$msg, $type) {
    
            // Example:
            //if ($type == 'success') $msg = "your success message";
    
        }
    
        // Page Render event
        function Page_Render() {
    
            //echo "Page Render";
        }
    
        // Page Data Rendering event
        function Page_DataRendering(&$header) {
    
            // Example:
            //$header = "your header";
    
        }
    
        // Page Data Rendered event
        function Page_DataRendered(&$footer) {
    
            // Example:
            //$footer = "your footer";
    
        }
    
        // User Logging In event
        function User_LoggingIn($usr, &$pwd) {
    
            // Enter your code here
            // To cancel, set return value to FALSE
    
            return TRUE;
        }
    
        // User Logged In event
        function User_LoggedIn($usr) {
    
            //echo "User Logged In";
        }
    
        // User Login Error event
        function User_LoginError($usr, $pwd) {
    
            //echo "User Login Error";
        }
    
        // Form Custom Validate event
        function Form_CustomValidate(&$CustomError) {
    
            // Return error message in CustomError
            return TRUE;
        }
    }