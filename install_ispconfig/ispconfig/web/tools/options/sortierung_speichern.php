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
include("../../lib/config.inc.php");
include("../../lib/session.inc.php");
if ($sort == "on" and $go_api->auth->check_write($groupid)):
mysql_query("UPDATE user_account SET bookmark_order = 'j' where userid = '$userid'");
$go_info["modul"]["item_order"] = "j";
$go_api->session->save();
// mysql_query("UPDATE session SET bookmark_order = 'j' where userid = '$userid'");
endif;
if ($sort == "off" and $go_api->auth->check_write($groupid)):
mysql_query("UPDATE user_account SET bookmark_order = 'n' where userid = '$userid'");
$go_info["modul"]["item_order"] = "n";
$go_api->session->save();
// mysql_query("UPDATE session SET bookmark_order = 'n' where userid = '$userid'");
endif;

header("Location: ../index.php?$session");

?>
<html>

<head>
<meta http-equiv="Content-Language" content="de">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $go_info["theme"]["charset"]; ?>">
<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
<meta name="ProgId" content="FrontPage.Editor.Document">
<title></title>
<base target="_top">

<STYLE type='text/css'>
<!--
<?include("../$session_style_path/style.css.php")?>
// -->
</STYLE>

</head>

<body bgcolor="<?echo $session_bgcolour?>">

    <div align="center">
      <center>

    <table border="0" width="466" height="146" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%" height="8">
          <p align="center" class="heading">myBookmark Optionen</td>
      </tr>
      <tr>
        <td width="100%" height="33">
          <p align="center" class="normal_bold">&nbsp; <a href="../index.php?<?echo $session?>" target="_top">Ansicht
          aktualisieren</a></p>
        </td>
      </tr>
    </table>
      </center>
    </div>
</body>

</html>