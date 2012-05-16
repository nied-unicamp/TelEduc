<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/reenvio.php

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
  ARQUIVO : administracao/reenvio.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include $bibliotecas."/geral.inc";
  include("../administracao/admin.inc");
  include("reenvio.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");


  /* Inicio do JavaScript */
  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar() {\n");
  echo("	 startList();\n");
  echo("      }\n");

  echo("      function Reenvia(){");
  echo("        document.frmDados.action = \"acoes.php\";\n");
  echo("        document.frmDados.target = \"_self\";\n");
  echo("        document.frmDados.submit();\n");
  echo("      }");

  echo("      function VerDados()\n");
  echo("      {\n");
  echo("        var verif = ValidaCheck();\n");
  echo("        if(verif == true){\n");
  echo("           window.open(\"informacoes_curso.php\",\"Dados\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("          document.frmDados.action = \"informacoes_curso.php\";\n");
  echo("          document.frmDados.target = \"Dados\";\n");
  echo("          document.frmDados.submit();\n");
  echo("          return true;\n");
  echo("        }\n");
  echo("        else{\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("      }\n");

echo("

function ValidaCheck()
{
     var novo_mail;
     var cont = false;
     var e;
     for (var i=0;i<document.frmDados.elements.length;i++)
     {
        e = document.frmDados.elements[i];
        if (e.checked==true)
        {
         cont=true;
        }
     }
     if (cont==false)
     {
         alert('Selecione um curso!');
     }
     return cont;
}

function ValidaCheckEmail()
{
     var novo_mail;
     var cont = false;
     var e;
     var Lista = new Array();
     for (var i=0;i<document.frmDados.elements.length;i++)
     {
        e = document.frmDados.elements[i];
        if (e.checked==true)
        {
         alert(document.getElementById('cod_curso_'+i).innerHTML);
         alert(document.getElementById('email_formador_'+i).innerHTML);
         Lista[i] = document.getElementById('email_formador_'+i).innerHTML;
         cont=true;
        }
     }

     xajax_ReenviaEmail(Lista);

     if (cont==false)
     {
         alert('Selecione um curso!');
     }
     return cont;
}");

  echo("      function Recarregar(ordem)\n");
  echo("      {\n");
  echo("         document.location='reenvio.php?&ordenar='+ordem;\n");
  echo("      }\n");

  echo("      function EnviarEmail()\n");
  echo("      {\n");
  echo("         window.open(\"envia_email.php?&codigo=\"+0,\"Dados\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        document.frmDados.action = \"envia_email.php\";\n");
  echo("        document.frmDados.submit();\n");
  echo("        return true;\n");
  echo("      }\n");

  // Marcar e Desmarcar checkbox
  echo("      function CheckTodos(){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("        var cod_itens=document.getElementsByName('cod_curso[]');\n");
  echo("        for(i = 0; i < cod_itens.length; i++){\n");
  echo("          e = cod_itens[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function VerificaCheck(){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var checkPai = document.getElementById('checkMenu');\n");
  echo("        var cod_itens=document.getElementsByName('cod_curso[]');\n");
  echo("        for(i = 0; i < cod_itens.length; i++){\n");
  echo("          e = cod_itens[i];\n");
  echo("          if(!e.checked)\n");
  echo("            break;\n");
  echo("        }\n");
  echo("        if(i == cod_itens.length)\n");
  echo("          checkPai.checked = true;\n");
  echo("        else\n");
  echo("          checkPai.checked = false;\n");   
  echo("      }\n\n");

  echo("      </script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases_pag_inicial=RetornaListaDeFrases($sock,-3);
  $lista = ListaCursosDisponiveis();
  $total = count($lista);
  $lista_frases = RetornaListaDeFrases($sock, -5);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 9 - Enviar e-mail para usu�rios */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,9)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='../administracao/index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr class=\"head01\">\n");
  echo("              <td>\n");
  /* 262- Ordenar por: */
  echo("                ".RetornaFraseDaLista($lista_frases, 262));

  if (!isset($ordenar))
    $ordenar = 'cod_curso';

  echo (" <SELECT NAME=\"select_ordenar\" class=\"input\" onChange=\"Recarregar(this.value);\" style=\"margin:5px 0px 5px 0;\">\n");
  /* 169 - Categoria */
  echo ("                 <OPTION VALUE=pasta ".(($ordenar == 'pasta') ? "SELECTED" : "")." >".RetornaFraseDaLista($lista_frases, 169)."\n");
  /* 264- C�digo do curso*/
  echo ("                 <OPTION VALUE=cod_curso ".(($ordenar == 'cod_curso') ? "SELECTED" : "")." >".RetornaFraseDaLista($lista_frases, 264)."\n");
  /* 272- E-mail*/
  echo ("                 <OPTION VALUE=e-mail ".(($ordenar == 'e-mail') ? "SELECTED" : "")." >".RetornaFraseDaLista($lista_frases, 272)."\n");
  /* 266 - Nome do coordenador*/
  echo ("                 <OPTION VALUE=nome ".(($ordenar == 'nome') ? "SELECTED" : "")." >".RetornaFraseDaLista($lista_frases, 266)."\n");
  /*265 - Nome do curso*/
  echo ("                 <OPTION VALUE=nome_curso ".(($ordenar == 'nome_curso') ? "SELECTED" : "")." >".RetornaFraseDaLista($lista_frases, 265)."\n");
  echo("                </SELECT>\n");
  /*fim menu de ordena��o*/
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  /*inicio do formulario que lista os cursos*/
  echo("                <form action=\"acoes.php\" name=\"frmDados\" target=\"_self\" method=\"post\">\n");
  echo("                  <input type=\"hidden\" name=\"acao\" value=\"email\" />\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td align=\"center\"><input type=\"checkbox\" id=\"checkMenu\" onClick=\"CheckTodos();\"/></td>\n");
  /*263- Categoria*/
  echo ("                   <td align=\"center\">".RetornaFraseDaLista($lista_frases, 263)."</td>\n");
  /*264- C�digo do curso*/
  echo("                    <td align=\"center\">".RetornaFraseDaLista($lista_frases, 264)."</td>\n");
  /*265- Nome do curso*/
  echo("                    <td align=\"center\">".RetornaFraseDaLista($lista_frases, 265)."</td>\n");
  /* 266 - Nome do coordenador*/
  echo("                    <td align=\"center\">".RetornaFraseDaLista($lista_frases, 266)."</td>\n");
  /*267- E-mail do coordenador*/
  echo("                    <td align=\"center\">".RetornaFraseDaLista($lista_frases, 267)."</td>\n");
  echo("                  </tr>\n");

  $lista_cursos = ListaDadosCursos($lista);
  //var_dump($lista);
 /*inicio da ordena��o dos cursos*/
 switch ($ordenar)
 {
   case 'nome':
    usort($lista_cursos,"CompNomeCoordenador");
     break;
   case 'e-mail':
     usort($lista_cursos,"CompEmail");
     break;
   case 'nome_curso':
      usort($lista_cursos,"CompNomeCurso");
     break;
   case 'cod_curso':
   usort($lista_cursos,"CompCod_Curso");
     break;
   case 'pasta':
   usort($lista_cursos,"CompPasta");
     break;
 }
 /*fim da ordena��o dos cursos*/

  //var_dump($lista_frases);
  //var_dump($lista_cursos);
 /*in�cio da lista dos cursos*/
  for ($j = 0; $j < $total; $j++)
  {
    //115 - Cursos Gerais (pag_inicial)
    $categoria = ($lista_cursos[$j]['pasta'] == "") ? RetornaFraseDaLista($lista_frases_pag_inicial,115) : $lista_cursos[$j]['pasta'];
    //286 - [nenhum email cadastrado]
    $email = ($lista_cursos[$j]['email'] == "") ? RetornaFraseDaLista($lista_frases, 286) : $lista_cursos[$j]['email'];
    //nenhum nome para o coordenador 
    $nome = ($lista_cursos[$j]['nome'] == "") ? "-" : $lista_cursos[$j]['nome'];

    // Unificando o numero dos checkboxes com o das linhas da tabela:
    $k = $j + 1;

    echo("                  <tr>\n");
    echo("                    <td><input type=\"checkbox\" name=\"cod_curso[]\" onclick=\"VerificaCheck();\" value=\"".$lista_cursos[$j]['cod_curso']."\"/></td>\n");
    echo("                    <td align=\"center\">".$categoria."</td>\n");
    echo("                    <td align=\"center\">".$lista_cursos[$j]['cod_curso']."&nbsp;</td>\n");
    echo("                    <td align=\"center\">".$lista_cursos[$j]['nome_curso']."&nbsp;</td>\n");
    echo("                    <td align=\"center\">".$nome."</td>\n");
    echo("                    <td align=\"center\">".$email."</td>\n");
    echo("                  </tr>\n");
  }

  echo("                </table>\n");
  echo("                </form>\n");
  echo("              </td>\n");
  echo("            </tr>\n");  
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"menuUp02\">\n");
  /* 268 - Enviar e-mail */
  /* 270 - Ver dados do curso */
  echo("                  <li>\n");
  echo("                    <span style=\"href: #\" title=\"Enviar e-mail\" onClick=\"Reenvia();\">".RetornaFraseDaLista($lista_frases, 268)."</span>\n");
  echo("                    <span style=\"href: #\" title=\"Ver dados do curso\" onClick=\"VerDados();\">".RetornaFraseDaLista($lista_frases, 270)."</span>\n");
  echo("                  </li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>