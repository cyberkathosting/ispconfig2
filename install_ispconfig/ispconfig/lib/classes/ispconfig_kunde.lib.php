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

class kunde {

        var $errorMessage = '';
        var $kunde_doctype_id = 1012;
        var $web_doctype_id = 1013;
        var $reseller_doctype_id = 1022;

        /*
        #############################################################
        # Kunden
        #############################################################
        */

        /*
        Function: kunde_list
        params:   leer
        return:   Array with Kunden Arrays
        */

        function kunde_list($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["kunde_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                if(!isset($params["reseller_title"]) || $params["reseller_title"] == "all"){
                  // hole Kunde
                  $kunden = $go_api->db->queryAllRecords("SELECT isp_nodes.title AS kunde_title, isp_isp_kunde.* FROM isp_isp_kunde, isp_nodes WHERE isp_isp_kunde.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '".$this->kunde_doctype_id."' AND isp_nodes.doctype_id = isp_isp_kunde.doctype_id ORDER BY isp_nodes.title, isp_isp_kunde.kunde_name");
                } else {
                  if($params["reseller_title"] == "admin"){
                    // hole Kunde
                    $kunden = $go_api->db->queryAllRecords("SELECT isp_nodes.title AS kunde_title, isp_isp_kunde.* FROM isp_isp_kunde, isp_nodes WHERE isp_isp_kunde.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '".$this->kunde_doctype_id."' AND isp_nodes.doctype_id = isp_isp_kunde.doctype_id AND isp_nodes.groupid = '1' ORDER BY isp_nodes.title, isp_isp_kunde.kunde_name");
                  } else {
                    if($reseller_group = $this->reseller_get_gid($params["reseller_title"])){
                      // hole Kunde
                      $kunden = $go_api->db->queryAllRecords("SELECT isp_nodes.title AS kunde_title, isp_isp_kunde.* FROM isp_isp_kunde, isp_nodes WHERE isp_isp_kunde.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '".$this->kunde_doctype_id."' AND isp_nodes.doctype_id = isp_isp_kunde.doctype_id AND isp_nodes.groupid = $reseller_group ORDER BY isp_nodes.title, isp_isp_kunde.kunde_name");
                    } else {
                      $this->errorMessage .= "cannot find reseller\r\n";
                      return false;
                    }
                  }
                }

                if(!empty($kunden)){
                  foreach($kunden as $kunde) {
                        $kunde["kunde_id"] = $kunde["doc_id"];
                        $out["kunde".$kunde["doc_id"]] = $kunde;

                  }
                  return $out;
                } else {
                  $this->errorMessage .= "no records found\r\n";
                  return false;
                }

        }

        /*
        Function: kunde_get
        Params: kunde_id oder kunde_title
        Return: kunde Array
        */

        function kunde_get($session,$params) {
                global $go_api, $go_info;


                // überprüfe Rechte
                if($session["user"]["kunde_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["kunde_id"]) && empty($params["kunde_title"])) {
                        $this->errorMessage .= "Parameters: kunde_title or kunde_id are required.\r\n";
                        return false;
                }

                // Hole Kunde ID
                if(empty($params["kunde_id"])) {
                        $kunde_id = $this->kunde_get_id($session,$params);
                } else {
                        $kunde_id = intval($params["kunde_id"]);
                }

                if(empty($kunde_id)) {
                        $this->errorMessage .= "kunde_id cannot be resolved\r\n";
                        return false;
                }

                // Hole Kunde
                $kunde = $go_api->db->queryOneRecord("SELECT isp_nodes.title AS kunde_title, isp_isp_kunde.* FROM isp_nodes, isp_isp_kunde WHERE isp_isp_kunde.doc_id = $kunde_id AND isp_nodes.doc_id = isp_isp_kunde.doc_id AND isp_nodes.doctype_id = '".$this->kunde_doctype_id."' AND isp_nodes.doctype_id = isp_isp_kunde.doctype_id");

                // überprüfe ob Kunde gefunden wurde
                if(!is_array($kunde)) {
                        $this->errorMessage .= "no records found\r\n";
                        return false;
                }

                $kunde["kunde_id"] = $kunde["doc_id"];
                $out = $kunde;

                return $out;


        }

        /*
        Function: kunde_get_id
        Params: kunde_title
        Return: kunde_id
        */

        function kunde_get_id($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["kunde_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["kunde_title"])) {
                        $this->errorMessage .= "Parameters: kunde_title is required.\r\n";
                        return false;
                }

                // Hole Kunde ID
                $kunde_title = addslashes($params["kunde_title"]);

                if($kunde = $go_api->db->queryOneRecord("SELECT isp_isp_kunde.doc_id FROM isp_nodes, isp_isp_kunde WHERE isp_nodes.title = '$kunde_title' AND isp_isp_kunde.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '".$this->kunde_doctype_id."' AND isp_isp_kunde.doctype_id = isp_nodes.doctype_id")){
                  return $kunde["doc_id"];
                } else {
                  return false;
                }
        }

        /*
        Function: kunde_add
        Params:
        Return: kunde_id
        */

        function kunde_add($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["kunde_insert"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["reseller_title"]) && empty($params["reseller_group"]))                 $this->errorMessage         .= "Parameter: reseller_title or reseller_group is required.\r\n";
                if(empty($params["kunde_title"]))                 $this->errorMessage         .= "Parameter: kunde_title is required.\r\n";
                if(empty($params["kunde_firma"]))                 $this->errorMessage         .= "Parameter: kunde_firma is required.\r\n";
                if(empty($params["kunde_anrede"]))         $this->errorMessage         .= "Parameter: kunde_anrede is required.\r\n";
                if(empty($params["kunde_vorname"]))         $this->errorMessage         .= "Parameter: kunde_vorname is required.\r\n";
                if(empty($params["kunde_name"]))         $this->errorMessage         .= "Parameter: kunde_name is required.\r\n";
                if(empty($params["kunde_strasse"]))         $this->errorMessage         .= "Parameter: kunde_strasse is required.\r\n";
                if(empty($params["kunde_plz"]))         $this->errorMessage         .= "Parameter: kunde_plz is required.\r\n";
                if(empty($params["kunde_ort"]))         $this->errorMessage         .= "Parameter: kunde_ort is required.\r\n";
                if(empty($params["kunde_province"]))         $this->errorMessage         .= "Parameter: kunde_province is required.\r\n";
                if(empty($params["kunde_land"]))         $this->errorMessage         .= "Parameter: kunde_land is required.\r\n";
                if(empty($params["kunde_telefon"]))         $this->errorMessage         .= "Parameter: kunde_telefon is required.\r\n";
                if(empty($params["webadmin_user"]))         $this->errorMessage         .= "Parameter: webadmin_user is required.\r\n";
                if(empty($params["webadmin_passwort"]))         $this->errorMessage         .= "Parameter: webadmin_passwort is required.\r\n";

                if($this->errorMessage != '') return false;

                // Hole Reseller Group
                if(empty($params["reseller_group"])) {
                        $reseller_group = $this->reseller_get_gid($params["reseller_title"]);
                } else {
                        $reseller_group = intval($params["reseller_group"]);
                }

                if(empty($reseller_group)) {
                        $this->errorMessage .= "reseller_group cannot be resolved\r\n";
                        return false;
                }

                $tree_id = $go_api->db->queryOneRecord("SELECT tree_id FROM isp_nodes WHERE groupid = '".$reseller_group."' AND parent = 'group".$reseller_group."' AND type = 'n' AND doctype_id = '0' AND doc_id = '0' AND title = 'Kunden'");
                $tree_id = $tree_id["tree_id"];
                if(empty($tree_id)){
                  $this->errorMessage .= "tree_id cannot be resolved\r\n";
                  return false;
                }

                // checke ob Kunde bereits existiert
                $kunde_id = $this->kunde_get_id($session,$params);
                if(!empty($kunde_id)) {
                        $this->errorMessage .= "Client: ".$params["kunde_title"]." already exists\r\n";
                        return false;
                }

                // Record einfügen
                $sql = "INSERT INTO isp_isp_kunde (
                                doctype_id,
                                kunde_anrede,
                                kunde_firma,
                                kunde_vorname,
                                kunde_name,
                                kunde_strasse,
                                kunde_plz,
                                kunde_ort,
                                kunde_land,
                                kunde_telefon,
                                kunde_fax,
                                kunde_email,
                                kunde_internet,
                                webadmin_user,
                                webadmin_passwort,
                                rechnung_firma,
                                rechnung_vorname,
                                rechnung_name,
                                rechnung_strasse,
                                rechnung_plz,
                                rechnung_ort,
                                rechnung_land,
                                rechnung_intervall,
                                rechnung_preis,
                                rechnung_zahlungsbedingungen,
                                kunde_province
                                ) VALUES (
                                '".$this->kunde_doctype_id."',
                                '".addslashes($params["kunde_anrede"])."',
                                '".addslashes($params["kunde_firma"])."',
                                '".addslashes($params["kunde_vorname"])."',
                                '".addslashes($params["kunde_name"])."',
                                '".addslashes($params["kunde_strasse"])."',
                                '".addslashes($params["kunde_plz"])."',
                                '".addslashes($params["kunde_ort"])."',
                                '".addslashes($params["kunde_land"])."',
                                '".addslashes($params["kunde_telefon"])."',
                                '".addslashes($params["kunde_fax"])."',
                                '".addslashes($params["kunde_email"])."',
                                '".addslashes($params["kunde_internet"])."',
                                '".addslashes($params["webadmin_user"])."',
                                '".addslashes($params["webadmin_passwort"])."',
                                '".addslashes($params["rechnung_firma"])."',
                                '".addslashes($params["rechnung_vorname"])."',
                                '".addslashes($params["rechnung_name"])."',
                                '".addslashes($params["rechnung_strasse"])."',
                                '".addslashes($params["rechnung_plz"])."',
                                '".addslashes($params["rechnung_ort"])."',
                                '".addslashes($params["rechnung_land"])."',
                                '".addslashes($params["rechnung_intervall"])."',
                                '".addslashes($params["rechnung_preis"])."',
                                '".addslashes($params["rechnung_zahlungsbedingungen"])."',
                                '".addslashes($params["kunde_province"])."'
                                )";

                $go_api->db->query($sql);
                $kunde_id = $doc_id = $go_api->db->insertID();

                // Node einfügen
                $sql = "INSERT INTO isp_nodes (
                                userid,
                                groupid,
                                parent,
                                type,
                                doctype_id,
                                status,
                                icon,
                                modul,
                                doc_id,
                                title
                                ) VALUES (
                                1,
                                '".$reseller_group."',
                                '".$tree_id."',
                                'i',
                                ".$this->kunde_doctype_id.",
                                '1',
                                'kunde.gif',
                                '',
                                '".$doc_id."',
                                '".addslashes($params["kunde_title"])."'
                                )";

                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->kunde_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_insert != "") {
                    $event_insert = $doc->event_insert;
                    $this->errorMessage .= $go_api->$event_class->$event_insert($doc_id,$this->kunde_doctype_id, 0);
                  }

                  // Usernamen neu setzen, da er durch die Insert-Routine fest vorgegeben wird
                  $go_api->db->query("UPDATE isp_isp_kunde SET webadmin_user = '".addslashes($params["webadmin_user"])."' WHERE doc_id = '".$doc_id."'");

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($doc_id,$this->kunde_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;
                return $kunde_id;
        }

        function kunde_update($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["kunde_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["kunde_id"]) && empty($params["kunde_title"]))         $this->errorMessage         .= "Parameter: kunde_id or kunde_title is required.\r\n";
                if($this->errorMessage != "") return false;

                // Hole Kunde ID
                if(empty($params["kunde_id"])) {
                        $kunde_id = $this->kunde_get_id($session,$params);
                } else {
                        $kunde_id = intval($params["kunde_id"]);
                }

                if(empty($kunde_id)) {
                        $this->errorMessage .= "kunde_id cannot be resolved\r\n";
                        return false;
                }

                // Liegt Objekt im Papierkorb?
                if($this->kunde_is_suspended($session,$params)){
                  $this->errorMessage .= "Client is suspended.\r\n";
                  return false;
                }

                $fields = $go_api->db->queryAllRecords("SHOW COLUMNS FROM isp_isp_kunde");
                foreach($fields as $field){
                  $field_arr[] = $field["Field"];
                }

                $changes = "";
                foreach($params as $key => $val){
                  if($key != "kunde_id" && $key != "kunde_title" && $key != "webadmin_userid" && $key != "doc_id" && $key != "doctype_id" && in_array($key, $field_arr)) $changes .= $key." = '".addslashes($val)."',";
                }
                $changes = substr($changes, 0, -1);

                $sql = "UPDATE isp_isp_kunde SET ".$changes." WHERE doc_id = ".$kunde_id;

                if(!$go_api->db->query($sql)) $this->errorMessage  .= "Database could not be updated.\r\n";
                if($this->errorMessage != '') return false;

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->kunde_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($kunde_id,$this->kunde_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $this->kunde_get($session,$params);
        }

        function kunde_suspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["kunde_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["kunde_title"]) && empty($params["kunde_id"])) {
                        $this->errorMessage .= "Parameters: kunde_title or kunde_id are required.\r\n";
                        return false;
                }

                // Hole Kunde ID
                if(empty($params["kunde_id"])) {
                        $kunde_id = $this->kunde_get_id($session,$params);
                } else {
                        $kunde_id = intval($params["kunde_id"]);
                }

                if(empty($kunde_id)) {
                        $this->errorMessage .= "kunde_id cannot be resolved\r\n";
                        return false;
                }

                if($this->kunde_is_suspended($session,$params)) return true;

                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                // alle zugehörigen Records in den Papierkorb verschieben
                $sql = "SELECT child_doc_id, child_doctype_id FROM isp_dep WHERE parent_doc_id = $kunde_id";
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

                       ######################
                       $sql = "SELECT child_doc_id, child_doctype_id FROM isp_dep WHERE parent_doc_id = $child[child_doc_id]";
                       $childs2 = $go_api->db->queryAllRecords($sql);
                       foreach($childs2 as $child2) {
                         $sql = "UPDATE isp_nodes SET status = 0 where doc_id = $child2[child_doc_id] and doctype_id = $child2[child_doctype_id]";
                         $go_api->db->query($sql);
                         #################
                         // Dokumententyp holen
                         $doc = $go_api->doc->doctype_get($child2[child_doctype_id]);

                         // on Delete Event ausführen
                         if($doc->event_class != "") {
                           $event_class = $doc->event_class;
                           if(!class_exists($event_class)){
                             $go_api->uses($doc->event_class);
                           }
                           if($doc->event_delete != "") {
                             $event_delete = $doc->event_delete;
                             $this->errorMessage .= $go_api->$event_class->$event_delete($child2[child_doc_id],$child2[child_doctype_id],'do', 0);
                          }
                        }
                        ######################

                }
                       ######################
                }

                // suspend durchführen
                $sql = "UPDATE isp_nodes SET status = 0 WHERE doc_id = $kunde_id and doctype_id = '".$this->kunde_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////

                if(!$doc = $go_api->doc->doctype_get($this->kunde_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($kunde_id,$this->kunde_doctype_id, 'do', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function kunde_unsuspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["kunde_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["kunde_title"]) && empty($params["kunde_id"])) {
                        $this->errorMessage .= "Parameters: kunde_title or kunde_id are required.\r\n";
                        return false;
                }

                // Hole Kunde ID
                if(empty($params["kunde_id"])) {
                        $kunde_id = $this->kunde_get_id($session,$params);
                } else {
                        $kunde_id = intval($params["kunde_id"]);
                }

                if(empty($kunde_id)) {
                        $this->errorMessage .= "kunde_id cannot be resolved\r\n";
                        return false;
                }

                if(!$this->kunde_is_suspended($session,$params)) return true;

                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                // unsuspend durchführen
                $sql = "UPDATE isp_nodes SET status = 1 WHERE doc_id = $kunde_id and doctype_id = '".$this->kunde_doctype_id."'";
                $go_api->db->query($sql);

                // alle zugehörigen Records aus dem Papierkorb holen
                $sql = "SELECT child_doc_id, child_doctype_id FROM isp_dep WHERE parent_doc_id = $kunde_id";
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

                       ######################
                       $sql = "SELECT child_doc_id, child_doctype_id FROM isp_dep WHERE parent_doc_id = $child[child_doc_id]";
                       $childs2 = $go_api->db->queryAllRecords($sql);
                       foreach($childs2 as $child2) {
                         $sql = "UPDATE isp_nodes SET status = 1 where doc_id = $child2[child_doc_id] and doctype_id = $child2[child_doctype_id]";
                         $go_api->db->query($sql);
                         #################
                         // Dokumententyp holen
                         $doc = $go_api->doc->doctype_get($child2[child_doctype_id]);

                         // on Delete Event ausführen
                         if($doc->event_class != "") {
                           $event_class = $doc->event_class;
                           if(!class_exists($event_class)){
                             $go_api->uses($doc->event_class);
                           }
                           if($doc->event_delete != "") {
                             $event_delete = $doc->event_delete;
                             $this->errorMessage .= $go_api->$event_class->$event_delete($child2[child_doc_id],$child2[child_doctype_id],'undo', 0);
                          }
                        }
                        ######################

                }
                       ######################
                }

                /////////////////////////

                if(!$doc = $go_api->doc->doctype_get($this->kunde_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($kunde_id,$this->kunde_doctype_id, 'undo', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function kunde_delete($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["kunde_delete"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["kunde_title"]) && empty($params["kunde_id"])) {
                        $this->errorMessage .= "Parameters: kunde_title or kunde_id are required.\r\n";
                        return false;
                }

                // Hole Kunde ID
                if(empty($params["kunde_id"])) {
                        $kunde_id = $this->kunde_get_id($session,$params);
                } else {
                        $kunde_id = intval($params["kunde_id"]);
                }

                if(empty($kunde_id)) {
                        $this->errorMessage .= "kunde_id cannot be resolved\r\n";
                        return false;
                }

                // Ist Kunde suspended?
                if(!$this->kunde_is_suspended($session,$params)) $this->kunde_suspend($session,$params);

                // DELETE durchführen
                //$sql = "DELETE isp_nodes.*, isp_isp_kunde.* FROM isp_nodes, isp_isp_kunde WHERE isp_nodes.doc_id = '$kunde_id' and isp_nodes.doctype_id = '".$this->kunde_doctype_id."' and isp_nodes.status != '1' AND isp_nodes.doc_id = isp_isp_kunde.doc_id AND isp_nodes.doctype_id = isp_isp_kunde.doctype_id";
                //return $go_api->db->query($sql);

                ##############################
                $go_api->uses('doc,auth,log,isp');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                $groupid = $go_api->db->queryOneRecord("SELECT groupid FROM isp_nodes WHERE doc_id = '".$kunde_id."' AND doctype_id = '".$this->kunde_doctype_id."'");
                $groupid = $groupid["groupid"];
                $sql = "SELECT * FROM isp_nodes where status = 0 and groupid = $groupid";
                $sql = "SELECT DISTINCT isp_nodes.* FROM isp_nodes, isp_dep where (isp_nodes.status = 0 and isp_nodes.groupid = $groupid AND isp_dep.groupid = isp_nodes.groupid AND isp_dep.parent_doctype_id = '".$this->kunde_doctype_id."' AND isp_dep.parent_doc_id = '".$kunde_id."' AND isp_dep.child_tree_id = isp_nodes.tree_id) OR (isp_nodes.doc_id = '".$kunde_id."' AND isp_nodes.doctype_id = '".$this->kunde_doctype_id."' AND isp_dep.parent_doc_id = '".$kunde_id."' AND isp_dep.parent_doctype_id = '".$this->kunde_doctype_id."' AND isp_nodes.tree_id = isp_dep.parent_tree_id)";
                $items = $tmp_items1 = $go_api->db->queryAllRecords($sql);

                if(!empty($tmp_items1)){
                  foreach($tmp_items1 as $tmp_item){
                    if($tmp_item["doctype_id"] == $this->web_doctype_id){
                      $sql = "SELECT DISTINCT isp_nodes.* FROM isp_nodes, isp_dep where (isp_nodes.status = 0 and isp_nodes.groupid = $groupid AND isp_dep.groupid = isp_nodes.groupid AND isp_dep.parent_doctype_id = '".$this->web_doctype_id."' AND isp_dep.parent_doc_id = '".$tmp_item["doc_id"]."' AND isp_dep.child_tree_id = isp_nodes.tree_id)";
                      $tmp_items2 = $go_api->db->queryAllRecords($sql);
                      $items = array_merge($items, $tmp_items2);
                    }
                  }
                }

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

        //////////////////////////// Help Functions //////////////////////////////

        function kunde_is_suspended($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["kunde_title"]) && empty($params["kunde_id"])) {
                        $this->errorMessage .= "Parameters: kunde_title or kunde_id are required.\r\n";
                        return false;
                }

                // Hole Kunde ID
                if(empty($params["kunde_id"])) {
                        $kunde_id = $this->kunde_get_id($session,$params);
                } else {
                        $kunde_id = intval($params["kunde_id"]);
                }

                if(empty($kunde_id)) {
                        $this->errorMessage .= "kunde_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM isp_nodes WHERE doc_id = '".$kunde_id."' AND doctype_id = '".$this->kunde_doctype_id."' AND status != '1'")){
                  return true;
                } else {
                  return false;
                }

        }

        function reseller_get_gid($reseller_title){
          global $go_api, $go_info;

          if($reseller_title == "admin") return 1;
          if($reseller = $go_api->db->queryOneRecord("SELECT isp_isp_reseller.reseller_group FROM isp_nodes, isp_isp_reseller WHERE isp_nodes.title = '$reseller_title' AND isp_isp_reseller.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '".$this->reseller_doctype_id."' AND isp_isp_reseller.doctype_id = isp_nodes.doctype_id")){
            return $reseller["reseller_group"];
          } else {
            return false;
          }
        }

}
?>