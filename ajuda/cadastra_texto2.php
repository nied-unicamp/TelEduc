<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/ajuda/cadastra_texto2.php

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
  ARQUIVO : administracao/ajuda/cadastra_texto2.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include("ajuda.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script language=JavaScript>\n");
  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("  }\n");
  echo("</script>\n");
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
  
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar */
  echo("                  <li><span onClick=\"document.location='index.php?cod_ferramenta=".$cod_ferramenta."&cod_lingua=".$cod_lingua."&modo=".$modo."&tipo_usuario=".$tipo_usuario."';\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr>\n");
  echo("                    <td>\n");

  if ($tipo_usuario=='A')
    $tipo_usuario_outro='F';
  else if ($tipo_usuario='F')
    $tipo_usuario_outro='A';

  $outras_paginas=RetornaPaginasdaFerramenta($sock,$cod_ferramenta,$cod_lingua,$tipo_usuario_outro);

  if (count($outras_paginas))
    foreach ($outras_paginas as $cod => $linha)
      if ($linha['nome_pagina']==$nome_pagina)
      {
        $cod_pagina_outra=$linha['cod_pagina'];
        if ($cod_pagina_outra>=$cod_pagina)
        {
          $mudou='sim';
          $cod_antigo=$cod_pagina;            /* vari�veis usadas para o caso de n�o estar criando */
          $cod_pagina=$cod_pagina_outra;
          unset($cod_pagina_outra);
        }
      }

  if (isset($cod_pagina_outra))
  {
    $max_outro=MaxCodPagina($sock,$cod_ferramenta,$cod_lingua,$tipo_usuario_outro);
    if ($max_outro > $cod_pagina)
    {
      $mudou='sim';
      $cod_antigo=$cod_pagina;               /* vari�veis usadas para o caso de n�o estar criando */
      $cod_pagina=$max_outro;
    }  
    AtualizaCodPagina($sock,$cod_ferramenta,$cod_pagina_outra,$cod_pagina,$tipo_usuario_outro);
  }

  if ($criar=='nao')
  {
    if ($mudou=='sim')
      AtualizaCodPagina($sock,$cod_ferramenta,$cod_antigo,$cod_pagina,$tipo_usuario);

    AtualizaNomePagina($sock,$cod_pagina,$nome_pagina,$cod_ferramenta,$cod_lingua,$tipo_usuario);
    AtualizaTexto($sock,$cod_ferramenta,$cod_pagina,$cod_lingua,$tipo_usuario,$texto);
  }
  else
  {
    $lista_lingua=ListaLinguas($sock);
    if (count($lista_lingua)>0)
      foreach($lista_lingua as $cod_lingua_outra => $lingua)
        if($cod_lingua_outra!=1)
          CriaCampoTexto($sock,$cod_ferramenta,$cod_pagina,'',$cod_lingua_outra,$tipo_usuario);

    CriaCampoTexto($sock,$cod_ferramenta,$cod_pagina,$nome_pagina,$cod_lingua,$tipo_usuario);
    AtualizaNomePagina($sock,$cod_pagina,$nome_pagina,$cod_ferramenta,$cod_lingua,$tipo_usuario);
    AtualizaTexto($sock,$cod_ferramenta,$cod_pagina,$cod_lingua,$tipo_usuario,$texto);
  }

  /* 521 - Texto da ajuda cadastrado com sucesso. */
  echo("".RetornaFraseDaLista($lista_frases,521)."\n");

  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar ($sock);
?>
