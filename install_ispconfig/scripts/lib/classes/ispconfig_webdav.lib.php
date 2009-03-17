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

class webdav{

var $FILE = "/root/ispconfig/scripts/lib/classes/ispconfig_webdav.lib.php";

function make_passwd_dav($doc_id) {
  global $mod, $isp_web;

  $user = $mod->system->data["isp_isp_user"][$doc_id];
  $sql = "SELECT * FROM isp_dep WHERE child_doc_id = '$doc_id' AND child_doctype_id = '".$isp_web->user_doctype_id."'";

  // doc_id des Webs bestimmen
  $web_dep = $mod->db->queryOneRecord($sql);
  $web_doc_id = $web_dep["parent_doc_id"];
  $web_path = $mod->system->server_conf["server_path_httpd_root"]."/web".$web_doc_id;

  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "htpasswd.master"));
  $mod->tpl->define_dynamic ( "user_password", "table" );

   if($web_users = $mod->db->queryAllRecords("SELECT isp_isp_user.doc_id, isp_isp_user.user_username FROM isp_nodes, isp_dep, isp_isp_user WHERE isp_dep.parent_doc_id = $web_doc_id AND isp_dep.parent_doctype_id = ".$isp_web->web_doctype_id." AND isp_dep.child_doc_id = isp_isp_user.doc_id AND isp_dep.child_doctype_id = ".$isp_web->user_doctype_id." AND isp_isp_user.user_webdav = 1 AND isp_nodes.doc_id = isp_isp_user.doc_id AND isp_nodes.doctype_id = ".$isp_web->user_doctype_id." AND isp_nodes.status = 1")){
    foreach($web_users as $web_user){
      $mod->tpl->assign( array( USER => $web_user['user_username'],
                                PASSWORT => $mod->system->getpasswd($web_user['user_username'])));
      $mod->tpl->parse(USER_PASSWORD,".user_password");
    }
  } else {
    $mod->tpl->assign( array( USER => '',
                              PASSWORT => ''));
    $mod->tpl->parse(USER_PASSWORD,".user_password");
  }

  $mod->tpl->parse(TABLE, table);
  $passwd_dav_text = $mod->tpl->fetch();
  if(trim($passwd_dav_text) == ':') $passwd_dav_text = '';
  $mod->file->wf($web_path.'/passwd.dav', $passwd_dav_text);
}

}
?>