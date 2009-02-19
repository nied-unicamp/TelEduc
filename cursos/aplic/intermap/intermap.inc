<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/intermap.inc

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
   ARQUIVO : cursos/aplic/intermap/intermap.inc
   ========================================================== */

/* *********************************************************************
   DesenhaMenus - Monta o menu do intermap
   Entrada: $menu
            $submenu,
            $cod_curso
   Saida: nenhuma (saida na tela)
*/
function DesenhaMenus($menu,$submenu,$cod_curso)
{
  global $lista_frases;

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("  <tr><td><ul class=\"btAuxTabs\">\n");

  // 15 - Correio
  echo("    <li><span title=\"Correio\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=Correio';\">".RetornaFraseDaLista($lista_frases,15)."</span></li>\n");

  // 30 - F�runs de Discuss�o
  echo("    <li><span title=\"F�runs de Discuss�o\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=Forum';\">".RetornaFraseDaLista($lista_frases,30)."</span></li>\n");

  // 14 - Bate-papo
  echo("    <li><span title=\"Correio\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=Batepapo';\">".RetornaFraseDaLista($lista_frases,14)."</span></li>\n");

  echo("</ul></td></tr>\n");
  echo("<tr><td><ul class=\"btAuxTabs03\">\n");

  if ($menu=="Correio")
  {
    // 38 - Mapa de Intera��o
    echo("    <li><span title=\"Mapa de Intera��o\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=".$menu."&amp;submenu=MapaInteracao';\">".RetornaFraseDaLista($lista_frases,38)."</span></li>\n");
    // 33 - Gr�fico por Per�odo
    echo("    <li><span title=\"Gr�fico por Per�odo\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=".$menu."&amp;submenu=GraficoPeriodo';\">".RetornaFraseDaLista($lista_frases,33)."</span></li>\n");
    // 32 - Gr�fico por Participante
    echo("    <li><span title=\"Gr�fico por Participante\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=".$menu."&amp;submenu=GraficoParticipante';\">".RetornaFraseDaLista($lista_frases,32)."</span></li>\n");
  }

  else if ($menu=="Forum")
  {
    // 38 - Mapa de Intera��o
    echo("    <li><span title=\"Mapa de Intera��o\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=".$menu."&amp;submenu=MapaInteracao';\">".RetornaFraseDaLista($lista_frases,38)."</span></li>\n");
    // 33 - Gr�fico por Per�odo
    echo("    <li><span title=\"Gr�fico por Per�odo\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=".$menu."&amp;submenu=GraficoPeriodo';\">".RetornaFraseDaLista($lista_frases,33)."</span></li>\n");
    // 32 - Gr�fico por Participante
    echo("    <li><span title=\"Gr�fico por Participante\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=".$menu."&amp;submenu=GraficoParticipante';\">".RetornaFraseDaLista($lista_frases,32)."</span></li>\n");
    // 27 - Fluxo de Conversa��o
    echo("    <li><span title=\"Fluxo de Conversa��o\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=".$menu."&amp;submenu=FluxoConversacao';\">".RetornaFraseDaLista($lista_frases,27)."</span></li>\n");
  }

  else if ($menu=="Batepapo")
  {
    // 38 - Mapa de Intera��o
    echo("    <li><span title=\"Mapa de Intera��o\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=".$menu."&amp;submenu=MapaInteracao';\">".RetornaFraseDaLista($lista_frases,38)."</span></li>\n");
    // 32 - Gr�fico por Participante
    echo("    <li><span title=\"Gr�fico por Participante\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=".$menu."&amp;submenu=GraficoParticipante';\">".RetornaFraseDaLista($lista_frases,32)."</span></li>\n");
    // 27 - Fluxo de Conversa��o
    echo("    <li><span title=\"Fluxo de Conversa��o\" onClick=\"document.location='intermap.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;menu=".$menu."&amp;submenu=FluxoConversacao';\">".RetornaFraseDaLista($lista_frases,27)."</span></li>\n");
  }

  echo("  </ul></td></tr>\n");

}


/* *********************************************************************
   RetornaDadosCurso - Retorna os dados do curso
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
                                         'N' - N�o permite
*/
/*function RetornaDadosCurso($sock,$cod_curso)
{
  $query ="select * from Cursos where cod_curso=".$cod_curso;
  $res=Enviar($sock,$query);
  return (RetornaLinha($res));
}*/

/* *********************************************************************
   RetornaListaCodUsuarioNome - Retorna a lista de usuario em funcao do cod_usuario or
   Entrada: $sock - BASE INTERNA
            $cod_curso - código do curso
   Saida: array [$cod_usuario]=nome
*/
function RetornaListaCodUsuarioNome($sock, $cod_curso)
{
  //nome da base geral
  $dbnamebase = $_SESSION['dbnamebase'];

  // os convidados que aparecem no Intermap sao somente os com interacao
  $query ="select UC.cod_usuario, U.nome from ".$dbnamebase.".Usuario U, ".$dbnamebase.".Usuario_curso UC";
  $query.=" where (UC.cod_curso=".$cod_curso.") and (U.cod_usuario=UC.cod_usuario_global) and (UC.cod_usuario>=0)";
  $query.=" and (binary UC.tipo_usuario='A' or binary UC.tipo_usuario='F' or binary UC.tipo_usuario='Z')";
  $query.=" order by U.nome";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista) > 0)
  {
    foreach($lista as $cod=>$linha)
    {
      $lista1[$linha['cod_usuario']]=$linha['nome'];
    }
  }
  
  return ($lista1);
}

/* *********************************************************************
   RetornaTodosuUsuarios - retorna todos os usuarios, desde alunos e formadores at� convidados com e sem interacao e visitantes
   entrada: $sock - conexao com a base
            $cod_curso - código do curso
   saida:   array[cod_usuario] == nome
*/
function RetornaTodosUsuarios($sock, $cod_curso)
{

  //nome da base geral
  $dbnamebase = $_SESSION['dbnamebase'];

  $query  = "select UC.cod_usuario, U.nome from ".$dbnamebase.".Usuario U, ".$dbnamebase.".Usuario_curso UC ";
  $query .= "where UC.cod_curso=".$cod_curso." and U.cod_usuario=UC.cod_usuario_global and ";
  $query .= "(binary UC.tipo_usuario='A' ";
  $query .=     "or binary UC.tipo_usuario='F' ";
  $query .=     "or binary UC.tipo_usuario='V' ";
  $query .=     "or binary UC.tipo_usuario='Z' ";
  $query .=     "or binary UC.tipo_usuario='z') ";
  $query .=    "and UC.cod_usuario>=0";
  
  $res   = Enviar($sock, $query);
  while ($linha = RetornaLinha($res))
  {
    $lista[ $linha[cod_usuario] ] = $linha[nome];
  }
  return $lista;
}

/* *********************************************************************
   RetornaListaCodUsuarioFormador - Retorna a lista de usuario que sao formadores
   Entrada: $sock - BASE INTERNA
            $cod_curso - código do curso
   Saida: array [$cod_usuario]=cod_usuario, se usuario formador
*/
function RetornaListaCodUsuarioFormador($sock, $cod_curso)
{

  //nome da base geral
  $dbnamebase = $_SESSION['dbnamebase'];

  $query ="select UC.cod_usuario from ".$dbnamebase.".Usuario_curso UC where UC.cod_curso=".$cod_curso." and binary UC.tipo_usuario='F' and UC.cod_usuario>=0 order by UC.cod_usuario";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista) > 0)
  {
    foreach($lista as $cod=>$linha)
    {
      $lista1[ $linha['cod_usuario'] ]=$linha['cod_usuario'];
    }
  }
  return ($lista1);
}

/* *********************************************************************
   RetornaAlunosFormadoresCodUsuarioNome - retorna somente alunos e formadores
   entrada: $sock - sock de conexao
            $cod_curso - código do curso
   saida: array[$cod_usuario] == nome, para alunos e formadores somente
*/
function RetornaAlunosFormadoresCodUsuarioNome($sock, $cod_curso)
{

  //nome da base geral
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "select UC.cod_usuario, U.nome from ".$dbnamebase.".Usuario U, ".$dbnamebase.".Usuario_curso UC where (UC.cod_curso=".$cod_curso.") and (U.cod_usuario=UC.cod_usuario_global) and (binary UC.tipo_usuario='A' or binary UC.tipo_usuario='F') and UC.cod_usuario >= 0";
  $res   = Enviar($sock, $query);

  while ($linha = RetornaLinha($res))
  {
    $lista1[ $linha['cod_usuario'] ] = $linha['nome'];
  }
  return $lista1;
}

/* *********************************************************************
    RetornaCodUsuarioConvidado - Retorna lista com os cod_usuario dos convidados do curso
    entrada: $sock - sock de conexao
             $cod_curso - código do curso
    saida:   array [$cod_usuario] = cod_usuario dos convidados
*/
function RetornaCodUsuarioConvidado($sock, $cod_curso)
{

  //nome da base geral
  $dbnamebase = $_SESSION['dbnamebase'];

  // lembrando que somente convidados com intera��o devem aparecer no Intermap, pois somente eles tem acesso as ferramentas de comunicacao
  $query = "select UC.cod_usuario from ".$dbnamebase.".Usuario_curso UC where UC.cod_curso=".$cod_curso." and binary UC.tipo_usuario='Z'";
  $res   = Enviar($sock, $query);
  while ($linha = RetornaLinha($res))
  {
    $lista[ $linha[cod_usuario] ] = $linha[cod_usuario];
  }
  return $lista;
}

/* *********************************************************************
   RetornaCodUsuarioConvidadoComSemInteracao - retorna todos os convidados
   entrada: $sock - sock de conexao
            $cod_curso - código do curso
   saida: array [ cod_usuario ] == cod_usuario, array com todos os convidados do curso
   OBS: Esta funcao � utilizada no Intermap do bate-papo, que � a �nica ferramenta de comunica��o acessivel aos convidados sem interacao e visitantes
*/
function RetornaCodUsuarioConvidadoComSemInteracao($sock, $cod_curso)
{

  //nome da base geral
  $dbnamebase = $_SESSION['dbnamebase'];

  // lembrando que somente convidados com intera��o devem aparecer no Intermap, pois somente eles tem acesso as ferramentas de comunicacao
  $query = "select UC.cod_usuario from ".$dbnamebase.".Usuario_curso UC where UC.cod_curso=".$cod_curso." and UC.tipo_usuario='Z' or UC.tipo_usuario='z'";
  $res   = Enviar($sock, $query);
  while ($linha = RetornaLinha($res))
  {
    $lista[ $linha[cod_usuario] ] = $linha[cod_usuario];
  }
  return $lista;
}

/* *********************************************************************
   RetornaVisitantes - Retorna lista com os visitantes do curso
   Entrada: $sock - BASE INTERNA
            $cod_curso - código do curso
   Saida: array [$cod_usuario]=nome
*/
function RetornaVisitantes($sock, $cod_curso)
{

  //nome da base geral
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "select UC.cod_usuario, U.nome from ".$dbnamebase.".Usuario U, ".$dbnamebase.".Usuario_curso UC where UC.cod_curso = ".$cod_curso." and U.cod_usuario = UC.cod_usuario_global and binary UC.tipo_usuario='V'";
  $res   = Enviar($sock, $query);
  while ($linha = RetornaLinha($res))
  {
    $lista[ $linha[cod_usuario] ] = $linha[ nome ];
  }
  return $lista;
}

/* *********************************************************************
   RetornaCodUsuarioVisitantes - Retorna lista com os visitantes do curso
   Entrada: $sock - BASE INTERNA
            $cod_curso - código do curso
   Saida: array [$cod_usuario]=cod_usuario
*/
function RetornaCodUsuarioVisitantes($sock, $cod_curso)
{

  //nome da base geral
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "select UC.cod_usuario from ".$dbnamebase.".Usuario_curso UC where UC.cod_curso=".$cod_curso." and binary UC.tipo_usuario='V'";
  $res   = Enviar($sock, $query);
  while ($linha = RetornaLinha($res))
  {
    $lista[ $linha[cod_usuario] ] = $linha[ cod_usuario ];
  }
  return $lista;
}


/* *********************************************************************
   RetornaListaCodUsuarioNomeOF - Retorna a lista de usuario ordenador com formador por ultimo
   Entrada: $sock - BASE INTERNA
            $cod_curso - código do curso
   Saida: array [$cod_usuario]=nome
*/
function RetornaListaCodUsuarioNomeOF($sock, $cod_curso)
{

  //nome da base geral
  $dbnamebase = $_SESSION['dbnamebase'];

  // somente convidados com interacao devem aparecer no intermap
  $query ="select UC.cod_usuario,U.nome from ".$dbnamebase.".Usuario U, ".$dbnamebase.".Usuario_curso UC";
  $query.=" where UC.cod_curso=".$cod_curso." and U.cod_usuario = UC.cod_usuario_global and UC.cod_usuario>=0";
  $query.=" and (binary UC.tipo_usuario='A' or binary UC.tipo_usuario='F' or binary UC.tipo_usuario='Z')";
  $query.=" order by UC.tipo_usuario,U.nome";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista) > 0)
  {
    foreach($lista as $cod=>$linha)
    {
      $lista1[$linha['cod_usuario']]=$linha['nome'];
    }
  }
  return ($lista1);
}

/* *********************************************************************
   MontaLinkPerfil - Monta o link para o perfil
   Entrada: $cod_usuario - Codigo do usuario
            $nome - Nome do Usu�rio
   Saida: String com a linha <a href=...> nome</a>) Se n�o estiver para salvar em arquivo, 
          sen�o s� nome
*/
function MontaLinkPerfil($cod_usuario,$nome)
{
  global $SalvarEmArquivo;
  if (!isset($SalvarEmArquivo))
  {
    return "<a href=# onClick=return(OpenWindowPerfil(".$cod_usuario."));>".$nome."</a>";
  }
  else
  {
    return $nome;
  }
}


?>
