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

class system{

var $FILE = "/root/ispconfig/scripts/lib/classes/ispconfig_system.lib.php";
var $server_id;
var $server_conf;
var $data;

function system(){
  global $go_info;
  $this->server_id = $go_info["isp"]["server_id"];
  $this->server_conf = $go_info["isp"]["server_conf"];
  $this->server_conf["passwd_datei"] = trim($this->server_conf["passwd_datei"]);
  $this->server_conf["shadow_datei"] = trim($this->server_conf["shadow_datei"]);
  $this->server_conf["group_datei"] = trim($this->server_conf["group_datei"]);
}

function hostname(){
  $dist = $this->server_conf["dist"];

  ob_start();
  passthru("hostname");
  $hostname = ob_get_contents();
  ob_end_clean();
  $hostname = trim($hostname);
  ob_start();
  if(!strstr($dist, "freebsd")){
    passthru("dnsdomainname");
  } else {
    passthru("domainname");
  }
  $domainname = ob_get_contents();
  ob_end_clean();
  $domainname = trim($domainname);
  if($domainname != ""){
    if(!strstr($hostname, $domainname)) $hostname .= ".".$domainname;
  }
  return $hostname;
}

function adduser($user_username, $uid, $gid, $username, $homedir, $shell, $passwort = '*'){
  global $mod;
  if($this->is_user($user_username)){
    return false;
  } else {
          if(trim($user_username) != '') {
                $user_datei = $this->server_conf["passwd_datei"];
            $shadow_datei = $this->server_conf["shadow_datei"];
            $shell = realpath($shell);
            if(trim($passwort) == "") $passwort = '*';
            $new_user = "\n$user_username:x:$uid:$gid:$username:$homedir:$shell\n";
                $mod->log->msg("USER: $new_user");
            $mod->file->af($user_datei, $new_user);
            if($shadow_datei == "/etc/shadow"){
                      $datum = time();
                      $tage = floor($datum/86400);
                      $new_passwd = "\n$user_username:$passwort:$tage:0:99999:7:::\n";
            } else {
                      $new_passwd = "\n$user_username:$passwort:$uid:$gid::0:0:$username:$homedir:$shell\n";
            }
            $mod->file->af($shadow_datei, $new_passwd);

                // TB: leere Zeilen entfernen
                $mod->file->remove_blank_lines($shadow_datei);
                $mod->file->remove_blank_lines($user_datei);
            // TB: user Sortierung deaktiviert
                //$this->order_users_groups();
            if($shadow_datei != "/etc/shadow"){
                      $mod->file->af($shadow_datei, "\n");

                        // TB: leere Zeilen entfernen
                        $mod->file->remove_blank_lines($shadow_datei);

                      $mod->log->caselog("pwd_mkdb $shadow_datei &> /dev/null", $this->FILE, __LINE__);
            }

            return true;
        }
  }
}

function updateuser($user_username, $uid, $gid, $username, $homedir, $shell, $passwort = '*'){
  $this->deluser($user_username);
  $this->adduser($user_username, $uid, $gid, $username, $homedir, $shell, $passwort);
}

function deactivateuser($user_username){
  $passwort = str_rot13($this->getpasswd($user_username));
  $user_attr = $this->get_user_attributes($user_username);
  $uid = $user_attr["uid"];
  $gid = $user_attr["gid"];
  $username = $user_attr["name"];
  $homedir = $user_attr["homedir"];
  $shell = "/dev/null";
  $this->deluser($user_username);
  $this->adduser($user_username, $uid, $gid, $username, $homedir, $shell, $passwort);
}

function deluser($user_username){
  global $mod;
  if($this->is_user($user_username)){
    $user_datei = $this->server_conf["passwd_datei"];
    $shadow_datei = $this->server_conf["shadow_datei"];
    $users = $mod->file->rf($user_datei);
    $lines = explode("\n", $users);
    if(is_array($lines)){
      $num_lines = sizeof($lines);
      for($i=0;$i<$num_lines;$i++){
        if(trim($lines[$i]) != ""){
          list($f1,) = explode(":", $lines[$i]);
          if($f1 != $user_username) $new_lines[] = $lines[$i];
        }
      }
      $new_users = implode("\n", $new_lines);
      $mod->file->wf($user_datei, $new_users);
      unset($new_lines);
      unset($lines);
      unset($new_users);
    }
    $mod->file->remove_blank_lines($user_datei);

    $passwds = $mod->file->rf($shadow_datei);
    $lines = explode("\n", $passwds);
    if(is_array($lines)){
      $num_lines = sizeof($lines);
      for($i=0;$i<$num_lines;$i++){
        if(trim($lines[$i]) != ""){
          list($f1,) = explode(":", $lines[$i]);
          if($f1 != $user_username) $new_lines[] = $lines[$i];
        }
      }
      $new_passwds = implode("\n", $new_lines);
      $mod->file->wf($shadow_datei, $new_passwds);
      unset($new_lines);
      unset($lines);
      unset($new_passwds);
    }
    $mod->file->remove_blank_lines($shadow_datei);

    $group_file = $mod->file->rf($this->server_conf["group_datei"]);
    $group_file_lines = explode("\n", $group_file);
    foreach($group_file_lines as $group_file_line){
      if(trim($group_file_line) != ""){
        list($f1, $f2, $f3, $f4) = explode(":", $group_file_line);
        $group_users = explode(",", str_replace(" ", "", $f4));
        if(in_array($user_username, $group_users)){
          $g_users = array();
          foreach($group_users as $group_user){
            if($group_user != $user_username) $g_users[] = $group_user;
          }
          $f4 = implode(",", $g_users);
        }
        $new_group_file[] = $f1.":".$f2.":".$f3.":".$f4;
      }
    }
    $new_group_file = implode("\n", $new_group_file);
    $mod->file->wf($this->server_conf["group_datei"], $new_group_file);
    // TB: auskommentiert
        //$this->order_users_groups();

    if($shadow_datei != "/etc/shadow"){
      $mod->file->af($shadow_datei, "\n");
      $mod->log->caselog("pwd_mkdb $shadow_datei &> /dev/null", $this->FILE, __LINE__);
    }
    return true;
  } else {
    return false;
  }
}

function addgroup($group, $gid, $members = ''){
  global $mod;
  if($this->is_group($group)){
    return false;
  } else {
    $group_datei = $this->server_conf["group_datei"];
    $shadow_datei = $this->server_conf["shadow_datei"];
    $new_group = "\n$group:x:$gid:$members\n";
    $mod->file->af($group_datei, $new_group);

        // TB: auskommentiert
        //$this->order_users_groups();
    if($shadow_datei != "/etc/shadow"){
      $mod->log->caselog("pwd_mkdb $shadow_datei &> /dev/null", $this->FILE, __LINE__);
    }
    return true;
  }
}

function updategroup($group, $gid, $members = ''){
  $this->delgroup($group);
  $this->addgroup($group, $gid, $members);
}

function delgroup($group){
  global $mod;
  if($this->is_group($group)){
    $group_datei = $this->server_conf["group_datei"];
    $shadow_datei = $this->server_conf["shadow_datei"];
    $groups = $mod->file->rf($group_datei);
    $lines = explode("\n", $groups);
    if(is_array($lines)){
      $num_lines = sizeof($lines);
      for($i=0;$i<$num_lines;$i++){
        if(trim($lines[$i]) != ""){
          list($f1,) = explode(":", $lines[$i]);
          if($f1 != $group) $new_lines[] = $lines[$i];
        }
      }
      $new_groups = implode("\n", $new_lines);
      $mod->file->wf($group_datei, $new_groups);
      unset($new_lines);
      unset($lines);
      unset($new_groups);
    }
        // TB: auskommentiert
    //$this->order_users_groups();
    if($shadow_datei != "/etc/shadow"){
      $mod->log->caselog("pwd_mkdb $shadow_datei &> /dev/null", $this->FILE, __LINE__);
    }
    return true;
  } else {
    return false;
  }
}

function order_users_groups(){
  global $mod;
  $user_datei = $this->server_conf["passwd_datei"];
  $shadow_datei = $this->server_conf["shadow_datei"];
  $group_datei = $this->server_conf["group_datei"];

  $groups = $mod->file->no_comments($group_datei);
  $lines = explode("\n", $groups);
  if(is_array($lines)){
    foreach($lines as $line){
      if(trim($line) != ""){
        list($f1, $f2, $f3, $f4) = explode(":", $line);
        $arr[$f3] = $line;
      }
    }
  }
  ksort($arr);
  reset($arr);
  if($shadow_datei != "/etc/shadow"){
    $mod->file->wf($group_datei, $mod->file->remove_blank_lines(implode("\n", $arr), 0)."\n");
  }else {
    $mod->file->wf($group_datei, $mod->file->remove_blank_lines(implode("\n", $arr), 0));
  }
  unset($arr);

  $users = $mod->file->no_comments($user_datei);
  $lines = explode("\n", $users);
  if(is_array($lines)){
    foreach($lines as $line){
      if(trim($line) != ""){
        list($f1, $f2, $f3,) = explode(":", $line);
        if($f1 != "toor"){
          $arr[$f3] = $line;
        } else {
          $arr[70000] = $line;
        }
      }
    }
  }
  ksort($arr);
  reset($arr);
  $mod->file->wf($user_datei, $mod->file->remove_blank_lines(implode("\n", $arr), 0));
  unset($arr);

  $passwds = $mod->file->no_comments($shadow_datei);
  $lines = explode("\n", $passwds);
  if(is_array($lines)){
    foreach($lines as $line){
      if(trim($line) != ""){
        list($f1, $f2, $f3,) = explode(":", $line);
        if($f1 != "toor"){
          $uid = $this->getuid($f1);
          if(!is_bool($uid)) $arr[$uid] = $line;
        } else {
          $arr[70000] = $line;
        }
      }
    }
  }
  ksort($arr);
  reset($arr);
  $mod->file->wf($shadow_datei, $mod->file->remove_blank_lines(implode("\n", $arr), 0));
  unset($arr);
}

function find_uid_gid($min, $max){
  global $mod;
  if($min < $max && $min >= 0 && $max >= 0 && $min <= 65536 && $max <= 65536 && is_int($min) && is_int($max)){
    for($i=$min;$i<=$max;$i++){
      $uid_arr[$i] = $gid_arr[$i] = 1;
    }
    $user_datei = $this->server_conf["passwd_datei"];
    $group_datei = $this->server_conf["group_datei"];

    $users = $mod->file->no_comments($user_datei);
    $lines = explode("\n", $users);
    if(is_array($lines)){
      foreach($lines as $line){
        if(trim($line) != ""){
          list($f1, $f2, $f3, $f4, $f5, $f6, $f7) = explode(":", $line);
          if($f3 >= $min && $f3 <= $max) unset($uid_arr[$f3]);
        }
      }
      if(!empty($uid_arr)){
        foreach($uid_arr as $key => $val){
          $uids[] = $key;
        }
        $min_uid = min($uids);
        unset($uid_arr);
      } else {
        return false;
      }
    }

    $groups = $mod->file->no_comments($group_datei);
    $lines = explode("\n", $groups);
    if(is_array($lines)){
      foreach($lines as $line){
        if(trim($line) != ""){
          list($f1, $f2, $f3, $f4) = explode(":", $line);
          if($f3 >= $min && $f3 <= $max) unset($gid_arr[$f3]);
        }
      }
      if(!empty($gid_arr)){
        foreach($gid_arr as $key => $val){
          $gids[] = $key;
        }
        $min_gid = min($gids);
        unset($gid_arr);
      } else {
        return false;
      }
    }

    $result = array_intersect($uids, $gids);
    $new_id = (max($result));
    unset($uids);
    unset($gids);
    unset($result);
    if($new_id <= $max){
      return $new_id;
    } else {
      return false;
    }
  } else {
    return false;
  }
}

function is_user($user){
  global $mod;
  $user_datei = $this->server_conf["passwd_datei"];
  $users = $mod->file->no_comments($user_datei);
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
  global $mod;
  $group_datei = $this->server_conf["group_datei"];
  $groups = $mod->file->no_comments($group_datei);
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
  global $mod;
  $group_datei = $this->server_conf["group_datei"];
  $groups = $mod->file->no_comments($group_datei);
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

function get_user_groups($username){
  global $mod;
  $user_groups = array();
  $group_datei = $this->server_conf["group_datei"];
  $groups = $mod->file->no_comments($group_datei);
  $lines = explode("\n", $groups);
  if(is_array($lines)){
    foreach($lines as $line){
      if(trim($line) != ""){
        list($f1, $f2, $f3, $f4) = explode(":", $line);
        if(intval($f3) < intval($this->server_conf["groupid_von"]) && trim($f1) != 'users'){
          $tmp_group_users = explode(',', str_replace(' ', '', $f4));
          if(in_array($username, $tmp_group_users) && trim($f1) != '') $user_groups[] = $f1;
          unset($tmp_group_users);
        }
      }
    }
  }
  if(!empty($user_groups)) return implode(',', $user_groups);
  return '';
}

function getpasswd($user){
  global $mod;
  if($this->is_user($user)){
    $shadow_datei = $this->server_conf["shadow_datei"];
    $passwds = $mod->file->no_comments($shadow_datei);
    $lines = explode("\n", $passwds);
    if(is_array($lines)){
      foreach($lines as $line){
        if(trim($line) != ""){
          list($f1, $f2,) = explode(":", $line);
          if($f1 == $user) return $f2;
        }
      }
    }
  } else {
    return false;
  }
}

function getuid($user){
  global $mod;
  if($this->is_user($user)){
    $user_datei = $this->server_conf["passwd_datei"];
    $users = $mod->file->no_comments($user_datei);
    $lines = explode("\n", $users);
    if(is_array($lines)){
      foreach($lines as $line){
        if(trim($line) != ""){
          list($f1, $f2, $f3,) = explode(":", $line);
          if($f1 == $user) return $f3;
        }
      }
    }
  } else {
    return false;
  }
}

function get_user_attributes($user){
  global $mod;
  if($this->is_user($user)){
    $user_datei = $this->server_conf["passwd_datei"];
    $users = $mod->file->no_comments($user_datei);
    $lines = explode("\n", $users);
    if(is_array($lines)){
      foreach($lines as $line){
        if(trim($line) != ""){
          list($f1, $f2, $f3, $f4, $f5, $f6, $f7) = explode(":", $line);
          if($f1 == $user){
            $user_attr["username"] = $f1;
            $user_attr["x"] = $f2;
            $user_attr["uid"] = $f3;
            $user_attr["gid"] = $f4;
            $user_attr["name"] = $f5;
            $user_attr["homedir"] = $f6;
            $user_attr["shell"] = $f7;
            return $user_attr;
          }
        }
      }
    }
  } else {
    return false;
  }
}

function chown($file, $owner, $group = ''){
  $owner_change = @chown($file, $owner);
  if($group != ""){
    $group_change = @chgrp($file, $group);
  } else {
    $group_change = 1;
  }
  if($owner_change && $group_change){
    return true;
  } else {
    return false;
  }
}

function add_user_to_group($group, $user = 'admispconfig'){
  global $mod;
  $group_file = $mod->file->rf($this->server_conf["group_datei"]);
  $group_file_lines = explode("\n", $group_file);
  foreach($group_file_lines as $group_file_line){
    list($group_name,$group_x,$group_id,$group_users) = explode(":",$group_file_line);
    if($group_name == $group){
      $group_users = explode(",", str_replace(" ", "", $group_users));
      if(!in_array($user, $group_users)){
        $group_users[] = $user;
      }
      $group_users = implode(",", $group_users);
      if(substr($group_users,0,1) == ",") $group_users = substr($group_users,1);
      $group_file_line = $group_name.":".$group_x.":".$group_id.":".$group_users;
    }
    $new_group_file[] = $group_file_line;
  }
  $new_group_file = implode("\n", $new_group_file);
  $mod->file->wf($this->server_conf["group_datei"], $new_group_file);
  $mod->file->remove_blank_lines($this->server_conf["group_datei"]);
  if($this->server_conf["shadow_datei"] != "/etc/shadow"){
    $mod->log->caselog("pwd_mkdb ".$this->server_conf["shadow_datei"]." &> /dev/null", $this->FILE, __LINE__);
  }
}

function usermod($user, $groups){
  global $mod;
  if($this->is_user($user)){
    $groups = explode(",", str_replace(" ", "", $groups));
    $group_file = $mod->file->rf($this->server_conf["group_datei"]);
    $group_file_lines = explode("\n", $group_file);
    foreach($group_file_lines as $group_file_line){
      if(trim($group_file_line) != ""){
        list($f1, $f2, $f3, $f4) = explode(":", $group_file_line);
        $group_users = explode(",", str_replace(" ", "", $f4));
        if(!in_array($f1, $groups)){
          if(in_array($user, $group_users)){
            $g_users = array();
            foreach($group_users as $group_user){
              if($group_user != $user) $g_users[] = $group_user;
            }
            $f4 = implode(",", $g_users);
          }
        } else {
          if(!in_array($user, $group_users)){
            if(trim($group_users[0]) == "") unset($group_users);
            $group_users[] = $user;
          }
          $f4 = implode(",", $group_users);
        }
        $new_group_file[] = $f1.":".$f2.":".$f3.":".$f4;
      }
    }
    $new_group_file = implode("\n", $new_group_file);
    $mod->file->wf($this->server_conf["group_datei"], $new_group_file);
    $mod->file->remove_blank_lines($this->server_conf["group_datei"]);
    if($this->server_conf["shadow_datei"] != "/etc/shadow"){
      $mod->log->caselog("pwd_mkdb ".$this->server_conf["shadow_datei"]." &> /dev/null", $this->FILE, __LINE__);
    }
    return true;
  } else {
    return false;
  }
}

function rc_edit($service, $rl, $action){
  // $action = "on|off";
  global $mod;
  $dist_init_scripts = $mod->system->server_conf["dist_init_scripts"];
  $dist_runlevel = $mod->system->server_conf["dist_runlevel"];
  $dist = $mod->system->server_conf["dist"];
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

function grep($content, $string, $params = ''){
  global $mod;
  // params: i, v, w
  $content = $mod->file->unix_nl($content);
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
          if(!$mod->string->is_word($string, $line, 'i')) $find[] = $line;
        } else {
          if($mod->string->is_word($string, $line, 'i')) $find[] = $line;
        }
      } else {
        if(strstr($params, 'v')){
          if(!$mod->string->is_word($string, $line)) $find[] = $line;
        } else {
          if($mod->string->is_word($string, $line)) $find[] = $line;
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

function cut($content, $field, $delimiter = ':'){
  global $mod;
  $content = $mod->file->unix_nl($content);
  $lines = explode("\n", $content);
  foreach($lines as $line){
    $elms = explode($delimiter, $line);
    $find[] = $elms[($field-1)];
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

function cat($file){
  global $mod;
  return $mod->file->rf($file);
}

function daemon_init($daemon, $action){
  // $action = start|stop|restart|reload
  global $mod;
  $dist = $this->server_conf["dist"];
  $dist_init_scripts = $this->server_conf["dist_init_scripts"];
  if(!strstr($dist, "freebsd")){
    $mod->log->caselog("$dist_init_scripts/$daemon $action &> /dev/null", $this->FILE, __LINE__);
  } else {
    if(is_file($dist_init_scripts."/".$daemon.".sh") || is_link($dist_init_scripts."/".$daemon.".sh")){
      if($action == "start" || $action == "stop"){
        $mod->log->caselog($dist_init_scripts."/".$daemon.".sh ".$action." &> /dev/null", $this->FILE, __LINE__);
      } else {
        $mod->log->caselog($dist_init_scripts."/".$daemon.".sh stop &> /dev/null", $this->FILE, __LINE__);
        sleep(3);
        $mod->log->caselog($dist_init_scripts."/".$daemon.".sh start &> /dev/null", $this->FILE, __LINE__);
      }
    } else {
      if(is_file($dist_init_scripts."/".$daemon) || is_link($dist_init_scripts."/".$daemon)){
        if($action == "start" || $action == "stop"){
          $mod->log->caselog($dist_init_scripts."/".$daemon." ".$action." &> /dev/null", $this->FILE, __LINE__);
        } else {
          $mod->log->caselog($dist_init_scripts."/".$daemon." stop &> /dev/null", $this->FILE, __LINE__);
          sleep(3);
          $mod->log->caselog($dist_init_scripts."/".$daemon." start &> /dev/null", $this->FILE, __LINE__);
        }
      } else {
        if(is_file("/etc/rc.d/".$daemon) || is_link("/etc/rc.d/".$daemon)){
          if($action == "start" || $action == "stop"){
            $mod->log->caselog("/etc/rc.d/".$daemon." ".$action." &> /dev/null", $this->FILE, __LINE__);
          } else {
            $mod->log->caselog("/etc/rc.d/".$daemon." stop &> /dev/null", $this->FILE, __LINE__);
            sleep(3);
            $mod->log->caselog("/etc/rc.d/".$daemon." start &> /dev/null", $this->FILE, __LINE__);
          }
        }
      }
    }
  }
}

function netmask($netmask){
  list($f1,$f2,$f3,$f4) = explode(".", trim($netmask));
  $bin = str_pad(decbin($f1),8,"0",STR_PAD_LEFT).str_pad(decbin($f2),8,"0",STR_PAD_LEFT).str_pad(decbin($f3),8,"0",STR_PAD_LEFT).str_pad(decbin($f4),8,"0",STR_PAD_LEFT);
  $parts = explode("0", $bin);
  $bin = str_pad($parts[0], 32, "0", STR_PAD_RIGHT);
  $bin = wordwrap($bin, 8, ".", 1);
  list($f1,$f2,$f3,$f4) = explode(".", trim($bin));
  return bindec($f1).".".bindec($f2).".".bindec($f3).".".bindec($f4);
}

function binary_netmask($netmask){
  list($f1,$f2,$f3,$f4) = explode(".", trim($netmask));
  $bin = str_pad(decbin($f1),8,"0",STR_PAD_LEFT).str_pad(decbin($f2),8,"0",STR_PAD_LEFT).str_pad(decbin($f3),8,"0",STR_PAD_LEFT).str_pad(decbin($f4),8,"0",STR_PAD_LEFT);
  $parts = explode("0", $bin);
  return substr_count($parts[0], "1");
}

function network($ip, $netmask){
  $netmask = $this->netmask($netmask);
  list($f1,$f2,$f3,$f4) = explode(".", $netmask);
  $netmask_bin = str_pad(decbin($f1),8,"0",STR_PAD_LEFT).str_pad(decbin($f2),8,"0",STR_PAD_LEFT).str_pad(decbin($f3),8,"0",STR_PAD_LEFT).str_pad(decbin($f4),8,"0",STR_PAD_LEFT);
  list($f1,$f2,$f3,$f4) = explode(".", $ip);
  $ip_bin = str_pad(decbin($f1),8,"0",STR_PAD_LEFT).str_pad(decbin($f2),8,"0",STR_PAD_LEFT).str_pad(decbin($f3),8,"0",STR_PAD_LEFT).str_pad(decbin($f4),8,"0",STR_PAD_LEFT);
  for($i=0;$i<32;$i++){
    $network_bin .= substr($netmask_bin,$i,1) * substr($ip_bin,$i,1);
  }
  $network_bin = wordwrap($network_bin, 8, ".", 1);
  list($f1,$f2,$f3,$f4) = explode(".", trim($network_bin));
  return bindec($f1).".".bindec($f2).".".bindec($f3).".".bindec($f4);
}

function broadcast($ip, $netmask){
  $netmask = $this->netmask($netmask);
  $binary_netmask = $this->binary_netmask($netmask);
  list($f1,$f2,$f3,$f4) = explode(".", $ip);
  $ip_bin = str_pad(decbin($f1),8,"0",STR_PAD_LEFT).str_pad(decbin($f2),8,"0",STR_PAD_LEFT).str_pad(decbin($f3),8,"0",STR_PAD_LEFT).str_pad(decbin($f4),8,"0",STR_PAD_LEFT);
  $broadcast_bin = str_pad(substr($ip_bin, 0, $binary_netmask),32,"1",STR_PAD_RIGHT);
  $broadcast_bin = wordwrap($broadcast_bin, 8, ".", 1);
  list($f1,$f2,$f3,$f4) = explode(".", trim($broadcast_bin));
  return bindec($f1).".".bindec($f2).".".bindec($f3).".".bindec($f4);
}

function network_info(){
  $dist = $this->server_conf["dist"];
  ob_start();
  passthru("ifconfig");
  $output = ob_get_contents();
  ob_end_clean();
  $lines = explode("\n", $output);
  foreach($lines as $line){
    $elms = explode(" ", $line);
    if(trim($elms[0]) != "" && substr($elms[0],0,1) != "\t"){
      $elms[0] = trim($elms[0]);
      if(strstr($dist, "freebsd")) $elms[0] = substr($elms[0],0,-1);
      $interfaces[] = $elms[0];
    }
  }
  if(!empty($interfaces)){
    foreach($interfaces as $interface){
      ob_start();
      if(!strstr($dist, "freebsd")){
        passthru("ifconfig ".$interface." | grep -iw 'inet' | cut -f2 -d: | cut -f1 -d' '");
      }else {
        passthru("ifconfig ".$interface." | grep -iw 'inet' | grep -iv 'inet6' | cut -f2 -d' '");
      }
      $output = trim(ob_get_contents());
      ob_end_clean();
      if($output != ""){
        $ifconfig["INTERFACE"][$interface] = $output;
        $ifconfig["IP"][$output] = $interface;
      }
    }
    if(!empty($ifconfig)){
      return $ifconfig;
    } else {
      return false;
    }
  } else {
    return false;
  }
}

function network_config(){
  $ifconfig = $this->network_info();
  if($ifconfig){
    $main_interface = $ifconfig["IP"][$this->server_conf["server_ip"]];
    if(strstr($main_interface, ":")){
      $parts = explode(":", $main_interface);
      $main_interface = trim($parts[0]);
    }
    if($main_interface != ""){
      $ips = $this->data["isp_server_ip"];
      if(!empty($ips)){
        foreach($ips as $ip){
          if(!isset($ifconfig["IP"][$ip["server_ip"]])){
            $to_set[] = $ip["server_ip"];
          } else {
            unset($ifconfig["IP"][$ip["server_ip"]]);
          }
        }
        if(!empty($ifconfig["IP"])){
          foreach($ifconfig["IP"] as $key => $val){
            if(!strstr($val, "lo") && !strstr($val, "lp") && strstr($val, $main_interface)){
              exec("ifconfig ".$val." down &> /dev/null");
              unset($ifconfig["INTERFACE"][$val]);
            }
          }
        }
        if(!empty($to_set)){
         foreach($to_set as $to){
           $i = 0;
           while($i >= 0){
             if(isset($ifconfig["INTERFACE"][$main_interface.":".$i])){
               $i++;
             } else {
               $new_interface = $main_interface.":".$i;
               $i = -1;
             }
           }
           exec("ifconfig ".$new_interface." ".$to." netmask ".$this->server_conf["server_netzmaske"]." up &> /dev/null");
           $ifconfig["INTERFACE"][$new_interface] = $to;
          }
        }
      }
    }
  }
}

function quota_dirs(){
  global $mod;
  $content = $mod->file->unix_nl($mod->file->no_comments("/etc/fstab"));
  $lines = explode("\n", $content);
  foreach($lines as $line){
    $line = trim($line);
    if($line != ""){
      $elms = explode("\t", $line);
      foreach($elms as $elm){
        if(trim($elm) != "") $f[] = $elm;
      }
      if(!empty($f) && stristr($f[3], "userquota") && stristr($f[3], "groupquota")){
        $q_dirs[] = trim($f[1]);
      }
      unset($f);
    }
  }
  if(!empty($q_dirs)){
    return $q_dirs;
  } else {
    return false;
  }
}

function make_trashscan(){
  global $mod;
  //trashscan erstellen
  // Template �ffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "trashscan.master"));

  if(!isset($this->server_conf["virusadmin"]) || trim($this->server_conf["virusadmin"]) == "") $this->server_conf["virusadmin"] = "admispconfig@localhost";
  if(substr($this->server_conf["virusadmin"],0,1) == "#"){
    $notify = "no";
  } else {
    $notify = "yes";
  }

  // Variablen zuweisen
  $mod->tpl->assign( array(VIRUSADMIN => $this->server_conf["virusadmin"],
                           NOTIFICATION => $notify));

  $mod->tpl->parse(TABLE, table);

  $trashscan_text = $mod->tpl->fetch();

  $datei = "/home/admispconfig/ispconfig/tools/clamav/bin/trashscan";
  $mod->file->wf($datei, $trashscan_text);

  exec("chown admispconfig:admispconfig $datei &> /dev/null");
  exec("chmod 755 $datei");
}

function get_time(){
  $addr = "http://www.ispconfig.org/";
  $timeout = 1;
  $url_parts = parse_url($addr);
  $path = $url_parts["path"];
  $port = 80;
  $urlHandle = @fsockopen($url_parts["host"], $port, $errno, $errstr, $timeout);
  if ($urlHandle){
    socket_set_timeout($urlHandle, $timeout);

    $urlString = "GET $path HTTP/1.0\r\nHost: ".$url_parts["host"]."\r\nConnection: Keep-Alive\r\nUser-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n";
    if ($user) $urlString .= "Authorization: Basic ".base64_encode("$user:$pass")."\r\n";
    $urlString .= "\r\n";
    fputs($urlHandle, $urlString);

    $month["Jan"] = "01";
    $month["Feb"] = "02";
    $month["Mar"] = "03";
    $month["Apr"] = "04";
    $month["May"] = "05";
    $month["Jun"] = "06";
    $month["Jul"] = "07";
    $month["Aug"] = "08";
    $month["Sep"] = "09";
    $month["Oct"] = "10";
    $month["Nov"] = "11";
    $month["Dec"] = "12";
    $c = 0;
    $l = 0;
    $startzeit = time();
    while(!feof($urlHandle) && $c < 2 && $l == 0){
      $line = trim(fgets($urlHandle,128));
      $response .= $line;
      $c = time() - $startzeit;
      if($line == "" || substr($line, 0, 5) == "Date:") $l += 1; // nur den Header auslesen
      if(substr($line, 0, 5) == "Date:"){
        $parts = explode(" ", $line);
        $tag = $parts[2];
        $monat = $month[$parts[3]];
        $jahr = $parts[4];
        list($stunde, $minute, $sekunde) = explode(":", $parts[5]);
        $timestamp = mktime($stunde,$minute,$sekunde,$monat,$tag,$jahr);
      }
    }

    @fclose($urlHandle);

    return $timestamp;
  } else {
    @fclose($urlHandle);
    return false;
  }
}

}
?>