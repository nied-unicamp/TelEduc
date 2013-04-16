<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/ajuda/index.php

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
  ARQUIVO : administracao/ajuda/index.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include("ajuda.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");
  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("  }\n");
  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  Desconectar($sock);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 171 - Cadastro de texto da Ajuda */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,171)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("<!-- Tabelao -->\n");
  echo("<form name=\"alterar\" action=\"seleciona_pagina.php\" method=\"post\">\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  echo("<td><ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("<li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='../administracao/index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("</ul></td></tr>\n");
  echo("<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");


  /* 72 - Selecione a lingua para cadastrar os textos: */
  echo("<tr class=\"head\"><td>".RetornaFraseDaLista($lista_frases,72)."</td>\n");

  /* 74 - Selecione a ferramenta: */
  echo("<td>".RetornaFraseDaLista($lista_frases,74)."</td></tr>\n");

  $sock=Conectar("");
  $lista=ListaLinguas($sock);
  Desconectar($sock);

  if (count($lista)>0)
  {
    echo("<tr><td><select class=\"input\" name=\"cod_lingua\">\n");

    foreach($lista as $cod => $lingua)
    {
      echo(" <option value=".$cod." ".(($cod_lingua == $cod) ? "selected" : "").">".$lingua);
      echo("</option>\n");
    }

    echo("</select>\n");
  }
  else
    /* COLOQUEI */
    /* 524 - Nenhuma lingua cadastrada */
    echo(RetornaFraseDaLista($lista_frases,524)."</td></tr>");

  echo("<td><select class=\"input\" name=\"cod_ferramenta\">\n");

  $lista_ferramentas=RetornaFerramentasOrdemMenu();

  if (count($lista_ferramentas)>0)
    foreach($lista_ferramentas as $cod => $nome)
      echo("  <option value=".$cod." ".(($cod_ferramenta == $cod) ? "selected" : "").">".$nome."</option>\n");

  /* 140 - Configurar */
  echo("  <option value=-1 ".(($cod_ferramenta == -1) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,140)."</option>\n");
  /* 80 - Administra��o Interna (Curso) */
  echo("  <option value=0 ".(($cod_ferramenta == 0) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,80)."</option>\n");

  echo("</select></td></tr>\n");

  echo("<tr>\n");
  echo("<td width=\"25%\"><input type=\"radio\" name=\"modo\" value=\"E\" ".(((!isset($modo)) || ($modo == 'E')) ?  "checked" : "")." />".RetornaFraseDaLista($lista_frases_geral,9)."</td>\n");
  echo("<td width=\"25%\"><input type=\"radio\" name=\"tipo_usuario\" value=\"A\" ".(((!isset($tipo_usuario)) || ($tipo_usuario == 'A')) ?  "checked" : "")." />".RetornaFraseDaLista($lista_frases,523)."</td></tr>\n");
  echo("<tr><td width=25%><input type=\"radio\" name=\"modo\" value=\"V\" ".(($modo == 'V') ?  "checked" : "")." />".RetornaFraseDaLista($lista_frases,179)."</td>\n");
  echo("<td width=\"25%\"><input type=\"radio\" name=\"tipo_usuario\" value=\"F\" ".(($tipo_usuario == 'F') ?  "checked" : "")." />".RetornaFraseDaLista($lista_frases,522)."</td></tr>\n");
  echo("</table>\n");

  echo("<div align=\"right\">\n");
  /* 55 - Continuar */
  echo("<input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,55)."\" onClick=\"document.alterar.submit();\" type=\"button\" />\n");
  echo("</div>\n");


  echo("</td></tr></table>\n");
  echo("</form>\n");
  echo("</td></tr>\n");
  include("../rodape_tela_inicial.php");
  echo("</body>\n");
  echo("</html>\n");
?>
