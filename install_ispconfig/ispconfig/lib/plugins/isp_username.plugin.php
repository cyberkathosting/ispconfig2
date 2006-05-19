<?php
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

 #############################################################
 #
 # ISPConfig Plugin
 # Version 1.0
 #
 # Plugin Name: ISP-Username
 # Version: 1.0
 # Autor: Till Brehm
 # Datum: 11.03.2003
 #
 #############################################################
 
 if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class isp_username_plugin {

    function show ($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api,$next_tree_id;
        $sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
        $user_prefix = $sys_config["user_prefix"];

        // Hole Web
        $next_tree_id = strval($next_tree_id);
        $web = $go_api->db->queryOneRecord("SELECT * from isp_nodes, isp_isp_web where isp_nodes.doc_id = isp_isp_web.doc_id and isp_nodes.tree_id = '$next_tree_id'");

        if($user_prefix != '') {
                // Ersetzungen vornehmen
                $user_prefix = str_replace("[WEBID]",$web["doc_id"],$user_prefix);
                $user_prefix = str_replace("[HOST]",$web["web_host"],$user_prefix);
                $user_prefix = str_replace("[DOMAIN]",$web["web_domain"],$user_prefix);
                if($web["web_host"] != '') {
                        $user_prefix = str_replace("[HOSTDOMAIN]",$web["web_host"].".".$web["web_domain"],$user_prefix);
                } else {
                        $user_prefix = str_replace("[HOSTDOMAIN]",$web["web_domain"],$user_prefix);
                }
        }

        /****************************************************
        * Username vorbereiten
        *****************************************************/


        if($doc_id > 0) {
                // Username Editieren
                $user = $go_api->db->queryOneRecord("SELECT * FROM isp_isp_user where doc_id = $doc_id");
                $username = $user["user_username"];
                $out_user = $username;

        } else {
                // neuer Username
                if($user_prefix != '') {
                        // Mit User-Prefix
                        $len_textbox = ((30 - strlen($user_prefix)) < 10)? 10: 29 - strlen($user_prefix);
                        $out_user = $user_prefix.'<input type="text" name="plugin[user]" size="'.$len_textbox.'" maxlength="'.(32 - strlen($user_prefix)).'" class="text">';
                } else {
                        //Ohne User-Prefix
                        $out_user = $user_prefix.'<input type="text" name="plugin[user]" size="30" maxlength="32" class="text">';
                }
        $go_api->renderer->element_jscript_nummer++;
        }

        /****************************************************
        * Emailadresse vorbereiten
        *****************************************************/

        //if($go_api->db->queryOneRecord("SELECT * FROM isp_isp_domain, isp_dep, isp_nodes WHERE isp_isp_domain.doc_id = isp_dep.child_doc_id AND isp_isp_domain.doctype_id = isp_dep.child_doctype_id AND isp_dep.parent_doctype_id = ".$web['doctype_id']." AND isp_dep.parent_doc_id = ".$web['doc_id']." AND isp_dep.child_doctype_id = '1015' AND (isp_isp_domain.domain_host = '' OR isp_isp_domain.domain_host IS NULL) AND isp_nodes.doc_id = isp_dep.parent_doc_id AND isp_nodes.doctype_id = '1015' AND isp_nodes.status != '0' AND isp_dep.child_tree_id = isp_nodes.tree_id")){
          $out_email = '<input type="text" name="plugin[email]" size="10" maxlength="32" class="text" value="'.$user["user_email"].'">@'.$web["web_domain"];
        //} else {
        //  $out_email = '<input type="text" name="plugin[email]" size="10" maxlength="32" class="text" value="'.$user["user_email"].'">@'.$web["web_host"].".".$web["web_domain"];
        //}
        $go_api->renderer->element_jscript_nummer++;

        $html_out = '<table width="100%" bgcolor="#EEEEEE">
           <tr>
         <td width="30%" class="normal" valign="top"><nobr><b>&nbsp;'.$go_api->lng("Email Adresse").':</b></nobr></td>
         <td width="70%" class="tbox">'.$out_email.'</td>
        </tr>
       <tr>
         <td width="30%" class="normal" valign="top"><nobr><b>&nbsp;'.$go_api->lng("Username").':</b></nobr></td>
         <td width="70%" class="tbox">'.$out_user.'</td>
        </tr>
        </table>';

        return $html_out;
    }


    function insert($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api,$HTTP_POST_VARS,$next_tree_id;
        $plugin_vars = $HTTP_POST_VARS["plugin"];
        $username = strtolower($plugin_vars["user"]);
            $email = $plugin_vars["email"];

        $sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
        $user_prefix = $sys_config["user_prefix"];

        if($user_prefix != '') {
                // Bestimme web Doc ID
                $next_tree_id = strval($next_tree_id);
                $web = $go_api->db->queryOneRecord("SELECT * from isp_nodes, isp_isp_web where isp_nodes.doc_id = isp_isp_web.doc_id and isp_nodes.tree_id = '$next_tree_id'");

                // Ersetzungen vornehmen
                $user_prefix = str_replace("[WEBID]",$web["doc_id"],$user_prefix);
                $user_prefix = str_replace("[HOST]",$web["web_host"],$user_prefix);
                $user_prefix = str_replace("[DOMAIN]",$web["web_domain"],$user_prefix);
                if($web["web_host"] != '') {
                        $user_prefix = str_replace("[HOSTDOMAIN]",$web["web_host"].".".$web["web_domain"],$user_prefix);
                } else {
                        $user_prefix = str_replace("[HOSTDOMAIN]",$web["web_domain"],$user_prefix);
                }
        }
        // Überprüfung Username mittels regex, bei Fehler löschen
        if(!preg_match("/^[a-z][\w\.\-\_]{3,64}$/",$user_prefix . $username)) {
                $go_api->db->query("DELETE from isp_isp_user where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
                $go_api->errorMessage('<b>'.$go_api->lng("Feld").': Username</b><br>'.$go_api->lng("Der Username muss aus min. 4, max 32 Buchstaben oder Zahlen bestehen, wobei er mit einem Buchstaben beginnen muss.") . "<br>&nbsp;<br>");
        }
    // Überprüfung email mittels regex, bei Fehler löschen
        if(!preg_match("/^[a-zA-Z][\w\.\-\_]{0,60}$/",$email)) {
                $go_api->db->query("DELETE from isp_isp_user where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
                $go_api->errorMessage('<b>'.$go_api->lng("Feld").': Email</b><br>'.$go_api->lng("Die Emailadresse muss aus min. 1, max 60 Buchstaben oder Zahlen bestehen.") . "<br>&nbsp;<br>");
        }
    // Nur Username + Email reinschreiben, überprüfung erfolgt durch doctype_event
        $go_api->db->query("UPDATE isp_isp_user SET user_username = '$user_prefix$username', user_email = '$email' where doc_id = $doc_id");

    return true;
    }

    function update($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api, $HTTP_POST_VARS, $next_tree_id, $form_changed;
        $plugin_vars = $HTTP_POST_VARS["plugin"];
    $email = $plugin_vars["email"];
        $form_changed = 1;

    // Überprüfung email mittels regex, bei Fehler löschen
        if(!preg_match("/^[a-zA-Z][\w\.\-\_]{0,60}$/",$email)) {
        $go_api->db->query("UPDATE isp_isp_user SET user_email = user_username where doc_id = $doc_id");
                //$go_api->db->query("DELETE from isp_isp_user where doc_id = '$doc_id'");
        //$go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
                $go_api->errorMessage('<b>'.$go_api->lng("Feld").': Email</b><br>'.$go_api->lng("Die Emailadresse muss aus min. 1, max 60 Buchstaben oder Zahlen bestehen.") . "<br>&nbsp;<br>");
        }
    // Nur Username + Email reinschreiben, überprüfung erfolgt durch doctype_event
        $go_api->db->query("UPDATE isp_isp_user SET user_email = '$email' where doc_id = $doc_id");


    return true;
    }

    function delete($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;

    return true;
    }

    function undelete($doc_id, $doctype_id, $groupid = 0)
    {
    global $go_info, $go_api;

    return true;
    }
}
?>