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

if(CONFIG_LOADED != 1) die('Direct access not permitted.');


class docadmin
{

function doctype_add($form, $groupid = 0)
    {
    global $go_api, $go_info;

    $form["doctype_name"] = strtolower($form["doctype_name"]);
    // Check ob name ungültige Zeichen enthält
    if (!preg_match("=^[a-z_\d]+$=i",$form["doctype_name"])) $go_api->errorMessage("Der Name des Eintrages enthält ungültige Zeichen.");


    $this->check($form);

    $doc = new doc();

    $doc->userid = $go_info["user"]["userid"];
    $doc->groupid = $form["doctype_groupid"];
    $doc->modul = $form["doctype_modul"];
    $doc->name = $form["doctype_name"];
    if($doc->storage_path == "") {
    $doc->storage_path = $doc->modul . '_' . $form["doctype_name"];
    } else {
    $doc->storage_path = $form["doctype_storage_path"];
    }
    $doc->storage_type = $form["doctype_storage_type"];
    $doc->title = $form["doctype_title"];
    $doc->description = $form["doctype_description"];
    $doc->cache = $form["doctype_cache"];
    $doc->tree = $form["tree"];
        $doc->buttons = $form["buttons"];
    $doc->form_width = $form["form_width"];
    $doc->icon = $form["doctype_icon"];
    $doc->permtype = $form["permtype"];
    $doc->group_required = $form["group_required"];

    $doc->event_class = $form["doctype_event_class"];
    $doc->event_insert = $form["doctype_event_insert"];
    $doc->event_update = $form["doctype_event_update"];
    $doc->event_delete = $form["doctype_event_delete"];
        $doc->event_show = $form["doctype_event_show"];

        $doc->wysiwyg_lib = $form["wysiwyg_lib"];

    $doc_serialized = serialize($doc);
    // $this->debug($doc);
    // die();
    // erstellen der DocType Definition

    $go_api->db->insert("doctype", array( userid => $doc->userid,
                                          groupid => $doc->groupid,
                                          doctype_modul => $doc->modul,
                                          doctype_name => $doc->name,
                                          doctype_title => $doc->title,
                                          doctype_def => $doc_serialized,
                                          doctype_tree => $doc->tree));
    $doctype_id = $go_api->db->insertID();
    // erstellen der Feld Tabelle

    $columns[] = array(action =>   'add',
                        name =>     'doc_id',
                        name_new => '',
                        type =>     'int64',
                        typeValue => '',
                        defaultValue =>  '',
                        notNull =>   true,
                        autoInc =>   true,
                        option =>   'primary');

    $columns[] = array(action =>   'add',
                        name =>     'doctype_id',
                        name_new => '',
                        type =>     'int64',
                        typeValue => '',
                        defaultValue =>  $doctype_id,
                        notNull =>   true,
                        autoInc =>   false,
                        option =>   'index');


    $go_api->db->createTable($doc->storage_path,$columns);
    //$go_api->db->query("CREATE TABLE ".$doc->storage_path." (doc_id BIGINT UNSIGNED not null AUTO_INCREMENT, doctype_id INT UNSIGNED DEFAULT '$doctype_id' not null, PRIMARY KEY (doc_id), INDEX (doc_id), UNIQUE (doc_id))");
    if($go_api->db->errorMessage != "") $go_api->errorMessage($go_api->db->errorMessage);
    return $doctype_id;
    }

function doctype_update($doctype_id, $form, $groupid = 0)
    {
    global $go_api, $go_info;

    $form["doctype_name"] = strtolower($form["doctype_name"]);
    // Check ob name ungültige Zeichen enthält
    if (!preg_match("=^[a-z_\d]+$=i",$form["doctype_name"])) $go_api->errorMessage("Der Name des Eintrages enthält ungültige Zeichen.");

    $doc = $this->doctype_get($doctype_id);

    $this->check($form);

    $doc->userid = $go_info["user"]["userid"];
    $doc->groupid = $form["doctype_groupid"];
    $doc->modul = $form["doctype_modul"];
    $doc->name = $form["doctype_name"];
    if($doc->storage_path == "") {
    $doc->storage_path = $doc->modul . '_' . $form["doctype_name"];
    } else {
    $doc->storage_path = $form["doctype_storage_path"];
    }

    // Teste storage path, ob Datenbanktabelle existiert
    $found_table = 0;
    $alltables = mysql_list_tables($go_info["server"]["db_name"]);
    while ($row = mysql_fetch_array($alltables)){
        if($row[0] == $doc->storage_path) $found_table = 1;
    }
    unset($row);
    if($found_table == 0) $go_api->errorMessage("Speicherpfad konnte nicht geändert werden, der Pfad oder die Datenbanktabelle existiert nicht.");

    $doc->storage_type = $form["doctype_storage_type"];
    $doc->title = $form["doctype_title"];
    $doc->description = $form["doctype_description"];
    $doc->cache = $form["doctype_cache"];
    $doc->tree = $form["tree"];
        $doc->buttons = $form["buttons"];
    $doc->form_width = $form["form_width"];
    $doc->icon = $form["doctype_icon"];
    $doc->permtype = $form["permtype"];
    $doc->group_required = $form["group_required"];

    $doc->event_class = $form["doctype_event_class"];
    $doc->event_insert = $form["doctype_event_insert"];
    $doc->event_update = $form["doctype_event_update"];
    $doc->event_delete = $form["doctype_event_delete"];
        $doc->event_show = $form["doctype_event_show"];

        $doc->wysiwyg_lib = $form["wysiwyg_lib"];

    $doc_serialized = serialize($doc);
    // $this->debug($doc);
    // die();
    // erstellen der DocType Definition

    // $this->debug($doc);
    // die();

    $go_api->db->update("doctype", array( userid => $doc->userid,
                                          groupid => $doc->groupid,
                                          doctype_modul => $doc->modul,
                                          doctype_name => $doc->name,
                                          doctype_title => $doc->title,
                                          doctype_def => $doc_serialized,
                                          doctype_tree => $doc->tree), "doctype_id = '$doctype_id'");

    if($go_api->db->errorMessage != "") $go_api->errorMessage($go_api->db->errorMessage);
    return $doctype_id;
    }

function doctype_delete($doctype_id, $groupid)
    {
    global $go_api, $go_info;

    }

function doctype_get($doctype_id)
    {
    global $go_api, $go_info;

    $row = $go_api->db->queryOneRecord("SELECT * from doctype where doctype_id = '$doctype_id'");
    return unserialize($row["doctype_def"]);

    }

function deck_add($doctype_id, $form)
    {
    global $go_api, $go_info;

    $doc = $this->doctype_get($doctype_id);
    $doc->deck[] = new deck();
    end($doc->deck);
    $deck_id = key($doc->deck);
    $doc->deck[$deck_id]->title = $form["deck_title"];
    $doc->deck[$deck_id]->visible = $form["deck_visible"];
    $doc->deck[$deck_id]->perm_read = $form["perm_read"];
    $doc->deck[$deck_id]->perm_write = $form["perm_write"];
    // $doc->deck[]->title = $form["deck_title"];
    $fields = array( doctype_def => serialize($doc));
    $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
    if($go_api->db->errorMessage != "") $go_api->errorMessage($go_api->db->errorMessage);
    }

function deck_update($doctype_id, $deck_id, $form)
    {
    global $go_api, $go_info;
    $doc = $this->doctype_get($doctype_id);
    $doc->deck[$deck_id]->title = $form["deck_title"];
    $doc->deck[$deck_id]->visible = $form["deck_visible"];
    $doc->deck[$deck_id]->perm_read = $form["perm_read"];
    $doc->deck[$deck_id]->perm_write = $form["perm_write"];
    $fields = array( doctype_def => serialize($doc));
    $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
    if($go_api->db->errorMessage != "") $go_api->errorMessage($go_api->db->errorMessage);
    return $doctype_id;
    }

function deck_delete($doctype_id, $deck_id)
    {
    global $go_api, $go_info;
        if($doctype_id != "" and $deck_id != ""){
            $doc = $this->doctype_get($doctype_id);
            unset($doc->deck[$deck_id]);
            // variable deck löschen, wenn array leer
            if(count($doc->deck) == 0) unset($doc->deck);
            // $this->debug($doc);
            // die();
            $fields = array( doctype_def => serialize($doc));
            $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
        }
    }

function deck_flip($doctype_id, $deck_id)
        {
    global $go_api, $go_info;
    $doc = $this->doctype_get($doctype_id);
    if($deck_id != 0) {
        $deck1 = $doc->deck[$deck_id];
        //nächst höheres Element suchen
        $n = $deck_id - 1;
        while($n >= 0 and !isset($doc->deck[$deck_id])){
        $n = $n - 1;
        }
        $deck2 = $doc->deck[$n];
        $doc->deck[$n] = $deck1;
        $doc->deck[$deck_id] = $deck2;
        $fields = array( doctype_def => serialize($doc));
        $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
    }
        }

function element_add($doctype_id, $deck_id, $form)
    {
    global $go_api, $go_info;

    $doc = $this->doctype_get($doctype_id);

    // überprüfen, ob Deck vorhanden
    if(!isset($doc->deck[$deck_id])) $go_api->errorMessage("Ebene nicht gefunden,<br> Bitte fügen Sie erst eine Ebene hinzu");


    $form["name"] = strtolower($form["name"]);
    // Check ob name ungültige Zeichen enthält
    if (!preg_match("=^[a-z_\d]+$=i",$form["name"])) $go_api->errorMessage("Der Name des Eintrages enthält ungültige Zeichen.");


    // Check ob name unique

    foreach($doc->deck as $decks){
        if(is_array($decks->elements)) {
        foreach($decks->elements as $elements) {
            if($elements->name == $form["name"]) $go_api->errorMessage("Das Feld wurde nicht hinzugefügt! Es besteht bereits ein gleichnamiges Feld.");
        }
        }
    }
    unset($decks);
    unset($elements);

    switch ($form["type"]) {
    // short Text Feld hinzufügen
    case "shortText":
    $doc->deck[$deck_id]->elements[] = new shortText($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->password = $form["password"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    $doc->deck[$deck_id]->elements[$element_id]->reg_expression = addslashes($form["reg_expression"]);
    $doc->deck[$deck_id]->elements[$element_id]->reg_fehler = $form["reg_fehler"];
    $doc->deck[$deck_id]->elements[$element_id]->write_once = $form["write_once"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   'add',
                        name =>     $form["name"],
                        name_new => '',
                        type =>     'varchar',
                        typeValue => $form["maxlength"],
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');

    //$go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." VARCHAR (".$form["maxlength"].") not null ");
    break;
    // long Text Feld hinzufügen
    case "longText":
    $doc->deck[$deck_id]->elements[] = new longText($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    //$doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->rows = $form["rows"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   'add',
                        name =>     $form["name"],
                        name_new => '',
                        type =>     'text',
                        typeValue => '',
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');

    //$go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." TEXT not null ");
    break;
    // Dokumentenabhängigkeit
    case "attachField":

    $val_lines = explode(";",$form["fields"]);
    $x = 0;
    while (list($key2, $val2) = each($val_lines)) {
    list($val_id,$val_val) = explode(",",$val2);
    if($val_id != "") $values[trim($val_id)] = $val_val;
    $x++;
    }

    $doc->deck[$deck_id]->elements[] = new attachField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->doctype = $form["doctype"];
    $doc->deck[$deck_id]->elements[$element_id]->fields = $values;
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->view = $form["view"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    unset($values);
    // $this->debug($doc);
    break;
    case "linkField":
    $doc->deck[$deck_id]->elements[] = new linkField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->target = $form["target"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   'add',
                        name =>     $form["name"],
                        name_new => '',
                        type =>     'varchar',
                        typeValue => 255,
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    //$go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." VARCHAR (255) not null ");
    break;
    case "doubleField":
    $doc->deck[$deck_id]->elements[] = new doubleField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    $doc->deck[$deck_id]->elements[$element_id]->currency = $form["currency"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   'add',
                        name =>     $form["name"],
                        name_new => '',
                        type =>     'double',
                        typeValue => '',
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    //$go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." DOUBLE not null ");
    break;
    case "integerField":
    $doc->deck[$deck_id]->elements[] = new integerField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   'add',
                        name =>     $form["name"],
                        name_new => '',
                        type =>     'int32',
                        typeValue => '',
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    //$go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." INT not null ");
    break;
    case "descField":
    $doc->deck[$deck_id]->elements[] = new descField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->alignment = $form["alignment"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    // $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    // $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." TEXT not null ");
    break;
    case "seperatorField":
    $doc->deck[$deck_id]->elements[] = new seperatorField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->width = $form["width"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    break;
    case "optionField":

    $val_lines = explode(";",$form["values"]);
    $x = 0;
    while (list($key2, $val2) = each($val_lines)) {
    list($val_id,$val_val) = explode(",",$val2);
    if($val_id != "") $values[trim($val_id)] = $val_val;
    $x++;
    }
    $doc->deck[$deck_id]->elements[] = new optionField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->option_type = $form["option_type"];
    $doc->deck[$deck_id]->elements[$element_id]->values = $values;
    $doc->deck[$deck_id]->elements[$element_id]->source = $form["source"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->source_table = $form["source_table"];
    $doc->deck[$deck_id]->elements[$element_id]->value_field = $form["value_field"];
    $doc->deck[$deck_id]->elements[$element_id]->id_field = $form["id_field"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->size = $form["size"];
    $doc->deck[$deck_id]->elements[$element_id]->multiple = $form["multiple"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    $doc->deck[$deck_id]->elements[$element_id]->order = $form["order"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   'add',
                        name =>     $form["name"],
                        name_new => '',
                        type =>     'varchar',
                        typeValue => 255,
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    //$go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." VARCHAR (255) not null ");
    break;
    case "dateField":
    $doc->deck[$deck_id]->elements[] = new dateField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->format = $form["format"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   'add',
                        name =>     $form["name"],
                        name_new => '',
                        type =>     'int64',
                        typeValue => '',
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    //$go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." BIGINT not null ");
    break;
    case "fileField":
    $doc->deck[$deck_id]->elements[] = new fileField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->storage_type = $form["storage_type"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    // $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." VARCHAR (".$form["maxlength"].") not null ");
    break;
    case "checkboxField":
    $doc->deck[$deck_id]->elements[] = new checkboxField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   'add',
                        name =>     $form["name"],
                        name_new => '',
                        type =>     'char',
                        typeValue => 1,
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    //$go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." TINYINT not null ");
    break;
    case "terminField":
    $doc->deck[$deck_id]->elements[] = new terminField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->format = $form["format"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    $doc->deck[$deck_id]->elements[$element_id]->zeit = $form["zeit"];
    $doc->deck[$deck_id]->elements[$element_id]->intervall = $form["intervall"];
    $doc->deck[$deck_id]->elements[$element_id]->erinnerung = $form["erinnerung"];
    $doc->deck[$deck_id]->elements[$element_id]->benachrichtigung = $form["benachrichtigung"];
    $doc->deck[$deck_id]->elements[$element_id]->bis = $form["bis"];
    //$this->debug($doc);
    // Tabellenfeld erzeugen
    //$go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." BIGINT not null ");
    break;
    case "pluginField":
    $doc->deck[$deck_id]->elements[] = new pluginField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->options = $form["options"];
    //$doc->deck[$deck_id]->elements[$element_id]->width = $form["width"];
    //$doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    break;
    }
    if(is_array($columns)) $go_api->db->alterTable($doc->storage_path,$columns);

    if($go_api->db->errorMessage != "") $go_api->errorMessage($go_api->db->errorMessage);
    // Document Type updaten
    $fields = array( doctype_def => serialize($doc));
    $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
    if($go_api->db->errorMessage != "") $go_api->errorMessage($go_api->db->errorMessage);
    }

function element_update($doctype_id, $deck_id, $element_id, $form)
    {
    global $go_api, $go_info;
    $doc = $this->doctype_get($doctype_id);

    $form["name"] = strtolower($form["name"]);
    // Check ob name ungültige Zeichen enthält
    if (!preg_match("=^[a-z_\d]+$=i",$form["name"])) $go_api->errorMessage("Der Name des Eintrages enthält ungültige Zeichen.");

    // feststellen, ob feld existiert
    $doctype_table = $doc->storage_path;

    // Tabellenfelder einlesen
    $result = @mysql_list_fields($go_info["server"]["db_name"],$doctype_table);
    $fields = @mysql_num_fields ($result);
    $i = 0;
    $table = @mysql_field_table ($result, $i);
    $is_in_table = 0;
    $name_old = $doc->deck[$deck_id]->elements[$element_id]->name;
    while ($i < $fields) {
        $name  = @mysql_field_name($result, $i);
        if($name == $name_old) $is_in_table = 1;
        $i++;
    }

    if($is_in_table == 1) {
    $action = 'alter';
    } else {
    $action = 'add';
    }

    // überprüfen, ob Deck vorhanden
    if(!isset($doc->deck[$deck_id])) $go_api->errorMessage("Ebene nicht gefunden,<br> Bitte fügen Sie erst eine Ebene hinzu");

    switch ($form["type"]) {
    // short Text Feld hinzufügen
    case "shortText":
    $doc->deck[$deck_id]->elements[$element_id]->name = $form["name"];
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->password = $form["password"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    $doc->deck[$deck_id]->elements[$element_id]->reg_expression = addslashes($form["reg_expression"]);
    $doc->deck[$deck_id]->elements[$element_id]->reg_fehler = $form["reg_fehler"];
    $doc->deck[$deck_id]->elements[$element_id]->write_once = $form["write_once"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen

    $columns[] = array(action =>   $action,
                        name =>     $name_old,
                        name_new => $form["name"],
                        type =>     'varchar',
                        typeValue => $form["maxlength"],
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    /*
    if($is_in_table == 1) {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." VARCHAR (".$form["maxlength"].") not null ");
    } else {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." VARCHAR (".$form["maxlength"].") not null ");
    }
    */

    break;
    case "longText":
    $doc->deck[$deck_id]->elements[$element_id]->name = $form["name"];
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    //$doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->rows = $form["rows"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    // die();
    $columns[] = array(action =>   $action,
                        name =>     $name_old,
                        name_new => $form["name"],
                        type =>     'text',
                        typeValue => '',
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    /*
    if($is_in_table == 1) {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." TEXT not null");
    } else {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." TEXT not null ");
    }
    */
    break;
    case "attachField":

    $val_lines = explode(";",$form["fields"]);
    $x = 0;
    while (list($key2, $val2) = each($val_lines)) {
    list($val_id,$val_val) = explode(",",$val2);
    if($val_id != "") $values[trim($val_id)] = $val_val;
    $x++;
    }

    $doc->deck[$deck_id]->elements[$element_id]->name = $form["name"];
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->doctype = $form["doctype"];
    $doc->deck[$deck_id]->elements[$element_id]->fields = $values;
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->view = $form["view"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    //$this->debug($doc);
    unset($values);
    // die();
    break;
    case "linkField":
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->target = $form["target"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   $action,
                        name =>     $name_old,
                        name_new => $form["name"],
                        type =>     'varchar',
                        typeValue => 255,
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    /*
    if($is_in_table == 1) {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." VARCHAR (255) not null ");
    } else {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." VARCHAR (255) not null ");
    }
    */
    break;
    case "doubleField":
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    $doc->deck[$deck_id]->elements[$element_id]->currency = $form["currency"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   $action,
                        name =>     $name_old,
                        name_new => $form["name"],
                        type =>     'double',
                        typeValue => '',
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    /*
    if($is_in_table == 1) {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." DOUBLE not null ");
    } else {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." DOUBLE not null ");
    }
    */
    break;
    case "integerField":
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   $action,
                        name =>     $name_old,
                        name_new => $form["name"],
                        type =>     'int32',
                        typeValue => '',
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    /*
    if($is_in_table == 1) {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." INT not null ");
    } else {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." INT not null ");
    }
    */
    break;
    case "descField":
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->alignment = $form["alignment"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    // $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    // $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." TEXT not null ");
    break;
    case "seperatorField":
    $doc->deck[$deck_id]->elements[$element_id]->width = $form["width"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    break;
    case "optionField":

    $val_lines = explode(";",$form["values"]);
    // $val_lines = str_replace("\n","",$val_lines);
    while (list($key2, $val2) = each($val_lines)) {
    list($val_id,$val_val) = explode(",",$val2);
    if($val_id != "") $values[trim($val_id)] = $val_val;
    $x++;
    }

    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->option_type = $form["option_type"];
    $doc->deck[$deck_id]->elements[$element_id]->values = $values;
    $doc->deck[$deck_id]->elements[$element_id]->source = $form["source"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->source_table = $form["source_table"];
    $doc->deck[$deck_id]->elements[$element_id]->value_field = $form["value_field"];
    $doc->deck[$deck_id]->elements[$element_id]->id_field = $form["id_field"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->size = $form["size"];
    $doc->deck[$deck_id]->elements[$element_id]->multiple = $form["multiple"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    $doc->deck[$deck_id]->elements[$element_id]->order = $form["order"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   $action,
                        name =>     $name_old,
                        name_new => $form["name"],
                        type =>     'varchar',
                        typeValue => 255,
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    /*
    if($is_in_table == 1) {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE ".$name_old." VARCHAR (255) not null ");
    } else {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." VARCHAR (255) not null ");
    }
    */
    break;
    case "dateField":
    $doc->deck[$deck_id]->elements[$element_id]->name = $form["name"];
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->format = $form["format"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   $action,
                        name =>     $name_old,
                        name_new => $form["name"],
                        type =>     'int64',
                        typeValue => '',
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    /*
    if($is_in_table == 1) {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." BIGINT not null ");
    } else {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." BIGINT not null ");
    }
    */
    break;
    case "fileField":
    $doc->deck[$deck_id]->elements[$element_id]->name = $form["name"];
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->storage_type = $form["storage_type"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    // $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." VARCHAR (".$form["maxlength"].") not null ");
    break;
    case "checkboxField":
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $columns[] = array(action =>   $action,
                        name =>     $name_old,
                        name_new => $form["name"],
                        type =>     'int16',
                        typeValue => '',
                        defaultValue =>  '',
                        notNull =>   false,
                        autoInc =>   false,
                        option =>   '');
    /*
    if($is_in_table == 1) {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." TINYINT not null ");
    } else {
        $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." TINYINT not null ");
    }
    */
    break;
    case "terminField":
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->format = $form["format"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    $doc->deck[$deck_id]->elements[$element_id]->zeit = $form["zeit"];
    $doc->deck[$deck_id]->elements[$element_id]->intervall = $form["intervall"];
    $doc->deck[$deck_id]->elements[$element_id]->erinnerung = $form["erinnerung"];
    $doc->deck[$deck_id]->elements[$element_id]->benachrichtigung = $form["benachrichtigung"];
    $doc->deck[$deck_id]->elements[$element_id]->bis = $form["bis"];
    //$this->debug($doc);
    // Tabellenfeld erzeugen
    //$go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." BIGINT not null ");
    break;
    case "pluginField":
    $doc->deck[$deck_id]->elements[$element_id]->name = $form["name"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->options = $form["option"];
    //die(print_r($doc));
    break;
    }

    if(is_array($columns)) $go_api->db->alterTable($doc->storage_path,$columns);

    if($go_api->db->errorMessage != "") $go_api->errorMessage($go_api->db->errorMessage);
    // Document Type updaten
    $fields = array( doctype_def => serialize($doc));
    $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
    if($go_api->db->errorMessage != "") $go_api->errorMessage($go_api->db->errorMessage);
    }

function element_delete($doctype_id, $deck_id, $element_id)
    {
    global $go_api, $go_info;
        if($doctype_id != "" and $deck_id != "" and $element_id != ""){
            $doc = $this->doctype_get($doctype_id);
            $feldname = $doc->deck[$deck_id]->elements[$element_id]->name;
            $columns[] = array( action =>   'drop',
                                name =>     $feldname);
            $go_api->db->alterTable($doc->storage_path,$columns);
            //$go_api->db->query("ALTER TABLE ".$doc->storage_path." DROP ".$feldname);
            unset($doc->deck[$deck_id]->elements[$element_id]);
            // variable elements löschen, wenn array leer
            if(count($doc->deck[$deck_id]->elements) == 0) unset($doc->deck[$deck_id]->elements);
            $fields = array( doctype_def => serialize($doc));
            $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
        }
    }

function element_flip($doctype_id, $deck_id, $element_id)
{
    global $go_api, $go_info;
    $doc = $this->doctype_get($doctype_id);
    if($element_id != 0) {
        $element1 = $doc->deck[$deck_id]->elements[$element_id];
        //nächst höheres Element suchen
        $n = $element_id - 1;
        while($n >= 0 and !isset($doc->deck[$deck_id]->elements[$n])){
        $n = $n - 1;
        }
        $element2 = $doc->deck[$deck_id]->elements[$n];
        $doc->deck[$deck_id]->elements[$n] = $element1;
        $doc->deck[$deck_id]->elements[$element_id] = $element2;
        $fields = array( doctype_def => serialize($doc));
        $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
    }
}

function check($to_check, $max_len = 0)

       {
       // $to_check = addslashes($to_check);
       if(!is_array($to_check)) {
       $to_check = strtr($to_check, "\"", "'");

            if($max_len != 0)
                   {
                   $to_check = substr($to_check,0,$max_len);
                   }
        }
       return $to_check;
       }

function debug($dbg)
    {
    print("<pre>&quot;");
    print_r( $dbg );
    print("&quot;</pre>" );
    }

function utime ()
        {
                $time = explode( " ", microtime());
                $usec = (double)$time[0];
                $sec = (double)$time[1];
                return $sec + $usec;
    }

}
?>