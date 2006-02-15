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
session_start();
include("../lib/config.inc.php");
require_once("login/lib/lang/".$go_info["server"]["lang"].".lng");
$err = intval($_GET["err"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>ISPConfig - Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $go_info["theme"]["charset"]; ?>">
<link href="design/default/style.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<script language= "JavaScript">
  <!--Break out of frames
    if (top.frames.length > 0)
    top.location=self.document.location;
  //-->
</script>
<form method="POST" action="login/login.php" name="loginForm">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="middle"><table width="400" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
      <tr>
        <td><img src="<? $reseller = $_REQUEST["reseller"];
                if(!preg_match("/^[a-zA-Z0-9_\-]{0,50}$/",$reseller)) die("The 'reseller' variable contains invalid chars. Allowed: 'a-z A-Z 0-9 _ -'. Max. Length: 50 chars.");
                $reseller = escapeshellcmd($reseller);
                $reseller = str_replace("/","",$reseller);
                $_SESSION["reseller_image_id"] = $reseller;
                echo ($reseller != '' && @is_file('design/reseller_images/login_'.$reseller.'.png'))?'design/reseller_images/login_'.$reseller.'.png':'design/default/images/login_logo.png';?>" width="398" height="78"></td>
      </tr>
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="0" cellpadding="5">
          <tr align="center">
            <td height="66" colspan="2" class="normal">
                        <?php
            if($err > 0){
                if($err == '101') echo "<b>".$wb["FEHLER 101: Username oder Passwort Falsch."]."</b>";
                if($err == '102') echo "<b>".$wb["FEHLER 102: Account inaktiv."]."</b>";
                if($err == '103') echo "<b>".$wb["FEHLER 103: Es wurde kein Username oder Passwort angegeben."]."</b>";
                if($err == '104') echo "<b>".$wb["FEHLER 104: Die Nutzersession wurde beendet."]."</b>";
                                if($err == '999') echo "<b>".$wb["Ihre Nutzersitzung wurde beendet."]."</b>";
                echo $wb["<br>&nbsp;<br>Hier können Sie sich erneut einloggen:"];
            } else {
                echo $wb["Hier können Sie sich einloggen:"];
            }
            ?>
                        </td>
            </tr>
          <tr>
            <td width="35%" align="left" class="normal">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $wb["Username"]; ?>:</td>
            <td width="65%"><input type="text" name="username" class="text"></td>
          </tr>
          <tr>
            <td align="left" class="normal">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $wb["Passwort"]; ?>:</td>
            <td><input type="password" name="passwort" class="text"></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="B1" value="  Login  " class="button"></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
    </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>
</body>
</html>