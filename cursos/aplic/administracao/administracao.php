<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/administracao.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
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

    Nied - Núcleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/administracao/administracao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda = 1;

  include("../topo_tela.php");
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  // 255 - Erro na operacao
  // 137 - Senha(s) enviada(s) por email.
  $feedbackObject->addAction("enviarSenha", 137, 255);
  //273 - Ferramentas do curso escolhidas com sucesso.
  $feedbackObject->addAction("escolherFerramentas", 273, 255);
  //274 - Ferramentas compartilhadas com sucesso.
  $feedbackObject->addAction("compartilharFerramentas", 274, 255);
  //277 - Ferramentas destacadas com sucesso. As ferramentas destacadas aparecem em vermelho.
  $feedbackObject->addAction("marcarFerramentas", 277, 255);
  //27 -Dados alterados com sucesso.
  $feedbackObject->addAction("alterarDadosCurso", 27, 255);
  //276 -Cronograma alterado com sucesso.
  $feedbackObject->addAction("alterarCronograma", 276, 255);

  echo("        <script type=\"text/javascript\">\n");
  echo("          function Iniciar()\n");
  echo("          {\n");
                    $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("            startList();\n");
  echo("          }\n\n");
  echo("        </script>\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  // P�gina Principal
  /* 1 - Administra��o */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1)."</h4>\n");
  echo($cabecalho);

  /*Voltar*/
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("         <div id=\"mudarFonte\">\n");
  echo("           <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("           <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("           <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaInterna\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 231 - Dados do Curso*/
  echo("                    <td width=\"50%\" align=\"center\"><b>".RetornaFraseDaLista($lista_frases, 231)."</b></td>\n");
  /* 232 - Ferramentas */
  echo("                    <td width=\"50%\" align=\"center\"><b>".RetornaFraseDaLista($lista_frases, 232)."</b></td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td align=\"left\">\n");
  echo("                      <a href=\"alterar_dados_curso.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");

  if ($ecoordenador = ECoordenadorMesmo($sock, $cod_curso, $cod_usuario))
  {
    /* 2 - Visualizar / Alterar Dados do Curso */
    echo(RetornaFraseDaLista($lista_frases,2)."</a><br />\n");
  }
  else
  {
    /* 49 - Visualizar Dados do Curso */
    echo(RetornaFraseDaLista($lista_frases,49)."</a><br />\n");
  }

  $bold_tag = array(array("", ""), array("<b>", "</b>"));

  $ferr_alt = HouveAlteracoes($sock,$cod_curso,$cod_usuario);

  echo("                      <a href=\"alterar_cronograma.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");

  /* 31 - Visualizar / Alterar Cronograma do Curso */
  echo($bold_tag[$ferr_alt[0]][0].RetornaFraseDaLista($lista_frases,31).$bold_tag[$ferr_alt[0]][1]."</a><br />\n");
  echo("                    </td>\n");
  echo("                    <td align=\"left\">\n");
  echo("                      <a href=\"escolher_ferramentas.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");
  /* 40 - Escolher Ferramentas do Curso */
  echo($bold_tag[$ferr_alt[1]][0].RetornaFraseDaLista($lista_frases,40).$bold_tag[$ferr_alt[1]][1]."</a><br />\n");
  echo("                      <a href=\"compartilhar_ferramentas.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=F\">");
 /* 202 - Compartilhar Ferramentas */
  echo($bold_tag[$ferr_alt[2]][0].RetornaFraseDaLista($lista_frases, 202).$bold_tag[$ferr_alt[2]][1]."</a><br />\n");
  echo("                      <a href=\"marcar_ferramentas.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=F\">");
  /* 141 - Marcar Ferramentas */
  echo($bold_tag[$ferr_alt[3]][0].RetornaFraseDaLista($lista_frases, 141).$bold_tag[$ferr_alt[3]][1]."</a><br />\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head\">\n");
  /* 233 - Inscricao*/
  echo("                    <td align=\"center\"><b>".RetornaFraseDaLista($lista_frases, 233)."</b></td>\n");
  /* 234 - Gerenciamento */
  echo("                    <td align=\"center\"><b>".RetornaFraseDaLista($lista_frases, 234)."</b></td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td align=\"left\">\n");
  if ($ecoordenador)
  {
    echo("                      <a href=\"inscrever.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=F\">");

    /* 50 - Inscrever Formadores */
    echo(RetornaFraseDaLista($lista_frases,50)."</a><br />\n");
  }

  echo("                      <a href=\"inscrever.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=A\">");

  /* 51 - Inscrever Alunos */
  echo(RetornaFraseDaLista($lista_frases,51)."</a><br />\n");

  // 164 - Inscrever Colaboradores
  echo("                      <a href=\"inscrever.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=Z\">".RetornaFraseDaLista($lista_frases, 164)."</a><br />\n");
  // aqui, a variavel origem indica que a proxima pagina veio de administracao.php

  // 182 - Inscrever visitantes
  echo("    <a href=\"inscrever.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=0&tipo_usuario=V\">".RetornaFraseDaLista($lista_frases,182)."</a><br /><br />\n");

  echo("                    </td>\n");
  echo("                    <td align=\"left\">\n");

  echo("                      <a href=\"gerenciamento_inscricoes.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=i\">");
  /* 74 - Gerenciamento de Inscri��es */
  echo($bold_tag[$ferr_alt[3]][0].RetornaFraseDaLista($lista_frases,74).$bold_tag[$ferr_alt[3]][1]."</a><br />\n");

  echo("                      <a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=A\">");
  /* 102 - Gerenciamento de Alunos */
  echo(RetornaFraseDaLista($lista_frases,102));
  echo("</a><br />\n");

  echo("                      <a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=F\">");
  // 103 - Gerenciamento de Formadores
  echo(RetornaFraseDaLista($lista_frases,103));
  echo("</a><br />\n");

  // 165 - Gerenciamento de Colaboradores
  echo("                      <a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=Z\">");
  echo(RetornaFraseDaLista($lista_frases, 165));
  echo("</a><br />\n");

  // 179 - Gerenciamento de Visitantes
  echo("                      <a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=V\">");
  echo(RetornaFraseDaLista($lista_frases, 179));
  echo("</a><br />\n");

  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head\">\n");
  /* 235 - Opcoes*/
  echo("                    <td align=\"center\"><b>".RetornaFraseDaLista($lista_frases, 235)."</b></td>\n");
  /* 236 - Extracao*/
  echo("                    <td align=\"center\"><b>".RetornaFraseDaLista($lista_frases, 236)."</b></td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td align=\"left\">\n");
  if ($ecoordenador)
  {
    echo("                      <a href=\"alterar_nomenclatura.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");
    /* 149 - Alterar nomenclatura do coordenador */
    echo(RetornaFraseDaLista($lista_frases, 149));
    echo("</a><br />\n");
  }

  echo("                      <a href=\"enviar_senha.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");
  /* 133 - Enviar Senha */
  echo(RetornaFraseDaLista($lista_frases,133)."</a><br />\n");
  echo("                    </td>\n");
  echo("                    <td align=\"left\">\n");

  Desconectar($sock);
  $sock = Conectar("");
  
  $extrator = 'nao';
  
  $query = "select valor from Config where item='extrator'";
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);
  $extrator = $linha['valor'];

  if ($extrator == 'sim') {
    if ($ecoordenador) {
      echo("                      <a href=\"extracao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");
      /* 212 - Agendar Extra��o do Curso */
      echo(RetornaFraseDaLista($lista_frases, 212)."</a><br />\n");

      echo("                      <a href=\"remover_extracao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");
      /* 213 - Listar / Remover Extra��o do Curso */
      echo(RetornaFraseDaLista($lista_frases, 213)."</a><br />\n");
    }
    else
      /*237 - Secao permitada somente a coordenadores*/
      echo("                      ".RetornaFraseDaLista($lista_frases, 237)."\n");
  }
  else
    /*238 - Secao atualmente inexistente*/
    echo("                      ".RetornaFraseDaLista($lista_frases, 238)."\n");

  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
