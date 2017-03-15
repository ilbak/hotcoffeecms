<?php
/*	 )  	                                     		    */
/*	 (  	Hot Coffee CMS                       		    */
/*	[_])	http://www.redrosesas.com/hotcoffeecms/	    */




if (!stristr($_SERVER['SCRIPT_FILENAME'], "index.php"))  { echo "<script> location.href='index.php'</script>";  }
// Generic variables
if ($cmspagina=="") { $cmspagina="0"; }
if ($_GET['pag']=="") { $pag=$_POST['pag']; } else { $pag=$_GET['pag']; }
if ((!file_exists($pag.".php")) or ($pag=="")) { $pag="Home"; }
$cmsdir=str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME']);
$cmsurl="http://".str_replace("index.php", "", $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']);




// Publish blocks
if(!function_exists('cmspubblica')){

	function cmspubblica($cmsobj, $cmsdir, $pag, $cmspagina) {
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
					if(!stristr($cmsjunkarray[$cmsjunk3],'-dx.php') AND !stristr($cmsjunkarray[$cmsjunk3],'-sx.php') AND ($cmsjunkarray[$cmsjunk3]!='Home.php') ){
					// Get menu items
					$cmsmenuarray[$cmsmenuarray2]=$cmsjunkarray[$cmsjunk3];
					$cmsmenuarray2++;
					}
				}
  			}
  
		if ($cmsmenuarray2>0) {
		// Publish menù
		sort($cmsmenuarray);
		array_unshift($cmsmenuarray, "Home.php");

		$junkmenu6 = count($cmsmenuarray);
			for($junkmenu7 = 0; $junkmenu7 < $junkmenu6; $junkmenu7++) {
			echo "<li><a href='index.php?pag=".str_replace(".php", "", $cmsmenuarray[$junkmenu7])."'>".str_replace(".php", "", $cmsmenuarray[$junkmenu7])."</a></li>";
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
cmspubblica($cmsobj, $cmsdir, $pag, $cmspagina);
break;

    case 1:
// Left block
$cmsobj="-sx.php";
cmspubblica($cmsobj, $cmsdir, $pag, $cmspagina);
break;

    case 2:
// Page content
include $pag.".php";
break;

    case 3:
// Right block
$cmsobj="-dx.php";
cmspubblica($cmsobj, $cmsdir, $pag, $cmspagina);
break;
}



?>
