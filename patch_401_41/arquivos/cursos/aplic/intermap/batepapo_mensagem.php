<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/batepapo_mensagem.php

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
  ARQUIVO : cursos/aplic/intermap/batepapo_mensagem.php
  ========================================================== */

/* Código principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("intermap.inc");
  include("batepapo.inc");

  $cod_ferramenta=19;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  /* topo_tela faz isso
  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,19);
  $lista_frases_batepapo=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario); */

  $lista_frases_batepapo=RetornaListaDeFrases($sock,10);

  echo("<script type=\"text/javascript\" language=javascript>\n");
  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  } \n");
  echo("</script>\n");

  echo("<body onLoad=\"Iniciar();\">\n");

  echo("<br><br>\n");
  /* 1 - Intermap */
  echo("<h4>".RetornaFraseDaLista($lista_frases,1));
  /* 14 - Bate-papo */
  echo(" - ".RetornaFraseDaLista($lista_frases,14)."</h4>\n");
  echo("<br>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  echo("      <ul class=\"btAuxTabs\">\n");
  // 26 - Fechar
  echo("        <li><span title=\"Fechar\" onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases,26)."</span></li>\n");
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  // 39 - Mensagem
  echo("          <td>".RetornaFraseDaLista($lista_frases,39)."</td>\n");
  echo("        </tr>\n");

  $lista_conversas=RetornaConversaSessao($sock,$cod_sessao,$cod_usu,$data,$data+5);

  if (count($lista_conversas)>0)
  {
    foreach ($lista_conversas as $cod => $linha)
    {
      echo("        <tr>\n");
      echo("          <td>\n");
      echo("<font class=textsmall>(".Unixtime2Hora($linha['Data']).")</font>\n");
      if ($cod_usuario == $linha['cod_usuario']) 
      {
        print "<font class=text color=#2a6686>";
      }
      if ($cod_usuario == $linha['cod_usuario_r']) 
      {
        print "<font class=text color=#2a6686>";
      }
      echo("<b>".$linha['Apelido']."</b> ".RetornaFraseDaLista($lista_frases_batepapo,$linha['cod_texto_fala'])." ");
      if ($linha['cod_texto_fala']>8) /* Não é entrada ou saída... */
      {
        echo("<b>".$linha['ApelidoR']."</b>: ".$linha['Mensagem']);
      }
      echo("</font>\n");
      echo("          </td>\n");
      echo("        </tr>\n");
    }
  }

  // Fim Tabela Interna
  echo("      </table>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabelão
  echo("</table>\n");

  Desconectar($sock);

  echo("</body>\n");
  echo("</html>\n");

?>