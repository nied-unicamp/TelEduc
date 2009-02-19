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

/*==========================================================
  ARQUIVO : pagina_inicial/acoes.php
  ========================================================== */

  
  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("autenticacao.inc");
  
  $cod_ferramenta=8;
  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,9);

  $query = "select diretorio from Diretorio where item = 'raiz_www'";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  $raiz_www = $linha[0];
  $caminho = $raiz_www."/pagina_inicial";

  if (!empty($_GET['cod_curso']))
  {
    $query = "select cod_curso from Cursos where cod_curso = ".VerificaNumeroQuery($cod_curso);
    $res = Enviar($sock,$query);
    $linha = RetornaLinha($res);
    $curso_valido = $linha[0];
  }

  if (!$cod_usuario = VerificaLoginSenha($_POST['login'], $_POST['senha']))
  {
    Desconectar($sock);
    header("Location:autenticacao.php?cod_curso=".$_POST['cod_curso']."&acao=erroAutenticacao&atualizacao=false");
    exit;
  }

  $_SESSION['cod_usuario_global_s'] = $cod_usuario;
  $_SESSION['cod_usuario_s'] = (!empty($cod_curso)) ? RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'],$cod_curso) : "";
  $_SESSION['login_usuario_s'] = $login;
  //$_SESSION['tipo_usuario_s'] = "";
  $_SESSION['cod_lingua_s'] = $cod_lingua; //??
  $_SESSION['visitante_s'] = $cod_visitante_s; //??
  $_SESSION['visao_formador_s'] = 1;
  //$_SESSION["logout_flag_s"];

  if(!empty($_POST['cod_curso']) && !empty($_POST['destino']))
  {
    Desconectar($sock);
    header("Location:{$caminho}/inscricao.php?cod_curso=".$_POST['cod_curso']."&tipo_curso=".$tipo_curso."");
  	exit;
  }
  else if(!empty($curso_valido))
  {
    Desconectar($sock);
    header("Location:{$caminho}/../cursos/aplic/index.php?cod_curso=".$_POST['$cod_curso']);
  	exit;
  }
  else if(!empty ($cod_usuario))
  {
    //se for admtele
    if($_SESSION["cod_usuario_global_s"] == -1)
    {
      Desconectar($sock);
      header("Location:{$caminho}/../administracao/index.php");
    	exit;
    }
    else 
    {
    	Desconectar($sock);
      header("Location:{$caminho}/exibe_cursos.php");
    	exit;
    }
  }
  else
  {
    Desconectar($sock);
    header("Location:autenticacao.php?cod_curso=".$_POST['cod_curso']."&erro_autenticacao=1");
  	exit;
  }

  Desconectar($sock);
  exit;
?>