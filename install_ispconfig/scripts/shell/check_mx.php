<?PHP

/*

Copyright (c) 2006, Oliver Blaha
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

set_time_limit(0);

include("/root/ispconfig/scripts/lib/config.inc.php");
include("/root/ispconfig/scripts/lib/server.inc.php");

$web_list = $mod->db->queryAllRecords("SELECT * from isp_isp_web");
foreach ($web_list as $web) {
    if (empty($web["optionen_local_mailserver"])) {
        continue;
    }

    $doc_id = $web["doc_id"];

    $host = "";
    if (!empty($web["web_host"])) {
        $host = $web["web_host"] . ".";
    }
    getmxrr($host . $web["web_domain"], $mx_list);

    $mx_found = false;
    foreach ($mx_list as $mx) {
        if (ip2long(gethostbyname($mx)) == ip2long($web["web_ip"])) {
            $mx_found = true;
        }
    }

    if (!$mx_found) {
        $mod->db->query("UPDATE isp_isp_web SET optionen_local_mailserver = '' WHERE doc_id = '$doc_id'");
    }
}

$domain_list = $mod->db->queryAllRecords("SELECT * from isp_isp_domain");
foreach ($domain_list as $domain) {
    if (empty($domain["domain_local_mailserver"])) {
        continue;
    }

    $doc_id = $domain["doc_id"];

    $host = "";
    if (!empty($domain["domain_host"])) {
        $host = $domain["domain_host"] . ".";
    }
    getmxrr($host . $domain["domain_domain"], $mx_list);

    $mx_found = false;
    foreach ($mx_list as $mx) {
        if (ip2long(gethostbyname($mx)) == ip2long($web["web_ip"])) {
            $mx_found = true;
        }
    }

    if (!$mx_found) {
        $mod->db->query("UPDATE isp_isp_domain SET domain_local_mailserver = '' WHERE doc_id = '$doc_id'");
    }
}


?>
