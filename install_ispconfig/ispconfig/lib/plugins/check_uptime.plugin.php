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

 if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class check_uptime_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;


    if($go_info["server"]["os"] != "linux") $go_api->errorMessage("Dieses Plugin ist nur für Linux verfügbar.");

    $html_out .= '<div align="center"><table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">';

    $fd = popen ("uptime", "r");
    while (!feof($fd)) {
        $buffer .= fgets($fd, 4096);
    }

        $uptime = explode(",",strrev($buffer));

    $online = explode("  ",strrev($uptime[4]));

    $proc_uptime = shell_exec("cat /proc/uptime | cut -f1 -d' '");
    $days = floor($proc_uptime/86400);
    $hours = floor(($proc_uptime-$days*86400)/3600);
    $minutes = str_pad(floor(($proc_uptime-$days*86400-$hours*3600)/60), 2, "0", STR_PAD_LEFT);

    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("Server Online seit").':</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$days.'d, '.$hours.':'.$minutes.'h</font></center></td>
     </tr>';

    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("User Online").':</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.strrev($uptime[3]).'</font></center></td>
     </tr>';

     $ausl = explode(":",strrev($uptime[2]));
     $ausl1 = $ausl[1];


     $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("System Load 1 Minute").':</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$ausl1.'</font></center></td>
     </tr>';

     $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("System Load 5 Minuten").':</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.strrev($uptime[1]).'</font></center></td>
     </tr>';

     $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("System Load 15 Minuten").':</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.strrev($uptime[0]).'</font></center></td>
     </tr>';

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