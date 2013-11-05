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
  include("ver_gabarito.inc");

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,1);
  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  
  $dir_exercicio_temp = CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_exercicio, $diretorio_arquivos, $diretorio_temp, "exercicio");
  $dir_questao_temp = CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_questao, $diretorio_arquivos, $diretorio_temp, "questao");
  
  //$dir_name = "exercicios";
  //$dir_item_temp=CriaLinkVisualizar($sock,$dir_name,$cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

  /* acao = Criar Nova Questao - origem = questoes.php */
  if ($acao=="criarQuestao")
  {
    $atualizacao = "true";

    $cod_questao = CriarQuestao($sock, $cod_usuario, $cod_curso,$novo_titulo , $tp_questao);
    if($cod_questao == -1) //erro na criacao! algum parametro da func. esta vazio
    {
      $atualizacao="false";
      Desconectar($sock);
      header("Location:questoes.php?cod_curso=".$cod_curso."&atualizacao=".$atualizacao);
      exit();
    } 

    Desconectar($sock);
    header("Location:editar_questao.php?cod_curso=".$cod_curso."&cod_questao=".$cod_questao."&tp_questao=".$tp_questao."&acao=".$acao."&atualizacao=".$atualizacao);
  }

  if ($acao=="incluirQuestao"){

    if ($cod_questao){

      AdicionaQuestaoAoExercicio($cod_usuario,$cod_curso,$cod_exercicio,$cod_questao);
    }

    header("Location:editar_exercicio.php?cod_curso=".$cod_curso."&cod_exercicio=".$cod_exercicio."&acao=".$acao."&atualizacao=true");

  }

  /* ação = Criar Novo Exercicio - origem = exercicos.php */
  if ($acao=="criarExercicio")
  {
    $atualizacao = "true";

    $cod_exercicio = CriarExercicio($sock, $cod_usuario, $cod_curso, $novo_titulo);

    if($cod_exercicio == -1) //erro na criacao! algum parametro da func. esta vazio
    {
      $atualizacao="false";
      Desconectar($sock);
      header("Location:exercicios.php?cod_curso=".$cod_curso."&atualizacao=".$atualizacao);
      exit();
    } 

    Desconectar($sock);
    header("Location:editar_exercicio.php?cod_curso=".$cod_curso."&cod_exercicio=".$cod_exercicio."&acao=".$acao."&atualizacao=true");
  }

  /* ação = Anexar Arquivo*/
  if ($acao=='anexar'){

    $atualizacao="true";

    /* Verifica a existência do diretório a ser movido o arquivo */
    if (!file_exists($diretorio_arquivos."/".$cod_curso)) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso);
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/exercicios/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/exercicios/");
    }
    if($pasta == 'questao')
    {
      if (!file_exists($diretorio_arquivos."/".$cod_curso."/exercicios/questao/")) {
        CriaDiretorio($diretorio_arquivos."/".$cod_curso."/exercicios/questao/");
      }
      if (!file_exists($diretorio_arquivos."/".$cod_curso."/exercicios/questao/".$cod_questao."/")) {
        CriaDiretorio($diretorio_arquivos."/".$cod_curso."/exercicios/questao/".$cod_questao."/");
      }
    }
    else if($pasta == 'exercicio')
    {
      if (!file_exists($diretorio_arquivos."/".$cod_curso."/exercicios/exercicio/")) {
        CriaDiretorio($diretorio_arquivos."/".$cod_curso."/exercicios/exercicio/");
      }
      if (!file_exists($diretorio_arquivos."/".$cod_curso."/exercicios/exercicio/".$cod_exercicio."/")) {
        CriaDiretorio($diretorio_arquivos."/".$cod_curso."/exercicios/exercicio/".$cod_exercicio."/");
      }
    }

    if($pasta == 'questao')
      $dir=$diretorio_arquivos."/".$cod_curso."/exercicios/questao/".$cod_questao."/".$subpasta;
    else if($pasta == 'exercicio')
      $dir=$diretorio_arquivos."/".$cod_curso."/exercicios/exercicio/".$cod_exercicio."/".$subpasta;

    $nome_arquivo = $_FILES['input_files']['name'];
    //converte o nome para UTF-8, pois o linux insere com essa codificação o arquivo 
    //nas pasta de destino.
    $nome_arquivo = mb_convert_encoding($nome_arquivo, "UTF-8", "ISO-8859-1");

    if (!RealizaUpload($input_files,$dir.$nome_arquivo))
    {
      $atualizacao="false";
    }

    if (($atualizacao == "true")&&($pasta == 'exercicio'))
    {
      // Alterando o status e a data (referente a modifica��o feita = recuperada)
      AtualizaExerciciosModelo($sock, $cod_exercicio);

      // Inserindo altera��o na tabela Exercicios_modelo_historico
      marcaLogExerciciosModeloHistorico($sock, $cod_exercicio, $cod_usuario, 'F');
    }

    //AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
    Desconectar($sock);
    
    if($cod_exercicio!=null){
      header("Location:editar_exercicio.php?cod_curso=".$cod_curso."&cod_exercicio=".$cod_exercicio."&cod_usuario=".$cod_usuario."&acao=".$acao."&atualizacao=".$atualizacao);
    }
    else{
      header("Location:editar_questao.php?cod_curso=".$cod_curso."&cod_questao=".$cod_questao."&acao=".$acao."&atualizacao=".$atualizacao);
    }
  }

  /* ação = Descompactar Arquivo - origem = editar_questao.php */
  else if ($acao=="descompactar")
  {
    if($pasta == 'questao'){
      $dir_tmp=$dir_questao_temp['diretorio'];
    }
    else{
      $dir_tmp=$dir_exercicio_temp['diretorio'];
    }
    $caminho="";

    $tmp=explode("/",$arq);
    for ($c=0;$c<count($tmp)-1;$c++)
      $caminho=$tmp[$c]."/";

    $res=DescompactarArquivoZip($dir_tmp.$arq,$dir_tmp.$caminho);
    $atualizacao="true";
    if(!$res){ 
      $atualizacao="false";
    }else{
      RemoveArquivo($dir_tmp.$arq);
    }
    
    if($cod_exercicio!=null){
      header("Location:editar_exercicio.php?cod_curso=".$cod_curso."&cod_exercicio=".$cod_exercicio."&acao=".$acao."&atualizacao=".$atualizacao);
    }
    else{
      header("Location:editar_questao.php?cod_curso=".$cod_curso."&cod_questao=".$cod_questao."&acao=".$acao."&atualizacao=".$atualizacao);
    }
    Desconectar($sock);
  }
  else if($acao == "apagar")
  {
    /* se a questao ja estiver na lixeira, entao o novo status sera X (excluida) */
    if($lixeira == "ok")
      $status = "X";
    else
      $status = "L";
    EnviarLixeira($cod_curso, $cod_questao, $status);
    header("Location:questoes.php?cod_curso=".$cod_curso."&atualizacao=".$atualizacao."&visualizar=Q");
  } else if($acao == "entregarExercicio"){
    EntregaExercicio($sock, $cod_resolucao,$cod_usuario);

    $cod = RetornaUsuarioPorResolucao($sock, $cod_resolucao);

    if($cod == null){
      $cod = RetornaGrupoPorResolucao($sock, $cod_resolucao);
      header("Location:ver_exercicios.php?cod_curso=".$cod_curso."&cod=".$cod."&acao=".$acao."&atualizacao=true&visualizar=G");
    }
    else
      header("Location:ver_exercicios.php?cod_curso=".$cod_curso."&cod=".$cod."&acao=".$acao."&atualizacao=true");

  }
  else if($acao == 'entregarCorrecao')
  {
    EntregaCorrecao($sock, $cod_curso, $cod_resolucao);
    Desconectar($sock);
    header("Location:exercicio.php?cod_curso=".$cod_curso."&atualizacao=true");
  }
  exit;
?>