AI Prompt — Fix Security Vulnerabilities in a PHP Web Application
You are a senior PHP security engineer. I have a PHP web application called Mentor-Mentee (a Toastmasters Nepal member directory). A VAPT (Vulnerability Assessment and Penetration Testing) scan was done on it and 18 security vulnerabilities were found. I will give you the exact vulnerable code for each issue. For each one, please:

Explain why the code is vulnerable (in simple terms)
Give me the exact fixed PHP/Apache code to replace the vulnerable section
Tell me where in the file to make the change (file name + line number)
The Application Stack
Language: PHP (no framework — raw PHP with OOP classes)
Database: MySQL via mysqli_* functions (no PDO)
Auth: PHP sessions
Email: PHPMailer via SMTP
Web server: Apache with .htaccess
VULNERABILITY 1 — Hardcoded SMTP Password in Source Code
File: classes/functions.class.php — Line 53–57
Severity: CRITICAL

php

// VULNERABLE CODE:
$objectSendMail->Host     = 'mail.rajanmaharjan.com.np';
$objectSendMail->SMTPAuth = true;
$objectSendMail->Username = 'mentor-mentee@rajanmaharjan.com.np';
$objectSendMail->Password = 'C0ntact@123';   // ← hardcoded plaintext password
$objectSendMail->Port     = 587;
Problem: The SMTP password is hardcoded in a PHP file committed to a public GitHub repository. Anyone can read it and use the email account.

Fix needed: Show me how to move this to an environment variable (using $_ENV or getenv()) and where to define it safely. Also show me what to add to .gitignore.

VULNERABILITY 2 — MD5 Used for Password Hashing
File: pages/change-password.php — Line 15
Severity: CRITICAL

php

// VULNERABLE CODE:
$objectUser->changePassword(
    array("pass_word" => md5($new_password), "pwd_reset_request" => "N"),
    $_SESSION['session_user_id']
);
Also in: classes/users.class.php — Line 82 (legacy fallback):

php

return hash_equals($storedHash, md5($plainPassword));
Problem: MD5 is not a password hashing algorithm. It has no salt and can be cracked in seconds using rainbow tables.

Fix needed: Replace md5() with password_hash() using bcrypt. Also show how to migrate old MD5 hashes to bcrypt on next login (the app already does this partially in authenticate() — keep that pattern consistent for changePassword() too).

VULNERABILITY 3 — Database Credentials in Public Git Repository
File: system-files/settings.php
Severity: CRITICAL

php

// VULNERABLE CODE:
$dbHostName = "localhost";
$dbName     = "rajanmah_mm_dbms";
$dbUserName = "root";     // ← using root DB user
$dbUserPwd  = "";         // ← empty password (or plaintext on production)
define("SITE_PATH", "http://localhost/mentor-mentee/");
Problem: DB credentials are in a versioned PHP file on a public repository. Using root as the DB user gives full database access if the app is ever compromised.

Fix needed:

How to move DB credentials to a .env file outside the web root
How to load them securely in settings.php using getenv() or a dotenv library
What .gitignore entries to add
What least-privilege MySQL user and GRANT statement to create instead of root
VULNERABILITY 4 — No Rate Limiting or Brute-Force Protection on Login
File: login.php — Lines 31–73
Severity: HIGH

php

// VULNERABLE CODE:
if(isset($_POST['btnLogin'])){
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    // ... no attempt counter, no lockout, no CAPTCHA
    $userData = $objectUser->authenticate($email, $password);
}
The app writes login attempts to tin_member_login_logs table but never checks or limits them.

Problem: An attacker can send unlimited login attempts (credential stuffing / brute-force) with no throttle.

Fix needed: Show me PHP code to:

Count failed attempts for an email in the last 15 minutes using the existing tin_member_login_logs table
Block login if count > 5, showing a friendly message
(Bonus) Add a hidden honeypot field to catch bots
VULNERABILITY 5 — Stored XSS: Unescaped Session Variable on Dashboard
File: pages/dashboard.php — Line 6
Severity: HIGH

php

// VULNERABLE CODE:
<h3 class="font-weight-bold">Welcome <?php echo $_SESSION['session_fullname'] ?></h3>
Problem: session_fullname is set from the database full_name field. If a user saves <script>alert(document.cookie)</script> as their name, it executes in the browser of every user who sees the dashboard.

Fix needed: Show the corrected single line using the app's existing h() helper function (already defined in files.inc.php as htmlspecialchars($v, ENT_QUOTES, 'UTF-8')).

VULNERABILITY 6 — Stored XSS: Multiple Unescaped Echoes in Dashboard Table
File: pages/dashboard.php — Lines 121, 127, 132, 136, 138
Severity: HIGH

php

// VULNERABLE CODE (5 unescaped outputs):
echo $singleUser->full_name;                          // line 121
echo $clubFound->club_name;                           // line 127
echo $singleUser->email;                              // line 132 (also in href)
echo $singleUser->mobile_number;                      // line 136
echo $singleUser->profile_link;                       // line 138 (in href attribute)
Problem: All database values are written to HTML without encoding. An attacker who controls any of these fields can inject HTML/JavaScript.

Fix needed: Give me the corrected version of each of these 5 echo lines using h() or htmlspecialchars(). For the href attribute (profile_link), also show how to validate it is a safe URL (must start with http:// or https://).

VULNERABILITY 7 — SQL Injection Pattern: Raw WHERE String Concatenation
File: classes/common.class.php — Lines 32–56, 122–124, 138–143
Severity: HIGH

php

// VULNERABLE PATTERN — $where is never sanitized inside this method:
function select($tableName, $fields = array("*"), $where = 1, ...) {
    $this->sql = "SELECT " . join(", ", $fields) . " FROM " . TBL_PREFIX . "$tableName ";
    if ($where != '' || $where != 1) {
        $this->sql .= " WHERE " . $where;   // ← raw string concat
    }
    // ...
}
function updateStatus($tableName, $whereCondition) {
    $this->sql = "UPDATE " . TBL_PREFIX . "$tableName set is_active = (1-is_active) where $whereCondition"; // ← raw
}
function delete($table, $where) {
    $this->sql = "update " . TBL_PREFIX . "$table set deleted_by='...' where $where"; // ← raw
}
Problem: Any caller that passes unsanitized user input to $where creates an exploitable SQL injection point. The pattern itself is inherently fragile.

Fix needed: The app uses mysqli (not PDO). Show how to refactor the select() and insert() / update() methods in common.class.php to use MySQLi prepared statements with ? bind parameters, while keeping the same method signatures so existing callers still work.

VULNERABILITY 8 — Login Log INSERT Uses Unescaped User Input
File: login.php — Lines 45–46
Severity: HIGH

php

// VULNERABLE CODE:
$objectFunctions->sql = "Insert into tin_member_login_logs (member_email) values ('$email')";
$objectFunctions->execute();
Problem: $email has only been through a regex format check, not SQL-escaped. It is written raw into the SQL string.

Fix needed: Show the corrected version using the app's existing $objectFunctions->escape() method, or a prepared statement.

VULNERABILITY 9 — error_log File Accessible From the Web
File: error_log (in web root), .htaccess
Severity: HIGH

Problem: The PHP error_log file is stored at /mentor-mentee/error_log inside the web root, exposing full server paths like /home/toastmas/mm.toastmastersnepal.org/classes/users.class.php to anyone who requests it over HTTP.

Fix needed:

The .htaccess rule to deny public access to the error_log file
The php.ini / ini_set() directive to redirect PHP errors to a path outside the web root
Where to call ini_set('error_log', ...) in the application (hint: files.inc.php)
VULNERABILITY 10 — Logout Does Not Properly Destroy the Session
File: logout.php
Severity: HIGH

php

// VULNERABLE CODE:
@session_start();
$_SESSION['session_email']    = '';
$_SESSION['session_fullname'] = '';
unset($_SESSION);   // ← does NOT destroy server-side session or clear the cookie
echo "<script ...>alert('Successfull logout');window.location='login.php'</script>";
Problem: unset($_SESSION) only clears the PHP in-memory array. The session file/record on the server survives, and the browser cookie is not cleared. An attacker with a captured cookie can continue using the session.

Fix needed: Give me the corrected logout.php that:

Properly clears $_SESSION
Clears the session cookie from the browser
Calls session_destroy()
Keeps the redirect to login.php
VULNERABILITY 11 — No CSRF Protection on Change-Password Form
File: pages/change-password.php
Severity: HIGH

php

// VULNERABLE CODE — form handler has no CSRF check:
if (isset($_POST['btnChangePassword'])) {
    $old_password   = trim($_POST['old_password']);
    $new_password   = trim($_POST['new_password']);
    $retype_password = trim($_POST['retype_password']);
    // ... no validateCsrfToken() call
html

<!-- VULNERABLE CODE — form has no hidden CSRF token field: -->
<form method="POST" action="" name="form-reset-pwd" id="form-reset-pwd">
    <!-- no csrf_token input here -->
Problem: Without a CSRF token, an attacker's malicious page can silently submit the change-password form for a logged-in victim.

Note: The app already has getCsrfToken() and validateCsrfToken() methods in functions.class.php. CSRF is already working on login.php, register.php, and appointment.php.

Fix needed: Show exactly what 2 lines to add to the HTML form and what PHP check to add at the top of the POST handler, matching the pattern already used in login.php.

VULNERABILITY 12 — profile_link Saved Without URL Validation (XSS / Open Redirect)
File: pages/profile.php — Line 150, and displayed in dashboard.php:138, clubs.php:48, mentorship.php:31
Severity: HIGH

php

// VULNERABLE CODE — no URL scheme validation when saving:
$arrayData['profile_link'] = trim($_POST['profile_link'] ?? '');
html

<!-- VULNERABLE CODE — output in href without validation: -->
<a href="<?php echo $singleUser->profile_link ?>">Personal Info</a>
Problem: A user can set profile_link to javascript:alert(document.cookie). Clicking it runs JS in the victim's browser (session hijacking).

Fix needed:

PHP validation before saving: only allow URLs beginning with http:// or https://
Corrected HTML output using h() for all 4 places where profile_link is output in href attributes
Add rel="noopener noreferrer" to all external links
VULNERABILITY 13 — Appointment Fields Not Validated Before DB Insert
File: pages/appointment.php — Lines 22–26
Severity: MEDIUM

php

// VULNERABLE CODE:
$arrayField['appointment_date'] = $_POST['meeting_date'];    // no date validation
$arrayField['agenda_discussed'] = $_POST['meeting_detail'];  // no length limit
$arrayField['meeting_type']     = $_POST['meeting_type'];    // not whitelisted
$arrayField['is_active']        = '1';
$arrayField['request_id']       = $postedRequestId;
Problem: meeting_date is not validated as a real date. meeting_type is not restricted to ['O', 'P']. agenda_discussed has no length limit and could store oversized payloads.

Fix needed: Show PHP validation code to:

Validate meeting_date is a valid date using DateTime::createFromFormat()
Whitelist meeting_type to ['O', 'P']
Truncate or reject agenda_discussed over 2000 characters
VULNERABILITY 14 — Password Reset Token Not Properly Invalidated After Use
File: reset-password.php — Lines 31–35
Severity: MEDIUM

php

// VULNERABLE CODE:
// Step 1: Reset the password
$objectFunctions->update('members', array('pass_word' => password_hash($newPassword, PASSWORD_DEFAULT), ...), "email='$safeSenderEmail'");
// Step 2: Invalidate token — but only blanks the key, doesn't delete the row
$objectFunctions->update('unlock_request', array('hash_key' => ''), "hash_key='" . $objectUser->escape($accode) . "'");
Problem:

Setting hash_key = '' means a second request with hash_key='' could potentially match rows and trigger another password reset
The row is never deleted, so the table fills with stale records
No used_at timestamp to mark tokens as consumed
Fix needed: Show how to properly invalidate the token by:

Either deleting the row after use OR
Adding a used_at timestamp column and checking WHERE used_at IS NULL And show how to add a cleanup query for tokens older than 10 minutes.
VULNERABILITY 15 — Session Cookie Missing secure Flag
File: files.inc.php — Lines 9–12
Severity: MEDIUM

php

// VULNERABLE CODE:
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    // 'secure' => true   ← MISSING
]);
session_start();
Problem: Without secure => true, the browser may send the session cookie over plain HTTP, exposing it to network sniffing.

Fix needed: Show the corrected session_set_cookie_params() call, plus the .htaccess lines to force HTTP→HTTPS redirect so secure cookies are never downgraded.

VULNERABILITY 16 — error_reporting(E_ALL) Enabled in Production
File: files.inc.php — Line 4
Severity: MEDIUM

php

// VULNERABLE CODE:
error_reporting(E_ALL);
Problem: If display_errors is also On (common on shared hosting), PHP error messages with file paths, class names, and stack traces are shown to users in the browser.

Fix needed: Show the correct production error configuration using ini_set() calls to:

Disable error display to the browser
Enable logging to a file outside the web root
Still report all errors for logging purposes
VULNERABILITY 17 — Developer's Personal Contact Details Hardcoded in HTML
File: register.php — Lines 59, 83
Severity: MEDIUM

php

// VULNERABLE CODE — personal phone number in email body:
"...OR you may also directly call/whatsapp to site admin Rajan Maharjan (9851122778)..."
// VULNERABLE CODE — personal contact in HTML page:
"...send screenshot...to mail@rajanmaharjan.com.np or whatsapp to 9851122778"
Problem: Developer's personal mobile number and personal email are exposed in user-facing HTML and in system emails — a privacy and social engineering risk.

Fix needed: Show how to:

Move these to named constants in system-files/constant.php
Reference constants in register.php instead of hardcoded strings
Suggest appropriate generic contact details to use
VULNERABILITY 18 — Missing Security HTTP Response Headers
File: .htaccess (current content shown below)
Severity: MEDIUM

apache

# CURRENT .htaccess (no security headers at all):
Options +FollowSymLinks
RewriteEngine on
RewriteRule ^/?([^/]*).html index.php?url1=$1
RewriteRule ^/?([^/]*)/([^/]*).html index.php?url1=$1&url2=$2
RewriteRule ^/?([^/]*)/([^/]*)/([^/]*).html default.php?url1=$1&url2=$2&url3=$3
Missing headers:

Content-Security-Policy — prevents XSS via inline scripts
X-Frame-Options: DENY — prevents clickjacking
X-Content-Type-Options: nosniff — prevents MIME sniffing
Referrer-Policy — prevents URL leakage
Strict-Transport-Security — forces HTTPS
Permissions-Policy — restricts browser features
Fix needed: Give me the complete updated .htaccess file with:

All the existing rewrite rules preserved
All 6 security headers added using Header always set
The HTTPS redirect rule
A rule to block access to error_log, .env, and .git directory
Summary of All Files That Need Changes
File	Issues
classes/functions.class.php	V1 – Remove hardcoded SMTP password
pages/change-password.php	V2 – Replace MD5 with bcrypt; V11 – Add CSRF token
system-files/settings.php	V3 – Move DB creds to env vars
login.php	V4 – Add brute-force protection; V8 – Escape login log insert
pages/dashboard.php	V5, V6 – Escape all output with h(); V12 – Validate profile_link href
classes/common.class.php	V7 – Parameterize SQL WHERE clauses
error_log + files.inc.php	V9 – Block public access; fix error_log path
logout.php	V10 – Properly destroy session
pages/profile.php	V12 – Validate profile_link URL on save
pages/clubs.php	V12 – Escape profile_link in href
pages/mentorship.php	V12 – Escape profile_link in href
pages/appointment.php	V13 – Validate date, meeting_type, agenda length
reset-password.php	V14 – Properly invalidate reset token
files.inc.php	V15 – Add secure cookie flag; V16 – Fix error_reporting
register.php	V17 – Move personal contact to constants
.htaccess	V18 – Add all security headers + HTTPS redirect
Additional Context
The app already has a working h() helper: htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8') defined in files.inc.php
CSRF token generation uses bin2hex(random_bytes(32)) stored in $_SESSION['csrf_token']
Password reset tokens use bin2hex(random_bytes(32)) — 64 hex chars validated with /^[a-f0-9]{64}$/
The DB layer uses raw mysqli (not PDO) via a custom connection class
PHP sessions use HttpOnly=true, SameSite=Lax already — just need secure=true
Please provide the fix for each vulnerability numbered V1 through V18 in order.