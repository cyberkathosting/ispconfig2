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

$go_api->uses("multidoc");

$go_api->content->define( array(
                            main    => "main.htm",
                            table   => "admin_news_list.htm",
                            stylesheet => "style.css"));

$go_api->content->define_dynamic ( "news", "table" );

if ($page == "") $page = 0;

$von = $page * 30;
$bis = $von + 30;
$lastpage = $page - 1;
$nextpage = $page + 1;
$x = 0;

$go_api->content->assign( array( TITLE => "$session_site News",
                                                SESSION => $session,
                                                BACKGROUND_GIF => "",
                                                COPYRIGHT => "von Till",
                                                FGCOLOR => "$session_nav_hcolour",
                                                TABLE_H_COLOR => "$session_page_hcolour",
                                                BOXSIZE => "450",
                                                WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; News von $von bis $bis</font>",
                                                SITENAME => "$session_site",
                                                DESIGNPATH => $session_design_path,
                                                CHARSET => $go_info["theme"]["charset"],
SERVERURL => $go_info["server"]["server_url"],

                                                S => $s

            ) );



$sql = "SELECT * from sys_news, sys_nodes where sys_news.doc_id = sys_nodes.doc_id and sys_nodes.status = '1' and sys_nodes.doctype_id = 3 order by datum desc";

$go_api->db->query($sql);


if($page > 0) $zurueck = "<a href=\"news_list.php?page=$lastpage&gid=$gid&s=$s\" class=\"t2l\">&lt;&lt; Zur&uuml;ck</a>";
if($go_api->db->numRows() > $bis) $weiter = "<a href=\"news_list.php?page=$nextpage&gid=$gid&s=$s\" class=\"t2l\">Weiter &gt;&gt;</a>";

$go_api->content->assign( array( WEITER => $weiter,
                                 ZURUECK => $zurueck));

$sql .= " LIMIT $von,30";

$go_api->db->query($sql);


while($row = $go_api->db->nextRecord()) {

if($bgcolor == $session_page_hcolour) {
$bgcolor = "#FFFFFF";
} else {
$bgcolor = $session_page_hcolour;
}

$go_api->content->assign( array(    NEWS_TITEL => $row["titel"],
                                    DATUM => date("d.m.Y",$row["datum"]),
                                    DOC_ID => $row["doc_id"],
                                    DOCTYPE_ID => $row["doctype_id"],
                                    BGCOLOR => $bgcolor,
                                    TREE_ID => $row["tree_id"]
                                    ));

$go_api->content->parse(NEWS, ".news");
$x++;

}

if($x == 0) $go_api->content->clear_dynamic("news");

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;

?>