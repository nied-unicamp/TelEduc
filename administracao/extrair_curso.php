<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/extracao/extrair_curso.php

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
  ARQUIVO : administracao/extracao/extrair_curso.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("extracao.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");

  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("}\n");

  echo("</script>\n");
  /* Fim do JavaScript */

  echo("<ul><table width=\"700\"><tr><td valign=\"top\">\n");

  /* 4 - Extra��o de Curso */
  PreparaCabecalhoOpcao(RetornaFraseDaLista($lista_frases,4));
 
  echo("<form action=\"extrair_curso2.php?\" method=\"get\">\n");

  /* 108 - Selecione abaixo o curso a ser extra�do* do ambiente TelEduc: */
  echo(RetornaFraseDaLista($lista_frases,108)."<br>\n");

  $lista=RetornaCursosExtraiveis();

  if (count($lista)>0)
  {
    echo("<select name=cod_curso>\n");
    foreach ($lista as $cod_curso => $nome)
      echo("  <option value=".$cod_curso.">".$nome."</option>\n");
    echo("</select><Br>\n");
  }
  else
    /* 118 - Nenhum curso dispon�vel. */
    echo(RetornaFraseDaLista($lista_frases,118)."<br><br>\n");

  if (count($lista)>0)
    /* 110 - Extrair */
    echo("<input type=\"submit\" value='".RetornaFraseDaLista($lista_frases,110)."'>&nbsp;&nbsp;&nbsp;&nbsp;\n");

  /* 2 - Cancelar (Ger) */
  echo("<input type=\"button\" value='".RetornaFraseDaLista($lista_frases_geral,2)."' onClick=\"history.go(-1);\">\n");

  echo("</b><br><br>\n");

  /* 109 - A opera��o de extrair um curso do ambiente remove os dados e arquivos relacionados ao curso escolhido para uma �rea isolada do ambiente, da qual podem ser retirados sem causar danos ao ambiente ou aos demais cursos nele armazenados. O curso ficar� inacess�vel a partir do ambiente. */
  echo(" * ".RetornaFraseDaLista($lista_frases,109)."<br>\n");

  echo("</form>\n");

  echo("</td></tr></table></ul>\n");

  echo("<hr>\n");

  echo("</body>\n");
  echo("</html>\n");
?>
