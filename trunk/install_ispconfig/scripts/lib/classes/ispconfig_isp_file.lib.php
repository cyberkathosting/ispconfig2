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

class isp_file{

function chmod($data){
  global $mod;
  $file = $data["file"];
  $chmod_str = str_pad($data["perms"], 4, "0", STR_PAD_LEFT);
  $dir_root = $data["web_root"];
  if(is_link($file) || strstr($file, "../") || substr($file,0,strlen($dir_root)) != $dir_root || substr($file,0,strlen($mod->system->server_conf["server_path_httpd_root"])) != $mod->system->server_conf["server_path_httpd_root"]){
    clearstatcache();
    return false;
  } else {
    clearstatcache();
    return @chmod($file, octdec($chmod_str));
  }
}


/*
        Function: chown

        Data Structure

        $data["file"]
        $data["web_root"]
        $data["user"]
        $data["group"]
        $data["recurse"] = 0 / 1
*/

function chown($data){
  global $mod;
  $file = escapeshellcmd($data["file"]);
  $user = escapeshellcmd($data["user"]);
  $group = escapeshellcmd($data["group"]);
  $recurse = ($data["recurse"] == 1)?'-R':'';
  $dir_root = $data["web_root"];

  if(is_link($file) || strstr($file, "../") || substr($file,0,strlen($dir_root)) != $dir_root || substr($file,0,strlen($mod->system->server_conf["server_path_httpd_root"])) != $mod->system->server_conf["server_path_httpd_root"]){
    clearstatcache();
    return false;
  } else {
    clearstatcache();
    return @exec("chown $recurse $user:$group $file");
  }
}

}
?>