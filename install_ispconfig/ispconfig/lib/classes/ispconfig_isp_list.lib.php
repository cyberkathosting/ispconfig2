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

//if(CONFIG_LOADED != 1) die('Direct access not permitted.');

class isp_list
{

	var $path_mailman_dir;
	var $web_doctype_id = 1013;
	var $user_doctype_id = 1014;
	var $list_doctype_id = 1033;
	var $default_mailman_domain = "lists.example.com";
	var $new_list_path = "/usr/lib/mailman/bin/newlist";
	var $config_list_path = "/usr/lib/mailman/bin/config_list";

	//Constructor
	//function isp_list() {
	//global $go_api, $go_info;
	//
	//}

	function list_show($doc_id, $doctype_id) {
		//global $go_api, $go_info, $doc, $tablevalues, $next_tree_id;
		global $go_api, $go_info, $doc, $next_tree_id;

		if($doc_id > 0) {
			$doc->deck[2]->elements[2]->values["accept"] = $go_api->lng("txt_accept");
			$doc->deck[2]->elements[2]->values["discard"] = $go_api->lng("txt_discard");
		}

	}

	function list_insert($doc_id, $doctype_id, $die_on_error = '1') {
		global $go_api, $go_info;

		// Eintrag der Liste holen
		$list = $go_api->db->queryOneRecord("select * from isp_isp_list where doc_id = '$doc_id'");

		// Check Ob maximale Anzah Listen des Web erreicht ist
		// Hole das Web des Users
		$web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_dep where
		isp_isp_web.doc_id = isp_dep.parent_doc_id and
		isp_isp_web.doctype_id = isp_dep.parent_doctype_id and
		isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");

		$list_allowed = $web["web_list"];
		$listlimit = $web["web_listlimit"];
		$web_doc_id = $web["doc_id"];
		$web_doctype_id = $web["doctype_id"];
		//unset($web);

		// sind f�r dieses Web �berhaupt Listen zugelassen?
		if(!$web["web_list"]){
			$status = "DELETE";
			$errorMessage = $go_api->lng("error_list_nicht_erlaubt");
		}

		// We create a name for the list
		if(!empty($web["web_host"])){
			$list["list_name"] = $web["web_host"].".".$web["web_domain"].".".$list["list_alias"];
		} else {
			$list["list_name"] = $web["web_domain"].".".$list["list_alias"];
		}

		$go_api->db->query("UPDATE isp_isp_list SET list_name = '".$list["list_name"]."' where doc_id = '$doc_id'");

		// Check ob bereits eine Liste mit diesem Namen existiert
		$listcount = $go_api->db->queryOneRecord("SELECT count(doc_id) as doc_count from isp_isp_list where list_name = '".$list["list_name"]."'");

		if($listcount["doc_count"] > 1) {
			$status = "DELETE";
			$errorMessage = $go_api->lng("error_syslist_exist_1")." ".$list["list_name"]." ".$go_api->lng("error_syslist_exist_2");
		}

		// wenn Listlimits gesetzt sind
		if($listlimit >= 0 && $list_allowed == 1) { // Hole Listanzahl
			$listcount = $go_api->db->queryOneRecord("SELECT count(doc_id) as doc_count from isp_isp_list, isp_dep where
			isp_isp_list.doc_id = isp_dep.child_doc_id and isp_isp_list.doctype_id = isp_dep.child_doctype_id and
			isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and isp_dep.child_doctype_id = $doctype_id");

			$listanzahl = $listcount["doc_count"];

			if($listanzahl > $listlimit) {
				$status = "DELETE";
				$errorMessage .= $go_api->lng("error_max_list_web");
			}
		}

		// Check Ob maximale Anzahl User des Resellers erreicht ist
		$user_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
		$resellerid = $user_node["groupid"];

		if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
			// Wenn Resellerlimit erreicht wurde
			if($reseller["limit_listlimit"] >= 0) {
				$reseller_listanzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as reseller_listanzahl from isp_nodes where groupid = '$resellerid' and doctype_id = '".$this->list_doctype_id."'");
				$reseller_listanzahl = $reseller_listanzahl["reseller_listanzahl"];
				if($reseller_listanzahl >= $reseller["limit_listlimit"]) {
					$status = "DELETE";
					$errorMessage .= $go_api->lng("error_max_list_anbieter");
				}
			}
		}

		// stelle sicher dass es keinen User mit dem gleichen "email" Feld gibt
		$user_doctype_id = $this->user_doctype_id;
		$list_doctype_id = $this->list_doctype_id;

		$sql = "SELECT count(*) AS anzahl FROM isp_isp_user, isp_dep where
        isp_isp_list.doc_id = isp_dep.child_doc_id and isp_isp_list.doctype_id = isp_dep.child_doctype_id and
        isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and
                isp_dep.child_doctype_id = $user_doctype_id and isp_isp_list.user_email = '".$list["list_alias"]."'";
		$tmp = $go_api->db->queryOneRecord($sql);

		$sql2 = "SELECT count(*) AS anzahl FROM isp_isp_list, isp_dep where
        isp_isp_list.doc_id = isp_dep.child_doc_id and isp_isp_list.doctype_id = isp_dep.child_doctype_id and
        isp_dep.parent_doctype_id = $web_doctype_id and isp_dep.parent_doc_id = $web_doc_id and
                isp_dep.child_doctype_id = $doctype_id and isp_isp_list.list_alias = '".$list["list_alias"]."'";
		$tmp2 = $go_api->db->queryOneRecord($sql2);

		if($tmp["anzahl"] != 0 OR $tmp2["anzahl"] > 1) {
			$status = "DELETE";
			$errorMessage .= $go_api->lng("error_listalias_exist");
		}

		if($status == "DELETE") {
			// Eintrag l�schen
			$go_api->db->query("DELETE from isp_isp_list where doc_id = '$doc_id'");
			$go_api->db->query("DELETE from isp_nodes where doc_id = '$doc_id' and doctype_id = '$doctype_id'");
			if($die_on_error){
				$go_api->errorMessage($errorMessage.$go_api->lng("weiter_link"));
			} else {
				return $errorMessage;
			}
		} else {
			// Status des User auf 'n' setzen
			$go_api->db->query("UPDATE isp_isp_list SET status = 'n' where doc_id = '$doc_id'");
			//User und Groupid auf die Werte des Web setzen
			$web_doc_id = $web["parent_doc_id"];
			$web_doctype_id = $web["parent_doctype_id"];
			$webnode = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
			$go_api->db->query("UPDATE isp_nodes SET groupid = ".$webnode["groupid"].", userid = ".$webnode["userid"]." where doc_id = '$doc_id' and doctype_id = '$doctype_id'");

			// We create the list
			exec($this->new_list_path." ".$list["list_name"]." ".$list["listadmin_addr"]." ".$list["list_admin_passwd"], $results);

			$filename = "/tmp/Mailman-".$list["list_name"].".conf";

			if ($handle = fopen($filename, 'w')) {
				fwrite($handle, "advertised = 0\n"."subject_prefix = \"[".$list["list_alias"]."]\"\n"."host_name = \"".$this->default_mailman_domain."\"\n"."msg_footer = \"\"" );
			}

			exec($this->config_list_path." -i "."/tmp/Mailman-".$list["list_name"].".conf ".$list["list_name"]);

			foreach ($results as $result) {
				$errorMessage .= $go_api->lng("  $result  ");
			}
		}

		// Server benachrichtigen
		$go_api->uses("isp");
		$server_id = 1;
		$go_api->isp->signal_server($server_id,'insert');

		$this->faktura_insert($doc_id,$web["doc_id"],$user["user_username"]);
		//$go_api->errorMessage($antwort);
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////

	function list_update($doc_id, $doctype_id, $die_on_error = '1') {
		//global $go_api, $go_info,$s,$HTTP_POST_VARS,$old_form_data;
		global $go_api, $old_form_data;
		$change_passwd_path = "/usr/lib/mailman/bin/change_pw";

		$go_api->uses("isp");

		$list = $go_api->db->queryOneRecord("select * from isp_isp_list where doc_id = '$doc_id'");

		//if($old_form_data == $list) return true;
		if($list["list_admin_passwd"] != "") {
			$ml_name = $list["list_name"];
			$passwd = $list["list_admin_passwd"];

			exec("$change_passwd_path -l $ml_name -p \"$passwd\"", $results);
			//$go_api->db->query("UPDATE isp_isp_user SET user_passwort = '$passwort' where doc_id = '$doc_id'");
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	function list_delete($doc_id, $doctype_id, $action, $die_on_error = '1') {

		global $go_api, $go_info;

		###########################
		$web = $go_api->db->queryOneRecord("SELECT * from isp_isp_web, isp_dep where
		isp_isp_web.doc_id = isp_dep.parent_doc_id and
		isp_isp_web.doctype_id = isp_dep.parent_doctype_id and
		isp_dep.child_doctype_id = $doctype_id and isp_dep.child_doc_id = $doc_id");
		$web_doc_id = $web["parent_doc_id"];
		$web_doctype_id = $web["doctype_id"];

		if($action == "do") {
			$go_api->db->query("UPDATE isp_isp_list SET status = 'd' where doc_id = '$doc_id'");
			$this->faktura_delete($doc_id,'do');

			// User in del_status eintragen
			$user_del_status = $go_api->db->queryOneRecord("SELECT * FROM del_status WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
			if(!$user_del_status){
				$list_info = $go_api->db->queryOneRecord("SELECT list_name FROM isp_isp_list WHERE doc_id = '".$doc_id."'");
				$go_api->db->query("INSERT INTO del_status (doc_id, doctype_id, name) VALUES ('".$doc_id."', '".$doctype_id."', '".$list_info["list_name"]."')");
				unset($list_info);
			}

		} else {
			// Pr�fen, ob Liste �berhaupt wiederhergestellt werden darf, ob f�r das Web genug User erlaubt sind
			// Hole Useranzahl
			$list_allowed = $web["web_list"];
			$listlimit = $web["web_listlimit"];

			if($web["web_listlimit"] >= 0){
				$listcount = $go_api->db->queryOneRecord("SELECT count(isp_isp_list.doc_id) as doc_count from isp_dep, isp_isp_list where isp_dep.parent_doc_id = '$web_doc_id' and isp_dep.parent_doctype_id = '$web_doctype_id' and isp_dep.child_doc_id = isp_isp_list.doc_id and isp_dep.child_doctype_id = '".$this->list_doctype_id."'");
				$listanzahl = $listcount["doc_count"];
			} else {
				$listanzahl = -1;
			}

			// Check Ob maximale Anzahl Listen des Resellers erreicht ist
			$user_node = $go_api->db->queryOneRecord("SELECT * from isp_nodes where doc_id = $web_doc_id and doctype_id = $web_doctype_id");
			$resellerid = $user_node["groupid"];

			if($reseller = $go_api->db->queryOneRecord("SELECT * from isp_isp_reseller where reseller_group = $resellerid")) {
				// Wenn Resellerlimit erreicht wurde
				if($reseller["limit_listlimit"] >= 0) {
					$reseller_listanzahl = $go_api->db->queryOneRecord("SELECT count(doc_id) as reseller_listanzahl from isp_nodes where groupid = '$resellerid' and doctype_id = '".$this->list_doctype_id."'");
					$reseller_listanzahl = $reseller_listanzahl["reseller_listanzahl"];
				} else {
					$reseller_listanzahl = -1;
					$reseller["limit_listlimit"] = -1;
				}
			} else {
				$reseller_listanzahl = -1;
				$reseller["limit_listlimit"] = -1;
			}


			if($web["web_list"] && $listanzahl <= $web["web_listlimit"] && $reseller_listanzahl <= $reseller["limit_listlimit"]){
				$go_api->db->query("UPDATE isp_isp_list SET status = 'n' WHERE doc_id = '$doc_id'");
				$this->faktura_delete($doc_id,'undo');
			} else {
				$go_api->db->query("UPDATE isp_nodes SET status = '0' WHERE doc_id = '".$doc_id."' AND doctype_id = '".$doctype_id."'");
				if($listanzahl > $web["web_listlimit"]){
					if($die_on_error){
						$go_api->errorMessage($go_api->lng("error_max_list_web").$go_api->lng("weiter_link"));
					} else {
						return $go_api->lng("error_max_list_web");
					}
				}
				if($reseller_listanzahl > $reseller["limit_listlimit"]){
					if($die_on_error){
						$go_api->errorMessage($go_api->lng("error_max_list_anbieter").$go_api->lng("weiter_link"));
					} else {
						return $go_api->lng("error_max_list_anbieter");
					}
				}
			}
		}
		#############################

		$go_api->db->query("UPDATE isp_isp_web SET status = 'u' where status != 'n' and status != 'd' and doc_id = '$web_doc_id'");

		// Server benachrichtigen
		$go_api->uses("isp");
		$server_id = 1;
		$go_api->isp->signal_server($server_id,'delete: '.$action);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Hilfsfunktionen Faktura
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function faktura_insert($doc_id,$web_id,$beschreibung) {
		global $go_api;
		$sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
		if($sys_config["faktura_on"] == 1) {
			$sql = "INSERT INTO isp_fakt_record (web_id,doc_id,doctype_id,typ,notiz) VALUES ($web_id,$doc_id,".$this->list_doctype_id.",'List','$beschreibung')";
			$go_api->db->query($sql);
		}
	}

	function faktura_update($doc_id,$web_id,$beschreibung) {
		global $go_api;
		$sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
		if($sys_config["faktura_on"] == 1) {
			$sql = "UPDATE isp_fakt_record SET notiz = '$beschreibung' where doc_id = $doc_id and doctype_id = ".$this->list_doctype_id;
			$go_api->db->query($sql);
		}
	}

	function faktura_delete($doc_id,$action) {
		global $go_api;
		$sys_config = $go_api->db->queryOneRecord("SELECT * from sys_config");
		if($sys_config["faktura_on"] == 1) {
			if($action == "do") {
				$sql = "UPDATE isp_fakt_record SET status = 0 where doc_id = $doc_id and doctype_id = ".$this->list_doctype_id;
				$go_api->db->query($sql);
			} else {
				$sql = "UPDATE isp_fakt_record SET status = 1 where doc_id = $doc_id and doctype_id = ".$this->list_doctype_id;
				$go_api->db->query($sql);
			}
		}
	}

}
?>
