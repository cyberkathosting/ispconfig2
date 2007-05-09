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

class isp_domain
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
function isp_domain() {
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


function domain_insert($doc_id, $doctype_id, $die_on_error = '1') {
global $go_api, $go_info;

    // Remove http:// and https:// and spaces from domains and hosts
    if($tmp_domains = $go_api->db->queryAllRecords("SELECT * FROM isp_isp_domain WHERE domain_host LIKE 'http://%' OR domain_host LIKE 'https://%' OR domain_host LIKE ' %' OR domain_host LIKE '% ' OR domain_domain LIKE 'http://%' OR domain_domain LIKE 'https://%' OR domain_domain LIKE ' %' OR domain_domain LIKE '% '")){
      foreach($tmp_domains as $tmp_domain){
        $tmp_domain['domain_host'] = str_replace('http://', '', $tmp_domain['domain_host']);
        $tmp_domain['domain_host'] = str_replace('https://', '', $tmp_domain['domain_host']);
        $tmp_domain['domain_host'] = trim($tmp_domain['domain_host']);
        $tmp_domain['domain_domain'] = str_replace('http://', '', $tmp_domain['domain_domain']);
        $tmp_domain['domain_domain'] = str_replace('https://', '', $tmp_domain['domain_domain']);
        $tmp_domain['domain_domain'] = trim($tmp_domain['domain_domain']);
        $go_api->db->query("UPDATE isp_isp_domain SET domain_host = '".$tmp_domain['domain_host']."', domain_domain = '".$tmp_domain['domain_domain']."' WHERE doc_id = ".$tmp_domain['doc_id']);
      }
      unset($tmp_domains);
    }

    if($tmp_webs = $go_api->db->queryAllRecords("SELECT * FROM isp_isp_web WHERE web_host LIKE 'http://%' OR web_host LIKE 'https://%' OR web_host LIKE ' %' OR web_host LIKE '% ' OR web_domain LIKE 'http://%' OR web_domain LIKE 'https://%' OR web_domain LIKE ' %' OR web_domain LIKE '% '")){
      foreach($tmp_webs as $tmp_web){
        $tmp_web['web_host'] = str_replace('http://', '', $tmp_web['web_host']);
        $tmp_web['web_host'] = str_replace('https://', '', $tmp_web['web_host']);
        $tmp_web['web_host'] = trim($tmp_web['web_host']);
        $tmp_web['web_domain'] = str_replace('http://', '', $tmp_web['web_domain']);
        $tmp_web['web_domain'] = str_replace('https://', '', $tmp_web['web_domain']);
        $tmp_web['web_domain'] = trim($tmp_web['web_domain']);
        $go_api->db->query("UPDATE isp_isp_web SET web_host = '".$tmp_web['web_host']."', web_domain = '".$tmp_web['web_domain']."' WHERE doc_id = ".$tmp_web['doc_id']);
      }
      unset($tmp_webs);
    }

    $domain = $go_api->db->queryOneRecord("SELECT * from isp_nodes, isp_isp_domain where isp_nodes.doc_id = isp_isp_domain.doc_id and isp_nodes.doctype_id = '1015' and isp_isp_domain.doc_id = '$doc_id'");
    $domaincount = $go_api->db->queryOneRecord("SELECT count(doc_id) as domain_count from isp_isp_domain where domain_host = '".$domain["domain_host"]."' and domain_domain = '".$domain["domain_domain"]."'");
        if($domaincount["domain_count"] > 1 or $go_api->db->queryOneRecord("SELECT doc_id from isp_isp_web where web_host = '".$domain["domain_host"]."' and web_domain = '".$domain["domain_domain"]."'")) {
        $status = "DELETE";
        $errorMessage  = $go_api->lng("error_web_doppelt")." ".$domain["domain_host"].".".$domain["domain_domain"]." ".$go_api->lng("angelegt");
        }

    // IP Adresse der Domain vom Web holen
    $sql = "SELECT * from isp_dep,isp_isp_web where isp_dep.parent_doc_id = isp_isp_web.doc_id and isp_dep.parent_doctype_id = ".$this->web_doctype_id." and isp_dep.child_doc_id = $doc_id and isp_dep.child_doctype_id = $doctype_id";
    $web = $go_api->db->queryOneRecord($sql);
    $go_api->db->query("UPDATE isp_isp_domain SET domain_ip = '".$web["web_ip"]."' where doc_id = $doc_id");
    $domain["domain_ip"] = $web["web_ip"];

    // Web Status auf update setzen
    $web_doc_id = $web["doc_id"];
    $go_api->db->query("UPDATE isp_isp_web SET status = 'u' where status != 'n' and doc_id = '$web_doc_id'");

    //User und Groupid auf die Werte des Web setzen
    $web_doc_id = $web["parent_doc_id"];
    $web_doctype_id = $web["parent_doctype_id"];
    $webnode = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
    $go_api->db->query("UPDATE isp_nodes SET groupid = ".$webnode["groupid"].", userid = ".$webnode["userid"]." where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
    $domain["groupid"] = $webnode["groupid"];
    $domain["userid"] = $webnode["userid"];

    // Check Ob maximale Anzah Domains des Web erreicht ist
     // Hole das Web des Users
     $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_dep where
     isp_isp_web.doc_id = isp_dep.parent_doc_id and
     isp_isp_web.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");

     $domainlimit = $web["web_domainlimit"];
     $domain["server_id"] = $web["server_id"];
    // $quotalimit = $web["web_speicher"];
     $web_doc_id = $web["doc_id"];
     $web_doctype_id = $web["doctype_id"];
     //unset($web);

     // Hole DomainAnzahl
     $domaincount = $go_api->db->queryOneRecord("SELECT count(doc_id) as doc_count from isp_isp_domain, isp_dep where
     isp_isp_domain.doc_id = isp_dep.child_doc_id and isp_isp_domain.doctype_id = isp_dep.child_doctype_id and
     isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and isp_dep.child_doctype_id = $doctype_id");

     $domainanzahl = $domaincount["doc_count"] + 1;

    // wenn Userlimits gesetzt sind
     if($domainlimit > 0) {
        if($domainanzahl > $domainlimit) {
        $status = "DELETE";
        $errorMessage .= $go_api->lng("error_max_domain_web");
        }
     }

/*
     // Check Ob maximale Anzahl Domains des Resellers erreicht ist
     $domain_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
     $resellerid = $domain_node["groupid"];

     if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
        // Wenn Resellerlimit erreicht wurde
        if($reseller["limit_domain"] >= 0) {
            if($domainanzahl >= $reseller["limit_domain"]) {
            $status = "DELETE";
            $errorMessage .= $go_api->lng("error_max_domain_anbieter");
            }
        }
     }
*/

     // Check Ob maximale Anzahl Domains des Resellers erreicht ist
     $domain_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
     $resellerid = $domain_node["groupid"];

     if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
        // Wenn Resellerlimit erreicht wurde
        if($reseller["limit_domain"] >= 0) {
            $reseller_domainanzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as reseller_domainanzahl from isp_nodes where groupid = '$resellerid' and doctype_id = '".$this->domain_doctype_id."'");
            $webanzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as webs from isp_nodes where groupid = '$resellerid' and doctype_id = '".$this->web_doctype_id."'");
            $webanzahl = $webanzahl["webs"];
            $reseller_domainanzahl = $reseller_domainanzahl["reseller_domainanzahl"] + $webanzahl;
            if($reseller_domainanzahl > $reseller["limit_domain"]) {
              $status = "DELETE";
              $errorMessage .= $go_api->lng("error_max_domain_anbieter");
            }
        }
     }

    // Checke, wenn die Domain bereits existiert, es sich also faktisch um eine
    // Sub-Domain handelt, ob der Eigentümer identisch des neuen Eintrages mit dem
    // Hauptdomain-eigentümer identisch ist.
    $haupt_domain = $go_api->db->queryOneRecord("SELECT * from isp_nodes, isp_isp_domain where isp_nodes.doc_id = isp_isp_domain.doc_id and isp_nodes.doctype_id = '1015' and isp_isp_domain.domain_domain = '".$domain["domain_domain"]."'");
    if($haupt_domain["userid"] != $domain["userid"]) {
        $status = "DELETE";
        $errorMessage .= $go_api->lng("err_0001");
    }
    unset($haupt_domain);


    if($status == "DELETE") {
        // Eintrag löschen
        $go_api->db->query("DELETE from isp_isp_domain where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        if($die_on_error){
          $go_api->errorMessage($errorMessage.$go_api->lng("weiter_link"));
        } else {
          return $errorMessage;
        }
    } else {
        // Status der Domain auf 'n' setzen
        $go_api->db->query("UPDATE isp_isp_domain SET status = 'n' where doc_id = '$doc_id'");
    }


    if($domain["domain_dns"] == 1) {
        $domain["domain_ip"] = $web["web_ip"];
        $this->_insert_dns($doc_id,$doctype_id,$domain,$web);
        //$go_api->db->query("UPDATE isp_isp_domain SET domain_dns = '', domain_dnsmail = '' where doc_id = $doc_id");
    }


    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');


        $this->faktura_insert($doc_id,$web["doc_id"],$domain["domain_host"].".".$domain["domain_domain"]);

}



function domain_update($doc_id, $doctype_id, $die_on_error = '1') {
global $go_api, $go_info,$s;

    // Remove http:// and https:// and spaces from domains and hosts
    if($tmp_domains = $go_api->db->queryAllRecords("SELECT * FROM isp_isp_domain WHERE domain_host LIKE 'http://%' OR domain_host LIKE 'https://%' OR domain_host LIKE ' %' OR domain_host LIKE '% ' OR domain_domain LIKE 'http://%' OR domain_domain LIKE 'https://%' OR domain_domain LIKE ' %' OR domain_domain LIKE '% '")){
      foreach($tmp_domains as $tmp_domain){
        $tmp_domain['domain_host'] = str_replace('http://', '', $tmp_domain['domain_host']);
        $tmp_domain['domain_host'] = str_replace('https://', '', $tmp_domain['domain_host']);
        $tmp_domain['domain_host'] = trim($tmp_domain['domain_host']);
        $tmp_domain['domain_domain'] = str_replace('http://', '', $tmp_domain['domain_domain']);
        $tmp_domain['domain_domain'] = str_replace('https://', '', $tmp_domain['domain_domain']);
        $tmp_domain['domain_domain'] = trim($tmp_domain['domain_domain']);
        $go_api->db->query("UPDATE isp_isp_domain SET domain_host = '".$tmp_domain['domain_host']."', domain_domain = '".$tmp_domain['domain_domain']."' WHERE doc_id = ".$tmp_domain['doc_id']);
      }
      unset($tmp_domains);
    }

    if($tmp_webs = $go_api->db->queryAllRecords("SELECT * FROM isp_isp_web WHERE web_host LIKE 'http://%' OR web_host LIKE 'https://%' OR web_host LIKE ' %' OR web_host LIKE '% ' OR web_domain LIKE 'http://%' OR web_domain LIKE 'https://%' OR web_domain LIKE ' %' OR web_domain LIKE '% '")){
      foreach($tmp_webs as $tmp_web){
        $tmp_web['web_host'] = str_replace('http://', '', $tmp_web['web_host']);
        $tmp_web['web_host'] = str_replace('https://', '', $tmp_web['web_host']);
        $tmp_web['web_host'] = trim($tmp_web['web_host']);
        $tmp_web['web_domain'] = str_replace('http://', '', $tmp_web['web_domain']);
        $tmp_web['web_domain'] = str_replace('https://', '', $tmp_web['web_domain']);
        $tmp_web['web_domain'] = trim($tmp_web['web_domain']);
        $go_api->db->query("UPDATE isp_isp_web SET web_host = '".$tmp_web['web_host']."', web_domain = '".$tmp_web['web_domain']."' WHERE doc_id = ".$tmp_web['doc_id']);
      }
      unset($tmp_webs);
    }

    $go_api->db->query("UPDATE isp_isp_domain SET status = 'u' where status != 'n' and doc_id = '$doc_id'");
    //$domain = $go_api->db->queryOneRecord("select * from isp_isp_domain where doc_id = '$doc_id'");
    // IP Adresse der Domain vom Web holen
    //$sql = "SELECT * from isp_dep,isp_isp_web where isp_dep.parent_doc_id = isp_isp_web.doc_id and isp_dep.parent_doctype_id = ".$this->web_doctype_id." and isp_dep.child_doc_id = $doc_id and isp_dep.child_doctype_id = $doctype_id";
    //$web = $go_api->db->queryOneRecord($sql);
    //$go_api->db->query("UPDATE isp_isp_domain SET domain_ip = '".$web["web_ip"]."' where doc_id = $doc_id");
    $domain = $go_api->db->queryOneRecord("select * from isp_isp_domain where doc_id = '$doc_id'");

    // IP Adresse der Domain vom Web holen
    $sql = "SELECT * from isp_dep,isp_isp_web where isp_dep.parent_doc_id = isp_isp_web.doc_id and isp_dep.parent_doctype_id = ".$this->web_doctype_id." and isp_dep.child_doc_id = $doc_id and isp_dep.child_doctype_id = $doctype_id";
    $web = $go_api->db->queryOneRecord($sql);

    if($domain["domain_dns"] == 1) {
        $this->_insert_dns($doc_id,$doctype_id,$domain,$web);
        //$go_api->db->query("UPDATE isp_isp_domain SET domain_dns = '', domain_dnsmail = '' where doc_id = $doc_id");
    }

    //User und Groupid auf die Werte des Web setzen
    $web_doc_id = $web["parent_doc_id"];
    $web_doctype_id = $web["parent_doctype_id"];
    $webnode = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
    $go_api->db->query("UPDATE isp_nodes SET groupid = ".$webnode["groupid"].", userid = ".$webnode["userid"]." where doc_id = '$doc_id' and doctype_id = '$doctype_id'");


        // Web Weiterleitung prüfen
                if($domain["domain_weiterleitung"] == '/') $domain["domain_weiterleitung"] = '';
        if($domain["domain_weiterleitung"] != '') {
                $weiterleitung = $domain["domain_weiterleitung"];
                if(stristr($weiterleitung,'http')) {
                        // Checke auf URL
                        if(!preg_match("/^[\w\.\-\/\:\?\=\&\~]{0,}$/",$weiterleitung)) $weiterleitung = '';
                } else {
                        // Checke auf Pfad
                        if(!preg_match("/^[\w\.\-\/]{0,255}$/",$weiterleitung)) $weiterleitung = '';
                }
                if(stristr($weiterleitung,'../')) $weiterleitung = '';
                if($weiterleitung == '') {
                        $go_api->db->query("UPDATE isp_isp_domain SET domain_weiterleitung = '' where doc_id = $doc_id");
                        if($die_on_error){
                          $go_api->errorMessage($go_api->lng("error_domain_forward").$go_api->lng("weiter_link"));
                        } else {
                          return $go_api->lng("error_domain_forward");
                        }
                        //die($weiterleitung);
                }

        }


    // Web Status auf update setzen
    $go_api->db->query("UPDATE isp_isp_web SET status = 'u' where status != 'n' and doc_id = '$web_doc_id'");

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'update');

        $this->faktura_update($doc_id,$web["doc_id"],$domain["domain_host"].".".$domain["domain_domain"]);

        // ISPConfig Rechte in nodes Table checken
        $go_api->isp->check_perms($doc_id, $doctype_id);

}

function domain_delete($doc_id, $doctype_id, $action, $die_on_error = '1') {
global $go_api, $go_info;

###########################
    $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_dep where
     isp_isp_web.doc_id = isp_dep.parent_doc_id and
     isp_isp_web.doctype_id = isp_dep.parent_doctype_id and
     isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");
    $web_doc_id = $web["parent_doc_id"];
    $web_doctype_id = $web["doctype_id"];

    if($action == "do") {
        $go_api->db->query("UPDATE isp_isp_domain SET status = 'd' where doc_id = '$doc_id'");
                $this->faktura_delete($doc_id,'do');
    } else {
        // Prüfen, ob Domain überhaupt wiederhergestellt werden darf, ob für das Web genug Domains erlaubt sind
        // Hole Domainanzahl
        if($web["web_domainlimit"] >= 0){
     $domaincount = $go_api->db->queryOneRecord("SELECT count(isp_isp_domain.doc_id) as doc_count from isp_dep, isp_isp_domain where isp_dep.parent_doc_id = '$web_doc_id' and isp_dep.parent_doctype_id = '$web_doctype_id' and isp_dep.child_doc_id = isp_isp_domain.doc_id and isp_dep.child_doctype_id = '".$this->domain_doctype_id."'");

     $domainanzahl = $domaincount["doc_count"] + 1;
     } else {
       $domainanzahl = -1;
     }


     // Check Ob maximale Anzahl Domains des Resellers erreicht ist
     $domain_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
     $resellerid = $domain_node["groupid"];

     if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
        // Wenn Resellerlimit erreicht wurde
        if($reseller["limit_domain"] >= 0) {
            $reseller_domainanzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as reseller_domainanzahl from isp_nodes where groupid = '$resellerid' and doctype_id = '".$this->domain_doctype_id."'");
            $webanzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as webs from isp_nodes where groupid = '$resellerid' and doctype_id = '".$this->web_doctype_id."'");
            $webanzahl = $webanzahl["webs"];
            $reseller_domainanzahl = $reseller_domainanzahl["reseller_domainanzahl"] + $webanzahl;
        } else {
          $reseller_domainanzahl = -1;
          $reseller["limit_domain"] = -1;
        }
     } else {
       $reseller_domainanzahl = -1;
       $reseller["limit_domain"] = -1;
     }


        if($domainanzahl <= $web["web_domainlimit"] && $reseller_domainanzahl <= $reseller["limit_domain"]){
          $go_api->db->query("UPDATE isp_isp_domain SET status = 'n' WHERE doc_id = '$doc_id'");
                $this->faktura_delete($doc_id,'undo');
        } else {
          $go_api->db->query("UPDATE isp_nodes SET status = '0' WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
          if($domainanzahl > $web["web_domainlimit"]){
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_domain_forward").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_domain_forward");
            }
          }
          if($reseller_domainanzahl > $reseller["limit_domain"]){
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_max_domain_anbieter").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_max_domain_anbieter");
            }
          }
        }
    }
#############################

/*
    if($action == "do") {
        $go_api->db->query("UPDATE isp_isp_domain SET status = 'd' where doc_id = '$doc_id'");
                $this->faktura_delete($doc_id,'do');
    } else {
        $go_api->db->query("UPDATE isp_isp_domain SET status = 'n' where doc_id = '$doc_id'");
                $this->faktura_delete($doc_id,'undo');
    }
    // Web Status auf update setzen
    $sql = "SELECT * from isp_dep,isp_isp_web where isp_dep.parent_doc_id = isp_isp_web.doc_id and isp_dep.parent_doctype_id = ".$this->web_doctype_id." and isp_dep.child_doc_id = $doc_id and isp_dep.child_doctype_id = $doctype_id";
    $web = $go_api->db->queryOneRecord($sql);
    $web_doc_id = $web["doc_id"];
*/

    $go_api->db->query("UPDATE isp_isp_web SET status = 'u' where status != 'n' and status != 'd' and doc_id = '$web_doc_id'");

/*
    // IP Adresse der Domain vom Web holen
    $sql = "SELECT * from isp_dep,isp_isp_web where isp_dep.parent_doc_id = isp_isp_web.doc_id and isp_dep.parent_doctype_id = ".$this->web_doctype_id." and isp_dep.child_doc_id = $doc_id and isp_dep.child_doctype_id = $doctype_id";
    $web = $go_api->db->queryOneRecord($sql);
*/

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'delete: '.$action);
}


##################################################################################################################

    ///////////////////////////////////////////////////
    // Externe Funktionen
    ///////////////////////////////////////////////////

function _insert_dns($doc_id,$doctype_id,$domain,$web) {
    global $go_api, $go_info;

    $dns_doctype_id = 1016;
    $a_record_doctype_id = 1018;
    $mx_record_doctype_id = 1020;
    $spf_record_doctype_id = 1031;

    // erzeuge DNS Records für Web
    $dns_soa = trim($domain["domain_domain"]);
    $server_id = $web["server_id"];
    $dns_soa_ip = $domain["domain_ip"];
    $dns_adminmail = 'admin@'.$domain["domain_domain"];

    // wenn noch kein SOA Record existiert
    if(!$go_api->db->queryOneRecord("SELECT * FROM dns_isp_dns where dns_soa = '$dns_soa'")) {

        $server = $go_api->db->queryOneRecord("SELECT * FROM isp_server where doc_id = '$server_id'");
        $dns_ns1 = $server["server_bind_ns1_default"];
        $dns_ns2 = $server["server_bind_ns2_default"];


        $sql = "INSERT INTO dns_isp_dns (dns_soa,dns_ns1,dns_ns2,dns_adminmail,server_id,dns_soa_ip,status) VALUES ('$dns_soa','$dns_ns1','$dns_ns2','$dns_adminmail','$server_id','$dns_soa_ip','n')";

        $go_api->db->query($sql);
        $dns_doc_id = $go_api->db->insertID();

        $userid = $go_info["user"]["userid"];
        $groupid = $web["groupid"];
        $dns_doctype_id = 1016;
        $a_record_doctype_id = 1018;
        $type = 'i';
                if($groupid == 0) {
                $parent = 'root';
                } else {
                        $parent = 'group'.$web["groupid"];
                }
        $status = 1;
        $title = $dns_soa;

        $sql = "INSERT INTO dns_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('$userid','$groupid','$parent','$type','$dns_doctype_id','$status','$dns_doc_id','$title')";
        $go_api->db->query($sql);
        //$dns_tree_id = $go_api->db->insertID();

    }

    // wenn host nicht = "", dann A Record für Host eintragen
    $host = trim($domain["domain_host"]);
        $dns_record = $go_api->db->queryOneRecord("SELECT * FROM dns_nodes,dns_isp_dns where dns_nodes.doc_id = dns_isp_dns.doc_id and dns_nodes.doctype_id = dns_isp_dns.doctype_id and dns_isp_dns.dns_soa = '$dns_soa'");
        $query_sql = "SELECT * FROM dns_a, dns_dep WHERE dns_a.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = 1018 and dns_dep.parent_doc_id = ".$dns_record["doc_id"]." and dns_a.host = '".$host."'";

        if($host != "" and !$go_api->db->queryOneRecord($query_sql)) {
    //if($domain["domain_host"] != "") {

        //$dns_soa = trim($domain["domain_domain"]);
        // A Record für Host anlegen
        //$dns_record = $go_api->db->queryOneRecord("SELECT * FROM dns_nodes,dns_isp_dns where dns_nodes.doc_id = dns_isp_dns.doc_id and dns_nodes.doctype_id = dns_isp_dns.doctype_id and dns_isp_dns.dns_soa = '$dns_soa'");


        $ip_adresse = $domain["domain_ip"];
        $sql = "INSERT INTO dns_a (host,ip_adresse) VALUES ('$host','$ip_adresse')";
        $go_api->db->query($sql);
        $a_doc_id = $go_api->db->insertID();

        $userid = $go_info["user"]["userid"];
        $groupid = $web["groupid"];
        $type = 'a';
        $parent = '';
        $status = 1;
        $title = '';

        $sql = "INSERT INTO dns_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('$userid','$groupid','$parent','$type','$a_record_doctype_id','$status','$a_doc_id','$title')";
        $go_api->db->query($sql);
        $a_tree_id = $go_api->db->insertID();
        $status = 1;

        $parent_doctype_id = $dns_doctype_id;
        $child_doctype_id = $a_record_doctype_id;

        $parent_doc_id = $dns_record["doc_id"];
        $child_doc_id = $a_doc_id;

        $parent_tree_id = $dns_record["tree_id"];
        $child_tree_id = $a_tree_id;

        $sql = "INSERT INTO dns_dep (userid,parent_doc_id,parent_doctype_id,parent_tree_id,child_doc_id,child_doctype_id,child_tree_id,status) VALUES ('$userid','$parent_doc_id','$parent_doctype_id','$parent_tree_id','$child_doc_id','$child_doctype_id','$child_tree_id','$status')";
        $go_api->db->query($sql);
    }


    // MX Record anlegen

    if($domain["domain_dnsmail"] == 1) {

        $dns_soa = trim($domain["domain_domain"]);
        // A Record für Host anlegen
        $dns_record = $go_api->db->queryOneRecord("SELECT * FROM dns_nodes,dns_isp_dns where dns_nodes.doc_id = dns_isp_dns.doc_id and dns_nodes.doctype_id = dns_isp_dns.doctype_id and dns_isp_dns.dns_soa = '$dns_soa'");

        $server = $go_api->db->queryOneRecord("SELECT * from isp_server where doc_id = $server_id");

        $host = trim($domain["domain_host"]);
        if(trim($server["server_host"]) == ''){
          $mailserver = trim($server["server_domain"]);
        } else {
          $mailserver = trim($server["server_host"]).'.'.trim($server["server_domain"]);
        }

                // Check, ob es den MX nicht schon gibt
                $sql_test = "SELECT * FROM dns_dep, dns_mx WHERE dns_dep.child_doc_id = dns_mx.doc_id AND dns_dep.child_doctype_id = dns_mx.doctype_id and dns_dep.parent_doc_id = $dns_record[doc_id] and host = '$host' and mailserver = '$mailserver'";

                $exist_spf_record = $go_api->db->queryOneRecord("SELECT * from dns_dep, dns_spf where dns_dep.child_doc_id = dns_spf.doc_id and dns_dep.child_doctype_id = $spf_record_doctype_id and dns_dep.parent_doc_id = $dns_record[doc_id] and dns_spf.host = ''");

                if(!$go_api->db->queryOneRecord($sql_test)) {
                //$ip_adresse = $domain["domain_ip"];
                $sql = "INSERT INTO dns_mx (host,prioritaet,mailserver) VALUES ('$host','10','$mailserver')";
                $go_api->db->query($sql);
                $a_doc_id = $go_api->db->insertID();

                $userid = $go_info["user"]["userid"];
                $groupid = $web["groupid"];
                $type = 'a';
                $parent = '';
                $status = 1;
                $title = '';

                $sql = "INSERT INTO dns_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('$userid','$groupid','$parent','$type','$mx_record_doctype_id','$status','$a_doc_id','$title')";
                $go_api->db->query($sql);
                $a_tree_id = $go_api->db->insertID();
                $status = 1;

                $parent_doctype_id = $dns_doctype_id;
                $child_doctype_id = $mx_record_doctype_id;

                $parent_doc_id = $dns_record["doc_id"];
                $child_doc_id = $a_doc_id;

                $parent_tree_id = $dns_record["tree_id"];
                $child_tree_id = $a_tree_id;

                $sql = "INSERT INTO dns_dep (userid,parent_doc_id,parent_doctype_id,parent_tree_id,child_doc_id,child_doctype_id,child_tree_id,status) VALUES ('$userid','$parent_doc_id','$parent_doctype_id','$parent_tree_id','$child_doc_id','$child_doctype_id','$child_tree_id','$status')";
                $go_api->db->query($sql);
                }

                //////////////////////
                // Create SPF Record
                //////////////////////
                $exist_spf_record = $go_api->db->queryOneRecord("SELECT * from dns_dep, dns_spf where dns_dep.child_doc_id = dns_spf.doc_id and dns_dep.child_doctype_id = $spf_record_doctype_id and dns_dep.parent_doc_id = $dns_record[doc_id] and dns_spf.host = ''");

                if(!$exist_spf_record){
                  $sql = "INSERT INTO dns_spf (host, a, mx, ptr, a_break, mx_break, ip4_break, include_break, all_) VALUES ('$host', '1', '1', '1', '', '', '', '', '1')";
                  $go_api->db->query($sql);
                  $spf_doc_id = $go_api->db->insertID();

                  $userid = $go_info["user"]["userid"];
                  $groupid = $web["groupid"];
                  $type = 'a';
                  $parent = '';
                  $status = 1;
                  $title = '';

                  $sql = "INSERT INTO dns_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('1','$groupid','$parent','$type','$spf_record_doctype_id','$status','$spf_doc_id','$title')";
                  $go_api->db->query($sql);
                  $spf_tree_id = $go_api->db->insertID();
                  $status = 1;

                  $parent_doctype_id = $dns_doctype_id;
                  $child_doctype_id = $spf_record_doctype_id;

                  $parent_doc_id = $dns_record["doc_id"];
                  $child_doc_id = $spf_doc_id;

                  $parent_tree_id = $dns_record["tree_id"];
                  $child_tree_id = $spf_tree_id;

                  $sql = "INSERT INTO dns_dep (userid,parent_doc_id,parent_doctype_id,parent_tree_id,child_doc_id,child_doctype_id,child_tree_id,status) VALUES ('$userid','$parent_doc_id','$parent_doctype_id','$parent_tree_id','$child_doc_id','$child_doctype_id','$child_tree_id','$status')";
                  $go_api->db->query($sql);
                }
    }

        $go_api->db->query("UPDATE dns_isp_dns SET status = 'u' where status != 'n' and doc_id = '".$dns_record["doc_id"]."'");

}

function faktura_insert($doc_id,$web_id,$beschreibung) {
        global $go_api;
        $sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
        if($sys_config["faktura_on"] == 1) {
                $sql = "INSERT INTO isp_fakt_record (web_id,doc_id,doctype_id,typ,notiz) VALUES ($web_id,$doc_id,1015,'Domain','$beschreibung')";
                $go_api->db->query($sql);
        }
}

function faktura_update($doc_id,$web_id,$beschreibung) {
        global $go_api;
        $sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
        if($sys_config["faktura_on"] == 1) {
                $sql = "UPDATE isp_fakt_record SET notiz = '$beschreibung' where doc_id = $doc_id and doctype_id = 1015";
                $go_api->db->query($sql);
        }
}

function faktura_delete($doc_id,$action) {
        global $go_api;
        $sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
        if($sys_config["faktura_on"] == 1) {
                if($action == "do") {
                        $sql = "UPDATE isp_fakt_record SET status = 0 where doc_id = $doc_id and doctype_id = 1015";
                        $go_api->db->query($sql);
                } else {
                        $sql = "UPDATE isp_fakt_record SET status = 1 where doc_id = $doc_id and doctype_id = 1015";
                        $go_api->db->query($sql);
                }
        }
}



}
?>