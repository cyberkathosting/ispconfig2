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

$go_api->uses_obj("liste");

                 
############################################################################
#
#   Template definieren
#
############################################################################

$tpl = new FastTemplate("../../templates");

$go_api->content->define( array(
		main    => "main.htm",
		table   => "multidoc_listadmin_listtype_show.htm",
		stylesheet => "style.css"));
		

$go_api->content->assign( array( TITLE => "$session_site Startseite",
						SESSION => $session,
						BACKGROUND_GIF => "",
						COPYRIGHT => "von Till",
						FGCOLOR => "$session_nav_hcolour",
						TABLE_H_COLOR => "$session_page_hcolour",
						BOXSIZE => "450",
						WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; Listen Designer</font>",
						SITENAME => "$session_site",
						DESIGNPATH => $session_design_path,
SERVERURL => $go_info["server"]["server_url"],

						S => $s
            
            ) );

if(isset($id)){
$row = $go_api->db->queryOneRecord("select * from listtype where listtype_id = '$id'");

$liste = unserialize($row["listtype_def"]);

//print_r($liste);

$go_api->content->assign( array( LISTTYPE_TITLE => $liste->title,
					 LISTTYPE_DESCRIPTION => $liste->description,
                     GROUP_NAME => $liste->groupid,
                     LISTTYPE_ID => $id
					 ));

if(is_array($liste->row)){
$rows = "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"4\" bgcolor=\"EEEEEE\">
";

while (list($key, $val) = each($liste->row)) 
    {
    $rows .= "
    <tr bgcolor=\"0000FF\"> 
    <td width=\"48%\" class=\"normal\"><b><font color=\"#FFFFFF\">".$val->title.":</font></b></td>  
    <td width=\"52%\" class=\"normal\">
      <div align=\"right\"><font color=\"#FFFFFF\">[</font><a href=\"element_edit.php?id=$key&art=row&listtype_id=".$row["listtype_id"]."&s=$s\"><font color=\"#FFFFFF\">Bearbeiten</font></a><font color=\"#FFFFFF\">] 
        [</font><a href=\"delete.php?row_id=".$key."&art=row&listtype_id=".$row["listtype_id"]."&s=$s\"><font color=\"#FFFFFF\">L&ouml;schen</font></a><font color=\"#FFFFFF\">]</font></div>
    </td>
   </tr>
        <tr> 
          
    <td colspan=\"2\" align=\"center\" height=\"1\" valign=\"middle\"> 
      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
    
    if(is_array($val->elements)){
        while (list($element_key, $element_val) = each($val->elements)){
        $rows .= "<tr> 
          <td class=\"normal\" width=\"45%\"><b>".$element_val->name."</b></td>
          <td class=\"normal\" width=\"22%\">".$element_val->type."</td>
          <td class=\"normal\" align=\"right\" width=\"33%\"><nobr>[<a href=\"element_edit.php?id=".$element_key."&art=".$element_val->type."&listtype_id=".$row["listtype_id"]."&row_id=".$key."&s=$s\">Bearbeiten</a>] 
            [<a href=\"delete.php?element_id=".$element_key."&art=field&listtype_id=".$row["listtype_id"]."&row_id=".$key."&s=$s\">L&ouml;schen</a>] 
            [<a href=\"element_flip.php?id=".$element_key."&listtype_id=".$row["listtype_id"]."&row_id=".$key."&s=$s\">^</a>]</nobr></td>
              </tr>";
        }
     } 
    $rows .= "
                </table>
          </td>
        </tr>";
    }
    $rows .= "</table>";
      
} else {
$go_api->content->no_strict();
}
}

/*
while (list($key, $val) = each($go_info["modul"]["element_types"]))
    {
    $go_api->content->assign( array( ELEMENT_VALUE => $key,
                                     ELEMENT_TEXT => $val));
    $go_api->content->parse(Dropdown, ".dropdown");
    }
*/
$go_api->content->assign( array( ROWS => $rows));

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>