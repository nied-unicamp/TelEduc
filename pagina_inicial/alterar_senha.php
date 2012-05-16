<?php 
/* 
<!--  
-------------------------------------------------------------------------------

    Arquivo :  pagina_inicial/alterar_senha.php

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
  ARQUIVO : pagina_inicial/alterar_senha.php
  ========================================================== */
    
  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->registerFunction("AtualizaSenhaUsuarioDinamic");

  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();
  
  $pag_atual = "alterar_senha.php";
  include("../topo_tela_inicial.php");

  /* Caso o usuário naum esteja logado, direciona para páigna de login */
  if (empty($_SESSION['login_usuario_s']))
  {
    /* Obt� a raiz_www */
    //$sock = Conectar("");
    $query = "select diretorio from Diretorio where item = 'raiz_www'";
    $res = Enviar($sock,$query);
    $linha = RetornaLinha($res);
    $raiz_www = $linha[0];

    $caminho = $raiz_www."/pagina_inicial/autenticacao";

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
  echo("        document.form_senhas.senha_antiga.focus();\n");
  echo("        document.form_senhas.senha_antiga.value = \"\";\n");
  echo("        startList();\n");
  echo("      }\n\n");
  
  echo("      function ValidaSenhas() \n");
  echo("      {\n");
  echo("        senha1=document.form_senhas.senha_antiga.value;\n");  
  echo("        senha=document.form_senhas.nova_senha.value;\n");
  echo("        senha2=document.form_senhas.nova_senha2.value;\n");
  echo("        if (senha1=='')\n");
  echo("        {\n");
  // 4 - A nova senha n�o pode ser vazia. Por favor digite a nova senha. ARRUMAR
  echo("          alert('".RetornaFraseDaLista($lista_frases_configurar,4)."');\n");
  echo("          document.form_senhas.senha_antiga.focus();\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        if (senha=='')\n");
  echo("        {\n");
  // 4 - A nova senha n�o pode ser vazia. Por favor digite a nova senha.
  echo("          alert('".RetornaFraseDaLista($lista_frases_configurar,4)."');\n");
  echo("          document.form_senhas.nova_senha.focus();\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        if (senha!=senha2) \n");
  echo("        {\n");
  // 5 - As novas senhas digitadas diferem entre si. Por favor redigite-as. 
  echo("          alert('".RetornaFraseDaLista($lista_frases_configurar,5)."');\n");
  echo("          document.form_senhas.nova_senha.value='';\n");
  echo("          document.form_senhas.nova_senha2.value='';\n");
  echo("          document.form_senhas.nova_senha.focus();\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        else \n");
  echo("          return(true);\n");
  echo("      }\n");

  echo("      function confereSenha()\n");
  echo("      {\n");
  echo("        if(ValidaSenhas())\n");
  echo("          xajax_AtualizaSenhaUsuarioDinamic(xajax.getFormValues('form_senhas'),'".RetornaFraseDaLista($lista_frases_configurar,10)."','".RetornaFraseDaLista($lista_frases_configurar,17)."');\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function trataEnvio(flag)\n");
  echo("      {\n");
  echo("        if(flag == ''){\n");
  echo("          document.getElementById('senha_antiga').value='';\n");
  echo("          document.getElementById('nova_senha').value='';\n");
  echo("          document.getElementById('nova_senha2').value='';\n");
  echo("          document.getElementById('senha_antiga').focus();\n");
  echo("        }\n");
  echo("        else\n");
  echo("        {\n");
  echo("          document.getElementById('senha_antiga').value='';\n");
  echo("          document.getElementById('nova_senha').value='';\n");
  echo("          document.getElementById('nova_senha2').value='';\n");
  echo("          document.getElementById('senha_antiga').focus();\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("    </script>\n");

  $objAjax->printJavascript("../xajax_0.2.4/");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
 
  /*1 - Configurar  2 - Alterar Senha  */
  echo("          <h4>".RetornaFraseDaLista($lista_frases_configurar,1)." - ".RetornaFraseDaLista($lista_frases_configurar,2)."</h4>\n");

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
  echo("                <form name=\"form_senhas\" id=\"form_senhas\" action=\"\" method=\"post\" onsubmit=\"return(confereSenha());\">\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                    <tr class=\"head\">\n");
  /* 69 - Senha Antiga*/
  echo("                      <td width=\"43%\">".RetornaFraseDaLista($lista_frases_configurar,69)."</td>\n");
  /* 70 - Nova Senha*/
  echo("                      <td width=\"43%\">".RetornaFraseDaLista($lista_frases_configurar,70)."</td>\n"); 
  echo("                      <td width=\"14%\">&nbsp;</td>\n");
  echo("                    </tr>\n");
  echo("                    <tr>\n");
  echo("                        <td>\n");
  /* 6 - Digite sua senha antiga: */ 
  echo("                          ".RetornaFraseDaLista($lista_frases_configurar,6)."\n");
  echo("                          <input class=\"input\" type=\"password\" name=\"senha_antiga\" id=\"senha_antiga\" size=\"16\" maxlength=\"16\" onLoad />\n");
  echo("                        </td>\n");
  echo("                        <td>\n");
  echo("                          <table>\n");
  echo("                            <tr>\n");
  /* 7 - Digite sua nova senha: */ 
  echo("                              <td style=\"text-align:right;border:none;\">".RetornaFraseDaLista($lista_frases_configurar,7)."</td>\n");
  echo("                              <td style=\"text-align:left;border:none;\"><input class=\"input\" type=\"password\" name=\"nova_senha\" id=\"nova_senha\" size=\"16\" maxlength=\"16\" /></td>\n");
  echo("                            </tr>\n");
  echo("                            <tr>\n");
  // 8 - Redigite sua nova senha: 
  echo("                              <td style=\"text-align:right;border:none;\">".RetornaFraseDaLista($lista_frases_configurar,8)."</td>\n");
  echo("                              <td style=\"text-align:left;border:none;\"><input class=\"input\" type=\"password\" name=\"nova_senha2\" id=\"nova_senha2\" size=\"16\" maxlength=\"16\" /></td>\n");
  echo("                            </tr>\n");
  echo("                          </table>\n");
  echo("                        </td>\n");
  echo("                        <td align=\"center\" >\n");
  echo("                          <ul>\n");
  /* 2 - Alterar Senha  */  
  echo("                            <li><input type=\"submit\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_configurar,2)."\" id=\"registar_alts\" /></li>\n");
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