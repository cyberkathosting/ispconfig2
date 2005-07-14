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

class check_services_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;
    //if($go_info["server"]["os"] != "linux") $go_api->errorMessage("Dieses Plugin ist nur für Linux verfügbar.");

    $html_out .= '<div align="center"><table width="80%" border="0" cellspacing="1" cellpadding="4" bgcolor="#CCCCCC">';

    // Checke Webserver
    if($this->_check_tcp('localhost',80)) {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#006600"><b>Online</b></font>';
    } else {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF0000"><b>Offline</b></font>';
    }
    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Web-Server:</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.$status.'</center></td>
     </tr>';


    // Checke FTP-Server
    if($this->_check_ftp('localhost',21)) {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#006600"><b>Online</b></font>';
    } else {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF0000"><b>Offline</b></font>';
    }
    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">FTP-Server:</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.$status.'</center></td>
     </tr>';

        /*
    // Checke Telnet-Server
    if($this->_check_tcp('localhost',22)) {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#006600"><b>Online</b></font>';
    } else {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF0000"><b>Offline</b></font>';
    }
    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Telnet-Server:</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.$status.'</center></td>
     </tr>';
    */

     // Checke SMTP-Server
    if($this->_check_tcp('localhost',25)) {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#006600"><b>Online</b></font>';
    } else {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF0000"><b>Offline</b></font>';
    }
    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">SMTP-Server:</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.$status.'</center></td>
     </tr>';

     // Checke POP3-Server
    if($this->_check_tcp('localhost',110)) {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#006600"><b>Online</b></font>';
    } else {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF0000"><b>Offline</b></font>';
    }
    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">POP3-Server:</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.$status.'</center></td>
     </tr>';

     // Checke BIND-Server
    if($this->_check_tcp('localhost',53)) {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#006600"><b>Online</b></font>';
    } else {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF0000"><b>Offline</b></font>';
    }
    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">BIND-Server:</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.$status.'</center></td>
     </tr>';

      // Checke MYSQL-Server
    //if($this->_check_tcp('localhost',3306)) {
    $status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#006600"><b>Online</b></font>';
    //} else {
    //$status = '<font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF0000"><b>Offline</b></font>';
    //}
    $html_out .= '<tr>
       <td width="70%" bgcolor="#FFFFFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">mySQL-Server:</font></td>
       <td width="30%" bgcolor="#FFFFFF"><center>'.$status.'</center></td>
     </tr>';


    $html_out .= '</table></div>';


    return $html_out;
    }

    function _check_tcp ($host,$port) {

        $fp = @fsockopen ($host, $port, &$errno, &$errstr, 2);

        if ($fp) {
            return true;
            fclose($fp);
        } else {
            return false;
            fclose($fp);
        }
    }

    function _check_udp ($host,$port) {

        $fp = @fsockopen ('udp://'.$host, $port, &$errno, &$errstr, 2);

        if ($fp) {
            return true;
            fclose($fp);
        } else {
            return false;
            fclose($fp);
        }
    }

    function _check_ftp ($host,$port){

      $conn_id = @ftp_connect($host, $port);

      if($conn_id){
        @ftp_close($conn_id);
        return true;
      } else {
        @ftp_close($conn_id);
        return false;
      }
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