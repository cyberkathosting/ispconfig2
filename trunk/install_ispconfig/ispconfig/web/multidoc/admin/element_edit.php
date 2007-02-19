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

if(!$go_api->auth->check_admin(0,1)) die("Access not permitted.");

$go_api->uses("doc,docadmin");

switch ($art) {
case "deck":
include("element_deck.php");
exit;
case "shortText":
include("element_shorttext.php");
exit;
case "longText":
include("element_longtext.php");
exit;
case "attachField":
include("element_attachdoc.php");
exit;
case "linkField":
include("element_linkfield.php");
exit;
case "doubleField":
include("element_doublefield.php");
exit;
case "integerField":
include("element_integerfield.php");
exit;
case "descField":
include("element_descfield.php");
exit;
case "seperatorField":
include("element_seperatorfield.php");
exit;
case "optionField":
include("element_optionfield.php");
exit;
case "dateField":
include("element_datefield.php");
case "fileField":
include("element_filefield.php");
exit;
case "checkboxField":
include("element_checkboxfield.php");
exit;
case "terminField":
include("element_terminfield.php");
exit;
case "pluginField":
include("element_pluginfield.php");
exit;
}

?>