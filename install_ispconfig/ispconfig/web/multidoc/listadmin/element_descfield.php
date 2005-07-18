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
$go_api->content->define( array(
		            main    => "main.htm",
		            table   => "multidoc_admin_descfield_edit.htm",
		            stylesheet => "style.css"));

$go_api->content->assign( array( TITLE => "",
						SESSION => $session,
						BACKGROUND_GIF => "",
						COPYRIGHT => "von Till",
						FGCOLOR => "$session_nav_hcolour",
						TABLE_H_COLOR => "$session_page_hcolour",
						BOXSIZE => "450",
						WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; Beschreibung</font>",
						SITENAME => "$session_site",
						DESIGNPATH => $session_design_path,
SERVERURL => $go_info["server"]["server_url"],

						S => $s
            
            ) );

$doc = $go_api->multidoc->doctype_get($doctype_id);
$decks = "";

while (list($key, $val) = each($doc->deck)) {
   if($deck_id == $key){
        $decks .= "<option value=\"$key\" SELECTED>$val->title</option>\n";
   }else{
        $decks .= "<option value=\"$key\">$val->title</option>\n";
   }
}


if(isset($id)){
// $row = $go_api->db->queryOneRecord("select * from doctype where doctype_id = '$doctype_id'");
// $doc = unserialize($row["doctype_def"]);
// $go_api->multidoc->debug($doc);

$go_api->content->assign( array( TITLE => $doc->deck[$deck_id]->elements[$id]->title,
                     NAME => $doc->deck[$deck_id]->elements[$id]->name,
                     DESCRIPTION => $doc->deck[$deck_id]->elements[$id]->description,
                     MAXLENGTH => $doc->deck[$deck_id]->elements[$id]->maxlength,
                     VALUE => $doc->deck[$deck_id]->elements[$id]->value,
                     ALIGNMENT => $doc->deck[$deck_id]->elements[$id]->alignment,
                     REQUIRED => $doc->deck[$deck_id]->elements[$id]->required,
                     VISIBLE => $doc->deck[$deck_id]->elements[$id]->visible,
                     CSS_CLASS => $doc->deck[$deck_id]->elements[$id]->css_class,
					 DECK_ID => $deck_id,
                     DECKS => $decks,
                     DOCTYPE_ID => $doctype_id,
                     ID => $id
					 ));

} else {
$go_api->content->assign( array( DOCTYPE_ID => $doctype_id,
                                 DECKS => $decks));
$go_api->content->no_strict();
}


$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>