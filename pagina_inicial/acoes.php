<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/autenticacao/acoes.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
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

    Nied - Ncleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/
//Teste
/*==========================================================
  ARQUIVO : pagina_inicial/acoes.php
  ========================================================== */

  
  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");
  include("autenticacao.inc");
  
  $cod_ferramenta	=	8;
  $sock	= Conectar("");

  $lista_frases	=	RetornaListaDeFrases($sock,9);

  $raiz_www = RetornaRaizWWW($sock);
  $caminho 	= $raiz_www."/pagina_inicial";


  // Vamos pegar o cod_curso, seja la por onde ele tenha vindo
  if (isset($_GET['cod_curso']) && ($_GET['cod_curso'] != ""))
  {
  	$cod_curso = $_GET['cod_curso'];
  } 
  else if(isset($_POST['cod_curso']) && ($_POST['cod_curso'] != ""))
  {
  	$cod_curso = $_POST['cod_curso'];
  }

  
  
  // Verifica se o usuario e a senha estao corretos
  $cod_usuario = VerificaLoginSenha($login, $_POST['senha']);
  if ($cod_usuario == 0)
  {
  	Desconectar($sock);
  	header("Location:autenticacao_cadastro.php?cod_curso=".$cod_curso."&acao=erroAutenticacao&atualizacao=false");
  	exit;
  }

  /* Verifica se o usuario jah confirmou o email */
  if(!VerificaConfirmacaoEmail($login)) {
  	Desconectar($sock);
  	header("Location:autenticacao_cadastro.php?cod_curso=".$cod_curso."&acao=erroConfirmacao&atualizacao=false");
  	exit;
  }
  
  $_SESSION['cod_usuario_global_s'] = $cod_usuario;
  $_SESSION['cod_usuario_s'] 	 	= (!empty($cod_curso)) ? RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'],$cod_curso) : "";
  //Email nao pode mais ser usado para login
  //$_SESSION['login_usuario_s']	= (BoolEhEmail($login) == 1) ? RetornaLoginUsuario($sock) : $login;
  $_SESSION['login_usuario_s']	= $login;

  //$_SESSION['cod_lingua_s'] 		= $cod_lingua;

  $_SESSION['visitante_s'] 			= $cod_visitante_s;
  $_SESSION['visao_formador_s'] = 1;
  
  
  /* Se a autenticacao for para inscricao, manda para tela de inscricao */
  if($_POST['destino']=="inscricao") {
  	Desconectar($sock);
  	header("Location:inscricao.php?cod_curso=".$cod_curso."&amp;tipo_curso=".$tipo_curso);
  	exit;
  }

  /* Verifica se o cod_curso corresponde a um curso valido e 
   * se foi fornecido algum cod_curso no ato de login 
   */
  $curso_valido = (!empty($cod_curso) ? CursoValido($sock, $cod_curso) : "");
  if(!empty($curso_valido))
  {
    Desconectar($sock);
    header("Location: ../cursos/aplic/index.php?cod_curso=".$cod_curso);
  	exit;
  }
  else 
  {
    /* Verifica se quem esta fazendo login eh o administrador do ambiente, admtele */
    if($_SESSION['cod_usuario_global_s'] == -1)
    {
      Desconectar($sock);
      header("Location: ../administracao/index.php?acao=logar&atualizacao=true&cod_lingua=".$_SESSION['cod_lingua_s']);
      exit;
    }
    else
    {
      /* Usuario comum esta fazendo o login */
      Desconectar($sock);

      /* Se o usuario recebeu algum email com endereco especifico, mande ele para la */
      if (!empty($_SESSION['url_de_acesso']))
      {
      	$urldeacesso = $_SESSION['url_de_acesso'];
      	unset($_SESSION['url_de_acesso']);
    	header("Location:".$urldeacesso);
    	exit;
      }
    	/* Redireciona para a tela de Meus Cursos */
    	header("Location: exibe_cursos.php?acao=logar&atualizacao=true");
    	exit;
    }
  }
  Desconectar($sock);
  exit;
?>
