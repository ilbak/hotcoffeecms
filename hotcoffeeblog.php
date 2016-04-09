<?
/*	 )  	                                        */
/*	 (  	Hot Coffee Blog                         */
/*	[_])	http://www.redrosesas.com/hotcoffeecms/ */




/* Start Setup */

// Password for blog author (change it!)
$blogpass="change";
// Blog tags separated with a comma
$blogtag="news,life,events";
// Number of characters in the post preview
$bloganteprima=800;


// Message of the day
$blogmotd="";

// Code to embed in the separation before and after the posts
$blogpostbefore="";
$blogpostafter= "";

// Embed the end of a message (eg service comments)
$blogpostcommenti="";


// RSS feed Information
// Author mail
$blogmail="author@blog.site";
// Title
$blogfeedtitolo="RSS Feed: ".$cmsurl;
// Description
$blogfeeddesc="The ".$cmsurl." RSS Feed";

/* End Setup */




if (!isset($cmspagina)) { die(); }

function permalink($permajunk) {
// Permalink creation
	$permajunk = strtolower($permajunk);
	$permajunk = preg_replace("/[^0-9A-Za-z ]/", "", $permajunk);
	$permajunk = str_replace(" ", "-", $permajunk);
	$permajunk = str_replace('\"', ' ', $permajunk);
	$permajunk = str_replace('è', 'e', $permajunk);
	$permajunk = str_replace('ì', 'i', $permajunk);
	$permajunk = str_replace('ù', 'u', $permajunk);
	$permajunk = str_replace('ò', 'o', $permajunk);
	$permajunk = str_replace('à', 'a', $permajunk);

	while (strstr($permajunk, "--")) {
		$permajunk = preg_replace("/--/", "-", $permajunk);
	}
	return($permajunk);
}

function bbcode($var,$blogtag) {
	// Decodifica bbcode
	
	$search = array(
                "/\[center\](.*?)\[\/center\]/is", 
                "/\[left\](.*?)\[\/left\]/is", 
                "/\[right\](.*?)\[\/right\]/is", 
                "/\[justify\](.*?)\[\/justify\]/is", 
                "/\[b\](.*?)\[\/b\]/is", 
                "/\[i\](.*?)\[\/i\]/is", 
                "/\[u\](.*?)\[\/u\]/is", 
                "/\[del\](.*?)\[\/del\]/is", 
                "/\[img\](.*?)\[\/img\]/is", 
                "/\[url\](.*?)\[\/url\]/is",
                "/\[url\=(.*?)\](.*?)\[\/url\]/is",
                "/\[quote\](.*?)\[\/quote\]/is",
                "/\[pre\](.*?)\[\/pre\]/is",
                "/\[size\=(.*?)\](.*?)\[\/size\]/is",
                "/\[color\=(.*?)\](.*?)\[\/color\]/is",
                "/\[spoiler\](.*?)\[\/spoiler\]/is", 
                "/\[youtube\](.*?)\[\/youtube\]/is", 
                );
	$replace = array(
		'<div align=center>$1</div>',
		'<div align=left>$1</div>',
		'<div align=right>$1</div>',
		'<div style="text-align: justify;">$1</div>',
		'<strong>$1</strong>',
		"<em>$1</em>",
		"<u>$1</u>",
		"<del>$1</del>",
		'<img src="$1" />',
		'<a href="$1" target="_blank">$2</a>',
		'<a href="$1" target="_blank">$2</a>',
		'<blockquote>$1</blockquote>',
		'<pre>$1</pre>',
		'<font size="$1">$2</font>',
		'<font color="$1">$2</font>',
		'<span style="background:#000000">$1</span>',
		'<div align=center><a href="http://youtu.be/$1" target="_blank"><DIV STYLE="position:static; top:00px; left:00px; width:480px; height:250px; visibility:visible "><img src="http://img.youtube.com/vi/$1/0.jpg" border=2></DIV><DIV STYLE="position:static; top:00px; left:00px; width:400px; height:100px; visibility:visible"><h2>> Play</h2></DIV></a></div>',
		);
        
	while(preg_match("#\[quote\](.*?)\[\/quote\]#is", $var)!=0)
		$var = preg_replace("#\[quote\](.*?)\[\/quote\]#is", '<table border="1"><tr><td>$1</td></tr></table>', $var);
	$var = preg_replace($search, $replace, $var);
	
	// Hide the sign of the preview and tags
	$var = str_replace("||", "", $var);
	if ($blogtag!=""){
		$bbcodetagarray=explode(',', $blogtag);
		$bbcodejunk = count($bbcodetagarray);
		for($bbcodejunk2 = 0; $bbcodejunk2 < $bbcodejunk; $bbcodejunk2++) {
			$var = str_replace("#".$bbcodetagarray[$bbcodejunk2], "", $var);
		}
		
	} 
	
	return $var;
} 

function creafeed($pag, $cmsdir, $blogdir, $cmsurl, $blogmail, $blogfeedtitolo, $blogfeeddesc) {
	// Feed creation
	$blogjunk2=$pag."-rss.xml";
	$blogjunk5 = fopen($blogjunk2, "w");
	fwrite($blogjunk5, "<?xml version=\"1.0\"?>\n");
	fwrite($blogjunk5, "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\"> \n");
	fwrite($blogjunk5, "<channel>\n");
	fwrite($blogjunk5, "<title>".htmlentities($blogfeedtitolo)."</title>\n");
	fwrite($blogjunk5, "<link>".$cmsurl."</link>\n");
	fwrite($blogjunk5, "<description>".htmlentities($blogfeeddesc)."</description>\n");
	fwrite($blogjunk5, "<atom:link href=\"".$cmsurl.$blogjunk2."\" rel=\"self\" type=\"application/rss+xml\" /> \n");
	
	
	$blogjunkarray=array();
	$blogjunk2=-1;
	if (is_dir($cmsdir)) {
		if ($blogjunk = opendir($cmsdir)) {
		        while (($blogfile = readdir($blogjunk)) !== false) {
		        	if(stristr($blogfile,'-post-none.php') && (substr($blogfile, 0, strlen($pag)) == $pag ) ) {
		        		$blogjunk2++;
		        		$blogjunkarray[$blogjunk2]=$blogfile;
		        	}
		        }
		        closedir($blogjunk);
		        
		        rsort($blogjunkarray);
		        
		        $blogcount=1;
		        foreach ($blogjunkarray AS $blogjunk3) {
		        	if ($blogcount<14){ 
		        		$blogjunk4=explode('-', $blogjunk3);
		        		
		        		include $blogdir.$blogjunk3;
		        		$blogtitolo = stripslashes($blogtitolo);
		        		$blogpost = stripslashes($blogpost);
		        		
		        		fwrite($blogjunk5, "<item>\n");
		        		fwrite($blogjunk5, "<title>".htmlentities($blogtitolo)."</title>\n");
  				        $blogjunk7=str_replace("&", "&amp;", $cmsurl."index.php?pag=".$pag."&post=".$blogjunk4[1]."-".permalink($blogtitolo));
		        		fwrite($blogjunk5, "<link>".$blogjunk7."</link>\n");
		        		fwrite($blogjunk5, "<guid>".$blogjunk7."</guid>\n");
		        		fwrite($blogjunk5, "<description>".$cmsurl." - ".htmlentities($blogtitolo)."</description>\n");
		        		fwrite($blogjunk5, "<author>".$blogmail." (".$cmsurl.")</author>\n");
		        		fwrite($blogjunk5, "<updated>".date(DATE_ATOM, $blogjunk4[1])."</updated>\n");
		        		fwrite($blogjunk5, "</item>\n");
		        		$blogcount++;
		        	}
		        	
		        }
		}
	}
	
	fwrite($blogjunk5, "</channel>\n");
	fwrite($blogjunk5, "</rss>\n");
	fclose($blogjunk5);
	
}


function errore($pag) {
	//Scrive un log dell'errore
	$blogjunk1=time();
	$blogjunk2=$pag."-errore-none.php";
	$blogjunk5 = fopen($blogjunk2, "w");
	fwrite($blogjunk5, "<?\n");
	fwrite($blogjunk5, "\$errip=\"".md5($_SERVER['REMOTE_ADDR'])."\";\n");
	fwrite($blogjunk5, "\$errtime=\"".$blogjunk1."\";\n");
	fwrite($blogjunk5, "?>");
	fclose($blogjunk5);
}

function errorever($pag) {
	if (file_exists($pag."-errore-none.php")) {
		
		include $pag."-errore-none.php";
		$blogjunk=time();
		$blogjunk2=$blogjunk-$errtime;
		if (($errip==md5($_SERVER['REMOTE_ADDR'])) && ($blogjunk2<=20)){
			echo "This IP is not authorized to operate. Please try again in a few seconds.";
			die(); 
		}
	}
} 

function erroreclean($pag) {
	if (file_exists($pag."-errore-none.php")) {
		include $pag."-errore-none.php";
		$blogjunk=time();
		$blogjunk2=$blogjunk-$errtime;
		if ($blogjunk2>20){
			unlink($pag."-errore-none.php");
		}
	}
}

function sociallink($bloglink, $bloglinktitolo) {
	// Creation feature links to share on social networks
	global $cmsurl, $pag;
	$bloglink=urlencode($cmsurl."index.php?pag=".$pag."&post=".$bloglink)."-".permalink($bloglinktitolo);
	
	echo "<a href='http://www.facebook.com/sharer/sharer.php?u=".$bloglink."&amp;t=".$bloglinktitolo."' target='_blank' title='Share \"$bloglinktitolo\" on Facebook'>[f]</a>&nbsp;&nbsp;";
	echo "<a href='https://twitter.com/intent/tweet?text=".urlencode($bloglinktitolo)."&url=".$bloglink."' target='_blank' title='Share \"$bloglinktitolo\" on Twitter'>[t]</a>&nbsp;&nbsp;";
	echo "<a href='https://plus.google.com/share?url=".$bloglink."&t=".urlencode($bloglinktitolo)."' target='_blank' title='Share \"$bloglinktitolo\" on Google+'>[g+]</a>&nbsp;&nbsp;";
	echo "<a href='mailto:indirizzo@destinatario.it?subject=Post ".$bloglinktitolo."&body=Segnalazione post ".$bloglinktitolo.": ".$bloglink.".  ' title='Share \"$bloglinktitolo\" via e-mail'>[m]</a>&nbsp;&nbsp;";
	
}


// Generic variables
$blogmodpass = $_POST['blogmodpass'];
$blogmodtitolo = $_POST['blogmodtitolo'];
$blogmodpost = $_POST['blogmodpost'];
$blogedit=$_POST['blogedit'];
if ($_GET['blogobj']=="") { $blogobj=$_POST['blogobj']; } else { $blogobj=$_GET['blogobj']; }
if ($_GET['post']=="") { $post=$_POST['post']; } else { $post=$_GET['post']; }
if ($_GET['blogricerca']=="") { $blogricerca=$_POST['blogricerca']; } else { $blogricerca="#".$_GET['blogricerca']; }
if ($blogricerca!="") { $blogobj="6"; }

if (($blogobj=="") && ($post!="")) { $blogobj="1"; }
if ($blogobj=="") { $blogobj="0"; }
$blogpag=$_GET['blogpag'];
if ($blogpag=="") { $blogpag="10"; }


switch ($blogobj) {
case 0:
	
	// Posts list
	if ($blogmotd!="") {
		echo "".$blogmotd."<br><br>";
	}
	
	$blogjunkarray=array();
	$blogjunk2=-1;
	if (is_dir($cmsdir)) {
		if ($blogjunk = opendir($cmsdir)) {
			while (($blogfile = readdir($blogjunk)) !== false) {
				
				if(!stristr($blogfile,'-errore-none.php') && stristr($blogfile,'-post-none.php') && (substr($blogfile, 0, strlen($pag)) == $pag)) {
					$blogjunk2++;
					$blogjunkarray[$blogjunk2]=$blogfile;
				}
			}
			closedir($blogjunk);
			
			rsort($blogjunkarray);
			$blogjunk2 = count($blogjunkarray);
			$blogjunk3=($blogpag-11);
			
			while ($blogjunk3 < $blogpag) {
				$blogjunk3++;
				if (file_exists($blogdir.$blogjunkarray[$blogjunk3])) {
					
					// Time post elaboration
					$blogjunk4=explode('-', $blogjunkarray[$blogjunk3]);
					
					include $blogdir.$blogjunkarray[$blogjunk3];
					$blogtitolo = stripslashes($blogtitolo);
					$blogpost = stripslashes($blogpost);
					
					// Flag for preview or not
					$blogjunk5="1";
					
					if ($blogricerca!="") {
						// Watch post content
						if ( (!strstr($blogtitolo,$blogricerca)) AND (!strstr($blogpost,$blogricerca)) ) {
							$blogjunk5="0";
						}
					}
					
					if ($blogjunk5=="1") {
						if ($blogtitolo=="") { $blogtitolo="...."; }
						// Post preview
						$blogjunk6 = strpos($blogpost, "||");
						if ($blogjunk6 !== false) { $bloganteprima2=$blogjunk6; } else { $bloganteprima2=$bloganteprima; }
						echo $blogpostbefore;
						echo "<div align=center><h3><a href='index.php?pag=".$pag."&post=".$blogjunk4[1]."-".permalink($blogtitolo)."'><b>".$blogtitolo."</b></a></h3></div><br>".bbcode(substr($blogpost, 0, $bloganteprima2), $blogtag);
						if (strlen($blogpost) > $bloganteprima2) { echo "<br><a href='index.php?pag=".$pag."&post=".$blogjunk4[1]."-".permalink($blogtitolo)."'><i>[ More... ]</i></a>"; }
						echo "<div align=right>";
						// Social link
						$bloglink=$blogjunk4[1];
						$bloglinktitolo=$blogtitolo;
						
						sociallink($bloglink, $bloglinktitolo);
						echo "[".date("d/m/y",$blogjunk4[1]). "]</div>";
						echo $blogpostafter;
						
					}
				}
			}
		}
	}
	
	if ($blogpag > 10) { echo "<a href='index.php?pag=".$pag."&blogpag=".($blogpag-10)."'>&nbsp;&nbsp;&nbsp;&nbsp;[ Next ]&nbsp;&nbsp;&nbsp;&nbsp;</a>"; }
	if ($blogjunk2 > $blogpag) { echo "<a href='index.php?pag=".$pag."&blogpag=".($blogpag+10)."'>&nbsp;&nbsp;&nbsp;&nbsp;[ Prev ]&nbsp;&nbsp;&nbsp;&nbsp;</a><br>"; }
	echo "<br><hr>";
	
	echo "<form method='post'>";
	echo "Search on ".$pag.": <input type='text' name='blogricerca' value='' size='10'>";
	echo "<input type='submit' value='Cerca'></form>";
	if ($blogtag!="") {
		echo "Cerca per tag:&nbsp;&nbsp;";
		$blogtagarray= explode(",", $blogtag);
		$blogjunk6 = count($blogtagarray);
		for($blogjunk7 = 0; $blogjunk7 < $blogjunk6; $blogjunk7++) {
			echo "<a href='index.php?pag=".$pag."&blogricerca=".$blogtagarray[$blogjunk7]."'>#".$blogtagarray[$blogjunk7]."</a>&nbsp;&nbsp;";
		}
	}
	echo "<br><br>";
	echo "<a href='".$pag."-rss.xml' title='Feed RSS'>&nbsp;&nbsp;&nbsp;&nbsp;[ Feed RSS ]&nbsp;&nbsp;&nbsp;&nbsp;</a>";
	echo "<a href='index.php?pag=".$pag."&blogobj=2'>&nbsp;&nbsp;&nbsp;&nbsp;[ New post ]&nbsp;&nbsp;&nbsp;&nbsp;</a><br>";
	echo "<br><br>";
	
	break;
	
	
case 1:
	// Post view
	if (file_exists($pag."-".$post."-post-none.php")) {
		
		include $blogdir.$pag."-".$post."-post-none.php";
		$blogtitolo = stripslashes($blogtitolo);
		$blogpost = stripslashes($blogpost);
		
		echo $blogpostbefore;
		echo "<script type='text/javascript'>document.title='".$blogtitolo."';</script>";
		echo "<div align=center><h3><b>".$blogtitolo."</b></h3></div><br>".bbcode($blogpost, $blogtag);
		echo "<div align=right>";
		// Social link
		$bloglink=$post;
		$bloglinktitolo=$blogtitolo;
		sociallink($bloglink, $bloglinktitolo);
		echo "[".date("d/m/y",$post). "]</div>";
		echo $blogpostafter;
		
		echo "<br>";
		
		echo $blogpostcommenti;    
		
		
		echo "<br><br><a href='index.php?pag=".$pag."&blogobj=4&post=".$post."'>&nbsp;&nbsp;&nbsp;&nbsp;[ Edit ]&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='index.php?pag=".$pag."'>&nbsp;&nbsp;&nbsp;&nbsp;[ Back ]&nbsp;&nbsp;&nbsp;&nbsp;</a><br>";
		echo "<br>";
	} else {
		echo "<br>Post NON found!<br><br>";
		echo "<a href='index.php?pag=".$pag."'>&nbsp;&nbsp;&nbsp;&nbsp;[ Back ]&nbsp;&nbsp;&nbsp;&nbsp;</a><br>";
	}
	
	break;
	
case 2:
	
	// Verification
	errorever($pag);
	
	// New post
	if ($blogedit=="1"){
		
		if ($blogmodpass== $blogpass){
			
			if ($blogmodpost== ""){
				echo "<br>You can not publish a post empty!<br><br>";
				
			} else {
				// Write new post
				$blogmodpost = str_replace("\n", "<br />", $blogmodpost);
				// Resolve string invisibility
				$blogmodpost = str_replace("\\$", "\$", str_replace("\$", "$", $blogmodpost));
				$blogjunktime = time();
				$blogjunk2=$pag."-".$blogjunktime."-post-none.php";
				$blogjunk = fopen($blogjunk2, "w");
				
				$blogmodtitolo = addslashes($blogmodtitolo);
				$blogmodpost = addslashes($blogmodpost);
				
				fwrite($blogjunk, "<?\n");
				fwrite($blogjunk, "\$blogtitolo=\"".$blogmodtitolo."\";\n");
				fwrite($blogjunk, "\$blogpost=\"".$blogmodpost."\";\n");
				fwrite($blogjunk, "?>");
				fclose($blogjunk);
				creafeed($pag, $cmsdir, $blogdir, $cmsurl, $blogmail, $blogfeedtitolo, $blogfeeddesc);
				erroreclean($pag);
				echo "<br>Post creato con successo!<br><br>";
				echo "<script> location.href='index.php?pag=".$pag."'</script>";
			}
		} else {
			errore($pag);
			echo "<br>Password error!<br><br>";
		}
		echo "<a href='index.php?pag=".$pag."'>&nbsp;&nbsp;&nbsp;&nbsp;[ Back ]&nbsp;&nbsp;&nbsp;&nbsp;</a><br>";
		
		
	} else {
		// Write post form
		
		echo "<form method='post'><fieldset><legend>New post</legend>";
		echo "<input type='hidden' name='blogobj' value='4'>";
		echo "<input type='hidden' name='blogedit' value='1'>";
		echo "<input type='hidden' name='pag' value='.$pag.'>";
		echo "<center>Title:<br><input type='text' name='blogmodtitolo' value='....' size='40'><br/>";
		echo "Content:<br><textarea name='blogmodpost' rows=20 cols=40 id='mytextarea'>Content.</textarea>";

		echo "<br>Password: <input type='password' name='blogmodpass' value='' size='15'><br/>";
		echo "<input type='reset' value='Reset'><input type='submit' value='OK'></center></fieldset></form>";
		
	}
	
	
	break;
	
	
	
	
case 4:
	// Edit post
	
	// Verification
	errorever($pag);
	
	include $blogdir.$pag."-".$post."-post-none.php";
	$blogtitolo = stripslashes($blogtitolo);
	$blogpost = stripslashes($blogpost);
	
	if ($blogedit=="1"){
		
		if ($blogmodpass== $blogpass){
			
			if ($blogmodpost== ""){
				// Delete empty post
				unlink($blogdir.$pag."-".$post."-post-none.php");
				creafeed($pag, $cmsdir, $blogdir, $cmsurl, $blogmail, $blogfeedtitolo, $blogfeeddesc);
				erroreclean($pag);
				echo "<br>Post deleted!<br><br>";
				echo "<script> location.href='index.php?pag=".$pag."'</script>";
			} else {
				// Save the post modification
				
				$blogmodpost = str_replace("\n", "<br />", $blogmodpost);
				// Resolve string inisibility
				$blogmodpost = str_replace("\\$", "\$", str_replace("\$", "$", $blogmodpost));
				$blogjunk2=$pag."-".$post."-post-none.php";
				$blogjunk = fopen($blogjunk2, "w");
				$blogmodtitolo = addslashes($blogmodtitolo);
				$blogmodpost = addslashes($blogmodpost);
				fwrite($blogjunk, "<?\n");
				fwrite($blogjunk, "\$blogtitolo=\"".$blogmodtitolo."\";\n");
				fwrite($blogjunk, "\$blogpost=\"".$blogmodpost."\";\n");
				fwrite($blogjunk, "?>");
				fclose($blogjunk);
				creafeed($pag, $cmsdir, $blogdir, $cmsurl, $blogmail, $blogfeedtitolo, $blogfeeddesc);
				erroreclean($pag);
				echo "<br>Post modified!<br><br>";
				echo "<script> location.href='index.php?pag=".$pag."&post=".$post."'</script>";
			}
		} else {
			errore($pag);
			echo "<br>Password error!<br><br>";
		}
		echo "<a href='index.php?pag=".$pag."'>&nbsp;&nbsp;&nbsp;&nbsp;[ Back ]&nbsp;&nbsp;&nbsp;&nbsp;</a><br>";
		
		
	} else {
		$blogpost = str_replace("<br />", "\n", $blogpost);
		
		echo "<form method='post'><fieldset><legend>Edit</legend>";
		echo "<input type='hidden' name='blogobj' value='4'>";
		echo "<input type='hidden' name='blogedit' value='1'>";
		echo "<input type='hidden' name='pag' value='.$pag.'>";
		echo "<input type='hidden' name='post' value='.$post.'>";
		echo "<center>Titolo:<br><input type='text' name='blogmodtitolo' value='".stripslashes($blogtitolo)."' size='40'><br/>";
		echo "Post:<br><textarea name='blogmodpost' rows=20 cols=40 id='mytextarea'>".stripslashes($blogpost)."</textarea>";
		
		echo "<br>Password: <input type='password' name='blogmodpass' value='' size='15'><br/>";
		echo "<input type='reset' value='Reset'><input type='submit' value='OK'></center></fieldset></form>";
		
		
	}
	
	break;
	
	
	
case 6:
	// Search
	if ($blogricerca=="") {echo "None search"; die(); }
	
	echo "<h2>Search of: ".$blogricerca."</h2><br><a href='index.php?pag=".$pag."'>&nbsp;&nbsp;&nbsp;&nbsp;[ Back ]&nbsp;&nbsp;&nbsp;&nbsp;</a><br><br><br>";
	
	$blogjunkarray=array();
	$blogjunk2=-1;
	if (is_dir($cmsdir)) {
		if ($blogjunk = opendir($cmsdir)) {
			while (($blogfile = readdir($blogjunk)) !== false) {
				
				if(stristr($blogfile,'-post-none.php') && (substr($blogfile, 0, strlen($pag)) == $pag)) {
					$blogjunk2++;
					$blogjunkarray[$blogjunk2]=$blogfile;
				}
			}
			closedir($blogjunk);
			
			rsort($blogjunkarray);
			$blogjunk2 = count($blogjunkarray);
			$blogjunk3=0;
			
			while (file_exists($blogdir.$blogjunkarray[$blogjunk3])) {
				
				// Elaborate post time
				$blogjunk4=explode('-', $blogjunkarray[$blogjunk3]);
				
				include $blogdir.$blogjunkarray[$blogjunk3];
				$blogtitolo = stripslashes($blogtitolo);
				$blogpost = stripslashes($blogpost);
				
				// Watch posts content
				if ( (strstr($blogtitolo,$blogricerca)) OR (strstr($blogpost,$blogricerca)) ) {
					if ($blogtitolo=="") { $blogtitolo="...."; }
					//Anteprima del post
					$blogjunk6 = strpos($blogpost, "||");
					if ($blogjunk6 !== false) { $bloganteprima2=$blogjunk6; } else { $bloganteprima2=$bloganteprima; }
					echo $blogpostbefore;
					echo "<div align=center><h3><a href='index.php?pag=".$pag."&post=".$blogjunk4[1]."'><b>".$blogtitolo."</b></a></h3></div><br>".bbcode(substr($blogpost, 0, $bloganteprima2), $blogtag);
					if (strlen($blogpost) > $bloganteprima2) { echo "<br><a href='index.php?pag=".$pag."&post=".$blogjunk4[1]."'><i>[ Continua... ]</i></a>"; }
					echo "<div align=right>";
					// Social link
					$bloglink=$blogjunk4[1];
					$bloglinktitolo=$blogtitolo;
					sociallink($bloglink, $bloglinktitolo);
					echo "[".date("d/m/y",$blogjunk4[1]). "]</div>";
					echo $blogpostafter;
					
					
				}
				$blogjunk3++;
			} 
	}}
	
	echo "<a href='index.php?pag=".$pag."'>&nbsp;&nbsp;&nbsp;&nbsp;[ Back ]&nbsp;&nbsp;&nbsp;&nbsp;</a><br>";
	
	break;
	
}
?>
  <script src='http://cdn.tinymce.com/4/tinymce.min.js'></script>
  <script>
  tinymce.init({
    selector: '#mytextarea',
 height: 500,
 plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste imagetools"
    ],
  toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | link image',
  });
  </script>
