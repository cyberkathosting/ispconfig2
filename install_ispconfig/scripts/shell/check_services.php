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

class check_services {

    function warning()
    {

    $server_name = trim(str_replace("\n", "", shell_exec("hostname")));
    $warning = "";

    /*
    // Checke Webserver
    if(!$this->_check_tcp('localhost',80)) {
    $warning .= "Warning: service httpd not running (server: ".$server_name.")!\n\n";
    }
    */

    /*
    // Checke FTP-Server
    if(!$this->_check_ftp('localhost',21)) {
    $warning .= "Warning: service ftpd not running (server: ".$server_name.")!\n\n";
    }
    */

    /*
    // Checke Telnet-Server
    if(!$this->_check_tcp('localhost',22)) {
    $warning .= "Warning: service sshd not running (server: ".$server_name.")!\n\n";
    }
    */

    /*
     // Checke SMTP-Server
    if(!$this->_check_tcp('localhost',25)) {
    $warning .= "Warning: service sendmail not running (server: ".$server_name.")!\n\n";
    }
    */

    /*
     // Checke POP3-Server
    if(!$this->_check_tcp('localhost',110)) {
    $warning .= "Warning: service pop3 not running (server: ".$server_name.")!\n\n";
    }
    */

    /*
     // Checke BIND-Server
    if(!$this->_check_tcp('localhost',53)) {
    $warning .= "Warning: service named not running (server: ".$server_name.")!\n\n";
    }
    */

      // Checke MYSQL-Server
    if(!$this->_check_tcp('localhost',3306)) {
    $warning .= "Warning: service mysqld not running (server: ".$server_name.")!\n\n";
    }

    if($warning != ""){
      $warning .= "Message generated at ".date("F j, Y, G:i", time()).".\n";
      return $warning;
    } else {
      return false;
    }

    }

    function _check_tcp ($host,$port) {

        $fp = fsockopen ($host, $port, &$errno, &$errstr, 2);

        if ($fp) {
            fclose($fp);
            return true;
        } else {
            return false;
        }
    }

    function _check_udp ($host,$port) {

        $fp = fsockopen ('udp://'.$host, $port, &$errno, &$errstr, 2);

        if ($fp) {
            fclose($fp);
            return true;
        } else {
            return false;
        }
    }

    function _check_ftp ($host,$port){

      $conn_id = @ftp_connect($host, $port);

      if($conn_id){
        @ftp_close($conn_id);
        return true;
      } else {
        return false;
      }
    }
}

$cs = new check_services;

if(is_file("/home/admispconfig/ispconfig/adminmail.txt")){
  $fp = fopen ("/home/admispconfig/ispconfig/adminmail.txt", "r");
  $email = trim(fread($fp, filesize ("/home/admispconfig/ispconfig/adminmail.txt")));
  fclose($fp);
  $email = str_replace("\r\n", "", $email);
  $email = str_replace("\r", "", $email);
  $email = str_replace("\n", "", $email);
}

if(!empty($email) && strstr($email, "@") && strlen($email) > 3 && $cs->warning()) mail($email, "WARNING: services not running", $cs->warning());

?>