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

$go_api->content->define(   array(      main    => "main.htm",
                                    	table   => "tools_standard.htm",
                                    	stylesheet => "style.css"));

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "$session_page_hcolour",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; Change IP</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                            CHARSET => $go_info["theme"]["charset"],
                                                            SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                                                                    ) );

// BEGIN TOOL ###########################################################################

$go_api->auth->check_admin(0);

$result = '';

if($_FILES["file"]['tmp_name'] != '') {
	$lines = file ($_FILES["file"]['tmp_name']);
	foreach($lines as $line) {
		$parts = explode(';',$line);
		
		$domain = trim($parts[0]);
		$ip_old = trim($parts[1]);
		$ip_new = trim($parts[2]);
		
		// Updating the SOA
		$sql = "SELECT * FROM dns_isp_dns WHERE dns_soa = '$domain'";
		$dns_soa = $go_api->db->queryOneRecord($sql);
		if($dns_soa["dns_soa_ip"] == $ip_old) {
			$result .= "Changed: $domain<br>";
			$sql = "UPDATE dns_isp_dns SET dns_soa_ip = '$ip_new', status = 'u' WHERE dns_soa = '$domain'";
			$go_api->db->query($sql);
			//echo $sql;
		} else {
			$result .= "NOT Changed: $domain<br>";
		}
		
		// Updating the A-Records of the SOA
		$sql = "SELECT * FROM dns_dep, dns_a WHERE dns_dep.child_doc_id = dns_a.doc_id AND dns_dep.child_doctype_id = 1018 AND dns_dep.parent_doc_id = ".$dns_soa["doc_id"];
		$records = $go_api->db->queryAllRecords($sql);
		if(is_array($records)) {
			foreach($records as $rec) {
				if($rec["ip_adresse"] == $ip_old) {
					$result .= "Changed: $domain A-Record: ".$rec["host"]."<br>";
					$sql = "UPDATE dns_a SET ip_adresse = '$ip_new' WHERE doc_id = ".$rec["doc_id"];
					$go_api->db->query($sql);
					//echo $sql;
				}
			}
		}
	}
}





$html_out = '&nbsp;<br><form name="form1" method="post" action="" enctype="multipart/form-data">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" class="t2">
				  The file must be in the format:<br>ybr>
				  domain.com; OLD-IP-Address; New-IP-Address
				  <br><br>CSV file:
                    <input type="file" name="file" id="file" />
                    &nbsp;
                    <input type="submit" name="Submit" value="Start" class="button"> </td>
                </tr>
              </table>
            </form><br>&nbsp;<br>';

$html_out = $html_out . $result;

// END TOOL #############################################################################

$go_api->content->assign( array( TOOL => $html_out));
$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>