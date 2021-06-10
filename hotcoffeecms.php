<?php
/* C 
	 M  	Hot Coffee CMS - Core
	 S
	[_])	https://github.com/ilbak/hotcoffeecms */




if (!stristr($_SERVER['SCRIPT_FILENAME'], "index.php"))  { echo "<script> location.href='index.php'</script>";  }
// Generic variables
if (!$cmspagina) { $cmspagina="0"; }


if ($_REQUEST['pag'] && file_exists(strtolower($_REQUEST['pag']).".php") ) {
	$GLOBALS['pag'] = strtolower($_REQUEST['pag']);
	} else {
	$GLOBALS['pag'] = "home";
		}


$cmsdir=str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME']);

$cmsissecure = false;
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $cmsissecure = true;
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $cmsissecure = true;
}
$REQUEST_PROTOCOL = $cmsissecure ? 'https' : 'http';
$cmsurl = $REQUEST_PROTOCOL . "://$_SERVER[HTTP_HOST]";

     if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $cmsip =  $_SERVER["HTTP_X_FORWARDED_FOR"];
     }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $cmsip = $_SERVER["REMOTE_ADDR"];
     }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            $cmsip = $_SERVER["HTTP_CLIENT_IP"];
     }



// Publish blocks
if(!function_exists('cmspubblica')){

if (file_exists("./{$GLOBALS['pag']}.css")) {
echo "<link rel='stylesheet' type='text/css' href='".$GLOBALS['pag'].".css'>";
}

if (file_exists("./{$GLOBALS['pag']}.js")) {
echo "<script language='JavaScript' type='text/JavaScript' src='".$GLOBALS['pag'].".js'></script>";
}


	function cmspubblica($cmsobj, $cmsdir, $cmspagina) {
	$cmsmenuarray=array();
	$cmsmenuarray2=0;

	$cmsjunkarray=array();
	$cmsjunk2=-1;
		if (is_dir($cmsdir)) {
			    if ($cmsjunk = opendir($cmsdir)) {
			        while (($cmsfile = readdir($cmsjunk)) !== false) {
   					if(!stristr($cmsfile,'-none.php') && !stristr($cmsfile,'hotcoffeecms.php') && !stristr($cmsfile,'index.php') && stristr($cmsfile,'.php')) {
					$cmsjunk2++;
					$cmsjunkarray[$cmsjunk2]=$cmsfile;
					}
       				}
		        closedir($cmsjunk);

			sort($cmsjunkarray);
			$cmsjunk2 = count($cmsjunkarray);
					echo "<ul>";

				for($cmsjunk3 = 0; $cmsjunk3 < $cmsjunk2; $cmsjunk3++) {

					if(stristr($cmsjunkarray[$cmsjunk3],'-sx.php') AND ($cmspagina=="1")){
					// Left Block
					echo "<li>";
					include $cmsdir.$cmsjunkarray[$cmsjunk3];
					echo "</li>";
					}

					if(stristr($cmsjunkarray[$cmsjunk3],'-dx.php') AND ($cmspagina=="3")){
					// Right Block
					echo "<li>";
					include $cmsdir.$cmsjunkarray[$cmsjunk3];
					echo "</li>";
					}


				if (($cmsobj==".php") AND ($cmspagina=="0")) {
					if(!stristr($cmsjunkarray[$cmsjunk3],'-dx.php') AND !stristr($cmsjunkarray[$cmsjunk3],'-sx.php') AND ($cmsjunkarray[$cmsjunk3]!='home.php') ){
					// Get menu items
					$cmsmenuarray[$cmsmenuarray2]=$cmsjunkarray[$cmsjunk3];
					$cmsmenuarray2++;
					}
				}
  			}
					echo "</ul>";

		if ($cmsmenuarray2>0) {
		// Publish menù
		sort($cmsmenuarray);
		array_unshift($cmsmenuarray, "home.php");

		$junkmenu6 = count($cmsmenuarray);
			for($junkmenu7 = 0; $junkmenu7 < $junkmenu6; $junkmenu7++) {
			echo "<li><a href='?pag=".str_replace(".php", "", $cmsmenuarray[$junkmenu7])."'>".str_replace(".php", "", $cmsmenuarray[$junkmenu7])."</a></li>";
				}
	  		}
		}
	}
}
}



// Switch depending on the part of the page to be processed

switch ($cmspagina) {
    case 0:
// Menù
$cmsobj=".php";
cmspubblica($cmsobj, $cmsdir, $cmspagina);
break;

    case 1:
// Left block
$cmsobj="-sx.php";
cmspubblica($cmsobj, $cmsdir, $cmspagina);
break;

    case 2:
// Page content
include $GLOBALS['pag'].".php";
break;

    case 3:
// Right block
$cmsobj="-dx.php";
cmspubblica($cmsobj, $cmsdir, $cmspagina);
break;
}

?>
