<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/index.php

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
  ARQUIVO : administracao/index.php
  ========================================================== */
	
  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");
  
  include("../topo_tela_inicial.php");
  
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro 
  $feedbackObject->addAction("logar", 198, 0);
  
  /* Inicio do JavaScript */
  echo("    <script language=\"javascript\"  type=\"text/javascript\">\n");

  echo("      function Iniciar() {\n");
                $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n");

  echo("    </script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  
  VerificaAutenticacaoAdministracao();
  
  /* 1 - Administração */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("<!-- Tabelao -->\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("<tr>\n");

  echo("<td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  /* Realizando checagem de novo Patch */

  $lista=RetornaArrayDiretorio("patch");

  if (count($lista)>0)
  {
    unset($patchs);
    // Existem Patchs no Diretorio
    foreach($lista as $cod =>$linha)
    {
      $query="select * from Patchs where patch='".$linha['Arquivo']."'";
      $res=Enviar($sock,$query);
      if (RetornaNumLinhas($res)==0)
        $patchs[$cod]=$linha['Arquivo'];

    }
   

    if (count($patchs)>0)
    {
      foreach($patchs as $cod => $nome)
      {
        echo("<b>".$nome."</b><br /><br />");

        include("patch/".$nome);

        $query="insert into Patchs (patch) values ('".$nome."')";
        Enviar($sock,$query);
      }

      /* 135 - Patch atualizado com sucesso! */
      echo("<b>".RetornaFraseDaLista($lista_frases,135)."</b><br><br>");

      // 18 - OK
      echo("<form><input type=\"button\" value='".RetornaFraseDaLista($lista_frases_geral,18)."' onclick='document.location=\"index.php?\";'></form>");


      echo("</body>\n");
      echo("</html>\n");
      exit();
    }
  }

  /* Fim da Checagem de novo Patch */


  /* X - Dados de Curso */                        /* Y - Categorias */
  echo("<tr class=\"head\">\n");
  echo("<td>Dados do Curso</td>\n");
  echo("<td>Categorias</td>\n");
  echo("</tr>\n");
  echo("<tr><td>\n");
  echo("<ul>\n");

  /* 3 - Cria��o de Curso */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,3),"\"criar_curso.php\"","");

  /* 4 - Extra��o de Curso */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,4),"\"../extracao/extrair_curso.php\"","");

  /* 141 - Inser��o de Cursos Extra�dos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,141),"\"inserir_curso.php\"","");

  /* 245 - Reutiliza��o de Cursos Encerrados */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,245),"\"resetar_curso.php\"","");

  echo("</ul>\n");
  echo("</td>\n");

  echo("<td>\n");
  echo("<ul>\n");

  /* 125 - Editar Categorias */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,125),"\"editar_categoria.php\"","");

  /* 131 - Selecionar Categoria dos Cursos */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,131),"\"selecionar_categoria.php\"","");

  echo("</ul>\n");
  echo("</td></tr>\n");

  echo("<tr class=\"head\"><td>Reenvio</td><td>Configurar</td></tr>\n");

  echo("<tr><td>\n");
  echo("<ul>\n");

  /* 293 - Reenvio de dados aos coordenadores*/
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,293),"\"../infocurso/reenvio.php\"","");

  /* 8 - Trocar login */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,8),"\"trocar_login.php\"","");

  /* 9 - Enviar e-mail para usu�rios */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,9),"\"enviar_email.php\"","");

  /* 5 - Consulta a Base de Dados */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,5),"\"consultar_base.php\"","");

  /* 13 - Contato - NIED - Unicamp */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,13),"\"mailto:curso@nied.unicamp.br\"","");

  echo("</ul>\n");
  echo("</td>\n");

  echo("<td>\n");
  echo("<ul>\n");

  /* 153 - Estat�sticas do Ambiente */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,153),"\"../estatistica/num_cursos.php\"","");

  /* 183 - Configurar dados do ambiente */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,183), "\"selecionar_lingua.php\"", "");

  /* 11 - Cadastro de L�nguas */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,11),"\"cadastro_linguas.php\"","");

  /* 171 - Cadastro de texto da Ajuda */
  PreparaBoldLink(RetornaFraseDaLista($lista_frases,171),"\"../ajuda/index.php\"","");

  echo("</ul>\n");
  echo("</td></tr></table>\n");

  /* 12 - Voltar a p�gina inicial */
  echo("<div align=\"right\">\n");
  echo("  <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,12)."\" onClick=\"document.location='../pagina_inicial/index.php?'\" type=\"button\"/>\n");
  echo("</div>\n");


  echo("</td></tr></table>\n");
  echo("</td></tr>\n");
  include("../rodape_tela_inicial.php");
  echo("</body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
