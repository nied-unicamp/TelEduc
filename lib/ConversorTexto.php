<?php

class ConversorTexto{
	
	/* *********************************************************************
	 VerificaStringQuery - Neutraliza qualquer tipo de SQL Injection em uma dada string
	Entrada: $string - string que será usada em uma query
	Saida: retorna a string tratada contra SQL Injection
	*/
	function VerificaStringQuery($string)
	{
		if (get_magic_quotes_gpc())
		{
			$string = stripslashes($string);
		}
	
		$string = mysql_real_escape_string($string);
	
		return $string;
	}
	
	/* *********************************************************************
	 VerificaNumeroQuery - Verifica se uma variavel contem mesmo um valor numerico
	Entrada: $num - variavel a ser verificada
	Saida: Retorna a propria variavel se a mesma contiver um valor numerico; "" caso contrario.
	*/
	function VerificaNumeroQuery($num)
	{
		if(is_numeric($num))
			return $num;
		else
			return "";
	}
	
	/* *********************************************************************
	 ConverteAspas2BarraAspas - troca ' e " por \' e \"
	Entrada: $linha - string a ser corrigida
	Saida: string corrigida
	*/
	function ConverteAspas2BarraAspas($linha)
	{
		$linha1=implode("\\'",explode("'",$linha));
		return implode('\\"',explode('"',$linha1));
	}
}
?>