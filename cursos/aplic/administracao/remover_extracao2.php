<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/remover_extracao2.php

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
  ARQUIVO : cursos/aplic/administracao/remover_extracao2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  include("../topo_tela.php");

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
    include("../menu_principal.php");
    
    echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    
    /* 1 - Administracao  28 - Area restrita ao formador. */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,28)."</h4>\n");
  
    /*Voltar*/
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");
  
    /* 23 - Voltar (gen) */
    echo("          <form><input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");
  
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }

  echo("  </head>\n");

  echo("  <body link=#0000ff vlink=#0000ff onLoad='document.frmMarcar.cmdVoltar.focus();'>\n");

  /* 1 - Administra�o */
  $cabecalho = "    <b class=\"titulo\">".RetornaFraseDaLista($lista_frases, 1)."</b>";
  /* 213 - Listar / Remover Extra�o do Curso */
  $cabecalho .= "    <b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases, 213)."</b>";
  echo(PreparaCabecalho($sock,$cod_curso, $cabecalho, 0,19));
  echo("    <br>\n");

  echo("    <form name=\"frmMarcar\" method=\"post\" action=\"administracao.php\">\n");
  echo("    <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");

  echo("      <font class=\"text\">\n");

  Desconectar($sock);
  $sock2 = Conectar("");
  
  if(count($selecionados) > 0)
  {
    /* 144 - Opera�o conclu�a com sucesso! */
    echo("        ".RetornaFraseDaLista($lista_frases, 144)."\n");

    foreach($selecionados as $sels => $usuario)
    {
      $query = "delete from Extracoes_agendadas where cod_usuario=$usuario and cod_curso=$cod_curso and extraido=0;";
      Enviar($sock2, $query);
    }
  }
  else
  {
    /* 224 - Selecione pelo menos um usu�io para ser removido da extra�o. */
    echo(RetornaFraseDaLista($lista_frases, 224));
  }
  
  Desconectar($sock2);
  echo("      </font>\n");
  echo("      <input type=\"submit\" name=\"cmdVoltar\" class=\"text\" value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
  echo("    </form>\n");

  echo("    </body>\n");
  echo("  </html>\n");

?>
