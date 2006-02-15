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

$go_api->uses("multidoc");

$tree_id = intval($_REQUEST["tree_id"]);
$doc_id = intval($_REQUEST["doc_id"]);
$doctype_id = intval($_REQUEST["doctype_id"]);
$gid = intval($_REQUEST["gid"]);
$userid = intval($_REQUEST["userid"]);

$go_api->content->define( array(
                            main    => "main.htm",
                            table   => "multidoc_listview_list.htm",
                            stylesheet => "style.css"));


$go_api->content->define_dynamic ( "documents", "table" );


if ($page == "") $page = 0;

$limit = 20;
$von = $page * $limit;
$bis = $von + $limit;
$lastpage = $page - 1;
$nextpage = $page + 1;
$x = 0;

$id = addslashes($HTTP_GET_VARS["id"]);
$nodes_table = $go_info["modul"]["table_name"]."_nodes";

$liste_ordner = $go_api->db->queryOneRecord("SELECT title from $nodes_table where tree_id = '$id'");

$go_api->content->assign( array( TITLE => "$session_site News",
                                                SESSION => $session,
                                                BACKGROUND_GIF => "",
                                                COPYRIGHT => "von Till",
                                                FGCOLOR => "$session_nav_hcolour",
                                                TABLE_H_COLOR => "$session_page_hcolour",
                                                BOXSIZE => '450',
                                                WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$liste_ordner["title"].":  $von - $bis</font>",
                                                SITENAME => "$session_site",
                                                DESIGNPATH => $session_design_path,
                                                CHARSET => $go_info["theme"]["charset"],
                        SERVERURL => $go_info["server"]["server_url"],
                        BOX_SPACER => '440',
                                                S => $s,
                        BEARBEITEN => $go_api->lng("Bearbeiten"),
                        LOESCHEN => $go_api->lng("Löschen")

            ) );


        $sql = "SELECT * from $nodes_table where $nodes_table.parent = '$id' and status = '1'";

        // Berechtigungen anhängen
        $sql .= " and ( $nodes_table.userid = '$userid' or";

        $gruppen = $go_api->groups->myGroups();

        foreach( $gruppen as $gkey => $gval) {
            if($gval["userstatus"] == 1) {
                $sql .= " $nodes_table.groupid = '$gkey' or";
            }
        }

        $sql = substr($sql,0,-2);
        $sql .= ") ORDER BY title";

        //echo $sql;



$go_api->db->query($sql);

if($limit != ""){

if($page > 0) {
    $zurueck = "<a href=\"list.php?page=$lastpage&id=$id&s=$s\" class=\"t2l\">&lt;&lt; ".$go_api->lng("Zurück")."</a>";
} else {
    $zurueck= "&nbsp;";
}
if($go_api->db->numRows() > $bis) {
    $weiter = "<a href=\"list.php?page=$nextpage&id=$id&s=$s\" class=\"t2l\">".$go_api->lng("Weiter")." &gt;&gt;</a>";
} else {
    $weiter = "&nbsp;";
}

$go_api->content->assign( array( WEITER => $weiter,
                                 ZURUECK => $zurueck));

$sql .= " LIMIT $von,$limit";
$rows = $go_api->db->queryAllRecords($sql);

} else {
$go_api->content->no_strict();
}


foreach($rows as $row) {
    if ($bgcolor == "#FFFFFF"){
        $bgcolor = "#EEEEEE";
    } else {
        $bgcolor = "#FFFFFF";
    }

    if($row["icon"] == "") {
        $icon = "doc.gif";
    } else {
        $icon = $row["icon"];
    }

    $go_api->content->assign( array(    DOC_TITLE => $row["title"],
                                        DOC_ID => $row["doc_id"],
                                        BGCOLOR => $bgcolor,
                                        ICON => $icon,
                                        TREE_ID => $row["tree_id"] ));
    $go_api->content->parse(Documents, ".documents");
    $x++;
    }

if($x < 1) $go_api->content->clear_dynamic("documents");


$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;

?>