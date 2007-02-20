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


class isp_firewall
{

//Constructor
function isp_firewall() {

}


function firewall_insert($doc_id, $doctype_id) {
global $go_api, $go_info;


        $go_api->db->query("UPDATE isp_firewall SET status = 'i' where doc_id = '$doc_id'");

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');

}

function firewall_update($doc_id, $doctype_id) {
global $go_api, $go_info;

        $go_api->db->query("UPDATE isp_firewall SET status = 'u' where doc_id = '$doc_id'");
    $fw = $go_api->db->query("SELECT * from isp_firewall where doc_id = '$doc_id'");

    // SSH und ISPConfig immer offen
    if($doc_id == 2) {
        $go_api->db->query("UPDATE isp_firewall SET dienst_aktiv = 'ja', dienst_typ = 'tcp', status = '', dienst_port = '22' where doc_id = '$doc_id'");
        $go_api->errorMessage($go_api->lng("Dieser Port kann nicht geändert werden.").$go_api->lng("weiter_link"));
    }

    if($doc_id == 7) {
        $go_api->db->query("UPDATE isp_firewall SET dienst_aktiv = 'ja', dienst_typ = 'tcp', status = '', dienst_port = '81' where doc_id = '$doc_id'");
        $go_api->errorMessage($go_api->lng("Dieser Port kann nicht geändert werden.").$go_api->lng("weiter_link"));
    }

    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'update');

}

function firewall_delete($doc_id, $doctype_id, $action) {
    global $go_api, $go_info;


        // SSH und ISPConfig immer offen
    if($doc_id == 2 or $doc_id == 7) {
        $go_api->db->query("UPDATE sys_nodes SET status = '1' where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
        $go_api->errorMessage($go_api->lng("Dieser Port kann nicht deaktiviert werden.").$go_api->lng("weiter_link"));
    }

    if($action == 'do') {
        $go_api->db->query("UPDATE isp_firewall SET status = 'd' where doc_id = '$doc_id'");
    } else {
        $go_api->db->query("UPDATE isp_firewall SET status = 'i' where doc_id = '$doc_id'");
    }


    // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'delete: '.$action);

}




}
?>