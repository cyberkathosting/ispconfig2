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

$go_api->uses_obj("liste");
                 
############################################################################
#
#   Template definieren
#
############################################################################

$go_api->content->define( array(
		            main    => "main.htm",
		            table   => "multidoc_listadmin_listtype_edit.htm",
		            stylesheet => "style.css"));

$go_api->content->assign( array( TITLE => "$session_site Startseite",
						SESSION => $session,
						BACKGROUND_GIF => "",
						COPYRIGHT => "von Till",
						FGCOLOR => "$session_nav_hcolour",
						TABLE_H_COLOR => "$session_page_hcolour",
						BOXSIZE => "450",
						WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; Listentyp bearbeiten/erstellen</font>",
						SITENAME => "$session_site",
						DESIGNPATH => $session_design_path,
SERVERURL => $go_info["server"]["server_url"],

						S => $s
            
            ) );

if(isset($id)){
$row = $go_api->db->queryOneRecord("select * from listtype where listtype_id = '$id'");

$liste = unserialize($row["listtype_def"]);

} else {
$liste = new liste();
$go_api->content->no_strict();
}

// Tree optionsfeld füllen
// if($doc->tree == "") $doc->tree = 1;
/*
if($doc->tree == 0){
$tree_option = "
<option value=\"1\">Ja</option>
<option value=\"0\" SELECTED>Nein</option>
";
}else{
$tree_option = "
<option value=\"1\" SELECTED>Ja</option>
<option value=\"0\">Nein</option>
";
}
*/          
/****************************************
*  Gruppen in Dropdownmenü Feld einfügen
****************************************/

if($liste->groupid == 0) {
$gruppe = "<option value=\"0\" selected>Alle User</option>";
$groupname = "Stammordner";
$ordnerid = "root";
} else {
$gruppe = "<option value=\"0\">Alle User</option>";
}

if($mygroups = $go_api->groups->myGroups()){
	foreach($mygroups as $key => $elem) {
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

// Doctypes auflisten
if($mydoctypes = $go_api->db->queryAllRecords("SELECT * from doctype order by doctype_name")){
	foreach($mydoctypes as $key => $elem) {
	$title = $elem["doctype_title"];
    $doctype_id = $elem["doctype_id"];
		if($doctype_id == $liste->doctype) {
			$doctypes .= "<option value=\"$doctype_id\" selected>$title</option>\n";
		} else {
			$doctypes .= "<option value=\"$doctype_id\">$title</option>\n";
		}
	}
}

// Module auflisten
$modules = "<option value=\"0\">Alle Module</option>";
if($mymodules = $go_api->db->queryAllRecords("SELECT * from modules")){
	foreach($mymodules as $key => $elem) {
	$title = $elem["module_title"];
    $name = $elem["module_name"];
		if($name == $liste->modul) {
			$modules .= "<option value=\"$name\" selected>$title</option>\n";
		} else {
			$modules .= "<option value=\"$name\">$title</option>\n";
		}
	}
}

$go_api->content->assign( array( LIST_TITLE => $liste->title,
					 LIST_DESCRIPTION => $liste->description,
                     LIST_QUERY => $liste->query,
                     LIST_LIMIT => $liste->limit,
                     GROUP_ID => $liste->groupid,
                     DOCTYPES => $doctypes,
                     MODULES => $modules,
                     LIST_ICON => $liste->icon,
                     LIST_WIDTH => $liste->width,
                     LISTTYPE_ID => $id,
                     GRUPPE => $gruppe,
                     ORDERFIELD => $liste->orderfield
					 ));


$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>