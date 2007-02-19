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
require_once('../../../lib/config.inc.php');
require_once('../lib/app.inc.php');

$app->uses('tpl,pop3');
$app->tpl->newTemplate("login.tpl.htm");

if(count($_POST) > 1) {
        if($_POST["username"] != '' and $_POST["passwort"] != '') {

                $username         = $_POST["username"];
                $passwort         = $_POST["passwort"];

                // Checke, ob es den User in ISPConfig DB gibt
                $user = $app->db->queryOneRecord("SELECT * FROM isp_isp_user WHERE user_username = '".addslashes($username)."'");

                if($user["doc_id"] > 0) {
                  // Hole das Web des Users
                  $web = $app->db->queryOneRecord("SELECT isp_isp_web.web_mailuser_login FROM isp_isp_web, isp_dep WHERE isp_isp_web.doc_id = isp_dep.parent_doc_id AND isp_isp_web.doctype_id = isp_dep.parent_doctype_id AND isp_dep.child_doctype_id = 1014 AND isp_dep.child_doc_id = ".$user["doc_id"]);
                  $login_allowed = $web["web_mailuser_login"];
                  unset($web);

                  if($login_allowed == 1){
                        // for DEBUG Only
                        if($go_info["server"]["mode"] == 'demo') {
							$app->pop3->hostname = "ispconfig.org";
						} else {
							$app->pop3->hostname = "localhost";
						}

                        // Öffne Pop3 Verbindung
                        $res = $app->pop3->Open();
                        if($res == '') {

                                // versuche Login
                                $res = $app->pop3->Login($username,$passwort,0);
                                if($res == '') {

                                        // Login war erfolgreich
                                        $_SESSION["s"]["userid"] = $user["doc_id"];
                                        $_SESSION["s"]["user"]   = $user;
                                        $app->pop3->Close();
                                        header("Location: ../mail/index.php");
                                        exit;

                                } else {
                                        // Username oder PW falsch
                                        $error = $res;
                                        $app->pop3->Close();
                                }
                        } else {
                                // kein pop3 Login möglich
                                $error = $res;
                                $app->pop3->Close();
                        }
                  } else {
                    // Mailuser-Login für das Web nicht zugelassen
                    $error = $app->lng("txt_no_mailuser_login");
                  }
                } else {
                        // User unbekannt in DB
                        $error = $app->lng("txt_user_unbekannt");
                }
        } else {
                $error = $app->lng("txt_email_passwort_leer");
        }
}

$app->tpl->setVar("error",$error);

$app->tpl_defaults();
$app->tpl->pparse();

?>