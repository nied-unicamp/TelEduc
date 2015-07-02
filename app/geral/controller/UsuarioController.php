<?php

require_once '../../../lib/Conexao.php';
require_once '../../../lib/data.php';
require_once '../../geral/model/Usuario.php';
require_once '../../geral/dao/UsuarioDao.php';

class UsuarioController{
	
	function insereUsuario(){
		
		$usuario = new Usuario();
		
		$senha = crypt('123abc', "AA");
		
		$data = new Data();
		
		$usuario->setAllId('camilakna', $senha, 'Camila Abreu', '123456789', 'camila.kna@gmail.com', "(19)12345678", 'Avenida das flores, 85', 'Campinas', 'SP', 'Brasil', $data->Data2UnixTime('05/01/1991'), 'F', 'Unicamp', 'Estagiária', 'Superior incompleto', null, $data->Data2UnixTime('19/06/2015'), 1, null);
		
		$dao = new UsuarioDao();
		
		return $dao->create($usuario);
	}
	
	function listaUsuarios(){
		
		$dao = new UsuarioDao();
		
		return $dao->loadAll();
		
	}
	
	function retornaUsuario($cod_usuario){
		$dao = new UsuarioDao();
		
		return $dao->load($cod_usuario);
	}
}