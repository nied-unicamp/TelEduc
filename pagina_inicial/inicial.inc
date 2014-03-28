<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/inicial.inc

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
  ARQUIVO : pagina_inicial/inicial.inc
  ========================================================== */

/* **********************************************************************
   RetornaCadastroUsuario - Retorna o dados cadastrais do usu�rio
                                 especificado.
   Entrada: $sock - sock do curso,
            $cod_usuario - c�digo do usu�rio.
   Saida: array contendo:
            ['nome'] - nome,
            ['email'] - email,
            ['telefone'] - telefone,
            ['endereco'] - endereco,
            ['cidade'] - cidade,
            ['estado'] - estado,
            ['pais'] - pais,
            ['data_nasc'] - data de nascimento,
            ['sexo'] - sexo,
            ['local_trab'] - local de trabalho,
            ['profissao'] - profissao,
            ['cod_escolaridade'] - c�digo de escolaridade,
            ['informacoes'] - informa��es adicionais,
            ['login'] - login.
*/
function RetornaCadastroUsuario($sock, $cod_usuario)
{
  $query  = "select nome, email, telefone, endereco, cidade, estado, pais, data_nasc,";
  $query .= " sexo, local_trab, profissao, cod_escolaridade, informacoes, login from";
  $query .= " Usuario where cod_usuario = ".VerificaNumeroQuery($cod_usuario)." order by";
  $query .= " ASCII(tipo_usuario) limit 1";

  $res = Enviar($sock,$query);
  $linha = RetornaArrayLinhas($res);
  return($linha[0]);
}

/* **********************************************************************
   RetornaConfiguracao - Retorna o conte�do de um item da configura��o
   Entrada: $sock - sock da base externa
            $item - item a ser buscado
   Saida: conte�do
*/
function RetornaConfiguracao($sock,$item)
{
  $query="select valor from Config where item='".VerificaStringQuery($item)."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha['valor']);
}

/* **********************************************************************
   RetornaInstituicao - Retorna o conte�do de um item da configura��o
   Entrada: $sock - sock da base externa
   Saida: array ['nome'] - nome da instituicao
                ['informacoes'] - informa��es adicionais
                ['link'] - endere�o da p�gina da institui��o
*/
function RetornaInstituicao($sock)
{
  $query="select * from Instituicao";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha);
}

/* **********************************************************************
   RetornaCodigoTextosFerramentas - Retorna o conte�do de um item da configura��o
   Entrada: $sock - sock da base externa
   Saida: array [0] - C�digo do texto do nome da ferramenta
                [1] - C�digo do texto da descri��o da ferramneta
                [2] - C�digo da ferramenta
                [3] - Posi��o da ferramenta no Menu.
*/
function RetornaCodigoTextosFerramentas($sock)
{
  $query="select F.cod_texto_nome,F.cod_texto_descricao,F.cod_ferramenta, M.posicao from Ferramentas F,Menu M where F.cod_ferramenta=M.cod_ferramenta order by M.posicao";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return ($lista);
}

/* **********************************************************************
   RetornaListaDeCursosUsuario - Retorna os cursos do qual um usu�rio
     participa/participou baseado em seu e-mail ou login.

   Entrada: $email - e-mail do usu�rio,
            $login - login do usu�rio.
   Saida: array []['cod_curso']['nome_curso']
                []['inscricao_inicio']
                []['inscricao_fim']
                []['acesso_visitante'] - 'A' se aceita, 'N' se n�o
*/

function RetornaListaDeCursosUsuario($email, $login)
{
  $sock = Conectar("");

  $query = "select cod_curso, nome_curso from Cursos";
  $res = Enviar($sock,$query);
  $total_cursos = RetornaNumLinhas($res);
  $lista = RetornaArrayLinhas($res);

  Desconectar($sock);

  if (!($email == '') || ($login == ''))
    $whclause = "email = '".VerificaStringQuery($email)."' or login = '".VerificaStringQuery($login);
  else if ($email != '')
    $whclause = "email = '".VerificaStringQuery($email);
  else if ($login != '')
    $whclause = "login = '".VerificaStringQuery($login);

  $query = "select cod_usuario from Usuario where ".$whclause."' order by";
  $query .= " ASCII(tipo_usuario) limit 1";

  for ($i = 0; $i < $total_cursos; $i++)
  {
    $sock = Conectar($lista[$i]['cod_curso']);

    $res = Enviar($sock,$query);
    $dados = RetornaArrayLinhas($res);

    if (count($dados) > 0)
    {
      $resultado[$lista[$i]['cod_curso']]['nome_curso'] = $lista[$i]['nome_curso'];
      $resultado[$lista[$i]['cod_curso']]['cod_usuario'] = $dados[0]['cod_usuario'];
    }
    Desconectar($sock);
  }

  return($resultado);
}

/* **********************************************************************
   RetornaListaDeCursos - Retorna a lista de cursos dado o tipo
   Entrada: $sock - sock da base externa
            $tipo_curso - tipo do curso
                          'N' - N�o iniciado
                          'A' - Em andamento
                          'I' - com inscri��es aberta
                          'E' - Encerrados
            $cod_pasta - codigo da pasta a qual pertence os cursos a serem retornados
   Saida: array []['cod_curso']
                []['nome_curso']
                []['inscricao_inicio']
                []['inscricao_fim']
                []['acesso_visitante'] - 'A' se aceita, 'N' se n�o
*/
function RetornaListaDeCursos($sock,$tipo_curso,$cod_pasta)
{
  $hoje=time();
  $ontem=$hoje - 86400;

  if($tipo_curso=='N')
    $where="(curso_inicio > ".time()." or curso_inicio is NULL)";
  else if ($tipo_curso=='A')
    $where="curso_inicio<=".$hoje." and curso_fim>=".$ontem;
  else if ($tipo_curso=='I')
    $where="inscricao_inicio<=".$hoje." and inscricao_fim>=".$ontem;
  else
    $where="curso_fim<".$ontem;

  if ($cod_pasta=="NULL" || $cod_pasta=="")
    $pasta="cod_pasta IS NULL";
  else
    $pasta="cod_pasta=".VerificaNumeroQuery($cod_pasta);

  $query="select cod_curso,nome_curso,inscricao_inicio,inscricao_fim,acesso_visitante from Cursos where ".$pasta." and ".$where." order by nome_curso";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0 && $lista != "")
  {
    $c=0;
    foreach($lista as $cod => $linha)
      $lista_saida[$c++]=$linha;
  }
  else
    $lista_saida=$lista;

  return ($lista_saida);
}

/* **********************************************************************
   RetornaDadosInstituicao - Retorna os dados da institui��o
   Entrada: $sock= Base externa
   Saida: array com ['nome'],['informacoes'],['link']
*/
function RetornaDadosIntituicao($sock)
{
  $query="select * from Instituicao";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha);
}

/* **********************************************************************
   RetornaContatos - Retorna os dados dos contatos para criacao de curso
   Entrada: nenhuma
   Saida: array com []['nome'],[]['email'],[]['status']
*/
function RetornaContatos($sock)
{
  $query="select * from Contatos where status='C' order by nome";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return ($lista);
}

/* **********************************************************************
   RetornaDadosMostraCurso - Retorna os dados do curso dado
   Entrada: $sock - base externo
   Saida: array com []['cod_curso']
                    []['nome_curso']
                    []['inscricao_inicio']
                    []['inscricao_fim']
                    []['curso_inicio']
                    []['curso_fim']
                    []['informacoes']
                    []['publico_alvo']
                    []['tipo_inscricao']
                    []['num_alunos']
                    []['cod_coordenador']
                    []['acesso_visitante']
*/
function RetornaDadosMostraCurso($sock,$cod_curso)
{
  $query="select * from Cursos where cod_curso=".VerificaNumeroQuery($cod_curso);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha);
}

/* *****************************************************************************
  RetornaListaEscolaridade - Verifica se existe a orientacao na tabela Perfil_Orientacao
  Entrada: $sock - sock de conexao EXTERNA
  Saida:   array com
            []['cod_escolaridade']
            []['cod_texto_escolaridade']
*/
function RetornaListaEscolaridade($sock)
{
  $query="select * from Escolaridade order by cod_escolaridade";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *****************************************************************************
  RetornaProximoCodigoUsuarioNoCurso - Retorna o codigo do proximo usuario no curso
  Entrada: $sock - sock de conexao EXTERNA
           $cod_usuario_global - codigo global do usuario
           $cod_curso - codigo do curso
  Saida:   codigo do proximo usuario no curso
*/
function RetornaProximoCodigoUsuarioNoCurso($sock,$cod_usuario_global,$cod_curso)
{
  $query="select cod_usuario from Usuario_curso where cod_curso=".VerificaNumeroQuery($cod_curso)." order by cod_usuario desc";
  $res=Enviar($sock,$query);

  $lista=RetornaArrayLinhas($res);

  return ($lista[0]['cod_usuario'] + 1);
}

/* *****************************************************************************
  CadastraUsuario - Cadastra o usu�rio na tabela
  Entrada: $sock - sock da base do Curso
           $nome_usuario, $email, $telefone, $endereco,
           $cidade, $estado, $pais, $data,
           $sexo, $local, $profissao, $cod_escolaridade,
           $informacoes,$login,$opcao
  Saida: nenhuma
*/
function CadastraUsuario($sock,$cod_usuario_global,$cod_curso)
{
  $cod_usuario_prox = RetornaProximoCodigoUsuarioNoCurso($sock,$cod_usuario_global,$cod_curso);

  $query  = "insert into Usuario_curso (cod_usuario_global,cod_usuario,cod_curso,tipo_usuario,data_inscricao) ";
  $query .= "values (".VerificaNumeroQuery($cod_usuario_global).",".$cod_usuario_prox.",".VerificaNumeroQuery($cod_curso).",'i',".time().")";
  Enviar($sock,$query);

  $query  = "insert into Usuario_config (cod_usuario,cod_curso) ";
  $query .= "values (".$cod_usuario_prox.",".VerificaNumeroQuery($cod_curso).")";
  Enviar($sock, $query);

}

/* *****************************************************************************
  ReCadastraUsuario - Recadastra o usu�rio na tabela, alterando sua inscri��o para
                      n�o avaliada.
  Entrada: $sock               - sock da base do Curso
           $cod_usuario_global - c�digo global do usuario, dispon�vel no mmomento
                                 em que ele se inscreve em um curso.
           $cod_curso          - c�digo do curso.
  Saida: nenhuma
*/
function ReCadastraUsuario($sock,$cod_usuario_global,$cod_curso)
{
  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query  = "update ".$dbnamebase.".Usuario_curso set tipo_usuario = 'i', data_inscricao = ".time()." ";
  $query .= "where cod_usuario = ".$cod_usuario." and ";
  $query .=       "cod_curso   = ".VerificaNumeroQuery($cod_curso);

  Enviar($sock, $query);
}

/* ****************************************************************************************
  RetornaPastasDeCursos - Retorna uma array com as pastas em que os cursos est�o divididos
                          (somente as pastas que contem algum curso)
  Entrada: $sock - sock Base Externa
           $tipo_curso - tipo do curso
                         'A' - Em andamento
                         'I' - com inscri��es aberta
                         'E' - Encerrados
  Saida: array com []['cod_pasta']
                   []['pasta']
                   []['num_cursos']
*/
function RetornaPastasDeCursos($sock,$tipo_curso)
{
  global $lista_frases;

  $hoje=time();
  $ontem=$hoje - 86400;

  if($tipo_curso=='N')
    $where="(C.curso_inicio > ".time()." or C.curso_inicio is NULL)";
  else if ($tipo_curso=='A')
    $where="C.curso_inicio<=".$hoje." and C.curso_fim>=".$ontem;
  else if ($tipo_curso=='I')
    $where="C.inscricao_inicio<=".$hoje." and C.inscricao_fim>=".$ontem;
  else
    $where="C.curso_fim<".$ontem;

  $query="select CP.cod_pasta,CP.pasta,count(*) 'num_cursos' from Cursos_pastas as CP, Cursos as C where CP.cod_pasta=C.cod_pasta and ".$where." group by CP.cod_pasta order by CP.pasta";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  $query="select count(*) from Cursos C where C.cod_pasta IS NULL and ".$where;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  if ($linha[0]>0)
  {
    $cont=count($lista);
    $lista[$cont]['cod_pasta']="NULL";
    /* 115 - Cursos gerais */
    $lista[$cont]['pasta']=RetornaFraseDaLista($lista_frases,115);
    $lista[$cont]['num_cursos']=$linha[0];
  }

  return ($lista);
}

/* **********************************************************************
   RetornaCategorias - Retorna as categorias que um curso pode pertencer
   Entrada: $sock - conexão com a base geral
   Saida: array com [$cod_pasta]='nome da pasta'
*/
function RetornaCategorias($sock)
{
  $query="select * from Cursos_pastas order by pasta";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0 && $lista != "")
  {
    foreach($lista as $cod => $linha)
      $saida[$linha['cod_pasta']]=$linha['pasta'];
  }
  else
    $saida = "";
  return $saida;
}

/* **********************************************************************
   RetornaCodCurso - Retorna o c�digo do curso a ser criado
   Entrada: $sock - Socket da conex�o atual
   Saida: $cod_curso = C�digo do curso a ser criado
*/
function RetornaCodCurso($sock)
{
  $query="select max(cod_curso) from Cursos_requisicao";
  $res=Enviar($sock,$query);
  $cod_curso=RetornaLinha($res);
  $max_cod_curso=$cod_curso['max(cod_curso)']+1;
  if ($max_cod_curso==1)
  {
    $query="select max(cod_curso) from Cursos";
    $res=Enviar($sock,$query);
    $cod_curso=RetornaLinha($res);
    $max_cod_curso=$cod_curso['max(cod_curso)']+1;
  }
  return($max_cod_curso);
}

/* **********************************************************************
   EnviaDadosCurso - Envia os dados para a base de dados
   Entrada: $sock - Socket da conex�o atual
   Saida: $cod_curso = C�digo do curso a ser criado
*/
function EnviaDadosCurso($sock,$cod_curso,$nome_curso,$duracao,$info,$publico_alvo,$tipo_inscricao,$num_alunos,$cod_pasta,$data,$nome_contato,$login,$email,$nome_inst,$avaliado)
{
  $query="insert into Cursos_requisicao (cod_curso,nome_curso,duracao,informacoes,publico_alvo,tipo_inscricao,num_alunos,cod_pasta,data,nome_contato,login_contato,email_contato,instituicao,avaliado) ";
  $query.="values (".VerificaNumeroQuery($cod_curso).",'".VerificaStringQuery($nome_curso)."','".VerificaStringQuery($duracao)."','".VerificaStringQuery($info)."','".VerificaStringQuery($publico_alvo)."','".VerificaStringQuery($tipo_inscricao)."',".VerificaNumeroQuery($num_alunos).",".((VerificaNumeroQuery($cod_pasta)=='')?'NULL':VerificaNumeroQuery($cod_pasta)).",".VerificaNumeroQuery($data).",'".VerificaStringQuery($nome_contato)."','".VerificaStringQuery($login)."','".VerificaStringQuery($email)."','".VerificaStringQuery($nome_inst)."','".VerificaStringQuery($avaliado)."')";
  $res=Enviar($sock,$query);
  return($res);
}

/* **********************************************************************
   RetornaDadosConfig - Retorna dado da tabela Config
   Entrada: $item - item da tabela de a ser retornado
   Saida: Retorna o valor correspondente ao item requisitado.
*/
function RetornaDadosConfig($item)
{
  $sock=Conectar("");
  $query="select valor from Config where item='".VerificaStringQuery($item)."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  Desconectar($sock);
  return $linha['valor'];
}

/********************************************************************
  Fun�ao RetornaCursosUsuario - Retorna os codigos dos cursos dos quais o usuario tem acesso
  Entradas: $sock - sock de conexao a base geral
  Saida:    array contendo os codigos dos cursos que o usuario tem acesso
********************************************************************/
function RetornaCursosUsuario($sock)
{
  /*
    I - aluno inv�ido ou inexistente
    i - Aluno inscrito
    a - Aluno desligado
    r - Aluno rejeitado
    f - Formador desligado
    v - Visitante desligado
    z - Colaborador desligado
  */

  $query  = "SELECT CUR.cod_curso ";
  $query .= "FROM Usuario USU ";
  $query .= "INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario) ";
  $query .= "INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso) ";
  $query .= "WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."' ";
  $query .= "AND binary USUC.tipo_usuario not in ('I', i', 'r', 'a', 'f', 'z', 'v')";

  $res = Enviar($sock, $query);

  return RetornaArrayLinhas($res);
}

/********************************************************************
  Fun�ao EnviaPedidoCriacaoDinamic - Envia pedido de criacao do curso dinamicamente
  Entradas: $dadosForm - dados do formulario que contem informacoes do curso a ser criado
  Saida:    Retorna respota XML
********************************************************************/

function EnviaPedidoCriacaoDinamic($dadosForm,$error1,$error2)
{
  $objResponse = new xajaxResponse();

  $nome_curso=$dadosForm['nome_curso'];
  $duracao=$dadosForm['duracao'];
  $num_alunos=$dadosForm['num_alunos'];
  $info=$dadosForm['info'];
  $publico_alvo=$dadosForm['publico_alvo'];
  $tipo_inscricao=$dadosForm['tipo_inscricao'];
  $nome_inst=$dadosForm['nome_inst'];
  $cod_pasta=$dadosForm['cod_pasta'];

  if(isset($_SESSION['cod_usuario_global_s'])){
    $nome_contato=$dadosForm['nome_contato'];
    $email=$dadosForm['email_cad'];
    $login=$dadosForm['login_cad'];
  }
  else{
    $nome_contato=$dadosForm['nome_usuario'];
    $email=$dadosForm['email'];
    $login=$dadosForm['login'];
    $senha=crypt($dadosForm['senha'],"AA");
  }

  $data=time();

  $avaliado='N';

  $nome_curso=EliminaScript($nome_curso);
  $duracao=EliminaScript($duracao);
  $info=EliminaScript($info);
  $publico_alvo=EliminaScript($publico_alvo);
  $tipo_inscricao=EliminaScript($tipo_inscricao);
  $nome_inst=EliminaScript($nome_inst);
  $nome_contato=EliminaScript($nome_contato);
  $email=EliminaScript($email);
  $login=EliminaScript($login);

  $nome_curso=LimpaConteudo($nome_curso);
  $duracao=LimpaConteudo($duracao);
  $info=LimpaConteudo($info);
  $publico_alvo=LimpaConteudo($publico_alvo);
  $tipo_inscricao=LimpaConteudo($tipo_inscricao);
  $nome_inst=LimpaConteudo($nome_inst);
  $nome_contato=LimpaConteudo($nome_contato);
  $email=LimpaConteudo($email);
  $login=LimpaConteudo($login);

  $info=Enter2BR($info);

  $sock = Conectar("");
  $cod_curso=RetornaCodCurso($sock);
  if(LoginRepetido($sock,$login)&&(!isset($_SESSION['cod_usuario_global_s']))){
    $objResponse->call("mostraFeedback", $error1, 'false');
  }
  else if(EmailRepetido($sock,$email)&&(!isset($_SESSION['cod_usuario_global_s']))){
    $objResponse->call("mostraFeedback",$error2, 'false');
  }
  else{
    if(!isset($_SESSION['cod_usuario_global_s'])){
      $sequencia = GeraChave(20);
      $cod_usuario=RetornaProximoCodigo($sock,"Usuario");
      $query="INSERT INTO Usuario(cod_usuario,login,senha,nome,email,cod_lingua,confirmacao)values(".$cod_usuario.",'".VerificaStringQuery($login)."','".VerificaStringQuery($senha)."','".VerificaStringQuery($nome_contato)."','".VerificaStringQuery($email)."','1','".VerificaStringQuery($sequencia)."')";
      $res=Enviar($sock,$query);

      /* Notificacao do Usuario via Email ************************************/

      $sock = Conectar('');
      $query="select diretorio from Diretorio where item='raiz_www'";
      $res=Enviar($sock,$query);
      $raiz_www_linha=RetornaLinha($res);
      $raiz_www = $raiz_www_linha[0];
      $host=RetornaConfiguracao($sock,"host");
      $lista_frases_email = RetornaListaDeFrases($sock,-2);
      $lista_frases_correio = RetornaListaDeFrases($sock,11);

      //87 - Confirmação de cadastro TelEduc

      $assunto = RetornaFraseDaLista($lista_frases_email, 87);
      $mensagem = "<p>".RetornaFraseDaLista($lista_frases_email, 88)."</p><p><a src='http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia."'>http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia."</a></p><p>".RetornaFraseDaLista($lista_frases_email, 89)."</p><p>".RetornaFraseDaLista($lista_frases_email, 78).",</p><p> ".RetornaFraseDaLista($lista_frases_email, 3)."</p>";

      // 115 - NAO_RESPONDA
      $remetente = RetornaFraseDaLista($lista_frases_correio, 115)."@".$host;
      $mensagem_html = MontaMsg($host, $raiz_www, '', $mensagem, $assunto, '', $nome_usuario);
      MandaMsg($remetente,$email,$assunto,$mensagem_html, '');
      $query="select diretorio from Diretorio where item='raiz_www'";
      $res=Enviar($sock,$query);
      $raiz_www_linha=RetornaLinha($res);
      $raiz_www = $raiz_www_linha[0];
      $host=RetornaConfiguracao($sock,"host");
      $lista_frases_email = RetornaListaDeFrases($sock,-2);
      $lista_frases_correio = RetornaListaDeFrases($sock,11);

    }
    $enviou=EnviaDadosCurso($sock,$cod_curso,$nome_curso,$duracao,$info,$publico_alvo,$tipo_inscricao,$num_alunos,$cod_pasta,$data,$nome_contato,$login,$email,$nome_inst,$avaliado);
    Desconectar($sock);

  }

  $objResponse->script("TrataEnvio('".$enviou."');");

  return $objResponse;
}

/********************************************************************
  Fun�ao RetornaTodosCursosUsuario - Retorna todos os codigos dos cursos que o usuario tem algum vinculo 
  Entradas: $sock - sock de conexao a base geral
  Saida:    array contendo os codigos dos cursos que o usuario tem algum vinculo
********************************************************************/
function RetornaTodosCursosUsuario($sock)
{
  $query  = "SELECT CUR.cod_curso ";
  $query .= "FROM Usuario USU ";
  $query .= "INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario) ";
  $query .= "INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso) ";
  $query .= "WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."'";// and ";
  // Se o usu�rio tentou se incriver em um curso, mas sua inscri��o foi cancelada, ele n�o possui v�nculo com aquele curso.
  //$query .= "      USUC.tipo_usuario <> 'r'";

  $res = Enviar($sock, $query);

  return RetornaArrayLinhas($res);
}

/*xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
  Fun�ao RetornaTodosCursosETipoUsuarioDeUsuario - Retorna todos os codigos dos cursos que o usuario tem algum vinculo e o tipo de usu�rio no curso 
  Entradas: $sock - sock de conexao a base geral
  Saida:    array contendo os codigos dos cursos que o usuario tem algum vinculo e o tipo de usu�rio no curso
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx*/
function RetornaTodosCursosETipoUsuarioDeUsuario($sock)
{
  $query  = "SELECT CUR.cod_curso, USUC.tipo_usuario ";
  $query .= "FROM Usuario USU ";
  $query .= "INNER JOIN Usuario_curso USUC on (USUC.cod_usuario_global = USU.cod_usuario) ";
  $query .= "INNER JOIN Cursos CUR on (CUR.cod_curso = USUC.cod_curso) ";
  $query .= "WHERE USU.cod_usuario = '".$_SESSION['cod_usuario_global_s']."'";

  $res = Enviar($sock, $query);

  return RetornaArrayLinhas($res);
}

/********************************************************************
  Fun��o RetornaDataInscricao - Retorna a data em que o usuario fez a inscri��o no curso
  Entradas: $cod_curso - c�digo do curso
  Saida: Data em que o usu�rio fez a inscri��o
********************************************************************/

function RetornaDataInscricao($cod_curso){

  $sock = Conectar("");
  $query  = "SELECT data_inscricao ";
  $query .= "FROM Usuario_curso ";
  $query .= "WHERE cod_usuario_global = '".$_SESSION["cod_usuario_global_s"]."' AND ";
  $query .= "      cod_curso = '".$cod_curso."'";

  $res = Enviar($sock, $query);
  $data = RetornaLinha($res);

  Desconectar($sock);

  return UnixTime2Data($data["data_inscricao"]);

}

/********************************************************************
  Fun��o RetornaTipoUsuario - Retorna o tipo_usuario da tabela Usuario_curso
  Entradas: $cod_curso - c�digo do curso
  Sa�da: Flag de tipo_usuario
*********************************************************************/

function RetornaTipoUsuario($sock, $cod_curso){

  $query  = "SELECT tipo_usuario ";
  $query .= "FROM Usuario_curso ";
  $query .= "WHERE cod_usuario_global = '".$_SESSION["cod_usuario_global_s"]."' AND ";
  $query .= "      cod_curso = '".$cod_curso."'";

  $res = Enviar($sock, $query);

  return RetornaLinha($res);
}

/* **********************************************************************
  Fun��o StatusUsuarioNoCurso - Retorna a condi��o do usuario no curso
  Entradas: $cod_curso - c�digo do curso
  Sa�da: String com a condi��o do usu�rio
*************************************************************************/

function StatusUsuarioNoCurso($sock, $cod_curso){
  $lista_frases = RetornaListaDeFrases($sock,-3);
  $status_usuario = RetornaTipoUsuario($sock, $cod_curso);
  if ($status_usuario == NULL)
    return RetornaFraseDaLista($lista_frases,232); 

  switch ($status_usuario['tipo_usuario']){
    case "i":
      /* 224 - Aluno Inscrito */
      return RetornaFraseDaLista($lista_frases,224);
    case "a":
      /* 234 - Aluno desligado */
      return RetornaFraseDaLista($lista_frases,234);
    case "r":
      /* 225 - Aluno n�o aceito */
      return RetornaFraseDaLista($lista_frases,225);
    case "A":
      /* 226 - Aluno aceito */
      return RetornaFraseDaLista($lista_frases,226);
    case "f":
      /* 228 - Formador desligado */
      return RetornaFraseDaLista($lista_frases,228);
    case "F":
      /* 227 - Formador */
      return RetornaFraseDaLista($lista_frases,227);
    case "z":
      /* 229 - Colaborador desligado*/
      return RetornaFraseDaLista($lista_frases,229);
    case "Z":
      /* 231 - Colaborador */
      return RetornaFraseDaLista($lista_frases,231);
    case "v":
      /* 233 - Vistante desligado */
      return RetornaFraseDaLista($lista_frases,233);
    case "V":
      /* 230 - Visitante */
      return RetornaFraseDaLista($lista_frases,230);
  }
}


/********************************************************************
  Fun�ao ParticipaDoCurso - Verifica se o usuario participa do curso
  Entradas:  $cod_curso - codigo do curso
  Saida:    'true' se o usuario pertence ao curso; 'false', caso contrario.
            Caso o usuario nao esteja logado, retornamos 'null'.
********************************************************************/

function ParticipaDoCurso($cod_curso)
{
  if(!empty($_SESSION['cod_usuario_global_s']))
  {
    $sock = Conectar("");
    //caso o usuario esteja logado, ve quais cursos ele possui acesso
    $meus_cursos = RetornaTodosCursosUsuario($sock);
    Desconectar($sock);
    if($meus_cursos=="") $meus_cursos=array();
    $tem_acesso_curso = false;

    //admtele tem acesso a todos os cursos
    if($_SESSION['cod_usuario_global_s'] == -1)
      $tem_acesso_curso = true;
    else
    {
      foreach ($meus_cursos as $meu_curso)
      {
        if($meu_curso['cod_curso'] == $cod_curso) {
          $tem_acesso_curso = true;
          break;
        }
      }
    }

    return $tem_acesso_curso;
  }
  else
    //caso contrario, retornamos null, indicando a impossibilidade de determinar se o usuario pertence ou nao ao curso 
    return null;
}

/********************************************************************
  Fun�ao RejeitadoDoCurso - Verifica se o usuario foi rejeitado do curso
  Entradas:  $cod_curso - codigo do curso
  Saida:    'true' se o usuario foi rejeitado do curso; 'false', caso contrario.
            Caso o usuario nao esteja logado, retornamos 'null'.
********************************************************************/

function RejeitadoDoCurso($cod_curso)
{
  if(!empty($_SESSION['cod_usuario_global_s']))
  {
    $sock = Conectar("");
    //caso o usuario esteja logado, ve quais cursos ele possui acesso
    $meus_cursos = RetornaTodosCursosETipoUsuarioDeUsuario($sock);
    Desconectar($sock);
    if($meus_cursos=="") $meus_cursos=array();
    $rejeitado_curso = false;

    //admtele tem acesso a todos os cursos
    //if($_SESSION['cod_usuario_global_s'] == -1)
    //  $tem_acesso_curso = true;
    //else
    //{ 
      foreach ($meus_cursos as $meu_curso)
      {
        if($meu_curso['cod_curso']    == $cod_curso && 
           $meu_curso['tipo_usuario'] == 'r') {
          $rejeitado_curso = true;
          break;
        }

      }
    //}

    return $rejeitado_curso;
  }
  else
    //caso contrario, retornamos null, indicando a impossibilidade de determinar se o usuario pertence ou nao ao curso 
    return null;
}

/* **************************************************************
  FichaUsuario - Retorna array com todos os dados do usuario
  Entradas: $sock - sock de conexao,
            $cod_usuario - codigo do usuario
  Saida:    Array com ['nome'] - nome do Usuario
                      ['endereco'] - endereco (rua e numero) do usuario
                      ['cidade'] - cidade do usuario
                      ['estado'] - sigla do estado do usuario
                      ['pais'] - pa�s do usuario
                      ['telefone'] - numero de telefone do usuario
                      ['email'] - email do usuario
                      ['data_nasc'] - data de nascimento do usuario(formato UnixTime)
                      ['sexo'] - sexo do usuario, M ou F
                      ['local_trab'] - Local de trabalho do usuario
                      ['profissao'] - Profissao do usuario
                      ['informacoes'] - informacoes adicionais sobre o usuario
                      ['cod_escolaridade'] - codigo da escolaridade do usuario
*/
function FichaUsuario($sock,$cod_usuario)
{
  $query="select nome,endereco,cidade,estado,pais,telefone,email,data_nasc,sexo,local_trab,profissao,informacoes,cod_escolaridade,rg from Usuario where cod_usuario=".VerificaNumeroQuery($cod_usuario);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return($linha);
}

/********************************************************************
  Fun�ao AtualizaDadosUsuarioDinamic - Atualiza dados os dados do usuario, dinamicamente
  Entradas:  $cod_curso - codigo do curso
  Saida:    'true' se o usuario pertence ao curso; 'false', caso contrario.
            Caso o usuario nao esteja logado, retornamos 'null'.
********************************************************************/

function AtualizaDadosUsuarioDinamic($dadosForm)
{
  $objResponse = new xajaxResponse();

  $cod_usuario = $_SESSION['cod_usuario_global_s'];
  $nome_usuario=$dadosForm['nome_usuario'];
  $endereco=$dadosForm['endereco'];
  $cidade=$dadosForm['cidade'];
  $estado=$dadosForm['estado'];
  $pais=$dadosForm['pais'];
  $telefone=$dadosForm['telefone'];
  $email=$dadosForm['email'];
  $data=$dadosForm['data_nascimento'];
  $sexo=$dadosForm['sexo'];
  $local=$dadosForm['local'];
  $profissao=$dadosForm['profissao'];  
  $informacoes=$dadosForm['informacoes'];
  $cod_escolaridade=$dadosForm['cod_escolaridade'];
  $rg=$dadosForm['rg'];
  $data_nascimento=$dadosForm['data_nascimento'];
//  $texto_feedback = $dadosForm['texto_feedback'];

  $data=Data2UnixTime($data_nascimento);

  $sock=Conectar("");
  AtualizaFichaUsuario($sock,$cod_usuario,$nome_usuario,$endereco,$cidade,$estado,$pais,$telefone,$email,$data,$sexo,$local,$profissao,$informacoes,$cod_escolaridade,$rg);
  Desconectar($sock);

  $objResponse->script("trataEnvio();");

  return $objResponse;
}

/* *****************************************************************************
  AtualizaFichaUsuario - Atualiza a ficha com os dados do usuario
  Entrada: $sock - sock de conexao
           $cod_usuario - codigo do usuario
           $nome - novo nome do usuario
           $endereco - novo endereco do usuario
           $cidade - nova cidade do usuario
           $estado - novo estado  do usuario
           $pais - novo pais do usuario
           $telefone - novo telefone do usuario
           $email - novo email do usuario
           $data_nasc - nova data de nascimento do usuario
           $sexo - novo sexo do usuario
           $local_trab - novo local de trabalho do usuario
           $profissao - nova profissao do usuario
           $informacoes - novas informacoes adicionais sobre o usuario
           $cod_escolaridade - nova escolaridade do usuario
           $rg - RG
  Saida:   Nenhuma
*/
function AtualizaFichaUsuario($sock,$cod_usuario,$nome,$endereco,$cidade,$estado,$pais,$telefone,$email,$data_nasc,$sexo,$local_trab,$profissao,$informacoes,$cod_escolaridade,$rg)
{
  $nome_limpo=LimpaTitulo($nome);
  $endereco_limpo=LimpaTitulo($endereco);
  $cidade_limpo=LimpaTitulo($cidade);
  $estado_limpo=LimpaTitulo($estado);
  $pais_limpo=LimpaTitulo($pais);
  $telefone_limpo=LimpaTitulo($telefone);
  $email_limpo=LimpaTitulo($email);
  $data_nasc_limpo=LimpaTitulo($data_nasc);
  $sexo_limpo=LimpaTitulo($sexo);
  $local_trab_limpo=LimpaTitulo($local_trab);
  $profissao_limpo=LimpaTitulo($profissao);
  $informacoes_limpo=LimpaTitulo($informacoes);
  $cod_escolaridade_limpo=LimpaTitulo($cod_escolaridade);
  $rg=LimpaTitulo($rg);

  $query="update Usuario set nome='".VerificaStringQuery($nome_limpo)."',endereco='".VerificaStringQuery($endereco_limpo)."',cidade='".VerificaStringQuery($cidade_limpo)."',estado='".VerificaStringQuery($estado_limpo)."',pais='".VerificaStringQuery($pais_limpo)."',telefone='".VerificaStringQuery($telefone_limpo)."',email='".VerificaStringQuery($email_limpo)."',data_nasc='".VerificaStringQuery($data_nasc_limpo)."',sexo='".VerificaStringQuery($sexo_limpo)."',local_trab='".VerificaStringQuery($local_trab_limpo)."',profissao='".VerificaStringQuery($profissao_limpo)."',informacoes='".VerificaStringQuery($informacoes_limpo)."',cod_escolaridade='".VerificaStringQuery($cod_escolaridade_limpo)."',rg='".VerificaStringQuery($rg)."' where cod_usuario=".VerificaNumeroQuery($cod_usuario);
  $res=Enviar($sock,$query);
}

/* *********************************************************************
   RetornaLoginUsuario - Retorna o login do usuario
   Entradas: $sock - sock aberto para a base geral
   Saida: login
*/
function RetornaLoginUsuario($sock)
{

  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $cod_usuario_global = $_SESSION['cod_usuario_global_s'];

  $query="select login from ".$dbnamebase.".Usuario where cod_usuario=".$cod_usuario_global;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $login_usuario=$linha[0];

  return ($login_usuario);
}

/* *********************************************************************
   RetornaEmailUsuario - Retorna o email do usuario
   Entradas: $sock - sock da base externa
            $cod_usuario - o cod_usuario_global do usuario  
   Saida: email
*/
function RetornaEmailUsuario($sock, $cod_usuario)
{
  $query="select email from Usuario where cod_usuario=".$cod_usuario."";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha['email']);
}


/********************************************************************
  Fun�ao AtualizaLoginUsuarioDinamic - Atualiza login do usuario, dinamicamente
  Entradas:  $cod_curso - codigo do curso
  Saida:    resposta XML
********************************************************************/

function AtualizaLoginUsuarioDinamic($dadosForm,$texto,$erro)
{
  $objResponse = new xajaxResponse();

  $cod_usuario = $_SESSION['cod_usuario_global_s'];
  $novo_login=$dadosForm['novo_login'];

  $sock=Conectar("");
  $res=ModificaLogin($sock,$cod_usuario,$novo_login);
  Desconectar($sock);

  if ($res == true)  $_SESSION['login_usuario_s'] = $novo_login;

  $objResponse->script("trataEnvio('".$res."','".$novo_login."');");
  //Seu login foi alterado com sucesso!
  if($res)
    $objResponse->call("mostraFeedback", $texto, 'true');
  //O login escolhido ja existe.Por favor, escolha um novo login.
  else $objResponse->call("mostraFeedback", $erro, 'false');
    return $objResponse;
}

/********************************************************************
  Fun�ao AtualizaSenhaUsuarioDinamic - Atualiza senha do usuario, dinamicamente
  Entradas:  $cod_curso - codigo do curso
  Saida:    resposta XML
********************************************************************/

function AtualizaSenhaUsuarioDinamic($dadosForm,$texto,$erro)
{
  $objResponse = new xajaxResponse();

  $cod_usuario = $_SESSION['cod_usuario_global_s'];
  $senha_antiga=$dadosForm['senha_antiga'];
  $nova_senha=$dadosForm['nova_senha'];
  $nova_senha2=$dadosForm['nova_senha2'];

  $sock=Conectar("");
  $lista_frases_email = RetornaListaDeFrases($sock,-2);
  $lista_frases_correio = RetornaListaDeFrases($sock,11);
  $host=RetornaConfiguracao($sock,"host");

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $raiz_www=$linha['diretorio'];


  $destino=RetornaEmailUsuario($sock,$cod_usuario);
  $res=ModificaSenha($sock,$cod_usuario,$senha_antiga,$nova_senha);
  Desconectar($sock);

  if($res)
  {
    /* 86 - Alteração de senha */
    $assunto=RetornaFraseDaLista($lista_frases_email,86);

    /* 81 - Sua alteração de senha no ambiente Teleduc foi efetuada com sucesso */
    /* 82 - Seu login continua sendo: */
    /* 83 - e sua nova senha passa a ser:*/
    /* 85 - O acesso deve ser feito a partir do endereco: */
    /* 78 - Atenciosamente */
    /* 3 - Ambiente de Administração do TelEduc */
   $mensagem = "<p>".RetornaFraseDaLista($lista_frases_email, 81)."</p><p>".RetornaFraseDaLista($lista_frases_email, 82)."<big><em><strong> ".$_SESSION['login_usuario_s']."</strong></em></big> ".RetornaFraseDaLista($lista_frases_email, 83)."<big><em><strong> ".$nova_senha."</strong></em></big></p><p>".RetornaFraseDaLista($lista_frases_email, 85)." http://$host$raiz_www/</p><br /><p>".RetornaFraseDaLista($lista_frases_email, 78).",</p><p>".RetornaFraseDaLista($lista_frases_email, 3).".</p>";

  /* 115 - NAO_RESPONDA */
  $remetente = RetornaFraseDaLista($lista_frases_correio, 115)."@".$host;
  $mensagem_envio = MontaMsg($host, $raiz_www, '', $mensagem, $assunto);
  MandaMsg($remetente,$destino,$assunto,$mensagem_envio);
  }
  $objResponse->script("trataEnvio('".$res."');");
  if($res){
    /* 10 - Senha alterada com sucesso. */
    $objResponse->call("mostraFeedback", $texto, 'true');
  }else{
    /* 17 - Ocorreu um erro ao registrar as alterações efetuadas. 
       verifique se sua senha antiga esta correta.*/
    $objResponse->call("mostraFeedback", $erro, 'false');

  }
  return $objResponse;
}


/* *****************************************************************************
  ModificaSenha - Se a senha fornecida for igual � da base, modifica-a para
                  a nova senha. Caso contrario, nao faz nada.
  Entrada: $sock - sock de conexao
           $cod_usuario - codigo do usuario
           $senha_antiga - senha atual que o usuario quer modificar
           $senha_nova - senha nova que o usuario escolheu
  Saida:   true - se conseguiu atualizar a senha
           false - caso contrario
*/
function ModificaSenha($sock,$cod_usuario,$senha_antiga,$senha_nova)
{
  $senha_antiga=crypt($senha_antiga,"AA");
  $senha_nova=crypt($senha_nova,"AA");
  $query="select senha from Usuario where cod_usuario=".VerificaNumeroQuery($cod_usuario);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  if ($linha[0]!=$senha_antiga)
     return false;
  else
  {
    $query="update Usuario set senha='".VerificaStringQuery($senha_nova)."' where cod_usuario=".VerificaNumeroQuery($cod_usuario);
    Enviar($sock,$query);
    return true;
  }
}

/* *****************************************************************************
  ModificaLogin - Se o login fornecido for igual ao da base, modifica-o para
                  o novo login. Caso contrario, nao faz nada.
  Entrada: $sock - sock de conexao
           $cod_usuario - codigo do usuario
           $novo_login - novo login que o usuario escolheu
  Saida:   true - se conseguiu atualizar o login
           false - caso contrario
*/
function ModificaLogin($sock, $cod_usuario, $novo_login)
{
  // Primeiro estamos verificando se o novo login escolhido j� existe
  if (LoginRepetido($sock, $novo_login))
    return false;

  // Caso n�o exista, alteramos o login
  $query="update Usuario set login='".VerificaStringQuery($novo_login)."' where cod_usuario=".VerificaNumeroQuery($cod_usuario); 
  $res=Enviar($sock, $query);

  if ($res)
    return true;
  else
    return false;
}

/* *****************************************************************************
  LoginRepetido - Verifica se o login escolhido pelo usuario ja existe
  Entrada: $sock - sock de conexao
           $novo_login - login que o usuario escolheu
  Saida:   true - se o login existe
           false - caso contrario
*/
function LoginRepetido($sock, $novo_login)
{
  $query="select login from Usuario where login='".VerificaStringQuery($novo_login)."'";
  $res=Enviar($sock, $query);

  if (RetornaNumLinhas($res) > 0)
    return true;

  return false;
}

/* *****************************************************************************
  EmailRepetido - Verifica se o e-mail escolhido pelo usuario ja existe
  Entrada: $sock - sock de conexao
           $email - email que o usuario escolheu
  Saida:   true - se o email existe
           false - caso contrario
*/
function EmailRepetido($sock, $email)
{
  $query="select email from Usuario where email='".VerificaStringQuery($email)."'";
  $res=Enviar($sock, $query);

  if (RetornaNumLinhas($res) > 0)
    return true;

  return false;
}

function EnviarConfirmacaoUsuarioDinamic($valor, $opcao)
{
  $objResponse = new xajaxResponse();

  $sock = Conectar("");
  $lista_frases_email   = RetornaListaDeFrases($sock,-2);
  $lista_frases_correio = RetornaListaDeFrases($sock,11);
  $flag = "";  /* Inicialmente, n�o h� flag ativiada */

  $query      = "select diretorio from Diretorio where item='raiz_www'";
  $res        = Enviar($sock,$query);
  $raiz_www_l = RetornaLinha($res);
  $raiz_www   = $raiz_www_l[0];
  $host       = RetornaConfiguracao($sock,"host");

  switch ($opcao)
  {
    case (1): /* $opcao = login */
    {
      $dados_usuario = RetornaDadosUsuarioLogin($sock,$valor);
      break;
    }
    case (2): /* $opcao = email */
    {
      $dados_usuario = RetornaDadosUsuarioEmail($sock,$valor);
      break;
    }
    default: /* $opcao = desconhecido */
      $dados_usuario = "";
  }

  if ($dados_usuario != "")
  {
    if ($dados_usuario["confirmacao"] != "")
    {
      // Gera uma nova sequencia de confirma��o para o usu�rio
      $sequencia = GeraChave(20);

      // Atualiza no BD, essa nova chave do usuario
      $query = "update Usuario set confirmacao='".$sequencia."' ";
      $query.= "where (cod_usuario = ".$dados_usuario['cod_usuario'].")";
      $res   = Enviar($sock,$query);

      // 87 - Confirma��o de cadastro TelEduc
      $assunto = RetornaFraseDaLista($lista_frases_email, 87);

      // 88 - Se voc� se cadastrou no ambiente TelEduc, favor confirmar seu e-mail
      //  clicando no link abaixo. Ap�s isso, sua inscri��o ser� efetuada com sucesso. 
      //  Voc� receber� neste e-mail a sua identifica��o e senha, e poder� acessar o ambiente normalmente.
      // 89 - Caso contr�rio, favor desconsiderar esta mensagem.
      // 78 - Atenciosamente
      // 03 - Ambiente de Administra��o do TelEduc

      $mensagem  = "<p>".RetornaFraseDaLista($lista_frases_email, 88)."</p>";
      $mensagem .= "<p>";
      $mensagem .= "<a src='http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$dados_usuario['cod_usuario']."&s=".$sequencia."'>http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$dados_usuario['cod_usuario']."&s=".$sequencia."</a>";
      $mensagem .= "</p>";
      $mensagem .= "<p>".RetornaFraseDaLista($lista_frases_email, 89)."</p>";
      $mensagem .= "<p>".RetornaFraseDaLista($lista_frases_email, 78).",</p>";
      $mensagem .= "<p>".RetornaFraseDaLista($lista_frases_email, 3)."</p>";

      // 115 - NAO_RESPONDA
      $remetente = RetornaFraseDaLista($lista_frases_correio, 115)."@".$host;
      $mensagem_html = MontaMsg($host, $raiz_www, '', $mensagem, $assunto, '', $dados_usuario['nome']);
      MandaMsg($remetente, $dados_usuario['email'], $assunto, $mensagem_html, '');
      $flag = 0; /* Email com autentica��o enviada */
    }
    else
    {
      $flag = 2; /* usuario j� fez a confirma��o */
    }
  }
  else
  {
    $flag = 1; /* Usuario n�o encontrado */
  }

  Desconectar($sock);

  $objResponse->script("trataEnvio(".$flag.");");
  return $objResponse;
}

/********************************************************************
  Fun�ao CadastraDadosUsuarioDinamic - Cadastra usuario, dinamicamente
  Entradas:  $dadosForm - dados do formulario de cadastro
  Saida:    reposta XML
********************************************************************/
function CadastraDadosUsuarioDinamic($dadosForm,$erro1,$erro2,$erro3,$texto)
{

  $objResponse = new xajaxResponse();

  $login            = $dadosForm['login'];
  $senha            = crypt($dadosForm['senha'],"AA");
  $nome_usuario     = $dadosForm['nome_usuario'];
  $endereco         = $dadosForm['endereco'];
  $cidade           = $dadosForm['cidade'];
  $estado           = $dadosForm['estado'];
  $pais             = $dadosForm['pais'];
  $email            = $dadosForm['email'];
  $rg               = $dadosForm['rg'];
  $telefone         = $dadosForm['telefone'];
  $sexo             = $dadosForm['sexo'];
  $local            = $dadosForm['local'];
  $profissao        = $dadosForm['profissao'];  
  $informacoes      = $dadosForm['informacoes'];
  $cod_escolaridade = $dadosForm['cod_escolaridade'];
  $data_nascimento  = $dadosForm['data_nascimento'];

  $data=Data2UnixTime($data_nascimento);

  $sock=Conectar("");

  $hash = false;
  $resultado = $dadosForm['resultado'];
  if($resultado  != $_SESSION['answer']){
    $hash = true;
  }

  $flag = 0;
  if(LoginRepetido($sock,$login)){
    $flag=1;
    $objResponse->call("mostraFeedback", $erro1, 'false');
  }else if(EmailRepetido($sock,$email)){
    $flag=2;
    $objResponse->call("mostraFeedback", $erro2, 'false');
  }else if($hash){
    $flag=3;
    $objResponse->call("mostraFeedback", $erro3, 'false');
  }else
  {
    $cod_usuario=RetornaProximoCodigo($sock,"Usuario");

    //Gera uma sequencia aletoria de 20 caracteres
    $sequencia = GeraChave(20);

    $query="insert into Usuario (cod_usuario,login,senha,nome,rg,email,telefone,endereco,cidade,estado,pais,data_nasc,sexo,local_trab,profissao,cod_escolaridade,informacoes,data_inscricao,cod_lingua,confirmacao) values (".VerificaNumeroQuery($cod_usuario).",'".VerificaStringQuery($login)."','".VerificaStringQuery($senha)."','".VerificaStringQuery($nome_usuario)."','".VerificaStringQuery($rg)."','".VerificaStringQuery($email)."','".VerificaStringQuery($telefone)."','".VerificaStringQuery($endereco)."','".VerificaStringQuery($cidade)."','".VerificaStringQuery($estado)."','".VerificaStringQuery($pais)."','".VerificaStringQuery($data)."','".VerificaStringQuery($sexo)."','".VerificaStringQuery($local)."','".VerificaStringQuery($profissao)."','".VerificaStringQuery($cod_escolaridade)."','".VerificaStringQuery($informacoes)."','".time()."',1,'".VerificaStringQuery($sequencia)."')";

    $res=Enviar($sock,$query);

    /* Notificacao do Usuario via Email ************************************/
    $sock = Conectar('');
    $query="select diretorio from Diretorio where item='raiz_www'";
    $res=Enviar($sock,$query);
    $raiz_www_linha=RetornaLinha($res);
    $raiz_www = $raiz_www_linha[0];
    $host=RetornaConfiguracao($sock,"host");

    $lista_frases_email = RetornaListaDeFrases($sock,-2);
    $lista_frases_correio = RetornaListaDeFrases($sock,11);

    $parametros_curso = "";
    if (isset($_GET["cod_curso"]) && !empty($_GET["cod_curso"])) {
      $parametros_curso .= "&c=".$_GET["cod_curso"];
      if (isset($_GET["tipo_curso"]) && !empty($_GET["tipo_curso"]))
        $parametros_curso .= "&t=".$_GET["tipo_curso"];
    }

    //87 - Confirmação de cadastro TelEduc
    $assunto = RetornaFraseDaLista($lista_frases_email, 87);
    $mensagem = "<p>".RetornaFraseDaLista($lista_frases_email, 88)."</p><p><a href='http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."'>http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."</a></p><p>".RetornaFraseDaLista($lista_frases_email, 89)."</p><p>".RetornaFraseDaLista($lista_frases_email, 78).",</p><p> ".RetornaFraseDaLista($lista_frases_email, 3)."</p>";
    /*
    // 74 - Bem-Vindo ao TelEduc!
    $assunto = RetornaFraseDaLista($lista_frases_email, 74);

    // 75 - Para acessar o ambiente, a sua Identificação é:
    // 76 - e a sua senha é:
    // 77 - O acesso deve ser feito a partir do endereco:
    // 78 - Atenciosamente
    // 3 - Ambiente de Administração do TelEduc

    $mensagem = "<p>".RetornaFraseDaLista($lista_frases_email, 75)." <big><em><strong>".$login."</strong></em></big> ".RetornaFraseDaLista($lista_frases_email, 76)."<big><em><strong> ".$dadosForm['senha']."</strong></em></big></p><p>".RetornaFraseDaLista($lista_frases_email, 77)." http://$host$raiz_www/</p><br /><p>".RetornaFraseDaLista($lista_frases_email, 78).",</p><p> ".RetornaFraseDaLista($lista_frases_email, 3)."</p>";

    */

    // 115 - NAO_RESPONDA
    $remetente = RetornaFraseDaLista($lista_frases_correio, 115)."@".$host;
    $mensagem_html = MontaMsg($host, $raiz_www, '', $mensagem, $assunto, '', $nome_usuario);
    MandaMsg($remetente,$email,$assunto,$mensagem_html, '');

    // Fim da Notificacao
    $objResponse->call("mostraFeedback", $texto, 'true');
  }

  Desconectar($sock);


  $objResponse->script("trataEnvio('".$flag."');");

  return $objResponse;
}

/********************************************************************
  Funcao GeraChave - Gera uma sequencia aleatoria de caracteres
  Entrada:  $digitos - tamanho da sequencia gerada
  Saida:    String com a sequencia gerada
********************************************************************/
function GeraChave($digitos) {
  $chave = '';
  $caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; 
  srand((double)microtime()*1000000); 
  for($i=0; $i<$digitos; $i++) { 
    $chave .= $caracteres[rand()%strlen($caracteres)]; 
  }
  return $chave;
}

/********************************************************************
  Funcao ConfirmaUsuario - Verifica se o código enviado por email esta correto
                           Se estiver, o email é valido e o cadastro é efetivado
  Entradas:  $user - Código do usuário
             $seq - Sequencia de caracteres enviada por email
  Saida:     Boolean - True se foi efetivado, false caso contrario
********************************************************************/
function ConfirmaUsuario($user, $seq) {

  $sock=Conectar("");

  //Seleciona a sequencia de caracteres cadastrada para este usuario
    $query="select confirmacao from Usuario where cod_usuario='".VerificaNumeroQuery($user)."'";

  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  $seq_bd = $linha['confirmacao'];

  if ((strcmp($seq, $seq_bd) == 0) || ($seq_bd == NULL)) {
    $query="UPDATE Usuario SET confirmacao=NULL WHERE cod_usuario=".VerificaNumeroQuery($user);
    $res=Enviar($sock,$query);
    Desconectar($sock);
    return true;
  }
  else {
    Desconectar($sock);
    return false;
  }
}

function CadastrarLogar($form,$cod_curso,$tipo_curso)
{
  $objResponse = new xajaxResponse();

  $login=$form['login'];
  $senha=crypt($form['senha'],"AA");

  $sock=Conectar("");

  //Seleciona senha e código global do usuario
  $query="select senha, cod_usuario from Usuario where login='".VerificaStringQuery($login)."'";


  $res=Enviar($sock,$query);
  $num=RetornaNumLinhas($res);
  $linha=RetornaLinha($res);

  $cod_usuario = $linha['cod_usuario'];

  $_SESSION['cod_usuario_global_s'] = $cod_usuario;
  $_SESSION['cod_usuario_s'] = (!empty($cod_curso)) ? RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'],$cod_curso) : "";
  $_SESSION['login_usuario_s'] = $login;
  //$_SESSION['tipo_usuario_s'] = "";
  $_SESSION['cod_lingua_s'] = $cod_lingua; //??
  //$_SESSION['visitante_s'] = $cod_visitante_s; //??
  //$_SESSION['visao_formador_s'] = 1;

  Desconectar($sock);

  $objResponse->redirect("inscricao.php?cod_curso=".$cod_curso."&tipo_curso=".$tipo_curso."");

  return $objResponse;
}

/********************************************************************
  Fun�ao AtualizaIdiomaDinamic - Atualiza o codigo da lingua do usuario
  Entradas:  $dadosForm - dados do formulario de mudança de codigo de lingua
  Saida:    reposta XML
********************************************************************/
function AtualizaIdiomaDinamic($dadosForm)
{
  $objResponse = new xajaxResponse();

  $cod_usuario=$dadosForm['cod_usuario'];
  $cod_lin=$dadosForm['cod_lin'];

  $sock=Conectar("");
  $query="update Usuario set cod_lingua=".VerificaNumeroQuery($cod_lin)." where cod_usuario=".VerificaNumeroQuery($cod_usuario);
  $res=Enviar($sock,$query);
  Desconectar($sock);

  $objResponse->script("window.location.reload();");

  //  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/********************************************************************
  Fun�ao ExcluiUsuarios - Exclui usuarios do servidor
  Entradas:  $lista_usuarios - array contendo os codigos dos usuarios a serem excluidos
             $ordem - indica o tipo de ordenacao (nome ou data)
             $pag_atual - indica em qual pagina o administrador estava ao excluir o usuario
  Saida:     resposta XML
********************************************************************/
function ExcluiUsuarios($lista_usuarios,$ordem,$pag_atual)
{
  $objResponse = new xajaxResponse();

  $sock=Conectar("");

  for($i=0;$i<count($lista_usuarios);$i++)
  {
      $query = "delete from Usuario where cod_usuario =".VerificaNumeroQuery($lista_usuarios[$i]);
      Enviar($sock, $query);
      $query = "delete from Usuario_config where cod_usuario_global =".VerificaNumeroQuery($lista_usuarios[$i]);
      Enviar($sock, $query);
      $query = "delete from Usuario curso cod_usuario_global =".VerificaNumeroQuery($lista_usuarios[$i]);
      Enviar($sock, $query);
  }

  Desconectar($sock);

  $objResponse->script("document.location='admcadastro.php?ordem=".$ordem."&pag_atual=".$pag_atual."';");

  //  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* *********************************************************************
  RetornaDadoUssuario - Retorna dados do usuario da tabela Usuario
  Entrada: $sock - BASE GERAL
            $cod_usuario - codigo do usuario
  Saida: array com os dados do usuario que possui o codigo de usuario dado, se houver
*/
function RetornaDadosUsuario($sock,$cod_usuario)
{

  $query="select * from Usuario where cod_usuario=".VerificaNumeroQuery($cod_usuario);
  $res=Enviar($sock,$query);
  return RetornaLinha($res);
}

/* *********************************************************************
  RetornaDadoUsuarioLogin - Retorna dados do usuario da tabela Usuario que possua um determinado login
  Entrada: $sock - BASE GERAL
           $login - login do usuario
  Saida: array com os dados do usuario que possui o login dado, se houver
*/
function RetornaDadosUsuarioLogin($sock,$login)
{
  $query="select cod_usuario,nome,email,login,confirmacao from Usuario where login='".VerificaStringQuery($login)."'";
  $res=Enviar($sock,$query);
  return RetornaLinha($res); 
}

/* *********************************************************************
  RetornaDadoUsuarioEmail - Retorna dados do usuario da tabela Usuario que possua um determinado email
  Entrada: $sock - BASE GERAL
           $email - email do usuario
  Saida: array com os dados do usuario que possui o email dado, se houver
*/
function RetornaDadosUsuarioEmail($sock,$email)
{
  $query="select cod_usuario,nome,login,email,confirmacao from Usuario where email='".VerificaStringQuery($email)."'";
  $res=Enviar($sock,$query);


  return RetornaLinha($res); 
}


/* *********************************************************************
  RetornaDadosTodosUsuariosEmail - Retorna dados de todos os usuarios da tabela Usuario que possua um mesmo email
  Entrada: $sock - BASE GERAL
           $email - email do usuario
  Saida: array com todas as linhas dos dados dos usuarios que possui o email dado, se houver
*/
function RetornaDadosTodosUsuariosEmail($sock,$email)
{
  $query="select cod_usuario,nome,login,email,confirmacao from Usuario where email='".VerificaStringQuery($email)."'";
  $res=Enviar($sock,$query);


  return RetornaArrayLinhas($res); 
}


/* *********************************************************************
  EnviarSenhaLoginUsuarioDinamic - Cria uma nova senha e a envia para o usuario juntamente com seu login
  Entrada: $valor - login ou email do usuario
  Saida:   reposta XML
*/
function EnviarSenhaLoginUsuarioDinamic($valor,$opt)
{
  $objResponse = new xajaxResponse();

  $sock=Conectar("");
  $lista_frases_mensagem = RetornaListaDeFrases($sock,0);
  $lista_frases_correio = RetornaListaDeFrases($sock,11);

  $host = RetConfig($sock, "host");
  $raiz_www = RetDiretorio($sock, "raiz_www");
  $flag="";
  /* Obt�m os dados do administrador: nome e e-mail para o remetente da mensagem. */
  $usuario_adm = RetornaDadosUsuario($sock, -1);
  $dados_usuario = "";
  /* O email ou login para a recuperaçãop de senha não pode ser referente ao administrador. */
  if($opt == 1 && $valor != $usuario_adm['login'])
    $dados_usuario = RetornaDadosUsuarioLogin($sock,$valor);
  else if ($valor != $usuario_adm['email'])
    $dados_usuario = RetornaDadosTodosUsuariosEmail($sock,$valor);

  if($dados_usuario != "")
  {

    // 138 - Login e senha do TelEduc
    $assunto=RetornaFraseDaLista($lista_frases_mensagem,138);
    if(count($dados_usuario) > 1){
      // 95 - Prezado(a)
      $mensagem =RetornaFraseDaLista($lista_frases_mensagem,95)." ".$dados_usuario[0]['nome'].",\n\n";
      $mensagem.="<br />";
      $mensagem.="<br />";
      // 38 - Esse e-mail possui mais de um login e senha associados. Segue abaixo a lista de logins e senhas cadastrados
      $mensagem.=RetornaFraseDaLista($lista_frases_mensagem,309).".\n\n";
      $mensagem.="<br />";
      $mensagem.="<br />";
      foreach($dados_usuario as $usuario){
        $senha = GeraSenha();
        $senha_crypt = crypt($senha,"AA");
        // 15 - Nome
        $mensagem.=RetornaFraseDaLista($lista_frases_mensagem,15).": ".$usuario['nome']."\n";
        $mensagem.="<br />";
        // 53 - Login
        $mensagem.=RetornaFraseDaLista($lista_frases_mensagem,53).": ".$usuario['login']."\n";
        $mensagem.="<br />";
        // 309 - Senha
        $mensagem.=RetornaFraseDaLista($lista_frases_mensagem,310).": ".$senha."\n\n";
        AtualizaSenhaUsuario($sock, $usuario['cod_usuario'], $senha_crypt);
        $mensagem.="<br />";
        $mensagem.="<br />";
      }

      //$destinatario = $dados_usuario[0]['email'];
      //var_dump($destinatario);
      //exit();

    }else{
      $senha = GeraSenha();
      $senha_crypt = crypt($senha,"AA");
      // 95 - Prezado(a)
      $mensagem =RetornaFraseDaLista($lista_frases_mensagem,95)." ".$dados_usuario[0]['nome'].",\n\n";
      $mensagem.="<br />";
      $mensagem.="<br />";
      // 140 - Segue abaixo o seu login e senha conforme solicitado para o curso
      $mensagem.=RetornaFraseDaLista($lista_frases_mensagem,140).".\n\n";
      $mensagem.="<br />";
      // 67 - Seu login � 
      $mensagem.="<p>".RetornaFraseDaLista($lista_frases_mensagem,67)." ".$dados_usuario[0]['login']." ";
      // 68 - e sua senha � 
      $mensagem.=RetornaFraseDaLista($lista_frases_mensagem,68)." ".$senha."\n\n</p>";
      $mensagem.="<br />";
      AtualizaSenhaUsuario($sock, $dados_usuario[0]['cod_usuario'], $senha_crypt);

      //$destinatario = $dados_usuario['email'];
      //var_dump($destinatario);
      //exit();

    }
    // 139 - Atenciosamente,
    $mensagem.=RetornaFraseDaLista($lista_frases_mensagem,139)."\n";
    $mensagem.="<br />";
    $mensagem.=$usuario_adm['nome']."\n\n"; 
    // Administra��o TelEduc


    // 115 - NAO_RESPONDA
    $remetente = RetornaFraseDaLista($lista_frases_correio, 115)."@".$host;

    $destinatario = $dados_usuario[0]['email'];


    $mensagem_envio = MontaMsg($host, $raiz_www, '', $mensagem, $assunto);
    //var_dump($remetente);
    //var_dump($destinatario);
    //var_dump($assunto);
    //var_dump($mensagem_envio);
    //exit();
    MandaMsg($remetente, $destinatario, $assunto, $mensagem_envio);
  }
  else
  {
    $flag = "1";
  }

  Desconectar($sock);

  $objResponse->script("trataEnvio('".$flag."');");

  //  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* *********************************************************************
  RetConfig - Retorna o valor associado a um item da tabela Config.
  Entrada: $sock - sock interno ou EXTERNO,
           $item - item cujo valor � requisitado.

  Saida  : valor associado ao item requisitado.
*/
function RetConfig($sock,$item)
{
  $query = "select valor from Config where item = '".VerificaStringQuery($item)."'";
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);

  return($linha['valor']);
}

/* **********************************************************************
  RetDiretorio - Retorna o Diretorio pedido
  Entrada: $item - item da tabela de diretorios a ser retornado
  Saida: Retorna o diretorio
*/
function RetDiretorio($sock,$item)
{
  $query="select diretorio from Diretorio where item='".VerificaStringQuery($item)."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['diretorio'];
}

/* *********************************************************************
   AtualizaSenhaUsuario - Atualiza a senha do Usu�rio.
   Entrada: $sock - BASE GERAL
            $cod_usuario - Codigo do Usuario
            $senha - Senha j� criptografada
   Saida: nenhuma
*/
function AtualizaSenhaUsuario($sock,$cod_usuario,$senha)
{
  $query="update Usuario set senha='".VerificaStringQuery($senha)."' where cod_usuario=".VerificaNumeroQuery($cod_usuario);
  $res=Enviar($sock,$query);
}

/* *********************************************************************
   CursoValido - Retorna estado do curso
   Entrada: $sock - BASE GERAL
            $cod_curso
   Saida: curso_valido
*/
function CursoValido($sock, $cod_curso){
    $query = "select cod_curso from Cursos where cod_curso = ".VerificaNumeroQuery($cod_curso);
    $res = Enviar($sock,$query);
    $linha = RetornaLinha($res);
    $curso_valido = $linha[0];
    return($curso_valido);
}

?>