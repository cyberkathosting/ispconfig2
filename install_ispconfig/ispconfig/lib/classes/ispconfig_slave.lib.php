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

class slave {

        var $errorMessage = '';
        var $reseller_doctype_id = 1022;
        var $slave_doctype_id = 1028;

        /*
        #############################################################
        # Zones
        #############################################################
        */

        /*
        Function: slave_list
        params:   leer
        return:   Array with slave Arrays
        */

        function slave_list($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["slave_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                if(!isset($params["reseller_title"]) || $params["reseller_title"] == "all"){
                  // hole Slave Record
                  $slaves = $go_api->db->queryAllRecords("SELECT * FROM dns_secondary ORDER BY domain");
                } else {
                  if($params["reseller_title"] == "admin"){
                    // hole Slave Record
                    $slaves = $go_api->db->queryAllRecords("SELECT dns_secondary.* FROM dns_secondary, dns_nodes WHERE dns_nodes.groupid = 1 AND dns_nodes.doctype_id = '".$this->slave_doctype_id."' AND dns_nodes.doc_id = dns_secondary.doc_id AND dns_nodes.doctype_id = dns_secondary.doctype_id ORDER BY dns_secondary.domain");
                  } else {
                    if($reseller_group = $this->reseller_get_gid($params["reseller_title"])){
                      // hole Slave Record
                      $slaves = $go_api->db->queryAllRecords("SELECT dns_secondary.* FROM dns_secondary, dns_nodes WHERE dns_nodes.groupid = $reseller_group AND dns_nodes.doctype_id = '".$this->slave_doctype_id."' AND dns_nodes.doc_id = dns_secondary.doc_id AND dns_nodes.doctype_id = dns_secondary.doctype_id ORDER BY dns_secondary.domain");
                    } else {
                      $this->errorMessage .= "cannot find reseller\r\n";
                      return false;
                    }
                  }
                }

                if(!empty($slaves)){
                  foreach($slaves as $slave) {
                        $slave["slave_id"] = $slave["doc_id"];
                        $out[$slave["domain"]] = $slave;

                  }
                  return $out;
                } else {
                  $this->errorMessage .= "no records found\r\n";
                  return false;
                }

        }

        /*
        Function: slave_get
        Params: slave_id oder slave_title
        Return: slave Array
        */

        function slave_get($session,$params) {
                global $go_api, $go_info;


                // überprüfe Rechte
                if($session["user"]["slave_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["slave_id"]) && empty($params["domain"])) {
                        $this->errorMessage .= "Parameters: domain or slave_id are required.\r\n";
                        return false;
                }

                // Hole Slave ID
                if(empty($params["slave_id"])) {
                        $slave_id = $this->slave_get_id($session,$params);
                } else {
                        $slave_id = intval($params["slave_id"]);
                }

                if(empty($slave_id)) {
                        $this->errorMessage .= "slave_id cannot be resolved\r\n";
                        return false;
                }

                // Hole Slave
                $slave = $go_api->db->queryOneRecord("SELECT * FROM dns_secondary WHERE doc_id = $slave_id");

                // überprüfe, ob Slave gefunden wurde
                if(!is_array($slave)) {
                        $this->errorMessage .= "no records found\r\n";
                        return false;
                }

                $slave["slave_id"] = $slave["doc_id"];
                $out = $slave;

                return $out;
        }

        /*
        Function: slave_get_id
        Params: slave_title
        Return: slave_id
        */

        function slave_get_id($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["slave_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["domain"])) {
                        $this->errorMessage .= "Parameters: domain is required.\r\n";
                        return false;
                }

                // Hole Slave ID
                $domain = addslashes($params["domain"]);

                if($slave = $go_api->db->queryOneRecord("SELECT doc_id FROM dns_secondary WHERE domain = '$domain'")){
                  return $slave["doc_id"];
                } else {
                  return false;
                }


        }

        /*
        Function: slave_add
        Params:
        Return: slave_id
        */

        function slave_add($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["slave_insert"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["domain"])) $this->errorMessage .= "Parameter: domain is required.\r\n";
                if(empty($params["master_ip"])) $this->errorMessage .= "Parameter: master_ip is required.\r\n";
                if(empty($params["reseller_title"]) && empty($params["reseller_group"])) $this->errorMessage .= "Parameter: reseller_title or reseller_group is required.\r\n";

                if($this->errorMessage != '') return false;
                if(empty($params["reseller_group"])){
                  if(!$reseller_group = $this->reseller_get_gid($params["reseller_title"])){
                    $this->errorMessage         .= "Cannot find reseller.\r\n";
                    return false;
                  }
                } else {
                  $reseller_group = intval($params["reseller_group"]);
                }

                // checke ob Slave bereits existiert
                $slave_id = $this->slave_get_id($session,$params);
                if(!empty($slave_id)) {
                        $this->errorMessage .= "Slave DNS Record: ".$params["domain"]." already exists\r\n";
                        return false;
                }

                // Record einfügen
                $sql = "INSERT INTO dns_secondary (doctype_id, domain, master_ip) VALUES ('".$this->slave_doctype_id."', '".addslashes($params["domain"])."', '".addslashes($params["master_ip"])."')";

                $go_api->db->query($sql);
                $slave_id = $doc_id = $go_api->db->insertID();

                // Node einfügen
                $sql = "INSERT INTO dns_nodes (userid, groupid, parent, type, doctype_id, status, icon, modul, doc_id, title ) VALUES (1, '".$reseller_group."', 'group".$reseller_group."', 'i', '".$this->slave_doctype_id."', '1', 'secondary.gif', '', $doc_id, '".addslashes($params["domain"])."')";

                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->slave_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_insert != "") {
                    $event_insert = $doc->event_insert;
                    $this->errorMessage .= $go_api->$event_class->$event_insert($doc_id,$this->slave_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;
                return $slave_id;
        }

        function slave_update($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["slave_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["slave_id"]) && empty($params["domain"])) $this->errorMessage .= "Parameter: slave_id or domain is required.\r\n";
                if(empty($params["master_ip"])) $this->errorMessage .= "Parameter: master_ip is required.\r\n";
                if($this->errorMessage != "") return false;

                // Hole Slave ID
                if(empty($params["slave_id"])) {
                        $slave_id = $this->slave_get_id($session,$params);
                } else {
                        $slave_id = intval($params["slave_id"]);
                }

                if(empty($slave_id)) {
                        $this->errorMessage .= "slave_id cannot be resolved\r\n";
                        return false;
                }

                // Liegt Objekt im Papierkorb?
                if($this->slave_is_suspended($session,$params)){
                  $this->errorMessage .= "Slave DNS Record is suspended.\r\n";
                  return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->slave_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                $fields = $go_api->db->queryAllRecords("SHOW COLUMNS FROM dns_secondary");
                foreach($fields as $field){
                  $field_arr[] = $field["Field"];
                }

                $changes = "";
                foreach($params as $key => $val){
                  if($key != "slave_id" && $key != "doc_id" && $key != "doctype_id" && $key != "status" && in_array($key, $field_arr)) $changes .= $key." = '".addslashes($val)."',";
                }
                $changes = substr($changes, 0, -1);

                $sql = "UPDATE dns_secondary SET ".$changes." WHERE doc_id = ".$slave_id;

                if(!$go_api->db->query($sql)) $this->errorMessage  .= "Database could not be updated.\r\n";
                if($this->errorMessage != '') return false;

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->slave_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($slave_id,$this->slave_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $this->slave_get($session,$params);
        }

        function slave_suspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["slave_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["domain"]) && empty($params["slave_id"])) {
                        $this->errorMessage .= "Parameters: domain or slave_id are required.\r\n";
                        return false;
                }

                // Hole Slave ID
                if(empty($params["slave_id"])) {
                        $slave_id = $this->slave_get_id($session,$params);
                } else {
                        $slave_id = intval($params["slave_id"]);
                }

                if(empty($slave_id)) {
                        $this->errorMessage .= "slave_id cannot be resolved\r\n";
                        return false;
                }

                if($this->slave_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->slave_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // suspend durchführen
                $sql = "UPDATE dns_nodes SET status = 0 WHERE doc_id = $slave_id and doctype_id = '".$this->slave_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->slave_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($slave_id,$this->slave_doctype_id, 'do', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function slave_unsuspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["slave_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["domain"]) && empty($params["slave_id"])) {
                        $this->errorMessage .= "Parameters: domain or slave_id are required.\r\n";
                        return false;
                }

                // Hole Slave ID
                if(empty($params["slave_id"])) {
                        $slave_id = $this->slave_get_id($session,$params);
                } else {
                        $slave_id = intval($params["slave_id"]);
                }

                if(empty($slave_id)) {
                        $this->errorMessage .= "slave_id cannot be resolved\r\n";
                        return false;
                }

                if(!$this->slave_is_suspended($session,$params)) return true;

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->slave_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // unsuspend durchführen
                $sql = "UPDATE dns_nodes SET status = 1 WHERE doc_id = $slave_id and doctype_id = '".$this->slave_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->slave_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($slave_id,$this->slave_doctype_id, 'undo', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function slave_delete($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["slave_delete"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["domain"]) && empty($params["slave_id"])) {
                        $this->errorMessage .= "Parameters: domain or slave_id are required.\r\n";
                        return false;
                }

                // Hole Slave ID
                if(empty($params["slave_id"])) {
                        $slave_id = $this->slave_get_id($session,$params);
                } else {
                        $slave_id = intval($params["slave_id"]);
                }

                if(empty($slave_id)) {
                        $this->errorMessage .= "slave_id cannot be resolved\r\n";
                        return false;
                }

                // Ist Slave suspended?
                if(!$this->slave_is_suspended($session,$params)){
                        $this->errorMessage .= "The record must be suspended before it can be deleted.\r\n";
                        return false;
                }

                // Ist Status-Feld leer (d.h., ist writeconf.php fertig)?
                if(!$this->slave_status_empty($session,$params)){
                  $this->errorMessage .= "Status field is not empty.\r\n";
                  return false;
                }

                // DELETE durchführen
                $sql = "DELETE dns_nodes.*, dns_secondary.* FROM dns_nodes, dns_secondary WHERE dns_nodes.doc_id = '$slave_id' and dns_nodes.doctype_id = '".$this->slave_doctype_id."' and dns_nodes.status != '1' AND dns_nodes.doc_id = dns_secondary.doc_id AND dns_nodes.doctype_id = dns_secondary.doctype_id";

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

        function slave_is_suspended($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["domain"]) && empty($params["slave_id"])) {
                        $this->errorMessage .= "Parameters: domain or slave_id are required.\r\n";
                        return false;
                }

                // Hole Slave ID
                if(empty($params["slave_id"])) {
                        $slave_id = $this->slave_get_id($session,$params);
                } else {
                        $slave_id = intval($params["slave_id"]);
                }

                if(empty($slave_id)) {
                        $this->errorMessage .= "slave_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM dns_nodes WHERE doc_id = '".$slave_id."' AND doctype_id = '".$this->slave_doctype_id."' AND status != '1'")){
                  return true;
                } else {
                  return false;
                }

        }

        function slave_status_empty($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["domain"]) && empty($params["slave_id"])) {
                        $this->errorMessage .= "Parameters: domain or slave_id are required.\r\n";
                        return false;
                }

                // Hole Slave ID
                if(empty($params["slave_id"])) {
                        $slave_id = $this->slave_get_id($session,$params);
                } else {
                        $slave_id = intval($params["slave_id"]);
                }

                if(empty($slave_id)) {
                        $this->errorMessage .= "slave_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM dns_secondary WHERE doc_id = '".$slave_id."' AND status != ''")){
                  return false;
                } else {
                  return true;
                }

        }

}
?>