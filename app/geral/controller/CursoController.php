<?php

require_once '../../../lib/Conexao.php';
require_once '../../../lib/data.php';
require_once '../../geral/model/Curso.php';
require_once '../../geral/dao/CursoDao.php';

class CursoController{
	
	function testaConexao(){
		
		$conexao = new Conexao();
		
		$conexao->Conectar();
		
		echo ' status='.$conexao->status;
	}
	

	
	function criaCurso(){
		$data = new Data();
		
		
		$curso = new Curso();
		
		
		$curso->setAll('Administração', 01012015, 15012015, 20012015, 30052015, 'Aprender os conceitos de administração', 'Estudantes em geral',1, 100, 3, 'sim', 09062015,1);
		
			$dao = new CursoDao();
		
		return $dao->create($curso);
	}

	function listaCursos(){
		
		$dao = new CursoDao();
		
		return $dao->loadAll();
		 
	}
	
	function apagaCursos(){
		
		$dao = new CursoDao();
		
		return $dao->delete(1);	
	}
	
	function retornaCurso($cod_curso){
		$dao = new CursoDao();
		
		return $dao->load($cod_curso);
	}
	
}
