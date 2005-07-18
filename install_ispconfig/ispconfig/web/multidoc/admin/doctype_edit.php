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

if(!$go_api->auth->check_admin(0,1)) die("Access not permitted.");

$go_api->uses("doc");
                 
############################################################################
#
#   Template definieren
#
############################################################################

$go_api->content->define( array(
		            main    => "main.htm",
		            table   => "multidoc_admin_doctype_edit.htm",
		            stylesheet => "style.css"));

$go_api->content->assign( array( TITLE => "$session_site Startseite",
						SESSION => $session,
						BACKGROUND_GIF => "",
						COPYRIGHT => "von Till",
						FGCOLOR => "$session_nav_hcolour",
						TABLE_H_COLOR => "$session_page_hcolour",
						BOXSIZE => "450",
						WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#000000\">&nbsp; Form editor</font>",
						SITENAME => "$session_site",
						DESIGNPATH => $session_design_path,
						SERVERURL => $go_info["server"]["server_url"],
						S => $s
            
            ) );

if(isset($id)){
$doc = $go_api->doc->doctype_get($id);
} else {
$doc = new doc();
$go_api->content->no_strict();
}

// Tree optionsfeld füllen
// if($doc->tree == "") $doc->tree = 1;

if($doc->tree == 0){
$tree_option = "
<option value=\"1\">yes</option>
<option value=\"0\" SELECTED>no</option>
";
}else{
$tree_option = "
<option value=\"1\" SELECTED>yes</option>
<option value=\"0\">no</option>
";
}

if($doc->buttons == 0){
$buttons_option = "
<option value=\"1\">yes</option>
<option value=\"0\" SELECTED>no</option>
";
}else{
$buttons_option = "
<option value=\"1\" SELECTED>yes</option>
<option value=\"0\">no</option>
";
}

if($doc->group_required == 0){
$group_required = "
<option value=\"1\">yes</option>
<option value=\"0\" SELECTED>no</option>
";
}else{
$group_required = "
<option value=\"1\" SELECTED>yes</option>
<option value=\"0\">no</option>
";
}

//DOCTYPE_WYSIWYG

if($doc->wysiwyg_lib == 0){
$wysiwyg_lib = "";
}else{
$wysiwyg_lib = "CHECKED";
}          
/****************************************
*  Gruppen in Dropdownmenü Feld einfügen
****************************************/

if($doc->groupid == 0) {
$gruppe = "<option value=\"0\" selected>All users</option>";
$groupname = "Root folder";
$ordnerid = "root";
} else {
$gruppe = "<option value=\"0\">All users</option>";
}

if($mygroups = $go_api->groups->myGroups()){
	foreach($mygroups as $key => $elem) {
	$name = $elem["name"];
		if($key == $doc->groupid) {
			$gruppe .= "<option value=\"$key\" selected>$name</option>\n";
			$groupname = $name;
			$ordnerid = "group$key";
		} else {
			$gruppe .= "<option value=\"$key\">$name</option>\n";
		}
	}
}


$mymodules = $go_api->db->queryAllRecords("select * from sys_modules");
	foreach($mymodules as $key => $elem) {
	$name = $elem["module_name"];
    $title = $elem["module_title"];
		if($doc->modul == $name) {
			$modules .= "<option value=\"$name\" selected>$title</option>\n";
		} else {
			$modules .= "<option value=\"$name\">$title</option>\n";
		}
	}


$go_api->content->assign( array( DOCTYPE_TITLE => $doc->title,
					 DOCTYPE_DESCRIPTION => $doc->description,
                     DOCTYPE_NAME => $doc->name,
                     DOCTYPE_STORAGE_PATH => $doc->storage_path,
                     GROUP_ID => $doc->groupid,
                     DOCTYPE_ID => $id,
                     DOCTYPE_ICON => $doc->icon,
                     FORM_WIDTH => $doc->form_width,
                     GRUPPE => $gruppe,
                     MODULES => $modules,
                     FORM_GROUP_REQUIRED => $group_required,
                     FORM_TREE => $tree_option,
					 FORM_BUTTONS => $buttons_option,
                     DOCTYPE_EVENT_CLASS => $doc->event_class,
                     DOCTYPE_EVENT_INSERT => $doc->event_insert,
                     DOCTYPE_EVENT_UPDATE => $doc->event_update,
                     DOCTYPE_EVENT_DELETE => $doc->event_delete,
					 DOCTYPE_EVENT_SHOW => $doc->event_show,
					 DOCTYPE_WYSIWYG => $wysiwyg_lib
					 ));


$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>