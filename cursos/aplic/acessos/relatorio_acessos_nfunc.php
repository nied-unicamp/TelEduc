<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/acessos/relatorio_acessos_nfunc.php

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
  ARQUIVO : cursos/aplic/acessos/relatorio_acessos_nfunc.php
  ========================================================== */

/* C�igo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("acessos.inc");

  $cod_ferramenta = 18;
  include("../topo_tela.php");

  $lista_acessos = RetornaUltimoENumeroAcessos($sock,$cod_ferramenta);

  /* por default, ordenacao por nome */
  $ordenacao = " nome";

  if(isset($radio_ord))
  {
    if ($radio_ord == "cidade")
      $ordenacao = " cidade";
    else if ($radio_ord == "local")
      $ordenacao = " local_trab";
    else if ($radio_ord == "estado")
      $ordenacao = " estado";
  }

  $lista_users = RetornaUsuarios($sock,$ordenacao,$cod_curso);

  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  if (!$SalvarEmArquivo)
  {
    echo("      function AbrePerfil(cod_usuario)\n");
    echo("      {\n");
    echo("         window.open('../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("      }\n");

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
    echo("      }\n");

    echo("      window.resizeTo(600,400);\n");
  }

  echo("    </script>\n");

  echo("  </head>\n");
  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
  echo("    <a name=\"topo\"></a>\n");

  /* 1 - Fóruns de Discussão */
  echo("    <br /><br /><h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");
  echo("    <br />\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  if (!$SalvarEmArquivo)
  {
    echo("                <form action=\"salvar_arquivo.php\" method=\"get\" name=\"formSalvar\">\n");
    echo("                  <input type=hidden name=cod_curso value=".$cod_curso." />\n");
    echo("                  <input type=hidden name=nome_arquivo value='relatorio_acessos.html' />\n");
    echo("                  <input type=hidden name=origem value='acessos' />\n");
    if (isset($check_ultimos))
      echo("                  <input type=hidden name=check_ultimos value=1 />\n");
    if (isset($check_qtde))
      echo("                  <input type=hidden name=check_qtde value=1 />\n");
    if (isset($check_local))
      echo("                  <input type=hidden name=check_local value=1 />\n");
    if (isset($check_cidade))
      echo("                  <input type=hidden name=check_cidade value=1 />\n");
    if (isset($check_estado))
      echo("                  <input type=hidden name=check_estado value=1 />\n");
    if (isset($radio_ord))
      echo("                  <input type=hidden name=radio_ord value=".$radio_ord." />\n");
    echo("                </form>\n");
    echo("                <ul class=\"btAuxTabs\">\n");
    /* 22 - Salvar Em Arquivo */
    echo("                  <li><span onClick=\"document.formSalvar.submit();\">".RetornaFraseDaLista($lista_frases,22)."</span></li>\n");
    /* G 14 - Imprimir */
    echo("                  <li><span onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");
    /* G 13 - Fechar */
    echo("                  <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  }
  else
  {
    echo("                <ul class=\"btAuxTabs\">\n");
    /* G 13 - Fechar */
    echo("                  <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  }

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 52 - Usuário */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,52)."</td>\n");
  /* numero de colunas na tabela */
  $num_colunas = 1;
  if (isset($check_ultimos) && $check_ultimos)
  {
    /* 19 - Último acesso */
    echo("                    <td>".RetornaFraseDaLista($lista_frases,19)."</td>\n");
    $num_colunas++;
  }
  if (isset($check_qtde) && $check_qtde)
  {
    /* 20 - Quantidade de acessos */
    echo("                    <td>".RetornaFraseDaLista($lista_frases,20)."</td>\n");
    $num_colunas++;
    $max_qtde_acessos=0;
    foreach ($lista_users as $cod => $linha)
    {
      $qtde_acessos = (isset($lista_acessos[$cod]) ? $lista_acessos[$cod]['num_acessos'] : 0);
      if ($max_qtde_acessos<$qtde_acessos)
       $max_qtde_acessos=$qtde_acessos;
    }
  }
  echo("                  </tr>\n");

  if (count($lista_users) > 0)
  {
    $local_trab_tmp = "";
    $cidade_tmp = "";
    $estado_tmp = "";
    $nome_tmp = "";
    $cod_grupo_tmp = -1;
    /* bool que indica se passar o traco no inicio da linha ou nao */
    $traco = false;

    foreach ($lista_users as $cod => $linha)
    {
      if ($radio_ord == "local" && $linha['local_trab'] != $local_trab_tmp)
      {
        $local_trab_tmp = $linha['local_trab'];
        $traco = true;
      }
      if ($radio_ord == "cidade" && $linha['cidade'] != $cidade_tmp)
      {
        $cidade_tmp = $linha['cidade'];
        $traco = true;
      }
      if ($radio_ord == "nome")
      {
        if ($nome_tmp != "")
        {
          $traco = true;
        }
        else
        {
          $nome_tmp = $linha['nome'];
        }
      }
      if ($radio_ord == "estado" && $linha['estado'] != $estado_tmp)
      {
        $estado_tmp = $linha['estado'];
        $traco = true;
      }
      if ($traco)
      {
        echo("                  <tr>\n");
        echo("                    <td colspan=".$num_colunas." height=1><hr size=1 /></td>\n");
        echo("                  </tr>\n");
        $traco = false;
      }

      echo("                  <tr>\n");

      /* nome do usuario*/
      echo("                    <td>");
      if (!$SalvarEmArquivo)
      {
        $link_abre="                      <span class=\"link\" onClick=\"AbrePerfil(".$cod.");\">";
        $link_fecha="</span>";
       }
      else
      {
        $link_abre="";
        $link_fecha="";
      }
      echo($link_abre.$linha['nome'].$link_fecha);

      unset($tmpAR);
      $c = 0;
      if ($check_local)
        $tmpAR[$c++] = $linha['local_trab'];
      if ($check_cidade)
        $tmpAR[$c++] = $linha['cidade'];
      if ($check_estado)
        $tmpAR[$c++] = $linha['estado'];
      if (count($tmpAR) > 0)
        echo("<br />".implode(" - ",$tmpAR)."\n");

      echo("                    </td>\n");

      /* ultimo acesso do usuario */
      if($check_ultimos)
      {
        echo("                    <td>\n");
        if (isset($lista_acessos[$cod]))
        {
          echo(UnixTime2DataHora($lista_acessos[$cod]['ultimo_acesso']));
        }
        else
        {
          /* 21 - Nenhum acesso */
          echo("                      <i>".RetornaFraseDaLista($lista_frases,21)."</i>");
        }
        echo("                    </td>\n");
      }
 
      /* numero de acessos do usuario*/
      if ($check_qtde)
      {
        $qtde_acessos = (isset($lista_acessos[$cod]) ? $lista_acessos[$cod]['num_acessos'] : 0);
        if ($max_qtde_acessos>0)
        {
          $tamanho_barra= (int)($qtde_acessos * 160 / $max_qtde_acessos);
        }
        echo("                    <td>");
        echo($qtde_acessos);
        echo("<br />");
        echo("                    </td>\n");
      }

      echo("                  </tr>\n");
    }
  }
  else
  {
    echo("                  <tr>\n");
    //?? - Nao existem usuarios
    echo("                    <td colspan=\"".$num_colunas."\">".RetornaFraseDaLista($lista_frases,2)."</td>\n");
    echo("                  </tr>\n");
  }

  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);

?>
