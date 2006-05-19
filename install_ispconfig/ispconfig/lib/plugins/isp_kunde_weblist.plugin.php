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

class isp_kunde_weblist_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api,$tree_id,$s;

        if(!isset($doc_id)) $go_api->errorMessage("no document ID");

        // bestimme Anbieter
        $kunde = $go_api->db->queryOneRecord("SELECT groupid FROM isp_nodes WHERE doc_id = $doc_id AND doctype_id = $doctype_id");

    $html_out = '
    <font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>&nbsp; '.$go_api->lng("Web").':</b> <input type="button" name="neu"  class="button" value=" '.$go_api->lng("Neu").' " onClick="'."top.seiteFrame.location.href='".'../../isp_manager/new/web.php?s='.$s.'&kunde='.$tree_id.'&anbieter='.$kunde["groupid"]."'".'"></a></font><br><br>
    <table width="100%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">
    <tr>
       <td bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>IP</b></font></td>
       <td bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Host</b></font></td>
       <td bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Domain</b></font></td>
    </tr>';

        $sql = "SELECT isp_dep.child_doc_id, isp_nodes.groupid FROM isp_dep, isp_nodes WHERE isp_nodes.doc_id = isp_dep.child_doc_id and isp_nodes.doctype_id = '1013' AND isp_nodes.status = 1 AND isp_dep.parent_doc_id = '$doc_id' AND isp_dep.parent_doctype_id = '1012' AND isp_dep.child_doctype_id = '1013'";
    $webs = $go_api->db->queryAllRecords($sql);

    foreach($webs as $web){
            $web_id = $web["child_doc_id"];
            $web_data = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_web WHERE doc_id = '$web_id'");

                $html_out .= '
        <tr>
       <td bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="edit.php?s='.$s.'&doctype_id=1013&next_tree_id='.$tree_id.'&caller_tree_id='.$tree_id.'&next_doctype_id=1012&doc_id='.$web_data["doc_id"].'">'.$web_data["web_ip"].'</a></font></td>
       <td bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$web_data["web_host"].'</font></td>
       <td bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$web_data["web_domain"].'</font></td>
    </tr>';

    }
            $html_out .= '</table>';
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