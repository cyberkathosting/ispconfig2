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


class isp
{

        /************************************************
        * Funktion zur Übermittlung des Update Signals
        * an den Server
        *
        * Parameter: ServerID, action
        *
        *************************************************/

        function signal_server($server_id, $action) {
    global $go_api, $go_info;

                // Signal Datei
                $file = "/home/admispconfig/ispconfig/.run";

                // Datei anlegen
                if($go_info["server"]["mode"] != "demo") {
                        if(@touch($file)) {
                                $go_api->log("Signalfile Set: $action",1);
                        } else {
                                $go_api->log("Setting Signalfile Failed: $action",4);
                        }
                }
        }

        /************************************************
        * Funktion zur Überprüfung und Korrektur der
        * Rechte eines Eintrages
        *
        * Parameter: ServerID, action
        *
        *************************************************/

        function check_perms($doc_id, $doctype_id) {
    global $go_api, $go_info, $doc, $tree_id;
				
				$doc_id = intval($doc_id);
				$doctype_id = intval($doctype_id);
				
                $nodes_table = $doc->modul . "_nodes";
                $dep_table = $doc->modul . "_dep";
                $item = $go_api->db->queryOneRecord("SELECT * from $nodes_table where doc_id = $doc_id and doctype_id = $doctype_id");

                if($item["groupid"] == 0) {

                        if($item["type"] == 'i') {
                                $go_api->db->query("UPDATE $nodes_table SET groupid = 1 where doc_id = $doc_id and doctype_id = $doctype_id");
                                //$go_api->tree->update_dep_group($tree_id,1);
                                $go_api->log("Correcting node Permissions: i",1);
                        }

                        if($item["type"] == 'a') {
                                $parent = $go_api->db->queryOneRecord("SELECT * from $nodes_table, $dep_table where $dep_table.child_doc_id = $doc_id and $dep_table.child_doctype_id and $dep_table.parent_doc_id = $nodes_table.doc_id and $dep_table.parent_doctype_id = $nodes_table.doctype_id");
                                if($parent["groupid"] != 0) {
                                        $go_api->db->query("UPDATE $nodes_table SET groupid = ".$parent["groupid"]." where doc_id = $doc_id and doctype_id = $doctype_id");
                                        $go_api->log("Correcting node Permissions: a",1);
                                }
                        }
                }
        }

        /************************************************
        * Funktion zum Aufruf von Funktionen auf dem
        * Server
        *
        * Parameter: module, function, data
        *
        *************************************************/

        function call_server_function($server_id, $module, $function, $data) {
    		global $go_api, $go_info;
			
			$server_id = intval($server_id);
			$module = addslashes($module);
			$function = addslashes($function);

                // Datei anlegen
                if($go_info["server"]["mode"] != "demo") {
                        // encode Data
                        $data = addslashes(serialize($data));
                        // Sign Data
                        $sc = md5($module.$function.$data.$go_info["server"]["db_user"].$go_info["server"]["db_password"]);
                        // Timestamp
                        $tstamp = time();
                        // save command
                        $go_api->db->query("INSERT INTO isp_com (server_id,modul,funktion,data,sc,tstamp) VALUES ($server_id,'$module','$function','$data','$sc',$tstamp)");
                        // Signal Server
                        $this->signal_server($server_id, "call: $module -> $function");
                }
        }


}
?>