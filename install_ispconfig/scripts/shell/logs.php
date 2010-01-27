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

set_time_limit(0);

$web_doctype_id = 1013;
include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");
$server_id = $mod->system->server_id;

$server = $mod->system->server_conf;
$dist_init_scripts = $mod->system->server_conf["dist_init_scripts"];
$dist_httpd_daemon = $mod->system->server_conf["dist_httpd_daemon"];

$access_log = $server["server_path_httpd_log"] . "_" . date("Y_m_d",time() - 43200);

//echo "opening: $access_log\n";

if(!$mod->file->is_file_lfs($access_log)) die();
$webroot = stripslashes($server["server_path_httpd_root"]);

function getmicrotime(){
   list($usec, $sec) = explode(" ",microtime());
   return ((float)$usec + (float)$sec);
}

function get_filename($virtual_host) {
        global $webroot, $jahr, $monat, $mod;
        if(!is_dir("$webroot/$virtual_host/log/$jahr/$monat")) $mod->file->mkdirs("$webroot/$virtual_host/log/$jahr/$monat");
        return "$webroot/$virtual_host/log/$jahr/$monat/web.log";
}

$time_start = getmicrotime();

$zeilen_gesamt = 0;
$handles = array();
$max_handles = 10;

$tag = date("d", (time() - 43200));
$monat = date("m", (time() - 43200));
$jahr = date("Y", (time() - 43200));

if($mod->file->is_file_lfs($access_log)) {
        $traffic = array();
        $fd = fopen($access_log, "r");
        while(!feof($fd)){

                $buffer = fgets($fd);
                list($virtual_host,$bytes,$normal_log) = explode("||||",$buffer);
                $bytes = trim($bytes);
                if($bytes != "-") $traffic[$virtual_host] += $bytes;

                // Logfile schreiben
                if($virtual_host != '') {

                        if(isset($handles[$virtual_host])) {
                                $h = $handles[$virtual_host];
                                unset($handles[$virtual_host]);
                                $handles[$virtual_host] = $h;
                        } else {
                                // Handle noch nicht in Liste
                                if(count($handles) > $max_handles) {
                                        unset($handles[0]);
                                }
                                $filename = get_filename($virtual_host);
                                $handles[$virtual_host]= fopen($filename, 'a');
                        }

                        $handle = $handles[$virtual_host];

                        if (!fwrite($handle, $normal_log)) {
                                //echo "Cannot write to file ($filename)";
                        } else {
                                $zeilen_gesamt++;
                        }
                }
        }
        fclose ($fd);

        // die offenen Handles schliessen
        foreach($handles as $handle) {
                fclose($handle);
        }

} else {
        die("Access log not found: $access_log");
}

$monat_jahr = date("m/Y", (time() - 43200));
$jahr = date("Y", (time() - 43200));
$current_time = time();

// Traffic auswerten und Symlinks für Webalizer aktualisieren
foreach($traffic as $virtual_host => $bytes) {

        if(trim($virtual_host) != '' && trim($virtual_host)!='localhost') {
                // Traffic in DB Schreiben
                // Bestimme Web-ID
                $link =  readlink("$webroot/$virtual_host");
                $parts = split("/",$link);
                $web_id = intval(substr($parts[count($parts) - 1],3));

                if($web_id > 0) {
                        $verify = $mod->db->queryAllRecords("SELECT * FROM isp_traffic WHERE web_id = '$web_id' AND monat = '$monat_jahr'");
                          if(empty($verify)){
                            $mod->db->query("INSERT INTO isp_traffic (web_id, monat, jahr, bytes_web, datum) VALUES ('$web_id','$monat_jahr','$jahr','$bytes','$current_time')");
                          } else {
                            $mod->db->query("UPDATE isp_traffic SET bytes_web = bytes_web + $bytes WHERE web_id = '$web_id' AND monat = '$monat_jahr'");
                          }
                }

                // Symlinks für webalizer korrigieren, falls neuer Monat
                if(@readlink("$webroot/$virtual_host/log/web.log") != get_filename($virtual_host)) {
                        if(is_link("$webroot/$virtual_host/log/web.log")) @unlink("$webroot/$virtual_host/log/web.log");
                        @symlink(get_filename($virtual_host),"$webroot/$virtual_host/log/web.log");
                }
                clearstatcache();
		if($go_info["server"]["perms_root"]["logs"]===true) {
		  exec("chown -R root:root ".$webroot."/".$virtual_host."/log &> /dev/null");
		} else {
		  $web_owner = @fileowner($webroot."/".$virtual_host."/log");
		  exec("chown -R ".$web_owner.":web".$web_id." ".$webroot."/".$virtual_host."/log &> /dev/null");
		}
        }

}

// lösche alte access logs

if (is_int($go_info["server"]["accesslog_purge_days"]) && $go_info["server"]["accesslog_purge_days"]>0) {
  $purge = $go_info["server"]["accesslog_purge_days"];
} else {
  $purge = 2; // default value
}
$dir = dirname($server["server_path_httpd_log"]);
if (is_dir($dir)) {
  if ($dh = opendir($dir)) {
    while (($file = readdir($dh)) !== false) {
      if (preg_match('/^'.basename($dir).'_/', $file)) {
	$files[] = $file;
        $index[] = filemtime( $dir.'/'.$file );
      }
    }
    closedir($dh);
  }
  asort($index);
  foreach($index as $i => $t) {
    if($t < time()-86400*$purge) {
      @unlink($dir.'/'.$files[$i]);
    }
  }
}

$time_end = getmicrotime();
$dauer = $time_end - $time_start;
//echo "Duration: $dauer seconds. Lines: $zeilen_gesamt\n";

?>