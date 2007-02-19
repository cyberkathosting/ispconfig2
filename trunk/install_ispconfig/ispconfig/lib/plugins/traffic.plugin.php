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

class traffic_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;

    $web_id = $doc_id;
    $web_data = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_web WHERE doc_id = '$web_id'");

    $traffics = $go_api->db->queryAllRecords("SELECT * from isp_traffic where web_id = '$web_id' ORDER BY jahr DESC, monat DESC LIMIT 0,36");

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

     $gesamt_traffic_web = 0;
     $gesamt_traffic_ftp = 0;
     $gesamt_traffic_mail = 0;

    foreach($traffics as $traffic){
    $monat = $traffic["monat"];
    $monat_traffic_web = number_format($traffic["bytes_web"]/1048576, 2, '.', '');
    $monat_traffic_ftp = number_format($traffic["bytes_ftp"]/1048576, 2, '.', '');
    $monat_traffic_mail = number_format($traffic["bytes_mail"]/1048576, 2, '.', '');
    $monat_traffic_gesamt = number_format(($traffic["bytes_web"] + $traffic["bytes_ftp"] + $traffic["bytes_mail"])/1048576, 2, '.', '');
    $gesamt_traffic_web += $traffic["bytes_web"];
    $gesamt_traffic_ftp += $traffic["bytes_ftp"];
    $gesamt_traffic_mail += $traffic["bytes_mail"];
    if((($traffic["bytes_web"] + $traffic["bytes_ftp"] + $traffic["bytes_mail"])/1048576) >= $web_data['web_traffic'] && $web_data['web_traffic'] >= 0){
      $color = ' color="#FF0000" style="font-weight:bold;"';
    } else {
      $color = '';
    }

    $html_out .= '<tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"'.$color.'>'.$monat.':</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"'.$color.'>'.$monat_traffic_web.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"'.$color.'>'.$monat_traffic_ftp.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"'.$color.'>'.$monat_traffic_mail.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"'.$color.'>'.$monat_traffic_gesamt.'</font></td>
     </tr>';
    }

    $gesamt_traffic = $gesamt_traffic_web + $gesamt_traffic_ftp + $gesamt_traffic_mail;
    $gesamt_traffic_web = number_format($gesamt_traffic_web/1048576, 2, '.', '');
    $gesamt_traffic_ftp = number_format($gesamt_traffic_ftp/1048576, 2, '.', '');
    $gesamt_traffic_mail = number_format($gesamt_traffic_mail/1048576, 2, '.', '');
    $gesamt_traffic = number_format($gesamt_traffic/1048576, 2, '.', '');
    $html_out .= '</table>
    <hr size="1">
    <table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">
    <tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$go_api->lng("Gesamt").':</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$gesamt_traffic_web.'</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$gesamt_traffic_ftp.'</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$gesamt_traffic_mail.'</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$gesamt_traffic.'</b></font></td>
     </tr>
     </table>';

     $jahr_aktuell = date("Y", time());
     $jahr_minus_1 = $jahr_aktuell - 1;
     $jahr_minus_2 = $jahr_aktuell - 2;
     $jahr_minus_3 = $jahr_aktuell - 3;

     $traffics_jahr_aktuell = $go_api->db->queryAllRecords("SELECT * from isp_traffic where web_id = '$web_id' AND jahr = '$jahr_aktuell'");

     $gesamt_traffic_web_jahr_aktuell = 0;
     $gesamt_traffic_ftp_jahr_aktuell = 0;
     $gesamt_traffic_mail_jahr_aktuell = 0;
     foreach($traffics_jahr_aktuell as $traffic_jahr_aktuell){
     $gesamt_traffic_web_jahr_aktuell += $traffic_jahr_aktuell["bytes_web"];
     $gesamt_traffic_ftp_jahr_aktuell += $traffic_jahr_aktuell["bytes_ftp"];
     $gesamt_traffic_mail_jahr_aktuell += $traffic_jahr_aktuell["bytes_mail"];
     }
     $gesamt_traffic_jahr_aktuell = number_format(($gesamt_traffic_web_jahr_aktuell + $gesamt_traffic_ftp_jahr_aktuell + $gesamt_traffic_mail_jahr_aktuell)/1048576, 2, '.', '');
     $gesamt_traffic_web_jahr_aktuell = number_format($gesamt_traffic_web_jahr_aktuell/1048576, 2, '.', '');
     $gesamt_traffic_ftp_jahr_aktuell = number_format($gesamt_traffic_ftp_jahr_aktuell/1048576, 2, '.', '');
     $gesamt_traffic_mail_jahr_aktuell = number_format($gesamt_traffic_mail_jahr_aktuell/1048576, 2, '.', '');

     $traffics_jahr_minus_1 = $go_api->db->queryAllRecords("SELECT * from isp_traffic where web_id = '$web_id' AND jahr = '$jahr_minus_1'");

     $gesamt_traffic_web_jahr_minus_1 = 0;
     $gesamt_traffic_ftp_jahr_minus_1 = 0;
     $gesamt_traffic_mail_jahr_minus_1 = 0;
     foreach($traffics_jahr_minus_1 as $traffic_jahr_minus_1){
     $gesamt_traffic_web_jahr_minus_1 += $traffic_jahr_minus_1["bytes_web"];
     $gesamt_traffic_ftp_jahr_minus_1 += $traffic_jahr_minus_1["bytes_ftp"];
     $gesamt_traffic_mail_jahr_minus_1 += $traffic_jahr_minus_1["bytes_mail"];
     }
     $gesamt_traffic_jahr_minus_1 = number_format(($gesamt_traffic_web_jahr_minus_1 + $gesamt_traffic_ftp_jahr_minus_1 + $gesamt_traffic_mail_jahr_minus_1)/1048576, 2, '.', '');
     $gesamt_traffic_web_jahr_minus_1 = number_format($gesamt_traffic_web_jahr_minus_1/1048576, 2, '.', '');
     $gesamt_traffic_ftp_jahr_minus_1 = number_format($gesamt_traffic_ftp_jahr_minus_1/1048576, 2, '.', '');
     $gesamt_traffic_mail_jahr_minus_1 = number_format($gesamt_traffic_mail_jahr_minus_1/1048576, 2, '.', '');

     $traffics_jahr_minus_2 = $go_api->db->queryAllRecords("SELECT * from isp_traffic where web_id = '$web_id' AND jahr = '$jahr_minus_2'");

     $gesamt_traffic_web_jahr_minus_2 = 0;
     $gesamt_traffic_ftp_jahr_minus_2 = 0;
     $gesamt_traffic_mail_jahr_minus_2 = 0;
     foreach($traffics_jahr_minus_2 as $traffic_jahr_minus_2){
     $gesamt_traffic_web_jahr_minus_2 += $traffic_jahr_minus_2["bytes_web"];
     $gesamt_traffic_ftp_jahr_minus_2 += $traffic_jahr_minus_2["bytes_ftp"];
     $gesamt_traffic_mail_jahr_minus_2 += $traffic_jahr_minus_2["bytes_mail"];
     }
     $gesamt_traffic_jahr_minus_2 = number_format(($gesamt_traffic_web_jahr_minus_2 + $gesamt_traffic_ftp_jahr_minus_2 + $gesamt_traffic_mail_jahr_minus_2)/1048576, 2, '.', '');
     $gesamt_traffic_web_jahr_minus_2 = number_format($gesamt_traffic_web_jahr_minus_2/1048576, 2, '.', '');
     $gesamt_traffic_ftp_jahr_minus_2 = number_format($gesamt_traffic_ftp_jahr_minus_2/1048576, 2, '.', '');
     $gesamt_traffic_mail_jahr_minus_2 = number_format($gesamt_traffic_mail_jahr_minus_2/1048576, 2, '.', '');

     $traffics_jahr_minus_3 = $go_api->db->queryAllRecords("SELECT * from isp_traffic where web_id = '$web_id' AND jahr = '$jahr_minus_3'");

     $gesamt_traffic_web_jahr_minus_3 = 0;
     $gesamt_traffic_ftp_jahr_minus_3 = 0;
     $gesamt_traffic_mail_jahr_minus_3 = 0;
     foreach($traffics_jahr_minus_3 as $traffic_jahr_minus_3){
     $gesamt_traffic_web_jahr_minus_3 += $traffic_jahr_minus_3["bytes_web"];
     $gesamt_traffic_ftp_jahr_minus_3 += $traffic_jahr_minus_3["bytes_ftp"];
     $gesamt_traffic_mail_jahr_minus_3 += $traffic_jahr_minus_3["bytes_mail"];
     }
     $gesamt_traffic_jahr_minus_3 = number_format(($gesamt_traffic_web_jahr_minus_3 + $gesamt_traffic_ftp_jahr_minus_3 + $gesamt_traffic_mail_jahr_minus_3)/1048576, 2, '.', '');
     $gesamt_traffic_web_jahr_minus_3 = number_format($gesamt_traffic_web_jahr_minus_3/1048576, 2, '.', '');
     $gesamt_traffic_ftp_jahr_minus_3 = number_format($gesamt_traffic_ftp_jahr_minus_3/1048576, 2, '.', '');
     $gesamt_traffic_mail_jahr_minus_3 = number_format($gesamt_traffic_mail_jahr_minus_3/1048576, 2, '.', '');

     $html_out .= '
    <hr size="1">
    <font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$go_api->lng("Traffic in den letzten 4 Jahren").' (in MB):</b></font><br>
    <table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">
    <tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$go_api->lng("Jahr").'</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Web</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>FTP</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Mail</b></font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>'.$go_api->lng("Gesamt").'</b></font></td>
     </tr>
    <tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$jahr_aktuell.':</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_web_jahr_aktuell.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_ftp_jahr_aktuell.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_mail_jahr_aktuell.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_jahr_aktuell.'</font></td>
     </tr>
     <tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$jahr_minus_1.':</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_web_jahr_minus_1.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_ftp_jahr_minus_1.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_mail_jahr_minus_1.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_jahr_minus_1.'</font></td>
     </tr>
     <tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$jahr_minus_2.':</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_web_jahr_minus_2.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_ftp_jahr_minus_2.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_mail_jahr_minus_2.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_jahr_minus_2.'</font></td>
     </tr>
     <tr>
       <td width="20%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$jahr_minus_3.':</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_web_jahr_minus_3.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_ftp_jahr_minus_3.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_mail_jahr_minus_3.'</font></td>
       <td width="20%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_traffic_jahr_minus_3.'</font></td>
     </tr>
     </table></div>';

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