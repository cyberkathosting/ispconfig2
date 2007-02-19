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



// Hole Artikeldaten
$tmp_artikel = $go_api->db->queryAllRecords("SELECT * from isp_fakt_artikel");

foreach($tmp_artikel as $tmp) {
	$artikel_id = $tmp["doc_id"];
	$artikel[$artikel_id] = $tmp;
}

unset($tmp_artikel);

// Hole Faktura Records
$records = $go_api->db->queryAllRecords("SELECT * from isp_fakt_record, isp_isp_web where isp_fakt_record.status = 1 and isp_fakt_record.artikel_id > 0 and isp_fakt_record.web_id = isp_isp_web.doc_id and isp_isp_web.web_faktura = 1");
$jetzt = time();

foreach($records as $record) {
	$artikel_id = $record["artikel_id"];
	$web_id = $record["web_id"];
	$record_id = $record["record_id"];
  
  	// Bestimme Kunden ID
  	$tmp_kunden_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes, isp_dep where isp_dep.child_doc_id = ".$record["web_id"]." and isp_dep.child_doctype_id = 1013 and isp_nodes.doc_id = isp_dep.parent_doc_id and isp_nodes.doctype_id = isp_dep.parent_doctype_id");
  	//$web_userid = $tmp_kunden_node["userid"];
  	$web_groupid = $tmp_kunden_node["groupid"];
  	//die(print_r($tmp_kunden_node));
  	//$kunde = $go_api->db->queryOneRecord("SELECT doc_id from isp_isp_kunde where webadmin_userid = $web_userid");
	$reseller = $go_api->db->queryOneRecord("SELECT doc_id from isp_isp_reseller where reseller_group = $web_groupid");
  
	$kunden_id = $tmp_kunden_node["doc_id"];
	$reseller_id = ($reseller["doc_id"])?$reseller["doc_id"]:0;
	
	////////////////////////////////////////////////////////////////////////
	// TRAFFIC ARTIKEL
	////////////////////////////////////////////////////////////////////////
	
	if($artikel[$artikel_id]["artikeltyp"] == "Traffic" or $artikel[$artikel_id]["artikeltyp"] == "IP-Traffic") {
	
		$faellig = mktime(0, 0, 0, date("m",$record["letzte_abrechnung"])+$artikel[$artikel_id]["verrechnung"], date("d",$record["letzte_abrechnung"]), date("Y",$record["letzte_abrechnung"]));
		if($faellig <= $jetzt and $artikel[$artikel_id]["verrechnung"] > 0) {
		
		// IP bestimmen
		$web_ip = $record["web_ip"];
			
		// IP-Traffic
		if($artikel[$artikel_id]["artikeltyp"] == "IP-Traffic") {
			$traffic_in = 0;
			$traffic_out = 0;
			$traffic_rows = $go_api->db->queryAllRecords("SELECT * from isp_traffic_ip where datum >= ".$record["letzte_abrechnung"]." and datum <= $jetzt and ip = $web_ip");
      if(is_array($traffic_rows)) {
				foreach($traffic_rows as $tr) {
					$traffic_in += $tr["traffic_in"];
					$traffic_out += $tr["traffic_out"];
				}
			}
			unset($traffic_rows);
			$traffic_gesamt = $traffic_in + $traffic_out;
		}
		
		// Traffic
		if($artikel[$artikel_id]["artikeltyp"] == "Traffic") {
			$traffic_mail = 0;
			$traffic_ftp = 0;
			$traffic_web = 0;
			$traffic_rows = $go_api->db->queryAllRecords("SELECT * from isp_traffic where datum >= ".$record["letzte_abrechnung"]." and datum <= $jetzt and web_id = $web_id");
      if(is_array($traffic_rows)) {
				foreach($traffic_rows as $tr) {
					$traffic_mail += $tr["bytes_mail"];
					$traffic_ftp += $tr["bytes_ftp"];
					$traffic_web += $tr["bytes_web"];
				}
			}
			unset($traffic_rows);
			$traffic_gesamt = $traffic_mail + $traffic_ftp + $traffic_web;
		}
		
		$faktor = 0;
		if($artikel[$artikel_id]["artikeleinheit"] == "MB") $faktor = 1024 * 1024;
		if($artikel[$artikel_id]["artikeleinheit"] == "GB") $faktor = 1024 * 1024 * 1024;
		
		$traffic_inclusive = $artikel[$artikel_id]["abpackung"] * $faktor;
		$traffic_ueberschreitung = $traffic_gesamt - $traffic_inclusive;
		
    $tmp_text = "Traffic: ".sprintf("%01.2f", ($traffic_gesamt / $faktor))." ".$artikel[$artikel_id]["artikeleinheit"]."\r\n";
    $tmp_artikelpreis = $artikel[$artikel_id]["artikelpreis"];
    
		// ‹bertraffic berechnen
		if($traffic_ueberschreitung > 0) {
			
			$faktor = 0;
			if($artikel[$artikel_id]["weitere_artikeleinheit"] == "MB") $faktor = 1024 * 1024;
			if($artikel[$artikel_id]["weitere_artikeleinheit"] == "GB") $faktor = 1024 * 1024 * 1024;
			
			$traffic_kontingent = $artikel[$artikel_id]["weitere_abpackung"] * $faktor;
			$ueberschreitungen = ceil($traffic_ueberschreitung / $traffic_kontingent);
      $tmp_artikelpreis += $artikel[$artikel_id]["weitere_artikelpreis"] * $ueberschreitungen;
      
      $tmp_text .= "‹berschreitung: ".sprintf("%01.2f", ($traffic_ueberschreitung / $faktor))." ".$artikel[$artikel_id]["weitere_artikeleinheit"]."\r\n";
      $tmp_text .= "Kosten weiterer Traffic: ".$artikel[$artikel_id]["weitere_artikelpreis"] * $ueberschreitungen." EUR";
		}
		
		// Artikel berechnen
		//$rechnung[$reseller_id][$kunden_id][$web_id][$record_id] = $record;
    
    $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["anzahl"] = $record["anzahl"];
    $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["type"] = $artikel[$artikel_id]["artikeltyp"];
	$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["nummer"] = $artikel[$artikel_id]["artikelnummer"];
    $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["name"] = $artikel[$artikel_id]["artikelbezeichnung"];
    $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["text"] = $artikel[$artikel_id]["beschreibung"] . $tmp_text;
    $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["preis"] = $tmp_artikelpreis;
    $rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["vat"] = $artikel[$artikel_id]["steuersatz"];
	
	}
		////////////////////////////////////////////////////////////////////////
		// NORMALE ARTIKEL
		////////////////////////////////////////////////////////////////////////
	
	} else {
		// alle Artikel ausser Traffic
		if($artikel[$artikel_id]["verrechnung"] > 0) {
			// Artikel mit regelm‰ﬂiger Abrechnung
			$faellig = mktime(0, 0, 0, date("m",$record["letzte_abrechnung"])+$artikel[$artikel_id]["verrechnung"], date("d",$record["letzte_abrechnung"]), date("Y",$record["letzte_abrechnung"]));
			if($faellig <= $jetzt) {
				//$rechnung[$reseller_id][$kunden_id][$web_id][$record_id] = $record;
        	$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["anzahl"] = $record["anzahl"];
    		$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["type"] = $artikel[$artikel_id]["artikeltyp"];
			$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["nummer"] = $artikel[$artikel_id]["artikelnummer"];
    		$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["name"] = $artikel[$artikel_id]["artikelbezeichnung"] . "\r\n (".$record["notiz"].")";
    		$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["text"] = $artikel[$artikel_id]["beschreibung"];
    		$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["preis"] = $artikel[$artikel_id]["artikelpreis"];
    		$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["vat"] = $artikel[$artikel_id]["steuersatz"];

			}
		} else {
			// Artikel wird nur einmal abgerechnet
			if($record["erste_abrechnung"] == 0) {
				//$rechnung[$reseller_id][$kunden_id][$web_id][$record_id] = $record;
        	$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["anzahl"] = $record["anzahl"];
    		$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["type"] = $artikel[$artikel_id]["artikeltyp"];
			$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["nummer"] = $artikel[$artikel_id]["artikelnummer"];
    		$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["name"] = $artikel[$artikel_id]["artikelbezeichnung"] . "\r\n (".$record["notiz"].")";
    		$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["text"] = $artikel[$artikel_id]["beschreibung"];
    		$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["preis"] = $artikel[$artikel_id]["artikelpreis"];
    		$rechnung[$reseller_id][$kunden_id][$web_id][$record_id]["vat"] = $artikel[$artikel_id]["steuersatz"];

			}
		}
	}
	
}

// Sortiere Rechnungen nach Webs
if(is_array($rechnung)) {
	ksort($rechnung);
	reset($rechnung);
}

if(is_array($rechnung)) {
	include("show.inc.php");
} else {
	$go_api->msg($go_api->lng("Keine Artikel zum Abrechnen gefunden."),$go_api->lng("Abrechnung"));
}

// Rechnung in Session speichern

$go_info["temp"]["rechnung"] = $rechnung;
$go_api->session->save();


?>