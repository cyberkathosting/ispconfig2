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
#######################################################
# modul Informationen
#######################################################
$go_info["modul"]["menu"] = "";

if($go_info["webftp"]["web_id"] > 0) {

$go_info["modul"]["menu"][0] = array(   icon => "icon_upload.gif",
                                        title => $go_api->lng("Datei Upload"),
                                        link => "edit/file_upload.php",
                                        perms => "rw",
										params => "web_id=".$go_info["webftp"]["web_id"]);

$go_info["modul"]["menu"][1] = array(   icon => "icon_ordner_neu.gif",
                                        title => $go_api->lng("Ordner neu"),
                                        link => "edit/ordner_edit.php",
                                        perms => "rw",
										params => "web_id=".$go_info["webftp"]["web_id"]."&ed=new");

$go_info["modul"]["menu"][2] = array(   icon => "icon_logout.gif",
                                        title => "FTP ".$go_api->lng("Logout"),
                                        link => "logout.php",
                                        perms => "rw",
										params => "",
										target => "_top");

/*
$go_info["modul"]["menu"][2] = array(   icon => "nav_gruppen.gif",
                                        title => "Gruppe neu",
                                        link => "../messenger/groups/group_edit.php",
                                        perms => "rw");
*/
}
/*                                        
$go_info["modul"]["menu"][3] = array(   icon => "nav_suchen.gif",
                                        title => "Suchen",
                                        link => "../multidoc/search/index.php",
                                        perms => "rw");
*/                                                                                
$go_info["modul"]["element_types"] = array( deck => "Ebene",
                                            shortText => "kleines Textfeld",
                                            longText => "grosses Textfeld",
                                            imageField => "*Bildfeld",
                                            linkField => "Hyperlink",
                                            doubleField => "Währungsfeld",
                                            integerField => "Ganzzahlen Feld",
                                            dateField => "Datumsfeld",
                                            fileField => "Dateifeld",
                                            attachField => "Dokumentenabhängigkeit",
                                            descField => "Beschreibung",
                                            seperatorField => "Trennstrich",
                                            checkboxField => "Checkbox",
                                            optionField => "Optionsfeld",
                                            messageField => "*Messenger",
                                            workflowField => "*Workflow",
                                            appointField => "*Termin");

$go_info["modul"]["table_name"]        = "isp_file";

$go_info["modul"]["name"]              = "isp_file";
$go_info["modul"]["title"]             = "WEB FTP";
$go_info["modul"]["include_dir"]       = $go_info["server"]["include_root"];
$go_info["modul"]["template_dir"]      = $go_info["server"]["template_root"];
$go_info["modul"]["lang_dir"]          = "";
$go_info["modul"]["version"]           = "";
// $go_info["modul"]["item_order"]        = $user_row["bookmark_order"];
// $go_info["modul"]["news"]              = $user_row["news"];

$secret = 'n23x7bknl8i535bl8n234578n23475n2358lb83475';
$go_info["modul"]["sidenav"]    = "tree" //flat oder tree
?>