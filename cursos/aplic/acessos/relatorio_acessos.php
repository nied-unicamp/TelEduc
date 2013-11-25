<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/acessos/acessos.php

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

/* Código principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("acessos.inc");

  $cod_ferramenta = 18;
  include("../topo_tela.php");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario, true);

  echo("    <script type=\"text/javascript\" language=\"javascript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("        self.focus();\n");
  echo("      }\n\n");

  echo("    </script>\n");

  if (!$SalvarEmArquivo)
  {
    echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
    echo("    <script type=\"text/javascript\">\n");
    echo("      function AbrePerfil(cod_usuario)\n");
    echo("      {\n");
    echo("         window.open('../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("      }\n\n");

    echo("      function ImprimirRelatorio()\n");
    echo("      {\n");
    echo("        if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape')\n");
    echo("        {\n");
    echo("          self.print();\n");
    echo("        }\n");
    echo("        else\n");
    echo("        {\n");
    /* 51- Infelizmente n� foi poss�el imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
    echo("          alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
    echo("        }\n");
    echo("      }\n\n");
    echo("    </script>\n");
  }
  else
  {
    echo("    <style>\n");
    include "../js-css/ambiente.css";
    include "../js-css/tabelas.css";
    include "../js-css/navegacao.css";
    echo("    </style>\n");
  }

  echo("  </head>\n");
  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
  echo("    <a name=\"topo\"></a>\n");

  /* 1 - Acessos */
  $cabecalho ="<h4>".RetornaFraseDaLista($lista_frases,1);
  /* 53 - Exibir Relat�io de Acessos */
  $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,53)."</h4>";
  echo("    <br /><br />".$cabecalho."\n");
  echo("    <br />\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  list($lista_acessos, $max_qtde_acessos) = RetornaUltimoENumeroAcessos($sock,'');

  if ($radio_ord == "cidade")
    $ordenacao = " cidade";
  else if ($radio_ord == "local")
    $ordenacao = " local_trab";
  else if ($radio_ord == "estado")
    $ordenacao = " estado";
  else
    /* por default, ordenacao por nome */
    $ordenacao = " nome";

  Desconectar($sock);
  $sock = Conectar("");
  $lista_users = RetornaUsuarios($sock,$ordenacao, $cod_curso);
  if (count($lista_users) > 0)
  {
    echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("      <tr>\n");
    echo("        <td valign=\"top\">\n");

    if (!$SalvarEmArquivo)
    {
      echo("          <form action=\"salvar_arquivo.php\" method=\"get\" name=\"formSalvar\">\n");
      echo("            <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
      echo("            <input type=\"hidden\" name=\"nome_arquivo\" value=\"relatorio_acessos.html\" />\n");
      echo("            <input type=\"hidden\" name=\"origem\" value=\"acessos\" />\n");
      if (isset($check_ultimos))
        echo("            <input type=\"hidden\" name=\"check_ultimos\" value=\"1\" />\n");
      if (isset($check_qtde))
        echo("            <input type=\"hidden\" name=\"check_qtde\"    value=\"1\" />\n");
      if (isset($check_local))
        echo("            <input type=\"hidden\" name=\"check_local\"   value=\"1\" />\n");
      if (isset($check_cidade))
        echo("            <input type=\"hidden\" name=\"check_cidade\"  value=\"1\" />\n");
      if (isset($check_estado))
        echo("            <input type=\"hidden\" name=\"check_estado\"  value=\"1\" />\n");
      if (isset($radio_ord))
        echo("            <input type=\"hidden\" name=\"radio_ord\" value=\"".$radio_ord."\" />\n");

      echo("          </form>\n");

      echo("          <ul class=\"btAuxTabs\">\n");
      /* 22 - Salvar Em Arquivo */
      echo("            <li><span onClick=\"document.formSalvar.submit();\">".RetornaFraseDaLista($lista_frases,22)."</span></li>");
      /* G 14 - Imprimir */
      echo("            <li><span onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");
      /* G 13 - Fechar */
      echo("            <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>");

    }
    else
    {
      echo("          <ul class=\"btAuxTabs\">\n");
      /* G 13 - Fechar */
      echo("            <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
    }
    echo("          </ul>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr>\n");
    echo("        <td>\n");
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("            <tr class=\"head\">\n");
    /* 52 - Usuário */
    echo("              <td>".RetornaFraseDaLista($lista_frases,52)."</td>\n");

    $coluna2 = ((isset($check_local)  && $check_local)  ||
                (isset($check_cidade) && $check_cidade) ||
                (isset($check_estado) && $check_estado));

    $coluna3 = ((isset($check_ultimos) && $check_ultimos) || 
                (isset($check_qtde)    && $check_qtde));

    /* numero de colunas na tabela */
    $num_colunas = 1;

    if ($coluna2){
      echo("              <td></td>\n");
      $num_colunas++;
    }

    if ($coluna3){
      echo("              <td></td>\n");
      $num_colunas++;
    }
    echo("            </tr>\n");

    $local_trab_tmp = "";
    $cidade_tmp = "";
    $estado_tmp = "";
    $nome_tmp = "";
    $cod_grupo_tmp = -1;

    foreach ($lista_users as $cod => $linha)
    {
      if ($radio_ord == "local" && $linha['local_trab'] != $local_trab_tmp)
      {
        $local_trab_tmp = $linha['local_trab'];
      }
      if ($radio_ord == "cidade" && $linha['cidade'] != $cidade_tmp)
      {
        $cidade_tmp = $linha['cidade'];
      }
      if ($radio_ord == "nome")
      {
        if ($nome_tmp == "")
        {
          $nome_tmp = $linha['nome'];
        }
      }
      if ($radio_ord == "estado" && $linha['estado'] != $estado_tmp)
      {
        $estado_tmp = $linha['estado'];
      }


      echo("            <tr>\n");

      /* nome do usuario*/
      echo("              <td>");
      if (!$SalvarEmArquivo)
      {
        $link_abre="<span class=\"link\" onClick=\"AbrePerfil(".$cod.");\">";
        $link_fecha="</span>";
       }
      else
      {
        $link_abre="";
        $link_fecha="";
      }

      echo($link_abre.$linha['nome'].$link_fecha);
      echo("</td>\n");

      if ($coluna2){

        echo("              <td class=\"alLeft\">\n");
        echo("                <ul>\n");

        if ($check_local)
          echo("                  <li><b>".RetornaFraseDaLista($lista_frases,9).":</b> ".$linha['local_trab']."</li>\n");
        if ($check_cidade)
          echo("                  <li><b>".RetornaFraseDaLista($lista_frases,10).":</b> ".$linha['cidade']."</li>\n");
        if ($check_estado)
          echo("                  <li><b>".RetornaFraseDaLista($lista_frases,11).":</b> ".$linha['estado']."</li>\n");
        echo("                </ul>\n");
        echo("              </td>\n");
      }

      if ($coluna3){
        echo("              <td class=\"alLeft\">\n");
        echo("                <ul>\n");
        /* ultimo acesso do usuario */
        if($check_ultimos)
        {

          if (isset($lista_acessos[$cod]))
          {
            echo("                  <li><b>".RetornaFraseDaLista($lista_frases,19).":</b> ".UnixTime2DataHora($lista_acessos[$cod]['ultimo_acesso'])."</li>\n");
          }
          else
          {
            /* 21 - Nenhum acesso */
            echo("                  <li><i>".RetornaFraseDaLista($lista_frases,21)."</i></li>\n");
          }
        }

        /* numero de acessos do usuario*/
        if (isset($check_qtde) && $check_qtde)
        {
          $qtde_acessos = (isset($lista_acessos[$cod]) ? $lista_acessos[$cod]['num_acessos'] : 0);
          if ($max_qtde_acessos>0)
          {
            $tamanho_barra= (int)($qtde_acessos * 160 / $max_qtde_acessos);
          }
          /* 20 - Quantidade de acessos */
          echo("                  <li><b>".RetornaFraseDaLista($lista_frases,20).":</b> ".$qtde_acessos."</li>\n");

          if ($qtde_acessos>0)
            echo("                  <li style=\"line-height:3px; height:3px; border:1pt solid; width:".$max_qtde_acessos."; border-left:".$tamanho_barra."pt solid #C32621;\">&nbsp;</li>\n");

        }
        echo("                </ul>\n");
        echo("              </td>\n");
      }
      echo("            </tr>\n");
    }

    echo("          </table>\n");
  }
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");

  Desconectar($sock);

  echo("  </body>\n");
  echo("</html>\n");


?>
