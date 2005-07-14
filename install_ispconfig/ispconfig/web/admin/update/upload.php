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

$go_api->auth->check_admin(0);

//$go_api->uses("multidoc");

$go_api->content->define( array(
		            main    => "main.htm",
		            table   => "admin_update_upload.htm",
		            stylesheet => "style.css"));

//$go_api->content->define_dynamic ( "updates", "table" );
//$go_api->content->define_dynamic ( "installed", "table" );


$go_api->content->assign( array( TITLE => "$session_site Updates",
						SESSION => $session,
						BACKGROUND_GIF => "",
						COPYRIGHT => "von Till",
						FGCOLOR => "$session_nav_hcolour",
						TABLE_H_COLOR => "$session_page_hcolour",
						BOXSIZE => "450",
						WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#333333\">&nbsp; Updates</font>",
						SITENAME => "$session_site",
						DESIGNPATH => $session_design_path,
                        SERVERURL => $go_info["server"]["server_url"],
						S => $s,
                        ODER => $go_api->lng("oder"),
                        INST => $go_api->lng("Installation starten"),
						DATEI => $go_api->lng("Datei")
                        ) );

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;

?>