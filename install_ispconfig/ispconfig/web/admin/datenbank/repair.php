<?
/*
Copyright (c) 2005, projektfarm Gmbh, Till Brehm, Falko Timme
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice,
      this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice,
      this list of conditions and the following disclaimer in the documentation
      and/or other materials provided with the distribution.
    * Neither the name of ISPConfig nor the names of its contributors
      may be used to endorse or promote products derived from this software without
      specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
include("../../../lib/config.inc.php");
include("../../../lib/session.inc.php");

$go_api->auth->check_admin(0);

$tables = $go_api->db->getTables($go_info["server"]["db_name"]);
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>ISPConfig Startseite</title>
<meta http-equiv="Content-Type" content="text/html; charset='.$go_info["theme"]["charset"].'">
<link href="/design/default/style.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#FFFFFF">';
echo "<table>";
foreach($tables as $table) {

    //$go_api->db->query("LOCK TABLES $table READ");
    //$go_api->db->query("FLUSH TABLES");
    $response = $go_api->db->queryAllRecords("REPAIR TABLE $table");
    //$go_api->db->query("UNLOCK TABLES");
    foreach($response as $resp) {
        echo "<tr><td><b>".$go_api->lng("Reparatur").":</b> ".$resp["Table"]." </td><td><b>".$resp["Msg_type"].":</b> ".$resp["Msg_text"]."</td></tr>";
    }
}
echo "</table>";
echo '</body>

</html>';



?>