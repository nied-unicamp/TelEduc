<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/correio_mapa_interacao2.php

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
  ARQUIVO : cursos/aplic/intermap/correio_mapa_interacao2.php
  ========================================================== */

/* C�digo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("intermap.inc");
  include("correio.inc");

  $cod_ferramenta=19;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  echo("<script type=\"text/javascript\" language=javascript>\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  } \n");

  echo("  function OpenWindowPerfil(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function SalvarEmArquivo()\n");
  echo("  {\n");
  echo("    document.location='salvar_arquivo.php?cod_curso=".$cod_curso."&inicio=".$inicio."&fim=".$fim."&apresentacao=".$apresentacao."&cod_forum=".$cod_forum."&visualizar=".$visualizar."&todos=".$todos."&nome_arquivo=correio_mapa_interacao'");
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
  /* 15 - Correio */
  /* 38 - Mapa de Intera��o */
  echo(" - ".RetornaFraseDaLista($lista_frases,15)." - ".RetornaFraseDaLista($lista_frases,38)."</h4>\n");

  if ($apresentacao=="tabela")
    $cod_pagina=3;
  else
    $cod_pagina=2;

  echo("<br>\n");

  echo("<form name=mapa action=correio_mapa_interacao2.php target=Intermap method=get>\n");
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("<input type=hidden name=inicio value='".$inicio."'>\n");
  echo("<input type=hidden name=fim value='".$fim."'>\n");
  echo("<input type=hidden name=apresentacao value='".$apresentacao."'>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  echo("      <ul class=\"btAuxTabs\">\n");
  // 26 - Fechar
  echo("        <li><span title=\"Fechar\" onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases,26)."</span></li>\n");

  if ($apresentacao=="tabela")
  {
    /* 14 - Imprimir (geral) */
    echo("        <li><span title=\"Imprimir\" onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");

    if (!$SalvarEmArquivo)
    {
        /* 22 - Salvar Em Arquivo */
        echo("        <li><span title=\"Salvar em Arquivo\" onClick=\"SalvarEmArquivo();\">".RetornaFraseDaLista($lista_frases_geral,50)."</span></li>\n");
    }
  }
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td>\n");

  echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  // 58 - Per�odo:
  echo("          <td>".RetornaFraseDaLista($lista_frases,58)."</td>");

  if ($apresentacao=="tabela")
  {
    if (!isset($visualizar))
      $visualizar="participantes_todos";
    if (!$SalvarEmArquivo)
    {
      // 78 - Visualizar:
      echo("          <td width=35%>".RetornaFraseDaLista($lista_frases,78)."</td>\n");
      //
      echo("          <td width=35%>&nbsp;</td>\n");
    }
    else
    {
      // 78 - Visualizar:
      echo("          <td width=35%>".RetornaFraseDaLista($lista_frases,78)."</td>\n");
      //
      echo("          <td width=35%>&nbsp;</td>\n");
    }
  }
  echo("        </tr>\n");
  echo("        <tr>\n");

  // Per�odo
  echo("          <td>".$inicio." - ".$fim."</td>\n");

  if ($apresentacao=="tabela")
  {
    if (!$SalvarEmArquivo)
    {
      $checked="";
      if ($visualizar=="participantes_todos")
        $checked="checked";

      echo("          <td>\n");
      // 74 - Todos os participantes do curso
      echo("            <input type=radio ".$checked." class=g1field name=visualizar value=participantes_todos onClick=\"document.mapa.submit();\">".RetornaFraseDaLista($lista_frases,74)."<br>\n");

      $checked="";
      if ($visualizar=="participantes_mensagens")
        $checked="checked";

      // 56 - Participantes que enviaram mensagens
      echo("            <input type=radio ".$checked." class=g1field name=visualizar value=participantes_mensagens onClick=\"document.mapa.submit();\">".RetornaFraseDaLista($lista_frases,56)."<br>\n");
      echo("          </td>\n");

      $checked="";
      if ($mensagens_para_todos=="sim")
        $checked="checked";

      // 43 - Exibir mensagens enviadas para TODOS OS PARTICIPANTES
      echo("          <td><input type=checkbox name=mensagens_para_todos value=sim ".$checked." onClick=\"document.mapa.submit();\">".RetornaFraseDaLista($lista_frases,43)."</td>\n");
    }
    else
    {
      echo("          <td>\n");
      if ($visualizar=="participantes_todos")
        // 74 - Todos os participantes do curso
        echo("            ".RetornaFraseDaLista($lista_frases,74)."<br>\n");
      else
        // 56 - Participantes que enviaram mensagens
        echo("            ".RetornaFraseDaLista($lista_frases,56)."<br>\n");
      echo("          </td>\n");

      echo("          <td>\n");
      if ($mensagens_para_todos=="sim")
        // 43 - Mensagens enviadas para TODOS OS PARTICIPANTES
        echo("            ".RetornaFraseDaLista($lista_frases,43));
      echo("          </td>\n");
    }
  }
  echo("        </tr>\n");
  // Fim Tabela Interna
  echo("      </table>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabel�o
  echo("</table>\n");
  echo("</form>\n");

  if ($apresentacao=="grafo")
  {
    echo("<br>\n");

    // Define lista de usu�rios e c�digo m�ximo de usu�rio.

    $max_cod_usuario=0;
    $lista_usuarios=RetornaListaCodUsuarioNome($sock, $cod_curso);
    $str_usuarios="";
    if (count($lista_usuarios)>0)
    {
      foreach ($lista_usuarios as $cod_usu => $nome)
      {
        $str_usuarios.=$cod_usu.":".$nome."/";
        if ($cod_usu>$max_cod_usuario)
          $max_cod_usuario=$cod_usu;
      }

      // 72 - Todos
      $str_usuarios.="0:".RetornaFraseDaLista($lista_frases,72)."/";

    }
    $str_usuarios = str_replace("'", "", $str_usuarios);
    $str_usuarios = str_replace('"', "", $str_usuarios);

    // Lista de formadores. Informa ao Applet quais usuarios da lista acima sao formadores
    $lista_formadores=RetornaListaCodUsuarioFormador($sock, $cod_curso);
    $str_formadores="";
    if (count($lista_formadores)>0)
    {
      $str_formadores = implode("/", $lista_formadores);
    }

    // Lista de convidados. Informa ao Applet quais usu�rios sao convidados
    $lista_convidados = RetornaCodUsuarioConvidado($sock, $cod_curso);
    $str_convidados   = "";
    if (is_array($lista_convidados))
    {
      $str_convidados = implode("/", $lista_convidados);
    }

    // busca no BD a lista de msgs
    $lista_msgs_com_no_todos=RetornaMapaInteracaoPeriodo($sock, $inicio, $fim, true, ($visualizar == "participantes_todos") );
    $lista_msgs_sem_no_todos=RetornaMapaInteracaoPeriodo($sock, $inicio, $fim, false, ($visualizar == "participantes_todos") );

    unset($tmp);
    unset($tmp2);
    $str_msgs_com_no_todos = "";
    if (is_array($lista_msgs_com_no_todos))
    {
      foreach($lista_msgs_com_no_todos as $cod_usuario_emissor => $array_receptores)
      {
        if (is_array($array_receptores))
        {
          foreach($array_receptores as $cod_usuario_receptor => $qtde)
          {
            if ($qtde>0 && $cod_usuario_receptor!=$cod_usuario_emissor) {  // cancela auto-arestas
              $tmp2[] = $cod_usuario_receptor.",".$qtde;
            }
          }
          if (count($tmp2)>0) {
            $tmp[] = $cod_usuario_emissor.":".implode(".", $tmp2);
          }
          unset($tmp2);
        }
      }
      if(is_array($tmp))
        $str_msgs_com_no_todos = implode("/", $tmp);
    }

    unset($tmp);
    unset($tmp2);
    $str_msgs_sem_no_todos = "";
    if (is_array($lista_msgs_sem_no_todos))
    {
      foreach($lista_msgs_sem_no_todos as $cod_usuario_emissor => $array_receptores)
      {
        if (is_array($array_receptores))
        {
          foreach($array_receptores as $cod_usuario_receptor => $qtde)
          {
            if ($qtde>0 && $cod_usuario_receptor!=$cod_usuario_emissor) {  // cancela auto-arestas
              $tmp2[] = $cod_usuario_receptor.",".$qtde;
            }
          }
          if (count($tmp2)>0) {
            $tmp[] = $cod_usuario_emissor.":".implode(".", $tmp2);
          }
          unset($tmp2);
        }
      }
      if(is_array($tmp))
        $str_msgs_sem_no_todos = implode("/", $tmp);
    }
    $str_usuarios = str_replace("'", "", $str_usuarios);
    $str_usuarios = str_replace('"', "", $str_usuarios);

    // Determina se o usu�rio em quest�o pode ou n�o acessar a ferramenta Perfil.
    if (TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,13)) {
      $mostrar_link_perfil = "sim";
    } else {
      $mostrar_link_perfil = "nao";
    }


    $textos=RetornaFraseDaLista($lista_frases,92)."/".RetornaFraseDaLista($lista_frases,93)."/";
    $textos.=RetornaFraseDaLista($lista_frases,103)."/".RetornaFraseDaLista($lista_frases,117)."/";
    $textos.=RetornaFraseDaLista($lista_frases,86)."/".RetornaFraseDaLista($lista_frases,72)."/";
    $textos.=RetornaFraseDaLista($lista_frases,89)."/".RetornaFraseDaLista($lista_frases,90)."/";
    $textos.=RetornaFraseDaLista($lista_frases,88)."/".RetornaFraseDaLista($lista_frases,85)."/";
    $textos.=RetornaFraseDaLista($lista_frases,97)."/".RetornaFraseDaLista($lista_frases,98)."/";
    $textos.=RetornaFraseDaLista($lista_frases,99)."/".RetornaFraseDaLista($lista_frases,100)."/";
    $textos.=RetornaFraseDaLista($lista_frases,101)."/".RetornaFraseDaLista($lista_frases,102)."/";
    $textos.=RetornaFraseDaLista($lista_frases,74)."/".RetornaFraseDaLista($lista_frases,67)."/";
    $textos.=RetornaFraseDaLista($lista_frases,56)."/".RetornaFraseDaLista($lista_frases,91)."/";
    $textos.=RetornaFraseDaLista($lista_frases,82)."/".RetornaFraseDaLista($lista_frases,81)."/";
    $textos.=RetornaFraseDaLista($lista_frases,83)."/".RetornaFraseDaLista($lista_frases,94)."/";
    $textos.=RetornaFraseDaLista($lista_frases,95)."/";
    $textos.=RetornaFraseDaLista($lista_frases,96)."/";
    $textos.=RetornaFraseDaLista($lista_frases,104)."/";
    $textos.=RetornaFraseDaLista($lista_frases,105)."/".RetornaFraseDaLista($lista_frases,106)."/";
    $textos.=RetornaFraseDaLista($lista_frases,107)."/".RetornaFraseDaLista($lista_frases,108)."/";
    $textos.=RetornaFraseDaLista($lista_frases,109)."/".RetornaFraseDaLista($lista_frases,110)."/";
    $textos.=RetornaFraseDaLista($lista_frases,111)."/".RetornaFraseDaLista($lista_frases,112)."/";
    $textos.=RetornaFraseDaLista($lista_frases,113)."/".RetornaFraseDaLista($lista_frases,114)."/";
    $textos.=RetornaFraseDaLista($lista_frases,115)."/".RetornaFraseDaLista($lista_frases,116);
										 

    // Invoca o applet.
    echo("<center>\n");
    echo("<applet codebase='grafo/' code='applet/GrafoApplet.class' width='100%' height='550px' >");

    /* ?? - Seu navegador n�o permite a execu��o de aplicativos em Java. Visite o site www.java.com para obter informa��es sobre como fazer que seu navegador seja compat�vel com essa tecnologia.
    /* OBSERVA��O IMPORTANTE: REGISTRAR ESSA FRASE TAMB�M

    echo("    alt=\"Seu navegador n�o permite a execu��o de aplicativos em Java. Visite o site www.java.com para obter informa��es sobre como fazer que seu navegador seja compat�vel com essa tecnologia.\" >\n");
    echo("  <param name='progressbar' value='true'>\n");
    /* ?? - Fazendo download do programa. Aguarde... */
    echo("  <param name='boxmessage' value='Fazendo download do programa. Aguarde...'>\n");
    echo("  <param name='boxbgcolor' value='#f0f0f0'>\n");
    echo("  <param name='boxfgcolor' value='#000000'>\n");
    echo("  <param name='progresscolor' value='#000080'>\n");
    echo("  <param name='cod_curso' value='".$cod_curso."'>\n");
    echo("  <param name='mostrar_link_perfil' value='".$mostrar_link_perfil."'>\n");
    echo("  <param name='textos' value=\"".$textos."\">\n");
    echo("  <param name='codigo_nome' value='-1:Administrador/".$str_usuarios."'>\n");
    echo("  <param name='codigo_maximo' value='".$max_cod_usuario."'>\n");
    echo("  <param name='arestas_com_no_todos' value='".$str_msgs_com_no_todos."'>\n");
    echo("  <param name='arestas_sem_no_todos' value='".$str_msgs_sem_no_todos."'>\n");
    echo("  <param name='formador' value='".$str_formadores."'>\n");
    echo("  <param name='convidado' value='".$str_convidados."'>\n");
    echo("</applet>\n");
    echo("</center>\n");

  }
  else  // apresentacao=="tabela"
  {
    /* <!----------------- Tabelao -----------------> */
    echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("        <tr class=\"head\">\n");
    // 13 - Autor da Mensagem
    echo("          <td>".RetornaFraseDaLista($lista_frases,13)."</td>\n");
    // 20 - Destinat�rio da Mensagem
    echo("          <td width=35%>".RetornaFraseDaLista($lista_frases,20)."</td>\n");
    // 59 - Quantidade
    echo("          <td width=35%>".RetornaFraseDaLista($lista_frases,59)."</td>\n");
    echo("        </tr>\n");

    $lista_usuarios=RetornaListaCodUsuarioNome($sock, $cod_curso);
    $lista_formadores=RetornaListaCodUsuarioFormador($sock, $cod_curso);
    $lista_msgs=RetornaMapaInteracaoPeriodo($sock,$inicio,$fim,($mensagens_para_todos == "sim"),($visualizar == "participantes_todos"));

    if (count($lista_usuarios)>0)
    {
      $cod_anterior=-1;
$pif=1;
$sif=1;
$tif=1;
      foreach ($lista_usuarios as $cod_usu => $nome)
      {
        if (is_array($lista_msgs[$cod_usu]))
        {
          foreach($lista_msgs[$cod_usu] as $cod_usu_rec => $qtde)
          {
            echo("        <tr>\n");
//             if ($cod_usu!=$cod_anterior){echo $cod_usu."=".$cod_anterior;
              echo("          <td>".MontaLinkPerfil($cod_usu,$nome)."</td>");
            if ($cod_usu_rec != 0){
              echo("          <td>".MontaLinkPerfil($cod_usu_rec,$lista_usuarios[$cod_usu_rec])."</td>\n");}
            else{
              // 72 - Todos
              echo("          <td>".RetornaFraseDaLista($lista_frases, 72)."</td>\n");}
            echo("          <td>".$qtde."</td>\n");
            echo("        </tr>\n");
            $cod_anterior=$cod_usu;
          }
        }
        else if ($visualizar=="participantes_todos")
        {
            echo("        <tr>\n");
            if ($cod_usu!=$cod_anterior)
              echo("          <td>".MontaLinkPerfil($cod_usu,$nome)."</td>");
            echo("          <td>-</td>\n");
            echo("          <td>0</td>\n");
            echo("        </tr>\n");
            $cod_anterior=$cod_usu;
        }
      }
      // Fim Tabela Interna
      echo("      </table>\n");
    }
    echo("    </td>\n");
    echo("  </tr>\n");
    // Fim Tabel�o
    echo("</table>\n");
  }

  Desconectar($sock);

  echo("</body>\n");
  echo("</html>\n");

?>
