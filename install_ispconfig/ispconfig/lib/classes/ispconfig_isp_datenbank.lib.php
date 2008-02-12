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

class isp_datenbank
{

var $path_httpd_conf;
var $path_httpd_root;
var $directory_mode = "0770";
var $web_doctype_id = 1013;
var $user_doctype_id = 1014;
var $domain_doctype_id = 1015;
var $datenbank_doctype_id = 1029;
var $vhost_conf;
var $sendmail_cw;
var $virtusertable;
var $user_von;
var $group_von;
var $server_conf;

//Constructor
function isp_datenbank() {
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

function datenbank_show($doc_id, $doctype_id) {
        global $go_api, $go_info, $doc, $tablevalues, $next_tree_id;

  // Hole Web
        $web = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_web, isp_nodes WHERE
     isp_isp_web.doc_id = isp_nodes.doc_id AND
     isp_isp_web.doctype_id = isp_nodes.doctype_id AND
     isp_nodes.tree_id = $next_tree_id");

     if($doc_id > 0) {
                 // hole DB
                 $datenbank = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_datenbank WHERE doc_id = '$doc_id'");

                 $doc->deck[0]->elements[0]->value = '<table width="100%"><tr><td class="normal" align="left" width="31%"><b>'.$go_api->lng("Datenbankname").':</b></b></td><td class="t2" align="left" width="69%">'.$datenbank["datenbankname"].'</td></tr></table>';
          $doc->deck[0]->elements[1]->value = '<table width="100%"><tr><td class="normal" align="left" width="31%"><b>'.$go_api->lng("Datenbankuser").':</b></td><td class="t2" align="left" width="69%">'.$datenbank["datenbankuser"].'</td></tr></table>';

        } else {
          $db_ids = $go_api->db->queryAllRecords("SELECT datenbankname FROM isp_isp_datenbank WHERE web_id = '".$web["doc_id"]."'");
          if(empty($db_ids)){
            $new_db_id = 1;
          } else {
            // kleinste freie ID finden
            foreach($db_ids as $db_id){
              if(strpos($db_id["datenbankname"], '_')){
                $db_nr[] = str_replace('web'.$web["doc_id"].'_db', '', $db_id["datenbankname"]);
              } else {
                $db_nr[] = str_replace('web'.$web["doc_id"].'db', '', $db_id["datenbankname"]);
              }
            }
            sort($db_nr, SORT_NUMERIC);
            reset($db_nr);
            $i = 0;
            while(!isset($new_db_id)){
              if($db_nr[$i] != ($i + 1)) $new_db_id = $i + 1;
              $i++;
            }
          }
          $doc->deck[0]->elements[0]->value = '<table width="100%"><tr><td class="normal" align="left" width="31%"><b>'.$go_api->lng("Datenbankname").':</b></td><td class="t2" align="left" width="69%">web'.$web["doc_id"].'db'.$new_db_id.'</td></tr></table>';
          $doc->deck[0]->elements[1]->value = '<table width="100%"><tr><td class="normal" align="left" width="31%"><b>'.$go_api->lng("Datenbankuser").':</b></td><td class="t2" align="left" width="69%">web'.$web["doc_id"].'u'.$new_db_id.'</td></tr></table>';
        }

}


function datenbank_insert($doc_id, $doctype_id, $die_on_error = '1') {
global $go_api, $go_info,$s;

    // Hole das Web der Datenbank
     $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_dep where
     isp_isp_web.doc_id = isp_dep.parent_doc_id and
     isp_isp_web.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");

     $web_doc_id = $web["doc_id"];
     $web_doctype_id = $web["doctype_id"];
     //unset($web);

     // sind für dieses Web überhaupt Datenbanken zugelassen?
     if(!$web["web_mysql"]){
       $status = "DELETE";
       $errorMessage = $go_api->lng("error_db_nicht_erlaubt");
     }

    // Datenbankname und -user festlegen
    $db_ids = $go_api->db->queryAllRecords("SELECT datenbankname FROM isp_isp_datenbank WHERE web_id = '$web_doc_id'");
          if(empty($db_ids)){
            $new_db_id = 1;
          } else {
            // kleinste freie ID finden
            foreach($db_ids as $db_id){
              if(strpos($db_id["datenbankname"], '_')){
                $db_nr[] = str_replace('web'.$web["doc_id"].'_db', '', $db_id["datenbankname"]);
              } else {
                $db_nr[] = str_replace('web'.$web["doc_id"].'db', '', $db_id["datenbankname"]);
              }
            }
            sort($db_nr, SORT_NUMERIC);
            reset($db_nr);
            $i = 0;
            while(!isset($new_db_id)){
              if($db_nr[$i] != ($i + 1)) $new_db_id = $i + 1;
              $i++;
            }
          }
    $datenbankname = 'web'.$web_doc_id.'db'.$new_db_id;
    $datenbankuser = 'web'.$web_doc_id.'u'.$new_db_id;


    $go_api->db->query("UPDATE isp_isp_datenbank SET datenbankname = '$datenbankname', datenbankuser = '$datenbankuser', web_id = '$web_doc_id' WHERE doc_id = '$doc_id'");

    // Eintrag der Datenbank holen
    $datenbank = $go_api->db->queryOneRecord("select * from isp_isp_datenbank where doc_id = '$doc_id'");

    // leere Passwörter zurückweisen
    if($datenbank["db_passwort"] == ""){
      $status = "DELETE";
      $errorMessage .= $go_api->lng("error_db_leeres_passwort");
    }

    // Check ob bereits eine Datenbank mit diesem Namen existiert
    $datenbankcount = $go_api->db->queryOneRecord("SELECT count(doc_id) as doc_count from isp_isp_datenbank where datenbankname = '".$datenbank["datenbankname"]."'");

        if($datenbankcount["doc_count"] > 1) {
        $status = "DELETE";
        $errorMessage .= $go_api->lng("error_db_exist_1")." ".$datenbank["datenbankname"]." ".$go_api->lng("error_db_exist_2");
    }

    // Check ob bereits ein Datenbankuser mit diesem Namen existiert
    $datenbankcount = $go_api->db->queryOneRecord("SELECT count(doc_id) as doc_count from isp_isp_datenbank where datenbankuser = '".$datenbank["datenbankuser"]."'");

        if($datenbankcount["doc_count"] > 1) {
        $status = "DELETE";
        $errorMessage .= $go_api->lng("error_db_user_exist_1")." ".$datenbank["datenbankuser"]." ".$go_api->lng("error_db_user_exist_2");
    }

    // Passwort der Datenbank verschlüsseln
    if(substr($datenbank["db_passwort"],0,5) != "||||:" and $datenbank["db_passwort"] != "") {
        $go_api->db->query("UPDATE isp_isp_datenbank SET db_passwort = concat('||||:' , password(db_passwort)) where doc_id = '$doc_id'");
    }

     // Checke, ob maximale Anzahl Datenbanken des Webs erreicht ist

     $datenbanklimit = $web["web_mysql_anzahl_dbs"];

     // Hole Datenbankanzahl
     $datenbankcount = $go_api->db->queryOneRecord("SELECT count(doc_id) as doc_count from isp_isp_datenbank, isp_dep where
     isp_isp_datenbank.doc_id = isp_dep.child_doc_id and isp_isp_datenbank.doctype_id = isp_dep.child_doctype_id and
     isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and isp_dep.child_doctype_id = $doctype_id");

     $datenbankanzahl = $datenbankcount["doc_count"];

    // wenn Datenbanklimits gesetzt sind
     if($datenbanklimit >= 0) {
        if($datenbankanzahl > $datenbanklimit) {
        $status = "DELETE";
        $errorMessage .= $go_api->lng("error_max_db_web");
        }
     }

     // Check Ob maximale Anzahl Datenbanken des Resellers erreicht ist
     $datenbank_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
     $resellerid = $datenbank_node["groupid"];

     if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
        // Wenn Resellerlimit erreicht wurde
        if($reseller["limit_mysql_anzahl_dbs"] >= 0) {
            $reseller_datenbankanzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as reseller_datenbankanzahl from isp_nodes where groupid = '$resellerid' and doctype_id = '".$this->datenbank_doctype_id."'");
            $reseller_datenbankanzahl = $reseller_datenbankanzahl["reseller_datenbankanzahl"];
            //echo $reseller_datenbankanzahl;
            if($reseller_datenbankanzahl >= $reseller["limit_mysql_anzahl_dbs"]) {
              $status = "DELETE";
              $errorMessage .= $go_api->lng("error_max_db_anbieter");
            }
        }
     }

    if($status == "DELETE") {
        // Eintrag löschen
        $go_api->db->query("DELETE from isp_isp_datenbank where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        if($die_on_error){
          $go_api->errorMessage($errorMessage.$go_api->lng("weiter_link"));
        } else {
          return $errorMessage;
        }
    } else {
        // Status der Datenbank auf 'n' setzen
        $go_api->db->query("UPDATE isp_isp_datenbank SET status = 'n' where doc_id = '$doc_id'");
        //User und Groupid auf die Werte des Web setzen
        $web_doc_id = $web["parent_doc_id"];
        $web_doctype_id = $web["parent_doctype_id"];
        $webnode = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
        $go_api->db->query("UPDATE isp_nodes SET groupid = ".$webnode["groupid"].", userid = ".$webnode["userid"].", title = '".$datenbank["datenbankname"]."' where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        $go_api->db->query("UPDATE isp_isp_web SET status = 'u' where status != 'n' and status != 'd' and doc_id = '$web_doc_id'");

    }

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');

        //$this->faktura_insert($doc_id,$web["doc_id"],$user["user_username"]);
    //$go_api->errorMessage($antwort);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////

function datenbank_update($doc_id, $doctype_id, $die_on_error = '1') {
global $go_api, $go_info,$s,$HTTP_POST_VARS;

        $go_api->uses("isp");

    $datenbank = $go_api->db->queryOneRecord("select * from isp_isp_datenbank where doc_id = '$doc_id'");

    // wenn Passwort leer, altes Passwort verwenden
    if($datenbank["db_passwort"] == ""){
      $db_server = $go_info["server"]["db_host"];
      $db_user = $go_info["server"]["db_user"];
      $db_password = $go_info["server"]["db_password"];
      $link = mysql_connect($db_server, $db_user, $db_password)
      or die("Could not connect");
      mysql_select_db("mysql")
      or die("Could not select database");
      $sql = "SELECT `Password` FROM `user` WHERE `User` = '".$datenbank["datenbankuser"]."' AND `Host` = 'localhost'";
      $conn = mysql_query($sql);
      if($row = mysql_fetch_array($conn)){
        if(trim($row["Password"]) != ""){
          $datenbank["db_passwort"] = '||||:'.$row["Password"];
          $go_api->db->query("UPDATE isp_isp_datenbank SET db_passwort = '".$datenbank["db_passwort"]."' WHERE doc_id = '$doc_id'");
          $errorMessage = $go_api->lng("error_db_leeres_passwort");
          if($die_on_error){
            $go_api->errorMessage($errorMessage.$go_api->lng("weiter_link"));
          } else {
            return $errorMessage;
          }
        }
      }
    }

    // Passwort der Datenbank verschlüsseln
    if(substr($datenbank["db_passwort"],0,5) != "||||:" and $datenbank["db_passwort"] != "") {
        $go_api->db->query("UPDATE isp_isp_datenbank SET db_passwort = concat('||||:' , password(db_passwort)) where doc_id = '$doc_id'");
    }

    $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_dep where
     isp_isp_web.doc_id = isp_dep.parent_doc_id and
     isp_isp_web.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");

     $go_api->db->query("UPDATE isp_isp_datenbank SET status = 'u' where status != 'n' and doc_id = '$doc_id'");


    //User und Groupid auf die Werte des Web setzen
    $web_doc_id = $web["parent_doc_id"];
    $web_doctype_id = $web["parent_doctype_id"];
    $webnode = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
    $go_api->db->query("UPDATE isp_nodes SET groupid = ".$webnode["groupid"].", userid = ".$webnode["userid"].", title = '.".$datenbank["datenbankname"]."' where doc_id = '$doc_id' and doctype_id = '$doctype_id'");

    $go_api->db->query("UPDATE isp_isp_web SET status = 'u' where status != 'n' and status != 'd' and doc_id = '$web_doc_id'");


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

function datenbank_delete($doc_id, $doctype_id, $action, $die_on_error = '1') {

global $go_api, $go_info;

    $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_dep where
     isp_isp_web.doc_id = isp_dep.parent_doc_id and
     isp_isp_web.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");
    $web_doc_id = $web["parent_doc_id"];
    $web_doctype_id = $web["doctype_id"];

    if($action == "do") {
        $go_api->db->query("UPDATE isp_isp_datenbank SET status = 'd' where doc_id = '$doc_id'");
                //$this->faktura_delete($doc_id,'do');
    } else {
        // Prüfen, ob die Datenbank überhaupt wiederhergestellt werden darf, ob das Web z.B. MySQL hat und genug DBs erlaubt sind
        // Hole Datenbankanzahl
     $datenbankcount = $go_api->db->queryOneRecord("SELECT count(doc_id) as doc_count from isp_isp_datenbank where web_id = '$web_doc_id'");

     $datenbankanzahl = $datenbankcount["doc_count"];


     // Check Ob maximale Anzahl Datenbanken des Resellers erreicht ist
     $datenbank_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
     $resellerid = $datenbank_node["groupid"];

     if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
        // Wenn Resellerlimit erreicht wurde
        if($reseller["limit_mysql_anzahl_dbs"] >= 0) {
            $reseller_datenbankanzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as reseller_datenbankanzahl from isp_nodes where groupid = '$resellerid' and doctype_id = '".$this->datenbank_doctype_id."'");
            $reseller_datenbankanzahl = $reseller_datenbankanzahl["reseller_datenbankanzahl"];
        } else {
          $reseller_datenbankanzahl = -1;
          $reseller["limit_mysql_anzahl_dbs"] = -1;
        }
     } else {
       $reseller_datenbankanzahl = -1;
       $reseller["limit_mysql_anzahl_dbs"] = -1;
     }


        if($web["web_mysql"] && $datenbankanzahl <= $web["web_mysql_anzahl_dbs"] && $reseller_datenbankanzahl <= $reseller["limit_mysql_anzahl_dbs"]){
          $go_api->db->query("UPDATE isp_isp_datenbank SET status = 'n' WHERE doc_id = '$doc_id'");
                //$this->faktura_delete($doc_id,'undo');
        } else {
          $go_api->db->query("UPDATE isp_nodes SET status = '0' WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
          if($datenbankanzahl > $web["web_mysql_anzahl_dbs"]){
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_max_db_web").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_max_db_web");
            }
          }
          if($reseller_datenbankanzahl > $reseller["limit_mysql_anzahl_dbs"]){
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_max_db_anbieter").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_max_db_anbieter");
            }
          }
        }
    }


    $go_api->db->query("UPDATE isp_isp_web SET status = 'u' WHERE status != 'n' AND status != 'd' AND doc_id = '$web_doc_id'");

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