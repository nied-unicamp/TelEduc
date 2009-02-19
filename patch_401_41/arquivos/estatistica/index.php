<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/estatistica/index.php

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
  ARQUIVO : administracao/estatistica/index.php
  ========================================================== */

  $bibliotecas="../../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("estat.inc");

  VerificaAutenticacaoAdministracao();

  if (isset($cod_lingua) && $cod_lingua!="")
  {
    $cod_lingua_s=$cod_lingua;
    $sock=Conectar("");
    for ($c=-5;$c<20;$c++)
      if (ListaDeFrasesEmMemoria($c))
        MemorizaListaDeFrases($sock, $c);
    Desconectar($sock);
  }

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  Desconectar($sock);

  PreparaCabecalhoEstat($lista_frases);

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");

  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("}\n");

  echo("</script>\n");
  /* Fim do JavaScript */

  /* 153 - Estat�sticas do Ambiente */
  PreparaCabecalhoOpcao(RetornaFraseDaLista($lista_frases,153));


  /* 154 - Quantidade de Cursos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,154),"num_cursos.php","");
  echo("<br>\n");
  /* 155 - Quantidade de Alunos e Formadores por Curso */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,155),"alunos_curso.php","");
  echo("<br>\n");
  /* 156 - Tamanho dos Arquivos dos Cursos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,156),"tam_curso.php","");

  echo("<ul class=\"btAuxTabsBottom\">\n");
  /* 23 - Voltar (geral) */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases_geral,23),"../index.php","");
  echo("</ul></B>\n");

  echo("</td>\n");
  echo("</td></tr></table>\n");

  echo("</body>\n");
  echo("</html>\n");

?>
