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

?>

<!-- Form Designer //-->
menuDaten.neu(new VerzEintrag('multidoc','root','Form Designer','','',''));
menuDaten.neu(new LinkEintrag('multidoc','Add list','<?echo $go_info["server"]["server_url"]?>/multidoc/admin/doctype_edit.php?<?echo $session?>','seiteFrame','form.gif','DocType Hinzufügen','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('multidoc','Edit list ','<?echo $go_info["server"]["server_url"]?>/multidoc/admin/doctype_list.php?<?echo $session?>','seiteFrame','form.gif','DocType bearbeiten','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('multidoc','Import Formdefinition','<?echo $go_info["server"]["server_url"]?>/multidoc/import/doctype.php?<?echo $session?>','seiteFrame','form.gif','DocType importieren','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('multidoc','Export Formdefinition','<?echo $go_info["server"]["server_url"]?>/multidoc/export/index.php?<?echo $session?>','seiteFrame','form.gif','DocType exportieren','n','<?echo "1&amp;$session"?>'));


menuDaten.neu(new LinkEintrag('multidoc','Import Data','<?echo $go_info["server"]["server_url"]?>/multidoc/import/import.php?<?echo $session?>','seiteFrame','form.gif','DocType importieren','n','<?echo "1&amp;$session"?>'));


<!-- Multidoc List Management //-->
menuDaten.neu(new VerzEintrag('list','root','List & Report Manager','','',''));
menuDaten.neu(new LinkEintrag('list','Add list','<?echo $go_info["server"]["server_url"]?>/multidoc/listadmin/listtype_edit.php?<?echo $session?>','seiteFrame','doc.gif','ListType hinzufügen','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('list','Edit list','<?echo $go_info["server"]["server_url"]?>/multidoc/listadmin/listtype_list.php?<?echo $session?>','seiteFrame','doc.gif','ListType bearbeiten','n','<?echo "1&amp;$session"?>'));

<?
?>