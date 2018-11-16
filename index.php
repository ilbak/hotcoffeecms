<!DOCTYPE html>
<html lang="en">
<head>
<title>Hot Coffee CMS</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {
    box-sizing: border-box;
}

body {
  margin: 0;
}


/* Style the header */
.header {
    background-color: #f1f1f1;
    padding: 20px;
    text-align: center;
}

/* Style the top navigation bar */
.topnav {
    overflow: hidden;
    background-color: #2F2E2E;
}

/* Style the topnav links */
.topnav a {
    float: left;
    display: block;
    color: #f2f2f2;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
}

/* Change color on hover */
.topnav a:hover {
    background-color: #ddd;
    color: black;
}

.topnav ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
}


/* Side columns */
.side {
    float: left;
    width: 25%;
    padding: 15px;
    
}

/* Center column */
.content {
    float: left;
    width: 50%;
    padding: 15px;
    min-height: 600px;
    
}

.row:after {
    content: "";
    display: table;
    clear: both;
}

.footer {
    background-color: #f1f1f1;
    padding: 20px;
    text-align: center;
}


/* Responsive layout */
@media screen and (max-width:600px) {
    .side {
        width: 100%;
    }
    .content {
        width: 100%;
    }

}

.pageloader {
	position: fixed;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
	z-index: 9999;
	background: #ffffff;
	text-align: center;
}
.pageloader p {
    margin: 0;
    position: absolute;
    top: 50%;
    left: 50%;
    margin-right: -50%;
    transform: translate(-50%, -50%)
    }
</style>
<script  src="https://code.jquery.com/jquery-3.2.1.min.js"  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="  crossorigin="anonymous"></script>

<script language="JavaScript" type="text/javascript">
	window.onload = function(){ $('.pageloader').fadeOut(); }
	 setTimeout(function(){
		$('.pageloader').fadeOut(); 
     }, 4000);
</script>
    
    
</head>
<body>
	<div class="pageloader"><p>Loading...</p></div>

<div class="header">
  <h1>Your site</h1>
  <p>I'll tell you later...</p>
</div>

<div class="topnav">
<ul> <?$cmspagina=0; include "hotcoffeecms.php"; ?> </ul>
</div>

<div class="row">
  <div class="side">
<?$cmspagina=1; include "hotcoffeecms.php"; ?>
  </div>
  <div class="content">
<?$cmspagina=2; include "hotcoffeecms.php"; ?>
  </div>
  <div class="side">
<?$cmspagina=3; include "hotcoffeecms.php"; ?>
  </div>
</div>

<div class="footer">
  <h1>Footer</h1>
  <p>Legal stuff</p>
  <p><a href="https://github.com/ilbak/hotcoffeecms" target="_new"><img src="https://a.fsdn.com/allura/p/hotcoffeecms/icon?1509012589" title="Made with Hot Coffee CMS" alt="Made with Hot Coffee CMS"></a></p>
</div>

</body>
</html>
