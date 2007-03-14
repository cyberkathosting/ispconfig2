<?php

/*
Copyright (c) 2007, projektfarm Gmbh, Till Brehm, Falko Timme
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
include("../../lib/app.inc.php");

// Lade Soap Klasse
$go_api->uses_obj("soap");


$s = new soap_server;


$s->register('service');
$s->register('login');
$s->register('logout');


function login($user, $pass) {

        global $go_api, $go_info;

        // alte Sessions löschen
        $go_api->db->query("DELETE FROM remote_session WHERE tstamp < '".(time() - 1800)."'");

        if(empty($user) or empty($pass)) {
                return new soap_fault('Client','','username or password empty.');
        } else {

                $user = addslashes($user);
                $pass = addslashes($pass);

                $user = $go_api->db->queryOneRecord("SELECT * FROM remote_user WHERE username = '$user' and passwort = md5('$pass')");

                // Checke IP
                if($user["ip"] != '') {
                        if($_SERVER['REMOTE_ADDR'] != $user["ip"]) return new soap_fault('Client','','IP-Address not allowed.');
                }

                if($user["ruserid"] > 0) {

                        $session["user"] = $user;
                        $session_data = addslashes(serialize($session));
                        $session_id = md5 (uniqid (rand()));
                        $go_api->db->query("INSERT INTO remote_session (sid,ruserid,data,tstamp) VALUES ('$session_id','".$user["ruserid"]."','$session_data','".time()."')");
                        return $session_id;

                } else {
                        return new soap_fault('Client','','username or password incorrect.');
                }
        }
}

function logout($sid) {
        global $go_api, $go_info;
        if(empty($sid)) {
                return new soap_fault('Client','','sid empty.');
        } else {
                $sid = addslashes($sid);
                $sql = "DELETE FROM remote_session WHERE sid = '$sid'";
                $go_api->db->query($sql);
                return true;
        }
}


function service($sid, $module, $function, $params) {
        global $go_api, $go_info;

        // prüfe ob session aktiv
        $session = addslashes($session);

        // lösche abgelaufene session records ( älter als 30 minuten)
        $go_api->db->query("DELETE FROM remote_session WHERE tstamp < ".time() + 1800);

        // hole Session
        $session_record = $go_api->db->queryOneRecord("SELECT * FROM remote_session WHERE sid = '$sid'");
        if(empty($session_record["data"])) {
                return new soap_fault('Server','','session not available.');
        } else {
                $session = unserialize(stripslashes($session_record["data"]));
                $ruserid = $session_record["ruserid"];
                unset($session_record);
        }

        // allowed Modules
        $allowed_modules[] = 'dns';
        $allowed_modules[] = 'slave';
        $allowed_modules[] = 'reseller';
        $allowed_modules[] = 'kunde';
        $allowed_modules[] = 'web';

        // überprüfen ob modul und funktion übergeben wurden


        // Checke IP
        if($session["ip"] != '') {
                if($_SERVER['REMOTE_ADDR'] != $session["ip"]) return new soap_fault('Client','','IP-Address not allowed.');
        }


        if(in_array($module,$allowed_modules)) {
                $go_api->uses($module);
                if(class_exists($module)) {
                        if(method_exists($go_api->$module,$function)) {
                                $retval = $go_api->$module->$function($session,$params);
                                if($go_api->$module->errorMessage == '') {
                                        return $retval;
                                } else {
                                        return new soap_fault('Client','',$go_api->$module->errorMessage);
                                }
                        } else {
                                return new soap_fault('Client','','function does not exist.');
                        }
                } else {
                        return new soap_fault('Client','','moduleclass not available.');
                }
        } else {
                return new soap_fault('Client','','module not allowed.');
        }

}





$s->service($HTTP_RAW_POST_DATA);

?>