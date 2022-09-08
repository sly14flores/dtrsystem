<?php

class pageNav {

var $total_num_rows;
var $per_page;
var $total_pages;
var $param; // *
var $cur; // *
var $d;
var $min; // *

var $str_first;
var $str_previous;
var $cur_page;
var $str_next;
var $str_last;
var $inNav;

function __construct($param,$per_page,$cur,$d) {

	$this->min = 1;
	$this->param = $param;
	$this->per_page = $per_page;	
	$this->cur = $cur;
	$this->d = $d;
	
}

function row_page($sql) {

global $db_con;

db_connect();
$rs = $db_con->query($sql);
$rec = $rs->fetch_array();
$this->total_num_rows = $rec[0];
db_close();

$this->total_pages = ceil($this->total_num_rows / $this->per_page);
if ($this->d == 3) $this->cur = $this->total_pages; // update to new total pages in case new record is added

$offset = ($this->cur - 1) * $this->per_page;

$row_page = " LIMIT $offset, ".$this->per_page;

return $row_page;
	
}

function getNav($pre,$suf) {

	if ($this->total_num_rows <= $this->per_page) return "";

	$this->str_first = '<li><a href="javascript: content(0,' . $this->param . ');">&laquo;</a></li>';
	$this->str_previous = '<li><a href="javascript: content(-1,' . $this->param . ');">&lsaquo;</a></li>';
	$this->cur_page = '<li class="active"><a href="javascript: content(2,' . $this->param . ');">' . $this->cur . '</a></li>';
	$this->str_next = '<li><a href="javascript: content(1,' . $this->param . ');">&rsaquo;</a></li>';
	$this->str_last = '<li><a href="javascript: content(3,' . $this->param . ');">&raquo;</a></li>';

	if ($this->cur == $this->min) {
		$this->str_first = '<li class="disabled"><a href="javascript: content(2,' . $this->param . ');">&laquo;</a></li>';
		$this->str_previous = '<li class="disabled"><a href="javascript: content(2,' . $this->param . ');">&lsaquo;</a></li>';
	}
	if ($this->cur == $this->total_pages) {
		$this->str_next = '<li class="disabled"><a href="javascript: content(2,' . $this->param . ');">&rsaquo;</a></li>';
		$this->str_last = '<li class="disabled"><a href="javascript: content(2,' . $this->param . ');">&raquo;</a></li>';
	}
	
	$this->inNav = $pre;
	$this->inNav .= '<ul class="pagination">';
	$this->inNav .= $this->str_first;
	$this->inNav .= $this->str_previous;
	$this->inNav .= $this->cur_page;
	$this->inNav .= $this->str_next;
	$this->inNav .= $this->str_last;
	$this->inNav .= '</ul>';
	$this->inNav .= $suf;	

	return $this->inNav;
		
}

}

class dbase {

var $table;
var $START_T;
var $END_T;
var $sql;
var $sql_arr;
var $fields;
var $values;
var $fields_values;
var $last_auto_id;

	function __construct($table) {

	$this->table = $table;
	$this->START_T = "START TRANSACTION;";
	$this->END_T = "COMMIT;";

	}

	function auto_incr_one() {

	$this->sql = "ALTER TABLE " . $this->table . " AUTO_INCREMENT = 1";

	}

	function add($data) {

		foreach ($data as $field => $value) {
			$this->fields .= $field . ",";
			if (is_numeric($value) || ($value == "CURRENT_TIMESTAMP")) $this->values .= addslashes($value) . ",";
			else $this->values .= "'" . addslashes($value) . "'" . ",";
		}

	$this->fields = substr($this->fields,0,strlen($this->fields)-1);
	$this->values = substr($this->values,0,strlen($this->values)-1);
	$this->sql = "INSERT INTO " . $this->table . " (" . $this->fields . ") VALUES (" . $this->values . ")";

	}

	function addOneToMany($data) {

	$this->sql = "";
	$this->fields = "";
	$this->values = "";
	
		$field_count = 0;
		foreach ($data as $field => $value) {
			foreach ($value as $field1 => $value1) {
				$this->fields .= $field1 . ",";
				$field_count++;
			}
			if ($field_count == count($value)) break;
		}

		foreach ($data as $field => $value) {
			$this->values .= "(";
			foreach ($value as $field1 => $value1) {			
				// if (is_numeric($value1) || ($value1 == "CURRENT_TIMESTAMP")) $this->values .= addslashes($value1) . ",";
				// else $this->values .= "'" . addslashes($value1) . "'" . ",";
				$this->values .= "'" . addslashes($value1) . "'" . ",";
			}
			$this->values = substr($this->values,0,strlen($this->values)-1);
			$this->values .= "),";
		}
	
	$this->fields = substr($this->fields,0,strlen($this->fields)-1);
	$this->values = substr($this->values,0,strlen($this->values)-1);
	$this->sql = "INSERT INTO " . $this->table . " (" . $this->fields . ") VALUES " . $this->values;
	
	}	
	
	function update($data,$arr_id) {

	$this->fields_values = "";

		foreach ($data as $field => $value) {
			if (is_numeric($value) || ($value == "CURRENT_TIMESTAMP")) $this->fields_values .= " $field = " . addslashes($value) . ",";
			else $this->fields_values .= " $field = '" . addslashes($value) . "',";
		}

	$this->fields_values = substr($this->fields_values,0,strlen($this->fields_values)-1);
	$this->sql = "UPDATE " . $this->table . " SET" . $this->fields_values . " WHERE " . array_keys($arr_id)[0] . " = " . array_values($arr_id)[0];
		
	}
	
	function updateOneToMany($data,$pk) {
	
	$this->sql_arr = [];
	$this->fields_values = "";
	$affected_row = "";
		
		foreach ($data as $field => $value) {
			$this->sql = "UPDATE " . $this->table . " SET ";
			$affected_row = " WHERE $pk = ";
			$this->fields_values = "";
			foreach ($value as $field1 => $value1) {
				if ($field1 == $pk) {
						$affected_row .= $value1;
						continue;
				}
				// if (is_numeric($value1) || ($value1 == "CURRENT_TIMESTAMP")) $this->fields_values .= " $field1 = " . addslashes($value1) . ",";
				// else $this->fields_values .= " $field1 = '" . addslashes($value1) . "',";
				$this->fields_values .= " $field1 = '" . addslashes($value1) . "',";
			}
			$this->fields_values = substr($this->fields_values,0,strlen($this->fields_values)-1);
			$this->sql .= $this->fields_values . " $affected_row";
			$this->sql_arr[] = $this->sql;
		}	
		
	}
	
	function async_add($field,$value,$key,$id) {
	
		$this->sql = "UPDATE " . $this->table . " SET " . $field . " = '" . addslashes($value) . "' WHERE $key = $id";
	
	}

	function async_add_num($field,$value,$key,$id) {
	
		$this->sql = "UPDATE " . $this->table . " SET " . $field . " = " . $value . " WHERE $key = $id";
	
	}	
	
	function delete($data) {

		foreach ($data as $field => $value) {
			$this->fields .= $field;
			$this->values .= $value;
		}

	$this->sql = "DELETE FROM " . $this->table . " WHERE " . $this->fields . " IN (" . $this->values .")";

	}

	function execute() {

	global $db_con;
				
		db_connect();
		$db_con->query($this->START_T);
		$db_con->query($this->sql);
		$db_con->query($this->END_T);
		db_close();
		
	}

	function execute_multi() {

	global $db_con;		
		db_connect();
		foreach ($this->sql_arr as $sql) {
		$db_con->query($this->START_T);
		$db_con->query($sql);
		$db_con->query($this->END_T);
		}
		db_close();
		
	}	
	
	function sql_get_id() {
	
	global $db_con;
	
		db_connect();
		$db_con->query($this->START_T);
		$db_con->query($this->sql);	
		$this->last_auto_id = $db_con->insert_id;
		$db_con->query($this->END_T);
		db_close();
		
	// return $this->last_auto_id;
	
	}
	
	function debug() {

	echo $this->sql;

	}
	
	function debug_r() {

	return $this->sql;

	}	

}

function left($str, $length) {
     return substr($str, 0, $length);
}

function right($str, $length) {
     return substr($str, -$length);
}

function convertToHoursMins($time, $format = '%d:%d') {
    settype($time, 'integer');
    if ($time < 1) {
        return;
    }
    $hours = floor($time/60);
	$rhours = $hours/60;
    $minutes = $hours%60;
    return sprintf($format, $rhours, $minutes);
}

function toHours($time, $format = '%d') {
    settype($time, 'integer');
    if ($time < 1) {
        return;
    }
    $hours = floor($time/60);
	$rhours = $hours/60;
    $minutes = $hours%60;
    return sprintf($format, $rhours);
}

function toMinutes($time, $format = '%d') {
    settype($time, 'integer');
    if ($time < 1) {
        return;
    }
    $hours = floor($time/60);
	$rhours = $hours/60;
    $minutes = $hours%60;
    return sprintf($format, $minutes);
}

function enc($q) {
    $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
    $qEncoded = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
    return( $qEncoded );
}

function dec($q) {
    $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
    $qDecoded = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
    return( $qDecoded );
}

function trade_mark() {

global $db_con;
$tm = "";

$sql = "SELECT trademark FROM about WHERE about_id = 1";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$k = 'qJB0rGtIn5UB1xG03efyCp';
	$tm = dec($rec['trademark']);
}
db_close();	

return $tm;
	
}

function searchInArray($arr,$str) {

$found = false;

	foreach($arr as $ind) {
		if ($ind == $str) {
			$found = true;
			break 1;
		}
	}

return $found;

}

function debug_log($txt) {

$file = fopen("debug.txt","a+");
fwrite($file,$txt."\r\n");
fclose($file);

}

?>