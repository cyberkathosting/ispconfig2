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

set_time_limit(0);

$kunde_doctype_id = 1012;
$web_doctype_id = 1013;
$user_doctype_id = 1014;
$reseller_doctype_id = 1022;
include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/config.lib.php");
include("/root/ispconfig/scripts/lib/server.inc.php");
$isp_web = new isp_web;
$server_id = $mod->system->server_id;

$server = $mod->system->server_conf;
$webroot = stripslashes($server["server_path_httpd_root"]);

$month = date("m/Y");

// Monatsanfang: gesperrte Webseiten zurücksetzen
if(date("m/Y", (time() - 86400)) != $month){
  if($suspended_webs = $mod->db->queryAllRecords("SELECT * FROM isp_isp_web WHERE web_traffic_status = '2'")){
    foreach($suspended_webs as $suspended_web){
      if($admin_user = $mod->db->queryOneRecord("SELECT isp_isp_user.user_username FROM isp_nodes, isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = '".$suspended_web['doc_id']."' AND isp_dep.parent_doctype_id = '".$web_doctype_id."' AND isp_dep.child_doc_id = isp_isp_user.doc_id AND isp_dep.child_doctype_id = '".$user_doctype_id."' AND isp_isp_user.user_admin = '1' AND isp_nodes.doc_id = isp_isp_user.doc_id AND isp_nodes.doctype_id = '".$user_doctype_id."' AND isp_nodes.status = '1'")){
        $admin_user = $admin_user['user_username'];
      } else {
        $admin_user = $isp_web->apache_user;
      }
      exec("chown ".$admin_user." ".$webroot."/web".$suspended_web['doc_id']." &> /dev/null");
      exec("chmod 755 ".$webroot."/web".$suspended_web['doc_id']." &> /dev/null");
      $mod->db->query("UPDATE isp_isp_web SET web_traffic_status = '1' WHERE doc_id = '".$suspended_web['doc_id']."'");
    }
  }
}


// Webseiten mit Übertraffic feststellen und geeignete Aktion einleiten
if($webs = $mod->db->queryAllRecords("SELECT isp_traffic.web_id, isp_traffic.bytes_web, isp_traffic.bytes_ftp, isp_traffic.bytes_mail, isp_isp_web.web_traffic, isp_isp_web.web_traffic_ueberschreitung, isp_isp_web.web_host, isp_isp_web.web_domain, isp_isp_web.web_traffic_status FROM isp_traffic, isp_isp_web, isp_nodes WHERE isp_traffic.monat = '".$month."' AND isp_traffic.web_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = isp_isp_web.doctype_id AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.status = 1 AND isp_isp_web.web_traffic_ueberschreitung != '3' AND isp_isp_web.web_traffic >= 0")){

  foreach($webs as $web){
    $total_traffic = $web['bytes_web'] + $web['bytes_ftp'] + $web['bytes_mail'];

    if($total_traffic >= ($web['web_traffic'] * 1048576)){

      // Kunde zu Web bestimmen
      $kunde = $mod->db->queryOneRecord("SELECT isp_isp_kunde.kunde_email, isp_isp_kunde.doc_id, isp_isp_kunde.kunde_vorname, isp_isp_kunde.kunde_name FROM isp_isp_kunde, isp_dep WHERE isp_dep.parent_doc_id = isp_isp_kunde.doc_id AND isp_dep.child_doc_id = ".$web['web_id']." AND isp_dep.child_doctype_id = ".$web_doctype_id." AND isp_dep.parent_doctype_id = ".$kunde_doctype_id);

      // Absender finden
      $kunde_node = $mod->db->queryOneRecord("SELECT * FROM isp_nodes WHERE doc_id = '".$kunde['doc_id']."' and doctype_id = '".$kunde_doctype_id."'");
      $reseller_group = $kunde_node["groupid"];
      $reseller = $mod->db->queryOneRecord("SELECT * FROM isp_isp_reseller WHERE reseller_group = $reseller_group");

      switch ($web['web_traffic_ueberschreitung']) {
      case 1:  // Suspension
        if($web['web_traffic_status'] != 2){
          exec("chown root ".$webroot."/web".$web['web_id']." &> /dev/null");
          exec("chmod 400 ".$webroot."/web".$web['web_id']." &> /dev/null");
          $mod->db->query("UPDATE isp_isp_web SET web_traffic_status = '2' WHERE doc_id = '".$web['web_id']."'");

          if($reseller){
            $absender_email = $reseller["traffic_suspension_sender_email"];
            $absender_name = $reseller["traffic_suspension_sender_name"];
            $bcc = $reseller["traffic_suspension_email_bcc"];
            $subject = $reseller["traffic_suspension_email_subject"];
            $message = $reseller["traffic_suspension_email_message"];
          } else {
            $absender_email = $server["traffic_suspension_sender_email"];
            $absender_name = $server["traffic_suspension_sender_name"];
            $bcc = $server["traffic_suspension_email_bcc"];
            $subject = $server["traffic_suspension_email_subject"];
            $message = $server["traffic_suspension_email_message"];
          }
          if($absender_email == '') $absender_email = $server['server_admin_email'];

          if($kunde["kunde_email"] != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $kunde["kunde_email"]) && !strstr($kunde["kunde_email"], " ") && !strstr($kunde["kunde_email"], "!") && !strstr($kunde["kunde_email"], "?") && !strstr($kunde["kunde_email"], "\"") && !strstr($kunde["kunde_email"], "(") && !strstr($kunde["kunde_email"], ")") && !strstr($kunde["kunde_email"], "[") && !strstr($kunde["kunde_email"], "]") && !strstr($kunde["kunde_email"], "{") && !strstr($kunde["kunde_email"], "}") && !strstr($kunde["kunde_email"], "/") && !strstr($kunde["kunde_email"], "#") && $absender_email != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $absender_email) && !strstr($absender_email, " ") && !strstr($absender_email, "!") && !strstr($absender_email, "?") && !strstr($absender_email, "\"") && !strstr($absender_email, "(") && !strstr($absender_email, ")") && !strstr($absender_email, "[") && !strstr($absender_email, "]") && !strstr($absender_email, "{") && !strstr($absender_email, "}") && !strstr($absender_email, "/") && !strstr($absender_email, "#")){

            if($subject == '') $subject = 'Traffic limit exceeded for '.$web['web_host'].($web['web_host'] == "" ? "" : ".").$web['web_domain'].' in '.$month;
            if($message == ''){
              $message = 'The traffic limit for '.$web['web_host'].($web['web_host'] == "" ? "" : ".").$web['web_domain'].' ('.$web['web_traffic'].'MB) has been exceeded in '.$month.'.
Your web site has been suspended for the rest of this month!';
            } else {
              $message = str_replace("%%%FIRST_NAME%%%", $kunde["kunde_vorname"], $message);
              $message = str_replace("%%%LAST_NAME%%%", $kunde["kunde_name"], $message);
              $message = str_replace("%%%WEBSITE%%%", $web['web_host'].($web['web_host'] == "" ? "" : ".").$web['web_domain'], $message);
              $message = str_replace("%%%MONTH%%%", $month, $message);
              $message = str_replace("%%%INCLUDED_TRAFFIC%%%", number_format($web['web_traffic'], 2, '.', '').'MB', $message);
              $message = str_replace("%%%TOTAL_TRAFFIC%%%", number_format(($total_traffic/1048576), 2, '.', '').'MB', $message);
            }
            $headers  = "From: ".$absender_name." <".$absender_email.">\n";
            if($bcc != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $bcc) && !strstr($bcc, " ") && !strstr($bcc, "!") && !strstr($bcc, "?") && !strstr($bcc, "\"") && !strstr($bcc, "(") && !strstr($bcc, ")") && !strstr($bcc, "[") && !strstr($bcc, "]") && !strstr($bcc, "{") && !strstr($bcc, "}") && !strstr($bcc, "/") && !strstr($bcc, "#")) $headers .= "Bcc: ".$bcc."\n";
            $headers .= "Reply-To: <".$absender_email.">\n";
            $headers .= "X-Sender: <".$absender_email.">\n";
            $headers .= "X-Mailer: PHP4\n"; //mailer
            $headers .= "X-Priority: 3\n"; //1 UrgentMessage, 3 Normal
            $headers .= "Return-Path: <".$absender_email.">\n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-Type: text/plain\n";
            mail($kunde["kunde_email"], $subject, $message, $headers);
          }
        }
      break;
      case 2:  // Notification

        if($reseller){
          $absender_email = $reseller["traffic_notification_sender_email"];
          $absender_name = $reseller["traffic_notification_sender_name"];
          $bcc = $reseller["traffic_notification_email_bcc"];
          $subject = $reseller["traffic_notification_email_subject"];
          $message = $reseller["traffic_notification_email_message"];
        } else {
          $absender_email = $server["traffic_notification_sender_email"];
          $absender_name = $server["traffic_notification_sender_name"];
          $bcc = $server["traffic_notification_email_bcc"];
          $subject = $server["traffic_notification_email_subject"];
          $message = $server["traffic_notification_email_message"];
        }
        if($absender_email == '') $absender_email = $server['server_admin_email'];

        if($kunde["kunde_email"] != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $kunde["kunde_email"]) && !strstr($kunde["kunde_email"], " ") && !strstr($kunde["kunde_email"], "!") && !strstr($kunde["kunde_email"], "?") && !strstr($kunde["kunde_email"], "\"") && !strstr($kunde["kunde_email"], "(") && !strstr($kunde["kunde_email"], ")") && !strstr($kunde["kunde_email"], "[") && !strstr($kunde["kunde_email"], "]") && !strstr($kunde["kunde_email"], "{") && !strstr($kunde["kunde_email"], "}") && !strstr($kunde["kunde_email"], "/") && !strstr($kunde["kunde_email"], "#") && $absender_email != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $absender_email) && !strstr($absender_email, " ") && !strstr($absender_email, "!") && !strstr($absender_email, "?") && !strstr($absender_email, "\"") && !strstr($absender_email, "(") && !strstr($absender_email, ")") && !strstr($absender_email, "[") && !strstr($absender_email, "]") && !strstr($absender_email, "{") && !strstr($absender_email, "}") && !strstr($absender_email, "/") && !strstr($absender_email, "#")){

          if($subject == '') $subject = 'Traffic limit exceeded for '.$web['web_host'].($web['web_host'] == "" ? "" : ".").$web['web_domain'].' in '.$month;
          if($message == ''){
            $message = 'The traffic limit for '.$web['web_host'].($web['web_host'] == "" ? "" : ".").$web['web_domain'].' ('.$web['web_traffic'].'MB) has been exceeded in '.$month.'.
However, your web site will stay online, but you might have to pay for the extra traffic!';
          } else {
            $message = str_replace("%%%FIRST_NAME%%%", $kunde["kunde_vorname"], $message);
            $message = str_replace("%%%LAST_NAME%%%", $kunde["kunde_name"], $message);
            $message = str_replace("%%%WEBSITE%%%", $web['web_host'].($web['web_host'] == "" ? "" : ".").$web['web_domain'], $message);
            $message = str_replace("%%%MONTH%%%", $month, $message);
            $message = str_replace("%%%INCLUDED_TRAFFIC%%%", number_format($web['web_traffic'], 2, '.', '').'MB', $message);
            $message = str_replace("%%%TOTAL_TRAFFIC%%%", number_format(($total_traffic/1048576), 2, '.', '').'MB', $message);
          }
          $headers  = "From: ".$absender_name." <".$absender_email.">\n";
          if($bcc != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $bcc) && !strstr($bcc, " ") && !strstr($bcc, "!") && !strstr($bcc, "?") && !strstr($bcc, "\"") && !strstr($bcc, "(") && !strstr($bcc, ")") && !strstr($bcc, "[") && !strstr($bcc, "]") && !strstr($bcc, "{") && !strstr($bcc, "}") && !strstr($bcc, "/") && !strstr($bcc, "#")) $headers .= "Bcc: ".$bcc."\n";
          $headers .= "Reply-To: <".$absender_email.">\n";
          $headers .= "X-Sender: <".$absender_email.">\n";
          $headers .= "X-Mailer: PHP4\n"; //mailer
          $headers .= "X-Priority: 3\n"; //1 UrgentMessage, 3 Normal
          $headers .= "Return-Path: <".$absender_email.">\n";
          $headers .= "MIME-Version: 1.0\n";
          $headers .= "Content-Type: text/plain\n";
          mail($kunde["kunde_email"], $subject, $message, $headers);
        }
      break;
      }
      unset($message);
      unset($subject);
    }
  }

}


// Reseller mit Trafficlimit feststellen und geeignete Aktion einleiten
if($resellers = $mod->db->queryAllRecords("SELECT isp_isp_reseller.doc_id, isp_isp_reseller.reseller_group, isp_isp_reseller.limit_traffic_ueberschreitung, isp_isp_reseller.email, isp_isp_reseller.limit_traffic FROM isp_isp_reseller, isp_nodes WHERE isp_isp_reseller.limit_traffic >= 0 AND isp_isp_reseller.limit_traffic_ueberschreitung != '3' AND isp_nodes.doctype_id = isp_isp_reseller.doctype_id AND isp_nodes.doc_id = isp_isp_reseller.doc_id AND isp_nodes.status = 1")){

  foreach($resellers as $reseller){
    $total_traffic = 0;
    if($webs = $mod->db->queryAllRecords("SELECT isp_isp_web.doc_id, isp_isp_web.web_traffic, isp_isp_web.web_host, isp_isp_web.web_domain, isp_traffic.bytes_web, isp_traffic.bytes_ftp, isp_traffic.bytes_mail FROM isp_traffic, isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_traffic.monat = '".$month."' AND isp_traffic.web_id = isp_isp_web.doc_id")){

      foreach($webs as $web){
        $total_traffic += $web['bytes_web'] + $web['bytes_ftp'] + $web['bytes_mail'];
      }

      if($total_traffic >= ($reseller['limit_traffic'] * 1048576)){
        switch ($reseller['limit_traffic_ueberschreitung']) {
        case 1: // Suspension

          foreach($webs as $web){
            exec("chown root ".$webroot."/web".$web['doc_id']." &> /dev/null");
            exec("chmod 400 ".$webroot."/web".$web['doc_id']." &> /dev/null");
            $mod->db->query("UPDATE isp_isp_web SET web_traffic_status = '2' WHERE doc_id = '".$web['doc_id']."'");
          }

          $absender_email = $server["res_traffic_notification_sender_email"];
          $absender_name = $server["res_traffic_notification_sender_name"];
          $bcc = $server["res_traffic_notification_email_bcc"];
          $subject = $server["res_traffic_notification_email_subject"];
          $message = $server["res_traffic_notification_email_message"];
          if($absender_email == '') $absender_email = $server['server_admin_email'];

          if($reseller["email"] != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $reseller["email"]) && !strstr($reseller["email"], " ") && !strstr($reseller["email"], "!") && !strstr($reseller["email"], "?") && !strstr($reseller["email"], "\"") && !strstr($reseller["email"], "(") && !strstr($reseller["email"], ")") && !strstr($reseller["email"], "[") && !strstr($reseller["email"], "]") && !strstr($reseller["email"], "{") && !strstr($reseller["email"], "}") && !strstr($reseller["email"], "/") && !strstr($reseller["email"], "#") && $absender_email != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $absender_email) && !strstr($absender_email, " ") && !strstr($absender_email, "!") && !strstr($absender_email, "?") && !strstr($absender_email, "\"") && !strstr($absender_email, "(") && !strstr($absender_email, ")") && !strstr($absender_email, "[") && !strstr($absender_email, "]") && !strstr($absender_email, "{") && !strstr($absender_email, "}") && !strstr($absender_email, "/") && !strstr($absender_email, "#")){

            if($subject == '') $subject = 'Traffic limit exceeded for your reseller account in '.$month;
            if($message == ''){
              $message = 'The traffic limit for your reseller account ('.$reseller['limit_traffic'].'MB) has been exceeded in '.$month.'.
All your web sites have been suspended until the end of this month!';
            } else {
              $message = str_replace("%%%FIRST_NAME%%%", $reseller["vorname"], $message);
              $message = str_replace("%%%LAST_NAME%%%", $reseller["name"], $message);
              $message = str_replace("%%%MONTH%%%", $month, $message);
              $message = str_replace("%%%INCLUDED_TRAFFIC%%%", number_format($reseller['limit_traffic'], 2, '.', '').'MB', $message);
              $message = str_replace("%%%TOTAL_TRAFFIC%%%", number_format(($total_traffic/1048576), 2, '.', '').'MB', $message);
            }
            $headers  = "From: ".$absender_name." <".$absender_email.">\n";
            if($bcc != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $bcc) && !strstr($bcc, " ") && !strstr($bcc, "!") && !strstr($bcc, "?") && !strstr($bcc, "\"") && !strstr($bcc, "(") && !strstr($bcc, ")") && !strstr($bcc, "[") && !strstr($bcc, "]") && !strstr($bcc, "{") && !strstr($bcc, "}") && !strstr($bcc, "/") && !strstr($bcc, "#")) $headers .= "Bcc: ".$bcc."\n";
            $headers .= "Reply-To: <".$absender_email.">\n";
            $headers .= "X-Sender: <".$absender_email.">\n";
            $headers .= "X-Mailer: PHP4\n"; //mailer
            $headers .= "X-Priority: 3\n"; //1 UrgentMessage, 3 Normal
            $headers .= "Return-Path: <".$absender_email.">\n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-Type: text/plain\n";
            mail($reseller["email"], $subject, $message, $headers);
          }


        break;
        case 2:  // Notification

          $absender_email = $server["res_traffic_notification_sender_email"];
          $absender_name = $server["res_traffic_notification_sender_name"];
          $bcc = $server["res_traffic_notification_email_bcc"];
          $subject = $server["res_traffic_notification_email_subject"];
          $message = $server["res_traffic_notification_email_message"];
          if($absender_email == '') $absender_email = $server['server_admin_email'];

          if($reseller["email"] != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $reseller["email"]) && !strstr($reseller["email"], " ") && !strstr($reseller["email"], "!") && !strstr($reseller["email"], "?") && !strstr($reseller["email"], "\"") && !strstr($reseller["email"], "(") && !strstr($reseller["email"], ")") && !strstr($reseller["email"], "[") && !strstr($reseller["email"], "]") && !strstr($reseller["email"], "{") && !strstr($reseller["email"], "}") && !strstr($reseller["email"], "/") && !strstr($reseller["email"], "#") && $absender_email != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $absender_email) && !strstr($absender_email, " ") && !strstr($absender_email, "!") && !strstr($absender_email, "?") && !strstr($absender_email, "\"") && !strstr($absender_email, "(") && !strstr($absender_email, ")") && !strstr($absender_email, "[") && !strstr($absender_email, "]") && !strstr($absender_email, "{") && !strstr($absender_email, "}") && !strstr($absender_email, "/") && !strstr($absender_email, "#")){

            if($subject == '') $subject = 'Traffic limit exceeded for your reseller account in '.$month;
            if($message == ''){
              $message = 'The traffic limit for your reseller account ('.$reseller['limit_traffic'].'MB) has been exceeded in '.$month.'.
However, your web sites will stay online, but you might have to pay for the extra traffic!';
            } else {
              $message = str_replace("%%%FIRST_NAME%%%", $reseller["vorname"], $message);
              $message = str_replace("%%%LAST_NAME%%%", $reseller["name"], $message);
              $message = str_replace("%%%MONTH%%%", $month, $message);
              $message = str_replace("%%%INCLUDED_TRAFFIC%%%", number_format($reseller['limit_traffic'], 2, '.', '').'MB', $message);
              $message = str_replace("%%%TOTAL_TRAFFIC%%%", number_format(($total_traffic/1048576), 2, '.', '').'MB', $message);
            }
            $headers  = "From: ".$absender_name." <".$absender_email.">\n";
            if($bcc != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $bcc) && !strstr($bcc, " ") && !strstr($bcc, "!") && !strstr($bcc, "?") && !strstr($bcc, "\"") && !strstr($bcc, "(") && !strstr($bcc, ")") && !strstr($bcc, "[") && !strstr($bcc, "]") && !strstr($bcc, "{") && !strstr($bcc, "}") && !strstr($bcc, "/") && !strstr($bcc, "#")) $headers .= "Bcc: ".$bcc."\n";
            $headers .= "Reply-To: <".$absender_email.">\n";
            $headers .= "X-Sender: <".$absender_email.">\n";
            $headers .= "X-Mailer: PHP4\n"; //mailer
            $headers .= "X-Priority: 3\n"; //1 UrgentMessage, 3 Normal
            $headers .= "Return-Path: <".$absender_email.">\n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-Type: text/plain\n";
            mail($reseller["email"], $subject, $message, $headers);
          }


        break;
        }
      }
    }
  }

}

?>