<?php

// Begin of modification Use Constants in Configuration File, by Masino Sinaga, July 3, 2013
// Put this always at the top of configuration file so you can change it easily!

define("MS_USE_CONSTANTS_IN_CONFIG_FILE", FALSE); // this is useful if you don't want to use the configuration settings from database, just use from this file!

// End of modification Use Constants in Configuration File, by Masino Sinaga, July 3, 2013
?>
<?php

/**
 * PHPMaker 12 configuration file
 */

// Relative path
if (!isset($EW_RELATIVE_PATH)) $EW_RELATIVE_PATH = ""; // v11.0.4

// Show SQL for debug
define("EW_DEBUG_ENABLED", FALSE); // TRUE to debug
if (EW_DEBUG_ENABLED) {
	@ini_set("display_errors", "1"); // Display errors
	error_reporting(E_ALL ^ E_NOTICE); // Report all errors except E_NOTICE
}

// General
define("EW_IS_WINDOWS", (strtolower(substr(PHP_OS, 0, 3)) === 'win')); // Is Windows OS
define("EW_IS_PHP5", (phpversion() >= "5.3.0")); // Is PHP 5.3 or later
if (!EW_IS_PHP5) die("This script requires PHP 5.3 or later. You are running " . phpversion() . ".");
define("EW_PATH_DELIMITER", ((EW_IS_WINDOWS) ? "\\" : "/")); // Physical path delimiter
$EW_ROOT_RELATIVE_PATH = "."; // Relative path of app root
define("EW_DEFAULT_DATE_FORMAT", "yyyy/mm/dd"); // Default date format
define("EW_DEFAULT_DATE_FORMAT_ID", "9"); // Default date format
define("EW_DATE_SEPARATOR", "/"); // Date separator
define("EW_UNFORMAT_YEAR", 50); // Unformat year
define("EW_PROJECT_NAME", "php_stock"); // Project name
define("EW_CONFIG_FILE_FOLDER", EW_PROJECT_NAME . ""); // Config file name
define("EW_PROJECT_ID", "{B36B93AF-B58F-461B-B767-5F08C12493E9}"); // Project ID (GUID)
$EW_RELATED_PROJECT_ID = "";
$EW_RELATED_LANGUAGE_FOLDER = "";
define("EW_RANDOM_KEY", 'eFBaNTBLTz0X04IF'); // Random key for encryption
define("EW_PROJECT_STYLESHEET_FILENAME", "phpcss/theme-default.css"); // "phpcss/php_stock.css"); // Project stylesheet file name
define("EW_CHARSET", "utf-8"); // Project charset
define("EW_EMAIL_CHARSET", EW_CHARSET); // Email charset
define("EW_EMAIL_KEYWORD_SEPARATOR", ""); // Email keyword separator
$EW_COMPOSITE_KEY_SEPARATOR = ","; // Composite key separator
define("EW_HIGHLIGHT_COMPARE", TRUE); // Highlight compare mode, TRUE(case-insensitive)|FALSE(case-sensitive)
if (!function_exists('xml_parser_create') && !class_exists("DOMDocument")) die("This script requires PHP XML Parser or DOM.");
define('EW_USE_DOM_XML', ((!function_exists('xml_parser_create') && class_exists("DOMDocument")) || FALSE));
if (!isset($ADODB_OUTP)) $ADODB_OUTP = 'ew_SetDebugMsg';
define("EW_FONT_SIZE", 14);
define("EW_TMP_IMAGE_FONT", "DejaVuSans"); // Font for temp files

// Set up font path
$EW_FONT_PATH = realpath('./phpfont');

// Database connection info
define("EW_USE_ADODB", FALSE); // Use ADOdb
if (!defined("EW_USE_MYSQLI"))
	define('EW_USE_MYSQLI', extension_loaded("mysqli")); // Use MySQLi
$EW_CONN["DB"] = array("conn" => NULL, "id" => "DB", "type" => "MYSQL", "host" => "localhost", "port" => 3306, "user" => "root", "pass" => "Elvis56", "db" => "php_stock", "qs" => "`", "qe" => "`");
$EW_CONN[0] = &$EW_CONN["DB"];

// Set up database error function
$EW_ERROR_FN = 'ew_ErrorFn'; // v11.0.4

// ADODB (Access/SQL Server)
define("EW_CODEPAGE", 65001); // Code page

/**
 * Character encoding
 * Note: If you use non English languages, you need to set character encoding
 * for some features. Make sure either iconv functions or multibyte string
 * functions are enabled and your encoding is supported. See PHP manual for
 * details.
 */
define("EW_ENCODING", "UTF-8"); // Character encoding
define("EW_IS_DOUBLE_BYTE", in_array(EW_ENCODING, array("GBK", "BIG5", "SHIFT_JIS"))); // Double-byte character encoding
define("EW_FILE_SYSTEM_ENCODING", ""); // File system encoding

// Database
define("EW_IS_MSACCESS", FALSE); // Access
define("EW_IS_MSSQL", FALSE); // SQL Server
define("EW_IS_MYSQL", TRUE); // MySQL
define("EW_IS_POSTGRESQL", FALSE); // PostgreSQL
define("EW_IS_ORACLE", FALSE); // Oracle
if (!EW_IS_WINDOWS && (EW_IS_MSACCESS || EW_IS_MSSQL))
	die("Microsoft Access or SQL Server is supported on Windows server only.");
define("EW_DB_QUOTE_START", "`");
define("EW_DB_QUOTE_END", "`");

// since v12 not used! <--> define("EW_SELECT_LIMIT", (EW_IS_MYSQL || EW_IS_POSTGRESQL || EW_IS_ORACLE)); // Modification (20140916) http://www.hkvforums.com/viewtopic.php?f=4&t=35486&p=102440#p102440

/**
 * MySQL charset (for SET NAMES statement, not used by default)
 * Note: Read http://dev.mysql.com/doc/refman/5.0/en/charset-connection.html
 * before using this setting.
 */
define("EW_MYSQL_CHARSET", "utf8");

/**
 * Password (MD5 and case-sensitivity)
 * Note: If you enable MD5 password, make sure that the passwords in your
 * user table are stored as MD5 hash (32-character hexadecimal number) of the
 * clear text password. If you also use case-insensitive password, convert the
 * clear text passwords to lower case first before calculating MD5 hash.
 * Otherwise, existing users will not be able to login. MD5 hash is
 * irreversible, password will be reset during password recovery.
 */
define("EW_ENCRYPTED_PASSWORD", TRUE); // Use encrypted password
define("EW_CASE_SENSITIVE_PASSWORD", TRUE); // Case-sensitive password

/**
 * Remove XSS
 * Note: If you want to allow these keywords, remove them from the following EW_XSS_ARRAY at your own risks.
*/
define("EW_REMOVE_XSS", TRUE);
$EW_XSS_ARRAY = array('javascript', 'vbscript', 'expression', '<applet', '<meta', '<xml', '<blink', '<link', '<style', '<script', '<embed', '<object', '<iframe', '<frame', '<frameset', '<ilayer', '<layer', '<bgsound', '<title', '<base',
'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

// Check Token
define("EW_CHECK_TOKEN", TRUE); // Check post token

// Session timeout time
define("EW_SESSION_TIMEOUT", 3); // Session timeout time (minutes)

// Session keep alive interval
define("EW_SESSION_KEEP_ALIVE_INTERVAL", 180); // Session keep alive interval (seconds)
define("EW_SESSION_TIMEOUT_COUNTDOWN", 60); // Session timeout count down interval (seconds)

// Session names
define("EW_SESSION_STATUS", EW_PROJECT_NAME . "_status"); // Login status
define("EW_SESSION_USER_NAME", EW_SESSION_STATUS . "_UserName"); // User name
define("EW_SESSION_USER_LOGIN_TYPE", EW_SESSION_STATUS . "_UserLoginType"); // User login type
define("EW_SESSION_USER_ID", EW_SESSION_STATUS . "_UserID"); // User ID
define("EW_SESSION_USER_PROFILE", EW_SESSION_STATUS . "_UserProfile"); // User profile
define("EW_SESSION_USER_PROFILE_USER_NAME", EW_SESSION_USER_PROFILE . "_UserName");
define("EW_SESSION_USER_PROFILE_PASSWORD", EW_SESSION_USER_PROFILE . "_Password");
define("EW_SESSION_USER_PROFILE_LOGIN_TYPE", EW_SESSION_USER_PROFILE . "_LoginType");
define("EW_SESSION_USER_LEVEL_ID", EW_SESSION_STATUS . "_UserLevel"); // User Level ID
define("EW_SESSION_USER_LEVEL_LIST", EW_SESSION_STATUS . "_UserLevelList"); // User Level List
define("EW_SESSION_USER_LEVEL_LIST_LOADED", EW_SESSION_STATUS . "_UserLevelListLoaded"); // User Level List Loaded
@define("EW_SESSION_USER_LEVEL", EW_SESSION_STATUS . "_UserLevelValue"); // User Level
define("EW_SESSION_PARENT_USER_ID", EW_SESSION_STATUS . "_ParentUserID"); // Parent User ID
define("EW_SESSION_SYS_ADMIN", EW_PROJECT_NAME . "_SysAdmin"); // System admin
define("EW_SESSION_PROJECT_ID", EW_PROJECT_NAME . "_ProjectID"); // User Level project ID
define("EW_SESSION_AR_USER_LEVEL", EW_PROJECT_NAME . "_arUserLevel"); // User Level array
define("EW_SESSION_AR_USER_LEVEL_PRIV", EW_PROJECT_NAME . "_arUserLevelPriv"); // User Level privilege array
define("EW_SESSION_USER_LEVEL_MSG", EW_PROJECT_NAME . "_UserLevelMessage"); // User Level Message
define("EW_SESSION_SECURITY", EW_PROJECT_NAME . "_Security"); // Security array
define("EW_SESSION_MESSAGE", EW_PROJECT_NAME . "_Message"); // System message
define("EW_SESSION_FAILURE_MESSAGE", EW_PROJECT_NAME . "_Failure_Message"); // System error message
define("EW_SESSION_SUCCESS_MESSAGE", EW_PROJECT_NAME . "_Success_Message"); // System message
define("EW_SESSION_WARNING_MESSAGE", EW_PROJECT_NAME . "_Warning_Message"); // Warning message
define("EW_SESSION_INLINE_MODE", EW_PROJECT_NAME . "_InlineMode"); // Inline mode
define("EW_SESSION_BREADCRUMB", EW_PROJECT_NAME . "_Breadcrumb"); // Breadcrumb
define("EW_SESSION_TEMP_IMAGES", EW_PROJECT_NAME . "_TempImages"); // Temp images

// Language settings
define("EW_LANGUAGE_FOLDER", $EW_RELATIVE_PATH . "phplang/");
$EW_LANGUAGE_FILE = array();
$EW_LANGUAGE_FILE[] = array("en", "", "english_phpstock.xml");
$EW_LANGUAGE_FILE[] = array("id", "", "indonesian_phpstock.xml");
define("EW_LANGUAGE_DEFAULT_ID", "en");
define("EW_SESSION_LANGUAGE_ID", EW_PROJECT_NAME . "_LanguageId"); // Language ID

// Page Token
define("EW_TOKEN_NAME", "token"); // DO NOT CHANGE!
define("EW_SESSION_TOKEN", EW_PROJECT_NAME . "_Token");

// Data types
define("EW_DATATYPE_NUMBER", 1);
define("EW_DATATYPE_DATE", 2);
define("EW_DATATYPE_STRING", 3);
define("EW_DATATYPE_BOOLEAN", 4);
define("EW_DATATYPE_MEMO", 5);
define("EW_DATATYPE_BLOB", 6);
define("EW_DATATYPE_TIME", 7);
define("EW_DATATYPE_GUID", 8);
define("EW_DATATYPE_XML", 9);
define("EW_DATATYPE_OTHER", 10);

// Row types
define("EW_ROWTYPE_HEADER", 0); // Row type header <-- since v11.0.6
define("EW_ROWTYPE_VIEW", 1); // Row type view
define("EW_ROWTYPE_ADD", 2); // Row type add
define("EW_ROWTYPE_EDIT", 3); // Row type edit
define("EW_ROWTYPE_SEARCH", 4); // Row type search
define("EW_ROWTYPE_MASTER", 5); // Row type master record
define("EW_ROWTYPE_AGGREGATEINIT", 6); // Row type aggregate init
define("EW_ROWTYPE_AGGREGATE", 7); // Row type aggregate

// List actions
define("EW_ACTION_POSTBACK", "P"); // Post back
define("EW_ACTION_AJAX", "A"); // Ajax
define("EW_ACTION_MULTIPLE", "M"); // Multiple records
define("EW_ACTION_SINGLE", "S"); // Single record

// Table parameters
define("EW_TABLE_PREFIX", "||PHPReportMaker||");
define("EW_TABLE_REC_PER_PAGE", "recperpage"); // Records per page
define("EW_TABLE_START_REC", "start"); // Start record
define("EW_TABLE_PAGE_NO", "pageno"); // Page number
define("EW_TABLE_BASIC_SEARCH", "psearch"); // Basic search keyword
define("EW_TABLE_BASIC_SEARCH_TYPE","psearchtype"); // Basic search type
define("EW_TABLE_ADVANCED_SEARCH", "advsrch"); // Advanced search
define("EW_TABLE_SEARCH_WHERE", "searchwhere"); // Search where clause
define("EW_TABLE_WHERE", "where"); // Table where
define("EW_TABLE_WHERE_LIST", "where_list"); // Table where (list page)
define("EW_TABLE_ORDER_BY", "orderby"); // Table order by
define("EW_TABLE_ORDER_BY_LIST", "orderby_list"); // Table order by (list page)
define("EW_TABLE_SORT", "sort"); // Table sort
define("EW_TABLE_KEY", "key"); // Table key
define("EW_TABLE_SHOW_MASTER", "showmaster"); // Table show master
define("EW_TABLE_SHOW_DETAIL", "showdetail"); // Table show detail
define("EW_TABLE_MASTER_TABLE", "mastertable"); // Master table
define("EW_TABLE_DETAIL_TABLE", "detailtable"); // Detail table
define("EW_TABLE_RETURN_URL", "return"); // Return URL
define("EW_TABLE_EXPORT_RETURN_URL", "exportreturn"); // Export return URL
define("EW_TABLE_GRID_ADD_ROW_COUNT", "gridaddcnt"); // Grid add row count

// Audit Trail
define("EW_AUDIT_TRAIL_TO_DATABASE", FALSE); // Write audit trail to DB
define("EW_AUDIT_TRAIL_DBID", "DB"); // Audit trail DBID
define("EW_AUDIT_TRAIL_TABLE_NAME", ""); // Audit trail table name
define("EW_AUDIT_TRAIL_TABLE_VAR", ""); // Audit trail table var
define("EW_AUDIT_TRAIL_FIELD_NAME_DATETIME", ""); // Audit trail DateTime field name
define("EW_AUDIT_TRAIL_FIELD_NAME_SCRIPT", ""); // Audit trail Script field name
define("EW_AUDIT_TRAIL_FIELD_NAME_USER", ""); // Audit trail User field name
define("EW_AUDIT_TRAIL_FIELD_NAME_ACTION", ""); // Audit trail Action field name
define("EW_AUDIT_TRAIL_FIELD_NAME_TABLE", ""); // Audit trail Table field name
define("EW_AUDIT_TRAIL_FIELD_NAME_FIELD", ""); // Audit trail Field field name
define("EW_AUDIT_TRAIL_FIELD_NAME_KEYVALUE", ""); // Audit trail Key Value field name
define("EW_AUDIT_TRAIL_FIELD_NAME_OLDVALUE", ""); // Audit trail Old Value field name
define("EW_AUDIT_TRAIL_FIELD_NAME_NEWVALUE", ""); // Audit trail New Value field name

// Security
define("EW_ADMIN_USER_NAME", "admin"); // Administrator user name
define("EW_ADMIN_PASSWORD", "master"); // Administrator password
define("EW_USE_CUSTOM_LOGIN", TRUE); // Use custom login
define("EW_ALLOW_LOGIN_BY_URL", FALSE); // Allow login by URL
define("EW_ALLOW_LOGIN_BY_SESSION", FALSE); // Allow login by session variables
define("EW_PHPASS_ITERATION_COUNT_LOG2", "[10,8]"); // Note: Use JSON array syntax

// Dynamic User Level settings
// User level definition table/field names

@define("EW_USER_LEVEL_DBID", "DB");
@define("EW_USER_LEVEL_TABLE", "`userlevels`");
@define("EW_USER_LEVEL_ID_FIELD", "`User_Level_ID`");
@define("EW_USER_LEVEL_NAME_FIELD", "`User_Level_Name`");

// User Level privileges table/field names
@define("EW_USER_LEVEL_PRIV_DBID", "DB");
@define("EW_USER_LEVEL_PRIV_TABLE", "`userlevelpermissions`");
@define("EW_USER_LEVEL_PRIV_TABLE_NAME_FIELD", "`Table_Name`");
@define("EW_USER_LEVEL_PRIV_TABLE_NAME_FIELD_2", "Table_Name");
@define("EW_USER_LEVEL_PRIV_TABLE_NAME_FIELD_SIZE", 255);
@define("EW_USER_LEVEL_PRIV_USER_LEVEL_ID_FIELD", "`User_Level_ID`");
@define("EW_USER_LEVEL_PRIV_PRIV_FIELD", "`Permission`");

// User level constants
define("EW_ALLOW_ADD", 1); // Add
define("EW_ALLOW_DELETE", 2); // Delete
define("EW_ALLOW_EDIT", 4); // Edit
@define("EW_ALLOW_LIST", 8); // List
if (defined("EW_USER_LEVEL_COMPAT")) {
	define("EW_ALLOW_VIEW", 8); // View
	define("EW_ALLOW_SEARCH", 8); // Search
} else {
	define("EW_ALLOW_VIEW", 32); // View
	define("EW_ALLOW_SEARCH", 64); // Search
}
@define("EW_ALLOW_REPORT", 8); // Report
@define("EW_ALLOW_ADMIN", 16); // Admin

// Hierarchical User ID
@define("EW_USER_ID_IS_HIERARCHICAL", TRUE); // Change to FALSE to show one level only

// Use subquery for master/detail
define("EW_USE_SUBQUERY_FOR_MASTER_USER_ID", FALSE);
define("EW_USER_ID_ALLOW", 104);

// User table filters
define("EW_USER_TABLE_DBID", "DB");
define("EW_USER_TABLE", "`users`");
define("EW_USER_NAME_FILTER", "(`Username` = '%u')");
define("EW_USER_ID_FILTER", "");
define("EW_USER_EMAIL_FILTER", "(`Email` = '%e')");
define("EW_USER_ACTIVATE_FILTER", "(`Activated` = 'Y')");
define("EW_USER_PROFILE_FIELD_NAME", "Profile");

// User Profile Constants
define("EW_USER_PROFILE_KEY_SEPARATOR", "");
define("EW_USER_PROFILE_FIELD_SEPARATOR", "");
define("EW_USER_PROFILE_SESSION_ID", "SessionID");
define("EW_USER_PROFILE_LAST_ACCESSED_DATE_TIME", "LastAccessedDateTime");
define("EW_USER_PROFILE_CONCURRENT_SESSION_COUNT", 1); // Maximum sessions allowed
define("EW_USER_PROFILE_SESSION_TIMEOUT", 20);
define("EW_USER_PROFILE_LOGIN_RETRY_COUNT", "LoginRetryCount");
define("EW_USER_PROFILE_LAST_BAD_LOGIN_DATE_TIME", "LastBadLoginDateTime");
define("EW_USER_PROFILE_MAX_RETRY", 3);
define("EW_USER_PROFILE_RETRY_LOCKOUT", 20);
define("EW_USER_PROFILE_LAST_PASSWORD_CHANGED_DATE", "LastPasswordChangedDate");
define("EW_USER_PROFILE_PASSWORD_EXPIRE", 90);

// Email
define("EW_SMTP_SERVER", "localhost"); // SMTP server
define("EW_SMTP_SERVER_PORT", 25); // SMTP server port
define("EW_SMTP_SECURE_OPTION", "");
define("EW_SMTP_SERVER_USERNAME", ""); // SMTP server user name
define("EW_SMTP_SERVER_PASSWORD", ""); // SMTP server password
define("EW_SENDER_EMAIL", "masino.sinaga@gmail.com"); // Sender email address
define("EW_RECIPIENT_EMAIL", "masino.sinaga@gmail.com"); // Recipient email address
define("EW_MAX_EMAIL_RECIPIENT", 3);
define("EW_MAX_EMAIL_SENT_COUNT", 3);
define("EW_EXPORT_EMAIL_COUNTER", EW_SESSION_STATUS . "_EmailCounter");
define("EW_EMAIL_CHANGEPWD_TEMPLATE", "changepwd.html");
define("EW_EMAIL_FORGOTPWD_TEMPLATE", "forgotpwd.html");
define("EW_EMAIL_NOTIFY_TEMPLATE", "notify.html");
define("EW_EMAIL_REGISTER_TEMPLATE", "register.html");
define("EW_EMAIL_RESETPWD_TEMPLATE", "resetpwd.html");
define("EW_EMAIL_TEMPLATE_PATH", "phphtml"); // Template path

// File upload
define("EW_UPLOAD_TEMP_PATH", ""); // Upload temp path (absolute)
define("EW_UPLOAD_DEST_PATH", ""); // Upload destination path (relative to app root)
define("EW_UPLOAD_URL", "ewupload12.php"); // Upload URL
define("EW_UPLOAD_TEMP_FOLDER_PREFIX", "temp__"); // Upload temp folders prefix
define("EW_UPLOAD_TEMP_FOLDER_TIME_LIMIT", 1440); // Upload temp folder time limit (minutes)
define("EW_UPLOAD_THUMBNAIL_FOLDER", "thumbnail"); // Temporary thumbnail folder
define("EW_UPLOAD_THUMBNAIL_WIDTH", 200); // Temporary thumbnail max width
define("EW_UPLOAD_THUMBNAIL_HEIGHT", 0); // Temporary thumbnail max height
define("EW_UPLOAD_ALLOWED_FILE_EXT", "gif,jpg,jpeg,bmp,png,doc,xls,pdf,zip"); // Allowed file extensions
define("EW_IMAGE_ALLOWED_FILE_EXT", "gif,jpg,png,bmp"); // Allowed file extensions for images
define("EW_DOWNLOAD_ALLOWED_FILE_EXT", "pdf,xls,doc,xlsx,docx"); // Allowed file extensions for download (non-image)
define("EW_ENCRYPT_FILE_PATH", TRUE); // Encrypt file path
define("EW_MAX_FILE_SIZE", 2000000); // Max file size
define("EW_MAX_FILE_COUNT", 0); // Max file count
define("EW_THUMBNAIL_DEFAULT_WIDTH", 0); // Thumbnail default width
define("EW_THUMBNAIL_DEFAULT_HEIGHT", 0); // Thumbnail default height
define("EW_THUMBNAIL_DEFAULT_QUALITY", 100); // Thumbnail default qualtity (JPEG)
define("EW_UPLOADED_FILE_MODE", 0666); // Uploaded file mode
define("EW_UPLOAD_TMP_PATH", ""); // User upload temp path (relative to app root) e.g. "tmp/"
define("EW_UPLOAD_CONVERT_ACCENTED_CHARS", FALSE); // Convert accented chars in upload file name
define("EW_USE_COLORBOX", TRUE); // Use Colorbox
define("EW_MULTIPLE_UPLOAD_SEPARATOR", ","); // Multiple upload separator

// Image resize
$EW_THUMBNAIL_CLASS = "cThumbnail";
define("EW_REDUCE_IMAGE_ONLY", TRUE);
define("EW_KEEP_ASPECT_RATIO", FALSE);
$EW_RESIZE_OPTIONS = array("keepAspectRatio" => EW_KEEP_ASPECT_RATIO, "resizeUp" => !EW_REDUCE_IMAGE_ONLY, "jpegQuality" => EW_THUMBNAIL_DEFAULT_QUALITY);

// Audit trail
define("EW_AUDIT_TRAIL_PATH", ""); // Audit trail path (relative to app root)

// Export records
define("EW_EXPORT_ALL", TRUE); // Export all records
define("EW_EXPORT_ALL_TIME_LIMIT", 120); // Export all records time limit
define("EW_XML_ENCODING", "utf-8"); // Encoding for Export to XML
define("EW_EXPORT_ORIGINAL_VALUE", FALSE);
define("EW_EXPORT_FIELD_CAPTION", FALSE); // TRUE to export field caption
define("EW_EXPORT_CSS_STYLES", TRUE); // TRUE to export CSS styles
define("EW_EXPORT_MASTER_RECORD", TRUE); // TRUE to export master record
define("EW_EXPORT_MASTER_RECORD_FOR_CSV", FALSE); // TRUE to export master record for CSV
define("EW_EXPORT_DETAIL_RECORDS", TRUE); // TRUE to export detail records
define("EW_EXPORT_DETAIL_RECORDS_FOR_CSV", FALSE); // TRUE to export detail records for CSV

// Begin of modification Printer Friendly always does not use stylesheet, by Masino Sinaga, October 8, 2013
$EW_EXPORT = array(
	"email" => "cExportEmail",
	"html" => "cExportHtml",
	"print" => "cExportPrint", // this is new/added !
	"word" => "cExportWord",
	"excel" => "cExportExcel",
	"pdf" => "cExportPdf",
	"csv" => "cExportCsv",
	"xml" => "cExportXml"
);

// End of modification Printer Friendly always does not use stylesheet, by Masino Sinaga, October 8, 2013
// Export records for reports

$EW_EXPORT_REPORT = array(
	"print" => "ExportReportHtml",
	"html" => "ExportReportHtml",
	"word" => "ExportReportWord",
	"excel" => "ExportReportExcel"
);

// MIME types
$EW_MIME_TYPES = array(
	"pdf"	=>	"application/pdf",
	"exe"	=>	"application/octet-stream",
	"zip"	=>	"application/zip",
	"doc"	=>	"application/msword",
	"docx"	=>	"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
	"xls"	=>	"application/vnd.ms-excel",
	"xlsx"	=>	"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
	"ppt"	=>	"application/vnd.ms-powerpoint",
	"pptx"	=>	"application/vnd.openxmlformats-officedocument.presentationml.presentation",
	"gif"	=>	"image/gif",
	"png"	=>	"image/png",
	"jpeg"	=>	"image/jpeg",
	"jpg"	=>	"image/jpeg",
	"mp3"	=>	"audio/mpeg",
	"wav"	=>	"audio/x-wav",
	"mpeg"	=>	"video/mpeg",
	"mpg"	=>	"video/mpeg",
	"mpe"	=>	"video/mpeg",
	"mov"	=>	"video/quicktime",
	"avi"	=>	"video/x-msvideo",
	"3gp"	=>	"video/3gpp",
	"css"	=>	"text/css",
	"js"	=>	"application/javascript",
	"htm"	=>	"text/html",
	"html"	=>	"text/html"
);

// Boolean html attributes
$EW_BOOLEAN_HTML_ATTRIBUTES = array("checked", "compact", "declare", "defer", "disabled", "ismap", "multiple", "nohref", "noresize", "noshade", "nowrap", "readonly", "selected");

// Use token in URL (reserved, not used, do NOT change!)
define("EW_USE_TOKEN_IN_URL", FALSE);

// Use ILIKE for PostgreSql
define("EW_USE_ILIKE_FOR_POSTGRESQL", TRUE);

// Use collation for MySQL
define("EW_LIKE_COLLATION_FOR_MYSQL", "");

// Use collation for MsSQL
define("EW_LIKE_COLLATION_FOR_MSSQL", "");

// Null / Not Null values
define("EW_NULL_VALUE", "##null##");
define("EW_NOT_NULL_VALUE", "##notnull##");

/**
 * Search multi value option
 * 1 - no multi value
 * 2 - AND all multi values
 * 3 - OR all multi values
*/
define("EW_SEARCH_MULTI_VALUE_OPTION", 3);

// Quick search
define("EW_BASIC_SEARCH_IGNORE_PATTERN", "/[\?,\.\^\*\(\)\[\]\\\"]/"); // Ignore special characters
define("EW_BASIC_SEARCH_ANY_FIELDS", FALSE); // Search "All keywords" in any selected fields

// Validate option
define("EW_CLIENT_VALIDATE", TRUE);
define("EW_SERVER_VALIDATE", TRUE);

// Blob field byte count for hash value calculation
define("EW_BLOB_FIELD_BYTE_COUNT", 200);

// Auto suggest max entries
define("EW_AUTO_SUGGEST_MAX_ENTRIES", 10);

// Auto fill original value
define("EW_AUTO_FILL_ORIGINAL_VALUE", false);

// Checkbox and radio button groups
define("EW_ITEM_TEMPLATE_CLASSNAME", "ewTemplate");
define("EW_ITEM_TABLE_CLASSNAME", "ewItemTable");

// Use responsive layout
$EW_USE_RESPONSIVE_LAYOUT = TRUE;

// Use css flip
define("EW_CSS_FLIP", FALSE);

// Time zone
$DEFAULT_TIME_ZONE = "GMT";

/**
 * Numeric and monetary formatting options
 * Note: DO NOT CHANGE THE FOLLOWING $DEFAULT_* VARIABLES!
 * If you want to use custom settings, customize the language file,
 * set "use_system_locale" to "0" to override localeconv and customize the
 * phrases under the <locale> node for ew_FormatCurrency/Number/Percent functions
 * Also read http://www.php.net/localeconv for description of the constants
*/
$DEFAULT_LOCALE = json_decode('{"decimal_point":".","thousands_sep":"","int_curr_symbol":"$","currency_symbol":"$","mon_decimal_point":".","mon_thousands_sep":"","positive_sign":"","negative_sign":"-","int_frac_digits":2,"frac_digits":2,"p_cs_precedes":1,"p_sep_by_space":0,"n_cs_precedes":1,"n_sep_by_space":0,"p_sign_posn":1,"n_sign_posn":1}'); 
//$DEFAULT_DECIMAL_POINT = &$DEFAULT_LOCALE["decimal_point"];
// 
// $DEFAULT_THOUSANDS_SEP = &$DEFAULT_LOCALE["thousands_sep"];
// $DEFAULT_CURRENCY_SYMBOL = &$DEFAULT_LOCALE["currency_symbol"];
// $DEFAULT_MON_DECIMAL_POINT = &$DEFAULT_LOCALE["mon_decimal_point"];
// $DEFAULT_MON_THOUSANDS_SEP = &$DEFAULT_LOCALE["mon_thousands_sep"];
// $DEFAULT_POSITIVE_SIGN = &$DEFAULT_LOCALE["positive_sign"];
// $DEFAULT_NEGATIVE_SIGN = &$DEFAULT_LOCALE["negative_sign"];
// $DEFAULT_FRAC_DIGITS = &$DEFAULT_LOCALE["frac_digits"];
// $DEFAULT_P_CS_PRECEDES = &$DEFAULT_LOCALE["p_cs_precedes"];
// $DEFAULT_P_SEP_BY_SPACE = &$DEFAULT_LOCALE["p_sep_by_space"];
// $DEFAULT_N_CS_PRECEDES = &$DEFAULT_LOCALE["n_cs_precedes"];
// $DEFAULT_N_SEP_BY_SPACE = &$DEFAULT_LOCALE["n_sep_by_space"];
// $DEFAULT_P_SIGN_POSN = &$DEFAULT_LOCALE["p_sign_posn"];
// $DEFAULT_N_SIGN_POSN = &$DEFAULT_LOCALE["n_sign_posn"];
$DEFAULT_DECIMAL_POINT = &$DEFAULT_LOCALE->decimal_point;
$DEFAULT_DECIMAL_POINT = &$DEFAULT_LOCALE->decimal_point;
$DEFAULT_THOUSANDS_SEP = &$DEFAULT_LOCALE->thousands_sep;
$DEFAULT_CURRENCY_SYMBOL = &$DEFAULT_LOCALE->currency_symbol;
$DEFAULT_MON_DECIMAL_POINT = &$DEFAULT_LOCALE->mon_decimal_point;
$DEFAULT_MON_THOUSANDS_SEP = &$DEFAULT_LOCALE->mon_thousands_sep;
$DEFAULT_POSITIVE_SIGN = &$DEFAULT_LOCALE->positive_sign;
$DEFAULT_NEGATIVE_SIGN = &$DEFAULT_LOCALE->negative_sign;
$DEFAULT_FRAC_DIGITS = &$DEFAULT_LOCALE->frac_digits;
$DEFAULT_P_CS_PRECEDES = &$DEFAULT_LOCALE->p_cs_precedes;
$DEFAULT_P_SEP_BY_SPACE = &$DEFAULT_LOCALE->p_sep_by_space;
$DEFAULT_N_CS_PRECEDES = &$DEFAULT_LOCALE->n_cs_precedes;
$DEFAULT_N_SEP_BY_SPACE = &$DEFAULT_LOCALE->n_sep_by_space;
$DEFAULT_P_SIGN_POSN = &$DEFAULT_LOCALE->p_sign_posn;
$DEFAULT_N_SIGN_POSN = &$DEFAULT_LOCALE->n_sign_posn;
// Cookies
define("EW_COOKIE_EXPIRY_TIME", time() + 365*24*60*60); // Change cookie expiry time here

// Client variables
$EW_CLIENT_VAR = array();

//
// Global variables
//

if (!isset($conn)) {

	// Common objects
	$conn = NULL; // Connection
	$Page = NULL; // Page
	$UserTable = NULL; // User table
	$UserTableConn = NULL; // User table connection
	$Table = NULL; // Main table
	$Grid = NULL; // Grid page object
	$Language = NULL; // Language
	$Security = NULL; // Security
	$UserProfile = NULL; // User profile
	$objForm = NULL; // Form

	// Current language
	$gsLanguage = "";

	// Token
	$gsToken = "";

	// Used by ValidateForm/ValidateSearch
	$gsFormError = ""; // Form error message
	$gsSearchError = ""; // Search form error message

	// Used by *master.php
	$gsMasterReturnUrl = "";

	// Used by header.php, export checking
	$gsExport = "";
	$gsExportFile = "";
	$gsCustomExport = "";

	// Used by header.php/footer.php, skip header/footer checking
	$gbSkipHeaderFooter = FALSE;
	$gbOldSkipHeaderFooter = $gbSkipHeaderFooter;

	// Email error message
	$gsEmailErrDesc = "";

	// Debug message
	$gsDebugMsg = "";

	// Debug timer
	$gTimer = NULL;

	// Keep temp images name for PDF export for delete
	$gTmpImages = array();
}

// Mobile detect
$MobileDetect = NULL;

// Breadcrumb
$Breadcrumb = NULL;
?>
<?php
define("EW_ROWTYPE_PREVIEW", 11); // Preview record
?>
<?php
define("EW_CAPTCHA_FONT", "monofont");
?>
<?php
define("EW_USE_PHPWORD", TRUE);
?>
<?php
define("EW_USE_PHPEXCEL", TRUE);
?>
<?php

// Menu
// Begin of modification Supports for Horizontal and Vertical Menu, by Masino Sinaga, April 30, 2011

define("MS_MENU_HORIZONTAL", TRUE); // Default value is , set the second parameter to TRUE if you want to use horizontal (default), or FALSE if you want vertical menu.

// End of modification Supports for Horizontal and Vertical Menu, by Masino Sinaga, April 30, 2011
?>
<?php 
if (@MS_MENU_HORIZONTAL) {

	// Menu Horizontal
	define("EW_MENUBAR_ID", "ewHorizMenu");
	define("EW_MENUBAR_BRAND", "");
	define("EW_MENUBAR_BRAND_HYPERLINK", "");
	define("EW_MENUBAR_CLASSNAME", "navbar navbar-default");
	define("EW_MENUBAR_INNER_CLASSNAME", "");
	define("EW_MENU_CLASSNAME", "nav navbar-nav");
	define("EW_SUBMENU_CLASSNAME", "dropdown-menu");
	define("EW_SUBMENU_DROPDOWN_IMAGE", " <b class=\"caret\"></b>");
	define("EW_SUBMENU_DROPDOWN_ICON_CLASSNAME", "");

	//define("EW_MENU_DIVIDER_CLASSNAME", "divider-vertical");
	define("EW_MENU_DIVIDER_CLASSNAME", "divider");
	define("EW_MENU_ITEM_CLASSNAME", "dropdown");
	define("EW_SUBMENU_ITEM_CLASSNAME", "dropdown-submenu");
	define("EW_MENU_ACTIVE_ITEM_CLASS", "active");
	define("EW_SUBMENU_ACTIVE_ITEM_CLASS", "active");
	define("EW_MENU_ROOT_GROUP_TITLE_AS_SUBMENU", TRUE);
	define("EW_SHOW_RIGHT_MENU", TRUE);
} else {

	// Menu Vertical
	define("EW_MENUBAR_ID", "RootMenu");
	define("EW_MENUBAR_BRAND", "");
	define("EW_MENUBAR_BRAND_HYPERLINK", "");
	define("EW_MENUBAR_CLASSNAME", "");

	//define("EW_MENU_CLASSNAME", "nav nav-list");
	define("EW_MENU_CLASSNAME", "dropdown-menu");
	define("EW_SUBMENU_CLASSNAME", "dropdown-menu");
	define("EW_SUBMENU_DROPDOWN_IMAGE", "");
	define("EW_SUBMENU_DROPDOWN_ICON_CLASSNAME", "");
	define("EW_MENU_DIVIDER_CLASSNAME", "divider");
	define("EW_MENU_ITEM_CLASSNAME", "dropdown-submenu");
	define("EW_SUBMENU_ITEM_CLASSNAME", "dropdown-submenu");
	define("EW_MENU_ACTIVE_ITEM_CLASS", "active");
	define("EW_SUBMENU_ACTIVE_ITEM_CLASS", "active");
	define("EW_MENU_ROOT_GROUP_TITLE_AS_SUBMENU", FALSE);
	define("EW_SHOW_RIGHT_MENU", FALSE);}
?>
<?php
define("EW_PDF_STYLESHEET_FILENAME", "phpcss/ewpdf.css"); // export PDF CSS styles
define("EW_PDF_MEMORY_LIMIT", "128M"); // Memory limit
define("EW_PDF_TIME_LIMIT", 120); // Time limit
?>
<?php
define("MS_ENABLE_VISITOR_STATS", FALSE);
define("MS_STATS_COUNTER_TABLE", "stats_counter");
define("MS_STATS_COUNTERLOG_TABLE", "stats_counterlog");
define("MS_STATS_HOUR_TABLE", "stats_hour");
define("MS_STATS_DATE_TABLE", "stats_date");
define("MS_STATS_MONTH_TABLE", "stats_month");
define("MS_STATS_YEAR_TABLE", "stats_year");
?>
<?php
define("MS_USE_PHPMAKER_SETTING_FOR_INITIATE_SEARCH_PANEL", TRUE); 
define("MS_USE_TABLE_SETTING_FOR_SEARCH_PANEL_STATUS", TRUE); 
?>
<?php

// Begin of modification Always Compare Root URL, by Masino Sinaga, October 18, 2015
define("ALWAYS_COMPARE_ROOT_URL", FALSE);

// End of modification Always Compare Root URL, by Masino Sinaga, October 18, 2015
?>
<?php 

// Begin of modification Use Alertify for Message Dialog, by Masino Sinaga, October 15, 2014
define("MS_USE_ALERTIFY_FOR_MESSAGE_DIALOG", TRUE);

// End of modification Use Alertify for Message Dialog, by Masino Sinaga, October 15, 2014
?>
<?php

// Begin of modification Enter for Moving Cursor to Next Field, by Masino Sinaga, October 10, 2014
define("MS_ENTER_MOVING_CURSOR_TO_NEXT_FIELD", TRUE);

// End of modification Enter for Moving Cursor to Next Field, by Masino Sinaga, October 10, 2014
?>
<?php

// Begin of modification Enable Help Online, by Masino Sinaga, September 19, 2014
define("MS_SHOW_HELP_ONLINE", TRUE); 

// End of modification Enable Help Online, by Masino Sinaga, September 19, 2014
?>
<?php

// Begin of modification Add Cancel Button next to Action Button, by Masino Sinaga, August 4, 2014
define("MS_ADD_CANCEL_BUTTON_NEXT_TO_ACTION_BUTTON", TRUE); 

// End of modification Add Cancel Button next to Action Button, by Masino Sinaga, August 4, 2014
?>
<?php

// Begin of modification Demo Mode and Allow User Preferences, by Masino Sinaga, July 29, 2014
define("MS_DEMO_MODE", FALSE);
define("MS_ALLOW_USER_PREFERENCES", FALSE);

// End of modification Demo Mode and Allow User Preferences, by Masino Sinaga, July 29, 2014
?>
<?php

// Begin of modification by Masino Sinaga, for saving the registered, last login, and last logout date time, November 6, 2011
define("MS_USER_PROFILE_REGISTERED_DATE_TIME", "RegisteredDateTime");
define("MS_USER_PROFILE_LAST_LOGIN_DATE_TIME", "LastLoginDateTime");
define("MS_USER_PROFILE_LAST_LOGOUT_DATE_TIME", "LastLogoutDateTime");

// End of modification by Masino Sinaga, for saving the registered, last login, and last logout date time, November 6, 2011
?>
<?php

// Begin of modification Show Detail Count Greater Than Zero and Badge for Detail Count, by Masino Sinaga, May 17, 2014
define("MS_SHOW_DETAILCOUNT_GREATER_THAN_ZERO_ONLY", TRUE);
define("MS_USE_BADGE_FOR_DETAILCOUNT", TRUE);

// End of modification Show Detail Count Greater Than Zero and Badge for Detail Count, by Masino Sinaga, May 17, 2014
?>
<?php

// Begin of modification Displaying Breadcrumb Links, by Masino Sinaga, October 5, 2013
define("MS_SHOW_PHPMAKER_BREADCRUMBLINKS", TRUE);
define("MS_SHOW_MASINO_BREADCRUMBLINKS", FALSE);
define("MS_MASINO_BREADCRUMBLINKS_TABLE", "breadcrumblinks");
define("MS_BREADCRUMBLINKS_DIVIDER", "/");

// End of modification Displaying Breadcrumb Links, by Masino Sinaga, October 5, 2013
?>
<?php

// Begin of modification Add Announcement Feature, by Masino Sinaga, February 4, 2014
define("MS_SEPARATED_ANNOUNCEMENT", FALSE); // flag to separate announcement
define("MS_ANNOUNCEMENT_TABLE", "announcement"); // Announcement table name, adjust with yours!

// End of modification Add Announcement Feature, by Masino Sinaga, February 4, 2014
?>
<?php

// Begin of modification Add Help Feature, by Masino Sinaga, June 6, 2012
define("MS_HELP_TABLE", "help"); // Help table name, adjust with yours!
define("MS_HELP_CATEGORIES_TABLE", "help_categories"); // Help Categories table name, adjust with yours!

// End of modification Add Help Feature, by Masino Sinaga, June 6, 2012
?>
<?php

// Begin of modification Languages table, by Masino Sinaga, June 6, 2012
define("MS_LANGUAGES_TABLE", "languages");

// End of modification Languages table, by Masino Sinaga, June 6, 2012	
// Begin of modification Application Settings Feature, by Masino Sinaga, July 3, 2012

define("MS_SETTINGS_TABLE", "settings");

// End of modification Application Settings Feature, by Masino Sinaga, July 3, 2012
// Begin of modification Themes Feature, by Masino Sinaga, July 10, 2012

define("MS_THEMES_TABLE", "themes");

// End of modification Timezone Feature, by Masino Sinaga, July 10, 2012
// Begin of modification Timezone Feature, by Masino Sinaga, July 3, 2012

define("MS_TIMEZONE_TABLE", "timezone");

// End of modification Timezone Feature, by Masino Sinaga, July 3, 2012
// Begin of modification Breadcrumb Links SP, October 29, 2013

define("MS_BREADCRUMB_LINKS_ADD_SP", "addnewbreadcrumb");
define("MS_BREADCRUMB_LINKS_CHECK_SP", "getbreadcrumblinks");
define("MS_BREADCRUMB_LINKS_MOVE_SP", "movebreadcrumb");
define("MS_BREADCRUMB_LINKS_DELETE_SP", "deletebreadcrumbbasedonpagetitle");

// End of modification Breadcrumb Links SP, October 29, 2013
?>
<?php

// Begin of modification Auto Logout After Idle for the Certain Time, by Masino Sinaga, May 5, 2012
define("MS_AUTO_LOGOUT_AFTER_IDLE_IN_MINUTES", 3); // get from project setting since v12 = 3 minutes!

// End of modification Auto Logout After Idle for the Certain Time, by Masino Sinaga, May 5, 2012
?>
<?php

// Begin of modification Customizing Search Panel, by Masino Sinaga, for customize search panel, May 1, 2012
define("MS_SEARCH_PANEL_COLLAPSED", FALSE); // Whether to collaps or expand the search panel, get the value from PHPMaker setting

// End of modification Customizing Search Panel, by Masino Sinaga, for customize search panel, May 1, 2012
?>
<?php
define("MS_USE_TABLE_SETTING_FOR_SEARCH_PANEL_COLLAPSED", TRUE);
define("MS_USE_TABLE_SETTING_FOR_EXPORT_FIELD_CAPTION", FALSE);
define("MS_USE_TABLE_SETTING_FOR_EXPORT_ORIGINAL_VALUE", FALSE);
?>
<?php

// Begin of modification Add Record Number Column on Exported List, modified by Masino Sinaga, June 3, 2012
define("MS_SHOW_RECNUM_COLUMN_ON_EXPORTED_LIST", TRUE);  // whether to show record number column on the exported list 

// End of modification Add Record Number Column on Exported List, modified by Masino Sinaga, June 3, 2012
?>
<?php

// Begin of modification Disable Add/Edit Success Message Box, by Masino Sinaga, August 1, 2012
define("MS_SHOW_ADD_SUCCESS_MESSAGE", TRUE);
define("MS_SHOW_EDIT_SUCCESS_MESSAGE", TRUE);

// End of modification Disable Add/Edit Success Message Box, by Masino Sinaga, August 1, 2012
?>
<?php

// Begin of modification Use Javascript Message, by Masino Sinaga, May 15, 2014
define("MS_USE_JAVASCRIPT_MESSAGE", 1); // available: 1 for TRUE, and 0 for FALSE (for Javascript compatibility reason).

// Begin of modification Use Javascript Message, by Masino Sinaga, May 15, 2014
// Begin of modification Auto Hide Message after 3 Sec, by Masino Sinaga, June 9, 2013

define("MS_AUTO_HIDE_SUCCESS_MESSAGE", FALSE); // only works if MS_AUTO_HIDE_SUCCESS_MESSAGE = TRUE

// End of modification Auto Hide Message after 3 Sec, by Masino Sinaga, June 9, 2013
?>
<?php 

// Begin of modification Border Layout, Shadow Layout, and Empty Table setting, by Masino Sinaga, November 11, 2013
define("MS_SHOW_BORDER_LAYOUT", FALSE); // Whether to show border layout
define("MS_SHOW_SHADOW_LAYOUT", FALSE); // Whether to show shadow layout
define("MS_SHOW_EMPTY_TABLE_ON_LIST_PAGE", TRUE); // Whether to show empty table in the List page if no records found
define("MS_ROWS_VERTICAL_ALIGN_TOP", TRUE); // Vertical align rows in List page

// End of modification Border Layout, Shadow Layout, and Empty Table setting, by Masino Sinaga, November 11, 2013
?>
<?php

// Begin of modification Customize Navigation/Pager Panel, by Masino Sinaga, May 2, 2012
define("MS_PAGINATION_STYLE", 2); // Whether to use drop down selection (2) or numeric link (1) for pagination style
define("MS_PAGINATION_POSITION", 3); // 1 = Top, 2 = Bottom, 3 = Top and Bottom
define("MS_SELECTABLE_PAGE_SIZES_POSITION", "Left"); // "Left" or "Right"
define("MS_TABLE_SELECTABLE_REC_PER_PAGE_LIST", "1,3,5,10,20,50,100"); // Selectable records per page list, derived from PHPMaker -> PHP -> List/View Page Options (Global) -> Selectable page sizes
define("MS_TABLE_MAXIMUM_SELECTED_RECORDS", 20); // Maximum selected records per page
define("MS_TABLE_RECPERPAGE_VALUE", 20); // Default records per page value
define("MS_SHOW_PAGENUM_IF_REC_NOT_OVER_PAGESIZE", FALSE); // Whether to show or hide the pagenumber if records not over pagesize. Set the second parameter to FALSE if you want to hide the pagenumber, otherwise set to TRUE in order to always show the pagenumber.

// End of modification Customize Navigation/Pager Panel, by Masino Sinaga, May 2, 2012
?>
<?php

// Begin of modification Fixed Width Site, by Masino Sinaga, April 28, 2012
define("MS_TABLE_WIDTH_STYLE", "3");  // 1 = Scroll, 2 = Normal, 3 = 100%
define("MS_SCROLL_TABLE_HEIGHT", 350); // The height size of the scrolling table
define("MS_SCROLL_TABLE_WIDTH", 990);  // The width size of the scrolling table
define("MS_VERTICAL_MENU_WIDTH", 160); // The width of vertical menu
if (@MS_MENU_HORIZONTAL) {
  define("MS_TOTAL_WIDTH", MS_SCROLL_TABLE_WIDTH + 40);
} else {
  define("MS_TOTAL_WIDTH", MS_SCROLL_TABLE_WIDTH + MS_VERTICAL_MENU_WIDTH - 50);
}

// End of modification Fixed Width Site, by Masino Sinaga, April 28, 2012
?>
<?php
define("MS_BOOTSTRAP_LEFT_COLUMN_CLASS", "col-sm-4"); // available: col-sm-2,col-sm-3,col-sm-4  (default: col-sm-2)
define("MS_BOOTSTRAP_RIGHT_COLUMN_CLASS", "col-sm-8"); // available: col-sm-10,col-sm-9,col-sm-8  (default: col-sm-10)
define("MS_BOOTSTRAP_OFFSET_LEFT_COLUMN_CLASS", "col-sm-offset-4"); // available: col-sm-offset-2, col-sm-offset-3, col-sm-offset-4  (default: col-sm-offset-2)
define("MS_BOOTSTRAP_OFFSET_RIGHT_COLUMN_CLASS", "col-sm-8"); // available: col-sm-10,col-sm-9,col-sm-8  (default: col-sm-10)
?>
<?php

// Begin of modification Permission Access for Export To Feature, by Masino Sinaga, May 5, 2012
define("MS_ALLOW_EXPORT_TO_PRINT", 128); // Printer Friendly
define("MS_ALLOW_EXPORT_TO_EXCEL", 256); // Export to Excel
define("MS_ALLOW_EXPORT_TO_WORD", 512); // Export to Word
define("MS_ALLOW_EXPORT_TO_HTML", 1024); // Export to HTML
define("MS_ALLOW_EXPORT_TO_XML", 2048); // Export to XML
define("MS_ALLOW_EXPORT_TO_CSV", 4096); // Export to CSV
define("MS_ALLOW_EXPORT_TO_PDF", 8192); // Export to PDF
define("MS_ALLOW_EXPORT_TO_EMAIL", 16384); // Export to Email

// End of modification Permission Access for Export To Feature, by Masino Sinaga, May 5, 2012
?>
<?php

// Begin of mofidication Flexibility of Export Records Options, by Masino Sinaga, May 14, 2012
define("MS_EXPORT_RECORD_OPTIONS", "currentpage"); // available values: "allpages", "currentpage", "selectedrecords"           

// End of modification Flexibility of Export Records Options, by Masino Sinaga, May 14, 2012
?>
<?php

// Begin of modification DropupListOptions, by Masino Sinaga, December 23, 2014
define("MS_USE_TABLE_SETTING_FOR_DROPUP_LISTOPTIONS", TRUE);
define("MS_GLOBAL_NUMBER_OF_ROWS_DROPUP_LISTOPTIONS", 4);

// End of modification DropupListOptions, by Masino Sinaga, December 23, 2014
?>
<?php

// Begin of modification Activate User Account by Admin, by Masino Sinaga, March 3, 2014
define("MS_SUSPEND_NEW_USER_ACCOUNT", FALSE);

// End of modification Activate User Account by Admin, by Masino Sinaga, March 3, 2014
define("MS_PASSWORD_POLICY_FROM_MASINO_REGISTER", FALSE);
define("MS_REGISTER_FORM_PANEL_TYPE", "panel-default"); // available: panel-default, panel-primary, panel-success, panel-warning, panel-danger, panel-info
define("MS_REGISTER_WINDOW_TYPE", "default"); // available: default, popup
define("MS_SHOW_BREADCRUMBLINKS_ON_REGISTER_PAGE", FALSE);
define("MS_TERMS_AND_CONDITION_CHECKBOX_ON_REGISTER_PAGE", FALSE);
define("MS_SHOW_CAPTCHA_ON_REGISTRATION_PAGE", TRUE); // <-- Switch TRUE (second param) to FALSE if you don't want to display Captcha on the Registration page
define("MS_SHOW_TERMS_AND_CONDITIONS_ON_REGISTRATION_PAGE", TRUE);
define("MS_PASSWORD_MINIMUM_LENGTH", 6); // default minimum 6 characters
define("MS_PASSWORD_MAXIMUM_LENGTH", 20); // default maximum 20 characters
define("MS_PASSWORD_MUST_COMPLY_WITH_MIN_LENGTH", TRUE);
define("MS_PASSWORD_MUST_COMPLY_WITH_MAX_LENGTH", TRUE);
define("MS_PASSWORD_MUST_INCLUDE_AT_LEAST_ONE_NUMBER", TRUE);
define("MS_PASSWORD_MUST_INCLUDE_AT_LEAST_ONE_LETTER", TRUE);
define("MS_PASSWORD_MUST_INCLUDE_AT_LEAST_ONE_CAPS", TRUE);
define("MS_PASSWORD_MUST_INCLUDE_AT_LEAST_ONE_SYMBOL", TRUE);
define("MS_PASSWORD_MUST_DIFFERENT_OLD_AND_NEW", TRUE);
?>
<?php
define("MS_LOGIN_FORM_PANEL_TYPE", "panel-default"); // available: panel-default, panel-primary, panel-success, panel-warning, panel-danger, panel-info
define("MS_LOGIN_WINDOW_TYPE", "default"); // available: default, popup
define("MS_SHOW_BREADCRUMBLINKS_ON_LOGIN_PAGE", FALSE);
define("MS_SHOW_CAPTCHA_ON_LOGIN_PAGE", TRUE);  // <-- Switch TRUE (second param) to FALSE if you don't want to display Captcha on the Login page
define("MS_USER_REGISTRATION", TRUE);
define("MS_REDIRECT_TO_LAST_VISITED_PAGE_AFTER_LOGIN", FALSE); // use for your own needs
?>
<?php
define("MS_LOADING_THEME", "pace-theme-corner-indicator.css"); // available: pace-theme-: barber-shop, big-counter, bounce, center-atom, center-circle, center-radar, center-simple, corner-indicator, fill-left, flash, flat-top, loading-bar, mac-osx, minimal
?>
<?php
define("MS_ABOUT_US_DIALOG_STYLE", "alertify"); // About Us Dialog style, available: "modal" or "alertify" (recommended: alertify <-- more feature and modern)
define("MS_TERMS_AND_CONDITIONS_DIALOG_STYLE", "alertify"); // Terms And Conditions Dialog style, available: "modal" or "alertify" (recommended: alertify <-- more feature and modern)
define("MS_HELP_DIALOG_STYLE", "alertify"); // Help Dialog style, available: "modal" or "alertify" (recommended: alertify <-- more feature and modern)
define("MS_AUTO_SWITCH_TABLE_WIDTH_STYLE", FALSE); // Auto switch table width style
define("MS_STICKY_MENU_ON_SCROLLING", TRUE); // Keep the Horizontal Menu sticky on top when user is scrolling down
define("MS_STICKY_FOOTER", TRUE); // Keep Footer sticky at the bottom of the page
define("MS_RELOAD_PAGE_FOR_FIRST_VISIT", FALSE); // If you have to force users to get the updated .js/.css by reloading page
define("MS_ALERTIFY_DIALOG_STYLE", "non-modal"); // Alertify dialog style, available: "modal" or "non-modal", default/recommended: "non-modal"
define("MS_ALERTIFY_TRANSITION_STYLE", "zoom"); // Alertify transition style, available: "pulse","slide","zoom","fade","flipx", or "flipy"
define("MS_SHOW_HEADER_IN_MOBILE_LAYOUT", FALSE); // Show header in mobile layout
define("MS_SHOW_LOGO_IN_MOBILE_LAYOUT", FALSE); // Show logo in mobile layout
define("MS_LOGO_IMAGE_IN_MOBILE_LAYOUT", ""); // Logo image file (i.e logo.png) for being displayed in mobile layout
define("MS_SHOW_ENTIRE_HEADER", TRUE); // Show entire header block
define("MS_TEXT_ALIGN_IN_HEADER", "right"); // Text align in header: "left", "center", or "right"
define("MS_SITE_TITLE_TEXT_STYLE", "normal"); // Whether "normal", "capitalize", or "uppercase"
define("MS_SHOW_LOGO_IN_HEADER", TRUE); // Show logo in header
define("MS_LOGO_WIDTH", 170); // Logo width in pixels
define("MS_SHOW_APP_TITLE_INSIDE_BODY", FALSE); // Show App Site title in body
define("MS_SHOW_SITE_TITLE_IN_HEADER", TRUE); // Show Site title in header
define("MS_SHOW_CURRENT_USER_IN_HEADER", TRUE); // Show current User status in header
define("MS_SHOW_ENTIRE_FOOTER", TRUE); // Show entire footer block
define("MS_SHOW_TEXT_IN_FOOTER", TRUE); // Show text in footer block
define("MS_SHOW_TERMS_AND_CONDITIONS_ON_FOOTER", TRUE); // Terms of Condition link
define("MS_SHOW_ABOUT_US_ON_FOOTER", TRUE); // About Us link
define("MS_SHOW_BACK_TO_TOP_ON_FOOTER", TRUE); // Show scroll to top on footer block
define("MS_LANGUAGE_SELECTOR_VISIBILITY", "belowheader"); // Whether "inheader", "belowheader", or "hidethemall"
define("MS_LANGUAGE_SELECTOR_ALIGN", "autoadjust"); // Language selector align: "autoadjust", "left", "center", or "right"

// Begin of modification Site Title Font Name and Font Size, by Masino Sinaga, August 20, 2014
define("MS_SITE_TITLE_FONT_NAME", "arial"); // Font Name; available options: "arial", "calibri", "century", "centurygothic", "comicsansms", "couriernew", "futuranormal", "lucidasans", "lucidasanstypewriter", "msgothic", "mssansserif", "tahoma", "timesnewroman", "verdana"
define("MS_SITE_TITLE_FONT_SIZE", "13px"); // Font Size; available options: "11px" up to "30px".

// End of modification Site Title Font Name and Font Size, by Masino Sinaga, August 20, 2014
// Begin of modification Font Name and Font Size, by Masino Sinaga, January 12, 2014

define("MS_FONT_NAME", "arial"); // Font Name; available options: "arial", "calibri", "century", "centurygothic", "comicsansms", "couriernew", "futuranormal", "lucidasans", "lucidasanstypewriter", "msgothic", "mssansserif", "tahoma", "timesnewroman", "verdana"
define("MS_FONT_SIZE", "13px"); // Font Size; available options: "11px", "12px", "13px", and "14px".

// End of modification Font Name and Font Size, by Masino Sinaga, January 12, 2014
// Begin of modification Header Logo and Text Class, by Masino Sinaga, August 14, 2014

define("MS_HEADER_LOGO_CLASS", "col-sm-5");
define("MS_HEADER_TEXT_CLASS", "col-sm-7");

// End of modification Header Logo and Text Class, by Masino Sinaga, August 14, 2014
// Begin of modification Announcement in All Pages, by Masino Sinaga, May 12, 2012

define("MS_SHOW_ANNOUNCEMENT", FALSE);  // Announcement status, set the second parameter to TRUE to show the announcement in all pages.
define("MS_ANNOUNCEMENT_TEXT", ""); // Announcement text, the value is derived from the .xml languages files

// End of modification Announcement in All Pages, by Masino Sinaga, May 12, 2012
// Begin of modification Maintenance Mode, by Masino Sinaga, May 12, 2012

define("MS_MAINTENANCE_MODE", FALSE); // Set the second parameter to TRUE if you want to display your website in Maintenance Mode
define("MS_MAINTENANCE_END_DATETIME", ""); // Set the second parameter to the future date/time value in "yyyy-MM-dd hh:mm:ss" format, if you want the system calculate how much long the system takes duration time to get the end of maintenance date/time. For example: 2011-08-30 17:28:00
define("MS_MAINTENANCE_TEXT", ""); // Just for displaying maintenance message to user with admin level, nothing else!
define("MS_AUTO_NORMAL_AFTER_MAINTENANCE", TRUE); // Set the second parameter to TRUE if you want the system to be automatically switch from the Maintenance Mode to Normal Mode whenever the end of maintenance date/time has been reached.

// End of modification Maintenance Mode, by Masino Sinaga, May 12, 2012
?>
<?php
define("MS_FORGOTPWD_FORM_PANEL_TYPE", "panel-default"); // available: panel-default, panel-primary, panel-success, panel-warning, panel-danger, panel-info
define("MS_FORGOTPWD_WINDOW_TYPE", "default"); // available: default, popup. Modified by Masino Sinaga, May 22, 2014
define("MS_SHOW_BREADCRUMBLINKS_ON_FORGOTPWD_PAGE", FALSE);
define("MS_SEND_PASSWORD_DIRECTLY_IF_NOT_ENCRYPTED", FALSE);

// Begin of modification Customizing Forgot Password Page, by Masino Sinaga, May 3, 2012
define("MS_SHOW_CAPTCHA_ON_FORGOT_PASSWORD_PAGE", TRUE); // <-- Switch TRUE (second param) to FALSE if you don't want to display Captcha on the Forgot Pwd page

// End of modification Customizing Forgot Password Page, by Masino Sinaga, May 3, 2012
define("MS_KNOWN_FIELD_OPTIONS", "Email"); // available: Email, Username, EmailOrUsername, modified by Masino Sinaga, April 21, 2014
?>
<?php
define("MS_DETECT_CHANGES_ON_ADD_FORM", TRUE);
define("MS_DETECT_CHANGES_ON_EDIT_FORM", TRUE); 
define("MS_DETECT_CHANGES_ON_SEARCH_FORM", TRUE); 
define("MS_DETECT_CHANGES_ON_LIST_FORM", FALSE);
define("MS_DETECT_CHANGES_ON_USERPRIV_FORM", TRUE);
define("MS_DETECT_CHANGES_ON_LOGIN_FORM", FALSE);
define("MS_DETECT_CHANGES_ON_REGISTRATION_FORM", TRUE);
define("MS_DETECT_CHANGES_ON_FORGOTPASSWORD_FORM", FALSE);
define("MS_DETECT_CHANGES_ON_CHANGEPASSWORD_FORM", TRUE);
?>
<?php
define("MS_SHOW_PLAIN_TEXT_PASSWORD", FALSE);
define("MS_PASSWORD_POLICY_FROM_MASINO_CHANGEPWD", FALSE);
define("MS_CHANGEPWD_FORM_PANEL_TYPE", "panel-default"); // available: panel-default, panel-primary, panel-success, panel-warning, panel-danger, panel-info
define("MS_CHANGEPWD_WINDOW_TYPE", "default"); // available: default, popup
define("MS_SHOW_BREADCRUMBLINKS_ON_CHANGEPWD_PAGE", FALSE);
define("MS_TERMS_AND_CONDITION_CHECKBOX_ON_CHANGEPWD_PAGE", FALSE);
define("MS_SHOW_CAPTCHA_ON_CHANGE_PASSWORD_PAGE", TRUE); // <-- Switch TRUE (second param) to FALSE if you don't want to display Captcha on the Change Password page
?>