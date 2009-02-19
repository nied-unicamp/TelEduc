<?

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/inserir_assunto.php

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
  ARQUIVO : cursos/aplic/perguntas/inserir_assunto.php
  ========================================================== */


  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,6);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock, $cod_curso, $cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,6);

  echo("  <html>\n");
  /* 1 - Perguntas Freqüentes */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");

  echo("<script language=JavaScript>\n\n");

  echo("  function CancelaAssunto()\n");
  echo("  {\n");
  echo("    document.location.href=\"perguntas.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."&cod_assunto_pai=".$cod_assunto_pai."&".time()."\";\n");
  echo("    return(true);\n");
  echo("  }\n\n");

  echo("  function testa_nome()\n");
  echo("  {\n");
  /* Elimina os espaços para verificar se o titulo nao eh formado por apenas espaços */
  echo("    assunto_nome = document.inserir_assunto.nome.value;\n");
  echo("    while (assunto_nome.search(\" \") != -1)\n");
  echo("    {\n");
  echo("      assunto_nome = assunto_nome.replace(/ /, \"\");\n");
  echo("    }\n");

  echo("    if (assunto_nome == '')\n");
  echo("    {\n");
  /* 7 - O assunto deve ter um nome. */
  echo("      alert('".RetornaFraseDaLista($lista_frases, 7)."');\n");
  echo("      return(false);\n");
  echo("    } else {\n");
  echo("      return(true);\n");
  echo("    }\n");
  echo("  }\n\n");

  echo("\n</script>\n\n");

  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  /* 1 - Perguntas Freqüentes */
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 2 - Inserir Assunto */
  $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,2)."</b>";
  echo(PreparaCabecalho($cod_curso, $cabecalho, 6,2));

  /* Obtem e exibe o caminho onde a pergunta sera inserida */
  echo("  <div><img src=../figuras/assuntoab.gif>\n");
  echo("    <font class=text>".RetornaCaminhoAssunto($sock, $cod_assunto_pai));
  echo("    </font>\n");
  echo("  </div>\n");
  echo("  <br>\n");

  echo("  <form name=inserir_assunto method=post action=inserir_assunto2.php onsubmit='return(testa_nome());'>\n");

  echo(RetornaSessionIDInput());
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");


  echo("    <input type=hidden name=cod_assunto_pai value='".$cod_assunto_pai."'>\n");

  echo("    <table border=0 width=100% cellspacing=0>\n");
  echo("      <tr class=colorfield>\n");
  /* 5 - Nome */
  echo("        <td class=colorfield>".RetornaFraseDaLista($lista_frases,5)."</td>\n");
  echo("      </tr>\n");

  echo("      <tr>\n");
  echo("        <td>\n");
  echo("          <input class=text type=text name=nome size=40 maxlength=150 value='".$nome."'>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");

  /* Poe o foco no campo nome. */
  echo("    <script language=JavaScript>\n\n");
  echo("      document.inserir_assunto.nome.focus();\n");
  echo("    </script>\n");


  echo("    <table border=0 width=100% cellspacing=0>\n");
  echo("      <tr class=colorfield>\n");
  /* 6 - Descrição */
  echo("        <td class=colorfield>".RetornaFraseDaLista($lista_frases, 6)."</td>\n");
  echo("      </tr>\n");

  echo("      <tr>\n");
  echo("        <td>\n");
  echo("          <textarea name=descricao class=text rows=5 cols=60 wrap=soft>".$descricao."</textarea>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("    <br>\n");
  echo("    <p>\n");

  echo("    <div align=right width=100%>\n");
  /* 11 - Enviar */
  echo("      <input type=submit value=".RetornaFraseDaLista($lista_frases_geral,11).">\n");
  /* 2 - Cancelar */
  echo("      <input type=button onclick='CancelaAssunto();' value=".RetornaFraseDaLista($lista_frases_geral,2).">\n");
  echo("    </div>\n");
  echo("  </form>\n");

  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);


?>
