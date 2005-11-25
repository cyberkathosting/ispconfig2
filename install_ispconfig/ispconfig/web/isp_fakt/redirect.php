<?include("../lib/dbonly.inc.php");
$s = addslashes($s);
$conn = mysql_query("SELECT * from redirect where rsession = '$s'");
$DB = mysql_fetch_array($conn);
$link = $DB["link"];
mysql_query("DELETE from redirect where rsession = '$s'");
// header("Location: $link");
// exit;
?><html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta http-equiv="refresh" content="0;URL=<?echo $link?>">
<title>Link</title>
</head>

<body>

</body>

</html>