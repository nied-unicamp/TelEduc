<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/avaliacoes_portfolio.inc

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
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/portfolio/avaliacoes_portfolio.inc
  ========================================================== */

  // o codigo da ferramenta avaliacao
  define("COD_AVALIACAO", 22);

/* *******************************************************************
   RetornaGruposDoUsuario - Retorna os cod_grupo de todos os grupos n� apagados
                            a que o usuario pertence
   entrada: $sock - sock de conexao
            $cod_usuario - codigo do usuario do qual listar os grupos
*/
function RetornaGruposDoUsuario($sock,$cod_usuario)
{
  $query = "select GU.cod_grupo 'cod_grupo' from Grupos_usuario GU, Grupos G where G.cod_grupo=GU.cod_grupo and G.status <> 'X' and GU.cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  $lista_retorno=RetornaArrayLinhas($res);
  return ($lista_retorno);
}


/* ********************************************************************************
  RetornaAssociacaoItemAvaliacao - Retorna o codigo da avalia�o do item associado, se tiver associado
   Entrada:  $sock - BASE DO CURSO

   Saida: array['Cod_avaliacao']= Codigo da avaliacao;
*/

function RetornaAssociacaoItemAvaliacao($sock,$cod_item)
{
   $query="select cod_avaliacao from Portfolio_itens_avaliacao  where cod_item=".$cod_item." and cod_avaliacao=cod_avaliacao";
   $res=Enviar($sock,$query);
   if (RetornaNumLinhas($res) > 0)
   {
     $lista=RetornaLinha($res);
     return $lista;
   }
   else
     return;
}


/* *********************************************************************
   FoiAvaliado - Verifica se o aluno j�foi avaliado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_aluno - aluno que est�sendo avaliado
            $cod_avaliacao - avaliacao que est�sendo corrigida
   Saida: true se foi avaliado
*/

function FoiAvaliado($sock,$cod_avaliacao,$cod_aluno)
{
  $query="select status from Avaliacao where cod_avaliacao=".$cod_avaliacao."";
  $res=Enviar($sock,$query);
  $status=RetornaLinha($res);
  
  if(($status[0]!='A') && ($status[0]!='X')){
    $query="select * from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and  cod_aluno=".$cod_aluno." and ((status='A') || (status='F'))";
    $res=Enviar($sock,$query);
    
    if (RetornaNumLinhas($res) > 0)
      return true;
  }
  return false;
}


/* *********************************************************************
   ItemFoiAvaliado - Verifica se o aluno j�foi avaliado
   Entrada: $sock - BASE DO CURSO
            $cod_item - codigo do item do portfolio
            $cod_avaliacao - avaliacao que est�sendo corrigida
   Saida: true se foi avaliado
*/

function ItemFoiAvaliado($sock,$cod_avaliacao,$cod_item)
{
  $query="select * from Portfolio_itens_avaliacao where cod_avaliacao=".$cod_avaliacao." and  cod_item=".$cod_item." and status='A'";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
    return true;
  else
    return false;
}

/* *********************************************************************
   ItemEmAvaliacao - Verifica se o item do portfolio est�sendo aavaliado
   Entrada: $sock - BASE DO CURSO
            $cod_item - codigo do item do portfolio
            $cod_avaliacao - avaliacao que est�sendo corrigida
   Saida: true se est�sendo avaliado
*/

function ItemEmAvaliacao($sock,$cod_avaliacao,$cod_aluno)
{
  $query="select * from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and  cod_aluno=".$cod_aluno." and status='E'";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
    return true;
  else
    return false;
}

/* ********************************************************************************
   NotaCompartilhadaAluno - Verifica se a nota est�compartilhada com o aluno
   Entrada: $sock - BASE DO CURSO
            $cod_avaliacao - c�igo da avaliacao realizada
            $cod_aluno - c�igo do aluno
   Saida: true se a nota estiver compartilhada com o aluno

*/
function NotaCompartilhadaAluno($sock,$cod_item,$cod_avaliacao,$cod_grupo_portfolio,$cod_usuario)
{
  if ($cod_grupo_portfolio=="NULL" || $cod_grupo_portfolio=="")
  {
    $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao_notas AN where PIA.cod_item=".$cod_item." and PIA.cod_avaliacao=".$cod_avaliacao." and PIA.status='A' and PIA.cod_nota=AN.cod_nota and (AN.tipo_compartilhamento='T' or (AN.tipo_compartilhamento='A' and AN.cod_aluno=".$cod_usuario."))";
    $res=Enviar($sock,$query);
    if (RetornaNumLinhas($res) > 0)      //N� encontrou nenhuma avaliacao para este item que esteja compartilhada com o aluno
      return true;
    else
      return false;
  }
  else
  {
    $tipo='G';
    $grupo=RetornaGruposDoUsuario($sock,$cod_usuario);
    if (count($grupo)>0)
    {
      foreach ($grupo as $cod =>$linha)
      {
        $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao_notas AN,Grupos_usuario G where PIA.cod_item=".$cod_item." and PIA.cod_avaliacao=".$cod_avaliacao." and PIA.status='A' and PIA.cod_nota=AN.cod_nota and (AN.tipo_compartilhamento='T' or (AN.tipo_compartilhamento='G' and AN.cod_aluno=G.cod_usuario and G.cod_grupo=".$linha['cod_grupo']."))";
        $res=Enviar($sock,$query);
        if (RetornaNumLinhas($res) > 0)
          return true;
        else
          return false;
      }
    }
    else
    {
      $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao_notas AN,Grupos_usuario G where PIA.cod_item=".$cod_item." and PIA.cod_avaliacao=".$cod_avaliacao." and PIA.status='A' and PIA.cod_nota=AN.cod_nota and (AN.tipo_compartilhamento='T')";
      $res=Enviar($sock,$query);
      if (RetornaNumLinhas($res) > 0)
        return true;
      else
        return false;
    }
  }
}


/* ********************************************************************************
  RetornaDadosAvaliacao - Retorna os dados da avalia�o cadastrada
   Entrada:  $sock - BASE DO CURSO
             $cod_avaliacao - Codigo da Avalia�o

   Saida: array['Cod_atividade']= Codigo da atividade;
          array['Tipo']= Tipo da Avalia�o;
          array['Valor']= Valor da Avalia�o;
          array['data_inicio']= Data de in�io da atividade;
          array['data_termino']= Data de termino da atividade;

*/
function RetornaDadosAvaliacao($sock,$cod_avaliacao)
{
   $query="select cod_atividade,tipo,valor,data_inicio,data_termino from Avaliacao where cod_avaliacao=".$cod_avaliacao."";
   $res=Enviar($sock,$query);
   $linha=RetornaLinha($res);
   return $linha;
}


/* ********************************************************************************
  RetornaTituloAtividade - Retorna  o titulo da atividade
   Entrada:  $sock - BASE DO CURSO

   Saida: titulo

*/
function RetornaTituloAtividade($sock,$cod_item)
{
   $query="select titulo from Atividade_itens where cod_item=".$cod_item."";
   $res=Enviar($sock,$query);
   $linha=RetornaLinha($res);
   return $linha[0];
}


/* *********************************************************************
   RetornaCodAlunoMaisNotasnoGrupo - Retorna o codigo de um aluno pertencente ao grupo que tem o maior numero de notas
   Entrada: $sock - BASE DO CURSO
            $cod_grupo- grupo que est�sendo avaliado
            $cod_avaliacao - avaliacao que est�sendo corrigida
   Saida: codigo do aluno
*/

function RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo)
{
  $query1="select cod_usuario from Grupos_usuario where cod_grupo=".$cod_grupo;
  $res1=Enviar($sock,$query1);
  $lista=RetornaArrayLinhas($res1);
  if (count($lista) > 0)
  {
    foreach ($lista as $cod=>$linha)
    {
      $query2="select * from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and cod_aluno=".$linha['cod_usuario']." and ((status='A') || (status='F'))";
      $res2=Enviar($sock,$query2);
      $num=RetornaArrayLinhas($res2);
      $cod_aluno[$linha['cod_usuario']]=count($num);  //armazena o cod_usuario e o numero de avalia�es de formadores encontradas para este aluno nesta atividade
    }
  }
  $maior_num=0;
  foreach ($lista as $cod=>$linha)
  {
    if ($cod_aluno[$linha['cod_usuario']] > $maior_num)
    {
      $maior_num=$cod_aluno[$linha['cod_usuario']];
      $retorno=$linha['cod_usuario'];
    }
  }
  if ($maior_num==0)
    $retorno=$linha['cod_usuario'];
  return $retorno;
}

/* *********************************************************************
   RetornaItensDeAvaliacao - Retorna array com os itens do portfolio do usuario que s� avalia�o
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_topico - t�ico
            $cod_usuario - codigo do usuario logado
            $eformador - true se o usuario logado for formador
            $cod_usuario_portfolio - codigo do usuario dono do portfolio
            $cod_grupo_portfolio - codigo do grupo dono do portfolio (NULL ou "" se individual
   Saida: array com []['cod_item']
                    []['cod_topico']
                    []['cod_usuario']
                    []['cod_grupo']
                    []['titulo']
                    []['texto']
                    []['tipo_compartilhamento']
                    []['data']
                    []['data_ativo']
                    []['data_inativo']
                    []['posicao_item']
                    []['status']
                    []['inicio_edicao']
                    []['num_comentarios']
                    []['num_comentarios_alunos'] - numero de coment�ios feitos por alunos que n� o usuario
                    []['num_comentarios_formadores'] - numero de coment�ios feitos por formadores que n� o usuario
                    []['num_comentarios_usuario'] - numero de coment�ios feitos pelo usuario
                    []['data_comentarios'] - Maior data de um coment�io

*/
function RetornaItensDeAvaliacao ($sock, $cod_curso, $cod_usuario, $eformador,$cod_usuario_portfolio,$cod_grupo_portfolio,$cod_avaliacao)
{
  if ($cod_usuario==$cod_usuario_portfolio)
    $regra="";
  else if ($cod_grupo_portfolio=="NULL" || $cod_grupo_portfolio=="")
  {
    if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }
  else
  {
    if (PertenceAoGrupo($sock,$cod_usuario, $cod_grupo_portfolio))
      $regra="";
    else if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }

  if (EFormadorComVisaoDeAluno($sock, $cod_curso, $cod_usuario))
    $regra="and tipo_compartilhamento='T'";

  if ($cod_grupo_portfolio=="")
    $consulta="select * from Portfolio_itens where cod_usuario=".$cod_usuario_portfolio." and cod_grupo IS NULL and cod_topico!=2 ".$regra." order by posicao_item";
  else
    $consulta="select * from Portfolio_itens where cod_grupo=".$cod_grupo_portfolio." and cod_topico!=2 ".$regra." order by posicao_item";

  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  $retorno = "";
  unset($retorno);
  if (count($lista)>0)
    foreach($lista as $cod => $linha)
    {
      $estaassociado=EhItemDeAvaliacao($sock,$linha['cod_item'],$cod_avaliacao);
      if (count($estaassociado)>0)
      {
        $retorno[$cod]['cod_item']=$linha['cod_item'];
        $retorno[$cod]['cod_usuario']=$linha['cod_usuario'];
        $retorno[$cod]['cod_grupo']=$linha['cod_grupo'];
        $retorno[$cod]['titulo']=$linha['titulo'];
        $retorno[$cod]['texto']=$linha['texto'];
        $retorno[$cod]['tipo_compartilhamento']=$linha['tipo_compartilhamento'];
        $retorno[$cod]['data']=$linha['data'];
        $retorno[$cod]['posicao_item']=$linha['posicao_item'];
        $retorno[$cod]['status']=$linha['status'];
        $retorno[$cod]['inicio_edicao']=$linha['inicio_edicao'];
        $tmp=RetornaNumComentariosItem ($sock, $cod_usuario, $linha['cod_item']);
        $retorno[$cod]['num_comentarios']=$tmp['num_comentarios'];
        $retorno[$cod]['num_comentarios_alunos']=$tmp['num_comentarios_alunos'];
        $retorno[$cod]['num_comentarios_formadores']=$tmp['num_comentarios_formadores'];
        $retorno[$cod]['num_comentarios_usuario']=$tmp['num_comentarios_usuario'];
        $retorno[$cod]['data_comentarios']=$tmp['data_comentarios'];
      }
    }
  return($retorno);
}



/* ********************************************************************************
  EhItemDeAvaliacao - Retorna o codigo da avalia�o do item associado, se tiver associado
   Entrada:  $sock - BASE DO CURSO

   Saida: array['Cod_avaliacao']= Codigo da avaliacao;
*/

function EhItemDeAvaliacao($sock,$cod_item,$cod_avaliacao)
{
   $query="select PIA.cod_avaliacao from Portfolio_itens_avaliacao PIA, Avaliacao A where PIA.cod_item=".$cod_item." and PIA.cod_avaliacao=".$cod_avaliacao." and A.status!='A' and A.status!='X' and A.status!='C' and PIA.cod_avaliacao=A.cod_avaliacao";
   $res=Enviar($sock,$query);
   if (RetornaNumLinhas($res) > 0)
   {
     $lista=RetornaLinha($res);
     return $lista;
   }
   else
     return;
}

/* ********************************************************************************
  RetornaAvaliacaoPortfolio - Retorna as atividades cadastradas para serem realizadas no Portf�io
   Entrada:  $sock - BASE DO CURSO
             $tipo - Tipo do portf�io - Individual ou Em grupo
	           $cod_usuario - codigo do usuario logado

   Saida: array['Cod_avaliacao']= Codigo da avaliacao;
          array['Cod_atividade']= Codigo da atividade;
          array['Valor']= Valor da Avalia�o;
          array['data_inicio']= Data de in�io da atividade;
          array['data_termino']= Data de termino da atividade;

*/
function RetornaAvaliacaoPortfolio($sock, $tipo, $cod_curso, $cod_usuario)
{
  if (EFormador($sock, $cod_curso, $cod_usuario))
    $comp = "(At.tipo_compartilhamento='F' or At.tipo_compartilhamento='T')";
  else if (EAluno($sock, $cod_curso, $cod_usuario)     || 
           EVisitante($sock, $cod_curso, $cod_usuario) ||
           EColaborador($sock, $cod_curso, $cod_usuario))
    $comp = "At.tipo_compartilhamento='T'";
  else
  {
    echo("Erro em ".__FILE__." ".__LINE__." ".__FUNCTION__);
    die();
  }
  $data=time();
  $query  = "select Av.cod_avaliacao, Av.cod_atividade, Av.valor, Av.data_inicio, Av.data_termino ";
  $query .= "from Avaliacao Av, Atividade_itens At ";
  $query .= "where At.cod_item=Av.cod_atividade ";
  $query .= "and ".$comp." ";
  $query .= "and Av.ferramenta='P' ";
  $query .= "and Av.tipo='".$tipo."' ";
  $query .= "and Av.status!='X' ";
  $query .= "and Av.status!='A' ";
  $query .= "and Av.status!='C' ";
  $query .= "and Av.data_termino>=$data";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}

/* ********************************************************************************
  AlterarAssociacaoItemAvaliacao - altera associa�o de um item de portf�io a uma avalia�o criada
   Entrada:  $sock - BASE DO CURSO
             $cod_item - c�igo do item do portf�io
             $cod_avaliacao - c�igo da avalia�o

   Saida:  nenhuma
*/
function AlterarAssociacaoItemAvaliacao($sock,$cod_item,$cod_avaliacao)
{
  $consulta="update Portfolio_itens_avaliacao set cod_avaliacao=".$cod_avaliacao." where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
}

/* ********************************************************************************
  AssociarItemAvaliacao - associa um item de portf�io a uma avalia�o criada
   Entrada:  $sock - BASE DO CURSO
             $cod_item - c�igo do item do portf�io
             $cod_avaliacao - c�igo da avalia�o

   Saida:  nenhuma

*/
function AssociarItemAvaliacao($sock,$cod_item,$cod_avaliacao)
{
  $consulta="insert into Portfolio_itens_avaliacao values (".$cod_item.", ".$cod_avaliacao.",0,'N')";
  $res=Enviar($sock, $consulta);
}

/* ********************************************************************************
  DesassociarItemAvaliacao - desassocia um item de portf�io de uma avalia�o
   Entrada:  $sock - BASE DO CURSO
             $cod_item - código do item do portfólio

   Saida:  nenhuma
*/
function DesassociarItemAvaliacao($sock,$cod_item)
{
  $consulta="delete from Portfolio_itens_avaliacao where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
   return($res);
}


/* *********************************************************************
   RetornaNumAvaliacoesGrupo - Retorna o nmero de itens de avaliacao no portfolio do grupo
   Entrada: $sock - BASE DO CURSO
            $cod_aluno - c�igo do aluno

   Saida:     numero de avaliacoes
*/
/*function RetornaNumAvaliacoesGrupo($sock,$cod_usuario,$eformador,$cod_topico,$cod_aluno,$cod_grupo_portfolio)
{
  if ($cod_usuario==$cod_usuario_portfolio)
    $regra="";
  else
  {
    if (PertenceAoGrupo($sock, $cod_usuario, $cod_grupo_portfolio))
      $regra="";
    else if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }

  $query="select PIA.cod_avaliacao from Portfolio_itens_avaliacao PIA, Portfolio_itens PI, Avaliacao A  where PIA.cod_item=PI.cod_item and PI.cod_usuario=".$cod_aluno." and PI.cod_topico=".$cod_topico." and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='G' and (A.status='F' or A.status='E') ".$regra;
  $res=Enviar($sock,$query);
  $num_avaliacoes=RetornaNumLinhas($res);
  return $num_avaliacoes;
}



/* *********************************************************************
   RetornaNumAvaliacoesIndividual - Retorna o nmero de itens de avaliacao no portfolio do usuario
   Entrada: $sock - BASE DO CURSO
            $cod_aluno - c�igo do aluno

   Saida:     numero de avaliacoes
*/
/*function RetornaNumAvaliacoesIndividual($sock,$cod_usuario,$eformador,$cod_topico,$cod_aluno)
{
  if ($cod_usuario==$cod_aluno)
    $regra="";
  else
  {
    if ($eformador)
      $regra="and (PI.tipo_compartilhamento='T' or PI.tipo_compartilhamento='F')";
    else
      $regra="and PI.tipo_compartilhamento='T'";
  }
  $query="select PIA.cod_avaliacao from Portfolio_itens_avaliacao PIA, Portfolio_itens PI, Avaliacao A  where PIA.cod_item=PI.cod_item and PI.cod_usuario=".$cod_aluno." and PI.cod_topico=".$cod_topico." and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='I' and (A.status='F' or A.status='E') ".$regra;
  $res=Enviar($sock,$query);
  $num_avaliacoes=RetornaNumLinhas($res);
  return $num_avaliacoes;
}


/* ********************************************************************************
  RetornaTituloAtividadePortfolio - Retorna o titulo da atividade que ser�realizada no Portf�io
   Entrada:  $sock - BASE DO CURSO

   Saida: titulo

*/
function RetornaTituloAtividadePortfolio($sock,$cod_avaliacao)
{
  $query="select cod_atividade from Avaliacao where cod_avaliacao=".$cod_avaliacao."";
  $res=Enviar($sock,$query);
  $cod_atividade=RetornaLinha($res);
  $titulo=RetornaTituloAtividade($sock,$cod_atividade[0]);
  return $titulo;
}

/* *********************************************************************
   RetornaNumItensNaoAvaliados - Retorna o numero de itens em um portf�io
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo da pasta base
            $portfolios - 'P' - Todos os itens, 'F' - Itens tipo F e T, 'T' - S�tipo T
   Saida: numero de itens
*/
function RetornaNumItensNaoAvaliados($sock,$cod_topico,$cod_usuario_portfolio,$cod_grupo_portfolio,$cod_usuario,$eformador)
{
  $num_nao_avaliados=0;

  if ($cod_usuario==$cod_usuario_portfolio)
    $regra="";
  else if ($cod_grupo_portfolio=="NULL" || $cod_grupo_portfolio=="")
  {
    if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }
  else
  {
    if (PertenceAoGrupo($sock, $cod_usuario, $cod_grupo_portfolio))
      $regra="";
    else if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }

  if ($cod_grupo_portfolio=="NULL" || $cod_grupo_portfolio=="")
  {
    $tipo='I';
    $query="select * from Portfolio_itens where cod_usuario=".$cod_usuario_portfolio." and cod_topico!=2 ".$regra." and cod_grupo IS NULL";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);
    if (count($lista)>0)
    {
      while ( list($cod, $linha) = each($lista) )
      {
        $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao A where PIA.cod_item=".$linha['cod_item']." and PIA.status='N' and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
        $res=Enviar($sock,$query);
        if (RetornaNumLinhas($res) > 0)
        {
          $num_nao_avaliados++;
        }
        if (!$eformador)
        {
          $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao_notas AN, Avaliacao A where PIA.cod_item=".$linha['cod_item']." and PIA.status='A' and PIA.cod_nota=AN.cod_nota and (AN.tipo_compartilhamento='T' or (AN.tipo_compartilhamento='A' and AN.cod_aluno=".$cod_usuario.")) and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
          $res=Enviar($sock,$query);
          if (RetornaNumLinhas($res) == 0)      //N� encontrou nenhuma avaliacao para este item que esteja compartilhada com o aluno
          {
            $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao_notas AN, Avaliacao A where PIA.cod_item=".$linha['cod_item']." and PIA.status='A' and PIA.cod_nota=AN.cod_nota and (AN.tipo_compartilhamento='F' or (AN.tipo_compartilhamento='A' and AN.cod_aluno!=".$cod_usuario.")) and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
            $res=Enviar($sock,$query);
            if (RetornaNumLinhas($res) > 0)
            {
              $num_nao_avaliados++;
            }
          }
        }
      }
    }
  }
  else
  {
    $tipo='G';
    $query="select * from Portfolio_itens where cod_grupo=".$cod_grupo_portfolio." ".$regra." and cod_topico!=2";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);
    if (count($lista)>0)
    {
      while ( list($cod, $linha) = each($lista) )
      {
        $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao A where PIA.cod_item=".$linha['cod_item']." and PIA.status='N' and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
        $res=Enviar($sock,$query);
        if (RetornaNumLinhas($res) > 0)
        {
          $num_nao_avaliados++;
        }
        $grupo=RetornaGruposDoUsuario($sock,$cod_usuario);
        if (count($grupo)>0)
        {
          foreach ($grupo as $cod => $linha_grupo)
          {
            $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao_notas AN, Avaliacao A, Grupos_usuario GU where PIA.cod_item=".$linha['cod_item']." and PIA.status='A' and PIA.cod_nota=AN.cod_nota and (AN.tipo_compartilhamento='T' or (AN.tipo_compartilhamento='G' and AN.cod_aluno=GU.cod_usuario and GU.cod_grupo=".$linha_grupo['cod_grupo'].")) and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
            $res=Enviar($sock,$query);
            if (RetornaNumLinhas($res) == 0)      //N� encontrou nenhuma avaliacao para este item que esteja compartilhada com o grupo
            {
              $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao_notas AN, Avaliacao A, Grupos_usuario G where PIA.cod_item=".$linha['cod_item']." and PIA.status='A' and PIA.cod_nota=AN.cod_nota and (AN.tipo_compartilhamento='F' or (AN.tipo_compartilhamento='G' and AN.cod_aluno=G.cod_usuario and G.cod_grupo!=".$linha_grupo['cod_grupo'].")) and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
              $res=Enviar($sock,$query);
              if (RetornaNumLinhas($res) > 0)
              {
                $num_nao_avaliados++;
              }
            }
          }
        }
        else
        {
          $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao_notas AN, Avaliacao A where PIA.cod_item=".$linha['cod_item']." and PIA.status='A' and PIA.cod_nota=AN.cod_nota and (AN.tipo_compartilhamento='T') and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
          $res=Enviar($sock,$query);
          if (RetornaNumLinhas($res) == 0)      //N� encontrou nenhuma avaliacao para este item que esteja compartilhada com o grupo
          {
            $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao_notas AN, Avaliacao A,Grupos_usuario G where PIA.cod_item=".$linha['cod_item']." and PIA.status='A' and PIA.cod_nota=AN.cod_nota and (AN.tipo_compartilhamento='F' or (AN.tipo_compartilhamento='G')) and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
            $res=Enviar($sock,$query);
            if (RetornaNumLinhas($res) > 0)
            {
              $num_nao_avaliados++;
            }
          }
        }
      }
    }
  }
  return $num_nao_avaliados;
}


/* *********************************************************************
   ExisteItemNaoAvaliado - Verifica se existe algum item associado a avaliacao selecionada que nao foi avaliado
   Entrada: $sock - BASE DO CURSO
            $cod_aluno - c�igo do aluno

   Saida:     numero de avaliacoes
*/
function ExisteItemNaoAvaliado($sock,$cod_avaliacao,$cod,$portfolio_grupo,$cod_topico_raiz)
{
  $ha_nao_avaliado=0;

  if ($portfolio_grupo)
  {
    $tipo='G';
    $query="select * from Portfolio_itens where cod_grupo=".$cod." and cod_topico!=2";
  }
  else
  {
    $tipo='I';
    $query="select * from Portfolio_itens where cod_usuario=".$cod." and cod_topico!=2 and cod_grupo IS NULL";
  }
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  if (count($lista)>0)
  {
    while (( list($cod, $linha) = each($lista) ) && !$ha_nao_avaliado)
    {
      $query="select * from Portfolio_itens_avaliacao PIA, Avaliacao A where PIA.cod_avaliacao=".$cod_avaliacao." and PIA.cod_item=".$linha['cod_item']." and PIA.status='N' and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
      $res=Enviar($sock,$query);
      if (RetornaNumLinhas($res) > 0)
      {
        $ha_nao_avaliado=1;
      }
    }
  }
  return $ha_nao_avaliado;
}

?>
