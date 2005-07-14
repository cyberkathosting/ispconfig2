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
class isp_dns
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



//Constructor
function isp_dns() {
global $go_api, $go_info;

$server_conf = $go_api->db->queryOneRecord("SELECT * from isp_server");
$this->path_httpd_conf = stripslashes($server_conf["server_path_httpd_conf"]);
$this->path_httpd_root = stripslashes($server_conf["server_path_httpd_root"]);
$this->vhost_conf = $this->path_httpd_conf . $go_info["server"]["dir_trenner"].'vhost'.$go_info["server"]["dir_trenner"].'vhost.conf';
$this->user_von = $server_conf["userid_von"];
$this->group_von = $server_conf["groupid_von"];
$this->virtusertable = $server_conf["server_sendmail_virtuser_datei"];
$this->sendmail_cw = $server_conf["server_sendmail_cw"];
}


function soa_insert($doc_id, $doctype_id, $die_on_error = '1') {
global $go_api, $go_info;

    $aufruf = '';

    if($doctype_id == 1016) {
        $doc_id = $doc_id;
        $doctype_id = $doctype_id;
        $aufruf = 'soa';
    } else {
        // Eltern Eintrag finden
        $sql = "SELECT * FROM dns_dep where child_doc_id = $doc_id and child_doctype_id = $doctype_id";
        $dep_row = $go_api->db->queryOneRecord($sql);
        $doc_id = $dep_row["parent_doc_id"];
        $doctype_id = $dep_row["parent_doctype_id"];
        $go_api->db->query("UPDATE dns_nodes SET groupid = '".$dep_row["groupid"]."' WHERE  doc_id = '".$dep_row["child_doc_id"]."' AND doctype_id = '".$dep_row["child_doctype_id"]."'");
        $aufruf = 'child';

        switch ($dep_row["child_doctype_id"]) {

        case 1018:

          $a_record = $go_api->db->queryOneRecord("SELECT * FROM dns_a WHERE doc_id = '".$dep_row["child_doc_id"]."'");
          // checke ob A-Record schon existiert
          $sql = "SELECT dns_a.doc_id FROM dns_dep, dns_a where dns_a.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '1018' and dns_dep.parent_doc_id = $doc_id and dns_dep.parent_doctype_id = '1016' and dns_a.host = '".$a_record["host"]."' and dns_a.doc_id != '".$dep_row["child_doc_id"]."'";

          $tmp = $go_api->db->queryOneRecord($sql);
          if($tmp["doc_id"] > 0) {
            $go_api->db->query("DELETE FROM dns_dep WHERE child_doc_id = '".$dep_row["child_doc_id"]."' AND child_doctype_id = '1018' AND parent_doc_id = '".$doc_id."' AND parent_doctype_id = '1016'");
            $go_api->db->query("DELETE dns_nodes.*, dns_a.* FROM dns_nodes, dns_a WHERE dns_nodes.doc_id = '".$dep_row["child_doc_id"]."' and dns_nodes.doctype_id = '1018' AND dns_nodes.doc_id = dns_a.doc_id AND dns_nodes.doctype_id = dns_a.doctype_id");
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_a_record_exists").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_a_record_exists");
            }
          }
          // checke, ob CNAME-Record schon existiert
          $sql = "SELECT dns_cname.doc_id FROM dns_dep, dns_cname where dns_cname.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '1019' and dns_dep.parent_doc_id = $doc_id and dns_dep.parent_doctype_id = '1016' and dns_cname.host = '".$a_record["host"]."'";

          $tmp = $go_api->db->queryOneRecord($sql);
          if($tmp["doc_id"] > 0) {
            $go_api->db->query("DELETE FROM dns_dep WHERE child_doc_id = '".$dep_row["child_doc_id"]."' AND child_doctype_id = '1018' AND parent_doc_id = '".$doc_id."' AND parent_doctype_id = '1016'");
            $go_api->db->query("DELETE dns_nodes.*, dns_a.* FROM dns_nodes, dns_a WHERE dns_nodes.doc_id = '".$dep_row["child_doc_id"]."' and dns_nodes.doctype_id = '1018' AND dns_nodes.doc_id = dns_a.doc_id AND dns_nodes.doctype_id = dns_a.doctype_id");
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_cname_record_exists").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_cname_record_exists");
            }
          }
          break;

        case 1019:

          $cname_record = $go_api->db->queryOneRecord("SELECT * FROM dns_cname WHERE doc_id = '".$dep_row["child_doc_id"]."'");
          // checke ob A-Record schon existiert
          $sql = "SELECT dns_a.doc_id FROM dns_dep, dns_a where dns_a.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '1018' and dns_dep.parent_doc_id = $doc_id and dns_dep.parent_doctype_id = '1016' and dns_a.host = '".$cname_record["host"]."' and dns_a.doc_id != '".$dep_row["child_doc_id"]."'";

          $tmp = $go_api->db->queryOneRecord($sql);
          if($tmp["doc_id"] > 0) {
            $go_api->db->query("DELETE FROM dns_dep WHERE child_doc_id = '".$dep_row["child_doc_id"]."' AND child_doctype_id = '1019' AND parent_doc_id = '".$doc_id."' AND parent_doctype_id = '1016'");
            $go_api->db->query("DELETE dns_nodes.*, dns_cname.* FROM dns_nodes, dns_cname WHERE dns_nodes.doc_id = '".$dep_row["child_doc_id"]."' and dns_nodes.doctype_id = '1019' AND dns_nodes.doc_id = dns_cname.doc_id AND dns_nodes.doctype_id = dns_cname.doctype_id");
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_a_record_exists").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_a_record_exists");
            }
          }
          // checke, ob CNAME-Record schon existiert
          $sql = "SELECT dns_cname.doc_id FROM dns_dep, dns_cname where dns_cname.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '1019' and dns_dep.parent_doc_id = $doc_id and dns_dep.parent_doctype_id = '1016' and dns_cname.host = '".$cname_record["host"]."' and dns_cname.doc_id != '".$dep_row["child_doc_id"]."'";

          $tmp = $go_api->db->queryOneRecord($sql);
          if($tmp["doc_id"] > 0) {
            $go_api->db->query("DELETE FROM dns_dep WHERE child_doc_id = '".$dep_row["child_doc_id"]."' AND child_doctype_id = '1019' AND parent_doc_id = '".$doc_id."' AND parent_doctype_id = '1016'");
            $go_api->db->query("DELETE dns_nodes.*, dns_cname.* FROM dns_nodes, dns_cname WHERE dns_nodes.doc_id = '".$dep_row["child_doc_id"]."' and dns_nodes.doctype_id = '1019' AND dns_nodes.doc_id = dns_cname.doc_id AND dns_nodes.doctype_id = dns_cname.doctype_id");
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_cname_record_exists").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_cname_record_exists");
            }
          }
          break;

        case 1020:

          $mx_record = $go_api->db->queryOneRecord("SELECT * FROM dns_mx WHERE doc_id = '".$dep_row["child_doc_id"]."'");
          // checke ob MX-Record schon existiert
          $sql = "SELECT dns_mx.doc_id FROM dns_dep, dns_mx where dns_mx.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '1020' and dns_dep.parent_doc_id = $doc_id and dns_dep.parent_doctype_id = '1016' and dns_mx.host = '".$mx_record["host"]."' AND (dns_mx.mailserver = '".$mx_record["mailserver"]."' OR dns_mx.prioritaet = '".$mx_record["prioritaet"]."') and dns_mx.doc_id != '".$dep_row["child_doc_id"]."'";

          $tmp = $go_api->db->queryOneRecord($sql);
          if($tmp["doc_id"] > 0) {
            $go_api->db->query("DELETE FROM dns_dep WHERE child_doc_id = '".$dep_row["child_doc_id"]."' AND child_doctype_id = '1020' AND parent_doc_id = '".$doc_id."' AND parent_doctype_id = '1016'");
            $go_api->db->query("DELETE dns_nodes.*, dns_mx.* FROM dns_nodes, dns_mx WHERE dns_nodes.doc_id = '".$dep_row["child_doc_id"]."' and dns_nodes.doctype_id = '1020' AND dns_nodes.doc_id = dns_mx.doc_id AND dns_nodes.doctype_id = dns_mx.doctype_id");
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_mx_record_exists").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_mx_record_exists");
            }
          }
          break;
        }
    }

    $go_api->db->query("UPDATE dns_isp_dns SET status = 'n' where doc_id = '$doc_id'");

    $soa = $go_api->db->queryOneRecord("select * from dns_nodes,dns_isp_dns where dns_isp_dns.doc_id = '$doc_id' and dns_nodes.doc_id = dns_isp_dns.doc_id and dns_nodes.doctype_id = $doctype_id");
    $soacount = $go_api->db->queryOneRecord("SELECT count(doc_id) as soa_count from dns_isp_dns where dns_soa = '".$soa["dns_soa"]."'");
    if($soacount["soa_count"] > 1) {
        $go_api->db->query("DELETE from dns_isp_dns where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from dns_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        if($die_on_error){
        $go_api->errorMessage($go_api->lng("error_soa_exists_1").$soa["dns_soa"].$go_api->lng("error_soa_exists_2").$go_api->lng("weiter_link"));
        } else {
          return $go_api->lng("error_soa_exists_1").$soa["dns_soa"].$go_api->lng("error_soa_exists_2");
        }
    }

        //////////////////////////////////////////////////////
        // Checke ob maximale Anzahl DNS-Domains erreicht ist
        //////////////////////////////////////////////////////

        // Wenn es nicht der Admin selbst ist
        if($soa["groupid"] > 1 and $aufruf == 'soa') {
                // Hole Resellerdaten
                $reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = " . $soa["groupid"]);
                if($reseller["limit_domain_dns"] >= 0 and is_array($reseller)) {
                        $domain_anzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as anzahl from dns_nodes where doctype_id = 1016 and groupid = " . $soa["groupid"]);
                        if($reseller["limit_domain_dns"] < $domain_anzahl["anzahl"]) {
                                $go_api->db->query("DELETE from dns_isp_dns where doc_id = '$doc_id'");
                                $go_api->db->query("DELETE from dns_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
                                if($die_on_error){
                                  $go_api->errorMessage($go_api->lng("Maximale Anzahl an DNS-Einträgen erreicht.").$go_api->lng("weiter_link"));
                                } else {
                                  return $go_api->lng("Maximale Anzahl an DNS-Einträgen erreicht.");
                                }
                        }
                }
        }

        // Standard Nameserver Setzen
        $server = $go_api->db->queryOneRecord("SELECT * from isp_server where doc_id = ".$soa["server_id"]);
        if($server["server_bind_ns1_default"] != '' and $aufruf == 'soa') {
                if($soa["dns_ns1"] == "") $go_api->db->query("UPDATE dns_isp_dns SET dns_ns1 = '".$server["server_bind_ns1_default"]."' where doc_id = '$doc_id'");
                if($soa["dns_ns2"] == "") $go_api->db->query("UPDATE dns_isp_dns SET dns_ns2 = '".$server["server_bind_ns2_default"]."' where doc_id = '$doc_id'");
                if($soa["dns_adminmail"] == "") $go_api->db->query("UPDATE dns_isp_dns SET dns_adminmail = '".$server["server_bind_adminmail_default"]."' where doc_id = '$doc_id'");
        }


        ////////////////////////////////////////
    // Standard MX Record anlegen
    ////////////////////////////////////////


        if($server["server_bind_standard_mx"] == 1 and $aufruf == 'soa') {
        //$ip_adresse = $domain["domain_ip"];
                $domain = $soa["dns_soa"];
        $sql = "INSERT INTO dns_mx (host,prioritaet,mailserver) VALUES ('','10','mail.$domain')";
        $go_api->db->query($sql);
        $mx_doc_id = $go_api->db->insertID();

        $userid = $go_info["user"]["userid"];
        $groupid = $soa["groupid"];
        $type = 'a';
        $parent = '';
        $status = 1;
        $title = '';

        $sql = "INSERT INTO dns_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('1','$groupid','$parent','$type','1020','$status','$mx_doc_id','$title')";
        $go_api->db->query($sql);
        $mx_tree_id = $go_api->db->insertID();
        $status = 1;

        $parent_doctype_id = 1016;
        $child_doctype_id = 1020;

        $parent_doc_id = $soa["doc_id"];
        $child_doc_id = $mx_doc_id;

        $parent_tree_id = $soa["tree_id"];
        $child_tree_id = $mx_tree_id;

        $sql = "INSERT INTO dns_dep (userid,parent_doc_id,parent_doctype_id,parent_tree_id,child_doc_id,child_doctype_id,child_tree_id,status) VALUES ('$userid','$parent_doc_id','$parent_doctype_id','$parent_tree_id','$child_doc_id','$child_doctype_id','$child_tree_id','$status')";
        $go_api->db->query($sql);

                // zugehörigen a record anlegen
                $host = 'mail';
        $ip_adresse = $soa["dns_soa_ip"];
        $sql = "INSERT INTO dns_a (host,ip_adresse) VALUES ('$host','$ip_adresse')";
        $go_api->db->query($sql);
        $a_doc_id = $go_api->db->insertID();

        $userid = $go_info["user"]["userid"];
        $groupid = $soa["groupid"];
        $type = 'a';
        $parent = '';
        $status = 1;
        $title = '';

        $sql = "INSERT INTO dns_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('1','$groupid','$parent','$type','1018','$status','$a_doc_id','$title')";
        $go_api->db->query($sql);
        $a_tree_id = $go_api->db->insertID();
        $status = 1;

        $parent_doctype_id = 1016;
        $child_doctype_id = 1018;

        $parent_doc_id = $soa["doc_id"];
        $child_doc_id = $a_doc_id;

        $parent_tree_id = $soa["tree_id"];
        $child_tree_id = $a_tree_id;

        $sql = "INSERT INTO dns_dep (userid,parent_doc_id,parent_doctype_id,parent_tree_id,child_doc_id,child_doctype_id,child_tree_id,status) VALUES ('$userid','$parent_doc_id','$parent_doctype_id','$parent_tree_id','$child_doc_id','$child_doctype_id','$child_tree_id','$status')";
        $go_api->db->query($sql);
        }


    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');

}

function soa_update($doc_id, $doctype_id, $die_on_error = '1') {
    global $go_api, $go_info, $old_form_data;

    if($doctype_id == 1016) {
        $doc_id = $doc_id;
        $doctype_id = $doctype_id;
    } else {
        // Eltern Eintrag finden
        $sql = "SELECT * FROM dns_dep where child_doc_id = $doc_id and child_doctype_id = $doctype_id";
        $dep_row = $go_api->db->queryOneRecord($sql);
        $doc_id = $dep_row["parent_doc_id"];
        $doctype_id = $dep_row["parent_doctype_id"];
        $go_api->db->query("UPDATE dns_nodes SET groupid = '".$dep_row["groupid"]."' WHERE  doc_id = '".$dep_row["child_doc_id"]."' AND doctype_id = '".$dep_row["child_doctype_id"]."'");

        switch ($dep_row["child_doctype_id"]) {

        case 1018:

          $a_record = $go_api->db->queryOneRecord("SELECT * FROM dns_a WHERE doc_id = '".$dep_row["child_doc_id"]."'");
          // checke ob A-Record schon existiert
          $sql = "SELECT dns_a.doc_id FROM dns_dep, dns_a where dns_a.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '1018' and dns_dep.parent_doc_id = $doc_id and dns_dep.parent_doctype_id = '1016' and dns_a.host = '".$a_record["host"]."' and dns_a.doc_id != '".$dep_row["child_doc_id"]."'";

          $tmp = $go_api->db->queryOneRecord($sql);
          if($tmp["doc_id"] > 0) {
            $go_api->db->query("UPDATE dns_a SET host = '".$old_form_data["host"]."', ip_adresse = '".$old_form_data["ip_adresse"]."' WHERE doc_id = '".$dep_row["child_doc_id"]."'");
            $status = "NOTIFY";
            $errorMessage .= $go_api->lng("error_a_record_exists");
          }
          // checke, ob CNAME-Record schon existiert
          $sql = "SELECT dns_cname.doc_id FROM dns_dep, dns_cname where dns_cname.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '1019' and dns_dep.parent_doc_id = $doc_id and dns_dep.parent_doctype_id = '1016' and dns_cname.host = '".$a_record["host"]."'";

          $tmp = $go_api->db->queryOneRecord($sql);
          if($tmp["doc_id"] > 0) {
            $go_api->db->query("UPDATE dns_a SET host = '".$old_form_data["host"]."', ip_adresse = '".$old_form_data["ip_adresse"]."' WHERE doc_id = '".$dep_row["child_doc_id"]."'");
            $status = "NOTIFY";
            $errorMessage .= $go_api->lng("error_cname_record_exists");
          }
          break;

        case 1019:

          $cname_record = $go_api->db->queryOneRecord("SELECT * FROM dns_cname WHERE doc_id = '".$dep_row["child_doc_id"]."'");
          // checke ob A-Record schon existiert
          $sql = "SELECT dns_a.doc_id FROM dns_dep, dns_a where dns_a.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '1018' and dns_dep.parent_doc_id = $doc_id and dns_dep.parent_doctype_id = '1016' and dns_a.host = '".$cname_record["host"]."' and dns_a.doc_id != '".$dep_row["child_doc_id"]."'";

          $tmp = $go_api->db->queryOneRecord($sql);
          if($tmp["doc_id"] > 0) {
            $go_api->db->query("UPDATE dns_cname SET host = '".$old_form_data["host"]."', ziel = '".$old_form_data["ziel"]."' WHERE doc_id = '".$dep_row["child_doc_id"]."'");
            $status = "NOTIFY";
            $errorMessage .= $go_api->lng("error_a_record_exists");
          }
          // checke, ob CNAME-Record schon existiert
          $sql = "SELECT dns_cname.doc_id FROM dns_dep, dns_cname where dns_cname.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '1019' and dns_dep.parent_doc_id = $doc_id and dns_dep.parent_doctype_id = '1016' and dns_cname.host = '".$cname_record["host"]."' and dns_cname.doc_id != '".$dep_row["child_doc_id"]."'";

          $tmp = $go_api->db->queryOneRecord($sql);
          if($tmp["doc_id"] > 0) {
            $go_api->db->query("UPDATE dns_cname SET host = '".$old_form_data["host"]."', ziel = '".$old_form_data["ziel"]."' WHERE doc_id = '".$dep_row["child_doc_id"]."'");
            $status = "NOTIFY";
            $errorMessage .= $go_api->lng("error_cname_record_exists");
          }
          break;

        case 1020:

          $mx_record = $go_api->db->queryOneRecord("SELECT * FROM dns_mx WHERE doc_id = '".$dep_row["child_doc_id"]."'");
          // checke ob MX-Record schon existiert
          $sql = "SELECT dns_mx.doc_id FROM dns_dep, dns_mx where dns_mx.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = '1020' and dns_dep.parent_doc_id = $doc_id and dns_dep.parent_doctype_id = '1016' and dns_mx.host = '".$mx_record["host"]."' AND (dns_mx.mailserver = '".$mx_record["mailserver"]."' OR dns_mx.prioritaet = '".$mx_record["prioritaet"]."') and dns_mx.doc_id != '".$dep_row["child_doc_id"]."'";

          $tmp = $go_api->db->queryOneRecord($sql);
          if($tmp["doc_id"] > 0) {
            $go_api->db->query("UPDATE dns_mx SET host = '".$old_form_data["host"]."', prioritaet = '".$old_form_data["prioritaet"]."', mailserver = '".$old_form_data["mailserver"]."' WHERE doc_id = '".$dep_row["child_doc_id"]."'");
            $status = "NOTIFY";
            $errorMessage .= $go_api->lng("error_mx_record_exists");
          }
          break;
        }

    }


    $go_api->db->query("UPDATE dns_isp_dns SET status = 'u' where status != 'n' and doc_id = '$doc_id'");

    //$soa = $go_api->db->queryOneRecord("select * from dns_isp_dns where doc_id = '$doc_id'");

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'update');

        // ISPConfig Rechte in nodes Table checken
        $go_api->isp->check_perms($doc_id, $doctype_id);

    if($status == "NOTIFY"){
      if($die_on_error){
        $go_api->errorMessage($errorMessage.$go_api->lng("weiter_link"));
      } else {
        return $errorMessage;
      }
    }
}

function soa_delete($doc_id, $doctype_id, $action, $die_on_error = '1') {
    global $go_api, $go_info;

    if($doctype_id == 1016) {
        $doc_id = $doc_id;
        $doctype_id = $doctype_id;
        $aufruf = 'soa';
    } else {
        // Eltern Eintrag finden
        $sql = "SELECT * FROM dns_dep where child_doc_id = $doc_id and child_doctype_id = $doctype_id";
        $dep_row = $go_api->db->queryOneRecord($sql);
        $doc_id = $dep_row["parent_doc_id"];
        $doctype_id = $dep_row["parent_doctype_id"];
        $aufruf = 'child';
    }

    $soa = $go_api->db->queryOneRecord("select * from dns_nodes,dns_isp_dns where dns_isp_dns.doc_id = '$doc_id' and dns_nodes.doc_id = dns_isp_dns.doc_id and dns_nodes.doctype_id = $doctype_id");

    if($action == "do") {
        $go_api->db->query("UPDATE dns_isp_dns SET status = 'd' where doc_id = '$doc_id'");
    } else {
        if($soa["groupid"] > 1 && $aufruf == 'soa' && $reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = " . $soa["groupid"])) {
          if($reseller["limit_domain_dns"] >= 0 && $reseller["limit_dns_manager"]) {
                        $domain_anzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as anzahl from dns_nodes where doctype_id = 1016 and groupid = " . $soa["groupid"]);
                        if($reseller["limit_domain_dns"] < $domain_anzahl["anzahl"]) {
                          $go_api->db->query("UPDATE dns_nodes SET status = '0' WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
                          if($die_on_error){
                            $go_api->errorMessage($go_api->lng("Maximale Anzahl an DNS-Einträgen erreicht.").$go_api->lng("weiter_link"));
                          } else {
                            return $go_api->lng("Maximale Anzahl an DNS-Einträgen erreicht.");
                          }
                        } else {
                          $go_api->db->query("UPDATE dns_isp_dns SET status = 'n' where doc_id = '$doc_id'");
                        }
                } else {
                  if(!$reseller["limit_dns_manager"]){
                    $go_api->db->query("UPDATE dns_nodes SET status = '0' WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
                    if($die_on_error){
                      $go_api->errorMessage($go_api->lng("error_no_zones_allowed").$go_api->lng("weiter_link"));
                    } else {
                      return $go_api->lng("error_no_zones_allowed");
                    }
                  } else {
                    $go_api->db->query("UPDATE dns_isp_dns SET status = 'n' where doc_id = '$doc_id'");
                  }
                }
        } else {
          $go_api->db->query("UPDATE dns_isp_dns SET status = 'n' where doc_id = '$doc_id'");
        }
    }

    //$soa = $go_api->db->queryOneRecord("select * from dns_isp_dns where doc_id = '$doc_id'");

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'delete: '.$action);

}




}
?>