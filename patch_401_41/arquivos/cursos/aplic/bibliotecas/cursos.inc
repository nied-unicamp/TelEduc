<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/cursos.inc

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
*/

/*==========================================================
  ARQUIVO : bibliotecas/cursos.inc
  ========================================================== */

/* *********************************************************************
   NomeCurso - Retorna o nome do curso
   Entrada: $sock - SOCK Externo
   Saida: string contendo o nome do curso
*/
function NomeCurso($sock, $cod_curso)
{
  $query="select nome_curso from Cursos where cod_curso = ".VerificaNumeroQuery($cod_curso);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return $linha['nome_curso'];
}


/* *********************************************************************
   RetornaCursoConfig - retorna o valor associado a uma informa�o
                        especificada por item.
   Entrada: $sock - Base de dados do curso,
            $item - informa�o cujo valor �requisitado

   Saida:   string contendo o valor solicitado.
*/

function RetornaCursoConfig($sock, $item)
{
  $query = "select valor from Config where item = '".VerificaStringQuery($item)."'";
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);
  return($linha['valor']);
}

/* *********************************************************************
   AlteraCursoConfig - altera o valor associado a uma informa�o
                       especificada por item.
   Entrada: $sock - Base de dados do curso,
            $item - informa�o cujo valor ser�alterado,
            $valor - novo valor.

   Saida:   true se bem sucedido, do contr�io false.
*/

function AlteraCursoConfig($sock, $item, $valor)
{
  $query = "update Config set valor = '".$valor."' where item = '".VerificaStringQuery($item)."'";
  $res = Enviar($sock, $query);
  return($res);
}

/* *********************************************************************
   RetornaCodCoordenador - Retorna codigo coordenador do curso
   Entrada: $sock - Base de dados do curso
            $cod_curso - codigo do curso
   Saida:   codigo do coordenador do curso
*/

function RetornaCodCoordenador($sock, $cod_curso)
{
  $query="select cod_coordenador from Cursos where cod_curso=".VerificaNumeroQuery($cod_curso);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  return $linha['cod_coordenador'];
}

/* *********************************************************************
   DadosCursoParaEmail - Retorna os dados do Curso para usar para enviar
                         e-mail
   Entrada: $sock - Base de dados do curso
            $cod_curso - codigo do curso
   Saida: array com ['nome_curso'] - nome do curso
                    ['nome_coordenador'] - nome do coordenador
                    ['email'] - email do coordenador
                    ['login'] - login do coordenador
*/

function DadosCursoParaEmail($sock, $cod_curso)
{

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $cod_coordenador = RetornaCodCoordenador($sock,$cod_curso);
  $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_coordenador, $cod_curso);

  $query="select C.nome_curso 'nome_curso',U.nome 'nome_coordenador',U.email 'email',U.login 'login' from ".$dbnamebase.".Cursos C, ".$dbnamebase.".Usuario U left join ".$dbnamebase.".Usuario_curso UC on UC.cod_usuario_global=U.cod_usuario where UC.cod_curso=".VerificaNumeroQuery($cod_curso)." and C.cod_curso=".VerificaNumeroQuery($cod_curso)." and U.cod_usuario=".$cod_usuario_global." and C.cod_coordenador=UC.cod_usuario";

  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  return $linha;
}

/* *********************************************************************
   DadosAdministracaoParaEmail - Retorna os dados da administracao para
                                 Enviar e-mail
   Entrada: $sock - SOCK Externo
   Saida: e-mail do coordenador
*/

function DadosAdministracaoParaEmail($sock)
{
  $query="select * from Config where item='adm_email'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha[1]);
}

/* *********************************************************************
   RetornaStatusCurso - Retorna se o curso j�est�em andamento, ou se j�
                        se encerrou
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             BASE DO CURSO
             $cod_curso - Curso em que ele est�
   Saida: N - N� come�u ainda
          A - Em Andamento
          E - Encerrado
          I - Invalido
*/
function RetornaStatusCurso($sock,$cod_curso)
{
  $query="select curso_inicio,curso_fim from Cursos where cod_curso=".VerificaNumeroQuery($cod_curso);
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    $linha=RetornaLinha($res);
                $hoje=time();
                $ontem=$hoje - 86400;
    if ($linha[0]>$hoje)
      return ("N");
    else if ($linha[1]>=$ontem)
      return ("A");
    else
      return ("E");
  }
  else
  {
    return ("I");
  }
}

/* *********************************************************************
   PreparaCabecalhoNomeCurso - Prepara o cabecalho com o nome do curso
   Entradas: $cod_curso
   Saida: String com a parte cabecalho do nome do curso
*/
function PreparaCabecalhoNomeCurso($sock, $cod_curso)
{
//   global $sock;
  $query="select nome_curso from Cursos where cod_curso=".VerificaNumeroQuery($cod_curso);

  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
//   $saida ="<table cellpadding=0 cellspacing=0 border=0 width=100%><tr><td><font face=Arial,Verdana size=+1><b><nobr>".$linha['nome_curso']."</nobr></b></font></td></tr><tr><td bgcolor=#000000><img src=../figuras/invisivel.gif width=1 height=1 border=0></td></tr></table>";
  return $linha['nome_curso'];
}

/* *********************************************************************
   PreparaAjuda - Prepara a ajuda das paginas
   Entradas: $cod_curso
             $cod_ferramenta - ferramenta atual
             $cod_pagina
   Saida: string com o link da ajuda
*/
function PreparaAjuda($sock, $cod_curso,$cod_ferramenta,$cod_pagina=null, $cod_usuario) {

//   global $cod_lingua_s,$sock,$cod_usuario,$bibliotecas;
  $bibliotecas="../bibliotecas/";
  $ajuda_nao_encontrada = true;
  
  if (isset($cod_pagina))
  {
    $eformador=EFormador($sock,$cod_curso,$cod_usuario);
    $sock = MudarDB($sock,""); 
    $query="select nome_pagina,texto from Ajuda where cod_ferramenta=".VerificaNumeroQuery($cod_ferramenta)." and cod_pagina=".VerificaNumeroQuery($cod_pagina)." and cod_lingua=".VerificaNumeroQuery($_SESSION['cod_lingua_s'])." and tipo_usuario='".($eformador ? 'F' : 'A' )."'";
    $res=Enviar($sock,$query);
    if (RetornaNumLinhas($res)>0)
    {
      $ajuda_nao_encontrada=false;
      $ajuda_link=$bibliotecas."../ajuda/ajuda.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_pagina=".$cod_pagina."&amp;tipo_usuario=".($eformador ? 'F' : 'A' );
      $html_link="<li><img src=\"../imgs/icAjuda.gif\" border=\"0\" alt=\"Ajuda\" />&nbsp;</li>";
      $html_link.="<li><a href=\"".$ajuda_link."\" onclick=\"window.open('".$ajuda_link."','AjudaDisplay','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');return false;\" target=\"blank\">Ajuda</a></li>";
      $html_link.="<li>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</li>";
    }
    $sock = MudarDB($sock,$cod_curso);
  }

  // Esse ficar�provis�iamente, at�que todas as p�inas estiverem listadas
  if ($ajuda_nao_encontrada)
  {
//     // Pegando o subtitulo da ferramenta para usar para linkar a ajuda
//     $tmp=explode("> - ",$cabecalho);
//     $tmp1=explode("</b>",$tmp[1]);
//     $aname=RetiraEspacoEAcentos($tmp1[0]);

    // Checando se existe o diret�io com a Ajuda da ferramenta
    if (EFormador($sock,$cod_curso,$cod_usuario))
      $arquivo=$cod_lingua_s."_".$cod_ferramenta."_f.html";
    else
      $arquivo=$cod_lingua_s."_".$cod_ferramenta."_a.html";  

    if (file_exists($bibliotecas."../ajuda/".$arquivo))
    {
      $ajuda_link=$bibliotecas."../ajuda/".$arquivo;
      // ?? - Ajuda
      $html_link="<li><img src=\"../imgs/icAjuda.gif\" border=\"0\" alt=\"Ajuda\" />&nbsp;</li>";
      $html_link.="<li><a href=\"".$ajuda_link."\" onClick=\"window.open('".$ajuda_link."','AjudaDisplay','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');return false;\" target=\"blank\">Ajuda</a></li>";
      $html_link.="<li>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</li>";
    }
  }
  
  return $html_link;
}

/* *********************************************************************
   PreparaCabecalho - Prepara o cabecalho das p�inas
   Entradas: $cod_curso
             $cabecalho - Premontagem do cabecalho
             $cod_ferramenta - ferramenta atual
   Saida: string com o cabecalho
*/
function PreparaCabecalho($cod_curso,$cabecalho,$cod_ferramenta,$cod_pagina=null)
{

//   global $cod_lingua_s,$sock,$cod_usuario,$bibliotecas;
  $bibliotecas="../bibliotecas/";
  $ajuda_nao_encontrada=true;
  if ($cod_ferramenta!=12 && $cod_ferramenta!=13 && $cod_ferramenta!=18 && $cod_ferramenta!=19 && $cod_ferramenta !=30 && $cod_ferramenta>0)
  {
    $busca_link=$bibliotecas."../busca/busca.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta;
    $busca_link="<td class=menu><nobr>&nbsp;<a class=menu href=".$busca_link." onClick=\"window.open('".$busca_link."','BuscaDisplay','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');return false;\" target=blank>Busca</a>&nbsp;</nobr></td>";
  }

  if (isset($cod_pagina))
  {
    $eformador=EFormador($sock,$cod_curso,$cod_usuario);
    $sock = MudarDB($sock,""); 
    $query="select nome_pagina,texto from Ajuda where cod_ferramenta=".VerificaNumeroQuery($cod_ferramenta)." and cod_pagina=".VerificaNumeroQuery($cod_pagina)." and cod_lingua=".VerificaNumeroQuery($_SESSION['cod_lingua_s'])." and tipo_usuario='".($eformador ? 'F' : 'A' )."'";
    $res=Enviar($sock,$query);
    if (RetornaNumLinhas($res)>0)
    {
      $ajuda_nao_encontrada=false;
      $ajuda_link=$bibliotecas."../ajuda/ajuda.php?cod_ferramenta=".$cod_ferramenta."&cod_pagina=".$cod_pagina."&tipo_usuario=".($eformador ? 'F' : 'A' );
      $html_link="<td class=menu><nobr>&nbsp;<a class=menu href=".$ajuda_link." onClick=\"window.open('".$ajuda_link."','AjudaDisplay','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');return false;\" target=blank>Ajuda</a>&nbsp;</nobr></td>";
    }
    $sock = MudarDB($sock,$cod_curso);
  }

  // Esse ficar�provis�iamente, at�que todas as p�inas estiverem listadas
  if ($ajuda_nao_encontrada)
  {
    // Pegando o subtitulo da ferramenta para usar para linkar a ajuda
    $tmp=explode("> - ",$cabecalho);
    $tmp1=explode("</b>",$tmp[1]);
    $aname=RetiraEspacoEAcentos($tmp1[0]);

    // Checando se existe o diret�io com a Ajuda da ferramenta
    if (EFormador($sock,$cod_curso,$cod_usuario))
      $arquivo=$cod_lingua_s."_".$cod_ferramenta."_f.html";
    else
      $arquivo=$cod_lingua_s."_".$cod_ferramenta."_a.html";  

    if (file_exists($bibliotecas."../ajuda/".$arquivo))
    {
      $ajuda_link=$bibliotecas."../ajuda/".$arquivo."#".$aname;
      // ?? - Ajuda
      $html_link="<td class=menu><nobr>&nbsp;<a class=menu href=".$ajuda_link." onClick=\"window.open('".$ajuda_link."','PerfilDisplay','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');return false;\" target=blank>Ajuda</a>&nbsp;</nobr></td>";
    }
  }

  $saida=PreparaCabecalhoNomeCurso($sock, $cod_curso);
  $saida.="<table width=100% cellpadding=0 cellspacing=0 border=0>\n";
  $saida.="  <tr><td>".$cabecalho."</td>\n";
  $saida.="  <td width=1%>\n";
  $saida.="    <table border=0><tr>\n";
//  $saida.="      <tr><td class=menu>&nbsp;Busca&nbsp;</td><td class=menu>&nbsp;Ajuda&nbsp;</td><tr>\n";
//  $saida.="      <tr><td>".$ajuda_link."</td><tr>\n";
  $saida.=$busca_link.$html_link;
  $saida.="    </tr></table>\n";
  $saida.="  </td></tr>\n";
  $saida.="</table>\n";
  return $saida;
}

/* *********************************************************************
   CompletarDadosCurso - Retorna True se faltam dados do curso para completar
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             $cod_curso - Curso em que ele est�
   Saida: true se precisa completar dados
*/
function CompletarDadosCurso($sock,$cod_curso)
{
  $query="select inscricao_inicio,inscricao_fim,curso_inicio,curso_fim,informacoes,publico_alvo,tipo_inscricao from Cursos where cod_curso=".VerificaNumeroQuery($cod_curso);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha['inscricao_inicio']=="" || $linha['inscricao_fim']=="" || $linha['curso_inicio']=="" || $linha['curso_fim']=="" || $linha['informacoes']=="" || $linha['publico_alvo']=="" || $linha['tipo_inscricao']=="")
    return true;
  return false;
}

/* **********************************************************************
   RetornaDadosCurso - Retorna os dados do curso que ser�o inseridos na 
                       tabela 'Cursos_extraidos'
   Entrada: $sock - Socket da conex�o
            $cod_curso - C�digo do curso a ter seus dados retornados
   Saida: Array com dados do curso que est� sendo extra�do
*/
function RetornaDadosCurso($sock,$cod_curso)
{
  if (function_exists("RetornaDadosCursoFachada"))
  {
    return(RetornaDadosCursoFachada($sock, $cod_curso));
    exit;
  }

  $query = "select curso_inicio, curso_fim from Cursos where cod_curso = ".$cod_curso;
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  $dados['curso_inicio'] = $linha['curso_inicio'];
  $dados['curso_fim'] = $linha['curso_fim'];

  return $dados;
}
?>