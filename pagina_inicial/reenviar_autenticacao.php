<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/esqueci_login.php

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
  ARQUIVO :pagina_inicial/esqueci_login.php
  ========================================================== */
  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");
  
  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  
  // Registre os nomes das funções em PHP que você quer chamar através do xajax
  $objAjax->registerFunction("EnviarSenhaLoginUsuarioDinamic");
  
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  include("../topo_tela_inicial.php");

  // Obtém as mensagens da ferramenta Administração.
  $pag_atual = "reenviar_autenticacao.php";
  $lista_frases_mensagem			= RetornaListaDeFrases($sock, 0);
  $lista_frases_autenticacao	=	RetornaListaDeFrases($sock,-2);

  $query 		= "select diretorio from Diretorio where item = 'raiz_www'";
  $res 			= Enviar($sock,$query);
  $linha 		= RetornaLinha($res);
  $raiz_www = $linha[0];

  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function EntradaValida()\n");
  echo("      {\n");
  echo("        log = document.frmLogin.input.value;\n");
  echo("        while (log.search(' ') != -1)\n");
  echo("        {\n");
  echo("          log = log.replace(/ /, '');\n");
  echo("        }\n");
  echo("        if (log == '')\n");				  /* 69 - O campo de e-mail não pode estar em branco! */
  echo("        {\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases_autenticacao, 69)."');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        else\n");
  echo("          return(true);\n");
  echo("        }\n\n");

  echo("			function EmailouLogin()");
  echo("      {\n");
  echo("				input = document.frmLogin.input.value;\n");
  echo("				if (input.search('@') == -1)\n");
	echo("					return 1;\n");			/* 1 é a opção para login */
	echo("				return 2;\n");				/* 2 para email */
  echo("      }\n");
  
  echo("      function confere()\n");
  echo("      {\n");
  echo("        if(EntradaValida())\n");
  echo("				{\n");
  echo("					opcao = EmailouLogin();\n");
  echo("          xajax_EnviarSenhaLoginUsuarioDinamic(document.frmLogin.input.value,opcao);\n");
  echo("				}\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function trataEnvio(flag)\n");
  echo("      {\n");
  echo("        if(flag == '1')\n");			  /* 72 - Email inválido! */
  echo("        {\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases_autenticacao, 72)."');\n");
  echo("          document.frmLogin.input.value='';\n");
  echo("          document.frmLogin.input.focus();\n");
  echo("        }\n");
  echo("        else");
  echo("        {\n");		/*  228 - Login e nova senha enviados por e-mail. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_mensagem,228)."');\n");
  echo("          document.location='autenticacao.php';\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  $objAjax->printJavascript("../xajax_0.2.4/");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  // 93 - Confirmar meu cadastro
  echo("          <h4>".RetornaFraseDaLista($lista_frases_autenticacao,93)."</h4>\n");

	// 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  // 94 - Se você não confirmou seu cadastro através de seu email ou está tendo
  // problemas com a confirmação, siga os passos abaixo para confirmar seu cadastro.
  // 95 - Você precisará do seu login ou do email que você cadastrou para o acesso.  
  // 96 - Preencha o campo abaixo e selecione o botão Enviar.
  // 97 - Um novo email será enviado para efetuar a confirmação de seu cadastro.
	// 71 - email:
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr>   \n");

  echo("                    <td align=left>\n");
  echo("                      <p style=\"text-indent:15px\">".RetornaFraseDaLista($lista_frases_autenticacao,94)."</p>\n");
  echo("                      <p style=\"text-indent:15px\">".RetornaFraseDaLista($lista_frases_autenticacao,95)."</p>\n");
  echo("                      <p style=\"text-indent:15px\">".RetornaFraseDaLista($lista_frases_autenticacao,96)."</p>\n");
  echo("                      <p style=\"text-indent:15px\">".RetornaFraseDaLista($lista_frases_autenticacao,97)."</p>\n");
  echo("                      <br />\n");
  echo("                      <form name=frmLogin method=post action=\"\" onSubmit=\"return(confere());\">\n");
  echo("                        <input type=hidden name=raiz_www value='".$raiz_www."' />\n");
  echo("                      		<div align=\"center\"><table>\n");
  echo("                        		<tr>\n");
  
  echo("                          		<td style=\"border:none; text-align:right;\">\n");
  echo("                          			".RetornaFraseDaLista($lista_frases_autenticacao,98)."\n");
  echo("                          			".RetornaFraseDaLista($lista_frases_autenticacao,71)."\n");
  echo("                          		</td>\n");
  echo("                          		<td style=\"border:none\">\n");
  echo("                            		<input class='input' type='text' name='input' />\n");
  echo("                          		</td>\n");
  
  echo("                        		</tr>\n");
  
  echo("                        		<tr>\n");
  echo("                          		<td style=\"border:none; text-align:right;\">\n");
  echo("                          		</td>\n");
  echo("                          		<td style=\"border:none\">\n");
  echo("                            	<br /><input class=\"input\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral, 11)."\" />\n");
  echo("                          		</td>\n");
  echo("                        		</tr>\n");
  echo("                      		</table>\n");
  echo("												</div>\n");
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