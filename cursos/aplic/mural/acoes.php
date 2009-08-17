<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/forum/acoes.php

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
  ARQUIVO : cursos/aplic/forum/acoes.php
  ========================================================== */
  
  
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("mural.inc");

  $cod_ferramenta=8;
  $cod_usuario_global=VerificaAutenticacao($cod_curso);
  

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,9);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  if($acao=='apagarMuralAtual'){
      if($usr_formador){
	      $query = "update Mural set status='X' where cod_mural =".VerificaNumeroQuery($cod_mural);
	      Enviar($sock, $query);
	      $atualizacao="true";
	      Desconectar($sock);
	      header("Location:mural.php?cod_curso=".$cod_curso."&pag_atual=".$pag_atual."&ordem=".$ordem."&todas_abertas=".$todas_abertas."&acao=apagarMuralAtual&atualizacao=".$atualizacao);
	      exit;
      }
  }
  else if ($acao=='nova_msg'){
    $msg_titulo=ConverteAspas2BarraAspas($msg_titulo);
    $msg_corpo=ConverteAspas2BarraAspas($msg_corpo);
      $atualizacao="true";
    if (!SalvaMensagemMural($sock, $cod_curso, $cod_usuario, $msg_titulo, $msg_corpo, $status_curso)){
      /* 18 - Erro na composi�o da mensagem. */
      $atualizacao="false";
    }
    ($usr_formador) ? ($tipo_usuario = 'F') : ($tipo_usuario = 'T');
    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
    Desconectar($sock);
    header("Location:mural.php?cod_curso=".$cod_curso."&ordem=".$ordem."&todas_abertas=".$todas_abertas."&acao=nova_msg&atualizacao=".$atualizacao);
    exit;
  }else if($acao=='apagarMural'){
    if($usr_formador){
      $atualizacao="false";
      if($elementos){
        $elementos = explode(",", $elementos);
  
        foreach($elementos as $chave => $valor){
          $query = "update Mural set status='X' where cod_mural =".VerificaNumeroQuery($valor);
          Enviar($sock, $query);
        }
        $atualizacao="true";
      }
      Desconectar($sock);
      header("Location:mural.php?cod_curso=".$cod_curso."&pag_atual=".$pag_atual."&ordem=".$ordem."&todas_abertas=".$todas_abertas."&acao=apagarMural&atualizacao=".$atualizacao);
      exit;
    }
  }



  header("Location:mural.php?cod_curso=".$cod_curso);
  
  exit;
?>