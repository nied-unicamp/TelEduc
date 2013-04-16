<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/forum.inc

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
   ARQUIVO : cursos/aplic/intermap/forum.inc
   ========================================================== */

/* *********************************************************************
   RetornaForuns - Retorna lista de f�runs.
   Entrada: $sock - BASE INTERNA
   Saida: array [$cod_forum]['nome']
                [$cod_forum]['status']
                [$cod_forum]['inicio']
                [$cod_forum]['fim']
*/
function RetornaForuns($sock)
{
  $query="select cod_forum,nome,status,data from Forum where status='A' or status='L' order by status,nome";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0)
  {
    foreach($lista as $cod => $linha)
    {
      $saida[$linha['cod_forum']]['nome']=$linha['nome'];
      $saida[$linha['cod_forum']]['status']=$linha['status'];
      $saida[$linha['cod_forum']]['inicio']=$linha['data'];
      $saida[$linha['cod_forum']]['fim']=$linha['data'];
    }
    $query="select cod_forum,max(data) 'data' from Forum_mensagens group by cod_forum";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);

    if (count($lista)>0)
      foreach($lista as $cod => $linha)
        if (isset($saida[$linha['cod_forum']]['fim']))
          $saida[$linha['cod_forum']]['fim']=$linha['data'];

    foreach($saida as $cod_forum => $linha)
      if ($linha['status']=='A')
        $saida[$cod_forum]['fim']=time();
  }

  return $saida;  
}

/* *********************************************************************
   RetornaForum - Retorna lista de f�runs.
   Entrada: $sock - BASE INTERNA
            $cod_forum
   Saida: array ['nome']
                ['status']
                ['inicio']
                ['fim']
*/
function RetornaForum($sock,$cod_forum)
{
  $query="select cod_forum,nome,status,data from Forum where (status='A' or status='L') and cod_forum=".$cod_forum." order by status,nome";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  $saida['nome']=$linha['nome'];
  $saida['status']=$linha['status'];
  $saida['inicio']=$linha['data'];
  $saida['fim']=time();

  if ($linha['status']=='L')
  {
    $query="select max(data) 'data' from Forum_mensagens where cod_forum=".$cod_forum;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    if ($linha['data']!="")
      $saida['fim']=$linha['data'];
  }

  return $saida;  
}

/* *********************************************************************
   RetornaQtdeMsgsPeriodo - Retorna um array com a qtde de mensagens num per�odo, segundo o
                            agrupamento dado
   Entrada: $sock - BASE INTERNA
            $cod_forum
            $data_inicio - Data em formato DD/MM/AAAA
            $data_fim - Data em formador DD/MM/AAAA
            $agrupar - dia, semana, mes
   Saida: array 
                [$periodo]['qtde_aluno']
                [$periodo]['qtde_formador']
                [$periodo]['qtde_total']
                [$periodo]['fim_periodo'] - Somente se $agrupar="semana"
   Obs: $periodo - no formato DD/MM/AAAA (dia) SS/MM/AAAA (semana) ou MM/AAAA(ano)
*/
function RetornaQtdeMsgsPeriodo($sock,$cod_curso, $cod_forum,$data_inicio,$data_fim,$agrupar)
{
  global $data_invertida_g;

  $inicio=Data2UnixTime($data_inicio);
  $fim=Data2UnixTime($data_fim) + 24*60*60-1;

  if ($agrupar=="dia")
  {
    $query="select FROM_UNIXTIME(data,'%d/%m/%Y') 'periodo', cod_usuario, count(DISTINCT cod_msg) 'qtde' from Forum_mensagens where cod_forum=".$cod_forum." and data>=".$inicio." and data<=".$fim." group by periodo,cod_usuario order by data";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);
    $lista_formadores=RetornaListaCodUsuarioFormador($sock, $cod_curso);

    $unixtime_inicio=$inicio;
    $unixtime_fim=$fim;

    while ($unixtime_inicio<$unixtime_fim)
    {
      $data=Unixtime2Dia($unixtime_inicio)."/".Unixtime2Mes($unixtime_inicio)."/".Unixtime2Ano($unixtime_inicio);
      $saida[$data]['qtde_total']+=0;
      $saida[$data]['qtde_formador']+=0;
      $saida[$data]['qtde_aluno']+=0;
      $unixtime_inicio=DiaMesAno2Unixtime(Unixtime2Dia($unixtime_inicio)+1,Unixtime2Mes($unixtime_inicio),Unixtime2Ano($unixtime_inicio));
    }


    $c=0;
    if (count($lista)>0)
    {
      foreach ($lista as $cod => $linha)
      {
        if ($lista_formadores[$linha['cod_usuario']]==$linha['cod_usuario'])
          $saida[$linha['periodo']]['qtde_formador']+=$linha['qtde'];
        else
          $saida[$linha['periodo']]['qtde_aluno']+=$linha['qtde'];
        $saida[$linha['periodo']]['qtde_total']+=$linha['qtde'];
      }
    }
  }
  else if ($agrupar=="mes")
  {
    $query="select FROM_UNIXTIME(data,'%m/%Y') 'periodo', cod_usuario, count(DISTINCT cod_msg) 'qtde' from Forum_mensagens where cod_forum=".$cod_forum." and data>=".$inicio." and data<=".$fim." group by periodo,cod_usuario order by data";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);
    $lista_formadores=RetornaListaCodUsuarioFormador($sock, $cod_curso);

    $unixtime_inicio=$inicio;
    $unixtime_fim=$fim;

    while ($unixtime_inicio<$unixtime_fim)
    {
      $data=Unixtime2Mes($unixtime_inicio)."/".Unixtime2Ano($unixtime_inicio);
      $saida[$data]['qtde_total']+=0;
      $saida[$data]['qtde_formador']+=0;
      $saida[$data]['qtde_aluno']+=0;
      $unixtime_inicio=DiaMesAno2Unixtime(1,Unixtime2Mes($unixtime_inicio)+1,Unixtime2Ano($unixtime_inicio));
    }


    $c=0;
    if (count($lista)>0)
    {
      foreach ($lista as $cod => $linha)
      {
        if ($lista_formadores[$linha['cod_usuario']]==$linha['cod_usuario'])
          $saida[$linha['periodo']]['qtde_formador']+=$linha['qtde'];
        else
          $saida[$linha['periodo']]['qtde_aluno']+=$linha['qtde'];
        $saida[$linha['periodo']]['qtde_total']+=$linha['qtde'];
      }
    }
  }
  else if ($agrupar=="semana")
  {
    $lista_formadores=RetornaListaCodUsuarioFormador($sock, $cod_curso);

    $unixtime_inicio=$inicio;
    $unixtime_fim=$fim;

    while ($unixtime_inicio<$unixtime_fim)
    {
      $data=Unixtime2Dia($unixtime_inicio)."/".Unixtime2Mes($unixtime_inicio)."/".Unixtime2Ano($unixtime_inicio);
      $unixtime_sem=DiaMesAno2Unixtime(Unixtime2Dia($unixtime_inicio)+6,Unixtime2Mes($unixtime_inicio),Unixtime2Ano($unixtime_inicio));
      $unixtime_prox=DiaMesAno2Unixtime(Unixtime2Dia($unixtime_inicio)+7,Unixtime2Mes($unixtime_inicio),Unixtime2Ano($unixtime_inicio));
      if ($unixtime_prox>$unixtime_fim)
        $unixtime_prox=$unixtime_fim;
      if ($unixtime_sem>$unixtime_fim)
        $unixtime_sem=$unixtime_fim;

      $query="select cod_usuario, count(DISTINCT cod_msg) 'qtde' from Forum_mensagens where cod_forum=".$cod_forum." and data>=".$unixtime_inicio." and data<".$unixtime_prox." group by cod_usuario";
      $res=Enviar($sock,$query);
      $lista=RetornaArrayLinhas($res);

      $saida[$data]['qtde_formador']=0;
      $saida[$data]['qtde_aluno']=0;
      $saida[$data]['qtde_total']=0;
      $saida[$data]['fim_periodo']=Unixtime2Dia($unixtime_sem)."/".Unixtime2Mes($unixtime_sem)."/".Unixtime2Ano($unixtime_sem);

      if (count($lista)>0)
      {
        foreach($lista as $cod => $linha)
        {
          if ($lista_formadores[$linha['cod_usuario']]==$linha['cod_usuario'])
            $saida[$data]['qtde_formador']+=$linha['qtde'];
          else
            $saida[$data]['qtde_aluno']+=$linha['qtde'];
          $saida[$data]['qtde_total']+=$linha['qtde'];
        }
      }
      $unixtime_inicio=$unixtime_prox;
    }
  }
  else
  {
    // 23 - Erro interno
    echo(RetornaFraseDaLista($_SESSION['lista_frases'],23));
  }

  return $saida;
}

/* *********************************************************************
   RetornaQtdeMsgsPeriodoUsuario - Retorna um array com a qtde de mensagens num per�odo, segundo o
                            agrupamento dado para um dado usu�rio
   Entrada: $sock - BASE INTERNA
            $cod_forum - 
            $data_inicio - Data em formato DD/MM/AAAA
            $data_fim - Data em formador DD/MM/AAAA
            $agrupar - dia, semana, mes
            $cod_usuario - codigo do usu�rio a ser feito o relat�rio
   Saida: array 
                [$periodo]['qtde']
                [$periodo]['fim_periodo'] - Somente se $agrupar="semana"
   Obs: $periodo - no formato DD/MM/AAAA (dia) DD/MM/AAAA (semana) ou MM/AAAA(ano)
*/
function RetornaQtdeMsgsPeriodoUsuario($sock,$cod_forum,$data_inicio,$data_fim,$agrupar,$cod_usuario)
{
  global $data_invertida_g;

  $inicio=Data2UnixTime($data_inicio);
  $fim=Data2UnixTime($data_fim) + 24*60*60-1;

  if ($agrupar=="dia")
  {
    $query="select FROM_UNIXTIME(data,'%d/%m/%Y') 'periodo', count(DISTINCT cod_msg) 'qtde' from Forum_mensagens where cod_forum=".$cod_forum." and cod_usuario=".$cod_usuario." and data>=".$inicio." and data<=".$fim." group by periodo order by data";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);

    $unixtime_inicio=$inicio;
    $unixtime_fim=$fim;

    while ($unixtime_inicio<$unixtime_fim)
    {
      $data=Unixtime2Dia($unixtime_inicio)."/".Unixtime2Mes($unixtime_inicio)."/".Unixtime2Ano($unixtime_inicio);
      $saida[$data]['qtde']+=0;
      $unixtime_inicio=DiaMesAno2Unixtime(Unixtime2Dia($unixtime_inicio)+1,Unixtime2Mes($unixtime_inicio),Unixtime2Ano($unixtime_inicio));
    }

    if (count($lista)>0)
      foreach ($lista as $cod => $linha)
        $saida[$linha['periodo']]['qtde']+=$linha['qtde'];
  }
  else if ($agrupar=="mes")
  {
    $query="select FROM_UNIXTIME(data,'%m/%Y') 'periodo', count(DISTINCT cod_msg) 'qtde' from Forum_mensagens where cod_forum=".$cod_forum." and cod_usuario=".$cod_usuario." and data>=".$inicio." and data<=".$fim." group by periodo order by data";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);

    $unixtime_inicio=$inicio;
    $unixtime_fim=$fim;

    while ($unixtime_inicio<$unixtime_fim)
    {
      $data=Unixtime2Mes($unixtime_inicio)."/".Unixtime2Ano($unixtime_inicio);
      $saida[$data]['qtde']+=0;
      $unixtime_inicio=DiaMesAno2Unixtime(1,Unixtime2Mes($unixtime_inicio)+1,Unixtime2Ano($unixtime_inicio));
    }

    if (count($lista)>0)
      foreach ($lista as $cod => $linha)
        $saida[$linha['periodo']]['qtde']+=$linha['qtde'];
  }
  else if ($agrupar=="semana")
  {
    $unixtime_inicio=$inicio;
    $unixtime_fim=$fim;

    while ($unixtime_inicio<$unixtime_fim)
    {
      $data=Unixtime2Dia($unixtime_inicio)."/".Unixtime2Mes($unixtime_inicio)."/".Unixtime2Ano($unixtime_inicio);
      $unixtime_sem=DiaMesAno2Unixtime(Unixtime2Dia($unixtime_inicio)+6,Unixtime2Mes($unixtime_inicio),Unixtime2Ano($unixtime_inicio));
      $unixtime_prox=DiaMesAno2Unixtime(Unixtime2Dia($unixtime_inicio)+7,Unixtime2Mes($unixtime_inicio),Unixtime2Ano($unixtime_inicio));
      if ($unixtime_prox>$unixtime_fim)
        $unixtime_prox=$unixtime_fim;
      if ($unixtime_sem>$unixtime_fim)
        $unixtime_sem=$unixtime_fim;

      $query="select count(DISTINCT cod_msg) 'qtde' from Forum_mensagens where cod_forum=".$cod_forum." and cod_usuario=".$cod_usuario." and data>=".$unixtime_inicio." and data<".$unixtime_prox;
      $res=Enviar($sock,$query);
      $linha=RetornaLinha($res);

      $saida[$data]['qtde']=0;
      $saida[$data]['fim_periodo']=Unixtime2Dia($unixtime_sem)."/".Unixtime2Mes($unixtime_sem)."/".Unixtime2Ano($unixtime_sem);

      $saida[$data]['qtde']+=$linha['qtde'];

      $unixtime_inicio=$unixtime_prox;
    }
  }
  else
  {
    // 23 - Erro interno
    echo(RetornaFraseDaLista($_SESSION['lista_frases'],23));
  }

  return $saida;
}

/* *********************************************************************
   RetornaQtdeMsgsPeriodoListaUsuarios - Retorna um array com a qtde de mensagens num per�odo,
                                        segundo o agrupamento dado para uma dada lista de usu�rios
   Entrada: $sock - BASE INTERNA
            $cod_forum - 
            $data_inicio - Data em formato DD/MM/AAAA
            $data_fim - Data em formador DD/MM/AAAA
            $agrupar - dia, semana, mes
            $lista_usuarios - array com o c�digo dos usu�rios como indice (retorno de RetornaListaCodUsuarioNome)
   Saida: array 
                [$cod_usuario][$periodo]['qtde']
                [$cod_usuario][$periodo]['fim_periodo'] - Somente se $agrupar="semana"
   Obs: $periodo - no formato DD/MM/AAAA (dia) DD/MM/AAAA (semana) ou MM/AAAA(ano)
*/
function RetornaQtdeMsgsPeriodoListaUsuarios($sock,$cod_forum,$data_inicio,$data_fim,$agrupar,$lista_usuarios)
{
  global $data_invertida_g;

  $inicio=Data2UnixTime($data_inicio);
  $fim=Data2UnixTime($data_fim) + 24*60*60-1;

  if ($agrupar=="dia")
  {
    $query="select FROM_UNIXTIME(data,'%d/%m/%Y') 'periodo', cod_usuario,count(DISTINCT cod_msg) 'qtde' from Forum_mensagens where cod_forum=".$cod_forum." and data>=".$inicio." and data<=".$fim." group by periodo,cod_usuario order by data";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);

    $unixtime_inicio=$inicio;
    $unixtime_fim=$fim;

    while ($unixtime_inicio<$unixtime_fim)
    {
      $data=Unixtime2Dia($unixtime_inicio)."/".Unixtime2Mes($unixtime_inicio)."/".Unixtime2Ano($unixtime_inicio);
      foreach ($lista_usuarios as $cod_usuario => $linha)
        $saida[$cod_usuario][$data]['qtde']+=0;
      $unixtime_inicio=DiaMesAno2Unixtime(Unixtime2Dia($unixtime_inicio)+1,Unixtime2Mes($unixtime_inicio),Unixtime2Ano($unixtime_inicio));
    }

    if (count($lista)>0)
      foreach ($lista as $cod => $linha)
        if (isset($lista_usuarios[$linha['cod_usuario']]))
          $saida[$linha['cod_usuario']][$linha['periodo']]['qtde']+=$linha['qtde'];
  }
  else if ($agrupar=="mes")
  {
    $query="select FROM_UNIXTIME(data,'%m/%Y') 'periodo', cod_usuario, count(DISTINCT cod_msg) 'qtde' from Forum_mensagens where cod_forum=".$cod_forum." and data>=".$inicio." and data<=".$fim." group by periodo,cod_usuario order by data";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);

    $unixtime_inicio=$inicio;
    $unixtime_fim=$fim;

    while ($unixtime_inicio<$unixtime_fim)
    {
      $data=Unixtime2Mes($unixtime_inicio)."/".Unixtime2Ano($unixtime_inicio);
      foreach($lista_usuarios as $cod_usuario => $linha)
        $saida[$cod_usuario][$data]['qtde']+=0;
      $unixtime_inicio=DiaMesAno2Unixtime(1,Unixtime2Mes($unixtime_inicio)+1,Unixtime2Ano($unixtime_inicio));
    }

    if (count($lista)>0)
      foreach ($lista as $cod => $linha)
        if (isset($lista_usuarios[$linha['cod_usuario']]))
          $saida[$linha['cod_usuario']][$linha['periodo']]['qtde']+=$linha['qtde'];
  }
  else if ($agrupar=="semana")
  {
    $unixtime_inicio=$inicio;
    $unixtime_fim=$fim;

    while ($unixtime_inicio<$unixtime_fim)
    {
      $data=Unixtime2Dia($unixtime_inicio)."/".Unixtime2Mes($unixtime_inicio)."/".Unixtime2Ano($unixtime_inicio);
      $unixtime_sem=DiaMesAno2Unixtime(Unixtime2Dia($unixtime_inicio)+6,Unixtime2Mes($unixtime_inicio),Unixtime2Ano($unixtime_inicio));
      $unixtime_prox=DiaMesAno2Unixtime(Unixtime2Dia($unixtime_inicio)+7,Unixtime2Mes($unixtime_inicio),Unixtime2Ano($unixtime_inicio));
      if ($unixtime_prox>$unixtime_fim)
        $unixtime_prox=$unixtime_fim;
      if ($unixtime_sem>$unixtime_fim)
        $unixtime_sem=$unixtime_fim;

      foreach($lista_usuarios as $cod => $linha)
      {
        $saida[$cod][$data]['qtde']=0;
        $saida[$cod][$data]['fim_periodo']=Unixtime2Dia($unixtime_sem)."/".Unixtime2Mes($unixtime_sem)."/".Unixtime2Ano($unixtime_sem);
      }

      $query="select cod_usuario,count(DISTINCT cod_msg) 'qtde' from Forum_mensagens where cod_forum=".$cod_forum." and data>=".$unixtime_inicio." and data<".$unixtime_prox." group by cod_usuario";
      $res=Enviar($sock,$query);
      $lista=RetornaArrayLinhas($res);

      if (count($lista)>0)
        foreach($lista as $cod => $linha)
        {
          if (isset($lista_usuarios[$linha['cod_usuario']]))
            $saida[$linha['cod_usuario']][$data]['qtde']+=$linha['qtde'];
        }

      $unixtime_inicio=$unixtime_prox;
    }
  }
  else
  {
    // 23 - Erro interno
    echo(RetornaFraseDaLista($_SESSION['lista_frases'],23));
  }

  return $saida;
}

/* *********************************************************************
   RetornaFluxoConversacao - Retorna o fluxo de conversacao de um f�rum 
                            agrupamento dado para um dado usu�rio
   Entrada: $sock - BASE INTERNA
            $cod_forum -             
   Saida: array [$cod_msg]['titulo'] - Titulo da Mensagem raiz
                [$cod_msg]['cod_usuario']
                [$cod_msg]['respostas'] - Array com [$cod_msg]['cod_usuario']
                                                    [$cod_msg]['data']
*/
function RetornaFluxoConversacao($sock,$cod_forum)
{
  $query="select cod_msg,titulo,cod_usuario from Forum_mensagens where cod_msg_pai IS NULL and cod_forum=".$cod_forum." and status='A' order by data";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  
  if (count($lista)>0)
  {
    foreach ($lista as $cod => $linha)
    {
      $saida[$linha['cod_msg']]['titulo']=$linha['titulo'];
      $saida[$linha['cod_msg']]['cod_usuario']=$linha['cod_usuario'];
      $saida[$linha['cod_msg']]['respostas']=RetornaArrayRespostas($sock,$cod_forum,$linha['cod_msg']);
    }
  }
 
  return $saida;
}

/* *********************************************************************
   RetornaArrayRespostas - Retorna um array com todas as mensagens 
                           descendentes da mensagem data (Todos os n�veis abaixo)
   Entrada: $sock - BASE INTERNA
            $cod_forum - codigo do forum
            $cod_msg_pai - codigo da mensagem pai
   Saida: array [$cod_msg]['cod_usuario']
                [$cod_msg]['data']
*/
function RetornaArrayRespostas($sock,$cod_forum,$cod_msg_pai)
{
  $query="select cod_msg,cod_usuario,data from Forum_mensagens where cod_msg_pai=".$cod_msg_pai." and cod_forum=".$cod_forum." and status='A' order by data";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  $cnt=0;
  if (count($lista)>0)
  {
    foreach($lista as $cod => $linha)
    {
      $saida[$linha['cod_msg']]['cod_usuario']=$linha['cod_usuario'];
      $saida[$linha['cod_msg']]['data']=$linha['data'];
      $tmp=RetornaArrayRespostas($sock,$cod_forum,$linha['cod_msg']);
      if (count($tmp)>0)
        foreach($tmp as $cod_msg_tmp => $linha_tmp)
          $saida[$cod_msg_tmp]=$linha_tmp;
    }    
  }
  return $saida;
}

/* *********************************************************************
   AgrupaArrayRespostasPorUsuario - Pega um array conforme saida do RetornaArrayRespostas
                                    e agrupa por usuario
   Entrada: $arrayRespostas = array [$cod_msg]['cod_usuario']
                                    [$cod_msg]['data']
   Saida: array [$cod_usuario][$cod_msg]=data
*/
function AgrupaArrayRespostasPorUsuario($arrayRespostas)
{

  if (count($arrayRespostas)>0)
    foreach ($arrayRespostas as $cod_msg => $linha)
      $saida[$linha['cod_usuario']][$cod_msg]=$linha['data'];
  return $saida;
}

/* *********************************************************************
   AgrupaArrayRespostasPorData - Pega um array conforme saida do RetornaArrayRespostas
                                    e agrupa por usuario
   Entrada: $arrayRespostas = array [$cod_msg]['cod_usuario']
                                    [$cod_msg]['data']
   Saida: array [data]['cod_msg']
                      ['cod_usuario']
*/
function AgrupaArrayRespostasPorData($arrayRespostas)
{

  if (count($arrayRespostas)>0)
  {
    foreach ($arrayRespostas as $cod_msg => $linha)
    {
      $saida[$linha['data']]['cod_msg']=$cod_msg;
      $saida[$linha['data']]['cod_usuario']=$linha['cod_usuario'];
    }
    ksort ($saida);
  }
  return $saida;
}

/* ******************************************************
  RetornaMensagem - Obt�m os dados da mensagem.

  Entradas: $sock - sock de conexao,
            $cod_msg - c�digo da mensagem,
            $cod_forum - c�digo do f�rum,

  Saida:    array contendo:
              $total - n�mero total de mensagens,
              $tupla - array com os dados da mensagem: cod_usuario,
                                                       titulo,
                                                       mensagem,
                                                       data.
*/
function RetornaMensagem($sock, $cod_msg, $cod_forum)
{
  $query = "select cod_usuario, titulo, mensagem, data from Forum_mensagens";
  $query .= " where cod_msg = ".$cod_msg." and cod_forum = ".$cod_forum;
  $query .= " and status = 'A'\n";

  $res = Enviar($sock, $query);
  $total = RetornaNumLinhas($res);
  $tupla = RetornaLinha($res);

  return array($total, $tupla);
}

/* *********************************************************************
   RetornaMapaInteracaoPeriodo - Retorna a lista com informa��es de quem 
                                 mandou mensagem para quem no periodo
   Entrada: $sock - BASE INTERNA
            $cod_forum - 
            $data_inicio - Data em formato DD/MM/AAAA
            $data_fim - Data em formador DD/MM/AAAA
            $lista_usuarios - lista no formador [$cod]=nome dos usu�rios a serem exibidos
   Saida: array [$cod_usuario_emissor][$cod_usuario_receptor]=qtde;
*/
function RetornaMapaInteracaoPeriodo($sock,$cod_forum,$data_inicio,$data_fim,$lista_usuarios)
{
  $inicio=Data2UnixTime($data_inicio);
  $fim=Data2UnixTime($data_fim) + 24*60*60;
  $query="select f.cod_usuario 'cod_usuario_emissor',p.cod_usuario 'cod_usuario_receptor',count(*) 'qtde' from Forum_mensagens f,Forum_mensagens p where f.cod_forum=".$cod_forum." and p.cod_forum=".$cod_forum." and f.cod_msg_pai=p.cod_msg and f.data>=".$inicio." and f.data<".$fim." and f.cod_usuario>=0 and p.cod_usuario>=0 group by f.cod_usuario,p.cod_usuario order by f.cod_usuario,p.cod_usuario";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  
  if (count($lista)>0)
  {
    foreach($lista as $cod => $linha)
    {
      if (isset($lista_usuarios[$linha['cod_usuario_emissor']]) && isset($lista_usuarios[$linha['cod_usuario_receptor']]))
      $saida[$linha['cod_usuario_emissor']][$linha['cod_usuario_receptor']]=$linha['qtde'];
    }    
  }
  return $saida;
}


?>
