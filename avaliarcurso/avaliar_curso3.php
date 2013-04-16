<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/avaliar_curso3.php

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
  ARQUIVO : administracao/avaliar_curso3.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include("avaliarcurso.inc");

  VerificaAutenticacaoAdministracao();

  if(!isset($todos))
    $todos = "";

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script type=\"text/javascript\" src=\"../cursos/aplic/bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("<script type=\"text/javascript\" src=\"../cursos/aplic/bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  echo("    <script type=\"text/javascript\" language=\"JavaScript\" src=\"../cursos/aplic/bibliotecas/javacrypt.js\"></script>\n");
  echo("    <script type=text/javascript>\n");

  /**********************************************************************
  Funcao Envia - JavaScript. Altera atributos do formul�rio para submiss�o � p�gina seguinte ou anterior.
    Entrada: pagina - nome da pr�xima p�gina.
    Saida: Transforma o formul�rio para o tipo 'submit'
  */
  echo("      function Envia(pagina)\n");
  echo("      {\n");
  echo("        document.enviar.action=pagina;\n");
  echo("        document.enviar.submit();\n");
  echo("      }\n");

  echo("      function Iniciar() {\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("    </script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 244 - Avaliar requisi��es para abertura de cursos */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,244)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  $curso=RetornaDadosCursoReq($sock,$cod_curso);

  /* 229 - Dados do curso solicitado: */
  $dados = RetornaFraseDaLista($lista_frases, 229)."<br /><br />";
  /* 92 - Nome do Curso: */
  $dados .= RetornaFraseDaLista($lista_frases, 92)." ".$curso['nome_curso']."<br />";
  /* 217 - Dura��o estimada: */
  $dados .= RetornaFraseDaLista($lista_frases, 217)." ".$curso['duracao']."<br />";
  /* 93 - N�mero de Alunos: */
  $dados .= RetornaFraseDaLista($lista_frases, 93)." ".$curso['num_alunos']."<br />";

  /* 94 - Categoria: */
  $dados .= RetornaFraseDaLista($lista_frases, 94)."<br />";

  if ($curso['cod_pasta'] != "")
    $dados .= RetornaCategoria($sock,$curso['cod_pasta'])."<br />";
  else
    $dados .= " ";

  /* 218 - P�blico alvo: */
  $dados .= RetornaFraseDaLista($lista_frases, 218)." ".$curso['publico_alvo']."<br />";

  /* 219 - Forma de inscri��o: */
  $dados .= RetornaFraseDaLista($lista_frases, 219)." ".$curso['tipo_inscricao']."<br />";
  /* 220 - Informa��es adicionais: */
  $dados .= RetornaFraseDaLista($lista_frases, 220)." ".$curso['informacoes']."<br />";
  /* 221 - Nome do Contatante: */
  $dados .= RetornaFraseDaLista($lista_frases, 221)." ".$curso['nome_contato']."<br />";
  /* 222 - Nome da Institui��o: */
  $dados .= RetornaFraseDaLista($lista_frases, 222)." ".$curso['instituicao']."<br />";
  /* 223 - E-mail para Contato: */
  $dados .= RetornaFraseDaLista($lista_frases, 223)." ".$curso['email_contato']."<br />";
  /* 224 - Data de requisi��o: */
  $dados .= RetornaFraseDaLista($lista_frases, 224)." ".UnixTime2DataHora($curso['data'])."<br />";

  if ($opcao=="Aceitar")
  {
    /* 230 - Aceitar Curso */
    $titulo = RetornaFraseDaLista($lista_frases, 230);
    /* 231 - Curso Aceito */
    $assunto = "TelEduc: ";
    $assunto .= RetornaFraseDaLista($lista_frases, 231);
    /* 232 - Prezado(a)  */
    /* 233 - , */
    /* 100 - Seu pedido para realiza��o do curso */
    /* 101 - foi aceito. */
    /* 105 -Atenciosamente, Administra��o do Ambiente TelEduc. */
    $mensagem = RetornaFraseDaLista($lista_frases, 232);
    $mensagem .= $curso['nome_contato'].RetornaFraseDaLista($lista_frases, 233)."<br /><br />";
    $mensagem .= "&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases,100)." ";
    $mensagem .= $curso['nome_curso']." ";
    $mensagem .= RetornaFraseDaLista($lista_frases,101)."<br /><br />";
    $mensagem .= $dados;
    $mensagem .= "<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,105)."</p>";
  }
  else if ($opcao=="Rejeitar")
  {
    /* 234 - Rejeitar Curso */
    $titulo = RetornaFraseDaLista($lista_frases,234);
    /* 235 - Curso n�o aceito */
    $assunto = "TelEduc: ";
    $assunto .= RetornaFraseDaLista($lista_frases,235);
    /* 232 - Prezado(a) */
    /* 233 - , */
    /* 236 - Infelizmente, seu pedido para realiza��o do curso */
    /* 237 - n�o foi aceito. */
    /* 105 -Atenciosamente, Administra��o do Ambiente TelEduc. */
    $mensagem = RetornaFraseDaLista($lista_frases,232);
    $mensagem .= $curso['nome_contato'].RetornaFraseDaLista($lista_frases,233)."<br /><br />";
    $mensagem .= "&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases,236)." ";
    $mensagem .= $curso['nome_curso']." ";
    $mensagem .= RetornaFraseDaLista($lista_frases,237)."<br /><br />";
    $mensagem .= $dados;
    $mensagem .= "<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,105)."</p>";
  }

  echo("          <form name=\"enviar\" method=\"post\" action=\"avaliar_curso4.php\" onsubmit=\"updateRTE('mensagem');\">\n");
  echo("            <input type=hidden name=cod value=".$cod.">\n");
  echo("            <input type=hidden name=todos value=".$todos.">\n");
  echo("            <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("            <input type=hidden name=opcao value=".$opcao.">\n");
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  /* 23 - Voltar (gen) */
  echo("                  <ul class=\"btAuxTabs\">\n");
  echo("                    <li><a href=\"#\" onclick=\"javascript:history.back(-1);\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                  </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td><b>".$titulo."</b></td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  echo("                    <td style=\"border:0;\">");
  
  if ($opcao=="Aceitar")
    /* 240 - Ao enviar a mensagem, estar� confirmando a aceita��o do curso */
    echo(RetornaFraseDaLista($lista_frases, 240)." \n");
  else if ($opcao=="Rejeitar")
    /* 241 - Ao enviar a mensagem, estar� confirmando a rejei��o do curso */
    echo(RetornaFraseDaLista($lista_frases, 241)." \n");

  echo(" ".$curso['nome_curso']."</td>\n");
  echo("                    </tr>\n");
  echo("                    <tr>\n");
  echo("                      <td style=\"border:0;\">\n");
  /* 238 - Titulo: */
  echo("                      <br>".RetornaFraseDaLista($lista_frases,238)."<br />\n");
  echo("                      <input class=\"input\" type=text size=60 name=\"assunto\" value='".$assunto."'><br /><br />\n");
  /* 48 - Mensagem: */
  echo("                      ".RetornaFraseDaLista($lista_frases,48)."\n");
  echo("                      <div align=\"center\"><script type=\"text/javascript\">\n");
  echo("                        writeRichText('mensagem', '".$mensagem."', 520, 200, true, false, false);\n");
  echo("                      </script></div>\n");
  echo("                      <font class=text color=red>".RetornaFraseDaLista($lista_frases,239)."</font>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td align=\"right\">\n");
  /* 11 - Enviar */
  echo("                <input class=\"input\" type=\"submit\" value='".RetornaFraseDaLista($lista_frases_geral,11)."'>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
