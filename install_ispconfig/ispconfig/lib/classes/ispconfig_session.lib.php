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

if(CONFIG_LOADED != 1) die('Direct access not permitted.');


class session
   {
                function start()
                {
                        global $go_api, $s, $go_info;

                        $jetzt = time();
                        $go_api->db->query("delete from session where bis <= $jetzt");

                        if(!empty($s))
                        {
                                if($row = $go_api->db->queryOneRecord("SELECT datas FROM session WHERE sessionid = '$s'"))
                                {
                                        $serialized = $row["datas"];
                                        $go_info = unserialize($serialized);
                                        $bis = time() + $go_info["session"]["timeout"];
                                        $go_api->db->query("UPDATE session SET bis = $bis where sessionid = '$s'");
                                } else {
                                $s = "";
                                $go_api->auth->login(104);
                                exit;
                                }
                        }
                        else
                        {
                                $s = md5(uniqid(time()));
                        }
                        return $s;
                }


                function register($var)
                {
                        if(!isset($GLOBALS['php3SessionsVars'][$var]))
                        {
                                $GLOBALS['php3SessionsVars'][$var] = $var;
                                // $GLOBALS['php3SessionsVars']['hallo'] = "12345";
                        }
                }

                function unregister($var)
                {
                        if(isset($GLOBALS['php3SessionsVars'][$var]))
                        {
                                $GLOBALS['php3SessionsVars'][$var] = '';
                        }
                }

                function save()
                {
                        global $go_api, $s, $go_info,$HTTP_SERVER_VARS;
                        $bis = time() + $go_info["session"]["timeout"];
                        $serialized = addslashes(serialize($go_info));
						$sql = "SELECT COUNT(*) as anzahl FROM session WHERE sessionid = '$s'";
                        $tmp = $go_api->db->queryOneRecord($sql);
                        if ($tmp["anzahl"] == 0)
                        {
                                $laston = date("y-m-d H:i:s");
                                $go_api->db->query("INSERT INTO session (sessionid, userid, bis, datas, remote_addr) VALUES ('$s', '".$go_info["user"]["userid"]."', ".$bis.", '".$serialized."', '".$HTTP_SERVER_VARS["REMOTE_ADDR"]."')");
                                $go_api->db->query("UPDATE friends_daten SET logon = 'j' where friend = " . $go_info["user"]["userid"]);
                                //$go_api->db->query("UPDATE sys_user SET laston = '$laston' where doc_id = " . $go_info["user"]["userid"]);
                        }
                        else
                        {
                                $go_api->db->query("UPDATE session SET datas = '".$serialized."', bis = ".$bis." WHERE sessionid = '$s'");
                        }

                        return true;
                }

                function destroy()
                {
                        global $go_api,$s;
                        $go_api->db->query("DELETE FROM session WHERE sessionid = '$s'");
                        unset($GLOBALS['$go_info']);
                }
        }
?>