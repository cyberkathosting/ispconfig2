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

if(is_array($go_api->groups->myGroups())) {
//This user is a reseller, because clients do not have a group!
  //echo "Resseller<br />";
  $to_mail = $go_info["server"]["log_mail"];
  $to_id = 1;
} else {
// This must be a client, now get the sys_user.doc_id of the reseller
  $reseller = $go_api->db->queryOneRecord("SELECT userid FROM sys_user WHERE doc_id = ".$go_info["user"]["userid"]);
  $r_doc_id = $reseller["userid"];
  if($r_doc_id == 1) {
    //echo "Client of Admin<br />";
    $to_mail = $go_info["server"]["log_mail"];
    $to_id = 1;
  } else {
    //echo "Client of a Resseller<br />";
        $reslmail = $go_api->db->queryOneRecord("SELECT email FROM isp_isp_reseller WHERE reseller_userid = $r_doc_id");
        $to_mail = $reslmail["email"];
        $to_id = $r_doc_id;
  }
}

$from_id = $go_info["user"]["userid"];
$subject = addslashes(strip_tags($subject));
$request = addslashes(strip_tags($request));

// Insert into database
$sql = "INSERT INTO help_tickets (ticket_from,ticket_to,ticket_status,ticket_urgency,ticket_subject,ticket_message,ticket_date) VALUES ($from_id,$to_id,\"O\",\"$priority\",\"$subject\",\"$request\",NOW())";
if(!$go_api->db->query($sql)) $go_api->errorMessage($go_api->lng("Fehler während der schaffung der karte"));

// Prepare and Send Mail
$from_name = $go_info["user"]["lastname"]." ".$go_info["user"]["firstname"];
$from_mail = $go_info["user"]["email"];

$message = $go_api->lng("Neue unterstützungskarte, die auf antwort wartet");

// Creating E-Mail message
$headers  = "From: ".$from_name." <".$from_mail.">\n";
$headers .= "Reply-To: <".$from_mail.">\n";
$headers .= "Return-Path: <".$from_mail.">\n";
$headers .= "X-Sender: <".$from_mail.">\n";
$headers .= "X-Mailer: PHP5\n"; //mailer
$headers .= "X-Priority: ".$priority."\n"; //1 UrgentMessage, 3 Normal
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/plain\n";

mail($to_mail, $go_api->lng("Neue Karte")." ".$subject, $message, $headers);

header("Location: ../../index.php?s=$s");
?>