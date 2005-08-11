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
//include_once($go_info["server"]["classes_root"] . $go_info["server"]["dir_trenner"] ."db_common.lib.php");

        class db_mysql
        {
                var $dbHost = "";                // hostname of the MySQL server
                var $dbName = "";                // logical database name on that server
                var $dbUser = "";                // database authorized user
                var $dbPass = "";                // user's password
                var $linkId = 0;                // last result of mysql_connect()
                var $queryId = 0;                // last result of mysql_query()
                var $record = array();        // last record fetched
        		var $autoCommit = 1;    // Autocommit Transactions
                var $currentRow;                // current row number
                var $errorNumber = 0;        // last error number
                var $errorMessage = "";        // last error message
                var $errorLocation = "";// last error location
                var $show_error_messages = false;

                // constructor
                function db_mysql()
                {

                        global $go_info;
                        $this->dbHost = $go_info["server"]["db_host"];
                        $this->dbName = $go_info["server"]["db_name"];
                        $this->dbUser = $go_info["server"]["db_user"];
                        $this->dbPass = $go_info["server"]["db_password"];
                        $this->connect();
                }

                // error handler
                function updateError($location)
                {
                        $this->errorNumber = mysqli_errno();
                        $this->errorMessage = mysqli_error();
                        $this->errorLocation = $location;
                        if($this->errorNumber && $this->show_error_messages)
                        {
                                echo('<br /><b>'.$this->errorLocation.'</b><br />'.$this->errorMessage);
                                flush();
                        }
                }

                function connect()
                {
                        if($this->linkId == 0)
                        {
                                $this->linkId = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass);
                                if(!$this->linkId)
                                {
                                        $this->updateError('DB::connect()<br />mysql_connect');
                                        return false;
                                }
                        }
                        return true;
                }

                function query($queryString)
                {
                        if(!$this->connect())
                        {
                                return false;
                        }
                        if(!mysqli_select_db($this->linkId, $this->dbName))
                        {
                                $this->updateError('DB::connect()<br />mysql_select_db');
                                return false;
                        }
                        $this->queryId = @mysqli_query($this->linkId, $queryString);
                        $this->updateError('DB::query('.$queryString.')<br />mysql_query');
                        if(!$this->queryId)
                        {
                                return false;
                        }
                        $this->currentRow = 0;
                        return $this->queryId;
                }

                // returns all records in an array
                function queryAllRecords($queryString)
                {
                        if(!$this->query($queryString))
                        {
                                return false;
                        }
                        $ret = array();
                        while($line = $this->nextRecord())
                        {
                                $ret[] = $line;
                        }
                        return $ret;
                }

                // returns one record in an array
                function queryOneRecord($queryString)
                {
                        if(!$this->query($queryString) || $this->numRows() == 0)
                        {
                                return false;
                        }
                        return $this->nextRecord();
                }

                // returns the next record in an array
                function nextRecord()
                {
            			$this->record = mysqli_fetch_assoc($this->queryId);
                        $this->updateError('DB::nextRecord()<br />mysql_fetch_array');
                        if(!$this->record || !is_array($this->record))
                        {
                                return false;
                        }
                        $this->currentRow++;
                        return $this->record;
                }

                // returns number of rows returned by the last select query
                function numRows()
                {
                        return mysqli_num_rows($this->queryId);
                }

                // returns mySQL insert id
                function insertID()
                {
                        return mysqli_insert_id($this->linkId);
                }

        // Check der variablen
        function check($formfield)
        {
            return addslashes($formfield);
        }


       function insert($tablename,$form,$debug = 0)
       {
         if(is_array($form)){
               foreach($form as $key => $value)
                       {
                       $sql_key .= "$key, ";
            		   $sql_value .= "'".$this->check($value)."', ";
                       }
               $sql_key = substr($sql_key,0,strlen($sql_key) - 2);
        	   $sql_value = substr($sql_value,0,strlen($sql_value) - 2);

               $sql = "INSERT INTO $tablename (" . $sql_key . ") VALUES (" . $sql_value .")";

                       if($debug == 1) echo "SQL-Statement: ".$sql."<br><br>";
                       $this->query($sql);
                       if($debug == 1) echo "mySQL Error Message: ".$this->errorMessage;
          }
       }

       function update($tablename,$form,$bedingung,$debug = 0)
       {

             if(is_array($form)){
           foreach($form as $key => $value)
                       {
                       $insql .= "$key = '".$this->check($value)."', ";
                       }
                       $insql = substr($insql,0,strlen($insql) - 2);
                       $sql = "UPDATE $tablename SET " . $insql . " WHERE $bedingung";
                       if($debug == 1) echo "SQL-Statement: ".$sql."<br><br>";
                       $this->query($sql);
                       if($debug == 1) echo "mySQL Error Message: ".$this->errorMessage;
           }
       }

       function closeConn() {

       }

       function freeResult() {


       }

       function delete() {

       }

       function Transaction($action) {
       //action = begin, commit oder rollback

       }

       /*
       $columns = array(action =>   add | alter | drop
                        name =>     Spaltenname
                        name_new => neuer Spaltenname, nur bei 'alter' belegt
                        type =>     ISPConfig-Meta-Type: int16, int32, int64, double, char, varchar, text, blob
                        typeValue => Wert z.B. bei Varchar
                        defaultValue =>  Default Wert
                        notNull =>   true | false
                        autoInc =>   true | false
                        option =>   unique | primary | index)


       */

       function createTable($table_name,$columns) {
       $index = "";
       $sql = "CREATE TABLE $table_name (";
       foreach($columns as $col){
            $sql .= $col["name"]." ".$this->mapType($col["type"],$col["typeValue"])." ";

            if($col["defaultValue"] != "") $sql .= "DEFAULT '".$col["defaultValue"]."' ";
            if($col["notNull"] == true) {
                $sql .= "NOT NULL ";
            } else {
                $sql .= "NULL ";
            }
            if($col["autoInc"] == true) $sql .= "auto_increment ";
            $sql.= ",";
            // key Definitionen
            if($col["option"] == "primary") $index .= "PRIMARY KEY (".$col["name"]."),";
            if($col["option"] == "index") $index .= "INDEX (".$col["name"]."),";
            if($col["option"] == "unique") $index .= "UNIQUE (".$col["name"]."),";
       }
       $sql .= $index;
       $sql = substr($sql,0,-1);
       $sql .= ")";

       $this->query($sql);
       return true;
       }

       /*
       $columns = array(action =>   add | alter | drop
                        name =>     Spaltenname
                        name_new => neuer Spaltenname, nur bei 'alter' belegt
                        type =>     ISPConfig-Meta-Type: int16, int32, int64, double, char, varchar, text, blob
                        typeValue => Wert z.B. bei Varchar
                        defaultValue =>  Default Wert
                        notNull =>   true | false
                        autoInc =>   true | false
                        option =>   unique | primary | index)


       */
       function alterTable($table_name,$columns) {
       $index = "";
       $sql = "ALTER TABLE $table_name ";
       foreach($columns as $col){
            if($col["action"] == 'add') {
                $sql .= "ADD ".$col["name"]." ".$this->mapType($col["type"],$col["typeValue"])." ";
            } elseif ($col["action"] == 'alter') {
                $sql .= "CHANGE ".$col["name"]." ".$col["name_new"]." ".$this->mapType($col["type"],$col["typeValue"])." ";
            } elseif ($col["action"] == 'drop') {
                $sql .= "DROP ".$col["name"]." ";
            }
            if($col["action"] != 'drop') {
            if($col["defaultValue"] != "") $sql .= "DEFAULT '".$col["defaultValue"]."' ";
            if($col["notNull"] == true) {
                $sql .= "NOT NULL ";
            } else {
                $sql .= "NULL ";
            }
            if($col["autoInc"] == true) $sql .= "auto_increment ";
            $sql.= ",";
            // key Definitionen
            if($col["option"] == "primary") $index .= "PRIMARY KEY (".$col["name"]."),";
            if($col["option"] == "index") $index .= "INDEX (".$col["name"]."),";
            if($col["option"] == "unique") $index .= "UNIQUE (".$col["name"]."),";
            }
       }
       $sql .= $index;
       $sql = substr($sql,0,-1);

       //die($sql);
       $this->query($sql);
       return true;
       }

       function dropTable($table_name) {
       	$this->check($table_name);
       	$sql = "DROP TABLE '". $table_name."'";
       	return $this->query($sql);
       }

       // gibt Array mit Tabellennamen zurück
       function getTables($database_name) {
            $sql = "SHOW TABLES FROM ".$database_name;
			$result = $this->query($sql);
			$i = 0;
			while($row = mysqli_fetch_row($result)) {
				$tb_names[$i] = $row[0];
				$i++
			}
            return $tb_names;
       }

       // gibt Feldinformationen zur Tabelle zurück
       /*
       $columns = array(action =>   add | alter | drop
                        name =>     Spaltenname
                        name_new => neuer Spaltenname, nur bei 'alter' belegt
                        type =>     ISPConfig-Meta-Type: int16, int32, int64, double, char, varchar, text, blob
                        typeValue => Wert z.B. bei Varchar
                        defaultValue =>  Default Wert
                        notNull =>   true | false
                        autoInc =>   true | false
                        option =>   unique | primary | index)


       */

       function tableInfo($table_name) {

       global $go_api,$go_info;
       // Tabellenfelder einlesen

        if($rows = $go_api->db->queryAllRecords("SHOW FIELDS FROM ".$table_name)){
        foreach($rows as $row) {
            $name = $row[0];
            $default = $row[4];
            $key = $row[3];
            $extra = $row[5];
            $isnull = $row[2];
            $type = $row[1];


            $column = array();

            $column["name"] = $name;
            //$column["type"] = $type;
            $column["defaultValue"] = $default;
            if(stristr($key,"PRI")) $column["option"] = "primary";
            if(stristr($isnull,"YES")) {
                $column["notNull"] = false;
            } else {
               $column["notNull"] = true;
            }
            if($extra == 'auto_increment') $column["autoInc"] = true;


            // Type in Metatype umsetzen

            if(stristr($type,"int(")) $metaType = 'int32';
            if(stristr($type,"bigint")) $metaType = 'int64';
            if(stristr($type,"char")) {
                $metaType = 'char';
                $tmp_typeValue = explode('(',$type);
                $column["typeValue"] = substr($tmp_typeValue[1],0,-1);
            }
            if(stristr($type,"varchar")) {
                $metaType = 'varchar';
                $tmp_typeValue = explode('(',$type);
                $column["typeValue"] = substr($tmp_typeValue[1],0,-1);
            }
            if(stristr($type,"text")) $metaType = 'text';
            if(stristr($type,"double")) $metaType = 'double';
            if(stristr($type,"blob")) $metaType = 'blob';


            $column["type"] = $metaType;

        $columns[] = $column;
        }
            return $columns;
        } else {
            return false;
        }


        //$this->createTable('tester',$columns);

        /*
        $result = mysql_list_fields($go_info["server"]["db_name"],$table_name);
        $fields = mysql_num_fields ($result);
        $i = 0;
        $table = mysql_field_table ($result, $i);
        while ($i < $fields) {
            $name  = mysql_field_name  ($result, $i);
            $type  = mysql_field_type  ($result, $i);
            $len   = mysql_field_len   ($result, $i);
            $flags = mysql_field_flags ($result, $i);
            print_r($flags);

            $columns = array(name => $name,
                        type =>     "",
                        defaultValue =>  "",
                        isnull =>   1,
                        option =>   "");
            $returnvar[] = $columns;

            $i++;
        }
        */



       }

       function mapType($metaType,$typeValue) {
       global $go_api;
       $metaType = strtolower($metaType);
       switch ($metaType) {
       case 'int16':
            return 'smallint';
       break;
       case 'int32':
            return 'int';
       break;
       case 'int64':
            return 'bigint';
       break;
       case 'double':
            return 'double';
       break;
       case 'char':
            return 'char';
       break;
       case 'varchar':
            if($typeValue < 1) $go_api->errorMessage("Datenbank Fehler: Für diesen Datentyp ist eine Längenangabe notwendig.");
            return 'varchar('.$typeValue.')';
       break;
       case 'text':
            return 'text';
       break;
       case 'blob':
            return 'blob';
       break;
       }
       }

        }

?>