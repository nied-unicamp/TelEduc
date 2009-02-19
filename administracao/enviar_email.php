<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/enviar_email.php

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
  ARQUIVO : administracao/enviar_email.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  /* Inicio do JavaScript */
  echo("    <script type=\"text/javascript\" src=\"../cursos/aplic/bibliotecas/rte/html2xhtml.js\"></script>\n");
  echo("    <script type=\"text/javascript\" src=\"../cursos/aplic/bibliotecas/rte/richtext.js\"></script>\n");
  echo("    <script type=\"text/javascript\">\n");
  echo("      initRTE(\"../cursos/aplic/bibliotecas/rte/images/\", \"../cursos/aplic/bibliotecas/rte/\", \"../cursos/aplic/bibliotecas/rte/\", true);\n");
  echo("    </script>\n");
  echo("    <script type=\"text/javascript\">\n");

  echo("      function verificar()\n");
  echo("      {\n");
  echo("        coordenadores = document.email.coordenadores.checked;\n");
  echo("        formadores = document.email.formadores.checked;\n");
  echo("        alunos = document.email.alunos.checked;\n");
  echo("        assunto = document.email.assunto.value;\n");
  echo("        updateRTE('mensagem');\n");
  echo("        mensagem = document.email.mensagem.value;\n");

  echo("        if (coordenadores == false && formadores == false && alunos == false)\n");
  echo("        {\n");

  /* 49 - Escolha pelo menos um tipo de destinat�rio para o e-mail. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,49)."');\n");
  echo("          document.email.coordenadores.focus();\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        if (assunto == '')\n");
  echo("        {\n");
 
  /* 50 - O campo assunto n�o pode ser vazio. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,50)."');\n");
  echo("          document.email.assunto.focus();\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        if (mensagem == '')\n");
  echo("        {\n");

  /* 51 - O campo mensagem n�o pode ser vazio. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,51)."');\n");
  echo("          document.email.mensagem.focus();\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("      }\n");

  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("      }\n");

  echo("    </script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  Desconectar($sock);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 9 - Enviar e-mail para usu�rios */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,9)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <!-- Tabelao -->\n");
  echo("          <form name=\"email\" action=\"enviar_email2.php\" method=\"post\" onsubmit=\"return(verificar());\">\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr style=\"width: 40%\">\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  /* 42 - Enviar e-mail para: */
  echo("                  <tr>\n");
  echo("                    <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,42)."</b></td>\n");
  echo("                    <td align=\"left\">\n");
  echo("                      <select name=\"cod_curso\" class=\"input\" size=\"1\">\n");
  /* 43 - Todos os cursos */
  echo("                          <option value=Todos>".RetornaFraseDaLista($lista_frases,43)."</option>\n");

  $lista=RetornaListaCursosAtivos();
  if (count($lista)>0)
    foreach ($lista as $cod => $nome)
      echo("                          <option value=".$cod.">".$nome."</option>\n");

  echo("                      </select>\n");

  /* 44 - Coordenadores */
  echo("                      <input class=\"input\" type=\"checkbox\" name=\"coordenadores\" value=\"1\" />".RetornaFraseDaLista($lista_frases,44)."\n");
  /* 45 - Formadores */
  echo("                      <input class=\"input\" type=\"checkbox\" name=\"formadores\" value=\"1\" />".RetornaFraseDaLista($lista_frases,45)."\n");
  /* 46 - Alunos */
  echo("                      <input class=\"input\" type=\"checkbox\" name=\"alunos\" value=\"1\" />".RetornaFraseDaLista($lista_frases,46)."\n");

  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                  <tr style=\"width: 40%\">\n");

  /* 47 - Assunto: */
  echo("                    <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,47)."</b></td>\n");
  echo("                    <td align=\"left\">\n");
  echo("                      <input type=\"text\" name=\"assunto\" class=\"input\" size=\"55\" />\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td align=\"center\" colspan=\"2\">\n");
  echo("                      <script type=\"text/javascript\">\n");
  echo("                        writeRichText('mensagem', '".$mensagem."', 400, 200, true, false);\n");
  echo("                      </script>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td align=\"right\">\n");
  /* 11 - Enviar (Ger) */
  echo("                <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,11)."\" type=\"submit\" />\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>