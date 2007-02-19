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

        // Remove http:// and https:// and spaces from domains and hosts
    if($tmp_as = $go_api->db->queryAllRecords("SELECT * FROM dns_a WHERE host LIKE 'http://%' OR host LIKE 'https://%' OR host LIKE ' %' OR host LIKE '% ' OR ip_adresse LIKE 'http://%' OR ip_adresse LIKE 'https://%' OR ip_adresse LIKE ' %' OR ip_adresse LIKE '% '")){
      foreach($tmp_as as $tmp_a){
        $tmp_a['host'] = str_replace('http://', '', $tmp_a['host']);
        $tmp_a['host'] = str_replace('https://', '', $tmp_a['host']);
        $tmp_a['host'] = trim($tmp_a['host']);
        $tmp_a['ip_adresse'] = str_replace('http://', '', $tmp_a['ip_adresse']);
        $tmp_a['ip_adresse'] = str_replace('https://', '', $tmp_a['ip_adresse']);
        $tmp_a['ip_adresse'] = trim($tmp_a['ip_adresse']);
        $go_api->db->query("UPDATE dns_a SET host = '".$tmp_a['host']."', ip_adresse = '".$tmp_a['ip_adresse']."' WHERE doc_id = ".$tmp_a['doc_id']);
      }
      unset($tmp_as);
    }

    if($tmp_cnames = $go_api->db->queryAllRecords("SELECT * FROM dns_cname WHERE host LIKE 'http://%' OR host LIKE 'https://%' OR host LIKE ' %' OR host LIKE '% ' OR ziel LIKE 'http://%' OR ziel LIKE 'https://%' OR ziel LIKE ' %' OR ziel LIKE '% '")){
      foreach($tmp_cnames as $tmp_cname){
        $tmp_cname['host'] = str_replace('http://', '', $tmp_cname['host']);
        $tmp_cname['host'] = str_replace('https://', '', $tmp_cname['host']);
        $tmp_cname['host'] = trim($tmp_cname['host']);
        $tmp_cname['ziel'] = str_replace('http://', '', $tmp_cname['ziel']);
        $tmp_cname['ziel'] = str_replace('https://', '', $tmp_cname['ziel']);
        $tmp_cname['ziel'] = trim($tmp_cname['ziel']);
        $go_api->db->query("UPDATE dns_cname SET host = '".$tmp_cname['host']."', ziel = '".$tmp_cname['ziel']."' WHERE doc_id = ".$tmp_cname['doc_id']);
      }
      unset($tmp_cnames);
    }

    if($tmp_dns_isp_dnss = $go_api->db->queryAllRecords("SELECT * FROM dns_isp_dns WHERE dns_soa LIKE 'http://%' OR dns_soa LIKE 'https://%' OR dns_soa LIKE ' %' OR dns_soa LIKE '% ' OR dns_ns1 LIKE 'http://%' OR dns_ns1 LIKE 'https://%' OR dns_ns1 LIKE ' %' OR dns_ns1 LIKE '% ' OR dns_ns2 LIKE 'http://%' OR dns_ns2 LIKE 'https://%' OR dns_ns2 LIKE ' %' OR dns_ns2 LIKE '% ' OR dns_soa_ip LIKE 'http://%' OR dns_soa_ip LIKE 'https://%' OR dns_soa_ip LIKE ' %' OR dns_soa_ip LIKE '% '")){
      foreach($tmp_dns_isp_dnss as $tmp_dns_isp_dns){
        $tmp_dns_isp_dns['dns_soa'] = str_replace('http://', '', $tmp_dns_isp_dns['dns_soa']);
        $tmp_dns_isp_dns['dns_soa'] = str_replace('https://', '', $tmp_dns_isp_dns['dns_soa']);
        $tmp_dns_isp_dns['dns_soa'] = trim($tmp_dns_isp_dns['dns_soa']);
        $tmp_dns_isp_dns['dns_ns1'] = str_replace('http://', '', $tmp_dns_isp_dns['dns_ns1']);
        $tmp_dns_isp_dns['dns_ns1'] = str_replace('https://', '', $tmp_dns_isp_dns['dns_ns1']);
        $tmp_dns_isp_dns['dns_ns1'] = trim($tmp_dns_isp_dns['dns_ns1']);
        $tmp_dns_isp_dns['dns_ns2'] = str_replace('http://', '', $tmp_dns_isp_dns['dns_ns2']);
        $tmp_dns_isp_dns['dns_ns2'] = str_replace('https://', '', $tmp_dns_isp_dns['dns_ns2']);
        $tmp_dns_isp_dns['dns_ns2'] = trim($tmp_dns_isp_dns['dns_ns2']);
        $tmp_dns_isp_dns['dns_soa_ip'] = str_replace('http://', '', $tmp_dns_isp_dns['dns_soa_ip']);
        $tmp_dns_isp_dns['dns_soa_ip'] = str_replace('https://', '', $tmp_dns_isp_dns['dns_soa_ip']);
        $tmp_dns_isp_dns['dns_soa_ip'] = trim($tmp_dns_isp_dns['dns_soa_ip']);
        $go_api->db->query("UPDATE dns_isp_dns SET dns_soa = '".$tmp_dns_isp_dns['dns_soa']."', dns_ns1 = '".$tmp_dns_isp_dns['dns_ns1']."', dns_ns2 = '".$tmp_dns_isp_dns['dns_ns2']."', dns_soa_ip = '".$tmp_dns_isp_dns['dns_soa_ip']."' WHERE doc_id = ".$tmp_dns_isp_dns['doc_id']);
      }
      unset($tmp_dns_isp_dnss);
    }

    if($tmp_mxs = $go_api->db->queryAllRecords("SELECT * FROM dns_mx WHERE host LIKE 'http://%' OR host LIKE 'https://%' OR host LIKE ' %' OR host LIKE '% ' OR mailserver LIKE 'http://%' OR mailserver LIKE 'https://%' OR mailserver LIKE ' %' OR mailserver LIKE '% '")){
      foreach($tmp_mxs as $tmp_mx){
        $tmp_mx['host'] = str_replace('http://', '', $tmp_mx['host']);
        $tmp_mx['host'] = str_replace('https://', '', $tmp_mx['host']);
        $tmp_mx['host'] = trim($tmp_mx['host']);
        $tmp_mx['mailserver'] = str_replace('http://', '', $tmp_mx['mailserver']);
        $tmp_mx['mailserver'] = str_replace('https://', '', $tmp_mx['mailserver']);
        $tmp_mx['mailserver'] = trim($tmp_mx['mailserver']);
        $go_api->db->query("UPDATE dns_mx SET host = '".$tmp_mx['host']."', mailserver = '".$tmp_mx['mailserver']."' WHERE doc_id = ".$tmp_mx['doc_id']);
      }
      unset($tmp_mxs);
    }

    if($tmp_secondaries = $go_api->db->queryAllRecords("SELECT * FROM dns_secondary WHERE domain LIKE 'http://%' OR domain LIKE 'https://%' OR domain LIKE ' %' OR domain LIKE '% ' OR master_ip LIKE 'http://%' OR master_ip LIKE 'https://%' OR master_ip LIKE ' %' OR master_ip LIKE '% '")){
      foreach($tmp_secondaries as $tmp_secondary){
        $tmp_secondary['domain'] = str_replace('http://', '', $tmp_secondary['domain']);
        $tmp_secondary['domain'] = str_replace('https://', '', $tmp_secondary['domain']);
        $tmp_secondary['domain'] = trim($tmp_secondary['domain']);
        $tmp_secondary['master_ip'] = str_replace('http://', '', $tmp_secondary['master_ip']);
        $tmp_secondary['master_ip'] = str_replace('https://', '', $tmp_secondary['master_ip']);
        $tmp_secondary['master_ip'] = trim($tmp_secondary['master_ip']);
        $go_api->db->query("UPDATE dns_secondary SET domain = '".$tmp_secondary['domain']."', master_ip = '".$tmp_secondary['master_ip']."' WHERE doc_id = ".$tmp_secondary['doc_id']);
      }
      unset($tmp_secondaries);
    }

    if($tmp_spfs = $go_api->db->queryAllRecords("SELECT * FROM dns_spf WHERE host LIKE 'http://%' OR host LIKE 'https://%' OR host LIKE ' %' OR host LIKE '% '")){
      foreach($tmp_spfs as $tmp_spf){
        $tmp_spf['host'] = str_replace('http://', '', $tmp_spf['host']);
        $tmp_spf['host'] = str_replace('https://', '', $tmp_spf['host']);
        $tmp_spf['host'] = trim($tmp_spf['host']);
        $go_api->db->query("UPDATE dns_spf SET host = '".$tmp_spf['host']."' WHERE doc_id = ".$tmp_spf['doc_id']);
      }
      unset($tmp_spfs);
    }

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

    // Remove http:// and https:// and spaces from domains and hosts
    if($tmp_as = $go_api->db->queryAllRecords("SELECT * FROM dns_a WHERE host LIKE 'http://%' OR host LIKE 'https://%' OR host LIKE ' %' OR host LIKE '% ' OR ip_adresse LIKE 'http://%' OR ip_adresse LIKE 'https://%' OR ip_adresse LIKE ' %' OR ip_adresse LIKE '% '")){
      foreach($tmp_as as $tmp_a){
        $tmp_a['host'] = str_replace('http://', '', $tmp_a['host']);
        $tmp_a['host'] = str_replace('https://', '', $tmp_a['host']);
        $tmp_a['host'] = trim($tmp_a['host']);
        $tmp_a['ip_adresse'] = str_replace('http://', '', $tmp_a['ip_adresse']);
        $tmp_a['ip_adresse'] = str_replace('https://', '', $tmp_a['ip_adresse']);
        $tmp_a['ip_adresse'] = trim($tmp_a['ip_adresse']);
        $go_api->db->query("UPDATE dns_a SET host = '".$tmp_a['host']."', ip_adresse = '".$tmp_a['ip_adresse']."' WHERE doc_id = ".$tmp_a['doc_id']);
      }
      unset($tmp_as);
    }

    if($tmp_cnames = $go_api->db->queryAllRecords("SELECT * FROM dns_cname WHERE host LIKE 'http://%' OR host LIKE 'https://%' OR host LIKE ' %' OR host LIKE '% ' OR ziel LIKE 'http://%' OR ziel LIKE 'https://%' OR ziel LIKE ' %' OR ziel LIKE '% '")){
      foreach($tmp_cnames as $tmp_cname){
        $tmp_cname['host'] = str_replace('http://', '', $tmp_cname['host']);
        $tmp_cname['host'] = str_replace('https://', '', $tmp_cname['host']);
        $tmp_cname['host'] = trim($tmp_cname['host']);
        $tmp_cname['ziel'] = str_replace('http://', '', $tmp_cname['ziel']);
        $tmp_cname['ziel'] = str_replace('https://', '', $tmp_cname['ziel']);
        $tmp_cname['ziel'] = trim($tmp_cname['ziel']);
        $go_api->db->query("UPDATE dns_cname SET host = '".$tmp_cname['host']."', ziel = '".$tmp_cname['ziel']."' WHERE doc_id = ".$tmp_cname['doc_id']);
      }
      unset($tmp_cnames);
    }

    if($tmp_dns_isp_dnss = $go_api->db->queryAllRecords("SELECT * FROM dns_isp_dns WHERE dns_soa LIKE 'http://%' OR dns_soa LIKE 'https://%' OR dns_soa LIKE ' %' OR dns_soa LIKE '% ' OR dns_ns1 LIKE 'http://%' OR dns_ns1 LIKE 'https://%' OR dns_ns1 LIKE ' %' OR dns_ns1 LIKE '% ' OR dns_ns2 LIKE 'http://%' OR dns_ns2 LIKE 'https://%' OR dns_ns2 LIKE ' %' OR dns_ns2 LIKE '% ' OR dns_soa_ip LIKE 'http://%' OR dns_soa_ip LIKE 'https://%' OR dns_soa_ip LIKE ' %' OR dns_soa_ip LIKE '% '")){
      foreach($tmp_dns_isp_dnss as $tmp_dns_isp_dns){
        $tmp_dns_isp_dns['dns_soa'] = str_replace('http://', '', $tmp_dns_isp_dns['dns_soa']);
        $tmp_dns_isp_dns['dns_soa'] = str_replace('https://', '', $tmp_dns_isp_dns['dns_soa']);
        $tmp_dns_isp_dns['dns_soa'] = trim($tmp_dns_isp_dns['dns_soa']);
        $tmp_dns_isp_dns['dns_ns1'] = str_replace('http://', '', $tmp_dns_isp_dns['dns_ns1']);
        $tmp_dns_isp_dns['dns_ns1'] = str_replace('https://', '', $tmp_dns_isp_dns['dns_ns1']);
        $tmp_dns_isp_dns['dns_ns1'] = trim($tmp_dns_isp_dns['dns_ns1']);
        $tmp_dns_isp_dns['dns_ns2'] = str_replace('http://', '', $tmp_dns_isp_dns['dns_ns2']);
        $tmp_dns_isp_dns['dns_ns2'] = str_replace('https://', '', $tmp_dns_isp_dns['dns_ns2']);
        $tmp_dns_isp_dns['dns_ns2'] = trim($tmp_dns_isp_dns['dns_ns2']);
        $tmp_dns_isp_dns['dns_soa_ip'] = str_replace('http://', '', $tmp_dns_isp_dns['dns_soa_ip']);
        $tmp_dns_isp_dns['dns_soa_ip'] = str_replace('https://', '', $tmp_dns_isp_dns['dns_soa_ip']);
        $tmp_dns_isp_dns['dns_soa_ip'] = trim($tmp_dns_isp_dns['dns_soa_ip']);
        $go_api->db->query("UPDATE dns_isp_dns SET dns_soa = '".$tmp_dns_isp_dns['dns_soa']."', dns_ns1 = '".$tmp_dns_isp_dns['dns_ns1']."', dns_ns2 = '".$tmp_dns_isp_dns['dns_ns2']."', dns_soa_ip = '".$tmp_dns_isp_dns['dns_soa_ip']."' WHERE doc_id = ".$tmp_dns_isp_dns['doc_id']);
      }
      unset($tmp_dns_isp_dnss);
    }

    if($tmp_mxs = $go_api->db->queryAllRecords("SELECT * FROM dns_mx WHERE host LIKE 'http://%' OR host LIKE 'https://%' OR host LIKE ' %' OR host LIKE '% ' OR mailserver LIKE 'http://%' OR mailserver LIKE 'https://%' OR mailserver LIKE ' %' OR mailserver LIKE '% '")){
      foreach($tmp_mxs as $tmp_mx){
        $tmp_mx['host'] = str_replace('http://', '', $tmp_mx['host']);
        $tmp_mx['host'] = str_replace('https://', '', $tmp_mx['host']);
        $tmp_mx['host'] = trim($tmp_mx['host']);
        $tmp_mx['mailserver'] = str_replace('http://', '', $tmp_mx['mailserver']);
        $tmp_mx['mailserver'] = str_replace('https://', '', $tmp_mx['mailserver']);
        $tmp_mx['mailserver'] = trim($tmp_mx['mailserver']);
        $go_api->db->query("UPDATE dns_mx SET host = '".$tmp_mx['host']."', mailserver = '".$tmp_mx['mailserver']."' WHERE doc_id = ".$tmp_mx['doc_id']);
      }
      unset($tmp_mxs);
    }

    if($tmp_secondaries = $go_api->db->queryAllRecords("SELECT * FROM dns_secondary WHERE domain LIKE 'http://%' OR domain LIKE 'https://%' OR domain LIKE ' %' OR domain LIKE '% ' OR master_ip LIKE 'http://%' OR master_ip LIKE 'https://%' OR master_ip LIKE ' %' OR master_ip LIKE '% '")){
      foreach($tmp_secondaries as $tmp_secondary){
        $tmp_secondary['domain'] = str_replace('http://', '', $tmp_secondary['domain']);
        $tmp_secondary['domain'] = str_replace('https://', '', $tmp_secondary['domain']);
        $tmp_secondary['domain'] = trim($tmp_secondary['domain']);
        $tmp_secondary['master_ip'] = str_replace('http://', '', $tmp_secondary['master_ip']);
        $tmp_secondary['master_ip'] = str_replace('https://', '', $tmp_secondary['master_ip']);
        $tmp_secondary['master_ip'] = trim($tmp_secondary['master_ip']);
        $go_api->db->query("UPDATE dns_secondary SET domain = '".$tmp_secondary['domain']."', master_ip = '".$tmp_secondary['master_ip']."' WHERE doc_id = ".$tmp_secondary['doc_id']);
      }
      unset($tmp_secondaries);
    }

    if($tmp_spfs = $go_api->db->queryAllRecords("SELECT * FROM dns_spf WHERE host LIKE 'http://%' OR host LIKE 'https://%' OR host LIKE ' %' OR host LIKE '% '")){
      foreach($tmp_spfs as $tmp_spf){
        $tmp_spf['host'] = str_replace('http://', '', $tmp_spf['host']);
        $tmp_spf['host'] = str_replace('https://', '', $tmp_spf['host']);
        $tmp_spf['host'] = trim($tmp_spf['host']);
        $go_api->db->query("UPDATE dns_spf SET host = '".$tmp_spf['host']."' WHERE doc_id = ".$tmp_spf['doc_id']);
      }
      unset($tmp_spfs);
    }


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