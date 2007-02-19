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

class auth
{

   var $status;
   var $userid;
   //var $gueltig_bis_tag = "01";
   //var $gueltig_bis_monat = "01";
   //var $gueltig_bis_jahr = "2003";
   //var $gueltig_ab_tag = "01";
   //var $gueltig_ab_monat = "08";
   //var $gueltig_ab_jahr = "2002";
   //var $user_max = 50;


        function check_user()
        {
         global $go_api, $username, $passwort, $PHP_AUTH_USER, $PHP_AUTH_PW;

                /* Check nach login PUT Variablen*/
                if (($username != "") && ($passwort != "")) {
                    $username = addslashes($username);
					$passwort = addslashes($passwort);

                    if ($row = $go_api->db->queryOneRecord("SELECT username,doc_id FROM sys_user WHERE username = '$username' AND (passwort = PASSWORD('$passwort') OR passwort = '".md5($passwort)."')")) {
                        if($row["username"] == $username)
                            {
                            $this->status = "ok";
                            $this->userid = $row["doc_id"];
                        } else {
                            $this->login(101);
                        }
                    } else {
                        $this->login(101);
                    }
                } else {
                    $this->login(103);
                }
        }



        function check_session()
        {
                global $go_api, $s, $HTTP_SERVER_VARS, $HTTP_COOKIE_VARS;


                // Session Check
                $sessionispconfig = $HTTP_COOKIE_VARS["sessionispconfig"];

                $s = addslashes($s);
                if ($row = $go_api->db->queryOneRecord("SELECT * FROM session where sessionid = '$s'"))
                    {
                    if($row["remote_addr"] == $HTTP_SERVER_VARS["REMOTE_ADDR"]) {
                    $this->status = "ok";
                    } elseif($row["remote_addr"] == "cookie" and $sessionispconfig == $s) {
                    $this->status = "ok";
                    } else {
                    $this->login();
                    }
                } else {
                    $this->login();
                }
        }

        function login($errCode = '')
        {
        global $go_api, $go_info;
        header('Location: '.$go_info["server"]["server_url"].'/login.php?err='.$errCode);
        exit;
        }

        function is_user($username) {

        global $go_api, $go_info, $s;


        if ($row = $go_api->db->queryOneRecord("SELECT doc_id FROM sys_user where username = '$username' and gueltig = '1'")) {
                return $row["doc_id"];
        } else {
                return false;
        }

        }
		
		function user_type($user_id) {
		global $go_api, $s;
		
		if($user_id == 1) return "admin";
		
		$sys_resl_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as num from isp_isp_reseller where reseller_userid = $user_id");
		if($sys_resl_count["num"] >= 1) return "reseller";
		
		$sys_user_count = $go_api->db->queryOneRecord("SELECT count(doc_id) as num from isp_isp_kunde where webadmin_userid = $user_id");
		if($sys_user_count["num"] >= 1) return "client";
		}

        function check_perms($groupid,$rights,$action = 0)
        {
        global $go_api, $go_info, $s;

        if($groupid == "" or $groupid == 0)

                {

                /******************************************
                * Checke User Schreibrechte
                ******************************************/
                if (stristr($go_info["user"]["perms"], $rights))
                {
                return true;
                } else {
                	$message = '<b>'.$go_api->lng('user_perms_nowrite_hd').'</b><p><br>'
                	           .
                	           $go_api->lng('user_perms_nowrite_reasons').':<br>'
                	           .
                	           $go_api->lng('user_perms_nowrite_reasons_1a').'<br>'
                	           .
                	           $go_api->lng('user_perms_nowrite_reasons_1b').'<br><br>'
                	           .
                	           $go_api->lng('user_perms_nowrite_reasons_2a').'<br>'
                	           .
                	           $go_api->lng('user_perms_nowrite_reasons_2b').'<br><br>'
                	           .
                	           $go_api->lng('user_perms_nowrite_reasons_2c').'<br><br>'
                	           .
                	           '<center><a href="' . $go_info["server"]["server_url"] . "/index.php?s=$s\">Weiter &gt;&gt;</a></center>";

                if($action == 0) {
            $go_api->errorMessage($message);
            } else {
            return false;
            }

                }
                } else {
                /******************************************
                * Checke Gruppen Schreibrechte
                ******************************************/

                        $sql = "SELECT * from user_groups where groupid = '$groupid' and userid = '" . $go_info["user"]["userid"] . "' and userstatus = '1'";
                        if($row = $go_api->db->queryOneRecord($sql)) {
                        $perms = $row["perms"];
                        }
                if (stristr($perms, $rights))
                {
                return true;
                } else {
                	$message = '<b>'.$go_api->lng('group_perms_nowrite_hd').'</b><p><br>'
                	           .
                	           $go_api->lng('group_perms_nowrite_reasons').':<br>'
                	           .
                	           $go_api->lng('group_perms_nowrite_reasons_1a').'<br>'
                	           .
                	           $go_api->lng('group_perms_nowrite_reasons_1b').'<br><br>'
                	           .
                	           $go_api->lng('group_perms_nowrite_reasons_2a').'<br>'
                	           .
                	           $go_api->lng('group_perms_nowrite_reasons_2b').'<br><br>'
                	           .
                	           $go_api->lng('group_perms_nowrite_reasons_2c').'<br><br>'
                	           .
                	           '<center><a href="' . $go_info["server"]["server_url"] . "/index.php?s=$s\">Weiter &gt;&gt;</a></center>";

            if($action == 0) {
            $go_api->errorMessage($message);
            } else {
            return false;
            }

                }
                }

        return false;
        }

        function check_read($groupid,$action = 0)
        {
        return $this->check_perms($groupid,"r",$action);
        }

        function check_write($groupid,$action = 0)
        {
        return $this->check_perms($groupid,"w",$action);
        }

        function check_admin($groupid,$action = 0)
        {
        return $this->check_perms($groupid,"a",$action);
        }

        function check_adduser($doc_id, $doctype_id)
        {
            global $go_api;
            $user = $go_api->db->queryOneRecord("SELECT * from sys_user where doc_id = $doc_id");
            if($user["pwcl"] != "") {
              $go_api->db->query("UPDATE sys_user SET passwort = password(pwcl), pwcl = '' where doc_id = $doc_id");
            }
        }

        function check_updateuser($doc_id, $doctype_id)
        {
            global $go_api;
                $user = $go_api->db->queryOneRecord("SELECT * from sys_user where doc_id = $doc_id");
                if($user["pwcl"] != "") {
                    $go_api->db->query("UPDATE sys_user SET passwort = password(pwcl), pwcl = '' where doc_id = $doc_id");
                }
        }

}
?>