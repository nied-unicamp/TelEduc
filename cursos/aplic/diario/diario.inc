<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/diario/diario.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
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
  EliminaEspacos - Elimina espacos no comeï¿½o e no
    final da frase.
  Retorna a frase sem os espacos no comeï¿½o e no final dela.
  Entradas: $frase - frase
  Saida:   frase sem os espacos no comeï¿½o e no final dela.

*/

function EliminaEspacos($frase)
{
  return(trim($frase));
}

/* ******************************************************
  Compara - Compara os os elementos do array multidimensional
    pelo nome por ordem natural e sem sensibilidade ï¿½ caixa
    das letras.
    Esta funï¿½ï¿½o ï¿½ utilizada pela funï¿½ï¿½o usort de
    RetornaDiarios como critï¿½rio de ordenaï¿½ï¿½o.

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
function RetornaDiarios($sock, $cod_curso, $cod_usuario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  if ( EFormadorComVisaoDeAluno ($sock, $cod_curso, $cod_usuario))
  {
    $regra_comp = "tipo_compartilhamento = 'T'";
  }
  else if (! EFormador ($sock, $cod_curso, $cod_usuario))
  {
    $regra_comp = "(tipo_compartilhamento = 'T' or UC.cod_usuario=".$cod_usuario.")";
  }
  else
  {
    $regra_comp = "(tipo_compartilhamento = 'T' or tipo_compartilhamento = 'F' or UC.cod_usuario=".$cod_usuario.")";
  }

  // Lista dos itens de diarios e seus respectivos usuários.
  $query  = "select DI.cod_item as codigo_item, DI.cod_usuario, U.nome, ";
  $query .= "       MAX(DI.data) as data_item, MAX(DC.data) as data_comentario ";
  $query .= "from ".$dbnamebase.".Usuario U ";
  $query .= "inner join ".$dbnamebase.".Usuario_curso UC on U.cod_usuario  = UC.cod_usuario_global ";
  $query .= "inner join Diario_itens                  DI on DI.cod_usuario = UC.cod_usuario ";
  $query .= "left  join Diario_comentarios            DC on DC.cod_item    = DI.cod_item ";
  $query .= "where UC.cod_curso = ".$cod_curso." and ";
  // Retornamos diários de usuários que não estão desligados.
  $query .= "      (binary UC.tipo_usuario = 'F' or ";
  $query .= "       binary UC.tipo_usuario = 'A' or ";
  $query .= "       binary UC.tipo_usuario = 'V' or ";
  $query .= "       binary UC.tipo_usuario = 'Z') and ";
  $query .=        $regra_comp." and ";
  $query .= "      DI.status = 'A' ";
  $query .= "group by DI.cod_item ";
  $query .= "order by DI.cod_usuario";

  $res         = Enviar ($sock, $query);
  $lista_itens = RetornaArrayLinhas ($res);
  
  if (is_array ($lista_itens))
  {

    $penultimo_acesso = PenultimoAcesso($sock, $cod_usuario, "");

    foreach ($lista_itens as $item)
    {

      $cod_usr   = $item['cod_usuario'];
      $data_item = max($item['data_item'], $item['data_comentario']);
      $cod_item = $item['codigo_item'];
      
      $foi_comentado = ItemFoiComentado ($sock, $cod_item);

      if (!isset($lista_diarios[$cod_usr])) {
        $lista_diarios[$cod_usr]['nome']                     = $item['nome'];
        $lista_diarios[$cod_usr]['data']                     = $data_item;
        $lista_diarios[$cod_usr]['num_itens']                = 0;
        $lista_diarios[$cod_usr]['num_itens_nao_comentados'] = 0;
        $lista_diarios[$cod_usr]['novidade']                 = 0;
      }
      else if ($lista_diarios[$cod_usr]['data'] < $data_item)
        // Retornar data do item mais novo (item mais novo = maior data)
        // dentro do diario ou do comentario mais novo.
        $lista_diarios[$cod_usr]['data']                     = $data_item;

      // numero de itens no diario
      $lista_diarios[$cod_usr]['num_itens']++;

      // ocorre novidade se houver um item novo ou um comentario novo
      $lista_diarios[$cod_usr]['novidade'] |= $item['data'] > $penultimo_acesso;

      // Verifica se o item estah comentado. Se nao estiver,
      // aumenta o numero de itens nao comentados no diario.
      if (!$foi_comentado)
        $lista_diarios[$cod_usr]['num_itens_nao_comentados']++;

    }
  }

  // Garantindo que o usuario logado vai ter seu
  // diario aparecendo, mesmo que este esteja vazio.
  if (!isset($lista_diarios[$cod_usuario])) {
    $lista_diarios[$cod_usuario]['nome']                     = NomeUsuario($sock, $cod_usuario, $cod_curso);
    $lista_diarios[$cod_usuario]['data']                     = NULL;
    $lista_diarios[$cod_usuario]['num_itens']                = 0;
    $lista_diarios[$cod_usuario]['num_itens_nao_comentados'] = 0;
  }
  uasort ($lista_diarios, Compara);

  return $lista_diarios;
}

function ItemFoiComentado ($sock, $cod_item){
  $status = 'A';
  
  $query = "select count(cod_comentario) as quantos from Diario_comentarios where status = '".$status."'";
  $query .=" and cod_item = ".$cod_item."";
  $res = Enviar($sock, $query);
  $qtd = RetornaLinha($res);
  
  if($qtd != NULL){
    if ($qtd['quantos'] != 0){
      return true;
    }
  }
  return false;
 
}

/* ******************************************************
  RetornaItem - Retorna informaï¿½ï¿½es detalhadas sobre o item.

  Entradas: $sock - sock de conexï¿½o,
            $cod_item - cï¿½digo do item.

  Saida:    $cod_usuario - cï¿½digo do usuï¿½rio,
            $titulo - titulo do item,
            $texto - texto do item,
            $tipo_compartilhamento - tipo de compartilhamento do item
            $data - data do item.

*/

function RetornaItem($sock, $cod_item)
{
  /* Status do item que serï¿½ selecionado.           */
  $status = 'A';

  $query = "select cod_usuario, titulo, texto, data, tipo_compartilhamento from Diario_itens where cod_item = ";
  $query .= $cod_item." and status = '".$status."'";

  $res = Enviar($sock, $query);
  $resultado = RetornaLinha($res);

  return($resultado);
}

/* ******************************************************
  RetornaItens - Retorna os itens do diï¿½rio do usuï¿½rio.

  Entradas: $sock - sock de conexï¿½o,
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario logado
            $cod_usuario_diario - cï¿½digo do usuï¿½rio dono do diario.

  Saida:    $cod_item - cï¿½digo do item,
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
  ApagaItem - Apaga o item especificado e seus comentï¿½rios.

  Entradas: $sock - sock de conexï¿½o,
            $cod_item - cï¿½digo do item.

  Saida:    true se bem-sucedido, false do contrï¿½rio.

*/

function ApagaItem($sock, $cod_item)
{
  /* Status do item e comentï¿½rio que serï¿½o          */
  /* selecionados.                                  */
  $status_ativo = 'A';
  /* Status para o qual o item ou comentï¿½rio serï¿½   */
  /* alterado.                                      */
  $status_apagado = 'D';

  /* "Apaga" os comentï¿½rios.                        */
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
  EditaItem - Edita o tï¿½tulo e o texto, e atualiza a data
    do item especificado.

  Entradas: $sock - sock de conexï¿½o,
            $cod_item - cï¿½digo do item,
            $titulo - tï¿½tulo do item,
            $texto - texto do item.
            $tipo_compartilhamento - tipo de compartilhamento do item

  Saida:    true se bem-sucedido, false do contrï¿½rio.

*/

function EditaItem($sock, $cod_item, $titulo, $texto, $tipo_compartilhamento)
{
  /* Status do item que serï¿½ selecionado.           */
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

  Entradas: $sock - sock de conexï¿½o,
            $cod_item - cï¿½digo do item,
            $titulo - tï¿½tulo do item.

  Saida:    true se bem-sucedido, false do contrï¿½rio.

*/

function RenomeiaItem($sock, $cod_item, $titulo)
{
  /* Status do item que serï¿½ selecionado.           */
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
   EditarTitulo - Edita o tÃ­tulo do item dado, dinÃ¢micamente
   Entrada: $cod_curso - cÃ³digo do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estarÃ¡ associado
            $novo_nome - novo tÃ­tulo do item
            $cod_usuario - cÃ³digo do usuÃ¡rio que estÃ¡ utilizando
   Saida: XML da funÃ§Ã£o Ajax
*/
function EditarTitulo ($cod_curso, $cod_item, $novo_nome, $cod_usuario, $texto)
{
  $objResponse = new xajaxResponse();

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $sock=Conectar($cod_curso);
  $consulta="update Diario_itens set titulo='".htmlentities($novo_nome)."', data=".$data." where cod_item=".$cod_item." and status = 'A'";
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  // Imprime no div valores do formul?io
  $objResponse->assign("tr_".$cod_item, "className", "novoitem");
  $objResponse->assign("tit_".$cod_item, "innerHTML", htmlentities($novo_nome));
  //$objResponse->assign("tit_".$cod_item, "className", "linkTexto");

  $atualizacao='false';
  if($res) $atualizacao='true';

  $objResponse->call("mostraFeedback", $texto, $atualizacao);  

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

function EditarTexto ($cod_curso, $cod_item, $novo_nome, $cod_usuario, $texto)
{
  $objResponse = new xajaxResponse();

  $novo_nome=ConverteAspas2BarraAspas($novo_nome);
  $sock=Conectar($cod_curso);

  $consulta="update Diario_itens set texto='".trim($novo_nome)."' where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  // Imprime no div valores do formulÃ¡rio
  $objResponse->assign("tr_".$cod_item, "className", "novoitem");
  $objResponse->assign("text_".$cod_item, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas($novo_nome)), true));

  $atualizacao='false';
  if($res) $atualizacao='true';

  $objResponse->call("mostraFeedback", $texto, $atualizacao);  


  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* ******************************************************
  SalvaItem - Salva o item especificado e retorna seu cod_item

  Entradas: $sock                  - sock de conexï¿½o,
            $titulo                - tï¿½tulo do item,
            $texto                 - texto do item,
            $cod_usuario           - cï¿½digo do usuï¿½rio.
            $tipo_compartilhamento - tipo de compartilhamento do item

  Saida:    codigo do item se bem-sucedido, -1 do contrï¿½rio.

*/

function SalvaItem($sock, $titulo, $texto, $cod_usuario, $tipo_compartilhamento)
{
  /* Status que serï¿½ salvo com o item.              */
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

  Entradas: $sock - sock de conexï¿½o,
            $cod_item - cï¿½digo do item,
            $comentario - comentï¿½rio do item,
            $cod_usuario - cï¿½digo do usuï¿½rio.

  Saida:    true se bem-sucedido, false do contrï¿½rio.

*/

function ComentaItem($sock, $cod_item, $comentario, $cod_usuario)
{
  /* Status do comentï¿½rio que serï¿½ salvo.           */
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
  ApagaComent - Apaga o comentï¿½rio especificado.

  Entradas: $sock - sock de conexï¿½o,
            $cod_comentario - cï¿½digo do comentï¿½rio.

  Saida:    true se bem-sucedido, false do contrï¿½rio.

*/

function ApagaComent($sock, $cod_comentario)
{
  /* Status do comentï¿½rio que serï¿½ selecionado.     */
  $status_ativo = 'A';
  /* Status para o qual o comentï¿½rio serï¿½ alterado. */
  $status_apagado = 'D';

  $query = "update Diario_comentarios set status = '".$status_apagado."'";
  $query .= " where cod_comentario = ".$cod_comentario." and status = '";
  $query .= $status_ativo."'";

  return(Enviar($sock, $query));
}


/* ******************************************************
  RetornaComentario - Obtï¿½m informaï¿½ï¿½es detalhadas sobre
    o comentï¿½rio especificado.

  Entradas: $sock - sock de conexï¿½o,
            $cod_comentario - cï¿½digo do comentï¿½rio.

  Saida:    $titulo - tï¿½tulo do item,
            $cod_comentarist - cï¿½digo do usuï¿½rio que fez
              o comentï¿½rio,
            $comentario - comentï¿½rio do item,
            $data - data do comentï¿½rio.

*/

function RetornaComentario($sock, $cod_comentario)
{
  /* Status do comentï¿½rio que serï¿½ selecionado.     */
  $status = 'A';

  $query = "select DI.titulo, DC.cod_comentarist, DC.comentario, DC.data";
  $query .=  " from Diario_itens as DI, Diario_comentarios as DC";
  $query .= " where DC.cod_comentario = ".$cod_comentario." and DC.status = '";
  $query .= $status."' and DI.cod_item = DC.cod_item";

  $res = Enviar($sock, $query);

  return(RetornaLinha($res));
}


/* ******************************************************
  TemComentario - Obtï¿½m o cod_usuario dos usuï¿½rios que
    fizeram comentï¿½rios e a data do comentï¿½rio mais
    recente. Esta funï¿½ï¿½o ï¿½ utilizada para sinalizar ao
    usuï¿½rio se hï¿½ comentï¿½rios, se ele ou outros usuï¿½rios
    foram os autores e se o comentï¿½rio ï¿½ novo.

  Entradas: $sock - sock de conexï¿½o,
            $cod_item - cï¿½digo do item.

  Saida:    $titulo - tï¿½tulo do item,
            $cod_comentarist - cï¿½digo do usuï¿½rio que fez
              o comentï¿½rio,
            $comentario - comentï¿½rio do item,
            $data - data do comentï¿½rio.

*/

function TemComentarios($sock, $cod_item)
{
  /* Status do item que serï¿½ selecionado.           */
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
                  'P' - Nï¿½o compartilhado
*/
function AlteraTipoCompartilhamento ($sock, $cod_item, $tipo_comp)
{
  $query = "update Diario_itens set tipo_compartilhamento='".$tipo_comp."' where cod_item=".$cod_item;
  $res   = Enviar ($sock, $query);
}

function MudaTipoCompartilhamento ($dados_form, $novo_comp)
{
  $objResponse = new xajaxResponse();

  $cod_curso = $dados_form['cod_curso'];
  $cod_item = $dados_form['cod_item'];
  $tipo_comp = $dados_form['tipo_comp'];
  $texto = $dados_form['texto'];

  $sock = Conectar($cod_curso);
  $query = "update Diario_itens set tipo_compartilhamento='".$tipo_comp."' where cod_item=".$cod_item;
  $res   = Enviar ($sock, $query);

  Desconectar($sock);

  $objResponse->script("MudaSpanCompartilhamento('span_".$cod_item."','".$novo_comp."','".$tipo_comp."',".$cod_item.");");

  $atualizacao = 'false';
  if($res) $atualizacao = 'true';

  $objResponse->call("mostraFeedback", $texto, $atualizacao);  

  return $objResponse;
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
  RetornaComentariosItem - Obtï¿½m o cod_comentario, a data e o
    cod_comentarist dos comentï¿½rios do item especificado.
    Lista os comentï¿½rios e seus respectivos autores.

  Entradas: $sock - sock de conexï¿½o,
            $cod_item - cï¿½digo do item.

  Saida:    $cod_comentario - cï¿½digo do comentï¿½rio,
            $cod_comentarist - cï¿½digo do usuï¿½rio que fez
              o comentï¿½rio,
            $data - data do comentï¿½rio.

*/
function RetornaComentariosItem($sock, $cod_item)
{
  /* Status do item que serï¿½ selecionado.           */
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
  RetornaCodProprietario - Obtï¿½m o cod_propriet do item
  especificado em cod_item.

  Entradas: $sock - sock de conexï¿½o,
            $cod_item - cï¿½digo do item.

  Saida:    $cod_proprietario - cï¿½digo do proprietï¿½rio do item.

*/
function RetornaCodProprietario($sock, $cod_item)
{
  $query="select cod_usuario from Diario_itens where cod_item=".$cod_item;

  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  return ($linha['cod_usuario']);
}

/* ******************************************************
  RetornaCodProprietComentario - Obtï¿½m o cod_propriet do item
  que tem o comentï¿½rio especificado em cod_comentario.

  Entradas: $sock - sock de conexï¿½o,
            $cod_comentario - cï¿½digo do comentï¿½rio.

  Saida:    $cod_proprietario - cï¿½digo do proprietï¿½rio do item.

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
   ExpulsaVisitante - Verifica se o usuario eh visitante, se for, impede
                      seu acesso.
   entrada: $sock - sock de conexao
            $cod_curso   - código do curso atual.
            $cod_usuario - código do usuário o qual pretende-se testar o
                           acesso à ferramenta correio.
            $eh_popup    - booleano que indica se a página que o chamou
                           é ou não um popup. Se não for, devemos fechar
                           as tags html do template comum de uma página
                           de ferramenta (com rodapé, fechando o tabelão,
                           etc).
   saida:   nenhuma
   OBS:     Para impedir o acesso, exibe tela com restricao de acesso e
            executa o comando exit
*/
function ExpulsaVisitante($sock, $cod_curso, $cod_usuario, $eh_popup = false)
{
  global $lista_frases;
  global $lista_frases_geral;
  
  if (EVisitante($sock, $cod_curso, $cod_usuario))
  {
    /* 1 - Correio */
    if ($eh_popup) {
      echo("  </head>\n");
      echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=\"white\">\n");
      echo("    <h3 style=\"margin-top:20px;\">".NomeCurso($sock,$cod_curso)."</h3>\n");
    }

    /* 1 - Correio 504 - Área restrita a alunos e formadores */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases_geral, 504)."</h4>\n");

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
    echo("  </html>\n");
    Desconectar($sock);
    exit();
  }
}

/* *********************************************************************
   IndentarComentario - Indenta o texto com os espaÃ§os passados por parÃ¢metro
   Entrada: $texto - texto a ser alterado
            $spaces - espaÃ§os a serem acrescentados

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
 
     Saida: XML da funï¿½ï¿½o Ajax
 */
  function ExcluirComentDinamic($cod_curso, $cod_itens, $cod_ferramenta, $cod_usuario){
 
    $objResponse = new xajaxResponse();
 
    $sock=Conectar($cod_curso);
 
 
    foreach($cod_itens as $cod => $linha){
      ApagaComent($sock, $linha);
    }
    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
    Desconectar($sock);

    $objResponse->call("Recarregar", "excluir_comentario", "true");

 
    return $objResponse;
 }

/*********************************************************************
     ApagaItensDinamic - Apaga os itens, dinamicamente
     Entrada: $cod_curso - codigo do curso
              $cod_item - array com o codigo das anotacoes
 
     Saida: XML da funï¿½ï¿½o Ajax
 */
function ApagaItensDinamic($cod_curso, $cod_itens,$cod_ferramenta, $cod_usuario){

  $objResponse = new xajaxResponse();

  $sock=Conectar($cod_curso);

  foreach($cod_itens as $cod => $linha){
    ApagaItem($sock, $linha);
  }

  AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
  Desconectar($sock);

  $objResponse->call("Recarregar", "apagar_itens", "true");

  return $objResponse;
}

?>