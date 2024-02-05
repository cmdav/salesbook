<?php



    class cregister extends cusers {
        use  RenderRowTrait,
                showMessageTrait, 
                pageMainTrait,
             pageInitTrait,
             passwordStrengthTrait,
                validateFormTrait,
             addRowTrait,
             dbLoadTrait,
             pageTerminateTrait,
             activateEmailTrait;
             
         // Page ID
         var $PageID = 'register';
     
         // Project ID
         var $ProjectID = "{B36B93AF-B58F-461B-B767-5F08C12493E9}";
     
         // Page object name
         var $PageObjName = 'register';
     
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
             $this->TokenTimeout = ew_SessionTimeoutTime();
     
             // Language object
             if (!isset($Language)) $Language = new cLanguage();
     
             // Parent constuctor
             parent::__construct();
     
             // Table object (users)
             if (!isset($GLOBALS["users"]) || get_class($GLOBALS["users"]) == "cusers") {
                 $GLOBALS["users"] = &$this;
                 $GLOBALS["Table"] = &$GLOBALS["users"];
             }
             if (!isset($GLOBALS["users"])) $GLOBALS["users"] = new cusers();
     
             // Page ID
             if (!defined("EW_PAGE_ID"))
     
                 define("EW_PAGE_ID", 'register');
     
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
     
         
     
         //
         // Page_Terminate
         
         var $FormClassName = "form-horizontal ewForm ewRegisterForm";
     
         // CAPTCHA
         var $captcha;
     
         // Validate Captcha
         function ValidateCaptcha() {
             return true;
             return ($this->captcha == @$_SESSION["EW_CAPTCHA_CODE"]);
         }
     
         // Reset Captcha
         function ResetCaptcha() {
             $_SESSION["EW_CAPTCHA_CODE"] = ew_Random();
         }
     
         
     
         // Get upload files
         function GetUploadFiles() {
             global $objForm, $Language;
     
             // Get upload data
         }
     
         
         // Render row values based on field settings
         
         // Validate form
         
         // Add record
         
     
         // Set up Breadcrumb
         function SetupBreadcrumb() {
             global $Breadcrumb, $Language;
             $Breadcrumb = new cBreadcrumb();
         }
     
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
     
         // Email Sending event
         function Email_Sending(&$Email, &$Args) {
     
             //var_dump($Email); var_dump($Args); exit();
             return TRUE;
         }
     
         // Form Custom Validate event
         function Form_CustomValidate(&$CustomError) {
     
             // Return error message in CustomError
             return TRUE;
         }
     
         // User Registered event
         function User_Registered(&$rs) {
     
           //echo "User_Registered";
         }
     
         // User Activated event
         function User_Activated(&$rs) {
     
           //echo "User_Activated";
         }
     }
