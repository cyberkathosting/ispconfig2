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

class log
{
    function msg($message,$level = 1,$device = '')
    {
    global $go_info;

    /*
    Log Level:
    0: DEBUG
    1: INFO
    2: WARN
    3: ERROR

    Devices:
    - FILE
    - SYSTEM
    - MAIL
    */
    $loglevel[0] = 'DEBUG';
    $loglevel[1] = 'INFO';
    $loglevel[2] = 'WARN';
    $loglevel[3] = 'ERROR';

    if($device == '') $device = $go_info["server"]["log_device"];
    if(substr($message,-1) != "\n") $message .= "\n";
    $err_msg = date("d.m.Y - H:i:s",time())." => ".$loglevel[$level]." - ".$message;

        if($device == 'FILE') {
            error_log ($err_msg, 3, $go_info["server"]["log_file"]);
        }

        if($device == 'SYSTEM') {
            error_log ($err_msg, 0);
        }

        if($device == 'MAIL') {
            error_log ($err_msg, 1, $go_info["server"]["log_mail"]);
        }

    $err_msg = NULL;
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
        $this->msg($pre."WARNING: ".$failure, 2);
      } else {
        if($success == "") $success = $command;
        $this->msg($pre.$success, 1);
      }
      return $ret_val;
    }

    function phpcaselog($ret_val, $mesg, $file = '', $line = ''){
      if(!empty($file) && !empty($line)){
        $pre = $file.", Line ".$line.": ";
      } else {
        $pre = "";
      }
      if($ret_val == true){
        $this->msg($pre.$mesg, 1);
      } else {
        $this->msg($pre."WARNING: could not ".$mesg, 2);
      }
      return $ret_val;
    }

    function ext_log($mesg, $level = 1, $file = '', $line = ''){
      if(!empty($file) && !empty($line)){
        $pre = $file.", Line ".$line.": ";
      } else {
        $pre = "";
      }
      $this->msg($pre.$mesg, $level);
      return $mesg;
    }

    function logrotate(){
      global $go_info, $mod;
      if(is_file($go_info["server"]["log_file"])){
        if(filesize($go_info["server"]["log_file"]) > 5000000){
          $datum = date("d-m-y_H-i-s");
          exec("tar -pczf ispconfig.log.".$datum.".tar.gz ".$go_info["server"]["log_file"]);
          $mod->file->wf($go_info["server"]["log_file"], "");
          exec("mv -f ispconfig.log.".$datum.".tar.gz ".$go_info["server"]["log_file"].".".$datum.".tar.gz");
        }
        clearstatcache();
      }
      return true;
    }
}
?>