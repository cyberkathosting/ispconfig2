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

if(CONFIG_LOADED != 1) die('Direct access not permitted.');


class listadmin
{

function listtype_add($form, $groupid = 0) 
    {
    global $go_api, $go_info;
    
    $this->check($form);
    
    $liste = new liste();
    
    $liste->userid = $go_info["user"]["userid"];
    $liste->groupid = $form["list_groupid"]; 
    $liste->modul = $form["list_modul"];
    $liste->title = $form["list_title"];
    $liste->doctype_id = $form["list_doctype"];
    $liste->limit = $form["list_limit"];
    $liste->description = $form["list_description"];
    $liste->cache = $form["list_cache"];
    $liste->perms = $form["list_perms"];
    $liste->width = $form["list_width"];
    $liste->icon = $form["list_icon"];
    $liste->query = $form["list_query"];
    $liste->orderfield = $form["orderfield"];
    
    $liste->datatable = "";
    
    $liste_serialized = serialize($liste);
    //$this->debug($liste);
    //die();
    // erstellen der DocType Definition
    
    $go_api->db->insert("listtype", array( userid => $liste->userid,
                                          groupid => $liste->groupid,
                                          listtype_modul => $liste->modul,
                                          listtype_datatable => $liste->datatable,
                                          listtype_title => $liste->title,
                                          listtype_def => $liste_serialized,
                                          listtype_doctype_id => $liste->doctype_id));
    $listtype_id = $go_api->db->insertID();
    return $listtype_id;
    }

function listtype_update($listtype_id, $form, $groupid = 0) 
    {
    global $go_api, $go_info;
    
    $liste = $this->listtype_get($listtype_id);
    
    $this->check($form);
    
    $liste->userid = $go_info["user"]["userid"];
    $liste->groupid = $form["list_groupid"]; 
    $liste->modul = $form["list_modul"];
    $liste->title = $form["list_title"];
    $liste->doctype_id = $form["list_doctype"];
    $liste->limit = $form["list_limit"];
    $liste->description = $form["list_description"];
    $liste->cache = $form["list_cache"];
    $liste->perms = $form["list_perms"];
    $liste->width = $form["list_width"];
    $liste->icon = $form["list_icon"];
    $liste->query = $form["list_query"];
    $liste->orderfield = $form["orderfield"];
    
    $liste->datatable = "";
    
    $liste_serialized = serialize($liste);
    
    $go_api->db->update("listtype", array( userid => $liste->userid,
                                          groupid => $liste->groupid,
                                          listtype_modul => $liste->modul,
                                          listtype_datatable => $liste->datatable,
                                          listtype_title => $liste->title,
                                          listtype_def => $liste_serialized,
                                          listtype_doctype_id => $liste->doctype_id), "listtype_id = '$listtype_id'");
    return $listtype_id;
    }

function doctype_delete($doctype_id, $groupid) 
    {
    global $go_api, $go_info;
    }

function listtype_get($listtype_id) 
    {
    global $go_api, $go_info;
    
    $row = $go_api->db->queryOneRecord("SELECT * from listtype where listtype_id = '$listtype_id'");
    return unserialize($row["listtype_def"]);
    }

function row_add($listtype_id, $form) 
    {
    global $go_api, $go_info;
    
    $liste = $this->listtype_get($listtype_id);
    
    $liste->row[] = new row();
    end($liste->row);
    $row_id = key($liste->row);
    $liste->row[$row_id]->title = $form["row_title"];
    $liste->row[$row_id]->edit_button = $form["row_edit"];
    $liste->row[$row_id]->delete_button = $form["row_delete"];
    $fields = array( listtype_def => serialize($liste));
    $go_api->db->update("listtype",$fields,"listtype_id = '$listtype_id'");
    }

function row_update($listtype_id, $row_id, $form) 
    {
    global $go_api, $go_info;
    $liste = $this->listtype_get($listtype_id);
    $liste->row[$row_id]->title = $form["row_title"];
    $liste->row[$row_id]->edit_button = $form["row_edit"];
    $liste->row[$row_id]->delete_button = $form["row_delete"];
    
    $fields = array( listtype_def => serialize($liste));
    $go_api->db->update("listtype",$fields,"listtype_id = '$listtype_id'");
    return $listtype_id;
    }
    
function row_delete($listtype_id, $row_id) 
    {
    global $go_api, $go_info;
        if($listtype_id != "" and $row_id != ""){
            $liste = $this->listtype_get($listtype_id);
            unset($liste->row[$row_id]);
            // variable row löschen, wenn array leer
            if(count($liste->row) == 0) unset($liste->row);
            // $this->debug($doc);
            // die();
            $fields = array( listtype_def => serialize($liste));
            $go_api->db->update("listtype",$fields,"listtype_id = '$listtype_id'");
        }
    }

function element_add($listtype_id, $row_id, $form) 
    {
    global $go_api, $go_info;
    
    $liste = $this->listtype_get($listtype_id);
    
    $liste->row[$row_id]->elements[] = new item($form["name"]);
    end($liste->row[$row_id]->elements);
    $element_id = key($liste->row[$row_id]->elements);
    $liste->row[$row_id]->elements[$element_id]->type = $form["type"];
    $liste->row[$row_id]->elements[$element_id]->width = $form["width"];
    $liste->row[$row_id]->elements[$element_id]->nobr = $form["nobr"];
    $liste->row[$row_id]->elements[$element_id]->css_class = $form["css_class"];
    $liste->row[$row_id]->elements[$element_id]->maxlength = $form["maxlength"];
    //$this->debug($liste);
    // Tabellenfeld erzeugen
    // Document Type updaten
    $fields = array( listtype_def => serialize($liste));
    $go_api->db->update("listtype",$fields,"listtype_id = '$listtype_id'");
    }
    
function element_update($listtype_id, $row_id, $element_id, $form) 
    {
    global $go_api, $go_info;
    $liste = $this->listtype_get($listtype_id);
    
    $liste = $this->listtype_get($listtype_id);
    $liste->row[$row_id]->elements[$element_id]->type = $form["type"];
    $liste->row[$row_id]->elements[$element_id]->width = $form["width"];
    $liste->row[$row_id]->elements[$element_id]->nobr = $form["nobr"];
    $liste->row[$row_id]->elements[$element_id]->css_class = $form["css_class"];
    $liste->row[$row_id]->elements[$element_id]->maxlength = $form["maxlength"];
    //$this->debug($liste);
    // Tabellenfeld erzeugen
    // Document Type updaten
    $fields = array( listtype_def => serialize($liste));
    $go_api->db->update("listtype",$fields,"listtype_id = '$listtype_id'");
    }
    
function element_delete($listtype_id, $row_id, $element_id) 
    {
    global $go_api, $go_info;
        if($listtype_id != "" and $row_id != "" and $element_id != ""){
            $liste = $this->listtype_get($listtype_id);
            unset($liste->row[$row_id]->elements[$element_id]);
            // variable elements löschen, wenn array leer
            if(count($liste->row[$row_id]->elements) == 0) unset($liste->row[$row_id]->elements);
            $fields = array( listtype_def => serialize($liste));
            $go_api->db->update("listtype",$fields,"listtype_id = '$listtype_id'");
        }
    }

function element_flip($listtype_id, $row_id, $element_id) 
    
    {
    global $go_api, $go_info;
    $liste = $this->listtype_get($listtype_id);
    if($element_id != 0) {
        $element1 = $liste->row[$row_id]->elements[$element_id];
        $element2 = $liste->row[$row_id]->elements[$element_id - 1];
        $liste->row[$row_id]->elements[$element_id - 1] = $element1;
        $liste->row[$row_id]->elements[$element_id] = $element2;
        $fields = array( listtype_def => serialize($liste));
        $go_api->db->update("listtype",$fields,"listtype_id = '$listtype_id'");
    }
    }
    
function check($to_check, $max_len = 0)
       
       {
       // $to_check = addslashes($to_check);
       if(!is_array($to_check)) {
       $to_check = strtr($to_check, "\"", "'");
       
            if($max_len != 0)
       	    {
       	    $to_check = substr($to_check,0,$max_len);
       	    }
        }
       return $to_check;
       }

function debug($dbg)
    {
    print("<pre>&quot;"); 
    print_r( $dbg ); 
    print("&quot;</pre>" );
    }

function utime ()
        {
                $time = explode( " ", microtime());
                $usec = (double)$time[0];
                $sec = (double)$time[1];
                return $sec + $usec;
    }
}
?>