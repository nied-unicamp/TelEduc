<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/entrar_sala.php

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
  ARQUIVO : cursos/aplic/batepapo/entrar_sala.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  /* topo_tela.php faz isso
  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10); */

  /* Encerra sess�o anterior, se n�o tiver ningu�m online */
  $cod_sessao=RetornaSessaoCorrente($sock);
  if (VerificaRetiradaOnline($sock))
  {
    LimpaOnline($sock,$cod_curso, 90);
  }

  if (!VerificaOnline($sock))
  {
    /* Todas as pessoas foram retiradas. Encerramos a sessao ent�o */
    EncerraSessao($sock,$cod_curso,$cod_sessao);
    $cod_sessao=RetornaSessaoCorrente($sock);
  }

  /* Verifica se j� est� online e se j� tem apelido. Se tiver, j� o deixa preenchido */
  $apelido=RetornaApelido($sock,$cod_sessao,$cod_usuario);

  echo("<script type=\"text/javascript\" language=javascript>\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  } \n");

  echo("  function conferir()\n");
  echo("  {\n");
  echo("    if (document.entrar.apelido.value == '')\n");
  echo("    {\n");
  /* 6 - Por favor digite um apelido ou seu nome antes de entrar na sala de bate-papo. */
  echo("      alert('".RetornaFraseDaLista($lista_frases,6)."');\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("    else\n");
  echo("      return true;\n");
  echo("  }\n");

  echo("  function OpenWindowLink(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"../perfil/exibir_perfis.php?&cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("</script>\n");
  echo("</head>\n");
  echo("<body onLoad=\"Iniciar();\">\n");

  echo("<br/><br/>\n");
  /* 1 - Bate-Papo */
  echo("<h4>".RetornaFraseDaLista($lista_frases,1));
  /* 2 - Entrar na sala de bate-papo */
  echo(" - ".RetornaFraseDaLista($lista_frases,2)."</h4>");
  echo("<br>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");
  echo("      <ul class=\"btAuxTabs\">\n");
  /* 13 - Fechar (Ger) */
  echo("        <li><span title=\"Fechar\" onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral, 13)."</span></li>\n");
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");

  echo("      <form name=\"entrar\" action=\"index_sala.php?cod_curso=".$cod_curso."\" method=\"post\" onSubmit=\"return(conferir());\">\n");
  //echo(RetornaSessionIDInput());
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");

  echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  /* 3 - Para entrar na sala de bate-papo, digite abaixo o nome que voc� deseja usar e pressione o bot�o Entrar. */
  echo("          <td>".RetornaFraseDaLista($lista_frases,3)."</td>\n");
  echo("        </tr>\n");
  echo("        <tr>\n");
  /* 4 - Nome: */
  echo("          <td>".RetornaFraseDaLista($lista_frases,4));
  echo("            <input class=\"input\" type=\"text\" maxlength=\"16\" name=\"apelido\" value='".$apelido."'></td>\n");
  echo("        </tr>\n");
  // Fim Tabela Interna
  echo("      </table>\n");

  echo("<br/><br/>\n");

  $lista_online=RetornaListaApelidosOnline($sock,$cod_sessao);

  echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  echo("        <tr class=\"head\">\n");
  /* 71 - Pessoas na sala de bate-papo */
  echo("          <td>".RetornaFraseDaLista($lista_frases,71)."</td>\n");
  echo("        </tr>\n");
  
  if (count($lista_online)>0)
  {

    $i=1;
    foreach ($lista_online as $cod => $linha)
    {
      $i = ($i + 1) % 2;
      if ($i==0)
        echo("        <tr>");
      else
        echo("        <tr>");
      if ($cod>0)
      {
        echo("          <td>".stripslashes($linha)." - <i><a href=# onclick=OpenWindowLink(".$cod.");>\n");
        echo(NomeUsuario($sock,$cod,$cod_curso)."</a></i></td></tr>\n");
      }
      else
        echo("          <td>".stripslashes($linha)." <i>".NomeUsuario($sock,$cod,$cod_curso)."</i></td></tr>\n");
    }
  }
  else
  {
    echo("        <tr>");
    /* 68 - Nenhuma pessoas na sala de bate-papo */
    echo("          <td>".RetornaFraseDaLista($lista_frases,68)."</td>\n");
    echo("        </tr>\n");
  }

  // Fim Tabela Interna
  echo("      </table>\n");

  echo("      <div align=\"right\">\n");
  /* 10 - Entrar */
  echo("        <input class=\"input\" type=\"submit\" value='".RetornaFraseDaLista($lista_frases_geral,10)."'>");
  echo("      </div>\n");

  echo("      </form>\n");

  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabel�o
  echo("</table>\n");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
