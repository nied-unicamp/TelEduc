<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/autenticacao.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
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

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : pagina_inicial/autenticacao.php
  ========================================================== */

  $bibliotecas = "../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("autenticacao.inc");

  if(!isset($ordem))
    $ordem="";
  if(!isset($todas_abertas))
    $todas_abertas="";
  if(!isset($login))
    $login="";
  if(!isset($erro_autenticacao))
    $erro_autenticacao="";

  $pag_atual = "autenticacao.php";
  include("../topo_tela_inicial.php");

  /* Caso o usuário já esteja logado, direciona para páigna inicial do curso */
  if (!empty ($_SESSION['login_usuario_s']))
  {
    /* Obt� a raiz_www */
    //$sock = Conectar("");
    $query = "select diretorio from Diretorio where item = 'raiz_www'";
    $res = Enviar($sock,$query);
    $linha = RetornaLinha($res);
    $raiz_www = $linha[0];

    $caminho = $raiz_www."/pagina_inicial";

    header("Location: {$caminho}/exibe_cursos.php");
    Desconectar($sock);
    exit;
  }
  
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("erroAutenticacao", 0, 180);
  $feedbackObject->addAction("emailConfirmacao", 210, 0);

  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\">\n\n");
  echo("      function Iniciar()\n");
  echo("      {\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
// adicionar a frase no banco de dados..
  if($destino == "inscricao")
  	echo("      mostraFeedback('".RetornaFraseDaLista($lista_frases, 201)." <a href=\"cadastro.php?cod_curso=".$cod_curso."&tipo_curso=".$tipo_curso."&acao=".$destino."\">".RetornaFraseDaLista($lista_frases, 203)."</a> ".RetornaFraseDaLista($lista_frases, 202)."', false);\n");
  echo("        document.getElementById('login').focus();\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function TestaNome(form){\n");
  /* Elimina os espaços para verificar se o titulo nao eh formado por apenas espaços */
  echo("        Campo_login = form.login.value;;\n");
  echo("        Campo_senha = form.senha.value;\n");
  echo("        while (Campo_login.search(\" \") != -1){\n");
  echo("          Campo_login = Campo_login.replace(/ /, \"\");\n");
  echo("        }\n");
  echo("        if (Campo_login == ''){\n");
  /* 181 - Por favor preencha o campo 'Login'. */
  echo("          alert('".html_entity_decode(RetornaFraseDaLista($lista_frases, 181))."');\n");
  echo("          document.formAutentica.login.focus();\n");
  echo("          return(false);\n");
  echo("        } else {\n");
  echo("          while (Campo_senha.search(\" \") != -1){\n");
  echo("            Campo_senha = Campo_senha.replace(/ /, \"\");\n");
  echo("          }\n");
  echo("          if (Campo_senha == ''){\n");
  /* 182 - Por favor preencha o campo \"Senha\". */
  echo("            alert('".html_entity_decode((RetornaFraseDaLista($lista_frases, 182)))."');\n");
  echo("          document.formAutentica.senha.focus();\n");
  echo("            return(false);\n");  
  echo("          }\n");
  echo("        }\n");
  echo("        return(true);\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 183 -  Autenticacao */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,183)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td colspan=\"4\">\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">   \n");
  // 165
  echo("                    <td align=\"left\">".RetornaFraseDaLista($lista_frases,165)."</td>\n");
  echo("                  </tr>   \n");
  echo("                  <tr id=\"caixaAutenticacao\">   \n");
  echo("                    <td align=\"center\">\n");
  echo("                        <form id=\"formAutentica\" name=\"formAutentica\" action=\"acoes.php\" onSubmit=\"return(TestaNome(document.formAutentica));\" method=\"post\" >\n");
  echo("                          <input type=\"hidden\" name=\"acao\" id=\"acao\" value=\"autenticar\" />\n");
  echo("                          <input type=\"hidden\" name=\"cod_curso\" value=\"".$_GET['cod_curso']."\" />\n");
  if(isset($tipo_curso))
    echo("                          <input type=\"hidden\" name=\"tipo_curso\" value=\"".$tipo_curso."\" />\n");
  if(isset($destino))
    echo("                          <input type=\"hidden\" name=\"destino\" value=\"".$destino."\" />\n");
  /* 157 Login */			
  echo("                          <table>\n");
  echo("                          	<tr>\n");
  echo("                          	  <td style=\"border:none; text-align:right;\">\n");
  echo("                          	    <b>".RetornaFraseDaLista($lista_frases,157)."</b>\n");
  echo("                          	  </td>\n");
  echo("                          	  <td style=\"border:none\">\n");
  echo("                          	    <input type=\"text\" id=\"login\" name=\"login\" size=\"20\" maxlength=\"100\" value='".$login."' style=\"border: 2px solid #9bc;\" />\n");
  echo("                          	  </td>\n");
  echo("                          	</tr>\n");
  /* 158 Senha */
  echo("                          	<tr>\n");
  echo("                          	  <td style=\"border:none; text-align:right;\">\n");
  echo("                          	    <b>".RetornaFraseDaLista($lista_frases,158)."</b>\n");
  echo("                          	  </td>\n");
  echo("                          	  <td style=\"border:none\">\n");
  echo("                                    <input type=\"password\" id=\"senha\" name=\"senha\" size=\"20\" maxlength=\"100\" style=\"border: 2px solid #9bc;\" />\n");
  echo("                          	  </td>\n");
  echo("                          	</tr>\n");
  echo("                          	<tr>\n");
  echo("                          	  <td style=\"border:none; text-align:right;\">&nbsp;</td>\n");
  echo("                          	  <td style=\"border:none\">\n");
  /* 18 - Ok */
  echo("                          <br /><input type=\"submit\" class=\"input\" id=\"OKLogin\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  echo("                          	  </td>\n");
  echo("                          	</tr>\n");
  echo("                          </table>\n");
  echo("                        </form>\n");
  /* 67 - Se esqueceu seu login, siga o link: */
  /* 68 - Esqueci meu login!                  */
  echo ("                    <br/>".RetornaFrase($sock, 67, -2)." <a href='esqueci_login.php'>".RetornaFrase($sock, 68, -2)."</a><br/>");
  /* 24 - Caso tenha esquecido sua senha siga o link: */
  /* 23 - Esqueci minha senha!                        */
  echo ("                    ".RetornaFrase($sock, 24, -2)." <a href='esqueci_senha.php'>".RetornaFrase($sock, 23, -2)."</a>");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>");
  Desconectar($sock);
?>

