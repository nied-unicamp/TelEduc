<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/dinamica/dinamica.inc

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

function RetornaDiretorio($sock,$item)
{
  $query="select diretorio from Diretorio where item='".VerificaStringQuery($item)."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha[0]);
}

/* *********************************************************************
   ExisteDinamica - Verifica se existe uma dinamica do curso
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
   Saida: 'N' - N�o tem
          'A' - Existem Arquivos
          'R' - Existe Registro na Base
*/
function ExisteDinamica($sock,$cod_curso,$diretorio_arquivos)
{
  $query="select * from Dinamica";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)==0)
        return 'N';
  else
  {
    $linha=RetornaLinha($res);
    if ($linha['status']=='I')
          return 'N';
    $lista=RetornaArrayDiretorio($diretorio_arquivos."/".$cod_curso."/dinamica/");
    if (count($lista)>0)
      return 'A';
    else
    {
      return 'R';
    }
  }
}

/* *********************************************************************
   ExisteDinamicaImport - Verifica se existe uma dinamica do curso
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
   Saida: 'N' - N�o tem
          'A' - Existem Arquivos
          'R' - Existe Registro na Base
*/
function ExisteDinamicaImport($sock,$cod_curso,$diretorio_arquivos)
{
  $query="select * from Dinamica";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha=='I')
  {
    return 'N';
  }
  $lista=RetornaArrayDiretorio($diretorio_arquivos."/".$cod_curso."/dinamica/");
  if (count($lista)>0)
    return 'A';
  else if (RetornaNumLinhas($res)==0)
  {
        return 'N';
  }
  else
  {
    return 'R';
  }

}

/* *********************************************************************
   ArquivoAExibir - Cria link simb�lico para dinamica e retorna o link
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
   Saida: caminho relativo
*/
function ArquivoAExibir($sock,$cod_curso,$cod_usuario,$diretorio_arquivos,$diretorio_temp)
{
  /* Busca Arquivo a ser mostrado */
  $dir=$diretorio_arquivos."/".$cod_curso."/dinamica/";
  $lista=RetornaArrayDiretorio($dir);
  foreach($lista as $cod => $linha)
    if (!$linha['Status'] && $linha['Arquivo']!="")
      $arquivo=$linha['Diretorio']."/".$linha['Arquivo'];

  /* Cria link simb�lico (apaga antigo, se houver)*/
  if ($cod_usuario<0)
    $cod_usuario=0;
  $dirlink=$diretorio_temp."/dinamica_".$cod_curso."_".$cod_usuario;
  $dirlinkpath="../../diretorio/dinamica_".$cod_curso."_".$cod_usuario;

  if (ExisteArquivo($dirlink))
    RemoveArquivo($dirlink);
  echo("(".$dirlink.")");
  CriaLinkSimbolico($dir,$dirlink);
        return ($dirlinkpath."/".ConverteUrl2Html($arquivo));
}


/* *********************************************************************
   ArquivoAExibirImport - Cria link simb�lico para dinamica e retorna o link
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
   Saida: caminho relativo
*/
function ArquivoAExibirImport($sock,$cod_curso,$cod_curso_origem,$cod_usuario,$diretorio_arquivos,$diretorio_temp,$arquivo)
{
  /* Busca Arquivo a ser mostrado */
  $dir=$diretorio_arquivos."/".$cod_curso_origem."/dinamica/";

    /* Cria link simb�lico (apaga antigo, se houver)*/
  if ($cod_usuario<0)
    $cod_usuario=0;
  $dirlink=$diretorio_temp."/dinamica_".$cod_curso."_".$cod_usuario."_".$cod_curso_origem;
  $dirlinkpath="../../diretorio/dinamica_".$cod_curso."_".$cod_usuario."_".$cod_curso_origem;

  if (ExisteArquivo($dirlink))
    RemoveArquivo($dirlink);
  CriaLinkSimbolico($dir,$dirlink);
        return ($dirlinkpath."/".ConverteUrl2Html($arquivo));
}

/* *********************************************************************
   RetornaTextoDinamica - Retorna o Texto atual da dinamica
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
   Saida: Texto da Dinamica
*/
function RetornaTextoDinamica($sock,$cod_curso)
{
  $query="select texto from Dinamica";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $saida=$linha['texto'];
  return ($saida);
}

/* *********************************************************************
   EncerrarExibicaoArquivo - Cria link simb�lico para dinamica e retorna o link
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
   Saida: caminho relativo
*/
function EncerrarExibicaoArquivo($sock,$cod_curso,$cod_usuario,$diretorio_arquivos)
{
  /* Cria link simb�lico (apaga antigo, se houver)*/
  if ($cod_usuario<0)
    $cod_usuario=0;
  $dirlink="../../diretorio/dinamica_".$cod_curso."_".$cod_usuario;
  return (RemoveArquivo($dirlink));
}

/* *********************************************************************
   CopiaArquivoParaTemporario - Copia os arquivos para temporario
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
   Saida: false - se houve algum erro.
*/
function CopiaArquivoParaTemporario($sock,$cod_curso,$diretorio_arquivos,$diretorio_temp)
{

  $dir=$diretorio_arquivos."/".$cod_curso."/dinamica/";
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/dinamica/";
  $dirtemp2=$diretorio_temp."/tmp/".$cod_curso."/";

  if (($diretorio_temp == "") || ($diretorio_arquivos == "") || ($cod_curso == ""))
    return false;
  if (!ExisteArquivo($dirtemp2))
  {
    if (!CriaDiretorio($dirtemp2))
      return false;
  }

  if (!ExisteArquivo($dir))
  {
    if (!CriaDiretorio($dir))
      return false;
  }

  if (ExisteArquivo($dirtemp))
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
   RemoveTemporario - Remove os arquivos para temporario
                      Se for na primeira inclus�o, remove da tabela tamb�m
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
   Saida: false - se houve algum erro.
*/
function RemoveTemporario($sock,$cod_curso,$diretorio_arquivos,$diretorio_temp)
{
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/dinamica/";


  $query="select status from Dinamica";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  if ($linha[0]=='E')
  {
    $query="update Dinamica set status='A',data=".time();
    Enviar($sock,$query);
  }

  if (ExisteArquivo($dirtemp))
  {
    if (!RemoveDiretorio($dirtemp));
      return false;
  }

  if ($linha[0]=='I')
  {
    $query="delete from Dinamica";
    $res=Enviar($sock,$query);
  }

  return true;
}

/* *********************************************************************
   AtualizaDinamica - Atualiza o Texto e os arquivos
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $texto - Texto a ser colocado
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
   Saida: false - se houve algum erro.
*/
function AtualizaDinamica($sock,$cod_curso,$texto,$diretorio_arquivos,$diretorio_temp)
{
  /* Copiar arquivos, quando necess�rio... */
  $dir=$diretorio_arquivos."/".$cod_curso."/";
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/dinamica/";

  $ret=SobrepoeDiretorio($dirtemp,$dir);
	AtualizaFerramentasNova($sock, $cod_ferramenta, 'T');
  $query="update Dinamica set texto='".$texto."',status='A',data=".time();
  Enviar($sock,$query);
}


/* *********************************************************************
   RetornaArquivosDinamica - Retorna lista de arquivos da Dinamica de Curso
   Entrada: $diretorio_temp - diretorio temporario de arquivos
   Saida: Array multidimensional com:
          $lista[<num>]['Caminho'] - caminho completo.
          $lista[<num>]['Diretorio'] - Diretorio do arquivo
          $lista[<num>]['Arquivo'] - Nome do arquivo
          $lista[<num>]['Status'] - Condi��o especial (true ou false);
          $lista[<num>]['Tamanho'] - tamanho do arquivo
          $lista[<num>]['Data'] - data da �ltima modifica��o
*/
function RetornaArquivosDinamica($cod_curso,$diretorio_temp)
{
  $dirtemp=$diretorio_temp."/tmp/".$cod_curso."/dinamica/";
  return (RetornaArrayDiretorio($dirtemp));
}

/* *********************************************************************
   RetornaDadosDinamica - Retorna os dados da dinamica atual
   Entrada: $sock - Base do Curso
   Saida: array ['status'], ['cod_usuario'], ['data']
*/
function RetornaDadosDinamica($sock)
{
  $query="select cod_dinamica,texto,status,cod_usuario,data from Dinamica";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha);
}

/* *********************************************************************
   AtualizaEdicaoDinamica - Atualiza os dados de edicao da dinamica atual
   Entrada: $sock - Base do Curso
   Saida: nenhuma
*/
function AtualizaEdicaoDinamica($sock,$cod_usuario)
{
  $query="update Dinamica set status='E', cod_usuario=".VerificaNumeroQuery($cod_usuario).",data=".time();
  $res=Enviar($sock,$query);
}

/* *********************************************************************
   InsereEdicaoDinamica - Retorna os dados da dinamica atual
   Entrada: $sock - Base do Curso
   Saida: nenhuma
*/
function InsereEdicaoDinamica($sock,$cod_usuario)
{
  $query="insert into Dinamica values (1,'',".time().",'I',".VerificaNumeroQuery($cod_usuario).")";
  $res=Enviar($sock,$query);
}

/* *********************************************************************
   ImportarDinamica -
   Entrada: $sock - conex�o com a base interna
            $cod_curso_destino - c�digo do curso no qual ser�o inseridos os
                itens importados.
            $cod_usuario - c�digo do usu�rio que est� importando.
            $cod_curso_origem - c�digo do curso cujos itens ser�o importados.
            $flag_curso_extraido - booleano, true se o curso � extra�do
            $nome_tabela - nome da tabela usada
            $dirname - nome do diret�rio da ferramenta
            $diretorio_arquivos_destino - caminho dos arquivos do curso para onde
                ser�o importados os arquivos dos itens
            $diretorio_arquivos_origem - caminho dos arquivos do curso cujos itens
                ser�o importados.
   Saida: nenhuma

*/
function ImportarDinamica($cod_curso_destino, $cod_usuario, $cod_curso_origem,
                          $flag_curso_extraido, $nome_tabela, $dirname,
                          $diretorio_arquivos_destino, $diretorio_arquivos_origem)
{

  $flag = true;
  if ($cod_curso_destino != $cod_curso_origem)  {
    $flag = false;

    if ($flag_curso_extraido)
      $tmp = TMPDB;
    else
      $tmp = "";

  // Obt�m os itens a serem importados
    $sock = Conectar($cod_curso_origem, $tmp);

    $arquivos_apag = $diretorio_arquivos_destino."/".$cod_curso_destino."/dinamica/";
    if (substr($arquivos_apag, -1)!="/")
    {
      $arquivos_apag .="/";
    }
    $dir = opendir($arquivos_apag); // Abre o diret�rio
    $files = Array(); // Array que conter� todos os objetos do diret�rio
    $result = Array();
    // Toda vez que a fun��o readdir � chamada, ela retorna o objeto e 'move' para o pr�ximo, caso exista.
    while ($file = readdir($dir))
    {
      $files[] = $file;
    }
    // Agora eu lerei todos os objetos do diret�rio e selecionarei apenas os que forem arquivos. Eu verifico isso usando a fun��o 'is_file'
    for ($i=0; $i<count($files); $i++)
    {
      if (is_file(addslashes(realpath($arquivos_apag.$files[$i]))))
        {
           $result[] = $files[$i];
        }
    }
    sort($result); // Ordena os arquivos em ordem alfab�tica
    closedir($dir);

//    $i = sizeof($result);
//    for ($itemp=0;$itemp!=$i;$itemp++)
//    {
//      echo $arquivos_apag.$result[$itemp];
//      RemoveArquivo($arquivos_apag.$result[$itemp]);
//    }

    if ($diretorio_arquivos_destino == "" || $cod_curso_destino == "")
       return false;

    RemoveDiretorio($diretorio_arquivos_destino."/".$cod_curso_destino."/dinamica/");
    CriaDiretorio($diretorio_arquivos_destino."/".$cod_curso_destino."/dinamica/");

    $texto = RetornaTextoDinamica($sock,$cod_curso_origem);


    $caminho = $diretorio_arquivos_origem."/".$cod_curso_origem."/dinamica/";
//    $texto = $linhas_itens[0]['texto'];


    if (!$texto) {
      Desconectar($sock);
      // Importa para a base de dados do CURSO destino
      $sock = Conectar($cod_curso_destino);
        // Se o item a ser importado possui arquivos anexados,
      // ent�o copiamos seus arquivos.
      if (ExisteArquivo($caminho))
      {
        // Criamos os diret�rios onde ficar�o os arquivos do item
        $arquivos_ferramenta = $diretorio_arquivos_destino."/".$cod_curso_destino."/dinamica/";
       // flag - Testa se consegui fazer o update
       if (!ExisteArquivo($arquivos_ferramenta))
         CriaDiretorio($arquivos_ferramenta);
         // flag - Testa se Copiou os arquivos
         $flag = CopiaArquivosDiretorio($caminho, $arquivos_ferramenta);
    }
  }
  Desconectar($sock);

  // Importa para a base de dados do CURSO destino
  $sock = Conectar($cod_curso_destino);
  $query="update ".$nome_tabela." set texto='".VerificaStringQuery($texto)."',status='".VerificaStringQuery($linhas_itens['status'])."',data=".time();
  // flag - Testa se consegui fazer o update
  $flag = Enviar($sock, $query);
  AtualizaFerramentasNova($sock, 16, 'T');
}

  return($flag);
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
   RetornaFraseDinamic - Retorna lista de frases para uma variavel JavaScript, dinamicamente
   Entrada: $variavel - nome da variavel que vai receber as frases

   Saida: XML da função Ajax
*/
function RetornaFraseDinamic($variavel){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,16);

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
        if((trim($pal)!="")&&(trim($pal)!="<p>")&&(trim($pal)!="</p>"))
        {
          return $texto;
        }
      }
    }
  }

  return "";
}

/* *********************************************************************
   EditarTexto - Edita o texto do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $novo_nome - novo texto do item
            $cod_usuario - código do usuário que está utilizando
   Saida: XML da função Ajax
*/
function EditarTexto($cod_curso, $cod_item, $novo_nome, $cod_usuario, $texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $novo_nome=ConverteAspas2BarraAspas($novo_nome);
  $sock=Conectar($cod_curso);

  $consulta="update Dinamica set texto='".VerificaStringQuery(trim(VerificaTexto($novo_nome)))."' where cod_dinamica=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);
  AtualizaFerramentasNova($sock, 16, 'T');
  Desconectar($sock);
  // Imprime no div valores do formulário
  $objResponse->addAssign("tr_".$cod_item, "className", "novoitem");
  $objResponse->addAssign("text_".$cod_item, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas(VerificaTexto($novo_nome))), true));

  $objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   ExcluirArquivo - Exclui o arquivo, dinamicamente
   Entrada: $numero - numero do arquivo, no item
            $arq - endereco do arquivo no servidor

   Saida: XML da função Ajax
*/
function ExcluirArquivo($numero, $arq){

  $objResponse = new xajaxResponse('ISO-8859-1');
  $arq=htmlspecialchars_decode($arq);
  $arq = str_replace("%20"," ",$arq);

  RemoveDiretorio($arq);

  $objResponse->addRemove('arq_'.$numero);

  return $objResponse->getXML();
}

/* *********************************************************************
   SelecionarEntradaDinamic - Oculta os arquivos passados num array, dinamicamente
   Entrada: $nomes_arquivos - array de nomes dos arquivos
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario

   Saida: XML da função Ajax
*/
function SelecionarEntradaDinamic($nomes_arquivos, $cod_curso, $cod_item, $cod_usuario)
{
  AbreEdicao($cod_curso, $cod_item, $cod_usuario);

  $objResponse = new xajaxResponse('ISO-8859-1');

  foreach($nomes_arquivos as $cod => $linha)
  {
    $nome_arquivo = implode("/", explode("//", $linha[0])); 
    if($linha[1] == 1)
      AlteraStatusArquivo($nome_arquivo,true);
    else
      AlteraStatusArquivo($nome_arquivo,false);
  }

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario);
  $objResponse->addScript('window.location = "editar_dinam.php?cod_curso='.$cod_curso.'&cod_usuario='.$cod_usuario.'&cod_ferramenta=16&acao=selecionarEntrada&atualizacao=true"');
  return $objResponse->getXML();
}


/* *********************************************************************
   RetirarEntradaDinamic - Desoculta os arquivos passados num array, dinamicamente
   Entrada: $nomes_arquivos - array de nomes dos arquivos
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario

   Saida: XML da função Ajax
*/
function RetirarEntradaDinamic($nome_arquivo, $cod_curso, $cod_item, $cod_usuario, $texto){

  AbreEdicao($cod_curso, $cod_item, $cod_usuario);  

  $objResponse = new xajaxResponse('ISO-8859-1');

  AlteraStatusArquivo($nome_arquivo,false);

  AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario);

  $objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  return $objResponse->getXML();
}

/* *********************************************************************
   AcabaEdicaoDinamic - Marca no banco de dados o fim da edição do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $cod_usuario - código do usuário que está utilizando

   Saida: XML da função Ajax
*/
function AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $consulta="update Dinamica set status='L',data=".VerificaNumeroQuery($data)." where cod_dinamica=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  return $objResponse->getXML();
}

/* *********************************************************************
   AcabaEdicao - Marca no banco de dados o fim da edição do item dado
   Entrada: $sock - conexão com o banco de dados
            $cod_item - item ao qual o endereco estará associado
            

   Saida: XML da função Ajax
*/
function AcabaEdicao($sock, $cod_item){

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $consulta="update Dinamica set status='L',data=".VerificaNumeroQuery($data)." where cod_dinamica=".VerificaNumeroQuery($cod_item);
  $res=Enviar($sock, $consulta);
}

/* *********************************************************************
   AbreEdicao - Marca no banco de dados o início da edição do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $cod_usuario - código do usuário que está utilizando  

   Saida: XML da função Ajax
*/
function AbreEdicao($cod_curso, $cod_item, $cod_usuario)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);
  
  $linha=RetornaDadosDinamica($sock);

  if (($linha['status']=="E")&&($cod_usuario!=$linha['cod_usuario'])){
    $objResponse->addScript("window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');");

    $objResponse->addScript("document.location='dinamica.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."cod_ferramenta=16'");
  }else {	
    $consulta="update Dinamica set status='E',cod_usuario=".VerificaNumeroQuery($cod_usuario)." where cod_dinamica=".VerificaNumeroQuery($cod_item);
    $res=Enviar($sock,$consulta);
  }

  Desconectar($sock);

  return $objResponse->getXML();
}

/* *********************************************************************
   RetornaArquivosDinamicaVer - Retorna lista de arquivos da Dinamica
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
function RetornaArquivosDinamicaVer($cod_curso, $dirlink)
{
  return (RetornaArrayDiretorio($dirlink));
}

/* *********************************************************************
   IniciaCriacaoDinamica - Insere uma nova Dinamica no Banco de Dados
   Entrada: $sock - Base do Curso
            $cod_usuario - codigo do usuario que está utlilizando
   Saida: nenhuma
*/
function IniciaCriacaoDinamica($sock,$cod_usuario)
{
  $cod_dinamica=RetornaProximoCodigo($sock, "Dinamica");
  $query="insert into Dinamica values (".VerificaNumeroQuery($cod_dinamica).",'',".time().",'L',".VerificaNumeroQuery($cod_usuario).")";
  $res=Enviar($sock,$query);
}
?>
