<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/vercompart.php

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
  ARQUIVO : cursos/aplic/material/vercompart.php
  ========================================================== */
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."importar.inc");
  include("perguntas.inc");

  // **************** VARIÁVEIS DE ENTRADA ****************
  // Recebe de 'importar_curso2.php'
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

  // ******************************************************

  session_register("cod_ferramenta_s");
  if (isset($cod_ferramenta))
    $cod_ferramenta_s = $cod_ferramenta;
  else
    $cod_ferramenta = $_SESSION['cod_ferramenta_s'];

  session_register("login_import_s");
  if (isset($login_import))
    $login_import_s = $login_import;
  else
    $login_import = $_SESSION['login_import_s'];

  if ($curso_extraido)
    $opt = TMPDB;
  else
    $opt = "";


  $cod_usuario = VerificaAutenticacao($cod_curso);

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
    echo("      alert('"."[Login ou senha inválidos]"."');\n");
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

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  if ($curso_extraido)
    $diretorio_arquivos=RetornaDiretorio($sock, 'Montagem');
  else
    $diretorio_arquivos=RetornaDiretorio($sock, 'Arquivos');

  $diretorio_temp=RetornaDiretorio($sock, 'ArquivosWeb');


  Desconectar($sock);
  $sock = Conectar($cod_curso);

  //$eformador = EFormador($sock,$cod_curso,$cod_usuario);

  echo("<html>\n");
  /* 1 - Perguntas Freqüentes*/
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");

  echo("  <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  $tabela="Pergunta";
  $dir="perguntas";


  // Página Principal
  // 1 - : Perguntas Freqüentes
  // 63 - : Importando Perguntas Freqüentes
  // 64 - Ver Perguntas Freqüentes
  $cabecalho = "<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n";
  $cabecalho .= "<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 63)."</b>\n";
  $cabecalho .= "<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 64)."</b>\n";

  # REVER O CÓDIGO DA PÁGINA DA AJUDA
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


  $sock = Conectar($cod_curso_import, $opt);

  $nome_curso_import = NomeCurso($sock, $cod_curso_import);

  if (!$curso_compartilhado)
  {
    VerificaAcessoAoCurso($sock, $cod_curso_import, $cod_usuario_import);
    VerificaAcessoAFerramenta($sock,$cod_curso_import, $cod_usuario_import, $cod_ferramenta);
  }

  $dir_item_temp = CriaLinkVisualizar($sock, $dir, $cod_curso_import, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

  echo("<script language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");

  echo("<script language=javascript type=text/javascript defer>\n\n");
  
  echo("  function WindowOpenVer(id)\n");
  echo("  {\n");
  echo("    popup = window.open('".$dir_item_temp['link']."'+id,'Material".$cod_ferramenta."','top=50,left=100,width=600,height=400,resizable=yes,menubar=yes,status=yes,toolbar=yes,scrollbars=yes');\n");
  echo("    popup.focus();\n");
  echo("  }\n\n");

  echo("  function WindowOpenVerURL(end)\n");
  echo("  {\n");
  echo("    popup2 = window.open(end,'MaterialURL".$cod_ferramenta."','top=50,left=100,width=600,height=400,resizable=yes,menubar=yes,status=yes,toolbar=yes,scrollbars=yes');\n");
  echo("    popup2.focus();\n");
  echo("  }\n\n");
  
  echo("  function MudarTopico(cod_topico)\n");
  echo("  {\n");
  echo("    document.frmImportar.cod_topico_raiz_import.value = cod_topico;\n");
  echo("    document.frmImportar.action = \"importar_perguntas.php?");
  echo(RetornaSessionID()."\";\n");
  echo("    document.frmImportar.submit();\n");
  echo("  }\n\n");

  
  echo("</script>\n\n");

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onUnLoad=\"top.invisivel.location='../material/saida.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_item=".$cod_item."';\">\n");
  echo("\n");

  echo($cabecalho);
  if (isset($caminho_original))
  {
    // ?? - Importando para:
    echo("<font class=text>"."[Importando para:]"." </font>");
    echo($caminho_original);
    echo("<br>\n");
  }

  // 1 - Perguntas Freqüentes
  echo("<font class=textsmall>(".RetornaFraseDaLista($lista_frases, 1)." \"<b>".$nome_curso_import."</b>\")</font><br>\n");

  $lista_topicos_ancestrais = RetornaTopicosAncestrais($sock, $tabela, $cod_topico_raiz_import);
  unset($path);
  foreach ($lista_topicos_ancestrais as $cod => $linha)
  {
    if ($cod_topico_raiz_import != $linha['cod_topico'])
    {
// "&cod_topico_raiz_import=".$linha['cod_topico'].
      $path="<a class=text href=# onClick='MudarTopico(".$linha['cod_assunto'].")'>".$linha['nome']."</a> &gt;&gt; ".$path;
    }
    else
    {
      $path="<a class=text href=# onClick='MudarTopico(".$linha['cod_assunto'].")'>".$linha['nome']."</a><br>\n";
    }
  }
  echo($path);

  echo("<br>\n");

  echo("<form method=post name=frmImportar>\n");
  echo(RetornaSessionIDInput()."\n");

  echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("  <input type=hidden name=cod_categoria value=".$cod_categoria.">\n");
  echo("  <input type=hidden name=cod_topico_raiz value=".$cod_topico_raiz.">\n");

  echo("  <input type=hidden name=cod_curso_import value=".$cod_curso_import.">\n");
  echo("  <input type=hidden name=cod_ferramenta value=".$cod_ferramenta.">\n");
//  echo("  <input type=hidden name=cod_usuario value=".$cod_usuario.">\n");

  echo("  <input type=hidden name=cod_item value=''>\n");

  echo("  <input type=hidden name=cod_topico_raiz_import value=".$cod_topico_raiz_import.">\n");

  echo("  <input type=hidden name=curso_compartilhado value=".$curso_compartilhado.">\n");
  echo("  <input type=hidden name=curso_extraido value=".$curso_extraido.">\n");


  echo("  <input type=hidden name=tipo_curso value=".$tipo_curso.">\n");
  if ('E' == $tipo_curso)
  {
    echo("  <input type=hidden name=data_inicio value='".$data_inicio."'>\n");
    echo("  <input type=hidden name=data_fim value='".$data_fim."'>\n");
  }

  echo("<table border=0 width=100% cellspacing=0>\n");
  echo("  <tr>\n");
  echo("    <td width=1% class=colorfield>&nbsp;</td>\n");
  /* ?? - Pergunta */
  echo("    <td class=colorfield>Pergunta</td>\n");
  /* ?? - Data */
  echo("    <td class=colorfield align=center>Data</td>\n");

  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td colspan=3 height=5></td>\n");
  echo("  </tr>\n");

  $linha_item = RetornaDadosDoItem($sock, $tabela, $cod_item);

  echo("  <tr>\n");
  echo("    <td><img src=\"../figuras/arqp.gif\" border=0></td>\n");
  echo("    <td>\n");

  echo("      <font class=text>".$linha_item['pergunta']."</font>\n");

  echo("    </td>\n");
  echo("    <td align=center><font class=text>".UnixTime2DataHora($linha_item['data'])."</font></td>\n");
  echo("  </tr>\n");
  echo("</table>\n");

  if ($linha_item['resposta']!="")
  {

    echo("<table border=0 width=100% cellspacing=0>\n");
    echo("  <tr>\n");
    /* 11 - Resposta  */
    echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases, 11)."</td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");

    echo("<font class=text>".AjustaParagrafo(Enter2Br(LimpaTags($linha_item['resposta'])))."</font>\n");

    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");

  }
  echo("</form>\n");
  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
