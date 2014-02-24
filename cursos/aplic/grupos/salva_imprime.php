<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/grupos/salva_imprime.php

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
  ARQUIVO : cursos/aplic/grupos/grupos.php
  ========================================================== */

  	$bibliotecas="../bibliotecas/";
  	include($bibliotecas."geral.inc");
  	include("grupos.inc");

  	require_once("../xajax_0.5/xajax_core/xajax.inc.php");
 
  	$cod_ferramenta=12;
  	$cod_ferramenta_ajuda=12;
  	$cod_pagina_ajuda=1;

  	include("../topo_tela.php");

        if($SalvarEmArquivo){
          echo("    <style>\n");
          include "../js-css/ambiente.css";
          include "../js-css/tabelas.css";
          include "../js-css/navegacao.css";
          echo("    </style>\n");
        }

	echo("    <script type=\"text/javascript\">\n");

	echo("    function ImprimirRelatorio()\n");
	echo("    {\n");
  	echo("      if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape')\n");
 	echo("      {\n");
 	echo("        self.print();\n");
 	echo("      }\n");
 	echo("      else\n");
 	echo("      {\n");
  /* 51- Infelizmente n� foi poss�el imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
  	echo("        alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
  	echo("      }\n");
  	echo("    }\n\n");

	echo("    function SalvaRelatorio(salvaRel)\n");
	echo("    {\n");
        echo("      salvaRel.action=\"salvar_arquivo.php\";\n");
        echo("      salvaRel.submit();\n");
 	echo("    }\n");
  	
  	echo("    function OpenWindowPerfil(id)\n");
	echo("    {\n");
  	echo("      window.open(\"../perfil/exibir_perfis.php?");
  	echo("      cod_curso=".$cod_curso."&cod_aluno[]=\" + id, \"PerfilDisplay\",\"width=600,height=400,");
  	echo("      top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  	echo("      return(false);\n");
  	echo("    }\n\n");

  	echo("    function Iniciar()\n");
	echo("    {\n");
  	echo("      startList();\n");
  	echo("    }\n\n");

	echo("    </script>\n");

        echo("    </head>\n");
        echo("    <body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=\"white\" onload=\"Iniciar();\" >\n");
        echo("      <table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
        echo("        <tr>\n");
        echo("          <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

        echo("    <h3 style=\"margin-top:20px;\">".NomeCurso($sock,$cod_curso)."</h3>\n");
	echo("    <h4 style=\"margin-top:10px;\">".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,78)."</h4><br />\n");
	echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
	echo("    <tr>\n");
  	echo("      <td valign=\"top\">\n");
  	
    	echo("          <form action=\"salvar_arquivo.php\" method=\"get\" name=\"salvaRel\">\n");
    	echo("            <input type=hidden name=cod_curso value=".$cod_curso." />\n");
    	echo("            <input type=hidden name=nome_arquivo value='grupos.html' />\n");
    	echo("            <input type=hidden name=origem value='grupos' />\n");
       	echo("          </form>\n");

	echo("        <ul class=\"btAuxTabs\">\n");
	if(!$SalvarEmArquivo){
       	/* G 13 - Fechar */
       	echo("    <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
        
  	/* 22 - Salvar Em Arquivo */
       	echo("    <li><span onclick=\"SalvaRelatorio(document.salvaRel);\">".RetornaFraseDaLista($lista_frases,24)."</span></li>\n");
        

       	/* G 14 - Imprimir */
       	echo("    <li><span onclick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");
	}
	echo("        </ul>\n");
  	echo("      </td>\n");
  	echo("    </tr>\n");
	echo("    <tr>\n");
        echo("      <td>\n");
	$lista_grupos=RetornaListaGrupos($sock, $cod_curso);
	echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">");
	
	$num_usuario=0;
	foreach ($lista_grupos as $cod => $linha)
	{
		echo("      <tr class=\"head01 alLeft\">\n");
		echo("        <td>\n");
		echo("          ".RetornaFraseDaLista($lista_frases,7)." ".$linha['nome_grupo']."\n");
		echo("        </td>\n");
		echo("      </tr>\n");

         	$lista_alunos=RetornaUsuariosNoGrupo($sock, $cod_curso, $linha['cod_grupo']);
                if($lista_alunos!="")
		{
			foreach ($lista_alunos as $cod2 => $linha_alunos)
			{
                              if(!$SalvarEmArquivo){
                                $preSpan =  "<span class=\"link\" onclick='OpenWindowPerfil(".$linha_alunos['cod_usuario'].");'>";
                              }else{
                                $preSpan = "<span>";
                              }
                              echo("      <tr class=\"altColor".($cod2%2)." alLeft\">\n");
                              echo("        <td style=\"padding-left:50px;\">\n");
                              switch($linha_alunos['tipo_usuario']){
                                  case ('F'): //19 - Formador
                                    echo("     ".$preSpan.RetornaFraseDaLista($lista_frases,19)." ".$linha_alunos['nome']."</span>\n");
                                    break;
                                  case('A'):  //18 - Aluno
                                    echo("      ".$preSpan.RetornaFraseDaLista($lista_frases,18)." ".$linha_alunos['nome']."</span>\n");
                                    break;
                                  case('V'):
                                    echo("       ".$preSpan.$linha_alunos['nome']."</span>\n");
                                    break;
                                  case('F'):
                                    echo("       ".$preSpan.$linha_alunos['nome']."</span>\n");
                                    break;
                                }
                              echo("        </td>\n");
                              echo("      </tr>\n");
			}
                }
		else
		{
                        echo("      <tr class=\"altColor".($cod2%2)." alLeft\">\n");
                        echo("        <td style=\"padding-left:50px;\">\n");
                	echo("           ".RetornaFrasedaLista($lista_frases, 31));
                        echo("        </td>\n");
                        echo("      </tr>\n");
		}

        }
	echo("      </table>\n");
        echo("      </td>\n");
        echo("    </tr>\n");
	echo("    </table>\n");
        echo("      </td>\n");
        echo("    </tr>\n");
	echo("    </table>\n");
  	echo("  </body>\n");
  	echo("</html>\n");
  	Desconectar($sock);
?>