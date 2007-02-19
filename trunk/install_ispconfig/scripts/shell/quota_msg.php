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

include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");
$server_id = $mod->system->server_id;

$server = $mod->system->server_conf;
$server_admin = $server['server_admin_email'];
if($server['server_host'] != ''){
  $server_name = $server['server_host'].'.'.$server['server_domain'];
} else {
  $server_name = $server['server_domain'];
}
$threshold = 0.85; // Wert zwischen 0 und 1; 0.9 bedeutet, 90% des Quotas sind erschöpft, und eine Nachricht wird verschickt

putenv("PATH=/sbin:/usr/sbin:/bin:/usr/bin:/usr/local/sbin:/usr/local/bin:/usr/X11R6/bin");
$user_quota = $mod->file->unix_nl(trim(shell_exec('repquota -au')));
$group_quota = $mod->file->unix_nl(trim(shell_exec('repquota -ag')));

$user_quota_lines = explode("\n", $user_quota);
$group_quota_lines = explode("\n", $group_quota);

$user_quota = array_slice($user_quota_lines, 5);
$group_quota = array_slice($group_quota_lines, 5);

if(is_array($user_quota) && !empty($user_quota)){
  foreach($user_quota as $quota){
    $columns = preg_split("/[\s]+/", $quota, -1, PREG_SPLIT_NO_EMPTY);
    if(is_array($columns) && !empty($columns)){
      $name = trim($columns[0]);
      $used = trim($columns[2]);
      $allocated = trim($columns[3]);
      if($allocated > 0 && $used >= ($allocated * $threshold)){
        if($user_check = $mod->db->queryOneRecord("SELECT isp_isp_user.user_name, isp_isp_user.user_email, isp_isp_user.doc_id FROM isp_isp_user, isp_nodes WHERE isp_isp_user.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '1014' AND isp_nodes.status = '1' AND isp_isp_user.user_username = '".$name."'")){
          $users_over_quota[$name]['used'] = $used;
          $users_over_quota[$name]['allocated'] = $allocated;
          $users_over_quota[$name]['real_name'] = $user_check['user_name'];

          $web = $mod->db->queryOneRecord("SELECT isp_isp_web.doc_id, isp_isp_web.web_host, isp_isp_web.web_domain FROM isp_isp_web, isp_dep WHERE isp_isp_web.doc_id = isp_dep.parent_doc_id AND isp_isp_web.doctype_id = isp_dep.parent_doctype_id AND isp_dep.child_doctype_id = 1014 AND isp_dep.child_doc_id = ".$user_check['doc_id']);

          $users_over_quota[$name]['email'] = $user_check['user_email'].'@'.$web['web_domain'];
          if($web['web_host'] != ''){
            $users_over_quota[$name]['web'] = $web['web_host'].'.'.$web['web_domain'];
          } else {
            $users_over_quota[$name]['web'] = $web['web_domain'];
          }
          $users_over_quota[$name]['group'] = 'web'.$web['doc_id'];

          $client = $mod->db->queryOneRecord("SELECT isp_isp_kunde.doc_id, isp_isp_kunde.kunde_firma, isp_isp_kunde.kunde_vorname, isp_isp_kunde.kunde_name, isp_isp_kunde.kunde_strasse, isp_isp_kunde.kunde_plz, isp_isp_kunde.kunde_ort, isp_isp_kunde.kunde_land, isp_isp_kunde.kunde_telefon, isp_isp_kunde.kunde_fax, isp_isp_kunde.kunde_email, isp_isp_kunde.kunde_internet FROM isp_isp_kunde, isp_dep WHERE isp_dep.parent_doc_id = isp_isp_kunde.doc_id AND isp_dep.child_doc_id = ".$web['doc_id']." AND isp_dep.child_doctype_id = 1013 AND isp_dep.parent_doctype_id = 1012");

          $users_over_quota[$name]['kunde_firma'] = $client['kunde_firma'];
          $users_over_quota[$name]['kunde_vorname'] = $client['kunde_vorname'];
          $users_over_quota[$name]['kunde_name'] = $client['kunde_name'];
          $users_over_quota[$name]['kunde_strasse'] = $client['kunde_strasse'];
          $users_over_quota[$name]['kunde_plz'] = $client['kunde_plz'];
          $users_over_quota[$name]['kunde_ort'] = $client['kunde_ort'];
          $users_over_quota[$name]['kunde_land'] = $client['kunde_land'];
          $users_over_quota[$name]['kunde_telefon'] = $client['kunde_telefon'];
          $users_over_quota[$name]['kunde_fax'] = $client['kunde_fax'];
          $users_over_quota[$name]['kunde_email'] = $client['kunde_email'];
          $users_over_quota[$name]['kunde_internet'] = $client['kunde_internet'];

          $kunde_node = $mod->db->queryOneRecord("SELECT groupid FROM isp_nodes WHERE doc_id = '".$client['doc_id']."' AND doctype_id = '1012'");
          $reseller_group = $kunde_node["groupid"];
          if($reseller = $mod->db->queryOneRecord("SELECT firma, vorname, name, strasse, plz, ort, telefon, fax, email, internet, land FROM isp_isp_reseller WHERE reseller_group = $reseller_group")){
            $users_over_quota[$name]['reseller_firma'] = $reseller['firma'];
            $users_over_quota[$name]['reseller_vorname'] = $reseller['vorname'];
            $users_over_quota[$name]['reseller_name'] = $reseller['name'];
            $users_over_quota[$name]['reseller_strasse'] = $reseller['strasse'];
            $users_over_quota[$name]['reseller_plz'] = $reseller['plz'];
            $users_over_quota[$name]['reseller_ort'] = $reseller['ort'];
            $users_over_quota[$name]['reseller_land'] = $reseller['land'];
            $users_over_quota[$name]['reseller_telefon'] = $reseller['telefon'];
            $users_over_quota[$name]['reseller_fax'] = $reseller['fax'];
            $users_over_quota[$name]['reseller_email'] = $reseller['email'];
            $users_over_quota[$name]['reseller_internet'] = $reseller['internet'];
          }
        }
      }
    }
  }
}

//print_r($users_over_quota);

if(is_array($group_quota) && !empty($group_quota)){
  foreach($group_quota as $quota){
    $columns = preg_split("/[\s]+/", $quota, -1, PREG_SPLIT_NO_EMPTY);
    if(is_array($columns) && !empty($columns)){
      $name = trim($columns[0]);
      $used = trim($columns[2]);
      $allocated = trim($columns[3]);
      if($allocated > 0 && $used >= ($allocated * $threshold)){
        if($web_check = $mod->db->queryOneRecord("SELECT isp_isp_web.doc_id, isp_isp_web.web_host, isp_isp_web.web_domain FROM isp_isp_web, isp_nodes WHERE isp_isp_web.doc_id = isp_nodes.doc_id AND isp_nodes.doctype_id = '1013' AND isp_isp_web.server_id = '".$server_id."' AND isp_nodes.status = '1' AND isp_isp_web.doc_id = ".str_replace('web', '', $name))){
          $groups_over_quota[$name]['used'] = $used;
          $groups_over_quota[$name]['allocated'] = $allocated;
          if($web_check['web_host'] != ''){
            $groups_over_quota[$name]['web'] = $web_check['web_host'].'.'.$web_check['web_domain'];
          } else {
            $groups_over_quota[$name]['web'] = $web_check['web_domain'];
          }

          $client = $mod->db->queryOneRecord("SELECT isp_isp_kunde.doc_id, isp_isp_kunde.kunde_firma, isp_isp_kunde.kunde_vorname, isp_isp_kunde.kunde_name, isp_isp_kunde.kunde_strasse, isp_isp_kunde.kunde_plz, isp_isp_kunde.kunde_ort, isp_isp_kunde.kunde_land, isp_isp_kunde.kunde_telefon, isp_isp_kunde.kunde_fax, isp_isp_kunde.kunde_email, isp_isp_kunde.kunde_internet FROM isp_isp_kunde, isp_dep WHERE isp_dep.parent_doc_id = isp_isp_kunde.doc_id AND isp_dep.child_doc_id = ".$web_check['doc_id']." AND isp_dep.child_doctype_id = 1013 AND isp_dep.parent_doctype_id = 1012");

          $groups_over_quota[$name]['kunde_firma'] = $client['kunde_firma'];
          $groups_over_quota[$name]['kunde_vorname'] = $client['kunde_vorname'];
          $groups_over_quota[$name]['kunde_name'] = $client['kunde_name'];
          $groups_over_quota[$name]['kunde_strasse'] = $client['kunde_strasse'];
          $groups_over_quota[$name]['kunde_plz'] = $client['kunde_plz'];
          $groups_over_quota[$name]['kunde_ort'] = $client['kunde_ort'];
          $groups_over_quota[$name]['kunde_land'] = $client['kunde_land'];
          $groups_over_quota[$name]['kunde_telefon'] = $client['kunde_telefon'];
          $groups_over_quota[$name]['kunde_fax'] = $client['kunde_fax'];
          $groups_over_quota[$name]['kunde_email'] = $client['kunde_email'];
          $groups_over_quota[$name]['kunde_internet'] = $client['kunde_internet'];

          $kunde_node = $mod->db->queryOneRecord("SELECT groupid FROM isp_nodes WHERE doc_id = '".$client['doc_id']."' AND doctype_id = '1012'");
          $reseller_group = $kunde_node["groupid"];
          if($reseller = $mod->db->queryOneRecord("SELECT firma, vorname, name, strasse, plz, ort, telefon, fax, email, internet, land FROM isp_isp_reseller WHERE reseller_group = $reseller_group")){
            $groups_over_quota[$name]['reseller_firma'] = $reseller['firma'];
            $groups_over_quota[$name]['reseller_vorname'] = $reseller['vorname'];
            $groups_over_quota[$name]['reseller_name'] = $reseller['name'];
            $groups_over_quota[$name]['reseller_strasse'] = $reseller['strasse'];
            $groups_over_quota[$name]['reseller_plz'] = $reseller['plz'];
            $groups_over_quota[$name]['reseller_ort'] = $reseller['ort'];
            $groups_over_quota[$name]['reseller_land'] = $reseller['land'];
            $groups_over_quota[$name]['reseller_telefon'] = $reseller['telefon'];
            $groups_over_quota[$name]['reseller_fax'] = $reseller['fax'];
            $groups_over_quota[$name]['reseller_email'] = $reseller['email'];
            $groups_over_quota[$name]['reseller_internet'] = $reseller['internet'];
          }
        }
      }
    }
  }
}

//print_r($groups_over_quota);
if(empty($users_over_quota) && empty($groups_over_quota)) die();

if($server_admin != "" && eregi("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,6}$", $server_admin) && !strstr($server_admin, " ") && !strstr($server_admin, "!") && !strstr($server_admin, "?") && !strstr($server_admin, "\"") && !strstr($server_admin, "(") && !strstr($server_admin, ")") && !strstr($server_admin, "[") && !strstr($server_admin, "]") && !strstr($server_admin, "{") && !strstr($server_admin, "}") && !strstr($server_admin, "/") && !strstr($server_admin, "#")){
  $headers  = "From: ".$server_admin." <".$server_admin.">\n";
  $headers .= "Reply-To: <".$server_admin.">\n";
  $headers .= "X-Sender: <".$server_admin.">\n";
  $headers .= "X-Mailer: PHP4\n"; // mailer
  $headers .= "X-Priority: 3\n"; // 1 UrgentMessage, 3 Normal
  $headers .= "Return-Path: <".$server_admin.">\n";
  $headers .= "MIME-Version: 1.0\n";
  $headers .= "Content-Type: text/plain\n";
  $subject = 'Some Users/Groups Are Near Or Already Over Their Quota Limit On Server '.$server_name;
  $message = 'The following users are near or already over their quota limit on server '.$server_name.':'."\n";
  $message .= '==================================================='."\n\n";

  if(is_array($users_over_quota) && !empty($users_over_quota)){
    foreach($users_over_quota as $key => $val){
      $message .= "Username:                           ".$key."\n";
      $message .= "Used Storage Space:           ".number_format(($val['used']/1024), 2, '.', '').' MB'."\n";
      $message .= "Allocated Storage Space:     ".number_format(($val['allocated']/1024), 2, '.', '').' MB'."\n";
      $message .= "User's Real Name:               ".$val['real_name']."\n";
      $message .= "User's Email Address:           ".$val['email']."\n";
      $message .= "Web Site:                             ".$val['web']."\n";
      $message .= "Group Of Web Site:             ".$val['group']."\n\n";
      $message .= "Customer's Company:           ".$val['kunde_firma']."\n";
      $message .= "Customer's Name:                ".$val['kunde_vorname'].' '.$val['kunde_name']."\n";
      $message .= "Customer's Address:             ".$val['kunde_strasse']."\n";
      $message .= "City:                                     ".$val['kunde_plz'].' '.$val['kunde_ort']."\n";
      $message .= "Country:                               ".$val['kunde_land']."\n";
      $message .= "Telephone:                           ".$val['kunde_telefon']."\n";
      $message .= "Telefax:                                ".$val['kunde_fax']."\n";
      $message .= "Customer's Email Address:   ".$val['kunde_email']."\n";
      $message .= "Customer's Web Site:           ".$val['kunde_internet']."\n\n";
      if(isset($val[reseller_firma])){
        $message .= "Reseller's Company:              ".$val['reseller_firma']."\n";
        $message .= "Reseller's Name:                    ".$val['reseller_vorname'].' '.$val['reseller_name']."\n";
        $message .= "Reseller's Address:                ".$val['reseller_strasse']."\n";
        $message .= "City:                                      ".$val['reseller_plz'].' '.$val['reseller_ort']."\n";
        $message .= "Country:                                ".$val['reseller_land']."\n";
        $message .= "Telephone:                            ".$val['reseller_telefon']."\n";
        $message .= "Telefax:                                 ".$val['reseller_fax']."\n";
        $message .= "Reseller's Email Address:       ".$val['reseller_email']."\n";
        $message .= "Reseller's Web Site:               ".$val['reseller_internet']."\n\n";
      } else {
        $message .= "This customer does not belong to a reseller.\n\n";
      }
      $message .= "----------------------------------------------------------------------------\n\n";
    }
  }
  $message .= "===================================================\n";
  $message .= "===================================================\n\n";
  $message .= "The following groups are near or already over their quota limit on server ".$server_name.":\n";
  $message .= "===================================================\n\n";

    if(is_array($groups_over_quota) && !empty($groups_over_quota)){
    foreach($groups_over_quota as $key => $val){
      $message .= "Group:                                 ".$key."\n";
      $message .= "Used Storage Space:           ".number_format(($val['used']/1024), 2, '.', '').' MB'."\n";
      $message .= "Allocated Storage Space:     ".number_format(($val['allocated']/1024), 2, '.', '').' MB'."\n";
      $message .= "Web Site:                             ".$val['web']."\n\n";
      $message .= "Customer's Company:           ".$val['kunde_firma']."\n";
      $message .= "Customer's Name:                ".$val['kunde_vorname'].' '.$val['kunde_name']."\n";
      $message .= "Customer's Address:             ".$val['kunde_strasse']."\n";
      $message .= "City:                                     ".$val['kunde_plz'].' '.$val['kunde_ort']."\n";
      $message .= "Country:                               ".$val['kunde_land']."\n";
      $message .= "Telephone:                           ".$val['kunde_telefon']."\n";
      $message .= "Telefax:                                ".$val['kunde_fax']."\n";
      $message .= "Customer's Email Address:   ".$val['kunde_email']."\n";
      $message .= "Customer's Web Site:           ".$val['kunde_internet']."\n\n";
      if(isset($val[reseller_firma])){
        $message .= "Reseller's Company:              ".$val['reseller_firma']."\n";
        $message .= "Reseller's Name:                    ".$val['reseller_vorname'].' '.$val['reseller_name']."\n";
        $message .= "Reseller's Address:                ".$val['reseller_strasse']."\n";
        $message .= "City:                                      ".$val['reseller_plz'].' '.$val['reseller_ort']."\n";
        $message .= "Country:                                ".$val['reseller_land']."\n";
        $message .= "Telephone:                            ".$val['reseller_telefon']."\n";
        $message .= "Telefax:                                 ".$val['reseller_fax']."\n";
        $message .= "Reseller's Email Address:       ".$val['reseller_email']."\n";
        $message .= "Reseller's Web Site:               ".$val['reseller_internet']."\n\n";
      } else {
        $message .= "This customer does not belong to a reseller.\n\n";
      }
      $message .= "----------------------------------------------------------------------------\n\n";
    }
  }
  mail($server_admin, $subject, $message, $headers);
}
?>