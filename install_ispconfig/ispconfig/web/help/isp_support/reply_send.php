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
$set_header = 0;
include("../../../lib/session.inc.php");

if($go_info["server"]["mode"] == 'demo') $go_api->errorMessage("Aktion im Demo-Modus nicht möglich.");

$tid = intval($tid);

switch($Submit){
	case $go_api->lng("Antworten") :
		$sql = "SELECT ticket_from,ticket_to,ticket_urgency FROM help_tickets WHERE doc_id=$tid";
		$ticket_data = $go_api->db->queryOneRecord($sql);
		if($go_info["user"]["userid"] == $ticket_data["ticket_to"]) {
			$new_ticket_from = $go_info["user"]["userid"];
			$new_ticket_to = $ticket_data["ticket_from"];
		} else {
			$new_ticket_from = $ticket_data["ticket_from"];
			$new_ticket_to = $go_info["user"]["userid"];
		}
    $answer = addslashes(strip_tags($answer));
		$sql = "INSERT INTO help_tickets (ticket_from,ticket_to,ticket_reply,ticket_message,ticket_date) VALUES ($new_ticket_from,$new_ticket_to,$tid,\"$answer\",NOW())";
		$go_api->db->query($sql);
		// Send Mail
		$to_usertype = $go_api->auth->user_type($new_ticket_to);
		if($from_usertype == "admin") {
			$to_mail = $go_info["server"]["log_mail"];
		} elseif($from_usertype == "client") {
			$dest = $go_api->db->queryOneRecord("SELECT kunde_email FROM isp_isp_kunde WHERE webadmin_userid = $new_ticket_to");
			$to_mail = $dest["kunde_email"];
		} else {
			$dest = $go_api->db->queryOneRecord("SELECT email FROM isp_isp_reseller WHERE reseller_userid = $new_ticket_to");
			$to_mail = $dest["email"];
		}
		if($go_info["user"]["userid"] == 1) {
			$from_mail = $go_info["server"]["log_mail"];
			$from_name = "Admin";
		} else {
			$from_mail = $go_info["user"]["email"];
			$from_name = $go_info["user"]["lastname"]." ".$go_info["user"]["firstname"];
		}
		$message = $from_name." ".$go_api->lng("geantwortet zu ihnen");
		$headers  = "From: ".$from_name." <".$from_mail.">\r\n";
		$headers .= "Reply-To: <".$from_mail.">\r\n";
		$headers .= "Return-Path: <".$from_mail.">\r\n";
		$headers .= "X-Sender: <".$from_mail.">\r\n";
		$headers .= "X-Mailer: PHP5\r\n"; //mailer
		$headers .= "X-Priority: ".$ticket_data["ticket_urgency"]."\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/plain\r\n";
		mail($to_mail, $go_api->lng("Sie erhielten eine antwort"), $message, $headers);		
	break;
	case $go_api->lng("Antworten")."&".$go_api->lng("Schließen") :
		$sql = "SELECT ticket_from,ticket_to FROM help_tickets WHERE doc_id=$tid";
		$ticket_data = $go_api->db->queryOneRecord($sql);
		if($go_info["user"]["userid"] == $ticket_data["ticket_to"]) {
			$new_ticket_from = $go_info["user"]["userid"];
			$new_ticket_to = $ticket_data["ticket_from"];
		} else {
			$new_ticket_from = $ticket_data["ticket_from"];
			$new_ticket_to = $go_info["user"]["userid"];
		}
    $answer = addslashes(strip_tags($answer));
		$sql = "INSERT INTO help_tickets (ticket_from,ticket_to,ticket_reply,ticket_message,ticket_date) VALUES ($new_ticket_from,$new_ticket_to,$tid,\"$answer\",NOW())";
		$go_api->db->query($sql);
		// Send Mail
		$to_usertype = $go_api->auth->user_type($new_ticket_to);
		if($from_usertype == "admin") {
			$to_mail = $go_info["server"]["log_mail"];
		} elseif($from_usertype == "client") {
			$dest = $go_api->db->queryOneRecord("SELECT kunde_email FROM isp_isp_kunde WHERE webadmin_userid = $new_ticket_to");
			$to_mail = $dest["kunde_email"];
		} else {
			$dest = $go_api->db->queryOneRecord("SELECT email FROM isp_isp_reseller WHERE reseller_userid = $new_ticket_to");
			$to_mail = $dest["email"];
		}
		if($go_info["user"]["userid"] == 1) {
			$from_mail = $go_info["server"]["log_mail"];
			$from_name = "Admin";
		} else {
			$from_mail = $go_info["user"]["email"];
			$from_name = $go_info["user"]["lastname"]." ".$go_info["user"]["firstname"];
		}
		$message = $from_name." ".$go_api->lng("geantwortet zu ihnen");
		$headers  = "From: ".$from_name." <".$from_mail.">\r\n";
		$headers .= "Reply-To: <".$from_mail.">\r\n";
		$headers .= "Return-Path: <".$from_mail.">\r\n";
		$headers .= "X-Sender: <".$from_mail.">\r\n";
		$headers .= "X-Mailer: PHP5\r\n"; //mailer
		$headers .= "X-Priority: ".$ticket_data["ticket_urgency"]."\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/plain\r\n";
		mail($to_mail, $go_api->lng("Sie erhielten eine antwort"), $message, $headers);		
		// Close Ticket
		$sql = "UPDATE help_tickets SET ticket_status='C' WHERE doc_id=$tid";
		$go_api->db->query($sql);		
	break;
	case $go_api->lng("Schließen") :
		$sql = "UPDATE help_tickets SET ticket_status='C' WHERE doc_id=$tid";
		$go_api->db->query($sql);		
	break;
}

header("Location: ../../index.php?s=$s");
?>