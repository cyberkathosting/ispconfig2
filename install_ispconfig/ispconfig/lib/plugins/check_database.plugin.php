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
 # Plugin Name: check_database
 # Version:     1.0
 # Datum:       01.03.2002
 #
 #############################################################
 
 if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class check_database_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;

    //if($go_info["server"]["os"] != "linux") $go_api->errorMessage("Dieses Plugin ist nur für Linux verfügbar.");
        $web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web where doc_id = '$doc_id'");

        $html_out .= '<div align="center"><table width="95%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">';

        $dbname = "web".$doc_id;
        //$dbname = "anwalt";
         $rows = $go_api->db->queryAllRecords("show table status from $dbname");

         //die(print_r($rows));

         $belegt = 0;
         if(is_array($rows)) {
                 foreach($rows as $row) {
                         $belegt += $row["Data_length"];
                 }
         $belegt = sprintf ("%01.2f", $belegt/1000);

         $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("Datenbank Name").':</font></td>
       <td width="30%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$dbname.'</font></td>
     </tr>';

         $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Username:</font></td>
       <td width="30%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$web["optionen_mysql_user"].'</font></td>
     </tr>';

         $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("Speicher belegt").':</font></td>
       <td width="30%" bgcolor="#FFFFFF" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$belegt.' KB</font></td>
     </tr>';

        } else {

                if($web["web_mysql"] == 1) {
                        $message = $go_api->lng("mysqldb_nicht_vorhanden");
                } else {
                        $message = $go_api->lng("mysql_nicht_aktiv");
                }


        $html_out .= '<tr>
       <td colspan="2" bgcolor="#FFFFFF" align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$message.'</font></td>
     </tr>';
        }


    $html_out .= '</table></div>';
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