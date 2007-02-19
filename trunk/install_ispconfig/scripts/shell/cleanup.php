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

$FILE = "/root/ispconfig/scripts/shell/cleanup.php";
include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");

//right now $curdir is the current directory
$curdir = "/root/ispconfig/scripts";
$inputarray = opendir($curdir);
$resultarray = array();

while($var = readdir($inputarray))
{
//this checks that the var listed is a file
//but skips . and ..
  if(is_file($curdir . "/" . $var))
  {
    if($var != '.' && $var != '..')
      $resultarray[]=$var;
  }
}

for($x = 0; $x < sizeof($resultarray); $x++)
{
//instead of just echoing to the screen
//you could put code here to do something
//to each directory like chown or mv

  $strs = explode(".",$resultarray[$x]);
  $count= count($strs);
  $extension = ".".$strs[$count-1];
  //echo $extension;
  if($extension == ".sql" || $extension == ".gz"){
   //echo $resultarray[$x] . "\n";
   $dateiname = $strs[0];
   if (stristr($dateiname,"web")){
    $web_doc_id = substr($dateiname, 3);
    if($extension == ".sql"){
      list($f1,$f2) = explode("_", $web_doc_id);
      $web_doc_id = $f1;
    }
    //Löschen, wenn Web nicht mehr vorhanden; SQL-Datei auch, wenn vorhanden
    $web_delete = $mod->db->queryOneRecord("SELECT * from isp_isp_web WHERE doc_id = '$web_doc_id'");
    if(empty($web_delete)){
      $mod->log->caselog("rm -f $curdir/$resultarray[$x]", $FILE, __LINE__);
      //if(is_file($curdir. "/web".$web_doc_id.".sql")) $mod->log->caselog("rm -f $curdir/web$web_doc_id.sql", $FILE, __LINE__);
      //$mod->log->caselog("rm -f $curdir/web".$web_doc_id."_db*", $FILE, __LINE__);
    }
   } else {
    //User abfragen
    $username = $dateiname;
    $user_delete = $mod->db->queryOneRecord("SELECT * from isp_isp_user WHERE user_username = '$username'");
    if(empty($user_delete)){
      $mod->log->caselog("rm -f $curdir/$resultarray[$x]", $FILE, __LINE__);
    }
   }
   //echo $dateiname."\n";
  }
}

// phptmp aufräumen (Objekte älter als 60 Min.)
if($webs = $mod->db->queryAllRecords("SELECT doc_id FROM isp_isp_web")){
  foreach($webs as $web){
    if(is_dir(realpath($mod->system->server_conf["server_path_httpd_root"])."/web".$web["doc_id"]."/phptmp")){
      exec("cd ".$mod->system->server_conf["server_path_httpd_root"]."/web".$web["doc_id"]."/phptmp; find -cmin +60 | grep -v -w .no_delete | xargs rm &> /dev/null");
    }
  }
}


?>