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
// include("../../lib/class.FastTemplate.php");


############################################################################
#
#   Template definieren
#
############################################################################

$tpl = new FastTemplate("../../templates");

$tpl->define( array(
                main    => "main.htm",
                table   => "bookmark_neu_ordner.htm",
                stylesheet => "style.css"));

$tpl->define_dynamic ( "ordner", "table" );



$tpl->assign( array( TITLE => "$session_site Startseite",
                                                SESSION => $session,
                                                BACKGROUND_GIF => "",
                                                COPYRIGHT => "von Till",
                                                FGCOLOR => "$session_nav_hcolour",
                                                TABLE_H_COLOR => "$session_page_hcolour",
                                                BOXSIZE => "450",
                                                WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; neuen Ordner hinzufügen</font>",
                                                SITENAME => "$session_site",
                                                DESIGNPATH => $session_design_path,
                                                CHARSET => $go_info["theme"]["charset"],
SERVERURL => $go_info["server"]["server_url"],

                                                S => $s

            ) );

$tree_id = intval($_REQUEST["tree_id"]);
$doc_id = intval($_REQUEST["doc_id"]);
$doctype_id = intval($_REQUEST["doctype_id"]);
$gid = intval($_REQUEST["gid"]);
$userid = intval($_REQUEST["userid"]);


#####################################################################################
#
#  Gruppen in Dropdownmenü Feld einfügen
#
#####################################################################################

$mygroups = $go_api->groups->myGroups();

if($gid == 0) {
$gruppe = "<option value=\"0\" selected>keine Gruppe</option>";
$groupname = "myBookmark";
$ordnerid = "root";
} else {
$gruppe = "<option value=\"0\">keine Gruppe</option>";
}

foreach($mygroups as $key => $elem) {
$name = $elem["name"];
if($key == $gid) {
$gruppe .= "<option value=\"$key\" selected>$name</option>\n";
$groupname = $name;
$ordnerid = "group$key";
} else {
$gruppe .= "<option value=\"$key\">$name</option>\n";
}

}

$tpl->assign( array( GRUPPE => $gruppe));

#####################################################################################
#
#  Ordner in Dropdownmenü Feld einfügen
#
#####################################################################################


$conn = mysql_query("SELECT * FROM bookmark_daten where userid = $userid and art = 'v' and groupid = '$gid' order by btext");

$tpl->assign( array( ORDNERID => "$ordnerid",
                                                ORDNERNAME => "$groupname"
                                                ));

$tpl->parse(Ordner,".ordner");


while($DB = mysql_fetch_array($conn)):

$tpl->assign( array( ORDNERID => $DB["id"],
                                                ORDNERNAME => $DB["btext"]
                                                ));

$tpl->parse(Ordner,".ordner");

endwhile;


#########################################################################################

$tpl->parse(STYLESHEET, stylesheet);
$tpl->parse(MAIN, array("table","main"));
$tpl->FastPrint();
exit;
?>