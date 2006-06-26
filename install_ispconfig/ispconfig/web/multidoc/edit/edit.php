<?php
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

foreach($_REQUEST as $key => $val) {
        $$key = addslashes($val);
}
$tree_id = intval($_REQUEST["tree_id"]);
$doc_id = intval($_REQUEST["doc_id"]);
$doctype_id = intval($_REQUEST["doctype_id"]);
$gid = intval($_REQUEST["gid"]);
$userid = intval($_REQUEST["userid"]);

$form = $HTTP_POST_VARS["form"];

// Module einbinden
$go_api->uses("doc,tree,renderer");
$go_api->tree->set_table($go_info["modul"]["table_name"]);
$start = $go_api->doc->utime();

if(count($HTTP_POST_VARS) > 3) {

// Dokumententyp laden
//$doc = $go_api->doc->doctype_get($doctype_id);
if(!$doc = $go_api->doc->doctype_get($doctype_id)) $go_api->errorMessage("Der angeforderte Dokumententyp existiert nicht");
// Check ob er diesen Doctype überhaupt anzeigen darf
if(!$go_api->groups->in_group($go_info["user"]["userid"],$doc->groupid) and $doc->groupid != 0 and $doc->groupid != "") $go_api->errorMessage("Sie haben nicht die erforderlichen Rechte, um diesen Dokumententyp zu bearbeiten");
// Check ob Gruppe io bei Doctype-Pflichtgruppen
if($doc->group_required == 1 and $gid == 0) $go_api->errorMessage("Dieser Dokumententyp kann nur in Gruppen gespeichert werden.");

$table = $doc->storage_path;




//action bestimmen
unset($action);
$form_changed = 0;

if($tree_id == "" or ($doc_id == "" and $doc->modul = "")){
$action = 'insert';
} else {
$action = 'update';
}

if($action == "update") {
    // Doctype holen
    if(isset($doctype_id)){
    if(!$doc = $go_api->doc->doctype_get($doctype_id)) $go_api->errorMessage("Der angeforderte Dokumententyp existiert nicht");

    // Check ob er diesen Doctype überhaupt anzeigen darf
    if(!$go_api->groups->in_group($go_info["user"]["userid"],$doc->groupid) and $doc->groupid != 0 and $doc->groupid != "") $go_api->errorMessage("Sie haben nicht die erforderlichen Rechte, um diesen Dokumententyp zu bearbeiten");

    // Datensatz holen + Überprüfung ob user Eigentümer ist
    if(isset($doc_id)){
    $old_form_data = $go_api->db->queryOneRecord("select * from ".$doc->storage_path." where doc_id = '$doc_id'");
    // Check ob der aktuelle User der Eigentümer ist
    }
    }
}

// form Variablen bearbeiten und checken
while (list($key, $val) = each($doc->deck))
    {
    if(is_array($val->elements) and $deck_id == $key){
        $reg_fehler = "";
        while (list($element_key, $element_val) = each($val->elements)){
        if(isset($form[$element_val->name])) $form[$element_val->name] = $go_api->doc->check($form[$element_val->name]);

        // abprüfung gegen reg_expression, ausser wenn es sich um write-once Textfeld handelt
        if(trim($element_val->reg_expression) != "" and !($element_val->write_once == 1 and $action == "update")) {
            if(!preg_match(stripslashes($element_val->reg_expression),$form[$element_val->name])) $reg_fehler .= '<b>'.$go_api->lng("Feld").': '.$element_val->title.'</b><br>'.$go_api->lng($element_val->reg_fehler) . "<br>&nbsp;<br>";
        }

        switch ($element_val->type) {
        case "shortText":
            if($element_val->write_once == 1 and $action == "update") unset($form[$element_val->name]);
            if($element_val->password == 1 and $form[$element_val->name] == "") unset($form[$element_val->name]);
        break;

        case "dateField":
        if(!empty($form[$element_val->name])){
            list( $day, $month, $year ) = split( '[/.-]', $form[$element_val->name]);
                $form[$element_val->name] = mktime(0,0,0,$month,$day,$year);
        }
        break;
        case "doubleField":
        if(!empty($form[$element_val->name])){
                $form[$element_val->name] = str_replace (",", ".", $form[$element_val->name]);
        }
        break;
        case "checkboxField":
        if($form[$element_val->name] == "" and count($form) > 0) $form[$element_val->name] = 0;
        break;
        case "optionField":
        if(is_array($form[$element_val->name])){
            $tempval = "";
            while (list($key, $selectval) = each ($form[$element_val->name])) {
            $tempval .= trim($selectval) .',';
            }
            $form[$element_val->name] = substr($tempval,0,-1);
        }
        break;
        case "terminField":
        $terminfeld = $element_val->name;
        //print_r($$terminfeld);
        $termin_event[] = $$terminfeld;
        break;
                case "pluginField":
        // Plugin Event
                $pluginfeld = $element_val->name;
        //print_r($$terminfeld);
        $plugin_event[$pluginfeld] = $element_val->options;
        break;
        }
        // Form Array in sicheres Array überführen (name: $save)
        if(isset($form[$element_val->name])) {
            // abprüfung, ob geändert
            if($old_form_data[$element_val->name] != $form[$element_val->name]) $form_changed = 1;
            // Form Array in sicheres Array überführen (name: $save)
            $save[$element_val->name] = $form[$element_val->name];
            }
        }

        if($reg_fehler != "") $go_api->errorMessage($reg_fehler);
    }
}
unset($form);



$vars = "";
$vars["title"] = trim($title);
$vars["doctype_id"] = $doctype_id;

if($go_api->auth->check_write($gid,1)) {
    if($action == 'insert'){
    // Anlegen eines neuen Dokumentes
    $go_api->db->insert($table,$save);
    // Debug Errors
    if($go_api->db->errorMessage != "") die($go_api->db->errorMessage);
    //$action = "insert";
    $doc_id = $go_api->db->insertID();
    $vars["doc_id"] = $doc_id;
    if($doc->tree == 1) {
        $vars["type"] = "i";
        $vars["parent"] = $ordner;
        $vars["icon"] = $doc->icon;
        $tree_id = $go_api->tree->item_add($vars,$gid);
    } else {
        $vars["type"] = "a";
        $vars["icon"] = $doc->icon;
        $tree_id = $go_api->tree->item_add($vars,$gid);
    }


    } else {
    // Updaten eines bestehenden Dokumentes
    //$action = "update";
    $item = $go_api->tree->item($tree_id);
    // Setze form changed auf 1 wenn sich Treedaten geändert haben
    if(($item["parent"] != $ordner or $item["groupid"] != $gid or $item["title"] != $title) and $doc->tree == 1) {
        $form_changed = 1;
    }
    // Check ob User Schreibberechtigung für Datensatz besitzt
    if($go_api->tree->is_owner($tree_id,'w') and count($save) > 0 and $form_changed == 1) {
        $doc_id = $item["doc_id"];
        $go_api->db->update($table,$save,"doc_id = '$doc_id'");
        // Debug Errors
        if($go_api->db->errorMessage != "") die($go_api->db->errorMessage);

        $vars["doc_id"] = $doc_id;
        if($doc->tree == 1) {
            $vars["type"] = "i";
            $vars["parent"] = $ordner;
            $go_api->tree->item_update($tree_id,$vars,$gid);
        } else {
            $vars["type"] = "a";
            $go_api->tree->item_update($tree_id,$vars,$gid);
        }
    }
    }
}
// Error Handler
if($go_api->db->errorMessage != "") die($go_api->db->errorMessage);


// Attachdoc
if($next_tree_id != "" and ($next_type == "" or $next_type == "doc")) {
// Dokumentenabhängigkeiten speichern
    if($action == "insert") {
    // neue abhängigkeit anlegen
        if($next_doc_id == "") {
            $item = $go_api->tree->item($next_tree_id);
            $next_doc_id = $item["doc_id"];
            $dep_gid = $item["groupid"];
        } else {
            $dep_gid = $gid;
        }

        $sql = "INSERT INTO ".$go_info["modul"]["table_name"]."_dep (userid,groupid,parent_doc_id,parent_doctype_id,parent_tree_id,child_doc_id,child_doctype_id,child_tree_id) VALUES
        ('$userid','$dep_gid','$next_doc_id','$next_doctype_id','$next_tree_id','$doc_id','$doctype_id','$tree_id')";
        $go_api->db->query($sql);

        //Gruppe + Ordner des angehängten Dokumentes an Hauptdokument anpassen
        //$myrow = $go_api->tree->item($next_doc_id);
        //$myvars = array(parent => $myrow["parent"]);
        //$go_api->tree->item_update($tree_id,$myvars,$myrow["groupid"]);


     } else {
     // abhängigkeit updaten

     }
}

// Termin Eintragen
if(is_array($termin_event)) {
    $go_api->uses('termin');
    foreach($termin_event as $tkey => $tval) {

        if($action == 'insert') {
        $parent["tree_id"] = $tree_id;
        $parent["doc_id"] = $doc_id;
        $parent["doctype_id"] = $doctype_id;
        $go_api->termin->termin_add($tval,$parent,$gid);
        }

        if($action == 'update') {
        $parent["tree_id"] = $tree_id;
        $parent["doc_id"] = $doc_id;
        $parent["doctype_id"] = $doctype_id;
        $sql = "Select * from termin_event where parent_doc_id = '$doc_id' and parent_doctype_id = '$doctype_id' and parent_field_name = '".$tval[field_name]."'";
        $trow = $go_api->db->queryOneRecord($sql);
        $go_api->termin->termin_update($trow["termin_id"],$tval,$parent,$gid);
        }
    }
}

// Plugin Events aufrufen
if(is_array($plugin_event)) {
    foreach($plugin_event as $tval => $options) {
                $plugin_file = INCLUDE_ROOT . DIR_TRENNER.'plugins'.DIR_TRENNER.$tval.".plugin.php";
        if(@is_file($plugin_file)) {
            include_once($plugin_file);
            $pluginclass = $tval . '_plugin';
            if(class_exists($pluginclass)) {
                    $plugin = new $pluginclass;
                                $plugin->_options = $options;
                                if($action == 'insert') $plugin->insert($doc_id,$doctype_id,0);
                                if($action == 'update') $plugin->update($doc_id,$doctype_id,0);
                    unset($plugin);
            } else {
                $go_api->errorMessage("Die KLasse: $pluginclass existiert nicht im Plugin: ".$tval.".");
            }
        } else {
                $go_api->errorMessage("Plugin: ".$tval." nicht gefunden.");
        }
    }
}



#############################################################
// wenn Gruppe geändert, Gruppen der attachten Docs ändern
#############################################################

if($gid != "" and ($gid_old != $gid) and $action == 'update'){

$go_api->tree->update_dep_group($tree_id,$gid);


//die("Gruppe wurde geändert $gid -- $gid_old");
}

##############################################################
// ausführen der doctype_events
##############################################################

if($doc->event_class != "") {
    $event_class = $doc->event_class;

    if(!class_exists($event_class)){
    $go_api->uses($doc->event_class);
    }

    if($doc->event_insert != "" and $action == 'insert') {
    $event_insert = $doc->event_insert;
    $go_api->$event_class->$event_insert($doc_id,$doctype_id);
    }

    if($doc->event_update != "" and $action == 'update') {
    $event_update = $doc->event_update;
    if($form_changed == 1) $go_api->$event_class->$event_update($doc_id,$doctype_id);
    }

    if($doc->event_delete != "" and $action == 'delete') {
    $event_delete = $doc->event_delete;
    $go_api->$event_class->$event_delete($doc_id,$doctype_id);
    }
}

##############################################################

    if($fertig == 0) {
    // wenn zwischen Decks gesprungen wurde
    $deck_id = $next_deck_id;
    //header("Location: edit.php?doctype_id=$doctype_id&doc_id=$doc_id&tree_id=$tree_id&gid=$gid&deck_id=$deck_id&next_doctype_id=$next_doctype_id&next_doc_id=$next_doc_id&next_tree_id=$next_tree_id&caller_tree_id=$caller_tree_id&$session");
    } else {
        if(($next_doctype_id != "" or $next_tree_id != "") and ($next_type == "" or $next_type == "doc")) {
            if($caller_tree_id != $next_tree_id){
            // Wenn attachtes Dokument aufgerufen werden soll
            header("Location: edit.php?doctype_id=$next_doctype_id&tree_id=$next_tree_id&next_tree_id=$tree_id&caller_tree_id=$caller_tree_id&next_doctype_id=$doctype_id&$session");
            exit;
            } else {
            // Wenn Aufruf von attachment beendet, rückkehr zum startdokument
            header("Location: edit.php?doctype_id=$next_doctype_id&tree_id=$next_tree_id&$session");
            exit;
            }
        } elseif(($next_doctype_id != "" or $next_tree_id != "") and $next_type == "file") {
            // File Bearbeitungsroutine
            if($caller_tree_id != $next_tree_id){
            // Wenn Fileadd Dokument aufgerufen werden soll
            header("Location: file.php?doc_id=$doc_id&doctype_id=$doctype_id&tree_id=$tree_id&file_id=$next_tree_id&$session");
            exit;
            } else {
            // Wenn Aufruf von attachment beendet, rückkehr zum startdokument
            header("Location: file.php?doc_id=$doc_id&doctype_id=$doctype_id&tree_id=$tree_id&file_id=$next_tree_id&$session");
            //header("Location: edit.php?doctype_id=$next_doctype_id&tree_id=$next_tree_id&$session");
            exit;
            }
        } else {
        // Dokument speichern
        if($go_info["modul"]["sidenav"] == 'flat') {
            header("Location: ../frame_start.php?$session");
        } else {
            header("Location: ../../index.php?$session");
        }
        exit;
        }
    }

}




############################################################################
#
#   Template definieren
#
############################################################################

$go_api->content->define( array(
                main    => "main.htm",
                table   => "multidoc_edit.htm",
                stylesheet => "style.css"));

// Form Target bestimmen
$formtarget = '_top';
if($go_info["modul"]["sidenav"] == 'flat') $formtarget = '_self';

$go_api->content->assign( array( TITLE => "$session_site Startseite",
                                                SESSION => $session,
                                                BACKGROUND_GIF => "",
                                                COPYRIGHT => "von Till",
                                                FGCOLOR => "$session_nav_hcolour",
                                                TABLE_H_COLOR => "$session_page_hcolour",
                                                WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Formular Designer")."</font>",
                                                SITENAME => "$session_site",
                                                DESIGNPATH => $session_design_path,
                                                CHARSET => $go_info["theme"]["charset"],
                        SERVERURL => $go_info["server"]["server_url"],
                        NEXT_TREE_ID => $next_tree_id,
                        CALLER_TREE_ID => $caller_tree_id,
                                                S => $s,
                        TARGET => $form_target
                        ) );

$form = "";
$element_jscript_nummer = 0;

// versuche tree_id zu ermitteln, wenn nur doc + doctype angegeben ist
if($tree_id == "" and $doc_id != "" and $doctype_id != "") {
$row = $go_api->db->queryOneRecord("SELECT * from ".$go_api->tree->_table." where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
$tree_id = $row["tree_id"];
unset($row);
}


// Holen der Tree Daten + Eigentümer überprüfen
if($tree_id != "") {
    if($item = $go_api->tree->item($tree_id)) {
        $doctype_id = $item["doctype_id"];
        if(isset($doctype_id)) $doc_id = $item["doc_id"];
        if($gid == "") $gid = $item["groupid"];
        $ordner = $item["parent"];
        $gid_old = $item["groupid"];
    } else {
    // error wenn User nicht Eigentümer des Tree Eintrages ist
    $go_api->errorMessage($go_api->lng("error_keine_rechte"));
    if($doctype_id == 1013){
      $go_api->content->strict(); // to allow something like "RewriteCond %{HTTP_HOST} ^.*$" in Apache Directives
    } else {
      $go_api->content->no_strict();
    }
    }
}


// Doctype holen
if(isset($doctype_id)){
if(!$doc = $go_api->doc->doctype_get($doctype_id)) $go_api->errorMessage($go_api->lng("error_doctype_nicht_vorhanden"));

// Check ob er diesen Doctype überhaupt anzeigen darf
if(!$go_api->groups->in_group($go_info["user"]["userid"],$doc->groupid) and $doc->groupid != 0 and $doc->groupid != "") $go_api->errorMessage($go_api->lng("error_doctype_rechte"));

// Datensatz holen + Überprüfung ob user Eigentümer ist
if(isset($doc_id)){
    $tablevalues = $go_api->db->queryOneRecord("select * from ".$doc->storage_path." where doc_id = '$doc_id'");
    // Check ob der aktuelle User der Eigentümer ist
}

##############################################################
// ausführen der doctype_events
##############################################################

if($doc->event_class != "") {
    $event_class = $doc->event_class;

    if(!class_exists($event_class)){
    $go_api->uses($doc->event_class);
    }

    if($doc->event_show != "") {
    $event_show = $doc->event_show;
    $go_api->$event_class->$event_show($doc_id,$doctype_id);
    }
}


if($deck_id == "") $deck_id = key($doc->deck);

// wysiwyg lib einbinden
$wysiwyg_lib = '';

if($doc->wysiwyg_lib == 1) {
        $wysiwyg_lib = "<script>QBPATH='".$go_info["server"]["server_url"]."/res/plugins/richtext/ie'; VISUAL=1; SECURE=1;</script>
        <script src='".$go_info["server"]["server_url"]."/res/plugins/richtext/ie/quickbuild.js'></script>
        <script src='".$go_info["server"]["server_url"]."/res/plugins/richtext/ie/tabedit.js'></script>";
}

$go_api->content->assign( array( DOCTYPE_TITLE => $doc->title,
                                         DOCTYPE_DESCRIPTION => $doc->description,
                     WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng($doc->title)."</font>",
                     GROUP_ID => $doc->groupid,
                     TREE_ID => $tree_id,
                     BOXSIZE => $doc->form_width,
                     DECK_ID => $deck_id,
                     DOC_ID => $doc_id,
                     DOCTYPE_ID => $doctype_id,
                     NEXT_DOCTYPE_ID => $next_doctype_id,
                                         WYSIWYG_LIB => $wysiwyg_lib
                                         ));

if($doc->tree == 1) {
// Anzeigen der Ordner Gruppen menüs
$element_jscript_nummer = 3;
if($gid == "") $gid = $doc->groupid;
if($gid == "") $gid = 0;

/****************************************
*  Gruppen in Dropdownmenü Feld einfügen
****************************************/
if($doc->group_required != 1) {
    if($gid == 0) {
        $gruppe = "<option value=\"0\" selected>".$go_api->lng("keine Gruppe")."</option>";
        $groupname = $go_api->lng("Stammordner");
        $ordnerid = "root";
    } else {
        $gruppe = "<option value=\"0\">".$go_api->lng("keine Gruppe")."</option>";
    }
}

if($doc->groupid == 0) {
// Wenn der Dokumententyp keiner Gruppe gehört
    if($mygroups = $go_api->groups->myGroups()){
        foreach($mygroups as $key => $elem) {
        $name = $elem["name"];
                if($key == $gid) {
                        $gruppe .= "<option value=\"$key\" selected>$name</option>\n";
                        $groupname = $name;
                        $ordnerid = "group$key";
                } else {
                        $gruppe .= "<option value=\"$key\">$name</option>\n";
            if($doc->group_required == 1 and $gid == 0) {
            $gid = $key;
            $ordnerid = "group$key";
            $groupname = $name;
            }
                }
        }
    }
} else {
// Wenn der Dokumententyp einer bestimmten Gruppe gehört
$mygroups = $go_api->groups->myGroups();
foreach($mygroups as $key => $elem) {
        $name = $elem["name"];
                if($key == $doc->groupid) {
                        $gruppe .= "<option value=\"$key\" selected>$name</option>\n";
                        $groupname = $name;
                        $ordnerid = "group$key";
            $gid = $doc->groupid;
                }
        }
}
/**************************************
*  Ordner in Dropdownmenü Feld einfügen
**************************************/

if($ordnerid != "") $node_option = "<option value=\"$ordnerid\">$groupname</option>\n";


if($nodes = $go_api->tree->node_optionlist($gid)){
        foreach($nodes as $key => $elem) {
            $node_title = $elem["title"];
        $node_id = $elem["tree_id"];
                if($node_id == $ordner and $node_id != $id) {
                        $node_option .= "<option value=\"$node_id\" selected>$node_title</option>\n";
                } elseif ($node_id != $id) {
                        $node_option .= "<option value=\"$node_id\">$node_title</option>\n";
                }
        }
}


$groups .=
"          <tr>
            <td>
             <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
                <tr>
                  <td width=\"18%\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">
                    ".$go_api->lng("Gruppe").":</font></td>
                  <td width=\"82%\">
                    <select  name=\"gid\" onChange=\"MM_goToURL('self','edit.php?s=$s&tree_id=$tree_id&doc_id=$doc_id&doctype_id=$doctype_id&deck_id=$deck_id&action=tmp&next_doctype_id=$next_doctype_id&next_tree_id=$next_tree_id&caller_tree_id=$caller_tree_id&gid=');return document.MM_returnValue\">
                    $gruppe
                    </select>
                  </td>
                </tr>
                <tr>
                  <td width=\"18%\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$go_api->lng("Titel").":</font></td>
                  <td width=\"82%\">
                    <input type=\"text\" name=\"title\" size=\"23\" value=\"".stripslashes($item["title"])."\" maxlength=\"30\" class=\"text\">
                  </td>
                </tr>
                <tr>
                  <td width=\"18%\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$go_api->lng("Ordner").":</font></td>
                  <td width=\"82%\">
                    <select size=\"1\" name=\"ordner\">
                    $node_option
                    </select>
                  </td>
                </tr>
              </table>
             </td>
           </tr>
           <tr>
            <td>&nbsp;</td>
           </tr>";

$checkform = "
if (theForm.title.value == \"\")
  {
    alert(\"".$go_api->lng("title_required").".\");
    theForm.title.focus();
    return (false);
  }";

}

// Buttonleiste (Speichern, Abbrechen) hinzufügen
if($doc->buttons == 1) {
$buttons = '<tr>
                      <td height="46">
                        <div align="center">
                <input type="submit" name="speichern" value=" '.$go_api->lng("Speichern").' " class="button">
                &nbsp;&nbsp; <input type="submit" name="abbrechen" value=" '.$go_api->lng("Abbrechen").' " class="button" onClick="window.location.href = '."'".'../frame_start.php?s='.$s."'".';return false;">
                &nbsp;&nbsp;&nbsp;<input type="submit" name="abbrechen" value=" '.$go_api->lng("Löschen").' " class="button" onClick="window.location.href = '."'".'delete.php?tree_id='.$tree_id.'&s='.$s."'".';return false;" target="'.$formtarget.'">
              </div>
                      </td>
                    </tr>';
/*
$buttons = '<tr>
                      <td height="46">
                        <div align="center">
                <input type="image" border="0" name="speichern" src="../../design/default/btn_speichern.gif" value="1">
                &nbsp;&nbsp; <a href="../frame_start.php?s='.$s.'"><img src="../../design/default/btn_abbrechen.gif" border="0"></a>
                &nbsp;&nbsp;&nbsp;<a href="delete.php?tree_id='.$tree_id.'&s='.$s.'" target="'.$formtarget.'"><img src="../../design/default/btn_loeschen.gif" border="0"></a>
              </div>
                      </td>
                    </tr>';
*/
} else {
$buttons = "";
}

// Checkform Inhalt des Tree Moduls zuweisen
$go_api->renderer->checkform = $checkform;

if(is_array($doc->deck)){
// Zeichnen der Decks
while (list($key, $val) = each($doc->deck))
    {

    if($val->visible == 1) {

    if($deck_id == $key) {
    $deck_nav_color = "#EEEEEE";
    } else {
    $deck_nav_color = "#CCCCCC";
    }

    $deck1 .= "<td rowspan=\"3\" height=\"16\" width=\"6\" bgcolor=\"$deck_nav_color\"><img src=\"../../design/default/tab/tab_active_l.gif\" width=\"6\" height=\"16\"></td>
               <td height=\"1\" bgcolor=\"$session_nav_hcolour\"><img src=\"../../design/default/tab/x.gif\" width=\"1\" height=\"1\"></td>
               <td rowspan=\"3\" width=\"6\" bgcolor=\"$deck_nav_color\"><img src=\"../../design/default/tab/tab_active_r.gif\" width=\"6\" height=\"16\"></td>
                  ";

    $deck2 .="<td height=\"14\" bgcolor=\"$deck_nav_color\">
                    <div align=\"center\"><a href=\"javascript:changeDeck($key)\" class=\"link8\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">".$go_api->lng($val->title)."</font></a></div>
                  </td>";

    // $decks .= "<a href=\"javascript:changeDeck($key)\">". $val->title . "</a> ";

        $go_api->renderer->doc_id = $doc_id;
        $go_api->renderer->doctype_id = $doctype_id;
        $go_api->renderer->tree_id = $tree_id;
        $go_api->renderer->element_jscript_nummer = $element_jscript_nummer;

                // Hole Help File, wenn vorhanden
                $hlp_file = INCLUDE_ROOT . DIR_TRENNER ."help".DIR_TRENNER.$go_api->language."_".$doctype_id.".hlp";
                if(is_file($hlp_file)) include_once($hlp_file);


    if(is_array($val->elements) and $deck_id == $key){
        // gehe durch alle Elemente des Decks
        while (list($element_key, $element_val) = each($val->elements)){

        // wenn Element sichtbar ist
        if($element_val->visible == 1) {

        // value = Wert aus Datenbank, ansonsten Standartwert aus Objekt (Voreintrag)
        if($tablevalues[$element_val->name] != ""){
        $value = $tablevalues[$element_val->name];
        } else {
        $value = $element_val->value;
        }
        $value = stripslashes($value);

        // Switch durch Datentypen
        $mode = 'r';
        if($go_api->auth->check_perms($gid,$val->perm_read,1)) $mode = 'r';
        if($go_api->auth->check_perms($gid,$val->perm_write,1)) $mode = 'rw';

        $go_api->renderer->render_deck($doc, $element_val, $value, $mode);



        //  ****************** Ende Der Felddefinitionen ************************************


        //$input = "<input type=\"text\" name=\"textfield\" size=\"25\" maxlength=\"255\" value=\"af\">";

                $hlp_text = $hlp[$element_val->name];

                // Hilfe Element einblenden, wenn Description vorhanden
                $help_element = '';
                if($element_val->description != '' or $hlp_text != '') {
                        $help_element = " <a href=\"#\" onClick=\"javascript:window.open('".$go_info["server"]["server_url"]."/multidoc/help/index.php?s=$s&doctype_id=$doctype_id&deck_id=$deck_id&element_id=$element_key','hlp','width=300,height=300');\"><img src=\"../../design/default/icons/help14.gif\" border=\"0\" height=\"16\" width=\"14\" /></a>";
                }

        if($go_api->renderer->input != "") {
        $go_api->renderer->elements .=
        "<tr bgcolor=\"#EEEEEE\">
         <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; ".$go_api->lng($element_val->title).": "."</b></nobr></td>
         <td width=\"69%\" class=\"normal\">".$go_api->renderer->input.$help_element."</td>
        </tr>
        ";
        }


        }
        }
        } else {
        if($doctype_id == 1013){
          $go_api->content->strict(); // to allow something like "RewriteCond %{HTTP_HOST} ^.*$" in Apache Directives
        } else {
          $go_api->content->no_strict();
        }
        }
    }
    }
} else {
  if($doctype_id == 1013){
    $go_api->content->strict(); // to allow something like "RewriteCond %{HTTP_HOST} ^.*$" in Apache Directives
  } else {
    $go_api->content->no_strict();
  }
}
}


$decks = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                <tr>
                  $deck1
                  <td><img src=\"../../design/default/tab/x.gif\" width=\"1\" height=\"1\"></td>
                </tr>
                <tr>
                  $deck2
                  <td height=\"14\"><img src=\"../../design/default/tab/x.gif\" width=\"1\" height=\"1\"></td>
                </tr>
              </table>";


$go_api->content->assign( array(    GROUPS => $groups,
                                    GID_OLD => $gid_old,
                                    ELEMENTS => $go_api->renderer->elements,
                                    DECKS => $decks,
                                    BUTTONS => $buttons,
                                    CHECKFORM => $go_api->renderer->checkform));

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();

//echo $go_api->doc->utime() - $start;
exit;
?>