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
include("../../../../lib/config.inc.php");
include("../../../../lib/session.inc.php");

$go_api->content->define(   array(	main    => "main.htm",
                                    table   => "tools_standard.htm",
                                    stylesheet => "style.css"));

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "$session_page_hcolour",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; Local Portscanner</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                    						SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                    						) );

// BEGIN TOOL ###########################################################################

$go_api->auth->check_admin(0);


class tool_portscan {

    function scan($start,$stop)
    {
    $msg = "<div class='t2'>";
    for($i=$start;$i<($stop+1);$i++){
      if($this->_check_tcp('localhost',$i)){
        $port_service = getservbyport($i, "tcp");
        if(empty($port_service)) $port_service = "unknown";
        $msg .= "&nbsp; &nbsp; Port $i (tcp) is open ($port_service)!<br>\n";
      } else {
        $msg .= "";
      }
    }
    $msg .= "</div>";

    return $msg;
    }

    function _check_tcp ($host,$port) {

        $fp = @fsockopen ($host, $port, &$errno, &$errstr, 2);

        if ($fp) {
            return true;
            fclose($fp);
        } else {
            return false;
            fclose($fp);
        }
    }

    function _check_udp ($host,$port) {

        $fp = @fsockopen ('udp://'.$host, $port, &$errno, &$errstr, 2);

        if ($fp) {
            return true;
            fclose($fp);
        } else {
            return false;
            fclose($fp);
        }
    }
}





$ps = new tool_portscan;
$von = intval($HTTP_POST_VARS["von"]);
$bis = intval($HTTP_POST_VARS["bis"]);
if(!empty($von) and !empty($bis)) {

    if($von < 1 or $von > 65000) $von = 1;
    if($bis < 1 or $bis > 65000) $bis = 65000;

    $portscan = $ps->scan($von, $bis);
    $portscan .= "<p>&nbsp;";
} else {
    $von = 1;
    $bis = 5000;
}

$html_out = '&nbsp;<br><form name="form1" method="post" action="">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" class="t2">From:
                    <input name="von" type="text" value="'.$von.'" size="5" class="text">
                    To:
                    <input name="bis" type="text" value="'.$bis.'" size="5" class="text">
                    &nbsp;
                    <input type="submit" name="Submit" value="Search &gt;&gt;" class="button"> </td>
                </tr>
              </table>
            </form><br>&nbsp;<br>';

$html_out = $html_out . $portscan;

// END TOOL #############################################################################

$go_api->content->assign( array( TOOL => $html_out));
$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>