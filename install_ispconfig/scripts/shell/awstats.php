<?
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

/**************************
*
* ISPConfig AWStats Script
* Version 1.8
* Author Jonas Schwarz
* http://www.jnsc.de
*
***************************/
set_time_limit(0);

include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");
$server_id = $mod->system->server_id;
$server = $mod->system->server_conf;
$path_httpd_root = stripslashes($server["server_path_httpd_root"]);

exec("which perl", $perl_location, $verify);

unset($perl_location);

if($verify == 0){

	$web_home = $path_httpd_root;

	$message = "";

	$handle = @opendir($web_home);
	while ($dir = @readdir ($handle)) {
		if ($dir != "." && $dir != "..") {
			if(@!is_link("$web_home/$dir") && substr($dir,0,3) == "web" && is_numeric(substr($dir,3))) {
				// ist kein symbolischer Link

				$webname = $dir;
				$web_doc_id = str_replace("web", "", $webname);
				if($web_data = $mod->db->queryOneRecord("SELECT * FROM isp_isp_web WHERE doc_id = '$web_doc_id' AND web_stats = 'awstats'")){
					$web_path = $web_home . "/$webname/web";
					$stats_path = $web_path . "/awstats";
					$logfile = $web_home . "/$webname/log/web.log";
					$web_user = fileowner($web_path);
					$web_group = filegroup($web_path);

					// erstelle awstats Verzeichnis, wenn nicht vorhanden
					if(!@is_dir($stats_path)) {
						mkdir($stats_path,0775);
						chown($stats_path,$web_user);
						chgrp($stats_path,$web_group);
						$message .= "Erstelle Statistik Verzeichnis: $stats_path\n";
					}


					// Experimentell: erstelle .htaccess Dateien mit Zugangsberechtigung fï¿½r Gruppe des Webs
					if(!@is_dir($stats_path."/.htaccess") AND !file_exists($stats_path."/.htaccess")) {

						$ht_file = "AuthType Basic
AuthName \"Members Only\"
AuthUserFile $web_home/$webname/.htpasswd
<limit GET PUT POST>
require valid-user
</limit>";
						$fp = fopen ($stats_path."/.htaccess", "w");
						fwrite($fp,$ht_file);
						fclose($fp);
						chmod($stats_path."/.htaccess",0664);
					}

					if(!@is_dir($web_home."/".$webname."/.htpasswd")) {

						exec("cat ".$mod->system->server_conf["passwd_datei"]." | grep ".$web_home."/".$webname."/ |cut -f1 -d:", $users);
						exec("cat ".$mod->system->server_conf["passwd_datei"]." | grep ".$web_home."/".$webname.": |cut -f1 -d:", $users);
						$ht_file = "";
						if(!empty($users)){
							foreach($users as $user){
								$user_password = exec("cat ".$mod->system->server_conf["shadow_datei"]." | grep '$user:' | grep -w $user |cut -f2 -d:");
								$ht_file .= "$user:$user_password\n";
							}
						} else {
							$ht_file .= "admin:\$1\$TAVCXZlv\$NAjnpdNgAfPMNT4/A61Z.0\n";
						}

						unset($users);
						$fp = fopen ($web_home."/".$webname."/.htpasswd", "w");
						fwrite($fp,$ht_file);
						fclose($fp);
						chmod($web_home."/".$webname."/.htpasswd",0664);
						exec("chown :".$webname." ".$web_home."/".$webname."/.htpasswd");
					}

					if(!empty($web_data["web_host"])){
						$web_real_name = $web_data["web_host"].".".$web_data["web_domain"];
					} else {
						$web_real_name = $web_data["web_domain"];
					}

					// Starte AWStats
					if(@is_file($logfile)) {

						// Experimentell: erstelle /etc/awstats/meindomain.config Datei
						if(!@is_dir("/etc/awstats/awstats.".$web_real_name.".conf") AND !file_exists("/etc/awstats/awstats.".$web_real_name.".conf")) {
							$lang =  $mod->db->queryOneRecord("SELECT language FROM isp_nodes, sys_user WHERE isp_nodes.doctype_id = 1013 and isp_nodes.doc_id = $web_doc_id and sys_user.doc_id = isp_nodes.userid");

							$suported_lang = array("al", "ba", "bg", "tw", "cn", "cz", "dk", "nl", "en", "et", "fi", "fr", "de", "gr", "he", "hu", "id", "it", "jp", "kr", "lv", "nn", "nb", "pl", "pt", "br", "ro", "ru", "sr", "sk", "es", "es_cat", "se", "tr", "ua", "wlk"); // For more info http://awstats.sourceforge.net/docs/awstats_config.html#Lang

							if($lang['language']) {
								$lang = $lang['language'];
							} else {
								$lang = $go_info["server"]["lang"];
							}

							if (!in_array ("$lang", $suported_lang)) {
								$lang = "en";
							}

							$web_alias = $mod->db->queryAllRecords("SELECT domain_host, domain_domain
FROM isp_isp_web, isp_dep, isp_isp_domain
WHERE isp_isp_web.doctype_id = isp_dep.parent_doctype_id
AND isp_isp_web.doc_id = isp_dep.parent_doc_id
AND isp_isp_domain.doctype_id = isp_dep.child_doctype_id
AND isp_isp_domain.doc_id = isp_dep.child_doc_id
AND isp_isp_web.doc_id = '$web_doc_id'");

							$host_alias = "";
							$space = "";

							foreach ($web_alias as $alias){
								if (!empty($alias['domain_host'])) {
									$host_alias .= "$space".$alias['domain_host'].".".$alias['domain_domain'];
               	} else {
									$host_alias .= "$space".$alias['domain_domain'];
                }
              	$space = " ";
							}

							$aw_conf_file = "Include \"/etc/awstats/awstats.shared.conf\"

LogFile=\"$logfile\"

SiteDomain=\"$web_real_name\"

DirData=\"$stats_path\"

Lang=\"$lang\"

HostAliases=\"$host_alias\"";

							$fp = fopen ("/etc/awstats/awstats.".$web_real_name.".conf", "w");
							fwrite($fp,$aw_conf_file);
							fclose($fp);
							chmod("/etc/awstats/awstats.".$web_real_name.".conf",0644);
						}

						$lastDay = time() - (24 * 60 * 60);
						$year = date('Y', $lastDay);
						$month = date('m', $lastDay);

						$message .= exec("perl /home/admispconfig/ispconfig/tools/awstats/tools/awstats_buildstaticpages.pl -year=$year -month=$month -update -config=$web_real_name -awstatsprog=/home/admispconfig/ispconfig/tools/awstats/wwwroot/cgi-bin/awstats.pl -builddate=$year-$month -dir=$stats_path")."\n";

						// Experimentell: erstelle eine index.html Datei
						if(!@is_dir("$stats_path/index.html")) {
							$index_file = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
<html>
<body>";
							if( $dh = opendir($stats_path)){
								while (($file = readdir($dh)) !== false) {
									$pattern = 'awstats\..*\.[0-9]{4}-[0-9]{2}\.html';
									if (ereg($pattern, $file)) {
										$index_file .= "<a href=\"$file\">$file</a><br />";
		}
	}
							}

							$index_file .= "</body>
</html>";

							$fp = fopen ("$stats_path/index.html", "w");
							fwrite($fp,$index_file);
							fclose($fp);
							chmod("$stats_path/index.html",0644);
						}
					}
					exec("chown -R $web_user:$web_group $stats_path &> /dev/null");
				}

			}
		}
	}

	echo $message;

}
?>
