<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/cadastro_linguas.php

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
  ARQUIVO : administracao/cadastro_linguas.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");
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

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 11 - Cadastro de L�nguas */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,11)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("<!-- Tabelao -->\n");
  echo("<form name=linguas action=cadastrar_textos.php method=post>\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  echo("<td><ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("<li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("</ul></td></tr>\n");
  echo("<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  /* 72 - Selecione a lingua para cadastrar os textos: */
  echo("<tr class=\"head\"><td width=50%>".RetornaFraseDaLista($lista_frases,72)."</td>\n");

  /* 74 - Selecione a ferramenta: */ 
  echo("<td width=\"50%\">".RetornaFraseDaLista($lista_frases,74)."</td></tr>\n");

  $sock=Conectar("");
  $lista=ListaLinguas($sock);
  Desconectar($sock);

  if (count($lista)>0)
  {
    echo("<tr><td><select class=\"input\" name=\"cod_lingua\">\n");

    foreach($lista as $cod => $lingua)
    {
      echo(" <option value=".$cod." ".(($cod_lingua == $cod) ? "selected" : "").">".$lingua);
      if ($cod!=1)
        echo("*");
      echo("</option>\n");
    }

    echo("</select></td>\n");
  }
  else
    echo("<td>Nenhuma Lingua Cadastrada???</td></tr>");

  echo("<td><select class=\"input\" name=\"cod_ferramenta\">\n");

  /* 75 - Gen�ricas */ 
  echo("  <option value=-1 ".(($cod_ferramenta == -1) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,75)."</option>\n");
  /* 76 - Bibliotecas */ 
  echo("  <option value=-2 ".(($cod_ferramenta == -2) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,76)."</option>\n");
  /* 77 - P�gina Inicial */ 
  echo("  <option value=-3 ".(($cod_ferramenta == -3) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,77)."</option>\n");
  /* 78 - Ferramentas: Texto Descritivo */ 
  echo("  <option value=-4 ".(($cod_ferramenta == -4) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,78)."</option>\n");
  /* 79 - Administra��o Externa (Ambiente) */ 
  echo("  <option value=-5 ".(($cod_ferramenta == -5) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,79)."</option>\n");
  /* 136 - Nome das L�nguas */
  echo("  <option value=-6 ".(($cod_ferramenta == -6) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,136)."</option>\n");
  /* 140 - Configurar */
  echo("  <option value=-7 ".(($cod_ferramenta == -7) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,140)."</option>\n");
  /* 297 - Scripts */
  echo("  <option value=-8 ".(($cod_ferramenta == -8) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,297)."</option>\n");
 

  /* 80 - Administra��o Interna (Curso) */   
  echo("  <option value=0 ".(($cod_ferramenta == 0) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,80)."</option>\n");
  
  $lista_ferramentas=RetornaFerramentasOrdemMenu();

  if (count($lista_ferramentas)>0)
    foreach($lista_ferramentas as $cod => $nome)
      echo("  <option value=".$cod." ".(($cod_ferramenta == $cod) ? "selected" : "").">".$nome."</option>\n");

  /* 254 - Busca */
  echo("  <option value=30 ".(($cod_ferramenta == 30) ? "selected" : "").">".RetornaFraseDaLista($lista_frases,254)."</option>\n");


  /* 81 - Exibir n�o preenchidos */
  echo("  <option value=Completar ".(($cod_ferramenta == "Completar") ? "selected" : "").">".RetornaFraseDaLista($lista_frases,81)."</option>\n");

  echo("</select></td></tr>\n");

  echo("<tr><td><input type=\"radio\" name=\"opcao\" value=\"E\" checked />Editar</td><td><input type=\"radio\" name=\"opcao\" value=\"V\" />Visualizar</td></tr>\n");

  /* 73 - Ser� exibido o texto base (portugu�s), para preenchimento */
  echo("<tr><td colspan=2><font size=-3>* ".RetornaFraseDaLista($lista_frases,73)."</font></td></tr></table>\n");

  echo("<div align=right>\n");
  /* 11 - Continuar */
  echo("<input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,55)."\" onClick=\"document.linguas.submit();\" type=\"button\" />\n");
  echo("</div>\n");

  echo("</td></tr></table>\n");
  echo("</form>\n");
  echo("</td></tr>\n");
  include("../rodape_tela_inicial.php");
  echo("</body>\n");
  echo("</html>\n");
?>
