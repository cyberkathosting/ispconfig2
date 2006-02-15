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

// $go_api->uses("multidoc");

// $start = $go_api->multidoc->utime();

############################################################################
#
#   Template definieren
#
############################################################################

$tpl = new FastTemplate("../../templates");

$tpl->define( array(
                main    => "main.htm",
                table   => "isp_manager_new_web.htm",
                stylesheet => "style.css"));


$tpl->assign( array( TITLE => "$session_site Startseite",
                                                SESSION => $session,
                                                BACKGROUND_GIF => "",
                                                COPYRIGHT => "von Till",
                                                FGCOLOR => "$session_nav_hcolour",
                                                TABLE_H_COLOR => "$session_page_hcolour",
                                                BOXSIZE => "450",
                                                WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("neues Web anlegen")."</font>",
                                                SITENAME => "$session_site",
                                                DESIGNPATH => $session_design_path,
                                                CHARSET => $go_info["theme"]["charset"],
                                                SERVERURL => $go_info["server"]["server_url"],
                                                S => $s

            ) );

$kunde = intval($_REQUEST["kunde"]);
$anbieter = intval($_REQUEST["anbieter"]);
$vorlage = intval($_REQUEST["vorlage"]);

if(!is_int($anbieter) and !empty($anbieter)) $go_api->errorMessage("Fehler bei ID Übergabe");

//die($anbieter);
// Hole Anbieter (Gruppen) des Users
if($mygroups = $go_api->groups->myGroups()){
    $y = 0;
    $anbieter_mnu = '';

        foreach($mygroups as $key => $elem) {
        $name = $elem["name"];
    if($y == 0 and empty($anbieter)) $anbieter = $key;
    $y++;
                if($key == $anbieter) {
                        $anbieter_mnu .= "<option value=\"$key\" selected>$name</option>\n";
                } else {
                        $anbieter_mnu .= "<option value=\"$key\">$name</option>\n";
                }
        }
}
$tpl->assign( array( ANBIETER => $anbieter_mnu));


if(!empty($anbieter)) {

        // Kunden des Anbieters suchen
        $x = 0;
    $rows = $go_api->db->queryAllRecords("select * from isp_nodes where doctype_id = '1012' and groupid = '$anbieter' ORDER BY title");
    $kunden = "";
    while (list($key, $row) = each($rows))
        {
        $x++;
        $kunden .= "<option value=\"".$row["tree_id"]."\">".$row["title"]."</option>\n";
        }

    if($x == 0) $kunden .= "<option value=\"\">".$go_api->lng("Kein Kunde vorhanden")."</option>\n";

    $tpl->assign( array( KUNDEN => "$kunden"));

        // Web-Vorlagen des Kunden suchen
        $x = 0;
    $rows = $go_api->db->queryAllRecords("select * from isp_nodes where doctype_id = '1030' and groupid = '$anbieter' ORDER BY title");
    $vorlagen = '<option value="0">'.$go_api->lng("Keine Vorlage verwenden").'</option>';
    while (list($key, $row) = each($rows))
        {
        $x++;
        $vorlagen .= "<option value=\"".$row["doc_id"]."\">".$row["title"]."</option>\n";
        }

    //if($x == 0) $vorlagen .= "<option value=\"\">".$go_api->lng("Keine Vorlage vorhanden")."</option>\n";

    $tpl->assign( array( VORLAGEN => "$vorlagen"));

}

if(!empty($_POST["kunde"]) and !empty($_POST["anbieter"])) {
    // Checke nochmal, ob Dokument dem Anbieter gehört
    $row = $go_api->db->queryOneRecord("SELECT groupid from isp_nodes where tree_id = $kunde");
    if($row["groupid"] == $anbieter) header("Location: ../../multidoc/edit/edit.php?s=$s&next_doctype_id=1012&doctype_id=1013&next_tree_id=$kunde&caller_tree_id=$kunde&gid=$anbieter&vorlage_id=$vorlage");
}

$tpl->assign( array( TXTANBIETER => $go_api->lng("Anbieter"),
                     TXTKUNDE => $go_api->lng("Kunde"),
                     TXTWEITER => $go_api->lng("weiter"),
                                         TXTWEBVORLAGE => $go_api->lng("Web Vorlage")));

$tpl->parse(STYLESHEET, stylesheet);
$tpl->parse(MAIN, array("table","main"));
$tpl->FastPrint();
exit;
?>