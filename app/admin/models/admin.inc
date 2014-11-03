<?php

$ferramenta_geral = 'geral';
		
$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'teleduc.inc';
require_once $model_geral.'sessao.inc';

class Admin{
	/* **********************************************************************
	 PreparaBoldLink - Monta um link na pï¿½gina (completa com RetornaSessionID()
	   Entrada: $nome - nome do link
	 		$endereco - Endereco a ser chamado
	 		$parametros - parametros a serem passados
	   Saida: Monta o a href
	 		*/
	static function PreparaBoldLink($nome,$endereco,$parametros)
	{
		echo("<li><a href=".$endereco."".$parametros." target=_top>".$nome."</a></li>\n");
	}
	
	/* **********************************************************************
	 RetornaConfiguracao - Retorna o conteudo de um item da configuracao
	Entrada: $sock - sock da base externa
	$item - item a ser buscado
	Saida: conteudo
		*/
	static function RetornaConfiguracao($sock,$item)
	{
		$query="select valor from Config where item='".$item."'";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		return ($linha['valor']);
	}
	
	/* **********************************************************************
	 RetornaCategorias - Retorna as categorias que um curso pode pertencer
	Entrada: nenhum
	Saida: array com [$cod_pasta]='nome da pasta'
	*/
	
	static function RetornaCategorias()
	{
		$sock=AcessoSQL::Conectar("");
		$query="select * from Cursos_pastas order by pasta";
		$res=AcessoSQL::Enviar($sock,$query);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		AcessoSQL::Desconectar($sock);
		if (count($lista)>0)
		foreach($lista as $cod => $linha)
			$saida[$linha['cod_pasta']]=$linha['pasta'];
		return $saida;
	}
	
	static function RetornaListaTodosLogins($sock)
	{
	
		$query="select login,email from Usuario where cod_usuario <> -1";
		$res = AcessoSQL::Enviar($sock,$query);
	
		return AcessoSQL::RetornaArrayLinhas($res);
	
	}
	
	/* **********************************************************************
	 RetornaCodUsuarioLogin - Retorna codigo do usuario que possua o login passado.
	Entrada: $sock - conexão com a base
	$login - login do usuario
	Saida: Retorna codigo do usuario que possua o login passado; retorna 0 caso o login nao seja encontrado.
	*/
	static function RetornaCodUsuarioLogin($sock,$login)
	{
	
		$query = "select cod_usuario from Usuario where login = '".$login."'";
		$res = AcessoSQL::Enviar($sock,$query);
	
		if($res != "")
		{
			$linha = AcessoSQL::RetornaLinha($res);
			return $linha['cod_usuario'];
		}
		else
			return 0;
	}
	
	static function VerificaSeLoginExiste($sock,$login)
	{
		$query = "select nome from Usuario where login='".$login."'";
		$res = AcessoSQL::Enviar($sock,$query);
		$linha = AcessoSQL::RetornaLinha($res);
	
		return $linha;
	}
	
	/********************************************************************
	 Funcao RetornaNomeEmailUsuario - Retorna nome e o email do usuario
	Entrada:  $sock - conexão com a base de dados
	$login - login do usuario
	Saida:    array contendo nome e email do usuario
	********************************************************************/
	static function RetornaNomeEmailUsuario($sock,$login)
	{
		$query = "select nome,email from Usuario where login='".$login."'";
		$res = AcessoSQL::Enviar($sock,$query);
		$linha = AcessoSQL::RetornaLinha($res);
	
		return $linha;
	}
	
	/* **********************************************************************
	 InserirCategoria - Insere a categoria, dinamicamente
	Entrada: $nome_pasta - nome da pasta
	Saida: codigo da pasta da categoria criada
	*/
	static function InserirCategoria($nome_pasta)
	{
		$sock=AcessoSQL::Conectar("");
	
		$query="select cod_pasta from Cursos_pastas where pasta like '".$nome_pasta."'";
		$res = AcessoSQL::Enviar($sock,$query);
		$linha = AcessoSQL::RetornaLinha($res);
	
		if(!$linha)
			/*Cria uma nova pasta*/
		{
			/* Descobre o cod_pasta da categoria a ser incluida */
			$query="select max(cod_pasta) from Cursos_pastas";
			$res = AcessoSQL::Enviar($sock,$query);
			$linha = AcessoSQL::RetornaLinha($res);
			$cod_pasta = $linha['cod_pasta'] + 1;
			$query="insert into Cursos_pastas (pasta) values ('".$nome_pasta."')";
			AcessoSQL::Enviar($sock,$query);
		}
		else
		{
			$cod_pasta = $linha['cod_pasta'];
		}
	
		AcessoSQL::Desconectar($sock);
	
		return $cod_pasta;
	}
	
	/* **********************************************************************
	 CriarBaseDoCurso - Obtem o codigo do proximo curso, e prepara a criacao das tabelas
	a partir do arquivo "base_curso/Base_Vazia.table".Caso usuario nao exista, cadastra usuario
	no banco de dados.
	Entrada: $nome_curso - nome do curso
	$num_alunos - numero de alunos previstos
	$cod_pasta - Codigo da pasta a qual pertence o curso.
	$nome_coordenador - nome do coordenador do curso
	$email - email do coordenador do curso
	$login - login do coordenador do curso
	$senha - senha de acesso do coordenador do curso
	$cod_usuario - codigo do usuario ( caso seja -1, significa que quer-se cadastrar um novo usuario como coordenador )
	Saida: Retorna o codigo do curso
	*/
	function CriarBaseDoCurso($nome_curso,$num_alunos,$cod_pasta,$nome_coordenador,$email,$login,$senha,$cod_usuario)
	{
		$dbnamecurso = $_SESSION['dbnamecurso'];
		$cod_lingua_s = Sessao::CodLinguaS();
		
		$sock=AcessoSQL::Conectar("");
		$query="select max(cod_curso) from Cursos";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		$cod_curso=$linha[0] + 1;
	
		$nome_base=$dbnamecurso.$cod_curso;
		$query="drop database if exists ".$nome_base;
		AcessoSQL::Enviar($sock,$query);
	
		$query="create database ".$nome_base;
		AcessoSQL::Enviar($sock,$query);
	
		if($cod_usuario == -1)
		{
			$senha_crypt=crypt($senha,"AA");
			$cod_usuario=AcessoSQL::RetornaProximoCodigo($sock,"Usuario");
	
			$query="insert into Usuario (cod_usuario,nome,email,login,senha,data_inscricao, cod_lingua) values (".$cod_usuario.",'".$nome_coordenador."','".$email."','".$login."','".$senha_crypt."',".time().", ".$cod_lingua_s.")";
			AcessoSQL::Enviar($sock,$query);
		}
	
		$query="insert into Usuario_config (cod_usuario,cod_curso) values (1, ".$cod_curso.")";
		AcessoSQL::Enviar($sock,$query);
	
		$query="insert into Usuario_curso (cod_usuario_global,cod_usuario,cod_curso,tipo_usuario,portfolio,data_inscricao) values (".$cod_usuario.",1, ".$cod_curso.", 'F', 'ativado', ".time().")";
		AcessoSQL::Enviar($sock,$query);
	
		$query="insert into Usuario_curso (cod_usuario_global,cod_usuario,cod_curso,tipo_usuario,portfolio,data_inscricao) values (-1,-1, ".$cod_curso.", 'F', 'ativado', ".time().")";
		AcessoSQL::Enviar($sock,$query);
	
		$query="insert into Cursos (cod_curso,nome_curso,num_alunos,cod_coordenador,cod_pasta,cod_lingua,acesso_visitante) values (".$cod_curso.",'".$nome_curso."',".$num_alunos.",1,".$cod_pasta.",".$cod_lingua_s.",'N')";
		AcessoSQL::Enviar($sock,$query);
	
		AcessoSQL::Desconectar($sock);
	
		$sock = AcessoSQL::Conectar($cod_curso);
	
		if ($fh=fopen("../../geral/models/Base_Vazia.table","r"))
		{
			$content = fread($fh, filesize("../../geral/models/Base_Vazia.table"));
			$lines = explode(";", $content);
	
			foreach ($lines as $sql){
		  if (trim($sql) != ""){
			 AcessoSQL::Enviar($sock, $sql);
		  }
			}
	
		}
	
		fclose($fh);
	
		$query="insert into Cursos (cod_curso,nome_curso,num_alunos,cod_coordenador,cod_pasta,cod_lingua,acesso_visitante) values (".$cod_curso.",'".$nome_curso."',".$num_alunos.",1,".$cod_pasta.",".$cod_lingua_s.",'N')";
		AcessoSQL::Enviar($sock,$query);
	
		AcessoSQL::Desconectar($sock);
	
		return $cod_curso;
	}
	
	/* **********************************************************************
	 RetornaDiretorio - Retorna o Diretorio pedido
	Entrada: $item - item da tabela de diretorios a ser retornado
	Saida: Retorna o diretorio
	*/
	static function RetornaDiretorio($item)
	{
		$sock=AcessoSQL::Conectar("");
		$query="select diretorio from Diretorio where item='".$item."'";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		AcessoSQL::Desconectar($sock);
		return $linha['diretorio'];
	}
	
	/* **********************************************************************
	 RetornaConfig - Retorna dado da tabela Config
	Entrada: $item - item da tabela de a ser retornado
	Saida: Retorna o valor correspondente ao item requisitado.
	*/
	static function RetornaConfig($item)
	{
		$sock=AcessoSQL::Conectar("");
		$query="select valor from Config where item='".$item."'";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		AcessoSQL::Desconectar($sock);
		return $linha['valor'];
	}
}
?>