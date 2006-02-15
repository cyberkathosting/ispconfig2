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
$go_api->uses("doc");

$go_api->content->define( array(    main    => "main.htm",
                                            table   => "multidoc_import_filter_csv_felder.htm",
                                            stylesheet => "style.css"));
$go_api->content->define_dynamic ( "felder", "table" );

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "von Till",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "$session_page_hcolour",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; CSV Daten importieren</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                            CHARSET => $go_info["theme"]["charset"],
                                                                        SERVERURL => $go_info["server"]["server_url"],
                                    DOC_ID => $doc_id,
                                    TREE_ID => $tree_id,
                                    DOCTYPE_ID => $doctype_id,
                                    FILE_ID => $file_id,
                                    CALLER_TREE_ID => $caller_tree_id,
                                                            S => $s
                                    ) );

// Kopiere Uploaded File

$filename = '';
if (is_uploaded_file($HTTP_POST_FILES['userfile']['tmp_name'])) {
   $filename = $s."_".$HTTP_POST_FILES['userfile']['name'];
   copy($HTTP_POST_FILES['userfile']['tmp_name'], $go_info["server"]["temp_dir"].$go_info["server"]["dir_trenner"].$filename);
} else {
   die("Possible file upload attack. Filename: " . $HTTP_POST_FILES['userfile']['name']);
}

// Öffne Datei
$fd = fopen ($go_info["server"]["temp_dir"].$go_info["server"]["dir_trenner"].$filename, "r");
$data = fread ($fd, filesize ($go_info["server"]["temp_dir"].$go_info["server"]["dir_trenner"].$filename));
fclose ($fd);

// Parse Datei
// Splitte Zeilen
//
$zeilen_trenner = trim($zeilen_trenner);
$zeilen = explode("\n",$data);
// Splitte erste Zeile in Felder
$felder = explode($feld_trenner,$zeilen[0]);

$csv_felder = "";
foreach($felder as $key => $feld) {
    $csv_felder .= "<option value=\"$key\">$feld</option>\r\n";
}

$doc = $go_api->doc->doctype_get($doctype_id);


// Fülle Ordner Dropdown
$nodes_table = $doc->modul."_nodes";
//die(print_r($doc));
//die("SELECT * from $nodes_table where userid = ".$go_info["user"]["userid"]." and type = 'n'");

$nodes = $go_api->db->queryAllRecords("SELECT * from $nodes_table where userid = ".$go_info["user"]["userid"]." and groupid = 0 and type = 'n'");
$ordner = '';
//die($nodes);
if(is_array($nodes)) {
        foreach($nodes as $node) {
                $ordner .= "<option value=\"".$node["tree_id"]."\">".$node["title"]."</option>\r\n";
        }
}

$go_api->content->assign( array( CSV_FELDER => $csv_felder,
                                 FILENAME => $filename,
                                 FELD_TRENNER => $feld_trenner,
                                 ZEILEN_TRENNER => $zeilen_trenner,
                                 ZEILE_UEBERSCHRIFT => $zeile_ueberschrift,
                                                                 ORDNER => $ordner));


$doc_felder = "";

foreach($doc->deck as $deck) {
    if(is_array($deck->elements)) {
    foreach($deck->elements as $feld) {

        $name = trim($feld->name);
        $title = trim($feld->title);
        if($title != "") {
        $go_api->content->assign( array( FELD_NAME => $name,
                                         FELD_TITLE => $title));

        $go_api->content->parse(Felder, ".felder");
        }

    }
    }
}
//die($doc_felder);
//$go_api->content->assign( array( DOC_FELDER => $doc_felder));

$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>