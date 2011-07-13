<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/esqueci_senha.php

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
  ARQUIVO :pagina_inicial/esqueci_senha.php
  ========================================================== */
  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->registerFunction("EnviarSenhaLoginUsuarioDinamic");

  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  include("../topo_tela_inicial.php");

  /* Obt�m as mensagens da ferramenta Administra��o. */
  $lista_frases_mensagem=RetornaListaDeFrases($sock, 0);
  $lista_frases_esqueci=RetornaListaDeFrases($sock,-2);
  /* Obt�m a raiz_www */
  $query = "select diretorio from Diretorio where item = 'raiz_www'";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  $raiz_www = $linha[0];

  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

   echo("      function EmailValido()\n");
  echo("      {\n");
  echo("        log = document.frmSenha.email.value;\n");
  echo("        while (log.search(\" \") != -1)\n");
  echo("        {\n");
  echo("          log = log.replace(/ /, \"\");\n");
  echo("        }\n");
  echo("        if (log == '')\n");
  echo("        {\n");
  /* 69 - O campo de e-mail n�o pode estar em branco! */
  echo("          alert('".RetornaFraseDaLista($lista_frases_esqueci, 69)."');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        else\n");
  echo("          return(true);\n");
  echo("        }\n\n");
  

  echo("      function confere()\n");
  echo("      {\n");
  echo("        if(EmailValido())\n");
  echo("          xajax_EnviarSenhaLoginUsuarioDinamic(document.frmSenha.email.value,2);");
  echo("        return false;");
  echo("      }\n\n");

  echo("      function trataEnvio(flag)\n");
  echo("      {\n");
  echo("        if(flag == '1')\n");
  echo("        {\n");
  /* 28 - Login inv�lido! */
  echo("          alert('".RetornaFraseDaLista($lista_frases_esqueci, 72)."');\n");
  echo("          document.frmSenha.email.value='';\n");
  echo("          document.frmSenha.email.focus();\n");
  echo("        }\n");
  echo("        else");
  echo("        {\n");
  /* 228 -Login e nova senha enviados por e-mail. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_mensagem,228)."');\n");
  echo("          document.location='autenticacao_cadastro.php';\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  $objAjax->printJavascript("../xajax_0.2.4/");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 23 - Pagina Inicial */
  echo("          <h4>".RetornaFraseDaLista($lista_frases_esqueci,23)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr>   \n");
  /* 25 - Se voc� esqueceu sua senha ou sua senha n�o est� funcionando por alguma raz�o, */
  /*      voc� pode obter uma nova. Voc� precisa apenas do seu login, que voc� cadastrou */
  /*      para o acesso. Preencha o campo abaixo e selecione o bot�o Enviar. Uma nova    */
  /*      senha ser� enviada para seu e-mail cadastrado.                                 */
  echo("                    <td align=left>\n");
  echo("                      <p style=\"text-indent:15px\">".RetornaFraseDaLista($lista_frases_esqueci,25)."</p>\n");
  echo("                      <br />\n");
  echo("                      <form name=frmSenha method=post action=\"\" onSubmit=\"return(confere());\">\n");
  echo("                      <div align=\"center\"><table>\n");
  echo("                        <tr>\n");
  echo("                          <td style=\"border:none; text-align:right;\">\n");
  /* 27 - login: */
  echo("                          ".RetornaFraseDaLista($lista_frases_esqueci,71)."\n");
  echo("                          </td>\n");
  echo("                          <td style=\"border:none\">\n");
  echo("                            <input class=\"input\" type=\"text\" name=\"email\" />\n");
  echo("                          </td>\n");
  echo("                        </tr>\n");
  echo("                        <tr>\n");
  echo("                          <td style=\"border:none; text-align:right;\">\n");
  echo("                          </td>\n");
  echo("                          <td style=\"border:none\">\n");
  echo("                            <br /><input class=\"input\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral, 11)."\" />\n");
  echo("                          </td>\n");
  echo("                        </tr>\n");
  echo("                      </table></div>\n");
  echo("                      </form>");
  echo("                    </td>\n");
  echo("                  </tr>   \n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>