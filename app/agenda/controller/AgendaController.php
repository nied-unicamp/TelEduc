<?php

require_once '../../../lib/Conexao.php';
require_once '../../../lib/data.php';
require_once '../model/AgendaItem.php';
require_once '../dao/AgendaItemDao.php';

class AgendaController{
	
	function testaConexao(){
		
		$conexao = new Conexao();
		
		$conexao->Conectar();
		
		echo ' status='.$conexao->status;
	}
	
	function criaAgenda(){
		
		$data = new Data();
		
		$data_criacao = $data->Data2UnixTime('18/06/2015');
		$data_publicacao = $data->Data2UnixTime('19/06/2015');
		$inicio_edicao = $data->Data2UnixTime('20/06/2015');
		
		$agenda_item = new Agenda_Item();
		
		$agenda_item->setAll(1, 1, 'Teste2', 'Testando insert pelo código','F', $data_criacao, $data_publicacao, 'L', $inicio_edicao);
		
		$dao = new Agenda_ItemDao();
		
		return $dao->create($agenda_item);
	}

	function listaAgendas(){
		
		$dao = new Agenda_ItemDao();
		
		return $dao->loadAll();
		 
	}
	
	function listaAgendasSituacao($situacao){
	
		$dao = new Agenda_ItemDao();
	
		return $dao->loadAllSituacao($situacao);
	}
	
	function apagaAgenda(){
		
		$dao = new Agenda_ItemDao();
		
		return $dao->delete(8);	
	}
	
	function atualizaAgenda(){
		
		$data = new Data();
		
		$data_criacao = $data->Data2UnixTime('18/06/2015');
		$data_publicacao = $data->Data2UnixTime('19/06/2015');
		$inicio_edicao = $data->Data2UnixTime('20/06/2015');
		
		$agenda_item = new Agenda_Item();
		
		$agenda_item->setAllId(26, 1, 1, 'Teste2Alterado', 'Testando alteração pelo código','F', $data_criacao, $data_publicacao, 'L', $inicio_edicao);
		
		$dao = new Agenda_ItemDao();
		
		$dao->save($agenda_item);
		
	}
}
