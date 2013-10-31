<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/marcar_ferramentas2.php

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
  ARQUIVO : cursos/aplic/administracao/marcar_ferramentas2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_usuario = VerificaAutenticacao($cod_curso);

  $sock = Conectar("");

  /* Obt� todas as ferramentas */
  $lista_ferramentas = RetornaListaFerramentas($sock);
  $ordem_ferramentas = RetornaOrdemFerramentas($sock);

  $total_ferramentas = count($lista_ferramentas);

  $lista_frases=RetornaListaDeFrases($sock,0);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  $lista_frases_ferramentas=RetornaListaDeFrases($sock,-4);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  echo("<html>\n");
  /* 1 - Administra�o */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=\"../teleduc.css\">\n");
  echo("  <link rel=stylesheet TYPE=text/css href=\"administracao.css\">\n");

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
  	/* 1 - Administracao  297 - Area restrita ao formador. */
  	echo("<h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,28)."</h4>\n");
	
    /*Voltar*/
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  	
    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("<form><input class=\"input\" type=button value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");

    Desconectar($sock);
    exit();
  }

  echo("  <body link=#0000ff vlink=#0000ff onLoad='document.frmMarcar.cmdVoltar.focus();'>\n");
  /* 1 - Administra�o */
  $cabecalho = "    <b class=titulo>".RetornaFraseDaLista($lista_frases, 1)."</b>";
  /* 141 - Marcar Ferramentas */
  $cabecalho .= "    <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 141)."</b>";
  echo(PreparaCabecalho($sock,$cod_curso, $cabecalho, 0,5));
  echo("    <br>\n");

  echo("    <form name=frmMarcar method=post action=\"administracao.php\">\n");
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("<input type=hidden name=time value=".time().">\n");


  echo("      <font class=text>\n");
  if (MarcaFerramentas($sock, $ferramentas))
  {
    /* 144 - Opera�o conclu�a com sucesso! */
    echo("        ".RetornaFraseDaLista($lista_frases, 144)."\n");
    echo("<script type=\"text/javascript\" language=\"JavaScript\">parent.menu.location='../menu.php?cod_curso=".$cod_curso."&cod_ferramenta=0';</script>\n");
  }
  else
  {
    /* 143 - Erro ao marcar ferramentas. */
    echo("        ".RetornaFraseDaLista($lista_frases, 145)."\n");
  }
  echo("      </font>\n");
  echo("      <p>\n");
  echo("      <input type=submit name=cmdVoltar class=text value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
  echo("    </form>\n");

  echo("    </body>\n");
  echo("  </html>\n");

  Desconectar($sock);

?>
