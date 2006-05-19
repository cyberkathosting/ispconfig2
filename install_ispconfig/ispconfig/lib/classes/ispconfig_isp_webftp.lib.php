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

if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class isp_webftp 
{

	var $username;
	var $passwort;
	var $server;
	var $conn_id;
	var $connected = false;
	var $dir_tree = '';

	function isp_webftp() 
	{
		global $go_info;
		
		if($go_info["server"]["mode"] == "demo") {
			// Lade Daten aus Config ...
			$this->username = "web1_user";
			$this->passwort = "falsch";
			$this->server 	= "192.168.0.200";
		} else {
			// Lade Daten aus Config ...
			$this->username = $go_info["webftp"]["user"];
			$this->passwort = $go_info["webftp"]["passwort"];
			$this->server = 'localhost';
		}
		
		if($go_info["webftp"]["dir_tree"] != '') $this->dir_tree = $go_info["webftp"]["dir_tree"];
	}
	
	function webftp_connect () {
		global $go_api, $go_info,$s;
		$this->conn_id = @ftp_connect($this->server) or $go_api->errorMessage($go_api->lng("Could not connect to").$this->server ." <a href'=".$go_info["server"]["server_url"]."/isp_file/logout.php?s=$s'>".$go_api->lng("Next >>")."</a>"); 
		@ftp_login($this->conn_id, $this->username, $this->passwort) or $go_api->errorMessage($go_api->lng("Could not connect as user").$this->username." <a href='".$go_info["server"]["server_url"]."/isp_file/logout.php?s=$s'>".$go_api->lng("Next >>")."</a>");
		$this->connected = true;
	}
	
	function webftp_close() {
		//@ftp_close($this->conn_id) or die("Cannot Close FTP connection");
		@ftp_close($this->conn_id);
		$this->connected = false;
	}
	
	function webftp_tree () {
		global $go_api,$go_info;
		if($this->dir_tree == '') {
			if(!$this->connected) $this->webftp_connect();
			$this->webftp_read_dir();
			$go_info["webftp"]["dir_tree"] = $this->dir_tree;
			$go_api->session->save();
		}
		return $this->dir_tree;
	}
	
	function webftp_read_dir($start = '') {
		global $go_api,$web_id;
		
		$contents = ftp_rawlist($this->conn_id, $start);
		if(is_array($contents)) {
		  foreach($contents as $file) {
			$tmp = preg_split ("/[\s,]+/", $file);
			list($perms,$tmp1,$tmp2,$group,$size,$month,$day,$time,$name) = $tmp;
			$name = trim($name);
			
        	if(substr($perms,0,1) == 'd') {
				if($name != "." and $name != "..") {
            		//$tverz = str_replace("/","root",$start);
					//$tverz = "root".$start;
            		//$tverz = str_replace("/",":",$tverz);
					$tverz = addslashes($start);
					$file = addslashes($name);
            		$msecret = md5($tverz.$secret);
            		//$this->dir_tree .= "menuDaten.neu(new VerzEintrag('$tverz:$name','$tverz','$name','','','edit/ordner.php?s=$s&ordner=$tverz:$name&web_id=$web_id'));\n";
					$this->dir_tree .= "$tverz/$name\n";
					//echo "$tverz:$name\n";
            		$this->webftp_read_dir($start.'/'.$name);
				}
			} 
    	  }
		}
	}
    
	function webftp_read($start = '') {
		global $go_api,$web_id;
		
		if(!$this->connected) $this->webftp_connect();
		
		if($contents = @ftp_rawlist($this->conn_id, $start)) {
		  if(is_array($contents)) {
			foreach($contents as $file) {
				$tmp = preg_split ("/[\s,]+/", $file);
				list($perms,$tmp1,$tmp2,$group,$size,$month,$day,$time,$name) = $tmp;
				$name = trim($name);
				
				// Nur Einträge die nicht mit einem Punkt beginnen
				if(substr($name,0,1) != '.') {
					$item["perms"] = $perms;
					$item["tmp1"] = $tmp1;
					$item["tmp2"] = $tmp2;
					$item["group"] = $group;
					$item["size"] = $size;
					$item["month"] = $month;
					$item["day"] = $day;
					$item["time"] = $time;
					$item["name"] = $name;
			
					$list[] = $item;
				}
		     }
		   }
			$this->webftp_close();
		
			return $list;
		} else {
			return false;
		}
	}
	
	function webftp_check_params($path) {
		
		global $go_api,$web_id,$go_info;
		
		if(substr($path,0,4) != "root") $go_api->errorMessage($go_api->lng("Parameterübergabe unvollständig"));
	
		if(!isset($web_id)) $go_api->errorMessage($go_api->lng("Parameterübergabe unvollständig"));
    	$web_id = intval($web_id);
    	if(!is_int($web_id) or $web_id == 0) $go_api->errorMessage($go_api->lng("Ungültiges Format der web_id."));
    	//Checke Userrechte am Web
    	if(!$row = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doctype_id = ".$go_api->isp_web->web_doctype_id." and doc_id = '$web_id'")) $go_api->errorMessage("Ungültige web_id.");

    	if($go_info["user"]["userid"] != $row["userid"]) {
        	$go_api->auth->check_write($row["groupid"]);
    	} else {
        	$go_api->auth->check_write(0);
    	}
    	

		// Pfad wiederherstellen
		$edstr = split(":",$path);
		//$filename = $edstr[count($edstr)-1];
		$path = str_replace(":","/",$path);
		$path = str_replace("root",'',$path);
		if($go_info["server"]["os"] != "win32") $path = escapeshellcmd($path);
		if(stristr($path,"..")) $go_api->errorMessage($go_api->lng("Der Pfad enthält ungültige Zeichen."));
		
		return $path;
	}
	
	function webftp_file_changed($file) {
		if(!$this->connected) $this->webftp_connect();
		if($result = @ftp_mdtm($this->conn_id, $file)) {
			$this->webftp_close();
			return $result;
		} else {
			return false;
		}
	}
	
	function webftp_mkdir($ordner) {
		global $go_api,$go_info;
		if(!$this->connected) $this->webftp_connect();
		if(@ftp_mkdir($this->conn_id, $ordner)) {
			$this->webftp_close();
			
			// Modifiziere Cache
			$this->dir_tree .= $ordner."\n";
			$dir_array = explode("\n",$this->dir_tree);
			sort($dir_array);
			reset($dir_array);
			$this->dir_tree = implode("\n",$dir_array);
			
			$go_info["webftp"]["dir_tree"] = $this->dir_tree;
			$go_api->session->save();
			
			return true;
		} else {
			return false;
		}
	}
	
	function webftp_chmod($mode,$file) {
		global $go_api,$go_info;
		if($go_info["server"]["webftp_chmod_disable"] != true) {
			if(!$this->connected) $this->webftp_connect();
			//ftp_chmod($this->conn_id, $mode, $file);
			$chmod_cmd="CHMOD $mode $file";
			if(@$chmod=ftp_site($this->conn_id, $chmod_cmd)) {
				$this->webftp_close();
				return true;
			} else {
				return false;
			}
		} 
		return true;
	}
	
	function webftp_mv($old,$new) {
		if(!$this->connected) $this->webftp_connect();
		if(@ftp_rename($this->conn_id, $old, $new)) {
			$this->webftp_close();
			return true;
		} else {
			return false;
		}
	}
	
	function webftp_get($localfile,$remotefile,$mode = FTP_BINARY) {
		if(!$this->connected) $this->webftp_connect();
		if(@ftp_get($this->conn_id, $localfile, $remotefile, $mode)) {
			$this->webftp_close();
			return true;
		} else {
			return false;
		}
	}
	
	function webftp_put($localfile,$remotefile,$mode = FTP_BINARY) {
		if(!$this->connected) $this->webftp_connect();
		if(@ftp_put($this->conn_id, $remotefile, $localfile, $mode)) {
			$this->webftp_close();
			return true;
		} else {
			return false;
		}
	}
	
	function webftp_rmdir($dir) {
		global $go_api,$go_info;
		if(!$this->connected) $this->webftp_connect();
		if(@ftp_rmdir($this->conn_id, $dir)) {
			$this->webftp_close();
			
			// Modifiziere Cache
			$dir_array = explode("\n",$this->dir_tree);
			$key = array_search($dir,$dir_array);
			unset($dir_array[$key]);
			$this->dir_tree = implode("\n",$dir_array);
			
			$go_info["webftp"]["dir_tree"] = $this->dir_tree;
			$go_api->session->save();
			
			return true;
		} else {
			return false;
		}
	}
	
	function webftp_delete($file) {
		if(!$this->connected) $this->webftp_connect();
		if(@ftp_delete($this->conn_id, $file)) {
			$this->webftp_close();
			return true;
		} else {
			return false;
		}
	}
}
?>