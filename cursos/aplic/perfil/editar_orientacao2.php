<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perfil/editar_orientacao2.php

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
  ARQUIVO : cursos/aplic/perfil/editar_orientacao2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perfil.inc");

  $cod_ferramenta=13;
  
  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");
  $lista_frases=RetornaListaDeFrases($sock,13);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,13);

  $eformador   = EFormador ($sock, $cod_curso, $cod_usuario);


  if (!$eformador)
  {
  
    $cod_ferramenta_ajuda = $cod_ferramenta;
 
    $cod_pagina_ajuda=1;
    include("../topo_tela.php");
    include("../menu_principal.php");
    echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    /* 1 - Perfil */
    $cabecalho = "<h4>".RetornaFraseDaLista($lista_frases, 1);
    /* 130 - �ea restrita a formadores */
    $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 130)."</h4>";
    echo("          ".$cabecalho);
    echo("        </td>\n");
    echo("      </tr>\n");
    include("../tela2.php");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();    
  }
    

  $nova_orientacao=EliminaScript($nova_orientacao); 
  $nova_orientacao=LimpaConteudo($nova_orientacao);
  /* se ja existe, update, senao insert */
  $orientacao_perfil=RetornaOrientacaoPerfil($sock, $cod_lingua);

  if (ExisteOrientacao($sock, $cod_lingua)) 
  {
    /* Ja existe orientacao; proceder update */
    AtualizaOrientacao($sock,$nova_orientacao, $cod_lingua);
    $confirma='true';
  } 
  else 
  {
    /* Nao existe orientacao; proceder insert */
    InsereOrientacao($sock,$nova_orientacao, $cod_lingua);
    $confirma='false';
    /* 51 - Orienta�o inserida com sucesso */
  }

  header("Location:perfil.php?cod_curso=".$cod_curso."&acao=enviouOrientacao&atualizacao=".$confirma."");

  Desconectar($sock);

?>
