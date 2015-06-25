<?php

require_once '../../../lib/Conexao.php';
require_once '../dao/FerramentaDao.php';

class FerramentaController{

	function listaFerramentas(){
		$dao = new FerramentaDao();
		$dao->loadAll();
	}
}