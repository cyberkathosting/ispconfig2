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

 #############################################################
 #
 # ISPConfig Plugin
 # Version 1.0
 #
 # Plugin Name:
 # Version:
 # Autor:
 # Datum:
 #
 #############################################################

 if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class dns_spf_list_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api,$tree_id,$s;

        if(!isset($doc_id)) $go_api->errorMessage("no document ID");

        // bestimme SPFs
        $tree_id = $go_api->db->queryOneRecord("SELECT tree_id FROM dns_nodes WHERE doc_id = $doc_id AND doctype_id = $doctype_id");
        $soa = $go_api->db->queryOneRecord("SELECT dns_soa FROM dns_isp_dns WHERE doc_id = $doc_id AND doctype_id = $doctype_id");

    $html_out = '
    <br><span class="t2b">&nbsp; '.$go_api->lng("SPF").'&nbsp; <input type="button" name="neu"  class="button" value=" '.$go_api->lng("Neu").' " onClick="'."top.seiteFrame.location.href='".'../../multidoc/edit/edit.php?doctype_id=1031&tree_id=&next_tree_id='.$tree_id['tree_id'].'&caller_tree_id='.$tree_id['tree_id'].'&next_doctype_id='.$doctype_id.'&s='.$s."'".'"></a></span>
    <hr noshade size="1">
    <table width="100%" border="0" cellspacing="0" cellpadding="4">
    <tr>
       <td class="t2b">'.$go_api->lng("Host").'</td>
    </tr>';

        $sql = "SELECT dns_dep.child_doc_id, dns_nodes.groupid, dns_nodes.tree_id FROM dns_dep, dns_nodes WHERE dns_nodes.doc_id = dns_dep.child_doc_id and dns_nodes.doctype_id = '1031' AND dns_nodes.status = 1 AND dns_dep.parent_doc_id = '$doc_id' AND dns_dep.parent_doctype_id = '".$doctype_id."' AND dns_dep.child_doctype_id = '1031'";
    $spfs = $go_api->db->queryAllRecords($sql);

    foreach($spfs as $spf){
            $spf_id = $spf["child_doc_id"];
            $spf_data = $go_api->db->queryOneRecord("SELECT * FROM dns_spf WHERE doc_id = '$spf_id'");

            if(substr(trim($spf_data["host"]),-1) == '.'){
              if(substr(trim($spf_data["host"]),-(strlen($soa["dns_soa"])+2)) == '.'.$soa["dns_soa"].'.'){
                $spf_data["host"] = substr(trim($spf_data["host"]),0,-(strlen($soa["dns_soa"])+2));
              } else {
                $spf_data["host"] = substr(trim($spf_data["host"]),0,-1);
              }
            }

                $html_out .= '
        <tr>
       <td class="t2b"><a href="../../multidoc/edit/edit.php?doctype_id=&tree_id='.$spf['tree_id'].'&next_tree_id='.$tree_id['tree_id'].'&caller_tree_id='.$tree_id['tree_id'].'&next_doctype_id='.$doctype_id.'&s='.$s.'" class="t2b">'.$spf_data["host"].($spf_data["host"] == "" ? "" : ".").$soa['dns_soa'].'</a></td>
    </tr>';

    }
            $html_out .= '</table><hr noshade size="1">';
    return $html_out;
    }


    ##################################################################

    function insert($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;


    return true;
    }

    function update($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;

    return true;
    }

    function delete($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;

    return true;
    }

    function undelete($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;

    return true;
    }
}
?>