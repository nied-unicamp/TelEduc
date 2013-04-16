<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/inserir_curso.php

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
  ARQUIVO : administracao/inserir_curso.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."extracao.inc");
  include("../administracao/admin.inc");
  include("insercao.inc");
//   include("../extracao/extracao.inc");
  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");
  echo("function Iniciar() {\n");
  echo("	startList();\n");
  echo("}\n");
  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  VerificaAutenticacaoAdministracao();

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);


  echo("<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 141 - Inser��o de Curso */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,141)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  

  echo("<!-- Tabelao -->\n");
  echo("<form name=fmrInsere action=verifica_versao.php? method=post>\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaInterna\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  echo("<td><ul class=\"btAuxTabs\">\n");

  /* 23 - Voltar (Ger) */
  echo("<li><span style=\"href: #\" title=\"voltar\" onClick=\"document.location='../administracao/index.php'\"  >".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");

  echo("</ul></td></tr>\n");
  echo("<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  echo("<tr class=\"head\"><td>\n");
  /* 142 - Selecione abaixo o curso a ser inserido no ambiente TelEduc: */
  echo(RetornaFraseDaLista($lista_frases,142)."</td><td>\n");
  //aqui pegamos a lista de todos os cursos inseriveis localizados na pasta extraidos para list�-los em um select
  $lista_inseriveis=RetornaCursosInseriveis();

  /* 335 - Selecione abaixo a categoria que voc� deseja inserir o curso:*/
  echo(RetornaFraseDaLista($lista_frases,335)."</td></tr><tr><td>\n");

  //aqui pegamos a lista de Todos os cursos para comparar com os cursos inseriveis. Se encontrarmos algum de nome igual avisamos o usu�rio para que ele n�o insira duas vezes o mesmo curso.
  $sock=Conectar("");
  $query = "SELECT nome_curso, curso_inicio, curso_fim FROM Cursos";
  $res=Enviar($sock, $query);
  $lista_cursos=RetornaArrayLinhas($res);
  

  if (count($lista_inseriveis)>0)
  {
    echo("<select class=input name=cod_pasta>\n");
    /* 333 - Vers�o */
    foreach ($lista_inseriveis as $cod_pasta => $dados_curso) {
    	if (!Curso_Existe($dados_curso, $lista_cursos)) 
    		echo("  <option value='$cod_pasta'>".$dados_curso['nome_curso']." -  ".$dados_curso['data_extracao']." - (".RetornaFraseDaLista($lista_frases,333)." ".$dados_curso['versao']." )</option>\n");
		else
      	/* 334 - Curso j� inserido*/
			echo("  <option value='$cod_pasta'>(".RetornaFraseDaLista($lista_frases,334).") ".$dados_curso['nome_curso']." - ".$dados_curso['data_extracao']." - (".RetornaFraseDaLista($lista_frases,333)." ".$dados_curso['versao'].")</option>\n");
  	 }
    echo("</select><br />\n");
  } else {
    /* 118 - Nenhum curso dispon�vel. */
    echo(RetornaFraseDaLista($lista_frases,118)."<br><br>\n");
  }
  echo("</td><td>\n");
  
  $categoria=RetornaCategorias();

  if (count($categoria) > 0) {

     echo("<select class=input name=categoria>\n");
     echo("<option value='null'>&nbsp;</option>\n");
  
     foreach ($categoria as $categ => $cod_categoria) {
       echo("<option value='$categ'> $cod_categoria </option>\n");
     }
     
     echo ("</select><br />");
  } else {
     echo("<input type='hidden' name='categoria' value='NULL'>");
  }

  echo("</td></tr></table>\n");

  echo("<div align=\"right\">\n");

  if (count($lista_inseriveis)>0) {
    /* 143 - Inserir */
    echo("<input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,143)."\" onClick=\"document.fmrInsere.submit();\" type=\"button\" />\n");
  }


  echo("</div>\n");
  echo("</td></tr></table>\n");

  echo("</form>\n");

  echo("</td></tr>\n");
  include("../cursos/aplic/tela2.php");
  echo("</body>\n");
  echo("</html>\n");
?>
