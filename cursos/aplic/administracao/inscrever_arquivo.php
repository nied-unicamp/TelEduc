<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/inscrever.php

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

    Nied - NÚcleo de InformÁtica Aplicada à Educação
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
  ARQUIVO : cursos/aplic/administracao/inscrever.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  switch($tipo_usuario)
  {
    case 'A':
      //Aluno
      $cod_pagina_ajuda = 7;
      break;
    case 'F':
      //Formador
      $cod_pagina_ajuda = 6;
      break;
    case 'Z':
      //Colaborador
      $cod_pagina_ajuda = 14;
      break;
    case 'V':
      //visitante
      //$cod_pagina_ajuda = ;
      break;
  }

  include("../topo_tela.php");

  /*Funcao JavaScript*/
  echo("    <script type=\"text/javascript\">\n\n");
  echo("      var numLogins = 5;\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function verificar()\n");
  echo("      {\n");
  echo("        var nome,email,login;\n");
  echo("        var mail_admin;\n");
  echo("        var regras1 = /(@.*@)|(\.{2,})|(@\.)|(\.@)|(^\.)|(\.$)/;\n");
  echo("        var regras2 = /^[a-zA-Z0-9\_\.\-]+\@[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})$/;\n");
  echo("        nome = 0;\n");
  echo("        email = 0;\n");
  echo("        login = 0;\n");
  echo("          for (var i=0;i<document.formul.elements.length;i++)\n");
  echo("          {\n");
  echo("            e = document.formul.elements[i];\n");
  echo("            if (e.value!='' && e.name=='email[]')\n");
  echo("            {\n");
  echo("              mail_admin=e.value;\n");
  echo("              if (regras1.test(mail_admin) || !regras2.test(mail_admin))\n");
  echo("              {\n");
  echo("                alert('".RetornaFraseDaLista($lista_frases, 157)."');\n");
  echo("                return(false);\n");
  echo("              }\n");
  echo("            }\n");
  echo("            if ((e.value!='') && (e.name=='nome[]'))\n");
  echo("            {\n");
  echo("                nome++;\n");
  echo("            }\n");
  echo("            if ((e.value!='') && (e.name=='email[]'))\n");
  echo("            {\n");
  echo("                email++;\n");
  echo("            }\n");
  echo("            if ((e.value!='') && (e.name=='login[]'))\n");
  echo("            {\n");
  echo("                login++;\n");
  echo("            }\n");
  echo("          }\n");
  echo("          if (((nome>0) && (email>0) && (login>0)) && ((nome==email) && (email==login)))  {\n");
  echo("            if (verifica_logins())\n");
  echo("            {\n");
  echo("              document.formul.method='post';\n");
  echo("              document.formul.action='acoes.php';\n");
  echo("              return true;\n");
  //echo("              document.formul.submit();\n");
  echo("            }\n");
  echo("            else {\n");
  // 225 - Existem logins repetidos. Especifique logins diferentes para cada usuário.
  echo("              alert('".RetornaFraseDaLista($lista_frases, 225)."');\n");
  echo("              return false;\n");
  echo("            }\n");
  echo("          }\n");
  echo("          else  {\n");
  echo("           alert('".RetornaFraseDaLista($lista_frases, 60)."');\n");
  echo("           return false;\n");
  echo("          }\n");
  echo("      }\n");

  echo("      function verifica_logins()\n");
  echo("      {\n");
  echo("        var todos_logins = new Array(numLogins);\n");
  echo("        var cont = 0;\n");
  echo("        for (var i=0; i<document.formul.elements.length; i++)\n");
  echo("        {\n");
  echo("          e = document.formul.elements[i];\n");
  echo("          if (e.value!='' && e.name=='login[]')\n");
  echo("          {\n");
  echo("            todos_logins[cont] = e.value;\n");
  echo("            cont++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        for (var j=0; j<cont; j++)\n");
  echo("        {\n");
  echo("          for (var k=0; k<cont; k++)\n");
  echo("          {\n");
  echo("            if (j != k)\n");
  echo("            {\n");
  echo("              if (todos_logins[j] == todos_logins[k])\n");
  echo("              {\n");
  echo("                return false;\n");
  echo("              }\n");
  echo("            }\n");
  echo("          }\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n");

  echo("      function addLogin(){\n");
  echo("        numLogins++;\n");
  echo("        elementoTable=document.getElementById('tabInterna');\n");
  echo("        elementoTrOldAddLogin=document.getElementById('addLogin');\n");
  echo("        elementoTr=document.createElement('tr');\n");
  echo("        elementoTdNum=document.createElement('td');\n");
  echo("        elementoBold=document.createElement('b');\n");
  echo("        elementoBold.innerHTML=numLogins+'.';\n");
  echo("        elementoTdNome=document.createElement('td');\n");
  echo("        elementoTdEmail=document.createElement('td');\n");
  echo("        elementoTdLogin=document.createElement('td');\n");
  echo("        inputNome=document.createElement('input');\n");
  echo("        inputNome.setAttribute(\"type\", \"text\");\n");
  echo("        inputNome.setAttribute(\"size\", \"20\");\n");
  echo("        inputNome.setAttribute(\"name\", \"nome[]\");\n");
  echo("        inputNome.setAttribute(\"class\", \"input\");\n");
  echo("        inputNome.setAttribute(\"maxlength\", \"127\");\n");
  echo("        inputEmail=document.createElement('input');\n");
  echo("        inputEmail.setAttribute(\"type\", \"text\");\n");
  echo("        inputEmail.setAttribute(\"size\", \"30\");\n");
  echo("        inputEmail.setAttribute(\"name\", \"email[]\");\n");
  echo("        inputEmail.setAttribute(\"class\", \"input\");\n");
  echo("        inputEmail.setAttribute(\"maxlength\", \"127\");\n");
  echo("        inputLogin=document.createElement('input');\n");
  echo("        inputLogin.setAttribute(\"type\", \"text\");\n");
  echo("        inputLogin.setAttribute(\"size\", \"10\");\n");
  echo("        inputLogin.setAttribute(\"name\", \"login[]\");\n");
  echo("        inputLogin.setAttribute(\"class\", \"input\");\n");
  echo("        inputLogin.setAttribute(\"maxlength\", \"20\");\n");
  echo("        elementoTdNum.appendChild(elementoBold);\n");
  echo("        elementoTdNome.appendChild(inputNome);\n");
  echo("        elementoTdEmail.appendChild(inputEmail);\n");
  echo("        elementoTdLogin.appendChild(inputLogin);\n");
  echo("        elementoTr.appendChild(elementoTdNum);\n");
  echo("        elementoTr.appendChild(elementoTdNome);\n");
  echo("        elementoTr.appendChild(elementoTdEmail);\n");
  echo("        elementoTr.appendChild(elementoTdLogin);\n");
  echo("        elementoTrOldAddLogin.parentNode.insertBefore(elementoTr, elementoTrOldAddLogin);\n");
  echo("      }\n\n");

  echo("      function verificaArquivo(){\n");
  echo("        nomeArq = document.getElementById('arquivoInsc').value;\n");
  echo("        if(nomeArq == ''){\n");
  echo("          mostraFeedback('Você nao selecionou nenhum arquivo', false);\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("      }\n");
  
  echo("    </script>\n\n");

  include("../menu_principal.php");
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
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

    echo("        </td>\n");
    echo("      </tr>\n");

    include("../tela2.php");

    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }
  
  echo("      <form name=\"inscreveArq\" action=\"faz_inscricao.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."\" onsubmit=\"return(verificaArquivo())\" method=\"post\" enctype=\"multipart/form-data\">\n");
  echo("        <input type=\"hidden\" name=\"cod_curso\"      value=".$cod_curso.">\n");
  echo("        <input type=\"hidden\" name=\"cod_ferramenta\" value=".$cod_ferramenta.">\n");
  echo("        <input type=\"hidden\" name=\"tipo_usuario\"   value=".$tipo_usuario.">\n");
  echo("        <input type=\"hidden\" name=\"action\"         value='inscrever'>\n");

  // Página Principal
  /* 1 - Administração */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1)."\n");

  if ($tipo_usuario == 'F')
  {
    /* 50 - Inscrever Formadores */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 50)."</h4>";
    $cod_pagina = 6;
  }
  else if ($tipo_usuario == 'Z')
  {
    // 164 - Inscrever Colaboradores
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 164)."</h4>";
    $cod_pagina = 14;
  }
  else if ($tipo_usuario == 'V')
  {
    // 182 - Inscrever Visitantes
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 182)."</h4>";
  }
  else if ($tipo_usuario == 'A')
  {
    /* 51 - Inscrever Alunos */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 51)."</h4>";
    $cod_pagina = 7;
  }
  else
  {
    echo("Arquivo inscrever.php, tipo_usuario inv&aacute;lido, tipo_usuario = [");
    var_dump($tipo_usuario);
    echo("]<br>\n");
    Desconectar($sock);
    die();
  }

  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (geral)*/
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;confirma=0\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table  cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head alLeft\">\n");
  /* 84 - Escolha um arquivo CSV com registros dos usuários a serem inscritos no formato "nome, email, login" : */
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases_geral,84)."</td>\n");
  echo("                  <tr>\n");
  echo("                  <tr class=\"alLeft\">\n");
  echo("                    <td>\n");
  echo("                      <input type=\"file\" name=\"arquivoInsc\" id=\"arquivoInsc\" />\n");
  echo("                    </td>\n");

  echo("                  <tr>\n");
  echo("                </table>\n");
  
  /* 59 - Inscrever */
  echo("                <div align=\"right\"><br><input type=\"submit\" class=\"input\" value='".RetornaFraseDaLista($lista_frases,59)."'></div>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
