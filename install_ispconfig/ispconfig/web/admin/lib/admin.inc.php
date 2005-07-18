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
<!-- Backup Management //-->

menuDaten.neu(new VerzEintrag('datenbank','root','<? echo $go_api->lng("ISPConfig Datenbank")?>','','',''));
menuDaten.neu(new LinkEintrag('datenbank','<? echo $go_api->lng("Backup erstellen")?>','datenbank/backup.php?<?echo $session?>','seiteFrame','data_copy.gif','Datenbank Backup erstellen','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('datenbank','<? echo $go_api->lng("Datenbank Check")?>','datenbank/check.php?<?echo $session?>','seiteFrame','data_find.gif','Datenbank Check','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('datenbank','<? echo $go_api->lng("Datenbank Optimieren")?>','datenbank/optimize.php?<?echo $session?>','seiteFrame','data_ok.gif','Datenbank Optimieren','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('datenbank','<? echo $go_api->lng("Datenbank Reparieren")?>','datenbank/repair.php?<?echo $session?>','seiteFrame','data_replace.gif','Datenbank Reparieren','n','<?echo "1&amp;$session"?>'));


<!-- Update Management //-->

menuDaten.neu(new VerzEintrag('update','root','<? echo $go_api->lng("Update Manager")?>','','',''));
menuDaten.neu(new LinkEintrag('update','<? echo $go_api->lng("Update")?>','update/upload.php?<?echo $session?>','seiteFrame','lightbulb_on.gif','News Hinzufügen','n','<?echo "1&amp;$session"?>'));
//menuDaten.neu(new LinkEintrag('news','<? echo $go_api->lng("Alle anzeigen")?>','news/news_list.php?<?echo $session?>','seiteFrame','newsgrup.gif','News anzeigen','n','<?echo "1&amp;$session"?>'));

<!-- System Config //-->

menuDaten.neu(new VerzEintrag('sysconf','root','<? echo $go_api->lng("System Config")?>','','',''));
menuDaten.neu(new LinkEintrag('sysconf','<? echo $go_api->lng("Eigenschaften")?>','<?echo $go_info["server"]["server_url"]?>/multidoc/edit/edit.php?doc_id=1&doctype_id=1027&<?echo $session?>','seiteFrame','window_sidebar.gif','System Config','n','<?echo "1&amp;$session"?>'));
//menuDaten.neu(new LinkEintrag('news','<? echo $go_api->lng("Alle anzeigen")?>','news/news_list.php?<?echo $session?>','seiteFrame','newsgrup.gif','News anzeigen','n','<?echo "1&amp;$session"?>'));


<!-- Form Designer //-->

menuDaten.neu(new VerzEintrag('multidoc','root','Form Designer','','',''));
menuDaten.neu(new LinkEintrag('multidoc','Add Form','<?echo $go_info["server"]["server_url"]?>/multidoc/admin/doctype_edit.php?<?echo $session?>','seiteFrame','form.gif','DocType Hinzufügen','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('multidoc','Edit Form','<?echo $go_info["server"]["server_url"]?>/multidoc/admin/doctype_list.php?<?echo $session?>','seiteFrame','form.gif','DocType bearbeiten','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('multidoc','Import Form','<?echo $go_info["server"]["server_url"]?>/multidoc/import/doctype.php?<?echo $session?>','seiteFrame','form.gif','DocType importieren','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('multidoc','Export Form','<?echo $go_info["server"]["server_url"]?>/multidoc/export/index.php?<?echo $session?>','seiteFrame','form.gif','DocType exportieren','n','<?echo "1&amp;$session"?>'));
menuDaten.neu(new LinkEintrag('multidoc','Import Data','<?echo $go_info["server"]["server_url"]?>/multidoc/import/import.php?<?echo $session?>','seiteFrame','form.gif','DocType importieren','n','<?echo "1&amp;$session"?>'));


<??>