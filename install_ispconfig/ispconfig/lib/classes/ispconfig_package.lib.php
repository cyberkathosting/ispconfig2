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
class package
{
var $inst;
var $allowed_programs;

function package() {

        // allowed Programs
        $prog[] = "tar";
        $prog[] = "chown";
        $prog[] = "chmod";

        $this->allowed_programs = $prog;
}

function parse($inst_path,$inst_file) {
global $go_api, $go_info;

    $installer = $inst_path.$go_info["server"]["dir_trenner"].$inst_file;

    if(!@is_file($installer)) $go_api->errorMessage("Installations Anweisungen nicht gefunden: $installer");

    // Datei auslesen
    $fp = fopen ($installer, "r");
    $install = fread($fp, filesize ($installer));
    fclose($fp);

    $install = str_replace("\r\n", "\n", $install);

    // Leerzeilen entfernen
    while(strstr($install, "\n\n")){
        $install = str_replace("\n\n", "\n", $install);
    }

    // Platzhalter ersetzen
    //$p_src = str_replace("\\","/",$inst_path);
    //$p_app = str_replace("\\","/",$inst_path);
    $install = str_replace("{src}", $inst_path, $install);
    $install = str_replace("{app}", $go_info["server"]["server_root"], $install);
    $install = str_replace("{ispconfig_db}", $go_info["server"]["db_name"], $install);


    list($info, $setup, $dirs, $files, $sql, $run, $uninstallrun, $installdelete) = explode("[", $install);
    unset($info);
    $setup = str_replace("Setup]\n", "", $setup);
    $dirs = str_replace("Dirs]\n", "", $dirs);
    $files = str_replace("Files]\n", "", $files);
    $sql = str_replace("SQL]\n", "", $sql);
    $run = str_replace("Run]\n", "", $run);
    $uninstallrun = str_replace("UninstallRun]\n", "", $uninstallrun);
    $installdelete = str_replace("InstallDelete]\n", "", $installdelete);


    if(substr($setup,-1) == "\n") $setup = substr($setup,0,-1);
    if(substr($dirs,-1) == "\n") $dirs = substr($dirs,0,-1);
    if(substr($files,-1) == "\n") $files = substr($files,0,-1);
    if(substr($sql,-1) == "\n") $sql = substr($sql,0,-1);
    if(substr($run,-1) == "\n") $run = substr($run,0,-1);
    if(substr($uninstallrun,-1) == "\n") $uninstallrun = substr($uninstallrun,0,-1);
    if(substr($installdelete,-1) == "\n") $installdelete = substr($installdelete,0,-1);

    /************************************
    * parse SETUP section
    *************************************/

    $setup_elements = explode("\n", $setup);
    foreach($setup_elements as $setup_element){
        if(!strstr(substr($setup_element,0,5), "#")){
        list($key, $val) = explode("=", $setup_element);
        $this->inst["setup"][$key] = $val;
        //${$key} = $$key;
        }
    }

    /************************************
    * parse DIR section
    *************************************/

    $dir_elements = explode("\n", $dirs);
    foreach($dir_elements as $dir_element){
        if(!strstr(substr($dir_element,0,5), "#")){
        list($key, $val) = explode(": ", $dir_element);
        $key = trim($key);
        $val = trim($val);
        $val = str_replace("\"", "", $val);
        if(!empty($key)) {
        $this->inst["dirs"][] = array('action' => $key,
                                       'dir' => $val);
        }
        }
    }

    /************************************
    * parse FILES section
    *************************************/

    $file_elements = explode("\n", $files);
    foreach($file_elements as $file_element){
        if(!strstr(substr($file_element,0,5), "#")){
        $cons = explode(";", $file_element);
        foreach($cons as $con){
            list($key, $val) = explode(": ", $con);
            $key = trim($key);
            $val = trim($val);
            $val = str_replace("\"", "", $val);

            ${$key} = $val;
            //echo $key."\n";
            //echo ${$key}."\n";
                switch ($key) {
                case 'CPFILE':
                    $command = "CPFILE";
                    $cpfile = $val;
                break;
                case 'DestDir':
                    $destdir = $val;
                break;
                case 'DestName':
                    $destname = $val;
                break;
                case 'Chmod':
                    $command2 = "chmod";
                    $chmod = $val;
                break;
                }
        }
        //if(!empty($command)) echo $command." ".$cpfile." ".$destdir."/".$destname."\n";
        //if(!empty($chmod)) echo $command2." ".$chmod." ".$destdir."/".$destname."\n";

        $this->inst["files"][] = array('action' => $command,
                                       'source' => $cpfile,
                                       'destdir' => $destdir,
                                       'destname' => $destname,
                                       'chmod' => $chmod);

        unset($command);
        unset($cpfile);
        unset($destdir);
        unset($destname);
        unset($command2);
        unset($chmod);
        }
    }

    /************************************
    * parse SQL section
    *************************************/

    $sql_elements = explode("\n", $sql);
    foreach($sql_elements as $sql_element){
        if(!strstr(substr($sql_element,0,5), "#")){
        $cons = explode(";", $sql_element);
        foreach($cons as $con){
            list($key, $val) = explode(": ", $con);
            $key = trim($key);
            $val = trim($val);
            $val = str_replace("\"", "", $val);

            ${$key} = $val;
            //echo $key."\n";
            //echo ${$key}."\n";
            switch ($key) {
                case 'SQLEXEC':
                    $sql_query = $val;
                    $action = "SQLEXEC";
                break;
                case 'SQLFILEEXEC':

                    if($go_info["server"]["db_password"] != ""){
                        $command = "mysql -u ".$go_info["server"]["db_user"]." -p".$go_info["server"]["db_password"] ."";
                    } else {
                        $command = "mysql -u ".$go_info["server"]["db_user"]."";
                    }
                    $sql_file = $val;
                    $action = "SQLFILEEXEC";
                break;
                case 'DATABASE':
                    $database = $val;
                break;
            }
        }

        if(!empty($sql_query) or !empty($sql_file)) {
            $this->inst["sql"][] = array(   'action' => $action,
                                            'query' => $sql_query,
                                            'command' => $command,
                                            'sql_file' => $sql_file,
                                            'db' => $database);
            }

        unset($action);
        unset($command);
        unset($sql_query);
        unset($database);
        unset($sql_file);
        }
    }

        /************************************
    * parse RUN section
    *************************************/

    $run_elements = explode("\n", $run);
    foreach($run_elements as $run_element){
        if(!strstr(substr($run_element,0,5), "#")){

                        $cons = explode(";", $run_element);
                       foreach($cons as $con){

                list($key, $val) = explode(": ", $con);
                $key = trim($key);
                $val = trim($val);
                $val = str_replace("\"", "", $val);

                        //${$key} = $val;
            //echo $key."\n";
            //echo ${$key}."\n";
                switch ($key) {
                case 'RUNBEFOREINSTALL':
                    $action = "RUNBEFOREINSTALL";
                    $script = $val;
                break;
                                case 'RUNAFTERINSTALL':
                    $action = "RUNAFTERINSTALL";
                    $script = $val;
                break;
                case 'Parameters':
                    $parameters = $val;
                break;
                case 'User':
                    $user = $val;
                break;
                case 'Type':
                    $type = $val;
                break;
                case 'Dir':
                    $dir = $val;
                break;
                }
                        }

                if(!empty($key)) {
                        $this->inst["run"][] = array('action' => $action,
                                                         'script' => $script,
                                                                                          'params' => $parameters,
                                                                                          'user' => $user,
                                                                                          'type' => $type,
                        'dir' => $dir);
                }

                        unset($action);
                        unset($script);
                        unset($params);
                        unset($user);
                        unset($type);
      unset($dir);
        }
    }
}

function install($temp_path) {
global $go_api, $go_info;

        // Run Before Install ausführen
    if(is_array($this->inst['run'])) {
        foreach($this->inst['run'] as $rnow) {
            if($rnow["action"] == "RUNBEFOREINSTALL") {
                // PHP Scripte
                                if($rnow["type"] == "php") {
                                        include_once($rnow["script"]);
                                }
        //die("1111");
                                // Shell Scripte
                                if($rnow["type"] == "shell") {
                                        $script = trim($rnow["script"]);
                                        if(in_array($script,$this->allowed_programs)) {
                                                                $params = escapeshellcmd($rnow["params"]);
                $cdir = ($rnow["dir"] != '')? "cd ".$rnow["dir"]."; ":'';
                                                                exec($cdir.$script." ".$params);
                                                                }
                                }
            }
        }
    }

    // Verzeichnisse anlegen
    if(is_array($this->inst['dirs'])) {
        foreach($this->inst['dirs'] as $mdir) {
            if($mdir["action"] == "MKDIR") {
                @mkdir($mdir["dir"],0755) or $go_api->errorMessage("Konnte Verzeichnis '".$mdir["dir"]."' nicht erstellen.");
            }
            // Achtung: verzeichnis muss leer sein!
            if($mdir["action"] == "RMDIR") {
                @mkdir($mdir["dir"]) or $go_api->errorMessage("Konnte Verzeichnis '".$mdir["dir"]."' nicht löschen.");;
            }
        }
    }

    // Dateien kopieren
    if(is_array($this->inst['files'])) {
        foreach($this->inst['files'] as $mfile) {
            if($mfile["action"] == "CPFILE") {
                @copy($mfile["source"],$mfile["destdir"].'/'.$mfile["destname"] ) or $go_api->errorMessage("Konnte Datei '".$mfile["destdir"].'/'.$mfile["destname"]."' nicht kopieren.");
            }
        }
    }

    // sql Statements ausführen
    if(is_array($this->inst['sql'])) {
        foreach($this->inst['sql'] as $msql) {
            if($msql["action"] == "SQLEXEC") {
                $go_api->db->query($msql["query"]);
            }

            if($msql["action"] == "SQLFILEEXEC") {
                //exec($msql["command"] . " -e \"source ".$msql["sql_file"]."\" ".$msql["db"]);
                $fp = fopen ($msql["sql_file"], "r") or $go_api->errorMessage("Can not open SQL File: ".$msql["sql_file"]);
                while($sql_line = fgets($fp,100000)) {
                    $sql_line = str_replace("\r\n", "", $sql_line);
                    $sql_line = str_replace("\n", "", $sql_line);
                    $go_api->db->query($sql_line);
                    if($go_api->db->errorMessage != "") $go_api->errorMessage($go_api->db->errorMessage);
                }
                fclose($fp);
            }
        }
    }

        // Run After Install ausführen
    if(is_array($this->inst['run'])) {
        foreach($this->inst['run'] as $rnow) {
            if($rnow["action"] == "RUNAFTERINSTALL") {
                // PHP Scripte
                                                        if($rnow["type"] == "php") {
                                                                include_once($rnow["script"]);
                                                        }

                                                        // Shell Scripte
                                                        if($rnow["type"] == "shell") {
                                                                $script = trim($rnow["script"]);
                                                                if(in_array($script,$this->allowed_programs)) {
                                                                $params = escapeshellcmd($rnow["params"]);
                $cdir = ($rnow["dir"] != '')? "cd ".$rnow["dir"]."; ":'';
                                                                exec($cdir.$script." ".$params);
                                                                }
                                                        }
            }
        }
    }
}



// Ende der Klasse
}
?>