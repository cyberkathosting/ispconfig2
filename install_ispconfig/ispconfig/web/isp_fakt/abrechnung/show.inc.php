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

//include("../../../lib/config.inc.php");
//include("../../../lib/session.inc.php");



//die(print_r($rechnung));


// daten anzeigen
/*
foreach($rechnung as $web_id => $records) {

        // Hole Web-Daten
        //$web =
        foreach($records as $record) {
                $artikel_id = $record["artikel_id"];
                echo " - Typ: ".$record["typ"]." Artikel: ".$artikel[$artikel_id]["artikelbezeichnung"]." : ".$record["notiz"]." Preis: ".$artikel[$artikel_id]["artikelpreis"]." EUR<br>";


        }
}

                        $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["anzahl"] = $record["anzahl"];
                    $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["type"] = $artikel[$artikel_id]["artikeltyp"];
                        $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["nummer"] = $artikel[$artikel_id]["artikelnummer"];
                    $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["name"] = $artikel[$artikel_id]["artikelbezeichnung"];
                    $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["text"] = $artikel[$artikel_id]["beschreibung"];
                    $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["preis"] = $artikel[$artikel_id]["artikelpreis"];
                    $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["vat"] = $artikel[$artikel_id]["steuersatz"];

*/

//$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["vat"]

?>
<html>
<head>
<title>Rechnung</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo $go_info["theme"]["charset"]; ?>">
<link href="../../design/default/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="rechnung_erstellen.php">
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <?
  foreach($rechnung as $reseller_id => $tmp_kunden) {
  $reseller_preis_gesamt = 0;
          if($reseller_id > 0) {
                  $reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller WHERE doc_id = $reseller_id");
                $reseller_name = $reseller["firma"]." - ".$reseller["vorname"]." ".$reseller["name"];
        } else {
                $reseller_name = "Administrator";
        }

  ?>
  <tr>
    <td colspan="6" bgcolor="#CCCCCC" class="t2b"><input type="checkbox" name="frm_reseller[<? echo $reseller_id?>]" value="1" checked> <img src="../../design/default/icons/group.gif" width="16" height="16">
        <? echo $reseller_name ?></td>
  </tr>
  <?
  foreach($tmp_kunden as $kunde_id => $tmp_webs) {
  $kunde_preis_gesamt = 0;
  $kunde = $go_api->db->queryOneRecord("SELECT * from isp_isp_kunde WHERE doc_id = $kunde_id");
  $kunde_name = $kunde["kunde_firma"]." - ".$kunde["kunde_vorname"]." ".$kunde["kunde_name"];
  ?>
  <tr>
    <td width="20" bgcolor="#CCCCCC">&nbsp;</td>
    <td colspan="5" bgcolor="#EEEEEE" class="t2b"><input type="checkbox" name="frm_kunde[<? echo $kunde_id?>]" value="1" checked>
      <img src="../../design/default/icons/user.gif" width="16" height="16"> <? echo $kunde_name ?></td>
    </tr>
        <?
        foreach($tmp_webs as $web_id => $tmp_records) {
        $web_preis_gesamt = 0;
        $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web WHERE doc_id = $web_id");
          $web_name = $web["web_host"].".".$web["web_domain"];
        ?>
  <tr>
    <td bgcolor="#CCCCCC">&nbsp;</td>
    <td width="25" bgcolor="#EEEEEE">&nbsp;</td>
    <td colspan="4" bgcolor="#FEFEFE" class="t2b"><input type="checkbox" name="frm_web[<? echo $web_id?>]" value="1" checked>
      <input name="imageField" type="image" src="../../design/default/icons/globus.gif" width="16" height="16" border="0">
      <? echo $web_name?></td>
    </tr>
        <?
        foreach($tmp_records as $record_id => $record) {
        ?>
  <tr>
    <td bgcolor="#CCCCCC">&nbsp;</td>
    <td bgcolor="#EEEEEE">&nbsp;</td>
    <td width="33" bgcolor="#FEFEFE">&nbsp;</td>
    <td width="24" bgcolor="#FEFEFE" class="t2b"><input type="checkbox" name="frm_record[<? echo $record_id?>]" value="1" checked></td>
    <td bgcolor="#FEFEFE" class="t2b"><? echo $record["name"]?></td>
    <td align="right" width="150" bgcolor="#FEFEFE" class="t2"><? echo $record["anzahl"]?> x <? echo number_format($record["preis"], 2, $go_info["localisation"]["dec_point"], $go_info["localisation"]["thousands_sep"]); ?> <? echo $go_info["localisation"]["currency"]; ?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC">&nbsp;</td>
    <td bgcolor="#EEEEEE">&nbsp;</td>
    <td bgcolor="#FEFEFE">&nbsp;</td>
    <td bgcolor="#FEFEFE" class="t2">&nbsp;</td>
    <td bgcolor="#FEFEFE" class="t2"><? echo nl2br($record["text"])?></td>
    <td bgcolor="#FEFEFE">&nbsp;</td>
  </tr>
  <?
  $web_preis_gesamt += $record["preis"]*$record["anzahl"];
  // Ende Record
  }
  ?>
  <tr>
    <td bgcolor="#CCCCCC">&nbsp;</td>
    <td bgcolor="#EEEEEE">&nbsp;</td>
    <td colspan="4" bgcolor="#FEFEFE"><hr size="1" noshade></td>
    </tr>
  <tr class="t2b">
    <td bgcolor="#CCCCCC">&nbsp;</td>
    <td bgcolor="#EEEEEE">&nbsp;</td>
    <td bgcolor="#FEFEFE">&nbsp;</td>
    <td colspan="2" align="right" bgcolor="#FEFEFE"><? echo $go_api->lng("Summe Web")?>:</td>
    <td align="right" bgcolor="#FEFEFE"><? echo number_format($web_preis_gesamt, 2, $go_info["localisation"]["dec_point"], $go_info["localisation"]["thousands_sep"])." ".$go_info["localisation"]["currency"]; ?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC">&nbsp;</td>
    <td bgcolor="#EEEEEE">&nbsp;</td>
    <td colspan="4" bgcolor="#FEFEFE"><hr size="1" noshade></td>
    </tr>
  <?
  $kunde_preis_gesamt += $web_preis_gesamt;
  // Ende Web
  }
  ?>
  <tr>
    <td bgcolor="#CCCCCC">&nbsp;</td>
    <td colspan="5" align="right" bgcolor="#EEEEEE"><hr size="1" noshade></td>
    </tr>
  <tr class="t2b">
    <td bgcolor="#CCCCCC">&nbsp;</td>
    <td bgcolor="#EEEEEE">&nbsp;</td>
    <td bgcolor="#EEEEEE">&nbsp;</td>
    <td colspan="2" align="right" bgcolor="#EEEEEE"><? echo $go_api->lng("Summe Kunde")?>:</td>
    <td align="right" bgcolor="#EEEEEE"><? echo number_format($kunde_preis_gesamt, 2, $go_info["localisation"]["dec_point"], $go_info["localisation"]["thousands_sep"])." ".$go_info["localisation"]["currency"]; ?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC">&nbsp;</td>
    <td colspan="5" align="right" bgcolor="#EEEEEE"><hr size="1" noshade></td>
    </tr>
  <?
  $reseller_preis_gesamt += $kunde_preis_gesamt;
  // Ende Kunde
  }
  ?>
  <tr bgcolor="#CCCCCC">
    <td colspan="6"><hr size="1" noshade></td>
    </tr>
  <tr bgcolor="#CCCCCC" class="t2b">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2" align="right"><? echo $go_api->lng("Summe Anbieter")?>:</td>
    <td align="right"><? echo number_format($reseller_preis_gesamt, 2, $go_info["localisation"]["dec_point"], $go_info["localisation"]["thousands_sep"])." ".$go_info["localisation"]["currency"]; ?></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td colspan="6"><hr size="1" noshade></td>
    </tr>
  <?
  // Ende Reseller
  }
  ?>
  <tr bgcolor="#CCCCCC">
    <td colspan="6" align="center" height="50">
                <input type="submit" name="speichern" value=" <? echo $go_api->lng("Weiter")?> >> " class="button">
                <input type="hidden" name="s" value="<? echo $s?>">
        </td>
  </tr>
</table>
</form>
</body>
</html>