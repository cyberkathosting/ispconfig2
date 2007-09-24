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

                // Selecting all co-domains
                $co_domains = $go_api->db->queryAllRecords("SELECT domain_host, domain_domain FROM isp_dep, isp_isp_domain WHERE isp_dep.parent_doc_id = ".$web["doc_id"]." and isp_dep.parent_doctype_id = ".$web["doctype_id"]." and isp_dep.child_doc_id = isp_isp_domain.doc_id and isp_dep.child_doctype_id = isp_isp_domain.doctype_id and isp_isp_domain.domain_local_mailserver = 1");

                // get the domains, if we are in edit mode
                if($doc_id > 0) {
                        $email_domains = explode("\n",$user["user_emaildomain"]);
                } else {
						$email_domains[0] = ($web["web_host"] != '')?$web["web_host"].'.'.$web["web_domain"]:$web["web_domain"];
                        foreach($co_domains as $co_domain) {
                                $email_domains[] = ($co_domain["domain_host"] != '')?$co_domain["domain_host"].'.'.$co_domain["domain_domain"]:$co_domain["domain_domain"];
                        }
                }

                // now we build the HTML output
                $tmp_domain = ($web["web_host"] != '')?$web["web_host"].'.'.$web["web_domain"]:$web["web_domain"];
                $tmp_checked = (in_array($tmp_domain,$email_domains,false))?' CHECKED':'';
                $out_email = '<table><tr><td><input type="text" name="plugin[email]" size="10" maxlength="32" class="text" value="'.$user["user_email"].'"></td><td><input type="checkbox" name="plugin[email_domain][0]" value="'.$tmp_domain.'"'.$tmp_checked.'>@'.$tmp_domain.'</td></tr>';
                $go_api->renderer->element_jscript_nummer += 2;
                unset($tmp_domain);
                unset($tmp_checked);

                $n = 1;
                foreach($co_domains as $co_domain) {
                        $tmp_domain = ($co_domain["domain_host"] != '')?$co_domain["domain_host"].'.'.$co_domain["domain_domain"]:$co_domain["domain_domain"];
                        $tmp_checked = (in_array($tmp_domain,$email_domains,false))?' CHECKED':'';
                        $out_email .= '<tr><td>&nbsp;</td><td><input type="checkbox" name="plugin[email_domain]['.$n.']" value="'.$tmp_domain.'"'.$tmp_checked.'>@'.$tmp_domain.'</td></tr>';
                        $n++;
                        $go_api->renderer->element_jscript_nummer++;
                }
                unset($tmp_domain);
                unset($tmp_checked);
                $out_email .= '</table>';

        $html_out = '<table width="100%" bgcolor="#EEEEEE">
           <tr>
         <td width="30%" class="normal" valign="top"><nobr><b>&nbsp;'.$go_api->lng("Email Adresse").':</b></nobr></td>
         <td width="70%" class="tbox">'.$out_email.'</td>
        </tr>
                <tr bgcolor="#EEEEEE">
            <td colspan="2" class=""><hr noshade size="1"></td>
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
				
				// Bestimme web Doc ID
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

        // Überprüfung Username mittels regex, bei Fehler löschen
        if(!preg_match("/^[\w\.\-\_]{2,64}$/",$user_prefix . $username)) {
                $go_api->db->query("DELETE from isp_isp_user where doc_id = '$doc_id'");
        $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
                $go_api->errorMessage('<b>'.$go_api->lng("Feld").': Username</b><br>'.$go_api->lng("Der Username muss aus min. 4, max 32 Buchstaben oder Zahlen bestehen, wobei er mit einem Buchstaben beginnen muss.") . "<br>&nbsp;<br>");
        }

                   // Überprüfung email mittels regex, bei Fehler löschen
        if(!preg_match("/^[a-zA-Z0-9][\w\.\-\+\_]{0,60}$/",$email)) {
                $go_api->db->query("DELETE from isp_isp_user where doc_id = '$doc_id'");
                        $go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
                $go_api->errorMessage('<b>'.$go_api->lng("Feld").': Email</b><br>'.$go_api->lng("Die Emailadresse muss aus min. 1, max 60 Buchstaben oder Zahlen bestehen.") . "<br>&nbsp;<br>");
        }

                // Write the email domains
                $email_domain_array = $plugin_vars["email_domain"];

                if(is_array($email_domain_array)) {
                        // check if the domains are really co-domains of this site to prevent attacks
                        $co_domains = $go_api->db->queryAllRecords("SELECT domain_host, domain_domain FROM isp_dep, isp_isp_domain WHERE isp_dep.parent_doc_id = ".$web["doc_id"]." and isp_dep.parent_doctype_id = ".$web["doctype_id"]." and isp_dep.child_doc_id = isp_isp_domain.doc_id and isp_dep.child_doctype_id = isp_isp_domain.doctype_id");
                        $web_domains[0] = ($web["web_host"] != '')?$web["web_host"].'.'.$web["web_domain"]:$web["web_domain"];
                        if( is_array($co_domains) ) {
                              foreach($co_domains as $co_domain) {
                                     $web_domains[] = ($co_domain["domain_host"] != '')?$co_domain["domain_host"].'.'.$co_domain["domain_domain"]:$co_domain["domain_domain"];
                              }
                        }
                        
                        if( is_array($email_domain_array) ) {
                             foreach($email_domain_array as $tmp_domain) {
                                     $tmp_domain = trim($tmp_domain);
                                     if(in_array($tmp_domain,$web_domains)) 
                                             $email_domain_checked[] = $tmp_domain;
                             }
                        }

                        //Don't do addslashes if it is not an array
                        if( !is_array($email_domain_checked) ) {
                        	 $email_domain = '';
                        }
                        else {
                        	$email_domain = addslashes(implode("\n",$email_domain_checked));
                        }
                } 
                else {
                         $email_domain = '';
                }


           // Nur Username + Email reinschreiben, überprüfung erfolgt durch doctype_event
             $go_api->db->query("UPDATE isp_isp_user SET user_username = '$user_prefix$username', user_email = '$email', user_emaildomain = '$email_domain' where doc_id = $doc_id");

            return true;
    }

    function update($doc_id, $doctype_id, $groupid = 0)
    {
                   global $go_info, $go_api, $HTTP_POST_VARS, $next_tree_id, $form_changed;
        $plugin_vars = $HTTP_POST_VARS["plugin"];
            $email = $plugin_vars["email"];
        $form_changed = 1;

                $next_tree_id = strval($next_tree_id);
        $web = $go_api->db->queryOneRecord("SELECT * from isp_nodes, isp_isp_web where isp_nodes.doc_id = isp_isp_web.doc_id and isp_nodes.tree_id = '$next_tree_id'");


            // Überprüfung email mittels regex, bei Fehler löschen
        if(!preg_match("/^[a-zA-Z0-9][\w\.\-\+\_]{0,60}$/",$email)) {
                $go_api->db->query("UPDATE isp_isp_user SET user_email = user_username where doc_id = $doc_id");
                //$go_api->db->query("DELETE from isp_isp_user where doc_id = '$doc_id'");
                //$go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
                $go_api->errorMessage('<b>'.$go_api->lng("Feld").': Email</b><br>'.$go_api->lng("Die Emailadresse muss aus min. 1, max 60 Buchstaben oder Zahlen bestehen.") . "<br>&nbsp;<br>");
        }

                // Write the email domains
                $email_domain_array = $plugin_vars["email_domain"];

                if(is_array($email_domain_array)) {
                        // check if the domains are really co-domains of this site to preventattacaks
                        $co_domains = $go_api->db->queryAllRecords("SELECT domain_host, domain_domain FROM isp_dep, isp_isp_domain WHERE isp_dep.parent_doc_id = ".$web["doc_id"]." and isp_dep.parent_doctype_id = ".$web["doctype_id"]." and isp_dep.child_doc_id = isp_isp_domain.doc_id and isp_dep.child_doctype_id = isp_isp_domain.doctype_id");
                        $web_domains[0] = ($web["web_host"] != '')?$web["web_host"].'.'.$web["web_domain"]:$web["web_domain"];
                        foreach($co_domains as $co_domain) {
                                $web_domains[] = ($co_domain["domain_host"] != '')?$co_domain["domain_host"].'.'.$co_domain["domain_domain"]:$co_domain["domain_domain"];
                        }
                        foreach($email_domain_array as $tmp_domain) {
                                $tmp_domain = trim($tmp_domain);
                                if(in_array($tmp_domain,$web_domains)) $email_domain_checked[] = $tmp_domain;
                        }

                        $email_domain = addslashes(implode("\n",$email_domain_checked));
                } else {
                        $email_domain = '';
                }

            // Nur Username + Email reinschreiben, überprüfung erfolgt durch doctype_event
        $go_api->db->query("UPDATE isp_isp_user SET user_email = '$email', user_emaildomain = '$email_domain' where doc_id = $doc_id");


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