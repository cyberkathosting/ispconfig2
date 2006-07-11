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
include("../../../../lib/config.inc.php");
include("../../../../lib/session.inc.php");

$go_api->content->define(   array(        main    => "main.htm",
                                    table   => "tools_standard.htm",
                                    stylesheet => "style.css"));

$go_api->content->assign( array(    TITLE => "$session_site Startseite",
                                                            SESSION => $session,
                                                            BACKGROUND_GIF => "",
                                                            COPYRIGHT => "",
                                                            FGCOLOR => "$session_nav_hcolour",
                                                            TABLE_H_COLOR => "$session_page_hcolour",
                                                            BOXSIZE => "450",
                                                            WINDOWTITLE => "<font size=\"2\" face=\"Verdana\" color=\"#FFFFFF\">&nbsp; ".$go_api->lng("txt_move_websites")."</font>",
                                                            SITENAME => "$session_site",
                                                            DESIGNPATH => $session_design_path,
                                                            CHARSET => $go_info["theme"]["charset"],
                                                                                    SERVERURL => $go_info["server"]["server_url"],
                                                            S => $s
                                                                                    ) );

// BEGIN TOOL ###########################################################################

$go_api->auth->check_admin(0);


if($_POST['web_doc_id'] > 0 && $_POST['old_customer_doc_id'] > 0 && $_POST['new_customer_doc_id'] > 0){

  // if the site has been moved to the recycle bin in the meantime, issue an error
  $web = $go_api->db->queryOneRecord("SELECT isp_nodes.status FROM isp_nodes, isp_isp_web WHERE isp_nodes.doc_id = '".$_POST['web_doc_id']."' AND isp_nodes.doctype_id = '1013' AND isp_isp_web.doc_id = '".$_POST['web_doc_id']."'");
  if($web['status'] != 1) $go_api->errorMessage('txt_website_deleted_no_move');


  $old_customer = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_kunde WHERE doc_id = ".$_POST['old_customer_doc_id']);
  $old_sys_user = $go_api->db->queryOneRecord("SELECT * FROM sys_user WHERE username = '".trim($old_customer["webadmin_user"])."'");

  // if the new customer has been moved to the recycle bin in the meantime, issue an error
  $new_customer = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_kunde, isp_nodes WHERE isp_isp_kunde.doc_id = ".$_POST['new_customer_doc_id']." AND isp_nodes.doc_id = '".$_POST['new_customer_doc_id']."' AND isp_nodes.doctype_id = '1012'");
  if($new_customer['status'] != 1) $go_api->errorMessage('txt_customer_deleted_no_move');
  $new_sys_user = $go_api->db->queryOneRecord("SELECT * FROM sys_user WHERE username = '".trim($new_customer["webadmin_user"])."'");

  // Website
  $isp_nodes_web = $go_api->db->queryOneRecord("SELECT * FROM isp_nodes WHERE doc_id = '".$_POST['web_doc_id']."' AND doctype_id = '1013' AND userid = '".$old_sys_user['doc_id']."'");
  $go_api->db->query("UPDATE isp_nodes SET userid = '".$new_sys_user['doc_id']."' WHERE doc_id = '".$_POST['web_doc_id']."' AND doctype_id = '1013' AND userid = '".$old_sys_user['doc_id']."'");
  $go_api->db->query("UPDATE isp_dep SET parent_doc_id = '".$_POST['new_customer_doc_id']."' WHERE child_tree_id = '".$isp_nodes_web['tree_id']."' AND child_doctype_id = '1013' AND child_doc_id = '".$_POST['web_doc_id']."' AND parent_doctype_id = '1012' AND parent_doc_id = '".$_POST['old_customer_doc_id']."'");

  // Co-Domains
  if($codomains = $go_api->db->queryAllRecords("SELECT * FROM isp_isp_domain, isp_dep WHERE isp_isp_domain.doc_id = isp_dep.child_doc_id AND isp_isp_domain.doctype_id = isp_dep.child_doctype_id AND isp_dep.parent_doctype_id = '1013' AND isp_dep.parent_doc_id = '".$_POST['web_doc_id']."' AND isp_dep.child_doctype_id = '1015'")){
    foreach($codomains as $codomain){
      $go_api->db->query("UPDATE isp_nodes SET userid = '".$new_sys_user['doc_id']."' WHERE doc_id = '".$codomain['doc_id']."' AND doctype_id = '1015' AND userid = '".$old_sys_user['doc_id']."'");
    }
  }
  unset($codomains);

  // Users
  if($users = $go_api->db->queryAllRecords("SELECT * FROM isp_isp_user, isp_dep WHERE isp_isp_user.doc_id = isp_dep.child_doc_id AND isp_isp_user.doctype_id = isp_dep.child_doctype_id AND isp_dep.parent_doctype_id = '1013' AND isp_dep.parent_doc_id = '".$_POST['web_doc_id']."' AND isp_dep.child_doctype_id = '1014'")){
    foreach($users as $user){
      $go_api->db->query("UPDATE isp_nodes SET userid = '".$new_sys_user['doc_id']."' WHERE doc_id = '".$user['doc_id']."' AND doctype_id = '1014' AND userid = '".$old_sys_user['doc_id']."'");
    }
  }
  unset($users);

  // Databases
  if($databases = $go_api->db->queryAllRecords("SELECT * FROM isp_isp_datenbank, isp_dep WHERE isp_isp_datenbank.doc_id = isp_dep.child_doc_id AND isp_isp_datenbank.doctype_id = isp_dep.child_doctype_id AND isp_dep.parent_doctype_id = '1013' AND isp_dep.parent_doc_id = '".$_POST['web_doc_id']."' AND isp_dep.child_doctype_id = '1029'")){
    foreach($databases as $database){
      $go_api->db->query("UPDATE isp_nodes SET userid = '".$new_sys_user['doc_id']."' WHERE doc_id = '".$database['doc_id']."' AND doctype_id = '1029' AND userid = '".$old_sys_user['doc_id']."'");
    }
  }
  unset($databases);



  $html_out = '<p align="center">'.$go_api->lng('txt_website_moved').'</p>';

} else {
  // select websites that are currently not in the recycle bin
  $websites = $go_api->db->queryAllRecords("SELECT * FROM isp_isp_web, isp_nodes WHERE isp_nodes.doc_id = isp_isp_web.doc_id AND isp_nodes.doctype_id = isp_isp_web.doctype_id AND isp_nodes.status = '1' AND isp_isp_web.status = ''");

  if($_POST['web_doc_id'] > 0){
    $customer = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_kunde, isp_dep WHERE isp_dep.parent_doc_id = isp_isp_kunde.doc_id AND isp_dep.child_doc_id = ".$_POST['web_doc_id']." AND isp_dep.child_doctype_id = 1013 AND isp_dep.parent_doctype_id = 1012");
    $from = '<tr><td align="center" class="t2">&nbsp;</td></tr>
             <tr><td align="center" class="t2"><b>'.$go_api->lng('txt_move_website_from').':</b> '.$customer['kunde_vorname'].' '.$customer['kunde_vorname'].($customer['kunde_firma'] != '' ? " (".$customer['kunde_firma'].")": "").'<input type="hidden" name="old_customer_doc_id" value="'.$customer['doc_id'].'"></input></td></tr>';

    // select possible new customers (must not be in recycle bin)
    $all_customers = $go_api->db->queryAllRecords("SELECT * FROM isp_isp_kunde, isp_nodes WHERE isp_isp_kunde.doc_id != ".$customer['doc_id']." AND isp_nodes.doc_id = isp_isp_kunde.doc_id AND isp_nodes.doctype_id = isp_isp_kunde.doctype_id AND isp_nodes.status = '1'");
    $to = '<tr><td align="center" class="t2">&nbsp;</td></tr>
           <tr><td align="center" class="t2"><b>'.$go_api->lng('txt_move_website_to').':</b> <select name="new_customer_doc_id"><option value=""></option>';
    if(is_array($all_customers) && !empty($all_customers)){
      foreach($all_customers as $all_customer){
        $to .= '<option value="'.$all_customer['doc_id'].'"'.($_POST['new_customer_doc_id'] == $all_customer['doc_id'] ? " selected" : "").'>'.$all_customer['kunde_vorname'].' '.$all_customer['kunde_vorname'].($all_customer['kunde_firma'] != '' ? " (".$all_customer['kunde_firma'].")": "").'</option>';
      }
    }
    $to .= '</select></td></tr>';
  }

  $html_out = '&nbsp;<br><form name="form1" method="post" action="">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" class="t2"><b>'.$go_api->lng('txt_move_website_website').':</b>
                    <select name="web_doc_id" onChange=\'document.form1.submit()\'>
                      <option value=""></option>';
  if(!empty($websites) && is_array($websites)){
    foreach($websites as $website){
      $html_out .= '<option value="'.$website['doc_id'].'"'.($_POST['web_doc_id'] == $website['doc_id'] ? " selected" : "").'>'.$website['web_host'].($website['web_host'] != '' ? "." : "").$website['web_domain'].'</option>';
    }
  }
  $html_out .= '</select></td>
                </tr>
                '.$from.'
                '.$to.'
                <tr><td align="center" class="t2">&nbsp;</td></tr>
                <tr><td align="center" class="t2"><input type="submit" name="Submit" value="'.$go_api->lng('txt_move_website_submit').'" class="button"></td></tr>
              </table>
            </form><br>&nbsp;<br>';
}



// END TOOL #############################################################################

$go_api->content->assign( array( TOOL => $html_out));
$go_api->content->parse(STYLESHEET, stylesheet);
$go_api->content->parse(MAIN, array("table","main"));
$go_api->content->FastPrint();
exit;
?>