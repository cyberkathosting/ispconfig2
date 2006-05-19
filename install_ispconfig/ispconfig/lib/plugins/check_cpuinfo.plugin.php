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
 # Version:     1.0
 # Datum:       01.03.2002
 #
 #############################################################
 
if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class check_cpuinfo_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;

    if($go_info["server"]["os"] != "linux") $go_api->errorMessage("Dieses Plugin ist nur für Linux verfügbar.");

    $html_out .= '<div align="center"><table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">';

        $n = 0;
        if(is_readable("/proc/cpuinfo")) {
            if($fd = fopen ("/proc/cpuinfo", "r")) {
                    while (!feof($fd)) {
                        $buffer .= fgets($fd, 4096);
                                $n++;
                                if($n > 100) break;
                    }
                    fclose($fd);
                }
    }

    $meminfo = split("\n",$buffer);

        if(is_array($meminfo)) {
    foreach($meminfo as $mline){
    if(trim($mline) != "") {

    $mpart = split(":",$mline);

    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$mpart[0].':</font></td>
       <td width="30%" bgcolor="#FFFFFF" align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$mpart[1].'</font></td>
     </tr>';
    }
        }

    $x++;
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