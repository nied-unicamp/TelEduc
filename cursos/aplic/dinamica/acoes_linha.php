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
  include("dinamica.inc");

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,16);

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
 
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  
  $dir_name = "dinamica";
  $linha_item = RetornaDadosDinamica($sock);
  $cod_item = $linha_item['cod_dinamica'];
  $dir_item_temp=CriaLinkVisualizar($sock,$dir_name,$cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

  /* ação = Anexar Arquivo */
  if ($acao=='anexar')
  {

    $atualizacao="true";
    AtualizaFerramentasNova($sock, 16, 'T');
    /* Verifica a existência do diretório a ser movido o arquivo */
    if (!file_exists($diretorio_arquivos."/".$cod_curso)) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso);
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/dinamica/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/dinamica/");
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/dinamica/".$cod_item."/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/dinamica/".$cod_item."/");
    }

    $dir=$diretorio_arquivos."/".$cod_curso."/dinamica/".$cod_item."/";

    $nome_arquivo = $_FILES['input_files']['name'];
    if (function_exists('mb_convert_encoding'))
      $nome_arquivo = mb_convert_encoding($nome_arquivo, "UTF-8", "ISO-8859-1");

    if (!RealizaUpload($input_files,$dir.$nome_arquivo))
    {
      /* 52 - Atenção: o arquivo que você anexou não existe ou tem mais de %dMb.*/
      $atualizacao="false";
    }
    Desconectar($sock);
    header("Location:editar_dinam.php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
  }

  /* ação = Descompactar Arquivo */
  else if ($acao=="descompactar")
  {

    $dir_tmp=$dir_item_temp['diretorio'];
    $caminho="";

    $tmp=explode("/",$arq);
    for ($c=0;$c<count($tmp)-1;$c++)
      $caminho=$tmp[$c]."/";

    /*Guarda o arquivo de entrada antigo */
    $dir = RetornaArrayDiretorio($dir_tmp.$caminho);

    foreach ($dir as $cod => $linha){
      if (($linha['Status']) && ($linha['Arquivo']!="")){
        $arquivo_entrada = $dir_tmp.$caminho.ConverteUrl2Html($linha['Diretorio']."/".$linha['Arquivo']);
        break;
      }
    }

    $res=DescompactarArquivoZip($dir_tmp.$arq,$dir_tmp.$caminho);

    $atualizacao="false";
    if($res){
      $atualizacao="true";
      RemoveArquivo($dir_tmp.$arq);
    }

    /*Define o status de todos os arquivos descompactados como false para que nenhum deles seja consiredo como 
      arquivo de entrada */
    $dir = RetornaArrayDiretorio($dir_tmp.$caminho);

    foreach ($dir as $cod => $linha){
      AlteraStatusArquivo($dir_tmp.$caminho.ConverteUrl2Html($linha['Diretorio']."/".$linha['Arquivo']),false);
    }

    AlteraStatusArquivo($arquivo_entrada,true);

    AcabaEdicao($sock, $cod_item);

    Desconectar($sock);
    header("Location:editar_dinam.php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
  } else if ($acao == "validarImportacao"){
    $sock = MudarDB($sock, "");

    $array = explode(";", $cod_curso_todos);

    $tipo_curso_origem = $array[0]; 
    $cod_curso_origem = $array[1];
    $tipo_curso = $_GET['tipo_curso'];
    $cod_categoria = $_GET['cod_categoria'];
    //var_dump($lista_frases);

    if($cod_curso_origem != NULL){
      $_SESSION['cod_topico_destino'] = $cod_topico_raiz;
      $_SESSION['cod_curso_origem'] = $cod_curso_origem;
      $_SESSION['flag_curso_extraido'] = ($tipo_curso_origem == 'E');
      $cod_usuario_import = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso_origem);
      
      if ( FerramentaEstaCompartilhada($sock, $cod_curso_origem, $cod_ferramenta) ){
        $_SESSION['flag_curso_compartilhado'] = TRUE;
        header("Location:importar_dinamica.php?cod_curso=".$cod_curso."&cod_assunto_pai=1&cod_curso_origem=".$cod_curso_origem);
      } else if ( $cod_usuario_import != NULL && EFormadorMesmo($sock,$cod_curso_origem,$cod_usuario_import) ){
        $_SESSION['flag_curso_compartilhado'] = FALSE;
        header("Location:importar_dinamica.php?cod_curso=".$cod_curso."&cod_assunto_pai=1&cod_curso_origem=".$cod_curso_origem);
      } else {
        header("Location:importar_curso.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&acao=".$acao."&atualizacao=false");
      }
    }else{
      header("Location:importar_curso.php?cod_curso=".$cod_curso."&acao=".$acao."&tipo_curso=".$tipo_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_ferramenta=".$cod_ferramenta."&cod_categoria=".$cod_categoria."&acao_feedback=FalhaImportacao&atualizacao=false");
    }
  
  } else if ($acao == "importarItem"){

    $cod_curso_destino = $cod_curso;
    $cod_topico_destino = $_SESSION['cod_topico_destino'];
    $cod_usuario;
    $cod_curso_origem = $_SESSION['cod_curso_origem'];
    $flag_curso_extraido = $_SESSION['flag_curso_extraido'];
    $flag_curso_compartilhado = $_SESSION['flag_curso_compartilhado'];
    $array_topicos_origem = $cod_assunto;
    $array_itens_origem = $cod_pergunta;
    $dir = "dinamica";
    $tabela = "Dinamica";

    $sock=Conectar("");
    if ($curso_extraido)
      $diretorio_arquivos_origem = RetornaDiretorio($sock, 'Montagem');
    else
      $diretorio_arquivos_origem = RetornaDiretorio($sock, 'Arquivos');

    // Raiz do diret�rio de arquivos do curso PARA O QUAL ser�o importados
    // os itens.
    $diretorio_arquivos_destino = RetornaDiretorio($sock, 'Arquivos');
    $diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');
    ImportarDinamica($cod_curso, $cod_usuario, $cod_curso_import, $curso_extraido, $tabela, $dir, $diretorio_arquivos_destino, $diretorio_arquivos_origem);

    header("Location:dinamica.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=".$cod_topico_destino."&acao=".$acao."&atualizacao=true");
  }

  Desconectar($sock);
  exit;
  
?>