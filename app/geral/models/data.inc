<?php

require_once 'adodb-time.inc.php';

$_SESSION['cod_lingua_s'] = 1;

if ($_SESSION['cod_lingua_s']==3)
	$data_invertida_g=true;
else
	$data_invertida_g=false;


class Data{
	
	/* ************************************************************************
	 Data2UnixTime - Converte string para UnixTime
	Entrada: $data - string no formato dd/mm/yyyy ou mm/dd/yyyy
	Sa�da: Unixtime da string
	*/
	static function Data2UnixTime($data)
	{
		global $data_invertida_g;
	
		$data=explode("/",$data);
		if ($data_invertida_g){
			return (adodb_mktime(0,0,0,$data[0],$data[1],$data[2]));
		}
		else{
			return (adodb_mktime(0,0,0,$data[1],$data[0],$data[2]));
		}
	}
	
	static function Data2MySql($data) {
		
		$data = explode("/",$data);
		return $data[2] . "-" . $data[1] . "-" . $data[0];
	}
	
	/* ************************************************************************
	UnixTime2DataHora - Converte inteiro UnixTime para data e hora
	Entrada: $timestamp - Unixtime a ser convertido
	Sa�da: string no formato: dd/mm/yyyy HH:MM:SS ou mm/dd/yyyy HH:MM:SS
	*/
	static function UnixTime2DataHora($timestamp)
	{
	global $data_invertida_g;
	
	$temp=adodb_getdate($timestamp);
	$temp=Data::CorrigeZeros($temp);
	if ($data_invertida_g)
		$data=$temp['mon']."/".$temp['mday']."/".$temp['year']." ".$temp['hours'].":".$temp['minutes'].":".$temp['seconds'];
		else
				$data=$temp['mday']."/".$temp['mon']."/".$temp['year']." ".$temp['hours'].":".$temp['minutes'].":".$temp['seconds'];
				return ($data);
	}
	
	static function CorrigeZeros($data)
	{
		foreach ($data as $cod => $temp)
		if (strlen($data[$cod])<2)
			$data[$cod]="0".$data[$cod];
		return ($data);
	}
	
	/* ************************************************************************
	 UnixTime2Data - Converte inteiro UnixTime para data
	Entrada: $timestamp - Unixtime a ser convertido
	Sa�da: string no formato: dd/mm/yyyy ou mm/dd/yyyy
	*/
	static function UnixTime2Data($timestamp)
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