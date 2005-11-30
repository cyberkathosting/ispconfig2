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

if($set_header == 1) {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");             // Date in the past
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
    header("Cache-Control: no-cache, must-revalidate");           // HTTP/1.1
    header("Pragma: no-cache");
    ob_start("ob_gzhandler");
}

$go_info["server"]["dir_trenner"] = '/';


/********************************************************/
/* Laden der Basis Klassen                              */
/********************************************************/

include($go_info["server"]["classes_root"] . $go_info["server"]["dir_trenner"] ."ispconfig_template.lib.php");

/*********************************************************/
/* Setting up API                                                                                                  */
/*********************************************************/

class go_api
  {
    var $template;
    var $language = 'de';
    var $_language_inc = 0;
    var $_wb;

    function go_api()
    {
      global $go_info;

      /**********************************************************/
      /* Aufsetzen Rest API                                     */
      /**********************************************************/
      /*
      include($go_info["server"]["classes_root"] .'\\adodb\\adodb.inc.php');
      $this->db = NewADOConnection($go_info["server"]["db_type"]);
      $this->db->Connect("localhost", "root", "", "db_ispconfig");
      //print_r($this->db);
      //die();
      */
      $dbclass = $go_info["server"]["classes_root"] . $go_info["server"]["dir_trenner"] ."ispconfig_db_".$go_info["server"]["db_type"].".lib.php";
      include_once($dbclass);
          $dbname = 'db_'.$go_info["server"]["db_type"];
      $this->db = new $dbname;

      $this->uses("auth,session,groups,content,log");
      $this->template = new FastTemplate;

      $this->language = $go_info["server"]["lang"];
    }

      /**********************************************************/
      /* Übersetzung / Messagekatalog                           */
      /**********************************************************/

      function lng($text)
      {
        global $go_info,$s;
        if($this->_language_inc != 1) {
            // lade globales und modul Wordbook
            @include_once($go_info["server"]["include_root"].$go_info["server"]["dir_trenner"]."lang".$go_info["server"]["dir_trenner"].$this->language.".lng");
            @include_once($go_info["server"]["server_root"] . $go_info["server"]["dir_trenner"]  ."web".$go_info["server"]["dir_trenner"]. $go_info["modul"]["path"] .  $go_info["server"]["dir_trenner"] ."lib" . $go_info["server"]["dir_trenner"] . "lang" . $go_info["server"]["dir_trenner"] . $this->language.".lng");
            $this->_wb = $wb;
            $this->_language_inc = 1;
        }
        /*
        if(!empty($this->_wb[$text])) {
            $text = '['.$this->_wb[$text].']';
        } else {
            $text = '[['.$text.']]';
        }
        */

        if(!empty($this->_wb[$text])) {
            $text = $this->_wb[$text];
        } else {
                        //$this->uses("lng_debug");
                        //$this->lng_debug->add($text);
                        //$text = '#'.$text.'#';
        }

        return $text;
      }

      /**********************************************************/
      /* Moduleinbindung                                        */
      /**********************************************************/

      function uses($modules)
      {
      global $go_info;

      $modules = explode(",",$modules);
      foreach($modules as $value)
                      {
            $value = trim($value);
                      include_once($go_info["server"]["classes_root"] . $go_info["server"]["dir_trenner"] ."ispconfig_".$value.".lib.php");
                           $this->$value = new $value;
                           }
      }

      /**********************************************************/
      /* Objekteinbindung                                       */
      /**********************************************************/

      function uses_obj($objects)
      {
      global $go_info;

      $objects = explode(",",$objects);
      foreach($objects as $value)
                      {
                      include_once($go_info["server"]["classes_root"] . $go_info["server"]["dir_trenner"] ."ispconfig_".$value.".obj.php");
                           }
      }

      /**********************************************************/
      /* Error Handler                                          */
      /**********************************************************/

      function errorMessage($message,$stop = 1)
      {
      $this->msg($message,'','error',$stop);
      }

      /**********************************************************/
      /* Message Box                                            */
      /**********************************************************/
      //type = 'error', 'msgbox', 'log'

      function msg($message, $title = '', $type = 'msgbox',$stop = 1)
      {
        $this->uses('msg');
                $this->log($message,0);
        if($type == 'error') {
            $this->msg->message($message,$this->lng("Es ist folgender Fehler aufgetreten:"),$stop);
        } else {
            $this->msg->message($message,$title,$stop);
        }
      }

          /**********************************************************/
      /* Logging                                                    */
      /**********************************************************/

          function log($message,$level = 1)
    {
        $this->log->msg($message,$level,$go_info["server"]["log_device"]);
    }

}


/**********************************************************/
/* Initialisieren des API, der Session                    */
/**********************************************************/

$go_api = new go_api;

/*********************************************************/
/* User Authentifizierung                                */
/*********************************************************/

if ($s == "")
   {
   // User überprüfen
   $go_api->auth->check_user();
   } else {
   // Session überprüfen
   $go_api->auth->check_session();

  }

if ($go_api->auth->status != "ok")
  {
   $go_api->auth->check_user();
  }

$s = $go_api->session->start();

$go_info["server"]["dir_trenner"] = '/';

/**********************************************************/
/* Füllen des info arrays, wenn neue Session              */
/**********************************************************/

if($go_info["session"]["id"] == "")
{
include($go_info["server"]["include_root"] . $go_info["server"]["dir_trenner"] ."go_info.inc.php");
$go_api->session->save();
}

// Standard Language Einstellungen mit User-Language überschreiben
$go_api->language = $go_info["user"]["language"];

/********************************************************/
/* Laden der optionalen Modulklassen                    */
/********************************************************/

if($go_info["modul"]["name"] == "bookmark")
{
include($go_info["server"]["classes_root"] . $go_info["server"]["dir_trenner"] ."ispconfig_bookmark.lib.php");
$go_api->bookmark = new bookmark;
}
/**********************************************************/
/* Laden des Modul Includes                               */
/**********************************************************/

//if(@is_file($go_info["server"]["server_root"] . $go_info["server"]["dir_trenner"]  ."web".$go_info["server"]["dir_trenner"]. $go_info["modul"]["path"] .  $go_info["server"]["dir_trenner"] ."lib" . $go_info["server"]["dir_trenner"] . "module.inc.php")) {
    include($go_info["server"]["server_root"] . $go_info["server"]["dir_trenner"]  ."web".$go_info["server"]["dir_trenner"]. $go_info["modul"]["path"] .  $go_info["server"]["dir_trenner"] ."lib" . $go_info["server"]["dir_trenner"] . "module.inc.php");
//}

/**********************************************************/
/* Ausgabe der session registry im debug modus            */
/**********************************************************/

if($go_api->debug == 1) {

echo $go_api->auth->userid;

foreach($go_info as $key => $elem) {

       echo "<br>$key => $elem <br><br>";
       foreach($go_info[$key] as $key2 => $elem2) {
       echo "$key2 => $elem2 <br>";

        }
        }
}

/**********************************************************/
/* Für Abwärtskompatibilität                              */
/**********************************************************/
        $serverurl = $go_info["server"]["server_url"];
        $userid = $go_info["user"]["userid"];
        $groupid = $go_info["group"]["groupid"];
        $session_design = $go_info["theme"]["title"];
        $bookmark_order = $go_info["modul"]["item_order"];
        $news = $go_info["modul"]["news"];
        $session = "s=" . $go_info["session"]["id"];
        $session_modul =  $go_info["modul"]["name"];
        $nav_color = $go_info["theme"]["page"]["nav_color"];
        $box_color = $go_info["theme"]["page"]["box_color"];
        $session_site = $go_info["session"]["site"];
        $session_domain = $go_info["session"]["domain"];
        $session_height = $go_info["session"]["screen_height"];
        $session_width = $go_info["session"]["screen_width"];
        // $session_design_width = $session_width - 57;
        $session_design_width = $go_info["theme"]["width"];
        // $session_design = "blau";
        $session_design_path = $go_info["theme"]["path"];
        $session_style_path = $go_info["theme"]["style_path"];
        $session_modus = $go_info["theme"]["modus"];
        $session_text_font = "";
        $session_text_fontsize = "";
        $session_text_title = "#000080";
        $session_text_colour = "#FF0000";
        $session_text_dcolour = "";
        $session_page_hcolour = "#".$box_color;
        $session_page_bgcolour = "#FFFFFF";
        $session_page_dcolour = "#FFFFFF";
        $session_nav_hcolour = "#".$nav_color;
        $session_nav_dcolour = "#000080";
        $session_bgcolour = $session_page_bgcolour;
?>