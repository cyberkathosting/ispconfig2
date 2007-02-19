<?
include("../../../lib/config.inc.php");
include("../../../lib/session.inc.php");


$go_api->uses("doc");
if($userfile != "none" and isset($userfile)){


$doc = "";

$fp = @fopen ($userfile, "r") or die("cannot open file in readmode");
$filedata = fread ($fp, filesize ($userfile));
fclose($fp);
$data = explode("#::#",$filedata);
if(!list( $doctype_id, $doctype_userid, $doctype_groupid, $doctype_modul, $doctype_name, $doctype_title, $doctype_tree ) = explode(",",$data[0])) die("File Corrupted");
$doctype_title = urldecode($doctype_title);
$doc = @unserialize($data[1]) or die("DocumentType Definition Corrupted");

if($data[2]) {
    $tableDef = @unserialize($data[2]) or die("Table Definition Corrupted");
    $go_api->db->createTable($doc->storage_path,$tableDef);
}

$doc_serialized = serialize($doc);

if($user == 1) {
    $dbarray["userid"] = $doctype_userid;
    $dbarray["groupid"] = $doctype_groupid;
} else {
    $dbarray["userid"] = $userid;
    $dbarray["groupid"] = 0;
}

if($id == 1) {
    $dbarray["doctype_id"] = $doctype_id;
}

$dbarray["doctype_modul"] = $doctype_modul;
$dbarray["doctype_name"] = $doctype_name;
$dbarray["doctype_title"] = $doctype_title;
$dbarray["doctype_def"] = $doc_serialized;
$dbarray["doctype_tree"] = $doctype_tree;

if($update == 1) {
    die("noch nicht implementiert");
} else {
    $go_api->db->insert("doctype",$dbarray);
    echo $go_api->db->errorMessage;
}

// print_r($doc);


}



/*

$vars["title"] = $userfile_name;
$vars["file_size"] = $userfile_size;
$vars["mimetype"] = $userfile_type;
$vars["file_date"] = time();
$vars["parent_doc_id"] = $doc_id;
$vars["parent_doctype_id"] = $doctype_id;
$go_api->db->insert("file_nodes",$vars);
$filename = $go_api->db->insertID();
$pfad = $go_info["server"]["files_dir"];
if(rename($userfile,$pfad.$go_info["server"]["dir_trenner"].$filename.".dat") != 1) die("Die Datei konnte nicht gespeichert werden. Prüfen Sie Berechtigungen und Disc-Quota");
} else {
// updaten
$vars["title"] = $title;
if($file_id != "") $go_api->db->update("file_nodes",$vars,"file_id = '$file_id'");
}

header("Location: edit.php?doctype_id=$doctype_id&tree_id=$tree_id&doc_id=$doc_id&$session");

*/
?>