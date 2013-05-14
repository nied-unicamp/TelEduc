<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/acesso_php.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
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

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : bibliotecas/acesso_php.inc
  ========================================================== */
/*
   Verifica�o de valida�o...
   IMPORTANTE : Tem que ser o primeiro comando do arquivo, e nada pode ter
   sido enviando para HTML...
   - Retorna o cod_usuario do usuario logado
*/


/* *********************************************************************
   AcessoNegado - Exibe a mensagem padr� para entrada em �ea restrita.
   Sem entrada ou saida. Interrompe o c�igo
*/
function AcessoNegado()
{
  $sock=Conectar($sock);
  /* 1 - Erro de Acesso */
  /* 2 - Sinto muito, mas voc�n� tem acesso a esta �ea. */
  echo("<HTML><BODY BGCOLOR=white><H1>TelEduc</H1><h2>".RetornaFrase($sock,1,-2)."</h2>".RetornaFrase($sock,2,-2));
  Desconectar($sock);
  die("");
}


/* *********************************************************************
   VerificaLoginSenha - Verifica se login/email e senha são validos para dado curso
   Entrada: $cod_curso - Codigo do curso
	    $login     - login fornecido pelo usuario
            $senha     - senha fornecida pelo usuario
   Saida:   codigo do usuario, caso par usuario/senha seja valido
	    0, caso contrario
*/
function VerificaLoginSenha ($login, $senha)
{
  $senha_crypt = crypt($senha,"AA");
  $sock = Conectar("");
  
  if ($login == "admtele") {
    $query = "select valor from Config where item = 'admtele'";
  }
  else {
    //$query = "select senha, cod_usuario, confirmacao from Usuario where login='".VerificaStringQuery($login)."' and senha='".VerificaStringQuery($senha_crypt)."'";
    $query = "select senha, cod_usuario, confirmacao from Usuario where login='".VerificaStringQuery($login)."'";
  }

  $res = Enviar($sock,$query);
  $num = RetornaNumLinhas($res);
  $linha = RetornaLinha($res);

  if ($login == "admtele") {
    $cod_usuario = -1;
    $confirmacao = "";
    $senha_db = $linha['valor'];
  } else {
    $cod_usuario = $linha['cod_usuario'];
	$confirmacao = $linha['confirmacao'];
	$senha_db = $linha['senha'];
  }

  return (($num < 1) || ($senha_db != $senha_crypt) ? 0 : $cod_usuario);
}

/* *********************************************************************
   ehEmail - Verifica se login eh um username ou um email
   Entrada: $login - login utilizado para autenticacao 
            $cod_curso   - Codigo do curso
   Saida:   caso o login seja um email, retorna um login correspondente, caso contrario
			mantem o login. se o email nao corresponder com nenhum login envia para
			pagina de autenticacao
*/

function ehEmail($login,$cod_curso){
	
	$sock = Conectar("");
	
	$email = explode('@',$login);

	if((count($email) == 2) && (substr($login,0,1) != "@")) {
		$query = "select login from Usuario where email='".VerificaStringQuery($login)."'";
		$res=Enviar($sock,$query);
		$linha=RetornaLinha($res);
		if($linha){
			return $linha['login'];
		}else{ 
    		Desconectar($sock);
    		header("Location:autenticacao_cadastro.php?cod_curso=".$cod_curso."&acao=erroAutenticacao&atualizacao=false");
    		exit;
		}
	}	
    return $login;
	
}

/* *********************************************************************
   VerificaAcessoCurso - Verifica se usuario tem acesso ao curso passado por parametro
   Entrada: $cod_usuario - Codigo do Usuario
            $cod_curso   - Codigo do curso
   Saida:   1, caso par usuario/curso seja valido
	    0, caso contrario
*/
function VerificaAcessoCurso ($cod_usuario, $cod_curso)
{
  $sock=Conectar("");
  $query="select cod_usuario from Usuario_curso where cod_usuario = '".VerificaStringQuery($cod_usuario)."' and cod_curso = '".VerificaStringQuery($cod_curso)."'";

  $res=Enviar($sock,$query);
  $num=RetornaNumLinhas($res);
  $linha=RetornaLinha($res);

  return ($num > 0);
}

/* *********************************************************************
   VerificaConfirmacaoEmail - Verifica se o email do login foi autenticado
   Entrada: $login - que sera verificado
   Saida:   true - email confirmado
						false - email nao confirmado
*/
function VerificaConfirmacaoEmail ($login)
{
	$sock = Conectar("");
	$query = "select confirmacao from Usuario ";
	$query.= "where login='".$login."'";
	$res = Enviar($sock, $query);
	$linha = RetornaLinha($res);
	if($linha[0]=="" || $linha[0]=="NULL") {
		return true;
	}
	return false;
}

/* *********************************************************************
   VerificaAutenticacao - Verifica se o usu�io tem autoriza�o no curso
   Entrada: $cod_curso - Codigo do curso
   Saida: Codigo do Usuario logado
*/
function VerificaAutenticacao($cod_curso)
{
  /* Informa�es sobre alunos. cod_usuario>0 aluno normal.
     cod_usuario = -1 Administrador (Formador).
     cod_usuario = -2 visitante. (Acesso Passivo).
     cod_usuario = -3 (Acesso negado ao curso).
  */

  /* Adquire as variaveis de verifica�o */
//   global /*$_SESSION,$visitante_s*/,$cod_curso_s,/*$cod_lingua_s*/,$logout_flag_s;

  /* Obt� a raiz_www */
  $sock = Conectar("");
  $query = "select diretorio from Diretorio where item = 'raiz_www'";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  $raiz_www = $linha[0];
  Desconectar($sock);

  $caminho = $raiz_www."/pagina_inicial";
 
/* Se quem esta acessando o ambiente eh um visitante */
  if ($_SESSION['visitante_s']=="sim")
  {
    $sock=Conectar("");
    $query="select acesso_visitante from Cursos where cod_curso=".VerificaNumeroQuery($cod_curso);
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    if ($linha[0]!="A")
    {
      /* 12 - Este curso n� permite a entrada de visitantes. */
      /* 23 - Voltar. */
      echo RetornaFrase($sock,12,-2).' <a href=# onclick=history.go(-1);>'.RetornaFrase($sock,23,-1).'</a>';
      Desconectar($sock);
      exit;
    }
    Desconectar($sock);
    return (-2);
  }
  else
  {
/* Se quem esta acessando nao esta logado */  	
    if ((!isset($_SESSION['login_usuario_s'])))
    {
      $sock=Conectar("");

      /* Salva a URL de acesso, para o caso de precisar redirecionar */
  	  $_SESSION['url_de_acesso'] = $_SERVER['REQUEST_URI'];

	  /* Usuario é direcionado para a pagina de autenticacao */
      header("Location:{$caminho}/autenticacao_cadastro.php?cod_curso=".$cod_curso);

      Desconectar($sock);
      exit;
    }
    else
    {
/* Se quem esta acessando, esta logado e eh o admtele */
      if (isset($_SESSION['login_usuario_s']) && ($_SESSION['login_usuario_s']=="admtele"))
      {
        $sock=Conectar("");

          //Desconectar($sock);
          if (isset($cod_curso))
          {
            $_SESSION['cod_usuario_s']=RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'],$cod_curso);
          }

		Desconectar($sock);
        return (-1);
      }
      else //VerificaAcessoCurso($_SESSION['cod_usuario_s'], $cod_curso)
      {
// Erro de l�gica, condi��o verificada anteriormente      	
/*        if (!isset($_SESSION['login_usuario_s'])) //($num<1 || $linha[0]!=$senha)
        {
          //Desconectar($sock);
          $sock=Conectar("");
      	 
		 // Salva a URL de acesso, para o caso de precisar redirecionar
  	  	 $_SESSION['url_de_acesso'] = $_SERVER['REQUEST_URI'];          
         header("Location:{$caminho}/autenticacao_cadastro.php?cod_curso=".$cod_curso);

          Desconectar($sock);
          exit;
        } */
          /* Se o usu�rio efetuou o logout, requisita novamente as informa�es para acesso. */
          if (isset($_SESSION['logout_flag_s']) && $_SESSION['logout_flag_s'] == 1)
          {
            $_SESSION['logout_flag_s'] = 0;

            //Desconectar($sock);
            $sock=Conectar("");

	    	header("Location:{$caminho}/autenticacao_cadastro.php?cod_curso=".$cod_curso);

            Desconectar($sock);
            exit;
          }
          else
          {
            //Desconectar($sock);
	    	$sock=Conectar("");

            $query="SELECT U.senha, U.cod_usuario
		    FROM Usuario U
		    INNER JOIN Usuario_curso UC
		    ON ((UC.cod_usuario_global = U.cod_usuario) AND (UC.cod_curso = '".VerificaStringQuery($cod_curso)."'))
		    WHERE login='".$_SESSION['login_usuario_s']."'";

            $res=Enviar($sock,$query);
            $num=RetornaNumLinhas($res);
            $linha=RetornaLinha($res);
	    	if ($num > 0)
	    	{
              $cod_curso_s=$cod_curso;
              $cod_usuario_s=RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'], $cod_curso);
              $cod_lin=RetornaLinguaUsuario($sock,$linha[1], $cod_curso);

              //Desconectar($sock);
// Retirado trecho do codigo abaixo pois estava mudando a lingua escolhida para a lingua no BD.              
//              if ($cod_lin!=$_SESSION['cod_lingua_s'])
//              {
//                $sock=Conectar("");
//                MudancaDeLingua($sock, $cod_lin); 
//	        //desconectar aqui??
//              }

              if(!PreencheuDadosPessoais($sock))
              {
                Desconectar($sock);
                header("Location:{$caminho}/preencher_dados.php?cod_curso=".$cod_curso."&acao=preencherDados&atualizacao=true");
              }

	      	  Desconectar($sock);
              return ($linha[1]);
            }
            else
            {
              return (-3);
            }
          }
        }
      }
    }
  
  echo("Se voc�est�vendo essa mensagem, ocorreu um erro na codifica�o dos IFs");
  exit;
}

/* *********************************************************************
   VerificaAutenticacaoAdministracao - Verifica se o usu�io
                                       tem autoriza�o a administracao
   Sem entradas.
   Saida: Codigo do Usuario logado
*/
function VerificaAutenticacaoAdministracao()
{
   $sock=Conectar("");
  	
  if (!isset($_SESSION['login_usuario_s']))
  {
    header("Location:../pagina_inicial/autenticacao_cadastro.php");
    Desconectar($sock);
    exit;
  }
  else if($_SESSION['login_usuario_s'] != "admtele")
  {
    /* 4 - Autoriza�o necess�ia para entrar no Ambiente de Administra�o do TelEduc. */
    /* 23 - Voltar */
    echo RetornaFrase($sock,4,-2).' <a href=# onclick=history.go(-1);>'.RetornaFrase($sock,23,-1).'</a>';
    Desconectar($sock);
    exit;
  }

  Desconectar($sock);
}

/* *********************************************************************
   GeraSenha - Gera uma senha aleat�ia para o usu�io
   Entrada: nenhuma
   Saida: senha gerada de 8 caracteres
*/
function GeraSenha()
{
  //$caracteres="ABCDEFGHJLKMNPQRSTUVWXYZabcdefghjmnpqrstuvwxyz23456789";
  $caracteres="abcdefghjkmnpqrstuvwxyz23456789";
  list($usec, $sec) = explode(' ', microtime());
  mt_srand((float) $sec + ((float) $usec * 100000));

  $senha="";

  for ($i=0;$i<8;$i++)
  {
    //$senha.=$caracteres[mt_rand(0,53)];
    $senha.=$caracteres[mt_rand(0,30)];
  }
  return $senha;
}

?>
