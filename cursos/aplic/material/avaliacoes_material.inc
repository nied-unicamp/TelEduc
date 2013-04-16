<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/avaliacoes_material.inc

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
  ARQUIVO : cursos/aplic/material/avaliacoes_material.inc
  ========================================================== */

/*
Funcoes nessa biblioteca:

RetornaAvaliacaoPortfolio
RetornaAvaliacaoApagada
AlteraCadastroAvaliacao
AtividadeEraAvaliacao
RetornaCodAvaliacaoApagada
RecuperaAvaliacao
ExcluiAvaliacao
AtividadeEhAvaliacao
RetornaCodAvaliacao
CancelaEdicaoAvaliacao
RetornaStatusAvaliacao
RetornaAvaliacao
ApagaAvaliacao
*/

/******************************************************************************************************
   RetornaAvaliacaoPortfolio - Retorna um array com os dados da avaliacao associada ao item
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - item
   Saida: array com []['cod_avaliacao']
                    []['cod_usuario']
                    []['comentario']
                    []['tipo']
                    []['valor']
                    []['tipo_compartilhamento']
                    []['data_inicio']
                    []['data_termino']
*******************************************************************************************************/
function RetornaAvaliacaoPortfolio ($sock, $tabela, $cod_item)
{
  $consulta="select cod_avaliacao,cod_usuario,objetivos,criterios,tipo,valor,status,data_inicio,data_termino from ".$tabela." where cod_atividade=".$cod_item." and ferramenta='P' and status != 'X' and status != 'A' and status != 'C' and status != 'T'";
  $res=Enviar($sock,$consulta);
  $num=RetornaNumLinhas($res);
  if ($num>0)
  {
    $linha=RetornaLinha($res);
    return($linha);
  }
  return;
}


/**************************************************************************************************
   RetornaAvaliacaoApagada - Retorna um array com os dados da avaliacao associada ao item
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - item
   Saida: array com []['cod_avaliacao']
                    []['cod_usuario']
                    []['comentario']
                    []['tipo']
                    []['valor']
                    []['tipo_compartilhamento']
                    []['data_inicio']
                    []['data_termino']
*****************************************************************************************************/
function RetornaAvaliacaoApagada ($sock, $tabela, $cod_item)
{
  $consulta="select cod_avaliacao,cod_usuario,objetivos,criterios,tipo,valor,status,data_inicio,data_termino from ".$tabela." where cod_atividade=".$cod_item." and ferramenta='P' and status != 'X' and status != 'C' and status != 'T'";
  $res=Enviar($sock,$consulta);
  $num=RetornaNumLinhas($res);
  if ($num>0)
  {
    $linha=RetornaLinha($res);
    return($linha);
  }
  return;
}


/********************************************************************************************************
   AlteraCadastroAvaliacao - Altera o cadastro de avaliacao de uma atividade
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_formador - Codigo do formador que cadastrou
            $objetivos - objetivos da avaliacao
            $criterios - criterios da avaliacao
            $tipo -tipo da atividade:individual ou em grupo
            $valor - Valor da avaliacao
            $data_inicio - Data de inicio da Atividade
            $data_termino - Data de termino da Atividade
            $cod_avaliacao - codigo que identifica a atividade em que a avaliacaao ira atuar
   Saida: nenhuma
*********************************************************************************************************/
function AlteraCadastroAvaliacao($sock,$tabela,$cod_formador,$objetivos, $criterios,$tipo,$valor,$data_inicio,$data_termino,$cod_avaliacao)
{
  $consulta="update ".$tabela." set cod_usuario=".$cod_formador.",objetivos='".$objetivos."',criterios='".$criterios."',tipo='".$tipo."',valor=".$valor.",status='F',data=".time().",data_inicio=".$data_inicio.",data_termino=".$data_termino." where cod_avaliacao=".$cod_avaliacao."";
  $res=Enviar($sock, $consulta);
  $consulta="insert into ".$tabela."_historicos values (".$cod_avaliacao.", ".$cod_formador.", ".time().", 'F')";
  $res=Enviar($sock,$consulta);
}


/******************************************************************************************************
   AtividadeEraAvaliacao - Verifica se a atividade era uma avaliacao, ou seja, se foi apagada
   Entrada:  $sock - BASE DO CURSO
            $cod_item - codigo do item da atividade a ser verificada
   Saida: true or false
*******************************************************************************************************/
function AtividadeEraAvaliacao ($sock,$cod_item)
{
  $query="select * from Avaliacao where cod_atividade=".$cod_item." and ferramenta='P' and status = 'A'";
  $res=Enviar($sock,$query);
  if (RetornanumLinhas($res) > 0)
  {
    return true;
  }
  else
  {
    return false;
  }
}


/*********************************************************************************************
  RetornaCodAvaliacaoApagada - Retorna  o codigo da Avaliacao
   Entrada:  $sock - BASE DO CURSO
             $cod_atividade -  Codigo do Forum
   Saida:   Codigo da Avaliacao
*********************************************************************************************/
function RetornaCodAvaliacaoApagada($sock,$cod_atividade)
{
  $query = "select cod_avaliacao from Avaliacao where cod_atividade = ".$cod_atividade." and ferramenta='P' and status = 'A'";
  $res = Enviar($sock, $query);
  $codigo = RetornaLinha($res);
  return($codigo[0]);
}


/**********************************************************************************
  RecuperaAvaliacao - Muda o status da Avaliacao associada a atividade
    para 'F', permitindo que sejam visualizadas.
  Entradas: $sock - sock de conexao,
            $cod_avaliacao - codigo da avaliacao.
            $cod_usuario - codigo do usuario
  Saida:    true se bem-sucedido, do contrario, false.
************************************************************************************/
function RecuperaAvaliacao($sock, $cod_avaliacao,$cod_usuario)
{
  $query = "update Avaliacao set status = 'F' where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock, $query);
   $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'R')";
  $res=Enviar($sock,$consulta);
  return($res);
}


/*********************************************************************************
  ExcluiAvaliacao - Muda o status da Avaliacao associada a atividade
    para 'X', impedindo que sejam visualizadas.
  Entradas: $sock - sock de conexao,
            $cod_avaliacao - codigo da avaliacao.
            $cod_usuario - codigo do usuario
  Saida:    true se bem-sucedido, do contrario, false.
*********************************************************************************/
function ExcluiAvaliacao($sock, $cod_avaliacao,$cod_usuario)
{
  $query = "update Avaliacao set status = 'X' where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock, $query);
   $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'X')";
  $res=Enviar($sock,$consulta);
  return($res);
}


/************************************************************************************************
   AtividadeEhAvaliacao - Verifica se a atividade é uma avaliacao
   Entrada:  $sock - BASE DO CURSO
            $cod_item - codigo do item da atividade a ser verificada
   Saida: true or false
*************************************************************************************************/
function AtividadeEhAvaliacao ($sock,$cod_item)
{
  $query="select * from Avaliacao where cod_atividade=".$cod_item." and ferramenta='P' and status != 'X' and status != 'A' and status != 'C'";
  $res=Enviar($sock,$query);
  if (RetornanumLinhas($res) > 0)
  {
    return true;
  }
  else
  {
    return false;
  }
}


/***********************************************************************************************
  RetornaCodAvaliacao - Retorna  o codigo da Avaliacao
   Entrada:  $sock - BASE DO CURSO
             $cod_atividade -  Codigo do Forum
   Saida:   Codigo da Avaliacao
************************************************************************************************/
function RetornaCodAvaliacao($sock,$cod_atividade)
{
  $query = "select cod_avaliacao from Avaliacao where cod_atividade = ".$cod_atividade." and ferramenta='P' and status != 'X' and status != 'A' and status != 'C'";
  $res = Enviar($sock, $query);
  $codigo = RetornaLinha($res);
  return($codigo[0]);
}


/*****************************************************************************
   CancelaEdicaoAvaliacao - Cancela a edicao de uma avaliacao
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacao)
            $cod_avaliacao - codigo da avaliacao
            $cod_usuario - codigo do usuario
   Saida: true se a edicao foi cancelada corretamente
*****************************************************************************/
function CancelaEdicaoAvaliacao ($sock, $tabela, $cod_avaliacao,$cod_usuario)
{
  $linha=RetornaStatusAvaliacao($sock, $tabela, $cod_avaliacao);

  if ($linha['status']=="C")
  {
    $consulta="delete from ".$tabela." where cod_avaliacao=".$cod_avaliacao;
    $res=Enviar($sock, $consulta);

    $consulta="delete from ".$tabela."_historicos where cod_avaliacao=".$cod_avaliacao;
    $res=Enviar($sock, $consulta);
    return true;
  }
  elseif ($linha['status']=="E")
  {
    $consulta="update ".$tabela." set status='F' where cod_avaliacao=".$cod_avaliacao;
    $res=Enviar($sock, $consulta);

    $consulta="insert into ".$tabela."_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'D')";
    $res=Enviar($sock, $consulta);
    return true;
  }
}


/*****************************************************************************
  RetornaStatusAvaliacao - retorna o status da avaliacao
  Entrada: $sock - sock de conexao
           $tabela - tabela de status
           $cod_avaliacao - codigo da avaliacao
  Saida: array com []['cod_avaliacao']
                    []['cod_usuario']
                    []['comentario']
                    []['tipo']
                    []['valor']
                    []['tipo_compartilhamento']
                    []['data_inicio']
                    []['data_termino']
*****************************************************************************/
function RetornaStatusAvaliacao($sock, $tabela, $cod_avaliacao)
{
  $query="Select status from ".$tabela." where cod_avaliacao=".$cod_avaliacao;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return($linha);

}


/*******************************************************************************************
  RetornaAvaliacao - Retorna os Dados de avaliacao da atividade
   Entrada:  $sock - BASE DO CURSO
             $cod_atividade - c�digo da atividade
   Saida:
          array['Cont']= Numero de Avaliacoes retornadas;
          array['Cod_avaliacao']= Codigo da Avaliacao;
          array['Status']= Status da avaliacao;
          array['Data']= Data de criacao da Avaliacao;
**********************************************************************************************/
function RetornaAvaliacao($sock,$cod_atividade)
{
   $query1="select count(*) from Avaliacao where cod_atividade=".$cod_atividade." and ferramenta='P' and status!='X'";
   $res1=Enviar($sock,$query1);
   $num=RetornaLinha($res1);

   $query="select cod_avaliacao,cod_usuario,status,data from Avaliacao where cod_atividade=".$cod_atividade." and ferramenta='P' and status!='X'";
   $res=Enviar($sock,$query);
   $linha=RetornaLinha($res);

   unset($retorno);
   $retorno['Cont']=$num[0];
   $retorno['Cod_avaliacao']=$linha['cod_avaliacao'];
   $retorno['Cod_usuario']=$linha['cod_usuario'];
   $retorno['Status']=$linha['status'];
   $retorno['Data']=$linha['data'];
   return $retorno;
}


/****************************************************************************************************
  ApagaAvaliacao - Muda o status da Avaliacao associada ao forum para 'A', impedindo que sejam 
                   visualizadas.
  Entradas: $sock - sock de conexao,
            $cod_avaliacao - cdigo da avaliacao.
            $cod_usuario - codigo do usuario
  Saida:    true se bem-sucedido, do contrario, false.
*****************************************************************************************************/
function ApagaAvaliacao($sock,$cod_avaliacao,$cod_usuario)
{
  $query = "update Avaliacao set status = 'A' where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock, $query);
   $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'A')";
  $res=Enviar($sock,$consulta);
  return($res);
}
?>
