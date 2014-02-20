<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/batepapo.inc

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
/*==========================================================
  ARQUIVO : cursos/aplic/batepapo/batepapo.inc
  ========================================================== */


session_register("lista_falas_s");

/* *********************************************************************
   RetornaSessaoCorrente- Retorna o nï¿½mero da sessao corrente.
                          ï¿½ sempre a ï¿½ltima sessï¿½o + 1
   Entrada: $sock - BASE DO CURSO
   Saida: nï¿½mero da sessï¿½o corrente
*/
function RetornaSessaoCorrente($sock)
{
  $query="select max(cod_sessao) from Batepapo_sessoes";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    $linha=RetornaLinha($res);
    if ($linha[0]=="")
      $linha[0]=0;
  }
  else
    $linha[0]=0;
  return ($linha[0]+1);
}

/* *********************************************************************
IniciaCriacaoAvaliacao - Inicia a criaï¿½o de uma nova avaliacao
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

  /* data_termino = tempo atï¿½ o exato instante, menos o que jah foi de hoje, somado a 23h59m59s */
  /* data_termino = hoje as 23h59m59s */ 
  $data_termino = time() - (time()%86400) + 86399;
  $consulta="insert into ".$tabela." (cod_avaliacao,cod_atividade,cod_usuario,ferramenta,tipo,status,data,data_inicio,data_termino,inicio_edicao) values (".$cod_avaliacao.", ".$cod_atividade.", ".$cod_formador.", '".$ferramenta."', '".$tipo."', 'L',".time().",".time().",".$data_termino.",".time().")";
  $res=Enviar($sock, $consulta);
  $consulta="insert into ".$tabela."_historicos values (".$cod_avaliacao.", ".$cod_formador.", ".time().", 'C')";
  $res=Enviar($sock, $consulta);

  return($cod_avaliacao);
}


/* *********************************************************************
AtualizaCadastroAvaliacao - Atualiza os campos objetivos, criterios, valor, a data de inicio, data de termino de uma avaliaï¿½o
Entrada: $sock - BASE DO CURSO
         $tabela - tabela a ser usada (Avaliacoes)
         $cod_formador - Cdigo do formador que cadastrou
         $objetivos - objetivos da avaliaï¿½o
         $criterios - criterios da avaliaï¿½o
         $valor - Valor da avaliaï¿½o
         $data_inicio - Data de inï¿½io da Atividade
         $data_termino - Data de termino da Atividade
         $cod_atividade - cdigo que identifica a atividade em que a avaliaï¿½o irï¿½atuar

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
   RetornaAssuntoSessao- Retorna o(s) assunto(s) da sessï¿½o dada
   Entrada: $sock - BASE DO CURSO
            $cod_sessao - cï¿½digo da sessï¿½o
   Saida: assunto(s) em uma string separada por vï¿½rgulas ou
          (nenhum assunto definido) se nï¿½o houver assunto
*/
function RetornaAssuntoSessao($sock,$cod_sessao)
{
  global $lista_frases; /* Definida na arquivo de chamada */

  /* Busca o perï¿½odo da sessï¿½o */
  $query="select data_inicio,data_fim from Batepapo_sessoes where cod_sessao=".$cod_sessao;
  $res=Enviar($sock,$query);

  if (RetornaNumLinhas($res)>0)
  {
    /* Se a sessï¿½o existe (nï¿½o ï¿½ a atual), varre tabela */
    $linha=RetornaLinha($res);

    $di=$linha[0];
    $df=$linha[1];

    $query ="select assunto from Batepapo_assuntos where ";
    $query.="(data_inicio<=".$di." and data_fim>=".$di.") or ";
    $query.="(data_inicio<=".$df." and data_fim>=".$df.") or ";
    $query.="(data_inicio>=".$di." and data_inicio<=".$df.") or ";
    $query.="(data_fim>=".$di." and data_fim<=".$df.")";
  }
  else
  {
    /* senï¿½o, vï¿½ assunto atual */
    $query ="select assunto from Batepapo_assuntos where ";
    $query.="data_inicio<=".time()." and data_fim>=".time();
  }
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
    /* 5 - (Sessï¿½o nï¿½o agendada) */
    return RetornaFraseDaLista($lista_frases,5);
}

/* *********************************************************************
   RetiraUsuarioSala - Retira o usuï¿½rio da sala
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_sessao - codigo da sessao
            $cod_usuario - codigo do usuario
   Saida: $sock - Base do Curso
*/
function RetiraUsuarioSala($sock,$cod_curso,$cod_sessao,$cod_usuario)
{
  InsereConversa($sock,$cod_curso,$cod_sessao,$cod_usuario,"",-1,"","");
  $query="delete from Batepapo_online where cod_usuario=".$cod_usuario;
  Enviar($sock,$query);
}


/* *********************************************************************
   VerificaOnline - Retorna true se tiver alguï¿½m na sala
   Entrada: $sock - BASE DO CURSO
   Saida: true - caso haja alguem na sala
          false - caso nï¿½o haja ninguï¿½m
*/
function VerificaOnline($sock)
{
  /* 1min30s sem aviso tira o cara */
  $data=time() - 90;
  $query="select cod_usuario,data from Batepapo_online";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    return true;
  }
  else
    return false;
}

function UsuarioOnline($sock, $cod_usuario){
  $query="select cod_usuario from Batepapo_online where cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)==0){
    return false;
  }
  return true;
}
/* *********************************************************************
   VerificaRetiradaOnline - Verifica se tem alguï¿½m a ser retirado
   Entrada: $sock - BASE DO CURSO
   Saida: true - caso alguem tenha que ser retirado
          false - caso ninguï¿½m tenha que ser retirado
*/
function VerificaRetiradaOnline($sock)
{
  /* 1min30s sem aviso tira o cara */
  $data=time() - 90;
  $query="select cod_usuario,data from Batepapo_online where data<".$data;
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    return true;
  }
  else
    return false;
}

/* *********************************************************************
   LimpaOnline - retira aqueles sem marcaï¿½ï¿½o de acesso nos ï¿½ltimos
                 $tempo minutos
   Entrada: $sock - BASE DO CURSO, $tempo: tempo para retirar o cara
   Saida: $sock - BASE DO CURSO
*/
function LimpaOnline($sock,$cod_curso, $tempo)
{
  /* $tempo sem aviso tira o cara */
  $data=time() - $tempo;
  $query="select cod_usuario,data from Batepapo_online";
  $res=Enviar($sock,$query);
  $cont = 0;
  $cod_user_retirados = array();
  if (RetornaNumLinhas($res)>0)
  {
    $cod_sessao=RetornaSessaoCorrente($sock);
    $lista=RetornaArrayLinhas($res);
    foreach ($lista as $cod => $linha)
      if ($linha[1]<$data)
        RetiraUsuarioSala($sock,$cod_curso,$cod_sessao,$linha[0]);
        $cod_user_retirados[$cont] = $linha[0];
        $cont++; 
  }
  return($cod_user_retirados);
}


/* *********************************************************************
   EncerraSessao - Encerra a sessï¿½o passada, se a mesma nï¿½o foi encerrada
                   Limpa a tabela externa.
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_sessao - codigo da Sessao
   Saida: $sock - com BASE DO CURSO - Necessï¿½rio pois ï¿½ necessï¿½rio acessar
                  outra base.
*/
function EncerraSessao($sock,$cod_curso,$cod_sessao)
{
  if (RetornaSessaoCorrente($sock)==$cod_sessao)
  {
    /* Sï¿½ faz sentido encerrar uma sessï¿½o que esteja ocorrendo agora */
    $query="select min(data),max(data) from Batepapo_conversa where cod_sessao=".$cod_sessao;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    /* Verifica se a sessao ocorreu de fato, senï¿½o, nï¿½o hï¿½ tempo minimo e
       maximo */
    if ($linha[0]!="" && $linha[1]!="")
    {
      $query="insert into Batepapo_sessoes (data_inicio,data_fim,status) values (".$linha[0].",".$linha[1].",'A')";
      Enviar($sock,$query);
    }

    $query="delete from ".$_SESSION['dbnamebase'].".Batepapo_sessoes_correntes where cod_curso=".$cod_curso." and cod_sessao=".$cod_sessao;
    Enviar($sock,$query);
//     Desconectar($sock);
//     $sock=Conectar($cod_curso);
  }
  return ($sock);
}

/* *********************************************************************
   RetornaApelido - Retorna o apelido do usuario em dada sessao
   Entrada: $sock - BASE DO CURSO
            $cod_sessao - codigo da Sessao
            $cod_usuario - codigo da usuario
   Saida: Apelido do usuario
*/
function RetornaApelido($sock,$cod_sessao,$cod_usuario)
{
  $query="select apelido from Batepapo_apelido where cod_sessao=".$cod_sessao." and cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}

/* *********************************************************************
   ManterOnline - Mantem o usuï¿½rio online na sala de batepapo
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo da usuario
   Saida: nenhuma
*/
function ManterOnline($sock,$cod_usuario)
{
  $query="select cod_usuario from Batepapo_online where cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)==0)
    $query="insert into Batepapo_online values (".$cod_usuario.",".time().")";
  else
    $query="update Batepapo_online set data=".time()." where cod_usuario=".$cod_usuario;
  Enviar($sock,$query);
}

/* *********************************************************************
   CadastrarApelido - coloca o apelido usado pelo usuï¿½rio na sessï¿½o
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo da usuario
            $cod_sessao - codigo da sessao
            $apelido - Apelido a ser cadastrado
   Saida: nenhuma
*/
function CadastrarApelido($sock,$cod_usuario,$cod_sessao,$apelido)
{
  $apelido_atual=RetornaApelido($sock,$cod_sessao,$cod_usuario);

  $apelido = addslashes($apelido);

  if ($apelido_atual=="")
  {
    /* Se nï¿½o tem apelido, insere */
    $query="insert into Batepapo_apelido values (".$cod_sessao.",".$cod_usuario.",'".VerificaStringQuery($apelido)."')";
    Enviar($sock,$query);
  }
  else if ($apelido!=$apelido_atual)
  {
    /* Sï¿½ altera se for diferente */
    $query="update Batepapo_apelido set apelido='".$apelido."' where cod_usuario=".$cod_usuario." and cod_sessao=".$cod_sessao;
    Enviar($sock,$query);
  }
}

/* *********************************************************************
   RetornaListaApelidosOnline - retorna a lista de apelidos online
   Entrada: $sock - BASE DO CURSO
            $cod_sessao - Codigo da sessao
   Saida: array [$cod_usuario]=apelido
*/
function RetornaListaApelidosOnline($sock,$cod_sessao)
{
  $query="select a.cod_usuario,a.apelido from Batepapo_apelido a, Batepapo_online o where a.cod_sessao=".$cod_sessao." and a.cod_usuario=o.cod_usuario order by a.apelido";
  $res=Enviar($sock,$query);
  while ($linha=RetornaLinha($res))
    $lista[$linha[0]]=$linha[1];
  return $lista;
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

/* *********************************************************************
   InsereConversa - insere frase na sessao
   Entrada: $sock - BASE DO CURSO
            $cod_curso - codigo do curso
            $cod_sessao - Codigo da sessao
            $cod_usuario - Codigo do usuario emissor
            $cod_usuario_r - Codigo do usuario Receptor
            $cod_fala - Codigo da fala
            $mensagem - Mensagem
   Saida: $sock - Base do Curso - Necessï¿½rio pois troca de base
*/
function InsereConversa($sock,$cod_curso,$cod_sessao,$cod_usuario,$cod_usuario_r,$cod_fala,$fala,$mensagem)
{
  global $lista_frases; /* Definida pela tela que a chama */
  $tempo_mensagem=time();
  $lista_falas=RetornaFalas($sock);
  $lista_apelidos=RetornaListaApelidos($sock,$cod_sessao);

  if(!(UsuarioOnline($sock, $cod_usuario) )){
    return;
  }

  if ($cod_fala==-1)
  {
    /* Se estï¿½ saindo da sala, tenho que verificar se o tempo da mensagem
       anterior mais antiga, para ver se nï¿½o foi uma sessï¿½o interrompida
       abruptamente (perguntar ao Diogo, se tiver dï¿½vidas....) */
    $query="select max(data) from Batepapo_conversa where cod_sessao=".$cod_sessao;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    if ($tempo_mensagem>$linha[0]+600)
    /* Ou seja, Ninguï¿½m estï¿½ interagindo a mais de 10 min,
       entï¿½o tenho que supor que a sessï¿½o foi abruptamente interrompida. */
    {
      $tempo_mensagem=$linha[0]+1;
    }
  }
  //se nao esta saindo da sala, entÃ£o atualiza a data na tabela Batepapo_online
  else{
    $query = "update Batepapo_online set data = ".$tempo_mensagem." where cod_usuario = ".$cod_usuario;
    Enviar($sock, $query);
  }

  $mensagem=Enter2Br(LimpaConteudo(EliminaScript($mensagem)));

  if (isset($cod_usuario_r) && $cod_usuario_r!="")
  {
    $query="insert into Batepapo_conversa values (".$cod_sessao.",".$cod_usuario.",".$cod_usuario_r.",".$cod_fala.",'".$mensagem."',".$tempo_mensagem.")";
    Enviar($sock,$query);
    $apelido_r=$lista_apelidos[$cod_usuario_r];
    $cod_usuario_r=$cod_usuario_r;
  }
  else
  {
    $query="insert into Batepapo_conversa values (".$cod_sessao.",".$cod_usuario.",NULL,".$cod_fala.",'".VerificaStringQuery($mensagem)."',".$tempo_mensagem.")";
    Enviar($sock,$query);
    /* 20 - Todos */
    $apelido_r=RetornaFraseDaLista($lista_frases,20);
    $cod_usuario_r="NULL";
  }

  foreach($lista_apelidos as $cod => $linha)
    if ($cod==$cod_usuario)
      $apelido=$linha;

//   Desconectar($sock);
//   $sock=Conectar("");

  $cod_texto_fala=0;

  foreach($lista_falas as $cod=>$linha)
    if ($cod==$cod_fala)
      $cod_texto_fala=$linha;

  $query ="insert into ".$_SESSION['dbnamebase'].".Batepapo_sessoes_correntes values (";
  $query.=$cod_curso.",".$cod_sessao.",".$cod_usuario.",";
  $query.="'".VerificaStringQuery($apelido)."',";
  $query.=$cod_usuario_r.",'".VerificaStringQuery($apelido_r)."',";
  $query.=$cod_texto_fala.",'";
  $query.=VerificaStringQuery($mensagem)."',".$tempo_mensagem.",'".$fala."')";
  Enviar($sock,$query);

}

/* *********************************************************************
   RetornaAssuntoPeriodo- Retorna o(s) assunto(s) do perï¿½odo dado
   Entrada: $sock - BASE DO CURSO
            $data_inicio - Data de Inï¿½cio
            $data_fim - Data de fim
   Saida: assunto(s) em uma string separada por vï¿½rgulas ou
          (nenhum assunto definido) se nï¿½o houver assunto
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
      $retorno.="<br/>".$linha[0];
    return $retorno;
  }
  else
    /* 5 - (Sessï¿½o nï¿½o agendada) */
    return RetornaFraseDaLista($lista_frases,5);
}

/* ************************************************************************
   RetornaListaSessoes - Retorna lista das sessï¿½es Ativas/Lixeira
   Entrada: $sock - BASE DO CURSO
            $status - 'A' - Ativo, 'L' Lixeira, 'X' Excluido
   Saida: array []['cod_sessao'] - Codigo da sessao
                []['Assunto'] - Assunto da Sessï¿½o
                []['DataInicio'] - Data de Inicio
                []['DataFim'] - Data de Fim

*/
function RetornaListaSessoes($sock,$status)
{
  $query="select * from Batepapo_sessoes where status='".$status." 'order by data_inicio";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  $cont=0;
  if (count($lista)>0)
    foreach ($lista as $cod => $linha)
    {
      $retorno[$cont]['cod_sessao']=$linha['cod_sessao'];
      $retorno[$cont]['DataInicio']=$linha['data_inicio'];
      $retorno[$cont]['DataFim']=$linha['data_fim'];
      $retorno[$cont]['Assunto']=RetornaAssuntoPeriodo($sock,$linha['data_inicio'],$linha['data_fim']);
      $cont ++;
    }
  return $retorno;
}

/* ************************************************************************
   RetornaSessao - Retorna dados da sessao
   Entrada: $sock - BASE DO CURSO
            $cod_sessao
   Saida: array ['cod_sessao'] - Codigo da sessao
                ['Assunto'] - Assunto da Sessï¿½o
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
  $retorno['Status']=$linha['status'];
  $retorno['Assunto']=RetornaAssuntoPeriodo($sock,$linha['data_inicio'],$linha['data_fim']);
  return $retorno;
}

/* ************************************************************************
   RetornaConversaSessao - Retorna as conversas realizadas durante a sessï¿½o
   Entrada: $sock - BASE DO CURSO
            $cod_sessao - codigo da sessï¿½o
   Saida: array ['Apelido'] - Apelido do Emissor
                ['ApelidoR'] - Apelido do Receptor
                ['cod_usuario'] - Codigo do usuï¿½rio
                ['cod_usuario_r'] - Codigo do usuï¿½rio receptor
                ['cod_texto_fala'] - cod do texto da fala
                ['Mensagem'] - Mensagem
                ['Data'] - data da frase

*/
function RetornaConversaSessao($sock,$cod_sessao)
{
  global $lista_frases;
  $falas=RetornaFalas($sock);
  $apelidos=RetornaListaApelidos($sock,$cod_sessao);

  $query="select * from Batepapo_conversa where cod_sessao=".$cod_sessao." order by data";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  $cont=0;
  if (count($lista)>0)
    foreach($lista as $cod => $linha)
    {
      $retorno[$cont]['Apelido']="???";
      $retorno[$cont]['cod_usuario']=$linha['cod_usuario'];
      if (count($apelidos)>0)
        foreach ($apelidos as $cod=>$apelido)
          if ($cod==$linha['cod_usuario'])
            $retorno[$cont]['Apelido']=$apelido;

      $retorno[$cont]['cod_usuario_r']=$linha['cod_usuario_r'];
      /* 20 - Todos */
      $retorno[$cont]['ApelidoR']=RetornaFraseDaLista($lista_frases,20);
      if ($linha['cod_usuario_r']!="")
        if (is_array($apelidos))
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

/* ************************************************************************
   RetornaListaSessoesMarcadas - Retorna lista de sessï¿½es marcadas que nï¿½o
                                 ocorreram/terminaram ainda
   Entrada: $sock - BASE DO CURSO
   Saida: array ['cod_assunto'] - codigo do assunto
                ['assunto'] - assunto
                ['data_inicio'] - data de inicio
                ['data_fim'] - data de termino
*/
function RetornaListaSessoesMarcadas($sock)
{
  $query="select * from Batepapo_assuntos where data_fim>".time()." order by data_inicio";
  $res=Enviar($sock,$query);
  return RetornaArrayLinhas($res);
}

/* ************************************************************************
   AtualizaStatusSessao - Atualiza o status da sessï¿½o
   Entrada: $sock - BASE DO CURSO
            $cod_sessoes - array com as sessoes a serem apagadas
            $status - Status a ser colocado
   Saida: Nenhuma
*/
function AtualizaStatusSessao ($sock,$cod_sessoes,$status)
{
  $where=implode(" or cod_sessao=",$cod_sessoes);
  $query="update Batepapo_sessoes set status='".$status."' where cod_sessao=".$where;
  Enviar($sock,$query);
}

/* *********************************************************************
   InsereBPAssuntos - realiza insercoes de infos da sessao na tabela Batepapo_assuntos
   entrada: $sock - sock de conexao
            $assunto - string de assunto
            $data_iniUT - data em UT de inicio da sessao
            $data_fimUT - data em UT de fim da sessao
   saida: nenhuma
*/
function InsereBPAssuntos($sock, $assunto, $data_iniUT, $data_fimUT)
{
  $query="insert into Batepapo_assuntos (assunto, data_inicio, data_fim) values ('".VerificaStringQuery(LimpaTitulo($assunto))."',".$data_iniUT.",".$data_fimUT.")";
  Enviar($sock,$query);
}

/* *********************************************************************
   RetornaCodAssuntoBatePapo- Retorna o codigo do assunto da sessï¿½o dada
   Entrada: $sock - BASE DO CURSO
            $cod_sessao - cï¿½digo da sessï¿½o
   Saida: cod_assunto
*/
function RetornaCodAssuntoBatePapo($sock,$cod_sessao)
{
  global $lista_frases; /* Definida na arquivo de chamada */

  /* Busca o perï¿½odo da sessï¿½o */
  $query="select data_inicio,data_fim from Batepapo_sessoes where cod_sessao=".$cod_sessao;
  $res=Enviar($sock,$query);

  /* Se a sessï¿½o existe (nï¿½o ï¿½ a atual), varre tabela */
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

/* ************************************************************************
   RetornaAssunto - Retorna o assunto da sessao
   Entrada: $sock - BASE DO CURSO
            $cod_assunto
   Saida: ['Assunto'] - Assunto da Sessï¿½o
*/
function RetornaAssunto($sock,$cod_assunto)
{
  $query="select assunto from Batepapo_assuntos where cod_assunto=".$cod_assunto;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha[0];
}


/* ********************************************************************************
   RetornaCodAssuntoSessao - Retorna o cï¿½digo do assunto da sessï¿½o de bate-papo marcada
   Entrada:  $sock - BASE DO CURSO
            $assunto - Assunto da sessï¿½o de bate-papo
            $data_inicio - Data de inicio da atividade
            $data_fim - Data de termino da atividade
   Saida: Nenhuma

*/
function RetornaCodAssuntoSessao($sock,$data_inicio,$data_fim)
{
  $query = "select cod_assunto from Batepapo_assuntos where data_inicio = ".$data_inicio." and data_fim=".$data_fim."";
  $res = Enviar($sock, $query);
  $codigo = RetornaLinha($res);

  return($codigo[0]);
}


/* ********************************************************************************
   RetornaCodAssunto - Retorna o cï¿½digo do assunto da sessï¿½o de bate-papo a ser avaliada
   Entrada:  $sock - BASE DO CURSO
            $assunto - Assunto da sessï¿½o de bate-papo
            $di - Data de inicio da atividade
            $df - Data de termino da atividade
   Saida: Nenhuma

*/
function RetornaCodAssunto($sock,$assunto,$di,$df)
{
  $query="select cod_assunto from Batepapo_assuntos where ";
  $query.="(assunto='".$assunto."' and data_inicio<=".$di." and data_fim>=".$di.") or ";
  $query.="(assunto='".$assunto."' and data_inicio<=".$df." and data_fim>=".$df.") or ";
  $query.="(assunto='".$assunto."' and data_inicio>=".$di." and data_inicio<=".$df.") or ";
  $query.="(assunto='".$assunto."' and data_fim>=".$di." and data_fim<=".$df.")";
  $res=Enviar($sock,$query);
  $cod_assunto=RetornaLinha($res);
  return $cod_assunto[0];
}


/* ************************************************************************
   RetornaConversaAluno - Retorna as falas de um aluno durante a sessï¿½o
   Entrada: $sock - BASE DO CURSO
            $cod_assunto - codigo do assunto da sessï¿½o
            $cod_aluno - codigo do aluno
   Saida: array ['Apelido'] - Apelido do Emissor
                ['ApelidoR'] - Apelido do Receptor
                ['cod_usuario'] - Codigo do usuï¿½rio
                ['cod_usuario_r'] - Codigo do usuï¿½rio receptor
                ['cod_texto_fala'] - cod do texto da fala
                ['Mensagem'] - Mensagem
                ['Data'] - data da frase

*/
function RetornaConversaAluno($sock,$cod_assunto,$cod_aluno)
{
  global $lista_frases;
  $falas=RetornaFalas($sock);

  $lista_sessoes  = RetornaSessoesDeUmAssunto($sock,$cod_assunto);
  $cont=0;
  foreach($lista_sessoes as $cnt => $sessao)
  {
    $apelidos=RetornaListaApelidos($sock,$sessao['cod_sessao']);
    $query="select * from Batepapo_conversa where cod_sessao=".$sessao['cod_sessao']." and cod_usuario=".$cod_aluno." and cod_fala>1 order by data";
    $res=Enviar($sock,$query);
    $lista=RetornaArrayLinhas($res);
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
      $retorno[$cont]['ApelidoR']=RetornaFraseDaLista($lista_frases,20);
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
  }
  return ($retorno);
}


/* ********************************************************************************
  RetornaSessoesDeUmAssunto - Retorna todas as sessï¿½es de bate-papo que possuem um mesmo assunto
   Entrada:  $sock - BASE DO CURSO
            $assunto - Assunto da sessï¿½o de bate-papo
   Saida: array [$cod_usuario]['cod_sessao'] - cï¿½digo da sessï¿½o
*/
function RetornaSessoesDeUmAssunto($sock,$cod_assunto)
{
  $query="select data_inicio,data_fim from Batepapo_assuntos where cod_assunto=".$cod_assunto;
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
  $cont=0;
  if (count($lista)>0)
    foreach ($lista as $cod => $linha)
    {
      $retorno[$cont]['cod_sessao']=$linha['cod_sessao'];
      $cont ++;
    }
  return $retorno;
}

/*********************************************************************
     RetornaListaApelidosOnlineDinamic - usado para atualizar a lista
                                         de usuários do bate-papo
     Entrada: $cod_curso   - Codigo do curso
              $cod_sessao  - Codigo da sessao
              $cod_usuario - Codigo do usuário atual para não incluir 
                             na lista. 
 */
function RetornaListaApelidosOnlineDinamic($cod_curso, $cod_sessao, $cod_usuario) {

  $objResponse = new xajaxResponse('ISO-8859-1');
  $sock = Conectar($cod_curso);
  
  $lista_apelidos = RetornaListaApelidosOnline($sock, $cod_sessao);
  
  unset($lista_apelidos[$cod_usuario]);
  Desconectar($sock);

  $objResponse->addScriptCall("AtualizaLista", $lista_apelidos);
  return $objResponse->getXML();
}

/*********************************************************************
     MudarStatusDinamic - Muda o status das sessï¿½es, dinamicamente
     Entrada: $cod_curso - codigo do curso
              $cod_sessao - array com o codigo das sessï¿½es
              $status - status que a sessï¿½o receberï¿½
 
     Saida: XML da funï¿½ï¿½o Ajax
 */
function MudarStatusDinamic($cod_curso, $cod_sessao, $status){

  $objResponse = new xajaxResponse('ISO-8859-1');

  $sock=Conectar($cod_curso);

  foreach($cod_sessao as $cod => $linha){
    $query="update Batepapo_sessoes set status='".$status."' where cod_sessao=".$linha;
    Enviar($sock,$query);
  }

  Desconectar($sock);

  $objResponse->addScript("Recarregar('".$status."');");

  return $objResponse->getXML();
}

/**************************************************************************
     DesmarcaSessaoDinamic - Desmarca as sessï¿½es marcadas e exclui a
                             avaliaï¿½ï¿½o marcada nesta sessï¿½o, dinamicamente
     Entrada: $cod_curso - codigo do curso
              $array_cod_assunto - array com o codigo dos assuntos

     Saida: XML da funï¿½ï¿½o Ajax
 */
function DesmarcaSessaoDinamic($cod_curso, $array_cod_assunto, $array_cod_usuario)
{
  $objResponse = new xajaxResponse('ISO-8859-1');
  $sock=Conectar($cod_curso);

  for($i=0;$i<count($array_cod_assunto);$i++)
  {
    $query="delete from Batepapo_assuntos where cod_assunto=".$array_cod_assunto[$i];
    Enviar($sock,$query);
    ExcluiAvaliacaoDesmarcada($sock,$array_cod_assunto[$i],$cod_usuario);
  }

  AtualizaFerramentasNova($sock, 10, 'T');
  Desconectar($sock);

  $objResponse->addScript("Recarregar();");

  return $objResponse->getXML();
}

/* *********************************************************************
   ExpulsaVisitante - verifica se o usuario eh visitante e impede seu
                      acesso se for
   entrada: $sock        - sock de conexao
            $cod_curso   - código do curso atual.
            $cod_usuario - código do usuário o qual pretende-se testar o
                           acesso à ferramenta correio.
            $eh_popup    - booleano que indica se a página que o chamou
                           é ou não um popup. Se não for, devemos fechar
                           as tags html do template comum de uma página
                           de ferramenta (com rodapé, fechando o tabelão,
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

    /* 1 - BatePapo 504 - Área restrita a alunos e formadores */
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