<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/batepapo.inc

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
   ARQUIVO : cursos/aplic/intermap/batepapo.inc
   ========================================================== */

/* *********************************************************************
   RetornaAssuntoPeriodo- Retorna o(s) assunto(s) do per�odo dado
   Entrada: $sock - BASE DO CURSO
            $data_inicio - Data de In�cio
            $data_fim - Data de fim
   Saida: assunto(s) em uma string separada por v�rgulas ou
          (nenhum assunto definido) se n�o houver assunto
*/
function RetornaAssuntoPeriodo($sock,$data_inicio,$data_fim)
{
  global $lista_frases; /* Definida na arquivo de chamada */
  
  $di=$data_inicio;
  $df=$data_fim;

  $query ="select assunto from Batepapo_assuntos where ";
  $query.="(data_inicio<=".$di." and data_fim>=".$di.") or ";
  $query.="(data_inicio<=".$df." and data_fim>=".$df.") or ";
  $query.="(data_inicio>=".$di." and data_inicio<=".$df.") or ";
  $query.="(data_fim>=".$di." and data_fim<=".$df.")";

  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    $linha=RetornaLinha($res);
    $retorno=$linha[0];
    while ($linha=RetornaLinha($res))
      $retorno.=", ".$linha[0];
    return $retorno;
  }
  else
    // 3 - (Sess�o n�o agendada)
    return RetornaFraseDaLista($lista_frases,3);
}


/* ************************************************************************
   RetornaListaSessoes - Retorna lista das sess�es Ativas/Lixeira
   Entrada: $sock - BASE DO CURSO
   Saida: array []['cod_sessao'] - Codigo da sessao
                []['Assunto'] - Assunto da Sess�o
                []['DataInicio'] - Data de Inicio
                []['DataFim'] - Data de Fim
*/
function RetornaListaSessoes($sock)
{
  $query="select * from Batepapo_sessoes where status='A' order by data_inicio";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  $cont=0;
  if (count($lista)>0)
    foreach ($lista as $cod => $linha)
    {
      $retorno[$cont]['cod_sessao']=$linha['cod_sessao'];
      $retorno[$cont]['DataInicio']=$linha['data_inicio'];
      $retorno[$cont]['DataFim']=$linha['data_fim'];
      $retorno[$cont]['Assunto']=TruncaString(RetornaAssuntoPeriodo($sock,$linha['data_inicio'],$linha['data_fim']),80);    
      $cont ++;
    }
  return $retorno;
}

/* ************************************************************************
   RetornaSessao - Retorna dados da sessao
   Entrada: $sock - BASE DO CURSO
            $cod_sessao
   Saida: array ['cod_sessao'] - Codigo da sessao
                ['Assunto'] - Assunto da Sess�o
                ['DataInicio'] - Data de Inicio
                ['DataFim'] - Data de Fim
*/
function RetornaSessao($sock,$cod_sessao)
{
  $query="select * from Batepapo_sessoes where cod_sessao=".$cod_sessao;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $retorno['cod_sessao']=$linha['cod_sessao'];
  $retorno['DataInicio']=$linha['data_inicio'];
  $retorno['DataFim']=$linha['data_fim'];
  $retorno['Assunto']=RetornaAssuntoPeriodo($sock,$linha['data_inicio'],$linha['data_fim']);    
  return $retorno;
}

/* *********************************************************************
   RetornaMapaInteracaoSessao - Retorna a lista com informa��es de quem 
                                 mandou mensagem para quem na sess�o
   Entrada: $sock - BASE INTERNA
            $cod_sessao - codigo da sessao
            $lista_usuarios - lista no formador [$cod]=nome dos usu�rios a serem exibidos
            $manter_no_todos - True se o n� todos for aparecer no grafo.
   Saida: array [$cod_usuario_emissor][$cod_usuario_receptor]=qtde;
   Obs: Se manter_no_todos = false, cada msg enviada para todos � considerada como varias msg enviadas para cada um que recebeu
*/
function RetornaMapaInteracaoSessao($sock,$cod_sessao,$manter_no_todos)
{
  $query = "select cod_usuario, cod_usuario_r, count(*) 'qtde'".
            " from Batepapo_conversa".
           " where cod_sessao=".$cod_sessao.
             " and cod_fala>1".
           " group by cod_usuario";
  $res   = Enviar($sock, $query);

  if ($manter_no_todos)
  {
    while ($linha = RetornaLinha($res))
    {
      if (isset($linha[cod_usuario_r]))
      {
        // msg com destinatario especifico
        $saida[$linha[cod_usuario]][$linha[cod_usuario_r]] = $linha[qtde];
      }
      else
      {
        // msg para Todos
        $saida[$linha[cod_usuario]][0] = $linha[qtde];
      }
    }
  }
  else
  {
    $lista_usuarios_sessao=RetornaListaApelidos($sock,$cod_sessao);
    while ($linha = RetornaLinha($res))
    {
      if (isset($linha[cod_usuario_r]))
      {
        // msg com destinatario especifico
        $saida[$linha[cod_usuario]][$linha[cod_usuario_r]] = $linha[qtde];
      }
      else
      {
        // msg para Todos. Revertemos em varias msgs para cada usuario na sessao
        if (is_array($lista_usuarios_sessao))
        {
          foreach($lista_usuarios_sessao as $cod_usuario_apelido => $apelido)
          {
            $saida[ $linha[cod_usuario] ][ $cod_usuario_apelido ] += $linha[ qtde ];
          }
        }
      }
    }
  }

  return $saida;
  
/*
  $query = "select cod_usuario,cod_usuario_r,count(*) 'qtde'";
  $query.= "  from Batepapo_conversa";
  $query.= " where cod_sessao=".$cod_sessao;
  // cod_fala > -1 para n�o considerar msgs do tipo "Entra na sala", que n�o fazem parte da conversa
  $query.=   " and cod_fala>1";
  $query.= " group by cod_usuario";
  $query.= " order by cod_usuario";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  unset ($saida);

  if (is_array($lista))
  {
    foreach ($lista as $linha)
    {
      if (isset ($linha['cod_usuario_r']))
      {
        // msg para algum outro usuario especifico
        $saida[ $linha['cod_usuario'] ][ $linha['cod_usuario_r'] ] = $linha['qtde'];
      }
      else
      {
        // msg para todos
        $saida[ $linha['cod_usuario'] ][ 0 ] = $linha['qtde'];
      }
    }
  }

  // Se o n� todos n�o for aparecer, tem que transcrev�-lo para os demais usu�rios que
  // participaram da sessao
  if (!$manter_no_todos)
  {
    $lista_usuarios_sessao=RetornaListaApelidos($sock,$cod_sessao);
    if (count($saida)>0)
      foreach($saida as $cod_usuario_emissor => $linha)
        foreach ($linha as $cod_usuario_receptor => $qtde)
        {
          if ($cod_usuario_receptor==0 && count($lista_usuarios_sessao)>0)
          {
            foreach ($lista_usuarios_sessao as $cod_usu => $nome)
            {
              $saida[$cod_usuario_emissor][$cod_usu] ++;
            }
            unset($saida[$cod_usuario_emissor][0]);
          }
        }
  }
  return $saida;
*/
}

/* *********************************************************************
   RetornaListaApelidos - retorna a lista de apelidos 
   Entrada: $sock - BASE DO CURSO
            $cod_sessao - Codigo da sessao
   Saida: array [$cod_usuario]=apelido
*/
function RetornaListaApelidos($sock,$cod_sessao)
{
  $query="select cod_usuario,apelido from Batepapo_apelido where cod_sessao=".$cod_sessao." order by apelido";
  $res=Enviar($sock,$query);
  while ($linha=RetornaLinha($res))
    $lista[$linha[0]]=$linha[1];
  return $lista;
}

/* *********************************************************************
   RetornaQtdeMsgsUsuario - Retorna a lista de qts mensagens cada usu�rio enviou numa sess�o
   Entrada: $sock - BASE INTERNA
            $cod_sessao - codigo da sessao
            $lista_usuarios - Lista de usu�rio do curso, ou lista de usuarios que participaram do bate-papo
   Saida: array [$cod_usuario_emissor]=qtde;
   OBS: A saida contem a qtde de falas (zero ou mais) de todos os usuarios que participaram do batepapo
*/
function RetornaQtdeMsgsUsuario($sock,$cod_sessao,$lista_usuarios)
{
  // todos os usuarios que participaram da sessao
  $lista_apelidos = RetornaListaApelidos($sock,$cod_sessao);

  // todas as frases que apareceram na sessao de batepapo
  $query="select cod_usuario,count(*) 'qtde' from Batepapo_conversa where cod_sessao=".$cod_sessao." and cod_fala>1 group by cod_usuario order by cod_usuario";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (is_array($lista))
  {
    foreach($lista as $linha)
      if (isset($lista_usuarios[$linha['cod_usuario']]))
        $tmp[$linha['cod_usuario']]=$linha['qtde'];
  }

  if (is_array($lista_apelidos))
  {
    foreach($lista_apelidos as $cod_usuario => $apelido)
    {
      if (isset($tmp[ $cod_usuario ]))
        $saida[ $cod_usuario ] = $tmp[ $cod_usuario ];
      else
        $saida[ $cod_usuario ] = 0;
    }
  }
  
  return $saida;
}

/* *********************************************************************
   RetornaFluxoMensagens - Retorna a lista de time e usu�rio que enviou mensagens na sess�o
   Entrada: $sock - BASE INTERNA
            $cod_sessao - codigo da sessao
   Saida: array [$cod_usuario][$data]=cod_fala 
*/
function RetornaFluxoMensagens($sock,$cod_sessao)
{
  $query="select cod_usuario,cod_fala,data from Batepapo_conversa where cod_sessao=".$cod_sessao." order by cod_usuario,data";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0)
  {
    foreach($lista as $cod => $linha)
    {
      $saida[$linha['cod_usuario']][$linha['data']]=$linha['cod_fala'];
    }
  }
  return $saida;
}

/* ************************************************************************
   RetornaConversaSessaoPeriodo - Retorna mensagens escritas por um um usu�rio durante a sess�o no periodo dado
   Entrada: $sock - BASE DO CURSO
            $cod_sessao - codigo da sess�o
            $cod_usuario - usuario que escreveu as mensagens
            $data_inicio 
            $data_fim
   Saida: array ['Apelido'] - Apelido do Emissor
                ['ApelidoR'] - Apelido do Receptor
                ['cod_usuario'] - Codigo do usu�rio 
                ['cod_usuario_r'] - Codigo do usu�rio receptor
                ['cod_texto_fala'] - cod do texto da fala
                ['Mensagem'] - Mensagem
                ['Data'] - data da frase 

*/
function RetornaConversaSessao($sock,$cod_sessao,$cod_usuario,$data_inicio,$data_fim)
{
  global $lista_frases_batepapo;
  $falas=RetornaFalas($sock);
  $apelidos=RetornaListaApelidos($sock,$cod_sessao);

  $query="select * from Batepapo_conversa where cod_sessao=".$cod_sessao." and data>=".$data_inicio." and data<=".$data_fim." and cod_usuario=".$cod_usuario." order by data";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  $cont=0;
  if (count($lista)>0)
    foreach($lista as $cod => $linha)
    {
      $retorno[$cont]['Apelido']="???";
      $retorno[$cont]['cod_usuario']=$linha['cod_usuario'];
      foreach ($apelidos as $cod=>$apelido)
        if ($cod==$linha['cod_usuario'])
          $retorno[$cont]['Apelido']=$apelido;

      $retorno[$cont]['cod_usuario_r']=$linha['cod_usuario_r'];
      /* 20 - Todos */
      $retorno[$cont]['ApelidoR']=RetornaFraseDaLista($lista_frases_batepapo,20);
      if ($linha['cod_usuario_r']!="")
        foreach ($apelidos as $cod=>$apelido)
          if ($cod==$linha['cod_usuario_r'])
            $retorno[$cont]['ApelidoR']=$apelido;

      $retorno[$cont]['cod_texto_fala']="???";
      foreach ($falas as $cod=>$fala)
        if ($cod==$linha['cod_fala'])
          $retorno[$cont]['cod_texto_fala']=$fala;
      $retorno[$cont]['Mensagem']=Enter2Br($linha['mensagem']);
      $retorno[$cont]['Data']=$linha['data'];
      $cont ++;
    }
  return ($retorno);
}

/* *********************************************************************
   RetornaFalas - retorna a lista de falas
   Entrada: $sock - BASE DO CURSO
   Saida: array [$cod_fala]=cod_texto_fala
*/
function RetornaFalas($sock)
{
  if (!isset($lista_falas_s))
  {
    $query="select * from Batepapo_fala";
    $res=Enviar($sock,$query);

    while ($linha=RetornaLinha($res))
      $lista[$linha[0]]=$linha[1];
    $lista_falas_s=$lista;
  }
  else
    $lista=$lista_falas_s;
  return $lista;
}


?>
