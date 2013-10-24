<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/acoes_linha.php

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
  ARQUIVO : cursos/aplic/portfolio/acoes_linha.php
  ========================================================== */


  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."importar.inc");
  include("agenda.inc");

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

  /* ação = Criar Nova Agenda - origem = ver_editar.php */
  if ($acao=="criarAgenda")
  {
    $atualizacao = "true";

    $cod_item = IniciaCriacao($sock, $cod_usuario, $cod_curso, $diretorio_temp, $novo_titulo, $novo_texto);
    if($cod_item == -1) //erro na criacao! algum parametro da func. esta vazio
    {
      $atualizacao="false";
      Desconectar($sock);
      header("Location:agenda.php?cod_curso=".$cod_curso."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
      exit();
    } 

    Desconectar($sock);
    header("Location:ver_linha.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
  }
  
  /* ação = Apagar item - origem = ver_linha.php */
  if ($acao=="apagarItem")
  {
    $atualizacao = "true";
    ApagarItem($sock,$cod_item,$cod_curso,$cod_usuario,$diretorio_arquivos,$diretorio_temp);

    Desconectar($sock);
    header("Location:".$origem.".php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
  }

  /* ação = Apagar item(s) selecionados - origem = ver_editar.php */
  if ($acao=="apagarSelecionados")
  {
    $atualizacao = "true";

    $cod_itens_array = explode(",", $cod_itens);

    if ($cod_itens!=""){
      foreach ($cod_itens_array as $cod => $linha){
        ApagarItem($sock,$linha,$cod_curso,$cod_usuario,$diretorio_arquivos,$diretorio_temp);
      }
    }else{
      $atualizacao = "false";
    }
 
    Desconectar($sock);
    header("Location:".$origem.".php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
  }

  /* ação = Ativar agenda - origem = ver_editar.php */
  if ($acao=="ativaragenda")
  {
      $atualizacao = "true";
      AtivarAgenda($sock, $cod_item, $cod_usuario);

      Desconectar($sock);
      header("Location:agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario);
  }

  /* ação = Arquivo - origem = ver_linha.php */
  if ($acao=='anexar'){

    $atualizacao = "true";
    
    // Analisa nome do arquivo
    $nome_arquivo = $_FILES['input_files']['name'];

    // Se possuir acentos ou outros caracteres problematicos
    if (VerificaAnexo($nome_arquivo) == 0)
    {
      // Nao realiza upload de arquivos com acentos
      $acao = "nomeAnexo";
      $atualizacao = "false";
      header("Location:ver_linha.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
      exit;
    }
    
    /* Verifica a existência do diretório a ser movido o arquivo */
    if (!file_exists($diretorio_arquivos."/".$cod_curso)) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso);
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/agenda/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/agenda/");
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/agenda/".$cod_item."/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/agenda/".$cod_item."/");
    }

    $dir=$diretorio_arquivos."/".$cod_curso."/agenda/".$cod_item."/";

    if (!RealizaUpload($input_files,$dir.$nome_arquivo))
    {
      /* 50 - Atenção: o arquivo que você anexou não existe ou tem mais de %dMb. Se você digitou o nome do arquivo, procure certificar-se que ele esteja correto ou então selecione o arquivo a partir do botão Procurar (ou Browse). */
	 $atualizacao = "false";
    }

    AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
    header("Location:ver_linha.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
  }

  /* ação = Descompactar Arquivo - origem = ver_linha.php */
  else if ($acao=="descompactar")
  {

    $atualizacao = "true";

    $dir_tmp=$dir_item_temp['diretorio'];
    $caminho="";

    $tmp=explode("/",$arq);
    for ($c=0;$c<count($tmp)-1;$c++)
      $caminho=$tmp[$c]."/";

    if(!($res=DescompactarArquivoZip($dir_tmp.$arq,$dir_tmp.$caminho))){
      $atualizacao = "false";
    }else{
      RemoveArquivo($dir_tmp.$arq);
    }

    /*Define o status de todos os arquivos descompactados como false para que nenhum deles seja consiredo como 
      arquivo de entrada */
    $dir = RetornaArrayDiretorio($dir_tmp.$caminho);
    foreach ($dir as $cod => $linha)
      AlteraStatusArquivo($dir_tmp.$caminho.ConverteUrl2Html($linha['Diretorio']."/".$linha['Arquivo']),false);
      	
    AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
    Desconectar($sock);
    header("Location:ver_linha.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
  }
   else if ($acao == "validarImportacao"){
 	$sock = MudarDB($sock, "");
 	
 	$cod_cursos = explode(";", $cod_curso_todos);
  	$tipo_curso_origem = $cod_cursos[0]; // B = Base, E = Extra�do
  	$cod_curso_origem = $cod_cursos[1];
  	
  	
  	$_SESSION['cod_topico_destino'] = $cod_topico_raiz;
  	$_SESSION['cod_curso_origem'] = $cod_curso_origem;
  	$_SESSION['flag_curso_extraido'] = ($tipo_curso_origem == 'E');
  	
  	
  	if($cod_curso_origem)
  	{
		$cod_usuario_import = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso_origem);
	
		if ( FerramentaEstaCompartilhada($sock, $cod_curso_origem, $cod_ferramenta) ){
			$_SESSION['flag_curso_compartilhado'] = TRUE;
			header("Location:importar_agenda.php?cod_curso=".$cod_curso."&cod_assunto_pai=1&cod_curso_origem=".$cod_curso_origem);
		} else if ( $cod_usuario_import != NULL && EFormadorMesmo($sock,$cod_curso_origem,$cod_usuario_import) ){
			$_SESSION['flag_curso_compartilhado'] = FALSE;
		 	header("Location:importar_agenda.php?cod_curso=".$cod_curso."&cod_assunto_pai=1&cod_curso_origem=".$cod_curso_origem);
		} else {
			header("Location:importar_curso.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&acao=".$acao."&atualizacao=false");
		}
  	}
  	else
  		header("Location:importar_curso.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&acao=ErroImportacao&atualizacao=false");
  	
  } else if ($acao == "importarItem"){
  	
  	$cod_curso_destino = $cod_curso;
  	$cod_topico_destino = $_SESSION['cod_topico_destino'];
  	$cod_usuario;
  	$cod_curso_origem = $_SESSION['cod_curso_origem'];
  	$flag_curso_extraido = $_SESSION['flag_curso_extraido'];
  	$flag_curso_compartilhado = $_SESSION['flag_curso_compartilhado'];
  	$array_topicos_origem = $cod_assunto;
  	$array_itens_origem = $cod_pergunta;
  	$dirname = "agenda";
  	$nome_tabela = "Agenda";
  	
  	$sock=Conectar("");
  	if ($curso_extraido)
		$diretorio_arquivos_origem = RetornaDiretorio($sock, 'Montagem');
	else
		$diretorio_arquivos_origem = RetornaDiretorio($sock, 'Arquivos');

	// Raiz do diret�rio de arquivos do curso PARA O QUAL ser�o importados
	// os itens.
	$diretorio_arquivos_destino = RetornaDiretorio($sock, 'Arquivos');
	$diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');

  	ImportarAgenda($cod_usuario, $cod_curso_destino, $cod_itens_import, $cod_curso_origem, $diretorio_arquivos_origem, $diretorio_arquivos_destino, $diretorio_temp);
    
  	header("Location:ver_editar.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=".$cod_topico_destino."&acao=".$acao."&atualizacao=true");
  }
  

  Desconectar($sock);
  exit;
?>