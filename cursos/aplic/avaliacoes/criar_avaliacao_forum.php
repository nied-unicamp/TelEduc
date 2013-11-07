<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/criar_avaliacao_forum.php

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
  ARQUIVO : cursos/aplic/avaliacoes/criar_avaliacao_forum.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_ferramenta=22;

  $cod_ferramenta_ajuda = 9; //ajuda da ferramenta forum
  $cod_pagina_ajuda=18;

  $tabela="Avaliacao";

  $tipo="I";

  include("../topo_tela.php");

  GeraJSVerificacaoData();
  GeraJSComparacaoDatas();
  /* ***************** Fun��es JavaScript **************** */

  echo("  <script language=\"javascript\">\n");
  echo("      function Iniciar()\n");
  echo("      {\n");

  echo("        startList();\n");
  echo("      }\n\n");

  echo("  function Atualiza() {\n");
  echo("    document.atualizar.submit();\n");
  echo("  }\n");

  echo("  function verifica_formulario(form)\n");
  echo("  {\n");
  echo("    if(form.valor.value == ''){\n");
  // 3 - Voc� n�o informou o valor da avalia��o.
  echo("      alert('".RetornaFraseDaLista($lista_frases,3)."');\n");
  echo("      form.valor.focus();\n");
  echo("      return(false);\n");
  echo("    }\n");
  echo("    if (form.valor.value < 0){\n");
  // 4 - A avalia��o n�o pode ter valor negativo. 
  echo("      alert('".RetornaFraseDaLista($lista_frases,4)."');\n");
  echo("      form.valor.focus();\n");
  echo("      return(false);\n");
  echo("    }\n");

  echo("    var valor = document.avaliacao.valor.value;\n");
  echo("    if (valor_com_digito_estranho(valor)) \n");
  echo("    {\n");
  // 5 - Voc� digitou caracteres estranhos neste valor da atividade. 
  // 6 - Use apenas d�gitos de 0 a 9 e o ponto ( . ) ou a v�rgula ( , ) para o campo valor (exemplo: 7.5).
  // 7 - Por favor retorne e corrija.
  echo("      alert('".RetornaFraseDaLista($lista_frases,5)."\\n".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,7)."');\n");
  echo("      form.valor.focus();\n");
  echo("      return(false);\n");
  echo("    }\n");

  echo("    data_ini=form.data_inicio.value;\n");
  echo("    data_fim=form.data_fim.value;\n");
  echo("    dia_ini = data_ini.substring(0,2);\n");
  echo("    dia_fim = data_fim.substring(0,2);\n");
  echo("    mes_ini = data_ini.substring(3,5);\n");
  echo("    mes_fim = data_fim.substring(3,5);\n");
  echo("    ano_ini = data_ini.substring(6,10);\n");
  echo("    ano_fim = data_fim.substring(6,10);\n");
  echo("    if (ano_fim < ano_ini){ \n");
  /* 212 - Período Inválido */
  echo("      alert('".RetornaFraseDaLista($lista_frases,212)."');");
  echo("      return(false); \n");
  echo("    }\n");
  echo("    if ((mes_fim < mes_ini) && (ano_fim==ano_ini)){ \n");
  /* 212 - Período Inválido */
  echo("      alert('".RetornaFraseDaLista($lista_frases,212)."');");
  echo("      return(false); \n");
  echo("    }\n");
  echo("    if ((mes_fim==mes_ini) && (ano_fim==ano_ini) && (dia_fim < dia_ini)){ \n");
  /* 212 - Período Inválido */
  echo("      alert('".RetornaFraseDaLista($lista_frases,212)."');");
  echo("      return(false); \n");
  echo("    }\n");
  // 8 - Verifica se as datas estao em um formato valido 
  echo("   if (! DataValidaAux(document.avaliacao.data_inicio)) {\n");
  echo("     return false;\n");
  echo("   }\n");
  echo("   if (! DataValidaAux(document.avaliacao.data_fim)) {\n");
  echo("     return false;\n");
  echo("   }\n");
  echo("  }\n");

  echo("  function verifica_valor(campo) \n");
  echo("  {\n");
  echo("    var valor = campo.value;\n");
  echo("  if (check)\n");
  echo("    {\n");
  echo("      if (valor_com_digito_estranho(valor)) \n");
  echo("      {\n");
  echo("      check=false;\n");
 /* 5 - Voc� digitou caracteres estranhos neste valor da atividade. */
  /* 6 - Use apenas d�gitos de 0 a 9 e o ponto ( . ) ou a v�rgula ( , ) para o campo valor (exemplo: 7.5).*/
  /* 7 - Por favor retorne e corrija.*/
  echo("        alert('".RetornaFraseDaLista($lista_frases,5)."\\n".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,7)."');\n");
  echo("        return(false);\n");
  echo("      }\n");
  echo("    }\n");
  echo("  }\n");


  echo("  function valor_com_digito_estranho(valor) \n");
  echo("  {\n");
  echo("    var erro=false;\n");
  echo("    var ponto=false;\n");
  echo("    var i=0;\n");
  echo("    var c='';\n");
  echo("   c=valor.charAt(i);\n");
  echo("   if(c<'0' || c>'9')\n");
  echo("   erro=true; \n");
  echo("    while (i<valor.length && !erro) \n");
  echo("    {\n");
  echo("      c=valor.charAt(i);\n");
  echo("      if ((c<'0' || c>'9') && c!='.' && c!=',') \n");
  echo("        erro=true; \n");
  echo("      if((c=='.' || c==',')&&(ponto==false))\n");
  echo("       ponto=true;\n");
  echo("     else if((c=='.' || c==',')&&(ponto==true))\n");
  echo("       erro=true;\n");
  echo("      i++; \n");
  echo("    }\n");
  echo("    return(erro);\n");
  echo("  }\n");

  echo("  function limpadados(){\n");
  echo("    form = document.avaliacao\n");
  echo("    form.valor.value = ''\n");
  echo("    form.objetivos.value = ''\n");
  echo("    form.criterios.value = ''\n");
  echo("    form.data_inicio.value = ''\n");
  echo("    form.data_fim.value = ''\n");
  echo("    form.valor.focus();");
  echo("  }\n");
  echo("  </script>\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">");

  /* Verifica se a pessoa a editar � formador */
  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
    /* 1 - Avalia��es  8 - �rea restrita ao formador. */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,8)."</h4>\n");

    /*Voltar*/
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("          <form><input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");

    echo("        </td>\n");
    echo("      </tr>\n");

    include("../tela2.php");

    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }

  /* 1 - Avalia��es */
  echo("          <h4>".RetornaFraseDaLista($lista_frases, 1));
  /* 9 - Cadastro de Avalia��o */
  echo("    - ".RetornaFraseDaLista($lista_frases, 9)."</h4>");
  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  $cod_pagina=5;

  $dados=RetornaAvaliacao($sock,$cod_atividade,'F');
  if ($dados['Cont']>0)
  {

    $linha_hist=RetornaUltimaPosicaoHistoricoAvaliacao($sock, 'Avaliacao_historicos', $dados['Cod_avaliacao']);
    if ($dados['Status']=='C')
    {
      /* algu�m j� est� editando */
      /* Ve se n�o � voc� */
      if ($cod_usuario!=$dados['Cod_usuario'])
      {
        if ($linha_hist['data']>time()-1800)
        {
          /* 78 - A Avalia��o j� est� sendo criada desde */
          echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,78));

          /* 57 - Por */
          echo(Unixtime2DataHora($dados['Data']));

          echo(" ".RetornaFraseDaLista($lista_frases_geral,57)." ".NomeUsuario($sock,$dados['Cod_usuario']).".<br><br>");

          /* 23 - Voltar (gen) */
          echo("<form><input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"location='../forum/forum.php?cod_curso=".$cod_curso."';\"></form>\n");

          echo("</body></html>\n");
          Desconectar($sock);
          exit;
        }
        /* Passou o tempo limite, captura a edi��o */
      }
      /* �. Atualiza data e segue em frente. */
      CancelaEdicaoAvaliacao($sock, $tabela, $dados['Cod_avaliacao'], $cod_usuario);
      $cod_avaliacao=IniciaCriacaoAvaliacao($sock, $tabela,$cod_atividade, $cod_usuario, 'F', $tipo);
    }
    elseif (($dados['Status']=='F') || ($dados['Status']=='E'))
    {
    // 70 - J� existe uma avalia��o criada para esta atividade.
      echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,70)."</font><br><br>");

      // 23 - Voltar (gen) 
      echo("<form><input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"location='../forum/forum.php?cod_curso=".$cod_curso."';\"></form>\n");
      echo("</body></html>\n");
      Desconectar($sock);
      exit;
    }
    elseif ($dados['Status']=='A')
    {
      // 71 - J� existe uma avalia��o criada para esta atividade. Por�m, ela foi apagada.
      // 72 - Se desejar criar outra avalia��o, voc� precisa primeiro excluir definitivamente a avalia��o existente.
      echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,71)."");
      echo(" ".RetornaFraseDaLista($lista_frases,72)."</font><br><br>");

      // 23 - Voltar (gen) 
      echo("<form><input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"location='../forum/forum.php?cod_curso=".$cod_curso."';\"></form>\n");
      echo("</body></html>\n");
      Desconectar($sock);
      exit;
    }
  }
  else
  {
    $cod_avaliacao=IniciaCriacaoAvaliacao($sock, $tabela,$cod_atividade, $cod_usuario, 'F', $tipo);
  }

  $forum=RetornaForum($sock,$cod_atividade);

  echo("<form name=\"avaliacao\" action=\"criar_avaliacao_forum2.php?\" method=\"post\" onSubmit=\"return(verifica_formulario(document.avaliacao));\">\n");

  echo("    <br>\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");
  echo("      <ul class=\"btAuxTabs\">\n");
  /* 224 - Limpar Dados */
  echo("        <li><a href=\"#\" onclick=\"limpadados();\">".RetornaFraseDaLista($lista_frases,224)."</a></li>\n");
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr class=\"head01\">\n");
  echo("    <td>\n");
  /* 12 - F�rum */
  echo("    ".RetornaFraseDaLista($lista_frases,12).":\n");
  echo("    ".$forum);
  /* 79 - Forne�a abaixo os dados que ser�o considerados na avalia��o desta atividade. */
  echo("  <br><font class=\"text\">".RetornaFraseDaLista($lista_frases,79)."<br>\n");
echo("   </td>\n");
echo("  </tr>\n");
echo("  <tr>\n");
echo("   <td>\n");

  echo("      <table border=0 width=\"100%\" cellspacing=0 id=\"tabelaInterna\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  /* 19 - Valor */
  echo("          <td class=\"itens\" colspan=\"2\">".RetornaFraseDaLista($lista_frases,19)."</td>\n");
  echo("        </tr>\n");
  echo("        <tr>\n");
  echo("          <td class=\"itens\" colspan=\"2\">\n");
  echo("            <input type=\"text\" name=\"valor\" class=input size=6 maxlength=10 value=\"".stripslashes($valor)."\" onChange=\"check=true;\" onBlur=\"verifica_valor(document.avaliacao.valor);\">\n");
  echo("          </td>\n");
  echo("        </tr>\n");

  echo("        <tr class=\"head\">\n");
  /* 75 - Objetivos */
  echo("          <td class=\"center\" width=\"50%\">".RetornaFraseDaLista($lista_frases,75)."</td>\n");
  /* 23 - Criterios */
  echo("          <td class=\"center\">".RetornaFraseDaLista($lista_frases,23)."</td>\n");
  echo("        </tr>\n");
  echo("        <tr>\n");
  echo("          <td width=\"50%\" style='vertical-align:top; text-align:left;'>\n");
  echo("            <textarea name=\"objetivos\" rows=6 cols=50 wrap=soft class=\"input\">".stripslashes($objetivos)."</textarea>\n");
  echo("          <td width=\"50%\" class=\"itens\">\n");
  echo("            <textarea name=\"criterios\" rows=6 cols=50 wrap=soft class=\"input\">".stripslashes($criterios)."</textarea>\n");
  echo("          </td>\n");
  echo("        </tr>\n");

  echo("  <tr class=\"head\">\n");

  /* 168 - Período */
  /* 18 - dd/mm/aaaa */ 
  echo("    <td colspan=2>".RetornaFraseDaLista($lista_frases,168)." (".RetornaFraseDaLista($lista_frases,18).")</td>\n");


  echo("  </tr>\n");
  echo("  <tr>\n");
  /* 16 - Data de in�cio*/
  echo("  <td>".RetornaFraseDaLista($lista_frases,16)." <input type=\"text\" id=\"data_inicio\" name=\"data_inicio\" size='10' maxlength='10' value=\"".UnixTime2Data(time())."\" class=\"input\" /><img src=\"../imgs/ico_calendario.gif\" alt=\"\" onclick=\"displayCalendar(document.getElementById ('data_inicio'),'dd/mm/yyyy',this);\" />\n");

  /* 17 - Data de T�rmino */
  echo("  <td>".RetornaFraseDaLista($lista_frases,17)." <input type=\"text\" id=\"data_fim\"    name=\"data_fim\"    size='10' maxlength='10' value=\"".UnixTime2Data(time())."\" class=\"input\" /><img src=\"../imgs/ico_calendario.gif\" alt=\"\" onclick=\"displayCalendar(document.getElementById ('data_fim'),'dd/mm/yyyy',this);\" />\n");

  echo("  </tr>\n");

   echo("<input type=\"hidden\" name=\"cod_curso\"      value=\"".$cod_curso."\">\n");
   echo("<input type=\"hidden\" name=\"cod_avaliacao\"  value=\"".$cod_avaliacao."\">\n");
   echo("<input type=\"hidden\" name=\"cod_ferramenta\" value=\"".$cod_ferramenta."\">\n");
   echo("<input type=\"hidden\" name=\"cod_atividade\"  value=\"".$cod_atividade."\">\n");
   echo("<input type=\"hidden\" name=\"cod_usuario\"    value=\"".$cod_usuario."\">\n");


   echo("<table width=100%><tr><td align=right>");
   /* 11 - Enviar */
   echo("      <input class=\"input\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral, 11)."\">\n");


   /* 2 - Cancelar (ger) */
   echo("<input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" onclick=\"location='../forum/acoes.php?cod_forum=".$cod_atividade."&cod_curso=".$cod_curso."&cancelar_avaliacao=sim';\">");


   echo("</td></tr></table>\n");
   echo("</table>\n");
   echo("</form>\n");

  include("../tela2.php");
  echo("</body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
