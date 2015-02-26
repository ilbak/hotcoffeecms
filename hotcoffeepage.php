<?
/*	 )  	                                        */
/*	 (  	Hot Coffee Page                         */
/*	[_])	http://www.redrosesas.com/hotcoffeecms/ */



// Password author (change it!)
$pagepass="changeit";

if (!isset($cmspagina)) { die(); }

// If there is the related page, creates it
if (!file_exists($pag."-data-none.php")) {
	$pagejunk2=$pag."-data-none.php";
	$pagejunk5 = fopen($pagejunk2, "w");
	fwrite($pagejunk5, "!! Page under construction !!");
	fclose($pagejunk5);
}

$pagepass2 = $_POST['pagepass2'];
$pagepost = $_POST['pagepost'];
$pageedit = $_POST['pageedit'];
if ($_GET['pageobj']=="") { $pageobj=$_POST['pageobj']; } else { $pageobj=$_GET['pageobj']; } 


function errore($pag) {
	// Writes a log of the error
	$pagejunk1=time();
	$pagejunk2=$pag."errore-none.php";
	$pagejunk5 = fopen($pagejunk2, "w");
	fwrite($pagejunk5, "<?\n");
	fwrite($pagejunk5, "\$errip=\"".md5($_SERVER['REMOTE_ADDR'])."\";\n");
	fwrite($pagejunk5, "\$errtime=\"".$pagejunk1."\";\n");
	fwrite($pagejunk5, "?>");
	fclose($pagejunk5);
}

function errorever($pag) {
	if (file_exists($pag."errore-none.php")) {
		
		include $pag."errore-none.php";
		$pagejunk=time();
		$pagejunk2=$pagejunk-$errtime;
		if (($errip==md5($_SERVER['REMOTE_ADDR'])) && ($pagejunk2<=20)){
			echo "This IP is not authorized to operate. Please try again in a few seconds.";
			die(); 
		}
} } 

function erroreclean($pag) {
	if (file_exists($pag."errore-none.php")) {
		include $pag."errore-none.php";
		$pagejunk=time();
		$pagejunk2=$pagejunk-$errtime;
		if ($pagejunk2>20){
			unlink($pag."errore-none.php");
	}}
}


switch ($pageobj) {
case 0:
	include $pag."-data-none.php";
	echo "<div align=right><br><a href='index.php?pag=".$pag."&pageobj=1'>[  Edit  ]</a></div>";
	break;
	
case 1:
	// Modify
	
	// Verify
	errorever($pag);
	
	if ($pageedit!="1") {
		$pagejunk=fopen($pag."-data-none.php","r");
		$pagecontent=fread($pagejunk,filesize($pag."-data-none.php"));
		fclose($pagejunk);
		
		
		echo "<form method='post'><fieldset><legend>Edit page</legend>";
		echo "<input type='hidden' name='pageobj' value='1'>";
		echo "<input type='hidden' name='pageedit' value='1'>";
		echo "<input type='hidden' name='pag' value='".$pag."'>";
		echo "<textarea name='pagepost' rows=30 cols=50>".$pagecontent."</textarea><br />";
		echo "<br><br>Password: <input type='password' name='pagepass2' value='' size='15'><br/>";
		echo "<input type='reset' value='Reset'><input type='submit' value='OK'></center></fieldset></form>";
		
		echo "<div align=right><br><a href='index.php?pag=".$pag."'>[  Back  ]</a></div>";
		
		echo $pageobj;
		
	} else {
		// if "pageedit" is active changes the page
		if ($pagepass2==$pagepass) {
			// Verify pass
			$pagejunk2=$pag."-data-none.php";
			$pagejunk5 = fopen($pagejunk2, "w");
			fwrite($pagejunk5, $pagepost);
			fclose($pagejunk5);
			erroreclean($pag);
			echo "<br>The page has been modified!<br><br>";
			echo "<div align=right><br><a href='index.php?pag=".$pag."'>[  Back  ]</a></div>";
			
		} else {
			echo "<br>Password error!<br><br>";
			errore($pag);
			echo "<br><div align=right><br><a href='index.php?pag=".$pag."'>[  Back  ]</a></div>";
		}
	}
	
	break;
	
}
?>
