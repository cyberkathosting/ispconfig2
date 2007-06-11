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

$tid = intval($tid);

$go_api->content->define( array(    main    => "main.htm",
                                            table   => "hilfe_view_ticket.htm",
                                            stylesheet => "style.css"));

$go_api->content->define_dynamic ( "liste", "table" );

$go_api->content->assign( array(    TITLE => $go_api->lng("Unterstützungskarten"),
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "Tribal-Dolphin",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "#FFFFFF",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Unterstützungskarten")."</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                            CHARSET => $go_info["theme"]["charset"],
                                                                        SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s,
                                                                        SUBJECT => $go_api->lng("Betreff"),
                                                                        PRIORITY => $go_api->lng("Priorität"),
                                                                        DATE => $go_api->lng("Datum"),
                                                                        FROM => $go_api->lng("Davon")
                                                ) );

// First Post
$sql = "SELECT * FROM help_tickets WHERE doc_id=$tid";
$first_ticket = $go_api->db->queryOneRecord($sql);
// Priority
switch($first_ticket["ticket_urgency"]) {
        case "1" : $TPRIORITY = $go_api->lng("Hoch"); break;
        case "3" : $TPRIORITY = $go_api->lng("Mittel"); break;
        case "5" : $TPRIORITY = $go_api->lng("Niedrig"); break;
}
// Name of Poster
$from_usertype = $go_api->auth->user_type($first_ticket["ticket_from"]);
if($from_usertype == "admin") {
  $TFROM = "Admin";
} elseif($from_usertype == "client") {
  $sql = "SELECT kunde_vorname,kunde_name FROM isp_isp_kunde WHERE webadmin_userid = ".$first_ticket["ticket_from"];
  $from_ident = $go_api->db->queryOneRecord($sql);
  $TFROM = $from_ident["kunde_vorname"]." ".$from_ident["kunde_name"];
} else {
  $sql = "SELECT vorname,name FROM isp_isp_reseller WHERE reseller_userid = ".$first_ticket["ticket_from"];
  $from_ident = $go_api->db->queryOneRecord($sql);
  $TFROM = $from_ident["vorname"]." ".$from_ident["name"];
}
// Assign Data For Ticket
$go_api->content->assign( array(    TSUBJECT => $first_ticket["ticket_subject"],
                                                                        TPRIORITY => $TPRIORITY
                                                ) );
// Assing Dynamic content
$vtemp = array();
if($go_info["user"]["language"] == "en") {
          $tannee = substr($first_ticket["ticket_date"],0,4); $tmois = substr($first_ticket["ticket_date"],5, 2); $tjour = substr($first_ticket["ticket_date"],8,2);
          $theure = substr($first_ticket["ticket_date"],11,8);
          $vtemp["TDATE"] = $tmois."/".$tjour."/".$tannee." ".$theure;
} else {
          $tannee = substr($first_ticket["ticket_date"],0,4); $tmois = substr($first_ticket["ticket_date"],5, 2); $tjour = substr($first_ticket["ticket_date"],8,2);
          $theure = substr($first_ticket["ticket_date"],11,8);
          $vtemp["TDATE"] = $tjour."/".$tmois."/".$tannee." ".$theure;
}
$vtemp["TFROM"] = $TFROM;
$vtemp["TMESS"] = stripslashes($first_ticket["ticket_message"]);

$go_api->content->assign($vtemp);
$go_api->content->parse(LISTE,".liste");

// Other Post
$other_tickets = array();

$sql = "SELECT ticket_from,ticket_date,ticket_message FROM help_tickets WHERE ticket_reply=$tid ORDER BY ticket_date";
$other_ticket = $go_api->db->queryAllRecords($sql);
// Assign Data of Posts
if(is_array($other_ticket)) {
        foreach($other_ticket as $row) {
                // Name of Poster
                $from_usertype = $go_api->auth->user_type($row["ticket_from"]);
                if($from_usertype == "admin") {
                        $vtemp["TFROM"] = "Admin";
                } elseif($from_usertype == "client") {
                        $sql = "SELECT kunde_vorname,kunde_name FROM isp_isp_kunde WHERE webadmin_userid = ".$row["ticket_from"];
                        $from_ident = $go_api->db->queryOneRecord($sql);
                        $vtemp["TFROM"] = $from_ident["kunde_vorname"]." ".$from_ident["kunde_name"];
                } else {
                        $sql = "SELECT vorname,name FROM isp_isp_reseller WHERE reseller_userid = ".$row["ticket_from"];
                        $from_ident = $go_api->db->queryOneRecord($sql);
                        $vtemp["TFROM"] = $from_ident["vorname"]." ".$from_ident["name"];
                }
                if($go_info["user"]["language"] == "en") {
                  $tannee = substr($row["ticket_date"],0,4); $tmois = substr($row["ticket_date"],5, 2); $tjour = substr($row["ticket_date"],8,2);
                  $theure = substr($row["ticket_date"],11,8);
                  $vtemp["TDATE"] = $tmois."/".$tjour."/".$tannee." ".$theure;
                } else {
                  $tannee = substr($row["ticket_date"],0,4); $tmois = substr($row["ticket_date"],5, 2); $tjour = substr($row["ticket_date"],8,2);
                  $theure = substr($row["ticket_date"],11,8);
                  $vtemp["TDATE"] = $tjour."/".$tmois."/".$tannee." ".$theure;
                }
                $vtemp["TMESS"] = $row["ticket_message"];

                $go_api->content->assign($vtemp);
                   $go_api->content->parse(LISTE,".liste");
          }
}

// Aff Form if ticket isn't closed
if($first_ticket["ticket_status"] != "C") {
        $RESPOND = "
<script language=\"JavaScript\">
function checkform(theForm) {
  if (document.forms[0].elements[0].value == '')
  {
    alert(\"".$go_api->lng("Geben Sie einen Wert in das Feld")." '".$go_api->lng("Ihre antwort")."'.\");
    document.forms[0].elements[2].focus();
    return (false);
  }
  return true;
}
//-->
</script>
<hr size=\"1\" noshade>
<table width=\"100%\" cellspacing=\"15\">
  <tr><td>
  <form name=\"form1\" onsubmit=\"return checkform(this)\" method=\"post\" action=\"reply_send.php\">
        <table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">
          <tr>
            <td class=\"t2\">&nbsp;".$go_api->lng("Ihre antwort").":</td>
                        <td><textarea name=\"answer\" cols=\"30\" rows=\"5\" wrap=\"physical\"></textarea></td>
          </tr>
          <tr>
            <td colspan=\"2\" align=\"center\"><input name=\"s\" type=\"hidden\" value=\"".$s."\"><hr size=\"1\" noshade><input name=\"tid\" type=\"hidden\" value=\"".$tid."\"></td>
          </tr>
          <tr>
            <td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"Submit\" value=\"".$go_api->lng("Antworten")."\" class=\"button\"> <input type=\"submit\" name=\"Submit\" value=\"".$go_api->lng("Antworten")."&".$go_api->lng("Schließen")."\" class=\"button\"> <input type=\"submit\" name=\"Submit\" OnClick=\"this.form.answer.value='*';\" value=\"".$go_api->lng("Schließen")."\" class=\"button\"></td>
          </tr>
        </table>
        </form>
</td></tr>
</table>";
$go_api->content->assign( array(    RESPOND => $RESPOND
                                                                        ) );

}

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();

exit;
?>