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
include("../../../../lib/config.inc.php");
$set_header = 0;
include("../../../../lib/session.inc.php");

//if($go_info["server"]["mode"] == "demo") $go_api->errorMessage("The export functions are not available in the demo mode.");

//if($web["userid"] = $go_info["user"]["userid"] or $go_api->groups->in_group($go_info["user"]["userid"],$web["groupid"])) {

$tr = $trennzeichen;
$ic = stripslashes($einschluss);

// Reseller Stammdaten
if($daten == 'reseller_stammdaten' and $go_api->auth->check_admin(0,1)) {
        $anbieter = $go_api->db->queryAllRecords("SELECT * from isp_nodes,isp_isp_reseller where isp_nodes.doc_id = isp_isp_reseller.doc_id and isp_nodes.doctype_id = '1022'");
        $out = $ic."firma".$ic.$tr.$ic."anrede".$ic.$tr.$ic."vorname".$ic.$tr.$ic."name".$ic.$tr.$ic."strasse".$ic.$tr.$ic."plz".$ic.$tr.$ic."ort".$ic.$tr.$ic."land".$ic.$tr.$ic."telefon".$ic.$tr.$ic."fax".$ic.$tr.$ic."email".$ic.$tr.$ic."internet$ic\r\n";
        if(is_array($anbieter)) {
                foreach($anbieter as $r) {
                        $gid = $r["groupid"];
                        if($reseller[$gid] == 1) {
                                $out .= $ic.$r["firma"].$ic.$tr.$ic.$r["anrede"].$ic.$tr.$ic.$r["vorname"].$ic.$tr.$ic.$r["name"].$ic.$tr.$ic.$r["strasse"].$ic.$tr.$ic.$r["plz"].$ic.$tr.$ic.$r["ort"].$ic.$tr.$ic.$r["land"].$ic.$tr.$ic.$r["telefon"].$ic.$tr.$ic.$r["fax"].$ic.$tr.$ic.$r["email"].$ic.$tr.$ic.$r["internet"].$ic."\r\n";
                        }
                }
        }
}

// Kunden Stammdaten
if($daten == 'kunden_stammdaten') {
                $sql = "SELECT * from isp_nodes,isp_isp_kunde where isp_nodes.doc_id = isp_isp_kunde.doc_id and isp_nodes.doctype_id = '1012' and ";
                $sql .= "( isp_nodes.userid = '$userid' or";
        $gruppen = $go_api->groups->myGroups();

        foreach( $gruppen as $gkey => $gval) {
            if($gval["userstatus"] == 1) {
                $sql .= " isp_nodes.groupid = '$gkey' or";
            }
        }
        $sql = substr($sql,0,-2);
                $sql .= ")";

        $kunde = $go_api->db->queryAllRecords($sql);
        $out = $ic."firma".$ic.$tr.$ic."anrede".$ic.$tr.$ic."vorname".$ic.$tr.$ic."name".$ic.$tr.$ic."strasse".$ic.$tr.$ic."plz".$ic.$tr.$ic."ort".$ic.$tr.$ic."land".$ic.$tr.$ic."telefon".$ic.$tr.$ic."fax".$ic.$tr.$ic."email".$ic.$tr.$ic."internet".$ic."rechnung_firma".$ic.$tr.$ic."rechnung_vorname".$ic.$tr.$ic."rechnung_name".$ic.$tr.$ic."rechnung_strasse".$ic.$tr.$ic."rechnung_plz,rechnung_ort".$ic.$tr.$ic."rechnung_land".$ic.$tr.$ic."rechnung_intervall".$ic.$tr.$ic."rechnung_preis".$ic.$tr.$ic."rechnung_zahlungsbedingungen"."\r\n";
        if(is_array($kunde)) {
                foreach($kunde as $r) {
                        $gid = $r["groupid"];
                        if($reseller[$gid] == 1) {
                                $out .= $ic.$r["kunde_firma"].$ic.$tr.$ic.$r["kunde_anrede"].$ic.$tr.$ic.$r["kunde_vorname"].$ic.$tr.$ic.$r["kunde_name"].$ic.$tr.$ic.$r["kunde_strasse"].$ic.$tr.$ic.$r["kunde_plz"].$ic.$tr.$ic.$r["kunde_ort"].$ic.$tr.$ic.$r["kunde_land"].$ic.$tr.$ic.$r["kunde_telefon"].$ic.$tr.$ic.$r["kunde_fax"].$ic.$tr.$ic.$r["kunde_email"].$ic.$tr.$ic.$r["kunde_internet"].$ic.$r["rechnung_firma"].$ic.$tr.$ic.$r["rechnung_vorname"].$ic.$tr.$ic.$r["rechnung_name"].$ic.$tr.$ic.$r["rechnung_strasse"].$ic.$tr.$ic.$r["rechnung_plz"].$ic.$tr.$ic.$r["rechnung_ort"].$ic.$tr.$ic.$r["rechnung_land"].$ic.$tr.$ic.$r["rechnung_intervall"].$ic.$tr.$ic.$r["rechnung_preis"].$ic.$tr.$ic.$r["rechnung_zahlungsbedingungen"]."\r\n";
                        }
                }
        }
}



// setze Header
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"export.csv\"");

echo $out;
?>