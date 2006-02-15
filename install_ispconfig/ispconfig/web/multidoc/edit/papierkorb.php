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

$tree_id = intval($_REQUEST["tree_id"]);
$doc_id = intval($_REQUEST["doc_id"]);
$doctype_id = intval($_REQUEST["doctype_id"]);
$gid = intval($_REQUEST["gid"]);
$userid = intval($_REQUEST["userid"]);

if($go_api->auth->check_write($gid)) {

$go_api->uses("multidoc");

$go_api->content->define( array(
                            main    => "main.htm",
                            table   => "multidoc_edit_papierkorb.htm",
                            stylesheet => "style.css"));

$go_api->content->define_dynamic ( "user", "table" );

if ($page == "") $page = 0;

$von = $page * 30;
$bis = $von + 30;
$lastpage = $page - 1;
$nextpage = $page + 1;
$x = 0;

$go_api->content->assign( array( TITLE => "$session_site Papierkorb",
                                                SESSION => $session,
                                                BACKGROUND_GIF => "",
                                                COPYRIGHT => "von Till",
                                                FGCOLOR => "$session_nav_hcolour",
                                                TABLE_H_COLOR => "$session_page_hcolour",
                                                BOXSIZE => "100%",
                                                WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Papierkorb: Eintr�ge")." $von - $bis</font>",
                                                SITENAME => "$session_site",
                                                DESIGNPATH => $session_design_path,
                                                CHARSET => $go_info["theme"]["charset"],
                        SERVERURL => $go_info["server"]["server_url"],
                                                S => $s,
                        WIEDERHERSTELLEN => $go_api->lng("Wiederherstellen")
                        ) );


if($gid == 0) {
$sql = "SELECT tree_id,title,icon,type,doctype_id from ".$go_info["modul"]["table_name"]."_nodes where status = '0' and userid = '$userid'";
} else {
$sql = "SELECT tree_id,title,icon,type,doctype_id from ".$go_info["modul"]["table_name"]."_nodes where status = '0' and groupid = '$gid'";
}

$rs = $go_api->db->query($sql);


if($page > 0) $zurueck = "<a href=\"papierkorb.php?page=$lastpage&gid=$gid&s=$s\" class=\"t2l\">&lt;&lt; ".$go_api->lng("Zur�ck")."</a>";
if($go_api->db->numRows() > $bis) $weiter = "<a href=\"papierkorb.php?page=$nextpage&gid=$gid&s=$s\" class=\"t2l\">".$go_api->lng("Weiter")." &gt;&gt;</a>";

$go_api->content->assign( array( WEITER => $weiter,
                                 ZURUECK => $zurueck));

if($gid == 0) {
$sql = "SELECT tree_id,title,icon,type,doctype_id from ".$go_info["modul"]["table_name"]."_nodes where status = '0' and userid = '$userid' LIMIT $von,30";
} else {
$sql = "SELECT tree_id,title,icon,type,doctype_id from ".$go_info["modul"]["table_name"]."_nodes where status = '0' and groupid = '$gid' LIMIT $von,30";
}
$rows = $go_api->db->queryAllRecords($sql);

// Doctypes einlesen, um Eintr�ge zu benennen
$doctypes = $go_api->db->queryAllRecords("SELECT doctype_id, doctype_title from doctype");
foreach($doctypes as $dt) {
        $dt_id = $dt["doctype_id"];
        $dtitle[$dt_id] = $dt["doctype_title"];
}



foreach($rows as $row) {

if($bgcolor == $session_page_hcolour) {
$bgcolor = "#FFFFFF";
} else {
$bgcolor = $session_page_hcolour;
}

// Icon bestimmen
if($row["icon"] == "") {
    if($row["type"] == 'n') {
    $icon = 'vzzu-0.gif';
    } else {
    $icon = 'globus.gif';
    }
} else {
$icon = $row["icon"];
}

$doctype_id = $row["doctype_id"];


$go_api->content->assign( array(    ITEMTITLE => $row["title"],
                                                                        DOCTYPETITLE => $dtitle[$doctype_id],
                                    TREE_ID => $row["tree_id"],
                                    BGCOLOR => $bgcolor,
                                    GID => $gid,
                                    ICON => $icon,
                                    TYPE => $row["type"]
                                    ));

$go_api->content->parse(USER, ".user");
$x++;

}

if($x == 0) $go_api->content->clear_dynamic("user");


$go_api->content->assign( array(    TXT_PAPIERKORB_LEEREN => $go_api->lng("Papierkorb leeren")));

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
}
?>