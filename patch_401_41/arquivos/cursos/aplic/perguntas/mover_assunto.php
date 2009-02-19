<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/mural/mover_assunto.php

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
  ARQUIVO : cursos/aplic/mural/mover_assunto.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  $cod_usuario = VerificaAutenticacao($cod_curso);

  $sock = Conectar("");

  $lista_frases = RetornaListaDeFrases($sock, 6);
  $lista_frases_geral = RetornaListaDeFrases($sock, -1);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  VerificaAcessoAoCurso($sock, $cod_curso, $cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,6);

  echo("  <html>\n");
  /* 1 - Perguntas Freqüentes */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  echo("\n");

  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  /* 1 - Perguntas Freqüentes */
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 34 - Mover Assunto */
  $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 34)."</b>";
  echo(PreparaCabecalho($cod_curso, $cabecalho, 6,1));
  echo("  <br>\n");


  echo("  <form action=perguntas.php method=post>\n");

  echo(RetornaSessionIDInput());
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");

  echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");

  if (MoveAssunto($sock, $cod_assunto, $cod_assunto_dest))
  {
    /* 35 - Assunto movido com sucesso. */
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases,35)."</font>\n");
    echo("    <p>\n");
    /* 23 - Voltar */
    echo("    <input class=text type=submit value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
  }
  else /* Se o assunto NAO pode ser movido apresenta uma mensagem de erro */
  {
    /* 36 - Erro ao mover o assunto. */
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases, 36)."</font>\n");
    echo("    <p>\n");
   /* 23 - Voltar */
    echo("    <input class=text type=submit value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
  }
  echo("  </form>\n");

  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
