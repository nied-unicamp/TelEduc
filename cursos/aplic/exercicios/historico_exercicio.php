<?php
/*
<!--
-------------------------------------------------------------------------------

  Arquivo : cursos/aplic/exercicios/historico_exercicio.php

  TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½cia
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

  Nied - Ncleo de Informï¿½ica Aplicada ï¿½Educaï¿½o
  Unicamp - Universidade Estadual de Campinas
  Cidade Universitï¿½ia "Zeferino Vaz"
  Bloco V da Reitoria - 2o. Piso
  CEP:13083-970 Campinas - SP - Brasil

  http://www.nied.unicamp.br
  nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

  /*==========================================================
  ARQUIVO : cursos/aplic/exercicios/historico_exercicio.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("exercicios.inc");
 
  $cod_usuario=VerificaAutenticacao($cod_curso);
  
  $sock=Conectar("");
 
  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $cod_ferramenta = 23;
  include("../topo_tela.php");
  
  echo("    <script type=\"text/javascript\">\n");
  echo("      function OpenWindowPerfil(funcao)\n");
  echo("      {\n");
  echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n");
  echo("    </script>\n");
  echo("  </head>\n");
  echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" onload=\"this.focus();\">\n");

  /* Página Principal */

  /* Frase #1 - Exercicios */
  /* Frase #56 - Historico */
  $cabecalho = "<br /><br /><h4>";
	$cabecalho .= RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases, 56);
  $cabecalho.= ("</h4>\n");
  echo($cabecalho);
  echo ("<br />\n");

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
  //echo("              <td  align=right><b>".RetornaFraseDaLista($lista_frases,18).":&nbsp;</b></td>\n");
  //echo("              <td colspan=2>".RetornaTituloQuestao($sock,$cod_questao)."</td>\n");
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
  /* Frase #134 - Acao */
  echo("              <td><b>".RetornaFraseDaLista($lista_frases, 134)."</b></td>\n");
  /* Frase #69 - Data */
  echo("              <td><b>".RetornaFraseDaLista($lista_frases, 69)."</b></td>\n");
  /* Frase #135 - Usuario */
  echo("              <td><b>".RetornaFraseDaLista($lista_frases, 135)."</b></td>\n");
  echo("            </tr>\n");

  $res = RetornaResHistoricoDoExercicio($sock, $cod_exercicio);
  $num_linhas = RetornaNumLinhas($res);

    while ($num_linhas>0)
    {
      $linha=RetornaLinha($res);
      $num_linhas--;
      $nome_usuario="<span class=\"link\" onclick=\"OpenWindowPerfil(".$linha['cod_usuario'].");\">".NomeUsuario($sock, $linha['cod_usuario'], $cod_curso)."</span>";
      $data=UnixTime2DataHora($linha['data']);

      switch ($linha['acao']){

        /* Frase #136 - Criacao */
        case ('C'): $acao=RetornaFraseDaLista($lista_frases,136); break;
        /* Frase #137 - Aplicada */
        case ('P'): $acao=RetornaFraseDaLista($lista_frases,137); break;
        /* Frase #138 - Cancelada aplicacao */
        case ('E'): $acao=RetornaFraseDaLista($lista_frases,138); break;
        /* Frase #139 - Desconhecida */
        default: $acao=RetornaFraseDaLista($lista_frases,139); break;
      }

      echo("            <tr>\n");
      echo("              <td align=center>".$acao."</td>\n");
      echo("              <td align=center>".$data."</td>\n");
      echo("              <td align=center>".$nome_usuario."</td>\n");
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