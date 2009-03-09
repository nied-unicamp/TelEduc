<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/exercicios/acoes.php

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
  ARQUIVO : cursos/aplic/exercicios/acoes.php
  ========================================================== */


  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("exercicios.inc");

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,1);
  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  $dir_name = "agenda";
  $dir_item_temp=CriaLinkVisualizar($sock,$dir_name,$cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

  /* ação = Criar Nova Questao - origem = questoes.php */
  if ($acao=="criarQuestao")
  {
    $atualizacao = "true";

    $cod_questao = CriarQuestao($sock, $cod_curso, $cod_usuario, $novo_titulo, $tp_questao);
    if($cod_questao == -1) //erro na criacao! algum parametro da func. esta vazio
    {
      $atualizacao="false";
      Desconectar($sock);
      header("Location:questoes.php?cod_curso=".$cod_curso."&atualizacao=".$atualizacao);
      exit();
    } 

    Desconectar($sock);
    header("Location:editar_questao.php?cod_curso=".$cod_curso."&cod_questao=".$cod_questao."&atualizacao=".$atualizacao);
  }
  
  /* ação = Anexar Arquivo - origem = ver.php */
  if ($acao=='anexar'){

    $atualizacao="true";

    /* Verifica a existência do diretório a ser movido o arquivo */
    if (!file_exists($diretorio_arquivos."/".$cod_curso)) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso);
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/exercicios/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/exercicios/");
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/exercicios/questao/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/exercicios/questao/");
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/exercicios/questao/".$cod_questao."/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/exercicios/questao/".$cod_questao."/");
    }

    $dir=$diretorio_arquivos."/".$cod_curso."/exercicios/questao/".$cod_questao."/";

    $nome_arquivo = $_FILES['input_files']['name'];
    //converte o nome para UTF-8, pois o linux insere com essa codificação o arquivo 
    //nas pasta de destino.
    $nome_arquivo = mb_convert_encoding($nome_arquivo, "UTF-8", "ISO-8859-1");
    
    if (!RealizaUpload($input_files,$dir.$nome_arquivo))
    {
      $atualizacao="false";
    }

    //AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
  }

  Desconectar($sock);
  exit;
?>