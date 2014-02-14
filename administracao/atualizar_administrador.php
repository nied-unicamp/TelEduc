<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/atualizar_administrador.php

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
  ARQUIVO : administracao/atualizar_administrador.php
  ========================================================== */

  /* Entradas:
        * configurar.php: PHPSESSION

      Saidas:
        * configurar.php: PHPSESSID

        * atualizar_administrador2.php: PHPSESSID
                                        adm_nome
                                        adm_email
  */


  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->registerFunction("AtualizaDadosAdmDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  /* Inicio do JavaScript */
  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function VerificaCampos()\n");
  echo("      {\n");
  echo("        var nome = document.frmAdm.adm_nome.value;\n");
  echo("        var email = document.frmAdm.adm_email.value;\n");
  echo("        while (email.search(\" \") != -1)\n");
  echo("          email = email.replace(/ /, \"\");\n\n");
  echo("        if ((email == '') || (nome == ''))\n");
  echo("        {\n");
  /* 17 - Algum campo não foi preenchido! */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm, 17)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        while (nome.search(\" \") != -1)\n");
  echo("          nome = nome.replace(/ /, \"\");\n\n");
  echo("        if ((email.indexOf('@')< 0) || (email.indexOf('.')< 0))\n");
  echo("        {\n");
  /* 168 - E-mail inv�lido. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm, 168)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        else\n");
  echo("        {\n");
  echo("          nome = document.frmAdm.adm_nome.value;\n");
  echo("          email = document.frmAdm.adm_email.value;\n");
  echo("          xajax_AtualizaDadosAdmDinamic(nome,email);\n");
  /* 192 - Dados do administrador alterados com sucesso. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm, 192)."');\n");
  echo("        }\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function Iniciar() {\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("    </script>\n");

  $objAjax->printJavascript("../xajax_0.2.4/");

  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  Desconectar($sock);


  $adm_nome = RetornaConfig("adm_nome");
  $adm_email = RetornaConfig("adm_email");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 191 - Altera��o de dados do administrador */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,191)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <form name=\"frmAdm\" action=\"atualizar_administrador2.php\" method=\"post\" onSubmit=\"return(VerificaCampos());\">\n");
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='selecionar_lingua.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  /* 191 - Altera��o de dados do administrador */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,191)."\" onClick=\"document.location='atualizar_administrador.php'\">".RetornaFraseDaLista($lista_frases,191)."</span></li>\n");
  /* 10 - Altera��o de Senha da Administra��o */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,10)."\" onClick=\"document.location='alterar_senha.php'\">".RetornaFraseDaLista($lista_frases,10)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  /* 23 - Nome: */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases,23)."</td>\n");
  /* 33 - E-mail: */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,33)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"adm_nome\" size=\"50\" value='".ConverteAspas2Html($adm_nome)."' /></td>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"adm_email\" size=\"50\" value='".ConverteAspas2Html($adm_email)."' /></td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("                <div align=\"right\">\n");
  /* 24 - Alterar (ger) */
  echo("                  <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,24)."\" type=\"submit\" />\n");
  echo("                </div>\n");
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