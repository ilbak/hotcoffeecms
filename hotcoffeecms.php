<?php 
/*   C
 	 M  	Hot Coffee CMS - Core - v. 25.12
	 S		> www.ilbak.it
	[_])	> https://github.com/ilbak/hotcoffeecms 

*/


// Determine script
$scriptChiamante = $_SERVER['SCRIPT_NAME'];
$GLOBALS['dir'] = ltrim(dirname($scriptChiamante), '/');

// Manage temporary errors
if (isset($_SESSION['cmserror'])) {
    // Block
    if ((time() - $_SESSION['cmserror']) < 120) {
        die("Temporary error.");
    } 
    // Reset
    unset($_SESSION['cmserror']);
}
function cmserror($text = "<h1>Error!</h1> <h2>Please wait a few moments before trying again</h2>" ) { 
    $_SESSION['cmserror'] = time(); 
    die($text); 
}


// Add log
function addlog($text) {
    $filePath = '/home/mhd-01/www.pxzine.com/htdocs/log.php';
    $maxLines = 100;
    $startMarker = "<? /*";
    $endMarker = "*/ ?>";
    $safeText = addslashes($text);

    if (!file_exists($filePath)) {
        file_put_contents($filePath, $startMarker . "\n\n" . $endMarker);
    }

    $fileContents = file($filePath, FILE_IGNORE_NEW_LINES);

    if (trim($fileContents[0]) !== $startMarker || trim(end($fileContents)) !== $endMarker) {
        throw new Exception("console.php not valid");
    }

    array_shift($fileContents);
    array_pop($fileContents);

	$safeTextForJs = json_encode($text);
	echo "<script>console.log({$safeTextForJs})</script>";

    array_unshift($fileContents, $safeText); 
    $fileContents = array_slice($fileContents, 0, $maxLines);

    // Put marker and save
    array_unshift($fileContents, $startMarker);
    array_push($fileContents, $endMarker);

    file_put_contents($filePath, implode("\n", $fileContents));
}

// Redirect to index.php if the script filename isn't index.php
if (!stristr($_SERVER['SCRIPT_FILENAME'], "index.php")) {
    header("Location: index.php");
    exit;
}


// Set language
$allowedLanguages = ['it', 'en', 'fr', 'de', 'es'];
if (isset($_REQUEST['lang']) && in_array($_REQUEST['lang'], $allowedLanguages)) {
    $_SESSION['lang'] = $_REQUEST['lang'];
} else {
    $_SESSION['lang'] = 'it';
}

// Set system variables
$cmsdir = rtrim(dirname($_SERVER['SCRIPT_FILENAME']), '/') . '/';
$cmspath = rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/';

// Detect if HTTPS is being used
$cmsissecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on');
$REQUEST_PROTOCOL = $cmsissecure ? 'https' : 'http';

// Generate global URL variable
$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(str_replace("index.php", "", strtok($_SERVER['REQUEST_URI'], '?')), '/');
$GLOBALS['cmsurl'] = $REQUEST_PROTOCOL . '://' . $host . $uri . '/';

// Set default home page
$page = isset($_REQUEST['pag']) ? strtolower($_REQUEST['pag']) : "home";
// Sanificazione rigorosa: accetta solo lettere, numeri e trattini
$sanitized_page = preg_replace('/[^a-z0-9-]/', '', strtolower($page));
$GLOBALS['pag'] = $sanitized_page;


// Redirect to index if requested page file does not exist
if (!file_exists($cmsdir . $GLOBALS['pag'] . '.php')) {
    if (!file_exists($cmsdir . '404.php')) {
echo "<p>[!] Page not found! [!]</p>";
        exit;
    } else {
        $GLOBALS['pag'] = "404";
    }
}

// Parse tags from URL parameter
if (isset($_REQUEST['tag'])) {
    $GLOBALS['tag'] = explode("-", $_REQUEST['tag']);
}

// Include initialization file if it exists
if (file_exists("./hotcoffeecms-init.php")) {
    include "./hotcoffeecms-init.php";
}

// Function to get real IP address
function getRealIp() {
    $ip_keys = ["HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED", "HTTP_FORWARDED_FOR", "HTTP_FORWARDED", "REMOTE_ADDR"];
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            return $_SERVER[$key];
        }
    }
    return 'UNKNOWN';
}

$ip = getRealIp();

// Create default home.php if it doesn't exist
if (!file_exists($cmsdir . "home.php")) {
    file_put_contents($cmsdir . "home.php", '<?php echo "<h1>Hot Coffee is ready!</h1><p>New site coming soon...</p>"; ?>');
}


// Include CSS and JS page if exists
$safe_pag = htmlspecialchars($GLOBALS['pag']);
if (file_exists($cmsdir . $GLOBALS['pag'] . ".css")) {
    echo "<link rel='stylesheet' type='text/css' href='" . htmlspecialchars($cmspath) . "{$safe_pag}.css'>";
}
if (file_exists($cmsdir . $GLOBALS['pag'] . ".js")) {
    echo "<link rel='stylesheet' type='text/css' href='" . htmlspecialchars($cmspath) . "{$safe_pag}.js'>";
    echo "<script src='" . htmlspecialchars($cmspath) . "{$safe_pag}.js' type='text/javascript'></script>";
}


// Publish blocks
if (isset($GLOBALS['cms'])) {
    if (is_array($GLOBALS['cms'])) {
        echo "<ul>";
        foreach ($GLOBALS['cms'] as $item) {
            if (file_exists($cmsdir . $item . ".php")) {
                echo $item === "home" ? "<li><a href='{$cmspath}'>{$item}</a></li>" : "<li><a href='{$cmspath}{$item}'>{$item}</a></li>";
            }
        }
        echo "</ul>";
    } elseif (is_dir($cmsdir)) {
        $GLOBALS['cmssideview'] = true;
        $filesToInclude = array_filter(scandir($cmsdir), function($file) {
            return strpos($file, $GLOBALS['cms'] . '.php') === 0;
        });

        sort($filesToInclude);
        echo "<ul>";
        foreach ($filesToInclude as $file) {
            echo "<li>";
            include $cmsdir . $file;
            echo "</li>";
        }
        echo "</ul>";
    }
} else {
    $GLOBALS['cmssideview'] = false;
    $pageFile = $cmsdir . $GLOBALS['pag'] . ".php";
    if (file_exists($pageFile)) {
        include $pageFile;
    } else {
        echo "<h1>[!]) Page not found. [!])</h1>";
        echo "<h1>{$GLOBALS['pag']}</h1>";
    }
}

// Clean up global variables
$GLOBALS['cmssideview'] = false;
unset($GLOBALS['cms']);
?>
