<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/inserir_curso3.php

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
  ARQUIVO : administracao/inserir_curso3.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../admin.inc");
  include($bibliotecas."extracao.inc");  
  include("insercao.inc");
  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");
  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("  }\n");
  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  VerificaAutenticacaoAdministracao();

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);


  $dir_extracao=RetornaDiretorio('Extracao');
  $dir_arquivos=RetornaDiretorio('Arquivos');
  $caminho_mysqldump=RetornaDiretorio('mysqldump');
  echo("<ul><table width=700><tr><td valign=top>\n");


  /* 141 - Inser��o de Curso */
  PreparaCabecalhoOpcao(RetornaFraseDaLista($lista_frases,141));

  flush();

  // atualiza a categoria do curso de acordo com a categoria escolhida pelo
  // usuario em "inserir_curso3.php"
  $query  = "UPDATE Cursos SET cod_pasta = ".$nova_categoria." WHERE ";
  $query .= "cod_curso = ".$cod_curso;

  $sock=Conectar("");
  $res=Enviar($sock,$query);
  Desconectar($sock);

  /* 261 - A categoria do curso foi atualizada. */
  echo("<p>".RetornaFraseDaLista($lista_frases,261).". <p>\n\n");

  /* 152 - Aten��o : Os arquivos utilizados na inser��o do curso n�o foram apagados... */
  echo("<p>".RetornaFraseDaLista($lista_frases,152).". <p>\n\n");

  /* 113 -  Opera��o completada com sucesso! */
  echo("<b>".RetornaFraseDaLista($lista_frases,113)."</b><p>\n");

  echo("<form action=../index.php?>\n");

  /* 23 - Voltar (Ger) */
  echo("<input type=submit value='".RetornaFraseDaLista($lista_frases_geral,23)."'>\n");

  echo("</form>\n");

  echo("<br><br>\n");

  echo("</td></tr></table></ul>\n");

  echo("<hr>\n");

  echo("</body>\n");
  echo("</html>\n");
?>
