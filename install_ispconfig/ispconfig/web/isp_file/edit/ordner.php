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

$go_api->content->define( array(    main    => "main.htm",
                                            table   => "ispfile_edit_ordner.htm",
                                            stylesheet => "style.css"));

$go_api->content->define_dynamic ( "liste", "table" );

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "von Till",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "#FEFEFE",
                                                            BOXSIZE => "100%",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Ordner").": </font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                                        SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                    ) );
// Assign Text Elements
$go_api->content->assign( array(        TXT_NAME => $go_api->lng("Name"),
                                                                        TXT_SIZE => $go_api->lng("Grösse"),
                                                                        TXT_DATUM => $go_api->lng("Datum")
                                                                        ));

$go_api->uses("isp_web,isp_webftp");

$web_id = $HTTP_GET_VARS["web_id"];
$ordner = $go_api->isp_webftp->webftp_check_params($HTTP_GET_VARS["ordner"]);

// Hole Einträge des Ordners
$list = $go_api->isp_webftp->webftp_read($ordner);
if(is_array($list)) {
        foreach($list as $item) {
                if(substr($item["perms"],0,1) == "d") {
                        $list_ordner[] = $item;
                } elseif (substr($item["perms"],0,1) != "l") {
                        $list_file[] = $item;
                }
        }
}
unset($list);

$bgcolor = "#EEEEEE";

// Auflisten der Ordner
if(is_array($list_ordner)) {
        sort($list_ordner);
        foreach($list_ordner as $item) {

        $datum = $item["month"]." ".$item["day"]." ".$item["time"];

        if($bgcolor == "#FFFFFF") {
                  $bgcolor = "#EEEEEE";
    } else {
            $bgcolor = "#FFFFFF";
    }

        $go_api->content->assign( array(        NAME => $item["name"],
                                                                                ICON => 'vzzu-0.gif',
                                                                                DATUM => $datum,
                                                                                SIZE => $item["size"],
                                                                                LOESCHEN => "ordner_delete.php?s=$s&web_id=$web_id&ordner=".$HTTP_GET_VARS["ordner"]."&ed=".$HTTP_GET_VARS["ordner"].':'.$item["name"],
                                                                                CONFIRM_DELETE => $go_api->lng("txt_confirm_delete"),
                                                                                ED => $HTTP_GET_VARS["ordner"].':'.$item["name"],
                                                            WEB_ID => $web_id,
                                                                                SCRIPT => "ordner_edit.php",
                                                                                BGCOLOR => $bgcolor
                                                                                ));

        $go_api->content->parse(LISTE,".liste");
        }
}


// Auflisten der Dateien
$n = 0;
if(is_array($list_file)) {
        sort($list_file);
        foreach($list_file as $item) {
                // Wenn Die Datei nicht mit einem Punkt beginnt
                if(substr($item,0,1) != ".") {

                        $datum = $item["month"]." ".$item["day"]." ".$item["time"];
                        $size = $item["size"];;

                        if($size > 0) $size = sprintf("%01.2f", $size / 1024) . " KB";

                        if($bgcolor == "#FFFFFF") {
                                  $bgcolor = "#EEEEEE";
                    } else {
                            $bgcolor = "#FFFFFF";
                    }

                        $go_api->content->assign( array(        NAME => $item["name"],
                                                                                                ICON => 'doc.gif',
                                                                                                DATUM => $datum,
                                                                                                SIZE => $size,
                                                                                                LOESCHEN => "file_delete.php?s=$s&web_id=$web_id&ordner=".$HTTP_GET_VARS["ordner"]."&ed=".$HTTP_GET_VARS["ordner"].':'.$item["name"],
                                                                                                CONFIRM_DELETE => $go_api->lng("txt_confirm_delete"),
                                                                                                ED => $HTTP_GET_VARS["ordner"].':'.$item["name"],
                                                                            WEB_ID => $web_id,
                                                                                                SCRIPT => "file_edit.php",
                                                                                                BGCOLOR => $bgcolor
                                                                                                ));

                        $go_api->content->parse(LISTE,".liste");
                        $n++;
                }
        }
}


if($n == 0) $go_api->content->clear_dynamic("liste");

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;


?>