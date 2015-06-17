<?php

require_once '../../../lib/adodb-time.inc.php';

class Data{
	
	function Data2UnixTime($data) {
		//global $data_invertida_g;
	
		$data = explode("/", $data);
		//if ($data_invertida_g) {
			//return (adodb_mktime(0, 0, 0, $data[0], $data[1], $data[2]));
		//} else {
			return (adodb_mktime(0, 0, 0, $data[1], $data[0], $data[2]));
		//}
	}
	
	
}