<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/inscrever3.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/administracao/inscrever3.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");
  
  require_once("../xajax_0.5/xajax_core/xajax.inc.php");
  
  //Instancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->register(XAJAX_FUNCTION,"ListaUsuariosPaginaDinamic");
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  switch($tipo_usuario){
    case 'F':
      //formador
      $cod_pagina_ajuda = 6;
      break;
    case 'A':
      //aluno
      $cod_pagina_ajuda = 7;
      break;
    case 'Z':
      //colaborador
      $cod_pagina_ajuda = 14;
      break;
    case 'V':
      //visitante
      //$cod_pagina_ajuda = ;
      break;
  }

  include("../topo_tela.php");

  $itens_por_pagina = 10;
  //$lista_usuarios = RetornaListaUsuariosGlobal($sock,$cod_curso,$busca,0,10);
  $total_usuarios = RetornaContagemUsuariosGlobal($sock,$cod_curso,$busca);

  /*Funcao JavaScript*/
  echo("    <script type=\"text/javascript\">\n\n");

  echo("      var numLogins = 5;\n");
  echo("      var total_usuarios = ".$total_usuarios.";\n");
  echo("      var itens_por_pagina = ".$itens_por_pagina.";\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        ExibePagina(1);\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function ExibePagina(pagina)\n");
  echo("      {\n");
  echo("        index_ini = (pagina - 1) * itens_por_pagina + 1;\n");
  echo("        index_fim = pagina * itens_por_pagina;\n");
  echo("        if (index_fim > total_usuarios)\n");
  echo("          index_fim = total_usuarios;\n\n");

  echo("        document.getElementById('prim_usr_index').innerHTML = index_ini;\n");
  echo("        document.getElementById('ult_usr_index').innerHTML = index_fim;\n\n");

  echo("        xajax_ListaUsuariosPaginaDinamic(pagina,".$itens_por_pagina.",".$cod_curso.",'".$busca."');\n");
  echo("      }\n");
  
  echo("      function ExibeControles(pagina)\n");
  echo("      {\n");
  echo("        pagina = parseInt(pagina);\n\n");

  echo("        var pagina_aux;\n");
  echo("        var fim = pagina * itens_por_pagina;\n");
  echo("        var ultima_pag = Math.ceil(total_usuarios / itens_por_pagina);\n\n");

  echo("        if (pagina == 1) {\n");
  echo("          document.getElementById('paginacao_first').onclick = '';\n");
  echo("          document.getElementById('paginacao_first').className = '';\n");
  echo("          document.getElementById('paginacao_back').onclick = '';\n");
  echo("          document.getElementById('paginacao_back').className = '';\n");
  echo("        }\n");
  echo("        else {\n");
  echo("          document.getElementById('paginacao_first').onclick = function(){ ExibePagina(1); };\n");
  echo("          document.getElementById('paginacao_first').className = 'link';\n");
  echo("          document.getElementById('paginacao_back').onclick = function(){ ExibePagina(pagina - 1); };\n");
  echo("          document.getElementById('paginacao_back').className = 'link';\n");
  echo("        }\n\n");

  echo("        if (fim >= total_usuarios) {\n");
  echo("          document.getElementById('paginacao_last').onclick = '';\n");
  echo("          document.getElementById('paginacao_last').className = '';\n");
  echo("          document.getElementById('paginacao_fwd').onclick = '';\n");
  echo("          document.getElementById('paginacao_fwd').className = '';\n");
  echo("        }\n");
  echo("        else {\n");
  echo("          document.getElementById('paginacao_last').onclick = function(){ ExibePagina(ultima_pag); };\n");
  echo("          document.getElementById('paginacao_last').className = 'link';\n");
  echo("          document.getElementById('paginacao_fwd').onclick = function(){ ExibePagina(pagina + 1); };\n");
  echo("          document.getElementById('paginacao_fwd').className = 'link';\n");
  echo("        }\n\n");

  echo("        pagina_aux = pagina - 2;\n");
  echo("        if (pagina_aux < 1) {\n");
  echo("          document.getElementById('paginacao_1').innerHTML = '';\n");
  echo("          document.getElementById('paginacao_1').onclick = '';\n");
  echo("          document.getElementById('paginacao_1').className = '';\n");
  echo("        }\n");
  echo("        else {\n");
  echo("          document.getElementById('paginacao_1').innerHTML = pagina_aux + ' ';\n");
  echo("          document.getElementById('paginacao_1').onclick = function(){ ExibePagina(pagina - 2); };\n");
  echo("          document.getElementById('paginacao_1').className = 'link';\n");
  echo("        }\n\n");

  echo("        pagina_aux = pagina - 1;\n");
  echo("        if (pagina_aux < 1) {\n");
  echo("          document.getElementById('paginacao_2').innerHTML = '';\n");
  echo("          document.getElementById('paginacao_2').onclick = '';\n");
  echo("          document.getElementById('paginacao_2').className = '';\n");
  echo("        }\n");
  echo("        else {\n");
  echo("          document.getElementById('paginacao_2').innerHTML = pagina_aux + ' ';\n");
  echo("          document.getElementById('paginacao_2').onclick = function(){ ExibePagina(pagina - 1); };\n");
  echo("          document.getElementById('paginacao_2').className = 'link';\n");
  echo("        }\n\n");

  echo("        document.getElementById('paginacao_3').innerHTML = '<b>[' + pagina + '] </b>';\n\n");

  echo("        pagina_aux = pagina + 1;\n");
  echo("        if (pagina_aux > ultima_pag) {\n");
  echo("          document.getElementById('paginacao_4').innerHTML = '';\n");
  echo("          document.getElementById('paginacao_4').onclick = '';\n");
  echo("          document.getElementById('paginacao_4').className = '';\n");
  echo("        }\n");
  echo("        else {\n");
  echo("          document.getElementById('paginacao_4').innerHTML = pagina_aux + ' ';\n");
  echo("          document.getElementById('paginacao_4').onclick = function(){ ExibePagina(pagina + 1); };\n");
  echo("          document.getElementById('paginacao_4').className = 'link';\n");
  echo("        }\n\n");

  echo("        pagina_aux = pagina + 2;\n");
  echo("        if (pagina_aux > ultima_pag) {\n");
  echo("          document.getElementById('paginacao_5').innerHTML = '';\n");
  echo("          document.getElementById('paginacao_5').onclick = '';\n");
  echo("          document.getElementById('paginacao_5').className = '';\n");
  echo("        }\n");
  echo("        else {\n");
  echo("          document.getElementById('paginacao_5').innerHTML = pagina_aux + ' ';\n");
  echo("          document.getElementById('paginacao_5').onclick = function(){ ExibePagina(pagina + 2); };\n");
  echo("          document.getElementById('paginacao_5').className = 'link';\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function IncluiUsuarioTabela(cod, nome, login, email, flag, i)\n");
  echo("      {\n");
  echo("        tbody = document.getElementById('tabInterna').children[0];\n");
  echo("        tr = document.createElement('tr');\n");
  echo("        td_check = document.createElement('td');\n");
  echo("        td_nome = document.createElement('td');\n");
  echo("        td_login = document.createElement('td');\n");
  echo("        td_email = document.createElement('td');\n");
  echo("        if(flag == 'L'){\n");
  echo("          td_check.innerHTML = \"<input type='checkbox' value='\"+cod+\"' onclick='VerificaCheck();' name='cod_usu_global[]' class='input'/>\"\n");
  echo("          //Se o nome estiver vazio, colocar '&nbsp;' para que nao ocorra quebra na borda da tabela.\n");
  echo("          td_nome.innerHTML = (nome=='')?'&nbsp;':nome\n");
  echo("          //td_nome.innerHTML = nome\n");
  echo("        }\n\n");

  echo("        //Se o email estiver vazio, colocar '&nbsp;' para que nao ocorra quebra na borda da tabela.\n");
  echo("        td_email.innerHTML = (email=='')?'&nbsp;':email\n");
  echo("        td_login.innerHTML = login\n");
  echo("        if(flag == 'B'){\n");
  echo("          td_check.innerHTML = '&nbsp;'\n");
  //TODO:Mensagem hard-coded. Colocar no BD.
  echo("          td_nome.innerHTML = nome+ '  <span class=\"aviso\">(UsuÃ¡rio jÃ¡ cadastrado no curso)</span>';\n");
  echo("        }\n");
  echo("        tr.appendChild(td_check)\n");
  echo("        tr.appendChild(td_nome)\n");
  echo("        tr.appendChild(td_email)\n");
  echo("        tr.appendChild(td_login)\n");
  echo("        tr.setAttribute('id','user_'+i);	/* seta o id da linha para saber qual linha apagar. */\n\n");

  echo("        tbody.appendChild(tr)\n");
  echo("      }\n\n");

  echo("      function IncluiControlePaginacao()\n");
  echo("      {\n");
  echo("        tbody = document.getElementById('tabInterna').children[0];\n\n");

  echo("        tr = document.createElement('tr');\n");
  echo("        td = document.createElement('td');\n");
  echo("        span1 = document.createElement('span');\n");
  echo("        span2 = document.createElement('span');\n");
  echo("        span31 = document.createElement('span');\n");
  echo("        span32 = document.createElement('span');\n");
  echo("        span33 = document.createElement('span');\n");
  echo("        span34 = document.createElement('span');\n");
  echo("        span35 = document.createElement('span');\n");
  echo("        span4 = document.createElement('span');\n");
  echo("        span5 = document.createElement('span');\n\n");

  echo("        tr.setAttribute('id','controle_pag');\n");
  echo("        td.setAttribute('align','right');\n");
  echo("        td.setAttribute('colSpan','5');\n");
  echo("        span1.setAttribute('id','paginacao_first');\n");
  echo("        span1.innerHTML = '<<';\n");
  echo("        span2.setAttribute('id','paginacao_back');\n");
  echo("        span2.innerHTML = '<';\n");
  echo("        span31.setAttribute('id','paginacao_1');\n");
  echo("        span32.setAttribute('id','paginacao_2');\n");
  echo("        span33.setAttribute('id','paginacao_3');\n");
  echo("        span34.setAttribute('id','paginacao_4');\n");
  echo("        span35.setAttribute('id','paginacao_5');\n");
  echo("        span4.setAttribute('id','paginacao_fwd');\n");
  echo("        span4.innerHTML = '>';\n");
  echo("        span5.setAttribute('id','paginacao_last');\n");
  echo("        span5.innerHTML = '>>';\n\n");

  echo("        td.appendChild(span1);\n");
  echo("        td.appendChild(span2);\n");
  echo("        td.appendChild(span31);\n");
  echo("        td.appendChild(span32);\n");
  echo("        td.appendChild(span33);\n");
  echo("        td.appendChild(span34);\n");
  echo("        td.appendChild(span35);\n");
  echo("        td.appendChild(span4);\n");
  echo("        td.appendChild(span5);\n");
  echo("        tr.appendChild(td);\n");
  echo("        tbody.appendChild(tr);\n");
  echo("      }\n\n");
  
  echo("      function RemovePaginaAntiga()\n");
  echo("      {\n\n");

  echo("        tbody = document.getElementById('tabInterna').children[0];\n");
  echo("        //users = document.getElementsByName('tr', 'user');\n");
  echo("        //var tam = users.length;\n\n");

  echo("        /* Apaga os usuarios da tabela. */\n");
  echo("        for (i = 0; document.getElementById('user_'+i) != null; i++) {\n");
  echo("          //tbody.removeChild(users[0]);\n");
  echo("          tbody.removeChild(document.getElementById('user_'+i));\n");
  echo("        }\n\n");

  echo("        cp = document.getElementById('controle_pag');\n");
  echo("        tbody.removeChild(cp);\n");
  echo("      }\n\n");

  echo("      function verifica_submit(e)\n");
  echo("      {\n");
  echo("        var keynum;\n\n");
  echo("        if(window.event) // IE\n");
  echo("          keynum = e.keyCode;\n");
  echo("        else if(e.which) // Netscape/Firefox/Opera\n");
  echo("          keynum = e.which;\n\n");

  echo("        if(keynum == 13)\n");
  echo("        {\n");
  echo("          buscar();\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n");

  echo("      function buscar()\n");
  echo("      {\n");
  echo("        document.formul.method='post';\n");
  echo("        document.formul.action='".$_SESSION['PHP_SELF']."';\n");
  echo("        document.formul.submit();\n");
  echo("        return true;\n");
  echo("      }\n");

  echo("      function verificar(array_itens)\n");
  echo("      {\n");
  echo("        document.formul.method='post';\n");
  echo("        document.formul.action='acoes.php';\n");
  echo("        document.getElementById('codigos_usu_global').value=array_itens;\n");
  echo("        document.formul.submit();\n");
  echo("      }\n\n");

  echo("      function VerificaCheck(){\n");
  echo("        var i;\n");
  echo("        var j=0;\n");
  echo("        var cod_itens = document.getElementsByName('cod_usu_global[]');\n");
  echo("        array_itens = new Array();\n");
  echo("        for (i=0; i < cod_itens.length; i++){\n");
  echo("          if (cod_itens[i].checked){\n");
  echo("            array_itens[j]=cod_itens[i].value;");
  echo("            j++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if(j > 0){\n");
  echo("          document.getElementById('inscreve').className=\"menuUp02\";\n");
  echo("          document.getElementById('inscreve').onclick=function(){ verificar(array_itens); };\n");
  echo("        }else{\n");
  echo("          document.getElementById('inscreve').className=\"menuUp\";\n");
  echo("          document.getElementById('inscreve').onclick=function(){ };\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  $objAjax->printJavascript();

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

  echo("    <form name=\"formul\" action=\"#\" method=\"post\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=".$cod_curso.">\n");
  echo("      <input type=\"hidden\" name=\"cod_usuario\" value=".$cod_usuario.">\n");
  echo("      <input type=\"hidden\" name=\"cod_ferramenta\" value=".$cod_ferramenta.">\n");
  echo("      <input type=\"hidden\" name=\"tipo_usuario\" value=".$tipo_usuario.">\n");
  echo("      <input type=\"hidden\" id=\"codigos_usu_global\" name=\"codigos_usu_global\" value=''>\n");
  echo("      <input type=\"hidden\" name=\"action\" value='inscrever_cadastrado'>\n");

  // Pï¿½gina Principal
  /* 1 - Administraï¿½ï¿½o */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1));

  if ($tipo_usuario == 'F')
  {
    /* 50 - Inscrever Formadores */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 50)."</h4>\n";
    $cod_pagina = 6;
  }
  else if ($tipo_usuario == 'Z')
  {
    /* 164 - Inscrever Colaboradores */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 164)."</h4>\n";
    $cod_pagina = 14;
  }
  else if ($tipo_usuario == 'V')
  {
    // 182 - Inscrever Visitantes
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 182)."</h4>\n";
  }
  else if ($tipo_usuario == 'A')
  {
    /* 51 - Inscrever Alunos */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 51)."</h4>\n";
    $cod_pagina = 7;
  }
  else
  {
    echo("Arquivo inscrever.php, tipo_usuario invï¿½lido, tipo_usuario = [");
    var_dump($tipo_usuario);
    echo("]<br>\n");
    Desconectar($sock);
    die();
  }

  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/
  /* 509 - Voltar */
  echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 2 - Cancelar (geral)*/
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;confirma=0\">".RetornaFraseDaLista($lista_frases_geral,2)."</a></li>\n");
  echo("                  <li><a href=\"inscrever.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."\">Novos usu&aacute;rios"./*RetornaFraseDaLista($lista_frases_geral,2).*/"</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table id=\"tabInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head alLeft\">\n");
  /* - */
  echo("                    <td colspan=\"4\">Selecione abaixo os usu&aacute;rios que deseja cadastrar:"/*.RetornaFraseDaLista($lista_frases,58)*/."</td>\n");
  echo("                  </tr>\n"); 
  echo("                  <tr>\n");
  /* - */
  echo("                    <td align=\"left\" valign=\"top\" colspan=\"4\">Nome/E-mail/Login: <input type='text' class='input' name='busca' onkeypress='return verifica_submit(event);' value='".$busca."'>&nbsp;<input type=\"button\" class=\"input\" onclick=\"return(buscar());\" value='Buscar"/*.RetornaFraseDaLista($lista_frases,59)*/."'></td>\n");
  echo("                  </tr>\n");
  
  
  /* Contagem de usuarios: Usuarios (1 a 10 de 23) */
  echo("                  <tr class=\"head01\">\n");
  echo("                    <td align=\"left\" valign=\"top\" colspan=\"4\">");
  /* 283 - UsuÃ¡rios */
  echo(RetornaFraseDaLista($lista_frases, 297)." (");
  echo("<span id=\"prim_usr_index\"></span> ");
  /* 284 - a */
  echo(RetornaFraseDaLista($lista_frases, 298)." ");
  echo("<span id=\"ult_usr_index\"></span> ");
  /* 285 - de */
  echo(RetornaFraseDaLista($lista_frases, 299)." ");
  echo(($total_usuarios).")</td>\n");
  echo("                  </tr>\n");
  
  
  echo("                  <tr class=\"head\">\n");
  echo("                    <td></td>\n");
  /* 15 - Nome */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,15)."</b></td>\n");
  /* 52 - E-mail */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,52)."</b></td>\n");
  /* 53 - Login */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,53)."</b></td>\n");
  echo("                  </tr>\n");

  //echo("                  <tr>\n");
  /* 279 - Sua pesquisa n&atilde;o retornou resultado. */
  //echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,279)."</td>\n");
  //echo("                  </tr>\n");

  echo("                </table>\n");
  echo("              </td>\n");
  echo("              </tr>\n");
    
  echo("              <tr>\n");
  echo("              <td>\n");
  /* 59 - Inscrever */
  echo("                <div align=\"right\"><br>\n");
  echo("                  <li id=\"inscreve\" class=\"menuUp\"><span>  ".RetornaFraseDaLista($lista_frases,59)."</span></li></div>\n");
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
