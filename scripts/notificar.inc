<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/configurar/notificar.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
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

    Nied - Núcleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/configurar/notificar.inc
  ========================================================== */

/* ************************************************************************
   RetornaListaDeFrases - Retorna uma lista com todas as frases da
                          ferramenta escolhida. usada em conjunto com
                          RetornaFraseDaLista
   Entrada: $sock - Sock da base geral aberta
            $cod_ferramenta - cod_ferramenta a ser empregada
   Saida: Array com a lista de frases
*/
function RetornaListaDeFrases($sock, $cod_ferramenta, $cod_lingua)
{
  $query = "SELECT cod_texto, texto FROM Lingua_textos WHERE ";
  $query .= "cod_lingua=".$cod_lingua." AND ";
  $query .= "cod_ferramenta=".$cod_ferramenta." ";
  $query .= "ORDER BY cod_texto";

  if ($id_query = Enviar($sock, $query))
  {
    unset($textos);

    if (RetornaNumLinhas($id_query) > 0)
      while($linha = RetornaLinha($id_query))
        $textos[$linha["cod_texto"]] = $linha["texto"];
  }
  else
  {
    // Erro na execução da cláusula : terminar o programa
    $msg = "<H3>ERRO : Não foi possível executar a cláusula SQL<H3>";
    $msg .= "<BR><B>SQL : </B><BR>".$query;
    $msg .= "<BR><B>Mensagem de erro do Msql : </B><BR>".msql_error();
    $msg .= "<BR><a href=# onclick=history.go(-1);>Voltar</a>";
    die($msg);
  }

  return($textos);
}

/* ************************************************************************
   RetornaFraseDaLista - Retorna a frase pedida da lingua atual
   Entrada: $textos - Array com lista de frases (RetornaListaDeFrases)
            $cod_texto - cod_texto a ser empregado.
   Saida: Texto na lingua pedida
*/
function RetornaFraseDaLista($textos, $cod_texto)
{
  return(ConverteAspas2Html($textos[$cod_texto]));
}


function UltimoAcessoAmbiente($sock, $cod_usuario)
{

  $query = "select data from Curso_acessos where cod_usuario=".$cod_usuario." and cod_ferramenta is not null order by data desc limit 1";
  $res = Enviar($sock, $query);
  $lista = RetornaLinha($res);

  return($lista[0]);
}

/* *********************************************************************
   PertenceAoGrupo - Retorna trua se o usuario pertence ao grupo e o grupo está ativo
   Entrada: $sock - BASE DO CURSO
            $cod_usuario - codigo do usuario
            $cod_grupo_portfolio - codigo do grupo
   Saida: True se pertence, false caso contrario
*/
function PertenceAoGrupo($sock,$cod_usuario,$cod_grupo_portfolio)
{
  if ($cod_grupo_portfolio=="" || $cod_grupo_portfolio=="NULL")
    return false;
  $query="select GU.cod_usuario 'cod_usuario' from Grupos_usuario GU, Grupos G where GU.cod_grupo=".$cod_grupo_portfolio." and
 G.cod_grupo=".$cod_grupo_portfolio." and GU.cod_usuario=".$cod_usuario." and G.status<>'X'";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
    return true;
  else
    return false;
}


/* ********************************************************************************
   RetornaMaiorItemComentario - Retorna a maior data e o cod_usuario do item ou comentário
                                    dentro de um tópico ou subtópico
   Entrada: $sock - Conexão com a base interna
            $cod_topico - codigo do topico a ser checado
            $tipo_comp - tipo do compartilhamento (F ou T)
            $data - Data do tópico
            $cod_usuario - usuario logado
   Saida: maior data
*/
function RetornaMaiorItemComentario($sock,$cod_topico,$tipo_comp,$dados,$cod_usuario)
{
  $query="select cod_usuario,cod_grupo from Portfolio_topicos where cod_topico=".$cod_topico;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $cod_usuario_portfolio=$linha['cod_usuario'];
  $cod_grupo_portfolio=$linha['cod_grupo'];

  if ($cod_grupo_portfolio=="" && $cod_usuario==$cod_usuario_portfolio)
    $tipo_comp="P";
  else
    if (PertenceAoGrupo($sock,$cod_usuario,$cod_grupo_portfolio))
      $tipo_comp="P";

  $query="select cod_topico,cod_usuario,data from Portfolio_topicos where cod_topico_pai=".$cod_topico;
  if ($tipo_comp=="T")
    $query.=" and tipo_compartilhamento='T'";
  if ($tipo_comp=="F")
    $query.=" and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";

  $res=Enviar($sock,$query);
  $lista_topicos=RetornaArrayLinhas($res);

  // Verifica se existem subtópicos
  if (count($lista_topicos)>0)
  {
    foreach($lista_topicos as $cod=>$linha)
    {
      $dados_tmp = RetornaMaiorItemComentario($sock, $linha['cod_topico'], $tipo_comp, array('cod_usuario' => $linha['cod_usuario'], 'data' => $linha['data']), $cod_usuario);

      if ($dados_tmp['data'] > $dados['data'])
        $dados = $dados_tmp;
    }
  }

  $query="select Pi.cod_usuario 'PiUsuario', max(Pi.data) 'PiData', Pic.cod_comentarista 'PicUsuario', max(Pic.data) 'PicData' from Portfolio_itens Pi, Portfolio_itens_comentarios Pic where (Pi.cod_topico = ".$cod_topico.") and (Pi.cod_item = Pic.cod_item)";
  if ($tipo_comp=="T")
    $query.=" and Pi.tipo_compartilhamento='T'";
  if ($tipo_comp=="F")
    $query.=" and (Pi.tipo_compartilhamento='T' or Pi.tipo_compartilhamento='F')";

  $query .= " group by Pic.cod_item order by Pi.data desc limit 1";

  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  if ($linha['PiData'] > $dados['data'])
  {
    $dados['data'] = $linha['PiData'];
    $dados['cod_usuario'] = $linha['PiUsuario'];
  }
  if ($linha['PicData'] > $dados['data'])
  {
    $dados['data'] = $linha['PicData'];
    $dados['cod_usuario'] = $linha['PicUsuario'];
  }

  $query="select Pi.cod_usuario 'PiUsuario', Pi.data 'PiData' from Portfolio_itens Pi where Pi.cod_topico=".$cod_topico;
  if ($tipo_comp=="T")
    $query .= " and Pi.tipo_compartilhamento='T'";
  if ($tipo_comp=="F")
    $query .= " and (Pi.tipo_compartilhamento='T' or Pi.tipo_compartilhamento='F')";

  $query .= " order by Pi.data desc limit 1";

  $res = Enviar($sock,$query);

  $linha = RetornaLinha($res);


  if ($linha['PiData'] > $dados['data'])
  {
    $dados['data'] = $linha['PiData'];
    $dados['cod_usuario'] = $linha['PiUsuario'];
  }

  return $dados;
}


/* *********************************************************************
   RetornaNovidadeFerramentas - retorna um array com as datas das últimas
                                atualizações para o usuário dado.
   Entrada: $sock - BASE DO CURSO
            $cod_curso
            $cod_usuario
   Saida: array [$cod_ferramenta]['data'] - data ultimo acesso,
                                 ['cod_usuario'] - código do usuário
                                                   que realizou a alteração.
*/
function RetornaNovidadeFerramentas($sock,$cod_curso,$cod_usuario)
{
  $query = "select cod_coordenador from Cursos";
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);
  $cod_coordenador = $linha['cod_coordenador'];

  $saida[0]['data'] = "0"; /* para ser usado como retorno padrão */
  $saida[0]['cod_usuario'] = $cod_coordenador;

  // Se for visitante, nunca há novidade
  if (EVisitante($sock,$cod_curso,$cod_usuario))
    return $saida;

  $formador = false;
  $tipo_compart = "T";
  if (EFormador($sock,$cod_curso,$cod_usuario))
  {
    $formador = true;
  }

  // Pega as ferramentas de tratamento comum
  // Algumas ferramentas tem tratamento especial
  $query="select cod_ferramenta,data from Ferramentas_nova ";
  if (! $formador)
  {
    $query .= "where tipo_compartilhamento='".$tipo_compart."' ";
  }
  $query .= "order by cod_ferramenta";

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  foreach($lista as $cod => $linha)
  {
    $saida[$linha['cod_ferramenta']]['data'] = $linha['data'];
    $saida[$linha['cod_ferramenta']]['cod_usuario'] = null;
  }


  // 1 - Agenda
  $query = "select cod_usuario, data from Agenda_itens_historicos where acao = 'A'";
  if ($formador)
    $query .= " or acao = 'F' ";
  $query .= "order by data desc limit 1";

  $res=Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[1]['data'] = $linha['data'];
    $saida[1]['cod_usuario'] = $linha['cod_usuario'];
  }

  // 16 - Dinâmica
  $query = "select cod_usuario, data from Dinamica where status = 'A'";
  $res=Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[16]['data'] = $linha['data'];
    $saida[16]['cod_usuario'] = $linha['cod_usuario'];
  }

  // 3 - Atividades
  $query = "select cod_usuario, data from Atividade_itens where ";
  if (! $formador)
  {
    $query .= "tipo_compartilhamento = '".$tipo_compart."' and ";
  }
  $query .= "status = 'L' order by data desc limit 1";


  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[3]['data'] = $linha['data'];
    $saida[3]['cod_usuario'] = $linha['cod_usuario'];
  }

  // 4 - Material de Apoio
  $query = "select cod_usuario, data from Apoio_itens where ";
  if (! $formador)
  {
    $query .= "tipo_compartilhamento = '".$tipo_compart."' and ";
  }
  $query .= "status = 'L' order by data desc limit 1";

  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[4]['data'] = $linha['data'];
    $saida[4]['cod_usuario'] = $linha['cod_usuario'];
  }

  // 5 - Leituras
  $query = "select cod_usuario, data from Leitura_itens where ";
  if (! $formador)
  {
    $query .= "tipo_compartilhamento = '".$tipo_compart."' and ";
  }
  $query .= "status = 'L' order by data desc limit 1";

  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[5]['data'] = $linha['data'];
    $saida[5]['cod_usuario'] = $linha['cod_usuario'];
  }

  // 7 - Parada
  $query = "select cod_usuario, data from Obrigatoria_itens where ";
  if (! $formador)
  {
    $query .= "tipo_compartilhamento = '".$tipo_compart."' and ";
  }
  $query .= "status = 'L' order by data desc limit 1";

  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[7]['data'] = $linha['data'];
    $saida[7]['cod_usuario'] = $linha['cod_usuario'];
  }

  // 6 - Perguntas Freqüentes
  $query = "select data from Pergunta_itens where (cod_assunto != 1 and cod_assunto != 2) order by data desc limit 1";
  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[6]['data'] = $linha['data'];
    $saida[6]['cod_usuario'] = -1;
  }

  // 8 - Mural
  $query = "select cod_usuario, data from Mural where status = 'A' order by data desc limit 1";
  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[8]['data'] = $linha['data'];
    $saida[8]['cod_usuario'] = $linha['cod_usuario'];
  }

  // 9 - Fóruns de discussão
  $query = "select cod_usuario, data from Forum_mensagens where (status != 'X' and status != 'D') order by data desc limit 1";
  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[9]['data'] = $linha['data'];
    $saida[9]['cod_usuario'] = $linha['cod_usuario'];
  }

  // 13 - Perfil
  $query = "select cod_usuario, data from Perfil_usuarios order by data desc limit 1";
  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[13]['data'] = $linha['data'];
    $saida[13]['cod_usuario'] = $linha['cod_usuario'];
  }

  // 10 - Bate-papo
  $saida[10]['cod_usuario'] = -1; // Assume que foi admtele quem marcou a sessao

  // 12 - Grupos
  $saida[12]['cod_usuario'] = -1; // Assume que foi admtele quem criou o grupo

  // 0 - Administração
  if ($formador)
    $saida[12]['cod_usuario'] = -1; // Assume que foi admtele quem criou o grupo
  else
    $saida[12]['cod_usuario'] = null;

  // Correio (11)
  // Se tiver alguma mensagem com N, aparece a estrela

  $query="select C.cod_usuario, C.data from Correio_mensagens as C, Correio_destinos as Cd where (Cd.estado = 'N') and (Cd.cod_msg = C.cod_msg) and (Cd.cod_usuario = ".$cod_usuario.") and (C.cod_usuario != ".$cod_usuario.") order by C.data desc limit 1";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[11]['data'] = $linha['data'];
    $saida[11]['cod_usuario'] = $linha['cod_usuario'];
  }

  // Diario (14)
  if ($formador)
  {
    // Se formador, verifica se há itens ou comentários novos em qualquer usuário
    // Itens novos.
    $query = "select cod_usuario, data from Diario_itens where status='A' and tipo_compartilhamento <> 'P' order by data desc limit 1";
    $res = Enviar($sock,$query);
    if (RetornaNumLinhas($res) > 0)
    {
      $dados_itens = RetornaLinha($res);

      $dados_comentarios = array (0 => null, 'cod_comentarist' => 0,
                                  1 => 0, 'data' => 0);

      $query = "select cod_comentarist, data from Diario_comentarios where (status = 'A') and (cod_comentarist <> ".$cod_usuario.") order by data desc limit 1";
      $res = Enviar($sock, $query);
      if (RetornaNumLinhas($res) > 0)
      {
        $dados_comentarios = RetornaLinha($res);
      }

      if ($dados_comentarios['data'] > $dados_itens['data'])
      {
        $saida[14]['data'] = $dados_comentarios['data'];
        $saida[14]['cod_usuario'] = $dados_comentarios['cod_comentarist'];
      }
      else
      {
        $saida[14]['data'] = $dados_itens['data'];
        $saida[14]['cod_usuario'] = $dados_itens['cod_usuario'];
      }
    }
  }
  else
  {
    // Se for aluno, checa se há comentário novo em seu diário
    $query="select Dc.cod_comentarist, Dc.data from Diario_itens Di, Diario_comentarios Dc where (Di.cod_item = Dc.cod_item) and (Di.cod_usuario = ".$cod_usuario.") and (Dc.cod_comentarist != ".$cod_usuario.") and (Di.status = 'A') and (Dc.status = 'A') order by Dc.data desc limit 1";
    $res=Enviar($sock,$query);
    if (RetornaNumLinhas($res) > 0)
    {
      $linha = RetornaLinha($res);
      $saida[14]['data'] = $linha['data'];
      $saida[14]['cod_usuario'] = $linha['cod_comentarist'];
    }
  }

  // Portfolio e Portfolio de Grupos

  if ($formador)
    $saida[15]=RetornaMaiorItemComentario($sock,1,'F', array('cod_usuario' => $cod_usuario, 'data' => 0),$cod_usuario);
  else
    $saida[15]=RetornaMaiorItemComentario($sock,1,'T', array('cod_usuario' => $cod_usuario, 'data' => 0),$cod_usuario);

  return $saida;
}

/* *********************************************************************
   RetornaListaFerramentas - Retorna a lista de ferramentas
   Entrada: $sock - BASE EXTERNA
   Saida: array [$cod_ferramenta]['cod_texto_nome']
                [$cod_ferramenta]['cod_texto_descricao']
                [$cod_ferramenta]['diretorio']
*/
function RetornaListaFerramentas($sock)
{

  $query = "select cod_lingua, texto from Lingua_textos where (cod_ferramenta = -4) and (cod_texto = 37)";
  $res = Enviar($sock, $query);
  $adm_dados = RetornaArrayLinhas($res);

  $query ="select Lt.cod_lingua, F.cod_ferramenta, Lt.texto from Ferramentas as F, Lingua_textos as Lt where (Lt.cod_ferramenta = -4) and (F.cod_texto_nome = Lt.cod_texto) order by cod_ferramenta";
  $res = Enviar($sock,$query);
  $lista = RetornaArrayLinhas($res);
  unset($lista1);

  if (is_array($lista))
  {
    foreach($lista as $cod=>$linha)
    {
      $lista1[$linha['cod_lingua']][$linha['cod_ferramenta']]['texto']=$linha['texto'];
    }
    if (is_array($adm_dados))
    {
      foreach($adm_dados as $idx => $valor)
        $lista1[$valor['cod_lingua']][0]['texto'] = $adm_dados['texto'];
    }
  }
  return ($lista1);
}

/* *********************************************************************
   RetornaFerramentasCurso - Retorna a lista de ferramentas com status e
                             acessibilidade
   Entrada: $sock - BASE DO CURSO
   Saida: array [$cod_ferramenta]['status'] - D, F, A
                [$cod_ferramenta]['acesso_visitante'] - A ou N
*/
function RetornaFerramentasCurso($sock)
{
  $query ="select * from Curso_ferramentas order by cod_ferramenta";
  $res=Enviar($sock,$query);
  unset($lista1);
  if (RetornaNumLinhas($res)>0)
  {
    $lista=RetornaArrayLinhas($res);
    foreach($lista as $cod=>$linha)
    {
      $lista1[$linha['cod_ferramenta']]['status']=$linha['status'];
      $lista1[$linha['cod_ferramenta']]['acesso_visitante']=$linha['acesso_visitante'];
    }
  }
  return ($lista1);
}

?>
