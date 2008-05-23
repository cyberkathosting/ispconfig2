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

class file
{
var $_table;

    // $vars enthält:
    // - parent     :id des Elternelementes
    // - type       :n = node, i = item
    // - doctype_id :id des Dokumententyps, wenn nicht im content Feld
    // - title      :Titel des Eintrages
    // - status     :1 = ok, d = delete
    // - icon       :icon im node-tree, optional
    // - modul      :modul des Eintrages, noch nicht verwendet
    // - doc_id     :id des zugehörigen Dokumentes

	// Funktion zum erstellen der Datenbanktabelle
    function create_node_table($tablename)
    {
    global $go_api, $go_info;
    if($go_api->auth->check_admin(0)){
	$tablename = addslashes($tablename);
    $go_api->db->query("CREATE TABLE ".$tablename."_nodes (tree_id BIGINT UNSIGNED not null AUTO_INCREMENT, userid BIGINT UNSIGNED not null , groupid BIGINT UNSIGNED not null , parent VARCHAR (100) not null , type CHAR (1) not null , doctype_id INT not null , title VARCHAR (255) not null , status CHAR (1) DEFAULT '1' not null , icon VARCHAR (255) not null , modul VARCHAR (255) not null , doc_id BIGINT not null , PRIMARY KEY (tree_id), INDEX (tree_id, userid, groupid), UNIQUE (tree_id))");
    }
    }
    
    // Returns an array with the item Properties
    function item($item_id)
    {
    global $go_api, $go_info;
        if($this->is_owner($item_id,"r") and $item_id != "") {
        return $go_api->db->queryOneRecord("SELECT * from ".$this->_table." where file_id = '$item_id'");
        } else {
        return false;
        }
    }
     
    // Funktion zum hinzufügen von nicht verzweigten Einträgen
    function item_add($vars, $groupid = 0)
    {
    global $go_api, $go_info;
        if($go_api->auth->check_write($groupid)) {
        
        $this->check($vars);
        $vars["userid"] = $go_info["user"]["userid"];
        $vars["groupid"] = $groupid;
        // $vars["type"] = "i";
        $vars["status"] = "1";
        $go_api->db->insert($this->_table,$vars);
        return $go_api->db->insertID();
        }
    }
    
    function item_update($item_id, $vars, $groupid = 0)
    {
    global $go_api, $go_info;
        if($this->is_owner($item_id,"w")) {
        
        $this->check($vars);
        $vars["userid"] = $go_info["user"]["userid"];
        $vars["groupid"] = $groupid;
        // $vars["type"] = "i";
        $vars["status"] = "1";
        $go_api->db->update($this->_table,$vars,"tree_id = '$item_id'");
        }
    }
    
    function item_delete($item_id, $groupid = 0)
    {
    global $go_api, $go_info;
        if($this->is_owner($item_id,"w")) {
        $go_api->db->query("DELETE from ".$this->_table." where tree_id = '$item_id'");
        }
    }
    
    function item_owner($item_id)
    {
    global $go_api, $go_info;
    
    }
    
    // Returns an array with the node Properties
    function node($node_id, $groupid = 0)
    {
    global $go_api, $go_info;
        if($this->is_owner($node_id,"r")) {
        return $go_api->db->queryOneRecord("SELECT * from ".$this->_table." where tree_id = '$node_id'");
        }
    }
    
    function node_add($vars, $groupid = 0)
    {
    global $go_api, $go_info;
        if($go_api->auth->check_write($groupid)) {
        
        $this->check($vars);
        $vars["userid"] = $go_info["user"]["userid"];
        $vars["groupid"] = $groupid;
        $vars["type"] = "n";
        $vars["status"] = "1";
        $go_api->db->insert($this->_table,$vars);
        return $go_api->db->insertID();
        }
    }
    
    function node_update($node_id, $vars, $groupid = 0)
    {
    global $go_api, $go_info;
        if($this->is_owner($node_id,"w")) {
        
        $this->check($vars);
        $vars["userid"] = $go_info["user"]["userid"];
        $vars["groupid"] = $groupid;
        $vars["type"] = "n";
        $vars["status"] = "1";
        $go_api->db->update($this->_table,$vars,"tree_id = '$node_id'");
        }
    }
    
    function node_delete($node_id, $groupid = 0)
    {
    global $go_api, $go_info;
    
    }
    
    function node_owner($node_id)
    {
    global $go_api, $go_info;
    
    }
    
    function node_list($groupid = 0)
    {
    global $go_api, $go_info;
    
    $userid = $go_info["user"]["userid"];
    
        if($groupid == 0) {
	        $nodes = $go_api->db->queryAllRecords("SELECT * FROM ".$this->_table." where userid = $userid and groupid = '0' and type = 'n' order by title");
        } else {
	        $nodes = $go_api->db->queryAllRecords("SELECT * FROM ".$this->_table." where type = 'n' and groupid = '$groupid' order by title");
        }

		if(is_array($nodes)){
			return $nodes;
		} else {
			return false;
		}
    }
    
    function node_optionlist($groupid = 0)
    {
    global $go_api, $go_info;
    
    }
    
    function empty_trash($groupid = 0)
    {
    global $go_api, $go_info;
    
    }
    
    function is_owner($item_id,$perms = "w")
    {
    global $go_api,$go_info;
    $row = $go_api->db->queryOneRecord("Select userid, groupid from ".$this->_table." where file_id = '$item_id'");	
      	// Check ob der aktuelle User der Eigentümer ist
      	if($row["userid"] == $go_info["user"]["userid"] and $go_api->auth->check_perms("0",$perms)) {
      	    return true;      	
      	} else {
      	    // Check Ob der aktuelle User Gruppenrechte an diesem Bookmark besitzt
      	    $group = $go_api->db->queryOneRecord("select * from user_groups where groupid = '".$row["groupid"]."' and userid = '".$go_info["user"]["userid"]."'");
            if(stristr($group["perms"],$perms)) {
                return true;
            }
      	return false;
      	}
    }
    
    function set_table($table)
    {
    $this->_table = $table."_nodes";
    }
    
    function check($to_check, $max_len = 0)
    {
       // $to_check = addslashes($to_check);
    $to_check = strtr($to_check, "\"", "'");
        if($max_len != 0)
       	{
       	    $to_check = substr($to_check,0,$max_len);
       	}
        return $to_check;
     }
     
     function debug($dbg)
    {
    print("<pre>&quot;"); 
    print_r( $dbg ); 
    print("&quot;</pre>" );
    }
    
###########################################################################################################################
###########################################################################################################################
###########################################################################################################################
    
    /********************************************************
   	* Diese Funktion fügt neue Ordner hinzu
   	*********************************************************/
			
  	function insert_ordner($btext,$ordner,$groupid)
   	
   	{
   	global $go_api, $go_info;
       
    if($btext != "" and $ordner != "" and $go_api->auth->check_write($groupid)) 
    {
   		/***********************************************
   		* Checke Felder auf ungültige Zeichen
   		************************************************/
   		
		$btext = $this->check($btext, 50);
		$ordner = $this->check($ordner, 0);
		
		/***********************************************
   		* Setze Standardwerte und schreibe in DB
   		************************************************/

		$userid = $go_info["user"]["userid"];
		$art = "v";
		$datum = date("Y-m-d");
		$go_api->db->query("INSERT INTO ".$this->table." ( userid, art, ordner, btext, link, datum, groupid ) VALUES ($userid, '$art', '$ordner', '$btext', '-', '$datum', '$groupid')");
		return true;
	} else {	
		return false;
    }
    }
    
    
	/********************************************************
   	* Funktion: Ordner löschen
   	*********************************************************/
			
  	function delete_ordner($id, $groupid)
   	
   	{
   	global $go_api, $go_info;
       
    if($id != "" and $go_api->auth->check_write($groupid) and $this->is_owner($id)) 
    {
   		/***********************************************
   		* Checke Felder auf ungültige Zeichen
   		************************************************/
   		
		$id = $this->check($id, 0);
		$userid = $go_info["user"]["userid"];
		
		/***********************************************
   		* rekursives löschen der Ordner
   		************************************************/
		if($groupid == 0){
		$go_api->db->query("SELECT * FROM ".$this->table." where ordner = $id and userid = $userid and art = 'v'");
		} else {
		$go_api->db->query("SELECT * FROM ".$this->table." where ordner = $id and groupid = $groupid and art = 'v'");
		}
		
		while($DB = $go_api->db->nextRecord()) {
		$newid = $DB["id"];
		$this->delete_ordner($newid,$groupid);
		
		}
		if($groupid == 0) {
		$go_api->db->query("DELETE FROM ".$this->table." where id = $id and userid = $userid");		
		$go_api->db->query("DELETE FROM ".$this->table." where ordner = $id and userid = $userid");
		} else {
		$go_api->db->query("DELETE FROM ".$this->table." where id = $id and groupid = '$groupid'");		
		$go_api->db->query("DELETE FROM ".$this->table." where ordner = $id and groupid = '$groupid'");		
		}
		
		return true;
	} else {	
		return false;
    }
    }    
    
    
	/********************************************************
   	* Funktion: Ordner updaten
   	*********************************************************/
			
  	function update_ordner($id,$btext,$ordner,$groupid)
   	
   	{
   	global $go_api, $go_info;
    
    if($id != "" and $btext != "" and $ordner != "" and $go_api->auth->check_write($groupid) and $this->is_owner($id) and !$this->in_path($ordner,$id,$groupid)) 
    {
   		/***********************************************
   		* Checke Felder auf ungültige Zeichen
   		************************************************/
   		
		$btext = $this->check($btext, 50);
		$ordner = $this->check($ordner, 0);
		
		/***********************************************
   		* Setze Standardwerte und schreibe in DB
   		************************************************/
		$userid = $go_info["user"]["userid"];
		$art = "v";
		$datum = date("Y-m-d");
		$sql = "UPDATE ".$this->table." SET";
		if ($ordner != "-") $sql = $sql . " ordner = '$ordner',";
		if ($btext != "") $sql = $sql . " btext = '$btext',";
		if ($groupid == "0") $sql = $sql . " userid = '".$go_info["user"]["userid"]."', ";
		if ($groupid != "") $sql = $sql . " groupid = '$groupid' ";
		$sql = $sql . " WHERE id = $id";
		$go_api->db->query($sql);
		$this->update_groupid($id,$userid,$groupid);
		return true;
	} else {	
		return false;
    }
    }
   
		/********************************************************
   		* Diese Funktion Checkt Felder auf ungültige Zeichen
   		*********************************************************/
   		
       function check_old($to_check, $max_len)
       
       {
       // $to_check = addslashes($to_check);
       $to_check = strtr($to_check, "\"", "'");
       
       if($max_len != 0)
       	{
       	$to_check = substr($to_check,0,$max_len);
       	}
       return $to_check;
       }
       
       /********************************************************
   		* Rekursives updaten der Groupid, wenn sich Gruppe ändert
   		*********************************************************/
       
       function update_groupid_old($id,$userid,$groupid)
       {
       global $go_api;
       /***********************************************
   		* rekursives updaten der Groupid's
   		************************************************/
		$go_api->db->query("SELECT * FROM ".$this->table." where ordner = $id and art = 'v'");

		
		while($DB = $go_api->db->nextRecord()) {
		$newid = $DB["id"];
		$this->update_groupid($newid,$userid,$groupid);
		
		}
		if($groupid == 0) {
		$go_api->db->query("UPDATE ".$this->table." SET groupid = '$groupid', userid = '$userid' where id = $id");		
		$go_api->db->query("UPDATE ".$this->table." SET groupid = '$groupid', userid = '$userid' where ordner = $id");
		} else {
		$go_api->db->query("UPDATE ".$this->table." SET groupid = '$groupid' where id = $id");		
		$go_api->db->query("UPDATE ".$this->table." SET groupid = '$groupid' where ordner = $id");
		}
       
       }
       
       /********************************************************
   		* Prüft ob der aktuelle Nutzer Eigentümer eines Bookmark /Ordners ist
   		* oder zu der Eigentümergruppe gehört
   		*********************************************************/
   		
      	function is_owner_old($bookmark_id)
      	{
      	global $go_api,$go_info;
      	$DB = $go_api->db->queryOneRecord("Select userid, groupid from ".$this->table." where id = '$bookmark_id'");
      	
      	// Check ob der aktuelle User der Eigentümer ist
      	if($DB["userid"] == $go_info["user"]["userid"] and $go_api->auth->check_write("0")) {
      	return true;      	
      	} else {
      	// Check Ob der aktuelle User Gruppenrechte an diesem Bookmark besitzt
      	$group = $go_api->db->queryOneRecord("select * from user_groups where groupid = '".$DB["groupid"]."' and userid = '".$go_info["user"]["userid"]."'");
       if(stristr($group["perms"],"rw")) {
       return true;
       }
      	return false;
      	}
      	}
      	
      	/********************************************************
   		* gibt true zurueck wenn sich ein Ordner im Pfad eines 
        * anderen Ordners befindet
        * $ordner enth&#xE4;lt den neuen Zielordner
        * $check enthaelt den alten Ordner, auf den getestet wird
   		*********************************************************/
      	
        function in_path_old($ordner,$check,$groupid)
      	{
      	global $go_api,$go_info;
        $userid = $go_info["user"]["userid"];
        $ret = false;
        if($groupid == "0"){
            $allordner = $go_api->db->queryAllRecords("Select * from ".$this->table." where userid = '$userid' and art = 'v'");
        } else {
            $allordner = $go_api->db->queryAllRecords("Select * from ".$this->table." where groupid = '$groupid' and art = 'v'");
        }
        while($x < 100 and $ordner != "root" and !stristr($ordner,"group")){
            while (list($key, $val) = each($allordner)) {
            if($val["id"] == $ordner){
                if($val["ordner"] == $check){
                    $ordner = $val["ordner"];
                    $ret = true;
                    break;
                } else {
                    $ordner = $val["ordner"];
                    break;
                }
            }
            }
        reset($allordner);
        $x++;
        }
        return $ret;
      	}

}
?>