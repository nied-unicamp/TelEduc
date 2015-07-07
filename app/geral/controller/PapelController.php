<?php

require_once '../../geral/dao/PapelDao.php';

class PapelController{
	
	function retornaPapelUsuarioCurso($cod_curso, $cod_usuario){
		$dao = new PapelDao();
		return $dao->retornaPapelUsuarioCurso($cod_curso, $cod_usuario);
	}
}