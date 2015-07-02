<?php

require_once '../../geral/dao/PermissaoDao.php';

class PermissaoController{
	
	function retornaPermissaoUsuario($cod_usuario, $cod_ferramenta){
		$dao = new PermissaoDao();
		return $dao->retornaPermissaoUsuario($cod_usuario, $cod_ferramenta);
	}
	
	function hasPermission($cod_usuario, $cod_ferramenta, $permissao){
		$dao = new PermissaoDao();
		
		return $dao->verificaPermissao($cod_usuario, $cod_ferramenta, $permissao);
		
	}
	
}