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

class reseller {

        var $errorMessage = '';
        var $reseller_doctype_id = 1022;

        /*
        #############################################################
        # Reseller
        #############################################################
        */

        /*
        Function: reseller_list
        params:   leer
        return:   Array with Reseller Arrays
        */

        function reseller_list($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["reseller_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // hole Reseller
                $resellers = $go_api->db->queryAllRecords("SELECT isp_nodes.title AS reseller_title, isp_isp_reseller.* from isp_isp_reseller, isp_nodes WHERE isp_isp_reseller.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '".$this->reseller_doctype_id."' AND isp_nodes.doctype_id = isp_isp_reseller.doctype_id ORDER BY isp_nodes.title, isp_isp_reseller.firma");

                if(!empty($resellers)){
                  foreach($resellers as $reseller) {
                        $reseller["reseller_id"] = $reseller["doc_id"];
                        $out["reseller".$reseller["doc_id"]] = $reseller;

                  }
                  return $out;
                } else {
                  $this->errorMessage .= "no records found\r\n";
                  return false;
                }

        }

        /*
        Function: reseller_get
        Params: reseller_id oder reseller_title
        Return: reseller Array
        */

        function reseller_get($session,$params) {
                global $go_api, $go_info;


                // überprüfe Rechte
                if($session["user"]["reseller_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["reseller_id"]) && empty($params["reseller_title"])) {
                        $this->errorMessage .= "Parameters: reseller_title or reseller_id are required.\r\n";
                        return false;
                }

                // Hole Reseller ID
                if(empty($params["reseller_id"])) {
                        $reseller_id = $this->reseller_get_id($session,$params);
                } else {
                        $reseller_id = intval($params["reseller_id"]);
                }

                if(empty($reseller_id)) {
                        $this->errorMessage .= "reseller_id cannot be resolved\r\n";
                        return false;
                }

                // Hole Reseller
                $reseller = $go_api->db->queryOneRecord("SELECT isp_nodes.title AS reseller_title, isp_isp_reseller.* FROM isp_nodes, isp_isp_reseller WHERE isp_isp_reseller.doc_id = $reseller_id AND isp_nodes.doc_id = isp_isp_reseller.doc_id AND isp_nodes.doctype_id = '".$this->reseller_doctype_id."' AND isp_nodes.doctype_id = isp_isp_reseller.doctype_id");

                // überprüfe ob Reseller gefunden wurde
                if(!is_array($reseller)) {
                        $this->errorMessage .= "no records found\r\n";
                        return false;
                }

                $reseller["reseller_id"] = $reseller["doc_id"];
                $out = $reseller;

                return $out;


        }

        /*
        Function: reseller_get_id
        Params: reseller_title
        Return: reseller_id
        */

        function reseller_get_id($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["reseller_query"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["reseller_title"])) {
                        $this->errorMessage .= "Parameters: reseller_title is required.\r\n";
                        return false;
                }

                // Hole Reseller ID
                $reseller_title = addslashes($params["reseller_title"]);

                if($reseller = $go_api->db->queryOneRecord("SELECT isp_isp_reseller.doc_id FROM isp_nodes, isp_isp_reseller WHERE isp_nodes.title = '$reseller_title' AND isp_isp_reseller.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '".$this->reseller_doctype_id."' AND isp_isp_reseller.doctype_id = isp_nodes.doctype_id")){
                  return $reseller["doc_id"];
                } else {
                  return false;
                }
        }

        /*
        Function: reseller_add
        Params:
        Return: reseller_id
        */

        function reseller_add($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["reseller_insert"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["reseller_title"]))                 $this->errorMessage         .= "Parameter: reseller_title is required.\r\n";
                if(empty($params["firma"]))                 $this->errorMessage         .= "Parameter: firma is required.\r\n";
                if(empty($params["anrede"]))         $this->errorMessage         .= "Parameter: anrede is required.\r\n";
                if(empty($params["vorname"]))         $this->errorMessage         .= "Parameter: vorname is required.\r\n";
                if(empty($params["name"]))         $this->errorMessage         .= "Parameter: name is required.\r\n";
                if(empty($params["strasse"]))         $this->errorMessage         .= "Parameter: strasse is required.\r\n";
                if(empty($params["plz"]))         $this->errorMessage         .= "Parameter: plz is required.\r\n";
                if(empty($params["ort"]))         $this->errorMessage         .= "Parameter: ort is required.\r\n";
                if(empty($params["province"]))         $this->errorMessage         .= "Parameter: province is required.\r\n";
                if(empty($params["land"]))         $this->errorMessage         .= "Parameter: land is required.\r\n";
                if(empty($params["telefon"]))         $this->errorMessage         .= "Parameter: telefon is required.\r\n";
                if(empty($params["reseller_user"]))         $this->errorMessage         .= "Parameter: reseller_user is required.\r\n";
                if(empty($params["reseller_passwort"]))         $this->errorMessage         .= "Parameter: reseller_passwort is required.\r\n";
                if(!isset($params["limit_user"]))         $this->errorMessage         .= "Parameter: limit_user is required.\r\n";
                if(!isset($params["limit_disk"]))         $this->errorMessage         .= "Parameter: limit_disk is required.\r\n";
                if(!isset($params["limit_web"]))         $this->errorMessage         .= "Parameter: limit_web is required.\r\n";
                if(!isset($params["limit_domain"]))         $this->errorMessage         .= "Parameter: limit_domain is required.\r\n";
                if(!isset($params["limit_httpd_include"]))         $this->errorMessage         .= "Parameter: limit_httpd_include is required.\r\n";
                if(!isset($params["limit_dns_manager"]))         $this->errorMessage         .= "Parameter: limit_dns_manager is required.\r\n";
                if(!isset($params["limit_domain_dns"]))         $this->errorMessage         .= "Parameter: limit_domain_dns is required.\r\n";
                if(!isset($params["limit_shell_access"]))         $this->errorMessage         .= "Parameter: limit_shell_access is required.\r\n";
                if(!isset($params["limit_cgi"]))         $this->errorMessage         .= "Parameter: limit_cgi is required.\r\n";
                if(!isset($params["limit_php"]))         $this->errorMessage         .= "Parameter: limit_php is required.\r\n";
                if(!isset($params["limit_ssi"]))         $this->errorMessage         .= "Parameter: limit_ssi is required.\r\n";
                if(!isset($params["limit_ftp"]))         $this->errorMessage         .= "Parameter: limit_ftp is required.\r\n";
                if(!isset($params["limit_mysql"]))         $this->errorMessage         .= "Parameter: limit_mysql is required.\r\n";
                if(!isset($params["limit_ssl"]))         $this->errorMessage         .= "Parameter: limit_ssl is required.\r\n";
                if(!isset($params["limit_anonftp"]))         $this->errorMessage         .= "Parameter: limit_anonftp is required.\r\n";
                if(!isset($params["limit_standard_cgis"]))         $this->errorMessage         .= "Parameter: limit_standard_cgis is required.\r\n";
                if(!isset($params["limit_wap"]))         $this->errorMessage         .= "Parameter: limit_wap is required.\r\n";
                if(!isset($params["limit_error_pages"]))         $this->errorMessage         .= "Parameter: limit_error_pages is required.\r\n";
                if(!isset($params["limit_frontpage"]))         $params["limit_frontpage"] = 0;
                if(!isset($params["limit_mysql_anzahl_dbs"]))         $params["limit_mysql_anzahl_dbs"]   = 0;
                if(!isset($params["limit_slave_dns"]))         $this->errorMessage         .= "Parameter: limit_slave_dns is required.\r\n";

                if($this->errorMessage != '') return false;

                if($params["limit_frontpage"] != 0){
                  $server_conf = $go_api->db->queryOneRecord("SELECT * from isp_server");
                  if($server_conf["server_enable_frontpage"] == 0) $params["limit_frontpage"] = 0;
                }

                // checke ob Reseller bereits existiert
                $reseller_id = $this->reseller_get_id($session,$params);
                if(!empty($reseller_id)) {
                        $this->errorMessage .= "Reseller: ".$params["reseller_title"]." already exists\r\n";
                        return false;
                }

                // Record einfügen
                $sql = "INSERT INTO isp_isp_reseller (
                                doctype_id,
                                firma,
                                vorname,
                                limit_user,
                                limit_disk,
                                limit_web,
                                limit_domain,
                                name,
                                strasse,
                                plz,
                                ort,
                                telefon,
                                fax,
                                email,
                                internet,
                                reseller_user,
                                reseller_passwort,
                                anrede,
                                land,
                                limit_httpd_include,
                                limit_dns_manager,
                                limit_domain_dns,
                                province,
                                limit_shell_access,
                                limit_cgi,
                                limit_php,
                                limit_ssi,
                                limit_ftp,
                                limit_mysql,
                                limit_ssl,
                                limit_anonftp,
                                limit_standard_cgis,
                                limit_wap,
                                limit_error_pages,
                                limit_frontpage,
                                limit_mysql_anzahl_dbs,
                                limit_slave_dns,
                                client_salutatory_email_sender_email,
                                client_salutatory_email_sender_name,
                                client_salutatory_email_bcc,
                                client_salutatory_email_subject,
                                client_salutatory_email_message,
                                standard_index,
                                user_standard_index
                                ) VALUES (
                                '".$this->reseller_doctype_id."',
                                '".addslashes($params["firma"])."',
                                '".addslashes($params["vorname"])."',
                                '".intval($params["limit_user"])."',
                                '".intval($params["limit_disk"])."',
                                '".intval($params["limit_web"])."',
                                '".intval($params["limit_domain"])."',
                                '".addslashes($params["name"])."',
                                '".addslashes($params["strasse"])."',
                                '".addslashes($params["plz"])."',
                                '".addslashes($params["ort"])."',
                                '".addslashes($params["telefon"])."',
                                '".addslashes($params["fax"])."',
                                '".addslashes($params["email"])."',
                                '".addslashes($params["internet"])."',
                                '".addslashes($params["reseller_user"])."',
                                '".addslashes($params["reseller_passwort"])."',
                                '".addslashes($params["anrede"])."',
                                '".addslashes($params["land"])."',
                                '".intval($params["limit_httpd_include"])."',
                                '".intval($params["limit_dns_manager"])."',
                                '".intval($params["limit_domain_dns"])."',
                                '".addslashes($params["province"])."',
                                '".intval($params["limit_shell_access"])."',
                                '".intval($params["limit_cgi"])."',
                                '".intval($params["limit_php"])."',
                                '".intval($params["limit_ssi"])."',
                                '".intval($params["limit_ftp"])."',
                                '".intval($params["limit_mysql"])."',
                                '".intval($params["limit_ssl"])."',
                                '".intval($params["limit_anonftp"])."',
                                '".intval($params["limit_standard_cgis"])."',
                                '".intval($params["limit_wap"])."',
                                '".intval($params["limit_error_pages"])."',
                                '".intval($params["limit_frontpage"])."',
                                '".intval($params["limit_mysql_anzahl_dbs"])."',
                                '".intval($params["limit_slave_dns"])."',
                                '".addslashes($params["client_salutatory_email_sender_email"])."',
                                '".addslashes($params["client_salutatory_email_sender_name"])."',
                                '".addslashes($params["client_salutatory_email_bcc"])."',
                                '".addslashes($params["client_salutatory_email_subject"])."',
                                '".addslashes($params["client_salutatory_email_message"])."',
                                '".addslashes($params["standard_index"])."',
                                '".addslashes($params["user_standard_index"])."'
                                )";

                $go_api->db->query($sql);
                $reseller_id = $doc_id = $go_api->db->insertID();

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
                                1,
                                '1',
                                'i',
                                ".$this->reseller_doctype_id.",
                                '1',
                                'anbieter.gif',
                                '',
                                $doc_id,
                                '".addslashes($params["reseller_title"])."'
                                )";

                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->reseller_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_insert != "") {
                    $event_insert = $doc->event_insert;
                    $this->errorMessage .= $go_api->$event_class->$event_insert($doc_id,$this->reseller_doctype_id, 0);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($doc_id,$this->reseller_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;
                return $reseller_id;
        }

        function reseller_update($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["reseller_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["reseller_id"]) && empty($params["reseller_title"]))         $this->errorMessage         .= "Parameter: reseller_id or reseller_title is required.\r\n";
                if($this->errorMessage != "") return false;

                // Hole Reseller ID
                if(empty($params["reseller_id"])) {
                        $reseller_id = $this->reseller_get_id($session,$params);
                } else {
                        $reseller_id = intval($params["reseller_id"]);
                }

                if(empty($reseller_id)) {
                        $this->errorMessage .= "reseller_id cannot be resolved\r\n";
                        return false;
                }

                // Liegt Objekt im Papierkorb?
                if($this->reseller_is_suspended($session,$params)){
                  $this->errorMessage .= "Reseller is suspended.\r\n";
                  return false;
                }

                $fields = $go_api->db->queryAllRecords("SHOW COLUMNS FROM isp_isp_reseller");
                foreach($fields as $field){
                  $field_arr[] = $field["Field"];
                }

                $changes = "";
                foreach($params as $key => $val){
                  if($key != "reseller_id" && $key != "reseller_title" && $key != "reseller_user" && $key != "doc_id" && $key != "doctype_id" && $key != "reseller_group" && $key != "reseller_userid" && in_array($key, $field_arr)) $changes .= $key." = '".addslashes($val)."',";
                }
                $changes = substr($changes, 0, -1);

                $sql = "UPDATE isp_isp_reseller SET ".$changes." WHERE doc_id = ".$reseller_id;

                if(!$go_api->db->query($sql)) $this->errorMessage  .= "Database could not be updated.\r\n";
                if($this->errorMessage != '') return false;

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->reseller_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_update != "") {
                    $event_update = $doc->event_update;
                    $this->errorMessage .= $go_api->$event_class->$event_update($reseller_id,$this->reseller_doctype_id, 0);
                  }

                }

                /////////////////////////
                if($this->errorMessage != "") return false;

                return $this->reseller_get($session,$params);
        }

        function reseller_suspend($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["reseller_update"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["reseller_title"]) && empty($params["reseller_id"])) {
                        $this->errorMessage .= "Parameters: reseller_title or reseller_id are required.\r\n";
                        return false;
                }

                // Hole Reseller ID
                if(empty($params["reseller_id"])) {
                        $reseller_id = $this->reseller_get_id($session,$params);
                } else {
                        $reseller_id = intval($params["reseller_id"]);
                }

                if(empty($reseller_id)) {
                        $this->errorMessage .= "reseller_id cannot be resolved\r\n";
                        return false;
                }

                if($this->reseller_is_suspended($session,$params)) return true;

                // suspend durchführen
                $sql = "UPDATE isp_nodes SET status = 0 WHERE doc_id = $reseller_id and doctype_id = '".$this->reseller_doctype_id."'";
                $go_api->db->query($sql);

                /////////////////////////
                $go_api->uses('doc,auth,log');
                $go_info["user"]["licence"]["p"] = "42go_isp_pro";
                $go_info["user"]["perms"] = 'rw';
                $go_info["user"]["userid"] = 1;
                $go_api->language = "en";

                if(!$doc = $go_api->doc->doctype_get($this->reseller_doctype_id)) $this->errorMessage .= $go_api->lng("error_doctype_nicht_vorhanden");

                if($doc->event_class != "") {
                  $event_class = $doc->event_class;

                  if(!class_exists($event_class)){
                    $go_api->uses($doc->event_class);
                  }

                  if($doc->event_delete != "") {
                    $event_delete = $doc->event_delete;
                    $this->errorMessage .= $go_api->$event_class->$event_delete($reseller_id,$this->reseller_doctype_id, 'do', 0);
                  }

                }
                /////////////////////////
                if($this->errorMessage != "") return false;

                return true;
        }

        function reseller_delete($session,$params) {
                global $go_api, $go_info;

                // überprüfe Rechte
                if($session["user"]["reseller_delete"] != 1) {
                        $this->errorMessage .= "method not allowed for user\r\n";
                        return false;
                }

                // überprüfe Parameter
                if(empty($params["reseller_title"]) && empty($params["reseller_id"])) {
                        $this->errorMessage .= "Parameters: reseller_title or reseller_id are required.\r\n";
                        return false;
                }

                // Hole Reseller ID
                if(empty($params["reseller_id"])) {
                        $reseller_id = $this->reseller_get_id($session,$params);
                } else {
                        $reseller_id = intval($params["reseller_id"]);
                }

                if(empty($reseller_id)) {
                        $this->errorMessage .= "reseller_id cannot be resolved\r\n";
                        return false;
                }

                // Ist Reseller suspended?
                if(!$this->reseller_is_suspended($session,$params)) $this->reseller_suspend($session,$params);

                // DELETE durchführen
                $sql = "DELETE isp_nodes.*, isp_isp_reseller.* FROM isp_nodes, isp_isp_reseller WHERE isp_nodes.doc_id = '$reseller_id' and isp_nodes.doctype_id = '".$this->reseller_doctype_id."' and isp_nodes.status != '1' AND isp_nodes.doc_id = isp_isp_reseller.doc_id AND isp_nodes.doctype_id = isp_isp_reseller.doctype_id";
                return $go_api->db->query($sql);
        }

        //////////////////////////// Help Functions //////////////////////////////

        function reseller_is_suspended($session,$params) {
                global $go_api, $go_info;

                // überprüfe Parameter
                if(empty($params["reseller_title"]) && empty($params["reseller_id"])) {
                        $this->errorMessage .= "Parameters: reseller_title or reseller_id are required.\r\n";
                        return false;
                }

                // Hole Reseller ID
                if(empty($params["reseller_id"])) {
                        $reseller_id = $this->reseller_get_id($session,$params);
                } else {
                        $reseller_id = intval($params["reseller_id"]);
                }

                if(empty($reseller_id)) {
                        $this->errorMessage .= "reseller_id cannot be resolved\r\n";
                        return false;
                }

                if($go_api->db->queryOneRecord("SELECT * FROM isp_nodes WHERE doc_id = '".$reseller_id."' AND doctype_id = '".$this->reseller_doctype_id."' AND status != '1'")){
                  return true;
                } else {
                  return false;
                }

        }

}
?>