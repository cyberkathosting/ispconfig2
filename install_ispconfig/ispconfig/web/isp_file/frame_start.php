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
include("../../lib/config.inc.php");
include("../../lib/session.inc.php");
//include("../start.php");


/*
// Web-Tree löschen, falls gesetzt
$go_info["webftp"]["dir_tree"] = '';
$go_api->session->save();

// Web-FTP einbinden und initialisieren
$go_api->uses("isp_webftp");

//$ordner = $go_api->isp_webftp->webftp_tree();
$ordner = explode("\n", $go_api->isp_webftp->webftp_tree());
$go_api->isp_webftp->webftp_close();



die("fertig");
*/

$web_id = intval($_REQUEST["web_id"]);

//die($_POST["ftp_user"]);

if($web_id > 0 and $_POST["ftp_user"] != '' and $_POST["ftp_passwort"] != '') {
        $go_info["webftp"]["web_id"]   = $web_id;
        $go_info["webftp"]["user"]     = $_POST["ftp_user"];
        $go_info["webftp"]["passwort"] = $_POST["ftp_passwort"];

        $go_api->session->save();

        header("Location: ../index.php?s=$s");
        exit;
}

if($go_info["webftp"]["web_id"] > 0 and $go_info["webftp"]["user"] != '') {
        header("Location: edit/ordner.php?s=$s&&ordner=root&web_id=".$go_info["webftp"]["web_id"]);
        exit;
}


$go_api->uses("isp_web");


$go_api->content->define( array(    main    => "main.htm",
                                            table   => "ispfile_webselect.htm",
                                            stylesheet => "style.css"));


$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "von Till",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "#FFFFFF",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Web Auswahl")."</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                                        SERVERURL => $go_info["server"]["server_url"],
                                                                        WEB_AUSWAHL => $go_api->lng("Web auswählen"),
                                                                        AUSWAHL => $go_api->lng("Auswahl"),
                                                                        ABBRECHEN => $go_api->lng("Abbrechen"),
                                                                        WEB_USERNAME => $go_api->lng("Username"),
                                                                        WEB_PASSWORT => $go_api->lng("Passwort"),
                                                            S => $s
                                    ) );




$group_select = '';
if($mygroups = $go_api->groups->myGroups()){
        foreach($mygroups as $key => $elem) {
    $group_select .= "or groupid = $key ";
        }
}

$sql = "SELECT * from isp_nodes where doctype_id = ".$go_api->isp_web->web_doctype_id." and (userid = ".$go_info["user"]["userid"]." $group_select ) and isp_nodes.status = 1 order by title";
$webnodes = $go_api->db->queryAllRecords($sql);


/**************************************
*  Webs in Dropdownmenü Feld einfügen
**************************************/

        foreach($webnodes as $key => $elem) {
            $node_title = $elem["title"];
        $node_id = $elem["doc_id"];
                if($web_id == '') $web_id = $node_id;
                if($node_id == $web_id) {
                        $node_option .= "<option value=\"$node_id\" selected>$node_title</option>\n";
                } else {
                        $node_option .= "<option value=\"$node_id\">$node_title</option>\n";
                }
        }

        if($web_id > 0) {
                $web = $go_api->db->queryOneRecord("SELECT * FROM isp_dep, isp_isp_user WHERE isp_dep.child_doc_id = isp_isp_user.doc_id and isp_dep.child_doctype_id = 1014 and isp_dep.parent_doc_id = $web_id and isp_dep.parent_doctype_id = 1013 and isp_isp_user.user_admin = 1");
                if(is_array($web)) $user = $web["user_username"];
        }


$go_api->content->assign( array( WEBS => $node_option,
                                                                 USER => $user));

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>