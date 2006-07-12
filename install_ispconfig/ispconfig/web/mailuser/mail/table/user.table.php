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

/*
        Tabellendefinition

        Datentypen:
        - INTEGER (Wandelt Ausdrücke in Int um)
        - DOUBLE
        - CURRENCY (Formatiert Zahlen nach Währungsnotation)
        - VARCHAR (kein weiterer Format Check)
        - DATE (Datumsformat, Timestamp Umwandlung)

        Formtype:
        - TEXT (normales Textfeld)
        - PASSWORD (Feldinhalt wird nicht angezeigt)
        - SELECT (Gibt Werte als option Feld aus)

        VALUE:
        - Wert oder Array

        Hinweis:
        Das ID-Feld ist nicht bei den Table Values einzufügen.


*/


$table_name = "isp_isp_user";
$table_index = "doc_id";

$table['user_passwort'] = array(         'datatype'                 => "VARCHAR",
                                                                          'formtype'                 => "TEXT",
                                                                          'regex'                        => "",
                                                                          'errmsg'                => "",
                                                                          'value'                 => "");

$table['user_emailalias'] = array(         'datatype'                 => "VARCHAR",
                                                                          'formtype'                 => "TEXT",
                                                                          'regex'                        => "",
                                                                          'errmsg'                => "",
                                                                          'value'                 => "");

$table['user_name'] = array(         'datatype'                 => "VARCHAR",
                                                                          'formtype'                 => "TEXT",
                                                                          'regex'                        => "",
                                                                          'errmsg'                => "",
                                                                          'value'                 => "");

$table['user_autoresponder'] = array(         'datatype'                 => "VARCHAR",
                                                                  'formtype'                 => "SELECT",
                                                                  'regex'                        => "",
                                                                  'errmsg'                => "",
                                                                  'value'                 => array(        0 => $app->lng("txt_no"),
                                                                                                                        1 => $app->lng("txt_yes")));


$table['user_autoresponder_text'] = array(         'datatype'                 => "VARCHAR",
                                                                          'formtype'                 => "TEXT",
                                                                          'regex'                        => "",
                                                                          'errmsg'                => "",
                                                                          'value'                 => "");

$table['user_catchallemail'] = array(         'datatype'                 => "VARCHAR",
                                                                  'formtype'                 => "SELECT",
                                                                  'regex'                        => "",
                                                                  'errmsg'                => "",
                                                                  'value'                 => array(        0 => $app->lng("txt_no"),
                                                                                                                        1 => $app->lng("txt_yes")));

$table['user_mailscan'] = array(         'datatype'                 => "VARCHAR",
                                                                  'formtype'                 => "SELECT",
                                                                  'regex'                        => "",
                                                                  'errmsg'                => "",
                                                                  'value'                 => array(        0 => $app->lng("txt_no"),
                                                                                                                        1 => $app->lng("txt_yes")));

$table['user_emailweiterleitung_local_copy'] = array(         'datatype'                 => "VARCHAR",
                                                                  'formtype'                 => "SELECT",
                                                                  'regex'                        => "",
                                                                  'errmsg'                => "",
                                                                  'value'                 => array(        0 => $app->lng("txt_no"),
                                                                                                                        1 => $app->lng("txt_yes")));

$table['user_email'] = array(         'datatype'                 => "VARCHAR",
                                                                          'formtype'                 => "TEXT",
                                                                          'regex'                        => "",
                                                                          'errmsg'                => "",
                                                                          'value'                 => "");

$table['user_emailweiterleitung'] = array(         'datatype'                 => "VARCHAR",
                                                                          'formtype'                 => "TEXT",
                                                                          'regex'                        => "",
                                                                          'errmsg'                => "",
                                                                          'value'                 => "");


$table['user_spamfilter'] = array(         'datatype'                 => "VARCHAR",
                                                                  'formtype'                 => "SELECT",
                                                                  'regex'                        => "",
                                                                  'errmsg'                => "",
                                                                  'value'                 => array(        0 => $app->lng("txt_no"),
                                                                                                                        1 => $app->lng("txt_yes")));


$table['spam_strategy'] = array(         'datatype'                 => "VARCHAR",
                                                                  'formtype'                 => "SELECT",
                                                                  'regex'                        => "",
                                                                  'errmsg'                => "",
                                                                  'value'                 => array(        "accept" => $app->lng("txt_accept"),
                                                                                                                        "discard" => $app->lng("txt_discard")));

$table['spam_hits'] = array('datatype'                 => "VARCHAR",
                                                                  'formtype'                 => "TEXT",
                                                                  'regex'                        => "/^[0-9]\.[0-9]{1}$/",
                                                                  'errmsg'                => $app->lng("txt_error_spam_hits_format")." 0.0",
                                                                  'value'                 => "");

$table['spam_rewrite_subject'] = array(         'datatype'                 => "VARCHAR",
                                                                  'formtype'                 => "SELECT",
                                                                  'regex'                        => "",
                                                                  'errmsg'                => "",
                                                                  'value'                 => array(        0 => $app->lng("txt_no"),
                                                                                                                        1 => $app->lng("txt_yes")));

$table['spam_subject_tag'] = array(         'datatype'                 => "VARCHAR",
                                                                          'formtype'                 => "TEXT",
                                                                          'regex'                        => "",
                                                                          'errmsg'                => "",
                                                                          'value'                 => "");

$table['spam_whitelist'] = array(         'datatype'                 => "VARCHAR",
                                                                          'formtype'                 => "TEXT",
                                                                          'regex'                        => "",
                                                                          'errmsg'                => "",
                                                                          'value'                 => "");

$table['spam_blacklist'] = array(         'datatype'                 => "VARCHAR",
                                                                          'formtype'                 => "TEXT",
                                                                          'regex'                        => "",
                                                                          'errmsg'                => "",
                                                                          'value'                 => "");

$table['antivirus'] = array(         'datatype'                 => "VARCHAR",
                                                                  'formtype'                 => "SELECT",
                                                                  'regex'                        => "",
                                                                  'errmsg'                => "",
                                                                  'value'                 => array(        0 => $app->lng("txt_no"),
                                                                                                                        1 => $app->lng("txt_yes")));

$ordner = realpath(getcwd()."/../lib/lang");
$handle = @opendir($ordner);
while ($file = @readdir ($handle)) {
    if ($file != "." && $file != "..") {
        if(@is_file($ordner."/".$file)) {
                        $path_parts = pathinfo($ordner."/".$file);
                        if($path_parts["extension"] == 'lng') {
                                $language = substr($path_parts["basename"],0,2);
                                $lng_arr[$language] = $language;
                        }
        }
    }
}

$table['user_lang'] = array(         'datatype'                 => "VARCHAR",
                                                                  'formtype'                 => "SELECT",
                                                                  'regex'                        => "",
                                                                  'errmsg'                => "",
                                                                  'value'                 => $lng_arr);


$table['status'] = array( 'datatype'                 => "VARCHAR",
                                                                          'formtype'                 => "TEXT",
                                                                          'regex'                        => "",
                                                                          'errmsg'                => "",
                                                                          'value'                 => "");

?>