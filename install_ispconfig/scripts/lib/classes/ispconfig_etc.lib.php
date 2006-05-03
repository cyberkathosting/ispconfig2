<?php
/*
Copyright (c) 2006 Oliver Blaha
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

class etc {

var $FILE = "/root/ispconfig/scripts/lib/classes/ispconfig_etc.lib.php";

function get_config_dir() {
  return "/etc/ispconfig";
}

function get_user_config_dir($username) {
  return $this->get_config_dir() . "/user/" . $username;
}

function create_user_config_dir($username) {
  $cfg_dir = $this->get_user_config_dir($username);

  if (!is_dir($cfg_dir)) {
    exec("mkdir -p $cfg_dir &> /dev/null");
    exec("chown -R root:$root_gruppe $cfg_dir &> /dev/null");
    exec("chmod 644 $cfg_dir");
  }

  return $cfg_dir;
}

function delete_user_config_dir($username) {
  $cfg_dir = get_user_config_dir($username);
  if (is_dir($cfg_dir)) {
    exec("rm -rf $cfg_dir");
  }
}

}
?>
