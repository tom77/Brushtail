<?php



//DATABASE SETTINGS

$DATABASE_FORMAT = "mysql"; //"sqlite", "sqlite3" or "mysql"


//MySQL 
//database user
$DBUSER= "intranet";
//database password
$DBPASSWORD = "intranet";
//database name
$DATABASE = "intranet";
//database host
$HOST = "localhost";

//SQLITE 
//absolute path

$SQLITE = "../sql/intranet.db";



//LDAP SETTINGS
//ldap server
$LDAPHOST = "lpadserver";
//ldap domain
$LDAPDOMAIN = "dc=foo,dc=org";
//allow updates to passwords on windows (ldap) server
$LDAPUPDATE = "yes";
//ldap administrative account user name - only needed if wanting to change active directory password
$LDAPADMINDN = "cn=Administrator,CN=Users,dc=foo,dc=org";
//ldap administrative account password - only needed if wanting to change active directory password
$LDAPADMINPASS = "password";


//Mailbox authentication - (only relevant if authenticating users via email accounts)
//requires imap to compiled/enabled in php
//imap example $MAILSERVER = "{hostname:143/imap}"; 
//pop3 example $MAILSERVER = "{hostname:110/pop3}"; 
//http://www.php.net/manual/en/function.imap-open.php
$MAILSERVER = "{mailserver:143/imap}";


$CAPTCHA = "all"; // all,internal,web,off
$SPAMTOOLS = "off"; // all,internal,web,off

$EMAIL_FROM = "intranet@intranet";

//Public event bookings
$EVENTAUTHENTICATION = "web";
//values: disabled/autoaccept/local/web/ldap/email/registration/soap
$UPCOMING_EVENTS_DAYS_AHEAD = 30;
// How may days ahead the upcoming events list will display
$MAX_WEB_BOOKINGS = 3;
//maximum number of event places that can be placed in a single booking
$WEB_CALENDAR_DIRECTORY_URL = "http://www.foolibrary.org/brushtail_5/web/";
$WEB_CALENDAR_POPUP = "off"; //on or off
//email registration confirmation url
$EVENT_EMAIL_FOOTER = "
For more events visit www.foolibrary.org/webcal.php
or Telephone XXXXXXXXXXXX.
";
//footer attached to emails sent by event booking sysytem
$HOME_LINK = "http://librarywebsite";
$HOME_LABEL = "Library homepage";
$EVENTBOOKINGS_FROM = "bookings@library.org";
$EMAIL_CONFIRMATION_URL = "http://yourdomain/intranet/web/";






//Authenticating patron bookings via web catalogue
$WEBHOST = 'server';
$WEBPATH = '/webcat_path';
$STRINGACTION = "accept"; //value: accept deny
$STRING = "Account Overview";
$USERFIELD = "username";
$PASSWORDFIELD = "password";
$OTHERFIELDS["formurl"] = "http://foo.org/webcatt";
$PROXY_IP = "";
$PROXY_PORT = "";
$CHECKREDIRECT = "no";





//Intranet PC pookings - 
$REFRESH = 5; // In minutes, staff booking screen automatic refresh.  0 means no automatic refesh.
$PCFOOTER = "
Book a pc online at 
www.foo.org/web/pcbookings.php"; //Intranet PC bookings - booking slip footer text


//Online pc bookings
$PCAUTHENTICATION = "disabled";//values: disabled/web/ldap/email/soap
$BOOKINGINTERVAL = 30;
$DAYSAHEAD = 7; // this is how may days ahead you can book
$DISPLAYPCNAME = 1; //set to 0 to hide the pc in the booking confirmation
$CURRENTWEEKONLY = 0; //bookings can only be made for current week
$DELAY = 1; // delay in hours before next web booking can commence from end of any existing bookings
$BOOKBY = "PC";  //PC or TYPE 
$PCIMAGE = "0";


//PHP to gzip compress page html output for faster page loading
//Not necessary if the web server is already performing compression
//values on or off
$COMPRESSION = "off";



//encryption key - used to encrypt user session information 
$KEY = "29b40asdfasdf334434243"; 

//Format for datepicker widget
//values %d-%m-%Y or %m-%d-%Y
$DATEFORMAT = "%d-%m-%Y";

//Calendar week starts on "SUN or ""MON"
$CALENDAR_START ="MON";
//Display day names in each calendar cell
$CALENDAR_DAY_NAMES ="off";

//WYSIWYG editor  Values:openWYSIWYG , CKEditor, NicEdit
$WYSIWYG = "NicEdit";


## Below you can select your location by uncommenting the setlocale line. This will affect date formatting.

#LOCALE SETTINGS
$locale = setlocale(LC_TIME, 'uk_UK', 'uk','en_GB');  //AUSTRALIA LINUX
$locale = setlocale(LC_MONETARY, 'us_US', 'us','en_US'); //AUSTRALIA LINUX

//setlocale(LC_ALL, 'ENA'); //Australia WINDOWS

//setlocale(LC_ALL, 'FRA'); // FRANCE WINDOWS
//$locale = setlocale(LC_ALL, 'fr_FR@euro', 'fr_FR', 'fr');   //FRANCE LINUX

//setlocale(LC_ALL, 'DEU'); // GERMANY WINDOWS
//$locale = setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge'); //GERMANY LINUX


//Email message parameters

//This value can be set on windows servers to specify the SMTP mail server to send email messages to
//By default this is set for the entire server in the php.ini
//By setting the value here the server can have multiple brushtail installations sending email to different servers

//$SMTP = "mailserverip";

// YOu can specify the email from address, that overides PHP default

$EMAIL_FROM = "blah@foo.org";


$PCDEBUG = "off";

?>