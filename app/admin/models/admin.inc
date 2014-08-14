<?php
class Admin{
	/* **********************************************************************
	 PreparaBoldLink - Monta um link na p�gina (completa com RetornaSessionID()
	   Entrada: $nome - nome do link
	 		$endereco - Endereco a ser chamado
	 		$parametros - parametros a serem passados
	   Saida: Monta o a href
	 		*/
	static function PreparaBoldLink($nome,$endereco,$parametros)
	{
		echo("<li><a href=".$endereco."".$parametros." target=_top>".$nome."</a></li>\n");
	}
	
	
}