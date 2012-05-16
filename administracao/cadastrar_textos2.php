<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/cadastrar_textos2.php

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
  ARQUIVO : administracao/cadastrar_textos2.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("    <script type=\"text/javascript\">\n");
  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("      }\n");
  echo("    </script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
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

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr>\n");
  echo("                    <td>\n");

  if (!isset($cod_ferramenta))
    $cod_ferramenta=$cod_ferr;

  if ($acao=="I")
  {
    $num=InsereTexto($cod_lin,$cod_ferramenta,$texto);
    /* 87 - Frase */
    /* 86 - inserida com c�digo */
    echo(RetornaFraseDaLista($lista_frases,87)." ".ConverteAspas2Html($texto)." ".RetornaFraseDaLista($lista_frases,86)." ".$num."<br>\n");
  }

  if ($acao=="A")
  {
    $num=AlteraTexto($cod_lin,$cod_ferramenta,$cod_texto,$texto);
    /* 88 - Frase de c�digo */
    /* 89 - alterada para  */
    echo(RetornaFraseDaLista($lista_frases,88)." ".$num." ".RetornaFraseDaLista($lista_frases,89)." '".ConverteAspas2Html($texto)."'<br>\n");
  }

  if ($acao=="X")
  {
    $num=ApagaTexto($cod_ferramenta,$cod_texto);
    /* 88 - Frase de c�digo */
    /* 90 - apagada  */
    echo(RetornaFraseDaLista($lista_frases,88)." ".$num." ".RetornaFraseDaLista($lista_frases,90).".<br>\n");
  }

  if ($acao=="L")
  {
    if (ExisteTexto($cod_lin,$cod_ferramenta,$cod_texto))
      $num=AlteraTexto($cod_lin,$cod_ferramenta,$cod_texto,$texto);
    else 
      $num=InsereTextoComCodigo($cod_lin,$cod_ferramenta,$cod_texto,$texto);
    /* 88 - Frase de c�digo */
    /* 89 - alterada para  */
    echo(RetornaFraseDaLista($lista_frases,88)." ".$num." ".RetornaFraseDaLista($lista_frases,89)." '".ConverteAspas2Html($texto)."'<br>\n");
  }

  if ($acao=="LD")
  {
    if (count($texto)>0)
      foreach($texto as $cod_ferr => $linha)
        if (count($linha)>0)
          foreach($linha as $cod_tex => $tex)
          {
            if (ExisteTexto($cod_lin,$cod_ferr,$cod_tex))
              $num=AlteraTexto($cod_lin,$cod_ferr,$cod_tex,$tex);
            else
              $num=InsereTextoComCodigo($cod_lin,$cod_ferr,$cod_tex,$tex);
          }
  }

  if ($acao=="AD")
  {
    if (count($texto)>0)
      foreach($texto as $cod_ferr => $linha)
        if (count($linha)>0)
          foreach($linha as $cod_tex => $tex)
          {
            if (ExisteTexto($cod_lin,$cod_ferr,$cod_tex))
              $num=AlteraTexto($cod_lin,$cod_ferr,$cod_tex,$tex);
            else
              $num=InsereTextoComCodigo($cod_lin,$cod_ferr,$cod_tex,$tex);
          }
  }

  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>