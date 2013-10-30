<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perfil/exibir_perfis.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
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

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/
/*==========================================================
  ARQUIVO : cursos/aplic/perfil/exibir_perfis.php
  ========================================================== */

/*
==================
Programa Principal
==================
*/

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perfil.inc");
  require_once("../xajax_0.2.4/xajax.inc.php");


//   Global $cod_lingua_s;
  $cod_lingua = $_SESSION['cod_lingua_s'];

  // Estancia o objeto XAJAX
  $objMudarComp = new xajax();
  // Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objMudarComp->registerFunction("RetornaDadosPerfilDinamic");
  $objMudarComp->registerFunction("EditarPerfilDinamic");

  // Manda o xajax executar os pedidos acima.
  $objMudarComp->processRequests();

  $cod_ferramenta=13;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("enviarFoto", 106, 107);
  $feedbackObject->addAction("apagarFoto", 128, 129);



  $sock=Conectar("");


  $curso_info = RetornaDadosCurso($sock, $cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $_SESSION["cod_usuario_global_s"], $cod_curso);


  $curso_info = RetornaDadosCurso($sock, $cod_curso);

  $diretorio_arquivo=RetornaDiretorio($sock,"Arquivos");
  $diretorio_temp=RetornaDiretorio($sock,"ArquivosWeb");

  Desconectar($sock);
  $sock=Conectar($cod_curso);

  $eformador  = EFormador ($sock, $cod_curso, $cod_usuario);
  $econvidado = EConvidado($sock, $cod_usuario, $cod_curso);

  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");

  /*
  ==================
  Funcoes JavaScript
  ==================
  */
  echo("    <script type=\"text/javascript\">\n");
  echo("      var cod_ferramenta='".$cod_ferramenta."';\n");
  echo("      var cod_curso='".$cod_curso."';\n");
  echo("      var cod_usuario='".$cod_usuario."';\n");
  echo("      var editando=-2;\n");
  /* (ger) 18 - Ok */
  // Texto do bot�o Ok do ckEditor
  echo("      var textoOk = '".RetornaFraseDaLista($lista_frases_geral, 18)."';\n\n");
  /* (ger) 2 - Cancelar */
  // Texto do bot�o Cancelar do ckEditor
  echo("      var textoCancelar = '".RetornaFraseDaLista($lista_frases_geral, 2)."';\n\n");

  /*
  Funcao Open Window - Abre a janela que ira mostrar os perfis
    Entrada:  id. Nome da pagina a abrir na nova janela
    Saida  :  false. Assim, qdo usada em uma tag <A HREF=# ..., nao recarrega a pagina
  */
  echo("      function OpenWindow(id)\n");
  echo("      {\n");
  echo("         window.open(id,\"JanelaDados\",\"width=600,height=400,top=110,left=110,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function Submissao(pagina)\n");
  echo("      {\n");
  echo("        document.frmPerfil.action = pagina;\n");
  echo("        document.frmPerfil.submit();\n");
  echo("        return false;\n");
  echo("      }\n");

  echo("      function ApagarFoto()\n");
  echo("      {\n");
  // 126 - Você deseja realmente apagar sua foto ?
  // 127 - (Você poderá enviar outra foto mais tarde usando a opção Enviar / Atualizar Foto)
  echo("        if (confirm ('".RetornaFraseDaLista($lista_frases, 126)."\\n".RetornaFraseDaLista($lista_frases, 127)."'))\n");
  echo("          return Submissao('apagar_foto.php');\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function Iniciar(){\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("        self.focus();\n");
  echo("      }\n");

  if ($eformador){
    echo("      function MostraDadosPessoais(cod){\n");
    echo("        divElement = document.getElementById('dados1_'+cod);\n");
    echo("        divElement.className=\"divHidden\";\n");
    echo("        divElement = document.getElementById('dados2_'+cod);\n");
    echo("        divElement.className=\"divShow\";\n");
    echo("        xajax_RetornaDadosPerfilDinamic('".$cod_curso."', cod);\n");
    echo("        if (editando!=-2){");
    echo("          EdicaoTexto('', 'text_'+editando, 'canc');\n");
    echo("        }\n");
    echo("      }\n\n");
  }

  /*echo("      function EditarPerfil(cod){\n");
  echo("        if(editando!=-2) return;\n");
  echo("        document.getElementById('orientacao').style.display=\"\";\n");
  //echo("        conteudo = document.getElementById('msg_corpo').innerHTML;\n");
  //echo("        writeRichTextOnJS('text_'+cod+'_text', conteudo, 520, 200, true, false, cod);\n");
  echo("		CKEDITOR.replace('msg_corpo');\n");
  echo("		document.getElementById('OKComent').style.display=\"\";\n ");
  echo("		document.getElementById('cancComent').style.display=\"\";\n ");

  //echo("		if(document.getElementById('msg_corpo') != null){\n");
  echo("			document.getElementById('msg_corpo').style.display=\"\";");
  //echo("		}");
  echo("        editando=cod;\n");
  echo("      }\n");*/

  echo("      function EditarPerfil(cod){\n");
  echo("        if(editando!=-2) return;\n");
  echo("        document.getElementById('orientacao').style.display=\"\";\n");
  //echo("        document.getElementById('text_'+cod).removeChild('p');\n");
  echo("        conteudo = document.getElementById('text_'+cod).innerHTML;\n");
  echo("        writeRichTextOnJS('text_'+cod+'_text', conteudo, 520, 200, true, false, cod);\n");
  echo("        editando=cod;\n");
  echo("      }\n");

  echo("      function EdicaoTexto(codigo, id, valor){\n");
  echo("        if (valor=='ok'){\n");
  //echo("          eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');");
  echo("   		conteudo = document.getElementById('text_'+codigo+'_text').value;");
  echo("		alert(conteudo);");
  echo("          xajax_EditarPerfilDinamic('".$cod_curso."', codigo, conteudo, '".RetornaFraseDaLista($lista_frases,116)."');\n");
  echo("        }\n");
  //echo("        document.getElementById('text_'+codigo).innerHTML= '$campo'+conteudo;\n");
  //echo("        document.getElementById('text_'+codigo).innerHTML= conteudo+'$campo'+conteudo+'</textarea>';\n");
  echo("        document.getElementById(id).innerHTML=conteudo;");
  echo("        document.getElementById('orientacao').style.display=\"none\";\n");
  echo("        editando=-2;\n");
  //echo("        document.getElementById(id).innerHTML=conteudo;");
  //echo("        CKEDITOR.remove('cke_msg_corpo');");
  echo("      }\n\n");

  /*echo("      function CancelarNovaMsg(cod){\n");
  echo("        alert(cod);");
  echo("        clearRTE(cod);\n");
  echo("        document.getElementById('cke_'+cod+'_text').style.display=\"none\";");
  echo("        document.getElementById('OKComent').style.display=\"none\";\n ");
  echo("        document.getElementById('cancComent').style.display=\"none\";\n ");
  echo("      }\n\n");*/

  echo("    </script>\n");

  $objMudarComp->printJavascript("../xajax_0.2.4/");
  /*
  =============================
  Retorno ao programa principal
  =============================
  */

  echo("  </head>\n");
  if($_GET['imprimir'] == 1){
    echo("  <body bgcolor=\"white\" onload=\"Iniciar();self.print();\">\n");
  }
  else{
    echo("  <body bgcolor=\"white\" onload=\"Iniciar();\">\n");
  }
  echo("    <a name=\"topo\"></a>\n");
  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
  echo("      <tr>\n");
  echo("        <td rowspan=\"3\"></td>\n");
  echo("        <td valign=\"top\">\n");
  /* 1 - Perfil */
  $cabecalho ="<h4>".RetornaFraseDaLista($lista_frases,1);
  /* 29 - Exibir perfis */
  $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,29)."</h4>";
  echo("          <br /><br />".$cabecalho);
  /* ? - ocorreu um erro na sua solicitacao */
  echo("          <div id=\"feedback\" class=\"feedback_hidden\"><span id=\"span_feedback\">ocorreu um erro na sua solicitacao</span></div>\n");
  echo("          </tr>\n");
  echo("          <tr>\n");
  echo("            <td>\n");

  /*
    Como funciona o fluxo desta pagina: É possivel chegar aqui através de perfil.php (e os usuarios a exibir vem atraves da checkbox)
    ou é possivel ir desta pagina para outra, e voltar para c�depois (entao os usuarios a exibir estao nos 5 arrays abaixo)
  */
  if (isset($alunocod) || isset($formadorcod) || isset($coordenadorcod) || isset($convidadocod) || isset($visitantecod))
  {
    unset($cod_aluno);
    unset($cod_formador);
    unset($cod_coordenador);
    unset($cod_convidado);
    unset($cod_visitante);

    if (isset($alunocod))
      $cod_aluno = explode("_", $alunocod);
    if (isset($formadorcod))
      $cod_formador = explode("_", $formadorcod);
    if (isset($coordenadorcod))
      $cod_coordenador = explode("_", $coordenadorcod);
    if (isset($convidadocod))
      $cod_convidado = explode ("_", $convidadocod);
    if (isset($visitantecod))
      $cod_visitante = explode ("_", $visitantecod);
  }

  // Aqui se tratam os arrays passados direto de perfil.php atrav� do submit, e os convertidos no if acima
  $i=0;
  unset ($perfil_Cod_usuario);

  if (count($cod_aluno)>0)
    foreach($cod_aluno as $cod => $valor)
      $perfil_Cod_usuario[$i++]=$valor;

  if (is_array ($cod_visitante))
    foreach($cod_visitante as $valor)
      $perfil_Cod_usuario[$i++] = $valor;

  if (is_array ($cod_convidado))
    foreach($cod_convidado as $cod => $valor)
      $perfil_Cod_usuario[$i++] = $valor;

  if (count($cod_formador)>0)
    foreach($cod_formador as $cod => $valor)
      $perfil_Cod_usuario[$i++]=$valor;

  if (count($cod_coordenador)>0)
    foreach($cod_coordenador as $cod => $valor)
      $perfil_Cod_usuario[$i++] = $valor;


  if (!is_array($perfil_Cod_usuario))
  {
    /* 10 - Nenhuma pessoa selecionada! */
    echo("          <b>".RetornaFraseDaLista($lista_frases,10)."</b>");
    echo("          <br /><br />");
    /* 11 - Selecione a pessoa de quem voc�deseja ver o perfil clicando
            sobre o nome da mesma, ou selecionando v�ias atrav� das caixas de sele�o e
            pressionando o bot� "Mostrar Selecionados" */
    echo("          ".RetornaFraseDaLista($lista_frases,11)."\n");
    echo("          <br /><br />");
    /* G 13 - Fechar */
    echo("          <ul class=\"btAuxTabs\">\n");
    echo("            <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
    echo("          </ul>\n");
  }
  else
  {
    echo("          <form action=\"\" name=\"frmPerfil\" method=\"get\">\n");
    echo("            <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
    if (is_array($cod_aluno))
    {
      $alunocod = implode("_",$cod_aluno);
      echo("            <input type=\"hidden\" name=\"alunocod\" value=\"".$alunocod."\" />\n");
    }
    if (is_array($cod_formador))
    {
      $formadorcod = implode("_",$cod_formador);
      echo("            <input type=\"hidden\" name=\"formadorcod\" value=\"".$formadorcod."\" />\n");
    }
    if (is_array($cod_coordenador))
    {
      $coordenadorcod = implode("_",$cod_coordenador);
      echo("            <input type=\"hidden\" name=\"coordenadorcod\" value=\"".$coordenadorcod."\" />\n");
    }
    if (is_array($cod_convidado))
    {
      $convidadocod = implode ("_", $cod_convidado);
      echo("            <input type=\"hidden\" name=\"convidadocod\" value=\"".$convidadocod."\" />\n");
    }
    if (is_array($cod_visitante))
    {
      $visitantecod = implode ("_", $cod_visitante);
      echo("            <input type=\"hidden\" name=\"visitantecod\" value=\"".$visitantecod."\" />\n");
    }

    $num=0;

    while($num < count($perfil_Cod_usuario))
    {
      $cod_usuario_ficha = $perfil_Cod_usuario[$num];
      $num++;

      $dados=DadosUsuario($sock,$cod_curso,$cod_usuario_ficha);

      $nome=$dados['nome'];
      $email=$dados['email'];
      $funcao=FuncaoPorExtenso($sock,$lista_frases,$dados['tipo_usuario']);

      $linha = RetornaPerfil($sock, $cod_usuario_ficha);

      $perfil_existe = ((count($linha)>0)&&($linha!="")&&($linha!="<P>&nbsp;</P>")&&($linha!="<br>"));

      echo("            <input type=\"hidden\" name=\"perfil_existe\" value=\"".$perfil_existe."\" />\n");

      $status_curso = RetornaStatusCurso($sock,$cod_curso);

      echo("            <table border=\"0\" width=\"100%\" cellspacing=\"2\">\n");
      echo("              <tr>\n");
      echo("                <td align=\"center\">\n");
      echo("                  <ul class=\"btAuxTabs\">\n");
      $nome_foto = CaminhoFoto($cod_curso,$cod_usuario_ficha,$diretorio_arquivo,$diretorio_temp);

      /* Se o curso estiver encerrado e for um aluno, n� pode mais editar o contedo */
      if (($cod_usuario_ficha == $cod_usuario) && !(($status_curso=='E') && (!EFormador($sock,$cod_curso,$cod_usuario))))
      {
        /* dono do perfil */

        if ($perfil_existe) /* se o perfil existe */
        {
          /* 21 - Alterar Perfil */
          $frase = RetornaFraseDaLista($lista_frases, 21);
        }
        else /* perfil vazio */
        {
          /* 24 - Preencher Perfil*/
          $frase = RetornaFraseDaLista($lista_frases,24);
        }

        echo("                    <li><span onclick=\"EditarPerfil(".$cod_usuario_ficha.");\">".$frase."</span></li>\n");
        /* 23 - Enviar/Atualizar Foto */
        echo("                    <li><span onclick=\"return(Submissao('enviar_foto.php'));\">".RetornaFraseDaLista($lista_frases,23)."</span></li>\n");

        if ($nome_foto != "")
        {
          /* 125 - Apagar Foto */
          echo("                    <li><span onclick=\"return(ApagarFoto());\">".RetornaFraseDaLista($lista_frases, 125)."</span></li>\n");
        }

        if ($eformador) /* se eh formador */
        {
          /* 20 - Ver dados pessoais */
          echo("                    <li><span onclick=\"MostraDadosPessoais('$cod_usuario_ficha');\">".RetornaFraseDaLista($lista_frases,20)."</span></li>\n");
                  }
      }
      else /* não eh dono do perfil */
      {
        if ($eformador) /* se eh formador */
        {
          /* 20 - Ver dados pessoais */
          echo("                    <li><span onclick=\"MostraDadosPessoais('$cod_usuario_ficha');\">".RetornaFraseDaLista($lista_frases,20)."</span></li>\n");
          /* 20 - Ver dados pessoais */
        }
      }
      /* G 13 - Fechar */
      echo("                    <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
      echo("                  </ul>\n");
      echo("                </td>\n");
      echo("              </tr>\n");
      echo("            </table>\n");
      if($_GET['imprimir'] == 0){
      	echo("            <br />\n");
      }

      echo("            <table border=\"0\" width=\"100%\" cellspacing=\"0\">\n");




      echo("              <tr>\n");
      echo("                <td>\n");

      if ($eformador) /* se eh formador */
      {
        echo("                  <div class=\"divHidden\" id=\"dados2_".$cod_usuario_ficha."\">\n");
        echo("                    <table class=\"g1field\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\" width=\"100%\">\n");
        echo("                      <tr>\n");
        echo("                        <td id=\"tdNome_".$cod_usuario_ficha."\" colspan=\"2\" style=\"font-weight:bold; text-align:center;\"></td>\n");
        echo("                      </tr>\n");
        echo("                      <tr>\n");
        /* 31 - Endereço */
        echo("                        <td valign=\"top\" align=\"right\" width=\"20%\" style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases,31).": </td>\n");
        echo("                        <td id=\"tdEnd_".$cod_usuario_ficha."\" align=\"left\"></td>\n");
        echo("                      </tr>\n");
        echo("                      <tr>\n");
        /* 32 - Telefone */
        echo("                        <td valign=\"top\" align=\"right\" style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases,32).": </td>\n");
        echo("                        <td id=\"tdTel_".$cod_usuario_ficha."\" align=\"left\"></td>\n");
        echo("                      </tr>\n");
        echo("                      <tr>\n");
        /* 33 - Email */
        echo("                        <td valign=\"top\" align=\"right\" style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases,33).": </td>\n");
        echo("                        <td id=\"tdEmail_".$cod_usuario_ficha."\" ></td>\n");
        echo("                      </tr>\n");
        echo("                      <tr>\n");
        /* 34 - Data de nascimento */
        echo("                        <td valign=\"top\" align=\"right\" style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases,34).": </td>\n");
        echo("                        <td id=\"tdNasc_".$cod_usuario_ficha."\"></td>\n");
        echo("                      </tr>\n");
        echo("                      <tr>\n");
        /* 35 - Local de trabalho */
        echo("                        <td valign=\"top\" align=\"right\" style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases,35).": </td>\n");
        echo("                        <td id=\"tdLocalTrab_".$cod_usuario_ficha."\"></td>\n");
        echo("                      </tr>\n");
        echo("                      <tr>\n");
        /* 36 - Profissao */
        echo("                        <td valign=\"top\" align=\"right\" style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases,36).": </td>\n");
        echo("                        <td id=\"tdProfis_".$cod_usuario_ficha."\" align=\"left\"></td>\n");
        echo("                      </tr>\n");
        echo("                      <tr>\n");
        /* 37 - Informações adicionais */
        echo("                        <td valign=\"top\" align=\"right\" style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_frases,37).": </td>\n");
        echo("                        <td id=\"tdInfo_".$cod_usuario_ficha."\" align=\"left\">".RetornaFraseDaLista($lista_frases,123)."</td>\n");
        echo("                      </tr>\n");
        echo("                    </table>\n");
        echo("                  </div>\n");
      }

      echo("                  <div id=\"dados1_".$cod_usuario_ficha."\">\n");
      echo("                    <b>".$nome."</b>\n");
      if($_GET['imprimir'] == 0){
      	echo("                    <br />\n");
      }

      if (! $econvidado)
        /* 33 - Email */
        echo("                    <b>".RetornaFraseDaLista($lista_frases,33).":</b> <a href=\"mailto:".$email."\">$email</a><br />\n");

      /* 14 - Função */
      echo("                    <b>".RetornaFraseDaLista($lista_frases,14).":</b> $funcao.<br />\n");
      echo("                  </div>\n");
      echo("                </td>\n");
      echo("                <td align=\"right\">\n");

      if ($nome_foto!="")
      {
        echo("                  <table style=\"border: 1pt solid #4388AE\">\n");
        echo("                    <tr>\n");
        echo("                      <td>\n");
        echo("                        <img src='".$nome_foto."?".time()."' alt='".$nome."' name=\"FotoJpg\" width=\"90\" height=\"120\" />\n");
      }
      else
      {
        echo("                  <table style=\"border: 1pt solid #4388AE\">\n");
        echo("                    <tr>\n");
        echo("                      <td width=\"90\" height=\"120\">\n");
        echo("                        <center>\n");
        /* 15 - Foto */ /* 16 - N� */ /* 17 - Dispon�el */
        echo("                          (".RetornaFraseDaLista($lista_frases,15)."<br />".RetornaFraseDaLista($lista_frases,16)."<br />".RetornaFraseDaLista($lista_frases,17));
        echo(")\n");
        echo("                        </center>\n");
      }
      echo("                      </td>\n");
      echo("                    </tr>\n");
      echo("                  </table>\n");

      if($cod_usuario_ficha == $cod_usuario){
        echo("                    <tr>\n");
        echo("                      <td>\n");
        $orientacao_perfil=Enter2BR(RetornaOrientacaoPerfil($sock, $cod_lingua));
        echo("                        <p id=\"orientacao\" style=\"display:none;\">".$orientacao_perfil."</p>\n");
        echo("                      </td>\n");
        echo("                    </tr>\n");
      }

      echo("            </table>\n");
      if($_GET['imprimir'] == 0){
        echo("            <br />\n");
      }
      echo("            <div class=\"divRichText\" id=\"text_".$cod_usuario_ficha."\">\n");
      							//echo($campo);
      if ($perfil_existe) /* se o perfil existe */
      {
        $perfil=$linha['perfil'];
        echo("              ".$perfil);
      }
      else
      {
        /* 26 - O perfil de */ /* 27 - ainda n� est�dispon�el */
        echo("              ".RetornaFraseDaLista($lista_frases,26)." ".$nome." ".RetornaFraseDaLista($lista_frases,27)."\n");
      }
      //echo("				</textarea>");
      echo("            </div>\n");
      /* 18 - Ok */
      //echo("            	<input type=\"button\" class=\"input\" id=\"OKComent\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" onclick=\"EdicaoTexto('".$cod_usuario_ficha."','msg_corpo', 'ok')\" style=\"display:none;margin-bottom:5px;\" />\n");
      /* 2 - Cancelar */
      //echo("            	<input type=\"button\" class=\"input\" id=\"cancComent\" onclick=\"EdicaoTexto('$cod_usuario_ficha','msg_corpo','canc')\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" style=\"display:none;margin-bottom:5px;\" />\n");
      if($_GET['imprimir'] == 0){
      	echo("            <br /><br />\n");
      }
      echo("            <hr />\n");
      echo("            <div align=\"right\">\n");
      echo("            </div>\n");
    }
    echo("          </form>\n");
  }
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
