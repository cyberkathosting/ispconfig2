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

class isp_reseller
{

var $path_httpd_conf;
var $path_httpd_root;
var $directory_mode = "0770";
var $web_doctype_id = 1013;
var $user_doctype_id = 1014;
var $domain_doctype_id = 1015;
var $dns_doctype_id = 1016;
var $slave_dns_doctype_id = 1028;
var $datenbank_doctype_id = 1029;
var $list_doctype_id = 1033;
var $vhost_conf;
var $sendmail_cw;
var $virtusertable;
var $user_von;
var $group_von;
var $server_conf;


//Constructor
function isp_reseller() {
global $go_api, $go_info;

$server_conf = $go_api->db->queryOneRecord("SELECT * from isp_server");
$this->server_conf = $server_conf;
$this->path_httpd_conf = stripslashes($server_conf["server_path_httpd_conf"]);
$this->path_httpd_root = stripslashes($server_conf["server_path_httpd_root"]);
$this->vhost_conf = $this->path_httpd_conf . $go_info["server"]["dir_trenner"].'vhost'.$go_info["server"]["dir_trenner"].'vhost.conf';
$this->user_von = $server_conf["userid_von"];
$this->group_von = $server_conf["groupid_von"];
$this->virtusertable = $server_conf["server_sendmail_virtuser_datei"];
$this->sendmail_cw = $server_conf["server_sendmail_cw"];
}

function reseller_show($doc_id, $doctype_id) {
    global $go_api, $go_info, $doc, $tablevalues;
//print_r($doc->deck[1]);
        ///////////////////////////////////////////////////////////////////////////
        // $tablevalues enthält daten, $doc enthält dokumententyp Repräsentation.
        ///////////////////////////////////////////////////////////////////////////

        // überprüfe welche Funktionen aktiv sind
        $server_id = 1;
        $server = $go_api->db->queryOneRecord("SELECT * from isp_server where doc_id = '$server_id'");
        // Deaktiviere Frontpage

        if($server["server_enable_frontpage"] != 1) {
            $doc->deck[1]->getElementByName("limit_frontpage")->visible = 0;
        }
        if($server["server_httpd_mod_perl"] != 1) {
            $doc->deck[1]->getElementByName("limit_cgi_mod_perl")->visible = 0;
        }

        // DNS-Reseller: "Sonstiges"-Tab verstecken (Begrüßungsemail für Kunden nicht nötig)
        // Resellerdaten auslesen
        $reseller = $go_api->db->queryOneRecord("select * from isp_nodes,isp_isp_reseller where isp_nodes.doc_id = isp_isp_reseller.doc_id and isp_nodes.doc_id = '$doc_id' and isp_nodes.doctype_id = '$doctype_id'");
        if($reseller["limit_dns_manager"] == 1 && $reseller["limit_web"] == 0 && $reseller["limit_user"] == 0 && $reseller["limit_domain"] == 0){
          $doc->deck[4]->visible = 0;
        }
}

function reseller_insert($doc_id, $doctype_id, $die_on_error = '1') {
global $go_api, $go_info;

    $reseller = $go_api->db->queryOneRecord("select * from isp_nodes,isp_isp_reseller where isp_nodes.doc_id = isp_isp_reseller.doc_id and isp_nodes.doc_id = '$doc_id' and isp_nodes.doctype_id = '$doctype_id'");

    // Check ob schon Gruppe mit Resellernamen existiert
    if($gruppe = $go_api->db->queryOneRecord("SELECT * from groups where name = '".trim($reseller["title"])."'")){
        $go_api->db->query("DELETE from isp_isp_reseller where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        if($die_on_error){
          $go_api->errorMessage($go_api->lng("error_reseller_title").$go_api->lng("weiter_link"));
        } else {
          return $go_api->lng("error_reseller_title");
        }

    } else {

        // anlegen einer Gruppe für den Reseller
        if($go_api->auth->check_write(0)) {
                        $datum = time();
                        $userid = $go_info["user"]["userid"];
                        $beschreibung = addslashes("Reseller: ".$reseller["title"]);
                        $name = addslashes(trim($reseller["title"]));
                        $sql = "INSERT INTO groups (userid, name, datum, groupstatus, beschreibung) VALUES ('$userid', '$name', '$datum', '1', '$beschreibung')";
                        $go_api->db->query($sql);
                        $gid = $go_api->db->insertID();

                        // Administrator anlegen
                        $username = $go_info["user"]["username"];
                        $sql = "INSERT INTO user_groups (groupid, userid, perms, userstatus, username) VALUES ('$gid', '$userid', 'rwa', '1', '$username')";
                        $go_api->db->query($sql);
            $go_api->db->query("UPDATE isp_isp_reseller SET reseller_group = '$gid' where doc_id = '$doc_id'");

            // Ordner für den Reseller anlegen
            $go_api->db->query("INSERT INTO isp_nodes (userid,groupid,parent,type,title) VALUES (0,$gid,'group$gid','n','Kunden')");
            $go_api->db->query("INSERT INTO isp_nodes (userid,groupid,parent,type,title) VALUES (0,$gid,'group$gid','n','Webs')");

                }
    }
/*
$soacount = $go_api->db->queryOneRecord("SELECT count(doc_id) as soa_count from dns_isp_dns where dns_soa = '".$soa["dns_soa"]."'");
if($soacount["soa_count"] > 1) {
    $go_api->db->query("DELETE from dns_isp_dns where doc_id = '$doc_id'");
    $go_api->db->query("DELETE from dns_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
    $go_api->errorMessage("Sie haben bereits einen SOA Record mit dem Namen ".$soa["dns_soa"]." angelegt");
}
*/

}

function reseller_update($doc_id, $doctype_id, $die_on_error = '1') {
    global $go_api, $go_info;

    // Resellerdaten auslesen
    $reseller = $go_api->db->queryOneRecord("select * from isp_nodes,isp_isp_reseller where isp_nodes.doc_id = isp_isp_reseller.doc_id and isp_nodes.doc_id = '$doc_id' and isp_nodes.doctype_id = '$doctype_id'");
    // Resellergruppe updaten
    if($gruppe = $go_api->db->queryOneRecord("SELECT * from groups where groupid = '".$reseller["reseller_group"]."'")){
        $go_api->db->query("UPDATE groups set name = '".addslashes($reseller["title"])."' where groupid = '".$reseller["reseller_group"]."'");
    } else {
        if($die_on_error){
          $go_api->errorMessage($go_api->lng("error_reseller_group_not_found").$go_api->lng("weiter_link"));
        } else {
          return $go_api->lng("error_reseller_group_not_found");
        }
    }

    //////////////// Dürfen Limits geändert werden? /////////////////
    $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id");
    $webanzahl = $webanzahl["anzahl"];
    if($reseller["limit_web"] >= 0){
      if($webanzahl > $reseller["limit_web"]){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_web = '$webanzahl' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage = $go_api->lng("error_anbieter_max_webs_ueberschritten");
      }
    }

    if(is_null($reseller["limit_disk"])) $reseller["limit_disk"] = 0;
    if($reseller["limit_disk"] >= 0){
      $diskspace = $go_api->db->queryOneRecord("SELECT sum(isp_isp_web.web_speicher) as diskspace, sum(isp_isp_web.web_mailquota) as mailquota, sum(isp_isp_web.web_mysql_quota) as mysqlquota from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '".$reseller["reseller_group"]."' and isp_nodes.doctype_id = ".$this->web_doctype_id);
      $minspace  = $go_api->db->queryOneRecord("SELECT min(isp_isp_web.web_speicher) as diskspace, min(isp_isp_web.web_mailquota) as mailquota, min(isp_isp_web.web_mysql_quota) as mysqlquota from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '".$reseller["reseller_group"]."' and isp_nodes.doctype_id = ".$this->web_doctype_id);
      $diskspace = $diskspace["diskspace"] + $diskspace["mailquota"] + $diskspace["mysqlquota"];
      $minspace  = min($diskspace["diskspace"], $diskspace["mailquota"], $diskspace["mysqlquota"]);
      if($diskspace > $reseller["limit_disk"]){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_disk = '$diskspace' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_max_diskspace_ueberschritten");
      } else if ($minspace < 0) {
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_disk = '-1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_max_diskspace_ueberschritten");
      }
      unset($diskspace);
      unset($minspace);
    }

    // TODO: Die folgenden Checks prüfen teilweise nur das tatsächlich Eingerichtete,
    //       nicht aber die an Webs vergebenen Limits.
    //       Dadurch können Reseller-Limits kleiner gesetzt werden als dessen Web-Limits.
    //       => Limits der Webs überprüfen anstatt der tatsächlich angelegten Elemente

    if(is_null($reseller["limit_user"])) $reseller["limit_user"] = 0;
    if($reseller["limit_user"] >= 0){
      $useranzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_user.doc_id) AS anzahl FROM isp_nodes, isp_isp_user WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->user_doctype_id."' AND isp_nodes.doc_id = isp_isp_user.doc_id");
      $useranzahl = $useranzahl["anzahl"];
      if($useranzahl > $reseller["limit_user"]){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_user = '$useranzahl' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_max_user_ueberschritten");
      }
      unset($useranzahl);
    }

    if(is_null($reseller["limit_domain"])) $reseller["limit_domain"] = 0;
    if($reseller["limit_domain"] >= 0){
      $domainanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_domain.doc_id) AS anzahl FROM isp_nodes, isp_isp_domain WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->domain_doctype_id."' AND isp_nodes.doc_id = isp_isp_domain.doc_id");
      $domainanzahl = $domainanzahl["anzahl"] + $webanzahl;
      if($domainanzahl > $reseller["limit_domain"]){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_domain = '$domainanzahl' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_max_domain_ueberschritten");
      }
      unset($domainanzahl);
    }

    unset($webanzahl);

    if(!$reseller["limit_dns_manager"]){
      $dnsanzahl = $go_api->db->queryOneRecord("SELECT COUNT(dns_isp_dns.doc_id) AS anzahl FROM dns_nodes,dns_isp_dns WHERE dns_nodes.doc_id = dns_isp_dns.doc_id and dns_nodes.doctype_id = '".$this->dns_doctype_id."' AND dns_nodes.groupid = '".$reseller["reseller_group"]."'");
      $dnsanzahl = $dnsanzahl["anzahl"];
      $slavednsanzahl = $go_api->db->queryOneRecord("SELECT COUNT(dns_secondary.doc_id) AS anzahl FROM dns_nodes,dns_secondary WHERE dns_nodes.doc_id = dns_secondary.doc_id and dns_nodes.doctype_id = '".$this->slave_dns_doctype_id."' AND dns_nodes.groupid = '".$reseller["reseller_group"]."'");
      $slavednsanzahl = $slavednsanzahl["anzahl"];
      if(($dnsanzahl + $slavednsanzahl) > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_dns_manager = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_dnsmanager_aktiv");
      }
      unset($dnsanzahl);
      unset($slavednsanzahl);
    }

    if(is_null($reseller["limit_domain_dns"])) $reseller["limit_domain_dns"] = 0;
    if($reseller["limit_domain_dns"] >= 0){
      $dnsanzahl = $go_api->db->queryOneRecord("SELECT COUNT(dns_isp_dns.doc_id) AS anzahl FROM dns_nodes,dns_isp_dns WHERE dns_nodes.doc_id = dns_isp_dns.doc_id and dns_nodes.doctype_id = '".$this->dns_doctype_id."' AND dns_nodes.groupid = '".$reseller["reseller_group"]."'");
      $dnsanzahl = $dnsanzahl["anzahl"];
      if($dnsanzahl > $reseller["limit_domain_dns"]){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_domain_dns = '$dnsanzahl' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_max_dns_ueberschritten");
      }
      unset($dnsanzahl);
    }

    if(is_null($reseller["limit_slave_dns"])) $reseller["limit_slave_dns"] = 0;
    if($reseller["limit_slave_dns"] >= 0){
      $slavednsanzahl = $go_api->db->queryOneRecord("SELECT COUNT(dns_secondary.doc_id) AS anzahl FROM dns_nodes,dns_secondary WHERE dns_nodes.doc_id = dns_secondary.doc_id and dns_nodes.doctype_id = '".$this->slave_dns_doctype_id."' AND dns_nodes.groupid = '".$reseller["reseller_group"]."'");
      $slavednsanzahl = $slavednsanzahl["anzahl"];
      if($slavednsanzahl > $reseller["limit_slave_dns"]){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_slave_dns = '$slavednsanzahl' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_max_slavedns_ueberschritten");
      }
      unset($slavednsanzahl);
    }

    if(!$reseller["limit_httpd_include"]){
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_httpd_include != ''");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_httpd_include = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_httpd_include_aktiv");
      }
      unset($webanzahl);
    }

    if(!$reseller["limit_shell_access"]){
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_shell = '1'");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_shell_access = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_shell_access_aktiv");
      }
      unset($webanzahl);
    }

    if(!$reseller["limit_cgi"]){
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_cgi = '1' AND isp_isp_web.web_cgi_mod_perl = '0'");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_cgi = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_cgi_aktiv");
      }
      unset($webanzahl);
    }

    if (!$reseller["limit_cgi_mod_perl"] && $server["server_httpd_mod_perl"] == 1) {
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_cgi = '1' AND isp_isp_web.web_cgi_mod_perl = '1'");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_cgi_mod_perl = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_cgi_mod_perl_aktiv");
      }
      unset($webanzahl);
    }

    if(!$reseller["limit_standard_cgis"]){
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_standard_cgi = '1'");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_standard_cgis = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_standard_cgi_aktiv");
      }
      unset($webanzahl);
    }

    if(!$reseller["limit_php"]){
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_php = '1'");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_php = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_php_aktiv");
      }
      unset($webanzahl);
    }

    if(!$reseller["limit_ssi"]){
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_ssi = '1'");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_ssi = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_ssi_aktiv");
      }
      unset($webanzahl);
    }

    if(!$reseller["limit_ftp"]){
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_ftp = '1'");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_ftp = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_ftp_aktiv");
      }
      unset($webanzahl);
    }

    if(!$reseller["limit_anonftp"]){
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_anonftp = '1'");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_anonftp = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_anonftp_aktiv");
      }
      unset($webanzahl);
    }

    $server_id = 1;
    $server = $go_api->db->queryOneRecord("SELECT * from isp_server where doc_id = '$server_id'");
    if($server["server_enable_frontpage"] == 1) {
      if(!$reseller["limit_frontpage"]){
        $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_frontpage = '1'");
        $webanzahl = $webanzahl["anzahl"];
        if($webanzahl > 0){
          $go_api->db->query("UPDATE isp_isp_reseller SET limit_frontpage = '1' WHERE doc_id = '$doc_id'");
          $res_limit_errorMessage .= $go_api->lng("error_anbieter_frontpage_aktiv");
        }
        unset($webanzahl);
      }
    }

    if(!$reseller["limit_mysql"]){
      $datenbankanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_datenbank.doc_id) AS anzahl FROM isp_nodes, isp_isp_datenbank WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->datenbank_doctype_id."' AND isp_nodes.doc_id = isp_isp_datenbank.doc_id");
      $datenbankanzahl = $datenbankanzahl["anzahl"];
      if($datenbankanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_mysql = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_datenbank_aktiv");
      }
      unset($datenbankanzahl);
    }

    if(is_null($reseller["limit_mysql_anzahl_dbs"])) $reseller["limit_mysql_anzahl_dbs"] = 0;
    if($reseller["limit_mysql_anzahl_dbs"] >= 0){
      $datenbankanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_datenbank.doc_id) AS anzahl FROM isp_nodes, isp_isp_datenbank WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->datenbank_doctype_id."' AND isp_nodes.doc_id = isp_isp_datenbank.doc_id");
      $datenbankanzahl = $datenbankanzahl["anzahl"];
      if($datenbankanzahl > $reseller["limit_mysql_anzahl_dbs"]){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_mysql_anzahl_dbs = '$datenbankanzahl' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_max_datenbank_ueberschritten");
      }
      unset($datenbankanzahl);
    }
    
    if(!$reseller["limit_list"]){
      $listenanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_list.doc_id) AS anzahl FROM isp_nodes, isp_isp_list WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->list_doctype_id."' AND isp_nodes.doc_id = isp_isp_list.doc_id");
      $listenanzahl = $listenanzahl["anzahl"];
      if($listenanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_list = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_list_aktiv");
      }
      unset($listenanzahl);
    }

    if(is_null($reseller["limit_listlimit"])) $reseller["limit_listlimit"] = 0;
    if($reseller["limit_listlimit"] >= 0){
      $listenanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_list.doc_id) AS anzahl FROM isp_nodes, isp_isp_list WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->list_doctype_id."' AND isp_nodes.doc_id = isp_isp_list.doc_id");
      $listenanzahl = $listenanzahl["anzahl"];
      if($listenanzahl > $reseller["limit_listlimit"]){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_listlimit = '$listenanzahl' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_max_list_ueberschritten");
      }
      unset($listenanzahl);
    }

    if(!$reseller["limit_ssl"]){
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_ssl = '1'");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_ssl = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_ssl_aktiv");
      }
      unset($webanzahl);
    }

    if(!$reseller["limit_wap"]){
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_wap = '1'");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_wap = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_wap_aktiv");
      }
      unset($webanzahl);
    }

    if(!$reseller["limit_error_pages"]){
      $webanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_web.doc_id) AS anzahl FROM isp_nodes, isp_isp_web WHERE isp_nodes.groupid = '".$reseller["reseller_group"]."' AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_isp_web.web_individual_error_pages = '1'");
      $webanzahl = $webanzahl["anzahl"];
      if($webanzahl > 0){
        $go_api->db->query("UPDATE isp_isp_reseller SET limit_error_pages = '1' WHERE doc_id = '$doc_id'");
        $res_limit_errorMessage .= $go_api->lng("error_anbieter_error_pages_aktiv");
      }
      unset($webanzahl);
    }

    if(isset($res_limit_errorMessage)){
      if($die_on_error){
        $go_api->errorMessage($res_limit_errorMessage.$go_api->lng("weiter_link"));
      } else {
        return $res_limit_errorMessage;
      }
    }
    //////////////// Dürfen Limits geändert werden? ENDE /////////////////

    // Passwort verschlüsseln
    if(substr($reseller["reseller_passwort"],0,5) != "||||:" and $reseller["reseller_passwort"] != "") {
        $go_api->db->query("UPDATE isp_isp_reseller SET reseller_passwort = CONCAT('||||:' , MD5(reseller_passwort)) WHERE doc_id = '$doc_id'");
        //$reseller["reseller_passwort"] = "||||:".$reseller["reseller_passwort"];

        ///////////////// Begrüßungsemail schicken ////////////////
        $absender_email = $this->server_conf["res_salutatory_email_sender_email"];
        $absender_name = $this->server_conf["res_salutatory_email_sender_name"];
        $bcc = $this->server_conf["res_salutatory_email_bcc"];
        $subject = $this->server_conf["res_salutatory_email_subject"];
        $message = $this->server_conf["res_salutatory_email_message"];
        $manual_lng = $go_api->db->queryOneRecord("SELECT language from sys_user where username = '".trim($reseller["reseller_user"])."'");
        if(!$manual_lng || empty($manual_lng["language"])){
          $manual_lng = $go_info["server"]["lang"];
        } else {
          $manual_lng = $manual_lng["language"];
        }

        if($reseller["email"] != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $reseller["email"]) && !strstr($reseller["email"], " ") && !strstr($reseller["email"], "!") && !strstr($reseller["email"], "?") && !strstr($reseller["email"], "\"") && !strstr($reseller["email"], "(") && !strstr($reseller["email"], ")") && !strstr($reseller["email"], "[") && !strstr($reseller["email"], "]") && !strstr($reseller["email"], "{") && !strstr($reseller["email"], "}") && !strstr($reseller["email"], "/") && !strstr($reseller["email"], "#") && $absender_email != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $absender_email) && !strstr($absender_email, " ") && !strstr($absender_email, "!") && !strstr($absender_email, "?") && !strstr($absender_email, "\"") && !strstr($absender_email, "(") && !strstr($absender_email, ")") && !strstr($absender_email, "[") && !strstr($absender_email, "]") && !strstr($absender_email, "{") && !strstr($absender_email, "}") && !strstr($absender_email, "/") && !strstr($absender_email, "#") && $absender_name != "" && $subject != "" && $message != ""){

          $message = str_replace("%%%USER%%%", $reseller["reseller_user"], $message);
          $message = str_replace("%%%PASSWORD%%%", $reseller["reseller_passwort"], $message);
          $message = str_replace("%%%FIRST_NAME%%%", $reseller["vorname"], $message);
          $message = str_replace("%%%LAST_NAME%%%", $reseller["name"], $message);
          $message = str_replace("%%%COMPANY%%%", $reseller["firma"], $message);
          $message = str_replace("%%%EMAIL%%%", $reseller["email"], $message);
          $message = str_replace("%%%CP%%%", $go_info["server"]["server_url"], $message);
          $message = str_replace("%%%MANUAL%%%", $go_info["server"]["server_url"]."/help/documents/".$manual_lng."/reseller.pdf", $message);

          if(trim($go_info["server"]["salutatory_email_charset"]) == '' || !isset($go_info["server"]["salutatory_email_charset"])){
            $salutatory_email_charset = 'unknown-8bit';
          } else {
            $salutatory_email_charset = trim($go_info["server"]["salutatory_email_charset"]);
          }

          $headers  = "From: ".$absender_name." <".$absender_email.">\n";
          if($bcc != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $bcc) && !strstr($bcc, " ") && !strstr($bcc, "!") && !strstr($bcc, "?") && !strstr($bcc, "\"") && !strstr($bcc, "(") && !strstr($bcc, ")") && !strstr($bcc, "[") && !strstr($bcc, "]") && !strstr($bcc, "{") && !strstr($bcc, "}") && !strstr($bcc, "/") && !strstr($bcc, "#")) $headers .= "Bcc: ".$bcc."\n";
          $headers .= "Reply-To: <".$absender_email.">\n";
          $headers .= "X-Sender: <".$absender_email.">\n";
          $headers .= "X-Mailer: PHP4\n"; //mailer
          $headers .= "X-Priority: 3\n"; //1 UrgentMessage, 3 Normal
          $headers .= "Return-Path: <".$absender_email.">\n";
          $headers .= "MIME-Version: 1.0\n";
          $headers .= "Content-Type: text/plain; charset=".$salutatory_email_charset."\n";
          mail($reseller["email"], $subject, $message, $headers);
        }
        ///////////////// Begrüßungsemail schicken ENDE ////////////////

        // Resellerdaten erneut auslesen
        $reseller = $go_api->db->queryOneRecord("select * from isp_nodes,isp_isp_reseller where isp_nodes.doc_id = isp_isp_reseller.doc_id and isp_nodes.doc_id = '$doc_id' and isp_nodes.doctype_id = '$doctype_id'");
    }

    // User für Reseller hinzufügen, wenn noch nicht angelegt
    if($reseller["reseller_userid"] == 0 and $reseller["reseller_user"] != ""){


        // Check Ob User noch nicht in sys_user existiert
        $sys_user_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as documents from sys_user where username = '".trim($reseller["reseller_user"])."'");

        if($sys_user_count["documents"] > 1) {
            // Es existiert bereits ein ISPConfig User dieses Namens, Löschen der Reseller_userfelder und Fehlermeldung
            $go_api->db->query("UPDATE isp_isp_reseller SET reseller_passwort = '', reseller_user = '', reseller_userid = 0 where doc_id = '$doc_id'");
            if($die_on_error){
              $go_api->errorMessage($go_api->lng("error_user_exist_1")." ".$go_api->lng("error_user_exist_2").$go_api->lng("weiter_link"));
            } else {
              return $go_api->lng("error_user_exist_1")." ".$go_api->lng("error_user_exist_2");
            }

        } else {

            // User hinzufügen

            /////////////////////////////////////////
            // Wenn User noch nicht existiert,
            // User in sys_user hinzufügen
            /////////////////////////////////////////
            if($go_api->auth->check_write(0)) {
                        $datum = time();
                        $userid = $go_info["user"]["userid"];
            $username = $reseller["reseller_user"];
            $passwort = substr($reseller["reseller_passwort"],5);
            if($reseller["limit_dns_manager"] == 1) {
              // DNS-Reseller anlegen
              if($reseller["limit_web"] == 0 && $reseller["limit_user"] == 0 && $reseller["limit_domain"] == 0){
                $modul_str = "dns";
                $modules_str = "dns,tools,help";
              } else {
                $modul_str = "isp";
                $modules_str = "isp,dns,isp_file,tools,help";
              }
            } else {
                $modul_str = "isp";
                $modules_str = "isp,isp_file,tools,help";
            }

                        $sql = "INSERT INTO sys_user (userid, username, passwort,gueltig,perms,modules,modul,design,site) VALUES ('$userid', '$username', '$passwort','1','rw','$modules_str','$modul_str','blau','ISPConfig')";
                        $go_api->db->query($sql);
                        $userid = $go_api->db->insertID();

            $sql = "INSERT INTO sys_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('1','0','','a','1','1','$userid','')";
                        $go_api->db->query($sql);
            $gid = $reseller["reseller_group"];

                        // User zur Gruppe des Resellers hinzufügen
                        $sql = "INSERT INTO user_groups (groupid, userid, perms, userstatus, username) VALUES ('$gid', '$userid', 'rwa', '1', '$username')";
                        $go_api->db->query($sql);
            $go_api->db->query("UPDATE isp_isp_reseller SET reseller_userid = '$userid' where doc_id = '$doc_id'");
                    }
        }
    }

    if($reseller["reseller_userid"] > 0 and $go_info["server"]["mode"] != "demo") {
    // Reseller ispconfig_user updaten
    // sys Userdaten holen
    $sys_user = $go_api->db->queryOneRecord("SELECT * from sys_user where doc_id = '".$reseller["reseller_userid"]."'");

        // Wenn sich nur Passwort ändert
        if($sys_user["username"] == $reseller["reseller_user"] and $reseller["reseller_passwort"] != "") {
            $passwort = substr($reseller["reseller_passwort"],5);
            $sql = "UPDATE sys_user SET passwort = '$passwort' where doc_id = ".$reseller["reseller_userid"];
            //die($sql);
            $go_api->db->query($sql);
        }

        if($sys_user["username"] != $reseller["reseller_user"]) {
            // Check Ob User noch nicht in sys_user existiert
            $sys_user_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as documents from sys_user where username = '".trim($reseller["reseller_user"])."'");
            if($sys_user_count["documents"] == 0) {
                //if(trim($reseller["reseller_passwort"]) == "") $go_api->errorMessage("Es kann kein User mit leerem Passwort angelegt werden.");
                $passwort = substr($reseller["reseller_passwort"],5);
                $go_api->db->query("UPDATE sys_user SET username = '".trim($reseller["reseller_user"])."' where doc_id = ".$reseller["reseller_userid"]);
            } else {
                $go_api->db->query("update isp_isp_reseller set reseller_user = '".$sys_user["username"]."' where doc_id = $doc_id");
                if($die_on_error){
                  $go_api->errorMessage($go_api->lng("error_user_exist_1")." ".$go_api->lng("error_user_exist_2").$go_api->lng("weiter_link"));
                } else {
                  return $go_api->lng("error_user_exist_1")." ".$go_api->lng("error_user_exist_2");
                }

            }
        }

        // Reseller Module updaten
        if($reseller["limit_dns_manager"] == 1) {
              // DNS-Reseller anlegen
            if($reseller["limit_web"] == 0 && $reseller["limit_user"] == 0 && $reseller["limit_domain"] == 0){
                $modul_str = "dns";
                $modules_str = "dns,tools,help";
            } else {
                $modul_str = "isp";
                $modules_str = "isp,dns,isp_file,tools,help";
            }
        } else {
                $modul_str = "isp";
                $modules_str = "isp,isp_file,tools,help";
        }
        $go_api->db->query("UPDATE sys_user SET modul = '$modul_str',  modules = '$modules_str' where doc_id = ".$reseller["reseller_userid"]);

    }
}

function reseller_delete($doc_id, $doctype_id, $action, $die_on_error = '1') {
    global $go_api, $go_info;

    // beim löschen
    if($action == 'do') {
    $anbieter = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_reseller where doc_id = $doc_id");

    $groupid = $anbieter["reseller_group"];

    if($groupid != 0) {
        // Überprüfen, ob noch Webs dieses Resellers existieren
        $records = $go_api->db->queryOneRecord("SELECT count(doc_id) as anzahl from isp_nodes where groupid = $groupid and doctype_id != 0");

            if($records["anzahl"] == 0) {
                // Löschen, wenn Keine Webs, Domains, User des Resellers mehr da sind
                $sys_user_id = $anbieter["reseller_userid"];
                // Löschen des ISPConfig Users
                $go_api->db->query("DELETE FROM sys_user where doc_id = $sys_user_id");
                $go_api->db->query("DELETE FROM sys_nodes where doctype_id = 1 and doc_id = $sys_user_id");
                // Löschen der Gruppe
                $go_api->db->query("DELETE FROM groups where groupid = $groupid");

                // Löschen des Gruppen-Users
                $go_api->db->query("DELETE FROM user_groups where groupid = $groupid");

                // Löschen der ISP Nodes
                $go_api->db->query("DELETE FROM isp_nodes where groupid = $groupid");

            } else {
                if($die_on_error){
                  $go_api->errorMessage($go_api->lng("error_anbieter_no_delete").$go_api->lng("weiter_link"));
                } else {
                  return $go_api->lng("error_anbieter_no_delete");
                }
            }
        }
    } else {
      if($die_on_error){
        $go_api->errorMessage($go_api->lng("error_anbieter_no_restore").$go_api->lng("weiter_link"));
      } else {
        return $go_api->lng("error_anbieter_no_restore");
      }
    }

}




}
?>