<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/acoes.php

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
  ARQUIVO : cursos/aplic/portfolio/acoes.php
  ========================================================== */


  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("portfolio.inc");
  include("avaliacoes_portfolio.inc");

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,15);

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  $dir_item_temp=CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

  /* ação = Anexar Arquivo - origem = ver.php */
  if ($acao=='anexar'){

    $atualizacao="true";

    // Analisa nome do arquivo
    $nome_arquivo = $_FILES['input_files']['name'];

    // Se possuir acentos ou outros caracteres problematicos
    if (VerificaAnexo($nome_arquivo) == 0)
    {
    	// Nao realiza upload de arquivos com acentos
    	$acao = "nomeAnexo";
    	$atualizacao = "false";
    	header("Location:ver.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao=".$acao."&atualizacao=".$atualizacao);
    	exit;
    }
	
    /* Verifica a existência do diretório a ser movido o arquivo */
    if (!file_exists($diretorio_arquivos."/".$cod_curso)) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso);
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/");
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/item/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/item/");
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/item/".$cod_item."/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/item/".$cod_item."/");
    }

    $dir=$diretorio_arquivos."/".$cod_curso."/portfolio/item/".$cod_item."/";

	//converte o nome para UTF-8, pois o linux insere com essa codificação o arquivo 
	//nas pasta de destino.
	//$nome_arquivo = mb_convert_encoding($nome_arquivo, "UTF-8", "ISO-8859-1");
    
    if (!RealizaUpload($input_files,$dir.$nome_arquivo))
    {
      $atualizacao="false";
    }

    AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
  }

  /* ação = Descompactar Arquivo - origem = ver.php */
  else if ($acao=="descompactar")
  {

    $dir_tmp=$dir_item_temp['diretorio'];
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

    AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
  }

  /* ação = Mover Arquivo - origem = ver.php */
  else if ($acao=="mover"){
    MoveArquivoPortfolio($origem, $destino);
    AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
    $atualizacao="true";
  }

  /* ação = Criar Item - origem = portfolio.php */
  else if ($acao=="criarItem"){
    $cod_item=IniciaCriacao ($sock, $cod_topico_raiz, $cod_usuario, $cod_grupo_portfolio, $cod_curso, $diretorio_temp, $novo_nome);
    $atualizacao="true";
    /* Adiciona a Novidade */
    if ($cod_grupo_portfolio == NULL || !isset($cod_grupo_portfolio)){
    	/* Portfolio Individual */
    	AtualizaFerramentasNovaUsuario($sock, "15", $cod_usuario);
    } else {
    	/* Portifolio em Grupo */
    	AtualizaFerramentasNovaGrupo($sock,"15",$cod_grupo_portfolio);
    }
  }

  /* ação = Apagar Item - origem = ver.php */
  else if ($acao=="apagarItem"){
    $atualizacao="true";
    ApagarItem($sock, $cod_item, $cod_usuario);
    Desconectar($sock);
    header("Location:portfolio.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao=".$acao."&atualizacao=".$atualizacao);
  }
    /* ação = Apagar Selecionados - origem = portfolio.php */
  else if ($acao=="apagarSelecionados"){

    $atualizacao="true";

    $cod_topicos_array = explode(",", $cod_topicos);
    $cod_itens_array = explode(",", $cod_itens);

    if ($cod_topicos!=""){
      foreach ($cod_topicos_array as $cod => $linha){
        ApagarTopico($sock, $linha, $cod_usuario);
      }
    }

    if ($cod_itens!=""){
      foreach ($cod_itens_array as $cod => $linha){
        ApagarItem($sock, $linha, $cod_usuario);
      }
    }

    Desconectar($sock);

    header("Location:portfolio.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao=".$acao."&atualizacao=".$atualizacao);
    exit; /* Aparentemente sem motivo, ele passava reto por esse header */
  }

  /* ação = Comentar - origem = comentarios.php */
  else if ($acao=="comentar")
  {

    $atualizacao="true";
  
    $cod_comentario = PegaUltimoCodComentario($sock, $cod_item, $cod_usuario);
  
    InsereComentario ($sock, $cod_comentario, $comentario);
  
    if (!file_exists($diretorio_arquivos."/".$cod_curso))
      CriaDiretorio($diretorio_arquivos."/".$cod_curso);
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/"))
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/");
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/comentario/"))
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/comentario/");
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/portfolio/comentario/".$cod_comentario."/"))
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/portfolio/comentario/".$cod_comentario."/");

    $dir = $diretorio_arquivos."/".$cod_curso."/portfolio/comentario/".$cod_comentario."/";

    $erro=false;
		
	if(is_array($_FILES[input_files]['name'])&&count($_FILES[input_files]['name'])>0)
		foreach($_FILES[input_files]['name'] as $cod => $linha){
	      //$linha = RetiraEspacoEAcentos($linha);
	      $linha = mb_convert_encoding($linha, "UTF-8", "ISO-8859-1");
	      if (!RealizaUpload($_FILES['input_files']['tmp_name'][$cod],$dir.$linha))
	      {
	        $erro=true;
	      }
	    }    

    AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
    
    if($erro){
      $atualizacao="false";
    }
    
    Desconectar($sock);
    header("Location:comentarios.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_usuario=".$cod_usuario."&acao=".$acao."&atualizacao=".$atualizacao);
  }

  Desconectar($sock);

  header("Location:ver.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_usuario=".$cod_usuario."&acao=".$acao."&atualizacao=".$atualizacao);
  exit;
?>
