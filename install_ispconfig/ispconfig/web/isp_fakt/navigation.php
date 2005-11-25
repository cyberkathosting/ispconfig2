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
</head>

<body topmargin="0" leftmargin="0" bgcolor="<?echo $session_nav_hcolour?>" >

<?include("$navigation")?>

</body>

</html>