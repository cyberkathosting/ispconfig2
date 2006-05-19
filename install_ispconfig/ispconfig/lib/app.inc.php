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

if(CONFIG_LOADED != true) die('Direct access not permitted.');

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

      $dbclass = $go_info["server"]["classes_root"] . $go_info["server"]["dir_trenner"] ."ispconfig_db_".$go_info["server"]["db_type"].".lib.php";
      include_once($dbclass);
          $dbname = 'db_'.$go_info["server"]["db_type"];
      $this->db = new $dbname;

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

?>