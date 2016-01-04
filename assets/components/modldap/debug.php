<?php
/*************************
 * SECURITY: IMPORTANT! *
*************************/
$_securityARG = "debug";
$_securityVAL = "secretword";  // change your secret word

header("Pragma: no-cache"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");   

// Only you can view this page!!!
if (!isset($_GET[$_securityARG]) || $_GET[$_securityARG] !== $_securityVAL || $_securityVAL == 'secretword' || empty($_securityVAL)) {
    if ($_tpl = file_get_contents('debug.html')) {
        echo $_tpl;
    } else {
        header('HTTP/1.0 403 Forbidden');
        echo "Access Forbidden!";
    }
    die();
}

/***********************> 
 * LDAP CONFIGURATION *
************************/
$ldap_server          = "localhost";
$ldap_server_ssl      = false;
$ldap_server_ssl_port = 603;
$ldap_server_protocol = 3;
$ldap_server_reveral  = 0;

$ldap_username = "MyUSERNAME"; // username
$ldap_password = "MyPASSWORD"; // password

//setting for ldap_bind
$ldap_bind_username = "uid=$ldap_username,ou=Member,dc=domain,dc=com";      // change to your username format
$ldap_bind_password = "$ldap_password";                                     // change to your password

//setting for ldap_search
$ldap_search_basedn = "dc=domain,dc=com";                                   // change to your basedn
$ldap_search_filter = "(&(objectClass=person)(uid=$ldap_username))";        // change to your filter format
$ldap_search_attrib =  array(                                               // change to your attributes desired. empty array means get all result...
    'cn', //fullname
    'mail', //email
    'memberof' //group:memberof
    );    


/***********************> 
 * START LDAP PROCESS *
************************/
putenv('LDAPTLS_REQCERT=never');
ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7); //debug

$_conn = null;
$_bind = null;
$_err  = false;
$_process= array();
$_warning= array();
$_result = array();

//connecting
if ($ldap_server_ssl) {
    $_process[] = "Connecting to <strong>ldaps://$ldap_server</strong> using SSL";
    process_time();
    $_conn = @ldap_connect("ldaps://".$ldap_server, $ldap_server_ssl_port);
} else {
    $_process[] = "Connecting to <strong>$ldap_server</strong>";
    process_time();
    $_conn = @ldap_connect($ldap_server);
}

if ($_conn) {
    $_warning[] = '<span class="success">'. @ldap_error($_conn) . ' <em> processed in ' . process_time() . '</em></span>';
    
    ldap_set_option($_conn, LDAP_OPT_PROTOCOL_VERSION, $ldap_server_protocol);
    ldap_set_option($_conn, LDAP_OPT_REFERRALS, $ldap_server_reveral);

    //binding
    $_process[] = "Binding to $ldap_server using <strong>$ldap_bind_username</strong>";
    process_time();
    if ($_bind = @ldap_bind($_conn, $ldap_bind_username, $ldap_bind_password)) {
        $_warning[] = '<span class="success">'. @ldap_error($_conn) . ' <em> processed in ' . process_time() . '</em></span>';
        
        //searching
        $_process[] = "Searching entries using basedn <strong>$ldap_search_basedn</strong>";
        process_time();
        if ($sr = @ldap_search($_conn, $ldap_search_basedn, $ldap_search_filter, $ldap_search_attrib)) {
            $_warning[] = '<span class="success">'. @ldap_error($_conn) . ' <em> processed in ' . process_time() . '</em></span>';
            
            //get entries...
            $_process[] = "Get LDAP entries";
            process_time();
            if ($_result = @ldap_get_entries($_conn, $sr)) {
                $_warning[] = '<span class="success">'. @ldap_error($_conn) . ' <em> processed in ' . process_time() . '</em></span>';
            } else {
                $_warning[] = '<span class="error">'. @ldap_error($_conn) . ' <em> processed in ' . process_time() . '</em></span>';
            }
            
        } else {
            $_warning[] = '<span class="error">'. @ldap_error($_conn) . ' <em> processed in ' . process_time() . '</em></span>';
        }
        
    } else {
        $_warning[] = '<span class="error">'. @ldap_error($_conn) . ' <em> processed in ' . process_time() . '</em></span>';
    }
    
} else {
    $_warning[] = '<span class="error">'. @ldap_error($_conn) . ' <em> processed in ' . process_time() . '</em></span>';
}

@ldap_close($_conn);

$t_start = 0;
function process_time() {
    global $t_start;
    $mtime= microtime();
    $mtime= explode(" ", $mtime);
    $mtime= $mtime[1] + $mtime[0];
    $t_ends= $mtime;
    if ($t_start) {
        $totalTime= ($t_ends - $t_start);
        $totalTime= sprintf("%2.4f s", $totalTime);
    } else {
        $totalTime='- s';
    }
    
    $t_start = $t_ends;
    
    return $totalTime;
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>ModLDAP Debug Test</title>
  <style>
    ul, li, p {font-family: "Arial", "Helvetica", sans-serif; font-size:11pt; margin-top:6px; margin-bottom:6px;} 
    h1, h2, h3, h4, h5, h6, u {font-family: "Times New Roman", "Garamond", Times, serif;}
    pre {border-left: 10px solid #dddddd; margin-left: 10px; padding-left: 10px;}
    .success {color:green; font-weight:bold; display:block;}
    .error {color:red; font-weight:bold; display:block;}
    .success em, .error em {font-weight: normal; color: #999999;}
  </style>
</head>

<body>
  <h1>ModLDAP  Debug Test</h1>
  
  <h2><u>Process:</u></h2>
  <ul>
<?php
foreach ($_process as $i=>$proc) {
    echo "    <li>$proc: " . $_warning[$i]. "</li>\n";
}
?>
  </ul>
  <h2><u>Results:</u></h2>
  <p>Below are the entries from LDAP</p>
  <pre>
<?php
  print_r($_result);
?>
  </pre>
  
</body>
</html>