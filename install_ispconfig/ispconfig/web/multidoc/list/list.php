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

$go_api->uses("multidoc,listadmin");
$go_api->uses_obj("liste");

$go_api->content->define( array(
                            main    => "main.htm",
                            table   => "multidoc_list_list.htm",
                            stylesheet => "style.css"));

$tree_id = intval($_REQUEST["tree_id"]);
$doc_id = intval($_REQUEST["doc_id"]);
$doctype_id = intval($_REQUEST["doctype_id"]);
$listtype_id = intval($_REQUEST["listtype_id"]);
$gid = intval($_REQUEST["gid"]);
$userid = intval($_REQUEST["userid"]);

if(!empty($listtype_id)){
$liste = $go_api->listadmin->listtype_get($listtype_id);
} else {
$go_api->errorMessage("Der Listentyp konnte nicht gefunden werden");
}



if ($page == "") $page = 0;

$von = $page * $liste->limit;
$bis = $von + $liste->limit;
$lastpage = $page - 1;
$nextpage = $page + 1;
$x = 0;

$go_api->content->assign( array( TITLE => "$session_site News",
                                                SESSION => $session,
                                                BACKGROUND_GIF => "",
                                                COPYRIGHT => "von Till",
                                                FGCOLOR => "$session_nav_hcolour",
                                                TABLE_H_COLOR => "$session_page_hcolour",
                                                BOXSIZE => $liste->width,
                                                WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$liste->title.": $von - $bis</font>",
                                                SITENAME => "$session_site",
                                                DESIGNPATH => $session_design_path,
                                                CHARSET => $go_info["theme"]["charset"],
SERVERURL => $go_info["server"]["server_url"],

                                                S => $s

            ) );


if(!empty($liste->query)){
    $sql = $liste->query;
} elseif(is_array($form)) {

    $doc = $go_api->multidoc->doctype_get($liste->doctype_id);
    $doctype_table = $doc->storage_path;
    unset($doc);

    $nodes_table = $go_info["modul"]["table_name"]."_nodes";
    $userid = $go_info["user"]["userid"];

    $sql = "SELECT * from $nodes_table,$doctype_table  where
    $doctype_table.doc_id = $nodes_table.doc_id and
    $nodes_table.doctype_id = ".$liste->doctype_id." and ";
        foreach($form as $key => $val) {

        $sql .= " $doctype_table.$key like '%$val%' and";


        }
        $sql = substr($sql,0,-3);
        //$sql .= " and userid = '".$go_info["user"]["userid"]."'";
        $sql .= " and ( $nodes_table.userid = '$userid' or";

        $gruppen = $go_api->groups->myGroups();

        foreach( $gruppen as $gkey => $gval) {
            if($gval["userstatus"] == 1) {
                $sql .= " $nodes_table.groupid = '$gkey' or";
            }
        }
        $sql = substr($sql,0,-2);
        $sql .= ")";

        //echo $sql;

} else {
$go_api->errorMessage("Es wurden keine Quelle angegeben");
}


$go_api->db->query($sql);

if($liste->limit != ""){

if($page > 0) $zurueck = "<a href=\"list.php?page=$lastpage&gid=$gid&s=$s\" class=\"t2l\">&lt;&lt; ".$go_api->lng("Zurück")."</a>";
if($go_api->db->numRows() > $bis) $weiter = "<a href=\"list.php?page=$nextpage&gid=$gid&s=$s\" class=\"t2l\">".$go_api->lng("Weiter")." &gt;&gt;</a>";

$go_api->content->assign( array( WEITER => $weiter,
                                 ZURUECK => $zurueck));

$sql .= " LIMIT $von,30";
$go_api->db->query($sql);

} else {
$go_api->content->no_strict();
}



while($row = $go_api->db->nextRecord()) {
    if(is_array($liste->row)){
    foreach($liste->row as $zeile) {

    if($bgcolor == $session_page_hcolour) {
        $bgcolor = "#FFFFFF";
    } else {
        $bgcolor = $session_page_hcolour;
    }

    $listtable .= "<tr bgcolor=\"$bgcolor\">
                <td>
                 <table width=\"100%\">
                   <tr>";
        foreach($zeile->elements as $spalte) {
        $wert = $row[$spalte->name];
        if($spalte->type == "date") {
            if($wert > 0) {
            $wert = date("d.m.Y",$wert);
            } else {
            $wert = "";
            }
        }

        $listtable .= "    <td width=\"".$spalte->width."%\" class=\"".$spalte->css_class."\">".$wert."</td>";
        }
        $doc_id = $row["doc_id"];
        if($zeile->edit_button == 1) $listtable .= "<td class=\"".$spalte->css_class."\"><a href=\"../edit/edit.php?doctype_id=$doctype_id&doc_id=$doc_id&gid=$gid&s=$s\">[".$go_api->lng("Bearbeiten")."]</a></td>";
        if($zeile->delete_button == 1) $listtable .= "<td class=\"".$spalte->css_class."\"><a href=\"../edit/delete.php?doctype_id=$doctype_id&doc_id=$doc_id&gid=$gid&s=$s\">[".$go_api->lng("Löschen")."]</a></td>";
    $listtable .= "    </tr>
                 </table>
                </td>
               </tr>";
    }
    }

}

$go_api->content->assign( array(    LISTE => $listtable));

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;

?>