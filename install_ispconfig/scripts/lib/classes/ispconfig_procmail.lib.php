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

class procmail {

var $FILE = "/root/ispconfig/scripts/lib/classes/ispconfig_procmail.lib.php";

function make_forward($doc_id) {
  global $mod, $isp_web;

  $user = $mod->system->data["isp_isp_user"][$doc_id];
  $sql = "SELECT * FROM isp_dep WHERE child_doc_id = '$doc_id' AND child_doctype_id = '".$isp_web->user_doctype_id."'";

  // doc_id des Webs bestimmen
  $web_dep = $mod->db->queryOneRecord($sql);
  $web_doc_id = $web_dep["parent_doc_id"];
  $web_path = $mod->system->server_conf["server_path_httpd_root"]."/web".$web_doc_id;
  $user_web = $mod->system->data["isp_isp_web"][$web_doc_id];
  $domain = $user_web["web_domain"];

  $user_username = $user["user_username"];
  //$user_emailalias = str_replace("\n", " ", $user["user_emailalias"]);
  //$alias = $user_username." ".$user_emailalias;

  if($user["user_admin"]){
    $datei = $web_path."/.forward";
    if(is_link($datei)) exec("rm -f $datei");
  } else {
    $datei = $web_path."/user/".$user_username."/.forward";
  }
  $mod->log->caselog("cp -f /root/ispconfig/isp/conf/forward.master $datei", $this->FILE, __LINE__);
  exec("chown $user_username:web$web_doc_id $datei &> /dev/null");
  exec("chmod 600 $datei");

  // Forwarding ab sofort via Procmail-Recipe
  /*
  if(!empty($user["user_emailweiterleitung"])){
    $forward_emails = str_replace("\n", ", ", trim($mod->file->unix_nl($user["user_emailweiterleitung"])));

    if(!$user["user_emailweiterleitung_local_copy"]){
      $datei_inhalt = $forward_emails;
    } else {
      $datei_inhalt = trim($mod->file->rf($datei)).", ".$forward_emails;
    }

    $mod->file->wf($datei, $datei_inhalt);
  }
  */

  // symbolische Links für Admin-User erstellen
  if($user["user_admin"]){
    if(is_file($web_path."/user/".$user_username."/.forward")){
      exec("rm -f ".$web_path."/user/".$user_username."/.forward");
    }
    exec("rm -f $web_path/.vacation.cache");
    if(!is_file($web_path."/user/".$user_username."/.vacation.cache")){
      touch($web_path."/user/".$user_username."/.vacation.cache");
      exec("chown ".$user_username.":web".$web_doc_id." ".$web_path."/user/".$user_username."/.vacation.cache");
    }
    @symlink($web_path."/user/".$user_username."/.vacation.cache", $web_path."/.vacation.cache");
  }
  clearstatcache();
}

function make_procmailrc($doc_id) {
  global $mod, $isp_web;

  $user = $mod->system->data["isp_isp_user"][$doc_id];
  $sql = "SELECT * FROM isp_dep WHERE child_doc_id = '$doc_id' AND child_doctype_id = '".$isp_web->user_doctype_id."'";

  // doc_id des Webs bestimmen
  $web_dep = $mod->db->queryOneRecord($sql);
  $web_doc_id = $web_dep["parent_doc_id"];
  $web_path = $mod->system->server_conf["server_path_httpd_root"]."/web".$web_doc_id;

  $user_username = $user["user_username"];

  // Maildir-Format
  if($mod->system->server_conf["use_maildir"] == 1){ // Nur bei Maildir
    $maildir_comment = "";

    // TB: Maildirmake aufrufen, sonst Fehler beim Mailabholen möglich
    if(!is_dir($web_path."/user/".$user_username."/Maildir")) $mod->log->caselog("maildirmake ".$web_path."/user/".$user_username."/Maildir &> /dev/null", "maildirmake ".$web_path."/user/".$user_username."/Maildir &> /dev/null", __LINE__);

    if(!is_dir($web_path."/user/".$user_username."/Maildir")) $mod->log->phpcaselog(mkdir($web_path."/user/".$user_username."/Maildir", 0700), "create ".$web_path."/user/".$user_username."/Maildir", $this->FILE, __LINE__);
    exec("chown -R ".$user_username.":web".$web_doc_id." ".$web_path."/user/".$user_username."/Maildir");
    exec("chmod 700 ".$user_username.":web".$web_doc_id." ".$web_path."/user/".$user_username."/Maildir");
    if($user["user_admin"]){
      exec("rm -f $web_path/Maildir &> /dev/null");
      $mod->log->phpcaselog(@symlink($web_path."/user/".$user_username."/Maildir", $web_path."/Maildir"), "symlink ".$web_path."/Maildir", $this->FILE, __LINE__);
    } else {
      if(is_link($web_path."/Maildir") && readlink($web_path."/Maildir") == $web_path."/user/".$user_username."/Maildir") $mod->log->phpcaselog(unlink($web_path."/Maildir"), "delete ".$web_path."/Maildir", $this->FILE, __LINE__);
    }
  } else {
    $maildir_comment = "## ";
  }

  if($mod->system->server_conf["use_maildir"]){ // Bei allem, außer bei mbox
    $user_mailquota = "";
    $quota_comment = '## ';
  } else {
    if($user["user_mailquota"] == "-1"){
      $user_mailquota = "";
      $quota_comment = '## ';
    } else {
      $user_mailquota = "QUOTA=".(1048576 * intval($user["user_mailquota"]));
      $quota_comment = '';
    }
  }

  if($user["user_autoresponder"]){
    $autoresponder_comment = "";
  } else {
    $autoresponder_comment = "## ";
  }

  if($user["user_mailscan"]){
    $mailscan_comment = "";
  } else {
    $mailscan_comment = "## ";
  }

  if($user["user_spamfilter"]){
    $spamfilter_comment = "";
  } else {
    $spamfilter_comment = "## ";
  }

  if(!isset($user["antivirus"])) $user["antivirus"] = 0;
  if($user["antivirus"]){
    $antivirus_comment = "";
  } else {
    $antivirus_comment = "## ";
  }
  
  if (!empty($user["user_emailweiterleitung"])){
    $forward_emails = str_replace("\n", ",", trim($mod->file->unix_nl($user["user_emailweiterleitung"])));

    if (!$user["user_emailweiterleitung_local_copy"]) {
      $forward_code = "\n:0 w\n! $forward_emails\n";
    } else {
      $forward_code = "\n:0 c\n! $forward_emails\n";
    }

    if ($user["user_emailweiterleitung_no_scan"]) {
	$forward = "";
	$forward_no_scan = $forward_code;
    } else {
	$forward_no_scan = "";
	$forward = $forward_code;
    }
  }

  $proc_dir   = $this->create_procmail_dir($web_path, $user_username, $user["user_admin"]);
  $recipe_dir = $this->create_procmail_dir($web_path, $user_username);

  //.procmailrc erstellen
  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "procmailrc.master"));

  // Variablen zuweisen
  $mod->tpl->assign( array(MAILDIR_COMMENT => $maildir_comment,
                      PMDIR => $recipe_dir,
                      QUOTA => $user_mailquota,
                      QUOTA_COMMENT => $quota_comment,
                      MAILSCAN_COMMENT => $mailscan_comment,
                      SPAMASSASSIN_COMMENT => $spamfilter_comment,
                      AUTORESPONDER_COMMENT => $autoresponder_comment,
                      ANTIVIRUS_COMMENT => $antivirus_comment,
		      FORWARD => $forward,
		      FORWARD_NO_SCAN => $forward_no_scan));

  $mod->tpl->parse(TABLE, table);

  $procmail_text = $mod->tpl->fetch();

  $datei = "$proc_dir/.procmailrc";
  if ($user["user_admin"] && is_link($datei)) {
    exec("rm -f $datei");
  }
  $mod->file->wf($datei, $procmail_text);

  if($user["user_admin"]){
    if(is_file($web_path."/user/".$user_username."/.procmailrc")){
      exec("rm -f ".$web_path."/user/".$user_username."/.procmailrc");
    }
  }

  $root_gruppe = $mod->system->root_group();
  exec("chown root:$root_gruppe $datei &> /dev/null");
  exec("chmod 644 $datei");
  clearstatcache();
}

function make_recipes($doc_id) {
  global $mod, $isp_web;

  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "vacation.msg.master"));

  $user = $mod->system->data["isp_isp_user"][$doc_id];
  $sql = "SELECT * FROM isp_dep WHERE child_doc_id = '$doc_id' AND child_doctype_id = '".$isp_web->user_doctype_id."'";

  // doc_id des Webs bestimmen
  $web_dep = $mod->db->queryOneRecord($sql);
  $web_doc_id = $web_dep["parent_doc_id"];
  $web_path = $mod->system->server_conf["server_path_httpd_root"]."/web".$web_doc_id;
  $user_web = $mod->system->data["isp_isp_web"][$web_doc_id];
  $domain = $user_web["web_domain"];

  $user_username = $user["user_username"];
  $user_name = $user["user_name"];
  $user_autoresponder_text = $user["user_autoresponder_text"];

  $recipe_dir = $this->create_procmail_dir($web_path, $user_username);

  // Variablen zuweisen
  $mod->tpl->assign( array(USER_AUTORESPONDER_TEXT => $user_autoresponder_text));

  $mod->tpl->parse(TABLE, table);

  $vacation_text = $mod->tpl->fetch();

  $datei = $recipe_dir."/.vacation.msg";

  $mod->file->wf($datei, $vacation_text);

  $root_gruppe = $mod->system->root_group();
  exec("chown root:$root_gruppe $datei &> /dev/null");
  exec("chmod 644 $datei");

  //autoresponder.rc erstellen
  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "autoresponder.rc.master"));

  if($user["user_email"] != '') {
          $email_adresse = $user["user_email"]."@".$domain;
  } else {
    $email_adresse = $user_username."@".$domain;
  }

  // Variablen zuweisen
  $mod->tpl->assign( array( PFAD => $recipe_dir,
                            EMAIL_ADDRESS => $email_adresse));

  $mod->tpl->parse(TABLE, table);

  $autoresponderrc_text = $mod->tpl->fetch();

  $datei2 = $recipe_dir."/.autoresponder.rc";
  $mod->file->wf($datei2, $autoresponderrc_text);

  exec("chown root:$root_gruppe $datei2 &> /dev/null");
  exec("chmod 644 $datei2");

  //quota.rc erstellen
  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "quota.rc.master"));

  // Variablen zuweisen
  $mod->tpl->assign( array(PFAD => $recipe_dir));

  $mod->tpl->parse(TABLE, table);

  $quotarc_text = $mod->tpl->fetch();

  $datei3 = $recipe_dir."/.quota.rc";
  $mod->file->wf($datei3, $quotarc_text);

  exec("chown root:$root_gruppe $datei3 &> /dev/null");
  exec("chmod 644 $datei3");

  //.spamassassin.rc erstellen
  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "spamassassin.rc.master"));

  if(!isset($user["spam_strategy"])) $user["spam_strategy"] = "accept";
  if($user["spam_strategy"] == "accept"){
    $spam_comment = "#";
  } else {
    $spam_comment = "";
  }

  // Variablen zuweisen
  $mod->tpl->assign( array(PREFS_FILE => $recipe_dir."/.user_prefs",
                           SPAM_COMMENT => $spam_comment,
                           USERNAME => $user_username));

  $mod->tpl->parse(TABLE, table);

  $sarc_text = $mod->tpl->fetch();

  $datei5 = $recipe_dir."/.spamassassin.rc";
  $mod->file->wf($datei5, $sarc_text);

  exec("chown root:$root_gruppe $datei5 &> /dev/null");
  exec("chmod 644 $datei5");

  //.user_prefs erstellen
  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "user_prefs.master"));

  if(!isset($user["spam_hits"])) $user["spam_hits"] = "5";
  if(!isset($user["spam_rewrite_subject"])) $user["spam_rewrite_subject"] = "1";
  if(!isset($user["spam_subject_tag"])) $user["spam_subject_tag"] = "***SPAM*** ";

  // Variablen zuweisen
  $mod->tpl->assign( array(HITS => $user["spam_hits"],
                           REWRITE_SUBJECT => $user["spam_rewrite_subject"],
                           REWRITE_SUBJECT_2 => (($user["spam_rewrite_subject"] == 1)? '':'# '),
                           SUBJECT_TAG => $user["spam_subject_tag"]));

  $mod->tpl->parse(TABLE, table);

  $user_prefs_text = $mod->tpl->fetch();

  $datei6 = $recipe_dir."/.user_prefs";
  $mod->file->wf($datei6, $user_prefs_text);

  exec("chown root:$root_gruppe $datei6 &> /dev/null");
  exec("chmod 644 $datei6");

  exec("cp -f /root/ispconfig/isp/conf/bounce-exceed-quota.txt ".$recipe_dir."/.bounce-exceed-quota.txt");
  exec("chown root:".$root_gruppe." ".$recipe_dir."/.bounce-exceed-quota.txt &> /dev/null");
  exec("chmod 644 ".$recipe_dir."/.bounce-exceed-quota.txt");

  exec("cp -f /root/ispconfig/isp/conf/html-trap.rc.master ".$recipe_dir."/.html-trap.rc");
  exec("chown root:".$root_gruppe." ".$recipe_dir."/.html-trap.rc &> /dev/null");
  exec("chmod 644 ".$recipe_dir."/.html-trap.rc");

  exec("cp -f /root/ispconfig/isp/conf/local-rules.rc.master ".$recipe_dir."/.local-rules.rc");
  exec("chown root:".$root_gruppe." ".$recipe_dir."/.local-rules.rc &> /dev/null");
  exec("chmod 644 ".$recipe_dir."/.local-rules.rc");

  exec("cp -f /root/ispconfig/isp/conf/mailsize.rc.master ".$recipe_dir."/.mailsize.rc");
  exec("chown root:".$root_gruppe." ".$recipe_dir."/.mailsize.rc &> /dev/null");
  exec("chmod 644 ".$recipe_dir."/.mailsize.rc");

  exec("cp -f /root/ispconfig/isp/conf/antivirus.rc.master ".$recipe_dir."/.antivirus.rc");
  exec("chown root:".$root_gruppe." ".$recipe_dir."/.antivirus.rc &> /dev/null");
  exec("chmod 644 ".$recipe_dir."/.antivirus.rc");
}

function create_procmail_dir($web_path, $user_username, $is_admin = false) {
  $external = ($mod->system->server_conf["use_maildir"] == 2);

  if ($external) {
    return $mod->ext->create_user_config_dir($user_username);
  }

  return $is_admin ? $web_path : "$web_path/user/$user_username";
}

}
?>
