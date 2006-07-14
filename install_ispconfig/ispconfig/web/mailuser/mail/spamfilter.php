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
require_once('../../../lib/config.inc.php');
require_once('../lib/app.inc.php');

if(!$_SESSION["s"]["userid"]) {
        header("Location: ../mail/index.php");
        exit;
}

$app->uses('tpl,form');
$app->tpl->newTemplate("main.tpl.htm");
$app->tpl->setInclude('content',"spamfilter.tpl.htm");

// Tabellendefinition und Formdefinition laden
$app->form->loadTableDef("table/user.table.php");
$app->form->loadFormDef("form/spamfilter.form.php");

$server = $app->db->queryOneRecord("SELECT * FROM isp_server");
$app->tpl->setVar($server);

if($server["spamfilter_enable"] != 1) die($app->lng("txt_access_forbidden"));

$id = $_SESSION["s"]["userid"];

if(count($_POST) > 1) {

        $rec = array();
        $rec["user_mailscan"] = $_POST["user_mailscan"];
        $rec["antivirus"] = $_POST["antivirus"];
        $rec["user_spamfilter"] = $_POST["user_spamfilter"];
        $rec["spam_strategy"] = $_POST["spam_strategy"];
        $rec["spam_rewrite_subject"] = $_POST["spam_rewrite_subject"];
        $rec["spam_hits"] = escapeshellcmd(substr($_POST["spam_hits"],0,7));
        $rec["spam_subject_tag"] = substr($_POST["spam_subject_tag"],0,50);
        $rec["use_uribl"] = $_POST["use_uribl"];
        $rec["spam_whitelist"] = substr($_POST["spam_whitelist"],0,10000);
        $rec["spam_blacklist"] = substr($_POST["spam_blacklist"],0,10000);
        $rec["status"] = 'u';

        $sql = $app->form->getSQL($rec,"UPDATE",$id);

        if($app->form->errorMessage == '') {

                $app->db->query($sql);
                @touch("/home/admispconfig/ispconfig/.run");

                // Liste anzeigen, wenn speichern geklickt wurde
            if($_REQUEST["next_tab"] == '') {
                    header("Location: index.php");
                exit;
            }

        } else {
                $app->tpl->setVar("error","<font color=\"red\"><b>".$app->lng("txt_error").":</b><br>".$app->form->errorMessage."</font>");
                $app->tpl->setVar($_POST);
        }
}


$sql = "SELECT * FROM isp_isp_user WHERE doc_id = '$id'";
// bestehenden Datensatz anzeigen
if($app->form->errorMessage == '') {
        $record = $app->db->queryOneRecord($sql);
} else {
        $record = $app->form->encode($_POST);
}
$record_orig = $record;
// Daten umwandeln
$record = $app->form->getHTML($record,'EDIT');

$app->tpl->setVar($record);

// Formular und Tabs erzeugen
$app->form->showForm();

// Defaultwerte setzen
$app->tpl_defaults();

// Template parsen
$app->tpl->pparse();

?>