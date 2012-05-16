<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/mostra_curso.php

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
  ARQUIVO : pagina_inicial/mostra_curso.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  $pag_atual = "mostra_curso.php";
  include("../topo_tela_inicial.php");

  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  $dados_curso=RetornaDadosMostraCurso($sock,$cod_curso);

  $dados_email=DadosCursoParaEmail($sock,$cod_curso);

  Desconectar($sock);
  $sock=Conectar($cod_curso);

  echo("          <h4>".$dados_curso['nome_curso']."</h4>\n");

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
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 68 - Voltar para lista de cursos */
  echo("                  <li><span onClick=\"document.location='cursos_all.php?tipo_curso=".$tipo_curso."';\">".RetornaFraseDaLista($lista_frases,68)."</span></li>\n");
  if ($dados_curso['acesso_visitante']=="A")
  {
    /* 56 - Visitar */
    echo("                <li><span onClick=\"document.location='../index.php?cod_curso=".$dados_curso['cod_curso']."&amp;visitante=sim';\">".RetornaFraseDaLista($lista_frases,56)."</span></li>\n");
  }
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td align=\"left\" colspan=\"2\">".$dados_curso['informacoes']."</td>\n");
  echo("                  </tr>\n");

  /* 60 - P�blico Alvo: */
  echo("                  <tr>\n");
  echo("                    <td align=left colspan=\"2\"><b>".RetornaFraseDaLista($lista_frases,60)."</b>".$dados_curso['publico_alvo']."</td>\n");
  echo("                  </tr>\n");

  /* 61 - Per�odo do curso: */
  /* 69 - de */
  /* 70 - a */
  echo("                  <tr>\n");
  echo("                    <td align=left colspan=\"2\"><b>".RetornaFraseDaLista($lista_frases,61)."</b> ".RetornaFraseDaLista($lista_frases,69)." ".UnixTime2Data($dados_curso['curso_inicio'])." ".RetornaFraseDaLista($lista_frases,70)." ".UnixTime2Data($dados_curso['curso_fim'])."</td>\n");
  echo("                  </tr>\n");

  /* 196 - Per�odo de inscrição no curso: */
  /* 69 - de */
  /* 70 - a */
  echo("                  <tr>\n");
  echo("                    <td align=left colspan=\"2\"><b>".RetornaFraseDaLista($lista_frases,196).": </b>".RetornaFraseDaLista($lista_frases,69)." ".UnixTime2Data($dados_curso['inscricao_inicio'])." ".RetornaFraseDaLista($lista_frases,70)." ".UnixTime2Data($dados_curso['inscricao_fim'])."</td>\n");
  echo("                  </tr>\n");

  /* 156 - Coordenador do curso: */
  echo("                  <tr>\n");
  echo("                    <td align=left colspan=\"2\"><b>".RetornaFraseDaLista($lista_frases,156)."</b>".$dados_email['nome_coordenador']."</td>\n");
  echo("                  </tr>\n");

  /* 62 - E-mail para contato: */
  echo("                  <tr>\n");
  echo("                    <td align=left colspan=\"2\"><b>".RetornaFraseDaLista($lista_frases,62)."</b><a href=mailto:".$dados_email['email'].">".$dados_email['email']."</a></td>\n");
  echo("                  </tr>\n");

  /* 63 - Tipo de inscri��o: */
  echo("                  <tr>\n");
  echo("                    <td align=left colspan=\"2\"><b>".RetornaFraseDaLista($lista_frases,63)."</b>".$dados_curso['tipo_inscricao']."</td>\n");
  echo("                  </tr>\n");

  $hoje=time();
  $ontem=$hoje - 86400;

  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  //if ($dados_curso['inscricao_inicio']<=$hoje && $dados_curso['inscricao_fim']>=$ontem && !ParticipaDoCurso($cod_curso) && !empty($_SESSION['login_usuario_s']))
  if ($dados_curso['inscricao_inicio']<=$hoje && $dados_curso['inscricao_fim']>=$ontem && !ParticipaDoCurso($cod_curso))
  {
    echo("            <tr>\n");
    echo("              <td align=right>\n");
    /* 67 - Inscreva-se! */
    echo("                <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,67)."\" onclick=\"document.location='inscricao.php?cod_curso=".$cod_curso."&amp;tipo_curso=".$tipo_curso."';\" type=\"button\" />\n");
    echo("              </td>\n");
    echo("            </tr>\n");
  }
  //else{
  	//echo("            <tr>\n");
    //echo("              <td align=right>\n");
    ///* 67 - Inscreva-se! */
    //echo("                <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,67)."\" onclick=\"document.location='autenticacao_cadastro.php?cod_curso=".$cod_curso."&amp;tipo_curso=".$tipo_curso."';\" type=\"button\" />\n");
    //echo("              </td>\n");
    //echo("            </tr>\n");
  //}

  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>