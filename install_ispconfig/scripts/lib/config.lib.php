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

var $FILE = "/root/ispconfig/scripts/lib/config.lib.php";
var $directory_mode = "0755";
var $web_doctype_id = 1013;
var $user_doctype_id = 1014;
var $domain_doctype_id = 1015;
var $dns_doctype_id = 1016;
var $a_record_doctype_id = 1018;
var $cname_record_doctype_id = 1019;
var $mx_record_doctype_id = 1020;
var $dienste_doctype_id = 1023;
var $monitor_doctype_id = 1024;
var $firewall_doctype_id = 1025;
var $slave_doctype_id = 1028;
var $datenbank_doctype_id = 1029;
var $spf_record_doctype_id = 1031;
var $list_doctype_id = 1033;
var $vhost_conf;
var $ftp_conf;
var $apache_user;

//Constructor
function isp_web() {
  global $mod;

  $this->vhost_conf = $mod->system->server_conf["server_path_httpd_conf"]."/".'vhosts'."/".'Vhosts_ispconfig.conf';
  if($mod->system->server_conf["server_ftp_typ"] == "proftpd"){
    $this->ftp_conf = "/etc/proftpd_ispconfig.conf";
  } else {
    $this->ftp_conf = "";
  }
  $this->apache_user = $this->apache_user();
}

function TYPO3_install() {
  include("/home/admispconfig/ispconfig/lib/config.inc.php");
  $go_info["isp"]["httpd"]["use_old_conf_on_errors"] = 1; // if httpd syntax check gives back errors, use old, working configuration

//////////////// DO NOT EDIT BELOW !!! //////////////////
  $go_info["isp"]["server_root"] = "/root/ispconfig";
  $go_info["isp"]["include_root"] = $go_info["isp"]["server_root"] . $go_info["server"]["dir_trenner"] ."scripts".$go_info["server"]["dir_trenner"]."lib";
  $go_info["isp"]["classes_root"] = $go_info["isp"]["include_root"] . $go_info["server"]["dir_trenner"] ."classes";
  $go_info["isp"]["server_id"] = 1;

/**************************************
* Server Einstellungen
* V1.0 ISPConfig SERVER Modules
***************************************/
  $link = @mysql_connect($go_info["server"]["db_host"], $go_info["server"]["db_user"], $go_info["server"]["db_password"])
  or die("Could not connect to MySQL server!");
  mysql_select_db($go_info["server"]["db_name"]);
  $server_params = mysql_query("SELECT * FROM isp_isp_web_package WHERE pending = 1");

  $res = mysql_fetch_array($server_params);
  if (!is_array($res)) exit;
  $result = mysql_query("SELECT * from isp_server");
  $server = mysql_fetch_assoc ( $result );
  $httpd_root = $server["server_path_httpd_root"];
  $typo3_path = $server["typo3_script_repository"];
  unset($server);


// clear database
  $typo3db = @mysql_connect($res['arg1'], $res['arg2'], $res['arg3'])
  or die("Could not connect to TYPO3 MySQL server!");
  mysql_select_db($res['arg4']);
  $sql = "SHOW TABLES";
  $result = mysql_query($sql);
   
  if (!$result) {
    echo "DB Error, could not list tables\n";
    echo 'MySQL Error: ' . mysql_error();
    exit;
  }
  while ($row = mysql_fetch_row($result)) {
//  echo "Table: {$row[0]}\n";
    $table[] = $row[0];
  }
  foreach ($table as $t) {
    $sql = "drop table ".$t;
    mysql_query($sql);
  }
// clear directory
  $webpath = $httpd_root.'/web'.$res['arg7'].'/web/';

  if ($handle = opendir($webpath)) {
//    echo "Directory handle: $handle\n";
//    echo "Files:\n";
    while (false !== ($file = readdir($handle))) {
    //  echo "$file\n";
      $files[] = $file;
    }
    foreach ($files as $f) {
      if (($f != ".") and ($f != "..") and ($f != "error") and ($f != "stats")) {
//        echo "File: $f\n";
        shell_exec('rm -rf '.$webpath.$f); 
      }
    }
  }

// load new database content
  $dbfile = $typo3_path.$res['arg8'].'/mysql.dump';
  if (is_file($dbfile)) {
    $output = shell_exec('mysql -u'.$res['arg2'].' -p'.$res['arg3'].' '.$res['arg4'].' < '.$dbfile.'');
    //echo "<pre>$output</pre>";
  }
// copy directory content
  $TYPO3dir = $typo3_path.$res['arg8'].'/dummy/';
  if (is_dir($TYPO3dir)) {
    shell_exec('cp -a '.$TYPO3dir.'* '.$webpath);
  }
// copy TYPO3 source
  $TYPO3src = $typo3_path.$res['arg8'].'/typo3_src-'.$res['arg8'].'/';
  if (is_dir($TYPO3src)) {
    shell_exec('cp -a '.$TYPO3src.' '.$webpath);
  }
// Set ownership and permissions
  shell_exec('chown -R www-data.web'.$res['arg7'].' '.$webpath);

// set install password
  $localconf = fopen($webpath.'typo3conf/localconf.php', 'r+');
  fseek($localconf, -2, SEEK_END);
  fwrite($localconf, '$TYPO3_CONF_VARS[\'BE\'][\'installToolPassword\'] = \''.md5($res['arg5']).'\';
');
  fwrite($localconf, '$TYPO3_CONF_VARS[\'SYS\'][\'encryptionKey\'] = \''.md5(uniqid(rand(),true)).'\';
');
  fwrite($localconf, '$typo_db_username = '.$res['arg2'].';  //  Modified or inserted by TYPO3 Install Tool.
');
  fwrite($localconf, '$typo_db_password = '.$res['arg3'].';  //  Modified or inserted by TYPO3 Install Tool.
');
  fwrite($localconf, '$typo_db_host = '.$res['arg1'].';  //  Modified or inserted by TYPO3 Install Tool.
');
  fwrite($localconf, '$typo_db = '.$res['arg4'].';  //  Modified or inserted by TYPO3 Install Tool.
');
  fwrite($localconf, '
?>');
  fclose($localconf);

// set admin password
  $typo3db = @mysql_connect($res['arg1'], $res['arg2'], $res['arg3'])
  or die("Could not connect to TYPO3 MySQL server!");
  mysql_select_db($res['arg4']);
  $sql = 'update be_users set password=\''.md5($res['arg6']).'\' where uid=1;';
  mysql_query($sql);

// set pending = 0, because we are finished
  $link = @mysql_connect($go_info["server"]["db_host"], $go_info["server"]["db_user"], $go_info["server"]["db_password"])
  or die("Could not connect to MySQL server!");
  mysql_select_db($go_info["server"]["db_name"]);

  $sql = 'update isp_isp_web_package set pending = 0 where doc_id = '.$res['doc_id'].';';
  mysql_query($sql);
}


function web_insert($doc_id, $doctype_id, $server_id) {
  global $go_info, $mod;
  // Daten des Webs aus DB holen
  $web = $mod->system->data["isp_isp_web"][$doc_id];
  if(empty($web)) $mod->log->ext_log("query result empty", 2, $this->FILE, __LINE__);
  // Verzeichnisse erzeugen
  $web_path = $this->make_docroot($doc_id,$web["web_host"],$web["web_domain"],$web["web_speicher"],0);

  //SSL einrichten
  if($web["web_ssl"] && (!empty($web["ssl_action"]))){
    $this->make_ssl($doc_id);
  }

  //Status zurücksetzen
  $mod->db->query("update isp_isp_web SET status = '' where doc_id = '$doc_id'");
  $mod->system->data["isp_isp_web"][$doc_id]["status"] = "";

  //Diskquota setzen
  $web_speicher = intval($web["web_speicher"]);

  if($web_speicher > 0){
    $blocks_soft = $web_speicher * 1024;
    $blocks_hard = $blocks_soft + 1024;
  } else {
    $blocks_soft = $blocks_hard = 0;
  }

  $group = "web".$doc_id;
  if(!strstr($mod->system->server_conf["dist"], "freebsd")){
    $mod->log->caselog("setquota -g $group $blocks_soft $blocks_hard 0 0 -a &> /dev/null", $this->FILE, __LINE__);
    $mod->log->caselog("setquota -T -g $group 604800 604800 -a &> /dev/null", $this->FILE, __LINE__);
  } else {
    if($q_dirs = $mod->system->quota_dirs()){
      if(!empty($q_dirs)){
        foreach($q_dirs as $q_dir){
          $mod->log->caselog("setquota -g -f ".$q_dir." -bh".$blocks_hard." -bs".$blocks_soft." ".$group." &> /dev/null", $this->FILE, __LINE__);
        }
      }
    }
  }


  //MySQL-User anlegen
  $datenbanken = $mod->db->queryAllRecords("SELECT isp_isp_datenbank.datenbankname, isp_isp_datenbank.datenbankuser, isp_isp_datenbank.db_passwort FROM isp_isp_datenbank, isp_nodes WHERE isp_isp_datenbank.web_id = '$doc_id' AND isp_isp_datenbank.status = 'n' AND isp_nodes.doc_id = isp_isp_datenbank.doc_id AND isp_nodes.doctype_id = '".$this->datenbank_doctype_id."' AND isp_nodes.status = '1'");
  $db_server = $go_info["server"]["db_host"];
  $db_user = $go_info["server"]["db_user"];
  $db_password = $go_info["server"]["db_password"];

  if($web["web_mysql"]){
    $link = mysql_connect($db_server, $db_user, $db_password)
    or die($mod->log->ext_log("Could not connect", 2, $this->FILE, __LINE__));
    $mod->log->ext_log("Connected successfully", 1, $this->FILE, __LINE__);
    mysql_select_db("mysql")
    or die($mod->log->ext_log("Could not select database", 2, $this->FILE, __LINE__));

    $priv_fields = array();
    $priv_result = mysql_query("SHOW COLUMNS FROM db");
    if ($priv_result) {
      if (mysql_num_rows($priv_result) > 0) {
        while ($priv_row = mysql_fetch_assoc($priv_result)) {
          if(substr($priv_row["Field"],-5) == '_priv') $priv_fields[$priv_row["Field"]] = 'Y';
        }
      }
    }
    if(!empty($priv_fields)){
      $priv_field_names = '';
      $priv_field_values = '';
      foreach($priv_fields as $priv_k => $priv_v){
        $priv_field_names .= "`$priv_k`, ";
        $priv_field_values .= "'$priv_v', ";
      }
      $priv_field_names = substr($priv_field_names,0,-2);
      $priv_field_values = substr($priv_field_values,0,-2);
    } else {
      $priv_field_names = '`Select_priv`, `Insert_priv`, `Update_priv`, `Delete_priv`, `Create_priv`, `Drop_priv`, `Grant_priv`, `References_priv`, `Index_priv`, `Alter_priv`';
      $priv_field_values = "'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y'";
    }

    if(!empty($datenbanken)){
      foreach($datenbanken as $datenbank){
############################
    mysql_select_db("mysql")
    or die($mod->log->ext_log("Could not select database", 2, $this->FILE, __LINE__));
    $new_db = $datenbank["datenbankname"];
    $mysql_user = $datenbank["datenbankuser"];
    $mysql_passwort = substr($datenbank["db_passwort"],5);

    //Existiert DB?
    $db_list = mysql_list_dbs();
    $i = 0;
    $new_db_exists = 0;
    $cnt = mysql_num_rows($db_list);
    while ($i < $cnt) {
      if(mysql_db_name($db_list, $i) == $new_db){
        $new_db_exists = 1;
        $i = $cnt;
      }
      $i++;
    }
    //neue DB anlegen, falls sie nicht existiert u. $mysql_user u. $mysql_passwort nicht leer
    if(!$new_db_exists){
      if((!empty($mysql_user)) && (!empty($mysql_passwort))){
        @mysql_query("CREATE DATABASE ".$new_db);

        $sql = "INSERT INTO `user` (`Host`, `User`, `Password`, `Select_priv`, `Insert_priv`, `Update_priv`, `Delete_priv`, `Create_priv`, `Drop_priv`, `Reload_priv`, `Shutdown_priv`, `Process_priv`, `File_priv`, `Grant_priv`, `References_priv`, `Index_priv`, `Alter_priv`) VALUES ('localhost', '$mysql_user', '$mysql_passwort', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N')";
        mysql_query($sql);

        $sql = "INSERT INTO `db` (`Host`, `Db`, `User`, $priv_field_names) VALUES ('localhost', '$new_db', '$mysql_user', $priv_field_values)";
        mysql_query($sql);

        if($go_info["server"]["db_host"] != 'localhost' && $go_info["server"]["db_host"] != '127.0.0.1'){
          $ips = $mod->system->server_conf['ips'];
          if(!empty($ips)){
            foreach($ips as $ip){
              $sql = "INSERT INTO `user` (`Host`, `User`, `Password`, `Select_priv`, `Insert_priv`, `Update_priv`, `Delete_priv`, `Create_priv`, `Drop_priv`, `Reload_priv`, `Shutdown_priv`, `Process_priv`, `File_priv`, `Grant_priv`, `References_priv`, `Index_priv`, `Alter_priv`) VALUES ('$ip', '$mysql_user', '$mysql_passwort', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N')";
              mysql_query($sql);

              $sql = "INSERT INTO `db` (`Host`, `Db`, `User`, $priv_field_names) VALUES ('$ip', '$new_db', '$mysql_user', $priv_field_values)";
              mysql_query($sql);
            }
          }
        }

        if($datenbank["remote_access"]){
          $sql = "INSERT INTO `user` (`Host`, `User`, `Password`, `Select_priv`, `Insert_priv`, `Update_priv`, `Delete_priv`, `Create_priv`, `Drop_priv`, `Reload_priv`, `Shutdown_priv`, `Process_priv`, `File_priv`, `Grant_priv`, `References_priv`, `Index_priv`, `Alter_priv`) VALUES ('%', '$mysql_user', '$mysql_passwort', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N')";
          mysql_query($sql);

          $sql = "INSERT INTO `db` (`Host`, `Db`, `User`, $priv_field_names) VALUES ('%', '$new_db', '$mysql_user', $priv_field_values)";
          mysql_query($sql);
        }

        if(is_file("/root/ispconfig/scripts/".$new_db.".sql")){
          if(!empty($db_password)){
            $mod->log->caselog("mysql -h ".$db_server." -u ".$db_user." -p".$db_password." ".$new_db." < /root/ispconfig/scripts/".$new_db.".sql", $this->FILE, __LINE__);
          } else {
            $mod->log->caselog("mysql -h ".$db_server." -u ".$db_user." ".$new_db." < /root/ispconfig/scripts/".$new_db.".sql", $this->FILE, __LINE__);
          }
          $mod->log->caselog("rm -f /root/ispconfig/scripts/".$new_db.".sql", $this->FILE, __LINE__);
        }

      }
     }
     $mod->db->query("UPDATE isp_isp_datenbank SET status = '' WHERE doc_id = '".$datenbank["doc_id"]."'");
     ############################
    }
  }

    $result = mysql_query('FLUSH PRIVILEGES'); //Änderungen wirksam werden lassen
    if($result){
      $mod->log->ext_log("MySQL FLUSH PRIVILEGES", 1, $this->FILE, __LINE__);
    } else {
      $mod->log->ext_log("MySQL: could not FLUSH PRIVILEGES", 2, $this->FILE, __LINE__);
    }

  }

  // Eintrag aus del_status entfernen, wenn vorhanden
  $mod->db->query("DELETE FROM del_status WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
}

function web_update($doc_id,$doctype_id,$server_id) {
  global $go_info, $mod;
  // Daten des Webs aus DB holen
  $web = $mod->system->data["isp_isp_web"][$doc_id];
  if(empty($web)) $mod->log->ext_log("query result empty", 2, $this->FILE, __LINE__);

  // Verzeichnisse erzeugen
  $web_path = $this->make_docroot($doc_id,$web["web_host"],$web["web_domain"],$web["web_speicher"],1);

  //SSL einrichten
  if($web["web_ssl"] && (!empty($web["ssl_action"]))){
    $this->make_ssl($doc_id);
  }
  //SSL-Web: IP-Adresse ändern -> SSL-Web mit IP existiert schon -> Delete Certificate ermöglichen
  if(!$web["web_ssl"] && ($web["ssl_action"] == "delete")){
    $this->make_ssl($doc_id);
  }

  //Status zurücksetzen
  $mod->db->query("update isp_isp_web SET status = '' where doc_id = '$doc_id'");
  $mod->system->data["isp_isp_web"][$doc_id]["status"] = "";

  //Diskquota setzen
  $web_speicher = intval($web["web_speicher"]);
  if($web_speicher > 0){
    $blocks_soft = $web_speicher * 1024;
    $blocks_hard = $blocks_soft + 1024;
  } else {
    $blocks_soft = $blocks_hard = 0;
  }
  $group = "web".$doc_id;
  if(!strstr($mod->system->server_conf["dist"], "freebsd")){
    $mod->log->caselog("setquota -g $group $blocks_soft $blocks_hard 0 0 -a &> /dev/null", $this->FILE, __LINE__);
    $mod->log->caselog("setquota -T -g $group 604800 604800 -a &> /dev/null", $this->FILE, __LINE__);
  } else {
    if($q_dirs = $mod->system->quota_dirs()){
      if(!empty($q_dirs)){
        foreach($q_dirs as $q_dir){
          $mod->log->caselog("setquota -g -f ".$q_dir." -bh".$blocks_hard." -bs".$blocks_soft." ".$group." &> /dev/null", $this->FILE, __LINE__);
        }
      }
    }
  }


  //MySQL-User ändern
  $datenbanken = $mod->db->queryAllRecords("SELECT * FROM isp_isp_datenbank WHERE web_id = '$doc_id' AND (status = 'n' OR status = 'u' OR status = 'd')");
  $db_server = $go_info["server"]["db_host"];
  $db_user = $go_info["server"]["db_user"];
  $db_password = $go_info["server"]["db_password"];
  $link = mysql_connect($db_server, $db_user, $db_password)
  or die($mod->log->ext_log("Could not connect", 2, $this->FILE, __LINE__));
  $mod->log->ext_log("Connected successfully", 1, $this->FILE, __LINE__);
  mysql_select_db("mysql")
  or die($mod->log->ext_log("Could not select database", 2, $this->FILE, __LINE__));

  $priv_fields = array();
  $priv_result = mysql_query("SHOW COLUMNS FROM db");
  if ($priv_result) {
    if (mysql_num_rows($priv_result) > 0) {
      while ($priv_row = mysql_fetch_assoc($priv_result)) {
        if(substr($priv_row["Field"],-5) == '_priv') $priv_fields[$priv_row["Field"]] = 'Y';
      }
    }
  }
  if(!empty($priv_fields)){
    $priv_field_names = '';
    $priv_field_values = '';
    foreach($priv_fields as $priv_k => $priv_v){
      $priv_field_names .= "`$priv_k`, ";
      $priv_field_values .= "'$priv_v', ";
    }
    $priv_field_names = substr($priv_field_names,0,-2);
    $priv_field_values = substr($priv_field_values,0,-2);
  } else {
    $priv_field_names = '`Select_priv`, `Insert_priv`, `Update_priv`, `Delete_priv`, `Create_priv`, `Drop_priv`, `Grant_priv`, `References_priv`, `Index_priv`, `Alter_priv`';
    $priv_field_values = "'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y'";
  }

  if(!empty($datenbanken)){
    foreach($datenbanken as $datenbank){
###############################
  mysql_select_db("mysql")
  or die($mod->log->ext_log("Could not select database", 2, $this->FILE, __LINE__));
  $new_db = $datenbank["datenbankname"];
  $mysql_user = $datenbank["datenbankuser"];
  $mysql_passwort = substr($datenbank["db_passwort"],5);

  //Existiert DB?
  $db_list = mysql_list_dbs();
  $i = 0;
  $new_db_exists = 0;
  $cnt = mysql_num_rows($db_list);
  while ($i < $cnt) {
      if(mysql_db_name($db_list, $i) == $new_db){
        $new_db_exists = 1;
        $i = $cnt;
      }
      $i++;
  }
  //DB anlegen, falls nicht schon vorhanden
  if($web["web_mysql"] && ($datenbank["status"] == "n" || $datenbank["status"] == "u")){
    if(!$new_db_exists){
      if((!empty($mysql_user)) && (!empty($mysql_passwort))){
        @mysql_query("CREATE DATABASE ".$new_db);

        $sql = "INSERT INTO `user` (`Host`, `User`, `Password`, `Select_priv`, `Insert_priv`, `Update_priv`, `Delete_priv`, `Create_priv`, `Drop_priv`, `Reload_priv`, `Shutdown_priv`, `Process_priv`, `File_priv`, `Grant_priv`, `References_priv`, `Index_priv`, `Alter_priv`) VALUES ('localhost', '$mysql_user', '$mysql_passwort', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N')";
        mysql_query($sql);

        $sql = "INSERT INTO `db` (`Host`, `Db`, `User`, $priv_field_names) VALUES ('localhost', '$new_db', '$mysql_user', $priv_field_values)";
        mysql_query($sql);

        if($go_info["server"]["db_host"] != 'localhost' && $go_info["server"]["db_host"] != '127.0.0.1'){
          $ips = $mod->system->server_conf['ips'];
          if(!empty($ips)){
            foreach($ips as $ip){
              $sql = "INSERT INTO `user` (`Host`, `User`, `Password`, `Select_priv`, `Insert_priv`, `Update_priv`, `Delete_priv`, `Create_priv`, `Drop_priv`, `Reload_priv`, `Shutdown_priv`, `Process_priv`, `File_priv`, `Grant_priv`, `References_priv`, `Index_priv`, `Alter_priv`) VALUES ('$ip', '$mysql_user', '$mysql_passwort', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N')";
              mysql_query($sql);

              $sql = "INSERT INTO `db` (`Host`, `Db`, `User`, $priv_field_names) VALUES ('$ip', '$new_db', '$mysql_user', $priv_field_values)";
              mysql_query($sql);
            }
          }
        }

        if($datenbank["remote_access"]){
          $sql = "INSERT INTO `user` (`Host`, `User`, `Password`, `Select_priv`, `Insert_priv`, `Update_priv`, `Delete_priv`, `Create_priv`, `Drop_priv`, `Reload_priv`, `Shutdown_priv`, `Process_priv`, `File_priv`, `Grant_priv`, `References_priv`, `Index_priv`, `Alter_priv`) VALUES ('%', '$mysql_user', '$mysql_passwort', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N')";
          mysql_query($sql);

          $sql = "INSERT INTO `db` (`Host`, `Db`, `User`, $priv_field_names) VALUES ('%', '$new_db', '$mysql_user', $priv_field_values)";
          mysql_query($sql);
        }
      }
    } else {
      //User updaten
      $sql = "UPDATE `user` SET `Password` = '$mysql_passwort' WHERE `User` = '$mysql_user' AND `Host` = 'localhost'";
      mysql_query($sql);

      if($go_info["server"]["db_host"] != 'localhost' && $go_info["server"]["db_host"] != '127.0.0.1'){
        $ips = $mod->system->server_conf['ips'];
        if(!empty($ips)){
          foreach($ips as $ip){
            $sql = "UPDATE `user` SET `Password` = '$mysql_passwort' WHERE `User` = '$mysql_user' AND `Host` = '$ip'";
            mysql_query($sql);
          }
        }
      }

      if($datenbank["remote_access"]){
        $sql = "SELECT * FROM `user` WHERE `User` = '$mysql_user' AND `Host` = '%'";
        $conn = mysql_query($sql);
        if(!mysql_fetch_array($conn)){

          $sql = "INSERT INTO `user` (`Host`, `User`, `Password`, `Select_priv`, `Insert_priv`, `Update_priv`, `Delete_priv`, `Create_priv`, `Drop_priv`, `Reload_priv`, `Shutdown_priv`, `Process_priv`, `File_priv`, `Grant_priv`, `References_priv`, `Index_priv`, `Alter_priv`) VALUES ('%', '$mysql_user', '$mysql_passwort', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N')";
          mysql_query($sql);

          $sql = "INSERT INTO `db` (`Host`, `Db`, `User`, $priv_field_names) VALUES ('%', '$new_db', '$mysql_user', $priv_field_values)";
          mysql_query($sql);
        } else {
          $sql = "UPDATE `user` SET `Password` = '$mysql_passwort' WHERE `User` = '$mysql_user' AND `Host` = '%'";
          mysql_query($sql);
        }
      } else {
        $sql = "DELETE FROM `user` WHERE `Host` = '%' AND `User` = '$mysql_user'";
        mysql_query($sql);

        $sql = "DELETE FROM `db` WHERE `Host` = '%' AND `Db` = '$new_db' AND `User` = '$mysql_user'";
        mysql_query($sql);
      }
    }

  } else {
    //DB u. DB-User löschen
    if($new_db_exists){
      @mysql_query("DROP DATABASE ".$new_db);
      $sql = "DELETE FROM `user` WHERE `Host` = 'localhost' AND `User` = '$mysql_user'";
      mysql_query($sql);

      $sql = "DELETE FROM `db` WHERE `Host` = 'localhost' AND `Db` = '$new_db' AND `User` = '$mysql_user'";
      mysql_query($sql);

      if($go_info["server"]["db_host"] != 'localhost' && $go_info["server"]["db_host"] != '127.0.0.1'){
        $ips = $mod->system->server_conf['ips'];
        if(!empty($ips)){
          foreach($ips as $ip){
            $sql = "DELETE FROM `user` WHERE `Host` = '$ip' AND `User` = '$mysql_user'";
            mysql_query($sql);

            $sql = "DELETE FROM `db` WHERE `Host` = '$ip' AND `Db` = '$new_db' AND `User` = '$mysql_user'";
            mysql_query($sql);
          }
        }
      }

      $sql = "DELETE FROM `user` WHERE `Host` = '%' AND `User` = '$mysql_user'";
      mysql_query($sql);

      $sql = "DELETE FROM `db` WHERE `Host` = '%' AND `Db` = '$new_db' AND `User` = '$mysql_user'";
      mysql_query($sql);
  }
  }
###############################
      $mod->db->query("UPDATE isp_isp_datenbank SET status = '' WHERE doc_id = '".$datenbank["doc_id"]."'");
    }
    $result = mysql_query('FLUSH PRIVILEGES');
    if($result){
      $mod->log->ext_log("MySQL FLUSH PRIVILEGES", 1, $this->FILE, __LINE__);
    } else {
      $mod->log->ext_log("MySQL: could not FLUSH PRIVILEGES", 2, $this->FILE, __LINE__);
    }
  }


  // Alle User des Web mit Status = u setzen (wenn z.B. Shell-Access gewährt wird)
    $sql = "SELECT * from isp_dep, isp_isp_user where isp_dep.parent_doc_id = $doc_id
    and isp_dep.parent_doctype_id = $doctype_id and isp_dep.child_doc_id = isp_isp_user.doc_id and isp_dep.child_doctype_id = ".$this->user_doctype_id."";

    $users = $mod->db->queryAllRecords($sql);
    foreach($users as $user) {
        $mod->db->query("UPDATE isp_isp_user SET status = 'u' WHERE doc_id = ".$user["doc_id"]." AND status != 'd' AND status != 'n'");
    }
    unset($users);

  //gibt es Admin-User für das Web? Wenn er gelöscht wurde, evtl. vorh. Autoresponder-Dateien löschen
  $sql = "SELECT * FROM isp_nodes, isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = '".$doc_id."' AND isp_dep.parent_doctype_id = '".$this->web_doctype_id."' AND isp_dep.child_doc_id = isp_isp_user.doc_id AND isp_dep.child_doctype_id = '".$this->user_doctype_id."' AND isp_isp_user.user_admin = '1' AND isp_nodes.doc_id = isp_isp_user.doc_id AND isp_nodes.doctype_id = '".$this->user_doctype_id."' AND isp_nodes.status = '1'";

    $user = $mod->db->queryOneRecord($sql);
    if(empty($user)){
     exec("rm -fr $web_path/.forward");
     exec("rm -fr $web_path/.procmailrc");
     exec("rm -fr $web_path/.vacation.cache");
     $apache_user = $this->apache_user;
     // alten admin herausfinden
     $old_admin_uid = fileowner($web_path);
     exec("chown $apache_user $web_path &> /dev/null");
     //exec("chown $apache_user $web_path/cgi-bin &> /dev/null");
     exec("chown -R --from=$old_admin_uid $apache_user $web_path/cgi-bin &> /dev/null");
     exec("chown $apache_user $web_path/log &> /dev/null");
     exec("chown $apache_user $web_path/ssl &> /dev/null");
     exec("chown $apache_user $web_path/user &> /dev/null");
     //exec("chown $apache_user $web_path/web &> /dev/null");
     exec("chown -R --from=$old_admin_uid $apache_user $web_path/web &> /dev/null");
     //exec("chown $apache_user $web_path/web/error &> /dev/null");
     $admin_user = $this->apache_user;
    } else {
     $admin_user = $user['user_username'];
    }

    // gesperrte Webs wieder aktivieren, wenn gewünscht
    if($web['web_traffic_status'] == 1){
      exec("chown ".$admin_user." ".$web_path." &> /dev/null");
      exec("chmod 755 ".$web_path." &> /dev/null");
    }
    unset($user);
}

function web_delete($doc_id,$doctype_id,$server_id) {
  global $go_info, $mod;
  // Daten des Webs aus DB holen
  $web = $mod->system->data["isp_isp_web"][$doc_id];
  if(empty($web)) $mod->log->ext_log("query result empty", 2, $this->FILE, __LINE__);

  if($web["web_host"] == ""){
    $web_path_realname = $mod->system->server_conf["server_path_httpd_root"] ."/". $web["web_domain"];
  } else {
    $web_path_realname = $mod->system->server_conf["server_path_httpd_root"] ."/". $web["web_host"] . "." . $web["web_domain"];
  }

/*
  //Verzeichnisse löschen
  $web_path = $mod->system->server_conf["server_path_httpd_root"] ."/". "web" . $doc_id;
  $mod->log->caselog("cp -fr $web_path /root/ispconfig/scripts", $this->FILE, __LINE__); //Backup erstellen
  exec("cd /root/ispconfig/scripts; tar -pczf web$doc_id.tar.gz web$doc_id"); //Backup
  exec("rm -fr /root/ispconfig/scripts/web$doc_id"); //Backup
  $mod->log->caselog("rm -fr $web_path_realname", $this->FILE, __LINE__);
  $mod->log->caselog("rm -fr $web_path", $this->FILE, __LINE__);
*/

  $mod->db->query("update isp_isp_web SET status = '' where doc_id = '$doc_id'");
  $mod->system->data["isp_isp_web"][$doc_id]["status"] = "";

  //MySQL-User löschen
  $datenbanken = $mod->db->queryAllRecords("SELECT * FROM isp_isp_datenbank WHERE web_id = '$doc_id'");
  $db_server = $go_info["server"]["db_host"];
  $db_user = $go_info["server"]["db_user"];
  $db_password = $go_info["server"]["db_password"];
  if($web["web_mysql"]){
    $link = mysql_connect($db_server, $db_user, $db_password)
    or die($mod->log->ext_log("Could not connect", 2, $this->FILE, __LINE__));
    $mod->log->ext_log("Connected successfully", 1, $this->FILE, __LINE__);
    mysql_select_db("mysql")
    or die($mod->log->ext_log("Could not select database", 2, $this->FILE, __LINE__));

    if(!empty($datenbanken)){
      foreach($datenbanken as $datenbank){
###################################
    mysql_select_db("mysql")
    or die($mod->log->ext_log("Could not select database", 2, $this->FILE, __LINE__));
    $new_db = $datenbank["datenbankname"];
    $mysql_user = $datenbank["datenbankuser"];
    $db_list = mysql_list_dbs();
    $i = 0;
    $new_db_exists = 0;
    $cnt = mysql_num_rows($db_list);
    while ($i < $cnt) {
      if(mysql_db_name($db_list, $i) == $new_db){
        $new_db_exists = 1;
        $i = $cnt;
      }
      $i++;
    }
    //DB löschen
    if($new_db_exists){
    //DB-Backup
    // $mod->log->caselog("mysqldump -h $db_server -u $db_user -p$db_password -c --add-drop-table --add-locks --all --quick --lock-tables $new_db >/root/ispconfig/scripts/$new_db.sql", $this->FILE, __LINE__);
        exec("mysqldump -h $db_server -u $db_user -p$db_password -c --add-drop-table --add-locks --all --quick --lock-tables $new_db >/root/ispconfig/scripts/$new_db.sql");
    @mysql_query("DROP DATABASE ".$new_db);
    }

    $sql = "DELETE from `db` WHERE `Db` = '$new_db' AND `User` = '$mysql_user' AND `Host` = 'localhost'";
    mysql_query($sql);

    $sql = "DELETE from `user` WHERE `User` = '$mysql_user' AND `Host` = 'localhost'";
    mysql_query($sql);

    if($go_info["server"]["db_host"] != 'localhost' && $go_info["server"]["db_host"] != '127.0.0.1'){
      $ips = $mod->system->server_conf['ips'];
      if(!empty($ips)){
        foreach($ips as $ip){
          $sql = "DELETE from `db` WHERE `Db` = '$new_db' AND `User` = '$mysql_user' AND `Host` = '$ip'";
          mysql_query($sql);

          $sql = "DELETE from `user` WHERE `User` = '$mysql_user' AND `Host` = '$ip'";
          mysql_query($sql);
        }
      }
    }

    $sql = "DELETE from `db` WHERE `Db` = '$new_db' AND `User` = '$mysql_user' AND `Host` = '%'";
    mysql_query($sql);

    $sql = "DELETE from `user` WHERE `User` = '$mysql_user' AND `Host` = '%'";
    mysql_query($sql);
###########################
      $mod->db->query("UPDATE isp_isp_datenbank SET status = '' WHERE doc_id = '".$datenbank["doc_id"]."'");
    }
  }

    $result = mysql_query('FLUSH PRIVILEGES');
    if($result){
      $mod->log->ext_log("MySQL FLUSH PRIVILEGES", 1, $this->FILE, __LINE__);
    } else {
      $mod->log->ext_log("MySQL: could not FLUSH PRIVILEGES", 2, $this->FILE, __LINE__);
    }
  }
}

////////////////////////////////////////////////////////////////////

function user_insert($doc_id, $doctype_id) {
  global $go_info, $mod;
  $dist = $mod->system->server_conf["dist"];
  $user = $mod->system->data["isp_isp_user"][$doc_id];
  if(empty($user)) $mod->log->ext_log("query result empty", 2, $this->FILE, __LINE__);
  // doc_id des Webs bestimmen
  $sql = "SELECT * FROM isp_dep WHERE child_doc_id = '$doc_id' AND child_doctype_id = '".$this->user_doctype_id."'";
  $web_dep = $mod->db->queryOneRecord($sql);
  $web_doc_id = $web_dep["parent_doc_id"];
  $web = $mod->system->data["isp_isp_web"][$web_doc_id];
  if(empty($web)) $mod->log->ext_log("query result empty", 2, $this->FILE, __LINE__);

  // user zum System hinzufügen
  $userid = $mod->system->server_conf["userid_von"] + $user["doc_id"];
  $user_name = $user["user_name"];
  $user_username = $user["user_username"];
  $user_speicher = intval($user["user_speicher"]);
  if($user["user_shell"] && $web["web_shell"]){
    $shell = "/bin/bash"; //Shell u. FTP
  } else {
    if($web["web_ftp"]){
      $shell = "/bin/false"; //nur FTP
    } else {
      $shell = "/dev/null"; //weder Shell noch FTP
    }
  }
  if($user_speicher > 0){
    $blocks_soft = $user_speicher * 1024;
    $blocks_hard = $blocks_soft + 1024;
  } else {
    $blocks_soft = $blocks_hard = 0;
  }
  if($mod->system->is_user($user_username)){
    $passwort = str_rot13($mod->system->getpasswd($user_username));
    $mod->system->deluser($user_username);
  } else {
    $passwort = substr($user["user_passwort"],5);
  }
  $web_path = $mod->system->server_conf["server_path_httpd_root"]."/web".$web_doc_id;
  $gid_web = $mod->system->server_conf["groupid_von"] + $web_doc_id;

  if($go_info["server"]["ssh_chroot"] == 1) {
          $chroot_addpath = "/./";
  } else {
          $chroot_addpath = "";
  }


  if($user["user_admin"]){
    $mod->system->adduser($user_username, $userid, $gid_web, $user_name, $web_path.$chroot_addpath, $shell, $passwort);
  } else {
    $mod->system->adduser($user_username, $userid, $gid_web, $user_name, $web_path."/user/".$user_username.$chroot_addpath, $shell, $passwort);
  }

/*
  //Verzeichnisse aus Papierkorb wiederherstellen
  if(is_file("/root/ispconfig/scripts/".$user_username.".tar.gz")){
    $mod->log->caselog("tar xvfz $user_username.tar.gz", $this->FILE, __LINE__);
    $mod->log->caselog("mv $user_username $web_path/user/ &> /dev/null", $this->FILE, __LINE__);
    $mod->log->caselog("rm -f $user_username.tar.gz", $this->FILE, __LINE__);
    $mod->log->caselog("rm -fr $user_username", $this->FILE, __LINE__);
  }
*/

  // Gehört User einem Reseller oder dem admin?
  if($reseller = $mod->db->queryOneRecord("SELECT isp_isp_reseller.user_standard_index FROM isp_nodes, isp_isp_reseller WHERE isp_nodes.doc_id = $doc_id AND isp_nodes.doctype_id = '".$doctype_id."' AND isp_nodes.groupid = isp_isp_reseller.reseller_group")){
    $user_standard_index_page = trim($reseller["user_standard_index"]);
  } else {
    $user_standard_index_page = trim($mod->system->server_conf["user_standard_index"]);
  }

  //Verzeichnisse erzeugen
  $directory_mode = octdec($this->directory_mode);
  if(!is_dir($web_path."/user/".$user_username)) mkdir($web_path."/user/".$user_username, $directory_mode);
  if(!is_dir($web_path."/user/".$user_username."/web")) mkdir($web_path."/user/".$user_username."/web", $directory_mode);
  if(!is_file($web_path."/user/".$user_username."/web/index.html")){
    if($user_standard_index_page == ""){
      $mod->log->caselog("cp -fr /root/ispconfig/isp/user_standard_index.html_".$go_info["server"]["lang"]." ".$web_path."/user/$user_username/web/index.html", $this->FILE, __LINE__); //Standard-Index-Seite einfügen
      $inhalt = $mod->file->rf($web_path."/user/".$user_username."/web/index.html");
      $inhalt = str_replace("{USER_USERNAME}", $user_username, $inhalt);
    } else {
      if($web["web_host"] == "") {
        $FQDN = $web["web_domain"];
      } else {
        $FQDN = $web["web_host"].".".$web["web_domain"];
      }
      $inhalt = str_replace("%%%FQDN%%%", $FQDN, $user_standard_index_page);
      unset($FQDN);
      $inhalt = str_replace("%%%USER%%%", $user_username, $inhalt);
    }
    $mod->file->wf($web_path."/user/".$user_username."/web/index.html", $inhalt);
  }
  exec("chown -R ".$user_username.":web".$web_doc_id." ".$web_path."/user/".$user_username." &> /dev/null");
  exec("chmod -R 775 $web_path/user/$user_username");
  exec("chmod 755 $web_path/user/$user_username");

  //wenn User Admin-User des Webs, Owner des Webs ändern, aber nicht Owner der User-Verzeichnisse!
  if($user["user_admin"]){
    //exec("usermod -G web".$web_doc_id." ".$user_username."");
    // alten admin herausfinden
    $old_admin_uid = fileowner($web_path);
    $mod->system->usermod($user_username, "web".$web_doc_id);
    exec("chown $user_username $web_path &> /dev/null");
    //exec("chown $user_username $web_path/cgi-bin &> /dev/null");
    exec("chown -R --from=$old_admin_uid $user_username $web_path/cgi-bin &> /dev/null");
    exec("chown $user_username $web_path/log &> /dev/null");
    exec("chown $user_username $web_path/ssl &> /dev/null");
    exec("chown $user_username $web_path/user &> /dev/null");
    //exec("chown $user_username $web_path/web &> /dev/null");
    exec("chown -R --from=$old_admin_uid $user_username $web_path/web &> /dev/null");
    //exec("chown $user_username $web_path/web/error &> /dev/null");
    //exec("chown $user_username $web_path/web/error/* &> /dev/null");
    exec("chown -R $user_username $web_path/log/* &> /dev/null");
  } else {
    //exec("usermod -G users ".$user_username."");
    $mod->system->usermod($user_username, "users");
  }

  //Passwort in ISPConfig-DB löschen
  $mod->db->query("UPDATE isp_isp_user SET user_passwort = '' where doc_id = '$doc_id'");
  $mod->system->data["isp_isp_user"]["user_passwort"] = '';

  // User-Mail-Datei anlegen, sonst evtl. Fehler bei Autoresponder
  if(!$mod->system->server_conf["use_maildir"]){
    if(!is_file("/var/spool/mail/".$user_username) && $user_username != ''){
      $mod->log->phpcaselog(touch("/var/spool/mail/".$user_username), "create /var/spool/mail/".$user_username, $this->FILE, __LINE__);
      $mod->log->caselog("chown $user_username:mail /var/spool/mail/$user_username &> /dev/null", $this->FILE, __LINE__);
      $mod->log->caselog("chmod 600 /var/spool/mail/$user_username", $this->FILE, __LINE__);
    }
  }

  // Diskquota setzen
  if(!strstr($mod->system->server_conf["dist"], "freebsd")){
    $mod->log->caselog("setquota -u $user_username $blocks_soft $blocks_hard 0 0 -a &> /dev/null", $this->FILE, __LINE__);
    $mod->log->caselog("setquota -T -u $user_username 604800 604800 -a &> /dev/null", $this->FILE, __LINE__);
  } else {
    if($q_dirs = $mod->system->quota_dirs()){
      if(!empty($q_dirs)){
        foreach($q_dirs as $q_dir){
          $mod->log->caselog("setquota -u -f ".$q_dir." -bh".$blocks_hard." -bs".$blocks_soft." ".$user_username." &> /dev/null", $this->FILE, __LINE__);
        }
      }
    }
  }
  // virtuser.conf updaten

  // Mailquota, Autoresponder etc. einrichten
  $mod->procmail->make_forward($doc_id);
  $mod->procmail->make_procmailrc($doc_id);
  $mod->procmail->make_recipes($doc_id);

  // wenn User Admin-User eines FP-Webs, Frontpage-Update aufrufen
  if($user["user_admin"] && $web["web_frontpage"] && is_file($mod->system->server_conf["server_path_frontpage"]) && !empty($web["optionen_frontpage_passwort"])){
    if($web["web_host"] == "") {
        $fp_servername = $web["web_domain"];
      } else {
        $fp_servername = $web["web_host"].".".$web["web_domain"];
      }
    $mod->log->caselog($mod->system->server_conf["server_path_frontpage"]." -o install -u admin -pw ".$web["optionen_frontpage_passwort"]." -p 80 -m ".$fp_servername." -s ".$this->vhost_conf." -xu ".$user["user_username"]." -xg web".$web["doc_id"]." &> /dev/null", $this->FILE, __LINE__);
  }

  //Status zurücksetzen
  $mod->db->query("update isp_isp_user SET status = '' where doc_id = '$doc_id'");
  $mod->system->data["isp_isp_user"][$doc_id]["status"] = '';

  // Eintrag aus del_status entfernen, wenn vorhanden
  $mod->db->query("DELETE FROM del_status WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");

  // Chroot enviroment erstellen
  if($go_info["server"]["ssh_chroot"] == 1) {
          exec("/root/ispconfig/scripts/shell/create_chroot_env.sh $user_username");
  }



}

function user_update($doc_id, $doctype_id) {
  global $mod,$go_info;
  $dist = $mod->system->server_conf["dist"];
  $user = $mod->system->data["isp_isp_user"][$doc_id];
  if(empty($user)) $mod->log->ext_log("query result empty", 2, $this->FILE, __LINE__);
  $sql = "SELECT * FROM isp_dep WHERE child_doc_id = '$doc_id' AND child_doctype_id = '".$this->user_doctype_id."'";

  // doc_id des Webs bestimmen
  $web_dep = $mod->db->queryOneRecord($sql);
  $web_doc_id = $web_dep["parent_doc_id"];
  $web = $mod->system->data["isp_isp_web"][$web_doc_id];
  if(empty($web)) $mod->log->ext_log("query result empty", 2, $this->FILE, __LINE__);

  // user zum System hinzufügen
  $userid = $mod->system->server_conf["userid_von"] + $user["doc_id"];
  $user_name = $user["user_name"];
  $user_username = $user["user_username"];
  $all_user_groups = $mod->system->get_user_groups($user_username);
  $user_speicher = intval($user["user_speicher"]);
  if($user["user_shell"] && $web["web_shell"]){
    $shell = "/bin/bash"; //Shell u. FTP
  } else {
    if($web["web_ftp"]){
      $shell = "/bin/false"; //nur FTP
    } else {
      $shell = "/dev/null"; //weder Shell noch FTP
    }
  }
  if($user_speicher > 0){
    $blocks_soft = $user_speicher * 1024;
    $blocks_hard = $blocks_soft + 1024;
  } else {
    $blocks_soft = $blocks_hard = 0;
  }
  $passwort = substr($user["user_passwort"],5);
  $web_path = $mod->system->server_conf["server_path_httpd_root"]."/web".$web_doc_id;
  $gid_web = $mod->system->server_conf["groupid_von"] + $web_doc_id;

  if($go_info["server"]["ssh_chroot"] == 1) {
          $chroot_addpath = "/./";
  } else {
          $chroot_addpath = "";
  }

  if(!empty($user["user_passwort"])){
    if($user["user_admin"]){
      $mod->system->updateuser($user_username, $userid, $gid_web, $user_name, $web_path.$chroot_addpath, $shell, $passwort);
    } else {
      $mod->system->updateuser($user_username, $userid, $gid_web, $user_name, $web_path."/user/".$user_username.$chroot_addpath, $shell, $passwort);
    }
  } else {
    if($user["user_admin"]){
      $mod->system->updateuser($user_username, $userid, $gid_web, $user_name, $web_path.$chroot_addpath, $shell, $mod->system->getpasswd($user_username));
    } else {
      $mod->system->updateuser($user_username, $userid, $gid_web, $user_name, $web_path."/user/".$user_username.$chroot_addpath, $shell, $mod->system->getpasswd($user_username));
    }
  }

  $mod->db->query("UPDATE isp_isp_user SET user_passwort = '' where doc_id = '$doc_id'");
  $mod->system->data["isp_isp_user"][$doc_id]["user_passwort"] = '';

  // User-Mail-Datei anlegen, sonst evtl. Fehler bei Autoresponder
  if(!$mod->system->server_conf["use_maildir"]){
    if(!is_file("/var/spool/mail/".$user_username)){
      $mod->log->phpcaselog(touch("/var/spool/mail/".$user_username), "create /var/spool/mail/".$user_username, $this->FILE, __LINE__);
      $mod->log->caselog("chown $user_username:mail /var/spool/mail/$user_username &> /dev/null", $this->FILE, __LINE__);
      $mod->log->caselog("chmod 600 /var/spool/mail/$user_username", $this->FILE, __LINE__);
    }
  }

  // Gehört User einem Reseller oder dem admin?
  if($reseller = $mod->db->queryOneRecord("SELECT isp_isp_reseller.user_standard_index FROM isp_nodes, isp_isp_reseller WHERE isp_nodes.doc_id = $doc_id AND isp_nodes.doctype_id = '".$doctype_id."' AND isp_nodes.groupid = isp_isp_reseller.reseller_group")){
    $user_standard_index_page = trim($reseller["user_standard_index"]);
  } else {
    $user_standard_index_page = trim($mod->system->server_conf["user_standard_index"]);
  }

  ////////// Verzeichnisse erzeugen //////////////
  if(!is_dir($web_path."/user/".$user_username)){
    $directory_mode = octdec($this->directory_mode);
    mkdir($web_path."/user/".$user_username, $directory_mode);
    if(!is_dir($web_path."/user/".$user_username."/web")) mkdir($web_path."/user/".$user_username."/web", $directory_mode);
    if(!is_file($web_path."/user/".$user_username."/web/index.html")){
      if($user_standard_index_page == ""){
        $mod->log->caselog("cp -fr /root/ispconfig/isp/user_standard_index.html_".$go_info["server"]["lang"]." ".$web_path."/user/$user_username/web/index.html", $this->FILE, __LINE__); //Standard-Index-Seite einfügen
        $inhalt = $mod->file->rf($web_path."/user/".$user_username."/web/index.html");
        $inhalt = str_replace("{USER_USERNAME}", $user_username, $inhalt);
      } else {
        if($web["web_host"] == "") {
          $FQDN = $web["web_domain"];
        } else {
          $FQDN = $web["web_host"].".".$web["web_domain"];
        }
        $inhalt = str_replace("%%%FQDN%%%", $FQDN, $user_standard_index_page);
        unset($FQDN);
        $inhalt = str_replace("%%%USER%%%", $user_username, $inhalt);
      }
      $mod->file->wf($web_path."/user/".$user_username."/web/index.html", $inhalt);
    }
    exec("chown -R ".$user_username.":web".$web_doc_id." ".$web_path."/user/".$user_username." &> /dev/null");
    exec("chmod -R 775 $web_path/user/$user_username");
    exec("chmod 755 $web_path/user/$user_username");
  }
  ////////// Verzeichnisse erzeugen ENDE ///////////


  //wenn User Admin-User des Webs, Owner des Webs ändern, aber nicht Owner der User-Verzeichnisse!
  if($user["user_admin"]){
    //exec("usermod -G web".$web_doc_id." ".$user_username."");
    // alten admin herausfinden
    $old_admin_uid = fileowner($web_path);
    $mod->system->usermod($user_username, "web".$web_doc_id.($all_user_groups != "" ? ",".$all_user_groups : ""));
    exec("chown $user_username $web_path &> /dev/null");
    //exec("chown $user_username $web_path/cgi-bin &> /dev/null");
    exec("chown -R --from=$old_admin_uid $user_username $web_path/cgi-bin &> /dev/null");
    exec("chown $user_username $web_path/log &> /dev/null");
    exec("chown $user_username $web_path/ssl &> /dev/null");
    exec("chown $user_username $web_path/user &> /dev/null");
    //exec("chown $user_username $web_path/web &> /dev/null");
    exec("chown -R --from=$old_admin_uid $user_username $web_path/web &> /dev/null");
    //exec("chown $user_username $web_path/web/error &> /dev/null");
    //exec("chown $user_username $web_path/web/error/* &> /dev/null");
    exec("chown -R $user_username $web_path/log/* &> /dev/null");
  } else {
    //exec("usermod -G users ".$user_username."");
    $mod->system->usermod($user_username, "users".($all_user_groups != "" ? ",".$all_user_groups : ""));
  }

  // Diskquota setzen
  if(!strstr($mod->system->server_conf["dist"], "freebsd")){
    $mod->log->caselog("setquota -u $user_username $blocks_soft $blocks_hard 0 0 -a &> /dev/null", $this->FILE, __LINE__);
    $mod->log->caselog("setquota -T -u $user_username 604800 604800 -a &> /dev/null", $this->FILE, __LINE__);
  } else {
    if($q_dirs = $mod->system->quota_dirs()){
      if(!empty($q_dirs)){
        foreach($q_dirs as $q_dir){
          $mod->log->caselog("setquota -u -f ".$q_dir." -bh".$blocks_hard." -bs".$blocks_soft." ".$user_username." &> /dev/null", $this->FILE, __LINE__);
        }
      }
    }
  }

  // Mailquota, Autoresponder etc. einrichten
  $mod->procmail->make_forward($doc_id);
  $mod->procmail->make_procmailrc($doc_id);
  $mod->procmail->make_recipes($doc_id);

  //Status zurücksetzen
  $mod->db->query("update isp_isp_user SET status = '' where doc_id = '$doc_id'");
  $mod->system->data["isp_isp_user"][$doc_id]["status"] = '';

  // Chroot enviroment erstellen
  if($go_info["server"]["ssh_chroot"] == 1) {
          exec("/root/ispconfig/scripts/shell/create_chroot_env.sh $user_username");
  }

}


function user_delete($doc_id, $doctype_id) {
  global $mod;
  $dist = $mod->system->server_conf["dist"];
  $user = $mod->system->data["isp_isp_user"][$doc_id];
  if(empty($user)) $mod->log->ext_log("query result empty", 2, $this->FILE, __LINE__);

  $userid = $mod->system->server_conf["userid_von"] + $user["doc_id"];
  $user_name = $user["user_name"];
  $user_username = $user["user_username"];
  $length = strlen($user_username) + 1;

  $sql = "SELECT * FROM isp_dep WHERE child_doc_id = '$doc_id' AND child_doctype_id = '".$this->user_doctype_id."'";
  $web_dep = $mod->db->queryOneRecord($sql);
  $web_doc_id = $web_dep["parent_doc_id"];
  $web_path = $mod->system->server_conf["server_path_httpd_root"]."/web".$web_doc_id;

/*
  //User-Verzeichnis löschen
  if(is_dir($web_path."/user/".$user_username)){
    $mod->log->caselog("cp -fr $web_path/user/$user_username /root/ispconfig/scripts", $this->FILE, __LINE__); //Backup erstellen
    exec("cd /root/ispconfig/scripts; tar -pczf $user_username.tar.gz $user_username &"); //Backup
    $mod->log->caselog("rm -fr /root/ispconfig/scripts/$user_username", $this->FILE, __LINE__); //Backup
    $mod->log->caselog("rm -fr $web_path/user/$user_username", $this->FILE, __LINE__);
  }
*/

  // wenn Admin-User gelöscht wird, soll der Hauptordner wieder dem Apache-User gehören
  if($user["user_admin"] && is_dir($web_path)){
    // alten admin herausfinden
    $old_admin_uid = fileowner($web_path);
    $apache_user = $this->apache_user;
    exec("chown $apache_user $web_path &> /dev/null");
    //exec("chown $apache_user $web_path/cgi-bin &> /dev/null");
    exec("chown -R --from=$old_admin_uid $apache_user $web_path/cgi-bin &> /dev/null");
    exec("chown $apache_user $web_path/log &> /dev/null");
    exec("chown $apache_user $web_path/phptmp &> /dev/null");
    exec("chown $apache_user $web_path/ssl &> /dev/null");
    exec("chown $apache_user $web_path/user &> /dev/null");
    //exec("chown $apache_user $web_path/web &> /dev/null");
    exec("chown -R --from=$old_admin_uid $apache_user $web_path/web &> /dev/null");
    //exec("chown $apache_user $web_path/web/error &> /dev/null");
    //exec("chown $apache_user $web_path/web/error/* &> /dev/null");
    exec("chown -R $apache_user $web_path/log/* &> /dev/null");
  }

  // Diskquota setzen
  if(!strstr($mod->system->server_conf["dist"], "freebsd")){
    $mod->log->caselog("setquota -u $user_username 0 0 0 0 -a &> /dev/null", $this->FILE, __LINE__);
  } else {
    if($q_dirs = $mod->system->quota_dirs()){
      if(!empty($q_dirs)){
        foreach($q_dirs as $q_dir){
          $mod->log->caselog("setquota -u -f ".$q_dir." -bh0 -bs0 ".$user_username." &> /dev/null", $this->FILE, __LINE__);
        }
      }
    }
  }

  //User deaktivieren
  //$mod->log->caselog("userdel -r $user_username &> /dev/null", $this->FILE, __LINE__);
  //$mod->system->deluser($user_username);
  $mod->system->deactivateuser($user_username);

  // User-Mail-Datei löschen
  if(is_file("/var/spool/mail/".$user_username)){
      $mod->log->caselog("rm -f /var/spool/mail/$user_username", $this->FILE, __LINE__);
  }

  $mod->db->query("update isp_isp_user SET status = '' where doc_id = '$doc_id'");
  $mod->system->data["isp_isp_user"][$doc_id]["status"] = '';
}

/////////////////////////////////////////////////////////////////////////////
// Helper Functions

function make_docroot($doc_id,$hostname,$domainname,$web_quota,$update) {
  global $go_info, $mod;

  //DocumentRoot anlegen, falls nicht vorhanden
  if(!is_dir($mod->system->server_conf["server_path_httpd_root"])){
    $mod->file->mkdirs($mod->system->server_conf["server_path_httpd_root"]);
  }

  if($hostname == "") {
    $web_path_realname = $mod->system->server_conf["server_path_httpd_root"] ."/". $domainname;
  } else {
    $web_path_realname = $mod->system->server_conf["server_path_httpd_root"] ."/". $hostname . "." . $domainname;
  }

/*
  //Web wiederherstellen
  if(is_file("/root/ispconfig/scripts/web".$doc_id.".tar.gz")){
    exec("tar xvfz web$doc_id.tar.gz");
    exec("mv web$doc_id ".$mod->system->server_conf["server_path_httpd_root"]);
    exec("rm -f web$doc_id.tar.gz");
    exec("rm -fr web$doc_id");
  }
*/

  // Gehört Web einem Reseller oder dem admin?
  if($reseller = $mod->db->queryOneRecord("SELECT isp_isp_reseller.standard_index FROM isp_nodes, isp_isp_reseller WHERE isp_nodes.doc_id = $doc_id AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.groupid = isp_isp_reseller.reseller_group")){
    $standard_index_page = trim($reseller["standard_index"]);
  } else {
    $standard_index_page = trim($mod->system->server_conf["standard_index"]);
  }

  //Verzeichnisse anlegen
  $directory_mode = octdec($this->directory_mode);
  $web_path = $mod->system->server_conf["server_path_httpd_root"] ."/". "web" . $doc_id;
  if(!is_dir($web_path)){
    mkdir($web_path, $directory_mode);
    $dir_new = 1;
  } else {
    $dir_new = 0;
  }
  if(!is_dir($web_path."/"."web")) mkdir($web_path."/"."web", $directory_mode);
  if(!is_dir($web_path."/"."user")) mkdir($web_path."/"."user", $directory_mode);
  if(!is_file($web_path."/"."user/.no_delete")){
    touch($web_path."/user/.no_delete");
  }
  if(!is_dir($web_path."/"."log")) mkdir($web_path."/"."log", $directory_mode);
  if(!is_file($web_path."/"."log/.no_delete")){
    touch($web_path."/log/.no_delete");
  }
  if(!is_dir($web_path."/"."cgi-bin")) mkdir($web_path."/"."cgi-bin", $directory_mode);
  if(!is_file($web_path."/"."cgi-bin/.no_delete")){
    touch($web_path."/cgi-bin/.no_delete");
  }
  if(!is_dir($web_path."/"."ssl")) mkdir($web_path."/"."ssl", $directory_mode);
  if(!is_file($web_path."/"."ssl/.no_delete")){
    touch($web_path."/ssl/.no_delete");
  }
  if(!is_dir($web_path."/"."phptmp")) $mod->file->mkdirs($web_path."/"."phptmp", "0777");
  if(!is_file($web_path."/"."phptmp/.no_delete")){
    touch($web_path."/phptmp/.no_delete");
  }

  $go_pfad = SERVER_ROOT;
  if(!is_dir($web_path."/web/error")){
    $mod->log->caselog("cp -fr /root/ispconfig/isp/error_".$go_info["server"]["lang"]." $web_path/web/error", $this->FILE, __LINE__);
  }
  if(!is_file($web_path."/"."web/error/.no_delete")){
    touch($web_path."/web/error/.no_delete");
  }

  if(!is_file($web_path."/web/index.html") && !is_file($web_path."/web/index.htm") && !is_file($web_path."/web/index.shtml") && !is_file($web_path."/web/index.shtm") && !is_file($web_path."/web/index.php") && !is_file($web_path."/web/index.php3") && !is_file($web_path."/web/index.php4") && !is_file($web_path."/web/index.php5") && !is_file($web_path."/web/index.phtml") && !is_file($web_path."/web/index.cgi") && !is_file($web_path."/web/index.pl") && !is_file($web_path."/web/index.jsp") && !is_file($web_path."/web/index.asp") && !is_file($web_path."/web/index.aspx")){
    if($standard_index_page == ""){
      $mod->log->caselog("cp -fr /root/ispconfig/isp/standard_index.html_".$go_info["server"]["lang"]." ".$web_path."/web/index.html", $this->FILE, __LINE__);
    } else {
      $mod->file->wf($web_path."/web/index.html", $standard_index_page."\n<!-- CUSTOM STANDARD INDEX END //-->");
    }
  }
  if(is_file($web_path."/web/index.html")){
  /////////////// index.html: Adresse einfügen //////////////////
    $index_html_inhalt = $mod->file->rf($web_path."/web/index.html");
    if(substr_count($index_html_inhalt, "<!--ADRESSE//-->") == 2){
      $index_html_arr = explode("<!--ADRESSE//-->", $index_html_inhalt);
      if($hostname == "") {
        $index_html_inhalt = $index_html_arr[0]."<!--ADRESSE//-->".$domainname."!<!--ADRESSE//-->".$index_html_arr[2];
      } else {
        $index_html_inhalt = $index_html_arr[0]."<!--ADRESSE//-->".$hostname.".".$domainname."!<!--ADRESSE//-->".$index_html_arr[2];
      }
      $mod->file->wf($web_path."/web/index.html", $index_html_inhalt);
    }
    if(strpos($index_html_inhalt, "<!-- CUSTOM STANDARD INDEX END //-->")){
      if($hostname == "") {
        $FQDN = $domainname;
      } else {
        $FQDN = $hostname.".".$domainname;
      }
      $index_html_inhalt = str_replace("%%%FQDN%%%", $FQDN, $index_html_inhalt);
      unset($FQDN);
      $mod->file->wf($web_path."/web/index.html", $index_html_inhalt);
    }
    clearstatcache();
  /////////////// index.html: Adresse einfügen ENDE //////////////////
  }

  // symbolischen Link erzeugen
  if(!is_link($web_path_realname)) $mod->log->phpcaselog(@symlink($web_path, $web_path_realname), "ln -s $web_path $web_path_realname", $this->FILE, __LINE__);

  if($handle = opendir($mod->system->server_conf["server_path_httpd_root"])){
    while (false !== ($file = readdir($handle))) {
      if($file != "." && $file != ".." && is_link($mod->system->server_conf["server_path_httpd_root"]."/".$file)){
        if($mod->system->server_conf["server_path_httpd_root"]."/".$file != $web_path_realname && readlink($mod->system->server_conf["server_path_httpd_root"]."/".$file) == $web_path){
          unlink($mod->system->server_conf["server_path_httpd_root"]."/".$file);
        }
      }
    }
    closedir($handle);
  }

  // Gruppe des Webs erstellen
  $groupid = $mod->system->server_conf["groupid_von"] + $doc_id;
  //if(!$mod->system->is_group("web".$doc_id)) $mod->log->caselog("groupadd -g $groupid web$doc_id &> /dev/null", $this->FILE, __LINE__);
  if(!$mod->system->is_group("web".$doc_id)) $mod->system->addgroup("web".$doc_id, $groupid);

  ///////////////// admispconfig der Gruppe hinzufügen ////////////////
  $mod->system->add_user_to_group("web".$doc_id);
  //////////////////// admispconfig der Gruppe hinzufügen ENDE //////////////

  $apache_user = $this->apache_user;
  if($update == 0 || $dir_new){
    exec("chown -R $apache_user:web$doc_id $web_path_realname &> /dev/null");
    exec("chown -R $apache_user:web$doc_id $web_path &> /dev/null");
    exec("chmod -R 775 $web_path");
    exec("chmod -R 775 $web_path_realname");
    exec("chmod 755 $web_path");
    exec("chmod 755 $web_path_realname");
    exec("chmod 755 $web_path/user"); // user-Verzeichnis sollte nicht group-writable sein, weil Sendmail sonst warnings ausgeben könnte wg. der .forward-Datei
    exec("chmod 755 $web_path/log");
    exec("chmod 755 $web_path/ssl");
    exec("chmod 777 $web_path/phptmp");
    if($mod->system->server_conf["server_httpd_suexec"]){
      exec("chmod 755 $web_path/cgi-bin");
    }
    exec("chmod 664 $web_path/web/error/*");
    exec("chmod 664 $web_path/web/index.html");
  } else {
    //gibt es Admin-User für das Web? Wenn er gelöscht wurde, evtl. vorh. Autoresponder-Dateien löschen
    $sql = "SELECT * FROM isp_nodes, isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = '".$doc_id."' AND isp_dep.parent_doctype_id = '".$this->web_doctype_id."' AND isp_dep.child_doc_id = isp_isp_user.doc_id AND isp_dep.child_doctype_id = '".$this->user_doctype_id."' AND isp_isp_user.user_admin = '1' AND isp_nodes.doc_id = isp_isp_user.doc_id AND isp_nodes.doctype_id = '".$this->user_doctype_id."' AND isp_nodes.status = '1'";
    $admin_user = $mod->db->queryOneRecord($sql);
    if(!empty($admin_user)){
      $owner = $admin_user["user_username"];
    } else {
      $owner = $apache_user;
    }
    exec("chmod 664 $web_path/web/error/*");
    exec("chown $owner:web$doc_id $web_path/web/error/*");
    exec("chown -R $owner:web$doc_id $web_path/log/*");
    exec("chown $owner:web$doc_id $web_path/phptmp");
    if(is_file($web_path."/web/index.html")){
      $index_html_inhalt = $mod->file->rf($web_path."/web/index.html");
      if(substr_count($index_html_inhalt, "<!--ADRESSE//-->") == 2){
        exec("chmod 664 $web_path/web/index.html");
        exec("chown $owner:web$doc_id $web_path/web/index.html");
      }
    }
  }

  //Rechte der SSL-Dateien richtig setzen
  $key_file = $mod->system->server_conf["server_path_httpd_root"] ."/web".$doc_id."/ssl/".$hostname.".".$domainname.".key.org";
  $key_file2 = $mod->system->server_conf["server_path_httpd_root"] ."/web".$doc_id."/ssl/".$hostname.".".$domainname.".key";
  $csr_file = $mod->system->server_conf["server_path_httpd_root"] ."/web".$doc_id."/ssl/".$hostname.".".$domainname.".csr";
  $crt_file = $mod->system->server_conf["server_path_httpd_root"] ."/web".$doc_id."/ssl/".$hostname.".".$domainname.".crt";
  if(is_file($key_file)) exec("chmod 644 $key_file");
  if(is_file($key_file2)) exec("chmod 400 $key_file2");
  if(is_file($csr_file)) exec("chmod 644 $csr_file");
  if(is_file($crt_file)) exec("chmod 644 $crt_file");

  $root_gruppe = $mod->system->root_group();
  exec("chmod 400 $web_path/user/.no_delete");
  exec("chown root:$root_gruppe $web_path/user/.no_delete");
  exec("chmod 400 $web_path/log/.no_delete");
  exec("chown root:$root_gruppe $web_path/log/.no_delete");
  exec("chmod 400 $web_path/cgi-bin/.no_delete");
  exec("chown root:$root_gruppe $web_path/cgi-bin/.no_delete");
  exec("chmod 400 $web_path/ssl/.no_delete");
  exec("chown root:$root_gruppe $web_path/ssl/.no_delete");
  exec("chmod 400 $web_path/phptmp/.no_delete");
  exec("chown root:$root_gruppe $web_path/phptmp/.no_delete");
  exec("chmod 400 $web_path/web/error/.no_delete");
  exec("chown root:$root_gruppe $web_path/web/error/.no_delete");

  ////////////// Standard CGIs ////////////////////
  $web = $mod->db->queryOneRecord("SELECT web_standard_cgi,  web_individual_error_pages, error_400, error_401, error_403, error_404, error_405, error_500, error_503 from isp_isp_web WHERE server_id = '".$mod->system->server_conf["doc_id"]."' AND doc_id = '$doc_id'");
  if($web["web_standard_cgi"]){
    if(!is_file($web_path."/cgi-bin/.csc")){  // .csc: Copy Standard CGIs
      $sc_files = $mod->file->getDirectoryListing("/root/ispconfig/standard_cgis", "a", 1, 1, "all", 0);
      if(!empty($sc_files)){
        foreach($sc_files as $sc_file){
          clearstatcache();
          if(is_dir("/root/ispconfig/standard_cgis/".$sc_file)){
            $mod->file->mkdirs($web_path."/".$sc_file, decoct(fileperms("/root/ispconfig/standard_cgis/".$sc_file)));
            exec("chown ".$owner.":web".$doc_id." ".$web_path."/".$sc_file);
          } else {
            if(!file_exists($web_path."/".$sc_file)) exec("cp -p /root/ispconfig/standard_cgis/".$sc_file." ".$web_path."/".$sc_file." && chown ".$owner.":web".$doc_id." ".$web_path."/".$sc_file);
          }
        }
      }
      touch($web_path."/cgi-bin/.csc");
      exec("chown ".$owner.":web".$doc_id." ".$web_path."/cgi-bin/.csc");
    }
  }
  ////////////// Standard CGIs ENDE ////////////////////

  ////////////// Individual Error Pages ////////////////////
  if($web["web_individual_error_pages"]){
    $mod->file->wf($web_path."/web/error/error_400.html", trim($web["error_400"]));
    exec("chown ".$owner.":web".$doc_id." ".$web_path."/web/error/error_400.html");
    $mod->file->wf($web_path."/web/error/error_401.html", trim($web["error_401"]));
    exec("chown ".$owner.":web".$doc_id." ".$web_path."/web/error/error_401.html");
    $mod->file->wf($web_path."/web/error/error_403.html", trim($web["error_403"]));
    exec("chown ".$owner.":web".$doc_id." ".$web_path."/web/error/error_403.html");
    $mod->file->wf($web_path."/web/error/error_404.html", trim($web["error_404"]));
    exec("chown ".$owner.":web".$doc_id." ".$web_path."/web/error/error_404.html");
    $mod->file->wf($web_path."/web/error/error_405.html", trim($web["error_405"]));
    exec("chown ".$owner.":web".$doc_id." ".$web_path."/web/error/error_405.html");
    $mod->file->wf($web_path."/web/error/error_500.html", trim($web["error_500"]));
    exec("chown ".$owner.":web".$doc_id." ".$web_path."/web/error/error_500.html");
    $mod->file->wf($web_path."/web/error/error_503.html", trim($web["error_503"]));
    exec("chown ".$owner.":web".$doc_id." ".$web_path."/web/error/error_503.html");
  }
  ////////////// Individual Error Pages ENDE ////////////////////

  return $web_path;
}

function make_vhost($server_id) {
  global $mod,$go_info;

  $mod->log->caselog("cp -fr $this->vhost_conf $this->vhost_conf~", $this->FILE, __LINE__);

  // alte Frontpage-Webs feststellen
  if(is_file($mod->system->server_conf["server_path_frontpage"])) exec("grep '## FP-WEB' $this->vhost_conf | cut -f4 -d/ | cut -f2 -db", $old_fp_webs);

  $apache_version = $this->apache_version();
  if($apache_version == 1){
    $fp_resource_config = "ResourceConfig /dev/null";
    $fp_access_config = "AccessConfig /dev/null";
    $web_port = "";
    $ssl_port = "";
  }
  if($apache_version == 2){
    $fp_resource_config = "";
    $fp_access_config = "";
    $web_port = ":80";
    $ssl_port = ":443";
  }

  $server = $mod->system->server_conf;
  if($server["server_httpd_suexec"]){
    exec("httpd -V | grep SUEXEC_BIN", $suexec_arr, $suexec_check);
    unset($suexec_arr);
  }

  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "vhost.conf.master"));
  $mod->tpl->define_dynamic( "vhost", "table" );
  $mod->tpl->define_dynamic( "namevhost", "table" );

  $ips = $mod->system->data["isp_server_ip"];

  foreach($ips as $ip){
    $ip_test = $mod->db->queryAllRecords("SELECT * FROM isp_isp_web,isp_nodes WHERE isp_isp_web.web_ip = '".$ip["server_ip"]."' AND isp_isp_web.server_id = '$server_id' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.status = '1'");

      //NameVirtualHost schreiben
      if(!empty($ip_test)){
        // Variablen zuweisen
        $mod->tpl->assign( array(SERVERIP => "NameVirtualHost ".$ip["server_ip"].$web_port."
<VirtualHost ".$ip["server_ip"].$web_port.">
  ServerName localhost
  ServerAdmin root@localhost
  DocumentRoot /var/www/sharedip
</VirtualHost>"));
      } else {
        $mod->tpl->assign( array(SERVERIP => ""));
      }
        $mod->tpl->parse(NAMEVHOST,".namevhost");
  }

  $webs = $mod->db->queryAllRecords("select * from isp_nodes,isp_isp_web WHERE isp_isp_web.server_id = '$server_id' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.status = '1'");

  if(!empty($webs)){
  foreach($webs as $web){
    if($web["web_host"] == "") {
      $servername = $web["web_domain"];
    } else {
      $servername = $web["web_host"].".".$web["web_domain"];
    }

    $document_root = $mod->system->server_conf["server_path_httpd_root"]."/"."web".$web["doc_id"]."/"."web";

    $directory_index = "DirectoryIndex ".trim(str_replace("\n", " ", $mod->file->unix_nl($web["optionen_directory_index"])));

    $web_httpd_include = $web["web_httpd_include"];
    if(!empty($web_httpd_include)){
      $web_httpd_include = $mod->file->unix_nl($web_httpd_include);
      $web_httpd_include = $this->httpd_syntax_check($web["doc_id"], $web_httpd_include, 1);
    }

    if($server["server_httpd_suexec"] || $go_info["server"]["apache2_php"] == 'suphp'){
      $sql = "SELECT * FROM isp_nodes, isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = '".$web["doc_id"]."' AND isp_dep.parent_doctype_id = '".$this->web_doctype_id."' AND isp_dep.child_doc_id = isp_isp_user.doc_id AND isp_dep.child_doctype_id = '".$this->user_doctype_id."' AND isp_isp_user.user_admin = '1' AND isp_nodes.doc_id = isp_isp_user.doc_id AND isp_nodes.doctype_id = '".$this->user_doctype_id."' AND isp_nodes.status = '1'";

      $admin_user = $mod->db->queryOneRecord($sql);

      if(!empty($admin_user)){
        $webadmin = $admin_user["user_username"];
      } else {
        $webadmin = "nobody";
      }

    }

    if($server["server_httpd_suexec"]){
      if($suexec_check == "0"){

        if($apache_version == 1){
          $suexec = "User ".$webadmin."
Group web".$web["doc_id"];
        }
        if($apache_version == 2){
          $suexec = "SuexecUserGroup ".$webadmin." web".$web["doc_id"];
        }

                //TB: Check, ob es Webadmin und webgroup gibt
                if($mod->system->is_user($webadmin) != true or $mod->system->is_group("web".$web["doc_id"]) != true) {
                        $suexec = "";
                }

        // TB: SuExec Überprüfung abschaltbar
                if($go_info["server"]["suexec_check_disable"] != true) {
                        $suexec = $this->httpd_syntax_check($web["doc_id"], $suexec, 0);
                        $mod->log->msg("HTTPD_SYNTAX_CHECK: suexec");
                }
      } else {
        $suexec = "";
      }
    } else {
      $suexec = "";
    }

    //Serveralias bestimmen
    $sql = "SELECT isp_dep.*, isp_isp_domain.* from isp_dep,isp_isp_domain,isp_nodes where isp_dep.child_doc_id = isp_isp_domain.doc_id and isp_dep.child_doctype_id ='".$this->domain_doctype_id."' and isp_dep.parent_doctype_id = '".$this->web_doctype_id."' and isp_dep.parent_doc_id = '".$web["doc_id"]."' and isp_isp_domain.status != 'd' AND isp_nodes.doc_id = isp_isp_domain.doc_id AND isp_nodes.doctype_id = isp_isp_domain.doctype_id AND isp_nodes.status = 1";
    $domains = $mod->db->queryAllRecords($sql);

    $serveralias = "ServerAlias ";
    $rewrite_rule = "RewriteEngine on";
    foreach($domains as $domain){
        if($domain["domain_host"] == "") {
          $serveralias .= $domain["domain_domain"].' ';
        } else {
          $serveralias .= $domain["domain_host"] . "." . $domain["domain_domain"].' ';
        }
        if(!empty($domain["domain_weiterleitung"])){
          if($domain["domain_host"] == "") {
            $rewrite_cond_url = str_replace(".", "\\.", $domain["domain_domain"]);
          } else {
            $rewrite_cond_url = str_replace(".", "\\.", $domain["domain_host"].".".$domain["domain_domain"]);
          }
          $rewrite_rule .= "\nRewriteCond %{HTTP_HOST}   ^".$rewrite_cond_url." [NC]";
          if(substr($domain["domain_weiterleitung"],0,4) == "http" || substr($domain["domain_weiterleitung"],0,4) == "HTTP"){
            $domain["domain_weiterleitung"] = strtolower($domain["domain_weiterleitung"]);
            if(substr($domain["domain_weiterleitung"],-1) == "/") $domain["domain_weiterleitung"] = substr($domain["domain_weiterleitung"],0,-1);
            $rewrite_rule .= "\nRewriteRule ^/(.*)         ".$domain["domain_weiterleitung"]."/$1 [L,R]";
          } else {
            if(substr($domain["domain_weiterleitung"],-1) != "/") $domain["domain_weiterleitung"] .= "/";
            if(substr($domain["domain_weiterleitung"],0,1) != "/") $domain["domain_weiterleitung"] = "/".$domain["domain_weiterleitung"];
            $rewrite_rule .= "\nRewriteRule   ^/(.*)$  http://".$servername.$domain["domain_weiterleitung"]."$1  [R]";
          }
        }
    }
    $serveralias = substr($serveralias,0,-1);
    if($serveralias == "ServerAlias") $serveralias = "";
    if($rewrite_rule == "RewriteEngine on"){
      $rewrite_rule = "";
    } else {
      $rewrite_rule = $this->httpd_syntax_check($web["doc_id"], $rewrite_rule, 0);
    }

    $cgi = "";
    if($web["web_cgi"] == 1) $cgi = "ScriptAlias  /cgi-bin/ ".$mod->system->server_conf["server_path_httpd_root"]."/"."web".$web["doc_id"]."/"."cgi-bin/
AddHandler cgi-script .cgi
AddHandler cgi-script .pl";

    if($web["web_php"]){
      if($apache_version == 1){
        $php = "AddType application/x-httpd-php .php .php3 .php4 .php5";
      }
      if($apache_version == 2){
                  $php = '';
                if($go_info["server"]["apache2_php"] == 'addtype' or $go_info["server"]["apache2_php"] == 'both' or $go_info["server"]["apache2_php"] == 'suphp') {
                        $php .= "AddType application/x-httpd-php .php .php3 .php4 .php5\n";
                }
                if ($go_info["server"]["apache2_php"] == 'addhandler') {
                        $php .= "AddHandler application/x-httpd-php .php .php3 .php4 .php5\n";
                }
                if($go_info["server"]["apache2_php"] == 'filter' or $go_info["server"]["apache2_php"] == 'both') {
            $php .= "<Files *.php>
    SetOutputFilter PHP
    SetInputFilter PHP
</Files>
<Files *.php3>
    SetOutputFilter PHP
    SetInputFilter PHP
</Files>
<Files *.php4>
    SetOutputFilter PHP
    SetInputFilter PHP
</Files>
<Files *.php5>
    SetOutputFilter PHP
    SetInputFilter PHP
</Files>";
                }
      }
          if($go_info["server"]["apache2_php"] == 'suphp'){
                  $php .= "suPHP_Engine on\n";
                  $php .= "suPHP_UserGroup ".$webadmin." web".$web["doc_id"]."\n";
                  $php .= "AddHandler x-httpd-php .php .php3 .php4 .php5\n";
                  $php .= "suPHP_AddHandler x-httpd-php\n";
          }

          if($go_info["server"]["apache2_php"] != 'suphp') {
              if($web["web_php_safe_mode"]){
                $php .= "\nphp_admin_flag safe_mode On
php_admin_value open_basedir ".$mod->system->server_conf["server_path_httpd_root"]."/"."web".$web["doc_id"]."/
php_admin_value file_uploads 1
php_admin_value upload_tmp_dir ".$mod->system->server_conf["server_path_httpd_root"]."/"."web".$web["doc_id"]."/phptmp/
php_admin_value session.save_path ".$mod->system->server_conf["server_path_httpd_root"]."/"."web".$web["doc_id"]."/phptmp/";
              } else {
                $php .= "\nphp_admin_flag safe_mode Off";
              }
        }
    } else {
      $php = "";
    }

    if($web["web_ssi"]){
      if($apache_version == 1){
        $ssi = "AddType text/html .shtml
AddHandler server-parsed .shtml";
      }
      if($apache_version == 2){
        $ssi = "AddType text/html .shtml
AddOutputFilter INCLUDES .shtml";
      }
    } else {
      $ssi = "";
    }

    if($web["web_wap"]){
      $wap = "AddType application/vnd.wap.wmlscriptc .wmlsc .wsc
AddType text/vnd.wap.wml .wml
AddType text/vnd.wap.wmlscript .ws .wmlscript
AddType image/vnd.wap.wbmp .wbmp";
    } else {
      $wap = "";
    }

    if($web["web_frontpage"] && is_file($mod->system->server_conf["server_path_frontpage"])){
      $frontpage = "<Directory ".$document_root.">         ## FP-WEB
  AllowOverRide All
</Directory>";
    } else {
      $frontpage = "";
    }

   if($apache_version == 1){
     $error_alias = "";
   }
   if($apache_version == 2){
     $error_alias = "Alias /error/ \"".$document_root."/error/\"";
   }

  ////////////// Error Pages ////////////////////
   if($web["web_individual_error_pages"]){
     if(trim($web["error_400"]) == ""){
       $error = "ErrorDocument 400 /error/invalidSyntax.html";
     } else {
       $error = "ErrorDocument 400 /error/error_400.html";
     }
     if(trim($web["error_401"]) == ""){
       $error .= "\nErrorDocument 401 /error/authorizationRequired.html";
     } else {
       $error .= "\nErrorDocument 401 /error/error_401.html";
     }
     if(trim($web["error_403"]) == ""){
       $error .= "\nErrorDocument 403 /error/forbidden.html";
     } else {
       $error .= "\nErrorDocument 403 /error/error_403.html";
     }
     if(trim($web["error_404"]) == ""){
       $error .= "\nErrorDocument 404 /error/fileNotFound.html";
     } else {
       $error .= "\nErrorDocument 404 /error/error_404.html";
     }
     if(trim($web["error_405"]) == ""){
       $error .= "\nErrorDocument 405 /error/methodNotAllowed.html";
     } else {
       $error .= "\nErrorDocument 405 /error/error_405.html";
     }
     if(trim($web["error_500"]) == ""){
       $error .= "\nErrorDocument 500 /error/internalServerError.html";
     } else {
       $error .= "\nErrorDocument 500 /error/error_500.html";
     }
     if(trim($web["error_503"]) == ""){
       $error .= "\nErrorDocument 503 /error/overloaded.html";
     } else {
       $error .= "\nErrorDocument 503 /error/error_503.html";
     }
   } else {
     $error = "ErrorDocument 400 /error/invalidSyntax.html\nErrorDocument 401 /error/authorizationRequired.html\nErrorDocument 403 /error/forbidden.html\nErrorDocument 404 /error/fileNotFound.html\nErrorDocument 405 /error/methodNotAllowed.html\nErrorDocument 500 /error/internalServerError.html\nErrorDocument 503 /error/overloaded.html";
   }
  ////////////// Error Pages ENDE ////////////////////

    //SSL-Web schreiben
    if($web["web_ssl"] && (!empty($web["ssl_request"])) && (!empty($web["ssl_cert"]))){
      $ssl = "
<IfModule mod_ssl.c>
<VirtualHost ".$web["web_ip"].":443>
".$web_httpd_include."
".$suexec."
ServerName ".$servername."".$ssl_port."
ServerAdmin webmaster@".$web["web_domain"]."
DocumentRoot ".$document_root."
".$serveralias."
".$directory_index."
".$cgi."
ErrorLog ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/log/error.log
".$php."
".$ssi."
".$wap."
SSLEngine on
SSLCertificateFile ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/".$web["web_host"].".".$web["web_domain"].".crt
SSLCertificateKeyFile ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/".$web["web_host"].".".$web["web_domain"].".key
".$error_alias."
".$error."
AliasMatch ^/~([^/]+)(/(.*))? ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/user/$1/web/$3
AliasMatch ^/users/([^/]+)(/(.*))? ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/user/$1/web/$3
".str_replace("\nRewriteRule   ^/(.*)$  http://".$servername.$domain["domain_weiterleitung"]."$1  [R]", "\nRewriteRule   ^/(.*)$  https://".$servername.$domain["domain_weiterleitung"]."$1  [R]", $rewrite_rule)."
".$frontpage."
SetEnvIf User-Agent \".*MSIE.*\" nokeepalive ssl-unclean-shutdown downgrade-1.0 force-response-1.0
</VirtualHost>
</IfModule>";

// Zertifikate ggf. umbenennen
if(!is_file($mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/".$web["web_host"].".".$web["web_domain"].".crt")) $mod->log->caselog("mv ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/*.crt ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/".$web["web_host"].".".$web["web_domain"].".crt &> /dev/null", $this->FILE, __LINE__);
if(!is_file($mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/".$web["web_host"].".".$web["web_domain"].".key")) $mod->log->caselog("mv ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/*.key ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/".$web["web_host"].".".$web["web_domain"].".key &> /dev/null", $this->FILE, __LINE__);
if(!is_file($mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/".$web["web_host"].".".$web["web_domain"].".key.org")) $mod->log->caselog("mv ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/*.key.org ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/".$web["web_host"].".".$web["web_domain"].".key.org &> /dev/null", $this->FILE, __LINE__);
if(!is_file($mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/".$web["web_host"].".".$web["web_domain"].".csr")) $mod->log->caselog("mv ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/*.csr ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/ssl/".$web["web_host"].".".$web["web_domain"].".csr &> /dev/null", $this->FILE, __LINE__);
clearstatcache();
    } else {
      $ssl = "";
    }

    // Variablen zuweisen
    $mod->tpl->assign( array( SERVERNAME => $servername.$web_port,
                        IP => $web["web_ip"].$web_port,
                        DOCUMENTROOT => $document_root,
                        SERVERALIAS => $serveralias,
                        DIRECTORYINDEX => $directory_index,
                        CGI => $cgi,
                        WEB_ERROR_LOG => $mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/log/error.log",
                        SERVERADMIN => "webmaster@".$web["web_domain"],
                        PHP => $php,
                        SSI => $ssi,
                        WAP => $wap,
                        ERRORALIAS => $error_alias,
                        ERROR => $error,
                        WEB => "web".$web["doc_id"],
                        HTTPD_INCLUDE => $web_httpd_include,
                        SUEXEC => $suexec,
                        REWRITE_RULE => $rewrite_rule,
                        FRONTPAGE => $frontpage,
                        SSL => $ssl,
                        HTTPD_ROOT => $mod->system->server_conf["server_path_httpd_root"]));
    $mod->tpl->parse(VHOST,".vhost");
  }
  } else {
  $mod->tpl->assign( array( SERVERNAME => "",
                        IP => "",
                        DOCUMENTROOT => "",
                        SERVERALIAS => "",
                        DIRECTORYINDEX => "",
                        CGI => "",
                        WEB_ERROR_LOG => "",
                        SERVERADMIN => "",
                        PHP => "",
                        SSI => "",
                        WAP => "",
                        ERRORALIAS => "",
                        ERROR => "",
                        WEB => "",
                        HTTPD_INCLUDE => "",
                        SUEXEC => "",
                        REWRITE_RULE => "",
                        FRONTPAGE => "",
                        SSL => "",
                        HTTPD_ROOT => ""));
    $mod->tpl->parse(VHOST,".vhost");
  }
  $mod->tpl->assign( array( FP_RESOURCE_CONFIG => $fp_resource_config,
                       FP_ACCESS_CONFIG => $fp_access_config));
  $mod->tpl->parse(TABLE, table);

  if(!empty($webs)){
  $vhost_text = $mod->tpl->fetch();
  } else {
  $vhost_text = "";
  }

  if(is_file($mod->system->server_conf["server_path_frontpage"])){
    $fp_webs = $mod->db->queryAllRecords("SELECT * FROM isp_nodes,isp_isp_web WHERE isp_isp_web.server_id = '$server_id' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.status = '1' AND isp_isp_web.web_frontpage = '1'");

    if(!empty($fp_webs)){
      $serverroot = "ServerRoot ".shell_exec("httpd -V | grep HTTPD_ROOT | cut -f2 -d'\"'");
      $vhost_text = $serverroot."\n".$vhost_text;
    }
  }

  $mod->file->wf($this->vhost_conf, $vhost_text);
  //Leerzeilen löschen
  $mod->file->remove_blank_lines($this->vhost_conf);

  if(!empty($fp_webs) && is_file($mod->system->server_conf["server_path_frontpage"])){
    foreach($fp_webs as $fp_web){
      if(!empty($fp_web["optionen_frontpage_passwort"])){
        if($fp_web["web_host"] == "") {
          $fp_servername = $fp_web["web_domain"];
        } else {
          $fp_servername = $fp_web["web_host"].".".$fp_web["web_domain"];
        }
        //gibt es Admin-User für das Web?
        $admin_user = $mod->db->queryOneRecord("SELECT * FROM isp_nodes, isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = '".$fp_web["doc_id"]."' AND isp_dep.parent_doctype_id = '".$this->web_doctype_id."' AND isp_dep.child_doc_id = isp_isp_user.doc_id AND isp_dep.child_doctype_id = '".$this->user_doctype_id."' AND isp_isp_user.user_admin = '1' AND isp_nodes.doc_id = isp_isp_user.doc_id AND isp_nodes.doctype_id = '".$this->user_doctype_id."' AND isp_nodes.status = '1'");
        if(!empty($admin_user)){
          $fp_owner_user = $admin_user["user_username"];
        } else {
          $fp_owner_user = $this->apache_user();
        }
        $mod->log->caselog($mod->system->server_conf["server_path_frontpage"]." -o install -u admin -pw ".$fp_web["optionen_frontpage_passwort"]." -p 80 -m ".$fp_servername." -s ".$this->vhost_conf." -xu ".$fp_owner_user." -xg web".$fp_web["doc_id"]." &> /dev/null", $this->FILE, __LINE__);
        $mod->log->caselog($mod->system->server_conf["server_path_frontpage"]." -o users -c changepassword -u admin -pw ".$fp_web["optionen_frontpage_passwort"]." -p 80 -m ".$fp_servername." -s ".$this->vhost_conf." &> /dev/null", $this->FILE, __LINE__);
      }
    }
  }

  if(!empty($old_fp_webs)){
    foreach($old_fp_webs as $old_fp_web){
      if($mod->system->data["isp_isp_web"][$old_fp_web]["server_id"] == $server_id && $mod->system->data["isp_isp_web"][$old_fp_web]["web_frontpage"] != 1){
        $old_fp_web_data = $mod->system->data["isp_isp_web"][$old_fp_web];
      }
      if(!empty($old_fp_web_data)){
        if($old_fp_web_data["web_host"] == "") {
          $old_fp_servername = $old_fp_web_data["web_domain"];
        } else {
          $old_fp_servername = $old_fp_web_data["web_host"].".".$old_fp_web_data["web_domain"];
        }
        $mod->log->caselog($mod->system->server_conf["server_path_frontpage"]." -o uninstall -p 80 -m ".$old_fp_servername." &> /dev/null", $this->FILE, __LINE__);
      }
    }
  }
  unset($fp_webs);
}

function backup($doc_id){
  global $go_info, $mod;
  // Daten des Webs aus DB holen
  $web = $mod->system->data["isp_isp_web"][$doc_id];
  $db_server = $go_info["server"]["db_host"];
  $db_user = $go_info["server"]["db_user"];
  $db_password = $go_info["server"]["db_password"];
  $web_path = $mod->system->server_conf["server_path_httpd_root"]."/web".$doc_id;

  $serial = date("Ymd");
  exec("tar -pczf $web_path/web$doc_id.$serial.tar.gz $web_path/web");

  exec("mysqldump -h $db_server -u $db_user -p$db_passwort -c --add-drop-table --add-locks --all --quick --lock-tables web$doc_id >$web_path/web$doc_id.sql");
}

function make_openssl_cnf($doc_id,$ssl_password) {
  global $mod;

  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "openssl.cnf.master"));

  $ssl = $mod->system->data["isp_isp_web"][$doc_id];

  if(!empty($ssl["ssl_state"])){
    $ssl["ssl_state"] = "ST                     = ".$ssl["ssl_state"];
  } else {
    $ssl["ssl_state"] = "";
  }
  if(!empty($ssl["ssl_organization_unit"])){
    $ssl["ssl_organization_unit"] = "OU                     = ".$ssl["ssl_organization_unit"];
  } else {
    $ssl["ssl_organization_unit"] = "";
  }

  // Variablen zuweisen
  $mod->tpl->assign( array(SSL_COUNTRY => $ssl["ssl_country"],
                      SSL_STATE => $ssl["ssl_state"],
                      SSL_LOCALITY => $ssl["ssl_locality"],
                      SSL_ORGANIZATION => $ssl["ssl_organization"],
                      SSL_ORGANIZATION_UNIT => $ssl["ssl_organization_unit"],
                      SSL_COMMON_NAME => $ssl["web_host"].".".$ssl["web_domain"],
                      SSL_EMAIL => "admin@".$ssl["web_domain"],
                      SSL_PASSWORD => $ssl_password));

  $mod->tpl->parse(TABLE, table);

  $openssl_text = $mod->tpl->fetch();
  $datei = $mod->system->server_conf["server_path_httpd_root"]."/web".$doc_id."/ssl/openssl.cnf";
  $mod->file->wf($datei, $openssl_text);
  // TB: Logging eingebaut
  $mod->log->msg("make_ssl_cnf $datei");
  return $datei;
}

function make_ssl($doc_id){
  global $mod;
  $ssl = $mod->system->data["isp_isp_web"][$doc_id];
  $host = $ssl["web_host"];
  $domain = $ssl["web_domain"];
  $ssl_days = $ssl["ssl_days"];
  $key_file = $mod->system->server_conf["server_path_httpd_root"]."/web".$doc_id."/ssl/".$host.".".$domain.".key.org";
  $key_file2 = $mod->system->server_conf["server_path_httpd_root"]."/web".$doc_id."/ssl/".$host.".".$domain.".key";
  $csr_file = $mod->system->server_conf["server_path_httpd_root"]."/web".$doc_id."/ssl/".$host.".".$domain.".csr";
  $crt_file = $mod->system->server_conf["server_path_httpd_root"]."/web".$doc_id."/ssl/".$host.".".$domain.".crt";



  if($ssl["ssl_action"] == "create"){
    //Zufallsdatei erzeugen
    $rand_file = $mod->system->server_conf["server_path_httpd_root"]."/web".$doc_id."/ssl/random_file";
    $rand_data = md5(uniqid(microtime(),1));
    for($i=0; $i<1000; $i++){
    $rand_data .= md5(uniqid(microtime(),1));
    $rand_data .= md5(uniqid(microtime(),1));
    $rand_data .= md5(uniqid(microtime(),1));
    $rand_data .= md5(uniqid(microtime(),1));
    }
    $mod->file->wf($rand_file, $rand_data);

    $ssl_password = substr(md5(uniqid(microtime(),1)), 0, 15);
    $config_file = $this->make_openssl_cnf($doc_id,$ssl_password);

        // TB: mit caselog eingebaut
        if(is_file($config_file)){
          $mod->log->caselog("openssl genrsa -des3 -rand $rand_file -passout pass:$ssl_password -out $key_file 1024 && openssl req -new -passin pass:$ssl_password -passout pass:$ssl_password -key $key_file -out $csr_file -days $ssl_days -config $config_file && openssl req -x509 -passin pass:$ssl_password -passout pass:$ssl_password -key $key_file -in $csr_file -out $crt_file -days $ssl_days -config $config_file && openssl rsa -passin pass:$ssl_password -in $key_file -out $key_file2", $this->FILE, __LINE__);
        }

    exec("chmod 400 $key_file2");
    exec("rm -f $config_file");
    exec("rm -f $rand_file");
    $ssl_request = $mod->file->rf($csr_file);
    $ssl_cert = $mod->file->rf($crt_file);
    $mod->db->query("UPDATE isp_isp_web SET ssl_request = '$ssl_request', ssl_cert = '$ssl_cert' WHERE doc_id = '$doc_id'");
    $mod->system->data["isp_isp_web"][$doc_id]["ssl_request"] = $ssl_request;
    $mod->system->data["isp_isp_web"][$doc_id]["ssl_cert"] = $ssl_cert;
  }

  if($ssl["ssl_action"] == "save"){
    $ssl_cert = $ssl["ssl_cert"];
    $ssl_cert_alt = $mod->file->rf($crt_file);
    $mod->file->wf($crt_file, $ssl_cert);
    exec("openssl verify $crt_file | grep 'unable to load certificate file'", $erg_arr, $exit_code);
    unset($erg_arr);
    if($exit_code == 0){
      exec("cp -f ".$crt_file." ".$mod->system->server_conf["server_path_httpd_root"]."/web".$doc_id."/ssl/invalid.crt_");
      $mod->file->wf($crt_file, $ssl_cert_alt);
      $mod->db->query("UPDATE isp_isp_web SET ssl_cert = '$ssl_cert_alt' WHERE doc_id = '$doc_id'");
      $mod->system->data["isp_isp_web"][$doc_id]["ssl_cert"] = $ssl_cert_alt;
    }
  }

  if($ssl["ssl_action"] == "delete"){
    exec("rm -f $key_file");
    exec("rm -f $key_file2");
    exec("rm -f $csr_file");
    exec("rm -f $crt_file");
    $mod->db->query("UPDATE isp_isp_web SET ssl_request = '', ssl_cert = '', ssl_country = '', ssl_state = '', ssl_locality = '', ssl_organization = '', ssl_organization_unit = '', ssl_days = '365' WHERE doc_id = '$doc_id'");
    $mod->system->data["isp_isp_web"][$doc_id]["ssl_request"] = '';
    $mod->system->data["isp_isp_web"][$doc_id]["ssl_cert"] = '';
    $mod->system->data["isp_isp_web"][$doc_id]["ssl_country"] = '';
    $mod->system->data["isp_isp_web"][$doc_id]["ssl_state"] = '';
    $mod->system->data["isp_isp_web"][$doc_id]["ssl_locality"] = '';
    $mod->system->data["isp_isp_web"][$doc_id]["ssl_organization"] = '';
    $mod->system->data["isp_isp_web"][$doc_id]["ssl_organization_unit"] = '';
    $mod->system->data["isp_isp_web"][$doc_id]["ssl_days"] = 365;
  }

  $mod->db->query("UPDATE isp_isp_web SET ssl_action = '' WHERE doc_id = '$doc_id'");
  $mod->system->data["isp_isp_web"][$doc_id]["ssl_action"] = '';
}

function make_cron($doc_id){
  global $mod;
  $user = $mod->system->data["isp_isp_user"][$doc_id];

  // doc_id des Webs bestimmen
  $sql = "SELECT * FROM isp_dep WHERE child_doc_id = '$doc_id' AND child_doctype_id = '".$this->user_doctype_id."'";
  $web_dep = $mod->db->queryOneRecord($sql);
  $web_doc_id = $web_dep["parent_doc_id"];
  $user_cron = $user["user_cron"];
  $user_name = $user["user_name"];
  $cron_file = $mod->system->server_conf["server_path_httpd_root"]."/web".$web_doc_id."/user/".$user_name."/cron";
  $mod->file->wf($cron_file, $user_cron);
  exec("crontab -u $user_name $cron_file");
  exec("rm -f $cron_file");
}

function apache_user(){
  global $mod;
  $httpd_conf = $mod->system->server_conf["dist_httpd_conf"];
  $includes = $mod->file->find_includes($httpd_conf);
  $anz_includes = sizeof($includes);
  for($i=0;$i<$anz_includes;$i++){
    $includes[$i] = $mod->file->unix_nl($mod->file->no_comments($includes[$i]));
    if($line = $mod->system->grep($includes[$i], "User", "w")){
      $lines = explode("\n", $line);
      $line = $lines[0];
      $line = trim($line);
      while(strstr($line, "  ")){
        $line = str_replace("  ", " ", $line);
      }
      list($f1, $apache_user) = explode(" ", $line);
      $apache_user = trim($apache_user);
      $i = $anz_includes;
    }
  }
  if(isset($apache_user) && $mod->system->is_user($apache_user)){
    return $apache_user;
  } else {
    return "root";
  }
}

function apache_version(){
  exec("httpd -v", $apache_version);
  while(strstr($apache_version[0], "  ")){
    $apache_version[0] = str_replace("  ", " ", $apache_version[0]);
  }
  $version = exec("echo '".$apache_version[0]."' | cut -f3 -d' ' | cut -f2 -d'/'");
  $version = substr($version,0,1);
  return $version;
}

function httpd_syntax_check($doc_id, $web_httpd_include, $is_web_httpd_include){
  global $mod;

  $test_vhost = "\n".'<VirtualHost 10.0.0.1:80>
ServerName www.test.tld:80
ServerAdmin webmaster@test.tld
DocumentRoot /home
'.$web_httpd_include.'
</VirtualHost>';

  $mod->file->af($this->vhost_conf, $test_vhost);

  if($go_info["server"]["httpd_check"] == 1) {
    $httpd_syntax_check = $mod->log->caselog("httpd -t &> /dev/null", $this->FILE, __LINE__);
  } else {
    // return always 0 = check OK
    $httpd_syntax_check = 0;
  }

  $conf = $mod->file->rf($this->vhost_conf);

  $conf = str_replace($test_vhost, "", $conf);

  $mod->file->wf($this->vhost_conf, $conf);

  if($httpd_syntax_check != 0){
    $web_httpd_include_lines = explode("\n", $web_httpd_include);
    for($i=0;$i<sizeof($web_httpd_include_lines);$i++){
      if(substr($web_httpd_include_lines[$i],0,1) != "#") $web_httpd_include_lines[$i] = "# ".$web_httpd_include_lines[$i]." # NOT SUPPORTED!";
    }
    $web_httpd_include = implode("\n", $web_httpd_include_lines);
    if($is_web_httpd_include) $mod->db->query("UPDATE isp_isp_web SET web_httpd_include = '$web_httpd_include' WHERE doc_id = '$doc_id'");
    $mod->system->data["isp_isp_web"][$doc_id]["web_httpd_include"] = $web_httpd_include;
  }

  return $web_httpd_include;
}

function make_ftp($server_id){
  global $mod;

  if($mod->system->server_conf["server_ftp_typ"] == "proftpd"){
    $mod->log->caselog("cp -fr $this->ftp_conf $this->ftp_conf~", $this->FILE, __LINE__);
  }

  // Template Öffnen
  if($mod->system->server_conf["server_ftp_typ"] == "proftpd"){
    $mod->tpl->clear_all();
    $mod->tpl->define( array(table    => "proftpd_ispconfig.conf.master"));
    $mod->tpl->define_dynamic( "vhost", "table" );
  } else {
    $mod->tpl->clear_all();
    $mod->tpl->define( array(table    => "vsftpd.conf.master"));
  }


  $ips = $mod->system->data["isp_server_ip"];

  foreach($ips as $ip){
    $web = $mod->db->queryOneRecord("SELECT * FROM isp_nodes,isp_isp_web WHERE isp_isp_web.server_id = '$server_id' AND isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = '".$this->web_doctype_id."' AND isp_nodes.status = '1' AND isp_isp_web.web_ip = '".$ip["server_ip"]."' AND isp_isp_web.web_anonftp = '1'");

    if(!empty($web)){
          $document_root = $mod->system->server_conf["server_path_httpd_root"]."/"."web".$web["doc_id"];
          $directory_mode = octdec($this->directory_mode);
          if(!is_dir($document_root."/ftp")) mkdir($document_root."/ftp", $directory_mode);
          if(!is_dir($document_root."/ftp/incoming")) mkdir($document_root."/ftp/incoming", $directory_mode);
          exec("chmod 775 ".$document_root."/ftp &> /dev/null");
          exec("chmod 773 ".$document_root."/ftp/incoming &> /dev/null");
          //gibt es Admin-User für das Web? Wenn er gelöscht wurde, evtl. vorh. Autoresponder-Dateien löschen
          $sql = "SELECT * FROM isp_nodes, isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = '".$web["doc_id"]."' AND isp_dep.parent_doctype_id = '".$this->web_doctype_id."' AND isp_dep.child_doc_id = isp_isp_user.doc_id AND isp_dep.child_doctype_id = '".$this->user_doctype_id."' AND isp_isp_user.user_admin = '1' AND isp_nodes.doc_id = isp_isp_user.doc_id AND isp_nodes.doctype_id = '".$this->user_doctype_id."' AND isp_nodes.status = '1'";
          $admin_user = $mod->db->queryOneRecord($sql);
          if(!empty($admin_user)){
            $owner = $admin_user["user_username"];
          } else {
            $owner = $this->apache_user();
          }
          exec("chown -R ".$owner.":web".$web["doc_id"]." ".$document_root."/ftp &> /dev/null");
          $mod->log->caselog("groupadd -g ".($mod->system->server_conf["userid_von"] + 2000 + $web["doc_id"])." web".$web["doc_id"]."_anonftp &> /dev/null", $this->FILE, __LINE__);
          $mod->log->caselog("useradd -d ".$document_root."/ftp -g web".$web["doc_id"]."_anonftp -m -s /bin/false -u ".($mod->system->server_conf["userid_von"] + 2000 + $web["doc_id"])." web".$web["doc_id"]."_anonftp &> /dev/null", $this->FILE, __LINE__);

          // Diskquota setzen für Anonymous FTP-User (entspricht max. Datenmenge, die per Anonymous FTP hochgeladen werfen kann)
          if(intval($web["web_anonftplimit"]) > 0){
            $blocks_soft = $web["web_anonftplimit"] * 1024;
            $blocks_hard = $blocks_soft + 1024;
          } else {
            $blocks_soft = $blocks_hard = 0;
          }
          if(!strstr($mod->system->server_conf["dist"], "freebsd")){
            $mod->log->caselog("setquota -u web".$web["doc_id"]."_anonftp ".$blocks_soft." ".$blocks_hard." 0 0 -a &> /dev/null", $this->FILE, __LINE__);
            $mod->log->caselog("setquota -T -u web".$web["doc_id"]."_anonftp 604800 604800 -a &> /dev/null", $this->FILE, __LINE__);
            $mod->log->caselog("setquota -g web".$web["doc_id"]."_anonftp ".$blocks_soft." ".$blocks_hard." 0 0 -a &> /dev/null", $this->FILE, __LINE__);
            $mod->log->caselog("setquota -T -g web".$web["doc_id"]."_anonftp 604800 604800 -a &> /dev/null", $this->FILE, __LINE__);
          } else {
            if($q_dirs = $mod->system->quota_dirs()){
              if(!empty($q_dirs)){
                foreach($q_dirs as $q_dir){
                  $mod->log->caselog("setquota -u -f ".$q_dir." -bh".$blocks_hard." -bs".$blocks_soft." web".$web["doc_id"]."_anonftp &> /dev/null", $this->FILE, __LINE__);
                  $mod->log->caselog("setquota -g -f ".$q_dir." -bh".$blocks_hard." -bs".$blocks_soft." web".$web["doc_id"]."_anonftp &> /dev/null", $this->FILE, __LINE__);
                }
              }
            }
          }

          exec("grep web".$web["doc_id"]."_anonftp /home/admispconfig/ispconfig/users &> /dev/null", $grep_result, $ret_val);
          unset($grep_result);
          if($ret_val != 0) exec("echo web".$web["doc_id"]."_anonftp >> /home/admispconfig/ispconfig/users");
          unset($ret_val);
          $anon_ftp = "<Anonymous ".$document_root."/ftp>
          User                          web".$web["doc_id"]."_anonftp
          Group                         web".$web["doc_id"]."_anonftp
          UserAlias                     anonymous web".$web["doc_id"]."_anonftp
          UserAlias                     guest web".$web["doc_id"]."_anonftp
          MaxClients                    10
          <Directory *>
            <Limit WRITE>
              DenyAll
            </Limit>
          </Directory>
          <Directory ".$document_root."/ftp/incoming>
            Umask                       002
            <Limit STOR>
              AllowAll
            </Limit>
            <Limit READ>
              DenyAll
            </Limit>
          </Directory>
        </Anonymous>";
       $anon_enable = "YES";
       $anon_ftp_user = "ftp_username=web".$web["doc_id"]."_anonftp";
     } else {
       $anon_ftp = "";
       $anon_enable = "NO";
       $anon_ftp_user = "#";
     }
      // Variablen zuweisen
      if($mod->system->server_conf["server_ftp_typ"] == "proftpd"){
        $mod->tpl->assign( array( IP => $ip["server_ip"],
                            ANON_FTP => $anon_ftp));
        $mod->tpl->parse(VHOST,".vhost");
      } else {
        $mod->tpl->assign( array( ANON_ENABLE => $anon_enable,
                            ANON_FTP_USER => $anon_ftp_user));
        $mod->tpl->parse(TABLE, table);
        $vhost_text = $mod->tpl->fetch();

        $mod->file->wf("/etc/vsftpd_".$ip["server_ip"].".conf", $vhost_text);
        //Leerzeilen löschen
        $mod->file->remove_blank_lines("/etc/vsftpd_".$ip["server_ip"].".conf");
      }

  }

  if($mod->system->server_conf["server_ftp_typ"] == "proftpd"){
    $mod->tpl->parse(TABLE, table);

    $vhost_text = $mod->tpl->fetch();

    $mod->file->wf($this->ftp_conf, $vhost_text);
    //Leerzeilen löschen
    $mod->file->remove_blank_lines($this->ftp_conf);
  }

  /*
  // Anonymous FTP-User löschen, wenn nicht mehr gebraucht
  $webs = $mod->db->queryAllRecords("SELECT * FROM isp_isp_web WHERE server_id = '$server_id' AND web_anonftp != '1'");
  if(!empty($webs)){
    foreach($webs as $web){
      //exec("userdel web".$web["doc_id"]."_anonftp &> /dev/null");
      $mod->system->deluser("web".$web["doc_id"]."_anonftp");
      //exec("groupdel web".$web["doc_id"]."_anonftp &> /dev/null");
      $mod->system->delgroup("web".$web["doc_id"]."_anonftp");
      exec("grep -v web".$web["doc_id"]."_anonftp /home/admispconfig/ispconfig/users > /home/admispconfig/ispconfig/users.tmp");
      exec("mv -f /home/admispconfig/ispconfig/users.tmp /home/admispconfig/ispconfig/users &> /dev/null");
    }
  }
  */

  // Anonymous FTP-User löschen, wenn nicht mehr gebraucht
  ob_start();
  system("grep _anonftp /home/admispconfig/ispconfig/users | grep web");
  $output = trim(ob_get_contents());
  ob_end_clean();
  if($output != ""){
    $lines = explode("\n", $output);
    foreach($lines as $line){
      $line = trim($line);
      if($line != ""){
        $web_id = str_replace("web", "", $line);
        $web_id = str_replace("_anonftp", "", $web_id);
        if(isset($mod->system->data["isp_isp_web"][$web_id]) && ($mod->system->data["isp_isp_web"][$web_id]["web_anonftp"] != 1)){
          $mod->system->deluser("web".$web_id."_anonftp");
          $mod->system->delgroup("web".$web_id."_anonftp");
          exec("grep -v web".$web_id."_anonftp /home/admispconfig/ispconfig/users > /home/admispconfig/ispconfig/users.tmp");
          exec("mv -f /home/admispconfig/ispconfig/users.tmp /home/admispconfig/ispconfig/users &> /dev/null");
        }
      }
    }
  }

}

function make_firewall(){
  global $mod;

  $new_firewall_config = $mod->db->queryAllRecords("SELECT * FROM isp_firewall WHERE status != ''");

  if(!empty($new_firewall_config)){
    // Template öffnen
    $mod->tpl->clear_all();

    if(!strstr($mod->system->server_conf["dist"], "freebsd")){
      $mod->tpl->define( array(table    => "bastille-firewall.cfg.master"));
      $services = $mod->db->queryAllRecords("SELECT * FROM isp_firewall, sys_nodes WHERE isp_firewall.doc_id = sys_nodes.doc_id AND sys_nodes.doctype_id = '".$this->firewall_doctype_id."' AND sys_nodes.status = '1' AND isp_firewall.dienst_aktiv = 'ja'");

      $tcp_public_services = "";
      $udp_public_services = "";
      if(!empty($services)){
        foreach($services as $service){
           if($service["dienst_typ"] == "tcp"){
             $tcp_public_services .= $service["dienst_port"]." ";
             $tcp_ports[] = $service["dienst_port"];
           }
           if($service["dienst_typ"] == "udp"){
             $udp_public_services .= $service["dienst_port"]." ";
             $udp_ports[] = $service["dienst_port"];
           }
        }
        if(!in_array("22", $tcp_ports)) $tcp_public_services .= "22 ";
        if(!in_array("81", $tcp_ports)) $tcp_public_services .= "81 ";
      } else {
        $tcp_public_services = "22 81";
      }

      $mod->tpl->assign( array( DNS_SERVERS => "",
                           TCP_PUBLIC_SERVICES => trim($tcp_public_services),
                           UDP_PUBLIC_SERVICES => trim($udp_public_services)));
      $mod->tpl->parse(TABLE, table);
      $firewall_text = $mod->tpl->fetch();

      $mod->file->wf("/etc/Bastille/bastille-firewall.cfg", $firewall_text);

    } else {  // FreeBSD

      $mod->tpl->define( array(table    => "freebsd_firewall.master"));
      $services = $mod->db->queryAllRecords("SELECT * FROM isp_firewall, sys_nodes WHERE isp_firewall.doc_id = sys_nodes.doc_id AND sys_nodes.doctype_id = '".$this->firewall_doctype_id."' AND sys_nodes.status = '1' AND isp_firewall.dienst_aktiv = 'ja'");

      $tcp_public_services = "";
      $udp_public_services = "";
      if(!empty($services)){
        foreach($services as $service){
           if($service["dienst_typ"] == "tcp"){
             $tcp_public_services .= $service["dienst_port"]." ";
             $tcp_ports[] = $service["dienst_port"];
           }
           if($service["dienst_typ"] == "udp"){
             $udp_public_services .= $service["dienst_port"]." ";
             $udp_ports[] = $service["dienst_port"];
           }
        }
        if(!in_array("22", $tcp_ports)) $tcp_public_services .= "22 ";
        if(!in_array("81", $tcp_ports)) $tcp_public_services .= "81 ";
      } else {
        $tcp_public_services = "22 81";
      }

      $tcp_public_services = str_replace(" ", ",", trim($tcp_public_services));
      $udp_public_services = str_replace(" ", ",", trim($udp_public_services));
      if($tcp_public_services == ""){
        $tcp_public_services_comment = "#";
      } else {
        $tcp_public_services_comment = "";
      }
      if($udp_public_services == ""){
        $udp_public_services_comment = "#";
      } else {
        $udp_public_services_comment = "";
      }

      $mod->tpl->assign( array( TCP_PUBLIC_SERVICES_COMMENT => $tcp_public_services_comment,
                           TCP_PUBLIC_SERVICES => trim($tcp_public_services),
                           UDP_PUBLIC_SERVICES_COMMENT => $udp_public_services_comment,
                           UDP_PUBLIC_SERVICES => trim($udp_public_services)));
      $mod->tpl->parse(TABLE, table);
      $firewall_text = $mod->tpl->fetch();

      $mod->file->wf("/etc/rc.d/bastille-firewall", $firewall_text);
      chmod("/etc/rc.d/bastille-firewall", 0700);
    }

    $mod->db->query("UPDATE isp_firewall SET status = ''");
    $this->firewall_restart();
  }
}

function dienste(){
  global $mod;

  $dist = $mod->system->server_conf["dist"];
  $dist_init_scripts = $mod->system->server_conf["dist_init_scripts"];
  $dist_httpd_daemon = $mod->system->server_conf["dist_httpd_daemon"];
  $dist_bind_init_script = $mod->system->server_conf["dist_bind_init_script"];
  $dist_ftp_version = $mod->system->server_conf["dist_ftp_version"];

  $dienste = $mod->system->data["isp_dienste"];

  if($dienste["status"] != ''){

    foreach($dienste as $key => $val){
      if($val == "restart"){
        $mod->db->query("UPDATE isp_dienste SET ".$key." = 'on' WHERE doc_id = '".$mod->system->data["isp_dienste"]["doc_id"]."'");
        $mod->system->data["isp_dienste"][$key] = "on";
      }
    }

  if(strstr($dist, "freebsd")){
    exec("netstat -ta | grep tcp | cut -f2 -d. | cut -f1 -d' '", $services);
  } else {
    exec("netstat -ta | grep tcp | cut -f2 -d: | cut -f1 -d' '", $services);
  }
  $services = array_unique($services);
  foreach($services as $service){
    if(!is_numeric($service)){
      $port = getservbyname($service, "tcp");
    } else {
      $port = $service;
    }
    if(trim($port) != "") $ports[] = $port;
  }
  asort($ports);
  if(in_array("80", $ports) && $dienste["dienst_www_status"] == "on"){
    //nichts
  }
  if(in_array("80", $ports) && $dienste["dienst_www_status"] == "off"){
    //Apache stoppen
    $mod->system->daemon_init($dist_httpd_daemon, "stop");
  }
  if(in_array("80", $ports) && $dienste["dienst_www_status"] == "restart"){
    //Apache neu starten
    $this->apache_restart();
  }
  if(!in_array("80", $ports) && $dienste["dienst_www_status"] == "on"){
    //Apache starten
    $mod->system->daemon_init($dist_httpd_daemon, "start");
  }
  if(!in_array("80", $ports) && $dienste["dienst_www_status"] == "off"){
    //nichts
  }
  if(!in_array("80", $ports) && $dienste["dienst_www_status"] == "restart"){
    //Apache starten
    $mod->system->daemon_init($dist_httpd_daemon, "start");
  }

  if(in_array("25", $ports) && $dienste["dienst_smtp_status"] == "on"){
    //nichts
  }
  if(in_array("25", $ports) && $dienste["dienst_smtp_status"] == "off"){
    //Sendmail stoppen
    $mod->system->daemon_init($mod->system->server_conf["server_mta"], "stop");
  }
  if(in_array("25", $ports) && $dienste["dienst_smtp_status"] == "restart"){
    //Sendmail neu starten
    $mod->mail->smtp_restart();
  }
  if(!in_array("25", $ports) && $dienste["dienst_smtp_status"] == "on"){
    //Sendmail starten
    $mod->system->daemon_init($mod->system->server_conf["server_mta"], "start");
  }
  if(!in_array("25", $ports) && $dienste["dienst_smtp_status"] == "off"){
    //nichts
  }
  if(!in_array("25", $ports) && $dienste["dienst_smtp_status"] == "restart"){
    //Sendmail starten
    $mod->system->daemon_init($mod->system->server_conf["server_mta"], "start");
  }

  if(in_array("53", $ports) && $dienste["dienst_dns_status"] == "on"){
    //nichts
  }
  if(in_array("53", $ports) && $dienste["dienst_dns_status"] == "off"){
    //BIND stoppen
    $mod->system->daemon_init($dist_bind_init_script, "stop");
  }
  if(in_array("53", $ports) && $dienste["dienst_dns_status"] == "restart"){
    //BIND neu starten
    $mod->dns->named_restart();
  }
  if(!in_array("53", $ports) && $dienste["dienst_dns_status"] == "on"){
    //BIND starten
    $mod->system->daemon_init($dist_bind_init_script, "start");
  }
  if(!in_array("53", $ports) && $dienste["dienst_dns_status"] == "off"){
    //nichts
  }
  if(!in_array("53", $ports) && $dienste["dienst_dns_status"] == "restart"){
    //BIND starten
    $mod->system->daemon_init($dist_bind_init_script, "start");
  }

  if(in_array("3306", $ports) && $dienste["dienst_mysql_status"] == "on"){
    //nichts
  }
  if(in_array("3306", $ports) && $dienste["dienst_mysql_status"] == "restart"){
    //MySQL neu starten
    $mod->db->query('FLUSH PRIVILEGES');
  }
  if(!in_array("3306", $ports) && $dienste["dienst_mysql_status"] == "on"){
    //MySQL starten
    $mod->system->daemon_init("mysql", "start");
  }
  if(!in_array("3306", $ports) && $dienste["dienst_mysql_status"] == "restart"){
    //MySQL starten
    $mod->system->daemon_init("mysql", "start");
  }

  if(in_array("21", $ports) && $dienste["dienst_ftp_status"] == "on"){
    //nichts
  }
  if(in_array("21", $ports) && $dienste["dienst_ftp_status"] == "off"){
    //FTP stoppen
    if($dist_ftp_version == "standalone"){
      $mod->system->daemon_init($mod->system->server_conf["server_ftp_typ"], "stop");
    } else {
      $mod->system->daemon_init("ispconfig_tcpserver", "stop");
    }
  }
  if(in_array("21", $ports) && $dienste["dienst_ftp_status"] == "restart"){
    //FTP neu starten
    if($dist_ftp_version == "standalone"){
      $mod->system->daemon_init($mod->system->server_conf["server_ftp_typ"], "restart");
    } else {
      $mod->system->daemon_init("ispconfig_tcpserver", "restart");
    }
  }
  if(!in_array("21", $ports) && $dienste["dienst_ftp_status"] == "on"){
    //FTP starten
    if($dist_ftp_version == "standalone"){
      $mod->system->daemon_init($mod->system->server_conf["server_ftp_typ"], "start");
    } else {
      $mod->system->daemon_init("ispconfig_tcpserver", "start");
    }
  }
  if(!in_array("21", $ports) && $dienste["dienst_ftp_status"] == "off"){
    //nichts
  }
  if(!in_array("21", $ports) && $dienste["dienst_ftp_status"] == "restart"){
    //FTP starten
    if($dist_ftp_version == "standalone"){
      $mod->system->daemon_init($mod->system->server_conf["server_ftp_typ"], "start");
    } else {
      $mod->system->daemon_init("ispconfig_tcpserver", "start");
    }
  }

  if($dienste["dienst_firewall_status"] == "on"){
    $mod->system->daemon_init("bastille-firewall", "restart");
  }
  if($dienste["dienst_firewall_status"] == "restart"){
    $mod->system->daemon_init("bastille-firewall", "restart");
  }
  if($dienste["dienst_firewall_status"] == "off"){
    $mod->system->daemon_init("bastille-firewall", "stop");
  }

  $mod->db->query("UPDATE isp_dienste SET status = ''");
  $mod->system->data["isp_dienste"]["status"] = "";
  }
}

function monitor(){
  global $mod;

  $new_monitor_config = $mod->db->queryAllRecords("SELECT * FROM isp_monitor WHERE status != ''");

  if(!empty($new_monitor_config)){
    // Template Öffnen
    $mod->tpl->clear_all();
    $mod->tpl->define( array(table    => "check_services.php.master"));
    $mod->tpl->define_dynamic( "monitor", "table" );

    $warnings = $mod->db->queryAllRecords("SELECT * FROM isp_monitor, sys_nodes WHERE isp_monitor.doc_id = sys_nodes.doc_id AND sys_nodes.doctype_id = '".$this->monitor_doctype_id."' AND sys_nodes.status = '1' AND isp_monitor.dienst_monitor = 'ja'");
    if(!empty($warnings)){
      $server_name = $mod->system->hostname();
      foreach($warnings as $warning){
        if(strstr($warning["dienst_host"], "://")){
          $host = explode("://", $warning["dienst_host"]);
          $warning["dienst_host"] = $host[1];
        }

        if(!empty($warning["dienst_run_offline"])){
            $offline_exec = 'exec("'.escapeshellcmd($warning["dienst_run_offline"]).'");';
          } else {
            $offline_exec = "";
          }

        if($warning["dienst_name"] == "ftp"){
          $warnung = 'if(!$this->_check_ftp("'.$warning["dienst_host"].'",'.$warning["dienst_port"].')) {
    $warning .= "WARNING: service '.$warning["dienst_name"].' not running (server: '.$server_name.', port: '.$warning["dienst_port"].')!\n\n";
    '.$offline_exec.'
    }';
        } else {
          if($warning["dienst_name"] != "etc"){
            $warnung = 'if(!$this->_check_tcp("'.$warning["dienst_host"].'",'.$warning["dienst_port"].')) {
    $warning .= "WARNING: service '.$warning["dienst_name"].' not running (server: '.$server_name.', port: '.$warning["dienst_port"].')!\n\n";
    '.$offline_exec.'
    }';
          } else {
            $warnung = 'if(!$this->_check_'.$warning["dienst_typ"].'("'.$warning["dienst_host"].'",'.$warning["dienst_port"].')) {
    $warning .= "WARNING: service not running (server: '.$server_name.', port: '.$warning["dienst_port"].')!\n\n";
    '.$offline_exec.'
    }';
          }
        }
        $mod->tpl->assign( array( WARNUNG => $warnung));
        $mod->tpl->parse(MONITOR,".monitor");
      }
    } else {
      $mod->tpl->assign( array( WARNUNG => ""));
      $mod->tpl->parse(MONITOR,".monitor");
    }



    $mod->tpl->parse(TABLE, table);

    $monitor_text = $mod->tpl->fetch();

    $mod->file->wf("/root/ispconfig/scripts/shell/check_services.php", $monitor_text);

    $mod->db->query("UPDATE isp_monitor SET status = ''");
  }
}

function apache_restart(){
  global $mod, $go_info;

  $dist_httpd_daemon = $mod->system->server_conf["dist_httpd_daemon"];
  $dist_httpd_conf_dir = $mod->system->server_conf["server_path_httpd_conf"];

  if($go_info["server"]["httpd_check"] == 1) {
    $ret_val = $mod->log->caselog("httpd -t  &> /dev/null", $this->FILE, __LINE__);
  } else {
    // return always 0 = check OK
    $ret_val = 0;
  }

  if($ret_val == 0){
    $mod->log->ext_log("httpd syntax ok", 1, $this->FILE, __LINE__);
  } else {
    if($go_info["isp"]["httpd"]["use_old_conf_on_errors"]){
      $datum = date("d-m-y_H-i-s");
      $mod->log->ext_log("httpd syntax seems to contain errors, restarting with old configuration", 2, $this->FILE, __LINE__);
      $mod->log->caselog("mv -f ".$dist_httpd_conf_dir."/vhosts/Vhosts_ispconfig.conf ".$dist_httpd_conf_dir."/vhosts/Vhosts_ispconfig.conf_".$datum, $this->FILE, __LINE__);
      $mod->log->caselog("mv -f ".$dist_httpd_conf_dir."/vhosts/Vhosts_ispconfig.conf~ ".$dist_httpd_conf_dir."/vhosts/Vhosts_ispconfig.conf", $this->FILE, __LINE__);
    } else {
      $mod->log->ext_log("httpd syntax seems to contain errors", 2, $this->FILE, __LINE__);
    }
  }
  $mod->system->daemon_init($dist_httpd_daemon, "restart");
}

function apache_reload(){
  global $mod, $go_info;

  $dist_httpd_daemon = $mod->system->server_conf["dist_httpd_daemon"];
  $dist_httpd_conf_dir = $mod->system->server_conf["server_path_httpd_conf"];

  if($go_info["server"]["httpd_check"] == 1) {
    $ret_val = $mod->log->caselog("httpd -t  &> /dev/null", $this->FILE, __LINE__);
  } else {
    // return always 0 = check OK
    $ret_val = 0;
  }

  if($ret_val == 0){
    $mod->log->ext_log("httpd syntax ok", 1, $this->FILE, __LINE__);
  } else {
    if($go_info["isp"]["httpd"]["use_old_conf_on_errors"]){
      $datum = date("d-m-y_H-i-s");
      $mod->log->ext_log("httpd syntax seems to contain errors, reloading with old configuration", 2, $this->FILE, __LINE__);
      $mod->log->caselog("mv -f ".$dist_httpd_conf_dir."/vhosts/Vhosts_ispconfig.conf ".$dist_httpd_conf_dir."/vhosts/Vhosts_ispconfig.conf_".$datum, $this->FILE, __LINE__);
      $mod->log->caselog("mv -f ".$dist_httpd_conf_dir."/vhosts/Vhosts_ispconfig.conf~ ".$dist_httpd_conf_dir."/vhosts/Vhosts_ispconfig.conf", $this->FILE, __LINE__);
    } else {
      $mod->log->ext_log("httpd syntax seems to contain errors", 2, $this->FILE, __LINE__);
    }
  }
  $mod->system->daemon_init($dist_httpd_daemon, "reload");
}

function ftp_restart(){
  global $mod;

  $dist_init_scripts = $mod->system->server_conf["dist_init_scripts"];
  $dist_ftp = $mod->system->server_conf["server_ftp_typ"];
  $dist_ftp_version = $mod->system->server_conf["dist_ftp_version"];

  if($dist_ftp_version == "standalone"){
    $mod->system->daemon_init($mod->system->server_conf["server_ftp_typ"], "restart");
  } else {
    $mod->system->daemon_init($dist_ftp_version, "restart");
  }
}

function firewall_restart(){
  global $mod;

  $dist_init_scripts = $mod->system->server_conf["dist_init_scripts"];

  $dienst = $mod->system->data["isp_dienste"];
  if($dienst["dienst_firewall_status"] != "off"){
    $mod->system->daemon_init("bastille-firewall", "restart");
  }
}

function web_user_clean(){
  global $mod;
  if($items = $mod->db->queryAllRecords("SELECT * FROM del_status WHERE status = 'd'")){
    foreach($items as $item){
      switch ($item["doctype_id"]) {
      case 1013:
          //Verzeichnisse löschen
          $web_path = $item["pfad"];
          if($item["web_host"] == ""){
            $web_path_realname = $mod->system->server_conf["server_path_httpd_root"] ."/". $item["web_domain"];
          } else {
            $web_path_realname = $mod->system->server_conf["server_path_httpd_root"] ."/". $item["web_host"] . "." . $item["web_domain"];
          }
          $mod->log->caselog("rm -fr $web_path_realname", $this->FILE, __LINE__);
          $mod->log->caselog("rm -fr $web_path", $this->FILE, __LINE__);

          // Gruppe des Webs löschen
          if(!is_dir($web_path)){
            if(!strstr($mod->system->server_conf["dist"], "freebsd")){
              $mod->log->caselog("setquota -g web".$item['doc_id']." 0 0 0 0 -a &> /dev/null", $this->FILE, __LINE__);
            } else {
              if($q_dirs = $mod->system->quota_dirs()){
                if(!empty($q_dirs)){
                  foreach($q_dirs as $q_dir){
                    $mod->log->caselog("setquota -g -f ".$q_dir." -bh0 -bs0 web".$item['doc_id']." &> /dev/null", $this->FILE, __LINE__);
                  }
                }
              }
            }

            $mod->system->delgroup("web".$item['doc_id']);
          }

          $mod->db->query("DELETE FROM del_status WHERE id = '".$item["id"]."'");
      break;
      case 1014:
          //User-Verzeichnis löschen
          if(is_dir($item["pfad"])){
            $mod->log->caselog("rm -fr ".$item["pfad"], $this->FILE, __LINE__);
          }
          $mod->system->deluser($item["name"]);
          $mod->db->query("DELETE FROM del_status WHERE id = '".$item["id"]."'");
      break;
      }
    }
  }
  return true;
}

}
?>