<?php session_start();
/*   C
 	 M  	Hot Coffee CMS - Core - v. 25.02
	 S		> www.ilbak.it
	[_])	> https://github.com/ilbak/hotcoffeecms 

*/

// Determina il percorso completo dello script chiamante
$scriptChiamante = $_SERVER['SCRIPT_NAME'];
$GLOBALS['dir'] = ltrim(dirname($scriptChiamante), '/');

// Gestione ban temporanei
if (isset($_SESSION['tempban'])) {
    if ((time() - $_SESSION['tempban']) < 120) {
        die("Temporary error.");
    } else {
        unset($_SESSION['tempban']);
    }
}

// Funzione per aggiungere log
function addlog($text) {
    $filePath = '.hotcoffeecmslog.php';
    $maxLines = 100;
    $startMarker = "<? /*";
    $endMarker = "*/ ?>";
    $safeText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    // Verifica se il file è scrivibile
    if (file_exists($filePath) && !is_writable($filePath)) {
        throw new Exception("Il file di log non è scrivibile.");
    }

    // Apri il file in modalità lettura/scrittura
    $file = fopen($filePath, 'r+');
    if (!$file) {
        throw new Exception("Impossibile aprire il file di log.");
    }

    // Leggi il contenuto del file
    $fileContents = [];
    while (($line = fgets($file)) !== false) {
        $fileContents[] = trim($line);
    }

    // Verifica i marker di inizio e fine
    if (trim($fileContents[0]) !== $startMarker || trim(end($fileContents)) !== $endMarker) {
        fclose($file);
        throw new Exception("Il file log.php non ha un formato valido.");
    }

    // Rimuovi i marker temporaneamente
    array_shift($fileContents);
    array_pop($fileContents);

    // Aggiungi il nuovo log in cima
    array_unshift($fileContents, $safeText);

    // Limita il numero di righe
    $fileContents = array_slice($fileContents, 0, $maxLines);

    // Reinserisci i marker
    array_unshift($fileContents, $startMarker);
    array_push($fileContents, $endMarker);

    // Tronca il file e riscrivi il contenuto
    ftruncate($file, 0);
    rewind($file);
    foreach ($fileContents as $line) {
        fwrite($file, $line . "\n");
    }

    fclose($file);

    // Log nella console del browser (solo per debug)
    echo "<script>console.log('{$safeText}')</script>";
}

// Reindirizzamento a index.php
//if (!stristr($_SERVER['SCRIPT_FILENAME'], "index.php")) {    header("Location: index.php");    exit; }

// Imposta la lingua
if (isset($_REQUEST['lang']) && in_array($_REQUEST['lang'], ['it', 'en', 'fr', 'de', 'es'])) {
    $_SESSION['lang'] = $_REQUEST['lang'];
} else {
    $_SESSION['lang'] = 'it';
}

// Imposta variabili di sistema
$cmsdir = rtrim(dirname($_SERVER['SCRIPT_FILENAME']), '/') . '/';
$cmspath = rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/';

// Rileva se è in uso HTTPS
$cmsissecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on');
$REQUEST_PROTOCOL = $cmsissecure ? 'https' : 'http';

// Genera l'URL globale
$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(str_replace("index.php", "", strtok($_SERVER['REQUEST_URI'], '?')), '/');
$GLOBALS['cmsurl'] = $REQUEST_PROTOCOL . '://' . $host . $uri . '/';

// Imposta la pagina home di default
$GLOBALS['pag'] = isset($_REQUEST['pag']) ? strtolower($_REQUEST['pag']) : "home";

// Reindirizza a index.php se la pagina richiesta non esiste
if (!file_exists($cmsdir . $GLOBALS['pag'] . '.php')) {
    if (!file_exists($cmsdir . '404.php')) {
        header("Location: ./index.php");
        exit;
    } else {
        $GLOBALS['pag'] = "404";
    }
}

// Analizza i tag dal parametro URL
if (isset($_REQUEST['tag'])) {
    $GLOBALS['tag'] = explode("-", $_REQUEST['tag']);
}

// Includi il file di inizializzazione se esiste
if (file_exists("./hotcoffeecms-init.php")) {
    include "./hotcoffeecms-init.php";
}

// Funzione per ottenere l'indirizzo IP reale
function getRealIp() {
    $ip_keys = ["HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED", "HTTP_FORWARDED_FOR", "HTTP_FORWARDED", "REMOTE_ADDR"];
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            return trim($ip);
        }
    }
    return 'UNKNOWN';
}

$ip = getRealIp();

// Crea home.php di default se non esiste
if (!file_exists($cmsdir . "home.php")) {
    $defaultContent = '<?php echo "<h1>Hot Coffee is ready!</h1><p>New site coming soon...</p>"; ?>';
    file_put_contents($cmsdir . "home.php", $defaultContent);
}

// Includi CSS se esiste per la pagina
if (file_exists($cmsdir . $GLOBALS['pag'] . ".css")) {
    echo "<link rel='stylesheet' type='text/css' href='{$cmspath}{$GLOBALS['pag']}.css'>";
}

// Includi JS se esiste per la pagina
if (file_exists($cmsdir . $GLOBALS['pag'] . ".js")) {
    echo "<script src='{$cmspath}{$GLOBALS['pag']}.js' type='text/javascript'></script>";
}

// Pubblica i blocchi
if (isset($GLOBALS['cms'])) {
    if (is_array($GLOBALS['cms'])) {
        echo "<ul>";
        foreach ($GLOBALS['cms'] as $item) {
            if (file_exists($cmsdir . $item . ".php")) {
                echo $item === "home" 
                    ? "<li><a href='{$cmspath}'>{$item}</a></li>" 
                    : "<li><a href='{$cmspath}{$item}'>{$item}</a></li>";
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

// Pulizia delle variabili globali
$GLOBALS['cmssideview'] = false;
unset($GLOBALS['cms']);
?>
