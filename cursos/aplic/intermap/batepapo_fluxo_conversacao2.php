<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/batepapo_fluxo_conversacao2.php

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
  ARQUIVO : cursos/aplic/intermap/correio_fluxo_conversacao2.php
  ========================================================== */

/* C�digo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("intermap.inc");
  include("batepapo.inc");

  $cod_ferramenta=19;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  echo("<script language=javascript>\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  } \n");

  echo("  function OpenWindowPerfil(funcao)\n");
  echo("  {\n");
  echo("    window.open('../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]='+funcao,'PerfilDisplay','width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function AbreMensagem(usuario,data)\n");
  echo("  {\n");
  // echo("    window.open('batepapo_mensagem.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_sessao=".$cod_sessao."&cod_usu='+usuario+'&data='+data,'DisplayMensagem','width=600,height=200,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("    window.open('batepapo_mensagem.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_sessao=".$cod_sessao."&cod_usu='+usuario+'&data='+data,'DisplayMensagem','width=600,height=200,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("    return(false);\n");
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
  /* 14 - Bate-papo */
  echo(" - ".RetornaFraseDaLista($lista_frases,14));
  /* 27 - Fluxo de Conversa��o */
  echo(" - ".RetornaFraseDaLista($lista_frases,27)."</h4>\n");

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
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td>\n");

  $sessao=RetornaSessao($sock,$cod_sessao);

  echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  // 64 - Sess�o:
  echo("          <td colspan=2>".RetornaFraseDaLista($lista_frases,64)."</td>\n");
  echo("        </tr>\n");
  echo("        <tr>\n");
  echo("          <td colspan=2>\n");
  echo(UnixTime2Data($sessao['DataInicio']));
  // 16 - das
  echo(" - ".RetornaFraseDaLista($lista_frases,16)." ");
  echo(UnixTime2Hora($sessao['DataInicio']));
  // 8 - as
  echo(" ".RetornaFraseDaLista($lista_frases,8)." ");
  echo(UnixTime2Hora($sessao['DataFim']));
  echo(" - ");
  echo($sessao['Assunto']);
  echo("          </td>\n");
  echo("        </tr>\n");

  $lista_usuarios=RetornaListaApelidos($sock,$cod_sessao);

  echo("        <tr class=\"head01\">\n");
  // Participante
  echo("          <td width=50%>Participante</td>\n");
  // Apelido
  echo("          <td width=50%>Apelido</td>\n");
  echo("        </tr>\n");

  if (count($lista_usuarios)>0)
  {
    $lista_nomes_usuarios=RetornaTodosUsuarios($sock, $cod_curso);
    foreach($lista_usuarios as $cod_usu => $apelido)
    {
      echo("        <tr>\n");
      echo("          <td><a href=# onClick=return(OpenWindowPerfil(".$cod_usu."));>".$lista_nomes_usuarios[$cod_usu]."</a></td>\n");
      echo("          <td>".$apelido."</td>\n");
      echo("        </tr>\n");
    }
  }

  // Fim Tabela Interna
  echo("      </table>\n");

  if (count($lista_usuarios)>0)
  {
    $lista_formadores = RetornaListaCodUsuarioFormador($sock, $cod_curso);
    $lista_convidados = RetornaCodUsuarioConvidado($sock, $cod_curso);
    $lista_visitantes = RetornaVisitantes($sock, $cod_curso);
    $fluxo_msgs=RetornaFluxoMensagens($sock,$cod_sessao);

    // Cria a barra temporal que acompanha o fluxo de conversa��o
    // Ajuste a variavel dif_tempo para determinar o tempo desejado
    $dif_tempo=120;

    $inicio=$sessao['DataInicio'];
    $fim=$sessao['DataFim']+4;

    $numero=1;
    $inicio=$sessao['DataInicio']-3;
    $fluxo=$fluxo_msgs[$cod_usu];
    if (count($fluxo)>0)
    {
      foreach($fluxo as $data => $cod_fala)
      {
        $numero++;
      }
    }

    echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("        <tr class=\"head\">\n");
    echo("          <td colspan=".$numero.">Mensagens dos Participantes</td>\n");
    echo("        </tr>");
    echo("        <tr class=\"head01\">\n");
    foreach($lista_usuarios as $cod_usu => $apelido)
    {
      echo("          <td colspan=".$numero."><a href=# onClick=return(OpenWindowPerfil(".$cod_usu."));>".$apelido."</a></td>\n");
    }
    echo("        </tr>\n");
    echo("        <tr>\n");

    // Monta a barra do fluxo de conversa��o de um dado usu�rio
    if ($lista_formadores[$cod_usu]==$cod_usu)
      $imagem="figuras/formador.gif";
    else if ($lista_convidados[$cod_usu] == $cod_usu)
      $imagem = "figuras/convidado.jpeg";
    else if ($lista_visitantes[$cod_usu])
      $imagem = "figuras/visitante.jpeg";
    else
      $imagem="figuras/aluno.gif";

     $inicio=$sessao['DataInicio']-3;
     $fluxo=$fluxo_msgs[$cod_usu];
     if (count($fluxo)>0)
     {
       $online=false;
       foreach($fluxo as $data => $cod_fala)
       {
         if ($data-$inicio-1>0)
         {
           echo("          <td><a href=# onClick=return(AbreMensagem(".$cod_usu.",".$data."));><img src=".$imagem." width=16 height=6 border=0></a></td>");
           $inicio=$data+5;
         }
         if ($cod_fala<0)
           $online=false;
         else
           $online=true;
       }
     }
    echo("        </tr>\n");

//     foreach($lista_usuarios as $cod_usu => $apelido)
//     {
//       // Monta a barra do fluxo de conversa��o de um dado usu�rio
//       echo("  <td class=text>\n");
//       if ($lista_formadores[$cod_usu]==$cod_usu)
//         $imagem="figuras/formador.gif";
//       else if ($lista_convidados[$cod_usu] == $cod_usu)
//         $imagem = "figuras/convidado.jpeg";
//       else if ($lista_visitantes[$cod_usu])
//         $imagem = "figuras/visitante.jpeg";
//       else
//         $imagem="figuras/aluno.gif";
// 
//       echo("    &nbsp;<a href=# onClick=return(OpenWindowPerfil(".$cod_usu."));>".$apelido."</a>&nbsp;\n");
// 
//       $inicio=$sessao['DataInicio']-3;
//       $fluxo=$fluxo_msgs[$cod_usu];
//       if (count($fluxo)>0)
//       {
//         $online=false;
//         echo("<table cellpadding=0 cellspacing=0 border=0 width=100%>");
//         foreach($fluxo as $data => $cod_fala)
//         {
//           if ($data-$inicio-1>0)
//           {
//             if ($online)
//               echo("<tr><td align=center><img src=".$imagem." width=4 height=".($data-$inicio-1)." border=0></td></tr>");
//             else
//               echo("<tr><td align=center><img src=figuras/invisivel.gif width=4 height=".($data-$inicio-1)." border=0></td></tr>");
// 
//             echo("<tr><td align=center><a href=# onClick=return(AbreMensagem(".$cod_usu.",".$data."));><img src=".$imagem." width=16 height=6 border=0></a></td></tr>");
// 
//             $inicio=$data+5;
//           }
//           if ($cod_fala<0)
//             $online=false;
//           else
//             $online=true;
// 
//         }
//         echo("</table>\n");
//       }
// 
//       echo("  </td>\n");
// 
//       // Cria a barra temporal que acompanha o fluxo de conversa��o
//       
//       $inicio=$sessao['DataInicio'];
//       $fim=$sessao['DataFim']+4;
//       echo("  <td><br>\n");
//       echo("<table cellpadding=0 cellspacing=0 border=0 width=100%>");
//       echo("<tr><td align=center><img src=figuras/invisivel.gif width=1 height=3 border=0></td></tr>");
//       echo("<tr><td align=center><img src=figuras/todos.gif width=5 height=1 border=0></td></tr>");
//       while($inicio+$dif_tempo<$fim)
//       {
//         echo("<tr><td align=center><img src=figuras/todos.gif width=1 height=".($dif_tempo-1)." border=0></td></tr>");
//         echo("<tr><td align=center><img src=figuras/todos.gif width=5 height=1 border=0></td></tr>");
//         $inicio+=$dif_tempo;
//       }
//       echo("<tr><td align=center><img src=figuras/todos.gif width=1 height=".($fim-$inicio)." border=0></td></tr>");
// 
//       echo("</table>\n");
//       echo("  </td>\n");     
// 
// 
//     }

    // Fim Tabela Interna
    echo("      </table>\n");

  }
  else
  {
    echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("        <tr>\n");
    // 49 - Nenhum aluno ou formador participou desta sess�o!
    echo("          <td>".RetornaFraseDaLista($lista_frases,49)."</td>\n");
    echo("        </tr>\n");
    // Fim Tabela Interna
    echo("      </table>\n");
  }

  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  // 36 - Legenda
  echo("      <b>".RetornaFraseDaLista($lista_frases,36)."</b>\n");
  // 81 - Formador
  echo("      &nbsp;&nbsp;&nbsp;&nbsp;<img src=figuras/formador.gif width=9 height=9 border=0> - ".RetornaFraseDaLista($lista_frases,81));
  // 82 - Aluno
  echo("      &nbsp;&nbsp;&nbsp;&nbsp;<img src=figuras/aluno.gif width=9 height=9 border=0> - ".RetornaFraseDaLista($lista_frases,82));
  // 83 - Convidado
  echo("      &nbsp;&nbsp;&nbsp;&nbsp;<img src=figuras/convidado.jpeg width=9 height=9 border=0> - ".RetornaFraseDaLista($lista_frases,83));
  // 86 - Visitantes
  echo("      &nbsp;&nbsp;&nbsp;&nbsp;<img src=figuras/visitante.jpeg width=9 height=9 border=0> - ".RetornaFraseDaLista($lista_frases,86));
  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabel�o
  echo("</table>\n");

  Desconectar($sock);

  echo("</body>\n");
  echo("</html>\n");

?>
