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
  - 'M' - Multipla Escolha
  - 'D' - Questao Dissertativa

- Status de um Exercicio e/ou de uma Questao (status):

  - 'V' - Valido(a)
  - 'X' - Excluido(a)
  - 'L' - Lixeira

- Status no historico de um Exercicio:

  - 'C' - Criado

- Status no historico de uma Questao:

  - 'A' - Alternativa criada
  - 'B' - Alternativa apagada
  - 'C' - Questao criada

- Situacao de um Exercicio:

- C - em criação
- A - aplicado


************************************************************************/

/* *********************************************************************
  	atualizaExercicioModelo - H� muito c�digo repetido fazendo altera��o,
  		isso �, update na tabela Exercicio_Modelo.
  		Fun��o criada para evitar repeti��o de c�digo.
   Entrada: $sock 			- BASE DO CURSO
			$cod_exercicio 	- c�digo do exerc�cio que est� sendo marcado.
			$opt			- array ['coluna'=>'valor'] para atualizar os valores
				da 'coluna' com 'valor' referenciado.
				Importante: Se inteiro, s� passar valor como n�mero; se for string
				passar como "'valor'" para evitar conflito no SQL
	//TODO - Alterar as fun��es desta biblioteca para implementar esta id�ia.
*/
function AtualizaExerciciosModelo($sock, $cod_exercicio, $opt="")
{
	$query = "update Exercicios_modelo set data = ".time()." ";
	
	if ($opt!="")
	{
		foreach ($opt as $coluna=>$valor)
		{
			$query.= $coluna." = ".$valor." ";
		}
	}
	
	$query.= "where cod_exercicio = ".$cod_exercicio;
	Enviar($sock, $query);
}

/* *********************************************************************
   marcaLogExerciciosModeloHistorico - Ao efetuar uam altera��o em algum exerc�cio,
								essa fun��o deve ser chamada para registrar a a��o 
								no log dos exerc�cios em Exercicios_modelo_historico
   Entrada: $sock 			- BASE DO CURSO
			$cod_exercicio 	- c�digo do exerc�cio que est� sendo marcado
			$cod_usuario   	- c�digo do usu�rio que est� editando o exerc�cio
			$acao 			- Acao da edi��o, verifique na wiki todas as dispon�veis
   Saida:	Uma inser��o na tabela Exercicios_modelo_historico
*/
function marcaLogExerciciosModeloHistorico($sock, $cod_exercicio, $cod_usuario, $acao)
{
  // Inserindo altera��o na tabela Exercicios_modelo_historico
  $query = "insert into Exercicios_modelo_historico ";
  $query.= "(cod_exercicio, cod_usuario, data, acao) values ";
  $query.= "(".$cod_exercicio.", ".$cod_usuario.", ".time().", '".$acao."') ";
  $query.= "on duplicate key update data=".time();
  $res = Enviar($sock, $query);
}

/* *********************************************************************
   marcaLogExerciciosResolucaoHistorico - Quando o aluno entrega um exerc�cio ou
								um formador corrige o trabalho, essa fun��o � 
								chamada para registrar a a��o no log das resolu��es.
   Entrada: $sock 			- BASE DO CURSO
			$cod_resolucao 	- c�dido da resolu��o que est� sendo feita do exerc�cio. 
			$acao 			- Acao da edi��o, verifique na wiki todas as dispon�veis
   Saida:	Uma inser��o na tabela Exercicios_resolucao_historico
*/
function marcaLogExerciciosResolucaoHistorico($sock,$cod_resolucao,$acao,$cod="")
{
	// Caso cod_usuario não passado por parametro
	if ($cod == "")
	{
		$query = "select cod_usuario, cod_grupo from Exercicios_resolucao ";
		$query.= "where cod_resolucao = ".$cod_resolucao;
		$res = Enviar($sock, $query);
		$linha = RetornaLinha($res);
		if ($linha['cod_usuario'] != "")
			$cod = $linha['cod_usuario'];
		else 
			$cod = $linha['cod_grupo'];
	}

	// Marca log apenas se cod_usuario salvo
	if ($cod!= "")
	{
		$query = "insert into Exercicios_resolucao_historico ";
		$query.= "(cod_resolucao,cod_usuario,data, acao) values ";
		$query.= "(".$cod_resolucao.", ".$cod.",";
		$query.= time().",'".$acao."') ";
		$query.= "on duplicate key update data=".time();
		Enviar($sock, $query);
	}
}

function RetornaUsuarioPorResolucao($sock, $cod_resolucao){
	
	$query = "select cod_usuario from Exercicios_resolucao where cod_resolucao = ".VerificaNumeroQuery($cod_resolucao);
	
	$res = Enviar($sock, $query);
	
	$linha = RetornaLinha($res);
	
	//if($linha['cod_usuario'] != null)
		return($linha['cod_usuario']);
	//else
		//RetornaGrupoPorResolucao($sock, $cod_resolucao)
	
}

function RetornaGrupoPorResolucao($sock, $cod_resolucao){
	
	$query = "select cod_grupo from Exercicios_resolucao where cod_resolucao = ".VerificaNumeroQuery($cod_resolucao);
	$res = Enviar($sock, $query);
	$linha = RetornaLinha($res);
	
	return($linha['cod_grupo']);
}

/* *********************************************************************
   RetornaExercicios - Retorna array com os exercicios válidos (V), ou seja, que não foram excluídos (X) e 
                       não estão na lixeira (L)
   Entrada: $sock - BASE DO CURSO
   Saida: array com []['cod_exercicio'] - chave primaria ou codigo do exercicio
					[]['cod_usuario'] - codigo do usuario que criou o exercicio
                    []['titulo'] - titulo do exercicio
                    []['situacao'] - flag a qual indica a situacao do exercicio
				    []['tipo_compartilhamento'] - tipo de compartilhamento
                    []['data'] - data em que o exercicio foi criado
*/
function RetornaExercicios ($sock)
{
  $consulta="select cod_exercicio, cod_usuario, titulo, situacao, tipo_compartilhamento, data from Exercicios_modelo where status='V'";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   RetornaDadosExercicioAplicado - Retorna array com os dados do exercicio aplicado
   Entrada: $sock - BASE DO CURSO
				  - cod_exercicio - codigo do exercicio
   Saida: array com []['cod_exercicio'] - chave primaria ou codigo do exercicio
                    []['dt_disponibilizacao'] - data em que o exercicio foi disponibilizado
                    []['dt_limite_submissao'] - data limite em que o exercicio pode ser submetido
                    []['tp_aplicacao'] - tipo de aplicacao
					[]['disp_gabarito'] - indica se havera gabarito disponivel
					[]['avaliacao'] - indica se o exercicio sera avaliado
*/
function RetornaDadosExercicioAplicado($sock,$cod_exercicio)
{
  $consulta="select * from Exercicios_aplicado where cod_exercicio=".VerificaNumeroQuery($cod_exercicio);
  $res=Enviar($sock, $consulta);
  $lista=RetornaLinha($res);
  return($lista);
}

/* *********************************************************************
   RetornaExerciciosLixeira - Retorna array com os exercicios da lixeira (L)
   Entrada: $sock - BASE DO CURSO
   Saida: array com []['cod_exercicio'] - chave primaria ou codigo do exercicio
                    []['titulo'] - titulo do exercicio
                    []['data'] - data em que o exercicio foi criado
*/
function RetornaExerciciosLixeira($sock)
{
  $consulta="select * from Exercicios_modelo where status='L'";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   RetornaExerciciosUsuario - Retorna array com os exercicios do usuario
   Entrada: $sock - BASE DO CURSO
			$cod_usuario - codigo do usuario logado
			$cod_curso - codigo do curso
            $eformador - true se o usuario logado for formador
            $cod_usuario_exercicio - codigo do usuario dono do exercicio
   Saida: array com []['cod_resolucao'] - chave primaria ou codigo da resolucao
                    []['titulo'] - titulo do exercicio
                    []['dt_submissao'] - data limite para submissao
                    []['submetida'] - indica se a resolucao foi submetida
                    []['corrigida'] - indica se a resolucao foi corrigida
                    []['compartilhada'] - indica o compartilhamento da resolucao
*/
function RetornaExerciciosUsuario($sock,$cod_usuario,$cod_curso,$eformador,$cod_usuario_exercicio)
{
  if ($cod_usuario==$cod_usuario_exercicio)
    $regra="";
  else if ($eformador)
    $regra="and (ER.compartilhada='T' or ER.compartilhada='F')";
  else
    $regra="and ER.compartilhada='T'";
    
  if (EFormadorComVisaoDeAluno($sock, $cod_curso, $cod_usuario))
    $regra="and ER.compartilhada='T'";
    
  $consulta="select ER.cod_resolucao as cod_resolucao,ER.cod_exercicio as cod_exercicio,EM.titulo as titulo,ER.dt_submissao as dt_submissao,ER.submetida as submetida,ER.corrigida as corrigida,ER.compartilhada as compartilhada 
             from Exercicios_aplicado EA, Exercicios_resolucao ER 
             inner join Exercicios_modelo EM on (ER.cod_exercicio = EM.cod_exercicio)
             where ER.cod_usuario=".VerificaNumeroQuery($cod_usuario_exercicio)." AND EA.cod_exercicio = ER.cod_exercicio AND EA.dt_disponibilizacao < ".time();
  $res=Enviar($sock,$consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   RetornaExerciciosGrupo - Retorna array com os exercicios do grupo
   Entrada: $sock - BASE DO CURSO
            $cod_grupo - codigo do grupo
   Saida: array com []['cod_resolucao'] - chave primaria ou codigo da resolucao
                    []['dt_submissao'] - data limite para submissao
                    []['submetida'] - indica se a resolucao foi submetida
                    []['corrigida'] - indica se a resolucao foi corrigida
                    []['compartilhada'] - indica o compartilhamento da resolucao
*/
function RetornaExerciciosGrupo($sock,$cod_usuario,$cod_curso,$eformador,$cod_grupo_exercicio)
{
  if (PertenceAoGrupo($sock,$cod_usuario, $cod_grupo_exercicio))
    $regra="";
  else if ($eformador)
    $regra="and (ER.compartilhada='T' or ER.compartilhada='F')";
  else
    $regra="and ER.compartilhada='T'";
  
  if (EFormadorComVisaoDeAluno($sock, $cod_curso, $cod_usuario))
    $regra="and ER.compartilhada='T'";

$consulta="select ER.cod_resolucao as cod_resolucao,ER.cod_exercicio as cod_exercicio,EM.titulo as titulo,ER.dt_submissao as dt_submissao,ER.submetida as submetida,ER.corrigida as corrigida,ER.compartilhada as compartilhada 
             from Exercicios_aplicado EA, Exercicios_resolucao ER 
             inner join Exercicios_modelo EM on (ER.cod_exercicio = EM.cod_exercicio)
             where ER.cod_grupo=".VerificaNumeroQuery($cod_grupo_exercicio)." ".$regra." AND EA.cod_exercicio = ER.cod_exercicio AND EA.dt_disponibilizacao < ".time();

  $res=Enviar($sock,$consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/**********************************************************************
   RetornaNumComentariosExercicio - Retorna o numero de comentarios e a maior data dentro de um exercicio
   Entrada: $sock - Conexao com a base interna
            $cod_usuario - codigo do usuario logado
            $cod_resolucao - codigo da resolucao a ser checada
   Saida: array com ['num_comentarios']
                    ['num_comentarios_alunos'] - numero de coment?ios feitos por alunos que n? o usuario
                    ['num_comentarios_formadores'] - numero de coment?ios feitos por formadores que n? o usuario
                    ['num_comentarios_usuario'] - numero de coment?ios feitos pelo usuario
                    ['data_comentarios'] - Maior data de um coment?io
*/
function RetornaNumComentariosExercicio($sock, $cod_usuario, $cod_resolucao)
{
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select Erc.cod_usuario 'cod', count(*) 'ct', max(Erc.data) 'dt' from Exercicios_resolucao_questao_comentarios Erc where Erc.cod_resolucao=".$cod_resolucao." and Erc.status='A' group by Erc.cod_usuario order by Erc.cod_usuario";
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

/**********************************************************************
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
   RetornaSituacaoExercicio - Retorna string dizendo a situacao do exercicio
   Entrada: $situacao - situacao do exercicio
            $data - data atual
            $data_disp - data em que foi/sera disponibilizado
   Saida: string dizendo a situacao do exercicio
*/
function RetornaSituacaoExercicio($situacao,$data,$data_disp)
{
  switch($situacao)
  {
  	case 'A': if($data >= $data_disp)
  	            return "Disponibilizado";
  	          else 
  	            return "Agendado";
  	          break;
  	case 'C': return "Em cricao";
  }
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
  $consulta="select cod_questao, cod_usuario, cod_topico, titulo, tp_questao, data, tipo_compartilhamento from Exercicios_questao where status='V'";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   RetornaResolucao - Retorna array a resolucao de um exercicio
   Entrada: $sock - BASE DO CURSO
            $cod_resolucao = código da resolucao
   Saida: array com []['cod_resolucao'] - chave primaria ou codigo da resolucao
                    []['cod_exercicio'] - codigo do exercicio referenre a resolucao
                    []['cod_usuario'] - codigo do usuario a que pertence a resolucao, se existir
                    []['cod_grupo'] - código do grupo ao qual pertence a resolucao, se existir
                    []['dt_submissao'] - data em que foi submetida, se foi submitida
                    []['nota'] - nota, caso tenho sido corrigida
                    []['submetida'] - indica se foi ou nao submetida
                    []['corrigida'] - indica se foi ou nao corrigida
                    []['compartilhada'] - indica qual o compartilhamento
                    []['compartilhada'] - situacao da resolucao
*/
function RetornaResolucao($sock,$cod_resolucao)
{
  $consulta="select * from Exercicios_resolucao where cod_resolucao = ".VerificaNumeroQuery($cod_resolucao);
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}

/* *********************************************************************
   RetornaNomeTopico - Retorna o nome do topico a partir de seu codigo
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo do topico que se deseja saber o nome
   Saida: array com []['topico'] - nome do topico
                    
*/
function RetornaNomeTopico($sock,$cod_topico)
{
  $consulta="select topico from Exercicios_topico where cod_topico=".VerificaNumeroQuery($cod_topico);
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
  $data = time();

  $consulta="insert into Exercicios_questao (cod_usuario, titulo, tp_questao, data, status, inicio_edicao) values (".VerificaNumeroQuery($cod_usuario).", '".VerificaStringQuery(htmlentities($titulo))."', '".VerificaStringQuery(htmlentities($tp_questao))."', ".$data.", 'V', ".$data.")";
  $res=Enviar($sock, $consulta);

  $cod_questao = mysql_insert_id();

  $consulta="insert into Exercicios_questao_historico values (".VerificaNumeroQuery($cod_questao).", ".VerificaNumeroQuery($cod_usuario).", ".$data.", 'C')";
  $res=Enviar($sock, $consulta);
  
  $consulta="insert into Exercicios_questao_diss (cod_questao) values (".VerificaNumeroQuery($cod_questao).")";
  $res=Enviar($sock, $consulta);

  return($cod_questao);
}

/* *********************************************************************
   CriarExercicio - Cria um novo exercicio 
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - usuario que criou o exercicio
            $cod_curso - codigo do curso ao qual pertence o exercicio
            $titulo - titulo do exercicio a ser criado
   Saida: codigo do exercicio criado
*/
function CriarExercicio($sock, $cod_usuario, $cod_curso, $titulo)
{
  $data = time();

  $consulta = "insert into Exercicios_modelo (cod_usuario, titulo, data, status, situacao, inicio_edicao) ";
  $consulta.= "values (".VerificaNumeroQuery($cod_usuario).", '".VerificaStringQuery(htmlentities($titulo))."', ".$data.", 'V', 'C', ".$data.")";
  $res=Enviar($sock, $consulta);

  $cod_exercicio = mysql_insert_id();

  marcaLogExerciciosModeloHistorico($sock, $cod_exercicio, $cod_usuario, 'C');
  AtualizaFerramentasNova($sock, 23, 'T');
  return($cod_exercicio);
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
   RetornaResHistoricoDaQuestao - Retorna todas as ocorr�ncias do historico de uma questao
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
   Saida: array []['cod_exercicio']
                []['cod_usuario']
                []['data']
                []['acao']
*/
function RetornaResHistoricoDoExercicio ($sock, $cod_exerc)
{
  $consulta="select * from Exercicios_modelo_historico where cod_exercicio=".VerificaNumeroQuery($cod_exerc)." order by data desc,acao desc";
  $res=Enviar($sock, $consulta);
  return($res);
}

/**********************************************************************
   RetornaQuestoesExercicio - Retorna as questoes de um exercicio
   Entrada: $sock - BASE Interna
            $cod_exercicio - codigo do exercicio
   Saida: array com []['cod_questao'] - chave primaria ou codigo da questao
                    []['valor'] - valor da questao 
                    []['titulo'] - titulo da questao
                    []['enunciado'] - enunciado da questao
                    []['tp_questao'] - tipo da questao
					[]['nivel'] - dificuldade da questao
					[]['cod_topico'] - codigo do topico da questao
*/
function RetornaQuestoesExercicio($sock,$cod_exercicio)
{
  $query="select MQ.cod_questao,MQ.valor,Q.titulo,Q.enunciado,Q.tp_questao,Q.nivel,Q.cod_topico from Exercicios_modelo_questao as MQ cross join Exercicios_questao as Q on MQ.cod_questao = Q.cod_questao where MQ.cod_exercicio=".VerificaNumeroQuery($cod_exercicio);
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}

/**********************************************************************
   RetornaSomaValorQuestoes - Retorna a soma do valor de cada questoes
   Entrada: $sock - BASE Interna
            $cod_exercicio - codigo do exercicio
   Saida: soma do valor de cada questao
*/
function RetornaSomaValorQuestoes($sock,$cod_exercicio)
{
  $query="select SUM(valor) as soma from Exercicios_modelo_questao where cod_exercicio=".VerificaNumeroQuery($cod_exercicio);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['soma'];
}

/**********************************************************************
   RetornaValorQuestao - Retorna o valor da questao
   Entrada: $sock - BASE Interna
            $cod_exercicio - codigo do exercicio
		    $cod_questao - codigo da questao
   Saida: soma do valor de cada questao
*/
function RetornaValorQuestao($sock,$cod_exercicio,$cod_questao)
{
  $query="select valor from Exercicios_modelo_questao where cod_exercicio=".VerificaNumeroQuery($cod_exercicio)." and cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['valor'];
}

/* *********************************************************************
   RetornaExercicio - Retorna um exercicio
   Entrada: $sock - BASE Interna
            $cod_exercicio - codigo do exercicio
   Saida: array ['cod_exercicio'] - codigo do exercicio
                ['cod_usuario'] - codigo do usuario
                ['titulo'] - titulo do exercicio
                ['texto'] - texto do exercicio
                ['tipo_compartilhamento'] - compartilhamento da questao
                ['situacao'] - situacao do exercicio
*/
function RetornaExercicio($sock,$cod_exercicio)
{
  $query="select cod_exercicio,cod_usuario,titulo,texto,tipo_compartilhamento,situacao from Exercicios_modelo where cod_exercicio=".VerificaNumeroQuery($cod_exercicio);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha;
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
  $query="select cod_questao,cod_usuario,cod_topico,titulo,enunciado,nivel,tp_questao,tipo_compartilhamento from Exercicios_questao where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha;
}

function RetornaQuestaoDiss($sock,$cod_questao)
{
  $query="select gabarito from Exercicios_questao_diss where cod_questao=".$cod_questao;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['gabarito'];
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
  $query="select cod_alternativa,cod_questao,texto from Exercicios_questao_alternativa where cod_questao=".VerificaNumeroQuery($cod_questao)." order by posicao_item";
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

  if($linha['gabarito'] != '') return($linha['gabarito']);
  else return false;
}

/* *********************************************************************
   RetornaGabaritoQuestaoMult - Retorna o gabarito de uma questao multipla escolha
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
   Saida: gabarito da questao
*/
function RetornaGabaritoQuestaoMult($sock, $cod_questao)
{
  $consulta="select gabarito from Exercicios_questao_mult where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  
  if($linha['gabarito'] != '') return($linha['gabarito']);
  else return false;
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
   RetornaCodAvaliacao - Retorna o codigo da avaliacao do exercicio aplicado
   Entrada: $sock - BASE DO CURSO
            $cod_exercicio - codigo do exercicio
   Saida: gabarito da alternativa
*/
function RetornaCodAvaliacao($sock, $cod_exercicio)
{
  $consulta="select avaliacao from Exercicios_aplicado where cod_exercicio=".VerificaNumeroQuery($cod_exercicio);
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha['avaliacao']);
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
   RetornaListaNomesOuTitulos - Retorna os nomes dos usuarios/grupos ou titulos dos exercicios visiveis pelo usuario
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_usuario - codigo do usuario
            $tela_formador - indica se o usuario é formador ou não
            $agrupamento - opcao do agrupamento (aluno,grupo ou exercicio)
            $visualizar - opcao de visualizacao (I - individual ou G - grupo)
   Saida: array ['cod'] - codigo do usuario/grupo/exercicio
                ['titulo'] - nome do usuario/grupo ou titulo do exercicio
                
*/
function RetornaListaNomesOuTitulos($sock,$cod_curso,$cod_usuario,$tela_formador,$agrupamento,$visualizar)
{
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];
  
  if($agrupamento == "A")
  {
    if($tela_formador)
      //Pega todos os usuarios
      $consulta = "SELECT U.nome as titulo, UC.cod_usuario as cod
                   FROM ".$dbnamebase.".Usuario U
                   INNER JOIN ".$dbnamebase.".Usuario_curso UC ON (UC.cod_usuario_global=U.cod_usuario AND UC.cod_curso='".VerificaStringQuery($cod_curso)."') WHERE UC.cod_usuario <> -1 ORDER BY U.nome";
    else
      //Pega apenas os usuarios com exercicios compartilhados
      $consulta = "SELECT U.nome as titulo,ER.cod_usuario as cod 
                   FROM ".$dbnamebase.".Usuario U
                   INNER JOIN ".$dbnamebase.".Usuario_curso UC ON (UC.cod_usuario_global=U.cod_usuario AND UC.cod_curso='".VerificaStringQuery($cod_curso)."')
                   INNER JOIN Exercicios_resolucao ER ON (UC.cod_usuario=ER.cod_usuario)
                   WHERE ER.compartilhada='T' GROUP BY ER.cod_usuario ORDER BY NULL";
  }
  else if($agrupamento == "G")
  {
    if($tela_formador)
	  $consulta = "SELECT g.nome as titulo, g.cod_grupo as cod FROM Grupos as g";
    else
      $consulta = "SELECT g.nome as titulo, g.cod_grupo as cod FROM Grupos as g INNER JOIN Grupos_usuario as gu ON g.cod_grupo=gu.cod_grupo, Exercicios_resolucao as er WHERE gu.cod_usuario = ".$cod_usuario." or (er.compartilhada='T' and g.cod_grupo = er.cod_grupo) GROUP BY g.cod_grupo ORDER BY NULL";
  }
  else if($agrupamento == "T")
  {
    if($visualizar == "I")
      $consulta = "SELECT em.cod_exercicio as cod, em.titulo as titulo from Exercicios_modelo as em INNER JOIN Exercicios_aplicado as ea ON em.cod_exercicio = ea.cod_exercicio WHERE ea.tp_aplicacao='I' ORDER BY em.cod_exercicio";
    else if($visualizar == "G")
      $consulta = "SELECT em.cod_exercicio as cod, em.titulo as titulo from Exercicios_modelo as em INNER JOIN Exercicios_aplicado as ea ON em.cod_exercicio = ea.cod_exercicio WHERE ea.tp_aplicacao='G' ORDER BY em.cod_exercicio";
  }
  
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);

  return $lista;
}

/* *********************************************************************
   RetornaNumExerciciosNaoEntregues - Retorna o numero de exercicios nao entregues
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo do usuario
            $tela_formador - indica se o usuario é formador ou não
            $agrupamento - opcao do agrupamento (aluno,grupo ou exercicio)
            $visualizar - opcao de visualizacao (I - individual ou G - grupo)
   Saida: array ['cod'] - codigo do usuario/grupo/exercicio
                ['num'] - numero de exercicios nao entregues
                
*/
function RetornaNumExerciciosEntregues($sock,$cod_usuario,$tela_formador,$agrupamento,$visualizar)
{
  if($agrupamento == "A")
  {
    if($tela_formador)
      $consulta = "SELECT er.cod_usuario as cod, COUNT(er.submetida) as num FROM Exercicios_resolucao as er WHERE (er.submetida='S' and er.cod_grupo IS NULL) OR (er.cod_grupo IS NULL and er.status = 'R') GROUP BY er.cod_usuario ORDER BY er.cod_usuario";
    else
      $consulta = "SELECT er.cod_usuario as cod, COUNT(er.submetida) as num FROM Exercicios_resolucao as er WHERE (er.submetida='S' and er.cod_grupo IS NULL and (er.compartilhada='T' or er.cod_usuario = ".$cod_usuario.") ) OR ( er.cod_grupo IS NULL and (er.cod_usuario = ".$cod_usuario." or er.compartilhada='T') and er.status='R') GROUP BY er.cod_usuario ORDER BY er.cod_usuario";
  }
  else if($agrupamento == "G")
  {
    if($tela_formador)
      $consulta = "SELECT er.cod_grupo as cod, COUNT(er.submetida) as num FROM Grupos as g, Exercicios_resolucao as er WHERE (er.cod_usuario IS NULL and g.cod_grupo = er.cod_grupo and er.submetida = 'S') or (er.cod_usuario IS NULL and g.cod_grupo = er.cod_grupo and er.status = 'R') GROUP BY er.cod_grupo ORDER BY g.cod_grupo";
    else //pega apenas o dono e os compartilhados
      $consulta = "SELECT er.cod_grupo as cod, COUNT(er.submetida) as num FROM Grupos as g INNER JOIN Grupos_usuario as gu ON g.cod_grupo=gu.cod_grupo, Exercicios_resolucao as er WHERE (g.cod_grupo = er.cod_grupo and (gu.cod_usuario = ".$cod_usuario." or er.compartilhada='T') and er.submetida='S') or (g.cod_grupo = er.cod_grupo and (gu.cod_usuario = ".$cod_usuario." or er.compartilhada='T') and er.status='R') GROUP BY er.cod_grupo ORDER BY g.cod_grupo";
  }
  else if($agrupamento == "T")
  {
    if($visualizar == "I")
      $consulta = "SELECT er.cod_exercicio as cod, COUNT(er.submetida) as num FROM Exercicios_resolucao as er WHERE er.submetida='S' AND er.cod_grupo IS NULL GROUP BY er.cod_exercicio ORDER BY er.cod_exercicio";
    else if($visualizar == "G")
      $consulta = "SELECT er.cod_exercicio as cod, COUNT(er.submetida) as num FROM Exercicios_resolucao as er WHERE er.submetida='S' AND er.cod_usuario IS NULL GROUP BY er.cod_exercicio ORDER BY er.cod_exercicio";
  }
    
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}

/* *********************************************************************
   RetornaNumExerciciosNaoCorrigidos - Retorna o numero de exercicios nao corrigidos
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo do usuario
            $tela_formador - indica se o usuario é formador ou não
            $agrupamento - opcao do agrupamento (aluno/grupo ou exercicio)
            $visualizar - opcao de visualizacao (I - individual ou G - grupo)
   Saida: array ['cod'] - codigo do usuario/grupo/exercicio
                ['num'] - numero de exercicios nao corrigidos
                
*/
function RetornaNumExerciciosCorrigidos($sock,$cod_usuario,$tela_formador,$agrupamento,$visualizar)
{
  if($agrupamento == "A")
  {
    if($tela_formador)
      $consulta = "SELECT er.cod_usuario as cod, COUNT(er.corrigida) as num FROM Exercicios_resolucao as er WHERE er.corrigida='S' and er.submetida='S' and er.status!='R' and er.cod_grupo IS NULL GROUP BY er.cod_usuario ORDER BY er.cod_usuario";
    else
      $consulta = "SELECT er.cod_usuario as cod, COUNT(er.corrigida) as num FROM Exercicios_resolucao as er WHERE er.corrigida='S' and er.submetida='S' and er.status!='R' and er.cod_grupo IS NULL  and (er.compartilhada='T' or er.cod_usuario = ".$cod_usuario.") GROUP BY er.cod_usuario ORDER BY er.cod_usuario";
  }
  else if($agrupamento == "G")
  {
    if($tela_formador)
      $consulta = "SELECT g.cod_grupo as cod, COUNT(er.corrigida) as num FROM Grupos as g, Exercicios_resolucao as er WHERE er.corrigida='S' and er.submetida='S' and er.status!='R' and g.cod_grupo = er.cod_grupo and er.cod_usuario IS NULL GROUP BY er.cod_grupo ORDER BY er.cod_usuario";
    else
      $consulta = "SELECT g.cod_grupo as cod, COUNT(er.corrigida) as num FROM Grupos as g INNER JOIN Grupos_usuario as gu ON g.cod_grupo=gu.cod_grupo, Exercicios_resolucao as er WHERE (gu.cod_usuario = ".$cod_usuario." or er.compartilhada='T') and er.corrigida='S' and er.submetida='S' and g.cod_grupo = er.cod_grupo and er.cod_usuario IS NULL GROUP BY er.cod_grupo ORDER BY er.cod_usuario";
  }
  else if($agrupamento == "T")
  {
    if($visualizar == "I")
      $consulta = "SELECT er.cod_exercicio as cod, COUNT(er.corrigida) as num FROM Exercicios_resolucao as er WHERE er.submetida='S' AND er.corrigida='S' AND er.cod_grupo IS NULL GROUP BY er.cod_exercicio ORDER BY er.cod_exercicio";
    else if($visualizar == "G")
      $consulta = "SELECT er.cod_exercicio as cod, COUNT(er.corrigida) as num FROM Exercicios_resolucao as er WHERE er.submetida='S' AND er.corrigida='S' AND er.cod_usuario IS NULL GROUP BY er.cod_exercicio ORDER BY er.cod_exercicio";
  }
  
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}

/***********************************************************************
   RetornaNumExercicios - Retorna o numero de exercicios
   Entrada: $sock - BASE DO CURSO
            $visualizar - opcao de visualizacao (I - individual ou G - grupo)
   Saida: Numero de exercicios         
*/
function RetornaNumExercicios($sock, $visualizar){
	
	$query = 'select COUNT(cod_exercicio) as cod from Exercicios_aplicado where tp_aplicacao = \''.VerificaStringQuery($visualizar).'\'';
	$res = Enviar($sock, $query);
	$linha = RetornaLinha($res);
	return (int) $linha[0];
	
}

/***********************************************************************
   RetornaCodGrupoUsuario - Retorna o codigo do grupo o qual o usuario pertence
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - código do usuário
   Saida: código do grupo o qual pertence o usuário         
*/
function RetornaCodGrupoUsuario($sock,$cod_usuario)
{
  $data = time();
  $consulta="select cod_grupo from Grupos_usuario where cod_usuario = ".$cod_usuario;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return $linha['cod_grupo'];
}

/***********************************************************************
   RetornaExerciciosDisponiveis - Retorna os exercicios que foram disponibilizados
   Entrada: $sock - BASE DO CURSO
   Saida: Saida: array ['cod_topico'] - codigo do topico
                       ['topico'] - nome do topico            
*/
function RetornaExerciciosDisponiveis($sock)
{
  $data = time();
  $consulta="select cod_exercicio,tp_aplicacao from Exercicios_aplicado where dt_disponibilizacao <= ".$data." and dt_limite_submissao > ".$data;
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}

/***********************************************************************
   RetornaExerciciosAplicados - Retorna os exercicios que foram aplicados
   Entrada: $sock - BASE DO CURSO
   Saida: Saida: [array][campos] = 'valores'           
*/
function RetornaExerciciosAplicados($sock)
{
  $data = time();
  $consulta="select * from Exercicios_aplicado";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return $lista;
}

/***********************************************************************
   InsereTuplaResolucao - Insere uma nova tupla na tabela Exercicios_resolucao 
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - código do usuario
            $cod_exercicio - código do exercicio
            $tp_aplicacao - 'I' ou 'G', indicando se individual ou em grupo
   Saida:           
*/
function InsereTuplaResolucao($sock,$cod_usuario,$cod_exercicio,$tp_aplicacao,$cod_curso)
{
	
    $cod_avaliacao = RetornaCodAvaliacao($sock,$cod_exercicio);

    /* Pega quem fez o Exercicio, para colocar como quem avaliou, nas Avaliacoes */
    $consulta = "select cod_usuario from Exercicios_modelo where cod_exercicio = ".$cod_exercicio;
    $res = Enviar($sock, $consulta);
    $cod_formador = RetornaLinha($res);
    $cod_formador = $cod_formador[0];
    MudarDB($sock,"");
    $cod_formador = RetornaCodigoUsuarioGlobal($sock,$cod_formador,$cod_curso);
    MudarDB($sock,$cod_curso);
    
    /* Aplicacao Individual */
    if ($tp_aplicacao == 'I')
    {
      $consulta = "insert into Exercicios_resolucao (cod_exercicio,cod_usuario,nota,submetida,corrigida,compartilhada,status) values ";
      $consulta .= "(".$cod_exercicio.",".$cod_usuario.",0,'N','N','F','A')";
      $res=Enviar($sock, $consulta);
      
      $cod_resolucao = mysql_insert_id();
        
      if($cod_avaliacao > 0)
      {
        $cod_nota = RetornaProximoCodigo($sock, 'Avaliacao_notas');
        $consulta =
                    "insert into Avaliacao_notas ("
                    ."cod_nota, cod_aluno, cod_grupo, cod_avaliacao, cod_formador, "
                    ."comentario, nota, status, data, tipo_compartilhamento, "
                    ."data_alteracao)"
                    ." values "
                    ."(".$cod_nota.", ".$cod_usuario.",'',"
                    ."".$cod_avaliacao.",".$cod_formador.","
                    ."'', '0', 'A', ".time().",'A',".time().")"; 
        $res = Enviar($sock, $consulta);
      }
    }
    else /* Aplicacao em grupo */
    {
      		
      $cod_grupo = RetornaCodGrupoUsuario($sock,$cod_usuario);
      if($cod_grupo != null)
      {
        $consulta = "insert into Exercicios_resolucao (cod_exercicio,cod_grupo,nota,submetida,corrigida,compartilhada,status) values  ";
        $consulta .= "(".$cod_exercicio.",".$cod_grupo.",0,'N','N','F','A')";
        $res = Enviar($sock, $consulta);
      
        $cod_resolucao = mysql_insert_id();

        if($cod_avaliacao > 0)
        {
          $consulta = "select cod_usuario from Grupos_usuario where cod_grupo = ".$cod_grupo;
          $res = Enviar($sock, $consulta);
          $usuarios_grupo = RetornaArrayLinhas($res);
          foreach($usuarios_grupo as $cod => $usuario_grupo)
          {
            $cod_nota = RetornaProximoCodigo($sock, 'Avaliacao_notas');
            $consulta =
                        "insert into Avaliacao_notas (cod_nota, cod_aluno, cod_grupo, cod_avaliacao,"
                        ."cod_formador, comentario, nota, status, data,"
                        ." tipo_compartilhamento,data_alteracao)"
                        ." values "
                        ."(".$cod_nota.",".$usuario_grupo['cod_usuario'].",".$cod_grupo.","
                        ."".$cod_avaliacao.",".$cod_formador.",'','0'"
                        .", 'A', ".time().",'A',".time().")";
            $res = Enviar($sock, $consulta);
          }
        }
      }
    }

    if (!$res)
      return false;
    else
      return $cod_resolucao;
}

/***********************************************************************
   FoiAplicado - Verifica se o exercicio ja possui uma resolucao, ou seja, foi aplicado ao usuario (grupo) 
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - código do usuario
            $cod_exercicio - código do exercicio
            $tp_aplicacao - 'I' ou 'G', indicando se individual ou em grupo
   Saida: true caso o exercicio tenha sido aplicado, false caso contrario           
*/
function FoiAplicado($sock,$cod_usuario,$cod_exercicio,$tp_aplicacao)
{
  if($tp_aplicacao == "I")
  {
    $consulta = "select * from Exercicios_resolucao where cod_exercicio = ".$cod_exercicio." and cod_usuario = ".$cod_usuario;
    $res = Enviar($sock, $consulta);
  }
  else
  {
  	
    $cod_grupo = RetornaCodGrupoUsuario($sock,$cod_usuario);
    if($cod_grupo==null)
    	$cod_grupo=-1;
    $consulta = "select * from Exercicios_resolucao where cod_exercicio = ".$cod_exercicio." and cod_grupo = ".$cod_grupo;
    $res = Enviar($sock, $consulta);
  }
  
  $linha = RetornaArrayLinhas($res);
  
  if($linha != null && count($linha) > 0)
  	return true;
  else
    return false;
}

/***********************************************************************
   AplicaExerciciosAoUsuario - 
   Entrada: $sock - BASE DO CURSO
   Saida: Saida: array ['cod_topico'] - codigo do topico
                       ['topico'] - nome do topico            
*/
function AplicaExerciciosAoUsuario($sock,$cod_curso,$cod_usuario)
{
  $aplicados = RetornaExerciciosAplicados($sock);
  
  foreach($aplicados as $cod => $linha) 
  {
    if(!FoiAplicado($sock,$cod_usuario,$linha['cod_exercicio'],$linha['tp_aplicacao']))
      /*Cria uma tupla na tabela Exercicios_resolucao*/
      InsereTuplaResolucao($sock,$cod_usuario,$linha['cod_exercicio'],$linha['tp_aplicacao'],$cod_curso);
  }
}

/***********************************************************************
   CancelaResolucaoDoExercicio - Exclui as resolucoes (e afins) referentes a um exercicio
   Entrada: $sock - BASE DO CURSO
            $cod_exercicio - codigo do exercicio
   Saida: void    
*/
function CancelaResolucaoDoExercicio($sock,$cod_exercicio)
{
  $consulta = "select cod_resolucao from Exercicios_resolucao where cod_exercicio = ".$cod_exercicio;
  $res = Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  
  $consulta = "delete from Exercicios_resolucao where cod_exercicio = ".$cod_exercicio;
  $res = Enviar($sock, $consulta);
  
  if (!empty($linha['cod_resolucao'])){
  $consulta = "delete from Exercicios_resolucao_questao_obj where cod_resolucao = ".$linha['cod_resolucao'];
  $res = Enviar($sock, $consulta);
  
  $consulta = "delete from Exercicios_resolucao_questao_mult where cod_resolucao = ".$linha['cod_resolucao'];
  $res = Enviar($sock, $consulta);
  
  $consulta = "delete from Exercicios_resolucao_questao_diss where cod_resolucao = ".$linha['cod_resolucao'];
  $res = Enviar($sock, $consulta);
  
  $consulta = "delete from Exercicios_resolucao_questao_comentarios where cod_resolucao = ".$linha['cod_resolucao'];
  $res = Enviar($sock, $consulta);
  }
}

/***********************************************************************
   RetornaRespostaQuestao - Retorna a resposta da questao
   Entrada: $sock - BASE DO CURSO
            $cod_resolucao - codigo do resolucao
            $cod_questao - codigo da questao
            $tp_questao - tipo da questao
   Saida: a reposta da questao, null caso nao tenha sido respondida
*/
function RetornaRespostaQuestao($sock,$cod_resolucao,$cod_questao,$tp_questao)
{


  if($tp_questao == 'O')
    $consulta = "select resposta from Exercicios_resolucao_questao_obj where cod_resolucao = ".$cod_resolucao." and cod_questao = ".$cod_questao;
  else if($tp_questao == 'M')
  	$consulta = "select resposta from Exercicios_resolucao_questao_mult where cod_resolucao = ".$cod_resolucao." and cod_questao = ".$cod_questao;
  else if($tp_questao == 'D')
    $consulta = "select resposta from Exercicios_resolucao_questao_diss where cod_resolucao = ".$cod_resolucao." and cod_questao = ".$cod_questao;
      
  $res = Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  
  return $linha['resposta'];
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
          $lista[<num>]['Data'] - data da ultima modifição
*/
function RetornaArquivosQuestao($cod_curso, $dirlink)
{
  return (RetornaArrayDiretorioSimples($dirlink));
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
            $cod_item - chave primária da questao ou do exercicio
            $diretorio_arquivos - diretorio dos arquivos do TelEduc
            $diretorio_temp - diretorio dos arquivos do TelEduc
            $pasta - pasta para qual sera criada o link (exercicio ou questao)
   Saida: caminho relativo
*/
function CriaLinkVisualizar($sock,$cod_curso,$cod_usuario,$cod_item,$diretorio_arquivos,$diretorio_temp,$pasta)
{
  /* Busca Arquivo a ser mostrado */
  $caminho = "";
  unset ($caminho);
  
  if($pasta == "questao")
    $dir=$diretorio_arquivos."/".$cod_curso."/exercicios/questao/".$cod_item."/";
  else if($pasta == "exercicio")
    $dir=$diretorio_arquivos."/".$cod_curso."/exercicios/exercicio/".$cod_item."/";
    
  /* Cria link simb?ico (apaga antigo, se houver)*/
  if ($cod_usuario<0)
    $cod_usuario=0;

  if($pasta == "questao")
  {  
    $dirlink=$diretorio_temp."/questoes_".$cod_curso."_".$cod_item."_".$cod_usuario;
    $dirlinkpath="../../diretorio/questoes_".$cod_curso."_".$cod_item."_".$cod_usuario;
  }
  else if($pasta == "exercicio")
  {
    $dirlink=$diretorio_temp."/exercicios_".$cod_curso."_".$cod_item."_".$cod_usuario;
    $dirlinkpath="../../diretorio/exercicios_".$cod_curso."_".$cod_item."_".$cod_usuario;
  }

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
   RetornaQuestoesLixeira - Retorna array com as questoes que estao na lixeira (L), ou seja, que não são válidas (V)
							e que não foram excluídas (X).
   Entrada: $sock - BASE DO CURSO
   Saida: array com []['cod_questao'] - chave primaria ou codigo da questao
                    []['cod_usuario'] - codigo do usuario que criou a questao 
                    []['cod_topico'] - codigo do topico ao qual a questao pertence
                    []['titulo'] - titulo da questao
                    []['tp_questao'] - campo que indica qual o tipo da questao (Dissertativa ou Objetiva)
                    []['data'] - data em que a questao foi criada
*/
function RetornaQuestoesLixeira($sock)
{
  $consulta="select cod_questao, cod_usuario, cod_topico, titulo, tp_questao, data from Exercicios_questao where status='L'";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  return($lista);
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
   EditarAlternativaObjDinamic - Salva no banco as altera��es feitas na alternativa de uma questao objetiva
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
			$cod_alternativa - codifo da alternativa
			$textoAlt - texto da alternativa
			$gabarito - gabarito da questao
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
   EditarAlternativaMultDinamic - Salva no banco as altera��es feitas na alternativa de uma questao multipla escolha
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
			$cod_alternativa - codifo da alternativa
			$textoAlt - texto da alternativa
			$gabarito - gabarito da questao
   Saida: XML da função Ajax
*/
function EditarAlternativaMultDinamic($cod_curso, $cod_questao, $cod_alternativa, $textoAlt, $gabarito)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao_alternativa set texto='".VerificaStringQuery($textoAlt)."' where cod_alternativa=".VerificaNumeroQuery($cod_alternativa);
  $res=Enviar($sock, $consulta);

  $consulta="update Exercicios_questao_mult set gabarito='".VerificaStringQuery($gabarito)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);
  
  Desconectar($sock);
  
  return $objResponse->getXML();
}

/* *********************************************************************
   EditarAlternativaDissDinamic - Salva no banco as alterações feitas na alterntiva de uma questao dissertativa
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
/***********************************************************************
		MudaTipoQuestaoDinamic - Muda o tipo de uma questao dinamicamente
		Entrada: $sock - BASE DO CURSO
				 $cod_curso - codigo do curso ao qual pertence a questao
            	 $cod_usuario - codigo do usuario que criou a questao
            	 $cod_questao - codigo da questao
            	 $novo_tipo - novo tipo da questao	
 		Saida: XML da função Ajax
*/
function MudaTipoQuestaoDinamic($cod_curso,$cod_usuario,$cod_questao,$novo_tipo){
	$objResponse = new xajaxResponse('ISO-8859-1');
	$sock=Conectar($cod_curso);
	$query="update Exercicios_questao set tp_questao='".VerificaStringQuery($novo_tipo)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
	$res=Enviar($sock,$query);
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

  $consulta="select max(posicao_item) from Exercicios_questao_alternativa where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);

  $posicao = $linha[0]+1;

  $consulta="insert into Exercicios_questao_alternativa (cod_questao,posicao_item) values (".VerificaNumeroQuery($cod_questao).",".$posicao.")";
  $res=Enviar($sock, $consulta);

  $cod_alternativa = mysql_insert_id();

  if($tp_questao == 'O') {
  	$gabarito = RetornaGabaritoQuestaoObj($sock, $cod_questao).'0';
  	$consulta="insert into Exercicios_questao_obj (cod_questao,gabarito,tipo) values (".$cod_questao.",0,'O') ON duplicate KEY UPDATE gabarito='".VerificaStringQuery($gabarito)."'";
  	$res=Enviar($sock, $consulta);
  	$cod = $cod_alternativa;
  } elseif ($tp_questao == 'M') {
  	$gabarito = RetornaGabaritoQuestaoMult($sock, $cod_questao).'0';
  	$consulta="insert into Exercicios_questao_mult (cod_questao,gabarito,tipo) values (".$cod_questao.",0,'M') ON duplicate KEY UPDATE gabarito='".VerificaStringQuery($gabarito)."'";
  	$res=Enviar($sock, $consulta);
  	$cod = $cod_alternativa;
  } else {
  	$consulta="insert into Exercicios_questao_diss (cod_questao,cod_alternativa) values (".VerificaNumeroQuery($cod_questao).",".VerificaNumeroQuery($cod_alternativa).")";
  	$res=Enviar($sock, $consulta);
  	$cod = $cod_questao.$cod_alternativa;
  }

  $consulta="insert into Exercicios_questao_historico values (".VerificaNumeroQuery($cod_questao).", ".VerificaNumeroQuery($cod_usuario).", ".time().", 'A')";
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  $objResponse->addScript("AdicionarAlternativa(".$cod.");");

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
function ApagarAlternativaDinamic($cod_curso, $cod_usuario, $cod_questao, $cod_alternativa, $tp_questao)
{
  $objResponse = new xajaxResponse('ISO-8859-1');
  
  $sock=Conectar($cod_curso);
  $consulta="delete from Exercicios_questao_alternativa where cod_alternativa=".VerificaNumeroQuery($cod_alternativa);
  $res=Enviar($sock, $consulta);

  if($tp_questao == 'D')
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
			$textoFeedback - feedback para o usuario
   Saida: XML da função Ajax
*/
function AtualizarNivelDinamic($cod_curso, $cod_questao, $nivel, $textoFeedback)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao set nivel='".VerificaStringQuery($nivel)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);
  
  $objResponse->addScriptCall("mostraFeedback", $textoFeedback, 'true');

  return $objResponse->getXML();
}

/* *********************************************************************
   AtualizarTopicoDinamic - Atualiza o topico da questao
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
            $cod_topico - codigo do novo topico da questao
			$textoFeedback - feedback para o usuario
   Saida: XML da função Ajax
*/
function AtualizarTopicoDinamic($cod_curso, $cod_questao, $cod_topico, $textoFeedback)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao set cod_topico='".VerificaNumeroQuery($cod_topico)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);
  
  $objResponse->addScriptCall("mostraFeedback", $textoFeedback, 'true');

  return $objResponse->getXML();
}

/* *********************************************************************
   CriaNovoTopicoDinamic - Cria um novo topico
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
            $novo_topico - nome do topico
			$textoFeedback - feedback para o usuario
   Saida: XML da função Ajax
*/
function CriaNovoTopicoDinamic($cod_curso, $cod_questao, $novo_topico, $textoFeedback)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="insert into Exercicios_topico (topico) values ('".VerificaStringQuery($novo_topico)."')";
  $res=Enviar($sock, $consulta);

  $cod_topico = mysql_insert_id();
  
  $consulta="update Exercicios_questao set cod_topico='".VerificaNumeroQuery($cod_topico)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  $objResponse->addScript("AdicionaNovoTopico(".$cod_topico.",'".$novo_topico."');");
  $objResponse->addScriptCall("mostraFeedback", $textoFeedback, 'true');
  $objResponse->addScriptCall("AtualizaTopicoCriado", $cod_topico);

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

  $objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   EditarTituloExercicioDinamic - Edita o título do exercicio
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_exercicio - codigo do exercicio
            $novo_nome - novo título do exercicio
            $cod_usuario - código do usuário que está utilizando
            $texto - mensagem de feedback
   Saida: XML da função Ajax
*/
function EditarTituloExercicioDinamic($cod_curso, $cod_exercicio, $novo_nome, $cod_usuario, $texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);
  $data = time();
  
  // Atualizando tabela Exercicios_modelo, atualizando titulo e data (referente a altera��o)
  $query = "update Exercicios_modelo set titulo='".VerificaStringQuery(htmlentities($novo_nome))."', ";
  $query.= "data=".$data." where cod_exercicio=".VerificaNumeroQuery($cod_exercicio);
  $res = Enviar($sock, $query);

  // Inserindo altera��o na tabela Exercicios_modelo_historico
  marcaLogExerciciosModeloHistorico($sock, $cod_exercicio, $cod_usuario, 'E');
  Desconectar($sock);

  // Imprime no div valores do formul?io
  $objResponse->addAssign("tit_".$cod_exercicio, "innerHTML", htmlentities($novo_nome));
  $objResponse->addEvent("renomear_".$cod_exercicio, "onclick", "AlteraTitulo('".$cod_exercicio."');");

  //AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->addScriptCall("mostraFeedback", $texto, 'true');

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
function EditarEnunciadoDinamic($cod_curso, $cod_questao, $novo_nome, $cod_usuario, $feedback)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $novo_nome=ConverteAspas2BarraAspas($novo_nome);
  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao set enunciado='".VerificaStringQuery(trim(VerificaTexto($novo_nome)))."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  //AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  if($feedback != " "){
	  $objResponse->addScriptCall("mostraFeedback", $feedback, 'true');
  }
  // Imprime no div valores do formulário
  $objResponse->addAssign("text_".$cod_questao, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas(VerificaTexto($novo_nome))), true));


  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   EditarTextoExercicioDinamic - Edita o texto do exercicio
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_exercicio - codigo da questao
            $novo_texto - novo texto do exercicio
            $cod_usuario - código do usuário que está utilizando
            $feedback - mensagem de feedback
   Saida: XML da função Ajax
*/
function EditarTextoExercicioDinamic($cod_curso, $cod_exercicio, $novo_texto, $cod_usuario, $feedback)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $novo_texto=ConverteAspas2BarraAspas($novo_texto);
  $sock=Conectar($cod_curso);
  $data = time();
  
  // Atualizando tabela Exercicios_modelo, atualizando titulo e data (referente a altera��o)
  $query = "update Exercicios_modelo set texto='".VerificaStringQuery(trim(VerificaTexto($novo_texto)))."', ";
  $query.= "data=".$data." where cod_exercicio=".VerificaNumeroQuery($cod_exercicio);
  $res=Enviar($sock, $query);

  // Inserindo altera��o na tabela Exercicios_modelo_historico
  marcaLogExerciciosModeloHistorico($sock, $cod_exercicio, $cod_usuario, 'E');
  Desconectar($sock);

  //AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->addScriptCall("mostraFeedback", $feedback, 'true');

  // Imprime no div valores do formulário
  $objResponse->addAssign("text_".$cod_exercicio, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas(VerificaTexto($novo_texto))), true));


  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   EditarGabaritoQuestaoDissDinamic - Salva no banco as alteracoes feitas no gabarito de uma de uma questao 
                                      dissertativa
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_questao - codigo da questao
            $cod_alternativa - codigo da alternativa
            $novo_nome - novo gabarito da alternativa
            $cod_usuario - código do usuário que está utilizando
            $texto - mensagem de feedback
   Saida: XML da função Ajax
*/
function EditarGabaritoQuestaoDissDinamic($cod_curso, $cod_questao, $texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  //$novo_nome=ConverteAspas2BarraAspas($novo_nome);
  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao_diss set gabarito='".$texto."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  //AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  //$objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  // Imprime no div valores do formulário
  $objResponse->addAssign("texto_".$cod_questao, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas(VerificaTexto($texto))), true));

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   EditarGabaritoQuestaoObjDinamic - Salva no banco as alteracoes feitas no gabarito de uma de uma questao 
                                      objetiva
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
			$gabarito - gabarito da questao
   Saida: XML da função Ajax
*/
function EditarGabaritoQuestaoObjDinamic($cod_curso, $cod_questao, $gabarito)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao_obj set gabarito='".VerificaStringQuery($gabarito)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  return $objResponse->getXML();
}

/* *********************************************************************
   EditarGabaritoQuestaoMultDinamic - Salva no banco as alteracoes feitas no gabarito de uma de uma questao 
                                      multipla escolha
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
			$gabarito - gabarito da questao
   Saida: XML da função Ajax
*/
function EditarGabaritoQuestaoMultDinamic($cod_curso, $cod_questao, $gabarito)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  $consulta="update Exercicios_questao_mult set gabarito='".VerificaStringQuery($gabarito)."' where cod_questao=".VerificaNumeroQuery($cod_questao);
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  return $objResponse->getXML();
}

/* *********************************************************************
   EntregaExercicio
   Entrada: $sock - BASE DO CURSO
            $cod_resolucao - codigo da resolucao a ser entregue
   Saida: void
*/
function EntregaExercicio($sock, $cod_resolucao,$cod_usuario){
	$data = time();
	$query = "update Exercicios_resolucao set submetida = 'S', dt_submissao = ".$data;
	$query.= "  where cod_resolucao = ".VerificaNumeroQuery($cod_resolucao);
	Enviar($sock, $query);
	
	// Marca no log das resolu��es, na tabela Exercicio_resolucao_historico
    // o evento de ter entregue o exercicio
    marcaLogExerciciosResolucaoHistorico($sock,$cod_resolucao, 'S',$cod_usuario);
    return;	
}

/* *********************************************************************
   ExcluiArquivoDinamic - Exclui o arquivo, dinamicamente
   Entrada: $numero - numero do arquivo, no item
            $arq - caminho do arquivo no servidor
            $cod_curso - codigo do curso
            $cod_item - codigo do item
            $cod_usuario - codigo do usuario
			$texto - texto de feedback
   Saida: XML da função Ajax
*/
function ExcluiArquivoDinamic($numero, $arq, $cod_curso, $cod_item, $cod_usuario, $texto){
  //obs: cod_item = cod_exercicio
  
  $objResponse = new xajaxResponse('ISO-8859-1');
  
  $arq=htmlspecialchars_decode($arq);
  
  if(RemoveDiretorio($arq)) {	/* Se nao ocorreu nenhum erro ao remover o diretorio */
	$sock=Conectar($cod_curso);
	$data = time();
  
	// Alterando o status e a data (referente a modifica��o feita = recuperada)
	$query = "update Exercicios_modelo set data = ".$data." ";
	$query.= "where cod_exercicio = ".$cod_item;
	Enviar($sock,$query);
    
  
	// Inserindo altera��o na tabela Exercicios_modelo_historico
	marcaLogExerciciosModeloHistorico($sock, $cod_item, $cod_usuario, 'N');
	Desconectar($sock);
	
	$atualizacao = 'true';
	$objResponse->addRemove('trArq_'.$numero);
	
	//AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);
  } else {						/* Se ocorreu algum erro ao remover o diretorio */
  	$atualizacao = 'false';
  	/* Frase #209 - Erro ao apagar arquivo. */
	$sock=Conectar("");
	$texto = RetornaFraseDaLista(RetornaListaDeFrases($sock,23), 209);
	Desconectar($sock);
  }

  $objResponse->addScriptCall("mostraFeedback", $texto, $atualizacao);
	
  return $objResponse->getXML();
}

/***********************************************************************
   ExibeArquivoAnexadoDinamic - Insere  na pagina os dados do arquivo anexado
   Entrada: $cod_curso - codigo do curso
            $cod_questao - codigo da questao
			$cod_usuario - codigo do usuario
            $arqNum - numero do arquivo
			$pasta - pasta onde se encontra o arquivo
			$nomeArq - nome do arquivo
			$textoFeedback - texto de feedback
   Saida: XML da função Ajax
*/
function ExibeArquivoAnexadoDinamic($cod_curso, $cod_questao, $cod_usuario, $arqNum, $pasta, $nomeArq, $textoFeedback){

  $objResponse = new xajaxResponse('ISO-8859-1');
  
  $caminho = $pasta.$nomeArq;
  $tamanho = round((filesize($caminho) / 1024), 2);
  $data = filemtime($caminho);
  
  if($tamanho >= 0)
  {
  	$objResponse->addScript("InsereLinhaArq('".$nomeArq."',".$tamanho.",".$arqNum.",'".$caminho."','".unixTime2DataHora($data)."','arquivo');");
  }
  else
  {
    $objResponse->addRemove('arq_'.$arqNum);
    $objResponse->addScript("LimpaBarraArq();");
  	$objResponse->addScriptCall("mostraFeedback", "Erro ao anexar arquivo.", 'false');
  }
  
  return $objResponse->getXML();
}

/***********************************************************************
   RetornaArquivosPastaDinamic - Retorna um diretorio
   Entrada: $caminho - caminho do diretorio a ser aberto
   Saida: XML da função Ajax
*/
function RetornaArquivosDiretorioDinamic($cod_curso,$cod_usuario,$caminho){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock = Conectar($cod_curso);
  $eformador = EFormadorMesmo($sock,$cod_curso,$cod_usuario);
  Desconectar($sock);
  
  $cont = 0;
  $diretorio = RetornaArrayDiretorioSimples($caminho);
  
  foreach ($diretorio as $cod => $linha)
  {
    if (!($linha['Arquivo'] == "") && $linha['Diretorio'] == "")
    {
      $nome = $linha['Arquivo'];
      $tipo = "arquivo";
    }
    else
    {
      $nome = $linha['Diretorio'];
      $tipo = "pasta";
    }
    
    $tamanho = $linha['Tamanho'];
    $data = unixTime2DataHora($linha['Data']);
    
    $nome = ConverteAspas2BarraAspas($nome);
    $linha['Caminho'] = ConverteAspas2BarraAspas($linha['Caminho']);
    
    if(!($linha['Status'] == true && !$eformador))
    {
      $cont++;
      $objResponse->addScript("NovoArquivo('".$nome."',".round(($tamanho/1024),2).",'".$tipo."','".$data."','".$linha['Caminho']."','".$linha['Status']."',".$cod.");");
    }
  }
  
  $objResponse->addScript("ExibeDiretorio(".$cont.");\n");
  
  return $objResponse->getXML();
}

/**********************************************************************
   VerificaExistenciaArquivoDinamic - Verifica se ja existe um arquivo com o nome passado como parametro
   Entrada: $cod_curso - codigo do curso
            $cod_questao - codigo da questao
			$cod_item - chave primária da questao ou do exercicio
			$pasta - pasta onde procurar o arquivo
			$nomeArq - nome do arquivo
   Saida: XML da função Ajax
*/
function VerificaExistenciaArquivoDinamic($cod_curso, $cod_item, $cod_usuario, $pasta, $nomeArq){

  $objResponse = new xajaxResponse('ISO-8859-1');
  
  if(file_exists($pasta.$nomeArq))
  	$flag = 1;
  else
  	$flag = 0;
  
  $objResponse->addScript("VerificaUpload('".$nomeArq."',$flag);");

  return $objResponse->getXML();
}

/**********************************************************************
   DescompactarArquivoDinamic - Descompacta arquivo
   Entrada: $pasta - pasta em que se encontra o arquivo
			$nomeArq - nome do arquivo
   Saida: XML da função Ajax
*/
function DescompactarArquivoDinamic($pasta, $nomeArq){

  $sock=Conectar("");
	
  $objResponse = new xajaxResponse('ISO-8859-1');
  
  $data = time();
  
  $caminho = $pasta.$nomeArq;
  $diretorio_antes = RetornaArrayDiretorioSimples($pasta);
  
  $flag = DescompactarArquivoZip($caminho,$pasta);
  
  if($flag) {				/* Se conseguiu descompactar o arquivo */
  	
  	RemoveArquivo($caminho);

  	$diretorio_depois = RetornaArrayDiretorioSimples($pasta);

  	foreach ($diretorio_depois as $cod => $linha)
  	{
  		foreach ($diretorio_antes as $cod1 => $linha1)
  		{
  			if($linha['Diretorio'] == "" && $linha['Arquivo'] == $linha1['Arquivo'])
  			break;
  			else if($linha['Diretorio'] == $linha1['Diretorio'])
  			break;
  		}

  		ModificaDataArquivo($linha['Caminho'],$data);
  	}

  	/* Frase #210 - Arquivo descompactado com sucesso. */
	$texto = RetornaFraseDaLista(RetornaListaDeFrases($sock,23), 210);
  	$atualizacao = "true";
  
  } else {				/* Se nao conseguiu descompactar o arquivo */

  	/* Frase #211 - Erro ao descompactar arquivo. */
  	$texto = RetornaFraseDaLista(RetornaListaDeFrases($sock,23), 211);
  	$atualizacao = "false";
  	
  }

  Desconectar($sock);

  $objResponse->addScriptCall("mostraFeedback", $texto, $atualizacao);
  	
  return $objResponse->getXML();
}

/**********************************************************************
   MudaStatusArquivos - Muda status dos arquivos
   Entrada: $arrayArq - array que contem os caminhos dos arquivos a serem adicionados ao pacote
   Saida: void
*/
function MudaStatusArquivos($arrayArq)
{
  foreach ($arrayArq as $cod => $linha)
  {
  	if(is_dir($linha))
  	{
  	  $dir = RetornaArrayDiretorioSimples($linha);
  	  foreach($dir as $cod1 => $linhaNext)
  	  {
  	    $arrayArqNext[$cod1] = $linhaNext['Caminho'];
  	  }
  	  MudaStatusArquivos($arrayArqNext);
  	}
  	
    AlteraStatusArquivo($linha,!RetornaStatusArquivo($linha));
  }
}

/**********************************************************************
   MudaStatusArquivosDinamic - Muda status dos arquivos
   Entrada: $arrayArq - array que contem os caminhos dos arquivos a serem adicionados ao pacote
            $texto - texto de feedback
   Saida: XML da função Ajax
*/
function MudaStatusArquivosDinamic($arrayArq,$texto)
{
  $objResponse = new xajaxResponse('ISO-8859-1');
  
  MudaStatusArquivos($arrayArq);
  $objResponse->addScript("AbrePasta(pastaAtual);");
  $objResponse->addScript("mostraFeedback(\"".$texto."\",true);");
  
  return $objResponse->getXML();
}

/********************************************************************************************************
  AtualizaPosicoesDasAlternativasDinamic - Atualiza posicoes das alternativas
  Entrada: $cod_curso - codigo do curso
           $cod_usuario - codigo do usuario
           $lista_ids - lista dos ids
		   $tp_questao - tipo da questao
  Saida: XML da funcao ajax
********************************************************************************************************/
function AtualizaPosicoesDasAlternativasDinamic($cod_curso, $cod_usuario, $lista_ids, $tp_questao){
  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);
  PegaSemaforo($sock, "Exercicios");

  $string = "";
  foreach($lista_ids as $cod => $linha){
    $vetor = split('_', $linha);
    $query = "update Exercicios_questao_alternativa set posicao_item = ".($cod+1)." where cod_alternativa=".$vetor[1];
    Enviar($sock, $query);
    if($tp_questao == 'O' || $tp_questao == 'M')
    	$string = $string.$vetor[1].",";
  }
  
  if($tp_questao == 'O' || $tp_questao == 'M')
    $objResponse->addScript("AtualizarMudancaPosicoes(\"".$string."\");");

  LiberaSemaforo($sock, "Exercicios");
  Desconectar($sock);
  return $objResponse->getXML();
}

/********************************************************************************************************
  AlteraStatusQuestaoDinamic - Altera o status da questao
  Entrada: $cod_curso - codigo do curso
           $cod_questao - codigo da questao
		   $status - status da questao (V - valida; L - lixeira;X - excluida definitivamente)	
  Saida: XML da funcao ajax
********************************************************************************************************/
function AlteraStatusQuestaoDinamic($cod_curso, $cod_questao, $status)
{ 
    $objResponse = new xajaxResponse('ISO-8859-1');
 
    $sock=Conectar($cod_curso);
    $query = "update Exercicios_questao set status = '".$status."' where cod_questao = ".$cod_questao;
    Enviar($sock,$query);
    Desconectar($sock);
 
    return $objResponse->getXML();
}

function AlteraStatusExercicioInternoDinamic($cod_usuario, $cod_curso, $cod_exercicio, $status)
{ 
    $objResponse = new xajaxResponse('ISO-8859-1');
    
    $sock=Conectar($cod_curso);
    $data = time();
    
    // Alterando o status e a data (referente a modifica��o feita = recuperada)
    $query = "update Exercicios_modelo set status = '".$status."', ";
    $query.= "data = ".$data." where cod_exercicio = ".$cod_exercicio;
    Enviar($sock,$query);
    
   	// Inserindo altera��o na tabela Exercicios_modelo_historico
   	marcaLogExerciciosModeloHistorico($sock, $cod_exercicio, $cod_usuario, $status);;
    Desconectar($sock);

 	$objResponse->addRedirect("exercicios.php?cod_curso=".$cod_curso."&visualizar=E");

    return $objResponse->getXML();
}


/********************************************************************************************************
  AlteraStatusExercicioDinamic - Altera o status de um exercicio
  Entrada: $cod_curso - codigo do curso
           $cod_exercicio - codigo do exercicio
		   $status - status da questao (V - valido; L - lixeira;X - excluido definitivamente)	
  Saida: XML da funcao ajax
********************************************************************************************************/
function AlteraStatusExercicioDinamic($cod_usuario, $cod_curso, $cod_exercicio, $status)
{ 
    $objResponse = new xajaxResponse('ISO-8859-1');
 
    $sock=Conectar($cod_curso);
 	$data = time();
    
 	// Alterando o status e a data (referente a modifica��o feita = recuperada)
    $query = "update Exercicios_modelo set status = '".$status."', ";
    $query.= "data = ".$data." where cod_exercicio = ".$cod_exercicio;
    Enviar($sock,$query);
    
  	// Inserindo altera��o na tabela Exercicios_modelo_historico
  	marcaLogExerciciosModeloHistorico($sock, $cod_exercicio, $cod_usuario, $status);
    Desconectar($sock);
 
    return $objResponse->getXML();
}

/* *********************************************************************
   MudarCompartilhamentoDinamic - Muda o compartilhamento do item dado
   Entrada: $sock - BASE DO CURSO
            $dadosForm - dados do formulario para mudar compartilhamento
            $novo_comp - novo tipo de compartilhamento
			$op - indica se a mudanca e para uma questao ou para um exercicio
   Saida: nenhuma
*/
function MudarCompartilhamentoDinamic($dadosForm, $novo_comp, $op)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $tipo_comp=$dadosForm['tipo_comp'];
  $cod_item=$dadosForm['cod_item'];
  $cod_curso=$dadosForm['cod_curso'];
  $cod_usuario=$dadosForm['cod_usuario'];
  $texto = $dadosForm['texto'];

  $sock=Conectar($cod_curso);
  if ($op == 'E')
  {
    $consulta="update Exercicios_modelo set tipo_compartilhamento='".$tipo_comp."', data=".time()." where cod_exercicio=".$cod_item;
    $res=Enviar($sock, $consulta);
    marcaLogExerciciosModeloHistorico($sock, $cod_item, $cod_usuario, 'E');
  }
  else if($op == 'Q')
  {
    $consulta="update Exercicios_questao set tipo_compartilhamento='".$tipo_comp."', data=".time()." where cod_questao=".$cod_item;
    $res=Enviar($sock, $consulta);
  }
  else if($op == 'R')
  {
    $consulta="update Exercicios_resolucao set compartilhada='".$tipo_comp."' where cod_resolucao=".$cod_item;
    $res=Enviar($sock, $consulta);
  }

  Desconectar($sock);

  // Imprime no div valores do formul?io
  if ($op == 'E')
    $objResponse->addAssign("trExercicio_".$cod_item, "className", "novoitem");
  else if($op == 'Q')
  	$objResponse->addAssign("trQuestao_".$cod_item, "className", "novoitem");
  else if($op == 'R')
  	$objResponse->addAssign("trResolucao_".$cod_item, "className", "novoitem");
  $objResponse->addAssign("comp_".$cod_item, "innerHTML", print_r($novo_comp, true));
  //addEvent não suporta o parametro 'event'
  $objResponse->addScript("document.getElementById('comp_".$cod_item."').onclick = function(event) { js_cod_item='".$cod_item."'; AtualizaComp('".$tipo_comp."'); MostraLayer(cod_comp,140, event); }" );
  if($op != 'R')
    $objResponse->addAssign("data_".$cod_item, "innerHTML", UnixTime2Data(time()));
  
  //AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->addScriptCall("mostraFeedback", $texto, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/********************************************************************************************************
  AdicionaQuestaoAoExercicioDinamic - Adiciona uma questao a um exercicio
  Entrada: $cod_curso - codigo do curso
           $cod_exercicio - codigo do exercicio
		   $cod_questao - codigo da questao
  Saida: XML da funcao ajax
********************************************************************************************************/
function AdicionaQuestaoAoExercicio($cod_usuario, $cod_curso, $cod_exercicio, $cod_questao)
{ 
		$cod_exercicio = VerificaNumeroQuery($cod_exercicio);
		$primeiraQuestao = 1;
		
		foreach ($cod_questao as $questao){
			if ($primeiraQuestao){
				$primeiraQuestao = 0;
				$values = "(".$cod_exercicio.",".VerificaNumeroQuery($questao).",0,'V')";
			} else {
				$values .= ", (".$cod_exercicio.",".VerificaNumeroQuery($questao).",0,'V')";
			}
			
		}
		
    $sock=Conectar($cod_curso);
    $data = time();
    
    $query = "insert ignore into Exercicios_modelo_questao ";
    $query.= "(cod_exercicio,cod_questao,valor,status) values ".$values;
    Enviar($sock,$query);
    
    // Alterando o status e a data (referente a modifica��o feita = recuperada)
    $query = "update Exercicios_modelo set data = ".$data." ";
    $query.= "where cod_exercicio = ".$cod_exercicio;
    Enviar($sock,$query);
    
    // Inserindo altera��o na tabela Exercicios_modelo_historico
    marcaLogExerciciosModeloHistorico($sock, $cod_exercicio, $cod_usuario, 'I');
    Desconectar($sock);
}

/********************************************************************************************************
  AtribuiValorAQuestaoDinamic - Atualiza o valor de uma questao e a soma total.
  Entrada: $cod_curso - codigo do curso
           $cod_exercicio - codigo do exercicio
		   $cod_questao - codigo da questao
		   $valor - valor da questao
  Saida: XML da funcao ajax
********************************************************************************************************/
function AtribuiValorAQuestaoDinamic($cod_curso, $cod_exercicio, $cod_questao, $valor)
{ 
    $objResponse = new xajaxResponse('ISO-8859-1');
 
    $sock=Conectar($cod_curso);
    $query = "update Exercicios_modelo_questao set valor = ".$valor." where cod_exercicio = ".VerificaNumeroQuery($cod_exercicio)." and cod_questao = ".VerificaNumeroQuery($cod_questao);
    Enviar($sock,$query);
    $valor = RetornaValorQuestao($sock,$cod_exercicio,$cod_questao);


    $objResponse->addAssign("valorQuestao_".$cod_questao, "innerHTML", $valor);
    $objResponse->addAssign("totalValorQuestoes", "innerHTML", "<b>".RetornaSomaValorQuestoes($sock,$cod_exercicio)."</b>");
    Desconectar($sock);

    return $objResponse->getXML();
}

/********************************************************************************************************
  AtualizaValorTotalExercicioDinamic - Atualiza o total da soma do valor de cada questao
  Entrada: $cod_curso - codigo do curso
           $cod_exercicio - codigo do exercicio
  Saida: XML da funcao ajax
********************************************************************************************************/
function AtualizaValorTotalExercicioDinamic($cod_curso, $cod_exercicio)
{ 
    $objResponse = new xajaxResponse('ISO-8859-1');
 
    $sock=Conectar($cod_curso);
    $valorTotal = RetornaSomaValorQuestoes($sock,$cod_exercicio);
    Desconectar($sock);
    
    $objResponse->addAssign("totalValorQuestoes", "innerHTML", "<b>".$valorTotal."<b>");

    return $objResponse->getXML();
}

/********************************************************************************************************
  ExluirQuestaoDoExercicioDinamic - Exclui a questao do exercicio
  Entrada: $cod_curso - codigo do curso
           $cod_exercicio - codigo do exercicio
           $cod_questao - codigo da questao
  Saida: XML da funcao ajax
********************************************************************************************************/
function ExluirQuestaoDoExercicioDinamic($cod_usuario, $cod_curso, $cod_exercicio, $cod_questao)
{ 
    $objResponse = new xajaxResponse('ISO-8859-1');
 
    $sock=Conectar($cod_curso);
    $data = time();
    
    $query = "delete from Exercicios_modelo_questao ";
    $query.= "where cod_exercicio = ".VerificaNumeroQuery($cod_exercicio)." ";
    $query.= "and cod_questao = ".VerificaNumeroQuery($cod_questao);
    Enviar($sock,$query);
    
    // Alterando o status e a data (referente a modifica��o feita = recuperada)
    $query = "update Exercicios_modelo set data = ".$data." ";
    $query.= "where cod_exercicio = ".$cod_exercicio;
    Enviar($sock,$query);
    
    // Inserindo altera��o na tabela Exercicios_modelo_historico
    marcaLogExerciciosModeloHistorico($sock, $cod_exercicio, $cod_usuario, 'R');
    Desconectar($sock);
    
    return $objResponse->getXML();
}

/********************************************************************************************************
  AplicaExercicioDinamic - Aplica o exercicio
  Entrada: $cod_curso - codigo do curso
           $cod_exercicio - codigo do exercicio
		   $cod_usuario - codigo do usuario que aplicou o exercicio
           $dt_disp - data da dispobilizacao do exercicio
		   $hr_disp - hora a ser disponibilizado o exercicio
           $dt_entrega - data limite para entrega
           $hr_entrega - hora limite para entrega
           $tp_aplicacao - tipo de aplicacao (individual ou em grupo)
           $disp_gabarito - indica se o gabarito deve ser disponibilizado com a correcao
           $avaliacao - indica se o exercicio sera associado a uma avaliacao
  Saida: XML da funcao ajax
********************************************************************************************************/
function AplicaExercicioDinamic($cod_curso,$cod_exercicio,$cod_usuario,$dt_disp,$hr_disp,$dt_entrega,$hr_entrega,$tp_aplicacao,$disp_gabarito,$avaliacao)
{ 
    $objResponse = new xajaxResponse('ISO-8859-1');
    $unixTimeDisp = DataHora2UnixTime($dt_disp." ".$hr_disp);
    $unixTimeEntrega = DataHora2UnixTime($dt_entrega." ".$hr_entrega);
    $cod_avaliacao = 0;

    $sock=Conectar($cod_curso);
    
    if($avaliacao == "S")
    {
      $data = time();
      $valor = RetornaSomaValorQuestoes($sock,$cod_exercicio);
      $cod_avaliacao = RetornaProximoCodigo($sock,'Avaliacao');
      $query="insert into Avaliacao (cod_avaliacao, cod_atividade, cod_usuario, ferramenta, tipo, valor, status, data, data_inicio, data_termino, inicio_edicao) values (".$cod_avaliacao.", ".$cod_exercicio.", ".$cod_usuario.", 'E', '".$tp_aplicacao."', ".$valor.", 'F', ".$data.", '".$unixTimeDisp."', '".$unixTimeEntrega."', '0')";
      $res=Enviar($sock, $query);
    }
    
 	$data = time();
 	
 	// Cria o exercicio na tabela Exercicios_aplicados
    $query = "insert into Exercicios_aplicado (cod_exercicio,dt_disponibilizacao,dt_limite_submissao,tp_aplicacao,disp_gabarito,avaliacao) ";
    $query.= "values (".VerificaNumeroQuery($cod_exercicio).",".$unixTimeDisp.",".$unixTimeEntrega.",'".$tp_aplicacao."','".$disp_gabarito."','".$cod_avaliacao."')";
    Enviar($sock,$query);
    
    // Atualiza o status e a data do modelo do exerc�cio em Exercicios_modelo
    $query = "update Exercicios_modelo set situacao = 'A', ";
    $query.= "data = ".$data." where cod_exercicio =".VerificaNumeroQuery($cod_exercicio);
    Enviar($sock,$query);
    
    // Atualiza o 'log' dos Exercicios em Exercicios_modelo_historico
    marcaLogExerciciosModeloHistorico($sock, $cod_exercicio, $cod_usuario, 'P');
    Desconectar($sock);
    
   	$objResponse->addScript("ExercicioAplicado('".$avaliacao."',".$cod_avaliacao.");"); 


    return $objResponse->getXML();
}


/********************************************************************************************************
  CancelaAplicacaoExercicioDinamic - Cancela aplicacao do exercicio
  Entrada: $cod_curso - codigo do curso
  		   $cod_usuario - codigo do usuario
           $cod_exercicio - codigo do exercicio
           $flag - indica se é um cancelamento definitivo ou apenas uma reaplicacao do exercicio
  Saida: XML da funcao ajax
********************************************************************************************************/
function CancelaAplicacaoExercicioDinamic($cod_curso,$cod_usuario,$cod_exercicio,$flag)
{ 
    $objResponse = new xajaxResponse('ISO-8859-1');

    $sock=Conectar($cod_curso);
    
    $cod_avaliacao = RetornaCodAvaliacao($sock,$cod_exercicio);
    
    $query = "delete from Exercicios_aplicado where cod_exercicio = ".VerificaNumeroQuery($cod_exercicio);
    Enviar($sock,$query);
    
    if($cod_avaliacao != 0)
    {
      $query = "delete from Avaliacao where cod_avaliacao = ".$cod_avaliacao;
      Enviar($sock,$query);
    }
    
    CancelaResolucaoDoExercicio($sock,$cod_exercicio);
    
    $query = "update Exercicios_modelo set situacao = 'C' where cod_exercicio =".VerificaNumeroQuery($cod_exercicio);
    Enviar($sock,$query);
    
    $data = time();
    
    marcaLogExerciciosModeloHistorico($sock, $cod_exercicio, $cod_usuario, 'E');
    Desconectar($sock);
    
    $objResponse->addScript("AplicacaoCancelada(".$flag.");");

    return $objResponse->getXML();
}

/********************************************************************************************************
  AtualizaRespostaDoUsuarioDinamic - Atualiza (ou insere caso nao tenha respondido ainda) a resposta de uma questao
  Entrada: $cod_curso - codigo do curso
           $cod_resolucao - codigo da resolucao
           $cod_questao - codigo da questao
           $resposta - resposta do exercicio
           $texto - texto de feedback
           $tp_questao - tipo da questao
  Saida: XML da funcao ajax
********************************************************************************************************/
function AtualizaRespostaDoUsuarioDinamic($cod_curso, $cod_resolucao, $cod_questao, $resposta, $texto, $tp_questao)
{ 
    $objResponse = new xajaxResponse('ISO-8859-1');
 
    $sock=Conectar($cod_curso);
    if($tp_questao == 'O'){
      $query = "insert into Exercicios_resolucao_questao_obj (cod_questao,cod_resolucao,resposta) values (".$cod_questao.",".$cod_resolucao.",'".$resposta."') on duplicate key update resposta = '".$resposta."'";
    } elseif($tp_questao == 'M'){
      $query = "insert into Exercicios_resolucao_questao_mult (cod_questao,cod_resolucao,resposta) values (".$cod_questao.",".$cod_resolucao.",'".$resposta."') on duplicate key update resposta = '".$resposta."'";
    } elseif($tp_questao == 'D'){
      $query = "insert into Exercicios_resolucao_questao_diss (cod_questao,cod_resolucao,resposta) values (".$cod_questao.",".$cod_resolucao.",'".$resposta."') on duplicate key update resposta = '".$resposta."'";
    }

    Enviar($sock,$query);
    Desconectar($sock);
    
    if($tp_questao == 'O'){
    	AtualizaNotaDinamicObj($cod_curso,$cod_questao,$cod_resolucao,$resposta);
    } elseif($tp_questao == 'M'){
    	AtualizaNotaDinamicaMult($cod_curso, $cod_questao, $cod_resolucao, $resposta);
    }
    
    /* ? - Respondida */
    $objResponse->addAssign('tdStatus_'.$cod_questao, "innerHTML", "Respondida");
    $objResponse->addScript("mostraFeedback(\"".$texto."\",true);");
    
    return $objResponse->getXML();
}

/* AtualizaNotaDinamicaObj - Atualiza a nota do participante.
 * 			Entrada: $cod_curso - codigo do curso para conectar ao banco de dados.
 * 					 $cod_questao - codigo da questão.
 * 					 $cod_resolucao - codigo da resolução
 * 	                 $resposta - resposta do participando na questao
 */

function AtualizaNotaDinamicObj($cod_curso,$cod_questao,$cod_resolucao,$resposta)
{
	$sock=Conectar($cod_curso);

	$resolucao = RetornaResolucao($sock,$cod_resolucao);
	
	$consulta="select valor from Exercicios_modelo_questao where cod_questao=".$cod_questao." and cod_exercicio=".$resolucao['cod_exercicio'];	
    $res=Enviar($sock, $consulta);
    $linha=RetornaLinha($res);
    
	$gabarito = RetornaGabaritoQuestaoObj($sock, $cod_questao);
	
    if($resposta == $gabarito)
		$valor = $linha['valor'];
	else $valor = 0;

	$consulta ="update Exercicios_resolucao_questao_obj set nota=".$valor." where cod_resolucao=".$cod_resolucao." and cod_questao=".$cod_questao;
	Enviar($sock, $consulta);
	Desconectar($sock);

}

/* AtualizaNotaDinamicaMult - Atualiza a nota do participante.
 * 			Entrada: $cod_curso - codigo do curso para conectar ao banco de dados.
 * 					 $cod_questao - codigo da questão.
 * 					 $cod_resolucao - codigo da resolução
 * 	                 $resposta - resposta do participando na questao
 */

function AtualizaNotaDinamicaMult($cod_curso,$cod_questao,$cod_resolucao,$resposta)
{
	$sock=Conectar($cod_curso);

	$resolucao = RetornaResolucao($sock,$cod_resolucao);
	
	$consulta="select valor from Exercicios_modelo_questao where cod_questao=".$cod_questao." and cod_exercicio=".$resolucao['cod_exercicio'];	
    $res=Enviar($sock, $consulta);
    $linha=RetornaLinha($res);
    
    $consulta = "select gabarito from Exercicios_questao_mult where cod_questao=".$cod_questao;
    $res=Enviar($sock, $consulta);
    $gabarito=RetornaLinha($res);

    if($resposta == $gabarito['gabarito'])
		$valor = $linha['valor'];
	else $valor = 0;

	$consulta ="update Exercicios_resolucao_questao_mult set nota=".$valor." where cod_resolucao=".$cod_resolucao." and cod_questao=".$cod_questao;
	Enviar($sock, $consulta);
	Desconectar($sock);

}

function AtualizaNotaDinamicDiss($cod_questao, $cod_curso, $cod_resolucao, $nota, $valor)
{
    $objResponse = new xajaxResponse('ISO-8859-1');	

    if($nota <= $valor && $nota >= 0)
    {
	    $sock = Conectar($cod_curso);
		
	 	$consulta = "update Exercicios_resolucao_questao_diss SET nota=".$nota."  WHERE cod_resolucao=".$cod_resolucao." AND cod_questao=".$cod_questao;
		Enviar($sock,$consulta);
		Desconectar($sock);
		$feedback = "Nota alterada com sucesso";
    }
    else
		$feedback = "Nota n�o permitida";
    $objResponse->addScriptCall("mostraFeedback", $feedback, 'true');
    return $objResponse->getXML();
}

function VerificaEntregaDinamic($cod_curso, $cod_resolucao)
{
	$objResponse = new xajaxResponse('ISO-8859-1');	
	$flag=1;
	$sock = Conectar($cod_curso);
		
	$consulta = "select * from Exercicios_resolucao_questao_diss where cod_resolucao=".$cod_resolucao." and isnull(nota)";
	$res=Enviar($sock,$consulta);
	if(RetornaLinha($res))
		$flag=0;

	$consulta = "select * from Exercicios_resolucao_questao_obj where cod_resolucao=".$cod_resolucao." and isnull(nota)";
	$res=Enviar($sock,$consulta);
	if(RetornaLinha($res))
		$flag=0;

	$consulta = "select * from Exercicios_resolucao_questao_mult where cod_resolucao=".$cod_resolucao." and isnull(nota)";
	$res=Enviar($sock,$consulta);
	if(RetornaLinha($res))
		$flag=0;
		
	$objResponse->addScript("VerificaEntrega(".$cod_curso.",".$cod_resolucao.",".$flag.");");
		
    return $objResponse->getXML();

}


/* *********************************************************************
   EditarRespostaQuestaoDissDinamic - Atualiza (ou insere) resposta do usuario para uma questao
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_resolucao - codigo da resolucao
            $cod_questao - codigo da questao
            $novo_texto - novo texto do exercicio
            $cod_usuario - código do usuário que está utilizando
            $feedback - mensagem de feedback
   Saida: XML da função Ajax
*/
function EditarRespostaQuestaoDissDinamic($cod_curso, $cod_resolucao, $cod_questao, $novo_texto, $cod_usuario, $feedback)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $novo_texto=ConverteAspas2BarraAspas($novo_texto);
  $sock=Conectar($cod_curso);

  $consulta="insert into Exercicios_resolucao_questao_diss (cod_resolucao,cod_questao,resposta) values  (".$cod_resolucao.",".$cod_questao.",'".VerificaStringQuery(trim(VerificaTexto($novo_texto)))."') on duplicate key update resposta = '".VerificaStringQuery(trim(VerificaTexto($novo_texto)))."'";
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  //AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->addScriptCall("mostraFeedback", $feedback, 'true');

  $objResponse->addAssign("text_".$cod_resolucao."_".$cod_questao, innerHTML, print_r(AjustaParagrafo(ConverteBarraAspas2Aspas(VerificaTexto($novo_texto))), true));

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

function EditarComentarioQuestaoDissDinamic($cod_curso, $cod_resolucao, $cod_questao, $novo_texto, $cod_usuario, $feedback)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $novo_texto=ConverteAspas2BarraAspas($novo_texto);
  $sock=Conectar($cod_curso);

  $consulta="insert into Exercicios_resolucao_questao_comentarios (cod_questao,cod_resolucao,cod_usuario,comentario,data) values  (".$cod_questao.", ".$cod_resolucao.",".$cod_usuario.", '".VerificaStringQuery(trim(VerificaTexto($novo_texto)))."', ".time().")";
  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  //AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario, 1);

  $objResponse->addScriptCall("mostraFeedback", $feedback, 'true');

  $objResponse->addAssign("text_".$cod_resolucao."_".$cod_questao, innerHTML, print_r(AjustaParagrafo(ConverteBarraAspas2Aspas(VerificaTexto($novo_texto))), true));

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse->getXML();
}

/* *********************************************************************
   ApagarExercicio - Atualiza o status da questao para X (apagado)
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_questao - codigo da questao
            $status - novo status da questao (V - valido; L - lixeira; X - excluido definitivamente)
*/
function EnviarLixeira($cod_curso, $cod_questao, $status)
{
  $sock=Conectar($cod_curso);
  $consulta = "UPDATE Exercicios_questao SET status = '".$status."' WHERE cod_questao = ".$cod_questao;
  $res=Enviar($sock, $consulta);
  Desconectar($sock);
}

/* *********************************************************************
   EntregaCorrecao - Entrega a correcao do exercicio, isso setando o corrigida = 'S' e atualizando a nota final do exercicio
   Entrada: $cod_curso - c�digo do curso, para conectar ao banco de dados
            $cod_resolucao - codigo da resolucao
*/
function EntregaCorrecao($sock, $cod_curso, $cod_resolucao)
{
	/* Pega a nota total */ 
	$soma = RetornaNotaFinal($sock, $cod_curso, $cod_resolucao);
	
  	/* Salva a nota total e entrega a resolucao do exercicio */
  	$update = "UPDATE Exercicios_resolucao SET nota=".$soma.", corrigida='S' WHERE cod_resolucao =".$cod_resolucao;
  	$res=Enviar($sock, $update);

  	/* Pega info do exercicio por cod_resolucao */
  	$query = "select * FROM Exercicios_resolucao WHERE cod_resolucao =".$cod_resolucao;
   	$res = Enviar($sock, $query);
    	$exercicio = RetornaLinha($res);
    
    	$cod_avaliacao = RetornaCodAvaliacao($sock,$exercicio['cod_exercicio']);
    
  	// Marca no log das resolu��es, na tabela Exercicio_resolucao_historico
    	// o evento de ter entregue o exercicio
    	marcaLogExerciciosResolucaoHistorico($sock, $cod_resolucao, 'C');
      	
  	/* Entrega da correcao para aplicacao Individual */
  	if (is_null($exercicio['cod_grupo'])){
   
    	$query = "UPDATE Avaliacao_notas SET nota=".$soma." WHERE cod_aluno=".$exercicio['cod_usuario']." and cod_avaliacao=".$cod_avaliacao;
    	$res = Enviar($sock, $query);
    	
  	} else {
  		/* Entrega da correcao para aplicacao em Grupo */
  		$query = "UPDATE Avaliacao_notas SET nota=".$soma." WHERE cod_grupo=".$exercicio['cod_grupo']." and cod_avaliacao=".$cod_avaliacao;
    	$res = Enviar($sock, $query);
  	}
}

/* *********************************************************************
   EntregaCorrecaoExsObjetivosEMultiplaEscolha - Entrega a corre��o de todos os exerc�cios com quest�es
   			apenas objetivas ou multipla escolha, com data ap�s o limite de submiss�o.
   Entrada: $cod_curso - c�digo do curso, para conectar ao banco de dados.
   Sa�da: True se teve resolu��es entregues e False caso contr�rio.
*/
function EntregaCorrecaoExsObjetivosEMultiplaEscolha($cod_curso)
{
	// Conecta com a banco do curso.
	$sock = Conectar($cod_curso);
	
	// Pega todos os exerc�cios que podem ter seu gabarito liberado.
	$query = "select cod_exercicio from Exercicios_aplicado ";
	$query.= "where (dt_limite_submissao < ".time().") and ";
	$query.= "(disp_gabarito = 'S')";
	$res = Enviar($sock, $query);
	
	// Se n�o houver resultados, n�o h� exerc�cios que possam ser entregues.
	if (RetornaNumLinhas($res) <= 0)
		return false;
		
	// Exercicios que possam ter gabaritos disponibilizados
	$array_cod_exercicios = RetornaArrayLinhas($res);
		
	// Nesse array, apenas estar�o os exerc�cios que possam ser entregues
	// e tenham apenas quest�es objetivas ou multipla escolha
	$ex_entregaveis = array();
	$atual = -1;				// Atual posi��o do array criado		
	
	// Pega todas as quest�es objetivas e de multipla escolha do banco.
	$query = "select cod_questao from Exercicios_questao ";
	$query.= "where (tp_questao = 'O') or ";
	$query.= "where (tp_questao = 'M')";
	$res = Enviar($sock, $query);
	
	// Se n�o houver resultados, n�o h� quest�es objetivas no banco e
	// portanto, n�o haver� exerc�cios a serem entregues.
	if (RetornaNumLinhas($res) <= 0)
		return false;
		
	// Todas as quest�es objetivas e de multipla escolha do banco de dados.	
	//$array_questoes_obj = RetornaArrayLinhas($res);
	$array_questoes = RetornaArrayLinhas($res);
	
	// Seleciona, de todos os exerc�cios pass�veis de serem entregues at� agora
	// quais possuem apenas quest�es objetivas ou de multipla escolha e adiciona no array $ex_entregaveis	
	for ($i = 0; $i < sizeof($array_cod_exercicios); $i++)
	{
		$query = "select cod_questao from Exercicios_modelo_questao ";
		$query.= "where (cod_exercicio = ".$array_cod_exercicios[$i]['cod_exercicio'].")";
		$res = Enviar($sock, $query);
		
		if (RetornaNumLinhas($res) > 0)
		{
			$array_cod_questoes = RetornaArrayLinhas($res);
			
			// Se alguma destas questoes n�o esta na lista de quest�es
			// objetivas, esse exercicio n�o deve ser entregue
			for ($j = 0; $j < sizeof($array_cod_questoes); $j++)
			{
				$flag = false;
				$k = 0;
				while (($k < sizeof($array_questoes)) && ($flag == false))
				{
					if ($array_cod_questoes[$j]['cod_questao'] == $array_questoes[$k]['cod_questao'])
						$flag = true;
					$k++;
				}
				
				if ($flag == false)
					break;
			}
			
			// Se a flag permaneceu true, todas as quest�es s�o objetivas
			// e podemos adicionar este exercicio ao array
			if ($flag == true)
			{
				$atual++;
				$ex_entregaveis[$atual] = $array_cod_exercicios[$i]['cod_exercicio'];
			}
			
		}
	}
	
	/* Se existem questoes a serem entregues */
	if ($atual >= 0){
		// Pega todas as resolucoes ainda n�o entregues dos ex_entregaveis
		$query = "select cod_resolucao from Exercicios_resolucao ";
		$query.= "where (corrigida = 'N') and ( ";
	
		while ($atual >= 0)
		{
			$query.= "(cod_exercicio = ".$ex_entregaveis[$atual].") ";
			if ($atual == 0)
				$query.= ")";
			else
				$query.= "or ";
				
			$atual--;
		}
		
		$res = Enviar($sock, $query);
		$array_cod_resolucoes = RetornaArrayLinhas($res);
		for ($i = 0; $i < sizeof($array_cod_resolucoes); $i++)
			EntregaCorrecao($sock, $cod_curso, $array_cod_resolucoes[$i]['cod_resolucao']);
			
		// Fim da fun��o.
		Desconectar($sock);
		return true;
	}
}

/* *********************************************************************
   VerificaSeCorrigida - Verifica se o exerc�cio j� foi corrigido para o usu�rio
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_questao - codigo da questao
            $status - novo status da questao (V - valido; L - lixeira; X - excluido definitivamente)
*/
function VerificaSeCorrigida($cod_curso,$cod_resolucao)
{
  $sock=Conectar($cod_curso);
  $consulta = "select corrigida from Exercicios_resolucao where cod_resolucao = ".$cod_resolucao;
  $res = Enviar($sock, $consulta);
  $linha = RetornaLinha($res);
  
  if($linha['corrigida'] == "S")
  	return true;
  else
    return false;
}

function PegaNotaObjetiva($cod_questao, $cod_curso, $cod_resolucao)
{
	$sock=Conectar($cod_curso);
  	$consulta = "select nota from Exercicios_resolucao_questao_obj where cod_resolucao = ".$cod_resolucao." and cod_questao = ".$cod_questao;
  	$res = Enviar($sock, $consulta);
  	$linha = RetornaLinha($res);
	return $linha['nota'];
}

function PegaNotaMultEscolha($cod_questao, $cod_curso, $cod_resolucao)
{
	$sock=Conectar($cod_curso);
  	$consulta = "select nota from Exercicios_resolucao_questao_mult where cod_resolucao = ".$cod_resolucao." and cod_questao = ".$cod_questao;
  	$res = Enviar($sock, $consulta);
  	$linha = RetornaLinha($res);
	return $linha['nota'];
}


function VerificaQuestaoDissertativa($cod_questao, $cod_curso, $cod_resolucao)
{
	$sock=Conectar($cod_curso);
  	$consulta = "select * from Exercicios_resolucao_questao_diss where cod_resolucao = ".$cod_resolucao." and cod_questao = ".$cod_questao;
  	$res = Enviar($sock, $consulta);
  	$linha = RetornaLinha($res);
	  	
  	$saida[0]=$linha['nota'];
  	$saida[1]=$linha['comentario'];
	return $saida;
}

function PegaComentarioQuestao($cod_curso, $cod_resolucao,$cod_questao, $cod_usuario) {
	$sock=Conectar($cod_curso);
  	$consulta = "select comentario from Exercicios_resolucao_questao_comentarios where cod_resolucao = ".$cod_resolucao." and cod_questao = ".$cod_questao." and cod_usuario = ".$cod_usuario;
  	$res = Enviar($sock, $consulta);
  	$linha = RetornaLinha($res);
  	if($linha)
  		$comentario = $linha[0];
  	else
  		$comentario = null;	 
	return $comentario;
}

/* *********************************************************************
   VerificaNotas - Verifica as notas das questoes de um Exercicio a ser aplicado.
   Entrada: $cod_exercicio - codigo do exercicio
						$cod_curso - codigo do curso
   Saida: valor = 0  -> Questoes sem nota ou nota = 0
   				valor = 1  -> Todas as questoes com nota, ok
   				valor = 2	 ->	Nao ha questoes
*/
function VerificaNotas($cod_exercicio,$cod_curso){
  	$objResponse = new xajaxResponse('ISO-8859-1');
	
  	$valor = 1;
  
	$sock=Conectar($cod_curso);
	
	$consulta = "SELECT * FROM Exercicios_modelo_questao WHERE cod_exercicio=".$cod_exercicio;
	$res=Enviar($sock,$consulta);
	
	Desconectar($sock);
	
  	$questoes = RetornaArrayLinhas($res);
	
  	if (count($questoes) > 0)
  	{
  		foreach ($questoes as $questao)
  		{
  			if ($questao['valor'] == 0)
  			{
  				$valor = 0;
  				break;	
  			}
  		}
  	}	
  	else 
  	{
 		$valor = 2;
 	}
 	
  	$objResponse->addScript("verifica_notas(".$valor.",".$cod_exercicio.");");

  	return $objResponse->getXML();
}

/* *********************************************************************
   AtualizaIconesDinamic - Atualiza os icones da tabela
   Entrada: $sock - BASE DO CURSO
            $cod_questao - codigo da questao
			$cod_curso - codigo do curso
			$tp_questao - O: objetiva
						- M: Multipla Escolha
   Saida: XML da fun��o Ajax
*/
function AtualizaIconesDinamic($cod_questao,$cod_curso,$tp_questao){
	$sock=Conectar($cod_curso);
	$objResponse = new xajaxResponse('ISO-8859-1');
	
	$alternativas = RetornaAlternativas($sock,$cod_questao);
	
	$numalt = count($alternativas);
	for($i=0;$i<$numalt;$i++){
		if($i!=0) 
			$alt = $alt.".".$alternativas[$i]['cod_alternativa'];
		else
			$alt = $alternativas[$i]['cod_alternativa'];
	}
	
  	if($tp_questao == 'O') {
		$gabarito = RetornaGabaritoQuestaoObj($sock,$cod_questao);
	} elseif ($tp_questao == 'M') {
		$gabarito = RetornaGabaritoQuestaoMult($sock, $cod_questao);
	}
	$objResponse->addScriptCall("AtualizaIcones",$alt,$gabarito);
	//$objResponse->addScriptCall("CarregaNumCerta");
	//$objResponse->addScriptCall("MudaCabecalho");
  	Desconectar($sock);

  	return $objResponse->getXML();
}
