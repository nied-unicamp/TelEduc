<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/importar_curso2.php

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
  ARQUIVO : cursos/aplic/material/importar_curso2.php
  ========================================================== */


/* Código principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."sql_dump.inc");
  include($bibliotecas."conversor.inc");
  include($bibliotecas."extracao.inc");
  include($bibliotecas."importar.inc");
  include("perguntas.inc");

  // período de espera para nova tentativa para montagem de um
  // curso extraído
  define("PERIODO_DE_ESPERA", 7000);
  // número máximo de tentativas para login
  define("MAX_TENTATIVAS_LOGIN", 3);

  // **************** VARIÁVEIS DE ENTRADA ****************
  // Recebe de 'importar_curso2.php'
  //    código do curso
  $cod_curso = $_POST['cod_curso'];
  //    código da categoria que estava sendo listada.
  $cod_categoria = $_POST['cod_categoria'];
  //    código do tópico, que estava visualizando antes da importação,
  //  para o qual irá importar os itens
  $cod_topico_raiz = $_POST['cod_topico_raiz'];
  //    código do curso selecionado na lista de cursos compartilhados
  if (isset($_POST['cod_curso_compart']))
    $cod_curso_compart = $_POST['cod_curso_compart'];
  //    código do curso selecionado na lista todos cursos
  if (isset($_POST['cod_curso_todos']))
    $cod_curso_todos = $_POST['cod_curso_todos'];
  //    código da ferramenta cujos itens serão importados
  $cod_ferramenta = $_POST['cod_ferramenta'];
  //    login do usuário no curso a ser importado
  $login_import = $_POST['login_import'];
  //    senha criptografada do usuário no curso a ser importado.
  $senha_import_crypt = $_POST['senha_import'];
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
    $cod_ferramenta = $_SESSION['cod_ferramenta_s'];

  // Se selecionado um curso da lista com todos eles,
  // incializa o contador de tentativas de autentificação.
  if (isset($_POST['cod_curso_todos']))
  {
    session_register("login_import_count_s");
    if (!isset($login_import_count))
    {
      // Se o contador já havia sido inicializado, então
      // incrementa seu valor que não deverá ultrapassar o valor
      // de MAX_TENTATIVAS_LOGIN
      if (isset($_SESSION['login_import_count_s']))
      {
        $login_import_count = ((int) $_SESSION['login_import_count_s'] + 1);
        $login_import_count_s = $login_import_count;
      }
      else
      {
        $login_import_count = $login_import_count_s = 0;
      }
    }
  }

  $cod_usuario = VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases = RetornaListaDeFrases($sock,$cod_ferramenta);
  $lista_frases_geral = RetornaListaDeFrases($sock,-1);
  $lista_frases_bibliotecas = RetornaListaDeFrases($sock,-2);

  MudarDB($sock, $cod_curso);

  echo("<html>\n");

  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases, 1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");

  echo("  <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  $tabela="Pergunta";
  $dir="perguntas";


  // 84 - Ver Perguntas_freqüentes
  $cabecalho = "<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n";
  // 58 - Importando Perguntas Freqüentes
  $cabecalho .= "<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,58)."</b>\n";
   // 64 - Ver Perguntas Freqüente
  $cabecalho .= "<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,64)."</b>\n";

  # REVER CÓDIGO DA PÁGINA DA AJUDA.
  $cabecalho = PreparaCabecalho($cod_curso, $cabecalho, $cod_ferramenta, 1);

  // Exibe o caminho para o tópico em que se encontrava (tópico
  // para o qual serão importados os itens).
  $lista_topicos_ancestrais = RetornaTopicosAncestrais($sock, $tabela, $cod_topico_raiz);
  if (count($lista_topicos_ancestrais) > 0)
  {
    unset($caminho_original, $parte);

    $caminho_original = "<font class=text>";
    foreach ($lista_topicos_ancestrais as $cod => $linha)
    {
      if ($cod_topico_raiz != $linha['cod_assunto'])
      {
        $parte = $linha['nome']." &gt;&gt; ".$parte;
      }
      else
      {
        $parte = "<b>".$linha['nome']."</b><br>";
      }
    }
    $caminho_original .= $parte."</font>\n";
  }

  Desconectar($sock);

  // Extraí o código do curso selecionado.
  if (isset($cod_curso_compart))
  {
    list ($status, $cod) =  explode(";", $cod_curso_compart, 2);
    $curso_compartilhado = 1;
  }
  else if (isset($cod_curso_todos))
  {
    list ($status, $cod) =  explode(";", $cod_curso_todos, 2);
    $curso_compartilhado = 0;
  }

  $codigo_import = (int) $cod;

  // Se o curso estiver extraído (em arquivo), definimos
  // o parâmetro, $opt, para conexão à base de dados de montagem
  // do curso.
  if ($status == 'E')
  {
    $opt = TMPDB;
    $curso_extraido = 1;
  }
  else
  {
    $opt = "";
    $curso_extraido = 0;
  }

  echo("  <body link=#ffffff vlink=#ffffff bgcolor=white>\n");

  if ($status == 'E')
  {
    $sock = Conectar("");
    $curso_montado = CursoFaseDeMontagem($sock, $codigo_import);

    // Se o curso estiver sendo montado ou desmontado aguarda
    // e tenta novamente.
    if (($curso_montado == 'montando') || ($curso_montando == 'desmontando'))
    {
      sleep(PERIODO_DE_ESPERA);
      $curso_montado = CursoFaseDeMontagem($sock, $codigo_import);
    }

    Desconectar($sock);

    // Se o curso não estiver montado, então lista as tabelas e pastas
    // das ferramentas compartilhadas.
    if ($curso_montado == 'nao')
    {
      list($tabelas, $pastas) = RetornaTabelasEPastasFerrCompartExtraido($codigo_import, $curso_compartilhado);

      MontaCursoExtraidoTemporario($codigo_import, $pastas, $tabelas);
    }
    // Se o curso continuar na fase de montagem ou desmontagem,
    // então informa a impossibilidade de importação dos materiais
    // desse curso ao usuário.
    else if (($curso_montado == 'montando') || ($curso_montando == 'desmontando'))
    {
      echo($cabecalho);
      if (isset($caminho_original))
      {
        // 70 - Importando para:
        echo("  <font class=text>".RetornaFraseDaLista($lista_frases,70)." </font>");
        echo($caminho_original);
        echo("  <br>\n");
      }


      $sock = Conectar("");
      $nome_curso_import = NomeCursoExtraido($sock, $codigo_import);
      Desconectar($sock);

      // 65 -  Perguntas Freqüentes do curso:
      echo("  <font class=textsmall>(".RetornaFraseDaLista($lista_frases,65)." \"<b>".$nome_curso_import."</b>\")</font><br>\n");

      echo("      <script language=javascript type=text/javascript defer>\n\n");

      echo("      function Cancelar()\n");
      echo("      {\n");
      echo("        document.frmRedir.action = 'importar_curso.php?'".RetornaSessionID().";\n");
      echo("        document.frmRedir.submit();\n");
      echo("      }\n\n");

      echo("    </script>\n\n");

      echo("    <form method=post name=frmRedir>\n");
      echo(RetornaSessionIDInput());
      echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
      echo("      <input type=hidden name=cod_categoria value=".$cod_categoria.">\n");
      echo("      <input type=hidden name=cod_topico_raiz value=".$cod_topico_raiz.">\n");
      echo("      <input type=hidden name=tipo_curso value='".$tipo_curso."'>\n");
      if ('E' == $tipo_curso)
      {
        echo("      <input type=hidden name=data_inicio value='".$data_inicio."'>\n");
        echo("      <input type=hidden name=data_fim value='".$data_fim."'>\n");
      }

      echo("      <input type=hidden name=cod_ferramenta value=".$cod_ferramenta.">\n");

      // 49 (bibli) - O curso solicitado está ocupado. Por favor tente novamente.
      echo("      <font class=text>".RetornaFraseDaLista($lista_frases_bibliotecas,49)."</font><br>\n");
      // 23 - Voltar (geral)
      echo("      <input type=button onClick='Cancelar();' value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
      //
      echo("    </form>\n");
      echo("  </body>\n");
      echo("</html>\n");
      exit();
    }
  }

  echo($cabecalho);
  if (isset($caminho_original))
  {
    // 70 - Importando para:
    echo("  <font class=text>".RetornaFraseDaLista($lista_frases,70)." </font>");
    echo($caminho_original);
    echo("  <br>\n");
  }

  // Conecta-se à base de dados do curso. Temporária,
  // se o curso estava extraído e foi montado (parâmetro $opt).
  $sock = Conectar($codigo_import, $opt);

  $nome_curso_import = NomeCurso($sock, $codigo_import);

  Desconectar($sock);

  // 65 - Perguntas Freqüentes do curso:
  echo("  <font class=textsmall>(".RetornaFraseDaLista($lista_frases,65)." \"<b>".$nome_curso_import."</b>\")</font><br>\n");


  echo("    <form method=post name=frmRedir>\n");
  echo(RetornaSessionIDInput());
  echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("      <input type=hidden name=cod_categoria value=".$cod_categoria.">\n");
  echo("      <input type=hidden name=cod_topico_raiz value=".$cod_topico_raiz.">\n");
  echo("      <input type=hidden name=tipo_curso value='".$tipo_curso."'>\n");
  if ('E' == $tipo_curso)
  {
    echo("      <input type=hidden name=data_inicio value='".$data_inicio."'>\n");
    echo("      <input type=hidden name=data_fim value='".$data_fim."'>\n");
  }

  echo("      <input type=hidden name=cod_ferramenta value=".$cod_ferramenta.">\n");

  
  // Se selecionado um curso com itens compartilhados, redireciona a página.
  if (isset($cod_curso_compart))
  {
    echo("      <input type=hidden name=cod_curso_import value=".$codigo_import.">\n");
    echo("      <input type=hidden name=curso_extraido value=".$curso_extraido.">\n");
    echo("      <input type=hidden name=curso_compartilhado value=".$curso_compartilhado.">\n");

    echo("      <script language=javascript type=text/javascript defer>\n\n");

    echo("        document.frmRedir.action = 'importar_perguntas.php?'".RetornaSessionID().";\n");
    echo("        document.frmRedir.submit();\n");

    echo("      </script>\n\n");
  }
  // Se selecionado um curso na listagem de todos eles, verifica a autenticação.
  else if (isset($cod_curso_todos))
  {
  
    $cod_usuario_import = VerificaAutentImportacao($codigo_import, $login_import, $senha_import_crypt, $opt);
    if ($login_import_count_s > MAX_TENTATIVAS_LOGIN)
    {
      echo("      <script language=javascript type=text/javascript defer>\n\n");

      echo("        function Cancelar()\n");
      echo("        {\n");
      echo("          document.frmRedir.action = 'importar_curso.php?'".RetornaSessionID().";\n");
      echo("          document.frmRedir.submit();\n");
      echo("        }\n\n");

      echo("      </script>\n\n");


      // 50 (bibli) - Excedido o limite de tentativas para acesso. Tente novamente.
      echo("      <font class=text>".RetornaFraseDaLista($lista_frases_bibliotecas,50)."</font><br>\n");
      // 23 - Voltar (geral)
      echo("      <input type=button onClick='Cancelar();' value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
      //
      echo("    </form>\n");
      echo("  </body>\n");
      echo("</html>\n");
      exit();
    }
    // Se o usuário não existe (autenticação falhou) no curso do
    // qual se deseja importar os itens, oferece nova tentativa
    // de autenticação.
    if (false === $cod_usuario_import)
    {
      echo("      <script language=javascript type=text/javascript src=../bibliotecas/javacrypt.js defer></script>\n\n");
      echo("      <script language=javascript type=text/javascript defer>\n\n");

      echo("        function Valida()\n");
      echo("        {\n");
      echo("          login_imp = document.frmRedir.login_import.value;\n");
      echo("          while (login_imp.search(\" \") != -1)\n");
      echo("            login_imp = login_imp.replace(/ /, \"\");\n\n");

      echo("          senha_imp = document.frmRedir.senha_import.value;\n");
      echo("          while (senha_imp.search(\" \") != -1)\n");
      echo("            senha_imp = senha_imp.replace(/ /, \"\");\n\n");

      echo("          if ((login_imp == \"\") || (senha_imp == \"\"))\n");
      echo("          {\n");
      // 32 (bibli) - Por favor informe o login e a senha!
      echo("            alert(\"".RetornaFraseDaLista($lista_frases_bibliotecas,32)."\");\n");
      echo("            if (login_imp == \"\")\n");
      echo("              document.frmRedir.login_import.focus();\n");
      echo("            else if (senha_imp == \"\")\n");
      echo("              document.frmRedir.senha_import.focus();\n");
      echo("            return(false);\n");
      echo("          }\n");
      echo("          else\n");
      echo("          {\n");
      echo("            document.frmRedir.senha_import.value =");
      echo(" Javacrypt.displayPassword(document.frmRedir.senha_import.value, 'AA');\n");
      echo("            return(true);\n");
      echo("          }\n");
      echo("        }\n\n");

      echo("        function ReAutent()\n");
      echo("        {\n");
      echo("          document.frmRedir.action = 'importar_curso2.php?'".RetornaSessionID().";\n");
      echo("          if (Valida())\n");
      echo("            document.frmRedir.submit();\n");
      echo("        }\n\n");

      echo("        function Cancelar()\n");
      echo("        {\n");
      echo("          document.frmRedir.action = 'importar_curso.php?'".RetornaSessionID().";\n");
      echo("          document.frmRedir.submit();\n");
      echo("        }\n\n");

      echo("      </script>\n\n");

      echo("      <input type=hidden name=cod_curso_todos value='".$cod_curso_todos."'>\n");

      echo("      <table width=100% border=0>\n");
      echo("        <tr class=text>\n");
      // 27 (bibli) - Login:
      echo("         <td class=text>".RetornaFraseDaLista($lista_frases_bibliotecas,27)."</td>\n");
      echo("         <td class=text>"."<input type=text class=text name=login_import value=''>"."</td>\n");
      echo("        </tr>\n");
      echo("        <tr class=text>\n");
      // 48 (bibli) - Senha:
      echo("         <td class=text>".RetornaFraseDaLista($lista_frases_bibliotecas,48)."</td>\n");
      echo("         <td class=text>"."<input type=password class=text name=senha_import value=''>"."</td>\n");
      echo("        </tr>\n");
      echo("      </table><br>\n");
      // 63 -  Importar Perguntas Freqüentes
      echo("      <input type=button value='".RetornaFraseDaLista($lista_frases,63)."' onClick='ReAutent()'>");
      // 2 - Cancelar (geral)
      echo("&nbsp;&nbsp;<input type=button value='");
      echo(RetornaFraseDaLista($lista_frases_geral, 2)."' onClick='Cancelar()'><br>\n");
      
      echo("      <script language=javascript type=text/javascript defer>\n\n");

      echo("        document.frmRedir.login_import.focus();\n");
      
      echo("      </script>\n\n");
    }
    else // Usuário autentifcado.
    {
      echo("      <input type=hidden name=cod_curso_import value=".$codigo_import.">\n");
      echo("      <input type=hidden name=curso_extraido value=".$curso_extraido.">\n");
      echo("      <input type=hidden name=curso_compartilhado value=".$curso_compartilhado.">\n");

      // Armazena chave de autenticação do usuário.
      session_register("login_import_s");
      $login_import_s = CriaChaveDeAutenticacao($cod_curso, $cod_usuario, $codigo_import,
                                                $login_import, $senha_import_crypt);

      echo("      <script language=javascript type=text/javascript defer>\n\n");

      echo("        document.frmRedir.action = 'importar_perguntas.php'".RetornaSessionID().";\n");
      echo("        document.frmRedir.submit();\n");
    
      echo("      </script>\n\n");
    }
  }
  
  echo("    </form>\n");
  echo("  </body>\n");
  echo("</html>\n");

?>
