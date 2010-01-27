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
* ISPConfig Webalizer Script
* Version 1.0
*
***************************/
set_time_limit(0);

//** 1 = Debugging enabled, 0 = Debugging disabled -----------------
$webalizer_debug = 0;
// -----------------------------------------------------------------

include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");
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

exec("which webalizer", $webalizer_location, $verify);

unset($webalizer_location);

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
            if($web_data = $mod->db->queryOneRecord("SELECT * FROM isp_isp_web WHERE doc_id = '$web_doc_id' AND webalizer_stats = '1'")){
              $web_path = $web_home . "/$webname/web";
              $old_stats_path = $web_path . "/stats";
              $stats_path = $web_path . "/webalizer";
              $logfile = $web_home . "/$webname/log/web.log";
              $web_user = fileowner($web_path);
              $web_group = filegroup($web_path);

              // erstelle Stats Verzeichnis, wenn nicht vorhanden
              if(!@is_dir($stats_path)) {
                if(@is_dir($old_stats_path)){
                  rename($old_stats_path, $stats_path);
                  $message .= "Benenne Statistik Verzeichnis: $old_stats_path um in $stats_path\n";
                } else {
                  mkdir($stats_path,0775);
                  chown($stats_path,$web_user);
                  chgrp($stats_path,$web_group);
                  $message .= "Erstelle Statistik Verzeichnis: $stats_path\n";
                }
              }


              // Experimentell: erstelle .htaccess Dateien mit Zugangsberechtigung für Gruppe des Webs
              if(!@is_dir($stats_path."/.htaccess")) {

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
                    //$ht_file .= "admin:\$1\$TAVCXZlv\$NAjnpdNgAfPMNT4/A61Z.0\n";
                  }
                  if($global_stats_user != '' && $global_stats_password != '') $ht_file .= $global_stats_user.":".$global_stats_password."\n";

                  unset($users);
                  $fp = fopen ($web_home."/".$webname."/.htpasswd", "w");
                  fwrite($fp,$ht_file);
                  fclose($fp);
                  chmod($web_home."/".$webname."/.htpasswd",0664);
                  exec("chown :".$webname." ".$web_home."/".$webname."/.htpasswd");
              }


              // Starte Webalizer
              if(@$mod->file->is_file_lfs($logfile)) {
                  if(!empty($web_data["web_host"])){
                    $web_real_name = $web_data["web_host"].".".$web_data["web_domain"];
                  } else {
                    $web_real_name = $web_data["web_domain"];
                  }
                  $message .= exec("webalizer -n $web_real_name -s $web_real_name -r $web_real_name -q -T -o $stats_path -c /root/ispconfig/scripts/shell/webalizer.conf $logfile")."\n";
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

////////////// LOGSIZE //////////////////

function dir_size($dir) {
  global $mod;
  $totalsize=0;
  if ($dirstream = @opendir($dir)) {
    while (false !== ($filename = readdir($dirstream))) {
      if ($filename!="." && $filename!=".."){
        if ($mod->file->is_file_lfs($dir."/".$filename) && !is_link($dir."/".$filename)){
          $totalsize += exec('wc -c '.$dir.'/'.$filename.' | cut -f1 -d " "');
          //$totalsize+=filesize($dir."/".$filename);
        }
        if (is_dir($dir."/".$filename)) $totalsize+=dir_size($dir."/".$filename);
      }
    }
  }
  closedir($dirstream);
  clearstatcache();
  return (float)$totalsize;
}

function dir_array($dir){
  global $mod;
  $directory_array = array();
  if ($dirstream = @opendir($dir)) {
    while (false !== ($filename = readdir($dirstream))) {
      if ($filename!="." && $filename!=".." && $filename!=".no_delete"){
        if ($mod->file->is_file_lfs($dir."/".$filename) && !is_link($dir."/".$filename)){
          //$directory_array[$dir."/".$filename] = filemtime($dir."/".$filename);
          $directory_array[$dir."/".$filename] = exec('stat -c %Y '.$dir.'/'.$filename);
        }
        if (is_dir($dir."/".$filename)) $directory_array = array_merge($directory_array, dir_array($dir."/".$filename));
      }
    }
  }
  closedir($dirstream);
  clearstatcache();
  return $directory_array;
}

$webs = $mod->db->queryAllRecords("SELECT * FROM isp_isp_web");
if(!empty($webs)){
  foreach($webs as $web){
    if($webalizer_debug == 1) echo "Domain: $web[web_domain]\n";
    $log_dir = $path_httpd_root."/web".$web["doc_id"]."/log";
    if(is_dir($log_dir)){
          if($webalizer_debug == 1) echo "Log Dir: $log_dir\n";
      $max_directory_size = str_replace(",", ".", trim($web["optionen_logsize"]));
      if(strstr($max_directory_size, '%')){
        if($web["web_speicher"] == -1){
          $log_check = false;
                  if($webalizer_debug == 1) echo "We do not check the logsize as Quota is set to unlimited.\n";
        } else {
          $parts = explode('%', $max_directory_size);
          if(is_numeric(trim($parts[0])) && trim($parts[0]) >= 0){
            $max_directory_size = str_replace(",", ".", $web["web_speicher"]) * 1048576 * floatval($max_directory_size) / 100;
            $log_check = true;
                        if($webalizer_debug == 1) echo "Max Log size: $max_directory_size\n";
          } else {
            $log_check = false;
          }
          $parts = NULL;
        }
      } else {
        if(is_numeric($max_directory_size) && $max_directory_size >= 0){
          $max_directory_size = $max_directory_size * 1048576;
          $log_check = true;
                  if($webalizer_debug == 1) echo "Max Log size: $max_directory_size\n";
        } else {
          $log_check = false;
        }
      }
      $directory_size = dir_size($log_dir);
      $max_directory_size = (float)$max_directory_size;
      if($webalizer_debug == 1) echo "Current Log size: $directory_size\n";

      if($log_check){
        while($directory_size >= $max_directory_size){
          $files = dir_array($log_dir);
          if(!empty($files)){
            asort($files);
            $files = array_slice ($files, 0, 1);
            foreach($files as $key => $val){
              if($mod->file->is_file_lfs($key)) unlink($key);
                          if($webalizer_debug == 1) echo "Deleting logfile $key\n";
            }
          } else {
            break;
          }
          unset($files);
          $directory_size = dir_size($log_dir);
        }
      }
    }
  }
}
//////////////// LOGSIZE ENDE ////////////////
?>