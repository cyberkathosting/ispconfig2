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

class sendmail{

var $FILE = "/root/ispconfig/scripts/lib/classes/ispconfig_sendmail.lib.php";

function make_local_host_names() {
  global $isp_web, $mod;

  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "sendmail.cw.master"));
  $mod->tpl->define_dynamic ( "sendmail", "table" );

  $sendmail_exclude_domains[] = "";

  $webs = $mod->db->queryAllRecords("SELECT * FROM isp_nodes,isp_isp_web WHERE isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = '".$isp_web->web_doctype_id."' AND isp_nodes.status = '1'");
  if(!empty($webs)){
  foreach($webs as $web){

    if(!empty($web["web_host"])){
      $domain = $web["web_host"].".".$web["web_domain"];
    } else {
      $domain = $web["web_domain"];
    }
    if(!$web["optionen_local_mailserver"]) $sendmail_exclude_domains[] = $domain;
    // Variablen zuweisen
    if(!in_array($domain, $sendmail_exclude_domains)){
      $mod->tpl->assign( array( DOMAIN => $domain));
      $mod->tpl->parse(SENDMAIL,".sendmail");
    } else {
      $mod->tpl->assign( array( DOMAIN => ""));
      $mod->tpl->parse(SENDMAIL,".sendmail");
    }
  }
  } else {
    $mod->tpl->assign( array( DOMAIN => ""));
    $mod->tpl->parse(SENDMAIL,".sendmail");
  }

  $webs = $mod->db->queryAllRecords("SELECT * FROM isp_dep,isp_isp_domain WHERE isp_dep.child_doc_id = isp_isp_domain.doc_id AND isp_dep.child_doctype_id ='".$isp_web->domain_doctype_id."' AND isp_dep.parent_doctype_id = '".$isp_web->web_doctype_id."' AND isp_isp_domain.status != 'd'");

  foreach($webs as $web){
    if(!empty($web["domain_host"])){
      $domain = $web["domain_host"].".".$web["domain_domain"];
    } else {
      $domain = $web["domain_domain"];
    }
    if(!$web["domain_local_mailserver"]) $sendmail_exclude_domains[] = $domain;
    // Variablen zuweisen
    if(!in_array($domain, $sendmail_exclude_domains)){
      $mod->tpl->assign( array( DOMAIN => $domain));
      $mod->tpl->parse(SENDMAIL,".sendmail");
    } else {
      $mod->tpl->assign( array( DOMAIN => ""));
      $mod->tpl->parse(SENDMAIL,".sendmail");
    }
  }

  $mod->tpl->parse(TABLE, table);
  $sendmail_text = rtrim($mod->tpl->fetch());

  list($sendmail_header, $sendmail_main) = explode("##----##", $sendmail_text);
  $sendmail_array = explode("\n", $sendmail_main);
  $sendmail_array = array_unique($sendmail_array);
  $sendmail_text = $sendmail_header.(implode("\n", $sendmail_array));
  $sendmail_text .= $mod->file->manual_entries($mod->system->server_conf["server_sendmail_cw"]);

  $mod->log->caselog("cp -fr ".$mod->system->server_conf["server_sendmail_cw"]." ".$mod->system->server_conf["server_sendmail_cw"]."~", $this->FILE, __LINE__);
  $mod->file->wf($mod->system->server_conf["server_sendmail_cw"], $sendmail_text);
  $mod->file->remove_blank_lines($mod->system->server_conf["server_sendmail_cw"]);
}

function make_virtusertable() {
  global $isp_web, $mod;
  $dist = $mod->system->server_conf["dist"];
  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "virtusertable.master"));
  $mod->tpl->define_dynamic ( "virtusertable", "table" );

  $existing_webs = $mod->db->queryAllRecords("SELECT isp_isp_web.web_host, isp_isp_web.web_domain, isp_isp_web.optionen_local_mailserver FROM isp_nodes, isp_isp_web WHERE isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = isp_isp_web.doctype_id AND isp_nodes.status = 1");
  $existing_domains = $mod->db->queryAllRecords("SELECT isp_isp_domain.domain_host, isp_isp_domain.domain_domain, isp_isp_domain.domain_local_mailserver FROM isp_nodes, isp_isp_domain WHERE isp_nodes.doc_id = isp_isp_domain.doc_id AND isp_nodes.doctype_id = isp_isp_domain.doctype_id AND isp_nodes.status = 1");
  if(!empty($existing_webs)){
    foreach($existing_webs as $existing_web){
      if(!empty($existing_web["web_host"])){
        $nouser_index = $existing_web["web_host"].".".$existing_web["web_domain"];
      } else {
        $nouser_index = $existing_web["web_domain"];
      }
      if($existing_web["optionen_local_mailserver"]) $nousers[$nouser_index] = "error:nouser No such user here";
    }
  }
  if(!empty($existing_domains)){
    foreach($existing_domains as $existing_domain){
      if(!empty($existing_domain["domain_host"])){
        $nouser_index = $existing_domain["domain_host"].".".$existing_domain["domain_domain"];
      } else {
        $nouser_index = $existing_domain["domain_domain"];
      }
      if($existing_domain["domain_local_mailserver"]) $nousers[$nouser_index] = "error:nouser No such user here";
    }
  }

  $users = $mod->db->queryAllRecords("select * from isp_nodes,isp_isp_user WHERE isp_nodes.doc_id = isp_isp_user.doc_id AND isp_nodes.doctype_id = '".$isp_web->user_doctype_id."' AND isp_nodes.status = '1'");

  //Emailalias und Benutzer herausfinden
  if(!empty($users)){
  foreach($users as $user){
    $doc_id = $user["doc_id"];
    $sql = "SELECT * FROM isp_dep WHERE child_doc_id = '$doc_id' AND child_doctype_id = '".$isp_web->user_doctype_id."'";
    $web_dep = $mod->db->queryOneRecord($sql);
    $web_doc_id = $web_dep["parent_doc_id"];
    $web = $mod->system->data["isp_isp_web"][$web_doc_id];

    if($user["user_catchallemail"]){
      $catchall = "\n####----||||----####";
    } else {
      $catchall = "";
    }

    $user_emailalias = $mod->file->unix_nl($user["user_emailalias"])."\n".$user["user_email"]."\n".$user["user_username"].$catchall;

    $user_username = $user["user_username"];

    if(!empty($user_emailalias)) {
      $emails = explode("\n", $user_emailalias);
      $count = sizeof($emails);
      for($i=0; $i<$count; $i++){
        // Variablen zuweisen
        if(!empty($emails[$i]) && $web["optionen_local_mailserver"]){

          if(!empty($web["web_host"])){
            $mod->tpl->assign( array(    EMAILALIAS => str_replace("####----||||----####", "", $emails[$i])."@".$web["web_host"].".".$web["web_domain"],
                                  USER => $user_username));
            $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
            if($user["user_catchallemail"] && isset($nousers[$web["web_host"].".".$web["web_domain"]])) unset($nousers[$web["web_host"].".".$web["web_domain"]]);
          } else {
            $mod->tpl->assign( array(  EMAILALIAS => str_replace("####----||||----####", "", $emails[$i])."@".$web["web_domain"],
                                  USER => $user_username));
            $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
            if($user["user_catchallemail"] && isset($nousers[$web["web_domain"]])) unset($nousers[$web["web_domain"]]);
          }
        } else {
          $mod->tpl->assign( array(    EMAILALIAS => "",
                                  USER => ""));
          $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
        }
      }
      $codomains = $mod->db->queryAllRecords("SELECT * from isp_dep,isp_isp_domain where isp_dep.child_doc_id = isp_isp_domain.doc_id and isp_dep.child_doctype_id ='".$isp_web->domain_doctype_id."' and isp_dep.parent_doctype_id = '".$isp_web->web_doctype_id."' and isp_dep.parent_doc_id = '".$web_doc_id."' and isp_isp_domain.status != 'd'");
      if(!empty($codomains)){
        foreach($codomains as $codomain){
          foreach($emails as $email){
            if(!empty($email) && $codomain["domain_local_mailserver"]){

              if(!empty($codomain["domain_host"])){
                $mod->tpl->assign( array(  EMAILALIAS => str_replace("####----||||----####", "", $email)."@".$codomain["domain_host"].".".$codomain["domain_domain"],
                                      USER => $user_username));
                $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
                if($user["user_catchallemail"] && isset($nousers[$codomain["domain_host"].".".$codomain["domain_domain"]])) unset($nousers[$codomain["domain_host"].".".$codomain["domain_domain"]]);
              } else {
                $mod->tpl->assign( array(  EMAILALIAS => str_replace("####----||||----####", "", $email)."@".$codomain["domain_domain"],
                                      USER => $user_username));
                $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
                if($user["user_catchallemail"] && isset($nousers[$codomain["domain_domain"]])) unset($nousers[$codomain["domain_domain"]]);
              }
            } else {
              $mod->tpl->assign( array(    EMAILALIAS => "",
                                      USER => ""));
              $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
            }
          }
        }
      }
    } else {
      $mod->tpl->assign( array(    EMAILALIAS => "",
                              USER => ""));
      $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
    }
  }
  } else {
     $mod->tpl->assign( array(    EMAILALIAS => "",
                              USER => ""));
     $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
  }

  if(!empty($nousers)){
    while (list($key, $val) = each($nousers)) {
      $mod->tpl->assign( array(EMAILALIAS => "@".$key,
                          USER => $val));
      $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
    }
  }

  $mod->tpl->parse(TABLE, table);
  if(!empty($users) || !empty($nousers)){
    $virtusertable_text = rtrim($mod->tpl->fetch());

    list($virtusertable_header, $virtusertable_main) = explode("##----##", $virtusertable_text);
    $virtusertable_array = explode("\n", $virtusertable_main);
    $virtusertable_array = array_unique($virtusertable_array);
    $virtusertable_text = $virtusertable_header.(implode("\n", $virtusertable_array));

  } else {
    $virtusertable_text = "";
  }
  $virtusertable_text = str_replace("    \n", "", $virtusertable_text); //Leerzeichen in leerer virtusertable-Datei entfernen
  $virtusertable_text .= $mod->file->manual_entries($mod->system->server_conf["server_sendmail_virtuser_datei"]);

  //Backup erstellen
  $mod->log->caselog("cp -fr ".$mod->system->server_conf["server_sendmail_virtuser_datei"]." ".$mod->system->server_conf["server_sendmail_virtuser_datei"]."~", $this->FILE, __LINE__);
  $mod->file->wf($mod->system->server_conf["server_sendmail_virtuser_datei"], $virtusertable_text);


  //virtusertable.db anlegen
  if(stristr($dist, "suse") || stristr($dist, "debian")){
    $mod->log->caselog("makemap hash ".$mod->system->server_conf["server_sendmail_virtuser_datei"].".db < ".$mod->system->server_conf["server_sendmail_virtuser_datei"], $this->FILE, __LINE__);
  }
  //Leerzeilen löschen
  $mod->file->remove_blank_lines($mod->system->server_conf["server_sendmail_virtuser_datei"]);
}

function smtp_restart(){
  global $mod, $go_info;
  $dist_init_scripts = $mod->system->server_conf["dist_init_scripts"];
  if($go_info["server"]["smtp_restart"] == 1){
    $mod->system->daemon_init($mod->system->server_conf["server_mta"], "stop");
    $mod->system->daemon_init($mod->system->server_conf["server_mta"], "start");
  } else {
    $mod->system->daemon_init($mod->system->server_conf["server_mta"], "restart");
  }
}

}
?>