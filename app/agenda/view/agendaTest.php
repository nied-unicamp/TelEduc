<?php

class AgendaTest extends PHPUnit_Framework_TestCase{
	
	public function testConexao(){
		$conexao = new Conexao();
		
		$conexao->Conectar();
		
		$this->assertEquals($conexao->status, 1);
	}
}