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
                                            table   => "ispfile_edit_file.htm",
                                            stylesheet => "style.css"));

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "von Till",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "#FFFFFF",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Datei")."</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                            CHARSET => $go_info["theme"]["charset"],
                                                                        SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                                ) );

// Assign Text Elements
$go_api->content->assign( array(        TXT_NAME => $go_api->lng("Name"),
                                                                        TXT_FOLDER => $go_api->lng("Ordner"),
                                                                        TXT_OWNER => $go_api->lng("Eigentümer"),
                                                                        TXT_GROUP => $go_api->lng("Gruppe"),
                                                                        TXT_ALL => $go_api->lng("Alle"),
                                                                        TXT_R => $go_api->lng("Lesen"),
                                                                        TXT_W => $go_api->lng("Schreiben"),
                                                                        TXT_X => $go_api->lng("Ausführen"),
                                                                        SPEICHERN => $go_api->lng("Speichern"),
                                                                        LOESCHEN => $go_api->lng("Löschen"),
                                                                        CONFIRM_DELETE => $go_api->lng("txt_confirm_delete"),
                                                                        ABBRECHEN => $go_api->lng("Abbrechen")
                                                                        ));

        $web_id = $HTTP_GET_VARS["web_id"];
        $ed = $HTTP_GET_VARS["ed"];

        if($ed != "new") $ed = $go_api->isp_webftp->webftp_check_params($ed);

    // Bestimme Unterordner und Ordnername
        $ed_array = explode("/",$ed);
        $file_name = $ed_array[count($ed_array) - 1];
        unset($ed_array[count($ed_array) - 1]);
        $unterordner = implode("/",$ed_array);

        $list = $go_api->isp_webftp->webftp_read($unterordner);

        if(is_array($list)) {
                foreach($list as $item) {
                        if($item["name"] == $file_name) {
                                $file = $item;
                                break;
                        }
                }
        }

        $perm_user = substr($file["perms"],1,3);
        $perm_group = substr($file["perms"],4,3);
        $perm_other = substr($file["perms"],7,3);


        if(stristr($perm_user,"r")) $perm_user_read = "CHECKED";
        if(stristr($perm_user,"w")) $perm_user_write = "CHECKED";
        if(stristr($perm_user,"x")) $perm_user_exec = "CHECKED";

        if(stristr($perm_group,"r")) $perm_group_read = "CHECKED";
        if(stristr($perm_group,"w")) $perm_group_write = "CHECKED";
        if(stristr($perm_group,"x")) $perm_group_exec = "CHECKED";

        if(stristr($perm_other,"r")) $perm_other_read = "CHECKED";
        if(stristr($perm_other,"w")) $perm_other_write = "CHECKED";
        if(stristr($perm_other,"x")) $perm_other_exec = "CHECKED";

        $go_api->content->assign( array( BTEXT => $file_name,
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