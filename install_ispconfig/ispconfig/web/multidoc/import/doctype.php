<?
include("../../../lib/config.inc.php");
include("../../../lib/session.inc.php");
// include("../../lib/class.FastTemplate.php");

$go_api->content->define( array(    main    => "main.htm",
		                            table   => "multidoc_import_doctype.htm",
		                            stylesheet => "style.css"));
$go_api->content->define_dynamic ( "add", "table" );
$go_api->content->define_dynamic ( "update", "table" );

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
						            SESSION => $session,
						            BACKGROUND_GIF => "",
						            COPYRIGHT => "von Till",
						            FGCOLOR => "$session_nav_hcolour",
						            TABLE_H_COLOR => "$session_page_hcolour",
						            BOXSIZE => "450",
						            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; DocType importieren</font>",
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



$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>