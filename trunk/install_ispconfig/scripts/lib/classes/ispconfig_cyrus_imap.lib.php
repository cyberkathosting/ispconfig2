<?php

/*

Copyright (c) 2006, Oliver Blaha
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

class cyrus_imap {

  var $FILE = "/root/ispconfig/scripts/lib/classes/ispconfig_cyrus_imap.lib.php";

  var $imap;

  function imap_login() {
    global $mod;

    $this->imap = fsockopen("localhost", 143, &$dummy, &$dummy);
    $dummy = fgets($this->imap, 1024);
	
    $login_user = $mod->system->server_conf["cyrus_admin"];
    $login_pass = $mod->system->server_conf["cyrus_password"];

    if ($this->imap_send("login \"$login_user\" \"$login_pass\"")) {
      return true;
    } else {
      $mod->log->msg("WARNING: Could not login as Cyrus IMAP administrator", 2);
    }
  }

  function imap_logout() {
    $this->imap_send("logout");
	
    if ($this->imap) {
      fclose($this->imap);
      $this->imap = false;
    }
  }
    
  function imap_send($cmd) {
    if (!$this->imap) {
      return false;
    }
	
    fputs($this->imap, ". $cmd\n");

    while ($result = fgets($this->imap, 1024)) {
      if ($result[0] == '.' || $result[0] == '*') {
	break;
      }
    }
	
    return (stripos($result, ". OK ") === 0);
  }
    
  function imap_create($user, $quota) {
    $mailbox  = $this->get_mailbox($user);
    return $this->imap_send("create \"$mailbox\"") && $this->imap_setquota($user, $quota);
  }

  function imap_setquota($user, $quota) {
    $mailbox  = $this->get_mailbox($user);
    $quota    = intval($quota) * 1024;
    $quota    = ($quota >= 0) ? "STORAGE $quota" : "";
    return $this->imap_send("setquota \"$mailbox\" ($quota)");
  }

  function imap_del($user) {
    global $mod;
    $login_user = $mod->system->server_conf["cyrus_admin"];
    $mailbox  = $this->get_mailbox($user);
    return $this->imap_send("setacl \"$mailbox\" \"$login_user\" acdilprsw") && $this->imap_send("delete \"$mailbox\"");
  }
    
  function get_mailbox($user) {
    return "user." . $user;
  }

  function add($user, $quota) {
    $this->imap_login();
    $this->imap_create($user);
    $this->imap_logout();
  }

  function update($user, $quota) {
    $this->imap_login();
    $this->imap_setquota($user, $quota) || $this->imap_create($user, $quota); // Falls Fehler bei Quota: Vorsichtshalber versuchen Mailbox neu anzulegen
    $this->imap_logout();
  }

  function del($user) {
    $this->imap_login();
    $this->imap_del($user);
    $this->imap_logout();
  }
}

?>
