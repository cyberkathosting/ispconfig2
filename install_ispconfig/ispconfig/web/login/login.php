<? include("../../lib/config.inc.php");
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

if(isset($_REQUEST["go_info"])) die('Variable not allowed as REQUEST parameter!');
if(!defined('SERVER_ROOT')) die('Include file is missing. Please run the setup script as described in the installation manual.');

$dbclass = CLASSES_ROOT . DIR_TRENNER ."ispconfig_db_".DB_TYPE.".lib.php";

include_once($dbclass);
$dbname = 'db_'.DB_TYPE;
$db = new $dbname;

$username = addslashes($username);
$passwort = addslashes($passwort);

if (empty($username) or empty($passwort))
{
  header("Location: ../login.php?err=103");
  exit; 
}

$sql = sprintf('SELECT COUNT(id) as anzahl from sys_login WHERE ip = "%s" AND logintime >= (UNIX_TIMESTAMP() - 1800) AND status = "failure"', addslashes($_SERVER['REMOTE_ADDR']));
$res = $db->queryOneRecord($sql);
if ($res['anzahl'] >= 6)
{
  header("Location: ../login.php?err=105");
  exit;  
}

$laston = date("y-m-d H:i:s");
//$conn = mysql_query("SELECT * FROM sys_user where username = '$username'");
$sql = "SELECT * FROM sys_user WHERE username = '{$username}' AND (passwort = md5('{$passwort}') OR passwort = PASSWORD('{$passwort}'))";
//die($sql);

if ($row = $db->queryOneRecord($sql) and $passwort != "")
{
  if ($row["doc_id"] != 0 and $row["gueltig"] == "1")
  {
    $passwort = stripslashes($passwort);
    include("../../lib/session.inc.php");
    $time = mktime()+86400;
    $date = date("l, d-M-y H:i:s", ($time));
    header("Set-Cookie: sessionispconfig=$s; expires=0 GMT; path=/;");
    echo("<head><meta http-equiv='refresh' content='0;URL=trylogin.php?s=$s&v=$version'></head>");
    
    // Logging of the Login-Attempt (success)
    $db->query(sprintf('INSERT INTO sys_login (username, ip, logintime, status) VALUES("%s", "%s", %d, "%s")', $username, addslashes($_SERVER['REMOTE_ADDR']), time(), 'success'));

    $db->query('DELETE FROM sys_login WHERE logintime < (UNIX_TIMESTAMP() - 7*24*3600)');
  } 
  else 
  {
    // Logging of the Login-Attempt (failure)
    $db->query(sprintf('INSERT INTO sys_login (username, ip, logintime, status) VALUES("%s", "%s", %d, "%s")', $username, $_SERVER['REMOTE_ADDR'], time(), 'failure'));

    header("Location: ../login.php?err=102");
    exit;
  }
}
else
{
  // Logging of the Login-Attempt (failure)
  $db->query(sprintf('INSERT INTO sys_login (username, ip, logintime, status) VALUES("%s", "%s", %d, "%s")', $username, $_SERVER['REMOTE_ADDR'], time(), 'failure'));

  header("Location: ../login.php?err=101");
  exit;
}



?>