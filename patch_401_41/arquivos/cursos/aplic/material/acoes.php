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

  include("material.inc");
  
  $cod_ferramenta = $cod_ferramenta_m; //cod_ferramenta_m é uma variável de sessão que guarda a ferramenta atualmente utilizada
  //var_dump($cod_itens);exit;
  if ($cod_ferramenta==3)
    include("avaliacoes_material.inc");

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso); 
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  switch ($cod_ferramenta) {
    case 3 :
      $tabela="Atividade";
      $dirname="atividades";
      break;
    case 4 :
      $tabela="Apoio";
      $dirname="apoio";
      break;
    case 5 :
      $tabela="Leitura";
      $dirname="leituras";
      break;
    case 7 :
      $tabela="Obrigatoria";
      $dirname="obrigatoria";
      break;
  }

  $dir_item_temp=CriaLinkVisualizar($sock, $dirname, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

  if ($acao=="apagarItem"){
 
    ApagarItem($sock, $tabela, $cod_item, $cod_usuario);
    if (($cod_ferramenta==3) && ($AcessoAvaliacao))
    {
      if (AtividadeEhAvaliacao($sock,$cod_item))
      {
        $cod_avaliacao=RetornaCodAvaliacao($sock,$cod_item);
        ApagaAvaliacaoPortfolio($sock,$cod_avaliacao,$cod_usuario);
      }
    }
    Desconectar($sock);
    header("Location:material.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&cod_topico=".$cod_topico_raiz."&acao=".$acao."&atualizacao=true");
    exit();
  }
  else if ($acao=="novotopico")
  {
    if (NaoExisteTop($sock, $tabela, $cod_topico_raiz, $novo_nome, $cod_usuario))
    {
      $cod_topico=CriarTopico($sock, $tabela, $cod_topico_raiz, $novo_nome, $cod_usuario);

      Desconectar($sock);
      header("Location:material.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."&cod_topico_raiz=".$cod_topico."&acao=".$acao."&statusAcao=true");
	  exit();
    }
    else
    {

      echo("<script type=\"text/javascript\" language=\"JavaScript\">\n");
      //       echo("  alert('".RetornaFraseDaLista($lista_frases_geral, 73)."');\n");
      header("Location:material.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."&cod_topico_raiz=".$cod_topico_raiz."&atualizacao=false");
      Desconectar($sock);
      exit();
    }

  }
  
  else if ($acao=="apagarSelecionados")
  {
    $cod_topicos_array = explode(",", $cod_topicos);
    $cod_itens_array = explode(",", $cod_itens);

    if ($cod_itens!=""){


      foreach ($cod_itens_array as $cod => $linha){
        ApagarItem($sock, $tabela, $linha, $cod_usuario);


        ApagarItem($sock, $tabela, $linha, $cod_usuario);
        if (($cod_ferramenta==3) && ($AcessoAvaliacao))          
        {          
          if (AtividadeEhAvaliacao($sock,$linha))
          {            
            $cod_avaliacao=RetornaCodAvaliacao($sock,$linha);
            ApagaAvaliacaoPortfolio($sock,$cod_avaliacao,$cod_usuario);
          }
        }
      }
    }

    
    if ($cod_topicos!=""){

      foreach ($cod_topicos_array as $cod => $linha){
        ApagarTopico($sock, $tabela, $linha, $cod_usuario);
      }
    }

    Desconectar($sock);
    header("Location:material.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&cod_topico_raiz=".$cod_topico_raiz."&acao=".$acao."&atualizacao=true");
    exit;
  }

  /* ação = Anexar Arquivo - origem = ver.php */
  else if ($acao=='anexar'){

    /* Verifica a existência do diretório a ser movido o arquivo */
    if (!file_exists($diretorio_arquivos."/".$cod_curso)) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso);
    }

    if (!file_exists($diretorio_arquivos."/".$cod_curso."/".$dirname."/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/".$dirname."/");
    }
    if (!file_exists($diretorio_arquivos."/".$cod_curso."/".$dirname."/".$cod_item."/")) {
      CriaDiretorio($diretorio_arquivos."/".$cod_curso."/".$dirname."/".$cod_item."/");
    }

    $dir=$diretorio_arquivos."/".$cod_curso."/".$dirname."/".$cod_item."/";

    $nome_arquivo = $_FILES['input_files']['name'];
    //$nome_arquivo = RetiraEspacoEAcentos($nome_arquivo);
    $nome_arquivo = mb_convert_encoding($nome_arquivo, "UTF-8", "ISO-8859-1");

    if (!RealizaUpload($input_files,$dir.$nome_arquivo))
    {
      AcabaEdicao($tabela, $sock, $cod_curso, $cod_item, $cod_usuario, 1);
      /* 61 - Atenção: o arquivo que você anexou não existe ou tem mais de %dMb. Se você digitou o nome do arquivo, procure certificar-se que ele esteja correto ou então selecione o arquivo a partir do botão Procurar (ou Browse). */
      echo("<html>\n");
      echo("  <head>\n");
      echo("    <script type=\"text/javascript\" language=\"JavaScript\">\n");
      echo("      alert('".sprintf(RetornaFraseDaLista($lista_frases,61), ((int) ini_get('upload_max_filesize')))."');\n");
      echo("      history.go(-1);\n");
      echo("    </script>\n");
      echo("  </head>\n");
      echo("  <body>\n");
      echo("  </body>\n");
      echo("</html>\n");
      exit;
    }

//     AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);
	$atualizacao='true';	
  }
  

  else if ($acao=="moveritem")
  {
    MoverItem($sock, $tabela, $cod_item, $cod_usuario, $cod_topico_raiz);
    ArrumaPosicoesItens($sock, $tabela, $cod_topico_ant);
    
    Desconectar($sock);
    header("Location:material.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."&cod_topico_raiz=".$cod_topico_raiz."&acao=".$acao."&atualizacao=true");
    exit();
  }
  
  else if ($acao=="movertopico")
  {
    if (MoverTopico($sock, $tabela, $cod_topico, $cod_usuario, $cod_topico_raiz))
      ArrumaPosicoesTopicos($sock, $tabela, $cod_topico_ant);
  
    Desconectar($sock);
    header("Location:material.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."&cod_topico_raiz=".$cod_topico_raiz."&acao=".$acao."&atualizacao=true");
    exit();
  }  /* ação = Criar Item - origem = portfolio.php */
  else if ($acao=="criarItem"){

    $cod_item=IniciaCriacao($sock, $tabela, $cod_topico_raiz, $cod_usuario, $cod_curso, $dirname, $diretorio_temp, $novo_nome);
	$atualizacao = 'true';
  }else if ($acao=="descompactar"){

    $dir_tmp=$dir_item_temp['diretorio'];
    $caminho="";

    $tmp=explode("/",$arq);
    for ($c=0;$c<count($tmp)-1;$c++)
      $caminho=$tmp[$c]."/";

    $res=DescompactarArquivoZip($dir_tmp.$arq,$dir_tmp.$caminho);

    if ($res == false){
      $atualizacao = 'false';
    }else{
      $atualizacao = 'true';
      RemoveArquivo($dir_tmp.$arq);
    }

    AcabaEdicao($tabela, $sock, $cod_curso, $cod_item, $cod_usuario, 1);

  }

  Desconectar($sock);
  header("Location:ver.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&acao=".$acao."&atualizacao=".$atualizacao);
  exit;
?>