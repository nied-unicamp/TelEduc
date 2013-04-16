<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/correio.inc

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
   ARQUIVO : cursos/aplic/intermap/correio.inc
   ========================================================== */

/* *********************************************************************
   RetornaMapaInteracaoPeriodo - Retorna a lista com informa��es de quem 
                                 mandou mensagem para quem no periodo
   Entrada: $sock - BASE INTERNA
            $data_inicio - Data em formato DD/MM/AAAA
            $data_fim - Data em formador DD/MM/AAAA
            $noh_todos - Booleano. True  = o noh todos aparece no grafo, coloque na saida as msgs para todos
                                   False = o noh todos nao aparece no grafo, coloque na saida apenas as msgs entre individuos
            $exibir_todos_usuarios - Booleano - true - Exibir todos usuarios, mesmo os que n�o enviaram msgs
	                                            - false - Exibir apenas os usuarios que enviaram msgs;
   Saida: array [$cod_usuario_emissor][$cod_usuario_receptor]=qtde;
   OBS: No caso de uma mensagem ter sido enviada para todos, ela pode aparecer de duas maneiras diferentes na tela:
	noh_todos = true  - A mensagem aparece como uma aresta entre o emissor e a entidade Todos
              = false - A mensagem deve ser apresentada como v�rias arestas entre o emissor e todos os receptores
*/
function RetornaMapaInteracaoPeriodo($sock,$data_inicio,$data_fim,$noh_todos,$exibir_todos_usuarios)
{
  $inicio=Data2UnixTime($data_inicio);
  $fim=Data2UnixTime($data_fim) + 24*60*60;

  if ($noh_todos)
  {
    // mensagens individuais e mensagens para Todos
    
    // essas sao as msgs individuais entre usuarios
    $query= "select CI.cod_usuario_emissor, CI.cod_usuario_receptor, count(distinct CI.cod_msg) 'qtde'".
             " from Correio_intermap CI, Correio_lista_destinos CLD".
             " where CI.cod_usuario_emissor>0 and CI.cod_usuario_receptor>0".
              " and CI.cod_msg=CLD.cod_msg".
              " and not(CLD.cod_destino=-1 and CLD.categ_destino='T')".
              " and CI.data>=".$inicio.
              " and CI.data<".$fim.
            " group by CI.cod_usuario_emissor, CI.cod_usuario_receptor";
    $res=Enviar($sock,$query);
    $lista_individuais=RetornaArrayLinhas($res);
    
    // essas sao as msgs enviadas para todos
    $query = "select CI.cod_msg, CI.cod_usuario_emissor, count(distinct CI.cod_msg) 'qtde'".
              " from Correio_intermap CI, Correio_lista_destinos CLD".
              " where CI.cod_msg=CLD.cod_msg".
               " and CI.data>=".$inicio.
               " and CI.data<".$fim.
               " and CI.cod_usuario_emissor>0 and CI.cod_usuario_receptor>0".
               " and CLD.cod_destino=-1 and CLD.categ_destino='T'".
             " group by CI.cod_usuario_emissor";
    $res=Enviar($sock,$query);
    $lista_todos=RetornaArrayLinhas($res);
    
    // PROBLEMA ! Veja que essas duas listas estao considerando msg 'fantasmas' 
    // (que foram enviadas, mas o usuario foi removido do curso ou transformado em 
    // convidado nao-interativo)
    // Vai ser preciso eliminar esses convidados da lista depois, faco isso abaixo
  }
  else
  {
    // somente mensagens individuais; Mensagens enviadas para Todos devem ser replicadas para os participantes que a receberam
    
    // Lembre-se como a ferramenta Correio insere mensagens na tabela Correio_intermap: ela insere cada par emissor-receptor.
    // Ou seja, independente de quem foi o destinat�rio REAL da mensagem (entidade Todos ou usu�rio inidividual ou um Grupo),
    // SE um Emissor enviou uma mensagem e um Receptor a leu, ent�o existe o par Emissor-Receptor na tabela Correio_intermap
    $query = "select cod_usuario_emissor, cod_usuario_receptor, count(distinct cod_msg) 'qtde'".
              " from Correio_intermap".
             " where cod_usuario_emissor>0 and cod_usuario_receptor>0".
               " and data>=".$inicio.
               " and data<".$fim.
             " group by cod_usuario_emissor, cod_usuario_receptor";
    $res   = Enviar($sock, $query);
    $lista_individuais = RetornaArrayLinhas($res);
     
    // PROBLEMA ! Essa lista tb considera msgs 'fantasmas'. Preciso limpa-la e faco isso abaixo 
  }

  // criamos uma lista com os usuarios que tem permissao para aparecer na saida:
  // alunos, formadores e convidados interativos. Os que nao estao nesse array sao fantasmas
  $query = "select UC.cod_usuario from ".$_SESSION['dbnamebase'].".Usuario_curso UC where binary UC.tipo_usuario='A' or binary UC.tipo_usuario='F' or binary UC.tipo_usuario='Z'";
  $res   = Enviar($sock, $query);
  $lista_permitidos = array();
  while ($linha = RetornaLinha($res))
  {
    $lista_permitidos[ $linha[cod_usuario] ] = 1;
  }
  
  if (is_array($lista_individuais))
  {
    foreach($lista_individuais as $cod => $linha)
    {
      // verificamos se estes usuarios sao permitidos
      if ($lista_permitidos[ $linha['cod_usuario_emissor'] ] && $lista_permitidos[ $linha['cod_usuario_receptor'] ])
      {
        $saida[$linha['cod_usuario_emissor']][$linha['cod_usuario_receptor']]=$linha['qtde'];
        // indicamos ao array que estes dois usuarios foram cobertos e portanto foram incluidos na saida
        // $lista_cobertos[ $linha[cod_usuario_emissor] ] = true;
        // $lista_cobertos[ $linha[cod_usuario_receptor] ] = true;
      }
    }
  }
  
  if (is_array($lista_todos))
  {
    foreach($lista_todos as $cod => $linha)
    {
      if ($lista_permitidos[$linha['cod_usuario_emissor']])
      {
        $saida[$linha['cod_usuario_emissor']][0] = $linha['qtde'];
        // indicamos ao array que este usuario foi incluido na saida
        // $lista_cobertos[ $linha[cod_usuario_emissor] ] = true;
      }
    }
  }


  return $saida;
}

/* *********************************************************************
   RetornaMapaInteracaoPeriodoTodos - Retorna a lista com informa��es de quem 
                                 mandou mensagem para quem no periodo
   Entrada: $sock - BASE INTERNA
            $data_inicio - Data em formato DD/MM/AAAA
            $data_fim - Data em formador DD/MM/AAAA
            $lista_usuarios - lista no formador [$cod]=nome dos usu�rios a serem exibidos
   Saida: array [$cod_usuario_emissor]=qtde;
*/
function RetornaMapaInteracaoPeriodoTodos($sock,$data_inicio,$data_fim,$lista_usuarios)
{
  $inicio=Data2UnixTime($data_inicio);
  $fim=Data2UnixTime($data_fim) + 24*60*60-1;
  $query = "select Cld.cod_usuario_emissor,count(DISTINCT Cld.cod_msg) 'qtde' from Correio_intermap Ci, Correio_lista_destinos Cld".
           " where Cld.cod_msg=Ci.cod_msg".
             " and Ci.data>=".$inicio.
             " and Ci.data<".$fim.
             " and Cld.cod_usuario_emissor>=0".
             " and Cld.cod_destino=-1".
             " and Cld.categ_destino='T'".
           " group by Cld.cod_usuario_emissor";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0)
  {
    foreach($lista as $cod => $linha)
    {
      if (isset($lista_usuarios[$linha['cod_usuario_emissor']]))
      {
        $saida[$linha['cod_usuario_emissor']]=$linha['qtde'];
      }
    }    
  }
  return $saida;
}

/* *********************************************************************
   RetornaQtdeMsgsPeriodo - Retorna um array com a qtde de mensagens num per�odo, segundo o
                            agrupamento dado
   Entrada: $sock - BASE INTERNA
            $data_inicio - Data em formato DD/MM/AAAA
            $data_fim - Data em formador DD/MM/AAAA
            $agrupar - dia, semana, mes
   Saida: array 
                [$periodo]['qtde_aluno']
                [$periodo]['qtde_formador']
                [$periodo]['qtde_convidado']
                [$periodo]['qtde_total']
                [$periodo]['fim_periodo'] - Somente se $agrupar="semana"
   Obs: $periodo - no formato DD/MM/AAAA (dia) SS/MM/AAAA (semana) ou MM/AAAA(ano)
*/
function RetornaQtdeMsgsPeriodo($sock,$cod_curso,$data_inicio,$data_fim,$agrupar)
{
  global $data_invertida_g;

  $inicio=Data2UnixTime($data_inicio);
  $fim=Data2UnixTime($data_fim) + 24*60*60-1;

  $lista_formadores = RetornaListaCodUsuarioFormador($sock, $cod_curso);
  $lista_convidados = RetornaCodUsuarioConvidado($sock, $cod_curso);
  
  if ($agrupar=="dia")
  {
    $query = "select FROM_UNIXTIME(data,'%d/%m/%Y') 'periodo', cod_usuario_emissor, count(DISTINCT cod_msg) 'qtde'";
    $query.= " from Correio_intermap";
    $query.= " where data>=".$inicio;
    $query.=   " and data<=".$fim;
    $query.= " group by periodo,cod_usuario_emissor order by data";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);

    $unixtime_inicio=$inicio;
    $unixtime_fim=$fim;

    while ($unixtime_inicio<$unixtime_fim)
    {
      $data=Unixtime2Dia($unixtime_inicio)."/".Unixtime2Mes($unixtime_inicio)."/".Unixtime2Ano($unixtime_inicio);
      $saida[$data]['qtde_total']+=0;
      $saida[$data]['qtde_formador']+=0;
      $saida[$data]['qtde_aluno']+=0;
      $saida[$data]['qtde_convidado']+=0;
      $unixtime_inicio=DiaMesAno2Unixtime(Unixtime2Dia($unixtime_inicio)+1,Unixtime2Mes($unixtime_inicio),Unixtime2Ano($unixtime_inicio));
    }

    $c=0;
    if (count($lista)>0)
    {
      foreach ($lista as $cod => $linha)
      {
        if ($lista_formadores[$linha['cod_usuario_emissor']]==$linha['cod_usuario_emissor'])
          // formador
          $saida[$linha['periodo']]['qtde_formador']+=$linha['qtde'];
        else if ($lista_convidados[$linha['cod_usuario_emissor']]==$linha['cod_usuario_emissor'])
          // convidado
          $saida[$linha['periodo']]['qtde_convidado']+=$linha['qtde'];
        else
          // aluno
          $saida[$linha['periodo']]['qtde_aluno']+=$linha['qtde'];
          
        $saida[$linha['periodo']]['qtde_total']+=$linha['qtde'];
      }
    }
  }
  else if ($agrupar=="mes")
  {
    $query  = "select FROM_UNIXTIME(data,'%m/%Y') 'periodo', cod_usuario_emissor, count(DISTINCT cod_msg) 'qtde'";
    $query .=  " from Correio_intermap";
    $query .= " where data>=".$inicio;
    $query .=   " and data<=".$fim;
    $query .= " group by periodo,cod_usuario_emissor order by data";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);

    $unixtime_inicio=$inicio;
    $unixtime_fim=$fim;

    while ($unixtime_inicio<$unixtime_fim)
    {
      $data=Unixtime2Mes($unixtime_inicio)."/".Unixtime2Ano($unixtime_inicio);
      $saida[$data]['qtde_total']+=0;
      $saida[$data]['qtde_formador']+=0;
      $saida[$data]['qtde_aluno']+=0;
      $saida[$data]['qtde_convidado']+=0;
      $unixtime_inicio=DiaMesAno2Unixtime(1,Unixtime2Mes($unixtime_inicio)+1,Unixtime2Ano($unixtime_inicio));
    }

    $c=0;
    if (count($lista)>0)
    {
      foreach ($lista as $cod => $linha)
      {
        if ($lista_formadores[$linha['cod_usuario_emissor']]==$linha['cod_usuario_emissor'])
          // formador
          $saida[$linha['periodo']]['qtde_formador']+=$linha['qtde'];
        else if ($lista_convidados[$linha['cod_usuario_emissor']]==$linha['cod_usuario_emissor'])
          // convidado
          $saida[$linha['periodo']]['qtde_convidado']+=$linha['qtde'];
        else
          // aluno
          $saida[$linha['periodo']]['qtde_aluno']+=$linha['qtde'];
        $saida[$linha['periodo']]['qtde_total']+=$linha['qtde'];
      }
    }
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

      $query = "select cod_usuario_emissor, count(DISTINCT cod_msg) 'qtde'";
      $query.=  " from Correio_intermap";
      $query.= " where data>=".$unixtime_inicio;
      $query.=   " and data<".$unixtime_prox;
      $query.= " group by cod_usuario_emissor";
      $res=Enviar($sock,$query);
      $lista=RetornaArrayLinhas($res);

      $saida[$data]['qtde_formador']=0;
      $saida[$data]['qtde_aluno']=0;
      $saida[$data]['qtde_total']=0;
      $saida[$data]['qtde_convidado']=0;
      $saida[$data]['fim_periodo']=Unixtime2Dia($unixtime_sem)."/".Unixtime2Mes($unixtime_sem)."/".Unixtime2Ano($unixtime_sem);

      if (count($lista)>0)
      {
        foreach($lista as $cod => $linha)
        {
          if ($lista_formadores[$linha['cod_usuario_emissor']]==$linha['cod_usuario_emissor'])
            // formador
            $saida[$data]['qtde_formador']+=$linha['qtde'];
          else if ($lista_convidados[$linha['cod_usuario_emissor']]==$linha['cod_usuario_emissor'])
            // convidado
            $saida[$data]['qtde_convidado']+=$linha['qtde'];
          else
            // aluno
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
            $data_inicio - Data em formato DD/MM/AAAA
            $data_fim - Data em formador DD/MM/AAAA
            $agrupar - dia, semana, mes
            $cod_usuario - codigo do usu�rio a ser feito o relat�rio
   Saida: array 
                [$periodo]['qtde']
                [$periodo]['fim_periodo'] - Somente se $agrupar="semana"
   Obs: $periodo - no formato DD/MM/AAAA (dia) DD/MM/AAAA (semana) ou MM/AAAA(ano)
*/
function RetornaQtdeMsgsPeriodoUsuario($sock,$data_inicio,$data_fim,$agrupar,$cod_usuario)
{
  global $data_invertida_g;

  $inicio=Data2UnixTime($data_inicio);
  $fim=Data2UnixTime($data_fim) + 24*60*60-1;

  if ($agrupar=="dia")
  {
    $query="select FROM_UNIXTIME(data,'%d/%m/%Y') 'periodo', count(DISTINCT cod_msg) 'qtde' from Correio_intermap where cod_usuario_emissor=".$cod_usuario." and data>=".$inicio." and data<=".$fim." group by periodo order by data";
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
    $query="select FROM_UNIXTIME(data,'%m/%Y') 'periodo', count(DISTINCT cod_msg) 'qtde' from Correio_intermap where cod_usuario_emissor=".$cod_usuario." and data>=".$inicio." and data<=".$fim." group by periodo order by data";
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

      $query="select count(DISTINCT cod_msg) 'qtde' from Correio_intermap where cod_usuario_emissor=".$cod_usuario." and data>=".$unixtime_inicio." and data<".$unixtime_prox;
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
            $data_inicio - Data em formato DD/MM/AAAA
            $data_fim - Data em formador DD/MM/AAAA
            $agrupar - dia, semana, mes
            $lista_usuarios - array com o c�digo dos usu�rios como indice (retorno de RetornaListaCodUsuarioNome)
   Saida: array 
                [$cod_usuario][$periodo]['qtde']
                [$cod_usuario][$periodo]['fim_periodo'] - Somente se $agrupar="semana"
   Obs: $periodo - no formato DD/MM/AAAA (dia) DD/MM/AAAA (semana) ou MM/AAAA(ano)
*/
function RetornaQtdeMsgsPeriodoListaUsuarios($sock,$data_inicio,$data_fim,$agrupar,$lista_usuarios)
{
  global $data_invertida_g;

  $inicio=Data2UnixTime($data_inicio);
  $fim=Data2UnixTime($data_fim) + 24*60*60-1;

  if ($agrupar=="dia")
  {
    $query="select FROM_UNIXTIME(data,'%d/%m/%Y') 'periodo', cod_usuario_emissor,count(DISTINCT cod_msg) 'qtde' from Correio_intermap where data>=".$inicio." and data<=".$fim." group by periodo,cod_usuario_emissor order by data";
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
        if (isset($lista_usuarios[$linha['cod_usuario_emissor']]))
          $saida[$linha['cod_usuario_emissor']][$linha['periodo']]['qtde']+=$linha['qtde'];
  }
  else if ($agrupar=="mes")
  {
    $query="select FROM_UNIXTIME(data,'%m/%Y') 'periodo', cod_usuario_emissor, count(DISTINCT cod_msg) 'qtde' from Correio_intermap where data>=".$inicio." and data<=".$fim." group by periodo,cod_usuario_emissor order by data";
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
        if (isset($lista_usuarios[$linha['cod_usuario_emissor']]))
          $saida[$linha['cod_usuario_emissor']][$linha['periodo']]['qtde']+=$linha['qtde'];
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

      $query="select cod_usuario_emissor,count(DISTINCT cod_msg) 'qtde' from Correio_intermap where data>=".$unixtime_inicio." and data<".$unixtime_prox." group by cod_usuario_emissor";
      $res=Enviar($sock,$query);
      $lista=RetornaArrayLinhas($res);

      if (count($lista)>0)
        foreach($lista as $cod => $linha)
        {
          if (isset($lista_usuarios[$linha['cod_usuario_emissor']]))
            $saida[$linha['cod_usuario_emissor']][$data]['qtde']+=$linha['qtde'];
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

?>
