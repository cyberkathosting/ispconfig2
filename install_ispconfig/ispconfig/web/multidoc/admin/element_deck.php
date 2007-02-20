<?php
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

if(CONFIG_LOADED != 1) die('Direct access not permitted.');

if(!$go_api->auth->check_admin(0,1)) die("Access not permitted.");

$go_api->content->define( array(
                            main    => "main.htm",
                            table   => "multidoc_admin_deck_edit.htm",
                            stylesheet => "style.css"));

$go_api->content->assign( array( TITLE => "$session_site Startseite",
                                                SESSION => $session,
                                                BACKGROUND_GIF => "",
                                                COPYRIGHT => "von Till",
                                                FGCOLOR => "$session_nav_hcolour",
                                                TABLE_H_COLOR => "$session_page_hcolour",
                                                BOXSIZE => "450",
                                                WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; Ebene bearbeiten/erstellen</font>",
                                                SITENAME => "$session_site",
                                                DESIGNPATH => $session_design_path,
                                                CHARSET => $go_info["theme"]["charset"],
SERVERURL => $go_info["server"]["server_url"],

                                                S => $s

            ) );

if(isset($id)){

$doc = $go_api->doc->doctype_get($doctype_id);;

if($doc->deck[$id]->visible == 1){
$invisible = '';
} else {
$invisible = 'selected';
}

if($doc->deck[$id]->perm_read == 'r') $perm_read_read = 'selected';
if($doc->deck[$id]->perm_read == 'w') $perm_read_write = 'selected';
if($doc->deck[$id]->perm_read == 'a') $perm_read_admin = 'selected';

if($doc->deck[$id]->perm_write == 'r') $perm_write_read = 'selected';
if($doc->deck[$id]->perm_write == 'w') $perm_write_write = 'selected';
if($doc->deck[$id]->perm_write == 'a') $perm_write_admin = 'selected';



$go_api->content->assign( array( DECK_TITLE => $doc->deck[$id]->title,
                                         DECK_ID => $id,
                     DOCTYPE_ID => $doctype_id,
                     INVISIBLE => $invisible,
                     PERM_READ_READ => $perm_read_read,
                     PERM_READ_WRITE => $perm_read_write,
                     PERM_READ_ADMIN => $perm_read_admin,
                     PERM_WRITE_READ => $perm_write_read,
                     PERM_WRITE_WRITE => $perm_write_write,
                     PERM_WRITE_ADMIN => $perm_write_admin
                                         ));

} else {
$go_api->content->assign( array( DOCTYPE_ID => $doctype_id));
$go_api->content->no_strict();
}


$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>