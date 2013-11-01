<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/batepapo.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/batepapo/batepapo.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;

  $bibliotecas="../bibliotecas/";
  include("../menu.inc");

  $cod_curso = $_GET['cod_curso'];
  $cod_usuario_global=VerificaAutenticacao($cod_curso);
  $sock=Conectar("");

  $lista_frases_menu=RetornaListaDeFrases($sock,-4);

  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);

  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  $tela_ordem_ferramentas=RetornaOrdemFerramentas($sock);
  $tela_lista_ferramentas=RetornaListaFerramentas($sock);
  $tela_lista_titulos=RetornaListaTitulos($sock, $_SESSION['cod_lingua_s']);
  $tela_email_suporte=RetornaConfiguracao($sock,"adm_email");

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $tela_raiz_www = $linha[0];

  $tela_host=RetornaConfiguracao($sock,"host");

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  $tela_formador      = EFormador($sock,$cod_curso,$cod_usuario);
  $tela_formadormesmo = EFormadorMesmo($sock,$cod_curso,$cod_usuario);

  // booleano, indica se usuario eh colaborador
  $tela_colaborador   = EColaborador($sock, $cod_curso, $cod_usuario);
  // booleano, indica se usuario eh visitante
  $tela_visitante     = EVisitante($sock, $cod_curso, $cod_usuario);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,0);
  MarcaAcesso($sock,$cod_usuario,$cod_ferramenta);

  /* Encerra sessão anterior, se não tiver ninguém online e se a sessão
   * anterior não for uma sessão marcada previamente (e portanto tem uma
   * hora marcada para acabar).
   */
  $cod_sessao     = RetornaSessaoCorrente($sock);
  $sessao_marcada = RetornaListaSessoesMarcadas($sock);

  if (VerificaRetiradaOnline($sock))
  {
    LimpaOnline($sock,$cod_curso, 90);
  }

  if (!VerificaOnline($sock) && empty($sessao_marcada))
  {
    /* Todas as pessoas foram retiradas. Encerramos a sessao entï¿½o */
    EncerraSessao($sock,$cod_curso,$cod_sessao);
    $cod_sessao=RetornaSessaoCorrente($sock);
  }

  Desconectar($sock);
  header("Location:ver_sessoes_realizadas.php?&cod_curso=".$cod_curso);
?>