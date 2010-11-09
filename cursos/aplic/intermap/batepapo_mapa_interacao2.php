<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/batepapo_mapa_interacao2.php

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
  ARQUIVO : cursos/aplic/intermap/batepapo_mapa_interacao2.php
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
  echo("    window.open(\"../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function SalvarEmArquivo()\n");
  echo("  {\n");
  echo("    document.location='salvar_arquivo.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&inicio=".$inicio."&fim=".$fim."&apresentacao=".$apresentacao."&cod_sessao=".$cod_sessao."&todos=".$todos."&visualizar=".$visualizar."&nome_arquivo=batepapo_mapa_interacao'");
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
  /* 38 - Mapa de Intera��o */
  echo(" - ".RetornaFraseDaLista($lista_frases,14)." - ".RetornaFraseDaLista($lista_frases,38)."</h4>\n");

  if ($apresentacao=="tabela")
    $cod_pagina=24;
  else
    $cod_pagina=23;

  echo("<br>\n");

  echo("<form name=mapa action=batepapo_mapa_interacao2.php target=Intermap method=get>\n");
  //echo(RetornaSessionIDInput()."\n");
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("<input type=hidden name=cod_sessao value='".$cod_sessao."'>\n");
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

  $sessao=RetornaSessao($sock,$cod_sessao);

  echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  // 64 - Sess�o:
  echo("          <td>".RetornaFraseDaLista($lista_frases,64)."</td>\n");

  if ($apresentacao=="tabela")
  {
    if (!$SalvarEmArquivo)
    {
      // 78 - Visualizar:
      echo("          <td width=30%>".RetornaFraseDaLista($lista_frases,78)."</td>\n");
      // &nbsp;
      echo("          <td width=35%>&nbsp;</td>\n");
    }
    else
    {
      // 78 - Visualizar:
      echo("          <td colspan=2>".RetornaFraseDaLista($lista_frases,78)."</td>\n");
    }
  }
  echo("        </tr>\n");

  echo("        <tr>\n");
  echo("          <td>\n");
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

  if ($apresentacao=="tabela")
  {
    if (!$SalvarEmArquivo)
    {
      echo("          <td>\n");
      $checked="";
      if ($visualizar=="todos")
        $checked="checked";

      // 74 - Todos os participantes do curso
      echo("            <input type=radio ".$checked." class=g1field name=visualizar value=todos onClick=\"document.mapa.submit();\">".RetornaFraseDaLista($lista_frases,74)."<br>\n");
      $checked="";
      if ($visualizar=="sessao")
        $checked="checked";

      // 87 - Todos os participantes da sess�o
      echo("            <input type=radio ".$checked." class=g1field name=visualizar value=sessao onClick=\"document.mapa;submit();\">".RetornaFraseDaLista($lista_frases,87)."<br>\n");
      $checked="";
      if ($visualizar=="mensagens")
        $checked="checked";

      // 56 - Participantes que enviaram mensagens
      echo("            <input type=radio ".$checked." class=g1field name=visualizar value=mensagens onClick=\"document.mapa.submit();\">".RetornaFraseDaLista($lista_frases,56)."<br>\n");
      echo("          </td>\n");

      echo("          <td>\n");
      $checked="";
      if ($todos=="sim")
        $checked="checked";
      // 43 - Mensagens enviadas para TODOS OS PARTICIPANTES
      echo("            <input type=checkbox name=todos value=sim ".$checked." onClick=\"document.mapa.submit();\"><font class=text>".RetornaFraseDaLista($lista_frases,43)."</font>\n");
      echo("          </td>\n");
    }
    else
    {
      echo("          <td>\n");

      if ($visualizar=="todos")
        // 74 - Todos os participantes do curso
        echo("            ".RetornaFraseDaLista($lista_frases,74));
      else if ($visualizar == "sessao")
        // 87 - Todos os participantes da sessao
        echo("            ".RetornaFraseDaLista($lista_frases,87));
      else
        // 56 - Participantes que enviaram mensagens
        echo("            ".RetornaFraseDaLista($lista_frases,56));

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
    // $lista_usuarios=RetornaTodosUsuarios($sock); // <-------- ERRO
    $lista_usuarios=RetornaListaCodUsuarioNome($sock, $cod_curso); // <- apenas os que interagem

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


    // Define lista de formadores

    $lista_formadores = RetornaListaCodUsuarioFormador($sock, $cod_curso);
    $str_formadores   = "";
    if (is_array($lista_formadores))
    {
      $str_formadores = implode("/", $lista_formadores);
    }

    // Define lista de convidados

    //$lista_convidados = RetornaCodUsuarioConvidadoComSemInteracao($sock);
    $lista_convidados = RetornaCodUsuarioConvidado($sock, $cod_curso); // com interacao apenas
    $str_convidados   = "";
    if (is_array($lista_convidados))
    {
      $str_convidados = implode("/", $lista_convidados);
    }

    // Define lista de visitantes

    $lista_visitantes = RetornaVisitantes($sock, $cod_curso);
    $str_visitantes   = "";
    if (is_array($lista_visitantes))
    {
      foreach ($lista_visitantes as $cod => $nome)
        $tmp[] = $cod;
      $str_visitantes = implode("/", $tmp);
    }

    // Obt�m intera��es entre participantes (com e sem n� todos)

    $lista_msgs_com_no_todos=RetornaMapaInteracaoSessao($sock,$cod_sessao,true);
    $lista_msgs_sem_no_todos=RetornaMapaInteracaoSessao($sock,$cod_sessao,false);
    $lista_usuarios_sessao=RetornaListaApelidos($sock,$cod_sessao);

    // Preenche listas de arestas (lista com n� Todos e sem n� Todos)

    $str_msgs_com_no_todos="";
    unset($tmp);
    unset($tmp2);
    if (count($lista_msgs_com_no_todos)>0)
    {
      foreach($lista_msgs_com_no_todos as $cod_usuario_emissor => $linha)
      {
        unset($tmp);
        foreach($linha as $cod_usuario_receptor => $qtde)
        {
          if ($qtde>0 && $cod_usuario_receptor!=$cod_usuario_emissor) {  // cancela auto-arestas
            $tmp[] = $cod_usuario_receptor.",".$qtde;
          }
        }
         if (count($tmp)>0) {
          $tmp2[] = $cod_usuario_emissor.":".implode(".",$tmp);
        }
      }
      if (count($tmp2)>0) {
        $str_msgs_com_no_todos = implode("/", $tmp2);
      }
    }

    $str_msgs_sem_no_todos = "";
    unset($tmp);
    unset($tmp2);
    if (count($lista_msgs_sem_no_todos)>0)
    {
      foreach($lista_msgs_sem_no_todos as $cod_usuario_emissor => $linha)
      {
        if (count($linha)>0) {
          unset($tmp);
          foreach($linha as $cod_usuario_receptor => $qtde)
          {
            if ($qtde>0 && $cod_usuario_receptor!=$cod_usuario_emissor) {  // cancela auto-arestas
              $tmp[] = $cod_usuario_receptor.",".$qtde;
            }
          }

          if (count($tmp)>0) {
            $tmp2[] = $cod_usuario_emissor.":".implode(".",$tmp);
          }

        }
      }
      if (count($tmp2)>0) {
        $str_msgs_sem_no_todos = implode("/", $tmp2);
      }
    }

    // Preenche lista de usuarios que fizeram parte da sessao
    unset($tmp);
    if (count($lista_usuarios_sessao)>0) {
      foreach($lista_usuarios_sessao as $cod => $nome) {
        $tmp[]=$cod;
      }
    }


    if(is_array($tmp))
      $usuarios_sessao=implode("/",$tmp);
    // Adiciona o no Todos nessa lista.
    if ($usuarios_sessao!="") {
      $usuarios_sessao.="/0";
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


    // Determina se o usu�rio em quest�o pode ou n�o acessar a ferramenta Perfil.
    if (TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,13)) {
      $mostrar_link_perfil = "sim";
    } else {
      $mostrar_link_perfil = "nao";
    }

    // Invoca o applet.
    echo("<center>\n");
    echo("<applet codebase='grafo/' code='applet/GrafoApplet.class' width='100%' height='550px' >");
    /* ?? - Seu navegador n�o permite a execu��o de aplicativos em Java. Visite o site www.java.com para obter informa��es sobre como fazer que seu navegador seja compat�vel com essa tecnologia.
    /* OBSERVA��O IMPORTANTE: REGISTRAR ESSA FRASE TAMB�M

    echo("    alt=\"Seu navegador n�o permite a execu��o de aplicativos em Java. Visite o site www.java.com para obter informa��es sobre como fazer que seu navegador seja compat�vel com essa tecnologia.\" >\n");
    echo("  <param name='progressbar' value='true'>\n");
    /* ?? - Fazendo download do programa. Aguarde... */
    /* OBSERVACAO IMPORTANTE: REGISTRAR ESSA FRASE TAMBEM */

    echo("  <param name='boxmessage' value='Fazendo download do programa. Aguarde...'>\n");
    echo("  <param name='boxbgcolor' value='#f0f0f0'>\n");
    echo("  <param name='boxfgcolor' value='#000000'>\n");
    echo("  <param name='progresscolor' value='#000080'>\n");
    echo("  <param name='cod_curso' value='".$cod_curso."'>\n");
    echo("  <param name='mostrar_link_perfil' value='".$mostrar_link_perfil."'>\n");

    /* OBSERVA��O IMPORTANTE: Substituir abaixo pelas respectivas frases do idioma corrente */

    echo("  <param name='textos' value=\"".$textos."\">\n");
//    echo("  <param name='textos' value=\"Simple Graph/Polar Graph/Start/Stop/animation/All/Students/Instructors/Guests/Visitors/Please wait while system prepares its graphical representations./Loading graph management programs.../Creating simple graph.../Creating polar graph.../Preparing windows.../End!/Show all course participants/Show only session participants/Show only participants that received or sent messages/Show separately messages sent to all participants/Student/Instructor/Guest/Visitor/Key/Profile/Show information/Hide node/Hide selected nodes/Hide not selected nodes/Show hidden nodes/Information/Category/Weight/Node/Edge/Interconnected elements/Information about/Number of messages\">\n");

// Bruno: faltou adicionar na lista a palavra Perfil, exatamente entre Legenda e Mostrar informa��o.

    echo("  <param name='codigo_nome' value='-1:Administrador/".$str_usuarios."'>\n");
    echo("  <param name='codigo_maximo' value='".$max_cod_usuario."'>\n");
    echo("  <param name='arestas_com_no_todos' value='".$str_msgs_com_no_todos."'>\n");
    echo("  <param name='arestas_sem_no_todos' value='".$str_msgs_sem_no_todos."'>\n");
    echo("  <param name='formador' value='".$str_formadores."'>\n");
    echo("  <param name='convidado' value='".$str_convidados."'>\n");

    // O Bate-papo � a �nica ferramenta em que Visitantes e Convidados sem intera��o podem interagir
    echo("  <param name='visitante' value='".$str_visitantes."'>\n");

    echo("  <param name='usuarios_sessao' value='".$usuarios_sessao."'>\n");
    echo("</applet>\n");
    echo("</center>\n");
  }



  else  // apresentacao=="tabela"
  {
    $lista_usuarios   = RetornaTodosUsuarios($sock, $cod_curso);
    $lista_formadores = RetornaListaCodUsuarioFormador($sock, $cod_curso);
    if ($visualizar == "todos")
    {
      // verificar todos os usuarios do curso
      $lista_usuarios_batepapo = $lista_usuarios;
    }
    else
    {
      // verificar usuarios da sessao
      $lista_usuarios_batepapo = RetornaListaApelidos($sock, $cod_sessao);
    }

    $lista_msgs=RetornaMapaInteracaoSessao($sock,$cod_sessao,($todos=="sim"));

    if (is_array($lista_usuarios_batepapo))
    {
      $cod_anterior=-1;
      // (Variaveis: visualizar=todos visualizar=mensagens todos=sim)

      /* <!----------------- Tabelao -----------------> */
      echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
      echo("  <tr>\n");
      echo("    <td>\n");
      echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
      echo("        <tr class=\"head\">\n");
      // 13 - Autor da Mensagem
      echo("          <td>".RetornaFraseDaLista($lista_frases,13)."</td>\n");
      // 20 - Destinat�rio da Mensagem
      echo("          <td width=30%>".RetornaFraseDaLista($lista_frases,20)."</td>\n");
      // 59 - Quantidade
      echo("          <td width=35%>".RetornaFraseDaLista($lista_frases,59)."</td>\n");
      echo("        </tr>\n");

      foreach ($lista_usuarios_batepapo as $cod_usu => $apelido)
      {
        // somente monto uma tabela para este se ele a opcao de exibicao manda ele aparecer ou se ele enviou alguma msg
        if ( $visualizar == "todos" || $visualizar == "sessao" || $lista_msgs[ $cod_usu ] > 0 )
        {
          if (count($lista_msgs[$cod_usu])>0)
          {
            foreach($lista_msgs[$cod_usu] as $cod_usu_rec => $qtde)
            {
              if ($cod_usu_rec>0)
              {
                // msgs para usuario especifico. As msgs para todos tratamos mais embaixo
                echo("        <tr>\n");
                if ($cod_usu!=$cod_anterior)
                  echo("          <td>".MontaLinkPerfil($cod_usu,$lista_usuarios[$cod_usu])."</td>\n");
                echo("          <td>".MontaLinkPerfil($cod_usu_rec,$lista_usuarios[$cod_usu_rec])."</td>\n");
                echo("          <td>".$qtde."</td>\n");
                echo("        </tr>\n");
                $cod_anterior=$cod_usu;
              }
            }
          }
          else
          {
            echo("        <tr>\n");
            if ($cod_usu!=$cod_anterior)
              echo("          <td>".MontaLinkPerfil($cod_usu,$lista_usuarios[$cod_usu])."</td>\n");
            echo("          <td>&nbsp;</td>\n");
            echo("          <td>0</td>\n");
            echo("        </tr>\n");
            $cod_anterior=$cod_usu;
          }
          if ($lista_msgs[$cod_usu][0]>0)
          {
            // Verificando se houve msgs desse para todos
            echo("        <tr>\n");
            if ($cod_usu!=$cod_anterior)
                echo("        <td>".MontaLinkPerfil($cod_usu,$lista_usuarios[$cod_usu])."</td>\n");
            // 72 - Todos
            echo("          <td>".RetornaFraseDaLista($lista_frases,72)."</td>\n");
            echo("          <td>".$lista_msgs[$cod_usu][0]."</td>\n");
            echo("        </tr>\n");
            $cod_anterior=$cod_usu;
          }
        }
      }
      // Fim Tabela Interna
      echo("      <table>\n");
      echo("    </td>\n");
      echo("  </tr>\n");
      // Fim Tabel�o
      echo("</table>\n");
    }
  }
  Desconectar($sock);

  echo("</body>\n");
  echo("</html>\n");

?>

