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

class groups
{

        function myGroups()
        {
         global $go_api, $go_info;
         
          $userid = $go_info["user"]["userid"];
	  $rows = $go_api->db->queryAllRecords("select * from user_groups, groups where user_groups.userid = '$userid' and user_groups.groupid = groups.groupid and user_groups.userstatus = '1' and groups.groupstatus = '1' order by groups.name");
	  if(is_array($rows)) {
	    foreach($rows as $row) {
              $groupid = $row["groupid"];
	      $mygroups[$groupid] = array( 	usergroupid => $row["usergroupid"],
						userid => $row["userid"],
		  				name => $row["name"],
						perms => $row["perms"],
			 			userstatus => $row["userstatus"],
						groupstatus => $row["groupstatus"]
					);
			
			
	    }
	  }
          unset($rows);
	  if(is_array($mygroups)){
		return $mygroups;
	  } else {
		return false;
	  }
        }
        
        function in_group($uid,$gid)
        {
        global $go_api, $go_info;
			$uid = intval($uid);
			$gid = intval($gid);
            if($row = $go_api->db->queryOneRecord("SELECT * from user_groups where userid = '$uid' and groupid = '$gid'")) {
                return $row["perms"];
            } else {
                return false;
            }
        }
        
        
        function user_delete($uid,$gid)
        {
        global $go_api, $go_info;
		$uid = intval($uid);
		$gid = intval($gid);
        if($go_api->auth->check_admin($gid,1) or $uid == $go_info["user"]["userid"]) {
        // Wie viele Admins hat die Gruppe
	        $go_api->db->query("SELECT * from user_groups where groupid = '$gid' and perms = 'rwa' and userstatus = '1'");
	        if($go_api->db->numRows() < 2) {		
	        // check ob sich der letzte admin aussperrt
	        $row = $go_api->db->queryOneRecord("SELECT * from user_groups where userid = '$uid' and groupid = '$gid'");
                if($row["perms"] == 'rwa' and $row["userstatus"] == '1') {
			        $message = "Achtung: Sie können nicht den einzigen Administrator der Gruppe löschen<br><br> 
			        Lösung: Löschen Sie stattdessen die gesamte Gruppe";
			        $go_api->errorMessage($message);
		        }
	        }
        $go_api->db->query("DELETE FROM user_groups where userid = '$uid' and groupid = '$gid'");
        } else {
        $go_api->errorMessage("Sie haben nicht die notwendigen Rechte um diese User zu löschen.");
        }
        }
        
        function group_delete($gid)
        {
        global $go_api, $go_info;
		$gid = intval($gid);
        if($go_api->auth->check_admin($gid)) {
        
        $go_api->db->query("DELETE FROM user_groups where groupid = '$gid'");
        $go_api->db->query("DELETE FROM groups where groupid = '$gid'");
        $go_api->db->query("DELETE FROM bookmark_daten where groupid = '$gid'");
        $go_api->db->query("DELETE FROM adressbuch_daten where groupid = '$gid'");
        }
        }
}
?>
