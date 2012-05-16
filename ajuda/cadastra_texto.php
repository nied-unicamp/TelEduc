<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/ajuda/cadastra_texto.php

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
  ARQUIVO : administracao/ajuda/cadastra_texto.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include("ajuda.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("    <script type=\"text/javascript\">\n");

  /**********************************************************************
  Funcao AbrePopUp - JavaScript. Altera atributos do formul�rio para submiss�o � janela de visualiza��o.
    Entrada: Nenhuma. Funcao espec�fica para o formul�rio da p�gina.
    Saida: Transforma o formul�rio para o tipo 'submit' e manda o texto � janela "pop-up"
  */

  echo("      function AbrePopUp()\n");
  echo("      {\n");
  echo("        var excerto;\n");
  echo("        excerto = document.cadastra.texto.value;\n");
  echo("        document.cadastra.action=\"visualizar.php\";\n");
  echo("        window.open('visualizar.php?','','width=800,height=600,scrollbars')");
  echo("      }\n");

  /**********************************************************************
  Funcao AlteraTexto - JavaScript. Altera atributos do formul�rio para submiss�o � p�gina seguinte,
                       alterando o texto na base de dados.
    Entrada: Nenhuma. Funcao espec�fica para o formul�rio da p�gina.
    Saida: Transforma o formul�rio para o tipo 'submit' e manda o texto � pr�xima p�gina
  */

  echo("      function AlteraTexto()\n");
  echo("      {\n");
  echo("        document.cadastra.action=\"cadastra_texto2.php?cod_ferramenta=".$cod_ferramenta."\";\n");
  echo("        document.cadastra.submit();\n");
  echo("      }\n");

  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("      }\n");

  echo("    </script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 171 - Cadastro de texto da Ajuda */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,171)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <form name=\"cadastra\" action=\"cadastra_texto2.php\" method=\"post\">\n");
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><a href=\"#\" title=\"Voltar\" onClick=\"document.location='index.php?cod_ferramenta=".$cod_ferramenta."&cod_lingua=".$cod_lingua."&tipo_usuario=".$tipo_usuario."&modo=".$modo."';\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  $lista_ferramentas=RetornaFerramentasOrdemMenu();
  $sock=Conectar("");
  $lista_lingua=ListaLinguas($sock);

  if($criar=='sim')
  {
    $cod_lingua=1;
    $modo="E";
  }

  /* 82 - L�ngua: */
  echo("                  <tr>\n");
  echo("                    <td align=left colspan=4>".RetornaFraseDaLista($lista_frases,82)." <b>".$lista_lingua[$cod_lingua]."</b></td>\n");
  echo("                  </tr>\n");
  /* 83 - Ferramenta: */
  echo("                  <tr>\n");
  echo("                    <td align=left colspan=4>".RetornaFraseDaLista($lista_frases,83)." <b>\n");
  if ($cod_ferramenta<0)
    /* 140 - Configurar */
    echo(RetornaFraseDaLista($lista_frases,140)."</b></td>\n");
  else if ($cod_ferramenta==0)
    /* 80 - Administra��o Interna (curso) */
    echo(RetornaFraseDaLista($lista_frases,80)."</b></td>\n");
  else
    echo($lista_ferramentas[$cod_ferramenta]."</b></td>\n");
  echo("                  </tr>");
  /* 173 - Modo de visualiza��o: */
  echo("                  <tr>\n");
  echo("                    <td align=left colspan=4>".RetornaFraseDaLista($lista_frases,173)."<b>\n");
  if ($tipo_usuario=="F")
    /* 522 - Visao do Formador */
    echo(RetornaFraseDaLista($lista_frases,522)."</b></td>\n");
  else if ($tipo_usuario=="A")
    /* 523 - Visao de Aluno */
    echo(RetornaFraseDaLista($lista_frases,523)."</b></td>\n");
  echo("                  </tr>");

  $cod_lingua_base=1;

  if($criar=='sim')
    $cod_pagina=MaxCodPagina($sock,$cod_ferramenta,$cod_lingua,$tipo_usuario);
  else
  {
    $nome_pagina=RetornaNomePagina($sock,$cod_pagina,$cod_ferramenta,$cod_lingua,$tipo_usuario);
    $nome_pagina_base=RetornaNomePagina($sock,$cod_pagina,$cod_ferramenta,$cod_lingua_base,$tipo_usuario);
    $texto=RetornaTextoDaAjuda($sock,$cod_ferramenta,$cod_pagina,$cod_lingua,$tipo_usuario);
  }

  echo("                  <tr class=\"head\">\n");
  echo("                    <td width=\"20%\">Codigo da pagina</td>\n");
  echo("                    <td>Nome da pagina</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td><b>".$cod_pagina."</b></td>\n");
  if ($criar=='nao' && $modo!='E')
   echo("                    <td>".$nome_pagina."</td>\n");
  else
    echo("                    <td colspan=\"2\"><input class=\"input\" type=\"text\" width=\"20\" name=\"nome_pagina\" value='".$nome_pagina."'></td>\n");  
  echo("                  </tr>\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"2\">Conteudo</td>\n");
  echo("                  </tr>\n");

  /* Modo de Edição */
  if ($modo=="E")
  {

    /* Verifica se a lingua utilizada não é o português */
    if ($cod_lingua!=1)
    {
      $texto_base=RetornaTextoDaAjuda($sock,$cod_ferramenta,$cod_pagina,$cod_lingua_base,$tipo_usuario);

      /* 178 - Texto base em portugu�s */
      echo("                  <tr class=\"head01\">\n");
      echo("                    <td><b>".RetornaFraseDaLista($lista_frases,178)."</b></td>\n");
      /* 177 - Texto a ser editado */
      echo("                    <td><b>".RetornaFraseDaLista($lista_frases,177)."</b></td>\n");
      echo("                  </tr>\n");
      echo("                  <tr>\n");
      echo("                    <td><textarea class=\"input\" name=\"texto\" cols=\"40\" rows=\"15\">".$texto_base."</textarea></td>\n");
      echo("                    <td colspan=\"2\"><textarea class=\"input\" name=\"texto\" cols=\"40\" rows=\"15\">".$texto."</textarea></td>\n");
      echo("                  </tr>\n");
    }
    else
    {
      echo("                  <tr>\n");
      echo("                    <td colspan=\"2\"><textarea class=\"input\" name=\"texto\" cols=\"80\" rows=\"15\">".$texto."</textarea></td>\n");
      echo("                  </tr>\n");
    }

    echo("                </table>\n");
    echo("                <div align=\"right\">\n");
    /* 179 - Visualizar */
    echo("                  <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,179)."\" onClick=\"AbrePopUp();\" type=\"button\">\n");
    /* 24 - Alterar (Ger) */
    echo("                  <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,24)."\" onClick=\"AlteraTexto();\" type=\"button\">\n");
    echo("                </div>\n");
  }

  /* Modo de Visualização */
  else if ($modo=="V")
  {
    /* Verifica se há algum texto */
    if (isset($texto))
    {
      echo("                  <tr>\n");
      echo("                    <td colspan=\"2\">".$texto."</td>\n");
    }
    else
    {
      /* 174 - Não há texto cadastrado para essa página. */
      echo("                  <tr>\n");
      echo("                    <td>".RetornaFraseDaLista($lista_frases,174)."</td>\n");
    }
    echo("                  </tr>\n");
    echo("                </table>\n");
  }

  echo("                <input type=hidden name=cod_pagina value=".$cod_pagina.">\n");
  echo("                <input type=hidden name=cod_lingua value=".$cod_lingua.">\n");
  echo("                <input type=hidden name=tipo_usuario value=".$tipo_usuario.">\n");
  echo("                <input type=hidden name=criar value=".$criar.">\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
