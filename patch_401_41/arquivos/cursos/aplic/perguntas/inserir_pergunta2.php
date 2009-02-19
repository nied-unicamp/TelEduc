<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/inserir_pergunta2.php

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
  ARQUIVO : cursos/aplic/mural/inserir_pergunta2.php
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
  // 1 - Perguntas Freq�entes 
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  echo("\n");

  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  // 1 - Perguntas Freq�entes
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  // 3 - Inserir Pergunta 
  $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,3)."</b>";
//  echo(PreparaCabecalho($cod_curso, $cabecalho, 6,3));
  echo("  <br>\n");


  // Se a pergunta for inserida exibe uma mensagem confirmando o sucesso 
  // e um botao para voltar aa pagina principal.                        
  if (SalvaPergunta($sock, $cod_assunto_pai, $pergunta, $resposta))
  {
    AtualizaFerramentasNova($sock,6,'T');
  	header("Location:perguntas.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=".$cod_assunto_pai."&acao=".$acao."&atualizacao=true");

    echo("  <form action=perguntas.php method=post>\n");

    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("<input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");

    // 14 - Pergunta inserida com sucesso. 
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases,14)."</font>\n");
    echo("    <p>\n");
    // 23 - Voltar 
    echo("    <input type=submit value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
    echo("  </form>\n");

  } // Se a pergunta NAO pode ser inserida apresenta uma mensagem de erro 
    // e oferece a possibilidade de edita-la.                            
  else
  {
    echo("  <form name=voltar action=inserir_pergunta.php method=post>\n");

    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("<input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");

    // 15 - Erro na inser��o da pergunta. 
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases,15)."</font>\n");
    echo("    <p>\n");
    echo("    <input type=hidden name=pergunta value='".$pergunta."'>\n");
    echo("    <input type=hidden name=resposta value='".$resposta."'>\n");
    // 23 - Voltar 
    echo("    <input type=submit value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
    echo("  </form>\n");
  }

  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
