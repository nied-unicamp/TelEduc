<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/exibe_cursos.php

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
  ARQUIVO : pagina_inicial/exibe_cursos.php
  ========================================================== */

  $bibliotecas = "../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc"); 
  include("exibe_cursos.inc");
  include("inicial.inc");
  $pag_atual = "exibe_cursos.php";
  
  $sock=Conectar("");
  /* Caso o usu�rio n�o esteja logado, manda para tela de login. */
  if (empty ($_SESSION['login_usuario_s']))
  {
    Desconectar($sock);
    header("Location: ../cursos/aplic/index.php");
    exit;
  }
  /* Caso o usu�rio não tenha preenchido seus dados pessoais, manda para tela de preenchimento. */
  else if(!PreencheuDadosPessoais($sock))
  {
    Desconectar($sock);
    header("Location: preencher_dados.php?acao=preencherDados&atualizacao=true");
    exit;
  }
  /* Caso o usuário seja o adm, manda para tela dos cursos em andamento. */
  else if($_SESSION['cod_usuario_global_s'] == -1)
  {
    Desconectar($sock);
    header("Location: cursos_all.php?tipo_curso=A");
    exit;
  }

  include("../topo_tela_inicial.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro 
  $feedbackObject->addAction("logar", 197, 0);

  $lista_frases_autenticacao = RetornaListaDeFrases($sock, 25);

  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  				$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("      }\n\n");

  echo("      function TestaNome(form){\n");
  /* Elimina os espaços para verificar se o titulo nao eh formado por apenas espaços */
  echo("        Campo_login = form.login.value;;\n");
  echo("        Campo_senha = form.senha.value;\n");
  echo("        while (Campo_login.search(\" \") != -1){\n");
  echo("          Campo_login = Campo_login.replace(/ /, \"\");\n");
  echo("        }\n");
  echo("        if (Campo_login == ''){\n");
  /* 4 - Por favor preencha o campo 'Login'. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_autenticacao, 4)."');\n");
  echo("          document.formAutentica.login.focus();\n");
  echo("          return(false);\n");
  echo("        } else {\n");
  echo("          while (Campo_senha.search(\" \") != -1){\n");
  echo("            Campo_senha = Campo_senha.replace(/ /, \"\");\n");
  echo("          }\n");
  echo("          if (Campo_senha == ''){\n");
  /* 5 - Por favor preencha o campo \"Senha\". */
  echo("            alert('".RetornaFraseDaLista($lista_frases_autenticacao, 5)."');\n");
  echo("          document.formAutentica.senha.focus();\n");
  echo("            return(false);\n");  
  echo("          }\n");
  echo("        }\n");
  echo("        return(true);\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /*162 - Meus Cursos  */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,162)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td colspan=4>\n");

  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /*192 - Não iniciados (recém-criados no servidor)*/
  echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,192)."</td>");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  /* 5 - Curso */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
  /* 163 - Tipo usuario */
  echo("                    <td width=\"15%\">".RetornaFraseDaLista($lista_frases,163)."</td>\n");
  echo("                  </tr>\n");

  /*Exibe cursos recém aceitos ou cursos que ainda não começaram*/

  list ($lista_cursos, $total_cursos) = RetornaCursosNaoIniciados($sock, $_SESSION['codigo_usuario_s']);

  
  if (($total_cursos)==0)
  {
    echo("                  <tr>\n");
   /* 164 - Voce nao esta cadastrado em nenhum curso */	
    echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,164)."</td>\n");
    echo("                  </tr>\n");
  }
  else
  {
    $num = 0;
    $cor = 0;

    while ($num < $total_cursos)
    {
      $cor++;
      $cor%=2;

      echo("                  <tr>\n");
      echo("                    <td class=\"alLeft\">\n");
      echo("                      <a href=\"../cursos/aplic/index.php?cod_curso=".$lista_cursos[$num]['cod_curso']."\">".$lista_cursos[$num]['nome_curso']."</a>");
      echo("                    </td>\n");
      echo("                    <td align=\"center\" valign=\"top\" class=\"botao2\">\n");

      switch ($lista_cursos[$num]['tipo_usuario'])
      {
        //58 - Formador (geral) // 178 - Usuário
        case "F": echo "".RetornaFraseDaLista($lista_frases_geral,58).""; break;
        default: echo "".RetornaFraseDaLista($lista_frases,178)."";
      }

      echo("                    </td>\n");
      echo("                  </tr>\n");

      /* Incrementa o contador. */
      $num++;
    }
  }

  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,171)."</td>");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  /* 5 - Curso */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
  /* 163 - Tipo usuario */
  echo("                    <td width=\"15%\">".RetornaFraseDaLista($lista_frases,163)."</td>\n");
  echo("                  </tr>\n");

  /*Exibe cursos em andamento*/

  list ($lista_cursos, $total_cursos) = RetornaCursosEmAndamento($sock, $_SESSION['codigo_usuario_s']);

  if (($total_cursos)==0)
  {
    echo("                  <tr>\n");
   /* 164 - Voce nao esta cadastrado em nenhum curso */	
    echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,164)."</td>\n");
    echo("                  </tr>\n");
  }
  else
  {
    $num = 0;
    $cor = 0;

    while ($num < $total_cursos)
    {
      $cor++;
      $cor%=2;

      echo("                  <tr>\n");
      echo("                    <td class=\"alLeft\">\n");
      echo("                      <a href=\"../cursos/aplic/index.php?cod_curso=".$lista_cursos[$num]['cod_curso']."\">".$lista_cursos[$num]['nome_curso']."</a>");
      echo("                    </td>\n");
      echo("                    <td align=\"center\" valign=\"top\" class=\"botao2\">\n");

      switch ($lista_cursos[$num]['tipo_usuario'])
      {
        //58 - Formador (geral) // 178 - Usuário
        case "F": echo "".RetornaFraseDaLista($lista_frases_geral,58).""; break;
        default: echo "".RetornaFraseDaLista($lista_frases,178)."";
      }

      echo("                    </td>\n");
      echo("                  </tr>\n");

      /* Incrementa o contador. */
      $num++;
    }
  }

  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,173)."</td>");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  /* 5 - Curso */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
  /* 163 */
  echo("                    <td width=\"15%\">".RetornaFraseDaLista($lista_frases,163)."</td>\n");
  echo("                  </tr>\n");

  /*Exibe cursos jah oferecidos*/

  list ($lista_cursos, $total_cursos) = RetornaCursosPassados($sock, $_SESSION['codigo_usuario_s']);

  
  if (($total_cursos)==0)
  {
    echo("                  <tr>\n");	
    /* 164 - Voce nao esta cadastrado em nenhum curso */
    echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,164)."</td>\n");
    echo("                  </tr>\n");
  }
  else
  {
    $num = 0;
    $cor = 0;

    while ($num < $total_cursos)
    {
      $cor++;
      $cor%=2;

      echo("                  <tr>\n");
      echo("                    <td class=\"alLeft\">\n");
      echo("                      <a href=\"../cursos/aplic/index.php?cod_curso=".$lista_cursos[$num]['cod_curso']."\">".$lista_cursos[$num]['nome_curso']."</a>");
      echo("                    </td>\n");
      echo("                    <td align=\"center\" valign=\"top\" class=\"botao2\">\n");

      switch ($lista_cursos[$num]['tipo_usuario'])
      {
        //58 - Formador (geral) // 178 - Usuário
        case "F": echo "".RetornaFraseDaLista($lista_frases_geral,58).""; break;
        default: echo "".RetornaFraseDaLista($lista_frases,178)."";
      }

      echo("                    </td>\n");
      echo("                  </tr>\n");

      /* Incrementa o contador. */
      $num++;
    }
  }

  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  Desconectar($sock);
  echo("  </body>\n");
  echo("</html>");
?>

