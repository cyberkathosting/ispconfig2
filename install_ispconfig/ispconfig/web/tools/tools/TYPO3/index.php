<?

/*
Copyright (c) 2007, Film & Farver ApS, Allan Jacobsen
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

include("../../../../lib/config.inc.php");
include("../../../../lib/session.inc.php");

$go_api->content->define(   array(
                                    main    => "main.htm",
                                    table   => "tools_tools_TYPO3.htm",
//                                    table   => "tools_tools_ispbackup.htm",
                                    stylesheet => "style.css"));

$go_api->content->assign( array(    TITLE => "$session_site TYPO3",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "$session_page_hcolour",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("TYPO3 Installation")."</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                            CHARSET => $go_info["theme"]["charset"],
                                    SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s,
                                                            TXT_TYPO3_MYSQL_HOST => $go_api->lng("Mysql host"),
                                                            TXT_TYPO3_MYSQL_USER => $go_api->lng("Mysql Username"),
                                                            TXT_TYPO3_MYSQL_PASSWORT => $go_api->lng("Mysql password"),
                                                            TXT_TYPO3_MYSQL_DB => $go_api->lng("Mysql Database "),
                                                            TXT_TYPO3_INSTALL_PASSWORT => $go_api->lng("Install password"),
                                                            TXT_TYPO3_ADMIN_PASSWORT => $go_api->lng("Admin password"),
                                                            TXT_TYPO3_VERSION => $go_api->lng("TYPO3 Version"),
                                                            TXT_INSTALL_STARTEN => $go_api->lng("Start Install")
                                    ));

// BEGIN TOOL ###########################################################################

// Find and show available versions.
// Find TYPO3 source path
$server = $go_api->db->queryOneRecord("SELECT * from isp_server");
$typo3_path = $server["typo3_script_repository"];
unset($server);
// Find directories in path
if ($handle = opendir($typo3_path)) {
  while (false !== ($file = readdir($handle))) {
    $files[] = $file;
  }
$versions  = '<tr bgcolor="#E0E0E0">
            <td colspan="2" class="t2b"><font color="#666666">&nbsp;TYPO3 Version:</font></td>
          </tr>';
// add version if dummy dir and mysql.dump exists
  foreach ($files as $f) {
    if ((is_readable($typo3_path.$f.'/mysql.dump')) and (is_readable($typo3_path.$f.'/dummy'))) {
$versions .= '<tr>
              <td width="25"> <div align="center"><input name="T3version" value="'.$f.'" type="radio"></div></td>
              <td width="859" class="t2">&nbsp;Version '.$f.'</td>
              </tr>';
    }
  }
}

// If you are admin, then show all webs
$gruppen = $go_api->groups->myGroups();
$webs = '';
if(is_array($gruppen)) {
// you are Admin or Reseller
        foreach($gruppen as $gid => $gval) {
                if($gval["userstatus"] == 1 and $gval["groupstatus"] == 1) {

                $webs .= '<tr bgcolor="#E0E0E0">
            <td colspan="2" class="t2b"><font color="#666666">&nbsp;'.$gval["name"].'</font></td>
          </tr>';
                  $n = 0;
                  // add Webs from Group
                  $web_nodes = $go_api->db->queryAllRecords("SELECT * FROM isp_nodes where doctype_id = 1013 and groupid = $gid");
                          foreach($web_nodes as $wn) {
                                $webs .= '<tr>
                    <td width="25"> <div align="center"><input name="webs" value="webs'.$wn["doc_id"].'" type="radio"></div></td>
                    <td width="859" class="t2">&nbsp;'.$wn["title"].'</td>
                          </tr>';
                                  $n++;
                        }
                        if($n == 0) {
                                // No Web available
                                $webs .= '<tr>
                    <td width="859" colspan="2" class="t2">&nbsp;'.$go_api->lng("Kein Web vorhanden.").'</td>
                          </tr>';
                        }


                }
        }
} else {
// It is a customer

                $webs .= '<tr bgcolor="#E0E0E0">
            <td colspan="2" class="t2b"><font color="#666666">&nbsp;'.$go_api->lng("Web(s)").'</font></td>
          </tr>';
                  $n = 0;
                  // add Webs from Group
                  $web_nodes = $go_api->db->queryAllRecords("SELECT * FROM isp_nodes where doctype_id = 1013 and userid = ".$go_info["user"]["userid"]);
                          foreach($web_nodes as $wn) {
                                $webs .= '<tr>
                    <td width="25"> <div align="center"><input name="webs" value="webs'.$wn["doc_id"].'" type="radio"></div></td>
                    <td width="859" class="t2">&nbsp;'.$wn["title"].'</td>
                          </tr>';
                                  $n++;
                        }
                        if($n == 0) {
                                // No Webs available
                                $webs .= '<tr>
                    <td width="859" colspan="2" class="t2">&nbsp;'.$go_api->lng("Kein Web vorhanden.").'</td>
                          </tr>';
                        }

}


// END TOOL #############################################################################

$go_api->content->assign( array( VERSIONS => $versions));
$go_api->content->assign( array( WEBS => $webs));
$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>