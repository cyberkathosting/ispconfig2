<?
include("../../../lib/config.inc.php");
include("../../../lib/session.inc.php");
$go_api->auth->check_admin(0);
$importfilter = escapeshellcmd($HTTP_POST_VARS["importfilter"]);
include_once("filter/$importfilter/felder.php");
?>