<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/correio/compor.php

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
  ARQUIVO : cursos/aplic/correio/compor.php
  ========================================================== */

/* =========================================================
 * Flags definidas:
 * Variavel $acao:
 * $acao = NULL: compor nova mensagem
 * $acao = 1: Responder
 * $acao = 2: Responder para todos os destinatarios
 * $acao = 3: Redirecionar
   =========================================================*/

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("correio.inc");

  $cod_ferramenta = 11;
  include("../topo_tela.php");

  Desconectar($sock);

  $sock = Conectar("");

  $diretorio_arq=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  $dir_arq=$diretorio_arq."/".$cod_curso."/correio/".$cod_msg;

  $cod_usuario_temp=$cod_usuario;
  if ($cod_usuario_temp<0)
    $cod_usuario_temp=0;

  $dir_temp=$diretorio_temp."/correio_".$cod_curso."_exib_".$cod_usuario_temp;
  $link_temp="../../diretorio/correio_".$cod_curso."_exib_".$cod_usuario_temp;

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario, true);

  $eformador = EFormador($sock, $cod_curso, $cod_usuario);

  $codFormadores    = RetornaCodigoFormadoresDoCurso($sock, $cod_curso);
  $codAlunos        = RetornaCodigoAlunosDoCurso($sock, $cod_curso);
  $codColaboradores = RetornaCodigoColaboradoresDoCurso($sock, $cod_curso);
  $codGrupos        = RetornaCodigoGruposDoCurso($sock);

  $codMsgAnt = $_GET['cod_msg_ant'];
  $acao = $_GET['acao'];
  
  if($codMsgAnt){
    $linha=RetornaInfosMensagem($sock,$codMsgAnt);
    $codUsuarioAutorAnt = $linha['cod_usuario'];
    $nomeAutorAnt = RetornaNomeUsuarioDeCodigo($sock, $codUsuarioAutorAnt, $cod_curso);
    $mensagem = $linha['mensagem'];
    $assunto = $linha['assunto'];
    $dataAnt = UnixTime2DataHora($linha['data']);
  }


/* **************** inicio funcoes javascript ***************************** */
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  echo("    <script type=\"text/javascript\">\n\n");
  echo("    var selec = '".$_GET['selec']."';\n");

  /* ************************************************************************
   * getfilename -  tira o caminho do arquivo retornando soh o arquivo
   * Entrada: path - caminho do arquivo
   * Saida: file - nome do arquivo
   */
  echo("      function getfilename(path) {\n");
  echo("        var pieces=path.split('\\\\');\n");
  echo("        var n=pieces.length;\n");
  echo("        var file=pieces[n-1];\n");
  echo("        pieces=file.split('/');\n");
  echo("        n=pieces.length;\n");
  echo("        file=pieces[n-1];\n");
  echo("        return(file);\n");
  echo("       }");

  /* ************************************************************************
   * MarcaOuDesmarcaTodos - 
   */
  echo("      function MarcaOuDesmarcaTodos(tipoUser){\n");
  echo("        var i;\n");
  echo("        var flag;\n");
  echo("        var tipoSelect = \"chkTodos\"+tipoUser;\n");
  echo("        marcados = document.getElementById(tipoSelect).checked;\n");
  echo("        var arrayUsers = document.getElementsByName('chk'+tipoUser+'[]');\n");
  echo("        for(i = 0; i < arrayUsers.length; i++){\n");
  echo("          arrayUsers[i].checked = marcados;\n");
  echo("        }\n");
  echo("      }\n");

  echo("      function OpenWindowPerfil(id){\n");
  echo("        window.open(\"../perfil/exibir_perfis.php?");
  echo("cod_curso=".$cod_curso."&cod_aluno[]=\" + id, \"PerfilDisplay\",\"width=600,height=400,");
  echo("top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("      }\n\n");

  echo("      function OpenWindowGrupo(id){\n");
  echo("        window.open(\"../grupos/exibir_grupo.php?");
  echo("cod_curso=".$cod_curso."&cod_grupo=\" + id, \"GrupoDisplay\",\"width=600,height=400,");
  echo("top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("      }\n\n");

  echo("      function ControlaChkTodos(tipoUser, chkClicado){\n");
  echo("        var i;\n");
  echo("        var arrayUsers = document.getElementsByName('chk'+tipoUser+'[]');\n");
  echo("        var chkTodos = document.getElementById('chkTodos'+tipoUser) \n");
  echo("        if(!chkTodos) return;\n");
  echo("        if((chkClicado) && (chkClicado.checked==false)){\n");
  echo("          chkTodos.checked = false;\n");
  echo("        }else{\n");
  echo("          for(i = 0; i < arrayUsers.length; i++){\n");
  echo("            if(arrayUsers[i].checked == false){\n");
  echo("              flag = false;\n");
  echo("              break;\n");
  echo("            }else\n");
  echo("              flag = true;\n");
  echo("          }\n");
  echo("          chkTodos.checked = flag;\n");
  echo("        }\n");
  echo("      }\n");

  echo("      function MostraEscondeUsers(tipoUser){\n");
  echo("        if(document.getElementById('mostra'+tipoUser).getAttribute('ver') == 'sim' ){\n");
  echo("          document.getElementById('mostra'+tipoUser).innerHTML = '[ + ]'\n");
  echo("          document.getElementById('mostra'+tipoUser).setAttribute('ver', 'nao') \n");
  echo("          document.getElementById('ulUser'+tipoUser).style.display='none';\n");
  echo("        }else{\n");
  echo("          document.getElementById('mostra'+tipoUser).innerHTML = '[ - ]'\n");
  echo("          document.getElementById('mostra'+tipoUser).setAttribute('ver', 'sim')\n");
  echo("          document.getElementById('ulUser'+tipoUser).style.display='';\n");
  echo("        }\n");
  echo("      }\n");

  echo("      var many_arqs=0;\n\n");

  echo("      function removeInputFile(numero){\n");
  echo("        elementoDiv = document.getElementById('arquivos');\n");
  echo("        elementoDiv.removeChild(document.getElementById('br_'+numero));\n");
  echo("        elementoDiv.removeChild(document.getElementById('remover_arquivo_'+numero));\n");
  echo("        elementoDiv.removeChild(document.getElementById('space_'+numero));\n");
  echo("        elementoDiv.removeChild(document.getElementById('input_file_'+numero));\n");
  echo("      }\n\n");

  echo("      function ArquivoValido(file)\n");
  echo("      {\n");
                /* Usando expressao regular para identificar caracteres invalidos */
  echo("        var vet  = file.match(/^[A-Za-z0-9-\.\_\ ]+/);\n");
  echo("        if ((file.length == 0) || (vet == null) || (file.length != vet[0].length))\n");
  echo("          return false;\n");
  echo("        return true;\n");
  echo("      }\n");

  /* ************************************************************************
   * EdicaoArq - funcao chamada ao selecionar um arq anexo
   *       		 Remove o arquivo selecionado caso o mesmo seja invalido e da um alert
   * Entrada:
   * 	id_name: input_fie_
   * 	id_num: num de identificacao do arquivo anexo (0,1,2,...)
   * 
   * Frase 141 (ferramenta 11):
   */
  echo("    function EdicaoArq(id_name, id_num){\n");
  echo("      var num = id_num+'';\n");
  echo("      var nomeArq = getfilename(document.getElementById(id_name+num).value);\n");
  echo("      if (ArquivoValido(nomeArq) == false){\n");
  echo("        alert('".RetornaFraseDaLista($lista_frases, 141)."');\n");
  echo("        removeInputFile(id_num);\n");
  echo("      }\n");
  echo("    }\n\n");

  /* ************************************************************************
   * addInputFile - Cria os elementos de anexar arquivos (input) e remove o link "Anexar Arquivo"
   *                Chamada ao clicar no link "Anexar Arquivo"
   */
  echo("      function addInputFile(){\n");
  echo("        var num = many_arqs;\n");
  echo("        elementoDiv = document.getElementById('arquivos');\n");
  echo("        while(elementoDiv.lastChild.tagName!=\"IMG\")\n");
  echo("          elementoDiv.removeChild(elementoDiv.lastChild);\n"); //remove "anexar arquivo"
  echo("        elementoDiv.removeChild(elementoDiv.lastChild);\n"); // remove o clipes
                /* inputFile: caixa de selecao do arquivo anexo*/
  echo("        inputFile=document.createElement('input');\n");
  echo("        inputFile.setAttribute(\"type\", \"file\");\n");
  echo("        inputFile.setAttribute(\"size\", \"40\");\n");
  echo("        inputFile.setAttribute(\"name\", \"input_files[]\");\n");
  echo("        inputFile.onchange = function() { EdicaoArq('input_file_', num); };\n");
  echo("        inputFile.setAttribute(\"id\", \"input_file_\"+many_arqs);\n");
  echo("        inputFile.setAttribute(\"style\", \"border:2px solid #9bc; margin-left:65px;\");\n\n");
                /* Espaco */
  echo("        createSpace=document.createElement('span');\n");
  echo("        createSpace.setAttribute(\"id\", \"space_\"+many_arqs);\n");
  echo("        createSpace.innerHTML=\"&nbsp;&nbsp;&nbsp;\"\n");
                /* Span: botao Remover arquivo anexo
                 * Frase 132 (ferramenta 11): Remover */
  echo("        createSpan = document.createElement('span');\n");
  echo("        createSpan.onclick = function() { removeInputFile(num); };\n");
  echo("        createSpan.setAttribute(\"id\", \"remover_arquivo_\"+many_arqs);\n");
  echo("        createSpan.className=\"link\";\n");
  echo("        createSpan.innerHTML=\"".RetornaFraseDaLista($lista_frases,132)."\";\n\n");
                /* Pula linha*/
  echo("        createBr = document.createElement('br');\n");
  echo("        createBr.setAttribute(\"id\", \"br_\"+many_arqs);\n\n");
                /* Imgaem do clips*/
  echo("        createImg = document.createElement('img');\n");
  echo("        createImg.setAttribute(\"src\", \"../imgs/paperclip.gif\");\n");
  echo("        createImg.setAttribute(\"border\", \"0\");\n");
  echo("        createImg.setAttribute(\"style\", \"margin-left:65px;\");\n\n");
                /* Link Anexar Arquivo
                 * Frase 85 (ferramenta 11): Anexar arquivo */
  echo("        createSpan2 = document.createElement('span');\n");
  echo("        createSpan2.className=\"link\";\n");
  echo("        createSpan2.onclick = function (){ addInputFile(); };\n");
  echo("        createSpan2.setAttribute(\"id\", \"anexar_arquivo\");\n"); //removi sem querer?!
  echo("        createSpan2.innerHTML=\"".RetornaFraseDaLista($lista_frases,85)."\";\n\n");
                /* Adiciona novos elementos como ultimos filhos (no final): */
  echo("        elementoDiv.appendChild(inputFile);\n");
  echo("        elementoDiv.appendChild(createSpace);\n");
  echo("        elementoDiv.appendChild(createSpan);\n");
  echo("        elementoDiv.appendChild(createBr);\n");
  echo("        elementoDiv.appendChild(createImg);\n\n");
  echo("        elementoDiv.appendChild(createSpan2);\n\n");
  echo("        many_arqs++;\n");
  echo("      }\n\n");

  /* ************************************************************************
   * submitForm - Verifica se o campo assunto e destinatarios estao ok,
   *              remove os campos dos arquivos anexos.
   */
  echo("      function submitForm(){\n");
                /* Se o assunto esta vazio, avisa ao usuario
                 * Frase 43 (ferramenta 11): Voce n�o informou o assunto da correspondencia. */
  echo("        if(document.getElementById('assunto').value.replace(\" \", \"\")  == ''){\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases,43)."');\n");
  echo("          document.getElementById('assunto').focus();\n");
  echo("          return false;\n");
  echo("        }\n");
 				/* Verifica se ha algum destinatario selecionado, caso contrario, informa ao usuario
   				* Frase 45 (ferramenta 11): Voce nao informou quem devera receber a correspondencia. */
  echo("        destOk = false;\n");
  echo("        tiposUsers = new Array();\n");
  echo("        tiposUsers = {0:'F', 1:'A', 2:'C', 3:'G'};\n");
  echo("        for(k = 0; k < 4; k++){\n");
  echo("          var arrayUsers = document.getElementsByName('chk'+tiposUsers[k]+'[]');\n");
  echo("          for(i = 0; i < arrayUsers.length; i++){\n");
  echo("            if(arrayUsers[i].checked){ destOk = true; break; }\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if(!destOk){\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases,45)."');\n");
  echo("          return false;\n");
  echo("        }\n");
				/* Arquivos anexos:
				 * Para cada arquivo em anexo diferente de vazio
				 * Remove respectivamente os componentes:
				 * 1. espaco o botao "Browse" e "Remover"
				 * 2. link "Remover"
				 * 3. o enter
				 * 4. a caixa com o end do arquivo e o botao "Browse" */ 
  echo("        element=document.getElementsByName('input_files[]');\n");
  echo("        for (i=0; i<element.length; i++){\n");
  echo("          if((element[i].value)==\"\"){\n");
  echo("            document.getElementById('arquivos').removeChild(element[i].nextSibling);\n");
  echo("            document.getElementById('arquivos').removeChild(element[i].nextSibling);\n");
  echo("            document.getElementById('arquivos').removeChild(element[i].nextSibling);\n");
  echo("            document.getElementById('arquivos').removeChild(element[i]);\n");
  echo("            i--;\n");
  echo("          }\n");
  echo("        }\n");
  /**/
  echo("        updateRTE('msg_corpo');\n");
  echo("        return true;\n");
  echo("      }\n");

  /* Se trata-se da resposta de uma mensagem ou de redirecionamento, exite a opcao de CancelarMensagem
   * voltando para a tela da mensagem anterior */
  if(isset($acao) && (($acao==1) || ($acao==2) || ($acao==3))){
    echo("      function CancelarMensagem(){\n");
    if(isset($_GET['selec'])){
      echo("        if(selec == 'yes'){\n");
      echo("        window_handle = window.open('exibir_mensagem.php?".RetornaSessionID().
"&cod_msg=".$_GET['cod_msg_ant']."&cod_curso=".$cod_curso."&modoVisualizacao=".$modoVisualizacao."','mensagem','width=700,height=900,top=100,left=100,scrollbars=yes,status=no,toolbar=no,menubar=no,resizable=no');\n");
  echo("        window_handle.opener = self;\n");
  echo("        window_handle.focus();\n");
      echo("        }return;\n");
    }
    echo("          document.location = document.referrer;\n");
    echo("      }\n\n");
  }

  echo("      function Iniciar(){\n");
  echo("        this.focus();\n");
  echo("        window.resizeTo(705,700);\n");
  echo("        tiposUsers = {0:'F', 1:'A', 2:'C', 3:'G'};\n");
  echo("        for(k = 0; k < 4; k++){\n");
  echo("          ControlaChkTodos(tiposUsers[k], false);\n");
  echo("        }\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("    </script>\n");
/* ******************** fim das funcoes javasript************************** */
  
  echo("  </head>\n");
  echo("  <body onload=\"Iniciar();\"><br />\n");
  echo("    <table width=\"670\" border=0>\n");
  echo("      <tr>\n");
  echo("        <td width=\"100%\" valign=\"top\">\n");
  /* 1 - Correio */
  $cabecalho = RetornaFraseDaLista($lista_frases,1);

  /* 2 - Compondo mensagem */
  $cabecalho .= "<b> - ".RetornaFraseDaLista($lista_frases,2)."</b>";
  echo("          <h4>".$cabecalho."</h4>\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  echo("      <tr>\n");
  echo("        <td width=\"100%\">\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  echo("      <tr>\n");
  echo("        <td width=\"100%\">\n");
  echo("          <form enctype=\"multipart/form-data\" name=\"enviarMsg\" id=\"enviarMsg\" method=\"post\" action=\"compor2.php\" onsubmit=\"return(submitForm());\">\n");
  echo("            <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("            <input type=\"hidden\" name=\"codMsgAnt\" value=\"".$codMsgAnt."\" />\n");
  echo("            <input type=\"hidden\" name=\"acao\"      value=\"".$acao."\" />\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");

  echo("              <tr>\n");
  echo("                <td width=\"100%\">\n");
  echo("                  <ul class=\"btAuxTabs\">\n");
  /* 13- Fechar */
  echo("                    <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13). "</span></li>\n");

  echo("                  </ul>\n");
  echo("                </td>\n");
  echo("              </tr>\n");

  echo("              <tr>\n");
  echo("                <td valign=\"top\">\n");
  echo("                  <table border=0 width=\"100%\" cellspacing=\"0\" style=\"cellspadding:0pt\" class=\"tabInterna\" id=\"tabelaMostraMsgs\">\n");

  echo("                    <tr>\n");
  echo("                      <td class=\"alRight\" valign=\"top\">\n");
  /* 27 - Destinatario */
  echo("                      ".RetornaFraseDaLista($lista_frases, 27)."\n");
  echo("                      </td>\n");
  echo("                      <td class=\"alLeft\">\n");
  include("listadest.php");
  echo("                      </td>\n");

  echo("                    </tr>\n");

  echo("                    <tr class=\"altColor0\">\n");
  echo("                      <td class=\"alRight\">\n");
      /* 20 - Assunto */
  echo("                        ".RetornaFraseDaLista($lista_frases,20)."\n");
  echo("                      </td>\n");
  echo("                      <td class=\"text alLeft\">\n");

  $prefixo = "";
  if(($acao == 1) || ($acao == 2))
    $prefixo = "Resp: ";
  else if($acao == 3)
    $prefixo =  "Red: ";

  echo("                        <input type=\"text\" id=\"assunto\" name=\"assunto\" class=\"input\" value=\"". $prefixo . $assunto."\" size=\"40\" maxlength=\"100\" />\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");

  echo("                    <tr>\n");
  echo("                      <td align=left colspan=4>\n");

  echo("                        <div id=\"arquivos\">\n");

  // Redirecionar
  if ($acao==3){ 
    Desconectar($sock);
    $sock=Conectar("");
    $diretorio_arq=RetornaDiretorio($sock,'Arquivos'); //SELECT `diretorio` FROM `Diretorio` WHERE `item`='Arquivos'
    $dir_arq_ant=$diretorio_arq."/".$cod_curso."/correio/".$cod_msg_ant."/";
    $dir_arq=$diretorio_arq."/".$cod_curso."/correio/".$cod_msg;
    $lista_arq=RetornaArrayDiretorio($dir_arq_ant);
    Desconectar($sock);
    $sock=Conectar($cod_curso);
  }

	/* Caso a mensagem seja uma resposta ou um redirecionamento, mostra os arquivos ja anexados na msgm anterior
	 * 	$dir_arq_ant = diretorio onde estao os arquivos anexados na msgm anterior
	 * 	$dir_arq_ant = [diretorio_onde_ficam_os_arquivos]/[cod_curso]/correio/[cod_msg_ant]/
	 * 	$listaArq = array com os arquivos anexados na mensagem anterior*/
    $listaArq = RetornaArrayDiretorio($dir_arq_ant);
    if(count($listaArq) > 0){
      $countArq = 0;
      foreach($listaArq as $cod => $linha){
        echo("                      <input type=\"checkbox\" id=\"chkArqAnexo\" name=\"chkArqAnexo[]\" value=".$linha['Caminho']." checked=\"checked\" style=\"margin-left:65px;\" /><a   href=".$link_temp ."/".ConverteURL2HTML($linha['Arquivo'])." target=blank> ".$linha['Arquivo']." </a><br />\n");
      }
    }

  /*95 - Anexar Arquivo */
  echo("                        <img alt=\"\" src=\"../imgs/paperclip.gif\" border=0 style=\"margin-left:65px;\"  /><span id=\"anexar_arquivo\" onclick=\"addInputFile();\" class=\"link\">".RetornaFraseDaLista($lista_frases,85)."</span>\n");
  echo("                        </div>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");

  echo("                    <tr>\n");
  echo("                      <td colspan=\"2\">\n");
  echo("                        <script type=\"text/javascript\">\n");
  echo("                          writeRichText('msg_corpo', '".VerificaStringQuery(Enter2Br($mensagem))."', 610, 200, true, false);\n");
  echo("                        </script>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");

  if($eformador){
    echo("                    <tr class=\"head01\">\n");
    echo("                      <td colspan=\"2\" class=\"alLeft\">\n");
    echo("                        <input type=\"checkbox\" name=\"msgExterna\" id=\"msgExterna\" value=1 /> <label for=\"msgExterna\"> ".RetornaFraseDaLista($lista_frases,112)."</label>\n");
    echo("                      </td>\n");
    echo("                    </tr>\n");
  }
  
  echo("                  </table>\n");/* fecha tabInterna */
  echo("                </td>\n");
  echo("              </tr>\n");

  echo("              <tr>\n");
  echo("                <td style=\"text-align:right;\">\n");
  if(isset($acao) && (($acao==1) || ($acao==2) || ($acao==3))){
    /* 2 - Cancelar */
    echo("                    <input type=\"button\" class=\"input\" onclick=\"CancelarMensagem();\" value=\"".RetornaFraseDaLista($lista_frases_geral, 2)."\" />\n");
  }
  /* 39 - Enviar Mensagem */
  echo("                    <input type=\"submit\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases, 39)."\" />\n");

  echo("                </td>\n");
  echo("              </tr>\n");
  echo("            </table>\n");/*fecha tabela tabExtern*/
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");/*fecha tabela geral*/
  echo("  </body>\n");
  echo("</html>\n");

?>
