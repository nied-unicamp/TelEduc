<?php

require_once 'adodb-time.inc.php';

$_SESSION['cod_lingua_s'] = 1;

if ($_SESSION['cod_lingua_s']==3)
	$data_invertida_g=true;
else
	$data_invertida_g=false;
/**
 * Classe Data do modulo geral
 * @author     TelEduc
 * @copyright  20014 TelEduc
 * @license    http://teleduc.org.br/
 */
class Data{
	
	/* ************************************************************************
	 Data2UnixTime - Converte string para UnixTime
	Entrada: $data - string no formato dd/mm/yyyy ou mm/dd/yyyy
	Saï¿½da: Unixtime da string
	*/
        
        /**
         * Data2UnixTime - Converte string para UnixTime
         * 
         * @param String $data String no formato dd/mm/yyyy ou mm/dd/yyyy
         * @return String Unixtime da string
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
	/**
         * Mascara para data no formato invertido, usado no mysql. 
         * 
         * @param string $data String no formato dd/mm/yyyy 
         * @return string Data no formato yyyy-mm-dd
         */
	static function Data2MySql($data) {
		
		$data = explode("/",$data);
		return $data[2] . "-" . $data[1] . "-" . $data[0];
	}
	
	/* ************************************************************************
	UnixTime2DataHora - Converte inteiro UnixTime para data e hora
	Entrada: $timestamp - Unixtime a ser convertido
	Saï¿½da: string no formato: dd/mm/yyyy HH:MM:SS ou mm/dd/yyyy HH:MM:SS
	*/
        
        /**
         * Converte inteiro UnixTime para data e hora
         * 
         * @param String $timestamp  Unixtime a ser convertido
         * @return string dd/mm/yyyy HH:MM:SS ou mm/dd/yyyy HH:MM:SS
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
        
        
        /**
         * Caso a data esteja com zeros faltantes, como por exemplo em 2/2/2002, serão adicionados 0 para ficar no formato 02/02/2002 
         * 
         * @param Array $data Datas separadas por indexadores, como em $data['dia']=5, $data['mes']=5 
         * @return array $data Datas separadas por indexadores, como em $data['dia']=5, $data['mes']=5 
         */
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
	Saï¿½da: string no formato: dd/mm/yyyy ou mm/dd/yyyy
	*/
        
        /**
         * Converte inteiro UnixTime para data
         * 
         * @param String $timestamp Unixtime a ser convertido
         * @return type no formato: dd/mm/yyyy ou mm/dd/yyyy
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