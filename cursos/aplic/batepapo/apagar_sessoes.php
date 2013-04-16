<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/ver_sessoes_realizadas.php

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
  ARQUIVO : cursos/aplic/batepapo/ver_sessoes_realizadas.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");
  include("avaliacoes_batepapo.inc");


  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10);

  echo("<html>\n");
  /* 1 - Bate-papo */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=batepapo.css>\n");

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  /* 1 - Bate-Papo */
  $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";

  /* 83 - Apagar sess�es */
  /* 84 - Recuperar sess�es */

  if ($acao=='A')
  {
    $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,84)."</b>";
    $cod_pagina=4;
  }
  else
  {
    $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,83)."</b>";
    $cod_pagina=5;
  }

  echo(PreparaCabecalho($sock,$cod_curso,$cabecalho,10,$cod_pagina));

  echo("<br>\n");
  echo("<p>\n");

  if (count($cod_sessao_apagar)>0)
  {
    AtualizaStatusSessao ($sock,$cod_sessao_apagar,$acao);
    AtualizaStatusAvaliacao($sock,$cod_sessao_apagar,$acao,$cod_usuario);

    if ($acao=='A')
    {
      /* 82 - Sess�es recuperadas com sucesso */
      echo("<font class=text><b>".RetornaFraseDaLista($lista_frases,82)."</b></font><br>\n");
    }
    else
    {
      /* 76 - Sess�es apagadas com sucesso. */
      echo("<font class=text><b>".RetornaFraseDaLista($lista_frases,76)."</b></font><br>\n");
    }
  }
  else
    /* 77 - Nenhuma sess�o foi selecionada.*/
    echo("<font class=text>".RetornaFraseDaLista($lista_frases,77)."</font><br>\n");

  echo("<form>\n");
  /* 23 - Voltar */
  echo("<input type=button value='".RetornaFraseDaLista($lista_frases_geral,23)."' onClick='document.location=\"ver_sessoes_realizadas.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&lixeira=".$lixeira."\";'>\n");
  echo("</form>\n");

  Desconectar($sock);
  echo("</body></html>\n");
?>