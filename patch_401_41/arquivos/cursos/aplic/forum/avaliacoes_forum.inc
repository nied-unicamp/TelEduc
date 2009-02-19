<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/forum/avaliacoes_forum.inc

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
  ARQUIVO : cursos/aplic/forum/avaliacoes_forum.inc
  ========================================================== */


/* ********************************************************************************
   ForumEhAvaliacao - Verifica se o forum é uma avaliaçao
   Entrada: $sock - sock de conexao
            $cod_forum - codigo do forum
   Saida: true se bem-sucedido, do contrario, false.
***********************************************************************************/
function ForumEhAvaliacao ($sock,$cod_forum)
{
  $query="select * from Avaliacao where cod_atividade=".$cod_forum." and ferramenta='F' and status != 'X' and status != 'A' and status != 'C'";
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


/************************************************************************
  ApagaAvaliacaoForum - Muda o status da Avaliaçao associada ao forum
                        para 'X', impedindo que sejam visualizadas.
  Entradas: $sock - sock de conexao,
            $cod_avaliacao - codigo da avaliacao.
            $cod_usuario - codigo do usuario
  Saida: true se bem-sucedido, do contrario, false.
************************************************************************/
function ApagaAvaliacaoForum($sock, $cod_avaliacao,$cod_usuario)
{
  $query = "update Avaliacao set status = 'A' where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock, $query);
   $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'A')";
  $res=Enviar($sock,$consulta);
  return($res);
}


/* ********************************************************************************
  RetornaCodAvaliacao - Retorna o codigo da Avaliacao
   Entrada: $sock - sock de conexao
             $cod_atividade - Codigo do Forum
   Saida: Codigo da Avaliacao
***********************************************************************************/
function RetornaCodAvaliacao($sock,$cod_atividade)
{
  $query = "select cod_avaliacao from Avaliacao where cod_atividade = ".$cod_atividade." and ferramenta='F' and status != 'X' and status != 'A' and status != 'C'";
  $res = Enviar($sock, $query);
  $codigo = RetornaLinha($res);
  return($codigo[0]);
}


/* ********************************************************************************
   ForumEraAvaliacao - Verifica se o forum é uma avaliaçao
   Entrada:  $sock - sock de conexao
            $cod_forum - forum a ser verificado
   Saida: true se bem-sucedido, do contrario, false.
************************************************************************************/
function ForumEraAvaliacao ($sock,$cod_forum)
{
  $query="select * from Avaliacao where cod_atividade=".$cod_forum." and ferramenta='F' and status = 'A'";
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

/* ********************************************************************************
  RetornaCodAvaliacaoDeletada - Retorna  o codigo da Avaliacao
   Entrada:  $sock - sock de conexao
             $cod_atividade - Codigo do Forum
   Saida: Codigo da Avaliaçao
************************************************************************************/
function RetornaCodAvaliacaoDeletada($sock,$cod_atividade)
{
  $query = "select cod_avaliacao from Avaliacao where cod_atividade = ".$cod_atividade." and ferramenta='F' and status = 'A'";
  $res = Enviar($sock, $query);
  $codigo = RetornaLinha($res);
  return($codigo[0]);
}


/* ******************************************************
  RecuperaAvaliacaoExcluida - Muda o status da Avaliaçao para 'A', voltando para o status 
                              em que estava. Esta funçao so é chamada qdo se excluiu ou 
                              recuperou a avaliaçao do forum e deu erro qdo foi excluir ou recuperar 
                              o forum
  Entradas: $sock - sock de conexao,
            $cod_avaliacao - codigo da avaliacao.
            $cod_usuario - codigo do usuario que esta recuperando a avaliaçao
  Saida:    true se bem-sucedido, do contrario, false.
*********************************************************************************************/
function RecuperaAvaliacaoExcluida($sock, $cod_avaliacao,$cod_usuario)
{
  $query = "update Avaliacao set status = 'A' where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock, $query);
   $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'A')";
  $res=Enviar($sock,$consulta);
  return($res);
}


/*****************************************************************************
  RecuperaAvaliacaoForum - Muda o status da Avaliaçao associada ao forum
                           para 'F', permitindo que sejam visualizadas.

  Entradas: $sock - sock de conexao,
            $cod_avaliacao - codigo da avaliacao.
            $cod_usuario - codigo do usuario
  Saida:    true se bem-sucedido, do contrario, false.

*******************************************************************************/
function RecuperaAvaliacaoForum($sock, $cod_avaliacao,$cod_usuario)
{
  $query = "update Avaliacao set status = 'F' where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock, $query);
   $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'R')";
  $res=Enviar($sock,$consulta);
  return($res);
}


/* ********************************************************************************
  RetornaDadosAvaliacao - Retorna os Dados de avalia��o do forum a ser avaliado
   Entrada:  $sock - sock de conexao
             $cod_atividade - codigo do forum
   Saida: array com: ['Cod_avaliacao']= Codigo da avaliacao;
                     ['Tipo']= Tipo da Avaliacao;
                     ['Valor']= Valor da Avaliacao;
                     ['Data_inicio']= Data de inicio da Avaliacao;
                     ['Data_termino']= Data de termino da Avaliaçao;
*************************************************************************************/
function RetornaDadosAvaliacao($sock,$cod_atividade)
{
   $query="select cod_avaliacao,valor,data_inicio,data_termino from Avaliacao where cod_atividade=".$cod_atividade." and ferramenta='F' and status != 'X' and status != 'A' and status != 'C'";
   $res=Enviar($sock,$query);
   $linha=RetornaLinha($res);
   $retorno = "";
   unset($retorno);
   $retorno['Cod_avaliacao']=$linha['cod_avaliacao'];
   $retorno['Tipo']=$linha['tipo'];
   $retorno['Valor']=$linha['valor'];
   $retorno['Data_inicio']=$linha['data_inicio'];
   $retorno['Data_termino']=$linha['data_termino'];
   return $retorno;
}

?>