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

include("../../../../lib/config.inc.php");
include("../../../../lib/session.inc.php");

$go_api->content->define(   array(
		                    main    => "main.htm",
		                    table   => "tools_tools_ispbackup.htm",
		                    stylesheet => "style.css"));
		
$go_api->content->assign( array(    TITLE => "$session_site Backup",
						            SESSION => $session,
						            BACKGROUND_GIF => "",
						            COPYRIGHT => "",
						            FGCOLOR => "$session_nav_hcolour",
						            TABLE_H_COLOR => "$session_page_hcolour",
						            BOXSIZE => "450",
						            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Web-Backup")."</font>",
						            SITENAME => "$session_site",
						            DESIGNPATH => $session_design_path,
                                    SERVERURL => $go_info["server"]["server_url"],
						            S => $s,
									TXT_DOWNLOAD => $go_api->lng("Download"),
									TXT_FTP => $go_api->lng("FTP"),
									TXT_FTP_SERVER => $go_api->lng("FTP-Server"),
									TXT_VERZEICHNIS => $go_api->lng("Verzeichnis"),
									TXT_USERNAME => $go_api->lng("Username"),
									TXT_PASSWORT => $go_api->lng("Passwort"),
									TXT_WEB_DATEN => $go_api->lng("Web Daten"),
									TXT_USER_DATEN => $go_api->lng("User Daten"),
									TXT_LOG_DATEN => $go_api->lng("Log Daten"),
									TXT_MYSQL_DB => $go_api->lng("mySQL Datenbank (wenn vorhanden)"),
									TXT_BACKUP_STARTEN => $go_api->lng("Backup starten")
                                    ) );

// BEGIN TOOL ###########################################################################

// Wenn admin, dann alle Webs der Reseller anzeigen
$gruppen = $go_api->groups->myGroups();
$webs = '';
if(is_array($gruppen)) {
// Es ist Admin oder Reseller
	foreach($gruppen as $gid => $gval) {
		if($gval["userstatus"] == 1 and $gval["groupstatus"] == 1) {

		$webs .= '<tr bgcolor="#E0E0E0"> 
            <td colspan="2" class="t2b"><font color="#666666">&nbsp;'.$gval["name"].'</font></td>
          </tr>';
		  $n = 0;
		  // füge Webs der Gruppe ein
		  $web_nodes = $go_api->db->queryAllRecords("SELECT * FROM isp_nodes where doctype_id = 1013 and groupid = $gid");
		  	foreach($web_nodes as $wn) {
				$webs .= '<tr> 
            	<td width="25"> <div align="center"><input name="webs['.$wn["doc_id"].']" type="checkbox" value="1"></div></td>
            	<td width="859" class="t2">&nbsp;'.$wn["title"].'</td>
          		</tr>';
		  		$n++;
			}
			if($n == 0) {
				// Kein Web vorhanden
				$webs .= '<tr>
            	<td width="859" colspan="2" class="t2">&nbsp;'.$go_api->lng("Kein Web vorhanden.").'</td>
          		</tr>';
			}
			
			
		}
	}
} else {
// Es ist ein Kunde

		$webs .= '<tr bgcolor="#E0E0E0"> 
            <td colspan="2" class="t2b"><font color="#666666">&nbsp;'.$go_api->lng("Web(s)").'</font></td>
          </tr>';
		  $n = 0;
		  // füge Webs der Gruppe ein
		  $web_nodes = $go_api->db->queryAllRecords("SELECT * FROM isp_nodes where doctype_id = 1013 and userid = ".$go_info["user"]["userid"]);
		  	foreach($web_nodes as $wn) {
				$webs .= '<tr> 
            	<td width="25"> <div align="center"><input name="webs['.$wn["doc_id"].']" type="checkbox" value="1"></div></td>
            	<td width="859" class="t2">&nbsp;'.$wn["title"].'</td>
          		</tr>';
		  		$n++;
			}
			if($n == 0) {
				// Kein Web vorhanden
				$webs .= '<tr>
            	<td width="859" colspan="2" class="t2">&nbsp;'.$go_api->lng("Kein Web vorhanden.").'</td>
          		</tr>';
			}

}


// END TOOL #############################################################################

$go_api->content->assign( array( WEBS => $webs));
$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>