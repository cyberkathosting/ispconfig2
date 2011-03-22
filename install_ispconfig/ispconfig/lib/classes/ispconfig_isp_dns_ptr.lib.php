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

class isp_dns_ptr
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
function isp_dns_ptr() {

}


function ptr_insert($doc_id, $doctype_id, $die_on_error = '1') {
        global $go_api, $go_info;

    // Remove http:// and https:// and spaces from hosts
    if($tmp_ptrs = $go_api->db->queryAllRecords("SELECT * FROM dns_ptr WHERE hostname LIKE 'http://%' OR hostname LIKE 'https://%' OR hostname LIKE ' %' OR hostname LIKE '% ' OR ip_address LIKE 'http://%' OR ip_address LIKE 'https://%' OR ip_address LIKE ' %' OR ip_address LIKE '% '")){
      foreach($tmp_ptrs as $tmp_ptr){
        $tmp_ptr['hostname'] = str_replace('http://', '', $tmp_ptr['hostname']);
        $tmp_ptr['hostname'] = str_replace('https://', '', $tmp_ptr['hostname']);
        $tmp_ptr['hostname'] = trim($tmp_ptr['hostname']);
        $tmp_ptr['ip_address'] = str_replace('http://', '', $tmp_ptr['ip_address']);
        $tmp_ptr['ip_address'] = str_replace('https://', '', $tmp_ptr['ip_address']);
        $tmp_ptr['ip_address'] = trim($tmp_ptr['ip_address']);
        if(substr($tmp_ptr['hostname'],-1) == '.') $tmp_ptr['hostname'] = substr($tmp_ptr['hostname'],0,-1);
        $go_api->db->query("UPDATE dns_ptr SET hostname = '".$tmp_ptr['hostname']."', ip_address = '".$tmp_ptr['ip_address']."' WHERE doc_id = ".$tmp_ptr['doc_id']);
      }
      unset($tmp_ptrs);
    }

    $ptr = $go_api->db->queryOneRecord("select * from dns_nodes,dns_ptr where dns_ptr.doc_id = '$doc_id' and dns_nodes.doc_id = dns_ptr.doc_id and dns_nodes.doctype_id = $doctype_id");
    if (!preg_match("/^([a-z0-9\-]+\.)+[a-z0-9\-]{2,8}$/ix", $ptr["hostname"])) {
        $go_api->db->query("DELETE from dns_ptr where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from dns_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        if($die_on_error){
          $go_api->errorMessage($go_api->lng("Invalid hostname").': '.$ptr["hostname"].$go_api->lng("weiter_link"));
        } else {
          return $go_api->lng("Invalid hostname").': '.$ptr["hostname"];
        }
    }

        $go_api->db->query("UPDATE dns_ptr SET status = 'n' where doc_id = '$doc_id'");


    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');

}

function ptr_update($doc_id, $doctype_id, $die_on_error = '1') {
    global $go_api, $go_info, $old_form_data;

    // Remove http:// and https:// and spaces from hosts
    if($tmp_ptrs = $go_api->db->queryAllRecords("SELECT * FROM dns_ptr WHERE hostname LIKE 'http://%' OR hostname LIKE 'https://%' OR hostname LIKE ' %' OR hostname LIKE '% ' OR ip_address LIKE 'http://%' OR ip_address LIKE 'https://%' OR ip_address LIKE ' %' OR ip_address LIKE '% '")){
      foreach($tmp_ptrs as $tmp_ptr){
        $tmp_ptr['hostname'] = str_replace('http://', '', $tmp_ptr['hostname']);
        $tmp_ptr['hostname'] = str_replace('https://', '', $tmp_ptr['hostname']);
        $tmp_ptr['hostname'] = trim($tmp_ptr['hostname']);
        $tmp_ptr['ip_address'] = str_replace('http://', '', $tmp_ptr['ip_address']);
        $tmp_ptr['ip_address'] = str_replace('https://', '', $tmp_ptr['ip_address']);
        $tmp_ptr['ip_address'] = trim($tmp_ptr['ip_address']);
        if(substr($tmp_ptr['hostname'],-1) == '.') $tmp_ptr['hostname'] = substr($tmp_ptr['hostname'],0,-1);
        $go_api->db->query("UPDATE dns_ptr SET hostname = '".$tmp_ptr['hostname']."', ip_address = '".$tmp_ptr['ip_address']."' WHERE doc_id = ".$tmp_ptr['doc_id']);
      }
      unset($tmp_ptrs);
    }

    $ptr = $go_api->db->queryOneRecord("select * from dns_nodes,dns_ptr where dns_ptr.doc_id = '$doc_id' and dns_nodes.doc_id = dns_ptr.doc_id and dns_nodes.doctype_id = $doctype_id");
    if (!preg_match("/^([a-z0-9\-]+\.)+[a-z0-9\-]{2,8}$/ix", $ptr["hostname"])) {
        $old_ptr_hostname = addslashes($old_form_data["hostname"]);
        $go_api->db->query("UPDATE dns_ptr SET hostname = '$old_ptr_hostname' where doc_id = '$doc_id'");
        if($die_on_error){
          $go_api->errorMessage($go_api->lng("Invalid hostname").': '.$ptr["hostname"].$go_api->lng("weiter_link"));
        } else {
          return $go_api->lng("Invalid hostname").': '.$ptr["hostname"];
        }
    }

        $go_api->db->query("UPDATE dns_ptr SET status = 'u' where doc_id = '$doc_id' and status != 'n'");

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'update');

}

function ptr_delete($doc_id, $doctype_id, $action, $die_on_error = '1') {
    global $go_api, $go_info;

    if($action == "do") {
        $go_api->db->query("UPDATE dns_ptr SET status = 'd' where doc_id = '$doc_id'");
    } else {
       $go_api->db->query("UPDATE dns_ptr SET status = 'n' where doc_id = '$doc_id'");
    }

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'delete: '.$action);

}

}
?>