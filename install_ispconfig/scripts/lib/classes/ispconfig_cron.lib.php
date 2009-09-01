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

class cron{

var $FILE = "/root/ispconfig/scripts/lib/classes/ispconfig_cron.lib.php";

function make_cron($doc_id) {
  global $mod, $isp_web;

  if(!$cron = $mod->db->queryOneRecord("SELECT * FROM isp_isp_cron WHERE user_id = $doc_id AND status != ''")) return false;
  if(!$user_crons = $mod->db->queryAllRecords("SELECT isp_isp_cron.doc_id, isp_isp_cron.cron_active, isp_isp_cron.cron_minutes, isp_isp_cron.cron_hours, isp_isp_cron.cron_days, isp_isp_cron.cron_months, isp_isp_cron.cron_weekdays, isp_isp_cron.cron_command, isp_isp_cron.cron_name, isp_nodes.status FROM isp_isp_cron, isp_nodes WHERE isp_isp_cron.user_id = $doc_id AND isp_isp_cron.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '".$isp_web->cron_doctype_id."'")) return false;

  $cron_jobs = array();
  $cron_jobs[] = '# CRON JOBS MANAGED BY ISPCONFIG. DO NOT EDIT BELOW!';
  foreach($user_crons as $user_cron){
    if($user_cron['cron_active'] && $user_cron['status']){
      if(strpos($user_cron['cron_minutes'], '*') !== FALSE) $user_cron['cron_minutes'] = '*';
      if(strpos($user_cron['cron_hours'], '*') !== FALSE) $user_cron['cron_hours'] = '*';
      if(strpos($user_cron['cron_days'], '*') !== FALSE) $user_cron['cron_days'] = '*';
      if(strpos($user_cron['cron_months'], '*') !== FALSE) $user_cron['cron_months'] = '*';
      if(strpos($user_cron['cron_weekdays'], '*') !== FALSE) $user_cron['cron_weekdays'] = '*';
      $cron_jobs[] = '#'.$user_cron['doc_id'].' '.$user_cron['cron_name'];
      $cron_jobs[] = $user_cron['cron_minutes'].' '.$user_cron['cron_hours'].' '.$user_cron['cron_days'].' '.$user_cron['cron_months'].' '.$user_cron['cron_weekdays'].' '.$user_cron['cron_command'];
    }
  }

  $user = $mod->system->data["isp_isp_user"][$doc_id];
  //$sql = "SELECT * FROM isp_dep WHERE child_doc_id = '$doc_id' AND child_doctype_id = '".$isp_web->user_doctype_id."'";

  $user_username = $user["user_username"];

  $temp_crontab = "/root/ispconfig/.crontab_".$user_username.".txt";

  exec("crontab -u $user_username -l > ".$temp_crontab);
  list($existing_cron_jobs,) = split('# CRON JOBS MANAGED BY ISPCONFIG. DO NOT EDIT BELOW!', $mod->file->rf($temp_crontab));
  $mod->file->wf($temp_crontab, $existing_cron_jobs);
  //$existing_cron_jobs = $mod->file->rf($temp_crontab);
  foreach($cron_jobs as $cron_job){
    if(!strstr($existing_cron_jobs, $cron_job)){
      $mod->file->af($temp_crontab, "\n".$cron_job."\n");
    }
  }
  $mod->file->wf($temp_crontab, trim($mod->file->rf($temp_crontab)));
  $mod->file->remove_blank_lines($temp_crontab);
  $mod->file->af($temp_crontab, "\n");
  exec("crontab -u ".$user_username." ".$temp_crontab." &> /dev/null");
  unlink($temp_crontab);

  $mod->db->query("UPDATE isp_isp_cron SET status = '' WHERE user_id = '".$doc_id."'");
}

}
?>