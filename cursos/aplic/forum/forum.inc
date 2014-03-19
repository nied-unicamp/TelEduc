<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/forum/forum.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
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

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/forum/forum.inc
  ========================================================== */


/* *********************************************************
  EliminaEspacos - Elimina espacos no começo e no
                   final da frase.
  Entradas: $frase - frase
  Saida: frase sem os espaços no começo e no final dela.
************************************************************/
function EliminaEspacos($frase)
{
  return(trim($frase));
}


/* ******************************************************
  ForumExiste - Verifica a existecia do forum.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum.
  Saida: true se existir, do contrario, false.
********************************************************/
function ForumExiste($sock, $cod_forum)
{
  $query = "select nome from Forum where cod_forum = ".$cod_forum;

  $res = Enviar($sock, $query);

  if (RetornaNumLinhas($res) > 0)
    return(true);
  else
    return(false);
}

/* ******************************************************************************************************
  RetornaForuns - lista os foruns nao apagados e permitidos.
  Entradas: $sock - sock de conexao,
            $ordem - "N" - Nome, "D" - data
	    $cod_usuario - codigo do usuario
  Saida:    Array com: ['nome'] - nome do forum,
                       ['cod_forum'] - codigo do forum,
                       ['status'] - status do forum: A - Ativo (escrita e leitura)
                                                     L - somente leitura
                                                     D - deletado (encontra-se na lixeira)
                                                     X - excluido
						     G - Define os alunos com permissao de Escrita e Leitura
						     R - Define os alunos com permissao de Escrita (a leitura     é aberta para todos)
                       ['data'] - data de criaçao do Forum
		       ['permissoes'] - usuarios/grupos com acesso permitido
            Total de foruns encontrados.
**********************************************************************************************************/
function RetornaForuns($sock,$ordem,$cod_usuario)
{
   global $cod_curso;
  
   /* Coordenador e Formadores tem os mesmo provilégios no forum, certo? */
   if (ECoordenador($sock,$cod_curso,$cod_usuario) || EFormador($sock, $cod_curso, $cod_usuario)){
        $query = "select nome, cod_forum, status, data from Forum where ";
        $query .= "status = 'A' or status = 'L' or status='R' or status='G' order by " ;
   } else {
        $grupos=RetornaGruposUsuario($sock,$cod_usuario);

         $query = "select distinct F.nome, F.cod_forum, F.status, F.data from Forum as F left join Forum_permissoes as FP on FP.cod_forum=F.cod_forum where F.status = 'A' or F.status = 'L' or F.status = 'R' or (F.status = 'G' and FP.cod_forum=F.cod_forum and FP.cod_permitido='".$cod_usuario."' and FP.tipo='U')";
        foreach($grupos as $cod => $g_linha)
          $query .= " or (F.status = 'G' and FP.cod_permitido='".$g_linha[$cod]."' and FP.tipo='G')";
        $query .= " order by ";  
  }	

  if ($ordem=="N")
    $query .="nome";
  else
    $query .= "data desc";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  foreach($tuplas as $cod => $linha){

    $query = "Select cod_permitido, tipo from Forum_permissoes where cod_forum=".$linha['cod_forum'];
    $res = Enviar($sock, $query);
    $permitidos_linha = RetornaArrayLinhas($res);
    $tuplas[$cod]['permissoes'] = $permitidos_linha;  //nao pode usar $linha, pois não eh referencia
  }

  $nMsgs = array();

  for ($i = 0; $i < $total; $i++)
  {
    /* Seleciona as mensagens ativas e obt� o total de mensagens pertencentes �cada f�um. */
    $query = "select count(*),max(data) 'data' from Forum_mensagens where cod_forum = ".$tuplas[$i]['cod_forum'];
    $query .= " and status = 'A'";

    $res = Enviar($sock, $query);
    $valor = RetornaLinha($res);
    /* Insere no array os totais de mensagens de cada f�um.                                 */
    array_push($nMsgs, $valor[0]);

    if ($valor['data']>0)
	$tuplas[$i]['data']=$valor['data'];
  }
  
  /* Ordena�o por Selecao */
  /* Coloca os foruns que foram utilizados por ultimo (alguma mensagem foi postada) em primeiro na ordenacao por data dos foruns */
  if ($ordem == 'D')
  {
      for ($i = 0; $i < $total; $i++)
      {
	  $min = $i;
	  for ($j = $i; $j < $total ; $j++)
	      if ($tuplas[$j]['data'] > $tuplas[$min]['data'])
		  $min = $j;
	  $t[$i] = $tuplas[$i];
	  $s[$i] = $nMsgs[$i];
	  
	  $tuplas[$i] = $tuplas[$min];
	  $nMsgs[$i] = $nMsgs[$min];
	  
	  $tuplas[$min] = $t[$i];
	  $nMsgs[$min] = $s[$i];
      }
  }
  /* Fim da Ordenacao */

  return array ($tuplas, $nMsgs, $total);
}


/* *************************************************************************************************
  RetornaForunsDeletados - lista os foruns deletados.
  Entradas: $sock - sock de conexao,
            $ordem - "N" - nome, "D" - Data
  Saida:    Array com: ['nome'] - nome do forum,
                       ['cod_forum'] - codigo do forum,
                       ['status'] - status do forum: A - Ativo (escrita e leitura)
                                                     L - somente leitura
                                                     D - deletado (encontra-se na lixeira)
                                                     X - excluido
                       ['data'] - data de criaçao do forum
            Total de foruns encontrados.
***************************************************************************************************/
function RetornaForunsDeletados($sock,$ordem)
{
  $query = "select nome, cod_forum, status, data from Forum where ";
  $query .= "status = 'D' order by ";

  if ($ordem=="N")
    $query .="nome";
  else
    $query .="data desc";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  /* Inicializa o array. */
  $nMsgs = array();

  for ($i = 0; $i < $total; $i++)
  {
    /* Seleciona as mensagens deletadas e obt� o total de mensagens pertencentes �cada f�um. */
    $query = "select count(*) from Forum_mensagens where cod_forum = ".$tuplas[$i]['cod_forum'];
    $query .= " and status = 'A'";

    $res = Enviar($sock, $query);
    $valor = RetornaLinha($res);

    /* Insere no array os totais de mensagens de cada f�um.             */
    array_push($nMsgs, $valor[0]);
  }

  return array ($tuplas, $nMsgs, $total);
}


/* **********************************************************************************************
  RetornaForum - lista os foruns nao apagados.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum.
  Saida:    Array com: ['nome'] - nome do forum,
                       ['status'] - status do forum: A - Ativo (escrita e leitura)
                                                     L - somente leitura
                                                     D - deletado (encontra-se na lixeira)
                                                     X - excluido
		       ['permissoes'] - array com codigo dos usuarios e grupos com permissao

****************************************************************************************************/
function RetornaForum($sock, $cod_forum)
{
  $query = "Select cod_forum, status, nome from Forum where cod_forum = ".$cod_forum;
  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);

  $query = "Select cod_permitido, tipo from Forum_permissoes where cod_forum = ".$cod_forum;
  $res = Enviar($sock, $query);
  $tupla2 = RetornaArrayLinhas($res);

  $tupla['permissoes'] = $tupla2;

  return($tupla);
}


/* *********************************************************************************
  ConfiguraForum - Altera o status do forum entre somente
                   leitura e ativado.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum,
            $status - status do forum:  A - Ativo (escrita e leitura)
                                        L - somente leitura
                                        D - deletado (encontra-se na lixeira)
                                        X - excluido
  Saida:    true se bem-sucedido, do contrario, false.
*************************************************************************************/
function ConfiguraForum($sock, $cod_forum, $status)
{
  $query = "update Forum set status='".$status."' where cod_forum = ".$cod_forum;

  $res = Enviar($sock, $query);

  return($res);
}


/* ******************************************************
  DeletaForum - Deleta o forum movendo para a Lixeira.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum.
  Saida: true se bem-sucedido, do contrario, false.
*********************************************************/
function DeletaForum($sock, $cod_forum)
{
  $query = "update Forum set status = 'D' where cod_forum = ".$cod_forum;
  $query .= " and (status = 'A' or status = 'L' or status = 'G' or status = 'R')";

  $res2 = Enviar($sock, $query);

  return($res2);
}


/* ******************************************************
  RenomeiaForum - Renomeia o forum especificado.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum,
            $nome - nome do forum.
  Saida:    true se bem-sucedido, do contrario, false.
*********************************************************/
function RenomeiaForum($sock, $cod_forum, $nome)
{
  $nome = EliminaScript($nome);
  $nome = LimpaTitulo($nome);
  $nome = EliminaEspacos($nome);

  $query = "update Forum set nome = '".$nome."' where cod_forum = ".$cod_forum;

  $res = Enviar($sock, $query);

  return($res);
}


/* ******************************************************
  SalvaForum - Salva o forum na base de dados.
  Entradas: $sock - sock de conexao,
            $nome - nome do forum.
  Saida:    true se bem-sucedido, do contrario, false.
**********************************************************/
function SalvaForum($sock, $nome)
{
  $nome = EliminaScript($nome);
  $nome = LimpaTitulo($nome);
  $nome = EliminaEspacos($nome);

  $query = "insert into Forum (nome, status, data)";
  $query .= " values ('".$nome."', 'A', ".time().")";

  return(Enviar($sock, $query));
}


/* ********************************************************************************************
  MensagemAlteradas - Verifica se alguma mensagem foi inserida/alterada. Se retornar true 
                      entao deve-se consultar a tabela de mensagens e listar todas as
                      mensagens. Do contrario usa-se apenas as mensagens guardadas em um 
                      array na memoria.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum,
            $ultima_data_alteracao - data anterior a consulta a tabela de mensagens.
  Saida:    true se alterada/inserida, do contrario, false.
***********************************************************************************************/
function MensagensAlteradas($sock, $cod_forum, $ultima_data_alteracao)
{
  $query = "select * from Forum_mensagens where cod_forum = ".$cod_forum;
  $query .= " and data > ".$ultima_data_alteracao;
  $res = Enviar($sock, $query);

  $numreg = RetornaNumLinhas($res);

  if ($numreg > 0)
    return(true);
  else
    return(false);
}


/* ***************************************************************************************************
  RetornaListagemCompletaRec - Recursivamente, cria uma lista com todas as mensagens, 
                               guardando em uma variavel, e em outra variavel cria os links 
                               das mensagens.
  Entradas: $sock - sock de conexao,
            $cod_msg_pai = codigo da mensagem-pai,
            $cod_forum - codigo do forum,
            $status - A - Ativo,
                      D - Deletado,
                      X - Excluido.
            $nivel - nivel das mensagens na arvore,
            &$msg - variavel que guarda os links das mensagens,
            &$NumMsg - numero de mensagens.
  Saida: Modifica as variaveis:
              &$msg - array com a estrura: ['cod_msg']['nivel'] - nivel da mensagem na arvore,
                                           ['cod_msg']['cod_usuario'] - codigo usuario que postou,
                                                                        a mensagem,
                                           ['cod_msg']['titulo'] - titulo da mensagem,
                                           ['cod_msg']['mensagem'] - texto da mensagem,
                                           ['cod_msg']['data'] - data da mensagem.
              &$NumMsg - numero de mensagens,
*******************************************************************************************************/
function RetornaListagemCompletaRec($sock, $cod_msg_pai, $cod_forum, $status,
  $nivel, &$msg, &$NumMsg)
{
  $query = "select cod_msg, cod_usuario, titulo, mensagem, data from ";
  $query .= "Forum_mensagens where cod_forum = ".$cod_forum." and ";
  $query .= "status = 'A' and cod_msg_pai = ".$cod_msg_pai." order by data";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  for ($i = 0; $i < $total; $i++)
  {
    $msg[$tuplas[$i]['cod_msg']]['nivel'] = $nivel;
    $msg[$tuplas[$i]['cod_msg']]['cod_usuario'] = $tuplas[$i]['cod_usuario'];
    $msg[$tuplas[$i]['cod_msg']]['titulo'] = $tuplas[$i]['titulo'];
    $msg[$tuplas[$i]['cod_msg']]['mensagem'] = $tuplas[$i]['mensagem'];
    $msg[$tuplas[$i]['cod_msg']]['data'] = $tuplas[$i]['data'];

    /* Incrementa o contador de mensagens.                        */
    $NumMsg++;

    /* Imprime as respostas recursivamente */
    RetornaListagemCompletaRec($sock, $tuplas[$i]['cod_msg'], $cod_forum, $status,
      $nivel + 1, $msg, $NumMsg);
  }
}


/* ************************************************************************************************************
  RetornaListagemCompleta - Cria uma lista com todas as mensagens-raiz, guardando em uma variavel, 
                            e em outra variavel cria os links das mensagens-raiz. Chama a funcao
                            RetornaListagemCompletaRec que faz o mesmo nos subniveis.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum,
            $status - A - Ativo,
                      D - Deletado,
                      X - Excluido.
            $cod_usuario - codigo do usuario.
  Saida:    array contendo: $NumMsg - total de mensagens,
                            $msg - array com a estrura: ['cod_msg']['nivel'] - nivel da mensagem na arvore,
                                                        ['cod_msg']['cod_usuario'] - codigo do usuario que 
										     postou a mensagem,
                     					['cod_msg']['titulo'] - titulo da mensagem,
                     					['cod_msg']['mensagem'] - texto da mensagem,
                     					['cod_msg']['data'] - data da mensagem.
*************************************************************************************************************/
function RetornaListagemCompleta($sock, $cod_forum, $status, $cod_usuario)
{
  /* Nmero de mensagens */
  $NumMsg = 0;
  /* Nivel das mensagens na �vore. Utilizado para identa�o. */
  $nivel = 0;

  $query = "select cod_msg, cod_usuario, titulo, mensagem, data from ";
  $query .= "Forum_mensagens where cod_forum = ".$cod_forum." and ";
  $query .= "status = 'A' and cod_msg_pai is NULL order by data";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  for ($i = 0; $i < $total; $i++)
  {
    $msg[$tuplas[$i]['cod_msg']]['nivel'] = $nivel;
    $msg[$tuplas[$i]['cod_msg']]['cod_usuario'] = $tuplas[$i]['cod_usuario'];
    $msg[$tuplas[$i]['cod_msg']]['titulo'] = $tuplas[$i]['titulo'];
    $msg[$tuplas[$i]['cod_msg']]['mensagem'] = $tuplas[$i]['mensagem'];
    $msg[$tuplas[$i]['cod_msg']]['data'] = $tuplas[$i]['data'];

    /* Incrementa o contador de mensagens.                        */
    $NumMsg++;

    /* Imprime as respostas recursivamente */
    RetornaListagemCompletaRec($sock, $tuplas[$i]['cod_msg'], $cod_forum, $status,
      $nivel + 1, $msg, $NumMsg);
  }
  return array($NumMsg, $msg);
}


/* ****************************************************************************************************
  RetornaMensagensRec - Funcao recursiva chamada pela funcao RetornaMensagens. Aprofunda procurando
                        mensagens-filhas (reply's) e altera o array, no qual sao armazenadas, passado
                        por referencia.
  Entradas: $sock - sock de conexao,
            $cod_msg_pai - codigo da mensagem-pai,
            $cod_forum - codigo do forum,
            $status - A - Ativo
                      D - Deletado,
                      X - Excluido.
            $msgperpag - numero de mensagens por pagina,
            $MAXSTRING - tamanho maximo de caracteres para truncar o titulo das mensagens,
            $nivel - nivel da mensagem na avore (utilizado para identar as mensagens),
            &$pag - numero da pagina atual,
            &$cont_msg - contador de mensagens por pagina,
            &$NumMsg - numero de mensagens,
            &$arraymsg - array que guarda as mensagens,
            $sinpag - array que sinaliza quais as paginas possuem mensagens novas,
            $penult_acesso - data do penultimo acesso.
  Saida: Modifica as variaveis:
              &$arraymsg - array que guarda as mensagens,
              &$pag - nmero da pagina atual,
              &$NumMsg - numero de mensagens,
              &$cont_msg - contador de mensagens por pagina.
*****************************************************************************************************/
function RetornaMensagensRec($sock, $cod_msg_pai, $cod_forum, $status, $msgperpag, $MAXSTRING,
  $nivel, &$pag, &$cont_msg, &$NumMsg, &$arraymsg, &$sinpag, $penult_acesso)
{

  $query = "SELECT FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data, FM.mensagem, FMR.relevancia FROM";
  $query .= " Forum_mensagens AS FM LEFT JOIN Forum_mensagens_relevancia AS FMR";
  $query .= " ON FM.cod_msg = FMR.cod_msg WHERE FM.cod_forum = ".$cod_forum." AND ";
  $query .= " FM.status = 'A' AND FM.cod_msg_pai = ".$cod_msg_pai." ORDER BY FM.data";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  for ($i = 0; $i < $total; $i++)
  {
    if ($tuplas[$i]['data'] > $penult_acesso)
      $sinpag[$pag] = true;

    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['nivel'] = $nivel;
    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['cod_usuario'] = $tuplas[$i]['cod_usuario'];
    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['data'] = $tuplas[$i]['data'];
    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['titulo'] = TruncaString($tuplas[$i]['titulo'], $MAXSTRING);
    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['relevancia'] = $tuplas[$i]['relevancia'];
    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['mensagem'] = $tuplas[$i]['mensagem'];

    $NumMsg++;
    $cont_msg++;

    /* Se for excedido o nmero de mensagens por p�ina, ent�  */
    /* incrementa o nmero de p�inas.                          */
    if ($cont_msg >= $msgperpag)
    {
      $pag++;
      $sinpag[$pag] = false;
      $cont_msg = 0;
    }

    /* Obt� as respostas recursivamente */
    RetornaMensagensRec($sock, $tuplas[$i]['cod_msg'], $cod_forum, $status, $msgperpag, $MAXSTRING,
      $nivel + 1, $pag, $cont_msg, $NumMsg, $arraymsg, $sinpag, $penult_acesso);
  }
}


/* *********************************************************************************************************
  RetornaMensagens - Retorna as mensagens-raiz, guardadas em um array e chama a funcao recursiva
                     RetornaMensagensRec.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum,
            $status - A - Ativo
                      D - Deletado,
                      X - Excluido.
            $msgperpag - numero de mensagens por pagina,
            $cod_usuario - codigo do usuario,
            $penult_acesso - data do penltimo acesso do usuario.
  Saida: array contendo:
              $NumMsg - total de mensagens,
              $msg - array com as mensagens, onde cada indice deste é uma pagina que
                contem $numpag mensagens,
              $sinpag - array sinalizando se a pagina contem  mensagens.
*************************************************************************************************************/
function RetornaMensagens($sock, $cod_forum, $status, $msgperpag, $cod_usuario, $penult_acesso)
{
  $MAXSTRING = 50;
  $msg = array();

  /* Array sinalizando se a p�ina possui mensagens novas */
  $sinpag = array();

  /* N�el das mensagens na �vore. */
  $nivel = 0;
  /* Contador de p�inas */
  $pag = 1;
  /* Nmero de mensagens */
  $NumMsg = 0;
  /* Contador de mensagens da p�ina atual */
  $cont_msg = 0;

  $query = "select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data, FM.mensagem, FMR.relevancia from";
  $query .= " Forum_mensagens as FM left join Forum_mensagens_relevancia as FMR";
  $query .= " ON FM.cod_msg = FMR.cod_msg where cod_forum = ".$cod_forum." AND";
  $query .= " status = 'A' and cod_msg_pai is NULL order by FM.data desc";
  
  $query2 = "select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data from";
  $query2 .= " Forum_mensagens as FM";
  $query2 .= " where cod_forum = ".$cod_forum." AND";
  $query2 .= " status = 'A' and cod_msg_pai is NULL order by FM.data desc";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  /* Atribui falso ao primeiro elemento do array de sinaliza�o de mensagens novas */
  /* (primeira p�ina).                                                            */
  $sinpag[$pag] = false;

  for ($i = 0; $i < $total; $i++)
  {
    /* Se a mensagem foi postada depois da penltima data de acesso ent� sinaliza */
    /* que esta p�ina possui mensagens novas.                                     */
    if ($tuplas[$i]['data'] > $penult_acesso)
      $sinpag[$pag] = true;

    $msg[$pag][$tuplas[$i]['cod_msg']]['nivel'] = $nivel;
    $msg[$pag][$tuplas[$i]['cod_msg']]['cod_usuario'] = $tuplas[$i]['cod_usuario'];
    $msg[$pag][$tuplas[$i]['cod_msg']]['data'] = $tuplas[$i]['data'];
    $msg[$pag][$tuplas[$i]['cod_msg']]['titulo'] = TruncaString($tuplas[$i]['titulo'], $MAXSTRING);
    $msg[$pag][$tuplas[$i]['cod_msg']]['relevancia'] = $tuplas[$i]['relevancia'];
    $msg[$pag][$tuplas[$i]['cod_msg']]['mensagem'] = $tuplas[$i]['mensagem'];

    $NumMsg++;
    $cont_msg++;

    /* Se for excedido o nmero de mensagens por p�ina, ent�  */
    /* incrementa o nmero de p�inas.                          */
    if ($cont_msg >= $msgperpag)
    {
      $pag++;
      $sinpag[$pag] = false;
      $cont_msg = 0;
    }

    /* Obt� as respostas recursivamente */
    RetornaMensagensRec($sock, $tuplas[$i]['cod_msg'], $cod_forum, $status, $msgperpag, $MAXSTRING,
      $nivel + 1, $pag, $cont_msg, $NumMsg, $msg, $sinpag, $penult_acesso);
  }
  return array($NumMsg, $msg, $sinpag);
}


/* ******************************************************************************************
  RetornaMensagensOrdenadas - Obter os dados da mensagem.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum,
            $status - A - Ativo
                      D - Deletado
                      X - Excluido.
            $numpag - nmero de mensagens por pagina,
            $cod_usuario - codigo do usuario.
            $ordem - data,
                     cod_usuario,
                     titulo.
            $penult_acesso - data do penltimo acesso do usuario.
  Saida: array contendo:
              $Nummsg - numero total de mensagens,
              $msg - array com os dados da mensagem,
              $sinpag - array sinalizando se a pagina contem mensagens.
*********************************************************************************************/
function RetornaMensagensOrdenadas($sock, $cod_curso, $cod_forum, $status, $numpag, $cod_usuario, $ordem, $penult_acesso)
{

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $MAXSTRING = 25;

  $msg = array();

  /* Nmero de p�inas contendo numpag mensagens */
  $pag = 1;
  /* Nmero de mensagens */
  $NumMsg = 0;
  /* contador de mensagens na p�ina atual  */
  $cont_msg = 0;

  /* Array sinalizando se a p�ina possui mensagens novas */
  $sinpag = array();

  if ($ordem == 'emissor')
  {
    $query  = " select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.mensagem, FM.data, FMR.relevancia";
    $query .= " FROM ".$dbnamebase.".Usuario_curso AS UC, Forum_mensagens AS FM LEFT JOIN Forum_mensagens_relevancia AS FMR";
    $query .= " ON FM.cod_msg = FMR.cod_msg WHERE UC.cod_curso=".$cod_curso." AND FM.cod_forum = ".$cod_forum;
    $query .= " AND FM.status = 'A' and FM.cod_usuario = UC.cod_usuario";
    $query .= " ORDER BY (select U2.nome from ".$dbnamebase.".Usuario as U2 left join ".$dbnamebase.".Usuario_curso as UC2 on UC2.cod_usuario_global=U2.cod_usuario where UC2.cod_curso=".$cod_curso." and UC2.cod_usuario=UC.cod_usuario) , FM.data DESC";
  }
  else if($ordem == 'relevancia')
  {
    $query = " select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data, FM.mensagem, FMR.relevancia FROM";
    $query .= " Forum_mensagens as FM left join Forum_mensagens_relevancia as FMR";
    $query .= " ON FM.cod_msg = FMR.cod_msg where FM.cod_forum = ".$cod_forum." AND";
    $query .= " FM.status = 'A' order by FMR.relevancia DESC, FM.data DESC";
  }
  else if($ordem == 'data'){

    $query = " select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data, FM.mensagem, FMR.relevancia FROM";
    $query .= " Forum_mensagens as FM left join Forum_mensagens_relevancia as FMR";
    $query .= " ON FM.cod_msg = FMR.cod_msg where FM.cod_forum = ".$cod_forum." AND";
    $query .= " FM.status = 'A' order by FM.data DESC";

  } else {

    $query = " select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data, FM.mensagem, FMR.relevancia FROM";
    $query .= " Forum_mensagens as FM left join Forum_mensagens_relevancia as FMR";
    $query .= " ON FM.cod_msg = FMR.cod_msg where FM.cod_forum = ".$cod_forum." AND";
    $query .= " FM.status = 'A' order by FM.".$ordem." ASC, FM.data DESC";
  }
   
  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  /* Atribui falso ao primeiro elemento do array de sinaliza�o de mensagens novas */
  /* (primeira p�ina).                                                            */
  $sinpag[$pag] = false;

  for ($i = 0; $i < $total; $i++)
  {
    /* Se a mensagem foi postada depois da penltima data de acesso ent� sinaliza */
    /* que esta p�ina possui mensagens novas.                                     */
    if ($tuplas[$i]['data'] > $penult_acesso)
      $sinpag[$pag] = true;

    $msg[$pag][$tuplas[$i]['cod_msg']]['cod_usuario'] = $tuplas[$i]['cod_usuario'];
    $msg[$pag][$tuplas[$i]['cod_msg']]['data'] = $tuplas[$i]['data'];
    $msg[$pag][$tuplas[$i]['cod_msg']]['titulo'] = TruncaString($tuplas[$i]['titulo'], $MAXSTRING);
    $msg[$pag][$tuplas[$i]['cod_msg']]['mensagem'] = $tuplas[$i]['mensagem'];

    $NumMsg++;
    $cont_msg++;

    /* Se for excedido o nmero de mensagens por p�ina, ent�  */
    /* incrementa o nmero de p�inas.                          */
    if ($cont_msg >= $numpag)
    {
      $pag++;
      $sinpag[$pag] = false;
      $cont_msg = 0;
    }
  }

  return array($NumMsg, $msg, $sinpag);
}

/* ****************************************************************************************************
  RetornaMensagensRec - Funcao recursiva chamada pela funcao RetornaMensagens. Aprofunda procurando
                        mensagens-filhas (reply's) e altera o array, no qual sao armazenadas, passado
                        por referencia.
  Entradas: $sock - sock de conexao,
            $cod_msg_pai - codigo da mensagem-pai,
            $cod_forum - codigo do forum,
            $status - A - Ativo
                      D - Deletado,
                      X - Excluido.
            $msgperpag - numero de mensagens por pagina,
            $MAXSTRING - tamanho maximo de caracteres para truncar o titulo das mensagens,
            $nivel - nivel da mensagem na avore (utilizado para identar as mensagens),
            &$pag - numero da pagina atual,
            &$cont_msg - contador de mensagens por pagina,
            &$NumMsg - numero de mensagens,
            &$arraymsg - array que guarda as mensagens,
            $sinpag - array que sinaliza quais as paginas possuem mensagens novas,
            $penult_acesso - data do penultimo acesso.
  Saida: Modifica as variaveis:
              &$arraymsg - array que guarda as mensagens,
              &$pag - nmero da pagina atual,
              &$NumMsg - numero de mensagens,
              &$cont_msg - contador de mensagens por pagina.
*****************************************************************************************************/
function RetornaMensagensAlunoRec($sock, $cod_msg_pai, $cod_forum, $status, $msgperpag, $MAXSTRING,
  $nivel, &$pag, &$cont_msg, &$NumMsg, &$arraymsg, &$sinpag, $penult_acesso)
{

  $query = "SELECT FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data, FM.mensagem, FMR.relevancia FROM";
  $query .= " Forum_mensagens AS FM LEFT JOIN Forum_mensagens_relevancia AS FMR";
  $query .= " ON FM.cod_msg = FMR.cod_msg WHERE FM.cod_forum = ".$cod_forum." AND ";
  $query .= " FM.status = 'A' AND FM.cod_msg_pai = ".$cod_msg_pai." ORDER BY FM.data";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  for ($i = 0; $i < $total; $i++)
  {
    if ($tuplas[$i]['data'] > $penult_acesso)
      $sinpag[$pag] = true;

    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['nivel'] = $nivel;
    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['cod_usuario'] = $tuplas[$i]['cod_usuario'];
    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['data'] = $tuplas[$i]['data'];
    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['titulo'] = TruncaString($tuplas[$i]['titulo'], $MAXSTRING);
    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['relevancia'] = $tuplas[$i]['relevancia'];
    $arraymsg[$pag][$tuplas[$i]['cod_msg']]['mensagem'] = $tuplas[$i]['mensagem'];

    $NumMsg++;
    $cont_msg++;

    /* Se for excedido o nmero de mensagens por p�ina, ent�  */
    /* incrementa o nmero de p�inas.                          */
    if ($cont_msg >= $msgperpag)
    {
      $pag++;
      $sinpag[$pag] = false;
      $cont_msg = 0;
    }

    /* Obt� as respostas recursivamente */
    RetornaMensagensAlunoRec($sock, $tuplas[$i]['cod_msg'], $cod_forum, $status, $msgperpag, $MAXSTRING,
      $nivel + 1, $pag, $cont_msg, $NumMsg, $arraymsg, $sinpag, $penult_acesso);
  }
}


/* *********************************************************************************************************
  RetornaMensagens - Retorna as mensagens-raiz, guardadas em um array e chama a funcao recursiva
                     RetornaMensagensRec.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum,
            $status - A - Ativo
                      D - Deletado,
                      X - Excluido.
            $msgperpag - numero de mensagens por pagina,
            $cod_usuario - codigo do usuario,
            $penult_acesso - data do penltimo acesso do usuario.
  Saida: array contendo:
              $NumMsg - total de mensagens,
              $msg - array com as mensagens, onde cada indice deste é uma pagina que
                contem $numpag mensagens,
              $sinpag - array sinalizando se a pagina contem  mensagens.
*************************************************************************************************************/
function RetornaMensagensAluno($sock, $cod_forum, $status, $msgperpag, $cod_usuario, $penult_acesso)
{
  $MAXSTRING = 50;
  $msg = array();

  /* Array sinalizando se a p�ina possui mensagens novas */
  $sinpag = array();

  /* N�el das mensagens na �vore. */
  $nivel = 0;
  /* Contador de p�inas */
  $pag = 1;
  /* Nmero de mensagens */
  $NumMsg = 0;
  /* Contador de mensagens da p�ina atual */
  $cont_msg = 0;

  $query = "select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data, FM.mensagem, FMR.relevancia from";
  $query .= " Forum_mensagens as FM left join Forum_mensagens_relevancia as FMR";
  $query .= " ON FM.cod_msg = FMR.cod_msg where cod_forum = ".$cod_forum." AND";
  $query .= " status = 'A' and cod_msg_pai is NULL and FM.cod_usuario = ".$cod_usuario." order by FM.data desc";
  
  $query2 = "select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data from";
  $query2 .= " Forum_mensagens as FM";
  $query2 .= " where cod_forum = ".$cod_forum." AND";
  $query2 .= " status = 'A' and cod_msg_pai is NULL  and FM.cod_usuario = ".$cod_usuario."order by FM.data desc";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  /* Atribui falso ao primeiro elemento do array de sinaliza�o de mensagens novas */
  /* (primeira p�ina).                                                            */
  $sinpag[$pag] = false;

  for ($i = 0; $i < $total; $i++)
  {
    /* Se a mensagem foi postada depois da penltima data de acesso ent� sinaliza */
    /* que esta p�ina possui mensagens novas.                                     */
    if ($tuplas[$i]['data'] > $penult_acesso)
      $sinpag[$pag] = true;

    $msg[$pag][$tuplas[$i]['cod_msg']]['nivel'] = $nivel;
    $msg[$pag][$tuplas[$i]['cod_msg']]['cod_usuario'] = $tuplas[$i]['cod_usuario'];
    $msg[$pag][$tuplas[$i]['cod_msg']]['data'] = $tuplas[$i]['data'];
    $msg[$pag][$tuplas[$i]['cod_msg']]['titulo'] = TruncaString($tuplas[$i]['titulo'], $MAXSTRING);
    $msg[$pag][$tuplas[$i]['cod_msg']]['relevancia'] = $tuplas[$i]['relevancia'];
    $msg[$pag][$tuplas[$i]['cod_msg']]['mensagem'] = $tuplas[$i]['mensagem'];

    $NumMsg++;
    $cont_msg++;

    /* Se for excedido o nmero de mensagens por p�ina, ent�  */
    /* incrementa o nmero de p�inas.                          */
    if ($cont_msg >= $msgperpag)
    {
      $pag++;
      $sinpag[$pag] = false;
      $cont_msg = 0;
    }

    /* Obt� as respostas recursivamente */
    RetornaMensagensAlunoRec($sock, $tuplas[$i]['cod_msg'], $cod_forum, $status, $msgperpag, $MAXSTRING,
      $nivel + 1, $pag, $cont_msg, $NumMsg, $msg, $sinpag, $penult_acesso);
  }
  return array($NumMsg, $msg, $sinpag);
}


/* ******************************************************************************************
  RetornaMensagensOrdenadas - Obter os dados da mensagem.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum,
            $status - A - Ativo
                      D - Deletado
                      X - Excluido.
            $numpag - nmero de mensagens por pagina,
            $cod_usuario - codigo do usuario.
            $ordem - data,
                     cod_usuario,
                     titulo.
            $penult_acesso - data do penltimo acesso do usuario.
  Saida: array contendo:
              $Nummsg - numero total de mensagens,
              $msg - array com os dados da mensagem,
              $sinpag - array sinalizando se a pagina contem mensagens.
*********************************************************************************************/
function RetornaMensagensAlunoOrdenadas($sock, $cod_curso, $cod_forum, $status, $numpag, $cod_usuario, $ordem, $penult_acesso)
{

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $MAXSTRING = 25;

  $msg = array();

  /* Nmero de p�inas contendo numpag mensagens */
  $pag = 1;
  /* Nmero de mensagens */
  $NumMsg = 0;
  /* contador de mensagens na p�ina atual  */
  $cont_msg = 0;

  /* Array sinalizando se a p�ina possui mensagens novas */
  $sinpag = array();

  if ($ordem == 'emissor')
  {
    $query  = " select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.mensagem, FM.data, FMR.relevancia";
    $query .= " FROM ".$dbnamebase.".Usuario_curso AS UC, Forum_mensagens AS FM LEFT JOIN Forum_mensagens_relevancia AS FMR";
    $query .= " ON FM.cod_msg = FMR.cod_msg WHERE UC.cod_curso=".$cod_curso." AND FM.cod_forum = ".$cod_forum;
    $query .= " AND FM.status = 'A' and FM.cod_usuario = UC.cod_usuario AND FM.cod_usuario = ".$cod_usuario;
    $query .= " ORDER BY (select U2.nome from ".$dbnamebase.".Usuario as U2 left join ".$dbnamebase.".Usuario_curso as UC2 on UC2.cod_usuario_global=U2.cod_usuario where UC2.cod_curso=".$cod_curso." and UC2.cod_usuario=UC.cod_usuario) , FM.data DESC";
  }
  else if($ordem == 'relevancia')
  {
    $query = " select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data, FM.mensagem, FMR.relevancia FROM";
    $query .= " Forum_mensagens as FM left join Forum_mensagens_relevancia as FMR";
    $query .= " ON FM.cod_msg = FMR.cod_msg where FM.cod_forum = ".$cod_forum." AND";
    $query .= " FM.status = 'A' AND FM.cod_usuario = ".$cod_usuario." order by FMR.relevancia DESC, FM.data DESC";
  }
  else if($ordem == 'data'){

    $query = " select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data, FM.mensagem, FMR.relevancia FROM";
    $query .= " Forum_mensagens as FM left join Forum_mensagens_relevancia as FMR";
    $query .= " ON FM.cod_msg = FMR.cod_msg where FM.cod_forum = ".$cod_forum." AND FM.cod_usuario = ".$cod_usuario;
    $query .= " AND FM.status = 'A' order by FM.data DESC";

  } else {

    $query = " select FM.cod_msg, FM.cod_usuario, FM.titulo, FM.data, FM.mensagem, FMR.relevancia FROM";
    $query .= " Forum_mensagens as FM left join Forum_mensagens_relevancia as FMR";
    $query .= " ON FM.cod_msg = FMR.cod_msg where FM.cod_forum = ".$cod_forum." AND FM.cod_usuario = ".$cod_usuario;
    $query .= " AND FM.status = 'A' order by FM.".$ordem." ASC, FM.data DESC";
  }
   
  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  /* Atribui falso ao primeiro elemento do array de sinaliza�o de mensagens novas */
  /* (primeira p�ina).                                                            */
  $sinpag[$pag] = false;

  for ($i = 0; $i < $total; $i++)
  {
    /* Se a mensagem foi postada depois da penltima data de acesso ent� sinaliza */
    /* que esta p�ina possui mensagens novas.                                     */
    if ($tuplas[$i]['data'] > $penult_acesso)
      $sinpag[$pag] = true;

    $msg[$pag][$tuplas[$i]['cod_msg']]['cod_usuario'] = $tuplas[$i]['cod_usuario'];
    $msg[$pag][$tuplas[$i]['cod_msg']]['data'] = $tuplas[$i]['data'];
    $msg[$pag][$tuplas[$i]['cod_msg']]['titulo'] = TruncaString($tuplas[$i]['titulo'], $MAXSTRING);
    $msg[$pag][$tuplas[$i]['cod_msg']]['mensagem'] = $tuplas[$i]['mensagem'];

    $NumMsg++;
    $cont_msg++;

    /* Se for excedido o nmero de mensagens por p�ina, ent�  */
    /* incrementa o nmero de p�inas.                          */
    if ($cont_msg >= $numpag)
    {
      $pag++;
      $sinpag[$pag] = false;
      $cont_msg = 0;
    }
  }

  return array($NumMsg, $msg, $sinpag);
}


/* *****************************************************************************************
  RetornaMensagem - Obtem os dados da mensagem.
  Entradas: $sock - sock de conexao,
            $cod_msg - codigo da mensagem,
            $cod_forum - codigo do forum,
            $status - A - Ativo
                      D - Deletado
                      X - Excluido.
  Saida: array contendo:
              $total - numero total de mensagens,
              $tupla - array com os dados da mensagem: cod_usuario,
                                                       titulo,
                                                       mensagem,
                                                       data.
*********************************************************************************************/
function RetornaMensagem($sock, $cod_msg, $cod_forum, $status)
{
  $query = "select cod_usuario, titulo, mensagem, data from Forum_mensagens";
  $query .= " where cod_msg = ".$cod_msg." and cod_forum = ".$cod_forum;
  $query .= " and status = 'A'\n";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tupla = RetornaLinha($res);

  return array($total, $tupla);
}



/* *******************************************************************************************
  ObtemMensagemAnteriorRecur - Obtem o codigo da mensagem-filha mais velha de uma mensagem 
                               sob ordenacao por arvore.
  Entradas: $sock - sock de conexao,
            $cod_msg_pai - codigo da mensagem-pai,
            $cod_forum - codigo do forum,
            $status - A - Ativo,
                      D - Deletado,
                      X - Excluido.
  Saida: codigo da mensagem-filha mais velha se esta existir, do contrario cod_msg_pai.
********************************************************************************************/
function ObtemMensagemAnteriorRecur($sock, $cod_msg_pai, $cod_forum, $status)
{
  $codigo = -1;

  $query = "select cod_msg from Forum_mensagens where cod_msg_pai = ".$cod_msg_pai;
  $query .= " and cod_forum = ".$cod_forum." and status = 'A";
  $query .= "' order by data desc limit 1";

  $res = Enviar($sock, $query);
  $num = RetornaNumLinhas($res);
  $tupla = RetornaLinha($res);

  if ($num > 0)
    $codigo = ObtemMensagemAnteriorRecur($sock, $tupla['cod_msg'], $cod_forum, $status);

  if ($codigo > 0)
    return($codigo);
  else
    return($cod_msg_pai);
}


/* ***************************************************************************************
  ObtemMensagemAnterior - Obtem o codigo da mensagem anterior sob ordenacao por arvore.
  Entradas: $sock - sock de conexao,
            $cod_msg - codigo da mensagem,
            $cod_forum - codigo do forum,
            $status - A - Ativo,
                      D - Deletado,
                      X - Excluido.
  Saida:    codigo da mensagem anterior se esta existir, do contrario -1.
********************************************************************************************/
function ObtemMensagemAnterior($sock, $cod_msg, $cod_forum, $status)
{
  /* Obt� o cod_msg_pai e a data da mensagem.                              */
  $query = "select cod_msg_pai, data from Forum_mensagens where cod_msg = ";
  $query .= $cod_msg." and cod_forum = ".$cod_forum." and status = 'A'";

  $res = Enviar($sock, $query);
  $resultado = RetornaLinha($res);
  /* Extrai o cod_msg_pai e a data da mensagem.                             */
  $cod_msg_pai = $resultado['cod_msg_pai'];
  $data_mensagem = $resultado['data'];

  /* Seleciona a mensagem-irm�cuja data �menor que a data da mensagem     */
  /* atual e maior que as das demais irm�.                                 */
  if ($cod_msg_pai == "")
  {
    $query = "select cod_msg from Forum_mensagens where data < ".$data_mensagem;
    $query .= " and cod_msg_pai is NULL and cod_forum = ".$cod_forum;
    $query .= " and status = 'A' order by data desc limit 1";
  }
  else
  {
    $query = "select cod_msg from Forum_mensagens where data < ".$data_mensagem;
    $query .= " and cod_msg_pai = ".$cod_msg_pai." and cod_forum = ".$cod_forum;
    $query .= " and status = 'A' order by data desc limit 1";
  }

  $res = Enviar($sock, $query);
  $achou_irmao_anterior = RetornaNumLinhas($res);

  $irmao_anterior = RetornaLinha($res);

  /* Se existir uma mensagem-irm�mais antiga, ent� procura sua mensagem-filha */
  /* mais velha.                                                                */
  if ($achou_irmao_anterior > 0)
  {
    # Localiza o filho mais velho
    $anterior = ObtemMensagemAnteriorRecur($sock, $irmao_anterior['cod_msg'],
      $cod_forum, $status);
  }
  else
  /* Se n� existir uma mensagem-irm�mais antiga, ent� verifica se h�uma     */
  /* mensagem-pai. Se existir ser�a mensagem anterior, do contr�io n� h�uma */
  /* mensagem anterior.                                                         */
  {
    /* Se n� houver mensagem-pai, ent� n� h�mensagem anterior.            */
    if ($cod_msg_pai == "")
      $anterior = -1; # N� h�antecessor.
    else
      $anterior = $cod_msg_pai; # A mensagem anterior �a mensagem-pai.
  }
  return($anterior);
}


/* **************************************************************************************************
  RetornaMensagemSeguinteRecur - Obtem recusivamente o cod_msg da proxima mensagem em ordenacao 
                                 por arvore.
  Entradas: $sock - sock de conexao,
            $cod_msg - codigo da mensagem,
            $cod_forum - codigo do forum,
            $status - A - Ativo
                      D - Deletado
                      X - Excluido.
 Saida: codigo da mensagem seguinte.
****************************************************************************************************/
function RetornaMensagemSeguinteRecur($sock, $cod_msg, $cod_forum, $status)
{
  # /: Obt� o cod_msg_pai e a data da mensagem.
  $query = "select cod_msg_pai, data from Forum_mensagens where cod_msg = ";
  $query .= $cod_msg." and cod_forum = ".$cod_forum." and status = 'A'";
  $res = Enviar($sock, $query);
  $resultado = RetornaLinha($res);

  $cod_msg_pai = $resultado['cod_msg_pai'];
  $data_mensagem = $resultado['data'];
  # :/

  /* Procura uma mensagem irm�cuja data �maior que a da mensagem atual. */

  /* Se o cod_msg_pai for nulo ent� a mensagem est�na raiz.             */
  if ($cod_msg_pai == "")
  {
    $query = "select cod_msg from Forum_mensagens where data > ".$data_mensagem;
    $query .= " and cod_msg_pai is NULL and cod_forum = ".$cod_forum;
    $query .= " and status = 'A' order by data limit 1";
  }
  else
  /* Se o cod_msg_pai N� for nulo ent� a mensagem �uma resposta.       */
  {
    $query = "select cod_msg from Forum_mensagens where data > ".$data_mensagem;
    $query .= " and cod_msg_pai = ".$cod_msg_pai." and cod_forum = ".$cod_forum;
    $query .= " and status = 'A' order by data limit 1";
  }

  $res = Enviar($sock, $query);
  $achou_proximo = RetornaNumLinhas($res);
  $proximo_irmao = RetornaLinha($res);

  # Se possuir irm�ent� esta �a mensagem seguinte
  if ($achou_proximo)
  {
    # A pr�ima mensagem �a mensagem-irm�
    $proximo = $proximo_irmao['cod_msg'];
  }
  else
  /* Se n� houver irm�s, ent� verifica se h�uma mensagem-pai */
  /* Se n� houver ent� n� h�mensagem seguinte.               */
  {
    if ($cod_msg_pai == "")
      $proximo = -1; # N� h�sucessor.
    else
    /* Se h�uma mensagem pai ent� deve-se procurar nela uma    */
    /* mensagem-irm�                                            */
      $proximo = RetornaMensagemSeguinteRecur($sock, $cod_msg_pai,
        $cod_forum, $status);
  }

  return($proximo);
}


/* *****************************************************************************************************
  ObtemMensagemSeguinte - Obtem o cod_msg da proxima mensagem em ordenacao por arvore.
  Entradas: $sock - sock de conexao,
            $cod_msg - codigo da mensagem,
            $cod_forum - codigo do forum,
            $status - A - Ativo
                      D - Deletado
                      X - Excluido.
  Saida:    codigo da mensagem seguinte.
********************************************************************************************************/
function ObtemMensagemSeguinte($sock, $cod_msg, $cod_forum, $status)
{
  # /: Obt� o cod_msg_pai e a data da mensagem.
  $query = "select cod_msg_pai, data from Forum_mensagens where cod_msg = ";
  $query .= $cod_msg." and cod_forum = ".$cod_forum." and status = 'A'";
  $res = Enviar($sock, $query);
  $resultado = RetornaLinha($res);

  $cod_msg_pai = $resultado['cod_msg_pai'];
  $data_mensagem = $resultado['data'];
  # :/

  # /: Obt� o cod_msg do filho mais "novo", se aquele existir.
  $query = "select cod_msg from Forum_mensagens where cod_msg_pai = ";
  $query .= $cod_msg." and cod_forum = ".$cod_forum." and status = '";
  $query .= "A' order by data limit 1";

  $res = Enviar($sock, $query);
  $numfilho = RetornaNumLinhas($res);
  $filho_mais_novo = RetornaLinha($res);
  # :/

  /* Se existir um filho ent� a mensagem seguinte ser�ele. */
  if ($numfilho > 0)
  {
    $proximo = $filho_mais_novo['cod_msg'];
  }
  else
  /* Se n� houver filhos ent� deve-se procurar uma mensagem irm�*/
  /* cuja data �maior que a da mensagem atual.                    */
  {
    if ($cod_msg_pai == "")
    /* Se o cod_msg_pai for nulo ent� a mensagem est�na raiz. */
    {
      $query = "select cod_msg from Forum_mensagens where data > ".$data_mensagem;
      $query .= " and cod_msg_pai is NULL and cod_forum = ".$cod_forum;
      $query .= " and status = 'A' order by data limit 1";
    }
    else
    /* Se o cod_msg_pai N� for nulo, ent� a mensagem �uma resposta. */
    {
      $query = "select cod_msg from Forum_mensagens where data > ".$data_mensagem;
      $query .= " and cod_msg_pai = ".$cod_msg_pai." and cod_forum = ".$cod_forum;
      $query .= " and status = 'A' order by data limit 1";
    }

    $res = Enviar($sock, $query);
    $achou_irmao_posterior = RetornaNumLinhas($res);
    $irmao_posterior = RetornaLinha($res);

    /* Se houver uma irm�ent� a mensagem seguinte �ela. */
    if ($achou_irmao_posterior > 0)
    {
      # A pr�ima mensagem �a mensagem-irm�
      $proximo = $irmao_posterior['cod_msg'];
    }
    else
    /* Se n� houver irm�s, ent� verifica se h�uma mensagem-pai */
    /* Se n� houver ent� n� h�mensagem seguinte.               */
    {
      if ($cod_msg_pai == "")
        $proximo = -1; # N� h�sucessor.
      else
      /* Se h�uma mensagem pai ent� deve-se procurar nela uma    */
      /* mensagem-irm�                                            */
        $proximo = RetornaMensagemSeguinteRecur($sock, $cod_msg_pai,
          $cod_forum, $status);
    }
  }
  return($proximo);
}


/* ******************************************************************************************
  MensagemExiste - Verifica se a mensagem existe para que se possa responde-la.
  Entradas: $sock - sock de conexao,
            $cod_msg - codigo da mensagem,
            $cod_forum - codigo do forum.
  Saida:    true se existir, do contrario, false
********************************************************************************************/
function MensagemExiste($sock, $cod_msg, $cod_forum)
{
  $query = "select cod_msg from Forum_mensagens where cod_msg = ".$cod_msg;
  $query .= " and cod_forum = ".$cod_forum;
  $res = Enviar($sock, $query);

  if (RetornaNumLinhas($res) > 0)
    return(true);
  else
    return(false);
}


/* ******************************************************************************************
  MensagemTemRespostas - Verifica se a mensagem tem respostas para avaliar se pode ser 
                         excluida. Somente mensagens sem respostas podem ser excluidas.
  Entradas: $sock - sock de conexao,
            $cod_msg - codigo da mensagem,
            $status - status da mensagem.
  Saida:    true se possuir respostas, do contrario, false.
********************************************************************************************/
function MensagemTemRespostas($sock, $cod_msg, $status)
{
  $query = "select cod_msg from Forum_mensagens where cod_msg_pai = ".$cod_msg;
  $query .= " and status = '".$status."'";

  $res = Enviar($sock, $query);

  if (RetornaNumLinhas($res) > 0)
    return(true);
  else
    return(false);
}

/* *********************************************************************************
  ApagaMensagem - Modifica o status da mensagem para X (excluido).
  Entradas: $sock - sock de conexao,
            $cod_msg - codigo da mensagem,
            $cod_forum - codigo do forum.
  Saida:    true
***********************************************************************************/
function ApagaMensagem($sock, $cod_msg, $cod_forum)
{
    /* UPDATE 10-dez-2003 N� �mais poss�el apagar mensagens */
    /* Elimino a funcionalidade de apagar desta fun�o e apenas a fa� retornar true */

  /*
  $query = "update Forum_mensagens set status = 'X' where cod_forum = ".$cod_forum;
  $query .= " and cod_msg = ".$cod_msg." and cod_forum = ".$cod_forum;

  $res = Enviar($sock, $query); */
  return(true);
}

/* ***************************************************************************************
  SalvaMensagem - Salva a mensagem.
  Entradas: $sock - sock de conexao,
            $cod_msg - codigo da mensagem,
            $cod_msg_pai - codigo da mensagem que esta sendo respondida (se houver),
            $cod_forum - codigo do forum,
            $cod_usuario - codigo do usuario,
            $titulo - titulo da mensagem,
            $mensagem - texto da mensagem.
  Saida:    true se bem-sucedido, do contrario, false
********************************************************************************************/
function SalvaMensagem($sock, $cod_msg_pai, $cod_forum, $cod_usuario, $titulo, $mensagem, $cod_curso)
{
  $objResponse = new xajaxResponse();
  

  /* Se n�o fizer o magic quotes, nos fazemos =) */
  if (!ini_get("magic_quotes_gpc")){
  $titulo = ConverteAspas2BarraAspas($titulo);
  $mensagem = ConverteAspas2BarraAspas($mensagem);
  }

  /* Verifica se o f�um �ativo.                 */
  $tupla = RetornaForum($sock, $cod_forum);

  $permitido = VerificaPermissao($sock,$cod_usuario,$tupla['permissoes']);
  
  if (($tupla['status'] != 'A') && ($tupla['status'] != 'G') && ($tupla['status'] != 'R') || (($tupla['status'] == 'G') && (!$permitido)) || (($tupla['status'] == 'R') && (!$permitido)) )
     return(false);

  $titulo = EliminaScript($titulo);
  $titulo = LimpaTitulo($titulo);
  $titulo = EliminaEspacos($titulo);

  $mensagem = EliminaScript($mensagem);
  $mensagem = LimpaConteudo($mensagem);

  $status = 'A';

  if ($cod_msg_pai == "") $cod_msg_pai = "NULL";

  $query = "insert into Forum_mensagens (cod_msg_pai, cod_forum, cod_usuario, ";
  $query .= "titulo, mensagem, status, data) values (".$cod_msg_pai.", ".$cod_forum;
  $query .= ", ".$cod_usuario.", '".$titulo."', '".$mensagem."', '".$status."', ".time().")";
  $res = Enviar($sock, $query);
  

  $objResponse->redirect("ver_forum.php?cod_forum=".$cod_forum."&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario);

  return($objResponse);
}


/* *****************************************************************
  RetornaCodForum - Retorna  o codigo do forum
   Entrada: $sock - sock de conexao
            $nome -  Nome do F�um
   Saida: Codigo do Forum
*******************************************************************/
function RetornaCodForum($sock,$nome)
{
  $query = "select cod_forum from Forum where nome = '".$nome."' and status='A'";
  $res = Enviar($sock, $query);
  $codigo = RetornaLinha($res);

  return($codigo[0]);
}


/* *******************************************************************************************
  RetornaUsuariosPermissao - Retorna os usuarios com permissao
   Entrada:  $sock - sock de conexa
             $cod_forum - Codigo do forum
   Saida: permitidos - array com os codigos dos usuarios/grupos com permissao
                       e tipo 'U' - usuario, 'G' - grupo
*********************************************************************************************/
function RetornaUsuariosPermissao($sock,$cod_forum)
{
  $query = "select cod_permitido from Forum_permissoes where cod_forum = ".$cod_forum." and tipo='U'";
  $res = Enviar($sock, $query);
  $permitidos = RetornaArrayLinhas($res);

  return($permitidos);
}


/* ***************************************************************************************
  RetornaGruposPermissao - Retorna os usuarios com permissao
   Entrada:  $sock - BASE DO CURSO
             $cod_forum - Codigo do forum
   Saida:   permitidos - array com os codigos dos usuarios/grupos com permissao
                         e tipo 'U' - usuario, 'G' - grupo
******************************************************************************************/
function RetornaGruposPermissao($sock,$cod_forum)
{
  $query = "select cod_permitido from Forum_permissoes where cod_forum = ".$cod_forum." and tipo='G'";
  $res = Enviar($sock, $query);
  $permitidos = RetornaArrayLinhas($res);

  return($permitidos);
}


/* *************************************************************************************
  RetornaListagemCompletaAluno - Cria um lista contendo todas as mensagens do aluno 
                                 selecionado e uma lista com links para essas mensagens.
  Entradas: $sock - sock de conexao,
            $cod_forum - codigo do forum,
            $cod_usuario - codigo do aluno.

  Saida:    array contendo:
              $Nummsg - numero total de mensagens,
              $msg - variavel com os links para as mensagens,
              $lista - variavel com a lista de mensagens.
****************************************************************************************/
function RetornaListagemCompletaAluno($sock, $cod_forum, $cod_usuario, $lista_frases){

  Global $SalvarEmArquivo; /* Usada para determinar se tem que criar o link para o perfil */

  $MAXSTRING = 25;

  $NumMsg = 0; # Nmero de mensagens
  $recuo = 0; # Recuo para hierarquiza�o das mensagens
  /* penltima data de acesso                                */
  $penult_acesso = PenultimoAcesso($sock, $cod_usuario, "");

  $query = "select cod_msg, titulo, mensagem, data from ";
  $query .= "Forum_mensagens where cod_forum = ".$cod_forum." and";
  $query .= " status = 'A' and cod_usuario=".$cod_usuario." order by data";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tuplas = RetornaArrayLinhas($res);

  for ($i = 0; $i < $total; $i++)
  {
    # Lista - INICIO

    /* adiciona um separador. */
    $lista .= "<tr>\n";
    $lista .= "  <td colspan=3><hr></td>\n";
    $lista .= "</tr>\n";

    /* Cria uma �cora para o link da mensagem. */
    $lista .= "<tr class=g".(($NumMsg % 2) + 1)."field>\n";
   // $lista .= "  <td><a name=".$tuplas[$i]['cod_msg']."></a>";

    /* Exibe o t�ulo da mensagem.                */
    $lista .= "  <td width=70%>\n";
    $lista .= "<font class=text>".($NumMsg + 1).".</font>\n";
    $lista .= "    <font class=text><b>".$tuplas[$i]['titulo']."</b></font>\n";
    $lista .= "  </td>\n";

    /* Exibe a data de postagem da mensagem.    */
    $lista .= "  <td width=30% align=right>\n";
    $lista .= "    <font class=text>".NomeDiaSemana(UnixTime2DiaSemana($tuplas[$i]['data'])).", ";
    $lista .= UnixTime2Data($tuplas[$i]['data']).", ".UnixTime2Hora($tuplas[$i]['data'])."</font>\n";
    $lista .= "  </td>\n";
    $lista .= "</tr>\n";
    
    //-------------------novo - relev�cia
    $rel_msg = RetornaRelevancia($sock, $tuplas[$i]['cod_msg'], $lista_frases);
    if ($rel_msg == -1)
       $rel_msg = " N� analisada";
       
    
    /* Exibe a relev�cia da mensagem.                */
    $lista .= "<tr class=g".(($NumMsg % 2) + 1)."field>\n";
    $lista .= "  <td colspan=2 widht=100% align=right>\n";
    $lista .= "    <font class=text> Relev�cia: </font>\n";
    $lista .= "    <font class=text><b>".$rel_msg."</b></font>\n";
    $lista .= "  </td>\n";
    $lista .= "</tr>\n";
    
    //-------------------fim novo - relev�cia

    /* Exibe o contedo da mensagem.            */
    $lista .= "<tr class=g".(($NumMsg % 2) + 1)."field>\n";
    $lista .= "  <td colspan=2 widht=100%>\n";
    $lista .= "    <font class=text>".Enter2BR(LimpaConteudo($tuplas[$i]['mensagem']));
    $lista .= "</font>\n";
    $lista .= "  </td>\n";

    $lista .= "</tr>\n";
    # Lista - FIM

    $NumMsg++;
  }

  return array($NumMsg, $lista);
}


/* ***********************************************************************************
  RetornaGrupoComCodigo - retorna nome do grupo do codigo dado
  Entrada: $sock - sock de conexao
           $cod_grupo - codigo do grupo
  Saida: nome do grupo de codigo igual a $cod_grupo
*************************************************************************************/
function RetornaGrupoComCodigo($sock,$cod_grupo)
{
  $query="select nome from Grupos where cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['nome'];
}


/* *************************************************************************
   RetornaGrupos - Retorna o cod_grupo de todos os grupos.
   entrada: $sock - sock de conexao
   saida: o codigo de todos os grupos 
***************************************************************************/
function RetornaGrupos($sock)
{ 
  /* select dos grupos que est� com status != apagado */
  $query="select cod_grupo from Grupos where (strcmp(status, 'X') != 0)";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  
  return ($lista);
} 


/* **********************************************************************************
   RetornaGruposUsuario - retorna todos os grupos de um usuario dado.
   entrada: $sock - sock de conexao
            $cod_usuario - codigo do usuario que se quer listar os grupos
   saida: array contendo os codigos dos grupos que o usuario faz parte
************************************************************************************/
function RetornaGruposUsuario($sock,$cod_usuario)
{
  $query="select cod_grupo from Grupos_usuario where cod_usuario=".$cod_usuario."";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  return ($lista);
}


/* ********************************************************************************************
   VerificaPermissao - verifica se um dado participante possui permissoess no forum
   entrada: $sock - sock de conexao
            $cod_usuario - codigo do usuario que se quer saber se possui permissao
            $forum_permissoes - array com os itens 'cod_permitido' e 'tipo' informando 
                                cod_usuario ou cod_grupo permitidos para aquele forum
   saida: booleano (true se permitido e false se nao permitido)
************************************************************************************************/
function VerificaPermissao($sock, $cod_usuario, $forum_permissoes)
{

  global $cod_curso;	 


  //Verifica primeiro se o usu�io est�na lista ou se ele �coordenador do curso
  
  //procura entre os valores um array que coincida com o abaixo
  $array_search_usuario = array(0 => $cod_usuario, 'cod_permitido' => $cod_usuario, 1=>'U', 'tipo'=>'U');
  if (((is_array($forum_permissoes)) && (array_search($array_search_usuario, $forum_permissoes)!==false)) || (ECoordenador($sock,$cod_curso,$cod_usuario)) ){
    return true;
  }else{
  	 
     //se n�, verifica-se se algum grupo do qual ele faz parte est�na lista
     $permitido = false;
     $grupos=RetornaGruposUsuario($sock,$cod_usuario);

     for ($g = 0; $g<sizeof($grupos); $g++){
        $array_search_grupo = array(0 => $grupos[$g][0], 'cod_permitido' => $grupos[$g][0], 1=>'G', 'tipo'=>'G');
        if ((is_array($forum_permissoes)) && (array_search($array_search_grupo, $forum_permissoes)!==false)){
          return true;
        }
      }
  }
  
  return false;
}


/* *****************************************************************************************
  RetornaCodigoAlunosDoCurso - retorna array com os cod_usuario dos alunos do curso
  Entrada: $sock - sock de conexao
  Saida: array com os cod_usuario dos alunos do curso
*******************************************************************************************/
function RetornaCodigoAlunosDoCurso($sock, $cod_curso)
{ 

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select cod_usuario from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso." and binary tipo_usuario='A' order by cod_usuario";
  $res=Enviar($sock,$query);
  $linha=RetornaArrayLinhas($res);
  return $linha;
  Desconectar($sock);
}


/* *************************************************************************************************
  RetornaCodigoFormadoresDoCurso - retorna array com os cod_usuario dos formadores do curso
  Entrada: $sock - sock de conexao
  Saida:   array com os cod_usuario dos formadores do curso
***************************************************************************************************/       
function RetornaCodigoFormadoresDoCurso($sock, $cod_curso)
{       
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select cod_usuario from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso." and binary tipo_usuario='F' order by cod_usuario";
  $res=Enviar($sock,$query);
  $linha=RetornaArrayLinhas($res);
  
  return $linha;
}


/* *********************************************************************************************
  RetornaNomeUsuarioDeCodigo - retorna nome do usuario cujo codigo eh igual a entrada
  Entrada: $sock - sock de conexao
           $cod_usuario - codigo do usuario do qual procurar o nome
  Saida: nome do usuario com codigo == $cod_usuario
***********************************************************************************************/
function RetornaNomeUsuarioDeCodigo($sock, $cod_usuario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select U.nome from ".$dbnamebase.".Usuario U left join ".$dbnamebase.".Usuario_curso UC on UC.cod_usuario_global=U.cod_usuario where UC.cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['nome'];
}


/* ********************************************************************************************
  RetornaCodigoGruposDoCurso - retorna array com os cod_grupo dos grupos do curso
  Entrada: $sock - sock de conexao
  Saida: array com os cod_grupo dos grupos do curso
**********************************************************************************************/
function RetornaCodigoGruposDoCurso($sock)
{
  $query="select cod_grupo from Grupos where status != 'X' order by nome";
  $res=Enviar($sock,$query);
  $linha=RetornaArrayLinhas($res);
  return $linha;
} 


/* ****************************************************************************************************
   StatusFerramentaGrupos - retorna se a ferramenta Grupos esta ativada
     PROVISORIO !!! - O LUGAR DESTA FUNCAO NAO EH AQUI
   Entrada: $sock - sock de conexao com a base de dados
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario logado
   Saida: true - ferramenta grupos esta ativada e visivel ao usuario
          false - ferramenta grupos nao esta ativa, ou nao eh visivel ao usuario
   OBS: Esta funcao eh diferente da funcao em portfolio.inc. La cod_curso e cod_usuario sao variaveis 
        globais. Aqui, sao parametros
********************************************************************************************************/
function StatusFerramentaGrupos ($sock, $cod_curso, $cod_usuario)
{
  $query = "select status from Curso_ferramentas where cod_ferramenta=12";
  $res   = Enviar ($sock, $query);
  $linha = RetornaLinha ($res);

  $status = $linha['status'];

  if ($status == 'D')
    $retorno = false;
  else if ($status == 'F')
    $retorno = EFormador ($sock, $cod_curso, $cod_usuario);
  else
    // se entrou aqui, status = 'A'. Permito para alunos e colaboradores
    $retorno = true;

  return $retorno;
}


/* ******************************************************************************************
  RetornaTodosFormadores - retorna todos os formadores de um curso
  Entrada: $sock - sock de conexao
  Saida:   lista com os formadores do curso
********************************************************************************************/
function RetornaTodosFormadores($sock, $cod_curso) {

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query= "select cod_usuario from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso." and binary tipo_usuario='F' and cod_usuario!=-1";
  $res= Enviar($sock, $query);
  $lista= RetornaArrayLinhas($res);
  return $lista;
} 
  

/* **************************************************************************
  RetornaTodosAlunos - retorna todos os alunos de um curso
  Entrada: $sock - sock de conexao
  Saida:   lista com os alunos do curso
****************************************************************************/
function RetornaTodosAlunos($sock, $cod_curso) {

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query= "select cod_usuario from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso." and tipo_usuario='A'";
  $res= Enviar($sock, $query);
  $lista= RetornaArrayLinhas($res);
  return $lista;
}   


/* *************************************************************************
  RetornaTodos - retorna todos os participantes de um curso
  Entrada: $sock - sock de conexao
  Saida:   lista com os participantes do curso
**************************************************************************/
function RetornaTodos($sock, $cod_curso) {

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query= "select cod_usuario from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso." and cod_usuario!=-1";
  $res= Enviar($sock, $query);
  $lista= RetornaArrayLinhas($res);
  return $lista;
}


/* *********************************************************************************************
  RetornaCodigoDeNomeUsuario - retorna codigo do usuario cujo nome é igual a entrada
  Entrada: $sock - sock de conexao
           $nome_usuario - nome do usuario do qual procurar o codigo
  Saida: codigo do usuario com nome == $nome_usuario
************************************************************************************************/
function RetornaCodigoDeNomeUsuario($sock,$cod_curso, $nome_usuario)
{

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select cod_usuario from ".$dbnamebase.".Usuario_curso UC left join ".$dbnamebase.".Usuario U on UC.cod_usuario_global=U.cod_usuario where UC.cod_curso=".$cod_curso." and U.nome like '".$nome_usuario."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['cod_usuario'];
}


/* **** Estas ltimas funcoes sao utilizadas na atribuicao de relevancia a mensagens do forum *****/

/* ********************************************************************************
  SalvaRelevancia - Salva a relevancia da mensagem.
  Entradas: $sock - sock de conexao,
            $cod_msg - codigo da mensagem,
            $relevancia - relevancia da mensagem.
  Saida:    true se bem-sucedido, do contrario, false
 ********************************************************************************/
function SalvaRelevancia($sock, $cod_msg, $relevancia)
{
  if (RelevanciaExiste($sock, $cod_msg)) {
	$query = "update Forum_mensagens_relevancia set relevancia='".$relevancia."', data_avaliacao=".time();
        $query .= " where cod_msg='".$cod_msg."'";
        $res = Enviar($sock, $query);
  }
  else {
    $query = "insert into Forum_mensagens_relevancia (cod_msg, relevancia,data_avaliacao) ";
    $query .= "values (".$cod_msg.", ".$relevancia.", ".time().")";
    $res = Enviar($sock, $query);
  }

  return($res);
}


/* ************************************************************************************
  RelevanciaExiste - Verifica se a mensagem ja possui alguma relevancia atribuida
  Entradas: $sock - sock de conexao,
            $cod_msg - codigo da mensagem.
  Saida: true se existir, do contrario, false
 *************************************************************************************/
function RelevanciaExiste($sock, $cod_msg)
{
  $query = "select cod_msg from Forum_mensagens_relevancia where cod_msg = '".$cod_msg."'";
  $res = Enviar($sock, $query);

  if (RetornaNumLinhas($res) > 0)
    return(true);
  else
    return(false);
}


/* *******************************************************************************************
  RetornaRelevancia - Retorna a relevancia atribuida a mensagem
  Entradas: $sock - sock de conexao,
            $cod_msg - codigo da mensagem.
  Saida: relevancia da mensagem, -1 se a mensagem ainda nao possui uma relevancia
 *******************************************************************************************/
 function RetornaRelevancia($sock, $cod_msg, $lista_frases)
{
    /*115 - N&atilde;o Relevante
      116 - Pouco Relevante
      117 - Relev�cia M�ia
      118 - Relevante
      119 - Muito Relevante    */

    $array_rel_frases[0] = RetornaFraseDaLista($lista_frases, 115);
    $array_rel_frases[1] = RetornaFraseDaLista($lista_frases, 116);
    $array_rel_frases[2] = RetornaFraseDaLista($lista_frases, 117);
    $array_rel_frases[3] = RetornaFraseDaLista($lista_frases, 118);
    $array_rel_frases[4] = RetornaFraseDaLista($lista_frases, 119);

  if (RelevanciaExiste($sock, $cod_msg)) {
    $query = "select relevancia from Forum_mensagens_relevancia where cod_msg = '".$cod_msg."'";
    $res = Enviar($sock, $query);

    $tupla = RetornaLinha($res);
    return $array_rel_frases[$tupla['relevancia']];
    }
  else {
	return -1;
    }
}


/* *****************************************************************************************
   RetornaTodosColaboradores - retorna os nomes dos colaboradores do curso
   Entrada: $sock - sock de conexao com a base
   Saida: array com os nomes dos colaboradores do curso
*******************************************************************************************/
function RetornaTodosColaboradores($sock, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query  = "select cod_usuario from ".$dbnamebase.".Usuario_curso ";
  $query .= "where cod_curso=".$cod_curso." and binary tipo_usuario = 'Z' ";
  $query .= "order by cod_usuario asc";

  $res   = Enviar ($sock, $query);
  $lista = RetornaArrayLinhas ($res);

  return $lista;
}


/* ***********************************************************************************
  MudarRelevanciaDinamic - Muda a relevancia da mensagem dinamicamente.
  Entradas: $dadosForm - array contendo: ['tipo_comp']
                                         ['cod_curso']
                                         ['cod_msg']
                                         ['nova_relevancia']
            $nova_relev - nova relevancia da mensagem.
  Saida: XML da funcao ajax
 *************************************************************************************/
function MudarRelevanciaDinamic($dadosForm, $nova_relev)
{
  $objResponse = new xajaxResponse();
  
  $cod_curso=$dadosForm['cod_curso'];
  $cod_msg = $dadosForm['cod_msg'];
  $nova_relevancia = $dadosForm['nova_relevancia'];
  $texto = $dadosForm['texto_feedback'];

  $data = time();
  
  $sock=Conectar($cod_curso);
  
  if (RelevanciaExiste($sock, $cod_msg)) {
	$query = "update Forum_mensagens_relevancia set relevancia='".$nova_relevancia."', data_avaliacao=".$data;
        $query .= " where cod_msg='".$cod_msg."'";
        $res = Enviar($sock, $query);
  }
  else {
    $query = "insert into Forum_mensagens_relevancia (cod_msg, relevancia,data_avaliacao) ";
    $query .= "values (".$cod_msg.", ".$nova_relevancia.", ".$data.")";
    $res = Enviar($sock, $query);
  }

  Desconectar($sock);

  // Imprime no div valores do formulário
  $objResponse->assign("msg_".$cod_msg, "innerHTML", "<b>".print_r($nova_relev, true)."</b>");
  $objResponse->addEvent("msg_".$cod_msg, "onClick", "AlteraCodMsg('".$cod_msg."', '".$nova_relevancia."'); MostraLayer(relevIni,0);");

  $objResponse->call("mostraFeedback", $texto, 'true');

//  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}


/* ***************************************************************************************
  MostraMensagemDinamic - mostrar mensagens dinamicamente
  Entradas: $cod_curso - codigo do curso
            $cod_msg - codigo da mensagem
            $cod_forum - codigo do forum
            $status - status do forum: A - Ativo (escrita e leitura)
                                       L - somente leitura
                                       D - deletado (encontra-se na lixeira)
                                       X - excluido
           $mensagem - conteudo da mensagem
  Saida: XML da funcao ajax
****************************************************************************************/
function MostraMensagemDinamic($cod_curso, $cod_msg, $cod_forum, $status, $mensagem)
{
  $objResponse = new xajaxResponse();
  $sock=Conectar($cod_curso);  
  
  list($total, $mensagem_dados) = RetornaMensagem($sock, $cod_msg, $cod_forum, $status);
  
  $objResponse->assign("td_msg_".$cod_msg, "innerHTML", "<b>".$mensagem.":</b><br /><br />". Enter2BR(LimpaConteudo($mensagem_dados['mensagem'])) );
  
  Desconectar($sock);
  //  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;  
}


/* ********************************************************************************************
  MudarConfiguracaoDinamic - mudar a configuracao dinamicamente
  Entradas: $dadosForm - array contendo: ['cod_curso']
                                         ['cod_forum']
            $nova_conf - nova configuracao
  Saida: XML da funcao ajax
*********************************************************************************************/
function MudarConfiguracaoDinamic($dadosForm, $nova_conf)
{
  $objResponse = new xajaxResponse();
  
  $cod_curso=$dadosForm['cod_curso'];
  $cod_forum=$dadosForm['cod_forum'];
  $data = time();
  
  $sock=Conectar($cod_curso);
  
  if(($nova_conf=='A')||($nova_conf=='L')){
    if (ConfiguraForum($sock, $cod_forum, $nova_conf))
    {
      $texto = $dadosForm['texto_feedback_sucesso'];
      $status = 'true';

      $query="DELETE FROM Forum_permissoes WHERE Forum_permissoes.cod_forum=".$cod_forum; //limpa a tabela dos participantes e grupos com permiss�
      Enviar($sock,$query);
      $objResponse->script("document.getElementById('forum_".$cod_forum."').setAttribute('status_conf', '".$nova_conf."');\n");
      $objResponse->script("document.getElementById('forum_".$cod_forum."').style.fontWeight='bold';\n");
      if($nova_conf=='A'){
        $objResponse->script("document.getElementById('forum_leitura_".$cod_forum."').innerHTML='';\n");
      }else{
        $objResponse->script("document.getElementById('forum_leitura_".$cod_forum."').innerHTML=msg_40;\n");
      }
    }
    else
    {
      $texto = $dadosForm['texto_feedback_falha'];
      $status = 'false';
      /* 57 - Erro ao alterar a configura�o. */
    }
    $objResponse->call("mostraFeedback", $texto, $status);
  }

  Desconectar($sock);

//  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}



/* **************************************************************************************
   EditarTituloDinamic - Edita o título do fórum dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_forum - fórum ao qual o endereco estará associado
            $novo_nome - novo título do fórum
   Saida: XML da função Ajax
****************************************************************************************/
function EditarTituloDinamic ($cod_curso, $cod_forum, $novo_nome, $texto)
{
  $objResponse = new xajaxResponse();

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $sock=Conectar($cod_curso);
  $consulta="update Forum set nome='".htmlentities($novo_nome)."', data=".$data." where cod_forum=".$cod_forum;
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  // Imprime no div valores do formulário
  $objResponse->assign("link_forum_".$cod_forum, "innerHTML", htmlentities($novo_nome));
  $objResponse->assign("link_forum_".$cod_forum, "style.fontWeight", "bold");
  $objResponse->assign("forumQtd_".$cod_forum, "style.fontWeight", "bold");  

  $objResponse->call("mostraFeedback", $texto, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}


function PreparaExibicaoMensagem($mensagem){		  
  return (ConverteBarraAspas2Aspas(Enter2BR(LimpaConteudo($mensagem))));
}

/* *********************************************************************
CancelaEdicaoAvaliacao - Cancela a edicao de uma avaliacao
Entrada: $sock - BASE DO CURSO
         $tabela - tabela a ser usada (Avaliacao)
         $cod_avaliacao - codigo da avaliacao

Saida: true se a edi�o foi cancelada corretamente
*/

function CancelaEdicaoAvaliacao ($sock, $cod_atividade,$cod_usuario)
{
 $consulta="select cod_avaliacao from Avaliacao where cod_atividade=".$cod_atividade;
 $res=Enviar($sock, $consulta);
 $linha=RetornaLinha($res); 

 $consulta="delete from Avaliacao where cod_atividade=".$cod_atividade;
 $res=Enviar($sock, $consulta);
 
 $consulta="delete from Avaliacao_historicos where cod_avaliacao=".$linha['cod_avaliacao'];
 $res=Enviar($sock, $consulta);

}

/* *********************************************************************
RetornaStatusAvaliacao - Retorna  o Status da Avaliacao (C,E,F,A ou X)
Entrada: $sock - BASE DO CURSO
         $tabela - tabela a ser usada (Avaliacao)
         $cod_avaliacao - codigo da avaliacao

Saida: status da avaliacao
*/
function RetornaStatusAvaliacao($sock, $cod_avaliacao)
{
$query="select status from Avaliacao where cod_avaliacao=".$cod_avaliacao;
$res=Enviar($sock,$query);
$linha=RetornaLinha($res);
return($linha);

}

function ListaUsuarios($sock,$tipo_usuario,$cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query  = "select UC.cod_usuario, U.nome, UC.tipo_usuario ";
  $query .= "from ".$dbnamebase.".Usuario_curso UC ";
  $query .= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
  $query .= "where UC.cod_usuario!=-1 ";
  $query .= "  and binary UC.tipo_usuario=\"".$tipo_usuario."\" ";
  $query .= "  and UC.cod_curso=\"".$cod_curso."\" ";
  $query .= "order by nome";
  $res=Enviar($sock,$query);

  $lista=RetornaArrayLinhas($res);
  return ($lista);
}
?>
