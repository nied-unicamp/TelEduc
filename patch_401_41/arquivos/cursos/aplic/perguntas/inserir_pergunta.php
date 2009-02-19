<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/perguntas.php

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
  ARQUIVO : cursos/aplic/perguntas/perguntas.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  
  $cod_ferramenta = 6;  
  include("../topo_tela.php");

  /* Verifica se o usuario eh formador. */
  if (EFormador($sock, $cod_curso, $cod_usuario))
    $usr_formador = true;
  else
    $usr_formador = false;

  echo("<script language=JavaScript>\n\n");

  echo("  function CancelaPergunta()\n");
  echo("  {\n");
  echo("    document.location.href=\"perguntas.php?");
  echo("&cod_curso=".$cod_curso."&cod_assunto_pai=".$cod_assunto_pai."&".time()."\";\n");
  echo("    return(true);\n");
  echo("  }\n\n");

  echo("  function testa_pergunta()\n");
  echo("  {\n");
  /* Elimina os espa�os para verificar se a pergunta nao eh formada apenas por espa�os */
  echo("    pergunta_conteudo = document.inserir_pergunta.pergunta.value;\n");
  echo("    while (pergunta_conteudo.search(\" \") != -1)\n");
  echo("    {\n");
  echo("      pergunta_conteudo = pergunta_conteudo.replace(/ /, \"\");\n");
  echo("    }\n");

  echo("    if (pergunta_conteudo == '')\n");
  echo("    {\n");
  /* 12 - A pergunta n�o pode estar vazia. */
  echo("      alert('".RetornaFraseDaLista($lista_frases, 12)."');\n");
  echo("      return(false);\n");
  echo("    } else {\n");
  echo("      return(true);\n");
  echo("    }\n");
  echo("  }\n\n");

  echo("  function testa_resposta()\n");
  echo("  {\n");
  /* Elimina os espa�os para verificar se a resposta nao eh formada apenas por espa�os */
  echo("    resposta_conteudo = document.inserir_pergunta.resposta.value;\n");
  echo("    while (resposta_conteudo.search(\" \") != -1)\n");
  echo("    {\n");
  echo("      resposta_conteudo = resposta_conteudo.replace(/ /, \"\");\n");
  echo("    }\n");

  echo("    if (resposta_conteudo == '')\n");
  echo("    {\n");
  /* 13 - A resposta n�o pode estar vazia. */
  echo("      alert('".RetornaFraseDaLista($lista_frases, 13)."');\n");
  echo("      return(false);\n");
  echo("    } else {\n");
  echo("      return(true);\n");
  echo("    }\n");
  echo("  }\n\n");

  echo("  function testa_campos()\n");
  echo("  {\n");
  echo("    if ((testa_pergunta()) && (testa_resposta()))\n");
  echo("    {\n");
  echo("      return(true);\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
  echo("      return(false);\n");
  echo("    }\n");
  echo("  }\n\n");

  echo("\n</script>\n\n");
  
  include("../menu_principal.php");

//  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  /* 1 - Perguntas Freq�entes */
 // echo(" <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>");
  /* 3 - Inserir Perguntas */
//  echo("  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,3)."</b>");

//  echo(PreparaCabecalho($cod_curso, $cabecalho, 6,3));
  
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* 1 - Perguntas */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1));
  /* 3 - Inserir Perguntas */
  echo(" - ".RetornaFraseDaLista($lista_frases,3));

  echo("</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");
  
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span>\n");
  
  /* 1 - Perguntas Freq�entes */
//  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";

  //echo("  <br>\n");
  
  echo("  <span class=\"btsNav2\"><a href=# onClick='MostraLayer(lay_estrutura,this);return(false);'><img src=../imgs/estrutura.gif border=0></a>\n");
  //echo("  <a href=# onMouseDown='MostraLayer(lay_estrutura,0);return(false);'><img src=../figuras/estrutura.gif border=0></a>\n");
  echo("    <font class=text>".RetornaLinkCaminhoAssunto($sock, $cod_assunto_pai, $cod_curso, "perguntas"));
  echo("    </font></span>\n");
  echo("  \n");
  
  
  /* Obtem e exibe o caminho onde a pergunta sera inserida */
/*  echo("  <div><img src=../figuras/assuntoab.gif>\n");
  echo("    <font class=text>".RetornaCaminhoAssunto($sock, $cod_assunto_pai));
  echo("    </font>\n");
  echo("  </div>\n");
  echo("  <br>\n");*/
  echo("  <form name=inserir_pergunta method=post action=acoes.php onsubmit='return(testa_campos());'>\n");

  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");


  echo("    <input type=hidden name=cod_assunto_pai value='".$cod_assunto_pai."'>\n");
  echo("	<input type=hidden name=acao value=\"SalvaPergunta\">\n");
  
  
//  echo("    <table border=\"0\" cellspacing=\"0\">\n");
//  echo("      <tr class=colorfield>\n");
  echo("          <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("              <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("                <td class=\"btAuxTabs\">\n");
  echo("                  <ul class=\"btAuxTabs\">\n");
 
  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");  
  echo("                    <tr class=\"head\">\n");
  

/* 10 - Pergunta */
  
  echo("        <td class=colorfield>".RetornaFraseDaLista($lista_frases,10)."</td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td>\n");
//  echo("          <input type=text name=pergunta size=40 maxlength=150 value='".$pergunta."'>\n");
  echo("          <input type=text name=pergunta size=40 maxlength=150 value='".$novo_nome."'>\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  /* Poe o foco no campo pergunta. */
  echo("    <script language=JavaScript>\n\n");
  echo("      document.inserir_pergunta.pergunta.focus();\n");
  echo("    </script>\n");

  echo("      <tr class=\"head\">\n");
  /* 11 - Resposta */
  echo("        <td class=colorfield>".RetornaFraseDaLista($lista_frases, 11)."</td>\n");
  echo("      </tr>\n");

  echo("      <tr>\n");
  echo("        <td>\n");
  echo("          <textarea name=resposta class=text rows=5 cols=60 wrap=soft>".$resposta."</textarea>\n");
  echo("        </td>\n");
  echo("      </tr>\n");


  echo("    <br>\n");
  echo("    <p>\n");

  echo("    <div align=left width=100%>\n");
  /* 11 - Enviar */
  echo("      <td><input type=submit class=\"input\" value=".RetornaFraseDaLista($lista_frases_geral,11).">\n");
  /* 2 - Cancelar */
  echo("      <input type=button class=\"input\" onclick='CancelaPergunta();' value=".RetornaFraseDaLista($lista_frases_geral,2)."></td>\n");
  echo("    </div>\n");
  echo("    </table>\n");
  echo("    </table>\n");
  echo("  </form>\n");

  echo("</td>");
  include("../tela2.php");
  
  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
