<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/reenvio.inc

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
  ARQUIVO : administracao/reenvio.inc
  ========================================================== */
/* **********************************************************************
   PreparaCabecalhoReenvio - Monta o Cabecalho padr�o da p�gina da administracao/infocurso
   Entrada: Nenhuma
   Saida: Monta o cabe�alho no HTML
*/
function PreparaCabecalhoReenvio($lista_frases)
{
  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("<head>\n");
  /* 1- Administra��o */
  echo("<title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title>\n");
  echo("<meta name=\"robots\" content=\"follow,index\" />\n");
  echo("<meta name=\"description\" content=\"\" />\n");
  echo("<meta name=\"keywords\" content=\"\" />\n");
  echo("<meta name=\"owner\" content=\"\" />\n");
  echo("<meta name=\"copyright\" content=\"\" />\n");
  echo("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("<link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("</head>\n");

  echo("<body text=#000000 bgcolor=#FFFFFF link=#0000FF vlink=#0000FF ALINK=#0000FF>\n");
  echo("<a name=\"topo\"></a>\n");
  echo("<h1><a href=\"index.htm\"><img src=\"../imgs/logo.gif\" border=\"0\" alt=\"TelEduc . Educa&ccedil;&atilde;o a Dist&acirc;ncia\" /></a></h1>\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
  echo("<tr>\n");
  echo("<td></td>\n");
  echo("<td valign=\"top\" id=\"topo\"><!--NAVEGACAO NIVEL 3-->\n");
  echo("<h3>".RetornaFraseDaLista($lista_frases,2)."</h3>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("<td valign=\"top\"><!--NAVEGACAO PRINCIPAL-->\n");
  echo("<ul id=\"nav\">\n");

  /* 3 - Cria��o de Curso */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,3),"index_criar_curso.php","");

  /* 125 - Editar Categorias */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,125),"editar_categoria.php","");

  /* 131 - Selecionar Categoria dos Cursos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,131),"selecionar_categoria.php","");

  /* 4 - Extra��o de Curso */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,4),"extracao/extrair_curso.php","");

  /* 141 - Inser��o de Cursos Extra�dos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,141),"insercao/inserir_curso.php","");

  /* 245 - Reutiliza��o de Cursos Encerrados */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,245),"resetar_curso.php","");

  /* 293 - Reenvio de dados aos coordenadores*/
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,293),"infocurso/reenvio.php","");

  /* 9 - Enviar e-mail para usu�rios */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,9),"enviar_email.php","");

  /* 8 - Trocar login */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,8),"trocar_login.php","");

  /* 5 - Consulta a Base de Dados */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,5),"consultar_base.php","");

  /* 153 - Estat�sticas do Ambiente */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,153),"estatistica/index.php","");

  /* 183 - Configurar dados do ambiente */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,183), "configurar.php", "");

  /* 11 - Cadastro de L�nguas */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,11),"cadastro_linguas.php","");

  /* 171 - Cadastro de texto da Ajuda */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,171),"ajuda/index.php","");

  /* 13 - Contato - NIED - Unicamp */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,13),"mailto:curso@nied.unicamp.br","");

  echo("</ul>\n");
  echo("<p align=\"center\"><a href=\"#\"><img src=\"../imgs/logoNied.gif\" alt=\"Nied\" width=\"28\" height=\"38\" border=\"0\" style=\"margin-right:8px;\" /></a> <a href=\"#\"><img src=\"../imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" width=\"44\" height=\"38\" border=\"0\" style=\"margin-right:6px;\" /></a> <a href=\"#\"><img src=\"../imgs/logoUnicamp.gif\" alt=\"UNICAMP\" width=\"35\" height=\"38\" border=\"0\" /></a></p>\n");
  echo("</td>\n");
  echo("<td width=\"100%\" valign=\"top\" style=\"padding-bottom:40px; padding-top:22px;\">\n");
}


/* **********************************************************************
   ListaCursosDisponiveis - Lista todos os cursos pela base externa.
   Entrada:
   Saida: array $lista[]='cod_curso'
*/
  function ListaCursosDisponiveis(){
  $sock=Conectar("");
  $query = "select cod_curso from Cursos ";
  $res = Enviar($sock, $query);
  $lista = RetornaArrayLinhas($res);
  Desconectar($sock);
  return($lista);
  }

/* **********************************************************************
   CompNomeCoordenador - Compara dois nomes de coordenadores n�o � case sensitive
   Entrada: $a - campo 1
            $b - campo 2
   Saida:Um inteiro que representa o resultado da compara��o
*/
  function CompNomeCoordenador($a, $b)
    {
       $al = strtolower($a['nome']);
       $bl = strtolower($b['nome']);
       return strcmp($al , $bl);
    }

/* **********************************************************************
   CompEmail - Compara dois e-mails de coordenadores n�o � case sensitive
   Entrada: $a - campo 1
            $b - campo 2
   Saida:Um inteiro que representa o resultado da compara��o
*/
  function CompEmail($a, $b)
    {
        $al = strtolower($a['email']);
        $bl = strtolower($b['email']);
        return strcmp($al , $bl);
    }

/* **********************************************************************
   CompNomeCurso - Compara dois nomes de curso n�o � case sensitive
   Entrada: $a - campo 1
            $b - campo 2
   Saida:Um inteiro que representa o resultado da compara��o
*/
function CompNomeCurso($a, $b)
{
  $al = strtolower($a['nome_curso']);
  $bl = strtolower($b['nome_curso']);
  return strcmp($al , $bl);
}

/* **********************************************************************
   CompCod_Curso - Compara dois cod_cursos
   Entrada: $a - campo 1
            $b - campo 2
   Saida:Um inteiro que representa o resultado da compara��o
*/
function CompCod_Curso($a, $b)
{
  $al =$a['cod_curso'];
  $bl =$b['cod_curso'];
  if ($al == $bl)
    return 0;
  return ($al > $bl) ? +1 : -1;
}

/* **********************************************************************
   CompCod_Pasta - Compara duas pastas
   Entrada: $a - campo 1
            $b - campo 2
   Saida:Um inteiro que representa o resultado da compara��o
*/
function CompPasta($a, $b)
{
  $al = strtolower($a['pasta']);
  $bl = strtolower($b['pasta']);
  return strcmp($al , $bl);
}

/* **********************************************************************
   ListaDadosCursos - Lista os dados dos cursos e de seu coordenador
   Entrada: $lista - lista de cursos
   Saida:  $jao_array vetor que contem cod_curso, cod_pasta, nome do coordenador e e-mail do coordenador
*/
function ListaDadosCursos ($lista)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $sock=Conectar("");
  $jao_array = array();
  $total=count($lista);
  
  for ($j = 0; $j < $total; $j++)
  {
    $cod_curso = $lista[$j]['cod_curso'];
    $query = "select Cp.pasta from ".$dbnamebase.".Cursos C,".$dbnamebase.".Cursos_pastas Cp where C.cod_curso=".$cod_curso." and Cp.cod_pasta=C.cod_pasta";
    $res = Enviar($sock, $query);
    $linha = RetornaLinha($res);
    // testa se retornou uma linha vazia
    if($linha){
      $query = "select C.cod_curso, C.nome_curso, Cp.pasta, U.nome, U.email from ";
      $query .= $dbnamebase.".Usuario U, ".$dbnamebase.".Cursos C,".$dbnamebase.".Usuario_curso UC, ".$dbnamebase.".Cursos_pastas Cp where C.cod_curso=".$cod_curso." and UC.cod_curso=C.cod_curso and UC.cod_usuario_global=U.cod_usuario and UC.cod_usuario=C.cod_coordenador and Cp.cod_pasta=C.cod_pasta";
      $res = Enviar($sock, $query);
      $linha = RetornaLinha($res);
      $jao_array[] = $linha;
    }
    // se retornou linha vazia, eh porque o curso nao tem categoria, dai tem que usar uma query diferente
    else{
      $query = "select C.cod_curso, C.nome_curso, U.nome, U.email from ";
      $query .= $dbnamebase.".Usuario U, ".$dbnamebase.".Cursos C,".$dbnamebase.".Usuario_curso UC where C.cod_curso=".$cod_curso." and UC.cod_curso=C.cod_curso and UC.cod_usuario_global=U.cod_usuario and UC.cod_usuario=C.cod_coordenador";
      $res = Enviar($sock, $query);
      $linha = RetornaLinha($res);
      $jao_array[] = $linha;
    }
  }
  Desconectar($sock);
  return ($jao_array);
}

/* **********************************************************************
   ListaDadosParaEmail - Lista os dados para enviar o e-mail com o login e a senha
   Entrada: $sock - conexao com a base geral
            $cod_curso - codigo do curso onde o coordenador ser� o destinat�rio do e-mail
   Saida:  vetor contendo os dados buscados
*/
Function ListaDadosParaEmail($sock,$cod_curso)
{
  $query = "select U.cod_usuario, U.login, U.nome, U.email, C.nome_curso from Usuario U, Usuario_curso UC, Cursos C where UC.cod_curso = ".$cod_curso." and UC.cod_curso = C.cod_curso and UC.cod_usuario = C.cod_coordenador and U.cod_usuario = UC.cod_usuario_global";

  $sock =Conectar("");
  $res = Enviar($sock, $query);

  return RetornaLinha($res);
}

/* **********************************************************************
   MandaMsgcomDados - Envia os dados do coordenador para o e-mail cadastrado.
   Entrada: $cod_curso - codigo do curso onde o coordenador ser� o destinat�rio do e-mail
   Saida: $vet['email'] o e-mail do coordenador
*/
Function MandaMsgcomDados($cod_curso)
{
  global $lista_frases;

  $sock=Conectar("");

  $query="select valor from Config where item='host'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $host=$linha['valor'];

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $raiz_www=$linha['diretorio'];

  $email_admi = DadosAdministracaoParaEmail($sock);
  
  $senha=GeraSenha();
  $senha_crypt = crypt($senha, "AA");

  $vet = ListaDadosParaEmail($sock,$cod_curso);
  
  /* 99 - Informa��es para acesso ao curso no TelEduc */
  $assunto = RetornaFraseDaLista($lista_frases,99);
  $remetente=$email_admi;
  $destino=$vet['email'];
  $endereco=$host.$raiz_www;

  $query="update Usuario set senha='".$senha_crypt."' where cod_usuario = ".$vet['cod_usuario'];
  $result = Enviar($sock,$query);

  Desconectar($sock);

  if (!$result)
  {
    return false;
  }

  /* 102 - Para acessar o curso, a sua Identifica��o �:*/
  /* 103 - e a sua senha �:*/
  /* 104 - O acesso deve ser feito a partir do endereco:*/
  /* 105 - Atenciosamente, Administra��o do Ambiente TelEduc*/

  $mensagem =$vet['nome'].",\n\n";
  $mensagem.=RetornaFraseDaLista($lista_frases,102)." ".$vet['login']." ".RetornaFraseDaLista($lista_frases,103)." ".$senha."\n\n";
  $mensagem.=RetornaFraseDaLista($lista_frases,104)."\nhttp://".$endereco."/cursos/aplic/index.php?cod_curso=".$cod_curso."\n\n";
  $mensagem.=RetornaFraseDaLista($lista_frases,105)."\n";

  $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto);
  MandaMsg($remetente,$destino,$assunto,$mensagem_envio);

  return ($vet['email']);
}

/* **********************************************************************
   BuscaInfoCursos - Lista informa��es do curso cujo cod_curso foi passado.
   Entrada: $cod_curso - codigo do curso cujas informa��es ser�o listadas
   Saida: vetor contendo informa��es sobre o curso cujo cod_curso foi passado
*/
function BuscaInfoCursos($cod_curso){
  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];
  
  $sock =Conectar("");

  $query = "select C.nome_curso, C.cod_curso, Cp.pasta, C.publico_alvo, C.informacoes, U.nome, U.email from ";
  $query .= $dbnamebase.".Usuario U, ".$dbnamebase.".Cursos C,".$dbnamebase.".Usuario_curso UC, ".$dbnamebase.".Cursos_pastas Cp where C.cod_curso=".$cod_curso." and UC.cod_curso=C.cod_curso and UC.cod_usuario_global=U.cod_usuario and Cp.cod_pasta=C.cod_pasta and UC.cod_usuario=C.cod_coordenador";

  $res = Enviar($sock, $query);
  $v = RetornaLinha($res);
  return ($v);
  Desconectar($sock);
}


/* **********************************************************************
   AlteraEmail - Altera o email atual do coordenador
   Entrada: $cod_curso - codigo do curso do coordenador
            $email - Email novo
   Saida: true ou false
*/
Function AlteraEmail($cod_curso,$email)
{
  $sock=Conectar("");

  $query="select UC.cod_usuario_global from Usuario_curso UC,Cursos C where UC.cod_curso = ".$cod_curso." and UC.cod_curso = C.cod_curso and UC.cod_usuario = C.cod_coordenador";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  $cod_usuario = $linha['cod_usuario_global'];
  if ($cod_usuario != ""){
    $query="update Usuario set email='".$email."' where cod_usuario = ".$cod_usuario;
    $result = Enviar($sock,$query);
    Desconectar($sock);
    if ($result){
      return true;
    }
    else{
      return false;
    }
  }
  else{
  return false;
  }
}

/* **********************************************************************
   Retornacor - Retorna  um codigo rgb se $j � par e outro se $j � impar
   Entrada: $j numero da linha
   Saida: #e8e8e8 ou #f8f8f8
*/
function Retornacor($j){
$cor=$j%2;
    if ($cor == 0)
    {
      return("#e8e8e8");
    }
    else
    {
      return("#f8f8f8");
    }
}

/* **********************************************************************
   AlterarEmailDinamic - Altera email do coordenador,dinamicamente
   Entrada: $cod_curso - codigo do curso
            $novo_email - novo valor do email

   Saida: retorna resposta XML
*/
function AlterarEmailDinamic($cod_curso,$novo_email)
{
  $objResponse = new xajaxResponse('ISO-8859-1');

  $res=AlteraEmail($cod_curso,$novo_email);
  $objResponse->addScript("EmailAlterado(".$res.");");

  return $objResponse->getXML();
}
?>
