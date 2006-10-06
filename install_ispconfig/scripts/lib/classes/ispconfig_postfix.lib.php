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

class postfix{

var $FILE = "/root/ispconfig/scripts/lib/classes/ispconfig_postfix.lib.php";

function make_local_host_names() {
  global $isp_web, $mod, $go_info;
	
	// This array $hostnames will contain the lines of the local-host-names file
	$hostnames = array();
	
	// This array includes all domains that are not on the local mailserver
	$sendmail_exclude_domains = array();
	
  if($go_info["server"]["postfix_config"] == 1){  // SENDMAIL-STYLE
    $sendmail_exclude_domains[] = "";

    /////////// write the HOSTNAMES IN local-host-names ////////////////
		$hostnames[] = "localhost";
    $hostname = $mod->system->hostname();
    if($hostname != ""){
      $hostnames[] = $hostname;
      $hostnames[] = "localhost.".$hostname;
      if(strpos($hostname, '.') !== false){
        $hostname_parts = explode('.', $hostname);
        $hostname_parts = array_slice($hostname_parts, 1);
        $hostnames[] = 'localhost.'.implode('.', $hostname_parts);
      }
      if(!in_array('localhost.localdomain', $hostnames)) $hostnames[] = 'localhost.localdomain';
      $hostname = NULL;
    }
    /////////// write the HOSTNAMES IN local-host-names END ////////////////
	
		// Get all website domains
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
        if(!in_array($domain, $sendmail_exclude_domains) && $domain != ''){
          $hostnames[] = $domain;
        }
      }
    }
	
		// Get all co-domains
    $webs = $mod->db->queryAllRecords("SELECT * FROM isp_dep,isp_isp_domain WHERE isp_dep.child_doc_id = isp_isp_domain.doc_id AND isp_dep.child_doctype_id ='".$isp_web->domain_doctype_id."' AND isp_dep.parent_doctype_id = '".$isp_web->web_doctype_id."' AND isp_isp_domain.status != 'd'");

    foreach($webs as $web){
      if(!empty($web["domain_host"])){
        $domain = $web["domain_host"].".".$web["domain_domain"];
      } else {
        $domain = $web["domain_domain"];
      }
      if(!$web["domain_local_mailserver"]) $sendmail_exclude_domains[] = $domain;
      // Variablen zuweisen
      if(!in_array($domain, $sendmail_exclude_domains) && $domain != ''){
          $hostnames[] = $domain;
        }
    }
  } else { // POSTFIX-STYLE
    /////////// HOSTNAMES IN local-host-names SCHREIBEN ////////////////
		$hostnames[] = "localhost";
    $hostname = $mod->system->hostname();
    if($hostname != ""){
      $hostnames[] = $hostname;
      $hostnames[] = "localhost.".$hostname;
      if(strpos($hostname, '.') !== false){
        $hostname_parts = explode('.', $hostname);
        $hostname_parts = array_slice($hostname_parts, 1);
        $hostnames[] = 'localhost.'.implode('.', $hostname_parts);
      }
      $hostname = NULL;
    }
    /////////// HOSTNAMES IN local-host-names SCHREIBEN ENDE ////////////////
  }
	
	$hostnames = array_unique($hostnames);
	
	$sendmail_header = '###################################
#
# ISPConfig local-host-names Configuration File
#         Version 1.1
#
###################################';

  $sendmail_text = $sendmail_header.(implode("\n", $hostnames));
	$sendmail_text .= "\n#### MAKE MANUAL ENTRIES BELOW THIS LINE! ####";
  $sendmail_text .= $mod->file->manual_entries($mod->system->server_conf["server_sendmail_cw"]);

  $mod->log->caselog("cp -fr ".$mod->system->server_conf["server_sendmail_cw"]." ".$mod->system->server_conf["server_sendmail_cw"]."~", $this->FILE, __LINE__);
  $mod->file->wf($mod->system->server_conf["server_sendmail_cw"], $sendmail_text);
  // $mod->file->remove_blank_lines($mod->system->server_conf["server_sendmail_cw"]);
}


function make_virtusertable() {
  global $isp_web, $mod, $go_info;

  if($go_info["server"]["postfix_config"] == 1){  // SENDMAIL-STYLE
    
		// This array contains all email addresses in the form 
		// $virtusertable_email[DOMAIN][LOCAL_PART] = USERNAME
		$virtusertable_email = array();
		
		// This array contains all email addresses in the form 
		// $virtusertable_email[DOMAIN][LOCAL_PART] = USERNAME
		$virtusertable_catchall = array();
		
		// get all users from the database
    $users = $mod->db->queryAllRecords("select * from isp_nodes,isp_isp_user WHERE isp_nodes.doc_id = isp_isp_user.doc_id AND isp_nodes.doctype_id = '".$isp_web->user_doctype_id."' AND isp_nodes.status = '1'");
		
		// get all email domains from local-host-names file
		$tmp_local_host_names = file_get_contents($mod->system->server_conf["server_sendmail_cw"]);
		$local_host_names = explode("\n",$tmp_local_host_names);
		unset($tmp_local_host_names);
		
		// go trough each user record
    if(!empty($users)){
    foreach($users as $user){
			if($user["user_emaildomain"] != '') {
				$tmp_domains = explode("\n",$user["user_emaildomain"]);
				foreach($tmp_domains as $tmp_domain) {
					$tmp_domain = trim($tmp_domain);
					$tmp_email = $user["user_email"];
					// Check if domain is not empty and if the domain is on the local mailserver
					if($tmp_domain != '' && in_array($tmp_domain,$local_host_names)) {
						if($user["user_catchallemail"]) {
							// add catchall
							$virtusertable_catchall[$tmp_domain] = $user["user_username"];
						} else {
							// add primary user address
							$virtusertable_email[$tmp_domain][$tmp_email] = $user["user_username"];
							// add aliases
							$tmp_aliases = explode("\n", $user["user_emailalias"]);
							foreach($tmp_aliases as $tmp_alias) {
								$virtusertable_email[$tmp_domain][$tmp_alias] = $user["user_username"];
							}
							unset($tmp_aliases);
							unset($tmp_alias);
							unset($tmp_email);
						} // end if
					} // end foreach
					unset($tmp_domains);
					unset($tmp_domain);
				} // end if
			}// end foreach
			unset($users);
			unset($user);
			unset($local_host_names);
			
			$virtusertable_text = '###################################
#
# ISPConfig virtusertable Configuration File
#         Version 1.1
#
###################################';
		
		
		if(is_array($virtusertable_email)) {
			foreach($virtusertable_email as $domain => $emails) {
				$emails = array_unique($emails);
				foreach($emails as $email => $username) {
					$virtusertable_text .= $email."@".$domain."    ".$username."\n";
				} // end foreach
				if($virtusertable_catchall[$domain]) {
					$virtusertable_text .= "@".$domain."    ".$username."\n";
				} // end if
			} // end foreach
		} // end if
			
    $virtusertable_text .= $mod->file->manual_entries($mod->system->server_conf["server_sendmail_virtuser_datei"]);

    //Backup erstellen
    $mod->log->caselog("cp -fr ".$mod->system->server_conf["server_sendmail_virtuser_datei"]." ".$mod->system->server_conf["server_sendmail_virtuser_datei"]."~", $this->FILE, __LINE__);
    $mod->file->wf($mod->system->server_conf["server_sendmail_virtuser_datei"], $virtusertable_text);


    //virtusertable.db anlegen
    $mod->log->caselog("postmap hash:".$mod->system->server_conf["server_sendmail_virtuser_datei"], $this->FILE, __LINE__);
    
		//Leerzeilen löschen
    //$mod->file->remove_blank_lines($mod->system->server_conf["server_sendmail_virtuser_datei"]);
		
  } else { // POSTFIX-STYLE
    // Template Öffnen
    $mod->tpl->clear_all();
    $mod->tpl->no_strict();
    $mod->tpl->define( array(table    => "virtusertable.master"));
    $mod->tpl->define_dynamic ( "virtusertable", "table" );

    $webs = $mod->db->queryAllRecords("SELECT isp_isp_web.web_host, isp_isp_web.web_domain, isp_isp_web.doc_id, isp_isp_web.doctype_id FROM isp_nodes, isp_isp_web WHERE isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = isp_isp_web.doctype_id AND isp_nodes.status = 1 AND isp_isp_web.optionen_local_mailserver = 1");
    if(is_array($webs)){
      foreach($webs as $web){
        $users = $mod->db->queryAllRecords("SELECT * FROM isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = ".$web["doc_id"]." AND isp_dep.parent_doctype_id = ".$web["doctype_id"]." AND isp_dep.child_doc_id = isp_isp_user.doc_id AND isp_dep.child_doctype_id = ".$isp_web->user_doctype_id."");
        if(is_array($users)){
          if(trim($web["web_host"]) != ""){
            $virtualdomain = $web["web_host"].".".$web["web_domain"];
          } else {
            $virtualdomain = $web["web_domain"];
          }
          $mod->tpl->assign( array(EMAILALIAS => $virtualdomain,
                                   USER => 'VIRTUALDOMAIN'));
          $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
          foreach($users as $user){
            $user_emailalias = $mod->file->unix_nl($user["user_emailalias"])."\n".$user["user_email"]."\n".$user["user_username"];
            $emails = explode("\n", $user_emailalias);
            foreach($emails as $email){
              if(trim($email) != ""){
                $mod->tpl->assign( array(EMAILALIAS => $email."@".$virtualdomain,
                                         USER => $user["user_username"]));
                $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
              }
              if($user["user_catchallemail"]){
                $catchall[] = $user["user_username"];
              }
            }
          }
          if(is_array($catchall)){
            foreach($catchall as $catch){
              if(trim($catch) != ""){
                $mod->tpl->assign( array(EMAILALIAS => "@".$virtualdomain,
                                         USER => $catch));
                $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
              }
            }
            unset($catchall);
          }
          $codomains = $mod->db->queryAllRecords("SELECT * FROM isp_dep,isp_isp_domain WHERE isp_dep.child_doc_id = isp_isp_domain.doc_id AND isp_dep.child_doctype_id ='".$isp_web->domain_doctype_id."' AND isp_dep.parent_doctype_id = '".$isp_web->web_doctype_id."' AND isp_dep.parent_doc_id = '".$web["doc_id"]."' AND isp_isp_domain.status != 'd' AND isp_isp_domain.domain_local_mailserver = 1");
          if(is_array($codomains)){
            foreach($codomains as $codomain){
              if(trim($codomain["domain_host"]) != ""){
                $virtualdomain = $codomain["domain_host"].".".$codomain["domain_domain"];
              } else {
                $virtualdomain = $codomain["domain_domain"];
              }
              $mod->tpl->assign( array(EMAILALIAS => $virtualdomain,
                                       USER => 'VIRTUALDOMAIN'));
              $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
              foreach($users as $user){
                $user_emailalias = $mod->file->unix_nl($user["user_emailalias"])."\n".$user["user_email"]."\n".$user["user_username"];
                $emails = explode("\n", $user_emailalias);
                foreach($emails as $email){
                  if(trim($email) != ""){
                    $mod->tpl->assign( array(EMAILALIAS => $email."@".$virtualdomain,
                                             USER => $user["user_username"]));
                    $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
                  }
                  if($user["user_catchallemail"]){
                    $catchall[] = $user["user_username"];
                  }
                }
              }
              if(is_array($catchall)){
                foreach($catchall as $catch){
                  if(trim($catch) != ""){
                    $mod->tpl->assign( array(EMAILALIAS => "@".$virtualdomain,
                                             USER => $catch));
                    $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
                  }
                }
                unset($catchall);
              }
            }
          }
        }
      }
    }
    if(!is_array($webs)){
      $mod->tpl->assign( array(EMAILALIAS => "",
                               USER => ""));
      $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
    }
    if(!is_array($users)){
      $mod->tpl->assign( array(EMAILALIAS => "",
                               USER => ""));
      $mod->tpl->parse(VIRTUSERTABLE,".virtusertable");
    }
    $mod->tpl->parse(TABLE, table);
    $virtusertable_text = $mod->tpl->fetch();
    list($virtusertable_header, $virtusertable_main) = explode("##----##", $virtusertable_text);
    $virtusertable_array = explode("\n", $virtusertable_main);
    $virtusertable_array = array_unique($virtusertable_array);
    $virtusertable_text = $virtusertable_header.(implode("\n", $virtusertable_array));
    $virtusertable_text = str_replace("    \n", "", $virtusertable_text); //Leerzeichen in leerer virtusertable-Datei entfernen
    $virtusertable_text .= $mod->file->manual_entries($mod->system->server_conf["server_sendmail_virtuser_datei"]);

    //Backup erstellen
    $mod->log->caselog("cp -fr ".$mod->system->server_conf["server_sendmail_virtuser_datei"]." ".$mod->system->server_conf["server_sendmail_virtuser_datei"]."~", $this->FILE, __LINE__);
    $mod->file->wf($mod->system->server_conf["server_sendmail_virtuser_datei"], $virtusertable_text);


    //virtusertable.db anlegen
    $mod->log->caselog("postmap hash:".$mod->system->server_conf["server_sendmail_virtuser_datei"], $this->FILE, __LINE__);
    //Leerzeilen löschen
    $mod->file->remove_blank_lines($mod->system->server_conf["server_sendmail_virtuser_datei"]);
  }
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