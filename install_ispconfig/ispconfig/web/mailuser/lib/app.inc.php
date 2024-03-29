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

if($go_info["server"]["mode"] == 'demo') {
	require_once("../../../lib/config.inc.php");
} else {
	require_once("/home/admispconfig/ispconfig/lib/config.inc.php");
}

if(CONFIG_LOADED != 1) die('Direct access not permitted.');

$conf["rootpath"]                 = SERVER_ROOT."/web/mailuser";
$conf["classpath"]                 = $conf["rootpath"]."/lib/classes";

/*
        Database Settings
*/

$conf["db_type"]                 = 'mysql';
$conf["db_host"]                 = $go_info["server"]["db_host"];
$conf["db_database"]         = $go_info["server"]["db_name"];
$conf["db_user"]                 = $go_info["server"]["db_user"];
$conf["db_password"]         = $go_info["server"]["db_password"];

$conf["charset"] = $go_info["theme"]["charset"];

/*
        Themes
*/

$conf["theme"]                         = 'default';

/*
        Default Language
*/

$conf['language']                = 'de';


/*
        Auto Load Modules
*/

$conf["start_db"]                 = true;
$conf["start_session"]         = true;



class app {

        var $_language_inc = 0;
        var $_wb;

        function app() {

                global $conf, $go_info;

                if($conf["start_db"] == true) {
                        $this->load('db_'.$conf["db_type"]);
                        $this->db = new db;
                }

                if($conf["start_session"] == true) {
                        session_start();
                        $_SESSION["s"]['id'] = session_id();
                        if($_SESSION["s"]["theme"] == '') $_SESSION["s"]['theme'] = $conf['theme'];
                        if($_SESSION["s"]["userid"] > 0){
                          $_SESSION["s"]["language"] = $_SESSION["s"]["user"]["user_lang"];
                        }
                        if($_SESSION["s"]["language"] == '') $_SESSION["s"]['language'] = $go_info["server"]["lang"];
                        if($_SESSION["s"]["language"] == '') $_SESSION["s"]['language'] = $conf['language'];
                }

        }

        function uses($classes) {
                global $conf;

                $cl = explode(',',$classes);
                if(is_array($cl)) {
                        foreach($cl as $classname) {
                                if(!is_object($this->$classname)) {
                                        include_once($conf['classpath'] . "/".$classname.".inc.php");
                                        $this->$classname = new $classname;
                                }
                        }
                }

        }

        function load($files) {

                global $conf;
                $fl = explode(',',$files);
                if(is_array($fl)) {
                        foreach($fl as $file) {
                                include_once($conf['classpath'] . "/".$file.".inc.php");
                        }
                }

        }

        /*
         0 = DEBUG
         1 = WARNING
         2 = ERROR
        */

        function log($msg, $priority = 0) {

                if($priority >= $conf["log_priority"]) {
                        if (is_writable($conf["log_file"])) {

                            if (!$fp = fopen ($conf["log_file"], "a")) {
                                $this->error("Logfile konnte nicht ge�ffnet werden.");
                            }
                            if (!fwrite($fp, date("d.m.Y-H:i")." - ". $msg."\r\n")) {
                                $this->error("Schreiben in Logfile nicht m�glich.");
                            }
                            fclose($fp);

                        } else {
                            $this->error("Logfile ist nicht beschreibbar.");
                        }
                } // if
        } // func

        /*
         0 = DEBUG
         1 = WARNING
         2 = ERROR
        */

        function error($msg, $priority = 2) {
                //$this->uses("error");
                //$this->error->message($msg, $priority);
                echo $msg;
                if($priority == 2) exit;
        }

        function lng($text)
      {
        global $conf;
        if($this->_language_inc != 1) {
            // lade globales und modul Wordbook
            @include_once($conf["rootpath"]."/lib/lang/".$_SESSION["s"]["language"].".lng");
            //@include_once($conf["rootpath"]."/web/".$_SESSION["s"]["module"]["name"]."/lib/lang/".$_SESSION["s"]["language"].".lng");
            $this->_wb = $wb;
            $this->_language_inc = 1;
        }

        if(!empty($this->_wb[$text])) {
            $text = $this->_wb[$text];
        }

        return $text;
      }

          function tpl_defaults() {
                  global $conf;

                $this->tpl->setVar('theme',$_SESSION["s"]["theme"]);
                $this->tpl->setVar('phpsessid',session_id());
                $this->tpl->setVar('charset', $conf["charset"]);

                if($this->_language_inc != 1) {
            // lade globales und modul Wordbook
            @include_once($conf["rootpath"]."/lib/lang/".$_SESSION["s"]["language"].".lng");
            //@include_once($conf["rootpath"]."/web/".$_SESSION["s"]["module"]["name"]."/lib/lang/".$_SESSION["s"]["language"].".lng");
            $this->_wb = $wb;
            $this->_language_inc = 1;
        }
                $this->tpl->setVar($this->_wb);

          }

}

/*
 Initialisiere Applikations-Objekt
*/

$app = new app;

?>