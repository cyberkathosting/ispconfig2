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

$FILE = realpath("../install_ispconfig/install.php");

function ilog($msg){
  $logfile = "/var/log/ispconfig_install.log";
  exec("echo `date` \"- [ISPConfig] - \"".$msg." >> ".$logfile);
}

function error($msg){
  ilog($msg);
  die($msg."\n");
}

function caselog($command, $file = '', $line = '', $success = '', $failure = ''){
  exec($command,$arr,$ret_val);
  $arr = NULL;
  if(!empty($file) && !empty($line)){
    $pre = $file.", Line ".$line.": ";
  } else {
    $pre = "";
  }
  if($ret_val != 0){
    if($failure == "") $failure = "could not ".$command;
    ilog($pre."WARNING: ".$failure);
  } else {
    if($success == "") $success = $command;
    ilog($pre.$success);
  }
}

function phpcaselog($ret_val, $msg, $file = '', $line = ''){
  if(!empty($file) && !empty($line)){
    $pre = $file.", Line ".$line.": ";
  } else {
    $pre = "";
  }
  if($ret_val == true){
    ilog($pre.$msg);
  } else {
    ilog($pre."WARNING: could not ".$msg);
  }
  return $ret_val;
}

function mkdirs($strPath, $mode = '0755'){
  if(isset($strPath) && $strPath != ""){
    // Verzeichnisse rekursiv erzeugen
    if(is_dir($strPath)) return true;
    $pStrPath = dirname($strPath);
    if(!mkdirs($pStrPath, $mode)) return false;
    $old_umask = umask(0);
    $ret_val = mkdir($strPath, octdec($mode));
    umask($old_umask);
    return $ret_val;
  } else {
    return false;
  }
}

function rf($file){
  clearstatcache();
  if(!$fp = fopen ($file, "rb")) ilog("WARNING: could not open file ".$file);
  if(filesize($file) > 0){
    $content = fread($fp, filesize($file));
  } else {
    $content = "";
  }
  fclose($fp);
  return $content;
}

function wf($file, $content){
  mkdirs(dirname($file));
  if(!$fp = fopen ($file, "wb")) ilog("WARNING: could not open file ".$file);
  fwrite($fp,$content);
  fclose($fp);
}

function af($file, $content){
  mkdirs(dirname($file));
  if(!$fp = fopen ($file, "ab")) ilog("WARNING: could not open file ".$file);
  fwrite($fp,$content);
  fclose($fp);
}

function aftsl($file, $content){
  if(!$fp = fopen ($file, "ab")) ilog("WARNING: could not open file ".$file);
  fwrite($fp,$content);
  fclose($fp);
}

function unix_nl($input){
  $output = str_replace("\r\n", "\n", $input);
  $output = str_replace("\r", "\n", $output);
  return $output;
}

function remove_blank_lines($input, $file = 1){
  //Leerzeilen löschen
  if($file){
    $content = unix_nl(rf($input));
  } else {
    $content = $input;
  }
  $lines = explode("\n", $content);
  if(!empty($lines)){
    foreach($lines as $line){
      if(trim($line) != "") $new_lines[] = $line;
    }
  }
  if(is_array($new_lines)){
    $content = implode("\n", $new_lines);
  } else {
    $content = "";
  }
  if($file){
    wf($input, $content);
  } else {
    return $content;
  }
}

function no_comments($file, $comment = '#'){
  $content = unix_nl(rf($file));
  $lines = explode("\n", $content);
  if(!empty($lines)){
    foreach($lines as $line){
      if(strstr($line, $comment)){
        $pos = strpos($line, $comment);
        if($pos != 0){
          $new_lines[] = substr($line,0,$pos);
        } else {
          $new_lines[] = "";
        }
      } else {
        $new_lines[] = $line;
      }
    }
  }
  if(is_array($new_lines)){
    $content_without_comments = implode("\n", $new_lines);
    $new_lines = NULL;
    return $content_without_comments;
  } else {
    return "";
  }
}

function find_includes($file){
  global $httpd_root;
  clearstatcache();
  if(is_file($file) && filesize($file) > 0){
    $includes[] = $file;
    $inhalt = unix_nl(no_comments($file));
    $lines = explode("\n", $inhalt);
    if(!empty($lines)){
      foreach($lines as $line){
        if(stristr($line, "include ")){
          $include_file = str_replace("\n", "", trim(shell_exec("echo \"$line\" | awk '{print \$2}'")));
          if(substr($include_file,0,1) != "/"){
            $include_file = $httpd_root."/".$include_file;
          }
          if(is_file($include_file)){
            if($further_includes = find_includes($include_file)){
              $includes = array_merge($includes, $further_includes);
            }
          } else {
            if(strstr($include_file, "*")){
              $more_files = explode("\n", shell_exec("ls -l $include_file | awk '{print \$9}'"));
              if(!empty($more_files)){
                foreach($more_files as $more_file){
                  if(is_file($more_file)){
                    if($further_includes = find_includes($more_file)){
                      $includes = array_merge($includes, $further_includes);
                    }
                  }
                }
              }
              unset($more_files);
              $more_files = explode("\n", shell_exec("ls -l $include_file | awk '{print \$10}'"));
              if(!empty($more_files)){
                foreach($more_files as $more_file){
                  if(is_file($more_file)){
                    if($further_includes = find_includes($more_file)){
                      $includes = array_merge($includes, $further_includes);
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }
  if(is_array($includes)){
    $includes = array_unique($includes);
    return $includes;
  } else {
    return false;
  }
}

function comment_out($file, $string){
  $inhalt = no_comments($file);
  $gesamt_inhalt = rf($file);
  $modules = explode(",",$string);
  foreach($modules as $val){
    $val = trim($val);
    if(strstr($inhalt, $val)){
      $gesamt_inhalt = str_replace($val, "##ISPConfig INSTALL## ".$val, $gesamt_inhalt);
    }
  }
  wf($file, $gesamt_inhalt);
}

function is_word($string, $text, $params = ''){
  // params: i
  if(preg_match("/\b$string\b/$params", $text)) {
    return true;
  } else {
    return false;
  }
}

function grep($content, $string, $params = ''){
  // params: i, v, w
  $content = unix_nl($content);
  $lines = explode("\n", $content);
  foreach($lines as $line){
    if(!strstr($params, 'w')){
      if(strstr($params, 'i')){
        if(strstr($params, 'v')){
          if(!stristr($line, $string)) $find[] = $line;
        } else {
          if(stristr($line, $string)) $find[] = $line;
        }
      } else {
        if(strstr($params, 'v')){
          if(!strstr($line, $string)) $find[] = $line;
        } else {
          if(strstr($line, $string)) $find[] = $line;
        }
      }
    } else {
      if(strstr($params, 'i')){
        if(strstr($params, 'v')){
          if(!is_word($string, $line, 'i')) $find[] = $line;
        } else {
          if(is_word($string, $line, 'i')) $find[] = $line;
        }
      } else {
        if(strstr($params, 'v')){
          if(!is_word($string, $line)) $find[] = $line;
        } else {
          if(is_word($string, $line)) $find[] = $line;
        }
      }
    }
  }
  if(is_array($find)){
    $ret_val = implode("\n", $find);
    if(substr($ret_val,-1) != "\n") $ret_val .= "\n";
    $find = NULL;
    return $ret_val;
  } else {
    return false;
  }
}

function edit_xinetd_conf($service){
  $xinetd_conf = "/etc/xinetd.conf";
  $contents = unix_nl(rf($xinetd_conf));
  $lines = explode("\n", $contents);
  $j = sizeof($lines);
  for($i=0;$i<sizeof($lines);$i++){
    if(grep($lines[$i], $service, "w")){
      $fundstelle_anfang = $i;
      $j = $i;
      $parts = explode($lines[$i], $contents);
    }
    if($j < sizeof($lines)){
      if(strstr($lines[$i], "}")){
        $fundstelle_ende = $i;
        $j = sizeof($lines);
      }
    }
  }
  if(isset($fundstelle_anfang) && isset($fundstelle_ende)){
    for($i=$fundstelle_anfang;$i<=$fundstelle_ende;$i++){
      if(strstr($lines[$i], "disable")){
        $disable = explode("=", $lines[$i]);
        $disable[1] = " yes";
        $lines[$i] = implode("=", $disable);
      }
    }
  }
  $fundstelle_anfang = NULL;
  $fundstelle_ende = NULL;
  $contents = implode("\n", $lines);
  wf($xinetd_conf, $contents);
}

$current_date = time();
$datum = date("d-m-y_H-i-s", $current_date);
$conf_datei_temp = "config.inc.php.tmp";
$sql_file = "db_ispconfig.sql";
$pfad = "/home/admispconfig";

$mysql = rf("mysql_config");
list($db_server,$db_user,$db_password,$new_db,$ip,$server_host,$server_domain,$procmail,$lang,$install_art,$server_ispconfigprotocol) = explode("\n",$mysql);
$server_ispconfigprotocol = strtolower(trim($server_ispconfigprotocol));

$conf = rf("/root/ispconfig/dist.info");

$conf = str_replace("dist", "\$dist", $conf);
$conf = str_replace("=", " = \"", $conf);
$conf = str_replace(" ##", "\";", $conf);
$conf_lines = explode("\n", $conf);
foreach($conf_lines as $conf_line){
  if(substr($conf_line,0,6) != "export") $new_conf_lines[] = $conf_line;
}
$conf = implode("\n", $new_conf_lines);
$conf = "<?\n".$conf."\n?>";
unset($conf_lines);
unset($new_conf_lines);

wf("/root/ispconfig/dist.inc.php", $conf);

include("/root/ispconfig/dist.inc.php");
$dist_passwd = trim($dist_passwd);
$dist_shadow = trim($dist_shadow);
$dist_group = trim($dist_group);

/////////////// Apache-Init-Skript finden ////////////////
ob_start();
system("ls ".$dist_init_scripts." | grep apache");
system("ls ".$dist_init_scripts." | grep httpd");
$tmp_output = trim(ob_get_contents());
ob_end_clean();

if($tmp_output != ""){
  $tmp_httpd_daemon = explode("\n", $tmp_output);
} else {
  $tmp_httpd_daemon = array();
}
unset($tmp_output);

if(sizeof($tmp_httpd_daemon) == 1 && !empty($tmp_httpd_daemon)) $dist_httpd_daemon = trim($tmp_httpd_daemon[0]);
unset($tmp_httpd_daemon);
/////////////// Apache-Init-Skript finden ENDE ////////////////

/////////////////////////////////
function is_user($user){
  global $dist_passwd;
  $user_datei = $dist_passwd;
  $users = no_comments($user_datei);
  $lines = explode("\n", $users);
  if(is_array($lines)){
    foreach($lines as $line){
      if(trim($line) != ""){
        list($f1, $f2, $f3, $f4, $f5, $f6, $f7) = explode(":", $line);
        if($f1 == $user) return true;
      }
    }
  }
  return false;
}

function is_group($group){
  global $dist_group;
  $group_datei = $dist_group;
  $groups = no_comments($group_datei);
  $lines = explode("\n", $groups);
  if(is_array($lines)){
    foreach($lines as $line){
      if(trim($line) != ""){
        list($f1, $f2, $f3, $f4) = explode(":", $line);
        if($f1 == $group) return true;
      }
    }
  }
  return false;
}

function root_group(){
  global $dist_group;
  $group_datei = $dist_group;
  $groups = no_comments($group_datei);
  $lines = explode("\n", $groups);
  if(is_array($lines)){
    foreach($lines as $line){
      if(trim($line) != ""){
        list($f1, $f2, $f3, $f4) = explode(":", $line);
        if($f3 == 0) return $f1;
      }
    }
  }
  return false;
}

function adduser($user_username, $uid, $gid, $username, $homedir, $shell, $passwort = '*'){
  global $dist_passwd, $dist_shadow, $FILE;
  if(is_user($user_username)){
    return false;
  } else {
    $user_datei = $dist_passwd;
    $shadow_datei = $dist_shadow;
    $shell = realpath($shell);
    if(trim($passwort) == "") $passwort = '*';
    $new_user = "\n$user_username:x:$uid:$gid:$username:$homedir:$shell\n";
    af($user_datei, $new_user);
    remove_blank_lines($user_datei);
    if($shadow_datei == "/etc/shadow"){
      $datum = time();
      $tage = floor($datum/86400);
      $new_passwd = "\n$user_username:$passwort:$tage:0:99999:7:::\n";
    } else {
      $new_passwd = "\n$user_username:$passwort:$uid:$gid::0:0:$username:$homedir:$shell\n";
    }
    af($shadow_datei, $new_passwd);
    remove_blank_lines($shadow_datei);
    if($shadow_datei != "/etc/shadow"){
      af($shadow_datei, "\n");
      caselog("pwd_mkdb $shadow_datei &> /dev/null", $FILE, __LINE__);
    }
    return true;
  }
}

function addgroup($group, $gid, $members = ''){
  global $dist_group, $dist_shadow, $FILE;
  if(is_group($group)){
    return false;
  } else {
    $group_datei = $dist_group;
    $shadow_datei = $dist_shadow;
    $new_group = "\n$group:x:$gid:$members\n";
    af($group_datei, $new_group);
    remove_blank_lines($group_datei);
    if($shadow_datei != "/etc/shadow"){
      af($group_datei, "\n");
      caselog("pwd_mkdb $shadow_datei &> /dev/null", $FILE, __LINE__);
    }
    return true;
  }
}

function find_uid_gid($min, $max){
  global $dist_passwd, $dist_group;
  if($min < $max && $min >= 0 && $max >= 0 && $min <= 65536 && $max <= 65536 && is_int($min) && is_int($max)){
    $user_datei = $dist_passwd;
    $group_datei = $dist_group;

    $users = no_comments($user_datei);
    $lines = explode("\n", $users);
    if(is_array($lines)){
      foreach($lines as $line){
        if(trim($line) != ""){
          list($f1, $f2, $f3, $f4, $f5, $f6, $f7) = explode(":", $line);
          if($f3 >= $min && $f3 <= $max) $uids[] = $f3;
        }
      }
      if(is_array($uids)){
        $max_uid = max($uids);
      } else {
        $max_uid = $min - 1;
      }
    }

    $groups = no_comments($group_datei);
    $lines = explode("\n", $groups);
    if(is_array($lines)){
      foreach($lines as $line){
        if(trim($line) != ""){
          list($f1, $f2, $f3, $f4) = explode(":", $line);
          if($f3 >= $min && $f3 <= $max) $gids[] = $f3;
        }
      }
      if(is_array($gids)){
        $max_gid = max($gids);
      } else {
        $max_gid = $min - 1;
      }
    }

    $new_id = (max($max_uid, $max_gid) + 1);
    if($new_id <= $max){
      return $new_id;
    } else {
      return false;
    }
  } else {
    return false;
  }
}

function rc_edit($service, $rl, $action){
  // $action = "on|off";
  global $dist_init_scripts, $dist_runlevel, $dist;
  if(trim($dist_runlevel) == ""){ // falls es keine runlevel gibt (FreeBSD)
    if($action == "on"){
      @symlink($dist_init_scripts."/".$service, $dist_init_scripts."/".$service.".sh");
    }
    if($action == "off"){
      if(is_link($dist_init_scripts."/".$service.".sh")){
        unlink($dist_init_scripts."/".$service.".sh");
      } else {
        exec("mv -f ".$dist_init_scripts."/".$service.".sh ".$dist_init_scripts."/".$service." &> /dev/null");
      }
    }
  } else { // Linux
    if(substr($dist, 0,4) == 'suse'){
      if($action == "on"){
        exec("chkconfig --add $service &> /dev/null");
      }
      if($action == "off"){
        exec("chkconfig --del $service &> /dev/null");
      }
    } else {
      $runlevels = explode(",", $rl);
      foreach($runlevels as $runlevel){
        $runlevel = trim($runlevel);
        if($runlevel != "" && is_dir($dist_runlevel."/rc".$runlevel.".d")){
          $handle=opendir($dist_runlevel."/rc".$runlevel.".d");
          while($file = readdir($handle)){
            if($file != "." && $file != ".."){
              $target = @readlink($dist_runlevel."/rc".$runlevel.".d/".$file);
              if(strstr($file, $service) && strstr($target, $service) && substr($file,0,1) == "S") $ln_arr[$runlevel][] = $dist_runlevel."/rc".$runlevel.".d/".$file;
            }
          }
          closedir($handle);
        }
        if($action == "on"){
          if(!is_array($ln_arr[$runlevel])) @symlink($dist_init_scripts."/".$service, $dist_runlevel."/rc".$runlevel.".d/S99".$service);
        }
        if($action == "off"){
          if(is_array($ln_arr[$runlevel])){
            foreach($ln_arr[$runlevel] as $link){
              unlink($link);
            }
          }
        }
      }
    }
  }
}

function daemon_init($daemon, $action){
  // $action = start|stop|restart|reload
  global $dist, $dist_init_scripts, $FILE;
  if(!strstr($dist, "freebsd")){
    caselog("$dist_init_scripts/$daemon $action &> /dev/null", $FILE, __LINE__);
  } else {
    if(is_file($dist_init_scripts."/".$daemon.".sh") || is_link($dist_init_scripts."/".$daemon.".sh")){
      if($action == "start" || $action == "stop"){
        caselog($dist_init_scripts."/".$daemon.".sh ".$action." &> /dev/null", $FILE, __LINE__);
      } else {
        caselog($dist_init_scripts."/".$daemon.".sh stop &> /dev/null", $FILE, __LINE__);
        sleep(3);
        caselog($dist_init_scripts."/".$daemon.".sh start &> /dev/null", $FILE, __LINE__);
      }
    } else {
      if(is_file($dist_init_scripts."/".$daemon) || is_link($dist_init_scripts."/".$daemon)){
        if($action == "start" || $action == "stop"){
          caselog($dist_init_scripts."/".$daemon." ".$action." &> /dev/null", $FILE, __LINE__);
        } else {
          caselog($dist_init_scripts."/".$daemon." stop &> /dev/null", $FILE, __LINE__);
          sleep(3);
          caselog($dist_init_scripts."/".$daemon." start &> /dev/null", $FILE, __LINE__);
        }
      } else {
        if(is_file("/etc/rc.d/".$daemon) || is_link("/etc/rc.d/".$daemon)){
          if($action == "start" || $action == "stop"){
            caselog("/etc/rc.d/".$daemon." ".$action." &> /dev/null", $FILE, __LINE__);
          } else {
            caselog("/etc/rc.d/".$daemon." stop &> /dev/null", $FILE, __LINE__);
            sleep(3);
            caselog("/etc/rc.d/".$daemon." start &> /dev/null", $FILE, __LINE__);
          }
        }
      }
    }
  }
}
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////

$root_gruppe = root_group();
$directory_mode = '0755';

$admispconfig_uid = $admispconfig_gid = find_uid_gid(100, 3000);
if(!is_group("admispconfig")) phpcaselog(addgroup("admispconfig", $admispconfig_gid, "admispconfig"), 'create group admispconfig', $FILE, __LINE__);
if(!is_user("admispconfig")) phpcaselog(adduser("admispconfig", $admispconfig_uid, $admispconfig_gid, "Administrator ISPConfig", "/home/admispconfig", "/bin/bash", '!'), 'create user admispconfig', $FILE, __LINE__);

// Add a user with uid 20000 as last user in the ISPConfig user range
$ispconfigend_uid = $ispconfigend_gid = find_uid_gid(20000, 30000);
if(!is_group("ispconfigend")) caselog("groupadd -g $ispconfigend_gid ispconfigend", $FILE, __LINE__);
if(!is_user("ispconfigend")) caselog("useradd -u $ispconfigend_uid -s `which nologin` ispconfigend -g ispconfigend", $FILE, __LINE__);



/////////// Symlink von /var/spool/mail auf /var/mail ////////////
if(is_dir("/var/mail") && !file_exists("/var/spool/mail")){
  phpcaselog(@symlink("/var/mail", "/var/spool/mail"), 'create symlink from /var/spool/mail to /var/mail', $FILE, __LINE__);
}
/////////// Symlink von /var/spool/mail auf /var/mail ENDE ////////////

if($dist == "Trustix30") {
  /////////// Trustix 30 ///////////
  if(is_dir("/home/mail") && !is_file("/home/mail/admispconfig")){
    phpcaselog(touch("/home/mail/admispconfig"), "create /home/mail/admispconfig", $FILE, __LINE__);
    caselog("chown admispconfig:mail /home/mail/admispconfig &> /dev/null", $FILE, __LINE__);
    caselog("chmod 600 /home/mail/admispconfig", $FILE, __LINE__);
  }
  elseif(is_dir("/var/spool/postfix") && !is_file("/var/spool/postfix/admispconfig")){
    phpcaselog(touch("/var/spool/postfix/admispconfig"), "create /var/spool/postfix/admispconfig", $FILE, __LINE__);
    caselog("chown admispconfig:mail /var/spool/postfix/admispconfig &> /dev/null", $FILE, __LINE__);
    caselog("chmod 600 /var/spool/postfix/admispconfig", $FILE, __LINE__);
  }
  /////////// Trustix 30 end ///////////
} else {
  if(!is_file("/var/spool/mail/admispconfig")){
    phpcaselog(touch("/var/spool/mail/admispconfig"), "create /var/spool/mail/admispconfig", $FILE, __LINE__);
    caselog("chown admispconfig:mail /var/spool/mail/admispconfig &> /dev/null", $FILE, __LINE__);
    caselog("chmod 600 /var/spool/mail/admispconfig", $FILE, __LINE__);
  }
}

if($install_art == "install"){
  $postfix_config = '1';
  $smtp_restart = '1';
  $network_config = '0';
  $sudo_du_enabled = '0';
  $apache2_php = 'addtype';
  $password_hash = 'md5';
  $do_automated_backups = '0';
  $ssh_chroot = '0';
  $httpd_check = '1';
  $salutatory_email_charset = 'iso-8859-1';
  $conf_webdav = '0';
  $conf_force_user_quota = '0';
  $conf_redirect_after_logout = '';
  $conf_redirect_mailuser_after_logout = '';
  $dec_point = ',';
  $thousands_sep = '.';
  $currency = 'EUR';
} else {
  include("config.inc.php");
  $postfix_config = (isset($go_info["server"]["postfix_config"]) ? $go_info["server"]["postfix_config"] : '1');
  $smtp_restart = (isset($go_info["server"]["smtp_restart"]) ? $go_info["server"]["smtp_restart"] : '1');
  $network_config = (isset($go_info["server"]["network_config"]) ? ($go_info["server"]["network_config"] == false ? '0' : '1') : '0');
  $sudo_du_enabled = (isset($go_info["server"]["sudo_du_enabled"]) ? ($go_info["server"]["sudo_du_enabled"] == false ? '0' : '1') : '0');
  $apache2_php = (isset($go_info["server"]["apache2_php"]) ? $go_info["server"]["apache2_php"] : 'addtype');
  $password_hash = (isset($go_info["server"]["password_hash"]) ? $go_info["server"]["password_hash"] : 'md5');
  $do_automated_backups = (isset($go_info["server"]["do_automated_backups"]) ? ($go_info["server"]["do_automated_backups"] == false ? '0' : '1') : '0');
  $ssh_chroot = (isset($go_info["server"]["ssh_chroot"]) ? ($go_info["server"]["ssh_chroot"] == false ? '0' : '1') : '0');
  $httpd_check = (isset($go_info["server"]["httpd_check"]) ? ($go_info["server"]["httpd_check"] == false ? '0' : '1') : '1');
  $salutatory_email_charset = (isset($go_info["server"]["salutatory_email_charset"]) ? $go_info["server"]["salutatory_email_charset"] : 'iso-8859-1');
  $conf_webdav = (isset($go_info["server"]["conf_webdav"]) ? ($go_info["server"]["conf_webdav"] == false ? '0' : '1') : '0');
  $conf_force_user_quota = (isset($go_info["server"]["force_user_quota"]) ? ($go_info["server"]["force_user_quota"] == false ? '0' : '1') : '0');
  $conf_redirect_after_logout = (isset($go_info["server"]["redirect_after_logout"]) ? $go_info["server"]["redirect_after_logout"] : '');
  $conf_redirect_mailuser_after_logout = (isset($go_info["server"]["redirect_mailuser_after_logout"]) ? $go_info["server"]["redirect_mailuser_after_logout"] : '');
  $dec_point = (isset($go_info["localisation"]["dec_point"]) ? $go_info["localisation"]["dec_point"] : ',');
  $thousands_sep = (isset($go_info["localisation"]["thousands_sep"]) ? $go_info["localisation"]["thousands_sep"] : '.');
  $currency = (isset($go_info["localisation"]["currency"]) ? $go_info["localisation"]["currency"] : 'EUR');
  $old_version = str_pad(str_replace(".", "", $go_info["server"]["version"]), 4, "0", STR_PAD_RIGHT);
  $server_url = $go_info["server"]["server_url"];
  $server_ispconfigprotocol = parse_url($server_url);
  $server_ispconfigprotocol = $server_ispconfigprotocol["scheme"];
  $url = str_replace(":81", "", $server_url);
  $db_server = $go_info["server"]["db_host"];
  $db_user = $go_info["server"]["db_user"];
  $db_password = $go_info["server"]["db_password"];
  $new_db = $go_info["server"]["db_name"];
  $link = mysql_connect($db_server, $db_user, $db_password)
    or error("Could not connect to db ".$new_db);
  echo "Connected successfully to db ".$new_db."\n";
  ilog("Connected successfully to db ".$new_db);
  mysql_select_db($new_db, $link);
  ///////////// admispconfig zum Mitglied aller Webgruppen machen /////////
  $conn = mysql_query("SELECT * FROM isp_isp_web");
  while($row = mysql_fetch_array($conn)){
    $web_groups[] = "web".$row["doc_id"];
  }
  if(is_array($web_groups)){
    $group_file = rf($dist_group);
    $group_file_lines = explode("\n", $group_file);
    foreach($group_file_lines as $group_file_line){
      list($group_name,$group_x,$group_id,$group_users) = explode(":",$group_file_line);
      if(in_array($group_name, $web_groups)){
        $group_users = explode(",", str_replace(" ", "", $group_users));
        if(!in_array("admispconfig", $group_users)){
          $group_users[] = "admispconfig";
        }
        $group_users = implode(",", $group_users);
        if(substr($group_users,0,1) == ",") $group_users = substr($group_users,1);
        $group_file_line = $group_name.":".$group_x.":".$group_id.":".$group_users;
      }
      $new_group_file[] = $group_file_line;
    }
    $new_group_file = implode("\n", $new_group_file);
    wf($dist_group, $new_group_file);
    remove_blank_lines($dist_group);
  }
  //////////// admispconfig zum Mitglied aller Webgruppen machen ENDE ////////
  $conn = mysql_query("SELECT * FROM isp_server WHERE doc_id = '1'");
  if($row = mysql_fetch_array($conn)){
    $ip = $row["server_ip"];
    $server_host = $row["server_host"];
    $server_domain = $row["server_domain"];
    $translate = array(
                       "dist" => "dist",
                       "dist_init_scripts" => "dist_init_scripts",
                       "server_ftp_typ" => "dist_ftp",
                       "server_proftpd_conf_datei" => "dist_ftp_conf",
                       "dist_ftp_version" => "dist_ftp_version",
                       "server_proftpd_log" => "dist_ftp_log",
                       "dist_pop3" => "dist_pop3",
                       "dist_pop3_version" => "dist_pop3_version",
                       "dist_runlevel" => "dist_runlevel",
                       "dist_smrsh" => "dist_smrsh",
                       "dist_shells" => "dist_shells",
                       "server_httpd_user" => "dist_http_user",
                       "server_httpd_group" => "dist_http_group",
                       "server_mta" => "dist_mail",
                       "dist_mail_log" => "dist_mail_log",
                       "server_sendmail_virtuser_datei" => "dist_mail_virtusertable",
                       "server_sendmail_cw" => "dist_mail_local_host_names",
                       "server_bind_user" => "dist_bind_user",
                       "server_bind_group" => "dist_bind_group",
                       "server_bind_named_conf" => "dist_bind_conf",
                       "server_bind_zonefile_dir" => "dist_bind_dir",
                       "dist_bind_pidfile" => "dist_bind_pidfile",
                       "dist_bind_hintfile" => "dist_bind_hintfile",
                       "dist_bind_localfile" => "dist_bind_localfile",
                       "passwd_datei" => "dist_passwd",
                       "shadow_datei" => "dist_shadow",
                       "group_datei" => "dist_group",
                       "dist_cron_daemon" => "dist_cron_daemon",
                       "dist_cron_tab" => "dist_cron_tab",
                       "dist_mysql_group" => "dist_mysql_group",
                       "dist_httpd_daemon" => "dist_httpd_daemon",
                       "server_path_httpd_conf" => "dist_httpd_dir",
                       "server_ip" => "dist_ip",
                       "server_path_httpd_root" => "dist_path_httpd_root",
                       "dist_httpd_conf" => "dist_httpd_conf"
                       );
    foreach($row as $key => $value){
      if(isset($translate[$key])){
        $dist_append = $translate[$key]."=".$value." ##\nexport ".$translate[$key]."\n";
        af("/root/ispconfig/dist.info", $dist_append);
      }
    }
  }
  $tables = mysql_list_tables($new_db);

  while (list($table_name) = mysql_fetch_array($tables)) {
    $old_tables_array[] = $table_name;
  }
  unset($tables);

  if(empty($db_password)){
    caselog("mysqldump -h $db_server -u $db_user -c -t --add-drop-table --add-locks --all --quick --lock-tables $new_db > existing_db.sql", $FILE, __LINE__, "dumped old db to file existing_db.sql","could not dump old db to file existing_db.sql");
  } else {
    caselog("mysqldump -h $db_server -u $db_user -p$db_password -c -t --add-drop-table --add-locks --all --quick --lock-tables $new_db > existing_db.sql", $FILE, __LINE__,"dumped old db to file existing_db.sql","could not dump old db to file existing_db.sql");
  }
  exec("chmod 600 existing_db.sql");
  caselog("mv -f root_ispconfig.tar.gz /tmp/root_ispconfig_".date("m_d_Y__H_i_s", $current_date).".tar.gz", $FILE, __LINE__,"moved root_ispconfig.tar.gz to /tmp/root_ispconfig_".date("m_d_Y__H_i_s", $current_date).".tar.gz","could not move root_ispconfig.tar.gz to /tmp/root_ispconfig_".date("m_d_Y__H_i_s", $current_date).".tar.gz");
  caselog("mv -f home_admispconfig.tar.gz /tmp/home_admispconfig_".date("m_d_Y__H_i_s", $current_date).".tar.gz", $FILE, __LINE__,"moved home_admispconfig.tar.gz to /tmp/home_admispconfig_".date("m_d_Y__H_i_s", $current_date).".tar.gz","could not move home_admispconfig.tar.gz to /tmp/home_admispconfig_".date("m_d_Y__H_i_s", $current_date).".tar.gz");
  caselog("cp -f existing_db.sql /tmp/existing_db_".date("m_d_Y__H_i_s", $current_date).".sql", $FILE, __LINE__,"copied existing_db.sql to /tmp/existing_db_".date("m_d_Y__H_i_s", $current_date).".sql","could not copy existing_db.sql to /tmp/existing_db_".date("m_d_Y__H_i_s", $current_date).".sql");

  @mysql_query("DROP DATABASE ".$new_db);
  @mysql_query("CREATE DATABASE ".$new_db." /*!40100 DEFAULT CHARACTER SET latin1 */");
  mysql_select_db($new_db, $link);
  exec("chmod 444 $sql_file");
  if(empty($db_password)){
    caselog("mysql -h $db_server -u $db_user $new_db < $sql_file &> /dev/null", $FILE, __LINE__,"read in $sql_file","could not read in $sql_file");
    $tables = mysql_list_tables($new_db);

    while(list($table_name) = mysql_fetch_array($tables)){
      if(in_array($table_name, $old_tables_array) && $table_name != "doctype" && $table_name != "sys_dep" && $table_name != "sys_modules" && $table_name != "sys_nodes") mysql_query("DELETE FROM $table_name");
    }
    unset($tables);
    caselog("mysql -f -s -h $db_server -u $db_user $new_db < existing_db.sql &> /dev/null", $FILE, __LINE__,"imported existing_db.sql","could not import existing_db.sql");
  } else {
    caselog("mysql -h $db_server -u $db_user -p$db_password $new_db < $sql_file &> /dev/null", $FILE, __LINE__,"read in $sql_file","could not read in $sql_file");
    $tables = mysql_list_tables($new_db);

    while (list($table_name) = mysql_fetch_array($tables)) {
      if(in_array($table_name, $old_tables_array) && $table_name != "doctype" && $table_name != "sys_dep" && $table_name != "sys_modules" && $table_name != "sys_nodes") mysql_query("DELETE FROM $table_name");
    }
    unset($tables);
    caselog("mysql -f -s -h $db_server -u $db_user -p$db_password $new_db < existing_db.sql &> /dev/null", $FILE, __LINE__,"imported existing_db.sql","could not import existing_db.sql");
  }
  //////////// Nachträge in neuer DB machen /////////////
  $conn = mysql_query("SELECT * FROM sys_user WHERE doc_id > 1");
  while($row = mysql_fetch_array($conn)){
    mysql_query("INSERT INTO sys_nodes (userid,groupid,type,doctype_id,status,modul,doc_id) VALUES ('1','0','a','1','1','sys','".$row["doc_id"]."')");
  }

  $conn = mysql_query("SELECT * FROM isp_firewall WHERE doc_id > 10");
  while($row = mysql_fetch_array($conn)){
    $result = mysql_query("SELECT * FROM sys_nodes WHERE userid = '1' AND groupid = '0' AND type = 'a' AND doctype_id = '1025' AND status = '1' AND doc_id = '".$row["doc_id"]."'");
    $num_rows = @mysql_num_rows($result);
    if(!$num_rows){
      mysql_query("INSERT INTO sys_nodes (userid,groupid,type,doctype_id,status,modul,doc_id) VALUES ('1','0','a','1025','1','','".$row["doc_id"]."')");
      mysql_query("INSERT INTO sys_dep (userid,groupid,parent_doc_id,parent_doctype_id,parent_tree_id,child_doc_id,child_doctype_id,child_tree_id,status) VALUES ('1','0','1','1023','15','".$row["doc_id"]."','1025','".mysql_insert_id()."','1')");
    }
  }

  $conn = mysql_query("SELECT * FROM isp_monitor");
  while($row = mysql_fetch_array($conn)){
    mysql_query("UPDATE isp_monitor SET status = 'u' WHERE doc_id = '".$row["doc_id"]."'");
    $result = mysql_query("SELECT * FROM sys_nodes WHERE userid = '1' AND groupid = '0' AND type = 'a' AND doctype_id = '1024' AND status = '1' AND doc_id = '".$row["doc_id"]."'");
    $num_rows = @mysql_num_rows($result);
    if(!$num_rows){
      mysql_query("INSERT INTO sys_nodes (userid,groupid,type,doctype_id,status,modul,doc_id) VALUES ('1','0','a','1024','1','','".$row["doc_id"]."')");
      mysql_query("INSERT INTO sys_dep (userid,groupid,parent_doc_id,parent_doctype_id,parent_tree_id,child_doc_id,child_doctype_id,child_tree_id,status) VALUES ('1','0','1','1023','15','".$row["doc_id"]."','1024','".mysql_insert_id()."','1')");
    }
  }

  mysql_query("UPDATE sys_user SET modules = CONCAT(modules, ',isp_file') WHERE modules NOT LIKE '%isp_file%'");
  mysql_query("UPDATE sys_user SET modules = CONCAT(modules, ',isp_fakt') WHERE doc_id = '1' AND modules NOT LIKE '%isp_fakt%'");
  mysql_query("UPDATE sys_user SET modules = CONCAT(modules, ',help') WHERE modules NOT LIKE '%help%'");
  mysql_query("UPDATE isp_isp_web SET optionen_directory_index = 'index.html\nindex.htm\nindex.php\nindex.php5\nindex.php4\nindex.php3\nindex.shtml\nindex.cgi\nindex.pl\nindex.jsp\nDefault.htm\ndefault.htm' WHERE optionen_directory_index IS NULL");
  mysql_query("UPDATE isp_server SET server_ftp_typ = 'proftpd' WHERE server_ftp_typ = 'proftp'");
  mysql_query("UPDATE isp_server SET server_ftp_typ = 'vsftpd' WHERE server_ftp_typ = 'vsftp'");
  $conn = mysql_query("SELECT * FROM isp_traffic");
  while($row = mysql_fetch_array($conn)){
    if(empty($row["datum"])){
      list($traffic_monat, $traffic_jahr) = explode("/", $row["monat"]);
      if(substr($traffic_monat,0,1) == "0") $traffic_monat = substr($traffic_monat,1,1);
      $traffic_datum = mktime(0,0,0,1,$traffic_monat,$traffic_jahr);
      mysql_query("UPDATE isp_traffic SET datum = '$traffic_datum' WHERE doc_id = '".$row["doc_id"]."'");
    }
  }

  if($old_version < 1200){
    // Anlegen der Faktura Records
    $sql = "SELECT * FROM isp_nodes, isp_isp_web WHERE isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = isp_isp_web.doctype_id";
    $conn = mysql_query($sql);

    while($web = mysql_fetch_array($conn)){

        $web_id = $web["doc_id"];
        $beschreibung = $web["web_host"].".".$web["web_domain"];

        // Web Record hinzufügen
        $sql = "INSERT INTO isp_fakt_record (web_id,doc_id,doctype_id,typ,notiz) VALUES ('$web_id','$web_id','1013','Web','$beschreibung')";
        mysql_query($sql);
        // Traffic Record hinzufügen
        $sql = "INSERT INTO isp_fakt_record (web_id,typ,notiz) VALUES ('$web_id','Traffic','$beschreibung')";
        mysql_query($sql);

        // User zu Web holen
        $sql = "SELECT * FROM isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = $web_id AND isp_dep.parent_doctype_id = 1013 AND isp_dep.child_doc_id = isp_isp_user.doc_id AND isp_dep.child_doctype_id = isp_isp_user.doctype_id";
        $conn2 = mysql_query($sql);
        while($user = mysql_fetch_array($conn2)) {
                $beschreibung = $user["user_username"];
                $doc_id = $user["doc_id"];
                $sql = "INSERT INTO isp_fakt_record (web_id,doc_id,doctype_id,typ,notiz) VALUES ('$web_id','$doc_id','1014','Email','$beschreibung')";
                mysql_query($sql);
        }
    }
  }
  /*
  if($old_version < 2000){
    $conn3 = mysql_query("SELECT isp_isp_web.doc_id FROM isp_isp_web, isp_nodes WHERE isp_isp_web.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '1013' AND isp_isp_web.status = '' and isp_isp_web.server_id = '1' and isp_nodes.status = '1' LIMIT 0,1");
    while($row = mysql_fetch_array($conn3)){
      mysql_query("UPDATE isp_isp_web SET status = 'u' WHERE doc_id = ".$row["doc_id"]);
    }
  }
  */
  //////////// Nachträge in neuer DB machen ENDE /////////////
  caselog("rm -f $sql_file", $FILE, __LINE__,"deleted $sql_file","could not delete $sql_file");
  caselog("rm -f existing_db.sql", $FILE, __LINE__,"deleted existing_db.sql","could not delete existing_db.sql");
}

$httpd_configuration = rf("httpd2");

list($httpd_root,$server_config_file) = explode("\n",$httpd_configuration);

if(substr($server_config_file,0,1) == "/"){
  $httpd_conf = $server_config_file;
} else {
  $httpd_conf = $httpd_root."/".$server_config_file;
}
$httpd_conf_array = explode("/", $httpd_conf);
$count = sizeof($httpd_conf_array);
$httpd_conf_dir = "";
for($i=0; $i<$count-1; $i++){
  $httpd_conf_dir .= $httpd_conf_array[$i]."/";
}
$httpd_conf_dir = substr($httpd_conf_dir, 0, -1);

if(!empty($server_host)){
  $server_name = $server_host.".".$server_domain;
} else {
  $server_name = $server_domain;
}
if(!isset($url)) $url = $server_ispconfigprotocol."://".$server_name;


$conf = rf("/root/ispconfig/httpd/conf/httpd.conf_".$server_ispconfigprotocol);
$conf = str_replace("{SERVER_NAME}", $server_name, $conf);
wf("/root/ispconfig/httpd/conf/httpd.conf", $conf);

$conf = rf("isp/conf/forward.master");
$conf = str_replace("{PROCMAIL}", $procmail, $conf);
wf("isp/conf/forward.master", $conf);

$conf = rf("isp/conf/named.conf.master");

$conf = str_replace("{PIDFILE}", $dist_bind_pidfile, $conf);
$conf = str_replace("{HINTFILE}", $dist_bind_hintfile, $conf);
$conf = str_replace("{LOCALFILE}", $dist_bind_localfile, $conf);
wf("isp/conf/named.conf.master", $conf);

$conf = rf("ispconfig_server");
$conf = str_replace("{INITDIR}", $dist_init_scripts, $conf);
wf("ispconfig_server", $conf);

$dist_info_add = "
dist_httpd_dir=".$httpd_conf_dir." ##
export dist_httpd_dir

dist_ip=".$ip." ##
export dist_ip
";

af("/root/ispconfig/dist.info", $dist_info_add);
remove_blank_lines("/root/ispconfig/dist.info");

$dist_inc_content = rf("/root/ispconfig/dist.inc.php");
$dist_inc_content = str_replace("?>", "", $dist_inc_content);
$dist_inc_add = '
$dist_httpd_dir = "'.$httpd_conf_dir.'";
$dist_httpd_conf = "'.$httpd_conf.'";
$dist_ip = "'.$ip.'";
?>
';

wf("/root/ispconfig/dist.inc.php", $dist_inc_content.$dist_inc_add);
remove_blank_lines("/root/ispconfig/dist.inc.php");

////////////////////////////////
exec("cp -f uninstall /root/ispconfig");
exec("chmod 700 /root/ispconfig/uninstall");
exec("chown root:".$root_gruppe." /root/ispconfig/uninstall");
//////////////////////////////////

caselog("cp -fr scripts /root/ispconfig", $FILE, __LINE__,"copied directory scripts to /root/ispconfig/","could not copy directory scripts to /root/ispconfig/");
caselog("cp -fr sv /root/ispconfig", $FILE, __LINE__,"copied directory sv to /root/ispconfig/","could not copy directory sv to /root/ispconfig/");
exec("chown root:".$root_gruppe." /root/ispconfig/sv/ispconfig_wconf");
exec("chmod 700 /root/ispconfig/sv/ispconfig_wconf");
caselog("cp -fr isp /root/ispconfig", $FILE, __LINE__,"copied directory isp to /root/ispconfig/","could not copy directory isp to /root/ispconfig/");
caselog("cp -f cronolog /root/ispconfig", $FILE, __LINE__,"copied cronolog to /root/ispconfig/","could not copy cronolog to /root/ispconfig/");
caselog("cp -f cronosplit /root/ispconfig", $FILE, __LINE__,"copied cronosplit to /root/ispconfig/","could not copy cronosplit to /root/ispconfig/");
exec("chmod 755 /root/ispconfig/cronolog");
exec("chmod 755 /root/ispconfig/cronosplit");

mkdirs("/root/ispconfig/standard_cgis");
mkdirs("/root/ispconfig/standard_cgis/cgi-bin");
mkdirs("/root/ispconfig/standard_cgis/web");

caselog("cp -f ispconfig_server $dist_init_scripts", $FILE, __LINE__,"copied ispconfig_server to $dist_init_scripts","could not copy ispconfig_server to $dist_init_scripts");
exec("chown root:$root_gruppe $dist_init_scripts/ispconfig_server");
exec("chmod 700 $dist_init_scripts/ispconfig_server");
rc_edit("ispconfig_server", "2,3,5", "on");

if(!is_dir($pfad)) mkdir($pfad, octdec($directory_mode));

$conf = rf("mailstats/.forward");
$conf = str_replace("{PROCMAIL}", $procmail, $conf);
wf($pfad."/.forward", $conf);

if(is_file('/etc/suphp.conf')) exec("mv /etc/suphp.conf /etc/suphp.conf_".$datum);
$conf = rf("suphp.conf");
$conf = str_replace("{APACHE_USER}", $dist_http_user, $conf);
wf("/etc/suphp.conf", $conf);
if(is_file('/etc/suphp/suphp.conf')){
  exec("mv /etc/suphp/suphp.conf /etc/suphp/suphp.conf_".$datum);
  symlink('/etc/suphp.conf', '/etc/suphp/suphp.conf');
}

exec("cp -f mailstats/.procmailrc $pfad");
mkdirs($pfad."/mailstats");
mkdirs($pfad."/ispconfig");
caselog("cp -fr ispconfig/* $pfad/ispconfig/", $FILE, __LINE__,"copied directory ispconfig to $pfad/ispconfig","could not copy directory ispconfig to $pfad/ispconfig");
caselog("chown -R admispconfig:admispconfig $pfad", $FILE, __LINE__);
exec("rm -fr ispconfig");
exec("chmod -R 755 $pfad");
caselog("chown -R admispconfig:$dist_mysql_group $pfad/ispconfig/backup", $FILE, __LINE__);
caselog("chmod -R 770 $pfad/ispconfig/backup", $FILE, __LINE__);
exec("chmod 700 $pfad/ispconfig/tools/tcpserver/ispconfig_tcpserver");
exec("chown root:$root_gruppe $pfad/ispconfig/tools/tcpserver/ispconfig_tcpserver");

if($dist_smrsh != "" && $dist_mail == "sendmail"){
  if(!is_link($dist_smrsh."/procmail")) phpcaselog(@symlink($procmail, $dist_smrsh."/procmail"), 'create symlink', $FILE, __LINE__);
}

if($install_art == "install"){
  $bin_bash = realpath("/bin/bash");
  $shells = no_comments($dist_shells);
  if(!strstr($shells, $bin_bash)) af($dist_shells, "\n".$bin_bash."\n");
  remove_blank_lines($dist_shells);

  $bin_false = realpath("/bin/false");
  $shells = no_comments($dist_shells);
  if(!strstr($shells, $bin_false)) af($dist_shells, "\n".$bin_false."\n");
  remove_blank_lines($dist_shells);

  #####################
  $sendmail_shell = "/SENDMAIL/ANY/SHELL/\n";
  $shells = no_comments($dist_shells);
  if(!strstr($shells, $sendmail_shell)) af($dist_shells, "\n".$sendmail_shell);
  remove_blank_lines($dist_shells);
  ######################
}

$conf = rf("ip_addresses");
$ip_addresses = explode("\n", $conf);

if($dist_ftp == "proftpd"){
  $default_root = "DefaultRoot ~";
  caselog("cp -f $dist_ftp_conf $dist_ftp_conf.orig", $FILE, __LINE__);
  $proftp = rf($dist_ftp_conf);

  $fp = fopen ($dist_ftp_conf, "w");
  $proftp_array = explode("\n", $proftp);
  foreach($proftp_array as $key => $val){
    if(strstr($val, $default_root)) $val = $default_root;
    fwrite($fp,$val."\n");
  }
  if(!strstr($proftp,$default_root)) fwrite($fp,$default_root."\n");
  fclose($fp);
  if(!strstr($proftp,"Include /etc/proftpd_ispconfig.conf")){
    af($dist_ftp_conf, "\nInclude /etc/proftpd_ispconfig.conf\n");
  }
  if(!is_file("/etc/proftpd_ispconfig.conf")) phpcaselog(touch("/etc/proftpd_ispconfig.conf"), 'create /etc/proftpd_ispconfig.conf', $FILE, __LINE__);
}

if($dist_ftp == "vsftpd"){
  caselog("cp -f ispconfig_tcpserver $dist_init_scripts", $FILE, __LINE__);
  caselog("chown root:$root_gruppe $dist_init_scripts/ispconfig_tcpserver", $FILE, __LINE__);
  caselog("chmod 700 $dist_init_scripts/ispconfig_tcpserver", $FILE, __LINE__);
  rc_edit("ispconfig_tcpserver", "2,3,5", "on");

  foreach($ip_addresses as $ip_address){
    if($ip_address != ""){
      if(!is_file("/etc/vsftpd_".$ip_address.".conf")) exec("cp -f vsftpd.conf /etc/vsftpd_".$ip_address.".conf");
    }
  }
  if($dist_ftp_version == "inetd"){
    caselog("mv -f /etc/inetd.conf /etc/inetd.conf.backup_".date("m_d_Y__H_i_s", $current_date)." &> /dev/null", $FILE, __LINE__);
    exec("cat /etc/inetd.conf.backup_".date("m_d_Y__H_i_s", $current_date)." | grep -v vsftpd > /etc/inetd.conf");
    daemon_init($dist_ftp_version, "restart");
  }
  if($dist_ftp_version == "xinetd"){
    if(is_file("/etc/xinetd.conf")) edit_xinetd_conf("vsftpd");
    if(is_file("/etc/xinetd.d/vsftpd")) caselog("mv -f /etc/xinetd.d/vsftpd /etc/vsftpd_xinetd.backup_".date("m_d_Y__H_i_s", $current_date)." &> /dev/null", $FILE, __LINE__);
    daemon_init($dist_ftp_version, "restart");
  }
  if($dist_ftp_version == "standalone"){
    daemon_init($dist_ftp, "stop");
    rc_edit($dist_ftp, "2,3,5", "off");
  }
  $vsftpd_change = rf("/root/ispconfig/dist.inc.php");
  $vsftpd_change = str_replace("\$dist_ftp_version = \"".$dist_ftp_version."\";", "\$dist_ftp_version = \"ispconfig_tcpserver\";", $vsftpd_change);
  wf("/root/ispconfig/dist.inc.php", $vsftpd_change);

  $vsftpd_change = rf("/root/ispconfig/dist.info");
  $vsftpd_change = str_replace("dist_ftp_version=".$dist_ftp_version." ##", "dist_ftp_version=ispconfig_tcpserver ##", $vsftpd_change);
  wf("/root/ispconfig/dist.info", $vsftpd_change);
  $vsftpd_change = NULL;
  $dist_ftp_version = "ispconfig_tcpserver";
}

if(isset($dist_path_httpd_root) && $dist_path_httpd_root != ""){
  if(!is_dir($dist_path_httpd_root) && !is_link($dist_path_httpd_root)) mkdirs($dist_path_httpd_root);
}

if($install_art == "install"){
  $link = mysql_connect($db_server, $db_user, $db_password)
    or error("Could not connect to MySQL server");
  echo "Connected successfully to MySQL server\n";
  ilog("Connected successfully to MySQL server");
  $db_list = mysql_list_dbs();
  $i = 0;
  $cnt = mysql_num_rows($db_list);
  while ($i < $cnt) {
    if(mysql_db_name($db_list, $i) == $new_db){
      $new_db_exists = 1;
      $i = $cnt;
    }
    $i++;
  }
  if(!$new_db_exists){
    mysql_query("CREATE DATABASE ".$new_db." /*!40100 DEFAULT CHARACTER SET latin1 */");
    mysql_select_db($new_db, $link);
    exec("chmod 444 $sql_file");
    if(empty($db_password)){
      caselog("mysql -h $db_server -u $db_user $new_db < $sql_file", $FILE, __LINE__,"read in $sql_file","could not read in $sql_file");
    } else {
      caselog("mysql -h $db_server -u $db_user -p$db_password $new_db < $sql_file", $FILE, __LINE__,"read in $sql_file","could not read in $sql_file");
    }
    caselog("rm -f $sql_file", $FILE, __LINE__);

    mysql_query("INSERT INTO isp_server_ip VALUES (1, 1, '$ip')");

    foreach($ip_addresses as $ip_address){
      if($ip_address != "127.0.0.1" && $ip_address != $ip && $ip_address != ""){
        mysql_query("INSERT INTO isp_server_ip (server_id, server_ip) VALUES('1', '$ip_address')");
        $server_ip_liste .= $ip_address."\n";
      }
    }
    $server_ip_liste = substr($server_ip_liste,0,-1);
    unlink("ip_addresses");

    if(substr($httpd_conf_dir,-1) == "/") $httpd_conf_dir = substr($httpd_conf_dir,0,-1);
    if(substr($dist_path_httpd_root,-1) == "/") $dist_path_httpd_root = substr($dist_path_httpd_root,0,-1);
    if(substr($dist_bind_dir,-1) == "/") $dist_bind_dir = substr($dist_bind_dir,0,-1);
    if(substr($dist_init_scripts,-1) == "/") $dist_init_scripts = substr($dist_init_scripts,0,-1);
    if(substr($dist_runlevel,-1) == "/") $dist_runlevel = substr($dist_runlevel,0,-1);
    if(substr($dist_smrsh,-1) == "/") $dist_smrsh = substr($dist_smrsh,0,-1);

    if(!is_link($httpd_conf_dir)) $httpd_conf_dir = realpath($httpd_conf_dir);
    if(!is_link($dist_path_httpd_root)) $dist_path_httpd_root = realpath($dist_path_httpd_root);
    if(!is_link($dist_bind_dir)) $dist_bind_dir = realpath($dist_bind_dir);
    if(!is_link($dist_init_scripts)) $dist_init_scripts = realpath($dist_init_scripts);
    if(!is_link($dist_runlevel)) $dist_runlevel = realpath($dist_runlevel);
    if(!is_link($dist_smrsh)) $dist_smrsh = realpath($dist_smrsh);

    mysql_query("INSERT INTO isp_server (doc_id, doctype_id, server_host, server_domain, server_ip, server_netzmaske, server_sprache, server_db_type, server_db_user, server_db_passwort, server_path_httpd_conf, server_path_httpd_root, server_httpd_user, server_httpd_group, server_path_frontpage, server_path_httpd_error, server_name, server_mta, server_sendmail_virtuser_datei, server_sendmail_cw, server_ftp_typ, server_proftpd_conf_datei, server_proftpd_log, server_bind_user, server_bind_group, server_bind_named_conf, server_bind_zonefile_dir, userid_von, groupid_von, passwd_datei, group_datei, server_ipliste, shadow_datei, server_bind_ns1_default, server_bind_ns2_default, server_path_httpd_log, server_soap_ip, server_soap_port, server_soap_encoding, server_admin_email, server_enable_frontpage, server_bind_standard_mx, server_bind_adminmail_default, server_mail_log_save, server_ftp_log_save, server_httpd_suexec, dist, dist_init_scripts, dist_runlevel, dist_smrsh, dist_shells, dist_bind_init_script, dist_bind_pidfile, dist_bind_hintfile, dist_bind_localfile, dist_cron_daemon, dist_cron_tab, dist_mysql_group, dist_httpd_daemon, dist_pop3, dist_pop3_version, dist_ftp_version, dist_httpd_conf, dist_mail_log) VALUES (1, 1010, '$server_host', '$server_domain', '$ip', '255.255.255.0', 'de', 'mysql', '', '', '$httpd_conf_dir', '$dist_path_httpd_root', '$dist_http_user', '$dist_http_group', '/usr/local/frontpage/version5.0/bin/owsadm.exe', '".$dist_path_httpd_root."/error', 'Server 1', '$dist_mail', '$dist_mail_virtusertable', '$dist_mail_local_host_names', '$dist_ftp', '$dist_ftp_conf', '$dist_ftp_log', '$dist_bind_user', '$dist_bind_group', '$dist_bind_conf', '$dist_bind_dir', '10000', '10000', '$dist_passwd', '$dist_group', '$server_ip_liste', '$dist_shadow', '$server_name', '$server_name', '/var/log/httpd/ispconfig_access_log', '', '', '', 'root@localhost', '0', '0', 'root@localhost', '0', '0', '0', '$dist', '$dist_init_scripts', '$dist_runlevel', '$dist_smrsh', '$dist_shells', '$dist_bind_init_script', '$dist_bind_pidfile', '$dist_bind_hintfile', '$dist_bind_localfile', '$dist_cron_daemon', '$dist_cron_tab', '$dist_mysql_group', '$dist_httpd_daemon', '$dist_pop3', '$dist_pop3_version', '$dist_ftp_version', '$httpd_conf', '$dist_mail_log')");


  } else {
    echo "Es ist schon eine Datenbank mit dem Namen ".$new_db." vorhanden!\nThere's already another database with the name ".$new_db."!\n";
    ilog("db ".$new_db." already exists!");
  }
} else {
  $dist_httpd_conf = $httpd_conf;
  $conn = mysql_query("SELECT * FROM isp_server WHERE doc_id = '1'");
  if($row = mysql_fetch_array($conn)){
    foreach($row as $key => $value){
      if((!isset($value) || $value == "") && isset($$key)){
        mysql_query("UPDATE isp_server SET $key = '".$$key."' WHERE doc_id = '1'");
      }
    }
  }
  mysql_query("ALTER TABLE sys_user CHANGE passwort passwort VARCHAR( 255 ), CHANGE pwcl pwcl VARCHAR( 255 )");
}

$conf_datei = $pfad."/ispconfig/lib/config.inc.php";
$conf = rf($conf_datei_temp);

$conf = str_replace("{DB_SERVER}", $db_server, $conf);
$conf = str_replace("{DB_USER}", $db_user, $conf);
$conf = str_replace("{DB_PASSWORD}", $db_password, $conf);
$conf = str_replace("{DB_NAME}", $new_db, $conf);
$conf = str_replace("{URL}", $url, $conf);
$conf = str_replace("{PROTOCOL}", $server_ispconfigprotocol."://", $conf);
$conf = str_replace("{LANG}", $lang, $conf);
$conf = str_replace("{POSTFIX_CONFIG}", $postfix_config, $conf);
$conf = str_replace("{SMTP_RESTART}", $smtp_restart, $conf);
$conf = str_replace("{NETWORK_CONFIG}", $network_config, $conf);
$conf = str_replace("{SUDO_DU_ENABLED}", $sudo_du_enabled, $conf);
$conf = str_replace("{APACHE2_PHP}", $apache2_php, $conf);
$conf = str_replace("{PASSWORD_HASH}", $password_hash, $conf);
$conf = str_replace("{DO_AUTOMATED_BACKUPS}", $do_automated_backups, $conf);
$conf = str_replace("{SSH_CHROOT}", $ssh_chroot, $conf);
$conf = str_replace("{HTTPD_CHECK}", $httpd_check, $conf);
$conf = str_replace("{SALUTATORY_EMAIL_CHARSET}", $salutatory_email_charset, $conf);
$conf = str_replace("{WEBDAV}", $conf_webdav, $conf);
$conf = str_replace("{FORCE_USER_QUOTA}", $conf_force_user_quota, $conf);
$conf = str_replace("{REDIRECT_AFTER_LOGOUT}", $conf_redirect_after_logout, $conf);
$conf = str_replace("{REDIRECT_MAILUSER_AFTER_LOGOUT}", $conf_redirect_mailuser_after_logout, $conf);
$conf = str_replace("{DEC_POINT}", $dec_point, $conf);
$conf = str_replace("{THOUSANDS_SEP}", $thousands_sep, $conf);
$conf = str_replace("{CURRENCY}", $currency, $conf);
if($install_art == "upgrade"){
  if($old_version < 2000){
    $conf = str_replace('$go_info["theme"]["page"]["nav_color"] = "025CCA";', '$go_info["theme"]["page"]["nav_color"] = "E0E0E0";', $conf);
  }
}
wf($conf_datei, $conf);
caselog("chmod 600 $conf_datei", $FILE, __LINE__);
caselog("chown admispconfig:admispconfig $conf_datei", $FILE, __LINE__);

if($install_art == "install"){
  $vhost = "
<Directory /var/www/sharedip>
    Options +Includes -Indexes
    AllowOverride None
    AllowOverride Indexes AuthConfig Limit FileInfo
    Order allow,deny
    Allow from all
    <Files ~ \"^\\.ht\">
    Deny from all
    </Files>
</Directory>

###############ispconfig_log###############
LogFormat \"%v||||%b||||%h %l %u %t \\\"%r\\\" %>s %b \\\"%{Referer}i\\\" \\\"%{User-Agent}i\\\"\" combined_ispconfig
CustomLog \"|/root/ispconfig/cronolog --symlink=/var/log/httpd/ispconfig_access_log /var/log/httpd/ispconfig_access_log_%Y_%m_%d\" combined_ispconfig

<Directory ".$dist_path_httpd_root."/*/web>
    Options +Includes -Indexes
    AllowOverride None
    AllowOverride Indexes AuthConfig Limit FileInfo
    Order allow,deny
    Allow from all
    <Files ~ \"^\\.ht\">
    Deny from all
    </Files>
</Directory>

<Directory ".$dist_path_httpd_root."/*/user/*/web>
    Options +Includes -Indexes
    AllowOverride None
    AllowOverride Indexes AuthConfig Limit FileInfo
    Order allow,deny
    Allow from all
    <Files ~ \"^\\.ht\">
    Deny from all
    </Files>
</Directory>

<Directory ".$dist_path_httpd_root."/*/cgi-bin>
    Options ExecCGI -Indexes
    AllowOverride None
    AllowOverride Indexes AuthConfig Limit FileInfo
    Order allow,deny
    Allow from all
    <Files ~ \"^\\.ht\">
    Deny from all
    </Files>
</Directory>

Include ".$httpd_conf_dir."/vhosts/Vhosts_ispconfig.conf

### AWStats Section ###
Alias /icon \"/home/admispconfig/ispconfig/tools/awstats/wwwroot/icon/\"
### End of AWStats Section ###

";

  caselog("cp -f $httpd_conf $httpd_conf.orig", $FILE, __LINE__);
  af($httpd_conf, $vhost);

  ///////// Auskommentieren: PHP, SSI, CGI ///////////////
  if($includes = find_includes($httpd_conf)){
    $mime_types = trim(shell_exec('httpd -V | awk -F"\"" \'$1==" -D TYPES_CONFIG_FILE="{print $2}\''));
    if(empty($mime_types)) $mime_types = trim(shell_exec('httpd -V | awk -F"\"" \'$1==" -D AP_TYPES_CONFIG_FILE="{print $2}\''));
    if(!empty($mime_types)){
      if(substr($mime_types,0,1) != "/"){
        $mime_types = $httpd_root."/".$mime_types;
      }
      $mime_types = realpath($mime_types);
      if(is_file($mime_types)) $includes[] = $mime_types;
    }
    if($mime_types != '/etc/mime.types'){
      if(is_file('/etc/mime.types')) $includes[] = '/etc/mime.types';
    }
    foreach($includes as $include){
      if(!strstr($include, "Vhosts_ispconfig.conf")){
        exec("cp -f ".$include." ".$include.".".$datum);
        comment_out($include, "AddType application/x-httpd-php,SetOutputFilter,SetInputFilter,AddType text/html .shtml,AddHandler server-parsed .shtml,AddOutputFilter INCLUDES .shtml,AddHandler cgi-script,application/x-httpd-php,application/x-perl,application/x-php,AddHandler php5-script .php,AddType text/html .php,text/x-perl");
      }
    }
  }
  ///////// Auskommentieren ENDE /////////////
}

if($install_art == "upgrade"){
  if($old_version < 2000){
    wf($httpd_conf, str_replace('CustomLog /var/log/httpd/ispconfig_access_log combined_ispconfig', 'CustomLog "|/root/ispconfig/cronolog --symlink=/var/log/httpd/ispconfig_access_log /var/log/httpd/ispconfig_access_log_%Y_%m_%d" combined_ispconfig', rf($httpd_conf)));
  }
}

$vhosts_dir = $httpd_conf_dir."/vhosts";
if(!is_dir($vhosts_dir)) mkdir($vhosts_dir, octdec($directory_mode));
exec("chmod 755 $vhosts_dir");
phpcaselog(touch($vhosts_dir."/Vhosts_ispconfig.conf"), "create ".$vhosts_dir."/Vhosts_ispconfig.conf", $FILE, __LINE__);
if(!is_dir("/var/log/httpd")) mkdir("/var/log/httpd", octdec($directory_mode));
phpcaselog(touch("/var/log/httpd/ispconfig_access_log"), "create /var/log/httpd/ispconfig_access_log", $FILE, __LINE__);
exec("chmod 644 /var/log/httpd/ispconfig_access_log");

//////////// Cron Jobs //////////////////
if($dist == "Trustix30"){
$cron_tsl_file = "/home/fcronisp";
if(is_file($cron_tsl_file)) unlink($cron_tsl_file);
    exec("$dist_cron_tab -l > $cron_tsl_file");
    exec("chmod 777 $cron_tsl_file");
$cron_job_tsl = array('30 00 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/logs.php &> /dev/null','59 23 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/ftp_logs.php &> /dev/null','59 23 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/mail_logs.php &> /dev/null','59 23 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/cleanup.php &> /dev/null','0 4 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/webalizer.php &> /dev/null','0 4 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/awstats.php &> /dev/null','0,30 * * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/check_services.php &> /dev/null','15 3,15 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/quota_msg.php &> /dev/null','40 00 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/traffic.php &> /dev/null','05 02 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/backup.php &> /dev/null');
foreach($cron_job_tsl as $cron_tsl){
    aftsl($cron_tsl_file, "\n".$cron_tsl."\n");
  }
  exec("$dist_cron_tab $cron_tsl_file");
  if(is_file($cron_tsl_file)) unlink($cron_tsl_file);
}
else {
  exec("crontab -u root -l > crontab.txt");
  $existing_cron_jobs = rf('crontab.txt');
  $cron_jobs = array('30 00 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/logs.php &> /dev/null','59 23 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/ftp_logs.php &> /dev/null','59 23 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/mail_logs.php &> /dev/null','59 23 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/cleanup.php &> /dev/null','0 4 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/webalizer.php &> /dev/null','0 4 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/awstats.php &> /dev/null','0,30 * * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/check_services.php &> /dev/null','15 3,15 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/quota_msg.php &> /dev/null','40 00 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/traffic.php &> /dev/null','05 02 * * * /root/ispconfig/php/php /root/ispconfig/scripts/shell/backup.php &> /dev/null');
  foreach($cron_jobs as $cron_job){
    if(!strstr($existing_cron_jobs, $cron_job)){
      af('crontab.txt', "\n".$cron_job."\n");
    }
  }
  wf('crontab.txt', trim(rf('crontab.txt')));
  remove_blank_lines('crontab.txt');
  af('crontab.txt', "\n");
  exec("crontab -u root crontab.txt &> /dev/null");
  unlink('crontab.txt');
}
daemon_init($dist_cron_daemon, "restart");
////////// Cron Jobs ENDE ///////////////

if($install_art == "install"){
  exec("cat $dist_passwd | cut -f1 -d: > $pfad/ispconfig/users");
} else {
  exec("cp -f users $pfad/ispconfig");
}

if($install_art == "install"){
  wf($pfad."/ispconfig/adminmail.txt", "root@localhost");
} else {
  caselog("cp -f adminmail.txt $pfad/ispconfig", $FILE, __LINE__);
}
exec("chown admispconfig:admispconfig ".$pfad."/ispconfig/adminmail.txt");
exec("chmod 644 ".$pfad."/ispconfig/adminmail.txt");

//////////////// POSTFIX //////////////////////
if($dist_mail == "postfix"){
  $pf_main_cf = "/etc/postfix/main.cf";
  caselog("cp -f $pf_main_cf $pf_main_cf.orig", $FILE, __LINE__);
  $postfix_main_cf = no_comments($pf_main_cf);
  if(strstr($postfix_main_cf,"virtual_maps = hash:$dist_mail_virtusertable")){
    ilog("virtusertable entry already in $pf_main_cf");
  } else {
    wf($pf_main_cf, str_replace("virtual_maps", "#virtual_maps", rf($pf_main_cf)));
    af($pf_main_cf, "\nvirtual_maps = hash:$dist_mail_virtusertable\n");
    ilog("created virtusertable entry in $pf_main_cf");
    if(!is_file($dist_mail_virtusertable)) phpcaselog(touch($dist_mail_virtusertable), "create ".$dist_mail_virtusertable, $FILE, __LINE__);
    caselog("postmap $dist_mail_virtusertable", $FILE, __LINE__);
  }
  if(strstr($postfix_main_cf, "mydestination = $dist_mail_local_host_names")){
    ilog("mydestination entry already in $pf_main_cf");
  } else {
    wf($pf_main_cf, str_replace("mydestination", "#mydestination", rf($pf_main_cf)));
    af($pf_main_cf, "\nmydestination = $dist_mail_local_host_names\n");
    ilog("created mydestination entry in $pf_main_cf");
    if(!is_file($dist_mail_local_host_names)) phpcaselog(touch($dist_mail_local_host_names), "create ".$dist_mail_local_host_names, $FILE, __LINE__);
  }
  if(strstr($postfix_main_cf, "virtual_alias_maps")){
    wf($pf_main_cf, str_replace("virtual_alias_maps", "#virtual_alias_maps", rf($pf_main_cf)));
    ilog("commented out virtual_alias_maps entry in $pf_main_cf");
  }
}
/////////////// POSTFIX ENDE //////////////////

//Firewall-Setup
if(!strstr($dist, "freebsd")){
  if(is_dir("/etc/Bastille")) caselog("mv -f /etc/Bastille /etc/Bastille.backup_".date("m_d_Y__H_i_s", $current_date), $FILE, __LINE__);
  @mkdir("/etc/Bastille", octdec($directory_mode));
  if(is_dir("/etc/Bastille.backup_".date("m_d_Y__H_i_s", $current_date)."/firewall.d")) caselog("cp -pfr /etc/Bastille.backup_".date("m_d_Y__H_i_s", $current_date)."/firewall.d /etc/Bastille/", $FILE, __LINE__);
  caselog("cp -f isp/conf/bastille-firewall.cfg.master /etc/Bastille/bastille-firewall.cfg", $FILE, __LINE__);
  caselog("chmod 644 /etc/Bastille/bastille-firewall.cfg", $FILE, __LINE__);
  $conf = rf("/etc/Bastille/bastille-firewall.cfg");
  $conf = str_replace("{DNS_SERVERS}", "", $conf);

  $tcp_public_services = '';
  $udp_public_services = '';
  if($conn = mysql_query("SELECT dienst_port, dienst_typ FROM isp_firewall WHERE dienst_aktiv = 'ja'")){
    while($row = mysql_fetch_array($conn)){
      if($row['dienst_typ'] == 'tcp') $tcp_public_services .= $row['dienst_port'].' ';
      if($row['dienst_typ'] == 'udp') $udp_public_services .= $row['dienst_port'].' ';
    }
    $tcp_public_services = trim($tcp_public_services);
    $udp_public_services = trim($udp_public_services);
  } else {
    $tcp_public_services = '21 22 25 53 80 81 110 443 10000';
    $udp_public_services = '53';
  }
  $conf = str_replace("{TCP_PUBLIC_SERVICES}", $tcp_public_services, $conf);
  $conf = str_replace("{UDP_PUBLIC_SERVICES}", $udp_public_services, $conf);

  wf("/etc/Bastille/bastille-firewall.cfg", $conf);

  if(is_file($dist_init_scripts."/bastille-firewall")) caselog("mv -f $dist_init_scripts/bastille-firewall $dist_init_scripts/bastille-firewall.backup_".date("m_d_Y__H_i_s", $current_date), $FILE, __LINE__);
  caselog("cp -f security/bastille-firewall $dist_init_scripts", $FILE, __LINE__);
  caselog("chmod 700 $dist_init_scripts/bastille-firewall", $FILE, __LINE__);

  if(is_file("/sbin/bastille-ipchains")) caselog("mv -f /sbin/bastille-ipchains /sbin/bastille-ipchains.backup_".date("m_d_Y__H_i_s", $current_date), $FILE, __LINE__);
  caselog("cp -f security/bastille-ipchains /sbin", $FILE, __LINE__);
  caselog("chmod 700 /sbin/bastille-ipchains", $FILE, __LINE__);

  if(is_file("/sbin/bastille-netfilter")) caselog("mv -f /sbin/bastille-netfilter /sbin/bastille-netfilter.backup_".date("m_d_Y__H_i_s", $current_date), $FILE, __LINE__);
  caselog("cp -f security/bastille-netfilter /sbin", $FILE, __LINE__);
  caselog("chmod 700 /sbin/bastille-netfilter", $FILE, __LINE__);

  exec("which ipchains &> /dev/null", $ipchains_location, $ret_val);
  if(!is_file("/sbin/ipchains") && !is_link("/sbin/ipchains") && $ret_val == 0) phpcaselog(@symlink(shell_exec("which ipchains"), "/sbin/ipchains"), 'create symlink', $FILE, __LINE__);
  unset($ipchains_location);
  exec("which iptables &> /dev/null", $iptables_location, $ret_val);
  if(!is_file("/sbin/iptables") && !is_link("/sbin/iptables") && $ret_val == 0) phpcaselog(@symlink(trim(shell_exec("which iptables")), "/sbin/iptables"), 'create symlink', $FILE, __LINE__);
  unset($iptables_location);
} else {
  if(is_file("/etc/rc.d/bastille-firewall")) caselog("mv -f /etc/rc.d/bastille-firewall /etc/rc.d/bastille-firewall.backup_".date("m_d_Y__H_i_s", $current_date), $FILE, __LINE__);
  caselog("cp -f isp/conf/freebsd_firewall.master /etc/rc.d/bastille-firewall", $FILE, __LINE__);
  $conf = rf("/etc/rc.d/bastille-firewall");
  $conf = str_replace("{TCP_PUBLIC_SERVICES_COMMENT}", "", $conf);
  $conf = str_replace("{TCP_PUBLIC_SERVICES}", "21,22,25,53,80,81,110,443,10000", $conf);
  $conf = str_replace("{UDP_PUBLIC_SERVICES_COMMENT}", "", $conf);
  $conf = str_replace("{UDP_PUBLIC_SERVICES}", "53", $conf);
  wf("/etc/rc.d/bastille-firewall", $conf);
  caselog("chmod 700 /etc/rc.d/bastille-firewall", $FILE, __LINE__);
}

//////////// trashscan anlegen ////////////////////
if(!is_file("/home/admispconfig/ispconfig/tools/clamav/bin/trashscan")){
  $conf = rf("isp/conf/trashscan.master");
  $conf = str_replace("{VIRUSADMIN}", "admispconfig@localhost", $conf);
  wf("/home/admispconfig/ispconfig/tools/clamav/bin/trashscan", $conf);
  caselog("chmod 755 /home/admispconfig/ispconfig/tools/clamav/bin/trashscan", $FILE, __LINE__);
  caselog("chown admispconfig:admispconfig /home/admispconfig/ispconfig/tools/clamav/bin/trashscan", $FILE, __LINE__);
}
//////////// trashscan anlegen ENDE ////////////////////

//////////// LOG-FILE ANLEGEN ////////////////
include($pfad."/ispconfig/lib/config.inc.php");
if(!is_file($go_info["server"]["log_file"])) touch($go_info["server"]["log_file"]);
exec("chown admispconfig:admispconfig ".$go_info["server"]["log_file"]);
exec("chmod 644 ".$go_info["server"]["log_file"]);
//////////// LOG-FILE ANLEGEN ENDE ////////////////


///////////////// CREATE CHROOT SSH ENV //////////////////
exec("chmod +x /root/ispconfig/scripts/shell/create_chroot_env.sh");

///////////////// CREATE CHROOT SSH ENV //////////////////

//////////////// CREATE AWStats DIR //////////////////
if($install_art == "install"){
        exec("mkdir /etc/awstats");
        exec("cp -f compile_aps/awstats.shared.conf /etc/awstats/awstats.shared.conf");
        exec("chmod 644 /etc/awstats/awstats.shared.conf");
}
///////////////// CREATE AWStats DIR ENDE //////////////////

exec("pwconv &> /dev/null");
exec("grpconv &> /dev/null");

phpcaselog(unlink("/root/ispconfig/dist.inc.php"), "delete /root/ispconfig/dist.inc.php", $FILE, __LINE__);
if($install_art == "upgrade"){
  touch($pfad."/ispconfig/.run");
}
?>