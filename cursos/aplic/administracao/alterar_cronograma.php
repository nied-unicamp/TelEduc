<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/alterar_cronograma.php

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
  ARQUIVO : cursos/aplic/administracao/alterar_cronograma.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda = 3;

  include("../topo_tela.php");

  GeraJSComparacaoDatas();
  GeraJSVerificacaoData();
  /*Funcao JavaScript*/
 
  echo("    <script type=\"text/javascript\">\n");
  // startList()
  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  if($ecoordenador = ECoordenador($sock, $cod_curso, $cod_usuario)){
  echo("      function verifica_intervalos() \n");
  echo("      {\n");
  echo("        i_ini=document.form.inscricao_inicio;\n");
  echo("        i_fim=document.form.inscricao_fim;\n");
  echo("        c_ini=document.form.curso_inicio;\n");
  echo("        c_fim=document.form.curso_fim;\n");
  echo("        if (!DataValidaAux(i_ini) || !DataValidaAux(i_fim) || !DataValidaAux(c_ini) || !DataValidaAux(c_fim))\n");
  echo("          return false;\n");
  echo("        if (ComparaData(i_ini,i_fim) > 0 ) // (i_ini>i_fim) \n");
  echo("        {\n");
  /* 8 - A data inicial do per�odo de inscri��o deve ser anterior � data final desse per�odo. */
  echo("          alert('".RetornaFraseDaLista($lista_frases,8)."');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        if (ComparaData(c_ini,c_fim) > 0) // (c_ini>c_fim)\n");
  echo("        {\n");
  /* 9 - A data inicial do curso deve ser anterior � sua data final. */
  echo("          alert('".RetornaFraseDaLista($lista_frases,9)."');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        if (ComparaData(i_ini,c_fim) > 0) // (i_ini>c_fim) \n");
  echo("        {\n");
  /* 10 - A data inicial do per�odo de inscri��o deve ser anterior � data final do curso. */
  echo("          alert('".RetornaFraseDaLista($lista_frases,10)."');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        if (ComparaData(i_fim,c_fim) > 0) // (i_fim>c_fim)\n");
  echo("        {\n");
  /* 11 - A data final do per�odo de inscri��o deve ser anterior � data final do curso. */
  echo("          alert('".RetornaFraseDaLista($lista_frases,11)."');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        if (ComparaData(i_ini,c_ini) > 0) // (i_ini>c_ini) \n");
  echo("        {\n");
//   /* 12 - A data inicial do per�odo de inscri��o deve ser anterior � data inicial do curso. */
  echo("          alert('".RetornaFraseDaLista($lista_frases,12)."');\n");
  echo("          return(false);\n");
  echo("        }\n");
  echo("        return(true);\n");
  echo("      }\n");
  }
  echo("    </script>\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
    /* 1 - Administracao  28 - Area restrita ao formador. */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,28)."</h4>\n");

    /*Voltar*/
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("          <form><input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");

    echo("        </td>\n");
    echo("      </tr>\n");

    include("../tela2.php");

    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }

/*Forms*/
  echo("    <form name=\"form\" action=\"acoes.php\" method=\"post\" onSubmit=\"return(verifica_intervalos());\">\n");
  echo("     <input type=\"hidden\" name=\"cod_curso\" value=".$cod_curso.">\n");
  echo("     <input type=\"hidden\" name=\"cod_ferramenta\" value=".$cod_ferramenta.">\n");  
  echo("     <input type=\"hidden\" name=\"action\" value='alterarCronograma'>\n");

  // P�gina Principal
  /* 1 - Administra��o */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1)."\n");
  /* 31 - Visualizar / Alterar Dados do Curso */
  $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 31)."</h4>";
  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("        <div id=\"mudarFonte\">\n");
  echo("          <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("          <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("          <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("        </div>\n");

  /*Voltar*/
  /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
   
  $linha=RetornaDadosCursoAdm($sock,$cod_curso);

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (geral)*/
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;confirma=0\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\"  class=\"tabInterna\">\n");
  echo("                  <tr class=\"head alLeft\">\n");
  /* 32 - Abaixo seguem datas referentes a per�odos do curso, as quais podem ser alteradas. */
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,32)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  /* 34 - In�cio */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,34)."</b></td>\n");
  /* 34 - Item */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,35)."</b></td>\n");
  /* 36 - Fim */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,36)."</b></td>\n");
  echo("                  </tr>\n");
  echo("                  <tr align=\"center\">\n");
  echo("                    <td>");
  echo("                      <ul>\n");
  echo("                        <li>\n");
  echo("                          <div>\n");
  if($ecoordenador){
  	echo("                            <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" id=\"inscricao_inicio\" name=\"inscricao_inicio\" value=\"".UnixTime2Data($linha['inscricao_inicio'])."\" />\n");
  	echo("                               <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('inscricao_inicio'),'dd/mm/yyyy',this);\" />\n");
  } else {
  	echo(UnixTime2Data($linha['inscricao_inicio']));
  }
  echo("                          </div>\n");
  echo("                        </li>\n");
  echo("                      </ul>\n");
  echo("                    </td>\n");
  /* 37 - Inscri��es */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,37)."</td>\n");
  echo("                    <td>");
  echo("                      <ul>\n");
  echo("                        <li>\n");
  echo("                          <div>\n");
  if($ecoordenador){
  echo("                            <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" id=\"inscricao_fim\" name=\"inscricao_fim\" value=\"".UnixTime2Data($linha['inscricao_fim'])."\" />\n");
  echo("                               <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('inscricao_fim'),'dd/mm/yyyy',this);\" />\n");
  } else {
  	echo(UnixTime2Data($linha['inscricao_fim']));
  }
  echo("                          </div>\n");
  echo("                        </li>\n");
  echo("                      </ul>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                  <tr align=\"center\">\n");
  echo("                    <td>");
  echo("                      <ul>\n");
  echo("                        <li>\n");
  echo("                          <div>\n");
  if($ecoordenador){
  echo("                            <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" id=\"curso_inicio\" name=\"curso_inicio\" value=\"".UnixTime2Data($linha['curso_inicio'])."\" />\n");
  echo("                               <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('curso_inicio'),'dd/mm/yyyy',this);\" />\n");
  } else {
  	echo(UnixTime2Data($linha['curso_inicio']));
  }
  echo("                          </div>\n");
  echo("                        </li>\n");
  echo("                      </ul>\n");
  echo("                    </td>\n");
  /* 38 - Curso */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,38)."</td>\n");
  echo("                    <td>");
  echo("                      <ul>\n");
  echo("                        <li>\n");
  echo("                          <div>\n");
  if($ecoordenador){
  echo("                            <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" id=\"curso_fim\" name=\"curso_fim\" value=\"".UnixTime2Data($linha['curso_fim'])."\" />\n");
  echo("                               <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('curso_fim'),'dd/mm/yyyy',this);\" />\n");
  } else {
  	echo(UnixTime2Data($linha['curso_fim']));
  }
  echo("                          </div>\n");
  echo("                        </li>\n");
  echo("                      </ul>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");

  /* 23 - Obs: */
  /* 24 - As datas devem estar no formato DD/MM/AAAA. */
  echo("                <b>".RetornaFraseDaLista($lista_frases,23)."</b> ".RetornaFraseDaLista($lista_frases,24)."\n");

  /* 23 - Obs: */
  /* 229 - Caso a data de inicio do curso seja maior que a data de fim das inscri��es, o curso somente poder� ser acessado diretamente pelo link recebido no e-mail do coordenador. Ou seja, ele n�o ser� listado em nenhuma das se��es 'Cursos em andamento', 'Cursos com inscri��es abertas' ou 'Cursos j� oferecidos'. */
  echo("                <br><br><b>".RetornaFraseDaLista($lista_frases,23)."</b> ".RetornaFraseDaLista($lista_frases,229)."<br><br>\n");
  
  /* 24 - Alterar (geral) */
  if($ecoordenador) echo("                <div align=right><input type=\"submit\" class=\"input\" value='".RetornaFraseDaLista($lista_frases_geral,24)."'></div>\n");

  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);

?>
