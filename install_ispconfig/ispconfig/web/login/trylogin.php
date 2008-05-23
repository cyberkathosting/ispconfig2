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
// Versionen ab 3.3

if(isset($_REQUEST["go_info"])) die('Variable not allowed as REQUEST parameter!');
if(!defined('SERVER_ROOT')) die('Include file is missing. Please run the setup script as described in the installation manual.');


$dbclass = CLASSES_ROOT . DIR_TRENNER ."ispconfig_db_".DB_TYPE.".lib.php";
include_once($dbclass);
$dbname = 'db_'.DB_TYPE;
$db = new $dbname;

$s = addslashes($s);

if($sessionispconfig == $s) {
        // wenn cookie gesetzt
        $db->query("UPDATE session SET remote_addr = 'cookie' where sessionid = '$s'");
        header("Location: ../index.php?s=$s");
        exit;

} else {
        //$conn = mysql_query("select * from session where sessionid = '$s'");
        //$DB = mysql_fetch_array($conn);
    $row = $db->queryOneRecord("select * from session where sessionid = '$s'");
        // Wenn Das Setzen der Remote Adresse funktioniert hat
        if($row["remote_addr"] == $HTTP_SERVER_VARS["REMOTE_ADDR"]){
        header("Location: ../index.php?s=$s");
        exit;
        }
}
header("Location: login_fehler.php");

?>