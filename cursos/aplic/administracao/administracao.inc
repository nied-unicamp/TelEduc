<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/administracao.inc

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

/* ==========================================================
   ARQUIVO : cursos/aplic/administracao/administracao.inc
   ========================================================== */

/* *********************************************************************
   RetConfig - Retorna o valor associado a um item da tabela Config.
   Entrada: $sock - sock interno ou EXTERNO,
            $item - item cujo valor � requisitado.

   Saida  : valor associado ao item requisitado.
*/
function RetConfig($sock,$item)
{
  $query = "select valor from Config where item = '".$item."'";
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
  $query="select diretorio from Diretorio where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['diretorio'];
}

/* *********************************************************************
   HouveAlteracoes - Verifica se houve altera��es nas bases de dados
                     utilizada pela administra��o.
   Entrada: $sock - BASE DO CURSO ou BASE EXTERNA
            $cod_curso= codigo do curso,
   Saida:   array ['cod_curso'] = codigo do curso
                  ['nome_curso'] = nome do curso
                  ['inscricao_inicio'] = timestamp do dia de inicio da inscricao
                  ['inscricao_fim'] = timestamp do dia de fim da inscricao
                  ['curso_inicio'] = timestamp do dia de inicio do curso
                  ['curso_fim'] = timestamp do dia de fim do curso
                  ['informacoes'] = informacoes sobre o curso
                  ['publico_alvo'] = Publico esperado
                  ['tipo_inscricao'] = tipo de inscricao
                  ['acesso_visitante'] = 'A' - Permite acesso de visitante
                                         'N' - N�o permite
*/
function HouveAlteracoes($sock,$cod_curso,$cod_usuario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];
  /* Cria um array com 4 elementos com valores iniciais iguais a zero. */
  /* O valor de cada elemento ser� utilizado para adicionar a tag bold */
  /* nas ferramentas onde houve modifica��es.                          */

  $alt = array(0,  /* Modifica��es no cronograma do curso.         */
               0,  /* Modifica��es na escolha de ferramentas.      */
               0,  /* Modifica��es no destaque de ferramentas.     */
               0); /* Nova requisi��o de inscri��o.                */

  /* Obt�m o hor�rio do penltimo acesso do usu�rio e compara-o com */
  /* as datas das tabelas.                                          */
  $penultac = PenultimoAcesso($sock, $cod_usuario, "");

  /* Verifica se houve modifica��o no cronograma do curso.          */
  $query = "select _timestamp 'data' from Cursos";
  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);
  if ($tupla['data'] > $penultac)
    $alt[0] = 1;

  /* Verifica se houve modifica��o na escolha das ferramentas.      */
  unset($tupla);
  $query = "select max(_timestamp) 'data' from Curso_ferramentas";
  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);
  if ($tupla['data'] > $penultac)
    $alt[1] = 1;

  /* Verifica se houve modifica��o no destaque das ferramentas.     */
  unset($tupla);
  $query = "select max(data) 'data' from Ferramentas_destaque";
  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);
  if ($tupla['data'] > $penultac)
    $alt[2] = 1;

  /* Verifica se h� uma nova requisi��o de inscri��o.               */
  unset($tupla);
  $query = "select max(data_inscricao) 'data' from ".$dbnamebase.".Usuario_curso where tipo_usuario = 'a' and cod_curso='".$cod_curso."'";
  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);
  if ($tupla['data'] > $penultac)
    $alt[3] = 1;

  return($alt);
}

/* *********************************************************************
   RetornaDadosCurso - Atualiza o cronograma do curso
   Entrada: $sock - BASE DO CURSO ou BASE EXTERNA
            $cod_curso= codigo do curso,
   Saida:   array ['cod_curso'] = codigo do curso
                  ['nome_curso'] = nome do curso
                  ['inscricao_inicio'] = timestamp do dia de inicio da inscricao
                  ['inscricao_fim'] = timestamp do dia de fim da inscricao
                  ['curso_inicio'] = timestamp do dia de inicio do curso
                  ['curso_fim'] = timestamp do dia de fim do curso
                  ['informacoes'] = informacoes sobre o curso
                  ['publico_alvo'] = Publico esperado
                  ['tipo_inscricao'] = tipo de inscricao
                  ['acesso_visitante'] = 'A' - Permite acesso de visitante
                                         'N' - N�o permite
                  ['cod_lingua'] = codigo da lingua padr�o do curso (atual)
*/
function RetornaDadosCursoAdm($sock,$cod_curso)
{
  $query ="select * from Cursos where cod_curso=".$cod_curso;
  $res=Enviar($sock,$query);
  return (RetornaLinha($res));
}

/* *********************************************************************
   CadastraDadosCurso - Atualiza o cronograma do curso 
   Entrada: $sock - BASE DO CURSO ou BASE EXTERNA
            $cod_curso= codigo do curso,
            $nome_curso = nome do curso
            $inscricao_inicio = timestamp do dia de inicio da inscricao
            $inscricao_fim = timestamp do dia de fim da inscricao
            $curso_inicio = timestamp do dia de inicio do curso
            $curso_fim = timestamp do dia de fim do curso
            $informacoes = informacoes sobre o curso
            $publico_alvo = Publico esperado
            $tipo_inscricao = tipo de inscricao
            $acesso_visitante = 'A' - Permite acesso de visitante
                                'N' - N�o permite
            $cod_lingua - Codigo da lingua padr�o a ser utilizada no curso
            $horario - hora atual (em que se est� realizando a atualiza��o)
   Saida: true se tudo ok.
*/
function CadastraDadosCurso($sock,$cod_curso,$nome_curso,$inscricao_inicio,$inscricao_fim,$curso_inicio,$curso_fim,$informacoes,$publico_alvo,$tipo_inscricao,$acesso_visitante,$cod_lingua,$horario)
{
  if ($acesso_visitante!='A')
    $acesso_visitante='N';

  $nome_curso=EliminaScript($nome_curso);
  $informacoes=EliminaScript($informacoes);
  $publico_alvo=EliminaScript($publico_alvo);
  $tipo_inscricao=EliminaScript($tipo_inscricao);

  $query ="update Cursos set nome_curso='".$nome_curso."',";
  $query.=" inscricao_inicio=".Data2Unixtime($inscricao_inicio).",";
  $query.=" inscricao_fim=".Data2Unixtime($inscricao_fim).",";
  $query.=" curso_inicio=".Data2Unixtime($curso_inicio).",";
  $query.=" curso_fim=".Data2Unixtime($curso_fim).",";
  $query.=" informacoes='".$informacoes."',";
  $query.=" publico_alvo='".$publico_alvo."',";
  $query.=" tipo_inscricao='".$tipo_inscricao."',";
  $query.=" cod_lingua=".$cod_lingua.",";
  $query.=" acesso_visitante='".$acesso_visitante."',";
  $query.=" _timestamp=".$horario;
  $query.=" where cod_curso=".$cod_curso;

  $res=Enviar($sock,$query);
  return $res;
}

/* *********************************************************************
   CadastraCronograma - Atualiza o cronograma do curso
   Entrada: $sock - BASE DO CURSO ou BASE EXTERNA
            $cod_curso= codigo do curso,
            $inscricao_inicio = timestamp do dia de inicio da inscricao
            $inscricao_fim = timestamp do dia de fim da inscricao
            $curso_inicio = timestamp do dia de inicio do curso
            $curso_fim = timestamp do dia de fim do curso
            $horario = hora atual (em que se est� realizando a atualiza��o)
   Saida: true se tudo ok.
*/
function CadastraCronograma($sock,$cod_curso,$inscricao_inicio,$inscricao_fim,$curso_inicio,$curso_fim,$horario)
{
  $query ="update Cursos set ";
  $query.=" inscricao_inicio=".Data2Unixtime($inscricao_inicio).",";
  $query.=" inscricao_fim=".Data2Unixtime($inscricao_fim).",";
  $query.=" curso_inicio=".Data2Unixtime($curso_inicio).",";
  $query.=" curso_fim=".Data2Unixtime($curso_fim).",";
  $query.=" _timestamp=".$horario;
  $query.=" where cod_curso=".$cod_curso;

  $res=Enviar($sock,$query);
  return $res;
}

/* *********************************************************************
   CompletaFerramentasCurso - Retorna a lista de ferramentas com status e
                             acessibilidade
   Entrada: $sock - BASE DO CURSO
            $lista_ferramentas - Lista de Ferramentas do Ambiente (RetornaListaFerramentas)
            $ferramentas_curso - Lista de Ferrametnas do Curso (RetornaFerramentasCurso)
   Saida: $ferramentas_curso modificada.
*/
function CompletaFerramentasCurso($sock,$lista_ferramentas,$ferramentas_curso)
{
  $lista1=$lista_ferramentas;
  $lista2=$ferramentas_curso;
  foreach($lista1 as $cod_ferramenta => $linha)
  {
    if (!isset($lista2[$cod_ferramenta]['status']))
    {
      if ($cod_ferramenta==1 || $cod_ferramenta==16 || $cod_ferramenta==17)
         $lista2[$cod_ferramenta]['status']='A';
      else
         $lista2[$cod_ferramenta]['status']='D';
      $lista2[$cod_ferramenta]['acesso_visitante']='N';
      $query="insert into Curso_ferramentas (cod_ferramenta,status,acesso_visitante, _timestamp) values (".$cod_ferramenta.",'".$lista2[$cod_ferramenta]['status']."','N', ".time().")";
      $res=Enviar($sock,$query);
    }
  }
  return ($lista2);
}

/* *********************************************************************
   MarcaFerramentas - Marca as ferramentas especificadas pelo array
     $lista_ferramentas. Se n�o houver ferramentas a serem marcadas
     insere na base com o cod_ferramenta -1 (inv�lido).

   Entrada: $sock - BASE DO CURSO,
            $lista_ferramentas - array com cod_ferramenta.
   Saida: true se bem-sucedido, false do contr�rio
*/
function MarcaFerramentas($sock, $lista_ferramentas)
{
  /* Obt�m a hora atual */
  $time = time();
  $res = true;

  /* Se n�o houver ferramentas marcadas, insira -1 como c�digo da ferramenta na base. */
  if (($total = count($lista_ferramentas)) == 0)
  {
    $query = "insert into Ferramentas_destaque values (-1, ".$time.")";
    $res = Enviar($sock, $query);
  }
  else
  {
    for ($i = 0; (($i < $total) && ($res == true)); $i++)
    {
      $query = "insert into Ferramentas_destaque values (".$lista_ferramentas[$i].", ".$time.")";
      $res = Enviar($sock, $query);
    }
  }
  AtualizaFerramentasNova($sock,0,'F');
  return($res);
}

/* *********************************************************************
   RetornaListaLogins - Retorna lista de logins existentes para confer�ncia
   Entrada: $sock - BASE DO CURSO
   Saida: array com [$login]==1 se login existe
*/
function RetornaListaLogins($sock,$cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select U.login
          from ".$dbnamebase.".Usuario_curso UC
          inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
          where UC.tipo_usuario<>'t'
          and UC.tipo_usuario<>'r'
          and UC.cod_curso='".$cod_curso."'";
  $res=Enviar($sock,$query);
  while ($linha = RetornaLinha($res))
  {
    $saida[strtoupper($linha['login'])]=1;
  }
  return $saida;
}

/* *********************************************************************
   RetornaLoginsInscricao - Funcao retorna a lista de logins de todos os usuarios, incluindo os usuarios removidos, para verificacao de logins repetidos na incricao.
   Entrada: $sock - BASE DO CURSO
   Saida: array com [$login]==1 se login existe
 */
function RetornaLoginsInscricao($sock)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select login from ".$dbnamebase.".Usuario";
  $res=Enviar($sock,$query);
  while ($linha = RetornaLinha($res))
	{
    	$saida[strtoupper($linha['login'])]=1;
	}
  return $saida;
}

/* *********************************************************************
   RetornaEmailsInscricao - Funcao retorna a lista de e-mails de todos os usuarios, incluindo os usuarios removidos, para verificacao de e-mails repetidos na incricao.
   Entrada: $sock - BASE DO CURSO
   Saida: array com [$login]==1 se login existe
 */
function RetornaEmailsInscricao($sock)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select email from ".$dbnamebase.".Usuario";
  $res=Enviar($sock,$query);
  while ($linha = RetornaLinha($res))
	{
    	$saida[strtoupper($linha['email'])]=1;
	}
  return $saida;
}

/* *********************************************************************
   ExisteEmail - Funcao verifica se email já existe na base de dados
   Entrada: $sock - BASE DO CURSO
            $login - Login a ser verificado
   Saida: boolean
 */
function ExisteEmail($sock,$cod_curso,$email, $cod_usuario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);

  $query="select email from ".$dbnamebase.".Usuario where email LIKE '".$email."' and cod_usuario <> '".$cod_usuario_global."'";
  $res=Enviar($sock,$query);
  $linha = RetornaLinha($res);

  return (empty($linha[0]));
}

/* *********************************************************************
   CadastrarUsuario - Cadastra o usu�rio com os dados passados. Envia
                      e-mail avisando login e senha.
   Entrada: $sock - BASE DO CURSO
            $cod_curso - Codigo do curso
            $dados - array com ['nome']
                               ['login']
                               ['email']
                               ['tipo_usuario']
                               ['senha']
   Saida: $sock com a base do curso  (necess�rio por causa do e-mail)
*/
function CadastrarUsuario($sock,$cod_curso,$dados, $lista_frases, $cod_usuario)
{

//   global $lista_frases,$_SESSION['cod_lingua_s'];



   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];
  $cod_lingua_s = $_SESSION['cod_lingua_s'];
  
  $data_inscricao = time();

  $cod_usuario_global_prox = RetornaProximoCodigo($sock,$dbnamebase.".Usuario");
  $query="insert into ".$dbnamebase.".Usuario (cod_usuario,nome,login,email,senha,data_inscricao,cod_lingua) values (".$cod_usuario_global_prox.",'".$dados['nome']."','".$dados['login']."','".$dados['email']."','".crypt($dados['senha'],"AA")."',".$data_inscricao.",".$_SESSION['cod_lingua_s'].")";
  Enviar($sock,$query);

  $cod_usuario_prox = RetornaProximoCodigoUsuarioCurso($sock,$cod_curso);
  $query="insert into ".$dbnamebase.".Usuario_curso (cod_usuario_global,cod_usuario,cod_curso,tipo_usuario,data_inscricao) values ('".$cod_usuario_global_prox."','".$cod_usuario_prox."','".$cod_curso."','".$dados['tipo_usuario']."',".$data_inscricao.")";
  Enviar($sock,$query);

  $query = "insert into ".$dbnamebase.".Usuario_config (cod_usuario,cod_curso,notificar_email) values ('".$cod_usuario_prox."','".$cod_curso."','0')";
  Enviar($sock, $query);

  $dados_curso=DadosCursoParaEmail($sock, $cod_curso);

  $remetente=$dados_curso['email'];
  $destino=$dados['email'];

  Desconectar($sock);
  $sock=Conectar("");

  $query="select valor from Config where item='host'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $host=$linha['valor'];

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $raiz_www=$linha['diretorio'];

  /* 63 - TelEduc: Inscri��o */
  $assunto=RetornaFraseDaLista($lista_frases,63);
  
  if ($dados['tipo_usuario']=="A")
  {
    /* 64 - Voc� foi inscrito como aluno para o curso */
    $mensagem ="<p>".RetornaFraseDaLista($lista_frases,64)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else if ($dados[tipo_usuario] == 'F')
  {
    /* 72 - Voc� foi inscrito como formador para o curso */
    $mensagem ="<p>".RetornaFraseDaLista($lista_frases,72)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else if ($dados[tipo_usuario] == 'z' || $dados[tipo_usuario] == 'Z')
  {
    /* 196 - Voc� foi inscrito como convidado para o curso */
    $mensagem ="<p>".RetornaFraseDaLista($lista_frases,196)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else
  {
    // Erro !
    echo("<big>Erro em ".__FILE__." linha ".__LINE__." parametro tipo_usuario inesperado</big>");
    var_dump($dados[tipo_usuario]);
    die();
  }

  /* 65 - Visite a p�gina do curso para obter informa��es sobre o seu in�cio. */
  $mensagem.="<p>".RetornaFraseDaLista($lista_frases,65).".</p>\n";

  /* 67 - Seu login �*/
  $mensagem.="<p>".RetornaFraseDaLista($lista_frases,67)." <big><em><strong>".$dados['login']."</strong></em></big> ";

  /* 68 - e sua senha �*/
  $mensagem.=RetornaFraseDaLista($lista_frases,68)." <big><em><strong>".$dados['senha']."</strong></em></big></p>\n";

  /* 249 - Esse login e senha são únicos para todos cursos em que for escrito no endereço  */
  $mensagem.="<p>".RetornaFraseDaLista($lista_frases,249)."<a href=\"http://".$host.$raiz_www."\">http://".$host.$raiz_www."</a></p>\n";

  /* 252 - Para acessar o curso basta ir ao endereço  */
  $mensagem.="<p>".RetornaFraseDaLista($lista_frases,252)."<br />\n";

  $mensagem.="<a href=\"http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$cod_curso."\">http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$cod_curso."</a></p>\n\n";


  $mensagem.="<hr /><br />\n";

  /* 250 - Quando você fizer o primeiro login, será solicitado o preenchimento dos seus dados cadastrais para o ambiente TelEduc localizado nesse endereço. */
  /* 251 - Depois de preenchido os dados, você pode entrar no seu curso com seu login único que te dará acesso a todos os cursos dos quais você for participar.*/
  $mensagem.="<p><small>".RetornaFraseDaLista($lista_frases,250)." ".RetornaFraseDaLista($lista_frases,251)."</small></p>\n";

  /* 253 - Para alterar a senha entre na opção Configurar localizada no menu fora do curso. */
  $mensagem.="<p><small>".RetornaFraseDaLista($lista_frases,253)."</small></p><br /><br />\n";

  /* 66 - Anteciosamente, Coordena��o do curso */
  $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,66)." <strong>".$dados_curso['nome_curso']."</strong>. </p><br />\n";

  Desconectar($sock);

  $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto, $cod_usuario);

  MandaMsg($remetente,$destino,$assunto,$mensagem_envio);

  $sock=Conectar($cod_curso);

  return ($sock);
}

/* *********************************************************************
   CadastrarUsuarioExistente - Cadastra o usu rio existente com os dados passados. Envia
                      e-mail avisando login e senha.
   Entrada: $sock - BASE DO CURSO
            $cod_curso - Codigo do curso
            $dados - array com ['cod_usuario_global']
                               ['tipo_usuario']

   Saida: $sock com a base do curso  (necess rio por causa do e-mail)
*/
function CadastrarUsuarioExistente($sock,$cod_curso,$dados, $lista_frases)
{

//   global $lista_frases,$_SESSION['cod_lingua_s'];

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];
  $cod_lingua_s = $_SESSION['cod_lingua_s'];

  $data_inscricao = time();

  $cod_usuario_prox = RetornaProximoCodigoUsuarioCurso($sock,$cod_curso);
  $query="insert into ".$dbnamebase.".Usuario_curso (cod_usuario_global,cod_usuario,cod_curso,tipo_usuario,data_inscricao) values ('".$dados['cod_usuario_global']."','".$cod_usuario_prox."','".$cod_curso."','".$dados['tipo_usuario']."',".$data_inscricao.")";
  Enviar($sock,$query);

  $query = "insert into ".$dbnamebase.".Usuario_config (cod_usuario,cod_curso,notificar_email) values ('".$cod_usuario_prox."','".$cod_curso."','0')";
  Enviar($sock, $query);

  $dados_curso=DadosCursoParaEmail($sock, $cod_curso);

  $remetente=$dados_curso['email'];

  $query = "select email from ".$dbnamebase.".Usuario where cod_usuario='".$dados['cod_usuario_global']."'";
  $res=Enviar($sock,$query);
  $email=RetornaLinha($res);
  $destino=$email[0];

  Desconectar($sock);
  $sock=Conectar("");

  $query="select valor from Config where item='host'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $host=$linha['valor'];

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $raiz_www=$linha['diretorio'];

  /* 63 - TelEduc: Inscri  o */
  $assunto=RetornaFraseDaLista($lista_frases,63);
  
  if ($dados['tipo_usuario']=="A")
  {
    /* 64 - Voc  foi inscrito como aluno para o curso */
    $mensagem ="<p>".RetornaFraseDaLista($lista_frases,64)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else if ($dados[tipo_usuario] == 'F')
  {
    /* 72 - Voc  foi inscrito como formador para o curso */
    $mensagem ="<p>".RetornaFraseDaLista($lista_frases,72)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else if ($dados[tipo_usuario] == 'z' || $dados[tipo_usuario] == 'Z')
  {
    /* 196 - Voc  foi inscrito como convidado para o curso */
    $mensagem ="<p>".RetornaFraseDaLista($lista_frases,196)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else
  {
    // Erro !
    echo("<big>Erro em ".__FILE__." linha ".__LINE__." parametro tipo_usuario inesperado</big>");
    var_dump($dados[tipo_usuario]);
    die();
  }

  /* 65 - Visite a p gina do curso para obter informa  es sobre o seu in cio. */
  $mensagem.="<p>".RetornaFraseDaLista($lista_frases,65).".</p>\n";

  //MENSAGEM DEVE INFORMAR QUE LOGIN E SENHA SAO OS MESMOS QUE ELE UTILIZA EM OUTROS CURSOS

  /* 254 - O seu login e senha são os mesmos já utilizados em cursos do endereço */
  $mensagem.="<p>".RetornaFraseDaLista($lista_frases,254)." <a href=\"http://".$host.$raiz_www."\">http://".$host.$raiz_www."</a></p>\n";

  $mensagem.="<p><a href=\"http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$cod_curso."\">http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$cod_curso."</a></p>\n\n";


  $mensagem.="<hr /><br />\n";

  /* 66 - Anteciosamente, Coordena  o do curso */
  $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,66)." <strong>".$dados_curso[nome_curso]."</strong>. </p><br />\n";

  Desconectar($sock);

  $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto);

  MandaMsg($remetente,$destino,$assunto,$mensagem_envio);

  $sock=Conectar($cod_curso);

  return ($sock);
}

/* *********************************************************************
   RetornaListaUsuariosDoGerenciamento - Retorna a lista de usuarios p/
                                         a tela gerenciamento.php
   Entrada: $sock - BASE DO CURSO
            $tipo_usuario - Tipo do usu�rio a ser retorna (a,r,A,F)
            $ordem - "nome" ou "data"
   Saida: array com [$cod_usuario]['nome']
                    [$cod_usuario]['data_inscricao']

*/
function RetornaListaUsuariosDoGerenciamento($sock,$cod_curso,$tipo_usuario,$ordem)
{
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  if ($ordem=="data")
    $ordem="data_inscricao";

  $query="select UC.cod_usuario,U.nome,U.data_inscricao,UC.portfolio
          from ".$dbnamebase.".Usuario_curso UC
          inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
          where binary UC.tipo_usuario='".$tipo_usuario."'
          and UC.cod_curso='".$cod_curso."'
          and UC.cod_usuario>=0
          order by ".$ordem;

  $res=Enviar($sock,$query);
  while ($linha=RetornaLinha($res))
  {
    $saida[$linha['cod_usuario']]['nome']=$linha['nome'];
    $saida[$linha['cod_usuario']]['data_inscricao']=$linha['data_inscricao'];
    $saida[$linha['cod_usuario']]['portfolio']=$linha['portfolio'];
  }
  return $saida;
}

/* *********************************************************************
   MudarTipoUsuarioComMensagem - Altera o tipo de usu�rio e envia um
      e-mail notificando a altera��o.
   
   Entrada: $sock - BASE DO CURSO
            $cod_curso - Codigo do curso
            $cod_usu - Array com codigo dos usuarios
            $assunto - Assunto da mensagem
            $mensagem - Mensagem em si (Em caso de A, ser� anexado instru��es de acesso).
   Saida: $sock da base do curso

*/
function MudarTipoUsuarioComMensagem($sock,$cod_curso,$cod_usu,$tipo_usuario,$assunto,$mensagem)
{
  global $lista_frases;

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  Desconectar($sock);

  $sock=Conectar("");

  $query="select valor from Config where item='host'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $host=$linha['valor'];

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $raiz_www=$linha['diretorio'];

  Desconectar($sock);

  /* 63 - TelEduc: Inscri��o */
  $assunto=RetornaFraseDaLista($lista_frases,63);

  if (count($cod_usu)>0)
  {
    $mensagem_temp=$mensagem;
    foreach($cod_usu as $cod => $cod_usuario)
    {
      $sock=Conectar($cod_curso);

      $query="update ".$dbnamebase.".Usuario_curso set tipo_usuario='".$tipo_usuario."' where cod_usuario='".$cod_usuario."' and cod_curso='".$cod_curso."'";
      $res=Enviar($sock,$query);

      $dados_curso=DadosCursoParaEmail($sock, $cod_curso);

      $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);

      $query="select nome,email,login from ".$dbnamebase.".Usuario where cod_usuario=".$cod_usuario_global;
      $res=Enviar($sock,$query);
      $dados=RetornaLinha($res);

      if ($tipo_usuario=="A" || $tipo_usuario == "V")
      {
        $mensagem=$mensagem_temp;
        $mensagem.="<hr style=\"margin:30px 0 30px 0;\" />\n";

        /* 252 - Para acessar o curso basta ir ao endereço */
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,252).": </p>\n";
      
        $mensagem.="<p><a href=\"http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$cod_curso."\">http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$cod_curso."</a></p>\n\n";

        $mensagem.="<hr style=\"margin:30px 0 30px 0;\" />\n";
      }

      $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto);

      MandaMsg($dados_curso['email'],$dados['email'],$assunto,$mensagem_envio);

    }
  }

  $sock=Conectar($cod_curso);

  return $sock;
}
/**
   TrocaCoordenador - Transforma um usuário formador em coordenador do curso.
   O coordenador anterior continua cadastrado no curso como formador.
   
   Entrada:	$sock - BASE DO CURSO
   			$cod_curso - Codigo do curso
   			$cod_usuario - cod_usuario do usuario a se tranformar coordenador
   
   Saída: nenhuma
*/
function TrocaCoordenador($sock, $cod_curso, $cod_usuario){
  /* Muda o cod_coordenador do banco principal */ 
  $sock = Conectar('');
  $query = "update Cursos set cod_coordenador = $cod_usuario where cod_curso = $cod_curso";
  Enviar($sock, $query);
  
  /* e depois do banco do proprio curso */
  MudarDB($sock, $cod_curso);
  $query = "update Cursos set cod_coordenador = $cod_usuario where cod_curso = $cod_curso";
  Enviar($sock, $query);
}

/* *********************************************************************
   MudaTipoUsuario - Muda o tipo do usu�rio.
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo do usuario
            $tipo_usuario - Tipo do usu�rio a ser definidio
   Saida: nenhuma
*/
function MudaTipoUsuario($sock,$cod_curso,$cod_usuario,$tipo_usuario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="update ".$dbnamebase.".Usuario_curso set tipo_usuario='".$tipo_usuario."' where cod_usuario=".$cod_usuario." and cod_curso='".$cod_curso."'";
  $res=Enviar($sock,$query);
}

/* *********************************************************************
   RetornaDadoUssuario - Retorna dados do usuario da tabela Usuario
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo do usuario
   Saida: nenhuma
*/
function RetornaDadosUsuario($sock,$cod_curso,$cod_usuario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select U.*, UC.*
          from ".$dbnamebase.".Usuario_curso UC
          inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
          where UC.cod_usuario='".$cod_usuario."'
          and UC.cod_curso='".$cod_curso."'";
  $res=Enviar($sock,$query);
  return RetornaLinha($res);
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
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select * from Escolaridade order by cod_escolaridade";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return($lista);
}

/* *********************************************************************
   RetornaListaUsuariosGlobal - Retorna a lista de usuarios nao cadastrados no curso
                                         para a tela inscrever3.php
   Entrada: $sock - BASE DO CURSO
   Saida: array com [$cod_usuario]['nome']
                    [$cod_usuario]['login']
                    [$cod_usuario]['senha']

*/
function RetornaListaUsuariosGlobal($sock,$cod_curso,$busca="")
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  //Seleciona usuario do curso
  $query ="select cod_usuario_global
           from ".$dbnamebase.".Usuario_curso
           where cod_curso = '".$cod_curso."'";
  $res=Enviar($sock,$query);

  $array = RetornaArrayLinhas($res);

  foreach($array as $cod => $linha){
    $usuario_curso[$cod] = $linha['cod_usuario_global'];
  }

  //Seleciona todos os usuarios, exceto os ja cadastrados no curso
  $query="select cod_usuario,nome,login,email
          from ".$dbnamebase.".Usuario
          where cod_usuario NOT IN (".implode($usuario_curso, ",").")
          and ((nome LIKE '%".$busca."%') OR (email LIKE '%".$busca."%') OR (login LIKE '%".$busca."%'))
          order by nome";
  $res=Enviar($sock,$query);

  $saida= RetornaArrayLinhas($res);

  return $saida;
}

/* *********************************************************************
   RetornaListaUsuariosDoGerenciamento - Retorna a lista de usuarios p/
                                         a tela gerenciamento.php
   Entrada: $sock - BASE DO CURSO
   Saida: array com [$cod_usuario]['nome']
                    [$cod_usuario]['login']

*/
function RetornaListaUsuariosSenha($sock,$cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select UC.cod_usuario,U.nome,U.login,U.email
          from ".$dbnamebase.".Usuario_curso UC
          inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
          where UC.cod_usuario>=0
          and UC.cod_curso = '".$cod_curso."'
          and (binary UC.tipo_usuario='A' or binary UC.tipo_usuario='F' or binary UC.tipo_usuario='V' or binary UC.tipo_usuario='Z' or binary UC.tipo_usuario='z')
          order by nome";
  $res=Enviar($sock,$query);
  while ($linha=RetornaLinha($res))
  {
    $saida[$linha['cod_usuario']]['nome']=$linha['nome'];
    $saida[$linha['cod_usuario']]['login']=$linha['login'];
    $saida[$linha['cod_usuario']]['email']=$linha['email'];
  }
  return $saida;
}

/* *********************************************************************
   AtualizaSenhaUsuario - Atualiza a senha do Usu�rio.
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - Codigo do Usuario
            $senha_crypt - Senha j� criptografada
   Saida: nenhuma
*/
function AtualizaSenhaUsuario($sock,$cod_curso,$cod_usuario,$senha)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);

  $query="update ".$dbnamebase.".Usuario set senha='".$senha."' where cod_usuario=".$cod_usuario_global;
  $res=Enviar($sock,$query);
}

/* *********************************************************************
   AtualizaEmailusuario - Atualiza o e-mail do Usu�rio.
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - Codigo do Usuario
            $email - $email do usuario
   Saida: nenhuma
*/
function AtualizaEmailUsuario($sock,$cod_curso,$cod_usuario,$email)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);

  $query="update ".$dbnamebase.".Usuario set email='".$email."' where cod_usuario=".$cod_usuario_global;
  return(Enviar($sock,$query));
}

/* *********************************************************************
   RetornaListaConvidados - retorna lista de convidados
   Entrada:
       $sock  - sock de conexao com a base de dados
       $acao - indica se mostra convidados com inscricao aceita ou com inscricao rejeitada
       $ordem - parametro de ordenacao (data ou nome)
   Saida: array
       [ $cod_usuario ]['nome'] - nome do convidado
       [ $cod_usuario ]['data_inscricao'] - data de inscricao
       [ $cod_usuario ]['interacao'] - booleano. indica se true: convidado com interacao ou false: convidado sem interacao
*/
function RetornaListaConvidados ($sock,$cod_curso, $acao, $ordem)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];


  // abandonamos a ideia de mostrar convidados com inscricao rejeitada. Agora mostramos apenas os convidados no curso
  $acao = 'a';

  if ($acao == 'a')
  {
    // convidados com inscricao aceita
    $where .= "(UC.tipo_usuario='Z' or UC.tipo_usuario='z')";
  }
  else if ($acao == 'r')
  {
    // convidados com inscricao rejeitada
    $where .= "UC.tipo_usuario='t'";
  }
  else
  {
    trigger_error("Erro em RetornaListaConvidados, linha ".__LINE__."<BR>");
    exit();
  }
  
  $query = "select UC.cod_usuario, U.nome, UC.tipo_usuario, U.data_inscricao 'data'
            from ".$dbnamebase.".Usuario_curso UC
            inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
            where ".$where."
            and UC.cod_curso='".$cod_curso."'
            order by ".$ordem;
  $res   = Enviar ($sock, $query);
  while ($linha = RetornaLinha ($res))
  {
    $lista_retorno [ $linha ['cod_usuario'] ]['nome'] = $linha ['nome'];
    $lista_retorno [ $linha ['cod_usuario'] ]['data_inscricao'] = $linha ['data'];
    $lista_retorno [ $linha ['cod_usuario'] ]['interacao'] = ( $linha ['tipo_usuario'] == 'Z' );
  }

  return $lista_retorno;
}

/* *********************************************************************
   AlterarConvidado - altera o tipo usuario do convidado
   Entrada: 
       $sock - sock de conexao
       $cod_usuario - codigo do usuario (convidado) a ter seu tipo_usuario alterado
       $interacao - booleano que indica se convidado tem interacao (tipo-usuario = 'Z' maiusculo), ou nao (tipo-usuario = 'z' minusculo)
   Saida:
       Nenhuma
*/
function AlterarConvidado ($sock,$cod_curso,$cod_usuario, $interacao)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $tipo_usuario = ($interacao ? 'Z' : 'z');

  $query = "update ".$dbnamebase.".Usuario_curso set tipo_usuario='".$tipo_usuario."' where cod_usuario='".$cod_usuario."' and cod_curso='".$cod_curso."'";
  $res   = Enviar ($sock, $query);
}

/* *********************************************************************
   RemoverFormador - Deleta o formador
   Entrada:
       $sock - sock de conexao
       $cod_usuario - codigo do usuario (convidado) a ter seu tipo_usuario alterado
       $interacao - booleano que indica se convidado tem interacao (tipo-usuario = 'F' maiusculo), ou nao (tipo-usuario = 'f' minusculo)
   Saida:
       Nenhuma
*/
function RemoverFormador ($sock,$cod_curso,$cod_usuario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $tipo_usuario='f';

  $query = "update ".$dbnamebase.".Usuario_curso set tipo_usuario='".$tipo_usuario."' where cod_usuario='".$cod_usuario."' and cod_curso='".$cod_curso."'";
  $res   = Enviar ($sock, $query);
}

/* *********************************************************************
   RetornaCodigoCoordenador - retorna cod_usuario do coordenador do curso
   entrada:  $sock - sock de conexao
   saida:    codigo do coordenador
*/
function RetornaCodigoCoordenador ($sock, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "select cod_coordenador from $dbnamebase.Cursos where cod_curso = $cod_curso";
  $res   = Enviar ($sock, $query);
  $linha = RetornaLinha ($res);

  $query = "select cod_usuario from $dbnamebase.Usuario_curso where cod_usuario_global = ".$linha['cod_coordenador']." and cod_curso = $cod_curso";
  $res = Enviar($sock, $query);
  $linha = RetornaLinha ($res);

  return $linha['cod_usuario'];
}

/* *********************************************************************
   RetornaListaVisitantes - retorna lista da visitantes
   entrada - $sock  - sock de conexao
             $ordem - parametro de ordenacao pde ser {"nome", "data"}
   saida: array[ $cod_usuario ]['nome'] - nome do visitante
               [ $cod_usuario ]['data_inscricao'] - data de inscricao em UT
*/
function RetornaListaVisitantes($sock, $cod_curso, $ordem)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  if ($ordem == "data")
    $ordem = "data_inscricao";

  $query = "select UC.cod_usuario, U.nome, U.data_inscricao
            from ".$dbnamebase.".Usuario_curso UC
            inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global)
            where UC.tipo_usuario='V'
            and UC.cod_curso='".$cod_curso."'
            order by ".$ordem;
  $res   = Enviar($sock, $query);

  while ($linha = RetornaLinha($res))
  {
    $cod_usuario = $linha['cod_usuario'];
    $saida[ $cod_usuario ]['nome'] = $linha['nome'];
    $saida[ $cod_usuario ]['data_inscricao'] = $linha['data_inscricao'];
  }
  return $saida;
}


/* *************************************************************************
   RetornaFerramCompartilhadas - Retorna lista de ferramentas compartilhadas
   do curso
   entradas:  $sock - conexao com a base de dados
              $cod_curso - c�digo do curso
   saida:    array['cod_ferramenta']
*/
function RetornaFerramCompartilhadas ($sock, $cod_curso)
{
  $query  = "select cod_ferramenta from Cursos_compart where ";
  $query .= "cod_curso=".$cod_curso." order by cod_ferramenta";
  $id = Enviar($sock, $query);
  $lista_compart = RetornaArrayLinhas($id);
  return $lista_compart;
}

/* *********************************************************************
   RetornaFerramCompartilhaveis - retorna as ferramentas compartilhaveis
   e seus respectivos status
   entrada:  $sock - conexao com base do curso
   saida:    array['cod_ferramenta']['status']
*/
function RetornaFerramCompartilhaveis ($sock)
{  
  $query  = "select status, cod_ferramenta from Curso_ferramentas where ";
  $query .= "cod_ferramenta in (1, 3, 4, 5, 6, 7, 16, 23)";
  $id = Enviar($sock, $query);
  $lista_ferramentas_disp = RetornaArrayLinhas($id);
  return $lista_ferramentas_disp;
}

/* ********************************************************************************
   CompartilhaFerramenta - Insere ferramenta na lista de ferramentas compartilhadas
   entrada:  $sock - conexao com base do curso
             $cod_curso - Codigo do curso
   saida:    Boolean - True caso a insercao tenha sido um sucesso, False caso contrario
*/
function CompartilhaFerramenta($sock, $cod_curso, $cod_ferramenta)
{
  $query  = "insert into Cursos_compart (cod_curso, cod_ferramenta)";
  $query .= "values (".$cod_curso.", ".$cod_ferramenta.")";
  return  Enviar($sock, $query);
}

/* ***********************************************************************************
   DescompartilhaFerramenta - Remove ferramenta da lista de ferramentas compartilhadas
   entrada:  $sock - conexao com base do curso
             $cod_curso - Codigo do curso
   saida:    Boolean - True caso a remocao tenha sido sucesso, False caso contrario
*/
function DescompartilhaFerramenta($sock, $cod_curso, $cod_ferramenta)
{
  $query  = "delete from Cursos_compart where cod_curso=".$cod_curso;
  $query .= " and cod_ferramenta=".$cod_ferramenta;
  return Enviar($sock, $query);
}

function StatusPortfolio($sock,$cod_curso, $cod_usuario, $opcao)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

	switch($opcao){
		case "ativar_port":
		  $status = "ativado";
		  break;
		case "desativar_port":
		  $status = "desativado";
		  break;
	}
	
	$query = "UPDATE ".$dbnamebase.".Usuario_curso SET portfolio='".$status."' WHERE cod_usuario='".$cod_usuario."' and cod_curso='".$cod_curso."'";
	Enviar($sock,$query);
}

function AlterarNomenclaturaDinamic($cod_curso, $status, $cod_ferramenta){
  $objResponse = new xajaxResponse('ISO-8859-1');
  $sock=Conectar("");
  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);

Desconectar($sock);

  $sock=Conectar($cod_curso);

  $query = "select valor from Config where item = 'status_coordenador'";
  $res = Enviar($sock, $query);
  $linha = RetornaArrayLinhas($res);

  if($linha[0]['valor'] != $status){
    if (AlteraCursoConfig($sock, 'status_coordenador', $status)){
      $frase = RetornaFraseDaLista($lista_frases, 271); //271 -Nomenclatura alterada com sucesso
      $confirma='true';
    }
    else{
      $frase = RetornaFraseDaLista($lista_frases, 272);// 272 - Erro na alteração
      $confirma='false';
    }

  $objResponse->addScriptCall("mostraFeedback",$frase,$confirma);
  }
  Desconectar($sock);

  
  return $objResponse->getXML();
}

function AtivarDesativarPortDinamic($cod_curso, $cod_usu, $opcao, $frase1, $frase2){
  $objResponse = new xajaxResponse('ISO-8859-1');
  $sock=Conectar($cod_curso);
  foreach($cod_usu as $cod => $cod_usuario){
     StatusPortfolio($sock,$cod_curso, $cod_usuario, $opcao);

        if ($opcao == 'ativar_port'){
            $msg='Ativado';
            $frase= $frase1; //Portfólio(s) ativado(s) com sucesso.
        }
        else{
            $msg='Desativado';//Portfólio(s) desativado(s) com sucesso.
            $frase= $frase2;
        }

$test="status_port".$cod_usuario;
$objResponse->addAssign($test,"innerHTML",$msg);
        
}
$confirma='true';
$objResponse->addScriptCall("mostraFeedback",$frase,$confirma);
Desconectar($sock);
return $objResponse->getXML();
}

?>
