<?php
/*
// <!-
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/portfolio.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist?cia
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

    Nied - Ncleo de Inform?ica Aplicada ?Educa?o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit?ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/portfolio/portfolio.inc
  ========================================================== */

/*
indice
RetornaDiretorio
UltimaPosicaoLivre
OrganizaPosicaoTopico
ApagarItem
ExcluirItem
ApagarTopico
RaizUsuario
RecuperarItem
AtualizaCompartilhamentoPastas
EPai
MoverItem
MoverTopico
PertenceAoGrupo
RetornaListaDeTopicos
RetornaListaDeTopicosRecursao
RenomearTopico
MudarCompartilhamento
RetornaItensDoTopico
RetornaNumComentariosItem
RetornaTopicosDoTopico
NomeGrupo
StatusGrupo
RetornaTopicosBase
RetornaNumItensPortfolio
RetornaNumItensPortfolio
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
EncerraExibicaoArquivos
RetornaArquivosVisiveis
RetornaMaiorData
VerificaPortfolioGrupos
MontaMenu
RetornaListaIntegrantes
RetornaItensDaLixeira
InsereComentario
RetornaComentariosDoItem
RetornaComentario
ApagaComentario
RetornaNumComentariosTopico
RetornaMaiorDataItemComentario
RetornaNumItensTopicoRec
RetornaStatusPortfolio
ExecutaAcao
*/


/******************************************************************************************
   CriaZipDinamic - Cria a estrutura de pastas para os downloads multiplos
      				e for�a o download
      				Fun��o para ser usada via ajax
   Entrada: $cod_topico_raiz - c�digo do t�pico para download
            $path - diretorio atual no "temp"
            $cod_curso - codigo do curso
            $cod_ferramenta - codigo da ferramenta
            $diretorio_arquivos - repositorio do proprio teleduc
            $tabela - nome da tabela da ferramenta
            $bibliotecas - path das bibliotecas
            $nome_ferramenta - nome da ferramenta
            $diretorio_temp - diret�rio registrado na base de dados
   Saida: nenhuma
*******************************************************************************************/
function CriaZipDinamic($cod_topico_raiz,$path,$cod_curso,$cod_ferramenta,$diretorio_arquivos,$tabela,$bibliotecas,$nome_ferramenta,$diretorio_temp){
  	// FIXME
	// inicializa��o do ajax
	$objResponse = new xajaxResponse();
	$sock = Conectar($cod_curso);
	// cria a hierarquia de pastas
	CriaArvorePastasTopico($sock, $cod_topico_raiz, $path, $cod_curso, $cod_ferramenta,$diretorio_arquivos);
  	// adequa o nome do t�pico
	
	(RetornaNomeTopico($sock,$tabela,$cod_topico_raiz)=="Raiz") ? ($nome_topico=$nome_ferramenta) : ($nome_topico=RetornaNomeTopico($sock,$tabela,$cod_topico_raiz));
    
	if ($nome_topico[0] == '#' && $nome_topico[1] == 'U'){
    	$cod_usuario = RetornaCodUsuarioTopico($sock, $tabela, $cod_topico_raiz);
    	$sock = MudarDB($sock);
    	$nome_topico = RetornaUsuario($sock,$cod_usuario);
    	$sock = MudarDB($sock,$cod_curso);
    }
    
	$path=$path.'/'.$nome_topico;
	
  	// limpa a hierarquia de pastas
  	RemovePastasVazias($path);
  	
  	$dir_tmp_ferramenta=$path."/../";
  	
  	$working_dir=getcwd();
  	// verifica se foi criada a hierarquia de pastas, caso contr�rio � porque n�o tem anexos a serem baixados
  	if (file_exists($path)){  		
  		chdir($path.'/../');
  		// gera o zip a partir do diretorio $nome_topico
  		$directoryToZip=$nome_topico;
  		// o zip gerado � colocado no cwd
  		$outputDir="";
  		// acrescenta a extens�o do arquivo
  		$zipName=$nome_topico.".zip";
  		chdir($working_dir);
  		include_once($bibliotecas."CreateZipFile.inc.php");
  		chdir($dir_tmp_ferramenta);
  		// m�gica para gerar o zip
  		$createZipFile=new CreateZipFile;
  		$createZipFile->zipDirectory($directoryToZip,$outputDir);
  		$fd=fopen($zipName, "wb");
  		$out=fwrite($fd,$createZipFile->getZippedfile());
  		$FilePath=getcwd();
  		fclose($fd);
  		// apaga a hierarquia de pastas
  		ApagaArvorePastas($path);
  		$FilePath.="/".$zipName;
  		chdir($working_dir);
  		$DownloadPath=$diretorio_temp;
  		$DownloadPath.="/".$zipName;
  		copy($FilePath,$DownloadPath);
  		// faz a m�gica do download
  		$objResponse->call("redirecionaDownloadAnexos",$DownloadPath);
  	}
  	else{
  		// se n�o tem anexos emite um alerta, mais percept�vel que um feedback
  		$objResponse->alert("Nao existem anexos a serem baixados!\n");
  	}
  	Desconectar($sock);
  	return $objResponse;
  
}



/******************************************************************************************
   CriaArvorePastasTopico - Cria a estrutura de pastas para os downloads multiplos
      						a partir de um t�pico
   Entrada: $sock - BASE EXTERNA
            $cod_topico - codigo do topico atual
            $path - diretorio atual no "temp"
            $cod_curso - codigo do curso
            $cod_ferramenta - codigo da ferramenta
            $diretorio_arquivos - repositorio do proprio teleduc
   Saida: nenhuma
*******************************************************************************************/
function CriaArvorePastasTopico($sock, $cod_topico, $path, $cod_curso, $cod_ferramenta,$diretorio_arquivos){
    // adequa as variaveis internas de acordo com a ferramenta
    switch ($cod_ferramenta) {
    case 3 :
        $tabela="Atividade";
        $dir="atividades";
        $nome_topico="Atividades";
        break;
    case 4 :
        $tabela="Apoio";
        $dir="apoio";
        $nome_topico="Material_de_apoio";
        break;
    case 5 :
        $tabela="Leitura";
        $dir="leituras";
        $nome_topico="Leituras";
        break;
    case 7 :
        $tabela="Obrigatoria";
        $dir="obrigatoria";
        $nome_topico="Parada_obrigatoria";
        break;
    case 15 :
        $tabela="Portfolio";
        $dir="portfolio/item";
        $nome_topico="Portfolio";
        break;
    }
    // se nao for o topico raiz, adequa $nome_topico ao nome do topico atual
    // modificar para buscar o nome de usuario quando o nome do topico for do tipo #Un
    if($cod_topico != 1) $nome_topico=RetornaNomeTopico($sock,$tabela,$cod_topico);
    if ($nome_topico[0] == '#' && $nome_topico[1] == 'U'){
        // TODO: quero trocar o #Un pelo nome do usuario, mas tah tenso
        // consigo pegar de boa o codigo do usuario, mas tah osso pegar o nome
        $cod_usuario = RetornaCodUsuarioTopico($sock, $tabela, $cod_topico);
        $sock = MudarDB($sock);
        $nome_topico = RetornaUsuario($sock,$cod_usuario);
        $sock = MudarDB($sock,$cod_curso);
    }
    //$sock = Conectar($cod_curso);
    $new_path=$path.'/'.$nome_topico;
	// cria a pasta do topico atual
    mkdir($new_path);
	// precisa usar a funcao RetornaItensDoTopico da mesma forma como visto em material.inc
    $itens=RetornaItensDoTopico2($sock,$tabela,$cod_topico);
    // ateh aqui tah aparentemente funfando
    
    // verifica se o topico possui itens
    if($itens){
        foreach ($itens as $item){
            // caso possua itens, cria uma pasta para cada item e copia os anexos do item
            $item_path=$new_path.'/'.$item["titulo"];
            mkdir($item_path);
            $copy_path=$diretorio_arquivos.'/'.$cod_curso.'/'.$dir.'/'.$item["cod_item"].'/';
            $item_arquivos=scandir($copy_path);
            foreach ($item_arquivos as $item_arquivo){            	
            	if($item_arquivo!="." && $item_arquivo!=".."){
            		// workaround para copiar os arquivos sem precisar das permiss�es adequadas
            		
            		try{
            			$source=$copy_path.$item_arquivo;
            			$dest=$item_path.'/'.$item_arquivo;
            			$data=file_get_contents($source);
            			$handle=fopen($dest,"w");
            			fwrite($handle,$data);
            			fclose($handle);
            		}catch(Exception $e){
            			die($e->errorMessage());
            		}          		
            	}
            }
        }    
    }   	
    $filhos=RetornaTopicosDoTopico2($sock,$tabela,$cod_topico);
    // verifica se o topico possui subtopicos
    if($filhos){
        foreach ($filhos as $filho){
        	// se tiver, tail call pra cada filho
            CriaArvorePastasTopico($sock,$filho["cod_topico"],$new_path,$cod_curso,$cod_ferramenta,$diretorio_arquivos);
        }    
    }
}




/******************************************************************************************
   RemovePastasVazias - Percorre uma hierarquia de diretorios removendo pastas vazias,
                        eh utilizado em conjunto com o CriaArvorePastas para implementacao
                        de downloads multiplos 
   Entrada: $path - diretorio a ser analisado            
   Saida: nenhuma
*******************************************************************************************/
function RemovePastasVazias($path){	
	if(TemSubPastas($path)){
		$subpastas=scandir($path);
		foreach ($subpastas as $subpasta){
			if($subpasta != "." && $subpasta != ".."){
				// chama recursivamente para cada subdiretorio do diretorio atual
				if(is_dir($path.'/'.$subpasta)) RemovePastasVazias($path.'/'.$subpasta);
			}
		}
	}
	// se o atual diretorio estiver vazio na volta da recursao, remove o mesmo
	if(!(TemSubPastas($path)||TemArquivos($path))){
		rmdir($path);
	}
}
/******************************************************************************************
   ApagaArvorePastas - Apaga toda uma hierarquia de diret�rios, pode ser perigosa se usada de modo indevido
   Entrada: $dir - diretorio a ser removido           
   Saida: nenhuma
*******************************************************************************************/
function ApagaArvorePastas($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") ApagaArvorePastas($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 } 


/******************************************************************************************
   TemSubPastas - Verifica se existem subdiretorios num determinado diretorio
   Entrada: $path - diretorio a ser analisado
   Saida: bool
*******************************************************************************************/
function TemSubPastas($path){
	$subpastas=scandir($path);
	// o tamanho do array vai sempre ser no minimo 2, ja que scandir sempre retorna o '.' e o '..'
	if(count($subpastas)>2){
	foreach ($subpastas as $subpasta){
			if($subpasta != "." && $subpasta != ".."){
				if(is_dir($path.'/'.$subpasta)) return true;
			}
		}
	}
	return false;
}
/******************************************************************************************
   TemArquivos - Verifica se existem arquivos num determinado diretorio
   Entrada: $path - diretorio a ser analisado
   Saida: bool
*******************************************************************************************/
function TemArquivos($path){
	$subpastas=scandir($path);
	if(count($subpastas)>2){
	foreach ($subpastas as $subpasta){
			if($subpasta != "." && $subpasta != ".."){
				if(is_file($path.'/'.$subpasta)) return true;
			}
		}
	}
	return false;
}

/******************************************************************************************
   RetornaNomeTopico - Retorna o Nome do Topico na tabela de topicos
   Entrada: $sock - BASE EXTERNA
            $tabela - tabela da ferramenta na qual se busca o topico
            $cod_topico - codigo do topico buscado
   Saida: string com o nome do topico
*******************************************************************************************/
function RetornaNomeTopico($sock,$tabela,$cod_topico)
{
  $consulta="select topico from ".$tabela."_topicos where cod_topico=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista[0][0]);
}

/******************************************************************************************
   RetornaCodUsuarioTopico - Retorna o Nome do Topico na tabela de topicos
   Entrada: $sock - BASE EXTERNA
            $tabela - tabela da ferramenta na qual se busca o topico
            $cod_topico - codigo do topico buscado
   Saida: string com o nome do topico
*******************************************************************************************/
function RetornaCodUsuarioTopico($sock,$tabela,$cod_topico)
{
  $consulta="select cod_usuario from ".$tabela."_topicos where cod_topico=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista[0][0]);
}

/******************************************************************************************
   RetornaUsuario - Retorna o Nome do Topico na tabela de topicos
   Entrada: $sock - BASE EXTERNA
            $tabela - tabela da ferramenta na qual se busca o topico
            $cod_topico - codigo do topico buscado
   Saida: string com o nome do topico
*******************************************************************************************/
function RetornaUsuario($sock,$cod_usuario)
{
  $consulta="select nome from Usuario where cod_usuario=".$cod_usuario;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista[0][0]);
}

/******************************************************************************************
   RetornaNomeItem - Retorna o Nome do Item na tabela de itens
   Entrada: $sock - BASE EXTERNA
            $tabela - tabela da ferramenta na qual se busca o item
            $cod_item - codigo do item buscado
   Saida: string com o nome do item
*******************************************************************************************/
function RetornaNomeItem($sock,$tabela,$cod_item)
{
  $consulta="select titulo from ".$tabela."_itens where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista[0][0]);
}


/*************************************************************************************************
   RetornaItensDoTopico2 - Retorna array com os itens do tï¿½ico dado
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
function RetornaItensDoTopico2 ($sock, $tabela, $cod_topico, $tipo_comp="")
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
   RetornaTopicosDoTopico2 - Retorna array com os topicos filhos do topico dado
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
function RetornaTopicosDoTopico2 ($sock, $tabela, $cod_topico)
{
  //não retorna a lixeira
  $consulta="select * from ".$tabela."_topicos where cod_topico_pai=".$cod_topico." and cod_topico<>'2' order by posicao_topico";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}



/* *********************************************************************
   RetornaDiretorio - Retorna o Diret?io da tabela de diretorios
   Entrada: $sock - BASE EXTERNA
            $item - nome do item a ser buscado
   Saida: string com o diret?io
*/
function RetornaDiretorio($sock,$item)
{
  $query="select diretorio from Diretorio where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha[0]);
}

/* *********************************************************************
   UltimaPosicaoLivre - Retorna a próxima posição livre no topico
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo do topico atual
   Saida: posicao
*/
function UltimaPosicaoLivre($sock, $cod_topico)
{
  $consulta="select max(posicao_item) from Portfolio_itens where cod_topico=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $max_posicao_item=RetornaLinha($res);

  if($max_posicao_item[0] == null) $max_posicao_item=0;

  $consulta="select max(posicao_topico) from Portfolio_topicos where cod_topico_pai=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $max_posicao_topico=RetornaLinha($res);
  if($max_posicao_topico[0] == null) $max_posicao_topico=0;
  

  return(max($max_posicao_topico[0], $max_posicao_item[0])+1);
}

/* *********************************************************************
   OrganizaPosicaoTopico - Organiza?o a numeracao da posicao dos itens
                           na p?ina, em ordem crescente
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo do topico atual
   Saida: nenhuma
*/
function OrganizaPosicaoTopico($sock, $cod_topico)
{
  $consulta="select cod_item, posicao_item from Portfolio_itens where cod_topico=".$cod_topico." and status<>'A' and status<>'X' and status<>'C' order by posicao_item";
  $res=Enviar($sock, $consulta);
  $lista_itens=RetornaArrayLinhas($res);
  if (count($lista_itens)>0)
  {
    $cont=1;
    foreach ($lista_itens as $cod => $linha)
    {
      if ($linha['posicao_item']!=$cont)
      {
        $consulta="update Portfolio_itens set posicao_item=".$cont." where cod_item=".$linha['cod_item'];
        $res=Enviar($sock, $consulta);
      }
      $cont++;
    }
  }
  $consulta="select cod_topico, posicao_topico from Portfolio_topicos where cod_topico_pai=".$cod_topico." order by posicao_topico";
  $res=Enviar($sock, $consulta);
  $lista_topicos=RetornaArrayLinhas($res);
  if (count($lista_topicos)>0)
  {
    $cont=1;
    foreach ($lista_topicos as $cod => $linha)
    {
      if ($linha['posicao_topico']!=$cont)
      {
        $consulta="update Portfolio_topicos set posicao_topico=".$cont." where cod_topico=".$linha['cod_topico'];
        $res=Enviar($sock, $consulta);
      }
      $cont++;
    }
  }

}

/* *********************************************************************
   ApagarItem - Apaga o item selecionado (envia para a lixeira)
   Entrada: $sock - BASE DO CURSO
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario que est?removendo, para o hist?ico
   Saida: nenhuma
*/
function ApagarItem ($sock, $cod_item, $cod_usuario)
{
  $linha=RetornaDadosDoItem($sock, $cod_item);
  $consulta="update Portfolio_itens set cod_topico=2 where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'A')";
  $res=Enviar($sock, $consulta);
  ArrumaPosicoes($sock, $linha['cod_topico']);
  AtualizaCompartilhamentoPastas ($sock, $linha['cod_topico'], 'P');
}

/* *********************************************************************
   ExcluirItem - Apaga o item selecionado (definitivamente)
   Entrada: $sock - BASE DO CURSO
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario que est?removendo, para o hist?ico
   Saida: nenhuma
*/
function ExcluirItem ($sock, $cod_item, $cod_usuario)
{
  $linha=RetornaDadosDoItem($sock, $cod_item);
  $consulta="update Portfolio_itens set cod_topico=0 where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'X')";
  $res=Enviar($sock, $consulta);
  ArrumaPosicoes($sock, $linha['cod_topico']);
}


/* *********************************************************************
   ApagarTopico - Apaga o topico selecionado (envia para a lixeira todos os itens das subpastas)
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo do topico
            $cod_usuario - codigo do usuario que est?removendo, para o hist?ico
   Saida: nenhuma
*/
function ApagarTopico ($sock, $cod_topico, $cod_usuario)
{
  /* Recursivamente, apaga os t?icos filhos */
  $query="select cod_topico from Portfolio_topicos where cod_topico_pai=".$cod_topico;
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  if (count($lista)>0)
    foreach($lista as $cod=>$linha)
      ApagarTopico($sock,$linha['cod_topico'],$cod_usuario);

  /* Apaga os itens do topico */
  $query="select cod_item from Portfolio_itens where cod_topico=".$cod_topico;
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  if (count($lista)>0)
    foreach ($lista as $cod=>$linha)
    {
      $query="update Portfolio_itens set cod_topico=2 where cod_item=".$linha['cod_item'];
      Enviar($sock,$query);
      $query="insert into Portfolio_itens_historicos values (".$linha['cod_item'].", ".$cod_usuario.", ".time().", 'A')";
      Enviar($sock,$query);
    }

  /* Apagando o t?ico e rearranjando ordem */
  $query="select cod_topico_pai from Portfolio_topicos where cod_topico=".$cod_topico;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $query="delete from Portfolio_topicos where cod_topico=".$cod_topico;
  Enviar($sock,$query);

  ArrumaPosicoes($sock, $linha['cod_topico_pai']);
  AtualizaCompartilhamentoPastas ($sock, $linha['cod_topico_pai'], 'P');
}

/* *********************************************************************
   RetornaPastaRaizUsuario - Retorna o c?igo da pasta raiz do usu?io ou grupo
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo do usuario que est?removendo, para o hist?ico
            $cod_grupo - codigo do grupo (NULL ou "" se portfolio individual)
   Saida: nenhuma
*/
function RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo)
{
  if ($cod_usuario==-2)
    return 'NULL';
  /* definindo regra de escolha de pasta */
  if ($cod_grupo=="NULL" || $cod_grupo=="")
  {
    $cod_topico_pai=3;
    $regra="(cod_usuario=".$cod_usuario." and cod_grupo IS NULL)";
    $cod_grupo="NULL";
    $rep="#U".$cod_usuario;
  }
  else
  {
    $cod_topico_pai=4;
    $regra="(cod_grupo=".$cod_grupo.")";
    $rep="#G".$cod_grupo;
  }

  $query="select cod_topico from Portfolio_topicos where ".$regra." and cod_topico_pai=".$cod_topico_pai;
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    $linha=RetornaLinha($res);
    return $linha['cod_topico'];
  }
  else
  {
    $query="insert into Portfolio_topicos (cod_topico_pai,cod_usuario,cod_grupo,tipo_compartilhamento,data,posicao_topico,topico) values (".$cod_topico_pai.",".$cod_usuario.",".$cod_grupo.",'P',".time().",".UltimaPosicaoLivre($sock, $cod_topico_pai).",'".$rep."')";
    Enviar($sock,$query);

    return ($cod_topico_pai);
  }
}


/* *********************************************************************
   RecuperarItem - Recupera o item selecionado (da lixeira)
   Entrada: $sock - BASE DO CURSO
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario que est?removendo, para o hist?ico
            $cod_grupo - codigo do grupo (NULL ou "" se portfolio individual)
   Saida: nenhuma
*/
function RecuperarItem ($sock, $cod_item, $cod_usuario,$cod_grupo)
{
  $cod_raiz=RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo);
  $linha=RetornaDadosDoItem($sock, $cod_item);
  $consulta="update Portfolio_itens set cod_topico=".$cod_raiz.", posicao_item=".UltimaPosicaoLivre($sock, $cod_raiz).",tipo_compartilhamento='P' where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'R')";
  $res=Enviar($sock, $consulta);
}

/* *********************************************************************
   AtualizaCompartilhamentoPastas - Atualiza o compartilhamento das pastas (recursivamente)
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo do topico
            $comp - Compartilhamento a ser setado (F ou T);
   Saida: nenhuma
*/
function AtualizaCompartilhamentoPastas ($sock, $cod_topico, $comp)
{
  if ($cod_topico>4)
  {
    if ($comp=="P")
    {
      // Verificamos se a pasta nao tem nenhum item ou filho compartilhado
      // Entao atualizamos o compartilhamento da pasta e dos pais

      $query  = "select tipo_compartilhamento from Portfolio_itens where cod_topico=".$cod_topico." and (tipo_compartilhamento='T' or tipo_compartilhamento='F') order by tipo_compartilhamento desc limit 1";
      $res    = Enviar($sock, $query);
      $linha1 = RetornaLinha($res);

      $query  = "select tipo_compartilhamento from Portfolio_topicos where cod_topico_pai=".$cod_topico." and (tipo_compartilhamento='T' or tipo_compartilhamento='F') order by tipo_compartilhamento desc limit 1";
      $res    = Enviar($sock, $query);
      $linha2 = RetornaLinha($res);

	  $compartilhamento = "";
      unset ($compartilhamento);

      if (is_array($linha1))
      {
        // Existe um item compartilhado. O compartilhamento da pasta eh o mesmo deste item.
        $compartilhamento = $linha1['tipo_compartilhamento'];
      }
      if (is_array($linha2))
      {
        // Existe um filho compartilhado. O comp da pasta eh o de maior prioridade entre o filho e o item acima.
        if (!isset($compartilhamento) || $compartilhamento != 'T')
          $compartilhamento = $linha2['tipo_compartilhamento'];
      }
      if (!isset($compartilhamento))
        // N? existem itens nem pastas comp. A pasta eh n? comp.
        $compartilhamento = 'P';
    }
    else if ($comp=="F")
    {
      // Significa que um item ou filho desta pasta eh comp. com forms.
      // Entao verificamos se esta pasta ou um filho dela nao eh compartilhado com todos
      $consulta="select cod_item from Portfolio_itens where cod_topico=".$cod_topico." and tipo_compartilhamento='T' limit 1";
      $res=Enviar($sock, $consulta);
      $num1=RetornaNumLinhas($res);
      $consulta="select cod_topico from Portfolio_topicos where cod_topico_pai=".$cod_topico." and tipo_compartilhamento='T' limit 1";
      $res=Enviar($sock, $consulta);
      $num2=RetornaNumLinhas($res);

      if ($num1==0 && $num2==0)
      {
        // Nenhum item ou filho eh compartilhado com todos,
        // Compartilhamos a pasta e seu pai com forms.
        $compartilhamento = 'F';
      }
      else
      {
        // Algum item ou filha eh compartilhado com todos,
        // Compartilhamos a pasta e seu pai com todos
        $compartilhamento = 'T';
      }
    }
    else
    {
      // aqui, $comp == 'T'
      // O que significa que um filho ou um item desta pasta eh compartilhado com todos
      // Entao esta pasta e seu pai devem ser compartilhados com todos
      $compartilhamento = 'T';
    }

    // Compartilhando a pasta
    $consulta="update Portfolio_topicos set tipo_compartilhamento='".$compartilhamento."' where cod_topico=".$cod_topico;
    $res=Enviar($sock, $consulta);

    // Encontrando o pai para atualizar.
    $consulta="select cod_topico_pai from Portfolio_topicos where cod_topico=".$cod_topico;
    $res=Enviar($sock, $consulta);
    $linha=RetornaLinha($res);

    AtualizaCompartilhamentoPastas($sock,  $linha['cod_topico_pai'], $compartilhamento);
  }
}

/* *********************************************************************
   EPai - Retorna verdadeiro se $cod_topico for descendente de $cod_topico
   Entrada: $sock - BASE DO CURSO
            $cod_topico_pai - codigo do topico pai
            $cod_topico - codigo do topico
   Saida: true, se For pai, false caso contr?io
*/
function EPai($sock, $cod_topico_pai, $cod_topico)
{
  if ($cod_topico_pai==$cod_topico)
    return(true);
  else
  {
    if ($cod_topico=="")
      return(false);
    else
    {
      $consulta="select cod_topico_pai from Portfolio_topicos where cod_topico=$cod_topico";
      $res=Enviar($sock, $consulta);
      $linha=RetornaLinha($res);
      return(EPai($sock, $cod_topico_pai, $linha['cod_topico_pai']));
    }
  }
}

/* *********************************************************************
   MoverItem - Move o item para outra pasta
   Entrada: $sock - BASE DO CURSO
            $cod_item - item a ser movido
            $cod_usuario - usu?io realizando a?o
            $cod_topico - topico destino
   Saida: nenhuma
*/
function MoverItem ($sock, $cod_item, $cod_usuario, $cod_topico)
{
  $data=time();
  // topico fonte do item
  $consulta="select cod_topico, tipo_compartilhamento from Portfolio_itens where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);

  // em que posicao o item aparece no destino
  $posicao_item=UltimaPosicaoLivre($sock, $cod_topico);

  /* Movendo item */
  $consulta="update Portfolio_itens set cod_topico=".$cod_topico.", posicao_item=".$posicao_item." where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  /* Inserindo dados no hist?ico */
  $consulta="insert into Portfolio_itens_historicos (cod_item, cod_usuario, data, acao) values (".$cod_item.", ".$cod_usuario.", ".$data.", 'M')";
  $res=Enviar($sock, $consulta);

  // atualizando modo de compartilhamento das pastas fonte e destino
  AtualizaCompartilhamentoPastas($sock, $linha['cod_topico'], "P");
  AtualizaCompartilhamentoPastas($sock, $cod_topico, $linha['tipo_compartilhamento']);

  return($posicao_item);
}

/* *********************************************************************
   MoverTopico - Move o topico para outra pasta
   Entrada: $sock - BASE DO CURSO
            $cod_topico - topico a ser movido
            $cod_usuario - usu?io realizando a?o
            $cod_topico_dest - topico destino
   Saida: true se nova posi?o for v?ida
*/
function MoverTopico ($sock, $cod_topico, $cod_usuario, $cod_topico_dest)
{
  if (EPai($sock, $cod_topico, $cod_topico_dest))
  {
    /* Erro! */
    return false;
  }
  else
  {
    $consulta="select cod_topico_pai, tipo_compartilhamento from Portfolio_topicos where cod_topico=".$cod_topico;
    $res=Enviar($sock, $consulta);
    $linha=RetornaLinha($res);
    $posicao_topico=UltimaPosicaoLivre($sock, $cod_topico_dest);
    /* Movendo topico */
    $consulta="update Portfolio_topicos set cod_topico_pai=".$cod_topico_dest.", posicao_topico=".$posicao_topico." where cod_topico=".$cod_topico;
    $res=Enviar($sock, $consulta);
    AtualizaCompartilhamentoPastas($sock, $linha['cod_topico_pai'], "P");
    AtualizaCompartilhamentoPastas($sock, $cod_topico_dest, $linha['tipo_compartilhamento']);
    return true;
  }
}

/* *********************************************************************
  PertenceAoGrupo - Retorna trua se o usuario pertence ao grupo e o grupo est� ativo
  Entrada: $sock        - BASE DO CURSO
           $cod_usuario - codigo do usuario
           $cod_grupo   - codigo do grupo
  Saida: True se pertence, false caso contrario
*/
function PertenceAoGrupo($sock, $cod_usuario, $cod_grupo)
{
  $cod_grupo = VerificaNumeroQuery($cod_grupo);
  if ($cod_grupo === '')
    return false;

  $query  = "select GU.cod_usuario ";
  $query .= "from Grupos_usuario GU, Grupos G ";
  $query .= "where GU.cod_grupo   = ".$cod_grupo."   and ";
  $query .=       "G.cod_grupo    = ".$cod_grupo."   and ";
  $query .=       "GU.cod_usuario = ".$cod_usuario;
  //$query .=       " and G.status<>'X'";

  $res = Enviar($sock,$query);

  if (RetornaNumLinhas($res)>0)
    return true;
  else
    return false;
}

/* *********************************************************************
   RetornaListaDeTopicos - retorna array com lista de topicos
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo do usuario logado
            $eformador - true se o usuario logado for formador
            $cod_usuario_portfolio - codigo do usuario dono do portfolio
            $cod_grupo_portfolio - codigo do grupo dono do portfolio (NULL ou "" se individual
   Saida: array []['cod_topico']
                []['cod_topico_pai']
                []['cod_usuario']
                []['cod_grupo']
                []['topico']
                []['tipo_compartilhamento']
                []['data']
                []['posicao_topico']
                []['nivel'] - Nivel de recurs? at?atingir o t?ico
                []['espacos'] - espacos que ser? usados para exibi?o (&nbsp;)
*/
function RetornaListaDeTopicos ($sock,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio)
{
  global $lista_frases_geral;

  /* Pegando a estrutura do usu?io sendo visualizado */
  $cod_raiz=RetornaPastaRaizUsuario($sock,$cod_usuario_portfolio,$cod_grupo_portfolio);

  $consulta="select * from Portfolio_topicos where cod_topico=".$cod_raiz;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  /* 37 - Pasta Raiz */
  $linha['topico']=RetornaFraseDaLista($lista_frases_geral,37);
  $retorno[0]=$linha;
  $retorno[0]['nivel']=0;
  $retorno[0]['espacos']="";
  $topicos=RetornaListaDeTopicosRecursao($sock, $linha,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio);
  $i=1;
  if (count($topicos))
  {
    foreach ($topicos as $cod => $topico)
    {
      $retorno[$i]=$topico;
      $retorno[$i]['nivel']++;
      $retorno[$i]['espacos']=$retorno[$i]['espacos']."&nbsp;&nbsp;";
      $i++;
    }
  }
  return($retorno);
}

/* *********************************************************************
   RetornaListaDeTopicosRecursao - retorna array com lista de topicos a partir de um topico dado
   Entrada: $sock - BASE DO CURSO
            $topico - array com 1 elemento []['cod_topico'], contendo o c?igo do t?ico
            $cod_usuario - codigo do usuario logado
            $eformador - true se o usuario logado for formador
            $cod_usuario_portfolio - codigo do usuario dono do portfolio
            $cod_grupo_portfolio - codigo do grupo dono do portfolio (NULL ou "" se individual
   Saida: array []['cod_topico']
                []['cod_topico_pai']
                []['cod_usuario']
                []['cod_grupo']
                []['topico']
                []['tipo_compartilhamento']
                []['data']
                []['posicao_topico']
                []['nivel'] - Nivel de recurs? at?atingir o t?ico
                []['espacos'] - espacos que ser? usados para exibi?o (&nbsp;)
*/
function RetornaListaDeTopicosRecursao ($sock, $topico,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio)
{
  if ($cod_usuario==$cod_usuario_portfolio)
    $regra="";
  else if ($cod_grupo_portfolio=="NULL" || $cod_grupo_portfolio=="")
  {
    if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }
  else
  {
    if (PertenceAoGrupo($sock, $cod_usuario, $cod_grupo_portfolio))
      $regra="";
    else if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }

  $consulta="select * from Portfolio_topicos where cod_topico_pai=".$topico['cod_topico']." ".$regra." order by posicao_topico";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  $i=0;
  if (isset($lista_retorno)) reset($lista_retorno);
  $lista_retorno = array();
  if (count($lista))
    foreach ($lista as $cod1 => $linha1)
    {
      $lista_retorno[$i]=$linha1;
      $lista_retorno[$i]['nivel']=0;
      $lista_retorno[$i]['espacos']="";

      $i++;
      $lista_filho=RetornaListaDeTopicosRecursao($sock, $linha1,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio);
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

/* *********************************************************************
   RenomearTopico - Renomeia o t?ico
   Entrada: $sock - BASE DO CURSO
            $cod_topico - topico a ser renomear
            $novo_nome - novo nome a ser usado
   Saida: nenhuma
*/
function RenomearTopico ($sock, $cod_topico, $novo_nome)
{
  $consulta="update Portfolio_topicos set topico='".$novo_nome."' where cod_topico=".$cod_topico;
  $res=Enviar($sock, $consulta);
}


/* *********************************************************************
 * RetornaGrupoPortfolio - Retorna o numero do grupo de dado portifolio
 * Entrada: $sock - Base do Curso
 * 			$cod_item - numero do portfolio
 * Saida: numero do grupo ou NULL (se não houver grupo)
 */
function RetornaGrupoPortfolio($sock, $cod_item){
	$query = "select cod_grupo from Portfolio_itens where cod_item = ".$cod_item;
	$res = Enviar($sock, $query);
	$ret = RetornaLinha($res);
	return($ret['cod_grupo']);
}

/* *********************************************************************
   MudarCompartilhamentoEAtualiza - Muda o compartilhamento do item dado
   Entrada: $sock - BASE DO CURSO
            $cod_item - item a ser alterado compartilhamento
            $tipo_comp - Tipo do Compartilhamento (F ou T)
   Saida: nenhuma
*/
function MudarCompartilhamentoEAtualiza($dadosForm, $novo_comp){
	$cod_curso=$dadosForm['cod_curso'];
	$sock=Conectar($cod_curso);
	
	$cod_grupo = RetornaGrupoPortfolio($sock, $dadosForm['cod_item']);
	$em_grupo = ($cod_grupo != NULL );
	if ($em_grupo){
		AtualizaFerramentasNovaGrupo($sock,"15",$cod_grupo);
		AtualizaFerramentasNova($sock, "15", $dadosForm['tipo_comp']);
	} else {
		AtualizaFerramentasNovaUsuario($sock,"15", $dadosForm['cod_usuario']);
		AtualizaFerramentasNova($sock, "15", $dadosForm['tipo_comp']);
	}
	return (MudarCompartilhamento($dadosForm, $novo_comp));
}

/* *********************************************************************
   MudarCompartilhamento - Muda o compartilhamento do item dado
   Entrada: $sock - BASE DO CURSO
            $cod_item - item a ser alterado compartilhamento
            $tipo_comp - Tipo do Compartilhamento (F ou T)
   Saida: nenhuma
*/
function MudarCompartilhamento ($dadosForm, $novo_comp)
{
  $objResponse = new xajaxResponse();

  $tipo_comp=$dadosForm['tipo_comp'];
  $cod_item=$dadosForm['cod_item'];
  $cod_curso=$dadosForm['cod_curso'];
  $cod_usuario=$dadosForm['cod_usuario'];
  $texto = $dadosForm['texto'];

  $sock=Conectar($cod_curso);
  $query="select tipo_compartilhamento from Portfolio_itens where cod_item=".$cod_item;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha['tipo_compartilhamento']!=$tipo_comp)
  {
    $consulta="update Portfolio_itens set tipo_compartilhamento='".$tipo_comp."', data=".time()." where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);
    $consulta="select cod_topico from Portfolio_itens where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);
    $linha=RetornaLinha($res);
    AtualizaCompartilhamentoPastas($sock, $linha['cod_topico'], $tipo_comp);
  }

  Desconectar($sock);

  // Imprime no div valores do formul?io
  $objResponse->assign("tr_".$cod_item, "className", "novoitem");
  $objResponse->assign("comp_".$cod_item, "innerHTML", print_r($novo_comp, true));
  //addEvent não suporta o parametro 'event'
  $objResponse->script("document.getElementById('comp_".$cod_item."').onclick = function(event) { js_cod_item='".$cod_item."'; AtualizaComp('".$tipo_comp."'); MostraLayer(cod_comp,140, event); }" );
  $objResponse->assign("data_".$cod_item, "innerHTML", UnixTime2Data(time()));

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->call("mostraFeedback", $texto, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}



/* *********************************************************************
   RetornaItensDoTopico - Retorna array com os itens do t?ico dado
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_topico - t?ico
            $cod_usuario - codigo do usuario logado
            $eformador - true se o usuario logado for formador
            $cod_usuario_portfolio - codigo do usuario dono do portfolio
            $cod_grupo_portfolio - codigo do grupo dono do portfolio (NULL ou "" se individual
   Saida: array com []['cod_item']
                    []['cod_topico']
                    []['cod_usuario']
                    []['cod_grupo']
                    []['titulo']
                    []['texto']
                    []['tipo_compartilhamento']
                    []['data']
                    []['data_ativo']
                    []['data_inativo']
                    []['posicao_item']
                    []['status']
                    []['inicio_edicao']
                    []['num_comentarios']
                    []['num_comentarios_alunos'] - numero de coment?ios feitos por alunos que n? o usuario
                    []['num_comentarios_formadores'] - numero de coment?ios feitos por formadores que n? o usuario
                    []['num_comentarios_usuario'] - numero de coment?ios feitos pelo usuario
                    []['data_comentarios'] - Maior data de um coment?io

*/
function RetornaItensDoTopico ($sock, $cod_curso, $cod_topico, $cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio)
{
 if ($cod_grupo_portfolio=="NULL" || $cod_grupo_portfolio=="")
  {
  	// Rafael - 22/11/2004 - Movi esta condeicao para dentro de portfolios Individuais pois para
	// alguem ver items de grupos nao compartilhados, a pessoal deve ser integrante do grupo
    if ($cod_usuario==$cod_usuario_portfolio)
      $regra="";
    else if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }
  else
  {
    if (PertenceAoGrupo($sock,$cod_usuario, $cod_grupo_portfolio))
      $regra="";
    else if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }

  if (EFormadorComVisaoDeAluno($sock, $cod_curso, $cod_usuario))
    $regra="and tipo_compartilhamento='T'";

  $consulta="select * from Portfolio_itens where cod_topico=".$cod_topico." ".$regra." order by posicao_item";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  if (count($lista))
    foreach($lista as $cod => $linha)
    {
      $tmp=RetornaNumComentariosItem ($sock, $cod_usuario, $linha['cod_item']);
      $lista[$cod]['num_comentarios']=$tmp['num_comentarios'];
      $lista[$cod]['num_comentarios_alunos']=$tmp['num_comentarios_alunos'];
      $lista[$cod]['num_comentarios_formadores']=$tmp['num_comentarios_formadores'];
      $lista[$cod]['num_comentarios_usuario']=$tmp['num_comentarios_usuario'];
      $lista[$cod]['data_comentarios']=$tmp['data_comentarios'];
    }

  return($lista);
}

/* *********************************************************************
   RetornaNumComentariosItem - retorna o nmero de coment?ios de um item
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - cod. usu?io logado
            $cod_item - item
   Saida: array com ['num_comentarios']
                    ['num_comentarios_alunos'] - numero de coment?ios feitos por alunos que n? o usuario
                    ['num_comentarios_formadores'] - numero de coment?ios feitos por formadores que n? o usuario
                    ['num_comentarios_usuario'] - numero de coment?ios feitos pelo usuario
                    ['data_comentarios'] - Maior data de um coment?io
*/
function RetornaNumComentariosItem ($sock, $cod_usuario, $cod_item)
{
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select Pic.cod_comentarista 'cod', count(*) 'ct', max(Pic.data) 'dt' from Portfolio_itens_comentarios Pic where Pic.cod_item=".$cod_item." and Pic.status='A' group by Pic.cod_comentarista order by Pic.cod_comentarista";
  $res=Enviar($sock,$query);

  $num_f=0;
  $num_a=0;
  $num_u=0;
  $data=0;

  while ($linha=RetornaLinha($res))
  {
    $query2="select tipo_usuario 'tu' from ".$dbnamebase.".Usuario_curso where cod_usuario='".$cod_usuario."' and cod_curso='".$_SESSION['cod_curso']."'";
    $res2=Enviar($sock,$query2);
    $linha2=RetornaLinha($res);
    $linha['tu'] = $linha2['tu'];

    if ($linha['cod']==$cod_usuario)
      $num_u+=$linha['ct'];
    else if ($linha['tu']=="F")
      $num_f+=$linha['ct'];
    else
      $num_a+=$linha['ct'];
    if ($data<$linha['dt'])
      $data=$linha['dt'];
  }
  $saida['num_comentarios']=$num_f+$num_a+$num_u;
  $saida['num_comentarios_formadores']=$num_f;
  $saida['num_comentarios_alunos']=$num_a;
  $saida['num_comentarios_usuario']=$num_u;
  $saida['data_comentarios']=$data;
  return $saida;
}

/* *********************************************************************
   RetornaTopicosDoTopico - Retorna array com os topicos filhos do t?ico dado
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_topico - t?ico
            $cod_usuario - codigo do usuario logado
            $eformador - true se o usuario logado for formador
            $cod_usuario_portfolio - codigo do usuario dono do portfolio
            $cod_grupo_portfolio - codigo do grupo dono do portfolio (NULL ou "" se individual
   Saida: array com []['cod_topico']
                    []['cod_topico_pai']
                    []['cod_usuario']
                    []['cod_grupo']
                    []['topico']
                    []['tipo_compartilhamento']
                    []['data']
                    []['posicao_topico']
*/
function RetornaTopicosDoTopico ($sock, $cod_curso, $cod_topico, $cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio)
{
  if ($cod_usuario==$cod_usuario_portfolio)
    $regra="";
  else if ($cod_grupo_portfolio=="NULL" || $cod_grupo_portfolio=="")
  {
    if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }
  else
  {
    if (PertenceAoGrupo($sock, $cod_usuario, $cod_grupo_portfolio))
      $regra="";
    else if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }

  if (EFormadorComVisaoDeAluno($sock, $cod_curso, $cod_usuario))
    $regra="and tipo_compartilhamento='T'";

  $consulta="select * from Portfolio_topicos where cod_topico_pai=".$cod_topico." ".$regra." order by posicao_topico";

  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   NomeGrupo - Retorna o nome do grupo
   Entrada: $sock - BASE DO CURSO
            $cod_grupo - grupo
   Saida: nome do grupo
*/
function NomeGrupo($sock, $cod_grupo)
{
  $consulta="select nome from Grupos where cod_grupo=".$cod_grupo;
  $res=Enviar($sock, $consulta);
  $num=RetornaNumLinhas($res);
  $linha=RetornaLinha($res);
  return $linha['nome'];
}

/* *********************************************************************
   StatusGrupo - Retorna o estado de um grupo
   Entrada: $sock - BASE DO CURSO
            $cod_grupo - grupo
   Saida: Status - A - Ativo, X - Apagado
*/
function StatusGrupo($sock, $cod_grupo)
{
  $consulta="select status from Grupos where cod_grupo=".$cod_grupo;
  $res=Enviar($sock, $consulta);
  $num=RetornaNumLinhas($res);
  $linha=RetornaLinha($res);
  if ($linha['status']!="X")
    $linha['status']="A";
  return $linha['status'];
}

/* *********************************************************************
   RetornaTopicosBase - retorna topicos raizes (portfolios) de usuarios ou grupos
   Entrada: $sock                       - sock de conexao com a base
            $cod_usuario                - codigo do usuario logado
            $cod_usuario_portfolio      - codigo do usuario dono dos portfolios a exibir
            $eformador                  - booleano, se o cara eh formador
            $acao_portfolio_s           - indica se
                                           'I' - portfolios individuais ou
                                           'G' - de grupo

   Saida: array com [$cod_topico]['cod_topico_pai']
                    [$cod_topico]['cod_usuario']
                    [$cod_topico]['cod_grupo']
                    [$cod_topico]['topico']
                    [$cod_topico]['tipo_compartilhamento']
                    [$cod_topico]['data']
                    [$cod_topico]['posicao_topico']
                    [$cod_topico]['num_itens']
                    [$cod_topico]['num_itens_comentados']
                    [$cod_topico]['num_itens_nao_comentados']
                    [$cod_topico]['figura']                    - nome do arquivo da figura a exibir
*/
function RetornaTopicosBase ($sock, $cod_usuario, $cod_usuario_portfolio, $eformador, $acao_portfolio_s, $cod_curso)
{
  global $lista_frases, $ferramenta_grupos_s;
  $lista_retorno = "";
  unset ($lista_retorno);
  
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  /*precisa dessa função pois na primeira vez em que uma pessoa entra na ferramenta portfolio 
  eh necessario criar a pasta raiz*/
  RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo);

  // lista de usuarios
  $query = "select USUC.cod_usuario, USU.nome, USUC.tipo_usuario, USUC.portfolio
          from ".$dbnamebase.".Usuario USU
          inner join ".$dbnamebase.".Usuario_curso USUC ON ((USUC.cod_usuario_global = USU.cod_usuario) AND (USUC.cod_curso = '".$cod_curso."'))";

  $res = Enviar ($sock, $query);
  $lista = RetornaArrayLinhas ($res);
  if (is_array ($lista))
    foreach ($lista as $c => $usuario)
    {
      $lista_usr[ $usuario['cod_usuario'] ]['portfolio'] = $usuario['portfolio'];
      $lista_usr[ $usuario['cod_usuario'] ]['nome']         = $usuario['nome'];
      $lista_usr[ $usuario['cod_usuario'] ]['tipo_usuario'] = $usuario['tipo_usuario'];
    }

  // lista de grupos
  $query = "select * from Grupos order by status";
  $res   = Enviar ($sock, $query);
  $lista = RetornaArrayLinhas ($res);
  unset ($lista_grupos);
  if (is_array ($lista))
    foreach ($lista as $c => $linha)
    {
      $lista_grupos [$cod_grupo = $linha['cod_grupo']]['nome'] = $linha['nome'];
      $lista_grupos [$cod_grupo]['status'] = $linha['status'];
    }

  // lista de grupos aos quais o usuario LOGADO pertence
  $query = "select * from Grupos_usuario where cod_usuario=".$cod_usuario;
  $res   = Enviar ($sock, $query);
  $lista = RetornaArrayLinhas ($res);
  unset ($grupos_usr);
  if (is_array ($lista))
    foreach ($lista as $c => $linha)
    {
      $grupos_usr[$linha['cod_grupo']] = true;
    }


  if ($eformador)
  {
    // variavel para escolher compartilhamento dentro da query SQL
    $regra_comp = "(tipo_compartilhamento = 'F' or tipo_compartilhamento = 'T')";
    // variavel para escolher compartilhamento ao contar numero de itens dentro de um portfolio
    $comp = 'F';
  }
  else
  {
    $regra_comp = "tipo_compartilhamento = 'T'";
    $comp = 'T';
  }
 
   /*MONTA MEUS PORTFOLIOS*/
    if ($acao_portfolio_s == 'M')
    {
      // primeiro pego portfolio do usuario
      $query = "select * from Portfolio_topicos where cod_topico_pai=3 and cod_usuario=".$cod_usuario;
      $res   = Enviar ($sock, $query);
      $portfolio_pessoal = RetornaLinha ($res);
      $lista_retorno [$cod_topico = $portfolio_pessoal['cod_topico']] = $portfolio_pessoal;
     // 76 - Portfolio de
      $lista_retorno [$cod_topico]['topico'] = RetornaFraseDaLista ($lista_frases, 76)." ".$lista_usr [ $cod_usuario ]['nome'];
      $lista_retorno [$cod_topico]['figura'] = "icPasta";
      $lista_retorno [$cod_topico]['num_itens'] = $num_itens = RetornaNumItensPortfolio($sock, $cod_topico, 'P');
      $lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio($sock, $cod_topico, 'P');
      $lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
      
      /*GRUPO*/
      if($ferramenta_grupos_s)/*variavel global*/
      {
         $query = "select * from Portfolio_topicos where cod_topico_pai=4";
         $res   = Enviar ($sock, $query);
         $lista = RetornaArrayLinhas ($res);
         if (is_array ($lista))
         {
            unset($port_meus_grp);
            foreach ($lista as $c => $linha_port)
            {            
               $cod_grupo = $linha_port['cod_grupo'];
               if ($grupos_usr[ $cod_grupo ])                                                                                              {                           
                  $port_meus_grp [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
                  // 77 - Portfolio do grupo
                  $port_meus_grp [ $cod_topico ]['topico'] = RetornaFraseDaLista ($lista_frases, 77)." ".$lista_grupos[$cod_grupo]['nome'];
                  // verifica se o grupo ainda existe
                  if ($lista_grupos[ $cod_grupo = $linha_port['cod_grupo'] ]['status'] != 'X')
                     $port_meus_grp [ $cod_topico ]['figura'] = "icPasta2";
                  else
                     $port_meus_grp [ $cod_topico ]['figura'] = "icPasta3";
               }   
            }
            if (is_array($port_meus_grp))
            {            
               uasort($port_meus_grp, CompareArrayStatus);
               foreach ($port_meus_grp as $cod_topico => $portfolio)
               {
                  $lista_retorno [ $cod_topico ] = $portfolio;
                  $lista_retorno [ $cod_topico ]['num_itens'] = $num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, 'P');
                  $lista_retorno [ $cod_topico ]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio ($sock, $cod_topico, 'P');
                  $lista_retorno [ $cod_topico ]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
               }
            }               
         }
      }           
    }
                                                     
  else if ($acao_portfolio_s == 'I')
  {
    // primeiro pego portfolio do usuario
    $query = "select * from Portfolio_topicos where cod_topico_pai=3 and cod_usuario=".$cod_usuario;
    $res   = Enviar ($sock, $query);
    $portfolio_pessoal = RetornaLinha ($res);

    $lista_retorno [$cod_topico = $portfolio_pessoal['cod_topico']] = $portfolio_pessoal;
    // 76 - Portfolio de
    $lista_retorno [$cod_topico]['topico'] = RetornaFraseDaLista ($lista_frases, 76)." ".$lista_usr [ $cod_usuario ]['nome'];
    $lista_retorno [$cod_topico]['figura'] = "icPasta";
    $lista_retorno [$cod_topico]['num_itens'] = $num_itens = RetornaNumItensPortfolio($sock, $cod_topico, 'P');
    $lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio($sock, $cod_topico, 'P');
    $lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
    //  $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,'P');

    // segundo, pego outros portfolios individuais que estao marcados como visiveis, isto e, o coordenador aprovou a permanencia do portifolio do aluno
    
    $query = "select * from Portfolio_topicos where cod_topico_pai=3 and cod_usuario <> ".$cod_usuario;
    $res   = Enviar ($sock, $query);
    $array_ind = RetornaArrayLinhas ($res);

    if (is_array ($array_ind))
    {
      unset ($port_ind);        // portfolios individuais
      unset ($port_rej);        // portfolios de alunos rejeitados

      foreach ($array_ind as $x => $linha_ind)
      {
      	$cod_usr = $linha_ind ['cod_usuario'];
      	if($lista_usr [ $cod_usr]['portfolio'] == 'ativado'){ // O portfolio esta ativo
        	if (($lista_usr [ $cod_usr]['tipo_usuario'] != 'r') && ($lista_usr [ $cod_usr]['tipo_usuario'] != 'f'))   // alguem do curso
        	{
          	$port_ind [ $cod_topico = $linha_ind ['cod_topico'] ] = $linha_ind;
          	// 76 - Portfolio de
          	$port_ind [ $cod_topico ]['topico'] = RetornaFraseDaLista ($lista_frases, 76)." ".$lista_usr [ $cod_usr ]['nome'];
          	$port_ind [ $cod_topico ]['figura'] = "icPasta2";
        	}
        	else     // aluno rejeitado ou formador desligado
        	{
          	$port_rej [ $cod_topico = $linha_ind ['cod_topico'] ] = $linha_ind;
          	// 76 - Portfolio de
          	$port_rej [ $cod_topico ]['topico'] = RetornaFraseDaLista ($lista_frases, 76)." ".$lista_usr [ $cod_usr ]['nome'];
          	$port_rej [ $cod_topico ]['figura'] = "icPasta3";
        	}
      	}
      }

      if (is_array ($port_ind))
      {
        uasort ($port_ind, CompareArrayTopicos);
        foreach ($port_ind as $cod_topico => $portfolio_individual)
        {
          $num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
          // LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
          // if ($num_itens > 0)
          {
            $lista_retorno [ $cod_topico ] = $portfolio_individual;
            $lista_retorno [ $cod_topico ]['num_itens'] = $num_itens;
            $lista_retorno [ $cod_topico ]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio($sock, $cod_topico, $comp);
            $lista_retorno [ $cod_topico ]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
            //     $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
          }
        }
      }

      if (is_array ($port_rej))
      {
        uasort ($port_rej, CompareArrayTopicos);
        foreach ($port_rej as $cod_topico => $portfolio_rejeitado)
        {
          $num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
          // LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
          // if ($num_itens > 0)
          {
            $lista_retorno [ $cod_topico ] = $portfolio_rejeitado;
            $lista_retorno [ $cod_topico ]['num_itens'] = $num_itens;
            $lista_retorno [ $cod_topico ]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio($sock, $cod_topico, $comp);
            $lista_retorno [ $cod_topico ]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
            //     $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
          }
        }
      }
    }
  }
  else if ($acao_portfolio_s == 'G')
  {
    $query = "select * from Portfolio_topicos where cod_topico_pai=4";
    $res   = Enviar ($sock, $query);
    $lista = RetornaArrayLinhas ($res);

    if (is_array ($lista))
    {
      unset ($port_meus_grp);           // portfolios de grupos aos quais pertenco
      unset ($port_outros_grp);         // portfolios de grupos aos quais NAO pertenco
      unset ($port_grupos_x);           // portfolios de grupos apagados

      foreach ($lista as $c => $linha_port)
      {
        // SE UM GRUPO FOI APAGADO, O SEU PORTFOLIO SERA EXIBIDO INDEPENDENTE DO TIPO DE COMPARTILHAMENTO QUE ELE TINHA QDO O GRUPO ERA ATIVO
        if ($lista_grupos[ $cod_grupo = $linha_port['cod_grupo'] ]['status'] == 'X') // grupo apagado
        {
          $port_grupos_x [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
          // 77 - Portfolio do grupo
          $port_grupos_x [ $cod_topico ]['topico'] = RetornaFraseDaLista ($lista_frases, 77)." ".$lista_grupos[$cod_grupo]['nome'];
          $port_grupos_x [ $cod_topico ]['figura'] = "icPasta3";
	  // $port_grupos_x [ $cod_topico ]['linkavel'] = "Y";
        }
        else if ($grupos_usr[ $cod_grupo ])     // portfolio de um grupo ao qual o usuario pertence
        {
          $port_meus_grp [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
          // 77 - Portfolio do grupo
          $port_meus_grp [ $cod_topico ]['topico'] = RetornaFraseDaLista ($lista_frases, 77)." ".$lista_grupos[$cod_grupo]['nome'];
          $port_meus_grp [ $cod_topico ]['figura'] = "icPasta";
	   //$port_meus_grp [ $cod_topico ]['linkavel'] = "Y";
        }
        else //if ( ($tipo = $linha_port ['tipo_compartilhamento']) == 'T' || $tipo == $comp)
		// portfolio de um grupo ativo ao qual o usuario nao pertence, neste caso somente retornamos se o tipo de compartilhamento permitir
	     
	     // Rafael - 22/11/2004
			// Cancelei a restricao acima, agora todos portfolios sao retornados	     
        {
          $port_outros_grp [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
          // 77 - Portfolio do grupo
          $port_outros_grp [ $cod_topico ]['topico'] = RetornaFraseDaLista ($lista_frases, 77)." ".$lista_grupos[$cod_grupo]['nome'];
          $port_outros_grp [ $cod_topico ]['figura'] = "icPasta2";
	 // $port_outros_grp [ $cod_topico ]['linkavel'] = "Y";
        }
      }

      // copiando para o retorno os portfolios de grupos aos quais pertenco: copio todos aqueles aos quais pertenco
      if (is_array ($port_meus_grp))
      {
        uasort ($port_meus_grp, CompareArrayTopicos);
        foreach ($port_meus_grp as $cod_topico => $portfolio)
        {
          $lista_retorno [ $cod_topico ] = $portfolio;
          $lista_retorno [ $cod_topico ]['num_itens'] = $num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, 'P');
          $lista_retorno [ $cod_topico ]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio ($sock, $cod_topico, 'P');
          $lista_retorno [ $cod_topico ]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
        //  $lista_retorno [ $cod_topico ]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,'P');
        }
      }

      // copiando para o retorno os portfolios de grupos aos quais eu nao pertenco: copio apenas aqueles que tem algum item dentro
      if (is_array ($port_outros_grp))
      {
        uasort ($port_outros_grp, CompareArrayTopicos);
        foreach ($port_outros_grp as $cod_topico => $portfolio)
        {
          $num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
          // LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
          // if ($num_itens > 0)
          {
            $lista_retorno [$cod_topico] = $portfolio;
            $lista_retorno [$cod_topico]['num_itens'] = $num_itens;
            $lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio ($sock, $cod_topico, $comp);
            $lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
            //    $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
          }
        }
      }
/*
      // copiando para o retorno os portfolios de grupos apagados
      if (is_array ($port_grupos_x))
      {
        uasort ($port_grupos_x, CompareArrayTopicos);
        foreach ($port_grupos_x as $cod_topico => $portfolio)
        {
          $num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
          // LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
          // if ($num_itens > 0)
          {
            $lista_retorno [$cod_topico] = $portfolio;
            $lista_retorno [$cod_topico]['num_itens'] = $num_itens;
            $lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio ($sock, $cod_topico, $comp);
            $lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
            //     $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
          }
        }
      }
*/ // Retirado portfolios de grupos apagados da lista de grupos
    }
  }
  else if ($acao_portfolio_s == 'F') {
    $query = "select * from Portfolio_topicos where cod_topico_pai=4";
    $res   = Enviar ($sock, $query);
    $lista = RetornaArrayLinhas ($res);

    if (is_array ($lista))
    {
      unset ($port_meus_grp);           // portfolios de grupos aos quais pertenco
      unset ($port_outros_grp);         // portfolios de grupos aos quais NAO pertenco
      unset ($port_grupos_x);           // portfolios de grupos apagados

      foreach ($lista as $c => $linha_port)
      {

        // SE UM GRUPO FOI APAGADO, O SEU PORTFOLIO SERA EXIBIDO INDEPENDENTE DO TIPO DE COMPARTILHAMENTO QUE ELE TINHA QDO O GRUPO ERA ATIVO
        if ($lista_grupos[ $cod_grupo = $linha_port['cod_grupo'] ]['status'] == 'X') // grupo apagado
        {
          $port_grupos_x [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
          // 77 - Portfolio do grupo
          $port_grupos_x [ $cod_topico ]['topico'] = RetornaFraseDaLista ($lista_frases, 77)." ".$lista_grupos[$cod_grupo]['nome'];
          $port_grupos_x [ $cod_topico ]['figura'] = "icPasta3";
     // $port_grupos_x [ $cod_topico ]['linkavel'] = "Y";
        }
/*        else if ($grupos_usr[ $cod_grupo ])     // portfolio de um grupo ao qual o usuario pertence
        {
          $port_meus_grp [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
          // 77 - Portfolio do grupo
          $port_meus_grp [ $cod_topico ]['topico'] = RetornaFraseDaLista ($lista_frases, 77)." ".$lista_grupos[$cod_grupo]['nome'];
          $port_meus_grp [ $cod_topico ]['figura'] = "portfolio_g_p.gif";
      //$port_meus_grp [ $cod_topico ]['linkavel'] = "Y";
        }
        else //if ( ($tipo = $linha_port ['tipo_compartilhamento']) == 'T' || $tipo == $comp)
      // portfolio de um grupo ativo ao qual o usuario nao pertence, neste caso somente retornamos se o tipo de compartilhamento permitir

        // Rafael - 22/11/2004
         // Cancelei a restricao acima, agora todos portfolios sao retornados      
        {
          $port_outros_grp [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
          // 77 - Portfolio do grupo
          $port_outros_grp [ $cod_topico ]['topico'] = RetornaFraseDaLista ($lista_frases, 77)." ".$lista_grupos[$cod_grupo]['nome'];
          $port_outros_grp [ $cod_topico ]['figura'] = "portfolio_g_n.gif";
    // $port_outros_grp [ $cod_topico ]['linkavel'] = "Y";
        }
*/ // Deixado somente a parte referente aos portfolios encerrados
      }
/*
      // copiando para o retorno os portfolios de grupos aos quais pertenco: copio todos aqueles aos quais pertenco
      if (is_array ($port_meus_grp))
      {
        uasort ($port_meus_grp, CompareArrayTopicos);
        foreach ($port_meus_grp as $cod_topico => $portfolio)
        {
          $lista_retorno [ $cod_topico ] = $portfolio;
          $lista_retorno [ $cod_topico ]['num_itens'] = $num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, 'P');
          $lista_retorno [ $cod_topico ]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio ($sock, $cod_topico, 'P');
          $lista_retorno [ $cod_topico ]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
        //  $lista_retorno [ $cod_topico ]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,'P');
        }
      }

      // copiando para o retorno os portfolios de grupos aos quais eu nao pertenco: copio apenas aqueles que tem algum item dentro
      if (is_array ($port_outros_grp))
      {
        uasort ($port_outros_grp, CompareArrayTopicos);
        foreach ($port_outros_grp as $cod_topico => $portfolio)
        {
          $num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
          // LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
          // if ($num_itens > 0)
          {
            $lista_retorno [$cod_topico] = $portfolio;
            $lista_retorno [$cod_topico]['num_itens'] = $num_itens;
            $lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio ($sock, $cod_topico, $comp);
            $lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
            //    $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
          }
        }
      }
*/ // Deixado somente a parte referente aos portfolios encerrados
      // copiando para o retorno os portfolios de grupos apagados
      if (is_array ($port_grupos_x))
      {
        uasort ($port_grupos_x, CompareArrayTopicos);
        foreach ($port_grupos_x as $cod_topico => $portfolio)
        {
          $comp='P';
          $num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
          // LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
          // if ($num_itens > 0)
          {
            $lista_retorno [$cod_topico] = $portfolio;
            $lista_retorno [$cod_topico]['num_itens'] = $num_itens;
            $lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio ($sock, $cod_topico, $comp);
            $lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
            //     $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
          }
        }
      }
    }

  }
  else
  {
    echo("<big><font color=red>Erro em RetornaTopicosBase: variavel acao_portfolio_s</font></big>");
    exit();
  }

  return $lista_retorno;
}

function CompareArrayTopicos($ar1, $ar2)
{
  if ($ar1['topico']<$ar2['topico'])
    return -1;
  else if ($ar1['topico']>$ar2['topico'])
    return 1;
  else
    return 0;
}

function CompareArrayStatus($ar1, $ar2)
{
   if ($ar1['status']<$ar2['status'])
       return -1;
   else if ($ar1['status']>$ar2['status'])
       return 1;
   else
       return 0;
                   
}
/* *********************************************************************
   RetornaNumItensPortfolio - Retorna o numero de itens em um portf?io
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo da pasta base
            $portfolios - 'P' - Todos os itens, 'F' - Itens tipo F e T, 'T' - Só tipo T
   Saida: numero de itens
*/
function RetornaNumItensPortfolio($sock, $cod_topico,$portfolios)
{
  $total=0;
  if ($portfolios=='T')
    $topico="tipo_compartilhamento='T' and ";
  else if ($portfolios=='F')
    $topico="tipo_compartilhamento<>'P' and ";
  else
    $topico="";

  $consulta="select cod_topico from Portfolio_topicos where ".$topico." cod_topico_pai=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);

  if (count($lista))
    foreach($lista as $cod => $linha)
      $total = $total + RetornaNumItensPortfolio($sock, $linha['cod_topico'],$portfolios);

  $consulta="select count(cod_item) from Portfolio_itens where ".$topico." cod_topico=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $count = RetornaArrayLinhas($res);
  $total = $total + $count[0][0];
  return $total;
}

/* *********************************************************************
   RetornaNumItensPortfolio - Retorna o numero de itens em um portf?io
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo da pasta base
            $portfolios - 'P' - Todos os itens, 'F' - Itens tipo F e T, 'T' - S?tipo T
   Saida: numero de itens
*/
function RetornaNumItensComentadosPortfolio($sock, $cod_topico,$portfolios)
{
  $total=0;
  if ($portfolios=='T')
    $topico="Pi.tipo_compartilhamento='T' and ";
  else if ($portfolios=='F')
    $topico="Pi.tipo_compartilhamento<>'P' and ";
  else
    $topico="";

  $consulta="select cod_topico from Portfolio_topicos Pi where ".$topico." Pi.cod_topico_pai=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);

  if (count($lista))
    foreach($lista as $cod => $linha)
      $total = $total + RetornaNumItensComentadosPortfolio($sock, $linha['cod_topico'],$portfolios);

  $consulta="select distinct Pi.cod_item from Portfolio_itens Pi, Portfolio_itens_comentarios Pic where ".$topico." Pi.cod_topico=".$cod_topico." and Pi.cod_item=Pic.cod_item and Pic.status<>'X'";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  
  if ((count($lista))&&($lista!=""))
    $total+=count($lista);

  return $total;
}


/* *********************************************************************
   MudarPosicaoItem - Muda a posi?o do item (utiliza semaforo)
   Entrada: $sock - BASE DO CURSO
            $cod_topico_raiz - t?ico a partir do qual ser? rearranjados os itens
            $cod_item - Codigo do item a ter a posi?o mudada
            $posicao - valor da nova posi?o do item
   Saida: nenhuma
*/
function MudarPosicaoItem ($sock, $cod_topico_raiz, $cod_item, $posicao)
{
  $consulta="select cod_item from Portfolio_itens where cod_topico=".$cod_topico_raiz." order by posicao_item";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  $cont=1;
   PegaSemaforo($sock, "Portfolio");
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
          $consulta="update Portfolio_itens set posicao_item=".$cont." where cod_item=".$cod_item;
          $res=Enviar($sock, $consulta);
          $cont++;
          $posicao=-1;
        }
      }
      else
      {
        if ($posicao==$cont)
        {
          $consulta="update Portfolio_itens set posicao_item=".$cont." where cod_item=".$cod_item;
          $res=Enviar($sock, $consulta);
          $cont++;
          $posicao=-1;
        }
        $consulta="update Portfolio_itens SET posicao_item=".$cont." where cod_item=".$linha['cod_item'];
        $res=Enviar($sock, $consulta);
        $cont++;
      }
    }
    if ($posicao>0)
    {
      $consulta="UPDATE Portfolio_itens set posicao_item=".$cont." where cod_item=".$cod_item;
      $res=Enviar($sock, $consulta);
      $cont++;
    }
  }
   LiberaSemaforo($sock, "Portfolio");
}

/* *********************************************************************
   MudarPosicaoTopico - Muda a posi?o do t?ico (utiliza semaforo)
   Entrada: $sock - BASE DO CURSO
            $cod_topico_raiz - t?ico a partir do qual ser? rearranjados os itens
            $cod_topico - Codigo do topico a ter a posi?o mudada
            $posicao - valor da nova posi?o do item
   Saida: nenhuma
*/
function MudarPosicaoTopico ($sock, $cod_topico_raiz, $cod_topico, $posicao)
{
  $consulta="select * from Portfolio_topicos where cod_topico_pai=".$cod_topico_raiz." order by posicao_topico";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  $cont=1;
  PegaSemaforo($sock, "Portfolio");
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
          $consulta="update Portfolio_topicos set posicao_topico=".$cont." where cod_topico=".$cod_topico;
          $res=Enviar($sock, $consulta);
          $cont++;
          $posicao=-1;
        }
      }
      else
      {
        if ($posicao==$cont)
        {
          $consulta="update Portfolio_topicos set posicao_topico=".$cont." where cod_topico=".$cod_topico;
          $res=Enviar($sock, $consulta);
          $cont++;
          $posicao=-1;
        }
        $consulta="update Portfolio_topicos set posicao_topico=".$cont." where cod_topico=".$linha['cod_topico'];
        $res=Enviar($sock, $consulta);
        $cont++;
      }
    }
    if ($posicao>0)
    {
      $consulta="update Portfolio_topicos set posicao_topico=".$cont." where cod_topico=".$cod_topico;
      $res=Enviar($sock, $consulta);
      $cont++;
      $posicao=-1;
    }
  }
  LiberaSemaforo($sock, "Portfolio");
}

/* *********************************************************************
   RetornaTopicosAncestrais - Retorna array com os t?icos ancestrais do t?ico dado
   Entrada: $sock - BASE DO CURSO
            $cod_topico - Codigo do topico
   Saida: array [] - topicos (em ordem crescente de ancestralidade)
*/
function RetornaTopicosAncestrais ($sock, $cod_topico)
{
  global $lista_frases;
  $cont=0;
  do
  {
    $consulta="select * from Portfolio_topicos where cod_topico=".$cod_topico;
    $res=Enviar($sock, $consulta);
    $linha=RetornaLinha($res);
    $cod_topico=$linha['cod_topico_pai'];
    $retorno[$cont]=$linha;
    $cont++;
  }
  while ($cod_topico>4); /* S?desce at?os portf?ios */
  // 124 - Raiz
  $retorno[$cont - 1]['topico']=RetornaFraseDaLista($lista_frases, 124);

  return($retorno);
}

/* *********************************************************************
   RetornaUltimaPosicaoHistorico - Retorna a ultima ocorr?cia do historico de um item dado
   Entrada: $sock - BASE DO CURSO
            $cod_item - item
   Saida: array ['cod_item']
                ['cod_usuario']
                ['data']
                ['acao']
*/
function RetornaUltimaPosicaoHistorico ($sock, $cod_item)
{
  $consulta="select * from Portfolio_itens_historicos where cod_item=".$cod_item." order by data desc limit 1";
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}

/* *********************************************************************
   CriarTopico - Cria um novo topico filho do topico dado
   Entrada: $sock - BASE DO CURSO
            $cod_topico_raiz - topico pai
            $novo_nome - nome do novo topico
            $cod_usuario - codigo do usuario atual
            $cod_grupo - codigo do grupo do portfolio (NULL ou "" se individual
   Saida: codigo do topico criado
*/
function CriarTopico ($sock, $cod_topico_raiz, $novo_nome, $cod_usuario, $cod_grupo)
{
  if ($cod_grupo=="")
    $cod_grupo="NULL";
  $posicao=UltimaPosicaoLivre($sock, $cod_topico_raiz);

  $consulta="insert into Portfolio_topicos (cod_topico_pai, cod_usuario, cod_grupo, topico, tipo_compartilhamento, data, posicao_topico) values (".$cod_topico_raiz.", ".$cod_usuario.", ".$cod_grupo.", '".$novo_nome."', 'T', ".time().", ".$posicao.")";
  $res=Enviar($sock, $consulta);

  $consulta="select cod_topico from Portfolio_topicos where posicao_topico='".$posicao."' and cod_topico_pai='".$cod_topico_raiz."'";
  $res=Enviar($sock, $consulta);
  $res=RetornaLinha($res);
  $cod_topico=$res[0];

  return($cod_topico);
}

/* *********************************************************************
   IniciaCriacao - Inicia a cria?o de um novo item
   Entrada: $sock - BASE DO CURSO
            $cod_topico - topico pai
            $cod_usuario - usuario atual
            $cod_grupo - codigo do grupo (NULL ou "" se individual)
            $cod_curso - codigo do curso
            $diretorio_temp - nome do diret?io temporario, usado durante a edi?o
   Saida: codigo do item criado
*/
function IniciaCriacao ($sock, $cod_topico, $cod_usuario, $cod_grupo, $cod_curso, $diretorio_temp, $titulo)
{
  // vamos mexer com datas, entao chamo a funcao time() uma vez soh
  $data = time();

  if ($cod_grupo=="")
    $cod_grupo="NULL";
  $cod_item=RetornaProximoCodigo($sock, "Portfolio_itens");
  $posicao=UltimaPosicaoLivre($sock, $cod_topico);
  $consulta="insert into Portfolio_itens (cod_item, cod_topico, cod_usuario, cod_grupo, titulo, data, posicao_item, status, inicio_edicao, tipo_compartilhamento) values (".$cod_item.", ".$cod_topico.", ".$cod_usuario.", ".$cod_grupo.", '".htmlentities($titulo)."', ".$data.", ".$posicao.", 'L', ".$data.", 'T')";
  $res=Enviar($sock, $consulta);
  $consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".$data.", 'C')";
  $res=Enviar($sock, $consulta);

  if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/"))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/");
  if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/portfolio/"))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/portfolio/");
  if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/portfolio/item/"))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/portfolio/item/");

  $dir=$diretorio_temp."/tmp/".$cod_curso."/portfolio/item/".$cod_item."/";
  CriaDiretorio($dir);

  return($cod_item);
}

/* *********************************************************************
   RetornaDadosDoItem - Retorna um array com os dados do item
   Entrada: $sock - BASE DO CURSO
            $cod_topico - t?ico
   Saida: array com ['cod_item']
                    ['cod_topico']
                    ['cod_usuario']
                    ['cod_grupo']
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
  $consulta="select * from Portfolio_itens where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}

/* *********************************************************************
   RetornaResHistoricoDoItem - Retorna todas as ocorr?cias do historico de um item dado
   Entrada: $sock - BASE DO CURSO
            $cod_item - item
   Saida: array []['cod_item']
                []['cod_usuario']
                []['data']
                []['acao']
*/
function RetornaResHistoricoDoItem ($sock, $cod_item)
{
  $consulta="select * from Portfolio_itens_historicos where cod_item=".$cod_item." order by data desc";
  $res=Enviar($sock, $consulta);
  return($res);
}

/* *********************************************************************
   CopiaArquivoParaTemporario - Copia os arquivos para temporario
   Entrada: $sock - BASE DO CURSO
            $cod_curso - c?igo do curso
            $diretorio_arquivos - diret?io dos arquivos do TelEduc
            $diretorio_temp - diret?io dos arquivos do TelEduc
            $cod_item - c?igo do item
   Saida: false - se houve algum erro.
*/
function CopiaArquivoParaTemporario($sock,$cod_curso,$diretorio_arquivos,$diretorio_temp,$cod_item)
{
  $dir=$diretorio_arquivos."/".$cod_curso."/portfolio/item/".$cod_item."/";
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/portfolio/item/".$cod_item."/";
  $dirtemp2=$diretorio_temp."/tmp/".$cod_curso."/portfolio/item/";

  if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/"))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/");
  if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/portfolio/"))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/portfolio/");
  if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/portfolio/item/"))
    CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/portfolio/item/");

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
   RetornaArquivosMaterialTemp - Retorna lista de arquivos do Material no diret?io tempor?io
   Entrada: $cod_curso - codigo do curso
            $diretorio_temp - diret?io temporario de arquivos
            $cod_item - c?igo do item
   Saida: Array multidimensional com:
          $lista[<num>]['Caminho'] - caminho completo.
          $lista[<num>]['Diretorio'] - Diretorio do arquivo
          $lista[<num>]['Arquivo'] - Nome do arquivo
          $lista[<num>]['Status'] - Condi?o especial (true ou false);
          $lista[<num>]['Tamanho'] - tamanho do arquivo
          $lista[<num>]['Data'] - data da ltima modifica?o

function RetornaArquivosMaterialTemp ($cod_curso, $diretorio_temp, $cod_item)
{
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/portfolio/".$cod_item."/";
  return (RetornaArrayDiretorio($dirtemp));
}
*/

/* *********************************************************************
   RetornaArquivosMaterialTemp - Retorna lista de arquivos do Portfolio no diret?io tempor?io
   Entrada: $cod_curso - codigo do curso
            $diretorio_temp - diret?io temporario de arquivos
            $cod_item - c?igo do item ou comentario
            $pag_pai - se est?editando ou comentando
   Saida: Array multidimensional com:
          $lista[<num>]['Caminho'] - caminho completo.
          $lista[<num>]['Diretorio'] - Diretorio do arquivo
          $lista[<num>]['Arquivo'] - Nome do arquivo
          $lista[<num>]['Status'] - Condi?o especial (true ou false);
          $lista[<num>]['Tamanho'] - tamanho do arquivo
          $lista[<num>]['Data'] - data da ltima modifica?o
*/
function RetornaArquivosMaterialTemp ($cod_curso, $diretorio_temp, $cod_item, $pag_pai)
{
  if($pag_pai=="editar")
     $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/portfolio/item/".$cod_item."/";
  else if($pag_pai=="comentar")
     $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/portfolio/comentario/".$cod_item."/";
  else
  {
     exit;
  }

  return (RetornaArrayDiretorio($dirtemp));
}

/* *********************************************************************
   RetornaArquivosMaterialVer - Retorna lista de arquivos do Material
   Entrada: $diretorio_temp - diret?io temporario de arquivos
            $dirlink - link
   Saida: Array multidimensional com:
          $lista[<num>]['Caminho'] - caminho completo.
          $lista[<num>]['Diretorio'] - Diretorio do arquivo
          $lista[<num>]['Arquivo'] - Nome do arquivo
          $lista[<num>]['Status'] - Condi?o especial (true ou false);
          $lista[<num>]['Tamanho'] - tamanho do arquivo
          $lista[<num>]['Data'] - data da ltima modifica?o
*/
function RetornaArquivosMaterialVer ($cod_curso, $dirlink)
{
  return (RetornaArrayDiretorio($dirlink));
}

/* *********************************************************************
   RetornaEnderecosMaterial - Retorna um array com os enderecos associados ao item
   Entrada: $sock - BASE DO CURSO
            $cod_item - item
   Saida: array com []['cod_endereco']
                    []['cod_item']
                    []['nome']
                    []['endereco']
                    []['status']
*/
function RetornaEnderecosMaterial ($sock, $cod_item)
{
  $consulta="select * from Portfolio_itens_enderecos where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   ApagaEndereco - apaga o endereco dado
   Entrada: $sock - BASE DO CURSO
            $cod_endereco - codigo do endereco a ser apagada
   Saida: nenhuma
*/
function ApagaEndereco ($sock, $cod_endereco)
{
  $consulta="select * from Portfolio_itens_enderecos where cod_endereco=".$cod_endereco;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  if ($linha['status']=="F") /* Existia antes da edi?o */
  {
    $consulta="update Portfolio_itens_enderecos set status='A' where cod_endereco=".$cod_endereco;
  }
  else
  {
    /* S?deve ter um caso: status="I", foi inserido nesta sess? */
    $consulta="delete from Portfolio_itens_enderecos where cod_endereco=".$cod_endereco;
  }
  $res=Enviar($sock, $consulta);
}

/* *********************************************************************
   ApagaTodosEnderecos - apaga todos os enderecos do item dado
   Entrada: $sock - BASE DO CURSO
            $cod_item - codigo do item a ter endere?s apagados
   Saida: nenhuma
*/
function ApagaTodosEnderecos ($sock, $cod_item)
{
  $consulta="select * from Portfolio_itens_enderecos where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  while($linha=RetornaLinha($res))
  {
    if ($linha['status']=="F") /* Existia antes da edi?o */
    {
      $consulta="update Portfolio_itens_enderecos set status='A' where cod_endereco=".$linha['cod_endereco'];
    }
    else
    {
      /* S?deve ter um caso: status="I", foi inserido nesta sess? */
      $consulta="delete from Portfolio_itens_enderecos where cod_endereco=".$linha['cod_endereco'];
    }
    Enviar($sock, $consulta);
  }
}



/* *********************************************************************
   InsereEndereco - Insere (ou atualiza, se j?existir) um endereco ao item dado
   Entrada: $sock - BASE DO CURSO
            $nome - nome do endereco
            $endereco - Endereco do item
            $cod_item - item ao qual o endereco estar?associado
   Saida: nenhuma
*/
function InsereEndereco ($sock, $nome, $endereco, $cod_item)
{
  $consulta="select * from Portfolio_itens_enderecos where endereco='".LimpaTitulo($endereco)."' and nome='".LimpaTitulo($nome)."' and cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $num=RetornaNumLinhas($res);
  if ($num>0)
  {
    $linha=RetornaLinha($res);
    if ($linha['status']=="A")
    {
      $consulta="update Portfolio_itens_enderecos set status='F' where cod_endereco=".$linha['cod_endereco'];
      $res=Enviar($sock, $consulta);
    }
  }
  else
  {
    $consulta="insert into Portfolio_itens_enderecos (cod_item, nome, endereco, status) values (".$cod_item.", '".$nome."', '".$endereco."', 'I')";
    $res=Enviar($sock, $consulta);
  }
}

/* *********************************************************************
   AtualizaDataEdicao - Atualiza a data de edi?o do item
   Entrada: $sock - BASE DO CURSO
            $cod_item - item
   Saida: nenhuma
*/
function AtualizaDataEdicao ($sock, $cod_item)
{
  $consulta="update Portfolio_itens set inicio_edicao=".time()." where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
}

/* *********************************************************************
   AtualizaMaterial - Atualiza o T?ulo, o Texto e os arquivos
   Entrada: $sock - BASE DO CURSO
            $cod_curso - c?igo do curso
            $cod_item - c?igo do item
            $titulo - t?ulo do material
            $texto - texto a ser colocado
            $compartilhamento - tipo de compartilhamento
            $diretorio_arquivos - diret?io dos arquivos do TelEduc
            $diretorio_temp - diret?io dos arquivos do TelEduc
            $cod_usuario - codigo do usuario
   Saida: false - se houve algum erro.
*/
function AtualizaMaterial($sock,$cod_curso,$cod_item,$titulo,$texto,$compartilhamento,$diretorio_arquivos,$diretorio_temp,$cod_usuario)
{
  // Copiar arquivos da pasta temporaria para a pasta do item
  $dir=$diretorio_arquivos."/".$cod_curso."/portfolio/item/";
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/portfolio/item/".$cod_item."/";

  if (file_exists($dir))
  {
    // Removemos o diretorio principal. Mais tarde vamos colocar o temporario em seu lugar.
    RemoveDiretorio($dir.$cod_item."/");
  }

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

  // o diretorio principal jah foi apagado. Se houver arquivos no temporario, copiamo-os como um novo diretorio principal
  if (is_array(RetornaArrayDiretorio($dirtemp)))
    $ret=SobrepoeDiretorio($dirtemp, $dir);

  RemoveDiretorio($dirtemp);

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  // atualizando item do portfolio
  $consulta="update Portfolio_itens set titulo='".$titulo."', texto='".$texto."', data=".$data.", status='L' where cod_item=".$cod_item;
  Enviar($sock,$consulta);

  MudarCompartilhamento($sock, $cod_item, $compartilhamento);

  $consulta="delete from Portfolio_itens_enderecos where cod_item=".$cod_item." and status='A'";
  Enviar($sock,$consulta);
  $consulta="update Portfolio_itens_enderecos set status='F' where cod_item=".$cod_item;
  Enviar($sock,$consulta);
  $consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'F')";
  $res=Enviar($sock, $consulta);
}

/* *********************************************************************
   MudaStatusEdicao - Muda p Estado do item para Em Edi?o
   Entrada: $sock - BASE DO CURSO
            $cod_item - item
            $cod_usuario - usuario realizando a edicao
   Saida: nenhuma
*/
function MudaStatusEdicao ($sock, $cod_item, $cod_usuario)
{
  $consulta="update Portfolio_itens set status='E', cod_usuario=".$cod_usuario.", inicio_edicao=".time()." where cod_item=".$cod_item;
  $res=Enviar($sock,$consulta);
  $consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'E')";
  $res=Enviar($sock,$consulta);
}

/* *********************************************************************
   CancelaEdicao - Cancela a edicao de um item (termina os diret?ios tb).
   Entrada: $sock - BASE DO CURSO
            $cod_item - codigo do item
            $cod_usuario - usuario cancelando a edicao
            $cod_curso - codigo do curso
            $diretorio_arquivos - diretorio dos arquivos definitivos
            $diretorio_temp - diretorio temporario da edicao
   Saida: true se a edi?o foi cancelada corretamente
*/
function CancelaEdicao($sock, $cod_item, $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_associacao,$estava_associado,$manter_avaliacao)
{
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/portfolio/".$cod_item."/";
  if (file_exists($dirtemp))
  {
    if (!RemoveDiretorio($dirtemp))
      return false;
  }
  $linha=RetornaDadosDoItem($sock, $cod_item);
  if ($linha['status']=="C")
  {
    $consulta="delete from Portfolio_itens_enderecos where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);

    $consulta="delete from Portfolio_itens_historicos where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);

    $consulta="delete from Portfolio_itens where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);

    $consulta="delete from Portfolio_itens_avaliacao where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);

    return true;
  }
  if ($linha['status']=="E")
  {
    $consulta="delete from Portfolio_itens_enderecos where cod_item=".$cod_item." and status='I'";
    $res=Enviar($sock, $consulta);
    $consulta="update Portfolio_itens_enderecos set status='F' where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);

    $consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'D')";
    $res=Enviar($sock, $consulta);

    $consulta="update Portfolio_itens set status='L' where cod_item=".$cod_item;
    $res=Enviar($sock, $consulta);

    if ($criacao_associacao)
    {
      $consulta="delete from Portfolio_itens_avaliacao where cod_item=".$cod_item;
      $res=Enviar($sock, $consulta);
    }
    elseif ($estava_associado)
    {
      $consulta="delete from Portfolio_itens_avaliacao where cod_item=".$cod_item;
      $res=Enviar($sock, $consulta);

      $consulta="insert into Portfolio_itens_avaliacao values (".$cod_item.", ".$manter_avaliacao.",0,'N')";
      $res=Enviar($sock, $consulta);
    }

    return true;
  }
}

/* *********************************************************************
   InsereCodComentario - Insere o comentario na tabela e Retorna o cod_comentario
   entrada - $sock - sock de conexao
   Saida: $cod_comentario + 1
*/
function PegaUltimoCodComentario($sock, $cod_item, $cod_comentarista)
{
  $query="insert into Portfolio_itens_comentarios (cod_item,comentario,cod_comentarista,data,status) values (".$cod_item.",'', ".$cod_comentarista.",".time().",'E')";
  Enviar($sock,$query);

  $query="select MAX(cod_comentario) from Portfolio_itens_comentarios";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);

  $cod_comentario = $linha[0];

  return ($cod_comentario);
}

/* *********************************************************************
   CriaLinkVisualizar - Cria link simb?ico para dinamica e retorna o link
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_item - c?igo do item
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
   Saida: caminho relativo
*/
function CriaLinkVisualizar($sock,$cod_curso,$cod_usuario,$cod_item,$diretorio_arquivos,$diretorio_temp)
{
  /* Busca Arquivo a ser mostrado */
  $caminho = "";
  unset ($caminho);
  $dir=$diretorio_arquivos."/".$cod_curso."/portfolio/item/".$cod_item."/";

  /* Cria link simb?ico (apaga antigo, se houver)*/
  if ($cod_usuario<0)
    $cod_usuario=0;
  $dirlink=$diretorio_temp."/portfolio_".$cod_curso."_".$cod_item."_".$cod_usuario;
  $dirlinkpath="../../diretorio/portfolio_".$cod_curso."_".$cod_item."_".$cod_usuario;

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
   EncerraExibicaoArquivos - Encerra os links criados
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_item - c?igo do item
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
   Saida: True se link removido corretamente
*/
function EncerrarExibicaoArquivo($sock,$cod_curso,$cod_usuario,$cod_item,$diretorio_arquivos)
{
  /* Cria link simb?ico (apaga antigo, se houver)*/
  if ($cod_usuario<0)
    $cod_usuario=0;
  $dirlink="../../diretorio/portfolio_".$cod_curso."_".$cod_item."_".$cod_usuario;
  return (RemoveArquivo($dirlink));
}

/* *********************************************************************
   RetornaNumArquivosVisiveis - Retorna array com os arquivos visiveis ao usuario
   Entrada: $lista_arq - **** Perguntar ao Davi a logica ******
   Saida: array com lista de arquivos
*/
/*
function RetornaNumArquivosVisiveis($lista_arq)
{
  $conta_arq=0;
  $cont=0;
  $pilha_index=0;
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
  return($conta_arq++);
}
*/

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

/* *********************************************************************
   RetornaMaiorData - Retorna a maior data dentro de um t?ico ou subt?ico
   Entrada: $sock - Conex? com a base interna
            $cod_topico - codigo do topico a ser checado
            $tipo_comp - tipo do compartilhamento (F ou T)
            $data - Data do t?ico
   Saida: maior data
*/
function RetornaMaiorData($sock,$cod_topico,$tipo_comp,$data)
{
  $query="select cod_topico,data from Portfolio_topicos where cod_topico_pai=".$cod_topico;
  if ($tipo_comp=="T")
    $query.=" and tipo_compartilhamento='T'";
  if ($tipo_comp=="F")
    $query.=" and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";

  $res=Enviar($sock,$query);
  $lista_topicos=RetornaArrayLinhas($res);

  /* Verifica se existem subt?icos */
  if (count($lista_topicos)>0)
  {
    foreach($lista_topicos as $cod => $linha)
    {
      $data_tmp=RetornaMaiorData($sock,$linha['cod_topico'],$tipo_comp,$linha['data']);
      if ($data_tmp>$data)
        $data=$data_tmp;
    }
  }

  $query="select max(data) from Portfolio_itens where cod_topico=".$cod_topico;
  if ($tipo_comp=="T")
    $query.=" and tipo_compartilhamento='T'";
  if ($tipo_comp=="F")
    $query.=" and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha[0]>$data)
    $data=$linha[0];
  return $data;
}

/* *********************************************************************
   VerificaAnexo - verifica se nome do anexo aprenseta acento ou chars ruins.
   Entrada: $string - com o nome do anexo a ser verificado
   Saida: 1 - nome do anexo est ok!
   		  0 - nome do anexo apresenta acento ou chars ruins.
*/
function VerificaAnexo($str_anexo)
{
	$string = utf8_decode($str_anexo);
	// Palavras acentuadas
	$bad_chars = "ÁáÉéÍíÓóÚú";
	$bad_chars.= "ÃãÕõ";
	$bad_chars.= "ÀàÈèÌìÒòÙù";
	$bad_chars.= "ÄäËëÏïÖöÜü";
	$bad_chars.= "ÂâÊêÎîÔôÛû";

	// Caracteres estranhos para nome de um arquivo anexo
	$bad_chars.= "÷`!@#$%*()+={}[]\\\"';:?/,<>";
	$str_res = strpbrk($string, $bad_chars);

	// Se apresentar algum char de $bad_chars, str_res != ""
	if (($str_res == "") || ($str_res == false))
		return 1;
	else
		return 0;
}

/* *********************************************************************
   VerificaPortfolioGrupos - Verifica se existem portfolios para todos os grupos aos quais
                             o usuario logado pertence
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo do usuario logado
   Saida: nenhuma
*/
function VerificaPortfolioGrupos ($sock, $cod_usuario)
{
  // lista dos grupos do usuario (excluindo grupos apagados: grupos apagados somente aparecem se foi colocado algo neles qdo estavam ativos)
  $query = "select GU.cod_grupo from Grupos_usuario GU, Grupos G where GU.cod_grupo=G.cod_grupo and GU.cod_usuario=".$cod_usuario." and G.status <> 'X' order by cod_grupo";
  $res   = Enviar ($sock, $query);
  $lista = RetornaArrayLinhas ($res);

  if (is_array ($lista))
    foreach ($lista as $c => $linha)
    {
      $lista_grupos [ $linha['cod_grupo'] ] = true;
    }

  // lista dos portfolios de grupo
  $query = "select cod_topico, cod_grupo from Portfolio_topicos where cod_grupo IS NOT NULL order by cod_grupo";
  $res   = Enviar ($sock, $query);
  $lista2= RetornaArrayLinhas ($res);

  if (is_array ($lista2))
    foreach ($lista2 as $c => $linha)
    {
      $lista_portfolios_grupos [ $linha['cod_grupo'] ] = true;
    }

  // fazendo o match: pegamos os grupos aos quais o usuario pertence e que n? tem portfolio na lista
  if (is_array ($lista_grupos))
    foreach ($lista_grupos as $cod_grupo => $c)
    {
      if (!isset ($lista_portfolios_grupos [ $cod_grupo ]))
      {
      	// Nao existe um portfolio para este grupo. Criamos
        $rep="#G".$cod_grupo;
        $query="insert into Portfolio_topicos (cod_topico_pai,cod_usuario,cod_grupo,tipo_compartilhamento,data,posicao_topico,topico) values (4,".$cod_usuario.",".$cod_grupo.",'P',".time().",".UltimaPosicaoLivre($sock, 4).",'".$rep."')";
        Enviar($sock,$query);
      }
    }
}

/* *********************************************************************
   MontaMenu - Monta o menu superior do portfolio
   Entrada:
            $cod_curso        - codigo do curso
            $acao_portfolio_s - se usuario esta em port individuais, = 'I'
                                se esta em port de grupos, = 'G'
   Saida: Exibe na tela o menu
*/
function MontaMenu($cod_curso, $acao_portfolio_s)
{
  global $lista_frases;
  global $ferramenta_grupos_s;

  //if ($ferramenta_grupos_s)     // se a ferramenta grupos estiver ativa, exibimos menus para portfolios individuais e de grupos
  //{
    // ajustando cores dos links
    if ($acao_portfolio_s == 'I')
    {
      $css_ind = "menuativo";
      $css_grp = "menu";
      $css_myp = "menu"; //modificacoes para menu meus portfolios
      $css_enc = "menu"; // Modifica?es para menu de Portf?ios encerrados
    }
    else if ($acao_portfolio_s == 'G')
    {
      $css_ind = "menu";
      $css_grp = "menuativo";
      $css_myp = "menu"; //modificacoes para menu meus portfolios
      $css_enc = "menu"; // Modifica?es para menu de Portf?ios encerrados
    }
    else if ($acao_portfolio_s == 'M')
    {
       $css_ind = "menu";
       $css_grp = "menu";
       $css_myp = "menuativo"; //modificacoes para menu meus portfolios
       $css_enc = "menu"; // Modifica?es para menu de Portf?ios encerrados 
    }
    else
    {
       $css_ind = "menu";
       $css_grp = "menu";
       $css_myp = "menu"; //modificacoes para menu meus portfolios
       $css_enc = "menuativo"; // Modifica?es para menu de Portf?ios encerrados
    } 

    echo("<table width=100% border=0 cellspacing=2 cellpadding=0><tr align=middle>\n");
   //174 - Meus portfolios 
    echo("<td class=menu><a class=".$css_myp." href=ver_portfolios.php?cod_curso=".$cod_curso."&exibir=myp>".RetornaFraseDaLista($lista_frases,174)."</a></td>\n");
    // 74 - Portfolios Individuais
    echo("<td class=menu><a class=".$css_ind." href=ver_portfolios.php?cod_curso=".$cod_curso."&exibir=ind>".RetornaFraseDaLista($lista_frases,74)."</a></td>\n");
      // 75 - Portfolios de Grupos
    if ($ferramenta_grupos_s) {
      echo("<td class=menu><a class=".$css_grp." href=ver_portfolios.php?cod_curso=".$cod_curso."&exibir=grp>".RetornaFraseDaLista($lista_frases,75)."</a></td>\n");
      // 177 - Portfolios encerrados
      echo("<td class=menu><a class=".$css_enc." href=ver_portfolios.php?cod_curso=".$cod_curso."&exibir=enc>".RetornaFraseDaLista($lista_frases,177)."</a></td>\n");
    }      
    echo("</tr></table>\n");
  //}
  /*
  else    // se a ferramenta grupos n? estiver ativa, nao existe distincao entre portfolios de grupos ou individuais
  {  
    echo("<table width=100% border=0 cellspacing=2 cellpadding=0><tr align=middle>\n");
    // 127 - Outros portfolios
    echo("<td class=menu><a class=menu href=ver_portfolios.php?".RetornaSessionID()."&cod_curso=".$cod_curso.">".RetornaFraseDaLista($lista_frases,127)."</a></td>\n");
    echo("</tr></table>\n");
  }*/
}

/* *********************************************************************
   RetornaListaIntegrantes - Retorna a lista de integrantes de um grupo
   Entrada: $sock - BASE DO CURSO
            $cod_grupo- codigo do grupo
   Saida: array com [$cod_usuario]['nome']
                                  ['tipo_usuario']
*/
function RetornaListaIntegrantes($sock,$cod_grupo, $cod_curso)
{
  $query="select GU.cod_usuario 'cod_usuario', U.nome 'nome', UC.tipo_usuario 'tipo_usuario'
          from Grupos_usuario GU, ".$dbnamebase.".Usuario_curso UC
          inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
          where GU.cod_grupo='".$cod_grupo."'
          and GU.cod_usuario=U.cod_usuario
          and (binary UC.tipo_usuario='A' or binary UC.tipo_usuario='F')
          and UC.cod_curso='".$cod_curso."'
          order by UC.tipo_usuario desc, U.nome";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  if (count($lista)>0)
    foreach($lista as $cod => $linha)
    {
      $saida[$linha['cod_usuario']]['nome']=$linha['nome'];
      $saida[$linha['cod_usuario']]['tipo_usuario']=$linha['tipo_usuario'];
    }
  return $saida;
}

/* *********************************************************************
   RetornaItensDaLixeira - Retorna array com os itens da lixeira
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo do usuario logado
            $cod_usuario_portfolio - codigo do usuario dono do portfolio
            $cod_grupo_portfolio - codigo do grupo dono do portfolio (NULL ou "" se individual
   Saida: array com []['cod_item']
                    []['cod_topico']
                    []['cod_usuario']
                    []['cod_grupo']
                    []['titulo']
                    []['texto']
                    []['tipo_compartilhamento']
                    []['data']
                    []['data_ativo']
                    []['data_inativo']
                    []['posicao_item']
                    []['status']
                    []['inicio_edicao']
*/
function RetornaItensDaLixeira ($sock, $cod_usuario,$cod_usuario_portfolio,$cod_grupo_portfolio)
{
  $saida=0;
  if ($cod_usuario!=$cod_usuario_portfolio)
  {
    if (!PertenceAoGrupo($sock,$cod_usuario, $cod_grupo_portfolio))
      return $saida;
    else
      $regra="(cod_grupo=".$cod_grupo_portfolio.")";
  }
  else
  {
    if (PertenceAoGrupo($sock,$cod_usuario, $cod_grupo_portfolio))
      $regra="(cod_grupo=".$cod_grupo_portfolio.")";
    else
      $regra="(cod_grupo IS NULL and cod_usuario=".$cod_usuario_portfolio.")";
  }
  $consulta="select * from Portfolio_itens where cod_topico=2 and ".$regra." order by posicao_item";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   InsereComentario - Insere o coment?io no item passado
   Entrada: $sock - BASE DO CURSO
            $cod_item - codigo do item
            $comentario - comentario a ser acrescido
            $cod_comentarista - Pessoa fazendo o comentario
   Saida: Nenhuma
*/
function InsereComentario ($sock, $cod_comentario, $comentario)
{
  $comentario=LimpaConteudo($comentario);
  $query="update Portfolio_itens_comentarios set comentario='".$comentario."', data=".time().", status='A' where cod_comentario='".$cod_comentario."'";
// (cod_item,comentario,cod_comentarista,data,status) values (".$cod_item.",'".$comentario."',".$cod_comentarista.",".time().",'A')";
  Enviar($sock,$query);
}

/* *********************************************************************
   RetornaComentariosDoItem - Retorna um array com os comentarios associados ao item
   Entrada: $sock - BASE DO CURSO
            $cod_item - item
   Saida: array com []['cod_comentario']
                    []['cod_item']
                    []['comentario']
                    []['cod_comentarista']
                    []['data']
                    []['status']
*/
function RetornaComentariosDoItem ($sock, $cod_item)
{
  $consulta="select * from Portfolio_itens_comentarios where cod_item=".$cod_item." and status='A' order by data desc";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   RetornaComentario - Retorna um array com os comentarios associados ao item
   Entrada: $sock - BASE DO CURSO
            $cod_comentario - comentario
   Saida: array com ['cod_comentario']
                    ['cod_item']
                    ['comentario']
                    ['cod_comentarista']
                    ['data']
                    ['status']
*/
function RetornaComentario ($sock, $cod_comentario)
{
  $consulta="select * from Portfolio_itens_comentarios where cod_comentario=".$cod_comentario;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}

/* *********************************************************************
   ApagaComentario - Apaga o comentario Dado
   Entrada: $sock - BASE DO CURSO
            $cod_comentario - comentario
   Saida: nenhuma
*/
function ApagaComentario ($sock, $cod_comentario)
{
  $consulta="update Portfolio_itens_comentarios set status='X' where cod_comentario=".$cod_comentario;
  Enviar($sock, $consulta);
}

/* *********************************************************************
   RetornaNumComentariosTopico - Retorna a maior data dentro de um t?ico ou subt?ico
   Entrada: $sock - Conex? com a base interna
            $cod_usuario - usuario
            $cod_topico - codigo do topico a ser checado
            $tipo_comp - tipo do compartilhamento (F ou T)
            $data - Data do t?ico
   Saida: array com ['num_comentarios']
                    ['num_comentarios_alunos'] - numero de coment?ios feitos por alunos que n? o usuario
                    ['num_comentarios_formadores'] - numero de coment?ios feitos por formadores que n? o usuario
                    ['num_comentarios_usuario'] - numero de coment?ios feitos pelo usuario
                    ['data_comentarios'] - Maior data de um coment?io
*/
function RetornaNumComentariosTopico($sock,$cod_usuario,$cod_topico,$tipo_comp,$data, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $num_f=0;
  $num_a=0;
  $num_u=0;
  $data=0;

  $query="select cod_topico,data from Portfolio_topicos where cod_topico_pai=".$cod_topico;
  if ($tipo_comp=="T")
    $query.=" and tipo_compartilhamento='T'";
  if ($tipo_comp=="F")
    $query.=" and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";

  $res=Enviar($sock,$query);
  $lista_topicos=RetornaArrayLinhas($res);

  /* Verifica se existem subt?icos */
  if (count($lista_topicos)>0)
  {
    foreach($lista_topicos as $cod=>$linha)
    {
      $num_tmp=RetornaNumComentariosTopico($sock,$cod_usuario,$linha['cod_topico'],$tipo_comp,$linha['data'], $cod_curso);
      $num_f += $num_tmp['num_comentarios_formadores'];
      $num_a += $num_tmp['num_comentarios_alunos'];
      $num_u += $num_tmp['num_comentarios_usuario'];
      if ($data < $num_tmp['data_comentarios'])
        $data = $num_tmp['data_comentarios'];
    }
  }

  /* checa o nivel atual */

  $query="select Pic.cod_comentarista 'cod', UC.tipo_usuario 'tu', count(*) 'ct', max(Pic.data) 'dt'
          from Portfolio_itens_comentarios Pic, Portfolio_itens Pi, ".$dbnamebase.".Usuario_curso UC
          where Pic.cod_comentarista=UC.cod_usuario
          and Pic.cod_item=Pi.cod_item
          and Pi.cod_topico=".$cod_topico."
          and Pic.status='A'
          and UC.cod_curso='".$cod_curso."'";

  if ($tipo_comp=="T")
    $query.=" and Pi.tipo_compartilhamento='T'";
  if ($tipo_comp=="F")
    $query.=" and (Pi.tipo_compartilhamento='T' or Pi.tipo_compartilhamento='F')";
  $query.=" group by Pic.cod_comentarista order by Pic.cod_comentarista";

  $res=Enviar($sock,$query);

  while ($linha=RetornaLinha($res))
  {
    if ($linha['cod']==$cod_usuario)
      $num_u+=$linha['ct'];
    else if ($linha['tu']=="F")
      $num_f+=$linha['ct'];
    else
      $num_a+=$linha['ct'];
    if ($data<$linha['dt'])
      $data=$linha['dt'];
  }
  $saida['num_comentarios']=$num_f+$num_a+$num_u;
  $saida['num_comentarios_formadores']=$num_f;
  $saida['num_comentarios_alunos']=$num_a;
  $saida['num_comentarios_usuario']=$num_u;
  $saida['data_comentarios']=$data;
  return $saida;
}

/* *********************************************************************
   RetornaMaiorDataItemComentario - Retorna a maior data de item ou coment?io
                                    dentro de um t?ico ou subt?ico
   Entrada: $sock - Conex? com a base interna
            $cod_topico - codigo do topico a ser checado
            $tipo_comp - tipo do compartilhamento (F ou T)
            $data - Data do t?ico
            $cod_usuario - usuario logado
   Saida: maior data
*/
function RetornaMaiorDataItemComentario($sock,$cod_topico,$tipo_comp,$data,$cod_usuario)
{
  $query="select cod_usuario,cod_grupo from Portfolio_topicos where cod_topico=".$cod_topico;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $cod_usuario_portfolio=$linha['cod_usuario'];
  $cod_grupo_portfolio=$linha['cod_grupo'];

  if ($cod_grupo_portfolio=="" && $cod_usuario==$cod_usuario_portfolio)
    $tipo_comp="P";
  else
    if (PertenceAoGrupo($sock,$cod_usuario,$cod_grupo_portfolio))
      $tipo_comp="P";

  $query="select cod_topico,data from Portfolio_topicos where cod_topico_pai=".$cod_topico;
  if ($tipo_comp=="T")
    $query.=" and tipo_compartilhamento='T'";
  if ($tipo_comp=="F")
    $query.=" and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";

  $res=Enviar($sock,$query);
  $lista_topicos=RetornaArrayLinhas($res);

  /* Verifica se existem subt?icos */
  if (count($lista_topicos)>0)
  {
    foreach($lista_topicos as $cod=>$linha)
    {
      $data_tmp=RetornaMaiorDataItemComentario($sock,$linha['cod_topico'],$tipo_comp,$linha['data'],$cod_usuario);
      if ($data_tmp>$data)
        $data=$data_tmp;
    }
  }

  $query="select max(Pi.data) 'PiData', max(Pic.data) 'PicData' from Portfolio_itens Pi, Portfolio_itens_comentarios Pic where Pi.cod_topico=".$cod_topico." and Pi.cod_item=Pic.cod_item";
  if ($tipo_comp=="T")
    $query.=" and Pi.tipo_compartilhamento='T'";
  if ($tipo_comp=="F")
    $query.=" and (Pi.tipo_compartilhamento='T' or Pi.tipo_compartilhamento='F')";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha['PiData']>$data)
    $data=$linha['PiData'];
  if ($linha['PicData']>$data)
    $data=$linha['PicData'];


  $query="select max(Pi.data) 'PiData' from Portfolio_itens Pi where Pi.cod_topico=".$cod_topico;
  if ($tipo_comp=="T")
    $query.=" and Pi.tipo_compartilhamento='T'";
  if ($tipo_comp=="F")
    $query.=" and (Pi.tipo_compartilhamento='T' or Pi.tipo_compartilhamento='F')";
  $res=Enviar($sock,$query);

  $linha=RetornaLinha($res);
  if ($linha['PiData']>$data)
    $data=$linha['PiData'];

  return $data;
}

/* *********************************************************************
   ArrumaPosicoes - rearranja as posicoes dos itens/topicos em um topico qdo um item/topico é movido
   entrada: $sock - sock de conexao
            $cod_topico - codigo do topico no qual arrumar as posicoes dos itens
   saida: nenhuma
*/
function ArrumaPosicoes($sock, $cod_topico)
{
  PegaSemaforo($sock, "Portfolio");

  $query="select * from Portfolio_itens where cod_topico=".$cod_topico." order by posicao_item asc";
  $res = Enviar($sock, $query);
  $lista_itens_ord=RetornaArrayLinhas($res);

  $query="select * from Portfolio_topicos where cod_topico_pai=".$cod_topico." order by posicao_topico asc";
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

  foreach($lista_unificada as $cod => $linha){
    //é item
    if(isset($linha['posicao_item'])){
      $update = "update Portfolio_itens set posicao_item=".($cod+1)." where cod_item = ".$linha['cod_item'];
      Enviar($sock, $update);
    }
    //é tópico
    else if(isset($linha['posicao_topico'])) {
      $update = "update Portfolio_topicos set posicao_topico=".($cod+1)." where cod_topico = ".$linha['cod_topico'];
      Enviar($sock, $update);
    }

  }

  LiberaSemaforo($sock, "Portfolio");
}

/* *********************************************************************
   TituloPagina - Retorna string com o subtitulo da pagina: portfolio individual ou de grupo0
   entrada:   $acao_portfolio_s - Indica se portfolio individual ou de grupo
   saida:     string com o subtitulo da pagina
*/
function TituloPagina ($acao_portfolio_s)
{
  global $lista_frases;

  if ($acao_portfolio_s == 'I')
    // 74 - Portfolios individuais
    return RetornaFraseDaLista ($lista_frases, 74);
  else if ($acao_portfolio_s == 'G')
    // 75 - Portfolios de grupos
    return RetornaFraseDaLista ($lista_frases, 75);
  else
   return "<big><font color=red>Funcao Titulo Pagina: Erro</font></big>";
}

/* *********************************************************************
   RetornaNumItensTopicoRec - Retorna numero de itens dentro de um topico e mergulhando
   entrada: $sock           - sock de conexao com a base
            $cod_topico     - codigo do topico a mergulhar contando itens
            $dono_portfolio - boolean, se o usuario logade eh dono do portfolio
            $eformador      - boolean, se o cara visitando eh formador
   saida:   numero de itens dentro do topico
   OBS:     A funcao calcula a regra de compartilhamento e chama sua auxiliar recursiva,
            para nao ter de calcular esta regra a cada chamada.
*/
function RetornaNumItensTopicoRec ($sock, $cod_topico, $dono_portfolio, $eformador)
{
  // Quando se fala de numero de itens, esta se falando de numero de itens visiveis ao usuario logado
  if (! $dono_portfolio)
  {
    if ($eformador)
      $regra_compartilhamento = " and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra_compartilhamento = " and tipo_compartilhamento='T'";
  }
  else // - se for dono do portfolio, ignorar regras de compartilhamento, mostrar tudo
  {
    $regra_compartilhamento = "";
  }

  return RetornaNumItensTopicoRecAux ($sock, $cod_topico, $regra_compartilhamento);
}

/* *********************************************************************
   RetornaNumItensTopicoRecAux - auxiliar, retorna numero de itens
       dentro de um topico, e mergulhando nos niveis
   entrada: $sock           - conexao com a base de dados
            $cod_topico     - codigo do topico inicial
   saida:   numero de itens no topico pedido
*/
function RetornaNumItensTopicoRecAux($sock, $cod_topico, $regra_compartilhamento)
{
  // Numero de itens dentro de um topico eh o numero de itens dentro daquele topico mais o numero de itens em cada topico filho deste

  // Numero de itens dentro do topico pai
  $query = "select count(cod_item) 'num_itens' from Portfolio_itens where cod_topico = ".$cod_topico.$regra_compartilhamento;
  $res   = Enviar ($sock, $query);
  $linha = RetornaLinha ($res);
  $num_itens = $linha ['num_itens'];

  // Numero de itens dentro de cada topico filho
  $query = "select cod_topico from Portfolio_topicos where cod_topico_pai = ".$cod_topico;
  $res   = Enviar ($sock, $query);
  $lista = RetornaArrayLinhas ($res);
  if (is_array ($lista))
    foreach ($lista as $c => $linha)
    {
      $num_itens += RetornaNumItensTopicoRecAux ($sock, $linha ['cod_topico'], $regra_compartilhamento);
    }

  return $num_itens;
}

/* *********************************************************************
   RetornaStatusPortfolio - retorna informacoes sobre o portfolio e como
                            o usuario interage com ele
   entrada:   $sock                     - sock de conexao
              $cod_curso                - codigo do curso
              $cod_usuario              - codigo do usuario logado
              $cod_usuario_portfolio    - codigo do usuario dono do portfolio
              $cod_grupo                - codigo do grupo dono do portfolio

   saida:     array com 3 booleanos:
              ['portfolio_grupo']   - se o portfolio eh de grupo
              ['portfolio_apagado'] - se o portfolio estah apagado (grupo apagadoou aluno rejeitado)
              ['dono_portfolio']    - se o usuario tem privilegios de dono do portfolio
   obs:
   - se o portfolio eh apagado, ninguem tem os privilegios de dono
   - se for um formador com visao de aluno, ele tb nao tem estes priviliegios
   - em cursos terminados, ninguem tem privilegio de dono, apenas formadores para seus portfolio
*/
function RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo)
{
  $retorno = "";
  unset ($retorno);
  $portfolio_grupo = ($cod_grupo !="" && $cod_grupo != "NULL");

  if ($portfolio_grupo)
  {
    $portfolio_apagado = ( StatusGrupo ($sock, $cod_grupo) == 'X' );
    $dono_portfolio    = PertenceAoGrupo($sock, $cod_usuario, $cod_grupo);
  }
  else
  {
    $portfolio_apagado = ( RetornaStatusUsuario($sock, $cod_curso, $cod_usuario_portfolio) == 'r' );
    $dono_portfolio    = ( $cod_usuario == $cod_usuario_portfolio );
  }

  $dono_portfolio &= ( !$portfolio_apagado && !EFormadorComVisaoDeAluno($sock, $cod_curso, $cod_usuario) );

  /* Verifica o t?mino do curso */
  $status_curso=RetornaStatusCurso($sock,$cod_curso);

  if ($status_curso=='E' && !EFormador($sock, $cod_curso, $cod_usuario) )
    $dono_portfolio=false;

  $retorno ['portfolio_grupo']   = $portfolio_grupo;
  $retorno ['portfolio_apagado'] = $portfolio_apagado;
  $retorno ['dono_portfolio']    = $dono_portfolio;

  return $retorno;
}

/* *********************************************************************
   TituloComDistincao - retorna subtitulo da pagina, fazendo distincao entre
                        portfolios individuais ou de grupos
   entrada:  $acao_portfolio_s    - indica se 'I' - portfolios individuais
                                              'G' - portfolios de grupos
             $ferramenta_grupos_s - bool, indica se ferramenta grupos esta ativada
                                    se nao estiver, nao faz a distincao
             $frase           - frase do subtitulo
   saida  :  frase para o subtitulo da pagina
*/
function TituloComDistincao ($acao_portfolio_s, $ferramenta_grupos_s, $frase)
{
  global $lista_frases;

  if (! $ferramenta_grupos_s)
  {
    $retorno = $frase;
  }
  else
  {
    if ($acao_portfolio_s == 'I')
    {
      // 2 - Portf?ios Individuais
      $retorno = RetornaFraseDaLista ($lista_frases, 2)." / ".$frase;
    }
    else
    {
      // 3 - Portfolios de grupo
      $retorno = RetornaFraseDaLista ($lista_frases, 3)." / ".$frase;
    }
  }
  return $retorno;
}

/* *********************************************************************
   StatusFerramentaGrupos - retorna se a ferramenta Grupos esta ativada

   PROVISORIO !!! - O LUGAR DESTA FUNCAO NAO EH AQUI

   entrada: nenhuma
   saida  : true  - ferramenta grupos estah ativada
            false - ferramenta grupos nao estah ativa
*/
function StatusFerramentaGrupos ($sock)
{
  $query = "select status from Curso_ferramentas where cod_ferramenta=12";
  $res   = Enviar ($sock, $query);
  $linha = RetornaLinha ($res);
  return ( $linha ['status'] != 'D' );
}

/* *********************************************************************
   Alertar - funcao para debugar, faz um alert JavaScript
   entrada: $string - string a alertar na tela
   saida:   nenhuma
*/
function Alertar ($string)
{
  echo ("<script language=JavaScript>\n");
  echo ("  alert (".$string.");");
  echo ("</script>\n");
}

/* *********************************************************************
   RemoveComentario - Remove o coment?io da tabela e apaga os arquivos referentes ao comentario (se houver)
   Entrada: $sock - BASE DO CURSO
            $cod_comentario - codigo do comentario na tabela
            $cod_comentario - codigo do comentario na tabela
            $diretorio_temp - diretorio temporario
            $cod_curso      - codigo do curso na tabela
   Saida: Nenhuma
*/
function RemoveComentario($sock, $cod_comentario, $diretorio_temp, $cod_curso)
{
  /*deleta arquivos temporario se houver*/
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/portfolio/comentario/".$cod_comentario."/";
  $array_temp=RetornaArrayDiretorio($diretorio_temp);
  if(count($array_temp)!=0)
     RemoveDiretorio($dirtemp);
     /*Apagar cod_comentario da tabela*/
  $query="delete from Portfolio_itens_comentarios where cod_comentario=".$cod_comentario;
  Enviar($sock,$query);
}

/* *********************************************************************
   print_array - funcao para debugar, imprime o array
   entrada: $array - array do qual imprimir conteudo
            $level - nivel de aninhamento de array dentro de array.
                     Primeira chamada sempre 0 (zero);
   saida:   nenhuma
*/
function print_array($array)
{
  print_array_aux($array,0);
}

function print_array_aux($array,$level)
{
  if (!is_array($array))
    return 0;

  reset($array);
  while ( list($key, $value) = each($array) )
  {
    for ($i = 0; $i < $level; ++$i)
    {
      echo "&nbsp;&nbsp;&nbsp;";
    }
    echo "['".$key."'] ".$value."<br>";
    if (is_array($value))
      print_array_aux($value,$level+1);
  }
}


/* *********************************************************************
   RetornaItensPortfolio - Retorna os itens de um portf?io
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo da pasta base
            $portfolios - 'P' - Todos os itens, 'F' - Itens tipo F e T, 'T' - S?tipo T
   Saida: numero de itens
*/
function RetornaItensPortfolio($sock, $cod_topico,$portfolios)
{
  $total=0;
  if ($portfolios=='T')
    $topico="tipo_compartilhamento='T' and ";
  else if ($portfolios=='F')
    $topico="tipo_compartilhamento<>'P' and ";
  else
    $topico="";

  $consulta="select cod_item from Portfolio_itens where ".$topico." cod_topico=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return ($lista);
}

/* *********************************************************************
   CriaLinkVisualizarComentar - Cria link simb?ico para arquivos no comentario do portfolio e retorna o link
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_comentario - c?igo do comentario
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
   Saida: caminho relativo
*/
function CriaLinkVisualizarComentar($sock,$cod_curso,$cod_usuario,$cod_comentario,$diretorio_arquivos,$diretorio_temp)
{
  /* Busca Arquivo a ser mostrado */
  $caminho = "";
  unset ($caminho);
  $dir=$diretorio_arquivos."/".$cod_curso."/portfolio/comentario/".$cod_comentario."/";

  /* Cria link simb?ico (apaga antigo, se houver)*/
  if ($cod_usuario<0)
    $cod_usuario=0;
  $dirlink=$diretorio_temp."/portfolio_".$cod_curso."_comentario_".$cod_comentario."_".$cod_usuario;
  $dirlinkpath="../../diretorio/portfolio_".$cod_curso."_comentario_".$cod_comentario."_".$cod_usuario;

  if (ExisteArquivo($dirlink))
    RemoveArquivo($dirlink);

  CriaLinkSimbolico($dir,$dirlink);

  $retorno['diretorio']=$dirlink."/";
  $retorno['link']=$dirlinkpath."/";

  return ($retorno);
}

/* *********************************************************************
   RetornaAntProxComentario - retorna codigos dos comentarios anterior e seguinte a um comentario
   entrada - $sock - sock de conexao
             $cod_item - codigo do item comentado
             $cod_comentario - codigo do comentario

   saida   - array ['ant']  = codigo do comentario anterior
                   ['prox'] = codigo do proximo comentario
*/
function RetornaAntProxComentario($sock, $cod_item, $cod_comentario)
{
  // retorna o codigo do comentario anterior
  $query = "select T2.cod_comentario
            from Portfolio_itens_comentarios T1, Portfolio_itens_comentarios T2
            where T1.cod_item=".$cod_item."
              and T1.cod_comentario=".$cod_comentario."
              and T2.cod_item=T1.cod_item
              and T2.data < T1.data
              and T2.status <> 'X'
	      and T2.status <> 'E'
            order by T2.data desc
            limit 1";
  $res   = Enviar ($sock, $query);
  if ($linha = RetornaLinha($res))
    $retorno ['ant'] = $linha['cod_comentario'];
  else
    $retorno ['ant'] = -1;

  // retorna o codigo do proximo comentario
  $query = "select  T2.cod_comentario
            from Portfolio_itens_comentarios T1, Portfolio_itens_comentarios T2
            where T1.cod_item=".$cod_item."
              and T1.cod_comentario=".$cod_comentario."
              and T2.cod_item=T1.cod_item
              and T2.data > T1.data
              and T2.status <> 'X'
	      and T2.status <> 'E'
            limit 1";
  $res   = Enviar ($sock, $query);
  if ($linha = RetornaLinha ($res))
    $retorno ['prox'] = $linha ['cod_comentario'];
  else
    $retorno ['prox'] = -1;

  return $retorno;
}

/* *********************************************************************
   InsereComentario2 - Insere o coment?io na posicao do cod_comentario
   Entrada: $sock - BASE DO CURSO
            $comentario - comentario a ser acrescido
            $cod_comentario - codigo do comentario na tabela
   Saida: Nenhuma
*/
function InsereComentario2($sock,  $comentario, $cod_comentario)
{
  $comentario=LimpaConteudo($comentario);
  $query="update Portfolio_itens_comentarios set status='A', comentario='".$comentario."' ,data=".time()." where cod_comentario=".$cod_comentario;
  Enviar($sock,$query);
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
  $objResponse = new xajaxResponse();

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $sock=Conectar($cod_curso);
  $consulta="update Portfolio_itens set titulo='".htmlentities($novo_nome)."', data=".$data.", status='L' where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  // Imprime no div valores do formul?io
  $objResponse->assign("tr_".$cod_item, "className", "novoitem");
  $objResponse->assign("tit_".$cod_item, "innerHTML", htmlentities($novo_nome));
  $objResponse->addEvent("renomear_".$cod_item, "onclick", "AlteraTitulo('".$cod_item."');");

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->call("mostraFeedback", $texto, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
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
  $objResponse = new xajaxResponse();

  $novo_nome=ConverteAspas2BarraAspas($novo_nome);
  $sock=Conectar($cod_curso);

  $consulta="update Portfolio_itens set texto='".trim($novo_nome)."' where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  // Imprime no div valores do formulário
  $objResponse->assign("tr_".$cod_item, "className", "novoitem");
  $objResponse->assign("text_".$cod_item, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas($novo_nome)), true));

  $objResponse->call("mostraFeedback", $texto, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* *********************************************************************
   InsereEnderecoDinamic - Insere (ou atualiza, se já existir) um endereco ao item dado, dinâmicamente
   Entrada: $nome - nome do endereco
            $endereco - Endereco do item
            $cod_item - item ao qual o endereco estará associado
            $cod_curso - código do curso, para conectar ao banco de dados
            $cod_usuario - código do usuário que está utilizando
   Saida: XML da função Ajax
*/
function InsereEnderecoDinamic ($nome, $endereco, $cod_item, $cod_curso, $cod_usuario, $texto)
{

  $objResponse = new xajaxResponse();

  $sock=Conectar($cod_curso);

  $consulta="select * from Portfolio_itens_enderecos where endereco='".LimpaTitulo($endereco)."' and nome='".LimpaTitulo($nome)."' and cod_item=".$cod_item;

  $res=Enviar($sock, $consulta);

  $linha=RetornaLinha($res);
  if ($linha[0]!='') return $objResponse;

  $num=RetornaNumLinhas($res);
  if ($num>0)
  {
    $linha=RetornaLinha($res);
    if ($linha['status']=="A")
    {
      $consulta="update Portfolio_itens_enderecos set status='F' where cod_endereco=".$linha['cod_endereco'];
      $res=Enviar($sock, $consulta);
    }
  }
  else
  {
    $consulta="insert into Portfolio_itens_enderecos (cod_item, nome, endereco, status) values (".$cod_item.", '".$nome."', '".$endereco."', 'F')";
    $res=Enviar($sock, $consulta);
  }

  $consulta="select * from Portfolio_itens_enderecos where endereco='".LimpaTitulo($endereco)."' and nome='".LimpaTitulo($nome)."' and cod_item=".$cod_item;

  $res=Enviar($sock, $consulta);

  $linha=RetornaLinha($res);
  $cod_endereco=$linha['cod_endereco'];

  $objResponse->create("listaEnderecos", "span", "end_".$cod_endereco);
  $objResponse->create("end_".$cod_endereco, "span", "link_".$cod_endereco);
  $objResponse->assign("link_".$cod_endereco, "className", "link");

  if ($num==0){
    if ($nome!=''){
      $objResponse->assign("link_".$cod_endereco, "innerHTML", $nome);
      $objResponse->insertAfter("link_".$cod_endereco, "span", "endEndereco_".$cod_endereco);
      $objResponse->assign("endEndereco_".$cod_endereco, "innerHTML", "&nbsp;&nbsp;(".RetornaURLValida($endereco).") - \n");
    }else{
      $objResponse->assign("link_".$cod_endereco, "innerHTML", RetornaURLValida($endereco)." - ");
    }
  }

  $objResponse->addEvent("link_".$cod_endereco, "onClick", "WindowOpenVerURL('".ConverteSpace2Mais(RetornaURLValida($endereco))."');return(false);");

  $objResponse->create("end_".$cod_endereco, "span", "endApagar_".$cod_endereco);
  $objResponse->assign("endApagar_".$cod_endereco, "className", 'link');
  $objResponse->assign("endApagar_".$cod_endereco, "innerHTML", 'Apagar<br>');
  $objResponse->addEvent("endApagar_".$cod_endereco, "onClick", "ApagarEndereco('".$cod_curso."', '".$cod_endereco."');");

  Desconectar($sock);

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->call("mostraFeedback", $texto, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* *********************************************************************
   AbreEdicao - Marca no banco de dados o início da edição do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $cod_usuario - código do usuário que está utilizando
            $cod_usuario_portfolio - código do usuário que está utilizando o portfolio
            $cod_grupo_portfolio - código do grupo o qual o usuário faz parte
            $cod_topico_raiz - código do tópico-pai

   Saida: XML da função Ajax
*/
function AbreEdicao($cod_curso, $cod_item, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio, $cod_topico_raiz){

  $objResponse = new xajaxResponse();

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data ="";
  unset ($data);
  $data = time();

  $sock=Conectar($cod_curso);

  //Correção feita para não acontecer de os horários ficarem iguais no banco e atrapalhar o status do item
  $consulta="select * from Portfolio_itens_historicos where data=".$data." and cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  if ($linha!=""){ $data++; }
  
  $linha=RetornaDadosDoItem($sock, $cod_item);

  $linha_historico=RetornaUltimaPosicaoHistorico($sock, $cod_item);

  if (($linha['status']=="E")&&($cod_usuario !=$linha_historico['cod_usuario'])){
    $objResponse->script("window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');");

    $objResponse->script("document.location='portfolio.php?cod_usuario=".$cod_usuario."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_topico_raiz=".$cod_topico_raiz."'");
  }else if ($linha['status']!="E"){
    $consulta="update Portfolio_itens set status='E', cod_usuario=".$cod_usuario.", inicio_edicao=".$data." where cod_item=".$cod_item;
    $res=Enviar($sock,$consulta);
    $consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".$data.", 'E')";
    $res=Enviar($sock,$consulta);
  }

  Desconectar($sock);

  return $objResponse;
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
  $data = "";
  unset ($data);
  $data = time();

  //Correção feita para não acontecer de os horários ficarem iguais no banco e atrapalhar o status do item
  $consulta="select * from Portfolio_itens where data=".$data." and cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  if ($linha!=""){ $data++; }

  $consulta="update Portfolio_itens set status='L', data=".$data." where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  if ($acao){
    $consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".$data.", 'F')";
  }else{
    $consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".$data.", 'D')";
  }

  $res=Enviar($sock, $consulta);

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

  $objResponse = new xajaxResponse();

  $sock=Conectar($cod_curso);

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = "";
  unset ($data);
  $data = time();

  //Correção feita para não acontecer de os horários ficarem iguais no banco e atrapalhar o status do item
  $consulta="select * from Portfolio_itens_historicos where data=".$data." and cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  if ($linha[0]!=""){ $data++; }

  $consulta="update Portfolio_itens set status='L', data=".$data." where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  if($acao){
    $consulta="insert into Portfolio_itens_historicos values ('".$cod_item."', '".$cod_usuario."', '".$data."', 'F')";
  }else{
    $consulta="insert into Portfolio_itens_historicos values ('".$cod_item."', '".$cod_usuario."', '".$data."', 'D')";
  }

  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  //Correção feita para não acontecer de os horários ficarem iguais no banco e atrapalhar o status do item
   if ($linha[0]!=""){ sleep(1); }
  
  return $objResponse;
}

  if ( !function_exists('htmlspecialchars_decode') )
  {
    function htmlspecialchars_decode($text)
    {
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
    }
  }
/* *********************************************************************
   ExcluirArquivo - Exclui o arquivo, dinamicamente
   Entrada: $numero - numero do arquivo, no item
            $arq - endereco do arquivo no servidor
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario

   Saida: XML da função Ajax
*/
function ExcluirArquivo($numero, $arq, $cod_curso, $cod_item, $cod_usuario, $texto){

  $objResponse = new xajaxResponse();
  $arq=htmlspecialchars_decode($arq);
  $arq=str_replace("%20"," ",$arq);
  
  RemoveDiretorio($arq);
  $objResponse->remove('arq_'.$numero);

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->call("mostraFeedback", $texto, 'true');

  return $objResponse;
}

/* *********************************************************************
   ExcluirEndereco - Exclui o endereco, dinamicamente
   Entrada: $cod_curso - codigo do curso
            $cod_endereco - codigo do endereco, no item
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario

   Saida: XML da função Ajax
*/
function ExcluirEndereco ($cod_curso, $cod_endereco, $cod_item, $cod_usuario, $texto)
{
  $objResponse = new xajaxResponse();

  $sock=Conectar($cod_curso);

  $consulta="delete from Portfolio_itens_enderecos where cod_endereco=".$cod_endereco;
  $res=Enviar($sock, $consulta);

  $objResponse->remove('end_'.$cod_endereco);

  Desconectar($sock);

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->call("mostraFeedback", $texto, 'true');

  return $objResponse;
}

/* *********************************************************************
   OcultarArquivosDinamic - Oculta os arquivos passados num array, dinamicamente
   Entrada: $nomes_arquivos - array de nomes dos arquivos
            $msg_oculto - string 'oculto'
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario

   Saida: XML da função Ajax
*/
function OcultarArquivosDinamic($nomes_arquivos, $msg_oculto, $cod_curso, $cod_item, $cod_usuario, $texto){
  $objResponse = new xajaxResponse();

  foreach($nomes_arquivos as $cod => $linha){
    $nome_arquivo = implode("/", explode("//", $linha[0]));
    $nome_arquivo = str_replace("%20"," ",$nome_arquivo);

    AlteraStatusArquivo($nome_arquivo,true);

    $objResponse->create('local_oculto_'.$linha[1], 'span', 'arq_oculto_'.$linha[1]);

    $objResponse->create('arq_oculto_'.$linha[1], 'span', 'arq_oculto_in1_'.$linha[1]);
    $objResponse->assign('arq_oculto_in1_'.$linha[1], 'innerHTML', '&nbsp;- ');

    $objResponse->create('arq_oculto_'.$linha[1], 'span', 'arq_oculto_in2_'.$linha[1]);
    $objResponse->assign('arq_oculto_in2_'.$linha[1], 'innerHTML', $msg_oculto);
    $objResponse->assign('arq_oculto_in2_'.$linha[1], 'className', 'arqOculto');

    $objResponse->script('document.getElementById(\'nomeArq_'.$linha[1].'\').setAttribute(\'arqOculto\', \'sim\');');
  }
  $objResponse->addEvent('sArq_ocultar', 'onclick', 'Desocultar();');
  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->call("mostraFeedback", $texto, 'true');

  return $objResponse;
}


/* *********************************************************************
   DesocultarArquivosDinamic - Desoculta os arquivos passados num array, dinamicamente
   Entrada: $nomes_arquivos - array de nomes dos arquivos
            $msg_oculto - string 'oculto'
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario

   Saida: XML da função Ajax
*/
function DesocultarArquivosDinamic($nomes_arquivos, $cod_curso, $cod_item, $cod_usuario, $texto){
  $objResponse = new xajaxResponse();

  foreach($nomes_arquivos as $cod => $linha){
    $nome_arquivo = implode("/", explode("//", $linha[0]));
    $nome_arquivo = str_replace("%20"," ",$nome_arquivo);

    AlteraStatusArquivo($nome_arquivo,false);

    $objResponse->remove('arq_oculto_'.$linha[1]);
    $objResponse->script('document.getElementById(\'nomeArq_'.$linha[1].'\').setAttribute(\'arqOculto\', \'nao\');');

  }
  $objResponse->addEvent('sArq_ocultar', 'onclick', 'Ocultar();');
  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->call("mostraFeedback", $texto, 'true');

  return $objResponse;
}


/* *********************************************************************
   MoverArquivosDinamic - Move o arquivo, dinamicamente
   Entrada: $origem - endereco de origem do arquivo
            $destino - endereco de destino do arquivo
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario
            $msg_erro - mensagem de erro, caso ocorra

   Saida: XML da função Ajax
*/
function MoverArquivosDinamic($origem, $destino, $cod_curso, $cod_item, $cod_usuario, $msg_erro){
  $objResponse = new xajaxResponse();

  // Se a origem ou destino forem vazios retorna false
  if (($origem == "") || ($destino == ""))
    return $objResponse;

  // Vari?el de retorno da fun?o
  $flag = true;

  // Resolve o caminho da origem ('realpath' testa tamb? se o arquivo
  // existe).

  //linha necessária para corrigir nome de pastas ou arquivos com espaço em branco
  $origem = eregi_replace("%20", " ", $origem);
  $origem = realpath($origem);
  if ($origem == false){
    $objResponse->script("EscondeLayers();");
    $objResponse->call("mostraFeedback", $msg_erro, 'false');
    return $objResponse;
  }

  // Se retornar mais que um elemento (ou seja, pelo menos
  // um que n? seja o pr?rio arquivo ou pasta a ser movida)
  // ent? os movemos.
  $total = count($conteudo);

      $nome_arquivo = basename($origem);

      $dest = $destino.DIRECTORY_SEPARATOR.$nome_arquivo;

      if (ExisteArquivo($dest)){
        AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);
        $objResponse->script("EscondeLayers();");
        $objResponse->script("window.location='ver.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&cod_topico_raiz='+cod_topico_ant+'&cod_usuario_portfolio='+cod_usuario_portfolio+'&acao=moverarquivos&atualizacao=true';");
        return $objResponse;
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
              $objResponse->script("EscondeLayers()");;
              $objResponse->call("mostraFeedback", $msg_erro, 'false');
              return $objResponse;
            }
          }
          else{
            $objResponse->script("EscondeLayers();");
            $objResponse->call("mostraFeedback", $msg_erro, 'false');
            return $objResponse;
          }
        }
        else{
          $objResponse->script("EscondeLayers();");
          $objResponse->call("mostraFeedback", $msg_erro, 'false');
          return $objResponse;
        }
      }

  clearstatcache();

  if (is_dir($origem))
    $flag = RemoveDiretorio($origem);

  $objResponse->script("window.location='ver.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&cod_topico_raiz='+cod_topico_ant+'&cod_usuario_portfolio='+cod_usuario_portfolio+'&acao=moverarquivos&atualizacao=true';");

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  return $objResponse;
}


/* *********************************************************************
   MoverItensDinamic - Move os itens passados num array, dinamicamente
   Entrada: $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_topico_raiz - codigo do topico-pai dos itens
            $cod_topico_novo - codigo do novo topico-pai dos itens
            $cod_topicos - string com os codigos dos topicos dos itens separados por virgula
            $cod_topicos - string com os codigos dos itens separados por virgula

   Saida: XML da função Ajax
*/
function MoverItensDinamic($cod_curso, $cod_usuario, $cod_topico_raiz, $cod_topico_novo, $cod_topicos, $cod_itens){

  $objResponse = new xajaxResponse();

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,15);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  //existem tópicos
  if (count($cod_topicos))
  {

    if (EPaiTopicos($sock, $cod_topico_novo, $cod_topicos))
    {
      //28 - Você não pode mover uma pasta para ela mesma ou para uma subpasta dela.
      $objResponse->call("mostraFeedback", RetornaFraseDaLista($lista_frases,28), 'false');
    }
    else
    {
      $existe = 0;
      for($i = 0; $i < count($cod_topicos); $i++)
      {
         if (!NaoExisteTop($sock, $cod_topico_novo, $cod_topicos[$i], $cod_usuario))
           $existe = 1;
      }

      if (!$existe)
      {
        // move todos os topicos selecionados
        for($i = 0; $i < count($cod_topicos); $i++)
        {
          MoverTopico($sock, $cod_topicos[$i], $cod_usuario, $cod_topico_novo);
        }
        // se tiver itens selecionados, move-os também
        if (strlen($cod_itens))
        {

          for($i = 0; $i < count($cod_itens); $i++)
          {
            MoverItem($sock, $cod_itens[$i], $cod_usuario, $cod_topico_novo);
          }
        }
        $objResponse->script('Redirecionar('.$cod_topico_raiz.', "moverItens", "true");');
      }
      //só existem itens
      else //existe topico com mesmo nome no diretorio destino
      {
         /* 71- Não foi possível mover a pasta, pois já existe uma pasta com mesmo nome no diretório destino. */
        $objResponse->call("mostraFeedback", RetornaFraseDaLista($lista_frases,71), 'false');
      }
    }
  }
  else
  {
    if (count($cod_itens))
    {
      for($i = 0; $i < count($cod_itens); $i++)
      {
        MoverItem($sock, $cod_itens[$i], $cod_usuario, $cod_topico_novo);
      }
    }
    $objResponse->script('Redirecionar('.$cod_topico_raiz.', "moverItens", "true");');
  }

  ArrumaPosicoes($sock, $cod_topico_novo);
  ArrumaPosicoes($sock, $cod_topico_raiz);

  Desconectar($sock);

  return $objResponse;
}


/* *********************************************************************
   NaoExisteTop - Retorna verdadeiro se n� existir uma pasta com mesmo nome que novo_nome, com mesmo
codigo_topico_pai e cod_usuario na tabela
   Entrada: $sock - BASE DO CURSO
            $cod_topico_pai - codigo do topico onde se encontra
            $topico - nome da pasta ou codigo da pasta
            $cod_usuario - codigo do usuario
   Saida: true, se n� existir topico com mesmo nome, false caso contr�io
*/
function NaoExisteTop($sock, $cod_topico_pai, $topico, $cod_usuario)
{
  if (is_numeric($topico))
  {
    $consulta = "select topico from Portfolio_topicos where cod_topico = ".$topico;
    $res = Enviar($sock, $consulta);
    $linha = RetornaLinha($res);
    $topico = $linha['topico'];
  }

  $consulta = "select topico from Portfolio_topicos where cod_usuario = ".$cod_usuario." and cod_topico_pai = ".$cod_topico_pai." and topico = '".$topico."'";

  $res = Enviar($sock, $consulta);
  $num = RetornaNumLinhas($res);

  if ($num > 0)
    return false;
  return true;
}

/* *********************************************************************
   EPaiTopicos - Retorna verdadeiro se algum elemento de v_topicos for descendente de $cod_topico_dest
   Entrada: $sock - BASE DO CURSO
            $cod_topico_dest - codigo do topico pra onde ser� movidos os v_topicos
            $v_topicos - codigos do topicos
   Saida: true, se For pai, false caso contr�io
*/
function EPaiTopicos($sock, $cod_topico_dest, $v_topicos)
{
  for($i = 0; $i < count($v_topicos); $i++)
  {
    if(EPai($sock, $v_topicos[$i], $cod_topico_dest))
      return true;
  }
  return false;
}

/* *********************************************************************
   ExcluirItensDinamic - Exclui os itens, dinamicamente
   Entrada: $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_item - array com o codigo dos itens

   Saida: XML da função Ajax
*/
function ExcluirItensDinamic($cod_curso, $cod_usuario, $cod_itens){

  $objResponse = new xajaxResponse();

  $sock=Conectar($cod_curso);

  if(!is_array($cod_itens)){
    ExcluirItem($sock, $cod_itens, $cod_usuario);
  } else {
    foreach($cod_itens as $cod => $linha){
      ExcluirItem($sock, $linha, $cod_usuario);
    }
  }

  Desconectar($sock);

  $objResponse->script("Recarregar('excluirItens', 'true');");

  return $objResponse;
}


/* *********************************************************************
   RecuperarItensDinamic - Exclui os itens, dinamicamente
   Entrada: $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_grupo_portfolio - codigo do grupo (NULL ou "" se portfolio individual)
            $cod_itens - array com o codigo dos itens

   Saida: XML da função Ajax
*/
function RecuperarItensDinamic($cod_curso, $cod_usuario, $cod_grupo_portfolio, $cod_itens){

  $objResponse = new xajaxResponse();

  $sock=Conectar($cod_curso);
  
  if(!is_array($cod_itens)){
    RecuperarItem($sock, $cod_itens, $cod_usuario,$cod_grupo_portfolio);
  } else {
    foreach($cod_itens as $cod => $linha){
      RecuperarItem($sock, $linha, $cod_usuario,$cod_grupo_portfolio);
    }
  }

  Desconectar($sock);

  $objResponse->script("Recarregar('recuperarItens', 'true');");

  return $objResponse;
}

/* *********************************************************************
   RetornaFraseDinamic - Retorna lista de frases para uma variavel JavaScript, dinamicamente
   Entrada: $variavel - nome da variavel que vai receber as frases

   Saida: XML da função Ajax
*/
function RetornaFraseDinamic($variavel){

  $objResponse = new xajaxResponse();

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,15);

  Desconectar($sock);

  $retorno="{\n";

  foreach ($lista_frases as $cod => $linha){
    if ($cod>1) $retorno.=", ";
    $retorno.="\"msg".$cod."\":\"".$linha."\" ";
  }
  
  $retorno.="\n}";

  $objResponse->script($variavel." = ".$retorno.";");

  return $objResponse;
}

/* *********************************************************************
   RetornaFraseDinamic - Retorna lista de frases 'geral' para uma variavel JavaScript, dinamicamente
   Entrada: $variavel - nome da variavel que vai receber as frases

   Saida: XML da função Ajax
*/
function RetornaFraseGeralDinamic($variavel){

  $objResponse = new xajaxResponse();

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $retorno="{\n";

  foreach ($lista_frases as $cod => $linha){
    if ($cod>1) $retorno.=", ";
    $retorno.="\"msg_ger".$cod."\":\"".$linha."\" ";
  }
  
  $retorno.="\n}";

  $objResponse->script($variavel." = ".$retorno.";");


  return $objResponse;
}

function RetornaTituloAvaliacaoDoItem($sock, $cod_item){
	$query = "select cod_avaliacao from Portfolio_itens_avaliacao where cod_item =".$cod_item;
	$res = Enviar($sock, $query);
	$linha = RetornaLinha($res);
	
	if(!$linha[0]) return '';
	
	$query = "select status from Avaliacao where cod_avaliacao=".$linha[0];
	$res=Enviar($sock,$query);
	$status = RetornaLinha($res);
	
	if($status[0]!='X' && $status[0]!='A'){
	
	$query="select cod_atividade from Avaliacao where cod_avaliacao=".$linha[0];
	$res=Enviar($sock,$query);
	$linha2=RetornaLinha($res);
	
	$query="select titulo from Atividade_itens where cod_item=".$linha2[0];
	$res = Enviar($sock, $query);
	$linha3 = RetornaLinha($res);
	
	$resposta = $linha3[0];
	}
	else{
	  $resposta = "";
	}
	
	return $resposta;
}

/* *********************************************************************
   AssociaAvaliacaoDinamic - Associa uma avaliacao ao item, dinamicamente
   Entrada: $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_item - codigo do item
            $cod_avaliacao - codigo da avaliacao
            $msg1 - 'Nao foi selecionado nenhuma avaliacao.'
            $msg2 - 'Item associado à avaliação:'

   Saida: XML da função Ajax
*/
function AssociaAvaliacaoDinamic($cod_curso, $cod_usuario, $cod_item,$cod_avaliacao, $msg1, $msg2, $msg_erro){

  $objResponse = new xajaxResponse();

  $sock=Conectar($cod_curso);

  $query="select * from Portfolio_itens_avaliacao where cod_item='".$cod_item."'";
  $res=Enviar($sock, $query);
  $linha=RetornaLinha($res);
  
  
  $cod_avaliacao_atual = $linha[0]['cod_avaliacao'];
      
  // Se cod_avaliacao nao for vazio, foi selecionada uma avaliacao no radiobox
  if ($cod_avaliacao!="") {
    
    if ($linha[0]=="") {  // Se nao existe avaliacao associada ao $cod_item
      AssociarItemAvaliacao($sock,$cod_item,$cod_avaliacao);  // Associa a avaliacao no BD
      $objResponse->script("AtualizaAssociacaoForm(".$cod_avaliacao.")");  // Esconde o radiobox da avaliacao na listagem
      $objResponse->call("mostraFeedback", $msg2, 'true');
	  $atividade=RetornaTituloAvaliacaoDoItem($sock, $cod_item);
      $objResponse->assign("estadoAvaliacao","innerHTML", "<a class=text href=# onClick=\"window.open('../avaliacoes/ver_popup.php?&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."','VerAvaliacao','width=450,height=450,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');EscondeLayers();return(false);\">".$atividade."</a><br />");
    } else {  //Se jah existe uma avaliacao associada ao $cod_item
        $foi_avaliado = FoiAvaliado($sock, $cod_avaliacao, $cod_usuario);
        if($foi_avaliado){
          $objResponse->call("mostraFeedback", $msg_erro, 'false');
        }
        else{
          AlterarAssociacaoItemAvaliacao($sock,$cod_item,$cod_avaliacao);  // Altera a associacao no BD
          $objResponse->script("AtualizaDesassociacaoForm(".$linha[0]['cod_avaliacao'].")");  //Mostra o radiobox da antiga avaliacao associada
          $objResponse->script("AtualizaAssociacaoForm(".$cod_avaliacao.")");  // Esconde o radiobox da avaliacao na listagem
          $objResponse->call("mostraFeedback", $msg2, 'true');
          $atividade=RetornaTituloAvaliacaoDoItem($sock, $cod_item);
          $objResponse->assign("estadoAvaliacao","innerHTML", "<a class=text href=# onClick=\"window.open('../avaliacoes/ver_popup.php?&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."','VerAvaliacao','width=450,height=450,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');EscondeLayers();return(false);\">".$atividade."</a><br />");
      }
    }
    
  } else {  // Nao foi selecionada avaliacao
    $objResponse->script("alert('".$msg1."');");
    $objResponse->script("FechaDivAvaliacoes();");
  }

  Desconectar($sock);

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  return $objResponse;
}

/* *********************************************************************
   DesassociaAvaliacaoDinamic - Associa uma avaliacao ao item, dinamicamente
   Entrada: $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_item - codigo do item
            $msg1 - 'Não'
            $msg2 - 'Item desassociado à avaliação com sucesso.'

   Saida: XML da função Ajax
*/
function DesassociaAvaliacaoDinamic($cod_curso, $cod_usuario, $cod_item, $msg1, $msg2){

  $objResponse = new xajaxResponse();
  
  global $lista_frases_geral;

  $sock=Conectar($cod_curso);

  $query="select * from Portfolio_itens_avaliacao where cod_item='".$cod_item."'";
  $res=Enviar($sock, $query);
  $linha=RetornaArrayLinhas($res);

  //Existe Associação
  if ($linha[0]!=""){
    DesassociarItemAvaliacao($sock,$cod_item);
    $objResponse->script("AtualizaDesassociacaoForm(".$linha[0]['cod_avaliacao'].")");
  }

  Desconectar($sock);

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->assign("estadoAvaliacao","innerHTML", $msg1);
  $objResponse->clear("associadoItem", "innerHTML");
  $objResponse->assign("assocAval", "className", "link");
  $objResponse->addEvent("assocAval", "onClick", "AssociarAvaliacao()");
  $objResponse->script("document.getElementById('tituloAval').removeChild(document.getElementById('tituloAval').lastChild);");
  $objResponse->script("cancelarElemento=null;");
  $objResponse->script("DesmarcaRadios();");
  $objResponse->assign("divAvaliacoes", "className", "divHidden");
  $objResponse->call("mostraFeedback", $msg2, 'true');

  return $objResponse;
}

function VerificaSePodeDesassociar($cod_curso, $cod_usuario, $cod_item, $msg1, $msg2, $msg_erro){
  $objResponse = new xajaxResponse();
  
  $sock=Conectar($cod_curso);

  $query="select * from Portfolio_itens_avaliacao where cod_item='".$cod_item."'";
  $res=Enviar($sock, $query);
  $linha=RetornaArrayLinhas($res);
  
  if($linha[0]!=""){
    
    $cod_avaliacao = $linha[0]['cod_avaliacao'];
    
    $foi_avaliado = FoiAvaliado($sock, $cod_avaliacao, $cod_usuario);
    
    if (!$foi_avaliado){
      DesassociarItemAvaliacao($sock,$cod_item);
      $objResponse->script("AtualizaDesassociacaoForm(".$linha[0]['cod_avaliacao'].")");
      Desconectar($sock);
      AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);
      
      $objResponse->assign("estadoAvaliacao","innerHTML", $msg1);
      $objResponse->clear("associadoItem", "innerHTML");
      $objResponse->assign("assocAval", "className", "link");
      $objResponse->addEvent("assocAval", "onClick", "AssociarAvaliacao()");
      $objResponse->script("document.getElementById('tituloAval').removeChild(document.getElementById('tituloAval').lastChild);");
      $objResponse->script("cancelarElemento=null;");
      $objResponse->script("DesmarcaRadios();");
      $objResponse->assign("divAvaliacoes", "className", "divHidden");
      $objResponse->call("mostraFeedback", $msg2, 'true');
    }
    else{
      $objResponse->call("mostraFeedback", $msg_erro, 'false');
    }
  }

  return $objResponse;
   
  }
/* *********************************************************************
   IndentarComentario - Indenta o texto com os espaços passados por parâmetro
   Entrada: $texto - texto a ser alterado
            $spaces - espaços a serem acrescentados

   Saida: texto alterado
*/
function IndentarComentario($texto, $spaces){
  return (implode($spaces."<br>",explode("<br>",$texto)));
}

function AtualizaPosicoes($cod_curso, $cod_usuario, $cod_topico, $tabela, $texto, $msg_erro){
  $objResponse = new xajaxResponse();

  $sock=Conectar($cod_curso);

  if(PegaSemaforo($sock, "Portfolio"))
  {
    foreach($tabela as $cod => $linha){
      $vetor = split('_', $linha);
      if(!is_numeric($vetor[1])){
        //é um topico
        $query = "update Portfolio_topicos set posicao_topico = ".($cod+1)." where cod_topico=".$vetor[2];
      }else{
        $query = "update Portfolio_itens set posicao_item = ".($cod+1)." where cod_topico=".$cod_topico." and cod_item=".$vetor[1];
      }

      Enviar($sock, $query);
    }

    LiberaSemaforo($sock, "Portfolio");
    $objResponse->call("mostraFeedback", $texto, 'true');
  }
  else
  {
    $objResponse->call("mostraFeedback", $msg_erro, 'false');
  }

  Desconectar($sock);

  return $objResponse;
}

function RenomearTopicoDinamic ($cod_curso, $cod_usuario, $cod_topico, $novo_nome, $texto, $msg_erro){
  $objResponse = new xajaxResponse();
  $sock=Conectar($cod_curso);

  $novo_nome=ConverteAspas2BarraAspas($novo_nome);

  $query = "select cod_topico_pai from Portfolio_topicos where cod_topico = ".$cod_topico;
  $res = Enviar($sock, $query);

  $cod_topico_raiz = RetornaLinha($res);

  if (NaoExisteTop($sock, $cod_topico_raiz[0], $novo_nome, $cod_usuario))
  {
    RenomearTopico ($sock, $cod_topico, $novo_nome);
    $objResponse->assign("nome_topico_atual", "innerHTML", htmlentities($novo_nome));
    $objResponse->script("EscondeLayers();");
    $objResponse->call("mostraFeedback", $texto, 'true');
  }else{
    $objResponse->script("document.getElementById('nome_topico_atual').innerHTML=nome_topico_atual;");
    $objResponse->script("EscondeLayers();");
    $objResponse->call("mostraFeedback", $msg_erro, 'false');
  }

  Desconectar($sock);
  return $objResponse;

}

function CriaTopicoDinamic($cod_curso, $cod_usuario, $cod_grupo_portfolio, $cod_usuario_portfolio, $cod_topico_raiz, $dirname, $novo_nome, $msg_erro){
  $objResponse = new xajaxResponse();

  $sock = Conectar("");

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');

  Desconectar($sock);


  $sock=Conectar($cod_curso);

  if (NaoExisteTop($sock, $cod_topico_raiz, $novo_nome, $cod_usuario))
  {
    $cod_topico=CriarTopico($sock, $cod_topico_raiz, $novo_nome, $cod_usuario, $cod_grupo_portfolio);

    $objResponse->redirect("portfolio.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico."&cod_usuario=".$cod_usuario."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao=criarTopico&atualizacao=true");

  }else{
    $objResponse->call("mostraFeedback", $msg_erro, 'false');
  }


  Desconectar($sock);
  return $objResponse;

}

/* ***********************************************************************************************************
   haArquivosVisiveisDir - verifica se ha arquivo visivel dentro da pasta
   Entrada: $lista_arq_vis - vetor retornado da funcao RetornaArquivosVisiveis
   Saida: Retorna true se existe algum arquivo nao oculto dentro da pasta ou das subpastas, false caso contrario
*/
function haArquivosVisiveisDir($dir, $lista_arq_vis){    
  foreach($lista_arq_vis as $cod => $linha){ 
    if(!($linha['Status'])){
	  if (($linha['Arquivo']!="") && (strncmp($linha['Diretorio'], $dir, strlen($dir)) == 0)){
	    return true;
	  }
    }
  }
  return false;
}

/* ***********************************************************************************************************
   PodeAlterarPortfolio - verifica as condi��es de um item com avalia��o, para que ele possa ou n�o alterar a sess�o
   de arquivos, associar a uma nova avalia��o ou desassociar o item da avalia��o
   Entrada: $cod_curso, $cod_usuario e $cod_item
   Saida: Retorna true se o item ainda n�o foi avaliado e se a avalia��o ainda est� ocorrendo (ainda n�o terminou), e false caso contr�rio 
*/
function PodeAlterarPortfolio($cod_curso, $cod_usuario, $cod_item){
  $sock=Conectar($cod_curso);
  
  $data_atual = time();
  
  $query="select * from Portfolio_itens_avaliacao where cod_item='".$cod_item."'";
  $res=Enviar($sock, $query);
  $linha=RetornaArrayLinhas($res);
  
  $ha_avaliacao = $linha[0]!="";
  
  if($ha_avaliacao){
    $cod_avaliacao = $linha[0]['cod_avaliacao'];
   
    $query2="select * from Avaliacao where cod_avaliacao='".$cod_avaliacao."'";
    $res2=Enviar($sock, $query2);
    $linha2=RetornaArrayLinhas($res2);
    
    $data_termino = $linha2[0]['data_termino'];
    
    $foi_avaliado = FoiAvaliado($sock, $cod_avaliacao, $cod_usuario);
    
    Desconectar($sock);

   if (($data_atual < $data_termino) && (!$foi_avaliado)){
      return true;
   }
   return false;
  }
}

function TemAvaliacao($cod_curso, $cod_item){
  $sock=Conectar($cod_curso);
  
  $query="select * from Portfolio_itens_avaliacao where cod_item='".$cod_item."'";
  $res=Enviar($sock, $query);
  $linha=RetornaArrayLinhas($res);
  
  if ($linha[0]!=""){
    return true;
  }
  return false;
}

/* *********************************************************************
   ExpulsaVisitante - verifica se o usuario eh visitante e impede seu
                      acesso se for
   entrada: $sock        - sock de conexao
            $cod_curso   - c�digo do curso atual.
            $cod_usuario - c�digo do usu�rio o qual pretende-se testar o
                           acesso � ferramenta correio.
            $eh_popup    - booleano que indica se a p�gina que o chamou
                           � ou n�o um popup. Se n�o for, devemos fechar
                           as tags html do template comum de uma p�gina
                           de ferramenta (com rodap�, fechando o tabel�o,
                           etc).
            saida:   nenhuma
   OBS:     Para impedir o acesso, exibe tela com restricao de acesso e
            executa o comando exit
*/
function ExpulsaVisitante($sock, $cod_curso, $cod_usuario, $eh_popup = false) {

  global $lista_frases;
  global $lista_frases_geral;

  if (EVisitante($sock, $cod_curso, $cod_usuario)) {

    //if (!$ehPopup) {
    //  include("../menu_principal.php");
    //  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    //}
    if ($eh_popup) {
      echo("  </head>\n");
      echo("  <body>\n");
      echo("    <h3 style=\"margin-top:20px;\">".NomeCurso($sock,$cod_curso)."</h3>\n");
    }

    /* 1 - Portf�lio 504 - �rea restrita a alunos e formadores */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases_geral,504)."</h4>\n");

    if (!$eh_popup) {
      echo("          <ul class=\"btsNav\">\n");
      echo("            <li>\n");
      /* 509 - Voltar */
      echo("              <span onclick=\"javascript:history.back(-1);\">\n");
      echo("                &nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;\n");
      echo("              </span>\n");
      echo("            </li>\n");
      echo("          </ul>\n");
    }

    if (!$eh_popup) {
      echo("          <div id=\"mudarFonte\">\n");
      echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
      echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
      echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
      echo("          </div>\n");
    }

    if ($eh_popup) {
      /* 13 - Fechar */
      echo("          <form>\n");
      echo("            <input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral, 13)."\" onclick=\"javascript:self.close();\" />\n");
      echo("          </form>\n");
    }
    else {
      /* 23 - Voltar */
      echo("          <form>\n");
      echo("            <input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral, 23)."\" onclick=\"javascript:history.go(-1);\" />\n");
      echo("          </form>\n");
    }

    if (!$eh_popup) {
      echo("        </td>\n");
      echo("      </tr>\n");
      include("../tela2.php");
    }

    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }
}

?>
