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
$go_api->uses("listadmin,multidoc");

$go_api->content->define( array(
		            main    => "main.htm",
		            table   => "multidoc_listadmin_field_edit.htm",
		            stylesheet => "style.css"));

$go_api->content->assign( array( TITLE => "",
						SESSION => $session,
						BACKGROUND_GIF => "",
						COPYRIGHT => "von Till",
						FGCOLOR => "$session_nav_hcolour",
						TABLE_H_COLOR => "$session_page_hcolour",
						BOXSIZE => "450",
						WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; Feld bearbeiten/erstellen</font>",
						SITENAME => "$session_site",
						DESIGNPATH => $session_design_path,
SERVERURL => $go_info["server"]["server_url"],

						S => $s
            
            ) );

$liste = $go_api->listadmin->listtype_get($listtype_id);
$rows = "";

while (list($key, $val) = each($liste->row)) {
   if($row_id == $key){
        $rows .= "<option value=\"$key\" SELECTED>$val->title</option>\n";
   }else{
        $rows .= "<option value=\"$key\">$val->title</option>\n";
   }
}

$doc = $go_api->multidoc->doctype_get($liste->doctype_id);
$doctype_table = $doc->storage_path;
unset($doc);

// Tabellenfelder einlesen

//$go_api->db->tableInfo($doctype_table);

$result = mysql_list_fields($go_info["server"]["db_name"],$doctype_table);
$fields = mysql_num_fields ($result);
$i = 0;
$table = mysql_field_table ($result, $i);
while ($i < $fields) {
    $name  = mysql_field_name  ($result, $i);
    $type  = mysql_field_type  ($result, $i);
    $len   = mysql_field_len   ($result, $i);
    //$flags = mysql_field_flags ($result, $i);
    if($liste->row[$row_id]->elements[$id]->name == $name){
        $fields .= "<option value=\"$name\" SELECTED>$name $type $len</option>\n";
   }else{
        $fields .= "<option value=\"$name\">$name ($type $len)</option>\n";
   }
    $i++;
}

if(isset($id)){

    if($liste->row[$row_id]->elements[$id]->nobr == 1){
        $nobr = 'checked';
    } else {
        $nobr = '';
    }


$go_api->content->assign( array(    MAXLENGTH => $liste->row[$row_id]->elements[$id]->maxlength,
                                    WIDTH => $liste->row[$row_id]->elements[$id]->width,
                                    LENGTH => $liste->row[$row_id]->elements[$id]->length,
                                    NOBR => $nobr,
                                    CSS_CLASS => $liste->row[$row_id]->elements[$id]->css_class,
					                ROW_ID => $row_id,
                                    ROWS => $rows,
                                    LISTTYPE_ID => $listtype_id,
                                    ID => $id,
                                    FIELDS => $fields
					                ));

} else {
$go_api->content->assign( array( LISTTYPE_ID => $listtype_id,
                                 ROWS => $rows,
                                 FIELDS => $fields));
$go_api->content->no_strict();
}


$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>