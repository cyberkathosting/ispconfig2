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
include("../../lib/config.inc.php");
include("../../lib/session.inc.php");

//include("../start.php");
if($go_info["server"]["os"] != "win32") {

    if($go_info["server"]["mode"] != "demo") {
        if($go_api->auth->check_admin(1,1)){
            // Ist Administrator
            header("Location: ../multidoc/edit/edit.php?doc_id=3&doctype_id=1021&s=$s");
        } else {

        $sysuser = $go_api->db->queryOneRecord("SELECT * from sys_user where doc_id = '$userid'");
        if($sysuser["modul"] == "isp_kunde") {
            header("Location: ../isp_kunde/frame_start.php?s=$s");
            exit;
        }

// Ist reseller ////////////////////////////////////////////////////////////////////

$go_api->content->define(   array(
                                    main    => "main.htm",
                                    table   => "tools_standard.htm",
                                    stylesheet => "style.css"));

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "$session_page_hcolour",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Anbieter")."</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                            CHARSET => $go_info["theme"]["charset"],
                                    SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                    ) );

// BEGIN TOOL ###########################################################################

    $html_out .= '<br><div align="center"><table width="90%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">';

    $reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_userid = '".$go_info["user"]["userid"]."'");
    $reseller_group = $reseller["reseller_group"];
    //unset($reseller);

    if($reseller["limit_web"] != 0){
    $web_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as anzahl from isp_nodes where groupid = $reseller_group and doctype_id = 1013");
    $weblimit = $reseller["limit_web"];
    if($reseller["limit_web"] == -1) $weblimit = $go_api->lng("unlimitiert");
    $html_out .= '<tr>
       <td width="50%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("Webs angelegt").':</font></td>
       <td width="50%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$web_count["anzahl"].' '.$go_api->lng("von").' '.$weblimit.'</font></td>
     </tr>';
     }

     // Diskspace
     if($reseller["limit_disk"] != 0){
     $diskspace = $go_api->db->queryOneRecord("SELECT sum(isp_isp_web.web_speicher) as diskspace, sum(isp_isp_web.web_mailquota) as mailquota, sum(isp_isp_web.web_mysql_quota) as mysqlquota from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '".$reseller_group."' and isp_nodes.doctype_id = 1013");
     $minspace  = $go_api->db->queryOneRecord("SELECT min(isp_isp_web.web_speicher) as diskspace, min(isp_isp_web.web_mailquota) as mailquota, min(isp_isp_web.web_mysql_quota) as mysqlquota from isp_isp_web,isp_nodes where isp_isp_web.doc_id = isp_nodes.doc_id and  isp_nodes.groupid = '".$reseller_group."' and isp_nodes.doctype_id = 1013");
    $diskspace = intval($diskspace["diskspace"]) + intval($diskspace["mailquota"]) + intval($diskspace["mysqlquota"]);
    $disklimit = $reseller["limit_disk"];
    if (min($minspace["diskspace"], $minspace["mailquota"], $minspace["mysqlquota"]) < 0) {
      $diskspace = $go_api->lng("unlimitiert");
    } else {
      $diskspace .= " MB";
    }
    if ($reseller["limit_disk"] == -1) {
      $disklimit = $go_api->lng("unlimitiert");
    } else {
      $disklimit .= " MB";
    }
    $html_out .= '<tr>
       <td width="50%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("Benutzter Diskspace").':</font></td>
       <td width="50%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$diskspace.' '.$go_api->lng("von").' '.$disklimit.'</font></td>
     </tr>';
     }

    if($reseller["limit_user"] != 0){
    $user_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as anzahl from isp_nodes where groupid = $reseller_group and doctype_id = 1014");
    $userlimit = $reseller["limit_user"];
    if($reseller["limit_user"] == -1) $userlimit = $go_api->lng("unlimitiert");
    $html_out .= '<tr>
       <td width="50%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("User angelegt").':</font></td>
       <td width="50%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$user_count["anzahl"].' '.$go_api->lng("von").' '.$userlimit.'</font></td>
     </tr>';
     }

    if($reseller["limit_domain"] != 0){
    $domain_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as anzahl from isp_nodes where groupid = $reseller_group and doctype_id = 1015");
    $domainlimit = $reseller["limit_domain"];
    $domainanzahl = $web_count["anzahl"] + $domain_count["anzahl"];
    if($reseller["limit_domain"] == -1) $domainlimit = $go_api->lng("unlimitiert");
    $html_out .= '<tr>
       <td width="50%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("Domains angelegt").':</font></td>
       <td width="50%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$domainanzahl.' '.$go_api->lng("von").' '.$domainlimit.'</font></td>
     </tr>';
     }

     if($reseller["limit_domain_dns"] != 0){
        $dns_domain_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as anzahl from dns_nodes where groupid = $reseller_group and doctype_id = 1016");
    $dns_domainlimit = $reseller["limit_domain_dns"];
    $dns_domainanzahl = $dns_domain_count["anzahl"];
    if($reseller["limit_domain_dns"] == -1) $dns_domainlimit = $go_api->lng("unlimitiert");
    $html_out .= '<tr>
       <td width="50%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("DNS-Domains angelegt").':</font></td>
       <td width="50%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$dns_domainanzahl.' '.$go_api->lng("von").' '.$dns_domainlimit.'</font></td>
     </tr>';
     }

     // Slave Zones
     if($reseller["limit_slave_dns"] != 0){
     $slavednsanzahl = $go_api->db->queryOneRecord("SELECT COUNT(dns_secondary.doc_id) AS anzahl FROM dns_nodes,dns_secondary WHERE dns_nodes.doc_id = dns_secondary.doc_id and dns_nodes.doctype_id = '1028' AND dns_nodes.groupid = '".$reseller_group."'");
    $slavelimit = $reseller["limit_slave_dns"];
    if($reseller["limit_slave_dns"] == -1) $slavelimit = $go_api->lng("unlimitiert");
    $html_out .= '<tr>
       <td width="50%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("Slave Zones angelegt").':</font></td>
       <td width="50%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$slavednsanzahl["anzahl"].' '.$go_api->lng("von").' '.$slavelimit.'</font></td>
     </tr>';
     }

     // MySQL Databases
    if($reseller["limit_mysql"]){
      if($reseller["limit_mysql_anzahl_dbs"] != 0){
     $datenbankanzahl = $go_api->db->queryOneRecord("SELECT COUNT(isp_isp_datenbank.doc_id) AS anzahl FROM isp_nodes, isp_isp_datenbank WHERE isp_nodes.groupid = '".$reseller_group."' AND isp_nodes.doctype_id = '1029' AND isp_nodes.doc_id = isp_isp_datenbank.doc_id");
    $datenbanklimit = $reseller["limit_mysql_anzahl_dbs"];
    if($reseller["limit_mysql_anzahl_dbs"] == -1) $datenbanklimit = $go_api->lng("unlimitiert");
    $html_out .= '<tr>
       <td width="50%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("Datenbanken angelegt").':</font></td>
       <td width="50%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$datenbankanzahl["anzahl"].' '.$go_api->lng("von").' '.$datenbanklimit.'</font></td>
     </tr>';
       }
     }


    $html_out .= '</table></div><br>';



// END TOOL #############################################################################

$go_api->content->assign( array( TOOL => $html_out));
$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();

////////////////////////////////////////////////////////////////////////////////////////////////


        }
    } else {
        $go_api->msg($go_api->lng("msg_beta"),$go_api->lng("title_isp_beta"));
    }

} else {
    $go_api->msg($go_api->lng("msg_kein_win"),$go_api->lng("title_isp_beta"));
}



?>
