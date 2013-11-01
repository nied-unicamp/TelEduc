<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/busca/busca.inc

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

/* **********************************************************************
   RetornaFerramentasOrdemMenu - Retorna o codigo e nome das ferramentas
                                 na ordem do menu
   Entrada: $sock - BASE EXTERNA
   Saida: array[$cod_ferramenta]=nome
*/
function RetornaFerramentasOrdemMenu($sock)
{
  $query ="select F.cod_ferramenta 'cod_ferramenta', L.texto 'texto', M.posicao 'posicao'";
  $query.=" from Ferramentas F, Menu M, Lingua_textos L where L.cod_lingua=".$_SESSION['cod_lingua_s'];
  $query.=" and L.cod_ferramenta=-4 and L.cod_texto=F.cod_texto_nome and F.cod_ferramenta=M.cod_ferramenta";
  $query.=" order by M.posicao";
  $res=Enviar($sock,$query);
  $saida = "";
  unset ($saida);
  while ($linha=RetornaLinha($res))
    $saida[$linha['cod_ferramenta']]=$linha['texto'];
  return $saida;
}

/* **********************************************************************
   RetornaBusca - Retorna todas as ocorr�ncias de $texto na ferramenta especificada em $tabela
   Entrada: $sock - base do curso
            $cod_curso - c�digo do curso
            $cod_usuario - c�digo do usu�rio
            $texto - array com palavras-chave a serem procuradas
            $tabela - nome da tabela a fazer a busca
            $par1 - par�metro para busca
            $par2 - par�metro para busca
   Saida: $busca = linhas de conte�do que t�m a ocorr�ncia das palavras de $texto
*/
function RetornaBusca($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par1,$par2)
{
  //para saber se est� com vis�o de formador ou aluno
  $formador=EFormador($sock,$cod_curso,$cod_usuario);

  foreach ($texto as $cod => $linha)
  {
    if ($cod==0)
    {
      //$query="select * from ".$tabela." where (((".$par1." rlike '[ .,:;!?]+".$linha."\$')or(".$par2." rlike '[ .,:;!?]+".$linha."\$'))";
      //$query.="or((".$par1." rlike '^".$linha."[ .,:;!?]+')or(".$par2." rlike '^".$linha."[ .,:;!?]+'))";
      //$query.="or((".$par1." rlike '[ .,:;!?]+".$linha."[ .,:;!?]+')or(".$par2." rlike '[ .,:;!?]+".$linha."[ .,:;!?]+'))";
      //$query.="or((".$par1." rlike '^".$linha."\$')or(".$par2." rlike '^".$linha."\$')))";
      $query="select * from ".$tabela." as T ";

      if ($tabela=="Forum_mensagens")
        $query.=",Forum as F ";
      if ($tabela=="Correio_mensagens")
        $query.=",Correio_destinos as D ";

      $query.="where ((".$par1." rlike '(^|[^[:alnum:]])".$linha."([^[:alnum:]]|\$)')";
      $query.="or (".$par2." rlike '(^|[^[:alnum:]])".$linha."([^[:alnum:]]|\$)'))";
    }	
    else
    {
      //$query.="and (((".$par1." rlike '[ .,:;!?]+".$linha."\$')or(".$par2." rlike '[ .,:;!?]+".$linha."\$'))";
      //$query.="or((".$par1." rlike '^".$linha."[ .,:;!?]+')or(".$par2." rlike '^".$linha."[ .,:;!?]+'))";
      //$query.="or((".$par1." rlike '[ .,:;!?]+".$linha."[ .,:;!?]+')or(".$par2." rlike '[ .,:;!?]+".$linha."[ .,:;!?]+'))";
      //$query.="or((".$par1." rlike '^".$linha."\$')or(".$par2." rlike '^".$linha."\$')))";
      $query.="and ((".$par1." rlike '(^|[^[:alnum:]])".$linha."([^[:alnum:]]|\$)')";
      $query.="or (".$par2." rlike '(^|[^[:alnum:]])".$linha."([^[:alnum:]]|\$)'))";
    }
  }

  if ($tabela=="Agenda_itens")
  {
    // s� busca a agenda se ela puder ser lida
    $query.="and status='L'";
    if (!($formador))
      // aluno n�o tem acesso a agendas inativas
      $query.="and situacao<>'N'";
  }

  if ($tabela=="Apoio_itens" || $tabela=="Atividade_itens" || $tabela=="Leitura_itens" || $tabela=="Obrigatoria_itens")
  {
    // s� busca item se ele puder ser lido
    $query.="and status='L'";
    if (!($formador))
      // aluno s� tem acesso a itens totalmente compartilhados e que n�o estejam na Lixeira
      $query.="and tipo_compartilhamento='T' and cod_topico<>2";
  }

  if ($tabela=="Pergunta_itens")
    if (!($formador))
      // aluno n�o tem acesso � Lixeira
      $query.="and cod_assunto<>2";

  if ($tabela=="Mural")
    // usu�rio n�o tem acesso aos murais apagados
    $query.="and status='A'";

  if ($tabela=="Forum_mensagens")
  {
    // usu�rio n�o tem acesso �s mensagens exclu�das
    $query.="and T.cod_forum=F.cod_forum and F.status<>'X'";
    if (!($formador))
      // aluno n�o tem acesso �s mensagens da Lixeira
      $query.="and F.status<>'D'";
  }

  if ($tabela=="Correio_mensagens")
  {
    $query.="and T.cod_msg=D.cod_msg ";
    // usu�rio s� l� mensagens enviadas se essa n�o tiver sido exclu�da
    $query.="and((T.cod_usuario=".$cod_usuario." and T.estado<>'X')";
    // usu�rio s� l� mensagem enviada para ele se essa n�o tiver sido exclu�da
    $query.="or (D.cod_usuario=".$cod_usuario." and D.estado<>'X'))";
  }

  if ($tabela=="Diario_itens")
  {
    // usu�rio s� tem acesso ao item se este for dele ou o compartilhamento n�o for "N�o Compartilhado"
    $query.="and (cod_usuario=".$cod_usuario." or tipo_compartilhamento<>'P')";
    // usu�rio s� v� item se este estiver ativo
    $query.="and status='A' ";
    if (!($formador))
      // aluno s� tem acesso aos itens compartilhados com todos
      $query.="and tipo_compartilhamento<>'F'";
  }

  if ($tabela=="Portfolio_itens")
  {
    // se o usu�rio n�o � o dono do item do portf�lio, ele tem acesso a esse item sob duas condi��es :
    // o item n�o tem o compartilhamento "N�o Compartilhado" e n�o est� na Lixeira
    $query.="and (cod_usuario=".$cod_usuario." or (tipo_compartilhamento<>'P' and cod_topico<>2))";
    // usu�rio s� v� item se este estiver livre
    $query.="and status='L' ";
    // usu�rio s� tem acesso ao item se este n�o foi exclu�do
    $query.="and cod_topico<>0 ";
    if (!($formador))
      // aluno s� tem acesso aos itens compartilhados com todos
      $query.="and tipo_compartilhamento<>'F'";
  }

  $res=Enviar($sock,$query);
  $busca=RetornaArrayLinhas($res);
  return ($busca);
}

/* **********************************************************************
   RetornaBuscaOpcao - Retorna todas as ocorr�ncias de $texto na ferramenta especificada em $tabela
   Entrada: $sock - base do curso
            $cod_curso - c�digo do curso
            $cod_usuario - c�digo do usu�rio
            $texto - array com palavras-chave a serem procuradas
            $tabela - nome da tabela a fazer a busca
            $par - par�metro para busca
   Saida: $busca = linhas de conte�do que t�m a ocorr�ncia das palavras de $texto
*/
function RetornaBuscaOpcao($sock,$cod_curso,$cod_usuario,$texto,$tabela,$par)
{
  //para saber se est� com vis�o de formador ou aluno
  $formador=EFormador($sock,$cod_curso,$cod_usuario);

  foreach ($texto as $cod => $linha)
  {
    if ($cod==0)
    {
      $query="select * from ".$tabela." as T ";

      if ($tabela == "Batepapo_conversa")
        $query.=", Batepapo_sessoes as S ";
      else if ($tabela == "Diario_comentarios")
        $query.=", Diario_itens as I ";
      else if ($tabela == "Portfolio_itens_comentarios")
        $query.=", Portfolio_itens as I ";

      $query.="where (".$par." rlike '(^|[^[:alnum:]])".$linha."([^[:alnum:]]|\$)')";
    }
    else
      $query.="and (".$par." rlike '(^|[^[:alnum:]])".$linha."([^[:alnum:]]|\$)')";
  }

  if ($tabela=="Dinamica")
    // n�o busca se a Din�mica estiver sendo inclu�da
    $query.="and status<>'I'";

  if ($tabela=="Batepapo_conversa")
  {
    // usu�rio n�o tem acesso �s sess�es exclu�das
    $query.="and T.cod_sessao=S.cod_sessao and status<>'X'";
    if (!($formador))
      // aluno n�o tem acesso �s sess�es da Lixeira
      $query.="and status<>'L'";
  }

  if ($tabela=="Diario_comentarios")
  {
    $query.="and T.cod_item=I.cod_item ";
    // usu�rio s� tem acesso ao item se este for dele ou o compartilhamento n�o for "N�o Compartilhado"
    $query.="and (cod_usuario=".$cod_usuario." or tipo_compartilhamento<>'P')";
    // usu�rio s� v� item se este estiver ativo
    $query.="and I.status='A' ";
    if (!($formador))
      // aluno s� tem acesso aos itens compartilhados com todos
      $query.="and tipo_compartilhamento<>'F'";
    // usu�rio s� v� coment�rios ativos (n�o apagados)
    $query.="and T.status='A'";
  }

  if ($tabela=="Portfolio_itens_comentarios")
  {
    $query.="and T.cod_item=I.cod_item ";
    // se o usu�rio n�o � o dono do item do portf�lio, ele tem acesso a esse item sob duas condi��es :
    // o item n�o tem o compartilhamento "N�o Compartilhado" e n�o est� na Lixeira
    $query.="and (cod_usuario=".$cod_usuario." or (tipo_compartilhamento<>'P' and cod_topico<>2)) ";
    // usu�rio s� v� item se este estiver livre
    $query.="and I.status='L' ";
    // usu�rio s� tem acesso ao item se este n�o foi exclu�do
    $query.="and cod_topico<>0 ";
    if (!($formador))
      // aluno s� tem acesso aos itens compartilhados com todos
      $query.="and tipo_compartilhamento<>'F' ";
    // usu�rio s� v� coment�rios ativos (n�o apagados)
    $query.="and T.status='A'";
  }

  $res=Enviar($sock,$query);
  $busca=RetornaArrayLinhas($res);
  return ($busca);
}

/* **********************************************************************
   RetornaBuscaAvaliacoes - retorna todas as ocorr�ncias de $texto na ferramenta
      Avalia��es
   entrada: $sock_curso  - sock de conexao com a base do curso
            $cod_curso   - codigo do curso
            $cod_usuario - codigo do usuario logado
            $texto       - array com os textos a procurar na ferramenta Avalia��es
   saida:  array []['texto'] - texto encontrado
                 []['pagina'] - script PHP a acessar para ver a ocorrencia, por exemplo, historico_desempenho.php
                 []['titulo'] - titulo da p�gina em que foi encontrada a ocorrencia, por exemplo, 'Avalia��es - Historico'
*/
function RetornaBuscaAvaliacoes($sock_curso, $cod_curso, $cod_usuario, $texto)
{
    /*
    tabelas a procurar na ferramenta Avalia��es
    Avaliacao - campos criterios, objetivos
    Avaliacao_notas - campo comentario

    verificar compartilhamentos
    */
    global $lista_frases_ferramentas, $lista_frases;

    $retorno = NULL;
    if (is_array($texto)) {
        $retorno = array();

        // 0. busca pelos t�tulos das Avalia��es
        // 0.1 forum
        $query1 = "select A.cod_avaliacao, F.nome from Avaliacao A, Forum F"
            ." "."where A.ferramenta='F' and A.cod_atividade=F.cod_forum";
        foreach ($texto as $frase) {
            $query = $query1." "."and (F.nome rlike '(^|[^[:alnum:]])".$frase."([^[:alnum:]]|\$)')";
            $res   = Enviar($sock_curso, $query);
            while ($linha = RetornaLinha($res)) {
                $retorno[] = array(
                    'texto'  => $linha['nome'],
                    'pagina' => "../avaliacoes/ver.php?cod_curso=".$cod_curso."&amp;cod_avaliacao=".$linha['cod_avaliacao'],
                    // F 52 - Avalia��es
                    // 15 - Ver Avalia��o
                    'titulo' => RetornaFraseDaLista($lista_frases_ferramentas, 52)." - ".RetornaFraseDaLista($lista_frases, 15)
                );
            }
        }
        // 0.2 sessao de bate-papo
        $query1 = "select A.cod_avaliacao, BP.assunto from Avaliacao A, Batepapo_assuntos BP"
            ." "."where A.ferramenta='B' and A.cod_atividade=BP.cod_assunto";
        foreach ($texto as $frase) {
            $query = $query1." "."and (BP.assunto rlike '(^|[^[:alnum:]])".$frase."([^[:alnum:]]|\$)')";
            $res   = Enviar($sock_curso, $query);
            while ($linha = RetornaLinha($res)) {
                $retorno[] = array(
                    'texto'  => $linha['assunto'],
                    'pagina' => "../avaliacoes/ver.php?cod_curso=".$cod_curso."&amp;cod_avaliacao=".$linha['cod_avaliacao'],
                    // F 52 - Avalia��es
                    // 15 - Ver Avalia��o
                    'titulo' => RetornaFraseDaLista($lista_frases_ferramentas, 52)." - ".RetornaFraseDaLista($lista_frases, 15)
                );
            }
        }

        // 0.3 avalia��o associada a atividade
        $query1 = "select A.cod_avaliacao, AT.titulo from Avaliacao A, Atividade_itens AT"
            ." "."where A.ferramenta='P' and A.cod_atividade=AT.cod_item";
        foreach ($texto as $frase) {
            $query = $query1." "."and (AT.titulo rlike '(^|[^[:alnum:]])".$frase."([^[:alnum:]]|\$)')";
            $res   = Enviar($sock_curso, $query);
            while ($linha = RetornaLinha($res)) {
                $retorno[] = array(
                    'texto'  => $linha['titulo'],
                    'pagina' => "../avaliacoes/ver.php?cod_curso=".$cod_curso."&amp;cod_avaliacao=".$linha['cod_avaliacao'],
                    // F 52 - Avalia��es
                    // 15 - Ver Avalia��o
                    'titulo' => RetornaFraseDaLista($lista_frases_ferramentas, 52)." - ".RetornaFraseDaLista($lista_frases, 15)
                );
            }
        }


        // 1. busca por criterios da avalia��o na tabela Avaliacao
        $query1 = "select cod_avaliacao, criterios from Avaliacao";
        foreach ($texto as $frase) {
            $query = $query1." "."where (criterios rlike '(^|[^[:alnum:]])".$frase."([^[:alnum:]]|\$)')";
            $res   = Enviar($sock_curso, $query);
            while ($linha = RetornaLinha($res)) {
                $retorno[] = array(
                    'texto'  => $linha['criterios'],
                    'pagina' => "../avaliacoes/ver.php?cod_curso=".$cod_curso."&amp;cod_avaliacao=".$linha['cod_avaliacao'],
                    // F 52 - Avalia��es
                    // 15 - Ver Avalia��o
                    'titulo' => RetornaFraseDaLista($lista_frases_ferramentas, 52)." - ".RetornaFraseDaLista($lista_frases, 15)
                );
            }
        }

        // 2. busca por objetivos da avalia��o na tabela Avaliacao
        $query1 = "select cod_avaliacao, objetivos from Avaliacao";
        foreach ($texto as $frase) {
            $query = $query1." "."where (objetivos rlike '(^|[^[:alnum:]])".$frase."([^[:alnum:]]|\$)')";
            $res   = Enviar($sock_curso, $query);
            while ($linha = RetornaLinha($res)) {
                $retorno[] = array(
                    'texto'  => $linha['objetivos'],
                    'pagina' => "../avaliacoes/ver.php?cod_curso=".$cod_curso."&amp;cod_avaliacao=".$linha['cod_avaliacao'],
                    // F 52 - Avalia��es
                    // 15 - Ver Avalia��o
                    'titulo' => RetornaFraseDaLista($lista_frases_ferramentas, 52)." - ".RetornaFraseDaLista($lista_frases, 15)
                );
            }
        }

        // 3. busca por justificativa para nota na tabela Avaliacao_notas
        $query1 = "select AN.cod_avaliacao, AN.cod_aluno, AN.cod_grupo, AN.comentario, A.ferramenta
                     from Avaliacao_notas AN, Avaliacao A
                    where AN.cod_avaliacao=A.cod_avaliacao";

        if (EFormador($sock_curso, $cod_curso, $cod_usuario)) {
            // acesso do formador n�o � restrito a nenhum compartilhamento
            $query_compartilhamento = "";
        } elseif (EAluno($sock_curso, $cod_curso, $cod_usuario)) {
            // compartilhado com todos
            $query_compartilhamento = "and (AN.tipo_compartilhamento='T'";
            // usuario eh dono
            $query_compartilhamento .= " "."or (AN.tipo_compartilhamento='A' and cod_aluno=".$cod_usuario.")";

            // verificamos a quais grupos o usuario pertence
            $query = "select GU.cod_grupo from Grupos_usuario GU, Grupos G where GU.cod_grupo=G.cod_grupo and G.status<>'X' and GU.cod_usuario=".$cod_usuario;

            $res   = Enviar($sock_curso, $query);
            $lista_grupos = RetornaArrayLinhas($res);
            if (count($lista_grupos) > 0) {
                $grupos = array();
                foreach ($lista_grupos as $cod => $linha) {
                    $grupos[] = $linha[0];
                }
                $grupos = "(".implode(",", $grupos).")";

                // usuario pertence ao grupo
                $query_compartilhamento .= " "."or (AN.tipo_compartilhamento='G' and cod_grupo in ".$grupos.")";
            }
            $query_compartilhamento .= ")";
        } else {
            die("Erro interno");
        }

        foreach ($texto as $frase) {
            $query = $query1." "."and (AN.comentario rlike '(^|[^[:alnum:]])".$frase."([^[:alnum:]]|\$)')"
                            ." "."and AN.status<>'X'"
                            ." ".$query_compartilhamento;

            $res   = Enviar($sock_curso, $query);
            while ($linha = RetornaLinha($res)) {
                $retorno[] = array(
                    'texto'  => $linha['comentario'],
                    'pagina' => "avaliacoes_historico.php?&amp;cod_curso=".$cod_curso
                                    ."&amp;cod_avaliacao=".$linha['cod_avaliacao']
                                    ."&amp;ferramenta=".$linha['ferramenta']
                                    // se cod_grupo nao for null, � avalia��o de portfolio de grupo
                                    // passamos cod_grupo como parametro. Se for null, passamos cod_aluno
                                    .($linha['cod_grupo'] != NULL ? "&cod_grupo=".$linha['cod_grupo'] : "&cod_aluno=".$linha['cod_aluno']),
                    // F 52 - Avalia��es
                    // 16 - Hist�rico de Desempenho
                    'titulo' => RetornaFraseDaLista($lista_frases_ferramentas, 52)." - ".RetornaFraseDaLista($lista_frases, 16)
                );
            }
        }
    }

    return $retorno;
}



/* **********************************************************************
   RetornaStatusFerramentas - Retorna o status de cada ferramenta (quanto a visibilidade)
   Entrada: $sock - base do curso
   Saida: $status_ferr = vetor com status de cada ferramenta na posi��o do c�digo da ferramenta
*/
function RetornaStatusFerramentas($sock)
{
  $query="select cod_ferramenta,status from Curso_ferramentas";
  $res=Enviar($sock,$query);
  $resposta=RetornaArrayLinhas($res);
  foreach ($resposta as $cod => $linha)
    $status_ferr[$linha['cod_ferramenta']]=$linha['status'];
  return ($status_ferr);
}

/* **********************************************************************
   RetornaTipoUsuario - Retorna o tipo do usu�rio que est� logado
   Entrada: $sock - base do curso
            $cod_usuario - c�digo do usu�rio
   Saida: $tipo_usuario - O tipo do usu�rio
*/
function RetornaTipoUsuario($sock, $cod_curso, $cod_usuario)
{

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query="select tipo_usuario from ".$dbnamebase.".Usuario_curso where cod_curso=".$cod_curso." and cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  $resposta=RetornaLinha($res);
  return ($resposta['tipo_usuario']);
}

/* **********************************************************************
   FormataSaida - Formata o texto de sa�da para a busca colocando negritos, retirando tags, dentre outras coisas
   Entrada: $texto - Texto original
            $palavras - Palavras a serem encontradas na Busca
   Saida: $texto - Texto formatado
*/
function FormataSaida($texto,$palavras)
{
  // transforma o texto para um trecho onde h� uma ocorr�ncia

  //$pos=strpos($texto,$palavras[0]);  ------->  n�o funciona pois � case sensitive !!
  $temp=stristr($texto,$palavras[0]);
  $pos=strlen($texto) - strlen ($temp);
  $pos_fim=strlen($texto);

  if ($pos>60)
  {
    $pos_inicio=strpos($texto,' ',$pos-60);
    $str_inicio="... ";
  }
  else
  {
    $pos_inicio=0;
    $str_inicio="";
  }

  if (($pos_fim - $pos)>150)
  {
    $str_fim=" ...";
    $pos_fim=strpos($texto,' ',$pos+150);
    $offset=$pos_fim - $pos +1;
  }
  else
  {
    $str_fim="";
    $offset=150;
  }

  if ($pos_fim>150)
    $texto=$str_inicio.substr($texto,$pos_inicio,$offset).$str_fim;

  // retira tags e substitui Enter por <br> nas ocorr�ncias
  $texto=Enter2BR(LimpaTags($texto));

  // substitui ocorr�ncias por negrito
  foreach ($palavras as $cod => $palavra)
     $texto=eregi_replace("(^|[^[:alnum:]])(".$palavra.")([^[:alnum:]]|\$)","\\1<b>\\2</b>\\3",$texto);

  return ($texto);
}
?>