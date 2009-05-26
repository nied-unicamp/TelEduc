<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/autenticacao/autenticacao.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/mural/mural.inc
  ========================================================== */

/*******************************************************
  Funï¿½ao NomeDoUsuario - Retorna o nome do usuario
  Retorna uma string com o nome do usuario.
  Entradas: $sock - sock de conexao,
            $cod_usuario - codigo do usuario.
  Saida:   string com nome do usuario
******************************************************/
function NomeDoUsuario($sock, $cod_usuario)
{
  if ($cod_usuario==-2)
  {
    $tuplas="Visitante";
  }
  else if ($cod_usuario==-1)
  {
    $tuplas="Administraï¿½ï¿½o do TelEduc";
  }
  else
  {
    $query="select nome from Usuario where cod_usuario = ".VerificaNumeroQuery($cod_usuario);
    $res=Enviar($sock, $query);

    $linhas=RetornaLinha($res);
    $tuplas=$linhas['nome'];
  }
  return($tuplas);
}


/*******************************************************
  Funï¿½ao EliminaEspacos - Elimina espacos no comeï¿½o e no
    final da frase.
  Retorna a frase sem os espacos no comeï¿½o e no final dela.
  Entradas: $frase - frase
  Saida:   frase sem os espacos no comeï¿½o e no final dela.
******************************************************/

function EliminaEspacos($frase)
{
  return(trim($frase));
}

/********************************************************************
  Funï¿½ao RetornaCursosNaoIniciados - Retorna cursos que ainda nÃ£o comeÃ§aram.
  Entradas: $sock - sock de conexao
            $cod_usuario - codigo do usuario
  Saida:   matriz com informaÃ§Ãµes (codigo do curso,nome do curso e tipo do usuario) do curso e estado do usuario 
           dentro daquele curso.
********************************************************************/

function RetornaCursosNaoIniciados($sock,$cod_usuario)
{
  $query = "SELECT CUR.cod_curso, CUR.nome_curso, USUC.tipo_usuario
	    FROM Usuario USU
	    INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario)
	    INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso)
            WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."'
            AND (CUR.curso_inicio > ".time()." OR CUR.curso_inicio is NULL)
	    ORDER BY CUR.nome_curso";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  $nCursos = is_array($tuplas) ? count($tuplas) : 0;

  return array ($tuplas, $nCursos);
}

/* **********************************************************************
   RetornaCursosNaoIniciadosSemUsuario - Retorna a lista de cursos não iniciados
   Entrada: $sock - sock da base externa
   Saida: array []['cod_curso']
                []['nome_curso']
                []['inscricao_inicio']
                []['inscricao_fim']
                []['acesso_visitante'] - 'A' se aceita, 'N' se não
*/

function RetornaCursosNaoIniciadosSemUsuario($sock)
{
  $hoje=time();
  $ontem=$hoje - 86400;
  $where="(curso_inicio > ".time()." or curso_inicio is NULL)";
  $query="select cod_curso,nome_curso,inscricao_inicio,inscricao_fim,acesso_visitante from Cursos where ".$where." order by nome_curso";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0 && $lista != "")
  {
    $c=0;
    foreach($lista as $cod => $linha)
      $lista_saida[$c++]=$linha;
  }
  else
    $lista_saida=$lista;

  return ($lista_saida);	
}

/********************************************************************
  Funï¿½ao ApagaMensagem - Atribue a flag de X (Deletado) ao campo
    status da mensagem.
  Retorna true se a operaï¿½ao for bem sucedida ou false
    se ocorrer um erro.
  Entradas: $sock - sock de conexao,
            $cod_mural - codigo do mural.
  Saida:   true se bem-sucedido ou false se falhar.
********************************************************************/

function RetornaCursosEmAndamento($sock,$cod_usuario)
{
  /*
    a - Aluno inscrito
    z - convidado passivo
    f - Formador rejeitado
    r - Aluno rejeitado
    t - convidado com inscricao cancelada
    y - visitante com inscricao cancelada
  */

  $query = "SELECT CUR.cod_curso, CUR.nome_curso, USUC.tipo_usuario
	    FROM Usuario USU
	    INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario)
	    INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso)
            WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."'
            AND CUR.curso_inicio <= '".time()."'
            AND CUR.curso_fim >= '".time()."'
            AND binary USUC.tipo_usuario not in ('X', 'f', 'r', 't', 'y', 'a')
	    ORDER BY CUR.nome_curso";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  $nCursos = is_array($tuplas) ? count($tuplas) : 0;

  return array ($tuplas, $nCursos);
}

/* **********************************************************************
   RetornaCursosEmAndamentoSemUsuario - Retorna a lista de cursos em andamento
   Entrada: $sock - sock da base externa
   Saida: array []['cod_curso']
                []['nome_curso']
                []['inscricao_inicio']
                []['inscricao_fim']
                []['acesso_visitante'] - 'A' se aceita, 'N' se não
*/
function RetornaCursosEmAndamentoSemUsuario($sock)
{
  $hoje=time();
  $ontem=$hoje - 86400;
  $where="curso_inicio<=".$hoje." and curso_fim>=".$ontem;
  $query="select cod_curso,nome_curso,inscricao_inicio,inscricao_fim,acesso_visitante from Cursos where ".$where." order by nome_curso";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0 && $lista != "")
  {
    $c=0;
    foreach($lista as $cod => $linha)
      $lista_saida[$c++]=$linha;
  }
  else
    $lista_saida=$lista;

  return ($lista_saida);	
}
/********************************************************************
  Funï¿½ao ApagaMensagem - Atribue a flag de X (Deletado) ao campo
    status da mensagem.
  Retorna true se a operaï¿½ao for bem sucedida ou false
    se ocorrer um erro.
  Entradas: $sock - sock de conexao,
            $cod_mural - codigo do mural.
  Saida:   true se bem-sucedido ou false se falhar.
********************************************************************/

function RetornaCursosPassados($sock,$cod_usuario)
{
  $hoje=time();
  $ontem=$hoje - 86400;

  /*
    a - Aluno inscrito
    z - convidado passivo
    f - Formador rejeitado
    r - Aluno rejeitado
    t - convidado com inscricao cancelada
    y - visitante com inscricao cancelada
  */

  $query = "SELECT CUR.cod_curso, CUR.nome_curso, USUC.tipo_usuario
	    FROM Usuario USU
	    INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario)
	    INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso)
            WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."'
            AND CUR.curso_fim < '".$ontem."'
            AND binary USUC.tipo_usuario not in ('X', 'f', 'r', 't', 'y', 'a')
	    ORDER BY CUR.nome_curso";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  $nCursos = is_array($tuplas) ? count($tuplas) : 0;

  return array ($tuplas, $nCursos);
}


/* **********************************************************************
   RetornaCursosPassadosSemUsuario - Retorna a lista de cursos encerrados
   Entrada: $sock - sock da base externa
   Saida: array []['cod_curso']
                []['nome_curso']
                []['inscricao_inicio']
                []['inscricao_fim']
                []['acesso_visitante'] - 'A' se aceita, 'N' se não
*/
function RetornaCursosPassadosSemUsuario($sock,$cod_usuario)
{
  $hoje=time();
  $ontem=$hoje - 86400;
  $where="curso_fim<".$ontem;
  $query="select cod_curso,nome_curso,inscricao_inicio,inscricao_fim,acesso_visitante from Cursos where ".$where." order by nome_curso";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0 && $lista != "")
  {
    $c=0;
    foreach($lista as $cod => $linha)
      $lista_saida[$c++]=$linha;
  }
  else
    $lista_saida=$lista;

  return ($lista_saida);	
}

/* **********************************************************************
   RetornaCursosInscricaoSemUsuario - Retorna a lista de cursos com inscrição aberta
   Entrada: $sock - sock da base externa
   Saida: array []['cod_curso']
                []['nome_curso']
                []['inscricao_inicio']
                []['inscricao_fim']
                []['acesso_visitante'] - 'A' se aceita, 'N' se não
*/
function RetornaCursosInscricaoSemUsuario($sock,$cod_usuario)
{
  $hoje=time();
  $ontem=$hoje - 86400;
  $where="inscricao_inicio<=".$hoje." and inscricao_fim>=".$ontem;
  $query="select cod_curso,nome_curso,inscricao_inicio,inscricao_fim,acesso_visitante from Cursos where ".$where." order by nome_curso";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0 && $lista != "")
  {
    $c=0;
    foreach($lista as $cod => $linha)
      $lista_saida[$c++]=$linha;
  }
  else
    $lista_saida=$lista;

  return ($lista_saida);	
}

/********************************************************************
  Funï¿½ao ApagaMensagem - Atribue a flag de X (Deletado) ao campo
    status da mensagem.
  Retorna true se a operaï¿½ao for bem sucedida ou false
    se ocorrer um erro.
  Entradas: $sock - sock de conexao,
            $cod_mural - codigo do mural.
  Saida:   true se bem-sucedido ou false se falhar.
********************************************************************/

function RetornaCursosInscAbertas($sock,$cod_usuario)
{
  $hoje=time();
  $ontem=$hoje - 86400;

  $query = "SELECT CUR.cod_curso, CUR.nome_curso, USUC.tipo_usuario
	    FROM Usuario USU
	    INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario)
	    INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso)
            WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."'
            AND CUR.inscricao_inicio <= '".$hoje."'
            AND CUR.inscricao_fim >= '".$ontem."'
            AND binary USUC.tipo_usuario not in ('X', 'f', 'r', 't', 'y', 'a')
	    ORDER BY CUR.nome_curso";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  $nCursos = is_array($tuplas) ? count($tuplas) : 0;

  return array ($tuplas, $nCursos);
}

?>