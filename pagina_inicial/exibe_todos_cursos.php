<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/exibe_todos_cursos.php

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
  ARQUIVO : pagina_inicial/exibe_todos_cursos.php
  ========================================================== */

  $bibliotecas = "../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc"); 
  include("exibe_cursos.inc");

  $pag_atual = "exibe_todos_cursos.php";

  /* Caso o usu�rio n�o tenha preenchido seus dados pessoais, manda para tela de preenchimento. */
  if (!empty($_SESSION['login_usuario_s'])) {
    $sock=Conectar("");
    if(!PreencheuDadosPessoais($sock))
    {
      Desconectar($sock);
      header("Location: preencher_dados.php?acao=preencherDados&atualizacao=true");
      exit;
    }
    Desconectar($sock);
  }

  include("../topo_tela_inicial.php");

  /**********************************************************
    @TODO - Verificar necessidade do feedbackobject abaixo, 
    s� ser� utilizado se mudarmos o link para onde o usu�rio ir� ap�s logar
   */
  
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro 
  $feedbackObject->addAction("logar", 197, 0);

  $lista_frases_autenticacao = RetornaListaDeFrases($sock, 25);

  /* Obt� a raiz_www */
  $raiz_www = RetornaRaizWWW($sock);
  
  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  				$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("      }\n\n");

  echo("      function TestaNome(form){\n");
  /* Elimina os espaços para verificar se o titulo nao eh formado por apenas espaços */
  echo("        Campo_login = form.login.value;;\n");
  echo("        Campo_senha = form.senha.value;\n");
  echo("        while (Campo_login.search(\" \") != -1){\n");
  echo("          Campo_login = Campo_login.replace(/ /, \"\");\n");
  echo("        }\n");
  echo("        if (Campo_login == ''){\n");
  /* 4 - Por favor preencha o campo 'Login'. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_autenticacao, 4)."');\n");
  echo("          document.formAutentica.login.focus();\n");
  echo("          return(false);\n");
  echo("        } else {\n");
  echo("          while (Campo_senha.search(\" \") != -1){\n");
  echo("            Campo_senha = Campo_senha.replace(/ /, \"\");\n");
  echo("          }\n");
  echo("          if (Campo_senha == ''){\n");
  /* 5 - Por favor preencha o campo \"Senha\". */
  echo("            alert('".RetornaFraseDaLista($lista_frases_autenticacao, 5)."');\n");
  echo("          document.formAutentica.senha.focus();\n");
  echo("            return(false);\n");  
  echo("          }\n");
  echo("        }\n");
  echo("        return(true);\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /*199 - Todos os cursos  */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,199)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  /*
  				  if(empty($_SESSION['login_usuario_s']))
        		  {
  echo("			  <div class=\"Aviso\"><h5>".RetornaFraseDaLista($lista_frases,221)."<br /><br />".RetornaFraseDaLista($lista_frases,217)."<a href=\"autenticacao_cadastro.php\">".RetornaFraseDaLista($lista_frases,218)."</a>.<br />".RetornaFraseDaLista($lista_frases,219)."<a href=\"cadastro.php\">".RetornaFraseDaLista($lista_frases,220)."</a>.</h5></div><br />\n");      		
        		  }
  */
  
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");

  echo("              <td colspan=4>\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /*192 - N�o iniciados (rec�m-criados no servidor)*/
  echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,192)."</td>");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  /* 5 - Curso */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
                            if(empty($_SESSION['login_usuario_s']))      /*caso o usuario nao esteja logado*/
  echo("                    <td colspan=2 width=\"10%\">&nbsp;</td>\n");
                            else
                            {
  echo("                    <td width=\"10%\">&nbsp;</td>\n");
  echo("                    <td width=\"10%\">&nbsp;</td>\n");
                            }
  echo("                  </tr>\n");

  require("inicial.inc");
  $lista = RetornaCursosNaoIniciadosSemUsuario($sock);
  $hoje=time();
  
  /*Exibe cursos que ainda n�o come�aram*/
  if (count($lista)>0 && $lista != "")
  {
    foreach($lista as $cod => $linha)
    {
      $cod_usuario = RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'], $linha['cod_curso']);
      Desconectar($sock);
      $tem_acesso_curso = ParticipaDoCurso($linha['cod_curso']);
      $sock=Conectar("");

      echo("                  <tr>\n");
      echo("                    <td class=\"alLeft\">".$linha['nome_curso']."</td>\n");
      if ($linha['acesso_visitante']=="A")
      {
        /* 56 - Visitar */
        echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,56)."\" onClick=\"document.location='../cursos/aplic/index.php?cod_curso=".$linha['cod_curso']."&amp;visitante=sim';\" type=\"button\" /></td>\n");
      }
      else if(empty($_SESSION['login_usuario_s']))
      {
        /* 53 - Informa��es */
        echo("                    <td colspan=2><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,53)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
      }
      else
      {
        
        /* Se o usuario estiver logado e for formador/coordenador do curso, pode entrar.
         * Se o usuario tem acesso ao curso e o curso já começou, também pode. 
         */
        if($tem_acesso_curso || empty($_SESSION['cod_usuario_global_s']))
        {
          /* 55 - Entrar */
          echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,55)."\" onClick=\"document.location='../cursos/aplic/index.php?cod_curso=".$linha['cod_curso']."';\" type=\"button\" /></td>\n");
        }
        else
          echo("                    <td>&nbsp;</td>\n");

        if($linha['inscricao_inicio']<=$hoje && $linha['inscricao_fim']>=$ontem && !$tem_acesso_curso)
        {
          /* 54 - Inscri��es */
          echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,54)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
        }
        else if(!$tem_acesso_curso)
        {
          /* 53 - Informa��es */
          echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,53)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
        }
        else
          echo("                    <td>&nbsp;</td>\n");

        echo("                  </tr>\n");
      }
    }
  }
  else
  {
    echo("                  <tr>\n");
    /* 57 - N�o h� nenhum */
    /* 195 - Curso n�o iniciado */
    echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,57)." ".RetornaFraseDaLista($lista_frases,195)."</td>\n");
    echo("                  </tr>\n");
  }
  /*171 - Em andamento */

  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,171)."</td>");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  /* 5 - Curso */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
 						    if(empty($_SESSION['login_usuario_s']))
  echo("                    	<td colspan=2 width=\"10%\">&nbsp;</td>\n");
  							else
  							{
  echo("                    	<td width=\"10%\">&nbsp;</td>\n");
  echo("                    	<td width=\"10%\">&nbsp;</td>\n");
  							}
  echo("                  </tr>\n");

  
  $lista = RetornaCursosEmAndamentoSemUsuario($sock);
  /*Exibe cursos em andamento*/
  if (count($lista)>0 && $lista != "")
  {
    foreach($lista as $cod => $linha)
    {
      $cod_usuario = RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'], $linha['cod_curso']);
      Desconectar($sock);
      $tem_acesso_curso = ParticipaDoCurso($linha['cod_curso']);
      $sock=Conectar("");

      echo("                  <tr>\n");
      echo("                    <td class=\"alLeft\">".$linha['nome_curso']."</td>\n");
      if ($linha['acesso_visitante']=="A")
      {
        /* 56 - Visitar */
        echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,56)."\" onClick=\"document.location='../cursos/aplic/index.php?cod_curso=".$linha['cod_curso']."&amp;visitante=sim';\" type=\"button\" /></td>\n");
      }
      else if(empty($_SESSION['login_usuario_s']))
      {
        /* 53 - Informa��es */
        echo("                    <td colspan=2><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,53)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
      }
      else
      {
        /* Se o usuario estiver logado e for formador/coordenador do curso, pode entrar.
         * Se o usuario tem acesso ao curso e o curso já começou, também pode. 
         */
        if($tem_acesso_curso || empty($_SESSION['cod_usuario_global_s']))
        {
          /* 55 - Entrar */
          echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,55)."\" onClick=\"document.location='../cursos/aplic/index.php?cod_curso=".$linha['cod_curso']."';\" type=\"button\" /></td>\n");
        }
        else
          echo("                    <td>&nbsp;</td>\n");

        if($linha['inscricao_inicio']<=$hoje && $linha['inscricao_fim']>=$ontem && !$tem_acesso_curso)
        {
          /* 54 - Inscri��es */
          echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,54)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
        }
        else if(!$tem_acesso_curso)
        {
          /* 53 - Informa��es */
          echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,53)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
        }
        else
          echo("                    <td>&nbsp;</td>\n");

        echo("                  </tr>\n");
      }
    }
  }
  else
  {
    echo("                  <tr>\n");
    /* 57 - N�o h� nenhum */
    /* 171 - Em andamento */
    echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,57)." ".RetornaFraseDaLista($lista_frases,171)."</td>\n");
    echo("                  </tr>\n");
  }
  /* 172 - Inscri��es abertas */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,172)."</td>");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  /* 5 - Curso */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
  						    if(empty($_SESSION['login_usuario_s']))
  echo("                    	<td colspan=2 width=\"10%\">&nbsp;</td>\n");
  							else
  							{
  echo("                    	<td width=\"10%\">&nbsp;</td>\n");
  echo("                    	<td width=\"10%\">&nbsp;</td>\n");
  							}
  echo("                  </tr>\n");
  
  $lista = RetornaCursosInscricaoSemUsuario($sock);
  /*Exibe cursos com Inscri��o em aberto*/
  if (count($lista)>0 && $lista != "")
  {
    foreach($lista as $cod => $linha)
      {
      	$cod_usuario = RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'], $linha['cod_curso']);
        Desconectar($sock);
        $tem_acesso_curso = ParticipaDoCurso($linha['cod_curso']);
        $sock=Conectar("");
        
        echo("                  <tr>\n");
        echo("                    <td class=\"alLeft\">".$linha['nome_curso']."</td>\n");
        if ($linha['acesso_visitante']=="A")
        {
          /* 56 - Visitar */
          echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,56)."\" onClick=\"document.location='../cursos/aplic/index.php?cod_curso=".$linha['cod_curso']."&amp;visitante=sim';\" type=\"button\" /></td>\n");
        }
        else if(empty($_SESSION['login_usuario_s']))
        {
        	/* 53 - Informa��es */
        	echo("                    <td colspan=2><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,53)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
        }
        else
        {
       		/* Se o usuario estiver logado e for formador/coordenador do curso, pode entrar.
       	 	* Se o usuario tem acesso ao curso e o curso já começou, também pode. 
       	 	*/
        	if($tem_acesso_curso || empty($_SESSION['cod_usuario_global_s']))
        	{
          		/* 55 - Entrar */
          		echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,55)."\" onClick=\"document.location='../cursos/aplic/index.php?cod_curso=".$linha['cod_curso']."';\" type=\"button\" /></td>\n");
        	}
        	else
          		echo("                    <td>&nbsp;</td>\n");
        
        	if($linha['inscricao_inicio']<=$hoje && $linha['inscricao_fim']>=$ontem && !$tem_acesso_curso)
        	{
          		/* 54 - Inscri��es */
          		echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,54)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
        	}
        	else if(!$tem_acesso_curso)
        	{
          		/* 53 - Informa��es */
          		echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,53)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
        	}
        	else
          		echo("                    <td>&nbsp;</td>\n");

	        echo("                  </tr>\n");
        }
      }
  }
  else
  {
  	echo("                  <tr>\n");
    /* 57 - N�o h� nenhum */	
  	/* 175 - curso com inscri��o aberta */
    echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,57)." ".RetornaFraseDaLista($lista_frases,175)."</td>\n");
    echo("                  </tr>\n");
  }

	/* 173 - Encerrados */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,173)."</td>");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  /* 5 - Curso */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,5)."</td>\n");
 						    if(empty($_SESSION['login_usuario_s']))
  echo("                    	<td colspan=2 width=\"10%\">&nbsp;</td>\n");
  							else
  							{
  echo("                    	<td width=\"10%\">&nbsp;</td>\n");
  echo("                    	<td width=\"10%\">&nbsp;</td>\n");
  							}
  echo("                  </tr>\n");
  


  $lista = RetornaCursosPassadosSemUsuario($sock, $_SESSION['codigo_usuario_s']);
    /*Exibe cursos jah encerrados*/
  if (count($lista)>0 && $lista != "")
  {
    foreach($lista as $cod => $linha)
    {
      $cod_usuario = RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'], $linha['cod_curso']);
      Desconectar($sock);
      $tem_acesso_curso = ParticipaDoCurso($linha['cod_curso']);
      $sock=Conectar("");

      echo("                  <tr>\n");
      echo("                    <td class=\"alLeft\">".$linha['nome_curso']."</td>\n");
      if ($linha['acesso_visitante']=="A")
      {
        /* 56 - Visitar */
        echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,56)."\" onClick=\"document.location='../cursos/aplic/index.php?cod_curso=".$linha['cod_curso']."&amp;visitante=sim';\" type=\"button\" /></td>\n");
      }
      else if(empty($_SESSION['login_usuario_s']))
      {
        /* 53 - Informa��es */
        echo("                    <td colspan=2><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,53)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
      }
      else
      {
        /* Se o usuario estiver logado e for formador/coordenador do curso, pode entrar.
         * Se o usuario tem acesso ao curso e o curso já começou, também pode. 
         */
        if($tem_acesso_curso || empty($_SESSION['cod_usuario_global_s']))
        {
          /* 55 - Entrar */
          echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,55)."\" onClick=\"document.location='../cursos/aplic/index.php?cod_curso=".$linha['cod_curso']."';\" type=\"button\" /></td>\n");
        }
        else
          echo("                    <td>&nbsp;</td>\n");

        if($linha['inscricao_inicio']<=$hoje && $linha['inscricao_fim']>=$ontem && !$tem_acesso_curso)
        {
          /* 54 - Inscri��es */
          echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,54)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
        }
        else if(!$tem_acesso_curso)
        {
          /* 53 - Informa��es */
          echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,53)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
        }
        else
          echo("                    <td>&nbsp;</td>\n");

        echo("                  </tr>\n");
      }
    }
  }
  else
  {
    echo("                  <tr>\n");
    /* 200 - N�o h� nenhum curso encerrado */
    echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,200)."</td>\n");
    echo("                  </tr>\n");
  }


  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  Desconectar($sock);
  echo("  </body>\n");
  echo("</html>");
?>