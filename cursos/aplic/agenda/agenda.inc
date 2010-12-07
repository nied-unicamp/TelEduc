<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/agenda/agenda.inc

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
  ARQUIVO : cursos/aplic/agenda/agenda.inc
  ========================================================== */

/* *********************************************************************
   RetornaDiretorio - Retorna o Diret�rio da tabela de diretorios
   Entrada: $sock - BASE EXTERNA
            $item - nome do item a ser buscado
   Saida: string com o diret�rio
*/
function RetornaDiretorio($sock,$item)
{
  $query="select diretorio from Diretorio where item='".VerificaStringQuery($item)."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha[0]);
}

/* *********************************************************************
   RetornaAgendaAtiva - Retorna a agenda ativa
   Entrada: $sock - BASE Interna
   Saida: array ['cod_item'] - codigo do agenda
                ['titulo'] - Titulo da agenda
                ['texto'] - Texto
                ['situacao'] - situacao A,N,H
*/
function RetornaAgendaAtiva($sock)
{
  $query="select cod_item,titulo,texto,situacao from Agenda_itens where situacao='A'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha;
}

/* *********************************************************************
   RetornaAgenda - Retorna a agenda dada
   Entrada: $sock - BASE Interna
            $cod_item - codigo do item
   Saida: array ['cod_item'] - codigo do agenda
                ['cod_usuario'] - codigo do usuario
                ['titulo'] - Titulo da agenda
                ['texto'] - Texto
                ['situacao'] - situacao A,N,H
                ['status'] - status E,D,F,H,A,C
*/
function RetornaAgenda($sock,$cod_item)
{
  $query="select cod_item,cod_usuario,titulo,texto,situacao,status from Agenda_itens where cod_item=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha;
}


/* *********************************************************************
   RetornaItensListaAgendas - Retorna array com os itens do t�pico dado
   Entrada: $sock - BASE DO CURSO
   Saida: array com []['cod_item']
                    []['cod_usuario']
                    []['titulo']
                    []['texto']
                    []['situacao']
                    []['data']
                    []['data_ativo']
                    []['data_inativo']
                    []['status']
                    []['inicio_edicao']
*/
function RetornaItensListaAgendas ($sock)
{
  $consulta="select * from Agenda_itens where situacao='N' OR situacao='A' order by situacao,data";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   RetornaItensHistorico - Retorna array com os itens no historico
   Entrada: $sock - BASE DO CURSO
   Saida: array com []['cod_item']
                    []['cod_usuario']
                    []['titulo']
                    []['texto']
                    []['situacao']
                    []['data']
                    []['data_ativo']
                    []['data_inativo']
                    []['status']
                    []['inicio_edicao']
*/
function RetornaItensHistorico($sock)
{
  $consulta="select * from Agenda_itens where situacao='H' order by data desc";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   IniciaCriacao - Inicia a cria��o de um novo item 
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - usuario atual
            $cod_curso - codigo do curso
            $diretorio_temp - nome do diret�rio temporario, usado durante a edi��o
            $titulo - titulo do item a ser criado
   Saida: codigo do item criado
*/
function IniciaCriacao ($sock, $cod_usuario, $cod_curso, $diretorio_temp, $titulo, $texto)
{
  if(trim($diretorio_temp) == "" || $cod_curso == "")
    return -1; //tah certo isso?!


  $cod_item=RetornaProximoCodigo($sock, "Agenda_itens");
  
  $consulta="insert into Agenda_itens (cod_item, cod_usuario, titulo, texto, situacao, data, status, inicio_edicao) values (".VerificaNumeroQuery($cod_item).", ".VerificaNumeroQuery($cod_usuario).", '".VerificaStringQuery(htmlentities($titulo))."','".VerificaStringQuery(htmlentities($texto))."', 'N', ".time().", 'L', ".time().")";
  
  $res=Enviar($sock, $consulta);
  $consulta="insert into Agenda_itens_historicos values (".VerificaNumeroQuery($cod_item).", ".VerificaNumeroQuery($cod_usuario).", ".time().", 'C')";

  $res=Enviar($sock, $consulta);

  if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/"))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/");
  if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/agenda/"))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/agenda/");
  
  $dir=$diretorio_temp."/tmp/".$cod_curso."/agenda/".$cod_item."/";  
  CriaDiretorio($dir);

  
  return($cod_item);
}

/* *********************************************************************
   AtualizaDataEdicao - Atualiza a data de edi��o do item
   Entrada: $sock - BASE DO CURSO
            $cod_item - item 
   Saida: nenhuma
*/
function AtualizaDataEdicao ($sock, $cod_item)
{
  $consulta="update Agenda_itens set inicio_edicao=".time()." where cod_item=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);
}

/* *********************************************************************
   RetornaArquivosAgendaTemp - Retorna lista de arquivos do Material no diret�rio tempor�rio
   Entrada: $cod_curso - codigo do curso
            $diretorio_temp - diret�rio temporario de arquivos
            $cod_item - c�digo do item
   Saida: Array multidimensional com:
          $lista[<num>]['Caminho'] - caminho completo.
          $lista[<num>]['Diretorio'] - Diretorio do arquivo
          $lista[<num>]['Arquivo'] - Nome do arquivo
          $lista[<num>]['Status'] - Condi��o especial (true ou false);
          $lista[<num>]['Tamanho'] - tamanho do arquivo
          $lista[<num>]['Data'] - data da �ltima modifica��o
*/
function RetornaArquivosAgendaTemp ($cod_curso, $diretorio_temp, $cod_item)
{
  if(trim($diretorio_temp) == "" || $cod_curso == "" || $cod_item == "")
	return NULL; //tah certo isso?!

  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/agenda/".$cod_item."/";
  return (RetornaArrayDiretorio($dirtemp));
}

/* *********************************************************************
   RetornaUltimaPosicaoHistorico - Retorna a ultima ocorr�ncia do historico de um item dado
   Entrada: $sock - BASE DO CURSO
            $cod_item - item
   Saida: array ['cod_item']
                ['cod_usuario']
                ['data']
                ['acao']
*/
function RetornaUltimaPosicaoHistorico ($sock, $cod_item)
{
  $consulta="select * from Agenda_itens_historicos where cod_item=".VerificaNumeroQuery($cod_item)." order by data desc limit 1";
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}

/* *********************************************************************
   AtualizaAgenda - Atualiza o T�tulo, o Texto e os arquivos
   Entrada: $sock - BASE DO CURSO
            $cod_curso - c�digo do curso
            $cod_item - c�digo do item
            $titulo - t�tulo do material
            $texto - texto a ser colocado
            $situacao - tipo de compartilhamento
            $diretorio_arquivos - diret�rio dos arquivos do TelEduc
            $diretorio_temp - diret�rio dos arquivos do TelEduc
            $cod_usuario - codigo do usuario
   Saida: false - se houve algum erro.
*/
function AtualizaAgenda($sock,$cod_curso,$cod_item,$titulo,$texto,$situacao,$diretorio_arquivos,$diretorio_temp,$cod_usuario)
{
  /* Copiar arquivos, quando necess�rio... */
  if(trim($diretorio_arquivos) == "" || trim($diretorio_temp) == "" || $cod_curso == "" || $cod_item == "" )
    return false;

  $dir=$diretorio_arquivos."/".$cod_curso."/agenda/";
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/agenda/".$cod_item."/";
  $ret=SobrepoeDiretorio($dirtemp,$dir);

  /* Checa se existem arquivos no diret�rio */

  $array_temp=RetornaArrayDiretorio($dir."/".$cod_item);

  if ($ret && count($array_temp)==0)
  {

    if(!RemoveDiretorio($dir."/".$cod_item)) /*se nao conesguir aborta a funcao e retorna false*/
	return false;
  }

  $consulta="update Agenda_itens set titulo='".VerificaStringQuery($titulo)."', texto='".VerificaStringQuery($texto)."', status='L', situacao='".VerificaStringQuery($situacao)."' where cod_item=".VerificaNumeroQuery($cod_item);
  $res = Enviar($sock,$consulta);

  if(!$res)
    return false;
    
  $consulta="insert into Agenda_itens_historicos values (".VerificaNumeroQuery($cod_item).", ".VerificaNumeroQuery($cod_usuario).", ".time().", 'F')";
  $res=Enviar($sock, $consulta);
  return $res;
}

/* *********************************************************************
   CancelaEdicao - Cancela a edicao de um item (termina os diret�rios tb).
   Entrada: $sock - BASE DO CURSO
            $cod_item - codigo do item
            $cod_usuario - usuario cancelando a edicao
            $cod_curso - codigo do curso
            $diretorio_arquivos - diretorio dos arquivos definitivos
            $diretorio_temp - diretorio temporario da edicao
   Saida: true se a edi��o foi cancelada corretamente
*/
function CancelaEdicao ($sock, $cod_item, $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp)
{
  if(trim($diretorio_temp) == "" || $cod_item == "" || $cod_curso == "")
    return false;

  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/agenda/".$cod_item."/";
  if (file_exists($dirtemp))
  {
    if (!RemoveDiretorio($dirtemp))
      return false;
  }
  $linha=RetornaDadosDoItem($sock, $cod_item);
  if ($linha['status']=="C")
  {

    $consulta="delete from Agenda_itens_historicos where cod_item=".VerificaNumeroQuery($cod_item);
    $res=Enviar($sock, $consulta);

    $consulta="delete from Agenda_itens where cod_item=".VerificaNumeroQuery($cod_item);
    $res=Enviar($sock, $consulta);

    return $res;
  }
  if ($linha['status']=="E")
  {
    $consulta="insert into Agenda_itens_historicos values (".VerificaNumeroQuery($cod_item).", ".VerificaNumeroQuery($cod_usuario).", ".time().", 'D')";
    $res=Enviar($sock, $consulta);

    $consulta="update Agenda_itens set status='L' where cod_item=".VerificaNumeroQuery($cod_item);
    $res=Enviar($sock, $consulta);

    return $res;
  }
}

/* *********************************************************************
   RetornaDadosDoItem - Retorna um array com os dados do item
   Entrada: $sock - BASE DO CURSO
            $cod_topico - t�pico
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
*/
function RetornaDadosDoItem ($sock, $cod_item)
{
  $consulta="select * from Agenda_itens where cod_item=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}

/* *********************************************************************
   MudaStatusEdicao - Muda o Estado do item para Em Edi��o
   Entrada: $sock - BASE DO CURSO
            $cod_item - item 
            $cod_usuario - usuario realizando a edicao
   Saida: nenhuma
*/
function MudaStatusEdicao ($sock, $cod_item, $cod_usuario)
{
  $consulta="update Agenda_itens set status='E', cod_usuario=".VerificaNumeroQuery($cod_usuario).", inicio_edicao=".time()." where cod_item=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock,$consulta);
  $consulta="insert into Agenda_itens_historicos values (".VerificaNumeroQuery($cod_item).", ".VerificaNumeroQuery($cod_usuario).", ".time().", 'E')";
  $res=Enviar($sock,$consulta);
}

/* *********************************************************************
   CopiaArquivoParaTemporario - Copia os arquivos para temporario
   Entrada: $sock - BASE DO CURSO
            $cod_curso - c�digo do curso
            $diretorio_arquivos - diret�rio dos arquivos do TelEduc
            $diretorio_temp - diret�rio dos arquivos do TelEduc
            $cod_item - c�digo do item
   Saida: false - se houve algum erro.
*/
function CopiaArquivoParaTemporario($sock,$cod_curso,$diretorio_arquivos,$diretorio_temp,$cod_item)
{
  if(trim($diretorio_arquivos) == "" || trim($diretorio_temp) == "" || $cod_curso == "" || $cod_item == "")
    return false;

  $dir=$diretorio_arquivos."/".$cod_curso."/agenda/".$cod_item."/";
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/agenda/".$cod_item."/";
  $dirtemp2=$diretorio_temp."/tmp/".$cod_curso."/agenda/";

  if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/"))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/");
  if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/agenda/"))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/agenda/");

  if (!file_exists($dir))
  {
    if (!CriaDiretorio($dir))
      return false;
  }

  if (file_exists($dirtemp))
  {
    RemoveDiretorio($dirtemp);
  }
  if (!CopiaDiretorio($dir,$dirtemp2))
  {
    return false;
  }
  return true;  
}

/* *********************************************************************
   ApagarItem - Apaga o item selecionado (envia para a lixeira)
   Entrada: $sock - BASE DO CURSO
            $cod_item - codigo do item
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario que est� removendo, para o hist�rico
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
   Saida: nenhuma
*/
function ApagarItem ($sock,$cod_item,$cod_curso,$cod_usuario,$diretorio_arquivos,$diretorio_temp)
{
  /*Exclui registro da agenda do banco de dados*/
  $linha=RetornaDadosDoItem($sock, $cod_item);
  $consulta="delete from Agenda_itens where cod_item=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);
  $consulta="delete from Agenda_itens_historicos where cod_item=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);

  /*Exclui diretório temporário e diretório de arquivos criados,caso existam*/
  if(file_exists($diretorio_temp."/tmp/".$cod_curso."/agenda/".$cod_item))
  	RemoveDiretorio($diretorio_temp."/tmp/".$cod_curso."/agenda/".$cod_item);
  if(file_exists($diretorio_arquivos."/".$cod_curso."/agenda/".$cod_item))
  	RemoveDiretorio($diretorio_arquivos."/".$cod_curso."/agenda/".$cod_item);	
}

/* *********************************************************************
   AtivarAgenda - Ativa a agenda, jogando a agenda anterior no historico
   Entrada: $sock - BASE DO CURSO
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario que est� removendo, para o hist�rico
   Saida: nenhuma
*/
function AtivarAgenda($sock,$cod_item,$cod_usuario)
{
  $linha=RetornaAgendaAtiva($sock,$cod_item);
  if (isset($linha['cod_item']) && $linha['cod_item']!="")
  {
    $consulta="update Agenda_itens set situacao='H' where cod_item=".VerificaNumeroQuery($linha['cod_item']);
    $res=Enviar($sock, $consulta);
    $consulta="insert into Agenda_itens_historicos values (".VerificaNumeroQuery($linha['cod_item']).",".VerificaNumeroQuery($cod_usuario).",".time().",'H')";
    $res=Enviar($sock, $consulta);    
  }
  $consulta="update Agenda_itens set situacao='A' where cod_item=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);
  $consulta="insert into Agenda_itens_historicos values (".VerificaNumeroQuery($cod_item).",".VerificaNumeroQuery($cod_usuario).",".time().",'A')";
  $res=Enviar($sock, $consulta);

  AtualizaFerramentasNova($sock, 1, 'T');
}

/* *********************************************************************
   RetornaResHistoricoDoItem - Retorna todas as ocorr�ncias do historico de um item dado
   Entrada: $sock - BASE DO CURSO
            $cod_item - item
   Saida: array []['cod_item']
                []['cod_usuario']
                []['data']
                []['acao']
*/
function RetornaResHistoricoDoItem ($sock, $cod_item)
{
  $consulta="select * from Agenda_itens_historicos where cod_item=".VerificaNumeroQuery($cod_item)." order by data desc,acao desc";
  $res=Enviar($sock, $consulta);
  return($res);
}

/* *********************************************************************
   EncerrarExibicaoArquivo - Fecha o link simb�lico
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
   Saida: True se link removido corretamente
*/
function EncerrarExibicaoArquivo($sock,$cod_curso,$cod_usuario,$diretorio_arquivos)
{
  if(trim($diretorio_arquivos) == "" || $cod_curso == "" || $cod_usuario == "")
    return false;

  /* Cria link simb�lico (apaga antigo, se houver)*/
  if ($cod_usuario<0) 
    $cod_usuario=0;
  $dirlink="../../diretorio/agenda_".$cod_curso."_".$cod_usuario;
  return (RemoveArquivo($dirlink));
}

/*verificar para q serve cada item desses e arrumar os comentarios.
/* *********************************************************************
   RetornaAgenda - Retorna a agenda dada
   Entrada: $sock - BASE Interna
   Saida: array ['cod_item'] - codigo do agenda
   		['cod_usuario'] - codigo do usuario
                ['titulo'] - Titulo da agenda
                ['texto'] - Texto
                ['situacao'] - situacao A,N,H
		['data']
		['data_ativo']
		['data_inativo']
		['status']
		['inicio_edicao']
*/

function RetornaAgendaCurso($sock)
{
  $query="select * from Agenda_itens";
  $res=Enviar($sock,$query);
  $linha=RetornaArrayLinhas($res);
  return $linha;
}

/* *********************************************************************
   ImportarAgenda - Retorna a agenda dada
   Entradas: $cod_usuario
   	     $cod_curso_destino
	     $cod_itens
	     $cod_curso_origem
	     $diretorio_origem
	     $diretorio_destino
	     $diretorio_temp
   Saida: booleano - true caso a importacao tenha sido um sucesso
   		     false caso tenha ocorrido algum erro
*/
function ImportarAgenda($cod_usuario, $cod_curso_destino, $cod_itens, $cod_curso_origem, $diretorio_origem, $diretorio_destino, $diretorio_temp)
{ 
  if(trim($diretorio_origem) == "" || trim($diretorio_destino) == "" || trim($diretorio_temp) == "" ||
      $cod_curso_origem == "" || $cod_itens == "" || $cod_curso_destino == "" )
    return false;

  foreach($cod_itens as $cod => $cod_item)
  {
    $ret = true;
    /*pega item a ser importado!*/
    $sock = Conectar($cod_curso_origem);
    $query = "select * from Agenda_itens where cod_item =".VerificaNumeroQuery($cod_item);
    $id = Enviar($sock, $query);
    $item_import = RetornaLinha($id);
    Desconectar($sock);
  
    /*insere item na base de dados do curso destino*/
    $sock = Conectar($cod_curso_destino);
    /*cria um item vazio na agenda do curso destino!*/
    $cod_item_dest = IniciaCriacao($sock, $cod_usuario, $cod_curso_destino, $diretorio_temp, $item_import['titulo'], $item_import['texto']);
    MudarDB($sock,$cod_curso_origem,$opt);
    
    /*Copia arquivo do diretorio de origem para o temp do curso destino!*/
    /* Checa se existem arquivos no diret�rio */
    /*� necessario futuramente fazer uma verificao melhor dos erros, com a utilizacao de codigos de erro. usando 
      o modelo atual � impossivel determinar qual foi o erro no t�rmino da funcao*/
    $array_dest=RetornaArrayDiretorio($diretorio_origem."/".$cod_curso_origem."/agenda/".$cod_item);
    if ($ret && count($array_dest)!=0)
    {
      $ret = CriaDiretorio($diretorio_destino."/".$cod_curso_destino."/agenda/".$cod_item_dest."/");
      if($ret)
      	$ret = CopiaArquivosDiretorio($diretorio_origem."/".$cod_curso_origem."/agenda/".$cod_item."/", $diretorio_destino."/".$cod_curso_destino."/agenda/".$cod_item_dest."/");
    }
 /*   if($ret)
    {	
	MudarDB($sock,$cod_curso_destino);
	/*faz a copia(Importacao) de fato!*/
/*        $ret = AtualizaAgenda($sock, $cod_curso_destino, $cod_item_dest, $item_import['titulo'], $item_import['texto'],
                              'N', $diretorio_destino, $diretorio_temp, $cod_usuario);

    }
    else
      return $ret;*/
    Desconectar($sock);
  }
  return $ret;
}

/* *********************************************************************
   RetornaArquivosAgendaVer - Retorna lista de arquivos da Agenda
   Entrada: $dirlink - nome do diret�rio da ferramenta
            $cod_curso - c�digo do item
   Saida: Array multidimensional com:
          $lista[<num>]['Caminho'] - caminho completo.
          $lista[<num>]['Diretorio'] - Diretorio do arquivo
          $lista[<num>]['Arquivo'] - Nome do arquivo
          $lista[<num>]['Status'] - Condi��o especial (true ou false);
          $lista[<num>]['Tamanho'] - tamanho do arquivo
          $lista[<num>]['Data'] - data da �ltima modifica��o
*/
function RetornaArquivosAgendaVer ($cod_curso, $dirlink)
{
  return (RetornaArrayDiretorio($dirlink));
}

/* ***********************************************************************
   CriaLinkVisualizar - Cria link simb�lico para dinamica e retorna o link
   Entrada: $sock - BASE DO CURSO
            $dirname - nome do diretorio da ferramenta
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_item - c�digo do item
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
   Saida: caminho relativo
*/
function CriaLinkVisualizar($sock,$dirname,$cod_curso,$cod_usuario,$cod_item,$diretorio_arquivos,$diretorio_temp)
{
  if(trim($diretorio_arquivos) == "" || trim($diretorio_temp) == "" || trim($dirname) == "" ||
     $cod_curso == "" || $cod_item =="")
    return false;

  /* Busca Arquivo a ser mostrado */
  $dir=$diretorio_arquivos."/".$cod_curso."/".$dirname."/".$cod_item."/";
  
  // Cria link simb�lico (apaga antigo, se houver)
  if ($cod_usuario < 0)
    $cod_usuario = 0;
  $dirlink = $diretorio_temp."/".$dirname."_".$cod_curso."_".$cod_usuario;
  $dirlinkpath="../../diretorio/".$dirname."_".$cod_curso."_".$cod_usuario;

  if (ExisteArquivo($dirlink))
  {
    RemoveArquivo($dirlink);
  }
  CriaLinkSimbolico($dir,$dirlink);

  $retorno['diretorio']=$dirlink."/";
  $retorno['link']=$dirlinkpath."/";

  return ($retorno);
}

/* *********************************************************************
   RetornaArquivosVisiveis - Retorna array com os arquivos visiveis ao usuario
   Entrada: $lista_arq - **** Perguntar ao Davi a logica ******
   Saida: array com lista de arquivos
*/
function RetornaArquivosVisiveis($lista_arq)
{

  $cont=0;
  $pilha_index=-1;
  $pilha = "";
  unset($pilha);
  if (count($lista_arq)>0)
  {
    foreach($lista_arq as $cod => $linha)
    {
      if ($linha['Diretorio']!="")
      {
        if ($linha['Arquivo']=="")
        {
          if ($pilha_index>=0)
          {

            $dir_array_p=explode("/", $pilha[$pilha_index]['Diretorio']);
            $nivel_p=count($dir_array_p)-1;
            $dir_name_p=$dir_array_p[$nivel_p];
            unset($dir_array_p[$nivel_p]);
            $dir_path_p=implode("/", $dir_array_p);

            $dir_array=explode("/", $linha['Diretorio']);
            $nivel=count($dir_array)-1;
            $dir_name=$dir_array[$nivel];
            unset($dir_array[$nivel]);
            $dir_path=implode("/", $dir_array);

            while ($nivel_p>=$nivel)
            {
              unset($pilha[$pilha_index]);
              $pilha_index--;
              $nivel_p--;
            }
            $pilha_index++;
            $pilha[$pilha_index]=$linha;
            $pilha[$pilha_index]['Posicao']=$cod;

          }
          else
          {
            $pilha_index++;
            $pilha[$pilha_index]=$linha;
            $pilha[$pilha_index]['Posicao']=$cod;
          }
        }
        else
        {
          if ($linha['Status'] && $pilha_index>=0)
          {

            foreach ($pilha as $cod_pilha => $linha_pilha)
            {
              $lista_arq[$linha_pilha['Posicao']]['Status']=true;
            }

          }
        }
      }
    }
  }
  return($lista_arq);
}

/* *********************************************************************
   EditarTitulo - Edita o título do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $novo_nome - novo título do item
            $cod_usuario - código do usuário que está utilizando
   Saida: XML da função Ajax
*/
function EditarTitulo ($cod_curso, $cod_item, $novo_nome, $cod_usuario, $texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $sock=Conectar($cod_curso);
  $consulta="update Agenda_itens set titulo='".VerificaStringQuery(htmlentities($novo_nome))."' where cod_item=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  // Imprime no div valores do formul?io
  $objResponse->addAssign("tr_".$cod_item, "className", "novoitem");
  $objResponse->addAssign("tit_".$cod_item, "innerHTML", htmlentities($novo_nome));
  $objResponse->addEvent("renomear_".$cod_item, "onclick", "AlteraTitulo('".$cod_item."');");

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   VerificaTexto -Verifica se o texto é composto apenas por caracteres "sem valor" (" ","<br>" ...).
   Entrada: $texto - texto a ser verificado

   Saida: -  O próprio texto ($texto) caso o mesmo contenha caracteres "com valor";
          -  "" caso contrário
*/            
function VerificaTexto($texto){
  
  $text1 = Enter2BR($texto);

  $text2 = explode("<br />",$text1);
  foreach($text2 as $string){
    $string1 = explode("&nbsp;",$string);
    foreach($string1 as $string2){
      $string3 = explode("<br>",$string2);	
      foreach($string3 as $pal){	
        if((trim($pal)!="")&&(trim($pal)!="<P>")&&(trim($pal)!="</P>"))
        {
          return $texto;
        }
      }
    }
  }

  return "";
}		

/* *********************************************************************
   VerificaAnexo - verifica se nome do anexo aprenseta acento ou chars ruins.
   Entrada: $string - com o nome do anexo a ser verificado
   Saida: 1 - nome do anexo est� ok!
   		  0 - nome do anexo apresenta acento ou chars ruins.
*/
function VerificaAnexo($str_anexo)
{
	$string = utf8_decode($str_anexo);
	// Palavras acentuadas
	$bad_chars = "����������";
	$bad_chars.= "����";
	$bad_chars.= "����������";
	$bad_chars.= "����������";
	$bad_chars.= "����������";

	// Caracteres estranhos para nome de um arquivo anexo
	$bad_chars.= "�`!@#$%*()+={}[]\\\"';:?/,<>";
	$str_res = strpbrk($string, $bad_chars);

	// Se apresentar algum char de $bad_chars, str_res != ""
	if (($str_res == "") || ($str_res == false))
		return 1;
	else
		return 0;
}
/* *********************************************************************
   EditarTexto - Edita o texto do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $novo_nome - novo texto do item
            $cod_usuario - código do usuário que está utilizando
   Saida: XML da função Ajax
*/
function EditarTexto ($cod_curso, $cod_item, $novo_nome, $cod_usuario, $texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $novo_nome=ConverteAspas2BarraAspas($novo_nome);
  $sock=Conectar($cod_curso);

  $consulta="update Agenda_itens set texto='".VerificaStringQuery(trim(VerificaTexto($novo_nome)))."' where cod_item=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  // Imprime no div valores do formulário
  $objResponse->addAssign("tr_".$cod_item, "className", "novoitem");
  $objResponse->addAssign("text_".$cod_item, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas(VerificaTexto($novo_nome))), true));


  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   AcabaEdicaoDinamic - Marca no banco de dados o fim da edição do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $cod_usuario - código do usuário que está utilizando
            $acao - 0 se for cancelar a edição,
                    1 se for finalizar a edição.

   Saida: XML da função Ajax
*/
function AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, $acao){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime

  $data = time();

  $consulta="update Agenda_itens set status='L', data=".VerificaNumeroQuery($data)." where cod_item=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);

  if($acao){
    $consulta="insert into Agenda_itens_historicos values ('".VerificaStringQuery($cod_item)."', '".VerificaStringQuery($cod_usuario)."', '".VerificaStringQuery($data)."', 'F')";
  }else{
    $consulta="insert into Agenda_itens_historicos values ('".VerificaNumeroQuery($cod_item)."', '".VerificaStringQuery($cod_usuario)."', '".VerificaStringQuery($data)."', 'D')";  
  }

  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  return $objResponse->getXML();
}

/* *********************************************************************
   AcabaEdicao - Marca no banco de dados o fim da edição do item dado
   Entrada: $sock - conexão com o BD
            $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $cod_usuario - código do usuário que está utilizando
            $acao - 0 se for cancelar a edição,
                    1 se for finalizar a edição.

   Saida: sem saída
*/
function AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, $acao){

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time()+1;

  $consulta="update Agenda_itens set status='L', data=".VerificaNumeroQuery($data)." where cod_item=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);

  if ($acao){
    $consulta="insert into Agenda_itens_historicos values (".VerificaNumeroQuery($cod_item).", ".VerificaNumeroQuery($cod_usuario).", ".VerificaNumeroQuery($data).", 'F')";
  }else{
    $consulta="insert into Agenda_itens_historicos values (".VerificaNumeroQuery($cod_item).", ".VerificaNumeroQuery($cod_usuario).", ".VerificaNumeroQuery($data).", 'D')";    
  }
  $res=Enviar($sock, $consulta);

}

/* *********************************************************************
   AbreEdicao - Marca no banco de dados o início da edição do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $cod_usuario - código do usuário que está utilizando  
            $origem - página de onde originou-se a chamada à essa função

   Saida: XML da função Ajax
*/
function AbreEdicao($cod_curso, $cod_item, $cod_usuario,$origem){

  $objResponse = new xajaxResponse('ISO-8859-1');

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime

  $data = time();

  $sock=Conectar($cod_curso);
  //Retorna os dados da agenda - Array: cod_item,cod_usuario,  titulo, texto, situacao,
  //data, data_ativo, data_inativo, status, inicio_edicao.
  $linha=RetornaAgenda($sock, $cod_item);
  //Retorna a ultima ocorrencia do historico de um item dado - cod_item, cod_usuario, data, acao	
  $linha_historico=RetornaUltimaPosicaoHistorico($sock, $cod_item);

  if (($linha['status']=="E")&&($cod_usuario !=$linha_historico['cod_usuario'])){
    $objResponse->addScript("window.open('em_edicao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');");

    $objResponse->addScript("document.location='".$origem.".php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."cod_ferramenta=1'");
  }else {
    if($linha['status']=="E"){		
      $consulta="insert into Agenda_itens_historicos values (".VerificaNumeroQuery($cod_item).", ".VerificaNumeroQuery($cod_usuario).", ".time().", 'D')";
      $res=Enviar($sock,$consulta);
    }	
    $consulta="update Agenda_itens set status='E', cod_usuario=".VerificaNumeroQuery($cod_usuario).", inicio_edicao=".time()." where cod_item=".VerificaNumeroQuery($cod_item);
    $res=Enviar($sock,$consulta);
    $consulta="insert into Agenda_itens_historicos values (".VerificaNumeroQuery($cod_item).", ".VerificaNumeroQuery($cod_usuario).", ".time().", 'E')";
    $res=Enviar($sock,$consulta);
  }

  Desconectar($sock);

  //Para evitar erros no histórico
  sleep(1);

  return $objResponse->getXML();
}


/* *********************************************************************
   RetornaFraseDinamic - Retorna lista de frases para uma variavel JavaScript, dinamicamente
   Entrada: $variavel - nome da variavel que vai receber as frases

   Saida: XML da função Ajax
*/
function RetornaFraseDinamic($variavel){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,1);

  Desconectar($sock);

  $retorno="{\n";

  foreach ($lista_frases as $cod => $linha){
    if ($cod>1) $retorno.=", ";
    $retorno.="\"msg".$cod."\":\"".$linha."\" ";
  }
  
  $retorno.="\n}";

  $objResponse->addScript($variavel." = ".$retorno.";");

  return $objResponse->getXML();
}

/* *********************************************************************
   RetornaFraseDinamic - Retorna lista de frases 'geral' para uma variavel JavaScript, dinamicamente
   Entrada: $variavel - nome da variavel que vai receber as frases

   Saida: XML da função Ajax
*/
function RetornaFraseGeralDinamic($variavel){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $retorno="{\n";

  foreach ($lista_frases as $cod => $linha){
    if ($cod>1) $retorno.=", ";
    $retorno.="\"msg_ger".$cod."\":\"".$linha."\" ";
  }
  
  $retorno.="\n}";

  $objResponse->addScript($variavel." = ".$retorno.";");


  return $objResponse->getXML();
}

/* *********************************************************************
   ExcluirArquivo - Exclui o arquivo, dinamicamente
   Entrada: $numero - numero do arquivo, no item
            $arq - endereco do arquivo no servidor
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario
            $origem - variável que preserva o valor da origem  

   Saida: XML da função Ajax
*/
function ExcluirArquivo($numero, $arq, $cod_curso, $cod_item, $cod_usuario, $origem){

  AbreEdicao($cod_curso, $cod_item, $cod_usuario, $origem);
  
  $objResponse = new xajaxResponse('ISO-8859-1');

  $arq=htmlspecialchars_decode($arq);

  RemoveDiretorio($arq);

  $objResponse->addRemove('arq_'.$numero);

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  return $objResponse->getXML();
}

/* *********************************************************************
   SelecionarEntradaDinamic - Oculta os arquivos passados num array, dinamicamente
   Entrada: $nomes_arquivos - array de nomes dos arquivos
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario
            $origem - variável que preserva o valor da origem 

   Saida: XML da função Ajax
*/
function SelecionarEntradaDinamic($nomes_arquivos, $cod_curso, $cod_item, $cod_usuario, $origem){
  
  AbreEdicao($cod_curso, $cod_item, $cod_usuario,$origem);

  $objResponse = new xajaxResponse('ISO-8859-1');

  foreach($nomes_arquivos as $cod => $linha)
  {
    $nome_arquivo = implode("/", explode("//", $linha[0])); 
    if($linha[1] == 1)
      AlteraStatusArquivo($nome_arquivo,true);
    else
      AlteraStatusArquivo($nome_arquivo,false);
  }

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->addScript('window.location = "ver_linha.php?cod_curso='.$cod_curso.'&cod_usuario='.$cod_usuario.'&cod_ferramenta=1&cod_item='.$cod_item.'&cod_usuario='.$cod_usuario.'&origem='.$origem.'&acao=selecionar_entrada&atualizacao=true"');

  return $objResponse->getXML();
}


/* *********************************************************************
   RetirarEntradaDinamic - Desoculta os arquivos passados num array, dinamicamente
   Entrada: $nomes_arquivos - array de nomes dos arquivos
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario
            $origem - variável que preserva o valor da origem 

   Saida: XML da função Ajax
*/
function RetirarEntradaDinamic($nome_arquivo, $cod_curso, $cod_item, $cod_usuario, $origem){
  
  AbreEdicao($cod_curso, $cod_item, $cod_usuario,$origem);
  
  $objResponse = new xajaxResponse('ISO-8859-1');

  AlteraStatusArquivo($nome_arquivo,false);

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

   $objResponse->addScript('window.location = "ver_linha.php?&cod_curso='.$cod_curso.'&cod_usuario='.$cod_usuario.'&cod_ferramenta=1&cod_item='.$cod_item.'&cod_usuario='.$cod_usuario.'&origem='.$origem.'&acao=retirar_entrada&atualizacao=true"');

  return $objResponse->getXML();
}

?>
