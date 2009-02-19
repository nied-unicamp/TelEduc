<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/acessos/relatorio_frequencia_usr.php

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
  ARQUIVO : cursos/aplic/acessos/relatorio_frequencia_usr.php
  ========================================================== */

/* C�igo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("acessos.inc");
  include("acessos_aux.inc");

  $sock=Conectar("");
  $lista_frases_ferramentas=RetornaListaDeFrases($sock,-4);
  $lista_ferramentas=RetornaListaFerramentas($sock);
  Desconectar($sock);

  $cod_ferramenta = 18;
  include("../topo_tela.php");

  echo("    <script language=JavaScript>\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");
  
  if (!isset($SalvarEmArquivo))
  {
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

    echo("      function AbrePerfil(cod_usuario)\n");
    echo("      {\n");
    echo("         window.open('../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("      }\n");
  }

  echo("    </script>\n");
  
  echo("  </head>\n");
  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
  echo("    <a name=\"topo\"></a>\n");

  /* 1 - Acessos */
  $cabecalho ="<h4>".RetornaFraseDaLista($lista_frases,1);
  /* 41 - Relatório de Acessos à Ferramentas */
  $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,41)."</h4>";
  echo("    <br /><br />".$cabecalho."\n");
  echo("    <br />\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("      <tr>\n");
  echo("        <td colspan=3 valign=\"top\">\n");

  if (!isset($SalvarEmArquivo))
  {
    echo("          <form action=\"salvar_arquivo.php\" name=\"formSalvar\">\n");
    echo("            <input type=hidden name=cod_curso value=".$cod_curso." />\n");
    echo("            <input type=hidden name=origem value='ferr_usr' />\n");
    echo("            <input type=hidden name=nome_arquivo value='relatorio_ferramentas_usr.html' />\n");
    if(isset($opcao))
      echo("            <input type=hidden name=opcao value=".$opcao." />\n");
    if(isset($cod_ferramenta))
      echo("            <input type=hidden name=cod_ferramenta value=".$cod_ferramenta." />\n");
    if(isset($diaUT))
      echo("            <input type=hidden name=diaUT value=".$diaUT." />\n");
    if(isset($data_iniUT))
      echo("            <input type=hidden name=data_iniUT value=".$data_iniUT." />\n");
    if(isset($data_fimUT))
      echo("            <input type=hidden name=data_fimUT value=".$data_fimUT." />\n");
    if (isset($usuario))
      echo("            <input type=hidden name=usuario value=".$usuario.">\n");
    if (isset($cod_grupo))
      echo("            <input type=hidden name=cod_grupo value=".$cod_grupo.">\n");
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
  echo("          <br />\n");
  echo("          <br />\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td>\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  
  if ((!isset($hora_iniUT) || $hora_iniUT == "") && (isset($diaUT)))
  {
    $hora_iniUT = Data2UnixTime(UnixTime2Data($diaUT));
    $hora_fimUT = $hora_iniUT + 86399;
  }

  /* tela anterior �relatorio_frequencia3.php para entrada no ambiente. */
  /* esta �uma lista de datas-ferramentas para um unico usuario */
  $infos_user = RetornaInfosUsuario($sock,$cod_curso, $usuario);

  $lista_acessos = RetornaAcessosFerramentasUsuario($sock,$usuario,$hora_iniUT,$hora_fimUT);
  if (!isset($SalvarEmArquivo))
  {
    $link_perfil_abre = "<span class=\"link\" onClick=return(AbrePerfil(".$usuario."));>";
    $link_perfil_fecha= "</span>";
  }
  else
  {
    $link_perfil_abre = "";
    $link_perfil_fecha= "";
  }
  echo($link_perfil_abre.$nome.$link_perfil_fecha);
  
      /* 18 - Usuário */
    echo("            <tr>\n");
    echo("              <td width=\"50%\">\n");
    echo("                <b>".RetornaFraseDaLista($lista_frases,18)."</b>\n");
    echo("              </td>\n");
    echo("              <td width=\"50%\">".$link_perfil_abre.$infos_user['nome'].$link_perfil_fecha."</td>\n");
    echo("            </tr>\n");

    /* acessos do usuario no dia, variaveis $usuario e $diaUT */
    $acessos_usuarioUT=RetornaUnicoUsuarioAcessosUT($sock,$usuario,$cod_ferramenta,$diaUT,($diaUT + 86399));
    /* 46 - Acessos no dia */
    echo("            <tr>\n");
    echo("              <td width=\"50%\">\n");
    echo("                <b>".RetornaFraseDaLista($lista_frases,46)."</b>\n");
    echo("              </td>\n");
    echo("              <td width=\"50%\">\n");
    echo("                ".UnixTime2Data($diaUT)."\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
    echo("          <br />\n");
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  if (count($lista_acessos) > 0)
  {

    /* cabecalho da tabela */
    echo("            <tr class=\"head\">\n");
    /* 49 - Hor�io */
    echo("              <td width=50%>".RetornaFraseDaLista($lista_frases,49)."</td>\n");
    /* 16 - Ferramenta */
    echo("              <td width=50%>".RetornaFraseDaLista($lista_frases,16)."</td>\n");
    echo("            </tr>\n");
    // variavel para alternancia de cores entre celulas
    $c = 1;
    foreach ($lista_acessos as $dataUT => $cod_ferramenta)
    {
      if ($cod_ferramenta > 0)
      {
        $css = "class=\"altColor".($c % 2)."\"";
        $cssTd="";
        $nome_ferramenta = RetornaFraseDaLista($lista_frases_ferramentas,$lista_ferramentas[$cod_ferramenta]['cod_texto_nome']);
      }
      else
      {
        $css="";
        $cssTd="style=\"font-weight:bold;\"";
        $nome_ferramenta=RetornaFraseDaLista($lista_frases,29);
      }
      echo("            <tr ".$css." >\n");
      echo("              <td ".$cssTd.">".UnixTime2Hora($dataUT)."</td>\n");
      echo("              <td ".$cssTd.">".$nome_ferramenta."</td>");
      echo("            </tr>\n");
      $c++;
    }

  }
  else
  {
        echo("            <tr class=\"head\">\n");
        echo("              <td>\n");
        /* 47 - N� houve acessos pelo usu�io no per�do. */
        echo("                ".RetornaFraseDaLista($lista_frases,47)."\n");
        echo("              </td>\n");
        echo("            </tr>\n");
  }
  
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  Desconectar($sock);
  echo("  </body>\n");
  echo("</html>");

?>
