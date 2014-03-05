<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/alterar_senha.php

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
  ARQUIVO : administracao/alterar_senha.php
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
  $objAjax->register(XAJAX_FUNCTION,"AtualizaSenhaAdmDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  /* Inicio do JavaScript */
  echo("    <script language=\"javascript\" type=\"text/javascript\">\n\n");

  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("      }\n");

  echo("      function Confirma()\n");
  echo("      {\n");
  echo("        var senha_antiga = document.frmSenha.senha_antiga.value;\n");
  echo("        var senha_nova = document.frmSenha.senha.value;\n");
  echo("        var senha_nova2 = document.frmSenha.senha_nova.value;\n");
  echo("        if(senha_antiga == '' || senha_nova == '' || senha_nova2 == '')\n");
  echo("        {\n");
  /* 17 - Alguma campo n�o foi preenchido! */
  /* 18 - Volte e digite-as novamente. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,17).RetornaFraseDaLista($lista_frases_adm,18)."');\n");
  echo("        }\n");
  echo("        else if(senha_nova != senha_nova2)\n");
  echo("        {\n");
  /* 19 - Senha nova n�o confere! */
  /* 18 - Volte e digite-as novamente. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,19).RetornaFraseDaLista($lista_frases_adm,18)."');\n");
  echo("          document.frmSenha.senha.value = '';\n");
  echo("          document.frmSenha.senha_nova.value = '';\n");
  echo("          document.frmSenha.senha.focus();\n");
  echo("        }\n");
  echo("        else\n");
  echo("        {\n");
  echo("          xajax_AtualizaSenhaAdmDinamic(senha_antiga,senha_nova);\n");
  echo("        }\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function RespostaUsuario(sucesso)\n");
  echo("      {\n");
  /* 21 - Senha alterada corretamente*/
  /* 22 - Ao voltar a administra��o, ser� requisitado que digite a senha nova. */
  echo("        if(sucesso)\n");
  echo("        {\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,21).RetornaFraseDaLista($lista_frases_adm,22)."');\n");
  echo("          document.frmSenha.senha_antiga.value = '';\n");
  echo("          document.frmSenha.senha.value = '';\n");
  echo("          document.frmSenha.senha_nova.value = '';\n");
  echo("        }\n");
  /* 20 - Senha antiga n�o confere! */
  echo("        else\n");
  echo("        {\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,20)."');\n");
  echo("          document.frmSenha.senha_antiga.value = '';\n");
  echo("          document.frmSenha.senha_antiga.focus();\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  Desconectar($sock);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 10 - Alteração de Senha da Administração */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,10)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    
  echo("          <form name=\"frmSenha\" action=\"alterar_senha2.php\" method=\"post\" onsubmit=\"return(Confirma());\">\n");
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
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
  /* 14 - Digite a senha antiga */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases,14)."</td>\n");
  /* 15 - Digite a nova senha */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,15)."</td>\n");
  /* 16 - Digite a nova senha novamente */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,16)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td><input class=\"input\" type=\"password\" name=\"senha_antiga\" maxlength=\"20\" /></td>\n");
  echo("                    <td><input class=\"input\" type=\"password\" name=\"senha\" maxlength=\"20\" /></td>\n");
  echo("                    <td><input class=\"input\" type=\"password\" name=\"senha_nova\" maxlength=\"20\" /></td>\n");
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