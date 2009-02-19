<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perfil/enviar_foto.php

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
  ARQUIVO : cursos/aplic/perfil/enviar_foto.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perfil.inc");

  $cod_ferramenta=13;
  include("../topo_tela.php");
  /*
==================
Funcoes JavaScript
==================
*/
  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function VerificaExtensao(arquivo)\n");
  echo("      {\n");
  echo("        var nome_arquivo=arquivo;\n");
  echo("        var pedacos=nome_arquivo.split('.');\n");
  echo("        var ext=pedacos[pedacos.length - 1];\n");
  echo("        ext=ext.toLowerCase();\n");
  echo("        if (arquivo == '')\n");
  echo("        {\n");
  /* 105 - Escolha primeiramente um arquivo clicando no bot� Browse (Procurar). */
  echo("          alert('".RetornaFraseDaLista($lista_frases,105)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        if (ext != 'gif' && ext !='jpg' && ext != 'jpeg')\n");
  echo("        {\n");
  /* 104 - Arquivo inv�ido! S�ser� aceitos arquivos com extens� jpg ou gif. */
  echo("          alert('".RetornaFraseDaLista($lista_frases,104)."');\n");
  echo("          return false;\n");
  echo("        }  \n");
  echo("        return true;\n");
  echo("      }\n\n");

  echo("      function Submissao(pagina)\n");
  echo("      {\n");
  echo("        document.anexar.action = pagina;\n");
  echo("        document.anexar.submit();\n");
  echo("      }\n\n");
  
  echo("    </script>\n");
  echo("  </head>\n");
  echo("  <body bgcolor=\"white\" onload=\"self.focus();Iniciar();\">\n");
  echo("    <a name=\"topo\"></a>\n");
  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
  echo("      <tr>\n");
  echo("        <td></td>\n");
  echo("        <td valign=\"top\">\n");
  /* 1 - Perfil */
  $cabecalho ="<h4>".RetornaFraseDaLista($lista_frases,1);
  /* 40 - Enviando Foto */
  $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,40)."</h4>";
  echo("          <br /><br />".$cabecalho);
  echo("          <br />\n");
  echo("          <form name=\"anexar\" action=\"enviar_foto2.php?cod_curso=".$cod_curso."\" enctype=\"multipart/form-data\" method=\"post\" onsubmit=\"return(VerificaExtensao(document.anexar.arquivo.value));\">\n");

  if (isset($alunocod))
    echo("            <input type=\"hidden\" name=\"alunocod\" value=\"".$alunocod."\" />\n");
  if (isset($convidadocod))
    echo("            <input type=\"hidden\" name=\"convidadocod\" value=\"".$convidadocod."\" />\n");
  if (isset($visitantecod))
    echo("            <input type=\"hidden\" name=\"visitantecod\" value=\"".$visitantecod."\" />\n");
  if (isset($formadorcod))
    echo("            <input type=\"hidden\" name=\"formadorcod\" value=\"".$formadorcod."\" />\n");
  if (isset($coordenadorcod))
    echo("            <input type=\"hidden\" name=\"coordenadorcod\" value=\"".$coordenadorcod."\" />\n");

  echo("            <br />\n");
  if ($erro==sim)
  {
    echo("           ". RetornaFraseDaLista($lista_frases,107));
    echo("            <br />\n");
    echo("            <br />\n");
  }
  
 
  /* 96 - Pressione o bot� Browse (ou Procurar) abaixo para selecionar o arquivo a ser anexado; em seguida, pressione 'Anexar foto' para prosseguir.*/
  echo("            ".RetornaFraseDaLista($lista_frases,81));
  echo(RetornaFraseDaLista($lista_frases,96)."<br />\n");
  echo("            <br />\n");
  echo("            <input class=\"input\" type=\"file\" name=\"arquivo\" /><br />\n");
  echo("            <br />\n");
  /* 42 - Anexar foto */
  echo("            <input class=\"input\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases,42)."\" />\n");
  echo("            &nbsp;&nbsp;&nbsp;\n");
  /* G 2 - Cancelar */
  echo("            <input class=\"input\" type=\"button\" onclick=\"history.go(-1);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  echo("          </form>\n");
  echo("          <br />\n");
  echo("          <br />\n");
  echo("          <br />\n");

  /* 95 - Obs */
  echo("          <b>".RetornaFraseDaLista($lista_frases,95).".:</b><br />\n");
  /* 97 - Esta foto deve ser nos formatos JPEG ou GIF (arquivos com extens� */
  /* 98 - jpg*/
  /* 99 - ou */
  /* 103 - gif */
  /* 100 - Aconselhamos que ela seja do tamanho 90x120 pixels (ou maior, mas na mesma propor�o) para que ela n� apare� distorcida ou sem resolu�o. */
  echo("          &nbsp;&nbsp;&nbsp;- ".RetornaFraseDaLista($lista_frases,97)." <b> ".RetornaFraseDaLista($lista_frases,98)."</b> ".RetornaFraseDaLista($lista_frases,99)." <b> ".RetornaFraseDaLista($lista_frases,103)."</b>) ".RetornaFraseDaLista($lista_frases,100)."<br />\n");
  /* 101 - Voc�poder�alterar esta foto quando desejar, simplesmente enviando outra foto em seu lugar. */
  echo("          &nbsp;&nbsp;&nbsp;- ".RetornaFraseDaLista($lista_frases,101)."<br />\n");
  /* 102 - Esta foto ficar�dispon�el em seu Perfil para todos os participantes deste curso. */
  echo("          &nbsp;&nbsp;&nbsp;- ".RetornaFraseDaLista($lista_frases,102)."<br />\n");

  echo("          <script type=\"text/javascript\">\n");
  echo("            Iniciar();\n");
  echo("          </script>\n");

  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
