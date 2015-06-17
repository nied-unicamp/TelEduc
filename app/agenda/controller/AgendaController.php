<?php

require_once '../../../lib/Conexao.php';
require_once '../../../lib/teleduc.inc';
require_once '../../../lib/data.php';
require_once '../model/AgendaItem.php';
require_once '../dao/AgendaItemDao.php';

class AgendaController{

	function testaConexao(){
		
		$conexao = new Conexao();
		
		return $conexao->conectar($_SESSION['dbhost'], $_SESSION['dbnamebase'], $_SESSION['dbuser'], $_SESSION['dbpassword']);
		
	}
	
	function criaAgenda(){
		
		$conexao = new Conexao();
		
		$conexao->conectar($_SESSION['dbhost'], $_SESSION['dbnamebase'], $_SESSION['dbuser'], $_SESSION['dbpassword']);
		
		$data = new Data();
		
		$data_criacao = $data->Data2UnixTime('17/06/2015');
		$data_publicacao = $data->Data2UnixTime('18/06/2015');
		$inicio_edicao = $data->Data2UnixTime('18/06/2015');
		
		$agenda_item = new Agenda_Item();
		
		$agenda_item->setAllId(7, 1, 1, 'Teste', 'Testando insert pelo código','A', $data_criacao, $data_publicacao, 'L', $inicio_edicao);
		
		$dao = new Agenda_ItemDao();
		
		if ($dao->create($conexao, $agenda_item)){
		 	echo ('inseriu');
		}
	}
}
