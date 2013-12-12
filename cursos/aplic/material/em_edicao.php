<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/em_edicao.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
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

    Nied - Ncleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/portfolio/em_edicao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("material.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta);
  
  if (EVisitante($sock, $cod_curso, $cod_usuario))
  {
    echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n");
    echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
    echo("<html lang=\"pt\">\n");
  /* 1 - 3: Atividades
         4: Material de Apoio
         5: Leituras
         7: Parada Obrigat�ria
   */
    echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title>\n");
    echo("    <script type=\"text/javascript\" language=\"JavaScript\" src=\"../js-css/jscript.js\"></script>\n");
    echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
    echo("  </head>\n");

    echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  /* 1 - 3: Atividades
         4: Material de Apoio
         5: Leituras
         7: Parada Obrigat�ria
   */
    $cabecalho = "<br><br><h5>".RetornaFraseDaLista($lista_frases, 1);
    /* 129 - Área restrita a alunos e formadores */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 129)."</h5>";
    echo($cabecalho);

    echo("    <br>\n");
    echo("  </body>\n");
    echo("  </html>\n");
    Desconectar($sock);
    exit();
  }

  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n");
  echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  /* 1 - 3: Atividades
         4: Material de Apoio
         5: Leituras
         7: Parada Obrigat�ria
   */
  echo("  <head>\n");
  echo("    <title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title>\n");
  echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("    <script type=\"text/javascript\" language=\"JavaScript\" src=\"../js-css/jscript.js\"></script>\n");
  echo("  </head>\n");
  echo("  <body link=#0000ff vlink=#0000ff onLoad=\"self.focus();\">\n");

  switch ($cod_ferramenta) {
    case 3 :
      echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"atividades.css\">\n");
      $tabela="Atividade";
      break;
    case 4 :
      echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"apoio.css\">\n");
      $tabela="Apoio";
      break;
    case 5 :
      echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"leituras.css\">\n");
      $tabela="Leitura";
      break;
    case 7 :
      echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"obrigatoria.css\">\n");
      $tabela="Obrigatoria";
      break;
  }


  /* Página Principal */
  $linha_item=RetornaDadosDoItem($sock, $tabela, $cod_item);

  /* 1 - 3: Atividades
         4: Material de Apoio
         5: Leituras
         7: Parada Obrigat�ria
   */
  $cabecalho =  "<br /><br /><h4>".RetornaFraseDaLista ($lista_frases, 1);

  /* 54(ger) - Em Edição */
  $cabecalho.= " - ".RetornaFraseDaLista($lista_frases_geral,54)."</h4>\n";
  echo("    ".$cabecalho);
  echo("    <br />\n");
 
  $res=RetornaResHistoricoDoItem($sock, $tabela, $cod_item);
  $num_linhas=RetornaNumLinhas($res);

  $linha=RetornaLinha($res);
  $num_linhas--;
  $nome_usuario=NomeUsuario($sock, $linha['cod_usuario'], $cod_curso);
  $data=UnixTime2DataHora($linha['data']);


  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("      <tr>\n");
  echo("        <td valign=\"top\">\n");
  echo("          <ul class=\"btAuxTabs\">\n");

  if ($linha['acao']=="E")
  {
    /* 52 - Atualizar (ger) */
    echo("            <li><a href=\"em_edicao.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_item=".$cod_item."&amp;origem=".$origem."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".RetornaFraseDaLista($lista_frases_geral,52)."</a></li>\n");

  }
  else
  {
    echo("            <script type=\"text/javascript\" language=\"javascript\">\n");
    echo("               window.opener.document.location='".$origem.".php?cod_curso=".$cod_curso."&amp;cod_topico=".$cod_topico_raiz."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_item=".$cod_item."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."';\n");
    echo("            </script>\n");
    echo("          </ul>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
  }

  /* 13 - Fechar (ger) */
  echo("            <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  echo("          </ul>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td>\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("            <tr>\n");
  /* 119 - Item */
  echo("              <td  align=right><b>".RetornaFraseDaLista($lista_frases,119).":&nbsp;</b></td>\n");
  echo("              <td>".$linha_item['titulo']."</td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  /* (ger) 53 - Situação */
  echo("              <td  align=right><b>".RetornaFraseDaLista($lista_frases_geral,53).":&nbsp;</b></td>\n");

  if ($linha['acao']=="E")
    /* (ger) 54 - Em Edição */
    echo("              <td>".RetornaFraseDaLista($lista_frases_geral,54)."</td>\n");
  else
    /* (ger) 55 - Edição concluída */
    echo("              <td>".RetornaFraseDaLista($lista_frases_geral,55)."</td>\n");

  echo("            </tr>\n");
  echo("            <tr>\n");
  /* 56  - Desde */
  echo("              <td align=right><b>".RetornaFraseDaLista($lista_frases_geral,56).":&nbsp;</b></td>\n");
  echo("              <td>".$data."</td>\n");
  echo("            </tr>\n");

  if ($linha['acao']=="E")
  {
    echo("            <tr>\n");
    /* 57 - Por */
    echo("              <td align=right><b>".RetornaFraseDaLista($lista_frases_geral,57).":&nbsp;</b></td>\n");
    echo("              <td>".$nome_usuario."</td>\n");
    echo("            </tr>\n");
  }

  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");

?>
