<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/acoes.php

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
  ARQUIVO : cursos/aplic/avaliacoes/acoes.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_ferramenta=22;

  $cod_curso = $_GET["cod_curso"]; /* Por padrao se usa GET */
  if ($cod_curso == NULL) $cod_curso = $_POST["cod_curso"]; /* Ao passar a norma e a expressão usa-se POST */

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock = Conectar("");
  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);  

  $lista_frases=RetornaListaDeFrases($sock,22);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $tabela = "Avaliacao";

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  $sock=MudarDB($sock, $cod_curso); 
  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);


  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
    Desconectar($sock);
    exit();
  }

  $msgErro = "";

  // acao da pagina "avaliacoes.php"

if ($acao == "criarAvaliacaoExt") {
  $cod_atividade = RetornaProximoCodigoExterna($sock,'N');
  $cod_avaliacao = IniciaCriacaoAvaliacao($sock, $tabela,$cod_atividade, $cod_usuario, 'N', $tipo);
  AtualizaFerramentasNova($sock,22,'T');
  GravaResgistroAvaliacaoExterna($sock,$novo_titulo,$tipo);

  Desconectar($sock);
  header("Location:ver.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=22&cod_avaliacao=".$cod_avaliacao."&tela_avaliacao=".$tela_avaliacao."&acao=criarAvaliacao&atualizacao=true");
}
  // acao da pagina "ver.php"
  if($acao == "excluirAvaliacao")
  {
    if (!ExcluiAvaliacao($sock, $cod_avaliacao,$cod_usuario))
    {
      /* 83 - Erro ao se excluir a avalia��o. */
      Desconectar($sock);
      header("Location:avaliacoes.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=22&tela_avaliacao=".$tela_avaliacao."&acao=excluirAvaliacao&atualizacao=false");
    }
    else
    {
      Desconectar($sock);
      header("Location:avaliacoes.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=22&tela_avaliacao=".$tela_avaliacao."&acao=excluirAvaliacao&atualizacao=true"); 
    }
  }

  Desconectar($sock);

  exit();

