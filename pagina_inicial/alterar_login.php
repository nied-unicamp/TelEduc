<?php
/* 
<!--  
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/alterar_login.php

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
  ARQUIVO : pagina_inicial/alterar_login.php
  ========================================================== */

  
  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  require_once("../cursos/aplic/xajax_0.5/xajax_core/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../cursos/aplic/xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"AtualizaLoginUsuarioDinamic");

  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_usuario = $_SESSION['cod_usuario_global_s'];
  $pag_atual = "alterar_login.php";
  include("../topo_tela_inicial.php");

  /* Caso o usuário naum esteja logado, direciona para páigna de login */
  if (empty($_SESSION['login_usuario_s']))
  {
    /* Obt� a raiz_www */
	$raiz_www = RetornaRaizWWW($sock);

    $caminho = $raiz_www."/pagina_inicial";

    header("Location: {$caminho}/autenticacao_cadastro.php");
    Desconectar($sock);
    exit;
  }

  /*
  ==================
  Fun��es JavaScript
  ==================
  */
  
  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");
  
  echo("      function ValidaLogins() \n");
  echo("      {\n");
  echo("        js_novo_login=document.form_logins.novo_login.value;\n");
  echo("        if ((js_novo_login==''))\n");
  echo("        {\n");
  // 59 - O campo de login n�o podem ser vazio. Por favor digite novamente o login desejado.
  echo("          alert('".RetornaFraseDaLista($lista_frases_configurar, 59)."');\n");
  echo("          document.form_logins.novo_login.focus();\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n");

  echo("      function confereLogin()\n");
  echo("      {\n");
  echo("        if(ValidaLogins())\n");
  ////60 - Seu login foi alterado com sucesso! * 61 - O login escolhido ja existe.Por favor, escolha um novo login.
  echo("          xajax_AtualizaLoginUsuarioDinamic(xajax.getFormValues('form_logins'),'".RetornaFraseDaLista($lista_frases_configurar,60)."','".RetornaFraseDaLista($lista_frases_configurar,62)."');\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function trataEnvio(flag,novoLogin)\n");
  echo("      {\n");
  echo("        if(flag == ''){\n");
  echo("          document.getElementById('novo_login').value='';\n");
  echo("          document.getElementById('novo_login').focus();\n");
  echo("        }else\n");
  echo("        {\n");
  echo("          tdElement = document.getElementById('td_login');\n");
  echo("          tdElement.innerHTML = '<b>'+novoLogin+'<\/b>';\n");
  echo("          document.getElementById('novo_login').value='';\n");
  echo("          document.getElementById('novo_login').focus();\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("    </script>\n");
  
  $objAjax->printJavascript();

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* 1 - Configurar*/ /*  65 - Alterar Login*/
  echo("          <h4>".RetornaFraseDaLista($lista_frases_configurar,1)." - ".RetornaFraseDaLista($lista_frases_configurar,65)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 27 - Alterar dados pessoais*/
  echo("                  <li><a href=\"dados.php\" id=\"alterar_dados\">".RetornaFraseDaLista($lista_frases_configurar, 27)."</a></li>\n");
  /* 65 - Alterar Login */
  echo("                  <li><a href=\"alterar_login.php\" id=\"alterar_login\">".RetornaFraseDaLista($lista_frases_configurar, 65)."</a></li>\n");
  /* 2 - Alterar Senha */
  echo("                  <li><a href=\"alterar_senha.php\" id=\"alterar_senha\">".RetornaFraseDaLista($lista_frases_configurar, 2)."</a></li>\n");
  /* 3 - Alterar Idioma */
  echo("                  <li><a href=\"selecionar_lingua.php\" id=\"selecionar_idioma\">".RetornaFraseDaLista($lista_frases_configurar, 3)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");  
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <form name=\"form_logins\" id=\"form_logins\" action=\"\" method=\"post\" onsubmit=\"return(confereLogin());\">\n");
  echo("                  <input type=\"hidden\" name=\"alterar_login\" value=\"1\" />\n");
  echo("                  <table cellspacing=\"0\" class=\"tabInterna\" id=\"tabelaMsgs\">\n");
  echo("                    <tr class=\"head\">\n");

  /* 67 - Login Atual */
  echo("                      <td width=\"43%\">".RetornaFraseDaLista($lista_frases_configurar,67)."</td>\n");

  /* 68 - Novo Login */
  echo("                      <td width=\"43%\">".RetornaFraseDaLista($lista_frases_configurar,68)."</td>\n");  
  
  echo("                      <td width=\"14%\">&nbsp;</td>\n"); 
  
  echo("                    </tr>\n");
  
  echo("                    <tr class=\"tabInterna\">\n");
  echo("                        <td id=\"td_login\">\n");
  echo("                          <b>".RetornaLoginUsuario($sock)."</b>\n");
  echo("                        </td>\n"); 
  echo("                        <td>\n");
  /* 58 - Digite seu novo login: */
  echo("                          ".RetornaFraseDaLista($lista_frases_configurar,58)."&nbsp;\n");
  echo("                          <input class=\"input\" type=\"text\" id=\"novo_login\" name=\"novo_login\" size=\"16\" maxlength=\"50\" />\n");
  echo("                        </td>\n");
  echo("                        <td align=\"center\">\n");
  echo("                          <ul>\n");
  /* 65 - Alterar Login  */ 
  echo("                            <li><input type=\"submit\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_configurar,65)."\" id=\"registar_alt\" /></li>\n");
  echo("                          </ul>\n");
  echo("                        </td>\n");
  echo("                    </tr>\n");
  echo("                  </table>\n");
  echo("                </form>\n"); 
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
