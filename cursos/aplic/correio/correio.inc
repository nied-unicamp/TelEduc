<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/correio/correio.inc

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
*/
/*==========================================================
  ARQUIVO : cursos/aplic/correio/correio.inc
  ========================================================== */

/*
Biblioteca de funcoes correio.inc
Para uso com a Ferramenta correio no Programa Teleduc
Autor: Leonel Aguilar Gayard
*/

/* ind_a
Funï¿½ï¿½es nesta biblioteca:
RetornaNomeGrupo - PROVISï¿½RIO
RetornaUsuariosGrupo - PROVISï¿½RIO
RetornaListaMensagensNovas
RetornaListaMensagensRecebidas
RetornaListaMensagensEnviadas
RetornaListaMensagensLixeira
ComparaAutorCres
ComparaAutorDesc
ComparaAutorDesc
ComparaDestDescLixeira
RetornaNumMensagens
RetornaInfosMensagem
RetornaCodigoGruposDoCurso
RetornaNomeUsuarioDeCodigo
RetornaNumArquivosDaMensagem
RetornaCodigoFormadoresDoCurso
RetornaEstadoDaMensagemPorExtenso
RetornaCodigoAlunosDoCurso

RetornaCodigoColaboradoresDoCurso

RetornaGrupoComCodigo
InsereMsgCM
InsereMsgCLDCD
InsereMsgCD
PrimeiroDestinatarioDaMensagem
ListaCompletaDestinatarios
ApagaMsgCD
ApagaMsgCM
ApagaDefCM
ApagaDefCD
RetornaEstadoAbrev
AtualizaEstadoMsgCD
RetornaNumDestinatariosMsg
RetornaCategDestinoCodUsuarioMsg
EndentarMensagem
UsuarioEhDestinatarioDaMensagem
UsuarioEhDestinatarioDaMensagemApagada
RecuperaCM
RecuperaCD

ind_b
*/


/* *******************************************************************
   RetornaConfig - retorna o valor associado a um item da tabela Config
                   externa (comum ao ambiente). Usado pela Funï¿½ï¿½o
                   CriaArquivoSendmail().
   entrada: $sock - sock de EXTERNO
            $item - item cujo valor correspondente ï¿½ requisitado
   saida:   valor correspondente ao item requisitado.
*/

function RetornaConfig($sock, $item)
{
  $query="select valor from Config where item = '".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha[0]);
}

/* *******************************************************************
   RetornaNomeGrupo - retorna o nome do grupo especificado
   entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo
   saida:   nome do grupo
*/
function RetornaNomeGrupo($sock,$cod_grupo)
{
  $query="select nome from Grupos where status <> 'X' and cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha['nome']);
}

/* *******************************************************************
   RetornaDadosTodosGrupos - Retorna o nome do grupo e os nomes e e-mail's
                             dos partipantes.
   entrada: $sock - sock de conexao
   saida:   array []['nome'] - nome do grupo
                  []['dados']['nome'] - nome do participante
                  []['dados']['email'] - email do participante
*/
function RetornaDadosTodosGrupos($sock, $cod_curso)
{
  /* select dos grupos que estï¿½o com status != apagado */
  $query="select cod_grupo,nome from Grupos where (strcmp(status, 'X') != 0)";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  $total=RetornaNumLinhas($res);

  for ($j = 0; $j < $total; $j++)
  {
    $lista_retorno[$j]['nome'] = $lista[$j]['nome'];
    $lista_retorno[$j]['dados'] = RetornaUsuariosGrupo($sock, $lista[$j]['cod_grupo'], $cod_curso);
  }

  return ($lista_retorno);
}

/* *******************************************************************
   RetornaUsuariosGrupo - retorna nomes e e-mail's dos participantes do
                          grupo especificado.
   entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo do qual listar os cod_usuarios
   saida:   array []['nome'] - nome do usuario
                  []['email'] - email do usuario
*/
function RetornaUsuariosGrupo($sock,$cod_grupo, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  /* todos os usuarios */
  $query  = "select U.nome,U.email, UC.cod_usuario from ".$dbnamebase.".Usuario as U, Grupos_usuario as GU, ".$dbnamebase.".Usuario_curso as UC where";
  $query .= " UC.cod_curso = ".$cod_curso." and U.cod_usuario=UC.cod_usuario_global and UC.cod_usuario >= 0 and";
  $query .= " (binary UC.tipo_usuario = 'A' or binary UC.tipo_usuario = 'F') and";
  $query .= " (UC.cod_usuario = GU.cod_usuario) and (GU.cod_grupo = ".$cod_grupo.") order by UC.tipo_usuario, U.nome";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  $total=RetornaNumLinhas($res);

  for ($i = 0; $i < $total; $i++)
  {
//    $lista_retorno['cod_usuario'][$i] = $lista[$i]['cod_usuario'];
    $lista_retorno['nome'][$i] = $lista[$i]['nome'];
    $lista_retorno['email'][$i] = $lista[$i]['email'];
    $lista_retorno['cod_usuario'][$i] = $lista[$i]['cod_usuario'];
  }
  return ($lista_retorno);
}

/* *******************************************************************
   RetornaDadosTodosUsuarios - retorna os e-mail's de todos usuï¿½rios.
   entrada: $sock - sock de conexao
   saida:   array []['email'] - email do usuario
*/
function RetornaDadosTodosUsuarios($sock,$cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select U.email, U.cod_usuario from ".$dbnamebase.".Usuario U, ".$dbnamebase.".Usuario_curso UC where UC.cod_curso = ".$cod_curso." and U.cod_usuario = UC.cod_usuario_global and ((binary UC.tipo_usuario = 'A') or (binary UC.tipo_usuario = 'F') or (binary UC.tipo_usuario = 'Z'))";
  
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  return ($lista);
}

/* *******************************************************************
   RetornaDadosCategUsuarios - retorna os e-mail's dos usuï¿½rios especificados.
   entrada: $sock - sock de conexao,
            $status - A: aluno
                      F: formador
   saida:   array []['email'] - email do usuario
*/
function RetornaDadosCategUsuarios($sock, $status)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select U.email from ".$dbnamebase.".Usuario U, ".$dbnamebase.".Usuario_curso UC where UC.cod_curso = ".$_SESSION["cod_curso_s"]." and (strcmp(UC.tipo_usuario,'".$status."') = 0)";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  return ($lista);
}

/* *******************************************************************
   RetornaDadosUsuario - retorna o nome e o e-mail do usuï¿½rio especificado.
   entrada: $sock - sock de conexao,
            $cod_usuario - cï¿½digo do usuï¿½rio,
            $status - A: aluno
                      F: formador
   saida:   array []['nome'] - nome do usuario
                  []['email'] - email do usuario
*/
function RetornaDadosUsuario($sock, $cod_usuario, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);
  $query="select nome,email from ".$dbnamebase.".Usuario where cod_usuario = ".$cod_usuario_global;

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  return ($lista[0]);
}

/* *******************************************************************
  RetornaListaMensagensNovas - retorna lista com somente as mensagens novas
                               para o usuario
  Entrada: $sock - sock de conexao
           $cod_usuario - codigo do usuario
           $ordem - parametro de ordenacao
  Saida:   array bidimensional, sendo o segundo array com:
           ['cod_msg'] - codigo da mensagem
           ['assunto'] - assunto da mensagem
           ['cod_usuario_autor'] - codigo do usuario autor da mensagem
           ['data'] - data da mensagem em UnixTime
           ['nome'] - nome do autor da mensagem
*/
function RetornaListaMensagensNovas($sock,$cod_usuario,$ordem)
{
  $query="select cod_msg from Correio_destinos where cod_usuario=".$cod_usuario." and estado='N'";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  $where_clause="CM.cod_msg=";
  if (count($lista)>0)
    foreach($lista as $cod=>$linha)
      $where_clause.=$linha['cod_msg']." or CM.cod_msg=";
  $where_clause.="-1"; /* Sï¿½ para simplificar o cï¿½digo, evitando fazer teste para caso vazio */

  $array_ordem="DataDesc,DataCres,AssuntoDesc,AssuntoCres,RemetenteDesc,RemetenteCres";
  $tmp_ordem=explode(",",$array_ordem);
  $array_base="CM.data DESC,CM.data,CM.assunto DESC,CM.assunto,U.nome DESC,U.nome";
  $tmp_base=explode(",",$array_base);

  $num=0;
  while($ordem != $tmp_ordem[$num] && $num < count($tmp_ordem))
    $num++;
  /* aqui, eh uma pesquisa nos arrays auxiliares para encontrar o correspondente MySQL para
     o parametro $ordem passado */

  $ordem_base=$tmp_base[$num];

  $query  = "select CM.cod_msg as cod_msg,CM.assunto as assunto,CM.cod_usuario as cod_usuario_autor,CM.data as data,U.nome as nome ";
  $query .= "from Correio_mensagens as CM,Usuario as U ";
  $query .= "where (".$where_clause.") and U.cod_usuario=CM.cod_usuario order by ".$ordem_base;

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  return $lista;
}

/* *******************************************************************
  RetornaListaMensagensRecebidas - retorna lista de mensagens novas e recebidas
                                        do usuario
  Entrada: $sock - sock de conexao
           $cod_usuario - codigo do usuario
           $ordem - parametro de ordenacao
  Saida:   array bidimensional, o segundo array com:
           ['cod_msg'] - codigo da mensagem
           ['assunto'] - assunto da mensagem
           ['cod_usuario_autor'] - codigo do usuario autor da mensagem
           ['data'] - data da mensagem em UnixTime
           ['nome'] - nome do usuario autor da mensagem
*/
function RetornaListaMensagensRecebidas($sock,$cod_usuario,$ordem){
  $query="select cod_msg from Correio_destinos where cod_usuario=".$cod_usuario." and (estado='N' or estado='L' or estado='R' or estado='U')";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  $array_ordem="DataDesc,DataCres,AssuntoDesc,AssuntoCres,RemetenteDesc,RemetenteCres";
  $tmp_ordem=explode(",",$array_ordem);
  $array_base="CM.data DESC,CM.data,CM.assunto DESC,CM.assunto,U.nome DESC,U.nome";
  $tmp_base=explode(",",$array_base);

  $num=0;
  while($ordem != $tmp_ordem[$num] && $num < count($tmp_ordem))
    $num++;
  /* aqui, eh uma pesquisa nos arrays auxiliares para encontrar o correspondente MySQL para
     o parametro $ordem passado */

  $ordem_base=$tmp_base[$num];

  $where_clause="cod_msg=";
  foreach($lista as $cod=>$linha)
    $where_clause.=$linha[0]." or cod_msg=";
  $where_clause.="-1"; /* Sï¿½ para simplificar o cï¿½digo, evitando fazer teste para caso vazio */

  /* codigo do usuario autor da mensagem */
  $query="select CM.cod_msg as cod_msg,CM.assunto as assunto,CM.cod_usuario as cod_usuario_autor,CM.data as data,U.nome as nome from Correio_mensagens as CM,Usuario as U where (".$where_clause.") and U.cod_usuario=CM.cod_usuario order by ".$ordem_base;

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}

function RetornaListaMensagensRecebidas2($sock,$cod_usuario = '',$ordem = ''){
  
  $query = "select * from Correio_mensagens CM, Correio_destinos CD where CD.cod_usuario=".$cod_usuario." and CD.estado <> 'A' and CD.estado <> 'X' and CD.cod_msg = CM.cod_msg order by data desc";
  $consulta = Enviar($sock, $query);
  $resultado = RetornaArrayLinhas($consulta);


// $i=0;
//   foreach($resultado as $campo => $valor){
//     $query = "select * from Correio_mensagens where cod_msg = ". $valor[0] ." order by data desc";
//     $consulta = Enviar($sock, $query);
//     $resultado = RetornaArrayLinhas($consulta);
//     if($resultado) $seila[$i] = $resultado[0];
//     $i++;
//   }
  return($resultado);
}

function RetornaListaMensagensEnviadas2($sock,$cod_usuario,$ordem){
  $query = "select * from Correio_mensagens where cod_usuario=".$cod_usuario." and (estado <> 'A') and (estado <> 'X') order by data desc";
  $consulta = Enviar($sock, $query);
  $resultado = RetornaArrayLinhas($consulta);
  return($resultado);
}

/* *******************************************************************
  RetornaListaMensagensEnviadas - retorna lista de mensagens enviadas pelo usuario
  Entrada: $sock - sock de conexao
           $cod_usuario - codigo do usuario
           $ordem - parametro de ordenacao
  Saida:   array bidimensional, o segundo array com:
           []['cod_msg'] - codigo da mensagem
           []['assunto'] - assunto da mensagem
           []['data'] - data da mensagem em UnixTime
           []['destinatario'] - destinatario da mensagem. Se for mais de um, retorna o
                                primeiro seguido de " ..."
*/
function RetornaListaMensagensEnviadas($sock,$cod_usuario,$ordem, $lista_frases, $cod_curso)
{
  $array_ordem="DataDesc,DataCres,AssuntoDesc,AssuntoCres,DestinatarioDesc,DestinatarioCres";
  $tmp_ordem=explode(",",$array_ordem);
  $array_base="data DESC,data,assunto DESC,assunto,---,---";
  $tmp_base=explode(",",$array_base);

  /* aqui, eh uma pesquisa nos arrays auxiliares para encontrar o correspondente MySQL para
     o parametro $ordem passado */
  $num=0;
  while($ordem != $tmp_ordem[$num] && $num < count($tmp_ordem))
    $num++;

  if ($ordem != "DestinatarioDesc" && $ordem != "DestinatarioCres")
  {
    /* $ordem nao eh DestinatarioDesc nem DestinatarioCres */
    $ordem_base="order by ".$tmp_base[$num];
  }

  /* else = ordenacao por destinatario */

  $query="select cod_msg, assunto, data from Correio_mensagens where (cod_usuario=".$cod_usuario.") and (estado = 'E' or estado = 'R') ".$ordem_base;
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0)
  {
    $cont=0;
    while ($cont<count($lista))
    {
      $lista[$cont]['destinatario']=PrimeiroDestinatarioDaMensagem($sock,$lista[$cont]['cod_msg'], $lista_frases, $cod_curso);
      $cont++;
    }
    if ($ordem == "DestinatarioDesc")
      uasort($lista,ComparaDestinatarioDescrescente);
    else if ($ordem == "DestinatarioCres")
      uasort($lista,ComparaDestinatarioCrescente);
  }
  return $lista;
}

/* *******************************************************************
  ComparaDestinatarioDescrescente - usado internamente com o uasort.
                                    compara o conteudo de uma sequencia
*/
function ComparaDestinatarioDescrescente($ar1, $ar2)
{
  if ($ar1['destinatario']>$ar2['destinatario'])
    return -1;
  else if ($ar1['destinatario']<$ar2['destinatario'])
    return 1;
  return 0;
}

/* *******************************************************************
  ComparaDestinatarioCrescente - usado internamente com o uasort.
                                 compara o conteudo de uma sequencia
*/
function ComparaDestinatarioCrescente($ar1, $ar2)
{
  if ($ar1['destinatario']>$ar2['destinatario'])
    return 1;
  else if ($ar1['destinatario']<$ar2['destinatario'])
    return -1;
  return 0;
}


function RetornaListaMensagensLixeira2($sock, $cod_usuario, $ordem){
  $query = "select CM.cod_msg, CM.cod_usuario, CM.assunto, CM.data, CM.mensagem, CM.estado, cod_msg_anterior from Correio_mensagens CM left join Correio_destinos CD on CD.cod_msg = CM.cod_msg where CD.cod_usuario = ".$cod_usuario." and CD.estado='A' UNION select CM.cod_msg, CM.cod_usuario, CM.assunto, CM.data, CM.mensagem, CM.estado, cod_msg_anterior from Correio_mensagens CM where CM.cod_usuario=".$cod_usuario." and CM.estado='A' order by data desc";


  $consulta = Enviar($sock, $query);
  $resultado = RetornaArrayLinhas($consulta);
  return $resultado;
}

/* *******************************************************************
  RetornaListaMensagensLixeira - Retorna Lista de mensagens da lixeira
  Entrada: $sock - sock de conexao
           $cod_usuario - codigo do usuario
           $ordem - parametro de ordenacao
  Saida:   array bidimensional, o segundo array com:
           []['cod_msg'] - codigo da mensagem
           []['assunto'] - assunto da mensagem
           []['cod_usuario'] - codigo do usuario autor da mensagem
           []['nome_autor'] - nome do autor da mensagem
           []['destinos'] - nome dos destinatarios da mensagem
           []['data'] - data da mensagem, em UnixTime

*/
function RetornaListaMensagensLixeira($sock,$cod_usuario,$ordem, $lista_frases, $cod_curso)
{
  /* Mensagens que foram recebidas e apagadas */
  $query="select cod_msg from Correio_destinos where estado='A' and cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  /* montando array com cod_msgs retornados */
  $num=RetornaNumLinhas($res);

  for ($i=0;$i<$num;$i++)
  {
    $linha=RetornaLinha($res);
    $lista_recebidas[$i]=$linha['cod_msg'];
  }

  /* Mensagens que foram enviadas e apagadas */
  $query="select cod_msg from Correio_mensagens where estado='A' and cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  /* montando array com cod_msgs retornados */
  $num=RetornaNumLinhas($res);
  unset($linha);

  for ($i=0;$i<$num;$i++)
  {
    $linha=RetornaLinha($res);
    $lista_enviadas[$i]=$linha['cod_msg'];
  }

  /* merge dos dois arrays */
  
    if (($lista_recebidas == NULL) && ($lista_enviadas == NULL))
        $lista_query = NULL;
    else if (($lista_recebidas == NULL) && ($lista_enviadas != NULL))
        $lista_query = array_unique($lista_enviadas);
    else if (($lista_recebidas != NULL) && ($lista_enviadas == NULL))
        $lista_query = array_unique($lista_recebidas);
    else
        $lista_query = array_unique(array_merge($lista_recebidas, $lista_enviadas));
 	

  $array_ordem="DataDesc,DataCres,AssuntoDesc,AssuntoCres,DestinatarioDesc,DestinatarioCres,RemetenteDesc,RemetenteCres";
  $tmp_ordem=explode(",",$array_ordem);
  $array_base="data DESC,data,assunto DESC,assunto,---,---,---,---";
  $tmp_base=explode(",",$array_base);

  /* busca no array para identificar parametro de ordenacao */
  $num=0;
  while($ordem != $tmp_ordem[$num] && $num < count($tmp_ordem))
    $num++;

  /* 0 - DataDesc     4 - DestinatarioDesc
     1 - DataCres     5 - DestinatarioCres
     2 - AssuntoDesc  6 - RemetenteDesc
     3 - Assunto Cres 7 - RemetenteCres
  */

  /* se ordenacao vai ser feita pela base de dados ou pela funcao uasort */
  if ($num != 4 && $num != 5 && $num != 6 & $num != 7)
  {
    $ordenar_base=true;   /* ordenar pela base */
    $param_ordenacao="order by ".$tmp_base[$num];
  }
  else
  {
    $ordenar_base=false;
  }

  if (count($lista_query) > 0)
  {
    $where_clause="cod_msg=";
    foreach($lista_query as $cod => $linha)
      $where_clause.=$linha." or cod_msg=";
    $where_clause.="-1";

    $query="select cod_msg, assunto, cod_usuario, data from Correio_mensagens where (".$where_clause.") ".$param_ordenacao;
    $res=Enviar($sock,$query);
    $lista_retorno=RetornaArrayLinhas($res);

    /* adicionando remetente e destinatarios da msg ao array de retorno*/
    $c=0;
    while ($c<count($lista_retorno))
    {

      $lista_retorno[$c]['nome_autor']=RetornaNomeUsuarioDeCodigo($sock,$lista_retorno[$c]['cod_usuario'], $cod_curso);
      $lista_retorno[$c]['destinos']=PrimeiroDestinatarioDaMensagem($sock,$lista_retorno[$c]['cod_msg'], $lista_frases, $cod_curso);


      $c++;
    }

    if (!$ordenar_base) /* ordenar pela funcao uasort */
    {
      if ($ordem == "DestinatarioDesc")
        uasort($lista_retorno,ComparaDestDescLixeira);
      else if($ordem == "DestinatarioCres")
        uasort($lista_retorno,ComparaDestCresLixeira);
      else if($ordem == "RemetenteDesc")
        uasort($lista_retorno,ComparaAutorDesc);
      else if($ordem == "RemetenteCres")
        uasort($lista_retorno,ComparaAutorCres);
    }

    return($lista_retorno);
  }
  else
  {
    return(null);
  }
}

/* *******************************************************************
  ComparaAutorCres - usado internamento com o uasort. (ordem crescente)
                     compara os destinatarios das mensagens na lixeira
*/
function ComparaAutorCres($ar1,$ar2)
{
  if ($ar1['nome_autor']>$ar2['nome_autor'])
    return 1;
  else if ($ar1['nome_autor']<$ar2['nome_autor'])
    return -1;
  return 0;
}

/* *******************************************************************
  ComparaAutorDesc - usado internamento com o uasort. (ordem decrescente)
                     compara os autores das mensagens na lixeira
*/
function ComparaAutorDesc($ar1,$ar2)
{
  if ($ar1['nome_autor']>$ar2['nome_autor'])
    return -1;
  else if ($ar1['nome_autor']<$ar2['nome_autor'])
    return 1;
  return 0;
}

/* *******************************************************************
  ComparaDestCresLixeira - usado internamento com o uasort. (ordem crescente)
                           compara os destinatarios das mensagens na lixeira
*/
function ComparaDestCresLixeira($ar1,$ar2)
{
  if ($ar1['destinos']>$ar2['destinos'])
    return 1;
  else if ($ar1['destinos']<$ar2['destinos'])
    return -1;
  return 0;
}

/* *******************************************************************
  ComparaDestDescLixeira - usado internamento com o uasort. (ordem decrescente)
                           compara os destinatarios das mensagens na lixeira
*/
function ComparaDestDescLixeira($ar1,$ar2)
{
  if ($ar1['destinos']>$ar2['destinos'])
    return -1;
  else if ($ar1['destinos']<$ar2['destinos'])
    return 1;
  return 0;
}

/* **********************************************************************
  RetornaNumMensagens - Retorna numero de mensagens na base de dados
                             dentro daquele item de mensagem
  Entrada: $sock - sock de conexao
           $letra_item - letra do item para a mensagem
                         Pode ser: 'N'ova,'E'nviadas,'R'ecebidas,'L'ixeira
           $cod_usuario - codigo do usuario
  Saida  : numero de mensagens naquele item
*/
function RetornaNumMensagens($sock,$letra_item,$cod_usuario)
{
  if ($letra_item == 'N')
  {
     //'N' - novas 
    $query="select count(*) from Correio_destinos where estado='N' and cod_usuario=".$cod_usuario;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    return $linha[0];
  }
  else if ($letra_item == 'R')
  {
    /* 'R' - respondidas, 'N' - novas, 'L' - lidas, 'U' - recuperadas */
    $query="select count(*) from Correio_destinos where (estado='R' or estado='N' or estado='L' or estado='U') and cod_usuario=".$cod_usuario;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    return $linha[0];
  }
  else if ($letra_item == 'E')
  {
    /* 'E' para Enviadas e 'R' para recuperada */
    $query="select count(*) from Correio_mensagens where (estado='E' or estado='R') and cod_usuario=".$cod_usuario;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);

    return $linha[0];
  }
  else if ($letra_item == 'L')
  {
    /* busca pelas mensagens enviadas e apagadas */
    $query="select cod_msg from Correio_mensagens where cod_usuario=".$cod_usuario." and estado='A'";
    $res=Enviar($sock,$query);
    /* montando array com cod_msgs de retorno */
      
    $num1=RetornaNumLinhas($res);
       
    for ($c=0;$c<$num1;$c++)
    {
      $linha=RetornaLinha($res);
      $lista1[$c]=$linha['cod_msg'];
    }
    /* busca pelas mensagens recebidas e apagadas */
    $query="select cod_msg from Correio_destinos where cod_usuario=".$cod_usuario." and estado='A'";
    $res=Enviar($sock,$query);
    /* montando array com cod_msgs de retorno */
    $num2=RetornaNumLinhas($res);
    
    for ($c=0;$c<$num2;$c++)
    {
      $linha=RetornaLinha($res);
      $lista2[$c]=$linha['cod_msg'];
    }
    
        /* faz o merge dos dois arrays caso os dois nao sejam vazios*/
    if (($lista1 == NULL) && ($lista2 == NULL))
        $lista_retorno = NULL;
    else if (($lista1 == NULL) && ($lista2 != NULL))
        $lista_retorno = array_unique($lista2);
    else if (($lista1 != NULL) && ($lista2 == NULL))
        $lista_retorno = array_unique($lista1);
    else
        $lista_retorno = array_unique(array_merge($lista1, $lista2));

    /* retorna a contagem de elementos */
    $num=count($lista_retorno);
    return($num);
  }
}

/* *************************************************************************
  RetornaInfosMensagem - retorna codigo do autor, assunto, data e conteudo da msg
  Entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem
  Saida:   array com
         ['cod_usuario'] - codigo do autor da mensagem
         ['assunto'] - assunto da mensagem
         ['data'] - data da mensagem em UnixTime
         ['mensagem'] - conteudo da mensagem
*/
function RetornaInfosMensagem($sock,$cod_msg)
{
  $Query="select cod_usuario,assunto,data,mensagem from Correio_mensagens where cod_msg=".$cod_msg;
  $res=Enviar($sock,$Query);
  $linha=RetornaLinha($res);
  return $linha;
}

/* *************************************************************************
  RetornaCodigoGruposDoCurso - retorna array com os cod_grupo dos grupos do curso
  Entrada: $sock - sock de conexao
  Saida:   array com os cod_grupo dos grupos do curso
*/
function RetornaCodigoGruposDoCurso($sock)
{
  $query="select cod_grupo from Grupos where status != 'X' order by nome";
  $res=Enviar($sock,$query);
  $linha=RetornaArrayLinhas($res);
  return $linha;
}

/* *************************************************************************
  RetornaNomeUsuarioDeCodigo - retorna nome do usuario cujo codigo eh igual a entrada
  Entrada: $sock - sock de conexao
           $cod_usuario - codigo do usuario do qual procurar o nome
  Saida:   nome do usuario com codigo == $cod_usuario
*/

function RetornaNomeUsuarioDeCodigo($sock, $cod_usuario, $cod_curso)
{

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);

  $query="select nome from ".$dbnamebase.".Usuario where cod_usuario=".$cod_usuario_global;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['nome'];
}

/* *************************************************************************
  RetornaNumArquivosDaMensagem - retorna numero de arquivos anexos ï¿½ mensagem
  Entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem
  Saida:   numero de arquivos na mensagem
*/
function RetornaNumArquivosDaMensagem($sock,$cod_msg)
{
  $query="select count(arquivo) from Correio_anexos where cod_msg=".$cod_msg;
  $res=Enviar($sock,$query);
  $num=RetornaLinha($res);
  return $num;
}

/* *************************************************************************
  RetornaCodigoFormadoresDoCurso - retorna array com os cod_usuario dos formadores do curso
  Entrada: $sock - sock de conexao
  Saida:   array com os cod_usuario dos formadores do curso
*/
function RetornaCodigoFormadoresDoCurso($sock, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query  = "select U.nome, UC.cod_usuario ";
  $query .= "from ".$dbnamebase.".Usuario U ";
  $query .= "inner join ".$dbnamebase.".Usuario_curso UC ON (UC.cod_usuario_global=U.cod_usuario) ";
  $query .= "where UC.cod_curso=".$cod_curso." AND binary UC.tipo_usuario = 'F' order by U.nome asc";

  $res=Enviar($sock,$query);
  $linha=RetornaArrayLinhas($res);
  return $linha;
}

/* **********************************************************************
  RetornaEstadoDaMensagemPorExtenso - Percorre as tabela CD ou CM, para encontrar o estado
                                           da msg para o usuario
  Entrada: $sock - sock de conexao
           $letra_item - letra do item de mensagens
           $cod_msg - codigo da mensagem
           $cod_usuario - codigo do usuario
  Saida:   se $letra_item == 'R' e $abrev == 'N'=> "nova"
           se $letra_item == 'R' e $abrev == 'A'=> "apagada"
           se $letra_item == 'R' e $abrev == 'R'=> "respondida"
           se $letra_item == 'R' e $abrev == 'L'=> "lida"
           se $letra_item == 'R' e $abrev == 'U'=> "recuperada"
           se $letra_item == 'E' e $abrev == 'E' => "enviada"
           se $letra_item == 'E' e $abrev == 'A' => "apagada"
           se $letra_item == 'E' e $abrev == 'R' => "recuperada"
*/
function RetornaEstadoDaMensagemPorExtenso($sock,$letra_item,$cod_msg,$cod_usuario, $lista_frases){

  if ($letra_item == 'R'){ /* usuario eh destinatario da msg: buscar estado em CD */
    $query="select estado from Correio_destinos where cod_msg=".$cod_msg." and cod_usuario=".$cod_usuario;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $abrev=$linha['estado'];

    /* 49 - Nova */
    $tabela['N']=RetornaFraseDaLista($lista_frases,49);
    /* 50 - Apagada*/
    $tabela['A']=RetornaFraseDaLista($lista_frases,50);
    /* 51 - Respondida*/
    $tabela['R']=RetornaFraseDaLista($lista_frases,51);
    /* 52 - Lida*/
    $tabela['L']=RetornaFraseDaLista($lista_frases,52);
    /* 53 - Recuperada */
    $tabela['U']=RetornaFraseDaLista($lista_frases,53);

    return $tabela[$abrev];
  }
  else if ($letra_item == 'E'){ /* usuario eh emissor da msg: buscar estado em CM */
    $query="select estado from Correio_mensagens where cod_msg=".$cod_msg;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $abrev=$linha['estado'];

    /* 54 - Enviada */
    $tabela['E']=RetornaFraseDaLista($lista_frases,54);
    /* 50 - Apagada */
    $tabela['A']=RetornaFraseDaLista($lista_frases,50);
    /* 53 - Recuperada */
    $tabela['R']=RetornaFraseDaLista($lista_frases,53);

    return $tabela[$abrev];
  }
}

function RetornaEstadoDaMensagem($sock, $modoVisualizacao, $cod_msg, $cod_usuario){

  if ($modoVisualizacao == 'R'){ /* usuario eh destinatario da msg: buscar estado em CD */
    $query="select estado from Correio_destinos where cod_msg=".$cod_msg." and cod_usuario=".$cod_usuario;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $abrev=$linha['estado'];
  }
  else if ($modoVisualizacao == 'E'){ /* usuario eh emissor da msg: buscar estado em CM */
    $query="select estado from Correio_mensagens where cod_msg=".$cod_msg;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $abrev=$linha['estado'];
  }

  return $linha['estado'];
}


function TrocaEstadoDaMensagemRecebidas($sock, $estado, $cod_msg, $cod_usuario){
  $query = "update Correio_destinos set estado = '".$estado."' where cod_msg = ".$cod_msg." and cod_usuario = ".$cod_usuario;
  $res = Enviar($sock, $query);
}

function TrocaEstadoDaMensagemEnviadas($sock, $estado, $cod_msg, $cod_usuario, $var = ''){
  $query = "update Correio_mensagens set estado = '".$estado."' where cod_msg = ".$cod_msg." and cod_usuario = ".$cod_usuario;
  $res = Enviar($sock, $query);
}

function TrocaEstadoDaMensagemLixeira($sock, $estado, $cod_msg, $cod_usuario, $recuperar=''){
  $query = "update Correio_destinos set estado ='".$estado."' where cod_msg = ".$cod_msg." and estado = 'A' and cod_usuario = ".$cod_usuario;
  $res = Enviar($sock, $query);
  $query = "update Correio_mensagens set estado ='".$estado."' where (cod_msg = ".$cod_msg." and estado = 'A' and   cod_usuario = ".$cod_usuario.")";
  $res = Enviar($sock, $query);
}

/* *************************************************************************
  RetornaCodigoAlunosDoCurso - retorna array com os cod_usuario dos alunos do curso
  Entrada: $sock - sock de conexao
  Saida:   array com os cod_usuario dos alunos do curso
*/
function RetornaCodigoAlunosDoCurso($sock, $cod_curso){

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query  = "select U.nome, UC.cod_usuario ";
  $query .= "from ".$dbnamebase.".Usuario U ";
  $query .= "inner join ".$dbnamebase.".Usuario_curso UC ON (UC.cod_usuario_global=U.cod_usuario) ";
  $query .= "where UC.cod_curso=".$cod_curso." AND binary UC.tipo_usuario = 'A' order by U.nome asc";

  $res=Enviar($sock,$query);
  $linha=RetornaArrayLinhas($res);
  return $linha;
}

/* *************************************************************************
  RetornaGrupoComCodigo - retorna nome do grupo do codigo dado
  Entrada: $sock - sock de conexao
           $cod_grupo - codigo do grupo
  Saida: nome do grupo de codigo igual a $cod_grupo
*/
function RetornaGrupoComCodigo($sock,$cod_grupo){
  $query="select nome from Grupos where cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['nome'];
}

/* *************************************************************************
  InsereMsgCM - insere mensagem e parametros em Correio_mensagens
  Entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem
           $cod_autor - codigo do usuario autor da mensagem
           $assunto - assunto da mensagem
           $data - data da mensagem em UnixTime
           $mensagem - conteudo da mensagem
           $cod_msg_ant - codigo da mensagem anterior, caso esta aqui seja reply
  Saida:   nenhuma
*/
function InsereMsgCM($sock,$cod_msg,$cod_autor,$assunto,$data,$mensagem,$codMsgAnt){

  if(!($codMsgAnt)){
    $codMsgAnt = 'NULL';
  }

  $mensagem_limpa=EliminaScript($mensagem);
  $mensagem_limpa = trim($mensagem_limpa);

  $query="insert into Correio_mensagens values (".$cod_msg.",".$cod_autor.",'".LimpaTitulo($assunto)."',".$data.",'".$mensagem_limpa."','E',".$codMsgAnt.")";
  $res=Enviar($sock,$query);
  if ($codMsgAnt!="" && $codMsgAnt!="NULL"){
    $query="update Correio_destinos set estado='R' where cod_msg=".$codMsgAnt." and cod_usuario=".$cod_autor;
    Enviar($sock,$query);
  }

}

/* *************************************************************************
  InsereMsgCLDCD - insere parametros da mensagem em Correio_lista_destinos e chama InsereMsgCD
  Entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem
           $cod_autor - codigo do usuario autor da mensagem
           $categ - parametro de categoria dos destinatarios
                  'TT' - todos
                  'TG' - todos os grupos
                  'TA' - todos os alunos
                  'TF' - todos os formadores
                  'TC' - todos os colaboradores
                  'G' - grupo individual
                  'U' - usuario individual
           $cod_categ - codigo da categoria destinataria da mensagem.
                  -1 se $categ for 'TT,'TG','TA', 'TC' ou 'TF'
           $array_enviadas - array contendo os cod_usuario dos usuarios que ja receberam esta msg,
                           para que nao a recebam 2 vezes
  Saida:   $array_enviadas - mesmo array de entrada, aumentado com os usuarios que receberam a msg
                           ao final da funcao
                           Caso a msg seja enviada para todos (TT), retorna array vazio
  OBS: Existe uma certa confusao em relacao ao valor 'G':
  - Na base de dados, tabela Correio_lista_destinos, categ_destinos = a:
       'G' = msg para todos os grupos
       'g' = msg para um grupo individual

*/
function InsereMsgCLDCD($sock,$cod_msg,$cod_autor,$categ,$cod_categ,$array_enviadas, $cod_curso){

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  if ($categ == "G"){
    $query="insert into Correio_lista_destinos values (".$cod_msg.",".$cod_autor.",".$cod_categ.",'g')";
    $res=Enviar($sock,$query);

    $query="select distinct cod_usuario from Grupos_usuario where cod_grupo=".$cod_categ;
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);
    
    if (is_array ($lista))
      foreach ($lista as $cod=>$linha)
        if (!in_array($linha['cod_usuario'],$array_enviadas)){
          InsereMsgCD($sock,$cod_msg,$linha['cod_usuario'],$cod_autor);
          array_push($array_enviadas,$linha['cod_usuario']);
        }
    return ($array_enviadas);
  }
  else if ($categ == "U"){
    if (!in_array($cod_categ,$array_enviadas)){
      $query="insert into Correio_lista_destinos values (".$cod_msg.",".$cod_autor.",".$cod_categ.",'U')";
      $res=Enviar($sock,$query);
      InsereMsgCD($sock,$cod_msg,$cod_categ,$cod_autor);
      array_push($array_enviadas,$cod_categ);
    }
    return ($array_enviadas);
  }
  else if ($categ == "TG"){
    // msg enviada para todos grupos, por isso $cod_categ = -1
    $query = "insert into Correio_lista_destinos values (".$cod_msg.", ".$cod_autor.", -1, 'G')";
    Enviar ($sock, $query);

    // Para cada usuario em cada grupo nï¿½o-apagado, inserir em CD
    $query  = "select distinct GU.cod_usuario 'cod_usuario' ";
    $query .= "from Grupos_usuario GU, Grupos G ";
    $query .= "where G.status <> 'X' and ";
    $query .=       "G.cod_grupo=GU.cod_grupo";

    $res   = Enviar ($sock, $query);
    while ($linha = RetornaLinha ($res)){
      if (! in_array ($cod_usuario = $linha ['cod_usuario'], $array_enviadas)){
        InsereMsgCD ($sock, $cod_msg, $cod_usuario, $cod_autor);
        array_push  ($array_enviadas, $cod_usuario);
      }
    }
    
    return ($array_enviadas);
  }
  else if ($categ == "TA"){
     /* msg enviada para todos os alunos, por isso, $cod_categ = -1 */
    $query="insert into Correio_lista_destinos values (".$cod_msg.",".$cod_autor.",-1,'A')";
    $res=Enviar($sock,$query);

    $query="select cod_usuario from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso." and binary tipo_usuario='A' order by cod_usuario asc";
    $res=Enviar($sock,$query);

    if (RetornaNumLinhas($res) > 0){
     $lista = RetornaArrayLinhas($res);
     if (is_array ($lista))
     foreach ($lista as $cod=>$linha)
       if (!in_array($linha['cod_usuario'],$array_enviadas)){
         InsereMsgCD($sock,$cod_msg,$linha['cod_usuario'],$cod_autor);
         array_push($array_enviadas,$linha['cod_usuario']);
       }
     return ($array_enviadas);
    }
    else
     return (array());
  }
  else if ($categ == "TF"){
    /* msg enviada para todos os formadores, por isso, $cod_categ = -1 */
    $query="insert into Correio_lista_destinos values (".$cod_msg.",".$cod_autor.",-1,'F')";
    $res=Enviar($sock,$query);

    $query="select cod_usuario from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso." and binary tipo_usuario='F' order by cod_usuario asc";
    $res=Enviar($sock,$query);
    
    if (RetornaNumLinhas($res) > 0){
      $lista=RetornaArrayLinhas($res);
      if (is_array ($lista))
      foreach ($lista as $cod=>$linha){
        if (!in_array($linha['cod_usuario'],$array_enviadas)){
          InsereMsgCD($sock,$cod_msg,$linha['cod_usuario'],$cod_autor);
          array_push($array_enviadas,$linha['cod_usuario']);
        }
      }
    }
    return ($array_enviadas);
  }
  else if ("TC" == $categ){
    // msg enviada para todos os colaboradores.
    
    // insercao em CLD
    $query = "insert into Correio_lista_destinos values (".$cod_msg.", ".$cod_autor.", -1, 'Z')";
    $res   = Enviar ($sock, $query);
    
    // insercao em CD
    $query="select cod_usuario from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso." and binary tipo_usuario='Z' order by cod_usuario asc";
    $res   = Enviar ($sock, $query);
    
    while ($linha_colaboradores = RetornaLinha($res) ){
      $cod_colaborador = $linha_colaboradores['cod_usuario'];
      
      // checar se este colaborador nao esta no array de caras que jah receberam a mensagem
      if (! in_array ($cod_colaborador, $array_enviadas)){
        InsereMsgCD($sock, $cod_msg, $cod_colaborador, $cod_autor);
        array_push ($array_enviadas, $cod_colaborador);
      }
    }
    return $array_enviadas;
  }
  else if ($categ == "TT"){
    $query="insert into Correio_lista_destinos values (".$cod_msg.",".$cod_autor.",-1,'T')";
    $res=Enviar($sock,$query);

    // enviar para formadores, alunos e colaboradores
    $query="select cod_usuario from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso." and (binary tipo_usuario='F' or binary tipo_usuario='A' or binary tipo_usuario='Z') order by cod_usuario asc";
    $res=Enviar($sock,$query);
    
    if (RetornaNumLinhas($res) > 0){
      $lista = RetornaArrayLinhas($res);
      if (is_array ($lista))
      foreach ($lista as $cod => $linha){
        if (!in_array($linha['cod_usuario'],$array_enviadas)){
          InsereMsgCD($sock, $cod_msg, $linha['cod_usuario'], $cod_autor);
          array_push($array_enviadas, $linha['cod_usuario']);
        }
      }
    }
//    $array_retorno = array();
//    return ($array_retorno);
    return ($array_enviadas);
  }
  else{
    echo("<font class=\"text\" color=\"red\">Em correio.inc, funcao InsereMsgCLDCD, parametro \$categ fora do esperado</font>");
    die ();
  }
}

/* *************************************************************************
  InsereMsgCD - Insere parametros da msg em Correio_destinos e em Correio_intermap
  Entrada: $sock - sock de conexao
           $cod_msg - codigo da msg
           $cod_usuario_receptor - codigo do usuario destinatario da mensagem
           $cod_usuario_emissor - codigo do usuario emissor da mensagem
  Saida:   nenhuma
*/
function InsereMsgCD($sock,$cod_msg,$cod_usuario_receptor,$cod_usuario_emissor){
  $query="insert into Correio_destinos values (".$cod_msg.",".$cod_usuario_receptor.",'N')";
  $res=Enviar($sock,$query);
 
  $query="insert into Correio_intermap values (".$cod_msg.",".$cod_usuario_emissor.",".$cod_usuario_receptor.",".time().")";
  $res=Enviar($sock,$query);
}

/* *************************************************************************
  CriaArquivoSendmail - cria arquivo para envio do e-mail.
  Entrada: $host - hostname do servidor, usado no dominio do remetente
           $cod_msg - codigo da msg, usado para cifrar a mensagem
           $arq_sendmail - caminho onde serï¿½ gravado o arquivo do sendmail
           $corpo - corpo da mensagem
           $formato - formato do corpo da mensagem: html ou texto
           $anexos - array []['Caminho'] - caminho do arquivo anexado
                           []['Arquivo'] - nome do arquivo anexado
  Saida:   true se bem sucedido, do contrario, false.
*/
function CriaArquivoSendmail($informacoes, $cod_msg, $arq_sendmail, $corpo, $formato, $anexos)
{
  $retval = true;

  /* Cria uma chave de identificacao Unica (boundary) para separar os   */
  /* diversos conteudos.                                                */
  $horario = time();
  $boundary = "_-----_".$cod_msg."A".(crypt($horario."X", "CAS"))."B".$cod_msg;

  /* Cria o arquivo de configuracao do sendmail.                        */
  
  if ($fh=fopen($arq_sendmail,"w"))
  { 
    /* Obtem as configuracoes referente as aspas, desabilita o recurso */
    /* e no final da execucao o restaura.                              */
    $mqr_status = get_magic_quotes_runtime();
    set_magic_quotes_runtime(0);

    /* Grava o cabecalho da mensagem. */
    fputs($fh, "From: \"".$informacoes['nome']."\" <".$informacoes['remetente']."@".$informacoes['host'].">\n");

    fputs($fh, "Subject: "."TelEduc: \"".$informacoes['nome_curso']."\" - ".$informacoes['nome_ferramenta']."\n");
    fputs($fh, "Mime-Version: 1.0"."\n");
    fputs($fh, "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\n");

    // CABECALHOS PARA O CORPO DA MENSAGEM.
    /* grava o separador de conteudo. */
    fputs($fh, "--".$boundary."\n");
    fputs($fh, "Content-Type: text/html; charset=iso-8859-1; name=\"bdy.html\"\n");
    fputs($fh, "Content-Disposition: inline; filename=\"bdy.html\"\n");
    fputs($fh, "Content-Transfer-Encoding: 7bit\n\n");
    /* Atente para as duas linhas que separam os cabecalhos do conteudo. */

    /* Grava o texto do corpo da mensagem. */
    fputs($fh, $corpo."\n\n");

    /* Obtem o total de anexos, se houver. */
    $numanexos = count($anexos);
    for ($i = 0; $i < $numanexos; $i++)
    {
      /* Para cada anexo obtem a extensao em minusculas e utiliza a funcao */
      /* mime_lookup (autor: Paul Southworth) para obter o tipo mime do    */
      /* arquivo. Caso a funcao retorne nula, utiliza por padrao o tipo    */
      /* application/octet-stream.                                         */
      $ext = strtolower(substr(strrchr($anexos[$i]['Arquivo'], "."), 1));
      $fullmimetype = mime_lookup($ext);
      if ($fullmimetype == "")
        $fullmimetype = "application/octet-stream";

      /* Separa o tipo mime em: principal e submime. */
      $mimetype = explode("/", $fullmimetype);
      $mtype = $mimetype[0];
      $submtype = $mimetype[1];

      // CABEï¿½ALHOS PARA ARQUIVOS EM ANEXO.
      /* grava o separador de conteï¿½do. */
      fputs($fh, "--".$boundary."\n");
      fputs($fh, "Content-Type: ".$fullmimetype."; ");

      /* Compara o submime para definir o nome do arquivo caso seja texto, html, */
      /* ou outro arquivo.                                                       */
      if (!((strcasecmp($submtype, "text") == 0) || (strcasecmp($submtype, "html") == 0)))
        fputs($fh, "name=\"".$anexos[$i]['Arquivo']."\"\n");
      else if (strcasecmp($submtype, "text") == 0)
        fputs($fh, "charset=iso-8859-1; name=\"bdy.txt\"\n");
//      else if (strcasecmp($submtype, "html") == 0)
//        fputs($fh, "charset=iso-8859-1; name=\"bdy.html\"\n");

      fputs($fh, "Content-Disposition: attachment; filename=\"".$anexos[$i]['Arquivo']."\"\n");
      fputs($fh, "Content-Transfer-Encoding: base64\n\n");
      /* Atente para as duas linhas que separam os cabeï¿½alhos do conteï¿½do. */

      /* Se o arquivo a ser anexado tiver permissï¿½es de leitura, entï¿½o o codifica em */
      /* base64. Em seguida, o arquivo ï¿½ quebrado em pequenos pedaï¿½os para serem     */
      /* gravados.                                                                   */
      clearstatcache();
      if (is_readable($anexos[$i]['Caminho']))
      {
        $fd = fopen($anexos[$i]['Caminho'], "r");
        $contents = fread($fd, filesize($anexos[$i]['Caminho']));
        $encoded = chunk_split(base64_encode($contents));
        fclose($fd);

        fputs($fh, $encoded."\n");
      }
      else
        $retval = $retval && false;

    }
    /* grava o FINALIZADOR de conteï¿½do. Note que ï¿½ terminado com -- */
    fputs($fh, "--".$boundary."--\n");

    /* restaura o recurso de "aspas mï¿½gicas". */
    set_magic_quotes_runtime($mqr_status);
    fclose($fh);

  }
  else
    return false;

  return $retval;
}

/* *******************************************************************
  PrimeiroDestinatarioDaMensagem - Retorna primeiro usuario ou categoria destinatario da mensagem

  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem
  saida:   se categoria destinaria - Todos, Todos os alunos, Todos os formadores ou Todos os grupos
           se usuario ou grupo destinatario - Nome do usuario ou grupo
           se a saida for mais de uma string, retorna-se o primeiro nome seguido de "..."
*/
function PrimeiroDestinatarioDaMensagem($sock,$cod_msg, $lista_frases, $cod_curso){

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select cod_destino,categ_destino from Correio_lista_destinos where cod_msg=".$cod_msg." order by categ_destino limit 2";
  $res=Enviar($sock,$query);
  $num=RetornaNumLinhas($res);
  $lista=RetornaArrayLinhas($res);

  //  Existe uma hierarquias entre as categorias de destino, no hora de exibir o destinatario da mensagem.
  //  As proximas linhas encontram dentro do vetor de destinatarios o de maior hierarquia para exibir o destinatario
  $vetor_hierarquia = array (
                              'T' => 7,
                              'A' => 6,
                              'F' => 5,
                              'Z' => 4,
                              'G' => 3,
                              'g' => 2,
                              'U' => 1
                            );
  $tmp = 0;
  foreach ($lista as $linha){
    if ($tmp2 = $vetor_hierarquia [ $linha ['categ_destino'] ] > $tmp){
      $tmp              = $tmp2;
      $categ            = $linha ['categ_destino'];
      $cod_usuario_dest = $linha ['cod_destino'];
    }
  }

  if ('T' == $categ)
    // 29 - Todos 
    $retorno=RetornaFraseDaLista($lista_frases,29);
  else if ('G' == $categ)
    // 32 - Todos os grupos
    $retorno = RetornaFraseDaLista($lista_frases, 32);
  else if ('A' == $categ)
    // 31 - Todos os alunos 
    $retorno=RetornaFraseDaLista($lista_frases,31);
  else if ('F' == $categ)
    // 30 - Todos os formadores 
    $retorno=RetornaFraseDaLista($lista_frases,30);
  else if ('Z' == $categ)
    // 117 - Todos os colaboradores
    $retorno = RetornaFraseDaLista($lista_frases, 117);
  else if ('g' == $categ){
    // algum grupo especifico
    $query="select nome from Grupos where cod_grupo=".$cod_usuario_dest;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $retorno=$linha['nome'];
  }
  else if ('U' == $categ){
    // algum usuario especifico
    $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario_dest, $cod_curso);
    $query="select nome from ".$dbnamebase.".Usuario where cod_usuario=".$cod_usuario_global;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $retorno=$linha['nome'];
  }
  else{
    echo("<font class=\"text\" color=\"red\">Erro interno. Em correio.inc, funcao PrimeiroDestinatarioDaMensagem, parametro desconhecido </font><br>\n");
    die ();
  }

  if($num>1)
    $retorno.=" ...";

  return $retorno;
}

/* *******************************************************************
  ListaCompletaDestinatarios - Lista nome e cod_usuario de todos os destinatarios ou -1
                               se destinatario nao for usuario.
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem
  saida:   array bidimensional com
           []['nome'] - nome do destinatario
           []['codigo'] - se o destinatario for um usuario, aqui vai seu cod_usuario
                          senao, este valor eh -1
           []['status'] - se for grupo, = 'g'. se for usuario, = 'u', senï¿½o = false.
*/
function ListaCompletaDestinatarios($sock,$cod_msg, $lista_frases, $cod_curso){

  $query="select categ_destino,cod_destino from Correio_lista_destinos where cod_msg=".$cod_msg." order by categ_destino";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0){
    unset ($retorno);
    $i=0;
    foreach ($lista as $cod => $linha){
      if ($lista[$i]['categ_destino'] == 'T'){
        /* 29 - Todos */
        $retorno[$i]['nome']   = RetornaFraseDaLista($lista_frases,29);
        $retorno[$i]['codigo'] = -1;
        $retorno[$i]['status'] = false;
      }
      else if ($lista[$i]['categ_destino'] == 'A'){
        /* 31 - Todos os alunos */
        $retorno[$i]['nome']   = RetornaFraseDaLista($lista_frases,31);
        $retorno[$i]['codigo'] = -1;
        $retorno[$i]['status'] = false;
      }
      else if ($lista[$i]['categ_destino'] == 'F'){
        /* 30 - Todos os formadores */
        $retorno[$i]['nome']   = RetornaFraseDaLista($lista_frases,30);
        $retorno[$i]['codigo'] = -1;
        $retorno[$i]['status'] = false;
      }
      else if ($lista[$i]['categ_destino'] == 'G'){
        // 32 - Todos os grupos
        $retorno [$i]['nome']   = RetornaFraseDaLista($lista_frases, 32);
        $retorno [$i]['codigo'] = -1;
        $retorno[$i]['status']  = false;
      }
      else if ('Z' == $lista[$i]['categ_destino']){
        // 117 - Todos os colaboradores
        $retorno[$i]['nome']   = RetornaFraseDaLista($lista_frases, 117);
        $retorno[$i]['codigo'] = -1;
        $retorno[$i]['status'] = false;
      }
      else if ($lista[$i]['categ_destino'] == 'g'){
        /* nome daquele grupo */
        $retorno[$i]['nome']   = RetornaGrupoComCodigo($sock,$linha['cod_destino']);
        $retorno[$i]['codigo'] = $linha ['cod_destino'];
        $retorno[$i]['status'] = 'g';
      }
      else if ($lista[$i]['categ_destino'] == 'U'){
        /* nome daquele usuario */

        $retorno[$i]['nome']   = RetornaNomeUsuarioDeCodigo($sock, $linha['cod_destino'], $cod_curso);
        $retorno[$i]['codigo'] = $linha['cod_destino'];
        $retorno[$i]['status'] = 'u';
      }
      else{
        echo("<font class=\"text\" color=\"red\">Em correio.inc, fun&ccedil;&atilde;o ListaCompletaDestinatarios, valor fora do esperado</font>\n");
        die ();
      }
      $i++;
    }
    return $retorno;
  }
}

/* *******************************************************************
  ApagaMsgCM - altera para 'A' o estado de uma msg na tabela Correio_mensagens
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem da qual alterar o estado
  saida:  nenhuma
*/
function ApagaMsgCM($sock,$cod_msg){
  $query="update Correio_mensagens set estado='A' where cod_msg=".$cod_msg;
  $res=Enviar($sock,$query);
}

/* *******************************************************************
  ApagaMsgCD - altera para 'A' o estado de uma msg na tabela Correio_destinos
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem da qual alterar o estado
           $cod_usuario - codigo do usuario destinatario da mensagem a apagar
  saida:   nenhuma
*/
function ApagaMsgCD($sock,$cod_msg,$cod_usuario){
  $query="update Correio_destinos set estado='A' where cod_msg=".$cod_msg." and cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
}

/* *******************************************************************
  ApagaDefCM - altera para 'X' o estado de uma msg na tabela Correio_mensagens
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem da qual alterar o estado
  saida:   nenhuma
*/
function ApagaDefCM($sock,$cod_msg){
  $query="update Correio_mensagens set estado='X' where cod_msg=".$cod_msg." and estado='A'";
  $res=Enviar($sock,$query);
}

/* *******************************************************************
  ApagaDefCD - altera para 'X' o estado de uma msg na tabela Correio_destinos
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem da qual alterar o estado
           $cod_usuario - codigo do usuario destinatario da mensagem
  saida:   nenhuma
*/
function ApagaDefCD($sock,$cod_msg,$cod_usuario){
  $query="update Correio_destinos set estado='X' where cod_msg=".$cod_msg." and cod_usuario=".$cod_usuario." and estado='A'";
  $res=Enviar($sock,$query);
}

/* *******************************************************************
  RetornaEstadoAbrev - retorna letra do estado para o destinatario
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem
           $cod_usuario - codigo do usuario destinatario
  saida:   letra de estado da mensagem para o destinatario
*/
function RetornaEstadoAbrev($sock,$cod_msg,$cod_usuario){
  $query="select estado from Correio_destinos where cod_msg=".$cod_msg." and cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha;
}

/* *******************************************************************
  AtualizaEstadoMsgCD - Atualiza o estado da msg na base Correio_destinos
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem
           $cod_usuario - codigo do usuario destinatario da mensagem
           $novo_estado - novo estado da mensagem
  saida:   nenhuma
*/
function AtualizaEstadoMsgCD($sock,$cod_msg,$cod_usuario,$novo_estado){
  $query="update Correio_destinos set estado='".$novo_estado."' where cod_msg=".$cod_msg." and cod_usuario = ".$cod_usuario;
  $res=Enviar($sock,$query);
}

/* *******************************************************************
  RetornaNumDestinatariosMsg - retorna o numero de destinatarios da msg dada
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem
  saida:   numero de destinatarios daquela msg
*/
function RetornaNumDestinatariosMsg($sock,$cod_msg){
  $query="select count(*) from Correio_lista_destinos where cod_msg=".$cod_msg;
  $res=Enviar($sock,$query);
  $num=RetornaLinha($res);
  return $num;
}

/* *******************************************************************
  RetornaCategDestinoCodUsuarioMsg - Consulta Correio_lista_destinos e retorna lista com
                                     categorias destinatarias da mensagem e seus codigos
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem
  saida: array bidimensional com
         []['categ_destino'] - categoria destinaria da mensagem
         []['cod_destino'] - codigo da categoria destinataria
*/
function RetornaCategDestinoCodUsuarioMsg($sock,$cod_msg){
  $query="select categ_destino,cod_destino from Correio_lista_destinos where cod_msg=".$cod_msg;
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}

/* **********************************************************************
  EndentarMensagem - Faz a identaï¿½ï¿½o de mensagens redirecionadas ou respondidas.
                     Retorna uma string com a mensagem passada identada e com cabeï¿½alho
                     contendo autor e data
  entrada: $mensagem - mensagem a formatar
           $data - data no formato extenso (string)
           $nome_autor - nome do autor da mensagem
  saida:?  nenhuma
*/
function EndentarMensagem($mensagem,$data,$nome_autor){
  $traco="_________________________________________________________\n";
  $mensagem="\n".$traco."\nEm ".$data.", ".$nome_autor." havia escrito:\n\n$mensagem\n\n".$traco;
  $mensagem="\n\n".str_replace("\n","\n> ",$mensagem);
  return($mensagem);
}

/* **********************************************************************
  UsuarioEhDestinatarioDaMensagem - identifica se usuario eh destinatario da mensagem
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem a identificar
           $cod_usuario - codigo do usuario
  saida: true - usuario eh destinatario da mensagem
         false - usuario nao eh destinatario da mensagem
*/
function UsuarioEhDestinatarioDaMensagem($sock,$cod_msg,$cod_usuario){
  $query="select cod_usuario from Correio_destinos where cod_msg=".$cod_msg." and cod_usuario=".$cod_usuario." and estado != 'X'";
  $res=Enviar($sock,$query);
  $num=RetornaNumLinhas($res);

  if ($num > 0)
    return true;
  else
    return false;
}

/* **********************************************************************
  UsuarioEhDestinatarioDaMensagemApagada - identifica se usuario eh destinatario de mensagem apagada
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem a identificar
           $cod_usuario - codigo do usuario
  saida: true - usuario eh destinatario da mensagem apagada
         false - caso contrario
*/
function UsuarioEhDestinatarioDaMensagemApagada($sock,$cod_msg,$cod_usuario){
  $query="select cod_usuario from Correio_destinos where cod_msg=".$cod_msg." and cod_usuario=".$cod_usuario." and estado = 'A'";
  $res=Enviar($sock,$query);
  $num=RetornaNumLinhas($res);

  if ($num > 0)
    return true;
  else
    return false;
}

/* **********************************************************************
  RecuperaCM - recupera mensagem na tabela Correio_mensagens
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem a recuperar
  saida: nenhuma
*/
function RecuperaCM($sock,$cod_msg){
  $query="update Correio_mensagens set estado='R' where cod_msg=".$cod_msg." and estado='A'";
  $res=Enviar($sock,$query);
}

/* **********************************************************************
  RecuperaCD - recupera mensagem na tabela Correio_destinos
  entrada: $sock - sock de conexao
           $cod_msg - codigo da mensagem
           $cod_usuario - codigo do usuario
  saida: nenhuma
*/
function RecuperaCD($sock,$cod_msg,$cod_usuario){
  $query="update Correio_destinos set estado='U' where cod_msg=".$cod_msg." and cod_usuario=".$cod_usuario." and estado='A'";
  $res=Enviar($sock,$query);
}


/* ***********************************************************************************
  RetornaDiretorio - Retorna Diretorio (path) do item pedido
  Entrada: $sock - sock de conexao (BASE EXTERNA)
           $item - arquivo do qual se quer descobrir o diretorio
  Saida:   path do item
*/
function RetornaDiretorio($sock,$item){
  $query="select diretorio from Diretorio where item='".$item."'";
  $res=Enviar($sock,$query); // Base externa!!!!
  $linha=RetornaLinha($res);
  return ($linha[0]);
}

/* **********************************************************************
   RetornaCodigoColaboradoresDoCurso - retorna codigo dos colaboradores
                                       do curso
   entrada: $sock - sock de conexao com a base
   saida:   array com os cod_usuario dos colaboradores do curso
*/
function RetornaCodigoColaboradoresDoCurso ($sock, $cod_curso){

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query  = "select U.nome, UC.cod_usuario ";
  $query .= "from ".$dbnamebase.".Usuario U ";
  $query .= "inner join ".$dbnamebase.".Usuario_curso UC ON (UC.cod_usuario_global=U.cod_usuario) ";
  $query .= "where UC.cod_curso=".$cod_curso." AND binary UC.tipo_usuario = 'Z' order by U.nome asc";

  $res   = Enviar ($sock, $query);
  $lista = RetornaArrayLinhas ($res);
  return $lista;
}

/* *********************************************************************
   StatusFerramentaGrupos - retorna se a ferramenta Grupos esta ativada

   PROVISORIO !!! - O LUGAR DESTA FUNCAO NAO EH AQUI

   entrada: $sock        - sock de conexao com a base de dados
            $cod_curso   - codigo do curso
            $cod_usuario - codigo do usuario logado		
   saida  : true  - ferramenta grupos estah ativada e visivel ao usuario 
            false - ferramenta grupos nao estah ativa, ou nao eh visivel ao usuario
   OBS: Esta funcao eh diferente da funcao em portfolio.inc.
        Lï¿½, cod_curso e cod_usuario sï¿½o variaveis globais
        Aqui, sao parametros
*/
function StatusFerramentaGrupos ($sock, $cod_curso, $cod_usuario){
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

/* *********************************************************************
   ExpulsaVisitante - verifica se o usuario eh visitante e impede seu
                      acesso se for
   entrada: $sock        - sock de conexao
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

    /* 1 - Correio 504 - Área restrita a alunos e formadores */
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

/* *********************************************************************
   DestinatarioMensagem - verifica se o usuario eh destinatario da mensagem
                             para permitir ou nao a leitura desta
                             Barra o usuario caso ele nao seja destinatario da mensagem
   entrada: $sock - sock de conexao
            $cod_msg - codigo da msg a exibir
            $cod_usuario - codigo do usuario que tenta ler a mensagem
   saida:   true - o usuario eh destinatario da msg
            false - caso contrario
*/
function DestinatarioMensagem($sock, $cod_msg, $cod_usuario){
  $query = "select cod_msg from Correio_destinos where cod_msg=".$cod_msg." and cod_usuario=".$cod_usuario;
  $res   = Enviar ($sock, $query);
  return (RetornaNumLinhas ($res) > 0);
}

/* *********************************************************************
   RemetenteMensagem - verifica se o usuario eh remetente da mensagem
                             para permitir ou nao a leitura desta
                             Barra o usuario caso ele nao seja destinatario da mensagem
   entrada: $sock - sock de conexao
            $cod_msg - codigo da msg a exibir
            $cod_usuario - codigo do usuario que tenta ler a mensagem
   saida:   true - o usuario eh remetente da msg
            false - caso contrario
*/
function RemetenteMensagem($sock, $cod_msg, $cod_usuario){
  $query = "select cod_msg from Correio_mensagens where cod_msg=".$cod_msg." and cod_usuario=".$cod_usuario;
  $res   = Enviar ($sock, $query);
  return(RetornaNumLinhas ($res) > 0);
}

function InsereMsgCorreioDestinos($sock, $codMsg, $destsCorreio, $cod_usuario){
  foreach($destsCorreio as $infs){
    $query="insert into Correio_destinos values(".$codMsg.",".$infs['cod_usuario'].", 'N')";
    $res=Enviar($sock,$query);
    
    $query="insert into Correio_intermap values(".$codMsg.",".$cod_usuario.",".$infs['cod_usuario'].",".time().")";
    $res=Enviar($sock,$query);
  }
}


function InsereMsgCorreioListaDestinos($sock, $codMsg, $destsCorreio, $indexDestCorreio=0, $cod_usuario, $listaCodCateg, $cod_curso){

  foreach($listaCodCateg as $infs){
    
    $query="insert into Correio_lista_destinos values (".$codMsg.",".$cod_usuario.",".$infs['codDestino'].", '".$infs['categDestino']."')";
    $res=Enviar($sock,$query);

    if($infs['categDestino'] == 'g'){
      $aux = 0;
      $usuariosGrupo = RetornaUsuariosGrupo($sock, $infs['codDestino'], $cod_curso);
      $nomeIntegrantes = $usuariosGrupo['nome'];
      $emailIntegrantes = $usuariosGrupo['email'];
  
      foreach($usuariosGrupo['cod_usuario'] as $codUser){
        if(!(UsuarioRepetido($destsCorreio, $codUser, 'cod_usuario')) ){
          $destsCorreio[$indexDestCorreio]['nome'] = $nomeIntegrantes[$aux];
          $destsCorreio[$indexDestCorreio]['email'] = $emailIntegrantes[$aux];
          $destsCorreio[$indexDestCorreio]['cod_usuario'] = $codUser;
          $indexDestCorreio++;
        }
        $aux++;
      }
    }
  }
  InsereMsgCorreioDestinos($sock, $codMsg, $destsCorreio, $cod_usuario);
}


function UsuarioRepetido($listaDestinatarios, $codUser, $cod){
  foreach($listaDestinatarios as $destinatario){
    if($destinatario[$cod] == $codUser) return true;
  }
  return false;
}

/* ************************************************************************
 * AnexarArquivos - Copia os arquivos anexos para o diretorio $dir_arq
 * Entrada: $dir_curso: [dir_arq]/[cod_curso]/
 * 			$dir_arq: [dir_arq]/[cod_curso]/correio/[cod_msg]/
 * 			$arquivosAnexos: array com os arquivos novos a serem anexados (via input)
 * 			$chkArqAnexo: array com todos arquivos anexos redirecionados 
 * 				(com o caminho completo ate ele)
 * */
function AnexarArquivos($dir_curso, $dir_arq, $arquivosAnexos, $chkArqAnexo){
  if (!ExisteArquivo($dir_curso))
    CriaDiretorio($dir_curso);
  if (!ExisteArquivo($dir_curso."/correio"))
    CriaDiretorio($dir_curso."/correio");

  if (!ExisteArquivo($dir_arq)){
    /* Se o diretorio nao existe, cria-se */
    CriaDiretorio($dir_arq);
  }

  /* Caso tenha arquivo anexo redirecionado (via checkbox chkArqAnexo)
   * faz uma copia do arquivo para a pasta da nova mensagem  */
  if($chkArqAnexo > 0)
    for($contArq = 0; $contArq < count($chkArqAnexo); $contArq++)
      CopiaArquivo($chkArqAnexo[$contArq], $dir_arq);

  /* Novos arquivos anexados (via input) */
  if(count($arquivosAnexos['name']) > 0){
    for($contArq = 0; $contArq < count($arquivosAnexos['name']); $contArq++){
      $arquivosAnexos['name'][$contArq] = mb_convert_encoding($arquivosAnexos['name'][$contArq], "UTF-8", "ISO-8859-1");
      $destino = $dir_arq."/".$arquivosAnexos['name'][$contArq];
      $num = 0;
      $destinoAux = $destino;
      $flag=true;
      while($flag){
        if(ExisteArquivo($destinoAux)){
          $num++;
          $destinoAux = explode("/", $destino);
          $tamCamArq = count($destinoAux);

          $nomeArq = $destinoAux[$tamCamArq-1];

          $nomeArq = explode(".", $nomeArq);
          $nomeArq[0] .= "(".$num.")";
          $nomeArq = implode(".", $nomeArq);
          $destinoAux[$tamCamArq-1] = $nomeArq;

          $destinoAux=implode("/",$destinoAux);

          $flag= true;
        }else{
          $destino = $destinoAux;
          $flag=false;
        }
      }
      move_uploaded_file($arquivosAnexos['tmp_name'][$contArq], $destino);
      chmod($dir_arq."/".$arquivosAnexos['name'][$contArq],0644);
    }
  }

}

function UpgradeUsuarioRepetido($listaDestinatarios, $codUser, $cod_msg, $cod){
  foreach($listaDestinatarios as $destinatario){
    if( ($destinatario[$cod] == $codUser) && ($destinatario['cod_msg'] == $cod_msg))  return true;
  }
  return false;
}

function trocaEstadoMsg($cod_usuario, $cod_curso, $modoVisualizacao, $msg, $estado, $texto='', $reload='', $recuperar=''){
    $objResponse = new xajaxResponse();
    $sock = Conectar($cod_curso);

    if(is_array($msg)){
      foreach($msg as $cod_msg){
        if($modoVisualizacao == 'R'){
          TrocaEstadoDaMensagemRecebidas($sock, $estado, $cod_msg, $cod_usuario);
          $objResponse->assign("img_".$cod_msg, "src", "../imgs/icMensagemLida.gif");
          $objResponse->script("document.getElementById('assunto_".$cod_msg."').style.fontWeight='';");
          $objResponse->script("document.getElementById('remetente_".$cod_msg."').style.fontWeight='';");
          $objResponse->script("document.getElementById('data_".$cod_msg."').style.fontWeight='';");
        }else if($modoVisualizacao == 'E'){
          TrocaEstadoDaMensagemEnviadas($sock, $estado, $cod_msg, $cod_usuario);
        }else if($modoVisualizacao == 'L'){
          TrocaEstadoDaMensagemLixeira($sock, $estado, $cod_msg, $cod_usuario, $recuperar);
        }
      }
    }else{
      if($modoVisualizacao == 'R'){
        TrocaEstadoDaMensagemRecebidas($sock, $estado, $msg, $cod_usuario);
        $objResponse->assign("img_".$msg, "src", "../imgs/icMensagemLida.gif");
        $objResponse->script("document.getElementById('assunto_".$msg."').style.fontWeight='';");
        $objResponse->script("document.getElementById('remetente_".$msg."').style.fontWeight='';");
        $objResponse->script("document.getElementById('data_".$msg."').style.fontWeight='';");
      }else if($modoVisualizacao == 'E'){
        TrocaEstadoDaMensagemEnviadas($sock, $estado, $msg, $cod_usuario);
      }else if($modoVisualizacao == 'L'){
        TrocaEstadoDaMensagemLixeira($sock, $estado, $msg, $cod_usuario, $recuperar);
      }
    }

//     if($reload == 'yes'){$objResponse->script("window.location.reload();");}
       if($reload == 'yes'){$objResponse->script("window.location='correio.php?cod_curso=".$cod_curso."&modoVisualizacao=".$modoVisualizacao."&acao=atualizarPag&atualizacao=true&rec=".$recuperar."'");}


//     if($modoVisualizacao != 'R'){
	 if($texto!='')
       $objResponse->call("mostraFeedback", $texto, 'true');
//     }

    Desconectar($sock);
    return $objResponse;
  }

  function VerificaMsgNova($cod_curso, $cod_usuario, $clock){
    $objResponse = new xajaxResponse();
    $sock = Conectar($cod_curso);
    //divide por mil pois no JS eh em milisegundos e no BD em segundos
    $query = "select * from Correio_mensagens CM left join Correio_destinos CD on CD.cod_msg = CM.cod_msg where (CM.data > ".($clock/1000).") and CD.cod_usuario = ".$cod_usuario;
    $res = Enviar($sock, $query);

    if(RetornaNumLinhas ($res) > 0){
      $objResponse->script("window.location.reload();");
    }
    return $objResponse;
  }

  function PreparaMensagemExibicao($msg){
    $msg_temp = preg_replace("/\r\n/", "", $msg);
    return(Enter2Br($msg_temp));
  }

?>
