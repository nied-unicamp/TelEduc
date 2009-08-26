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

    $nome_arquivo = $_FILES[input_files][name];
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
  }

  Desconectar($sock);
  exit;
?>