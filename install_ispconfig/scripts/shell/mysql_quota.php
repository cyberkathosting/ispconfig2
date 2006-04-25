<?PHP

  /* MySQL quota tool */
  /* for ISPConfig    */

  /* Oliver Blaha     */

  /*
     This program is free software; you can redistribute it and/or modify
     it under the terms of the GNU General Public License as published by
     the Free Software Foundation; either version 2 of the License, or
     (at your option) any later version.
    
     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.
    
     You should have received a copy of the GNU General Public License
     along with this program; if not, write to the Free Software
     Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
  */

set_time_limit(0);

include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");

if ($go_info["server"]["db_type"] != "mysql")
  exit;

$mysql_table_web = 'isp_isp_web';
$mysql_table_db  = 'isp_isp_datenbank';

/* ----- */
 
if (!mysql_connect($go_info["server"]["db_host"], $go_info["server"]["db_user"], $go_info["server"]["db_password"])) {
  echo "Connection to MySQL server $mysql_host failed!";
  exit;
}

if (!mysql_select_db($go_info["server"]["db_name"])) {
  echo "Selection of database $mysql_db failed!";
  exit;
}

$sql_web = "SELECT * FROM $mysql_table_web;";
$result_web = mysql_query($sql_web);

while ($row_web = mysql_fetch_array($result_web)) {
  $web_id = intval($row_web['doc_id']);
  $quota = $row_web['web_mysql_space'];
  $over_quota_flag = ($row_web['web_mysql_space_exceeded']=='Y') ? 1 : 0;
  
  if (!isset($quota))
    $quota = -1;
  
  $quota *= 1024 * 1024; # use megabytes
  
  $sql_db = "SELECT * FROM $mysql_table_db WHERE web_id='$web_id';";
  $result_db = mysql_query($sql_db);

  $db_list = array();
  $used = 0;

  while ($row_db = mysql_fetch_array($result_db)) {
    $quota_db = $row_db['datenbankname'];
    $db_list[] = $quota_db;
    $used += get_db_size($quota_db);
  }

  $over_quota = over_quota($used, $quota);

  $new_flag = $over_quota ? "Y" : "N";
  $used_fract = (($quota > 0) ? ($used / $quota) : (($quota == 0) ? 1 : 0));

  $sql_update = "UPDATE $mysql_table_web SET web_mysql_space_exceeded='$new_flag',web_mysql_space_used_fract=$used_fract WHERE doc_id='$web_id';";
  mysql_query($sql_update);

  if ($over_quota != $over_quota_flag) {
    foreach ($db_list as $quota_db) {
      grant_privileges($quota_db, !$over_quota);
    }
  }
}

function grant_privileges($db, $grant = true) {
  global $go_info;
  $privs = $go_info["server"]["mysql_quota_privs"];
  
  if (!isset($privs)) {
    $privs = array("Insert", "Create");
  }

  $assign = "_priv='" . ($grant ? "Y" : "N") . "'";
  $set = implode("$assign,", $privs) . $assign;

  if ($set != $assign) {
    $sql = "UPDATE mysql.db SET $set WHERE Db='$db'";
    mysql_query($sql);
  }
}

function over_quota($used, $quota) {
  return ($quota >= 0) && ($used >= $quota);
}

function get_db_size($db) {
  $sql = "SHOW TABLE STATUS FROM $db";
  $result = mysql_query($sql);

  $size = 0;

  while ($row = mysql_fetch_array($result)) {
    $size += $row['Index_length'] + $row['Data_length'];
  }

  return $size;
}

?>
