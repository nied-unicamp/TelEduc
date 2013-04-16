<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/index_sala.php

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
  ARQUIVO : cursos/aplic/batepapo/index_sala.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  /* topo_tela.php faz isso
  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
 
  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10); */

  /* Colocar Online */

  $cod_sessao=RetornaSessaoCorrente($sock);

  ManterOnline($sock,$cod_usuario);

  /* Cadastrar Apelido, sen�o tiver ningu�m com o mesmo... */
  $lista=RetornaListaApelidosOnline($sock,$cod_sessao);

  $apelido=(LimpaTitulo($apelido));

  if (count($lista)>0)  
    foreach($lista as $cod => $linha)
      if ($linha==$apelido && $cod!=$cod_usuario)
      {
        /* Se j� tem algu�m na sala com o nick escolhido, impede a entrada */

        echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
        /* 1 - Bate-Papo */
        echo("<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n");
        /* 2 - Entrar na sala de bate-papo */
        echo("<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,2)."</b>\n");
        echo("<br>\n");
        echo("<p>\n");

        /* 25 - Infelizmente, este apelido (ou nome) j� se encontra em uso por outra pessoa. */
        /* 26 - Por favor, pressione "Voltar" e tente outro apelido. */
        echo("<font class=text>".RetornaFraseDaLista($lista_frases,25)."</font><p>\n");
        echo("<font class=text>".RetornaFraseDaLista($lista_frases,26)."</font><p>\n");
        
        echo("<form>\n");
        /* 23 - Voltar (ger) */
        echo("<input type=button value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1); />\n");
        echo("</form>\n");

        Desconectar($sock);
        exit();
      }
  CadastrarApelido($sock,$cod_usuario,$cod_sessao,$apelido);

  $fala=RetornaFraseDaLista($lista_frases,'7');
  
  $tempo = time()-30;
					      
  /* Enviar mensagem de entrada na sala */
  InsereConversa($sock,$cod_curso,$cod_sessao,$cod_usuario,"",1,$fala,"");
  
//   global $dbnamebase;
  $dbnamebase = $_SESSION['dbnamebase'];


  echo("<frameset rows=\"50,*,175,0\" border=1>\n");
  echo("  <frame name=assunto src=\"sala_assunto.php?cod_curso=".$cod_curso."\" NORESIZE SCROLLING=no>\n");
  echo("    <frameset cols=\"*,175\" border=\"0\">\n");
  echo("      <frame name=meio src=\"sala_meio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_sessao=".$cod_sessao."&tempo=".(time() - 30)."&cod_lingua=".$cod_lingua_s."&db=".$dbnamebase."\" NORESIZE SCROLLING=auto>\n");
  echo("      <frame name=lateral src=\"sala_nicks.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_sessao=".$cod_sessao."&cod_lingua=".$cod_lingua_s."&db=".$dbnamebase."\" NORESIZE SCROLLING=auto>\n");
  echo("    </frameset>\n");
  echo("  <frame name=base src=\"sala_base.php?&cod_curso=".$cod_curso."&scrollbox=sim&cod_usuario=".$cod_usuario."&cod_sessao=".$cod_sessao."&tempo=".(time() - 30)."&cod_lingua=".$cod_lingua_s."&db=".$dbnamebase."\" NORESIZE SCROLLING=yes>\n");
  echo("  <frame name=reload src=\"sala_reload.php?&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&tempo=".$tempo."\" NORESIZE SCROLLING=no>\n");
  echo("</frameset>\n");

  Desconectar($sock);

?>
