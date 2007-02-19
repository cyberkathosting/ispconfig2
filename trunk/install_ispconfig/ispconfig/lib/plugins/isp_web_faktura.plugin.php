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

 #############################################################
 #
 # ISPConfig(c) Plugin
 # Version 1.0
 #
 # Plugin Name: ISP-Web-Faktura
 # Version: 1.0
 # Autor: Till Brehm
 # Datum: 10.03.2003
 # Letztes Update: 06.05.2003
 #
 #############################################################

class isp_web_faktura_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api,$s;
    
    $web_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where  doc_id = $doc_id and doctype_id = $doctype_id");
    
    $groupid = $web_node["groupid"];
    $web_id = $doc_id;
    $artikel_liste = array();
    
    $artikel = $go_api->db->queryAllRecords("SELECT * from isp_fakt_nodes, isp_fakt_artikel where isp_fakt_artikel.doc_id = isp_fakt_nodes.doc_id and isp_fakt_nodes.doctype_id = 1026 and isp_fakt_nodes.status = 1 and (isp_fakt_artikel.artikelgroup = '' or isp_fakt_artikel.artikelgroup = $groupid)");
	foreach($artikel as $artk) {
        $artikel_typ = $artk["artikeltyp"];
        $artikel_id = $artk["doc_id"];
        $artikel_liste[$artikel_typ][$artikel_id] = $artk["artikelbezeichnung"];
		$artikel_alle[$artikel_id] = $artk["artikelbezeichnung"];
    }
    unset($artikel);
    
    $records = $go_api->db->queryAllRecords("SELECT * from isp_fakt_record where web_id = $web_id and status = 1 order by record_id");
    
    $html =
'<div align="center">
<table width="98%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="23">&nbsp;</td>
    <td width="53">&nbsp;</td>
    <td width="370" height="20" class="normal" align="right">&nbsp;</td>
    <td width="42">&nbsp;</td>
  </tr>';
  foreach ($records as $rec) {
  	//if($rec["artikel_id"] > 0) {
  	//$artikel = $go_api->db->queryOneRecord("SELECT * from isp_fakt_artikel where doc_id = ".$rec["artikel_id"]);
  	$artikel_typ = $rec["typ"];
	//} else {
	//	$artikel_typ = 0;
	//}
  	$artikel_option = "<option value='0'>".$go_api->lng("Bitte Artikel auswählen")."</option>";
  	if($artikel_typ != '') {
		if(is_array($artikel_liste[$artikel_typ])) {
			foreach($artikel_liste[$artikel_typ] as $key => $val) {
        		$artikel_option .= "<option value='$key'".(($rec["artikel_id"] == $key)?'SELECTED':'').">$val</option>";
    		}
		}
	} else {
		foreach($artikel_alle as $key => $val) {
        	$artikel_option .= "<option value='$key'".(($rec["artikel_id"] == $key)?'SELECTED':'').">$val</option>";
    	}
	}
  
  $html .= 
  '<tr>
    <td class="normal">&nbsp;</td>
    <td class="normal"><input name="plugin['.$rec["record_id"].'][anzahl]" type="text" value="'.$rec["anzahl"].'" size="2"></td>
    <td class="normal"><select name="plugin['.$rec["record_id"].'][artikel_id]">
      '.$artikel_option.'
    </select></td>
    <td class="normal">'.(($rec["manuell"] == 1)?'[<a href="../../isp_fakt/new/record_delete.php?s='.$s.'&record_id='.$rec["record_id"].'&web_id='.$doc_id.'">X</a>]':'').'</td>
  </tr>';
  
  if($rec["manuell"] != 1) {
  $html .= '<tr>
    <td class="normal">&nbsp;</td>
    <td class="normal" colspan="2"><b>'.$rec["typ"].':</b> '.$rec["notiz"].'</td>
    <td class="normal">&nbsp;</td>
  </tr>';
  }
  $html .= '<tr>
    <td class="normal">&nbsp;</td>
    <td class="normal" colspan="2"><b>'.$go_api->lng("Letzte Abrechnung").':</b> <input type="text" name="plugin['.$rec["record_id"].'][letzte_abrechnung]" size="10" class="text" value="'.(($rec["letzte_abrechnung"] > 0)?date("d.m.Y",$rec["letzte_abrechnung"]):"").'"></input> (Format: TT.MM.YYYY)</td>
    <td class="normal">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" class="normal"><hr size="1" noshade></td>
    </tr>';
  }
  $html .= 
  '
  <tr>
    <td align="center" colspan="4" class="normal" height="30" valign="middle">
	'."<input type=\"submit\" name=\"hinzufuegen\" value=\" ".$go_api->lng("Artikel hinzufügen")." \" class=\"button\" onClick=\"window.location.href = '../../isp_fakt/new/record.php?s=".$s."&web_id=".$doc_id."';return false;\">".'
	</a></td>
  </tr>
</table></div>';
    
    $html_out = $html;
	
	if(!$go_api->auth->check_admin(0,1)) $html_out = '';
    
    return $html_out;
    }
    
    
    function insert($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;
    
    
    return true;
    }
    
    function update($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api,$HTTP_POST_VARS;
	//die("Bin Da");
	$plugin = $HTTP_POST_VARS["plugin"];
	if(is_array($plugin)) {
		foreach($plugin as $key => $val) {
			$key = intval($key);
			$anzahl = intval($val["anzahl"]);
			$artikel_id = intval($val["artikel_id"]);
			if($val["letzte_abrechnung"]) {
				list( $day, $month, $year ) = split( '[/.-]', $val["letzte_abrechnung"]);
	    		$letzte_abrechnung = mktime(0,0,0,$month,$day,$year);
			} else {
				$letzte_abrechnung = 0;
			}
			$sql = "UPDATE isp_fakt_record SET anzahl = $anzahl, artikel_id = $artikel_id, letzte_abrechnung = $letzte_abrechnung where record_id = $key";

			$go_api->db->query($sql);
			$letzte_abrechnung = 0;
		}	
    }
	
    return true;
    }
    
    function delete($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;
    
    return true;
    }
    
    function undelete($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;
    
    return true;
    }
}
?>