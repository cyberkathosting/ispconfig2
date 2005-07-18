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

if ($groupid = "") $groupid = 0;
$go_api->uses_obj("liste");
$go_api->uses("listadmin");

    if($art == "listtype") {
        if($listtype_id == ""){
	    $listtype_id = $go_api->listadmin->listtype_add($form);
        } else {
        $listtype_id = $go_api->listadmin->listtype_update($listtype_id,$form);
        }
    } elseif ($art == "row") {
        if($row_id == ""){
        $go_api->listadmin->row_add($listtype_id,$form);
        } else {
        $go_api->listadmin->row_update($listtype_id,$row_id,$form);
        }
    } else {
        //$form["type"] = $art;
        if($element_id == "") {
        $go_api->listadmin->element_add($listtype_id,$row_id,$form);
        } else {
        $go_api->listadmin->element_update($listtype_id,$row_id,$element_id,$form);
        }
    }
header("Location: listtype_show.php?id=$listtype_id&$session");
exit;
?>






