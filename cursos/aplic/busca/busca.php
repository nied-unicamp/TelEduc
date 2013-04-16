<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/busca/busca.php

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
  ARQUIVO : cursos/aplic/busca/busca.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("busca.inc");

  $cod_ferramenta=30;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  //para ser usado no acesso �s ferramentas
  $tipo_usuario=RetornaTipoUsuario($sock,$cod_curso,$cod_usuario);

  // para ser usado na listagem das ferramentas na busca
  $status_ferr=RetornaStatusFerramentas($sock);

  //para saber se est� com vis�o de formador ou aluno
  $formador=EFormador($sock,$cod_curso,$cod_usuario);


  /*
  ==================
  Funcoes JavaScript
  ==================
  */

  echo("    <script type=\"text/javascript\">\n");

  /* *********************************************************************
  Funcao TestaBusca - JavaScript. Testa de n�o h� campos a serem preenchidos.
    Entrada: nenhuma. Func��o espec�fica da p�gina.
    Saida:   Boolean, para controle do onClick;
             true, se nao houver erros no formulario,
             false, se houver.
  */

  echo("      function TestaBusca()\n");
  echo("      {\n");
  echo("        busca=document.dadosbusca.texto.value;\n");
  echo("        if (busca == '') \n");
  echo("        {\n");
  /* 2 - Por favor, digite o texto para fazer a Busca. */
  echo("          alert('".RetornaFraseDaLista($lista_frases,2)."');\n");
  echo("          document.dadosbusca.texto.focus();\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        var alguma_selecionada = false;\n");
  echo("        for ( var i = 0; i < document.dadosbusca.elements.length && !alguma_selecionada; i++)\n");
  echo("        {\n");
  echo("          var e = document.dadosbusca.elements[i];\n");
  echo("          if (e.name.substr(0,8) == 'cod_ferr' && e.checked == true)\n");
  echo("            alguma_selecionada = true;\n");
  echo("        }\n");
  echo("        if (!alguma_selecionada)\n");
  echo("        {\n");
  /* 3 - N�o h� nenhuma ferramenta selecionada ! */
  echo("          alert('".RetornaFraseDaLista($lista_frases,3)."');\n");
  echo("          return (false);\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n");


  echo("      function VerificaCheck()\n");
  echo("      {\n");
  echo("        var elem=document.getElementsByName('cod_ferr');\n");
  echo("        var nome_var_all=document.getElementById('cod_ferr_all');\n");
  echo("        var i=0;\n");
  echo("        var cont=0;\n");
  echo("        var tam= elem.length;\n");
  echo("        for (i=0; i < tam; i++)\n");
  echo("        {\n");
  echo("          if (elem[i].checked){\n");
  echo("            cont++;\n");
  echo("            if (cont==tam)\n");
  echo("              nome_var_all.checked=true;\n");
  echo("            else\n");
  echo("              nome_var_all.checked=false;\n");
  echo("          }\n");
  echo("        }\n");
  echo("      }\n");

  /* ******************************************************************
  Funcao Check All - Marca todas as checkboxes de ferramentas
    Entrada: nenhuma
    Saida: nenhuma
  */
  echo("      function CheckAll()\n");
  echo("      {\n");
  echo("        var elem=document.dadosbusca.elements;\n");
  echo("        var nome_var='cod_ferr';\n");
  echo("        var nome_var_all='cod_ferr_all';\n");
  echo("        var changed=false;\n");
  echo("        var i=0;\n");
  echo("        while (i < elem.length)\n");
  echo("        {\n");
  echo("          if (elem[i].name==nome_var_all)\n");
  echo("            changed=elem[i].checked;\n");
  echo("          else if (elem[i].name.substr(0,8)==nome_var)\n");
  echo("            elem[i].checked=changed;\n");
  echo("          i++;\n");
  echo("        }\n");
  echo("      }\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        CheckAll();");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n");

  /*
  ==================
  Programa Principal
  ==================
  */

  include("../menu_principal.php");

  Desconectar($sock);

  $sock=Conectar("");

  $ferramentas=RetornaFerramentasOrdemMenu($sock);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 1 - Busca */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("            <a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("            <a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("          </div>\n");
  echo("          <form name=\"dadosbusca\" method=\"post\" action=\"busca2.php?cod_curso=".$cod_curso."\" onsubmit=\"return(TestaBusca());\">\n");
  /* <!----------------- Tabelao -----------------> */
  echo("            <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\"class=\"tabExterna\">\n");
  echo("              <tr>\n");
  echo("                <td valign=\"top\">\n");
  /* <!----------------- Tabela Interna -----------------> */
  echo("                  <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  if (count($ferramentas)>0)
  {
                                          /* 4 - Selecione a(s) ferramenta(s) para fazer a Busca: */
    echo("                    <tr class=\"head\"><td colspan=\"8\">".RetornaFraseDaLista($lista_frases,4)."</td></tr>\n");

    echo("                    <tr class=\"head01\">\n");
    echo("                      <td align=\"center\" width=\"5%\"><input type=\"checkbox\" name=\"cod_ferr_all\" id=\"cod_ferr_all\" value=\"1\" onclick=\"CheckAll();\" /></td>\n");
                                                         /* 5 - Selecionar Todas */
    echo("                      <td align=\"left\" colspan=\"8\"><b>".RetornaFraseDaLista($lista_frases,5)."</b></td>\n");
    echo("                    </tr>\n");

    $cont=0;
    $num_ferr=0;

    foreach ($ferramentas as $cod => $nome)
    {
      // n�o h� busca nas ferramentas Estrutura do Ambiente, Perfil, Grupos, Acessos, Intermap, Configurar , Adm.Interna e na própria Busca */
      // al�m disso, se a ferramenta est� oculta (status='D') tambem n�o pode haver busca nela
      if ($cod!=12 && $cod!=13 && $cod!=17 && $cod!=18 && $cod!=19 && $cod!=30 && $status_ferr[$cod]!='D')
      {
        if ($formador || ($status_ferr[$cod]!='F'))
        {
          if (($cont%4)==0)
            echo("                    <tr>\n");
          $cont++;
          if ($cod==@$cod_ferr[$num_ferr])
          {
            echo("                      <td width=\"5%\" align=\"center\"><input type=\"checkbox\" checked name=\"cod_ferr\" value=\"".$cod."\" onclick=\"VerificaCheck();\" /></td><td class=\"alLeft\" width=\"20%\" >".$nome."</td>\n");
            $num_ferr++;
          }else{
            echo("                      <td width=\"5%\" align=\"center\"><input type=\"checkbox\" name=\"cod_ferr[".$cod."]\" value=\"".$cod."\" onclick=\"VerificaCheck();\" /></td><td class=\"alLeft\" width=\"20%\" >".$nome."</td>\n");
          }
          if (($cont%4)==0)
            echo("                    </tr>\n");
        }
      }
    }
  }

  echo("                    <tr><td colspan=\"8\">&nbsp;</td></tr>\n");

  /* 6 - Digite o texto para fazer a Busca: */
  echo("                    <tr>\n");
  echo("                      <td colspan=\"2\" align=\"right\">".RetornaFraseDaLista($lista_frases,6)."</td>\n");
  echo("                      <td colspan=\"6\" align=\"left\"><input class=\"input\" type=\"text\" size=\"60\" style=\"maxlenght: 100\" name=\"texto\" /></td>\n");
  echo("                    </tr>\n");
  /* <!----------------- Fim Tabela Interna -----------------> */
  echo("                  </table>\n");
  echo("                  <div align=\"right\">\n");
  /* 7 - Buscar */
  echo("                    <input class=\"input\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases,7)."\" />\n");
  echo("                  </div>\n");

  /* <!----------------- Fim Tabel�o -----------------> */
  echo("                </td>\n");
  echo("              </tr>\n");
  echo("            </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>