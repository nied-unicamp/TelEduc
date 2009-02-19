<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/criar_curso2.php

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
  ARQUIVO : pagina_inicial/criar_curso2.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  $pag_atual = "criar_curso2.php";
  include("../topo_tela_inicial.php");

  /* Caso o usuário naum esteja logado, direciona para página de login */
  if (empty($_SESSION['login_usuario_s']))
  {
    /* Obt� a raiz_www */
    //$sock = Conectar("");
    $query = "select diretorio from Diretorio where item = 'raiz_www'";
    $res = Enviar($sock,$query);
    $linha = RetornaLinha($res);
    $raiz_www = $linha[0];

    $caminho = $raiz_www."/pagina_inicial";

    header("Location: {$caminho}/autenticacao.php");
    Desconectar($sock);
    exit;
  }

  if ((!isset($curso_form)) || ($curso_form == "nao"))
    exit();

  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  //9 - Como criar um curso
  echo("          <h4>".RetornaFraseDaLista($lista_frases,9)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("          <tr>\n");
  echo("            <td>\n");
  echo("              <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                <li><span onClick=\"document.location='criar_curso.php';\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("              </ul>\n");
  echo("            </td>\n");
  echo("          </tr>\n");
  echo("          <tr>\n");
  echo("            <td valign=\"top\">\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");

  $cod_curso=RetornaCodCurso($sock);
  $data=time();

  $avaliado='N';

  $nome_curso=EliminaScript($nome_curso);
  $duracao=EliminaScript($duracao);
  $info=EliminaScript($info);
  $publico_alvo=EliminaScript($publico_alvo);
  $tipo_inscricao=EliminaScript($tipo_inscricao);
  $nome_inst=EliminaScript($nome_inst);
  $nome_contato=EliminaScript($nome_contato);
  $email=EliminaScript($email);
  $login=EliminaScript($login);

  $nome_curso=LimpaConteudo($nome_curso);
  $duracao=LimpaConteudo($duracao);
  $info=LimpaConteudo($info);
  $publico_alvo=LimpaConteudo($publico_alvo);
  $tipo_inscricao=LimpaConteudo($tipo_inscricao);
  $nome_inst=LimpaConteudo($nome_inst);
  $nome_contato=LimpaConteudo($nome_contato);
  $email=LimpaConteudo($email);
  $login=LimpaConteudo($login);

  $info=Enter2BR($info);

  $enviou=EnviaDadosCurso($sock,$cod_curso,$nome_curso,$duracao,$info,$publico_alvo,$tipo_inscricao,$num_alunos,$cod_pasta,$data,$nome_contato,$login,$email,$nome_inst,$avaliado);

  if ($enviou)
  {
    /* 152 - Pedido de curso enviado com sucesso. */
    echo("              <tr>\n");
    echo("                <td>".RetornaFraseDaLista($lista_frases, 152)."</td>\n");
    echo("              </tr>\n");
  }
  else
  {
    /* 153 - Erro no envio de pedido. Tente novamente.*/
    echo("              <tr>\n");
    echo("                <td>".RetornaFraseDaLista($lista_frases, 153)."</td>\n");
    echo("              </tr>\n");
  }

  echo("              </table>\n");
  echo("            </td>\n");
  echo("          </tr>\n");
  echo("        </table>\n");
  echo("      </td>\n");
  echo("    </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
