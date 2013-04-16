<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/grupos/grupos.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
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

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
*/
/*==========================================================
  ARQUIVO : cursos/aplic/grupos/grupos.inc
  ========================================================== */

/*
Biblioteca de funcoes grupos.inc
Para uso com a Ferramenta grupos no Programa Teleduc
Autor: Leonel Aguilar Gayard
*/

/*
ind_a
Fun�es nesta biblioteca:

GruposFechados
RetornaListaGrupos
RetornaUsuariosSemGrupo
RetornaTodosUsuarios
InsereGrupoG
InsereGrupoGU
ExisteGrupo
RetornaUsuariosNoGrupo
RetornaGruposDoUsuario
AbrirFecharGrupos
RetornaNomeGrupo
InsereUsuarioNoGrupoGU
ExcluirGrupo
RetornaUsuariosForaDoGrupo
ExcluirUsuarioDoGrupo
AlteraNomeGrupo
ind_b
*/

/****************************************************************************
   GruposFechados -  Verifica se os grupos estao fechados ou nao
   Entradas : $sock - sock de conexao,
   Saida:     true - se os grupos estiverem travados
              false - se os grupos nao estiverem travados
****************************************************************************/
function GruposFechados($sock)
{
  $query = "select status from Grupos where status='T' limit 1";
  $res   = Enviar($sock,$query);
  $num   = RetornaNumLinhas($res);

   if ($num > 0)
    return(true);
  else
    return(false);
}


/*****************************************************************************
   RetornaListaGrupos - retorna lista de grupos
   entrada: $sock - sock de conexao
   saida:   array []['cod_grupo'] - codigo do grupo
                  []['nome_grupo'] - nome do grupo
                  []['num_usuarios'] - numero de usuarios no grupo
******************************************************************************/
function RetornaListaGrupos($sock, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  /* todos usuarios */
  $query1="select cod_grupo,nome 'nome_grupo' from Grupos where status <> 'X'";
  $res1=Enviar ($sock,$query1);
  $lista1=RetornaArrayLinhas($res1);

  /* contagem dos usuarios em cada grupo */
  $query2="select GU.cod_grupo,count(GU.cod_usuario) 'num_usuarios'
           from Grupos_usuario as GU, ".$dbnamebase.".Usuario_curso as USUC
           where (GU.cod_usuario = USUC.cod_usuario)
           and (USUC.cod_curso = '".$cod_curso."')
           and ((binary USUC.tipo_usuario = 'F') or (binary USUC.tipo_usuario = 'A'))
           group by GU.cod_grupo";

  $res2=Enviar ($sock,$query2);
  $lista2=RetornaArrayLinhas($res2);

  if (count($lista2)>0)
    foreach ($lista2 as $cod => $linha)
    {
      $lista_aux[$linha['cod_grupo']] = $linha['num_usuarios'];
    }

  if (count($lista1)>0)
    foreach ($lista1 as $cod => $linha)
    {
      if (isset($lista_aux[$linha['cod_grupo']]))
        $lista1[$cod]['num_usuarios'] = $lista_aux[$linha['cod_grupo']];
      else
        $lista1[$cod]['num_usuarios'] = 0;
    }

  return $lista1;
}


/***********************************************************************************************
   RetornaUsuariosSemGrupo - retorna array com cod_usuarios, nomes e tipo_usuario
                             dos usuarios sem grupo
   entrada: $sock - sock de conexao
   saida:   array []['cod_usuario'] - codigo dos usuarios sem grupo
                  []['nome'] - nome dos usuarios sem grupo
                  []['tipo_usuario'] - se usuario aluno ou formador
*************************************************************************************************/
function RetornaUsuariosSemGrupo($sock, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  /* todos os usuarios */
  $query1="select UC.cod_usuario,U.nome,UC.tipo_usuario
           from ".$dbnamebase.".Usuario_curso UC
           inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
           where (binary UC.tipo_usuario = 'A' or binary UC.tipo_usuario = 'F')
           and UC.cod_curso = '".$cod_curso."'
           and UC.cod_usuario >= 0
           order by tipo_usuario,nome";
  $res1=Enviar($sock,$query1);
  $lista1=RetornaArrayLinhas($res1);

  /* usuarios que tem grupo */
  $query2="select distinct GU.cod_usuario 'cod_usuario' from Grupos G, Grupos_usuario GU where G.cod_grupo=GU.cod_grupo and G.status != 'X'";
  $res2=Enviar($sock,$query2);
  $lista2=RetornaArrayLinhas($res2);

  /* cria array de quem estah em lista1 e nao estah em lista2 */
  $array_aux="";
  $lista_retorno="";
  unset($array_aux);
  unset($lista_retorno);
  $c=0;
  if (count($lista2)>0)
  {
    /* h�usu�ios sem grupo */
    /* array_aux[x] == true: usuario de codigo x tem grupo */
    foreach ($lista2 as $cod => $linha)
    {
      $array_aux[$linha['cod_usuario']] = true;
    }
  }

  if (count($lista1)>0)
  {
    /* h�usuarios */
    foreach ($lista1 as $cod => $linha)
    {
      if (!isset($array_aux[$linha['cod_usuario']]))
      {
        /* usuario n� est�"setado", portanto n� tem grupo */
        $lista_retorno[$c]['cod_usuario'] = $linha['cod_usuario'];
        $lista_retorno[$c]['nome'] = $linha['nome'];
        $lista_retorno[$c]['tipo_usuario'] = $linha['tipo_usuario'];
        $c++;
      }
    }
  }

  return ($lista_retorno);
}


/*****************************************************************************************
   RetornaUsuariosComGrupo - retorna array com o codigo, o nome e o tipo
                             de todos os usuarios com grupo
   entrada: $sock - sock de conexao
   saida:   array []['cod_usuario'] - cod_usuario dos usuarios
                  []['nome'] - nome dos usuarios
                  []['tipo_usuario'] - tipo_usuario dos usuarios
******************************************************************************************/
function RetornaUsuariosComGrupo($sock, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  /* usuarios que tem grupo */
  $query="select distinct GU.cod_usuario 'cod_usuario', U.nome, UC.tipo_usuario
          from Grupos G, Grupos_usuario GU, ".$dbnamebase.".Usuario_curso UC
          inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
          where (binary UC.tipo_usuario = 'A' or binary UC.tipo_usuario = 'F')
          and UC.cod_curso = '".$cod_curso."'
          and (UC.cod_usuario >= 0) and
          (UC.cod_usuario = GU.cod_usuario) and
          (G.cod_grupo=GU.cod_grupo) and (G.status <> 'X')
          order by UC.tipo_usuario, U.nome";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}


/*********************************************************************************
   RetornaTodosUsuarios- retorna codigo de usuario e nome de todos os
                          usuarios
   entrada: $sock - sock de conexao
   saida:   array []['cod_usuario'] - cod_usuario dos usuarios
                  []['nome'] - nome dos usuarios
                  []['tipo_usuario'] - tipo_usuario dos usuarios
                  []['num_grupos'] - número de grupos que o usuário participa
***********************************************************************************/
function RetornaTodosUsuarios($sock, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select UC.cod_usuario,US.nome,UC.tipo_usuario,
          (select count(GU.cod_grupo) from Grupos_usuario GU, Grupos G where GU.cod_usuario = UC.cod_usuario and G.cod_grupo = GU.cod_grupo and (G.status = 'T' or G.status = 'A') ) as num_grupos
          from ".$dbnamebase.".Usuario_curso UC
          inner join ".$dbnamebase.".Usuario US ON (US.cod_usuario = UC.cod_usuario_global)
          where (binary UC.tipo_usuario = 'A' or binary UC.tipo_usuario = 'F')
          and UC.cod_curso = '".$cod_curso."'
          and UC.cod_usuario >= 0
          order by tipo_usuario, nome";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}


/*********************************************************************************************************
   RetornaUsuariosNaoEstaoEmOutroGrupo - retorna array com cod_usuarios, nomes e tipo_usuario 
                                         dos usuarios que nao pertencem a algum grupo que nao 
                                         seja o recebido por parametro (identificado por cod_grupo)
   entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo para referencia (para saber quais sao os "outros grupos")
   saida:   array []['cod_usuario'] - codigo dos usuarios sem grupo
                  []['nome'] - nome dos usuarios sem grupo
                  []['tipo_usuario'] - se usuario aluno ou formador
**********************************************************************************************************/
function RetornaUsuariosNaoEstaoEmOutroGrupo($sock, $cod_grupo, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  /* todos os usuarios */
  $query1="select UC.cod_usuario,U.nome,UC.tipo_usuario
           from ".$dbnamebase.".Usuario_curso UC
           inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
           where (binary UC.tipo_usuario = 'A' or binary UC.tipo_usuario = 'F')
           and UC.cod_curso='".$cod_curso."'
           and UC.cod_usuario >= 0
           order by tipo_usuario,nome";
  $res1=Enviar($sock,$query1);
  $lista1=RetornaArrayLinhas($res1);

  /* usuarios que perten�m a algum grupo que nao seja o recebido por parametro (cod_grupo) */
  $query2="select distinct GU.cod_usuario 'cod_usuario' from Grupos G, Grupos_usuario GU where G.cod_grupo=GU.cod_grupo and G.cod_grupo != ".$cod_grupo." and G.status != 'X'";
  $res2=Enviar($sock,$query2);
  $lista2=RetornaArrayLinhas($res2);

  /* cria array de quem estah em lista1 e nao estah em lista2 */
  $array_aux="";
  $lista_retorno="";
  unset($array_aux);
  unset($lista_retorno);
  $c=0;
  if (count($lista2)>0)
  {
    /* h�usu�ios sem grupo ou pertencente ao grupo recebido por parametro*/
    /* array_aux[x] == true: usuario de codigo x tem grupo */
    foreach ($lista2 as $cod => $linha)
    {
      $array_aux[$linha['cod_usuario']] = true;
    }
  }

  if (count($lista1)>0)
  {
    /* h�usuarios */
    foreach ($lista1 as $cod => $linha)
    {
      if (!isset($array_aux[$linha['cod_usuario']]))
      {
        /* usuario n� est�"setado", portanto n� tem grupo ou
        pertence ao grupo recebido por parametro*/
        $lista_retorno[$c]['cod_usuario'] = $linha['cod_usuario'];
        $lista_retorno[$c]['nome'] = $linha['nome'];
        $lista_retorno[$c]['tipo_usuario'] = $linha['tipo_usuario'];
        $c++;
      }
    }
  }

  return ($lista_retorno);
}


/***************************************************************************
   InsereGrupoG - cria (insere) um novo grupo na tabela Grupos
   Entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo
            $nome - nome do grupo a criar
   Saida:   nenhuma
***************************************************************************/
function InsereGrupoG($sock,$cod_grupo,$nome)
{
  $query = "insert into Grupos values (".$cod_grupo.",'".LimpaTitulo($nome)."','A')";
  $res = Enviar($sock,$query);
}


/***********************************************************************************************
   InsereGrupoGU - insere na tabela Grupos_usuario os pares cod_grupo - cod_usuario
   Entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo
            $cod_usuario - codigo do usuario
   Saida: nenhuma
************************************************************************************************/
function InsereGrupoGU($sock,$cod_grupo,$cod_usuario)
{
  $query = "insert into Grupos_usuario values (".$cod_grupo.",".$cod_usuario.")";
  $res = Enviar($sock,$query);
}


/**************************************************************************************************
   ExisteGrupo - verifica na tabela Grupos a existencia de um grupo de determinado nome
   entrada: $sock - sock de conexao
            $nome - nome do grupo a verificar
   saida:   true - ja existe um grupo com tal nome
            false - nao existe um grupo com este nome
   obs: verifica apenas pelo nome, nao pelo codigo
***************************************************************************************************/
function ExisteGrupo($sock,$nome)
{
  $query="select nome from Grupos where nome='".$nome."' and status <> 'X' limit 1";
  $res  = Enviar($sock,$query);
  $num  = RetornaNumLinhas($res);
  if ($num > 0)
    return(true);
  else
    return(false);
}


/*****************************************************************************************
   RetornaUsuariosNoGrupo - retorna array com codigo dos usuarios no grupo pedido
   entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo do qual listar os cod_usuarios
   saida:   array []['cod_usuario'] - codigo dos usuarios naquele grupo
                  []['nome'] - nome do usuario
                  []['tipo_usuario'] - se usuario aluno ou formador
******************************************************************************************/
function RetornaUsuariosNoGrupo($sock,$cod_curso, $cod_grupo)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select GU.cod_usuario, U.login, U.senha, U.nome, U.rg, U.email, U.telefone, U.endereco, U.cidade, U.estado, U.pais, U.data_nasc, U.sexo, U.local_trab, U.profissao, U.cod_escolaridade, U.informacoes, U.data_inscricao, U.cod_lingua, UC.tipo_usuario
          from Grupos_usuario GU
          right join ".$dbnamebase.".Usuario_curso UC ON (GU.cod_usuario=UC.cod_usuario AND UC.cod_curso='".$cod_curso."')
          inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
          where GU.cod_grupo=".$cod_grupo;

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  return ($lista);
}


/*******************************************************************************************
   AbrirFecharGrupos - Coloca os grupos nao-apagados no status aberto ou fechado
   entrada: $sock - sock de conexao
            $acao - 'A' - Abrir Grupos
                    'T' - Travar Grupos
   saida:  nenhuma
*********************************************************************************************/
function AbrirFecharGrupos($sock,$acao)
{
  if ($acao == 'A')
  {
    $query = "update Grupos set status = 'A' where status <> 'X'";
  }
  else if ($acao == 'T')
  {
    $query = "update Grupos set status = 'T' where status <> 'X'";
  }
  $res=Enviar($sock,$query);
}


/******************************************************************************************
   RetornaNomeGrupo - retorna nome do grupo de codigo pedido
   entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo de que procurar o nome
   saida:   nome do grupo
******************************************************************************************/
function RetornaNomeGrupo($sock,$cod_grupo)
{
  $query="select nome from Grupos where status <> 'X' and cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha['nome']);
}


/*************************************************************************************************
   InsereUsuarioNoGrupoGU - cria dupla cod_grupo/cod_usuario na tabela Grupos_usuario
   entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo no qual inserir o usuario
            $cod_usuario - codigo do usuario a inserir no grupo
   saida:   nenhuma
*************************************************************************************************/
function InsereUsuarioNoGrupoGU($sock,$cod_grupo,$cod_usuario)
{
  /* verifica se o usuario ja esta presente no grupo */
    $query="insert into Grupos_usuario values (".$cod_grupo.",".$cod_usuario.")";
    $res=Enviar($sock,$query);
}


/*************************************************************************************
   ExcluirGrupo - marca como 'X' o status do grupo a excluir na tabela Grupos
   entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo a excluir
   saida:   nenhuma
   obs:     Nao afeta a tabela Grupos_usuario
**************************************************************************************/
function ExcluirGrupo($sock,$cod_grupo)
{
  $item="select cod_item from Portfolio_itens where cod_grupo=$cod_grupo";
  $res1=Enviar($sock,$item);
  $item1=RetornaArrayLinhas($res1);
  if($item1 == NULL)
  {
     $query="DELETE from Portfolio_topicos where cod_grupo=$cod_grupo";
     $res=Enviar($sock,$query);
  }
  $query="update Grupos set status='X' where cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
}


/************************************************************************************************
   RetornaUsuariosForaDoGrupo - retorna todos usuarios que nao fazem parte do grupo pedido
   entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo a que os usuarios N� pertencem
   saida:   array []['cod_usuario'] - codigo dos usuarios sem grupo
                  []['nome'] - nome dos usuarios sem grupo
                  []['tipo_usuario'] - se usuario aluno ou formador
*************************************************************************************************/
function RetornaUsuariosForaDoGrupo($sock,$cod_grupo,$cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  /* todos os usuarios */
  $query1="select UC.cod_usuario,U.nome,UC.tipo_usuario
           from ".$dbnamebase.".Usuario_curso UC
           inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
           where (binary UC.tipo_usuario = 'A' or binary UC.tipo_usuario = 'F')
           and UC.cod_curso='".$cod_curso."'
           and UC.cod_usuario >= 0
           order by tipo_usuario,nome";
  $res1=Enviar($sock,$query1);
  $lista1=RetornaArrayLinhas($res1);

  /* usuarios que est� no grupo */
  $query2="select cod_usuario from Grupos_usuario where cod_grupo=".$cod_grupo;
  $res2=Enviar($sock,$query2);
  $lista2=RetornaArrayLinhas($res2);

  $c=0;
  if (count($lista2) > 0)  // se h�usu�ios no grupo
  {
    /* cria array de quem est�em lista1 mas n� est�em lista2 */
    $array_aux="";
    unset($array_aux);
    foreach ($lista2 as $cod => $linha)
    {
      /* array_aux[x] == true significa que aquele usuario x esta dentro do grupo */
      $array_aux[$linha['cod_usuario']]=true;
    }

    if (count($lista1) > 0)
      foreach ($lista1 as $cod => $linha)
      {
        if (!isset($array_aux[$linha['cod_usuario']]))
        {
          $lista_retorno[$c++]=$linha;
        }
      }
  }
  else // se n� h�usu�io dentro do grupo
  {
    $lista_retorno = $lista1; // Se o grupo esta vazio, entao os usuarios fora do
                              // grupo sao, na verdade, todos os usuarios do curso
                              // (presentes em $lista1).
  }

  return ($lista_retorno);
}


/**************************************************************************************************
  ExcluirUsuarioDoGrupo - exclue usuario do grupo, apagando a dupla cod_usuario/cod_grupo 
                          correspondente na tabela Grupos_usuario
  entrada: $sock - sock de conexao
           $cod_usuario - usuario a excluir do grupo
           $cod_grupo - codigo do grupo do qual excluir o usuario
  saida: nenhuma
**************************************************************************************************/
function ExcluirUsuarioDoGrupo($sock,$cod_usuario,$cod_grupo)
{
  $query="delete from Grupos_usuario where cod_usuario=".$cod_usuario." and cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
}


/*****************************************************************************************
  ExcluirTodosUsuariosDoGrupo - exclui todos os usuarios pertencentes ao grupo recebido 
                                por paramentro (cod_grupo)
  entrada:     $sock - sock de conexao
               $cod_grupo - codigo do grupo do qual excluir o usuario
  saida:       nenhuma
*****************************************************************************************/
function ExcluirTodosUsuariosDoGrupo($sock,$cod_grupo)
{
  $query="delete from Grupos_usuario where cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
}


/*********************************************************************************************
   AlteraNomeGrupo - altera o nome do grupo na base de dados
   entrada:    $sock - sock de conexao
               $cod_grupo - codigo do grupo a renomear
               $nome_grupo - novo nome para o grupo
   saida:      nenhuma
**********************************************************************************************/
function AlteraNomeGrupo($sock,$cod_grupo,$nome_grupo)
{
  $query="update Grupos set nome='".LimpaTitulo($nome_grupo)."' where cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
}


/***************************************************************************************
   UltimaPosicaoLivreTopico - Retorna a posicao do ultimo topico livre
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo do topico atual
   Saida: posicao
****************************************************************************************/
function UltimaPosicaoLivreTopico($sock, $cod_topico)
{
  $consulta="select posicao_topico from Portfolio_topicos where cod_topico_pai=".$cod_topico." order by posicao_topico desc limit 1";
  $res=Enviar($sock, $consulta);
  $num=RetornaNumLinhas($res);
  if ($num>0)
  {
    $linha=RetornaLinha($res);
    $linha['posicao_topico']++;
    return($linha['posicao_topico']);
  }
  else
    return(1);
}

/********************************************************************
  RetornaGruposDoUsuario - Retorna os cod_grupo de todos os grupos nao apagados
                            a que o usuario pertence
  entrada: $sock - sock de conexao
            $cod_usuario - codigo do usuario do qual listar os grupos
*/
function RetornaGruposDoUsuario($sock,$cod_usuario)
{

  $query = "select Grupos_usuario.cod_usuario, Grupos.* from Grupos_usuario right join Grupos ON Grupos_usuario.cod_grupo=Grupos.cod_grupo where Grupos_usuario.cod_usuario=".$cod_usuario." and (Grupos.status='A' or Grupos.status='T')";
  $res=Enviar($sock,$query);
  $lista_retorno=RetornaArrayLinhas($res);
  return ($lista_retorno);
}

/*********************************************************************************
   MostraGrupoDinamic - mostra o grupo todo dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_grupo - codigo do grupo
            $frases - mensagens pertencentes ao grupo
   Saida: XML da função Ajax
*********************************************************************************/
function MostraGrupoDinamic($cod_curso, $cod_grupo, $frases)
{
  $objResponse = new xajaxResponse('ISO-8859-1');
  $sock=Conectar($cod_curso);  
  
  $grupo_fechados=GruposFechados($sock);
  $cod=-1;
  
  if($cod_grupo!=-1){
    $lista = RetornaUsuariosNoGrupo($sock,$cod_curso, $cod_grupo);
  }else{
    $lista = RetornaUsuariosSemGrupo($sock, $cod_curso);
  }
  if($lista==""){
    $objResponse->addAppend('span_grupo_'.$cod_grupo, 'innerHTML', $frases[4]."<br />");
  }else{
    $objResponse->addAppend('span_grupo_'.$cod_grupo, 'innerHTML', $frases[5].":<br /><br />");
    foreach($lista as $cod => $linha){

      if ($linha['tipo_usuario'] == 'A')
        /* 18 - Aluno */
        $tmp = $frases[0];
      else if ($linha['tipo_usuario'] == 'F')
        /* 19 - Formador */
        $tmp = $frases[1];
      else
        $tmp = "Erro";  //usuários desligados

      if(($linha['tipo_usuario'] == 'A') || ($linha['tipo_usuario'] == 'F')){ 
        if(!(($grupo_fechados) or ($cod_grupo==-1))){
          $objResponse->addCreateInput('td_grupo_'.$cod_grupo, 'checkbox', 'chk_grupo_'.$cod_grupo, 'chk_grupo_'.$cod_grupo.'_cmp_'.$linha['cod_usuario']);
          $objResponse->addEvent('chk_grupo_'.$cod_grupo.'_cmp_'.$linha['cod_usuario'], 'onclick', 'VerificarCheckBox('.$cod_grupo.', '.$linha['cod_usuario'].');');
        }

        $objResponse->addCreate('td_grupo_'.$cod_grupo, 'span', 'span2_grupo_'.$cod_grupo.'_'.$linha['cod_usuario']);
        $objResponse->addAppend('span2_grupo_'.$cod_grupo.'_'.$linha['cod_usuario'], 'innerHTML', $tmp.' <a class="text" onclick="return(OpenWindowLink('.$linha['cod_usuario'].'));" href="#">'.$linha['nome'].'</a><br />');
      }

    }
  }

  $objResponse->addAppend('td_grupo_'.$cod_grupo, 'style.padding', '10px 0px 10px 50px');
  $objResponse->addCreate('td_grupo_'.$cod_grupo, 'br', '');
  if((!$grupo_fechados)&&($cod_grupo!=-1)){
    $objResponse->addCreate('td_grupo_'.$cod_grupo, 'ul', 'ul_grupo_'.$cod_grupo);
    $objResponse->addCreate('ul_grupo_'.$cod_grupo, 'li', 'li1_grupo_'.$cod_grupo);
    $objResponse->addCreate('li1_grupo_'.$cod_grupo, 'span', 'span_li1_grupo_'.$cod_grupo);
    $objResponse->addEvent('span_li1_grupo_'.$cod_grupo, 'onclick', 'IncluirComponentes('.$cod_grupo.');');
    $objResponse->addAppend('li1_grupo_'.$cod_grupo, 'className', 'menuUp02');
    $objResponse->addAppend('li1_grupo_'.$cod_grupo, 'style.float', 'right');
    $objResponse->addAppend('span_li1_grupo_'.$cod_grupo, 'innerHTML', $frases[2]);

    $objResponse->addCreate('ul_grupo_'.$cod_grupo, 'li', 'li2_grupo_'.$cod_grupo);
    $objResponse->addCreate('li2_grupo_'.$cod_grupo, 'span', 'span_li2_grupo_'.$cod_grupo);
    $objResponse->addAppend('li2_grupo_'.$cod_grupo, 'className', 'menuUp');
    $objResponse->addAppend('li2_grupo_'.$cod_grupo, 'style.float', 'right');
    $objResponse->addAppend('span_li2_grupo_'.$cod_grupo, 'innerHTML', $frases[3]);
  }
  $objResponse->addClear('usuarios_grupo_'.$cod_grupo, 'innerHTML');
  $objResponse->addAppend('usuarios_grupo_'.$cod_grupo, 'innerHTML', ($cod+1));



  Desconectar($sock);
  //  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();  
}

/**************************************************************************************
   EditarTituloDinamic - Edita o título do fórum dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_forum - fórum ao qual o endereco estará associado
            $novo_nome - novo título do fórum
   Saida: XML da função Ajax
*************************************************************************************/
function EditarTituloDinamic ($cod_curso, $cod_grupo, $novo_nome, $texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $sock=Conectar($cod_curso);
  $consulta="update Grupos set nome='".htmlentities($novo_nome)."' where cod_grupo=".$cod_grupo;
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  // Imprime no div valores do formulário
  $objResponse->addAssign("grupo_".$cod_grupo, "innerHTML", htmlentities($novo_nome));
  $objResponse->addAssign("grupo_".$cod_grupo, "className", "link");
  $objResponse->addAssign("grupo_".$cod_grupo, "style.fontWeight", "bold");
  $objResponse->addEvent("grupo_".$cod_grupo, "onClick", "MostrarComponentes('".$cod_grupo."');");

  $objResponse->addScriptCall("mostraFeedback", $texto, true);

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}


/**************************************************************************************
   MudarConfiguracaoDinamic - muda a configuracao do fórum dinâmicamente
   Entrada: $dadosForm - array com: ['cod_curso']
                                    ['cod_forum']
            $acao - 'A' - abrir grupos
                    'T' - travar grupos
   Saida: XML da função Ajax
**************************************************************************************/
function MudarConfiguracaoDinamic($dadosForm, $acao)
{
  $objResponse = new xajaxResponse('ISO-8859-1');
  
  $cod_curso=$dadosForm['cod_curso'];
  $cod_forum=$dadosForm['cod_forum'];
  $data = time();
  
  $sock=Conectar($cod_curso);
  
  AbrirFecharGrupos($sock,$acao);
  Desconectar($sock);
  
  $objResponse->addRedirect("grupos.php?cod_curso=".$cod_curso."&acao=mudar_configuracao&atualizacao=true");

//  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}


/**********************************************************************************************
   ExcluirComponentesDinamic - exclui componentes do forum dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_grupo - codigo do grupo
            $arraySelect - array contendo os nomes dos participantes a serem excluidos
   Saida: XML da função Ajax
***********************************************************************************************/
function ExcluirComponentesDinamic($cod_curso, $cod_grupo, $arraySelect, $texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');
  
  $sock=Conectar($cod_curso);
  
  $participantes = implode(",", $arraySelect);

  $query="delete from Grupos_usuario where cod_grupo='".$cod_grupo."' and cod_usuario in (".$participantes.")";
  Enviar($sock, $query);

  $objResponse->addAssign('li2_grupo_'.$cod_grupo, 'className', 'menuUp');
  $objResponse->addEvent('span_li2_grupo_'.$cod_grupo, 'onClick', '');

  Desconectar($sock);

  $objResponse->addScriptCall("mostraFeedback", $texto, true);

//  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}


/*****************************************************************************************
   MostraComponenteDinamic - mostra os componentes do forum dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_usuraio - codigo do usuario
            $frases_grupo - frases dos grupos
   Saida: XML da função Ajax
******************************************************************************************/
function MostraComponenteDinamic($cod_curso, $cod_usuario, $frases_grupo)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);  
  
  $grupo_fechados=GruposFechados($sock);
  $cod=-1;

  if($cod_grupo!=-1){
    $lista = RetornaGruposDoUsuario($sock,$cod_usuario);
    $objResponse->addAppend('span_grupo_'.$cod_grupo, 'innerHTML', $frases_grupo[5].":<br /><br />");

    if($lista!=""){
      foreach($lista as $cod => $linha){
        $objResponse->addCreate('td_usuario_'.$cod_usuario, 'span', 'span2_usuario_'.$cod_usuario.'_'.$linha['cod_grupo']);
        $objResponse->addAppend('span2_usuario_'.$cod_usuario.'_'.$linha['cod_grupo'], 'innerHTML', $frases_grupo[0]." ".$linha['nome'].'<br />');
      }
    }else{
        $objResponse->addCreate('td_usuario_'.$cod_usuario, 'span', 'span2_usuario_'.$cod_usuario.'_'.$linha['cod_grupo']);
        $objResponse->addAppend('span2_usuario_'.$cod_usuario.'_'.$linha['cod_grupo'], 'innerHTML', $frases_grupo[1].'<br />');
    }
  }

  $objResponse->addAppend('td_usuario_'.$cod_usuario, 'style.padding', '10px 0px 10px 50px');
  $objResponse->addClear('usuarios_usuario_'.$cod_usuario, 'innerHTML');
  $objResponse->addAppend('usuarios_usuario_'.$cod_usuario, 'innerHTML', ($cod+1));
  $objResponse->addAppend('tr_usuario_'.$cod_usuario, 'className', 'altColor0');
  $objResponse->addCreate('td_usuario_'.$cod_usuario, 'br', '');

  Desconectar($sock);
  //  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}


/************************************************************************************************
   VerificaTituloDinamic - Verifica/Muda o novo título dado ao fórum dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $nome_grupo - nome do grupo
            $msg - mensagem de alerta
   Saida: XML da função Ajax
*************************************************************************************************/
function VerificaNovoTituloDinamic($cod_curso, $nome_grupo, $msg){
  $objResponse = new xajaxResponse('ISO-8859-1');
  $sock=Conectar($cod_curso); 
  $query = "Select * from Grupos where nome like '".LimpaTitulo($nome_grupo)."' and status!='X'"; 
  $res = Enviar($sock, $query);
  $res2 = RetornaLinha($res);
  /* Já existe um grupo com este nome */
  if($res2!="") {$objResponse->addAlert($msg);}
  else{ $objResponse->addScript('document.form_novo_grupo.submit();'); }
  Desconectar($sock);
  //  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();  
}
?>