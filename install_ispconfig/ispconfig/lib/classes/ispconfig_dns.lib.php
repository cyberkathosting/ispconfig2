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

class dns {

        var $errorMessage = '';
        var $zone_doctype_id = 1016;
        var $a_doctype_id = 1018;
        var $cname_doctype_id = 1019;
        var $mx_doctype_id = 1020;
        var $reseller_doctype_id = 1022;

        /*
        #############################################################
        # Zones
        #############################################################
        */

        /*
        Function: zone_list
        params:   leer
        return:   Array with Zone Arrays
        */

        function zone_list($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                if(!isset($params["reseller_title"]) || $params["reseller_title"] == "all"){
                  // hole Zonen
                  $zones = $go_api->db->queryAllRecords("SELECT * from dns_isp_dns WHERE 1 ORDER BY dns_soa_ip");
                } else {
                  if($params["reseller_title"] == "admin"){
                    // hole Zonen
                    $zones = $go_api->db->queryAllRecords("SELECT dns_isp_dns.* FROM dns_isp_dns, dns_nodes WHERE dns_nodes.groupid = 1 AND dns_nodes.doctype_id = '".$this->zone_doctype_id."' AND dns_nodes.doc_id = dns_isp_dns.doc_id AND dns_nodes.doctype_id = dns_isp_dns.doctype_id ORDER BY dns_isp_dns.dns_soa_ip");
                  } else {
                    if($reseller_group = $this->reseller_get_gid($params["reseller_title"])){
                      // hole Zonen
                      $zones = $go_api->db->queryAllRecords("SELECT dns_isp_dns.* FROM dns_isp_dns, dns_nodes WHERE dns_nodes.groupid = $reseller_group AND dns_nodes.doctype_id = '".$this->zone_doctype_id."' AND dns_nodes.doc_id = dns_isp_dns.doc_id AND dns_nodes.doctype_id = dns_isp_dns.doctype_id ORDER BY dns_isp_dns.dns_soa_ip");
                    } else {
                      $this->errorMessage .= "cannot find reseller\r\n";
                      return false;
                    }
                  }
                }

                if(!empty($zones)){
                  foreach($zones as $zone) {
                        $soa = $zone["dns_soa"];
                        $zone["zone_id"] = $zone["doc_id"];
                        $out[$soa] = $zone;
                  }
                  return $out;
                } else {
                  $this->errorMessage .= "no records found\r\n";
                  return false;
                }
        }

        /*
        Function: zone_get
        Params: zone_id oder zone
        Return: zone Array
        */

        function zone_get($session,$params) {
                global $go_api, $go_info;


                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // Hole Zone
                $zone         = $go_api->db->queryOneRecord("SELECT * FROM dns_isp_dns WHERE doc_id = $zone_id");

                // überprüfe ob Zone gefunden wurde
                if(!is_array($zone)) {
                        $this->errorMessage .= "no records found\r\n";
                        return false;
                }

                $zone["zone_id"] = $zone["doc_id"];
                $out = $zone;

                return $out;


        }

        /*
        Function: zone_get_id
        Params: zone
        Return: zone_id
        */

        function zone_get_id($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"])) {
                        $this->errorMessage .= "Parameters: dns_soa is required.\r\n";
                        return false;
                }

                // Hole Zone ID
                $zone_name = addslashes($params["dns_soa"]);
                $zone = $go_api->db->queryOneRecord("SELECT * FROM dns_isp_dns WHERE dns_soa = '$zone_name'");
                return $zone["doc_id"];


        }

        /*
        Function: zone_add
        Params:
        Return: zone_id
        */

        function zone_add($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_insert"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["reseller_title"]) && empty($params["reseller_group"])) $this->errorMessage .= "Parameter: reseller_title or reseller_group is required.\r\n";
                if(empty($params["dns_soa"]))                 $this->errorMessage         .= "Parameter: dns_soa is required.\r\n";
                if(empty($params["dns_soa_ip"]))                 $this->errorMessage         .= "Parameter: dns_soa_ip is required.\r\n";
                if(empty($params["dns_refresh"]))         $params["dns_refresh"]                  = 28800;
                if(empty($params["dns_retry"]))         $params["dns_retry"]                  = 7200;
                if(empty($params["dns_expire"]))         $params["dns_expire"]                  = 604800;
                if(empty($params["dns_ttl"]))                 $params["dns_ttl"]                         = 86400;
                if(empty($params["dns_ns1"]))                 $this->errorMessage         .= "Parameter: dns_ns1 is required.\r\n";
                if(empty($params["dns_ns2"]))                 $params["dns_ns2"] = $params["ns1"];
                if(empty($params["dns_adminmail"]))         $this->errorMessage         .= "Parameter: dns_adminmail is required.\r\n";

                if($this->errorMessage != '') return false;
                if(empty($params["reseller_group"])){
                  if(!$reseller_group = $this->reseller_get_gid($params["reseller_title"])){
                    $this->errorMessage         .= "Cannot find reseller.\r\n";
                    return false;
                  }
                } else {
                  $reseller_group = intval($params["reseller_group"]);
                }

                // checke ob Zone bereits existiert
                $zone_id = $this->zone_get_id($session,$params);
                if(!empty($zone_id)) {
                        $this->errorMessage .= "Zone: ".$params["dns_soa"]." already exists\r\n";
                        return false;
                }

                // Record einfügen
                $sql = "INSERT INTO dns_isp_dns (
                                doctype_id,
                                dns_soa,
                                dns_refresh,
                                dns_retry,
                                dns_expire,
                                dns_ttl,
                                dns_ns1,
                                dns_ns2,
                                dns_adminmail,
                                server_id,
                                status,
                                dns_soa_ip
                                ) VALUES (
                                '".$this->zone_doctype_id."',
                                '".addslashes($params["dns_soa"])."',
                                '".intval($params["dns_refresh"])."',
                                '".intval($params["dns_retry"])."',
                                '".intval($params["dns_expire"])."',
                                '".intval($params["dns_ttl"])."',
                                '".addslashes($params["dns_ns1"])."',
                                '".addslashes($params["dns_ns2"])."',
                                '".addslashes($params["dns_adminmail"])."',
                                '1',
                                'n',
                                '".addslashes($params["dns_soa_ip"])."'
                                )";

                $go_api->db->query($sql);
                $zone_id = $doc_id = $go_api->db->insertID();

                // Node einfügen
                $sql = "INSERT INTO dns_nodes (
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
                                '1',
                                '".$reseller_group."',
                                'group".$reseller_group."',
                                'i',
                                '".$this->zone_doctype_id."',
                                '1',
                                '',
                                '',
                                $zone_id,
                                '".addslashes($params["dns_soa"])."'
                                )";

                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->zone_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_insert != "") {
                    $event_insert = $doc->event_insert;
                    $this->errorMessage .= $go_api->$event_class->$event_insert($doc_id,$this->zone_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $zone_id;
        }

        function zone_update($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["zone_id"]) && empty($params["dns_soa"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }
                if($this->errorMessage != '') return false;

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // Liegt Objekt im Papierkorb?
                if($this->zone_is_suspended($session,$params)){
                  $this->errorMessage .= "Zone is suspended.\r\n";
                  return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $fields = $go_api->db->queryAllRecords("SHOW COLUMNS FROM dns_isp_dns");
                foreach($fields as $field){
                  $field_arr[] = $field["Field"];
                }

                $changes = "";
                foreach($params as $key => $val){
                  if($key != "zone_id" && $key != "doc_id" && $key != "doctype_id" && $key != "status" && $key != "dns_soa" && $key != "server_id" && in_array($key, $field_arr)) $changes .= $key." = '".addslashes($val)."',";
                }
                $changes = substr($changes, 0, -1);

                $sql = "UPDATE dns_isp_dns SET ".$changes." WHERE doc_id = ".$zone_id;

                if(!$go_api->db->query($sql)) $this->errorMessage  .= "Database could not be updated.\r\n";
                if($this->errorMessage != '') return false;

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->zone_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($zone_id,$this->zone_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $this->zone_get($session,$params);
        }

        function zone_suspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                if($this->zone_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                // alle zugehörigen Records in den Papierkorb verschieben
                $sql = "SELECT child_doc_id, child_doctype_id FROM dns_dep WHERE parent_doc_id = $zone_id";
                $childs = $go_api->db->queryAllRecords($sql);
                foreach($childs as $child) {
                        $sql = "UPDATE dns_nodes SET status = 0 where doc_id = $child[child_doc_id] and doctype_id = $child[child_doctype_id]";
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
                $sql = "UPDATE dns_nodes SET status = 0 WHERE doc_id = $zone_id and doctype_id = 1016";
                $go_api->db->query($sql);

                /////////////////////////
                if(!$doc = $go_api->doc->doctype_get($this->zone_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($zone_id,$this->zone_doctype_id, 'do', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;

        }

        function zone_unsuspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                if(!$this->zone_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                // unsuspend durchführen
                $sql = "UPDATE dns_nodes SET status = 1 WHERE doc_id = $zone_id and doctype_id = 1016";
                $go_api->db->query($sql);

                // alle zugehörigen Records aus dem Papierkorb holen
                $sql = "SELECT child_doc_id, child_doctype_id FROM dns_dep WHERE parent_doc_id = $zone_id";
                $childs = $go_api->db->queryAllRecords($sql);
                foreach($childs as $child) {
                        $sql = "UPDATE dns_nodes SET status = 1 where doc_id = $child[child_doc_id] and doctype_id = $child[child_doctype_id]";
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
                if(!$doc = $go_api->doc->doctype_get($this->zone_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($zone_id,$this->zone_doctype_id, 'undo', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function zone_delete($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_delete"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // Ist Zone suspended?
                if(!$this->zone_is_suspended($session,$params)){
                        $this->errorMessage .= "The record must be suspended before it can be deleted.\r\n";
                        return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // DELETE durchführen
                //$sql = "DELETE FROM dns_nodes WHERE doc_id = $zone_id and doctype_id = 1016 and status != 1";
                //$go_api->db->query($sql);

                //$sql = "DELETE FROM dns_isp_dns WHERE doc_id = $zone_id and status = ''";
                //$go_api->db->query($sql);

                //return true;

                ##############################
                $go_api->uses('doc,auth,log,isp');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                $groupid = $go_api->db->queryOneRecord("SELECT groupid FROM dns_nodes WHERE doc_id = '".$zone_id."' AND doctype_id = '".$this->zone_doctype_id."'");
                $groupid = $groupid["groupid"];
                $sql = "SELECT DISTINCT dns_nodes.* FROM dns_nodes, dns_dep where (dns_nodes.status = 0 and dns_nodes.groupid = $groupid AND dns_dep.groupid = dns_nodes.groupid AND dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' AND dns_dep.parent_doc_id = '".$zone_id."' AND dns_dep.child_tree_id = dns_nodes.tree_id) OR (dns_nodes.doc_id = '".$zone_id."' AND dns_nodes.doctype_id = '".$this->zone_doctype_id."' AND dns_dep.parent_doc_id = '".$zone_id."' AND dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' AND dns_nodes.tree_id = dns_dep.parent_tree_id)";
                $items = $go_api->db->queryAllRecords($sql);


                foreach ($items as $key => $value) {
                  $row = $go_api->db->queryOneRecord("SELECT doctype_modul,doctype_name from doctype where doctype_id = ".$value["doctype_id"]);

                  if($value["type"] == 'i' or $value["type"] == 'a') {

                    $stat = $go_api->db->queryOneRecord("SELECT status from ".$row["doctype_modul"]."_".$row["doctype_name"]." where doc_id = ".$value["doc_id"]);

                      if($stat["status"] == "" or !isset($stat["status"]) or empty($stat["status"])) {

                        // Löschen des Tree eintrages
                        $go_api->db->query("DELETE from dns_nodes where status = 0 and tree_id = ".$value["tree_id"]);
                        // löschen der Daten Einträge
                        $go_api->db->query("DELETE from ".$row["doctype_modul"]."_".$row["doctype_name"]." where doc_id = ".$value["doc_id"]);
                        // löschen der abhängigkeiten
                        $go_api->db->query("DELETE from dns_dep where parent_tree_id = ".$value["tree_id"]." or child_tree_id = ".$value["tree_id"]);
                        // löschen angehängte Termine
                        $go_api->db->query("DELETE from termin_event where parent_doc_id = ".$value["doc_id"]." and parent_doctype_id = ".$value["doctype_id"]);
                        //löschen abhängigkeiten der angehängte Files
                        $go_api->db->query("UPDATE file_nodes SET parent_doc_id = 0, parent_doctype_id = 0 where parent_doc_id = ".$value["doc_id"]." and parent_doctype_id = ".$value["doctype_id"]);
                      }
                    } else {
                      // Löschen des Tree eintrages
                      $go_api->db->query("DELETE from dns_nodes where status = 0 and tree_id = ".$value["tree_id"]);
                    }
                  }

            // Löschen der Faktura Daten
            $go_api->db->query("DELETE from isp_fakt_record where status = 0");

            return true;
            ################################
        }

        /*
        #############################################################
        # A Records
        #############################################################
        */

        /*
        Function: a_list
        Params: zone_id oder zone
        Return: list of a-records Array
        */

        function a_list($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                $sql = "SELECT dns_a.* FROM dns_dep, dns_a WHERE dns_dep.parent_doc_id = $zone_id AND dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' and dns_dep.child_doc_id = dns_a.doc_id and dns_dep.child_doctype_id = '".$this->a_doctype_id."'";

                // hole A-Records
                $as = $go_api->db->queryAllRecords($sql);

                if(!empty($as)){
                  foreach($as as $a) {
                        $a["a_id"] = $a["doc_id"];
                        $out[$a["host"]] = $a;

                  }
                  return $out;
                } else {
                  $this->errorMessage .= "no records found\r\n";
                  return false;
                }

        }

        function a_get_id($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["host"]) && empty($params["a_id"])) {
                        $this->errorMessage .= "Parameter: host or a_id is required.\r\n";
                        return false;
                }

                if(!empty($params["a_id"])) return intval($params["a_id"]);

                $host = addslashes($params["host"]);

                $sql = "SELECT dns_a.doc_id FROM dns_dep, dns_a WHERE dns_dep.parent_doc_id = $zone_id AND dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' and dns_dep.child_doc_id = dns_a.doc_id and dns_dep.child_doctype_id = '".$this->a_doctype_id."' and dns_a.host = '".$host."'";
                $record = $go_api->db->queryOneRecord($sql);
                return $record["doc_id"];


        }

        function a_get($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["host"]) && empty($params["a_id"])) {
                        $this->errorMessage .= "Parameters: host or a_id are required.\r\n";
                        return false;
                }

                // Hole A ID
                if(empty($params["a_id"])) {
                        $a_id = $this->a_get_id($session,$params);
                } else {
                        $a_id = intval($params["a_id"]);
                }

                if(empty($a_id)) {
                        $this->errorMessage .= "a_id cannot be resolved\r\n";
                        return false;
                }

                        $sql = "SELECT dns_a.* FROM dns_dep, dns_a WHERE dns_dep.parent_doc_id = $zone_id AND dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' and dns_dep.child_doc_id = dns_a.doc_id and dns_dep.child_doctype_id = '".$this->a_doctype_id."' and dns_a.doc_id = '$a_id'";
                $a = $go_api->db->queryOneRecord($sql);

                // überprüfe, ob A Record gefunden wurde
                if(!is_array($a)) {
                        $this->errorMessage .= "no records found\r\n";
                        return false;
                }

                $a["a_id"] = $a["doc_id"];
                $out = $a;

                return $out;
        }

        function a_add($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_insert"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["host"]) or empty($params["ip_adresse"])) {
                        $this->errorMessage .= "Parameters: host and ip_adresse are required.\r\n";
                        return false;
                }

                // Hole Daten zu Parent Record (Zone)
                $sql = "SELECT * FROM dns_nodes WHERE doc_id = $zone_id and doctype_id = '".$this->zone_doctype_id."'";
                $zone_record = $go_api->db->queryOneRecord($sql);

                if(empty($zone_record["userid"])) {
                        $this->errorMessage .= "zone user cannot be resolved.\r\n";
                        return false;
                }

                // Checke ob A-Record bereits existiert
                $a_id = $this->a_get_id($session,$params);
                if(!empty($a_id)) {
                        $this->errorMessage .= "A-Record: ".$params["host"]." already exists\r\n";
                        return false;
                }

                // Füge A-Record ein
                $sql = "INSERT INTO dns_a (doctype_id,host,ip_adresse) VALUES ('".$this->a_doctype_id."','".addslashes($params["host"])."','".addslashes($params["ip_adresse"])."')";
                $go_api->db->query($sql);
                $a_id = $doc_id = $go_api->db->insertID();

                // Node einfügen
                $sql = "INSERT INTO dns_nodes ( userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title
                                ) VALUES (
                                ".intval($zone_record["userid"]).",
                                ".intval($zone_record["groupid"]).",
                                '',
                                'a',
                                '".$this->a_doctype_id."',
                                '1',
                                '',
                                '',
                                $a_id,
                                ''
                                )";

                $go_api->db->query($sql);
                $a_tree_id = $go_api->db->insertID();

                // DEP-Record einfügen
                $sql = "INSERT INTO dns_dep (userid, groupid, parent_doc_id, parent_doctype_id, parent_tree_id, child_doc_id, child_doctype_id, child_tree_id, status)
                VALUES (
                ".intval($zone_record["userid"]).",
                ".intval($zone_record["groupid"]).",
                ".intval($zone_record["doc_id"]).",
                '".$this->zone_doctype_id."',
                ".intval($zone_record["tree_id"]).",
                $a_id,
                '".$this->a_doctype_id."',
                $a_tree_id,
                1)";

                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->a_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_insert != "") {
                    $event_insert = $doc->event_insert;
                    $this->errorMessage .= $go_api->$event_class->$event_insert($doc_id,$this->a_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;
                return $a_id;

        }

        function a_update($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["host"]) && empty($params["a_id"])){
                  $this->errorMessage .= "Parameter: a_id or host is required.\r\n";
                  return false;
                }

                // Hole A ID
                if(empty($params["a_id"])) {
                        $a_id = $this->a_get_id($session,$params);
                } else {
                        $a_id = intval($params["a_id"]);
                }

                if(empty($a_id)) {
                        $this->errorMessage .= "a_id cannot be resolved\r\n";
                        return false;
                }

                // checke ob Host schon existiert
                $sql = "SELECT dns_a.doc_id FROM dns_dep, dns_a where dns_a.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '".$this->a_doctype_id."' and dns_dep.parent_doc_id = $zone_id and dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' and dns_a.host = '".addslashes($params["host"])."' and dns_a.doc_id != $a_id";

                $tmp = $go_api->db->queryOneRecord($sql);
                if($tmp["doc_id"] > 0) {
                        $this->errorMessage .= "Host alredy exist with other a_id.\r\n";
                        return false;
                }

                // Liegt Objekt im Papierkorb?
                if($this->a_is_suspended($session,$params)){
                  $this->errorMessage .= "This A Record is suspended.\r\n";
                  return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $fields = $go_api->db->queryAllRecords("SHOW COLUMNS FROM dns_a");
                foreach($fields as $field){
                  $field_arr[] = $field["Field"];
                }

                $changes = "";
                foreach($params as $key => $val){
                  if($key != "a_id" && $key != "doc_id" && $key != "doctype_id" && $key != "host" && $key != "dns_soa" && $key != "zone_id" && in_array($key, $field_arr)) $changes .= $key." = '".addslashes($val)."',";
                }
                $changes = substr($changes, 0, -1);

                $sql = "UPDATE dns_a SET ".$changes." WHERE doc_id = ".$a_id;

                if(!$go_api->db->query($sql)) $this->errorMessage  .= "Database could not be updated.\r\n";
                if($this->errorMessage != '') return false;

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->a_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($a_id,$this->a_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $this->a_get($session,$params);

        }

        function a_suspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["a_id"]) && empty($params["host"])) {
                        $this->errorMessage .= "Parameters: host or a_id are required.\r\n";
                        return false;
                }

                if(empty($params["a_id"])) {
                        $a_id = $this->a_get_id($session,$params);
                } else {
                        $a_id = intval($params["a_id"]);
                }
                if(empty($a_id)) {
                        $this->errorMessage .= "a_id cannot be resolved\r\n";
                        return false;
                }

                if($this->a_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // suspend durchführen
                $sql = "UPDATE dns_nodes SET status = 0 WHERE doc_id = $a_id and doctype_id = '".$this->a_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->a_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($a_id,$this->a_doctype_id, 'do', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function a_unsuspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["a_id"]) && empty($params["host"])) {
                        $this->errorMessage .= "Parameters: host or a_id are required.\r\n";
                        return false;
                }

                if(empty($params["a_id"])) {
                        $a_id = $this->a_get_id($session,$params);
                } else {
                        $a_id = intval($params["a_id"]);
                }
                if(empty($a_id)) {
                        $this->errorMessage .= "a_id cannot be resolved\r\n";
                        return false;
                }

                if(!$this->a_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // unsuspend durchführen
                $sql = "UPDATE dns_nodes SET status = 1 WHERE doc_id = $a_id and doctype_id = '".$this->a_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->a_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($a_id,$this->a_doctype_id, 'undo', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function a_delete($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_delete"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["a_id"]) && empty($params["host"])) {
                        $this->errorMessage .= "Parameters: host or a_id are required.\r\n";
                        return false;
                }

                if(empty($params["a_id"])) {
                        $a_id = $this->a_get_id($session,$params);
                } else {
                        $a_id = intval($params["a_id"]);
                }
                if(empty($a_id)) {
                        $this->errorMessage .= "a_id cannot be resolved\r\n";
                        return false;
                }

                // Ist A Record suspended?
                if(!$this->a_is_suspended($session,$params)){
                        $this->errorMessage .= "The record must be suspended before it can be deleted.\r\n";
                        return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // DELETE durchführen
                $go_api->db->query("DELETE FROM dns_dep WHERE child_doc_id = '".$a_id."' AND child_doctype_id = '".$this->a_doctype_id."' AND parent_doc_id = '".$zone_id."' AND parent_doctype_id = '".$this->zone_doctype_id."'");
                $sql = "DELETE dns_nodes.*, dns_a.* FROM dns_nodes, dns_a WHERE dns_nodes.doc_id = '$a_id' and dns_nodes.doctype_id = '".$this->a_doctype_id."' and dns_nodes.status != '1' AND dns_nodes.doc_id = dns_a.doc_id AND dns_nodes.doctype_id = dns_a.doctype_id";
                return $go_api->db->query($sql);
        }

        /*
        #############################################################
        # Cname Records
        #############################################################
        */

        function cname_list($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                $sql = "SELECT dns_cname.* FROM dns_dep, dns_cname WHERE dns_dep.parent_doc_id = $zone_id AND dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' and dns_dep.child_doc_id = dns_cname.doc_id and dns_dep.child_doctype_id = '".$this->cname_doctype_id."'";

                // hole CNAME-Records
                $cs = $go_api->db->queryAllRecords($sql);

                if(!empty($cs)){
                  foreach($cs as $c) {
                        $c["cname_id"] = $c["doc_id"];
                        $out[$c["host"]] = $c;

                  }
                  return $out;
                } else {
                  $this->errorMessage .= "no records found\r\n";
                  return false;
                }

        }

        function cname_get_id($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["host"]) && empty($params["cname_id"])) {
                        $this->errorMessage .= "Parameter: host or cname_id is required.\r\n";
                        return false;
                }

                if(!empty($params["cname_id"])) return intval($params["cname_id"]);

                $host = addslashes($params["host"]);

                $sql = "SELECT dns_cname.doc_id FROM dns_dep, dns_cname WHERE dns_dep.parent_doc_id = $zone_id AND dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' and dns_dep.child_doc_id = dns_cname.doc_id and dns_dep.child_doctype_id = '".$this->cname_doctype_id."' and dns_cname.host = '$host'";
                $record = $go_api->db->queryOneRecord($sql);

                return $record["doc_id"];


        }

        function cname_get($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["host"]) && empty($params["cname_id"])) {
                        $this->errorMessage .= "Parameters: host or cname_id are required.\r\n";
                        return false;
                }

                // Hole CNAME ID
                if(empty($params["cname_id"])) {
                        $cname_id = $this->cname_get_id($session,$params);
                } else {
                        $cname_id = intval($params["cname_id"]);
                }

                if(empty($cname_id)) {
                        $this->errorMessage .= "cname_id cannot be resolved\r\n";
                        return false;
                }

                        $sql = "SELECT dns_cname.* FROM dns_dep, dns_cname WHERE dns_dep.parent_doc_id = $zone_id AND dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' and dns_dep.child_doc_id = dns_cname.doc_id and dns_dep.child_doctype_id = '".$this->cname_doctype_id."' and dns_cname.doc_id = '$cname_id'";
                $c = $go_api->db->queryOneRecord($sql);

                // überprüfe, ob CNAME Record gefunden wurde
                if(!is_array($c)) {
                        $this->errorMessage .= "no records found\r\n";
                        return false;
                }

                $c["cname_id"] = $c["doc_id"];
                $out = $c;

                return $out;
        }

        function cname_add($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_insert"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["host"]) or empty($params["ziel"])) {
                        $this->errorMessage .= "Parameters: host and ziel are required.\r\n";
                        return false;
                }

                // Hole Daten zu Parent Record (Zone)
                $sql = "SELECT * FROM dns_nodes WHERE doc_id = $zone_id and doctype_id = '".$this->zone_doctype_id."'";
                $zone_record = $go_api->db->queryOneRecord($sql);

                if(empty($zone_record["userid"])) {
                        $this->errorMessage .= "zone user cannot be resolved.\r\n";
                        return false;
                }

                // Checke ob CN-Record bereits existiert
                $cname_id = $this->cname_get_id($session,$params);
                if(!empty($cname_id)) {
                        $this->errorMessage .= "CNAME Record: ".$params["host"]." already exists\r\n";
                        return false;
                }

                // Füge CNAME-Record ein
                $sql = "INSERT INTO dns_cname (doctype_id,host,ziel) VALUES ('".$this->cname_doctype_id."','".addslashes($params["host"])."','".addslashes($params["ziel"])."')";
                $go_api->db->query($sql);
                $cname_id = $doc_id = $go_api->db->insertID();

                // Node einfügen
                $sql = "INSERT INTO dns_nodes ( userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title
                                ) VALUES (
                                ".intval($zone_record["userid"]).",
                                ".intval($zone_record["groupid"]).",
                                '',
                                'a',
                                '".$this->cname_doctype_id."',
                                '1',
                                '',
                                '',
                                $cname_id,
                                ''
                                )";

                $go_api->db->query($sql);
                $cname_tree_id = $go_api->db->insertID();

                // DEP-Record einfügen
                $sql = "INSERT INTO dns_dep (userid, groupid, parent_doc_id, parent_doctype_id, parent_tree_id, child_doc_id, child_doctype_id, child_tree_id, status)
                VALUES (
                ".intval($zone_record["userid"]).",
                ".intval($zone_record["groupid"]).",
                ".intval($zone_record["doc_id"]).",
                '".$this->zone_doctype_id."',
                ".intval($zone_record["tree_id"]).",
                $cname_id,
                '".$this->cname_doctype_id."',
                $cname_tree_id,
                1)";

                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->cname_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_insert != "") {
                    $event_insert = $doc->event_insert;
                    $this->errorMessage .= $go_api->$event_class->$event_insert($doc_id,$this->cname_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;
                return $cname_id;

        }

        function cname_update($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["host"]) && empty($params["cname_id"])){
                  $this->errorMessage .= "Parameter: cname_id or host is required.\r\n";
                  return false;
                }

                // Hole CNAME ID
                if(empty($params["cname_id"])) {
                        $cname_id = $this->cname_get_id($session,$params);
                } else {
                        $cname_id = intval($params["cname_id"]);
                }

                if(empty($cname_id)) {
                        $this->errorMessage .= "cname_id cannot be resolved\r\n";
                        return false;
                }

                // checke ob Host schon existiert
                $sql = "SELECT dns_cname.doc_id FROM dns_dep, dns_cname where dns_cname.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '".$this->cname_doctype_id."' and dns_dep.parent_doc_id = $zone_id and dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' and dns_cname.host = '".addslashes($params["host"])."' and dns_cname.doc_id != $cname_id";

                $tmp = $go_api->db->queryOneRecord($sql);
                if($tmp["doc_id"] > 0) {
                        $this->errorMessage .= "Host alredy exist with other cname_id.\r\n";
                        return false;
                }

                // Liegt Objekt im Papierkorb?
                if($this->cname_is_suspended($session,$params)){
                  $this->errorMessage .= "This CNAME Record is suspended.\r\n";
                  return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $fields = $go_api->db->queryAllRecords("SHOW COLUMNS FROM dns_cname");
                foreach($fields as $field){
                  $field_arr[] = $field["Field"];
                }

                $changes = "";
                foreach($params as $key => $val){
                  if($key != "cname_id" && $key != "doc_id" && $key != "doctype_id" && $key != "host" && $key != "dns_soa" && $key != "zone_id" && in_array($key, $field_arr)) $changes .= $key." = '".addslashes($val)."',";
                }
                $changes = substr($changes, 0, -1);

                $sql = "UPDATE dns_cname SET ".$changes." WHERE doc_id = ".$cname_id;

                if(!$go_api->db->query($sql)) $this->errorMessage  .= "Database could not be updated.\r\n";
                if($this->errorMessage != '') return false;

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->cname_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($cname_id,$this->cname_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $this->cname_get($session,$params);

        }

        function cname_suspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["cname_id"]) && empty($params["host"])) {
                        $this->errorMessage .= "Parameters: host or a_id are required.\r\n";
                        return false;
                }

                if(empty($params["cname_id"])) {
                        $cname_id = $this->cname_get_id($session,$params);
                } else {
                        $cname_id = intval($params["cname_id"]);
                }
                if(empty($cname_id)) {
                        $this->errorMessage .= "cname_id cannot be resolved\r\n";
                        return false;
                }

                if($this->cname_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // suspend durchführen
                $sql = "UPDATE dns_nodes SET status = 0 WHERE doc_id = $cname_id and doctype_id = '".$this->cname_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->cname_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($cname_id,$this->cname_doctype_id, 'do', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function cname_unsuspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["cname_id"]) && empty($params["host"])) {
                        $this->errorMessage .= "Parameters: host or cname_id are required.\r\n";
                        return false;
                }

                if(empty($params["cname_id"])) {
                        $cname_id = $this->cname_get_id($session,$params);
                } else {
                        $cname_id = intval($params["cname_id"]);
                }
                if(empty($cname_id)) {
                        $this->errorMessage .= "cname_id cannot be resolved\r\n";
                        return false;
                }

                if(!$this->cname_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // unsuspend durchführen
                $sql = "UPDATE dns_nodes SET status = 1 WHERE doc_id = $cname_id and doctype_id = '".$this->cname_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->cname_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($cname_id,$this->cname_doctype_id, 'undo', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function cname_delete($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_delete"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["cname_id"]) && empty($params["host"])) {
                        $this->errorMessage .= "Parameters: host or cname_id are required.\r\n";
                        return false;
                }

                if(empty($params["cname_id"])) {
                        $cname_id = $this->cname_get_id($session,$params);
                } else {
                        $cname_id = intval($params["cname_id"]);
                }
                if(empty($cname_id)) {
                        $this->errorMessage .= "cname_id cannot be resolved\r\n";
                        return false;
                }

                // Ist CNAME Record suspended?
                if(!$this->cname_is_suspended($session,$params)){
                        $this->errorMessage .= "The record must be suspended before it can be deleted.\r\n";
                        return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // DELETE durchführen
                $go_api->db->query("DELETE FROM dns_dep WHERE child_doc_id = '".$cname_id."' AND child_doctype_id = '".$this->cname_doctype_id."' AND parent_doc_id = '".$zone_id."' AND parent_doctype_id = '".$this->zone_doctype_id."'");
                $sql = "DELETE dns_nodes.*, dns_cname.* FROM dns_nodes, dns_cname WHERE dns_nodes.doc_id = '$cname_id' and dns_nodes.doctype_id = '".$this->cname_doctype_id."' and dns_nodes.status != '1' AND dns_nodes.doc_id = dns_cname.doc_id AND dns_nodes.doctype_id = dns_cname.doctype_id";
                return $go_api->db->query($sql);
        }

        /*
        #############################################################
        # MX Records
        #############################################################
        */

        function mx_list($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                $sql = "SELECT dns_mx.* FROM dns_dep, dns_mx WHERE dns_dep.parent_doc_id = $zone_id AND dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' and dns_dep.child_doc_id = dns_mx.doc_id and dns_dep.child_doctype_id = '".$this->mx_doctype_id."'";

                // hole MX-Records
                $mxs = $go_api->db->queryAllRecords($sql);

                if(!empty($mxs)){
                  foreach($mxs as $mx) {
                        $mx["mx_id"] = $mx["doc_id"];
                        $out["mx_id".$mx["doc_id"]] = $mx;

                  }
                  return $out;
                } else {
                  $this->errorMessage .= "no records found\r\n";
                  return false;
                }

        }

        function mx_get_id($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                if(empty($params["mx_id"])){
                  $this->errorMessage .= "Parameter: mx_id is required.\r\n";
                  return false;
                } else {
                  return intval($params["mx_id"]);
                }
        }

        function mx_get($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["mx_id"])) {
                        $this->errorMessage .= "Parameter: host or mx_id are required.\r\n";
                        return false;
                }

                // Hole MX ID
                $mx_id = intval($params["mx_id"]);

                if(empty($mx_id)) {
                        $this->errorMessage .= "mx_id cannot be resolved\r\n";
                        return false;
                }

                        $sql = "SELECT dns_mx.* FROM dns_dep, dns_mx WHERE dns_dep.parent_doc_id = $zone_id AND dns_dep.parent_doctype_id = '".$this->zone_doctype_id."' and dns_dep.child_doc_id = dns_mx.doc_id and dns_dep.child_doctype_id = '".$this->mx_doctype_id."' and dns_mx.doc_id = '$mx_id'";
                $mx = $go_api->db->queryOneRecord($sql);

                // überprüfe, ob MX Record gefunden wurde
                if(!is_array($mx)) {
                        $this->errorMessage .= "no records found\r\n";
                        return false;
                }

                $mx["mx_id"] = $mx["doc_id"];
                $out = $mx;

                return $out;
        }

        function mx_add($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_insert"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(!isset($params["host"])) $this->errorMessage .= "Parameter: host is required.\r\n";
                if(empty($params["prioritaet"]) or empty($params["mailserver"])) $this->errorMessage .= "Parameters: prioritaet and mailserver are required.\r\n";
                if($this->errorMessage != "") return false;

                // Hole Daten zu Parent Record (Zone)
                $sql = "SELECT * FROM dns_nodes WHERE doc_id = $zone_id and doctype_id = '".$this->zone_doctype_id."'";
                $zone_record = $go_api->db->queryOneRecord($sql);

                if(empty($zone_record["userid"])) {
                        $this->errorMessage .= "zone user cannot be resolved.\r\n";
                        return false;
                }

                // Füge MX-Record ein
                $sql = "INSERT INTO dns_mx (doctype_id,host,prioritaet,mailserver) VALUES ('".$this->mx_doctype_id."','".addslashes($params["host"])."','".addslashes($params["prioritaet"])."','".addslashes($params["mailserver"])."')";
                $go_api->db->query($sql);
                $mx_id = $doc_id = $go_api->db->insertID();

                // Node einfügen
                $sql = "INSERT INTO dns_nodes ( userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title
                                ) VALUES (
                                ".intval($zone_record["userid"]).",
                                ".intval($zone_record["groupid"]).",
                                '',
                                'a',
                                '".$this->mx_doctype_id."',
                                '1',
                                '',
                                '',
                                $mx_id,
                                ''
                                )";

                $go_api->db->query($sql);
                $mx_tree_id = $go_api->db->insertID();

                // DEP-Record einfügen
                $sql = "INSERT INTO dns_dep (userid, groupid, parent_doc_id, parent_doctype_id, parent_tree_id, child_doc_id, child_doctype_id, child_tree_id, status)
                VALUES (
                ".intval($zone_record["userid"]).",
                ".intval($zone_record["groupid"]).",
                ".intval($zone_record["doc_id"]).",
                '".$this->zone_doctype_id."',
                ".intval($zone_record["tree_id"]).",
                $mx_id,
                '".$this->mx_doctype_id."',
                $mx_tree_id,
                1)";

                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->mx_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_insert != "") {
                    $event_insert = $doc->event_insert;
                    $this->errorMessage .= $go_api->$event_class->$event_insert($doc_id,$this->mx_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;
                return $mx_id;
        }

        function mx_update($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["mx_id"])){
                  $this->errorMessage .= "Parameter: mx_id is required.\r\n";
                  return false;
                }

                // Hole MX ID
                $mx_id = intval($params["mx_id"]);

                if(empty($mx_id)) {
                        $this->errorMessage .= "mx_id cannot be resolved\r\n";
                        return false;
                }

                // Liegt Objekt im Papierkorb?
                if($this->mx_is_suspended($session,$params)){
                  $this->errorMessage .= "This MX Record is suspended.\r\n";
                  return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $fields = $go_api->db->queryAllRecords("SHOW COLUMNS FROM dns_mx");
                foreach($fields as $field){
                  $field_arr[] = $field["Field"];
                }

                $changes = "";
                foreach($params as $key => $val){
                  if($key != "m_id" && $key != "doc_id" && $key != "doctype_id" && $key != "dns_soa" && $key != "zone_id" && in_array($key, $field_arr)) $changes .= $key." = '".addslashes($val)."',";
                }
                $changes = substr($changes, 0, -1);

                $sql = "UPDATE dns_mx SET ".$changes." WHERE doc_id = ".$mx_id;

                if(!$go_api->db->query($sql)) $this->errorMessage  .= "Database could not be updated.\r\n";
                if($this->errorMessage != '') return false;

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->mx_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($mx_id,$this->mx_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $this->mx_get($session,$params);

        }

        function mx_suspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["mx_id"])) {
                        $this->errorMessage .= "Parameter: mx_id is required.\r\n";
                        return false;
                }

                $mx_id = intval($params["mx_id"]);
                if(empty($mx_id)) {
                        $this->errorMessage .= "mx_id cannot be resolved\r\n";
                        return false;
                }

                if($this->mx_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // suspend durchführen
                $sql = "UPDATE dns_nodes SET status = 0 WHERE doc_id = $mx_id and doctype_id = '".$this->mx_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->mx_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($mx_id,$this->mx_doctype_id, 'do', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function mx_unsuspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["mx_id"])) {
                        $this->errorMessage .= "Parameter: mx_id is required.\r\n";
                        return false;
                }

                $mx_id = intval($params["mx_id"]);
                if(empty($mx_id)) {
                        $this->errorMessage .= "mx_id cannot be resolved\r\n";
                        return false;
                }

                if(!$this->mx_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // unsuspend durchführen
                $sql = "UPDATE dns_nodes SET status = 1 WHERE doc_id = $mx_id and doctype_id = '".$this->mx_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->mx_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($mx_id,$this->mx_doctype_id, 'undo', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function mx_delete($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["dns_delete"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                // überprüfe Parameter für Record
                if(empty($params["mx_id"])) {
                        $this->errorMessage .= "Parameter: mx_id is required.\r\n";
                        return false;
                }

                $mx_id = intval($params["mx_id"]);
                if(empty($mx_id)) {
                        $this->errorMessage .= "mx_id cannot be resolved\r\n";
                        return false;
                }

                // Ist MX Record suspended?
                if(!$this->mx_is_suspended($session,$params)){
                        $this->errorMessage .= "The record must be suspended before it can be deleted.\r\n";
                        return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->zone_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // DELETE durchführen
                $go_api->db->query("DELETE FROM dns_dep WHERE child_doc_id = '".$mx_id."' AND child_doctype_id = '".$this->mx_doctype_id."' AND parent_doc_id = '".$zone_id."' AND parent_doctype_id = '".$this->zone_doctype_id."'");
                $sql = "DELETE dns_nodes.*, dns_mx.* FROM dns_nodes, dns_mx WHERE dns_nodes.doc_id = '$mx_id' and dns_nodes.doctype_id = '".$this->mx_doctype_id."' and dns_nodes.status != '1' AND dns_nodes.doc_id = dns_mx.doc_id AND dns_nodes.doctype_id = dns_mx.doctype_id";
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

        function zone_is_suspended($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM dns_nodes WHERE doc_id = '".$zone_id."' AND doctype_id = '".$this->zone_doctype_id."' AND status != '1'")){
                  return true;
                } else {
                  return false;
                }

        }

        function zone_status_empty($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                // Hole Zone ID
                if(empty($params["zone_id"])) {
                        $zone_id = $this->zone_get_id($session,$params);
                } else {
                        $zone_id = intval($params["zone_id"]);
                }

                if(empty($zone_id)) {
                        $this->errorMessage .= "zone_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM dns_isp_dns WHERE doc_id = '".$zone_id."' AND status != ''")){
                  return false;
                } else {
                  return true;
                }

        }

        function a_is_suspended($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                if(empty($params["a_id"]) && empty($params["host"])) {
                        $this->errorMessage .= "Parameters: host or a_id are required.\r\n";
                        return false;
                }

                // Hole A ID
                if(empty($params["a_id"])) {
                        $a_id = $this->a_get_id($session,$params);
                } else {
                        $a_id = intval($params["a_id"]);
                }

                if(empty($a_id)) {
                        $this->errorMessage .= "a_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM dns_nodes WHERE doc_id = '".$a_id."' AND doctype_id = '".$this->a_doctype_id."' AND status != '1'")){
                  return true;
                } else {
                  return false;
                }

        }

        function cname_is_suspended($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["dns_soa"]) && empty($params["zone_id"])) {
                        $this->errorMessage .= "Parameters: dns_soa or zone_id are required.\r\n";
                        return false;
                }

                if(empty($params["cname_id"]) && empty($params["host"])) {
                        $this->errorMessage .= "Parameters: host or cname_id are required.\r\n";
                        return false;
                }

                // Hole CNAME ID
                if(empty($params["cname_id"])) {
                        $cname_id = $this->cname_get_id($session,$params);
                } else {
                        $cname_id = intval($params["cname_id"]);
                }

                if(empty($cname_id)) {
                        $this->errorMessage .= "cname_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM dns_nodes WHERE doc_id = '".$cname_id."' AND doctype_id = '".$this->cname_doctype_id."' AND status != '1'")){
                  return true;
                } else {
                  return false;
                }

        }

        function mx_is_suspended($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["mx_id"])) {
                        $this->errorMessage .= "Parameter: mx_id is required.\r\n";
                        return false;
                }

                // Hole CNAME ID
                $mx_id = intval($params["mx_id"]);

                if(empty($mx_id)) {
                        $this->errorMessage .= "mx_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM dns_nodes WHERE doc_id = '".$mx_id."' AND doctype_id = '".$this->mx_doctype_id."' AND status != '1'")){
                  return true;
                } else {
                  return false;
                }

        }

}
?>