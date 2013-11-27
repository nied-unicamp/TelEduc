<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/diario/acoes.php

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

    Nied - Ncleo de Informática Aplicada à Educação
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
  ARQUIVO : cursos/aplic/diario/acoes.php
  ========================================================== */


  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("diario.inc");

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,15);
  $cod_ferramenta = 14;
  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  $usr_visitante = EVisitante($sock, $cod_curso, $cod_usuario);

  /* A��o = Comentar - origem = comentarios.php */
  if ($acao=="comentar")
  {
    $atualizacao="false";
    if(ComentaItem($sock, $cod_item, LimpaConteudo($comentario), $cod_usuario)){
      $atualizacao="true";
    }
    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
    header("Location:comentarios.php?&cod_curso=".$cod_curso."&cod_item=".$cod_item."&cos_usuario=".$cod_usuario."&acao=".$acao."&atualizacao=".$atualizacao);
    Desconectar($sock);
  }

  /* A��o = Novo Item - origem = diario.php */
  if ($acao=="novo_item" && !$usr_visitante)
  {
    $atualizacao="false";
    if($cod_item = SalvaItem($sock, $titulo, $texto, $cod_usuario, $tipo_compartilhamento)){
      $atualizacao="true";
    }
    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
    header("Location:ver_item.php?&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_item=".$cod_item."&cod_propriet=".$cod_propriet."&origem=diario&acao=".$acao."&atualizacao=".$atualizacao);
    Desconectar($sock);
  }

  /* A��o = Apagar Item - origem = ver_item.php */
  if ($acao=="apagarItem")
  {

    $atualizacao="false";
    if(ApagaItem($sock, $cod_item)){
      $atualizacao="true";
    }
    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
    header("Location:diario.php?&cod_curso=".$cod_curso."&cod_item=".$cod_item."&cos_usuario=".$cod_usuario."&acao=".$acao."&atualizacao=".$atualizacao);
    Desconectar($sock);
  }

?>