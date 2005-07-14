<?
#######################################################
# modul Informationen
#######################################################
$go_info["modul"]["menu"] = "";

$go_info["modul"]["menu"][0] = array(   icon => "icon_dnsmaster_neu.gif",
                                        title => "Master neu",
                                        link => "new/dns.php",
                                        perms => "rw");

$go_info["modul"]["menu"][1] = array(   icon => "icon_dnsslave_neu.gif",
                                        title => "Slave neu",
                                        link => "new/dns_slave.php",
                                        perms => "rw");

/*
$go_info["modul"]["menu"][1] = array(   icon => "nav_neu.gif",
                                        title => "bearbeiten",
                                        link => "admin/doctype_list.php",
                                        perms => "rw");
*/
$go_info["modul"]["menu"][2] = array(   icon => "icon_ordner_neu.gif",
                                        title => "Ordner neu",
                                        link => "../multidoc/edit/node.php",
                                        perms => "rw");
/*
$go_info["modul"]["menu"][2] = array(   icon => "nav_gruppen.gif",
                                        title => "Gruppe neu",
                                        link => "../messenger/groups/group_edit.php",
                                        perms => "rw");
*/                                   

$go_info["modul"]["menu"][3] = array(   icon => "icon_suchen.gif",
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
$go_info["modul"]["include_dir"]       = $go_info["server"]["include_root"];
$go_info["modul"]["template_dir"]      = $go_info["server"]["template_root"];
$go_info["modul"]["lang_dir"]          = "";
$go_info["modul"]["version"]           = "";
$go_info["modul"]["groups"]           = 2; // 0 = keine Gruppen, 1 = Gruppen und privater Bereich, 2 = nur Gruppen
// $go_info["modul"]["item_order"]        = $user_row["bookmark_order"];
// $go_info["modul"]["news"]              = $user_row["news"];

$go_info["modul"]["sidenav"]    = "tree" //flat oder tree
?>