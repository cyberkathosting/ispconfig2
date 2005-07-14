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
		                    table   => "tools_tools_reseller_mail.htm",
		                    stylesheet => "style.css"));
		
$go_api->content->assign( array(    TITLE => "Standard Index",
						            SESSION => $session,
						            BACKGROUND_GIF => "",
						            COPYRIGHT => "",
						            FGCOLOR => "$session_nav_hcolour",
						            TABLE_H_COLOR => "$session_page_hcolour",
						            BOXSIZE => "450",
						            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Client Email")."</font>",
						            SITENAME => "$session_site",
						            DESIGNPATH => $session_design_path,
                                    SERVERURL => $go_info["server"]["server_url"],
						            S => $s
                                    ));

// BEGIN TOOL ###########################################################################

$gruppen = $go_api->groups->myGroups();
if($go_api->auth->check_admin(0,1) or !is_array($gruppen)) die("Access not permitted.");

if(count($_POST) > 0) {
	// Speichern
	$client_salutatory_email_sender_email = addslashes($_POST["client_salutatory_email_sender_email"]);
	$client_salutatory_email_sender_name = addslashes($_POST["client_salutatory_email_sender_name"]);
	$client_salutatory_email_bcc = addslashes($_POST["client_salutatory_email_bcc"]);
	$client_salutatory_email_subject = addslashes($_POST["client_salutatory_email_subject"]);
	$client_salutatory_email_message = addslashes($_POST["client_salutatory_email_message"]);
	
	$sql = "UPDATE isp_isp_reseller SET 
	client_salutatory_email_sender_email = '$client_salutatory_email_sender_email', 
	client_salutatory_email_bcc = '$client_salutatory_email_bcc',
	client_salutatory_email_sender_name = '$client_salutatory_email_sender_name',
	client_salutatory_email_subject = '$client_salutatory_email_subject',
	client_salutatory_email_message = '$client_salutatory_email_message' WHERE reseller_userid = ".$go_info["user"]["userid"];
	// nicht für Admin updaten
	if($go_info["user"]["userid"] > 1) $go_api->db->query($sql);
	header("Location: ../../frame_start.php?s=$s");
	exit;
	
} else {
	// Anzeigen
	$reseller = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_reseller WHERE reseller_userid = ".$go_info["user"]["userid"]);
	$go_api->content->assign( array(	CLIENT_SALUTORY_EMAIL_SENDER_EMAIL => $reseller["client_salutatory_email_sender_email"],
										CLIENT_SALUTORY_EMAIL_SENDER_NAME => $reseller["client_salutatory_email_sender_name"],
										CLIENT_SALUTORY_EMAIL_BCC => $reseller["client_salutatory_email_bcc"],
										CLIENT_SALUTORY_EMAIL_SUBJECT => $reseller["client_salutatory_email_subject"],
										CLIENT_SALUTORY_EMAIL_MESSAGE => $reseller["client_salutatory_email_message"],
										TXT_CLIENT_SALUTORY_EMAIL_SENDER_EMAIL => $go_api->lng("txt_client_salutatory_email_sender_email"),
										TXT_CLIENT_SALUTORY_EMAIL_SENDER_NAME => $go_api->lng("txt_client_salutatory_email_sender_name"),
										TXT_CLIENT_SALUTORY_EMAIL_BCC => $go_api->lng("txt_client_salutatory_email_bcc"),
										TXT_CLIENT_SALUTORY_EMAIL_SUBJECT => $go_api->lng("txt_client_salutatory_email_subject"),
										TXT_CLIENT_SALUTORY_EMAIL_MESSAGE => $go_api->lng("txt_client_salutatory_email_message"),
										TXT_CLIENT_SALUTORY_EMAIL_VARIABLES => $go_api->lng("txt_client_salutatory_email_variables"),
										SPEICHERN => $go_api->lng("Speichern")));
	
}


// END TOOL #############################################################################

$go_api->content->assign( array( WEBS => $webs));
$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>