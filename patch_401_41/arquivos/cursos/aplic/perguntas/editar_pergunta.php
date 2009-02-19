<?

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/editar_pergunta.php

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
  ARQUIVO : cursos/aplic/perguntas/editar_pergunta.php
  ========================================================== */


  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  $cod_ferramenta = 6;  
  include("../topo_tela.php");
/*  $cod_usuario = VerificaAutenticacao($cod_curso);

  $sock = Conectar("");

  $lista_frases = RetornaListaDeFrases($sock, 6);
  $lista_frases_geral = RetornaListaDeFrases($sock, -1);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  VerificaAcessoAoCurso($sock, $cod_curso, $cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,6);

  echo("  <html>\n");
  // 1 - Perguntas Freq�entes 
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
*/
  echo("<script language=JavaScript>\n\n");

  echo("  function CancelaEdicao()\n");
  echo("  {\n");
  echo("    document.frmCancelaEdicao.submit();\n");
  echo("    return(true);\n");
  echo("  }\n\n");

  echo("  function testa_pergunta()\n");
  echo("  {\n");
  /* Elimina os espa�os para verificar se a pergunta nao eh formada apenas por espa�os */
  echo("    pergunta_conteudo = document.editar_pergunta.pergunta.value;\n");
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
  echo("    resposta_conteudo = document.editar_pergunta.resposta.value;\n");
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

//  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  /* 1 - Perguntas Freq�entes */
//  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 22 - Editar Pergunta */
//  $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,22)."</b>";
//  echo(PreparaCabecalho($cod_curso, $cabecalho, 6,6));

  // Obtem e exibe o caminho onde a pergunta esta armazenada
/*  echo("  <div><img src=../figuras/assuntoab.gif>\n");
  echo("    <font class=text>".RetornaCaminhoAssunto($sock, $cod_assunto_pai));
  echo("    </font>\n");
  echo("  </div>\n");
*/  echo("  <br>\n");

  /* Obtem a pergunta e a resposta. */
  $dados_pergunta = RetornaPergunta($sock, $cod_pergunta);
  $pergunta = $dados_pergunta['pergunta'];
  $resposta = $dados_pergunta['resposta'];

  echo("  <form name=editar_pergunta method=post action=acoes.php onsubmit='return(testa_campos());'>\n");
  echo("    <input type=hidden name=acao value=\"EditarPergunta\">\n");
  
  
//  echo(RetornaSessionIDInput());
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");

  echo("    <input type=hidden name=RetCheck value=".$cod_pergunta.">\n");

  /* Repassa o documento de origem*/
  echo("    <input type=hidden name=origem value=".$origem.">\n");

  /* Repassa a variavel que indica o documento da pagina principal, */
  /* o qual chamou o ver_pergunta.php. Isto eh necessario para      */
  /* atualizar a pagina principal que pode ser perguntas.php ou     */
  /* exibir_todas.php.                                              */
  echo("    <input type=hidden name=pagprinc value=".$pagprinc.">\n");

  echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
  echo("    <input type=hidden name=cod_pergunta value='".$cod_pergunta."'>\n");

//  echo("    <table border=0 width=100% cellspacing=0>\n");
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
  echo("          <input type=text name=pergunta size=40 maxlength=150 value='".ConverteAspas2HTML($pergunta)."'>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
//  echo("    </table>\n");

  /* Poe o foco no campo pergunta. */
  echo("    <script language=JavaScript>\n\n");
  echo("      document.editar_pergunta.pergunta.focus();\n");
  echo("    </script>\n");

//  echo("    <table border=0 width=100% cellspacing=0>\n");
  echo("      <tr class=\"head\">\n");
  /* 11 - Resposta */
  echo("        <td>".RetornaFraseDaLista($lista_frases, 11)."</td>\n");
  echo("      </tr>\n");

  echo("      <tr>\n");
  echo("        <td>\n");
  echo("          <textarea name=resposta rows=5 cols=60 wrap=soft>".$resposta."</textarea>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");

  echo("    <br>\n");
  echo("    <p>\n");

  echo("    <div align=right width=100%>\n");
  /* 11 - Enviar */
  echo("      <input class=\"input\" type=submit value=".RetornaFraseDaLista($lista_frases_geral,11).">\n");
  /* 2 - Cancelar */
//  echo("      <input class=text type=button onclick='CancelaEdicao();' value=".RetornaFraseDaLista($lista_frases_geral,2).">\n");
  echo("      <input class=\"input\" type=button onclick='history.go(-1);' value=".RetornaFraseDaLista($lista_frases_geral,2).">\n");
  echo("    </div>\n");
  echo("  </form>\n");

  /* Formulario de Cancelamento da Edi�ao da pergunta.        */
  /* Volta para o documento de origem.                        */
  echo("  <form name=frmCancelaEdicao action=".$origem.".php? method=post>\n");

//  echo(RetornaSessionIDInput());
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");


	//  /* Deixa 'check' no formato de string, deseq�encializa e    */
	//  /* coloca no array 'check[]'. Necessario para listar todas  */
	//  /* as perguntas visualizadas.                               */
  //$CurCheck = unserialize(urldecode($check));
  $CurCheck = explode("_",$$cod_pergunta);
  $totalcheck = count($CurCheck);
  for ($j = 0; $j < $totalcheck; $j++)
  {
    echo("    <input type=hidden name=check[] value=".$CurCheck[$j].">\n");
  }
  /* Repassa o $cod_assunto_pai para execu�ao de outras       */
  /* opera�oes como apagar ou editar.                         */
  echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
  /* RePassa a variavel $pagprinc, a qual armazena o nome do documento  */
  /* que estah sendo exibida na pagina principal.                       */
  echo("    <input type=hidden name=pagprinc value=".$pagprinc.">\n");


  echo("  </form>\n");

  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
