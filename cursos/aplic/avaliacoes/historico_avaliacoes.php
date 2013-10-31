<?php
/*
<!--
-------------------------------------------------------------------------------

  Arquivo : cursos/aplic/portfolio/historico_avaliacoes.php

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
  ARQUIVO : cursos/aplic/portfolio/historico_avaliacoes.php
  ========================================================== */
	/* TODO - Incluir topo_tela e tela2.php */
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $cod_curso = $_GET['cod_curso'];

  $sock = Conectar("");
  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);  

  $lista_frases=RetornaListaDeFrases($sock,22);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  $tabela="Avaliacao_historicos";

  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n"); echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  /* 1 - Avaliacoes */
  echo("  <head>\n");
  echo("    <title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title>\n");
  echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("      function startList() {\n");
  echo("        if (document.all && document.getElementById) {\n");
  echo("          nodes = document.getElementsByTagName(\"span\");\n");
  echo("          for (i=0; i < nodes.length; i++) {\n");
  echo("            node = nodes[i];\n");
  echo("            node.onmouseover = function() {\n");
  echo("              this.className += \"Hover\";\n");
  echo("            }\n");
  echo("            node.onmouseout = function() {\n");
  echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
  echo("            }\n");
  echo("          }\n");
  echo("          nodes = document.getElementsByTagName(\"li\");\n");
  echo("          for (i=0; i < nodes.length; i++) {\n");
  echo("            node = nodes[i];\n");
  echo("            node.onmouseover = function() {\n");
  echo("              this.className += \"Hover\";\n");
  echo("            }\n");
  echo("            node.onmouseout = function() {\n");
  echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
  echo("            }\n");
  echo("          }\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function OpenWindowPerfil(funcao)\n");
  echo("      {\n");
  echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n");
  echo("    </script>\n");
  echo("	<script type=\"text/javascript\" src=\"../js-css/jscript.js\"></script>");
  echo("  </head>\n");
  echo("  <body link=#0000ff vlink=#0000ff onLoad=\"Iniciar();\">\n");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  $tmp = RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  $cod_atividade = $tmp['Cod_atividade'];
  $tipo_ferramenta = $tmp['Ferramenta'];
  $titulo=RetornaTituloAvaliacao($sock,$tipo_ferramenta,$cod_atividade);

  /* Página Principal */

  // 1 - Avaliacoes
  $cabecalho = ("<br /><br /><h4>".RetornaFraseDaLista ($lista_frases, 1));
  /* 99 - Hist�ico */
  $cabecalho.= (" - ".RetornaFraseDaLista($lista_frases,99)."</h4>\n");
  echo($cabecalho);
  echo ("<br />\n");

      // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

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
  /*123 - Titulo*/
  echo("              <td  align=right><b>".RetornaFraseDaLista($lista_frases,123).":&nbsp;</b></td>\n");
  echo("              <td colspan=2>".$titulo."</td>\n");
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
  /* 100 - Ação */
  echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases,100)."</b></td>\n");
  /* 101 - Data */
  echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases,101)."</b></td>\n");
  /* 102 - Usuário */
  echo("              <td align=center><b>".RetornaFraseDaLista($lista_frases,102)."</b></td>\n");
  echo("            </tr>\n");

  $res=RetornaResHistoricoDaAvaliacao($sock, $tabela, $cod_avaliacao);
  $num_linhas=RetornaNumLinhas($res);

  while ($num_linhas>0)
  {
    $linha=RetornaLinha($res);
    $num_linhas--;
    $nome_usuario="<a href=# onclick=\"return(OpenWindowPerfil(".$linha['cod_usuario']."));\">".NomeUsuario($sock, $linha['cod_usuario'], $cod_curso)."</a>";
    $data=UnixTime2DataHora($linha['data']);

    switch ($linha['acao']){

                /* 149 - Cria�o */
      case ('C'): $acao=RetornaFraseDaLista($lista_frases,149); break;
                /* 150 - Edi�o Cancelada */
      case ('D'): $acao=RetornaFraseDaLista($lista_frases,150); break;
                /* 154 - Em Edi�o */
      case ('E'): $acao=RetornaFraseDaLista($lista_frases,154); break;
                /* 147 - Edi�o Finalizada */
      case ('F'): $acao=RetornaFraseDaLista($lista_frases,147); break;
                /* 151 - Exclus�o*/
      case ('A'): $acao=RetornaFraseDaLista($lista_frases,151); break;
                /* 152 - Recupera��o*/
      case ('R'): $acao=RetornaFraseDaLista($lista_frases,152); break;
                /* 153 - Exclu�da definitivamente*/
      case ('X'): $acao=RetornaFraseDaLista($lista_frases,153); break;
                /* 148 - Desconhecida */
      default: $acao=RetornaFraseDaLista($lista_frases,148); break;
    }

    echo("            <tr>\n");
    echo("              <td align=center><font class=\"text\">".$acao."</font></td>\n");
    echo("              <td align=center><font class=\"text\">".$data."</font></td>\n");
    echo("              <td align=center><font class=\"text\">".$nome_usuario."</font></td>\n");
    echo("            </tr>\n");
  }

  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);

?>
