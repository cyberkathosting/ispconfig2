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

$go_api->content->define(   array(        main    => "main.htm",
                                    table   => "tools_standard.htm",
                                    stylesheet => "style.css"));

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "$session_page_hcolour",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; Update Assistant</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                            CHARSET => $go_info["theme"]["charset"],
                                                                                    SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                                                                    ) );

// BEGIN TOOL ###########################################################################

$go_api->auth->check_admin(0);

$version = $_REQUEST["version"];
if(!empty($version)) {
	
	if($version == '2.2-2.3') {
		
		// Add all co-domains to the user_emaildomain field
		$sql = "SELECT doc_id, doctype_id, web_host, web_domain from isp_isp_web WHERE optionen_local_mailserver = 1";
		$webs = $go_api->db->queryAllRecords($sql);
		if(is_array($webs)) {
		foreach($webs as $web) {
			$domain_array = array();
			if($web["web_host"] != '') {
				$domain_array[] = $web["web_host"].".".$web["web_domain"];
			} else {
				$domain_array[] = $web["web_domain"];
			}
			
			$sql = "SELECT * FROM isp_dep, isp_isp_domain WHERE isp_dep.parent_doc_id = $web[doc_id] and isp_dep.parent_doctype_id = 1013
			and isp_dep.child_doc_id = isp_isp_domain.doc_id and isp_dep.child_doctype_id = 1015";
			
			$domains = $go_api->db->queryAllRecords($sql);
			if(is_array($domains)) {
				foreach($domains as $domain) {
					if($domain["domain_host"] != '') {
						$domain_array[] = $domain["domain_host"].".".$domain["domain_domain"];
					} else {
						$domain_array[] = $domain["domain_domain"];
					}
				}
			}
			
			$domain_string = addslashes(implode("\n",$domain_array));
			
			$sql = "SELECT * FROM isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = $web[doc_id] and isp_dep.parent_doctype_id = 1013
			and isp_dep.child_doc_id = isp_isp_user.doc_id and isp_dep.child_doctype_id = 1014";
			
			$users = $go_api->db->queryAllRecords($sql);
			if(is_array($users)) {
				foreach($users as $u) {
					$sql = "update isp_isp_user SET user_emaildomain = '$domain_string' WHERE doc_id = $u[doc_id]";
					$go_api->db->query($sql);
					$result .= "Updated User ".$u["user_username"]."<br>";
				}
			} else {
				$result .= "No users updated.";
			}
		}
		} else {
			$result .= "No Websites found.";
		}
		
	$result .= "Finished migration from 2.2.x to 2.3.x.<br><br>";
	} // endif  version 2.2-2.3
}

$html_out = '&nbsp;<br><form name="form1" method="post" action="">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" class="t2">
                    <input type="radio" name="version" value="2.2-2.3" id="version" /> Migrate database from 2.2.x to 2.3.x
					<br><br>
                    <input type="submit" name="Submit" value="Start Migration" class="button"> </td>
                </tr>
              </table>
            </form><br>&nbsp;<br>';

$html_out = $html_out . "<center>".$result."</center>";

// END TOOL #############################################################################

$go_api->content->assign( array( TOOL => $html_out));
$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>