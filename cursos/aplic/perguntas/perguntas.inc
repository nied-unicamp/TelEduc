<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/perguntas.inc

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
  ARQUIVO : cursos/aplic/perguntas/perguntas.inc
  ========================================================== */

/* ******************************************************
  Fun�ao EliminaEspacos - Elimina espacos no come�o e no
    final da frase.
  Retorna a frase sem os espacos no come�o e no final dela.
  Entradas: $frase - frase
  Saida:   frase sem os espacos no come�o e no final dela.
*/

function EliminaEspacos($frase)
{
  return(trim($frase));
}


/* *********************************************************
  Fun�ao ExisteAssunto - Verifica se o assunto existe.
    Retorna true se existir, do contr�rio, false .
  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto a ser verificado.
  Saida:    true se o assunto existir, do contr�rio, false.
*/

function ExisteAssunto($sock, $cod_assunto)
{
  $query = "select nome from Pergunta_assuntos where cod_assunto = ".$cod_assunto;

  $res = Enviar($sock, $query);

  /* Se Retornar algum valor entao retorna true. */
  if (RetornaNumLinhas($res) != 0)
    return(true);
  else
    return(false);
}
// denis
/*
function AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, $acao){

  $objResponse = new xajaxResponse();

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
  
  return $objResponse;
}
*/
function EditarTexto($tabela, $cod_curso, $cod_item, $novo_nome, $cod_usuario)
{

  $objResponse = new xajaxResponse();

  $novo_nome=ConverteAspas2BarraAspas($novo_nome);

  $sock=Conectar($cod_curso);
  
  $consulta="update ".$tabela."_itens set resposta='".trim($novo_nome)."' where cod_pergunta=".$cod_item;
  $res=Enviar($sock, $consulta);
  
  // Atualiza Ferramentas_nova
  AtualizaFerramentasNova($sock, 6, 'T');
    
  Desconectar($sock);

//  AcabaEdicaoDinamic($tabela, $cod_curso, $cod_item, $cod_usuario, 1);

  // Imprime no div valores do formulário
//  $objResponse->assign("tr_".$cod_item, "className", "novoitem");
  $objResponse->assign("text_".$cod_item, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas($novo_nome)), true));
//  $objResponde->alert(".$consulta.");
  //  $objResponse->assign("data_".$cod_item, "innerHTML", UnixTime2DataHora(time()));
  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* *********************************************************
  Fun�ao AlteraDados - Altera dinamicamente os campos do assunto.
  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto a ser verificado.
  Saida:    true se o assunto existir, do contr�rio, false.
*/
function AlteraDadosAssuntoDinamic($id, $valor, $cod_assunto){
	$campo = ($id == "Assunto") ? "nome" : "descricao";
	
	$sock = Conectar($_GET['cod_curso']);
	$query = "update Pergunta_assuntos set $campo = '".VerificaStringQuery($valor);
	$query .= "', data=".time()." where cod_assunto = ".$cod_assunto;
	Enviar($sock,$query);
	Desconectar($sock);
	
	$sock = MudarDB($sock, "");
	$texto = RetornaFrase($sock, ($campo == "nome") ? 81 : 82 , 6);
	Desconectar($sock);
	
	$objResponse = new xajaxResponse();
	$objResponse->call("mostraFeedback", $texto , "true");
	return $objResponse;
}

/* *************************************************************
  Fun�ao RetornaAssunto - Retorna um array contendo o nome e
    a descri�ao do assunto.
  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto.
  Saida:   Array com: ['nome'] - nome do assunto,
                      ['descricao'] - descri�ao do assunto.
*/

function RetornaAssunto($sock, $cod_assunto)
{
  $query = "select nome, descricao, data, cod_assunto, cod_assunto_pai from Pergunta_assuntos where cod_assunto = ".$cod_assunto;
  $res = Enviar($sock, $query);

  $tupla = RetornaLinha($res);
  return($tupla);
}


/**************************************************************
  Fun�ao ListaAssuntos - Retorna uma lista contendo todos os
    assuntos do Perguntas Freq�entes.
  Retorna uma lista com o nome, codigo do assunto e data de
    inser�ao.
  Entradas: $sock - sock de conexao.
            $cod_assunto_pai - codigo do assunto pai: 1 - Raiz
                                                      2 - Lixeira
                                                      N - Outro
  Saida:   Array com: ['cod_assunto'] - codigo do assunto,
                      ['nome'] - nome do assunto.
**************************************************************/

function ListaAssuntos($sock, $cod_assunto_pai)
{
  $query  = "select nome, cod_assunto from Pergunta_assuntos ";
  $query .= "where cod_assunto_pai = ".$cod_assunto_pai." order by nome";
  $res = Enviar($sock, $query);
  $tuplas = RetornaArrayLinhas($res);
  return($tuplas);
}


/* *********************************************************************************
  Fun�ao RetornaLinkCaminhoAssuntoRecur - Retorna uma string contendo links
    para o assunto atual e os assuntos anteriores a ele.
  � chamada pela fun�ao RetornaLinkCaminho a qual ira concatenar com
    o assunto atual em negrito.
  Utilizado na exibi�ao de assuntos e perguntas.
  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto: 1 - Raiz
                                              2 - Lixeira
                                              N - Outro
            $cod_curso - codigo do curso,
            $pagprinc - documento da pagina principal: "perguntas" ou
              "exibir_todas".
  Saida: String com: <a>Assunto1</a> >> <a>Assunto2</a> >> ... >> <a>AssuntoAtual</a>
*/

function RetornaLinkCaminhoAssuntoRecur($sock, $cod_assunto, $cod_curso, $pagprinc)
{
  global $lista_frases_geral;

  $query = "select cod_assunto_pai, nome from Pergunta_assuntos where cod_assunto = ".$cod_assunto;
  $res = Enviar($sock, $query);

  if (RetornaNumLinhas($res) != 0)
  {
    $linha = RetornaLinha($res);
    $codigo_assunto_pai = $linha['cod_assunto_pai'];

    /* Cria um link para o assunto atual com o nome truncado */
    /* se acima de 20 caracteres.                            */
    $nome = "<a href='".$pagprinc.".php?".RetornaSessionID();
    $nome .= "&cod_curso=".$cod_curso."&cod_assunto_pai=".$cod_assunto;
    $nome .= "&".time()."'>";
    
    /* Se o cod_assunto 1(Pasta Raiz) ou 2(Lixeira), pega da lista_frases para traducao. */
    if($cod_assunto == 1) {
        /* 37 - Raiz(Ger) */
        $nome .= RetornaFraseDaLista($lista_frases_geral,37)."</a>";
    } else if($cod_assunto == 2) {
        /* 16 - Lixeira(Ger) */
        $nome .= RetornaFraseDaLista($lista_frases_geral,16)."</a>";
    } else {
        $nome .= TruncaString($linha['nome'], 20)."</a>";
    }

    if (($cod_assunto == 1) || ($cod_assunto == 2))
    /* Se estiver na Raiz ou Lixeira entao retorna o nome. */
      return($nome);
    else
    /* Se NAO estiver na Raiz ou Lixeira entao retorna o caminho truncado. */
    {
      $anterior = RetornaLinkCaminhoAssuntoRecur($sock, $codigo_assunto_pai, $cod_curso, $pagprinc);
      return($anterior." >> ".$nome);
    }
  }
}


/* **************************************************************************************
  Fun�ao RetornaLinkCaminhoAssunto - Retorna uma string contendo links
    para os assuntos anteriores ao atual e o assunto atual em
    negrito.
  Chama a fun�ao RetornaLinkCaminhoRecur a qual ira gerar os
    links.
  Utilizado na exibi�ao de assuntos e perguntas.
  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto: 1 - Raiz
                                              2 - Lixeira
                                              N - Outro
            $cod_curso - codigo do curso,
            $pagprinc - documento da pagina principal: "perguntas" ou
              "exibir_todas".
  Saida: String com: <a>Assunto1</a> >> <a>Assunto2</a> >> ... >> <b>AssuntoAtual</b>
*/

function RetornaLinkCaminhoAssunto($sock, $cod_assunto, $cod_curso, $pagprinc)
{
  global $lista_frases_geral;
    
  $query = "select cod_assunto_pai, nome from Pergunta_assuntos where cod_assunto = ".$cod_assunto;
  $res = Enviar($sock, $query);

  /* Se possui algum assunto-pai entao: */
  $linha = RetornaLinha($res);
  $codigo_assunto_pai = $linha['cod_assunto_pai'];

  /* Se o cod_assunto 1(Pasta Raiz) ou 2(Lixeira), pega da lista_frases para traducao. */
  if($cod_assunto == 1) {
    /* 37 - Raiz(Ger) */
    $nome = "<b>".RetornaFraseDaLista($lista_frases_geral,37)."</b>";
  } else if($cod_assunto == 2) {
    /* 16 - Lixeira(Ger) */
    $nome = "<b>".RetornaFraseDaLista($lista_frases_geral,16)."</b>";
  } else {
    $nome = "<b>".$linha['nome']."</b>";
  }
  
  if ($codigo_assunto_pai != "")
  {
    $caminho_links = RetornaLinkCaminhoAssuntoRecur($sock, $codigo_assunto_pai, $cod_curso, $pagprinc);
    return($caminho_links." >> ".$nome);
  }
  else
    return($nome);
}


/* ********************************************************************
  Fun�ao RetornaCaminhoAssunto - Retorna uma string contendo o caminho
    completo no qual o assunto esta localizado.
  Utilizado na inser�ao de assuntos e perguntas.
  Entradas: $sock - sock de conexao.
            $cod_assunto - codigo do assunto: 1 - Raiz
                                              2 - Lixeira
                                              N - Outro
  Saida:   String com: Assunto1 - Assunto2 - ... - AssuntoAtual.
*/

function RetornaCaminhoAssunto($sock, $cod_assunto)
{
  $query = "select cod_assunto_pai, nome from Pergunta_assuntos where cod_assunto = ".$cod_assunto;
  $res = Enviar($sock, $query);

  if (RetornaNumLinhas($res) != 0)
  {
    $linha = RetornaLinha($res);
    $codigo_assunto_pai = $linha['cod_assunto_pai'];
    $nome = $linha['nome'];

    if (($cod_assunto == 1) || ($cod_assunto == 2))
      return($nome);
    else
    {
      $anterior = RetornaCaminhoAssunto($sock, $codigo_assunto_pai);
      return($anterior." >> ".$nome);
    }
  }
}


/* *************************************************************************
  Fun�ao EstruturaDeAssuntosRecur - cria um link para a fun�ao javascript
    Abrir(id).
  Se o $cod_assunto_pai (codigo do assunto que estah sendo processada) for
    igual ao $cod_assunto_atual (codigo do assunto em que o usuario estah
    navegando), entao escreve apenas uma string com o nome do assunto e
    chama recursivamente a fun�ao EstruturaDeAssuntosRecur.

  Entradas: $sock - sock de conexao,
            $cod_assunto_pai - codigo do assunto que estah sendo processado,
            $cod_assunto_atual - codigo do assunto em o usuario se
              encontra,
            $nivel - qual nivel estah sendo processado, para indenta�ao com
              espa�os da string.
  Saida:    Retorna uma string em HTML com a imagem das pastas e os links
*/

function EstruturaDeAssuntosRecur($sock, $cod_assunto_pai, $cod_assunto_atual, $nivel)
{
  $query1 = "select nome from Pergunta_assuntos ";
  $query1 .= "where cod_assunto = ".$cod_assunto_pai." order by nome";
  $res1 = Enviar($sock, $query1);
  $tupla = RetornaLinha($res1);

  $query2 = "select nome, cod_assunto from Pergunta_assuntos ";
  $query2 .= "where cod_assunto_pai = ".$cod_assunto_pai." order by nome";
  $res2 = Enviar($sock, $query2);
  $tuplas = RetornaArrayLinhas($res2);
  $total = count($tuplas);

  $caminho = "";
  $nivel++;

  for ($j = 0; $j < $nivel; $j++)
    $caminho .= "&nbsp;&nbsp;&nbsp;&nbsp;";

  if ($cod_assunto_pai == $cod_assunto_atual)
  {
    $caminho .= "<img src=\"../imgs/pasta.gif\" border=0>\n";
    $caminho .= "<b><font class=text>".TruncaString($tupla['nome'], 20)."</font></b><br>\n";
  }
  else
  {
    $caminho .= "<a class=text href=# onClick='Abrir(".$cod_assunto_pai.");return(false);'><img src=\"../imgs/pasta.gif\" border=0></a>\n";

    $caminho .= "<a class=text href=# onClick='Abrir";
    $caminho .= "(".$cod_assunto_pai.");return(false);'>";

    $caminho .= TruncaString($tupla['nome'], 20)."</a><br>\n";
  }

  /* Para cada assunto contido nos sub-assuntos cria os links.        */
  for ($i = 0; $i < $total; $i++)
    $caminho .= EstruturaDeAssuntosRecur($sock, $tuplas[$i]['cod_assunto'], $cod_assunto_atual, $nivel);

  return($caminho);
}


/* *************************************************************************
  Fun�ao EstruturaDeAssuntos - cria um link para a fun�ao javascript
    Abrir(id).
  Se o $cod_assunto_pai (codigo do assunto em que o usuario estah
    navegando) for igual a 1 (assunto-raiz), entao escreve apenas uma
    string com o nome do assunto-raiz e chama a fun�ao
    EstruturaDeAssuntosRecur, que se encarregar� de criar os links dos
    sub-assuntos. Se o usuario for 'Formador', entao exibe a lixeira na
    estrutura.

  Entradas: $sock - sock de conexao,
            $cod_assunto_atual - codigo do assunto em o usuario se
              encontra,
            $eformador - booleano que sinaliza se o usuario eh ou nao
              formador. Se for, exibe a lixeira na estrutura.
  Saida:    Retorna uma string em HTML com a imagem das pastas e os links
*/

function EstruturaDeAssuntos($sock, $cod_assunto_atual, $eformador)
{
  global $lista_frases_geral;
  
  $query1 = "select nome from Pergunta_assuntos ";
  $query1 .= "where cod_assunto = 1";
  $res1 = Enviar($sock, $query1);
  $tupla = RetornaLinha($res1);

  $query2 = "select nome, cod_assunto from Pergunta_assuntos ";
  $query2 .= "where cod_assunto_pai = 1 order by nome";
  $res2 = Enviar($sock, $query2);

  $tuplas = RetornaArrayLinhas($res2);
  $total = count($tuplas);

  $nivel = 0;

  if ($cod_assunto_atual == 1)
  /* Se estiver no assunto-raiz entao, cria uma apenas uma string com o */
  /* nome do assunto-raiz e chama a fun�ao EstruturaMoverAssuntoPropr.  */
  {
      $caminho = "<img src=\"../imgs/pasta.gif\" border=0>\n";
      /* 37 - Raiz(Ger) */
      $caminho .= RetornaFraseDaLista($lista_frases_geral,37)."<br>\n";
  }
  else
  /* Se NAO estiver no assunto-raiz entao, cria um link para o       */
  /* assunto-raiz.                                                   */
  {
    $caminho = "<img src=\"../imgs/pasta.gif\" border=0><a class=\"link\" href=# onClick='Abrir(1);return(false);'/>\n";
    $caminho .= "<a class=\"link\" href=# onClick='Abrir";
    $caminho .= "(1);return(false);'>";
    /* 37 - Raiz(Ger) */
    $caminho .= RetornaFraseDaLista($lista_frases_geral,37)."</a><br>\n";
  }

  /* Para cada assunto contido nos sub-assuntos cria os links.        */
  for ($i = 0; $i < $total; $i++)
    $caminho .= EstruturaDeAssuntosRecur($sock, $tuplas[$i]['cod_assunto'], $cod_assunto_atual, $nivel);


  if ($eformador)
  /* Se o usuario for Formador entao exibe a Lixeira.                */
  {

    $query = "select nome from Pergunta_assuntos ";
    $query .= "where cod_assunto = 2";
    $res = Enviar($sock, $query);
    $tupla = RetornaLinha($res);

    if ($cod_assunto_atual == 2)
    {
      $caminho .= "<img src=\"../imgs/trash.gif\" border=0>\n";
      /* Se estiver na lixeira entao, cria uma apenas uma string com o */
      /* nome da lixeira.                                              */
      /* 16 - Lixeira(Ger) */
      $caminho .= RetornaFraseDaLista($lista_frases_geral,16)."<br>\n";
    }
    else
    /* Se NAO estiver na lixeira entao, cria um link para a lixeira. */
    {
      $caminho .= "<a class=\"link\" href=# onClick='Abrir(2);return(false);'><img src=\"../imgs/trash.gif\" border=0></a>\n";
      $caminho .= "<a class=\"link\" href=# onClick='Abrir";
      $caminho .= "(2);return(false);'>";
      /* 16 - Lixeira(Ger) */
      $caminho .= RetornaFraseDaLista($lista_frases_geral,16)."<br>\n";
    }
  }

  return($caminho);
}


/* *************************************************************************
  Fun�ao EstruturaMoverAssuntoProprRecur - cria um link para a fun�ao
    javascript MoverAssunto(origem, destino, proprietario) onde o parametro
    'proprietario' e' mantido constante, ou seja todos os links para a
    fun�ao MoverAssunto dos sub-assuntos terao o parametro 'proprietario'
    igual a $cod_assunto_propr.

  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto,
            $cod_assunto_propr - codigo do assunto proprietario,
            $nivel - nivel.
  Saida:    Retorna uma string em HTML com a imagem das pastas e os links
*/

function EstruturaMoverAssuntoProprRecur($sock, $cod_assunto, $cod_assunto_propr, $nivel)
{
  /* Obtem o nome do assunto atual.                        */
  $query1 = "select nome from Pergunta_assuntos where ";
  $query1 .= "cod_assunto = ".$cod_assunto;

  $res1 = Enviar($sock, $query1);
  $tupla = RetornaLinha($res1);

  /* Obtem os nome e cod_assunto dos assuntos-filho.     */
  $query2 = "select nome, cod_assunto from Pergunta_assuntos where ";
  $query2 .= "cod_assunto_pai = ".$cod_assunto." order by nome";

  $res2 = Enviar($sock, $query2);
  $tuplas = RetornaArrayLinhas($res2);
  $total = count($tuplas);

  $caminho = "";
  /* Incrementa o nivel.                                  */
  $nivel++;

  /* Alinha os assuntos com espa�os de acordo com o nivel em que se */
  /* encontra. */
  for ($j = 0; $j < $nivel; $j++)
    $caminho .= "&nbsp;&nbsp;&nbsp;&nbsp;";
    
  $caminho .= "<a class=text href=# onClick='MoverAssunto";
  $caminho .= "(selected_item, ".$cod_assunto.", ".$cod_assunto_propr;
  $caminho .= ");return(false);'>";
  $caminho .= "<img src=\"../imgs/pasta.gif\" border=0></a>\n";
  /* Mantem o parametro 'proprietario' da fun�ao javascript         */
  /* MoverAssunto(origem, destino, proprietario) constante em       */
  /* $cod_assunto_propr                                             */
  $caminho .= "<a class=text href=# onClick='MoverAssunto";
  $caminho .= "(selected_item, ".$cod_assunto.", ".$cod_assunto_propr;
  $caminho .= ");return(false);'>";
  $caminho .= TruncaString($tupla['nome'], 20)."</a><br>\n";

  /* Para cada assunto contido nos sub-assuntos cria os links.      */
  for ($i = 0; $i < $total; $i++)
    $caminho .= EstruturaMoverAssuntoProprRecur($sock, $tuplas[$i]['cod_assunto'], $cod_assunto_propr, $nivel);

  return($caminho);
}

/* *************************************************************************
  Fun�ao EstruturaMoverAssuntoProprRecur - cria um link para a fun�ao
    javascript MoverAssunto(origem, destino, proprietario) onde o parametro
    'proprietario' e' mantido constante, ou seja todos os links para a
    fun�ao MoverAssunto dos sub-assuntos terao o parametro 'proprietario'
    igual a $cod_assunto_propr.

  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto,
            $cod_assunto_propr - codigo do assunto proprietario,
            $nivel - nivel.
  Saida:    Retorna uma string em HTML com a imagem das pastas e os links
*/

function EstruturaRecuperarAssuntoProprRecur($sock, $cod_assunto, $cod_assunto_propr, $nivel)
{
  /* Obtem o nome do assunto atual.                        */
  $query1 = "select nome from Pergunta_assuntos where ";
  $query1 .= "cod_assunto = ".$cod_assunto;

  $res1 = Enviar($sock, $query1);
  $tupla = RetornaLinha($res1);

  /* Obtem os nome e cod_assunto dos assuntos-filho.     */
  $query2 = "select nome, cod_assunto from Pergunta_assuntos where ";
  $query2 .= "cod_assunto_pai = ".$cod_assunto." order by nome";

  $res2 = Enviar($sock, $query2);
  $tuplas = RetornaArrayLinhas($res2);
  $total = count($tuplas);

  $caminho = "";
  /* Incrementa o nivel.                                  */
  $nivel++;

  /* Alinha os assuntos com espa�os de acordo com o nivel em que se */
  /* encontra. */
  for ($j = 0; $j < $nivel; $j++)
    $caminho .= "&nbsp;&nbsp;&nbsp;&nbsp;";
    
  $caminho .= "<a class=text href=# onClick='RecuperarPergunta";
  $caminho .= "(selected_item, ".$cod_assunto.", ".$cod_assunto_propr;
  $caminho .= ");return(false);'>";
  $caminho .= "<img src=\"../imgs/pasta.gif\" border=0></a>\n";
  /* Mantem o parametro 'proprietario' da fun�ao javascript         */
  /* MoverAssunto(origem, destino, proprietario) constante em       */
  /* $cod_assunto_propr                                             */
  $caminho .= "<a class=text href=# onClick='RecuperarPergunta";
  $caminho .= "(selected_item, ".$cod_assunto.", ".$cod_assunto_propr;
  $caminho .= ");return(false);'>";
  $caminho .= TruncaString($tupla['nome'], 20)."</a><br>\n";

  /* Para cada assunto contido nos sub-assuntos cria os links.      */
  for ($i = 0; $i < $total; $i++)
    $caminho .= EstruturaRecuperarAssuntoProprRecur($sock, $tuplas[$i]['cod_assunto'], $cod_assunto_propr, $nivel);

  return($caminho);
}


/* *************************************************************************
  Fun�ao EstruturaMoverAssuntoPropr - cria um link para a fun�ao
    javascript MoverAssunto(origem, destino, proprietario) onde o parametro
    'proprietario' e' mantido constante.
  Chama a fun�ao recursiva EstruturaMoverAssuntoProprRecur, o qual
    manterah todos os links para a fun�ao MoverAssunto dos sub-assuntos
    com o parametro 'proprietario' igual a $cod_assunto.

  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto,
            $nivel - nivel.
  Saida:    Retorna uma string em HTML com a imagem das pastas e os links
*/

function EstruturaMoverAssuntoPropr($sock, $cod_assunto, $nivel)
{
  /* Obtem o nome do assunto atual.                        */
  $query1 = "select nome from Pergunta_assuntos where ";
  $query1 .= "cod_assunto = ".$cod_assunto;

  $res1 = Enviar($sock, $query1);
  $tupla = RetornaLinha($res1);

  /* Obtem os nome e cod_assunto dos sub-assuntos-filho.     */
  $query2 = "select nome, cod_assunto from Pergunta_assuntos where ";
  $query2 .= "cod_assunto_pai = ".$cod_assunto." order by nome";

  $res2 = Enviar($sock, $query2);
  $tuplas = RetornaArrayLinhas($res2);
  $total = count($tuplas);

  $caminho = "";
  /* Incrementa o nivel.                                  */
  $nivel++;

  /* Alinha os assuntos com espa�os de acordo com o nivel em que se */
  /* encontra. */
  for ($j = 0; $j < $nivel; $j++)
    $caminho .= "&nbsp;&nbsp;&nbsp;&nbsp;";

  $caminho .= "<a class=link href=# onClick='MoverAssunto";
  $caminho .= "(selected_item, ".$cod_assunto.", ".$cod_assunto;
  $caminho .= ");return(false);'>";
  $caminho .= "<img src=\"../imgs/pasta.gif\" border=0></A>\n";
  /* Mantem o parametro 'proprietario' da fun�ao javascript         */
  /* MoverAssunto(origem, destino, proprietario) constante em       */
  /* $cod_assunto                                                   */
  $caminho .= "<a class=link href=# onClick='MoverAssunto";
  $caminho .= "(selected_item, ".$cod_assunto.", ".$cod_assunto;
  $caminho .= ");return(false);'>";
  $caminho .= TruncaString($tupla['nome'], 20)."</a><br>\n";

  /* Para cada assunto contido nos sub-assuntos cria os links.      */
  for ($i = 0; $i < $total; $i++)
    $caminho .= EstruturaMoverAssuntoProprRecur($sock, $tuplas[$i]['cod_assunto'], $cod_assunto, $nivel);

  return($caminho);
}

/* *************************************************************************
  Fun�ao EstruturaMoverAssuntoPropr - cria um link para a fun�ao
    javascript MoverAssunto(origem, destino, proprietario) onde o parametro
    'proprietario' e' mantido constante.
  Chama a fun�ao recursiva EstruturaMoverAssuntoProprRecur, o qual
    manterah todos os links para a fun�ao MoverAssunto dos sub-assuntos
    com o parametro 'proprietario' igual a $cod_assunto.

  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto,
            $nivel - nivel.
  Saida:    Retorna uma string em HTML com a imagem das pastas e os links
*/

function EstruturaRecuperarAssuntoPropr($sock, $cod_assunto, $nivel)
{
  /* Obtem o nome do assunto atual.                        */
  $query1 = "select nome from Pergunta_assuntos where ";
  $query1 .= "cod_assunto = ".$cod_assunto;

  $res1 = Enviar($sock, $query1);
  $tupla = RetornaLinha($res1);

  /* Obtem os nome e cod_assunto dos sub-assuntos-filho.     */
  $query2 = "select nome, cod_assunto from Pergunta_assuntos where ";
  $query2 .= "cod_assunto_pai = ".$cod_assunto." order by nome";

  $res2 = Enviar($sock, $query2);
  $tuplas = RetornaArrayLinhas($res2);
  $total = count($tuplas);

  $caminho = "";
  /* Incrementa o nivel.                                  */
  $nivel++;

  /* Alinha os assuntos com espa�os de acordo com o nivel em que se */
  /* encontra. */
  for ($j = 0; $j < $nivel; $j++)
    $caminho .= "&nbsp;&nbsp;&nbsp;&nbsp;";

  $caminho .= "<a class=link href=# onClick='RecuperarPergunta";
  $caminho .= "(selected_item, ".$cod_assunto.", ".$cod_assunto;
  $caminho .= ");return(false);'>";
  $caminho .= "<img src=\"../imgs/pasta.gif\" border=0></A>\n";
  /* Mantem o parametro 'proprietario' da fun�ao javascript         */
  /* MoverAssunto(origem, destino, proprietario) constante em       */
  /* $cod_assunto                                                   */
  $caminho .= "<a class=link href=# onClick='RecuperarPergunta";
  $caminho .= "(selected_item, ".$cod_assunto.", ".$cod_assunto;
  $caminho .= ");return(false);'>";
  $caminho .= TruncaString($tupla['nome'], 20)."</a><br>\n";

  /* Para cada assunto contido nos sub-assuntos cria os links.      */
  for ($i = 0; $i < $total; $i++)
    $caminho .= EstruturaRecuperarAssuntoProprRecur($sock, $tuplas[$i]['cod_assunto'], $cod_assunto, $nivel);

  return($caminho);
}


/* *************************************************************************
  Fun�ao EstruturaMoverAssuntoRecur - cria um link para a fun�ao
    javascript MoverAssunto(origem, destino, proprietario).
  Se o $cod_assunto_pai (codigo do assunto que estah sendo processada) for
    igual ao $cod_assunto (codigo do assunto em que o usuario estah
    navegando), entao escreve apenas uma string com o nome do assunto e
    chama a fun�ao EstruturaMoverAssuntoPropr, que se encarregar� em manter
    constante o parametro 'proprietario' da fun�ao javascript
    MoverAssunto(origem, destino, proprietario).
  Se o $cod_assunto_pai NAO for igual ao $cod_assunto, entao chama-se
    recursivamente.

  Entradas: $sock - sock de conexao,
            $cod_assunto_pai - codigo do assunto,
            $cod_assunto - codigo do assunto,
            $nivel - nivel.
  Saida:    Retorna uma string em HTML com a imagem das pastas e os links
*/

function EstruturaMoverAssuntoRecur($sock, $cod_assunto_pai, $cod_assunto, $nivel)
{
  /* Obtem o nome do assunto atual.                        */
  $query1 = "select nome from Pergunta_assuntos where ";
  $query1 .= "cod_assunto = ".$cod_assunto_pai;

  $res1 = Enviar($sock, $query1);
  $tupla = RetornaLinha($res1);

  /* Obtem os nome e cod_assunto dos assuntos-filho.      */
  $query2 = "select nome, cod_assunto from Pergunta_assuntos where ";
  $query2 .= "cod_assunto_pai = ".$cod_assunto_pai." order by nome";

  $res2 = Enviar($sock, $query2);
  $tuplas = RetornaArrayLinhas($res2);
  $total = count($tuplas);

  $caminho = "";

  /* Incrementa o nivel.                                            */
  $nivel++;

  /* Alinha os assuntos com espa�os de acordo com o nivel em que se */
  /* encontra. */
  for ($j = 0; $j < $nivel; $j++)
    $caminho .= "&nbsp;&nbsp;&nbsp;&nbsp;";

  if ($cod_assunto == $cod_assunto_pai)
  /* Se o assunto processado for o mesmo em que o usuario se encontra */
  /* cria uma string com o nome do assunto.                           */
  {
    $caminho .= "<img src=\"../imgs/pasta.gif\" border=0>\n";
    $caminho .= TruncaString($tupla['nome'], 20)."<br>\n";

    /* Para cada assunto contido nos sub-assuntos cria os links.      */
    for ($i = 0; $i < $total; $i++)
    {
      $caminho .= EstruturaMoverAssuntoPropr($sock, $tuplas[$i]['cod_assunto'], $nivel);
    }
  }
  else
  /* Se o assunto processado NAO for o mesmo em que o usuario se      */
  /* encontra, entao cria um link com o nome do assunto.              */
  {
    $caminho .= "<a class=link href=# onClick='MoverAssunto";
    $caminho .= "(selected_item, ".$cod_assunto_pai.", ".$cod_assunto_pai.");return(false);'>";
    $caminho .= "<img src=\"../imgs/pasta.gif\" border=0></a>\n";
    /* Mantem o parametro 'proprietario' da fun�ao javascript         */
    /* MoverAssunto(origem, destino, proprietario) constante em       */
    /* $cod_assunto_pai                                               */
    $caminho .= "<a class=link href=# onClick='MoverAssunto";
    $caminho .= "(selected_item, ".$cod_assunto_pai.", ".$cod_assunto_pai.");return(false);'>";
    $caminho .= TruncaString($tupla['nome'], 20)."</a><br>\n";

    /* Para cada assunto contido nos sub-assuntos cria os links.      */
    for ($i = 0; $i < $total; $i++)
    {
      $caminho .= EstruturaMoverAssuntoRecur($sock, $tuplas[$i]['cod_assunto'], $cod_assunto, $nivel);
    }
  }
  return($caminho);
}


/* *************************************************************************
  Fun�ao EstruturaMoverAssunto - cria um link para a fun�ao javascript
    MoverAssunto(origem, destino, proprietario).
  Se o $cod_assunto_pai (codigo do assunto em que o usuario estah
    navegando) for igual a 1 (assunto-raiz), entao escreve apenas uma
    string com o nome do assunto-raiz e chama a fun�ao
    EstruturaMoverAssuntoPropr, que se encarregar� em manter constante o
    parametro 'proprietario' da fun�ao javascript
    MoverAssunto(origem, destino, proprietario) com o cod_assunto dos
    sub-assuntos do proximo nivel.
  Se o $cod_assunto_pai NAO for igual ao $cod_assunto, entao chama-se
    recursivamente.

  Entradas: $sock - sock de conexao,
            $cod_assunto_pai - codigo do assunto,
  Saida:    Retorna uma string em HTML com a imagem das pastas e os links
*/

function EstruturaMoverAssunto($sock, $cod_assunto_pai)
{ 
  /* Obtem o nome do assunto-raiz.                        */
  $query1 = "select nome from Pergunta_assuntos where ";
  $query1 .= "cod_assunto = 1";

  $res1 = Enviar($sock, $query1);
  $tupla = RetornaLinha($res1);

  /* Obtem os nome e cod_assunto dos assuntos-filho.      */
  $query2 = "select nome, cod_assunto from Pergunta_assuntos where ";
  $query2 .= "cod_assunto_pai = 1 order by nome";

  $res2 = Enviar($sock, $query2);
  $tuplas = RetornaArrayLinhas($res2);
  $total = count($tuplas);

  /* Parte do nivel zero.                                               */
  $nivel = 0;

  if ($cod_assunto_pai == 1)
  /* Se estiver no assunto-raiz entao, cria uma apenas uma string com o */
  /* nome do assunto-raiz e chama a fun�ao EstruturaMoverAssuntoPropr.  */
  {
    $caminho = "<img src=\"../imgs/pasta.gif\" border=0>\n";
    $caminho .= "<font class=text>".TruncaString($tupla['nome'], 20)."</font><br>\n";

    /* Para cada assunto contido nos sub-assuntos cria os links.        */
    for ($i = 0; $i < $total; $i++)
    {
      $caminho .= EstruturaMoverAssuntoPropr($sock, $tuplas[$i]['cod_assunto'], $nivel);
    }
  }
  else
  /* Se NAO estiver no assunto-raiz entao, cria um link para o       */
  /* assunto-raiz.                                                   */
  {
    $caminho .= "<a class=link href=# onClick='MoverAssunto";
    $caminho .= "(selected_item, 1, 1);return(false);'>";
    $caminho .= "<img src=\"../imgs/pasta.gif\" border=0></a>\n";
    /* Mantem o parametro 'proprietario' da fun�ao javascript         */
    /* MoverAssunto(origem, destino, proprietario) constante em  1    */
    /* (assunto-raiz).                                                */
    $caminho .= "<a class=link href=# onClick='MoverAssunto";
    $caminho .= "(selected_item, 1, 1);return(false);'>";
    $caminho .= TruncaString($tupla['nome'], 20)."</a><br>\n";
    /* Para cada assunto contido nos sub-assuntos cria os links.        */
    for ($i = 0; $i < $total; $i++)
    {
      $caminho .= EstruturaMoverAssuntoRecur($sock, $tuplas[$i]['cod_assunto'], $cod_assunto_pai, $nivel);
    }
  }

  return($caminho);
}

/* *************************************************************************
  Fun�ao EstruturaMoverAssunto - cria um link para a fun�ao javascript
    MoverAssunto(origem, destino, proprietario).
  Se o $cod_assunto_pai (codigo do assunto em que o usuario estah
    navegando) for igual a 1 (assunto-raiz), entao escreve apenas uma
    string com o nome do assunto-raiz e chama a fun�ao
    EstruturaMoverAssuntoPropr, que se encarregar� em manter constante o
    parametro 'proprietario' da fun�ao javascript
    MoverAssunto(origem, destino, proprietario) com o cod_assunto dos
    sub-assuntos do proximo nivel.
  Se o $cod_assunto_pai NAO for igual ao $cod_assunto, entao chama-se
    recursivamente.

  Entradas: $sock - sock de conexao,
            $cod_assunto_pai - codigo do assunto,
  Saida:    Retorna uma string em HTML com a imagem das pastas e os links
*/

function EstruturaRecuperarAssunto($sock, $cod_assunto_pai)
{
  /* Obtem o nome do assunto-raiz.                        */
  $query1 = "select nome from Pergunta_assuntos where ";
  $query1 .= "cod_assunto = 1";

  $res1 = Enviar($sock, $query1);
  $tupla = RetornaLinha($res1);

  /* Obtem os nome e cod_assunto dos assuntos-filho.      */
  $query2 = "select nome, cod_assunto from Pergunta_assuntos where ";
  $query2 .= "cod_assunto_pai = 1 order by nome";

  $res2 = Enviar($sock, $query2);
  $tuplas = RetornaArrayLinhas($res2);
  $total = count($tuplas);

  /* Parte do nivel zero.                                               */
  $nivel = 0;

//T  if ($cod_assunto_pai == 1)
//T  /* Se estiver no assunto-raiz entao, cria uma apenas uma string com o */
//T  /* nome do assunto-raiz e chama a fun�ao EstruturaMoverAssuntoPropr.  */
//T  {
//T    $caminho = "<img src=\"../imgs/pasta.gif\" border=0>\n";
//T    $caminho .= "<font class=text>".TruncaString($tupla['nome'], 20)."</font><br>\n";
//T    
//T    /* Para cada assunto contido nos sub-assuntos cria os links.        */
//T    for ($i = 0; $i < $total; $i++)
//T    {
//T      $caminho .= EstruturaRecuperarAssuntoPropr($sock, $tuplas[$i]['cod_assunto'], $nivel);
//T    }
//T  }
//T  else
//T  /* Se NAO estiver no assunto-raiz entao, cria um link para o       */
//T  /* assunto-raiz.                                                   */
//T  {
    $caminho .= "<a class=link href=# onClick='RecuperarPergunta";
    $caminho .= "(selected_item, 1, 1);return(false);'>";
    $caminho .= "<img src=\"../imgs/pasta.gif\" border=0></a>\n";
    /* Mantem o parametro 'proprietario' da fun�ao javascript         */
    /* MoverAssunto(origem, destino, proprietario) constante em  1    */
    /* (assunto-raiz).                                                */
    $caminho .= "<a class=link href=# onClick='RecuperarPergunta";
    $caminho .= "(selected_item, 1, 1);return(false);'>";
    $caminho .= TruncaString($tupla['nome'], 20)."</a><br>\n";
    
    for ($i = 0; $i < $total; $i++)
    {
      $caminho .= EstruturaRecuperarAssuntoProprRecur($sock, $tuplas[$i]['cod_assunto'], $cod_assunto_pai, $nivel);
    }
//T  }

  return($caminho);
}


/* *************************************************************************
  Fun�ao EstruturaMoverPerguntaRecur - cria um link para a fun�ao
    javascript MoverPergunta(id, destino).
  Se o $cod_assunto_pai NAO for igual ao $cod_assunto, entao chama-se
    recursivamente.

  Entradas: $sock - sock de conexao,
            $cod_assunto_pai - codigo do assunto atual,
            $cod_assunto - codigo do assunto,
            $nivel - nivel.
  Saida:    Retorna uma string em HTML com a imagem das pastas e os links
*/

function EstruturaMoverPerguntaRecur($sock, $cod_assunto_pai, $cod_assunto, $nivel)
{
  /* Obtem o nome do assunto atual.                        */
  $query1 = "select nome from Pergunta_assuntos where ";
  $query1 .= "cod_assunto = ".$cod_assunto_pai;

  $res1 = Enviar($sock, $query1);
  $tupla = RetornaLinha($res1);

  /* Obtem os nome e cod_assunto dos assuntos-filho.      */
  $query2 = "select nome, cod_assunto from Pergunta_assuntos where ";
  $query2 .= "cod_assunto_pai = ".$cod_assunto_pai." order by nome";

  $res2 = Enviar($sock, $query2);
  $tuplas = RetornaArrayLinhas($res2);
  $total = count($tuplas);

  $caminho = "";

  /* Incrementa o nivel.                                            */
  $nivel++;

  /* Alinha os assuntos com espa�os de acordo com o nivel em que se */
  /* encontra. */
  for ($j = 0; $j < $nivel; $j++)
    $caminho .= "&nbsp;&nbsp;&nbsp;&nbsp;";

  if ($cod_assunto == $cod_assunto_pai)
  /* Se o assunto processado for o mesmo em que o usuario se encontra */
  /* cria uma string com o nome do assunto.                           */
  {
    $caminho .= "<img src=\"../figuras/assuntoab.gif\" border=0>\n";
    $caminho .= "<font class=text>".TruncaString($tupla['nome'], 20)."</font><br>\n";
  }
  else
  /* Se o assunto processado NAO for o mesmo em que o usuario se      */
  /* encontra, entao cria um link com o nome do assunto.              */
  {
    $caminho .= "<a class=text href=# onClick='MoverPergunta";
    $caminho .= "(selected_item, ".$cod_assunto_pai.");return(false);'>";
    $caminho .= "<img src=\"../figuras/assunto.gif\" border=0></A>\n";
    $caminho .= "<a class=text href=# onClick='MoverPergunta";
    $caminho .= "(selected_item, ".$cod_assunto_pai.");return(false);'>";
    $caminho .= TruncaString($tupla['nome'], 20)."</a><br>\n";
  }

  /* Para cada assunto contido nos sub-assuntos cria os links.      */
  for ($i = 0; $i < $total; $i++)
  {
    $caminho .= EstruturaMoverPerguntaRecur($sock, $tuplas[$i]['cod_assunto'], $cod_assunto, $nivel);
  }

  return($caminho);
}


/* *************************************************************************
  Fun�ao EstruturaMoverPergunta - cria um link para a fun�ao javascript
    MoverPergunta(id, destino).

  Entradas: $sock - sock de conexao,
            $cod_assunto_pai - codigo do assunto atual,
  Saida:    Retorna uma string em HTML com a imagem das pastas e os links
*/

function EstruturaMoverPergunta($sock, $cod_assunto_pai)
{
  /* Obtem o nome do assunto-raiz.                        */
  $query1 = "select nome from Pergunta_assuntos where ";
  $query1 .= "cod_assunto = 1";

  $res1 = Enviar($sock, $query1);
  $tupla = RetornaLinha($res1);

  /* Obtem os nome e cod_assunto dos assuntos-filho.      */
  $query2 = "select nome, cod_assunto from Pergunta_assuntos where ";
  $query2 .= "cod_assunto_pai = 1 order by nome";

  $res2 = Enviar($sock, $query2);
  $tuplas = RetornaArrayLinhas($res2);
  $total = count($tuplas);

  /* Parte do nivel zero.                                               */
  $nivel = 0;

  if ($cod_assunto_pai == 1)
    $caminho = "<img src=\"../figuras/assuntoab.gif\" border=0>\n";
  else
    $caminho = "<img src=\"../figuras/assunto.gif\" border=0>\n";
  $caminho .= "<font class=text>".TruncaString($tupla['nome'], 20)."</font><br>\n";

  /* Para cada assunto contido nos sub-assuntos cria os links.        */
  for ($i = 0; $i < $total; $i++)
  {
    $caminho .= EstruturaMoverPerguntaRecur($sock, $tuplas[$i]['cod_assunto'], $cod_assunto_pai, $nivel);
  }
  return($caminho);
}


/* *************************************************************************
  Fun�ao VerificaDescendencia - verifica se o assunto de $cod_assunto eh
    filho do assunto de $cod_assunto_pai, ou se eh o proprio
    $cod_assunto_pai.
  Se satisfazer as condi�oes acima retorna true, do contr�rio, false.
  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto,
            $cod_assunto_pai - codigo do assunto pai,
  Saida:    true se for descendente ou o proprio assunto, false do
              contrario.
*/

function VerificaDescendencia($sock, $cod_assunto, $cod_assunto_pai)
{
  /* Se o assunto for ele mesmo entao retorna true.             */
  if ($cod_assunto == $cod_assunto_pai)
    return(true);

  /* Retorna o codigo do assunto pai do assunto em questao.     */
  $query = "select cod_assunto_pai from Pergunta_assuntos where ";
  $query .= "cod_assunto = ".$cod_assunto;

  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);

  /* Se NAO for a Lixeira ou o Assunto-Raiz entao:              */
  if ($tupla['cod_assunto_pai'] != "")
  {
    /* Se for o proprio $cod_assunto_pai, entao retorna true.   */
    if ($tupla['cod_assunto_pai'] == $cod_assunto_pai)
      return(true);
    else
    /* Do contrario, verifica se os sub-assuntos do assunto de  */
    /* $cod_assunto sao descendentes de $cod_assunto_pai        */
      return (VerificaDescendencia($sock, $tupla['cod_assunto_pai'], $cod_assunto_pai));
  }
  return(false);
}


/* ****************************************************************
  Fun�ao MoveAssunto - Move o assunto, ou seja este passarah a ser
    filho do assunto de codigo $cod_assunto_dest.
  Retorna true se bem-sucedido, do contr�rio, false.
  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto
            $cod_assunto_dest - codigo do assunto de destino,
  Saida:   true se bem-sucedido, do contr�rio, false.
*/

function MoveAssunto($sock, $cod_assunto, $cod_assunto_dest)
{
  /* Se o assunto a ser movido for o assunto-raiz ou lixeira, ou se */
  /* o assunto-destino for a lixeira, entao retorna false.          */
  if (($cod_assunto == 1) || ($cod_assunto == 2) || ($cod_assunto_dest == 2))
    return(false);

  /* Verifique se o assunto a ser movido existe. */
  $query1 = "select nome from Pergunta_assuntos where ";
  $query1 .= "cod_assunto = ".$cod_assunto;
  $res1 = Enviar($sock, $query1);

  /* Verifique se o assunto destino existe. */
  $query2 = "select nome from Pergunta_assuntos where ";
  $query2 .= "cod_assunto = ".$cod_assunto_dest;
  $res2 = Enviar($sock, $query2);

  if ((RetornaNumLinhas($res1) == 0) && (RetornaNumLinhas($res2) == 0))
    return(false);

  /* Se o assunto destino NAO for descendente do assunto a ser   */
  /* movido, entao o $cod_assunto_pai deste assunto serah igual  */
  /* a $cod_assunto_dest.                                        */
  if (!VerificaDescendencia($sock, $cod_assunto_dest, $cod_assunto))
  {
    $query = "update Pergunta_assuntos set cod_assunto_pai = ".$cod_assunto_dest;
    $query .= ", data = ".time()." where cod_assunto = ".$cod_assunto;
    $res = Enviar($sock, $query);

    if ($res)
      return(true);
  }
  else
    return(false);
}

/* ****************************************************************
  Fun�ao ApagaAssunto - Apaga o assunto e todas as perguntas nele
    contidas.
  Retorna true se bem-sucedido, do contr�rio, false.
  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto
  Saida:   true se bem-sucedido, do contr�rio, false.
*/

function ApagaAssunto($sock, $cod_assunto)
{
  /* Se o assunto for o assunto-raiz ou lixeira entao retorna false. */
  if (($cod_assunto == 1) || ($cod_assunto == 2))
    return(false);

  /* Verifica se o assunto JAH foi apagado. */
  $query = "select nome from Pergunta_assuntos where cod_assunto = ".$cod_assunto;
  $res = Enviar($sock, $query);
  /* Se o assunto jah foi apagado, entao retorna false. */
  if (RetornaNumLinhas($res) == 0)
    return(false);

  /* Lista todos os assuntos-filho. Se n�o houver entao apaga todas as perguntas */
  /* do assunto e em seguida apaga o assunto.                                    */
  $query = "select cod_assunto from Pergunta_assuntos where cod_assunto_pai =".$cod_assunto;
  $res = Enviar($sock, $query);
  $tuplas = RetornaArrayLinhas($res);
  $total = count($tuplas);

  /* Se o assunto possuir filhos entao apaga-os recursivamente.                  */
  for ($i = 0; $i < $total; $i++)
  {
    if (ApagaAssunto($sock, $tuplas[$i]['cod_assunto']) == false)
      return(false);
  }

  /* Apaga todas as perguntas pertencentes a este assunto.                     */
  $query1 = "update Pergunta_itens set cod_assunto = 2 where cod_assunto = ".$cod_assunto;
  $res1 = Enviar($sock, $query1);
  /* Apaga o assunto atual.                                                    */
  $query2 = "delete from Pergunta_assuntos where cod_assunto =".$cod_assunto;
  $res2 = Enviar($sock, $query2);

  /* Se conseguiu apagar o assunto, entao retorna true.                        */
  if ($res2)
    return(true);
  else
    return(false);
}


/* ****************************************************************
  Fun�ao SalvaAssunto - Salva o assunto.
  Retorna true se bem-sucedido, do contr�rio, false.
  Entradas: $sock - sock de conexao,
            $cod_assunto_pai - codigo do assunto_pai: 1 - Raiz
                                                      2 - Lixeira
                                                      N - Outro
            $nome - nome do assunto,
            $descricao - descri�ao do assunto.
  Saida:   true se bem-sucedido, do contr�rio, false.
*/

function SalvaAssunto($sock, $cod_assunto_pai, $nome, $descricao)
{
  /* NAO permite inserir um assunto na lixeira */
  if ($cod_assunto_pai == 2)
    return(false);

  $nome = EliminaScript($nome);
  $nome = LimpaTags($nome);
  $nome = EliminaEspacos($nome);

  $descricao = EliminaScript($descricao);
  $descricao = Limpaconteudo($descricao);

  $query = "insert into Pergunta_assuntos (cod_assunto_pai, nome, descricao, data)";
  $query .= " values (".$cod_assunto_pai.", '".$nome."', '".$descricao."', ".time().")";

  $res = Enviar($sock, $query);

  if ($res)
  {
    $query2 = "select cod_assunto from Pergunta_assuntos where";
    $query2 .= " cod_assunto_pai = ".$cod_assunto_pai." and nome = '".$nome."'";
    $query2 .= " and descricao = '".$descricao."'";

    $res2 = Enviar($sock, $query2);

    $tupla = RetornaLinha($res2);
    return array (true, $tupla['cod_assunto']);
  }
  else
    return array (false, -1);
}


/* ****************************************************************
  Fun�ao EditaAssunto - Edita o nome do assunto e sua descri�ao.
  Retorna true se bem-sucedido, do contr�rio, false.
  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto,
            $nome - nome do assunto,
            $descricao - descri�ao do assunto.
  Saida:   true se bem-sucedido, do contr�rio, false.
*/

function EditaAssunto($sock, $cod_assunto, $nome, $descricao)
{
  if (($cod_assunto == 1) || ($cod_assunto == 2))
    return(false);

  $nome = EliminaScript($nome);
  $nome = LimpaTitulo($nome);
  $nome = EliminaEspacos($nome);

  $descricao = EliminaScript($descricao);
  $descricao = LimpaConteudo($descricao);

  $query = "update Pergunta_assuntos set nome = '".$nome."',";
  $query .= " descricao = '".$descricao."', data = ".time();
  $query .= " where cod_assunto = ".$cod_assunto;

  $res = Enviar($sock, $query);

  if ($res)
    return(true);
  else
    return(false);
}


/* ****************************************************************
  Fun�ao PerguntaExiste - Verifica se a pergunta existe.
  Entradas: $sock - sock de conexao,
            $cod_pergunta - codigo da pergunta,
  Saida:   true se bem-sucedido, do contr�rio, false.
*/

function PerguntaExiste($sock, $cod_pergunta)
{
  /* Verifica se a pergunta JAH foi apagada. */
  $query = "select cod_assunto from Pergunta_itens where cod_pergunta = ".$cod_pergunta;
  $res = Enviar($sock, $query);

  /* Verifica se a pergunta existe. */
  if (RetornaNumLinhas($res) == 0)
    return(false);
  else
    return(true);
}


/* ****************************************************************
  Fun�ao PerguntaFoiApagada - Verifica se a pergunta existe e se o
  $cod_assunto da pergunta eh igual a 2 (Lixeira).
  Entradas: $sock - sock de conexao,
            $cod_pergunta - codigo da pergunta,
  Saida:   true se bem-sucedido, do contr�rio, false.
*/

function PerguntaFoiApagada($sock, $cod_pergunta)
{
  /* Verifica se a pergunta JAH foi apagada. */
  $query = "select cod_assunto from Pergunta_itens where cod_pergunta = ".$cod_pergunta;
  $res = Enviar($sock, $query);

  /* Verifica se a pergunta existe.                          */
  /* NAO utiliza a fun�ao PerguntaExiste para NAO gerar mais */
  /* uma consulta a base de dados.                           */
  if (RetornaNumLinhas($res) == 0)
    return(false);

  $tupla = RetornaLinha($res);
  /* Se a pergunta jah foi apagada entao retorne false. */
  if ($tupla['cod_assunto'] == 2)
    return(true);
  else
    return(false);
}


/* *************************************************************
  Fun�ao RetornaPergunta - Retorna um array contendo o assunto
    a qual pertence e a pergunta e sua resposta .
  Entradas: $sock - sock de conexao.
            $cod_pergunta - codigo da pergunta.
  Saida:   Array com: ['cod_assunto'] - codigo do assunto,
                      ['pergunta'] - texto da pergunta,
                      ['resposta'] - texto da resposta.
*/

function RetornaPergunta($sock, $cod_pergunta)
{
  $query = "select cod_assunto, pergunta, resposta from ";
  $query .= "Pergunta_itens where cod_pergunta = ".$cod_pergunta;

  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);

  return($tupla);
}


/* *************************************************************
  Fun�ao RetornaTodasPerguntas - Retorna uma string com TAGS
    HTML contendo todas as perguntas do assunto de $cod_assunto
    e de seus sub-assuntos (obtidas recursivamente).
  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto,
            $eformador - booleano que indica se o usuario eh
              formador. Necessario para criar links para um menu
              ou para a fun�ao javascript Ver.
  Saida:   String no formato: <tr><td><a href# OnMouseDown=...>Pergunta</a></td>...</tr>.
*/

function RetornaTodasPerguntas($sock, $cod_assunto, $eformador)
{
  $query = "select pergunta, cod_pergunta from Pergunta_itens ";
  $query .= "where cod_assunto = ".$cod_assunto." order by pergunta";
  $res = Enviar($sock, $query);
  $tuplas = RetornaArrayLinhas($res);
  $total = count($tuplas);

  $todasperguntas = "";

  if ($eformador)
  {
    for ($i = 0; $i < $total; $i++)
    {
      $todasperguntas .= "      <tr class=wtfield>\n";
      /* Coloca uma caixa de sele�ao para exibi�ao multipla de perguntas */
      $todasperguntas .= "        <td class=wtfield width=1%>\n";
      $todasperguntas .= "          <input type=checkbox name=check[] value=".$tuplas[$i]['cod_pergunta'].">";
      $todasperguntas .= "        </td>\n";
      /* Insere a imagem associada aa pergunta e cria um link nela */
      /* direto para a pergunta.                                   */

      $todasperguntas .= "        <td class=wtfield width=1%><a class=text href=# onClick=";
      $todasperguntas .= "'Ver(".$tuplas[$i]['cod_pergunta'].");  return(false);'>";

      $todasperguntas .= "          <img src=\"../figuras/inter.gif\"";
      $todasperguntas .= " border=0></a>\n";
      $todasperguntas .= "        </td>\n";
      /* Insere o texto da pergunta truncada para acima de 40 caracteres,  */
      /* seu caminho completo e cria um link para o menu.                  */
      $todasperguntas .= "        <td class=wtfield>\n";
      $todasperguntas .= "          <a class=text href=# ";
      $todasperguntas .= " onMouseDown='MostraLayer(lay_pergunta,this);";
       $todasperguntas .= "selected_item=".$tuplas[$i]['cod_pergunta'].";return(false);'>";
      $todasperguntas .= TruncaString(LimpaTags($tuplas[$i]['pergunta']), 40)."</a>\n";
      $todasperguntas .= "          <b><font class=textsmall>(";
      $todasperguntas .= RetornaCaminhoAssunto($sock, $cod_assunto);
      $todasperguntas .= ")</b></font>\n";
      $todasperguntas .= "        </td>\n";
      $todasperguntas .= "      </tr>\n";
    }
  }
  else
  {
    for ($i = 0; $i < $total; $i++)
    {
      $todasperguntas .= "      <tr class=wtfield>\n";
      /* Coloca uma caixa de sele�ao para exibi�ao multipla de perguntas */
      $todasperguntas .= "        <td class=wtfield width=1%>\n";
      $todasperguntas .= "          <input type=checkbox name=check[] value=".$tuplas[$i]['cod_pergunta'].">";
      $todasperguntas .= "        </td>\n";
      $todasperguntas .= "        <td class=wtfield width=1%><a class=text href=# onClick='Ver(";
      $todasperguntas .= $tuplas[$i]['cod_pergunta'].");return(false);'>";
      $todasperguntas .= "<img src=\"../figuras/inter.gif\" border=0></a>\n";
      $todasperguntas .= "        </td>\n";
      /* Insere o texto da pergunta truncada para acima de 40 caracteres,  */
      /* seu caminho completo e cria um link para a fun�ao javascript Ver. */
      $todasperguntas .= "        <td class=wtfield>\n";
      $todasperguntas .= "          <a class=text href=# onClick='Ver(";
      $todasperguntas .= $tuplas[$i]['cod_pergunta'].");return(false);'>";
      $todasperguntas .= TruncaString(LimpaTags($tuplas[$i]['pergunta']), 40)."</a>\n";
      $todasperguntas .= "          <b><font class=textsmall>(";
      $todasperguntas .= RetornaCaminhoAssunto($sock, $cod_assunto);
      $todasperguntas .= ")</b></font>\n";
      $todasperguntas .= "        </td>\n";
      $todasperguntas .= "      </tr>\n";
      $todasperguntas .= "        </table>\n";
    }
  }

  /* Lista todos os assuntos-filho. Se n�o houver entao apaga todas as perguntas */
  /* do assunto e em seguida apaga o assunto.                                    */
  $query = "select cod_assunto from Pergunta_assuntos where cod_assunto_pai = ";
  $query .= $cod_assunto." order by nome";
  $res = Enviar($sock, $query);
  $tuplas = RetornaArrayLinhas($res);
  $total = count($tuplas);

  /* Se o assunto possuir filhos entao retorna suas perguntas recursivamente.    */
  for ($i = 0; $i < $total; $i++)
  {
    $todasperguntas .= RetornaTodasPerguntas($sock, $tuplas[$i]['cod_assunto'], $eformador);
  }
  return($todasperguntas);
}


/* *************************************************************
  Fun�ao ListaTodasPerguntas - Retorna uma lista contendo todas as
    perguntas do Perguntas Freq�entes.
  Retorna uma lista com o nome, codigo do assunto e data de
    inser�ao.
  Entradas: $sock - sock de conexao.
            $cod_assunto - codigo do assunto: 1 - Raiz
                                              2 - Lixeira
                                              N - Outro
  Saida:   Array com: ['cod_pergunta'] - codigo da pergunta,
                      ['pergunta'] - pergunta.
*/

function ListaTodasPerguntas($sock)
{
  // Pega todas as perguntas, menos as que estao no lixo.
  $query = "select cod_pergunta, cod_assunto, pergunta from Pergunta_itens where cod_assunto <> 2 order by cod_pergunta";
  $res = Enviar($sock, $query);
  $tuplas = RetornaArrayLinhas($res);
  return($tuplas);
}

/* *************************************************************
  Fun�ao ListaPerguntas - Retorna uma lista contendo todas as
    perguntas do Perguntas Freq�entes.
  Retorna uma lista com o nome, codigo do assunto e data de
    inser�ao.
  Entradas: $sock - sock de conexao.
            $cod_assunto - codigo do assunto: 1 - Raiz
                                              2 - Lixeira
                                              N - Outro
  Saida:   Array com: ['cod_pergunta'] - codigo da pergunta,
                      ['pergunta'] - pergunta.
*/

function ListaPerguntas($sock, $cod_assunto)
{
  $query = "select cod_pergunta, pergunta from Pergunta_itens where cod_assunto = ".$cod_assunto." order by pergunta";
  $res = Enviar($sock, $query);
  $tuplas = RetornaArrayLinhas($res);
  return($tuplas);
}

/* ****************************************************************
  Fun�ao ApagaPergunta - "Apaga" a pergunta, modifica a campo
    cod_assunto para 2 (Lixeira).
  Retorna true se bem-sucedido, do contr�rio, false.
  Entradas: $sock - sock de conexao,
            $cod_pergunta - codigo da pergunta.
  Saida:   true se bem-sucedido, do contr�rio, false.
*/

function ApagaPergunta($sock, $cod_pergunta)
{
  if(!is_array($cod_pergunta))
  {
    /* Se a pergunta JAH foi apagada retorna false. */
    if (PerguntaFoiApagada($sock, $cod_pergunta))
      return(false);

    $query = "update Pergunta_itens set cod_assunto = 2 where cod_pergunta = ".$cod_pergunta;
    $res = Enviar($sock, $query);
  }
  else
  {
   $total = count($cod_pergunta);
   for ($i = 0; ($i < $total) && ($cod_pergunta[$i] != ""); $i++)
   {
     /* Se a pergunta JAH foi apagada retorna false. */
    if (PerguntaFoiApagada($sock, $cod_pergunta[$i]))
      return(false);

    $query = "update Pergunta_itens set cod_assunto = 2 where cod_pergunta = ".$cod_pergunta[$i];
    $res = Enviar($sock, $query);
    
    if(!$res)
      return(false);
   }
   }
  if ($res)
    return(true);
  else
    return(false);
}


/* ****************************************************************
  Fun�ao ExcluiPergunta - Exclui a pergunta da base de dados,
    modifica a campo cod_assunto para 2 (Lixeira).
  Retorna true se bem-sucedido, do contr�rio, false.
  Entradas: $sock - sock de conexao,
            $cod_pergunta - codigo da pergunta.
  Saida:   true se bem-sucedido, do contr�rio, false.
*/

function ExcluiPergunta($sock, $cod_pergunta)
{
 if(!is_array($cod_pergunta))
 {
  /* Verifica se a pergunta encontra-se na Lixeira.                            */
  if (!PerguntaFoiApagada($sock, $cod_pergunta))
    return(false);

  /* Excluir a pergunta.                                                       */
  $query = "delete from Pergunta_itens where cod_pergunta = ".$cod_pergunta;
  $res = Enviar($sock, $query);
  }
  else
  {
   $total = count($cod_pergunta);
   for ($i = 0; ($i < $total) && ($cod_pergunta[$i] != ""); $i++)
   {
  /* Verifica se a pergunta encontra-se na Lixeira.                            */
  if (!PerguntaFoiApagada($sock, $cod_pergunta[$i]))
    return(false);
  /* Excluir a pergunta.                                                       */
  $query = "delete from Pergunta_itens where cod_pergunta = ".$cod_pergunta[$i];
  $res = Enviar($sock, $query);
  if(!$res)
    return(false);
  }
  }
  /* Se conseguiu excluir a pergunta, entao retorna true.                      */
  if ($res)
    return(true);
  else
    return(false);
}

/* ****************************************************************
  Fun�ao MovePergunta - Move a pergunta para o assunto de
    $cod_assunto.
  Retorna true se bem-sucedido, do contr�rio, false.
  Entradas: $sock - sock de conexao,
            $cod_pergunta - codigo da pergunta,
            $cod_assunto - codigo do assunto destino.
  Saida:   true se bem-sucedido, do contr�rio, false.
*/

function MovePergunta($sock, $cod_pergunta, $cod_assunto)
{
  /* Se a pergunta NAO existe entao retorna false. */
  if (!PerguntaExiste($sock, $cod_pergunta))
    return(false);

  $query = "update Pergunta_itens set cod_assunto = ".$cod_assunto;
  $query .= ", data = ".time()." where cod_pergunta = ".$cod_pergunta;
  $res = Enviar($sock, $query);

  if ($res)
    return(true);
  else
    return(false);
}

/* ****************************************************************
  Fun�ao EditaPergunta - Edita a pergunta e a resposta.
  Retorna true se bem-sucedido, do contr�rio, false.
  Entradas: $sock - sock de conexao,
            $cod_pergunta - codigo da pergunta,
            $pergunta - pergunta,
            $resposta - resposta.
  Saida:   true se bem-sucedido, do contr�rio, false.
*/

function EditaPergunta($sock, $cod_pergunta, $pergunta, $resposta)
{
  $pergunta = EliminaScript($pergunta);
  $pergunta = LimpaTitulo($pergunta);
  $pergunta = EliminaEspacos($pergunta);

  $resposta = EliminaScript($resposta);
  $resposta = LimpaConteudo($resposta);

  /* Se a pergunta JAH foi apagada retorna false. */
  if (PerguntaFoiApagada($sock, $cod_pergunta))
    return(false);

  $query = "update Pergunta_itens set pergunta = '".$pergunta."',";
  $query .= " resposta = '".$resposta."', data = ".time();
  $query .= " where cod_pergunta = ".$cod_pergunta;

  $res = Enviar($sock, $query);

  if (!$res)
    return(false);
  else
    return(true);
}


/* ****************************************************************
  Fun�ao SalvaPergunta - Salva a pergunta e a resposta.
  Retorna true se bem-sucedido, do contr�rio, false.
  Entradas: $sock - sock de conexao,
            $cod_assunto - codigo do assunto: 1 - Raiz
                                              2 - Lixeira
                                              N - Outro
            $pergunta - pergunta,
            $resposta - resposta.
  Saida:   true se bem-sucedido, do contr�rio, false.
*/

function SalvaPergunta($sock, $cod_assunto, $pergunta, $resposta)
{
  $pergunta = EliminaScript($pergunta);
  $pergunta = LimpaTitulo($pergunta);
  $pergunta = EliminaEspacos($pergunta);

  $resposta = EliminaScript($resposta);
  $resposta = LimpaConteudo($resposta);

  $query = "insert into Pergunta_itens (cod_assunto, pergunta, resposta, data)";
  $query .= " values (".$cod_assunto.", '".$pergunta."', '".$resposta."', ".time().")";

  $res = Enviar($sock, $query);

  if ($res)
  {
    $query2 = "select cod_pergunta from Pergunta_itens where";
    $query2 .= " cod_assunto = ".$cod_assunto." and pergunta = '".$pergunta."'";
    $query2 .= " and resposta = '".$resposta."'";

    $res2 = Enviar($sock, $query2);

    $tupla = RetornaLinha($res2);

    return array (true, $tupla['cod_pergunta']);
  }
  else
    return array (false, -1);
  
}

/* ****************************************************** 
   RetornaNumPerguntasAssunto - retorna numero de perguntas dentro de um assunto e seus filhos
                                RECURSIVAMENTE
   entrada: $sock - conexao a base de dados
            $cod_assunto - codigo do assunto a recorrer contando as perguntas
   saida:   numero de perguntas dentro do assunto
*/
function RetornaNumPerguntasAssunto($sock, $cod_assunto)
{
  /*
    O numero de perguntas no assunto $cod_assunto � o numero de perguntas no assunto 
    mais o numero de perguntas em cada um de seus filhos.
  */
  // numero de perguntas no assunto
  $query = "select count(cod_pergunta) 'num_perguntas' from Pergunta_itens where cod_assunto=".$cod_assunto;
  $res   = Enviar ($sock, $query);
  $linha = RetornaLinha ($res);
  $num   = $linha['num_perguntas'];
 
  // Os filhos do assunto. 
  $query = "select cod_assunto from Pergunta_assuntos where cod_assunto_pai=".$cod_assunto;
  $res   = Enviar ($sock, $query);
  $lista_filhos = RetornaArrayLinhas($res);
  
  // numero de perguntas nos filhos
  if (count($lista_filhos) > 0)
  {
    foreach ($lista_filhos as $cod => $linha_assunto)
    {
      $num += RetornaNumPerguntasAssunto($sock, $linha_assunto['cod_assunto']);
    }
  }
  return $num;
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
   RetornaTopicosAncestrais - Retorna array com os t�picos ancestrais do t�pico dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - Codigo do topico
   Saida: array [] - topicos (em ordem crescente de ancestralidade)
*/
function RetornaTopicosAncestrais ($sock, $tabela, $cod_topico)
{
  do
  {
    $consulta="select * from ".$tabela."_assuntos where cod_assunto=".$cod_topico;
    $res=Enviar($sock, $consulta);
    $linha=RetornaLinha($res);
    $cod_topico=$linha['cod_assunto_pai'];
    $retorno[$cont]=$linha;
    $cont++;
  }
  while ($cod_topico!="");
  return($retorno);
}

/* *********************************************************************
   RetornaTopicosDoTopico - Retorna array com os topicos filhos do t�pico dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - t�pico
   Saida: array com []['cod_assunto']
                    []['cod_assunto_pai']
                    []['cod_usuario']
                    []['topico']
                    []['tipo_compartilhamento']
                    []['data']
                    []['posicao_topico']
*/
function RetornaTopicosDoTopico ($sock, $tabela, $cod_topico)
{
  $consulta="select * from ".$tabela."_assuntos where cod_assunto_pai=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   RetornaItensDoTopico - Retorna array com os itens do t�pico dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada
            $cod_topico - t�pico
   Saida: array com []['cod_item']
                    []['cod_assunto']
                    []['pergunta']
                    []['resposta']
                    []['data']
*/
function RetornaItensDoTopico ($sock, $tabela, $cod_topico)
{
  $consulta="select * from ".$tabela."_itens where cod_assunto=".$cod_topico;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   RetornaUltimaPosicaoHistorico - Retorna a ultima ocorr�ncia do historico de um item dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_item - item
   Saida: array ['cod_item']
                ['cod_usuario']
                ['data']
                ['acao']
*/
function RetornaUltimaPosicaoHistorico ($sock, $tabela, $cod_item)
{
  $consulta="select * from ".$tabela."_itens_historicos where cod_item=".$cod_item." order by data desc limit 1";
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}

/* *********************************************************************
   RetornaListaDeTopicos - retorna array com lista de topicos
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
   Saida: array []['cod_assunto']
                []['cod_assunto_pai']
                []['nome']
                []['descricao']
                []['data']
                []['nivel']
                []['espacos']
*/
function RetornaListaDeTopicos ($sock, $tabela)
{
  $consulta="select * from ".$tabela."_assuntos where cod_assunto=1";
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  $retorno[0]=$linha;
  $retorno[0]['nivel']=0;
  $retorno[0]['espacos']="";
  $topicos=RetornaListaDeTopicosRecursao($sock, $tabela, $linha);
  $i=1;
  if (count($topicos)>0)
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
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $topico - array com 1 elemento []['cod_topico'], contendo o c�digo do t�pico
   Saida: array []['cod_assunto']
                []['cod_assunto_pai']
                []['nome']
                []['data']
                []['nivel'] - Nivel de recurs�o at� atingir o t�pico
                []['espacos'] - espacos que ser�o usados para exibi��o (&nbsp;)
*/
function RetornaListaDeTopicosRecursao ($sock, $tabela, $topico)
{
  $consulta="select * from ".$tabela."_assuntos where cod_assunto_pai=".$topico['cod_assunto'];
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

/* *********************************************************************
   RetornaDiretorio - Retorna o Diret�rio da tabela de diretorios
   Entrada: $sock - BASE EXTERNA
            $item - nome do item a ser buscado
   Saida: string com o diret�rio
*/
function RetornaDiretorio($sock,$item)
{
  $query="select diretorio from Diretorio where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha[0]);
}

/* *********************************************************************
   RetornaMaiorData - Retorna a maior data dentro de um t�pico ou subt�pico
   Entrada: $sock - Conex�o com a base interna
            $tabela - Tabela usada
            $cod_topico - codigo do topico a ser checado
            $tipo_comp - tipo do compartilhamento (F ou T)
            $data - Data do t�pico
   Saida: maior data
*/
function RetornaMaiorData($sock,$tabela,$cod_topico,$tipo_comp,$data)
{
  $query="select cod_assunto,data from ".$tabela."_assuntos where cod_assunto_pai=".$cod_topico;
  $res=Enviar($sock,$query);
  $lista_topicos=RetornaArrayLinhas($res);

  /* Verifica se existem subt�picos */
  if (count($lista_topicos)>0)
  {
    foreach($lista_topicos as $cod=>$linha)
    {
      $data_tmp=RetornaMaiorData($sock,$tabela,$linha['cod_assunto'],$tipo_comp,$linha['data']);
      if ($data_tmp>$data)
        $data=$data_tmp;
    }
  }

  $query="select max(data) from ".$tabela."_itens where cod_assunto=".$cod_topico;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha[0]>$data)
    $data=$linha[0];

  return $data;
}

/* *********************************************************************
   ImportarMateriaisObterRecur -
   Entrada: $sock - conex�o com a base interna
            $cod_curso_origem - c�digo do curso cujos itens ser�o importados
            $flag_curso_compartilhado - booleano, true se o curso foi escolhido
              na lista de cursos com itens compartilhados.
            $nome_tabela - nome da tabela usada
            $dirname - nome do diret�rio da ferramenta
            $diretorio_arquivos_origem - caminho dos arquivos do curso cujos
                    itens ser�o importados
            $cod_topico_origem - c�digo do topico a ser listado
            $tipo_comp - tipo do compartilhamento (F ou T)
            $data - Data do t�pico
   Saida: ['NOME'] - nome do t�pico
          ['SUB'][] - array de elementos desta estrutura (recursivo)
          ['ITENS'][j][*] - dados de um item
                   [j]['CAMINHO'] - caminho dos arquivos do item
                   [j]['ENDERECOS'][k][*] - dados de um endereo
*/
function ImportarMateriaisObterRecur($sock, $cod_curso_origem, $flag_curso_compartilhado,
                                     $nome_tabela, $dirname,
                                     $diretorio_arquivos_origem, $cod_topico_origem)
{

  $query = "select nome
              from ".$nome_tabela."_assuntos
              where cod_assunto = ".$cod_topico_origem;

  $id = Enviar($sock, $query);
  $linha = RetornaLinha($id);
  $nome_topico = $linha['nome'];

  $resultado['NOME'] = $nome_topico;

  $query = "select cod_assunto, nome
              from ".$nome_tabela."_assuntos
              where cod_assunto_pai = ".$cod_topico_origem;

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
                                    $diretorio_arquivos_origem, $dados_subtopico['cod_assunto']);
    }
  }
  else
    $resultado['SUB'] = array(); // vazio

  if ($flag_curso_compartilhado)
    $tipo_compartilhamento = "'T'";
  else
    $tipo_compartilhamento = "'T', 'F'";

  $query = "select cod_pergunta, pergunta,
                   resposta,  data
              from ".$nome_tabela."_itens
              where cod_assunto = ".$cod_topico_origem;

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
           rtrim($caminho, "/")."/".$linhas_itens[$i]['cod_pergunta']."/";
      $k++;
    }
  }
  else
    $resultado['ITENS'] = array(); // vazio

  return ($resultado);
}

/* *********************************************************************
   ImportarMateriais -
   Entrada: $sock - conex�o com a base interna
            $cod_curso_destino - c�digo do curso no qual ser�o inseridos os
                itens importados.
            $cod_topico_destino - c�digo do t�pico para onde ser�o importados
                os itens
            $cod_usuario - c�digo do usu�rio que est� importando.
            $cod_curso_origem - c�digo do curso cujos itens ser�o importados.
            $flag_curso_extraido - booleano, true se o curso � extra�do
            $flag_curso_compartilhado - booleano, true se o curso foi selecionado
               da lista de cursos compartilhados
            $array_topicos_origem - array de cod_topico a ser importado
                selecionado pelo usu�rio.
            $array_itens_origem - array de cod_item a ser importado
                selecionado pelo usu�rio.
            $nome_tabela - nome da tabela usada
            $dirname - nome do diret�rio da ferramenta
            $diretorio_arquivos_destino - caminho dos arquivos do curso para onde
                ser�o importados os arquivos dos itens
            $diretorio_arquivos_origem - caminho dos arquivos do curso cujos itens
                ser�o importados.
   Saida: nenhuma

*/
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

  // Obt�m os itens e t�picos a serem importados
  $sock = Conectar($cod_curso_origem, $tmp);

  // Obt�m os t�picos e seus conte�dos a serem importados recursivamente
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

    if ($flag_curso_compartilhado)
      $tipo_compartilhamento = "'T'";
    else
      $tipo_compartilhamento = "'T', 'F'";


    // Obt�m os itens a serem importados
    $query = "select  cod_pergunta, pergunta,
                   resposta,  data
                from ".$nome_tabela."_itens
                where cod_pergunta in (".$cod_itens_origem.")";

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

  // Importamos os t�picos selecionados,
  // recursivamente.
  if (count($res['SUB']) > 0)
  {
    foreach($res['SUB'] as $idx => $dados)
    {
      $flag1 = $flag1 && ImportarMateriaisInserirRecur($sock, $cod_curso_destino, $cod_usuario, $nome_tabela,
                                    $cod_topico_destino, $dirname, $diretorio_arquivos_destino,
                                    $dados);
    }
  }

  // Se houve itens selecionados no t�pico atual,
  // importamos.
  if (count($res['ITENS']) > 0)
  {
    foreach($res['ITENS'] as $idx => $dados)
    {
      $flag2 = $flag2 && ImportarItem($sock, $cod_curso_destino, $cod_usuario,
                   $nome_tabela, $cod_topico_destino, $dirname,
                   $diretorio_arquivos_destino, $dados);
    }
  }
  Desconectar($sock);


  return($flag1 && $flag2);
}

/* *********************************************************************
   ImportarMateriaisInserirRecur -
   Entrada: $sock - conex�o com a base interna
            $cod_curso_destino - c�digo do curso no qual ser�o inseridos os
                itens importados.
            $cod_usuario - c�digo do usu�rio que est� importando.
            $nome_tabela - nome da tabela usada
            $cod_topico_destino - c�digo do t�pico para onde ser�o importados
                os itens
            $dirname - nome do diret�rio da ferramenta
            $diretorio_arquivos_destino - caminho dos arquivos do curso para onde
                ser�o importados os arquivos dos itens
            $dados_topico['NOME'] - nome do t�pico
                         ['SUB'][] - array de elementos desta estrutura (recursivo)
                         ['ITENS'][j][*] - dados de um item
                                  [j]['CAMINHO'] - caminho dos arquivos do item
                                  [j]['ENDERECOS'][k][*] - dados de um endereo
   Saida: nenhuma

*/
function ImportarMateriaisInserirRecur($sock, $cod_curso_destino, $cod_usuario, $nome_tabela,
                                       $cod_topico_destino, $dirname, $diretorio_arquivos_destino,
                                       $dados_topico)
{
  $flag = true;

  $novo_cod_topico = ImportarTopico($sock, $nome_tabela,
                                    $cod_topico_destino, $dados_topico['NOME'],
                                    $cod_usuario);

  $total_subtopicos = count($dados_topico['SUB']);
  if ($total_subtopicos > 0)
  {
    foreach ($dados_topico['SUB'] as $idx => $dados_subtopico)
    {
      $flag = $flag && ImportarMateriaisInserirRecur($sock, $cod_curso_destino, $cod_usuario, $nome_tabela,
                                    $novo_cod_topico, $dirname, $diretorio_arquivos_destino,
                                    $dados_subtopico);
    }
  }

  $total_itens = count($dados_topico['ITENS']);
  if ($total_itens > 0 )
  {
    for ($k = 0; $k < $total_itens; $k++)
    {
      $flag = $flag && ImportarItem($sock, $cod_curso_destino, $cod_usuario,
                   $nome_tabela, $novo_cod_topico, $dirname,
                   $diretorio_arquivos_destino, $dados_topico['ITENS'][$k]);
    }
  }
  return($flag);
}

/* *********************************************************************
   CriarTopico - Cria um novo topico filho do topico dado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico_raiz - topico pai
            $novo_nome - nome do novo topico
            $cod_usuario - codigo do usuario atual
   Saida: codigo do topico criado
*/
function CriarTopico ($sock, $tabela, $cod_topico_raiz, $novo_nome, $cod_usuario)
{
  $consulta="insert into ".$tabela."_assuntos (cod_assunto_pai, nome,  data) values (".$cod_topico_raiz.", '".$novo_nome."',  ".time().")";
  $res=Enviar($sock, $consulta);
  $cod_topico = RetornaUltimoAutoIncrement($sock);
  return($cod_topico);
}

/* *********************************************************************
   ImportarTopico -
   Entrada: $sock - conex�o com a base interna
            $cod_curso_destino - c�digo do curso no qual ser�o inseridos os
                itens importados.
            $nome_tabela - nome da tabela usada
            $cod_topico_destino - c�digo do t�pico onde ser� importado
                o t�pico.
            $novo_nome - nome do t�pico
            $cod_usuario - c�digo do usu�rio que est� importando.
   Saida: nenhuma

*/
function ImportarTopico($sock, $nome_tabela, $cod_topico_destino, $novo_nome, $cod_usuario)
{
  return(CriarTopico($sock, $nome_tabela, $cod_topico_destino, $novo_nome, $cod_usuario));
}

/* *********************************************************************
   ImportarItem -
   Entrada: $sock - conex�o com a base interna
            $cod_curso_destino - c�digo do curso no qual ser�o inseridos os
                itens importados.
            $cod_usuario - c�digo do usu�rio que est� importando.
            $nome_tabela - nome da tabela usada.
            $cod_topico_destino - c�digo do t�pico onde ser� importado
                o item.
            $dirname - nome da pasta da ferramenta
            $diretorio_arquivos_destino - caminho da pasta de arquivos
               para onde ser�o importados os itens
            $dados_item[j][*] - dados de um item
                       [j]['CAMINHO'] - caminho dos arquivos do item
                       [j]['ENDERECOS'][k][*] - dados de um endereo
   Saida: nenhuma

*/
function ImportarItem($sock, $cod_curso_destino, $cod_usuario,
                      $nome_tabela, $cod_topico_destino, $dirname,
                      $diretorio_arquivos_destino, $dados_item)
{

  $flag1 = true;

  $horario = time();

  // Importamos o item compartilhado apenas com formadores
  // e com status "livre" ('L') de edi��o.
  $consulta = "insert into ".$nome_tabela."_itens
                   ( cod_assunto, pergunta,
                    resposta, data)
                 values ( ".$cod_topico_destino.",
                         '".$dados_item['pergunta']."', '".$dados_item['resposta']."',
                         ".$horario.")";
  $flag1 = Enviar($sock, $consulta);


  return($flag1);
}

/* *********************************************************************
   RetornaArquivosMaterialVer - Retorna lista de arquivos do Material
   Entrada: $diretorio_temp - diret�rio temporario de arquivos
            $dir - nome do diret�rio da ferramenta
            $cod_item - c�digo do item
   Saida: Array multidimensional com:
          $lista[<num>]['Caminho'] - caminho completo.
          $lista[<num>]['Diretorio'] - Diretorio do arquivo
          $lista[<num>]['Arquivo'] - Nome do arquivo
          $lista[<num>]['Status'] - Condi��o especial (true ou false);
          $lista[<num>]['Tamanho'] - tamanho do arquivo
          $lista[<num>]['Data'] - data da �ltima modifica��o
*/
function RetornaArquivosMaterialVer ($cod_curso, $dirlink)
{
  return (RetornaArrayDiretorio($dirlink));
}

/* *********************************************************************
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
  /* Busca Arquivo a ser mostrado */
  unset ($caminho);
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
   RetornaDadosDoItem - Retorna um array com os dados do item
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_topico - t�pico
   Saida: array com ['cod_pergunta']
                    ['cod_assunto']
                    ['pergunta']
                    ['resposta']
                    ['data']
*/
function RetornaDadosDoItem ($sock, $tabela, $cod_item)
{
  $consulta="select * from ".$tabela."_itens where cod_pergunta=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}

?>
