<?include("../../lib/config.inc.php");
include("../../lib/session.inc.php");
$navigation = "../" . $session_design_path . "/nav.inc.php";?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link href="../design/default/style.css" rel="stylesheet" type="text/css">
<title>nav frame</title>

<STYLE type='text/css'>
<!--
<?include("../$session_style_path/style.css.php")?>
// -->
</STYLE>
<?if($go_info["server"]["ort"] != "local"){?>
<script language="JavaScript">
<!--
var bannerid;

bannerid = Math.round(Math.random()*1000) + 1;

function bannershow() 

{
document.banner.src = "http://kansas.valueclick.com/cycle?host=hs0195481&size=468x60&b=index&noscript=1";
setTimeout('bannershow()', 50000);
}

function bannerclick() 

{
open("http://kansas.valueclick.com/redirect?host=hs0195481&size=468x60&b=index&v=0");
}
-->
</script>
<?}?>
</head>

<body topmargin="0" leftmargin="0" bgcolor="<?echo $session_nav_hcolour?>" <?if($go_info["server"]["ort"] != "local") echo"onload=\"bannershow()\""?>>

<?include("$navigation")?>

</body>

</html>