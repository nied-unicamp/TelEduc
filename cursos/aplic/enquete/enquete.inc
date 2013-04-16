<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/enquete/enquete.inc

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
  ARQUIVO : cursos/aplic/enquete/enquete.inc
  ========================================================== */

/* ***************************************************************
	AtualizaFerramentaEnquete - atualiza a tabela Ferramentas_nova
							para haver notifica��es da Enquete via
							Email.
	Entrada: $sock - sock da conexao
			 $idEnquete - para buscar no banco informacoes de
			  aplicacao / resultado da enquete
			 $opcao - 'INICIO' - para nova Enquete
					  'FIM' - para o encerramento da enquete
	Sa�da: Atualizacao da tabela Ferramentas_nova
 */
function AtualizaFerramentaEnquete($sock, $idEnquete, $opcao)
{
	$cod_ferramenta = 24;
	$query_nova = "select aplicacao from Enquete where idEnquete = ".$idEnquete;
	$query_fim = "select resultado from Enquete where idEnquete = ".$idEnquete;
	
	if ($opcao == 'INICIO')
	{
		// Atualiza a tabela apenas para os usuarios que podem ver/votar na
		// Enquete, setado na coluna 'aplicacao' da tabela Enquete
		$res = Enviar($sock, $query_nova);
		$linha =  RetornaLinha($res);
		$size = strlen($linha['aplicacao']);
		
		for ($i = 0; $i < $size; $i++)
		{
			if (strcmp($linha['aplicacao'][$i], "-") != 0)
				AtualizaFerramentasNova($sock, $cod_ferramenta, $linha['aplicacao'][$i]);	
		}
	}
  	else if ($opcao == 'FIM')
  	{
  		// Atualiza a tabela apenas para os usuarios que podem ver o resultado
  		// da Enquete, setado na coluna 'resultado' da tabela Enquete
  		$res = Enviar($sock, $query_fim);
		$linha =  RetornaLinha($res);
		$size = strlen($linha['resultado']);
		
  		for ($i = 0; $i < $size; $i++)
  		{
  			if (strcmp($linha['resultado'][$i], "-") != 0)
  				AtualizaFerramentasNova($sock, $cod_ferramenta, $linha['resultado'][$i]);
  		}
  	}
}


/* *******************************************************************
   getEnquetesAndamento - retorna lista com as enquetes em andamento para
                          determinado usu�io, passado como par�etro
   entrada: $sock - sock de conexao
            $papel - codigo do papel (RFAVC)
   saida:   array bidimensional, sendo o segundo array com:
            ['idEnquete'] - id da enquete
            ['cod_usuario'] - usu�io criador da enquete
            ['titulo'] - titulo da enquete
            ['pergunta '] - pergunta da enquete
            ['data '] - data de cria�o da enquete
            ['data_inicio '] - data de in�io da consulta
            ['data_fim '] - data de fim da consulta
            ['aplicacao '] - especifica�o de quais p��s votar� na enquete (RFAVC)
            ['resultado'] - especifica�o de quais p��s ver� o resultado da enquete (RFAVC)
            ['resultado_parcial '] - 'S' se possuir resultado parcial, 'N' caso contr�io
            ['identidade_votos '] - 'S' se a identidade dos votos ser�mostrada, 'N' caso contr�io
            ['num_escolhas '] - '1' se somente uma resposta pode ser escolhida, 'N' caso v�ias possam
*/
function getEnquetesAndamento($sock, $papel)
{
  $sigla = VerificaStringQuery($papel);
  $data = time();
  $consulta="select * from Enquete where status = 'A' AND ((aplicacao LIKE '%".$sigla."%') OR (resultado LIKE '%".$sigla."%')) order by data_inicio DESC";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);

}


/* *******************************************************************
   getEnquetesEncerradas - retorna lista com as enquetes encerradas para
                          determinado usu�io, passado como par�etro
   entrada: $sock - sock de conexao
            $papel - codigo do papel (RFAVC)
   saida:   array bidimensional, sendo o segundo array com:
            ['idEnquete'] - id da enquete
            ['cod_usuario'] - usu�io criador da enquete
            ['titulo'] - titulo da enquete
            ['pergunta '] - pergunta da enquete
            ['data '] - data de cria�o da enquete
            ['data_inicio '] - data de in�io da consulta
            ['data_fim '] - data de fim da consulta
            ['aplicacao '] - especifica�o de quais p��s votar� na enquete (RFAVC)
            ['resultado'] - especifica�o de quais p��s ver� o resultado da enquete (RFAVC)
            ['resultado_parcial '] - 'S' se possuir resultado parcial, 'N' caso contr�io
            ['identidade_votos '] - 'S' se a identidade dos votos ser�mostrada, 'N' caso contr�io
            ['num_escolhas '] - '1' se somente uma resposta pode ser escolhida, 'N' caso v�ias possam
*/
function getEnquetesEncerradas($sock, $papel)
{
 
  $sigla = VerificaStringQuery($papel);
  $data = time();
  $consulta="select * from Enquete where status = 'F' AND ((aplicacao LIKE '%$sigla%') OR (resultado LIKE '%$sigla%')) order by data_inicio DESC";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);

}


/* *******************************************************************
   getEnquete - retorna array com informa�es da enquete cujo id �idEnquete
   entrada: $sock - sock de conexao
            $idEnquete - id da enquete
   saida:   array com
            ['idEnquete'] - id da enquete
            ['cod_usuario'] - usu�io criador da enquete
            ['titulo'] - titulo da enquete
            ['pergunta '] - pergunta da enquete
            ['data '] - data de cria�o da enquete
            ['data_inicio '] - data de in�io da consulta
            ['data_fim '] - data de fim da consulta
            ['aplicacao '] - especifica�o de quais p��s votar� na enquete (RFAVC)
            ['resultado'] - especifica�o de quais p��s ver� o resultado da enquete (RFAVC)
            ['resultado_parcial '] - 'S' se possuir resultado parcial, 'N' caso contr�io
            ['identidade_votos '] - 'S' se a identidade dos votos ser�mostrada, 'N' caso contr�io
            ['num_escolhas '] - '1' se somente uma resposta pode ser escolhida, 'N' caso v�ias possam
*/
function getEnquete($sock, $idEnquete)
{
  $consulta="select * from Enquete where idEnquete='".VerificaStringQuery($idEnquete)."'";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista[0]);
}
  

/* *******************************************************************
   getAlternativas - retorna todas as alternativas de uma dada enquete
   entrada: $sock - sock de conexao
            $idEnquete - id da enquete
   saida:   array com as alternativas
            ['idAlternativa'] - id da alternativa
            ['idEnquete'] - id da enquete ao qual a alternativa pertence
            ['texto '] - texto da alternativa
            ['votos '] - numero ve votos na alternativa
*/
function getAlternativas($sock, $idEnquete)
{
  $consulta="select * from Enquete_alternativa where idEnquete=".VerificaNumeroQuery($idEnquete)." ORDER BY idAlternativa";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}


/*==========================================================
  ========================================================== */
  
  
/* *******************************************************************
   getTodasEnquetesNaoAplicadas - retorna lista de todas as enquetes n� aplicadas
                                  (para formadores)
   entrada: $sock - sock de conexao
   saida:   array bidimensional, sendo o segundo array com:
            ['idEnquete'] - id da enquete
            ['cod_usuario'] - usu�io criador da enquete
            ['titulo'] - titulo da enquete
            ['pergunta '] - pergunta da enquete
            ['data '] - data de cria�o da enquete
            ['data_inicio '] - data de in�io da consulta
            ['data_fim '] - data de fim da consulta
            ['aplicacao '] - especifica�o de quais p��s votar� na enquete (RFAVC)
            ['resultado'] - especifica�o de quais p��s ver� o resultado da enquete (RFAVC)
            ['resultado_parcial '] - 'S' se possuir resultado parcial, 'N' caso contr�io
            ['identidade_votos '] - 'S' se a identidade dos votos ser�mostrada, 'N' caso contr�io
            ['num_escolhas '] - '1' se somente uma resposta pode ser escolhida, 'N' caso v�ias possam
*/  
function getTodasEnquetesNaoAplicadas($sock)
{
  $data = time();
  $consulta="select * from Enquete WHERE status = 'I' ORDER BY data_inicio DESC";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}


/* *******************************************************************
   getTodasEnquetesEmAndamento - retorna lista de todas as enquetes em andamento
                                  (para formadores)
   entrada: $sock - sock de conexao
   saida:   array bidimensional, sendo o segundo array com:
            ['idEnquete'] - id da enquete
            ['cod_usuario'] - usu�io criador da enquete
            ['titulo'] - titulo da enquete
            ['pergunta '] - pergunta da enquete
            ['data '] - data de cria�o da enquete
            ['data_inicio '] - data de in�io da consulta
            ['data_fim '] - data de fim da consulta
            ['aplicacao '] - especifica�o de quais p��s votar� na enquete (RFAVC)
            ['resultado'] - especifica�o de quais p��s ver� o resultado da enquete (RFAVC)
            ['resultado_parcial '] - 'S' se possuir resultado parcial, 'N' caso contr�io
            ['identidade_votos '] - 'S' se a identidade dos votos ser�mostrada, 'N' caso contr�io
            ['num_escolhas '] - '1' se somente uma resposta pode ser escolhida, 'N' caso v�ias possam
*/  
function getTodasEnquetesEmAndamento($sock, $cod_curso)
{
  $data = time();
  $consulta="select * from Enquete WHERE status = 'A'  ORDER BY data_inicio DESC";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);

  foreach($lista as $cod => $linha){
    $linha['votos'] = getParticipantesEnquete($sock,$linha['idEnquete'], $cod_curso);
  }
  return($lista);
}

/* *******************************************************************
   getTodasEnquetesEncerradas - retorna lista de todas as enquetes encerradas
                                  (para formadores)
   entrada: $sock - sock de conexao
   saida:   array bidimensional, sendo o segundo array com:
            ['idEnquete'] - id da enquete
            ['cod_usuario'] - usu�io criador da enquete
            ['titulo'] - titulo da enquete
            ['pergunta '] - pergunta da enquete
            ['data '] - data de cria�o da enquete
            ['data_inicio '] - data de in�io da consulta
            ['data_fim '] - data de fim da consulta
            ['aplicacao '] - especifica�o de quais p��s votar� na enquete (RFAVC)
            ['resultado'] - especifica�o de quais p��s ver� o resultado da enquete (RFAVC)
            ['resultado_parcial '] - 'S' se possuir resultado parcial, 'N' caso contr�io
            ['identidade_votos '] - 'S' se a identidade dos votos ser�mostrada, 'N' caso contr�io
            ['num_escolhas '] - '1' se somente uma resposta pode ser escolhida, 'N' caso v�ias possam
*/  
function getTodasEnquetesEncerradas($sock)
{
  $data = time();
  $consulta="select * from Enquete WHERE status = 'F' ORDER BY data_inicio DESC";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}  


/* *******************************************************************
   getTodasEnquetesLixeira - retorna lista de todas as enquetes na lixeira
                                  (para formadores)
   entrada: $sock - sock de conexao
   saida:   array bidimensional, sendo o segundo array com:
            ['idEnquete'] - id da enquete
            ['cod_usuario'] - usu�io criador da enquete
            ['titulo'] - titulo da enquete
            ['pergunta '] - pergunta da enquete
            ['data '] - data de cria�o da enquete
            ['data_inicio '] - data de in�io da consulta
            ['data_fim '] - data de fim da consulta
            ['aplicacao '] - especifica�o de quais p��s votar� na enquete (RFAVC)
            ['resultado'] - especifica�o de quais p��s ver� o resultado da enquete (RFAVC)
            ['resultado_parcial '] - 'S' se possuir resultado parcial, 'N' caso contr�io
            ['identidade_votos '] - 'S' se a identidade dos votos ser�mostrada, 'N' caso contr�io
            ['num_escolhas '] - '1' se somente uma resposta pode ser escolhida, 'N' caso v�ias possam
*/  
function getTodasEnquetesLixeira($sock)
{
  $data = time();
  $consulta="select * from Enquete WHERE status = 'L'";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}  

  

/*==========================================================
  ========================================================== */

/* *******************************************************************
   deleteEnquete - exclui permamentemente a enquete da lixeira
   entrada: $sock - sock de conexao
            $idEnquete - id da enquete
   saida:   resposta da consulta delete (true/false)
*/  
function deleteEnquete($sock, $idEnquete)
{
  //deleta enquete
  $consulta="delete from Enquete where idEnquete=".VerificaNumeroQuery($idEnquete);
  $res=Enviar($sock, $consulta);
  //pega alternativas
  $consulta="select idAlternativa from Enquete_alternativa where idEnquete=".VerificaNumeroQuery($idEnquete);
  $alternativas=Enviar($sock, $consulta);
  //deleta alternativas
  $consulta="delete from Enquete_alternativa where idEnquete=".VerificaNumeroQuery($idEnquete);
  $res2=Enviar($sock, $consulta);
  while($alternativa = RetornaLinha($alternativas)){  
    $consulta="delete from Enquete_voto where idAlternativa=".VerificaNumeroQuery($alternativa['idAlternativa']);
    $res3=Enviar($sock, $consulta);
  }
  return($res);
}

/* *******************************************************************
   apagarEnquete - manda a enquete para a lixeira
   entrada: $sock - sock de conexao
            $idEnquete - id da enquete
   saida:   resposta da consulta delete (true/false)
*/  
function apagarEnquete($sock, $idEnquete)
{
  $consulta="update Enquete set status = 'L' where idEnquete=".VerificaNumeroQuery($idEnquete);
  $res=Enviar($sock, $consulta);
  return($res);
}
 
/* *******************************************************************
   recuperarEnquete - recupera a mensagem da lixeira de volta ao seu local
   entrada: $sock - sock de conexao
            $idEnquete - id da enquete
   saida:   resposta da consulta delete (true/false)
*/ 
function recuperarEnquete($sock, $idEnquete)
{	
	$consulta = "select data_inicio, data_fim from Enquete where idEnquete = ".VerificaNumeroQuery($idEnquete);
	$res = Enviar($sock, $consulta);
	$linha = RetornaLinha($res);
	
	if ($linha['data_fim'] < time())
		$status = 'F';
	else if ($linha['data_inicio'] > time ())
		$status = 'I';
	else
		$status = 'A';
	
	$consulta="update Enquete set status = ".$status." where idEnquete=".VerificaNumeroQuery($idEnquete);
  	  		
  	// Atualiza para notifica��o Ferramentas_nova (caso 'A')
  	if ($status == 'A')
  		AtualizaFerramentaEnquete($sock, $idEnquete, 'INICIO');
  		  		
  	$res=Enviar($sock, $consulta);
  	return($res);
}


/* *******************************************************************
   finalizarEnquete - finaliza uma enquete (data_fim = agora)
   entrada: $sock - sock de conexao
            $idEnquete - id da enquete
   saida:   resposta da consulta update (true/false)
*/  
function finalizarEnquete($sock, $idEnquete)
{
  $data = time();
  $consulta="update Enquete set data_fim = ".$data.", status = 'F' where idEnquete=".VerificaNumeroQuery($idEnquete);
  $res=Enviar($sock, $consulta);
  AtualizaFerramentaEnquete($sock, $idEnquete, 'FIM');
  return($res);
}

/* *******************************************************************
   aplicarEnquete - aplica uma enquete (data_inicio = agora)
   entrada: $sock - sock de conexao
            $idEnquete - id da enquete
   saida:   resposta da consulta update (true/false)
*/  
function aplicarEnquete($sock, $idEnquete)
{
  $data = time();
  $consulta  = "update Enquete set data_inicio = ".$data.", status = 'A' ";
  $consulta .= "where idEnquete = ".VerificaNumeroQuery($idEnquete);
  $res = Enviar($sock, $consulta);
  AtualizaFerramentaEnquete($sock, $idEnquete, 'INICIO');
  return($res);
}

/*==========================================================
  ========================================================== */

/* *******************************************************************
   verificaVotoAlternativa - verifica se o usuario votou na alternativa
   entrada: $sock - sock de conexao
            $cod_usuario - cod do usuario
            $idAlternativa - id da alternativa
   saida:   'true' caso o usuario tenha votado na alternativa, 'false' caso contr�io
*/  
function verificaVotoAlternativa($sock, $cod_usuario, $idAlternativa){
	$query="SELECT idAlternativa from Enquete_voto where cod_usuario=".VerificaNumeroQuery($cod_usuario)." AND idAlternativa = ".VerificaNumeroQuery($idAlternativa);
	$res = Enviar($sock, $query);
	if (RetornaNumLinhas($res) == 0)
		return false;
	else
		return true;
}


/* *******************************************************************
   getTotalVotos - retorna numero de votos da enquete
   entrada: $sock - sock de conexao
            $idEnquete - id da enquete
   saida:   numero total de votos na enquete
*/  
function getTotalVotos($sock, $idEnquete)
{
  $consulta="select SUM(votos) as total_votos from Enquete_alternativa where idEnquete=".VerificaNumeroQuery($idEnquete);
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista[0]['total_votos']);
}

/* *******************************************************************
   getVotos - retorna os usuario que votaram numa alternativa
   entrada: $sock - sock de conexao
            $idAlternativa - id da alternativa
   saida:   array com
            ['cod_usuario'] - codigo do usuario
            ['nome'] - nome do usuario
*/ 
function getVotos($sock,$idAlternativa,$cod_curso){
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

	$query="SELECT U.nome, UC.cod_usuario
	        FROM Enquete_voto EV, ".$dbnamebase.".Usuario_curso UC
	        INNER JOIN ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
	        WHERE EV.cod_usuario = UC.cod_usuario
	        AND UC.cod_curso = ".$cod_curso."
	        AND EV.idAlternativa= ".VerificaNumeroQuery($idAlternativa);
	$res = Enviar($sock, $query);

  	return($res);
}

/* *******************************************************************
   getParticipantesEnquete - retorna os usuario que participaram de uma enquete (votaram na enquete)
   entrada: $sock - sock de conexao
            $idEnquete - id da enquete
   saida:   array com
            ['cod_usuario'] - codigo do usuario
            ['nome'] - nome do usuario
*/ 
function getParticipantesEnquete($sock, $idEnquete, $cod_curso){
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

	$query="select idAlternativa from Enquete_alternativa where idEnquete = ".VerificaNumeroQuery($idEnquete);
	$alternativas = Enviar($sock, $query);
	
	$alternativa = RetornaLinha($alternativas);
	$query="SELECT DISTINCT U.nome, UC.cod_usuario
	        FROM Enquete_voto EV, ".$dbnamebase.".Usuario_curso UC
	        INNER JOIN ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
	        WHERE EV.cod_usuario = UC.cod_usuario
	        AND UC.cod_curso = ".$cod_curso."
	        AND (EV.idAlternativa = ".VerificaNumeroQuery($alternativa['idAlternativa']);
	
	while ($alternativa = RetornaLinha($alternativas)){
		$query .= " OR EV.idAlternativa = ".VerificaNumeroQuery($alternativa['idAlternativa']);
	}
	
	$query .= ")";
	
	$res = Enviar($sock, $query);
  	return($res);
}


/* *******************************************************************
   getInputType - retorna tipo de input
   entrada: $num_escolhas
   saida:   'RADIO' se $num_escolhas = 1
            'CHECKBOX' se $num_escolhas = N
*/        
function getInputType($num_escolhas){
	if (strcmp($num_escolhas, "1") == 0){
		return "RADIO";
	} else { //N
		return "CHECKBOX";
	}
}

/* *******************************************************************
   getStatusEnquete - retorna status da enquete
   entrada: $sock - sock de conexao
            $enquete - array associativo da enquete
   saida:   'LIXEIRA' - enquete na lixeira
            'NAO_REALIZADA' - data_inicio > agora
            'ANDAMENTO' - data_inicio < agora e data_fim > agora
            'ENCERRADA' - data_fim < agora
*/      
function getStatusEnquete($sock, $enquete)
{
  $data_inicio = $enquete['data_inicio'];
  $data_fim = $enquete['data_fim'];
  $data = time();
  if (strcmp($enquete['status'], 'L') == 0)
        return "LIXEIRA";
  if (($data_inicio < $data) && ($data_fim > $data))
  	return "ANDAMENTO";
  if (($data_inicio > $data))
  	return "NAO_APLICADA";
  if (($data_fim < $data))
  	return "ENCERRADA";

}

/* *******************************************************************
	setStatusEnquetesRecentes - verifica todas as enquetes que
		possuem status que precisam ser alterados! 
		entrada: $sock - sock de conex�o com o curso
*/
function setStatusEnquetesRecentes($sock)
{
	$data = time();
	
	// Inicializa enquetes Inativas
	
	// Pega ids de enquetes que serao ativadas
	$query  = "select idEnquete from Enquete ";
	$query .= "where status = 'I' and data_inicio < ".$data;
	$res = Enviar($sock, $query);
	$linhas_nova = RetornaArrayLinhas($res);
	
	$query  = "update Enquete set status = 'A' ";
	$query .= "where status = 'I' and data_inicio < ".$data;
	$res = Enviar($sock, $query);
	
	for($i = 0; $i < sizeof($linhas_nova); $i++)
	{
		AtualizaFerramentaEnquete($sock, $linhas_nova[$i]['idEnquete'], 'INICIO');
	}
	
	// Finaliza enquetes Ativas
	
	// Pega ids de enquetes que serao finalizadas
	$query  = "select idEnquete from Enquete ";
	$query .= "where status = 'A' and data_fim < ".$data;
	$res = Enviar($sock, $query);
	$linhas_fim = RetornaArrayLinhas($res);
	
	$query  = "update Enquete set status = 'F' ";
	$query .= "where status = 'A' and data_fim < ".$data;	
	$res = Enviar($sock, $query);
	
	for($i = 0; $i < sizeof($linhas_fim); $i++)
	{
		AtualizaFerramentaEnquete($sock, $linhas_fim[$i]['idEnquete'], 'FIM');
	}
}	

/* *******************************************************************
   getCodStatusEnquete - retorna o codigo do status da enquete
   entrada: $sock - sock de conexao
            $enquete - array associativo da enquete
   saida:   3 - 'LIXEIRA' 
            0 -'NAO_REALIZADA' 
            1 -'ANDAMENTO'
            2 -'ENCERRADA' 
*/      
function getCodStatusEnquete($sock, $idEnquete)
{
  $enquete = getEnquete($sock, $idEnquete);
  $data_inicio = $enquete['data_inicio'];
  $data_fim = $enquete['data_fim'];
  $data = time();
  if (strcmp($enquete['status'], 'L') == 0)
        return "3";
  if (($data_inicio > $data))
  	return "0";
  if (($data_inicio < $data) && ($data_fim > $data))
  	return "1";
  if (($data_fim < $data))
  	return "2";

}
  
/*==========================================================
  ========================================================== */

/*******************************************************
  Fun�o SalvaEnquete- Salva enquete
  Retorna true se a opera�o for bem sucedida ou false
    se ocorrer um erro.
  Entradas: $sock - sock de conexao,
            $cod_usuario - codigo do usuario,
            $titulo - titulo da enquete,
            $pergunta - pergunta da enquete
            $data_inicio - data de inicio da consulta 
            $data_fim -  data de fim da consulta
            $aplicacao - especifica��o de quais pessoas votariam na enquete (RFAVC)
            $resultado - especifica��o de quais pessoas veriam o resultado da enquete (RFAVC)
            $resultado_parcial - 'S' se possuir resultado parcial, 'N' caso contr�io
            $identidade_votos  - 'S' se a identidade dos votos ser�mostrada, 'N' caso contr�io 
            $num_escolhas - '1' se somente uma resposta pode ser escolhida, 'N' caso v�ias possam
            $alternativa[] - array com as alternativas para a enquete
  Saida:   true se bem-sucedido ou false se falhar.
******************************************************/
function SalvaEnquete($sock,$cod_usuario,$titulo,$pergunta, $data_inicio, $data_fim, $aplicacao, $resultado, $resultado_parcial, $identidade_votos, $num_escolhas, $alternativa)
{
  /* Elimina possiveis scripts e tags HTML.          */
  $titulo=EliminaScript($titulo);
  $titulo=LimpaTitulo($titulo);
  $pergunta=EliminaScript($pergunta);
  $pergunta=LimpaTags($pergunta);
  
  if ($data_inicio < time ())
  {
  	if ($data_fim > time())
  		$status = "A";
  	else
  		$status = "F";
  }
  else
  {
  	$status = "I";
  }

  $query ="INSERT INTO Enquete (cod_usuario,titulo,pergunta, data_inicio, data_fim, aplicacao, resultado , resultado_parcial, identidade_votos, num_escolhas, status) VALUES (".VerificaNumeroQuery($cod_usuario).",'".VerificaStringQuery($titulo)."', '".VerificaStringQuery($pergunta)."' , ".VerificaNumeroQuery($data_inicio).", ".VerificaNumeroQuery($data_fim).", '".VerificaStringQuery($aplicacao)."', '".VerificaStringQuery($resultado)."', '".VerificaStringQuery($resultado_parcial)."', '".VerificaStringQuery($identidade_votos)."', '".VerificaStringQuery($num_escolhas)."', '".$status."')";
  $res = Enviar($sock, $query);
  $idEnquete = mysql_insert_id();  
  for($i = 0; $i <= sizeof($alternativa); $i++){
    	if (strcmp($alternativa[$i], "") != 0){
    		$query ="INSERT INTO Enquete_alternativa (idEnquete, texto) VALUES (".VerificaNumeroQuery($idEnquete)." , '".VerificaStringQuery($alternativa[$i])."')";
  		Enviar($sock, $query);
	}
  }
  if (!$res)
    return false;
  else
  {
  	if ($status == 'A')
  		AtualizaFerramentaEnquete($sock, $idEnquete, 'INICIO');

    return $idEnquete;
  }
}

/*******************************************************
  Fun�o EditaEnquete - Edita  enquete
  Retorna true se a opera�o for bem sucedida ou false
    se ocorrer um erro.
  Entradas: $sock - sock de conexao,
            $cod_usuario - codigo do usuario,
            $titulo - titulo da enquete,
            $pergunta - pergunta da enquete
            $data_inicio - data de inicio da consulta 
            $data_fim -  data de fim da consulta
            $aplicacao - especifica�o de quais p��s votar� na enquete (RFAVC)
            $resultado - especifica�o de quais p��s ver� o resultado da enquete (RFAVC)
            $resultado_parcial - 'S' se possuir resultado parcial, 'N' caso contr�io
            $identidade_votos  - 'S' se a identidade dos votos ser�mostrada, 'N' caso contr�io 
            $num_escolhas - '1' se somente uma resposta pode ser escolhida, 'N' caso v�ias possam
            $alternativa[] - array com as alternativas para a enquete
  Saida:   true se bem-sucedido ou false se falhar.
******************************************************/

function EditaEnquete($sock, $idEnquete, $titulo,$pergunta, $data_inicio, $data_fim, $aplicacao, $resultado, $resultado_parcial, $identidade_votos, $num_escolhas , $alternativa)
{
/* Elimina poss�eis scripts e tags HTML.          */
  $titulo=EliminaScript($titulo);
  $titulo=LimpaTitulo($titulo);
  $pergunta=EliminaScript($pergunta);
  $pergunta=LimpaTags($pergunta);
  
  if ($data_inicio < time ())
  {
  	if ($data_fim > time())
  		$status = "A";
  	else
  		$status = "F";
  }
  else
  {
  	$status = "I";
  }
  

  $query ="update Enquete set titulo = '".VerificaStringQuery($titulo)."', pergunta = '".VerificaStringQuery($pergunta)."', data_inicio = ".VerificaNumeroQuery($data_inicio).", data_fim = ".VerificaNumeroQuery($data_fim)." , aplicacao = '".VerificaStringQuery($aplicacao)."', resultado = '".VerificaStringQuery($resultado)."',  resultado_parcial = '".VerificaStringQuery($resultado_parcial)."', identidade_votos = '".VerificaStringQuery($identidade_votos)."', num_escolhas = '".VerificaStringQuery($num_escolhas)."', status = '".$status."' WHERE idEnquete = ".VerificaNumeroQuery($idEnquete);
  $res = Enviar($sock, $query);
  
  //deleta todas as alternativas dessa enquete
  $query="delete from Enquete_alternativa where idEnquete = ".VerificaNumeroQuery($idEnquete);
  $res2 = Enviar($sock, $query);
  
  //insere alternativas
  for($i = 0; $i <= sizeof($alternativa); $i++){
    	if (strcmp($alternativa[$i], "") != 0){
    		$query ="INSERT INTO Enquete_alternativa (idEnquete, texto) VALUES (".VerificaNumeroQuery($idEnquete)." , '".VerificaStringQuery($alternativa[$i])."')";
  		Enviar($sock, $query);
	}
  }
  if (!$res)
    return false;
  else
  {
  	if ($status == 'A')
  		AtualizaFerramentaEnquete($sock, $idEnquete, 'INICIO');
  		
    return true;
  }
}

/* *******************************************************************
   MudaFimEnquete - muda a data de t�mino de uma enquete (adianta/prorroga)
   entrada: $sock - sock de conexao
            $idEnquete - id da enquete
            $data_fim -  nova data de t�mino
   saida:   nenhuma
*/ 
function MudaFimEnquete($sock, $idEnquete, $data_fim)
{
	$consulta = "select data_inicio, status from Enquete where idEnquete = ".VerificaNumeroQuery($idEnquete);
	$res = Enviar($sock, $consulta);
	$linha = RetornaLinha($res);
	$data = time();
	
	if ($linha['data_inicio'] > $data)
	{
		if ($data_fim < $data)
			$status = "F";
		else
			$status = "A";
	}
	else
	{
		$status = "I";
	}

	$consulta="update Enquete set data_fim = ".VerificaNumeroQuery($data_fim).", status = '".$status."' where idEnquete=".VerificaNumeroQuery($idEnquete);
	$res=Enviar($sock, $consulta);
	
	if ((strcmp($linha['status'], "I") == 0) && (strcmp($status,"A") == 0))
		AtualizaFerramentaEnquete($sock, $idEnquete, 'INICIO');
	else if	((strcmp($linha['status'], "A") == 0) && (strcmp($status,"F") == 0))
		AtualizaFerramentaEnquete($sock, $idEnquete, 'FIM');
		
		
  	return($res);
}

/* *******************************************************************
   deleteVoto - retira o voto de um usuario numa alternativa e decrementa o contador de votos da alternativa
   entrada: $sock - sock de conexao
            $cod_usuario - codigo do usuario
            $idAlternativa - id da alternativa
   saida:   nenhuma
*/ 
function deleteVoto($sock, $cod_usuario, $idAlternativa){
		
	$query="update Enquete_alternativa set votos = (votos - 1)  where idAlternativa = ".VerificaNumeroQuery($idAlternativa);
	$res = Enviar($sock, $query);
		
	$query="delete from Enquete_voto where idAlternativa = ".VerificaNumeroQuery($idAlternativa)." AND cod_usuario =". VerificaNumeroQuery($cod_usuario);
	$res = Enviar($sock, $query);	
}


/* *******************************************************************
   insertVoto - insere voto numa alternativa ou alternativas
   entrada: $sock - sock de conexao
            $cod_usuario - codigo do usuario
            $idAlternativa[] - array com id's de alternativa
   saida:   nenhuma
*/ 
function insertVoto($sock, $cod_usuario, $idEnquete, $idAlternativa){
        $status = true;

	//pega todas as alternativas dessa enquete
        $ja_votou = false;
        $query="select idAlternativa from Enquete_alternativa where idEnquete = ".VerificaNumeroQuery($idEnquete);
	$alternativas = Enviar($sock, $query);
	// deleta os votos anteriores
	while ($alternativa = RetornaLinha($alternativas)){
		$query="select idAlternativa from Enquete_voto where idAlternativa = ".VerificaNumeroQuery($alternativa['idAlternativa']). " AND cod_usuario = ".VerificaNumeroQuery($cod_usuario);
		$res = Enviar($sock, $query);
		if (RetornaNumLinhas($res) != 0){
                        $ja_votou = true;	
			deleteVoto($sock, $cod_usuario, $alternativa['idAlternativa']);
		}	
	}
        
        if($ja_votou){
		$status = "SUBSTITUIDO";	
	}
	
	// insere voto para cada uma das alternativas
	for ($i=0; $i < sizeof($idAlternativa); $i++){
		$query="update Enquete_alternativa set votos = votos +1  where idAlternativa = ".VerificaNumeroQuery($idAlternativa[$i]);
		if (!($res = Enviar($sock, $query))) return false;
		
		$query="insert into Enquete_voto (idAlternativa, cod_usuario) values (".VerificaNumeroQuery($idAlternativa[$i]).", ".VerificaNumeroQuery($cod_usuario).")";
		if(!($res = Enviar($sock, $query))) return false;
	}
	
	return $status;	
}

/*==========================================================
  ========================================================== */
  
  
/* *******************************************************************
   votaEnquete - verifica se um usu�rio pode votar numa enquete
   entrada: $sock - sock de conexao
            $ator - c�digo do ator
            $enquete - array associativo da enquete
   saida:   'true' se o usuario tem permiss�o para votar na enquete, 'false' caso contr�rio
*/ 
function votaEnquete($sock, $ator, $enquete){
	
        if(strcmp($ator, 'R') == 0)
          $ator = 'F';
        
        $pos = strpos($enquete['aplicacao'], $ator);
        
        if ($pos === false) 	
                return false;
	else
		return true;
}

/* *******************************************************************
   jaVotouEnquete - verifica se um usu�rio j� votou numa enquete
   entrada: $sock - sock de conexao
            $ator - c�digo do ator
            $enquete - array associativo da enquete
   saida:   'true' se o usuario j� tinha votado na enquete, 'false' caso contr�rio
*/ 
function jaVotouEnquete($sock,$cod_usuario, $idEnquete){
	$query="SELECT EV.cod_usuario from Enquete_voto EV, Enquete_alternativa EA WHERE EV.idAlternativa = EA.idAlternativa AND  EA.idEnquete = ".VerificaNumeroQuery($idEnquete)." AND EV.cod_usuario= ".VerificaNumeroQuery($cod_usuario);
	$res = Enviar($sock, $query);
	if (RetornaNumLinhas($res) == 0)
		return false;
	else
		return true;
}

/*==========================================================
  ========================================================== */
  
/* *******************************************************************
   podeVerEnquete - verifica se um usu�io pode ver uma enquete (se esta aplicada a ele ou o resultado esta compartilhado com ele)
   entrada: $sock - sock de conexao
            $ator - c�igo do ator
            $enquete - array associativo da enquete
   saida:   'true' se o usuario tenha permiss� para ver a enquete, 'false' caso contr�io
*/
function podeVerEnquete($sock, $ator, $enquete){
        
        if ((strcmp($ator, "R") == 0) || (strcmp($ator, "F") == 0))
          return true;
        
        $pos1 = strpos($enquete['aplicacao'], $ator);
        $pos2 = strpos($enquete['resultado'], $ator);
        
        if (($pos1 === false) && ($pos2 === false)) 	
                return false;
	else
		return true;
}


/* *******************************************************************
   podeVerResultadoEnquete - verifica se um usu�io pode ver o resultado da enquete
   entrada: $sock - sock de conexao
            $ator - c�igo do ator
            $enquete - array associativo da enquete
   saida:   'true' se o usuario tem permiss� para ver o resultado da enquete, 'false' caso contr�io
*/
function podeVerResultadoEnquete($sock, $ator, $enquete, $criadorEnquete){

        $pos = strpos($enquete['resultado'], $ator);

        /* se é o criador da enquete, pode ver, mesmo que não esteja compartilhado */
        if($criadorEnquete) return true;

        /* se for coordenador e estiver compartilhado para formadores ele tamb� pode ver        */

        if((strcmp($ator, 'R') == 0) ||  $criadorEnquete) {
          if (($pos === false)) $pos = strpos($enquete['resultado'], 'F');
        }

        $status_enquete = getStatusEnquete($sock, $enquete);

        //se esta esta em andamento e n� tem resultado parcial, n� pode ver
        //j�se tiver resultado parcial e o resultado estiver compartilhado com o ator, pode

        if (strcmp($status_enquete, "ANDAMENTO") == 0){
           if ($enquete['resultado_parcial'] == 'S'){
            if (!($pos === false)) return true;
            else return false;
           }else{
            return false;
           }
        //se n� estiver em andamento s�depende do resultado estar ou nao compartilhado 
        } else {
          if (!($pos === false)) return true;
          else return false;
        }
}

function CriadorEnquete($sock, $cod_curso, $cod_usuario, $idEnquete){
  $query = "select * from Enquete where idEnquete = ".VerificaNumeroQuery($idEnquete)." AND cod_usuario = ".VerificaNumeroQuery($cod_usuario);
  $consult = Enviar($sock, $query);
  $res = RetornaArrayLinhas($consult);
  if(count($res > 0) && ($res != "") && eFormador($sock, $cod_curso, $cod_usuario)) return true;
  else return false;
}

/* *******************************************************************
   podeVerIdentidadeEnquete - verifica se um usu�io pode ver a identidade dos votos da enquete
   entrada: $sock - sock de conexao
            $ator - c�igo do ator
            $enquete - array associativo da enquete
   saida:   'true' se o usuario tem permiss� para ver a identidade dos votos da enquete, 'false' caso contr�io
*/
function podeVerIdentidadeEnquete($sock,$ator, $enquete, $criadorEnquete){

        //se pode ver resultado e identidade dos votos est�aberta, pode ver
        if(((podeVerResultadoEnquete($sock,$ator, $enquete, $criadorEnquete))  && (strcmp($enquete['identidade_votos'], "S") == 0)) || $criadorEnquete) {
            return true;
        } else{
          return false;
        }
}

/*==========================================================
  ========================================================== */

/* *******************************************************************
   getTipoAtor - retorna sigla do ator
   entrada: $sock - sock de conexao
            $cod_curso- codigo do curso
            $cod_usuario - codigo do usuario
   saida:   'R' se for Coordenador
            'F' se for Formador
            'A' se for Aluno
            'V' se for Visitante
            'C' se for Convidado
   
function getTipoAtor($sock, $cod_curso, $cod_usuario){
    if (ECoordenador($sock, $cod_curso, $cod_usuario)){
          $sigla= 'R';
    } elseif (EFormador($sock, $cod_curso, $cod_usuario)){
          $sigla= 'F';
    } elseif (EAluno($sock, $cod_curso, $cod_usuario)){
          $sigla= 'A';
    } elseif (EVisitante($sock, $cod_curso, $cod_usuario)){
          $sigla= 'V';
    } elseif (EConvidado($sock, $cod_usuario)){
          $sigla= 'Z';
    } 
    
    return $sigla;

}
*/
/*==========================================================
  Fun�es para preencher a tele de edi�o de enquete
  ========================================================== */

/* *******************************************************************
   RetornaCheckedAplicacao - retorna array de informa�o sobre a aplicacao da enquete
   entrada: $permissoes - permissoes de aplicacao da enquete (quem vota)      
   saida: array contendo CHECKED no index i se o ator de indice i pode votar, vazio caso contrario
          checked[0] - F (formadores)
          checked[1] - A (alunos)
          checked[2] - C (convidados)
          checked[3] - V (visitantes)
*/ 
function RetornaCheckedAplicacao($permissoes){
  for($i = 0; $i < 4; $i++){ 
    if (!(strcmp($permissoes[$i], '-') == 0)) $checked[$i] = "CHECKED";
  }
  return $checked;
}

/* *******************************************************************
   RetornaCheckedAplicacao - etorna array de informa�o sobre o compartilhamento de resultado da enquete
   entrada: $permissoes - permissoes de resultado da enquete (quem pode ver o resultado)      
   saida: array contendo CHECKED no index i se o ator de indice i pode ver o resultado, vazio caso contrario
          checked[0] - R (coordenador)
          checked[1] - F (formadores)
          checked[2] - A (alunos)
          checked[3] - C (convidados)
          checked[4] - V (visitantes)
*/ 
function RetornaCheckedResultado($permissoes){
  for($i = 0; $i < 5; $i++){ 
    if (!(strcmp($permissoes[$i], '-') == 0)) $checked[$i] = "CHECKED";
  }
  return $checked;
}

/* *******************************************************************
   RetornaCheckedSimNao - etorna array de informa�o sobre o compartilhamento de resultado da enquete
   entrada: $bool - S (sim) / N (nao)     
   saida: ($cheched[0] =  "CHECKED" && $cheched[1] =  NULL) SE bool = true
          ($cheched[0] =  NULL && $cheched[1] = "CHECKED") SE bool = false
*/ 
function RetornaCheckedSimNao($bool){
  if (strcmp($bool, 'S') == 0) $checked[0] = "CHECKED";
  else $checked[1] = "CHECKED";
  
  return $checked;
}

/* *******************************************************************
   RetornaCheckedSimNao - etorna array de informa�o sobre o compartilhamento de resultado da enquete
   entrada: $num_escolhas - 1 (somente uma) / N (uma ou mais alternativas)     
   saida: ($cheched[0] =  "CHECKED" && $cheched[1] =  NULL) SE $num_escolhas = 1
          ($cheched[0] =  NULL && $cheched[1] = "CHECKED") SE $num_escolhas = N
*/ 
function RetornaCheckedNumEscolhas($num_escolhas){
  if (strcmp($num_escolhas, '1') == 0) $checked[0] = "CHECKED";
  else $checked[1] = "CHECKED";
  
  return $checked;

}

?>
