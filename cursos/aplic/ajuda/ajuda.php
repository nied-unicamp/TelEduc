<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/ajuda/ajuda.php

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
  ARQUIVO : cursos/aplic/ajuda/ajuda.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("ajuda.inc");


  $sock=Conectar("");
  $lista_frases=RetornaListaDeFrases($sock,13);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  Desconectar($sock);
 
  $sock=Conectar("");

  $lista_nome_ferramentas=RetornaFerramentasOrdemMenu($sock);
  if ($cod_ferramenta==-1)
    $nome_ferramenta="Configurar";
  else if ($cod_ferramenta==0)
    $nome_ferramenta="Administra��o";
  else
    $nome_ferramenta=$lista_nome_ferramentas[$cod_ferramenta];

  $lista_ferramentas=RetornaListaFerramentas($sock);
  if ($cod_ferramenta==-1)
    $diretorio="configurar";
  else if ($cod_ferramenta==0)
    $diretorio="administracao";
  else
    $diretorio=$lista_ferramentas[$cod_ferramenta]['diretorio'];

  if ($cod_ferramenta==3)
    $nome_css="atividades";
  else if ($cod_ferramenta==4)
    $nome_css="apoio";
  else if ($cod_ferramenta==5)
    $nome_css="leituras";
  else if ($cod_ferramenta==7)
    $nome_css="obrigatoria";
  else
    $nome_css=$diretorio;

//   global $cod_lingua_s;

  include("../topo_tela.php");
  Desconectar($sock);

  $sock=Conectar("");

  /*
  ==================
  Funcoes JavaScript
  ==================
  */

  echo("  <script type=\"text/javascript\">\n");

  /* *********************************************************************
  Funcao AlteraSalvar - JavaScript.  Altera o formul�rio para submiss�o � p�gina seguinte.
    Entrada: nome_form = nome do formul�rio que ser� alterado
    Saida:   Nenhuma.
  */

  echo("    function AlteraSalvar(nome_form)\n");
  echo("    {\n");
  echo("      nome_form.action=\"salvar_arquivo.php?cod_curso=".$cod_curso."\";\n");
  echo("      nome_form.submit();\n");
  echo("    }\n");

  /* *********************************************************************
  Funcao AlteraAjuda - JavaScript.  Altera o formul�rio para submiss�o � p�gina seguinte.
    Entrada: nome_form = nome do formul�rio que ser� alterado
    Saida:   Nenhuma.
  */

  echo("    function AlteraAjuda(nome_form)\n");
  echo("    {\n");
  echo("      nome_form.action=\"ajuda.php?cod_curso=\"+nome_form.cod_curso.value;\n");
  echo("      nome_form.submit();\n");
  echo("    }\n");

  /* *********************************************************************
  Funcao VerToda - JavaScript.  Altera o valor de um input hidden.
    Entrada: nome_form = nome do formul�rio que contem o input que ser� alterado
    Saida:   Nenhuma.
  */

  echo("    function VerToda(nome_form)\n");
  echo("    {\n");
  echo("      nome_form.ver_toda.value=\"1\";\n");
  echo("    }\n");

  echo("  </script>\n");


  if (!isset($SalvarEmArquivo))
  {
    echo("    <script type=\"text/javascript\">\n");

    /* *********************************************************************
    Funcao ImprimirRelatorio - JavaScript. Imprime a p�gina da Ajuda
      Entrada: Nenhuma.
      Saida:   Nenhuma.
    */

    echo("      function ImprimirRelatorio()\n");
    echo("      {\n");
    echo("        if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape')\n");
    echo("        {\n");
    echo("          self.print();\n");
    echo("        }\n");
    echo("        else\n");
    echo("        {\n");
    /* 51- Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
    echo("          alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
    echo("        }\n");
    echo("      }\n");
    echo("      window.resizeTo(600,400);\n");
    echo("    </script>\n");


  }else{
    echo("    <style>\n");
    include "../js-css/ambiente.css";
    include "../js-css/tabelas.css";
    include "../js-css/navegacao.css";
    echo("    </style>\n");
  }

  echo("  </head>\n");


  echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" onload=\"this.focus();\">\n");
  
  $titulo=strtoupper($nome_ferramenta);

  echo("    <br /><br /><h4>".$titulo);

  if ($tipo_usuario=='F')
    /* 58 - Formador */
    echo(" - ".RetornaFraseDaLista($lista_frases_geral,58)."&nbsp;");
  else if ($tipo_usuario=='A')
    /* ?? - Aluno */
    echo(" - Aluno&nbsp;");

  echo ("</h4><br />\n");


  echo("    <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("      <tr>\n");
  echo("        <td valign=\"top\" colspan=\"3\">\n");
  
  if (!isset($SalvarEmArquivo))
  {
    echo("          <form name=\"ajuda1\" method=\"post\" action=\"\">\n");
    echo("            <ul class=\"btAuxTabs\">\n");
    if (!isset($ver_toda) || $ver_toda!=1)
      /* ?? - Ver toda a Ajuda */
      echo("              <li><span onclick=\"VerToda(document.ajuda1);AlteraAjuda(document.ajuda1);\">Ver toda a Ajuda</span></li>\n");

    /* G 50 - Salvar Em Arquivo */
    echo("              <li><span onclick=\"AlteraSalvar(document.ajuda1);\">".RetornaFraseDaLista($lista_frases_geral,50)."</span></li>\n");

    /* G 14 - Imprimir */
    echo("              <li><span onclick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");

    /* G 13 - Fechar */
    echo("              <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
    
    echo("            </ul>\n");

    echo("              <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");

    if (isset($ver_toda) && $ver_toda==1)
      echo("              <input type=\"hidden\" name=\"nome_arquivo\" value=\"".$nome_ferramenta.".html\" />\n");
    else
    {
      $nome_provisorio=RetornaNomePagina($sock,$cod_pagina,$cod_ferramenta,$_SESSION['cod_lingua_s'],$tipo_usuario);
      echo("              <input type=\"hidden\" name=\"nome_arquivo\" value=\"".$nome_ferramenta." - ".$nome_provisorio.".html\" />\n");
    }

    echo("              <input type=\"hidden\" name=\"cod_pagina\" value=\"".$cod_pagina."\" />\n");
    echo("              <input type=\"hidden\" name=\"cod_ferramenta\" value=\"".$cod_ferramenta."\" />\n");
    echo("              <input type=\"hidden\" name=\"tipo_usuario\" value=\"".$tipo_usuario."\" />\n");

    echo("              <input type=\"hidden\" name=\"ver_toda\" value=\"".$ver_toda."\" />\n");

    
    echo("          </form>\n");
  }

  
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td>\n");


  if (!isset($ver_toda) || $ver_toda!=1)
    $paginas[0]['cod_pagina']=$cod_pagina;
  else
    $paginas=RetornaPaginasFerramenta($sock,$cod_ferramenta,$_SESSION['cod_lingua_s'],$tipo_usuario);

  foreach ($paginas as $cod => $cod_p)
  {
    if (!isset($ver_toda) || $ver_toda!=1)
      $nome_pagina=RetornaNomePagina($sock,$cod_pagina,$cod_ferramenta,$_SESSION['cod_lingua_s'],$tipo_usuario);
    else
      $nome_pagina=RetornaNomePagina($sock,$cod_p,$cod_ferramenta,$_SESSION['cod_lingua_s'],$tipo_usuario);

    echo("          <table cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"white\" style=\"padding:8px 8px 8px 8px\">\n");
    echo("            <tr>\n");
    echo("              <td><b>".$nome_ferramenta."</b>\n");



    if ($nome_pagina!='')
      echo("                <b> - ".$nome_pagina."</b>\n");



    if (!isset($ver_toda) || $ver_toda!=1)
      $texto=RetornaTextoDaAjuda($sock,$cod_ferramenta,$cod_pagina,$_SESSION['cod_lingua_s'],$tipo_usuario);
    else
      $texto=RetornaTextoDaAjuda($sock,$cod_ferramenta,$cod_p,$_SESSION['cod_lingua_s'],$tipo_usuario);

    echo($texto."\n");


    echo ("<br /><br />\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
  }


  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  
  echo("        <td valign=\"bottom\" colspan=\"3\">\n");
  if (!isset($SalvarEmArquivo))
  {
    echo("          <form name=\"ajuda2\" method=\"post\" action=\"\">\n");
    echo("            ".RetornaSessionIDInput());
    echo("            <ul class=\"btAuxTabs\">\n");
    if (!isset($ver_toda) || $ver_toda!=1)
      /* ?? - Ver toda a Ajuda */
      echo("              <li><span onclick=\"VerToda(document.ajuda2);AlteraAjuda(document.ajuda2);\">Ver toda a Ajuda</span></li>\n");

    /* G 50 - Salvar Em Arquivo */
    echo("              <li><span onclick=\"AlteraSalvar(document.ajuda2);\">".RetornaFraseDaLista($lista_frases_geral,50)."</span></li>\n");
    
    /* G 14 - Imprimir */
    echo("              <li><span onclick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");
    
    /* G 13 - Fechar */
    echo("              <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");

    echo("            </ul>\n");
    
    echo("              <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");

   if ($nome_pagina=='' || (isset($ver_toda) && $ver_toda==1))
     echo("              <input type=\"hidden\" name=\"nome_arquivo\" value=\"".$nome_ferramenta.".html\" />\n");
   else
    {
      $nome_provisorio=RetornaNomePagina($sock,$cod_pagina,$cod_ferramenta,$_SESSION['cod_lingua_s'],$tipo_usuario);
      echo("             <input type=\"hidden\" name=\"nome_arquivo\" value=\"".$nome_ferramenta." - ".$nome_provisorio.".html\" />\n");
    }

    echo("              <input type=\"hidden\" name=\"cod_pagina\" value=\"".$cod_pagina."\" />\n");
    echo("              <input type=\"hidden\" name=\"cod_ferramenta\" value=\"".$cod_ferramenta."\" />\n");
    echo("              <input type=\"hidden\" name=\"tipo_usuario\" value=\"".$tipo_usuario."\" />\n");

    echo("              <input type=\"hidden\" name=\"ver_toda\" value=\"".$ver_toda."\" />\n");

    echo("          </form>\n");
  }
  

  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  
  /* tela2 */
  include("../tela2.php");
  
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>