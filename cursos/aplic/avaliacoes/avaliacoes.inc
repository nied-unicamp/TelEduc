<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/avaliacoes.inc

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

/*==========================================================
  ARQUIVO : cursos/aplic/avaliacoes/avaliacoes.inc
  ========================================================== */
  /*
    Status criados
    Tabela Avaliacao
    A - avalia�o apagada
    C - avalia�o em cria�o
    E - avalia�o em edi�o
    F - avalia�o finalizada
    X - avalia�o exclu�a

    Tabela Avaliacao_notas
    A - nota anterior
    E - avalia�o de aluno em edi�o
    F - nota final
    X - nota exclu�a

    Tabela Avaliacao_historicos
    A - avalia�o apagada
    C - avalia�o em cria�o
    D - edi�o cancelada
    E - avalia�o em edi�o
    F - edi�o finalizada
    X - avalia�o exclu�a
    R - avalia�o recuperada

    Tabela Portfolio_itens_avaliacao
    A - Item de Portfolio Avaliado
    N - Item de Portfolio Nao Avaliado

  */

define("COD_AVALIACAO", 22);

/* *********************************************************************
   RetornaDiretorio - Retorna o Diret�rio da tabela de diretorios
   Entrada: $sock - BASE EXTERNA
            $item - nome do item a ser buscado
   Saida: string com o diret�rio
*/
function RetornaDiretorio($sock, $item)
{
  $query="select diretorio from Diretorio where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha[0]);
}

/* *********************************************************************
   StatusGrupo - Retorna o estado de um grupo
   Entrada: $sock - BASE DO CURSO
            $cod_grupo - grupo
   Saida: Status - A - Ativo, X - Apagado
*/
function StatusGrupo($sock, $cod_grupo)
{
  $consulta="select status from Grupos where cod_grupo=".$cod_grupo;
  $res=Enviar($sock, $consulta);
  $num=RetornaNumLinhas($res);
  $linha=RetornaLinha($res);
  if ($linha['status']!="X")
    $linha['status']="A";
  return $linha['status'];
}

/* *********************************************************************
   RetornaAvaliacoes - Retorna array com as todas as avaliacoes
   Entrada: $sock - BASE DO CURSO
            $usr_formador
   Saida: array com []['cod_Avaliacao']
                    []['Ferramenta']
                    []['Data_inicio']
                    []['Data_termino']
                    []['Titulo']
                    []['Tipo'] - individual ou Em Grupo/
*/
function RetornaAvaliacoes($sock, $usr_formador)
{
  $consulta="select cod_avaliacao,cod_atividade,ferramenta,valor,tipo,data,data_inicio,data_termino from Avaliacao where status='F' or status='E' order by data_inicio asc";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  unset($retorno);
  $cont=0;
  if (RetornaNumLinhas($res) > 0)
  {
    foreach ($lista as $cod => $linha)
    {
      if (!strcmp($linha['ferramenta'],'F'))
        $consulta="select nome from Forum where cod_forum=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'B'))
        $consulta="select assunto from Batepapo_assuntos where cod_assunto=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'E'))
        $consulta="select titulo from Exercicios_modelo where cod_exercicio=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'N'))        
        $consulta="select titulo from Avaliacao_externa where codigo=".$linha['cod_atividade'];
      else
      {
        $consulta="select titulo from Atividade_itens where cod_item=".$linha['cod_atividade'];
        if (!$usr_formador)
          $consulta.=" and tipo_compartilhamento='T'";
      }
      $res=Enviar($sock, $consulta);
      $titulo=RetornaLinha($res);
      if (($titulo!="")&&(!is_null($titulo)))
      {
        $retorno[$cont]['Cod_avaliacao'] = $linha['cod_avaliacao'];
        $retorno[$cont]['Ferramenta']    = $linha['ferramenta'];
        $retorno[$cont]['Valor']         = $linha['valor'];
        $retorno[$cont]['Data']          = $linha['data'];
        $retorno[$cont]['Data_inicio']   = $linha['data_inicio'];
        $retorno[$cont]['Data_termino']  = $linha['data_termino'];
        $retorno[$cont]['Titulo']        = $titulo[0];
        $retorno[$cont]['tipo']          = $linha['tipo'];
        $retorno[$cont]['Cod_atividade'] = $linha['cod_atividade'];
        $cont++;
      }
    }
  }
  return $retorno;
}


/* *********************************************************************
   RetornaAvaliacoesAtuais - Retorna array com as avaliacoes atuais
   Entrada: $sock - BASE DO CURSO
   Saida: array com []['cod_Avaliacao']
                    []['Ferramenta']
                    []['Data_inicio']
                    []['Data_termino']
                    []['Titulo']
*/
function RetornaAvaliacoesAtuais($sock, $usr_formador)
{
  $consulta="select cod_avaliacao,cod_atividade,ferramenta,tipo,data,data_inicio,data_termino from Avaliacao where data_inicio <=".time()." and data_termino >= ".time()." and status!='A' and status!='C' and status!='X' order by data_inicio desc";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);

  $retorno = array();
  $cont = 0;

  if (RetornaNumLinhas($res) > 0)
  {
    foreach ($lista as $cod => $linha)
    {
      if (!strcmp($linha['ferramenta'],'F'))
        $consulta="select nome from Forum where cod_forum=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'B'))
        $consulta="select assunto from Batepapo_assuntos where cod_assunto=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'E'))
        $consulta="select titulo from Exercicios_modelo where cod_exercicio=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'N'))
         $consulta="select titulo from Avaliacao_externa where codigo=".$linha['cod_atividade'];
      else
      {
        $consulta="select titulo from Atividade_itens where cod_item=".$linha['cod_atividade'];
        if (!$usr_formador)
          $consulta.=" and tipo_compartilhamento='T'";
      }
      $res=Enviar($sock, $consulta);
      $titulo=RetornaLinha($res);
      if (($titulo!="")&&(!is_null($titulo)))
      {
        $retorno[$cont]['Cod_avaliacao'] = $linha['cod_avaliacao'];
        $retorno[$cont]['Ferramenta']    = $linha['ferramenta'];
        $retorno[$cont]['Data']          = $linha['data'];
        $retorno[$cont]['Data_inicio']   = $linha['data_inicio'];
        $retorno[$cont]['Data_termino']  = $linha['data_termino'];
        $retorno[$cont]['Titulo']        = $titulo[0];
        $retorno[$cont]['tipo']          = $linha['tipo'];
        $cont++;
      }
    }
  }
  return $retorno;
}

/* *********************************************************************
   RetornaAvaliacoesFuturas - Retorna array com as avaliacoes futuras
   Entrada: $sock - BASE DO CURSO
   Saida: array com []['cod_Avaliacao']
                    []['Ferramenta']
                    []['Data_inicio']
                    []['Data_termino']
                    []['Titulo']
*/
function RetornaAvaliacoesFuturas($sock, $usr_formador)
{
  $consulta="select cod_avaliacao,cod_atividade,ferramenta,data,tipo,data_inicio,data_termino from Avaliacao where data_inicio > ".time()." and status!='A' and status!='C' and status!='X' order by data_inicio asc";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);

  $retorno = array();
  $cont = 0;

  if (RetornaNumLinhas($res) > 0)
  {
    foreach ($lista as $cod => $linha)
    {
      if (!strcmp($linha['ferramenta'],'F'))
        $consulta="select nome from Forum where cod_forum=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'B'))
        $consulta="select assunto from Batepapo_assuntos where cod_assunto=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'E'))
        $consulta="select titulo from Exercicios_modelo where cod_exercicio=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'N'))
        $consulta="select titulo from Avaliacao_externa where codigo=".$linha['cod_atividade'];
      else
      {
        $consulta="select titulo from Atividade_itens where cod_item=".$linha['cod_atividade'];
        if (!$usr_formador)
          $consulta.=" and tipo_compartilhamento='T'";
      }
      $res=Enviar($sock, $consulta);
      $titulo=RetornaLinha($res);
      if (($titulo!="")&&(!is_null($titulo)))
      {
        $retorno[$cont]['Cod_avaliacao'] = $linha['cod_avaliacao'];
        $retorno[$cont]['Ferramenta']    = $linha['ferramenta'];
        $retorno[$cont]['Data']          = $linha['data'];
        $retorno[$cont]['Data_inicio']   = $linha['data_inicio'];
        $retorno[$cont]['Data_termino']  = $linha['data_termino'];
        $retorno[$cont]['Titulo']        = $titulo[0];
        $retorno[$cont]['tipo']          = $linha['tipo'];
        $cont++;
      }
    }
  }
  return $retorno;
}

/* *********************************************************************
   RetornaAvaliacoesAnteriores - Retorna array com as avaliacoes anteriores
   Entrada: $sock - BASE DO CURSO
   Saida: array com []['cod_Avaliacao']
                    []['Ferramenta']
                    []['Data_inicio']
                    []['Data_termino']
                    []['Titulo']
*/
function RetornaAvaliacoesAnteriores($sock, $usr_formador)
{
  $consulta="select cod_avaliacao,cod_atividade,ferramenta,tipo,data,data_inicio,data_termino from Avaliacao where data_inicio < ".time()." and data_termino < ".time()." and status!='A' and status!='C' and status!='X' order by data_inicio desc";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);

  $retorno = array();
  $cont = 0;

  if (RetornaNumLinhas($res) > 0)
  {
    foreach ($lista as $cod => $linha)
    {
      if (!strcmp($linha['ferramenta'],'F'))
        $consulta="select nome from Forum where cod_forum=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'B'))
        $consulta="select assunto from Batepapo_assuntos where cod_assunto=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'E'))
        $consulta="select titulo from Exercicios_modelo where cod_exercicio=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'N'))
        $consulta="select titulo from Avaliacao_externa where codigo=".$linha['cod_atividade'];
      else
      {
        $consulta="select titulo from Atividade_itens where cod_item=".$linha['cod_atividade'];
        if (!$usr_formador)
          $consulta.=" and tipo_compartilhamento='T'";
      }
      $res=Enviar($sock, $consulta);
      $titulo=RetornaLinha($res);
      if (($titulo!="")&&(!is_null($titulo)))
      {
        $retorno[$cont]['Cod_avaliacao'] = $linha['cod_avaliacao'];
        $retorno[$cont]['Ferramenta']    = $linha['ferramenta'];
        $retorno[$cont]['Data']          = $linha['data'];
        $retorno[$cont]['Data_inicio']   = $linha['data_inicio'];
        $retorno[$cont]['Data_termino']  = $linha['data_termino'];
        $retorno[$cont]['Titulo']        = $titulo[0];
        $retorno[$cont]['tipo']          = $linha['tipo'];
        $cont++;
      }
    }
  }
  return $retorno;
}

/* *********************************************************************
   RetornaAvaliacoesApagadas - Retorna array com as avaliacoes apagadas
   Entrada: $sock - BASE DO CURSO
   Saida: array com []['cod_Avaliacao']
                    []['Ferramenta']
                    []['Data_inicio']
                    []['Data_termino']
                    []['Titulo']
                    []['Tipo']
*/
function RetornaAvaliacoesApagadas($sock, $usr_formador)
{
  $consulta="select cod_avaliacao,cod_atividade,ferramenta,data_inicio,data_termino,tipo from Avaliacao where status='A' order by data_inicio desc";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);

  $retorno = array();
  $cont = 0;

  if (RetornaNumLinhas($res) > 0)
  {
    foreach ($lista as $cod => $linha)
    {
      if (!strcmp($linha['ferramenta'],'F'))
        $consulta="select nome from Forum where cod_forum=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'B'))
        $consulta="select assunto from Batepapo_assuntos where cod_assunto=".$linha['cod_atividade'];
      elseif (!strcmp($linha['ferramenta'],'E'))
        $consulta="select titulo from Exercicios_modelo where cod_exercicio=".$linha['cod_atividade'];
      else
      {
        $consulta="select titulo from Atividade_itens where cod_item=".$linha['cod_atividade'];
        if (!$usr_formador)
          $consulta.=" and tipo_compartilhamento='T'";
      }
      $res=Enviar($sock, $consulta);
      $titulo=RetornaLinha($res);
      if (($titulo!="")&&(!is_null($titulo)))
      {
        $retorno[$cont]['Cod_avaliacao'] = $linha['cod_avaliacao'];
        $retorno[$cont]['Ferramenta']    = $linha['ferramenta'];
        $retorno[$cont]['Data_inicio']   = $linha['data_inicio'];
        $retorno[$cont]['Data_termino']  = $linha['data_termino'];
        $retorno[$cont]['Titulo']        = $titulo[0];
        $retorno[$cont]['Tipo']          = $linha['tipo'];
        $cont++;
      }
    }
  }
  return $retorno;
}


/* *********************************************************************
   IniciaAlteracaoCadastroAvaliacao - Inicia a altera�o do cadastro de uma avalia�o
   Entrada: $sock - BASE DO CURSO
            $cod_avaliacao - avaliacao em edi�o
   Saida: nenhuma
*/
function IniciaAlteracaoCadastroAvaliacao($sock, $cod_avaliacao, $cod_usuario)
{
  $consulta="update Avaliacao set status='E', cod_usuario=".$cod_usuario.", inicio_edicao=".time()." where cod_avaliacao=".$cod_avaliacao;
  $res=Enviar($sock,$consulta);
  $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'E')";
  $res=Enviar($sock,$consulta);
}

/* *********************************************************************
   AlteraCadastroAvaliacao - Altera o cadastro de avalia�o de uma atividade
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_formador - Cdigo do formador que cadastrou
            $objetivos - objetivos da avalia�o
            $criterios - criterios da avalia�o
            $tipo -tipo da atividade:individual ou em grupo
            $valor - Valor da avalia�o
            $data_inicio - Data de in�io da Atividade
            $data_termino - Data de termino da Atividade
            $cod_atividade - cdigo que identifica a atividade em que a avalia�o ir�atuar

   Saida: nenhuma
*/
function AlteraCadastroAvaliacao($sock, $tabela, $cod_formador, $objetivos, $criterios, $tipo, $valor, $data_inicio, $data_termino, $cod_avaliacao)
{
  $objetivos=EliminaScript($objetivos);
  $objetivos=LimpaConteudo($objetivos);
  $criterios=EliminaScript($criterios);
  $criterios=LimpaConteudo($criterios);

  $consulta="update ".$tabela." set cod_usuario=".$cod_formador.",objetivos='".$objetivos."',criterios='".$criterios."',tipo='".$tipo."',valor=".$valor.",status='F',data=".time().",data_inicio=".$data_inicio.",data_termino=".$data_termino." where cod_avaliacao=".$cod_avaliacao."";
  $res=Enviar($sock, $consulta);
  $consulta="insert into ".$tabela."_historicos values (".$cod_avaliacao.", ".$cod_formador.", ".time().", 'F')";
  $res=Enviar($sock,$consulta);
}

/* *********************************************************************
   AlteraCadastroAvaliacaoExercicio - Altera o cadastro de avalia�o de um exercicio
            e sua data de  disponibiliza�o e submiss�
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_formador - Cdigo do formador que cadastrou
            $objetivos - objetivos da avalia�o
            $criterios - criterios da avalia�o
            $tipo -tipo da atividade:individual ou em grupo
            $valor - Valor da avalia�o
            $data_inicio - Data de in�io da Atividade
            $data_termino - Data de termino da Atividade
            $cod_atividade - cdigo que identifica a atividade em que a avalia�o ir�atuar

   Saida: nenhuma
*/
function AlteraCadastroAvaliacaoExercicio($sock, $tabela, $cod_formador, $objetivos, $criterios, $tipo, $valor, $data_inicio, $data_termino, $cod_avaliacao)
{
  /* Atualizando a tabela Avaliacao */
  $objetivos=EliminaScript($objetivos);
  $objetivos=LimpaConteudo($objetivos);
  $criterios=EliminaScript($criterios);
  $criterios=LimpaConteudo($criterios);
  $consulta="update ".$tabela." set cod_usuario=".$cod_formador.",objetivos='".$objetivos."',criterios='".$criterios."',tipo='".$tipo."',valor=".$valor.",status='F',data=".time().",data_inicio=".$data_inicio.",data_termino=".$data_termino." where cod_avaliacao=".$cod_avaliacao."";
  $res=Enviar($sock, $consulta);
  $consulta="insert into ".$tabela."_historicos values (".$cod_avaliacao.", ".$cod_formador.", ".time().", 'F')";
  $res=Enviar($sock,$consulta);

  /* Atualizando as datas na tabela Exercicios_aplicado */
  $query = "select dt_disponibilizacao, dt_limite_submissao from Exercicios_aplicado where avaliacao=".$cod_avaliacao."";
  $res = Enviar($sock, $query);
  $datas = RetornaLinha($res);

  $hr_inicio = UnixTime2Hora($datas['dt_disponibilizacao']);
  $hora_i=explode(":",$hr_inicio);
  /* Calculo em segundos do horario de inicio do exercicio 
                              Horas            Minutos          Segundos */
  $horainicio_timestamp = 3600 * $hora_i[0] + 60 * $hora_i[1] + $hora_i[2];

  $hr_fim = UnixTime2Hora($datas['dt_limite_submissao']);
  $hora_f=explode(":",$hr_fim);
  /* Calculo em segundos do horario de termino do exercicio 
                          Horas            Minutos          Segundos */
  $horafim_timestamp = 3600 * $hora_f[0] + 60 * $hora_f[1] + $hora_f[2];

  /* Calculo das novas datas atualizadas, mantendo o horario de inicio e termino do exercicio */
  $datahora_inicio = $data_inicio + $horainicio_timestamp;
  $datahora_fim = $data_termino + $horafim_timestamp;

  $query = "update Exercicios_aplicado set dt_disponibilizacao=".$datahora_inicio.", dt_limite_submissao=".$datahora_fim." where avaliacao=".$cod_avaliacao."";
  Enviar($sock,$query);
}



/* *********************************************************************
   AlteraCadastroAvaliacao2 - Altera os dados de uma avalia�o cadastrada no batepapo
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_formador - Cdigo do formador que cadastrou
            $comentario - comentario da avalia�o
            $valor - Valor da avalia�o
            $cod_avaliacao - cdigo da avalia�o

   Saida: nenhuma
*/
function AlteraCadastroAvaliacao2($sock, $tabela, $cod_formador, $objetivos, $criterios, $valor, $cod_avaliacao)
{
  $objetivos=EliminaScript($objetivos);
  $objetivos=LimpaConteudo($objetivos);
  $criterios=EliminaScript($criterios);
  $criterios=LimpaConteudo($criterios);
  $consulta="update ".$tabela." set cod_usuario=".$cod_formador.",objetivos='".$objetivos."',criterios='".$criterios."',valor=".$valor.",status='F',data=".time()." where cod_avaliacao=".$cod_avaliacao."";
  $res=Enviar($sock, $consulta);
  $consulta="insert into ".$tabela."_historicos values (".$cod_avaliacao.", ".$cod_formador.", ".time().", 'F')";
  $res=Enviar($sock,$consulta);
}

/* ********************************************************************************
  RetornaAvaliacaoCadastrada - Retorna os Dados da avalia�o cadastrada
   Entrada:  $sock - BASE DO CURSO
             $cod_avaliacao - cdigo da avaliacao

   Saida: array['Cod_atividade'] = Cdigo da Atividade que a avalia�o se refere;
          array['Objetivos']     = Objetivos da Avaliacao;
          array['Crit�ios']     = Crit�ios da Avaliacao;
          array['Valor']         = Valor da Avalia�o;
          array['Status']        = Status da avalia�o;
          array['Data_inicio']   = Data de inicio da Avalia�o;
          array['Data_termino']  = Data de termino da Avalia�o;
*/
function RetornaAvaliacaoCadastrada($sock, $cod_avaliacao)
{
  $query="select cod_atividade,cod_usuario,objetivos,criterios,ferramenta,tipo,valor,status,data,data_inicio,data_termino from Avaliacao where cod_avaliacao=".$cod_avaliacao."";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $retorno['Cod_atividade']=$linha['cod_atividade'];
  $retorno['Cod_usuario']=$linha['cod_usuario'];
  $retorno['Objetivos']=$linha['objetivos'];
  $retorno['Criterios']=$linha['criterios'];
  $retorno['Ferramenta']=$linha['ferramenta'];
  $retorno['Tipo']=$linha['tipo'];
  $retorno['Valor']=$linha['valor'];
  $retorno['Status']=$linha['status'];
  $retorno['Data']=$linha['data'];
  $retorno['Data_inicio']=$linha['data_inicio'];
  $retorno['Data_termino']=$linha['data_termino'];
  return $retorno;
}

/* ******************************************************
   RetornaForum - retorna o nome do frum.

   Entradas: $sock - sock de conexao,
             $cod_forum - cdigo do frum.

   Saida:    nome do frum,
*/
function RetornaForum($sock, $cod_forum)
{
  $query = "select nome from Forum where cod_forum = ".$cod_forum;

  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);

  return $tupla[0];
}

/* ************************************************************************
   RetornaAssunto - Retorna o assunto da sessao
   Entrada: $sock - BASE DO CURSO
            $cod_assunto
   Saida: ['Assunto'] - Assunto da Sess�
*/
function RetornaAssunto($sock, $cod_assunto)
{
  $query="select assunto from Batepapo_assuntos where cod_assunto=".$cod_assunto;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}

/* ************************************************************************
   RetornaAtividade - Retorna a atividade
   Entrada: $sock - BASE DO CURSO
            $cod_assunto
   Saida: ['Assunto'] - Assunto da Sess�
*/
function RetornaAtividade($sock, $cod_atividade)
{
  $query="select titulo from Atividade_itens where cod_item=".$cod_atividade;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}


/* *********************************************************************
   RetornaUsuarios - Retorna lista de usuarios
   Entrada: $sock - sock de conexao
            $cod_curso - codigo do curso
   Saida:   array [$cod_usuario]= nome do usuario
*/
function RetornaUsuarios($sock, $cod_curso)
{

  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "select UC.cod_usuario,U.nome from ".$dbnamebase.".Usuario_curso UC ".
           " inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global and UC.cod_curso='".$cod_curso."') ".
           " where UC.cod_usuario >= 0 ".
           " and (binary UC.tipo_usuario='A' or ".
           "      binary UC.tipo_usuario='F') ".
           " order by U.nome";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  unset($lista1);
  if (count($lista) > 0)
  {
    foreach($lista as $cod=>$linha)
    {
      $lista1[$linha['cod_usuario']]=$linha['nome'];
    }
  }
  return ($lista1);
}

function RetornaNomeUsuario($sock, $cod_curso, $cod_usuario)
{

  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "select UC.cod_usuario,U.nome from ".$dbnamebase.".Usuario_curso UC ".
           " inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global and UC.cod_curso='".$cod_curso."') ".
           " where UC.cod_usuario='".$cod_usuario."'".
           " and (binary UC.tipo_usuario='A' or ".
           "      binary UC.tipo_usuario='F') ".
           " order by U.nome";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  
  return $lista;
  
}

/* ********************************************************************************
   RetornaDadosNota - Retorna a nota do aluno selecionado
   Entrada:  $sock - BASE DO CURSO
             $cod - Cdigo do aluno
             $cod_avaliacao - Cdigo da avalia�o
   Saida:   cod_nota,nota,tipo_compartilhamento
            NULL se nao houver nota para aquele aluno na avaliacao
*/
function RetornaDadosNota($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $usr_formador)
{
  // array com as condicoes da query
  $where = array();
  if ($usr_formador)
  {
    // formador. Enxerga todas as notas.

    // Nao ha restricoes de compartilhamento
  }
  else if ($cod_usuario == $cod_aluno)
  {
    // Aluno dono da nota. Enxerga notas compartilhadas com todos e suas proprias notas
    $where[] = "(tipo_compartilhamento='T' or tipo_compartilhamento='A' or tipo_compartilhamento='G')";
  }
  else
  {
    $where[] = "tipo_compartilhamento = 'T'";
  }

  // o aluno que foi avaliado
  $where[] = "cod_aluno=".$cod_aluno;
  // qual avaliacao
  $where[] = "cod_avaliacao=".$cod_avaliacao;
  // a avaliacao foi finalizada
  $where[] = "(status='F' or status='A')";

  // retornar todas as notas visiveis ao usuario
  $query = "select cod_nota, data, data_alteracao, nota, tipo_compartilhamento from Avaliacao_notas".
           " where ".implode(" and ", $where).
           " order by data desc";
  $res   = Enviar($sock, $query);

  return RetornaLinha($res);

}

/* ********************************************************************************
   RetornaDadosNotaGrupoStatusF - Retorna a nota do aluno ,caso exista, pertencente ao grupo designado, cujo status da nota 
                           seja 'F' ou 'A'
   Entrada:  $sock - BASE DO CURSO
             $cod_grupo - Cdigo do grupo
             $cod_avaliacao - Cdigo da avalia�o
             $usr_formador - flag que indica se o usario eh formador
   Saida:    cod_nota,nota,data,tipo_compartilhamento
*/
function RetornaDadosNotaGrupoStatusF($sock, $cod_grupo, $cod_avaliacao, $usr_formador)
{
  $cod_aluno=RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo);

  $query="select cod_nota,data,nota,tipo_compartilhamento from Avaliacao_notas where cod_grupo=".$cod_grupo." and cod_avaliacao=".$cod_avaliacao." and (status='F' || status='A') and cod_aluno=".$cod_aluno." order by cod_nota desc";
  $res=Enviar($sock,$query);

  return RetornaLinha($res);
}

/* ********************************************************************************
   RetornaDadosNotaGrupo - Retorna a nota do aluno selecionado
   Entrada:  $sock - BASE DO CURSO
             $cod - Cdigo do grupo
             $cod_avaliacao - Cdigo da avalia�o
   Saida:   cod_nota,nota,tipo_compartilhamento
*/
function RetornaDadosNotaGrupo($sock, $cod, $cod_avaliacao, $cod_usuario, $usr_formador, $cod_aluno)
{
  $sair=0;
  $query="select cod_nota,data,nota,tipo_compartilhamento from Avaliacao_notas where cod_grupo=".$cod." and cod_aluno=".$cod_aluno." and cod_avaliacao=".$cod_avaliacao." and (status='F' || status='A') order by cod_nota desc";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha=RetornaLinha($res);
    $compartilhamento=$linha['tipo_compartilhamento'];
    if (!strcmp($compartilhamento,'T'))
      $sair=1;
    elseif ((!strcmp($compartilhamento,'A')) && ($cod_usuario==$cod))
      $sair=1;
    else //A nota est�compartilhada s com formadores  ou (compartilhado so com dono da nota e usuario n� �dono da nota )
    {
      if ($usr_formador)
        $sair=1;
      //else
      //  continue;
    }
  }
  if ($sair)
  {
    return $linha;
    break;
  }

  $query="select cod_nota,data,nota,tipo_compartilhamento from Avaliacao_notas where cod_grupo=".$cod." and cod_aluno=".$cod_aluno." and cod_avaliacao=".$cod_avaliacao." and status='A' order by cod_nota desc";
  $res=Enviar($sock,$query);
  $num=RetornaNumLinhas($res);
  $i=0;
  while ($i < $num)
  {
    $i++;
    $linha=RetornaLinha($res);
    $compartilhamento=$linha['tipo_compartilhamento'];
    if (!strcmp($compartilhamento,'T'))
      $sair=1;
    elseif ((!strcmp($compartilhamento,'A')) && ($cod_usuario==$cod))
      $sair=1;
    else //A nota est�compartilhada s com formadores  ou (compartilhado so com dono da nota e usuario n� �dono da nota )
    {
      if ($usr_formador)
        $sair=1;
//    else
//      continue;
    }
    if ($sair)
    {
      return $linha;
      break;
    }
  }
  return $linha;
}

/* ********************************************************************************
   RetornaCompartilhamento - Retorna o tipo de compartilhamento da nota do aluno selecionado
   Entrada:  $sock - BASE DO CURSO
             $cod - Cdigo do aluno
             $cod_avaliacao - Cdigo da avalia�o
   Saida:   compartilhamento da nota
*/
function RetornaCompartilhamento($sock, $cod, $cod_avaliacao)
{
  $query="select tipo_compartilhamento from Avaliacao_notas where cod_aluno=".$cod." and cod_avaliacao=".$cod_avaliacao." and status='F'";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha=RetornaLinha($res);
  }
  elseif (RetornaNumLinhas($res)==0)
  {
    $query="select tipo_compartilhamento from Avaliacao_notas where cod_aluno=".$cod." and cod_avaliacao=".$cod_avaliacao." and status='A'";
    $res=Enviar($sock,$query);
    $num=RetornaNumLinhas($res);
    $i=0;
    while ($i < $num)
    {
      $i++;
      $linha=RetornaLinha($res);
    }
  }
  return $linha[0];
}


/* ********************************************************************************
   ParticipouDaSessao - Verifica se o aluno participou da sess� de bate-papo referente a avalia�o em quest�
   Entrada:  $sock - BASE DO CURSO
             $cod_aluno - Cdigo do aluno
             $cod_assunto - Assunto da sess� de bate-papo
   Saida: true se o aluno participou da atividade
*/
function ParticipouDaSessao($sock, $cod_aluno, $cod_assunto)
{
  $lista_sessoes = RetornaSessoesDeUmAssunto($sock,$cod_assunto);
  if (is_array($lista_sessoes))
    foreach($lista_sessoes as $cod => $linha)
    {
      $query="select * from Batepapo_apelido where cod_sessao=".$linha['cod_sessao']." and cod_usuario=".$cod_aluno."";
      $res=Enviar($sock,$query);
      if (RetornanumLinhas($res) > 0)
      {
        return true;
      }
    }
  return false;
}


/* ********************************************************************************
   RetornaSessoesDeUmAssunto - Retorna todas as sesses de bate-papo que possuem um mesmo assunto
   Entrada:  $sock - BASE DO CURSO
             $cod_assunto - Cdigo do Assunto da sessao de bate-papo
   Saida: array [$cod_usuario]['cod_sessao'] - cdigo da sess�
*/
function RetornaSessoesDeUmAssunto($sock, $cod_assunto)
{
  $query="select data_inicio,data_fim from Batepapo_assuntos where cod_assunto='".$cod_assunto."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $di=$linha[0];
  $df=$linha[1];

  $query ="select cod_sessao from Batepapo_sessoes where ";
  $query.="(data_inicio<=".$di." and data_fim>=".$di.") or ";
  $query.="(data_inicio<=".$df." and data_fim>=".$df.") or ";
  $query.="(data_inicio>=".$di." and data_inicio<=".$df.") or ";
  $query.="(data_fim>=".$di." and data_fim<=".$df.")";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  unset($retorno);
  $cont=0;
  if (count($lista)>0)
    foreach ($lista as $cod => $linha)
    {
      $retorno[$cont]['cod_sessao']=$linha['cod_sessao'];
      $cont ++;
    }
  return $retorno;
}


/* ********************************************************************************
   BatePapoExiste - Verifica se a sess� de bate-papo existe
   Entrada:  $sock - BASE DO CURSO
             $cod_assunto - Cdigo do Assunto da sessao de bate-papo
   Saida:   true se a sess� de bate-papo existe
*/
function BatePapoExiste($sock, $cod_assunto)
{
  $query="select data_inicio,data_fim from Batepapo_assuntos where cod_assunto='".$cod_assunto."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $di=$linha[0];
  $df=$linha[1];

  $query ="select count(*) from Batepapo_sessoes where ";
  //$query.="(data_inicio>=".$di." and data_fim<=".$df.")";
  $query.="(data_inicio<=".$di." and data_fim>=".$di.") or ";
  $query.="(data_inicio<=".$df." and data_fim>=".$df.") or ";
  $query.="(data_inicio>=".$di." and data_inicio<=".$df.") or ";
  $query.="(data_fim>=".$di." and data_fim<=".$df.")";


  $res=Enviar($sock,$query);
  $lista=RetornaLinha($res);
  if ($lista[0] > 0)
    return true;
  else
    return false;
}


/* ********************************************************************************
   ParticipouDoForum - Verifica se o aluno participou do frum referente a avalia�o em quest�
   Entrada:  $sock - BASE DO CURSO
             $cod_aluno - Cdigo do aluno que postou a mensagem
             $cod_forum - Cdigo do forum
   Saida:  true se o aluno participou da atividade
*/
function ParticipouDoForum($sock, $cod_aluno, $cod_forum)
{
  $query="select count(*) from Forum_mensagens where cod_forum=".$cod_forum." and cod_usuario=".$cod_aluno." and status='A'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha[0] > 0)
    return true;
  else
    return false;
}


/* ********************************************************************************
   RetornaTituloAvaliacao - Retorna o Titulo da Avaliacao (nome do forum, nome da
                           sessao de bate-papo ou nome da atividade)
   Entrada:  $sock - BASE DO CURSO
             $ferramenta - Ferramenta em que a avalia�o ir�atuar
             $cod_atividade - Cdigo da atividade a que a avaliacao se refere
   Saida:  titulo da atividade
*/
function RetornaTituloAvaliacao($sock, $ferramenta, $cod_atividade)
{
  if (!strcmp($ferramenta,'B'))
  {
    $query="select assunto from Batepapo_assuntos where cod_assunto=".$cod_atividade;
  }
  elseif (!strcmp($ferramenta,'F'))
  {
    $query="select nome from Forum where cod_forum=".$cod_atividade;
  }
  elseif (!strcmp($ferramenta,'E'))
  {
    $query="select titulo from Exercicios_modelo where cod_exercicio=".$cod_atividade;
  }
  elseif (!strcmp($ferramenta,'N'))
  {
    $query="select titulo from Avaliacao_externa where codigo=".$cod_atividade;
  }
  else
  {
    $query="select titulo from Atividade_itens where cod_item=".$cod_atividade;
  }
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}

/* ********************************************************************************
   RealizouAtividadenoPortfolio - Verifica se o aluno realizou a atividade a ser avaliada no Portfolio
   Entrada:   Entrada:  $sock - BASE DO CURSO
             $cod - Cdigo do aluno ou grupo
             $cod_avaliacao - codigo da avaliacao
             $portfolio_grupo - diz se �portfolio de grupo ou nao
   Saida:  true se realizou a atividade
*/
function RealizouAtividadeNoPortfolio($sock, $cod_avaliacao, $cod, $portfolio_grupo)
{
  if ($portfolio_grupo)
    $query="select * from Portfolio_itens where cod_grupo=".$cod." and cod_topico!=2";
  else
    $query="select * from Portfolio_itens where cod_usuario=".$cod." and cod_topico!=2 and cod_grupo IS NULL";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  if (count($lista)>0)
  {
    foreach ($lista as $cod => $linha)
    {
      $query="select * from Portfolio_itens_avaliacao where cod_item=".$linha['cod_item']." and cod_avaliacao=".$cod_avaliacao;
      $res=Enviar($sock,$query);
      if (RetornaNumLinhas($res) > 0)
        return true;
    }
    return false;
  }
  else
    return false;
}

/* ********************************************************************************
   RespondeuExercicio - Verifica se o aluno respondeu Exercicio proposto
   Entrada:  Entrada:  $sock - BASE DO CURSO
             $cod - Cdigo do aluno ou grupo
             $cod_avaliacao - codigo da avaliacao
             $exercicio_grupo - diz se �exerc�io de grupo ou nao
   Saida:  zero se realizou a atividade ou o cdigo do modelo, caso contr�io
*/
function RespondeuExercicio($sock, $cod_avaliacao, $cod, $exercicio_grupo)
{
  $query="select cod_exercicio from Exercicios_aplicado where avaliacao=".$cod_avaliacao;
  $res=Enviar($sock,$query);
  $exercicio=RetornaLinha($res);
  if ($exercicio_grupo)
    $query="select cod_resolucao from Exercicios_resolucao where cod_grupo=".$cod." and submetida='S' and cod_exercicio=".$exercicio['cod_exercicio'];
  else
    $query="select cod_resolucao from Exercicios_resolucao where cod_usuario=".$cod." and submetida='S' and cod_exercicio=".$exercicio['cod_exercicio'];

  $res=Enviar($sock,$query);
  $lista=RetornaLinha($res);
  if (count($lista['cod_resolucao'])>0) {
    $exercicio['cod_resolucao'] = $lista['cod_resolucao'];
    return($exercicio);
  }
  else
    return 0;

}



/* ********************************************************************************
   RetornaNumItensAvaliacaoPortfolioGrupo -  Retorna o numero de itens postado pelo grupo referentes a avaliacao
   Entrada:  $sock - BASE DO CURSO
             $cod - Cdigo do grupo
             $cod_avaliacao - codigo da avaliacao
             $portfolio_grupo - diz se �portfolio de grupo ou nao
   Saida:      numero de itens postados
*/
/*function RetornaNumItensAvaliacaoPortfolioGrupo ($sock, $cod_grupo, $cod_avaliacao)
{
  $query="select * from Portfolio_itens where cod_grupo=".$cod_grupo." and cod_topico!=2";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  $cont=0;
  if (count($lista)>0)
  {
    foreach ($lista as $cod => $linha)
    {
      $query="select distinct(count(*)) from Portfolio_itens_avaliacao where cod_item=".$linha['cod_item']." and cod_avaliacao=".$cod_avaliacao;
      $res=Enviar($sock,$query);
      unset($linha);
      $linha=RetornaLinha($res);
      $cont=$cont+$linha[0];
    }
  }
  return $cont;
}
*/
function PertenceAoGrupoAux($sock, $cod_usuario, $cod_grupo_portfolio)
{
  if ($cod_grupo_portfolio=="" || $cod_grupo_portfolio=="NULL")
    return false;
  $query="select GU.cod_usuario 'cod_usuario' from Grupos_usuario GU, Grupos G where GU.cod_grupo=".$cod_grupo_portfolio." and G.cod_grupo=".$cod_grupo_portfolio." and GU.cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
    return true;
  else
    return false;
}


/* ********************************************************************************
   RetornaNumItensPortfolioAvaliacao -  Retorna o numero de itens postado pelo aluno ou grupo referentes a avaliacao
   Entrada:  $sock - BASE DO CURSO
             $cod - Cdigo do aluno ou grupo
             $cod_avaliacao - codigo da avaliacao
             $portfolio_grupo - diz se �portfolio de grupo ou nao
   Saida:      numero de itens postados
*/
function RetornaNumItensPortfolioAvaliacao($sock, $cod, $cod_avaliacao, $portfolio_grupo, $cod_usuario, $eformador, $RetornaCodAlunodoGrupo)
{
  if ($portfolio_grupo)
  {
    if (PertenceAoGrupoAux($sock,$cod_usuario, $cod))
      $regra="";
    else if ($eformador)
      $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
    else
      $regra="and tipo_compartilhamento='T'";
  }
  else
  {
    if ($cod_usuario==$cod_usuario_portfolio)
      $regra="";
    else
    {
      if ($eformador)
        $regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
      else
        $regra="and tipo_compartilhamento='T'";
    }
  }

  if ($portfolio_grupo)
    $query="select * from Portfolio_itens where cod_grupo=".$cod." and cod_topico!=2 ".$regra;
  else
    $query="select * from Portfolio_itens where cod_usuario=".$cod." and cod_grupo IS NULL and cod_topico!=2 ".$regra;
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  $cont=0;
  if (count($lista)>0)
  {
    foreach ($lista as $cod => $linha)
    {
      $query="select distinct(count(*)) from Portfolio_itens_avaliacao where cod_item=".$linha['cod_item']." and cod_avaliacao=".$cod_avaliacao;
      $res=Enviar($sock,$query);
      unset($linha);
      $linha=RetornaLinha($res);
      if ($linha[0]>0)
      $cont=$cont+1;
    }
  }
  return $cont;
}


/* ********************************************************************************
   RetornaNumItensPortfolioGrupoAvaliacao -  Retorna o numero de itens postado pelo aluno ou grupo referentes a avaliacao
   Entrada:  $sock - BASE DO CURSO
             $cod - Cdigo do aluno ou grupo
             $cod_avaliacao - codigo da avaliacao
             $portfolio_grupo - diz se �portfolio de grupo ou nao
   Saida:      numero de itens postados
*/
/*function RetornaNumItensPortfolioGrupoAvaliacao($sock, $cod, $cod_avaliacao)
{
  $query="select * from Portfolio_itens where cod_grupo=".$cod." and cod_topico!=2";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  $cont=0;
  if (count($lista)>0)
  {
    foreach ($lista as $cod => $linha)
    {
      $query="select count(*) from Portfolio_itens_avaliacao where cod_item=".$linha['cod_item']." and cod_avaliacao=".$cod_avaliacao;
      $res=Enviar($sock,$query);
      unset($linha);
      $linha=RetornaLinha($res);
      $cont=$cont+$linha[0];
    }
  }
  return $cont;
}

/*function AlteraCompartilhamento($sock, $compartilhamento, $cod_aluno, $cod_avaliacao)
{
  $consulta="update Avaliacao_notas set tipo_compartilhamento='".$compartilhamento."' where cod_avaliacao=".$cod_avaliacao." and cod_aluno=".$cod_aluno." and status='F'";
  $res=Enviar($sock, $consulta);
  return($res);
}

/* ********************************************************************************
   RetornaNumMsgsParticipantesForum - Retorna o nmero de mensagens postadas em um frum por um aluno
   Entrada:  $sock - BASE DO CURSO
             $cod_forum - forum que ser�feita a avalia�o
             $cod_aluno - Cdigo do aluno que postou a mensagem
   Saida: Nmero de mensagens
*/
function RetornaNumMsgsParticipantesForum($sock, $cod_forum, $cod_aluno)
{
  $query="select count(*) from Forum_mensagens where cod_forum=".$cod_forum." and cod_usuario=".$cod_aluno." and status='A'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  return $linha[0];
}

/* *********************************************************************
   MudarCompartilhamento - Muda o compartilhamento da nota
   Entrada: $sock - BASE DO CURSO
            $cod_nota - nota a ser alterado compartilhamento
            $tipo_comp - Tipo do Compartilhamento (F ,T ou A)
   Saida: nenhuma
*/
function MudarCompartilhamento($sock, $cod_nota, $tipo_comp)
{
  $query="select tipo_compartilhamento from Avaliacao_notas where cod_nota=".$cod_nota;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha['tipo_compartilhamento']!=$tipo_comp)
  {
    $consulta="update Avaliacao_notas set tipo_compartilhamento='".$tipo_comp."',data_alteracao=".time()." where cod_nota=".$cod_nota;
    $res=Enviar($sock, $consulta);
  }
}


/* ********************************************************************************
   RetornaNota - Retorna a nota do aluno selecionado
   Entrada:  $sock - BASE DO CURSO
             $cod - Cdigo do aluno
             $cod_avaliacao - Cdigo da avalia�o
   Saida:  nota do aluno
*/
function RetornaNota($sock, $cod, $cod_avaliacao)
{
  $query="select nota from Avaliacao_notas where cod_aluno=".$cod." and cod_avaliacao=".$cod_avaliacao." and status='F'";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha=RetornaLinha($res);
  }
  elseif (RetornaNumLinhas($res)==0)
  {
    $query="select nota from Avaliacao_notas where cod_aluno=".$cod." and cod_avaliacao=".$cod_avaliacao." and status='A'";
    $res=Enviar($sock,$query);
    $num=RetornaNumLinhas($res);
    $i=0;
    while ($i < $num)
    {
      $i++;
      $linha=RetornaLinha($res);
    }
  }
  return $linha[0];
}

/* ********************************************************************************
   RetornaCodNota - Retorna a nota do aluno selecionado
   Entrada:  $sock - BASE DO CURSO
             $cod - Cdigo do aluno
             $cod_avaliacao - Cdigo da avalia�o
   Saida:   codigo da nota do aluno
*/
function RetornaCodNota($sock, $cod, $cod_avaliacao)
{
  $query="select cod_nota from Avaliacao_notas where cod_aluno=".$cod." and cod_avaliacao=".$cod_avaliacao." and status='F'order by cod_nota desc";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha=RetornaLinha($res);
  }

  elseif (RetornaNumLinhas($res)==0)
  {
    $query="select cod_nota from Avaliacao_notas where cod_aluno=".$cod." and cod_avaliacao=".$cod_avaliacao." and status='A'";
    $res=Enviar($sock,$query);
    $num=RetornaNumLinhas($res);
    $i=0;
    while ($i < $num)
    {
      $i++;
      $linha=RetornaLinha($res);
    }
  }
  return $linha[0];
}

/* ********************************************************************************
   RetornaCodNotaGrupo - Retorna a nota do grupo selecionado
   Entrada:  $sock - BASE DO CURSO
             $cod_grupo - Cdigo do aluno
             $cod_avaliacao - Cdigo da avalia�o
   Saida:   codigo da nota do aluno
*/
function RetornaCodNotaGrupo($sock, $cod, $cod_avaliacao)
{
  $query="select cod_nota from Avaliacao_notas where cod_grupo=".$cod." and cod_avaliacao=".$cod_avaliacao." and status='F' order by cod_nota";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha=RetornaLinha($res);
  }
  elseif (RetornaNumLinhas($res)==0)
  {
    $query="select cod_nota from Avaliacao_notas where cod_grupo=".$cod." and cod_avaliacao=".$cod_avaliacao." and status='A' order by cod_nota";
    $res=Enviar($sock,$query);
    $num=RetornaNumLinhas($res);
    $i=0;
    while ($i < $num)
    {
      $i++;
      $linha=RetornaLinha($res);
    }
  }
  return $linha[0];
}



/* ********************************************************************************
   RetornaCodSessao - Retorna o cdigo da sess� de bate-papo
   Entrada:  $sock - BASE DO CURSO
             $cod_assunto - Cdigo do assunto da sess�
   Saida:   codigo da sess� de bate-papo
*/
function RetornaCodSessao($sock,$cod_assunto)
{
  $lista_sessoes  = RetornaSessoesDeUmAssunto($sock,$cod_assunto);
  unset($retorno);
  $cont=0;
  foreach($lista_sessoes as $cod => $linha)
  {
    $retorno[$cont]['Cod_sessao']=$linha['cod_sessao'];
    $cont++;
  }
  return ($retorno);
}



/* *********************************************************************
   RetornaQtdeMsgsUsuario - Retorna a lista de qts mensagens cada usu�io enviou numa sess�
   Entrada: $sock - BASE INTERNA
            $cod_sessao - codigo da sessao
            $lista_usuarios - Lista de usu�io do curso
   Saida: array [$cod_usuario_emissor]=qtde;
*/
function RetornaQtdeMsgsUsuario($sock,$cod_sessao,$lista_usuarios)
{
  $query="select cod_usuario,count(*) 'qtde' from Batepapo_conversa where cod_sessao=".$cod_sessao." and cod_fala>1 group by cod_usuario order by cod_usuario";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  unset ($saida);

  if (count($lista)>0)
  {
    foreach($lista as $cod => $linha)
    {
      if (isset($lista_usuarios[$linha['cod_usuario']]))
        $saida[$linha['cod_usuario']]=$linha['qtde'];
    }
  }
  return $saida;
}


/* *********************************************************************
   FoiAvaliado - Verifica se o aluno j�foi avaliado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_aluno - aluno que est�sendo avaliado
            $cod_avaliacao - avaliacao que est�sendo corrigida
   Saida: true se foi avaliado
*/
function FoiAvaliado($sock,$cod_avaliacao,$cod_aluno)
{
  $query="select * from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and cod_aluno=".$cod_aluno." and ((status='A') || (status='F'))";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
    return true;
  else
    return false;
}


/*******************************************************************************************
   ListaUsuario - Retorna uma lista com o cdigo, nome e tipo de usu�io (ou formador)
                 do curso, dado o tipo
   Entradas: $sock - sock de conexao,
            $tipo_usuario - Tipo do Usuario("A","F"),
   Saida:   Array com: ['cod_usuario'] - codigo usuario
                      ['nome'] - nome
                      ['tipo_usuario'] - tipo de usuario
*/
function ListaUsuario($sock,$tipo_usuario)
{
  $query = "select cod_usuario, nome, tipo_usuario from Usuario where binary tipo_usuario=\"".$tipo_usuario."\" order by nome";
  $res=Enviar($sock,$query);

  $lista=RetornaArrayLinhas($res);
  return ($lista);
}

function ListaTodosUsuarios($sock)
{
  $query = "select cod_usuario, nome, tipo_usuario from Usuario where cod_usuario>0";
  $res=Enviar($sock,$query);

  $lista=RetornaArrayLinhas($res);
  return ($lista);
}


/* *********************************************************************
   RetornaUltimaPosicaoHistoricoAvaliacao - Retorna a ultima ocorr�cia do historico de uma dada avaliacao
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avalia�o)
            $cod_avaliacao - codigo da avaliacao
   Saida: array ['cod_avaliacao']
                ['cod_usuario']
                ['data']
                ['acao']
*/
function RetornaUltimaPosicaoHistoricoAvaliacao ($sock, $tabela, $cod_avaliacao)
{
  $consulta="select * from ".$tabela." where cod_avaliacao=".$cod_avaliacao." order by data desc limit 1";
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}

/* *********************************************************************
   CancelaEdicaoAvaliacao - Cancela a edicao de uma avaliacao
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacao)
            $cod_avaliacao - codigo da avaliacao

   Saida: true se a edi�o foi cancelada corretamente
*/
function CancelaEdicaoAvaliacao ($sock, $tabela, $cod_avaliacao, $cod_usuario)
{
  $linha=RetornaStatusAvaliacao($sock, $tabela, $cod_avaliacao);

  if ($linha['status']=="C")
  {
    $consulta="delete from ".$tabela." where cod_avaliacao=".$cod_avaliacao;
    $res=Enviar($sock, $consulta);

    $consulta="delete from ".$tabela."_historicos where cod_avaliacao=".$cod_avaliacao;
    $res=Enviar($sock, $consulta);
    return true;
  }
  elseif ($linha['status']=="E")
  {
    $consulta="update ".$tabela." set status='F' where cod_avaliacao=".$cod_avaliacao;
    $res=Enviar($sock, $consulta);
    $consulta="insert into ".$tabela."_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'D')";
    $res=Enviar($sock, $consulta);
    return true;
  }
}

/* *********************************************************************
   RetornaStatusAvaliacao - Retorna  o Status da Avaliacao (C,E,F,A ou X)
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacao)
            $cod_avaliacao - codigo da avaliacao

   Saida: status da avaliacao
*/
function RetornaStatusAvaliacao($sock, $tabela, $cod_avaliacao)
{
  $query="select status, cod_usuario from ".$tabela." where cod_avaliacao=".$cod_avaliacao;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return($linha);
}

/* *********************************************************************
   RetornaCodGrupoPortfolio - Retorna o cdigo do grupo que o aluno pertence
   Entrada: $sock - BASE DO CURSO
            $cod_aluno - cdigo do aluno

   Saida:   codigo do grupo
*/
function RetornaCodGrupoPortfolio($sock,$cod_aluno)
{
  $query="select cod_grupo from Grupos_usuario where cod_usuario=".$cod_aluno;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}

/* *********************************************************************
   RetornaCodGrupoPortfolio - Retorna o codigo do grupo correspondente ao codigo da nota
   Entrada: $sock - BASE DO CURSO
            $cod_nota - codigo da nota

   Saida:   codigo do grupo
*/
function RetornaCodGrupoAvaliacao($sock,$cod_nota)
{
  $query="select cod_grupo from Avaliacao_notas where cod_nota=".$cod_nota;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}

/* *********************************************************************
   RetornaCodAluno - Retorna o cdigo do aluno
   Entrada: $sock - BASE DO CURSO
            $cod_nota - cdigo da nota do aluno

   Saida:   codigo do aluno
*/
function RetornaCodAluno($sock,$cod_nota)
{
  $query="select cod_aluno from Avaliacao_notas where cod_nota=".$cod_nota;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}


/* *******************************************************************
   RetornaListaGrupos - retorna lista de grupos
   entrada: $sock - sock de conexao
   saida:   array []['cod_grupo'] - codigo do grupo
                  []['nome_grupo'] - nome do grupo
                  []['num_usuarios'] - numero de usuarios no grupo
*/
function RetornaListaGrupos($sock)
{
  /* todos usuarios */
  $query="select cod_grupo,nome from Grupos where status <> 'X'";
  $res=Enviar ($sock,$query);
  $lista=RetornaArrayLinhas($res);

  unset($lista1);
  if (count($lista) > 0)
  {
    foreach($lista as $cod=>$linha)
    {
      $lista1[$linha['cod_grupo']]=$linha['nome'];
    }
  }
  return ($lista1);
}


/*function RetornaNomeGrupo($sock, $cod_grupo)
{
  
  $query="select cod_grupo,nome from Grupos where status <> 'X' and cod_grupo='".$cod_grupo."'";
  $res=Enviar ($sock,$query);
  $lista=RetornaArrayLinhas($res);

  unset($nome);
  if (count($lista) > 0)
  {
    foreach($lista as $cod=>$linha)
    {
      $nome[$linha['cod_grupo']]=$linha['nome'];
    }
  }
  return ($nome);
}*/


/* *********************************************************************
   GrupoFoiAvaliado - Verifica se o grupo j�foi avaliado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_grupo- grupo que est�sendo avaliado
            $cod_avaliacao - avaliacao que est�sendo corrigida
   Saida: true se foi avaliado
*/
function GrupoFoiAvaliado($sock,$cod_avaliacao,$cod_grupo)
{
  $foi_avaliado=0;
  $query2="select * from Avaliacao_notas where cod_grupo=".$cod_grupo." and cod_avaliacao=".$cod_avaliacao." and ((status='A') || (status='F'))";
  $res2=Enviar($sock,$query2);
  $lista1=RetornaArrayLinhas($res2);
  if (count($lista1) > 0)
    $foi_avaliado=1;
  if ($foi_avaliado)
    return true;
  else
    return false;
}

/* *********************************************************************
   RetornaCodAlunodoGrupo - Retorna o codigo de um aluno pertencente ao grupo
   Entrada: $sock - BASE DO CURSO
            $cod_grupo- grupo que est�sendo avaliado
            $cod_avaliacao - avaliacao que est�sendo corrigida
   Saida: codigo do aluno
*/
function RetornaCodAlunodoGrupo($sock,$cod_avaliacao,$cod_grupo)
{
  $foi_avaliado=0;
  $query1="select cod_usuario from Grupos_usuario where cod_grupo=".$cod_grupo;
  $res1=Enviar($sock,$query1);
  $lista=RetornaArrayLinhas($res1);
  $ultimo=$lista[count($lista)]['cod_usuario'];
  if (count($lista) > 0)
  {
    foreach ($lista as $cod=>$linha)
    {
      $query2="select * from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and cod_aluno=".$linha['cod_usuario']." and ((status='A') || (status='F'))";
      $res2=Enviar($sock,$query2);
      $lista1=RetornaArrayLinhas($res2);
      if (count($lista1) > 0)
      {
        $foi_avaliado=1;
        $cod_aluno=$linha['cod_usuario'];   // pega um usuario que foi avaliado, para evitar que pegue um usuario que entrou no grupo
                                            // depois da avalia�o e ele nao ter nota, ai o grupo apareceria como se nao tivesse sido avaliado qdo na verdade foi
      }
      if ($foi_avaliado)
        break;
      else
        continue;
    }
  }
  if (!$foi_avaliado)
  {
    $cod_aluno=$ultimo;    //retorna o ultimo, pois o grupo nao foi avaliado, entao pode retornar qualquer usuario do grupo
  }
  return $cod_aluno;
}

/* *********************************************************************
   RetornaListaIntegrantes - Retorna a lista de integrantes de um grupo
   Entrada: $sock - BASE DO CURSO
            $cod_grupo- codigo do grupo
   Saida: array com [$cod_usuario]['nome']
                                  ['tipo_usuario']

*/
function RetornaListaIntegrantes($sock,$cod_grupo) {
  $query="select GU.cod_usuario from Grupos_usuario GU where GU.cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  $x=0;
  foreach($lista as $cod_aluno => $lista_codigo)
  {
    $codigos[$x]['cod_aluno']=$lista_codigo['cod_usuario'];
    $x++;
  }

  $sock = MudarDB($sock, "");
  foreach ($codigos as $cod => $linha)
  {
    $query = "select U.nome, UC.tipo_usuario from Usuario U inner join Usuario_curso UC ON (UC.cod_usuario_global = U.cod_usuario) AND (UC.cod_usuario = ".$linha['cod_aluno'].") AND (U.cod_usuario > 0)";
    $res=Enviar($sock,$query);
    $Dados=RetornaLinha($res);
    $saida[$linha['cod_aluno']]['nome']=$Dados['nome'];
    $saida[$linha['cod_aluno']]['tipo_usuario']=$Dados['tipo_usuario'];
  }
  $sock = MudarDB($sock, $_GET['cod_curso']);
  return $saida;
}

/* ********************************************************************************
   RetornarCompartilhamentosAvaliacaoAluno - Retorna os compartilhamentos da avalia�o de um aluno/grupo avaliado
   Entrada:  $sock - BASE DO CURSO
             $cod_aluno - aluno que foi avaliado
             $cod_avaliacao - avaliacao que est�sendo corrigida

   Saida: array['tipo_compartilhamento']= Codigo do formador;
*/
function RetornarCompartilhamentosAvaliacaoAluno($sock,$cod_avaliacao,$cod_aluno)
{
  $query="select tipo_compartilhamento from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and cod_aluno=".$cod_aluno." and status!='E' and status!='X'";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return ($lista);
}

/* *********************************************************************
   RetornaCodAlunoMaisNotasnoGrupo - Retorna o codigo de um aluno pertencente ao grupo que tem o maior numero de notas
   Entrada: $sock - BASE DO CURSO
            $cod_grupo- grupo que est�sendo avaliado
            $cod_avaliacao - avaliacao que est�sendo corrigida
   Saida: codigo do aluno
*/
function RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo)
{
  $query1="select cod_usuario from Grupos_usuario where cod_grupo=".$cod_grupo;
  $res1=Enviar($sock,$query1);
  $lista=RetornaArrayLinhas($res1);
  if (count($lista) > 0)
  {
    foreach ($lista as $cod=>$linha)
    {
      $query2="select * from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and cod_aluno=".$linha['cod_usuario']." and ((status='A') || (status='F'))";
      $res2=Enviar($sock,$query2);
      $num=RetornaArrayLinhas($res2);
      $cod_aluno[$linha['cod_usuario']]=count($num);  //armazena o cod_usuario e o numero de avalia��es de formadores encontradas para este aluno nesta atividade
    }
  }
  $maior_num=0;
  foreach ($lista as $cod=>$linha)
  {
    if ($cod_aluno[$linha['cod_usuario']] > $maior_num)
    {
      $maior_num=$cod_aluno[$linha['cod_usuario']];
      $retorno=$linha['cod_usuario'];
    }
  }
  if ($maior_num==0)
    $retorno=$linha['cod_usuario'];
  return $retorno;
}

// Modificado. Antes retornava o aluno que mais tem notas no grupo.
// Agora retorna o cod_aluno de um aluno do grupo que recebeu a avaliacao
/*function RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo)
{
  $query = "select cod_aluno from Avaliacao_notas
            where cod_avaliacao=".$cod_avaliacao."
              and cod_grupo=".$cod_grupo."
              and (status='A' or status='F')
            order by data desc";
  $res   = Enviar($sock, $query);
  $linha = RetornaLinha($res);
  return $linha[0];
}*/

/* ******************************************************
   ApagaAvaliacao - Muda o status da Avalia�o associada ao frum
    para 'A', impedindo que sejam visualizadas.

   Entradas: $sock - sock de conexao,
             $cod_avaliacao - cdigo da avaliacao.
             $cod_usuario - codigo do usuario
   Saida:    true se bem-sucedido, do contr�io, false.
*/
function ApagaAvaliacao($sock, $cod_avaliacao,$cod_usuario)
{
  $res1 = true;
  $query = "select ferramenta from Avaliacao where cod_avaliacao=".$cod_avaliacao;
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);
  
  if (!strcmp($linha[0],"E"))
  {
    $query = "update  Exercicios_aplicado set avaliacao=null where avaliacao=".$cod_avaliacao;
    $res1 = Enviar($sock, $query);
  }
  
  $query = "update Avaliacao set status = 'A' where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock, $query);
  $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'A')";
  $res=Enviar($sock,$consulta);
  return($res and $res1);
}

/* ******************************************************
   ExcluiAvaliacao - Muda o status da Avalia�o
    para 'X', impedindo que sejam visualizadas.

   Entradas: $sock - sock de conexao,
            $cod_avaliacao - cdigo da avaliacao.
   Saida:    true se bem-sucedido, do contr�io, false.
*/
function ExcluiAvaliacao($sock, $cod_avaliacao,$cod_usuario)
{
  $query = "select ferramenta from Avaliacao where cod_avaliacao=".$cod_avaliacao;
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);

  if (!strcmp($linha[0],"E"))
  {
    $query = "update  Exercicios_aplicado set avaliacao=null where avaliacao=".$cod_avaliacao;
    $res1 = Enviar($sock, $query);
  }

  $query = "update Avaliacao set status = 'X' where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock, $query);
  $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'X')";
  $res=Enviar($sock,$consulta);
  return($res);
}

/* *********************************************************************
   RetornaResHistoricoDaAvaliacao - Retorna todas as ocorr�cias do historico de uma avaliacao dada
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Atividade,Apoio,Leitura,Obrigatoria)
            $cod_avaliacao - avaliacao
   Saida: array []['cod_avaliacao']
                []['cod_usuario']
                []['data']
                []['acao']
*/
function RetornaResHistoricoDaAvaliacao ($sock, $tabela, $cod_avaliacao)
{
  $consulta="select * from ".$tabela." where cod_avaliacao=".$cod_avaliacao." order by data desc";
  $res=Enviar($sock, $consulta);
  return($res);
}

/* ******************************************************
   RecuperaAvaliacao - Muda o status da Avalia�o
    para 'F', permitindo que sejam visualizadas.

   Entradas: $sock - sock de conexao,
             $cod_avaliacao - cdigo da avaliacao.
             $cod_usuario - codigo do usuario que est�recuperando a avalia�o
   Saida:    true se bem-sucedido, do contr�io, false.

*/
function RecuperaAvaliacao($sock, $cod_avaliacao,$cod_usuario)
{
  $query = "update Avaliacao set status = 'F' where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock, $query);

  $query="select * from Avaliacao where cod_avaliacao= ".$cod_avaliacao;
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  if ($linha['ferramenta']=='E')
  {
    $query="update Exercicios_aplicado set avaliacao=".$linha['cod_avaliacao']." where cod_modelo=".$linha['cod_atividade'];
    $res = Enviar($sock,$query);
  }

  $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'R')";
  $res=Enviar($sock,$consulta);
  return($res);
}


/* ******************************************************************************************
   ListaCoordenador - Retorna uma lista com o cdigo e o nome do coordenador do curso
   Entradas: $sock - sock de conexao,
            $cod_curso - codigo do curso,
   Saida:   Array com: ['cod_usuario'] - codigo usuario
                       ['nome'] - nome
*/
function ListaCoordenador($sock, $cod_curso)
{
  $query = "select cod_coordenador from Cursos where cod_curso = ".$cod_curso;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  $query = "select U.cod_usuario, U.nome, Cf.valor 'status' from Usuario as U, Config as Cf where";
  $query .= " U.cod_usuario = ".$linha['cod_coordenador']." and Cf.item = 'status_coordenador'";
  $res = Enviar($sock,$query);

  $linha = RetornaLinha($res);

  return ($linha);
}

/* *********************************************************************
   RetornaListaUsuariosAluno - Retorna lista de nomes e cdigos de usuarios
   Entrada: $cod_curso - codigo do curso
   Saida:   array [$cod_usuario]= nome do usuario
   TODO - Altrar chamadas
*/
function RetornaListaUsuariosAluno($cod_curso)
{
  $sock = Conectar("");
  $query = "select Usuario.nome, Usuario_curso.cod_usuario from Usuario_curso inner join Usuario where (Usuario.cod_usuario = Usuario_curso.cod_usuario_global) AND (Usuario_curso.cod_curso = $cod_curso) AND (Usuario.cod_usuario > 0) AND (binary Usuario_curso.tipo_usuario='A') order by Usuario.nome";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista) > 0)
  {
    foreach ($lista as $cod=>$linha)
    {
      $lista1[$linha['cod_usuario']]=$linha['nome'];
    }
  }
  return ($lista1);
}

/* *********************************************************************
   RetornaListaUsuariosFormador - Retorna lista de nomes e cdigos de usuarios
   Entrada: $cod_curso - codigo do curso
   Saida:   array [$cod_usuario]= nome do usuario
   TODO - Altrar chamadas
*/
function RetornaListaUsuariosFormador($cod_curso)
{
  $sock = Conectar("");
  $query = "select Usuario.nome, Usuario_curso.cod_usuario from Usuario_curso inner join Usuario where (Usuario.cod_usuario = Usuario_curso.cod_usuario_global) AND (Usuario_curso.cod_curso = $cod_curso) AND (Usuario.cod_usuario > 0) AND (binary Usuario_curso.tipo_usuario='F')";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  if (count($lista) > 0)
  {
    foreach ($lista as $cod=>$linha)
    {
      $lista1[$linha['cod_usuario']]=$linha['nome'];
    }
  }
  return ($lista1);
}

/* ********************************************************************************
   RetornarAvaliacaoAluno - Retorna os Dados da avalia�o de um aluno/grupo avaliado na atividade
   Entrada:  $sock - BASE DO CURSO
             $cod_aluno - aluno ou grupo que foi avaliado
             $cod_avaliacao - avaliacao que est�sendo corrigida

   Saida: array['cod_nota'] = codigo da nota
          array['Cod_formador']= Codigo do formador;
          array['Comentario']= Comentario da avalia�o;
          array['Nota']= Nota da Avalia�o;
          array['Data']= Data da Avalia�o;
*/
function RetornarAvaliacaoAluno($sock,$cod_avaliacao,$cod_aluno)
{
  $query="select cod_nota,cod_formador,comentario,nota,data,tipo_compartilhamento from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and cod_aluno=".$cod_aluno." and status!='E' and status!='X' order by cod_nota";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return ($lista);
}

/* ********************************************************************************
   RetornarAvaliacaoGrupo - Retorna os Dados da avalia�o de um aluno/grupo avaliado na atividade
   Entrada:  $sock - BASE DO CURSO
             $cod_avaliacao - avaliacao que est�sendo corrigida
             $cod_grupo - aluno ou grupo que foi avaliado

   Saida: array['Cod_formador']= Codigo do formador;
          array['Comentario']= Comentario da avalia�o;
          array['Nota']= Nota da Avalia�o;
          array['Data']= Data da Avalia�o;
*/
function RetornarAvaliacaoGrupo($sock,$cod_avaliacao,$cod_grupo)
{
  $cod_aluno = RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo);

  $query="select cod_nota,cod_formador,comentario,nota,data,tipo_compartilhamento from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and cod_grupo=".$cod_grupo." and cod_aluno=".$cod_aluno." and status!='E' and status!='X' order by cod_nota desc";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  return ($lista);
}

/* ********************************************************************************
   RetornaDataNota - Retorna data da nota do aluno selecionado
   Entrada:  $sock - BASE DO CURSO
             $cod_nota - Cdigo da nota
   Saida:   linha
*/
function RetornaDataNota($sock, $cod_nota)
{
  $query="select data from Avaliacao_notas where cod_nota=".$cod_nota;
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha=RetornaLinha($res);
  }
  return $linha;
}

/* ********************************************************************************
   RetornaCod - Retorna o cdigo da nota do aluno selecionado
   Entrada:  $sock - BASE DO CURSO
             $cod_nota - Cdigo da nota
   Saida:   linha
*/
function RetornaCod($sock, $cod, $cod_avaliacao,$data)
{
  $query="select cod_nota from Avaliacao_notas where cod_aluno=".$cod." and cod_avaliacao=".$cod_avaliacao." and data=".$data;
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
  {
    $cod_nota=RetornaLinha($res);
    return $cod_nota[0];
  }
  else return;

}

/* ******************************************************
   ApagarNota - Muda o status da Avalia�o do aluno
    para 'X', impedindo que sejam visualizadas.

   Entradas: $sock - sock de conexao,
            $cod_nota - cdigo da nota.
   Saida:    true se bem-sucedido, do contr�io, false.
*/
function ApagarNota($sock,$cod_nota,$cod_usuario)
{
  $query = "delete from Avaliacao_notas where cod_nota= ".$cod_nota;
  $res = Enviar($sock, $query);
  return($res);
}

/* *********************************************************************
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
   AlunoFoiAvaliado - Verifica se o aluno j�foi avaliado
   Entrada: $sock - BASE DO CURSO
         $cod_aluno - aluno que est�sendo avaliado
         $cod_avaliacao - avaliacao que est�sendo corrigida
   Saida: array ['Existe'] - bool, se existe uma avaliacao para aquele aluno
             ['Edicao'] - bool, se o aluno est�sendo avaliado no momento
             ['Data'] - data em unixtime do in�io da avalia�o
             ['Cod_formador'] - o codigo do formador que iniciou a avaliacao
             ['Cod_nota'] - o codigo da ultima nota associada �avaliacao e ao aluno
*/
function AlunoFoiAvaliado($sock,$cod_avaliacao,$cod_aluno)
{
  $query="select cod_nota,cod_formador,status,data from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and cod_aluno=".$cod_aluno." and status!='X'";
  $res=Enviar($sock,$query);
  unset($retorno);
  $cont=0;
  if (RetornanumLinhas($res) > 0)
  {
    $retorno['Existe']=true;  //O aluno j�foi avaliado ou a avalia�o est�em edi�o
    $lista=RetornaArrayLinhas($res);
    $retorno['Edicao']=false; //A avalia�o n� est�em edi�o
    foreach ($lista as $cod => $linha)
    {
      //conta quantas avaliacoes foi feita para um aluno em uma avaliacao
      $cont++;
      if (!strcmp($linha['status'],'E'))
      {
        $retorno['Edicao']=true; //A avalia�o est�em edi�o
        $retorno['Data']=$linha['data'];
        $retorno['Cod_formador']=$linha['cod_formador'];
        $retorno['Cod_nota']=$linha['cod_nota'];
      }
    }
    $retorno['Cont']=$cont;
  }
  else
  {
    $retorno['Existe']=false;
  }
  return ($retorno);
}

/* ********************************************************************************
   RetornaAssociacaoItemAvaliacao - Retorna o codigo da avalia�o do item de portfolio associado, se tiver associado
   Entrada:  $sock - BASE DO CURSO
             $cod_item - Codigo do item do portfolio
   Saida: array['Cod_avaliacao']= Codigo da avaliacao;
*/
function RetornaAssociacaoItemAvaliacao($sock,$cod_item)
{
  $query="select PIA.cod_avaliacao from Portfolio_itens_avaliacao PIA, Avaliacao A where PIA.cod_item=".$cod_item." and A.status!='A' and A.status!='X' and A.status!='C' and PIA.cod_avaliacao=A.cod_avaliacao";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
  {
    $lista=RetornaLinha($res);
    return $lista;
  }
  else
    return;
}

/* *********************************************************************
   RetornaDadosDoItem - Retorna um array com os dados do item do portfolio
   Entrada: $sock - BASE DO CURSO
            $cod_topico - tpico
   Saida: array com ['cod_item']
                    ['cod_topico']
                    ['cod_usuario']
                    ['cod_grupo']
                    ['titulo']
                    ['texto']
                    ['tipo_compartilhamento']
                    ['data']
                    ['data_ativo']
                    ['data_inativo']
                    ['posicao_item']
                    ['status']
                    ['inicio_edicao']
*/
function RetornaDadosDoItem ($sock, $cod_item)
{
  $consulta="select * from Portfolio_itens where cod_item=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}

/* ********************************************************************************
   CancelaEdicaoAvaliacaoElementodoGrupo - Cancela a edi�o da avalia�o de um elemento do grupo
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela avaliacao_notas
            $cod_aluno - aluno que est�sendo avaliado
            $cod_avaliacao - avaliacao que est�sendo corrigida

   Saida: nenhuma
*/
function CancelaEdicaoAvaliacaoElementodoGrupo ($sock, $tabela, $cod_aluno, $cod_avaliacao)
{
  $consulta="delete from ".$tabela." where cod_avaliacao=".$cod_avaliacao." and cod_aluno=".$cod_aluno." and status='E'";
  $res=Enviar($sock, $consulta);
  return true;
}

/* ********************************************************************************
   CancelaEdicaoAvaliacaoParticipante- Cancela a edi�o da avalia�o de um participante
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela avaliacao_notas
            $cod_nota - codigo da nota

   Saida: nenhuma
*/
function CancelaEdicaoAvaliacaoParticipante ($sock, $tabela, $cod_nota)
{
  $consulta="delete from ".$tabela." where cod_nota=".$cod_nota;
  $res=Enviar($sock, $consulta);
  return true;
}

/* *********************************************************************
   IniciaAvaliacaoAluno - Inicia o processo de avalia�o de um aluno
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_aluno - aluno que est�sendo avaliado
            $cod_avaliacao - avaliacao que est�sendo corrigida
            $cod_formador - codigo do  formador que est�avaliando o aluno
            $cod_grupo - codigo do grupo se houver se n� passa null
   Saida: nenhuma
*/
function IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_formador, $cod_grupo)
{
  $cod_nota=RetornaProximoCodigo($sock, 'Avaliacao_notas');
  $consulta="insert into Avaliacao_notas (cod_nota, cod_aluno, cod_grupo, cod_avaliacao,cod_formador, comentario, nota, status, data,tipo_compartilhamento,data_alteracao) values (".$cod_nota.", ".$cod_aluno.",".$cod_grupo." ,".$cod_avaliacao.", ".$cod_formador.", '', '', 'E', ".time().",'',".time().")";
  $res=Enviar($sock, $consulta);
  return($cod_nota);
}

/* *********************************************************************
   AtualizaAvaliacaoAluno - Insere os dados de avalia�o de um aluno avaliado no portfolio
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_aluno - aluno que est�sendo avaliado
            $cod_nota - codigo da nota
            $comentario -  comentario atribuido pelo formador a avalia�o realizada
            $nota - Nota atribu�a ao aluno
            $compartilhamento - tipo de compartilhamento da avaliacao
   Saida: nenhuma
*/
function AtualizaAvaliacaoAluno($sock, $tabela, $cod_nota, $comentario, $nota, $compartilhamento)
{
  EliminaScript($comentario);
  LimpaConteudo($comentario);
  $consulta=" Update ".$tabela." set status='F',comentario='".$comentario."',nota=".$nota.",tipo_compartilhamento='".$compartilhamento."',data_alteracao=".time()." where cod_nota=".$cod_nota;
  Enviar($sock,$consulta);
}

/* *********************************************************************
   AtualizaAvaliacaoElementodoGrupo - Insere os dados de avalia�o de um elemento de um grupo em avaliacao no portfolio
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacao_notas)
            $cod_grupo - grupo que est�sendo avaliado
            $cod_avaliacao - avaliacao que est�sendo corrigida
            $comentario -  comentario atribuido pelo formador a avalia�o realizada
            $nota - Nota atribu�a ao aluno
            $compartilhamento - tipo de compartilhamento da avaliacao
   Saida: nenhuma
*/
function AtualizaAvaliacaoElementodoGrupo($sock, $tabela, $cod_grupo, $cod_avaliacao, $comentario, $nota,$compartilhamento)
{
  $comentario=EliminaScript($comentario);
  $comentario=LimpaConteudo($comentario);
  $consulta="update ".$tabela." set status='F',comentario='".$comentario."',nota=".$nota.",tipo_compartilhamento='".$compartilhamento."',data_alteracao=".time()." where cod_avaliacao=".$cod_avaliacao." and cod_grupo=".$cod_grupo." and status='E'";
  Enviar($sock,$consulta);
}

/* *********************************************************************
   AtualizaAlteracaoAvaliacao - Altera os dados de avalia�o de um aluno j�avaliado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_aluno - aluno que est�sendo avaliado
            $cod_avaliacao - avaliacao que est�sendo corrigida
            $cod_nota - codigo da nota
            $comentario -  comentario atribuido pelo formador a avalia�o realizada
            $nota - Nota atribu�a ao aluno
            $compartilhamento - tipo de compartilhamento da avaliacao
   Saida: nenhuma
*/
function AtualizaAlteracaoAvaliacao($sock, $tabela, $cod_aluno, $cod_avaliacao, $cod_nota, $comentario, $nota,$compartilhamento)
{
  $comentario=EliminaScript($comentario);
  $comentario=LimpaConteudo($comentario);
  $consulta="update ".$tabela." set status='A' where cod_avaliacao=".$cod_avaliacao." and cod_aluno=".$cod_aluno." and status='F'";
  Enviar($sock,$consulta);
  $consulta="update ".$tabela." set comentario='".$comentario."', nota=".$nota.", status='F', tipo_compartilhamento='".$compartilhamento."',data_alteracao=".time()." where cod_nota=".$cod_nota;
  $res=Enviar($sock, $consulta);
}

/* *********************************************************************
   AtualizaAlteracaoAvaliacaoElementodoGrupo - Altera os dados de avalia�o de um elemento de um grupo j�avaliado
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_aluno - aluno que est�sendo avaliado
            $cod_avaliacao - avaliacao que est�sendo corrigida
            $comentario -  comentario atribuido pelo formador a avalia�o realizada
            $nota - Nota atribu�a ao aluno
            $compartilhamento - tipo de compartilhamento da avaliacao
   Saida: nenhuma
*/
function AtualizaAlteracaoAvaliacaoElementodoGrupo($sock, $tabela, $cod_grupo, $cod_avaliacao, $comentario, $nota,$compartilhamento)
{
  $comentario=EliminaScript($comentario);
  $comentario=LimpaConteudo($comentario);
  $consulta="update ".$tabela." set status='A' where cod_avaliacao=".$cod_avaliacao." and cod_grupo=".$cod_grupo." and status='F'";
  Enviar($sock,$consulta);
  $consulta="update ".$tabela." set comentario='".$comentario."', nota=".$nota.", status='F', tipo_compartilhamento='".$compartilhamento."',data_alteracao=".time()." where cod_avaliacao=".$cod_avaliacao." and cod_grupo=".$cod_grupo." and status='E'";
  $res=Enviar($sock, $consulta);
}

/* ********************************************************************************
   RetornaDadosAvaliacao - Retorna os Dados de avalia�o do forum a ser avaliado
   Entrada:  $sock - BASE DO CURSO
             $cod_atividade - codigo do forum que ser�feita a avalia�o
             $ferramenta - ferramenta na qual est�sendo realizada a atividade
   Saida: array['Cod_avaliacao']= Codigo da avaliacao;
          array['Tipo']= Tipo da Avalia�o;
          array['Valor']= Valor da Avalia�o;
          array['Data_inicio']= Data de in�io da Avalia�o;
          array['Data_t�mino']= Data de t�mino da Avalia�o;
*/
function RetornaDadosAvaliacao($sock,$cod_atividade,$ferramenta)
{
  $query="select cod_avaliacao,ferramenta,valor,data_inicio,data_termino from Avaliacao where cod_atividade=".$cod_atividade." and ferramenta='".$ferramenta."' and status != 'X' and status != 'A' and status != 'C'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  unset($retorno);
  $retorno['Cod_avaliacao']=$linha['cod_avaliacao'];
  $retorno['Tipo']=$linha['tipo'];
  $retorno['Valor']=$linha['valor'];
  $retorno['Data_inicio']=$linha['data_inicio'];
  $retorno['Data_termino']=$linha['data_termino'];
  return $retorno;
}

/* ********************************************************************************
   RetornaCodGrupo - Retorna o codigo do grupo que postou o item
   Entrada: $sock - BASE DO CURSO
            $cod_item - cdigo do item do portfolio
   Saida: codigo do grupo

*/
function RetornaCodGrupo($sock,$cod_item)
{
  $query="select cod_grupo from Portfolio_itens where cod_item=".$cod_item;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}


/* ********************************************************************************
   RetornaCodUsuarioPortfolio - Retorna o codigo do aluno que postou o item
   Entrada: $sock - BASE DO CURSO
            $cod_item - cdigo do item do portfolio
   Saida: codigo do aluno

*/
function RetornaCodUsuarioPortfolio($sock,$cod_item)
{
  $query="select cod_usuario from Portfolio_itens where cod_item=".$cod_item;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}

/* ********************************************************************************
   RetornaCodAtividade - Retorna o codigo da atividade da avalia�o cadastrada
   Entrada:  $sock - BASE DO CURSO
             $cod_avaliacao - Codigo da Avalia�o

   Saida: array['Cod_atividade']= Codigo da atividade;
*/
function RetornaCodAtividade($sock,$cod_avaliacao)
{
  $query="select cod_atividade from Avaliacao where cod_avaliacao=".$cod_avaliacao."";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}

/* ********************************************************************************
   RetornaAvaliacao - Retorna os Dados de avalia�o do forum a ser criado
   Entrada:  $sock - BASE DO CURSO
             $cod_avaliacao - cdigo da avaliacao
             $ferramenta- Ferramenta em que a avalia�o �realizada

   Saida: array['Cod_avaliacao']= Cdigo da Avaliacao;
          array['Objetivos']= Objetivos da Avaliacao;
          array['Crit�ios']= Crit�ios da Avaliacao;
          array['Valor']= Valor da Avalia�o;
          array['Status']= Status da avalia�o;
          array['Data']= Data de cria�o da Avalia�o;
          array['Data_inicio']= Data de inicio da Avalia�o;
          array['Data_termino']= Data de termino da Avalia�o;
*/
function RetornaAvaliacao($sock,$cod_atividade,$ferramenta)
{
  $query1="select count(*) from Avaliacao where cod_atividade=".$cod_atividade." and ferramenta='".$ferramenta."' and status!='X'";
  $res1=Enviar($sock,$query1);
  $num=RetornaLinha($res1);

  $query="select cod_avaliacao,cod_usuario,status,data from Avaliacao where cod_atividade=".$cod_atividade." and ferramenta='".$ferramenta."' and status!='X'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  unset($retorno);
  $retorno['Cont']=$num[0];
  $retorno['Cod_avaliacao']=$linha['cod_avaliacao'];
  $retorno['Cod_usuario']=$linha['cod_usuario'];
  $retorno['Status']=$linha['status'];
  $retorno['Data']=$linha['data'];
  return $retorno;
}

/* *********************************************************************
   IniciaCriacaoAvaliacao - Inicia a cria�o de uma nova avaliacao
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacao)
            $cod_atividade - codigo da atividade  (cod_item da atividade)
            $cod_formador - formador que esta realizando a avaliacao
            $ferramenta - Ferramenta de comunicacao que sera realizada a atividade
   Saida: codigo da avaliacao criada
*/
function IniciaCriacaoAvaliacao ($sock, $tabela, $cod_atividade, $cod_formador, $ferramenta, $tipo)
{
  $cod_avaliacao=RetornaProximoCodigo($sock,$tabela);

  /* data_termino = tempo at� o exato instante, menos o que jah foi de hoje, somado a 23h59m59s */
  /* data_termino = hoje as 23h59m59s */ 
  $data_termino = time() - (time()%86400) + 86399;
  $consulta="insert into ".$tabela." (cod_avaliacao,cod_atividade,cod_usuario,ferramenta,tipo,status,data,data_inicio,data_termino,inicio_edicao) values (".$cod_avaliacao.", ".$cod_atividade.", ".$cod_formador.", '".$ferramenta."', '".$tipo."', 'L',".time().",".time().",".$data_termino.",".time().")";
  $res=Enviar($sock, $consulta);
  $consulta="insert into ".$tabela."_historicos values (".$cod_avaliacao.", ".$cod_formador.", ".time().", 'C')";
  $res=Enviar($sock, $consulta);
  
  return($cod_avaliacao);
}

/* *********************************************************************
   IniciaCriacaoAvaliacaoExercicio - Inicia a cria�o de uma nova avaliacao e atyualiza a base de exercicio
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacao)
            $cod_atividade - codigo da atividade  (cod_item da atividade)
            $cod_formador - formador que esta realizando a avaliacao
            $ferramenta - Ferramenta de comunicacao que sera realizada a atividade
   Saida: codigo da avaliacao criada
*/
function IniciaCriacaoAvaliacaoExercicio ($sock, $tabela, $cod_atividade, $cod_formador, $ferramenta)
{
  $cod_avaliacao=RetornaProximoCodigo($sock,$tabela);
  $consulta="insert into ".$tabela." (cod_avaliacao,cod_atividade,cod_usuario,ferramenta,status,data,inicio_edicao) values (".$cod_avaliacao.", ".$cod_atividade.", ".$cod_formador.", '".$ferramenta."', 'C',".time().", ".time().")";
  $res=Enviar($sock, $consulta);
  $consulta="insert into ".$tabela."_historicos values (".$cod_avaliacao.", ".$cod_formador.", ".time().", 'C')";
  $res=Enviar($sock, $consulta);
  $query = "update Exercicios_aplicado set avaliacao=".$cod_avaliacao." where cod_modelo=".$cod_atividade;
  $res=Enviar($sock, $query);
  return($cod_avaliacao);
}

/* *********************************************************************
   RetornaCodAssuntoBatePapo- Retorna o codigo do assunto da sess� dada
   Entrada: $sock - BASE DO CURSO
            $cod_sessao - cdigo da sess�
   Saida: cod_assunto
*/
function RetornaCodAssuntoBatePapo($sock,$cod_sessao)
{
  global $lista_frases; /* Definida na arquivo de chamada */

  /* Busca o per�do da sess� */
  $query="select data_inicio,data_fim from Batepapo_sessoes where cod_sessao=".$cod_sessao;
  $res=Enviar($sock,$query);

  /* Se a sess� existe (n� �a atual), varre tabela */
  $linha=RetornaLinha($res);

  $di=$linha[0];
  $df=$linha[1];

  $query ="select cod_assunto from Batepapo_assuntos where ";
  $query.="(data_inicio<=".$di." and data_fim>=".$di.") or ";
  $query.="(data_inicio<=".$df." and data_fim>=".$df.") or ";
  $query.="(data_inicio>=".$di." and data_inicio<=".$df.") or ";
  $query.="(data_fim>=".$di." and data_fim<=".$df.")";

  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
   return $linha[0];

}

/* *********************************************************************
   AtualizaCadastroAvaliacao - Atualiza os campos objetivos, criterios, valor, a data de inicio, data de termino de uma avalia�o
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_formador - Cdigo do formador que cadastrou
            $objetivos - objetivos da avalia�o
            $criterios - criterios da avalia�o
            $valor - Valor da avalia�o
            $data_inicio - Data de in�io da Atividade
            $data_termino - Data de termino da Atividade
            $cod_atividade - cdigo que identifica a atividade em que a avalia�o ir�atuar

   Saida: nenhuma
*/
function AtualizaCadastroAvaliacao($sock,$tabela,$cod_formador,$objetivos,$criterios,$tipo,$valor,$data_inicio,$data_termino,$cod_avaliacao)
{
$objetivos=EliminaScript($objetivos);
$objetivos=LimpaConteudo($objetivos);
$criterios=EliminaScript($criterios);
$criterios=LimpaConteudo($criterios);
$consulta="update ".$tabela." set cod_usuario=".$cod_formador.",objetivos='".$objetivos."', criterios='".$criterios."', tipo='".$tipo."', valor=".$valor.",status='F',data=".time().",data_inicio=".$data_inicio.",data_termino=".$data_termino." where cod_avaliacao=".$cod_avaliacao."";
$res=Enviar($sock, $consulta);
//Verificar a consulta abaixo, pois a avaliacao s sera finalizada no final da edicao da atividade, entao so deveria inserir em avaliacao_historicos
//depois que finalizasse a edicao da atividade, porem isso faria que fosse inserido em Avaliacao_historico toda vez que editasse a atividade
$consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_formador.", ".time().", 'F')";
$res=Enviar($sock, $consulta);
}


/* *********************************************************************
   AtualizaAvaliacaoTemporaria - Atualiza o coment�io,o valor, a data de inicio, data de termino de uma avalia�o
   Entrada: $sock - BASE DO CURSO
            $tabela - tabela a ser usada (Avaliacoes)
            $cod_formador - Cdigo do formador que cadastrou
            $objetivos - objetivos da avalia�o
            $criterios - criterios da avalia�o
            $tipo -tipo da atividade:individual ou em grupo
            $valor - Valor da avalia�o
            $data_inicio - Data de in�io da Atividade
            $data_termino - Data de termino da Atividade
            $cod_atividade - cdigo que identifica a atividade em que a avalia�o ir�atuar

   Saida: nenhuma
*/
function AtualizaAvaliacaoTemporaria($sock,$tabela,$cod_formador,$objetivos,$criterios,$tipo,$valor,$data_inicio,$data_termino,$cod_avaliacao)
{
  $objetivos=EliminaScript($objetivos);
  $objetivos=LimpaConteudo($objetivos);
  $criterios=EliminaScript($criterios);
  $criterios=LimpaConteudo($criterios);
  $consulta="update ".$tabela." set cod_usuario=".$cod_formador.",objetivos='".$objetivos."', criterios='".$criterios."',tipo='".$tipo."',valor=".$valor.",status='D',data=".time().",data_inicio=".$data_inicio.",data_termino=".$data_termino." where cod_avaliacao=".$cod_avaliacao."";
  $res=Enviar($sock, $consulta);
}


/* ******************************************************
   ApagaAvaliacaoTemporario - Muda o status da Avalia�o associada ao portflio
    para 'T', impedindo que seja visualizada.
    status T quer dizer que �temporario, passar�para 'A' qdo terminar a edicao da atividade

   Entradas: $sock - sock de conexao,
            $cod_avaliacao - cdigo da avaliacao.
   Saida:    true se bem-sucedido, do contr�io, false.
*/
function ApagaAvaliacaoTemporario($sock, $cod_avaliacao, $cod_usuario)
{
  $query = "update Avaliacao set status='T' where cod_avaliacao=".$cod_avaliacao;
  $res = Enviar($sock, $query);
  return($res);
}

/* ******************************************************
   AtualizaItemDeAvaliacao - Muda o status do item de avalia�o para 'A' (Avaliado)

   Entradas: $sock - sock de conexao,
            $cod_avaliacao - cdigo da avaliacao.
            $cod_item - Codigo do item do portfolio
   Saida:    true se bem-sucedido, do contr�io, false.
*/
function AtualizaItemDeAvaliacao($sock,$cod_avaliacao,$cod_item,$cod_nota)
{

  $query = "select * from Portfolio_itens_avaliacao where status='N' and cod_avaliacao=".$cod_avaliacao." and cod_item=".$cod_item;
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    $query = "update Portfolio_itens_avaliacao set status='A',cod_nota=".$cod_nota." where cod_avaliacao=".$cod_avaliacao." and cod_item=".$cod_item;
    $res = Enviar($sock, $query);
    return($res);
  }
  else
  {
    $consulta="insert into Portfolio_itens_avaliacao values (".$cod_item.",".$cod_avaliacao.", ".$cod_nota.",'A')";
    $res=Enviar($sock,$consulta);
  }
}


/* *********************************************************************
   RetornaItemNaoAvaliado - retorna o item associado a avaliacao que nao foi avaliado. Se n� houver retorna um item que ja foi avaliado
   Entrada: $sock - BASE DO CURSO
            $cod_avaliacao - codigo da avaliacao
            $cod - cdigo do aluno ou do grupo
            $portfolio_grupo - true se portfolio de grupo, false caso contrario
            $cod_topico_raiz - codigo do topico raiz do usuario selecionado

   Saida:     numero de avaliacoes
*/
function RetornaItemNaoAvaliado($sock,$cod_avaliacao,$cod_usr_grp,$portfolio_grupo,$cod_topico_raiz)
{
  $ha_nao_avaliado=0;
  $ha_avaliado=0;

  if ($portfolio_grupo)
  {
    $tipo='G';
    $query="select PIA.cod_item from Portfolio_itens_avaliacao PIA, Portfolio_itens PI, Avaliacao A where PIA.cod_avaliacao=".$cod_avaliacao." and PIA.status='N' and PIA.cod_item=PI.cod_item and PI.cod_grupo=".$cod_usr_grp." and PI.cod_topico!=2 and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
  }
  else
  {
    $tipo='I';
    $query="select PIA.cod_item from Portfolio_itens_avaliacao PIA, Portfolio_itens PI, Avaliacao A where PIA.cod_avaliacao=".$cod_avaliacao." and PIA.status='N' and PIA.cod_item=PI.cod_item and PI.cod_usuario=".$cod_usr_grp." and PI.cod_topico!=2 and PI.cod_grupo IS NULL and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
  }
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0)
  {
    while (( list($cod, $linha) = each($lista) ) && !$ha_nao_avaliado)
    {
      $ha_nao_avaliado=1;
      $cod_item=$linha['cod_item'];
    }
    if ($ha_nao_avaliado)    //encontrou um item nao avaliado
      return $cod_item;
  }
  else  //nao existe item nao avaliado, precisa pegar um item avaliado
  {
    if ($portfolio_grupo)
      $query1="select PIA.cod_item from Portfolio_itens_avaliacao PIA, Portfolio_itens PI, Avaliacao A where PIA.cod_avaliacao=".$cod_avaliacao." and PIA.status='A' and PIA.cod_item=PI.cod_item and PI.cod_grupo=".$cod_usr_grp." and PI.cod_topico!=2 and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";
    else
      $query1="select PIA.cod_item from Portfolio_itens_avaliacao PIA, Portfolio_itens PI, Avaliacao A where PIA.cod_avaliacao=".$cod_avaliacao." and PIA.status='A' and PIA.cod_item=PI.cod_item and PI.cod_usuario=".$cod_usr_grp." and PI.cod_topico!=2 and PI.cod_grupo IS NULL and A.cod_avaliacao=PIA.cod_avaliacao and A.tipo='".$tipo."' and (A.status='F' or A.status='E')";

    $res1=Enviar($sock,$query1);
    $lista1=RetornaArrayLinhas($res1);

    if (count($lista1) > 0)
    {
      while (( list($cod1, $linha1) = each($lista1) ) && !$ha_avaliado)
      {
         $ha_avaliado=1;
         $cod_item=$linha1['cod_item'];
      }
    }
    if ($ha_avaliado)    //encontrou um item avaliado
      return $cod_item;
  }
  return ""; //nao h�itens para este usuario
}

/* *********************************************************************
   RetornaPastaRaizUsuario - Retorna o cdigo da pasta raiz do usu�io ou grupo
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo do usuario que est�removendo, para o histrico
            $cod_grupo - codigo do grupo (NULL ou "" se portfolio individual)
   Saida: nenhuma
*/
function RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo)
{
  if ($cod_usuario==-2)
    return 'NULL';
  /* definindo regra de escolha de pasta */
  if ($cod_grupo=="NULL" || $cod_grupo=="")
  {
    $cod_topico_pai=3;
    $regra="(cod_usuario=".$cod_usuario." and cod_grupo IS NULL)";
    $cod_grupo="NULL";
    $rep="#U".$cod_usuario;
  }
  else
  {
    $cod_topico_pai=4;
    $regra="(cod_grupo=".$cod_grupo.")";
    $rep="#G".$cod_grupo;
  }

  $query="select cod_topico from Portfolio_topicos where ".$regra." and cod_topico_pai=".$cod_topico_pai;
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    $linha=RetornaLinha($res);
    return $linha['cod_topico'];
  }
  else
  {
    $query="insert into Portfolio_topicos (cod_topico_pai,cod_usuario,cod_grupo,tipo_compartilhamento,data,posicao_topico,topico) values (".$cod_topico_pai.",".$cod_usuario.",".$cod_grupo.",'P',".time().",".UltimaPosicaoLivreTopico($sock, $cod_topico_pai).",'".$rep."')";
    Enviar($sock,$query);
    return RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo);
  }
}


/* *********************************************************************
   UltimaPosicaoLivreTopico - Retorna a posicao do ultimo topico livre
   Entrada: $sock - BASE DO CURSO
            $cod_topico - codigo do topico atual
   Saida: posicao
*/
function UltimaPosicaoLivreTopico($sock, $cod_topico)
{
  $consulta="select posicao_topico from Portfolio_topicos where cod_topico_pai=".$cod_topico." order by posicao_topico desc limit 1";
  $res=Enviar($sock, $consulta);
  $num=RetornaNumLinhas($res);
  if ($num>0)
  {
    $linha=RetornaLinha($res);
    $linha['posicao_topico']++;
    return($linha['posicao_topico']);
  }
  else
    return(1);
}


/**********************************************************************
RetornaAvaliacoesFuturas - Verifica se �uma avalia�o futura
Entrada: $sock - BASE DO CURSO
         $cod_avaliacao -  cdigo da avalia�o
Saida: true se �avaliacao futura, false caso contr�io
*/
function EhAvaliacaoFutura($sock,$cod_avaliacao)
{
  $consulta = "select * from Avaliacao ";
  $consulta.= "where data_inicio > ".time()." and ";
  $consulta.= "      status!='A' and ";
  $consulta.= "      status!='C' and ";
  $consulta.= "      status!='X' and ";
  $consulta.= "      cod_avaliacao=".$cod_avaliacao;
  $res=Enviar($sock, $consulta);
  if (RetornaNumLinhas($res) > 0)
    return true;
  else
    return false;
}

/* ***************************************************************************
   FormataNota - Escreve a nota em determinado formato, no caso com duas casas decimais
   entrada: $nota - numerico com o valor da nota
   saida: nota em formato com duas casas decimais.
*/
function FormataNota($nota)
{
  return sprintf("%0.2f", $nota);
}

/* ***************************************************************************
   RetornaListaItensAvaliacaoPortfolio - retorna uma lista com os itens que o aluno ou grupo
    associaram a avaliacao
   entrada: $sock - sock de conexao
            $cod_avaliacao - codigo da avaliacao
            $cod_aluno_grupo - codigo do aluno ou grupo que realizou o item
   saida: array[][cod_item]         - codigo do item
               [][titulo]           - titulo do item
               [][data]             - data do item
               [][compartilhamento] - pode ser 'P'essoal, Compartilhado com 'F'ormadores
               [][status]           - status do item
*/
function RetornaListaItensAvaliacaoPortfolio($sock, $cod_avaliacao, $cod_aluno_grupo)
{
  $query  = "select distinct(PI.cod_item), PI.titulo, PI.data, PI.tipo_compartilhamento, PIA.status";
  $query .= "  from Portfolio_itens PI, Portfolio_itens_avaliacao PIA, Avaliacao A ";
  $query .= "where PI.cod_item=PIA.cod_item and ";
  $query .= "      A.cod_avaliacao=PIA.cod_avaliacao and ";
  $query .= "      PIA.cod_avaliacao=".$cod_avaliacao." and ";
  $query .= "      ((A.tipo='I' and PI.cod_usuario=".$cod_aluno_grupo.") or ";
  $query .= "       (A.tipo='G' and PI.cod_grupo=".$cod_aluno_grupo."))";

  $res   = Enviar($sock, $query);
  $lista = RetornaArrayLinhas($res);
  return $lista;
}

/* ***************************************************************************
   RetornaCodGrupoPortfolioAvaliacao - retorna o grupo em que o aluno estava
    quando foi avaliado
   entrada: $sock - sock de conexao
            $cod_aluno - codigo do aluno
            $cod_avaliacao - codigo da avaliacao
   saida: codigo do grupo
*/
function RetornaCodGrupoPortfolioAvaliacao($sock,$cod_aluno,$cod_avaliacao)
{
  $query  = "select G.cod_grupo from Avaliacao_notas A, Grupos_usuario G ";
  $query .= "where A.cod_avaliacao=".$cod_avaliacao." and ";
  $query .= "      A.cod_grupo=G.cod_grupo and ";
  $query .= "      G.cod_usuario=".$cod_aluno;
  $res  = Enviar($sock, $query);
  $linha = RetornaLinha($res);
  return $linha[0];
}

/* ******************************************************
   RetornaExercicio - retorna o nome do modelo.

   Entradas: $sock - sock de conexao,
             $cod_exercicio - c�digo do exercicio.

   Saida:    nome do frum,
*/
function RetornaExercicio($sock, $cod_exercicio)
{
  $query = "select titulo from Exercicios_modelo where cod_exercicio = ".$cod_exercicio;
  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);
  return $tupla['titulo'];
}

/* ******************************************************
   VerificaAvaliacaoParticipantes - Verifica se a Avalia�o �
       referente a exercicios e se sim, verifica se �um 
       exercicio objetivo.

   Entradas:$cod_avaliacao - cdigo da avaliacao.

   Saida:   true se pode avaliar participante,
            false caso contr�io.
*/
function VerificaAvalicaoParticipantes($sock, $cod_avaliacao)
{
  $query = "select cod_atividade, ferramenta from Avaliacao where cod_avaliacao=".$cod_avaliacao;
  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);
  return true;

  if (strcmp($tupla['ferramenta'],"E")||true)
    return(true);
  else
  {
    $query = "select objetiva from Exercicios_aplicado where avaliacao=".$cod_avaliacao;
    $res = Enviar($sock, $query);
    $linha = RetornaLinha($res);
    if (!strcmp($linha['objetiva'], "N"))
    {
      return(true);
    }
    else
      return(false);
  }
}


/* ***************************************************************************
   RetornaDadosExercicioAvaliado - retorna dados do exercicio avaliado
   entrada: $sock - sock de conexao
            $cod_avaliacao - codigo da avalia�o
            $cod_aluno_grupo - codigo do aluno ou grupo que realizou o item
   saida: array[][cod_resolucao]    - codigo da resolucao
               [][titulo]           - titulo do modelo
               [][dt_submissao]     - data de submiss�
               [][compartilhada]    - pode ser 'P'essoal, Compartilhado com 'F'ormadores
               [][status]           - status do item
               [][cod_modelo]       - codigo do modelo
*/
function RetornaDadosExercicioAvaliado($sock, $cod_avaliacao, $cod_aluno_grupo,$grupo)
{
  if ($grupo)
  {
  $query = "select DISTINCT ER.cod_resolucao, EM.titulo, ER.dt_submissao, ER.compartilhada, ER.status, EM.cod_modelo, ER.cod_usuario".
           "  from Exercicios_resolucao ER, Exercicios_modelo EM, Avaliacao A, Exercicios_aplicado EA".
           " where ER.cod_modelo=EM.cod_modelo".
           "   and EA.cod_modelo=EM.cod_modelo".
           "   and EA.avaliacao=".$cod_avaliacao.
           "   and ER.cod_grupo=".$cod_aluno_grupo;
  }
  else
  {
      $query ="select DISTINCT ER.cod_resolucao, EM.titulo, ER.dt_submissao, ER.compartilhada, ER.status, EM.cod_modelo, ER.cod_usuario".
            "  from Exercicios_resolucao ER, Exercicios_modelo EM, Avaliacao A, Exercicios_aplicado EA".
            " where ER.cod_modelo=EM.cod_modelo".
            "   and EA.cod_modelo=EM.cod_modelo".
            "   and EA.avaliacao=".$cod_avaliacao.
            "   and ER.cod_usuario=".$cod_aluno_grupo;
  }
  $res   = Enviar($sock, $query);
  $lista = RetornaLinha($res);
  return $lista;
}

/* ***************************************************************************
   RetornaDadosNotaExercicio - retorna array com a nota e um campo indicando a existencia de avaliacao
   entrada: $sock - sock de conexao
            $cod_avaliacao - codigo da avaliacao
            $cod_aluno_grupo - codigo do aluno ou do grupo
   saida:  array[][nota] - retorna a nota
           array[][comentario] - retorna o comentario
           array[][resolvido] - True se foi resolvido
           array[][existe] - True se existe
*/
function RetornaDadosNotaExercicio($sock, $cod_avaliacao, $cod_aluno_grupo,$grupo)
{
  if ($grupo)
    $query = "select nota, comentario from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and cod_grupo=".$cod_aluno_grupo; //and status='F'";
  else
    $query = "select nota, comentario from Avaliacao_notas where cod_avaliacao=".$cod_avaliacao." and cod_aluno=".$cod_aluno_grupo; //and status='F'";
  $res = Enviar($sock, $query);
  $Existe = RetornaNumLinhas($res);
  $lista = RetornaLinha($res);
  if (!$grupo)
    $retorno['nota']=RetornaNotaExercicio($sock, $cod_avaliacao, $cod_aluno_grupo);
    //$retorno['nota']=$lista['nota'];
  $retorno['comentario']=$lista['comentario'];

  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  $cod_atividade=$dados['Cod_atividade'];

  //$query2 = "select ER.cod_resolucao from Exercicios_resolucao ER, Exercicios_aplicado EA  where ER.cod_modelo=EA.cod_modelo and EA.avaliacao=".$cod_avaliacao;
  $query2 = "select corrigida from Exercicios_resolucao where cod_exercicio=".$cod_atividade." and cod_usuario=".$cod_aluno_grupo;
  $res2 = Enviar($sock, $query2);
  $return = RetornaLinha($res2);

  //$num = RetornaNumLinhas($res2);//T� errado?
  $retorno['resolvido'] = 1;
  if ($return['corrigida'] == 'S')
    $retorno['resolvido'] = 1;
  else 
    $retorno['resolvido'] = 0;

  $retorno['existe'] = 1;
  if ($Existe == 0)
    $retorno['existe'] = 0;
  else
    $retorno['existe'] = 1;

  return $retorno;
}

/* ***************************************************************************
   RetornaCodigoAtividade - retorna o codigo da atividade relacionada a avaliacao
   entrada: $sock - sock de conexao
            $cod_avaliacao - codigo da avalia�o
   saida: codigo da atividade relacionada a avaliacao
*/
function RetornaCodigoAtividade($sock, $cod_avaliacao)
{
  $query ="select cod_atividade from Avaliacao where cod_avaliacao=".$cod_avaliacao;
  $res   = Enviar($sock, $query);
  $lista = RetornaLinha($res);
  return $lista['cod_atividade'];
}


/* *********************************************************************
   RetornaDataExercicio - retorna as datas do modelo.

   Entradas: $sock - sock de conexao,
             $cod_exercicio - cdigo do exercicio.

   Saida:    array com dt_disponibilizacao e dt_limite_submissao,
*/
function RetornaDataExercicio($sock, $cod_exercicio)
{
  $query = "select dt_disponibilizacao, dt_limite_submissao from Exercicios_aplicado where cod_modelo = ".$cod_exercicio;

  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);

  return $tupla;
}

/* *********************************************************************
   RetornValorModelo - retorna o valor do modelo.

   Entradas: $sock - sock de conexao,
             $cod_exercicio - cdigo do modelo.

   Saida:    inteiro com o valor total do modelo,
*/
function RetornaValorModelo($sock, $cod_exercicio)
{
  $query = "select SUM(valor) from Exercicios_questao_modelo where cod_modelo = ".$cod_exercicio;

  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);

  return $tupla[0];
}


/* *********************************************************************
   RetornaTipoModelo - retorna o valor do modelo.

   Entradas: $sock - sock de conexao,
             $cod_exercicio - cdigo do modelo.

   Saida:    inteiro com o valor total do modelo,
*/
function RetornaTipoModelo($sock, $cod_exercicio)
{
  $query = "select tp_aplicacao from Exercicios_aplicado where cod_modelo = ".$cod_exercicio;

  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);

  return $tupla[0];
}


/* *********************************************************************
   RetornaExercicioResolvido - retorna 1 se o exercicio foi resolvido e 0 se nao.

   Entradas: $sock - sock de conexao,
             $cod_avaliacao - cdigo da avaliacao.
             $cod_grupo - codigo do grupo dono do exercicio                                                                                                                
   Saida:  retorna 1 se o exercicio foi resolvido e 0 se n�,
*/
function RetornaExercicioResolvido($sock, $cod_avaliacao, $cod_grupo)
{
  $query = "select cod_atividade from Avaliacao where cod_avaliacao =".$cod_avaliacao;
  $res = Enviar($sock, $query);
  $cod_exercicio = RetornaLinha($res);

  $query = "select corrigida from Exercicios_resolucao where cod_exercicio = ".$cod_exercicio[0]." and cod_grupo = ".$cod_grupo;

  $res = Enviar($sock, $query);
  $tupla = RetornaLinha($res);

  if ($tupla[0]== 'S')
    return 1;
  return 0;
}

/* *******************************************************************
   RetornaNomeGrupo - retorna nome do grupo de codigo pedido
   Entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo de que procurar o nome
   Saida:   nome do grupo
*/
function RetornaNomeGrupo($sock,$cod_grupo)
{
  $query="select nome from Grupos where status <> 'X' and cod_grupo=".$cod_grupo;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha['nome']);
}

/* *******************************************************************
   GruposFechados -  Verifica se os grupos estao fechados ou nao
   Entradas : $sock - sock de conexao,

   Saida:     true - se os grupos estiverem travados
              false - se os grupos n� estiverem travados
*/
function GruposFechados($sock)
{
  $query = "select status from Grupos where status='T' limit 1";
  $res   = Enviar($sock,$query);

  $num   = RetornaNumLinhas($res);

  if ($num > 0)
    return(true);
  else
    return(false);
}

/* *******************************************************************
   RetornaUsuariosNoGrupo - retorna array com codigo dos usuarios no grupo pedido
   entrada: $sock - sock de conexao
            $cod_grupo - codigo do grupo do qual listar os cod_usuarios
   saida:   array []['cod_usuario'] - codigo dos usuarios naquele grupo
                  []['nome'] - nome do usuario
                  []['tipo_usuario'] - se usuario aluno ou formador
*/
function RetornaUsuariosNoGrupo($sock,$cod_grupo)
{
  /* todos os usuarios */
  $query1="select cod_usuario,nome,tipo_usuario from Usuario where (binary tipo_usuario = 'A' or binary tipo_usuario = 'F') and cod_usuario >= 0 order by tipo_usuario,nome";
  $res1=Enviar($sock,$query1);
  $lista1=RetornaArrayLinhas($res1);

  /* usuarios naquele grupo */
  $query2="select cod_usuario from Grupos_usuario where cod_grupo=".$cod_grupo;
  $res2=Enviar($sock,$query2);
  $lista2=RetornaArrayLinhas($res2);

  unset($lista_retorno);
  if (count($lista2) > 0)
  {
    unset($array_aux);
    foreach ($lista2 as $cod => $linha)
    {
      /* array-aux[x]==true significa que usuario x est�naquele grupo */
      $array_aux[$linha['cod_usuario']]=true;
    }
    $c=0;
    if (count($lista1) > 0)
      foreach ($lista1 as $cod => $linha)
      {
        if (isset($array_aux[$linha['cod_usuario']]))
        {
          $lista_retorno[$c++]=$linha;
        }
      }
  }
 return ($lista_retorno);
}

/* ************************************************************************
   RetornaExpressaoMedia - Retorna informa��es para c�lculo da m�dia (expressao, norma e data)
   Entrada: $sock - BASE EXTERNA
   Sa�da: �ltimo registro gravado com informa��es de c�lculo da m�dia
*/
function RetornaInformacoesMedia($sock)
{
  $query="select * from Avaliacao_expressao order by data desc limit 1";
  $res=Enviar($sock, $query);
  $linha=RetornaLinha($res);

  return $linha;
}

/* ************************************************************************
   GravarExpressaoMedia - Seleciona o tipo da quest�o
   Entrada: $sock - BASE EXTERNA
            $expressao - Express�o de c�lculo da m�dia a ser gravada no banco de dados
            $norma - Valor para normalizar o c�lculo da m�dia
            $tipo_compartilhamento - Tipo de compartilhamento da m�dia
   Sa�da: true, se tudo ocrreu sem problemas, false, caso contr�rio
*/
function GravarExpressaoMedia($sock, $expressao, $norma, $tipo_compartilhamento)
{
  $query="insert into Avaliacao_expressao(expressao, norma, data, tipo_compartilhamento) values ('".$expressao."', '".$norma."', '".time()."','".$tipo_compartilhamento."')";
  $res=Enviar($sock, $query);

  if ($res)
    return true;

  return false;
}

/*
 ****************************************************************************************************
****************************************************************************************************
/*AQUI PARA FACILITAR FORAM COPIADAS ALGUMAS FUN�OES DA PARTE DE EXERCICIO PARA QUE FOSSEM USADAS NA 
CorrigeExercicioNaoEntregueAvaliacao()
****************************************************************************************************
****************************************************************************************************
*/


/* ************************************************************************
   TipoQuestaoAvaliacao - Seleciona o tipo da quest�
   Entrada: $sock - BASE EXTERNA
            $cod_quest - c�igo da quest�
   Sa�a: tipo da quest�
*/
function TipoQuestaoAvaliacao($sock,$cod_quest)
{
  $consulta="select tp_questao from Exercicios_questao where cod_quest=".$cod_quest;
  $res=Enviar($sock,$consulta);
  $linha=RetornaLinha($res);
  return($linha['tp_questao']);
}


/* **************************************************************
   AlternativasObjetivaAvaliacao - Retorna informa�es sobre as alternativas de
    determinada questao do tipo objetiva (multipla escolha, associativa e verdadeiro/falso)
   Entrada: $sock - sock de conexao
            $cod_quest - codigo da questao
            $gabarito - indica se deve retornar tamb� a resposta das alternativas
   Saida  : $array contendo as alternativas da questao
*/
  function AlternativasObjetivaAvaliacao($sock,$cod_quest,$gabarito)
  {
    if ($gabarito)
      $consulta = "SELECT cod_alternativa,alternativa,cod_resposta FROM Exercicios_alternativa_objetiva WHERE cod_quest=".$cod_quest." ORDER BY posicao_item";
    else
      $consulta = "SELECT cod_alternativa,alternativa FROM Exercicios_alternativa_objetiva WHERE cod_quest=".$cod_quest." ORDER BY posicao_item";
    $res = Enviar($sock,$consulta);
    return(RetornaArrayLinhas($res));
  }


/* *********************************************************************
   QuestoesModeloAvaliacao - Retorna os codigos das questoes que fazem parte do modelo
   Entrada: $sock - BASE DO CURSO
            $cod_modelo - c�igo do modelo
   Saida: array contendo os codigos das questoes
 */
function QuestoesModeloAvaliacao($sock,$cod_modelo)
{
  $consulta = "select t1.cod_quest,t1.cod_topico, t1.enunciado, t1.tp_questao, t2.valor";
  $consulta .= " from Exercicios_questao as t1, Exercicios_questao_modelo as t2 WHERE";
  $consulta .= " t2.cod_modelo=".$cod_modelo." and t1.cod_quest = t2.cod_quest";
  $consulta .= " and t2.status='F' order by t2.posicao_item";
  $res=Enviar($sock,$consulta);
  return(RetornaArrayLinhas($res));
}

/* *********************************************************************
   RetornaNotaQuestaoAvaliacao - Retorna a nota da quest� que o aluno recebeu
   Entrada: $sock - BASE DO CURSO
            $cod_quest - codigo da questao
            $cod_resolucao - codigo da resolucao
   Saida: nota do aluno na questao
 */
function RetornaNotaQuestaoAvaliacao($sock,$cod_quest,$cod_resolucao,$tipo_questao)
{
  $tipo_questao = TipoQuestaoAvaliacao($sock,$cod_quest);
  switch($tipo_questao)
  {
    case 1: /*Dissertativa*/
      $consulta = "SELECT distinct nota FROM Exercicios_quest_res_dissertativa WHERE cod_resolucao=".$cod_resolucao." and cod_quest=".$cod_quest;
      $res = Enviar($sock,$consulta);
      $nota = RetornaLinha($res);
      return($nota['nota']);
      break;
    case 5: /*Lacunas*/
      //$consulta = "SELECT distinct nota FROM Exercicios_quest_res_lacuna WHERE cod_resolucao=".$cod_resolucao." and cod_quest=".$cod_quest;
      break;
    default:
      /*melhorar!!!*/
      $alternativas = AlternativasObjetivaAvaliacao($sock,$cod_quest,true);
      if (count($alternativas) > 0)
      {
        $notas = array();
        foreach($alternativas as $cod=>$dados)
        {
           $consulta = "SELECT nota FROM Exercicios_quest_res_objetiva WHERE cod_resolucao=".$cod_resolucao." and cod_quest=".$cod_quest." and cod_alternativa=".$dados['cod_alternativa'];
           $res = Enviar($sock,$consulta);
           $nota = RetornaLinha($res);
           $notas[] = $nota['nota'];
        }
      }
      return((round(array_sum($notas)*10))/10);
  }
}

/********************************************************************
   RetornaQuestaoDissertativaNulaAvaliacao - Retorna o numero de alternativas
   nulas em uma questao dissertativa.

   Entrada: $sock - BASE DO CURSO
            $cod_questao - c�digo da quest�o
            $cod_alternartiva - c�digo da alternativa
            $cod_resolu��o - c�digo da resolu��o
*/
function RetornaQuestaoDissertativaNulaAvaliacao($sock, $cod_resolucao, $cod_questao)
{
  $consulta="select * from Exercicios_quest_res_dissertativa where cod_resolucao=".$cod_resolucao." and cod_quest=".$cod_questao;
  $res=Enviar($sock,$consulta);
  return RetornaNumLinhas($res);
}

/* ********************************************************************
   RetornaQuestaoDissertativaAvaliacao - Retorna numero de alternativas numa
   quest�o dissertativa

   Entrada: $sock - BASE DO CURSO
            $cod_questao - c�digo da quest�o
            $cod_alternartiva - c�digo da alternativa
            $cod_resolu��o - c�digo da resolu��o
*/
function RetornaQuestaoDissertativaAvaliacao($sock, $cod_resolucao, $cod_questao)
{
  $consulta="select * from Exercicios_quest_res_dissertativa where cod_resolucao=".$cod_resolucao." and cod_quest=".$cod_questao." and resposta IS NULL";
  $res=Enviar($sock,$consulta);
  return RetornaNumLinhas($res);
}

/* ********************************************************************
   AtualizarNotaDissertativaAvaliacao - Atualiza a nota de uma quest� dissertativa para $nota.
   Todas as alternativas da quest� ter� a mesma nota
   Entradas:$sock - BASE EXTERNA
            $cod_resolucao - codigo da resolucao
            $cod_quest - codigo da questao
            $nota - nota nova
   Saida:
 */
function AtualizarNotaDissertativaAvaliacao($sock,$cod_resolucao,$cod_quest,$nota)
{
  $consulta = "update Exercicios_quest_res_dissertativa set nota=".$nota." WHERE cod_resolucao=".$cod_resolucao." and cod_quest=".$cod_quest;
  $res = Enviar ($sock,$consulta);
}

/* *********************************************************************
   RespostaValorQuestaoAvaliacao - Retorna o valor m�imo que uma questao do modelo possui
   Entradas:$sock - BASE EXTERNA
            $cod_modelo - codigo do modelo
            $cod_quest - codigo da questao
            Saida:
*/
function RetornaValorQuestaoAvaliacao($sock,$cod_modelo,$cod_quest)
{
  $consulta = "SELECT valor from Exercicios_questao_modelo where cod_modelo=".$cod_modelo." and cod_quest=".$cod_quest;
  $res = Enviar($sock,$consulta);
  return(RetornaLinha($res));
}

/* *********************************************************************
   RespostaAlternativaObjetivaAvaliacao - Retorna a resposta da alternativa de uma questao objetiva
   Entradas:$sock - BASE EXTERNA
            $cod_resolucao - codigo da resolucao
            $cod_quest - codigo da questao
            $cod_alternativa - codigo da alternativa
   Saida:
 */
function RespostaAlternativaObjetivaAvaliacao($sock,$cod_quest,$cod_resolucao,$cod_alternativa)
{
  $consulta = "Select cod_alternativa,resposta FROM Exercicios_quest_res_objetiva WHERE cod_resolucao=".$cod_resolucao." and cod_quest=".$cod_quest." and cod_alternativa=".$cod_alternativa;
  $res = Enviar($sock,$consulta);
  return(RetornaLinha($res));
}

/* *********************************************************************
   AtualizarNotaAlternativaObjetivaAvaliacao - Atualiza a nota de uma alternativa de uma questao
   Entradas:$sock - BASE EXTERNA
            $cod_resolucao - codigo da resolucao
            $cod_quest - codigo da questao
            $cod_alternativa - codigo da alternativa
            $nota - nota a ser atualizada
   Saida:
*/
function AtualizarNotaAlternativaObjetivaAvaliacao($sock,$cod_resolucao,$cod_quest,$cod_alternativa,$nota)                                                     {
  $consulta = "update Exercicios_quest_res_objetiva set nota=".$nota." WHERE cod_resolucao=".$cod_resolucao." and cod_quest=".$cod_quest." and cod_alternativa=".$cod_alternativa;
  $res = Enviar ($sock,$consulta);
}


/* ********************************************************************
   AtualizarNotaObjetivaAvaliacao - Atualizar as notas das alternativas dependendo do valor da questao
   Entradas:$sock - BASE EXTERNA
            $cod_resolucao - codigo da resolucao
            $cod_quest - codigo da questao
            $tipo - tipo da questao
            $cod_modelo - codigo do modelo
   Saida:
 */
function AtualizarNotaObjetivaAvaliacao($sock,$cod_resolucao,$cod_quest,$tipo,$cod_modelo)
{
  $valor = RetornaValorQuestaoAvaliacao($sock,$cod_modelo,$cod_quest);

  if ($tipo == 2) /*questao multipla escolha*/
  {
    /* array contendo os codigos das alternativas da questao e o codigo de resposta*/
    $consulta = "SELECT * FROM Exercicios_alternativa_objetiva WHERE cod_quest=".$cod_quest;
    //$consulta .= " and cod_resposta=1 ORDER BY posicao_item";
    $consulta .= " ORDER BY posicao_item";
    $res = Enviar($sock,$consulta);
    $alternativas = RetornaArrayLinhas($res);
  }
  else /*questoes verdadeiro/falso e associativa*/
  {
    /* array contendo os codigos das alternativas da questao e o codigo de resposta*/
    $alternativas = AlternativasObjetivaAvaliacao($sock,$cod_quest,true);
  }


  $quantidade = count($alternativas);

  if ($quantidade > 0)
  {
    /* calcular o valor de cada alternativa */
    if ($tipo == 3)
      $valor_alternativa = $valor['valor'] / ($quantidade / 2);
    else
      $valor_alternativa = $valor['valor'] / $quantidade;

    foreach($alternativas as $cod=>$dados)
    {
      $resposta = RespostaAlternativaObjetivaAvaliacao($sock,$cod_quest,$cod_resolucao,$dados['cod_alternativa']);

      if ($resposta['resposta'] == $dados['cod_resposta'])
      {
        AtualizarNotaAlternativaObjetivaAvaliacao($sock,$cod_resolucao,$cod_quest,$dados['cod_alternativa'],$valor_alternativa);
      }
      else
        AtualizarNotaAlternativaObjetivaAvaliacao($sock,$cod_resolucao,$cod_quest,$dados['cod_alternativa'],0);
    }
  }
}

/* *********************************************************************
   AtualizarNotaResolucaoAvaliacao - Atualiza a nota total de determinada resolucao
   Entradas:$sock - BASE EXTERNA
            $cod_resolucao - codigo da resolucao
            $cod_modelo
            $cod_aluno - codigo do usuario que est�sendo avaliado
   Saida:
 */
function AtualizaNotaResolucaoAvaliacao($sock,$cod_resolucao,$cod_modelo)
{
  $questoes = QuestoesModeloAvaliacao($sock,$cod_modelo);
  $notas[]=0;
  if (count($questoes)>0)
  {
    foreach($questoes as $cod => $dados)
    {
      $nota = RetornaNotaQuestaoAvaliacao($sock,$dados['cod_quest'],$cod_resolucao, $dados['tp_questao']);
      if ( ($nota != -1.000))
        $notas[]=$nota;
    }
  }
  $total = (round(array_sum($notas)*100))/100;

  /*Atualizar campo nota na tabela Exercicios_resolucao*/
  $consulta = "update Exercicios_resolucao set nota=".$total." where cod_resolucao=".$cod_resolucao;
  $res = Enviar($sock,$consulta);

  $consulta = "select cod_usuario,cod_grupo from Exercicios_resolucao where cod_resolucao=".$cod_resolucao;
  $res = Enviar($sock,$consulta);
  $codigos = RetornaLinha($res);

  $consulta="select cod_avaliacao from Avaliacao where cod_atividade=".$cod_modelo." and ferramenta='E'";
  $res=Enviar($sock, $consulta);
  $cod_avaliacao=RetornaLinha($res);

  if ($cod_avaliacao['cod_avaliacao']!=null)
  {
    $consulta="update Avaliacao_notas set nota=".$total." where cod_avaliacao=".$cod_avaliacao['cod_avaliacao'];
    /*Consist�ncia para mudar nota para o grupo todo */
    if ($codigos['cod_grupo']==null || $codigos['cod_grupo']==0)
    {
       $consulta.=" and cod_aluno=".$codigos['cod_usuario'];
       $consulta.=" and cod_grupo='".$codigos['cod_grupo']."'";
    }
    /* Algumas mudan�as aqui */
    else
       $consulta.=" and cod_grupo=".$codigos['cod_grupo'];
    $res=Enviar($sock, $consulta);
  }
}


/* ************************************************************************
   SelecionaExercicios - Seleciona os exerc�ios do usu�io/grupo de c�igo $cod_usuario
   Entrada: $cod_usuario - c�igo do usu�io/grupo
            $view - tipo de visualiza�o
            $permissao - permissao do usuario que esta acessando
            $data - identifica exercicios com data limite de submissao ou nao
   Sa�a: query para selecionar os exercicios
*/
function SelecionaExercicios($cod_usuario, $view, $permissao, $data)
{
  if ($view == "i")
  {
    $strSQL = "select Exercicios_modelo.titulo, Exercicios_modelo.cod_usuario as dono_modelo, Exercicios_resolucao.cod_modelo, Usuario.nome, ";
    $strSQL.= "Exercicios_aplicado.dt_limite_submissao, Exercicios_resolucao.compartilhada, ";
    $strSQL.= "Exercicios_aplicado.avaliacao, Exercicios_resolucao.status,Exercicios_aplicado.objetiva, ";
    $strSQL.= "Exercicios_aplicado.dt_disponibilizacao, Usuario.cod_usuario, Exercicios_resolucao.cod_resolucao from Usuario, ";
    $strSQL.= "Exercicios_resolucao, Exercicios_aplicado, Exercicios_modelo WHERE ";
    $strSQL.= "(Exercicios_resolucao.cod_modelo=Exercicios_aplicado.cod_modelo AND ";
    $strSQL.= "Exercicios_aplicado.tp_aplicacao='".$view."' AND ";
    $strSQL.= "Exercicios_resolucao.cod_modelo=Exercicios_modelo.cod_modelo) AND ";
    $strSQL.= "(Usuario.cod_usuario=Exercicios_resolucao.cod_usuario AND Usuario.cod_usuario='".$cod_usuario."') ";

    if ($permissao == "compartilhados")
    {
      $strSQL.= "AND (Exercicios_resolucao.compartilhada='T') ";
    }

    if ($data == "nao")
    {
      /*seleciona exercicios sem data limite de submissao*/
      $strSQL.= "AND Exercicios_aplicado.dt_limite_submissao='0' ";
    }
    else
    {
      /*seleciona todos menos os sem data limite de submissao*/
      $strSQL.= "AND Exercicios_aplicado.dt_limite_submissao!='0' ";
    }

    $strSQL.= "ORDER BY Exercicios_aplicado.dt_limite_submissao desc";

  }
  else
  {
    $strSQL = "select Exercicios_modelo.titulo, Exercicios_modelo.cod_usuario as dono_modelo, Exercicios_resolucao.cod_modelo, Grupos.nome, ";
    $strSQL.= "Exercicios_aplicado.dt_limite_submissao, Exercicios_resolucao.compartilhada,Exercicios_aplicado.objetiva, ";
    $strSQL.= "Exercicios_aplicado.avaliacao, Exercicios_resolucao.status, Exercicios_aplicado.dt_disponibilizacao, ";
    $strSQL.= "Grupos_usuario.cod_grupo, Exercicios_resolucao.cod_resolucao from Grupos, Grupos_usuario, Exercicios_resolucao, Exercicios_aplicado, ";
    $strSQL.= "Exercicios_modelo where (Exercicios_resolucao.cod_modelo=Exercicios_aplicado.cod_modelo AND ";
    $strSQL.= "Exercicios_aplicado.tp_aplicacao='G' AND ";
    $strSQL.= "Exercicios_resolucao.cod_modelo=Exercicios_modelo.cod_modelo) AND ";

    $strSQL.= "(Grupos.cod_grupo=Exercicios_resolucao.cod_grupo AND ";
    $strSQL.= "Grupos.cod_grupo='".$cod_usuario."' AND Grupos.cod_grupo=Grupos_usuario.cod_grupo ";

    if ($permissao == compartilhados)
    {
      $strSQL.= " AND Exercicios_resolucao.compartilhada='T'";
    }

    if ($data == "nao")
    {  /*seleciona exercicios sem data limite de submissao*/
      $strSQL.= "AND Exercicios_aplicado.dt_limite_submissao='0' ";
    }
    else
    { /*seleciona todos menos os sem data limite de submissao*/
      $strSQL.= "AND Exercicios_aplicado.dt_limite_submissao!='0' ";
    }    

    $strSQL.= ") Group By Exercicios_resolucao.cod_resolucao ORDER BY Exercicios_aplicado.dt_limite_submissao desc";

  }
  return $strSQL;
}

/* *********************************************************************
   RetornaDadosAplicadoAvaliacao - Retorna um array com os dados do modelo aplicado
   Entrada: $sock - BASE DO CURSO
            $cod_modelo - c�igo da modelo
   Saida: $linha - array com o modelo selecionado

 */ 
  function RetornaDadosAplicadoAvaliacao($sock, $cod_modelo)
  {
    $consulta = "select * from Exercicios_aplicado where cod_modelo=".$cod_modelo;
    $res = Enviar($sock, $consulta);

    $linha=RetornaLinha($res);

    return($linha);
  }

/* ********************************************************************
   RetornaResolucaoAvaliacao - Retorna o codigo e o status da resolu�o do modelo que pertence a $cod_dono
   Entrada: $sock - BASE DO CURSO
            $cod_modelo - codigo do modelo
            $cod_dono - codigo do dono da resolucao
   Saida: $linha - codigo da resolucao e o status dela
*/
function RetornaResolucaoAvaliacao($sock,$cod_modelo,$cod_dono)
{
  //  Verificamos que tipo de aplica�o o modelo tem (individual ou grupo)
  $dados_modelo = RetornaDadosAplicadoAvaliacao($sock,$cod_modelo);
  //Aplicacao Individual 
  if ($dados_modelo['tp_aplicacao'] == 'I')
  {
    $consulta ="select * from Exercicios_resolucao where cod_modelo=".$cod_modelo." and cod_usuario=".$cod_dono;
  }
  else //Aplicacao de Grupo/
  {
    $consulta = "select * from Exercicios_resolucao where cod_modelo=".$cod_modelo." and cod_grupo=".$cod_dono;
  }
  $res=Enviar($sock,$consulta);
  $linha=RetornaLinha($res);
  return($linha);
}


/* *********************************************************************
   AtualizarStatusResolucaoAvaliacao -  atualiza o status de uma resolucao. Quando
   cod_resolucao for igual a -1, atualizar todas as resolucoes do modelo.
   Entrada: $sock - BASE DO CURSO
            $cod_modelo - codigo do modelo
            $cod_resolucao - codigo da resolucao a ser atualizada
            $novo_status -  novo a status a ser atualizado
   Saida: true ou false, indicando se a atualiza�o na tabela foi bem sucedida
 */
function AtualizarStatusResolucaoAvaliacao($sock,$cod_modelo,$cod_resolucao,$novo_status)
{
  if ($cod_resolucao == -1)
  {
    if (($novo_status=='G')||($novo_status=='N'))
    {
      $consulta="select cod_avaliacao from Avaliacao where cod_atividade=".$cod_modelo." and ferramenta='E'";
      $res=Enviar($sock, $consulta);
      // $cod_avaliacao=RetornaLinha($res);
      // if ($cod_avaliacao > 0) {
      if ($cod_avaliacao=RetornaLinha($res))
      {
        $select ="select cod_usuario,cod_grupo,nota from Exercicios_resolucao where cod_modelo=".$cod_modelo;
        $res=Enviar($sock,$select);
        $usuarios=RetornaArrayLinhas($res);

        if ((count($cod_avaliacao)>0)&&(!is_null($cod_avaliacao['cod_avaliacao'])) && (count($usuarios) > 0))
        {
          foreach($usuarios as $cod => $usuario)
          {
            if (!is_null($usuario['cod_grupo']))
            {
              $consulta="update Avaliacao_notas set nota=".$usuario['nota']." where cod_grupo='".$usuario['cod_grupo']."' and cod_avaliacao=".$cod_avaliacao['cod_avaliacao'];
              $res=Enviar($sock, $consulta);
            }
            else
            {
              $consulta="update Avaliacao_notas set nota=".$usuario['nota']." where cod_aluno='".$usuario['cod_usuario']."' and cod_grupo='".$usuario['cod_grupo']."' and cod_avaliacao=".$cod_avaliacao['cod_avaliacao'];
              $res=Enviar($sock, $consulta);
            }
          }
        }
      }
      $consulta = "update Exercicios_resolucao set status='".$novo_status."' where cod_modelo=".$cod_modelo;
      $res=Enviar($sock,$consulta);
    }
  }
  else
  {
    $consulta="update Exercicios_resolucao set status='".$novo_status."' where cod_resolucao=".$cod_resolucao;
    $res=Enviar($sock,$consulta);
    if ($novo_status=='S') {
      $consulta = "update Exercicios_resolucao set submetida='S' where cod_resolucao=".$cod_resolucao;
      $res=Enviar($sock,$consulta);
    }
    else if (($novo_status=='G') || ($novo_status=='N')) {
      $consulta = "update Exercicios_resolucao set corrigida='S' where cod_resolucao=".$cod_resolucao;
      $res=Enviar($sock,$consulta);
    }
    $consulta="select cod_avaliacao from Avaliacao where cod_atividade=".$cod_modelo." and ferramenta='E'";
    $res=Enviar($sock, $consulta);
    $cod_avaliacao=RetornaLinha($res);
    if ((count($cod_avaliacao)>0)&&(!is_null($cod_avaliacao['cod_avaliacao']))) {
      $select ="select cod_usuario,cod_grupo,nota from Exercicios_resolucao where cod_resolucao=".$cod_resolucao;
      $res=Enviar($sock,$select);
      $usuario=RetornaLinha($res);
      if (!is_null($usuario['cod_grupo'])) {
        $consulta="update Avaliacao_notas set nota=".$usuario['nota']." where cod_grupo='".$usuario['cod_grupo']."' and cod_avaliacao=".$cod_avaliacao['cod_avaliacao'];
        $res=Enviar($sock, $consulta);
      }
      else
      {
        $consulta="update Avaliacao_notas set nota=".$usuario['nota']." where cod_aluno='".$usuario['cod_usuario']."' and cod_grupo='".$usuario['cod_grupo']."' and cod_avaliacao=".$cod_avaliacao['cod_avaliacao'];
        $res=Enviar($sock, $consulta);
      }
    }
  }
  if (!$res)
    return false;
  else
    return true;
}

/* *********************************************************************
   CorrigeExercicioNaoEntregueAvaliacao - Corrigi automaticamente exercicios que ja passaram da data de entrega e naum foram submetidos 

   Entrada:
        $sock - BASE DO CURSO
        $data - data limite de submiss�o do exercicio
        $status_resolucao - status da resolu��o
        $cod_modelo - c�digo do modelo
        $cod_resolucao - c�digo da resolu��o
        $tp_gabarito - tipo do gabarito
   Saida:
        0 -> n�o mudou nada
        1 -> fez altera��es
*/
function CorrigeExercicioNaoEntregueAvaliacao($sock, $data, $status_resolucao, $cod_modelo, $cod_resolucao)
{
  $contador=0;
  $data_atual=time();
  if (($data < $data_atual) && ($status_resolucao!='G' && $status_resolucao!='N'))
  {
    if ($cod_modelo)
      $questoes = QuestoesModeloAvaliacao($sock,$cod_modelo);
    else
    {
      $questoes=array();
    }
    if (count($questoes)>0)
    {
      foreach($questoes as $cod => $dados)
      {
        $nota = RetornaNotaQuestaoAvaliacao($sock,$dados['cod_quest'],$cod_resolucao, $dados['tp_questao']);
        if ($dados['tp_questao']==1)
        {
          if (RetornaQuestaoDissertativaAvaliacao($sock,$cod_resolucao, $dados['cod_quest'])==RetornaQuestaoDissertativaNulaAvaliacao($sock,$cod_resolucao, $dados['cod_quest']))
          {
            AtualizarNotaDissertativaAvaliacao($sock,$cod_resolucao,$dados['cod_quest'],0);
            $contador=$contador+1;
          }
        }
        else
        {
          if ($nota<0)
          {
            AtualizarNotaObjetivaAvaliacao($sock,$cod_resolucao,$dados['cod_quest'],$dados['tp_questao'],$cod_modelo);
          }
          $contador=$contador+1;
        }
      }/*for*/
      if ($contador>0)
      {
        AtualizaNotaResolucaoAvaliacao($sock,$cod_resolucao,$cod_modelo);
        if ($contador==count($questoes))
        {
          $dados_aplicacao=RetornaDadosAplicadoAvaliacao($sock, $cod_modelo);
          if ($dados_aplicacao['tp_gabarito'] == 'S')
            AtualizarStatusResolucaoAvaliacao($sock,$cod_modelo,$cod_resolucao,'G');
          else
            AtualizarStatusResolucaoAvaliacao($sock,$cod_modelo,$cod_resolucao,'N');
        }
        //$resolucao = RetornaResolucao($sock, $cod_modelo, $cod_dono);
      }/*if*/
   }/*if*/
    else
      return 0;

    return 1;
  }/*if*/
  else
  {
    return 0;
  }
}

/* ******************************************************************************************
    ListaCoordenadorAvaliacao - Retorna uma lista com o c�digo e o nome do coordenador do curso
    Entradas: $sock - sock de conexao,
             $cod_curso - codigo do curso,
    Saida:   Array com: ['cod_usuario'] - codigo usuario
                        ['nome'] - nome
*/
function ListaCoordenadorAvaliacao($sock, $cod_curso)
{
  $query = "select cod_coordenador from Cursos where cod_curso = ".$cod_curso;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  $query = "select U.cod_usuario, U.nome, Cf.valor 'status' from Usuario as U, Config as Cf where";
  $query .= " U.cod_usuario = ".$linha['cod_coordenador']." and Cf.item = 'status_coordenador'";
  $res = Enviar($sock,$query);

  $linha = RetornaLinha($res);

  return ($linha);
}

/* *********************************************************************
   RetornaAvaliacoesExercicios - Retorna array com as todas as avaliacoes relacionadas a exercicios
   Entrada: $sock - BASE DO CURSO
            $usr_formador
   Saida: array com []['cod_Avaliacao']
                    []['Ferramenta']
                    []['Data_inicio']
                    []['Data_termino']
                    []['Titulo']
                    []['Tipo'] - individual ou Em Grupo/
*/
function RetornaAvaliacoesExercicios($sock, $usr_formador)
{

  $consulta="select cod_avaliacao,cod_atividade,valor,tipo,data,data_inicio,data_termino from Avaliacao where status='F' or status='E' and ferramenta='E' and data_termino<".time()." order by data_inicio asc";
  $res=Enviar($sock, $consulta);
  $lista=RetornaArrayLinhas($res);
  unset($retorno);
  $cont=0;
  if (RetornaNumLinhas($res) > 0)
  {
    foreach ($lista as $cod => $linha)
    { 
      $consulta="select titulo from Exercicios_modelo where cod_exercicio=".$linha['cod_atividade'];

      $res=Enviar($sock, $consulta);
      $titulo=RetornaLinha($res);
      if (($titulo!="")&&(!is_null($titulo)))
      {
        $retorno[$cont]['Cod_avaliacao']=$linha['cod_avaliacao'];
        $retorno[$cont]['Ferramenta']=$linha['ferramenta'];
        $retorno[$cont]['Valor']=$linha['valor'];
        $retorno[$cont]['Data']=$linha['data'];
        $retorno[$cont]['Data_inicio']=$linha['data_inicio'];
        $retorno[$cont]['Data_termino']=$linha['data_termino'];
        $retorno[$cont]['Titulo']=$titulo[0];
        $retorno[$cont]['tipo']  = $linha['tipo'];
        $retorno[$cont]['Cod_atividade']=$linha['cod_atividade'];
        $cont++;
      }
    }
  }
  return $retorno;
}


/* ******************************************************************************************
   VerificaExerciciosNaoCorrigidos - Verifica se h�xerc�os n�corrigidos no curso
   Entradas: $sock - sock de conexao
   Saida: 1 se existem exercicios n�corrigidos, 0 se n�existem
*/
function VerificaExerciciosNaoCorrigidos($sock)
{
  $query = "select * from Exercicios_resolucao where status!='G' and status!='N'";
  $res=Enviar($sock, $query);

  if (RetornaNumLinhas($res) > 0) {
    return 1;
  }

  return 0;
}

/* ************************************************************************
   SelecionaExerciciosNaoCorrigidos - Seleciona os exercicios do usuario/grupo de codigo $cod_usuario
   Entrada: $cod_usuario - codigo do usuario/grupo
            $view - tipo de visualizacao
            $permissao - permissao do usuario que esta acessando
            $data - identifica exercicios com data limite de submissao ou nao
   Saida: query para selecionar os exercicios
*/
function SelecionaExerciciosNaoCorrigidos($cod_usuario, $view, $permissao, $data)
{
  if ($view == "i")
  {
    $strSQL = "select Exercicios_modelo.titulo, Exercicios_modelo.cod_usuario as dono_modelo, Exercicios_resolucao.cod_modelo, Usuario.nome, ";
    $strSQL.= "Exercicios_aplicado.dt_limite_submissao, Exercicios_resolucao.compartilhada, ";
    $strSQL.= "Exercicios_aplicado.avaliacao, Exercicios_resolucao.status,Exercicios_aplicado.objetiva, ";
    $strSQL.= "Exercicios_aplicado.dt_disponibilizacao, Usuario.cod_usuario, Exercicios_resolucao.cod_resolucao from Usuario, ";
    $strSQL.= "Exercicios_resolucao, Exercicios_aplicado, Exercicios_modelo WHERE ";
    $strSQL.= "(Exercicios_resolucao.cod_modelo=Exercicios_aplicado.cod_modelo AND ";
    $strSQL.= "Exercicios_aplicado.tp_aplicacao='".$view."' AND ";
    $strSQL.= "Exercicios_resolucao.cod_modelo=Exercicios_modelo.cod_modelo) AND ";
    $strSQL.= "(Usuario.cod_usuario=Exercicios_resolucao.cod_usuario AND Usuario.cod_usuario='".$cod_usuario."') ";

    $strSQL.= "and Exercicios_resolucao.status!='G' and Exercicios_resolucao.status!='N' ";

    if ($permissao == "compartilhados")
    {
      $strSQL.= "AND (Exercicios_resolucao.compartilhada='T') ";
    }

    if ($data == "nao")
    {
      /*seleciona exercicios sem data limite de submissao*/
      $strSQL.= "AND Exercicios_aplicado.dt_limite_submissao='0' ";
    }
    else
    {
      /*seleciona todos menos os sem data limite de submissao*/
      $strSQL.= "AND Exercicios_aplicado.dt_limite_submissao!='0' ";
    }

    $strSQL.= "ORDER BY Exercicios_aplicado.dt_limite_submissao desc";

  }
  else
  {
    $strSQL = "select Exercicios_modelo.titulo, Exercicios_modelo.cod_usuario as dono_modelo, Exercicios_resolucao.cod_modelo, Grupos.nome, ";
    $strSQL.= "Exercicios_aplicado.dt_limite_submissao, Exercicios_resolucao.compartilhada,Exercicios_aplicado.objetiva, ";
    $strSQL.= "Exercicios_aplicado.avaliacao, Exercicios_resolucao.status, Exercicios_aplicado.dt_disponibilizacao, ";
    $strSQL.= "Grupos_usuario.cod_grupo, Exercicios_resolucao.cod_resolucao from Grupos, Grupos_usuario, Exercicios_resolucao, Exercicios_aplicado, ";
    $strSQL.= "Exercicios_modelo where (Exercicios_resolucao.cod_modelo=Exercicios_aplicado.cod_modelo AND ";
    $strSQL.= "Exercicios_aplicado.tp_aplicacao='G' AND ";
    $strSQL.= "Exercicios_resolucao.cod_modelo=Exercicios_modelo.cod_modelo) AND ";

    $strSQL.= "(Grupos.cod_grupo=Exercicios_resolucao.cod_grupo AND ";
    $strSQL.= "Grupos.cod_grupo='".$cod_usuario."' AND Grupos.cod_grupo=Grupos_usuario.cod_grupo ";

    $strSQL.= "and Exercicios_resolucao.status!='G' and Exercicios_resolucao.status!='N' ";

    if ($permissao == compartilhados)
    {
      $strSQL.= " AND Exercicios_resolucao.compartilhada='T'";
    }

    if ($data == "nao")
    { 
      /*seleciona exercicios sem data limite de submissao*/
      $strSQL.= "AND Exercicios_aplicado.dt_limite_submissao='0' ";
    }
    else
    { 
      /*seleciona todos menos os sem data limite de submissao*/
      $strSQL.= "AND Exercicios_aplicado.dt_limite_submissao!='0' ";
    }

    $strSQL.= ") Group By Exercicios_resolucao.cod_resolucao ORDER BY Exercicios_aplicado.dt_limite_submissao desc";

  }
  return $strSQL;
}

/* ************************************************************************
   RetornaCodigoGrupoAvaliacao - Retorna o c�digo do grupo olhando apenas para a tabela Avaliacao_notas assim ele retorna o c�digo do grupo em que o aluno estava quando fez a avalia��o
   Entrada:
      $cod_aluno - c�digo do aluno
      $cod_avaliacao - c�digo da avalaicao

   Saida:
         Codigo do grupo
*/
function RetornaCodigoGrupoAvaliacao($sock, $cod_aluno, $cod_avaliacao)
{
  $query="select cod_grupo from Avaliacao_notas where cod_aluno=".$cod_aluno." and cod_avaliacao=".$cod_avaliacao;
  $res=Enviar($sock,$query);
  $codigo=RetornaLinha($res);
  return $codigo['cod_grupo'];
}

/* ************************************************************************
   RetornaListaIntegrantesMomentoAvaliacao - Retorna um array com a lista de integrantes que o grupo tinha no momento em que foi avaliado
 */
function RetornaListaIntegrantesMomentoAvaliacao($sock,$cod_curso,$cod_grupo,$cod_avaliacao)
{
  global $dbnamebase;

  $query="select MAX(data_alteracao) from Avaliacao_notas where cod_grupo=".$cod_grupo." and cod_avaliacao=".$cod_avaliacao;
  $res=Enviar($sock,$query);
  $data=RetornaLinha($res);
  if ($data[0])
  {
    $query="select cod_aluno from Avaliacao_notas where cod_grupo=".$cod_grupo." and cod_avaliacao=".$cod_avaliacao." and data_alteracao<=".$data[0];
    $res=Enviar($sock,$query);
    $codigos=RetornaArrayLinhas($res);
  }
  else
  {
    $query="select GU.cod_usuario from Grupos_usuario GU where GU.cod_grupo=".$cod_grupo;
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);
    $x=0;
    foreach($lista as $cod_aluno => $lista_codigo)
    {
      $codigos[$x]['cod_aluno']=$lista_codigo['cod_usuario'];
      $x++;
    }
  }
  $sock = MudarDB($sock, "");
  foreach ($codigos as $cod => $linha)
  {
    $query = "select U.nome, UC.tipo_usuario, U.cod_usuario, UC.cod_usuario as UCcod_usuario, UC.cod_usuario_global from Usuario U inner join Usuario_curso UC ON (UC.cod_usuario_global = U.cod_usuario) AND (UC.cod_usuario = ".$linha['cod_aluno'].") AND (U.cod_usuario > 0) AND (UC.cod_curso = ".$cod_curso.")";
    $res=Enviar($sock,$query);
    $Dados=RetornaLinha($res);
    $saida[$linha['cod_aluno']]['nome']=$Dados['nome'];
    $saida[$linha['cod_aluno']]['tipo_usuario']=$Dados['tipo_usuario'];
  }

  $sock = MudarDB($sock, $_SESSION['cod_curso']);
  return $saida;
}

/* ************************************************************************
   RetornaProximoCodigoExterna - Retorna o pr�ximo cod_atividade olhando apenas para a tabela Avaliacao
   Entradas:
      $tabela - no caso tem de ser a tabela Avaliacao
      $ferramenta - ferramenta da atividade que se quer o proximo cod_atividade;
   Saida:
      Proximo cod_atividade;
*/
function RetornaProximoCodigoExterna($sock,$ferramenta)
{
  $query="select MAX(codigo) from Avaliacao_externa;"; // where ferramenta='".$ferramenta."'";
  $res=Enviar($sock,$query);
  $dados=RetornaLinha($res);
  $prox_cod=$dados[0];
  $prox_cod++;
  return $prox_cod;
}
/* ************************************************************************
   RetornaDadosDoItemExterna - Retorna um array com os dados do item do portfolio
   Entrada: $sock - BASE DO CURSO
            $cod_topico - tpico
   Saida: array com ['codigo']
                    ['titulo']
                    ['texto']
                    ['tipo_compartilhamento']
                    ['data']
                    ['data_ativo']
                    ['data_inativo']
                    ['posicao_item']
                    ['status']
                    ['inicio_edicao']
*/
function RetornaDadosDoItemExterna ($sock, $cod_item)
{
  $consulta="select * from Avaliacao_externa where codigo=".$cod_item;
  $res=Enviar($sock, $consulta);
  $linha=RetornaLinha($res);
  return($linha);
}

/* ********************************************************************************
   GravaResgistroAvaliacaoExterna - Grava um registro na tabela Nota_externa
   Entrada: $sock - BASE DO CURSO
            $data_inicio
            $data_termino
            $descricao
            $titulo
            $valor
            $status 

            OBS: o status da tabela Nota_externa � apenas g para grupo ou i para individual.
*/
function GravaResgistroAvaliacaoExterna($sock, $titulo, $status)
{
  $query="INSERT INTO `Avaliacao_externa` (`titulo` , `status` ) ".
         "VALUES ('".$titulo."','".$status."')";
  $res=Enviar($sock,$query);
}

/* ********************************************************************************
   RetornaDadosUltimaNotaStatusA - Retorna a nota do aluno selecionado
   Entrada:  $sock - BASE DO CURSO
             $cod - Cdigo do aluno
             $cod_avaliacao - Cdigo da avalia�o
   Saida:  nota do aluno
*/
function RetornaDadosUltimaNotaStatusA($sock, $cod, $cod_avaliacao)
{ 
  $query="select cod_nota, data, data_alteracao, nota, tipo_compartilhamento from Avaliacao_notas where cod_aluno=".$cod." and cod_avaliacao=".$cod_avaliacao." and status='A' order by data desc";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
    return RetornaLinha($res);
  else
    return NULL;
}

/* ********************************************************************************
   MudaStatusAvaliacaoParaF - Seta o campo status de uma avaliacao para 'F'
   Entrada:  $sock - BASE DO CURSO
             $data - data de criacao da avaliacao
   Saida:  nota do aluno
*/
function MudaStatusAvaliacaoParaF($sock,$data)
{ 
  $query="update Avaliacao_notas set status='F' where data=".$data;
  $res=Enviar($sock,$query);
}


/**********************************************************************
   EditarTitulo - Edita o título do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_item - item ao qual o endereco estará associado
            $novo_nome - novo título do item
            $cod_usuario - código do usuário que está utilizando
            $ferramenta - ferramenta a que pertence a avaliacao
            $cod_atividade - codigo da atividade
            $cod_avaliacao - codigo da avaliacao
   Saida: XML da função Ajax
*/
function EditarTitulo($cod_curso, $novo_nome, $cod_usuario, $ferramenta, $cod_atividade, $cod_avaliacao)
{
  $objResponse = new xajaxResponse();
  
  $cod_lingua = (int) $_SESSION['cod_lingua_s'];

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $sock=Conectar($cod_curso);

  if (!strcmp($ferramenta,'B'))
  {
    $query="update Batepapo_assuntos set assunto='".VerificaStringQuery($novo_nome)."' where cod_assunto=".$cod_atividade;
  }
  elseif (!strcmp($ferramenta,'F'))
  {
    $query="update Forum set nome='".VerificaStringQuery($novo_nome)."' where cod_forum=".$cod_atividade;
  }
  elseif (!strcmp($ferramenta,'E'))
  {
    $query="update Exercicios_modelo set titulo='".VerificaStringQuery($novo_nome)."' where cod_exercicio=".$cod_atividade;
  }
  elseif (!strcmp($ferramenta,'N'))
  {
    $query="update Avaliacao_externa set titulo='".VerificaStringQuery($novo_nome)."' where codigo=".$cod_atividade;
  }   
  else
  {
    $query="update Atividade_itens set titulo='".VerificaStringQuery($novo_nome)."' where cod_item=".$cod_atividade;
  }

  $res=Enviar($sock, $query);

  Desconectar($sock);

  // Imprime no div valores do formul?io
  $objResponse->assign("tr_".$cod_atividade, "className", "novoitem");
  $objResponse->assign("tit_".$cod_atividade, "innerHTML", htmlentities($novo_nome));
  //$objResponse->assign("tit_".$cod_atividade, "className", "linkTexto");
  //$objResponse->addEvent("tit_".$cod_atividade, "onClick", "AlteraCampo('".$tag."','".$cod_atividade."');");

  AcabaEdicaoDinamic($cod_curso, $cod_avaliacao, $cod_usuario, 1);
  
   $cod_texto = 220;
   $cod_ferramenta = 22;
   $texto_feedback = RetornaTexto($cod_lingua, $cod_ferramenta, $cod_texto);
   $objResponse->call("mostraFeedback", $texto_feedback, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}


/* *********************************************************************
   RetornaTexto - Retorna uma frase do bando de dados (tabela Lingua_textos)
   Entrada: $cod_lingua - lingua utilizada pelo usuario
            $cod_ferramenta - a que ferramenta pertence o texto
            $cod_texto - codigo do texto a ser devolvido
   Saida: texto desejado
*/
function RetornaTexto($cod_lingua, $cod_ferramenta, $cod_texto){
  $sock = Conectar('');
  $sql = "select texto from Lingua_textos where cod_lingua = $cod_lingua AND cod_ferramenta = $cod_ferramenta AND cod_texto = $cod_texto";
  $resp = Enviar($sock, $sql);
  $texto = RetornaLinha($resp);
  Desconectar($sock);
  return($texto[0]);
}

/* *********************************************************************
   EditarValor - Edita a nota dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $novo_nome - novo título do item
            $cod_usuario - código do usuário que está utilizando
            $ferramenta - ferramenta a que pertence a avaliacao
            $cod_atividade - codigo da atividade
            $cod_avaliacao - codigo da avaliacao
   Saida: XML da função Ajax
*/
function EditarValor ($cod_curso, $novo_nome, $cod_usuario, $ferramenta, $cod_atividade, $cod_avaliacao)
{
  $objResponse = new xajaxResponse();

  $cod_lingua = (int) $_SESSION['cod_lingua_s']; /* Utilizado pra pegar a frase do feedback */
  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  $data = time();

  $novo_nome = FormataNota($novo_nome);

  $sock=Conectar($cod_curso);
  /*Atualiza a nota*/
  $query="update Avaliacao set valor='".VerificaStringQuery($novo_nome)."' where cod_avaliacao=".VerificaNumeroQuery($cod_avaliacao);

  $res=Enviar($sock, $query);

  Desconectar($sock);

  // Imprime no div valores do formul?io
  $objResponse->assign("tr_".$cod_atividade, "className", "novoitem");
  $objResponse->assign("valor_".$cod_atividade, "innerHTML", htmlentities($novo_nome));
  //$objResponse->assign("valor_".$cod_atividade, "className", "linkTexto");
  //$objResponse->addEvent("valor_".$cod_atividade, "onClick", "AlteraNota('".$tag."','".$cod_atividade."');");
 
  AcabaEdicaoDinamic($cod_curso, $cod_avaliacao, $cod_usuario, 1);
  
  /* Feedback é sempre bom né? */
  $cod_ferramenta = 22;
  $cod_texto = 215;
  $texto_feedback = RetornaTexto($cod_lingua, $cod_ferramenta, $cod_texto);
  $objResponse->call("mostraFeedback", $texto_feedback, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* *********************************************************************
   VerificaTexto -Verifica se o texto é composto apenas por caracteres sem valor ("&nbsp;","<br>").
   Entrada: $texto - texto a ser verificado

   Saida: -  O próprio texto ($texto) caso o mesmo contenha caracteres com valor;
          -  "" caso contrário
*/
function VerificaTexto($texto){

  $text = Space2Nbsp($texto);
  $text1 = Enter2BR($text);

  $text2 = explode("<br />",$text1);
  foreach($text2 as $string){
    $string1 = explode("&nbsp;",$string);
    foreach($string1 as $string2){
      $string3 = explode("<br>",$string2);
      foreach($string3 as $pal){
        if ($pal!=""){
          return $texto;
        }
      }
    }
  }

  return "";
}

/* *********************************************************************
   EditarTexto - Edita o texto do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_atividade - codigo da atividade
            $novo_nome - novo texto do item
            $cod_usuario - código do usuário que está utilizando
            $cod_avaliacao - codigo da avaliacao
            $id - id da tr a que pertence ao texto (criterio ou objetivos)
   Saida: XML da função Ajax
*/
function EditarTexto ($cod_curso, $cod_atividade, $novo_nome, $cod_usuario, $cod_avaliacao, $id)
{
  $objResponse = new xajaxResponse();

  $cod_lingua = (int) $_SESSION['cod_lingua_s']; /* Utilizado pra pegar a frase do feedback */

  $novo_nome = VerificaStringQuery($novo_nome);

  /*Atualiza objetivos*/
  if ($id == "text_obj"){
    $cod_texto = 221; /* cod_texto do feedback para essa atualizacao */
    $consulta="update Avaliacao set objetivos='".trim($novo_nome)."' where cod_avaliacao=".$cod_avaliacao;
  }
  /*Atualiza criterios*/
  else {
    $cod_texto = 222; /* cod_texto do feedback para essa atualizacao */
    $consulta="update Avaliacao set criterios='".trim($novo_nome)."' where cod_avaliacao=".$cod_avaliacao;
  }

  /* Feedback é sempre bom né? */
  $cod_ferramenta = 22;
  $texto_feedback = RetornaTexto($cod_lingua, $cod_ferramenta, $cod_texto);
  $objResponse->call("mostraFeedback", $texto_feedback, 'true');

  $sock=Conectar($cod_curso);
  $res=Enviar($sock,$consulta);

  Desconectar($sock);

  AcabaEdicaoDinamic($cod_curso, $cod_avaliacao, $cod_usuario, 1);

  // Imprime no div valores do formulário
  $objResponse->assign("tr_".$cod_atividade, "className", "novoitem");

  $objResponse->assign($id."_".$cod_atividade, "innerHTML", print_r(AjustaParagrafo(ConverteBarraAspas2Aspas($novo_nome)), true));


  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* *********************************************************************
   AdicionaComentarioDinamic - Adiciona comentario feito pelo usuario.
   TODO: Funcao nao terminada -> Falta adicionar chamada pro feedback com o
   valor da mensagem para aparecer.
*/
function AdicionaComentarioDinamic($sock, $cod_avaliacao, $cod_usuario, $comentario)
{
  $objResponse = new xajaxResponse();

  // Seta comentario no banco de dados.
  $query = "insert into Avaliacao_notas_comentarios ";
  $query.= "(cod_avaliacao, cod_usuario, comentario, data) ";
  $query.= "values (".$cod_avaliacao.", ".$cod_usuario;
  $query.= ", '".$comentario."', ".time().")";
  Enviar($sock, $query);

  // Seta modificacao no historico das avaliacoes.
  $query = "insert into Avaliacao_historicos ";
  $query.= "(cod_avaliacao, cod_usuario, data, acao) ";
  $query.= "values (".$cod_avaliacao.", ".$cod_usuario;
  $query.= ", ".time().", K)";
  Enviar($sock, $query);

  // Retorno chamando feedback
  $objResponse->script();

  return $objResponse;
}

/* *********************************************************************
   AlertaFraseFerramenta - Pega a frase no BD e gera um alert com tal frase
   Entrada: $cod_texto - codigo da frase no BD
            $cod_ferramenta - codigo da ferramenta

   Saida: XML da funcao Ajax que exibe o alert
*/
function AlertaFraseFerramenta($cod_texto,$cod_ferramenta){
  $objResponse = new xajaxResponse();

  $sock=Conectar("");
  $msg=RetornaFrase($sock, $cod_texto, $cod_ferramenta);
  Desconectar($sock);

  $objResponse->script("alert('".$msg."');");

  return $objResponse;

}


/* *********************************************************************
   RetornaFraseGeralDinamic - Retorna lista de frases 'geral' para uma variavel JavaScript, dinamicamente
   Entrada: $variavel - nome da variavel que vai receber as frases

   Saida: XML da função Ajax
*/
function RetornaFraseGeralDinamic($variavel){

  $objResponse = new xajaxResponse();

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $retorno="{\n";

  foreach ($lista_frases as $cod => $linha){
    if ($cod>1) $retorno.=", ";
    $retorno.="\"msg_ger".$cod."\":\"".$linha."\" ";
  }

  $retorno.="\n}";

  $objResponse->script($variavel." = ".$retorno.";");


  return $objResponse;
}

/* *********************************************************************
   AcabaEdicaoDinamic - Marca no banco de dados o fim da edição do item dado, dinâmicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_avaliacao -avaliacao a qual o endereco estará associado
            $cod_usuario - código do usuário que está utilizando
            $acao - 0 se for cancelar a edição,
                    1 se for finalizar a edição.

   Saida: XML da função Ajax
*/
function AcabaEdicaoDinamic($cod_curso, $cod_avaliacao, $cod_usuario, $acao){

  $objResponse = new xajaxResponse();

  $sock=Conectar($cod_curso);

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  unset ($data);
  $data = time();

  $consulta="update Avaliacao set status='F', data=".$data." where cod_avaliacao=".$cod_avaliacao;
  $res=Enviar($sock, $consulta);

  if ($acao){
    $consulta="insert into Avaliacao_historicos values ('".$cod_avaliacao."', '".$cod_usuario."', '".$data."', 'F')";
  }else{
    $consulta="insert into Avaliacao_historicos values ('".$cod_avaliacao."', '".$cod_usuario."', '".$data."', 'D')";
  }

  $res=Enviar($sock, $consulta);

  Desconectar($sock);

  return $objResponse;
}

/* *********************************************************************
   AbreEdicao - Marca no banco de dados o início da edição do item dado, dinamicamente
   Entrada: $cod_curso - código do curso, para conectar ao banco de dados
            $cod_avaliacao - avaliacao a qual o endereco estará associado
            $cod_usuario - código do usuário que está utilizando  
            $tela_avaliacao - identificador da tela de avaliacao (passadas, atuais ou futuras)

   Saida: XML da função Ajax
*/
function AbreEdicao($cod_curso, $cod_avaliacao, $cod_usuario, $tela_avaliacao){

  $objResponse = new xajaxResponse();

  // como vou precisar atualizar campos de data, preciso saber a data em UnixTime
  unset ($data);
  $data = time();

  $sock=Conectar($cod_curso);

  $linha=RetornaStatusAvaliacao($sock, "Avaliacao", $cod_avaliacao);

  $linha_historico=RetornaUltimaPosicaoHistoricoAvaliacao($sock, "Avaliacao", $cod_avaliacao);

  if (($linha['status']=="E") && ($cod_usuario!=$linha_historico['cod_usuario'])){
    $objResponse->script("window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');");

    $objResponse->script("document.location='avaliacoes.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=22&cod_avaliacao=".$cod_avaliacao."&tela_avaliacao=".$tela_avaliacao."';");
  }else {
    if ($linha['status']=="E"){
      $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'D')";
      $res=Enviar($sock,$consulta);
    }
    $consulta="update Avaliacao set status='E', cod_usuario=".$cod_usuario.", inicio_edicao=".time()." where cod_avaliacao=".$cod_avaliacao;
    $res=Enviar($sock,$consulta);
    $consulta="insert into Avaliacao_historicos values (".$cod_avaliacao.", ".$cod_usuario.", ".time().", 'E')";
    $res=Enviar($sock,$consulta);
  }

  Desconectar($sock);

  //Para evitar erros no histórico
  sleep(1);

  return $objResponse;
}

/* *********************************************************************
   AlterarPeriodoDinamic - Altera o periodo corresponde ao inicio e terminio de uma avaliacao
   Entrada: $dadosForm -  variavel que contem os valores do formulario 

   Saida: XML da função Ajax
*/
function AlterarPeriodoDinamic($dadosForm){

  $objResponse = new xajaxResponse();

  $cod_curso = $dadosForm['cod_curso'];
  $cod_usuario = $dadosForm['cod_usuario'];
  $cod_avaliacao = $dadosForm['cod_avaliacao'];
  $tela_avaliacao = $dadosForm['tela_avaliacao'];
  $data_inicio=Data2UnixTime($dadosForm['data_inicio']);

  /* Aqui contamos que o dia acaba no ultimo segundo (23h59m59s)*/
  $data_fim=Data2UnixTime($dadosForm['data_fim']) + 86399;
  $texto = $dadosForm['texto']; /* Texto do feedback */

  AbreEdicao($cod_curso, $cod_avaliacao, $cod_usuario, $tela_avaliacao);

  $sock=Conectar($cod_curso);

  /*Atualiza data inicial e final*/
  $consulta="update Avaliacao set data_inicio=".$data_inicio.",data_termino=".$data_fim." where cod_avaliacao=".$cod_avaliacao;

  $res=Enviar($sock,$consulta);

  Desconectar($sock);

  AcabaEdicaoDinamic($cod_curso, $cod_avaliacao, $cod_usuario, 1);

  /* Feedback é sempre bom né? */
  $objResponse->call("mostraFeedback", $texto, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* *********************************************************************
   MudarCompartilhamentoDinamic - Muda o compartilhamento do item dado dinamicamente
   Entrada: $dadosForm - Dados provenientes do formulario 'form_comp' 
            $novo_comp - Nome do novo tipo de compartilhamento
   Saida: nenhuma
*/
function MudarCompartilhamentoDinamic($dadosForm,$novo_comp)
{
  $objResponse = new xajaxResponse();

  $tipo_comp=$dadosForm[tipo_comp];
  $cod_nota=$dadosForm[cod_nota];
  $cod_curso=$dadosForm[cod_curso];
  $cod_avaliacao=$dadosForm[cod_avaliacao];
  $portfolio_grupo=$dadosForm[portfolio_grupo];
  $cod_grupo=$dadosForm[cod_grupo];
  $cod_aluno=$dadosForm[cod_aluno];
  $spanName=$dadosForm[spanName];

  $sock=Conectar($cod_curso);

  if ($portfolio_grupo == '')
  {
    $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
    if ( (!strcmp($dados['Ferramenta'],'P') ) || (!strcmp($dados['Ferramenta'],'E')) || (!strcmp($dados['Ferramenta'],'N') ) )
    {
      if (!strcmp($dados['Tipo'],'G'))
        $portfolio_grupo=1;
      else
        $portfolio_grupo=0;
    }
  }

  if ($portfolio_grupo)
  {
    if ($cod_grupo != '' && $cod_grupo != -1)
      $cod_grupo_mudarcomp=$cod_grupo;
    else
      $cod_grupo_mudarcomp=RetornaCodGrupoPortfolioAvaliacao($sock,$cod_aluno,$cod_avaliacao);
    $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo_mudarcomp);
    $data=RetornaDataNota($sock,$cod_nota);
    foreach ($lista_integrantes as $cod_aluno => $linha)
    {
      $cod_nota_temp=RetornaCod($sock, $cod_aluno, $cod_avaliacao,$data['data']);
      if ($cod_nota_temp != null)
        MudarCompartilhamento($sock, $cod_nota_temp, $tipo_comp);
    }
  }
  else
      MudarCompartilhamento($sock, $cod_nota, $tipo_comp);

  Desconectar($sock);

  $objResponse->script("MudaSpanCompartilhamento('".$spanName."','".$novo_comp."','".$tipo_comp."',".$cod_nota.",".$cod_grupo.",".$cod_aluno.");");

  /* Feedback é sempre bom né? */
  $cod_lingua = (int) $_SESSION['cod_lingua_s'];
  $cod_ferramenta = 22;
  $cod_texto = 217;
  $texto_feedback = RetornaTexto($cod_lingua, $cod_ferramenta, $cod_texto);
  $objResponse->call("mostraFeedback", $texto_feedback, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* *********************************************************************
   MostrarAvaliacoesDinamic - Mostra o historico de avaliacoes do aluno/grupo,dinamicamente
   Entrada: $cod_curso - codigo do curso
            $cod_avaliacao - codigo da avaliacao
            $cod_aluno - codigo do aluno
            $cod_grupo - codigo do grupo
            $portfolio_grupo - codigo do portifolio grupo
            $id - id da tr correpondente ao aluno/grupo
            $cod_nota - codigo da nota
   Saida: nenhuma
*/
function MostrarAvaliacoesDinamic($cod_curso,$cod_avaliacao,$cod_aluno,$cod_grupo,$portfolio_grupo,$id,$cod_nota)
{
  $objResponse = new xajaxResponse();

  $sock=Conectar("");
  $lista_frases=RetornaListaDeFrases($sock,22);


  Desconectar($sock);

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock = Conectar("");
  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

  $sock=Conectar($cod_curso);

  $usr_formador=EFormador($sock,$cod_curso,$cod_usuario);
  $usr_aluno=EAluno($sock,$cod_curso,$cod_usuario);
  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  if ($portfolio_grupo == '')
  {
    $origem = "notasParticipantes";
    if ((!strcmp($dados['Ferramenta'],'P')) || (!strcmp($dados['Ferramenta'],'E')) || (!strcmp($dados['Ferramenta'],'N')))
    {
      if (!strcmp($dados['Tipo'],'G'))
        $portfolio_grupo=1;
      else
        $portfolio_grupo=0;
    }
  }
  else
    $origem = "avaliarParticipantes";

  if ($portfolio_grupo)
  {
    if ($cod_grupo != '' && $cod_grupo != -1)
      $cod_grupo_portfolio=$cod_grupo;
    else
      /*Aqui troquei a fun��o RetornaCodGrupoPortfolioAvaliacao($sock,$cod_aluno,$cod_avaliacao) pela fun��o RetornaCodigoGrupoAvaliacao($sock,$cod,$linha['Cod_avaliacao']);, para pegar o codigo do grupo em que o aluno estava quando a avalia��o foi feita*/
      $cod_grupo_portfolio=RetornaCodigoGrupoAvaliacao($sock,$cod_aluno,$cod_avaliacao);

    /*Aqui havia um problema que ele permitia a variavel $cod_grupo_portfolio ficar nula, agora ele n�o deixa mais e exibe a frase 93 - Este grupo ainda n�o foi avaliado nesta atividade!*/
    if (!$cod_grupo_portfolio)
      $cod_grupo_portfolio=0;

    if ($cod_grupo != '' && $cod_grupo != -1)
      //seta cod_aluno
      $cod_aluno=RetornaCodAlunodoGrupo($sock,$cod_avaliacao,$cod_grupo);
    else if ($cod_nota != '')
      //seta cod_grupo
      $cod_grupo=RetornaCodGrupoAvaliacao($sock,intval($cod_nota));
  }

  $i=0;
  $matrizConteudo;
  $foiavaliado=FoiAvaliado($sock,$cod_avaliacao,$cod_aluno);

  if ($foiavaliado)
  {
    if (($dados['Ferramenta'] == 'P') && $portfolio_grupo)
    {
      $cod=$cod_grupo_portfolio;
      $avaliacao_atual=RetornarAvaliacaoGrupo($sock,$cod_avaliacao,$cod_grupo_portfolio);
    }
    /*Altera��o que talvez resolva o problema da nota da avaliac��o que � registrada mais de uma vez*/
    else if ($dados['Ferramenta'] == 'E' && $dados['Tipo']=='G')
    {
      $avaliacao_atual=RetornarAvaliacaoGrupo($sock,$cod_avaliacao,$cod_grupo);
    }
    else if ($dados['Ferramenta'] == 'N' && $dados['Tipo']=='G')
    {
      $avaliacao_atual=RetornarAvaliacaoGrupo($sock,$cod_avaliacao,$cod_grupo);
    }
    else
    {
      $cod=$cod_aluno;
      $avaliacao_atual=RetornarAvaliacaoAluno($sock,$cod_avaliacao,$cod);
    }

    $cont=0;
    if (count($avaliacao_atual)>0)
    {
      foreach ($avaliacao_atual as $cod => $linha)
      {
        if ($usr_formador)   //Formador pode ver qualquer nota
          $podevernota=1;
        elseif ($usr_aluno)   //Aluno so pode ver nota compartilhada com ele
        {
          if (!strcmp($linha['tipo_compartilhamento'],'T'))
            $podevernota=1;
          elseif (!strcmp($linha['tipo_compartilhamento'],'F'))
            $podevernota=0;
          elseif (($portfolio_grupo) && (!strcmp($linha['tipo_compartilhamento'],'G')))
          {
            // � portfolio de grupo e nota compartilhada so com o grupo avaliado
            $cod_grupo_usuario=RetornaCodGrupoPortfolioAvaliacao($sock,$cod_usuario,$cod_avaliacao);         //retorna o codigo do grupo do usuario que esta acessando

            if ($cod_grupo_usuario==$cod_grupo_portfolio)    // O usuario pertence ao grupo que foi avaliado
              $podevernota=1;
            else                   // outro grupo nao pode ver
              $podevernota=0;
          }
          elseif (!strcmp($linha['tipo_compartilhamento'],'A'))
          {
            // nota compartilhada so com o aluno avaliado
            if ($cod_usuario==$cod_aluno)    //O usuario � o aluno que foi avaliado
              $podevernota=1;
            else                   //outro aluno nao pode ver
              $podevernota=0;
          }
        }

        if ($podevernota==1)
        {
          $cont++;

          if (!strcmp($linha['tipo_compartilhamento'],'T'))
            // 51 - Totalmente Compartilhado
            $compartilhamento=RetornaFraseDaLista($lista_frases,51);
          elseif (!strcmp($linha['tipo_compartilhamento'],'A'))
            // 54 - Compartilhado com Formadores e com o Participante
            $compartilhamento=RetornaFraseDaLista($lista_frases,54);
          elseif (!strcmp($linha['tipo_compartilhamento'],'G'))
            // 53 - Compartilhado com Formadores e com o Grupo
            $compartilhamento=RetornaFraseDaLista($lista_frases,53);
          else
            // 52 - Compartilhado com Formadores
            $compartilhamento=RetornaFraseDaLista($lista_frases,52);

          // nome do formador que avaliou
          if ($linha['cod_formador']==-2) // 66 (geral) - Sistema 
            $formador = RetornaFraseDaLista($lista_frases_geral, 66);
          else
            $formador = NomeUsuario($sock,RetornaCodigoUsuarioCurso($sock, $linha['cod_formador'], $cod_curso), $cod_curso);

          // 163 - Justificativa
          if (($comentario = Space2Nbsp($linha['comentario'])) == "")
            //200 - Nao definido
            $comentario = RetornaFraseDaLista($lista_frases,200);

          // a nota
          $matrizConteudo[$i][0] = FormataNota($linha['nota']);
          // a data da avaliacao
          $matrizConteudo[$i][1] = UnixTime2Data($linha['data']);
          // o formador que efetuou a avaliacao
          $matrizConteudo[$i][2] = $formador;
          // o nome do compartilhamento
          $matrizConteudo[$i][3] = $compartilhamento;
          // a justificativa
          $matrizConteudo[$i][4] = ConverteBarraAspas2Aspas(Enter2BR(LimpaConteudo($comentario)));
          // o codigo do formador
          $matrizConteudo[$i][5] = RetornaCodigoUsuarioCurso($sock, $linha['cod_formador'], $cod_curso);
          // o codigo da nota
          $matrizConteudo[$i][6] = $linha['cod_nota'];
           // o tipo de compartilhamento 
          $matrizConteudo[$i][7] = $linha['tipo_compartilhamento'];

          $i++;
        }
      }
    }
  }

  if ($i>0)
  {
    $objResponse->script("js_arrayConteudo = new Array();");
  }
  for($j=0;$j<$i;$j++)
  {
    $objResponse->script("js_arrayConteudo[".$j."] = new Array();");
    for($k=0;$k<8;$k++)
      $objResponse->script("js_arrayConteudo[".$j."][".$k."] = '".$matrizConteudo[$j][$k]."';");
  }
  if ($origem == "notasParticipantes")
  {
    $cod_grupo = RetornaCodGrupoPortfolioAvaliacao($sock,$cod_aluno,$cod_avaliacao);
    if ($cod_grupo == null)
      //trata-se de avaliacao de um aluno e naum de grupo
      $cod_grupo = -1;
    $objResponse->script("HabilitaHistorico(js_arrayConteudo,".$i.",'".$id."',".$cod_avaliacao.",".$cod_aluno.",".$cod_grupo.");");
  }
  else
    $objResponse->script("HabilitaHistorico(js_arrayConteudo,".$i.",'".$id."');");

  Desconectar($sock);

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* *********************************************************************
   MostrarParticipacoesDinamic - Mostra partipacoes do aluno/grupo,dinamicamente
   Entrada: $cod_curso - codigo do curso
            $cod_avaliacao - codigo da avaliacao
            $cod_aluno - codigo do aluno
            $cod_grupo - codigo do grupo
   Saida: nenhuma
*/
function MostrarParticipacoesDinamic($cod_curso,$cod_avaliacao,$cod_aluno,$cod_grupo)
{
  $objResponse = new xajaxResponse();

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");
  $lista_frases=RetornaListaDeFrases($sock,22);
  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $usr_formador=EFormador($sock,$cod_curso,$cod_usuario);
  $usr_aluno=EAluno($sock,$cod_curso,$cod_usuario);
  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  $ferramenta=$dados['Ferramenta'];
  $cod_atividade=$dados['Cod_atividade'];

  if ( (!strcmp($dados['Ferramenta'],'P') ) || (!strcmp($dados['Ferramenta'],'E')) || (!strcmp($dados['Ferramenta'],'N') ) )
  {
    if (!strcmp($dados['Tipo'],'G'))
      $portfolio_grupo=1;
    else
      $portfolio_grupo=0;
  }

  if ($portfolio_grupo)
  {
    if ($cod_grupo != '' && $cod_grupo != NaN)
      $cod_grupo_portfolio=$cod_grupo;
    else
      /*Aqui troquei a fun��o RetornaCodGrupoPortfolioAvaliacao($sock,$cod_aluno,$cod_avaliacao) pela fun��o RetornaCodigoGrupoAvaliacao($sock,$cod,$linha['Cod_avaliacao']);, para pegar o codigo do grupo em que o aluno estava quando a avalia��o foi feita*/
      $cod_grupo_portfolio=RetornaCodigoGrupoAvaliacao($sock,$cod_aluno,$cod_avaliacao);

    /*Aqui havia um problema que ele permitia a variavel $cod_grupo_portfolio ficar nula, agora ele n�o deixa mais e exibe a frase 93 - Este grupo ainda n�o foi avaliado nesta atividade!*/
    if (!$cod_grupo_portfolio)
      $cod_grupo_portfolio=0;
  }

  if (!strcmp($ferramenta,'P'))
  {
    if ($portfolio_grupo)
      $cod=$cod_grupo_portfolio;
    else
      $cod=$cod_aluno;

    if (RealizouAtividadeNoPortfolio($sock,$cod_avaliacao,$cod,$portfolio_grupo))
    {
      $num_participacoes=RetornaNumItensPortfolioAvaliacao($sock,$cod,$cod_avaliacao,$portfolio_grupo,$cod_usuario,$usr_formador,$cod_aluno);

      if ($portfolio_grupo)
      {
        $script="window.open('../portfolio/ver_itens_aluno.php?cod_curso=".$cod_curso."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_avaliacao=".$cod_avaliacao."','ItensPortfolioParticipante','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');";
      }
      else
      {
        $script="window.open('../portfolio/ver_itens_aluno.php?cod_curso=".$cod_curso."&cod_usuario_portfolio=".$cod_aluno."&cod_avaliacao=".$cod_avaliacao."','ItensPortfolioParticipante','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');";
      }
    }
    else
      $script = "";

  }
  else if ($ferramenta=='N')
  {
     $script = "";
  }
  else if (!strcmp($ferramenta,'E'))
  {
    if ($portfolio_grupo)
      $cod=$cod_grupo_portfolio;
    else
      $cod=$cod_aluno;

    $modelo=RespondeuExercicio($sock,$cod_avaliacao,$cod,$portfolio_grupo);

    if ($modelo!=0)
    {
      $param = "'width=600,height=400,top=150,left=150,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes'";
      if ($portfolio_grupo)
      {
        $script="window.open('../exercicios/ver_aplicado_popup.php?&origem=avaliacao&cod_dono=".$cod_grupo_portfolio."&cod_resolucao=".$modelo['cod_resolucao']."&cod_curso=".$cod_curso."','ExercicioResolvido',".$param.");";
      }
      else
      {
         $script="window.open('../exercicios/ver_aplicado_popup.php?&origem=avaliacao&cod_dono=".$cod_aluno."&cod_resolucao=".$modelo['cod_resolucao']."&cod_curso=".$cod_curso."','ExercicioResolvido',".$param.");";
      }
    }
  }
  elseif (!strcmp($ferramenta,'B'))
  {
    if (BatePapoExiste($sock,$cod_atividade))
    {
      $lista_usuarios=RetornaUsuarios($sock,$cod_curso);
      $lista_sessoes=RetornaCodSessao($sock,$cod_atividade);
      foreach($lista_sessoes as $cod => $linha)
      {
        $msgs_qtde=RetornaQtdeMsgsUsuario($sock,$linha['Cod_sessao'],$lista_usuarios);
        //para cada aluno incrementar a quantidade de mensagens
        foreach($lista_usuarios as $cod => $nome)
        {
          $msgs_total[$cod]=$msgs_total[$cod]+$msgs_qtde[$cod];
        }
      }

      if ( ($num_participacoes = (int)$msgs_total[$cod_aluno]) == 0 )
      {
        // zero participa��es
        $script = "";
      }
      else
      {
        $script="window.open('../batepapo/ver_falas_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno=".$cod_aluno."&cod_assunto=".$cod_atividade."&cod_avaliacao=".$cod_avaliacao."','VerParticipacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');";
      }
    }
    else
      $script = "";
  }
  elseif (!strcmp($ferramenta,'F'))
  {
    if (ParticipouDoForum($sock,$cod_aluno,$cod_atividade))
    {
      $script="window.open('../forum/ver_mensagens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno=".$cod_aluno."&cod_forum=".$cod_atividade."&cod_avaliacao=".$cod_avaliacao."','VerParticipacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');";
    }
    else
    {
      $script="";
    }
  }

  /* Feedback é sempre bom né? */
  $cod_lingua = (int) $_SESSION['cod_lingua_s'];
  $cod_ferramenta = 22;
  $cod_texto = 223;
  $texto_feedback = RetornaTexto($cod_lingua, $cod_ferramenta, $cod_texto);

  if ($script != "")
    $objResponse->script($script);
  else
    $objResponse->alert($texto_feedback);

  return $objResponse;
}

/* *********************************************************************
   ApagarAvalicaoDinamic - Apaga uma avaliacao
   Entrada: $cod_curso - codigo do curso
            $cod_avaliacao - codigo da avaliacao
            $cod_nota - codigo da nota
            $cod_aluno - codigo do aluno
            $cod_grupo - codigo do grupo
            $portfolio_grupo - codigo do portifolio grupo
            $id - id da tr correspondente ao aluno/grupo cuja avaliacao foi apagada
   Saida: nenhuma
*/
function ApagarAvalicaoDinamic($cod_curso,$cod_avaliacao,$cod_nota,$cod_aluno,$cod_grupo,$portfolio_grupo,$id)
{
  $objResponse = new xajaxResponse();

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar($cod_curso);

  if ($cod_grupo != '' && $cod_grupo != -1 && $cod_grupo != NaN)
    $cod_aluno = RetornaCodAlunodoGrupo($sock,$cod_avaliacao,$cod_grupo);

  $data=RetornaDataNota($sock,$cod_nota);
  $usr_formador=EFormador($sock,$cod_curso,$cod_usuario);

  if ($portfolio_grupo == '')
  {
    $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
    if ( (!strcmp($dados['Ferramenta'],'P') ) || (!strcmp($dados['Ferramenta'],'E')) || (!strcmp($dados['Ferramenta'],'N')))
    {
      if (!strcmp($dados['Tipo'],'G'))
        $portfolio_grupo=1;
      else
        $portfolio_grupo=0;
    }
  }

  if (!$portfolio_grupo)
    //dados da ultima avaliacao
    $dados_nota=RetornaDadosNota($sock, $cod_aluno, $cod_avaliacao,$cod_usuario,$usr_formador);
  else
    //dados da ultima avaliacao
    $dados_nota=RetornaDadosNotaGrupoStatusF($sock, $cod_grupo, $cod_avaliacao,$usr_formador);

  if ($portfolio_grupo)
  {
    if ($cod_grupo != '' && $cod_grupo != -1 && $cod_grupo != NaN)
      $cod_grupo_apagar=$cod_grupo;
    else
      $cod_grupo_apagar=RetornaCodGrupoPortfolioAvaliacao($sock,$cod_aluno,$cod_avaliacao);

    $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo_apagar);
    $data=RetornaDataNota($sock,$cod_nota);
    foreach ($lista_integrantes as $cod_aluno => $linha)
    {
      $cod_nota_temp=RetornaCod($sock, $cod_aluno, $cod_avaliacao,$data['data']);
      if ($cod_nota_temp != null)
        ApagarNota($sock, $cod_nota_temp, $cod_usuario);
    }
  }
  else
    ApagarNota($sock, $cod_nota, $cod_usuario);


  if ($dados_nota['data'] == $data['data'])
  //avaliacao apagada era a ultima avaliacao
  {
    $dados_nota=RetornaDadosUltimaNotaStatusA($sock, $cod_aluno, $cod_avaliacao);
    if ($dados_nota != NULL)//ainda existem avaliacoes
    {
      MudaStatusAvaliacaoParaF($sock,$dados_nota['data']);
      $objResponse->script("AtualizaTr('".$id."',".$dados_nota['cod_nota'].",'".FormataNota($dados_nota['nota'])."','".UnixTime2Data($dados_nota['data'])."','".$dados_nota['tipo_compartilhamento']."');");
    }
    else
    {
      $objResponse->script("DesabilitaTr('".$id."'+'_hist');");
      $objResponse->script("AtualizaTr('".$id."',-1,'','','');");
    }
  }

  Desconectar($sock);

  /* Feedback é sempre bom né? */
  $cod_lingua = (int) $_SESSION['cod_lingua_s'];
  $cod_ferramenta = 22;
  $cod_texto = 219;
  $texto_feedback = RetornaTexto($cod_lingua, $cod_ferramenta, $cod_texto);
  $objResponse->call("mostraFeedback", $texto_feedback, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* ***************************************************************************
   AvaliarDinamic - habilita a linha para poder avaliar o aluno/grupo
   entrada: $cod_curso - codigo do curso
            $cod_avaliacao - codigo da avaliacao
            $cod_aluno_grupo - codigo do aluno ou do grupo
            $exercicio_grupo - codigo exercio grupo
            $id - id da tr correponde ao aluno/grupo que sera avaliado 
   saida: array[][cod_resolucao]    - codigo da resolucao
*/
function AvaliarDinamic($cod_curso, $cod_avaliacao, $cod_aluno_grupo, $exercicio_grupo, $id)
{
  $objResponse = new xajaxResponse();

  $sock=Conectar($cod_curso);

  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  //$lista_itens = RetornaDadosExercicioAvaliado($sock, $cod_avaliacao, $cod_aluno_grupo,$exercicio_grupo);

  $opNota = 4;
  $opJust = 2;
  $opComp = 0;
  $opEnviar = 0;
  $cod_modelo = -1;
  $valueInputNota = 0;

  //$array_exercicio = RetornaDadosNotaExercicio($sock, $cod_avaliacao, $cod_aluno_grupo,$exercicio_grupo);

  if ($dados['Ferramenta'] == 'E')
  {
    $array_exercicio = RetornaDadosNotaExercicio($sock, $cod_avaliacao, $cod_aluno_grupo,$exercicio_grupo);
    if ($array_exercicio['existe'] == false)
    {
      $opNota = 1;
      $opNota = 3;
      if ($array_exercicio['resolvido'] == true)
      {
        $opNota = 3;
      /*if (is_array($lista_itens))
          $cod_modelo=$lista_itens['cod_modelo']; */
      }
    }
    else
    {
      $valueInputNota = $array_exercicio['nota'];
      $opNota = 3;
    }
  }

  if (($dados['Ferramenta'] == 'E')&&($array_exercicio['existe'] == false))
    $opJust = 1;

  if (($dados['Ferramenta'] != 'E')||($array_exercicio['existe'] == true))//Se existe avaliacao para este exercicio
    $opComp = 1;

  if ((is_null($array_exercicio['existe'])) || ($array_exercicio['existe'] == true))//Se existe avaliacao para este exercicio
    $opEnviar = 1;

  Desconectar($sock);

  $objResponse->script("HabilitaAvaliar('".$id."',".$opNota.",".$opJust.",".$opComp.",".$valueInputNota.",".$cod_modelo.",".$opEnviar.",".$cod_aluno_grupo.");");

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* ***************************************************************************
   RegistrarAvaliacaoDinamic - registra uma avaliacao a base, dinamicamente
   entrada: $cod_curso - codigo do curso
            $cod_avaliacao - codigo da avaliacao
            $cod_aluno_grupo - codigo do aluno ou do grupo
            $cod_grupo_portfolio - codifo do grupo ou do portifolio 
            $cod_item - codigo do item
            $nota - nota da avaliacao   
            $comentario - justificativa
            $compartilhamento - tipo de compartilhamento
            $id - id da tr corresponde ao aluno/grupo que foi avaliado 
   saida: array[][cod_resolucao]    - codigo da resolucao
*/
function RegistrarAvaliacaoDinamic($cod_curso, $cod_avaliacao, $cod_aluno_grupo, $cod_grupo_portfolio, $cod_item, $nota,$comentario, $compartilhamento, $id)
{
  $objResponse = new xajaxResponse();

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar($cod_curso);

  $usr_formador=EFormador($sock,$cod_curso,$cod_usuario);
  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  $cod_aluno = $cod_grupo = $cod_aluno_grupo;
  $portfolio_grupo = ( ($dados['Ferramenta'] == 'P' || $dados['Ferramenta']=='N') && ($dados['Tipo'] == 'G') );
  $exercicio_grupo = ( ($dados['Ferramenta'] == 'E') && ($dados['Tipo'] == 'G') );

  $alteracao = 0;

  if ((!$portfolio_grupo)&&(!$exercicio_grupo))
  {
    $verificacao=AlunoFoiAvaliado($sock,$cod_avaliacao,$cod_aluno);
    if ($verificacao['Existe'])
    {
      if (($verificacao['Cont']==1) && ($verificacao['Edicao'])) // � a primeira avalia��o realizada, mas havia uma em edicao antes que foi cancelada
      {
        if (($portfolio_grupo)||($exercicio_grupo))
        {
          $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo_portfolio);
          $sock = MudarDB($sock, $cod_curso);
          foreach ($lista_integrantes as $cod_aluno => $linha)
          {
            $alteracao = 0;
            $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_grupo_portfolio);
          }
        }
        else
        {
          $alteracao = 0;
          $cod_null='NULL';
          $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_null);
        }
      }
      else // O aluno ja foi avaliado
      {
        $alteracao = $verificacao['Existe'];
        if (($portfolio_grupo)||($exercicio_grupo))
        {
          $lista_integrantes=RetornaListaIntegrantesMomentoAvaliacao($sock,$cod_curso,$cod_grupo_portfolio,$cod_avaliacao);
          $sock = MudarDB($sock, $cod_curso);
          foreach ($lista_integrantes as $cod_aluno => $linha)
          {
            $cod_nota=IniciaAvaliacaoAluno($sock,$cod_aluno, $cod_avaliacao, $cod_usuario, $cod_grupo_portfolio);
          }
          // mudan�a
          $avaliacao_atual=RetornarAvaliacaoGrupo($sock,$cod_avaliacao,$cod_grupo_portfolio);
        }
        else
        {
          $cod_null='NULL';
          $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_null);
          $avaliacao_atual=RetornarAvaliacaoAluno($sock,$cod_avaliacao,$cod_aluno);
        }
      }
    }
    else //$verificacao[existe]=false      A avalia��o n�o existe, o formador pode avaliar o aluno
    {
      $alteracao = $verificacao['Existe'];
      if (($portfolio_grupo)&&($exercicio_grupo))
      {
        $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo_portfolio);
        $sock = MudarDB($sock, $cod_curso);
        foreach ($lista_integrantes as $cod_aluno => $linha)
        {
          $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_grupo_portfolio);
        }
      }
      else
      {
        $cod_null='NULL';
        $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_null);
      }
    }
  }
  else if (($portfolio_grupo)||($exercicio_grupo))
  {
    if (GrupoFoiAvaliado($sock,$cod_avaliacao,$cod_grupo))
    {
      $lista_integrantes=RetornaListaIntegrantesMomentoAvaliacao($sock,$cod_curso,$cod_grupo,$cod_avaliacao);
      $sock = MudarDB($sock, $cod_curso);
      if (is_array($lista_integrantes))
      foreach ($lista_integrantes as $cod_aluno => $linha)
      {
        $alteracao = 1;
        $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_grupo);
      }
    }
    else
    {
      $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo);
      $sock = MudarDB($sock, $cod_curso);
      if (is_array($lista_integrantes))
      foreach ($lista_integrantes as $cod_aluno => $linha)
      {
        $alteracao = 0;
        $cod_null='NULL';
        $cod_nota=IniciaAvaliacaoAluno($sock, $cod_aluno, $cod_avaliacao, $cod_usuario, $cod_grupo);
      }
    }
  }

  $tabela="Avaliacao_notas";
  $cod_aluno = $cod_grupo = $cod_aluno_grupo;

  $virgula = strstr($nota, ",");
  if (strcmp($virgula,""))
  {
    $tmpnota=explode(",",$nota);
    $nota=implode(".", $tmpnota);
  }

  $comentario = EliminaScript($comentario);
  $comentario = LimpaConteudo($comentario);

  if ($alteracao)
  {
    if ($portfolio_grupo || $exercicio_grupo)
    {
      AtualizaAlteracaoAvaliacaoElementodoGrupo($sock, $tabela, $cod_grupo, $cod_avaliacao, trim($comentario), $nota, $compartilhamento);
      $verifica=1;
    }
    else
    {
      AtualizaAlteracaoAvaliacao($sock, $tabela, $cod_aluno, $cod_avaliacao, $cod_nota, trim($comentario), $nota, $compartilhamento);
    }
  }
  else
  {
    if ($portfolio_grupo || $exercicio_grupo)
    {
      AtualizaAvaliacaoElementodoGrupo($sock, $tabela, $cod_grupo, $cod_avaliacao, trim($comentario), $nota, $compartilhamento);
      $verifica=1;
    }
    else
    {
      AtualizaAvaliacaoAluno($sock, $tabela, $cod_nota, trim($comentario), $nota, $compartilhamento);
    }
  }


  //if ($dados['Ferramenta'] == 'P' && $cod_item != '')
  if ($dados['Ferramenta'] == 'P')
  {
    $lista_itens=RetornaListaItensAvaliacaoPortfolio($sock, $cod_avaliacao, $cod_aluno);
    $cod_item=$lista_itens[0]['cod_item'];

    AtualizaItemDeAvaliacao($sock,$cod_avaliacao,$cod_item,$cod_nota);
  }

  if ($dados['Tipo'] == 'G')
    $dados_nota=RetornaDadosNotaGrupoStatusF($sock,$cod_grupo,$cod_avaliacao,$usr_formador);
  else
    $dados_nota=RetornaDadosNota($sock,$cod_aluno,$cod_avaliacao,$cod_usuario,$usr_formador);

  $cod_nota = $dados_nota['cod_nota'];

  $objResponse->script("AtualizaTr('".$id."',".$cod_nota.",'".FormataNota($nota)."','".UnixTime2Data(time())."','".$compartilhamento."',".$cod_aluno.",'".$cod_grupo."');");
  $objResponse->script("DesabilitaTr('".$id."'+'_avaliar');");
  
  if (!($dados['Tipo'] == 'G')){
    $lista_nomes = RetornaNomeUsuario($sock, $cod_curso, $cod_aluno);
    unset($lista1);
    
    foreach($lista_nomes as $cod=>$linha)
    {
      $nome = $lista1[$linha['cod_usuario']]=$linha['nome'];
    }
  }
  else{
    $nome = RetornaNomeGrupo($sock, $cod_grupo);
  }
    
  Desconectar($sock);
  
  /* Feedback é sempre bom né? */
  $cod_lingua = (int) $_SESSION['cod_lingua_s'];
  /* #237 - A nota da avalia��o de */
  $texto1 = RetornaTexto($cod_lingua, 22, 237);
  /* #238 - foi alterada com sucesso */
  $texto2 = RetornaTexto($cod_lingua, 22, 238);
  
  $objResponse->call("mostraFeedback", $texto1." ".$nome." ".$texto2, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* ***************************************************************************
   GravarExpressaoDinamic - Grava nova expressao no BD
   entrada: $dadosForm - dados passados pelo formulario de registro de uma nova expressao

   saida: retorna a resposta de XML
*/
function GravarExpressaoDinamic($dadosForm)
{
  $objResponse = new xajaxResponse();

  $nova_expressao=$dadosForm[nova_expressao];
  $nova_norma=$dadosForm[nova_norma];
  $tipo_compartilhamento=$dadosForm[tipo_compartilhamento];
  $cod_curso=$dadosForm[cod_curso];

  $sock=Conectar($cod_curso);

  GravarExpressaoMedia($sock, $nova_expressao, $nova_norma, $tipo_compartilhamento);

  Desconectar($sock);

  /* Feedback é sempre bom né? */
  $cod_lingua = (int) $_SESSION['cod_lingua_s'];
  $cod_ferramenta = 22;
  $cod_texto = 218;
  $texto_feedback = RetornaTexto($cod_lingua, $cod_ferramenta, $cod_texto);
  $objResponse->call("mostraFeedback", $texto_feedback, 'true');

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* ***************************************************************************
   RetornaNotaExercicio - Retorna a nota do aluno em determinado exercicio pela tabela de exercicio
   entradas:   $sock
               $cod_usuario
               $cod_avaliacao -> usado para descobrir o cod_exercicio == cod_atividade
   saida:      $nota - Nota do aluno
*/
function RetornaNotaExercicio($sock, $cod_avaliacao, $cod_usuario) {
  // retornar todas as notas visiveis ao usuario
  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  $cod_atividade=$dados['Cod_atividade'];

  $query = "select nota from Exercicios_resolucao where cod_usuario=".$cod_usuario." and cod_exercicio=".$cod_atividade;
  $res = Enviar($sock, $query);
  $retorno = RetornaLinha($res);
  $nota = $retorno['nota'];

  return $nota;
}

/* ***************************************************************************
   RetornaNotaExercicioGrupo - Retorna a nota do grupo em determinado exercicio pela tabela de exercicio
   entradas:   $sock
               $cod_usuario
               $cod_avaliacao -> usado para descobrir o cod_exercicio == cod_atividade
   saida:      $nota - Nota do grupo
*/
function RetornaNotaExercicioGrupo($sock, $cod_avaliacao, $cod_grupo){
  // retornar todas as notas visiveis ao usuario
  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  $cod_atividade=$dados['Cod_atividade'];

  $query = "select nota from Exercicios_resolucao where cod_grupo=".$cod_grupo." and cod_exercicio=".$cod_atividade;
  $res = Enviar($sock, $query);
  $retorno = RetornaLinha($res);
  $nota = $retorno['nota'];

  return $nota;
}

function RetornaCodResolucaoExercicio($sock, $cod_avaliacao, $cod_grupo){
  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  $cod_atividade=$dados['Cod_atividade'];

  $query = "select cod_resolucao from Exercicios_resolucao where cod_grupo=".$cod_grupo." and cod_exercicio=".$cod_atividade;
  $res = Enviar($sock, $query);
  $retorno = RetornaLinha($res);
  return($retorno['cod_resolucao']);
}

/* *********************************************************************
   ExpulsaVisitante - verifica se o usuario eh visitante e impede seu
                    acesso se for
   entrada: $sock        - sock de conexao
   $cod_curso   - c�digo do curso atual.
   $cod_usuario - c�digo do usu�rio o qual pretende-se testar o
                  acesso � ferramenta correio.
   $eh_popup    - booleano que indica se a p�gina que o chamou
                  � ou n�o um popup. Se n�o for, devemos fechar
                  as tags html do template comum de uma p�gina
                  de ferramenta (com rodap�, fechando o tabel�o,
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

    /* 1 - Avalia��es 504 - �rea restrita a alunos e formadores */
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

?>
