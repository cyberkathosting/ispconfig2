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

class mod{

  function mod(){
    global $mod, $go_info;

    include_once($go_info["isp"]["classes_root"] . $go_info["server"]["dir_trenner"] ."ispconfig_template.lib.php");
    $this->tpl = new FastTemplate($go_info["isp"]["server_root"].'/isp/conf');

    // Einbindung Datenbank
    include_once($go_info["isp"]["classes_root"] . $go_info["server"]["dir_trenner"] ."ispconfig_db_".$go_info["server"]["db_type"].".lib.php");
    $dbname = 'db_'.$go_info["server"]["db_type"];
    $this->db = new $dbname;

    // Einbindung Logging
    $this->uses("log");

    // Einbindung der Standard Module
    if(is_array($go_info["modules"])){
      foreach($go_info["modules"] as $key => $val){
        include_once($go_info["isp"]["classes_root"] . $go_info["server"]["dir_trenner"] ."ispconfig_".$val.".lib.php");
        $this->$key = new $val;
      }
    }
  }

  function uses($modules){
    global $mod, $go_info;
    $modules = explode(",",$modules);
    foreach($modules as $value){
      $value = trim($value);
      include_once($go_info["isp"]["classes_root"] . $go_info["server"]["dir_trenner"] ."ispconfig_".$value.".lib.php");
      $this->$value = new $value;
    }
  }

  function log($message,$level = 1){
    global $go_info;
    $this->log->msg($message,$level,$go_info["server"]["log_device"]);
  }
}

$mod = new mod;
?>