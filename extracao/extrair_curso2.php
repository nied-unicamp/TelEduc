<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/extracao/extrair_curso2.php

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
  ARQUIVO : administracao/extracao/extrair_curso2.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."extracao.inc");
  include("extracao.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");
  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("  }\n");
  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");


  $lista_frases=RetornaListaDeFrases($sock,-5);

  Desconectar($sock);


  // Ser� avaliado antes da chamada � fun��o RetornaCaminhoExtracao
  $caminho_restricao = "\$campos_restricao = array ('codigo' => \$codigo_extracao);";


  $dir_arquivos = RetornaDiretorio('Arquivos');
  $caminho_mysqldump = RetornaDiretorio('mysqldump');
  $caminho_tar = RetornaDiretorio('tar');
  $caminho_base_extracao = RetornaDiretorio('Extracao');

  echo("<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 4 - Extra��o de Curso */
  echo("  <h4>".RetornaFraseDaLista($lista_frases,4)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("  &nbsp;<!-- Tabelao -->\n");
  echo("  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\" id=\"tabelaExterna\">\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  echo("      <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='extrair_curso.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("      <tr>\n");
  echo("        <td>\n");
   


  // ******** Aqui come�a o c�digo para atualizar as tabelas de cursos extra�dos **********

  // 300 - Fase 1 - Inserindo na base os dados do curso extra�do
  echo("<b>".RetornaFraseDaLista($lista_frases,300)."</b><p>\n");

  $sock = Conectar("");
  
  // 1 - OBTER DADOS DAS TABELAS
  // Obt�m os dados relevantes do curso a ser extra�do
  // $dados_curso = RetornaDadosCurso($sock, $cod_curso);
  $versao = RetornaVersaoAmbiente($sock);

  // 2 - INSERIR ENTRADAS EM 'Cursos_extraidos'
  // Atualiza os dados dos cursos extra�dos
  // Reserva c�digo na tabela Cursos_extraidos
  $codigo_extracao = RetornaProximoCodigo($sock, "Cursos_extraidos");
  // Copia os dados do curso para a tabela Cursos_extraidos
  CopiaDadosCursoParaExtraidos($sock, $codigo_extracao, $cod_curso, $versao);
  // Copia as ferramentas compartilhadas para a tabela Cursos_extraidos_compart
  CopiaFerrCompartCursoParaExtraidos($sock, $codigo_extracao, $cod_curso);

  /********* Agora copiamos os arquivos do curso para a pasta de extra�dos *******/

  // 3 - RESOLVER O CAMINHO PARA EXTRACAO
  // Obt�m o caminho completo para extra��o
  eval($caminho_restricao);
  $caminho = RetornaCaminhoExtracao($sock, $campos_restricao, $caminho_base_extracao);

  // Atualiza o campo caminho da tabela 'Cursos_extraidos'
  AtualizaCaminho($sock, $caminho, $codigo_extracao);

  // 301 - Fase 1 - Conclu�da
  echo("<b>".RetornaFraseDaLista($lista_frases,301)."</b><p>\n");

  // 4 - COPIAR ARQUIVOS
  /* 111 - Fase 2 - Copiando dados e arquivos */
  echo("<b>".RetornaFraseDaLista($lista_frases,111)."</b><p>\n");

  echo("\n                                                                                                                                                                        \n");
  flush();

  /* � necess�ria essa nova fun��o pois o mkdir s� cria diret�rios com um n�vel de profundidade */
  /* para aninhanemnto � necess�rio um par�metro ao mkdir que n�o est� dispon�vel na fun��o original */
  CriaDiretorio($caminho);

  /* 119 - Copiando: */
  /* 120 - Arquivos do Curso */
  echo(RetornaFraseDaLista($lista_frases,119)." ".RetornaFraseDaLista($lista_frases,120)." ... \n\n");

  flush();

  /*   fun��o necess�ria pois a original invariavelmente mantem o nome da �ltima pasta do caminho, */
  /* o que n�o � desej�vel nesse caso */
  CopiaArquivosDiretorio($dir_arquivos."/".$cod_curso."/", $caminho);
  /* 304 - Ok */
  echo(RetornaFraseDaLista($lista_frases,304)."<p>\n");

    /* se usu�rio optou por compacta��o dos arquivos, obedece */
  if (CompactarCurso($sock))
  {
     /* 302 - Compactando arquivos do curso */
     echo(RetornaFraseDaLista($lista_frases,302)." \n\n");
     MudarParaDiretorio($caminho);

     $t = RetornaArrayDiretorio('.');

     if (is_array($t))
     {
       if (!(shell_exec($caminho_tar." -zcvf ".$caminho."/".NOME_ARQUIVOS." *")))
       {
          /* 303 - Erro interno ao compactar arquivos */
          die(RetornaFraseDaLista($lista_frases,303));
       }
       // Remove os subdiret�rios.
       $subs = RetornaSubDiretorios($caminho);
       if (count($subs)>0)
       {
          foreach($subs as $dir)
          {
            RemoveDiretorio($caminho."/".$dir);
          }
       }
     }
     /*304 - Ok*/
     echo(RetornaFraseDaLista($lista_frases,304)."<p>\n");
  }

  /* 119 - Copiando: */
  echo(RetornaFraseDaLista($lista_frases,119)." '".$dbnamebase.DUMP_AMBIENTE."' ...\n\n");

  flush();

  
  if (!($fp = popen($caminho_mysqldump." --default-character-set=latin1 -u ".$dbuser." -p ".$dbnamebase." > ".$caminho."/".$dbnamebase.DUMP_AMBIENTE, "w")))
    die('Erro interno');
  fputs($fp, $dbpassword);
  pclose($fp);

  /*304 - Ok*/
     echo(RetornaFraseDaLista($lista_frases,304)."<p>\n");

  /* 119 - Copiando: */
  echo(RetornaFraseDaLista($lista_frases,119)." '".$dbnamecurso.$codigo_extracao.DUMP_CURSO."' ...\n\n");

  flush();

  if (!($fp = popen($caminho_mysqldump." --default-character-set=latin1 -u ".$dbuser." -p ".$dbnamecurso.$cod_curso." > ".$caminho."/".$dbnamecurso.$codigo_extracao.DUMP_CURSO,"w")))
    die('Erro interno');
  fputs($fp,$dbpassword);
  pclose($fp);


  /* como o sed n�o aceita transformar o arquivo nele mesmo, cria-se um tempor�rio e depois move o tempor�rio */
  /* para o verdadeiro */
  shell_exec("mv ".$caminho."/".$dbnamecurso."temp".DUMP_CURSO." ".$caminho."/".$dbnamecurso.$codigo_extracao.DUMP_CURSO);

  /* Faz o backup dos usuarios */
  $query = "select * from Usuario_curso where cod_curso = ".$cod_curso;
  $res = Enviar($sock, $query);
  $lista = RetornaArrayLinhas($res);

  $sql = "INSERT INTO `Usuario_curso` (`cod_usuario_global`, `cod_usuario`, `cod_curso`, `tipo_usuario`, `portfolio`, `data_inscricao`) VALUES \n";


  /* Transforma os selects de usuarios atuais do curso em insert */
  $i = count($lista);
  foreach ($lista as $cod => $linha){
    if ($i == 1)
      $sql .= "(".$linha['cod_usuario_global'].", ".$linha['cod_usuario'].", ".$linha['cod_curso'].", '".$linha['tipo_usuario']."', '".$linha['portfolio']."', ".$linha['data_inscricao'].");";
    else
      $sql .= "(".$linha['cod_usuario_global'].", ".$linha['cod_usuario'].", -1, '".$linha['tipo_usuario']."', '".$linha['portfolio']."', ".$linha['data_inscricao']."),\n";
    $i--;
  }
  $arquivo = fopen($caminho."/Usuario_curso.sql","w");
  fwrite($arquivo, $sql);
  fclose($arquivo);
  /* Fim Backup dos Usuarios */

  /*304 - Ok*/
  echo(RetornaFraseDaLista($lista_frases,304)."<p>\n");
  
  // 305 - Fase 2 - Conclu�da
  echo("<b>".RetornaFraseDaLista($lista_frases,305)."</b><p>\n");

  /******** Aqui come�a o c�digo para criar o arquivo de resumo ********/

  /* 306 - Fase 3 - Criando arquivo de resumo do curso extra�do */
  echo("<b>".RetornaFraseDaLista($lista_frases,306)." ".$caminho."/".NOME_RESUMO."</b><p>\n");

  $arquivo = fopen($caminho."/".NOME_RESUMO,"w");

  $texto = MontaCursoResumo($sock, $codigo_extracao);

  fwrite($arquivo, $texto);
  fclose($arquivo);

  Desconectar($sock);
  
  
  // 307 - Fase 3 - Conclu�da
  echo("<b>".RetornaFraseDaLista($lista_frases,307)."</b><p>\n");

  /************************** Acabou !! **********************************/
  if (strcmp($check_remover,"on")==0) {
  
     /* 112 - Fase 4 - Removendo o curso do ambiente */
     echo("<b>".RetornaFraseDaLista($lista_frases,112)."</b><p>\n");

     echo("\n                                                                                                                                                                        \n");

     /* Apaga todas as refer�ncias ao curso extra�do da base de dados */
     ApagaReferencias($cod_curso);

     $sock=Conectar("");

     /* 121 - Removendo dados do Curso da Base */
     echo(RetornaFraseDaLista($lista_frases,121)." ... \n\n");

     flush();

     $query = "drop database ".$dbnamecurso.$cod_curso;
     Enviar($sock,$query);

     // Remove os usuarios 
     $query = "delete from Usuario_curso where cod_curso = ".$cod_curso;
     Enviar($sock, $query);
     
     $query = "delete from Usuario_config where cod_curso = ".$cod_curso;
     Enviar($sock, $query);
     
     /* 304 - Ok */
     echo(RetornaFraseDaLista($lista_frases,304)."<p>\n");

     /* 122 - Removendo arquivos do Curso */
     echo(RetornaFraseDaLista($lista_frases,122)." ... \n\n");

     flush();

     RemoveDiretorio($dir_arquivos."/".$cod_curso."/");

     /* 304 - Ok */
     echo(RetornaFraseDaLista($lista_frases,304)."<p>\n");
  
     // 308 - Fase 4 - Conclu�da
     echo("<b>".RetornaFraseDaLista($lista_frases,308)."</b><p>\n");
  }
  else {
  	  $sock=Conectar("");
  }


  /* 114 - O curso foi removido com sucesso e est� agora armazenado no diretorio*/
  /* 299 - e est� dividido em duas partes: */
  echo(RetornaFraseDaLista($lista_frases,114)."<br /><b>'".$caminho."/' </b>".RetornaFraseDaLista($lista_frases,299)."<br /><br>\n");
 
  /* 115 - base de dados: em formato semelhante ao gerado pelo comando 'mysqldump', est�o em: */
  echo("<ul><li> ".RetornaFraseDaLista($lista_frases,115)."<b> '".$caminho."/".$dbnamebase.DUMP_AMBIENTE."'</b> e <b>'".$caminho."/".$dbnamecurso.$codigo_extracao.DUMP_CURSO."'</b>.</li></ul><br />\n");

  /* 116 - os arquivos enviados via 'upload' para as ferramentas do curso est�o em:  */
  if (CompactarCurso($sock))
    echo(" <ul><li> ".RetornaFraseDaLista($lista_frases,116)."<b> '".$caminho."/".NOME_ARQUIVOS."/'</b></li></ul><br /><br />\n");
  else
    echo(" <ul><li> ".RetornaFraseDaLista($lista_frases,116)."<b> '".$caminho."/'</b></li></ul><br /><br />\n");

  /* 117 - Esses arquivos podem ser retirados desses locais sem causar quaisquer problemas ao ambiente TelEduc nem aos demais cursos nele armazenados. Aconselha-se armazen�-los em CD ou outras m�dias, liberando assim espa�o em disco neste computador. */
  /* 298 - Para reinserir este curso depois de grava-lo em m�dia voc� deve colocar os arquivos na mesma estrutura de pastas.*/
  echo(RetornaFraseDaLista($lista_frases,117)." ".RetornaFraseDaLista($lista_frases,298)."\n");
 
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  </table>\n");
  echo("  </td>\n");
  echo("  </tr> \n");
  echo("</table>\n");
  include("../cursos/aplic/tela2.php");
  echo("</body>\n");
  echo("</html>\n");
?>
