<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/cursos_all.php

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
  ARQUIVO : pagina_inicial/cursos_all.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  if(!isset($extremos))
    $extremos = "";

  $pag_atual = "cursos_all.php";
  include("../topo_tela_inicial.php");

  $lista_frases_adm = RetornaListaDeFrases($sock,-5);

  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* Verificar qual o nome a ser exibido...*/
  if ($tipo_curso=="N")
    /* 194 - Cursos nao iniciados */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,194)."</h4>\n");
  else if ($tipo_curso=="A")
    /* 6 - Cursos em Andamento */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,6)."</h4>\n");
  else if ($tipo_curso=="I")
    /* 7 - Cursos com inscri��es abertas */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,7)."</h4>\n");
  else
    /* 8 - Cursos encerrados */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,8)."</h4>\n");
  /*else	/* else if($tipo_curso=="T") */
  	/* 199 - Todos os cursos */ 
  /*echo("          <h4>".RetornaFraseDaLista($lista_frases,199)."</h4>\n");*/

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  $lista_pastas=RetornaPastasDeCursos($sock,$tipo_curso);

  if (count($lista_pastas)<2 || isset($cod_pasta))
  {
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("            <tr>\n");
    echo("              <td valign=\"top\">\n");
    echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

    if (count($lista_pastas) == 0 || $lista_pastas == "")
    {
      $class = "head";
      $cod_pasta="";
    }
    else
    {
      $class = "head01";
      foreach($lista_pastas as $cod => $linha)
      {
        if(count($lista_pastas) == 1)
        {
          $cod_pasta = $linha['cod_pasta'];
          $nome_pasta=$linha['pasta'];
        }
        else if ($linha['cod_pasta'] == $cod_pasta)
        {
          $nome_pasta=$linha['pasta'];
        }
      }

      //169 - Categoria (adm)
      echo("                  <tr class=\"head\">\n");
      echo("                    <td colspan=\"3\"><b>".RetornaFraseDaLista($lista_frases_adm,169).": ".$nome_pasta."</b></td>\n");
      echo("                  </tr>\n");
    }

    echo("                  <tr class=\"".$class."\">\n");
    //265 - Nome do Curso (adm)
    echo("                    <td align=\"left\" width=80%>".RetornaFraseDaLista($lista_frases_adm,265)."</td>\n");
  //						    if(empty($_SESSION['login_usuario_s']))      /*caso o usuario nao esteja logado*/
  echo("                    	<td colspan=2 width=\"10%\">&nbsp;</td>\n");
  //							else
  //							{
  //echo("                    	<td width=\"10%\">&nbsp;</td>\n");
  //echo("                    	<td width=\"10%\">&nbsp;</td>\n");
  //							}
    echo("                  </tr>\n");

    $lista=RetornaListaDeCursos($sock,$tipo_curso,$cod_pasta);

    if (count($lista)>0 && $lista != "")
    {
      $hoje=time();
      $ontem=$hoje - 86400;

      foreach($lista as $cod => $linha)
      {
        $cod_usuario = RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'], $linha['cod_curso']);
        Desconectar($sock);
        $tem_acesso_curso = ParticipaDoCurso($linha['cod_curso']);
        $rejeitado_curso = RejeitadoDoCurso($linha['cod_curso']);
        $sock=Conectar("");

        echo("                  <tr>\n");
        echo("                    <td align=\"left\">".$linha['nome_curso']."</td>\n");
        if ($linha['acesso_visitante']=="A")
        {
          /* 56 - Visitar */
          echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,56)."\" onClick=\"document.location='../cursos/aplic/index.php?cod_curso=".$linha['cod_curso']."&amp;visitante=sim';\" type=\"button\" /></td>\n");
        }
        
        if(!empty($_SESSION['cod_usuario_global_s'])){
          echo("                    <td id=\"status_cel\"> ".StatusUsuarioNoCurso($sock, $linha['cod_curso'])." </td>");
        }
        
        if($tem_acesso_curso) {
          if($rejeitado_curso) {
            /* 223 - Inscrio no aceita */
            echo("                    <td>\n");
            echo("                      ".RetornaFraseDaLista($lista_frases,223)."\n");
            /* 235 - Inscrever-se novamente */
            echo("                      <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,235)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" />\n");
            echo("                    </td>\n");
          }else{
              /* 55 - Entrar */
              echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,55)."\" onClick=\"document.location='../cursos/aplic/index.php?cod_curso=".$linha['cod_curso']."';\" type=\"button\" /></td>\n");
          }
        }
        else
        {
          if($tipo_curso == "I")
          {
            /* 54 - Inscri��es */
            echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,54)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
          }
          else
          {
            /* 53 - Informa��es */
            echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,53)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
          }
        }
        
      }
    }
    else
    {
      /* 195 - curso nao iniciado */
      /* 174 - curso em andamento */
      /* 175 - curso com inscri��o aberta */
      /* 176 - Curso encerrado */
      if ($tipo_curso=="N")
        $tela2=RetornaFraseDaLista($lista_frases,195);
      else if ($tipo_curso=="A")
        $tela2=RetornaFraseDaLista($lista_frases,174);
      else if ($tipo_curso=="I")
        $tela2=RetornaFraseDaLista($lista_frases,175);
      else
        $tela2=RetornaFraseDaLista($lista_frases,176);
      /* 57 - N� h� nenhum */
      echo("                  <tr>\n");
      echo("                    <td colspan=3>".RetornaFraseDaLista($lista_frases,57)." ".$tela2.".</td>\n");
      echo("                  </tr>\n");
    }

    echo("                </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
  }
  else /* H� mais de uma pasta de cursos com cursos nela, e n�o se est� dentro de nenhuma */
  {
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
    echo("            <tr>\n");
    echo("              <td valign=\"top\">\n");
    echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("                  <tr class=\"head\">\n");
    /* 116 - Selecione uma categoria: */
    echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,116)."</td>\n");
    echo("                  </tr>\n");

    $link="cursos_all.php?tipo_curso=".$tipo_curso;
    foreach ($lista_pastas as $cod => $linha)
    {
        echo("                  <tr>\n");
        echo("                    <td colspan=\"3\"><a href=".$link."&amp;cod_pasta=".$linha['cod_pasta'].">".$linha['pasta']." (".$linha['num_cursos'].")</a></td>\n");
        echo("                  </tr>\n");
    }

    echo("                </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>");
  Desconectar($sock);
?>
