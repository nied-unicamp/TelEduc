<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/importar.inc

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
  ARQUIVO : bibliotecas/importar.inc
  ========================================================== */

if (!defined("PERIODO_ESPERA_DESMONTAGEM"))
  define("PERIODO_ESPERA_DESMONTAGEM", (3600 * 5));

if (!defined("CH_SEP"))
  define ("CH_SEP", "~~");


/* **************************************************************************
  NomeCursoExtraido - Obt�m o nome do curso extra�do da tabela
       'Cursos_extraidos'.
  Entrada: $sock - sock Base do Ambiente
           $codigo - c�digo do curso extra�do
  Saida: nome do curso
************************************************************************** */
function NomeCursoExtraido($sock, $codigo)
{
  $query = "select codigo, nome
              from Cursos_extraidos
              where codigo = ".VerificaNumeroQuery($codigo);
  $id = Enviar($sock, $query);
  
  $linha = RetornaLinha($id);
  return ($linha['nome']);
}

/* **************************************************************************
  ExtraiValoresDaChaveAutenticacao - Extrai os valores da chave de
       autentica��o. Fun��o chamada por 'UsuarioEstaAutenticadoImportacao'.

  Entrada: $chave - chave de autentica��o
  Saida: array[]['cod_curso']   - c�digo do curso para o qual ser�o importados
                                  os itens;
                ['cod_usuario'] - c�digo do usu�rio que est� importando no curso
                                  acima;
                ['cod_curso_import'] - c�digo do curso do qual ser�o importados
                                  os itens;
                ['login_import'] - login do curso do qual ser�o importados itens;
                ['senha_import_crypt'] - senha criptografada do curso do qual
                                   ser�o importados os itens.
************************************************************************** */
function ExtraiValoresDaChaveAutenticacao($chave)
{
  list ($res['cod_curso'],
        $res['cod_usuario'],
        $res['cod_curso_import'],
        $res['login_import'],
        $res['senha_import_crypt']) = explode(CH_SEP, $chave);
  return ($res);
}

/* **************************************************************************
  CriaChaveDeAutenticacao - Cria uma chave de autentica��o para ser validada
           atrav�s da fun��o 'UsuarioEstaAutenticadoImportacao'.

  Entrada: $cod_curso          - c�digo do curso para o qual ser�o importados
                                 os itens;
           $cod_usuario        - c�digo do usu�rio que est� importando no
                                 curso acima;
           $cod_curso_import   - c�digo do curso do qual ser�o importados
                                 os itens;
           $login_import       - login do curso do qual ser�o importados itens;
           $senha_import_crypt - senha criptografada do curso do qual
                                 ser�o importados os itens.

  Saida:   chave de autentica��o
************************************************************************** */
function CriaChaveDeAutenticacao($cod_curso, $cod_usuario, $cod_curso_import, $login_import, $senha_import_crypt)
{

  $chave = $cod_curso.CH_SEP.$cod_usuario.CH_SEP.$cod_curso_import.CH_SEP.$login_import.CH_SEP.$senha_import_crypt;

  return ($chave);
}

/* **************************************************************************
  UsuarioEstaAutenticadoImportacao - Testa a autentica��o do usu�rio
       comparando a chave gerada, ARMAZENADA EM SESS�O, com as informa��es
       sobre o usu�rio.

  Entrada: $cod_curso          - c�digo do curso para o qual ser�o importados
                                 os itens;
           $cod_usuario        - c�digo do usu�rio que est� importando no
                                 curso acima;
           $cod_curso_import   - c�digo do curso do qual ser�o importados
                                 os itens;
           [$opt] - se igual a TMPDB conecta-se a base de dados tempor�ria

  Saida:   true se autenticado, false do contr�rio.
************************************************************************** */
function UsuarioEstaAutenticadoImportacao($cod_curso, $cod_usuario, $cod_curso_import, $opt = "")
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $res = "";
  unset ($res);
  $res = false;

  // Verifica se existe a chave de autentica��o na SESS�O
  if (isset($_SESSION['login_import_s']))
  {
    // Obt�m a chave de autentica��o
    $login_import = $_SESSION['login_import_s'];

    // Obt�m os valores da chave
    $dados = ExtraiValoresDaChaveAutenticacao($login_import);

    // Verifica a autentica��o do usu�rio.
    if ($dados['cod_curso_import'] == $cod_curso_import)
    {
      $sock = Conectar("", $opt);
      $query = "select UC.cod_usuario
                from ".$dbnamebase.".Usuario U
                inner join ".$dbnamebase.".Usuario_curso UC ON (UC.cod_usuario_global = U.cod_usuario and UC.cod_curso='".VerificaStringQuery($cod_curso_import)."')
                where U.login = '".$dados['login_import']."'
                and U.senha = '".$dados['senha_import_crypt']."'";
      $id = Enviar($sock, $query);

      if (1 == RetornaNumLinhas($id))
      {
        $linha = RetornaLinha($id);
        $res = $linha['cod_usuario'];
      }
      else
        $res = false;
    }
    else
      $res = false;
  }
  else
    $res = false;

  Desconectar($sock);
  return ($res);
}

/* **************************************************************************
  VerificaAutentImportacao - Verifica a autentica��o do usu�rio no curso
       especificado.

  Entrada: $cod_curso   - c�digo do curso DO qual ser�o importados
                          os itens;
           $login       - login do usu�rio no curso;
           $senha_crypt - senha criptografada do usu�rio no curso;
           [$opt] - se igual a TMPDB conecta-se a base de dados tempor�ria

  Saida:   true se autenticado, false do contr�rio.
************************************************************************** */
function VerificaAutentImportacao($cod_curso, $login, $senha_crypt, $opt = "", $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $sock = Conectar($cod_curso, $opt);


  $query = "  select UC.cod_usuario,UC.tipo_usuario
              from ".$dbnamebase.".Usuario U
              inner join ".$dbnamebase.".Usuario_curso UC ON (UC.cod_usuario_global = U.cod_usuario and UC.cod_curso='".$cod_curso."')
              where U.login = '".VerificaStringQuery($login)."'
              and U.senha = '".VerificaStringQuery($senha_crypt)."'";
                    
  $id = Enviar($sock, $query);

  $cod_usuario = "";
  unset($cod_usuario);
  $cod_usuario = false;
  
  if (RetornaNumLinhas($id) == 1)
  {
    $linha = RetornaLinha($id);
    if($linha['tipo_usuario']=='F')
      $cod_usuario = $linha['cod_usuario'];
    else
      return false;
  }
  Desconectar($sock);
  return($cod_usuario);
}

/* **************************************************************************
  RetornaCategoriasCursos - Retorna uma array com as categorias em que os
                              cursos est�o divididos
                              (somente as categorias que contem algum curso)
  Entrada: $sock - sock Base Externa
           $tipo_curso - tipo do curso
                         'A' - Em andamento
                         'I' - Com inscri��o aberta
                         'E' - Encerrado
                         'L' - Latente
           $cod_ferramenta - codigo da ferramenta
  Saida: array com []['cod_pasta']
                   []['pasta']
                   []['qtos_cursos']
************************************************************************** */
function RetornaCategoriasCursos($sock, $tipo_curso, $cod_ferramenta)
{
  $hoje = time();
  $ontem  = $hoje - 86400;
  
  if($tipo_curso=='') $tipo_curso='A';

  $where="";
  
  if ($tipo_curso == 'A')  // Cursos em andamento
    $where = " C.curso_inicio <= ".$hoje." and C.curso_fim >= ".$ontem;
  elseif ($tipo_curso == 'I') // Cursos com Inscricoes Abertas
    $where = " ((C.inscricao_inicio <= ".$hoje.") and
                (C.inscricao_fim >= ".$ontem.")) ";
  elseif ($tipo_curso == 'E') // Cursos Encerrados
    $where = " C.curso_fim < ".$ontem;
  elseif ($tipo_curso == 'L') // Cursos Latentes
  {
    $where = " (((C.curso_inicio > ".$hoje.") and
                 ((C.inscricao_inicio > ".$hoje.") or
                  (C.inscricao_fim < ".$hoje."))
                ) or
                ((C.curso_inicio is NULL) and
                 (C.curso_fim is NULL) and
                 (C.inscricao_inicio is NULL) and
                 (C.inscricao_fim is NULL))
               )";
  }


  $query  = "select CP.cod_pasta, CP.pasta, count(*) 'qtos_cursos'
               from Cursos_pastas as CP,
                    Cursos as C
               where ".$where." AND CP.cod_pasta = C.cod_pasta
               group by CP.cod_pasta
               order by CP.cod_pasta";

  $res   = Enviar ($sock, $query);
  $Lista_Categorias = RetornaArrayLinhas ($res);
  return $Lista_Categorias;
}

/* **************************************************************************
  RetornaTodosCursos - Retorna uma array com todos os cursos, cuja data de
                         inicio esteja no periodo determinado por
                         $periodo_inicio e $periodo_fim

  Entrada: $sock - sock Base Externa
           $tipo_curso - tipo do curso: 'A' - em andamento
                                        'I' - com inscri��es abertas
                                        'L' - latentes
                                        'E' - encerrados
           $cod_categoria - c�digo da categoria da qual ser�o listados os cursos
           $periodo_inicio - data inicial do periodo (em formato UnixTime)
           $periodo_fim - data final do periodo (em formato UnixTime)

  Saida: array com []['cod_curso']
                   []['nome_curso']
                   []['status'] - 'B' em base de dados, 'E' extra�do
************************************************************************** */
function RetornaTodosCursos ($sock, $tipo_curso, $cod_categoria, $periodo_inicio, $periodo_fim)
{


  $hoje = time();
  $ontem = $hoje - 86400;

  if($tipo_curso=="") $tipo_curso='A';

  if ($tipo_curso == 'A')
    $where = " curso_inicio <= ".$hoje." and
               curso_fim >= ".$ontem;
  else if ($tipo_curso == 'I')
    $where = " inscricao_inicio <= ".$hoje." and
               inscricao_fim >= ".$ontem;
  else if ($tipo_curso == 'L')
    $where = "(((curso_inicio > ".$hoje.") and
                ((inscricao_inicio > ".$hoje.") or
                 (inscricao_fim < ".$hoje."))
               ) or
               ((curso_inicio is NULL) and
                (curso_fim is NULL) and
                (inscricao_inicio is NULL) and
                (inscricao_fim is NULL))
              )";
  else if ($tipo_curso == 'E')
  {
    if ($periodo_fim > $ontem)
      $periodo_fim = $ontem;
      
    $where = " curso_fim < ".VerificaNumeroQuery($periodo_fim)." and
               curso_inicio >= ".VerificaNumeroQuery($periodo_inicio);
  }
  if ($cod_categoria == "NULL" || $cod_categoria == "")
    $pasta = " is NULL";
  else if (is_int((int) $cod_categoria))
    $pasta = " = ".VerificaNumeroQuery($cod_categoria);

  $query = "select cod_curso, nome_curso, 'B' as 'status'
              from Cursos
              where ".$where." and
                    cod_pasta ".$pasta."
              order by nome_curso";

  $res   = Enviar ($sock, $query);
  $Lista_Cursos = RetornaArrayLinhas ($res);

  // Se estiver listando um curso Encerrado e a categoria for
  // Cursos gerais, listamos os cursos extra�dos.
  if (($tipo_curso == 'E') &&
      (($cod_categoria == "NULL" || $cod_categoria == ""))
     )
  {
    $Lista_Cursos_Extraidos = RetornaTodosCursosExtraidos($sock, $periodo_inicio, $periodo_fim);

    if (is_array($Lista_Cursos) && is_array($Lista_Cursos_Extraidos))
      $lista = array_merge($Lista_Cursos, $Lista_Cursos_Extraidos);
    elseif (is_array($Lista_Cursos_Extraidos))
      $lista = $Lista_Cursos_Extraidos;
    elseif (is_array($Lista_Cursos))
      $lista = $Lista_Cursos;
    else
      $lista = NULL;

    $Lista_Cursos = $lista;
    
  }

  return $Lista_Cursos;
}

/* **************************************************************************
  RetornaTodosCursosExtraidos - Retorna uma array com todos os cursos extra�dos
                         (tabela 'Cursos_extraidos'), cuja data de inicio esteja
                         no periodo determinado por $periodo_inicio e $periodo_fim

  Entrada: $sock - sock Base Externa
           $periodo_inicio - data inicial do periodo (em formato UnixTime)
           $periodo_fim - data final do periodo (em formato UnixTime)

  Saida: array com []['cod_curso']
                   []['nome_curso']
                   []['status'] - 'B' em base de dados, 'E' extra�do
************************************************************************** */
function RetornaTodosCursosExtraidos($sock, $periodo_inicio, $periodo_fim)
{
  $query  = "select codigo as 'cod_curso', nome as 'nome_curso', 'E' as 'status'
               from Cursos_extraidos
               where curso_fim < ".VerificaNumeroQuery($periodo_fim)." and
                     curso_inicio >= ".VerificaNumeroQuery($periodo_inicio)." and
                     curso_inicio <= ".VerificaNumeroQuery($periodo_fim)."
               order by codigo";
  $res   = Enviar ($sock, $query);
  $Lista_Cursos = RetornaArrayLinhas ($res);

  return $Lista_Cursos;
}

/* **************************************************************************
  RetornaCursosAndamentoCompart - Retorna todos os cursos com
                                     compartilhamento e que est�o em andamento
                                     (que compartilham a ferramenta com
                                     cod_ferramenta). O curso deve pertencer
                                     a categoria especificada por $categoria.
                                     O inicio do curso (Curso_Inicio) tamb�m
                                     deve estar dentro do periodo especificado

  Entrada: $sock - sock Base Externa
           $cod_ferramenta - c�digo da ferramenta compartilhada
           $cod_categoria - c�digo da categoria (pasta) a que pertence o curso.
                        Se $cod_categoria = "NULL", ent�o c�digo da pasta = NULL
                        (curso estar� na categoria "Cursos Gerais")
           $periodo_inicio - data inicial do periodo (em formato UnixTime)
           $periodo_fim - data final do periodo (em formato UnixTime)

  Saida: array com []['cod_curso']
                   []['nome_curso']
                   []['status'] - 'B' em base de dados, 'E' extra�do
************************************************************************** */
function RetornaCursosAndamentoCompart($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim)
{
  $horario = time();
  $query  = "select C.cod_curso, C.nome_curso, 'B' as 'status'
               from Cursos as C,
                    Cursos_compart as Cc
               where C.cod_curso = Cc.cod_curso and
                     Cc.cod_ferramenta = ".VerificaNumeroQuery($cod_ferramenta)." and
                     C.curso_inicio <= ".$horario." and
                     C.curso_fim >=  ".$horario." and
                     C.cod_pasta ".((($cod_categoria == "NULL")||($cod_categoria=="")) ? ("is NULL") : ("= ".VerificaNumeroQuery($cod_categoria)))."
               order by C.nome_curso";

  $res   = Enviar ($sock, $query);
  $Lista_Cursos = RetornaArrayLinhas ($res);
  return $Lista_Cursos;
}

/* **************************************************************************
  RetornaCursosLatentesCompart - Retorna todos os cursos com
                                     compartilhamento (que compartilham a
                                     ferramenta com cod_ferramenta) e que s�o
                                     latentes (periodo de realiza��o ainda n�o
                                     come�ou e n�o se encontram em per�odo de
                                     inscri��o)
                                     O curso deve pertencer a categoria
                                     especificada por $categoria.

  Entrada: $sock - sock Base Externa
           $cod_ferramenta - c�digo da ferramenta compartilhada
           $cod_categoria - c�digo da categoria (pasta) a que pertence o curso.
                        Se $categoria = NULL, ent�o c�digo da pasta = NULL
                        (curso estar� na categoria "Cursos Gerais")

  Saida: array com []['cod_curso']
                   []['nome_curso']
                   []['status'] - 'B' em base de dados, 'E' extra�do
************************************************************************** */
function RetornaCursosLatentesCompart ($sock, $cod_ferramenta, $cod_categoria)
{
  $hoje = time();

  $query  = "select C.cod_curso, C.nome_curso, 'B' as 'status'
               from Cursos as C,
                    Cursos_compart as Cc
               where C.cod_curso = Cc.cod_curso and
                     Cc.cod_ferramenta = ".VerificaNumeroQuery($cod_ferramenta)." and
                     (((C.curso_inicio > ".$hoje.") and
                       ((C.inscricao_inicio > ".$hoje.") or
                        (C.inscricao_fim < ".$hoje."))
                      ) or
                      ((C.curso_inicio is NULL) and
                       (C.curso_fim is NULL) and
                       (C.inscricao_inicio is NULL) and
                       (C.inscricao_fim is NULL))
                     ) and
                     C.cod_pasta ".((($cod_categoria == "NULL")||($cod_categoria=="")) ? ("is NULL") : ("= ".VerificaNumeroQuery($cod_categoria)))."
               order by C.nome_curso";

  $res   = Enviar ($sock, $query);
  $Lista_Cursos = RetornaArrayLinhas ($res);
  return $Lista_Cursos;
}

/* **************************************************************************
  RetornaCursosInscrAbertasCompart - Retorna todos os cursos com
                                          compartilhamento (que compartilham
                                          a ferramenta com cod_ferramenta) e
                                          que est�o com as inscri��es abertas
                                          O curso deve pertencer a categoria
                                          especificada por $categoria.
                                          O inicio do curso (curso_inicio)
                                          tamb�m deve estar dentro do periodo
                                          especificado.

  Entrada: $sock - sock Base Externa
           $cod_ferramenta - c�digo da ferramenta compartilhada
           $cod_categoria - c�digo da categoria (pasta) a que pertence o curso.
                        Se $categoria = NULL, ent�o c�digo da pasta = NULL
                        (curso estar� na categoria "Cursos Gerais")
           $periodo_inicio - data inicial do periodo (em formato UnixTime)
           $periodo_fim - data final do periodo (em formato UnixTime)

  Saida: array com []['cod_curso']
                   []['nome_curso']
                   []['status'] - 'B' em base de dados, 'E' extra�do
************************************************************************** */
function RetornaCursosInscrAbertasCompart ($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim)
{
  $hoje = time();
  $ontem = $hoje - 86400;

  $query  = "select C.cod_curso, C.nome_curso, 'B' as 'status'
               from Cursos as C,
                    Cursos_compart as Cc
               where C.cod_curso = Cc.cod_curso and
                     Cc.cod_ferramenta = ".VerificaNumeroQuery($cod_ferramenta)." and
                     C.inscricao_inicio <= ".$hoje." and
                     C.inscricao_fim >= ".$ontem." and
                     C.cod_pasta ".((($cod_categoria == "NULL")||($cod_categoria=="")) ? ("is NULL") : ("= ".VerificaNumeroQuery($cod_categoria)))."
               order by C.nome_curso";

  $res   = Enviar ($sock, $query);
  $Lista_Cursos = RetornaArrayLinhas ($res);
  return $Lista_Cursos;
}

/* **************************************************************************
  RetornaCursosEncerradosCompart - Seleciona todos os cursos com
                                          compartilhamento (que compartilham a
                                          ferramenta com cod_ferramenta) e que
                                          foram encerrados.
                                          O curso deve pertencer a categoria
                                          especificada por $categoria.
                                          O inicio do curso (curso_inicio)
                                          tamb�m deve estar dentro do periodo
                                          especificado.

  Entrada: $sock - sock Base Externa
           $cod_ferramenta - c�digo da ferramenta compartilhada
           $cod_categoria - c�digo da categoria (pasta) a que pertence o curso.
                        Se $categoria = NULL, ent�o c�digo da pasta = NULL
                        (curso estar� na categoria "Cursos Gerais")
           $periodo_inicio - data inicial do periodo (em formato UnixTime)
           $periodo_fim - data final do periodo (em formato UnixTime)

  Saida: array com []['cod_curso']
                   []['nome_curso']
                   []['status] - 'B' em base de dados, 'E' extra�do
************************************************************************** */
function RetornaCursosEncerradosCompart ($sock, $cod_ferramenta, $cod_categoria, $periodo_inicio, $periodo_fim)
{
  $query  = "select C.cod_curso, C.nome_curso, 'B' as 'status'
               from Cursos as C,
                    Cursos_compart as Cc
               where C.cod_curso = Cc.cod_curso and
                     Cc.cod_ferramenta = ".VerificaNumeroQuery($cod_ferramenta)." and
                     C.curso_fim < ".VerificaNumeroQuery($periodo_fim)." and
                     C.curso_inicio >= ".VerificaNumeroQuery($periodo_inicio)." and
                     C.curso_inicio <= ".VerificaNumeroQuery($periodo_fim)." and
                     C.cod_pasta ".((($cod_categoria == "NULL")||($cod_categoria=="")) ? ("is NULL") : ("= ".VerificaNumeroQuery($cod_categoria)))."
               order by C.nome_curso";

	       
  $res   = Enviar ($sock, $query);
  $Lista_Cursos_Encerrados = RetornaArrayLinhas ($res);

  $Lista_Cursos="";

  // Se a categoria for a geral, listamos tamb�m os cursos extra�dos.
  if ($cod_categoria == "NULL" && is_array($Lista_Cursos_Extraidos = RetornaCursosExtraidosCompart($sock, $cod_ferramenta, $periodo_inicio, $periodo_fim)) && is_array($Lista_Cursos_Encerrados))
  {
        $Lista_Cursos = array_merge($Lista_Cursos_Encerrados , $Lista_Cursos_Extraidos);
  }
  else if (is_array($Lista_Cursos_Encerrados))
    $Lista_Cursos = $Lista_Cursos_Encerrados;
  else if (is_array($Lista_Cursos_Extraidos))
    $Lista_Cursos = $Lista_Cursos_Extraidos;

  return $Lista_Cursos;
}


/* **************************************************************************
  RetornaCursosExtraidosCompart - Seleciona todos os cursos extra�dos com
                                          compartilhamento (que compartilham a
                                          ferramenta com cod_ferramenta).
                                          O inicio do curso (curso_inicio)
                                          tamb�m deve estar dentro do periodo
                                          especificado.

  Entrada: $sock - sock Base Externa
           $cod_ferramenta - c�digo da ferramenta compartilhada
           $periodo_inicio - data inicial do periodo (em formato UnixTime)
           $periodo_fim - data final do periodo (em formato UnixTime)

  Saida: array com []['cod_curso']
                   []['nome_curso']
                   []['status] - 'B' em base de dados, 'E' extra�do
************************************************************************** */
function RetornaCursosExtraidosCompart($sock, $cod_ferramenta, $periodo_inicio, $periodo_fim)
{
  // Para cursos extraidos ignoramos a categoria a qual pertencia,
  // pois ela pode n�o mais existir.
  $query  = "select Ce.codigo as 'cod_curso', Ce.nome as 'nome_curso', 'E' as 'status'
               from Cursos_extraidos Ce
                 inner join Cursos_extraidos_compart Cec
                   on Ce.codigo = Cec.codigo and
                      Cec.cod_ferramenta = ".VerificaNumeroQuery($cod_ferramenta)."
               where Ce.curso_fim < ".VerificaNumeroQuery($periodo_fim)." and
                     Ce.curso_inicio >= ".VerificaNumeroQuery($periodo_inicio)." and
                     Ce.curso_inicio <= ".VerificaNumeroQuery($periodo_fim)."
               order by Ce.codigo";
  $res   = Enviar ($sock, $query);
  $Lista_Cursos = RetornaArrayLinhas ($res);
  return $Lista_Cursos;
}

/* **************************************************************************
  FerramentaEstaCompartilhada - Retorna todos os cursos com
                                     compartilhamento (que compartilham a
                                     ferramenta com cod_ferramenta) e que s�o
                                     latentes (periodo de realiza��o ainda n�o
                                     come�ou e n�o se encontram em per�odo de
                                     inscri��o)
                                     O curso deve pertencer a categoria
                                     especificada por $categoria.

  Entrada: $sock - sock Base Externa
           $cod_ferramenta - c�digo da ferramenta compartilhada
           $categoria - c�digo da categoria (pasta) a que pertence o curso.
                        Se $categoria = -1, ent�o c�digo da pasta = NULL
                        (curso estar� na categoria "Cursos Gerais")

  Saida: array com []['cod_curso']
                   []['nome_curso']
************************************************************************** */
function FerramentaEstaCompartilhada($sock, $cod_curso, $cod_ferramenta)
{
  $query  = "select cod_curso
               from Cursos_compart
               where cod_curso = ".VerificaNumeroQuery($cod_curso)." and
                     cod_ferramenta = ".VerificaNumeroQuery($cod_ferramenta);

  $res   = Enviar ($sock, $query);
  $qtas_linhas = RetornaNumLinhas ($res);

  if ($qtas_linhas > 0)
    return true;
  else
    return false;
}

/* **************************************************************************
  CursoFaseDeMontagem - Retorna a fase de montagem de um curso extra�do para
           evitar duas montagens, acesso enquanto ainda n�o montado ou sua
           demontagem prematura.

  Entrada: $sock - sock Base do Ambiente
           $codigo - c�digo do curso

  Saida: fase: 'nao'         - curso n�o est� montado;
               'montando'    - curso est� sendo montado;
               'sim'         - curso foi montado;
               'desmontando' - curso est� sendo desmontado.
************************************************************************** */
function CursoFaseDeMontagem($sock, $codigo)
{
  $query = "select montado
              from Cursos_extraidos
              where codigo = ".VerificaNumeroQuery($codigo);
              
  $id = Enviar($sock, $query);
  $linha = RetornaLinha($id);
  
  return ($linha['montado']);
}

/* **************************************************************************
  RetornaTabelasEPastasFerrCompartExtraido - Retorna lista de tabelas (e as
           tabelas 'Usuario', 'Cursos' e 'Curso_ferramentas') e de pastas
           utilizadas pelas ferramentas compartilhadas de um curso extra�do.

  Entrada: $codigo - c�digo do curso
           $curso_compartilhado - booleano. true se compartilhado, false se
                       autenticado.

  Saida: array(0 => - array de tabelas utilizadas pelas ferramentas
               1 => - array de pastas utilizadas pelas ferramentas
              )
************************************************************************** */
function RetornaTabelasEPastasFerrCompartExtraido($codigo, $curso_compartilhado)
{
  $ferr_consultas = array( 1 => array( 'TABELA' => array( 'Agenda_itens'
                                                        , 'Agenda_itens_historicos')
                                     , 'PASTA' => 'agenda')
                         , 4 => array( 'TABELA' => array( 'Apoio_itens'
                                                        , 'Apoio_itens_enderecos'
                                                        , 'Apoio_itens_historicos'
                                                        , 'Apoio_topicos')
                                     , 'PASTA' => 'apoio')
                         , 3 => array( 'TABELA' => array( 'Atividade_itens'
                                                        , 'Atividade_itens_enderecos'
                                                        , 'Atividade_itens_historicos'
                                                        , 'Atividade_topicos')
                                     , 'PASTA' => 'atividades')
                         , 16 => array( 'TABELA' => array( 'Dinamica')
                                      , 'PASTA' => 'dinamica')
                         , 5 => array( 'TABELA' => array( 'Leitura_itens'
                                                        , 'Leitura_itens_enderecos'
                                                        , 'Leitura_itens_historicos'
                                                        , 'Leitura_topicos')
                                     , 'PASTA' => 'leituras')
                         , 7 => array( 'TABELA' => array( 'Obrigatoria_itens'
                                                        , 'Obrigatoria_itens_enderecos'
                                                        , 'Obrigatoria_itens_historicos'
                                                        , 'Obrigatoria_topicos')
                                     , 'PASTA' => 'obrigatoria')
                         , 6 => array( 'TABELA' => array( 'Pergunta_assuntos'
                                                        , 'Pergunta_itens')
                                     , 'PASTA' => '')
                         , 22 => array( 'TABELA' => array( 'Avaliacao'
                                                         , 'Avaliacao_historicos'
                                                         , 'Avaliacao_notas')
                                      , 'PASTA' => '')
                         );

  // Inicializamos as vari�veis de retorno
  $tabelas = $pastas = array();

  // Se � um curso compartilhado ent�o obt�m as ferramentas
  // compartilhadas
  if ($curso_compartilhado)
  {
    $sock = Conectar("");
    
    // Obtemos os c�digos das ferramentas compartilhadas para
    // o curso extra�do.
    $query = "select cod_ferramenta
              from Cursos_extraidos_compart
              where codigo = ".VerificaNumeroQuery($codigo);

    $id = Enviar($sock, $query);
    $total = RetornaNumLinhas($id);
    $linhas = RetornaArrayLinhas($id);

    // Para cada ferramenta obtemos as tabelas por ela
    // utilizadas.
    for ($i = 0; $i < $total; $i++)
    {
      $cod_ferramenta = $linhas[$i]['cod_ferramenta'];
      $tmp = $ferr_consultas[$cod_ferramenta]['TABELA'];

      // Copiamos cada tabela usada � vari�vel de retorno
      for ($j = 0, $totB = count($tmp); $j < $totB; $j++)
        $tabelas[] = $tmp[$j];

      // Se houver uma pasta de arquivos ent�o copiamo-na
      // para a vari�vel de retorno.
      if ('' <> trim($ferr_consultas[$cod_ferramenta]['PASTA']))
        $pastas[] = $ferr_consultas[$cod_ferramenta]['PASTA'];
    }

    Desconectar($sock);
  }
  else if (!$curso_compartilhado)
  // Se autenticou-se no curso ent�o todas as ferramentas devem
  // estar dispon�veis.
  {
    $linhas = array_keys($ferr_consultas);
 
    $total_ferramentas_compartilhaveis = count($ferr_consultas);
    
    // Para cada ferramenta obtemos as tabelas por ela
    // utilizadas.
    for ($i = 0; $i < $total_ferramentas_compartilhaveis; $i++)
    {
      $cod_ferramenta = $linhas[$i];
      $tmp = $ferr_consultas[$cod_ferramenta]['TABELA'];

      // Copiamos cada tabela usada � vari�vel de retorno
      for ($j = 0, $totB = count($tmp); $j < $totB; $j++)
        $tabelas[] = $tmp[$j];

      // Se houver uma pasta de arquivos ent�o copiamo-na
      // para a vari�vel de retorno.
      if ('' <> trim($ferr_consultas[$cod_ferramenta]['PASTA']))
        $pastas[] = $ferr_consultas[$cod_ferramenta]['PASTA'];
    }
  }

  // Copiamos tamb�m tabelas usadas pelo curso.
  $tabelas[] = 'Usuario';
  $tabelas[] = 'Cursos';
  $tabelas[] = 'Curso_ferramentas';
  
  return (array($tabelas, $pastas));
}

/* **************************************************************************
  DesmontarCursos - Desmonta os cursos tempor�rios baseado em sua data de
       �ltimo acesso. Se a data de �ltimo acesso de um curso (montado, que
       estava sendo montado ou desmontado) for menor que o hor�rio em
       que foi chamada esta fun��o subtra�do de uma margem de seguran�a, ent�o
       esse curso deve ser desmontado. O teste � feito em cima da data n�o
       importando muito a fase de montagem em que se encontrava, pois poderia
       ter ocorrido um erro na montagem ou demontagem do mesmo, logo ele deve
       ser demontado.

  Entrada: nenhuma

  Saida: nenhuma
************************************************************************** */
function DesmontarCursos()
{
  global $dbtmpnamecurso;

  $sock = Conectar("");

  // Obt�m o caminho dos arquivos dos cursos montados.
  $query = "select diretorio
              from Diretorio
              where item = 'Montagem'";
  $id = Enviar($sock, $query);
  $linha = RetornaLinha($id);
  $dir_montagem = $linha['diretorio'];

  // Hor�rio da desmontagem
  $horario_desmontagem = time();

  // Altera o estado  e a data de �ltimo acesso de todos os cursos, cujos estados
  // eram diferentes de 'nao' e a data de �ltimo acesso era menor que o hor�rio
  // atual subtra�do de uma margem de seguran�a.
  $query = "update Cursos_extraidos
              set montado = 'desmontando',
                  data_ultimo_acesso = ".$horario_desmontagem."
              where montado <> 'nao' and
                    data_ultimo_acesso < ".($horario_desmontagem - PERIODO_ESPERA_DESMONTAGEM);

  if (Enviar($sock, $query))
  {
    // Obt�m os cursos que ser�o desmontados.
    $query = "select codigo
                from Cursos_extraidos
                where montado = 'desmontando' and
                      data_ultimo_acesso = ".$horario_desmontagem;
                
    $id = Enviar($sock, $query);
    if (0 < RetornaNumLinhas($id))
    {
       $linhas = RetornaArrayLinhas($id);
       Desconectar($sock);
       
 //      unset ($flag);

       $sock = Conectar("", TMPDB);

       foreach ($linhas as $idx => $dados)
       {
         $query = "drop database ".$dbtmpnamecurso.$dados['codigo'];
         // Sem verifica��o de erros pois a montagem de um curso
         // poderia ter sido interrompida antes da cria��o da base de dados.
         $flag = EnviarNC($sock, $query);

         if (ExisteArquivo($dir_montagem."/".$dados['codigo']))
           RemoveDiretorio($dir_montagem."/".$dados['codigo']);

       }
       Desconectar($sock);
       
       $sock = Conectar("");
       
       $query = "update Cursos_extraidos
                   set montado = 'nao'
                   where data_ultimo_acesso = ".$horario_desmontagem." and
                         montado = 'desmontando'";
                         
       Enviar($sock, $query);
    }
  }
  Desconectar($sock);
}

/* **************************************************************************
  MontaCursoExtraidoTemporario - Monta as tabelas e pastas das ferramentas de
     um curso extra�do.

  Entrada: $dbtmpnamecurso (VARI�VEL GLOBAL) - prefixo do nome da base de dados
                   do curso;
           $codigo - c�digo do curso;
           $pastas - array de pastas a serem copiadas
           $tabelas - array de tabelas a serem inseridas

  Saida: true se bem sucedido.
************************************************************************** */
function MontaCursoExtraidoTemporario($codigo, $pastas, $tabelas)
{
  global $dbtmpnamecurso;
  $sock = Conectar("");

  $query = "update Cursos_extraidos
                set montado = 'montando',
                    data_ultimo_acesso = ".time()."
                where codigo = ".VerificaNumeroQuery($codigo);

  Enviar($sock, $query);

  $query = "select caminho, versao_teleduc
              from Cursos_extraidos
              where codigo = ".VerificaNumeroQuery($codigo);
  $id = Enviar($sock, $query);
  $linha = RetornaLinha($id);

  $caminho = $linha['caminho'];
  $versao_teleduc = $linha['versao_teleduc'];

  $dir_montagem = RetornaDiretorio($sock, "Montagem");

  $id = Enviar($sock, $query);
  $linha = RetornaLinha($id);

  RemoveDiretorio($dir_montagem."/".$codigo);
  if (!CriaDiretorio($dir_montagem."/".$codigo))
  {
    $query = "update Cursos_extraidos
                set montado = 'montando',
                    data_ultimo_acesso = ".time()."
                where codigo = ".VerificaNumeroQuery($codigo);

    Enviar($sock, $query);

    die(__LINE__.": ERRO! N�o foi poss�vel criar pasta!!");
  }
  Desconectar($sock);

  if (ExisteArquivo($caminho."/".NOME_ARQUIVOS))
  {
    $dir_corrente = RetornaDiretorioCorrente();
    MudarParaDiretorio($dir_montagem."/".$codigo);

    $diretorios = implode(" ", $pastas);

    if (!(shell_exec("/bin/tar -zxvf ".$caminho."/".NOME_ARQUIVOS." ".$diretorios)))
    {
      // ?? - Erro ao descompactar arquivos
      die("<br>"."[Erro ao descompactar arquivos]"."<p>\n");
    }
    MudarParaDiretorio($dir_corrente);
  }
  else
  {
    // copia todo o diretorio do curso extraido para o novo diretorio no caminho dos
    // cursos atuais (j� com o novo numero do curso)

    foreach ($pastas as $dir)
    {
      if (!(CopiaDiretorio($caminho."/".$dir, $dir_montagem."/".$codigo)))
        // ?? - Erro na c�pia de arquivos do curso
        die("<br>"."[Erro na c�pia de arquivos do curso]"."<p>\n");
    }
  }

  // Conecta-se ao servidor tempor�rio de cursos.
  $sock = Conectar("", TMPDB);

  $query = "create database ".$dbtmpnamecurso.VerificaNumeroQuery($codigo);
  
  if (Enviar($sock, $query))
  {
    // Alterna para a base de dados rec�m criada.
    $sock=MudarDB($sock, $codigo, TMPDB);

    // Procura pelo arquivo dump do CURSO
    $arquivo = ListaArquivos($caminho, '*'.DUMP_CURSO, 'A', 1);
    if (!is_array($arquivo))
      die(__LINE__.": "."Erro! N�o foi poss�vel localizar o arquivo de dump do curso");
    $ARQUIVO_DUMP = $arquivo[0]['Caminho'];
    
    // Insere as tabelas especificadas na base de dados.
    InsereTabelasBD($sock, $tabelas, $ARQUIVO_DUMP);
    $query = "lock tables Cursos write";
    Enviar($sock, $query);
    $query = "update Cursos set cod_curso = ".VerificaNumeroQuery($codigo);
    Enviar($sock, $query);
    $query = "unlock tables";
    Enviar($sock, $query);
    AtualizaTabelasVersaoCurso($sock, $tabelas, $versao_teleduc);
    
    Desconectar($sock);
    
    $sock = Conectar("");
    $query = "update Cursos_extraidos
                set montado = 'sim',
                    data_ultimo_acesso = ".time()."
                where codigo = ".VerificaNumeroQuery($codigo);
                
    Enviar($sock, $query);
  }
  
  Desconectar($sock);

  return true;
}

/* **************************************************************************
  MarcarAcessoCursoExtraidoTemporario - Marca a data de �ltimo acesso ao curso
     extra�do. Essa fun��o � de suma import�ncia para evitar que um curso montado,
     que esteja sendo acessado, possa ser desmontado.

  Entrada: $sock - socket de conex�o da base do ambiente
           $codigo - c�digo do curso.

  Saida: true se bem sucedido, false do contr�rio.
************************************************************************** */
function MarcarAcessoCursoExtraidoTemporario($sock, $codigo)
{
  $query = "update Cursos_extraidos
              set data_ultimo_acesso = ".time()."
              where codigo = ".VerificaNumeroQuery($codigo);
              
  return(Enviar($sock, $query));

}

function AlterarPeriodoDinamic($dadosForm){
  $objResponse = new xajaxResponse();

  $cod_categoria=$dadosForm['cod_categoria'];
  $cod_topico_raiz=$dadosForm['cod_topico_raiz'];
  $tipo_curso=$dadosForm['tipo_curso'];
  $cod_ferramenta=$dadosForm['cod_ferramenta'];
  $data_inicio=Data2UnixTime($dadosForm['data_inicio']);
  $data_fim=Data2UnixTime($dadosForm['data_fim']);
  $frase = $dadosForm['extraido'];

  $sock=Conectar("");

  $todos_cursos = RetornaTodosCursos($sock, $tipo_curso, $cod_categoria, $data_inicio, $data_fim);
  $objResponse->script("var y;");
  
  foreach($todos_cursos as $chave => $valor){
    $objResponse->script("y= document.createElement('option');");
    $objResponse->script("y.innerHTML='".$valor['nome_curso']." ".(($valor['status'] == 'E') ? $frase:"")."'");
    $objResponse->script("y.value='".$valor['status'].";".$valor["cod_curso"]."'");
    $objResponse->script("document.getElementById('cod_curso_todos').appendChild(y);");
  }


  $cursos_compart = RetornaCursosEncerradosCompart($sock, $cod_ferramenta, $cod_categoria, $data_inicio, $data_fim);
  $objResponse->script("var y;");
  
  foreach($todos_cursos as $chave => $valor){
    $objResponse->script("y= document.createElement('option');");
    $objResponse->script("y.innerHTML='".$valor['nome_curso']." ".(($valor['status'] == 'E') ? $frase:"")."'");
    $objResponse->script("y.value='".$valor['status'].";".$valor["cod_curso"]."'");
    $objResponse->script("document.getElementById('cod_curso_compart').appendChild(y);");
  }

  Desconectar($sock);

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;

  

}
?>
