<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/importar_perguntas2.php

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

    Nied - Núcleo de Informática Aplicada à Educação
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
  ARQUIVO : cursos/aplic/material/importar_perguntas2.php
  ========================================================== */
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."importar.inc");
  include("perguntas.inc");

  // **************** VARIÁVEIS DE ENTRADA ****************
  // Recebe de 'importar_perguntas.php'
  //    código do curso
  $cod_curso = $_POST['cod_curso'];
  //    código da categoria que estava sendo listada.
  $cod_categoria = $_POST['cod_categoria'];
  //    código do tópico, que estava visualizando antes da importação,
  //  para o qual irá importar os itens
  $cod_topico_raiz = $_POST['cod_topico_raiz'];
  //    código do curso do qual itens serão importados
  $cod_curso_import = $_POST['cod_curso_import'];
  //    código da ferramenta cujos itens serão importados
  $cod_ferramenta = $_POST['cod_ferramenta'];
  //    tipo do curso: A(ndamento), I(nscrições abertas), L(atentes),
  //  E(ncerrados)
  $tipo_curso = $_POST['tipo_curso'];
  if ('E' == $tipo_curso)
  {
    //    período especificado para listar os cursos
    //  encerrados.
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
  }
  //    booleano, se o curso, cujos itens serão importados, foi
  //  escolhido na lista de cursos compartilhados.
  $curso_compartilhado = $_POST['curso_compartilhado'];
  //    booleando, se o curso, cujos itens serão importados, é um
  //  curso extraído.
  $curso_extraido = $_POST['curso_extraido'];
  //    código do tópico do curso do qual itens serão importados.
  $cod_topico_raiz_import = $_POST['cod_topico_raiz_import'];
  //    arrays de itens e tópicos que serão importados
  $cod_itens_import = $_POST['cod_itens_import'];
  $cod_topicos_import = $_POST['cod_topicos_import'];

  // ******************************************************


  /* Registrando código da ferramenta nas variáveis de sessão.
     É necessário para saber qual ferramenta está sendo
     utilizada, já que este arquivo faz parte de quatro
     ferramentas quase distintas.
   */
  session_register("cod_ferramenta_s");
  if (isset($cod_ferramenta))
    $cod_ferramenta_s = $cod_ferramenta;
  else
    $cod_ferramenta = $cod_ferramenta_s;

  /* Necessário para a lixeira. */
  session_register("cod_topico_s");
  unset($cod_topico_s);

  session_register("login_import_s");
  if (isset($login_import))
    $login_import_s = $login_import;
  else
    $login_import = $_SESSION['login_import_s'];

  // Se não foi definido um curso do qual serão importados
  // itens, emite uma mensagem de erro.
  if (!isset($cod_curso_import))
  {
    // ?? - Erro ! Nenhum código de curso para importação foi recebido !
    echo ("Erro ! Nenhum código de curso para importação foi recebido !");
    die();
  }

  // Se o curso DO QUAL serão importados itens foi montado
  // na base temporária, então define o parâmetro $opt para
  // conexão a ela.
  if ($curso_extraido)
    $opt = TMPDB;
  else
    $opt = "";

  // Autentica no curso PARA O QUAL serão importados os itens.
  $cod_usuario = VerificaAutenticacao($cod_curso);

  // Se o curso foi selecionado na lista de todos cursos e
  // a autenticação do usuário nesse curso não é válida então
  // encerra a execução do script.
  if ((!$curso_compartilhado) &&
      (false === ($cod_usuario_import = UsuarioEstaAutenticadoImportacao($cod_curso, $cod_usuario, $cod_curso_import, $opt))))
  {
    // Testar se é identicamente falso,
    // pois 0 pode ser um valor válido para cod_usuario
    echo("<html>\n");
    echo("  <script language=javascript type=text/javascript defer>\n\n");

    echo("    function ReLogar()\n");
    echo("    {\n");
   // ?? - Login ou senha inválidos
    echo("      alert('[Login ou senha inválidos]');\n");
    echo("      document.frmRedir.submit();\n");
    echo("    }\n\n");

    echo("  </script>\n\n");
    echo("  <body onLoad='ReLogar();'>\n");
    echo("    <form method=post name=frmRedir action=importar_curso.php>\n");
    echo(RetornaSessionIDInput());
    echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("      <input type=hidden name=cod_categoria value=".$cod_categoria.">\n");
    echo("      <input type=hidden name=cod_topico_raiz value=".$cod_topico_raiz.">\n");
    echo("      <input type=hidden name=cod_ferramenta value=".$cod_ferramenta.">\n");
    echo("    </form>\n");

    echo("  </body>\n");
    echo("</html>\n");
    exit();
  }

  $sock = Conectar("");

  // Marca data de último acesso ao curso temporário. Esse recurso é importante
  // para eliminação das bases temporárias, mediante comparação dessa data adicionado
  // um período de folga com a data em que o script para eliminação estiver rodando.
  MarcarAcessoCursoExtraidoTemporario($sock, $cod_curso_import);

  $lista_frases = RetornaListaDeFrases($sock, $cod_ferramenta);
  $lista_frases_geral = RetornaListaDeFrases($sock,-1);

  // Se o curso foi montado (extraído) lista os arquivos do caminho
  // temporário.
  if ($curso_extraido)
    $diretorio_arquivos_origem = RetornaDiretorio($sock, 'Montagem');
  else
    $diretorio_arquivos_origem = RetornaDiretorio($sock, 'Arquivos');

  // Raiz do diretório de arquivos do curso PARA O QUAL serão importados
  // os itens.
  $diretorio_arquivos_destino = RetornaDiretorio($sock, 'Arquivos');
  $diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');

  Desconectar($sock);

  // Conecta-se à base do curso.
  $sock = Conectar($cod_curso_import, $opt);

  // Obtém o nome do curso.
  $nome_curso_import = NomeCurso($sock, $cod_curso_import);

  // Se o curso não foi selecionado na lista de todos cursos,
  // verifica as permissões de acesso ao curso e às ferramentas.
  if (!$curso_compartilhado)
  {
    VerificaAcessoAoCurso($sock, $cod_curso_import, $cod_usuario_import);
    VerificaAcessoAFerramenta($sock, $cod_curso_import, $cod_usuario_import, $cod_ferramenta);
  }

  Desconectar($sock);

  echo("<html>\n");
  /* 1 - : Perguntas Freqüentes*/
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  
  echo("  <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  $tabela="Pergunta";
  $dir="perguntas";
  
  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onLoad='document.frmImportar.submit()'>\n");

  if (ImportarMateriais($cod_curso, $cod_topico_raiz, $cod_usuario,
                        $cod_curso_import, $curso_extraido, $curso_compartilhado,
                        $cod_topicos_import, $cod_itens_import, $tabela,
                        $dir, $diretorio_arquivos_destino, $diretorio_arquivos_origem))
    $sucesso = true;
  else
    $sucesso = false;


  echo("  <form method=post name=frmImportar action=importar_perguntas3.php>\n");
  echo(RetornaSessionIDInput()."\n");
  echo("    <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("    <input type=hidden name=cod_categoria value=".$cod_categoria.">\n");
  echo("    <input type=hidden name=cod_topico_raiz value=".$cod_topico_raiz.">\n");

  echo("    <input type=hidden name=cod_curso_import value=".$cod_curso_import.">\n");
  echo("    <input type=hidden name=cod_topico_raiz_import value=".$cod_topico_raiz_import.">\n");

  echo("    <input type=hidden name=curso_extraido value=".$curso_extraido.">\n");
  echo("    <input type=hidden name=curso_compartilhado value=".$curso_compartilhado.">\n");

  echo("    <input type=hidden name=tipo_curso value='".$tipo_curso."'>\n");

  if ('E' == $tipo_curso)
  {
    echo("    <input type=hidden name=data_inicio value='".$data_inicio."'>\n");
    echo("    <input type=hidden name=data_fim value='".$data_fim."'>\n");
  }

  echo("    <input type=hidden name=cod_ferramenta value=".$cod_ferramenta.">\n");
  echo("    <input type=hidden name=sucesso value=".$sucesso.">\n");
  echo("  </form>\n");

  echo("</body>\n");
  echo("</html>\n");

?>
