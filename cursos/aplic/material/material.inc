<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/material.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½cia
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

    Nied - Ncleo de Informï¿½ica Aplicada ï¿½Educaï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/material/material.inc
  ========================================================== */  
  
/*
Funcoes dessa biblioteca:

RetornaDiretorio
UltimaPosicaoLivre
OrganizaPosicaoTopico
ApagarItem
ExcluirItem
ApagarTopico 
RecuperarItem
AtualizaCompartilhamentoPastas
EPai
MoverItem
MoverTopico
RetornaListaDeTopicos
RetornaListaDeTopicosRecursao
RenomearTopico
MudarCompartilhamento 
RetornaItensDoTopico
RetornaTopicosDoTopico
ArrumaPosicoes
MudarPosicaoItem
MudarPosicaoTopico
RetornaTopicosAncestrais
RetornaUltimaPosicaoHistorico
CriarTopico
IniciaCriacao
RetornaDadosDoItem
RetornaResHistoricoDoItem
CopiaArquivoParaTemporario
RetornaArquivosMaterialTemp
RetornaArquivosMaterialVer
RetornaEnderecosMaterial
ApagaEndereco
ApagaTodosEnderecos
InsereEndereco
AtualizaDataEdicao
AtualizaMaterial
MudaStatusEdicao
CancelaEdicao
CriaLinkVisualizar
EncerrarExibicaoArquivo
RetornaNumArquivosVisiveis
haArquivosVisiveisDir
RetornaMaiorData
ApagaAvaliacaoPortfolio
ImportarMateriaisObterRecur
ImportarMateriaisInserirRecur
ImportarMateriais
ImportarTopico
ImportarItem
ImportarAvaliacao
EPaiTopicos
NaoExisteTopPeloCod
NaoExisteTopPeloTexto
ExistemItensVisiveis
MoverItensDinamic
RetornaFraseDinamic
AtualizaPosicoes
RenomearTopicoDinamic
CriaTopicoDinamic
EditarTitulo
EditarTexto
InsereEnderecoDinamic
AbreEdicao
ExcluirArquivo
ExcluirEndereco
OcultarArquivosDinamic
DesocultarArquivosDinamic
AcabaEdicaoDinamic
AcabaEdicao
MoverArquivosDinamic
ExcluirItensDinamic
RecuperarItensDinamic
*/


/******************************************************************************************
   RetornaDiretorio - Retorna o Diretïorio da tabela de diretorios
   Entrada: $sock - BASE EXTERNA
            $item - nome do item a ser buscado
   Saida: string com o diretïorio
*******************************************************************************************/
function RetornaDiretorio($sock,$item)
{
  $query="select diretorio from Diretorio where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha[0]);
}

/*********************************************************************************************
   UltimaPosicaoLivre - Retorna a posicao do ultimo item livre
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - codigo do topico atual
   Saida: posicao
**********************************************************************************************/
function UltimaPosicaoLivre($sock, $tabela, $cod_topico)
{
  $consulta="select max(posicao_item) from ".$tabela."_itens where cod_topico=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $max_posicao_item=RetornaLinha($res);

  if($max_posicao_item[0] == null) $max_posicao_item=0;

  $consulta="select max(posicao_topico) from ".$tabela."_topicos where cod_topico_pai=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $max_posicao_topico=RetornaLinha($res);
  if($max_posicao_topico[0] == null) $max_posicao_topico=0;
  

  return(max($max_posicao_topico[0], $max_posicao_item[0])+1);
}


/**************************************************************************************
   OrganizaPosicaoTopico - Organizaïcao a numeracao da posicao dos itens
                           na pagina, em ordem crescente
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - codigo do topico atual
   Saida: nenhuma
***************************************************************************************/
function OrganizaPosicaoTopico($sock, $tabela, $cod_topico)
{
  $consulta="select cod_item, posicao_item from ".$tabela."_itens where cod_topico=$cod_topico and status<>'A' and status<>'X' and status<>'C' order by posicao_item";
  $res=Enviar($sock, $consulta);
  $lista_itens=RetornaArrayLinhas($res);
  if (count($lista_itens)>0)
  {
    $cont=1;
    foreach ($lista_itens as $cod => $linha)
    {
      if ($linha['posicao_item']!=$cont)
      {
        $consulta="update ".$tabela."_itens set posicao_item=".$cont." where cod_item=".$linha['cod_item'];
        $res=Enviar($sock, $consulta);
      }
      $cont++;
    }
  }
  $consulta="select cod_topico, posicao_topico from ".$tabela."_topicos where cod_topico_pai=$cod_topico order by posicao_topico";
  $res=Enviar($sock, $consulta);
  $lista_topicos=RetornaArrayLinhas($res);
  if (count($lista_topicos)>0)
  {
    $cont=1;
    foreach ($lista_topicos as $cod => $linha)
    {
      if ($linha['posicao_topico']!=$cont)
      {
        $consulta="update ".$tabela."_topicos set posicao_topico=".$cont." where cod_topico=".$linha['cod_topico'];
        $res=Enviar($sock, $consulta);
      }
      $cont++;
    }
  }

}


/******************************************************************************************
   ApagarItem - Apaga o item selecionado (envia para a lixeira)
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario que estïver removendo, para o historico
   Saida: nenhuma
*******************************************************************************************/
function ApagarItem ($sock, $tabela, $cod_item, $cod_usuario)
{
  $linha=RetornaDadosDoItem($sock, $tabela, $cod_item);
  $consulta="update ".$tabela."_itens set cod_topico=2, data=".time()." where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $consulta="insert into ".$tabela."_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'A')";
  $res=Enviar($sock, $consulta);
  ArrumaPosicoes($sock, $tabela, $linha['cod_topico']);
  AtualizaCompartilhamentoPastas ($sock, $tabela, $linha['cod_topico'], 'F');
}


/********************************************************************************************
   ExcluirItem - Apaga o item selecionado (definitivamente)
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario que estiver removendo, para o historico
   Saida: nenhuma
*********************************************************************************************/
function ExcluirItem ($sock, $tabela, $cod_item, $cod_usuario)
{
  $linha=RetornaDadosDoItem($sock, $tabela, $cod_item);
  $consulta="update ".$tabela."_itens set cod_topico=0, data=".time()." where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $consulta="insert into ".$tabela."_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'X')";
  $res=Enviar($sock, $consulta);
  OrganizaPosicaoTopico($sock, $tabela, $linha['cod_topico']);
}


/****************************************************************************************************
   ApagarTopico - Apaga o topico selecionado (envia para a lixeira todos os itens das subpastas)
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - codigo do topico
            $cod_usuario - codigo do usuario que estiver removendo, para o historico
   Saida: nenhuma
******************************************************************************************************/
function ApagarTopico ($sock, $tabela, $cod_topico, $cod_usuario)
{
  /* Recursivamente, apaga os tï¿½icos filhos */
  $query="select cod_topico from ".$tabela."_topicos where cod_topico_pai=".$cod_topico;
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  if (count($lista)>0)
    foreach($lista as $cod=>$linha)
      ApagarTopico($sock,$tabela,$linha['cod_topico'],$cod_usuario);

  /* Apaga os itens do topico */
  $query="select cod_item from ".$tabela."_itens where cod_topico=".$cod_topico;
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  if (count($lista)>0)
    foreach ($lista as $cod=>$linha)
    {
      $query="update ".$tabela."_itens set cod_topico=2,data=".time()." where cod_item=".$linha['cod_item'];
      Enviar($sock,$query);
      $query="insert into ".$tabela."_itens_historicos values (".$linha['cod_item'].", ".$cod_usuario.", ".time().", 'A')";
      Enviar($sock,$query);
    }

  /* Apagando o tï¿½ico e rearranjando ordem */
  $query="select cod_topico_pai from ".$tabela."_topicos where cod_topico=".$cod_topico;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $query="delete from ".$tabela."_topicos where cod_topico=".$cod_topico;
  Enviar($sock,$query);
  ArrumaPosicoes($sock, $tabela, $cod_topico);
  ArrumaPosicoes($sock, $tabela, $cod_topico['cod_topico_pai']);
}


/**********************************************************************************************
   RecuperarItem - Recupera o item selecionado (da lixeira)
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario que estiver removendo, para o historico
   Saida: nenhuma
**********************************************************************************************/
function RecuperarItem ($sock, $tabela, $cod_item, $cod_usuario)
{
  $linha=RetornaDadosDoItem($sock, $tabela, $cod_item);
  $consulta="update ".$tabela."_itens set cod_topico=1, data=".time().", posicao_item=".UltimaPosicaoLivre($sock, $tabela, 1).",tipo_compartilhamento='F' where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $consulta="insert into ".$tabela."_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'R')";
  $res=Enviar($sock, $consulta);
}


/**************************************************************************************************
   AtualizaCompartilhamentoPastas - Atualiza o compartilhamento das pastas (recursivamente)
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - codigo do topico
            $comp - Compartilhamento a ser setado (F ou T);
   Saida: nenhuma
***************************************************************************************************/
function AtualizaCompartilhamentoPastas ($sock, $tabela, $cod_topico, $comp)
{
  if ($comp=="F")
  {
    $consulta="select cod_item from ".$tabela."_itens where cod_topico=".$cod_topico." and tipo_compartilhamento='T' limit 1";
    $res=Enviar($sock, $consulta);
    $num1=RetornaNumLinhas($res);

    $consulta="select cod_topico from ".$tabela."_topicos where cod_topico_pai=".$cod_topico." and tipo_compartilhamento='T' limit 1";
    $res=Enviar($sock, $consulta);
    $num2=RetornaNumLinhas($res);

    if ($num1==0 && $num2==0)
    {
      $consulta="select cod_topico_pai from ".$tabela."_topicos where cod_topico=".$cod_topico;
      $res=Enviar($sock, $consulta);
      $linha=RetornaLinha($res);

      if ($linha['cod_topico_pai']!="")
      {
        $consulta="update ".$tabela."_topicos set tipo_compartilhamento='F' where cod_topico=".$cod_topico;
        $res=Enviar($sock, $consulta);

        AtualizaCompartilhamentoPastas($sock, $tabela, $linha['cod_topico_pai'], $comp);
      }
    }
  }
  else
  {
    $consulta="select cod_topico_pai from ".$tabela."_topicos where cod_topico=$cod_topico";
    $res=Enviar($sock, $consulta);
    $linha=RetornaLinha($res);

    if ($linha['cod_topico_pai']!="")
    {
      $consulta="update ".$tabela."_topicos set tipo_compartilhamento='T' where cod_topico=$cod_topico";
      $res=Enviar($sock, $consulta);

      AtualizaCompartilhamentoPastas($sock, $tabela, $linha['cod_topico_pai'], $comp);
    }
  }

}


/*****************************************************************************************
   EPai - Retorna verdadeiro se $cod_topico for descendente de $cod_topico_pai
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico_pai - codigo do topico pai
            $cod_topico - codigo do topico
   Saida: true, se For pai, false caso contrario
******************************************************************************************/
function EPai($sock, $tabela, $cod_topico_pai, $cod_topico)
{
  if ($cod_topico_pai==$cod_topico)
    return(true);
  else
  {
    if ($cod_topico=="")
      return(false);
    else
    {
      $consulta="select cod_topico_pai from ".$tabela."_topicos where cod_topico=$cod_topico";
      $res=Enviar($sock, $consulta);
      $linha=RetornaLinha($res);
      return(EPai($sock, $tabela, $cod_topico_pai, $linha['cod_topico_pai']));
    }
  }
}


/**********************************************************************************************
   MoverItem - Move o item para outra pasta
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - item a ser movido
            $cod_usuario - usuario realizando a acao
            $cod_topico - topico destino
   Saida: nenhuma
***********************************************************************************************/
function MoverItem ($sock, $tabela, $cod_item, $cod_usuario, $cod_topico)
{
  $data=time();
  $consulta="select cod_topico, tipo_compartilhamento from ".$tabela."_itens where cod_item=$cod_item";
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  $posicao_item=UltimaPosicaoLivre($sock, $tabela, $cod_topico);
  /* Movendo item */
  $consulta="update ".$tabela."_itens set cod_topico=$cod_topico, posicao_item=$posicao_item where cod_item=$cod_item";
  $res=Enviar($sock, $consulta);
  /* Inserindo dados no histï¿½ico */
  $consulta="insert into ".$tabela."_itens_historicos (cod_item, cod_usuario, data, acao) values ($cod_item, $cod_usuario, $data, 'M')";
  $res=Enviar($sock, $consulta);
  AtualizaCompartilhamentoPastas($sock, $tabela, $linha['cod_topico'], "F");
  AtualizaCompartilhamentoPastas($sock, $tabela, $cod_topico, $linha['tipo_compartilhamento']);
}


/************************************************************************************
   MoverTopico - Move o topico para outra pasta
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - topico a ser movido
            $cod_usuario - usuario realizando a acao
            $cod_topico_dest - topico destino
   Saida: true se nova posicao for vï¿½ida
*************************************************************************************/
function MoverTopico ($sock, $tabela, $cod_topico, $cod_usuario, $cod_topico_dest)
{
  if (EPai($sock, $tabela, $cod_topico, $cod_topico_dest))
  {
    /* Erro! */
    return false;
  }
  else
  {
    $consulta="select cod_topico_pai, tipo_compartilhamento from ".$tabela."_topicos where cod_topico=$cod_topico";
    $res=Enviar($sock, $consulta);
    $linha=RetornaLinha($res);
    $posicao_topico=UltimaPosicaoLivre($sock, $tabela, $cod_topico_dest);
    /* Movendo topico */
    $consulta="update ".$tabela."_topicos set cod_topico_pai=$cod_topico_dest, posicao_topico=$posicao_topico where cod_topico=$cod_topico";
    $res=Enviar($sock, $consulta);

    AtualizaCompartilhamentoPastas($sock, $tabela, $linha['cod_topico_pai'], "F");
    AtualizaCompartilhamentoPastas($sock, $tabela, $cod_topico_dest, $linha['tipo_compartilhamento']);
    return true;
  }
}


/******************************************************************************************
   RetornaListaDeTopicos - retorna array com lista de topicos
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
   Saida: array []['cod_topico']
                []['cod_topico_pai']
                []['cod_usuario']
                []['topico']
                []['tipo_compartilhamento']
                []['data']
                []['posicao_topico']
                []['nivel'] - Nivel de recursao até atingir o topico
                []['espacos'] - espacos que serao usados para exibicao (&nbsp;)
********************************************************************************************/
function RetornaListaDeTopicos ($sock, $tabela)
{
  $consulta="select * from ".$tabela."_topicos where cod_topico=1";
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  $retorno[0]=$linha;
  $retorno[0]['nivel']=0;
  $retorno[0]['espacos']="";
  $topicos=RetornaListaDeTopicosRecursao($sock, $tabela, $linha);
  $i=1;
  if (!empty($topicos)>0)
  {
    foreach ($topicos as $cod => $topico)
    {
      if($topico['cod_topico']!=2){
        $retorno[$i]=$topico;
        $retorno[$i]['nivel']++;
        $retorno[$i]['espacos']=$retorno[$i]['espacos']."&nbsp;&nbsp;";
        $i++;
      }
    }
  }
  return($retorno);
}


/*******************************************************************************************************
   RetornaListaDeTopicosRecursao - retorna array com lista de topicos a partir de um topico dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $topico - array com 1 elemento []['cod_topico'], contendo o cï¿½igo do tï¿½ico
   Saida: array []['cod_topico']
                []['cod_topico_pai']
                []['cod_usuario']
                []['topico']
                []['tipo_compartilhamento']
                []['data']
                []['posicao_topico']
                []['nivel'] - Nivel de recursao até atingir o topico
                []['espacos'] - espacos que serao usados para exibicao (&nbsp;)
*********************************************************************************************************/
function RetornaListaDeTopicosRecursao ($sock, $tabela, $topico)
{
  $consulta="select * from ".$tabela."_topicos where cod_topico_pai=".$topico['cod_topico']." order by posicao_topico";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  $i=0;
  unset($lista_retorno);
  if (count($lista)>0)
    foreach ($lista as $cod1 => $linha1)
    {
      $lista_retorno[$i]=$linha1;
      $lista_retorno[$i]['nivel']=0;
      $lista_retorno[$i]['espacos']="";

      $i++;
      $lista_filho=RetornaListaDeTopicosRecursao($sock, $tabela, $linha1);
      if (count($lista_filho)>0)
        foreach ($lista_filho as $cod2 => $linha2)
        {
          $lista_retorno[$i]=$linha2;
          $lista_retorno[$i]['nivel']++;
          $lista_retorno[$i]['espacos']=$lista_retorno[$i]['espacos']."&nbsp;&nbsp;";

          $i++;
        }
    }
  return($lista_retorno);
}


/*************************************************************************************************
   RenomearTopico - Renomeia o topico
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - topico a ser renomear
            $novo_nome - novo nome a ser usado
   Saida: nenhuma
*************************************************************************************************/
function RenomearTopico ($sock, $tabela, $cod_topico, $novo_nome)
{
  $consulta="update ".$tabela."_topicos set topico='".$novo_nome."' where cod_topico=".$cod_topico;
  $res=Enviar($sock, $consulta);
}


/*************************************************************************************************
   MudarCompartilhamento - Muda o compartilhamento do item dado
   Entrada: $dadosForm - array com [tipo_comp]
                                   [cod_item]
                                   [cod_curso]
                                   [cod_pagina]
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $novo_comp - novo compartilhamento
   Saida: nenhuma
*************************************************************************************************/
function MudarCompartilhamento ($dadosForm,$tabela,$novo_comp)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $tipo_comp = $dadosForm['tipo_comp'];
  $cod_item = $dadosForm['cod_item'];
  $cod_curso = $dadosForm['cod_curso'];
  $cod_pagina = $dadosForm['cod_pagina'];
  $texto = $dadosForm['texto'];
  
  $sock=Conectar($cod_curso);
  $cod_usuario_global=$_SESSION['cod_usuario_global_s'];
  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  
  $query="select tipo_compartilhamento from ".$tabela."_itens where cod_item=".$cod_item;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha['tipo_compartilhamento']!=$tipo_comp)
  {
    $data_agora=time();
    $consulta="update ".$tabela."_itens set tipo_compartilhamento='".$tipo_comp."', data=".$data_agora." where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);
    $consulta="select cod_topico from ".$tabela."_itens where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);
    $linha=RetornaLinha($res);
    AtualizaCompartilhamentoPastas($sock, $tabela, $linha['cod_topico'], $tipo_comp);
    Desconectar($sock);
    AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, 1);

    // material.php
    $data_agora=UnixTime2Data($data_agora);

    if($cod_pagina == 2) // ver.php
      $data_agora=UnixTime2DataHora($data_agora);
  
    $objResponse->addAssign("data_".$cod_item, "innerHTML", $data_agora);
    $objResponse->addAssign("comp_".$cod_item, "innerHTML", print_r($novo_comp, true));
    $objResponse->addScript("document.getElementById('comp_".$cod_item."').onclick = function(event) { js_cod_item='".$cod_item."'; AtualizaComp('".$tipo_comp."'); MostraLayer(cod_comp,140, event); }" );

    $objResponse->addAssign("tr_".$cod_item, "className", "novoitem");
  }
  else // nÃ£o editou
    AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, 0);

	/* Feedback */
  	$objResponse->addScriptCall("mostraFeedback", $texto, 'true');
  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}


/*************************************************************************************************
   RetornaItensDoTopico - Retorna array com os itens do tï¿½ico dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - topico
   Saida: array com []['cod_item']
                    []['cod_topico']
                    []['cod_usuario']
                    []['titulo']
                    []['texto']
                    []['tipo_compartilhamento']
                    []['data']
                    []['data_ativo']
                    []['data_inativo']
                    []['posicao_item']
                    []['status']
                    []['inicio_edicao']
*************************************************************************************************/
function RetornaItensDoTopico ($sock, $tabela, $cod_topico, $tipo_comp="")
{
  $consulta="select * from ".$tabela."_itens where cod_topico=".$cod_topico;
  
  if($tipo_comp!="")
    $consulta=" and tipo_compartilhamento='".$tipo_comp."'";
  $consulta.=" order by posicao_item";
    
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}


/*************************************************************************************************
   RetornaTopicosDoTopico - Retorna array com os topicos filhos do topico dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - topico
   Saida: array com []['cod_topico']
                    []['cod_topico_pai']
                    []['cod_usuario']
                    []['topico']
                    []['tipo_compartilhamento']
                    []['data']
                    []['posicao_topico']
*************************************************************************************************/
function RetornaTopicosDoTopico ($sock, $tabela, $cod_topico)
{
  //não retorna a lixeira
  $consulta="select * from ".$tabela."_topicos where cod_topico_pai=".$cod_topico." and cod_topico<>'2' order by posicao_topico";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}


/*************************************************************************************************
   ArrumaPosicoes - rearranja as posicoes dos itens em um topico qdo um item removido
   entrada: $sock - sock de conexao
            $tabela - tabela da ferramenta na qual arrumar as posicoes (Atividades, Material, Leituras,                 Parada)
            $cod_topico - codigo do topico no qual arrumar as posicoes dos itens
   saida: nenhuma
**************************************************************************************************/
function ArrumaPosicoes($sock, $tabela, $cod_topico)
{
  PegaSemaforo($sock, $tabela);

  $query="select * from ".$tabela."_itens where cod_topico=".$cod_topico." order by posicao_item asc";
  $res = Enviar($sock, $query);
  $lista_itens_ord=RetornaArrayLinhas($res);

  $query="select * from ".$tabela."_topicos where cod_topico_pai=".$cod_topico." order by posicao_topico asc";
  $res = Enviar($sock, $query);
  $lista_topicos_ord=RetornaArrayLinhas($res);

  $top_index = 0;
  $itens_index = 0;

  for($i=0; $i<((count($lista_topicos_ord))+(count($lista_itens_ord))); $i++){
    if((!isset($lista_topicos_ord[$top_index]['posicao_topico'])) || (isset($lista_itens_ord[$itens_index]['posicao_item']) &&($lista_topicos_ord[$top_index]['posicao_topico'] > $lista_itens_ord[$itens_index]['posicao_item']))) {
      $lista_unificada[$i] = $lista_itens_ord[$itens_index];
      $itens_index++;
    }else{
      //este if é para não alterar a estrutura dos portfólios antigos
      if((isset($lista_itens_ord[$top_index]['posicao_item'])) && ($lista_topicos_ord[$top_index]['posicao_topico'] == $lista_itens_ord[$itens_index]['posicao_item'])) {
        $lista_itens_ord[$itens_index]['posicao_item']++;
      }
      $lista_unificada[$i] = $lista_topicos_ord[$top_index];
      $top_index++;
    }
  }

  if($lista_unificada)	
  {
    foreach($lista_unificada as $cod => $linha){

    //é item
      if(isset($linha['posicao_item'])){
        $update = "update ".$tabela."_itens set posicao_item=".($cod+1)." where cod_item = ".$linha['cod_item'];
        Enviar($sock, $update);
      }
      //é tópico
      else if(isset($linha['posicao_topico'])) {
        $update = "update ".$tabela."_topicos set posicao_topico=".($cod+1)." where cod_topico = ".$linha['cod_topico'];
        Enviar($sock, $update);
      }
    }
  }

  LiberaSemaforo($sock, $tabela);
}


/*************************************************************************************************
   MudarPosicaoItem - Muda a posicao do item (utiliza semaforo)
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico_raiz - tï¿½ico a partir do qual serï¿½ rearranjados os itens
            $cod_item - Codigo do item a ter a posiï¿½o mudada
            $posicao - valor da nova posiï¿½o do item
   Saida: nenhuma
**************************************************************************************************/
function MudarPosicaoItem ($sock, $tabela, $cod_topico_raiz, $cod_item, $posicao)
{
  $consulta="select cod_item from ".$tabela."_itens where cod_topico=".$cod_topico_raiz." order by posicao_item";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  $cont=1;
  PegaSemaforo($sock, $tabela);
  if ($posicao==0)
    $posicao=1;
  if (count($lista)>0)
  {
    foreach ($lista as $cod => $linha)
    {
      if ($cod_item==$linha['cod_item'])
      {
        if ($posicao==$cont)
        {
          $consulta="update ".$tabela."_itens set posicao_item=".$cont." where cod_item=".$cod_item;
          $res=Enviar($sock, $consulta);
          $cont++;
          $posicao=-1;
        }
      }
      else
      {
        if ($posicao==$cont)
        {
          $consulta="update ".$tabela."_itens set posicao_item=".$cont." where cod_item=".$cod_item;
          $res=Enviar($sock, $consulta);
          $cont++;
          $posicao=-1;
        }
        $consulta="update ".$tabela."_itens set posicao_item=".$cont." where cod_item=".$linha['cod_item'];
        $res=Enviar($sock, $consulta);
        $cont++;
      }
    }
    if ($posicao>0)
    {
      $consulta="update ".$tabela."_itens set posicao_item=".$cont." where cod_item=".$cod_item;
      $res=Enviar($sock, $consulta);
      $cont++;
    }
  }
  LiberaSemaforo($sock, $tabela);
}


/*************************************************************************************************
   MudarPosicaoTopico - Muda a posicao do topico (utiliza semaforo)
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico_raiz - topico a partir do qual sera rearranjados os itens
            $cod_topico - Codigo do topico a ter a posicao mudada
            $posicao - valor da nova posicao do item
   Saida: nenhuma
*************************************************************************************************/
function MudarPosicaoTopico ($sock, $tabela, $cod_topico_raiz, $cod_topico, $posicao)
{
  $consulta="select * from ".$tabela."_topicos where cod_topico_pai=".$cod_topico_raiz." order by posicao_topico";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  $cont=1;
  PegaSemaforo($sock, $tabela);
  if ($posicao==0)
    $posicao=1;
  if (count($lista)>0)
  {
    foreach ($lista as $cod => $linha)
    {
      if ($cod_topico==$linha['cod_topico'])
      {
        if ($posicao==$cont)
        {
          $consulta="update ".$tabela."_topicos set posicao_topico=".$cont." where cod_topico=".$cod_topico;
          $res=Enviar($sock, $consulta);
          $cont++;
          $posicao=-1;
        }
      }
      else
      {
        if ($posicao==$cont)
        {
          $consulta="update ".$tabela."_topicos set posicao_topico=".$cont." where cod_topico=".$cod_topico;
          $res=Enviar($sock, $consulta);
          $cont++;
          $posicao=-1;
        }
        $consulta="update ".$tabela."_topicos set posicao_topico=".$cont." where cod_topico=".$linha['cod_topico'];
        $res=Enviar($sock, $consulta);
        $cont++;
      }
    }
    if ($posicao>0)
    {
      $consulta="update ".$tabela."_topicos set posicao_topico=".$cont." where cod_topico=".$cod_topico;
      $res=Enviar($sock, $consulta);
      $cont++;
      $posicao=-1;
    }
  }
  LiberaSemaforo($sock, $tabela);
}


/*************************************************************************************************
   RetornaTopicosAncestrais - Retorna array com os topicos ancestrais do topico dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - Codigo do topico
   Saida: array [] - topicos (em ordem crescente de ancestralidade)
**************************************************************************************************/
function RetornaTopicosAncestrais ($sock, $tabela, $cod_topico)
{
  $cont=0;
  do
  {
    $consulta="select * from ".$tabela."_topicos where cod_topico=".$cod_topico;
    $res=Enviar($sock, $consulta);
    $linha=RetornaLinha($res);
    $cod_topico=$linha['cod_topico_pai'];
    $retorno[$cont]=$linha;
    $cont++;
  }
  while ($cod_topico!="");
  return($retorno);
}


/*************************************************************************************************
   RetornaUltimaPosicaoHistorico - Retorna a ultima ocorrencia do historico de um item dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - item
   Saida: array ['cod_item']
                ['cod_usuario']
                ['data']
                ['acao']
**************************************************************************************************/
function RetornaUltimaPosicaoHistorico ($sock, $tabela, $cod_item)
{
  $consulta="select * from ".$tabela."_itens_historicos where cod_item=".$cod_item." order by data desc limit 1";
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}


/*************************************************************************************************
   CriarTopico - Cria um novo topico filho do topico dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico_raiz - topico pai
            $novo_nome - nome do novo topico
            $cod_usuario - codigo do usuario atual
   Saida: codigo do topico criado
***********************************************************************************************/
function CriarTopico ($sock, $tabela, $cod_topico_raiz, $novo_nome, $cod_usuario)
{
  $posicao=UltimaPosicaoLivre($sock, $tabela, $cod_topico_raiz);
  $consulta="insert into ".$tabela."_topicos (cod_topico_pai, cod_usuario, topico, tipo_compartilhamento, data, posicao_topico) values (".$cod_topico_raiz.", ".$cod_usuario.", '".$novo_nome."', 'F', ".time().", ".$posicao.")";
  $res=Enviar($sock, $consulta);
  $cod_topico = RetornaUltimoAutoIncrement($sock);
  return($cod_topico);
}


/*******************************************************************************************
   IniciaCriacao - Inicia a criacao de um novo item
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - topico pai
            $cod_usuario - usuario atual
            $cod_curso - codigo do curso
            $dirname - Nome do diretorio onde os arquivos sao arquivados
            $diretorio_temp - nome do diretorio temporario, usado durante a edicao
            $novo_nome - Título do item
   Saida: codigo do item criado
******************************************************************************************/
function IniciaCriacao ($sock, $tabela, $cod_topico, $cod_usuario, $cod_curso, $dirname, $diretorio_temp, $novo_nome)
{
  $cod_item=RetornaProximoCodigo($sock, $tabela."_itens");
  $posicao=UltimaPosicaoLivre($sock, $tabela, $cod_topico);
  $consulta="insert into ".$tabela."_itens (cod_item, cod_topico, cod_usuario, titulo, data, posicao_item, status, inicio_edicao, tipo_compartilhamento) values (".$cod_item.", ".$cod_topico.", ".$cod_usuario.", '".$novo_nome."', ".time().", ".$posicao.", 'L', ".time().", 'T')";
  $res=Enviar($sock, $consulta);
  $consulta="insert into ".$tabela."_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'C')";
  $res=Enviar($sock, $consulta);

  if ((trim($diretorio_temp)!="")&&($cod_curso!="")&&(!ExisteArquivo($diretorio_temp."/tmp/".$cod_curso."/")))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/");
  if ((trim($diretorio_temp)!="")&&($cod_curso!="")&&(trim($dirname)!="")&&!ExisteArquivo($diretorio_temp."/tmp/".$cod_curso."/".$dirname."/"))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/".$dirname."/");

  $dir=$diretorio_temp."/tmp/".$cod_curso."/".$dirname."/".$cod_item."/";
  CriaDiretorio($dir);

  return($cod_item);
}


/****************************************************************************************
   RetornaDadosDoItem - Retorna um array com os dados do item
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - topico
   Saida: array com ['cod_item']
                    ['cod_topico']
                    ['cod_usuario']
                    ['titulo']
                    ['texto']
                    ['tipo_compartilhamento']
                    ['data']
                    ['data_ativo']
                    ['data_inativo']
                    ['posicao_item']
                    ['status']
                    ['inicio_edicao']
*******************************************************************************************/
function RetornaDadosDoItem ($sock, $tabela, $cod_item)
{
  $consulta="select * from ".$tabela."_itens where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}


/**************************************************************************************************
   RetornaResHistoricoDoItem - Retorna todas as ocorrencias do historico de um item dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - item
   Saida: array []['cod_item']
                []['cod_usuario']
                []['data']
                []['acao']
***************************************************************************************************/
function RetornaResHistoricoDoItem ($sock, $tabela, $cod_item)
{
  $consulta="select * from ".$tabela."_itens_historicos where cod_item=".$cod_item." order by data desc";
  $res=Enviar($sock, $consulta);
  return($res);
}


/********************************************************************************************
   CopiaArquivoParaTemporario - Copia os arquivos para temporario
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
            $dirname - nome do diretorio da ferramenta
            $cod_item - codigo do item
   Saida: false - se houve algum erro.
*******************************************************************************************/
function CopiaArquivoParaTemporario($sock,$cod_curso,$diretorio_arquivos,$diretorio_temp,$dirname,$cod_item)
{
  $dir=$diretorio_arquivos."/".$cod_curso."/".$dirname."/".$cod_item."/";
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/".$dirname."/".$cod_item."/";
  $dirtemp2=$diretorio_temp."/tmp/".$cod_curso."/".$dirname."/";

  $ret = true;
  if(($diretorio_arquivos=="")||($cod_curso=="")||($dirname=="")||($cod_item=="")||($diretorio_temp==""))
       return false;

  if (!ExisteArquivo($diretorio_temp."/tmp/".$cod_curso."/"))
     if(!CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/"))
       return false;
  if (!ExisteArquivo($diretorio_temp."/tmp/".$cod_curso."/".$dirname."/"))
     if(!CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/".$dirname."/"))
       return false;

  if (!ExisteArquivo($dir))
  {
    if (!CriaDiretorio($dir))
      return false;
  }

  if (ExisteArquivo($dirtemp))
  {
    if (!RemoveDiretorio($dirtemp))
       return false;
  }
  if (!CopiaDiretorio($dir,$dirtemp2))
  {
    $ret = false;
  }
  return $ret;
}


/********************************************************************************************************
   RetornaArquivosMaterialTemp - Retorna lista de arquivos do Material no diretorio temporario
   Entrada: $diretorio_temp - diretorio temporario de arquivos
            $dir - nome do diretorio da ferramenta
            $cod_item - codigo do item
   Saida: Array multidimensional com:
          $lista[<num>]['Caminho'] - caminho completo.
          $lista[<num>]['Diretorio'] - Diretorio do arquivo
          $lista[<num>]['Arquivo'] - Nome do arquivo
          $lista[<num>]['Status'] - Condicao especial (true ou false);
          $lista[<num>]['Tamanho'] - tamanho do arquivo
          $lista[<num>]['Data'] - data da ltima modificacao
********************************************************************************************************/
function RetornaArquivosMaterialTemp ($cod_curso, $diretorio_temp, $dir, $cod_item)
{
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/".$dir."/".$cod_item."/";
  return (RetornaArrayDiretorio($dirtemp));
}


/*********************************************************************************************
   RetornaArquivosMaterialVer - Retorna lista de arquivos do Material
   Entrada: $diretorio_temp - diretorio temporario de arquivos
            $dir - nome do diretorio da ferramenta
            $cod_item - codigo do item
   Saida: Array multidimensional com:
          $lista[<num>]['Caminho'] - caminho completo.
          $lista[<num>]['Diretorio'] - Diretorio do arquivo
          $lista[<num>]['Arquivo'] - Nome do arquivo
          $lista[<num>]['Status'] - Condicao especial (true ou false);
          $lista[<num>]['Tamanho'] - tamanho do arquivo
          $lista[<num>]['Data'] - data da ltima modificacao
*********************************************************************************************/
function RetornaArquivosMaterialVer ($cod_curso, $dirlink)
{
  return (RetornaArrayDiretorio($dirlink));
}


/*************************************************************************************************
   RetornaEnderecosMaterial - Retorna um array com os enderecos associados ao item
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - item
   Saida: array com []['cod_endereco']
                    []['cod_item']
                    []['nome']
                    []['endereco']
                    []['status']
**************************************************************************************************/
function RetornaEnderecosMaterial ($sock, $tabela, $cod_item)
{
  $consulta="select * from ".$tabela."_itens_enderecos where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}


/*******************************************************************************************
   ApagaEndereco - apaga o endereco dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_endereco - codigo do endereco a ser apagada
   Saida: nenhuma
*******************************************************************************************/
function ApagaEndereco ($sock, $tabela, $cod_endereco)
{
  $consulta="select * from ".$tabela."_itens_enderecos where cod_endereco=".$cod_endereco;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  if ($linha['status']=="F") /* Existia antes da ediï¿½o */
  {
    $consulta="update ".$tabela."_itens_enderecos set status='A' where cod_endereco=".$cod_endereco;
  }
  else
  {
    /* Sï¿½deve ter um caso: status="I", foi inserido nesta sessï¿½ */
    $consulta="delete from ".$tabela."_itens_enderecos where cod_endereco=".$cod_endereco;
  }
  $res=Enviar($sock, $consulta);
}


/****************************************************************************************
   ApagaTodosEnderecos - apaga todos os enderecos do item dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - codigo do item a ter enderecos apagados
   Saida: nenhuma
****************************************************************************************/
function ApagaTodosEnderecos ($sock, $tabela, $cod_item)
{
  $consulta="select * from ".$tabela."_itens_enderecos where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  while($linha=RetornaLinha($res))
  {
    if ($linha['status']=="F") /* Existia antes da ediï¿½o */
    {
      $consulta="update ".$tabela."_itens_enderecos set status='A' where cod_endereco=".$linha['cod_endereco'];
    }
    else
    {
      /* Sï¿½deve ter um caso: status="I", foi inserido nesta sessï¿½ */
      $consulta="delete from ".$tabela."_itens_enderecos where cod_endereco=".$linha['cod_endereco'];
    }
    Enviar($sock, $consulta);
  }
}


/*******************************************************************************************
   InsereEndereco - Insere (ou atualiza, se ja existir) um endereco ao item dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $nome - nome do endereco
            $endereco - Endereco do item
            $cod_item - item ao qual o endereco estara associado
   Saida: nenhuma
*******************************************************************************************/
function InsereEndereco ($sock, $tabela, $nome, $endereco, $cod_item)
{
  $consulta="select * from ".$tabela."_itens_enderecos where endereco='".LimpaTitulo($endereco)."' and nome='".LimpaTitulo($nome)."' and cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $num=RetornaNumLinhas($res);
  if ($num>0)
  {
    $linha=RetornaLinha($res);
    if ($linha['status']=="A")
    {
      $consulta="update ".$tabela."_itens_enderecos set status='F' where cod_endereco=".$linha['cod_endereco'];
      $res=Enviar($sock, $consulta);
    }
  }
  else
  {
    $consulta="insert into ".$tabela."_itens_enderecos (cod_item, nome, endereco, status) values (".$cod_item.", '".$nome."', '".$endereco."', 'I')";
    $res=Enviar($sock, $consulta);
  }
}


/********************************************************************************************
   AtualizaDataEdicao - Atualiza a data de edicao do item
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - item
   Saida: nenhuma
*********************************************************************************************/
function AtualizaDataEdicao ($sock, $tabela, $cod_item)
{
  $consulta="update ".$tabela."_itens set inicio_edicao=".time()." where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
}


/*************************************************************************************************
   AtualizaMaterial - Atualiza o Titulo, o Texto e os arquivos
   Entrada: $sock - BASE DO CURSO
            $tabela - nome da tabela da ferramenta
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $titulo - titulo do material
            $texto - texto a ser colocado
            $compartilhamento - tipo de compartilhamento
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
            $dirname - nome do diretorio da ferramenta
            $cod_usuario - codigo do usuario
   Saida: false - se houve algum erro.
*************************************************************************************************/
function AtualizaMaterial($sock,$tabela,$cod_curso,$cod_item,$titulo,$texto,$compartilhamento,$diretorio_arquivos,$diretorio_temp,$dirname,$cod_usuario)
{
  /* Copiar arquivos, quando necessï¿½io... */
  $dir=$diretorio_arquivos."/".$cod_curso."/".$dirname."/";
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/".$dirname."/".$cod_item."/";

  $ret=SobrepoeDiretorio($dirtemp,$dir);

  /* Checa se existem arquivos no diretï¿½io */
  $array_temp=RetornaArrayDiretorio($dir."/".$cod_item);
    
  if ($ret && count($array_temp)==0)
  {
    if(!RemoveDiretorio($dir."/".$cod_item))
      $ret = false;
  }
  $data=time();

  $consulta="update ".$tabela."_itens set titulo='".$titulo."', texto='".$texto."', status='L',data=".$data." where cod_item=".$cod_item;
  Enviar($sock,$consulta);

  MudarCompartilhamento ($sock, $tabela, $cod_item, $compartilhamento);

  $consulta="delete from ".$tabela."_itens_enderecos where cod_item=".$cod_item." and status='A'";
  Enviar($sock,$consulta);
  $consulta="update ".$tabela."_itens_enderecos set status='F' where cod_item=".$cod_item;
  Enviar($sock,$consulta);
  $consulta="insert into ".$tabela."_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".$data.", 'F')";
  $res=Enviar($sock, $consulta);

  if ((TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22)) && (!strcmp($tabela,'Atividade')))
  {
    $consulta="select cod_avaliacao,status from Avaliacao where cod_atividade=".$cod_item." and ferramenta='P' and status !='C' and status !='X'";
    $res=Enviar($sock, $consulta);
    if (RetornaNumLinhas($res) > 0)       //existe uma avaliacao
    {
      $dados=RetornaLinha($res);
      $cod_avaliacao=$dados[0];
      /*if (strcmp($dados[1],'A'))
      {
      //Finaliza a criacao de uma avaliacao
      $consulta="update Avaliacao set status='F' where cod_atividade=".$cod_item." and ferramenta='P'";
      Enviar($sock,$consulta);
      $consulta="update Avaliacao_historicos set acao='F' where cod_avaliacao=".$dados[0]." and acao='F'";
      $res=Enviar($sock, $consulta);
      } */
      if (!strcmp($dados[1],'T'))
        ApagaAvaliacaoPortfolio($sock, $cod_avaliacao, $cod_usuario);
      if (!strcmp($dados[1],'D'))
      {
        $consulta="update Avaliacao set status='F' where cod_avaliacao=".$cod_avaliacao." and ferramenta='P' and status='D'";
        $res=Enviar($sock, $consulta);
        $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".$data.", 'F')";
        $res=Enviar($sock, $consulta);
      }
      return $ret;
    }
    return $ret;
  }
  return $ret;
}


/*******************************************************************************************
   MudaStatusEdicao - Muda p Estado do item para Em Edicao
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - item
            $cod_usuario - usuario realizando a edicao
   Saida: nenhuma
********************************************************************************************/
function MudaStatusEdicao ($sock, $tabela, $cod_item, $cod_usuario)
{
  $consulta="update ".$tabela."_itens set status='E', cod_usuario=".$cod_usuario.", inicio_edicao=".time()." where cod_item=".$cod_item;
  $res=Enviar($sock,$consulta);
  $consulta="insert into ".$tabela."_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'E')";
  $res=Enviar($sock,$consulta);
}

/* INICIO - funcoes utilizadas pelo Ajax */


/* FIM - funcoes utilizadas pelo Ajax */


/***************************************************************************************
   CancelaEdicao - Cancela a edicao de um item (termina os diretórios tb).
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $dir - diretorio da ferramenta (atividade,apoio,leitura,obrigatoria)
            $cod_item - codigo do item
            $cod_usuario - usuario cancelando a edicao
            $cod_curso - codigo do curso
            $diretorio_arquivos - diretorio dos arquivos definitivos
            $diretorio_temp - diretorio temporario da edicao
   Saida: true se a edição foi cancelada corretamente
***************************************************************************************/
function CancelaEdicao ($sock, $tabela, $dir, $cod_item, $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_avaliacao)
{
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/".$dir."/".$cod_item."/";
  if (ExisteArquivo($dirtemp))
  {
    if (!RemoveDiretorio($dirtemp))
      return false;
  }
  $linha=RetornaDadosDoItem($sock, $tabela, $cod_item);
  if ($linha['status']=="C")
  {

    $consulta="delete from ".$tabela."_itens_enderecos where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);

    $consulta="delete from ".$tabela."_itens_historicos where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);

    $consulta="delete from ".$tabela."_itens where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);

     if (!strcmp($tabela,'Atividade'))
    {
      $consulta="delete from Avaliacao where cod_atividade=".$cod_item." and ferramenta='P' and status!='X'";    //quando um item ï¿½criado as avaliacoes podem estar em 3 status: E,A,C,F.Cancelar edicao, deleta qualquer uma delas
       $res=Enviar($sock, $consulta);
    }
    return true;
  }
  if ($linha['status']=="E")
  {
    $consulta="delete from ".$tabela."_itens_enderecos where cod_item=".$cod_item." and status='I'";
    $res=Enviar($sock, $consulta);
    $consulta="update ".$tabela."_itens_enderecos set status='F' where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);

    $consulta="insert into ".$tabela."_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'D')";
    $res=Enviar($sock, $consulta);

    $consulta="update ".$tabela."_itens set status='L' where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);

    if (( TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22)) && (!strcmp($tabela,'Atividade')))
    {
      if ($criacao_avaliacao)
      {
        $consulta="delete from Avaliacao where cod_atividade=".$cod_item." and ferramenta='P' and status!='X'";
        $res=Enviar($sock, $consulta);
      }
      else
      {
        $consulta="delete from Avaliacao where cod_atividade=".$cod_item." and ferramenta='P' and status='C'";
        $res=Enviar($sock, $consulta);

        //$consulta="delete from Avaliacao where cod_atividade=".$cod_item." and ferramenta='P' and status='T'";
        //$res=Enviar($sock, $consulta);

        $consulta="update Avaliacao set status='F' where cod_atividade=".$cod_item." and ferramenta='P' and status='E'";
        $res=Enviar($sock, $consulta);

        $consulta="update Avaliacao set status='F' where cod_atividade=".$cod_item." and ferramenta='P' and status='T'";
        $res=Enviar($sock, $consulta);

        $consulta="delete from Avaliacao where cod_atividade=".$cod_item." and ferramenta='P' and status='D'";
        $res=Enviar($sock, $consulta);
      }
    }
    return true;
  }
}


/*********************************************************************************************
   CriaLinkVisualizar - Cria link simbolico para dinamica e retorna o link
   Entrada: $sock - BASE DO CURSO
            $dirname - nome do diretorio da ferramenta
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_item - codigo do item
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
   Saida: caminho relativo
*********************************************************************************************/
function CriaLinkVisualizar($sock,$dirname,$cod_curso,$cod_usuario,$cod_item,$diretorio_arquivos,$diretorio_temp)
{
  /* Busca Arquivo a ser mostrado */
  unset ($caminho);
  $dir=$diretorio_arquivos."/".$cod_curso."/".$dirname."/".$cod_item."/";
/*
  $lista=RetornaArrayDiretorio($dir);
  foreach($lista as $cod => $linha)
    if ($linha['Status'] && $linha['Arquivo']!="")
      $arquivo=$linha['Diretorio']."/".$linha['Arquivo'];
*/

  /* Cria link simbï¿½ico (apaga antigo, se houver)*/
  if ($cod_usuario<0)
    $cod_usuario=0;
  $dirlink=$diretorio_temp."/".$dirname."_".$cod_curso."_".$cod_usuario;
  $dirlinkpath="../../diretorio/".$dirname."_".$cod_curso."_".$cod_usuario;

  if (ExisteArquivo($dirlink))
    RemoveArquivo($dirlink);

  CriaLinkSimbolico($dir,$dirlink);

  $retorno['diretorio']=$dirlink."/";
  $retorno['link']=$dirlinkpath."/";

  return ($retorno);
}


/******************************************************************************************
   EncerrarExibicaoArquivo - Remove o link simbolico criado para
                             visualizacao dos itens
   Entrada: $sock - BASE DO CURSO
            $dirname - nome do diretorio da ferramenta
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_item - codigo do item
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
   Saida: True se link removido corretamente
   Obs.: Alguns parametros nao sao mais usados... Entretanto, alterar a
         funcao teria impacto em diversas paginas
******************************************************************************************/
function EncerrarExibicaoArquivo($sock,$dirname,$cod_curso,$cod_usuario,$cod_item,$diretorio_arquivos)
{
  /* Cria link simbï¿½ico (apaga antigo, se houver)*/
  if ($cod_usuario<0)
    $cod_usuario=0;
  $dirlink="../../diretorio/".$dirname."_".$cod_curso."_".$cod_usuario;
  return (RemoveArquivo($dirlink));
}


/**************************************************************************************
   RetornaNumArquivosVisiveis - Retorna o número de arquivos visiveis ao usuario
   Entrada: $lista_arq - lista ed arquivos
   Saida: array com lista de arquivos
***************************************************************************************/
function RetornaNumArquivosVisiveis($lista_arq)
{
  $conta_arq=0;

  if (count($lista_arq)>0)
  {
    foreach($lista_arq as $cod => $linha){
      if((!$linha['Status']) &&  ($linha['Arquivo']!="")) {
        $conta_arq++;
      }
    }

  }
  return($conta_arq);
}


/************************************************************************************************************
   haArquivosVisiveisDir - verifica se ha arquivo visivel dentro da pasta
   Entrada: $lista_arq_vis - vetor retornado da funcao RetornaArquivosVisiveis
   Saida: Retorna true se existe algum arquivo nao oculto dentro da pasta ou das subpastas, false caso               contrario
*************************************************************************************************************/
function haArquivosVisiveisDir($dir, $lista_arq_vis){    
    foreach($lista_arq_vis as $cod => $linha)
    { 
	if(!($linha['Status'])){
		if (($linha['Arquivo']!="") && (strncmp($linha['Diretorio'], $dir, strlen($dir)) == 0)){
			return true;
		}
	}
    }
    
    return false;
}


/********************************************************************************************
   RetornaMaiorData - Retorna a maior data dentro de um topico ou subtopico
   Entrada: $sock - Conexao com a base interna
            $tabela - Tabela usada
            $cod_topico - codigo do topico a ser checado
            $tipo_comp - tipo do compartilhamento (F ou T)
            $data - Data do topico
   Saida: maior data
********************************************************************************************/
function RetornaMaiorData($sock,$tabela,$cod_topico,$tipo_comp,$data)
{
  $query="select cod_topico,data from ".$tabela."_topicos where cod_topico_pai=".$cod_topico;
  if ($tipo_comp=="T")
    $query.=" and tipo_compartilhamento='T'";
  $res=Enviar($sock,$query);
  $lista_topicos=RetornaArrayLinhas($res);

  /* Verifica se existem subtópicos */
  if (count($lista_topicos)>0)
  {
    foreach($lista_topicos as $cod => $linha)
    {
      $data_tmp=RetornaMaiorData($sock,$tabela,$linha['cod_topico'],$tipo_comp,$linha['data']);
      if ($data_tmp>$data)
        $data=$data_tmp;
    }
  }

  $query="select max(data) from ".$tabela."_itens where cod_topico=".$cod_topico;
  if ($tipo_comp=="T")
    $query.=" and tipo_compartilhamento='T'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha[0]>$data)
    $data=$linha[0];

  return $data;
}


/**************************************************************************************************
  ApagaAvaliacaoPortfolio - Muda o status da Avaliaï¿½o associada ao portfïolio
    para 'A', impedindo que seja visualizada.
  Entradas: $sock - sock de conexao,
            $cod_avaliacao - codigo da avaliacao.
  Saida:    true se bem-sucedido, do contrario, false.
*************************************************************************************************/
function ApagaAvaliacaoPortfolio($sock, $cod_avaliacao, $cod_usuario)
{
  $query = "update Avaliacao set status='A' where cod_avaliacao=".$cod_avaliacao;
  $res = Enviar($sock, $query);
   $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'A')";
  $res=Enviar($sock,$consulta);
  return($res);
}


/***********************************************************************************************
   ImportarMateriaisObterRecur - importar materiais
   Entrada: $sock - conexao com a base interna
            $cod_curso_origem - codigo do curso cujos itens serao importados
            $flag_curso_compartilhado - booleano, true se o curso foi escolhido
              na lista de cursos com itens compartilhados.
            $nome_tabela - nome da tabela usada
            $dirname - nome do diretorio da ferramenta
            $diretorio_arquivos_origem - caminho dos arquivos do curso cujos
                    itens serao importados
            $cod_topico_origem - codigo do topico a ser listado
            $tipo_comp - tipo do compartilhamento (F ou T)
            $data - Data do todico
   Saida: ['NOME'] - nome do topico
          ['SUB'][] - array de elementos desta estrutura (recursivo)
          ['ITENS'][j][*] - dados de um item
                   [j]['CAMINHO'] - caminho dos arquivos do item
                   [j]['ENDERECOS'][k][*] - dados de um endereo
**********************************************************************************************/
function ImportarMateriaisObterRecur($sock, $cod_curso_origem, $flag_curso_compartilhado,
                                     $nome_tabela, $dirname,
                                     $diretorio_arquivos_origem, $cod_topico_origem)
{

  $query = "select topico
              from ".$nome_tabela."_topicos
              where cod_topico = ".$cod_topico_origem;

  $id = Enviar($sock, $query);
  $linha = RetornaLinha($id);
  $nome_topico = $linha['topico'];

  $resultado['NOME'] = $nome_topico;

  $query = "select cod_topico, topico
              from ".$nome_tabela."_topicos
              where cod_topico_pai = ".$cod_topico_origem;

  $id = Enviar($sock, $query);
  $total_subtopicos = RetornaNumLinhas($id);

  if ($total_subtopicos > 0)
  {
    $linhas_subtopicos = RetornaArrayLinhas($id);

    foreach ($linhas_subtopicos as $idx => $dados_subtopico)
    {
      $resultado['SUB'][] =
        ImportarMateriaisObterRecur($sock, $cod_curso_origem, $flag_curso_compartilhado,
                                    $nome_tabela, $dirname,
                                    $diretorio_arquivos_origem, $dados_subtopico['cod_topico']);
    }
  }
  else
    $resultado['SUB'] = array(); // vazio

//  if ($flag_curso_compartilhado)
//    $tipo_compartilhamento = "'T'";
//  else
    $tipo_compartilhamento = "'T', 'F'";

  $query = "select cod_item, titulo,
                   texto, tipo_compartilhamento, data, data_ativo,
                   data_inativo, posicao_item, status, inicio_edicao
              from ".$nome_tabela."_itens
              where cod_topico = ".$cod_topico_origem." and
                    (status = 'L' or status = 'E') and
                    tipo_compartilhamento in (".$tipo_compartilhamento.")";

  $id = Enviar($sock, $query);
  $total_itens = RetornaNumLinhas($id);

  if ($total_itens > 0)
  {
    $linhas_itens = RetornaArrayLinhas($id);

    unset($caminho);
    $caminho = $diretorio_arquivos_origem."/".$cod_curso_origem."/".$dirname."/";

    $k = 0;
    for ($i = 0; $i < $total_itens; $i++)
    {
      $resultado['ITENS'][$k] = $linhas_itens[$i];
      $resultado['ITENS'][$k]['CAMINHO'] =
           rtrim($caminho, "/")."/".$linhas_itens[$i]['cod_item']."/";


      $query = "select cod_endereco, nome, endereco, status
                  from ".$nome_tabela."_itens_enderecos
                  where cod_item = ".$linhas_itens[$i]['cod_item']." and
                        status = 'F'";

      $id = Enviar($sock, $query);
      $total_enderecos = RetornaNumLinhas($id);

      if ($total_enderecos > 0)
      {
        $linhas_enderecos = RetornaArrayLinhas($id);

        $resultado['ITENS'][$k]['ENDERECOS'] = $linhas_enderecos;
      }
      else
        $resultado['ITENS'][$k]['ENDERECOS'] = array();

      $k++;
    }
  }
  else
    $resultado['ITENS'] = array(); // vazio

  return ($resultado);

}


/***********************************************************************************************
   ImportarMateriaisInserirRecur -
   Entrada: $sock - conexao com a base interna
            $cod_curso_origem  -  codigo do curso de origem
            $cod_curso_destino - codigo do curso no qual serao inseridos os
                itens importados.
            $cod_usuario - codigo do usuario que esta importando.
            $nome_tabela - nome da tabela usada
            $cod_topico_destino - codigo do topico para onde serao importados
                os itens
            $dirname - nome do diretorio da ferramenta
            $diretorio_arquivos_destino - caminho dos arquivos do curso para onde
                serao importados os arquivos dos itens
            $dados_topico['NOME'] - nome do topico
                         ['SUB'][] - array de elementos desta estrutura (recursivo)
                         ['ITENS'][j][*] - dados de um item
                                  [j]['CAMINHO'] - caminho dos arquivos do item
                                  [j]['ENDERECOS'][k][*] - dados de um endereo
   Saida: nenhuma
**************************************************************************************************/
function ImportarMateriaisInserirRecur($sock, $cod_curso_origem, $cod_curso_destino, $cod_usuario, $nome_tabela,
                                       $cod_topico_destino, $dirname, $diretorio_arquivos_destino,
                                       $dados_topico, $flag_curso_extraido)
{
  $flag = true;

  $novo_cod_topico = ImportarTopico($sock, $nome_tabela, $cod_topico_destino, $dados_topico['NOME'],                                                $cod_usuario);

  $total_subtopicos = count($dados_topico['SUB']);
  if ($total_subtopicos > 0)
  {
    foreach ($dados_topico['SUB'] as $idx => $dados_subtopico)
    {
      $flag = $flag && ImportarMateriaisInserirRecur($sock, $cod_curso_origem,$cod_curso_destino, $cod_usuario,                    $nome_tabela, $novo_cod_topico, $dirname, $diretorio_arquivos_destino, $dados_subtopico, $flag_curso_extraido);
    }
  }

  $total_itens = count($dados_topico['ITENS']);
  if ($total_itens > 0 )
  {
    for ($k = 0; $k < $total_itens; $k++)
    {
      $flag = $flag && ImportarItem($sock, $cod_curso_origem, $cod_curso_destino, $cod_usuario,
                   $nome_tabela, $novo_cod_topico, $dirname,
                   $diretorio_arquivos_destino, $dados_topico['ITENS'][$k], $flag_curso_extraido);
    }
  }

  return($flag);
}


/*************************************************************************************************
   ImportarMateriais - importar materiais
   Entrada: $sock - conexao com a base interna
            $cod_curso_destino - codigo do curso no qual serao inseridos os
                itens importados.
            $cod_topico_destino - codigo do topico para onde serao importados
                os itens
            $cod_usuario - codigo do usuario que esta importando.
            $cod_curso_origem - codigo do curso cujos itens serao importados.
            $flag_curso_extraido - booleano, true se o curso foi extraido
            $flag_curso_compartilhado - booleano, true se o curso foi selecionado
               da lista de cursos compartilhados
            $array_topicos_origem - array de cod_topico a ser importado
                selecionado pelo usuario.
            $array_itens_origem - array de cod_item a ser importado
                selecionado pelo usuario.
            $nome_tabela - nome da tabela usada
            $dirname - nome do diretorio da ferramenta
            $diretorio_arquivos_destino - caminho dos arquivos do curso para onde
                serao importados os arquivos dos itens
            $diretorio_arquivos_origem - caminho dos arquivos do curso cujos itens
                serao importados.
   Saida: nenhuma
***********************************************************************************************/
function ImportarMateriais($cod_curso_destino, $cod_topico_destino, $cod_usuario,
                           $cod_curso_origem, $flag_curso_extraido, $flag_curso_compartilhado,
                           $array_topicos_origem, $array_itens_origem, $nome_tabela,
                           $dirname, $diretorio_arquivos_destino, $diretorio_arquivos_origem)
{

  $flag1 = $flag2 = true;

  if ($flag_curso_extraido)
    $tmp = TMPDB;
  else
    $tmp = "";

  // Obtï¿½ os itens e tï¿½icos a serem importados
  $sock = Conectar($cod_curso_origem, $tmp);

  // Obtï¿½ os tï¿½icos e seus contedos a serem importados recursivamente
  if (count($array_topicos_origem) > 0)
  {
    foreach($array_topicos_origem as $idx => $cod_topico_origem)
    {
      $res['SUB'][] = ImportarMateriaisObterRecur($sock, $cod_curso_origem, $flag_curso_compartilhado,
                                                  $nome_tabela, $dirname,
                                                  $diretorio_arquivos_origem, $cod_topico_origem);
    }
  }
  else
  {
    $res['SUB'] = array();
  }

  if (count($array_itens_origem) > 0)
  {
    $cod_itens_origem = implode(",", $array_itens_origem);

//    if ($flag_curso_compartilhado)
//      $tipo_compartilhamento = "'T'";
//    else
      $tipo_compartilhamento = "'T', 'F'";


    // Obtï¿½ os itens a serem importados
    $query = "select cod_item, titulo,
                     texto, tipo_compartilhamento, data, data_ativo,
                     data_inativo, posicao_item, status, inicio_edicao
                from ".$nome_tabela."_itens
                where cod_item in (".$cod_itens_origem.") and
                      (status = 'L' or status = 'E') and
                      tipo_compartilhamento in (".$tipo_compartilhamento.")";

    $id = Enviar($sock, $query);
    $total_itens = RetornaNumLinhas($id);

    if ($total_itens > 0)
    {
      $linhas_itens = RetornaArrayLinhas($id);

      unset($caminho);
      $caminho = $diretorio_arquivos_origem."/".$cod_curso_origem."/".$dirname."/";

      $k = 0;
      for ($i = 0; $i < $total_itens; $i++)
      {
        $res['ITENS'][$k] = $linhas_itens[$i];
        $res['ITENS'][$k]['CAMINHO'] =
             rtrim($caminho, "/")."/".$linhas_itens[$i]['cod_item']."/";

        $query = "select cod_endereco, nome, endereco, status
                    from ".$nome_tabela."_itens_enderecos
                    where cod_item = ".$linhas_itens[$i]['cod_item']." and
                          status = 'F'";

        $id = Enviar($sock, $query);
        $total_enderecos = RetornaNumLinhas($id);

        if ($total_enderecos > 0)
        {

          $linhas_enderecos = RetornaArrayLinhas($id);

          $res['ITENS'][$k]['ENDERECOS'] = $linhas_enderecos;
        }
        else
          $res['ITENS'][$k]['ENDERECOS'] = array();

        $k++;
      }
    }
    else
      $res['ITENS'] = array();
  }
  else
    $res['ITENS'] = array();

  Desconectar($sock);

  // Importa para a base de dados do CURSO destino
  $sock = Conectar($cod_curso_destino);

  // Importamos os tï¿½icos selecionados,
  // recursivamente.
  if (count($res['SUB']) > 0)
  {
    foreach($res['SUB'] as $idx => $dados)
    {
      $flag1 = $flag1 && ImportarMateriaisInserirRecur($sock, $cod_curso_origem, $cod_curso_destino,                  $cod_usuario, $nome_tabela,$cod_topico_destino, $dirname, $diretorio_arquivos_destino,$dados, $flag_curso_extraido);
    }
  }

  // Se houve itens selecionados no tï¿½ico atual,
  // importamos.
  if (count($res['ITENS']) > 0)
  {
    foreach($res['ITENS'] as $idx => $dados)
    {
      $flag2 = $flag2 && ImportarItem($sock, $cod_curso_origem, $cod_curso_destino, $cod_usuario,
                   $nome_tabela, $cod_topico_destino, $dirname,
                   $diretorio_arquivos_destino, $dados, $flag_curso_extraido);
    }
  }
  Desconectar($sock);

  return($flag1 && $flag2);
}


/***************************************************************************************************
   ImportarTopico - importar topico
   Entrada: $sock - conexao com a base interna
            $cod_curso_destino - codigo do curso no qual serao inseridos os
                                 itens importados.
            $nome_tabela - nome da tabela usada
            $cod_topico_destino - codigo do topico onde sera importado o topico.
            $novo_nome - nome do topico
            $cod_usuario - codigo do usuario que esta importando.
   Saida: nenhuma
**************************************************************************************************/
function ImportarTopico($sock, $nome_tabela, $cod_topico_destino, $novo_nome, $cod_usuario)
{

  $nome_livre = $nome_item = ConverteAspas2BarraAspas($novo_nome);
  $cont = 1;
  $complemento="";

  do{
    //verificar se existe nome igual, se tiver, acrescentar (2) ou (3) ou ... (x) no final
    $query = "select * from ".$nome_tabela."_topicos where topico like '".$nome_item.$complemento."' and cod_topico_pai='".$cod_topico_destino."'";
    $res = Enviar($sock, $query);

    $nome_livre = $nome_item.$complemento;
    $complemento = "(".$cont.")";
    $cont++;

  }while(RetornaLinha($res) != "");

  return(CriarTopico($sock, $nome_tabela, $cod_topico_destino, $nome_livre, $cod_usuario));
}


/**************************************************************************************
   ImportarItem - importar item
   Entrada: $sock - conexao com a base interna
            $cod_curso_origem  -  codigo do curso de origem da importacao
            $cod_curso_destino - codigo do curso no qual serao inseridos os
                itens importados.
            $cod_usuario - codigo do usuario que esta importando.
            $nome_tabela - nome da tabela usada.
            $cod_topico_destino - codigo do topico onde sera importado
                o item.
            $dirname - nome da pasta da ferramenta
            $diretorio_arquivos_destino - caminho da pasta de arquivos
               para onde serao importados os itens
            $dados_item[j][*] - dados de um item
                       [j]['CAMINHO'] - caminho dos arquivos do item
                       [j]['ENDERECOS'][k][*] - dados de um endereo
   Saida: nenhuma

**************************************************************************************/
function ImportarItem($sock, $cod_curso_origem, $cod_curso_destino, $cod_usuario,
                      $nome_tabela, $cod_topico_destino, $dirname,
                      $diretorio_arquivos_destino, $dados_item, $flag_curso_extraido)
{

  $flag1 = $flag2 = $flag3 = $flag4 = $flag5 = true;
  
  $cod_item = RetornaProximoCodigo($sock, $nome_tabela."_itens");
  $posicao = UltimaPosicaoLivre($sock, $nome_tabela, $cod_topico_destino);

  $horario = time();

  // Importamos o item compartilhado apenas com formadores
  // e com status "livre" ('L') de ediï¿½o.
  $consulta = "insert into ".$nome_tabela."_itens
                   (cod_item, cod_topico, cod_usuario,
                    titulo, texto, tipo_compartilhamento,
                    data, data_ativo, data_inativo,
                    posicao_item, status, inicio_edicao)
                 values (".$cod_item.", ".$cod_topico_destino.", ".$cod_usuario.",
                         '".ConverteAspas2BarraAspas($dados_item['titulo'])."', '".ConverteAspas2BarraAspas($dados_item['texto'])."', 'F',
                         ".$horario.", NULL, NULL,
                         ".$posicao.", 'L', ".$horario.")";
  $flag1 = Enviar($sock, $consulta);

  // Importamos os endereï¿½s do item
  if (count($dados_item['ENDERECOS']) > 0)
  {
    foreach ($dados_item['ENDERECOS'] as $idx => $dados_endereco)
    {
      $consulta = "insert into ".$nome_tabela."_itens_enderecos
                       (cod_item, nome,
                        endereco, status)
                     values (".$cod_item.", '".$dados_endereco['nome']."',
                             '".$dados_endereco['endereco']."', 'F')";
      $flag2 = $flag2 && Enviar($sock, $consulta);
    }
  }
  $consulta = "insert into ".$nome_tabela."_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'C')";
  $flag3 = Enviar($sock, $consulta);

  if (strcmp($nome_tabela, "Atividade") == 0) {
    $flag4=ImportarAvaliacao($sock, $cod_curso_origem, $cod_curso_destino, $cod_usuario, $dados_item['cod_item'], $cod_item, $flag_curso_extraido);
  }

  // Se o item a ser importado possui arquivos anexados,
  // entï¿½ copiamos seus arquivos.
  $tes = RetornaArrayDiretorio($dados_item['CAMINHO']);
// echo(file_exists($dados_item['CAMINHO'])); echo("<BR><BR><BR>"); var_dump($tes); exit();
  if (count($tes)!="")
  {
    // Criamos os diretï¿½ios onde ficarï¿½ os arquivos do item
    $arquivos_ferramenta = $diretorio_arquivos_destino."/".$cod_curso_destino."/".$dirname."/";
    if (!ExisteArquivo($arquivos_ferramenta))
      CriaDiretorio($arquivos_ferramenta);

    $arquivos_item = $diretorio_arquivos_destino."/".$cod_curso_destino."/".$dirname."/".$cod_item."/";
    if (!ExisteArquivo($arquivos_item))
      CriaDiretorio($arquivos_item);
    $flag5 = CopiaArquivosDiretorio($dados_item['CAMINHO'], $arquivos_item);
  }
  return($flag1 && $flag2 && $flag3 && $flag4 &&  $flag5);
}


/*************************************************************************************************
   ImportarAvaliacao - importar avaliacao da atividade 
   Entrada: $sock - conexao com a base interna
            $cod_curso_origem - codigo do cuso de origem
            $cod_curso_destino - codigo do curso no qual serao inseridos os
                itens importados.
            $cod_usuario - codigo do usuario que esta importando.
            $cod_atividade - codigo da atividade que esta sendo importada no curso origem
	    $cod_item - codigo da atividade no curso destino
   Saida: nenhuma
*************************************************************************************************/
function ImportarAvaliacao($sock, $cod_curso_origem, $cod_curso_destino, $cod_usuario, $cod_atividade, $cod_item, $flag_curso_extraido)
{

   if ($flag_curso_extraido)
     $tmp = TMPDB;
   else
     $tmp = "";
  
   $sock = Conectar($cod_curso_origem, $tmp);
   $query = "select cod_avaliacao, tipo, valor, status, data, data_inicio, data_termino, inicio_edicao, criterios, objetivos from Avaliacao where cod_atividade=".$cod_atividade." and ferramenta='P' and (status='F' or status='E')";
   $res = Enviar($sock, $query);
   $sock = Conectar($cod_curso_destino);
   $num = RetornaNumLinhas($res);

   if($num >0)
   {
     $lista=RetornaLinha($res);
     if($lista['status']=='E')
     {
      $linha_hist=RetornaUltimaPosicaoHistoricoAvaliacao($sock, 'Avaliacao_historicos',$lista['cod_avaliacao']);
       if (($cod_usuario!=$dados['Cod_usuario']) && ($linha_hist['data']>time()-1800))
          return false;
       CancelaEdicaoAvaliacao($sock, $tabela, $cod_avaliacao, $cod_usuario);
     }
   }
   else
     return true;

     $t=time();
     $cod_avaliacao=RetornaProximoCodigo($sock,'Avaliacao');
     $insert="insert into Avaliacao (cod_avaliacao, cod_atividade, cod_usuario, ferramenta, tipo, valor, ";          $insert.="status, data, data_inicio, data_termino, inicio_edicao, criterios, objetivos) values (";              $insert.=$cod_avaliacao.",".$cod_item.", ".$cod_usuario.", 'P','".$lista['tipo']."',".$lista['valor'].", ";
     $insert.="'F', ".$t.", ".$lista['data_inicio'].", ".$lista['data_termino'].", ".$t.", '";
     $insert.=$lista['criterios']."', '".$lista['objetivos']."')";
     $res1 = Enviar($sock, $insert);

     $insert_hist="insert into Avaliacao_historicos (cod_avaliacao, cod_usuario, data, acao) values (";
     $insert_hist.=$cod_avaliacao.", ".$cod_usuario.", ".$t.", 'C')";
     $res2 = Enviar($sock, $insert_hist);
   return $res1 && $res2;
}


/*************************************************************************************************
   EPaiTopicos - Retorna verdadeiro se algum elemento de v_topicos for descendente de $cod_topico_dest
   Entrada: $sock - BASE DO CURSO
            $cod_topico_dest - codigo do topico pra onde serao movidos os v_topicos
            $v_topicos - codigos do topicos
   Saida: true, se For pai, false caso contrario
************************************************************************************************/
function EPaiTopicos($sock, $tabela, $cod_topico_dest, $v_topicos)
{
  for($i = 0; $i < count($v_topicos); $i++)
  {
    if(EPai($sock, $tabela, $v_topicos[$i], $cod_topico_dest))
      return true;
  }
  return false;
}


/**********************************************************************************************************
   NaoExisteTopPeloCod - Retorna verdadeiro se nao existir uma pasta com mesmo código que novo_nome, com                           mesmo codigo_topico_pai e cod_usuario na $tabela
   Entrada: $sock - BASE DO CURSO
	    $tabela - tabela onde a pasta foi criada, ou seja, onde sera feita a consulta
            $cod_topico_pai - codigo do topico onde se encontra
            $topico - codigo da pasta
            $cod_usuario - codigo do usuario
            $cod_topico - (opcional) - verifica se é diferente do cod_topico
   Saida: true, se nao existir topico com mesmo código, false caso contrario
***********************************************************************************************************/
function NaoExisteTopPeloCod($sock, $tabela, $cod_topico_pai, $topico, $cod_usuario, $cod_topico='')
{
  if (is_numeric($topico))
  {
    $consulta = "select topico from ".$tabela."_topicos where cod_topico = ".$topico;
    $res = Enviar($sock, $consulta);
    $linha = RetornaLinha($res);
    $topico = $linha['topico'];
  }

  return NaoExisteTopPeloTexto($sock, $tabela, $cod_topico_pai, $topico, $cod_usuario, $cod_topico);
}


/*********************************************************************************************************
   NaoExisteTopPeloTexto - Retorna verdadeiro se nao existir uma pasta com mesmo nome que novo_nome, com                             mesmo codigo_topico_pai e cod_usuario na $tabela
   Entrada: $sock - BASE DO CURSO
	    $tabela - tabela onde a pasta foi criada, ou seja, onde sera feita a consulta
            $cod_topico_pai - codigo do topico onde se encontra
            $topico - nome codigo da pasta
            $cod_usuario - codigo do usuario
            $cod_topico - (opcional) - verifica se é diferente do cod_topico
   Saida: true, se nao existir topico com mesmo nome, false caso contrario
*********************************************************************************************************/
function NaoExisteTopPeloTexto($sock, $tabela, $cod_topico_pai, $topico, $cod_usuario, $cod_topico='')
{
  $consulta = "select topico from ".$tabela."_topicos where cod_topico_pai = ".$cod_topico_pai." and topico = '".$topico."'";

  if($cod_topico!= ''){
    $consulta.=" and cod_topico<>".$cod_topico;
  }

  $res = Enviar($sock, $consulta);
  $num = RetornaNumLinhas($res);

  if ($num > 0)
    return false;
  return true;
}


/**************************************************************************************************
  ExistemItensVisiveis - retorna verdadeiro se existem itens visiveis
  Entrada: $sock - sock de entrada
           $tabela - tabela onde a pasta foi criada, ou seja, onde sera feita a consulta
           $cod_topico - codigo do topico
           $eformador - indica se o usuario é formador
  Saida: True se tiver itens visiveis, false caso contrario
**************************************************************************************************/
function ExistemItensVisiveis($sock, $tabela, $cod_topico, $eformador)
{
  $consulta="select * from ".$tabela."_itens where cod_topico=".$cod_topico;
  
  if(!$eformador)
    $consulta.=" and tipo_compartilhamento<>'F'";

  $consulta.=" order by posicao_item";
    
  $res=Enviar($sock, $consulta);
  $lista=RetornaNumLinhas($res);

  if($lista == 0) return false;

  return true;
}


/********* funções AJAX **********************/

/**************************************************************************************************
  MoverItensDinamic - mover itens dinamicamente
  Entrada: $tabela - tabela onde a pasta foi criada, ou seja, onde sera feita a consulta
           $cod_curso - codigo do curso
           $cod_ferramenta - codigo da ferramenta
           $cod_usuario - codigo do usuario
           $cod_topico_ant - codigo do topico anterior
           $cod_topico_raiz - codigo do topico raiz
           $cod_topicos - codigo dos topicos
           $cod_itens - codigo dos itens
  Saida: XML da funcao ajax
**************************************************************************************************/
function MoverItensDinamic($tabela, $cod_curso, $cod_ferramenta, $cod_usuario, $cod_topico_ant, $cod_topico_raiz, $cod_topicos, $cod_itens){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  if (!empty($cod_topicos))
  {
  
    for($i = 0; $i < count($cod_topicos); $i++)
//       $cod_topicos[$i] = trim($cod_topicos[$i]);
    
    if (EPaiTopicos($sock, $tabela, $cod_topico_raiz, $cod_topicos))
    /* 2 - Você não pode mover uma pasta para ela mesma ou para uma subpasta dela. */
    /* 72- Verifique as pastas selecionadas.  */
      $objResponse->addAlert(RetornaFraseDaLista($lista_frases,2)."\n".RetornaFraseDaLista($lista_frases_geral,72));
    else
    {
      $existe = 0;
      for($i = 0; $i < count($cod_topicos); $i++)
      {
        if (!NaoExisteTopPeloCod($sock, $tabela, $cod_topico_raiz, $cod_topicos[$i], $cod_usuario))
          $existe = 1;
      }

      if (!$existe)
      {
        // move todos os topicos selecionados
        for($i = 0; $i < count($cod_topicos); $i++)
        {
          MoverTopico($sock, $tabela, $cod_topicos[$i], $cod_usuario, $cod_topico_raiz);
        }
        // se tiver itens selecionados, move-os também
        if (count($cod_itens))
        {

          for($i = 0; $i < count($cod_itens); $i++)
          {
            MoverItem($sock, $tabela, $cod_itens[$i], $cod_usuario, $cod_topico_raiz);
          }
        }
        $objResponse->addScript('Redirecionar('.$cod_topico_ant.');');
      }      
      else //existe topico com mesmo nome no diretorio destino
      {
        /* 71 (ger) - Não foi possível mover a pasta, pois já existe uma pasta com mesmo nome no diretório destino. */
        /* 72 (ger) - Verifique as pastas selecionadas.  */
        $objResponse->addAlert(RetornaFraseDaLista($lista_frases_geral,71)."\n".RetornaFraseDaLista($lista_frases_geral,72));
      }

    }
  }
  else
  {
    if (!empty($cod_itens))
    {

      if(is_array($cod_itens)){
      for($i = 0; $i < count($cod_itens); $i++){
          MoverItem($sock, $tabela, $cod_itens[$i], $cod_usuario, $cod_topico_raiz);
        }
      }else{
        MoverItem($sock, $tabela, $cod_itens, $cod_usuario, $cod_topico_raiz);
      }
    }
    $objResponse->addScript('window.location="material.php?cod_curso='.$cod_curso.'&cod_usuario='.$cod_usuario.'&cod_ferramenta='.$cod_ferramenta.'&cod_topico_raiz='.$cod_topico_raiz.'";');

  }

  ArrumaPosicoes($sock, $tabela, $cod_topico_raiz);
  ArrumaPosicoes($sock, $tabela, $cod_topico_ant);

  Desconectar($sock);

  return $objResponse->getXML();
}


/*******************************************************************************************************
  RetornaFraseDinamic - retorna as frases dinamicamente
  Entrada: $variavel - variavel
           $cod_ferramenta - codigo da ferramenta
  Saida: XML da funcao ajax
*******************************************************************************************************/
function RetornaFraseDinamic($variavel, $cod_ferramenta){
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock, $cod_ferramenta);

  Desconectar($sock);

  $retorno="{\n";

  foreach ($lista_frases as $cod => $linha){
    $retorno.='"msg'.$cod.'":"'.$linha.'", ';
  }
 
  $retorno.="\n}";

  $objResponse->addScript($variavel." = ".$retorno.";");

  return $objResponse->getXML();
}


/********************************************************************************************************
  AtualizaPosicoes - atualiza posicoes
  Entrada: $cod_curso - codigo do curso
           $cod_usuario - codigo do usuario
           $cod_topico - codigo do topico
           $lista_ids - lista dos ids
           $tabela - tabela onde a pasta foi criada, ou seja, onde sera feita a consulta
  Saida: XML da funcao ajax
********************************************************************************************************/
function AtualizaPosicoes($cod_curso, $cod_usuario, $cod_topico, $lista_ids, $tabela){
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);
  PegaSemaforo($sock, $tabela);

  foreach($lista_ids as $cod => $linha){
    $vetor = split('_', $linha);
    if(!is_numeric($vetor[1])){
      //é um topico
      $query = "update ".$tabela."_topicos set posicao_topico = ".($cod+1)." where cod_topico=".$vetor[2];
    }else{
      $query = "update ".$tabela."_itens set posicao_item = ".($cod+1)." where cod_topico=".$cod_topico." and cod_item=".$vetor[1];
    }
//      $objResponse->addAlert($query);
    Enviar($sock, $query);
  }

  LiberaSemaforo($sock, $tabela);
  Desconectar($sock);
  return $objResponse->getXML();
}


/****************************************************************************************************
  RenomearTopicoDinamic - renomear topico dinamicamente
  Entrada: $cod_curso - codigo do curso
           $cod_topico - codigo do topico
           $cod_usuario - codigo do usuario
           $tabela - tabela onde a pasta foi criada, ou seja, onde sera feita a consulta
           $novo_nome - novo nome do topico
           $msg_erro - mensagem de erro
           $id - id
****************************************************************************************************/
function RenomearTopicoDinamic ($cod_curso, $cod_topico, $cod_usuario, $tabela, $novo_nome, $msg_erro, $id){
  $objResponse = new xajaxResponse('ISO-8859-1');
  $sock=Conectar($cod_curso);

  $novo_nome=ConverteAspas2BarraAspas(htmlentities($novo_nome));

  $consulta="select * from ".$tabela."_topicos where cod_topico=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  $cod_topico_raiz=$linha['cod_topico_pai'];

  if (NaoExisteTopPeloTexto($sock, $tabela, $cod_topico_raiz, $novo_nome, $cod_usuario, $cod_topico))
  {
    RenomearTopico ($sock, $tabela, $cod_topico, $novo_nome);
    $objResponse->addAssign("nome_topico_atual", "innerHTML", htmlentities($novo_nome));

  }else{
    $objResponse->addAlert($msg_erro);
    $objResponse->addScript("document.getElementById('nome_topico_atual').innerHTML=nome_topico_atual;");
  }
  Desconectar($sock);
  return $objResponse->getXML();

}


/*************************************************************************************************
  CriaTopicoDinamic - cria topico dinamicamente
  Entrada: $cod_curso - codigo do curso
           $cod_usuario - codigo do usuario 
	   $tabela- tabela onde a pasta foi criada, ou seja, onde sera feita a consulta
           $cod_topico_raiz - codigo do topico raiz
	   $dirname - nome do diretorio
           $novo_nome - novo nome do topico
           $msg_erro - mensagem de erro
  Saida: XML da funcao ajax 
**************************************************************************************************/
function CriaTopicoDinamic($cod_curso, $cod_usuario, $cod_ferramenta, $tabela, $cod_topico_raiz, $dirname, $novo_nome, $msg_erro){
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock = Conectar("");

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');

  Desconectar($sock);


  $sock=Conectar($cod_curso);

  if (NaoExisteTopPeloTexto($sock, $tabela, $cod_topico_raiz, $novo_nome, $cod_usuario))
  {
    $cod_topico=CriarTopico($sock, $tabela, $cod_topico_raiz, $novo_nome, $cod_usuario);
    $objResponse->addRedirect("material.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_topico_raiz=".$cod_topico."&cod_usuario=".$cod_usuario);

  }else{
    $objResponse->addAlert($msg_erro);
  }


  Desconectar($sock);
  return $objResponse->getXML();

}


/***************************************************************************************
   EditarTitulo - Edita o título do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $novo_nome - novo título do item
            $cod_usuario - código do usuário que está utilizando
   Saida: XML da função Ajax
***************************************************************************************/
function EditarTitulo ($tabela, $cod_curso, $cod_item, $novo_nome, $cod_usuario,$texto='')
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $sock=Conectar($cod_curso);
  $consulta="update ".$tabela."_itens set titulo='".htmlentities($novo_nome)."', data=".$data.", status='L' where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  // Imprime no div valores do formul?io
  $objResponse->addAssign("tr_".$cod_item, "className", "novoitem");
  $objResponse->addAssign("tit_".$cod_item, "innerHTML", htmlentities($novo_nome));
  $objResponse->addAssign("tit_".$cod_item, "className", "linkTexto");
  $objResponse->addEvent("tit_".$cod_item, "onClick", "AlteraTitulo('".$cod_item."');");
  $objResponse->addAssign("data_".$cod_item, "innerHTML", UnixTime2DataHora($data));

  AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, 1);
	if($texto!='')
  	$objResponse->addScriptCall("mostraFeedback", $texto, 'true');
  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}


/*********************************************************************************************
   EditarTexto - Edita o texto do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $novo_nome - novo texto do item
            $cod_usuario - código do usuário que está utilizando
   Saida: XML da função Ajax
*********************************************************************************************/
function EditarTexto ($tabela, $cod_curso, $cod_item, $novo_nome, $cod_usuario)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $novo_nome=ConverteAspas2BarraAspas($novo_nome);

  $sock=Conectar($cod_curso);

  $consulta="update ".$tabela."_itens set texto='".trim($novo_nome)."' where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, 1);

  // Imprime no div valores do formulário
  $objResponse->addAssign("tr_".$cod_item, "className", "novoitem");
  $objResponse->addAssign("text_".$cod_item, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas($novo_nome)), true));
  $objResponse->addAssign("data_".$cod_item, "innerHTML", UnixTime2DataHora(time()));
  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}


/***********************************************************************************************************
   InsereEnderecoDinamic - Insere (ou atualiza, se já existir) um endereco ao item dado, dinâmicamente
   Entrada: $nome - nome do endereco
            $endereco - Endereco do item
            $cod_item - item ao qual o endereco estará associado
            $cod_curso - código do curso, para conectar ao banco de dados
            $cod_usuario - código do usuário que está utilizando
   Saida: XML da função Ajax
**********************************************************************************************************/
function InsereEnderecoDinamic ($tabela, $nome, $endereco, $cod_item, $cod_curso, $cod_usuario,$texto='')
{

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="select * from ".$tabela."_itens_enderecos where endereco='".LimpaTitulo($endereco)."' and nome='".LimpaTitulo($nome)."' and cod_item=".$cod_item;

  $res=Enviar($sock, $consulta);

  $linha=RetornaLinha($res);
  if ($linha[0]!='') return $objResponse->getXML();

  $num=RetornaNumLinhas($res);
  if ($num>0)
  {
    $linha=RetornaLinha($res);
    if ($linha['status']=="A")
    {
      $consulta="update ".$tabela."_itens_enderecos set status='F' where cod_endereco=".$linha['cod_endereco'];
      $res=Enviar($sock, $consulta);
    }
  }
  else
  {
    $consulta="insert into ".$tabela."_itens_enderecos (cod_item, nome, endereco, status) values (".$cod_item.", '".$nome."', '".$endereco."', 'F')";
    $res=Enviar($sock, $consulta);
  }

  $consulta="select * from ".$tabela."_itens_enderecos where endereco='".LimpaTitulo($endereco)."' and nome='".LimpaTitulo($nome)."' and cod_item=".$cod_item;

  $res=Enviar($sock, $consulta);

  $linha=RetornaLinha($res);
  $cod_endereco=$linha['cod_endereco'];

  $objResponse->addCreate("listaEnderecos", "span", "end_".$cod_endereco);
  $objResponse->addCreate("end_".$cod_endereco, "span", "link_".$cod_endereco);
  $objResponse->addAssign("link_".$cod_endereco, "className", "link");

  if ($num==0){
    if ($nome!=''){
      $objResponse->addAssign("link_".$cod_endereco, "innerHTML", $nome);
      $objResponse->addInsertAfter("link_".$cod_endereco, "span", "endEndereco_".$cod_endereco);
      $objResponse->addAssign("endEndereco_".$cod_endereco, "innerHTML", "&nbsp;&nbsp;(".RetornaURLValida($endereco).") - \n");
    }else{
      $objResponse->addAssign("link_".$cod_endereco, "innerHTML", RetornaURLValida($endereco)." - ");
    }
  }

  $objResponse->addEvent("link_".$cod_endereco, "onClick", "WindowOpenVerURL('".ConverteSpace2Mais(RetornaURLValida($endereco))."');return(false);");

  $objResponse->addCreate("end_".$cod_endereco, "span", "endApagar_".$cod_endereco);
  $objResponse->addAssign("endApagar_".$cod_endereco, "className", 'link');
  $objResponse->addAssign("endApagar_".$cod_endereco, "innerHTML", 'Apagar<br>');
  $objResponse->addEvent("endApagar_".$cod_endereco, "onClick", "ApagarEndereco('".$cod_curso."', '".$cod_endereco."');");

  if($texto!='')
  		$objResponse->addScriptCall("mostraFeedback", $texto, 'true');
  
  Desconectar($sock);

  AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, 1);
	
  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   RetornaTexto - Retorna uma frase do bando de dados (tabela Lingua_textos)
   Entrada: $cod_lingua - lingua utilizada pelo usuario
            $cod_ferramenta - a que ferramenta pertence o texto
            $cod_texto - codigo do texto a ser devolvido
   Saida: texto desejado
*/
function RetornaTexto($cod_lingua, $cod_ferramenta, $cod_texto){
	$sock = Conectar('');
	$sql = "select texto from Lingua_textos where cod_lingua = $cod_lingua AND cod_ferramenta = $cod_ferramenta AND cod_texto = $cod_texto";
	$resp = Enviar($sock, $sql);
	$texto = RetornaLinha($resp);
	Desconectar($sock);
	return($texto[0]);
}

/***********************************************************************************************************
   InsereAvaliacaoDinamic - Insere (ou atualiza, se já existir) uma avaliacao relacionada ao item dado, dinamicamente
   Entrada: $data_inicio - Data de inicio da avaliacao
            $data_termino - Data de termino da avaliacao
            $valor - Valor da avaliacao
            $tipo_atividade - Tipo da atividade (individual ou em grupo)
            $objetivos - Objetivos da Avaliacao
            $criterios - Criterios da Avaliacao
            $cod_item - item ao qual o endereco estará associado
            $cod_curso - código do curso, para conectar ao banco de dados
            $cod_usuario - código do usuário que está utilizando
   Saida: XML da função Ajax
**********************************************************************************************************/
function InsereAvaliacaoDinamic ($data_inicio, $data_termino, $valor, $tipo_atividade, $objetivos, $criterios, $cod_item, $cod_curso, $cod_usuario)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $data = time();
  $data_inicio= Data2UnixTime($data_inicio);
  $data_termino=Data2UnixTime($data_termino);

  $sock=Conectar($cod_curso);

  $consulta="select * from Avaliacao where cod_atividade='".$cod_item."' and ferramenta='P' and status !='C' and status !='X' and status !='A'";
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);

  //Se já existe avaliação para esta atividade
  if ($linha[0]!='')
  {
    $cod_avaliacao = $linha['cod_avaliacao'];

    $consulta="update Avaliacao SET tipo='".$tipo_atividade."', valor='".$valor."', data_inicio='".$data_inicio."', data_termino='".$data_termino."', criterios='".$criterios."', objetivos='".$objetivos."' where cod_avaliacao='".$cod_avaliacao."'";
    $res=Enviar($sock, $consulta);

    if (!strcmp($linha['status'],'T'))
      ApagaAvaliacaoPortfolio($sock, $cod_avaliacao, $cod_usuario);
    else if (!strcmp($linha['status'],'D'))
    {
      $consulta="update Avaliacao set status='F' where cod_avaliacao='".$cod_avaliacao."' and ferramenta='P' and status='D'";
      $res=Enviar($sock, $consulta);
      $consulta="insert into Avaliacao_historicos values ('".$cod_avaliacao."', '".$cod_usuario."', '".$data."', 'F')";
      $res=Enviar($sock, $consulta);
    }
  }
  else
  {
    $cod_avaliacao=RetornaProximoCodigo($sock,'Avaliacao');

    $consulta="insert into Avaliacao (cod_avaliacao, cod_atividade, cod_usuario, ferramenta, tipo, valor, status, data, data_inicio, data_termino, inicio_edicao, criterios, objetivos) values (".$cod_avaliacao.", ".$cod_item.", ".$cod_usuario.", 'P', '".$tipo_atividade."', ".$valor.", 'F', ".$data.", '".$data_inicio."', '".$data_termino."', '0', '".$criterios."', '".$objetivos."')";
    $res=Enviar($sock, $consulta);
  }

  Desconectar($sock);
  
  /* Pega o texto de Feedback */
  $cod_lingua = (int) $_SESSION['cod_lingua_s'];
  $cod_ferramenta = 3;
  $cod_texto = 148;
  $texto_feedback = RetornaTexto($cod_lingua, $cod_ferramenta, $cod_texto); 
  
  /* Adiciona a chamada do feedback a resposta do xajax */
  $objResponse->addScriptCall("mostraFeedback", $texto_feedback, 'true');
  

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/***********************************************************************************************************
   ApagaAvaliacaoDinamic - Apaga uma avaliacao relacionada ao item dado, dinamicamente
   Entrada: $cod_item - item ao qual o endereco estará associado
            $cod_curso - código do curso, para conectar ao banco de dados
            $cod_usuario - código do usuário que está utilizando
   Saida: XML da função Ajax
**********************************************************************************************************/
function ApagaAvaliacaoPortfolioDinamic ($cod_item, $cod_curso, $cod_usuario)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $query="select cod_avaliacao from Avaliacao where cod_atividade=".$cod_item." and ferramenta='P' and status!='X' and status!='A'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $cod_avaliacao = $linha['cod_avaliacao'];

  ApagaAvaliacaoPortfolio($sock, $cod_avaliacao, $cod_usuario);

  Desconectar($sock);

  /* Pega o texto de Feedback */
  $cod_lingua = (int) $_SESSION['cod_lingua_s'];
  $cod_ferramenta = 3;
  $cod_texto = 149;
  $texto_feedback = RetornaTexto($cod_lingua, $cod_ferramenta, $cod_texto); 
  
  /* Adiciona a chamada do feedback a resposta do xajax */
  $objResponse->addScriptCall("mostraFeedback", $texto_feedback, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/*****************************************************************************************************
   AbreEdicao - Marca no banco de dados o início da edição do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $cod_usuario - código do usuário que está utilizando
            $cod_usuario_portfolio - código do usuário que está utilizando o portfolio
            $cod_grupo_portfolio - código do grupo o qual o usuário faz parte
            $cod_topico_raiz - código do tópico-pai
   Saida: XML da função Ajax
*****************************************************************************************************/
function AbreEdicao($tabela, $cod_curso, $cod_item, $cod_usuario, $cod_topico_raiz){

  $objResponse = new xajaxResponse('ISO-8859-1');

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $sock=Conectar($cod_curso);

  //Correção feita para não acontecer de os horários ficarem iguais no banco e atrapalhar o status do item
  $consulta="select * from ".$tabela."_itens_historicos where data=".$data." and cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  if ($linha!=""){ $data++; }
 
  $linha=RetornaDadosDoItem($sock, $tabela, $cod_item);

  $linha_historico=RetornaUltimaPosicaoHistorico($sock, $tabela, $cod_item);

  if (($linha['status']=="E")&&($cod_usuario !=$linha_historico['cod_usuario'])){
    $objResponse->addScript("window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');");

    $objResponse->addScript("document.location='material.php?cod_usuario=".$cod_usuario."&cod_curso=".$cod_curso."&cod_item=".$cod_item."'");
  }else if ($linha['status']!="E"){
    $consulta="update ".$tabela."_itens set status='E', cod_usuario=".$cod_usuario.", inicio_edicao=".$data." where cod_item=".$cod_item;
    $res=Enviar($sock,$consulta);
    $consulta="insert into ".$tabela."_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".$data.", 'E')";
    $res=Enviar($sock,$consulta);
  }

  Desconectar($sock);

  return $objResponse->getXML();
}


/*******************************************************************************************
   ExcluirArquivo - Exclui o arquivo, dinamicamente
   Entrada: $numero - numero do arquivo, no item
            $arq - endereco do arquivo no servidor
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario
   Saida: XML da função Ajax
*******************************************************************************************/
function ExcluirArquivo($tabela, $numero, $arq, $cod_curso, $cod_item, $cod_usuario,$texto=''){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $arq=htmlspecialchars_decode($arq);

  $removido = RemoveDiretorio($arq);
  //if(!$removido){
	//$objResponse->addAlert("Falha ao apagar arquivo(s). Por favor verifique se a pasta '../cursos/diretorio' está com permissão de escrita e atualize a página.");
  //}else{
  	$objResponse->addRemove('arq_'.$numero); 
  	$objResponse->AddScript('js_conta_arq=0');
  	if($texto!='')
  		$objResponse->addScriptCall("mostraFeedback", $texto, 'true'); 	
  //	}
  
  
  AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, 1);
  return $objResponse->getXML();
}


/**********************************************************************************
   ExcluirEndereco - Exclui o endereco, dinamicamente
   Entrada: $cod_curso - codigo do curso
            $cod_endereco - codigo do endereco, no item
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario
   Saida: XML da função Ajax
**********************************************************************************/
function ExcluirEndereco ($tabela, $cod_curso, $cod_endereco, $cod_item, $cod_usuario,$texto='')
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="delete from ".$tabela."_itens_enderecos where cod_endereco=".$cod_endereco;
  $res=Enviar($sock, $consulta);

  $objResponse->addRemove('end_'.$cod_endereco);

  Desconectar($sock);

  AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, 1);
  
  if($texto!='')
  	$objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  return $objResponse->getXML();
}


/*******************************************************************************************
   OcultarArquivosDinamic - Oculta os arquivos passados num array, dinamicamente
   Entrada: $nomes_arquivos - array de nomes dos arquivos
            $msg_oculto - string 'oculto'
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario

   Saida: XML da função Ajax
*******************************************************************************************/
function OcultarArquivosDinamic($tabela, $nomes_arquivos, $msg_oculto, $cod_curso, $cod_item, $cod_usuario,$texto){
  $objResponse = new xajaxResponse('ISO-8859-1');

  foreach($nomes_arquivos as $cod => $linha){
    $nome_arquivo = implode("/", explode("//", $linha[0])); 

    AlteraStatusArquivo($nome_arquivo,true);

    $objResponse->addCreate('local_oculto_'.$linha[1], 'span', 'arq_oculto_'.$linha[1]);

    $objResponse->addCreate('arq_oculto_'.$linha[1], 'span', 'arq_oculto_in1_'.$linha[1]);
    $objResponse->addAssign('arq_oculto_in1_'.$linha[1], 'innerHTML', '&nbsp;- ');

    $objResponse->addCreate('arq_oculto_'.$linha[1], 'span', 'arq_oculto_in2_'.$linha[1]);
    $objResponse->addAssign('arq_oculto_in2_'.$linha[1], 'innerHTML', $msg_oculto);
    $objResponse->addAssign('arq_oculto_in2_'.$linha[1], 'className', 'arqOculto');

    $objResponse->addScript('document.getElementById(\'nomeArq_'.$linha[1].'\').setAttribute(\'arqOculto\', \'sim\');');
  }
  $objResponse->addEvent('sArq_ocultar', 'onclick', 'Desocultar();');
  AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, 1);
  if($texto!='')
  	$objResponse->addScriptCall("mostraFeedback", $texto, 'true');
  return $objResponse->getXML();
}


/***********************************************************************************************
   DesocultarArquivosDinamic - Desoculta os arquivos passados num array, dinamicamente
   Entrada: $nomes_arquivos - array de nomes dos arquivos
            $msg_oculto - string 'oculto'
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario
   Saida: XML da função Ajax
***********************************************************************************************/
function DesocultarArquivosDinamic($tabela, $nomes_arquivos, $cod_curso, $cod_item, $cod_usuario,$texto=''){
  $objResponse = new xajaxResponse('ISO-8859-1');

  foreach($nomes_arquivos as $cod => $linha){
    $nome_arquivo = implode("/", explode("//", $linha[0])); 

    AlteraStatusArquivo($nome_arquivo,false);

    $objResponse->addRemove('arq_oculto_'.$linha[1]);
    $objResponse->addScript('document.getElementById(\'nomeArq_'.$linha[1].'\').setAttribute(\'arqOculto\', \'nao\');');

  }
  $objResponse->addEvent('sArq_ocultar', 'onclick', 'Ocultar();');
  AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, 1);
	if($texto!='')
  	$objResponse->addScriptCall("mostraFeedback", $texto, 'true');
  return $objResponse->getXML();
}


/********************************************************************************************************
   AcabaEdicaoDinamic - Marca no banco de dados o fim da edição do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $cod_usuario - código do usuário que está utilizando
            $acao - 0 se for cancelar a edição,
                    1 se for finalizar a edição.
   Saida: XML da função Ajax
********************************************************************************************************/
function AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, $acao){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  unset ($data);
  $data = time();
  // tabela onde a pasta foi criada, ou seja, onde sera feita a consulta
  //Correção feita para não acontecer de os horários ficarem iguais no banco e atrapalhar o status do item
  $consulta="select * from ".$tabela."_itens_historicos where data=".$data." and cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  if ($linha[0]!=""){ $data++; }

  $consulta="update ".$tabela."_itens set status='L', data=".$data." where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  if($acao){
    $consulta="insert into ".$tabela."_itens_historicos values ('".$cod_item."', '".$cod_usuario."', '".$data."', 'F')";
  }else{
    $consulta="insert into ".$tabela."_itens_historicos values ('".$cod_item."', '".$cod_usuario."', '".$data."', 'D')";  
  }

  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  //Correção feita para não acontecer de os horários ficarem iguais no banco e atrapalhar o status do item
   if ($linha[0]!=""){ sleep(1); }
  
  return $objResponse->getXML();
}


/*******************************************************************************
   AcabaEdicao - Marca no banco de dados o fim da edição do item dado
   Entrada: $sock - conexão com o BD
            $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $cod_usuario - código do usuário que está utilizando
            $acao - 0 se for cancelar a edição,
                    1 se for finalizar a edição.
   Saida: sem saída
*******************************************************************************/
function AcabaEdicao($tabela, $sock, $cod_curso, $cod_item, $cod_usuario, $acao){

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  unset ($data);
  $data = time();

  //Correção feita para não acontecer de os horários ficarem iguais no banco e atrapalhar o status do item
  $consulta="select * from ".$tabela."_itens where data=".$data." and cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  if ($linha!=""){ $data++; }

  $consulta="update ".$tabela."_itens set status='L', data=".$data." where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  if ($acao){
    $consulta="insert into ".$tabela."_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".$data.", 'F')";
  }else{
    $consulta="insert into ".$tabela."_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".$data.", 'D')";    
  }

  $res=Enviar($sock, $consulta);

}


/*****************************************************************************
   MoverArquivosDinamic - Move o arquivo, dinamicamente
   Entrada: $origem - endereco de origem do arquivo
            $destino - endereco de destino do arquivo
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario
            $msg_erro - mensagem de erro, caso ocorra
   Saida: XML da função Ajax
*****************************************************************************/
function MoverArquivosDinamic($origem, $destino, $cod_curso, $cod_item, $cod_usuario, $msg_erro, $cod_ferramenta, $cod_topico_raiz){
  $objResponse = new xajaxResponse('ISO-8859-1');

  // Se a origem ou destino forem vazios retorna false
  if (($origem == "") || ($destino == ""))
    return $objResponse->getXML();

  // Vari?el de retorno da fun?o
  $flag = true;

  // Resolve o caminho da origem ('realpath' testa tamb? se o arquivo
  // existe).
  $origem = realpath($origem);
  if ($origem == false){
    $objResponse->addScript("EscondeLayers();");
    $objResponse->addAlert($msg_erro);
    return $objResponse->getXML();
  }

  // Se retornar mais que um elemento (ou seja, pelo menos
  // um que n? seja o pr?rio arquivo ou pasta a ser movida)
  // ent? os movemos.
  $total = count($conteudo);

      $nome_arquivo = basename($origem);

      $dest = $destino.DIRECTORY_SEPARATOR.$nome_arquivo;

      if (ExisteArquivo($dest)){
        AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);
        $objResponse->addScript("EscondeLayers();");
        $objResponse->addScript("window.location='material.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&cod_ferramenta=".$cod_ferramenta.";");
        return $objResponse->getXML();
      }

      // O teste aqui tamb? ?necess?io para ver se 'RemoveArquivo'
      // n? retornou false.
      if ($flag)
      {
        $stat = RetornaStatusArquivo($conteudo);
        if (copy($origem, $dest))
        {
          if (AlteraStatusArquivo($dest, $stat))
          {
            if (!RemoveArquivo($origem)){
              $objResponse->addScript("EscondeLayers()");;
              $objResponse->addAlert($msg_erro);
              return $objResponse->getXML();
            }
          }
          else{
            $objResponse->addScript("EscondeLayers();");
              $objResponse->addAlert($msg_erro);
            return $objResponse->getXML();
          }
        }
        else{
          $objResponse->addScript("EscondeLayers();");
          $objResponse->addAlert($msg_erro);
          return $objResponse->getXML();
        }
      }

  clearstatcache();

  if (is_dir($origem))
    $flag = RemoveDiretorio($origem);

  $objResponse->addScript("window.location='ver.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&cod_topico_raiz='+cod_topico_ant+'&cod_usuario_portfolio='+cod_usuario_portfolio;");

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  return $objResponse->getXML();
}


/**************************************************************************
   ExcluirItensDinamic - Exclui os itens, dinamicamente
   Entrada: $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_item - array com o codigo dos itens
   Saida: XML da função Ajax
*************************************************************************/
function ExcluirItensDinamic($tabela, $cod_curso, $cod_usuario, $cod_itens){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  if(is_array($cod_itens)){
    foreach($cod_itens as $cod => $linha){
      ExcluirItem($sock, $tabela, $linha, $cod_usuario);
    }
  }else if($cod_itens!=""){
    ExcluirItem($sock, $tabela, $cod_itens, $cod_usuario);
  }

  Desconectar($sock);

  $objResponse->addScript("Recarregar();");

  return $objResponse->getXML();
}


/*********************************************************************************************
   RecuperarItensDinamic - Exclui os itens, dinamicamente
   Entrada: $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_grupo_portfolio - codigo do grupo (NULL ou "" se portfolio individual)
            $cod_itens - array com o codigo dos itens, ou apenas o código de 1 item
   Saida: XML da função Ajax
*********************************************************************************************/
function RecuperarItensDinamic($tabela, $cod_curso, $cod_usuario, $cod_grupo_portfolio, $cod_itens){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  if(is_array($cod_itens)){
    foreach($cod_itens as $cod => $linha){
      RecuperarItem($sock, $tabela, $linha, $cod_usuario,$cod_grupo_portfolio);
    }
  }else if($cod_itens!=""){
    RecuperarItem($sock, $tabela, $cod_itens, $cod_usuario,$cod_grupo_portfolio);
  }


  Desconectar($sock);

  $objResponse->addScript("Recarregar();");

  return $objResponse->getXML();
}
?>
