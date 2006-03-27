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
 # Plugin Name: check_disk
 # Version:     1.1
 # Datum:       18.04.2003
 #
 #############################################################

class check_disk_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;

    if($go_info["server"]["os"] != "linux") $go_api->errorMessage("Dieses Plugin ist nur für Linux verfügbar.");

    $html_out .= '<div align="center"><table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">';

    $fd = popen ("df -h", "r");
    while (!feof($fd)) {
        $buffer .= fgets($fd, 4096);
    }

        $df_out = split("\n",$buffer);
        for($i=0;$i<sizeof($df_out);$i++){
          if(ltrim($df_out[$i]) != $df_out[$i]){
            if(isset($df_out[($i-1)])){
              $df_out[($i-1)] .= $df_out[$i];
              unset($df_out[$i]);
            }
          }
        }

        $html_out .= '<tr>';
        $mrow = 0;
        foreach($df_out as $df_line) {
        $values = preg_split ("/[\s]+/", $df_line);
        $mln = 0;
        $font_class = 'normal_bold';
        if($mrow > 0) $font_class = 'normal';
        foreach($values as $value) {
        $align = 'left';
        if($mln > 0 and $mln < 5) $align = 'right';
        if($mln < 6 and $value != "") $html_out .= '<td bgcolor="#FFFFFF" class="'.$font_class.'" align="'.$align.'">'.$value.'</td>';
        $mln++;
        }
        $mrow++;

        $html_out .= '</tr>';
        }


    /*

    //$online = split("  ",strrev($uptime[4]));

    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Uptime:</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.strrev($uptime[4]).'</center></td>
     </tr>';

    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("User Online").':</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.strrev($uptime[3]).'</center></td>
     </tr>';

     $ausl = split(":",strrev($uptime[2]));
     $ausl1 = $ausl[1];


     $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("CPU Auslastung 1 Minute").':</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.$ausl1.'</center></td>
     </tr>';

     $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("CPU Auslastung 5 Minuten").':</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.strrev($uptime[1]).'</center></td>
     </tr>';

     $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">'.$go_api->lng("CPU Auslastung 15 Minuten").':</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.strrev($uptime[0]).'</center></td>
     </tr>';
    */


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