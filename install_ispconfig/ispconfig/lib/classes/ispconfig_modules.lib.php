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


class modules
{


//Constructor
function modules() {
global $go_api, $go_info;

}


function module_insert($doc_id, $doctype_id) {
global $go_api, $go_info;

    $module = $go_api->db->queryOneRecord("select * from sys_modules where doc_id = $doc_id");
    $module_name = $module["module_name"];
    unset($module);
    

    $go_api->db->query("CREATE TABLE ".$module_name."_dep (
  dep_id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  groupid int(10) unsigned NOT NULL default '0',
  parent_doc_id int(10) unsigned NOT NULL default '0',
  parent_doctype_id int(10) unsigned NOT NULL default '0',
  parent_tree_id int(11) NOT NULL default '0',
  child_doc_id int(11) NOT NULL default '0',
  child_doctype_id int(11) NOT NULL default '0',
  child_tree_id int(11) NOT NULL default '0',
  status tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (dep_id),
  UNIQUE KEY dep_id_2 (dep_id),
  KEY dep_id (dep_id,userid,groupid,parent_doc_id,parent_doctype_id),
  KEY tree_id (parent_tree_id,child_doc_id,child_doctype_id,child_tree_id)
) TYPE=MyISAM PACK_KEYS=1");

    $go_api->db->query("CREATE TABLE ".$module_name."_nodes (
  tree_id bigint(20) unsigned NOT NULL auto_increment,
  userid bigint(20) unsigned NOT NULL default '0',
  groupid bigint(20) unsigned NOT NULL default '0',
  parent varchar(100) NOT NULL default '',
  type char(1) NOT NULL default '',
  doctype_id int(11) NOT NULL default '0',
  status char(1) NOT NULL default '1',
  icon varchar(255) NOT NULL default '',
  modul varchar(255) NOT NULL default '',
  doc_id bigint(21) NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  PRIMARY KEY  (tree_id),
  UNIQUE KEY tree_id_2 (tree_id),
  KEY tree_id (tree_id,userid,groupid),
  KEY doc_id (doc_id)
) TYPE=MyISAM PACK_KEYS=1");


}



function module_update($doc_id, $doctype_id) {
global $go_api, $go_info;

}

function module_delete($doc_id, $doctype_id, $action) {
global $go_api, $go_info;

}



}
?>