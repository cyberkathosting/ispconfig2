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
class nodetree {
var $childs;
var $btext;
var $id;
}

class ordner
{
var $table = "bookmark_daten";

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
		$allnodes = $go_api->db->queryAllRecords("SELECT * FROM ".$this->table." where ordner = $id and userid = $userid and art = 'v'");
		} else {
		$allnodes = $go_api->db->queryAllRecords("SELECT * FROM ".$this->table." where ordner = $id and groupid = $groupid and art = 'v'");
		}
		
		foreach ($queryArray as $DB) {
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
    
    
    function ordner_optionlist($groupid = 0)
    {
    global $go_api, $go_info;
    
    $userid = $go_info["user"]["userid"];
    
        if($groupid == 0) {
	        $nodes = $go_api->db->queryAllRecords("SELECT * FROM bookmark_daten where userid = $userid and groupid = '0' and art = 'v' order by btext");
        } else {
	        $nodes = $go_api->db->queryAllRecords("SELECT * FROM bookmark_daten where art = 'v' and groupid = '$groupid' order by btext");
        }
        
        $myroot = new nodetree();

        foreach($nodes as $row) {

            $id = "my".$row["id"];
            $btext = $row["btext"];
            $ordner = 'my'.$row["ordner"];
            if(!is_object($$id)) $$id = new nodetree();
            $$id->btext = $btext;
            $$id->id = $row["id"];

            if(is_object($$ordner)) {
                 $$ordner->childs[] = &$$id;
            } else {
                $$ordner = new nodetree();
                $$ordner->childs[] = &$$id;
            }
        }

            function ptree($myobj, $ebene, &$optionlist){
                $ebene .= "&nbsp;";

                if(is_array($myobj->childs)) {
                foreach($myobj->childs as $val) {
                    $optionlist[] = array( title => $ebene . $val->btext,
                                   tree_id => $val->id);
                    ptree($val,$ebene, $optionlist);
                }
                }
                }
                
                if($groupid == 0) {
                ptree($myroot,'',$optionlist);
                } else {
                $rootname = 'mygroup'.$groupid;
                ptree($$rootname,'',$optionlist);
                }
        
        if(is_array($nodes)){
			return $optionlist;
		} else {
			return false;
		}
    }
    
    
    
   
		/********************************************************
   		* Diese Funktion Checkt Felder auf ungültige Zeichen
   		*********************************************************/
   		
       function check($to_check, $max_len)
       
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
       
       function update_groupid($id,$userid,$groupid)
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
   		
      	function is_owner($bookmark_id)
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
      	
        function in_path($ordner,$check,$groupid)
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