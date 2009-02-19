<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/desmarcar_sessoes2.php

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
  ARQUIVO : cursos/aplic/batepapo/desmarcar_sessoes2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");
  include("avaliacoes_batepapo.inc");

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  /* topo_tela.php faz isso
  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10); */

  echo("<script type=\"text/javascript\" language=JavaScript>\n\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  } \n");

  echo("  function OpenWindow() \n");
  echo("  {\n");
  echo("    window.open(\"entrar_sala.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\",\"Batepapo\",\"width=1000,height=700,top=50,left=50,scrollbars=no,status=yes,toolbar=no,menubar=no,resizable=no\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("</script>\n");

  $e_formador=EFormador($sock,$cod_curso,$cod_usuario);

  /* Verifica se alguma sessão foi marcada. Se nenhuma foi, apresenta opção de volta a tela anterior */
  if (count($cod_assunto_desmarcar)!=0)
  {
    $where_clause=implode(" or cod_assunto=",$cod_assunto_desmarcar);
    $query="delete from Batepapo_assuntos where cod_assunto=".$where_clause;
    Enviar($sock,$query);

    ExcluiAvaliacaoDesmarcada($sock,$cod_assunto_desmarcar,$cod_usuario);

    header("Location:desmarcar_sessoes.php?cod_curso=".$cod_curso);
    Desconectar($sock);
  }
  else
  {
    include("../menu_principal.php");

    echo("<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

    /* 1 - Bate-Papo */
    echo("<h4>".RetornaFraseDaLista($lista_frases,1));
    /* 63 - Desmarcar sessï¿½es */
    echo(" - ".RetornaFraseDaLista($lista_frases,63)."</h4>");

    echo("<span class=\"btsNav\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a></span><br/>\n");

    echo("<div id=\"mudarFonte\">\n");
    echo("	<a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
    echo("	<a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
    echo("	<a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
    echo("</div>\n");

    /* <!----------------- Tabelao -----------------> */
    echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("  <tr>\n");
    echo("    <td valign=\"top\">\n");

    echo("      <ul class=\"btAuxTabs\">\n");
    /* 27 - Ver sessï¿½es realizadas */
    echo("        <li><span title=\"Ver sessï¿½es realizadas\" onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 27)."</span></li>\n");
    if ($e_formador)
    {
      /* 47 - Marcar sessï¿½o */
      echo("        <li><span title=\"Marcar sessï¿½o\" onClick=\"document.location='marcar_sessao.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 47)."</span></li>\n");
      /* 63 - Desmarcar sessï¿½es */
      echo("        <li><span title=\"Desmarcar sessï¿½es\" onClick=\"document.location='desmarcar_sessoes.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 63)."</span></li>\n");

      /* 78 - Lixeira */
      echo("        <li><span title=\"Lixeira\" onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;lixeira=sim';\">".RetornaFraseDaLista($lista_frases, 78)."</span></li>\n");
    }
    /* 55 - Próxima sessão marcada */
    echo("        <li><span title=\"Próxima sessão marcada\" onClick=\"document.location='ver_sessoes_marcadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 55)."</span></li>\n");

    echo("      </ul>\n");

    echo("    </td>\n");
    echo("  </tr>\n");

    echo("  <tr>\n");
    echo("    <td valign=\"top\">\n");
    echo("      <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("        <tr>\n");
    /* 65 - Nenhuma sessão foi selecionada. Volte e selecione as sessões a serem desmarcadas. */
    echo("          <td>".RetornaFraseDaLista($lista_frases,65)."</td>\n");
    echo("        </tr>\n");
    // Fim Tabela Interna
    echo("      </table>\n");

    echo("      <ul class=\"btAuxTabs03\">\n");
    /* 2 - Entrar na sala de bate-papo */
    echo("        <li><span title=\"Entrar na sala de bate-papo\" onClick=\"return(OpenWindow());\">".RetornaFraseDaLista($lista_frases, 2)."</span></li>\n");
    echo("      </ul>\n");

  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabelão
  echo("</table>\n");

  include("../tela2.php");

  }

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>