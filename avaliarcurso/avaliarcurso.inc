<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/avaliarcurso/avaliarcurso.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�ncia
    Copyright (C) 2001  NIED - Unicamp

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2 as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    You could contact us through the following addresses:

    Nied - N�cleo de Inform�tica Aplicada � Educa��o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : administracao/avaliarcurso/avaliarcurso.inc
  ========================================================== */
/* **********************************************************************
   RetornaCursosRequisicao - Retorna lista de cursos a serem avaliados
   Entrada: $sock - Socket da conex�o atual
   Saida: $cursos = Array com c�digos e nomes dos cursos a serem avaliados
*/
function RetornaCursosRequisicao($sock)
{
  $query="select cod_curso,nome_curso,avaliado from Cursos_requisicao where avaliado='N' order by cod_curso";
  $res=Enviar($sock,$query);
  $cursos=RetornaArrayLinhas($res);
  //if (count($lista)>0)
    //foreach($lista as $cod => $linha)
      //$cursos[$linha['cod_curso']]=$linha['nome_curso'];
  return($cursos);
}

/*************************************************************************
  VerificaUsuario - veirifica se o usuario confirmou seu cadastro via email
  Entrada- $sock - Socket de conex�o atual
  Saida: false para usuario n�o confirmado, true para usuario confirmado
*/
function VerificaUsuario($sock,$email){
	$query="SELECT confirmacao FROM Usuario WHERE email='".$email."'";
	$res=Enviar($sock,$query);
	$linha=RetornaLinha($res);
	if($linha['confirmacao']=="")
	 		return true;
	else
		return false;    		
}

/* **********************************************************************
   RetornaTodosCursos - Retorna lista de cursos a serem avaliados e rejeitados
   Entrada: $sock - Socket da conex�o atual
   Saida: $cursos = Array com c�digos e nomes dos cursos a serem avaliados e rejeitados
*/
function RetornaTodosCursos($sock)
{
  $query="select cod_curso,nome_curso,avaliado from Cursos_requisicao where avaliado='N' or avaliado='R' order by cod_curso";
  $res=Enviar($sock,$query);
  $cursos=RetornaArrayLinhas($res);
  //if (count($lista)>0)
    //foreach($lista as $cod => $linha)
      //$cursos[$linha['cod_curso']]=$linha['nome_curso'];
  return($cursos);
}

/* **********************************************************************
   RetornaDadosCurso - Retorna array com dados do curso a ser avaliado
   Entrada: $sock - Socket da conex�o atual
            $cod_curso - C�digo do curso do qual se quer as informa��es
   Saida: $curso = Array com dados do curso a ser avaliado
*/
function RetornaDadosCursoReq($sock,$cod_curso)
{
  $query="select * from Cursos_requisicao where cod_curso=".$cod_curso;
  $res=Enviar($sock,$query);
  $curso=RetornaLinha($res);
  return ($curso);
}

/* **********************************************************************
   RetornaCategoria - Retorna categoria do curso a ser avaliado
   Entrada: $sock - Socket da conex�o atual
            $cod_pasta - C�digo de pasta do curso do qual se quer as informa��es
   Saida: $categoria = Nome da categoria do curso
*/
function RetornaCategoria($sock,$cod_pasta)
{
  $query="select * from Cursos_pastas where cod_pasta=".$cod_pasta;
  $res=Enviar($sock,$query);
  $categoria=RetornaLinha($res);
  return ($categoria['pasta']);
}

/* **********************************************************************
   RetornaMinCodCurso - Retorna o menor cod_curso da tabela de requisi��o de cursos
   Entrada: $sock - Socket da conex�o atual
            $todos - Se est�o sendo mostrados todos os cursos ou somente os n�o avaliados
   Saida: $min_cod_curso = Menor cod_curso da tabela
*/
function RetornaMinCodCurso($sock,$todos)
{
  if (isset($todos) && $todos != "")
    $query="select min(cod_curso) from Cursos_requisicao where avaliado='N' or avaliado='R'";
  else
    $query="select min(cod_curso) from Cursos_requisicao where avaliado='N'";
  $res=Enviar($sock,$query);
  $cod_curso=RetornaLinha($res);
  $min_cod_curso=$cod_curso['min(cod_curso)'];
  return($min_cod_curso);
}

/* **********************************************************************
   RetornaMaxCodCurso - Retorna o maior cod_curso da tabela de requisi��o de cursos
   Entrada: $sock - Socket da conex�o atual
            $todos - Se est�o sendo mostrados todos os cursos ou somente os n�o avaliados
   Saida: $max_cod_curso = Maior cod_curso da tabela
*/
function RetornaMaxCodCurso($sock,$todos)
{
  if (isset($todos) && $todos != "")
    $query="select max(cod_curso) from Cursos_requisicao where avaliado='N' or avaliado='R'";
  else
    $query="select max(cod_curso) from Cursos_requisicao where avaliado='N'";
  $res=Enviar($sock,$query);
  $cod_curso=RetornaLinha($res);
  $max_cod_curso=$cod_curso['max(cod_curso)'];
  return($max_cod_curso);
}

/* **********************************************************************
   CriaPasta - Cria pasta com o nome dado pelo usu�rio
   Entrada: $sock - Socket da conex�o atual
            $cod_curso - C�digo do curso que se quer atualizar o cod_pasta
            $nome_pasta - Nome da categoria(pasta) a ser criada
   Saida: $cod_curso = C�digo do curso a ser criado
*/
function CriaPasta($sock,$cod_curso,$nome_pasta)
{
  $query="select * from Cursos_pastas where pasta like '".$nome_pasta."'";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    $linha=RetornaLinha($res);
    $query="update Cursos_requisicao set cod_pasta=".$linha['cod_pasta']." where cod_curso=".$cod_curso."";
    Enviar($sock,$query);
    return $linha['cod_pasta'];
  }
  else
  {
    $query="select max(cod_pasta) from Cursos_pastas";
    $res=Enviar($sock,$query);
    $cod_pasta=RetornaLinha($res);
    $cod_pasta=$cod_pasta['max(cod_pasta)']+1;

    $query="insert into Cursos_pastas (cod_pasta,pasta) values (".$cod_pasta.",'".$nome_pasta."')";
    Enviar($sock,$query);

    $query="update Cursos_requisicao set cod_pasta=".$cod_pasta." where cod_curso=".$cod_curso."";
    Enviar($sock,$query);

    return $cod_pasta;
  }
}

/* **********************************************************************
   CriarCurso - Obtem o c�digo do proximo curso, e prepara a cria��o das tabelas
                      a partir do arquivo "base_curso/Base_Vazia.table".
   Entrada: $cod_curso - C�digo do curso a ser criado
            $cod_curso_antigo - cod_curso antigo do curso, usado para atualizar Cursos_requisicao
            $nome_curso - nome do curso
            $num_alunos - numero de alunos previstos
            $cod_pasta - Codigo da pasta a qual pertence o curso.
   Saida: Retorna o c�digo do curso
*/
function CriarCurso($cod_curso,$cod_curso_antigo,$nome_curso,$num_alunos,$cod_pasta,$informacoes,$publico_alvo,$tipo_inscricao,$email)
{
  global $dbnamecurso;
  global $cod_lingua_s;
  $sock=Conectar("");

  $nome_base=$dbnamecurso.$cod_curso;
  $query="drop database if exists ".$nome_base;
  Enviar($sock,$query);

  $query="create database ".$nome_base;
  Enviar($sock,$query);

  $cod_usuario_global = RetornaCodUsuarioGlobalEmail($sock,$email);

  $query="insert into Cursos (cod_curso,nome_curso,num_alunos,cod_coordenador,cod_pasta,cod_lingua,acesso_visitante,informacoes,publico_alvo,tipo_inscricao) values (".$cod_curso.",'".$nome_curso."',".$num_alunos.",1,".$cod_pasta.",".$cod_lingua_s.",'N','".$informacoes."','".$publico_alvo."','".$tipo_inscricao."')";
  // insere dados na tabela do curso
  Enviar($sock,$query);

  $query="insert into Usuario_curso (cod_usuario_global,cod_usuario,cod_curso,tipo_usuario,data_inscricao) values (".$cod_usuario_global.",1,".$cod_curso.",'F',".time().")";
  Enviar($sock,$query);

  $query="insert into Usuario_curso (cod_usuario_global,cod_usuario,cod_curso,tipo_usuario,portfolio,data_inscricao) values (-1,-1, ".$cod_curso.", 'F', 'ativado', ".time().")";
  Enviar($sock,$query);

  Desconectar($sock);
  $sock=Conectar($cod_curso);

  if ($fh=fopen("../administracao/base_curso/Base_Vazia.table","r"))
  {
    $comando="";
    while (!feof($fh))
    {
      $linha=fgets($fh,1024);
      if ($linha[0]!="#")
      {
        $linha=eregi_replace("\n"," ",$linha);
        $linha=trim($linha);
        $comando .= $linha;
        if (eregi(";",$linha))
        {
          $comando=eregi_replace(";"," ",$comando);
          $comando=trim($comando);
          Enviar($sock,$comando);
          $comando ="";
        }
      }
    }
  }
  fclose($fh);

  $query="insert into Cursos (cod_curso,nome_curso,num_alunos,cod_coordenador,cod_pasta,cod_lingua,acesso_visitante,informacoes,publico_alvo,tipo_inscricao) values (".$cod_curso.",'".$nome_curso."',".$num_alunos.",1,".$cod_pasta.",".$cod_lingua_s.",'N','".$informacoes."','".$publico_alvo."','".$tipo_inscricao."')";
  //insere dados na tabela do curso
  Enviar($sock,$query);

  Desconectar($sock);

  $sock=Conectar("");

  $query="update Cursos_requisicao set data=".time().",avaliado='A' where cod_curso=".$cod_curso_antigo."\n";
  Enviar($sock,$query);

  Desconectar($sock);
}

/* **********************************************************************
   RetornaDiretorioOpcao - Retorna o Diretorio pedido
   Entrada: $sock - Socket da conex�o atual
            $item - item da tabela de diretorios a ser retornado
   Saida: Retorna o diretorio
*/
function RetornaDiretorioOpcao($sock,$item)
{
  $query="select diretorio from Diretorio where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['diretorio'];
}

/* **********************************************************************
   RetornaConfigOpcao - Retorna dado da tabela Config
   Entrada: $sock - Socket da conex�o atual
            $item - item da tabela de diretorios a ser retornado
   Saida: Retorna o diretorio
*/
function RetornaConfigOpcao($sock,$item)
{
  $query="select valor from Config where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['valor'];
}

/* **********************************************************************
   RejeitaCurso - Rejeita o curso especificado
   Entrada: $sock - Socket da conex�o atual
            $cod_curso - C�digo do curso a ser rejeitado
   Saida: Muda o valor 'avaliado' na tabela Cursos_requisicao do curso dado para 'R'(rejeitado)
*/
function RejeitaCurso($sock,$cod_curso)
{
  $query="update Cursos_requisicao set avaliado='R' where cod_curso=".$cod_curso."";
  Enviar($sock,$query);
}

/* *********************************************************************
   RetornaNovoCodCurso - Retorna o novo cod_curso do curso a ser criado
   Entrada: $sock - Socket da conex�o atual
   Sa�da: $cod_curso - Novo c�digo do curso que ser� criado
*/
function RetornaNovoCodCurso($sock)
{
  $query="select max(cod_curso) from Cursos";
  $res=Enviar($sock,$query);
  $cod_curso=RetornaLinha($res);
  $max_cod_curso=$cod_curso['max(cod_curso)']+1;
  return($max_cod_curso);
}

/* *********************************************************************
   RetornaCodUsuarioGlobalEmail - Retorna o codigo de usuario global do usuario que possui o email passado
   Entrada: $sock - Socket da conex�o atual
            $email - email do usuario
   Sa�da: codigo de usuario global  
*/
function RetornaCodUsuarioGlobalEmail($sock,$email)
{
  $query="select cod_usuario from Usuario where email = '".$email."'";
  $res = Enviar($sock,$query);

  $linha = RetornaLinha($res);

  return $linha['cod_usuario'];
}

?>
