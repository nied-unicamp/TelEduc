<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/importar_perguntas3.php

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
  ARQUIVO : cursos/aplic/material/importar_perguntas3.php
  ========================================================== */
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."importar.inc");
  include("perguntas.inc");

  // **************** VARI�VEIS DE ENTRADA ****************
  // Recebe de 'importar_perguntas2.php'
  //    c�digo do curso
  $cod_curso = $_POST['cod_curso'];
  //    c�digo da categoria que estava sendo listada.
  $cod_categoria = $_POST['cod_categoria'];
  //    c�digo do t�pico, que estava visualizando antes da importa��o,
  //  para o qual ir� importar os itens
  $cod_topico_raiz = $_POST['cod_topico_raiz'];
  //    c�digo do curso do qual itens ser�o importados
  $cod_curso_import = $_POST['cod_curso_import'];
  //    c�digo da ferramenta cujos itens ser�o importados
  $cod_ferramenta = $_POST['cod_ferramenta'];
  //    tipo do curso: A(ndamento), I(nscri��es abertas), L(atentes),
  //  E(ncerrados)
  $tipo_curso = $_POST['tipo_curso'];
  if ('E' == $tipo_curso)
  {
    //    per�odo especificado para listar os cursos
    //  encerrados.
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
  }
  //    booleano, se o curso, cujos itens ser�o importados, foi
  //  escolhido na lista de cursos compartilhados.
  $curso_compartilhado = $_POST['curso_compartilhado'];
  //    booleando, se o curso, cujos itens ser�o importados, � um
  //  curso extra�do.
  $curso_extraido = $_POST['curso_extraido'];
  //    c�digo do t�pico do curso do qual itens ser�o importados.
  $cod_topico_raiz_import = $_POST['cod_topico_raiz_import'];
  //    booleano, true se a opera��o de inser��o obteve sucesso
  $sucesso = $_POST['sucesso'];

  // ******************************************************


  /* Registrando c�digo da ferramenta nas vari�veis de sess�o.
     � necess�rio para saber qual ferramenta est� sendo
     utilizada, j� que este arquivo faz parte de quatro
     ferramentas quase distintas.
   */
  session_register("cod_ferramenta_s");
  if (isset($cod_ferramenta))
    $cod_ferramenta_s = $cod_ferramenta;
  else
    $cod_ferramenta = $cod_ferramenta_s;

  /* Necess�rio para a lixeira. */
  session_register("cod_topico_s");
  unset($cod_topico_s);

  session_register("login_import_s");
  if (isset($login_import))
    $login_import_s = $login_import;
  else
    $login_import = $_SESSION['login_import_s'];


  if (!isset($cod_curso_import))
  {
    // ?? - Erro ! Nenhum c�digo de curso para importa��o foi recebido !
    echo ("[Erro ! Nenhum c�digo de curso para importa��o foi recebido !]");
  }
  
  if ($curso_extraido)
    $opt = TMPDB;
  else
    $opt = "";

  $cod_usuario = VerificaAutenticacao($cod_curso);

  if ((!$curso_compartilhado) &&
      (false === ($cod_usuario_import = UsuarioEstaAutenticadoImportacao($cod_curso, $cod_usuario, $cod_curso_import, $opt))))
  {
    // Testar se � identicamente falso,
    // pois 0 pode ser um valor v�lido para cod_usuario
    echo("<html>\n");
    echo("  <script language=javascript type=text/javascript defer>\n\n");

    echo("    function ReLogar()\n");
    echo("    {\n");
    // ?? - Login ou senha inv�lidos
    echo("      alert('"."[Login ou senha inv�lidos]"."');\n");
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

  MarcarAcessoCursoExtraidoTemporario($sock, $cod_curso_import);

  $lista_frases = RetornaListaDeFrases($sock, $cod_ferramenta);
  $lista_frases_geral = RetornaListaDeFrases($sock,-1);
  $lista_frases_bibliotecas = RetornaListaDeFrases($sock,-2);

  echo("<html>\n");
  /* 1 - Perguntas Freq�entes*/
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  
  echo("  <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  $tabela="Pergunta";
  $dir="perguntas";

  MudarDB($sock, $cod_curso);
  
  /* P�gina Principal */
  /* 1 - : Perguntas Freq�entes*/
  $cabecalho = "<b class=titulo>".RetornaFraseDaLista($lista_frases, 1)."</b>\n";
  // 63 - Importando Perguntas Freq�entes
  $cabecalho .= "<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 63)."</b>\n";
  // Cabecalho
  $cabecalho = PreparaCabecalho($cod_curso, $cabecalho, $cod_ferramenta, 1);

  $lista_topicos_ancestrais = RetornaTopicosAncestrais($sock, $tabela, $cod_topico_raiz);
  if (count($lista_topicos_ancestrais) > 0)
  {
    unset($caminho_original, $parte);

    $caminho_original = "<font class=text>";
    foreach ($lista_topicos_ancestrais as $cod => $linha)
    {
      if ($cod_topico_raiz != $linha['cod_assunto'])
      {
// "&cod_topico_raiz_import=".$linha['cod_topico'].
        $parte = $linha['topico']." &gt;&gt; ".$parte;
      }
      else
      {
        $parte = "<b>".$linha['nome']."</b><br>";
      }
    }
    $caminho_original .= $parte."</font>\n";
  }
  Desconectar($sock);


  $sock = Conectar($cod_curso_import, $opt);

  $nome_curso_import = NomeCurso($sock, $cod_curso_import);

  if (!$curso_compartilhado)
  {
    VerificaAcessoAoCurso($sock, $cod_curso_import, $cod_usuario_import);
    VerificaAcessoAFerramenta($sock, $cod_curso_import, $cod_usuario_import, $cod_ferramenta);
  }


  echo("<body onLoad='document.frmImportar.cmdVoltar.focus();'>\n");

  echo($cabecalho);
  if (isset($caminho_original))
  {
    // 70 - Importando para:
    echo("<font class=text>".RetornaFraseDaLista($lista_frases,70)." </font>");
    echo($caminho_original);
    echo("<br>\n");
  }

  // 65 - Perguntas Freq�entes do curso:
  echo("<font class=textsmall>(".RetornaFraseDaLista($lista_frases, 65)." \"<b>".$nome_curso_import."</b>\")</font><br>\n");

  $lista_topicos_ancestrais = RetornaTopicosAncestrais($sock, $tabela, $cod_topico_raiz_import);
  unset($path);
  foreach ($lista_topicos_ancestrais as $cod => $linha)
  {
    if ($cod_topico_raiz_import != $linha['cod_assunto'])
    {
// "&cod_topico_raiz_import=".$linha['cod_topico'].
      $path="<font class=text >".$linha['nome']."</a> &gt;&gt; ".$path;
    }
    else
    {
      $path="<font class=text>".$linha['nome']."</a><br>\n";
    }
  }
  echo($path);

  echo("<br>\n");

  echo("  <form method=post name=frmImportar action=importar_perguntas.php>\n");
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

  echo("  <font class=text>");
  if ($sucesso)
  {
    // 55 (bibli) - Itens importados com sucesso.
    echo(RetornaFraseDaLista($lista_frases_bibliotecas, 55));
  }
  else
  {
    // 56 (ger) - Erro na importa��o dos itens selecionados.
    echo(RetornaFraseDaLista($lista_frases_bibliotecas, 56));
  }

  echo("<br>\n");
  echo(RetornaFraseDaLista($lista_frases_bibliotecas, 66));
  echo("</font><br><p>\n");
                                                                                                                             
  echo("  <table>\n");
  echo("    <tr>\n");
  echo("    <td>\n");
  //Sim
  echo("  <input type=submit name=cmdVoltar value='".RetornaFraseDaLista($lista_frases_geral, 35)."'>\n");
  echo("  </form>\n");
  echo("    </td>\n");
  echo("    <td>\n");
  echo("  <form method=post action=perguntas.php>\n");
  echo("    <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("    <input type=hidden name=cod_ferramenta value=".$cod_ferramenta.">\n");
  //Nao
  echo("  <input type=submit name=cmdVoltar value='".RetornaFraseDaLista($lista_frases_geral, 36)."'>\n");
  echo("  </form>\n");
  echo("    </td>\n");
  echo("    </tr>\n");
  echo("    </table>\n");


  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
