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
/**
* Listenbehandlung
*
* @package form
* @author Till Brehm
* @version 1.1
*/

if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class listform {

	//var $tableDef;
	//var $table_name;
	var $debug = 0;
	//var $table_index;
	var $errorMessage;
    var $listDef;
	var $searchValues;
	var $pagingHTML;
	var $pagingValues;
	var $searchChanged = 0;
	
	/*
	function loadTableDef($file) {
		global $app,$conf;
		
		include_once($file);
		$this->tableDef = $table;
		$this->table_name = $table_name;
		$this->table_index = $table_index;
		return true;
	}
	*/
    
    function loadListDef($file) {
		global $app,$conf;
		include_once($file);
		$this->listDef = $liste;
		return true;
	}
	
	function getSearchSQL($sql_where = "") {
		global $db;
		
		// Hole Config Variablen
		$list_name = $this->listDef["name"];
		$search_prefix = $this->listDef["search_prefix"];
		
		// speichere Suchanfrage
		foreach($this->listDef["item"] as $i) {
			$field = $i["field"];
			// hat sich die suche geändert
			if(isset($_REQUEST[$search_prefix.$field]) and $_REQUEST[$search_prefix.$field] != $_SESSION["search"][$list_name][$search_prefix.$field]) $this->searchChanged = 1;
			// suchfeld in session speichern.
			if(isset($_REQUEST[$search_prefix.$field])) $_SESSION["search"][$list_name][$search_prefix.$field] = $_REQUEST[$search_prefix.$field];
		}
		
		// Speichere Index
		//$_SESSION["search"][$list_name][$table_index] = $_REQUEST[$table_index];
		
		// Speichere Variablen in Objekt zum späteren einparsen in Template
		$this->searchValues = $_SESSION["search"][$list_name];
		
		// baue SQL Abfrage
		//$sql_where = " $table_index = ".$this->searchValues[$table_index].' and';
		//$sql_where = "";
		
		foreach($this->listDef["item"] as $i) {
			$field = $i["field"];
			if($this->searchValues[$search_prefix.$field] != '') $sql_where .= " $field ".$i["op"]." '".$i["prefix"].$this->searchValues[$search_prefix.$field].$i["suffix"]."' and";
		}
		
		if($sql_where != '') {
			$sql_where = substr($sql_where,0,-3);
		} else {
			$sql_where = "1";
		}
		
		return $sql_where;
	}
	
	function getPagingSQL($sql_where = "1") {
		global $app, $conf;
		
		// Hole Config Variablen
		$list_name 			= $this->listDef["name"];
		$search_prefix 		= $this->listDef["search_prefix"];
		$records_per_page 	= $this->listDef["records_per_page"];
		$table 				= $this->listDef["table"];
		
		
		// setze page auf null, wenn in session nicht gesetzt
		if($_SESSION["search"][$list_name]["page"] == '') $_SESSION["search"][$list_name]["page"] = 0;
		
		// setze page auf wert der request variablen "page"
		if(isset($_REQUEST["page"])) $_SESSION["search"][$list_name]["page"] = $_REQUEST["page"];
		
		// page auf 0 setzen, wenn suche sich geändert hat.
		if($this->searchChanged == 1) $_SESSION["search"][$list_name]["page"] = 0;
		
		$sql_von = $_SESSION["search"][$list_name]["page"] * $records_per_page;
		$record_count = $app->db->queryOneRecord("SELECT count(*) AS anzahl FROM $table WHERE $sql_where");
		$seiten = intval($record_count["anzahl"] / $records_per_page);
		
		//die("SELECT count(*) AS anzahl FROM $table WHERE $sql_where");
		
		$vars["list_file"] = $this->listDef["file"];
		$vars["page"] = $_SESSION["search"][$list_name]["page"];
		$vars["last_page"] = $_SESSION["search"][$list_name]["page"] - 1;
		$vars["next_page"] = $_SESSION["search"][$list_name]["page"] + 1;
		$vars["pages"] = $seiten;
		$vars["max_pages"] = $seiten + 1;
		$vars["records_gesamt"] = $record_count["anzahl"];
		
		
		if($_SESSION["search"][$list_name]["page"] > 0) $vars["show_page_back"] = 1;
		if($_SESSION["search"][$list_name]["page"] <= $seiten - 1) $vars["show_page_next"] = 1;
		
		$this->pagingValues = $vars;
		
		$pg_tpl = new tpl();
		$pg_tpl->newTemplate($this->listDef["paging_tpl"]);
		$pg_tpl->setVar($vars);
		
		$this->pagingHTML = $pg_tpl->grab();
		
		$limit_sql = "LIMIT $sql_von, $records_per_page";
		
		return $limit_sql;
	}
	
	function getSQL() {
	
			
		

////////////////////////////
// hole alle Produkte
////////////////////////////

$sql = "SELECT * FROM domain_record WHERE $sql_where ORDER BY domain LIMIT $sql_von,$records_per_page";
	
	
	
	
	}
	
	
}

?>