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

$go_api->content->define( array(    main    => "main.htm",
                                            table   => "ispfakt_view_reseller.htm",
                                            stylesheet => "style.css"));

$go_api->content->define_dynamic ( "liste", "table" );

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "von Till",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "#EEEEEE",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Rechnungen Anbieter").": </font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                            CHARSET => $go_info["theme"]["charset"],
                                                                        SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s,
                                                            TXT_DATUM => $go_api->lng("Datum"),
                                                            TXT_ANBIETER => $go_api->lng("Anbieter"),
                                                            TXT_RG_NR => $go_api->lng("Rg. Nr."),
                                                            TXT_OK => $go_api->lng("OK")
                                    ) );

$monat = (isset($HTTP_POST_VARS["monat"]))?intval($HTTP_POST_VARS["monat"]):date("m");
$jahr = (isset($HTTP_POST_VARS["jahr"]))?intval($HTTP_POST_VARS["jahr"]):date("Y");

// berechne Start + Enddatum für Suche nach Rechnungen
$suche_start = mktime (0,0,0,$monat,1,$jahr);
$suche_ende = mktime (23,59,59,$monat+1,0,$jahr);

if($go_info["user"]["userid"] == 1) {
        $reseller_id = 0;
} else {
        $go_api->errorMessage("Nur für den Admin verfügbar.");
}

$rows = $go_api->db->queryAllRecords("SELECT * FROM isp_fakt_rechnung where typ = 'reseller' and datum >= $suche_start and datum <= $suche_ende");

$config_tmp = $go_api->db->queryOneRecord("SELECT * from sys_config");
$rg_nr_start = $config_tmp["rechnung_nr_start"];
unset($config_tmp);

// Datums Selector füllen
$select_monat = '';
for($i = 1; $i <= 12; $i++) {
        $selected = ($monat == $i)? " SELECTED":"";
        $select_monat .= "<option value=\"$i\"$selected>$i</option>";
}

$select_jahr = '';
for($i = 2003; $i <= date("Y"); $i++) {
        $selected = ($jahr == $i)? " SELECTED":"";
        $select_jahr .= "<option value=\"$i\"$selected>$i</option>";
}

$go_api->content->assign( array(        MONAT => $select_monat,
                                                                        JAHR => $select_jahr
                                                                        ));


$n = 0;
if(is_array($rows)) {
  foreach($rows as $row) {

        $datum = date("d.m.Y",$row["datum"]);
        $reseller = $go_api->db->queryOneRecord("SELECT firma, vorname, name from isp_isp_reseller where doc_id = ".$row["reseller_id"]);

        $kunde = ($row["reseller_id"] != 0)? $reseller["firma"] ." - ".$reseller["vorname"] ." ".$reseller["name"] ." ":$go_api->lng("admin");

        $go_api->content->assign( array(        KUNDE => $kunde,
                                                                                RNR => $row["rechnung_id"] + $rg_nr_start,
                                                                                DATUM => $datum,
                                                                                ID => $row["rechnung_id"]
                                                                                ));

        $go_api->content->parse(LISTE,".liste");
        $n++;
  }
}


if($n == 0) $go_api->content->clear_dynamic("liste");


$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;



?>