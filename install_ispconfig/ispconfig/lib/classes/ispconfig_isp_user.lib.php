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

class isp_user
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
function isp_user() {
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

function user_show($doc_id, $doctype_id) {
        global $go_api, $go_info, $doc, $tablevalues, $next_tree_id;

        //
        if($this->server_conf["use_maildir"] == 1) $doc->deck[0]->elements[8]->visible = 0;

        // Hole Web
        $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_nodes where
     isp_isp_web.doc_id = isp_nodes.doc_id and
     isp_isp_web.doctype_id = isp_nodes.doctype_id and
     isp_nodes.tree_id = $next_tree_id");

         // Wenn Shellzugriff im Web deaktiviert ist
         if($web["web_shell"] != '1') $doc->deck[0]->elements[11]->visible = 0;

         // Spamfilter Settings deaktivieren
         if($this->server_conf["spamfilter_enable"] != 1) $doc->deck[2]->visible = 0;


         if($doc_id > 0) {
                 // hole user
                 $user = $go_api->db->queryOneRecord("select * from isp_isp_user where doc_id = '$doc_id'");
                 // Autoresponder ausblenden, wenn Emailweiterleitung angekreuzt
                 if($user["user_emailweiterleitung"] != '' and $user["user_emailweiterleitung_local_copy"] != 1) {
                         $doc->deck[1]->elements[9]->visible = 0;
                        $doc->deck[1]->elements[10]->visible = 0;
                        $doc->deck[1]->elements[11]->visible = 0;
                 }
                 $doc->deck[2]->elements[2]->values["accept"] = $go_api->lng("txt_accept");
                 $doc->deck[2]->elements[2]->values["discard"] = $go_api->lng("txt_discard");
        }

}


function user_insert($doc_id, $doctype_id, $die_on_error = '1') {
global $go_api, $go_info,$s;

    // Eintrag des User holen
    $user = $go_api->db->queryOneRecord("select * from isp_isp_user where doc_id = '$doc_id'");

    // Check ob bereits ein User mit diesem Namen existiert
    $usercount = $go_api->db->queryOneRecord("SELECT count(doc_id) as doc_count from isp_isp_user where user_username = '".$user["user_username"]."'");

        if($usercount["doc_count"] > 1) {
        $status = "DELETE";
        $errorMessage = $go_api->lng("error_sysuser_exist_1")." ".$user["user_username"]." ".$go_api->lng("error_sysuser_exist_2");
    }

        //calculate 2/8 random chars as salt for the crypt // by bjmg
        if($go_info["server"]["password_hash"] == 'crypt') {
            $salt="";
            for ($n=0;$n<2;$n++) {
            $salt.=chr(mt_rand(64,126));
        }
        } else {
            $salt="$1$";
            for ($n=0;$n<8;$n++) {
                $salt.=chr(mt_rand(64,126));
            }
            $salt.="$";
        }
        $passwort = "||||:".crypt($user["user_passwort"], $salt);


    $go_api->db->query("UPDATE isp_isp_user SET user_passwort = '$passwort' where doc_id = '$doc_id'");

     // Check Ob maximale Anzah User des Web erreicht ist
     // Hole das Web des Users
     $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_dep where
     isp_isp_web.doc_id = isp_dep.parent_doc_id and
     isp_isp_web.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");

     $userlimit = $web["web_userlimit"];
     $quotalimit = $web["web_speicher"];
     $web_doc_id = $web["doc_id"];
     $web_doctype_id = $web["doctype_id"];
     //unset($web);

     // Hole Useranzahl
     $usercount = $go_api->db->queryOneRecord("SELECT count(doc_id) as doc_count from isp_isp_user, isp_dep where
     isp_isp_user.doc_id = isp_dep.child_doc_id and isp_isp_user.doctype_id = isp_dep.child_doctype_id and
     isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and isp_dep.child_doctype_id = $doctype_id");

     $useranzahl = $usercount["doc_count"];

    // wenn Userlimits gesetzt sind
     if($userlimit >= 0) {
        if($useranzahl > $userlimit) {
        $status = "DELETE";
        $errorMessage .= $go_api->lng("error_max_user_web");
        }
     }

     // wenn Quotalimits gesetzt sind
     if($quotalimit >= 0) {
        if($user["user_speicher"] > $quotalimit) {
        $status = "DELETE";
        $errorMessage .= $go_api->lng("error_max_space_web");
        }
     }

     /*
     // Check Ob maximale Anzahl User des Resellers erreicht ist
     $user_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
     $resellerid = $user_node["groupid"];

     if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
        // Wenn Resellerlimit erreicht wurde
        if($reseller["limit_user"] >= 0) {
            if($useranzahl > $reseller["limit_user"]) {
            $status = "DELETE";
            $errorMessage .= $go_api->lng("error_max_user_anbieter");
            }
        }
     }
     */

     // Check Ob maximale Anzahl User des Resellers erreicht ist
     $user_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
     $resellerid = $user_node["groupid"];

     if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
        // Wenn Resellerlimit erreicht wurde
        if($reseller["limit_user"] >= 0) {
            $reseller_useranzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as reseller_useranzahl from isp_nodes where groupid = '$resellerid' and doctype_id = '".$this->user_doctype_id."'");
            $reseller_useranzahl = $reseller_useranzahl["reseller_useranzahl"];
            if($reseller_useranzahl >= $reseller["limit_user"]) {
              $status = "DELETE";
              $errorMessage .= $go_api->lng("error_max_user_anbieter");
            }
        }
     }

     // check ob username erlaubt ist anhand der baduser datei
     $baduser_datei = SERVER_ROOT . DIR_TRENNER."users";
     if($fd = @fopen ($baduser_datei, "rb")) {
        $baduser = @fread ($fd, filesize ($baduser_datei));
        fclose ($fd);

        if(stristr($baduser,$user["user_username"])) {
            $status = "DELETE";
            $errorMessage .= $go_api->lng("error_sysuser_exist");
        }
     }

         // stelle sicher dass es keinen User mit dem gleichen "email" Feld gibt
         $sql = "SELECT count(*) AS anzahl FROM isp_isp_user, isp_dep where
        isp_isp_user.doc_id = isp_dep.child_doc_id and isp_isp_user.doctype_id = isp_dep.child_doctype_id and
        isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and
                isp_dep.child_doctype_id = $doctype_id and isp_isp_user.user_email = '".$user["user_email"]."'";
         $tmp = $go_api->db->queryOneRecord($sql);

        if($tmp["anzahl"] > 1) {
                $status = "DELETE";
        $errorMessage .= $go_api->lng("error_email_exist");
        }


     /*
     // Überprüfung, ob der Email alias nur einmal vorkommt
        $alias = array();
        // Hole alle useraccounts des Web
        $full_user = $go_api->db->queryOneRecord("SELECT * from isp_isp_user, isp_dep where
        isp_isp_user.doc_id = isp_dep.child_doc_id and isp_isp_user.doctype_id = isp_dep.child_doctype_id and
        isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and isp_dep.child_doctype_id = $doctype_id");

        //packe alle user in ein array
        foreach($full_user as $fuser) {
            //nehme nur die anderen User
            if($fuser["doc_id"] != $doc_id) {
                $alias[] = $fuser["user_username"];
                $full_user_alias = explode("\n",$fuser["user_emailalias"]);
                foreach($full_user_alias as $fuser_alias) {
                    $alias[] = $fuser_alias;
                }
            }
        }
        die(print_r($alias));
     */


     // Stelle sicher, dass es nur einen Admin pro Web gibt
     $admincount = $go_api->db->queryOneRecord("SELECT count(doc_id) as admin_count from isp_isp_user, isp_dep where
     isp_isp_user.doc_id = isp_dep.child_doc_id and isp_isp_user.doctype_id = isp_dep.child_doctype_id and
     isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and isp_dep.child_doctype_id = $doctype_id and user_admin = '1'");

     if($admincount["admin_count"] > 1) {
            $status = "DELETE";
            $errorMessage .= $go_api->lng("error_admin_exist");
     }


    if($status == "DELETE") {
        // Eintrag löschen
        $go_api->db->query("DELETE from isp_isp_user where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        if($die_on_error){
          $go_api->errorMessage($errorMessage.$go_api->lng("weiter_link"));
        } else {
          return $errorMessage;
        }
    } else {
        // Status des User auf 'n' setzen
        $go_api->db->query("UPDATE isp_isp_user SET status = 'n' where doc_id = '$doc_id'");
        //User und Groupid auf die Werte des Web setzen
        $web_doc_id = $web["parent_doc_id"];
        $web_doctype_id = $web["parent_doctype_id"];
        $webnode = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
        $go_api->db->query("UPDATE isp_nodes SET groupid = ".$webnode["groupid"].", userid = ".$webnode["userid"]." where doc_id = '$doc_id' and doctype_id = '$doctype_id'");

    }

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');

        $this->faktura_insert($doc_id,$web["doc_id"],$user["user_username"]);
    //$go_api->errorMessage($antwort);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////

function user_update($doc_id, $doctype_id, $die_on_error = '1') {
global $go_api, $go_info,$s,$HTTP_POST_VARS,$old_form_data;

        $go_api->uses("isp");

    $user = $go_api->db->queryOneRecord("select * from isp_isp_user where doc_id = '$doc_id'");
    if($old_form_data == $user) return true;
    if(substr($user["user_passwort"],0,5) != "||||:" and $user["user_passwort"] != "") {
        //calculate 2/8 random chars as salt for the crypt // by bjmg
        if($go_info["server"]["password_hash"] == 'crypt') {
            $salt="";
            for ($n=0;$n<2;$n++) {
                $salt.=chr(mt_rand(64,126));
            }
        } else {
            $salt="$1$";
            for ($n=0;$n<8;$n++) {
                $salt.=chr(mt_rand(64,126));
            }
            $salt.="$";
        }

        $passwort = "||||:".crypt($user["user_passwort"], $salt);

        $go_api->db->query("UPDATE isp_isp_user SET user_passwort = '$passwort' where doc_id = '$doc_id'");
    }

    $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_dep where
     isp_isp_web.doc_id = isp_dep.parent_doc_id and
     isp_isp_web.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");

     $go_api->db->query("UPDATE isp_isp_user SET status = 'u' where status != 'n' and doc_id = '$doc_id'");


    //User und Groupid auf die Werte des Web setzen
    $web_doc_id = $web["parent_doc_id"];
    $web_doctype_id = $web["parent_doctype_id"];
    $webnode = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
    $go_api->db->query("UPDATE isp_nodes SET groupid = ".$webnode["groupid"].", userid = ".$webnode["userid"]." where doc_id = '$doc_id' and doctype_id = '$doctype_id'");

    $go_api->db->query("UPDATE isp_isp_web SET status = 'u' where status != 'n' and status != 'd' and doc_id = '$web_doc_id'");

    // check ob username erlaubt ist anhand der baduser datei
     $baduser_datei = SERVER_ROOT . DIR_TRENNER."users";
     if($fd = @fopen ($baduser_datei, "rb")) {
        $baduser = @fread ($fd, filesize ($baduser_datei));
        fclose ($fd);

        if(stristr($baduser,$user["user_username"])) {
            $status = "DELETE";
            $errorMessage .= $go_api->lng("error_sysuser_exist");
        }
     } else {
                 $go_api->log("Cannot Open File: $baduser_datei",2);
         }

     // Stelle sicher, dass es nur einen Admin pro Web gibt
     $admincount = $go_api->db->queryOneRecord("SELECT count(doc_id) as admin_count from isp_isp_user, isp_dep where
     isp_isp_user.doc_id = isp_dep.child_doc_id and isp_isp_user.doctype_id = isp_dep.child_doctype_id and
     isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and isp_dep.child_doctype_id = $doctype_id and user_admin = '1'");

     if($admincount["admin_count"] > 1) {
            $go_api->db->query("UPDATE isp_isp_user SET user_admin = '0' where doc_id = $doc_id");
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_admin_exist_long").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_admin_exist_long");
            }
     }

         // stelle sicher dass es keinen User mit dem gleichen "email" Feld gibt
         $sql = "SELECT count(*) AS anzahl FROM isp_isp_user, isp_dep where
        isp_isp_user.doc_id = isp_dep.child_doc_id and isp_isp_user.doctype_id = isp_dep.child_doctype_id and
        isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and
                isp_dep.child_doctype_id = $doctype_id and isp_isp_user.user_email = '".$user["user_email"]."'";
         $tmp = $go_api->db->queryOneRecord($sql);

        if($tmp["anzahl"] > 1) {
                $go_api->db->query("UPDATE isp_isp_user SET user_email = '".$user['user_email'].$tmp["anzahl"]."' where doc_id = '$doc_id'");
                if($die_on_error){
                  $go_api->errorMessage($go_api->lng("error_email_exist").$go_api->lng("weiter_link"));
                } else {
                  return $go_api->lng("error_email_exist");
                }
        }

     // wenn Quotalimits gesetzt sind
     $quotalimit = $web["web_speicher"];
     if($quotalimit > 0) {
        if($user["user_speicher"] > $quotalimit) {
          $go_api->db->query("UPDATE isp_isp_user SET user_speicher = '".$web["web_speicher"]."' where doc_id = $doc_id");
          if($die_on_error){
            $go_api->errorMessage($go_api->lng("error_max_space_web").$go_api->lng("weiter_link"));
          } else {
            return $go_api->lng("error_max_space_web");
          }
        }
     }

     // überprüfe email alias auf richtige Angaben
     $emailalias = explode ("\r\n",$user["user_emailalias"]);
     $emailalias_new = '';
     foreach($emailalias as $em_ad) {
        $em_tmp = explode("@",$em_ad);
        $tmpals = strtolower($em_tmp[0]);
        if(!empty($tmpals)) $emailalias_new[] = $tmpals;
     }
     //$go_api->db->query("UPDATE isp_isp_user SET user_emailalias = '$emailalias_new' where doc_id = $doc_id");

        // checke Spam-Hits auf richtige Formatierung
        $spam_hits = str_replace(",",".",$user["spam_hits"]);
        $spam_hits = sprintf("%01.1f", $spam_hits);
        $go_api->db->query("UPDATE isp_isp_user SET spam_hits = '$spam_hits' where doc_id = $doc_id");


    ///////////////////////////////////////////////////////
    // Checke Email alias auf duplikate
    ///////////////////////////////////////////////////////

        // Hole Emailadresse
        $user_email = strtolower($user["user_email"]);

        $alias_dupl = array();
        $alias_new = array();
        $alias_dupl[0] = strtolower($user["user_username"]); // füge username hinzu
        $alias_dupl[1] = $user_email; // füge Emailadressenfeld hinzu

                if(is_array($emailalias_new) and is_array($alias_dupl)) {
                $alias_dupl = array_merge($alias_dupl,$emailalias_new);
                }

                foreach($alias_dupl as $adp) {
            if(!in_array($adp,$alias_new) && $adp != strtolower($user["user_username"]) && $adp != strtolower($user["user_email"])) $alias_new[] = $adp;
        }
        $alias_dupl = $alias_new;
        //unset($alias_dupl[0]); // lösche username aus alias liste
        //unset($alias_dupl[1]); // lösche Emailadressfeld aus alias liste
        $emailalias_new = $alias_dupl;
        unset($alias_dupl);
        unset($alias_new);

        // Überprüfung, ob der Email alias nur einmal pro web vorkommt
        $alias = array();
        // Hole alle useraccounts des Web
        $full_user = $go_api->db->queryAllRecords("SELECT * from isp_isp_user, isp_dep where
        isp_isp_user.doc_id = isp_dep.child_doc_id and isp_isp_user.doctype_id = isp_dep.child_doctype_id and
        isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and isp_dep.child_doctype_id = $doctype_id");

        //packe alle anderen user des Web in ein array
                if(is_array($full_user)) {
        foreach($full_user as $fuser) {
            //nehme nur die anderen User
            if($fuser["doc_id"] != $doc_id) {
                $alias[] = strtolower($fuser["user_username"]);
                $alias[] = strtolower($fuser["user_email"]);
                $full_user_alias = explode("\r\n",$fuser["user_emailalias"]);
                foreach($full_user_alias as $fuser_alias) {
                    if(!empty($fuser_alias)) $alias[] = strtolower($fuser_alias);
                }
            }
        }
                }

        // Packe alle alias dieses Users in ein array
        $myalias = array();
        $out_alias = '';

        // vergleiche Alias
        foreach($emailalias_new as $mal) {
            if(!in_array($mal,$alias)) $out_alias .= $mal ."\r\n";
                        // Wenn email Adresse oder alias bereits bei anderem user existiert
                        if(in_array($user_email,$alias)) $user_email = '';
        }

        unset($myalias);
        unset($user_alias);
        unset($full_user);
        unset($alias);

        $go_api->db->query("UPDATE isp_isp_user SET user_emailalias = '$out_alias', user_email = '$user_email' where doc_id = $doc_id");

    // checke, dass nur ein Email Catch all pro Domain vorkommt
    $catchallcount = $go_api->db->queryOneRecord("SELECT count(doc_id) as catchall_count from isp_isp_user, isp_dep where
     isp_isp_user.doc_id = isp_dep.child_doc_id and isp_isp_user.doctype_id = isp_dep.child_doctype_id and
     isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and isp_dep.child_doctype_id = $doctype_id and user_catchallemail = '1'");

     if($catchallcount["catchall_count"] > 1) {
            $go_api->db->query("UPDATE isp_isp_user SET user_catchallemail = '0' where doc_id = $doc_id");
     }

        ///////////////////////////////////////////////////////
            // Leerzeilen, Duplikate, "nicht Emailadressen" aus
        // Email-Weiterleitung entfernen
            ///////////////////////////////////////////////////////

        $emailweiterleitung = explode ("\r\n",$user["user_emailweiterleitung"]);
        if(is_array($emailweiterleitung)) {
                $emailweiterleitung = array_unique($emailweiterleitung);
                foreach($emailweiterleitung as $val) {
                        if(!empty($val) and stristr($val,"@")) $tmp_new[] = $val;
                }
                $emailweiterleitung = @implode($tmp_new,"\r\n");
                $go_api->db->query("UPDATE isp_isp_user SET user_emailweiterleitung = '$emailweiterleitung' where doc_id = $doc_id");
        }

        ////////////////////////////////////////////////////////
            // Server benachrichtigen
        ////////////////////////////////////////////////////////

        $server_id = 1;
        $go_api->isp->signal_server($server_id,'update');

        // Faktura Updaten
        $this->faktura_update($doc_id,$web["doc_id"],$user["user_username"]);

        // ISPConfig Rechte in nodes Table checken
        $go_api->isp->check_perms($doc_id, $doctype_id);

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////

function user_delete($doc_id, $doctype_id, $action, $die_on_error = '1') {

global $go_api, $go_info;

###########################
    $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_dep where
     isp_isp_web.doc_id = isp_dep.parent_doc_id and
     isp_isp_web.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");
    $web_doc_id = $web["parent_doc_id"];
    $web_doctype_id = $web["doctype_id"];

    if($action == "do") {
        $go_api->db->query("UPDATE isp_isp_user SET status = 'd' where doc_id = '$doc_id'");
                $this->faktura_delete($doc_id,'do');

        // User in del_status eintragen
            $user_del_status = $go_api->db->queryOneRecord("SELECT * FROM del_status WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
            if(!$user_del_status){
              $user_info = $go_api->db->queryOneRecord("SELECT user_username FROM isp_isp_user WHERE doc_id = '".$doc_id."'");
              $go_api->db->query("INSERT INTO del_status (doc_id, doctype_id, name, pfad) VALUES ('".$doc_id."', '".$doctype_id."', '".$user_info["user_username"]."', '".realpath($this->server_conf["server_path_httpd_root"])."/web".$web_doc_id."/user/".$user_info["user_username"]."')");
              unset($user_info);
            }

    } else {
        // Prüfen, ob User überhaupt wiederhergestellt werden darf, ob für das Web genug User erlaubt sind
        // Hole Useranzahl
        if($web["web_userlimit"] >= 0){
     $usercount = $go_api->db->queryOneRecord("SELECT count(isp_isp_user.doc_id) as doc_count from isp_dep, isp_isp_user where isp_dep.parent_doc_id = '$web_doc_id' and isp_dep.parent_doctype_id = '$web_doctype_id' and isp_dep.child_doc_id = isp_isp_user.doc_id and isp_dep.child_doctype_id = '".$this->user_doctype_id."'");

     $useranzahl = $usercount["doc_count"];
     } else {
       $useranzahl = -1;
     }


     // Check Ob maximale Anzahl User des Resellers erreicht ist
     $user_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
     $resellerid = $user_node["groupid"];

     if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
        // Wenn Resellerlimit erreicht wurde
        if($reseller["limit_user"] >= 0) {
            $reseller_useranzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as reseller_useranzahl from isp_nodes where groupid = '$resellerid' and doctype_id = '".$this->user_doctype_id."'");
            $reseller_useranzahl = $reseller_useranzahl["reseller_useranzahl"];
        } else {
          $reseller_useranzahl = -1;
          $reseller["limit_user"] = -1;
        }
     } else {
       $reseller_useranzahl = -1;
       $reseller["limit_user"] = -1;
     }


        if($useranzahl <= $web["web_userlimit"] && $reseller_useranzahl <= $reseller["limit_user"]){
          $go_api->db->query("UPDATE isp_isp_user SET status = 'n' WHERE doc_id = '$doc_id'");
                $this->faktura_delete($doc_id,'undo');
        } else {
          $go_api->db->query("UPDATE isp_nodes SET status = '0' WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
          if($useranzahl > $web["web_userlimit"]){
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_max_user_web").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_max_user_web");
            }
          }
          if($reseller_useranzahl > $reseller["limit_user"]){
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_max_user_anbieter").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_max_user_anbieter");
            }
          }
        }
    }
#############################

/*
    if($action == "do") {
        $go_api->db->query("UPDATE isp_isp_user SET status = 'd' where doc_id = '$doc_id'");
                $this->faktura_delete($doc_id,'do');
    } else {
        $go_api->db->query("UPDATE isp_isp_user SET status = 'n' where doc_id = '$doc_id'");
                $this->faktura_delete($doc_id,'undo');
    }

    $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_dep where
     isp_isp_web.doc_id = isp_dep.parent_doc_id and
     isp_isp_web.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");

    $web_doc_id = $web["parent_doc_id"];
*/

    $go_api->db->query("UPDATE isp_isp_web SET status = 'u' where status != 'n' and status != 'd' and doc_id = '$web_doc_id'");

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