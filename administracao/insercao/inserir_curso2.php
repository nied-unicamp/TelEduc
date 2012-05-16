<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/inserir_curso2.php

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
  ARQUIVO : administracao/inserir_curso2.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include($bibliotecas."extracao.inc");
  include($bibliotecas."conversor.inc");
  include($bibliotecas."sql_dump.inc");
  include("insercao.inc");
  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");
  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("  }\n");
  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  global $dbuser, $dbpassword;

  VerificaAutenticacaoAdministracao();

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  
  Desconectar($sock);



    // --------------------------------------------------------------
  // - in�cio - OBTEN��O DOS CAMINHOS DAS PASTAS E APLICA��ES UTILIZADAS PELO INSERSOR DE CURSOS
  // descri��o: Obt�m o caminho dos arquivos do curso e dos utilit�rios para
  //            manipula��o de arquivos.
  // . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .

  $dir_arquivos = RetornaDiretorio('Arquivos');
  if (!ExisteArquivo($dir_arquivos))
  {
    // 327: N�o foi poss�vel localizar a pasta de arquivos do curso.
    $msg_erro = RetornaFraseDaLista($lista_frases, 327)."\n";
    // 330: Por favor, verifique as configura��es do ambiente na Administra��o (p�gina inicial) atrav�s da funcionalidade 'Configurar dados do ambiente', 'Configurar endere�o para acesso e estrutura de pastas'.
    $msg_erro .= RetornaFraseDaLista($lista_frases, 330)."\n";
  }

  $caminho_mysqldump = RetornaDiretorio('mysqldump');
  if (!ExisteArquivo($caminho_mysqldump))
  {
    // 328: N�o foi poss�vel localizar o utilit�rio mysqldump.
    $msg_erro = RetornaFraseDaLista($lista_frases, 328)."\n";
    // 330: Por favor, verifique as configura��es do ambiente na Administra��o (p�gina inicial) atrav�s da funcionalidade 'Configurar dados do ambiente', 'Configurar endere�o para acesso e estrutura de pastas'.
    $msg_erro .= RetornaFraseDaLista($lista_frases, 330)."\n";
  }

  $caminho_tar = RetornaDiretorio('tar');
  if (!ExisteArquivo($caminho_tar))
  {
    // 326: N�o foi poss�vel localizar o utilit�rio de arquivamento 'tar'.
    $msg_erro = RetornaFraseDaLista($lista_frases, 326)."\n";
    // 330: Por favor, verifique as configura��es do ambiente na Administra��o (p�gina inicial) atrav�s da funcionalidade 'Configurar dados do ambiente', 'Configurar endere�o para acesso e estrutura de pastas'.
    $msg_erro .= RetornaFraseDaLista($lista_frases, 330)."\n";
  }
  
  $diretorio_extracao = RetornaDiretorio('Extracao');
  
  //Aqui pegamos o caminho do mysql utilizando o comando whereis mysql na shell
    
  if ($msg_erro != "")
    EncerraScript($msg_erro);

  // - fim - OBTEN��O DOS CAMINHOS DAS PASTAS E APLICA��ES UTILIZADAS PELO INSERSOR DE CURSOS
  // --------------------------------------------------------------


  echo("<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 141 - Inser��o de Curso */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,141)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  

  echo("<!-- Tabelao -->\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaInterna\" class=\"tabExterna\">\n");
  echo("<tr>\n");

  echo("<td><ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("<li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='../administracao/index.php?'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("</ul></td></tr>\n");
  echo("<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("<tr><td>\n");

  flush();

  // --------------------------------------------------------------
  // - in�cio - ETAPA 1: RESERVA DE COD_CURSO
  // descri��o: tenta reservar 'cod_curso' na tabela 'Cursos' da base
  //            do ambiente.
  // . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .

  // 146 - Atualizando c�digo do curso
  echo(RetornaFraseDaLista($lista_frases,146)."...\n\n");


  $sock = Conectar("");
  $query = "SELECT MAX(cod_curso)+1 AS novo_codigo FROM Cursos";
  $res = Enviar($sock,$query);
  $linha = RetornaLinha($res);

  if (!isset($linha['novo_codigo']))
    $novo_cod_curso = 1;
  else
    $novo_cod_curso = $linha['novo_codigo'];

  // Reserva o cod_curso na tabela 'Cursos'
  $query = "insert into Cursos (cod_curso) values (".$novo_cod_curso.")";
  // se n�o conseguir reservar na primeira tentativa, incrementa o c�digo
  // e tenta novamente.
  if (!EnviarNC($sock, $query))
  {
    $novo_cod_curso++;
    if (!EnviarNC($sock, $query))
    {
      // 318: Erro de concorr�ncia para reserva de c�digo na tabela 'Cursos'.
      $msg_erro = RetornaFraseDaLista($lista_frases, 318)."\n";
      // 329: Por favor tente novamente.
      $msg_erro .= RetornaFraseDaLista($lista_frases, 329);
      EncerraScript($msg_erro);
    }
  }
  flush();
  /*18 - OK*/
  echo(RetornaFraseDaLista($lista_frases_geral, 18)."<p>\n");
  // - fim - ETAPA 1: RESERVA DE COD_CURSO
  // --------------------------------------------------------------

  // --------------------------------------------------------------
  // - in�cio - OBTEN��O DOS CAMINHOS DOS DUMPS DAS BASES DE DADOS E RESUMO DO CURSO
  // descri��o: Determina o caminho dos arquivos de dump e do arquivo
  //            de resumo de informa��es do curso
  // . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .

  
  $caminho_arquivos_curso="$diretorio_extracao/$cod_pasta";
  $arquivo = ListaArquivos($caminho_arquivos_curso, "*".DUMP_CURSO, 'A', 1);

  $ARQUIVO_CURSO_ANTIGO = $arquivo[0]['Caminho'];  //OK
  
  if (!ExisteArquivo($ARQUIVO_CURSO_ANTIGO))
    // 310: Arquivo de dump da base de dados do CURSO n�o encontrado!.
    $msg_erro = RetornaFraseDaLista($lista_frases, 310)."\n";
  
  if (!ExisteArquivo($dir_arquivos."/".$novo_cod_curso))
    $ret = CriaDiretorio($dir_arquivos."/".$novo_cod_curso);

  if (!$ret)
    // 324: N�o foi poss�vel criar diret�rio de arquivos para o curso a ser inserido.
    EncerraScript(RetornaFraseDaLista($lista_frases, 324));


  // Caminho para nome do arquivo de resumo de informa��es do curso.
  // Esse arquivo cont�m informa��es como nome do curso e a categoria
  // � qual pertencia. A exist�ncia do arquivo � testada posteriormente
  // para escolha da categoria do curso.
  
  $ARQUIVO_RESUMO = $caminho_arquivos_curso."/".NOME_RESUMO;


  if ($msg_erro != "")
    EncerraScript($msg_erro);

  // - fim - OBTEN��O DOS CAMINHOS DOS DUMPS DAS BASES DE DADOS E RESUMO DO CURSO
  // --------------------------------------------------------------

  
  // --------------------------------------------------------------
  // - in�cio - ETAPA 2: C�PIA DOS ARQUIVOS ANEXADOS
  // descri��o: copia os arquivos do curso ou descompacta-os.
  // . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .

  // 144 - Criando diret�rio para os arquivos do curso
  echo(RetornaFraseDaLista($lista_frases,144)."...\n\n");

  CriaDiretorio($dir_arquivos."/".$novo_cod_curso);

  // 18 G - OK
  echo(RetornaFraseDaLista($lista_frases_geral, 18)."<p>\n");

  // 145 - Copiando arquivos para novo diret�rio
  echo(RetornaFraseDaLista($lista_frases,145)."...\n\n");

  flush();

  if (ExisteArquivo($caminho_arquivos_curso."/".NOME_ARQUIVOS))
  {
     // 315: Descompactando arquivos do curso para novo diret�rio:
     echo("<br />".RetornaFraseDaLista($lista_frases, 315)."...\n\n");

     MudarParaDiretorio($dir_arquivos."/".$novo_cod_curso);

     if (!(shell_exec($caminho_tar." -zxvf ".$caminho_arquivos_curso."/".NOME_ARQUIVOS)))
     {
        // 317: Erro ao descompactar os arquivos do curso.
        EncerrarScript(RetornaFraseDaLista($lista_frases, 317));
     }
  }
  else
  {
    // copia todo o diretorio do curso extraido para o novo diretorio no caminho dos
    // cursos atuais (j� com o novo numero do curso)

    $subdirs = RetornaSubDiretorios($caminho_arquivos_curso);
    if (count($subdirs) > 0)
    {
      foreach ($subdirs as $dir)
      if (!(CopiaDiretorio($caminho_arquivos_curso."/".$dir, $dir_arquivos."/".$novo_cod_curso)))
        // 319: Erro na c�pia de arquivos do curso.
        die("<br />".RetornaFraseDaLista($lista_frases, 319)."<p>\n");
    }

  }

  // 18 G - OK
  echo(RetornaFraseDaLista($lista_frases_geral, 18)."<p>\n");

  flush();
  // - fim - ETAPA 2: C�PIA DOS ARQUIVOS ANEXADOS
  // --------------------------------------------------------------

  // --------------------------------------------------------------
  // - in�cio - ETAPA 3: CRIA��O DA BASE DE DADOS DO CURSO
  // descri��o: cria a base de dados do curso e insere os comandos
  //            SQL do novo dump (gerado na etapa 1) e atualiza (pois
  //            um registro j� havia sido inserido na reserva do c�digo)
  //            a tabela 'Cursos' da base de dados do ambiente.
  // . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .

  // 147 - Criando banco de dados...
  echo(RetornaFraseDaLista($lista_frases,147)."...\n\n");

  // cria o novo banco de dados TelEducCursoX (mysql) para insercao do curso desejado

  $query = "create database ".$dbnamecurso.$novo_cod_curso;

  
  if(!EnviarNC($sock, $query))
  {
  	DesfazInsercao($novo_cod_curso, $dbnamecurso, $dir_arquivo);
    // 322: N�o foi poss�vel criar a base de dados do curso.
    $msg_erro = RetornaFraseDaLista($lista_frases, 322)."\n";;
    // 329: Por favor tente novamente.
    $msg_erro .= RetornaFraseDaLista($lista_frases, 329);
    EncerrarScript($msg_erro);
  }

  // 18 G - OK
  echo(RetornaFraseDaLista($lista_frases_geral, 18)."<br />\n");

  // 312: Base de dados do curso:
  echo(RetornaFraseDaLista($lista_frases, 312)." ".$dbnamecurso.$novo_cod_curso."<p>\n");

  // 148 - Inserindo todas as tabelas do curso no banco de dados criado...
  echo(RetornaFraseDaLista($lista_frases,148)."...\n\n");

  // insere todas as tabelas do curso desejado no novo banco de dados criado

  $comando = "mysql -u$dbuser -p$dbpassword ".$dbnamecurso.$novo_cod_curso." < ". $ARQUIVO_CURSO_ANTIGO;

if (shell_exec("$comando")) {
	DesfazInsercao($novo_cod_curso, $dbnamecurso, $dir_arquivo);
	EncerraScript (RetornaFraseDaLista($lista_frases, 336));
}

  /* Recupera os Usuarios */

  /*  $resumo = LeCursoResumo($ARQUIVO_RESUMO);
  $query = "insert into Usuario_curso values (".$resumo['cod_coordenador'].", 1, ".$novo_cod_curso.", 'F', 'ativado', 0)";
  Enviar($sock, $query);*/

  $arquivo = fopen($caminho_arquivos_curso."/Usuario_curso.sql", "r");
  $query = fread($arquivo, filesize($caminho_arquivos_curso."/Usuario_curso.sql"));
  fclose($arquivo);

  $query2 = "update Usuario_curso set cod_curso = ".$novo_cod_curso." where cod_curso = -1;";

  Enviar($sock, $query);
  Enviar($sock, $query2);

  /*18 - OK*/

  MudarDB($sock, $novo_cod_curso);

  //Atualiza A tabela Cursos para o novo cod_curso
  $query = "UPDATE Cursos SET cod_curso=$novo_cod_curso, cod_pasta=$categoria";
  
  if(!EnviarNC($sock, $query)){
  	DesfazInsercao($novo_cod_curso, $dbnamecurso, $dir_arquivo);
	$msg_erro = RetornaFraseDaLista($lista_frases, 336);
	}

  // Testa se h� dados na tabela 'Cursos' da base do CURSO
  $query = "select * from Cursos";
  $id = Enviar($sock, $query);
  $linha_curso = RetornaLinha($id);

  if (!is_array($linha_curso))
  {
    // 331: Tabela 'Cursos' inserida vazia. N�o h� informa��es sobre o curso. Por favor examine o arquivo de dump do ambiente para poss�vel obten��o de informa��es sobre o curso.
    EncerraScript(RetornaFraseDaLista($lista_frases, 331));
  }
  else
  {
    // Atualiza a tabela 'Cursos' da base de dados do AMBIENTE copiando igualzinho � da base do curso
    MudarDB($sock, "");

    $update_set_array = array();
    foreach ($linha_curso as $campo => $valor)
    {
      if (!is_int($campo))
      {	
      	if ($valor!='')
			$update_set_array[] = $campo." = '".$valor."'";
		else
			$update_set_array[] = $campo." = NULL";
      }
    }
    $update_set = implode(", ", $update_set_array);

    $query = "update Cursos set ".$update_set."
                where cod_curso = '".$novo_cod_curso."'";

    if (!Enviar($sock, $query))
    {
      // 321: N�o foi poss�vel atualizar a tabela 'Cursos' na base de dados do ambiente.
  	  DesfazInsercao($novo_cod_curso, $dbnamecurso, $dir_arquivo);
      EncerraScript(RetornaFraseDaLista($lista_frases, 321));
    }
  }

  // 18 G - OK
  echo(RetornaFraseDaLista($lista_frases_geral, 18)."<p>\n");

  flush();

  // - fim - ETAPA 3: CRIA��O DA BASE DE DADOS DO CURSO
  // --------------------------------------------------------------


  $finalizou_insercao = 0;
  // --------------------------------------------------------------
  // - in�cio - ETAPA 4: ATUALIZA��O DAS TABELAS DO CURSO

  // 311: Atualizando vers�o do curso que est� sendo inserido.
  echo ("<b>".RetornaFraseDaLista($lista_frases, 311)."....");

  AtualizaTabelasSequencia($novo_cod_curso);
  // aqui achamos a vers�o em que o curso foi extra�do
  // atualiza vers�o do curso
  AtualizaTabelasVersaoCurso($sock, "", $versao_ambiente_anterior);
  echo(RetornaFraseDaLista($lista_frases_geral, 18)."</b></p>\n");
  // - fim - ETAPA 4: ATUALIZA��O DAS TABELAS DO CURSO
  // --------------------------------------------------------------


  // --------------------------------------------------------------
  // - in�cio - ETAPA 5: FINALIZA��O DA INSER��O

  // Chama fun��o de fachada para finalizar particularidades na inser��o
  FinalizaInsercao($args);

  if ($finalizou_insercao)
  {
    /* 152 - Aten��o : Os arquivos utilizados na inser��o do curso n�o foram apagados... */
    echo("<p>".RetornaFraseDaLista($lista_frases,152).". </p>\n\n");

    /* 113 -  Opera��o completada com sucesso! */
    echo("<b>".RetornaFraseDaLista($lista_frases,113)."<b><p>\n");

  }
    MudarDB($sock, $novo_cod_curso);
  //Agora vamos corrigir as tabelas Sequencia j� seus campos de Auto-Increment est�o corretos


  // - fim - ETAPA 5: FINALIZA��O DA INSER��O
  // --------------------------------------------------------------
  echo("<p>".RetornaFraseDaLista($lista_frases,339).". </p>\n\n");

  echo("</table>\n");
  echo("</td></tr></table>\n");
  echo("</td></tr>\n");
  include("../cursos/aplic/tela2.php");


  echo("</body>\n");
  echo("</html>\n");
?>
