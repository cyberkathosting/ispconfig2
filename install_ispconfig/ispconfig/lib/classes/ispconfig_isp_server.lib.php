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

if(CONFIG_LOADED != 1) die('Direct access not permitted.');


class isp_server
{

var $path_httpd_conf;
var $path_httpd_root;
var $directory_mode = "0770";
var $web_doctype_id = 1013;
var $user_doctype_id = 1014;
var $domain_doctype_id = 1015;
var $vhost_conf;
var $sendmail_cw;
var $virtusertable;
var $user_von;
var $group_von;


//Constructor
function isp_server() {
global $go_api, $go_info;

$server_conf = $go_api->db->queryOneRecord("SELECT * from isp_server");
$this->path_httpd_conf = stripslashes($server_conf["server_path_httpd_conf"]);
$this->path_httpd_root = stripslashes($server_conf["server_path_httpd_root"]);
$this->vhost_conf = $this->path_httpd_conf . $go_info["server"]["dir_trenner"].'vhost'.$go_info["server"]["dir_trenner"].'vhost.conf';
$this->user_von = $server_conf["userid_von"];
$this->group_von = $server_conf["groupid_von"];
$this->virtusertable = $server_conf["server_sendmail_virtuser_datei"];
$this->sendmail_cw = $server_conf["server_sendmail_cw"];
}


function server_insert($doc_id, $doctype_id) {
        global $go_api, $go_info;
        $server = $go_api->db->queryOneRecord("SELECT * from isp_server where doc_id = '$doc_id'");

        if(substr($server["server_path_httpd_conf"],-1) == "/") $server["server_path_httpd_conf"] = substr($server["server_path_httpd_conf"],0,-1);
        if(substr($server["server_path_httpd_root"],-1) == "/") $server["server_path_httpd_root"] = substr($server["server_path_httpd_root"],0,-1);
        if(substr($server["server_path_httpd_error"],-1) == "/") $server["server_path_httpd_error"] = substr($server["server_path_httpd_error"],0,-1);
        if(substr($server["server_bind_zonefile_dir"],-1) == "/") $server["server_bind_zonefile_dir"] = substr($server["server_bind_zonefile_dir"],0,-1);
        if(substr($server["dist_init_scripts"],-1) == "/") $server["dist_init_scripts"] = substr($server["dist_init_scripts"],0,-1);
        if(substr($server["dist_runlevel"],-1) == "/") $server["dist_runlevel"] = substr($server["dist_runlevel"],0,-1);
        if(substr($server["dist_smrsh"],-1) == "/") $server["dist_smrsh"] = substr($server["dist_smrsh"],0,-1);

        if(!is_link($server["server_path_httpd_conf"])) $server["server_path_httpd_conf"] = realpath($server["server_path_httpd_conf"]);
        if(!is_link($server["server_path_httpd_root"])) $server["server_path_httpd_root"] = realpath($server["server_path_httpd_root"]);
        if(!is_link($server["server_path_httpd_error"])) $server["server_path_httpd_error"] = realpath($server["server_path_httpd_error"]);
        if(!is_link($server["server_bind_zonefile_dir"])) $server["server_bind_zonefile_dir"] = realpath($server["server_bind_zonefile_dir"]);
        if(!is_link($server["dist_init_scripts"])) $server["dist_init_scripts"] = realpath($server["dist_init_scripts"]);
        if(!is_link($server["dist_runlevel"])) $server["dist_runlevel"] = realpath($server["dist_runlevel"]);
        if(!is_link($server["dist_smrsh"])) $server["dist_smrsh"] = realpath($server["dist_smrsh"]);

        $go_api->db->query("UPDATE isp_server SET server_path_httpd_conf = '".$server["server_path_httpd_conf"]."', server_path_httpd_root = '".realpath($server["server_path_httpd_root"])."', server_path_httpd_error = '".$server["server_path_httpd_error"]."', server_bind_zonefile_dir = '".$server["server_bind_zonefile_dir"]."', dist_init_scripts = '".$server["dist_init_scripts"]."', dist_runlevel = '".$server["dist_runlevel"]."', dist_smrsh = '".$server["dist_smrsh"]."' WHERE doc_id = '".$doc_id."'");

        $ipliste = explode("\n",$server["server_ipliste"]);

        // löschen der alten ip_liste
        $go_api->db->query("DELETE from isp_server_ip where server_id = '$doc_id'");
        // einfügen der neuen IPs
        $ip = $server["server_ip"];
        $ip = trim($ip);
        if(!empty($ip)) $go_api->db->query("INSERT INTO isp_server_ip (server_id, server_ip) values ('$doc_id', '$ip')");

        foreach( $ipliste as $ip) {
            $ip = trim($ip);
            if(!empty($ip)) $go_api->db->query("INSERT INTO isp_server_ip (server_id, server_ip) values ('$doc_id', '$ip')");
        }

        // adminmail.txt anlegen
        if($go_info["server"]["mode"] != 'demo') {
            $pfad = SERVER_ROOT."/adminmail.txt";
            $fp = fopen ($pfad, "w");
            fwrite($fp,$server["server_admin_email"]);
            fclose($fp);
        }

        // Server benachrichtigen
        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'insert');


}



function server_update($doc_id, $doctype_id) {
global $go_api, $go_info;

        $server = $go_api->db->queryOneRecord("SELECT * from isp_server where doc_id = '$doc_id'");

        if(substr($server["server_path_httpd_conf"],-1) == "/") $server["server_path_httpd_conf"] = substr($server["server_path_httpd_conf"],0,-1);
        if(substr($server["server_path_httpd_root"],-1) == "/") $server["server_path_httpd_root"] = substr($server["server_path_httpd_root"],0,-1);
        if(substr($server["server_path_httpd_error"],-1) == "/") $server["server_path_httpd_error"] = substr($server["server_path_httpd_error"],0,-1);
        if(substr($server["server_bind_zonefile_dir"],-1) == "/") $server["server_bind_zonefile_dir"] = substr($server["server_bind_zonefile_dir"],0,-1);
        if(substr($server["dist_init_scripts"],-1) == "/") $server["dist_init_scripts"] = substr($server["dist_init_scripts"],0,-1);
        if(substr($server["dist_runlevel"],-1) == "/") $server["dist_runlevel"] = substr($server["dist_runlevel"],0,-1);
        if(substr($server["dist_smrsh"],-1) == "/") $server["dist_smrsh"] = substr($server["dist_smrsh"],0,-1);

        if(!@is_link($server["server_path_httpd_conf"])) $server["server_path_httpd_conf"] = realpath($server["server_path_httpd_conf"]);
        if(!@is_link($server["server_path_httpd_root"])) $server["server_path_httpd_root"] = realpath($server["server_path_httpd_root"]);
        if(!@is_link($server["server_path_httpd_error"])) $server["server_path_httpd_error"] = realpath($server["server_path_httpd_error"]);
        if(!@is_link($server["server_bind_zonefile_dir"])) $server["server_bind_zonefile_dir"] = realpath($server["server_bind_zonefile_dir"]);
        if(!@is_link($server["dist_init_scripts"])) $server["dist_init_scripts"] = realpath($server["dist_init_scripts"]);
        if(!@is_link($server["dist_runlevel"])) $server["dist_runlevel"] = realpath($server["dist_runlevel"]);
        if(!@is_link($server["dist_smrsh"])) $server["dist_smrsh"] = realpath($server["dist_smrsh"]);

        $go_api->db->query("UPDATE isp_server SET server_path_httpd_conf = '".$server["server_path_httpd_conf"]."', server_path_httpd_root = '".realpath($server["server_path_httpd_root"])."', server_path_httpd_error = '".$server["server_path_httpd_error"]."', server_bind_zonefile_dir = '".$server["server_bind_zonefile_dir"]."', dist_init_scripts = '".$server["dist_init_scripts"]."', dist_runlevel = '".$server["dist_runlevel"]."', dist_smrsh = '".$server["dist_smrsh"]."' WHERE doc_id = '".$doc_id."'");

        $ipliste = explode("\r\n",$server["server_ipliste"]);

        // löschen der alten ip_liste
        $go_api->db->query("DELETE from isp_server_ip where server_id = '$doc_id'");
        // einfügen der neuen IPs
        $ip = $server["server_ip"];
        $ip = trim($ip);
        if(!empty($ip)) $go_api->db->query("INSERT INTO isp_server_ip (server_id, server_ip) values ('$doc_id', '$ip')");

        foreach( $ipliste as $ip) {
            $ip = trim($ip);
            if(!empty($ip)) $go_api->db->query("INSERT INTO isp_server_ip (server_id, server_ip) values ('$doc_id', '$ip')");
        }


        // adminmail.txt anlegen
        if($go_info["server"]["mode"] != 'demo') {
            $pfad = SERVER_ROOT."/adminmail.txt";
            $fp = fopen ($pfad, "w");
            fwrite($fp,$server["server_admin_email"]);
            fclose($fp);
        }

        // Checken, ob maildir Support geändert wurde
        if($old_form_data["use_maildir"] != $server["use_maildir"]) {
                $all_users = $go_api->db->queryAllRecords("SELECT isp_isp_user.doc_id FROM isp_isp_user, isp_nodes WHERE isp_nodes.status = 1 and isp_isp_user.doc_id = isp_nodes.doc_id and isp_nodes.doctype_id = 1014");
                foreach($all_users as $user) {
                        $go_api->db->query("UPDATE isp_isp_user SET status = 'u' WHERE doc_id = ".$user["doc_id"]." and status != 'n' and status != 'd'");
                }
        }



        $go_api->uses("isp");
        $server_id = 1;
        $go_api->isp->signal_server($server_id,'update');

}

}
?>