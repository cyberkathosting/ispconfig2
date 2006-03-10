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
include("../../../lib/config.inc.php");
include("../../../lib/session.inc.php");

$tree_id = intval($_REQUEST["tree_id"]);
$doc_id = intval($_REQUEST["doc_id"]);
$doctype_id = intval($_REQUEST["doctype_id"]);
$gid = intval($_REQUEST["gid"]);
$userid = intval($_REQUEST["userid"]);

if ($gid == "") $gid = 0;
$go_api->uses("tree");
$go_api->tree->set_table($go_info["modul"]["table_name"]);

//$go_api->tree->empty_trash($gid);

$groupid = $gid;



if($go_api->auth->check_write($groupid)) {

            if($groupid == 0) {
            $items = $go_api->db->queryAllRecords("SELECT * FROM ".$go_info["modul"]["table_name"]."_nodes where status = 0 and userid = ".$go_info["user"]["userid"]);
            // löschen der tree einträge
            //$go_api->db->query("DELETE from ".$this->_table." where status = 0 and userid = ".$go_info["user"]["userid"]);
            } else {
            $sql = "SELECT * FROM ".$go_info["modul"]["table_name"]."_nodes where status = 0 and groupid = $groupid";
            $items = $go_api->db->queryAllRecords($sql);
            // löschen der tree einträge
            //$go_api->db->query("DELETE from ".$this->_table." where status = 0 and groupid = $groupid");
            }


            foreach ($items as $key => $value) {
            $row = $go_api->db->queryOneRecord("SELECT doctype_modul,doctype_name from doctype where doctype_id = ".$value["doctype_id"]);

            if($value["type"] == 'i' or $value["type"] == 'a') {

                $stat = $go_api->db->queryOneRecord("SELECT status from ".$row["doctype_modul"]."_".$row["doctype_name"]." where doc_id = ".$value["doc_id"]);

                if(($stat["status"] == "" or !isset($stat["status"]) or empty($stat["status"])) or $go_info["server"]["mode"] == "demo") {

                // Löschen des Tree eintrages
                $go_api->db->query("DELETE from ".$go_info["modul"]["table_name"]."_nodes where status = 0 and tree_id = ".$value["tree_id"]);
                // löschen der Daten Einträge
                $go_api->db->query("DELETE from ".$row["doctype_modul"]."_".$row["doctype_name"]." where doc_id = ".$value["doc_id"]);
                // löschen der abhängigkeiten
                $go_api->db->query("DELETE from ".$go_info["modul"]["table_name"]."_dep where parent_tree_id = ".$value["tree_id"]." or child_tree_id = ".$value["tree_id"]);
                // löschen angehängte Termine
                //$go_api->db->query("DELETE from termin_event where parent_doc_id = ".$value["doc_id"]." and parent_doctype_id = ".$value["doctype_id"]);
                //löschen abhängigkeiten der angehängte Files
                //$go_api->db->query("UPDATE file_nodes SET parent_doc_id = 0, parent_doctype_id = 0 where parent_doc_id = ".$value["doc_id"]." and parent_doctype_id = ".$value["doctype_id"]);
                }
            } else {
                // Löschen des Tree eintrages
                $go_api->db->query("DELETE from ".$go_info["modul"]["table_name"]."_nodes where status = 0 and tree_id = ".$value["tree_id"]);
            }


            }

        }

                        // Löschen der Faktura Daten
                        //$go_api->db->query("DELETE from isp_fakt_record where status = 0");

                        // Einträge in del_status auf Status 'd' setzen
                        $go_api->db->query("UPDATE del_status SET status = 'd'");

                        // Server benachrichtigen
                        $go_api->uses("isp");
                        $server_id = 1;
                        $go_api->isp->signal_server($server_id,'empty trash');



header("Location: ../../index.php?$session");
exit;
?>





