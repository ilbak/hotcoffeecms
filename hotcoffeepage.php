<?
/*	 C 
	 M  	Hot Coffee CMS - Page
	 S 
	[_])	https://github.com/ilbak/hotcoffeecms */




// Password author (change it!)
$pagepass="changeit";




// If there is the related page, creates it
if (!file_exists($GLOBALS['pag']."-data.php")) {
	$pagejunk2=$GLOBALS['pag']."-data.php";
	$pagejunk5 = fopen($pagejunk2, "w");
	fwrite($pagejunk5, "!! Page under construction !!");
	fclose($pagejunk5);
}

$pageobj = $_REQUEST['pageobj'];
$pagepass2 = $_REQUEST['pagepass2'];
$pageedit = $_REQUEST['pageedit'];
$pagepost = $_REQUEST['pagepost'];

switch ($pageobj) {
default:
case 0:
echo "<section id='contatti' class='section'>
            <div class='container'>";
            
	include $GLOBALS['pag']."-data.php";

echo "</section>
            </div>";


	echo "<div align=right><br><a href='index.php?pag=".$GLOBALS['pag']."&pageobj=1'>[  Edit  ]</a></div>";
	break;
	
case 1:
	// Modify
	
	if ($pageedit!="1") {
		$pagejunk=fopen($GLOBALS['pag']."-data.php","r");
		$pagecontent=fread($pagejunk,filesize($GLOBALS['pag']."-data.php"));
		fclose($pagejunk);
		
		
		echo "<form method='post'><fieldset><legend>Edit page</legend>";
		echo "<input type='hidden' name='pageobj' value='1'>";
		echo "<input type='hidden' name='pageedit' value='1'>";
		echo "<input type='hidden' name='pag' value='".$GLOBALS['pag']."'>";
		echo "<textarea name='pagepost' rows=30 cols=50  id='mytextarea'>".$pagecontent."</textarea><br />";
		echo "<br><br>Password: <input type='password' name='pagepass2' value='' size='15'><br/>";
		echo "<input type='reset' value='Reset'><input type='submit' value='OK'></center></fieldset></form>";
		
		echo "<div align=right><br><a href='index.php?pag=".$GLOBALS['pag']."'>[  Back  ]</a></div>";
		
		echo $pageobj;
		
	} else {
		// if "pageedit" is active changes the page
		if ($pagepass2==$pagepass) {
			// Verify pass
			$pagejunk2=$GLOBALS['pag']."-data.php";
			$pagejunk5 = fopen($pagejunk2, "w");
			fwrite($pagejunk5, $pagepost);
			fclose($pagejunk5);
			echo "<br>The page has been modified!<br><br>";
			echo "<div align=right><br><a href='index.php?pag=".$GLOBALS['pag']."'>[  Back  ]</a></div>";
		} else {
			echo "<br>Password error!<br><br>";
			echo "<br><div align=right><br><a href='index.php?pag=".$GLOBALS['pag']."'>[  Back  ]</a></div>";
		}
	}
	
	break;
	
}
?>
