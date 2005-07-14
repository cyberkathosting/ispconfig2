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

class reseller_traffic_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {

    global $go_info, $go_api;


        if(!isset($doc_id)) $go_api->errorMessage("no document ID");

    $reseller_node = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller WHERE doc_id = '$doc_id'");
    $groupid = $reseller_node["reseller_group"];
    $clients = $go_api->db->queryAllRecords("SELECT * FROM isp_nodes WHERE groupid = '$groupid' AND doctype_id = '1012'");

     $html_out = '<div align="center">
    <font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$go_api->lng("Trafficübersicht").' (in MB):</b></font><br>
    <table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">
    <tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$go_api->lng("Monat").'</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Web</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>FTP</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Mail</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$go_api->lng("Gesamt").'</b></font></td>
     </tr>';

         if(is_array($clients)) {
    foreach($clients as $client){

      $kunden = $go_api->db->queryAllRecords("SELECT * FROM isp_isp_kunde WHERE doc_id = '".$client["doc_id"]."'");

          if(is_array($kunden)) {
      foreach($kunden as $kunde){
       $kunden_ident = $kunde["kunde_firma"].' '.$kunde["kunde_vorname"].' '.$kunde["kunde_name"];
       $html_out .= '<tr>
       <td colspan="5" align="center" bgcolor="#9F9F9F"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><b>'.$kunden_ident.'</b></font></td>
    </tr>';

       //$webs = $go_api->db->queryAllRecords("SELECT child_doc_id FROM isp_dep WHERE parent_doc_id = '".$kunde["doc_id"]."' AND parent_doctype_id = '1012' AND child_doctype_id = '1013'");
       $webs = $go_api->db->queryAllRecords("SELECT isp_nodes.doc_id FROM isp_nodes, isp_isp_web WHERE isp_nodes.userid = '".$kunde["webadmin_userid"]."' AND isp_nodes.doctype_id = '1013' AND isp_nodes.doc_id = isp_isp_web.doc_id");
//print_r($webs);
       if(is_array($webs)) {
         foreach($webs as $web){
           //$web_id = $web["child_doc_id"];
           $web_id = $web["doc_id"];

           $web_data = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_web WHERE doc_id = '$web_id'");
           //print_r($web_data);

           $traffics = $go_api->db->queryAllRecords("SELECT * FROM isp_traffic WHERE web_id = '$web_id' ORDER BY jahr DESC, monat DESC LIMIT 0,36");
           //echo "web_id: ".$web_id."<br>";
           //print_r($traffics);
           $html_out .= '<tr>
       <td colspan="5" align="center" bgcolor="#EFEFEF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$web_data["web_domain"].'</b></font></td>
    </tr>';

           if(is_array($traffics)) {
                   foreach($traffics as $traffic){
             $monat = $traffic["monat"];
             $monat_traffic_web = number_format($traffic["bytes_web"]/1048576, 2, '.', '');
             $monat_traffic_ftp = number_format($traffic["bytes_ftp"]/1048576, 2, '.', '');
             $monat_traffic_mail = number_format($traffic["bytes_mail"]/1048576, 2, '.', '');
             $monat_traffic_gesamt = number_format(($traffic["bytes_web"] + $traffic["bytes_ftp"] + $traffic["bytes_mail"])/1048576, 2, '.', '');

             $monat_gesamt[$monat]["monat"] = $monat;
             $monat_gesamt[$monat]["web"] += $traffic["bytes_web"];
             $monat_gesamt[$monat]["ftp"] += $traffic["bytes_ftp"];
             $monat_gesamt[$monat]["mail"] += $traffic["bytes_mail"];
             $monat_gesamt[$monat]["gesamt"] = $monat_gesamt[$monat]["web"] + $monat_gesamt[$monat]["ftp"] + $monat_gesamt[$monat]["mail"];

             $monat_kunde_gesamt[$kunden_ident]["kunde"] = $kunden_ident;
             $monat_kunde_gesamt[$kunden_ident][$monat]["monat"] = $monat;
             $monat_kunde_gesamt[$kunden_ident][$monat]["web"] += $traffic["bytes_web"];
             $monat_kunde_gesamt[$kunden_ident][$monat]["ftp"] += $traffic["bytes_ftp"];
             $monat_kunde_gesamt[$kunden_ident][$monat]["mail"] += $traffic["bytes_mail"];
             $monat_kunde_gesamt[$kunden_ident][$monat]["gesamt"] = $monat_kunde_gesamt[$kunden_ident][$monat]["web"] + $monat_kunde_gesamt[$kunden_ident][$monat]["ftp"] + $monat_kunde_gesamt[$kunden_ident][$monat]["mail"];

             $gesamt_traffic_web += $traffic["bytes_web"];
             $gesamt_traffic_ftp += $traffic["bytes_ftp"];
             $gesamt_traffic_mail += $traffic["bytes_mail"];
             $html_out .= '<tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$monat.':</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$monat_traffic_web.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$monat_traffic_ftp.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$monat_traffic_mail.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$monat_traffic_gesamt.'</font></td>
     </tr>';
             }
                         }
          }
        } else {
          $html_out .= '<tr>
       <td colspan="5" align="center" bgcolor="#EFEFEF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("Kein Web angelegt").'!</font></td>
    </tr>';
       }

       $gesamt_traffic = $gesamt_traffic_web + $gesamt_traffic_ftp + $gesamt_traffic_mail;
     }
         }
}
}
    $gesamt_traffic_web_form = number_format($gesamt_traffic_web/1048576, 2, '.', '');
    $gesamt_traffic_ftp_form = number_format($gesamt_traffic_ftp/1048576, 2, '.', '');
    $gesamt_traffic_mail_form = number_format($gesamt_traffic_mail/1048576, 2, '.', '');
    $gesamt_traffic_form = number_format($gesamt_traffic/1048576, 2, '.', '');


    $html_out .= '</table>';
    $html_out .= '<hr size="1">
    <table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">';

        if(is_array($monat_kunde_gesamt)) {
    foreach($monat_kunde_gesamt as $monat_kunde_gs){
      $html_out .= '<tr>
       <td colspan="5" align="center" bgcolor="#9F9F9F"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><b>'.$monat_kunde_gs["kunde"].'</b></font></td>
    </tr>';
      unset($monat_kunde_gs["kunde"]);
          if(is_array($monat_kunde_gs)) {
      foreach($monat_kunde_gs as $monat_kunde_g){
        $html_out .= '<tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$monat_kunde_g["monat"].'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.number_format($monat_kunde_g["web"]/1048576, 2, '.', '').'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.number_format($monat_kunde_g["ftp"]/1048576, 2, '.', '').'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.number_format($monat_kunde_g["mail"]/1048576, 2, '.', '').'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.number_format($monat_kunde_g["gesamt"]/1048576, 2, '.', '').'</font></td>
     </tr>';
     }
         }
   }
   }

    $html_out .= '</table>';
    $html_out .= '<hr size="1">
    <table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">';
        if(is_array($monat_gesamt)) {
    foreach($monat_gesamt as $monat_gs){
      $html_out .= '<tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$monat_gs["monat"].'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.number_format($monat_gs["web"]/1048576, 2, '.', '').'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.number_format($monat_gs["ftp"]/1048576, 2, '.', '').'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.number_format($monat_gs["mail"]/1048576, 2, '.', '').'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.number_format($monat_gs["gesamt"]/1048576, 2, '.', '').'</font></td>
     </tr>';
    }
        }

    $html_out .= '<tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$go_api->lng("Gesamt").':</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$gesamt_traffic_web_form.'</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$gesamt_traffic_ftp_form.'</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$gesamt_traffic_mail_form.'</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$gesamt_traffic_form.'</b></font></td>
     </tr>
     ';



    $html_out .= '</table></div>';
    if($reseller_node["limit_dns_manager"] == 1 && $reseller_node["limit_web"] == 0 && $reseller_node["limit_user"] == 0 && $reseller_node["limit_domain"] == 0) $html_out = "";
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