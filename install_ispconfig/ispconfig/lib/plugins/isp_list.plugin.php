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

#############################################################
#
# ISPConfig Plugin
# Version 1.0
#
# Plugin Name: ISP-List
# Version: 1.0
# Autor: Jonas Schwarz
# Datum: 21.11.2006
#
#############################################################

class isp_list_plugin {

	var	$list_members_path = "/usr/lib/mailman/bin/list_members";
	var $rem_members_path = "/usr/lib/mailman/bin/remove_members";
	var $add_members_path = "/usr/lib/mailman/bin/add_members";

	function show ($doc_id, $doctype_id, $groupid = 0)
	{
		global $go_info, $go_api, $next_tree_id;

		$list = $go_api->db->queryOneRecord("select * from isp_isp_list where doc_id = '$doc_id'");
		$ml_name = $list["list_name"];
		$list_members_path = $this->list_members_path;

		unset($results);

		if ($ml_name){
			$html_out = '<nobr><b>&nbsp; Delete E-Mail address: </b></nobr><br />';
			exec("$list_members_path ".escapeshellcmd($ml_name), $results);


			foreach($results as $result) {
				$html_out .= "<input type=\"checkbox\" name=\"plugin[emails][]\" value=\"$result\" />$result<br />\n";

			}
			$html_out .= "<hr noshade size=\"1\">";
			
			$html_out .= "<nobr><b>&nbsp; Add E-Mail address: </b></nobr><br />\n";
		$html_out .= "<input type=\"text\" name=\"plugin[new_emails][]\" /><br />\n";
		}

		//echo "<form action=\"unsubscribe.php\" method=\"POST\">";
		//$html_out = '<table width="100%" bgcolor="#EEEEEE"><tr>     <td colspan="2">';

		//$count = count($results);
		//$i = 0;
		//unset($max_strlen);


		//foreach($results as $result) {

		/*if ($i == 0){
		$emails = "$result";
		} else {
		$emails .= "\n$result";
		}
		$i++;
		$cur_strlen = strlen($result);*/

		/*if ($cur_strlen > $max_strlen) {
		$max_strlen = $cur_strlen;
		}*/

		//$html_out .= "<input type=\"checkbox\" name=\"plugin[emails][]\" value=\"$result\" />$result<br />\n";
		//}
		//$html_out .= '<textarea rows="'.$count.'" cols="'.$max_strlen.'">'.$emails.'</textarea>';

		return $html_out;
	}

	function insert($doc_id, $doctype_id, $groupid = 0)
	{
		return true;
	}

	function update($doc_id, $doctype_id, $groupid = 0)
	{
		global $HTTP_POST_VARS, $go_api;
		//global $go_info, $go_api, $HTTP_POST_VARS, $next_tree_id, $form_changed;

		$list = $go_api->db->queryOneRecord("select * from isp_isp_list where doc_id = '$doc_id'");
		$ml_name = $list["list_name"];
		$rem_members_path = $this->rem_members_path;
		$add_members_path = $this->add_members_path;

		$plugin_vars = $HTTP_POST_VARS["plugin"];
		$stat = true;

		if($plugin_vars["emails"]) {
			$add_str = "";
			foreach($plugin_vars["emails"] as $email) {
				$add_str .= " $email";
			}
			$exec = exec("$rem_members_path ".escapeshellcmd($ml_name.$add_str), $results);
			if (!$exec){
				$stat = false;
			}
		}

		if ($plugin_vars["new_emails"][0]) { // Ugly Need to change that
			$add_str = "echo -e '";
			foreach($plugin_vars["new_emails"] as $email) {
				if(!preg_match("/^([a-z0-9_\+\.-]+\@[a-z0-9\.-]+)$/",$email)) {
					$go_api->errorMessage('<b>'.$go_api->lng("Feld").': Username</b><br>'.$go_api->lng("Der Username muss aus min. 4, max 32 Buchstaben oder Zahlen bestehen, wobei er mit einem Buchstaben beginnen muss.") . "<br>&nbsp;<br>");
				} else {
					$add_str .= escapeshellcmd($email)."\\n";
				}
			}

			$add_str .= "'";
			$exec = exec("$add_str | $add_members_path -r - -w n ".escapeshellcmd($ml_name), $results);
			if (!$exec){
				$stat = false;
			}
		}
		return($stat);
	}

	function delete($doc_id, $doctype_id, $groupid = 0)
	{
		return true;
	}

	function undelete($doc_id, $doctype_id, $groupid = 0)
	{
		return true;
	}
}
?>