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
                                            table   => "ispfile_edit_upload.htm",
                                            stylesheet => "style.css"));

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "von Till",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "#FFFFFF",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Datei Upload")."</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                            CHARSET => $go_info["theme"]["charset"],
                                                                        SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                                ) );

// Assign Text Elements
$go_api->content->assign( array(        TXT_FILE => $go_api->lng("Datei"),
                                                                        TXT_FOLDER => $go_api->lng("Ordner"),
                                                                        TXT_OWNER => $go_api->lng("Eigentümer"),
                                                                        TXT_GROUP => $go_api->lng("Gruppe"),
                                                                        TXT_ALL => $go_api->lng("Alle"),
                                                                        TXT_R => $go_api->lng("Lesen"),
                                                                        TXT_W => $go_api->lng("Schreiben"),
                                                                        TXT_X => $go_api->lng("Ausführen"),
                                                                        SPEICHERN => $go_api->lng("Speichern"),
                                                                        LOESCHEN => $go_api->lng("Löschen"),
                                                                        ABBRECHEN => $go_api->lng("Abbrechen")
                                                                        ));


        $web_id = $HTTP_GET_VARS["web_id"];
        //$ed = $HTTP_GET_VARS["ed"];
        $ed = "";

    // Checke Web_id

    if(!isset($web_id)) $go_api->errorMessage($go_api->lng("Parameterübergabe unvollständig"));
    $web_id = intval($web_id);
    if(!is_int($web_id) or $web_id == 0) $go_api->errorMessage($go_api->lng("Ungültiges Format der web_id."));

    //Checke Userrechte am Web
    if(!$row = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doctype_id = ".$go_api->isp_web->web_doctype_id." and doc_id = '$web_id'")) $go_api->errorMessage($go_api->lng("Ungültige web_id."));

    if($go_info["user"]["userid"] != $row["userid"]) {
        $go_api->auth->check_write($row["groupid"]);
    } else {
        $go_api->auth->check_write(0);
    }

        // Hole document root
    //if(!$row = $go_api->db->queryOneRecord("SELECT * from isp_server")) $go_api->errorMessage($go_api->lng("Konnte server_path_httpd_root nicht ermitteln."));
    //$dir_root = $row["server_path_httpd_root"]."/web$web_id";

    // zum testen
    //if($go_info["server"]["mode"] == "demo") $dir_root = $go_info["demo"]["web_path"];

        $perm_user_read = "CHECKED";
        $perm_user_write = "CHECKED";
        //$perm_user_exec = "CHECKED";

        $perm_group_read = "CHECKED";
        //$perm_group_write = "CHECKED";
        //$perm_group_exec = "CHECKED";

        $perm_other_read = "CHECKED";
        //$perm_other_write = "CHECKED";
        //$perm_other_exec = "CHECKED";


        $go_api->content->assign( array( BTEXT => $filename,
                                 PERM_USER_READ => $perm_user_read,
                                 PERM_USER_WRITE => $perm_user_write,
                                 PERM_USER_EXEC => $perm_user_exec,
                                 PERM_GROUP_READ => $perm_group_read,
                                 PERM_GROUP_WRITE => $perm_group_write,
                                 PERM_GROUP_EXEC => $perm_group_exec,
                                 PERM_OTHER_READ => $perm_other_read,
                                 PERM_OTHER_WRITE => $perm_other_write,
                                 PERM_OTHER_EXEC => $perm_other_exec));

        if($go_info["server"]["webftp_chmod_disable"] == true) {
                $go_api->content->assign( array(         CHMOD_BEGIN => '<!--',
                                                                                        CHMOD_END         => '-->'));
        } else {
                $go_api->content->assign( array(         CHMOD_BEGIN => '',
                                                                                        CHMOD_END         => ''));
        }

        $ordner_option = explode("\n", $go_api->isp_webftp->webftp_tree());

        if(is_array($ordner_option)) {
                foreach($ordner_option as $item) {
                        $selected = ($unterordner == $item)?'SELECTED':'';
                        if($item != $ed and $item != '') $node_option .= "<option value=\"root".str_replace("/",":",$item)."\" $selected>$item</option>\n";
                }
        }

        $go_api->content->assign( array(         NODES => $node_option,
                                                                                ED => $HTTP_GET_VARS["ed"],
                                                                                WEB_ID => $web_id));

        $go_api->content->parse(STYLESHEET, stylesheet);
        $go_api->content->parse(MAIN, array("table","main"));
        $go_api->content->FastPrint();
        exit;
?>