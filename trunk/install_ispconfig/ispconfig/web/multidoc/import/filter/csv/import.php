<?
$go_api->uses("doc");

//print_r($HTTP_POST_VARS);


// Öffne Datei
$fd = fopen ($go_info["server"]["temp_dir"].$go_info["server"]["dir_trenner"].$form["filename"], "r");
$x = 0;
$in_sql = '';
$sql_feld = '';
$sql_wert = '';


$doc = $go_api->doc->doctype_get($form["doctype_id"]);
$tabelle = $doc->storage_path;
if($doc->tree == 1) {
    $type = 'i';
} else {
    $type = 'a';
}
$icon = $doc->icon;

unset($doc);

$doctp = $go_api->db->queryOneRecord("SELECT * from doctype where doctype_id = ".$form["doctype_id"]);

$nodes_tabelle = $doctp["doctype_modul"]."_nodes";
unset($doctp);

$parent = $form["ordner"];
$title_idx = $form["title"];

while($line = fgets ($fd, 4096)){

    if($form["zeile_ueberschrift"] == 1 and $x == 0) {
        // erste Zeile nicht verwenden
    } else {
       $csv_felder = explode($form["feld_trenner"],$line);
       //print_r($felder);
       
       
       foreach($feld as $key => $val) {
            if($val["feld"] != "") {
                $feldindex = $val["feld"];
                $sql_feld .= $key.",";
                $wert = $csv_felder[$feldindex];
                
                // funktionen auf Feldinhalt anwenden
                if($val["function"] == "CURRENCY") {
                    $wert = str_replace(",",".",$wert);
                }
                
                
                
                $sql_wert .= "'".$wert."',";
            }
       }
       $sql_feld = substr($sql_feld,0,-1);
       $sql_wert = substr($sql_wert,0,-1);
       $in_sql = "INSERT INTO $tabelle ($sql_feld) VALUES ($sql_wert)";
       
       $title = $csv_felder[$title_idx];
       
       /////////////////////////////////
       // Dynamischen Ordner ermitteln
       /////////////////////////////////
       
       if($form["ordner_dyn_type"] == 'none') {
            $parent = $form["ordner"];
       }
       
       if($form["ordner_dyn_type"] == 'id') {
       
            
            // Check ob Ordner existiert
            $feldindex = $form["ordner_dyn_feld"];
            $check_sql = "SELECT tree_id from $nodes_tabelle where tree_id = '".$csv_felder[$feldindex]."'";
            if($row = $go_api->db->queryOneRecord($check_sql)) {
                $parent = $csv_felder[$feldindex];
            } else {
                $parent = $form["ordner"];
            }
       }
       
       if($form["ordner_dyn_type"] == 'name') {
            // Check ob Ordner existiert
            $feldindex = $form["ordner_dyn_feld"];
            if($row = $go_api->db->queryOneRecord("SELECT tree_id from $nodes_tabelle where title = '".$csv_felder[$feldindex]."'")) {
                $parent = $row["tree_id"];
            } else {
                $parent = $form["ordner"];
            }
       }

       $node_sql = "INSERT INTO $nodes_tabelle (userid,groupid,parent,type,doctype_id,status,icon,doc_id,title) VALUES ('1','0','$parent','$type','".$form["doctype_id"]."','1','$icon',LAST_INSERT_ID(),'$title')";
       
       echo $in_sql . "<br>";
       echo $node_sql . "<br>";
       
       
       $go_api->db->query("LOCK TABLES $tabelle,$nodes_tabelle WRITE");
       $go_api->db->query($in_sql);
       $go_api->db->query($node_sql);
       $go_api->db->query("UNLOCK TABLES");
       
       
    }
    $sql_feld = '';
    $sql_wert = '';
    $in_sql = '';
    //reset($feld);
    $x++;
}
fclose ($fd);
// Temp. Datei löschen
unlink($go_info["server"]["temp_dir"].$go_info["server"]["dir_trenner"].$form["filename"]);








?>