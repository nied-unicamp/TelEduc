<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/criar_curso.php

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
  ARQUIVO : pagina_inicial/criar_curso.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");
       
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->registerFunction("EnviaPedidoCriacaoDinamic");

  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  $pag_atual = "criar_curso.php";
  include("../topo_tela_inicial.php");

  /* Caso o usuÃ¡rio naum esteja logado, direciona para pÃ¡gina de login */
  /*if (empty($_SESSION['login_usuario_s']))
  {*/
    /* Obtï¿½ a raiz_www */
    /*$sock = Conectar("");
    $query = "select diretorio from Diretorio where item = 'raiz_www'";
    $res = Enviar($sock,$query);
    $linha = RetornaLinha($res);
    $raiz_www = $linha[0];

    $caminho = $raiz_www."/pagina_inicial";

    header("Location: {$caminho}/autenticacao.php");
    Desconectar($sock);
    exit;
  }*/

  /* Configuração da tela que será exibida ao usuário */
  $curso_form = RetornaConfiguracao($sock, "curso_form");
  $normas = RetornaConfiguracao($sock, "normas");
  $tem_normas = (trim($normas) != '');
  $categ = RetornaCategorias($sock);

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

  if ($curso_form == "sim")
  {

    /* *********************************************************************
    Funcao ValidaSolicitacao - JavaScript. Chama as funï¿½ï¿½es que verificam os
                               campos e a caixa de checagem de requisitos.
      Entrada: Nenhuma. Funcao especï¿½fica para o formulï¿½rio da pï¿½gina
      Saida:   Boolean, para controle do onSubmit;
               true, se nao houver erros no formulario,
               false, se houver.
    */
    echo("      function ValidaSolicitacao()\n");
    echo("      {\n");
    echo("        var campos = VerificaCampos();\n");

    if ($tem_normas)
    {
      echo("        if (campos == true)\n");
      echo("          var check = VerificaCheck();\n");
      echo("        return (campos && check);\n");
    }
    else
      echo("        return (campos);\n");
    echo("      }\n\n");


    /* *********************************************************************
    Funcao VerificaCampos - JavaScript. Verifica os campos que nï¿½o podem estar vazios.
      Entrada: Nenhuma. Funcao especï¿½fica para o formulï¿½rio da pï¿½gina
      Saida:   Boolean, para controle do onSubmit;
               true, se nao houver erros no formulario,
               false, se houver.
    */

    echo("      function VerificaCampos()\n");
    echo("      {\n");
    echo("        curso = document.requisicao.nome_curso.value;\n");
    echo("        duracao = document.requisicao.duracao.value;\n");
    echo("        num_alunos = document.requisicao.num_alunos.value;\n");
    echo("        nome = document.requisicao.nome_contato.value;\n");
    echo("        email = document.requisicao.email.value;\n");
    echo("        login = document.requisicao.login.value;\n");
    echo("        if (curso == '') \n");
    echo("        {\n");
    /* 133 - O campo 'Nome do Curso' nï¿½o pode ser vazio. */
    echo("          alert('".RetornaFraseDaLista($lista_frases, 133)."');\n");
    echo("          document.requisicao.nome_curso.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        if (duracao == '') \n");
    echo("        {\n");
    /* 134 - O campo 'Duraï¿½ï¿½o' nï¿½o pode ser vazio. */
    echo("          alert('".RetornaFraseDaLista($lista_frases, 134)."');\n");
    echo("          document.requisicao.duracao.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        if (num_alunos == '') \n");
    echo("        {\n");
    /* 135 - O campo 'Nï¿½mero de Alunos' nï¿½o pode ser vazio. */
    echo("          alert('".RetornaFraseDaLista($lista_frases, 135)."');\n");
    echo("          document.requisicao.num_alunos.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        var Chars = \"0123456789\";\n");
    echo("        for (var i = 0; i < num_alunos.length; i++)\n");
    echo("        {\n");
    echo("          if (Chars.indexOf(num_alunos.charAt(i)) == -1)\n");
    echo("          {\n");
    /* 136 - O campo 'Nï¿½mero de Alunos' deve conter um nï¿½mero maior que 0. */
    echo("            alert('".RetornaFraseDaLista($lista_frases, 136)."');\n");
    echo("            document.requisicao.num_alunos.focus();\n");
    echo("            return false;\n");
    echo("          }\n");
    echo("        }\n");
    echo("        if (num_alunos<0) \n");
    echo("        {\n");
    /* 136 - O campo 'Nï¿½mero de Alunos' deve conter um nï¿½mero maior que 0. */
    echo("          alert('".RetornaFraseDaLista($lista_frases, 136)."');\n");
    echo("          document.requisicao.num_alunos.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        if (nome == '') \n");
    echo("        {\n");
    /* 137 - O campo 'Nome do Contatante' nï¿½o pode ser vazio. */
    echo("          alert('".RetornaFraseDaLista($lista_frases, 137)."');\n");
    echo("          document.requisicao.duracao.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        if (email == '') \n");
    echo("        {\n");
    /* 97 - O e-mail informado parece estar errado. Por favor verifique se vocï¿½ esqueceu o sinal '@' */
    echo("          alert('".RetornaFraseDaLista($lista_frases, 97)."');\n");
    echo("          document.requisicao.email.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        else  \n");
    echo("        {\n");
    echo("          if (email.indexOf('@')<0)\n");
    echo("          {\n");
    /* 97 - O e-mail informado parece estar errado. Por favor verifique se vocï¿½ esqueceu o sinal '@'. */
    echo("            alert('".RetornaFraseDaLista($lista_frases, 97)."');\n");
    echo("            document.requisicao.email.focus();\n");
    echo("            return false;\n");
    echo("          }\n");
    echo("          if((email.indexOf(' ')>=0)) \n");
    echo("          {\n");
    /* 98 - O e-mail informado possui espaï¿½os em branco. Por favor retire-os. */
    echo("            alert('".RetornaFraseDaLista($lista_frases, 98)."');\n");
    echo("            document.requisicao.email.focus();\n");
    echo("            return false;\n");
    echo("          }\n");
    echo("        }\n");
    echo("        if (login == '') \n");
    echo("        {\n");
    /* 99 - O campo login nï¿½o pode ser vazio. */
    echo("          alert('".RetornaFraseDaLista($lista_frases, 99)."');\n");
    echo("          document.requisicao.login.focus();\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        else  \n");
    echo("        {\n");
    echo("          if (login.indexOf(' ')>=0)\n");
    echo("          {\n");
    /* 107 - O campo login nï¿½o pode conter espaï¿½os. */
    echo("            alert('".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases, 107)))."');\n");
    echo("            document.requisicao.login.focus();\n");
    echo("            return false;\n");
    echo("          }\n");
    echo("        }\n");
    echo("        return true;\n");
    echo("      }\n\n");

    if ($tem_normas)
    {
      /* *********************************************************************
      Funcao VerificaCheck - JavaScript. Verifica se a caixa de checagem de
                             requisitos estï¿½ marcada.
        Entrada: Nenhuma. Funcao especï¿½fica para o formulï¿½rio da pï¿½gina
        Saida:   Boolean, para controle do onSubmit;
                 true, se nao houver erros no formulario,
                 false, se houver.
      */
      echo("      function VerificaCheck()\n");
      echo("      {\n");
      echo("        if (document.requisicao.chkAceita.checked)\n");
      echo("          return true;\n");
      echo("        else\n");
      echo("        {\n");
      /* 139 - Por favor leia os requisitos para solicitaï¿½ï¿½o de abertura de cursos. */
      echo("          alert('".ConverteAspas2BarraAspas(RetornaFraseDaLista($lista_frases, 139))."');\n");
      echo("          return false;\n");
      echo("        }\n");
      echo("      }\n\n");
    }
  }

  echo("      function EnviaPedido()\n");
  echo("      {\n");
  echo("        if(ValidaSolicitacao())\n");
  echo("          xajax_EnviaPedidoCriacaoDinamic(xajax.getFormValues('requisicao'));\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function TrataEnvio(flag)\n");
  echo("      {\n");
  echo("        if (flag == '' || flag == null)\n");
  echo("        {\n");
  echo("          divElement = document.getElementById('divErro');\n");
  echo("          divElement.style.display='';\n");
  echo("        }\n");
  echo("        else\n");
  echo("        {\n");
  /* 152 - Pedido de curso enviado com sucesso. */
  echo("          alert('".RetornaFraseDaLista($lista_frases, 152)."');\n");
  echo("          window.location='index.php';\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("    </script>\n");

  $objAjax->printJavascript("../xajax_0.2.4/");

  include("../menu_principal_tela_inicial.php");

  /*
  ==================
  Programa Principal
  ==================
  */

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  //9 - Como criar um curso
  echo("          <h4>".RetornaFraseDaLista($lista_frases,9)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  if ($curso_form == "sim")
  {
    echo("            <tr>\n");
    echo("              <td>\n");
    echo("                <ul class=\"btAuxTabs\">\n");
    /* 2 - Cancelar (Ger) */
    echo("                  <li><span onclick=\"document.location='index.php';\">".RetornaFraseDaLista($lista_frases_geral,2)."</span></li>\n");
    echo("                </ul>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
  }
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");

  if ($curso_form == "sim")
  {
    $dados = RetornaDadosUsuario($sock,$_SESSION['cod_usuario_global_s']);

    echo("              <form name=\"requisicao\" id=\"requisicao\" action=\"\" method=\"get\" onSubmit=\"return(EnviaPedido());\">\n");
    // Passa o valor de curso_form para criar_curso2.php onde seu valor serï¿½ testado para
    // efetivar a solicitaï¿½ï¿½o.
    echo("                  <input type=\"hidden\" name=\"curso_form\" value='".$curso_form."' />\n");
    echo("                  <input type=\"hidden\" name=\"nome_contato\" value='".$dados['nome']."' />\n");
    echo("                  <input type=\"hidden\" name=\"email\" value='".$dados['email']."' />\n");
    echo("                  <input type=\"hidden\" name=\"login\" value='".$dados['login']."' />\n");
    if (count($categ)<=0)
      echo("                  <input type=\"hidden\" name=\"cod_pasta\" value=\"NULL\" />\n");
  }
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  $instituicao=RetornaDadosIntituicao($sock);

  if ($curso_form == "nao")
  {
    echo("                  <tr class=\"head\">\n");
    /* 58 - Se vocï¿½ deseja utilizar o ambiente TelEduc para realizar um curso a distï¿½ncia, entre em contato com os responsï¿½veis pelo ambiente. */
    /* 59 - O curso serï¿½ realizado com a autorizaï¿½ï¿½o destes e serï¿½ hospedado no servidor do(a) */
    echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases, 58).RetornaFraseDaLista($lista_frases,59)." ".$instituicao['nome']."</td>\n");
    echo("                  </tr>\n");
    
    $lista=RetornaContatos($sock);

    echo("                  <tr class=\"head01\">\n");
    /* 42 - Responsï¿½veis */
    /* 43 - E-mail */
    echo("                    <td width=\"60%\">".RetornaFraseDaLista($lista_frases,42)."</td>\n");
    echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,43)."</td>\n");
    echo("                  </tr>\n");

    if (count($lista)>0 && $lista != "")
    {
      foreach($lista as $cod => $linha)
      {
        echo("                  <tr>\n");
        echo("                    <td>".$linha['nome']."</td>\n");
        echo("                    <td class=\"alLeft\"><a href=mailto:".$linha['email'].">".$linha['email']."</a></td>\n");
        echo("                  </tr>\n");
      }
    }
    else
    {
      echo("                  <tr>\n");
      /* 44 - Nenhum responsï¿½vel cadastrado */
      echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,44)."</td>\n");
      echo("                  </tr>\n");
    }
  }
  else if ($curso_form == "sim")
  {
    /* 142 - Dados do Curso */
    echo("                  <tr class=\"head\">\n");
    echo("                    <td>".RetornaFraseDaLista($lista_frases,142)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"alLeft\">\n");
    /* 154 - Se vocï¿½ deseja utilizar o ambiente TelEduc para realizar um curso a distï¿½ncia, preencha e envie o formulï¿½rio abaixo.  */
    /* 155 - O pedido serï¿½ avaliado pelos responsï¿½veis pelo ambiente e, caso aprovado, o curso serï¿½ hospedado no servidor do(a)  */
    echo("                    <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases, 154).RetornaFraseDaLista($lista_frases, 155)."</p><br />\n");
    /* 153 - Erro no envio de pedido. Tente novamente.*/
    echo("                    <div id=\"divErro\" align=\"center\" style=\"display:none;\"><font color=\"red\">".RetornaFraseDaLista($lista_frases, 153)."</font></div>\n");
    echo("                      <div align=\"center\"><br /><table>\n");

    /* 143 - Nome do Curso:  */
    echo("                        <tr>\n");
    echo("                          <td style=\"border:none; text-align:right;\">".RetornaFraseDaLista($lista_frases,143)." (*)</td>\n");
    echo("                          <td class=\"alLeft\" style=\"border:none\"><input class=\"input\" type=\"text\" name=\"nome_curso\" size=\"33\" maxlength=\"128\" /></td>\n");
    echo("                        </tr>\n");

    /* 144 - Duraï¿½ï¿½o estimada:  */
    echo("                        <tr>\n");
    echo("                          <td style=\"border:none; text-align:right;\">".RetornaFraseDaLista($lista_frases,144)." (*)</td>\n");
    echo("                          <td class=\"alLeft\" style=\"border:none\"><input class=\"input\" type=\"text\" name=\"duracao\" size=\"20\" maxlength=\"128\" /></td>\n");
    echo("                        </tr>\n");

    /* 145 - Nï¿½mero de Alunos:  */
    echo("                        <tr>\n");
    echo("                          <td style=\"border:none; text-align:right;\">".RetornaFraseDaLista($lista_frases,145)." (*)</td>\n");
    echo("                          <td class=\"alLeft\" style=\"border:none\"><input class=\"input\" type=\"text\" name=\"num_alunos\" size=\"6\" maxlength=\"10\" /></td>\n");
    echo("                        </tr>\n");

    if (!empty($categ))
    {
      /* 146 - Categoria: */
      echo("                        <tr>\n");
      echo("                          <td style=\"border:none; text-align:right;\">".RetornaFraseDaLista($lista_frases,146)."</td>\n");
      echo("                          <td class=\"alLeft\" style=\"border:none\">\n");
      echo("                            <select class=\"input\" name=\"cod_pasta\">\n");
      echo("                              <option value=NULL>&nbsp;</option>\n");
      foreach ($categ as $cod_pasta => $pasta)
      {
        echo("                              <option value=".$cod_pasta.">".$pasta."</option>\n");
      }
      echo("                            </select>\n");
      echo("                          </td>\n");
      echo("                        </tr>\n");
    }

    /* 60 - Pï¿½blico alvo: */
    echo("                        <tr>\n");
    echo("                          <td style=\"border:none; text-align:right;\">".RetornaFraseDaLista($lista_frases,60)."</td>\n");
    echo("                          <td class=\"alLeft\" style=\"border:none\"><input class=\"input\" type=\"text\" name=\"publico_alvo\" size=\"33\" /></td>\n");
    echo("                        </tr>\n");

    /* 63 - Tipo de inscriï¿½ï¿½o: */
    echo("                        <tr>\n");
    echo("                          <td style=\"border:none; text-align:right;\">".RetornaFraseDaLista($lista_frases, 63)."</td>\n");
    echo("                          <td class=\"alLeft\" style=\"border:none\"><input class=\"input\" type=\"text\" name=\"tipo_inscricao\" size=\"33\" /></td>\n");
    echo("                        </tr>\n");

    /* 87 - Informaï¿½ï¿½es adicionais: */
    echo("                        <tr>\n");
    echo("                          <td valign=\"top\" style=\"border:none; text-align:right;\">".RetornaFraseDaLista($lista_frases,87)."</td>\n");
    echo("                          <td class=\"alLeft\" style=\"border:none\"><textarea class=\"input\" name=\"info\" rows=\"4\" cols=\"40\"></textarea></td>\n");
    echo("                        </tr>\n");

    /* 150 - Nome da Instituiï¿½ï¿½o:  */
    echo("                        <tr>\n");
    echo("                          <td style=\"border:none; text-align:right;\">".RetornaFraseDaLista($lista_frases,150)."</td>\n");
    echo("                          <td class=\"alLeft\" style=\"border:none\"><input class=\"input\" type=\"text\" name=\"nome_inst\" size=\"33\" maxlength=\"128\" /></td>\n");
    echo("                        </tr>\n");

    echo("                        <tr>\n");
    echo("                          <td class=\"alLeft\" colspan=\"2\" style=\"border:none;\">\n");
    if ($tem_normas)
    {
      echo("                            <br /><div align=center>\n");
      /* 140 - Somente serï¿½o avaliados/aceitos cursos que satisfaï¿½am os requisitos abaixo: */
      echo("                            ".RetornaFraseDaLista($lista_frases,140)."<br /><br />\n");
      echo(                             "<textarea class=\"input\" name=\"taNormas\" readonly cols=\"60\" rows=\"5\">".$normas."</textarea><br /><br />\n");
      if ($curso_form == "sim")
      {
        /* 141 - Li os requisitos acima e o curso que pretendo criar adequa-se a eles. */
        echo("                            <input type=\"checkbox\" name=\"chkAceita\" value=\"1\" />".RetornaFraseDaLista($lista_frases,141)."\n");
      }
      echo("                            </div>\n");
    }
    echo("                          </td>\n");
    echo("                        </tr>\n");
    echo("                          <tr>\n");
    echo("                            <td style=\"border:none\"></td>\n");
    echo("                            <td class=\"alLeft\" style=\"border:none\">\n");
    /* 66 - (*) Campos Obrigatï¿½rios */
    echo("                              ".RetornaFraseDaLista($lista_frases_configurar,66)."\n");
    echo("                              <br /><br />\n");
    echo("                            </td>\n"); 
    echo("                          </tr>\n");
    /* 11 - Enviar */
    echo("                        <tr>\n");
    echo("                          <td style=\"border:none; text-align:right;\"></td>\n");
    echo("                          <td class=\"alLeft\" style=\"border:none\"><input class=\"input\" type=\"submit\" value='".RetornaFraseDaLista($lista_frases_geral,11)."' /></td>\n");
    echo("                        </tr>\n");
    echo("                      </table></div>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  echo("                </table>\n");
  echo("                </form>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
