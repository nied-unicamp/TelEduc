<?php

require_once '../../../lib/Conexao.php';
require_once '../../../lib/Data.php';
require_once '../model/AgendaItem.php';
require_once '../dao/AgendaItemDao.php';



class AgendaController{
	
	function Conecta(){
		
		$conexao = new Conexao();
		
		$conexao->Conectar();
		
		echo ' status='.$conexao->status;
	}
	
	/*Cria uma nova agenda a qual é armazenada no banco só com o título 
	retorna um tipo AgendaItem;

	*

	*/
	function criaAgenda($titulo, $codcurso, $codusuario){
		
		
		$conn = new Conexao();

		$data_criacao = $data->Data2UnixTime('09/07/2015');
		$data_publicacao = null;
		$inicio_edicao = $data->Data2UnixTime('09/07/2015');
		
		$agenda_item = new Agenda_Item();
		 
		$dao = new Agenda_ItemDao();
		
		$id= $dao->proxId($conn);

		echo "PROX:".$id."\n";

		$agenda_item->setAll($codcurso, $codusuario, $titulo,'','N', $data_criacao, $data_publicacao, 'L', $inicio_edicao);

		
		// echo ($agenda_item->toString());

		$rs = $dao->create2($conn, $agenda_item);
		
		if($conn){
			$conn->Desconectar();
		}
		return $rs;
	}

	function listaAgendas(){
		
		$dao = new Agenda_ItemDao();
		
		return $dao->loadAll();
		 
	}
	
	function listaAgendasSituacao($cod_curso, $situacao){
	
		$dao = new Agenda_ItemDao();
	
		return $dao->loadAllSituacao($cod_curso, $situacao);
	}
	
	function apagaAgenda($id){
		
		$dao = new Agenda_ItemDao();
		
		return $dao->delete($id);
	}
	
	function atualizaAgenda(){
		
		$data = new Data();
		
		$data_criacao = $data->Data2UnixTime('18/06/2015');
		$data_publicacao = $data->Data2UnixTime('19/06/2015');
		$inicio_edicao = $data->Data2UnixTime('20/06/2015');
		
		$agenda_item = new Agenda_Item();
		

		$agenda_item->setAllId(26, 1, 1, 'Teste2Alterado', 'Testando altera��o pelo c�digo','F', $data_criacao, $data_publicacao, 'L', $inicio_edicao);
		
		$dao = new Agenda_ItemDao();
		
		$dao->save($agenda_item);
	}
	
	function ListaAgenda($cod_item) {
		$dao= new Agenda_ItemDao();
		return $dao-> load($cod_item);
	}
	
	function ativaAgenda($cod_item,$cod_usuario, $cod_curso){
		
		$dao = new Agenda_ItemDao();
		return $dao->ativarAgenda($cod_item, $cod_usuario, $cod_curso);
	}
}
?>
