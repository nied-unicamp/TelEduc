<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/configurar/configurar.inc

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

/* ==========================================================
   ARQUIVO : cursos/aplic/configurar/configurar.inc
   ========================================================== */

/* *******************************************************************
   RetornaPlanoNotificacao - retorna o plano de envio de notifica��es de
                             novidades do ambiente por e-mail.
   entrada: $sock - sock de conex�o,
            $cod_usuario - c�digo do usu�rio.
   saida:   char:
                    0 - n�o receber,
                    1 - resumo uma vez por dia,
                    2 - resumo parcial, duas vezes por dia,
                    3 - a cada novidade.
*/

function RetornaPlanoNotificacao($sock, $cod_usuario, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select notificar_email from ".$dbnamebase.".Usuario_config where cod_usuario = ".$cod_usuario." and cod_curso=".$cod_curso;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha[0]);
}


/* *******************************************************************
   AlteraPlanoNotificacao - altera o plano de notifica��o de novidades
                            do ambiente por e-mail.
   entrada: $sock - sock de conex�o,
            $cod_usuario - c�digo do usu�rio,
            $plano - plano de notifica��o:
                                           0 - n�o receber,
                                           1 - resumo uma vez por dia,
                                           2 - resumo parcial, duas vezes por dia,
                                           3 - a cada novidade.
   saida:   true em caso de sucesso, do contr�rio, false.
*/

function AlteraPlanoNotificacao($sock, $cod_usuario, $plano, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "update ".$dbnamebase.".Usuario_config set notificar_email = '".$plano."' where cod_usuario = ".$cod_usuario." and cod_curso=".$cod_curso;
  $res = Enviar($sock, $query);
  return($res);
}

function AtualizaPlanoNotificacaoDinamic($dadosForm)
{
  $objResponse = new xajaxResponse();

  $cod_curso=$dadosForm['cod_curso'];
  $cod_usuario=$dadosForm['cod_usuario'];
  $plano=$dadosForm['plano'];

  $sock=Conectar($cod_curso);

  if (!AlteraPlanoNotificacao($sock, $cod_usuario, $plano, $cod_curso))
  {
    $lista_frases = RetornaListaDeFrases($sock, -7);
    /* 26 - Erro ao alterar o plano de notifica��es de novidades */
    $objResponse->alert(RetornaFraseDaLista($lista_frases, 26));
  }

  Desconectar($sock);

  $objResponse->script("Recarregar();");

  //  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

?>
