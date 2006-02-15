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
class msg
{
      function message($message,$title,$stop)
      {
      global $go_api, $go_info, $s;

$msg = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>ISPConfig Startseite</title>
<meta http-equiv="Content-Type" content="text/html; charset='.$go_info["theme"]["charset"].'">
<link href="'.$go_info["server"]["server_url"].'/design/default/style.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#FFFFFF">
<table width="450" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" background="'.$go_info["server"]["server_url"].'/design/default/nav_hg.gif">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="4"><img src="'.$go_info["server"]["server_url"].'/design/default/nav_lft.gif"></td>
          <td background="'.$go_info["server"]["server_url"].'/design/default/nav_hg.gif"><b><font size="2" face="Verdana" color="#333333">&nbsp; '.$title.'</font></b></td>
          <td width="4"><img src="'.$go_info["server"]["server_url"].'/design/default/nav_rgt.gif"></td>
        </tr>
      </table>
      </td>
  </tr>
  <tr>
    <td width="2" bgcolor="#E0E0E0"><img src="'.$go_info["server"]["server_url"].'/design/default/x.gif" width="1" height="1"></td>
    <td bgcolor="#FFFFFF">
        <table><tr><td width="30">&nbsp;</td><td>
        <p align="center"><font size="2" face="Verdana"><br>
                        <br>
                        &nbsp;<br>
                        '.$message.'<br>
                        &nbsp;</font>
                <p>
        </td><td width="30">&nbsp;</td></tr></table>
        </td>
    <td width="2" bgcolor="#E0E0E0"><img src="'.$go_info["server"]["server_url"].'/design/default/x.gif" width="1" height="1"></td>
  </tr>
  <tr>
    <td height="2" colspan="3" bgcolor="#E0E0E0"><img src="'.$go_info["server"]["server_url"].'/design/default/x.gif" width="1" height="1"></td>
  </tr>
</table>
</body>

</html>';

    if($stop == 1) {
        die($msg);
    } else {
        echo($msg);
    }

      }


}
?>