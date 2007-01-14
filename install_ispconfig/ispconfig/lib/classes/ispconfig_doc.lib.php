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
class doc 
{
var $userid;
var $groupid;
var $group_required = 0;
var $modul;
var $tree = 1;
var $buttons = 1;
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
var $permtype;
var $version = 1.1;
var $event_class;
var $event_insert;
var $event_update;
var $event_delete;
var $event_show;
var $wysiwyg_lib = 0;

function doc() 
	{
	// Initialisierung des Documentes, falls notwendig
		
	}
    
function doctype_get($doctype_id) 
    {
    global $go_api, $go_info;
    
	$doctype_id =intval($doctype_id);
    $row = $go_api->db->queryOneRecord("SELECT * from doctype where doctype_id = '$doctype_id'");
    return unserialize($row["doctype_def"]);
    
    }
function check($to_check, $max_len = 0)
       
       {
       // $to_check = addslashes($to_check);
       if(!is_array($to_check)) {
       //$to_check = strtr($to_check, "\"", "'");
       
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

class deck
{
var $elements;
var $title;
var $visible = 1;
var $perm_read = 'r';
var $perm_write = 'w';

function deck()
    {
    // Initialisierung deck, falls notwendig
    
    }
	
	function getElementByName($name) {
		foreach($this->elements as $id => $element) {
			if($element->name == $name) {
				reset($this->elements);
				return $this->elements[$id];
			}
		}
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
var $reg_fehler;
var $search;
}

class shortText extends element
{
var $value;
var $css_class;
var $maxlength = 255;
var $password;
var $write_once;

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
var $currency;

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

class terminField extends element
{
var $value;
var $css_class;
var $maxlength;
var $format = "d.m.Y";
var $zeit;
var $intervall;
var $erinnerung;
var $benachrichtigung;
var $bis;

function terminField($name = "")
	{
	$this->type = "terminField";
	$this->name = $name;
    $this->maxlength = 10;
	}
}

// Plugin field
class pluginField extends element
{
var $css_class;
var $options;

function pluginField($name = "")
	{
	$this->type = "pluginField";
	$this->name = $name;
	}
}
?>