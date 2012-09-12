<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/avaliacoes_batepapo.inc

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
  ARQUIVO : cursos/aplic/batepapo/avaliacoes_batepapo.inc
  ========================================================== */


/* ************************************************************************
   AtualizaStatusAvaliacao - Atualiza o status da avaliac�o
   Entrada: $sock - BASE DO CURSO
            $cod_sessoes - array com as sessoes a serem apagadas
            $a�ao - Status a ser colocado
            $cod_usuario - Codigo do usuario
   Saida: Nenhuma
*/
function AtualizaStatusAvaliacao($sock,$cod_sessoes,$acao,$cod_usuario)
{
  foreach($cod_sessoes as $num => $cod_sessao)
  {
    $assunto=RetornaAssuntoSessao($sock,$cod_sessao);
    if (strcmp($assunto,"(Sess�o n�o agendada)"))
    {
      $cod_assunto=RetornaCodAssuntoBatePapo($sock,$cod_sessao);
      $sessoes=RetornaSessoesDeUmAssunto($sock,$cod_assunto);
      if ($cod_assunto!="")
      {
        if (count($sessoes)==1) //se houver mais de uma sessao relacionada ao assunto nao pode apagar ou excluir a avalia�ao,pois pode haver alguma sessao que nao foi apagada ou excluida
        {
          if ($acao=='L')
          {
            if (EhAvaliacaoBatePapo($sock,$cod_assunto))
            {
              $cod_avaliacao=RetornaCodAvaliacao($sock,$cod_assunto);
              ApagaAvaliacaoBatepapo($sock,$cod_avaliacao,$cod_usuario);
            }
          }
          elseif($acao=='X')
          {
            if (AvaliacaoFoiApagada($sock,$cod_assunto))
            {
              $cod_avaliacao=RetornaCodAvaliacaoApagada($sock,$cod_assunto);
              ExcluiAvaliacaoBatepapo($sock, $cod_avaliacao,$cod_usuario);
            }
          }
        }

        if ($acao=='A')     //Recuperar sessao de bate-papo e avalia��o
        {
          if (AvaliacaoFoiApagada($sock,$cod_assunto))
          {
            $cod_avaliacao=RetornaCodAvaliacaoApagada($sock,$cod_assunto);
            RecuperaAvaliacaoBatepapo($sock,$cod_avaliacao,$cod_usuario);
          }
        }
      }
    }
  }
}


/* ************************************************************************
   ExcluiAvaliacaoDesmarcada - Atualiza o status da avaliac�o
   Entrada: $sock - BASE DO CURSO
            $cod_assuntos - array com os assuntos a serem desmarcados
            $cod_usuario - Codigo do usuario
   Saida: Nenhuma
*/
function ExcluiAvaliacaoDesmarcada ($sock,$cod_assuntos,$cod_usuario)
{
  foreach($cod_assuntos as $num => $cod_assunto)
  {
    if ($cod_assunto!="")
    {
      if (EhAvaliacaoBatePapo($sock,$cod_assunto))
      {
        $cod_avaliacao=RetornaCodAvaliacao($sock,$cod_assunto);
        ExcluiAvaliacaoBatepapo($sock,$cod_avaliacao,$cod_usuario);
      }
    }
  }
}


/* ********************************************************************************
   EhAvaliacaoBatePapo - Verifica se a sess�o de bate-papo � uma avalia��o
   Entrada:  $sock - BASE DO CURSO
            $cod_assunto - Codigo do Assunto da sess�o de bate-papo
   Saida: Nenhuma

*/

function EhAvaliacaoBatePapo($sock,$cod_assunto)
{
  $query="select cod_avaliacao from Avaliacao where cod_atividade=".$cod_assunto." and ferramenta='B' and status!='X'and status != 'A' and status != 'C'";
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
   BatePapoEhAvaliacao - Verifica se a sess�o de bate-papo � uma avalia��o
   Entrada:  $sock - BASE DO CURSO
            $cod_sessao - sessao visualizada
            $assunto - Assunto da sess�o de bate-papo
   Saida: Nenhuma

*/

function BatePapoEhAvaliacao($sock,$assunto,$di,$df)
{
  /*
      Ve se um dos assuntos � avalia��o
  */
  $assunto_unico = explode("<br/>", $assunto);
  foreach($assunto_unico as $assunto_u){
  	$cod_assunto=RetornaCodAssunto($sock,$assunto_u,$di,$df);
  	//echo ("Cod_assunto = ".$cod_assunto);
 	 $query="select cod_avaliacao from Avaliacao where cod_atividade=".$cod_assunto." and ferramenta='B' and status!='X'and status != 'A' and status != 'C'";
 	 $res=Enviar($sock,$query);
 	 if (RetornanumLinhas($res) > 0)
  	 {
   	 	return true;
  	 }
  }
  return false;
}


/* ********************************************************************************
  RetornaCodAvaliacao - Retorna  o codigo da Avaliacao
   Entrada:  $sock - BASE DO CURSO
             $cod_atividade -  C�digo do Assunto da sess�o de Bate-Papo

   Saida:   C�digo da Avalia��o
*/
function RetornaCodAvaliacao($sock,$cod_atividade)
{
  $query = "select cod_avaliacao from Avaliacao where cod_atividade = ".$cod_atividade." and ferramenta='B' and status != 'X' and status != 'A' and status != 'C'";
  $res = Enviar($sock, $query);
  $codigo = RetornaLinha($res);
  return($codigo[0]);
}


/* *********************************************************************
   FoiAvaliado - Verifica se o aluno j� foi avaliado
   Entrada: $sock - BASE DO CURSO
            $cod_aluno - aluno que est� sendo avaliado
            $cod_avaliacao - avaliacao que est� sendo corrigida
   Saida: nenhuma
*/

function FoiAvaliado($sock,$cod_avaliacao,$cod_aluno)
{
   $query="select * from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and  cod_aluno=".$cod_aluno." and ((status='A') || (status='F'))";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
    return true;
  else
    return false;
}


/* ********************************************************************************
   BatePapoEraAvaliacao - Verifica se a sess�o de bate-papo era uma avalia��o, ou seja se agora est� apagada
   Entrada:  $sock - BASE DO CURSO
            $cod_sessao - sessao visualizada
            $assunto - Assunto da sess�o de bate-papo
   Saida: Nenhuma

*/

function BatePapoEraAvaliacao($sock,$assunto,$di,$df)
{
  $cod_assunto=RetornaCodAssunto($sock,$assunto,$di,$df);
  $query="select cod_avaliacao from Avaliacao where cod_atividade=".$cod_assunto." and ferramenta='B' and status='A'";
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
  RetornaCodAvaliacaoApagada - Retorna  o codigo da Avaliacao
   Entrada:  $sock - BASE DO CURSO
             $cod_atividade -  C�digo do Assunto da sess�o de Bate-Papo

   Saida:   C�digo da Avalia��o
*/
function RetornaCodAvaliacaoApagada($sock,$cod_atividade)
{
  $query = "select cod_avaliacao from Avaliacao where cod_atividade = ".$cod_atividade." and ferramenta='B' and status = 'A'";
  $res = Enviar($sock, $query);
  $codigo = RetornaLinha($res);
  return($codigo[0]);
}


/* ******************************************************
  ApagaAvaliacaoBatepapo - Muda o status da Avalia��o associada a sess�o de bate-papo
    para 'A', impedindo que sejam visualizadas.

  Entradas: $sock - sock de conexao,
            $cod_avaliacao - c�digo da avaliacao.
  Saida:    true se bem-sucedido, do contr�rio, false.

*/
function ApagaAvaliacaoBatepapo($sock, $cod_avaliacao,$cod_usuario)
{
  $query = "update Avaliacao set status = 'A' where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock, $query);
  $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'A')";
  $res=Enviar($sock,$consulta);

  return($res);
}


/* ********************************************************************************
   AvaliacaoFoiApagada - Verifica se a sess�o de bate-papo era uma avalia��o, ou seja se � uma avalia��o apagada
   Entrada:  $sock - BASE DO CURSO
            $cod_assunto - Codigo do Assunto da sess�o de bate-papo
   Saida: Nenhuma

*/

function AvaliacaoFoiApagada($sock,$cod_assunto)
{
  $query="select cod_avaliacao from Avaliacao where cod_atividade=".$cod_assunto." and ferramenta='B' and status='A'";
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


/* ******************************************************
  RecuperaAvaliacaoBatepapo - Muda o status da Avalia��o associada a sess�o de bate-papo
    para 'F', permitindo que sejam visualizadas.

  Entradas: $sock - sock de conexao,
            $cod_avaliacao - c�digo da avaliacao.
            $cod_usuario - codigo do usuario que recuperou a avalia�ao
  Saida:    true se bem-sucedido, do contr�rio, false.

*/
function RecuperaAvaliacaoBatepapo($sock,$cod_avaliacao,$cod_usuario)
{
  $query = "update Avaliacao set status='F' where cod_avaliacao=".$cod_avaliacao;
  $res = Enviar($sock, $query);
  $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'R')";
  $res=Enviar($sock,$consulta);

  return($res);
}



/* ******************************************************
  ExcluiAvaliacaoBatepapo - Muda o status da Avalia��o associada a sess�o de bate-papo
    para 'X', impedindo que sejam visualizadas.

  Entradas: $sock - sock de conexao,
            $cod_avaliacao - c�digo da avaliacao.
            $cod_usuario - codigo do usuario que recuperou a avalia�ao
  Saida:    true se bem-sucedido, do contr�rio, false.

*/
function ExcluiAvaliacaoBatepapo($sock, $cod_avaliacao,$cod_usuario)
{
  $query = "update Avaliacao set status = 'X' where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock, $query);
  $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'X')";
  $res=Enviar($sock,$consulta);

  return($res);
}

?>
