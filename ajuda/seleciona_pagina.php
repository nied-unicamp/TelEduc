<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/ajuda/seleciona_pagina.php

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
  ARQUIVO : administracao/ajuda/seleciona_pagina.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include("ajuda.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("    <script language=\"javascript\" type=\"text/javascript\">\n");
  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("      }\n");
  echo("    </script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);

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
  
  echo("          <!-- Tabelao -->\n");
  echo("          <form name=\"alterar\" action=\"cadastra_texto.php\" method=\"post\">\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='index.php?cod_ferramenta=".$cod_ferramenta."&amp;cod_lingua=".$cod_lingua."&amp;tipo_usuario=".$tipo_usuario."&amp;modo=".$modo."'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");


  $lista_ferramentas=RetornaFerramentasOrdemMenu();
  $sock = Conectar("");
  $lista_lingua=ListaLinguas($sock);

  /* 82 - L�ngua: */
  echo("                  <tr>\n");
  echo("                    <td align=\"left\">".RetornaFraseDaLista($lista_frases,82)." <b>".$lista_lingua[$cod_lingua]."</b></td>\n");
  echo("                  </tr>\n");
  /* 83 - Ferramenta: */
  echo("                  <tr>\n");
  echo("                    <td align=\"left\">".RetornaFraseDaLista($lista_frases,83)." <b>\n");

  if ($cod_ferramenta<0)
    /* 140 - Configurar */
    echo(RetornaFraseDaLista($lista_frases,140)."</b></td>\n");
  else if ($cod_ferramenta==0)
    /* 80 - Administra��o Interna (curso) */
    echo(RetornaFraseDaLista($lista_frases,80)."</b></td>\n");
  else
    echo($lista_ferramentas[$cod_ferramenta]."</b></td>\n");
  echo("                  </tr>");
  /* 173 - Modo de visualiza��o: */
  echo("                  <tr>\n");
  echo("                    <td align=\"left\">".RetornaFraseDaLista($lista_frases,173)." <b>\n");

  if ($tipo_usuario=="F")
    echo(RetornaFraseDaLista($lista_frases,523)."</b></td>\n");
  else if ($tipo_usuario=="A")
    echo(RetornaFraseDaLista($lista_frases,522)."</b></td>\n");
  echo("                  </tr>");

  $lista_paginas=RetornaPaginasdaFerramenta($sock,$cod_ferramenta,$cod_lingua,$tipo_usuario);

  /* Retorna nomes das p�ginas em portugu�s para mostr�-las na tela, facilitando assim a sele��o da p�gina em outra
     l�ngua qualquer */
  $cod_lingua_padrao=1;
  $lista_paginas_padrao=RetornaPaginasdaFerramenta($sock,$cod_ferramenta,$cod_lingua_padrao,$tipo_usuario);

  if (count($lista_paginas)>0 && $lista_paginas != "")
  {
    /* 176 - Selecione a p�gina: */
    echo("                  <tr class=\"head\">\n");
    echo("                    <td>".RetornaFraseDaLista($lista_frases,176)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td>\n");
    echo("                      <select class=\"input\" name=\"cod_pagina\">\n");

    foreach($lista_paginas as $cod_pagina => $linha)
      if ($cod_lingua==$cod_lingua_padrao)
        echo("                      <option value=".$linha['cod_pagina'].">".$linha['cod_pagina']." - ".$linha['nome_pagina']."</option>\n");
      else
        echo("                      <option value=".$linha['cod_pagina'].">".$linha['cod_pagina']." - ".$lista_paginas_padrao[$cod_pagina]['nome_pagina']." / ".$linha['nome_pagina']."</option>\n");

    echo("                      </select>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }
  else
  {
    /* 175 - Nenhuma p�gina encontrada */
    echo("                  <tr>\n");
    echo("                    <td>".RetornaFraseDaLista($lista_frases,175)."</td>\n");
    echo("                  </tr>\n");
  }

  /* 525 * - Ao escolher essa op��o, voc� estar� criando uma nova p�gina no idioma padrão, ou seja, portugu�s, mesmo que tenha selecionado previamente outro idioma. */
  echo("                  <tr>\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases,525)."</td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("                <div align=right>\n");
  if (count($lista_paginas)>0 && $lista_paginas != "")
  {
    /* 55 - Continuar */
    echo("                  <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,55)."\" onClick=\"document.alterar.submit();\" type=\"button\" />\n");
  }
  if ($modo=='E')
  {
    /* 172 - Criar nova p�gina */
    echo("                  <input class=\"input\" type=\"submit\" value='".RetornaFraseDaLista($lista_frases,172)." *' onclick=\"document.alterar.criar.value='sim'\" />\n");
  }
  echo("                </div>\n");
  echo("                <input type=\"hidden\" name=\"criar\" value='nao' />\n");
  echo("                <input type=\"hidden\" name=\"cod_ferramenta\" value='".$cod_ferramenta."' />\n");
  echo("                <input type=\"hidden\" name=\"cod_lingua\" value='".$cod_lingua."' />\n");
  echo("                <input type=\"hidden\" name=\"tipo_usuario\" value='".$tipo_usuario."' />\n");
  echo("                <input type=\"hidden\" name=\"modo\" value='".$modo."' />\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
