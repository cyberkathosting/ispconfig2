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
class isp_web
{

var $path_httpd_conf;
var $path_httpd_root;
var $directory_mode = "0770";
var $web_doctype_id = 1013;
var $user_doctype_id = 1014;
var $domain_doctype_id = 1015;
var $kunde_doctype_id = 1012;
var $datenbank_doctype_id = 1029;
var $vhost_conf;
var $sendmail_cw;
var $virtusertable;
var $user_von;
var $group_von;
var $server_conf;

//Constructor
function isp_web() {
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

function web_show($doc_id, $doctype_id) {
    global $go_api, $go_info, $doc, $tablevalues;
//print_r($doc->deck[0]);
        ///////////////////////////////////////////////////////////////////////////
        // $tablevalues enthält daten, $doc enthält dokumententyp Repräsentation.
        ///////////////////////////////////////////////////////////////////////////

        // schalte SSL Tab unsichtbar, wenn deaktiviert
        if($tablevalues["web_ssl"] == 0) $doc->deck[3]->visible = 0;

        // Faktura Tab nur für Admin sichtbar
        if($go_info["user"]["userid"] != 1) $doc->deck[7]->visible = 0;

        // mySQL modifikationen
        if($tablevalues["web_mysql"] == 0 || $tablevalues["web_mysql_anzahl_dbs"] == 0) {
                $doc->deck[6]->elements[0]->visible = 0;
        }
        // alte MySQL-Felder immer unsichtbar schalten
        $doc->deck[6]->elements[1]->visible = 0;
        $doc->deck[6]->elements[2]->visible = 0;
        $doc->deck[6]->elements[3]->visible = 0;
        $doc->deck[6]->elements[4]->visible = 0;

        // Website-Status nur anzeigen, wenn Website gesperrt ist
        $web = $go_api->db->queryOneRecord("SELECT isp_isp_web.web_traffic_status FROM isp_nodes,isp_isp_web WHERE isp_nodes.doc_id = '$doc_id' AND isp_nodes.doctype_id = '$doctype_id' AND isp_isp_web.doc_id = '$doc_id'");
        if($web['web_traffic_status'] == 2){
          $doc->deck[0]->elements[39]->visible = 1;
        } else {
          $doc->deck[0]->elements[39]->visible = 0;
        }

        // Individuelle Fehler-Seiten unsichtbar schalten, wenn nicht aktiviert
        if($tablevalues["web_individual_error_pages"] == 0) {
                $doc->deck[6]->elements[9]->visible = 0;
                $doc->deck[6]->elements[10]->visible = 0;
                $doc->deck[6]->elements[11]->visible = 0;
                $doc->deck[6]->elements[12]->visible = 0;
                $doc->deck[6]->elements[13]->visible = 0;
                $doc->deck[6]->elements[14]->visible = 0;
                $doc->deck[6]->elements[15]->visible = 0;
                $doc->deck[6]->elements[16]->visible = 0;
                $doc->deck[6]->elements[17]->visible = 0;
        }

        // Reseller Limits
        // Hole Reseller Daten
        $user_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $doc_id and doctype_id = $doctype_id");
            $resellerid = $user_node["groupid"];
            unset($user_node);

        $reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller WHERE reseller_group = $resellerid");

        if (!$reseller && $go_info["user"]["userid"] != 1 && is_array($go_api->groups->myGroups())) {
            $reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller WHERE reseller_userid = " . $go_info["user"]["userid"]);
        }

        if ($reseller) {

		if($reseller["limit_dns_manager"] != 1){
		  $doc->deck[0]->elements[5]->visible = 0;
		  $doc->deck[0]->elements[6]->visible = 0;
		}
                if($reseller["limit_shell_access"] != 1) $doc->deck[0]->elements[15]->visible = 0;
                if($reseller["limit_cgi"] != 1 && $reseller["limit_cgi_mod_perl"] != 1){
                  $doc->deck[0]->elements[16]->visible = 0;
                  $doc->deck[0]->elements[17]->visible = 0;
                  $doc->deck[0]->elements[20]->visible = 0;
                } else {
                  if($reseller["limit_standard_cgis"] != 1) $doc->deck[0]->elements[20]->visible = 0;
                  if($reseller["limit_cgi"] != 1 || $reseller["limit_cgi_mod_perl"] != 1) {
		    $mode = ($reseller["limit_cgi_mod_perl"] == 1) ? 1 : 0;
		    $doc->deck[0]->elements[17]->values = array("$mode" => $doc->deck[0]->elements[17]->values[$mode]);
		  }
                }
                if($reseller["limit_php"] != 1){
                  $doc->deck[0]->elements[21]->visible = 0;
                  $doc->deck[0]->elements[22]->visible = 0;
                }
                if($reseller["limit_ssi"] != 1) $doc->deck[0]->elements[23]->visible = 0;
                if($reseller["limit_ftp"] != 1) $doc->deck[0]->elements[24]->visible = 0;
                if($reseller["limit_mysql"] != 1){
                  $doc->deck[0]->elements[26]->visible = 0;
                  $doc->deck[0]->elements[27]->visible = 0;
                  $doc->deck[0]->elements[28]->visible = 0;
                }
                if($reseller["limit_ssl"] != 1) $doc->deck[0]->elements[29]->visible = 0;
                if($reseller["limit_anonftp"] != 1) $doc->deck[0]->elements[30]->visible = 0;
                if($reseller["limit_anonftp"] != 1) $doc->deck[0]->elements[31]->visible = 0;
                if($reseller["limit_wap"] != 1) $doc->deck[0]->elements[33]->visible = 0;
                if($reseller["limit_error_pages"] != 1) $doc->deck[0]->elements[34]->visible = 0;
                if($reseller["limit_httpd_include"] != 1){
                  $doc->deck[0]->elements[37]->visible = 0;
                  $doc->deck[0]->elements[38]->visible = 0;
                }

                if($reseller["limit_frontpage"] != 1) {
                  $doc->deck[0]->elements[23]->visible = 0;
                  $doc->deck[6]->elements[5]->visible = 0;
                }
        }

        // überprüfe welche Funktionen aktiv sind
        $server_id = $tablevalues["server_id"];
        if($server_id == "") $server_id = 1;
        $server = $go_api->db->queryOneRecord("SELECT * from isp_server where doc_id = '$server_id'");
        // Deaktiviere Frontpage
        if($server["server_enable_frontpage"] != 1) {
            $doc->deck[0]->elements[25]->visible = 0;
            $doc->deck[6]->elements[5]->visible = 0;
        }
	// Deaktiviere mod_perl
	if($server["server_httpd_mod_perl"] != 1) {
	    $doc->deck[0]->elements[17]->visible = 0;
	}

                // Hostingplan anwenden
                $vorlage_id = intval($_REQUEST["vorlage_id"]);
                if(!empty($vorlage_id)) {

                        // Hole Vorlage
                        $vorlage = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_web_template WHERE doc_id = $vorlage_id");

                        $doc->deck[0]->elements[8]->value = $vorlage["web_speicher"];
                        $doc->deck[0]->elements[9]->value = $vorlage["web_traffic"];
                        $doc->deck[0]->elements[10]->value = $vorlage["web_traffic_ueberschreitung"];
                        $doc->deck[0]->elements[11]->value = $vorlage["web_mailquota"];
                        $doc->deck[0]->elements[12]->value = $vorlage["web_userlimit"];
                        $doc->deck[0]->elements[13]->value = $vorlage["web_domainlimit"];
                        $doc->deck[0]->elements[15]->value = $vorlage["web_shell"];
                        $doc->deck[0]->elements[16]->value = $vorlage["web_cgi"];
                        $doc->deck[0]->elements[17]->value = $vorlage["web_standard_cgi"];
                        $doc->deck[0]->elements[18]->value = $vorlage["web_php"];
                        $doc->deck[0]->elements[19]->value = $vorlage["web_php_safe_mode"];
                        $doc->deck[0]->elements[21]->value = $vorlage["web_ssi"];
                        $doc->deck[0]->elements[22]->value = $vorlage["web_ftp"];
                        $doc->deck[0]->elements[23]->value = $vorlage["web_frontpage"];
                        $doc->deck[0]->elements[24]->value = $vorlage["web_mysql"];
                        $doc->deck[0]->elements[25]->value = $vorlage["web_mysql_anzahl_dbs"];
                        $doc->deck[0]->elements[26]->value = $vorlage["web_mysql_quota"];
                        $doc->deck[0]->elements[27]->value = $vorlage["web_ssl"];
                        $doc->deck[0]->elements[28]->value = $vorlage["web_anonftp"];
                        $doc->deck[0]->elements[29]->value = $vorlage["web_anonftplimit"];
                        $doc->deck[0]->elements[31]->value = $vorlage["web_wap"];
                        $doc->deck[0]->elements[32]->value = $vorlage["web_individual_error_pages"];
                        $doc->deck[0]->elements[34]->value = $vorlage["web_mailuser_login"];
                        $doc->deck[0]->elements[36]->value = $vorlage["web_httpd_include"];
                }


}



function web_insert($doc_id, $doctype_id, $die_on_error = '1') {
    global $go_api, $go_info, $form_changed,$old_form_data;
    $status = '';
    $go_api->db->query("UPDATE isp_isp_web SET status = 'n' where doc_id = '$doc_id'");

    $web = $go_api->db->queryOneRecord("select * from isp_nodes,isp_isp_web where isp_nodes.doc_id = '$doc_id' and isp_nodes.doctype_id = '1013' and isp_isp_web.doc_id = '$doc_id'");
    $webcount = $go_api->db->queryOneRecord("SELECT count(doc_id) as web_count from isp_isp_web where web_host = '".$web["web_host"]."' and web_domain = '".$web["web_domain"]."'");
    if($webcount["web_count"] > 1  or $go_api->db->queryOneRecord("SELECT doc_id from isp_isp_domain where domain_host = '".$web["web_host"]."' and domain_domain = '".$web["web_domain"]."'")) {
        $go_api->db->query("DELETE from isp_isp_web where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        if($die_on_error){
          $go_api->errorMessage($go_api->lng("error_web_doppelt")." ".$web["web_host"].".".$web["web_domain"]." ".$go_api->lng("angelegt").$go_api->lng("weiter_link"));
        } else {
          return $go_api->lng("error_web_doppelt")." ".$web["web_host"].".".$web["web_domain"]." ".$go_api->lng("angelegt");
        }
    }

    /////// Prüfen, ob Diskspace, User- und Domainanzahl innerhalb der Grenzen des Resellers liegen ////////
    $user_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $doc_id and doctype_id = $doctype_id");
    $resellerid = $user_node["groupid"];
    unset($user_node);
    if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
      // Diskspace
      if($reseller["limit_disk"] >= 0){
        $diskspace = $go_api->db->queryOneRecord("SELECT sum(isp_isp_web.web_speicher) as diskspace, sum(isp_isp_web.web_mailquota) as mailquota, sum(isp_isp_web.web_mysql_quota) as mysqlquota from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '$resellerid' and isp_nodes.doctype_id = ".$this->web_doctype_id);
	if ($web["web_mysql"] != 1) { $web["web_mysql_quota"] = 0; $diskspace["mysqlquota"] = 0; }
        $diskspace = $diskspace["diskspace"] + $diskspace["mailquota"] + $diskspace["mysqlquota"];
        if(($web["web_speicher"] < 0) || ($web["web_mailquota"] < 0) || ($web["web_mysql_quota"] < 0)){
          $diskspace -= $web["web_speicher"] + $web["web_mailquota"] + $web["web_mysql_quota"];
          $free = $reseller["limit_disk"] - $diskspace;
          $limit_errors .= $go_api->lng("error_web_no_unlimited_diskspace_1").$free.$go_api->lng("error_web_no_unlimited_diskspace_2");
        } else {
          $free = $reseller["limit_disk"] - $diskspace;
          if($free < 0){
            $max_free = $free + $web["web_speicher"] + $web["web_mailquota"] + $web["web_mysql_quota"];
            $limit_errors .= $go_api->lng("error_web_no_diskspace_free_1").$max_free.$go_api->lng("error_web_no_diskspace_free_2");
          }
        }
      }
      // Useranzahl
      if($reseller["limit_user"] >= 0){
        $useranzahl = $go_api->db->queryOneRecord("SELECT sum(isp_isp_web.web_userlimit) as useranzahl from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '$resellerid' and isp_nodes.doctype_id = ".$this->web_doctype_id);
        $useranzahl = $useranzahl["useranzahl"];
        if($web["web_userlimit"] < 0){
          $useranzahl -= $web["web_userlimit"];
          $free = $reseller["limit_user"] - $useranzahl;
          $limit_errors .= $go_api->lng("error_web_no_unlimited_users_1").$free.$go_api->lng("error_web_no_unlimited_users_2");
        } else {
          $free = $reseller["limit_user"] - $useranzahl;
          if($free < 0){
            $max_free = $free + $web["web_userlimit"];
            $limit_errors .= $go_api->lng("error_web_no_users_free_1").$max_free.$go_api->lng("error_web_no_users_free_2");
          }
        }
      }
      // Domains
      if($reseller["limit_domain"] >= 0){
        $domainanzahl = $go_api->db->queryOneRecord("SELECT sum(isp_isp_web.web_domainlimit) as domainanzahl from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '$resellerid' and isp_nodes.doctype_id = ".$this->web_doctype_id);
        $domainanzahl = $domainanzahl["domainanzahl"];
        if($web["web_domainlimit"] < 0){
          $domainanzahl -= $web["web_domainlimit"];
          $free = $reseller["limit_domain"] - $domainanzahl;
          $limit_errors .= $go_api->lng("error_web_no_unlimited_domains_1").$free.$go_api->lng("error_web_no_unlimited_domains_2");
        } else {
          $free = $reseller["limit_domain"] - $domainanzahl;
          if($free < 0){
            $max_free = $free + $web["web_domainlimit"];
            $limit_errors .= $go_api->lng("error_web_no_domains_free_1").$max_free.$go_api->lng("error_web_no_domains_free_2");
          }
        }
      }
      // andere Limits
      if($reseller["limit_shell_access"] != 1 && $web["web_shell"] == 1) $limit_errors .= $go_api->lng("error_web_no_shell_access");
      if($reseller["limit_cgi"] != 1 && $web["web_cgi"] == 1 && $web["web_cgi_mod_perl"] != 1) $limit_errors .= $go_api->lng("error_web_no_cgi");
      if($reseller["limit_cgi_mod_perl"] != 1 && $web["web_cgi"] && $web["web_cgi_mod_perl"] == 1) $limit_errors .= $go_api->lng("error_web_no_cgi_mod_perl");
      if(($reseller["limit_standard_cgis"] != 1 || $reseller["limit_cgi"] != 1) && $web["web_standard_cgi"] == 1) $limit_errors .= $go_api->lng("error_web_no_standard_cgi");
      if($reseller["limit_php"] != 1 && ($web["web_php"] == 1 || $web[" web_php_safe_mode"] == 1)) $limit_errors .= $go_api->lng("error_web_no_php");
      if($reseller["limit_ssi"] != 1 && $web["web_ssi"] == 1) $limit_errors .= $go_api->lng("error_web_no_ssi");
      if($reseller["limit_ftp"] != 1 && $web["ftp"] == 1) $limit_errors .= $go_api->lng("error_web_no_ftp");
      if($reseller["limit_mysql"] != 1 && $web["web_mysql"] == 1) $limit_errors .= $go_api->lng("error_web_no_mysql");
      // Datenbanken
      if($web["web_mysql"] == 1 && $reseller["limit_mysql_anzahl_dbs"] >= 0){
        $datenbankanzahl = $go_api->db->queryOneRecord("SELECT sum(isp_isp_web.web_mysql_anzahl_dbs) as datenbankanzahl from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '$resellerid' and isp_nodes.doctype_id = ".$this->web_doctype_id);
        $datenbankanzahl = $datenbankanzahl["datenbankanzahl"];
        $free = $reseller["limit_mysql_anzahl_dbs"] - $datenbankanzahl;
        if($free < 0){
          $max_free = $free + $web["web_mysql_anzahl_dbs"];
          $limit_errors .= $go_api->lng("error_web_no_databases_free_1").$max_free.$go_api->lng("error_web_no_databases_free_2");
        }
      }
      if($reseller["limit_ssl"] != 1 && $web["web_ssl"] == 1) $limit_errors .= $go_api->lng("error_web_no_ssl");
      if($reseller["limit_anonftp"] != 1 && $web["web_anonftp"] == 1) $limit_errors .= $go_api->lng("error_web_no_anonftp");
      if($reseller["limit_wap"] != 1 && $web["web_wap"] == 1) $limit_errors .= $go_api->lng("error_web_no_wap");
      if($reseller["limit_error_pages"] != 1 && $web["web_individual_error_pages"] == 1) $limit_errors .= $go_api->lng("error_web_no_individual_error_pages");
      if($reseller["limit_httpd_include"] != 1 && $web["web_httpd_include"] != "") $limit_errors .= $go_api->lng("error_web_no_httpd_include");
      if($reseller["limit_frontpage"] != 1 && $web["web_frontpage"] == 1) $limit_errors .= $go_api->lng("error_web_no_frontpage");

      if(isset($limit_errors)){
        $go_api->db->query("DELETE from isp_isp_web where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
	$go_api->db->query("ALTER TABLE isp_isp_web AUTO_INCREMENT = 1"); // Im Fehlerfall nächstes Mal wieder die gleiche ID benutzen
        if($die_on_error){
          $go_api->errorMessage($limit_errors.$go_api->lng("weiter_link"));
        } else {
          return $limit_errors;
        }
        unset($limit_errors);
      }
    }

    /////// Prüfen, ob Diskspace, User- und Domainanzahl innerhalb der Grenzen des Resellers liegen ENDE ////////

        ////////////////////////////////////////
    // Directory Index beim Insert setzen
    ////////////////////////////////////////

        $go_api->db->query("UPDATE isp_isp_web SET optionen_directory_index = 'index.html\r\nindex.htm\r\nindex.php\r\nindex.php5\r\nindex.php4\r\nindex.php3\r\nindex.shtml\r\nindex.cgi\r\nindex.pl\r\nindex.jsp\r\nDefault.htm\r\ndefault.htm' where doc_id = '$doc_id'");

    ////////////////////////////////////////
    // mySQL User Passwort verschlüsseln
    ////////////////////////////////////////

    if(substr($web["optionen_mysql_passwort"],0,5) != "||||:" and $web["optionen_mysql_passwort"] != "") {
        $go_api->db->query("UPDATE isp_isp_web SET optionen_mysql_passwort = concat('||||:' , password(optionen_mysql_passwort)) where doc_id = '$doc_id'");
    }

        ////////////////////////////////////////
    // mySQL User anlegen
    ////////////////////////////////////////

    $go_api->db->query("UPDATE isp_isp_web SET optionen_mysql_user = 'web".$doc_id."' where doc_id = '$doc_id'");


    /////////////////////////////////////////////////////////////
    // Check ob Reseller weiteres Web hinzufügen darf
    // Check Ob Reseller HTTPD Includes benutzen darf
    /////////////////////////////////////////////////////////////

     // Check Ob maximale Anzahl Webs des Resellers erreicht ist
     $sql = "SELECT count(doc_id) as webs from isp_nodes where groupid = '$resellerid' and doctype_id = ".$this->web_doctype_id;
     $webanzahl = $go_api->db->queryOneRecord($sql);
     $webanzahl = $webanzahl["webs"];
         $reseller_web_div = 1;

     if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
        // Wenn Resellerlimit erreicht wurde
        if($reseller["limit_web"] >= 0) {
                        $reseller_web_div = $reseller["limit_web"] - $webanzahl;
            if($webanzahl > $reseller["limit_web"]) {
                $go_api->db->query("DELETE from isp_isp_web where doc_id = '$doc_id'");
                $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
                if($die_on_error){
                  $go_api->errorMessage($go_api->lng("error_max_web_anbieter").$go_api->lng("weiter_link"));
                } else {
                  return $go_api->lng("error_max_web_anbieter");
                }
            }
        }
        // Check, HTTPD Includes nicht erlaubt
        if($go_info["user"]["userid"] != 1 and $reseller["limit_httpd_include"] != 1) {
            // checke, ob sich httpd Include Feld geändert hat.
            if($old_form_data["web_httpd_include"] != $web["web_httpd_include"]) {
                $go_api->db->query("UPDATE isp_isp_web SET web_httpd_include = '# HTTPD-Includes not available.\n# HTTPD-Includes nicht möglich.' where doc_id = $doc_id");
            }
        }

     }

    
    // Check ob lokaler Host als MX eingetragen ist
    if (!empty($web["web_host"])) {
        $host = $web["web_host"] . ".";
    }
    getmxrr($host . $web["web_domain"], $mx_list);

    $mx_found = false;
    foreach ($mx_list as $mx) {
        if (ip2long(gethostbyname($mx)) == ip2long($web["web_ip"])) {
            $mx_found = true;
        }
    }

    if (!$mx_found) {
        $go_api->db->query("UPDATE isp_isp_web SET optionen_local_mailserver = '' WHERE doc_id = '$doc_id'");
    }


    //////////////////////////////////////////////////////
    // Check ob bereits ein SSL Cert auf der IP Existiert
    //////////////////////////////////////////////////////

    $ssl_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as ssl_count from isp_isp_web where web_ip = '".$web["web_ip"]."' and web_ssl = 1");
    if($ssl_count["ssl_count"] > 1) {
        // Es existiert bereits ein SSL Web mit dieser IP
        $status = "NOTIFY";
        $errorMessage .= $go_api->lng("error_web_ssl_exist");
        $go_api->db->query("UPDATE isp_isp_web set web_ssl = 0 where doc_id = $doc_id");
    }

     ////////////////////////////////////////////////////
     // Userid des Webs auf ISPConfig-Userid des Kunden setzen
     ////////////////////////////////////////////////////

     // Kunde zu Web bestimmen
     $sql = "SELECT * from isp_isp_kunde, isp_dep where isp_dep.parent_doc_id = isp_isp_kunde.doc_id
     and isp_dep.child_doc_id = $doc_id and isp_dep.child_doctype_id = $doctype_id and
     isp_dep.parent_doctype_id = ".$this->kunde_doctype_id;

     $kunde = $go_api->db->queryOneRecord($sql);
     $webadmin_userid = $kunde["webadmin_userid"];

     // UserID updaten
     $go_api->db->query("UPDATE isp_nodes SET userid = $webadmin_userid where doc_id = $doc_id and doctype_id = $doctype_id");

    ///////////////////////////////////
    // Automatisch DNS Records erzeugen
    ///////////////////////////////////

    if($web["web_dns"] == 1) {
        $this->_insert_dns($doc_id,$doctype_id,$web);
        //$go_api->db->query("UPDATE isp_isp_web SET web_dns = '' where doc_id = $doc_id");
    }

    ///////////////////////////////////////////////////////
    // Automatisch FTP aktivieren, wenn shell access aktiv
    ///////////////////////////////////////////////////////

    if($web["web_shell"] == 1 and $web["web_ftp"] != 1) {
        $this->_insert_dns($doc_id,$doctype_id,$web);
        $go_api->db->query("UPDATE isp_isp_web SET web_ftp = '1' where doc_id = $doc_id");
    }

    /////////////////////////////////////////////////////
    // co-Domain ohne host anlegen, wenn Domain host hat
    // und die Domain selbst noch nicht angelegt wurde.
    /////////////////////////////////////////////////////

        $reseller_domain_div = 1;
        $sql = "SELECT count(doc_id) as domains from isp_nodes where groupid = '$resellerid' and doctype_id = ".$this->domain_doctype_id;
    $domainanzahl = $go_api->db->queryOneRecord($sql);
    $domainanzahl = $domainanzahl["domains"];
        if($reseller["limit_domain"] > 0) $reseller_domain_div = $reseller["limit_domain"] - $domainanzahl;

        // nur anlegen, wenn resellerlimit noch nicht erreicht
    if($web["web_host"] != "" and $reseller_domain_div > 0) {
    // Überprüfe, ob Domain noch nicht existiert
    $host_codomain_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as anzahl from isp_isp_domain where (domain_host = '' OR domain_host IS NULL) and domain_domain = '".$web["web_domain"]."'");
    $host_webdomain_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as anzahl from isp_isp_web where (web_host = '' OR web_host IS NULL) and web_domain = '".$web["web_domain"]."'");

        if($host_codomain_count["anzahl"] == 0 and $host_webdomain_count["anzahl"] == 0) {
            // anlegen einer "hostlosen" Co-Domain
            $go_api->db->query("INSERT INTO isp_isp_domain (domain_domain,domain_ip,status,domain_dns,domain_dnsmail) VALUES ('".$web["web_domain"]."','".$web["web_ip"]."','n',0,0)");
            $codomain_doc_id = $go_api->db->insertID();

            $userid = $go_info["user"]["userid"];
            $groupid = $web["groupid"];
            $domain_doctype_id = $this->domain_doctype_id;
            $web_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $doc_id and doctype_id = $doctype_id");
            $sql = "INSERT INTO isp_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('".$web_node["userid"]."','".$web_node["groupid"]."','','a','$domain_doctype_id','1','$codomain_doc_id','')";
            $go_api->db->query($sql);
            $codomain_tree_id = $go_api->db->insertID();
            $sql = "INSERT INTO isp_dep (userid,parent_doc_id,parent_doctype_id,parent_tree_id,child_doc_id,child_doctype_id,child_tree_id,status,groupid) VALUES ('$userid','$doc_id','$doctype_id','".$web_node["tree_id"]."','$codomain_doc_id','$domain_doctype_id','$codomain_tree_id','1','".$web_node["groupid"]."')";
            $go_api->db->query($sql);

            // anlegen eines a-records für co-Domain, wenn bei web
            //if($web["web_dns"] == 1) {
            //    $go_api->uses("isp_domain");
            //    $domain = $go_api->db->queryOneRecord("SELECT * from isp_isp_domain where doc_id = '$codomain_doc_id'");
            //    $go_api->isp_domain->_insert_dns($codomain_doc_id,$domain_doctype_id,$domain);
            //}

            // Check ob lokaler Host als MX eingetragen ist
            getmxrr($web["web_domain"], $mx_list);

            $mx_found = false;
            foreach ($mx_list as $mx) {
                if (ip2long(gethostbyname($mx)) == ip2long($web["web_ip"])) {
                    $mx_found = true;
                }
            }

            if (!$mx_found) {
                $go_api->db->query("UPDATE isp_isp_domain SET domain_local_mailserver = '' WHERE doc_id = '$codomain_doc_id'");
            }
        }

    unset($host_codomain_count);
    unset($host_webdomain_count);
    }

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');

    // Faktura Eintrag erzeugen
        $this->faktura_insert($doc_id,$web["web_host"].".".$web["web_domain"]);

    ///////////////////////////////////
    // NOTIFY Error Handler
    ///////////////////////////////////

    // Dieses muss die letzte Eintrag der Funktion sein
    if($status == "NOTIFY"){
      if($die_on_error){
        $go_api->errorMessage($errorMessage.$go_api->lng("weiter_link"));
      } else {
        return $errorMessage;
      }
    }

}


function web_update($doc_id,$doctype_id, $die_on_error = '1') {
global $go_api, $go_info, $old_form_data;
$go_api->db->query("UPDATE isp_isp_web SET status = 'u' where status != 'n' and doc_id = '$doc_id'");

    $web = $go_api->db->queryOneRecord("select * from isp_nodes,isp_isp_web where isp_nodes.doc_id = '$doc_id' and isp_nodes.doctype_id = '1013' and isp_isp_web.doc_id = '$doc_id'");

    /////////////////////////////////////////////
    // Check Ob Domainname bereits vergeben
    /////////////////////////////////////////////

    $webcount = $go_api->db->queryOneRecord("SELECT count(doc_id) as web_count from isp_isp_web where web_host = '".$web["web_host"]."' and web_domain = '".$web["web_domain"]."' and doc_id != $doc_id");
    //$domaincount = $go_api->db->queryOneRecord("SELECT count(doc_id) as domain_count from isp_isp_domain where domain_host = '".$web["web_host"]."' and domain_domain = '".$web["web_domain"]."'");
    //die(print_r($old_form_data));
    if($webcount["web_count"] > 0) {

        // alte Webeinstellungen wiederherstellen
        $go_api->db->query("UPDATE isp_isp_web SET web_host = '".$old_form_data["web_host"]."', web_domain = '".$old_form_data["web_domain"]."' where doc_id = $doc_id");
        $status = "NOTIFY";
        $errorMessage .= $go_api->lng("error_web_doppelt")." ".$web["web_host"].".".$web["web_domain"]." ".$go_api->lng("angelegt");
        }

    /////// Prüfen, ob Diskspace, User- und Domainanzahl innerhalb der Grenzen des Resellers liegen ////////
    
    // TODO: Hier fehlen durchweg noch Prüfungen ob das Web ggf. schon mehr benutzt (bzw. an User vergeben hat)
    //       als zugewiesen werden soll
    
    $user_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $doc_id and doctype_id = $doctype_id");
    $resellerid = $user_node["groupid"];
    unset($user_node);
    if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
      // Diskspace
      if($reseller["limit_disk"] >= 0){
        $diskspace = $go_api->db->queryOneRecord("SELECT sum(isp_isp_web.web_speicher) as diskspace, sum(isp_isp_web.web_mailquota) as mailquota, sum(isp_isp_web.web_mysql_quota) as mysqlquota from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '$resellerid' and isp_nodes.doctype_id = ".$this->web_doctype_id);
	if ($web["web_mysql"] != 1) { $web["web_mysql_quota"] = 0; $diskspace["mysqlquota"] = 0; }
        $diskspace = $diskspace["diskspace"] + $diskspace["mailquota"] + $diskspace["mysqlquota"];
        if(($web["web_speicher"] < 0) || ($web["web_mailquota"] < 0) || ($web["web_mysql_quota"] < 0)){
          $diskspace -= $web["web_speicher"] + $web["web_mailquota"] + $web["web_mysql_quota"];
          $free = $reseller["limit_disk"] - $diskspace;
	  // Da sich hier 3 Werte eine Obergrenze teilen ist es schwer den Wert zu korrigieren, daher: Alte Werte behalten
          $go_api->db->query("UPDATE isp_isp_web SET web_speicher = '".$old_form_data["web_speicher"]."' WHERE doc_id = '".$doc_id."'");
          $go_api->db->query("UPDATE isp_isp_web SET web_mailquota = '".$old_form_data["web_mailquota"]."' WHERE doc_id = '".$doc_id."'");
          $go_api->db->query("UPDATE isp_isp_web SET web_mysql_quota = '".$old_form_data["web_mysql_quota"]."' WHERE doc_id = '".$doc_id."'");
          $status = "NOTIFY";
          $errorMessage .= $go_api->lng("error_web_no_unlimited_diskspace_1").$free.$go_api->lng("error_web_no_unlimited_diskspace_2");
        } else {
          $free = $reseller["limit_disk"] - $diskspace;
          if($free < 0){
            $max_free = $free + $web["web_speicher"] + $web["web_mailquota"] + $web["web_mysql_quota"];
            // Da sich hier 3 Werte eine Obergrenze teilen ist es schwer den Wert zu korrigieren, daher: Alte Werte behalten
            $go_api->db->query("UPDATE isp_isp_web SET web_speicher = '".$old_form_data["web_speicher"]."' WHERE doc_id = '".$doc_id."'");
            $go_api->db->query("UPDATE isp_isp_web SET web_mailquota = '".$old_form_data["web_mailquota"]."' WHERE doc_id = '".$doc_id."'");
            $go_api->db->query("UPDATE isp_isp_web SET web_mysql_quota = '".$old_form_data["web_mysql_quota"]."' WHERE doc_id = '".$doc_id."'");
            $status = "NOTIFY";
            $errorMessage .= $go_api->lng("error_web_no_diskspace_free_1").$max_free.$go_api->lng("error_web_no_diskspace_free_2");
          }
        }
      }
      // Useranzahl
      if($reseller["limit_user"] >= 0){
        $useranzahl = $go_api->db->queryOneRecord("SELECT sum(isp_isp_web.web_userlimit) as useranzahl from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '$resellerid' and isp_nodes.doctype_id = ".$this->web_doctype_id);
        $useranzahl = $useranzahl["useranzahl"];
        if($web["web_userlimit"] < 0){
          $useranzahl -= $web["web_userlimit"];
          $free = $reseller["limit_user"] - $useranzahl;
          if($free >= 0){
            $go_api->db->query("UPDATE isp_isp_web SET web_userlimit = '".$free."' WHERE doc_id = '".$doc_id."'");
          } else {
            $go_api->db->query("UPDATE isp_isp_web SET web_userlimit = '".$old_form_data["web_userlimit"]."' WHERE doc_id = '".$doc_id."'");
          }
          $status = "NOTIFY";
          $errorMessage .= $go_api->lng("error_web_no_unlimited_users_1").$free.$go_api->lng("error_web_no_unlimited_users_2");
        } else {
          $free = $reseller["limit_user"] - $useranzahl;
          if($free < 0){
            $max_free = $free + $web["web_userlimit"];
            if($max_free >= 0){
              $go_api->db->query("UPDATE isp_isp_web SET web_userlimit = '".$max_free."' WHERE doc_id = '".$doc_id."'");
            } else {
              $go_api->db->query("UPDATE isp_isp_web SET web_userlimit = '".$old_form_data["web_userlimit"]."' WHERE doc_id = '".$doc_id."'");
            }
            $status = "NOTIFY";
            $errorMessage .= $go_api->lng("error_web_no_users_free_1").$max_free.$go_api->lng("error_web_no_users_free_2");
          }
        }
      }
      // Domains
      if($reseller["limit_domain"] >= 0){
        $domainanzahl = $go_api->db->queryOneRecord("SELECT sum(isp_isp_web.web_domainlimit) as domainanzahl from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '$resellerid' and isp_nodes.doctype_id = ".$this->web_doctype_id);
        $domainanzahl = $domainanzahl["domainanzahl"];
        if($web["web_domainlimit"] < 0){
          $domainanzahl -= $web["web_domainlimit"];
          $free = $reseller["limit_domain"] - $domainanzahl;
          if($free >= 0){
            $go_api->db->query("UPDATE isp_isp_web SET web_domainlimit = '".$free."' WHERE doc_id = '".$doc_id."'");
          } else {
            $go_api->db->query("UPDATE isp_isp_web SET web_domainlimit = '".$old_form_data["web_domainlimit"]."' WHERE doc_id = '".$doc_id."'");
          }
          $status = "NOTIFY";
          $errorMessage .= $go_api->lng("error_web_no_unlimited_domains_1").$free.$go_api->lng("error_web_no_unlimited_domains_2");
        } else {
          $free = $reseller["limit_domain"] - $domainanzahl;
          if($free < 0){
            $max_free = $free + $web["web_domainlimit"];
            if($max_free >= 0){
              $go_api->db->query("UPDATE isp_isp_web SET web_domainlimit = '".$max_free."' WHERE doc_id = '".$doc_id."'");
            } else {
              $go_api->db->query("UPDATE isp_isp_web SET web_domainlimit = '".$old_form_data["web_domainlimit"]."' WHERE doc_id = '".$doc_id."'");
            }
            $status = "NOTIFY";
            $errorMessage .= $go_api->lng("error_web_no_domains_free_1").$max_free.$go_api->lng("error_web_no_domains_free_2");
          }
        }
      }

      // andere Limits
      if($reseller["limit_shell_access"] != 1 && $web["web_shell"] == 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_shell = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_shell_access");
      }
      if($reseller["limit_cgi"] != 1 && $web["web_cgi"] == 1 && $web["web_cgi_mod_perl"] != 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_cgi = '0', web_standard_cgi = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_cgi");
      }
      if($reseller["limit_cgi_mod_perl"] != 1 && $web["web_cgi"] == 1 && $web["web_cgi_mod_perl"] == 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_cgi = '0', web_standard_cgi = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_cgi_mod_perl");
      }
      if(($reseller["limit_standard_cgis"] != 1 || $reseller["limit_cgi"] != 1) && $web["web_standard_cgi"] == 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_standard_cgi = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_standard_cgi");
      }
      if($reseller["limit_php"] != 1 && ($web["web_php"] == 1 || $web[" web_php_safe_mode"] == 1)){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_php = '0', web_php_safe_mode = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_php");
      }
      if($reseller["limit_ssi"] != 1 && $web["web_ssi"] == 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_ssi = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_ssi");
      }
      if($reseller["limit_ftp"] != 1 && $web["ftp"] == 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_ftp = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_ftp");
      }
      if($reseller["limit_mysql"] != 1 && $web["web_mysql"] == 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_mysql = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_mysql");
      }
      // Datenbanken
      if($web["web_mysql"] == 1 && $reseller["limit_mysql_anzahl_dbs"] >= 0){
        $datenbankanzahl = $go_api->db->queryOneRecord("SELECT sum(isp_isp_web.web_mysql_anzahl_dbs) as datenbankanzahl from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '$resellerid' and isp_nodes.doctype_id = ".$this->web_doctype_id);
        $datenbankanzahl = $datenbankanzahl["datenbankanzahl"];
        $free = $reseller["limit_mysql_anzahl_dbs"] - $datenbankanzahl;
        if($free < 0){
          $max_free = $free + $web["web_mysql_anzahl_dbs"];
          if($max_free >= 0){
            $go_api->db->query("UPDATE isp_isp_web SET web_mysql_anzahl_dbs = '".$max_free."' WHERE doc_id = '".$doc_id."'");
          } else {
            $go_api->db->query("UPDATE isp_isp_web SET web_mysql_anzahl_dbs = '".$old_form_data["web_mysql_anzahl_dbs"]."' WHERE doc_id = '".$doc_id."'");
          }
          $status = "NOTIFY";
          $errorMessage .= $go_api->lng("error_web_no_databases_free_1").$max_free.$go_api->lng("error_web_no_databases_free_2");
        }
      }
      if($reseller["limit_ssl"] != 1 && $web["web_ssl"] == 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_ssl = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_ssl");
      }
      if($reseller["limit_anonftp"] != 1 && $web["web_anonftp"] == 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_anonftp = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_anonftp");
      }
      if($reseller["limit_wap"] != 1 && $web["web_wap"] == 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_wap = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_wap");
      }
      if($reseller["limit_error_pages"] != 1 && $web["web_individual_error_pages"] == 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_individual_error_pages = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_individual_error_pages");
      }
      if($reseller["limit_httpd_include"] != 1 && $web["web_httpd_include"] != ""){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_httpd_include = '' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_httpd_include");
      }
      if($reseller["limit_frontpage"] != 1 && $web["web_frontpage"] == 1){
        $status = "NOTIFY";
        $go_api->db->query("UPDATE isp_isp_web SET web_frontpage = '0' WHERE doc_id = '".$doc_id."'");
        $errorMessage .= $go_api->lng("error_web_no_frontpage");
      }
    }
    /////// Prüfen, ob Diskspace, User- und Domainanzahl innerhalb der Grenzen des Resellers liegen ENDE ////////

    /////// Prüfen, ob User-, Domain- und Datenbankanzahl oberhalb der schon angelegten User-/Domainanzahl liegen ////////
    // Web neu holen
    $web = $go_api->db->queryOneRecord("select * from isp_nodes,isp_isp_web where isp_nodes.doc_id = '$doc_id' and isp_nodes.doctype_id = '1013' and isp_isp_web.doc_id = '$doc_id'");
    // Hole Useranzahl
    $usercount = $go_api->db->queryOneRecord("SELECT count(isp_isp_user.doc_id) as doc_count from isp_isp_user, isp_dep where isp_isp_user.doc_id = isp_dep.child_doc_id and isp_isp_user.doctype_id = isp_dep.child_doctype_id and isp_dep.parent_doctype_id = $doctype_id and isp_dep.parent_doc_id = $doc_id and isp_dep.child_doctype_id = '".$this->user_doctype_id."'");

    $useranzahl = $usercount["doc_count"];
    if($useranzahl > $web["web_userlimit"] && $web["web_userlimit"] >= 0){
      $go_api->db->query("UPDATE isp_isp_web SET web_userlimit = '".$useranzahl."' WHERE doc_id = '".$doc_id."'");
      $status = "NOTIFY";
      $errorMessage .= $go_api->lng("error_web_no_user_decrease_1").$useranzahl.$go_api->lng("error_web_no_user_decrease_2");
    }

    // Hole Domainanzahl
    $domaincount = $go_api->db->queryOneRecord("SELECT count(isp_isp_domain.doc_id) as doc_count from isp_isp_domain, isp_dep where isp_isp_domain.doc_id = isp_dep.child_doc_id and isp_isp_domain.doctype_id = isp_dep.child_doctype_id and isp_dep.parent_doctype_id = $doctype_id and isp_dep.parent_doc_id = $doc_id and isp_dep.child_doctype_id = '".$this->domain_doctype_id."'");

    $domainanzahl = $domaincount["doc_count"] + 1;

    if($domainanzahl > $web["web_domainlimit"] && $web["web_domainlimit"] >= 0){
      $go_api->db->query("UPDATE isp_isp_web SET web_domainlimit = '".$domainanzahl."' WHERE doc_id = '".$doc_id."'");
      $status = "NOTIFY";
      $errorMessage .= $go_api->lng("error_web_no_domain_decrease_1").$useranzahl.$go_api->lng("error_web_no_domain_decrease_2");
    }

    // Hole Datenbankanzahl
    $datenbankcount = $go_api->db->queryOneRecord("SELECT count(isp_isp_datenbank.doc_id) as doc_count from isp_isp_datenbank, isp_dep where isp_isp_datenbank.doc_id = isp_dep.child_doc_id and isp_isp_datenbank.doctype_id = isp_dep.child_doctype_id and isp_dep.parent_doctype_id = $doctype_id and isp_dep.parent_doc_id = $doc_id and isp_dep.child_doctype_id = '".$this->datenbank_doctype_id."'");

    $datenbankanzahl = $datenbankcount["doc_count"];
    if($datenbankanzahl > $web["web_mysql_anzahl_dbs"] && $web["web_mysql_anzahl_dbs"] >= 0){
      $go_api->db->query("UPDATE isp_isp_web SET web_mysql_anzahl_dbs = '".$datenbankanzahl."' WHERE doc_id = '".$doc_id."'");
      $status = "NOTIFY";
      $errorMessage .= $go_api->lng("error_web_no_database_decrease_1").$datenbankanzahl.$go_api->lng("error_web_no_database_decrease_2");
    }

    $datenbankanzahl = $datenbankcount["doc_count"];
    if($datenbankanzahl > $web["web_mysql_anzahl_dbs"] && $web["web_mysql_anzahl_dbs"] >= 0){
      $go_api->db->query("UPDATE isp_isp_web SET web_mysql_anzahl_dbs = '".$datenbankanzahl."' WHERE doc_id = '".$doc_id."'");
      $status = "NOTIFY";
      $errorMessage .= $go_api->lng("error_web_no_database_decrease_1").$datenbankanzahl.$go_api->lng("error_web_no_database_decrease_2");
    }
    /////// Prüfen, ob User-, Domain- und Datenbankanzahl oberhalb der schon angelegten User-/Domainanzahl liegen ENDE ////////

    // Web neu holen
    $web = $go_api->db->queryOneRecord("select * from isp_nodes,isp_isp_web where isp_nodes.doc_id = '$doc_id' and isp_nodes.doctype_id = '1013' and isp_isp_web.doc_id = '$doc_id'");

    /////////////////////////////////////////////
    // mySQL Passwort verschlüsseln
    /////////////////////////////////////////////

    if(substr($web["optionen_mysql_passwort"],0,5) != "||||:" and $web["optionen_mysql_passwort"] != "") {
        //$go_api->db->query("UPDATE isp_isp_web SET optionen_mysql_passwort = password('$passwort') where doc_id = '$doc_id'");
        $go_api->db->query("UPDATE isp_isp_web SET optionen_mysql_passwort = concat('||||:' , password(optionen_mysql_passwort)) where doc_id = '$doc_id'");
    }

        ////////////////////////////////////////
    // mySQL User anlegen
    ////////////////////////////////////////

    $go_api->db->query("UPDATE isp_isp_web SET optionen_mysql_user = 'web".$doc_id."' where doc_id = '$doc_id'");

    /////////////////////////////////////////////////////////////
    // Check Ob Reseller HTTPD Includes benutzen darf
    /////////////////////////////////////////////////////////////

     if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
        // Check, HTTPD Includes nicht erlaubt
        if($go_info["user"]["userid"] != 1 and $reseller["limit_httpd_include"] != 1) {
            // checke, ob sich httpd Include Feld geändert hat.
            if($old_form_data["web_httpd_include"] != $web["web_httpd_include"]) {
                $go_api->db->query("UPDATE isp_isp_web SET web_httpd_include = '# HTTPD-Includes not available.\r\n#HTTPD-Includes nicht möglich.' where doc_id = $doc_id");
            }
        }

     }


    //////////////////////////////////////////////////////
    // Domain-IP Adressen updaten (angehängte Dokumente)
    //////////////////////////////////////////////////////

    $sql = "SELECT * from isp_dep where parent_doc_id = $doc_id and parent_doctype_id = $doctype_id and child_doctype_id = ".$this->domain_doctype_id;
    $domains_dep = $go_api->db->queryAllRecords($sql);
    $web_ip = $web["web_ip"];
    foreach($domains_dep as $domain_dep) {
        $go_api->db->query("update isp_isp_domain SET domain_ip = '$web_ip' where doc_id = ". $domain_dep["child_doc_id"]);
    }

    //////////////////////////////////////////////////////
    // Check ob bereits ein SSL Cert auf der IP Existiert
    //////////////////////////////////////////////////////

    $ssl_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as ssl_count from isp_isp_web where web_ip = '".$web["web_ip"]."' and web_ssl = 1");
    if($ssl_count["ssl_count"] > 1) {
        // Es existiert bereits ein SSL Web mit dieser IP
        $status = "NOTIFY";
        $errorMessage .= $go_api->lng("error_web_ssl_exist");
        $go_api->db->query("UPDATE isp_isp_web set web_ssl = 0 where doc_id = $doc_id");
    }

    //////////////////////////////////////////////////////
    // DNS Einträge für Web Erstellen
    //////////////////////////////////////////////////////

    if($web["web_dns"] == 1) {
        $this->_insert_dns($doc_id,$doctype_id,$web);
        //$go_api->db->query("UPDATE isp_isp_web SET web_dns = '' where doc_id = $doc_id");
    }

    ///////////////////////////////////////////////////////
    // Automatisch FTP aktivieren, wenn shell access aktiv
    ///////////////////////////////////////////////////////

    if($web["web_shell"] == 1 and $web["web_ftp"] != 1) {
        $this->_insert_dns($doc_id,$doctype_id,$web);
        $go_api->db->query("UPDATE isp_isp_web SET web_ftp = '1' where doc_id = $doc_id");
    }

    ///////////////////////////////////////////////////////
    // Datenbanken löschen, wenn MySQL deaktiviert
    ///////////////////////////////////////////////////////

    if(!$web["web_mysql"]){
      $datenbanken = $go_api->db->queryAllRecords("SELECT doc_id, doctype_id FROM isp_isp_datenbank WHERE web_id = '$doc_id'");
      if(!empty($datenbanken)){
        $go_api->db->query("UPDATE isp_isp_datenbank SET status = 'd' WHERE web_id = '$doc_id'");
        foreach($datenbanken as $datenbank){
          $go_api->db->query("UPDATE isp_nodes SET status = '0' where doc_id = '".$datenbank["doc_id"]."' and doctype_id = '".$datenbank["doctype_id"]."'");
        }
      }
    }

    if($web["web_mysql"] && !$old_form_data["web_mysql"]){
      $go_api->db->query("UPDATE isp_isp_datenbank, isp_nodes SET isp_isp_datenbank.status = 'u' WHERE isp_isp_datenbank.web_id = '$doc_id' AND isp_isp_datenbank.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = isp_isp_datenbank.doctype_id AND isp_nodes.status = '1'");
    }

    ////////////////////////////////////////////////////
     // Userid des Webs auf ISPConfig-Userid des Kunden setzen
     ////////////////////////////////////////////////////

     // Kunde zu Web bestimmen
     $sql = "SELECT * from isp_isp_kunde, isp_dep where isp_dep.parent_doc_id = isp_isp_kunde.doc_id
     and isp_dep.child_doc_id = $doc_id and isp_dep.child_doctype_id = $doctype_id and
     isp_dep.parent_doctype_id = ".$this->kunde_doctype_id;

     $kunde = $go_api->db->queryOneRecord($sql);
     $webadmin_userid = $kunde["webadmin_userid"];

     // UserID updaten
     $go_api->db->query("UPDATE isp_nodes SET userid = $webadmin_userid where doc_id = $doc_id and doctype_id = $doctype_id");

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');

        // Faktura Eintrag Updaten
        $this->faktura_update($doc_id,$web["web_host"].".".$web["web_domain"]);

        // ISPConfig Rechte in nodes Table checken
        $go_api->isp->check_perms($doc_id, $doctype_id);


    // Check ob lokaler Host als MX eingetragen ist
    if (!empty($web["web_host"])) {
        $host = $web["web_host"] . ".";
    }
    getmxrr($host . $web["web_domain"], $mx_list);

    $mx_found = false;
    foreach ($mx_list as $mx) {
        if (ip2long(gethostbyname($mx)) == ip2long($web["web_ip"])) {
            $mx_found = true;
        }
    }

    if (!$mx_found) {
        $go_api->db->query("UPDATE isp_isp_web SET optionen_local_mailserver = '' WHERE doc_id = '$doc_id'");
    }


    ///////////////////////////////////
    // NOTIFY Error Handler
    ///////////////////////////////////

    // Dieses muss die letzte Eintrag der Funktion sein
    if($status == "NOTIFY"){
      if($die_on_error){
        $go_api->errorMessage($errorMessage.$go_api->lng("weiter_link"));
      } else {
        return $errorMessage;
      }
    }


}

function web_delete($doc_id, $doctype_id, $action, $die_on_error = '1') {

global $go_api, $go_info;

    if($action == "do") {
        $go_api->db->query("UPDATE isp_isp_web SET status = 'd' where doc_id = '$doc_id'");

        // Web in del_status eintragen
        $web_del_status = $go_api->db->queryOneRecord("SELECT * FROM del_status WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
        if(!$web_del_status){
          $web_info = $go_api->db->queryOneRecord("select * from isp_isp_web where doc_id = '$doc_id'");
          $go_api->db->query("INSERT INTO del_status (doc_id, doctype_id, name, pfad, web_host, web_domain) VALUES ('".$doc_id."', '".$doctype_id."', 'web".$doc_id."', '".realpath($this->server_conf["server_path_httpd_root"])."/web".$doc_id."', '".$web_info["web_host"]."', '".$web_info["web_domain"]."')");
        }

        // Alle User des Web mit Status = d setzen
        $sql = "SELECT * from isp_dep, isp_isp_user where isp_dep.parent_doc_id = $doc_id
        and isp_dep.parent_doctype_id = $doctype_id and isp_dep.child_doc_id = isp_isp_user.doc_id
        and isp_dep.child_doctype_id = ".$this->user_doctype_id."";

        $users = $go_api->db->queryAllRecords($sql);
        foreach($users as $user) {
            $go_api->db->query("UPDATE isp_isp_user SET status = 'd' where doc_id = ". $user["doc_id"]);

            // User in del_status eintragen
            $user_del_status = $go_api->db->queryOneRecord("SELECT * FROM del_status WHERE doc_id = '".$doc_id."' AND doctype_id = '".$this->user_doctype_id."'");
            if(!$user_del_status) $go_api->db->query("INSERT INTO del_status (doc_id, doctype_id, name, pfad) VALUES ('".$user["doc_id"]."', '".$this->user_doctype_id."', '".$user["user_username"]."', '".realpath($this->server_conf["server_path_httpd_root"])."/web".$doc_id."/user/".$user["user_username"]."')");
        }
        unset($users);

        // Alle Domains des Web mit Status = d setzen
        $sql = "SELECT * from isp_dep, isp_isp_domain where isp_dep.parent_doc_id = $doc_id
        and isp_dep.parent_doctype_id = $doctype_id and isp_dep.child_doc_id = isp_isp_domain.doc_id
        and isp_dep.child_doctype_id = ".$this->domain_doctype_id."";

        $domains = $go_api->db->queryAllRecords($sql);
        foreach($domains as $domain) {
            $go_api->db->query("UPDATE isp_isp_domain SET status = 'd' where doc_id = ". $domain["doc_id"]);
        }

        unset($domains);

                $this->faktura_delete($doc_id,'do');

    } else {

        // wenn das Web einem Reseller gehört: ist sein Web-Limit hoch genug, damit er das Web wiederherstellen darf?
        // Check Ob maximale Anzahl Webs des Resellers erreicht ist
       $user_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $doc_id and doctype_id = $doctype_id");
       $resellerid = $user_node["groupid"];
       unset($user_node);
       if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
         $sql = "SELECT count(doc_id) as webs from isp_nodes where groupid = '$resellerid' and doctype_id = ".$this->web_doctype_id;
         $webanzahl = $go_api->db->queryOneRecord($sql);
         $webanzahl = $webanzahl["webs"];
         // Wenn Resellerlimit erreicht wurde
         if($reseller["limit_web"] >= 0) {
            if($webanzahl > $reseller["limit_web"]) {
                $go_api->db->query("UPDATE isp_nodes SET status = '0' WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
                if($die_on_error){
                  $go_api->errorMessage($go_api->lng("error_max_web_anbieter").$go_api->lng("weiter_link"));
                } else {
                  return $go_api->lng("error_max_web_anbieter");
                }
            } else {
              $go_api->db->query("UPDATE isp_isp_web SET status = 'n' where doc_id = '$doc_id'");
                $this->faktura_delete($doc_id,'undo');
            }
         } else {
          $go_api->db->query("UPDATE isp_isp_web SET status = 'n' where doc_id = '$doc_id'");
                $this->faktura_delete($doc_id,'undo');
         }
       } else {
        $go_api->db->query("UPDATE isp_isp_web SET status = 'n' where doc_id = '$doc_id'");
                $this->faktura_delete($doc_id,'undo');
       }
    }

    //$web = $go_api->db->queryOneRecord("select * from isp_isp_web where doc_id = '$doc_id'");

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');

}

##################################################################################################################

    ///////////////////////////////////////////////////
    // Externe Funktionen
    ///////////////////////////////////////////////////

function _insert_dns($doc_id,$doctype_id,$web) {
    global $go_api, $go_info;

    $dns_doctype_id = 1016;
    $a_record_doctype_id = 1018;
    $mx_record_doctype_id = 1020;

    // erzeuge DNS Records für Web
    $dns_soa = trim($web["web_domain"]);
    $server_id = $web["server_id"];
    $dns_soa_ip = $web["web_ip"];
    $dns_adminmail = 'admin@'.$web["web_domain"];

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

        $sql = "INSERT INTO dns_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('1','$groupid','$parent','$type','$dns_doctype_id','$status','$dns_doc_id','$title')";
        $go_api->db->query($sql);
        $dns_tree_id = $go_api->db->insertID();


                ////////////////////////////////////////
        // MX Record anlegen
        ////////////////////////////////////////

        if(trim($server["server_host"]) == ''){
          $mailserver = trim($server["server_domain"]);
        } else {
          $mailserver = trim($server["server_host"]).'.'.trim($server["server_domain"]);
        }
        $host = $go_api->db->queryOneRecord("SELECT web_host FROM isp_isp_web WHERE doc_id = $doc_id");
        $host = trim($host['web_host']);
                if(!empty($mailserver)) {
                //$ip_adresse = $domain["domain_ip"];
                $sql = "INSERT INTO dns_mx (host,prioritaet,mailserver) VALUES ('$host','10','$mailserver')";
                $go_api->db->query($sql);
                $mx_doc_id = $go_api->db->insertID();

                $userid = $go_info["user"]["userid"];
                $groupid = $web["groupid"];
                $type = 'a';
                $parent = '';
                $status = 1;
                $title = '';

                $sql = "INSERT INTO dns_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('1','$groupid','$parent','$type','$mx_record_doctype_id','$status','$mx_doc_id','$title')";
                $go_api->db->query($sql);
                $mx_tree_id = $go_api->db->insertID();
                $status = 1;

                $parent_doctype_id = $dns_doctype_id;
                $child_doctype_id = $mx_record_doctype_id;

                $parent_doc_id = $dns_doc_id;
                $child_doc_id = $mx_doc_id;

                $parent_tree_id = $dns_tree_id;
                $child_tree_id = $mx_tree_id;

                $sql = "INSERT INTO dns_dep (userid,parent_doc_id,parent_doctype_id,parent_tree_id,child_doc_id,child_doctype_id,child_tree_id,status) VALUES ('$userid','$parent_doc_id','$parent_doctype_id','$parent_tree_id','$child_doc_id','$child_doctype_id','$child_tree_id','$status')";
                $go_api->db->query($sql);
                }
      } else {
                  // hole SOA Record
                $soa = $go_api->db->queryOneRecord("SELECT * FROM dns_isp_dns where dns_soa = '$dns_soa'");
                $parent_doc_id = $soa["doc_id"];
          }


            // wenn host nicht = "", dann A Record für Host eintragen
                $query_sql = "SELECT * FROM dns_a, dns_dep WHERE dns_a.doc_id = dns_dep.child_doc_id and dns_dep.child_doctype_id = 1018 and dns_dep.parent_doc_id = $parent_doc_id and dns_a.host = '".trim($web["web_host"])."'";

                if($web["web_host"] != "" and !$go_api->db->queryOneRecord($query_sql)) {

        $dns_soa = trim($web["web_domain"]);
        // A Record für Host anlegen
        // DNS-Record (SOA) holen
                $dns_record = $go_api->db->queryOneRecord("SELECT * FROM dns_nodes,dns_isp_dns where dns_nodes.doc_id = dns_isp_dns.doc_id and dns_nodes.doctype_id = dns_isp_dns.doctype_id and dns_isp_dns.dns_soa = '$dns_soa'");

                        $ptid = $dns_record["tree_id"];
                        // überprüfe, on a-record schon existiert
                        $exist_a_record = $go_api->db->queryOneRecord("SELECT * from dns_dep, dns_a where dns_dep.child_doc_id = dns_a.doc_id and dns_dep.child_doctype_id = $a_record_doctype_id and dns_a.host = '".$web["host"]."' and dns_dep.parent_tree_id = '$ptid'");
                        if(!is_array($exist_a_record)) {

                        $host = trim($web["web_host"]);
                        $ip_adresse = $web["web_ip"];
                        $sql = "INSERT INTO dns_a (host,ip_adresse) VALUES ('$host','$ip_adresse')";
                        $go_api->db->query($sql);
                        $a_doc_id = $go_api->db->insertID();

                        $userid = $go_info["user"]["userid"];
                        $groupid = $web["groupid"];
                        $type = 'a';
                        $parent = '';
                        $status = 1;
                        $title = '';

                        $sql = "INSERT INTO dns_nodes (userid,groupid,parent,type,doctype_id,status,doc_id,title) VALUES ('1','$groupid','$parent','$type','$a_record_doctype_id','$status','$a_doc_id','$title')";
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
                }

                $go_api->db->query("UPDATE dns_isp_dns SET status = 'u' where status != 'n' and doc_id = '$parent_doc_id'");

}

function _WebToMsg($web = array()){

    $msg["web_ip"] = $web["web_ip"];
    $msg["host"] = $web["web_host"];
    $msg["domain"] = $web["web_domain"];
    $msg["speicher"]    = $web["web_speicher"];
    $msg["traffic"]     = $web["web_traffic"];
    $msg["userlimit"]   = $web["web_userlimit"];
    $msg["domainlimit"] = $web["web_domainlimit"];
    $msg["cgi"]         = $web["web_cgi"];
    $msg["php"]         = $web["web_php"];
    $msg["ssi"]         = $web["web_ssi"];
    $msg["ftp"]         = $web["web_ftp"];
    $msg["frontpage"]   = $web["web_frontpage"];
    $msg["mysql"]       = $web["web_mysql"];
    $msg["postgresql"]  = $web["web_postgresql"];
    $msg["shell"]       = $web["web_shell"];
    $msg["shop"]        = $web["web_shop"];
    $msg["phpmyadmin"]  = $web["web_phpmyadmin"];
    $msg["webmail"]     = $web["web_webmail"];
    $msg["webalizer"]   = $web["web_webalizer"];

    $msg["ssl"]         = $web["web_ssl"];
    $msg["ssl_request"] = $web["ssl_request"];
    $msg["ssl_cert"]    = $web["ssl_cert"];
    $msg["ssl_action"]  = $web["ssl_action"];
    $msg["ssl_country"] = $web["ssl_country"];
    $msg["ssl_state"]   = $web["ssl_state"];
    $msg["ssl_locality"]            = $web["ssl_locality"];
    $msg["ssl_organisation"]        = $web["ssl_organization"];
    $msg["ssl_organisation_unit"]   = $web["ssl_organization_unit"];
    $msg["mysql_user"]              = $web["optionen_mysql_user"];
    $msg["mysql_passwort"]          = $web["optionen_mysql_passwort"];
    $msg["dns"]                     = $web["web_dns"];

    //return $msg;
     return '';


}

function faktura_insert($web_id,$beschreibung) {
        global $go_api;
        $sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
        if($sys_config["faktura_on"] == 1) {
                // Web Record hinzufügen
                $sql = "INSERT INTO isp_fakt_record (web_id,doc_id,doctype_id,typ,notiz) VALUES ($web_id,$web_id,1013,'Web','$beschreibung')";
                $go_api->db->query($sql);
                // Traffic Record hinzufügen
                $sql = "INSERT INTO isp_fakt_record (web_id,typ,notiz) VALUES ($web_id,'Traffic','$beschreibung')";
                $go_api->db->query($sql);
        }
}

function faktura_update($web_id,$beschreibung) {
        global $go_api;
        $sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
        if($sys_config["faktura_on"] == 1) {
                // Web Record Updaten
                $sql = "UPDATE isp_fakt_record SET notiz = '$beschreibung' where doc_id = $web_id and doctype_id = 1013";
                $go_api->db->query($sql);
        }
}

function faktura_delete($web_id,$action) {
        global $go_api;
        $sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
        if($sys_config["faktura_on"] == 1) {
                if($action == "do") {
                        $sql = "UPDATE isp_fakt_record SET status = 0 where web_id = $web_id";
                        $go_api->db->query($sql);
                } else {
                        $sql = "UPDATE isp_fakt_record SET status = 1 where web_id = $web_id";
                        $go_api->db->query($sql);
                }
        }
}


}
?>
