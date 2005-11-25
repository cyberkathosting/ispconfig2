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

if($go_info["user"]["userid"] != 1) $go_api->errorMessage($go_api->lng("Nur für Admin verfügbar.")); 

$rechnung_id = intval($HTTP_GET_VARS["rechnung_id"]);
$rg_record = $go_api->db->queryOneRecord("SELECT * from isp_fakt_rechnung where rechnung_id = $rechnung_id");

$rechnung = unserialize(stripslashes($rg_record["data"]));
$reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where doc_id = ".$rg_record["reseller_id"]);

// Kunden + Reseller nr von:
$sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
$gpreis = 0;
$mwst_gesamt = 0;
/*
echo "<pre>";
die(print_r($rechnung));
echo "</pre>";
*/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../design/default/style.css" rel="stylesheet" type="text/css">
</head>

<body>                    <table width="600" border="0" cellspacing="0" cellpadding="2">
                      <tr class="normal_bold">
                        <td>&nbsp;</td>
                        <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="54%">
					<table width="100%" border="0" cellspacing="0" cellpadding="2">
					  <tr>
                        <td width="1%">&nbsp;</td>
                        <td width="23%" class="normal_bold"><? echo $go_api->lng("Firma")?>: </td>
                        <td width="76%" class="normal"><? echo $reseller["firma"];?></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td class="normal_bold"><? echo $go_api->lng("Vorname")?>:</td>
                        <td class="normal"><? echo $reseller["vorname"];?></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td class="normal_bold"><? echo $go_api->lng("Name")?>:</td>
                        <td class="normal"><? echo $reseller["name"];?></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td class="normal_bold"><? echo $go_api->lng("Strasse")?>:</td>
                        <td class="normal"><? echo $reseller["strasse"];?></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td class="normal_bold"><? echo $go_api->lng("PLZ")?>:</td>
                        <td class="normal"><? echo $reseller["plz"];?></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td class="normal_bold"><? echo $go_api->lng("Ort")?>:</td>
                        <td class="normal"><? echo $reseller["ort"];?></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td class="normal_bold"><? echo $go_api->lng("Land")?>:</td>
                        <td class="normal"><? echo $reseller["land"];?></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td class="normal_bold"><? echo $go_api->lng("Steuernr.")?>:</td>
                        <td class="normal"><? echo $reseller["steuernr"];?></td>
                      </tr>
                      </table>
							</td>
                            <td width="46%" valign="bottom"><table width="100%" border="0" cellspacing="0" cellpadding="2">
                              <tr align="right" valign="top">
                                <td height="135" colspan="2" class="normal_bold"><font size="6"><? echo $go_api->lng("Rechnung")?></font></td>
                              </tr>
                              <tr>
                                <td class="normal_bold"><? echo $go_api->lng("Datum")?>:</td>
                                <td class="normal"><? echo date("d.m.Y",$rg_record["datum"])?></td>
                              </tr>
                              <tr>
                                <td width="53%" class="normal_bold"><? echo $go_api->lng("Rechnung Nr.")?></td>
                                <td width="47%" class="normal"><? echo $sys_config["rechnung_nr_start"] + $rg_record["rechnung_id"]?></td>
                              </tr>
                            </table></td>
                          </tr>
                        </table></td>
                      </tr>
                      <tr class="normal_bold">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td width="528">&nbsp;</td>
                      </tr>
					  <?
					  
					  foreach($rechnung as $kunde) {
					  foreach($kunde as $web_id => $web) {
					  $web_gpreis = 0;
					  $web_record = $go_api->db->queryOneRecord("SELECT * from isp_isp_web where doc_id = $web_id");
					  ?>
					  <tr>
                        <td width="2" height="23" bgcolor="#CCCCCC" class="normal_bold">&nbsp;</td>
                        <td width="58" bgcolor="#CCCCCC" class="normal_bold"><? echo $go_api->lng("Web")?>:</td>
                        <td bgcolor="#CCCCCC" class="normal"><? echo $web_record["web_host"].".".$web_record["web_domain"]?></td>
                      </tr>
                      <tr class="normal_bold">
                        <td height="23">&nbsp;</td>
                        <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2">
                          <tr>
                            <td width="10%" class="normal_bold"><? echo $go_api->lng("Anzahl")?></td>
                            <td width="14%" class="normal_bold"><? echo $go_api->lng("Art. Nr.")?> </td>
                            <td width="50%" class="normal_bold"><? echo $go_api->lng("Artikel")?></td>
                            <td width="12%" align="right" class="normal_bold"><? echo $go_api->lng("Preis")?></td>
                            <td width="14%" align="right" class="normal_bold"><? echo $go_api->lng("Gesamt")?></td>
                          </tr>
						  <?
						  foreach($web as $record) {
						  ?>
						  <tr>
                            <td class="normal"><? echo $record["anzahl"]?></td>
                            <td class="normal"><? echo $record["nummer"]?></td>
                            <td class="normal"><? echo $record["name"]?></td>
                            <td align="right" valign="baseline" class="normal"><? echo sprintf("%01.2f", $record["preis"])?> <? echo $go_api->lng("EURO")?></td>
                            <td align="right" valign="baseline" class="normal"><? echo sprintf("%01.2f", $record["preis"] * $record["anzahl"])?> <? echo $go_api->lng("EURO")?></td>
                          </tr>
						  <tr>
                            <td class="normal">&nbsp;</td>
							<td class="normal">&nbsp;</td>
                            <td class="normal"><? echo $record["text"]?></td>
                            <td class="normal">&nbsp;</td>
                            <td class="normal">&nbsp;</td>
                          </tr>
						  <?
						  $mwst_gesamt += $record["preis"] * $record["anzahl"] * (intval($record["vat"]) / 100);
						  $web_gpreis += $record["preis"] * $record["anzahl"];
						  }
						  ?>
                          <tr>
                            <td colspan="5"><hr size="1" noshade></td>
                          </tr>
                          <tr align="right">
                            <td colspan="5" class="normal_bold"><? echo $go_api->lng("Web gesamt")?>: <? echo sprintf("%01.2f", $web_gpreis)?> <? echo $go_api->lng("EURO")?></td>
                          </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td colspan="2" align="right">&nbsp;</td>
                      </tr>
					  <?
					  $gpreis += $web_gpreis;
					  }
					  }
					  ?>
                      <tr>
                        <td>&nbsp;</td>
                        <td colspan="2"><hr size="1" noshade></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td colspan="2" align="right" class="normal"><? echo $go_api->lng("enthaltene Mehrwertsteuer")?>:
                         <? echo sprintf("%01.2f", $mwst_gesamt)?> <? echo $go_api->lng("EURO")?></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td colspan="2" align="right" class="t6b"><? echo $go_api->lng("Gesamtbetrag")?>: <? echo sprintf("%01.2f", $gpreis)?> <? echo $go_api->lng("EURO")?></td>
                      </tr>
  					</table>
</body>
</html>