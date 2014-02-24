<?php
/*
 <!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/faz_inscricao.php

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
  ARQUIVO : cursos/aplic/administracao/faz_inscricao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta = 0;

  include("../topo_tela.php");

  /* Impede o acesso a algumas secoes aos usuÃ¡rios que nÃ£o sÃ£o formadores. */
  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
    include("../menu_principal.php");

    echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

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

  $nomeArq = $_FILES['arquivoInsc']['name'];
  $row = 0; $contSuc = 0;
  $falhas = array();
  $leu_arquivo = (($fp = fopen($_FILES['arquivoInsc']['tmp_name'], "r")) !== FALSE);
  if ($leu_arquivo) {
    while (($data = fgetcsv($fp, 0, ",")) !== FALSE) {
      $num = count ($data);
      if($num == 3){
        $regExp = "^[a-zA-Z][a-zA-Z0-9\._-]*[a-zA-Z0-9_]@[a-zA-Z0-9]+([.][a-zA-Z0-9]+)+(\.[a-z]{0,6})?$";
        $regExp = "/^[\w-]+(\.[\w-]+)*@(([A-Za-z\d][A-Za-z\d-]{0,61}[A-Za-z\d]\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/";

        if($data[0]=="" || $data[1]=="" || $data[2]==""){
          $falhas[] = ++$row;
        }
        else if(preg_match($regExp, $data[1])) {
          if (function_exists('mb_convert_encoding')) {
            //converte o encoding do nome e do login do usuario de UTF-8 para ISO-8859-1
            $data[0] = mb_convert_encoding($data[0], "ISO-8859-1", "UTF-8");
            $data[2] = mb_convert_encoding($data[2], "ISO-8859-1", "UTF-8");
          }
          $dados_preenchidos_s[$contSuc]['nome']=$data[0];
          $dados_preenchidos_s[$contSuc]['email']=$data[1];
          $dados_preenchidos_s[$contSuc]['login']=$data[2];
          $dados_preenchidos_s[$contSuc]['linhaReg']=$row+1;
          $contSuc++;
          $row++;
        }else{
          $falhas[] = ++$row;
        }

      }else{
        $falhas[] = ++$row;
      }
    } 

    $logins = RetornaLoginsInscricao($sock);
    $emails = RetornaEmailsInscricao($sock);

    // booleano que indica se um login passado jah estah sendo usado por outro usuario
    $login_existente = false;
    $email_existente = false;

    $contExist=0;
    $login_existente = false;

    foreach($dados_preenchidos_s as $cod => $linha){
      if($emails[strtoupper($linha['email'])]==1){
        /*pega o login do usuï¿½rio*/
        $dados_preenchidos_s[$cod]['login']=PegaLogin($sock,$linha['email']);
        $dados_preenchidos_s[$cod]['status_email']=1;
      }
      else
        $dados_preenchidos_s[$cod]['status_email']=0;
    }
    foreach($dados_preenchidos_s as $cod => $linha){
      if ($logins[strtoupper($linha['login'])]==1 && $linha['status_email']==0){
        $dados_preenchidos_s[$cod]['status_login']=1;
        $dados_preenchidos_s[$cod]['novo_login']=GeraLogin($sock,$linha['email']);
        $dados_preenchidos_s[$cod]['senha']=GeraSenha();
        $dados_preenchidos_s[$cod]['tipo_usuario']=$tipo_usuario;
        $login_existente=true;
      }
    }
    foreach($dados_preenchidos_s as $cod => $linha){
      if($dados_preenchidos_s[$cod]['status_login']!=1){
        $linha['tipo_usuario']=$tipo_usuario;
        $linha['senha']=GeraSenha();
        if($linha['status_email']==1){
          $cadastrado=LoginCadastradoCurso($sock, $linha['login'], $cod_curso);
          if($cadastrado==false)
            $sock=CadastrarUsuarioExistente($sock,$cod_curso,$linha,$lista_frases);
        }
        else
          $sock=CadastrarUsuario($sock,$cod_curso,$linha,$lista_frases,$cod_usuario);
      }
    }
  }

//  $dados_preenchidos_s = "";

  if(!empty($falhas) || ($login_existente != false) || !$leu_arquivo) {
    /*
    ==================
    Funï¿½ï¿½es JavaScript
    ==================
    */
    echo("    <script type=\"text/javascript\">\n\n");

    echo("      function Iniciar()\n");
    echo("      {\n");
    echo("        startList();\n");
    echo("      }\n\n");

    echo("    </script>\n\n");

    include("../menu_principal.php");

    echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

    /* 1 - Enquete */
    echo("          <h4>".RetornaFraseDaLista($lista_frases, 1));

    if ($tipo_usuario=="F"){
      /* 50 - Inscrever Formadores */
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 50)."</h4>";
      $cod_pagina=6;
    }
    else if ($tipo_usuario == 'Z'){
      // 164 - Inscrever Colaboradores
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 164)."</h4>";
      $cod_pagina=14;
    }
    else if ($tipo_usuario == 'V'){
      // 182 - Inscrever Visitantes
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 182)."</h4>";
    }
    else if ($tipo_usuario == 'A'){
      /* 51 - Inscrever Alunos */
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 51)."</h4>";
      $cod_pagina=7;
    }
    echo $cabecalho;

    /*Voltar*/
    /* 509 - Voltar */
    echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    echo("          <!-- Tabelao -->\n");
    echo("			  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
    echo("			  <tr>\n");
    echo("			  <td>\n");
    echo("			  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    if (!$leu_arquivo) {
      echo("<tr>\n");
      echo("<td colspan=\"2\">\n");
      echo("Falha ao abrir arquivo.");
      echo("</td>");
      echo("</tr>");
    }
    if (!empty($falhas)) {
      echo("<tr>\n");
      echo("<td colspan=\"2\">\n");

      if(count($falhas)==1)
        echo("O registro contido na seguinte linha do arquivo <strong>".$nomeArq."</strong> n&atilde;o est&aacute; no formato correto:<br>");
      else
        echo("Os registros contidos nas seguintes linhas do arquivo <strong>".$nomeArq."</strong> n&atilde;o est&atilde;o no formato correto:<br>");
      foreach($falhas as $cod => $falha){
        if($cod != 0) echo " - ".$falha;
        else echo $falha;
      }
      echo "<br>";
      echo("</td>");
      echo("</tr>");
    }
    if($login_existente == true && empty($falhas)) {

        echo("<tr class=\"head\">\n");
        /* 308 - Não foi possível realizar o cadastro. Um ou mais usuário já cadastrado no curso. */
        echo("<td colspan=\"2\">".RetornaFraseDaLista($lista_frases,308)."<strong></td>");
        echo("</tr>\n");
        echo("<tr class=\"head\">\n");
        /* 309 - Esse e-mail possui mais de um login e senha associados. Segue abaixo a lista de logins e senhas cadastrados */
        echo("<td>".RetornaFraseDaLista($lista_frases,309)."</td>\n");
        /* 310 - Senha */
        echo("<td>".RetornaFraseDaLista($lista_frases,310)."</td>");

        foreach($dados_preenchidos_s as $cod => $existente) {
          if($existente['status_login']==1) {
            echo("<tr>\n");
            echo("<td>".$existente['login']."</td>\n");
            echo("<td><input type=\"text\" value=".$existente['novo_login']." maxlength=\"20\" size=\"10\" name=\"login[]\" id=\"login\" class=\"input\"></td>\n");
            echo("</tr>\n");
            echo("\n");
          }
        }

       $_SESSION['login_existente']= $login_existente;
       $_SESSION['dados']=$dados_preenchidos_s;
       $_SESSION['lista_frases']=$lista_frases;

    }
    echo("              </td>\n");
    echo("              </tr>\n");

    echo("              </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
    echo("              <td>\n");
    echo("                <ul class=\"btAuxTabs\">\n");
    echo("                  <li id=\"continuar\"><a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=0&tipo_usuario=".$tipo_usuario."\" id=\"apagarMsg\">Continuar</span></li>\n");
    echo("                </ul>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");

    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");

  } else {
    $confirma='true';
    Header("Location:gerenciamento_usuarios.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&tipo_usuario=".$tipo_usuario."&falha[]=".$falhas."&logindEmail_existentes[]=".$logindEmail_existentes."&acao_fb=inscrever&atualizacao=".$confirma."");
  }

  Desconectar($sock);

?>