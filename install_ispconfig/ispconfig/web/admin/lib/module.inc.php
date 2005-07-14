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
#######################################################
# modul Informationen
#######################################################
$go_info["modul"]["menu"] = "";
                                                                             
$go_info["modul"]["element_types"] = array( deck => "Ebene",
                                            shortText => "kleines Textfeld",
                                            longText => "grosses Textfeld",
                                            optionField => "Optionsfeld",
                                            dateField => "Datumsfeld",
                                            attachField => "Dokumentenabhängigkeit",
                                            checkboxField => "Checkbox",
                                            seperatorField => "Trennstrich",
                                            descField => "Beschreibung",
                                            fileField => "Dateifeld",
                                            linkField => "Hyperlink",
                                            doubleField => "Währungsfeld",
                                            integerField => "Ganzzahlen Feld",
                                            terminField => "Termin",
                                            pluginField => "Plugin",
                                            imageField => "*Bildfeld",
                                            messageField => "*Messenger",
                                            workflowField => "*Workflow"
                                            );

$go_info["modul"]["table_name"]        = "sys";

$go_info["modul"]["name"]              = "sys";
$go_info["modul"]["title"]             = "Administration";
$go_info["modul"]["include_dir"]       = $go_info["server"]["include_root"];
$go_info["modul"]["template_dir"]      = $go_info["server"]["template_root"];
$go_info["modul"]["lang_dir"]          = "";
$go_info["modul"]["version"]           = "";

$go_info["modul"]["sidenav"]    = "tree" //flat oder tree

?>