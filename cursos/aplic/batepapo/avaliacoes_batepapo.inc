<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/avaliacoes_batepapo.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
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

    Nied - Núcleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
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
   AtualizaStatusAvaliacao - Atualiza o status da avaliacão
   Entrada: $sock - BASE DO CURSO
            $cod_sessoes - array com as sessoes a serem apagadas
            $açao - Status a ser colocado
            $cod_usuario - Codigo do usuario
   Saida: Nenhuma
*/
function AtualizaStatusAvaliacao($sock,$cod_sessoes,$acao,$cod_usuario)
{
  foreach($cod_sessoes as $num => $cod_sessao)
  {
    $assunto=RetornaAssuntoSessao($sock,$cod_sessao);
    if (strcmp($assunto,"(Sessão não agendada)"))
    {
      $cod_assunto=RetornaCodAssuntoBatePapo($sock,$cod_sessao);
      $sessoes=RetornaSessoesDeUmAssunto($sock,$cod_assunto);
      if ($cod_assunto!="")
      {
        if (count($sessoes)==1) //se houver mais de uma sessao relacionada ao assunto nao pode apagar ou excluir a avaliaçao,pois pode haver alguma sessao que nao foi apagada ou excluida
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

        if ($acao=='A')     //Recuperar sessao de bate-papo e avaliação
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
   ExcluiAvaliacaoDesmarcada - Atualiza o status da avaliacão
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
   EhAvaliacaoBatePapo - Verifica se a sessão de bate-papo é uma avaliação
   Entrada:  $sock - BASE DO CURSO
            $cod_assunto - Codigo do Assunto da sessão de bate-papo
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
   BatePapoEhAvaliacao - Verifica se a sessão de bate-papo é uma avaliação
   Entrada:  $sock - BASE DO CURSO
            $cod_sessao - sessao visualizada
            $assunto - Assunto da sessão de bate-papo
   Saida: Nenhuma

*/

function BatePapoEhAvaliacao($sock,$assunto,$di,$df)
{
  /*
      Ve se um dos assuntos é avaliação
  */
  $assunto_unico = explode("<br>", $assunto);
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
             $cod_atividade -  Código do Assunto da sessão de Bate-Papo

   Saida:   Código da Avaliação
*/
function RetornaCodAvaliacao($sock,$cod_atividade)
{
  $query = "select cod_avaliacao from Avaliacao where cod_atividade = ".$cod_atividade." and ferramenta='B' and status != 'X' and status != 'A' and status != 'C'";
  $res = Enviar($sock, $query);
  $codigo = RetornaLinha($res);
  return($codigo[0]);
}


/* *********************************************************************
   FoiAvaliado - Verifica se o aluno já foi avaliado
   Entrada: $sock - BASE DO CURSO
            $cod_aluno - aluno que está sendo avaliado
            $cod_avaliacao - avaliacao que está sendo corrigida
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
   BatePapoEraAvaliacao - Verifica se a sessão de bate-papo era uma avaliação, ou seja se agora está apagada
   Entrada:  $sock - BASE DO CURSO
            $cod_sessao - sessao visualizada
            $assunto - Assunto da sessão de bate-papo
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
             $cod_atividade -  Código do Assunto da sessão de Bate-Papo

   Saida:   Código da Avaliação
*/
function RetornaCodAvaliacaoApagada($sock,$cod_atividade)
{
  $query = "select cod_avaliacao from Avaliacao where cod_atividade = ".$cod_atividade." and ferramenta='B' and status = 'A'";
  $res = Enviar($sock, $query);
  $codigo = RetornaLinha($res);
  return($codigo[0]);
}


/* ******************************************************
  ApagaAvaliacaoBatepapo - Muda o status da Avaliação associada a sessão de bate-papo
    para 'A', impedindo que sejam visualizadas.

  Entradas: $sock - sock de conexao,
            $cod_avaliacao - código da avaliacao.
  Saida:    true se bem-sucedido, do contrário, false.

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
   AvaliacaoFoiApagada - Verifica se a sessão de bate-papo era uma avaliação, ou seja se é uma avaliação apagada
   Entrada:  $sock - BASE DO CURSO
            $cod_assunto - Codigo do Assunto da sessão de bate-papo
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
  RecuperaAvaliacaoBatepapo - Muda o status da Avaliação associada a sessão de bate-papo
    para 'F', permitindo que sejam visualizadas.

  Entradas: $sock - sock de conexao,
            $cod_avaliacao - código da avaliacao.
            $cod_usuario - codigo do usuario que recuperou a avaliaçao
  Saida:    true se bem-sucedido, do contrário, false.

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
  ExcluiAvaliacaoBatepapo - Muda o status da Avaliação associada a sessão de bate-papo
    para 'X', impedindo que sejam visualizadas.

  Entradas: $sock - sock de conexao,
            $cod_avaliacao - código da avaliacao.
            $cod_usuario - codigo do usuario que recuperou a avaliaçao
  Saida:    true se bem-sucedido, do contrário, false.

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
