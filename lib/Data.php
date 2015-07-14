<?php

require_once '../../../lib/adodb-time.inc.php';

if ($_SESSION['cod_lingua_s']==3)
	$data_invertida_g=true;
else
	$data_invertida_g=false;

class Data{
	
	function CorrigeZeros($data)
	{
		foreach ($data as $cod => $temp)
		if (strlen($data[$cod])<2)
			$data[$cod]="0".$data[$cod];
		return ($data);
	}
	
	function Data2UnixTime($data) {
	
		$data = explode("/", $data);
		if ($data_invertida_g) {
			return (adodb_mktime(0, 0, 0, $data[0], $data[1], $data[2]));
		} else {
			return (adodb_mktime(0, 0, 0, $data[1], $data[0], $data[2]));
		}
	}
	
	/* ************************************************************************
	 UnixTime2Data - Converte inteiro UnixTime para data
	Entrada: $timestamp - Unixtime a ser convertido
	Saida: string no formato: dd/mm/yyyy ou mm/dd/yyyy
	*/
	function UnixTime2Data($timestamp)
	{
		global $data_invertida_g;
	
		$temp=adodb_getdate($timestamp);
		$temp=Data::CorrigeZeros($temp);
		if ($data_invertida_g)
			$data=$temp['mon']."/".$temp['mday']."/".$temp['year'];
		else
			$data=$temp['mday']."/".$temp['mon']."/".$temp['year'];
		return ($data);
	}
	
	
}