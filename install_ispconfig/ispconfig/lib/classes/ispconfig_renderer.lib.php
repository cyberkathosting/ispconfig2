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

class renderer
{
   var $input;
   var $elements;
   var $checkform;
   var $element_jscript_nummer = 0;
   var $doc_id;
   var $doctype_id;
   var $tree_id;
        
        
        function render_deck(&$doc, &$element_val, &$value, $mode)
        {
         global $go_api, $go_info;
                
        $element_jscript_nummer = $this->element_jscript_nummer;
        $doc_id = $this->doc_id;
        $tree_id = $this->tree_id;
        
        switch ($element_val->type) {
        //****************** kleines Textfeld ****************************************************
        case "shortText":
        
        $this->shortText(&$doc, &$element_val, &$value, $mode);
        
        break;
        //****************großes Textfeld *******************************************************
        case "longText":
        
        $this->longText(&$doc, &$element_val, &$value, $mode);
        
        break;
        //****************Dokumentenabhängigkeit ************************************************
        case "attachField":
        
        $this->attachField(&$doc, &$element_val, &$value, $mode);
        
        break;
        //**************************** Link Feld ***********************************************************************
        case "linkField":
        
        $this->linkField(&$doc, &$element_val, &$value, $mode);
        
        break;
        //*************************** Double Feld ************************************************************************
        case "doubleField":
        $this->doubleField(&$doc, &$element_val, &$value, $mode);
        break;
        //***************************Integer Feld ***********************************************************************
        case "integerField":
        $this->integerField(&$doc, &$element_val, &$value, $mode);
        break;
        //************************** Beschreibungsfeld **********************************************************
        case "descField":
        $this->descField(&$doc, &$element_val, &$value, $mode);
        break;
        //************************** Trennstrich ********************************************************************
        case "seperatorField":
        $this->seperatorField(&$doc, &$element_val, &$value, $mode);
        break;
        //************** Optionsfeld ***********************************************************************************
        case "optionField":
        $this->optionField(&$doc, &$element_val, &$value, $mode);
        break;
        //***************************** Datumsfeld ****************************************************************************
        case "dateField":
        $this->dateField(&$doc, &$element_val, &$value, $mode);
        break;
        //************************** Dateifeld ********************************************
        case "fileField":
        $this->fileField(&$doc, &$element_val, &$value, $mode);
        break;
        //************************ Checkbox ************************************************
        case "checkboxField":
        $this->checkboxField(&$doc, &$element_val, &$value, $mode);
        break;
        //************************** Terminfeld ********************************************
        case "terminField":
        $this->terminField(&$doc, &$element_val, &$value, $mode);
        break;
        //************************** Plugin ********************************************
        case "pluginField":
        $this->pluginField(&$doc, &$element_val, &$value, $mode);
        break;
        }
        //$this->element_jscript_nummer += $element_jscript_nummer;
        }
        
        
    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////
    // shortText Feld
    ////////////////////////////////////////
        
    function shortText(&$doc, &$element_val, &$value, $mode) 
    
    {
        global $go_api;
        if($mode == 'rw') {
            if($element_val->password == 1) {
                $inputtype = "password";
                $value = "";
            }else {
                $inputtype = "text";
            }
        
            if($element_val->write_once == 1 and trim($value) != "") {
                $this->input = $value;
            } else {
            
                $this->input = '<input type="'.$inputtype.'" name="form['.$element_val->name.
                        ']" size="'.$element_val->length.
                        '" maxlength="'.$element_val->maxlength.'" value="'.$value.'" class="text">';
                if($element_val->required == 1) {
                $this->checkform .= "
                    if (document.forms[0].elements[".$this->element_jscript_nummer."].value == '')
                    {
                        alert(\"".$go_api->lng("Geben Sie einen Wert in das Feld")." '".$element_val->title."' ".$go_api->lng("jscript_ein").".\");
                        document.forms[0].elements[".$this->element_jscript_nummer."].focus();
                        return (false);
                    }";
                }
            $this->element_jscript_nummer++;
        
        }
        }
        
            if ($mode == 'r') {
        
            if($element_val->password == 1) $value = "";
            $this->input = $value." ";

            } 
        
    }
    
    ////////////////////////////////////////
    // LongText Feld
    ////////////////////////////////////////
    
    function longText(&$doc, &$element_val, &$value, $mode) 
    
    {
    global $go_api;
    if($mode == 'rw') {
    
        $this->input = '<textarea name="form['.$element_val->name.
                    ']" cols="'.$element_val->length.
                    '" rows="'.$element_val->rows.
                    '">'.$value.
                    '</textarea>';
            if($element_val->required == 1) {
            $this->checkform .= "
                if (document.forms[0].elements[".$this->element_jscript_nummer."].value == '')
                {
                    alert(\"".$go_api->lng("Geben Sie einen Wert in das Feld")." '".$element_val->title."' ".$go_api->lng("jscript_ein").".\");
                    document.forms[0].elements[".$this->element_jscript_nummer."].focus();
                    return (false);
                }";
            }
            $this->element_jscript_nummer++;
      }
      
      if($mode == 'r') {
      $this->input = $value;
      
      }
    }
    
    ////////////////////////////////////////
    // attachField Feld
    ////////////////////////////////////////
    
    function attachField(&$doc, &$element_val, &$value, $mode) 
    
    {
    global $go_api,$go_info;
    
    if($mode == 'rw') {
    
        $this->input = "";
        // ATTACHMENT FELD
        if($this->doc_id != ""){
        $attachment_hinzufuegen = 
        '<input type="submit" name="abbrechen" value=" '.$go_api->lng("Neu").' " class="button" onClick="attachDoc('.$element_val->doctype.','."'".'doc'."'".')"; return false;">';
        //"<a href=\"javascript:attachDoc(".$element_val->doctype.",'doc')\"><img src=\"../../design/default/btn_neu.gif\" border=\"0\"></a>";
        }
        $this->elements .= 
        "
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\">&nbsp;</td>
        </tr>
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\" class=\"t2b\">&nbsp; ".$go_api->lng($element_val->title)."&nbsp; $attachment_hinzufuegen</td>
        </tr>
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\"><hr noshade size=\"1\"></td>
        </tr>
        ";
        $tempdoc = $go_api->doc->doctype_get($element_val->doctype);
        $attach_table = $tempdoc->storage_path;
        unset($tempdoc);
        $sql = "SELECT * from ".$go_info["modul"]["table_name"]."_nodes , ".$go_info["modul"]["table_name"]."_dep , $attach_table where $attach_table.doc_id = ".$go_info["modul"]["table_name"]."_nodes.doc_id and $attach_table.doctype_id = ".$go_info["modul"]["table_name"]."_nodes.doctype_id and $attach_table.doc_id = ".$go_info["modul"]["table_name"]."_dep.child_doc_id and ".$go_info["modul"]["table_name"]."_dep.child_doctype_id = ".$element_val->doctype." and ".$go_info["modul"]["table_name"]."_nodes.status = '1' and ".$go_info["modul"]["table_name"]."_dep.parent_tree_id = ".$this->tree_id;
        //echo $sql;
        $rows = $go_api->db->queryAllRecords($sql);
        if(is_array($rows)){
            $this->elements .= "<tr bgcolor=\"#EEEEEE\"><td colspan=\"2\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr>";
            if(is_array($element_val->fields)) {
            // neue Methode, mit Spaltembeschreibung
                foreach($element_val->fields as  $feldkey => $feldvalue){
                    $this->elements .= "<td class=\"t2b\">&nbsp; " . $go_api->lng($feldvalue) . "&nbsp; </td>";
                }            
                $this->elements .= "</tr>";
            
            // Spalten füllen
                $mycol = 0;
                while (list($row_key, $row_val) = each($rows)){
                    $this->elements .= "<tr>";
                        foreach($element_val->fields as  $feldkey => $feldvalue){
                            if($mycol == 0) {
                                $this->elements .= "<td>&nbsp; <a href=\"javascript:editDoc(".$row_val["child_tree_id"].",'doc')\" class=\"".$element_val->css_class."\" >".$row_val[$feldkey] . "</a>&nbsp; </td>";
                            } else {
                                $this->elements .= "<td class=\"".$element_val->css_class."\">&nbsp; ".$go_api->lng($row_val[$feldkey])."&nbsp; </td>";
                            }
                            $mycol++;
                        }
                        $this->elements .= "</tr>";
                        $mycol = 0;
                }
                
            } else {
            // Alte Methode, ohne Beschreibung der spalten
                $fieldarray = explode(",",$element_val->fields);
                    if(is_array($fieldarray)){
                        foreach($fieldarray as  $fieldarray_key => $fieldarray_val){
                        $this->elements .= "<td class=\"t2b\">&nbsp; ".$go_api->lng($fieldarray_val) . "&nbsp; </td>";
                        }
                    } else {
                    $this->elements .= "<td class=\"t2b\">&nbsp; ".$go_api->lng($fieldarray) . "&nbsp; </td>";
                    };            
                $this->elements .= "</tr>";
                
                // Spalten füllen
                while (list($row_key, $row_val) = each($rows)){
                    //edit.php?doctype_id=".$row_val["doctype_id"]."&doc_id=".$row_val["doc_id"]."&parent_id=$doc_id&s=$s
                    $this->elements .= 
                    "<tr>";
                        if(is_array($fieldarray)){
                            foreach($fieldarray as  $fieldarray_key => $fieldarray_val){
                                if($mycol == 0) {
                                    $this->elements .= "<td>&nbsp; <a href=\"javascript:editDoc(".$row_val["child_tree_id"].",'doc')\" class=\"".$element_val->css_class."\" >".$row_val[$fieldarray_val] . "</a>&nbsp; </td>";
                                } else {
                                    $this->elements .= "<td class=\"".$element_val->css_class."\">&nbsp; ".$row_val[$fieldarray_val] . "&nbsp; </td>";
                                }
                                $mycol++;
                            }
                        } else {
                            $this->elements .= "<td><a href=\"javascript:editDoc(".$row_val["child_tree_id"].",'doc')\" class=\"".$element_val->css_class."\" >".$row_val[$element_val->fields]. "&nbsp; </a></td>";
                        };
                        $this->elements .= "<tr>";
                        $mycol = 0;
                    }
                }
        $this->elements .= "</table></td></tr>";
        }
        $this->elements .= "
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\"><hr noshade size=\"1\"></td>
        </tr>";
      }
      
      // Read Only MOde
      if($mode == 'r') {
      
      $this->input = "";
        // ATTACHMENT FELD
        $this->elements .= 
        "
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\">&nbsp;</td>
        </tr>
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\" class=\"t2b\">&nbsp; ".$element_val->title."&nbsp;</td>
        </tr>
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\"><hr noshade size=\"1\"></td>
        </tr>
        ";
        $tempdoc = $go_api->doc->doctype_get($element_val->doctype);
        $attach_table = $tempdoc->storage_path;
        unset($tempdoc);
        //$sql = "SELECT * from multidoc_nodes , $attach_table where $attach_table.doc_id = multidoc_nodes.doc_id and multidoc_nodes.doctype_id = ".$element_val->doctype." and multidoc_nodes.parent = ".$tree_id;
        $sql = "SELECT * from ".$go_info["modul"]["table_name"]."_dep , $attach_table where $attach_table.doc_id = ".$go_info["modul"]["table_name"]."_dep.child_doc_id and ".$go_info["modul"]["table_name"]."_dep.child_doctype_id = ".$element_val->doctype." and ".$go_info["modul"]["table_name"]."_dep.parent_tree_id = ".$tree_id;
        //echo $sql;
        $rows = $go_api->db->queryAllRecords($sql);
        if(is_array($rows)){
        $fieldarray = explode(",",$element_val->fields);
        $this->elements .= "<tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\">
                <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                    <tr>";
        if(is_array($fieldarray)){
                foreach($fieldarray as  $fieldarray_key => $fieldarray_val){
                $this->elements .= "<td class=\"t2b\">&nbsp; ".$fieldarray_val . "&nbsp; </td>";
                }
            } else {
            $this->elements .= "<td class=\"t2b\">&nbsp; ".$fieldarray . "&nbsp; </td>";
            };            
        $this->elements .= "
                    </tr>";
        
        
        while (list($row_key, $row_val) = each($rows)){
        //edit.php?doctype_id=".$row_val["doctype_id"]."&doc_id=".$row_val["doc_id"]."&parent_id=$doc_id&s=$s
        $this->elements .= 
        "<tr>";
            if(is_array($fieldarray)){
                foreach($fieldarray as  $fieldarray_key => $fieldarray_val){
                
                $this->elements .= "<td class=\"".$element_val->css_class."\">&nbsp; ".$row_val[$fieldarray_val] . "&nbsp; </td>";
        
                }
            } else {
            $this->elements .= "<td>".$row_val[$element_val->fields]. "&nbsp; </td>";
            };
            $this->elements .= "<tr>";
            $mycol = 0;
            
        }
        $this->elements .= "</table></td>
        </tr>
        ";
        
        }
        $this->elements .= "
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\"><hr noshade size=\"1\"></td>
        </tr>";
      }
    }
    
    ////////////////////////////////////////
    // Link Feld
    ////////////////////////////////////////
    
    function linkField(&$doc, &$element_val, &$value, $mode) 
    
    {
    global $go_api;
    if($mode == 'rw') {
    
        $this->input = "<nobr><input type=\"text\" name=\"form[".$element_val->name.
                 "]\" size=\"".$element_val->length.
                 "\" maxlength=\"".$element_val->maxlength."\" value=\"".$value."\" class=\"text\"> [<a href=\"#\" onClick=\"window.open(document.forms[0].elements[".$this->element_jscript_nummer."].value)\">".$go_api->lng("Öffnen")."</a>]</nobr>";
        if($element_val->required == 1) {
        $this->checkform .= "
            if (document.forms[0].elements[".$this->element_jscript_nummer."].value == '')
            {
                alert(\"".$go_api->lng("Geben Sie einen Wert in das Feld")." '".$element_val->title."' ".$go_api->lng("jscript_ein").".\");
                document.forms[0].elements[".$this->element_jscript_nummer."].focus();
                return (false);
            }";
        }
        $this->element_jscript_nummer++;
      }
      
      if($mode == 'r') {
      $this->input = $value;
      }
    }
    
    ////////////////////////////////////////
    // Double Feld
    ////////////////////////////////////////
    
    function doubleField(&$doc, &$element_val, &$value, $mode) 
    
    {
    global $go_api;
    if($mode == 'rw') {
    
        // formatiere Währung:
        $value = str_replace (".", ",", sprintf ("%01.2f", $value));
    
        $this->input = "<input type=\"text\" name=\"form[".$element_val->name.
                 "]\" size=\"".$element_val->length.
                 "\" maxlength=\"".$element_val->maxlength."\" value=\"".$value."\" class=\"text\"> ".$element_val->currency;
        if($element_val->required == 1) {
        $this->checkform .= "
            if (document.forms[0].elements[".$this->element_jscript_nummer."].value == '')
            {
                alert(\"".$go_api->lng("Geben Sie einen Wert in das Feld")." '".$element_val->title."' ".$go_api->lng("jscript_ein").".\");
                document.forms[0].elements[".$this->element_jscript_nummer."].focus();
                return (false);
            }";
        }
        $this->element_jscript_nummer++;
      }
      
      if($mode == 'r') {
      $this->input = $value . " ".$element_val->currency;
      }
    }
    
    ////////////////////////////////////////
    // Integer Feld
    ////////////////////////////////////////
    
    function integerField(&$doc, &$element_val, &$value, $mode) 
    
    {
    global $go_api;
    if($mode == 'rw') {
    
        $this->input = "<input type=\"text\" name=\"form[".$element_val->name.
                 "]\" size=\"".$element_val->length.
                 "\" maxlength=\"".$element_val->maxlength."\" value=\"".$value."\" class=\"text\">";
        if($element_val->required == 1) {
        $this->checkform .= "
            if (document.forms[0].elements[".$this->element_jscript_nummer."].value == '')
            {
                alert(\"".$go_api->lng("Geben Sie einen Wert in das Feld")." '".$element_val->title."' ".$go_api->lng("jscript_ein").".\");
                document.forms[0].elements[".$this->element_jscript_nummer."].focus();
                return (false);
            }";
        }
        $this->element_jscript_nummer++;
      }
      
      if($mode == 'r') {
      $this->input = $value;
      }
    }
    
    ////////////////////////////////////////
    // Description Feld
    ////////////////////////////////////////
    
    function descField(&$doc, &$element_val, &$value, $mode) 
    
    {
    global $go_api;
    $this->input = "";
        $this->elements .= 
        "<tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\" align=\"center\"><table width=\"95%\"><tr><td class=\"".$element_val->css_class."\" align=\"".$element_val->alignment."\">".$go_api->lng($element_val->value)."</td></tr></table></td>
        </tr>
        ";
    }
    
    ////////////////////////////////////////
    // Trennlinie
    ////////////////////////////////////////
    
    function seperatorField(&$doc, &$element_val, &$value, $mode) 
    
    {
        $this->input = "";
        $this->elements .= 
        "<tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\" class=\"".$element_val->css_class."\"><hr noshade size=\"".$element_val->width."\"></td>
        </tr>
        ";
    }
    
    ////////////////////////////////////////
    // OptionsFeld
    ////////////////////////////////////////
    
    function optionField(&$doc, &$element_val, &$value, $mode) 
    
    {
    global $go_api,$go_info;
    
    if($mode == 'rw') {
    $this->input = "";
        if($element_val->size != "") {
        $size = "size=\"".$element_val->size."\"";
        } else {
        $size = "";
        }
        
        
        
        if($element_val->multiple != "") {
        $multiple = "multiple";
        $multiple_array = "[]";
        $value = explode(",",$value);
        } else {
        $multiple = "";
        $multiple_array = "";
        }
        
        // Wenn auflistung aus Liste kommt
        if($element_val->source == "list") {
            // Typ: Dropdown
            if($element_val->option_type == "dropdown") {
                $this->input = "<select name=\"form[".$element_val->name."]$multiple_array\" $size $multiple>";
                if($element_val->required != 1) $this->input .= "<option value=\"\">&nbsp;</option>";
                foreach ($element_val->values as $key2 => $val2) {
                  if($key2 != '' or $key2 == '0') {
                    if(is_array($value)) {
                        if(in_array(trim($key2),$value)) {
                            $this->input .= "<option value=\"$key2\" SELECTED>".$go_api->lng($val2)."</option>";
                        } else {
                            $this->input .= "<option value=\"$key2\">".$go_api->lng($val2)."</option>";
                        }
                    } elseif(!is_array($value)) {
                        
                        if($key2 == $value) {
                            $this->input .= "<option value=\"$key2\" SELECTED>".$go_api->lng($val2)."</option>";
                        } else {
                            $this->input .= "<option value=\"$key2\">".$go_api->lng($val2)."</option>";
                        }
                    }
                  }
                }

                $this->input .= "</select>";
                $this->element_jscript_nummer++;
            }
            // Typ: Options oder Checkboxfeld
            if($element_val->option_type == "option") {
                while (list($key2, $val2) = each($element_val->values)) {
                    if(is_array($value) and $key2 != "") {
                        if(in_array(trim($key2),$value)) {
                            $this->input .= "<input type=\"checkbox\" name=\"form[".$element_val->name."][]\" value=\"$key2\" checked>".$go_api->lng($val2)."<br>";
                        } else {
                            $this->input .= "<input type=\"checkbox\" name=\"form[".$element_val->name."][]\" value=\"$key2\">".$go_api->lng($val2)."<br>";
                        }
                    } elseif(!is_array($value) and $key2 != "") {
                        if($key2 == $value) {
                            $this->input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"$key2\" checked>".$go_api->lng($val2)."<br>";
                        } else {
                            $this->input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"$key2\">".$go_api->lng($val2)."<br>";
                        }
                    }
                }
            }
        }
        // Wenn Inhalt aus Datenbank kommt
        if($element_val->source == "db") {
            if($element_val->order == 1) $order_string = " order by ".$element_val->value_field;
        $sql = "SELECT ".$element_val->value_field.",".$element_val->id_field." from ".$element_val->source_table . $order_string;
        $rows = $go_api->db->queryAllRecords($sql);
            // Typ Dropdown
            if($element_val->option_type == "dropdown") {
                $this->input = "<select name=\"form[".$element_val->name."]\" $size>";
                if($element_val->required != 1) $this->input .= "<option value=\"\">&nbsp;</option>";
                if(is_array($rows)) {
                while (list($key2, $val2) = each($rows)) {
                    if(is_array($value) and $val2[$element_val->id_field] != "") {
                        if(in_array(trim($val2[$element_val->id_field]),$value)) {
                            $this->input .= "<option value=\"".$val2[$element_val->id_field]."\" SELECTED>".$go_api->lng($val2[$element_val->value_field])."</option>";
                        } else {
                            $this->input .= "<option value=\"".$val2[$element_val->id_field]."\">".$go_api->lng($val2[$element_val->value_field])."</option>";
                        }
                    } elseif(!is_array($value) and $val2[$element_val->id_field] != "") {
                        if($val2[$element_val->id_field] == $value) {
                            $this->input .= "<option value=\"".$val2[$element_val->id_field]."\" SELECTED>".$go_api->lng($val2[$element_val->value_field])."</option>";
                        } else {
                            $this->input .= "<option value=\"".$val2[$element_val->id_field]."\">".$go_api->lng($val2[$element_val->value_field])."</option>";
                        }
                    }
                }
                }
                /*
                while (list($key2, $val2) = each($rows)) {
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] == $value) $this->input .= "<option value=\"".$val2[$element_val->id_field]."\" SELECTED>".$val2[$element_val->value_field]."</option>";
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] != $value) $this->input .= "<option value=\"".$val2[$element_val->id_field]."\">".$val2[$element_val->value_field]."</option>";
                }
                */
                $this->input .= "</select>";
                $this->element_jscript_nummer++;
            }
            // Typ Options oder Checkboxfeld
            if($element_val->option_type == "option") {
                while (list($key2, $val2) = each($rows)) {
                    if(is_array($value) and $val2[$element_val->id_field] != "") {
                        if(in_array(trim($val2[$element_val->id_field]),$value)) {
                            $this->input .= "<input type=\"checkbox\" name=\"form[".$element_val->name."][]\" value=\"".$val2[$element_val->id_field]."\" checked>".$go_api->lng($val2[$element_val->value_field])."<br>";
                        } else {
                            $this->input .= "<input type=\"checkbox\" name=\"form[".$element_val->name."][]\" value=\"".$val2[$element_val->id_field]."\">".$go_api->lng($val2[$element_val->value_field])."<br>";
                        }
                    } elseif(!is_array($value) and $val2[$element_val->id_field] != "") {
                        if($val2[$element_val->id_field] == $value) {
                            $this->input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"".$val2[$element_val->id_field]."\" checked>".$go_api->lng($val2[$element_val->value_field])."<br>";
                        } else {
                            $this->input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"".$val2[$element_val->id_field]."\">".$go_api->lng($val2[$element_val->value_field])."<br>";
                        }
                    }
                }
                /*
                while (list($key2, $val2) = each($rows)) {
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] == $value) $this->input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"".$val2[$element_val->id_field]."\" checked>".$val2[$element_val->value_field]."<br>";
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] != $value) $this->input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"".$val2[$element_val->id_field]."\">".$val2[$element_val->value_field]."<br>";
                $element_jscript_nummer++;
                }
                */
            }
        }
      }
      
      // Read Only Mode
      
      if($mode == 'r') {
      $this->input = "";
        if($element_val->size != "") {
        $size = "size=\"".$element_val->size."\"";
        } else {
        $size = "";
        }
        
        if($element_val->multiple != "") {
        $multiple = "multiple";
        $multiple_array = "[]";
        $value = explode(",",$value);
        } else {
        $multiple = "";
        $multiple_array = "";
        }
        
        // Wenn auflistung aus Liste kommt
        if($element_val->source == "list") {
            // Typ: Dropdown
            if($element_val->option_type == "dropdown") {
                $this->input = "";
                if($element_val->required != 1) $this->input .= "";
                while (list($key2, $val2) = each($element_val->values)) {
                    if(is_array($value) and $key2 != "") {
                        if(in_array(trim($key2),$value)) {
                            $this->input .= $val2."<br>";
                        } else {
                            $this->input .= "";
                        }
                    } elseif(!is_array($value) and $key2 != "") {
                        if($key2 == $value) {
                            $this->input .= $val2;
                        } else {
                            $this->input .= "";
                        }
                    }
                }
                $this->input .= "";
                
            }
            // Typ: Options oder Checkboxfeld
            if($element_val->option_type == "option") {
                while (list($key2, $val2) = each($element_val->values)) {
                    if(is_array($value) and $key2 != "") {
                        if(in_array(trim($key2),$value)) {
                            $this->input .= $val2."<br>";
                        } else {
                            $this->input .= "";
                        }
                    } elseif(!is_array($value) and $key2 != "") {
                        if($key2 == $value) {
                            $this->input .= "$val2<br>";
                        } else {
                            $this->input .= "";
                        }
                    }
                }
            }
        }
        // Wenn Inhalt aus Datenbank kommt
        if($element_val->source == "db") {
            if($element_val->order == 1) $order_string = " order by ".$element_val->value_field;
        $sql = "SELECT ".$element_val->value_field.",".$element_val->id_field." from ".$element_val->source_table . $order_string;
        $rows = $go_api->db->queryAllRecords($sql);
            // Typ Dropdown
            if($element_val->option_type == "dropdown") {
                $this->input = "";
                if($element_val->required != 1) $this->input .= "";
                while (list($key2, $val2) = each($rows)) {
                    if(is_array($value) and $val2[$element_val->id_field] != "") {
                        if(in_array(trim($val2[$element_val->id_field]),$value)) {
                            $this->input .= $val2[$element_val->value_field]."<br>";
                        } else {
                            $this->input .= "";
                        }
                    } elseif(!is_array($value) and $val2[$element_val->id_field] != "") {
                        if($val2[$element_val->id_field] == $value) {
                            $this->input .= $val2[$element_val->value_field]."<br>";
                        } else {
                            $this->input .= "";
                        }
                    }
                }
                /*
                while (list($key2, $val2) = each($rows)) {
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] == $value) $this->input .= "<option value=\"".$val2[$element_val->id_field]."\" SELECTED>".$val2[$element_val->value_field]."</option>";
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] != $value) $this->input .= "<option value=\"".$val2[$element_val->id_field]."\">".$val2[$element_val->value_field]."</option>";
                }
                */
                $this->input .= "";
                
            }
            // Typ Options oder Checkboxfeld
            if($element_val->option_type == "option") {
                while (list($key2, $val2) = each($rows)) {
                    if(is_array($value) and $val2[$element_val->id_field] != "") {
                        if(in_array(trim($val2[$element_val->id_field]),$value)) {
                            $this->input .= $val2[$element_val->value_field]."<br>";
                        } else {
                            $this->input .= "";
                        }
                    } elseif(!is_array($value) and $val2[$element_val->id_field] != "") {
                        if($val2[$element_val->id_field] == $value) {
                            $this->input .= $val2[$element_val->value_field]."<br>";
                        } else {
                            $this->input .= "";
                        }
                    }
                }
                /*
                while (list($key2, $val2) = each($rows)) {
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] == $value) $this->input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"".$val2[$element_val->id_field]."\" checked>".$val2[$element_val->value_field]."<br>";
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] != $value) $this->input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"".$val2[$element_val->id_field]."\">".$val2[$element_val->value_field]."<br>";
                $element_jscript_nummer++;
                }
                */
            }
        }
      }
    }
    
    ////////////////////////////////////////
    // Datum Feld
    ////////////////////////////////////////
    
    function dateField(&$doc, &$element_val, &$value, $mode) 
    
    {
	global $go_api;
    if($mode == 'rw') {
        If(!empty($value) and $value != 0) $datum = @date($element_val->format,$value);
        $this->input = "<input type=\"text\" name=\"form[".$element_val->name.
                 "]\" size=\"".$element_val->length.
                 "\" maxlength=\"".$element_val->maxlength."\" value=\"".$datum."\" class=\"text\"> (tt.mm.jjjj)";
        if($element_val->required == 1) {
        $checkform .= "
            if (document.forms[0].elements[".$this->element_jscript_nummer."].value == '')
            {
                alert(\"".$go_api->lng("Geben Sie einen Wert in das Feld")." '".$element_val->title."' ".$go_api->lng("jscript_ein").".\");
                document.forms[0].elements[".$this->element_jscript_nummer."].focus();
                return (false);
            }";
        }
        $this->element_jscript_nummer++;
        $datum = "";
      }
      
      
      if($mode == 'r') {
      $this->input = $datum;
      }
    }
    
    ////////////////////////////////////////
    // Datei Feld
    ////////////////////////////////////////
    
    function fileField(&$doc, &$element_val, &$value, $mode) 
    
    {
    global $go_api,$go_info,$doc_id,$doctype_id;
        if($mode == 'rw') {
        $this->input = "";
        // Dateifeld
        if($doc_id != ""){
        $attachment_hinzufuegen = 
        "<a href=\"javascript:attachDoc(0,'file')\">[Hinzufügen]</a>";
        }
        $this->elements .= 
        "
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\">&nbsp;</td>
        </tr>
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\" class=\"t2b\">&nbsp; ".$element_val->title."&nbsp; $attachment_hinzufuegen</td>
        </tr>
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\"><hr noshade size=\"1\"></td>
        </tr>
        ";
        $sql = "SELECT * from file_nodes where parent_doc_id = ".$doc_id." and parent_doctype_id = ".$doctype_id." and status = 1 order by title";
        $rows = $go_api->db->queryAllRecords($sql);
        if(is_array($rows)){
        while (list($row_key, $row_val) = each($rows)){
        $this->elements .= 
        "<tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\" class=\"".$element_val->css_class."\">&nbsp; <a href=\"javascript:editDoc(".$row_val["file_id"].",'file')\" class=\"".$element_val->css_class."\" >
            ".$row_val[title]."</a>&nbsp; ".date("d.m.Y",$row_val[file_date])."</td>
        </tr>
        ";
        }
        }
        $this->elements .= "
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\"><hr noshade size=\"1\"></td>
        </tr>";
      }
      
      
      if($mode == 'r') {
      $this->input = "";
        // Dateifeld
        $this->elements .= 
        "
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\">&nbsp;</td>
        </tr>
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\" class=\"t2b\">&nbsp; ".$element_val->title."&nbsp; </td>
        </tr>
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\"><hr noshade size=\"1\"></td>
        </tr>
        ";
        $sql = "SELECT * from file_nodes where parent_doc_id = ".$doc_id." and parent_doctype_id = ".$doctype_id." order by title";
        $rows = $go_api->db->queryAllRecords($sql);
        if(is_array($rows)){
        while (list($row_key, $row_val) = each($rows)){
        $this->elements .= 
        "<tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\" class=\"".$element_val->css_class."\">&nbsp; 
            ".$row_val[title]."&nbsp; ".date("d.m.Y",$row_val[file_date])."</td>
        </tr>
        ";
        }
        }
        $this->elements .= "
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\"><hr noshade size=\"1\"></td>
        </tr>";
      }
    }
    
    ////////////////////////////////////////
    // Checkbox Feld
    ////////////////////////////////////////
    
    function checkboxField(&$doc, &$element_val, &$value, $mode) 
    
    {
	global $go_api;
    if($mode == 'rw') {
        $checked = "";
        if($value == 1) $checked = "checked";
        $this->input = "<input type=\"checkbox\" name=\"form[".$element_val->name.
                 "]\" value=\"1\" $checked>";
        $this->element_jscript_nummer++;
      }
      
      
      if($mode == 'r') {
      $this->input = $value;
      }
    }
    
    ////////////////////////////////////////
    // Termin Feld
    ////////////////////////////////////////
    
    function terminField(&$doc, &$element_val, &$value, $mode) 
    
    {
	global $go_api;
    if($mode == 'rw') {
        //wenn update, termin auslesen
        if($doc_id != "") {
        $sql = "Select * from termin_event where parent_doc_id = '$doc_id' and parent_doctype_id = '$doctype_id' and parent_field_name = '".$element_val->name."'";
        $termin_event = $go_api->db->queryOneRecord($sql); 
        }
        if($element_val->required == 1 or !empty($termin_event["von"])) {
            if(empty($termin_event["von"])) {
               $termin_event["von"] = time();
            }
            $termin_event["von_tag"] = date("d",$termin_event["von"]);
            $termin_event["von_monat"] = date("m",$termin_event["von"]);
            $termin_event["von_jahr"] = date("Y",$termin_event["von"]);
        }
        if($element_val->bis == 1) $von = " von";
        $this->input = "";
        $this->elements .= 
        "<tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Datum$von:</b></nobr></td>
      <td width=\"69%\" class=\"normal\"> 
        <input type=\"text\" name=\"".$element_val->name."[von_datum]\" size=\"10\" maxlength=\"10\" class=\"text\" value=\"".$termin_event["von_tag"].".".$termin_event["von_monat"].".".$termin_event["von_jahr"]."\">
        (tt.mm.jjjj)
        
        <input type=\"hidden\" name=\"".$element_val->name."[field_name]\" value=\"".$element_val->name."\"></td>
    </tr>";
    unset($von);
    $this->element_jscript_nummer += 2;
    
    if($element_val->zeit == 1) {
        if($element_val->required == 1 or !empty($termin_event["von"])) {
            if(empty($termin_event["von"])) {
               $termin_event["von"] = time();
            }
            $termin_event["von_minute"] = date("i",$termin_event["von"]);
            $termin_event["von_stunde"] = date("H",$termin_event["von"]);
        }
    $this->elements .= "
    <tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Zeit:</b></nobr></td>
      <td width=\"69%\" class=\"normal\"> 
        <input type=\"text\" name=\"".$element_val->name."[von_stunde]\" size=\"2\" maxlength=\"2\" class=\"text\" value=\"".$termin_event["von_stunde"]."\"> :
        <input type=\"text\" name=\"".$element_val->name."[von_minute]\" size=\"2\" maxlength=\"2\" class=\"text\" value=\"".$termin_event["von_minute"]."\">
        (hh:mm)</td>
    </tr>";
    $this->element_jscript_nummer += 2;
    }
    
    if($element_val->bis == 1) {
    if($element_val->required == 1 or !empty($termin_event["bis"])) {
            if(empty($termin_event["bis"])) {
                $termin_event["bis"] = time();
            }
            $termin_event["bis_tag"] = date("d",$termin_event["bis"]);
            $termin_event["bis_monat"] = date("m",$termin_event["bis"]);
            $termin_event["bis_jahr"] = date("Y",$termin_event["bis"]);
        }
        $this->input = "";
        $this->elements .= 
        "<tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Datum bis:</b></nobr></td>
      <td width=\"69%\" class=\"normal\"> 
        <input type=\"text\" name=\"".$element_val->name."[bis_datum]\" size=\"10\" maxlength=\"10\" class=\"text\" value=\"".$termin_event["bis_tag"].".".$termin_event["bis_monat"].".".$termin_event["bis_jahr"]."\">
        (tt.mm.jjjj)
    </tr>";
    $this->element_jscript_nummer += 2;
    
    if($element_val->zeit == 1) {
        if($element_val->required == 1 or !empty($termin_event["bis"])) {
            if(empty($termin_event["bis"])) {
               $termin_event["bis"] = time();
            }
            $termin_event["bis_minute"] = date("i",$termin_event["bis"]);
            $termin_event["bis_stunde"] = date("H",$termin_event["bis"]);
        }
    $this->elements .= "
    <tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Zeit:</b></nobr></td>
      <td width=\"69%\" class=\"normal\"> 
        <input type=\"text\" name=\"".$element_val->name."[bis_stunde]\" size=\"2\" maxlength=\"2\" class=\"text\" value=\"".$termin_event["bis_stunde"]."\"> :
        <input type=\"text\" name=\"".$element_val->name."[bis_minute]\" size=\"2\" maxlength=\"2\" class=\"text\" value=\"".$termin_event["bis_minute"]."\">
        (hh:mm)</td>
    </tr>";
    $this->element_jscript_nummer += 2;
    }
    }
    
    if($element_val->intervall == 1) {
    
    $ivals = array( Tag => 86400,
                    Woche => 604800,
                    Monat => 2628000,
                    Jahr => 31536000);
    
    $this->elements .= "
    <tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Intervall:</b></nobr></td>
      <td width=\"69%\" class=\"normal\">
      <input type=\"text\" name=\"".$element_val->name."[intervall]\" size=\"3\" maxlength=\"3\" value=\"".$termin_event["intervall"]."\" class=\"text\">
&nbsp;<select size=\"1\" name=\"".$element_val->name."[intervall_einheit]\">";
     foreach($ivals as $ival_key => $ival_value) {             
        if($ival_value == $termin_event["intervall_einheit"]) {
            $this->elements .= "<option value=\"$ival_value\" selected>$ival_key</option>";
        } else {
            $this->elements .= "<option value=\"$ival_value\">$ival_key</option>";
        }
     }
     $this->elements .= "</select>
				</td>
    </tr>";
    unset($ivals);
    unset($ival_key);
    unset($ival_value);
    $this->element_jscript_nummer += 2;
    }
    
    
    if($element_val->erinnerung == 1) {
    
    $ivals = array( Stunde => 3600,
                    Tag => 86400,
                    Woche => 604800,
                    Monat => 2628000);
    
    $this->elements .= "<tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Erinnerung:</b></nobr></td>
      <td width=\"69%\" class=\"normal\">
	  <input type=\"text\" name=\"".$element_val->name."[vorlauf]\" size=\"3\" maxlength=\"3\" value=\"".$termin_event["vorlauf"]."\" class=\"text\">
        &nbsp;<select size=\"1\" name=\"".$element_val->name."[vorlauf_einheit]\">
                  ";
     foreach($ivals as $ival_key => $ival_value) {             
        if($ival_value == $termin_event["vorlauf_einheit"]) {
            $this->elements .= "<option value=\"$ival_value\" selected>$ival_key</option>";
        } else {
            $this->elements .= "<option value=\"$ival_value\">$ival_key</option>";
        }
     }
     $this->elements .= "</select> vor dem Termin
				</td>
    </tr>";
    unset($ivals);
    unset($ival_key);
    unset($ival_value);
    $this->element_jscript_nummer += 2;
    }
    
    if($element_val->benachrichtigung == 1) {
    
    $ivals = array( keine => 'Keine Erinnerung',
                    email => 'Email');
    
    $this->elements .= "<tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Benachrichtigung:</b></nobr></td>
      <td width=\"69%\" class=\"normal\">
	  <select size=\"1\" name=\"".$element_val->name."[medium]\">";
      
     foreach($ivals as $ival_key => $ival_value) {             
        if($ival_key == $termin_event["medium"]) {
            $this->elements .= "<option value=\"$ival_key\" selected>$ival_value</option>";
        } else {
            $this->elements .= "<option value=\"$ival_key\">$ival_value</option>";
        }
     }
     $this->elements .= "</select>
				</td>
    </tr>
    <tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; EMail:</b></nobr></td>
      <td width=\"69%\" class=\"normal\">
        <input type=\"text\" name=\"".$element_val->name."[email]\" size=\"30\" maxlength=\"255\" value=\"".$termin_event["adresse"]."\" class=\"text\">
      </td>
    </tr>";
    unset($ivals);
    unset($ival_key);
    unset($ival_value);
    $this->element_jscript_nummer += 2;
    }
        unset($termin_event);
      }
      
      // nur Lesen Modus /////////////////////////////////////////
      
      if($mode == 'r') {
      //wenn update, termin auslesen
        if($doc_id != "") {
        $sql = "Select * from termin_event where parent_doc_id = '$doc_id' and parent_doctype_id = '$doctype_id' and parent_field_name = '".$element_val->name."'";
        $termin_event = $go_api->db->queryOneRecord($sql); 
        }
        if($element_val->required == 1 or !empty($termin_event["von"])) {
            if(empty($termin_event["von"])) {
               $termin_event["von"] = time();
            }
            $termin_event["von_tag"] = date("d",$termin_event["von"]);
            $termin_event["von_monat"] = date("m",$termin_event["von"]);
            $termin_event["von_jahr"] = date("Y",$termin_event["von"]);
        }
        if($element_val->bis == 1) $von = " von";
        $this->input = "";
        $this->elements .= 
        "<tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Datum$von:</b></nobr></td>
      <td width=\"69%\" class=\"normal\"> 
        ".$termin_event["von_tag"].".".$termin_event["von_monat"].".".$termin_event["von_jahr"]."
        (tt.mm.jjjj)
        
        </td>
    </tr>";
    unset($von);
    
    if($element_val->zeit == 1) {
        if($element_val->required == 1 or !empty($termin_event["von"])) {
            if(empty($termin_event["von"])) {
               $termin_event["von"] = time();
            }
            $termin_event["von_minute"] = date("i",$termin_event["von"]);
            $termin_event["von_stunde"] = date("H",$termin_event["von"]);
        }
    $this->elements .= "
    <tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Zeit:</b></nobr></td>
      <td width=\"69%\" class=\"normal\"> 
        ".$termin_event["von_stunde"].":".$termin_event["von_minute"]."
        Uhr</td>
    </tr>";
    }
    
    if($element_val->bis == 1) {
    if($element_val->required == 1 or !empty($termin_event["bis"])) {
            if(empty($termin_event["bis"])) {
                $termin_event["bis"] = time();
            }
            $termin_event["bis_tag"] = date("d",$termin_event["bis"]);
            $termin_event["bis_monat"] = date("m",$termin_event["bis"]);
            $termin_event["bis_jahr"] = date("Y",$termin_event["bis"]);
        }
        $this->input = "";
        $this->elements .= 
        "<tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Datum bis:</b></nobr></td>
      <td width=\"69%\" class=\"normal\"> 
        ".$termin_event["bis_tag"].".".$termin_event["bis_monat"].".".$termin_event["bis_jahr"]."
        (tt.mm.jjjj)
    </tr>";

    
    if($element_val->zeit == 1) {
        if($element_val->required == 1 or !empty($termin_event["bis"])) {
            if(empty($termin_event["bis"])) {
               $termin_event["bis"] = time();
            }
            $termin_event["bis_minute"] = date("i",$termin_event["bis"]);
            $termin_event["bis_stunde"] = date("H",$termin_event["bis"]);
        }
    $this->elements .= "
    <tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Zeit:</b></nobr></td>
      <td width=\"69%\" class=\"normal\"> 
        ".$termin_event["bis_stunde"].":".$termin_event["bis_minute"]." Uhr</td>
    </tr>";
    }
    }
    
    if($element_val->intervall == 1) {
    
    $ivals = array( Tag => 86400,
                    Woche => 604800,
                    Monat => 2628000,
                    Jahr => 31536000);
    
    $this->elements .= "
    <tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Intervall:</b></nobr></td>
      <td width=\"69%\" class=\"normal\">
      ".$termin_event["intervall"]."
&nbsp;";
     foreach($ivals as $ival_key => $ival_value) {             
        if($ival_value == $termin_event["intervall_einheit"]) {
            $this->elements .= "$ival_key";
        } else {
            $this->elements .= "";
        }
     }
     $this->elements .= "
				</td>
    </tr>";
    unset($ivals);
    unset($ival_key);
    unset($ival_value);
    }
    
    
    if($element_val->erinnerung == 1) {
    
    $ivals = array( Stunde => 3600,
                    Tag => 86400,
                    Woche => 604800,
                    Monat => 2628000);
    
    $this->elements .= "<tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Erinnerung:</b></nobr></td>
      <td width=\"69%\" class=\"normal\">
	  ".$termin_event["vorlauf"]."
        &nbsp;
                  ";
     foreach($ivals as $ival_key => $ival_value) {             
        if($ival_value == $termin_event["vorlauf_einheit"]) {
            $this->elements .= "$ival_key";
        } else {
            $this->elements .= "";
        }
     }
     $this->elements .= " vor dem Termin
				</td>
    </tr>";
    unset($ivals);
    unset($ival_key);
    unset($ival_value);
    }
    
    if($element_val->benachrichtigung == 1) {
    
    $ivals = array( keine => 'Keine Erinnerung',
                    email => 'Email');
    
    $this->elements .= "<tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; Benachrichtigung:</b></nobr></td>
      <td width=\"69%\" class=\"normal\">
	  ";
      
     foreach($ivals as $ival_key => $ival_value) {             
        if($ival_key == $termin_event["medium"]) {
            $this->elements .= "$ival_value";
        } else {
            $this->elements .= "";
        }
     }
     $this->elements .= "
				</td>
    </tr>
    <tr bgcolor=\"#EEEEEE\"> 
      <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; EMail:</b></nobr></td>
      <td width=\"69%\" class=\"normal\">
        ".$termin_event["adresse"]."
      </td>
    </tr>";
    unset($ivals);
    unset($ival_key);
    unset($ival_value);
    }
        unset($termin_event);
      }
    }
    
    
    ////////////////////////////////////////
    // Plugin
    ////////////////////////////////////////
    
    function pluginField(&$doc, &$element_val, &$value, $mode) 
    
    {
    global $go_info,$go_api,$doc_id,$doctype_id,$groupid;
    $this->input = "";
    if($element_val->name != "") {
        $plugin_file = $go_info["server"]["include_root"].$go_info["server"]["dir_trenner"].'plugins'.$go_info["server"]["dir_trenner"].$element_val->name.".plugin.php";
        if(@is_file($plugin_file)) {
            include_once($plugin_file);
            $pluginclass = $element_val->name . '_plugin';
            if(class_exists($pluginclass)) {
            $plugin = new $pluginclass;
            $plugin->_options = $element_val->options;
                $this->elements .= 
                "<tr bgcolor='#EEEEEE'>
                    <td colspan='2' class = '".$element_val->css_class."'>".$plugin->show($doc_id, $doctype_id, $groupid)."</td>
                </tr>
                ";
            unset($plugin);
            } else {
                $go_api->errorMessage("Die KLasse: $pluginclass existiert nicht im Plugin: ".$element_val->name.".");
            }
            } else {
                $go_api->errorMessage("Plugin: ".$element_val->name." nicht gefunden.");
            }
        }
    } 

}
?>