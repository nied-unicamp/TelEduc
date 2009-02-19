<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/estatistica/estat.inc

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
  ARQUIVO : administracao/estatistica/estat.inc
  ========================================================== */

/* **********************************************************************
   PreparaCabecalhoEstat - Monta o Cabecalho padr�o da p�gina da administracao/estatistica
   Entrada: Nenhuma
   Saida: Monta o cabe�alho no HTML
*/
function PreparaCabecalhoEstat($lista_frases)
{
  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("<head>\n");
  /* 1- Administra��o */
  echo("<title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title>\n");
  echo("<meta name=\"robots\" content=\"follow,index\" />\n");
  echo("<meta name=\"description\" content=\"\" />\n");
  echo("<meta name=\"keywords\" content=\"\" />\n");
  echo("<meta name=\"owner\" content=\"\" />\n");
  echo("<meta name=\"copyright\" content=\"\" />\n");
  echo("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("<link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("</head>\n");

  echo("<body text=#000000 bgcolor=#FFFFFF link=#0000FF vlink=#0000FF ALINK=#0000FF>\n");
  echo("<a name=\"topo\"></a>\n");
  echo("<h1><a href=\"index.htm\"><img src=\"../imgs/logo.gif\" border=\"0\" alt=\"TelEduc . Educa&ccedil;&atilde;o a Dist&acirc;ncia\" /></a></h1>\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
  echo("<tr>\n");
  echo("<td></td>\n");
  echo("<td valign=\"top\" id=\"topo\"><!--NAVEGACAO NIVEL 3-->\n");
  echo("<h3>".RetornaFraseDaLista($lista_frases,2)."</h3>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("<td valign=\"top\"><!--NAVEGACAO PRINCIPAL-->\n");
  echo("<ul id=\"nav\">\n");

  /* 3 - Cria��o de Curso */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,3),"index_criar_curso.php","");

  /* 125 - Editar Categorias */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,125),"editar_categoria.php","");

  /* 131 - Selecionar Categoria dos Cursos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,131),"selecionar_categoria.php","");

  /* 4 - Extra��o de Curso */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,4),"extracao/extrair_curso.php","");

  /* 141 - Inser��o de Cursos Extra�dos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,141),"insercao/inserir_curso.php","");

  /* 245 - Reutiliza��o de Cursos Encerrados */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,245),"resetar_curso.php","");

  /* 293 - Reenvio de dados aos coordenadores*/
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,293),"infocurso/reenvio.php","");
  
  /* 9 - Enviar e-mail para usu�rios */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,9),"enviar_email.php","");

  /* 8 - Trocar login */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,8),"trocar_login.php","");

  /* 5 - Consulta a Base de Dados */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,5),"consultar_base.php","");

  /* 153 - Estat�sticas do Ambiente */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,153),"estatistica/index.php","");

  /* 183 - Configurar dados do ambiente */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,183), "configurar.php", "");

  /* 11 - Cadastro de L�nguas */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,11),"cadastro_linguas.php","");

  /* 171 - Cadastro de texto da Ajuda */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,171),"ajuda/index.php","");

  /* 13 - Contato - NIED - Unicamp */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,13),"mailto:curso@nied.unicamp.br","");

  echo("</ul>\n");
  echo("<p align=\"center\"><a href=\"#\"><img src=\"../imgs/logoNied.gif\" alt=\"Nied\" width=\"28\" height=\"38\" border=\"0\" style=\"margin-right:8px;\" /></a> <a href=\"#\"><img src=\"../imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" width=\"44\" height=\"38\" border=\"0\" style=\"margin-right:6px;\" /></a> <a href=\"#\"><img src=\"../imgs/logoUnicamp.gif\" alt=\"UNICAMP\" width=\"35\" height=\"38\" border=\"0\" /></a></p>\n");
  echo("</td>\n");
  echo("<td width=\"100%\" valign=\"top\" style=\"padding-bottom:40px; padding-top:22px;\">\n");
}

/* **********************************************************************
   RetornaNumeroDeCursos - Retorna um array com n�mero de cursos ativos, encerrados e latentes, respectivamente
   Entrada: $sock - Socket de conex�o
            $cod_pasta - C�digo da pasta do curso
   Saida: array($ativos, $encerrados, $latentes)
*/
function RetornaNumeroDeCursos($sock,$cod_pasta)
{
  global $lista_frases;

  /* Número total de cursos */
  if (!isset($cod_pasta))
    $query = "select * from Cursos";
  else if ($cod_pasta=='nenhum')
    $query = "select * from Cursos where cod_pasta is NULL";
  else if ($cod_pasta=='todos')
    $query = "select * from Cursos where cod_pasta is not NULL";
  else
    $query = "select * from Cursos where cod_pasta=".$cod_pasta."";

  $res = Enviar($sock,$query);
  $num_cursos = RetornaNumLinhas($res);

  /*Cursos com datas de ínicio e fim definidas*/
  if (!isset($cod_pasta))
    $query = "select curso_inicio,curso_fim from Cursos where curso_inicio is not NULL";
  else if ($cod_pasta=='nenhum')
    $query = "select curso_inicio,curso_fim from Cursos where curso_inicio is not NULL and cod_pasta is NULL";
  else if ($cod_pasta=='todos')
    $query = "select curso_inicio,curso_fim from Cursos where curso_inicio is not NULL and cod_pasta is not NULL";
  else
    $query = "select curso_inicio,curso_fim from Cursos where curso_inicio is not NULL and cod_pasta=".$cod_pasta."";


  $res = Enviar($sock,$query);
  $nao_latentes = RetornaNumLinhas($res);
  $array = RetornaArrayLinhas($res);

  $horatual = time();

  $ativos = 0; $encerrados = 0;
  for ($i = 0; $i < $nao_latentes; $i++)
  {
   if ($horatual > $array[$i]['curso_fim'])
     $encerrados++;
   else if ($horatual < $array[$i]['curso_fim'] && $horatual > $array[$i]['curso_inicio'])
     $ativos++;
  }
  $latentes = $num_cursos - $encerrados - $ativos;

  //return array($ativos, $encerrados, $latentes);

  /*Cabeçalho da tabela*/
  echo("<tr class=\"head01\">\n");
  // 163 - Ativos
  echo("<td>".RetornaFraseDaLista($lista_frases,163)."</td>\n");
  // 164 - Encerrados
  echo("<td>".RetornaFraseDaLista($lista_frases,164)."</td>\n");
  // 165 - Latentes
  echo("<td>".RetornaFraseDaLista($lista_frases,165)."</td>\n");
  echo("<td></td>\n");
  echo("</tr>\n");

  //list($ativos, $encerrados, $latentes) = RetornaNumeroDeCursos($sock);

  echo("<tr><td>".$ativos."</td><td>".$encerrados."</td><td>".$latentes."</td><td></td></tr>\n");
}

/* **********************************************************************
   ImprimeTamanhoDosCursos - Imprime os nomes dos cursos e seus respectivos tamanhos (em Kbytes) como parte
                             de uma tabela
   Entrada: $sock - Socket de conex�o
            $diretorio - Diretorio com os arquivos dos cursos
            $cod_pasta - C�digo da pasta do curso
   Saida: Monta parte da tabela do tamanho dos cursos no HTML
*/
function ImprimeTamanhoDosCursos($sock,$diretorio,$cod_pasta)
{
  global $lista_frases;

  if (!isset($cod_pasta))
    $query="select cod_curso,nome_curso from Cursos order by nome_curso";
  else if ($cod_pasta=='nenhum')
    $query="select cod_curso,nome_curso from Cursos where cod_pasta IS NULL order by nome_curso";
  else
    $query="select cod_curso,nome_curso from Cursos where cod_pasta=".$cod_pasta." order by nome_curso";

  $res = Enviar($sock,$query);

  $array = RetornaArrayLinhas($res);
  $tam_array = RetornaNumLinhas($res);

  if (count($array)>0)
  {
    echo("<tr class=\"head01\">\n");
    /*Cabe�alho da tabela*/

    // 157 - Curso
    echo("<td>".RetornaFraseDaLista($lista_frases,157)."</td>\n");

    // 158 - Tamanho (Kbytes)
    echo("<td>".RetornaFraseDaLista($lista_frases,158)."</td>\n");
    echo("</tr>\n");

    // Não há cursos na categoria
    if ($tam_array == 0)
      echo("<tr><td colspan=\"2\">".RetornaFraseDaLista($lista_frases,159)."</td></tr>\n");


    for ($i = 0; $i < $tam_array; $i++)
    {
      $valor=shell_exec("du -ks ".$diretorio."/".$array[$i]['cod_curso']."");
      $tam=explode('/', $valor);
      echo("<tr><td>".$array[$i]['nome_curso']."</td><td>".$tam[0]."&nbsp;</td></tr>\n");
    }
  }
  else
    /* 159 - N�o h� nenhum curso. */
    echo(RetornaFraseDaLista($lista_frases,159)."\n<br>\n");

}

/* **********************************************************************
   ImprimeNumeroDeAlunos - Imprime os nomes dos cursos e seus respectivos n�meros de alunos
                           e formadores como parte de uma tabela
   Entrada: $sock - Socket de conex�o
            $cod_pasta - C�digo da pasta do curso
   Saida: Monta a tabela do n�mero de alunos por curso no HTML
*/
function ImprimeNumeroDeAlunos($sock,$cod_pasta)
{
  global $lista_frases;

  if (!isset($cod_pasta))
    $query="select cod_curso,nome_curso from Cursos order by nome_curso";
  else if ($cod_pasta=='nenhum')
    $query="select cod_curso,nome_curso from Cursos where cod_pasta IS NULL order by nome_curso";
  else
    $query="select cod_curso,nome_curso from Cursos where cod_pasta=".$cod_pasta." order by nome_curso";

  $sock = Conectar("");
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);

  if (count($lista)>0)
  {
    $TotalInsc=0; $TotalAc=0; $TotalForm=0;
    echo("          <tr class=\"head01\">\n");
    /*Cabe�alho da tabela*/
    // 157 - Curso
    echo("            <td>".RetornaFraseDaLista($lista_frases,157)."</td>\n");
    // 160 - Alunos Inscritos
    echo("            <td>".RetornaFraseDaLista($lista_frases,160)."</td>\n");
    // 161 - Alunos Aceitos
    echo("            <td>".RetornaFraseDaLista($lista_frases,161)."</td>\n");
    // 162 - Formadores
    echo("            <td>".RetornaFraseDaLista($lista_frases,162)."</td>\n");
    echo("          </tr>\n");
    Desconectar($sock);
    $sock = Conectar("");
    $i = 0;
    
    foreach($lista as $cod => $linha)
    {
      $cod_curso=$linha['cod_curso'];
      $nome_curso=$linha['nome_curso'];
      Desconectar($sock);
      $sock = Conectar("");
      $query="select binary tipo_usuario 'tipo_usuario',count(*) 'qtde' from Usuario_curso where cod_usuario>=0 and cod_curso = ".$cod_curso." group by binary tipo_usuario";
      $res=Enviar($sock,$query);
      MudarDB($sock,$cod_curso);
      $dados_curso=RetornaArrayLinhas($res);

      $AInscritos=0; $AAceitos=0; $Formadores=0;
      if (count($dados_curso)>0)
      {
        foreach($dados_curso as $cod => $linha1)
        {
          if ($linha1['tipo_usuario']=='r' || $linha1['tipo_usuario']=='a')
            $AInscritos+=$linha1['qtde'];
          else if ($linha1['tipo_usuario']=='A')
            $AAceitos=$linha1['qtde'];
          else if ($linha1['tipo_usuario']=='F')
            $Formadores=$linha1['qtde'];
        }
        $AInscritos += $AAceitos;
      }

      echo("          <tr><td>".$nome_curso."</td><td>".$AInscritos."</td><td>".$AAceitos."</td><td>".$Formadores."</td></tr>\n");
      $i++;
      $TotalInsc += $AInscritos;
      $TotalAc += $AAceitos;
      $TotalForm += $Formadores;
    }
    MudarDB($sock,"");

    echo("          <tr><td><b>Todos</b></td><td>".$TotalInsc."</td><td>".$TotalAc."</td><td>".$TotalForm."</td></tr>\n");
  }
  else
    /* 159 - N�o h� nenhum curso. */
    echo("".RetornaFraseDaLista($lista_frases,159)."\n<br>\n");
//   echo("<p>\n");
}

/* **********************************************************************
   FraseTipoAgrupamento - Mostra a frase e o bot�o de agrupamento por categoria de cursos.
   Entrada: $nome_pagina - nome da p�gina em vigor.
   Saida: nenhuma.
*/
function FraseTipoAgrupamento($nome_pagina,$agrupar)
{
  global $lista_frases;

  /* Fun��o javascript */

  echo("              <script language=\"javascript\" type=\"text/javascript\">\n");
  echo("                function Submissao() {\n");
  echo("                  document.frmAgrupar.submit();\n");
  echo("                  return false;\n");
  echo("                }\n");

  echo("              </script>\n");

  /* Principal */

  echo("              <form name=\"frmAgrupar\" method=post action=".$nome_pagina.">\n");

  // 180 - Clique aqui
  echo("              <a class=text href=# onClick=return(Submissao());>".RetornaFraseDaLista($lista_frases,180)."</a>\n");


  if (!isset($agrupar) || $agrupar==0)
  {
    // 181 - caso queira agrupar os cursos por categoria.
    echo("              ".RetornaFraseDaLista($lista_frases,181)."\n");
    echo("              <input type=\"hidden\" name=\"agrupar\" value=\"1\" />\n");
  }
  else
  {
    // 182 - caso queira desagrupar os cursos.
    echo("               ".RetornaFraseDaLista($lista_frases,182)."\n");
    echo("              <input type=\"hidden\" name=\"agrupar\" value=\"0\" />\n");
  }

  echo("              </form>\n");
}

/* **********************************************************************
   RetornaPastas - Retorna um array com as pastas dos cursos.
   Entrada: $sock - socket da conex�o atua�.
   Saida: Vetor com cod_pasta e pasta de todas categorias de cursos.
*/
function RetornaPastas($sock)
{
  $query="select * from Cursos_pastas order by pasta";
  $res=Enviar($sock,$query);
  return (RetornaArrayLinhas($res));
}
?>
