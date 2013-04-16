<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/editar_material.php

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
  ARQUIVO : cursos/aplic/material/editar_material.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("material.inc");

  /*session_register("texto_s");
  session_register("titulo_s");*/
  /*session_register("compartilhamento_s");*/
  
  $texto = $_POST['texto'];
  $titulo = $_POST['titulo'];
  $compartilhamento = $_POST['compartilhamento'];

  session_register("cod_item_s");
  if (isset($cod_item))
    $cod_item_s=$cod_item;
  else
    $cod_item=$cod_item_s;

  session_register("cod_topico_s");
  if (isset($cod_topico))
    $cod_topico_s=$cod_topico;
  else
    $cod_topico=$cod_topico_s;

  session_register("origem_s");
  if (isset($origem))
    $origem_s=$origem;
  else
    $origem=$origem_s;

  session_register("cod_ferramenta_m");
  if (isset($cod_ferramenta))
    $cod_ferramenta_m=$cod_ferramenta;
  else
    $cod_ferramenta=$cod_ferramenta_m;

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta);

  switch ($cod_ferramenta) {
    case 3 :
      $css="<link rel=\"stylesheet\" TYPE=\"text/css\" href=\"atividades.css\">";
      $tabela="Atividade";
      $dir="atividades";
      break;
    case 4 :
      $css="<link rel=\"stylesheet\" TYPE=\"text/css\" href=\"apoio.css\">";
      $tabela="Apoio";
      $dir="apoio";
      break;
    case 5 :
      $css="<link rel=\"stylesheet\" TYPE=\"text/css\" href=\"leituras.css\">";
      $tabela="Leitura";
      $dir="leituras";
      break;
    case 7 :
      $css="<link rel=\"stylesheet\" TYPE=\"text/css\" href=\"obrigatoria.css\">";
      $tabela="Obrigatoria";
      $dir="obrigatoria";
      break;
  }

  /* Verifica se a pessoa a editar � formador */
  if (!EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("<html>\n");
    /* 1 - 3: Atividades
           4: Material de Apoio
           5: Leituras
           7: Parada Obrigat�ria
     */
    echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
    echo("  <link rel=\"stylesheet\" TYPE=\"text/css\" href=\"../teleduc.css\">\n");
    echo("  ".$css."\n");

    echo("<body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=white>\n");
    /* 1 - 3: Atividades
           4: Material de Apoio
           5: Leituras
           7: Parada Obrigat�ria
     */
    /* 45 - �rea restrita ao formador. */
    $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,45)."</b>\n";
    $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,45)."</b>\n";

    /* Cabecalho */
    echo($cabecalho);


    /* 23 - Voltar (gen) */
    echo("<form><input class=\"input\" type=button value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");
    echo("</body></html>\n");
    Desconectar($sock);
    exit;
  }
  PegaSemaforo($sock,$tabela);

  //echo("<p><font color=red>RetornaDadosDoItem($sock, $tabela, $cod_item)</font><br>\n");
  
  $linha=RetornaDadosDoItem($sock, $tabela, $cod_item);
  $linha_hist=RetornaUltimaPosicaoHistorico($sock, $tabela, $cod_item);
  if (count($linha)>0)
  {
    if ($linha['status']=='E')
    {
      /* algu�m j� est� editando */

      /* Ve se n�o � voc� */
      if ($cod_usuario!=$linha['cod_usuario'])
      {

        if ($linha_hist['data']>time()-1800)
        {
          echo("<html>\n");
          /* 1 - 3: Atividades
                 4: Material de Apoio
                 5: Leituras
                 7: Parada Obrigat�ria
           */
          echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
          echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
          echo("  ".$css."\n");

          LiberaSemaforo($sock,$tabela);
          /* N�o faz... Avisa para usu�rio quem est� editando e volta */
          echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
          /* 1 - 3: Atividades
                 4: Material de Apoio
                 5: Leituras
                 7: Parada Obrigat�ria
           */
          $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n";
          /* 46 - Editar Material */
          $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,46)."</b>\n";

          echo($cabecalho);

          /* 47 - O Material j� est� sendo editado desde */
          echo("<p><font class=text>".RetornaFraseDaLista($lista_frases,47)." ");

          /* 48 - por */
          echo(Unixtime2DataHora($linha['data'])." ".RetornaFraseDaLista($lista_frases,48));

          echo(" ".NomeUsuario($sock,$linha['cod_usuario'], $cod_curso).".<br /><br />");

          /* 23 - Voltar (gen) */
          echo("<form><input class=\"input\" type=button value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");
          echo("</body></html>\n");
          Desconectar($sock);
          exit;
        }
        /* Passou o tempo limite, captura a edi��o */
      }
      /* �. Atualiza data e segue em frente. */

      CancelaEdicao($sock, $tabela, $dir, $linha['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_avaliacao);
      MudaStatusEdicao($sock, $tabela, $cod_item, $cod_usuario);
    }
    else
    {
      /* Pega e segue em frente */
      MudaStatusEdicao($sock, $tabela, $cod_item, $cod_usuario);
    }
  }
  else
  {
    /* N�o deveria acontecer... */
    /* 89 - Erro Interno...*/
    exit(RetornaFraseDaLista($lista_frases,89));
  }

  LiberaSemaforo($sock,$tabela);

  /* Copia arquivos pro temporario */
  if (!CopiaArquivoParaTemporario($sock,$cod_curso,$diretorio_arquivos,$diretorio_temp,$dir,$cod_item))
  {
    echo("<html>\n");
    /* 1 - 3: Atividades
           4: Material de Apoio
           5: Leituras
           7: Parada Obrigat�ria
     */
    echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("  ".$css."\n");

    echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    /* 1 - 3: Atividades
           4: Material de Apoio
           5: Leituras
           7: Parada Obrigat�ria
     */
    $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n";

    echo($cabecalho);

    echo("<br>\n");
    echo("<p>\n");
    /* 49 - Erro ao criar diret�rio tempor�rio. Avise o suporte. */
    echo(RetornaFraseDaLista($lista_frases,49)."<br>\n");

    /* 23 - Voltar (gen) */
    echo("<form><input class=\"input\" type=button value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");
    echo("</body></html>\n");
    Desconectar($sock);
    exit;
  }

  /* Arquivos no Temporario. Ir para Tela seguinte */

  $texto=ConverteAspas2Html($linha['texto']);
  $titulo=ConverteAspas2Html($linha['titulo']);

  Desconectar($sock);
  echo("<body>\n");
  echo("<table><tr><td>");
  echo("<form name=tela action=ver.php method=post>\n"); 
  echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("  <script type=\"text/javascript\" language=\"JavaScript\">alert('".$cod_item."');</script>\n");
  echo("  <input type=hidden name=cod_item value=".$cod_item.">\n");
  echo("  <input type=hidden name=cod_topico_raiz value=".$cod_topico_raiz.">\n");
  echo("</form>\n");

  echo("<script type=\"text/javascript\" language=\"JavaScript\">\n");
  echo("  document.tela.submit();\n");
  echo("</script>\n");
  echo("</body>\n");

?>
