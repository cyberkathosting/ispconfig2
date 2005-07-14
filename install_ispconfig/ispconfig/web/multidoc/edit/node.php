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


// Gruppen aussortieren

if(stristr($id, "group")){
$id = substr($id,5);
if(is_file('../../messenger/groups/group_show.php')) {
    header("Location: ../../messenger/groups/group_show.php?$session&id=$id");
} else {
    header("Location: ../frame_start.php?$session&id=$id");
}
exit;
}

if($id == "grp_root"){
header("Location: ../../optionen/frame_optionen.php?$session");
exit;
}

if ($id == "news"){
header("Location: ../../optionen/frame_optionen.php?$session");
exit;
}

$go_api->uses("tree");
$go_api->tree->set_table($go_info["modul"]["table_name"]);





$go_api->content->define( array(    main    => "main.htm",
		                            table   => "multidoc_node.htm",
		                            stylesheet => "style.css"));


$go_api->content->assign( array(    TITLE => "$session_site Startseite",
						            SESSION => $session,
						            BACKGROUND_GIF => "",
						            COPYRIGHT => "von Till",
						            FGCOLOR => "$session_nav_hcolour",
						            TABLE_H_COLOR => "$session_page_hcolour",
						            BOXSIZE => "450",
						            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Ordner")."</font>",
						            SITENAME => "$session_site",
						            DESIGNPATH => $session_design_path,
                                    SERVERURL => $go_info["server"]["server_url"],
						            S => $s,
                                    TXTGRUPPE => $go_api->lng('Gruppe'),
                                    TXTNAME => $go_api->lng('Name'),
                                    TXTINORDNER => $go_api->lng('in_ordner')
                                    ) );

if(isset($id)) {
    $item = $go_api->tree->item($id);
    if($gid == "") {
        $gid = $item["groupid"];
    }
}

if($gid == "") $gid = 0;

/****************************************
*  Gruppen in Dropdownmenü Feld einfügen
****************************************/
// 0 = keine Gruppen, 1 = Gruppen und privater Bereich, 2 = nur Gruppen
if($go_info["modul"]["groups"] != 2) {
    if($gid == 0) {
        $gruppe = "<option value=\"0\" selected>".$go_api->lng("keine Gruppe")."</option>";
        $groupname = $go_api->lng("Stammordner");
        $ordnerid = "root";
    } else {
        $gruppe = "<option value=\"0\">".$go_api->lng("keine Gruppe")."</option>";
    }
}



if($mygroups = $go_api->groups->myGroups()){
	foreach($mygroups as $key => $elem) {
    
    // Wenn nur Gruppen erlaubt sind und keine Gruppe übergeben wurde, 
    // wähle erste Gruppe als Standard
    if($go_info["modul"]["groups"] == 2 and $gid == 0) $gid = $key;
    
	$name = $elem["name"];
		if($key == $gid) {
			$gruppe .= "<option value=\"$key\" selected>$name</option>\n";
			$groupname = $name;
			$ordnerid = "group$key";
		} else {
			$gruppe .= "<option value=\"$key\">$name</option>\n";
		}
	}
}

$go_api->content->assign( array( GRUPPE => $gruppe));

/**************************************
*  Ordner in Dropdownmenü Feld einfügen
**************************************/

$node_option = "<option value=\"$ordnerid\">$groupname</option>\n";

if($nodes = $go_api->tree->node_optionlist($gid)){
	foreach($nodes as $key => $elem) {
	    $node_title = $elem["title"];
        $node_id = $elem["tree_id"];
		if($node_id == $item["parent"] and $node_id != $id) {
			$node_option .= "<option value=\"$node_id\" selected>$node_title</option>\n";
		} elseif ($node_id != $id) {
			$node_option .= "<option value=\"$node_id\">$node_title</option>\n";
		}
	}
}

$go_api->content->assign( array( NODES => $node_option));


/*******************
*  Textfelder füllen
********************/

if(isset($item)) {
$go_api->content->assign( array(    ID => $item["tree_id"],
						            BTEXT => $item["title"],
						            GID => $item["groupid"],
                                    SPEICHERN => $go_api->lng("Speichern"),
                                    ABBRECHEN => $go_api->lng("Abbrechen"),
                                    LOESCHEN => $go_api->lng("Löschen")
						            ));

if($gid == "") $gid = $item["groupid"];
$ordner = $item["parent"];
} else {
$go_api->content->assign( array(    SPEICHERN => $go_api->lng("Speichern"),
                                    ABBRECHEN => $go_api->lng("Abbrechen"),
                                    LOESCHEN => $go_api->lng("Löschen")
						            ));
$go_api->content->no_strict();
}

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;

?>