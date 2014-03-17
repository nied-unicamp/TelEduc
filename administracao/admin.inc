<?php

 if (!defined("NOME_RESUMO"))
  define("NOME_RESUMO", "curso.txt");

 if (!defined("NOME_ARQUIVOS"))
  define("NOME_ARQUIVOS", "arquivos.tar.gz");

 if (!defined("DUMP_AMBIENTE"))
  define("DUMP_AMBIENTE", ".ambiente.table");

 if (!defined("DUMP_CURSO"))
  define("DUMP_CURSO", ".curso.table");

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/admin.inc

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
  ARQUIVO : administracao/admin.inc
  ========================================================== */

/* **********************************************************************
   PreparaCabecalhoOpcao - Prepara o cabecalho da op��o da administra��o
   Entrada: Nenhuma
   Saida: Monta o cabe�alho no HTML
*/
function PreparaCabecalhoOpcao($texto)
{
  echo("    <h4>".$texto."</h4>\n");
  echo("    <br />\n");
}

/* **********************************************************************
   PreparaBoldLink - Monta um link na p�gina (completa com RetornaSessionID()
   Entrada: $nome - nome do link
            $endereco - Endereco a ser chamado
            $parametros - parametros a serem passados
   Saida: Monta o a href
*/
function PreparaBoldLink($nome,$endereco,$parametros)
{
  echo("<li><a href=".$endereco."".$parametros." target=_top>".$nome."</a></li>\n");
}

/* **********************************************************************
   RetornaSenhaAtual - Retorna a senha atual criptografada da administracao
   Entrada: Nenhum
   Saida: senha criptografada
*/
function RetornaSenhaAtual()
{
  $sock=Conectar("");
  $query="select valor from Config where item='admtele'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  Desconectar($sock);
  return $linha[0];
}

/* **********************************************************************
   AlteraSenhaAdministracao - Altera a senha atual da administracao
   Entrada: $senha_antiga - Senha antiga n�o criptografada
            $senha - Senha nova J� criptografada
   Saida: nenhum
*/
function AlteraSenhaAdministracao($senha_antiga,$senha)
{
  $senha_antiga=crypt($senha_antiga,"AA");
  if ($senha_antiga==RetornaSenhaAtual())
  {
    $sock=Conectar("");
    $query="update Config set valor='".$senha."' where item='admtele'";
    Enviar($sock,$query);
    $query="update Usuario set senha='".$senha."' where login='admtele'";
    Enviar($sock,$query);
    Desconectar($sock);
  }
}

/* **********************************************************************
   RetornaDadosInstituicao - Retorna os dados da institui��o
   Entrada: nenhuma
   Saida: array com ['nome'],['informacoes'],['link']
*/
function RetornaDadosIntituicao()
{
  $sock=Conectar("");
  $query="select * from Instituicao";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  Desconectar($sock);
  return ($linha);
}

/* **********************************************************************
   AtualizaDadosInstituicao - Atualiza os dados da institui��o
   Entrada: $nome - nome da instituicao
            $informacoes - Informacoes
            $link - Pagina WEB da instituicao
   Saida: nenhum
*/
function AtualizaDadosIntituicao($nome,$informacoes,$link)
{
  $nome=ConverteAspas2Html($nome);
  $informacoes=Enter2Br(ConverteAspas2Html($informacoes));
  $link=ConverteAspas2Html($link);
  $sock=Conectar("");
  $query="select count(*) from Instituicao";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  if ($linha[0]>0)
  {
    $query="update Instituicao set nome='".$nome."',informacoes='".$informacoes."',link='".$link."'";
  }
  else
  {
    $query="insert into Instituicao values ('".$nome."','".$informacoes."','".$link."')";
  }
  Enviar($sock,$query);
  Desconectar($sock);
}

/* **********************************************************************
   RetornaContatos - Retorna os dados dos contatos
   Entrada: nenhuma
   Saida: array com []['nome'],[]['email'],[]['status'] (C-Criar Curso, " "-caso contrario)
*/
function RetornaContatos()
{
  $sock=Conectar("");
  $query="select * from Contatos order by nome";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  Desconectar($sock);
  return ($lista);
}

/* **********************************************************************
   InsereContato - Insere contato
   Entrada: $nome - nome do contato
            $email - email do contato
            $status - [C] Lista de Criar Curso [ ] s� em contatos
   Saida: nenhuma
*/
function InsereContato($nome,$email,$status)
{
  $nome=ConverteAspas2Html($nome);
  $sock=Conectar("");
  $query="insert into Contatos values ('".$nome."','".$email."','".$status."')";
  Enviar($sock,$query);
  Desconectar($sock);
}

/* **********************************************************************
   RemoveContato- remove contato
   Entrada: $nome - nome do contato
   Saida: nenhuma
*/
function RemoveContato($nome)
{
  $nome=ConverteAspas2Html($nome);
  $sock=Conectar("");
  $query="delete from Contatos where nome='".$nome."'";
  Enviar($sock,$query);
  Desconectar($sock);
}

/* **********************************************************************
   EnviarTodasBasesCursos - Envia para todas as bases de curso do ambiente.
   Entrada: $query - nome do contato
   Saida: array[]['NomeBase'] - Nome da Base
          array[]['Res'] - Resultado da consulta na base
*/
function EnviarTodasBasesCursos($query)
{
  global $dbnamecurso;
  $sock=Conectar("");
  $lista_bases=RetornaListaBases($sock);
  Desconectar($sock);
  $i=0;

  if (count($lista_bases)>0)
  {
    foreach($lista_bases as $cod => $linha)
    {
      if (eregi("^".$dbnamecurso,$linha))
      {
        $saida[$i]['NomeBase']=$linha;
        $sock=ConectarDB($linha);
        if ($i==0)
          $res=Enviar($sock,$query);
        if (($res=EnviarNC($sock,$query)))
        {
          $saida[$i]['Campos']=RetornaCampos($res);
          $saida[$i]['Res']=RetornaArrayLinhas($res);
        }
        Desconectar($sock);
        $i++;
      }
    }
  }
  return ($saida);
}

/* **********************************************************************
   RetornaListaCursosAtivos - Retorna a lista de cursos n�o encerrados
   Entrada: nenhuma
   Saida: array [$cod_curso] - Nome do curso
*/
function RetornaListaCursosAtivos()
{
  $ontem=time() - 24*60*60;
        $sock=Conectar("");
  $query="select cod_curso,nome_curso from Cursos where curso_fim>".$ontem." order by nome_curso";
  $res=Enviar($sock,$query);
  Desconectar($sock);
  while (($linha=RetornaLinha($res)))
    $saida[$linha['cod_curso']]=$linha['nome_curso'];
  return $saida;
}

/* **********************************************************************
   EnviarMensagem - Envia a mensagem para os destinatarios.
   Entrada: $cod_curso - codigo do curso a receber a mensagem.
            $coordenadores - 1 se for para enviar para coordenadores
            $formadores - 1 se for para enviar para formadores
            $alunos - 1 se for para enviar para alunos
            $assunto - assunto da mensagem
            $mensagem - mensagem a ser enviada
            $destinos - lista de emails que j� receberam o e-mail.
   Saida: string com lista de emails que receberam e-mail.
*/
function EnviarMensagem($cod_curso,$coordenadores,$formadores,$alunos,$assunto,$mensagem,$destinos)
{
  $sock=Conectar("");

  if ($formadores==1)
  {
    $query="select U.email from Usuario U, Usuario_curso UC where U.cod_usuario=UC.cod_usuario_global and UC.cod_curso = ".$cod_curso." and UC.tipo_usuario='F'";
    $res=Enviar($sock,$query);
    $cont=0;
    while($linha=RetornaLinha($res))
    {
      if ($linha['email']<>"")
      {
        $lista_formadores[$cont]=$linha['email'];
        $cont ++;
      }
    }
  }
  else if ($coordenadores==1)
  {
    $query="select U.email as 'email' from Usuario U, Cursos C where C.cod_coordenador=U.cod_usuario and C.cod_curso=".$cod_curso;
    $res=Enviar($sock,$query);
    $cont=0;
    while($linha=RetornaLinha($res))
    {
      if ($linha['email']<>"")
      {
        $lista_formadores[$cont]=$linha['email'];
        $cont ++;
      }
    }
  }

  if ($alunos==1)
  {
    $query="select U.email from Usuario U, Usuario_curso UC where U.cod_usuario=UC.cod_usuario_global and UC.cod_curso = ".$cod_curso." and UC.tipo_usuario='A'";
    $res=Enviar($sock,$query);
    $cont=0;
    while($linha=RetornaLinha($res))
    {
      if ($linha['email']<>"")
      {
        $lista_alunos[$cont]=$linha['email'];
        $cont ++;
      }
    }
  }

  if (count($lista_formadores)>0 && count($lista_alunos)>0)
    $lista_destino=implode(",",$lista_formadores).",".implode(",",$lista_alunos);
  else if (count($lista_formadores)>0)
    $lista_destino=implode(",",$lista_formadores);
  else
    $lista_destino=implode(",",$lista_alunos);

  /* enviar e-mail */

  $remetente=DadosAdministracaoParaEmail($sock);
  
  Desconectar($sock);

  MandaMsg($remetente,$lista_destino,$assunto,$mensagem);

  $destinos.=",".$lista_destino;

  return $destinos;
}

/* **********************************************************************
   RetornaListaLogins - Retorna a lista de logins dos usu�rios do curso
   Entrada: $cod_curso - codigo do curso
   Saida: array [$cod_usuario]=login do usuario
*/
function RetornaListaLogins($cod_curso)
{
        $sock=Conectar($cod_curso);
  $query="select cod_usuario,login from Usuario";
  $res=Enviar($sock,$query);
  Desconectar($sock);
  while (($linha=RetornaLinha($res)))
    $saida[$linha['cod_usuario']]=$linha['login'];
  return $saida;

}

/* **********************************************************************
   AlterarLoginUsuario - Altera o login do usuario
   Entrada: $cod_curso - codigo do curso
            $cod_usuario - codigo do usu�rio
            $login - novo login a ser colocado
   Saida: true se a altera��o foi conclu�da, false se o login for repetido
*/
function AlterarLoginUsuario($cod_curso,$cod_usuario,$login)
{
  $sock=Conectar($cod_curso);
  $query="select login from Usuario where cod_usuario<>".$cod_usuario." and login='".$login."'";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)==0)
  {
    $query="update Usuario set login='".$login."' where cod_usuario=".$cod_usuario;
    Enviar($sock,$query);
    return true;
  }
  else
    return false;
}

/* *******************************************************************************
  DadosUsuario - Retorna array com dados basicos do usuario
  Entradas: $sock - sock de conexao,
            $cod_curso - codigo do curso,
            $cod_usuario - codigo do usuario
  Saida:    Array com ['nome'] - nome do Usuario
                      ['email'] - endereco email do usuario
                      ['tipo_usuario'] - (char) tipo de usuario - (A)luno,(F)ormador,(C)oordenador
*/
function DadosUsuario($sock,$cod_curso,$cod_usuario)
{
  $query="select nome,email,tipo_usuario from Usuario where cod_usuario=".$cod_usuario;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  if(ECoordenador($sock,$cod_curso,$cod_usuario)==1)
  {
    $linha['tipo_usuario']="C";
  }

  return($linha);
}

/* **********************************************************************
   RetornaFerramentasOrdemMenu - Retorna o codigo e nome das ferramentas
                                 na ordem do menu
   Entrada: nenhuma
   Saida: array[$cod_ferramenta]=nome
*/
function RetornaFerramentasOrdemMenu()
{
  global $cod_lingua_s;
  $sock=Conectar("");
  $query ="select F.cod_ferramenta 'cod_ferramenta', L.texto 'texto', M.posicao 'posicao'";
  $query.=" from Ferramentas F, Menu M, Lingua_textos L where L.cod_lingua=".$cod_lingua_s;
  $query.=" and L.cod_ferramenta=-4 and L.cod_texto=F.cod_texto_nome and F.cod_ferramenta=M.cod_ferramenta";
  $query.=" order by M.posicao";
  $res=Enviar($sock,$query);

  while ($linha=RetornaLinha($res))
    $saida[$linha['cod_ferramenta']]=$linha['texto'];
  Desconectar($sock);
  return $saida;
}

/* **********************************************************************
   ListaFrasesDecrescente - Retorna a lista de frases em ordem decrescente
   Entrada: $cod_lingua - codigo da lingua a ser lida
            $cod_ferramenta - codigo da ferramenta a ser lida
   Saida: array [cod_ferramenta][cod_texto]=texto na lingua pedida
*/
function ListaFrasesDecrescente($cod_lingua,$cod_ferramenta)
{
  $sock=Conectar("");
  $query="select * from Lingua_textos where cod_lingua=".$cod_lingua." and cod_ferramenta=".$cod_ferramenta." order by cod_texto desc";
  $res=Enviar($sock,$query);
  Desconectar($sock);
  while ($linha=RetornaLinha($res))
    $saida[$linha['cod_ferramenta']][$linha['cod_texto']]=$linha['texto'];
  return $saida;
}

/* **********************************************************************
   ListaFrasesNaoPreenchidas - Retorna as frases n�o preenchidas na outra lingua
   Entrada: $cod_lingua - codigo da lingua a ser lida
   Saida: array [cod_ferramenta][cod_texto]=texto na lingua base
*/
function ListaFrasesNaoPreenchidas($cod_lingua)
{
  $sock=Conectar("");
  /* Buscar elementos n�o comuns a segunda lingua */
  $query="select * from Lingua_textos where cod_lingua=1 order by cod_ferramenta,cod_texto";
  $res=Enviar($sock,$query);
  while ($linha=RetornaLinha($res))
    $lista_base[$linha['cod_ferramenta']][$linha['cod_texto']]=$linha['texto'];

  $query="select * from Lingua_textos where cod_lingua=".$cod_lingua." order by cod_ferramenta,cod_texto";
  $res=Enviar($sock,$query);
  while ($linha=RetornaLinha($res))
    $lista_comp[$linha['cod_ferramenta']][$linha['cod_texto']]=$linha['texto'];

  Desconectar($sock);
  /* Ler os elementos n�o preenchidos */


  foreach($lista_base as $cod_ferramenta => $linha)
    foreach($linha as $cod_texto => $texto)
      if (!isset($lista_comp[$cod_ferramenta][$cod_texto]) || $lista_comp[$cod_ferramenta][$cod_texto]=="")
        $saida[$cod_ferramenta][$cod_texto]=$texto;

  return $saida;
}

/* **********************************************************************
   InsereTexto - Insere o texto na ferramenta passada
   Entrada: $cod_lingua - codigo da lingua a ser lida
            $cod_ferramenta - codigo da ferramenta
            $texto - Texto a ser colocado
   Saida: Retorna o c�digo do texto em que foi inserido
*/
function InsereTexto($cod_lingua,$cod_ferramenta,$texto)
{
  $sock=Conectar("");
  $query="select cod_texto from Lingua_textos where cod_lingua=1 and cod_ferramenta=".$cod_ferramenta." order by cod_texto desc limit 1";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $cod_texto=((int)$linha[0])+1;
  $texto=ConverteAspas2Html($texto);
  $query="insert into Lingua_textos values (".$cod_texto.",".$cod_lingua.",".$cod_ferramenta.",'".$texto."')";
  Enviar($sock,$query);
  Desconectar($sock);
  return $cod_texto;
}

/* **********************************************************************
   AlteraTexto - altera o texto na ferramenta passada
   Entrada: $cod_lingua - codigo da lingua
            $cod_ferramenta - codigo da ferramenta
            $cod_texto - codigo do texto a ser alterado
            $texto - Texto a ser alterado
   Saida: codigo do texto alterado
*/
function AlteraTexto($cod_lingua,$cod_ferramenta,$cod_texto,$texto)
{
  $sock=Conectar("");
  $texto=ConverteAspas2Html($texto);
  
  $query="select texto from Lingua_textos where cod_lingua=".$cod_lingua." and cod_ferramenta=".$cod_ferramenta." and cod_texto=".$cod_texto;
  $res=Enviar($sock,$query);
  $numero_de_linhas=RetornaNumLinhas($res);
  
  // Se o texto ja esta no BD, faz update
  if($numero_de_linhas>0) {
    $query="update Lingua_textos set texto='".$texto."' where cod_lingua=".$cod_lingua." and cod_ferramenta=".$cod_ferramenta." and cod_texto=".$cod_texto;
    Enviar($sock,$query);
  } else {    // Se nao, insere no BD
    $query="insert into Lingua_textos values (".$cod_texto.",".$cod_lingua.",".$cod_ferramenta.",'".$texto."')";
    Enviar($sock,$query);
  }

  Desconectar($sock);
  return $cod_texto;
}

/* **********************************************************************
   ApagaTexto - Apaga o texto na ferramenta passada
   Entrada: $cod_ferramenta - codigo da ferramenta
            $cod_texto - codigo do texto a ser alterado
            $texto - Texto a ser alterado
   Saida: nenhuma
   OBS.: Apagar� as frases equivalentes em todas as linguas... use com cuidado.
*/
function ApagaTexto($cod_ferramenta,$cod_texto)
{
  $sock=Conectar("");
  $texto=ConverteAspas2Html($texto);
  $query="delete from Lingua_textos where cod_ferramenta=".$cod_ferramenta." and cod_texto=".$cod_texto;
  Enviar($sock,$query);
  Desconectar($sock);
  return $cod_texto;
}

/* **********************************************************************
   ExisteTexto - Retorna true se existe o registro
   Entrada: $cod_lingua - codigo da lingua
            $cod_ferramenta - codigo da ferramenta
            $cod_texto - codigo do texto a ser alterado
   Saida: true se existir
*/
function ExisteTexto($cod_lingua,$cod_ferramenta,$cod_texto)
{
  $sock=Conectar("");
  $query="select * from Lingua_textos where cod_lingua=".$cod_lingua." and cod_ferramenta=".$cod_ferramenta." and cod_texto=".$cod_texto;
  $res=Enviar($sock,$query);
  Desconectar($sock);
  if (RetornaNumLinhas($res)>0)
    return true;
  return false;
}

/* **********************************************************************
   InsereTextoComCodigo - Insere o texto na ferramenta passada
   Entrada: $cod_lingua - codigo da lingua a ser lida
            $cod_ferramenta - codigo da ferramenta
            $cod_texto - codigo do texto
            $texto - Texto a ser colocado
   Saida: Retorna o c�digo do texto em que foi inserido
*/
function InsereTextoComCodigo($cod_lingua,$cod_ferramenta,$cod_texto,$texto)
{
  $sock=Conectar("");
  $texto=ConverteAspas2Html($texto);
  $query="insert into Lingua_textos values (".$cod_texto.",".$cod_lingua.",".$cod_ferramenta.",'".$texto."')";
  Enviar($sock,$query);
  Desconectar($sock);
  return $cod_texto;
}

/* **********************************************************************
   CriarBaseDoCurso - Obtem o c�digo do proximo curso, e prepara a cria��o das tabelas
                      a partir do arquivo "base_curso/Base_Vazia.table".Caso usuario nao exista, cadastra usuario
                      no banco de dados.
   Entrada: $nome_curso - nome do curso
            $num_alunos - numero de alunos previstos
            $cod_pasta - Codigo da pasta a qual pertence o curso.
            $nome_coordenador - nome do coordenador do curso
            $email - email do coordenador do curso
            $login - login do coordenador do curso
            $senha - senha de acesso do coordenador do curso
            $cod_usuario - codigo do usuario ( caso seja -1, significa que quer-se cadastrar um novo usuario como coordenador )
   Saida: Retorna o c�digo do curso
*/
function CriarBaseDoCurso($nome_curso,$num_alunos,$cod_pasta,$nome_coordenador,$email,$login,$senha,$cod_usuario)
{
  global $dbnamecurso;
  global $cod_lingua_s;
  $sock=Conectar("");
  $query="select max(cod_curso) from Cursos";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $cod_curso=$linha[0] + 1;

  $nome_base=$dbnamecurso.$cod_curso;
  $query="drop database if exists ".$nome_base;
  Enviar($sock,$query);

  $query="create database ".$nome_base;
  Enviar($sock,$query);

  if($cod_usuario == -1)
  {
    $senha_crypt=crypt($senha,"AA");
    $cod_usuario=RetornaProximoCodigo($sock,"Usuario");

    $query="insert into Usuario (cod_usuario,nome,email,login,senha,data_inscricao, cod_lingua) values (".$cod_usuario.",'".$nome_coordenador."','".$email."','".$login."','".$senha_crypt."',".time().", ".$cod_lingua_s.")";
    Enviar($sock,$query);
  }

  $query="insert into Usuario_config (cod_usuario,cod_curso) values (1, ".$cod_curso.")";
  Enviar($sock,$query);

  $query="insert into Usuario_curso (cod_usuario_global,cod_usuario,cod_curso,tipo_usuario,portfolio,data_inscricao) values (".$cod_usuario.",1, ".$cod_curso.", 'F', 'ativado', ".time().")";
  Enviar($sock,$query);

  $query="insert into Usuario_curso (cod_usuario_global,cod_usuario,cod_curso,tipo_usuario,portfolio,data_inscricao) values (-1,-1, ".$cod_curso.", 'F', 'ativado', ".time().")";
  Enviar($sock,$query);

  $query="insert into Cursos (cod_curso,nome_curso,num_alunos,cod_coordenador,cod_pasta,cod_lingua,acesso_visitante) values (".$cod_curso.",'".$nome_curso."',".$num_alunos.",1,".$cod_pasta.",".$cod_lingua_s.",'N')";
  Enviar($sock,$query);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  if ($fh=fopen("base_curso/Base_Vazia.table","r"))
  {
    $content = fread($fh, filesize("base_curso/Base_Vazia.table"));
    $lines = explode(";", $content);

    foreach ($lines as $sql){
	  if (trim($sql) != ""){
		  Enviar($sock, $sql);
	  }
    }
 
  }

  fclose($fh);

  $query="insert into Cursos (cod_curso,nome_curso,num_alunos,cod_coordenador,cod_pasta,cod_lingua,acesso_visitante) values (".$cod_curso.",'".$nome_curso."',".$num_alunos.",1,".$cod_pasta.",".$cod_lingua_s.",'N')";
  Enviar($sock,$query);

  Desconectar($sock);

  return $cod_curso;
}

/* **********************************************************************
   AtualizaConfiguracoes - Atualiza as configuracoes requisitadas por uma lista
   Entrada: []['lingua']    - c�digo da l�ngua-padr�o do ambiente,
            []['admtele']   - senha criptografada do administrador do ambiente,
            []['host']      - hostname ou ip do servidor (utilizado para compor a URL]
                              para acesso enviada aos usu�rios por e-mail),
            []['versao']    - vers�o do ambiente,
            []['adm_nome']  - nome do administrador,
            []['adm_email'] - e-mail do administrador.

   Saida: true se bem sucedido, false do contr�rio.
*/
function AtualizaConfiguracoes($lista)
{
  $sucesso = false;

  if (is_array($lista))
  {
    $sucesso = true;
    foreach ($lista as $item => $valor)
    {
      $query = "update Config set valor = '".$valor."' where item = '".$item."'";
      $sock = Conectar("");
      $res = Enviar($sock, $query);

      $sucesso = $sucesso && $res;
    }
  }
  return($sucesso);
}

/* **********************************************************************
   AtualizaDiretorios - Atualiza os diretorios requisitados por uma lista
   Entrada: []['Arquivos']    - pasta onde ficar�o os arquivos do curso,
            []['ArquivosWeb'] - pasta onde ficar�o os links simb�licos para
                                acesso aos arquivos,
            []['Extracao']    - pasta onde ficar�o os arquivos dos cursos
                                extra�dos,
            []['raiz_www']    - caminho da URL para acesso ao ambiente,
            []['sendmail']    - caminho do aplicativo sendmail,
            []['mysqldump']   - caminho do aplicativo mysqldump,
            ([]['mimetypes']) - caminho do arquivo mime.types do Apache.

   Saida: true se bem sucedido, false do contr�rio.
*/
function AtualizaDiretorios($lista)
{
  $sucesso = false;

  if (is_array($lista))
  {
    $sucesso = true;

    foreach ($lista as $item => $diretorio)
    {
      $query = "update Diretorio set diretorio = '".$diretorio."' where item = '".$item."'";
      $sock = Conectar("");
      $res = Enviar($sock, $query);

      $sucesso = $sucesso && $res;
    }
  }
  return($sucesso);
}

/* **********************************************************************
   RetornaTodosDiretorios - Retorna todos diret�rios pedidos
   Entrada: vazio
   Saida: []['Arquivos'] - pasta onde ficam os arquivos do curso,
          []['ArquivosWeb'] - pasta onde ficam os links simb�licos,
          []['sendmail'] - caminho do sendmail,
          []['raiz_www'] - caminho para acesso via web,
          []['Extracao'] - pasta onde ficam os arquivos do curso extra�do,
          []['mysqldump'] - caminho do mysqldump,
          []['mimetypes'] - caminho do arquivo mime.types.
*/
function RetornaTodosDiretorios()
{
  $sock = Conectar("");
  $query = "select * from Diretorio";
  $res = Enviar($sock,$query);
  $lista = RetornaArrayLinhas($res);
  if (is_array($lista))
  {
    foreach ($lista as $idx => $linha)
    {
      $retorno[$linha['item']] = $linha['diretorio'];
    }
  }
  Desconectar($sock);
  return $retorno;
}

/* **********************************************************************
   RetornaDiretorio - Retorna o Diretorio pedido
   Entrada: $item - item da tabela de diretorios a ser retornado
   Saida: Retorna o diretorio
*/
function RetornaDiretorio($item)
{
  $sock=Conectar("");
  $query="select diretorio from Diretorio where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  Desconectar($sock);
  return $linha['diretorio'];
}

/* **********************************************************************
   RetornaConfig - Retorna dado da tabela Config
   Entrada: $item - item da tabela de a ser retornado
   Saida: Retorna o valor correspondente ao item requisitado.
*/
function RetornaConfig($item)
{
  $sock=Conectar("");
  $query="select valor from Config where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  Desconectar($sock);
  return $linha['valor'];
}

/* **********************************************************************
   RetornaCursosExtraiveis - Retorna lista de cursos que podem ser extraidos (terminados)
   Entrada: nenhuma
   Saida: Retorna array com [$cod_curso]=nome_curso
*/
function RetornaCursosExtraiveis()
{
  $sock=Conectar("");
  $query="select cod_curso,nome_curso from Cursos where curso_fim<".(time()+86399)." order by nome_curso";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  Desconectar($sock);
  if (count($lista)>0)
    foreach($lista as $cod => $linha)
      $saida[$linha['cod_curso']]=$linha['nome_curso'];
  return $saida;
}


/* **********************************************************************
   RetornaCursosInseriveis - Retorna lista de cursos que podem ser inseridos
   Entrada: nenhuma
   Saida: Retorna array com [$cod_curso]=nome_curso
*/
function RetornaCursosInseriveis()
{
  global $dbnamebasecurso;

  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $diretorio_extracao = RetornaDiretorio('Extracao');
  $lista_diretorios = RetornaSubDiretorios($diretorio_extracao);

  //  echo '<pre>'; echo__FILE__; echo ' '; echo __LINE__; echo ' '; var_dump($lista_diretorios); echo '</pre>';

  /*

  //
  // POR QUE COMENTEI ESTE CODIGO
  //
  // LEONEL - 2003-DEZ-18
  // O nome e o codigo do curso nao devem ser buscados do arquivo de dump do ambiente
  // Vou tentar busca-los do arquivo de resumo

  if (count($lista_diretorios) > 0)
    foreach ($lista_diretorios as $diretorio)
    {
       $arquivo = $diretorio_extracao.'/'.$diretorio.'/'.$dbnamebase.DUMP_AMBIENTE;

       $fd = fopen ($arquivo, "r");
       $texto_procurado = 'INSERT INTO Cursos VALUES ('.$diretorio.',';
       $achou_nome = 0;
       while ((!feof($fd)) && (!$achou_nome))
       {
           $buffer = fgets($fd, 4096);

           if (strstr($buffer, $texto_procurado))
           {
             $partes = explode("'", $buffer);
             $nome_do_curso = $partes[1];
             $achou_nome = 1;
           }
       }
       fclose ($fd);

      $saida[$diretorio]=$nome_do_curso;
    }
  */

  if (count($lista_diretorios) > 0) {
      $saida = array();

      foreach ($lista_diretorios as $diretorio => $indice) {
          $arquivo = $diretorio_extracao.'/'.$indice.'/'.NOME_RESUMO;
      		if ( ($fd = fopen($arquivo, 'r')) !== false ) {
              $achou = false;
			     $saida[$indice]['data_extracao'] = date ("d/m/Y - H:i",filectime ($arquivo));
              // somente listamos o curso como possivel de inserir se encontrarmos o arquivo de resumo
              while ( !feof($fd)) {
                 $buffer = fgets($fd, 4096);
                 if (strstr($buffer, 'nome_curso=')) {
                     $partes = explode('=', $buffer);
                     $nome_do_curso = rtrim($partes[1]);
                     $saida[ $indice ]['nome_curso'] = $nome_do_curso;
                 }
                 if (strstr($buffer, 'versao_teleduc=')) {
                     $partes = explode('=', $buffer);
                     $versao_do_curso = $partes[1];
                     $saida[$indice]['versao'] = $versao_do_curso;
      	         }
		 if (strstr($buffer, 'curso_inicio=')) {
                     $partes = explode('=', $buffer);
                     $curso_inicio = rtrim($partes[1]);
                     $saida[ $indice ]['curso_inicio'] = $curso_inicio;
                 }
		 if (strstr($buffer, 'curso_fim')) {
                     $partes = explode('=', $buffer);
                     $curso_fim = rtrim($partes[1]);
                     $saida[ $indice ]['curso_fim'] = $curso_fim;
                 }
              }
          }
      }
  }

  return $saida;
}


/* **********************************************************************
   RetornaCategorias - Retorna as categorias que um curso pode pertencer
   Entrada: nenhum
   Saida: array com [$cod_pasta]='nome da pasta'
*/

function RetornaCategorias()
{
  $sock=Conectar("");
  $query="select * from Cursos_pastas order by pasta";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  Desconectar($sock);
  if (count($lista)>0)
    foreach($lista as $cod => $linha)
      $saida[$linha['cod_pasta']]=$linha['pasta'];
  return $saida;
}

/* **********************************************************************
   InserirCategoriaDinamic - Insere a categoria e retorna o c�digo da mesma
   Entrada: $nome_pasta - nome da pasta
   Saida: resposta XML
*/
function InserirCategoriaDinamic($nome_pasta,$texto,$textoErro)
{
  $objResponse = new xajaxResponse();

  $sock=Conectar("");
  $query="select * from Cursos_pastas where pasta like '".$nome_pasta."'";
  $resposta=Enviar($sock,$query);

  /* Descobre o cod_pasta da categoria a ser incluida */
  $query="select max(cod_pasta) from Cursos_pastas";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);
  $cod_pasta = $linha[0] + 1;

  /* Se a tabela JÁ teve dados, mas agora está vazia, o valor de max(cod_pasta) 
     vai ser 1, porém, o mysql continua o indice de onde parou, o que resulta 
     num cod_pasta != de 1 */
  if ($cod_pasta == 1){
    $query = "ALTER TABLE Cursos_pastas AUTO_INCREMENT = 1";
    Enviar($sock, $query);
  }

  /* Carrega as frases dos botões pra colocar no html */
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  $frase = RetornaFraseDaLista($lista_frases_geral, 19);
  $frase2 = RetornaFraseDaLista($lista_frases_geral, 1);

  // FEEDBACK

  if (RetornaNumLinhas($resposta) == 0)
  {
    $query="insert into Cursos_pastas (pasta) values ('".$nome_pasta."')";
    Enviar($sock,$query);

    /* 0 - Sucesso 1 - Fracasso */
    $objResponse->script("HTMLNovaCategoria('$nome_pasta', '$cod_pasta', '$frase', '$frase2');");
    $objResponse->script("escondetr(1)");
    $objResponse->call("mostraFeedback", $texto, 'true');
  } else {
    $objResponse->call("mostraFeedback", $textoErro, 'false');
  }

  return $objResponse;
}

/* **********************************************************************
   InserirCategoria - Insere a categoria, dinamicamente
   Entrada: $nome_pasta - nome da pasta
   Saida: codigo da pasta da categoria criada
*/
function InserirCategoria($nome_pasta)
{
  $sock=Conectar("");

  $query="select cod_pasta from Cursos_pastas where pasta like '".$nome_pasta."'";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);

  if(!$linha)
  /*Cria uma nova pasta*/
  {
    /* Descobre o cod_pasta da categoria a ser incluida */
    $query="select max(cod_pasta) from Cursos_pastas";
    $res = Enviar($sock,$query);
    $linha = RetornaLinha($res);
    $cod_pasta = $linha['cod_pasta'] + 1;
    $query="insert into Cursos_pastas (pasta) values ('".$nome_pasta."')";
    Enviar($sock,$query);
  }
  else
  {
    $cod_pasta = $linha['cod_pasta'];
  }

  Desconectar($sock);

  return $cod_pasta;
}

/* **********************************************************************
   TrocaCategoriaDinamic -  Troca a pasta (categoria) a que pertence o curso, dinamicamente
   Entrada: $cod_curso, $cod_pasta
   Saida: nenhuma
*/
function TrocaCategoriaDinamic($cod_curso,$cod_pasta,$texto)
{
  $objResponse = new xajaxResponse();

  AtualizaPastaCursos($cod_curso,$cod_pasta);
  $objResponse->call("mostraFeedback", $texto, 'true');

  return $objResponse;
}

/* **********************************************************************
   RenomearCategoria - Renomeia a categoria
   Entrada: $cod_pasta
            $nome
   Saida: nenhuma
*/
function RenomearCategoria($cod_pasta,$nome)
{
  $sock=Conectar("");
  $query="update Cursos_pastas set pasta='".$nome."' where cod_pasta=".$cod_pasta;
  Enviar($sock,$query);
  Desconectar($sock);
}

/* **********************************************************************
   RetornaCursos - Retorna lista de cursos
   Entrada: nenhuma
   Saida: Retorna array com [$cod_curso]=nome_curso
*/
function RetornaCursos()
{
  $sock=Conectar("");
  $query="select * from Cursos order by nome_curso,cod_pasta";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  Desconectar($sock);
  if (count($lista)>0)
    foreach($lista as $cod => $linha)
      $saida[$linha['cod_curso']]=$linha;
  return $saida;
}

/* **********************************************************************
   AtualizaPastaCursos - atualiza a pasta (categoria) a que pertence o curso
   Entrada: $cod_curso, $cod_pasta
   Saida: nenhum
*/
function AtualizaPastaCursos($cod_curso, $cod_pasta)
{
  $query="update Cursos set cod_pasta=".$cod_pasta." where cod_curso=".$cod_curso;
  $sock=Conectar("");
  Enviar($sock,$query);
  Desconectar($sock);
  $sock=Conectar($cod_curso);
  Enviar($sock,$query);
  Desconectar($sock);
}

/* *******************************************************************************
  AlteraLinguaUsuario - Altera a lingua padr�o do usu�rio
  Entradas: $sock - sock de conexao,
            $cod_usuario - codigo do usuario
            $cod_lingua - codigo da lingua a ser usada
  Saida: nenhuma
*/
function AlteraLinguaUsuario($sock,$cod_usuario,$cod_lingua)
{
  $query="update Usuario set cod_lingua=".$cod_lingua." where cod_usuario=".$cod_usuario;
  Enviar($sock,$query);
}

function AtualizaIdiomaDinamic($cod_lin)
{
  $objResponse = new xajaxResponse();

  $sock=Conectar("");
  $query="update Usuario set cod_lingua=".$cod_lin." where cod_usuario = -1";
  $res=Enviar($sock,$query);
  Desconectar($sock);

  $objResponse->script("window.location.reload();");

  //  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}


function ApagarDinamic($cod_pasta)
{
  $objResponse = new xajaxResponse();
    
  ApagarCategoria($cod_pasta);
  $objResponse->script("document.getElementById('tr_master').parentNode.removeChild(document.getElementById('tr_".$cod_pasta."'))");
  $sock=Conectar("");
  $query="select * from Cursos_pastas";
  $res = Enviar($sock,$query);
  $res = RetornaLinha($res);
  if(!$res){
  	$objResponse->script("escondetr(0);");
  }
  Desconectar($sock);
  //  Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

/* **********************************************************************
   ApagarCategoria - Apaga a categoria passada
   Entrada: $cod_pasta
   Saida: nenhuma
*/
function ApagarCategoria($cod_pasta)
{

  $sock=Conectar("");
  $query="delete from Cursos_pastas where cod_pasta=".$cod_pasta;
  Enviar($sock,$query);
  $query="update Cursos set cod_pasta=NULL where cod_pasta=".$cod_pasta;
  Enviar($sock,$query);
  Desconectar($sock);
}

function EditarTituloDinamic($cod_curso, $cod_pasta, $novo_nome)
{
  $objResponse = new xajaxResponse();

  RenomearCategoria($cod_pasta,htmlentities($novo_nome));

  // Imprime no div valores do formulário
  $objResponse->assign("titulo_".$cod_pasta, "innerHTML", htmlentities($novo_nome));
  $objResponse->assign("titulo_".$cod_pasta, "style.fontWeight", "bold");

  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  return $objResponse;
}

if (!function_exists("DecodificaString")){
  function DecodificaString($id, $texto, $local){
  
    $objResponse = new xajaxResponse();
  
    $objResponse->assign($id, $local, html_entity_decode($texto));
  
    return $objResponse;
  }
}

function RetornaListaTodosLogins($sock)
{

  $query="select login,email from Usuario where cod_usuario <> -1";
  $res = Enviar($sock,$query);

  return RetornaArrayLinhas($res);

}

function SugerirLoginDinamic($pal,$frase)
{
  $objResponse = new xajaxResponse();

  $sock = Conectar("");
  $logins_usuarios = RetornaListaTodosLogins($sock);
  Desconectar($sock);

  $count=0;
  $hint="";
  for($i=0; $i<count($logins_usuarios) && $count < 10 && $logins_usuarios != ""; $i++)
  {
    if($pal != "" && ($pal == substr($logins_usuarios[$i]['login'],0,strlen($pal)) || $pal == substr($logins_usuarios[$i]['email'],0,strlen($pal))))
    {
      $hint .= "<li><a href=\"#\" onclick=\"javascript:document.getElementById('login').value='".$logins_usuarios[$i]['login']."';document.getElementById('divSugs').style.display = 'none';document.getElementById('tr_sugs').style.display = 'none';\">".$logins_usuarios[$i]['login']."&nbsp;-&nbsp;(".$logins_usuarios[$i]['email'].")</a></li>";
      $count++;
    }
  }

  if($hint != "")
  { 
    $hint = "<ul><li><i>".$frase."</i></li>".$hint."</ul>";
    $objResponse->script("document.getElementById('divSugs').style.display = '';");
    $objResponse->script("document.getElementById('tr_sugs').style.display = '';");
  }
  else 
  {
    $objResponse->script("document.getElementById('divSugs').style.display = 'none';");
    $objResponse->script("document.getElementById('tr_sugs').style.display = 'none';");
  }

  $objResponse->assign("divSugs", "innerHTML", $hint);

  return $objResponse;
}

/* **********************************************************************
   RetornaCodUsuarioLogin - Retorna codigo do usuario que possua o login passado.
   Entrada: $sock - conexão com a base
            $login - login do usuario
   Saida: Retorna codigo do usuario que possua o login passado; retorna 0 caso o login nao seja encontrado.
*/
function RetornaCodUsuarioLogin($sock,$login)
{

  $query = "select cod_usuario from Usuario where login = '".$login."'";
  $res = Enviar($sock,$query);

  if($res != "")
  {
    $linha = RetornaLinha($res);
    return $linha['cod_usuario'];
  }
  else
    return 0;
}

/***********************************************************************
   ExisteExisteLoginEmail - Verifica se o email e/ou login passados ja se encontram cadastrados no banco
   Entrada: $login - login do usuario
            $email - email do usuario 
            $escolha - variavel que indica se o adm quer escolher como coordenador um usuario ja cadastrado
            
   Saida: Retorna true se o email se encontra no banco; retorna false caso contrario.
*/
function ExisteLoginEmail($login,$email,$escolha)
{
  $objResponse = new xajaxResponse();

  $sock=Conectar("");

  $query = "select * from Usuario where login='".$login."' or email = '".$email."'" ;
  $res = Enviar($sock,$query);

  Desconectar($sock);

  $res = RetornaLinha($res);
  if($res != "" && $escolha == "nao" && $email != "" && $login != "")
    $objResponse->script("EmailLoginRepetido();");
  else
    $objResponse->script("TestaForm();");

  return $objResponse;
}

/********************************************************************
  Fun�ao RetornaUsuariosCadastrados - Retorna todos os usuarios cadastrados no servidor
  Entradas:  $sock - conexao com a base geral
             $ordem - indica em que ordem aparecerao os usuarios
  Saida:    array contendo os dados de todos os usuarios
********************************************************************/
function RetornaUsuariosCadastrados($sock,$ordem)
{
  $query="select cod_usuario,nome,data_inscricao,login,email from Usuario where cod_usuario <> -1 order by ".$ordem;
  $res=Enviar($sock,$query);

  return RetornaArrayLinhas($res);  
}

/********************************************************************
  Fun�ao TrocaLoginUsuario - Troca login de um usuario
  Entradas:  $login_antigo - login atual
             $novo_login - novo login
  Saida:    array contendo os dados de todos os usuarios
********************************************************************/
function TrocaLoginUsuario($login_antigo,$novo_login)
{
  $objResponse = new xajaxResponse();

  $sock = Conectar("");
  $query = "select * from Usuario where login='".$novo_login."'";
  $res=Enviar($sock,$query);

  if(RetornaLinha($res) != "")
    // novo login ja existia
    $res = 0;
  else
  {
    $query = "select * from Usuario where login='".$login_antigo."'";
    $res=Enviar($sock,$query);

    if(RetornaLinha($res) == "")
       // login antigo errado
      $res = 1;
    else
    {
      $query = "update Usuario set login='".$novo_login."' where login='".$login_antigo."'";
      $res = Enviar($sock,$query);

      // novo login cadastrado com sucesso
      $res = 2;
    }
  }

  Desconectar($sock);

  $objResponse->script("RespostaUsuario(".$res.");");

  return $objResponse;
}

/********************************************************************
  Fun�ao AlteraTextoDinamic - Altera texto da base de dados, dinamicamente
  Entradas:  $cod_texto - codigo do texto
             $cod_ferramenta - codigo da ferramenta
             $cod_lin - codigo da lingua
             $texto - novo valor do texto
  Saida:     resposta XML
********************************************************************/
function AlteraTextoDinamic($cod_texto,$cod_ferramenta,$cod_lin,$texto)
{
  $objResponse = new xajaxResponse();

  $num=AlteraTexto($cod_lin,$cod_ferramenta,$cod_texto,$texto);
  $objResponse->script("RespostaUsuario('A',".$num.",'".$texto."');");

  return $objResponse;
}

/********************************************************************
  Fun�ao InsereTextoDinamic -Insere texto na base de dados, dinamicamente
  Entradas:  $cod_ferramenta - codigo da ferramenta
             $cod_lin - codigo da lingua
             $texto - texto a ser inserido
  Saida:     resposta XML
********************************************************************/
function InsereTextoDinamic($cod_ferramenta,$cod_lin,$texto)
{
  $objResponse = new xajaxResponse();

  $num=InsereTexto($cod_lin,$cod_ferramenta,$texto);
  $objResponse->script("RespostaUsuario('I',".$num.",'".$texto."');");

  return $objResponse;
}

/********************************************************************
  Fun�ao ApagaTextoDinamic -Apaga texto da base de dados, dinamicamente
  Entradas:  $cod_texto - codigo do texto
             $cod_ferramenta - codigo da ferramenta
  Saida:     resposta XML
********************************************************************/
function ApagaTextoDinamic($cod_texto,$cod_ferramenta)
{
  $objResponse = new xajaxResponse();

  $num=ApagaTexto($cod_ferramenta,$cod_texto);

  return $objResponse;
}

/********************************************************************
  Fun�ao AtualizaDadosInstDinamic - Atualiza os dados institucionais
  Entradas:  $nome - nome da instituicao
             $informacoes - informacoes sobre a instituicao
             $link - endereco na web da instituicao
  Saida:     resposta XML
********************************************************************/
function AtualizaDadosInstDinamic($nome,$informacoes,$link)
{
  $objResponse = new xajaxResponse();

  AtualizaDadosIntituicao($nome,$informacoes,$link);
  $objResponse->script("RespostaUsuario();");

  return $objResponse;
}

/********************************************************************
  Fun�ao AtualizaConfiguracoesDinamic - Atualiza forma de solicitação de um curso
  Entradas:  $curso_form - variavel se a abertura do curso sera via formulario ("sim") ou via email ("nao")
             $normas - normas ou requisitos para abertura de um curso
  Saida:     resposta XML
********************************************************************/
function AtualizaConfiguracoesDinamic($curso_form,$normas)
{
  $objResponse = new xajaxResponse();

  $lista_conf = array ("curso_form" => LimpaTags($curso_form), "normas" => $normas);
  $sucesso = AtualizaConfiguracoes($lista_conf);
  $objResponse->script("RespostaUsuario(".$sucesso.");");

  return $objResponse;
}

/********************************************************************
  Fun�ao AtualizaPastasDinamic - Atualiza endereços para acessos e estruturas das pastas do teleduc
  Entradas:  $host - endereço do host
             $arquivos - endereco da pasta onde se encontram os arquivos
             $arquivosweb - endereco da pasta onde se encontra os links simbolicos dos arquivos
             $extraidos - endereco da pasta onde se encontram os cursos removidos
             $raizwww - caminho via browser
             $sendmail -  caminho do sendmail
             $mysqldump - caminho do mysqldump
             $mimetypes - caminho do arquivo "mime.types" do Apache para resolução do arquivos 
  Saida:     resposta XML
********************************************************************/
function AtualizaPastasDinamic($host,$arquivos,$arquivosweb,$extraidos,$raizwww,$sendmail,$mysqldump,$tar,$mimetypes)
{
  $objResponse = new xajaxResponse();

  $lista_conf = array("host" => LimpaTags($host));
  $lista_dir  = array("Arquivos"  => LimpaTags($arquivos),  "ArquivosWeb" => LimpaTags($arquivosweb),
                      "Extracao"  => LimpaTags($extraidos), "raiz_www"    => LimpaTags($raizwww),
                      "sendmail"  => LimpaTags($sendmail),  "mysqldump"   => LimpaTags($mysqldump),
                      "mimetypes" => LimpaTags($mimetypes), "tar"         => LimpaTags($tar)
                     );

  $sucesso = (AtualizaConfiguracoes($lista_conf) && AtualizaDiretorios($lista_dir));
  $objResponse->script("RespostaUsuario(".$sucesso.");");

  return $objResponse;
}

/********************************************************************
  Fun�ao AtualizaDadosAdmDinamic - Atualiza dados do administrador
  Entradas:  $adm_nome - nome do administrador
             $adm_email - email do administrador
  Saida:     resposta XML
********************************************************************/
function AtualizaDadosAdmDinamic($adm_nome,$adm_email)
{
  $objResponse = new xajaxResponse();

  $lista_conf = array ("adm_nome" => $adm_nome, "adm_email" => $adm_email);
  $sucesso = (AtualizaConfiguracoes($lista_conf));
  

  return $objResponse;
}

/********************************************************************
  Fun�ao AtualizaSenhaAdmDinamic - Atualiza senha do administrador
  Entradas:  $senha_antiga - senha antiga
             $senha_nova - nova senha
  Saida:     resposta XML
********************************************************************/
function AtualizaSenhaAdmDinamic($senha_antiga,$senha_nova)
{
  $objResponse = new xajaxResponse();

  $senha_antiga_cript=crypt($senha_antiga,"AA");
  $senha_nova=crypt($senha_nova,"AA");

  if ($senha_antiga_cript != RetornaSenhaAtual())
  {
    $objResponse->script("RespostaUsuario(0);");
  }
  else
  {
    AlteraSenhaAdministracao($senha_antiga,$senha_nova);
    $objResponse->script("RespostaUsuario(1);");
  }

  return $objResponse;
}

/********************************************************************
  Fun�ao CadastraResponsavelDinamic - Cadastra responsavel pelo ambiente
  Entradas:  $nome - nome do responsavel
             $email - email do responsavel
             $status - indica se o responsavel pode autorizar abertura de curso
  Saida:     resposta XML
********************************************************************/
function CadastraResponsavelDinamic($nome,$email,$status)
{
  $objResponse = new xajaxResponse();

  InsereContato($nome,$email,$status);
  $objResponse->script("RespostaUsuario(1);");

  return $objResponse;
}

/********************************************************************
  Fun�ao RemoveResponsavelDinamic - Remove um responsavel pelo ambiente
  Entrada:  $nome - nome do responsavel
  Saida:     resposta XML
********************************************************************/
function RemoveResponsavelDinamic($nome)
{
  $objResponse = new xajaxResponse();

  RemoveContato($nome);

  return $objResponse;
}

/********************************************************************
  Fun�ao RetornaNomeEmailUsuario - Retorna nome e o email do usuario
  Entrada:  $sock - conexão com a base de dados
            $login - login do usuario
  Saida:    array contendo nome e email do usuario
********************************************************************/
function RetornaNomeEmailUsuario($sock,$login)
{
  $query = "select nome,email from Usuario where login='".$login."'";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);

  return $linha;
}

/********************************************************************
  Fun�ao RetornaNomeEmailUsuario - Retorna nome e o email do usuario
  Entrada:  $sock - conexão com a base de dados
            $login - login do usuario
  Saida:    array contendo nome e email do usuario
********************************************************************/
function VerificaSeLoginExiste($sock,$login)
{
  $query = "select nome from Usuario where login='".$login."'";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);

  return $linha;
}

/* **********************************************************************
   RetornaConfiguracao - Retorna o conte�do de um item da configura��o
   Entrada: $sock - sock da base externa
            $item - item a ser buscado
   Saida: conte�do
*/
function RetornaConfiguracao($sock,$item)
{
  $query="select valor from Config where item='".$item."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  return ($linha['valor']);
}

?>
