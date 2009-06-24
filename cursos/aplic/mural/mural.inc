<?

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/mural/mural.inc

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
  ARQUIVO : cursos/aplic/mural/mural.inc
  ========================================================== */


/********************************************************************
  Funçao VerificaStatus - Retorna o status da mensagem
  Entrada: $sock - sock de conexao
           $cod_mural - codigo do mural
  Saida: ['status'] - status da mensagem: A - Incluido
                                          X - Deletado 
********************************************************************/
function VerificaStatus($sock, $cod_mural)
{
  $query="select status from Mural where cod_mural = ".VerificaNumeroQuery($cod_mural);
  $res=Enviar($sock, $query);
  $tuplas=RetornaLinha($res);
  return($tuplas['status']);
}


/********************************************************************
  Funçao ConteudoMensagem - Retorna o conteudo da mensagem
  Retorna uma lista com o cod_usuario, titulo e conteudo da
    mensagem, e data de postagem.
  Entradas: $sock - sock de conexao,
            $cod_mural - codigo do mural.
  Saida:   Array com: ['cod_usuario'] - codigo do usuario,
                      ['titulo'] - titulo da mensagem,
                      ['texto'] - texto da mensagem,
                      ['data'] - data de postagem.
                      ['status'] - status da mensagem: A - Incluido
                                                       X - Deletado
********************************************************************/
function ConteudoMensagem($sock, $cod_mural)
{
  $query="select cod_usuario,titulo,texto,data,status from Mural where cod_mural = ".VerificaNumeroQuery($cod_mural);
  $res=Enviar($sock, $query);

  $tuplas=RetornaLinha($res);
  return($tuplas);
}


/*********************************************************************
 Funcao MostraMensagemDinamicMural - atualiza e mostra mensagem dinamicamente no mural
 Entrada: $cod_curso - codigo do curso 
          $cod_mural - codigo do mural
          $mensagem - conteudo da mensagem
Saida: XML da funçao ajax
*********************************************************************/
function MostraMensagemDinamicMural($cod_curso, $cod_mural, $mensagem)
{
  $objResponse = new xajaxResponse('ISO-8859-1');
  $sock=Conectar($cod_curso);
  
  $pega_linha = ConteudoMensagem($sock, $cod_mural);

  $objResponse->addAssign("td_msg_".$cod_mural, "innerHTML", "<b>".$mensagem.":</b><br /><br />". Enter2BR(LimpaConteudo($pega_linha['texto'])) );
//   
  Desconectar($sock);
  //  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}


/********************************************************************
  Funçao ListaMensagens - Retorna uma lista contendo todas as
    mensagens do mural ativas.
  Retorna uma lista com o titulo da mensagem, codigo do usuario
    e data de postagem.
  Entradas: $sock - sock de conexao.
            $ordem - ordem de organização
  Saida:   Array com: ['cod_mural'] - codigo da mensagem,
                      ['cod_usuario'] - codigo do usuario,
                      ['titulo'] - titulo da mensagem,
                      ['data'] - data de postagem,
                      ['status'] - status da mensagem: A - Incluido
                                                       X - Deletado
********************************************************************/
function ListaMensagens($sock, $ordem, $cod_curso){
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  if(!(($ordem == 'data') || ($ordem =='nome') || ($ordem =='titulo')))
    $ordem = 'data';
  if(!($ordem == 'data')){
    $ordem .= ', data';
    $sequencia = "asc";
  }else
    $sequencia = "desc";

  $query="select M.cod_mural,M.cod_usuario,M.titulo,M.data,M.status, M.texto, U.nome, M.cod_usuario from ".$dbnamebase.".Usuario U, Mural M left join ".$dbnamebase.".Usuario_curso UC on M.cod_usuario = UC.cod_usuario where UC.cod_curso = ".$cod_curso." and U.cod_usuario = UC.cod_usuario_global and M.status='A' order by ".$ordem." ".$sequencia;
    $res=Enviar($sock, $query);

  $tuplas=RetornaArrayLinhas($res);
  return($tuplas);
}


/*******************************************************
  Funçao NomeDoUsuario - Retorna o nome do usuario
  Retorna uma string com o nome do usuario.
  Entradas: $sock - sock de conexao,
            $cod_usuario - codigo do usuario.
  Saida:   string com nome do usuario
******************************************************/
function NomeDoUsuario($sock, $cod_usuario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  if ($cod_usuario==-2)
  {
    $tuplas="Visitante";
  }
  else if ($cod_usuario==-1)
  {
    $tuplas="Administra&ccedil;&atilde;o do TelEduc";
  }
  else
  {
    $query="select nome from ".$dbnamebase.".Usuario where cod_usuario = ".VerificaNumeroQuery($cod_usuario);
    $res=Enviar($sock, $query);

    $linhas=RetornaLinha($res);
    $tuplas=$linhas['nome'];
  }
  return($tuplas);
}


/*******************************************************
  Funçao EliminaEspacos - Elimina espacos no começo e no
    final da frase.
  Retorna a frase sem os espacos no começo e no final dela.
  Entradas: $frase - frase
  Saida:   frase sem os espacos no começo e no final dela.
******************************************************/
function EliminaEspacos($frase)
{
  return(trim($frase));
}


/********************************************************************
  Funçao SalvaMensagemMural - Salva mensagem
  Se o convidado for ativo, retorna true se a operaçao for bem sucedida ou false
    se ocorrer um erro.
  Entradas: $sock - sock de conexao,
            $cod_usuario - codigo do usuario,
            $titulo - titulo da mensagem,
            $mensagem - conteudo da mensagem.
  Saida:   true se bem-sucedido ou false se falhar.
******************************************************/  
function SalvaMensagemMural($sock, $cod_curso, $cod_usuario, $titulo, $mensagem, $status_curso)
{
  if (!EConvidadoPassivo($sock, $cod_usuario, $cod_curso)){
    if (!($status_curso=='E' && !EFormador($sock,$cod_curso,$cod_usuario))){
      $titulo = EliminaScript($titulo);
      $titulo = LimpaTitulo($titulo);
      $titulo = EliminaEspacos($titulo);
    
      $mensagem = EliminaScript($mensagem);
      $mensagem = LimpaConteudo($mensagem);
    
      $status = 'A';
    
      $query = "insert into Mural (cod_usuario, data, titulo, texto, status) values (";
      $query .= VerificaNumeroQuery($cod_usuario).", ".time()." , '".VerificaStringQuery($titulo)."', '".VerificaStringQuery($mensagem)."', '".VerificaStringQuery($status)."')";
      $res = Enviar($sock, $query);
      
      return($res);
    }
  }

return(false);
}


/********************************************************************
  Funçao ApagaMensagem - Atribue a flag de X (Deletado) ao campo
    status da mensagem.
  Retorna true se a operaçao for bem sucedida ou false
    se ocorrer um erro.
  Entradas: $sock - sock de conexao,
            $cod_mural - codigo do mural.
  Saida:   true se bem-sucedido ou false se falhar.
********************************************************************/
function ApagaMensagem($sock, $cod_mural)
{
  $query = "update Mural set status = 'X' where cod_mural = ".VerificaNumeroQuery($cod_mural);
  $res=Enviar($sock, $query);

  if (!$res)
    return false;
  else
    return true;
}

function PreparaExibicaoMensagem($mensagem){
  return VerificaTagTable(ConverteBarraAspas2Aspas(LimpaConteudo($mensagem)));
}
?>