<?


/*
Copyright (c) 2007, projektfarm Gmbh, Till Brehm, Falko Timme
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
                                    table   => "tools_standard.htm",
                                    stylesheet => "style.css"));

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "$session_page_hcolour",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; ".$go_api->lng("Remoting User")."</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                    SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                    ) );

// BEGIN TOOL ###########################################################################

$go_api->auth->check_admin(0);


if(count($_POST) > 1) {

        $uid = intval($_POST["uid"]);
        $username = addslashes($_POST["username"]);
        $passwort = addslashes($_POST["passwort"]);
        $ip = addslashes($_POST["ip"]);
        $dns_query = intval($_POST["dns_query"]);
        $dns_insert = intval($_POST["dns_insert"]);
        $dns_update = intval($_POST["dns_update"]);
        $dns_delete = intval($_POST["dns_delete"]);
        $slave_query = intval($_POST["slave_query"]);
        $slave_insert = intval($_POST["slave_insert"]);
        $slave_update = intval($_POST["slave_update"]);
        $slave_delete = intval($_POST["slave_delete"]);
        $reseller_query = intval($_POST["reseller_query"]);
        $reseller_insert = intval($_POST["reseller_insert"]);
        $reseller_update = intval($_POST["reseller_update"]);
        $reseller_delete = intval($_POST["reseller_delete"]);
        $kunde_query = intval($_POST["kunde_query"]);
        $kunde_insert = intval($_POST["kunde_insert"]);
        $kunde_update = intval($_POST["kunde_update"]);
        $kunde_delete = intval($_POST["kunde_delete"]);
        $web_query = intval($_POST["web_query"]);
        $web_insert = intval($_POST["web_insert"]);
        $web_update = intval($_POST["web_update"]);
        $web_delete = intval($_POST["web_delete"]);

        if($uid > 0) {

                // checke ob Username bereits vergeben
                $tmp = $go_api->db->queryOneRecord("SELECT count(*) as anzahl FROM remote_user WHERE username = '$username' and ruserid != $uid");
                if($tmp["anzahl"] > 0) $go_api->errorMessage("Username bereits vergeben.");

                $sql = "UPDATE remote_user SET username = '$username', ip = '$ip', dns_query = $dns_query, dns_insert = $dns_insert, dns_update = $dns_update, dns_delete = $dns_delete, slave_query = $slave_query, slave_insert = $slave_insert, slave_update = $slave_update, slave_delete = $slave_delete, reseller_query = $reseller_query, reseller_insert = $reseller_insert, reseller_update = $reseller_update, reseller_delete = $reseller_delete, kunde_query = $kunde_query, kunde_insert = $kunde_insert, kunde_update = $kunde_update, kunde_delete = $kunde_delete, web_query = $web_query, web_insert = $web_insert, web_update = $web_update, web_delete = $web_delete where ruserid = $uid";
                $go_api->db->query($sql);
                if($passwort != '') {
                        $sql = "UPDATE remote_user SET passwort = md5('$passwort') WHERE ruserid = $uid";
                        $go_api->db->query($sql);
                }
        } else {

                // checke ob Username bereits vergeben
                $tmp = $go_api->db->queryOneRecord("SELECT count(*) as anzahl FROM remote_user WHERE username = '$username'");
                if($tmp["anzahl"] > 0) $go_api->errorMessage("Username bereits vergeben.");

                $sql = "INSERT INTO remote_user (username,passwort,ip,dns_query,dns_insert,dns_update,dns_delete,slave_query,slave_insert,slave_update,slave_delete,reseller_query,reseller_insert,reseller_update,reseller_delete,kunde_query,kunde_insert,kunde_update,kunde_delete,web_query,web_insert,web_update,web_delete) VALUES ('$username',md5('$passwort'),'$ip',$dns_query,$dns_insert,$dns_update,$dns_delete,$slave_query,$slave_insert,$slave_update,$slave_delete,$reseller_query,$reseller_insert,$reseller_update,$reseller_delete,$kunde_query,$kunde_insert,$kunde_update,$kunde_delete,$web_query,$web_insert,$web_update,$web_delete)";
                $go_api->db->query($sql);
        }

        header("Location: userlist.php?s=$s");
        exit;
} else {
        $uid = intval($_GET["uid"]);
        $user = array();
        if($uid > 0) {
                $user = $go_api->db->queryOneRecord("SELECT * FROM remote_user WHERE ruserid = $uid");
        }
}


$dns_query = ($user["dns_query"] == 1)?'CHECKED':'';
$dns_insert = ($user["dns_insert"] == 1)?'CHECKED':'';
$dns_update = ($user["dns_update"] == 1)?'CHECKED':'';
$dns_delete = ($user["dns_delete"] == 1)?'CHECKED':'';
$slave_query = ($user["slave_query"] == 1)?'CHECKED':'';
$slave_insert = ($user["slave_insert"] == 1)?'CHECKED':'';
$slave_update = ($user["slave_update"] == 1)?'CHECKED':'';
$slave_delete = ($user["slave_delete"] == 1)?'CHECKED':'';
$reseller_query = ($user["reseller_query"] == 1)?'CHECKED':'';
$reseller_insert = ($user["reseller_insert"] == 1)?'CHECKED':'';
$reseller_update = ($user["reseller_update"] == 1)?'CHECKED':'';
$reseller_delete = ($user["reseller_delete"] == 1)?'CHECKED':'';
$kunde_query = ($user["kunde_query"] == 1)?'CHECKED':'';
$kunde_insert = ($user["kunde_insert"] == 1)?'CHECKED':'';
$kunde_update = ($user["kunde_update"] == 1)?'CHECKED':'';
$kunde_delete = ($user["kunde_delete"] == 1)?'CHECKED':'';
$web_query = ($user["web_query"] == 1)?'CHECKED':'';
$web_insert = ($user["web_insert"] == 1)?'CHECKED':'';
$web_update = ($user["web_update"] == 1)?'CHECKED':'';
$web_delete = ($user["web_delete"] == 1)?'CHECKED':'';

$html_out = '
<form action="useredit.php" method="POST">
<table width="444" border="0" cellspacing="4" cellpadding="0">
  <tr>
    <td width="104">&nbsp;</td>
    <td width="340">&nbsp;</td>
  </tr>
  <tr>
    <td class="t2">&nbsp;'.$go_api->lng("Username").':</td>
    <td class="t2"><input type="text" name="username" value="'.$user["username"].'" class="text"></td>
  </tr>
  <tr>
    <td class="t2">&nbsp;'.$go_api->lng("Passwort").':</td>
    <td class="t2"><input type="password" name="passwort" value="" class="text"></td>
  </tr>
  <tr>
    <td class="t2">&nbsp;'.$go_api->lng("IP-Adresse").':</td>
    <td class="t2"><input type="text" name="ip" value="'.$user["ip"].'" class="text"></td>
  </tr>
  <tr>
    <td class="t2">&nbsp;</td>
    <td class="t2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="t2b">&nbsp;'.$go_api->lng("Berechtigungen").'</td>
  </tr>
  <tr bgcolor="'.$session_nav_hcolour.'">
    <td colspan="2" height="1"><img src="../../../'.$session_design_path.'/x.gif"></td>
  </tr>
  <tr>
    <td colspan="2" class="t2b">&nbsp;'.$go_api->lng("txt_primary_dns_records").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="dns_query" value="1" '.$dns_query.'>&nbsp;'.$go_api->lng("DNS-Query").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="dns_insert" value="1" '.$dns_insert.'>&nbsp;'.$go_api->lng("DNS-Insert").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="dns_update" value="1" '.$dns_update.'>&nbsp;'.$go_api->lng("DNS-Update").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="dns_delete" value="1" '.$dns_delete.'>&nbsp;'.$go_api->lng("DNS-Delete").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2b">&nbsp;'.$go_api->lng("txt_secondary_dns_records").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="slave_query" value="1" '.$slave_query.'>&nbsp;'.$go_api->lng("slave_query").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="slave_insert" value="1" '.$slave_insert.'>&nbsp;'.$go_api->lng("slave_insert").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="slave_update" value="1" '.$slave_update.'>&nbsp;'.$go_api->lng("slave_update").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="slave_delete" value="1" '.$slave_delete.'>&nbsp;'.$go_api->lng("slave_delete").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2b">&nbsp;'.$go_api->lng("txt_anbieter").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="reseller_query" value="1" '.$reseller_query.'>&nbsp;'.$go_api->lng("reseller_query").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="reseller_insert" value="1" '.$reseller_insert.'>&nbsp;'.$go_api->lng("reseller_insert").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="reseller_update" value="1" '.$reseller_update.'>&nbsp;'.$go_api->lng("reseller_update").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="reseller_delete" value="1" '.$reseller_delete.'>&nbsp;'.$go_api->lng("reseller_delete").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2b">&nbsp;'.$go_api->lng("txt_kunden").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="kunde_query" value="1" '.$kunde_query.'>&nbsp;'.$go_api->lng("kunde_query").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="kunde_insert" value="1" '.$kunde_insert.'>&nbsp;'.$go_api->lng("kunde_insert").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="kunde_update" value="1" '.$kunde_update.'>&nbsp;'.$go_api->lng("kunde_update").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="kunde_delete" value="1" '.$kunde_delete.'>&nbsp;'.$go_api->lng("kunde_delete").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2b">&nbsp;'.$go_api->lng("txt_webs").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="web_query" value="1" '.$web_query.'>&nbsp;'.$go_api->lng("web_query").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="web_insert" value="1" '.$web_insert.'>&nbsp;'.$go_api->lng("web_insert").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="web_update" value="1" '.$web_update.'>&nbsp;'.$go_api->lng("web_update").'</td>
  </tr>
  <tr>
    <td colspan="2" class="t2">&nbsp;<input type="checkbox" name="web_delete" value="1" '.$web_delete.'>&nbsp;'.$go_api->lng("web_delete").'</td>
  </tr>
  <tr>
    <td class="t2">&nbsp;</td>
    <td class="t2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center">
                <input type="submit" name="speichern" value=" '.$go_api->lng("Speichern").' " class="button"> &nbsp;
                <input type="button" name="loeschen" value=" '.$go_api->lng("Löschen").' " class="button" OnClick="document.location.href='."'".'userdelete.php?uid='.$uid.'&s='.$s."'".'"> &nbsp;
                <input type="button" name="loeschen" value=" '.$go_api->lng("Abbrechen").' " class="button" OnClick="document.location.href='."'".'userlist.php?s='.$s."'".'">
        </td>
  </tr>
  <tr>
    <td class="t2">&nbsp;</td>
    <td class="t2">&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="s" value="'.$s.'">
<input type="hidden" name="uid" value="'.$uid.'">
</form>';

// END TOOL #############################################################################

$go_api->content->assign( array( TOOL => $html_out));
$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>