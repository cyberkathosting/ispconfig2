<?

/*
Copyright (c) 2007, projektfarm Gmbh, Till Brehm, Falko Timme
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
		                    table   => "tools_standard.htm",
		                    stylesheet => "style.css"));
		
$go_api->content->assign( array(    TITLE => "$session_site Startseite",
						            SESSION => $session,
						            BACKGROUND_GIF => "",
						            COPYRIGHT => "",
						            FGCOLOR => "$session_nav_hcolour",
						            TABLE_H_COLOR => "$session_page_hcolour",
						            BOXSIZE => "450",
						            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Remoting User")."</font>",
						            SITENAME => "$session_site",
						            DESIGNPATH => $session_design_path,
                                    SERVERURL => $go_info["server"]["server_url"],
						            S => $s
                                    ) );

// BEGIN TOOL ###########################################################################

$go_api->auth->check_admin(0);

$users = $go_api->db->queryAllRecords("SELECT * FROM remote_user WHERE 1 ORDER BY username");

$html_out = '
<table width="444" border="0" cellspacing="4" cellpadding="0">
  <tr>
    <td width="20">&nbsp;</td>
    <td width="393">&nbsp;</td>
  </tr>
  <tr>
    <td class="t2">&nbsp;</td>
    <td class="t2b">&nbsp;User [<a href="useredit.php?'.$session.'">'.$go_api->lng("Hinzufügen").'</a>]</td>
  </tr>
  <tr bgcolor="'.$session_nav_hcolour.'">
    <td colspan="3" height="1"><img src="../../../'.$session_design_path.'/x.gif"></td>
  </tr>';
  
  if(is_array($users)) {
  	foreach($users as $user) {
  		$html_out .= '<tr>
    		<td class="t2"><img src="../../../'.$session_design_path.'/icons/user.gif"></td>
    		<td class="t2" bgcolor="#FFFFFF">&nbsp;<a href="useredit.php?uid='.$user["ruserid"].'&'.$session.'" class="t2">'.$user["username"].'</a></td>
  		</tr>';
  	}
  }
  
  
  $html_out .= '<tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>';

// END TOOL #############################################################################

$go_api->content->assign( array( TOOL => $html_out));
$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>