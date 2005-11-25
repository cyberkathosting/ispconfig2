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

include("../../../lib/config.inc.php");
include("../../../lib/session.inc.php");

$rechnung = $go_info["temp"]["rechnung"];

// Kunden + Reseller nr von:
$sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");


  foreach($rechnung as $reseller_id => $tmp_kunden) {
  
  // Wenn Reseller markiert ist
  if($HTTP_POST_VARS["frm_reseller"][$reseller_id] == 1) {
  
  $reseller_preis_gesamt = 0;
  	if($reseller_id > 0) {
  		$reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller WHERE doc_id = $reseller_id");
		$reseller_name = $reseller["firma"]." - ".$reseller["vorname"]." ".$reseller["name"];
	} else {
		$reseller_name = "Administrator";
	}
  // RESELLER
  //echo $reseller_id;
  
  $reseller_invoice = "<reseller>
  <resellerid>$reseller_id</resellerid>\r\n";
  
  foreach($tmp_kunden as $kunde_id => $tmp_webs) {

  // Wenn Kunde markiert ist
  if($HTTP_POST_VARS["frm_kunde"][$kunde_id] == 1) {
  
  $kunde_preis_gesamt = 0;
  $kunde = $go_api->db->queryOneRecord("SELECT * from isp_isp_kunde WHERE doc_id = $kunde_id");
  
  $kunden_nr = $kunde_id + $sys_config["kunde_nr_von"];
  
  $invoice = "<invoice>
  <clientnr>$kunden_nr</clientnr>
  <company>".htmlentities($kunde["rechnung_firma"])."</company>
  <forename>".htmlentities($kunde["rechnung_vorname"])."</forename>
  <surname>".htmlentities($kunde["rechnung_name"])."</surname>
  <street>".htmlentities($kunde["rechnung_strasse"])."</street>
  <zip>".htmlentities($kunde["rechnung_plz"])."</zip>
  <town>".htmlentities($kunde["rechnung_ort"])."</town>
  <country>".htmlentities($kunde["rechnung_land"])."</country>
  <vatnr></vatnr>";
 	
	//KUNDE
	
	foreach($tmp_webs as $web_id => $tmp_records) {
	
	// Wenn Web markiert ist
  	if($HTTP_POST_VARS["frm_web"][$web_id] == 1) {
	
	$web_preis_gesamt = 0;
	$web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web WHERE doc_id = $web_id");
  	$web_name = $web["web_host"].".".$web["web_domain"];
	
		//WEB
	  	//echo $web_name;
		
		$invoice .= "
  <web>
    <webid>$web_id</webid>
    <host>".htmlentities($web_name)."</host>";
		
	  
		foreach($tmp_records as $record_id => $record) {
		
			// Wenn Record markiert ist
  			if($HTTP_POST_VARS["frm_record"][$record_id] == 1) {
			
			// RECORD
			//echo $record_id;
			//echo $record["name"];
			//echo sprintf("%01.2f", $record["preis"]);
			//echo nl2br($record["text"]);
  			$web_preis_gesamt += $record["preis"];
			
			$invoice .= "
    <record>
      <recordid>$record_id</recordid>
      <artnr>".htmlentities($record["nummer"])."</artnr>
      <number>".htmlentities($record["anzahl"])."</number>
      <type>".htmlentities($record["type"])."</type>
      <name>".htmlentities($record["name"])."</name>
      <description>".htmlentities($record["text"])."</description>
      <price>".sprintf("%01.2f", $record["preis"])."</price>
      <vat>".$record["vat"]."</vat>
    </record>";
			
			}
  			// Ende RECORD
			
			// Element als abgerechnet markieren.
			$sql = "UPDATE isp_fakt_record SET letzte_abrechnung = ".time()." where record_id = $record_id";
			$go_api->db->query($sql);
			
  		}
  		//echo sprintf("%01.2f", $web_preis_gesamt);
  		$kunde_preis_gesamt += $web_preis_gesamt;
		
		$invoice .= "
  </web>\r\n";
		
	}
  	// Ende WEB
  	}
  
  	//echo sprintf("%01.2f", $kunde_preis_gesamt);
  	$reseller_preis_gesamt += $kunde_preis_gesamt;
	
	$invoice .= "</invoice>\r\n";
	
	// Daten Serialisieren
	$data = addslashes(serialize($tmp_webs));
	
	// Rechnung speichern
	$sql = "INSERT INTO isp_fakt_rechnung 
	(reseller_id, kunde_id, typ, datum, versand, rechnung, data) VALUES 
	($reseller_id, $kunde_id, 'client', ".time().", 0, '".addslashes($invoice)."', '$data')";
	
	$go_api->db->query($sql);
	
	// Rechnung zu Reseller_rechnung hinzufügen
	$reseller_invoice .= $invoice;
	
	
  }
  // Ende KUNDE
  }
  //echo sprintf("%01.2f", $reseller_preis_gesamt);
  }
  
  $reseller_invoice .= "</reseller>\r\n";
  
  // Daten Serialisieren
  $data = addslashes(serialize($tmp_kunden));
  
  // Rechnung speichern
  $sql = "INSERT INTO isp_fakt_rechnung 
  (reseller_id, kunde_id, typ, datum, versand, rechnung, data) VALUES 
  ($reseller_id, $kunde_id, 'reseller', ".time().", 0, '".addslashes($invoice)."', '$data')";
	
  $go_api->db->query($sql);
  
  // ende RESELLER
  }

$go_api->msg($go_api->lng("Abrechnung abgeschlossen."),$go_api->lng("Abrechnung"));

?>