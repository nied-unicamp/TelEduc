<?php
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

  echo("              <form name=\"frmAgrupar\" method=\"post\" action=".$nome_pagina.">\n");

  // 180 - Clique aqui
  echo("              <a class=\"text\" href=\"#\" onClick=return(Submissao());>".RetornaFraseDaLista($lista_frases,180)."</a>\n");


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
