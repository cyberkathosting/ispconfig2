<?
include("../../../lib/config.inc.php");
include("../../../lib/session.inc.php");
// include("../../lib/class.FastTemplate.php");

$go_api->content->define( array(    main    => "main.htm",
		                            table   => "multidoc_import_import.htm",
		                            stylesheet => "style.css"));

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
						            SESSION => $session,
						            BACKGROUND_GIF => "",
						            COPYRIGHT => "von Till",
						            FGCOLOR => "$session_nav_hcolour",
						            TABLE_H_COLOR => "$session_page_hcolour",
						            BOXSIZE => "450",
						            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; Daten importieren</font>",
						            SITENAME => "$session_site",
						            DESIGNPATH => $session_design_path,
SERVERURL => $go_info["server"]["server_url"],

                                    DOC_ID => $doc_id,
                                    TREE_ID => $tree_id,
                                    DOCTYPE_ID => $doctype_id,
                                    FILE_ID => $file_id,
                                    CALLER_TREE_ID => $caller_tree_id,
						            S => $s
                                    ) );

$go_api->auth->check_admin(0);

$rows = $go_api->db->queryAllRecords("select * from doctype where doctype_title != 'dummy' order by doctype_modul, doctype_title");
$$doctypes = "";
while (list($key, $row) = each($rows))
    {
    $doctype_id = $row["doctype_id"];
    $doctype_title = $row["doctype_title"];    
    $doctypes .= "<option value=\"$doctype_id\">$doctype_title</option>\r\n";
}
    
$go_api->content->assign( array( DOCTYPES => $doctypes));


$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>