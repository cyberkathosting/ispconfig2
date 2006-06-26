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

class isp_dns_slave
{

var $path_httpd_conf;
var $path_httpd_root;
var $directory_mode = "0770";
var $web_doctype_id = 1013;
var $user_doctype_id = 1014;
var $domain_doctype_id = 1015;
var $slave_doctype_id = 1028;
var $vhost_conf;
var $sendmail_cw;
var $virtusertable;
var $user_von;
var $group_von;



//Constructor
function isp_dns_slave() {

}


function slave_insert($doc_id, $doctype_id, $die_on_error = '1') {
        global $go_api, $go_info;

        // Check Ob maximale Anzahl Slave Zones des Resellers erreicht ist
        $slave = $go_api->db->queryOneRecord("select * from dns_nodes, dns_secondary where dns_secondary.doc_id = '$doc_id' and dns_nodes.doc_id = dns_secondary.doc_id and dns_nodes.doctype_id = $doctype_id");
        $resellerid = $slave["groupid"];

        if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = ".$resellerid)){
          if(!$reseller["limit_dns_manager"]){
            $status = "DELETE";
            $errorMessage = $go_api->lng("error_no_dns_anbieter");
          } else {
            if($reseller["limit_slave_dns"] >= 0){
              $reseller_slaveanzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as reseller_slaveanzahl from dns_nodes where groupid = '$resellerid' and doctype_id = '".$this->slave_doctype_id."'");
              $reseller_slaveanzahl = $reseller_slaveanzahl["reseller_slaveanzahl"];
              if($reseller_slaveanzahl > $reseller["limit_slave_dns"]) {
                $status = "DELETE";
                $errorMessage .= $go_api->lng("error_max_slave_dns_anbieter");
              }
            }
          }
        }
		
		// Check for duplicates
		$tmp_rec = $go_api->db->queryOneRecord("SELECT count(doc_id) as domain_number FROM dns_secondary where domain = '".$slave["domain"]."' AND doc_id != ".$slave["doc_id"]);
		if($tmp_rec["domain_number"] > 0) {
			$status = "DELETE";
			$errorMessage .= $go_api->lng("Duplicate record.");
		}
		unset($tmp_rec);

        if($status == "DELETE") {
        // Eintrag löschen
          $go_api->db->query("DELETE from dns_secondary where doc_id = '$doc_id'");
          $go_api->db->query("DELETE from dns_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
          if($die_on_error){
            $go_api->errorMessage($errorMessage.$go_api->lng("weiter_link"));
          } else {
            return $errorMessage;
          }
        } else {
          $go_api->db->query("UPDATE dns_secondary SET status = 'n' where doc_id = '$doc_id'");
        }


    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');

}

function slave_update($doc_id, $doctype_id, $die_on_error = '1') {
    global $go_api, $go_info;


        $go_api->db->query("UPDATE dns_secondary SET status = 'u' where doc_id = '$doc_id' and status != 'n'");

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'update');

}

function slave_delete($doc_id, $doctype_id, $action, $die_on_error = '1') {
    global $go_api, $go_info;

    if($action == "do") {
        $go_api->db->query("UPDATE dns_secondary SET status = 'd' where doc_id = '$doc_id'");
    } else {
      // Check Ob maximale Anzahl Slave Zones des Resellers erreicht ist
        $slave = $go_api->db->queryOneRecord("select * from dns_nodes, dns_secondary where dns_secondary.doc_id = '$doc_id' and dns_nodes.doc_id = dns_secondary.doc_id and dns_nodes.doctype_id = $doctype_id");
        $resellerid = $slave["groupid"];

        if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = ".$resellerid)){
          if(!$reseller["limit_dns_manager"]){
            $status = "NO_RESTORE";
            $errorMessage = $go_api->lng("error_no_dns_anbieter");
          } else {
            if($reseller["limit_slave_dns"] >= 0){
              $reseller_slaveanzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as reseller_slaveanzahl from dns_nodes where groupid = '$resellerid' and doctype_id = '".$this->slave_doctype_id."'");
              $reseller_slaveanzahl = $reseller_slaveanzahl["reseller_slaveanzahl"];
              if($reseller_slaveanzahl > $reseller["limit_slave_dns"]) {
                $status = "NO_RESTORE";
                $errorMessage .= $go_api->lng("error_max_slave_dns_anbieter");
              }
            }
          }
        }

        if($status == "NO_RESTORE"){
          $go_api->db->query("UPDATE dns_nodes SET status = '0' WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
          if($die_on_error){
            $go_api->errorMessage($errorMessage.$go_api->lng("weiter_link"));
          } else {
            return $errorMessage;
          }
        } else {
          $go_api->db->query("UPDATE dns_secondary SET status = 'n' where doc_id = '$doc_id'");
        }
    }

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'delete: '.$action);

}

}
?>