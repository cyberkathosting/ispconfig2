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

class isp_cron
{

var $path_httpd_conf;
var $path_httpd_root;
var $directory_mode = "0770";
var $web_doctype_id = 1013;
var $user_doctype_id = 1014;
var $domain_doctype_id = 1015;
var $cron_doctype_id = 1032;
var $vhost_conf;
var $sendmail_cw;
var $virtusertable;
var $user_von;
var $group_von;
var $server_conf;

//Constructor
function isp_cron() {
        global $go_api, $go_info;

        $server_conf = $go_api->db->queryOneRecord("SELECT * from isp_server");
        $this->path_httpd_conf = stripslashes($server_conf["server_path_httpd_conf"]);
        $this->path_httpd_root = stripslashes($server_conf["server_path_httpd_root"]);
        $this->vhost_conf = $this->path_httpd_conf . $go_info["server"]["dir_trenner"].'vhost'.$go_info["server"]["dir_trenner"].'vhost.conf';
        $this->user_von = $server_conf["userid_von"];
        $this->group_von = $server_conf["groupid_von"];
        $this->virtusertable = $server_conf["server_sendmail_virtuser_datei"];
        $this->sendmail_cw = $server_conf["server_sendmail_cw"];

        $this->server_conf = $server_conf;
}

function cron_show($doc_id, $doctype_id) {
        global $go_api, $go_info, $doc, $tablevalues, $next_tree_id;



}


function cron_insert($doc_id, $doctype_id, $die_on_error = '1') {
global $go_api, $go_info,$s;

    // Hole User des Cron Jobs
     $user = $go_api->db->queryOneRecord("SELECT * from isp_isp_user, isp_dep where
     isp_isp_user.doc_id = isp_dep.parent_doc_id and
     isp_isp_user.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");

     $user_doc_id = $user["doc_id"];
     $user_doctype_id = $user["doctype_id"];
     //unset($user);

     // sind für diesen User überhaupt Cron Jobs zugelassen?
     if(!$user["user_cron"]){
       $status = "DELETE";
       $errorMessage = $go_api->lng("error_cron_nicht_erlaubt");
     }

    $go_api->db->query("UPDATE isp_isp_cron SET user_id = '$user_doc_id' WHERE doc_id = '$doc_id'");

    // Eintrag des Cron Jobs holen
    $cron = $go_api->db->queryOneRecord("select * from isp_isp_cron where doc_id = '$doc_id'");

    if($status == "DELETE") {
        // Eintrag löschen
        $go_api->db->query("DELETE from isp_isp_cron where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        if($die_on_error){
          $go_api->errorMessage($errorMessage.$go_api->lng("weiter_link"));
        } else {
          return $errorMessage;
        }
    } else {
        // Status des Cron Jobs auf 'n' setzen
        $go_api->db->query("UPDATE isp_isp_cron SET status = 'n' where doc_id = '$doc_id'");
        //User und Groupid auf die Werte des Web setzen
        $user_doc_id = $user["parent_doc_id"];
        $user_doctype_id = $user["parent_doctype_id"];
        $usernode = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $user_doc_id and doctype_id = $user_doctype_id");
        $go_api->db->query("UPDATE isp_nodes SET groupid = ".$usernode["groupid"].", userid = ".$usernode["userid"].", title = 'Cron Job: ".$cron["cron_name"]."' where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        $go_api->db->query("UPDATE isp_isp_user SET status = 'u' where status != 'n' and status != 'd' and doc_id = '$user_doc_id'");

    }

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');

        //$this->faktura_insert($doc_id,$web["doc_id"],$user["user_username"]);
    //$go_api->errorMessage($antwort);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////

function cron_update($doc_id, $doctype_id, $die_on_error = '1') {
global $go_api, $go_info,$s,$HTTP_POST_VARS;

        $go_api->uses("isp");

        $cron = $go_api->db->queryOneRecord("select * from isp_isp_cron where doc_id = '$doc_id'");


    $user = $go_api->db->queryOneRecord("SELECT * from isp_isp_user, isp_dep where
     isp_isp_user.doc_id = isp_dep.parent_doc_id and
     isp_isp_user.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");

     $go_api->db->query("UPDATE isp_isp_cron SET status = 'u' where status != 'n' and doc_id = '$doc_id'");

    //User und Groupid auf die Werte des Web setzen
    $user_doc_id = $user["parent_doc_id"];
    $user_doctype_id = $user["parent_doctype_id"];
    $usernode = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $user_doc_id and doctype_id = $user_doctype_id");
    $go_api->db->query("UPDATE isp_nodes SET groupid = ".$usernode["groupid"].", userid = ".$usernode["userid"].", title = 'Cron Job: ".$cron["cron_name"]."' where doc_id = '$doc_id' and doctype_id = '$doctype_id'");

    $go_api->db->query("UPDATE isp_isp_user SET status = 'u' where status != 'n' and status != 'd' and doc_id = '$user_doc_id'");

        ////////////////////////////////////////////////////////
            // Server benachrichtigen
        ////////////////////////////////////////////////////////

        $server_id = 1;
        $go_api->isp->signal_server($server_id,'update');

        // Faktura Updaten
        //$this->faktura_update($doc_id,$web["doc_id"],$user["user_username"]);

        // ISPConfig Rechte in nodes Table checken
        $go_api->isp->check_perms($doc_id, $doctype_id);

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////

function cron_delete($doc_id, $doctype_id, $action, $die_on_error = '1') {

global $go_api, $go_info;

    $user = $go_api->db->queryOneRecord("SELECT * from isp_isp_user, isp_dep where
     isp_isp_user.doc_id = isp_dep.parent_doc_id and
     isp_isp_user.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");
    $user_doc_id = $user["parent_doc_id"];
    $user_doctype_id = $user["doctype_id"];

    if($action == "do") {
        $go_api->db->query("UPDATE isp_isp_cron SET status = 'd' where doc_id = '$doc_id'");
                //$this->faktura_delete($doc_id,'do');
    } else {

        if($user["user_cron"]){
          $go_api->db->query("UPDATE isp_isp_cron SET status = 'n' WHERE doc_id = '$doc_id'");
                //$this->faktura_delete($doc_id,'undo');
        } else {
          $go_api->db->query("UPDATE isp_nodes SET status = '0' WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
          if($die_on_error){
            $go_api->errorMessage($go_api->lng("error_cron_nicht_erlaubt").$go_api->lng("weiter_link"));
          } else {
            return $go_api->lng("error_cron_nicht_erlaubt");
          }
        }
    }

    $go_api->db->query("UPDATE isp_isp_user SET status = 'u' WHERE status != 'n' AND status != 'd' AND doc_id = '$user_doc_id'");

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'delete: '.$action);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Hilfsfunktionen Faktura
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

function faktura_insert($doc_id,$web_id,$beschreibung) {
        global $go_api;
        $sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
        if($sys_config["faktura_on"] == 1) {
                $sql = "INSERT INTO isp_fakt_record (web_id,doc_id,doctype_id,typ,notiz) VALUES ($web_id,$doc_id,1014,'Email','$beschreibung')";
                $go_api->db->query($sql);
        }
}

function faktura_update($doc_id,$web_id,$beschreibung) {
        global $go_api;
        $sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
        if($sys_config["faktura_on"] == 1) {
                $sql = "UPDATE isp_fakt_record SET notiz = '$beschreibung' where doc_id = $doc_id and doctype_id = 1014";
                $go_api->db->query($sql);
        }
}

function faktura_delete($doc_id,$action) {
        global $go_api;
        $sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
        if($sys_config["faktura_on"] == 1) {
                if($action == "do") {
                        $sql = "UPDATE isp_fakt_record SET status = 0 where doc_id = $doc_id and doctype_id = 1014";
                        $go_api->db->query($sql);
                } else {
                        $sql = "UPDATE isp_fakt_record SET status = 1 where doc_id = $doc_id and doctype_id = 1014";
                        $go_api->db->query($sql);
                }
        }
}

}
?>