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

$go_api->content->define( array(    main    => "main.htm",
		                            table   => "hilfe_list_tickets.htm",
		                            stylesheet => "style.css"));

$go_api->content->define_dynamic ( "liste", "table" );

$go_api->content->assign( array(    TITLE => $go_api->lng("Unterstützungskarten"),
						            SESSION => $session,
						            BACKGROUND_GIF => "",
						            COPYRIGHT => "Tribal-Dolphin",
						            FGCOLOR => "$session_nav_hcolour",
						            TABLE_H_COLOR => "#FFFFFF",
						            BOXSIZE => "450",
						            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Unterstützungskarten")."</font>",
						            SITENAME => "$session_site",
						            DESIGNPATH => $session_design_path,
									SERVERURL => $go_info["server"]["server_url"],
						            S => $s,
									GKARTE => $go_api->lng("Geöffnete Karten"),
									SUBJECT => $go_api->lng("Betreff"),
									PRIORITY => $go_api->lng("Priorität"),
									DATE => $go_api->lng("Datum")
                        			) );

$vtemp = array();
$opened_tickets = array();

$sql = "SELECT doc_id,ticket_subject,ticket_urgency,ticket_date FROM help_tickets WHERE (ticket_to=".$go_info["user"]["userid"]." OR ticket_from=".$go_info["user"]["userid"].") AND ticket_status='O'";
$opened_tickets = $go_api->db->queryAllRecords($sql);

if(is_array($opened_tickets)) {
	foreach($opened_tickets as $row) {
		$vtemp["TID"] = $row["doc_id"];
		$vtemp["TSUBJECT"] = $row["ticket_subject"];
		switch($row["ticket_urgency"]) {
			case "1" : $vtemp["TPRIORITY"] = $go_api->lng("Hoch"); break;
			case "3" : $vtemp["TPRIORITY"] = $go_api->lng("Mittel"); break;
			case "5" : $vtemp["TPRIORITY"] = $go_api->lng("Niedrig"); break;
		}
		$vtemp["TDATE"] = $row["ticket_date"];

   		if($bgcolor == "#CCCCCC") {
   			$bgcolor = "#EEEEEE";
   		} else {
   			$bgcolor = "#CCCCCC";
   		}
   		$vtemp["BGCOLOR"] = $bgcolor;
		
		$go_api->content->assign($vtemp);
   		$go_api->content->parse(LISTE,".liste");
  	}
}

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();

exit;
?>