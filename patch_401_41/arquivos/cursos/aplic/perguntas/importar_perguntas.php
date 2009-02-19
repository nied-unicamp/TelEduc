<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/importar_perguntas.php

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
  ARQUIVO : cursos/aplic/perguntas/importar_perguntas.php
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
  if (!isset($_POST['cod_topico_raiz_import']))
    $cod_topico_raiz_import = 1;
  else
    $cod_topico_raiz_import = $_POST['cod_topico_raiz_import'];

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

  if (!isset($cod_curso_import))
  {
    // ?? - Erro ! Nenhum código de curso para importação foi recebido !
    echo ("Erro ! Nenhum código de curso para importação foi recebido !");
  }
  
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
    echo("      alert('Login ou senha inválidos');\n");
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

  if ($curso_extraido)
  {
    $opt = TMPDB;
    $diretorio_arquivos=RetornaDiretorio($sock, 'Montagem');
  }
  else
  {
    $opt = "";
    $diretorio_arquivos=RetornaDiretorio($sock, 'Arquivos');
  }
  
  $diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');

  Desconectar($sock);

  echo("<html>\n");
  /* 1 - Perguntas Freqüentes*/
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");

  echo("  <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  $tabela="Pergunta";
  $dir="perguntas";

  // Alterna para a base de dados do curso
  $sock = Conectar($cod_curso);

  // Página Principal
  // 1 - Perguntas Freqüentes
  $cabecalho = "<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n";
  // 1 - Perguntas Freqüentes
  $cabecalho .= "<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,1)."</b>\n";
  $cabecalho = PreparaCabecalho($cod_curso, $cabecalho, $cod_ferramenta, 9);

  $data_acesso = PenultimoAcesso($sock, $cod_usuario, "");

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


  // Apaga link simbolico que por acaso tenha sobrado daquele usuario
  $link_arquivo = $diretorio_temp."/".$dir."_".$cod_curso_import."_".$cod_usuario_import;
  if (ExisteArquivo($link_arquivo))
  {
    RemoveArquivo($link_arquivo);
  }

  /* Funções JavaScript */
  echo("<script language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");
  echo("<script language=JavaScript type=text/javascript defer>\n\n");

  echo("  var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("  var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
  echo("  var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
  echo("  var Xpos, Ypos;\n");
  echo("  var js_cod_item, js_cod_topico;\n");
  echo("  var js_nome_topico;\n");
  echo("  var js_tipo_item;\n");
  echo("  var js_comp = new Array();\n\n");

  echo("  if (isNav)\n");
  echo("  {\n");
  echo("    document.captureEvents(Event.MOUSEMOVE);\n");
  echo("  }\n");
  echo("  document.onmousemove = TrataMouse;\n\n");

  echo("  function TrataMouse(e)\n");
  echo("  {\n");
  echo("    Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("    Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("  }\n\n");

  echo("  function getPageScrollY()\n");
  echo("  {\n");
  echo("    if (isNav)\n");
  echo("      return(window.pageYOffset);\n");
  echo("    if (isIE)\n");
  echo("      return(document.body.scrollTop);\n");
  echo("  }\n\n");

  echo("  function AjustePosMenuIE()\n");
  echo("  {\n");
  echo("    if (isIE)\n");
  echo("      return(getPageScrollY());\n");
  echo("    else\n");
  echo("      return(0);\n");
  echo("  }\n\n");

  echo("  function Iniciar()\n");
  echo("  {\n");
  echo("    cod_topicos = getLayer(\"topicos\");\n");
  echo("  }\n\n");

  echo("  function EscondeLayers()\n");
  echo("  {\n");
  echo("    hideLayer(cod_topicos);\n");
  echo("  }\n\n");

  echo("  function MostraLayer(cod_layer, ajuste)\n");
  echo("  {\n");
  echo("    EscondeLayers();\n");
  echo("    moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
  echo("    showLayer(cod_layer);\n");
  echo("  }\n\n");

  echo("  function EscondeLayer(cod_layer)\n");
  echo("  {\n");
  echo("    hideLayer(cod_layer);\n");
  echo("  }\n\n");

  echo("  function MudarTopico(cod_topico)\n");
  echo("  {\n");
  echo("    document.frmImportar.action = \"importar_perguntas.php?");
  echo(RetornaSessionID()."\";\n");
  echo("    document.frmImportar.cod_topico_raiz_import.value = cod_topico;\n");
  echo("    document.frmImportar.submit();\n");
  echo("  }\n\n");

  echo("  function ExibirItem(cod_item)\n");
  echo("  {\n");
  echo("    document.frmImportar.cod_item.value = cod_item;\n");
  echo("    document.frmImportar.action = \"importar_ver.php?");
  echo(RetornaSessionID()."\";\n");
  echo("    document.frmImportar.submit();\n");
  echo("  }\n\n");

  echo("  function Importar()\n");
  echo("  {\n");
  echo("    var cont=true;\n");
  if (count($lista_topicos_ancestrais) == 1)
  echo("    cont=Verifica();");
  echo("if(cont==true){");
  echo("    document.frmImportar.action = \"importar_perguntas2.php?");
  echo(RetornaSessionID()."\";\n");
  echo("    document.frmImportar.submit();\n");
  echo("  }}\n\n");
  
  echo("  function Verifica()\n");
  echo("  {\n");
  echo("    var cont=false;\n");
  echo("     var e;\n");
  echo("     for (var i=0;i<document.frmImportar.elements.length;i++)\n");
  echo("     { \n");
  echo("       e = document.frmImportar.elements[i];\n");
  echo("       if ((e.checked==true)&&(document.frmImportar.elements[i].name=='cod_itens_import[]'))\n");
  echo("       {\n");
  echo("        cont=true;\n");
  echo("       }\n");
  echo("     } \n");
  echo("    if (cont==true)   \n");
  echo("    {\n");
  /* 66 - Uma pergunta não pode ser importada diretamente para a raiz!*/
  echo("    alert('".RetornaFraseDaLista($lista_frases,66)."'); \n");
  echo("    return false; \n");
  echo("    }  \n");
  echo("    else  \n");
  echo("    {   \n");
  echo("    return true; \n");
  echo("    }    \n");
  echo("  }\n\n");
  
    echo("  function Validacheck()\n");
  echo("  {\n");
  echo("    var cont = false;\n");
  echo("    var nome_var1 = 'cod_topicos_import[]';\n");
  echo("    var nome_var2 = 'cod_itens_import[]';\n");
  echo("    var e;\n");

  echo("    for (i = 0, total = document.frmImportar.elements.length; ((i < total) && (cont == false)); i++)\n");
  echo("    {\n");
  echo("      e = document.frmImportar.elements[i];\n");
  echo("      if ((e.type == 'checkbox') && ((e.name == nome_var1) || (e.name == nome_var2)) && (e.checked == true))\n");
  echo("      {\n");
  echo("        cont = true;\n");
  echo("      }\n");
  echo("    }\n");

  echo("    if (cont == true)\n");
  echo("       return true;\n");
  echo("     else\n");
  echo("     {\n");
  /*58(biblioteca) - Selecione pelo menos um item*/
  echo("       alert('".RetornaFraseDaLista($lista_frases_bibliotecas,58)."');\n");
  echo("       return false;\n");
  echo("     }\n");
  echo("  }\n");

  echo("  function CancelarImportacao()\n");
  echo("  {\n");
  echo("    document.frmImportar.action = \"importar_curso.php?");
  echo(RetornaSessionID()."\";\n");
  echo("    document.frmImportar.submit();\n");
  echo("  }\n\n");

  echo("  function CheckAll()\n");
  echo("  {\n");
  echo("    var elem = document.frmImportar.elements;\n");
  echo("    var nome_var1 = 'cod_topicos_import[]';\n");
  echo("    var nome_var2 = 'cod_itens_import[]';\n");
  echo("    var nome_var_all = 'select_all';\n");
  echo("    var changed = false;\n\n");

  echo("    var i=0;\n\n");

  echo("    while (i < elem.length)\n");
  echo("    {\n");
  echo("      if (elem[i].name == nome_var_all)\n");
  echo("        changed = elem[i].checked;\n");
  echo("      else if ((elem[i].name == nome_var1) || (elem[i].name == nome_var2))\n");
  echo("        elem[i].checked = changed;\n");
  echo("      i++;\n");
  echo("    }\n");
  echo("  }\n\n");


  echo("</script>\n\n");

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
  echo("\n");

  echo("\n");

  echo($cabecalho);
  if (isset($caminho_original))
  {
    // 70 - Importando para:
    echo("<font class=text>".RetornaFraseDaLista($lista_frases, 70)." </font>");
    echo($caminho_original);
    echo("<br>\n");
  }

  // 65 - Perguntas Freqüentes do curso:
  echo("<font class=textsmall>(".RetornaFraseDaLista($lista_frases,65)." \"<b>".$nome_curso_import."</b>\")</font><br>\n");

  $lista_topicos_ancestrais = RetornaTopicosAncestrais($sock, $tabela, $cod_topico_raiz_import);
  unset($path);
  foreach ($lista_topicos_ancestrais as $cod => $linha)
  {
    if ($cod_topico_raiz_import!=$linha['cod_assunto'])
    {
      $path = "<a class=text href=# onClick='MudarTopico(".$linha['cod_assunto'].")'>".$linha['nome']."</a> &gt;&gt; ".$path;
//                              ".RetornaSessionID()."&cod_curso_import=".$cod_curso_import."&cod_topico_raiz_import=".$linha['cod_topico']."\">".$linha['topico']."</a> &gt;&gt; ".$path;
    }
    else
    {
      $path = "<b class=text>".$linha['nome']."</b><br>\n";
    }
  }

  echo("<a href=# onMouseDown=\"MostraLayer(cod_topicos, 0);return(false);\"><img src=\"../figuras/estrutura.gif\" border=0></a>");
  echo($path);
  echo("<p>\n");

  echo("<form method=post name=frmImportar>\n");
  echo(RetornaSessionIDInput()."\n");

  echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("  <input type=hidden name=cod_categoria=".$cod_categoria.">\n");
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
  echo("    <td width=1% class=colorfield><input type=checkbox name=select_all onClick='CheckAll()'></td>\n");
  echo("    <td width=1% class=colorfield>&nbsp;</td>\n");
  /* 1 - Perguntas Freqüentes*/
  echo("    <td class=colorfield>".RetornaFraseDaLista($lista_frases,1)."</td>\n");
  /* 53 (bibli) - Data */
  echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases_bibliotecas,53)."</td>\n");

  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td colspan=5 height=5></td>\n");
  echo("  </tr>\n");

  $lista_topicos = RetornaTopicosDoTopico($sock, $tabela, $cod_topico_raiz_import);

  $lista_itens = RetornaItensDoTopico($sock, $tabela, $cod_topico_raiz_import);
  if (count($lista_topicos) < 1 && count($lista_itens) < 1)
  {
    echo("  <tr class=text>\n");
    /* 67 - Não há nenhuma pergunta freqüente.*/
    echo("    <td class=text colspan=5>".RetornaFraseDaLista($lista_frases,67)."</td>\n");
    echo("  </tr>\n");
  }
  else
  {
    if (count($lista_topicos)>0)
      foreach ($lista_topicos as $cod => $linha_topico)
      {
        $data="<font class=text>".UnixTime2Data($linha_topico['data'])."</font>";

        $max_data=RetornaMaiorData($sock,$tabela,$linha_topico['cod_assunto'],'F',$linha_topico['data']);

        if ($data_acesso<$max_data)
        {
          $marcaib="<b>";
          $marcafb="</b>";
          $marcatr=" bgcolor=#f0f0f0";
        }
        else
        {
          $marcaib="";
          $marcafb="";
          $marcatr="";
        }
        echo("  <tr".$marcatr.">\n");

        echo("    <td width=10><input type=checkbox name=cod_topicos_import[] value=".$linha_topico['cod_assunto']."></td>\n");
        echo("    <td width=10 align=center><a class=text href=# onClick='MudarTopico(");
        echo($linha_topico['cod_assunto'].")'><img src=../figuras/pasta.gif border=0></a></td>\n");

        echo("    <td><font class=text> - </font>");
        echo("<a class=text href=# onClick='MudarTopico(".$linha_topico['cod_assunto'].");'>");
        echo($linha_topico['nome']."</a>".$marcafb."</td>\n");


        echo("    <td align=center>".$marcaib.$data.$marcafb."</td>\n");
        echo("    <td align=center>&nbsp;</td>\n");
        echo("  </tr>\n");
        echo("  <tr>\n");
        echo("    <td colspan=5 height=1><hr size=1></td>\n");
        echo("  </tr>\n");
      }

    if (count($lista_itens)>0)
      foreach ($lista_itens as $cod => $linha_item)
      {
          $data="<font class=text>".UnixTime2Data($linha_item['data'])."</font>";

          if ($data_acesso<$linha_item['data'])
          {
            $marcaib="<b>";
            $marcafb="</b>";
            $marcatr=" bgcolor=#f0f0f0";
          }
          else
          {
            $marcaib="";
            $marcafb="";
            $marcatr="";
          }
          
            $compartilhamento=$marcaib."<a class=text>".$compartilhamento."</a>".$marcafb;

              $titulo = $marcaib."<a class=text href=# onClick='ExibirItem(";
              $titulo .= $linha_item['cod_pergunta'].");'>".$linha_item['pergunta']."</a>".$marcafb;

              $icone = "<a href=# onClick='ExibirItem(".$linha_item['cod_pergunta'].");'>";
              $icone .= "<img src=../figuras/arqp.gif border=0></a>";

            echo("  <tr".$marcatr.">\n");
            echo("    <td width=10><input type=checkbox name=cod_itens_import[] value=".$linha_item['cod_pergunta']."></td>\n");
            echo("    <td width=10 align=center>".$icone."</td>\n");
            echo("    <td>".$titulo."</td>\n");
            echo("    <td align=center>".$marcaib.$data.$marcafb."</td>\n");

            if (! $curso_compartilhado)
            {
              echo("    <td align=center>".$compartilhamento."</td>\n");
            }
            echo("  </tr>\n");
            echo("  <tr>\n");
            echo("    <td colspan=5 height=1><hr size=1></td>\n");
            echo("  </tr>\n");

      }  // de foreach
  }
  echo("</table>\n");
  echo("<br>\n");
  // 54 (ger) - Importar selecionados
  echo("<input type=button onClick='if(Validacheck())Importar();' value='".RetornaFraseDaLista($lista_frases_bibliotecas,54)."'>\n");
  // 2 - Cancelar (geral)
  echo("<input type=button onClick='CancelarImportacao()' value='".RetornaFraseDaLista($lista_frases_geral, 2)."'>\n");
  
  echo("</form>\n");

  /* Estrutura de Tópicos */
  echo("<div id=topicos class=block visibility=hidden onContextMenu='return(false);'>\n");
  echo("<table class=wtfield cellspacing=1 cellpadding=1 border=2>\n");
  echo("  <tr>\n");
  echo("    <td class=bgcolor align=right><a href=# onClick=EscondeLayer(cod_topicos);return(false);><img src=../figuras/x.gif border=0></a></td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  $lista_topicos = RetornaListaDeTopicos($sock, $tabela);
  if (count($lista_topicos) > 0)
  {
    foreach ($lista_topicos as $cod => $linha_topico)
    {
      if ($cod_topico_raiz_import == $linha_topico['cod_assunto'])
        echo("      ".$linha_topico['espacos']."<b class=text><img src=\"../figuras/pasta.gif\" border=0>".$linha_topico['nome']."</b><br>\n");
      else
      {
        echo("      ".$linha_topico['espacos']."<a class=text href=# onClick='MudarTopico(");
        echo($linha_topico['cod_assunto'].");EscondeLayer(cod_topicos);'>");
        echo("<img src=\"../figuras/pasta.gif\" border=0>");
        echo($linha_topico['nome']."</a><br>\n");
      }
    }
  }
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("</table>\n");
  echo("</div>\n");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
