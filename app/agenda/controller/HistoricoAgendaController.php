<?php

require_once '../dao/HistoricoAgendaItemDao.php';

class HistoricoAgendaController {
	
	function retornaHistoricoDoItem($cod_item){
		$dao = new Historico_Agenda_ItemDao();
		
		return $dao->retornaHistoricoDoItem($cod_item);
	}
}