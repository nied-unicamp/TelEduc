<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/alterar_avaliacao2.php

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
  ARQUIVO : cursos/aplic/avaliacoes/alterar_avaliacao2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");


  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,22);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  echo("<html>\n");
  /* 1 - Avalia��es*/
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  $tabela="Avaliacao";

  echo("    <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");
  echo("\n");

  echo("<script languague=JavaScript>\n");
  echo("  function Atualiza() {\n");
  echo("    document.atualizar.titulo.value=top.opener.document.material.titulo.value;\n");
  echo("    document.atualizar.texto.value=top.opener.document.material.texto.value;\n");
  echo("    document.atualizar.compartilhamento.value=top.opener.document.material.tipo_comp.value;\n");
  echo("    document.atualizar.submit();\n");
  echo("  }\n");
  echo("</script>\n");

  /* Verifica se a pessoa a editar � formador */
  if (!EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    /* 1 - Avaliac�es*/
    echo("<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>\n");
    /* 8 - �rea restrita ao formador. */
    echo("<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,8)."</b><br>\n");
    /* 23 - Voltar (gen) */
    echo("<form><input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");
    echo("</body></html>\n");
    Desconectar($sock);
    exit;
  }

  $virgula = strstr($valor, ",");
  if (strcmp($virgula,""))
  {
    $tmpvalor=explode(",",$valor);
    $valor=implode(".", $tmpvalor);
  }

  if (strcmp($ferramenta,'B'))
  {
    $hora_inicio='00';
    $hora_fim='23';
        
    $data_inicio=DataHora2Unixtime($data_inicio." ".$hora_inicio.":00");
    $data_termino=DataHora2Unixtime($data_termino." ".$hora_fim.":59");
    if (!strcmp($ferramenta,'P'))
      AlteraCadastroAvaliacao($sock, $tabela, $cod_usuario,trim($objetivos),trim($criterios),$tipo,$valor,$data_inicio,$data_termino,$cod_avaliacao);
    elseif (!strcmp($ferramenta,'F'))
      AlteraCadastroAvaliacao($sock, $tabela, $cod_usuario,trim($objetivos),trim($criterios),'',$valor,$data_inicio,$data_termino,$cod_avaliacao);
    elseif (!strcmp($ferramenta,'E'))
      AlteraCadastroAvaliacaoExercicio($sock, $tabela, $cod_usuario,trim($objetivos),trim($criterios),$tipo,$valor,$data_inicio,$data_termino,$cod_avaliacao);
    elseif (!strcmp($ferramenta,'N'))
      AlteraCadastroAvaliacao($sock, $tabela, $cod_usuario,trim($objetivos),trim($criterios),$tipo,$valor,$data_inicio,$data_termino,$cod_avaliacao);
        
  }
  else
     AlteraCadastroAvaliacao2($sock, $tabela, $cod_usuario,trim($objetivos),trim($criterios),$valor,$cod_avaliacao);

  AtualizaFerramentasNova($sock,22,'T');

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus();>\n");
  /* 1 - Avaliac�es*/
  $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 9 - Cadastro de Avalia��o */
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,9)."</b>";
  // nao ha ajuda para esta p�gina.
  $cod_pagina = -1;
  echo(PreparaCabecalho($cod_curso,$cabecalho,22, $cod_pagina));

  echo("<br>\n");
  echo("<p>\n");


  echo("<form action=".$origem.".php?".RetornaSessionID()." method=post>\n");

  /* 25 - Avalia��o alterada com sucesso. */
  echo("<font class=text>".RetornaFraseDaLista($lista_frases,25)."<br><br>\n");

  if ($VeioDaAtividade)
  {
    if ((!strcmp($ferramenta,'P')) && (!strcmp($origem,'../material/editar_material2')))
    {
      /* 35 - Voltar */
      echo("  <input class=text type=submit value='Voltar' onclick=\"Atualiza();self.close();\" class=text>\n");
      echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
      echo("  <input type=hidden name=cod_topico_raiz value=".$cod_topico.">\n");
      echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
      echo("  <input type=hidden name=criacao_avaliacao value=".$criacao_avaliacao.">\n");

      echo("</form>\n");

      echo("<form name=atualizar action=".$origem.".php method=post target=trabalho>\n");
      echo(RetornaSessionIDInput());
      echo("  <input type=hidden name=cod_ferramenta value=3>");/*Atividades que perde a variavel de sess�o quando aberta a ajuda*/
      echo("  <input type=hidden name=titulo value=\"\">\n");
      echo("  <input type=hidden name=texto value=\"\">\n");
      echo("  <input type=hidden name=compartilhamento value=\"\">\n");
      echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
      echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
      echo("  <input type=hidden name=tipo value=".$tipo.">\n");
      echo("  <input type=hidden name=alteracao value=1>\n");
      echo("  <input type=hidden name=criacao_avaliacao value=".$criacao_avaliacao.">\n");
      echo("</form>\n");
    }
    else
    {
    /* 13 - Fechar (ger) */
      echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,13)."' onClick=self.close()>\n");
      echo("</form>\n");
    }
  }
  else
  {
    /* 23 - Voltar (ger))*/
    echo("  <input class=text type=submit value='".RetornaFraseDaLista($lista_frases_geral,23)."' self.close();\" class=text>\n");
    echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
    echo("</form>\n");
  }

  echo("</body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
