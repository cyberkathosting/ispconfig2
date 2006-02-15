<?php
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

$go_api->uses("doc");

$tree_id = intval($_REQUEST["tree_id"]);
$doc_id = intval($_REQUEST["doc_id"]);
$doctype_id = intval($_REQUEST["doctype_id"]);
$gid = intval($_REQUEST["gid"]);
$userid = intval($_REQUEST["userid"]);

if(!empty($doctype_id)) {
        $doctype_id = intval($doctype_id);
        $doc = $go_api->doc->doctype_get($doctype_id);
        $message = $go_api->lng($doc->deck[$deck_id]->elements[$element_id]->description);

        if(trim($message) == '') {
                // Hole Help File, wenn vorhanden
                $hlp_file = $go_info["server"]["include_root"] . $go_info["server"]["dir_trenner"] ."help".$go_info["server"]["dir_trenner"].$go_api->language."_".$doctype_id.".hlp";
                if(is_file($hlp_file)) include_once($hlp_file);
                $element_name = $doc->deck[$deck_id]->elements[$element_id]->name;
                $message = nl2br($hlp[$element_name]);
        }

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>ISPConfig</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $go_info["theme"]["charset"]; ?>">
<link href="../../design/default/style.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="300" border="0" cellspacing="0" cellpadding="0">
  <tr bgcolor="<? echo $session_nav_hcolour ?>">
    <td height="24" colspan="3"><div align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $go_api->lng("Online Hilfe")?></font></div></td>
  </tr>
  <tr>
    <td width="19">&nbsp;</td>
    <td width="265" class="normal">&nbsp;<br><? echo $message?></td>
    <td width="16">&nbsp;</td>
  </tr>
  <tr>
    <td height="27">&nbsp;</td>
    <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="javascript:window.close()"><? echo $go_api->lng("schliessen")?></a></font></td>
    <td>&nbsp;</td>
  </tr>
</table>

</body>
</html>