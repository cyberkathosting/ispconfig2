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

if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class isp_kunde
{

var $path_httpd_conf;
var $path_httpd_root;
var $directory_mode = "0770";
var $web_doctype_id = 1013;
var $user_doctype_id = 1014;
var $domain_doctype_id = 1015;
var $vhost_conf;
var $sendmail_cw;
var $virtusertable;
var $user_von;
var $group_von;
var $server_conf;


//Constructor
function isp_kunde() {
global $go_api, $go_info;

$server_conf = $go_api->db->queryOneRecord("SELECT * from isp_server");
$this->server_conf = $server_conf;
$this->path_httpd_conf = stripslashes($server_conf["server_path_httpd_conf"]);
$this->path_httpd_root = stripslashes($server_conf["server_path_httpd_root"]);
$this->vhost_conf = $this->path_httpd_conf . $go_info["server"]["dir_trenner"].'vhost'.$go_info["server"]["dir_trenner"].'vhost.conf';
$this->user_von = $server_conf["userid_von"];
$this->group_von = $server_conf["groupid_von"];
$this->virtusertable = $server_conf["server_sendmail_virtuser_datei"];
$this->sendmail_cw = $server_conf["server_sendmail_cw"];
}


function kunde_insert($doc_id, $doctype_id, $die_on_error = '1') {
global $go_api, $go_info;

        $username = $go_api->lng('Kunde').$doc_id;
                //$sys_user_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as documents from sys_user where username = '$username'");
        //if($sys_user_count["documents"] == 0) $go_api->db->query("UPDATE isp_isp_kunde SET webadmin_user = '$username' where doc_id = '$doc_id'");


        $sys_user_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as documents from sys_user where username = '$username'");

        if($sys_user_count["documents"] >= 1) {
            // Es existiert bereits ein ISPConfig User dieses Namens, Löschen der Reseller_userfelder und Fehlermeldung
            $sql = "UPDATE isp_isp_kunde SET webadmin_passwort = '', webadmin_user = '', webadmin_userid = 0 where doc_id = '$doc_id'";
            $go_api->db->query($sql);
            if($die_on_error){
            $go_api->errorMessage($go_api->lng("error_user_exist_1")." ".$username." ".$go_api->lng("error_user_exist_2").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_user_exist_1")." ".$username." ".$go_api->lng("error_user_exist_2");
            }

        } else {
            /////////////////////////////////////////
            // Wenn User noch nicht existiert,
            // User in sys_user hinzufügen
            /////////////////////////////////////////
            if($go_api->auth->check_write(0)) {
                        $datum = time();
                        $userid = $go_info["user"]["userid"];
            //$username = $kunde["webadmin_user"];
            $passwort = '';

                        $sql = "INSERT INTO sys_user (userid, username, passwort,gueltig,perms,modules,modul) VALUES ('$userid', '$username', '$passwort','1','rw','isp_kunde,isp_file,tools,help','isp_kunde')";
                        $go_api->db->query($sql);
                        $userid = $go_api->db->insertID();

            $sql = "INSERT INTO sys_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('1','0','','a','1','1','$userid','')";
                        $go_api->db->query($sql);
            //$gid = $reseller["reseller_group"];
            $go_api->db->query("UPDATE isp_isp_kunde SET webadmin_userid = '$userid', webadmin_user = '$username' where doc_id = '$doc_id'");

            }
        }

}

function kunde_update($doc_id, $doctype_id, $die_on_error = '1') {
    global $go_api, $go_info;

    // Kundendaten auslesen
    $kunde = $go_api->db->queryOneRecord("select * from isp_nodes,isp_isp_kunde where isp_nodes.doc_id = isp_isp_kunde.doc_id and isp_nodes.doc_id = '$doc_id' and isp_nodes.doctype_id = '$doctype_id'");
//print_r($kunde);

    // Passwort verschlüsseln
    if(substr($kunde["webadmin_passwort"],0,5) != "||||:" and $kunde["webadmin_passwort"] != "") {
        $go_api->db->query("UPDATE isp_isp_kunde SET webadmin_passwort = CONCAT('||||:' , MD5(webadmin_passwort)) WHERE doc_id = '$doc_id'");


        ///////////////// Begrüßungsemail schicken ////////////////
        // Reseller zum Kunden finden
        $kunde_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        $reseller_group = $kunde_node["groupid"];
        if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $reseller_group")){
          $absender_email = $reseller["client_salutatory_email_sender_email"];
          $absender_name = $reseller["client_salutatory_email_sender_name"];
          $bcc = $reseller["client_salutatory_email_bcc"];
          $subject = $reseller["client_salutatory_email_subject"];
          $message = $reseller["client_salutatory_email_message"];
        } else {
          $absender_email = $this->server_conf["client_salutatory_email_sender_email"];
          $absender_name = $this->server_conf["client_salutatory_email_sender_name"];
          $bcc = $this->server_conf["client_salutatory_email_bcc"];
          $subject = $this->server_conf["client_salutatory_email_subject"];
          $message = $this->server_conf["client_salutatory_email_message"];
        }
        $manual_lng = $go_api->db->queryOneRecord("SELECT language from sys_user where username = '".trim($kunde["webadmin_user"])."'");
        if(!$manual_lng || empty($manual_lng["language"])){
          $manual_lng = $go_info["server"]["lang"];
        } else {
          $manual_lng = $manual_lng["language"];
        }

        if($kunde["kunde_email"] != "" && eregi("^[a-z0-9,!#\$%&'\*\+/=\?\^_`\{\|}~-]+(\.[a-z0-9,!#\$%&'\*\+/=\?\^_`\{\|}~-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,})$", $kunde["kunde_email"]) && $absender_email != "" && eregi("^[a-z0-9,!#\$%&'\*\+/=\?\^_`\{\|}~-]+(\.[a-z0-9,!#\$%&'\*\+/=\?\^_`\{\|}~-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,})$", $absender_email) && $absender_name != "" && $subject != "" && $message != ""){

          $message = str_replace("%%%USER%%%", $kunde["webadmin_user"], $message);
          $message = str_replace("%%%PASSWORD%%%", $kunde["webadmin_passwort"], $message);
          $message = str_replace("%%%FIRST_NAME%%%", $kunde["kunde_vorname"], $message);
          $message = str_replace("%%%LAST_NAME%%%", $kunde["kunde_name"], $message);
          $message = str_replace("%%%COMPANY%%%", $kunde["kunde_firma"], $message);
          $message = str_replace("%%%EMAIL%%%", $kunde["kunde_email"], $message);
          $message = str_replace("%%%CP%%%", $go_info["server"]["server_url"], $message);
          $message = str_replace("%%%MANUAL%%%", $go_info["server"]["server_url"]."/help/documents/".$manual_lng."/client.pdf", $message);

          if(trim($go_info["server"]["salutatory_email_charset"]) == '' || !isset($go_info["server"]["salutatory_email_charset"])){
            $salutatory_email_charset = 'unknown-8bit';
          } else {
            $salutatory_email_charset = trim($go_info["server"]["salutatory_email_charset"]);
          }

          $headers  = "From: ".$absender_name." <".$absender_email.">\n";
          if($bcc != "" && eregi("^[a-z0-9,!#\$%&'\*\+/=\?\^_`\{\|}~-]+(\.[a-z0-9,!#\$%&'\*\+/=\?\^_`\{\|}~-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,})$", $bcc)) $headers .= "Bcc: ".$bcc."\n";
          $headers .= "Reply-To: <".$absender_email.">\n";
          $headers .= "X-Sender: <".$absender_email.">\n";
          $headers .= "X-Mailer: PHP4\n"; //mailer
          $headers .= "X-Priority: 3\n"; //1 UrgentMessage, 3 Normal
          $headers .= "Return-Path: <".$absender_email.">\n";
          $headers .= "MIME-Version: 1.0\n";
          $headers .= "Content-Type: text/plain; charset=".$salutatory_email_charset."\n";
          mail($kunde["kunde_email"], $subject, $message, $headers);
        }
        ///////////////// Begrüßungsemail schicken ENDE ////////////////

        $kunde = $go_api->db->queryOneRecord("select * from isp_nodes,isp_isp_kunde where isp_nodes.doc_id = isp_isp_kunde.doc_id and isp_nodes.doc_id = '$doc_id' and isp_nodes.doctype_id = '$doctype_id'");
    }


    // User für Kunde hinzufügen, wenn noch nicht angelegt
    if($kunde["webadmin_userid"] == 0 and $kunde["webadmin_user"] != ""){
        // Check Ob User noch nicht in sys_user existiert
        $sys_user_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as documents from sys_user where username = '".trim($kunde["webadmin_user"])."'");


        if($sys_user_count["documents"] >= 1) {
            // Es existiert bereits ein ISPConfig User dieses Namens, Löschen der Reseller_userfelder und Fehlermeldung
            $sql = "UPDATE isp_isp_kunde SET webadmin_passwort = '', webadmin_user = '', webadmin_userid = 0 where doc_id = '$doc_id'";
            $go_api->db->query($sql);
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_user_exist_1")." ".$go_api->lng("error_user_exist_2").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_user_exist_1")." ".$go_api->lng("error_user_exist_2");
            }

        } else {

            // User hinzufügen

            /////////////////////////////////////////
            // Wenn User noch nicht existiert,
            // User in sys_user hinzufügen
            /////////////////////////////////////////
            if($go_api->auth->check_write(0)) {
                        $datum = time();
                        $userid = $go_info["user"]["userid"];
            $username = $kunde["webadmin_user"];
            $passwort = substr($kunde["webadmin_passwort"],5);

                        $sql = "INSERT INTO sys_user (userid, username, passwort,gueltig,perms,modules,modul,site) VALUES ('$userid', '$username', '$passwort','1','r','isp_kunde,tools','isp_kunde','ISPConfig')";

                        $go_api->db->query($sql);
                        $userid = $go_api->db->insertID();

            $sql = "INSERT INTO sys_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('1','0','','a','1','1','$userid','')";
                        $go_api->db->query($sql);
            //$gid = $reseller["reseller_group"];
            $go_api->db->query("UPDATE isp_isp_kunde SET webadmin_userid = '$userid' where doc_id = '$doc_id'");

            }
        }
    }

    if($kunde["webadmin_userid"] > 0 and $go_info["server"]["mode"] != "demo") {
    // Reseller ispconfig_user updaten
    // sys Userdaten holen
    $sys_user = $go_api->db->queryOneRecord("SELECT * from sys_user where doc_id = '".$kunde["webadmin_userid"]."'");

        // Wenn sich Passwort ändert
        if($kunde["webadmin_passwort"] != "") {
            $passwort = substr($kunde["webadmin_passwort"],5);
            $sql = "UPDATE sys_user SET passwort = '$passwort' where doc_id = ".$kunde["webadmin_userid"];
            //die($sql);
            $go_api->db->query($sql);
        }

        if($kunde["webadmin_user"] != "") {
            // Check Ob User noch nicht in sys_user existiert
            $sys_user_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as documents from sys_user where username = '".trim($kunde["webadmin_user"])."' and doc_id != ".$kunde["webadmin_userid"]);
            if($sys_user_count["documents"] == 0) {
                $go_api->db->query("update sys_user set username = '".$kunde["webadmin_user"]."' where doc_id = ".$kunde["webadmin_userid"]);
            } else {
                $go_api->db->query("update isp_isp_kunde set webadmin_user = '".$sys_user["username"]."' where doc_id = $doc_id");
                if($die_on_error){
                  $go_api->errorMessage($go_api->lng("error_user_exist_1")." ".$go_api->lng("error_user_exist_2").$go_api->lng("weiter_link"));
                } else {
                  return $go_api->lng("error_user_exist_1")." ".$go_api->lng("error_user_exist_2");
                }

            }
        }


                /*
        if($sys_user["username"] != $kunde["webadmin_user"]) {
            // Check Ob User noch nicht in sys_user existiert
            //$sys_user_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as documents from sys_user where username = '".trim($kunde["webadmin_user"])."'");
            if($sys_user_count["documents"] == 0) {
                //if(trim($reseller["reseller_passwort"]) == "") $go_api->errorMessage("Es kann kein User mit leerem Passwort angelegt werden.");
                $passwort = substr($kunde["webadmin_passwort"],5);
                $go_api->db->query("UPDATE sys_user SET username = '".trim($kunde["webadmin_user"])."', passwort = '$passwort' where doc_id = ".$kunde["webadmin_userid"]);
            } else {
                $go_api->db->query("update isp_isp_kunde set webadmin_user = '".$sys_user["username"]."' where doc_id = $doc_id");
                $go_api->errorMessage($go_api->lng("error_user_exist_1")." ".$go_api->lng("error_user_exist_2").$go_api->lng("weiter_link"));

            }
        }
                */

    }

}

function kunde_delete($doc_id, $doctype_id, $action, $die_on_error = '1') {
    global $go_api, $go_info;

    // beim Löschen
    $kunde = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_kunde where doc_id = $doc_id");
    $sys_user_id = $kunde["webadmin_userid"];
    $userid = $go_info["user"]["userid"];
    $username = $kunde["webadmin_user"];

    if($action == 'do') {
      $go_api->db->query("DELETE FROM sys_user where doc_id = $sys_user_id");
      $go_api->db->query("DELETE FROM sys_nodes where doctype_id = 1 and doc_id = $sys_user_id");
    } else {
      $sql = "INSERT INTO sys_user (doc_id, userid, username, passwort,gueltig,perms,modules,modul) VALUES ('".$sys_user_id."','$userid', '$username', '','1','rw','isp_kunde,isp_file,tools,help','isp_kunde')";
      $go_api->db->query($sql);
      $sql = "INSERT INTO sys_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('1','0','','a','1','1','$sys_user_id','')";
      $go_api->db->query($sql);
      $this->kunde_update($doc_id, $doctype_id, $die_on_error);
    }


}

}
?>