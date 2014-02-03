<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/acoes.php

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
  include($bibliotecas."importar.inc");
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
  
  $AcessoAvaliacaoM = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);
  
  
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
    if (($cod_ferramenta==3) && ($AcessoAvaliacaoM))
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
      AtualizaFerramentasNova($sock, $cod_ferramenta, 'T');
      Desconectar($sock);
      header("Location:material.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."&cod_topico_raiz=".$cod_topico."&acao=".$acao."&statusAcao=true");
      exit();
    }
    else
    {
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

        if (($cod_ferramenta==3) && ($AcessoAvaliacaoM))
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

    // Verifica se o nome do arquivo tem acentos ou chars estranhos.
    $nome_arquivo = $_FILES['input_files']['name'];

    // Se possuir acentos ou outros caracteres problematicos
    if (VerificaAnexo($nome_arquivo) == 0)
    {
      // Nao realiza upload de arquivos com acentos
      $acao = "nomeAnexo";
      $atualizacao = "false";
      header("Location:ver.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."&cod_topico_raiz=".$cod_topico_raiz."&cod_item=".$cod_item."&acao=".$acao."&atualizacao=".$atualizacao);	
      exit;
    }

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

    if (!RealizaUpload($input_files,$dir.$nome_arquivo))
    {
      /* 61 - Atenção: o arquivo que você anexou não existe ou tem mais de %dMb. Se você digitou o nome do arquivo, procure certificar-se que ele esteja correto ou então selecione o arquivo a partir do botão Procurar (ou Browse). */
      $atualizacao='false';
    }

    AcabaEdicao($tabela, $sock, $cod_curso, $cod_item, $cod_usuario, 1);
    $atualizacao='true';
    AtualizaFerramentasNova($sock, $cod_ferramenta, 'T');
    header("Location:ver.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."&cod_topico_raiz=".$cod_topico_raiz."&cod_item=".$cod_item."&acao=".$acao."&atualizacao=".$atualizacao);
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
    AtualizaFerramentasNova($sock, $cod_ferramenta, 'T');
    Desconectar($sock);
    header("Location:ver.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&acao=".$acao."&atualizacao=".$atualizacao);
    exit;

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
    
    header("Location:ver.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&acao=descompactar&atualizacao=true");  

  }
  else if ($acao == "validarImportacao"){
    $sock = MudarDB($sock, "");

    $cod_cursos = explode(";", $cod_curso_todos);
    $tipo_curso_origem = $cod_cursos[0]; // B = Base, E = Extra�do
    $cod_curso_origem = $cod_cursos[1];
    
    $tipo_curso = $_GET['tipo_curso'];
    $cod_categoria = $_GET['cod_categoria'];
    
    $_SESSION['cod_topico_destino'] = $cod_topico_raiz;
    $_SESSION['cod_curso_origem'] = $cod_curso_origem;
    $_SESSION['flag_curso_extraido'] = ($tipo_curso_origem == 'E');

    if($cod_curso_origem)
    {
      $cod_usuario_import = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso_origem);
      
      if ( FerramentaEstaCompartilhada($sock, $cod_curso_origem, $cod_ferramenta) ){
        $_SESSION['flag_curso_compartilhado'] = TRUE;
        header("Location:importar_material.php?cod_curso=".$cod_curso."&cod_curso_origem=".$cod_curso_origem."&cod_topico_raiz=".$cod_topico_raiz."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=1");
      } else if ( $cod_usuario_import != NULL && EFormadorMesmo($sock,$cod_curso_origem,$cod_usuario_import) ){
        $_SESSION['flag_curso_compartilhado'] = FALSE;
        header("Location:importar_material.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=1&cod_topico_raiz=".$cod_topico_raiz."&cod_curso_origem=".$cod_curso_origem);
      } else {
        header("Location:importar_curso.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_topico_raiz=".$cod_topico_raiz."&acao_feedback=".$acao."&atualizacao=false");
      }
    }
    else
      header("Location:importar_curso.php?cod_ferramenta=".$cod_ferramenta."&cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&acao=".$acao."&tipo_curso=".$tipo_curso."&cod_categoria=".$cod_categoria."&acao_feedback=falhaImportacao&atualizacao=false");
  }else if ($acao == "importarItem"){

    $cod_curso_destino = $cod_curso;
    $cod_topico_destino = $_SESSION['cod_topico_destino'];
    $cod_usuario;
    $cod_curso_origem = $_SESSION['cod_curso_origem'];
    $flag_curso_extraido = $_SESSION['flag_curso_extraido'];
    $flag_curso_compartilhado = $_SESSION['flag_curso_compartilhado'];
    $array_topicos_origem = $cod_topicos_import;
    $array_itens_origem = $cod_itens_import;
    $dirname = $dir;
    $nome_tabela = $tabela;
    $sock=Conectar("");
    if ($curso_extraido)
      $diretorio_arquivos_origem = RetornaDiretorio($sock, 'Montagem');
    else
      $diretorio_arquivos_origem = RetornaDiretorio($sock, 'Arquivos');
    $diretorio_arquivos_destino = RetornaDiretorio($sock, 'Arquivos');
    $diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');

    $sock = MudarDB($sock, $cod_curso);
    AtualizaFerramentasNova($sock, $cod_ferramenta, 'T');
    $sock = MudarDB($sock, "");

    ImportarMateriais($cod_curso_destino, $cod_topico_destino, $cod_usuario,
                      $cod_curso_origem, $flag_curso_extraido, $flag_curso_compartilhado,
                      $array_topicos_origem, $array_itens_origem, $nome_tabela,
                      $dirname, $diretorio_arquivos_destino, $diretorio_arquivos_origem);
    
    header("Location:material.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=".$cod_topico_destino."&acao=".$acao."&atualizacao=true");
  }

?>
