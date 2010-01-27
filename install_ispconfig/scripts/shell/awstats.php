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
 * Version 2.0
 * Author Jonas Schwarz
 * http://www.jnsc.ch
 *
 ***************************/
set_time_limit(0);

include ("/root/ispconfig/scripts/lib/config.inc.php");
include ("/root/ispconfig/scripts/lib/server.inc.php");
$server_id = $mod->system->server_id;
$server = $mod->system->server_conf;
$path_httpd_root = stripslashes($server["server_path_httpd_root"]);
$global_stats_user = trim($server['global_stats_user']);
$global_stats_password = trim($server['global_stats_password']);
if($global_stats_password != ''){
        //calculate 2/8 random chars as salt for the crypt // by bjmg
        if($go_info["server"]["password_hash"] == 'crypt') {
            $salt="";
            for ($n=0;$n<2;$n++) {
                $salt.=chr(mt_rand(64,126));
            }
        } else {
            $salt="$1$";
            for ($n=0;$n<8;$n++) {
                $salt.=chr(mt_rand(64,126));
            }
            $salt.="$";
        }

        $global_stats_password = crypt($global_stats_password, $salt);
}

exec("which perl", $perl_location, $verify);

unset ($perl_location);

if ($verify == 0)
{

    $web_home = $path_httpd_root;

    $message = "";

    $handle = @opendir($web_home);
    while ($dir = @readdir($handle))
    {
        if ($dir != "." && $dir != "..")
        {
            if (@!is_link("$web_home/$dir") && substr($dir, 0, 3) == "web" && is_numeric(substr($dir, 3)))
            {
                // ist kein symbolischer Link

                $webname = $dir;
                $web_doc_id = str_replace("web", "", $webname);
                if ($web_data = $mod->db->queryOneRecord("SELECT * FROM isp_isp_web WHERE doc_id = '$web_doc_id' AND webalizer_stats = '2'"))
                {
                    $web_path = $web_home."/$webname/web";
                    $stats_path = $web_path."/awstats";
                    $logfile = $web_home."/$webname/log/web.log";
                    $web_user = fileowner($web_path);
                    $web_group = filegroup($web_path);

                    // erstelle awstats Verzeichnis, wenn nicht vorhanden
                    if (!@is_dir($stats_path))
                    {
                        mkdir($stats_path, 0775);
                        chown($stats_path, $web_user);
                        chgrp($stats_path, $web_group);
                        $message .= "Erstelle Statistik Verzeichnis: $stats_path\n";
                    }


                    // Experimentell: erstelle .htaccess Dateien mit Zugangsberechtigung f�r Gruppe des Webs
                    if (!@is_dir($stats_path."/.htaccess")AND!file_exists($stats_path."/.htaccess"))
                    {

                        $ht_file = "AuthType Basic
AuthName \"Members Only\"
AuthUserFile $web_home/$webname/.htpasswd
<limit GET PUT POST>
require valid-user
</limit>";
                        $fp = fopen($stats_path."/.htaccess", "w");
                        fwrite($fp, $ht_file);
                        fclose($fp);
                        chmod($stats_path."/.htaccess", 0664);
                    }

                    if (!@is_dir($web_home."/".$webname."/.htpasswd"))
                    {

                        exec("cat ".$mod->system->server_conf["passwd_datei"]." | grep ".$web_home."/".$webname."/ |cut -f1 -d:", $users);
                        exec("cat ".$mod->system->server_conf["passwd_datei"]." | grep ".$web_home."/".$webname.": |cut -f1 -d:", $users);
                        $ht_file = "";
                        if (! empty($users))
                        {
                            foreach ($users as $user)
                            {
                                $user_password = exec("cat ".$mod->system->server_conf["shadow_datei"]." | grep '$user:' | grep -w $user |cut -f2 -d:");
                                $ht_file .= "$user:$user_password\n";
                            }
                        } else
                        {
			  //$ht_file .= "admin:\$1\$TAVCXZlv\$NAjnpdNgAfPMNT4/A61Z.0\n";
                        }
			if($global_stats_user != '' && $global_stats_password != '') $ht_file .= $global_stats_user.":".$global_stats_password."\n";

                        unset ($users);
                        $fp = fopen($web_home."/".$webname."/.htpasswd", "w");
                        fwrite($fp, $ht_file);
                        fclose($fp);
                        chmod($web_home."/".$webname."/.htpasswd", 0664);
                        exec("chown :".$webname." ".$web_home."/".$webname."/.htpasswd");
                    }

                    if (! empty($web_data["web_host"]))
                    {
                        $web_real_name = $web_data["web_host"].".".$web_data["web_domain"];
                    } else
                    {
                        $web_real_name = $web_data["web_domain"];
                    }

                    // Starte AWStats
                    if (@is_file($logfile))
                    {

                        // Experimentell: erstelle /etc/awstats/meindomain.config Datei
                        if (!@is_dir("/etc/awstats/awstats.".$web_real_name.".conf")AND!file_exists("/etc/awstats/awstats.".$web_real_name.".conf"))
                        {
                            $lang = $mod->db->queryOneRecord("SELECT language FROM isp_nodes, sys_user WHERE isp_nodes.doctype_id = 1013 and isp_nodes.doc_id = $web_doc_id and sys_user.doc_id = isp_nodes.userid");

                            $suported_lang = array ("al", "ba", "bg", "tw", "cn", "cz", "dk", "nl", "en", "et", "fi", "fr", "de", "gr", "he", "hu", "id", "it", "jp", "kr", "lv", "nn", "nb", "pl", "pt", "br", "ro", "ru", "sr", "sk", "es", "es_cat", "se", "tr", "ua", "wlk"); // For more info http://awstats.sourceforge.net/docs/awstats_config.html#Lang

                            if ($lang['language'])
                            {
                                $lang = $lang['language'];
                            } else
                            {
                                $lang = $go_info["server"]["lang"];
                            }

                            if (!in_array("$lang", $suported_lang))
                            {
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

                            foreach ($web_alias as $alias)
                            {
                                if (! empty($alias['domain_host']))
                                {
                                    $host_alias .= "$space".$alias['domain_host'].".".$alias['domain_domain'];
                                } else
                                {
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

                            $fp = fopen("/etc/awstats/awstats.".$web_real_name.".conf", "w");
                            fwrite($fp, $aw_conf_file);
                            fclose($fp);
                            chmod("/etc/awstats/awstats.".$web_real_name.".conf", 0644);
                        }

                        $lastDay = time()-(24*60*60);
                        $year = date('Y', $lastDay);
                        $month = date('m', $lastDay);

                        $message .= exec("perl /home/admispconfig/ispconfig/tools/awstats/tools/awstats_buildstaticpages.pl -year=$year -month=$month -update -config=$web_real_name -awstatsprog=/home/admispconfig/ispconfig/tools/awstats/wwwroot/cgi-bin/awstats.pl -builddate=$year-$month -dir=$stats_path")."\n";

                        // Experimentell: erstelle eine index.html Datei
                        if (!@is_dir("$stats_path/index.html"))
                        {
                            $index_file = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
<html>
<head>
<title>AWStats - $web_real_name</title>
<style type=\"text/css\">
h1, h2, p {text-align:center}
</style>
</head>
<body>
<h1>$web_real_name</h1>\n";
                            if ($dh = opendir($stats_path))
                            {
                                $files = array ();
                                while (($file = readdir($dh)) !== false)
                                {
                                    $pattern = 'awstats\..*\.[0-9]{4}-[0-9]{2}\.html';
                                    if (ereg($pattern, $file))
                                    {
                                        ereg("(awstats).(.*).([0-9]{4})-([0-9]{2}).(html)", $file, $date);
                                        $files[$date[3]][$date[4]] = "<a href=\"$file\">".$date[4]."</a><br />\n";
                                    }
                                }
                            }

                            krsort($files);

                            foreach ($files as $year=>$months)
                            {
                                krsort($months);
                                $index_file .= "<h2>$year</h2>\n<p>\n";
                                foreach ($months as $file)
                                {
                                    $index_file .= "$file";
                                }
                                $index_file .= "</p>\n";
                            }

                            $index_file .= "</body>
</html>";

                            $fp = fopen("$stats_path/index.html", "w");
                            fwrite($fp, $index_file);
                            fclose($fp);
                            chmod("$stats_path/index.html", 0644);
                        }
                    }

		    if($go_info["server"]["perms_root"]["stats"]===true) {
		      exec("chown -R root:root $stats_path &> /dev/null");
		    } else {
		      exec("chown -R $web_user:$web_group $stats_path &> /dev/null");
		    }
                }

            }
        }
    }

    echo $message;

}
?>
