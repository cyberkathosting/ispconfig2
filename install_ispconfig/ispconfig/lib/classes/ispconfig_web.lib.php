<?php

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

if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class web {

        var $errorMessage = '';
        var $kunde_doctype_id = 1012;
        var $web_doctype_id = 1013;
        var $user_doctype_id = 1014;
        var $reseller_doctype_id = 1022;
        var $slave_doctype_id = 1028;
        var $datenbank_doctype_id = 1029;
		var $list_doctype_id = 1033;

        /*
        #############################################################
        # Webs
        #############################################################
        */

        /*
        Function: web_list
        params:   leer
        return:   Array with web Arrays
        */

        function web_list($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                if(!isset($params["kunde_title"]) && !isset($params["kunde_id"])){
                  if(!isset($params["reseller_title"]) || $params["reseller_title"] == "all"){
                    // hole webs
                    $webs = $go_api->db->queryAllRecords("SELECT isp_nodes.title AS web_title, isp_isp_web.* FROM isp_isp_web, isp_nodes WHERE isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = isp_isp_web.doctype_id ORDER BY isp_isp_web.web_domain");
                  } else {
                    if($params["reseller_title"] == "admin"){
                      // hole Webs
                      $webs = $go_api->db->queryAllRecords("SELECT isp_nodes.title AS web_title, isp_isp_web.* FROM isp_isp_web, isp_nodes WHERE isp_nodes.groupid = 1 AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = isp_isp_web.doctype_id ORDER BY isp_isp_web.web_domain");
                    } else {
                      if($reseller_group = $this->reseller_get_gid($params["reseller_title"])){
                        // hole Webs
                        $webs = $go_api->db->queryAllRecords("SELECT isp_nodes.title AS web_title, isp_isp_web.* FROM isp_isp_web, isp_nodes WHERE isp_nodes.groupid = $reseller_group AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = isp_isp_web.doctype_id ORDER BY isp_isp_web.web_domain");
                      } else {
                        $this->errorMessage .= "cannot find reseller\r\n";
                        return false;
                      }
                    }
                  }
                } else {
                  if($webadmin_userid = $this->kunde_get_webadmin_userid($session,$params)){
                      // hole Webs
                      $webs = $go_api->db->queryAllRecords("SELECT isp_nodes.title AS web_title, isp_isp_web.* FROM isp_isp_web, isp_nodes WHERE isp_nodes.userid = $webadmin_userid AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = isp_isp_web.doctype_id ORDER BY isp_isp_web.web_domain");
                    } else {
                      $this->errorMessage .= "cannot find client\r\n";
                      return false;
                    }
                }

                if(!empty($webs)){
                  foreach($webs as $web) {
                        $web["web_id"] = $web["doc_id"];
                        $out[$web["web_title"]] = $web;

                  }
                  return $out;
                } else {
                  $this->errorMessage .= "no records found\r\n";
                  return false;
                }

        }

        /*
        Function: web_get
        Params: web_id oder web_title
        Return: web Array
        */

        function web_get($session,$params) {
                global $go_api, $go_info;


                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["web_id"]) && empty($params["web_title"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved\r\n";
                        return false;
                }

                // Hole web
                $web = $go_api->db->queryOneRecord("SELECT isp_nodes.title AS web_title, isp_isp_web.* FROM isp_nodes, isp_isp_web WHERE isp_isp_web.doc_id = $web_id AND isp_isp_web.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doctype_id = isp_isp_web.doctype_id");

                // überprüfe, ob web gefunden wurde
                if(!is_array($web)) {
                        $this->errorMessage .= "no records found\r\n";
                        return false;
                }

                $web["web_id"] = $web["doc_id"];
                $out = $web;

                return $out;
        }

        /*
        Function: web_get_id
        Params: web_title
        Return: web_id
        */

        function web_get_id($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                if(isset($params["web_id"]) && !empty($params["web_id"])) return intval($params["web_id"]);

                // überprüfe Parameter
                if(empty($params["web_title"])) {
                        $this->errorMessage .= "Parameter: web_title is required.\r\n";
                        return false;
                }

                // Hole web ID
                $web_title = addslashes($params["web_title"]);

                if($web = $go_api->db->queryOneRecord("SELECT doc_id FROM isp_nodes WHERE title = '$web_title' AND doctype_id = '".$this->web_doctype_id."'")){
                  return $web["doc_id"];
                } else {
                  return false;
                }


        }

        /*
        Function: web_add
        Params:
        Return: web_id
        */

        function web_add($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_insert"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["kunde_title"]) && empty($params["kunde_id"])) $this->errorMessage .= "Parameter: kunde_title or kunde_id is required.\r\n";
                if(empty($params["web_title"])) $this->errorMessage .= "Parameter: web_title is required.\r\n";
                if(empty($params["web_host"])) $this->errorMessage .= "Parameter: web_host is required.\r\n";
                if(empty($params["web_domain"])) $this->errorMessage .= "Parameter: web_domain is required.\r\n";
                if(empty($params["web_ip"])) $this->errorMessage .= "Parameter: web_ip is required.\r\n";
                if(empty($params["web_speicher"])) $this->errorMessage .= "Parameter: web_speicher is required.\r\n";
                if(empty($params["web_userlimit"])) $this->errorMessage .= "Parameter: web_userlimit is required.\r\n";
                if(empty($params["web_domainlimit"])) $this->errorMessage .= "Parameter: web_domainlimit is required.\r\n";
                if($this->errorMessage != '') return false;

                if(empty($params["web_dns"])) $params["web_dns"] = 0;
                if(empty($params["web_shell"])) $params["web_shell"] = 0;
                if(empty($params["web_cgi"])) $params["web_cgi"] = 0;
                if(empty($params["web_standard_cgi"]) || $params["web_cgi"] == 0) $params["web_standard_cgi"] = 0;
                if(empty($params["web_php"])) $params["web_php"] = 0;
                if(empty($params["web_php_safe_mode"]) || $params["web_php"] == 0) $params["web_php_safe_mode"] = 0;
                if(empty($params["web_ssi"])) $params["web_ssi"] = 0;
                if(empty($params["web_ftp"])) $params["web_ftp"] = 0;
                if(empty($params["web_frontpage"])) $params["web_frontpage"] = 0;
                if(empty($params["web_mysql"])) $params["web_mysql"] = 0;
                if(empty($params["web_mysql_anzahl_dbs"]) || $params["web_mysql"] == 0) $params["web_mysql_anzahl_dbs"] = 0;
                if($params["web_mysql_anzahl_dbs"] < 1) $params["web_mysql_anzahl_dbs"] = 1;
                if($params["web_mysql_anzahl_dbs"] > 30) $params["web_mysql_anzahl_dbs"] = 30;
                if(empty($params["web_ssl"])) $params["web_ssl"] = 0;
                if(empty($params["web_anonftp"])) $params["web_anonftp"] = 0;
                if(empty($params["web_anonftplimit"]) || $params["web_anonftp"] == 0) $params["web_anonftplimit"] = 0;
                if(empty($params["web_wap"])) $params["web_wap"] = 0;
                if(empty($params["web_individual_error_pages"])) $params["web_individual_error_pages"] = 0;

                if($params["web_frontpage"] != 0){
                  $server_conf = $go_api->db->queryOneRecord("SELECT * from isp_server");
                  if($server_conf["server_enable_frontpage"] == 0) $params["web_frontpage"] = 0;
                }

                if(!$kunde_webadmin_userid = $this->kunde_get_webadmin_userid($session,$params)){
                  $this->errorMessage         .= "Parameter: cannot find client.\r\n";
                  return false;
                }

                // Hole Reseller Group
                $kunde_node = $go_api->db->queryOneRecord("SELECT isp_nodes.groupid, isp_nodes.doc_id, isp_nodes.tree_id FROM isp_nodes, isp_isp_kunde WHERE isp_isp_kunde.webadmin_userid = '$kunde_webadmin_userid' and isp_nodes.doctype_id = '".$this->kunde_doctype_id."' AND isp_nodes.doc_id = isp_isp_kunde.doc_id AND isp_nodes.doctype_id = isp_isp_kunde.doctype_id");
                $reseller_group = $kunde_node["groupid"];
                if(empty($reseller_group)) {
                        $this->errorMessage .= "reseller_group cannot be resolved\r\n";
                        return false;
                }

                $tree_id = $go_api->db->queryOneRecord("SELECT tree_id FROM isp_nodes WHERE groupid = '".$reseller_group."' AND parent = 'group".$reseller_group."' AND type = 'n' AND doctype_id = '0' AND doc_id = '0' AND title = 'Webs'");
                $tree_id = $tree_id["tree_id"];
                if(empty($tree_id)){
                  $this->errorMessage .= "tree_id cannot be resolved\r\n";
                  return false;
                }

                // checke ob Web bereits existiert
                $web_id = $this->web_get_id($session,$params);
                if(!empty($web_id)) {
                        $this->errorMessage .= "Web: ".$params["web_title"]." already exists\r\n";
                        return false;
                }

                // Record einfügen
                $sql = "INSERT INTO isp_isp_web (doctype_id, web_host, web_domain, web_ip, web_speicher, web_dns, web_userlimit, web_domainlimit, web_shell, web_cgi, web_standard_cgi, web_php, web_php_safe_mode, web_ssi, web_ftp, web_frontpage, web_mysql, web_mysql_anzahl_dbs, web_ssl, web_anonftp, web_anonftplimit, web_wap, web_individual_error_pages, server_id) VALUES ('".$this->web_doctype_id."', '".addslashes($params["web_host"])."', '".addslashes($params["web_domain"])."', '".addslashes($params["web_ip"])."', '".addslashes($params["web_speicher"])."', '".addslashes($params["web_dns"])."', '".addslashes($params["web_userlimit"])."', '".addslashes($params["web_domainlimit"])."', '".addslashes($params["web_shell"])."', '".addslashes($params["web_cgi"])."', '".addslashes($params["web_standard_cgi"])."', '".addslashes($params["web_php"])."', '".addslashes($params["web_php_safe_mode"])."', '".addslashes($params["web_ssi"])."', '".addslashes($params["web_ftp"])."', '".addslashes($params["web_frontpage"])."', '".addslashes($params["web_mysql"])."', '".addslashes($params["web_web_mysql_anzahl_dbs"])."', '".addslashes($params["web_ssl"])."', '".addslashes($params["web_anonftp"])."', '".addslashes($params["web_anonftplimit"])."', '".addslashes($params["web_wap"])."', '".addslashes($params["web_individual_error_pages"])."', '1')";

                $go_api->db->query($sql);
                $web_id = $doc_id = $go_api->db->insertID();

                // Node einfügen
                $sql = "INSERT INTO isp_nodes (userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title ) VALUES ('".$kunde_webadmin_userid."', '".$reseller_group."', '".$tree_id."', 'i', '".$this->web_doctype_id."', '1', '', '', $doc_id, '".addslashes($params["web_title"])."')";

                $go_api->db->query($sql);
                $child_tree_id = $go_api->db->insertID();

                // isp_dep
                $go_api->db->query("INSERT INTO isp_dep (userid, groupid, parent_doc_id, parent_doctype_id, parent_tree_id, child_doc_id, child_doctype_id, child_tree_id, status) VALUES ('1', '".$reseller_group."', '".$kunde_node["doc_id"]."', '".$this->kunde_doctype_id."', '".$kunde_node["tree_id"]."', '".$doc_id."', '".$this->web_doctype_id."', '".$child_tree_id."', '1')");

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";;

                if(!$doc = $go_api->doc->doctype_get($this->web_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }



                  if($doc->event_insert != "") {
                    $event_insert = $doc->event_insert;
                    $this->errorMessage .= $go_api->$event_class->$event_insert($doc_id,$this->web_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;
                return $web_id;
        }

        function web_update($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["web_id"]) && empty($params["web_title"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }
                if(isset($params["web_mysql_anzahl_dbs"])){
                  if($params["web_mysql_anzahl_dbs"] < 1) $params["web_mysql_anzahl_dbs"] = 1;
                  if($params["web_mysql_anzahl_dbs"] > 30) $params["web_mysql_anzahl_dbs"] = 30;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved\r\n";
                        return false;
                }

                // Liegt Objekt im Papierkorb?
                if($this->web_is_suspended($session,$params)){
                  $this->errorMessage .= "Web is suspended.\r\n";
                  return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->web_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $fields = $go_api->db->queryAllRecords("SHOW COLUMNS FROM isp_isp_web");
                foreach($fields as $field){
                  $field_arr[] = $field["Field"];
                }

                $changes = "";
                foreach($params as $key => $val){
                  if($key != "web_id" && $key != "doc_id" && $key != "doctype_id" && $key != "status" && $key != "server_id" && $key != "optionen_mysql_user" && $key != "optionen_mysql_passwort" && $key != "optionen_mysql_remote_access" && $key != "ssl_request" && $key != "ssl_cert" && in_array($key, $field_arr)) $changes .= $key." = '".addslashes($val)."',";
                }
                $changes = substr($changes, 0, -1);

                $sql = "UPDATE isp_isp_web SET ".$changes." WHERE doc_id = ".$web_id;

                if(!$go_api->db->query($sql)) $this->errorMessage  .= "Database could not be updated.\r\n";
                if($this->errorMessage != '') return false;

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->web_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($web_id,$this->web_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $this->web_get($session,$params);
        }

        function web_suspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["web_title"]) && empty($params["web_id"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved\r\n";
                        return false;
                }

                if($this->web_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->web_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                // alle zugehörigen Records in den Papierkorb verschieben
                $sql = "SELECT child_doc_id, child_doctype_id FROM isp_dep WHERE parent_doc_id = $web_id";
                $childs = $go_api->db->queryAllRecords($sql);
                foreach($childs as $child) {
                        $sql = "UPDATE isp_nodes SET status = 0 where doc_id = $child[child_doc_id] and doctype_id = $child[child_doctype_id]";
                        $go_api->db->query($sql);
                        #################
                        // Dokumententyp holen
                        $doc = $go_api->doc->doctype_get($child[child_doctype_id]);

                       // on Delete Event ausführen
                       if($doc->event_class != "") {
                         $event_class = $doc->event_class;
                         if(!class_exists($event_class)){
                           $go_api->uses($doc->event_class);
                         }
                         if($doc->event_delete != "") {
                           $event_delete = $doc->event_delete;
                           $this->errorMessage .= $go_api->$event_class->$event_delete($child[child_doc_id],$child[child_doctype_id],'do', 0);
                         }
                       }
                       ######################
                }

                // suspend durchführen
                $sql = "UPDATE isp_nodes SET status = 0 WHERE doc_id = $web_id and doctype_id = '".$this->web_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                if(!$doc = $go_api->doc->doctype_get($this->web_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($web_id,$this->web_doctype_id, 'do', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function web_unsuspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["web_title"]) && empty($params["web_id"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved\r\n";
                        return false;
                }

                if(!$this->web_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->web_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                // unsuspend durchführen
                $sql = "UPDATE isp_nodes SET status = 1 WHERE doc_id = $web_id and doctype_id = '".$this->web_doctype_id."'";
                $go_api->db->query($sql);

                // alle zugehörigen Records aus dem Papierkorb holen
                $sql = "SELECT child_doc_id, child_doctype_id FROM isp_dep WHERE parent_doc_id = $web_id";
                $childs = $go_api->db->queryAllRecords($sql);
                foreach($childs as $child) {
                        $sql = "UPDATE isp_nodes SET status = 1 where doc_id = $child[child_doc_id] and doctype_id = $child[child_doctype_id]";
                        $go_api->db->query($sql);
                        #################
                        // Dokumententyp holen
                        $doc = $go_api->doc->doctype_get($child[child_doctype_id]);

                       // on Delete Event ausführen
                       if($doc->event_class != "") {
                         $event_class = $doc->event_class;
                         if(!class_exists($event_class)){
                           $go_api->uses($doc->event_class);
                         }
                         if($doc->event_delete != "") {
                           $event_delete = $doc->event_delete;
                           $this->errorMessage .= $go_api->$event_class->$event_delete($child[child_doc_id],$child[child_doctype_id],'undo', 0);
                         }
                       }
                       ######################
                }

                /////////////////////////
                if(!$doc = $go_api->doc->doctype_get($this->web_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($web_id,$this->web_doctype_id, 'undo', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function web_delete($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_delete"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["web_title"]) && empty($params["web_id"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved\r\n";
                        return false;
                }

                // Ist Web suspended?
                if(!$this->web_is_suspended($session,$params)){
                        $this->errorMessage .= "The record must be suspended before it can be deleted.\r\n";
                        return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->web_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // DELETE durchführen
                //$sql = "DELETE dns_nodes.*, dns_secondary.* FROM dns_nodes, dns_secondary WHERE dns_nodes.doc_id = '$slave_id' and dns_nodes.doctype_id = '".$this->slave_doctype_id."' and dns_nodes.status != '1' AND dns_nodes.doc_id = dns_secondary.doc_id AND dns_nodes.doctype_id = dns_secondary.doctype_id";

                //return $go_api->db->query($sql);

                ##############################
                $go_api->uses('doc,auth,log,isp');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                $groupid = $go_api->db->queryOneRecord("SELECT groupid FROM isp_nodes WHERE doc_id = '".$web_id."' AND doctype_id = '".$this->web_doctype_id."'");
                $groupid = $groupid["groupid"];
                //$sql = "SELECT * FROM isp_nodes where status = 0 and groupid = $groupid";
                $sql = "SELECT DISTINCT isp_nodes.* FROM isp_nodes, isp_dep where (isp_nodes.status = 0 and isp_nodes.groupid = $groupid AND isp_dep.groupid = isp_nodes.groupid AND isp_dep.parent_doctype_id = '".$this->web_doctype_id."' AND isp_dep.parent_doc_id = '".$web_id."' AND isp_dep.child_tree_id = isp_nodes.tree_id) OR (isp_nodes.doc_id = '".$web_id."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_dep.parent_doc_id = '".$web_id."' AND isp_dep.parent_doctype_id = '".$this->web_doctype_id."' AND isp_nodes.tree_id = isp_dep.parent_tree_id)";
                $items = $go_api->db->queryAllRecords($sql);


                foreach ($items as $key => $value) {
                  // Einträge in del_status auf Status 'd' setzen
                  $go_api->db->query("UPDATE del_status SET status = 'd' WHERE doc_id = '".$value["doc_id"]."' AND doctype_id = '".$value["doctype_id"]."'");

                  $row = $go_api->db->queryOneRecord("SELECT doctype_modul,doctype_name from doctype where doctype_id = ".$value["doctype_id"]);

                  if($value["type"] == 'i' or $value["type"] == 'a') {

                    $stat = $go_api->db->queryOneRecord("SELECT status from ".$row["doctype_modul"]."_".$row["doctype_name"]." where doc_id = ".$value["doc_id"]);

                      if($stat["status"] == "" or !isset($stat["status"]) or empty($stat["status"])) {

                        // Löschen des Tree eintrages
                        $go_api->db->query("DELETE from isp_nodes where status = 0 and tree_id = ".$value["tree_id"]);
                        // löschen der Daten Einträge
                        $go_api->db->query("DELETE from ".$row["doctype_modul"]."_".$row["doctype_name"]." where doc_id = ".$value["doc_id"]);
                        // löschen der abhängigkeiten
                        $go_api->db->query("DELETE from isp_dep where parent_tree_id = ".$value["tree_id"]." or child_tree_id = ".$value["tree_id"]);
                        // löschen angehängte Termine
                        $go_api->db->query("DELETE from termin_event where parent_doc_id = ".$value["doc_id"]." and parent_doctype_id = ".$value["doctype_id"]);
                        //löschen abhängigkeiten der angehängte Files
                        $go_api->db->query("UPDATE file_nodes SET parent_doc_id = 0, parent_doctype_id = 0 where parent_doc_id = ".$value["doc_id"]." and parent_doctype_id = ".$value["doctype_id"]);
                      }
                    } else {
                      // Löschen des Tree eintrages
                      $go_api->db->query("DELETE from isp_nodes where status = 0 and tree_id = ".$value["tree_id"]);
                    }
                  }

            // Löschen der Faktura Daten
            $go_api->db->query("DELETE from isp_fakt_record where status = 0");

            // Server benachrichtigen
            $server_id = 1;
            $go_api->isp->signal_server($server_id,'empty trash');
            return true;
            ################################
        }

        /*
        #############################################################
        # Users
        #############################################################
        */

        /*
        Function: user_list
        Params: web_id oder web_title
        Return: list of users Array
        */

        function user_list($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["web_title"]) && empty($params["web_id"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved.\r\n";
                        return false;
                }

                $sql = "SELECT isp_isp_user.* FROM isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = $web_id AND isp_dep.parent_doctype_id = '".$this->web_doctype_id."' and isp_dep.child_doc_id = isp_isp_user.doc_id and isp_dep.child_doctype_id = '".$this->user_doctype_id."'";

                // hole User
                $users = $go_api->db->queryAllRecords($sql);

                if(!empty($users)){
                  foreach($users as $user) {
                        $user["user_id"] = $user["doc_id"];
                        $out[$user["user_username"]] = $user;

                  }
                  return $out;
                } else {
                  $this->errorMessage .= "no records found\r\n";
                  return false;
                }

        }

        function user_get_id($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                if(isset($params["user_id"]) && !empty($params["user_id"])) return intval($params["user_id"]);

                // überprüfe Parameter
                if(empty($params["user_username"])) {
                        $this->errorMessage .= "Parameter: user_username is required.\r\n";
                        return false;
                }

                // Hole User ID
                $user_username = addslashes($params["user_username"]);

                if($user = $go_api->db->queryOneRecord("SELECT doc_id FROM isp_isp_user WHERE user_username = '$user_username'")){
                  return $user["doc_id"];
                } else {
                  return false;
                }


        }

        function user_get($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["user_username"]) && empty($params["user_id"])) {
                        $this->errorMessage .= "Parameters: user_username or user_id are required.\r\n";
                        return false;
                }

                // Hole User ID
                if(empty($params["user_id"])) {
                        $user_id = $this->user_get_id($session,$params);
                } else {
                        $user_id = intval($params["user_id"]);
                }

                if(empty($user_id)) {
                        $this->errorMessage .= "user_id cannot be resolved\r\n";
                        return false;
                }

                $user = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_user WHERE doc_id = $user_id");

                // überprüfe, ob User gefunden wurde
                if(!is_array($user)) {
                        $this->errorMessage .= "no records found\r\n";
                        return false;
                }

                $user["user_id"] = $user["doc_id"];
                $out = $user;

                return $out;
        }

        function user_add($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_insert"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["web_title"]) && empty($params["web_id"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(!isset($params["user_name"])) $this->errorMessage .= "Parameter: user_name is required.\r\n";
                if(empty($params["user_email"])) $this->errorMessage .= "Parameter: user_email is required.\r\n";
                if(empty($params["user_username"])) $this->errorMessage .= "Parameter: user_username is required.\r\n";
                if(empty($params["user_passwort"])) $this->errorMessage .= "Parameter: user_passwort is required.\r\n";
                if(!isset($params["user_speicher"])) $this->errorMessage .= "Parameter: user_speicher is required.\r\n";
                if(!isset($params["user_mailquota"])) $this->errorMessage .= "Parameter: user_mailquota is required.\r\n";
                if(!isset($params["user_admin"])) $this->errorMessage .= "Parameter: user_admin is required.\r\n";
                if($this->errorMessage != "") return false;

                // Hole Daten zu Parent Record (Web)
                $sql = "SELECT * FROM isp_nodes WHERE doc_id = $web_id and doctype_id = '".$this->web_doctype_id."'";
                $web_record = $go_api->db->queryOneRecord($sql);

                if(empty($web_record["userid"])) {
                        $this->errorMessage .= "web user cannot be resolved.\r\n";
                        return false;
                }

                // Checke ob User bereits existiert
                $user_id = $this->user_get_id($session,$params);
                if(!empty($user_id)) {
                        $this->errorMessage .= "User: ".$params["user_username"]." already exists\r\n";
                        return false;
                }

                // Füge User ein
                $sql = "INSERT INTO isp_isp_user (doctype_id,user_name,user_email,user_username,user_passwort,user_speicher,user_mailquota,user_admin) VALUES ('".$this->user_doctype_id."','".addslashes($params["user_name"])."','".addslashes($params["user_email"])."','".addslashes($params["user_username"])."','".addslashes($params["user_passwort"])."','".addslashes($params["user_speicher"])."','".addslashes($params["user_mailquota"])."','".addslashes($params["user_admin"])."')";
                $go_api->db->query($sql);
                $user_id = $doc_id = $go_api->db->insertID();

                // Node einfügen
                $sql = "INSERT INTO isp_nodes ( userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title
                                ) VALUES (
                                ".intval($web_record["userid"]).",
                                ".intval($web_record["groupid"]).",
                                '',
                                'a',
                                '".$this->user_doctype_id."',
                                '1',
                                '',
                                '',
                                $user_id,
                                ''
                                )";

                $go_api->db->query($sql);
                $user_tree_id = $go_api->db->insertID();

                // DEP-Record einfügen
                $sql = "INSERT INTO isp_dep (userid, groupid, parent_doc_id, parent_doctype_id, parent_tree_id, child_doc_id, child_doctype_id, child_tree_id, status)
                VALUES (
                ".intval($web_record["userid"]).",
                ".intval($web_record["groupid"]).",
                ".intval($web_record["doc_id"]).",
                '".$this->web_doctype_id."',
                ".intval($web_record["tree_id"]).",
                $user_id,
                '".$this->user_doctype_id."',
                $user_tree_id,
                1)";

                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->user_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_insert != "") {
                    $event_insert = $doc->event_insert;
                    $this->errorMessage .= $go_api->$event_class->$event_insert($doc_id,$this->user_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;
                return $user_id;

        }

        function user_update($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["user_username"]) && empty($params["user_id"])){
                  $this->errorMessage .= "Parameter: user_id or user_username is required.\r\n";
                  return false;
                }

                // Hole User ID
                if(empty($params["user_id"])) {
                        $user_id = $this->user_get_id($session,$params);
                } else {
                        $user_id = intval($params["user_id"]);
                }

                if(empty($user_id)) {
                        $this->errorMessage .= "user_id cannot be resolved\r\n";
                        return false;
                }

                // checke ob User schon existiert
                $sql = "SELECT doc_id FROM isp_isp_user where user_username = '".addslashes($params["user_username"])."' and isp_isp_user.doc_id != $user_id";

                $tmp = $go_api->db->queryOneRecord($sql);
                if($tmp["doc_id"] > 0) {
                        $this->errorMessage .= "User alredy exists with other user_id.\r\n";
                        return false;
                }

                // Liegt Objekt im Papierkorb?
                if($this->user_is_suspended($session,$params)){
                  $this->errorMessage .= "This user is suspended.\r\n";
                  return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->user_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $fields = $go_api->db->queryAllRecords("SHOW COLUMNS FROM isp_isp_user");
                foreach($fields as $field){
                  $field_arr[] = $field["Field"];
                }

                $changes = "";
                foreach($params as $key => $val){
                  if($key != "user_id" && $key != "doc_id" && $key != "doctype_id" && $key != "user_username" && $key != "status" && in_array($key, $field_arr)) $changes .= $key." = '".addslashes($val)."',";
                }
                $changes = substr($changes, 0, -1);

                $sql = "UPDATE isp_isp_user SET ".$changes." WHERE doc_id = ".$user_id;

                if(!$go_api->db->query($sql)) $this->errorMessage  .= "Database could not be updated.\r\n";
                if($this->errorMessage != '') return false;

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->user_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($user_id,$this->user_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $this->user_get($session,$params);

        }

        function user_suspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["user_id"]) && empty($params["user_username"])) {
                        $this->errorMessage .= "Parameters: user_username or user_id are required.\r\n";
                        return false;
                }

                if(empty($params["user_id"])) {
                        $user_id = $this->user_get_id($session,$params);
                } else {
                        $user_id = intval($params["user_id"]);
                }
                if(empty($user_id)) {
                        $this->errorMessage .= "user_id cannot be resolved\r\n";
                        return false;
                }

                if($this->user_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->user_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // suspend durchführen
                $sql = "UPDATE isp_nodes SET status = 0 WHERE doc_id = $user_id and doctype_id = '".$this->user_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->user_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($user_id,$this->user_doctype_id, 'do', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function user_unsuspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["user_id"]) && empty($params["user_username"])) {
                        $this->errorMessage .= "Parameters: user_username or user_id are required.\r\n";
                        return false;
                }

                if(empty($params["user_id"])) {
                        $user_id = $this->user_get_id($session,$params);
                } else {
                        $user_id = intval($params["user_id"]);
                }
                if(empty($user_id)) {
                        $this->errorMessage .= "user_id cannot be resolved\r\n";
                        return false;
                }

                if(!$this->user_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->user_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // unsuspend durchführen
                $sql = "UPDATE isp_nodes SET status = 1 WHERE doc_id = $user_id and doctype_id = '".$this->user_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->user_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($user_id,$this->user_doctype_id, 'undo', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function user_delete($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_delete"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["user_id"]) && empty($params["user_username"])) {
                        $this->errorMessage .= "Parameters: user_username or user_id are required.\r\n";
                        return false;
                }

                if(empty($params["user_id"])) {
                        $user_id = $this->user_get_id($session,$params);
                } else {
                        $user_id = intval($params["user_id"]);
                }
                if(empty($user_id)) {
                        $this->errorMessage .= "user_id cannot be resolved\r\n";
                        return false;
                }

                // Ist User suspended?
                if(!$this->user_is_suspended($session,$params)){
                        $this->errorMessage .= "The record must be suspended before it can be deleted.\r\n";
                        return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->user_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // DELETE durchführen
                $go_api->db->query("DELETE FROM isp_dep WHERE child_doc_id = '".$user_id."' AND child_doctype_id = '".$this->user_doctype_id."' AND parent_doctype_id = '".$this->web_doctype_id."'");
                $sql = "DELETE isp_nodes.*, isp_isp_user.* FROM isp_nodes, isp_isp_user WHERE isp_nodes.doc_id = '$user_id' and isp_nodes.doctype_id = '".$this->user_doctype_id."' and isp_nodes.status != '1' AND isp_nodes.doc_id = isp_isp_user.doc_id AND isp_nodes.doctype_id = isp_isp_user.doctype_id";
                return $go_api->db->query($sql);
        }

        /*
        #############################################################
        # Lists
        #############################################################
        */

        /*
        Function: list_list
        Params: web_id oder web_title
        Return: list of lists Array
        */

        function list_list($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["web_title"]) && empty($params["web_id"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved.\r\n";
                        return false;
                }

                $sql = "SELECT isp_isp_list.* FROM isp_dep, isp_isp_list WHERE isp_dep.parent_doc_id = $web_id AND isp_dep.parent_doctype_id = '".$this->web_doctype_id."' and isp_dep.child_doc_id = isp_isp_list.doc_id and isp_dep.child_doctype_id = '".$this->list_doctype_id."'";

                // hole List
                $lists = $go_api->db->queryAllRecords($sql);

                if(!empty($lists)){
                  foreach($lists as $list) {
                        $list["list_id"] = $list["doc_id"];
                        $out[$list["list_name"]] = $list;

                  }
                  return $out;
                } else {
                  $this->errorMessage .= "no records found\r\n";
                  return false;
                }

        }

        function list_get_id($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                if(isset($params["list_id"]) && !empty($params["list_id"])) return intval($params["list_id"]);

                // überprüfe Parameter
                if(empty($params["list_name"])) {
                        $this->errorMessage .= "Parameter: list_name is required.\r\n";
                        return false;
                }

                // Hole List ID
                $list_name = addslashes($params["list_name"]);

                if($list = $go_api->db->queryOneRecord("SELECT doc_id FROM isp_isp_list WHERE list_name = '$list_name'")){
                  return $list["doc_id"];
                } else {
                  return false;
                }


        }

        function list_get($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["list_name"]) && empty($params["list_id"])) {
                        $this->errorMessage .= "Parameters: list_name or list_id are required.\r\n";
                        return false;
                }

                // Hole List ID
                if(empty($params["list_id"])) {
                        $list_id = $this->list_get_id($session,$params);
                } else {
                        $list_id = intval($params["list_id"]);
                }

                if(empty($list_id)) {
                        $this->errorMessage .= "list_id cannot be resolved\r\n";
                        return false;
                }

                $list = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_list WHERE doc_id = $list_id");

                // überprüfe, ob List gefunden wurde
                if(!is_array($list)) {
                        $this->errorMessage .= "no records found\r\n";
                        return false;
                }

                $list["list_id"] = $list["list_id"];
                $out = $list;

                return $out;
        }

        /*function list_add($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_insert"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["web_title"]) && empty($params["web_id"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(!isset($params["user_name"])) $this->errorMessage .= "Parameter: user_name is required.\r\n";
                if(empty($params["user_email"])) $this->errorMessage .= "Parameter: user_email is required.\r\n";
                if(empty($params["user_username"])) $this->errorMessage .= "Parameter: user_username is required.\r\n";
                if(empty($params["user_passwort"])) $this->errorMessage .= "Parameter: user_passwort is required.\r\n";
                if(!isset($params["user_speicher"])) $this->errorMessage .= "Parameter: user_speicher is required.\r\n";
                if(!isset($params["user_mailquota"])) $this->errorMessage .= "Parameter: user_mailquota is required.\r\n";
                if(!isset($params["user_admin"])) $this->errorMessage .= "Parameter: user_admin is required.\r\n";
                if($this->errorMessage != "") return false;

                // Hole Daten zu Parent Record (Web)
                $sql = "SELECT * FROM isp_nodes WHERE doc_id = $web_id and doctype_id = '".$this->web_doctype_id."'";
                $web_record = $go_api->db->queryOneRecord($sql);

                if(empty($web_record["userid"])) {
                        $this->errorMessage .= "web user cannot be resolved.\r\n";
                        return false;
                }

                // Checke ob User bereits existiert
                $user_id = $this->user_get_id($session,$params);
                if(!empty($user_id)) {
                        $this->errorMessage .= "User: ".$params["user_username"]." already exists\r\n";
                        return false;
                }

                // Füge User ein
                $sql = "INSERT INTO isp_isp_user (doctype_id,user_name,user_email,user_username,user_passwort,user_speicher,user_mailquota,user_admin) VALUES ('".$this->user_doctype_id."','".addslashes($params["user_name"])."','".addslashes($params["user_email"])."','".addslashes($params["user_username"])."','".addslashes($params["user_passwort"])."','".addslashes($params["user_speicher"])."','".addslashes($params["user_mailquota"])."','".addslashes($params["user_admin"])."')";
                $go_api->db->query($sql);
                $user_id = $doc_id = $go_api->db->insertID();

                // Node einfügen
                $sql = "INSERT INTO isp_nodes ( userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title
                                ) VALUES (
                                ".intval($web_record["userid"]).",
                                ".intval($web_record["groupid"]).",
                                '',
                                'a',
                                '".$this->user_doctype_id."',
                                '1',
                                '',
                                '',
                                $user_id,
                                ''
                                )";

                $go_api->db->query($sql);
                $user_tree_id = $go_api->db->insertID();

                // DEP-Record einfügen
                $sql = "INSERT INTO isp_dep (userid, groupid, parent_doc_id, parent_doctype_id, parent_tree_id, child_doc_id, child_doctype_id, child_tree_id, status)
                VALUES (
                ".intval($web_record["userid"]).",
                ".intval($web_record["groupid"]).",
                ".intval($web_record["doc_id"]).",
                '".$this->web_doctype_id."',
                ".intval($web_record["tree_id"]).",
                $user_id,
                '".$this->user_doctype_id."',
                $user_tree_id,
                1)";

                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->user_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_insert != "") {
                    $event_insert = $doc->event_insert;
                    $this->errorMessage .= $go_api->$event_class->$event_insert($doc_id,$this->user_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;
                return $user_id;

        }*/

        /*function user_update($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["user_username"]) && empty($params["user_id"])){
                  $this->errorMessage .= "Parameter: user_id or user_username is required.\r\n";
                  return false;
                }

                // Hole User ID
                if(empty($params["user_id"])) {
                        $user_id = $this->user_get_id($session,$params);
                } else {
                        $user_id = intval($params["user_id"]);
                }

                if(empty($user_id)) {
                        $this->errorMessage .= "user_id cannot be resolved\r\n";
                        return false;
                }

                // checke ob User schon existiert
                $sql = "SELECT doc_id FROM isp_isp_user where user_username = '".addslashes($params["user_username"])."' and isp_isp_user.doc_id != $user_id";

                $tmp = $go_api->db->queryOneRecord($sql);
                if($tmp["doc_id"] > 0) {
                        $this->errorMessage .= "User alredy exists with other user_id.\r\n";
                        return false;
                }

                // Liegt Objekt im Papierkorb?
                if($this->user_is_suspended($session,$params)){
                  $this->errorMessage .= "This user is suspended.\r\n";
                  return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->user_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $fields = $go_api->db->queryAllRecords("SHOW COLUMNS FROM isp_isp_user");
                foreach($fields as $field){
                  $field_arr[] = $field["Field"];
                }

                $changes = "";
                foreach($params as $key => $val){
                  if($key != "user_id" && $key != "doc_id" && $key != "doctype_id" && $key != "user_username" && $key != "status" && in_array($key, $field_arr)) $changes .= $key." = '".addslashes($val)."',";
                }
                $changes = substr($changes, 0, -1);

                $sql = "UPDATE isp_isp_user SET ".$changes." WHERE doc_id = ".$user_id;

                if(!$go_api->db->query($sql)) $this->errorMessage  .= "Database could not be updated.\r\n";
                if($this->errorMessage != '') return false;

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->user_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($user_id,$this->user_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $this->user_get($session,$params);

        }*/

        function list_suspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["list_id"]) && empty($params["list_name"])) {
                        $this->errorMessage .= "Parameters: list_name or list_id are required.\r\n";
                        return false;
                }

                if(empty($params["list_id"])) {
                        $list_id = $this->list_get_id($session,$params);
                } else {
                        $list_id = intval($params["list_id"]);
                }
                if(empty($list_id)) {
                        $this->errorMessage .= "list_id cannot be resolved\r\n";
                        return false;
                }

                if($this->list_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->list_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // suspend durchführen
                $sql = "UPDATE isp_nodes SET status = 0 WHERE doc_id = $list_id and doctype_id = '".$this->list_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->list_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($list_id,$this->list_doctype_id, 'do', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function list_unsuspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["list_id"]) && empty($params["list_name"])) {
                        $this->errorMessage .= "Parameters: list_name or list_id are required.\r\n";
                        return false;
                }

                if(empty($params["list_id"])) {
                        $list_id = $this->list_get_id($session,$params);
                } else {
                        $list_id = intval($params["list_id"]);
                }
                if(empty($list_id)) {
                        $this->errorMessage .= "list_id cannot be resolved\r\n";
                        return false;
                }

                if(!$this->list_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->list_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // unsuspend durchführen
                $sql = "UPDATE isp_nodes SET status = 1 WHERE doc_id = $list_id and doctype_id = '".$this->list_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->list_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($list_id,$this->list_doctype_id, 'undo', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function list_delete($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_delete"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["list_id"]) && empty($params["list_name"])) {
                        $this->errorMessage .= "Parameters: list_name or list_id are required.\r\n";
                        return false;
                }

                if(empty($params["list_id"])) {
                        $list_id = $this->list_get_id($session,$params);
                } else {
                        $list_id = intval($params["list_id"]);
                }
                if(empty($list_id)) {
                        $this->errorMessage .= "list_id cannot be resolved\r\n";
                        return false;
                }

                // Ist List suspended?
                if(!$this->list_is_suspended($session,$params)){
                        $this->errorMessage .= "The record must be suspended before it can be deleted.\r\n";
                        return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->list_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // DELETE durchführen
                $go_api->db->query("DELETE FROM isp_dep WHERE child_doc_id = '".$list_id."' AND child_doctype_id = '".$this->list_doctype_id."' AND parent_doctype_id = '".$this->web_doctype_id."'");
                $sql = "DELETE isp_nodes.*, isp_isp_list.* FROM isp_nodes, isp_isp_list WHERE isp_nodes.doc_id = '$list_id' and isp_nodes.doctype_id = '".$this->list_doctype_id."' and isp_nodes.status != '1' AND isp_nodes.doc_id = isp_isp_list.doc_id AND isp_nodes.doctype_id = isp_isp_list.doctype_id";
                return $go_api->db->query($sql);
        }
        
        /*
        #############################################################
        # Databases
        #############################################################
        */

        /*
        Function: datenbank_list
        Params: web_id oder web_title
        Return: list of datenbank Array
        */

        function datenbank_list($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["web_title"]) && empty($params["web_id"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved.\r\n";
                        return false;
                }

                $sql = "SELECT isp_isp_datenbank.* FROM isp_dep, isp_isp_datenbank WHERE isp_dep.parent_doc_id = $web_id AND isp_dep.parent_doctype_id = '".$this->web_doctype_id."' and isp_dep.child_doc_id = isp_isp_datenbank.doc_id and isp_dep.child_doctype_id = '".$this->datenbank_doctype_id."'";

                // hole Datenbank
                $datenbanken = $go_api->db->queryAllRecords($sql);

                if(!empty($datenbanken)){
                  foreach($datenbanken as $datenbank) {
                        $datenbank["datenbank_id"] = $datenbank["doc_id"];
                        $out[$datenbank["datenbankname"]] = $datenbank;

                  }
                  return $out;
                } else {
                  $this->errorMessage .= "no records found\r\n";
                  return false;
                }

        }

        function datenbank_get_id($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                if(isset($params["datenbank_id"]) && !empty($params["datenbank_id"])) return intval($params["datenbank_id"]);

                // überprüfe Parameter
                if(empty($params["datenbankname"])) {
                        $this->errorMessage .= "Parameter: datenbankname is required.\r\n";
                        return false;
                }

                // Hole Datenbank ID
                $datenbankname = addslashes($params["datenbankname"]);

                if($datenbank = $go_api->db->queryOneRecord("SELECT doc_id FROM isp_isp_datenbank WHERE datenbankname = '$datenbankname'")){
                  return $datenbank["doc_id"];
                } else {
                  return false;
                }


        }

        function datenbank_get($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["datenbankname"]) && empty($params["datenbank_id"])) {
                        $this->errorMessage .= "Parameters: datenbankname or datenbank_id are required.\r\n";
                        return false;
                }

                // Hole Datenbank ID
                if(empty($params["datenbank_id"])) {
                        $datenbank_id = $this->datenbank_get_id($session,$params);
                } else {
                        $datenbank_id = intval($params["datenbank_id"]);
                }

                if(empty($datenbank_id)) {
                        $this->errorMessage .= "datenbank_id cannot be resolved\r\n";
                        return false;
                }

                $datenbank = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_datenbank WHERE doc_id = $datenbank_id");

                // überprüfe, ob Datenbank gefunden wurde
                if(!is_array($datenbank)) {
                        $this->errorMessage .= "no records found\r\n";
                        return false;
                }

                $datenbank["datenbank_id"] = $datenbank["doc_id"];
                $out = $datenbank;

                return $out;
        }

        function datenbank_add($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_insert"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["web_title"]) && empty($params["web_id"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["db_passwort"])) $this->errorMessage .= "Parameter: db_passwort is required.\r\n";
                if(!isset($params["remote_access"])) $params["remote_access"] = 0;
                if($this->errorMessage != "") return false;

                // Hole Daten zu Parent Record (Web)
                $sql = "SELECT * FROM isp_nodes WHERE doc_id = $web_id and doctype_id = '".$this->web_doctype_id."'";
                $web_record = $go_api->db->queryOneRecord($sql);

                if(empty($web_record["userid"])) {
                        $this->errorMessage .= "web user cannot be resolved.\r\n";
                        return false;
                }

                // Füge Datenbank ein
                $sql = "INSERT INTO isp_isp_datenbank (doctype_id,db_passwort,remote_access) VALUES ('".$this->datenbank_doctype_id."','".addslashes($params["db_passwort"])."','".addslashes($params["remote_access"])."')";
                $go_api->db->query($sql);
                $datenbank_id = $doc_id = $go_api->db->insertID();

                // Node einfügen
                $sql = "INSERT INTO isp_nodes ( userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title
                                ) VALUES (
                                ".intval($web_record["userid"]).",
                                ".intval($web_record["groupid"]).",
                                '',
                                'a',
                                '".$this->datenbank_doctype_id."',
                                '1',
                                '',
                                '',
                                $datenbank_id,
                                ''
                                )";

                $go_api->db->query($sql);
                $datenbank_tree_id = $go_api->db->insertID();

                // DEP-Record einfügen
                $sql = "INSERT INTO isp_dep (userid, groupid, parent_doc_id, parent_doctype_id, parent_tree_id, child_doc_id, child_doctype_id, child_tree_id, status)
                VALUES (
                ".intval($web_record["userid"]).",
                ".intval($web_record["groupid"]).",
                ".intval($web_record["doc_id"]).",
                '".$this->web_doctype_id."',
                ".intval($web_record["tree_id"]).",
                $datenbank_id,
                '".$this->datenbank_doctype_id."',
                $datenbank_tree_id,
                1)";

                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->datenbank_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_insert != "") {
                    $event_insert = $doc->event_insert;
                    $this->errorMessage .= $go_api->$event_class->$event_insert($doc_id,$this->datenbank_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;
                return $datenbank_id;

        }

        function datenbank_update($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["datenbankname"]) && empty($params["datenbank_id"])){
                  $this->errorMessage .= "Parameter: datenbank_id or datenbankname is required.\r\n";
                  return false;
                }

                // Hole Datenbank ID
                if(empty($params["datenbank_id"])) {
                        $datenbank_id = $this->datenbank_get_id($session,$params);
                } else {
                        $datenbank_id = intval($params["datenbank_id"]);
                }

                if(empty($datenbank_id)) {
                        $this->errorMessage .= "datenbank_id cannot be resolved\r\n";
                        return false;
                }

                // checke ob Datenbank schon existiert
                $sql = "SELECT doc_id FROM isp_isp_datenbank where datenbankname = '".addslashes($params["datenbankname"])."' and isp_isp_datenbank.doc_id != $datenbank_id";

                $tmp = $go_api->db->queryOneRecord($sql);
                if($tmp["doc_id"] > 0) {
                        $this->errorMessage .= "Database alredy exists with other datenbank_id.\r\n";
                        return false;
                }

                // Liegt Objekt im Papierkorb?
                if($this->datenbank_is_suspended($session,$params)){
                  $this->errorMessage .= "This database is suspended.\r\n";
                  return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->datenbank_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $fields = $go_api->db->queryAllRecords("SHOW COLUMNS FROM isp_isp_datenbank");
                foreach($fields as $field){
                  $field_arr[] = $field["Field"];
                }

                $changes = "";
                foreach($params as $key => $val){
                  if($key != "datenbank_id" && $key != "doc_id" && $key != "doctype_id" && $key != "datenbankname" && $key != "datenbankuser" && $key != "web_id" && $key != "status" && in_array($key, $field_arr)) $changes .= $key." = '".addslashes($val)."',";
                }
                $changes = substr($changes, 0, -1);

                $sql = "UPDATE isp_isp_datenbank SET ".$changes." WHERE doc_id = ".$datenbank_id;

                if(!$go_api->db->query($sql)) $this->errorMessage  .= "Database could not be updated.\r\n";
                if($this->errorMessage != '') return false;

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->datenbank_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($datenbank_id,$this->datenbank_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $this->datenbank_get($session,$params);

        }

        function datenbank_suspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["datenbank_id"]) && empty($params["datenbankname"])) {
                        $this->errorMessage .= "Parameters: datenbankname or datenbank_id are required.\r\n";
                        return false;
                }

                if(empty($params["datenbank_id"])) {
                        $datenbank_id = $this->datenbank_get_id($session,$params);
                } else {
                        $datenbank_id = intval($params["datenbank_id"]);
                }
                if(empty($datenbank_id)) {
                        $this->errorMessage .= "datenbank_id cannot be resolved\r\n";
                        return false;
                }

                if($this->datenbank_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->datenbank_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // suspend durchführen
                $sql = "UPDATE isp_nodes SET status = 0 WHERE doc_id = $datenbank_id and doctype_id = '".$this->datenbank_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->datenbank_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($datenbank_id,$this->datenbank_doctype_id, 'do', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function datenbank_unsuspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["datenbank_id"]) && empty($params["datenbankname"])) {
                        $this->errorMessage .= "Parameters: datenbankname or datenbank_id are required.\r\n";
                        return false;
                }

                if(empty($params["datenbank_id"])) {
                        $datenbank_id = $this->datenbank_get_id($session,$params);
                } else {
                        $datenbank_id = intval($params["datenbank_id"]);
                }
                if(empty($datenbank_id)) {
                        $this->errorMessage .= "datenbank_id cannot be resolved\r\n";
                        return false;
                }

                if(!$this->datenbank_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->datenbank_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // unsuspend durchführen
                $sql = "UPDATE isp_nodes SET status = 1 WHERE doc_id = $datenbank_id and doctype_id = '".$this->datenbank_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->datenbank_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($datenbank_id,$this->datenbank_doctype_id, 'undo', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function datenbank_delete($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["web_delete"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["datenbank_id"]) && empty($params["datenbankname"])) {
                        $this->errorMessage .= "Parameters: datenbankname or datenbank_id are required.\r\n";
                        return false;
                }

                if(empty($params["datenbank_id"])) {
                        $datenbank_id = $this->datenbank_get_id($session,$params);
                } else {
                        $datenbank_id = intval($params["datenbank_id"]);
                }
                if(empty($datenbank_id)) {
                        $this->errorMessage .= "datenbank_id cannot be resolved\r\n";
                        return false;
                }

                // Ist Datenbank suspended?
                if(!$this->datenbank_is_suspended($session,$params)){
                        $this->errorMessage .= "The record must be suspended before it can be deleted.\r\n";
                        return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->datenbank_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // DELETE durchführen
                $go_api->db->query("DELETE FROM isp_dep WHERE child_doc_id = '".$datenbank_id."' AND child_doctype_id = '".$this->datenbank_doctype_id."' AND parent_doctype_id = '".$this->web_doctype_id."'");
                $sql = "DELETE isp_nodes.*, isp_isp_datenbank.* FROM isp_nodes, isp_isp_datenbank WHERE isp_nodes.doc_id = '$datenbank_id' and isp_nodes.doctype_id = '".$this->datenbank_doctype_id."' and isp_nodes.status != '1' AND isp_nodes.doc_id = isp_isp_datenbank.doc_id AND isp_nodes.doctype_id = isp_isp_datenbank.doctype_id";
                return $go_api->db->query($sql);
        }

        //////////////////////////// Help Functions //////////////////////////////

        function reseller_get_gid($reseller_title){
          global $go_api, $go_info;

          if($reseller_title == "admin") return 1;
          if($reseller = $go_api->db->queryOneRecord("SELECT isp_isp_reseller.reseller_group FROM isp_nodes, isp_isp_reseller WHERE isp_nodes.title = '$reseller_title' AND isp_isp_reseller.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '".$this->reseller_doctype_id."' AND isp_isp_reseller.doctype_id = isp_nodes.doctype_id")){
            return $reseller["reseller_group"];
          } else {
            return false;
          }
        }

        function kunde_get_webadmin_userid($session,$params){
          global $go_api, $go_info;

          if(isset($params["kunde_id"])){
            if($kunde = $go_api->db->queryOneRecord("SELECT webadmin_userid FROM isp_isp_kunde WHERE doc_id = '".$params["kunde_id"]."'")){
              return $kunde["webadmin_userid"];
            } else {
              return false;
            }
          } else {
            if(isset($params["kunde_title"])){
              if($kunde = $go_api->db->queryOneRecord("SELECT isp_isp_kunde.webadmin_userid FROM isp_nodes, isp_isp_kunde WHERE isp_nodes.title = '".$params["kunde_title"]."' AND isp_isp_kunde.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '".$this->kunde_doctype_id."' AND isp_isp_kunde.doctype_id = isp_nodes.doctype_id")){
                return $kunde["webadmin_userid"];
              } else {
                return false;
              }
            } else {
              return false;
            }
          }
        }

        function web_is_suspended($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["web_title"]) && empty($params["web_id"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM isp_nodes WHERE doc_id = '".$web_id."' AND doctype_id = '".$this->web_doctype_id."' AND status != '1'")){
                  return true;
                } else {
                  return false;
                }

        }

        function web_status_empty($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["web_title"]) && empty($params["web_id"])) {
                        $this->errorMessage .= "Parameters: web_title or web_id are required.\r\n";
                        return false;
                }

                // Hole Web ID
                if(empty($params["web_id"])) {
                        $web_id = $this->web_get_id($session,$params);
                } else {
                        $web_id = intval($params["web_id"]);
                }

                if(empty($web_id)) {
                        $this->errorMessage .= "web_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM isp_isp_web WHERE doc_id = '".$web_id."' AND status != ''")){
                  return false;
                } else {
                  return true;
                }

        }

        function user_is_suspended($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["user_username"]) && empty($params["user_id"])) {
                        $this->errorMessage .= "Parameters: user_username or user_id are required.\r\n";
                        return false;
                }

                // Hole User ID
                if(empty($params["user_id"])) {
                        $user_id = $this->user_get_id($session,$params);
                } else {
                        $user_id = intval($params["user_id"]);
                }

                if(empty($user_id)) {
                        $this->errorMessage .= "user_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM isp_nodes WHERE doc_id = '".$user_id."' AND doctype_id = '".$this->user_doctype_id."' AND status != '1'")){
                  return true;
                } else {
                  return false;
                }

        }

        function user_status_empty($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["user_username"]) && empty($params["user_id"])) {
                        $this->errorMessage .= "Parameters: user_username or user_id are required.\r\n";
                        return false;
                }

                // Hole User ID
                if(empty($params["user_id"])) {
                        $user_id = $this->user_get_id($session,$params);
                } else {
                        $user_id = intval($params["user_id"]);
                }

                if(empty($user_id)) {
                        $this->errorMessage .= "user_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM isp_isp_user WHERE doc_id = '".$user_id."' AND status != ''")){
                  return false;
                } else {
                  return true;
                }

        }
        
        function list_is_suspended($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["list_name"]) && empty($params["list_id"])) {
                        $this->errorMessage .= "Parameters: list_name or list_id are required.\r\n";
                        return false;
                }

                // Hole List ID
                if(empty($params["list_id"])) {
                        $list_id = $this->list_get_id($session,$params);
                } else {
                        $list_id = intval($params["list_id"]);
                }

                if(empty($list_id)) {
                        $this->errorMessage .= "list_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM isp_nodes WHERE doc_id = '".$list_id."' AND doctype_id = '".$this->list_doctype_id."' AND status != '1'")){
                  return true;
                } else {
                  return false;
                }

        }

        function list_status_empty($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["list_name"]) && empty($params["list_id"])) {
                        $this->errorMessage .= "Parameters: list_name or list_id are required.\r\n";
                        return false;
                }

                // Hole User ID
                if(empty($params["list_id"])) {
                        $list_id = $this->list_get_id($session,$params);
                } else {
                        $list_id = intval($params["list_id"]);
                }

                if(empty($list_id)) {
                        $this->errorMessage .= "list_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM isp_isp_list WHERE doc_id = '".$list_id."' AND status != ''")){
                  return false;
                } else {
                  return true;
                }

        }
        
        function datenbank_is_suspended($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["datenbankname"]) && empty($params["datenbank_id"])) {
                        $this->errorMessage .= "Parameters: datenbankname or datenbank_id are required.\r\n";
                        return false;
                }

                // Hole Datenbank ID
                if(empty($params["datenbank_id"])) {
                        $datenbank_id = $this->datenbank_get_id($session,$params);
                } else {
                        $datenbank_id = intval($params["datenbank_id"]);
                }

                if(empty($datenbank_id)) {
                        $this->errorMessage .= "datenbank_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM isp_nodes WHERE doc_id = '".$datenbank_id."' AND doctype_id = '".$this->datenbank_doctype_id."' AND status != '1'")){
                  return true;
                } else {
                  return false;
                }

        }

        function datenbank_status_empty($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["datenbankname"]) && empty($params["datenbank_id"])) {
                        $this->errorMessage .= "Parameters: datenbankname or datenbank_id are required.\r\n";
                        return false;
                }

                // Hole Datenbank ID
                if(empty($params["datenbank_id"])) {
                        $datenbank_id = $this->datenbank_get_id($session,$params);
                } else {
                        $datenbank_id = intval($params["datenbank_id"]);
                }

                if(empty($datenbank_id)) {
                        $this->errorMessage .= "datenbank_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM isp_isp_datenbank WHERE doc_id = '".$datenbank_id."' AND status != ''")){
                  return false;
                } else {
                  return true;
                }

        }

}
?>