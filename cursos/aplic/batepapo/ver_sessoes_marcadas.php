<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/ver_sessoes_marcadas.php

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
  ARQUIVO : cursos/aplic/batepapo/ver_sessoes_marcadas.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");
  include("avaliacoes_batepapo.inc");

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  // 82 - Sess�o marcada com sucesso.
  // 115 - Erro ao marcar a sess�o.
  $feedbackObject->addAction("sessao_marcada", 53, 115);

  $AcessoAvaliacao = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);
  $e_formador      = EFormador($sock,$cod_curso,$cod_usuario);

  echo("    <script type=\"text/javascript\" language=\"javascript\">\n\n");
  if($AcessoAvaliacao)
  {
    echo("      function VerAvaliacao(id)\n");
    echo("      {\n");
    echo("        window.open(\"../avaliacoes/ver_popup.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&cod_avaliacao=\"+id,\"VerAvaliacao\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("        return(false);\n");
    echo("      }\n");
  }

  echo("      function Iniciar() \n");
  echo("      {\n");
                $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList(); \n");
  echo("      }\n");

  echo("      function OpenWindow() \n");
  echo("      {\n");
  echo("        window.open(\"entrar_sala.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\",\"Batepapo\",\"width=1000,height=700,top=50,left=50,scrollbars=no,status=yes,toolbar=no,menubar=no,resizable=no\");\n");
  echo("        return(false);\n");
  echo("      }\n");

  echo("    </script>\n");

  include("../menu_principal.php");

  echo("<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* 1 - Bate-Papo */
  echo("<h4>".RetornaFraseDaLista($lista_frases,1));
  /* 58 - Ver sess�es marcadas */
  echo(" - ".RetornaFraseDaLista($lista_frases,58)."</h4>");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("  <div id=\"mudarFonte\">\n");
  echo("    <a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("    <a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("    <a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("  </div>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");

  echo("      <ul class=\"btAuxTabs\">\n");
  /* 27 - Ver sess�es realizadas */
  echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 27)."\" onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 27)."</span></li>\n");
  if ($e_formador)
  {
    /* 47 - Marcar sess�o */
    echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 47)."\" onClick=\"document.location='marcar_sessao.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 47)."</span></li>\n");
    /* 63 - Desmarcar sess�es */
    echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 63)."\" onClick=\"document.location='desmarcar_sessoes.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 63)."</span></li>\n");

    /* 78 - Lixeira */
    echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 78)."\" onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;lixeira=sim';\">".RetornaFraseDaLista($lista_frases, 78)."</span></li>\n");
  }
  /* 55 - Pr�xima sess�o marcada */
  echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 55)."\" onClick=\"document.location='ver_sessoes_marcadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 55)."</span></li>\n");

  echo("      </ul>\n");

  echo("    </td>\n");
  echo("  </tr>\n");

  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");

  echo("      <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  /* 40 - Assunto da Sess�o */
  echo("          <td>".RetornaFraseDaLista($lista_frases,40)."</td>\n");
  /* 41 - Data */
  echo("          <td>".RetornaFraseDaLista($lista_frases,41)."</td>\n");
  /* 29 - In�cio */
  echo("          <td>".RetornaFraseDaLista($lista_frases,29)."</td>\n");
  /* 30 - Fim */
  echo("          <td>".RetornaFraseDaLista($lista_frases,30)."</td>\n");
  if($AcessoAvaliacao)
  {
    /* 88 - Avalia��o */
    echo("          <td>".RetornaFraseDaLista($lista_frases,88)."</td>\n");
  }
  echo("        </tr>\n");

  $lista=RetornaListaSessoesMarcadas($sock,$d_inicio,$d_fim);

  $i=0;
  if (count($lista)>0  && $lista!="")
  {
    foreach($lista as $cod => $linha)
    {
      if ($i==0)
        echo("        <tr>\n");
      else
        echo("        <tr>\n");
      $i = ($i + 1) % 2;
      echo("          <td>".$linha['assunto']."</td>\n");
      echo("          <td>".Unixtime2Data($linha['data_inicio'])."</td>\n");
      echo("          <td>".Unixtime2Hora($linha['data_inicio'])."</td>\n");
      echo("          <td>".Unixtime2Hora($linha['data_fim'])."</td>\n");
      if($AcessoAvaliacao)
      {
        if (BatePapoEhAvaliacao($sock,$linha['assunto'],$linha['data_inicio'],$linha['data_fim']))
        {
          $cod_assunto=RetornaCodAssunto($sock,$linha['assunto'],$linha['data_inicio'],$linha['data_fim']);
          $cod_avaliacao=RetornaCodAvaliacao($sock,$cod_assunto);
          // G 35 - Sim
          echo("          <td><a class=\"text\" href=\"#\" onClick='VerAvaliacao(".$cod_avaliacao.");return(false);'>".RetornaFraseDaLista($lista_frases_geral,35)."</a></td>");
        }
        else
          // G 36 - N�o
          echo("          <td>".RetornaFraseDaLista($lista_frases_geral,36)."</td>\n");
      }
      echo("        </tr>\n");
    }
  }
  else
  {
    echo("        <tr>\n");
    /* 59 - (N�o existe nenhuma sess�o marcada) */
    echo("          <td colspan=5>".RetornaFraseDaLista($lista_frases,59)."</td>\n");
    echo("        </tr>\n");
  }

  // Fim Tabela Interna
  echo("      </table>\n");

  echo("      <ul class=\"btAuxTabs03\">\n");
  /* 2 - Entrar na sala de bate-papo */
  echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 2)."\" onClick=\"return(OpenWindow());\">".RetornaFraseDaLista($lista_frases, 2)."</span></li>\n");
  echo("      </ul>\n");

  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabel�o
  echo("</table>\n");

  include("../tela2.php");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>