<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/em_edicao.php

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
  ARQUIVO : cursos/aplic/avaliacoes/em_edicao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,22);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  $tabela="Avaliacao_historicos";

  if (!EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n"); echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
    echo("<html lang=\"pt\">\n");
    /* 1 - Avaliacoes */
    echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title>\n");
    echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
    echo("  </head>\n");
    echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    /* 1 - Avaliacoes */
    $cabecalho = "  <br /><br /><h5>".RetornaFraseDaLista($lista_frases, 1);
    /* 25 - �rea restrita ao formador. */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 25)."</h5>";
    echo($cabecalho);
    echo("    <br />\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }
  else
  {
    echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n"); echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
    echo("<html lang=\"pt\">\n");
    /* 1 - Avalicaoes */
    echo("  <head>\n");
    echo("    <title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title>\n");
    echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
    echo("  </head>\n");
    echo("  <body link=#0000ff vlink=#0000ff onLoad=\"this.focus();\">\n");

    /* Página Principal */

    /*1 - Avaliacoes */
    $cabecalho = ("<br /><br /><h4>".RetornaFraseDaLista ($lista_frases, 1));
    /*?? - Em edicao */
    $cabecalho.= (" - Em edicao</h4>\n");
    echo($cabecalho);

    echo ("<br />\n");

    $linha_item = RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

    echo("    <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
    echo("      <tr>\n");
    echo("        <td valign=\"top\" colspan=3>\n");
    echo("          <ul class=\"btAuxTabs\">\n");
    /* 13 - Fechar (ger) */
    echo("            <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
    /* 52 - Atualizar (ger) */
    echo("            <li><span onclick=\"window.location='em_edicao.php?cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&origem=".$origem."';\">".RetornaFraseDaLista($lista_frases_geral,52)."</span></li>\n");
    echo("          </ul>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr>\n");
    echo("        <td colspan=3>\n");    
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("            <tr>\n");
    /*123 - Titulo*/
    echo("              <td  align=right><b>".RetornaFraseDaLista($lista_frases,123).":&nbsp;</b></td>\n");
    echo("              <td colspan=2>".RetornaTituloAvaliacao($sock, $linha_item['Ferramenta'], $linha_item['Cod_atividade'])."</td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("    <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
    echo("      <tr>\n");
    echo("        <td>\n");
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("            <tr>\n");
    /* 53 - Situa��o (ger)*/
    echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases_geral,53)."</b></td>\n");
    /* 56  - Desde (ger) */
    echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases_geral,56)."</b></td>\n");
     /* 57 - Por (ger)*/
    echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases_geral,57)."</b></td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");

    $res=RetornaResHistoricoDaAvaliacao($sock, $tabela, $cod_avaliacao);
    $num_linhas=RetornaNumLinhas($res);

    $linha=RetornaLinha($res);
    $num_linhas--;
    $nome_usuario=NomeUsuario($sock, $linha['cod_usuario']);
    $data=UnixTime2DataHora($linha['data']);

    if ($linha['acao']=="E")
      /* 54 - Em Edi��o (ger) */
      echo("              <td align=center>".RetornaFraseDaLista($lista_frases_geral,54)."</td>\n");
    else
      /* 55 - Edi��o conclu�da (ger) */
      echo("              <td align=center>".RetornaFraseDaLista($lista_frases_geral,55)."</td>\n");

    echo("              <td align=center>".$data."</td>\n");
    echo("              <td align=center>".$nome_usuario."</td>\n");

    echo("          </table>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
  }

?>
