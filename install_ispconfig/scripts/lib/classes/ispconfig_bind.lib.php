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

class bind{

var $FILE = "/root/ispconfig/scripts/lib/classes/ispconfig_bind.lib.php";

function make_named($server_id) {
  global $mod, $isp_web;

  $bind_restart = 0;

  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->define( array(table    => "named.conf.master"));
  $mod->tpl->define_dynamic( "named_reverse", "table" );
  $mod->tpl->define_dynamic( "named", "table" );
  $mod->tpl->define_dynamic( "named_slave", "table" );

  if(!is_dir($mod->system->server_conf["server_bind_zonefile_dir"])){
    $mod->file->mkdirs($mod->system->server_conf["server_bind_zonefile_dir"]);
  }
  if(substr($mod->system->server_conf["server_bind_zonefile_dir"],-1) == "/"){
    $dist_bind_hintfile = $mod->system->server_conf["server_bind_zonefile_dir"].$mod->system->server_conf["dist_bind_hintfile"];
    $dist_bind_localfile = $mod->system->server_conf["server_bind_zonefile_dir"].$mod->system->server_conf["dist_bind_localfile"];
  } else {
    $dist_bind_hintfile = $mod->system->server_conf["server_bind_zonefile_dir"]."/".$mod->system->server_conf["dist_bind_hintfile"];
    $dist_bind_localfile = $mod->system->server_conf["server_bind_zonefile_dir"]."/".$mod->system->server_conf["dist_bind_localfile"];
  }
  if(!is_file($dist_bind_hintfile)) $mod->log->caselog("cp -f /root/ispconfig/isp/conf/db.root.master $dist_bind_hintfile", $this->FILE, __LINE__);
  if(!is_file($dist_bind_localfile)) $mod->log->caselog("cp -f /root/ispconfig/isp/conf/db.local.master $dist_bind_localfile", $this->FILE, __LINE__);

  $mod->tpl->assign( array('BINDDIR' => $mod->system->server_conf["server_bind_zonefile_dir"]));

  //$ips = $mod->system->data["isp_server_ip"];
  $ips = $mod->db->queryAllRecords("SELECT dns_isp_dns.doc_id, dns_isp_dns.server_id, dns_isp_dns.dns_soa, dns_isp_dns.dns_soa_ip AS server_ip FROM dns_nodes, dns_isp_dns WHERE dns_isp_dns.server_id = '$server_id' AND dns_nodes.doc_id = dns_isp_dns.doc_id AND dns_nodes.doctype_id = '".$isp_web->dns_doctype_id."' AND dns_nodes.status = '1'");

  foreach($ips as $ip){
    list($ip1,$ip2,$ip3,$ip4) = explode(".", $ip["server_ip"]);
    $ip_addresses[] = $ip3.".".$ip2.".".$ip1;
  }

  $ip_addresses = array_unique($ip_addresses);

  if(is_array($ip_addresses) && !empty($ip_addresses)){
    foreach($ip_addresses as $ip_address){
      $zone = $ip_address;

      // Variablen zuweisen
      $mod->tpl->assign( array('ZONE' => $zone));
      $mod->tpl->parse('NAMED_REVERSE',".named_reverse");
    }
  } else {
    $mod->tpl->clear_dynamic('named_reverse');
  }

  //$dnss = $mod->db->queryAllRecords("select * from dns_nodes,dns_isp_dns WHERE dns_isp_dns.server_id = '$server_id' and dns_nodes.doc_id = dns_isp_dns.doc_id AND dns_nodes.doctype_id = '".$isp_web->dns_doctype_id."' AND dns_nodes.status = '1'");
  $dnss = $ips;
  foreach($dnss as $dns){
    $domain = trim($dns["dns_soa"]);

    if($domain != ""){
      // Variablen zuweisen
      $mod->tpl->assign( array( 'DOMAIN' => $domain));
      $mod->tpl->parse('NAMED',".named");
    }
  }

  if(empty($dnss)) $mod->tpl->clear_dynamic('named');

  $slave_dnss = $mod->db->queryAllRecords("SELECT dns_secondary.* FROM dns_nodes,dns_secondary WHERE dns_nodes.doc_id = dns_secondary.doc_id AND dns_nodes.doctype_id = '".$isp_web->slave_doctype_id."' AND dns_nodes.status = '1'");

  if(!empty($slave_dnss)){
    foreach($slave_dnss as $slave_dns){
      $domain = trim($slave_dns["domain"]);
      $masters = trim($slave_dns["master_ip"]);

      if($domain != "" && $masters != ""){
        // Variablen zuweisen
        $mod->tpl->assign( array( 'DOMAIN' => $domain,
                                  'MASTERS' => $masters));
        $mod->tpl->parse('NAMED_SLAVE',".named_slave");
      }
    }
  } else {
    $mod->tpl->clear_dynamic('named_slave');
  }
  $mod->db->query("UPDATE dns_secondary SET status = ''");

  $mod->tpl->parse('TABLE', table);

  $named_text = $mod->tpl->fetch();
  $named_text .= $mod->file->manual_entries($mod->system->server_conf["server_bind_named_conf"], "//// MAKE MANUAL ENTRIES BELOW THIS LINE! ////");

  if(!is_file($mod->system->server_conf["server_bind_named_conf"])) $mod->log->phpcaselog(touch($mod->system->server_conf["server_bind_named_conf"]), "create ".$mod->system->server_conf["server_bind_named_conf"], $this->FILE, __LINE__);

  if(md5_file($mod->system->server_conf["server_bind_named_conf"]) != md5($named_text)){
    $mod->log->caselog("cp -fr ".$mod->system->server_conf["server_bind_named_conf"]." ".$mod->system->server_conf["server_bind_named_conf"]."~", $this->FILE, __LINE__);
    $mod->system->chown($mod->system->server_conf["server_bind_named_conf"]."~", $mod->system->server_conf["server_bind_user"], $mod->system->server_conf["server_bind_group"]);
    $mod->file->wf($mod->system->server_conf["server_bind_named_conf"], $named_text);
    $bind_restart = 1;
  }

  $server = $mod->system->server_conf;
  $datei = $mod->system->server_conf["server_bind_named_conf"];
  $server_bind_user = $server["server_bind_user"];
  $server_bind_group = $server["server_bind_group"];

  exec("chown $server_bind_user:$server_bind_group $datei &> /dev/null");
  return $bind_restart;
}


function make_zonefile($doc_id) {
  global $mod, $isp_web;

  if(!is_dir($mod->system->server_conf["server_bind_zonefile_dir"])){
    $mod->file->mkdirs($mod->system->server_conf["server_bind_zonefile_dir"]);
  }

  // Template Öffnen
  $mod->tpl->clear_all();
  $mod->tpl->no_strict();
  $mod->tpl->define( array(table    => "pri.domain.master"));
  $mod->tpl->define_dynamic( "arecords", "table" );
  $mod->tpl->define_dynamic( "cnamerecords", "table" );
  $mod->tpl->define_dynamic( "mxrecords", "table" );
  $mod->tpl->define_dynamic( "spfrecords", "table" );

  $dns = $mod->db->queryOneRecord("select * from dns_isp_dns WHERE doc_id = '$doc_id'");
  $server_id = $dns["server_id"];

  $bind_file = $mod->system->server_conf["server_bind_zonefile_dir"]."/pri.".$dns["dns_soa"];
  if(is_file($bind_file)){
    $serial = exec("grep -i serial $bind_file | cut -f1 -d';'");
    $serial = trim($serial);
    if(substr($serial,0,8) == date("Ymd")){
      $new_serial = date("Ymd").str_pad((substr($serial,8) + 1), 2, "0", STR_PAD_LEFT);
    } else {
      $new_serial = date("Ymd")."01";
    }
  } else {
   $new_serial = date("Ymd")."01";
  }

  // Variablen zuweisen
  $mod->tpl->assign( array('DNS_SOA' => $dns["dns_soa"],
                      'DNS_ADMINMAIL' => str_replace("@", ".", $dns["dns_adminmail"]),
                      'SERIAL' => $new_serial,
                      'DNS_REFRESH' => $dns["dns_refresh"],
                      'DNS_RETRY' => $dns["dns_retry"],
                      'DNS_EXPIRE' => $dns["dns_expire"],
                      'DNS_TTL' => $dns["dns_ttl"],
                      'DNS_NS1' => $dns["dns_ns1"],
                      'DNS_NS2' => $dns["dns_ns2"],
                      'DNS_SOA_IP' => $dns["dns_soa_ip"]));

  $arecords = $mod->db->queryAllRecords("select dns_a.host, dns_a.ip_adresse from dns_dep, dns_a, dns_nodes WHERE dns_dep.parent_doc_id = '$doc_id' AND dns_dep.parent_doctype_id = '".$isp_web->dns_doctype_id."' AND dns_dep.child_doctype_id = '".$isp_web->a_record_doctype_id."' AND dns_a.doc_id = dns_dep.child_doc_id and dns_nodes.type = 'a' and dns_nodes.doctype_id = '".$isp_web->a_record_doctype_id."' and dns_nodes.status = '1' and dns_nodes.doc_id = dns_a.doc_id");

  foreach($arecords as $arecord){

    // Variablen zuweisen
    $mod->tpl->assign( array( 'A_HOST' => $arecord["host"],
                         'A_IP' => $arecord["ip_adresse"]));
    $mod->tpl->parse('ARECORDS',".arecords");
    $has_arecords = 1;
  }

  if($has_arecords != 1) $mod->tpl->clear_dynamic('arecords');

  $cnamerecords = $mod->db->queryAllRecords("select dns_cname.host, dns_cname.ziel from dns_dep, dns_cname, dns_nodes WHERE dns_dep.parent_doc_id = '$doc_id' AND dns_dep.parent_doctype_id = '".$isp_web->dns_doctype_id."' AND dns_dep.child_doctype_id = '".$isp_web->cname_record_doctype_id."' AND dns_cname.doc_id = dns_dep.child_doc_id and dns_nodes.type = 'a' and dns_nodes.doctype_id = '".$isp_web->cname_record_doctype_id."' and dns_nodes.status = '1' and dns_nodes.doc_id = dns_cname.doc_id");

  foreach($cnamerecords as $cnamerecord){

    // Variablen zuweisen
    $mod->tpl->assign( array( 'CNAME_HOST' => $cnamerecord["host"],
                         'CNAME_ZIEL' => $cnamerecord["ziel"]));
    $mod->tpl->parse('CNAMERECORDS',".cnamerecords");
    $has_cnamerecords =1;
  }

  if($has_cnamerecords != 1) $mod->tpl->clear_dynamic('cnamerecords');

  $mxrecords = $mod->db->queryAllRecords("select dns_mx.host, dns_mx.prioritaet, dns_mx.mailserver from dns_dep, dns_mx, dns_nodes WHERE dns_dep.parent_doc_id = '$doc_id' AND dns_dep.parent_doctype_id = '".$isp_web->dns_doctype_id."' AND dns_dep.child_doctype_id = '".$isp_web->mx_record_doctype_id."' AND dns_mx.doc_id = dns_dep.child_doc_id and dns_nodes.type = 'a' and dns_nodes.doctype_id = '".$isp_web->mx_record_doctype_id."' and dns_nodes.status = '1' and dns_nodes.doc_id = dns_mx.doc_id");

  foreach($mxrecords as $mxrecord){

    // Variablen zuweisen
    $mod->tpl->assign( array( 'MX_HOST' => $mxrecord["host"],
                         'MX_PRIORITAET' => $mxrecord["prioritaet"],
                         'MX_MAILSERVER' => $mxrecord["mailserver"]));
    $mod->tpl->parse('MXRECORDS',".mxrecords");
    $has_mxrecords = 1;
  }

  if($has_mxrecords != 1) $mod->tpl->clear_dynamic('mxrecords');

  ////// SPF //////
  $spfrecords = $mod->db->queryAllRecords("SELECT dns_spf.host, dns_spf.a, dns_spf.mx, dns_spf.ptr, dns_spf.a_break, dns_spf.mx_break, dns_spf.ip4_break, dns_spf.include_break, dns_spf.all_ FROM dns_dep, dns_spf, dns_nodes WHERE dns_dep.parent_doc_id = '$doc_id' AND dns_dep.parent_doctype_id = '".$isp_web->dns_doctype_id."' AND dns_dep.child_doctype_id = '".$isp_web->spf_record_doctype_id."' AND dns_spf.doc_id = dns_dep.child_doc_id AND dns_nodes.type = 'a' AND dns_nodes.doctype_id = '".$isp_web->spf_record_doctype_id."' AND dns_nodes.status = '1' AND dns_nodes.doc_id = dns_spf.doc_id");

  foreach($spfrecords as $spfrecord){
    $spf = '';
    if($mod->file->unix_nl(trim($spfrecord['ip4_break'])) != ''){
      $ip4_breaks = explode("\n", $mod->file->unix_nl(trim($spfrecord['ip4_break'])));
      if(!empty($ip4_breaks)){
        foreach($ip4_breaks as $ip4_break){
          $spf .= 'ip4:'.$ip4_break.' ';
        }
      }
    }
    if($spfrecord['a'] == 1) $spf .= 'a ';
    if($spfrecord['mx'] == 1) $spf .= 'mx ';
    if($spfrecord['ptr'] == 1) $spf .= 'ptr ';
    if($mod->file->unix_nl(trim($spfrecord['a_break'])) != ''){
      $a_breaks = explode("\n", $mod->file->unix_nl(trim($spfrecord['a_break'])));
      if(!empty($a_breaks)){
        foreach($a_breaks as $a_break){
          $spf .= 'a:'.$a_break.' ';
        }
      }
    }
    if($mod->file->unix_nl(trim($spfrecord['mx_break'])) != ''){
      $mx_breaks = explode("\n", $mod->file->unix_nl(trim($spfrecord['mx_break'])));
      if(!empty($mx_breaks)){
        foreach($mx_breaks as $mx_break){
          $spf .= 'mx:'.$mx_break.' ';
        }
      }
    }
    if(trim($spfrecord['include_break']) != '') $spf .= 'include:'.trim($spfrecord['include_break']).' ';
    if($spfrecord['all_'] == 1){
      $spf .= '~all';
    } else {
      $spf .= '?all';
    }

    // Variablen zuweisen
    $mod->tpl->assign( array( 'SPF_HOST' => $spfrecord["host"].($spfrecord["host"] == '' ? '' : '.').$dns["dns_soa"],
                         'SPF' => trim($spf)));
    $mod->tpl->parse('SPFRECORDS',".spfrecords");
    $has_spfrecords = 1;
  }

  if($has_spfrecords != 1) $mod->tpl->clear_dynamic('spfrecords');
  ////// SPF ENDE //////

  $mod->tpl->parse('TABLE', table);

  $zonefile_text = $mod->tpl->fetch();
  $zonefile_text .= $mod->file->manual_entries($bind_file, ";;;; MAKE MANUAL ENTRIES BELOW THIS LINE! ;;;;");

  if(!is_file($bind_file)) $mod->log->phpcaselog(touch($bind_file), "create ".$bind_file, $this->FILE, __LINE__);

  $zonefile_text_old = $mod->file->rf($bind_file);
  clearstatcache();

  $zonefile_text_old_no_serial = shell_exec("echo '$zonefile_text_old' | grep -v serial");
  $zonefile_text_no_serial = shell_exec("echo '$zonefile_text' | grep -v serial");

  if(md5($zonefile_text_old_no_serial) != md5($zonefile_text_no_serial)){
    if($zonefile_text_old != ""){
      $mod->log->caselog("cp -fr $bind_file $bind_file~", $this->FILE, __LINE__);
      $mod->system->chown($bind_file."~", $mod->system->server_conf["server_bind_user"], $mod->system->server_conf["server_bind_group"]);
    }
    $mod->file->wf($bind_file, $zonefile_text);
    $bind_restart = 1;
  } else {
    $bind_restart = 0;
  }

  $server = $mod->system->server_conf;

  $server_bind_user = $server["server_bind_user"];
  $server_bind_group = $server["server_bind_group"];
  exec("chown $server_bind_user:$server_bind_group $bind_file &> /dev/null");
  return $bind_restart;
}

function make_reverse_zonefile($server_id) {
  global $mod, $isp_web;

  $bind_restart = 0;

  if(!is_dir($mod->system->server_conf["server_bind_zonefile_dir"])){
    $mod->file->mkdirs($mod->system->server_conf["server_bind_zonefile_dir"]);
  }

  $server = $mod->system->server_conf;

  $ips = $mod->db->queryAllRecords("SELECT dns_isp_dns.doc_id, dns_isp_dns.server_id, dns_isp_dns.dns_soa_ip AS server_ip FROM dns_nodes, dns_isp_dns WHERE dns_isp_dns.server_id = '$server_id' AND dns_nodes.doc_id = dns_isp_dns.doc_id AND dns_nodes.doctype_id = '".$isp_web->dns_doctype_id."' AND dns_nodes.status = '1'");

  foreach($ips as $ip){
    if(trim($ip["server_ip"]) != ""){
      list($ip1,$ip2,$ip3,$ip4) = explode(".", trim($ip["server_ip"]));
      $ip_addresses[] = $ip1.".".$ip2.".".$ip3;
    }
  }

  $ip_addresses = array_unique($ip_addresses);

  foreach($ip_addresses as $ip_address){

    // Template Öffnen
    $mod->tpl->clear_all();
    $mod->tpl->define( array(table    => "reverse_zone.in-addr.arpa.master"));
    $mod->tpl->define_dynamic( "reverse_records", "table" );

    list($ip1,$ip2,$ip3) = explode(".", $ip_address);
    $zone = $ip3.".".$ip2.".".$ip1;

    $datei = $mod->system->server_conf["server_bind_zonefile_dir"]."/pri.".$zone.".in-addr.arpa";
    if(is_file($datei)){
      $serial = exec("grep -i serial $datei | cut -f1 -d';'");
      $serial = trim($serial);
      if(substr($serial,0,8) == date("Ymd")){
        $new_serial = date("Ymd").str_pad((substr($serial,8) + 1), 2, "0", STR_PAD_LEFT);
      } else {
        $new_serial = date("Ymd")."01";
      }
    } else {
      $new_serial = date("Ymd")."01";
    }

    // Variablen zuweisen
    $mod->tpl->assign( array('SERVER_DOMAIN' => $server["server_domain"],
                  'SERIAL' => $new_serial,
                  'SERVER_BIND_NS1_DEFAULT' => $server["server_bind_ns1_default"],
                  'SERVER_BIND_NS2_DEFAULT' => $server["server_bind_ns2_default"]));

    $dnss = $mod->db->queryAllRecords("select * from dns_nodes,dns_isp_dns WHERE dns_isp_dns.server_id = '$server_id' and dns_nodes.doc_id = dns_isp_dns.doc_id AND dns_nodes.doctype_id = '".$isp_web->dns_doctype_id."' AND dns_nodes.status = '1' AND dns_isp_dns.dns_soa_ip LIKE '".$ip_address."%'");

    foreach($dnss as $dns){

      $domain = $dns["dns_soa"];
      list($ip1,$ip2,$ip3,$ip4) = explode(".", $dns["dns_soa_ip"]);
      $ip_ende = $ip4;
      // Variablen zuweisen
      $mod->tpl->assign( array( 'DNS_SOA' => $domain,
                           'IP_ENDE' => $ip_ende));
      $mod->tpl->parse('REVERSE_RECORDS',".reverse_records");

      /////////////////
      $tree_id = $dns["tree_id"];
      // A Records
      $a_records = $mod->db->queryAllRecords("SELECT * FROM dns_dep, dns_a WHERE dns_dep.parent_tree_id = '$tree_id' AND dns_dep.child_doc_id = dns_a.doc_id AND dns_dep.child_doctype_id = '".$isp_web->a_record_doctype_id."' AND dns_a.ip_adresse LIKE '".$ip_address."%'");
      if(!empty($a_records)){
        foreach($a_records as $a_record){
          if(!empty($a_record["host"])){
            $domain_with_host = $a_record["host"].".".$domain;
            list($ip1,$ip2,$ip3,$ip4) = explode(".", $a_record["ip_adresse"]);
            $ip_ende = $ip4;
            // Variablen zuweisen
            $mod->tpl->assign( array( 'DNS_SOA' => $domain_with_host,
                                      'IP_ENDE' => $ip_ende));
            $mod->tpl->parse('REVERSE_RECORDS',".reverse_records");
          }
        }
      }
      /////////////////
    }
    if(empty($dnss)) $mod->tpl->clear_dynamic('reverse_records');
    $mod->tpl->parse('TABLE', table);

    $named_text = $mod->tpl->fetch();
    $named_text .= $mod->file->manual_entries($datei, ";;;; MAKE MANUAL ENTRIES BELOW THIS LINE! ;;;;");

    if(!is_file($datei)) $mod->log->phpcaselog(touch($datei), "create ".$datei, $this->FILE, __LINE__);

    $named_text_old = $mod->file->rf($datei);
    clearstatcache();

    $named_text_old_no_serial = shell_exec("echo '$named_text_old' | grep -v serial");
    $named_text_no_serial = shell_exec("echo '$named_text' | grep -v serial");

    if(md5($named_text_old_no_serial) != md5($named_text_no_serial)){
      if($named_text_old != ""){
        $mod->log->caselog("cp -fr $datei $datei~", $this->FILE, __LINE__);
        $mod->system->chown($datei."~", $mod->system->server_conf["server_bind_user"], $mod->system->server_conf["server_bind_group"]);
      }
      $mod->file->wf($datei, $named_text);
      $bind_restart += 1;
    }

    $mod->system->chown($datei, $mod->system->server_conf["server_bind_user"], $mod->system->server_conf["server_bind_group"]);

    unset($named_text);
  }
  return $bind_restart;
}

function del_file(){
  global $mod;
  $named_conf = $mod->system->server_conf["server_bind_named_conf"];
  $named_conf_content = $mod->file->no_comments($named_conf, '//');
  $dir = $mod->system->server_conf["server_bind_zonefile_dir"];
  if(substr($dir,-1) != "/") $dir .= "/";
  $handle=opendir($dir);
  while($file = readdir($handle)){
    if($file != "." && $file != ".."){
      if(substr($file,-1) == '~'){
        if(!$mod->system->grep($named_conf_content, substr($file,0,strlen($file)-1), 'w') && (substr($file,0,4) == "pri." || substr($file,0,4) == "sec.")) $files[] = $dir.$file;
      } else {
        if(!$mod->system->grep($named_conf_content, $file, 'w') && (substr($file,0,4) == "pri." || substr($file,0,4) == "sec.")) $files[] = $dir.$file;
      }
    }
  }
  closedir($handle);
  if(is_array($files)){
    foreach($files as $file){
      if(is_file($file)) unlink($file);
    }
  }
  $files = NULL;
}

function named_restart(){
  global $mod;
  $dist_init_scripts = $mod->system->server_conf["dist_init_scripts"];
  $dist_bind_init_script = $mod->system->server_conf["dist_bind_init_script"];
  $mod->system->daemon_init($dist_bind_init_script, "restart");
}

}
?>