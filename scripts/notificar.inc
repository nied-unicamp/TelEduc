<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/configurar/notificar.inc

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
  ARQUIVO : cursos/aplic/configurar/notificar.inc
  ========================================================== */

/* ************************************************************************
   RetornaListaDeFrases - Retorna uma lista com todas as frases da
                          ferramenta escolhida. usada em conjunto com
                          RetornaFraseDaListaNotificar
   Entrada: $sock - Sock da base geral aberta
            $cod_ferramenta - cod_ferramenta a ser empregada
   Saida: Array com a lista de frases
*/
function RetornaListaDeFrasesNotificar($sock, $cod_ferramenta, $cod_lingua)
{
  $query  = "SELECT cod_texto, texto FROM Lingua_textos WHERE ";
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
    // Erro na execu��o da cl�usula : terminar o programa
    $msg = "<h3>ERRO : N&atilde;o foi poss&iacute;vel executar a cl&aacute;usula SQL</h3>";
    $msg .= "<br><b>SQL : </b><br>".$query;
    $msg .= "<br><b>Mensagem de erro do Msql : </b><brR>".msql_error();
    $msg .= "<br><a href=\"#\" onclick=\"history.go(-1);\">Voltar</a>";
    die($msg);
  }

  return($textos);
}

/* ************************************************************************
   RetornaFraseDaListaNotificar - Retorna a frase pedida da lingua atual
   Entrada: $textos - Array com lista de frases (RetornaListaDeFrases)
            $cod_texto - cod_texto a ser empregado.
   Saida: Texto na lingua pedida
*/
function RetornaFraseDaListaNotificar($textos, $cod_texto)
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
  PertenceAoGrupo - Retorna trua se o usuario pertence ao grupo e o grupo est� ativo
  Entrada: $sock        - BASE DO CURSO
           $cod_usuario - codigo do usuario
           $cod_grupo   - codigo do grupo
  Saida: True se pertence, false caso contrario
*/
function PertenceAoGrupo($sock, $cod_usuario, $cod_grupo)
{
  $cod_grupo = VerificaNumeroQuery($cod_grupo);
  if ($cod_grupo === '')
    return false;

  $query  = "select GU.cod_usuario ";
  $query .= "from Grupos_usuario GU, Grupos G ";
  $query .= "where GU.cod_grupo   = ".$cod_grupo."   and ";
  $query .=       "G.cod_grupo    = ".$cod_grupo."   and ";
  $query .=       "GU.cod_usuario = ".$cod_usuario." and ";
  $query .=       "G.status<>'X'";

  $res = Enviar($sock,$query);

  if (RetornaNumLinhas($res)>0)
    return true;
  else
    return false;
}

/* ********************************************************************************
   RetornaMaiorItemComentario - Retorna a maior data e o cod_usuario do item ou coment�rio
                                    dentro de um t�pico ou subt�pico
   Entrada: $sock - Conex�o com a base interna
            $cod_topico - codigo do topico a ser checado
            $tipo_comp - tipo do compartilhamento (F ou T)
            $data - Data do t�pico
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

  // Verifica se existem subt�picos
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

/* *******************************************************************
    RetornaGruposDoUsuario - Retorna os cod_grupo de todos os grupos n� apagados
                              a que o usuario pertence
    entrada: $sock - sock de conexao
              $cod_usuario - codigo do usuario do qual listar os grupos
**/
function RetornaGruposDoUsuarioNotificar($sock,$cod_usuario)
{
	$query = "select grupoUsuario.cod_grupo as cod_grupo from Grupos_usuario as grupoUsuario, Grupos as grupo ";
	$query.= "where (grupoUsuario.cod_usuario = 16) and (grupo.cod_grupo = grupoUsuario.cod_grupo) ";
	$query.= "and ((grupo.status = 'A') or (grupo.status = 'T'))";
    $res=Enviar($sock,$query);
    return (RetornaNumLinhas($res) > 0) ? RetornaArrayLinhas($res) : null;
}

/* *********************************************************************
   RetornaNovidadeFerramentas - retorna um array com as datas das �ltimas
                                atualiza��es para o usu�rio dado.
   Entrada: $sock - BASE DO CURSO
            $cod_curso
            $cod_usuario
   Saida: array [$cod_ferramenta]['data'] - data ultimo acesso,
                                 ['cod_usuario'] - c�digo do usu�rio
                                                   que realizou a altera��o.
*/
function RetornaNovidadeFerramentasNotificar($sock,$cod_curso,$cod_usuario)
{
  $query = "select cod_coordenador from Cursos";
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);
  $cod_coordenador = $linha['cod_coordenador'];

  // Se for visitante ou o participante ainda n�o foi autorizado
  // pelo coordenador do curso , nunca h� novidade 
  if ((EVisitante($sock,$cod_curso,$cod_usuario)) || 
      (!EAluno($sock, $cod_curso, $cod_usuario) && 
       !EFormador($sock, $cod_curso, $cod_usuario) && 
       !EColaborador($sock, $cod_curso, $cod_usuario)
      )
     )
  {
    return null;
  }

  $formador = false;
  $tipo_compart = "T";
  if (EFormador($sock,$cod_curso,$cod_usuario))
  {
    $formador = true;
  }

/********************************************************************
 * IDEAL - Ferramentas_nova && Ferramentas_nova_usuario
 *******************************************************************/  
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
  
/********************************************************************
 * 1 - Agenda
 *******************************************************************/
  // Pega �ltima atualiza��o da agenda (Ativa ou Edi��o Finalizada)
  $query = "select cod_usuario, data from Agenda_itens_historicos ";
  $query .= "where acao = 'A' or acao = 'F' ";
  $query .= "order by data desc limit 1";

  $res=Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[1]['data'] = $linha['data'];
    $saida[1]['cod_usuario'] = $linha['cod_usuario'];
  }

/********************************************************************
 * 3 - Atividades
 *******************************************************************/
  $query1 = "select cod_usuario, data from Atividade_itens where ";
  $query2 = "select cod_usuario, data from Atividade_topicos ";
  if (! $formador)
  {
    $query1 .= "tipo_compartilhamento = '".$tipo_compart."' and ";
    $query2 .= "where tipo_compartilhamento = '".$tipo_compart."' ";
  }
  $query1 .= "status = 'L' order by data desc limit 1";
  $query2 .= "order by data desc limit 1";

  $res1 = Enviar($sock, $query1);
  $res2 = Enviar($sock, $query2);
  
  // Verifica se tem novidade nas atividades
  if (RetornaNumLinhas($res1) > 0)
  {
    $linha1 = RetornaLinha($res1);
    $saida[3]['data'] = $linha1['data'];
    $saida[3]['cod_usuario'] = $linha1['cod_usuario'];
  }

  // Verifica se tem nova organizacao das pastas
  if (RetornaNumLinhas($res2) > 0)
  {
    $linha2 = RetornaLinha($res2);
    if ($linha2['data'] > $linha1['data'])
    {
    	$saida[3]['data'] = $linha2['data'];
    	$saida[3]['cod_usuario'] = $linha2['cod_usuario'];
    }
  }
  
/********************************************************************
 * 4 - Material de Apoio
 *******************************************************************/  
  $query1 = "select cod_usuario, data from Apoio_itens where ";
  $query2 = "select cod_usuario, data from Apoio_topicos ";
  if (! $formador)
  {
    $query1 .= "tipo_compartilhamento = '".$tipo_compart."' and ";
    $query2 .= "where tipo_compartilhamento = '".$tipo_compart."' ";
  }
  
  $query1 .= "status = 'L' order by data desc limit 1";
  $query2 .= "order by data desc limit 1";
  $res1 = Enviar($sock, $query1);
  $res2 = Enviar($sock, $query2);
  
  // Verifica se tem novidade nos Materiais de Apoio
  if (RetornaNumLinhas($res1) > 0)
  {
    $linha1 = RetornaLinha($res1);
    $saida[4]['data'] = $linha1['data'];
    $saida[4]['cod_usuario'] = $linha1['cod_usuario'];
  }

  // Verifica se tem nova organizacao das pastas
  if (RetornaNumLinhas($res2) > 0)
  {
    $linha2 = RetornaLinha($res2);
    $saida[4]['data'] = $linha2['data'];
    $saida[4]['cod_usuario'] = $linha2['cod_usuario'];
  }
  
/********************************************************************
 * 5 - Leituras
 *******************************************************************/
  $query1 = "select cod_usuario, data from Leitura_itens where ";
  $query2 = "select cod_usuario, data from Leitura_topicos ";
  if (! $formador)
  {
    $query1 .= "tipo_compartilhamento = '".$tipo_compart."' and ";
    $query2 .= "where tipo_compartilhamento = '".$tipo_compart."' ";
  }
  $query1 .= "status = 'L' order by data desc limit 1";
  $query2 .= "order by data desc limit 1";  
  
  $res1 = Enviar($sock, $query1);
  $res2 = Enviar($sock, $query2);

  // Verifica se tem novidade nas Leituras
  if (RetornaNumLinhas($res1) > 0)
  {
    $linha1 = RetornaLinha($res1);
    $saida[5]['data'] = $linha1['data'];
    $saida[5]['cod_usuario'] = $linha1['cod_usuario'];
  }
  // Verifica se tem novidade nas Pastas
  if (RetornaNumLinhas($res2) > 0)
  {
    $linha2 = RetornaLinha($res2);
    if ($linha2['data'] > $linha1['data'])
    {
      $saida[5]['data'] = $linha2['data'];
      $saida[5]['cod_usuario'] = $linha2['cod_usuario'];
    }
  }

/********************************************************************
 * 6 - Perguntas Frequentes
 *******************************************************************/  
  $query1 = "select data from Ferramentas_nova ";
  $query1 .= "where (cod_ferramenta = 6) and (tipo_compartilhamento = 'T')";
  $res1 = Enviar($sock, $query1);
  if (RetornaNumLinhas($res1) > 0)
  {
    $linha1 = RetornaLinha($res1);
    $saida[6]['data'] = $linha1['data'];
    $saida[6]['cod_usuario'] = -1;
  }

  // Verifica novidade nos Assuntos
  $query2 = "select data from Pergunta_assuntos ";
  $query2 .= "order by data desc limit 1";
  $res2 = Enviar($sock, $query2);
  if (RetornaNumLinhas($res2) > 0)
  {
    $linha2 = RetornaLinha($res2);
    if ($linha2['data'] > $linha1['data'])
    {
    	$saida[6]['data'] = $linha2['data'];
    	$saida[6]['cod_usuario'] = -1;
    }
  }
/********************************************************************
 * 7 - Parada Obrigatoria
 *******************************************************************/  
  $query1 = "select cod_usuario, data from Obrigatoria_itens where ";
  $query2 = "select cod_usuario, data from Obrigatoria_topicos ";
  if (! $formador)
  {
    $query1 .= "tipo_compartilhamento = '".$tipo_compart."' and ";
    $query2 .= "where tipo_compartilhamento = '".$tipo_compart."' ";
  }
  
  $query1 .= "status = 'L' order by data desc limit 1";
  $query2 .= "order by data desc limit 1";
  $res1 = Enviar($sock, $query1);
  $res2 = Enviar($sock, $query2);

  // Verifica se tem novidade nas Paradas Obrigatorias
  if (RetornaNumLinhas($res1) > 0)
  {
    $linha1 = RetornaLinha($res1);
    $saida[7]['data'] = $linha1['data'];
    $saida[7]['cod_usuario'] = $linha1['cod_usuario'];
  }
  
  // Verifica se tem novidade na organizacao das pastas
  if (RetornaNumLinhas($res2) > 0)
  {
    $linha2 = RetornaLinha($res2);
    if ($linha2['data'] > $linha1['data'])
    {
    	$saida[7]['data'] = $linha2['data'];
    	$saida[7]['cod_usuario'] = $linha2['cod_usuario'];
    }
  }
/********************************************************************
 * 8 - Mural
 *******************************************************************/
  $query = "select cod_usuario, data from Mural where status = 'A' order by data desc limit 1";
  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[8]['data'] = $linha['data'];
    $saida[8]['cod_usuario'] = $linha['cod_usuario'];
  }

  // 9 - F�runs de discuss�o
  $saida[9]['data'] = 0;
  $query1 = "select cod_usuario, data from Forum_mensagens ";
  $query2 = "select data from Forum ";
  $query3 = "select data from Ferramentas_nova_usuarios ";
  $query3 .= "where (cod = ".$cod_usuario.") and (cod_ferramenta = 9)";
   	      
  if (! $formador)
  {
    $query1 .= "where (status != 'X' and status != 'D') ";
    $query2 .= "where (status != 'X' and status != 'D') ";
  }
  $query1 .= "order by data desc limit 1";
  $query2 .= "order by data desc limit 1";

  // Verifica novidades nas Mensagens
  $res = Enviar($sock, $query1);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[9]['data'] = $linha['data'];
    $saida[9]['cod_usuario'] = $linha['cod_usuario'];
  }
  
  // Verifica novidades nos Foruns
  $res = Enviar($sock, $query2);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    if ($linha['data'] > $saida[9]['data'])
    {
    	$saida[9]['data'] = $linha['data'];
    	// na tabela nao ha info do cod_usuario
    	$saida[9]['cod_usuario'] = -1;	
    }
  }
      
  // Verifica novidades especificas para o usuario
  // em Ferramentas_nova_usuario
  $res = Enviar($sock, $query3);
  if (RetornaNumLinhas($res) > 0)
  {
  	$linha = RetornaLinha($res);
    if ($linha['data'] > $saida[9]['data'])
    {
      $saida[9]['data'] = $linha['data'];
      // na tabela nao ha info do cod_usuario
      $saida[9]['cod_usuario'] = -1;
    }
  }

/********************************************************************
 * 10 - Batepapo
 *******************************************************************/  
  $query = "select data from Ferramentas_nova ";
  $query .= "where (cod_ferramenta = 10) and (tipo_compartilhamento = 'T')";
  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[10]['data'] = $linha['data'];
    $saida[10]['cod_usuario'] = -1;
  }

/********************************************************************
 * 11 - Correio
 *******************************************************************/  
  // Se tiver alguma mensagem com N, aparece a estrela
  $query = "select C.cod_usuario, C.data from Correio_mensagens as C, Correio_destinos as Cd ";
  $query .= "where (Cd.estado = 'N') and (Cd.cod_msg = C.cod_msg) ";
  $query .= "and (Cd.cod_usuario = ".$cod_usuario.") ";
  $query .= "and (C.cod_usuario != ".$cod_usuario.") order by C.data desc limit 1";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[11]['data'] = $linha['data'];
    $saida[11]['cod_usuario'] = $linha['cod_usuario'];
  }

/********************************************************************
 * 12 - Grupos
 *******************************************************************/  
  $query  = "select data from Ferramentas_nova_usuarios ";
  $query .= "where (cod_ferramenta = 12) and (cod = ".$cod_usuario.") ";
  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
  	$linha = RetornaLinha($res);
    $saida[12]['data'] = $linha['data'];
    $saida[12]['cod_usuario'] = 1;
  }

/********************************************************************
 * 13 - Perfil
 *******************************************************************/  
  $query = "select cod_usuario, data from Perfil_usuarios order by data desc limit 1";
  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[13]['data'] = $linha['data'];
    $saida[13]['cod_usuario'] = $linha['cod_usuario'];
  }

/********************************************************************
 * 14 - Diario
 *******************************************************************/  
  if ($formador)
  {
    // Se formador, verifica se h� itens ou coment�rios novos em qualquer usu�rio
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
    // Se for aluno, checa se h� coment�rio novo em seu di�rio
    $query="select Dc.cod_comentarist, Dc.data from Diario_itens Di, Diario_comentarios Dc where (Di.cod_item = Dc.cod_item) and (Di.cod_usuario = ".$cod_usuario.") and (Dc.cod_comentarist != ".$cod_usuario.") and (Di.status = 'A') and (Dc.status = 'A') order by Dc.data desc limit 1";
    $res=Enviar($sock,$query);
    if (RetornaNumLinhas($res) > 0)
    {
      $linha = RetornaLinha($res);
      $saida[14]['data'] = $linha['data'];
      $saida[14]['cod_usuario'] = $linha['cod_comentarist'];
    }
  }
  
/********************************************************************
 * 15 - Portfolio e Portfolio de Grupos
 *******************************************************************/
  if ($formador)
    $saida[15]=RetornaMaiorItemComentario($sock,1,'F', array('cod_usuario' => $cod_usuario, 'data' => 0),$cod_usuario);
  else
    $saida[15]=RetornaMaiorItemComentario($sock,1,'T', array('cod_usuario' => $cod_usuario, 'data' => 0),$cod_usuario);

/********************************************************************
 * 16 - Din�mica
 *******************************************************************/    
  $query = "select cod_usuario, data from Dinamica where status = 'A'";
  $res=Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha = RetornaLinha($res);
    $saida[16]['data'] = $linha['data'];
    $saida[16]['cod_usuario'] = $linha['cod_usuario'];
  }
    
/********************************************************************
 * 22 - Avalia��es
 *******************************************************************/    
  // Pega �ltima atualiza��o da avalia��o (Ativa ou Edi��o Finalizada)
  $query = "select cod_usuario, data from Avaliacao_historicos ";
  $query .= "where (acao = 'C') or (acao = 'F') order by data desc limit 1";
  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
  	$linha = RetornaLinha($res);
    $saida[22]['data'] = $linha['data'];
    $saida[22]['cod_usuario'] = $linha['cod_usuario'];
  }

/********************************************************************
 * 23 - Exercicios
 *******************************************************************/

/**
 *  $query1
 *  1. Formador cria um exercicio, todos os formadores s�o notificados.
 *  2. Formador edita um exerc�cio, todos os formadores s�o notificados.
 *  3. Formador aplica um exerc�cio, todos os formadores e alunos s�o
 *  notificados.
 *
 *  $query2
 *  1. Formador corrige um trabalho, aluno recebe a notifica��o SE o tempo
 *  de submiss�o do exerc�cio acabou.
 *  2. Aluno submete um exerc�cio, Formador recebe notifica��o.
 *
**/

    $query1 = "select data from Exercicios_modelo_historico where ";
    $query1.= "(acao != 'L') and (acao != 'C') ";	

    // As 3 tabelas ser�o necess�rias para verificar o timelimit de um
    // exerc�cio, para nao notificar aluno antes do fim do exerc�cio.
    $query2 = "select hist.data as data from Exercicios_resolucao_historico as hist, ";
    $query2.= "Exercicios_resolucao as reso, Exercicios_aplicado as aplic ";
    $query2.= "where ";

    if ($formador)
    {
      /* Formador quer saber se algum aluno submeteu exercicio */
      $query2.= "(acao = 'S') ";
    }
    else    /* se n�o � formador */
    {
      /* Aluno quer saber se algum exercicio foi aplicado */
      $query1.= "and (acao = 'P') ";

      /* Aluno quer saber se tem corre��o dispon�vel pra ele ver */
      $query2.= "(hist.cod_usuario = ".$cod_usuario.") and (hist.acao = 'C') ";
      $query2.= "and ((hist.cod_resolucao = reso.cod_resolucao) ";
      $query2.= "and (reso.cod_exercicio = aplic.cod_exercicio) ";
      $query2.= "and (".time()." > aplic.dt_limite_submissao)) ";
    }
    $query1.= "order by data desc limit 1";
    $query2.= "order by data desc limit 1";

    $saida[23]['data'] = 0;     /* Inicializa��o necess�ria */
    $res = Enviar($sock, $query1);
    if (RetornaNumLinhas($res) > 0)
    {
      $linha = RetornaLinha($res);
      $saida[23]['data'] = $linha['data'];
      $saida[23]['cod_usuario'] = -1;
    }
    
    $res = Enviar($sock, $query2);
    if (RetornaNumLinhas($res) > 0)
    {
      $linha = RetornaLinha($res);
      if ($linha['data'] > $saida[23]['data'])
      {
        $saida[23]['data'] = $linha['data'];
        $saida[23]['cod_usuario'] = -1;
      }
    }
  
/********************************************************************
 * 24 - Enquete
 *******************************************************************/  
  // Pega atualiza��o do status da Enquete (Ativa ou Encerrada)
  MudarDB($sock, "");
  $query  = "select tipo_usuario from Usuario_curso ";
  $query .= "where cod_curso = ".$cod_curso;
  $query .= " and cod_usuario = ".$cod_usuario;
  
  $res = Enviar($sock, $query);
  $linha_q1 = RetornaLinha($res);
  
  MudarDB($sock, $cod_curso);
  $query  = "select data from Ferramentas_nova ";
  $query .= "where (cod_ferramenta = 24) "; 
  $query .= "and (tipo_compartilhamento = '".$linha_q1['tipo_usuario']."')";
  
  $res = Enviar($sock, $query);
  if (RetornaNumLinhas($res) > 0)
  {
    $linha_q2 = RetornaLinha($res);
    $saida[24]['data'] = $linha_q2['data'];
    $saida[24]['cod_usuario'] = -1;
  }
  
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
function RetornaFerramentasCursoNotificar($sock)
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
