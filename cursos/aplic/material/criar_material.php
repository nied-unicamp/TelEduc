<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/criar_material.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
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

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/material/criar_material.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("material.inc");

  /* Registrando c�igo da ferramenta nas vari�eis de sess�.
     �necess�io para saber qual ferramenta est�sendo
     utilizada, j�que este arquivo faz parte de quatro
     ferramentas quase distintas.
   */
  session_register("cod_ferramenta_m");
  if (isset($cod_ferramenta))
    $cod_ferramenta_m=$cod_ferramenta;
  else
    $cod_ferramenta=$cod_ferramenta_m;

  session_register("cod_item_s");
  if (isset($cod_item))
    $cod_item_s=$cod_item;
  else
    $cod_item=$cod_item_s;

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta);

  echo("<html>\n");
  /* 1 - 3: Atividades
         4: Material de Apoio
         5: Leituras
         7: Parada Obrigat�ia
   */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  switch ($cod_ferramenta) {
    case 3 :
      echo("  <link rel=stylesheet TYPE=text/css href=atividades.css>\n");
      $tabela="Atividade";
      $dirname="atividades";
      break;
    case 4 :
      echo("  <link rel=stylesheet TYPE=text/css href=apoio.css>\n");
      $tabela="Apoio";
      $dirname="apoio";
      break;
    case 5 :
      echo("  <link rel=stylesheet TYPE=text/css href=leituras.css>\n");
      $tabela="Leitura";
      $dirname="leituras";
      break;
    case 7 :
      echo("  <link rel=stylesheet TYPE=text/css href=obrigatoria.css>\n");
      $tabela="Obrigatoria";
      $dirname="obrigatoria";
      break;
  }

  if (EFormador($sock, $cod_curso, $cod_usuario))
  {
    $cod_item=IniciaCriacao($sock, $tabela, $cod_topico, $cod_usuario, $cod_curso, $dirname, $diretorio_temp);
    echo("<body onLoad=\"document.criar.submit();\">\n");

    echo("<form name=criar action=\"editar_material2_new.php\" method=post>\n"); // dani -  target=trabalho
    echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("  <input type=hidden name=cod_item value=".$cod_item.">\n");
    echo("  <input type=hidden name=cod_topico value=".$cod_topico.">\n");
    echo("  <input type=hidden name=origem value=".$origem.">\n");
    echo("  <input type=hidden name=titulo value=\"\">\n");
    echo("  <input type=hidden name=texto value=\"\">\n");
    echo("  <input type=hidden name=compartilhamento value=\"\">\n");
    echo("</form>\n");

    echo("</body>\n");
    echo("</html>\n");

  }
  else
  {
    /* Acesso Negado */
  }

  Desconectar($sock);
  exit;

?>
