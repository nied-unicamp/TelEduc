<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/administracao.inc

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

/* ==========================================================
   ARQUIVO : cursos/aplic/administracao/administracao.inc
   ========================================================== */

/* *********************************************************************
   RetConfig - Retorna o valor associado a um item da tabela Config.
   Entrada: $sock - sock interno ou EXTERNO,
            $item - item cujo valor ï¿½ requisitado.

   Saida  : valor associado ao item requisitado.
*/
function RetConfig($sock,$item)
{
  $query = "select valor from Config where item = '".$item."'";
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);

  return($linha['valor']);
}

function RetornaListaTodosLogins($sock)
{

  $query="select login,email,nome from Usuario where cod_usuario <> -1";
  $res = Enviar($sock,$query);

  return RetornaArrayLinhas($res);

}

function SugerirLoginDinamic($pal,$frase,$pos){

  $objResponse = new xajaxResponse();
  $sock = Conectar("");
  $logins_usuarios = RetornaListaTodosLogins($sock);
  Desconectar($sock);
  $count=0;
  $hint="";
  for($i=0; $i<count($logins_usuarios) && $count < 10 && $logins_usuarios != ""; $i++){
    if($pal != "" && ($pal == substr($logins_usuarios[$i]['login'],0,strlen($pal)) || $pal == substr($logins_usuarios[$i]['email'],0,strlen($pal)))){
      $hint .= "<li><a href=\"#\" onclick=\"javascript:
      email=document.getElementsByName('email[]');
      email[$pos].value='".$logins_usuarios[$i]['email']."';
      nome=document.getElementsByName('nome[]');
      nome[$pos].value='".$logins_usuarios[$i]['nome']."';
      login=document.getElementsByName('login[]');
      login[$pos].value='".$logins_usuarios[$i]['login']."';
      document.getElementById('divSugs').style.display = 'none';
      EscondeLayer(cod_sugestao)\">".$logins_usuarios[$i]['email']."&nbsp;-&nbsp;(".$logins_usuarios[$i]['login'].")</a></li>";
      $count++;
    }
  }
  if($hint != ""){
    $hint = "<ul><li><i>".$frase."</i></li>".$hint."</ul>";
    $objResponse->script("document.getElementById('divSugs').style.display = '';");
    $objResponse->assign("divSugs", "innerHTML", $hint);
    $objResponse->call("XajaxMostraLayer",$pos);
  }
  else{
    $objResponse->call('XajaxEscondeLayer');
  }

  return $objResponse;
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

/* **********************************************************************
   GeraLogin- Retorna um login vï¿½lido, apartir do email do usuï¿½rio
   Entrada: Email do usuï¿½rio
   Saï¿½da:   Login vï¿½lido
*/
function GeraLogin($sock,$email){
  $novo_email=explode("@",$email);/*cria login apartir da primeira parte do email do usuario*/
  $novo_email=$novo_email[0];     /*pega a primeira parte do email*/

  /*abaixo verificamos se o login criado apartir do email do usuï¿½rio ja existe*/
  $logins=RetornaLoginsInscricao($sock);
  $i=2;
  $teste=$novo_email;
  while($logins[strtoupper($teste)]==1){
    $teste=$novo_email.'_'.$i;
    $i++;
  }
  $novo_email=$teste;
  return ($novo_email);
}

/* **********************************************************************
   Retorna Login de um usuï¿½rio apartir de um email fornecido
   Entrada: email do usuï¿½rio
   Saï¿½da: Login do usuï¿½rio
*/
function PegaLogin($sock,$email){
  $dbnamebase = $_SESSION['dbnamebase'];
  $query="select login from ".$dbnamebase.".Usuario where email='$email'";
  $res=Enviar($sock,$query);
  $login=RetornaLinha($res);
  return($login[0]);
}

/* *********************************************************************
   HouveAlteracoes - Verifica se houve alteraï¿½ï¿½es nas bases de dados
                     utilizada pela administraï¿½ï¿½o.
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
                                         'N' - Nï¿½o permite
*/
function HouveAlteracoes($sock,$cod_curso,$cod_usuario)
{
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];
  /* Cria um array com 4 elementos com valores iniciais iguais a zero. */
  /* O valor de cada elemento serï¿½ utilizado para adicionar a tag bold */
  /* nas ferramentas onde houve modificaï¿½ï¿½es.                          */

  $alt = array(0,  /* Modificaï¿½ï¿½es no cronograma do curso.         */
               0,  /* Modificaï¿½ï¿½es na escolha de ferramentas.      */
               0,  /* Modificaï¿½ï¿½es no destaque de ferramentas.     */
               0); /* Nova requisiï¿½ï¿½o de inscriï¿½ï¿½o.            */

  /* Obtï¿½m o horï¿½rio do penltimo acesso do usuï¿½rio e compara-o com */
  /* as datas das tabelas.                                          */
  $penultac = PenultimoAcesso($sock, $cod_usuario, "");

  /* Verifica se houve modificaï¿½ï¿½o no cronograma do curso.          */
  $query = "select _timestamp 'data' from Cursos";
  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);
  if ($tupla['data'] > $penultac)
    $alt[0] = 1;

  /* Verifica se houve modificaï¿½ï¿½o na escolha das ferramentas.      */
  unset($tupla);
  $query = "select max(_timestamp) 'data' from Curso_ferramentas";
  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);
  if ($tupla['data'] > $penultac)
    $alt[1] = 1;

  /* Verifica se houve modificaï¿½ï¿½o no destaque das ferramentas.     */
  unset($tupla);
  $query = "select max(data) 'data' from Ferramentas_destaque";
  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);
  if ($tupla['data'] > $penultac)
    $alt[2] = 1;

  /* Verifica se hï¿½ uma nova requisiï¿½ï¿½o de inscriï¿½ï¿½o.               */
  unset($tupla);
  $query = "select max(data_inscricao) 'data' from ".$dbnamebase.".Usuario_curso where binary tipo_usuario = 'a' and cod_curso='".$cod_curso."'";
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
                                         'N' - Nï¿½o permite
                  ['cod_lingua'] = codigo da lingua padrï¿½o do curso (atual)
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
                                'N' - Nï¿½o permite
            $cod_lingua - Codigo da lingua padrï¿½o a ser utilizada no curso
            $horario - hora atual (em que se estï¿½ realizando a atualizaï¿½ï¿½o)
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
            $horario = hora atual (em que se estï¿½ realizando a atualizaï¿½ï¿½o)
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
     $lista_ferramentas. Se nï¿½o houver ferramentas a serem marcadas
     insere na base com o cod_ferramenta -1 (invï¿½lido).

   Entrada: $sock - BASE DO CURSO,
            $lista_ferramentas - array com cod_ferramenta.
   Saida: true se bem-sucedido, false do contrï¿½rio
*/
function MarcaFerramentas($sock, $lista_ferramentas)
{
  /* Obtï¿½m a hora atual */
  $time = time();
  $res = true;

  /* Se nï¿½o houver ferramentas marcadas, insira -1 como cï¿½digo da ferramenta na base. */
  if (($total = count($lista_ferramentas)) == 0)
  {
    $query = "insert into Ferramentas_destaque values (-1, ".$time.")";
    $res = Enviar($sock, $query);
  }
  else
  {
    /* Limpa a lista de ferramentas destacadas */
    $query = "delete from Ferramentas_destaque";
    Enviar($sock, $query);

    /* Destaca ferramenta por ferramenta */
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
   RetornaListaLogins - Retorna lista de logins existentes para conferï¿½ncia
   Entrada: $sock - BASE DO CURSO
   Saida: array com [$login]==1 se login existe
*/
function RetornaListaLogins($sock,$cod_curso)
{
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "select U.login ";
  $query.= "from ".$dbnamebase.".Usuario_curso UC ";
  $query.= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
  // Não incluir usuários com inscrições rejeitadas 
  $query.= "where binary UC.tipo_usuario <> 'a' and ";
  $query.=       "binary UC.tipo_usuario <> 'r' and ";
  $query.=       "binary UC.tipo_usuario <> 'f' and ";
  $query.=       "binary UC.tipo_usuario <> 'z' and ";
  $query.=       "binary UC.tipo_usuario <> 'v' and ";
  $query.=       "UC.cod_curso='".$cod_curso."'";

  $res=Enviar($sock,$query);
  while ($linha = RetornaLinha($res))
  {
    $saida[strtoupper($linha['login'])] = 1;
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
   ExisteEmail - Funcao verifica se email jÃ¡ existe na base de dados
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

/*
   Retorna o codigo do usuario atravï¿½s do login caso ele exista
 */
function RetornaCodigoUsuario($sock,$login){
  $dbnamebase = $_SESSION['dbnamebase'];
  $query="select cod_usuario from ".$dbnamebase.".Usuario where login='$login'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return($linha[0]);
}
/* *********************************************************************
   LoginCadastradoCurso - Verifica se o login do usuario ja esta cadastrado
                          no curso antes de cadastra-lo novamente
   Entrada - $sock - BASE DO CURSO
             $cod_curso - codigo do curso a ser verificado
             $login - login do usuario a ser verificado
   Saída   - $status - Se o usuário está cadastrado no curso
 */
function LoginCadastradoCurso($sock, $login, $cod_curso) {
  $codigo = RetornaCodigoUsuario($sock,$login);
  return CadastradoCurso($sock, $codigo, $cod_curso);
}

/* *********************************************************************
   CadastradoCurso - verifica se o usuario ja esta cadastrado no curso antes
                     de cadastra-lo novamente
   Entrada - $sock - BASE DO CURSO
             $cod_curso - codigo do curso a ser verificado
             $codigo_usuario - código do usuario a ser verificado
   Saída   - $status - Se o usuário está cadastrado no curso
*/
function CadastradoCurso($sock, $codigo_usuario, $cod_curso){

  $dbnamebase = $_SESSION['dbnamebase'];
  $query = "select count(*) as total from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso." and cod_usuario_global=".$codigo_usuario;
  $res = Enviar($sock,$query);
  $status = false;

  $linha = RetornaLinha($res);
  if ($linha['total'] > 0) {
    $status = true;
  }

  return $status;

}

/* *********************************************************************
   CadastrarUsuario - Cadastra o usuï¿½rio com os dados passados. Envia
                      e-mail avisando login e senha.
   Entrada: $sock - BASE DO CURSO
            $cod_curso - Codigo do curso
            $dados - array com ['nome']
                               ['login']
                               ['email']
                               ['tipo_usuario']
                               ['senha']
   Saida: $sock com a base do curso  (necessï¿½rio por causa do e-mail)
*/
function CadastrarUsuario($sock, $cod_curso, $dados, $lista_frases, $cod_usuario)
{

  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];
  $cod_lingua_s = $_SESSION['cod_lingua_s'];

  $data_inscricao = time();

  $cod_usuario_global_prox = RetornaProximoCodigo($sock,$dbnamebase.".Usuario");
  $query="insert into ".$dbnamebase.".Usuario (cod_usuario,nome,login,email,senha,data_inscricao,cod_lingua) values (".$cod_usuario_global_prox.",'".VerificaStringQuery(trim($dados['nome']))."','".VerificaStringQuery($dados['login'])."','".VerificaStringQuery($dados['email'])."','".crypt($dados['senha'],"AA")."',".$data_inscricao.",".$_SESSION['cod_lingua_s'].")";
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

  /* 63 - TelEduc: Inscriï¿½ï¿½o */
  $assunto=RetornaFraseDaLista($lista_frases,63);

  if ($dados['tipo_usuario'] == 'A')
  {
    /* 64 - Vocï¿½ foi inscrito como aluno para o curso */
    $mensagem = "<p>".RetornaFraseDaLista($lista_frases,64)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else if ($dados[tipo_usuario] == 'F')
  {
    /* 72 - Vocï¿½ foi inscrito como formador para o curso */
    $mensagem = "<p>".RetornaFraseDaLista($lista_frases,72)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else if ($dados[tipo_usuario] == 'Z')
  {
    /* 196 - Vocï¿½ foi inscrito como colaborador para o curso */
    $mensagem = "<p>".RetornaFraseDaLista($lista_frases,196)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else if ($dados[tipo_usuario] == 'V')
  {
    /* 188 - Vocï¿½ foi inscrito como visitante para o curso */
    $mensagem = "<p>".RetornaFraseDaLista($lista_frases,188)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else
  {
    // Erro !
    echo("<big>Erro em ".__FILE__." linha ".__LINE__." parametro tipo_usuario inesperado</big>");
    var_dump($dados[tipo_usuario]);
    die();
  }

  /* 65 - Visite a pï¿½gina do curso para obter informaï¿½ï¿½es sobre o seu inï¿½cio. */
  $mensagem.="<p>".RetornaFraseDaLista($lista_frases,65).".</p>\n";

  /* 67 - Seu login ï¿½*/
  $mensagem.="<p>".RetornaFraseDaLista($lista_frases,67)." <big><em><strong>".$dados['login']."</strong></em></big> ";

  /* 68 - e sua senha ï¿½*/
  $mensagem.=RetornaFraseDaLista($lista_frases,68)." <big><em><strong>".$dados['senha']."</strong></em></big></p>\n";

  /* 252 - Para acessar o curso basta ir ao endereÃ§o  */
  $mensagem.="<p>".RetornaFraseDaLista($lista_frases,252)."<br />\n";

  $mensagem.="<a href=\"http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$cod_curso."\">http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$cod_curso."</a></p>\n\n";


  $mensagem.="<hr /><br />\n";

  /* 250 - Quando vocÃª fizer o primeiro login, serÃ¡ solicitado o preenchimento dos seus dados cadastrais para o ambiente TelEduc localizado nesse endereÃ§o. */
  /* 251 - Depois de preenchido os dados, vocÃª pode entrar no seu curso com seu login Ãºnico que te darÃ¡ acesso a todos os cursos dos quais vocÃª for participar.*/
  $mensagem.="<p><small>".RetornaFraseDaLista($lista_frases,250)." ".RetornaFraseDaLista($lista_frases,251)."</small></p>\n";

  /* 253 - Para alterar a senha entre na opÃ§Ã£o Configurar localizada no menu fora do curso. */
  $mensagem.="<p><small>".RetornaFraseDaLista($lista_frases,253)."</small></p><br /><br />\n";

  /* 66 - Anteciosamente, Coordenaï¿½ï¿½o do curso */
  $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,66)." <strong>".$dados_curso['nome_curso']."</strong>. </p><br />\n";

  Desconectar($sock);

  $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto, $cod_usuario);

  MandaMsg($remetente,$destino,$assunto,$mensagem_envio);

  $sock=Conectar($cod_curso);

  return ($sock);

}

/* *********************************************************************
   CadastrarUsuarioExistente - Cadastra o usuário existente com os dados 
                               passados. Envia e-mail avisando login e senha.
   Entrada: $sock - BASE DO CURSO
            $cod_curso - Codigo do curso
            $dados - array com ['cod_usuario_global']
                               ['tipo_usuario']

   Saida: $sock com a base do curso  (necess rio por causa do e-mail)
*/
function CadastrarUsuarioExistente($sock,$cod_curso,$dados, $lista_frases)
{

  //global $lista_frases,$_SESSION['cod_lingua_s'];

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];
  $cod_lingua_s = $_SESSION['cod_lingua_s'];

  $data_inscricao = time();

  $cod_usuario_prox = RetornaProximoCodigoUsuarioCurso($sock,$cod_curso);
  $dados['cod_usuario_global'] = RetornaCodigoUsuarioGlobalPorLogin($sock, $dados['login']);

  $query  = "insert into ".$dbnamebase.".Usuario_curso (cod_usuario_global,cod_usuario,cod_curso,tipo_usuario,data_inscricao) ";
  $query .= "values ('".$dados['cod_usuario_global']."','".$cod_usuario_prox."','".$cod_curso."','".$dados['tipo_usuario']."',".$data_inscricao.")";
  Enviar($sock,$query);

  $query  = "insert into ".$dbnamebase.".Usuario_config (cod_usuario,cod_curso,notificar_email) values ";
  $query .= "('".$cod_usuario_prox."','".$cod_curso."','0')";
  Enviar($sock, $query);

  $dados_curso = DadosCursoParaEmail($sock, $cod_curso);

  $remetente=$dados_curso['email'];

  $query  = "select email from ".$dbnamebase.".Usuario ";
  $query .= "where cod_usuario='".$dados['cod_usuario_global']."'";
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
  $assunto=RetornaFraseDaLista($lista_frases,63)." ".$dados_curso['nome_curso'];

  if ($dados['tipo_usuario'] == 'A')
  {
    /* 64 - Você foi inscrito como aluno no curso */
    $mensagem = "<p>".RetornaFraseDaLista($lista_frases,64)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else if ($dados[tipo_usuario] == 'F')
  {
    /* 72 - Você foi inscrito como formador no curso */
    $mensagem = "<p>".RetornaFraseDaLista($lista_frases,72)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else if ($dados[tipo_usuario] == 'Z')
  {
    /* 196 - Você foi inscrito como colaborador no curso */
    $mensagem = "<p>".RetornaFraseDaLista($lista_frases,196)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else if ($dados[tipo_usuario] == 'V')
  {
    /* 188 - Você foi inscrito como visitante no curso */
    $mensagem = "<p>".RetornaFraseDaLista($lista_frases,188)." <strong>".$dados_curso['nome_curso']."</strong>.</p>\n";
  }
  else
  {
    // Erro !
    echo("<big>Erro em ".__FILE__." linha ".__LINE__." parametro tipo_usuario inesperado</big>");
    var_dump($dados[tipo_usuario]);
    die();
  }

  /* 65 - Visite a p gina do curso para obter informa  es sobre o seu in cio. */
  //$mensagem.="<p>".RetornaFraseDaLista($lista_frases,65).".</p>\n";

  //MENSAGEM DEVE INFORMAR QUE LOGIN E SENHA SAO OS MESMOS QUE ELE UTILIZA EM OUTROS CURSOS

  /* 254 - O seu login e senha sÃ£o os mesmos jÃ¡ utilizados em cursos do endereÃ§o */
  //$mensagem.="<p>".RetornaFraseDaLista($lista_frases,254)." <a href=\"http://".$host.$raiz_www."\">http://".$host.$raiz_www."</a></p>\n";

  /* 311 - Para acessar o curso clique  em*/
  $mensagem .= "<p>".RetornaFraseDaLista($lista_frases,311).": </p>";
  $mensagem .= "<p><a href=\"http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$cod_curso."\">http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$cod_curso."</a></p>\n\n";
  /* 312 - Use login e senha jï¿½ cadastrados.*/
  $mensagem .= "<br /><p>".RetornaFraseDaLista($lista_frases,312)."</p>";
  /* 313 - Visite a pï¿½gina do curso pra saber o seu inï¿½cio.*/
  $mensagem .= "<br /><p>".RetornaFraseDaLista($lista_frases,313)."</p>";


  $mensagem.="\n";

  /* 66 - Anteciosamente, Coordena  o do curso */
  $mensagem .= "<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,66)." <strong>".$dados_curso[nome_curso]."</strong>. </p><br />\n";

  Desconectar($sock);

  $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto);

  MandaMsg($remetente,$destino,$assunto,$mensagem_envio);

  $sock=Conectar($cod_curso);

  return ($sock);
}

/* *********************************************************************
   RetornaListaUsuarios - Retorna a lista de usuarios
   Entrada: $sock - BASE DO CURSO
            $tipo_usuario - Tipo do usuï¿½rio a ser retornado.
            Os tipos válidos são i, r, a, A, f, F, z, Z, v, V.
            $ordem - "nome" ou "data"
   Saida: array com [$cod_usuario]['nome']
                    [$cod_usuario]['data_inscricao']
 */
function RetornaListaUsuarios($sock, $cod_curso, $tipo_usuario, $ordem)
{
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  if ($ordem=="data")
    $ordem="data_inscricao";
  
  /*if (!in_array($tipo_usuario, array('i', 'r', 'a', 'A', 'f', 'F', 'z', 'Z', 'v', 'V')))
  {
    trigger_error("Erro em RetornaListaUsuarios, linha ".__LINE__."<BR>");
    exit();
  }*/

  $query  = "select UC.cod_usuario,U.nome,U.data_inscricao,UC.portfolio ";
  $query .= "from ".$dbnamebase.".Usuario_curso UC ";
  $query .= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
  $query .= "where binary UC.tipo_usuario='".$tipo_usuario."' and ";
  $query .=       "UC.cod_curso='".$cod_curso."' and ";
  $query .=       "UC.cod_usuario >= 0 ";
  $query .= "order by ".$ordem;

  $res    = Enviar($sock,$query);
  while($linha = RetornaLinha($res))
  {
    $lista_retorno[$linha['cod_usuario']]['nome']           = $linha['nome'];
    $lista_retorno[$linha['cod_usuario']]['data_inscricao'] = $linha['data_inscricao'];
    $lista_retorno[$linha['cod_usuario']]['portfolio']      = $linha['portfolio'];
  }
  
  return $lista_retorno;
}

function PaginacaoDinamic($aux,$pag,$pag_atual,$cod_curso,$tipo_usuario,$ordem,$frase_ativado,$frase_desativado){
  $objResponse = new xajaxResponse();

  /*busca no banco os registros a serem mostrados*/
  $sock=Conectar("");
  $lista_usuarios   = RetornaListaUsuarios($sock,$cod_curso,$tipo_usuario,$ordem);
  // Se o tipo de usuário que estamos paginando é algum papel desligado que
  // tenha portfólio, devemos mostrar a opcao de ativar ou não o portfolio.
  $mostra_portfolio = ($tipo_usuario == 'a' || $tipo_usuario == 'f' || $tipo_usuario == 'z');
  $num              = count($lista_usuarios);

  /* Começa a preparar até onde pegaremos, ou seja, se for a ultima pagina
   * vai ate o fim, caso contrario pega vai ate um multiplo de 10.
   */

  $limite=((int)$pag_atual*10);
  $i=(((int)$pag_atual-1)*10)+1;

  if($i==0)
    $i=1;

  if(($num-$limite)< 0)
    $limite=$num;

  /*apaga todas as mensagens anteriores.Teremos 10 mensagens ou atï¿½ terminar caso seja a ultima pagina*/
  $objResponse->call("ApagaPagina",'T');

  /*colocamos na pï¿½gina o conteudo da respectiva pagina*/
  $control=1;
  foreach($lista_usuarios as $cod_usuario_l => $linha){
    if($control>=$i && $control<=$limite){
      if($mostra_portfolio) {
        if($linha['portfolio'] == "ativado")
          $frase_port=$frase_ativado;
        else
          $frase_port=$frase_desativado;
      }
      $objResponse->call("CriaElementoTab",$linha['nome'],Unixtime2Data($linha['data_inscricao']),"dados",$cod_usuario_l,$mostra_portfolio,$frase_port);
    }
    $control++;
  }
  /*muda a variavel atual via javascript, para tirar o evento onclick da pagina que 
     esta sendo mostrada*/

  $objResponse->call("MudaAtual",$pag_atual);

  $objResponse->call("Paginacao",$aux);
  $objResponse->call("MudaControleDetalhes",$aux,$pag_atual,$pag,'L');
  return $objResponse;

}
/*funcï¿½o responsavel por criar a paginaï¿½ï¿½o na primeira vez*/

function IniciaPaginacaoDinamic($cod_curso,$tipo_usuario,$ordem){
  $objResponse = new xajaxResponse();
  $sock=Conectar("");
  $lista_usuarios=RetornaListaUsuarios($sock,$cod_curso,$tipo_usuario,$ordem);
  $num=count($lista_usuarios);

  /*numero de paginas inteiras a ser mostrada*/
  $numpagInt=(ceil($num/10));

  /*caso exista mais que 5 pï¿½ginas mostramos a primeira paginaï¿½ï¿½o com 5*/
  /*A variavel que controla o proximo ï¿½ liberada, assim a navegaï¿½ï¿½o ainda existe para frente*/
  $prox="N";
  if($numpagInt>5){
    $numpagInt=5;
    $prox="L";
  }
  $objResponse->call("Inicial",(int)$numpagInt,$prox);

  return $objResponse;

}

/*funcï¿½o que controla a mudanï¿½a no intervalo de paginacao*/

function MudaDinamic($intervalo,$flag,$cod_curso,$tipo_usuario,$ordem){
  $objResponse = new xajaxResponse();
  $sock=Conectar("");
  $lista_usuarios=RetornaListaUsuarios($sock,$cod_curso,$tipo_usuario,$ordem);
  $num=count($lista_usuarios);
  if($flag=='L'){
     $intervalo=(ceil(($num-50)/10))+1;
     $status='BF';
  }
  if($flag=='P'){
    if($num-(($intervalo*10)+50)<=0)
       $status='BF';
    else
      $status='LF';
    $intervalo++;
  }
  if($flag=='A'){
    $intervalo--;
    if($intervalo==1)
      $status='BV';
    else
      $status='LV';
  }
  if($flag=='PR'){
    $intervalo=1;
    $status='BV';
  }
  $objResponse->call("MudaIntervalo",$intervalo);
  $objResponse->call("ApagaPagina",'N');
  $objResponse->call("Paginacao",$status);
  return $objResponse;
}



/* *********************************************************************
   MudarTipoUsuarioComMensagem - Altera o tipo de usuï¿½rio e envia um
      e-mail notificando a alteraï¿½ï¿½o.

   Entrada: $sock - BASE DO CURSO
            $cod_curso - Codigo do curso
            $cod_usu - Array com codigo dos usuarios
            $assunto - Assunto da mensagem
            $mensagem - Mensagem em si (Em caso de A, serï¿½ anexado instruï¿½ï¿½es de acesso).
   Saida: $sock da base do curso

*/
function MudarTipoUsuarioComMensagem($sock, $cod_curso, $cod_usu, $tipo_usuario, $assunto, $mensagem)
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

  /* 63 - TelEduc: Inscriï¿½ï¿½o */
  $assunto=RetornaFraseDaLista($lista_frases,63);

  if (count($cod_usu)>0)
  {
    foreach($cod_usu as $cod => $cod_usuario)
    {
      $sock=Conectar($cod_curso);

      $query  = "update ".$dbnamebase.".Usuario_curso ";
      $query .= "set tipo_usuario='".VerificaStringQuery($tipo_usuario)."' ";
      $query .= "where cod_usuario='".VerificaStringQuery($cod_usuario)."' and "; 
      $query .=       "cod_curso='".VerificaStringQuery($cod_curso)."'";
      $res=Enviar($sock,$query);

      $dados_curso=DadosCursoParaEmail($sock, $cod_curso);

      $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);

      $query="select nome,email,login from ".$dbnamebase.".Usuario where cod_usuario=".$cod_usuario_global;
      $res=Enviar($sock,$query);
      $dados=RetornaLinha($res);

      if ($tipo_usuario == "A" || $tipo_usuario == "V")
      {
        $mensagem.="<hr style=\"margin:30px 0 30px 0;\" />\n";

        /* 252 - Para acessar o curso basta ir ao endereÃ§o */
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
   TrocaCoordenador - Transforma um usuÃ¡rio formador em coordenador do curso.
   O coordenador anterior continua cadastrado no curso como formador.

   Entrada: $sock - BASE DO CURSO
            $cod_curso - Codigo do curso
            $cod_usuario - cod_usuario do usuario a se tranformar coordenador

   SaÃ­da: nenhuma
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
   MudaTipoUsuario - Muda o tipo do usuï¿½rio.
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo do usuario
            $tipo_usuario - Tipo do usuï¿½rio a ser definidio
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

  $query  = "select U.*, UC.* ";
  $query .= "from ".$dbnamebase.".Usuario_curso UC ";
  $query .= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
  $query .= "where UC.cod_usuario='".$cod_usuario."' and ";
  $query .=       "UC.cod_curso='".$cod_curso."'";

  $res    = Enviar($sock,$query);
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
   RetornaListaUsuariosGlobal - Retorna a lista de usuarios cadastrados no Sistema
   Entrada: $sock - BASE DO CURSO
   Saida: array com [$cod_usuario]['nome']
                    [$cod_usuario]['login']
                    [$cod_usuario]['senha']

*/
function RetornaListaUsuariosGlobal($sock,$busca,$inicio,$num_registros)
{

  //Seleciona todos os usuarios, exceto os ja cadastrados no curso
  $query  = "select cod_usuario, nome, login, email ";
  $query .= "from ".$dbnamebase.".Usuario ";
  $query .= "where ((nome LIKE '%".$busca."%') OR (email LIKE '%".$busca."%') OR (login LIKE '%".$busca."%')) ";
  $query .= "order by nome ";
  $query .= "LIMIT ".$inicio." , ".$num_registros;

  $res=Enviar($sock,$query);
  $saida= RetornaArrayLinhas($res);

  return $saida;
}

/* ********************************************************************************
   ListaUsuariosPaginaDinamic - busca dinamicamente os usuarios no banco de dados
                                e os adiciona a pagina
   entrada: $pagina - pagina atual
            $tam_pagina - numero de itens exibidos por pagina
            $cod_curso - Codigo do curso
            $busca - string opcional para filtrar resultados
   saida:   XML
*/

function ListaUsuariosPaginaDinamic($pagina, $tam_pagina, $cod_curso, $busca="") {
  $objResponse = new xajaxResponse();

  $inicio = ($pagina-1) * $tam_pagina;
  $sock=Conectar("");
  $lista = RetornaListaUsuariosGlobal($sock,$busca,$inicio,$tam_pagina);
  Desconectar($sock);

  $objResponse->call("RemovePaginaAntiga");

  $i=0;
  foreach($lista as $user) {
    $flag = CadastradoCurso($sock, $user['cod_usuario'], $cod_curso)?'B':'L';
    $objResponse->call("IncluiUsuarioTabela", $user['cod_usuario'], $user['nome'], $user['login'],$user['email'],$flag, $i++);
  }

  $objResponse->call("IncluiControlePaginacao");
  $objResponse->call("ExibeControles", $pagina);

  return $objResponse;
}

/* *********************************************************************
   RetornaContagemUsuariosGlobal - Retorna a contagem usuarios nao cadastrados
                                   no curso para a tela inscrever3.php
   Entrada: $sock - BASE DO CURSO
            $cod_curso - Codigo do curso
            $busca - string opcional para filtrar resultados
   Saida: inteiro com o numero de resultados da busca

*/
function RetornaContagemUsuariosGlobal($sock,$cod_curso,$busca="")
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  //Conta todos os usuarios, exceto os ja cadastrados no curso
  $query  = "select count(*) as total ";
  $query .= "from ".$dbnamebase.".Usuario ";
  $query .= "where ((nome LIKE '%".$busca."%') OR (email LIKE '%".$busca."%') OR (login LIKE '%".$busca."%')) ";
  $query .= "order by nome";
  $res    = Enviar($sock,$query);

  $ar    = mysql_fetch_array($res);
  $saida = $ar["total"];

  return $saida;
}

/* *********************************************************************
   RetornaListaUsuariosSenha - Retorna a lista de usuarios p/
                               as telas de gerenciamento
   Entrada: $sock - BASE DO CURSO
   Saida: array com [$cod_usuario]['nome']
                    [$cod_usuario]['login']

*/
function RetornaListaUsuariosSenha($sock,$cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query  = "select UC.cod_usuario,U.nome,U.login,U.email ";
  $query .= "from ".$dbnamebase.".Usuario_curso UC ";
  $query .= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
  $query .= "where UC.cod_usuario>=0 and ";
  $query .=       "UC.cod_curso = '".$cod_curso."' and ";
  $query .=      "(binary UC.tipo_usuario='A' or ";
  $query .=       "binary UC.tipo_usuario='F' or ";
  $query .=       "binary UC.tipo_usuario='V' or ";
  $query .=       "binary UC.tipo_usuario='Z') ";
  $query .= "order by nome";

  $res    = Enviar($sock,$query);
  while ($linha=RetornaLinha($res))
  {
    $saida[$linha['cod_usuario']]['nome']  = $linha['nome'];
    $saida[$linha['cod_usuario']]['login'] = $linha['login'];
    $saida[$linha['cod_usuario']]['email'] = $linha['email'];
  }
  return $saida;
}

/* *********************************************************************
   AtualizaSenhaUsuario - Atualiza a senha do Usuï¿½rio.
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - Codigo do Usuario
            $senha_crypt - Senha jï¿½ criptografada
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
   AtualizaEmailusuario - Atualiza o e-mail do Usuï¿½rio.
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

  return $linha['cod_coordenador'];
}

/* *************************************************************************
   RetornaFerramCompartilhadas - Retorna lista de ferramentas compartilhadas
   do curso
   entradas:  $sock - conexao com a base de dados
              $cod_curso - cï¿½digo do curso
   saida:    array['cod_ferramenta']
*/
function RetornaFerramCompartilhadas ($sock, $cod_curso)
{
  $query  = "select cod_ferramenta from Cursos_compart ";
  $query .= "where cod_curso=".$cod_curso." ";
  $query .= "order by cod_ferramenta";
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
  $query  = "select status, cod_ferramenta from Curso_ferramentas ";
  $query .= "where cod_ferramenta in (1, 3, 4, 5, 6, 7, 16, 23)";
  $id = Enviar($sock, $query);
  $lista_ferramentas_disp = RetornaArrayLinhas($id);
  return $lista_ferramentas_disp;
}

/* *********************************************************************
   VerificaFerramCompartilhada - retorna true se a ferramenta passada
   por parÃ¢metro estÃ¡ compartilhada
   entrada:  $sock - conexao com base do curso
             $cod_curso
             $cod_ferramenta - CÃ³digo da Ferramenta para verificar
   saida:    Boolean - True caso compartilhada, False caso contrÃ¡rio
*/
function VerificaFerramCompartilhada($sock, $cod_curso, $cod_ferramenta)
{
  $ferramentas_compartilhadas = RetornaFerramCompartilhadas($sock, $cod_curso);
  foreach ($ferramentas_compartilhadas as $ferramenta)
  {
    if ($ferramenta['cod_ferramenta'] == $cod_ferramenta) {
      return true;
    }
  }
  return false;
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
  $query  = "delete from Cursos_compart ";
  $query .= "where cod_curso=".$cod_curso." and ";
  $query .=       "cod_ferramenta=".$cod_ferramenta;
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
  $objResponse = new xajaxResponse();
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
      $frase = RetornaFraseDaLista($lista_frases, 272);// 272 - Erro na alteraÃ§Ã£o
      $confirma='false';
    }

  $objResponse->call("mostraFeedback",$frase,$confirma);
  }
  Desconectar($sock);


  return $objResponse;
}

function AtivarDesativarPortDinamic($cod_curso, $cod_usu, $opcao, $frase1, $frase2){
  $objResponse = new xajaxResponse();
  $sock=Conectar($cod_curso);
  foreach($cod_usu as $cod => $cod_usuario){
    StatusPortfolio($sock,$cod_curso, $cod_usuario, $opcao);

    if ($opcao == 'ativar_port'){
      $msg='Ativado';
      $frase= $frase1; //PortfÃ³lio(s) ativado(s) com sucesso.
    }
    else{
      $msg='Desativado';//PortfÃ³lio(s) desativado(s) com sucesso.
      $frase= $frase2;
    }

    $test="status_port".$cod_usuario;
    $objResponse->assign($test,"innerHTML",$msg);

  }
  $confirma='true';
  $objResponse->call("mostraFeedback",$frase,$confirma);
  Desconectar($sock);
  return $objResponse;
}

function AlteraTodasFerramCompartilhadasDinamic($cod_curso, $cod_ferramenta, $status)
{
  $objResponse = new xajaxResponse();

  $sock = Conectar($cod_curso);
  $lista_ferramentas_disp = RetornaFerramCompartilhaveis($sock);
  Desconectar($sock);

  $sock = Conectar("");
  $lista_frases = RetornaListaDeFrases($sock, $cod_ferramenta);

  $tamanho = count($lista_ferramentas_disp);
  $confirma = "true";

  if ($status == 'false')
    $frase = RetornaFraseDaLista($lista_frases, 300); /* 300 - Todas as Ferramentas foram compartilhadas */
  else 
    $frase = RetornaFraseDaLista($lista_frases, 301); /* 301 - Todas as Ferramentas foram descompartilhadas */

  for($i = 0; $i < $tamanho; $i++) 
  {
    $cod_ferramenta_t = $lista_ferramentas_disp[$i]['cod_ferramenta'];
    if ($status == 'false')
    {
      if (VerificaFerramCompartilhada($sock, $cod_curso, $cod_ferramenta_t) == true)
      {
        if (DescompartilhaFerramenta($sock, $cod_curso, $cod_ferramenta_t) == false)
        {
          $confirma = "false";
          $frase = RetornaFraseDaLista($lista_frases, 302); /* 302 - Erro ao descompartilhar todas as ferramentas */
        }
      }
    }
    else
    {
      if (VerificaFerramCompartilhada($sock, $cod_curso, $cod_ferramenta_t) == false)
      {
        if (CompartilhaFerramenta($sock, $cod_curso, $cod_ferramenta_t) == false)
        {
          $confirma = "false";
          $frase = RetornaFraseDaLista($lista_frases, 303); /* 303 - Erro ao compartilhar todas as ferramentas */
        }
      }
    }
  }
  $objResponse->call("mostraFeedback",$frase,$confirma);
  Desconectar($sock);
  return $objResponse;
}

function AlterarFerramCompartilhadasDinamic($cod_curso, $cod_ferramenta, $cod_ferramenta_compart, $status)
{
  $objResponse = new xajaxResponse();
  $sock = Conectar("");
  $lista_frases = RetornaListaDeFrases($sock, $cod_ferramenta);

  if ($status == 'false')
  {
    if ((VerificaFerramCompartilhada($sock, $cod_curso, $cod_ferramenta_compart) == false) || 
        (DescompartilhaFerramenta($sock, $cod_curso, $cod_ferramenta_compart != false)))
    {
      $frase = RetornaFraseDaLista($lista_frases, 304); /* 304 - Ferramenta descompartilhada com sucesso */
      $confirma = "true";
    }
    else
    {
      $frase = RetornaFraseDaLista($lista_frases, 305); /* 305 - Erro ao descompartilhar a Ferramenta */
      $confirma = "false";
    }
  }
  else
  {  
      if ((VerificaFerramCompartilhada($sock, $cod_curso, $cod_ferramenta_compart) == true) || 
          (CompartilhaFerramenta($sock, $cod_curso, $cod_ferramenta_compart) != false))
    {
      $frase = RetornaFraseDaLista($lista_frases, 306); /* 306 - Ferramenta compartilhada com sucesso */
      $confirma = "true";
    }
    else
    {
      $frase = RetornaFraseDaLista($lista_frases, 307); /* 307 - Erro em compartilhar a Ferramenta */
      $confirma = "false";
    }
  }

  $objResponse->call("mostraFeedback",$frase,$confirma);
  Desconectar($sock);
  return $objResponse;
}


/**
 * UsuarioComMesmoPapel - Verifica se o usuario jah estah inscrito no curso com o mesmo papel
 * @param $email - email do usuario
 * @param $cod_curso - codigo do curso
 * @param $papel - papel do usuario no curso que serah verificado
 * @return Boolean - true, se o usuario jah estah inscrito no curso com o papel(tipo_usuario). False, caso contrario
 */
function UsuarioComMesmoPapel($email, $cod_curso, $papel) {
  $sock=Conectar("");

  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  /* Seleciona todos os papeis do usuario no determinado curso */
  $query  = "select UC.tipo_usuario ";
  $query .= "from ".$dbnamebase.".Usuario_curso UC ";
  $query .= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
  $query .= "where U.email = '".$email."' ";
  $query .= "and UC.cod_curso=".$cod_curso." ";

  $res = Enviar($sock,$query);

  $lista_tipo_usuario = RetornaArrayLinhas($res);

  /* Itera na lista de papeis para verificar se o usuario jah estah inscrito no curso com o papel */
  foreach($lista_tipo_usuario as $tipo_usuario => $linha) {
    if($linha['tipo_usuario']==$papel) {
      return true;
    }
  }

  return false;
}

?>
