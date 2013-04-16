<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/informacoes_curso.php

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
  ARQUIVO : administracao/informacoes_curso.php
  ========================================================== */


  $bibliotecas="../cursos/aplic/bibliotecas/";
  include $bibliotecas."/geral.inc";
  include "../administracao/admin.inc";
  include "reenvio.inc";

  VerificaAutenticacaoAdministracao();

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->registerFunction("AlterarEmailDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();


  include "../topo_tela_inicial.php";

  $lista_frases=RetornaListaDeFrases($sock,-5);
  $lista_frases_pag_incial=RetornaListaDeFrases($sock,-3);

  /* Inicio do JavaScript */
  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar() {\n");
  echo("	       startList();\n");
  echo("      }\n");

  echo("      function EnviarEmail()\n");
  echo("      {\n");
//   echo("	       window.open(\"envia_email.php?&codigo=\"+0,\"Dados\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("	document.frmDados.action = \"envia_email.php\";\n");
  echo("	document.frmDados.submit();\n");
  echo("	return true;\n");
  echo("      }\n");

  echo("      function MudarEmail(cod_curso)\n");
  echo("      {\n");
  echo("        var verif = ValidaCheck();\n");
  echo("	if(verif == true){\n");
//   echo("	         window.open(\"altera_email.php\",\"Dados\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("          document.frmDados.action = \"altera_email.php\";\n");
  echo("	  document.frmDados.submit();\n");
  echo("	  return true;\n");
  echo("	}\n");
  echo("        else{\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("      }\n");


echo ("function ValidaEmailCheck(total)
{
var regras1 = /(@.*@)|(\.{2,})|(@\.)|(\.@)|(^\.)|(\.$)/;
     var regras2 = /^[a-zA-Z0-9_.-]+\@[a-zA-Z0-9.-]+\.([a-zA-Z]{2,3}|[0-9]{1,3})$/;
     var novo_mail;
     var cont=false;
      var e;
      var d;
      var f;
      var j = 0;
      d = new Array();
      d = new Array(total);
      f = new Array();
      f = new Array(total);
      for ( var i=0;i< document.frmDados.elements.length;i++)
      {
        e = document.frmDados.elements[i];
        if (e.name=='emailcheck[]')
        {
         d[j]=e.value;
         j++;
        }
        if (e.checked==true)
        {
         f[j]=true;
        }
      }
      for (var j = 0; j < total; j++)
      {
       if((f[j]==true))
         {
            if ((regras1.test(d[j]) || !regras2.test(d[j])))
             {
                 alert('email '+d[j]+' inv�lido!');
                 return false
             }
             else
              {
                cont=true;
              }
        }
     }
     if (cont==true)
     {
     EnviarEmail();
       return true;
     }
     else
     {
     alert('Selecione um curso!');
     return false;
     }
}

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
}");

  echo("      function EmailAlterado(res)\n");
  echo("      {\n");
  echo("        if(res == true)\n");  
  echo("          alert('');\n");
  echo("        else\n");
  /*279- Erro: N�o foi poss�vel atualizar o e-mail.*/
  echo("          alert('".RetornaFraseDaLista($lista_frases,279)."');\n");
  echo("      }\n");

  echo("    </script>\n");

  $objAjax->printJavascript("../xajax_0.2.4/");

  /* Fim do JavaScript */

  echo("</head>\n");

  $cod_checked = explode('_',$cod_check); //ver se h� check box selecionadas
  $cont=0;  //contador de caixas selecionadas
  $total=count($cod_curso);

  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
  echo("    <a name=\"topo\"></a>\n");
  echo("    <br /><br />\n");
  /*91 - Dados do curso*/
  echo("          <h4>".RetornaFraseDaLista($lista_frases,91)."</h4>\n");
  echo("    <br />\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <form action='' name=\"frmDados\" target=\"Dados\" method=\"post\">\n");
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* G 13 - Fechar */
  echo("                  <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");

  for( $i=0; $i<$total; $i++)
  {
    $v = BuscaInfoCursos($cod_curso[$i]);

     //115 - Cursos Gerais (pag_inicial)
    $categoria = ($v['pasta'] == "") ? RetornaFraseDaLista($lista_frases_pag_incial, 115) : $v['pasta'];
    //286 - [nenhum email cadastrado]
    $email = ($v['email'] == "") ? RetornaFraseDaLista($lista_frases, 286) : $v['email'];
    //nenhum nome para o coordenador 
    $nome = ($v['nome'] == "") ? "-" : $v['nome'];

    echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("                  <tr class=\"head\">\n");
    /*92 - Nome do curso*/
    echo("                    <td colspan=\"2\" align=\"right\" width=\"40%\">".RetornaFraseDaLista($lista_frases, 92)."</td><td align=left>".$v['nome_curso']."</td>\n");
    echo("                  </tr>\n");
    /*281- C�digo do curso: */
    echo("                  <tr>\n");
    echo("                    <td width=\"2%\" rowspan=\"7\">\n");

    if($v['cod_curso']== $cod_checked[$cont])
    {
      echo("                      <input type=\"checkbox\" name=\"cod_curso[]\" CHECKED value=\"".$v['cod_curso']."\"/></td>\n");
      $cont++;
    }
    else
    {
      echo("                      <input type=\"checkbox\" name=\"cod_curso[]\" value=\"".$v['cod_curso']."\"/></td>\n");
    }

    echo("                    <td align=\"right\">".RetornaFraseDaLista($lista_frases, 281)."</td><td align=\"left\">".$v['cod_curso']."</td>\n");
    echo("                  </tr>\n");
    /* 94 - Categoria: */
    echo("                  <tr>\n");
    echo("                    <td align=\"right\">".RetornaFraseDaLista($lista_frases, 94)."</td>\n");
    echo("                    <td align=\"left\">".$categoria."</td>\n");
    echo("                  </tr>\n");
    /* 287 - Nome do coordenador:*/
    echo("                  <tr>\n");
    echo("                    <td align=\"right\">".RetornaFraseDaLista($lista_frases, 287)."</td>\n");
    echo("                    <td align=\"left\">".$nome."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr style=\"display:none;\">\n");
    echo("                    <td colspan=\"2\">\n");
    /* 33 - E-mail: */
    echo("                      <input type=\"hidden\" NAME=\"emailcheck[]\" VALUE=\"".$email."\"/>\n");
    echo("                    </td>");
    echo("                  </tr>");
    echo("                  <tr>\n");
    echo("                    <td align=\"right\">".RetornaFraseDaLista($lista_frases, 33)."</td>\n");
    echo("                    <td align=\"left\">\n");
    echo("                      <input type=\"text\" class=\"input\" name=\"email_".$i."\" VALUE=\"".$email."\" />\n");
    /*269- Mudar e-mail*/
    echo("                      &nbsp;&nbsp;&nbsp;&nbsp;<input type=\"button\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,269)."\" onclick=\"xajax_AlterarEmailDinamic(".$v['cod_curso'].",document.frmDados.email_".$i.".value);\" />\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");

    if($v['publico_alvo']!=NULL){
    /* 218 - P�blico alvo: */
      echo("                  <tr>\n");
      echo("                    <td align=\"right\">".RetornaFraseDaLista($lista_frases, 218)."</td>\n");
      echo("                    <td align=left>".$v['publico_alvo']."</td>\n");
      echo("                  </tr>\n");
    }
    if($v['informacoes']!= NULL){
    /*290- Informa��es: */
      echo("                  <tr>\n");
      echo("                    <td align=\"right\">".RetornaFraseDaLista($lista_frases, 290)."</td>\n");
      echo("                    <td align=left>".$v['informacoes']."</td>\n");
      echo("                  </tr>\n");
    }

    echo("                </table>\n");
  }

  $cod2 = implode('_',$cod_curso);  //para quando voltar de altera_email ou de troca_email ter os cod_cursos para listar
  echo("                <input type=\"hidden\" NAME=\"cod_check\" VALUE=\"".$cod2."\"/>");
  echo("              </td>\n");
  echo("            </tr>\n");  
  echo("          </table>\n");
  echo("          </form>\n");
  echo("          <ul class=\"menuUp02\">\n");
  /*268- Enviar e-mail*/
  echo("            <li><span style=\"href: #\" title=\"Enviar e-mail\" onClick=\"return(ValidaEmailCheck(".$total."));\">".RetornaFraseDaLista($lista_frases, 268)."</span></li>\n");
  echo("          </ul>\n");
  echo("  </body>\n");
  echo("</html>\n");
?>