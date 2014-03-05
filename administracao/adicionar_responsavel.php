<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/adicionar_responsavel.php

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
  ARQUIVO : administracao/adicionar_responsavel.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  require_once("../cursos/aplic/xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../cursos/aplic/xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"CadastraResponsavelDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  /* Inicio do JavaScript */
  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("      }\n");

  echo("      function VerificaCampos()\n");
  echo("      {\n");
  echo("        var nome = document.frmResp.nome.value;\n");
  echo("        while (nome.search(\" \") != -1)\n");
  echo("          nome = nome.replace(/ /, \"\");\n\n");
  echo("        var email = document.frmResp.email.value;\n");
  echo("        if ((email.indexOf('@')< 0) || (email.indexOf('.')< 0))\n");
  echo("        {\n");
  /* 168 - E-mail inv�lido. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm, 168)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        while (email.search(\" \") != -1)\n");
  echo("          email = email.replace(/ /, \"\");\n\n");
  echo("        if ((email == '') || (nome == ''))\n");
  echo("        {\n");
  /* 17 - Algum campo n�o foi preenchido! */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm, 17)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        else\n");
  echo("        {\n");
  echo("          nome = document.frmResp.nome.value;\n");
  echo("          email = document.frmResp.email.value;\n");
  echo("          xajax_CadastraResponsavelDinamic(nome,email);\n");
  echo("        }\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function RespostaUsuario(sucesso)\n");
  echo("      {\n");
  /* 192 - Dados do administrador alterados com sucesso. */
  echo("        if(sucesso)\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,192)."');\n");
  /* 193 - Ocorreu um erro na atualiza��o dos dados do administrador. */
  echo("        else\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,193)."');\n");
  echo("      }\n\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  Desconectar($sock);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 7 - Atualizar lista de respons�veis pelo Ambiente */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,7)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
   
  echo("          <form name=\"frmResp\" action=\"adicionar_responsavel2.php\" onsubmit=\"return(VerificaCampos());\" method=\"post\">\n");
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  /* 137 - Selecionar Idioma Padr�o */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,137)."\" onClick=\"document.location='selecionar_lingua.php'\">".RetornaFraseDaLista($lista_frases,137)."</span></li>\n");
  /* 504 - Configurações */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,504)."\" onClick=\"document.location='configurar_solicitacao.php'\">".RetornaFraseDaLista($lista_frases,504)."</span></li>\n");
  /* 505 - Alterações */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,505)."\" onClick=\"document.location='atualizar_administrador.php'\">".RetornaFraseDaLista($lista_frases,505)."</span></li>\n");
  /* 7 - Atualizar lista de respons�veis pelo Ambiente */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,7)."\" onClick=\"document.location='atualizar_responsaveis.php'\">".RetornaFraseDaLista($lista_frases,7)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  /* 32 - Nome do respons�vel: */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases,32)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"nome\" maxlength=\"100\" size=\"60\" /></td>\n");
  echo("                  </tr>\n");
  /* 33 - E-mail: */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases,33)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"email\" size=\"60\" /></td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  /* 34 - Esta pessoa deve fazer parte da lista de pessoas a serem contatadas para autorizar a cria��o de um curso. */
  echo("                    <td><input type=\"checkbox\" name=\"status\" value='C' checked />".RetornaFraseDaLista($lista_frases,34)."</td> \n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("                <div align=\"right\">\n");
  /* 11 - Enviar (Ger) */
  echo("                  <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,11)."\" type=\"submit\" />\n");
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