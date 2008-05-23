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

if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class doc 
{
var $userid;
var $groupid;
var $modul;
var $tree = 1;
var $name;
var $type = "text/html";
var $template_type = "file";
var $template_path;
var $storage_type = "db";
var $storage_path;
var $form_width = "450";
var $deck;
var $title;
var $keywords;
var $description;
var $path;
var $icon;
var $cache = 0;

function doc() 
	{
	// Initialisierung des Documentes, falls notwendig
		
	}
}

class deck
{
var $elements;
var $title;

function deck()
    {
    // Initialisierung deck, falls notwendig
    
    }
}

class element
{
var $name;
var $type;
var $title;
var $language = "de";
var $description;
var $length = 30;
var $visible = 1;
var $required = 1;
var $reg_expression;
var $search;
}

class shortText extends element
{
var $value;
var $css_class;
var $maxlength = 255;
var $password;

function shortText($name = "")
	{
	$this->type = "shortText";
	$this->name = $name;
    $this->maxlength;
	}
}

class longText extends element
{
var $value;
var $storage_type = "db";
var $storage_path;
var $wrap = "physical";
var $css_class;
var $maxlength;
var $rows;

function longText($name = "")
	{
	$this->type = "longText";
	$this->name = $name;
	}
}

class imageField extends element
{
var $storage_type = "file";
var $storage_path;
var $height;
var $width;
var $link;
var $target;
var $css_class;

function imageField($name = "")
	{
	$this->type = "imageField";
	$this->name = $name;
	}
}

class linkField extends element
{
var $value = "http://";
var $target;
var $css_class;

function linkField($name = "")
	{
	$this->type = "linkField";
	$this->name = $name;
	}
}

class doubleField extends element
{
var $value = 0;
var $css_class;
var $maxlength;

function doubleField($name = "")
	{
	$this->type = "doubleField";
	$this->name = $name;
    $this->maxlength = 255;
	}
}

class integerField extends element
{
var $value = 0;
var $css_class;
var $maxlength;

function integerField($name = "")
	{
	$this->type = "integerField";
	$this->name = $name;
    $this->maxlength = 255;
	}
}

class dateField extends element
{
var $value;
var $css_class;
var $maxlength;
var $format = "d.m.Y";

function dateField($name = "")
	{
	$this->type = "dateField";
	$this->name = $name;
    $this->maxlength = 10;
	}
}

class fileField extends element
{
var $storage_type = "file";
var $css_class;
var $maxlength;

function fileField($name = "")
	{
	$this->type = "fileField";
	$this->name = $name;
    $this->maxlength = 255;
	}
}

// attach other Documents
class attachField extends element
{
var $value;
var $doctype;
var $view = "list"; // list / full
var $fields;
var $css_class;

function attachField($name = "")
	{
	$this->type = "attachField";
	$this->name = $name;
	}
}

// Description field
class descField extends element
{
var $value;
var $css_class;
var $alignment;

function descField($name = "")
	{
	$this->type = "descField";
	$this->name = $name;
	}
}

// Separator field
class seperatorField extends element
{
var $width;
var $css_class;

function seperatorField($name = "")
	{
	$this->type = "seperatorField";
	$this->name = $name;
	}
}

class optionField extends element
{
var $option_type; //dropdown, list, option
var $values;
var $source; //db, list
var $source_table;
var $value_field;
var $id_field;
var $css_class;
var $size;
var $multiple;
var $order;

function optionField($name = "")
	{
	$this->type = "optionField";
	$this->name = $name;
	}
}

class checkboxField extends element
{
var $value = 0;
var $css_class;

function checkboxField($name = "")
	{
	$this->type = "checkboxField";
	$this->name = $name;
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////////

class multidoc
{

function doctype_add($form, $groupid = 0) 
    {
    global $go_api, $go_info;
    
    $this->check($form);
    
    $doc = new doc();
    
    $doc->userid = $go_info["user"]["userid"];
    $doc->groupid = $form["doctype_groupid"]; 
    $doc->modul = $form["doctype_modul"];
    $doc->name = $form["doctype_name"];
    if($doc->modul != "") {
    $doc->storage_path = $doc->modul . '_' . $form["doctype_name"];
    } else {
    $doc->storage_path = $form["doctype_name"];
    }
    $doc->storage_type = $form["doctype_storage_type"];
    $doc->title = $form["doctype_title"];
    $doc->description = $form["doctype_description"];
    $doc->cache = $form["doctype_cache"];
    $doc->tree = $form["tree"];
    $doc->form_width = $form["form_width"];
    $doc->icon = $form["doctype_icon"];
    
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
    
    $go_api->db->query("CREATE TABLE ".$doc->storage_path." (doc_id BIGINT UNSIGNED not null AUTO_INCREMENT, doctype_id INT UNSIGNED DEFAULT '$doctype_id' not null, userid BIGINT UNSIGNED not null, groupid BIGINT UNSIGNED not null, PRIMARY KEY (doc_id), INDEX (doc_id,userid,groupid), UNIQUE (doc_id))");
    return $doctype_id;
    }

function doctype_update($doctype_id, $form, $groupid = 0) 
    {
    global $go_api, $go_info;
    
    $doc = $this->doctype_get($doctype_id);
    
    $this->check($form);
    
    $doc->userid = $go_info["user"]["userid"];
    $doc->groupid = $form["doctype_groupid"]; 
    $doc->modul = $form["doctype_modul"];
    $doc->name = $form["doctype_name"];
    if($doc->modul != "") {
    $doc->storage_path = $doc->modul . '_' . $form["doctype_name"];
    } else {
    $doc->storage_path = $form["doctype_name"];
    }
    $doc->storage_type = $form["doctype_storage_type"];
    $doc->title = $form["doctype_title"];
    $doc->description = $form["doctype_description"];
    $doc->cache = $form["doctype_cache"];
    $doc->tree = $form["tree"];
    $doc->form_width = $form["form_width"];
    $doc->icon = $form["doctype_icon"];
    
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
    // $doc->deck[]->title = $form["deck_title"];
    $fields = array( doctype_def => serialize($doc));
    $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
    }

function deck_update($doctype_id, $deck_id, $form) 
    {
    global $go_api, $go_info;
    $doc = $this->doctype_get($doctype_id);
    $doc->deck[$deck_id]->title = $form["deck_title"];
    $fields = array( doctype_def => serialize($doc));
    $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
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

function element_add($doctype_id, $deck_id, $form) 
    {
    global $go_api, $go_info;
    
    $doc = $this->doctype_get($doctype_id);
    
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
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." VARCHAR (".$form["maxlength"].") not null ");
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
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->rows = $form["rows"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." TEXT not null ");
    break;
    // Dokumentenabhängigkeit
    case "attachField":
    $doc->deck[$deck_id]->elements[] = new attachField($form["name"]);
    end($doc->deck[$deck_id]->elements);
    $element_id = key($doc->deck[$deck_id]->elements);
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->doctype = $form["doctype"];
    $doc->deck[$deck_id]->elements[$element_id]->fields = $form["fields"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->view = $form["view"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
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
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." VARCHAR (255) not null ");
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
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." DOUBLE not null ");
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
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." INT not null ");
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
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." VARCHAR (255) not null ");
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
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." BIGINT not null ");
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
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." ADD ".$form["name"]." TINYINT not null ");
    break;
    }
    // Document Type updaten
    $fields = array( doctype_def => serialize($doc));
    $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
    }
    
function element_update($doctype_id, $deck_id, $element_id, $form) 
    {
    global $go_api, $go_info;
    $doc = $this->doctype_get($doctype_id);
    
    switch ($form["type"]) {
    // short Text Feld hinzufügen
    case "shortText":
    $name_old = $doc->deck[$deck_id]->elements[$element_id]->name;
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
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." VARCHAR (".$form["maxlength"].") not null ");
    break;
    case "longText":
    $name_old = $doc->deck[$deck_id]->elements[$element_id]->name;
    $doc->deck[$deck_id]->elements[$element_id]->name = $form["name"];
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->length = $form["length"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->maxlength = $form["maxlength"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->rows = $form["rows"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    // die();
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." TEXT not null");
    break;
    case "attachField":
    $doc->deck[$deck_id]->elements[$element_id]->name = $form["name"];
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->doctype = $form["doctype"];
    $doc->deck[$deck_id]->elements[$element_id]->fields = $form["fields"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->view = $form["view"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $this->debug($doc);
    // die();
    break;
    case "linkField":
    $name_old = $doc->deck[$deck_id]->elements[$element_id]->name;
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
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." VARCHAR (255) not null ");
    break;
    case "doubleField":
    $name_old = $doc->deck[$deck_id]->elements[$element_id]->name;
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
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." DOUBLE not null ");
    break;
    case "integerField":
    $name_old = $doc->deck[$deck_id]->elements[$element_id]->name;
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
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." INT not null ");
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
    $name_old = $doc->deck[$deck_id]->elements[$element_id]->name;
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
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE ".$name_old." VARCHAR (255) not null ");
    break;
    case "dateField":
    $name_old = $doc->deck[$deck_id]->elements[$element_id]->name;
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
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." BIGINT not null ");
    break;
    case "fileField":
    $name_old = $doc->deck[$deck_id]->elements[$element_id]->name;
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
    $name_old = $doc->deck[$deck_id]->elements[$element_id]->name;
    $doc->deck[$deck_id]->elements[$element_id]->description = $form["description"];
    $doc->deck[$deck_id]->elements[$element_id]->value = $form["value"];
    $doc->deck[$deck_id]->elements[$element_id]->title = $form["title"];
    $doc->deck[$deck_id]->elements[$element_id]->required = $form["required"];
    $doc->deck[$deck_id]->elements[$element_id]->visible = $form["visible"];
    $doc->deck[$deck_id]->elements[$element_id]->css_class = $form["css_class"];
    $doc->deck[$deck_id]->elements[$element_id]->search = $form["search"];
    // $this->debug($doc);
    // Tabellenfeld erzeugen
    $go_api->db->query("ALTER TABLE ".$doc->storage_path." CHANGE $name_old ".$form["name"]." TINYINT not null ");
    break;
    }
    // Document Type updaten
    $fields = array( doctype_def => serialize($doc));
    $go_api->db->update("doctype",$fields,"doctype_id = '$doctype_id'");
    }
    
function element_delete($doctype_id, $deck_id, $element_id) 
    {
    global $go_api, $go_info;
        if($doctype_id != "" and $deck_id != "" and $element_id != ""){
            $doc = $this->doctype_get($doctype_id);
            $feldname = $doc->deck[$deck_id]->elements[$element_id]->name;
            $go_api->db->query("ALTER TABLE ".$doc->storage_path." DROP ".$feldname);
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
        $element2 = $doc->deck[$deck_id]->elements[$element_id - 1];
        $doc->deck[$deck_id]->elements[$element_id - 1] = $element1;
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