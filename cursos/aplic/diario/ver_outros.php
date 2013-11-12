<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/diario/ver_outros.php

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
  ARQUIVO : cursos/aplic/diario/ver_outros.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("diario.inc");

  $cod_ferramenta=14;
  $cod_ferramenta_ajuda=$cod_ferramenta;
  $cod_pagina_ajuda=6;
  include("../topo_tela.php");

  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  /*
 ==================
 Funcoes JavaScript
 ==================
 */
  echo("        <script type=\"text/javascript\">\n");

  echo("          function Iniciar()\n");
  echo("          {\n");
  echo("            startList();\n");
  echo("          }\n");
  
  echo("          function Atualizar()\n");
  echo("          {\n");
  echo("            document.location='ver_outros.php?&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."';\n");
  echo("          }\n\n");

  echo("          function OpenWindowPerfil(id)\n");
  echo("          {\n");
  echo("            window.open(\"../perfil/exibir_perfis.php?");
  echo("&cod_curso=".$cod_curso."&cod_aluno[]=\" + id, \"PerfilDisplay\",\"width=600,height=400,");
  echo("top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("            return(false);\n");
  echo("          }\n\n");

  echo("        </script>\n\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario);

  /* 1 - Diï¿½rio de Bordo */
  echo("          <h4>".RetornaFraseDaLista($lista_frases, 1));
  /* 31 - Diï¿½rios dos participantes do curso */
  echo(" - ".RetornaFraseDaLista($lista_frases, 31)."</h4>\n");
  
   /* 509 - Voltar */
  echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("            <a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("            <a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("          </div>\n");

  //<!----------------- Tabelao ----------------->
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 3 - Atualizar */
  echo("                  <li><span title=\"Atualizar\" onclick=\"window.location.reload();\">".RetornaFraseDaLista($lista_frases, 3)."</span></li>\n");
  /* 32 - Diario Pessoal */
  echo("                  <li><a href=\"diario.php?&amp;cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$cod_item."&amp;cod_propriet=".$cod_usuario."&amp;origem=diario\">".RetornaFraseDaLista($lista_frases, 32)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  $lista_diarios = RetornaDiarios ($sock, $cod_curso, $cod_usuario);

  echo("            <tr>\n");
  //<!----------------- Tabela Interna ----------------->
  echo("              <td valign=\"top\">\n");
  echo("                <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td width=\"45%\" align=\"center\">Di&aacute;rios</td>\n");
  /* 10 - Data */
  echo("                    <td width=\"20%\" align=\"center\">".RetornaFraseDaLista($lista_frases, 10)."</td>\n");
  /* 11 - Itens */
  echo("                    <td width=\"15%\" align=\"center\">".RetornaFraseDaLista($lista_frases, 11)."</td>\n");
  /* 6 - Itens nao comentados */
  echo("                    <td width=\"20%\" align=\"center\">".RetornaFraseDaLista($lista_frases, 6)."</td>\n");
  echo("                  </tr>\n");

  foreach ($lista_diarios as $cod_usr => $linha_diario)
  {
    if ($linha_diario ['novidade'])
    {
      $negrito_abre = "<b>";
      $negrito_fecha= "</b>";
    }
    else
    {
      $negrito_abre = $negrito_fecha = "";
    }

    echo("                  <tr>\n");

    echo("                    <td align=\"left\">\n");
    echo("                      <a href=\"diario.php?&amp;cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$cod_item."&amp;cod_propriet=".$cod_usr."&amp;origem=diario\"><img src=\"../imgs/arquivo_g_p.gif\" border=\"0\" /></a>\n");
    /* 56 - Diário de */
    echo("                      <a href=\"diario.php?&amp;cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$cod_item."&amp;cod_propriet=".$cod_usr."&amp;origem=diario\">".$negrito_abre.RetornaFraseDaLista($lista_frases, 56)." ".$linha_diario['nome'].$negrito_fecha."</a>\n");
    echo("                    </td>\n");
    echo("                    <td align=\"center\" class=\"g1field\">".$negrito_abre.UnixTime2DataHora($linha_diario['data']).$negrito_fecha."</td>\n");
    echo("                    <td align=\"center\" class=\"g1field\">".$negrito_abre.$linha_diario['num_itens'].$negrito_fecha."</td>\n");
    echo("                    <td align=\"center\" class=\"g1field\">".$negrito_abre.$linha_diario['num_itens_nao_comentados'].$negrito_fecha."</td>\n");
    echo("                  </tr>\n");
  }

  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");

  echo("          <br />\n");
  /* 509 - voltar, 510 - topo */
  echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
