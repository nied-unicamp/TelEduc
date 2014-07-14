<?php
/* if (isset($_POST['acao'])){
	$acao = $_POST['acao'];
	switch($acao){
		case 'CadastraDados':
			Teste::FuncaodeTeste();
			//Inicial::CadastraDadosUsuario($erro1, $erro2, $erro3, $texto);
			break;
	}
}
class Teste{
	static function FuncaodeTeste(){ */
require_once 'acesso_sql.inc';
	
		$sock = AcessoSQL::Conectar("");
	
		//$dados = $_POST['dados'];
		$query = "insert into Teste values (1, '".$_POST['nome']."')";
		$res = AcessoSQL::Enviar($sock, $query);
		
		AcessoSQL::Desconectar($sock);