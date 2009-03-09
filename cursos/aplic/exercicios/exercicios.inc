<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/exercicios/exercicios.inc

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
  ARQUIVO : cursos/aplic/exercicios/exercicio.inc
  ========================================================== */


/***********************************************************************

                              Observações

- Tipo de questão (tp_questao):

  - 'O' - Questao Objetiva
  - 'D' - Questao Dissertativa

- Status de um Exercicio e/ou de uma Questao (status):

  - 'V' - Valido(a)
  - 'X' - Excluido(a)
  - 'L' - Lixeira

- Status no historico de um Exercicios:

  - 'C' - Criado

- Status no historico de uma Questao:

  - 'C' - Criada
  - 'A' - Alternativa criada
  - 'B' - Alternativa apagada



************************************************************************/




/* *********************************************************************
   RetornaExercicios - Retorna array com os exercicios válidos (V), ou seja, que não foram excluídos (X) e 
                       não estão na lixeira (L)
   Entrada: $sock - BASE DO CURSO
   Saida: array com []['cod_exercicio'] - chave primaria ou codigo do exercicio
                    []['cod_usuario'] - codigo do usuario que criou o exercicio
                    []['titulo'] - titulo do exercicio
                    []['aplicado'] - flag a qual indica se o exercicio foi aplicado (S) ou não (N)
                    []['data'] - data em que o exercicio foi criado
*/
function RetornaExercicios ($sock)
{
  $consulta="select cod_exercicio, cod_usuario, titulo, aplicado, data from Exercicios_modelo where status='V'";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   RetornaQuestoes - Retorna array com as questoes válidas (V), ou seja, que não foram excluídas (X) e 
                       não estão na lixeira (L)
   Entrada: $sock - BASE DO CURSO
   Saida: array com []['cod_questao'] - chave primaria ou codigo da questao
                    []['cod_usuario'] - codigo do usuario que criou a questao 
                    []['cod_topico'] - codigo do topico ao qual a questao pertence
                    []['titulo'] - titulo da questao
                    []['tp_questao'] - campo que indica qual o tipo da questao (Dissertativa ou Objetiva)
                    []['data'] - data em que a questao foi criada
*/
function RetornaQuestoes($sock)
{
  $consulta="select cod_questao, cod_usuario, cod_topico, titulo, tp_questao, data from Exercicios_questao where status='V'";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   RetornaNomeTopico - Retorna o nome do topico a partir de seu codigo
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo do topico que se deseja saber o nome
   Saida: array com []['topico'] - nome do topico
                    
*/
function RetornaNomeTopico($sock,$cod_topico)
{
  $consulta="select topico from Exercicios_topico where cod_topico=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha['topico']);
}

/* *********************************************************************
   CriarQuestao - Cria uma nova questao 
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - usuario que criou a questao
            $cod_curso - codigo do curso ao qual pertence a questao
            $titulo - titulo da questao a ser criada
            $tp_questao - tipo da questao
   Saida: codigo da questao criada
*/
function CriarQuestao($sock, $cod_usuario, $cod_curso, $titulo, $tp_questao)
{
  $consulta="insert into Exercicios_questao (cod_usuario, titulo, tp_questao, data, status, inicio_edicao) values (".VerificaNumeroQuery($cod_usuario).", '".VerificaStringQuery(htmlentities($titulo))."', '".VerificaStringQuery(htmlentities($tp_questao))."', ".time().", 'V', ".time().")";
  $res=Enviar($sock, $consulta);

  $cod_questao = mysql_insert_id();

  $consulta="insert into Exercicios_questao_historico values (".VerificaNumeroQuery($cod_questao).", ".VerificaNumeroQuery($cod_usuario).", ".time().", 'C')";
  $res=Enviar($sock, $consulta);

  return($cod_questao);
}

/* *********************************************************************
   RetornaTituloQuestao - Retorna o titulo da questao
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
   Saida: titulo da questao
*/
function RetornaTituloQuestao ($sock, $cod_questao)
{
  $consulta="select titulo from Exercicios_questao where cod_questao=".$cod_questao;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha['titulo']);
}

/* *********************************************************************
   RetornaResHistoricoDaQuestao - Retorna todas as ocorr�ncias do historico de uma questao
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
   Saida: array []['cod_questao']
                []['cod_usuario']
                []['data']
                []['acao']
*/
function RetornaResHistoricoDaQuestao ($sock, $cod_questao)
{
  $consulta="select * from Exercicios_questao_historico where cod_questao=".VerificaNumeroQuery($cod_questao)." order by data desc,acao desc";
  $res=Enviar($sock, $consulta);
  return($res);
}

/* *********************************************************************
   RetornaQuestao - Retorna a questao dada
   Entrada: $sock - BASE Interna
            $cod_questao - codigo da questao
   Saida: array ['cod_questao'] - codigo da questao
                ['cod_usuario'] - codigo do usuario
                ['cod_topico'] - codigo do topico ao qual a questao pertence
                ['titulo'] - titulo da questao
                ['enunciado'] - enunciado da questao
                ['nivel'] - nivel da questao
*/
function RetornaQuestao($sock,$cod_questao)
{
  $query="select cod_questao,cod_usuario,cod_topico,titulo,enunciado,nivel,tp_questao from Exercicios_questao where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha;
}

/* *********************************************************************
   RetornaAlternativas - Retorna as alternativas de uma questao
   Entrada: $sock - BASE Interna
            $cod_questao - codigo da questao
   Saida: array ['cod_alternativa'] - codigo da alternativa
                ['cod_questao'] - codigo da questao
                ['texto'] - texto da alternativa
*/
function RetornaAlternativas($sock,$cod_questao)
{
  $query="select cod_alternativa,cod_questao,texto from Exercicios_questao_alternativa where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}

/* *********************************************************************
   RetornaGabaritoQuestaoObj - Retorna o gabarito de uma questao objetiva
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
   Saida: gabarito da questao
*/
function RetornaGabaritoQuestaoObj($sock, $cod_questao)
{
  $consulta="select gabarito from Exercicios_questao_obj where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha['gabarito']);
}

/* *********************************************************************
   RetornaGabaritoQuestaoDiss - Retorna o gabarito de uma alternativa de uma questao dissertativa
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
            $cod_alternativa - codigo da alternativa
   Saida: gabarito da alternativa
*/
function RetornaGabaritoQuestaoDiss($sock, $cod_questao, $cod_alternativa)
{
  $consulta="select gabarito from Exercicios_questao_diss where cod_questao=".VerificaNumeroQuery($cod_questao)." and cod_alternativa=".VerificaNumeroQuery($cod_alternativa);
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha['gabarito']);
}

/* *********************************************************************
   RetornaTopicos - Retorna os topicos
   Entrada: $sock - BASE DO CURSO
   Saida: Saida: array ['cod_topico'] - codigo do topico
                       ['topico'] - nome do topico
                
*/
function RetornaTopicos($sock)
{
  $consulta="select * from Exercicios_topico";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}

/* *********************************************************************
   RetornaArquivosQuestao - Retorna lista de arquivos da questao
   Entrada: $cod_curso - codigo do curso
            $dirlink - link
   Saida: Array multidimensional com:
          $lista[<num>]['Caminho'] - caminho completo.
          $lista[<num>]['Diretorio'] - Diretorio do arquivo
          $lista[<num>]['Arquivo'] - Nome do arquivo
          $lista[<num>]['Status'] - Condição especial (true ou false);
          $lista[<num>]['Tamanho'] - tamanho do arquivo
          $lista[<num>]['Data'] - data da ltima modifição
*/
function RetornaArquivosQuestao($cod_curso, $dirlink)
{
  return (RetornaArrayDiretorio($dirlink));
}

/* *********************************************************************
   RetornaDiretorio - Retorna o Diret?io da tabela de diretorios
   Entrada: $sock - BASE EXTERNA
            $item - nome do item a ser buscado
   Saida: string com o diretório
*/
function RetornaDiretorio($sock,$item)
{
  $query="select diretorio from Diretorio where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha[0]);
}

/* *********************************************************************
   CriaLinkVisualizar - Cria link simbólico e retorna o link
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $cod_questao - código da questao
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
   Saida: caminho relativo
*/
function CriaLinkVisualizar($sock,$cod_curso,$cod_usuario,$cod_questao,$diretorio_arquivos,$diretorio_temp)
{
  /* Busca Arquivo a ser mostrado */
  $caminho = "";
  unset ($caminho);
  $dir=$diretorio_arquivos."/".$cod_curso."/exercicios/questao/".$cod_questao."/";

  /* Cria link simb?ico (apaga antigo, se houver)*/
  if ($cod_usuario<0)
    $cod_usuario=0;
  $dirlink=$diretorio_temp."/exercicios_".$cod_curso."_".$cod_questao."_".$cod_usuario;
  $dirlinkpath="../../diretorio/exercicios_".$cod_curso."_".$cod_questao."_".$cod_usuario;

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
   RetornaNumArquivosVisiveis - Retorna numero de arquivos visiveis ao usuario
   Entrada: $lista_arq
   Saida: numero de arquivos visiveis
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
   EditarAlternativaObjDinamic - Salva no banco as alterações feitas na alterntiva objetiva
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
   Saida: XML da função Ajax
*/
function EditarAlternativaObjDinamic($cod_curso, $cod_questao, $cod_alternativa, $textoAlt, $gabarito)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao_alternativa set texto='".VerificaStringQuery($textoAlt)."' where cod_alternativa=".VerificaNumeroQuery($cod_alternativa);
  $res=Enviar($sock, $consulta);

  $consulta="update Exercicios_questao_obj set gabarito='".VerificaStringQuery($gabarito)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  return $objResponse->getXML();
}

/* *********************************************************************
   EditarAlternativaDissDinamic - Salva no banco as alterações feitas na alterntiva
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
   Saida: XML da função Ajax
*/
function EditarAlternativaDissDinamic($cod_curso, $cod_questao, $cod_alternativa, $textoAlt)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao_alternativa set texto='".VerificaStringQuery($textoAlt)."' where cod_alternativa=".VerificaNumeroQuery($cod_alternativa);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  return $objResponse->getXML();
}

/* *********************************************************************
   CriarAlternativaDinamic - Cria uma nova alternativa 
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso ao qual pertence a alternativa
            $cod_usuario - codigo do usuario que criou a alternativa
            $cod_questao - codigo da questao a qual pertence a alternativa
            $tp_questao - tipo da questao
   Saida: XML da função Ajax
*/
function CriarAlternativaDinamic($cod_curso, $cod_usuario, $cod_questao, $tp_questao)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="insert into Exercicios_questao_alternativa (cod_questao) values (".VerificaNumeroQuery($cod_questao).")";
  $res=Enviar($sock, $consulta);

  $cod_alternativa = mysql_insert_id();

  if($tp_questao == 'O')
  {
    $gabarito = RetornaGabaritoQuestaoObj($sock, $cod_questao).'0'; 

    $consulta="update Exercicios_questao_obj set gabarito='".VerificaStringQuery($gabarito)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
    $res=Enviar($sock, $consulta);
    $cod = $cod_alternativa;
  }
  else if($tp_questao == 'D')
  {
    $consulta="insert into Exercicios_questao_diss (cod_questao,cod_alternativa) values (".VerificaNumeroQuery($cod_questao).",".VerificaNumeroQuery($cod_alternativa).")";
    $res=Enviar($sock, $consulta);
    $cod = $cod_questao.$cod_alternativa;
  }

  $consulta="insert into Exercicios_questao_historico values (".VerificaNumeroQuery($cod_questao).", ".VerificaNumeroQuery($cod_usuario).", ".time().", 'A')";
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  $objResponse->addScript("AdicionarAlternativa(".$cod.");");

  sleep(1);

  return $objResponse->getXML();
}

/* *********************************************************************
   ApagarAlternativaDinamic - Apaga uma alternativa 
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso ao qual pertence a alternativa
            $cod_usuario - codigo do usuario que apagou a alternativa
            $cod_questao - codigo da questao a qual pertence a alternativa
            $cod_alternativa - codigo da alternativa a ser apagada
            $tp_questao - tipo da questao
   Saida: XML da função Ajax
*/
function ApagarAlternativaDinamic($cod_curso, $cod_usuario, $cod_questao, $cod_alternativa, $gabarito, $tp_questao)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="delete from Exercicios_questao_alternativa where cod_alternativa=".VerificaNumeroQuery($cod_alternativa);
  $res=Enviar($sock, $consulta);

  if($tp_questao == 'O')
  {
    $consulta="update Exercicios_questao_obj set gabarito='".VerificaStringQuery($gabarito)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
    $res=Enviar($sock, $consulta);
  }
  else if($tp_questao == 'D')
  {
    $consulta="delete from Exercicios_questao_diss where cod_questao=".VerificaNumeroQuery($cod_questao)." and cod_alternativa=".VerificaNumeroQuery($cod_alternativa);
    $res=Enviar($sock, $consulta);
  }

  Desconectar($sock);

  return $objResponse->getXML();
}

/* *********************************************************************
   AtualizarNivelDinamic - Atualiza o nivel da questao
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
            $nivel - novo nivel da questao
   Saida: XML da função Ajax
*/
function AtualizarNivelDinamic($cod_curso, $cod_questao, $nivel)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao set nivel='".VerificaStringQuery($nivel)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  return $objResponse->getXML();
}

/* *********************************************************************
   AtualizarTopicoDinamic - Atualiza o topico da questao
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
            $cod_topico - codigo do novo topico da questao
   Saida: XML da função Ajax
*/
function AtualizarTopicoDinamic($cod_curso, $cod_questao, $cod_topico)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao set cod_topico='".VerificaNumeroQuery($cod_topico)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  return $objResponse->getXML();
}

/* *********************************************************************
   CriaNovoTopicoDinamic - Cria um novo topico
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
            $novo_topico - nome do topico
   Saida: XML da função Ajax
*/
function CriaNovoTopicoDinamic($cod_curso, $cod_questao, $novo_topico)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="insert into Exercicios_topico (topico) values ('".VerificaStringQuery($novo_topico)."')";
  $res=Enviar($sock, $consulta);

  $cod_topico = mysql_insert_id();

  Desconectar($sock);

  $objResponse->addScript("AdicionaNovoTopico(".$cod_topico.",'".$novo_topico."');");

  return $objResponse->getXML();
}

/* *********************************************************************
   EditarTituloQuestaoDinamic - Edita o título da questao
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_questao - codigo da questao
            $novo_nome - novo título da questao
            $cod_usuario - código do usuário que está utilizando
            $texto - mensagem de feedback
   Saida: XML da função Ajax
*/
function EditarTituloQuestaoDinamic($cod_curso, $cod_questao, $novo_nome, $cod_usuario, $texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $sock=Conectar($cod_curso);
  $consulta="update Exercicios_questao set titulo='".VerificaStringQuery(htmlentities($novo_nome))."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  // Imprime no div valores do formul?io
  $objResponse->addAssign("tit_".$cod_questao, "innerHTML", htmlentities($novo_nome));
  $objResponse->addEvent("renomear_".$cod_questao, "onclick", "AlteraTitulo('".$cod_questao."');");

  //AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  //$objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   EditarEnunciadoDinamic - Edita o enunciado da questao
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_questao - codigo da questao
            $novo_nome - novo enunciado da questao
            $cod_usuario - código do usuário que está utilizando
            $texto - mensagem de feedback
   Saida: XML da função Ajax
*/
function EditarEnunciadoDinamic($cod_curso, $cod_questao, $novo_nome, $cod_usuario, $texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $novo_nome=ConverteAspas2BarraAspas($novo_nome);
  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao set enunciado='".VerificaStringQuery(trim(VerificaTexto($novo_nome)))."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  //AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  //$objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  // Imprime no div valores do formulário
  $objResponse->addAssign("text_".$cod_questao, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas(VerificaTexto($novo_nome))), true));


  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   EditarGabaritoDinamic - Edita o gabarito de uma alternativa
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_questao - codigo da questao
            $cod_alternativa - codigo da alternativa
            $novo_nome - novo gabarito da alternativa
            $cod_usuario - código do usuário que está utilizando
            $texto - mensagem de feedback
   Saida: XML da função Ajax
*/
function EditarGabaritoDinamic($cod_curso, $cod_questao, $cod_alternativa, $novo_nome, $cod_usuario, $texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $novo_nome=ConverteAspas2BarraAspas($novo_nome);
  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao_diss set gabarito='".VerificaStringQuery(trim(VerificaTexto($novo_nome)))."' where cod_questao=".VerificaNumeroQuery($cod_questao)." and cod_alternativa=".VerificaNumeroQuery($cod_alternativa);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  //AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  //$objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  // Imprime no div valores do formulário
  $objResponse->addAssign("text_".$cod_questao.$cod_alternativa, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas(VerificaTexto($novo_nome))), true));


  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   AnexarArquivosDinamic - Anexa arquivos a questao
   Entrada: $dados_form - dados do formulario para anexar arquivos
            $nome_arquivo - nome do arquivo
   Saida: XML da função Ajax
*/
function AnexarArquivosDinamic($nome_arquivo)
{
    $objResponse = new xajaxResponse('ISO-8859-1');

    $objResponse->addAlert("formData: " . print_r($dados_form, true));
    $objResponse->addAlert("_FILES: " . print_r($_FILES, true));

    //AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 1);

    // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
    return $objResponse->getXML();
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
function ExcluiArquivoDinamic($numero, $arq, $cod_curso, $cod_item, $cod_usuario, $texto){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $arq=htmlspecialchars_decode($arq);

  RemoveDiretorio($arq);

  $objResponse->addRemove('arq_'.$numero);

  //AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  //$objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  return $objResponse->getXML();
}
