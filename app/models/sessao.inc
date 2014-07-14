<?php

//require_once "acesso_sql.php";

class Sessao {
	/* *******************************************************************
	 RetornaSessionID - Retorna o identificador de session usado pelo PHP
	Usar em toda chamada a arquivos (arquivo.php?".RetornaSessionID."&...)
	Saida: String com o Session (se necess�io, ou vazio se Cookie ativo)
	*/
	static function RetornaSessionID()
	{
		return SID;
	}
	
	/* *******************************************************************
	 RetornaSessionIDInput - retorna a linha <input type=hidden name=PHPSESSID value=....>
	Saida: String com o Session (se necess�io, ou vazio se Cookie ativo)
	*/
	static function RetornaSessionIDInput()
	{
		return "<input type=\"hidden\" name=\"PHPSESSID\" value=\"".session_id()."\" />\n";
	}
	
	
	/* Se nenhuma lingua foi definida, portugu� �o padr� */
	static function CodLinguaS() {
		
		$sock=AcessoSQL::Conectar("");
		$query="select valor from Config where item='lingua'";
		$res=AcessoSQL::Enviar($sock,$query);
		/*AcessoSQL::Desconectar($sock);*/
		$linha=AcessoSQL::RetornaLinha($res);
		if (isset($linha['valor']))
			$retorno = $linha['valor'];
		else
			$retorno = 1;
	
		if (isset($cod_ferramenta))
			$cod_ferramenta_s=$cod_ferramenta;
		return $retorno;
	}
}


?>