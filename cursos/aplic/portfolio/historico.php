<?php
/*
<!--
-------------------------------------------------------------------------------

  Arquivo : cursos/aplic/portfolio/historico.php

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
  ARQUIVO : cursos/aplic/portfolio/historico.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("portfolio.inc");

  $cod_ferramenta = 15;
  include("../topo_tela.php");
  
  $sock=Conectar("");
 
  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario, true);

  $linha_item=RetornaDadosDoItem($sock, $cod_item);

  /* Página Principal */

  $status_portfolio = RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $linha_item['cod_grupo']);

  $dono_portfolio    = $status_portfolio ['dono_portfolio'];
  $portfolio_apagado = $status_portfolio ['portfolio_apagado'];
  $portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

  // 1 - Portf�io
  $cabecalho = ("<br /><br /><h4>".RetornaFraseDaLista ($lista_frases, 1));

  // 2 - Portfolio individual
  // 3 - Portfolio de grupo
  $tipo_portfolio = ($portfolio_grupo ? RetornaFraseDaLista ($lista_frases, 3) : RetornaFraseDaLista ($lista_frases, 2) );

  /* 72 - Hist�ico */
  $cabecalho.= (" - ".$tipo_portfolio." / ".RetornaFraseDaLista($lista_frases,72)."</h4>\n");
  echo($cabecalho);
  // 3 A's - Muda o Tamanho da fonte
  echo("      <div id=\"mudarFonte\" style=\"top: 42px;\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo ("<br />\n");

  $figura = "../imgs/arquivo_";
  $figura.= ($portfolio_grupo   ? "g_" : "i_");
  if ($portfolio_apagado)
  {
    $figura .= "x.gif";
  }
  else
  {
    if ($dono_portfolio)
      $figura .= "p.gif";
    else
      $figura .= "n.gif";
  }

  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("      <tr>\n");
  echo("        <td valign=\"top\" colspan=3>\n");
  echo("          <ul class=\"btAuxTabs\">\n");
   /* 13 - Fechar (ger) */
  echo("            <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  echo("          </ul>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td colspan=3>\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("            <tr>\n");
  echo("              <td  align=right><b>".RetornaFraseDaLista($lista_frases,125).":&nbsp;</b></td>\n");
  echo("              <td colspan=2>".$linha_item['titulo']."</td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td>\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("            <tr>\n");
  /* 89 - Ação */
  echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases,89)."</b></td>\n");
  /* 9 - Data */
  echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases,9)."</b></td>\n");
  /* 90 - Usuário */
  echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases,90)."</b></td>\n");
  echo("            </tr>\n");

  $res=RetornaResHistoricoDoItem($sock, $cod_item);

   $res2=RetornaArrayLinhas($res);

  foreach($res2 as $cod => $linha){
    switch ($linha['acao']){

                  /* 93 - Cria�o */
      case ('C'): $acao=RetornaFraseDaLista($lista_frases,93); break;
                  /* 94 - Edi�o Cancelada */
      case ('D'): $acao=RetornaFraseDaLista($lista_frases,94); break;
                  /* 54 - Em Edi�o */
      case ('E'): $acao=RetornaFraseDaLista($lista_frases_geral,54); break;
                  /* 91 - Edi�o Finalizada */
      case ('F'): $acao=RetornaFraseDaLista($lista_frases,91); break;
                  /* 95 - Movida */
      case ('M'): $acao=RetornaFraseDaLista($lista_frases,95); break;
                  /* 96 - Exclus� */
      case ('A'): $acao=RetornaFraseDaLista($lista_frases,96); break;
                  /* 97 - Recupera�o */
      case ('R'): $acao=RetornaFraseDaLista($lista_frases,97); break;
                  /* 98 - Exclu�a definitivamente */
      case ('X'): $acao=RetornaFraseDaLista($lista_frases,98); break;
                  /* 92 - Desconhecida */
      default: $acao=RetornaFraseDaLista($lista_frases,92); break;
    }

    $data = UnixTime2DataHora($linha['data']);
    $nome_usuario="<a href=\"#\" onclick=\"return(OpenWindowPerfil(".$linha['cod_usuario']."));\">".NomeUsuario($sock, $linha['cod_usuario'], $cod_curso)."</a>";

    echo("            <tr>\n");
    echo("              <td align=center><font class=text>".$acao."</font></td>\n");
    echo("              <td align=center><font class=text>".$data."</font></td>\n");
    echo("              <td align=center><font class=text>".$nome_usuario."</font></td>\n");
    echo("            </tr>\n");
  }

  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");

?>
