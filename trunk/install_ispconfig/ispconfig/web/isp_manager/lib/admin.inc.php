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
// Inhaltsverzeichnis des Admin Bereiches
?>

<!-- Multidoc Management //-->

menuDaten.neu(new VerzEintrag('server','root','<? echo $go_api->lng("Server")?>','','',''));
menuDaten.neu(new LinkEintrag('server','<? echo $go_api->lng("Eigenschaften")?>','<?echo $go_info["server"]["server_url"]?>/multidoc/edit/edit.php?doc_id=1&doctype_id=1010&<?echo $session?>','seiteFrame','preferences.gif','Eigenschaften','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('server','<? echo $go_api->lng("Status")?>','<?echo $go_info["server"]["server_url"]?>/multidoc/edit/edit.php?doc_id=3&doctype_id=1021&<?echo $session?>','seiteFrame','shield_green.gif','Status','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('server','<? echo $go_api->lng("Dienste")?>','<?echo $go_info["server"]["server_url"]?>/multidoc/edit/edit.php?doc_id=1&doctype_id=1023&<?echo $session?>','seiteFrame','server_to_client.gif','Dienste','n','<?echo "1&amp;$session"?>'));

<?
?>