<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/forum_grafico_periodo2.php

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
  ARQUIVO : cursos/aplic/intermap/forum_grafico_periodo2.php
  ========================================================== */

/* C�digo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("intermap.inc");
  include("forum.inc");

  $cod_ferramenta=19;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario, true);

  echo("<script language=\"javascript\">\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  } \n");

  echo("  function SalvarEmArquivo()\n");
  echo("  {\n");
  echo("    document.location='salvar_arquivo.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&inicio=".$inicio."&fim=".$fim."&apresentacao=".$apresentacao."&cod_forum=".$cod_forum."&todos=".$todos."&agrupar=".$agrupar."&exibir=".$exibir."&nome_arquivo=forum_grafico_periodo'");
  echo("  }\n");

  echo("  function ImprimirRelatorio()\n");
  echo("  {\n");
  echo("    if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("    {\n");
  echo("      self.print();\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
  /* 51- Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
  echo("      alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
  echo("    }\n");
  echo("  }\n");

  echo("</script>\n");

  echo("<body onLoad=\"Iniciar();\">\n");
  echo("<br><br>\n");

  /* 1 - Intermap */
  echo("<h4>".RetornaFraseDaLista($lista_frases,1));
  /* 28 - F�rum de Discuss�o */
  echo(" - ".RetornaFraseDaLista($lista_frases,28));

  if ($apresentacao=="grafico")
    /* 33 - Gr�fico por Per�odo */
    echo(" - ".RetornaFraseDaLista($lista_frases,33)."</h4>\n");
  else
    /* 70 - Tabela por Per�odo */
    echo(" - ".RetornaFraseDaLista($lista_frases,70)."</h4>\n");

  if ($apresentacao=="tabela")
    $cod_pagina=15;
  else
    $cod_pagina=14;

  echo("<br>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  echo("      <ul class=\"btAuxTabs\">\n");
  /* 13 - Fechar (geral) */
  echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  /* 14 - Imprimir (geral) */
  echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,14)."\" onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");

  if (!$SalvarEmArquivo && $apresentacao=="tabela")
  {
    /* 22 - Salvar Em Arquivo */
    echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,50)."\" onClick=\"SalvarEmArquivo();\">".RetornaFraseDaLista($lista_frases_geral,50)."</span></li>\n");
  }
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td>\n");

  $forum=RetornaForum($sock,$cod_forum);

  echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  // 29 - F�rum de Discuss�o:
  echo("          <td>".RetornaFraseDaLista($lista_frases,29)."</td>\n");
  // 58 - Per�odo:
  echo("          <td>".RetornaFraseDaLista($lista_frases,58)."</td>\n");
  // 5 - Agrupado por:
  echo("          <td>".RetornaFraseDaLista($lista_frases,5)."</td>\n");
  // 25 - Exibir:
  echo("          <td>".RetornaFraseDaLista($lista_frases,25)."</td>\n");
  echo("        </tr>\n");

  echo("        <tr>\n");
  echo("          <td>\n");
  echo($forum['nome']." ");
  if ($forum['status']=='L')
    echo("(somente leitura)");
  // 18 - de
  echo(" - ".RetornaFraseDaLista($lista_frases,18)." ".UnixTime2Data($forum['inicio']));
  // 11 - at�
  echo(" ".RetornaFraseDaLista($lista_frases,11)." ".UnixTime2Data($forum['fim']));
  echo("          </td>\n");
  echo("          <td>".$inicio." - ".$fim."</td>\n");
  echo("          <td>\n");
  if ($agrupar=="dia")
    // 21 - Dia
    echo("            ".RetornaFraseDaLista($lista_frases,21));
  else if ($agrupar=="semana")
    // 63 - Semana
    echo("            ".RetornaFraseDaLista($lista_frases,63));
  else if ($agrupar=="mes")
    // 46 - M�s
    echo("            ".RetornaFraseDaLista($lista_frases,46));
  echo("          </td>\n");
  echo("          <td>\n");
  if ($exibir=="todos")
    // 73 - Todos os participantes
    echo("            ".RetornaFraseDaLista($lista_frases,73));
  else if ($exibir=="formador")
    // 66 - Somente Formadores
    echo("            ".RetornaFraseDaLista($lista_frases,66));
  else if ($exibir=="aluno")
    // 65 - Somente Alunos
    echo("            ".RetornaFraseDaLista($lista_frases,65));
  echo("          </td>\n");
  echo("        </tr>\n");

  $msgs_qtde=RetornaQtdeMsgsPeriodo($sock,$cod_curso,$cod_forum,$inicio,$fim,$agrupar);

  if (count($msgs_qtde)>0)
  {
     $max_qtde=0;
     foreach($msgs_qtde as $cod => $linha)
     {
       if ($exibir=="todos")
         $msgs_qtde[$cod]['qtde']=(int)$linha['qtde_total'];
       else if ($exibir=="formador")
         $msgs_qtde[$cod]['qtde']=(int)$linha['qtde_formador'];
       else if ($exibir=="aluno")
         $msgs_qtde[$cod]['qtde']=(int)$linha['qtde_aluno'];
       if ($max_qtde<$msgs_qtde[$cod]['qtde'])
         $max_qtde=$msgs_qtde[$cod]['qtde'];
     }

     if ($exibir=="todos")
       $classe="Todos";
     else if ($exibir=="aluno")
       $classe="Aluno";
     else if ($exibir=="formador")
       $classe="Formador";
 
     $total=0;

     if ($agrupar=="dia" && $apresentacao=="grafico")
     {
       // Fim Tabela Interna
       echo("      </table>\n");
       echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
       echo("        <tr class=\"head01\">\n");
       // 79 - Quantidade de Mensagens por Dia
       echo("          <td colspan=\"".(count($msgs_qtde)+1)."\">".RetornaFraseDaLista($lista_frases,79)."</td>\n");
       echo("        </tr>\n");
       echo("        <tr valign=\"bottom\" align=\"center\">\n");
       echo("          <td class=\"text\" valign=\"middle\" align=\"right\">&nbsp;</td>\n");
       foreach($msgs_qtde as $periodo => $linha)
       {
         $total+=$linha['qtde'];
         if ($linha['qtde']>0)
           $tam=$linha['qtde']*200/$max_qtde;
         else
           $tam=1;
         echo("          <td class=\"text\">".$linha['qtde']."<br>\n");
         echo("            <img src=\"figuras/".$exibir.".gif\" border=\"0\" width=\"5\" height=\"".$tam."\">\n");
         echo("          </td>\n");
       }
       echo("        </tr>\n");

       echo("        <tr valign=\"bottom\" align=\"center\">\n");
       // 21 - Dia
       echo("          <td class=\"text\" align=\"right\"><b>".RetornaFraseDaLista($lista_frases,21)."</b></td>\n");
       $number=0;
       foreach($msgs_qtde as $periodo => $linha)
       {
         $tmp=explode("/",$periodo);
         echo("          <td class=\"text\">&nbsp;".$tmp[0]."&nbsp;</td>\n");
         $number++;
       }
       echo("        </tr>\n");

       echo("        <tr valign=\"bottom\">\n");
       // 46 - M�s
       echo("          <td class=\"text\" align=\"right\"><b>".RetornaFraseDaLista($lista_frases,46)."</b></td>\n");

       $mes_anterior="-1";
       unset($meses);
       foreach($msgs_qtde as $periodo => $linha)
       {
         $tmp=explode("/",$periodo);
         if ($mes_anterior!=$tmp[1])
         {
            $mes_anterior=$tmp[1];
            $meses[$tmp[1]]['nome']=NomeMes((int)$tmp[1]);
            $meses[$tmp[1]]['num_dias']=1;
         }
         else
            $meses[$tmp[1]]['num_dias']++;
       }
       foreach($meses as $cod => $linha)
       {
         echo("          <td class=\"text\" colspan=\"".$linha['num_dias']."\">&nbsp;".$linha['nome']."&nbsp;</td>\n");
       }
       echo("        </tr>\n");
     }
     else if ($agrupar=="dia" && $apresentacao=="tabela")
     {
       echo("        <tr class=\"head01\">\n");
       // 21 - Dia
       echo("          <td>".RetornaFraseDaLista($lista_frases,21)."</td>\n");
       // 46 - M�s
       echo("          <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,46)."</td>\n");
       // 60 - Quantidade de Mensagens
       echo("          <td>".RetornaFraseDaLista($lista_frases,60)."</td>\n");
       echo("        </tr>\n");

       $i=1;
       foreach($msgs_qtde as $periodo => $linha)
       {
         $total+=$linha['qtde'];
         echo("        <tr>\n");
         $i = ($i + 1) % 2;
         $tmp=explode("/",$periodo);
         $nome=NomeMes((int)$tmp[1]);
         echo("          <td>".$tmp[0]."</td>\n");
         echo("          <td colspan=\"2\">".$nome."</td>\n");
         echo("          <td>".$linha['qtde']."</td>\n");
         echo("        </tr>\n");
       }
     }
     else if ($agrupar=="mes")
     {
       echo("        <tr class=\"head01\">\n");
       // 46 - M�s
       echo("          <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,46)."</td>\n");
       // 60 - Quantidade de Mensagens
       echo("          <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,60)."</td>\n");
       echo("        </tr>\n");

       $i=1;
       foreach($msgs_qtde as $periodo => $linha)
       {
         $total+=$linha['qtde'];
         echo("        <tr>\n");
         $tmp=explode("/",$periodo);
         $nome=NomeMes((int)$tmp[0]);
         echo("          <td colspan=\"2\">".$nome."</td>\n");

         if ($apresentacao=="grafico")
         {
           if ($linha['qtde']>0)
             $tam=$linha['qtde']*600/$max_qtde;
           else
             $tam=1;
           echo("          <td colspan=\"2\" align=\"left\">\n");
           echo("            <img src=\"figuras/".$exibir.".gif\" border=\"0\" width=\"".$tam."\" height=\"5\"> ".$linha['qtde']."\n");
           echo("          </td>\n");
         }
         else
         {
           $i = ($i + 1) % 2;
           echo("          <td colspan=\"2\">".$linha['qtde']."</td>\n");
         }
         echo("        </tr>\n");
       }
     }
     else if ($agrupar=="semana")
     {
       echo("        <tr class=\"head01\">\n");
       // 63 - Semana
       echo("          <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,63)."</td>\n");
       // 60 - Quantidade de Mensagens
       echo("          <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,60)."</td>\n");
       echo("        </tr>\n");

       $i=1;
       foreach($msgs_qtde as $periodo => $linha)
       {
         $total+=$linha['qtde'];
         if ($linha['qtde']>0)
           $tam=$linha['qtde']*600/$max_qtde;
         else
           $tam=1;

         $tmp1=explode("/",$periodo);
//         $mes1=NomeMes((int)$tmp1[1]);
         $mes1=$tmp1[1];
         $tmp2=explode("/",$linha['fim_periodo']);
//         $mes2=NomeMes((int)$tmp2[1]);
         $mes2=$tmp2[1];

         if ($data_invertida_g)
         {
           $mes1=$mes1."/".$tmp1[0];
           $mes2=$mes2."/".$tmp2[0];
         }
         else
         {
           $mes1=$tmp1[0]."/".$mes1;
           $mes2=$tmp2[0]."/".$mes2;
         }

         echo("        <tr>\n");
         echo("          <td colspan=\"2\">".$mes1."&nbsp;a&nbsp;".$mes2."</td>\n");
         if ($apresentacao=="grafico")
         {
           echo("          <td colspan=\"2\" align=\"left\">\n");
           echo("            <img src=\"figuras/".$exibir.".gif\" border=\"0\" width=\"".$tam."\" height=\"5\">&nbsp;".$linha['qtde']."\n");
           echo("          </td>\n");
         }
         else
         {
           $i = ($i + 1) % 2;
           echo("          <td colspan=\"2\">".$linha['qtde']."</td>\n");
         }
         echo("        </tr>\n");
       }
     }
     if ($agrupar=="dia" && $apresentacao=="grafico")
     {
       $number=$number+1;
       echo("        <tr class=\"head01\">\n");
       // 76 - Total de mensagens enviadas:
       echo("          <td colspan=\"".$number."\">".RetornaFraseDaLista($lista_frases,76)."&nbsp;<a>".$total."</a></td>\n");
       echo("        </tr>\n");
     }
     else
     {
       echo("        <tr class=\"head01\">\n");
       // 76 - Total de mensagens enviadas:
       echo("          <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,76)."&nbsp;<a>".$total."</a></td>\n");
       echo("        </tr>\n");
     }
  }
  else
  {
    echo("        <tr>\n");
    // 51 - Nenhuma mensagem enviada no per�odo selecionado!
    echo("          <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,51)."</td>\n");
    echo("        </tr>\n");
  }

  // Fim Tabela Interna
  echo("      </table>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabel�o
  echo("</table>\n");

  Desconectar($sock);

  echo("</body>\n");
  echo("</html>\n");

?>
