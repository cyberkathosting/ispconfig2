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
include("../../../lib/config.inc.php");
include("../../../lib/session.inc.php");

$go_api->uses("isp_web,isp_webftp");
$go_api->content->define( array(    main    => "main.htm",
                                            table   => "ispfile_edit_htaccess_list.htm",
                                            stylesheet => "style.css"));

$go_api->content->define_dynamic ( "liste", "table" );

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "von Till",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "#FFFFFF",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng(".htaccess Userübersicht")."</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                            CHARSET => $go_info["theme"]["charset"],
                                                                        SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                                ) );

// Assign Text Elements
$go_api->content->assign( array(        BEARBEITEN => $go_api->lng("Bearbeiten"),
                                                                        LOESCHEN => $go_api->lng("Löschen"),
                                                                        HINZUFUEGEN => $go_api->lng("Hinzufügen")
                                                                        ));

        $web_id = $HTTP_GET_VARS["web_id"];
        $ed = $HTTP_GET_VARS["ed"];

    $file = $go_api->isp_webftp->webftp_check_params($ed);


        $vtemp = array();
        $htaccess = array();
    $bgcolor = "#EEEEEE";

        // Lese .htpasswd Datei
        if($go_api->isp_webftp->webftp_file_changed($file."/.htpasswd") != -1) {
                $tmpfname = tempnam($go_info["server"]["temp_dir"], "hta_");
                if(!$go_api->isp_webftp->webftp_get($tmpfname,$file."/.htpasswd", FTP_BINARY)) $go_api->errorMessage($go_api->lng("FTP: Failed to open $file/.htpasswd"));
                if(!$htpasswd = file($tmpfname)) $go_api->errorMessage($go_api->lng("Failed to open $tmpfname"));
                unlink($tmpfname);
        }

        // Schreibe Datei in Array $users
        if(is_array($htpasswd)) {
                foreach($htpasswd as $line) {
                        list($usr,$pw) = explode(":",$line);

                    $vtemp["NAME"] = $usr;
                    $vtemp["USERNAME"] = urlencode($usr);
                    $vtemp["SCRIPT"] = 'htaccess_user.php';
                    $vtemp["WEB_ID"] = $web_id;
                          $vtemp["ED"] = $ed;

                    if($bgcolor == "#FFFFFF") {
                            $bgcolor = "#EEEEEE";
                    } else {
                            $bgcolor = "#FFFFFF";
                    }
                    $vtemp["BGCOLOR"] = $bgcolor;

                        $go_api->content->assign($vtemp);
                    $go_api->content->parse(LISTE,".liste");
                }
        }

          $vtemp = array();

          $vtemp["WEB_ID"] = $web_id;
          $vtemp["ED"] = $ed;
          $go_api->content->assign($vtemp);


        $go_api->content->parse(STYLESHEET, stylesheet);
        $go_api->content->parse(MAIN, array("table","main"));
        $go_api->content->FastPrint();
        exit;

?>