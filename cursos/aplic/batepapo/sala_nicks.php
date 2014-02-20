<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/batepapo.php

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
  ARQUIVO : cursos/aplic/batepapo/sala_nicks.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");


  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;

  $cod_curso = $_GET['cod_curso'];
  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10);

  $cod_sessao=RetornaSessaoCorrente($sock);

  echo("<!DOCTYPE HTML SYSTEM \"http://teleduc.nied.unicamp.br/~teleduc/loose-custom.dtd\">\n");
  echo("<html lang=\"pt\">\n"); 
  echo("  <head>\n");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\">\n");
  echo("    <link href=\"../js-css/dhtmlgoodies_calendar.css\" rel=\"stylesheet\" type=\"text/css\">\n");
  echo("    <script type=\"text/javascript\" src=\"../js-css/jscript.js\"></script>\n");

  echo("    <script type=\"text/javascript\" language=\"javascript\">\n");
  echo("      var timeoutnick;\n");
  echo("      timeoutnick=setTimeout(\"Recarrega()\", 1000)\n");

  echo("      function Iniciar() \n");
  echo("      { \n");
  echo("        startList(); \n");
  echo("      } \n");

  echo("      function Recarrega(){\n");
  echo("        if (navigator.userAgent.indexOf('MSIE') != '-1'){\n");
  echo("          window.self.history.go(0);\n");
  echo("        }else{\n");
  echo("          window.self.location.reload();\n");
  echo("        }\n");
  
  echo("      }\n");
  
  //lista que sera usada tanto na funcao OpenWindowLink como para colocar um link de perfil em cada aluno
   $lista_apelidos=RetornaListaApelidosOnline($sock,$cod_sessao);
   
    /* *********************************************************
  Funcao OpenWindowLink
    Abre nova janela com o perfil, se acessado atraves do link
    Entrada: funcao = $cod_curso - Codigo do curso
    Saida:   false - para nao dar reload na pagina. Conferir a
                     chamada da fun�o
  */
  echo("      function OpenWindowLink(cod_aluno) \n");
  echo("      {\n");
  echo("        if (cod_aluno >= 0)\n");	/* perfil de apenas um aluno */
  echo("        {\n");
  echo("          window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+cod_aluno,\"PerfilDisplay\",\"width=600,height=400,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        }\n");
  echo("        if (cod_aluno == ' ')\n");	/* perfil de todos os alunos */
  echo("        {\n");
  $url_exibir_todos = "window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso;
  if(is_array($lista_apelidos)) {
    foreach($lista_apelidos as $cod => $apelido){
      $url_exibir_todos .= "&cod_aluno[]=".$cod;
    }
  }
  $url_exibir_todos .= "\",'PerfilDisplay',\"width=600,height=400,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\"); \n";
  echo("          ".$url_exibir_todos);
  echo("        }\n");
  echo("        return(false);\n");
  echo("      }\n\n");

  echo("    </script>\n");
  echo("  </head>\n");
  echo("  <body style=\"background:none;border-left:1px solid; padding-left:5px;\">\n");
  echo("    <br />\n");
  /* 117 - Usu&aacute;rios Online */
  echo("    <b>".RetornaFraseDaLista($lista_frases,117)."</b><br>\n");
  /* 20 - Todos */
  //echo("    <a href=\"#\" onclick=\"OpenWindowLink(' ');\">".RetornaFraseDaLista($lista_frases,20)."</a><br />\n");
  foreach($lista_apelidos as $cod => $apelido){
     if ($cod!=$cod_usuario)
      echo("    <a href=\"#\" onclick=\"OpenWindowLink(".$cod.");\">".html_entity_decode($apelido)."</a><br />\n");
  }

  echo("  </body>\n");
  echo("</html>\n");