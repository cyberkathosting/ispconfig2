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
 # Plugin Name: check_services
 # Version:     1.1
 # Datum:       18.04.2003
 #
 #############################################################

class check_webspace_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;

    if($go_info["server"]["os"] != "linux") $go_api->errorMessage("Dieses Plugin ist nur für Linux verfügbar.");

    $web_id = $doc_id;

    if($web_id > 0) {
    $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web where doc_id = '$web_id'");
    $server_conf = $go_api->db->queryOneRecord("SELECT * FROM isp_server WHERE doc_id = '1'");
    $path_httpd_root = stripslashes($server_conf["server_path_httpd_root"]);

    $html_out .= '<div align="center"><table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">';

        if($go_info["server"]["sudo_du_enabled"]) {
            $fd = popen ("sudo du -h --max-depth=1 ".$path_httpd_root."/web".$web_id, "r");
        } else {
                $fd = popen ("du -h --max-depth=1 ".$path_httpd_root."/web".$web_id, "r");
        }

        while (!feof($fd)) {
        $buffer .= fgets($fd, 4096);
    }
    pclose($fd);

    $meminfo = split("\n",$buffer);

    foreach($meminfo as $mline){
    if(trim($mline) != "" and $x < count($meminfo) - 2) {

    $mpart = split($path_httpd_root."/web".$web_id,$mline);
    $text = $mpart[1];

    $detail .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$text.'</font></td>
       <td width="30%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$mpart[0].'</font></td>
     </tr>';
    }

    $x++;
    }

    if($web["web_speicher"] != '-1') {
        $web_speicher = $web["web_speicher"] . "MB";
    } else {
        $web_speicher = $go_api->lng("unlimitiert");
    }

    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("Speicherplatz gesamt").':</font></td>
       <td width="30%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$web_speicher.'</font></td>
     </tr>';

     $db_size = 0; // nicht mehr für Gesamtspeicherplatz mitrechnen, da jetzt separat einstellbar
     $mpart = split($path_httpd_root."/web".$web_id,$meminfo[$x - 2]);
         //stristr()die(print_r($mpart));
         if(stristr($mpart[0],"k")) {
                 $gesamt_groesse = sprintf("%01.2f", $mpart[0] + $db_size) . "kB";
         } elseif (stristr($mpart[0],"M")) {
                 $gesamt_groesse = sprintf("%01.2f",$mpart[0] + ($db_size / 1024))."MB";
         } elseif (stristr($mpart[0],"G")) {
                 $gesamt_groesse = sprintf("%01.2f",$mpart[0] + ($db_size / 1048576))."GB";
         } else {
                 $gesamt_groesse = sprintf("%01.2f", $mpart[0] + $db_size) . "kB";
         }

     $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("verbraucht").':</font></td>
       <td width="30%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$gesamt_groesse.'</font></td>
     </tr>';

        // Größe mySQL Datenbank
        $belegt = 0;
        /*
        // Hole Datenbanken
        $datenbanken = $go_api->db->queryAllRecords("SELECT datenbankname from isp_isp_datenbank, isp_dep where isp_isp_datenbank.doc_id = isp_dep.child_doc_id and isp_isp_datenbank.doctype_id = isp_dep.child_doctype_id and isp_dep.parent_doctype_id = $doctype_id and isp_dep.parent_doc_id = $doc_id and isp_dep.child_doctype_id = 1029");
        if(!empty($datenbanken)){
          foreach($datenbanken as $datenbank){
            //$dbname = "web".$doc_id;
            $dbname = $datenbank["datenbankname"];
            $rows = $go_api->db->queryAllRecords("show table status from $dbname");
            if(is_array($rows)) {
                 foreach($rows as $row) {
                         $belegt += $row["Data_length"] + $row["Index_length"];
                 }
            }
          }
        }

        $db_size = $belegt / 1024;

        */

        $used_fract = floatval($web["web_mysql_quota_used_fract"]);
        $mysqlquota = floatval($web["web_mysql_quota"]);

    $db_size = $used_fract * $mysqlquota;

    if ($mysqlquota > 0) {
        $html_out .= '</table></div>';
        $html_out .= '<br />';
        $html_out .= '<div align="center"><table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">';
        $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">DB::MySQL</font></td>
       <td width="30%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.sprintf("%01.1f ".$go_api->lng("von")." %01.1fMB<br />(%01.1f%%)", $db_size, $mysqlquota, $used_fract * 100).'</font></td>
     </tr>';
    }

    $html_out .= '</table></div>';

    $html_out .= '<br>';
    $html_out .= '<div align="center"><table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">';
    $html_out .= $detail;
    $html_out .= '</table></div>';

    } else {
    $go_api->errorMessage($go_api->lng("error_web_id"));
    }
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