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

$go_info["modul"]["menu"][0] = array(   icon => "nav_neu.gif",
                                        title => "neu",
                                        link => "edit/new_switch.php",
                                        perms => "rw");
/*
$go_info["modul"]["menu"][1] = array(   icon => "nav_neu.gif",
                                        title => "bearbeiten",
                                        link => "admin/doctype_list.php",
                                        perms => "rw");
*/
$go_info["modul"]["menu"][1] = array(   icon => "nav_kategorie.gif",
                                        title => "ordner neu",
                                        link => "edit/node.php",
                                        perms => "rw");

$go_info["modul"]["menu"][2] = array(   icon => "nav_gruppen.gif",
                                        title => "Gruppe neu",
                                        link => "../messenger/groups/group_edit.php",
                                        perms => "rw");
                                      
$go_info["modul"]["menu"][3] = array(   icon => "nav_suchen.gif",
                                        title => "Suchen",
                                        link => "search/index.php",
                                        perms => "rw");

                                                                               
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
                                            pluginField => "Plugin",
                                            messageField => "*Messenger",
                                            workflowField => "*Workflow",
                                            terminField => "Termin");

//$go_info["modul"]["table_name"]        = "multidoc";

//$go_info["modul"]["name"]              = "multidoc";
//$go_info["modul"]["title"]             = "Archiv";
$go_info["modul"]["include_dir"]       = INCLUDE_ROOT;
$go_info["modul"]["template_dir"]      = $go_info["server"]["template_root"];
$go_info["modul"]["lang_dir"]          = "";
$go_info["modul"]["version"]           = "";
$go_info["modul"]["groups"]           = 1; // 0 = keine Gruppen, 1 = Gruppen und privater Bereich, 2 = nur Gruppen
// $go_info["modul"]["item_order"]        = $user_row["bookmark_order"];
// $go_info["modul"]["news"]              = $user_row["news"];

$go_info["modul"]["sidenav"]    = "flat" //flat oder tree

?>