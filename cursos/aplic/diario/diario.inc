<?

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/diario/diario.inc

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
  ARQUIVO : cursos/aplic/diario/diario.inc
  ========================================================== */

/* ******************************************************
  EliminaEspacos - Elimina espacos no come�o e no
    final da frase.
  Retorna a frase sem os espacos no come�o e no final dela.
  Entradas: $frase - frase
  Saida:   frase sem os espacos no come�o e no final dela.

*/

function EliminaEspacos($frase)
{
  return(trim($frase));
}

/* ******************************************************
  Compara - Compara os os elementos do array multidimensional
    pelo nome por ordem natural e sem sensibilidade � caixa
    das letras.
    Esta fun��o � utilizada pela fun��o usort de
    RetornaDiarios como crit�rio de ordena��o.

  Entradas: $ar1 - elemento ,
            $ar2 - outro elemento do array.

  Saida:    < 0 se $ar1 for MENOR que $ar2,
            > 0 se $ar1 for MAIOR que $ar2,
            = 0 se $ar1 for IGUAL a $ar2.

*/

function Compara($ar1, $ar2)
{
  return (strnatcasecmp($ar1['nome'], $ar2['nome']));
}

/* ***********************************************************************
   RetornaDiarios - retorna links para os diarios
   entrada: $sock - sock de conexao
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario logado
   saida:   array [ $cod_usuario ]['nome'] - 'diario de fulano de tal'
                                  ['data'] - data
                                  ['num_itens'] - numero de itens no diario
                                  ['num_itens_nao_comentados'] - numero de itens nao comentados
                                  ['novidade'] - booleano que indica se houve novidade no portfolio
*/
function RetornaDiarios ($sock, $cod_curso, $cod_usuario)
{
  global $lista_frases;

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];


  // lista dos usuarios
  $query = "select UC.cod_usuario, U.nome from ".$dbnamebase.".Usuario U inner join ".$dbnamebase.".Usuario_curso UC on U.cod_usuario=UC.cod_usuario_global where UC.cod_curso=".$cod_curso;
  $res   = Enviar ($sock, $query);
  $lista = RetornaArrayLinhas ($res);
  if (is_array ($lista))
    foreach ($lista as $c => $linha)
      $lista_usr [ $linha ['cod_usuario'] ] = $linha ['nome'];


  // garantindo que o usuario logado vai ter seu diario aparecendo, mesmo que este esteja vazio
  // se houver algo no diario, esta informacao vai ser sobreescrita no loop mais embaixo
  // 56 - Diario de
  $lista_diarios [ $cod_usuario ]['nome'] = RetornaFraseDaLista ($lista_frases, 56)." ".$lista_usr[ $cod_usuario ];
  $lista_diarios [ $cod_usuario ]['data'] = 0;
  $lista_diarios [ $cod_usuario ]['num_itens'] = 0;
  $lista_diarios [ $cod_usuario ]['num_itens_nao_comentados'] = 0;

  if ( EFormadorComVisaoDeAluno ($sock, $cod_curso, $cod_usuario))
  {
    $regra_comp = "tipo_compartilhamento = 'T'";
  }
  else if (! EFormador ($sock, $cod_curso, $cod_usuario))
  {
    $regra_comp = "(tipo_compartilhamento = 'T' or cod_usuario=".$cod_usuario.")";
  }
  else
  {
    $regra_comp = "(tipo_compartilhamento = 'T' or tipo_compartilhamento = 'F' or cod_usuario=".$cod_usuario.")";
  }

  // lista dos diarios a retornar
  $query = "select cod_usuario, cod_item, data from Diario_itens where ".$regra_comp." AND status='A' order by cod_usuario";
  $res   = Enviar ($sock, $query);
  $lista1= RetornaArrayLinhas ($res);

  if (is_array ($lista1))
  {
    // lista com diarios e numero de itens comentados em cada diario e maior data de comentario
    $query = "select cod_item, count(cod_comentario) 'num_comentarios', max(data) 'data' from Diario_comentarios group by cod_item";
    $res   = Enviar ($sock, $query);
    $lista2= RetornaArrayLinhas ($res);

    if (is_array ($lista2))
    {
      foreach ($lista2 as $linha2)
      {
        $cod_item = $linha2 ['cod_item'];
        $lista_comentarios [ $cod_item ]['num_comentarios'] = $linha2 ['num_comentarios'];
        $lista_comentarios [ $cod_item ]['data']            = $linha2 ['data'];
      }
    }

    $penultimo_acesso = PenultimoAcesso($sock, $cod_usuario, "");

    foreach ($lista1 as $linha1)
    {
      $cod_usr         = $linha1 ['cod_usuario'];
      $cod_item        = $linha1 ['cod_item'];
      $data_item       = $linha1 ['data'];
      $data_comentario = $lista_comentarios [$cod_item]['data'];
      // 56 - Diario de
      $lista_diarios [ $cod_usr ]['nome'] = RetornaFraseDaLista ($lista_frases, 56)." ".$lista_usr [ $cod_usr ];

      // retornar data do item mais novo (item mais novo = maior data) dentro do diario ou do comentarios mais novo
      if (!isset ($lista_diarios [ $cod_usr ]['data']) || $lista_diarios [ $cod_usr ]['data'] < $data_item)
        $lista_diarios [ $cod_usr ]['data'] = $data_item;
      if ($lista_diarios [ $cod_usr ]['data'] < $data_comentario)
        $lista_diarios [ $cod_usr ]['data'] = $data_comentario;

      // numero de itens no diario
      $lista_diarios [ $cod_usr ]['num_itens'] ++;

      // verifica se o item estah comentado. Se nao estiver, aumenta o numero de itens nao comentados no diario
      if (! isset ($lista_comentarios [ $cod_item ]))
        $lista_diarios [ $cod_usr ]['num_itens_nao_comentados'] ++;
      else
        $lista_diarios [ $cod_usr ]['num_itens_nao_comentados'] += 0;

      // ocorre novidade se houver um item novo ou um comentario novo
      $lista_diarios [ $cod_usr ]['novidade'] |= ($data_item > $penultimo_acesso || $data_comentario > $penultimo_acesso);
    }
  }

  uasort ($lista_diarios, Compara);

  return $lista_diarios;
}

/* ******************************************************
  RetornaItem - Retorna informa��es detalhadas sobre o item.

  Entradas: $sock - sock de conex�o,
            $cod_item - c�digo do item.

  Saida:    $cod_usuario - c�digo do usu�rio,
            $titulo - titulo do item,
            $texto - texto do item,
            $tipo_compartilhamento - tipo de compartilhamento do item
            $data - data do item.

*/

function RetornaItem($sock, $cod_item)
{
  /* Status do item que ser� selecionado.           */
  $status = 'A';

  $query = "select cod_usuario, titulo, texto, data, tipo_compartilhamento from Diario_itens where cod_item = ";
  $query .= $cod_item." and status = '".$status."'";

  $res = Enviar($sock, $query);
  $resultado = RetornaLinha($res);

  return($resultado);
}

/* ******************************************************
  RetornaItens - Retorna os itens do di�rio do usu�rio.

  Entradas: $sock - sock de conex�o,
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario logado
            $cod_usuario_diario - c�digo do usu�rio dono do diario.

  Saida:    $cod_item - c�digo do item,
            $titulo - titulo do item,
            $data - data do item.

*/

function RetornaItens ($sock, $cod_curso, $cod_usuario, $cod_usuario_diario)
{
  // selecionando quais itens do diario retornar
  $where = "cod_usuario=".$cod_usuario_diario;

  if (EFormadorComVisaoDeAluno ($sock, $cod_curso, $cod_usuario))
  {
    $where.= " and tipo_compartilhamento = 'T'";
  }
  else if ($cod_usuario != $cod_usuario_diario)
  {
    if ( EFormador ($sock, $cod_curso, $cod_usuario) )
    {
      $where .= " and (tipo_compartilhamento = 'T' or tipo_compartilhamento = 'F')";
    }
    else
    {
      $where .= " and tipo_compartilhamento = 'T'";
    }
  }

  // selecionando diarios
  $query = "select cod_item, titulo, data, tipo_compartilhamento from Diario_itens where status<>'D' and ".$where;
  $res   = Enviar ($sock, $query);
  $lista = RetornaArrayLinhas ($res);

  return $lista;
}

/* ******************************************************
  ApagaItem - Apaga o item especificado e seus coment�rios.

  Entradas: $sock - sock de conex�o,
            $cod_item - c�digo do item.

  Saida:    true se bem-sucedido, false do contr�rio.

*/

function ApagaItem($sock, $cod_item)
{
  /* Status do item e coment�rio que ser�o          */
  /* selecionados.                                  */
  $status_ativo = 'A';
  /* Status para o qual o item ou coment�rio ser�   */
  /* alterado.                                      */
  $status_apagado = 'D';

  /* "Apaga" os coment�rios.                        */
  $query = "update Diario_comentarios set status = '".$status_apagado."' where cod_item = ";
  $query .= $cod_item." and status = '".$status_ativo."'";

  $res1 = Enviar($sock, $query);

  /* "Apaga" o item.                                */
  $query = "update Diario_itens set status = '".$status_apagado."' where cod_item = ";
  $query .= $cod_item." and status = '".$status_ativo."'";

  $res2 = Enviar($sock, $query);

  return($res1 && $res2);
}


/* ******************************************************
  EditaItem - Edita o t�tulo e o texto, e atualiza a data
    do item especificado.

  Entradas: $sock - sock de conex�o,
            $cod_item - c�digo do item,
            $titulo - t�tulo do item,
            $texto - texto do item.
            $tipo_compartilhamento - tipo de compartilhamento do item

  Saida:    true se bem-sucedido, false do contr�rio.

*/

function EditaItem($sock, $cod_item, $titulo, $texto, $tipo_compartilhamento)
{
  /* Status do item que ser� selecionado.           */
  $status = 'A';

  $titulo = EliminaScript($titulo);
  $titulo = LimpaTitulo($titulo);
  $titulo = EliminaEspacos($titulo);

  $texto = EliminaScript($texto);
  $texto = LimpaTags($texto);

  $query = "update Diario_itens set titulo = '".$titulo."', texto = '";
  $query .= $texto."', data = ".time().", tipo_compartilhamento = '".$tipo_compartilhamento."' where cod_item = ".$cod_item;
  $query .= " and status = '".$status."'";

  $res = Enviar($sock, $query);

  return($res);
}


/* ******************************************************
  RenomeiaItem - Renomeia o item especificado.

  Entradas: $sock - sock de conex�o,
            $cod_item - c�digo do item,
            $titulo - t�tulo do item.

  Saida:    true se bem-sucedido, false do contr�rio.

*/

function RenomeiaItem($sock, $cod_item, $titulo)
{
  /* Status do item que ser� selecionado.           */
  $status = 'A';

  $titulo = EliminaScript($titulo);
  $titulo = LimpaTitulo($titulo);
  $titulo = EliminaEspacos($titulo);

  $query = "update Diario_itens set titulo = '".$titulo."', data = ".time();
  $query .= " where cod_item = ".$cod_item." and status = '".$status."'";

  $res = Enviar($sock, $query);

  return($res);
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
  $consulta="update Diario_itens set titulo='".htmlentities($novo_nome)."', data=".$data." where cod_item=".$cod_item." and status = 'A'";
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  // Imprime no div valores do formul?io
  $objResponse->addAssign("tr_".$cod_item, "className", "novoitem");
  $objResponse->addAssign("tit_".$cod_item, "innerHTML", htmlentities($novo_nome));
  //$objResponse->addAssign("tit_".$cod_item, "className", "linkTexto");

  $atualizacao='false';
  if($res) $atualizacao='true';

  $objResponse->addScriptCall("mostraFeedback", $texto, $atualizacao);  

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

function EditarTexto ($cod_curso, $cod_item, $novo_nome, $cod_usuario, $texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $novo_nome=ConverteAspas2BarraAspas($novo_nome);
  $sock=Conectar($cod_curso);

  $consulta="update Diario_itens set texto='".trim($novo_nome)."' where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  // Imprime no div valores do formulário
  $objResponse->addAssign("tr_".$cod_item, "className", "novoitem");
  $objResponse->addAssign("text_".$cod_item, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas($novo_nome)), true));

  $atualizacao='false';
  if($res) $atualizacao='true';

  $objResponse->addScriptCall("mostraFeedback", $texto, $atualizacao);  


  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* ******************************************************
  SalvaItem - Salva o item especificado e retorna seu cod_item

  Entradas: $sock                  - sock de conex�o,
            $titulo                - t�tulo do item,
            $texto                 - texto do item,
            $cod_usuario           - c�digo do usu�rio.
            $tipo_compartilhamento - tipo de compartilhamento do item

  Saida:    codigo do item se bem-sucedido, -1 do contr�rio.

*/

function SalvaItem($sock, $titulo, $texto, $cod_usuario, $tipo_compartilhamento)
{
  /* Status que ser� salvo com o item.              */
  $status = 'A';

  $titulo = EliminaScript($titulo);
  $titulo = LimpaTitulo($titulo);
  $titulo = EliminaEspacos($titulo);

  $texto = EliminaScript($texto);
  $texto = LimpaTags($texto);

  $data = time();

  $query = "insert into Diario_itens (cod_usuario, titulo, texto, data, status, tipo_compartilhamento) values";
  $query .= " (".$cod_usuario.", '".$titulo."', '".$texto."', ".time().", '".$status."', '".$tipo_compartilhamento."')";
  $res = Enviar($sock, $query);

  if($res)
  {
    $query = "select cod_item from Diario_itens where data=".$data;
    $res = Enviar($sock, $query);
    $linha = RetornaLinha($res);

    return $linha['cod_item'];
  }

  return -1;
}


/* ******************************************************
  ComentaItem - Comenta o item especificado.

  Entradas: $sock - sock de conex�o,
            $cod_item - c�digo do item,
            $comentario - coment�rio do item,
            $cod_usuario - c�digo do usu�rio.

  Saida:    true se bem-sucedido, false do contr�rio.

*/

function ComentaItem($sock, $cod_item, $comentario, $cod_usuario)
{
  /* Status do coment�rio que ser� salvo.           */
  $status = 'A';

  $comentario = EliminaScript($comentario);
  $comentario = LimpaConteudo($comentario);

  $query = "insert into Diario_comentarios (cod_item, comentario,";
  $query .= " cod_comentarist, data, status) values (".$cod_item;
  $query .= ", '".$comentario."', ".$cod_usuario.", ".time();
  $query .= ", '".$status."')";

  $res = Enviar($sock, $query);

  return($res);
}


/* ******************************************************
  ApagaComent - Apaga o coment�rio especificado.

  Entradas: $sock - sock de conex�o,
            $cod_comentario - c�digo do coment�rio.

  Saida:    true se bem-sucedido, false do contr�rio.

*/

function ApagaComent($sock, $cod_comentario)
{
  /* Status do coment�rio que ser� selecionado.     */
  $status_ativo = 'A';
  /* Status para o qual o coment�rio ser� alterado. */
  $status_apagado = 'D';

  $query = "update Diario_comentarios set status = '".$status_apagado."'";
  $query .= " where cod_comentario = ".$cod_comentario." and status = '";
  $query .= $status_ativo."'";

  return(Enviar($sock, $query));
}


/* ******************************************************
  RetornaComentario - Obt�m informa��es detalhadas sobre
    o coment�rio especificado.

  Entradas: $sock - sock de conex�o,
            $cod_comentario - c�digo do coment�rio.

  Saida:    $titulo - t�tulo do item,
            $cod_comentarist - c�digo do usu�rio que fez
              o coment�rio,
            $comentario - coment�rio do item,
            $data - data do coment�rio.

*/

function RetornaComentario($sock, $cod_comentario)
{
  /* Status do coment�rio que ser� selecionado.     */
  $status = 'A';

  $query = "select DI.titulo, DC.cod_comentarist, DC.comentario, DC.data";
  $query .=  " from Diario_itens as DI, Diario_comentarios as DC";
  $query .= " where DC.cod_comentario = ".$cod_comentario." and DC.status = '";
  $query .= $status."' and DI.cod_item = DC.cod_item";

  $res = Enviar($sock, $query);

  return(RetornaLinha($res));
}


/* ******************************************************
  TemComentario - Obt�m o cod_usuario dos usu�rios que
    fizeram coment�rios e a data do coment�rio mais
    recente. Esta fun��o � utilizada para sinalizar ao
    usu�rio se h� coment�rios, se ele ou outros usu�rios
    foram os autores e se o coment�rio � novo.

  Entradas: $sock - sock de conex�o,
            $cod_item - c�digo do item.

  Saida:    $titulo - t�tulo do item,
            $cod_comentarist - c�digo do usu�rio que fez
              o coment�rio,
            $comentario - coment�rio do item,
            $data - data do coment�rio.

*/

function TemComentarios($sock, $cod_item)
{
  /* Status do item que ser� selecionado.           */
  $status = 'A';

  $query = "select cod_comentarist from Diario_comentarios where cod_item = ";
  $query .= $cod_item." and status = '".$status."' group by cod_comentarist order";
  $query .= " by data desc";

  $res = Enviar($sock, $query);
  $tuplas = RetornaArrayLinhas($res);

  $query = "select data from Diario_comentarios where cod_item = ";
  $query .= $cod_item." and status = '".$status."' order by data desc limit 1";
  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);

  $data = $tupla['data'];

  return array($tuplas, $data);
}

/* ****************************************************************************************************
   RetornaListaComentarios - retorna lista de comentarios indicando se para cada item houve comentarios de
                             formador, ou do dono do item
   entrada: $sock - sock de conexao
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario logado
            $cod_usuario_diario - codigo do usuario dono do diario

   saida: array [ $cod_item ]['comentario_aluno'] - booleano. indica se ha comentarios de aluno no item
                [ $cod_item ]['comentario_formador'] - booleano. indica se ha comentarios de formador no item
                [ $cod_item ]['comentario_dono'] - booleano. indica se ha comentarios do dono do item no item
                [ $cod_item ]['novidade'] - booleano. indica se ha novos comentarios desde o ultimo acesso do usuario
*/

function RetornaListaComentarios ($sock, $cod_curso, $cod_usuario, $cod_usuario_diario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];


  $query = "select cod_usuario, tipo_usuario from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso;
  $res   = Enviar ($sock, $query);
  $lista = RetornaArrayLinhas ($res);
  if (is_array ($lista))
    foreach ($lista as $linha)
      $lista_usr [ $linha['cod_usuario'] ] = $linha ['tipo_usuario'];

  // se usuario que estah navegando for dono do diario, a regra eh apenas esta, sem compartilhamentos
  $regra = "where cod_usuario=".$cod_usuario_diario;

  if ($cod_usuario != $cod_usuario_diario)
  {
    if ( EFormador ($sock, $cod_curso, $cod_usuario))
    {
      // usuario que estah navegando nao eh dono, mas eh formador
      $regra.= " and (tipo_compartilhamento = 'T' or tipo_compartilhamento = 'F')";
    }
    else
    {
      // usuario que estah navegando nao eh dono do diario nem eh formador
      $regra.= " and tipo_compartilhamento = 'T'";
    }
  }

  $query = "select cod_item from Diario_itens ".$regra;
  $res   = Enviar ($sock, $query);
  $lista1= RetornaArrayLinhas ($res);

  if (is_array ($lista1))
  {
    foreach ($lista1 as $linha)
      $lista_itens [ $linha ['cod_item'] ] = true;

    $query = "select cod_item, cod_comentarist, data from Diario_comentarios where status='A' order by cod_item";
    $res   = Enviar ($sock, $query);
    $lista2= RetornaArrayLinhas ($res);

    $penultimo_acesso = PenultimoAcesso($sock, $cod_usuario, "");

    if (is_array ($lista2))
    {
      foreach ($lista2 as $linha)
      {
        if ( $lista_itens [ $cod_item = $linha ['cod_item'] ] )
        {
          $cod_comentarista = $linha ['cod_comentarist'];
          $data_comentario  = $linha ['data'];

          $lista_retorno [ $cod_item ]['novidade']              |=  ( $data_comentario > $penultimo_acesso );

          if ( $cod_comentarista == $cod_usuario )
          {
            $lista_retorno [ $cod_item ]['comentario_dono'] = true;
          }
          else
          {
            $lista_retorno [ $cod_item ]['comentario_aluno']    |=  ( $lista_usr [ $cod_comentarista ] == 'A' );
            $lista_retorno [ $cod_item ]['comentario_formador'] |=  ( $lista_usr [ $cod_comentarista ] == 'F' );
          }
        }
      }
    }
  }
  return $lista_retorno;
}

/* ******************************************************
   AlteraTipoCompartilhamento - Altera o tipo de compartilhamento de um item
   entrada: $sock - sock de conexao
            $cod_item - codigo do item
            $tipo_comp - novo modo de compartilhamento do item
                  'T' - totalmente compartilhado
                  'F' - compartilhado com formadores
                  'P' - N�o compartilhado
*/
function AlteraTipoCompartilhamento ($sock, $cod_item, $tipo_comp)
{
  $query = "update Diario_itens set tipo_compartilhamento='".$tipo_comp."' where cod_item=".$cod_item;
  $res   = Enviar ($sock, $query);
}

function MudaTipoCompartilhamento ($dados_form, $novo_comp)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $cod_curso = $dados_form['cod_curso'];
  $cod_item = $dados_form['cod_item'];
  $tipo_comp = $dados_form['tipo_comp'];
  $texto = $dados_form['texto'];

  $sock = Conectar($cod_curso);
  $query = "update Diario_itens set tipo_compartilhamento='".$tipo_comp."' where cod_item=".$cod_item;
  $res   = Enviar ($sock, $query);

  Desconectar($sock);

  $objResponse->addScript("MudaSpanCompartilhamento('span_".$cod_item."','".$novo_comp."','".$tipo_comp."',".$cod_item.");");

  $atualizacao = 'false';
  if($res) $atualizacao = 'true';

  $objResponse->addScriptCall("mostraFeedback", $texto, $atualizacao);  

  return $objResponse->getXML();
}

/* ******************************************************
   VerificaDonoDiario - verifica se o usuario eh dono do diario ou do item visualizado
   entrada: $sock - sock de conexao
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario logado
            $cod_propriet - codigo do usuario dono do diario
   saida: true - eh realmente o dono do diario
          false - nao eh o dono, ou estah na visao de aluno
*/
function VerificaDonoDiario ($sock, $cod_curso, $cod_usuario, $cod_propriet)
{
  return ( ($cod_usuario == $cod_propriet) && (!EFormadorComVisaoDeAluno ($sock, $cod_curso, $cod_usuario)) && (RetornaStatusCurso($sock,$cod_curso) != 'E') );
}

/* ******************************************************
  RetornaComentariosItem - Obt�m o cod_comentario, a data e o
    cod_comentarist dos coment�rios do item especificado.
    Lista os coment�rios e seus respectivos autores.

  Entradas: $sock - sock de conex�o,
            $cod_item - c�digo do item.

  Saida:    $cod_comentario - c�digo do coment�rio,
            $cod_comentarist - c�digo do usu�rio que fez
              o coment�rio,
            $data - data do coment�rio.

*/
function RetornaComentariosItem($sock, $cod_item)
{
  /* Status do item que ser� selecionado.           */
  $status = 'A';

  $query = "select cod_comentario, cod_comentarist, data from Diario_comentarios";
  $query .= " where cod_item = ".$cod_item." and status = '";
  $query .= $status."' order by data desc";

  $res = Enviar($sock, $query);

  return(RetornaArrayLinhas($res));

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
  return print_array_aux($array,0);
}

function print_array_aux($array,$level)
{
  if (!is_array($array))
    return 0;

  reset($array);
  $retorno = "";
  while ( list($key, $value) = each($array) )
  {
    for ($i = 0; $i < $level; ++$i)
    {
      $retorno .= "&nbsp;&nbsp;&nbsp;";
    }
    $retorno .= "['".$key."'] ".$value."<br>";
    if (is_array($value))
      $retorno .= print_array_aux($value,$level+1);
  }
}

/* ******************************************************
  RetornaCodProprietario - Obt�m o cod_propriet do item
  especificado em cod_item.

  Entradas: $sock - sock de conex�o,
            $cod_item - c�digo do item.

  Saida:    $cod_proprietario - c�digo do propriet�rio do item.

*/
function RetornaCodProprietario($sock, $cod_item)
{
  $query="select cod_usuario from Diario_itens where cod_item=".$cod_item;

  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  return ($linha['cod_usuario']);
}

/* ******************************************************
  RetornaCodProprietComentario - Obt�m o cod_propriet do item
  que tem o coment�rio especificado em cod_comentario.

  Entradas: $sock - sock de conex�o,
            $cod_comentario - c�digo do coment�rio.

  Saida:    $cod_proprietario - c�digo do propriet�rio do item.

*/
function RetornaCodProprietComentario($sock, $cod_comentario)
{
  $query="select cod_usuario from Diario_itens as I, Diario_comentarios as C ";
  $query.="where I.cod_item=C.cod_item and cod_comentario=".$cod_comentario;

  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  return ($linha['cod_usuario']);
}

/* *********************************************************************
   ExpulsaConvidado - verifica se o usuario eh convidado, se for, impede seu acesso
   entrada: $sock - sock de conexao
            $cod_usuario
   saida:   nenhuma
   OBS:     Para impedir o acesso, exibe tela com restricao de acesso e executa o comando exit
*/
function ExpulsaConvidado($sock, $cod_usuario, $cod_curso)
{
  global $lista_frases;
  
  if (EConvidado($sock, $cod_usuario, $cod_curso))
  {
    echo("<html>\n");
    /* 1 - Correio */
    echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=diario.css>\n");

    echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    /* 1 - Correio */
    $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases, 1)."</b>";
    /* 7 - �rea restrita a alunos e formadores */
    $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 7)."</b>";
    echo($cabecalho);

    echo("    <br>\n");
    echo("    <p>\n");
    echo("  </body>\n");
    echo("  </html>\n");
    Desconectar($sock);
    exit();
  }
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

/*********************************************************************
     ExcluirComentDinamic - Exclui os itens, dinamicamente
     Entrada: $cod_curso - codigo do curso
              $cod_usuario - codigo do usuario
              $cod_item - array com o codigo dos comentarios
 
     Saida: XML da fun��o Ajax
 */
  function ExcluirComentDinamic($cod_curso, $cod_itens, $cod_ferramenta, $cod_usuario){
 
    $objResponse = new xajaxResponse('ISO-8859-1');
 
    $sock=Conectar($cod_curso);
 
 
    foreach($cod_itens as $cod => $linha){
      ApagaComent($sock, $linha);
    }
    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
    Desconectar($sock);

    $objResponse->addScriptCall("Recarregar", "excluir_comentario", "true");

 
    return $objResponse->getXML();
 }

/*********************************************************************
     ApagaItensDinamic - Apaga os itens, dinamicamente
     Entrada: $cod_curso - codigo do curso
              $cod_item - array com o codigo das anotacoes
 
     Saida: XML da fun��o Ajax
 */
  function ApagaItensDinamic($cod_curso, $cod_itens,$cod_ferramenta, $cod_usuario){
 
    $objResponse = new xajaxResponse('ISO-8859-1');
 
    $sock=Conectar($cod_curso);
 
    foreach($cod_itens as $cod => $linha){
      ApagaItem($sock, $linha);
    }

    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
    Desconectar($sock);

    $objResponse->addScriptCall("Recarregar", "apagar_itens", "true");

 
    return $objResponse->getXML();
 }

?>