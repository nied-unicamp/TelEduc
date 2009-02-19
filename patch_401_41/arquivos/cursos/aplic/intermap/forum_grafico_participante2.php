<?
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

  if (!isset($limite_baixo) || !isset($limite_alto))
  {
    $limite_baixo=2;
    $limite_alto=6;
  }

  echo("<script language=javascript>\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  } \n");

  echo("  function OpenWindowPerfil(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function SalvarEmArquivo()\n");
  echo("  {\n");
  echo("    document.location='salvar_arquivo.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&todos=".$todos."&apresentacao=".$apresentacao."&cod_forum=".$cod_forum."&cod_usu=".$cod_usu."&inicio=".$inicio."&fim=".$fim."&agrupar=".$agrupar."&nome_arquivo=forum_grafico_participante'");
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
    /* 32 - Gr�fico por Participante */
    echo(" - ".RetornaFraseDaLista($lista_frases,32)."</h4>\n");
  else
    /* 69 - Tabela por Participante */
    echo(" - ".RetornaFraseDaLista($lista_frases,69)."</h4>\n");

  if ($apresentacao=="tabela")
    $cod_pagina=18;
  else
    $cod_pagina=17;

  echo("<br>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  echo("      <ul class=\"btAuxTabs\">\n");
  // 26 - Fechar
  echo("        <li><span title=\"Fechar\" onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases,26)."</span></li>\n");
  /* 14 - Imprimir (geral) */
  echo("        <li><span title=\"Imprimir\" onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");

  if (!isset($SalvarEmArquivo) && $apresentacao=="tabela")
  {
    /* 22 - Salvar Em Arquivo */
    echo("       <li><span title=\"Salvar em Arquivo\" onClick=\"SalvarEmArquivo();\">".RetornaFraseDaLista($lista_frases_geral,50)."</span></li>\n");
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
  // 55 - Participante(s):
  echo("          <td>".RetornaFraseDaLista($lista_frases,55)."</td>\n");
  // 58 - Per�odo:
  echo("          <td>".RetornaFraseDaLista($lista_frases,58)."</td>\n");
  // 5 - Agrupado por:
  echo("          <td>".RetornaFraseDaLista($lista_frases,5)."</td>\n");
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
  $lista_usuarios=RetornaListaCodUsuarioNome($sock, $cod_curso);
  if ((int)$cod_usu==-1)
  {
    // 72 - Todos
    echo("          <td>".RetornaFraseDaLista($lista_frases,72)."</td>\n");
  }
  else
  {
    echo("          <td>".MontaLinkPerfil($cod_usu,$lista_usuarios[$cod_usu])."</td>\n");
  }
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
  echo("        </tr>\n");

  $lista_formadores=RetornaListaCodUsuarioFormador($sock, $cod_curso);

  if ($cod_usu>=0)
  {
    $msgs_qtde=RetornaQtdeMsgsPeriodoUsuario($sock,$cod_forum,$inicio,$fim,$agrupar,$cod_usu);

    if (count($msgs_qtde)>0)
    {
       $max_qtde=0;
       foreach($msgs_qtde as $cod => $linha)
       {
         if ($max_qtde<$msgs_qtde[$cod]['qtde'])
           $max_qtde=$msgs_qtde[$cod]['qtde'];
       }

       if ($lista_formadores[$cod_usu]==$cod_usu)
       {
         $exibir="formador";
         $classe="Formador";
       }
       else
       {
         $exibir="aluno";
         $classe="Aluno";
       }

       $total=0;

       if ($agrupar=="dia" && $apresentacao=="grafico")
       {
         // Fim Tabela Interna
         echo("      </table>\n");
         echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
         // 79 - Quantidade de Mensagens por Dia
         echo("        <tr class=\"head01\"><td colspan=".(count($msgs_qtde)+1).">".RetornaFraseDaLista($lista_frases,79)."</td>\n");
         echo("        <tr valign=bottom align=center>\n");
         echo("          <td class=text valign=middle align=right>&nbsp;</td>\n");
         foreach($msgs_qtde as $periodo => $linha)
         {
           $total+=$linha['qtde'];
           if ($linha['qtde']>0)
             $tam=$linha['qtde']*200/$max_qtde;
           else
             $tam=1;
           echo("          <td class=text>".$linha['qtde']."<br><img src=figuras/".$exibir.".gif border=0 width=5 height=".$tam."></td>\n");
         }
         echo("        </tr>\n");
         echo("        <tr valign=bottom align=center>\n");
         // 21 - Dia
         echo("          <td class=text align=right><b>".RetornaFraseDaLista($lista_frases,21)."</b></td>\n");
         $number=0;
         foreach($msgs_qtde as $periodo => $linha)
         {
           $tmp=explode("/",$periodo);
           echo("          <td class=text>&nbsp;".$tmp[0]."&nbsp;</td>\n");
           $number++;
         }
         echo("        </tr>\n");
         echo("  <tr valign=bottom>\n");
         // 46 - M�s
         echo("          <td class=text align=right><b>".RetornaFraseDaLista($lista_frases,46)."</b></td>\n");

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
           echo("          <td class=text colspan=".$linha['num_dias'].">&nbsp;".$linha['nome']."&nbsp;</td>\n");
         }
         echo("        </tr>\n");
       }
       else if ($agrupar=="dia" && $apresentacao=="tabela")
       {
         echo("        <tr class=\"head01\">\n");
         // 21 - Dia
         echo("          <td>".RetornaFraseDaLista($lista_frases,21)."</td>\n");
         // 46 - M�s
         echo("          <td colspan=2>".RetornaFraseDaLista($lista_frases,46)."</td>\n");
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
           echo("          <td colspan=2>".$nome."</td>\n");
           echo("          <td>".$linha['qtde']."</td>\n");
           echo("        </tr>\n");
         }
       }
       else if ($agrupar=="mes")
       {
         echo("        <tr class=\"head01\">\n");
         // 46 - M�s
         echo("          <td colspan=2>".RetornaFraseDaLista($lista_frases,46)."</td>\n");
         // 60 - Quantidade de Mensagens
         echo("          <td colspan=2>".RetornaFraseDaLista($lista_frases,60)."</td>\n");
         echo("        </tr>\n");

         $i=1;
         foreach($msgs_qtde as $periodo => $linha)
         {
           $total+=$linha['qtde'];
           echo("        <tr>\n");
           $tmp=explode("/",$periodo);
           $nome=NomeMes((int)$tmp[0]);
           echo("          <td colspan=2>".$nome."</td>\n");

           if ($apresentacao=="grafico")
           {
             if ($linha['qtde']>0)
               $tam=$linha['qtde']*600/$max_qtde;
             else
               $tam=1;
             echo("          <td colspan=2 align=left><img src=figuras/".$exibir.".gif border=0 width=".$tam." height=5> ".$linha['qtde']."</td>\n");
           }
           else
           {
             $i = ($i + 1) % 2;
             echo("          <td colspan=2>".$linha['qtde']."</td>\n");
           }
           echo("        </tr>\n");
         }
       }
       else if ($agrupar=="semana")
       {
         echo("        <tr class=\"head01\">\n");
         // 63 - Semana
         echo("          <td colspan=2>".RetornaFraseDaLista($lista_frases,63)."</td>\n");
         // 60 - Quantidade de Mensagens
         echo("          <td colspan=2>".RetornaFraseDaLista($lista_frases,60)."</td>\n");
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
           $mes1=$tmp1[1];
           $tmp2=explode("/",$linha['fim_periodo']);
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
           echo("          <td colspan=2>".$mes1."&nbsp;a&nbsp;".$mes2."</td>\n");
           if ($apresentacao=="grafico")
           {
             echo("          <td colspan=2 align=left><img src=figuras/".$exibir.".gif border=0 width=".$tam." height=5>&nbsp;".$linha['qtde']."</td>\n");
           }
           else
           {
             $i = ($i + 1) % 2;
             echo("          <td colspan=2>".$linha['qtde']."</td>\n");
           }
           echo("        </tr>\n");
         }
       }
       if ($agrupar=="dia" && $apresentacao=="grafico")
       {
         $number=$number+1;
         echo("        <tr class=\"head01\">\n");
         // 76 - Total de mensagens enviadas:
         echo("          <td colspan=".$number.">".RetornaFraseDaLista($lista_frases,76)."&nbsp;<a>".$total."</a></td>\n");
         echo("        </tr>\n");
       }
       else
       {
         echo("        <tr class=\"head01\">\n");
         // 76 - Total de mensagens enviadas:
         echo("          <td colspan=4>".RetornaFraseDaLista($lista_frases,76)."&nbsp;<a>".$total."</a></td>\n");
         echo("        </tr>\n");
       }
    }
    else
    {
      echo("        <tr>\n");
      // 51 - Nenhuma mensagem enviada no per�odo selecionado!
      echo("          <td>".RetornaFraseDaLista($lista_frases,51)."</td>\n");
      echo("        </tr>\n");
    }
  }
  else  // Todos os participantes: cod_usu=-1
  {
    $legenda="sim";

    $usu_msgs_qtde=RetornaQtdeMsgsPeriodoListaUsuarios($sock,$cod_forum,$inicio,$fim,$agrupar,$lista_usuarios);
    $primeira_linha=true;

    echo("          </td>\n");
    echo("        </tr>\n");
    // Fim Tabela Interna
    echo("      </table>\n");

    if (count($usu_msgs_qtde)>0)
    {
      $i=1;
      echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
      foreach($usu_msgs_qtde as $cod_usu => $msgs_qtde)
      {
        if ($primeira_linha)
        {
          $primeira_linha=false;

          if ($agrupar=="dia")
          {
            // Exibindo o nome dos meses

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
           echo("        <tr class=\"head\">\n");
           echo("          <td>&nbsp;</td>\n");
           foreach($meses as $cod => $linha)
           {
             echo("          <td colspan=".$linha['num_dias'].">".$linha['nome']."</td>\n");
           }
            echo("          <td>&nbsp;</td>\n");
            echo("        </tr>\n");
          }
          // 54 - Participante
          echo("        <tr class=\"head01\">\n");
          echo("          <td>".RetornaFraseDaLista($lista_frases,54)."</td>\n");
          foreach ($msgs_qtde as $periodo => $linha)
          {
            if ($agrupar=="dia")
            {
              $tmp=explode("/",$periodo);
              $data=$tmp[0];
            } 
            else if ($agrupar=="mes")
            {
              $tmp=explode("/",$periodo);
              $data=NomeMes($tmp[0]);
            } 
            else if ($agrupar=="semana")
            {
              $tmp1=explode("/",$periodo);
              $tmp2=explode("/",$linha['fim_periodo']);
              $mes1=$tmp1[1];
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
              // 4 - a
              $data=$mes1." ".RetornaFraseDaLista($lista_frases,4)." ".$mes2;
            }

            echo("          <td>".$data."</td>\n");
          }
          // 75 - Total
          echo("          <td>".RetornaFraseDaLista($lista_frases,75)."</td>\n");
          echo("        </tr>\n");
        }
        $total=0;
        echo("        <tr>\n");
        echo("          <td>".MontaLinkPerfil($cod_usu,Space2Nbsp($lista_usuarios[$cod_usu]))."</td>\n");
        $j = 0;
        foreach($msgs_qtde as $periodo => $linha)
        {
          $j = ($j + 2) % 4;
          if ($j == 2)
            $classe="class=i".($i+3)."field";
          else
            $classe="";

          if ($linha['qtde']>0)
          {
            $total += $linha['qtde'];
            if ($apresentacao=="tabela")
              echo("          <td>".$linha['qtde']."</td>\n");
            else
            {
              if ($linha['qtde']<=$limite_baixo)
                echo("          <td><img src=figuras/poucas.gif border=0></td>\n");
              else if ($linha['qtde']<=$limite_alto)
                echo("          <td><img src=figuras/medias.gif border=0></td>\n");
              else
                echo("          <td><img src=figuras/muitas.gif border=0></td>\n");
            }
          }
          else
            echo("          <td>&nbsp;</td>\n");
        }
        $j = ($j + 2) % 4;
        if ($j == 2)
          $classe="class=i".($i+3)."field";
        else
          $classe="";

        echo("          <td>".$total."</td>\n");
        echo("        </tr>\n");
        $i = ($i + 1) % 2;

      }
    }
  }

  // Fim Tabela Interna
  echo("      </table>\n");
  echo("    </td>\n");
  echo("  </tr>\n");

  if ($apresentacao=="grafico" && $legenda=="sim")
  {
    echo("  <tr>\n");
    echo("    <td>\n");
    // 36 - Legenda:
    echo("      <b>".RetornaFraseDaLista($lista_frases,36)."</b>\n");
    // 42 - mensagens enviadas
    echo("      &nbsp;&nbsp;&nbsp;&nbsp;<img src=figuras/poucas.gif border=0> 1 - ".$limite_baixo." ".RetornaFraseDaLista($lista_frases,42));
    // 42 - mensagens enviadas
    echo("      &nbsp;&nbsp;&nbsp;&nbsp;<img src=figuras/medias.gif border=0> ".($limite_baixo+1)." - ".$limite_alto." ".RetornaFraseDaLista($lista_frases,42));
    // 37 - Mais de
    // 42 - mensagens enviadas
    echo("      &nbsp;&nbsp;&nbsp;&nbsp;<img src=figuras/muitas.gif border=0> ".RetornaFraseDaLista($lista_frases,37)." ".$limite_alto." ".RetornaFraseDaLista($lista_frases,42));
    echo("    </td>\n");
    echo("  </tr>\n");
  }

  // Fim Tabel�o
  echo("</table>\n");

  Desconectar($sock);

  echo("</body>\n");
  echo("</html>\n");

?>
