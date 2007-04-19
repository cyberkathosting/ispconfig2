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

class postfix {

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
	
	$hostnames = array_merge($hostnames,$this->get_local_hostnames());
	
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
###################################
';

  $sendmail_text = $sendmail_header.(implode("\n", $hostnames));
  $sendmail_text .= "\n#### MAKE MANUAL ENTRIES BELOW THIS LINE! ####";
  $sendmail_text .= $mod->file->manual_entries($mod->system->server_conf["server_sendmail_cw"]);

  $mod->log->caselog("cp -fr ".$mod->system->server_conf["server_sendmail_cw"]." ".$mod->system->server_conf["server_sendmail_cw"]."~", $this->FILE, __LINE__);
  $mod->file->wf($mod->system->server_conf["server_sendmail_cw"], $sendmail_text);
  // $mod->file->remove_blank_lines($mod->system->server_conf["server_sendmail_cw"]);
}


function make_virtusertable() {
  global $isp_web, $mod, $go_info, $isp_list;
  
  	// This array contains all email addresses in the form 
	// $virtusertable_email[DOMAIN][LOCAL_PART] = USERNAME
	$virtusertable_email = array();
		
	// This array contains all email addresses in the form 
	// $virtusertable_catchall[DOMAIN][LOCAL_PART] = USERNAME
	$virtusertable_catchall = array();
		
	// get all users from the database
    $users = $mod->db->queryAllRecords("select * from isp_nodes,isp_isp_user WHERE isp_nodes.doc_id = isp_isp_user.doc_id AND isp_nodes.doctype_id = '".$isp_web->user_doctype_id."' AND isp_nodes.status = '1'");
		
	// get all local email domains
	$local_host_names = $this->get_local_hostnames();
	
	// go trough each user record to create the $virtusertable_email
	// and $virtusertable_catchall arrays. See comments above for the
	// structure of these arrays.
    if(!empty($users)){
    	foreach($users as $user){
			if($user["user_emaildomain"] != '') {
				$tmp_domains = explode("\n",$user["user_emaildomain"]);
				foreach($tmp_domains as $tmp_domain) {
					$tmp_domain = trim($tmp_domain);
					$tmp_email = $user["user_email"];
					// Check if domain is not empty and if the domain is on the local mailserver
					if($tmp_domain != '' && @in_array($tmp_domain,$local_host_names)) {
						if($user["user_catchallemail"]) {
							// add catchall
							$virtusertable_catchall[$tmp_domain] = $user["user_username"];
						} // end if
						// add primary user address
						$virtusertable_email[$tmp_domain][$tmp_email] = $user["user_username"];
						// add aliases
						$tmp_aliases = explode("\n", $user["user_emailalias"]);
						foreach($tmp_aliases as $tmp_alias) {
							$tmp_alias = trim($tmp_alias);
							if($tmp_alias != '') $virtusertable_email[$tmp_domain][$tmp_alias] = $user["user_username"];
						}
						unset($tmp_aliases);
						unset($tmp_alias);
						unset($tmp_email); 
					} // end if
				} // end foreach
				unset($tmp_domains);
				unset($tmp_domain);
		}// end if
		} // end foreach
	unset($users);
	unset($user);
	unset($local_host_names);
	}
	
	// Handle mailing lists
	$lists = $mod->db->queryAllRecords("select * from isp_nodes,isp_isp_list WHERE isp_nodes.doc_id = isp_isp_list.doc_id AND isp_nodes.doctype_id = '".$isp_web->list_doctype_id."' AND isp_nodes.status = '1'");
	if(is_array($lists)) {
		foreach($lists as $list) {
			// Get the web of this list
			$doc_id = $list["doc_id"];
			$sql = "SELECT * FROM isp_dep WHERE child_doc_id = '$doc_id' AND child_doctype_id = '".$isp_web->list_doctype_id."'";
			$web_dep = $mod->db->queryOneRecord($sql);
			$web_doc_id = $web_dep["parent_doc_id"];
			$web = $mod->system->data["isp_isp_web"][$web_doc_id];
			
			$alias_list = "";
			$list_alias = $list["list_alias"];
			$list_name = $list["list_name"];
			
			// Adding the list alias for the main domain
			if(!empty($list_name) && $web["optionen_local_mailserver"]){
				if(!empty($web["web_host"])){
					$tmp_domain = $web["web_host"].".".$web["web_domain"];
					$virtusertable_email[$tmp_domain][$list_alias] = $list_name."@".$go_info["server"]["mailman"]["default_mailman_domain"];
					$alias_list .= "\\n".$list_alias."@".$tmp_domain;
				} else {
					$tmp_domain = $web["web_domain"];
					$virtusertable_email[$tmp_domain][$list_alias] = $list_name."@".$go_info["server"]["mailman"]["default_mailman_domain"];
					$alias_list .= "\\n".$list_alias."@".$tmp_domain;
				} // end if
			} // end if
			// Adding the list alias for the co-domains
			$codomains = $mod->db->queryAllRecords("SELECT * from isp_dep,isp_isp_domain where isp_dep.child_doc_id = isp_isp_domain.doc_id and isp_dep.child_doctype_id ='".$isp_web->domain_doctype_id."' and isp_dep.parent_doctype_id = '".$isp_web->web_doctype_id."' and isp_dep.parent_doc_id = '".$web_doc_id."' and isp_isp_domain.status != 'd'");
			if(!empty($codomains)){
				foreach($codomains as $codomain){
					if(!empty($list_name) && $codomain["domain_local_mailserver"]){
						if(!empty($codomain["domain_host"])){
							$tmp_domain = $codomain["domain_host"].".".$codomain["domain_domain"];
							$virtusertable_email[$tmp_domain][$list_alias] = $list_name."@".$go_info["server"]["mailman"]["default_mailman_domain"];
							$alias_list .= "\\n".$list_alias."@".$tmp_domain;
						} else {
							$tmp_domain = $codomain["domain_domain"];
							$virtusertable_email[$tmp_domain][$list_alias] = $list_name."@".$go_info["server"]["mailman"]["default_mailman_domain"];
							 $alias_list .= "\\n".$list_alias."@".$tmp_domain;
						} // end if
					} // end if
				} // end foreach
			} // end if
			
			$filename = "/tmp/Mailman-".escapeshellcmd($list["list_name"]).".aliases";
			if ($handle = fopen($filename, 'w')) {
				fwrite($handle, "acceptable_aliases = \"$alias_list\"" );
				fclose($handle);
				$mod->log->caselog($go_info["server"]["mailman"]["config_list_path"]." -i "."/tmp/Mailman-".escapeshellcmd($list["list_name"]).".aliases ".escapeshellcmd($list["list_name"]), $this->FILE, __LINE__);
				unset($filename);
			}
			
		} // end foreach
	} // end if
	unset($lists);
	
	// We support two types of configuration, postfix and sendmail style.
	// First we create the file in sendmail style
	if($go_info["server"]["postfix_config"] == 1){  // SENDMAIL-STYLE
    	
		// Writing the virtusertable configuration file in sendmail style
		$virtusertable_text = '###################################
#
# ISPConfig virtusertable Configuration File
#         Version 1.2
#
###################################
';
		
		
		if(is_array($virtusertable_email)) {
			foreach($virtusertable_email as $domain => $emails) {
				foreach($emails as $email => $username) {
					$virtusertable_text .= $email."@".$domain."    ".$username."\n";
				} // end foreach
				if($virtusertable_catchall[$domain]) {
					$virtusertable_text .= "@".$domain."    ".$virtusertable_catchall[$domain]."\n";
				} // end if
			} // end foreach
		} // end if
		
		unset($virtusertable_email);
		unset($virtusertable_catchall);
		
		$virtusertable_text .= "#### MAKE MANUAL ENTRIES BELOW THIS LINE! ####";
			
    	$virtusertable_text .= $mod->file->manual_entries($mod->system->server_conf["server_sendmail_virtuser_datei"]);

    	// Making a backup
    	$mod->log->caselog("cp -fr ".$mod->system->server_conf["server_sendmail_virtuser_datei"]." ".$mod->system->server_conf["server_sendmail_virtuser_datei"]."~", $this->FILE, __LINE__);
    	$mod->file->wf($mod->system->server_conf["server_sendmail_virtuser_datei"], $virtusertable_text);


    	//Creating the virtusertable.db file
    	$mod->log->caselog("postmap hash:".$mod->system->server_conf["server_sendmail_virtuser_datei"], $this->FILE, __LINE__);
    
		// removing blank lines
    	//$mod->file->remove_blank_lines($mod->system->server_conf["server_sendmail_virtuser_datei"]);
		
	} else { // POSTFIX-STYLE
	
		// Writing the virtusertable configuration file in postfix style
		$virtusertable_text = '###################################
#
# ISPConfig virtusertable Configuration File
#         Version 1.2
#
###################################
';
		
		
		if(is_array($virtusertable_email)) {
			foreach($virtusertable_email as $domain => $emails) {
				$virtusertable_text .= $domain."    VIRTUALDOMAIN\n";
				foreach($emails as $email => $username) {
					$virtusertable_text .= $email."@".$domain."    ".$username."\n";
				} // end foreach
				if($virtusertable_catchall[$domain]) {
					$virtusertable_text .= "@".$domain."    ".$virtusertable_catchall[$domain]."\n";
				} // end if
			} // end foreach
		} // end if
		
		unset($virtusertable_email);
		unset($virtusertable_catchall);
		
		$virtusertable_text .= "#### MAKE MANUAL ENTRIES BELOW THIS LINE! ####";
			
    	$virtusertable_text .= $mod->file->manual_entries($mod->system->server_conf["server_sendmail_virtuser_datei"]);

    	// Making a backup
    	$mod->log->caselog("cp -fr ".$mod->system->server_conf["server_sendmail_virtuser_datei"]." ".$mod->system->server_conf["server_sendmail_virtuser_datei"]."~", $this->FILE, __LINE__);
    	$mod->file->wf($mod->system->server_conf["server_sendmail_virtuser_datei"], $virtusertable_text);
		
		unset($virtusertable_text);

    	//Creating the virtusertable.db file
    	$mod->log->caselog("postmap hash:".$mod->system->server_conf["server_sendmail_virtuser_datei"], $this->FILE, __LINE__);
    
		// removing blank lines
    	//$mod->file->remove_blank_lines($mod->system->server_conf["server_sendmail_virtuser_datei"]);
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

// This function returns a array with all local domains

function get_local_hostnames() {
	global $mod, $go_info, $isp_web;
	
	// Get all website domains
	$sendmail_exclude_domains = array();
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
	unset($webs);
	
	// Get all co-domains
    $codomains = $mod->db->queryAllRecords("SELECT * FROM isp_dep,isp_isp_domain WHERE isp_dep.child_doc_id = isp_isp_domain.doc_id AND isp_dep.child_doctype_id ='".$isp_web->domain_doctype_id."' AND isp_dep.parent_doctype_id = '".$isp_web->web_doctype_id."' AND isp_isp_domain.status != 'd'");

    foreach($codomains as $web){
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
	unset($codomains);
	$local_host_names = @array_unique($hostnames);
	unset($hostnames);
	
	return $local_host_names;
}

 function make_mailman_transport() {
                global $isp_web, $mod, $go_info;

                if ($go_info["server"]["mailman"]["default_mailman_domain"] != "") {

                        $sendmail_header = '###################################
#
# ISPConfig transport Map Configuration File
#         Version 1.1
#
###################################
';

                        $sendmail_text = $sendmail_header.($go_info["server"]["mailman"]["default_mailman_domain"]." mailman:");
                        $sendmail_text .= "\n#### MAKE MANUAL ENTRIES BELOW THIS LINE! ####";
                        $sendmail_text .= $mod->file->manual_entries("/etc/postfix/transport");

                        $mod->log->caselog("cp -fr /etc/postfix/transport /etc/postfix/transport~", $this->FILE, __LINE__);
                        $mod->file->wf("/etc/postfix/transport", $sendmail_text);

                        //Creating the transport.db file
                        $mod->log->caselog("postmap hash:/etc/postfix/transport", $this->FILE, __LINE__);
                }
        }

}
?>