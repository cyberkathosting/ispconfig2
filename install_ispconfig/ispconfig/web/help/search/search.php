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

$go_api->uses("multidoc,tree");
$go_api->tree->set_table($go_info["modul"]["table_name"]);

$start = $go_api->multidoc->utime();
                 
############################################################################
#
#   Template definieren
#
############################################################################

$tpl = new FastTemplate("../../templates");

$go_api->content->define( array(
		main    => "main.htm",
		table   => "multidoc_search.htm",
		stylesheet => "style.css"));

$go_api->content->assign( array( TITLE => "$session_site Startseite",
						SESSION => $session,
						BACKGROUND_GIF => "",
						COPYRIGHT => "von Till",
						FGCOLOR => "$session_nav_hcolour",
						TABLE_H_COLOR => "$session_page_hcolour",
						WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; Formular Designer</font>",
						SITENAME => "$session_site",
						DESIGNPATH => $session_design_path,
SERVERURL => $go_info["server"]["server_url"],

                        PARENT_ID => $parent_id,
                        NEXT_TREE_ID => $next_tree_id,
                        NEXT_DOCTYPE_ID => $next_doctype_id,
                        CALLER_TREE_ID => $caller_tree_id,
						S => $s
            
            ) );

$form = "";
$element_jscript_nummer = 0;

$tree_id = intval($_REQUEST["tree_id"]);
$doc_id = intval($_REQUEST["doc_id"]);
$doctype_id = intval($_REQUEST["doctype_id"]);

/*******************
*  Textfelder füllen
********************/
// Wenn nur Tree_id angegeben wurde
if($tree_id != "") {
    if($item = $go_api->tree->item($tree_id)) {
    $doctype_id = $item["doctype_id"];
        if(isset($doctype_id)) $doc_id = $item["doc_id"];
        if($gid == "") $gid = $item["groupid"];
        $ordner = $item["parent"];
    } else {
    $go_api->content->no_strict();
    }
}




if(isset($doctype_id)){
$row = $go_api->db->queryOneRecord("select * from doctype where doctype_id = '$doctype_id'");
$doc = unserialize($row["doctype_def"]);

if(isset($doc_id)){
$tablevalues = $go_api->db->queryOneRecord("select * from ".$doc->storage_path." where doc_id = '$doc_id'");
}
// $go_api->multidoc->debug($doc);

if($deck_id == "") $deck_id = key($doc->deck);

$go_api->content->assign( array( DOCTYPE_TITLE => $doc->title,
					 DOCTYPE_DESCRIPTION => $doc->description,
                     WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$doc->title."</font>",
                     GROUP_ID => $doc->groupid,
                     DOCTYPE_ID => $doctype_id,
                     TREE_ID => $tree_id,
                     DOC_ID => $doc_id,
                     BOXSIZE => $doc->form_width,
                     DECK_ID => $deck_id
					 ));



if(is_array($doc->deck)){


    $deck1 .= "<td rowspan=\"3\" height=\"16\" width=\"6\" bgcolor=\"#EEEEEE\"><img src=\"../../design/default/tab/tab_active_l.gif\" width=\"6\" height=\"16\"></td>
               <td height=\"1\" bgcolor=\"#0066CC\"><img src=\"../../design/default/tab/x.gif\" width=\"1\" height=\"1\"></td>
               <td rowspan=\"3\" width=\"6\" bgcolor=\"#EEEEEE\"><img src=\"../../design/default/tab/tab_active_r.gif\" width=\"6\" height=\"16\"></td>
                  ";
    
    $deck2 .="<td height=\"14\" bgcolor=\"#EEEEEE\"> 
                    <div align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">Suchmaske</font></div>
                  </td>";

while (list($key, $val) = each($doc->deck)) 
    {

    if(is_array($val->elements)){
        // gehe durch alle Elemente des Decks
        while (list($element_key, $element_val) = each($val->elements)){
        
        // wenn Element sichtbar ist
        if($element_val->visible == 1 and $element_val->search == 1) {
        
        /*
        // value = Wert aus Datenbank, ansonsten Standartwert aus Objekt (Voreintrag)
        if($tablevalues[$element_val->name] != ""){
        $value = $tablevalues[$element_val->name];
        } else {
        $value = $element_val->value;
        }
        */
        $value == '';
        
        // Switch durch Datentypen
        switch ($element_val->type) {
        //****************** kleines Textfeld ****************************************************
        case "shortText":
        if($element_val->password == 1) {
        $inputtype = "password";
        }else {
        $inputtype = "text";
        }
        
        $input = '<input type="'.$inputtype.'" name="form['.$element_val->name.
                 ']" size="'.$element_val->length.
                 '" maxlength="'.$element_val->maxlength.'" value="'.$value.'" class="text">';
        if($element_val->required == 1) {
        $checkform .= "
            if (document.forms[0].elements[$element_jscript_nummer].value == '')
            {
                alert(\"Geben Sie einen Wert in das Feld '".$element_val->title."' ein.\");
                document.forms[0].elements[$element_jscript_nummer].focus();
                return (false);
            }";
        }
        $element_jscript_nummer++;
        break;
        //****************großes Textfeld *******************************************************
        case "longText":
        $input = '<textarea name="form['.$element_val->name.
                 ']" cols="'.$element_val->length.
                 '" rows="'.$element_val->rows.
                 '">'.$value.
                 '</textarea>';
        $checkform .= "
            if (document.forms[0].elements[$element_jscript_nummer].value == '')
            {
                alert(\"Geben Sie einen Wert in das Feld '".$element_val->title."' ein.\");
                document.forms[0].elements[$element_jscript_nummer].focus();
                return (false);
            }";
        $element_jscript_nummer++;
        break;
        //****************Dokumentenabhängigkeit ************************************************
        case "attachField":
        $input = "";
        // ATTACHMENT FELD
        if($doc_id != ""){
        $attachment_hinzufuegen .= 
        "<a href=\"javascript:attachDoc(".$element_val->doctype.",'doc')\">[Hinzufügen]</a>";
        }
        $elements .= 
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
        $tempdoc = $go_api->multidoc->doctype_get($element_val->doctype);
        $attach_table = $tempdoc->storage_path;
        unset($tempdoc);
        $sql = "SELECT * from multidoc_nodes , $attach_table where $attach_table.doc_id = multidoc_nodes.doc_id and multidoc_nodes.doctype_id = ".$element_val->doctype." and multidoc_nodes.parent = ".$tree_id;
        $rows = $go_api->db->queryAllRecords($sql);
        if(is_array($rows)){
        while (list($row_key, $row_val) = each($rows)){
        //edit.php?doctype_id=".$row_val["doctype_id"]."&doc_id=".$row_val["doc_id"]."&parent_id=$doc_id&s=$s
        $elements .= 
        "<tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\">&nbsp; <a href=\"javascript:editDoc(".$row_val["tree_id"].",'doc')\" class=\"".$element_val->css_class."\" >";
            $fieldarray = explode(",",$element_val->fields);
            if(is_array($fieldarray)){
                while (list($fieldarray_key, $fieldarray_val) = each($fieldarray)){
                $elements .= $row_val[$fieldarray_val] . "&nbsp; ";
                }
            } else {
            $elements .= $row_val[$element_val->fields];
            };
            
            $elements .= "</a></td>
        </tr>
        ";
        }
        }
        $elements .= "
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\"><hr noshade size=\"1\"></td>
        </tr>";
        
        break;
        case "linkField":
        $input = "<input type=\"text\" name=\"form[".$element_val->name.
                 "]\" size=\"".$element_val->length.
                 "\" maxlength=\"".$element_val->maxlength."\" value=\"".$value."\">";
        $element_jscript_nummer++;
        break;
        case "doubleField":
        $input = "<input type=\"text\" name=\"form[".$element_val->name.
                 "]\" size=\"".$element_val->length.
                 "\" maxlength=\"".$element_val->maxlength."\" value=\"".$value."\">";
        $element_jscript_nummer++;
        break;
        case "integerField":
        $input = "<input type=\"text\" name=\"form[".$element_val->name.
                 "]\" size=\"".$element_val->length.
                 "\" maxlength=\"".$element_val->maxlength."\" value=\"".$value."\">";
        $element_jscript_nummer++;
        break;
        case "descField":
        $input = "";
        $elements .= 
        "<tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\" class=\"".$element_val->css_class."\" align=\"".$element_val->alignment."\">".$element_val->value."</td>
        </tr>
        ";
        break;
        case "seperatorField":
        $input = "";
        $elements .= 
        "<tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\" class=\"".$element_val->css_class."\"><hr noshade size=\"".$element_val->width."\"></td>
        </tr>
        ";
        break;
        //************** Optionsfeld ***********************************************************************************
        case "optionField":
        $input = "";
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
                $input = "<select name=\"form[".$element_val->name."]$multiple_array\" $size $multiple>";
                if($element_val->required != 1) $input .= "<option value=\"\">&nbsp;</option>";
                while (list($key2, $val2) = each($element_val->values)) {
                    if(is_array($value) and $key2 != "") {
                        if(in_array(trim($key2),$value)) {
                            $input .= "<option value=\"$key2\" SELECTED>$val2</option>";
                        } else {
                            $input .= "<option value=\"$key2\">$val2</option>";
                        }
                    } elseif(!is_array($value) and $key2 != "") {
                        if($key2 == $value) {
                            $input .= "<option value=\"$key2\" SELECTED>$val2</option>";
                        } else {
                            $input .= "<option value=\"$key2\">$val2</option>";
                        }
                    }
                }
                $input .= "</select>";
                $element_jscript_nummer++;
            }
            // Typ: Options oder Checkboxfeld
            if($element_val->option_type == "option") {
                while (list($key2, $val2) = each($element_val->values)) {
                    if(is_array($value) and $key2 != "") {
                        if(in_array(trim($key2),$value)) {
                            $input .= "<input type=\"checkbox\" name=\"form[".$element_val->name."][]\" value=\"$key2\" checked>$val2<br>";
                        } else {
                            $input .= "<input type=\"checkbox\" name=\"form[".$element_val->name."][]\" value=\"$key2\">$val2<br>";
                        }
                    } elseif(!is_array($value) and $key2 != "") {
                        if($key2 == $value) {
                            $input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"$key2\" checked>$val2<br>";
                        } else {
                            $input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"$key2\">$val2<br>";
                        }
                    }
                }
            }
        }
        // Wenn Inhalt aus Datenbank kommt
        if($element_val->source == "db") {
        $sql = "SELECT ".$element_val->value_field.",".$element_val->id_field." from ".$element_val->source_table . " order by ".$element_val->value_field;
        $rows = $go_api->db->queryAllRecords($sql);
            // Typ Dropdown
            if($element_val->option_type == "dropdown") {
                $input = "<select name=\"form[".$element_val->name."]\" $size>";
                if($element_val->required == 1) $input .= "<option value=\"\">&nbsp;</option>";
                while (list($key2, $val2) = each($rows)) {
                    if(is_array($value) and $val2[$element_val->id_field] != "") {
                        if(in_array(trim($val2[$element_val->id_field]),$value)) {
                            $input .= "<option value=\"".$val2[$element_val->id_field]."\" SELECTED>".$val2[$element_val->value_field]."</option>";
                        } else {
                            $input .= "<option value=\"".$val2[$element_val->id_field]."\">".$val2[$element_val->value_field]."</option>";
                        }
                    } elseif(!is_array($value) and $val2[$element_val->id_field] != "") {
                        if($key2 == $value) {
                            $input .= "<option value=\"".$val2[$element_val->id_field]."\" SELECTED>".$val2[$element_val->value_field]."</option>";
                        } else {
                            $input .= "<option value=\"".$val2[$element_val->id_field]."\">".$val2[$element_val->value_field]."</option>";
                        }
                    }
                }
                /*
                while (list($key2, $val2) = each($rows)) {
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] == $value) $input .= "<option value=\"".$val2[$element_val->id_field]."\" SELECTED>".$val2[$element_val->value_field]."</option>";
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] != $value) $input .= "<option value=\"".$val2[$element_val->id_field]."\">".$val2[$element_val->value_field]."</option>";
                }
                */
                $input .= "</select>";
                $element_jscript_nummer++;
            }
            // Typ Options oder Checkboxfeld
            if($element_val->option_type == "option") {
                while (list($key2, $val2) = each($rows)) {
                    if(is_array($value) and $val2[$element_val->id_field] != "") {
                        if(in_array(trim($val2[$element_val->id_field]),$value)) {
                            $input .= "<input type=\"checkbox\" name=\"form[".$element_val->name."][]\" value=\"".$val2[$element_val->id_field]."\" checked>".$val2[$element_val->value_field]."<br>";
                        } else {
                            $input .= "<input type=\"checkbox\" name=\"form[".$element_val->name."][]\" value=\"".$val2[$element_val->id_field]."\">".$val2[$element_val->value_field]."<br>";
                        }
                    } elseif(!is_array($value) and $val2[$element_val->id_field] != "") {
                        if($val2[$element_val->id_field] == $value) {
                            $input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"".$val2[$element_val->id_field]."\" checked>".$val2[$element_val->value_field]."<br>";
                        } else {
                            $input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"".$val2[$element_val->id_field]."\">".$val2[$element_val->value_field]."<br>";
                        }
                    }
                }
                /*
                while (list($key2, $val2) = each($rows)) {
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] == $value) $input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"".$val2[$element_val->id_field]."\" checked>".$val2[$element_val->value_field]."<br>";
                if($val2[$element_val->id_field] != "" and $val2[$element_val->id_field] != $value) $input .= "<input type=\"radio\" name=\"form[".$element_val->name."]\" value=\"".$val2[$element_val->id_field]."\">".$val2[$element_val->value_field]."<br>";
                $element_jscript_nummer++;
                }
                */
            }
        }
        break;
        //***************************** Datumsfeld ****************************************************************************
        case "dateField":
        If(!empty($value)) $datum = date($element_val->format,$value);
        $input = "<input type=\"text\" name=\"form[".$element_val->name.
                 "]\" size=\"".$element_val->length.
                 "\" maxlength=\"".$element_val->maxlength."\" value=\"".$datum."\">";
        $element_jscript_nummer++;
        break;
        case "fileField":
        $input = "";
        // Dateifeld
        if($doc_id != ""){
        $attachment_hinzufuegen .= 
        "<a href=\"javascript:attachDoc(0,'file')\">[Hinzufügen]</a>";
        }
        $elements .= 
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
        $sql = "SELECT * from file_nodes where parent_doc_id = ".$doc_id." and parent_doctype_id = ".$doctype_id." order by title";
        $rows = $go_api->db->queryAllRecords($sql);
        if(is_array($rows)){
        while (list($row_key, $row_val) = each($rows)){
        $elements .= 
        "<tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\" class=\"".$element_val->css_class."\">&nbsp; <a href=\"javascript:editDoc(".$row_val["file_id"].",'file')\" class=\"".$element_val->css_class."\" >
            ".$row_val[title]."</a>&nbsp; ".date("d.m.Y",$row_val[file_date])."</td>
        </tr>
        ";
        }
        }
        $elements .= "
        <tr bgcolor=\"#EEEEEE\">
            <td colspan=\"2\"><hr noshade size=\"1\"></td>
        </tr>";
        
        break;
        case "checkboxField":
        $checked = "";
        if($value == 1) $checked = "checked";
        $input = "<input type=\"checkbox\" name=\"form[".$element_val->name.
                 "]\" value=\"1\" $checked>";
        $element_jscript_nummer++;
        break;
        }
    
        //$input = "<input type=\"text\" name=\"textfield\" size=\"25\" maxlength=\"255\" value=\"af\">";
        
        
        
        if($input != "") {    
        $elements .= 
        "<tr bgcolor=\"#EEEEEE\"> 
         <td width=\"31%\" class=\"normal\" valign=\"top\"><nobr><b>&nbsp; ".$element_val->title.":</b></nobr></td>
         <td width=\"69%\" class=\"normal\">$input</td>
        </tr>
        ";
        }
        
          
        }
        }
        } else {
        $go_api->content->no_strict();
        }
    }  
} else {
$go_api->content->no_strict();
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
                                    ELEMENTS => $elements,
                                    DECKS => $decks,
                                    CHECKFORM => $checkform));

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();

echo $go_api->multidoc->utime() - $start;
exit;
?>