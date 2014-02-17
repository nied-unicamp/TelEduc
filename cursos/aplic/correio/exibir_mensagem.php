<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/correio/exibir_mensagem.php

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
  ARQUIVO : cursos/aplic/correio/exibir_mensagem.php
  ========================================================== */

/* C�digo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("correio.inc");

  $cod_ferramenta = 11;
  include("../topo_tela.php");
  $modoVisualizacao = $_GET['modoVisualizacao'];

  Desconectar($sock);
  $sock=Conectar("");

  $diretorio_arq=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  $dir_arq=$diretorio_arq."/".$cod_curso."/correio/".$cod_msg;

  $cod_usuario_temp=$cod_usuario;
  if ($cod_usuario_temp<0)
    $cod_usuario_temp=0;

  $dir_temp=$diretorio_temp."/correio_".$cod_curso."_exib_".$cod_usuario_temp;
  $link_temp="../../diretorio/correio_".$cod_curso."_exib_".$cod_usuario_temp;

  if (ExisteArquivo($dir_temp))
    RemoveArquivo($dir_temp);

  $tem_arquivos=false;

  if (ExisteArquivo($dir_arq))
  {
    CriaLinkSimbolico($dir_arq,$dir_temp);
    $tem_arquivos=true;
  }

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario, true);

  if (! is_numeric ($cod_msg) || (!RemetenteMensagem($sock, $cod_msg, $cod_usuario) &&  !DestinatarioMensagem($sock, $cod_msg, $cod_usuario)))
  {
    echo("    <script type=\"text/javascript\">\n");
    /* 119 - Ocorreu um erro ao tentar acessar a mensagem */
    echo("      alert('".RetornaFraseDaLista($lista_frases, 119)."');\n");
    echo("      self.close();\n");
    echo("    </script>\n");

    Desconectar ($sock);
    exit();
  }

  $listaDest=ListaCompletaDestinatarios($sock,$cod_msg,$lista_frases, $cod_curso);
  $numDest = count($listaDest);
  if ($numDest > 6){
    $tamY = 625;
    $tamQuadroDest = '80px';
  }else{
    $tamY = (495 + (13 * $numDest));
  }
  if($tem_arquivos) $tamY += 20;

  $linha=RetornaInfosMensagem($sock,$cod_msg);
  $estadoMsg = RetornaEstadoDaMensagem($sock,$modoVisualizacao, $cod_msg, $cod_usuario);

//   if($modoVisualizacao == 'R' && $estadoMsg == 'N')
//     TrocaEstadoDaMensagemRecebidas($sock, 'L', $cod_msg, $cod_usuario);

  echo("    <script type=\"text/javascript\">\n");

  /* ************************************
       AbrePerfil - abre o perfil do usuario em nova janela
       entrada: cod_usuario - codigo do usuario
         do qual abrir o perfil
       saida: false - para nao mudar a pagina atual
  */
  echo("    function AbrePerfil(cod_usuario)\n");
  echo("    {\n");
  echo("      window.open('../perfil/exibir_perfis.php?&cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("      return(false);\n");
  echo("    }\n");

  echo("      function AbreGrupo(id){\n");
  echo("        window.open(\"../grupos/exibir_grupo.php?");
  echo("cod_curso=".$cod_curso."&cod_grupo=\" + id, \"GrupoDisplay\",\"width=600,height=400,");
  echo("top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("      }\n\n");

  echo("    function ImprimirRelatorio(){ \n");
  echo("      if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape')\n");
  echo("      {\n");
  echo("        self.print();\n");
  echo("      }\n");
  echo("      else\n");
  echo("      {\n");
  /* 51- Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
  echo("      }\n");
  echo("    }\n");

  echo("    function Resposta(acao,cod_msg){\n");
  echo("      window.open('compor.php?cod_curso=" .$cod_curso. "&cod_msg_ant='+cod_msg+'&acao='+acao, 'mensagem', 'top=50,left=100,scrollbars=yes, status=no,toolbar=no,menubar=no,resizable=yes');\n");
  echo("    }\n\n");

  if ($modoVisualizacao == 'L'){
    echo("    function ConfirmaExcluir()\n");
    echo("    {\n");
    /* 103 - Voce tem certeza de que deseja excluir definitivamente esta mensagem ? */
    echo("      if(confirm('".RetornaFraseDalista($lista_frases,103)."')){\n");
    echo("        document.getElementById('formApagar').acao.value=\"excluir\";\n");
    echo("        document.getElementById('formApagar').submit();\n");
    echo("      }\n");
    echo("    }\n\n");

    echo("    function ConfirmaRecuperar()\n");
    echo("    {\n");
    /* 104 - Voce tem certeza de que deseja recuperar esta mensagem ? */
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases,104)."')){\n");
    echo("        document.getElementById('formApagar').acao.value=\"recuperar\";\n");
    echo("        document.getElementById('formApagar').submit();\n");
    echo("      }\n");
    echo("    }\n\n");
  }
  else
  {
    echo("    function ConfirmaApagar()\n");
    echo("    {\n");
    /* 106 - Voce tem certeza de que deseja mover esta mensagem para a Lixeira? */
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases,106)."')){;\n");
    echo("        document.getElementById('formApagar').submit()};\n");
    echo("    }\n\n");
  }
  echo("      function Iniciar(){\n");
  echo("        var tamY =".$tamY.";\n");
  echo("        if(tamY > window.screen.availHeight){\n");
  echo("          tamY = window.screen.availHeight;\n");
  echo("        }\n");
  if($tem_arquivos == 1){
    echo("        tamY = tamY+5;\n");
  }
  echo("        window.resizeTo(700, tamY);\n");
  echo("        startList();\n");
  echo("      }\n");

   echo("    </script>\n");

  echo("  </head>\n");

  echo("  <body style=\"width:1500\" onLoad=\"Iniciar();\"><br />\n");
  echo("    <h3 style=\"margin-top:20px;\">".NomeCurso($sock,$cod_curso)."</h3>\n");
  echo("    <table style=name:tabelaGlobal class=\"tabelaGlobal\" id=\"tabelaGlobal\" width=\"670\" border=0>\n");
  echo("      <tr>\n");
  echo("        <td width=\"100%\" valign=\"top\">\n");
  /* 131 - Correio */
  $cabecalho = RetornaFraseDaLista($lista_frases,1);

  if ($modoVisualizacao == 'L'){
    /* 107 - Visualizando mensagem da Lixeira */
    $cabecalho .= "<b> - ".RetornaFraseDaLista($lista_frases,107)."</b>\n";
  }
  else{
    /* 67 - Visualizando mensagem */
    $cabecalho .= "<b> - ".RetornaFraseDaLista($lista_frases,67)."</b>\n";
  }
  echo("          <h4>".$cabecalho."</h4>\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  echo("      <tr>\n");
  echo("        <td>\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <form name=\"formApagar\" id=\"formApagar\" action=\"acoes.php\" method=\"post\">\n");
  echo("                  <input type=\"hidden\" name=\"cod_msg\"         value=\"".$cod_msg."\" />\n");
  echo("                  <input type=\"hidden\" name=\"acao\"            value=\"apagar\" />\n");
  echo("                  <input type=\"hidden\" name=\"cod_curso\"       value=\"".$cod_curso."\" />\n");
  echo("                  <input type=\"hidden\" name=\"status_mensagem\" value=\"".$modoVisualizacao."\" />\n");
  echo("                  <input type=\"hidden\" name=\"cod_usuario\"     value=\"".$cod_usuario."\" />\n");
  echo("                </form>\n");

  echo("                <ul class=\"btAuxTabs\">\n");
  if(!$SalvarEmArquivo)
  {
    /* 13 - Fechar */
    if($modoVisualizacao != 'L'){
      echo("                  <li><span onclick=\"opener.focus(); document.location='remove_link_simbolico.php?cod_curso=".$cod_curso."'\">".RetornaFraseDaLista($lista_frases_geral,13). "</span></li>\n");
     //     echo("                  <li><span onclick=\"opener.xajax_RemoveLinkSimbolico('".$cod_curso."', '".$cod_usuario."'); opener.focus();\">".RetornaFraseDaLista($lista_frases_geral,13). "</span></li>\n");
    }else{
      echo("                  <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13). "</span></li>\n");
  
      //     echo("                  <li><span onclick=\"opener.xajax_RemoveLinkSimbolico('".$cod_curso."', '".$cod_usuario."'); opener.focus();\">".RetornaFraseDaLista($lista_frases_geral,13). "</span></li>\n");
    }
    if($modoVisualizacao != 'L'){ 
      $status_curso=RetornaStatusCurso($sock,$cod_curso);
      if (!($status_curso=='E' && !EFormador($sock,$cod_curso,$cod_usuario))){

        /* 71 - Responder */
        echo("                    <li><span onclick=\"Resposta(1,".$cod_msg.");\">".RetornaFraseDaLista($lista_frases, 71)."</span></li>\n");
        /* 72 - Responder para todos os destinatários */
        echo("                    <li><span onclick=\"Resposta(2,".$cod_msg.");\">".RetornaFraseDaLista($lista_frases, 72)."</span></li>\n");
        /* 73 - Redirecionar */
        echo("                    <li><span onclick=\"Resposta(3,".$cod_msg.");\">".RetornaFraseDaLista($lista_frases, 73)."</span></li>\n");
        /* 12 - Excluir */
        echo("                    <li><span onclick=\"ConfirmaApagar();\">".RetornaFraseDaLista($lista_frases_geral, 12)."</span></li>\n");
      }
      /* 14 - Imprimir */
      echo("                  <li><span onclick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral, 14)."</span></li>\n");
    
      echo("                </ul>\n");
   }else{
    /* form com botoes para recuperar e excluir definitivo */
    $status_curso=RetornaStatusCurso($sock,$cod_curso);
    if (!($status_curso=='E' && !EFormador($sock,$cod_curso,$cod_usuario))){
      /* 101 - Recuperar */
      echo("                    <li><span onClick=\"ConfirmaRecuperar();\">". RetornaFraseDaLista($lista_frases, 101). "</span></li>\n");
      /* 102 - Excluir definitivamente */
      echo("                    <li><span onClick=\"ConfirmaExcluir();\">". RetornaFraseDaLista($lista_frases, 102). "</span></li>\n");
    }
   }
  }

  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");

  echo("                <table border=0 width=100% cellspacing=\"0\" style=\"cellspadding:0pt;\" class=\"tabInterna\" id=\"tabelaMostraMsgs\">\n");
  
  echo("                  <tr>\n");
  echo("                    <td class=\"alRight\" style=\"width:100\">\n");
  /* 23 - Remetente*/
  echo("                      ".RetornaFraseDaLista($lista_frases, 23)."\n");
  echo("                    </td>\n");
 
  echo("                    <td class=\"alLeft\">\n");
  echo("                      <a class=\"text\" href=\"#\" onClick=\"AbrePerfil(".$linha['cod_usuario'].");\">"); 
  echo("                      ".RetornaNomeUsuarioDeCodigo($sock,$linha['cod_usuario'], $cod_curso). "</a>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");

  echo("                  <tr>\n");
  echo("                    <td style=\"width:15%\" class=\"alRight\" valign=\"top\">\n");
  /* 27 - Destinatario*/
  echo("                      ".RetornaFraseDaLista($lista_frases, 27)."\n");
  echo("                    </td>\n");

  echo("                    <td class=\"alLeft\" style=\"height:30;overflow:auto\">\n");
  if($numDest > 6){
    echo("<div style=\"width:505;height:".$tamQuadroDest.";overflow:auto\">\n");
  }else{
  echo("<div>\n");}
  foreach ($listaDest as $k)
  {
    if ($SalvarEmArquivo || (false == $k ['status']) ){
      // Salvar em arquivo ou destinatario eh 'todos <alguma coisa>' ==> Sem link
      $link_abre  = "<font class=\"text\">";
      $link_fecha = "</font>";
    }
    else if ('g' == $k['status']){
        $link_abre  = "                     <a class=\"text\" href=\"#\" onclick=return(AbreGrupo(".$k['codigo']."))>";
        $link_fecha = "</a>";
    }
    else if ('u' == $k['status']){
      // usuario espec�fico ==> link para perfil
      $link_abre  = "                     <a class=\"text\" href=\"#\" onclick=return(AbrePerfil(".$k['codigo']."))>";
      $link_fecha = "</a>";
    }
    else{
      echo("<font class=\"text\" color=\"red\">Erro interno em exibir_varias_mensagens.php. Parametro inesperado </font><br />");
      die ();
    }

    echo($link_abre.$k['nome'].$link_fecha."<br />\n");
  }
  echo("</div>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  /* 24 - Data */
  echo("                    <td class=\"alRight\">". RetornaFraseDaLista($lista_frases, 24)."\n");
  echo("                    <td class=\"g1field alLeft\"><font class=\"text\">".UnixTime2DataHora($linha['data'])."</font></td>\n");
  echo("                  </tr>\n");

  echo("                  <tr>\n");
  echo("                    <td class=\"alRight\">\n");
      /* 20 - Assunto */
  echo("                      ".RetornaFraseDaLista($lista_frases,20)."\n");
  echo("                    </td>\n");
  echo("                    <td class=\"text alLeft\">\n");
  echo("                      <b>".$linha['assunto']."</b>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");  
  echo("                    <td colspan=\"2\" class=\"itens divRichText\">\n");
  echo(                         PreparaMensagemExibicao($linha['mensagem']));
  echo("                    </td>\n");
  echo("                  </tr>\n");

  /* Arquivos anexos: 
   * Se existem arquivos anexos (existe link pro arquivo no dir_temp)
   * Mostra o link pros anexos no fim da mensagem */
  if($tem_arquivos){
    echo("                  <tr>\n"); 
    echo("                    <td class=\"alLeft\" colspan=\"2\">\n");
    /* 100 - Arquivos anexos */
    echo("                      <b>". RetornaFraseDaLista($lista_frases,100).":</b>\n");
    $listaArq = RetornaArrayDiretorio($dir_temp);
    if(count($listaArq) > 0){
      foreach($listaArq as $cod => $linha){
        $linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
        if($cod == 0){
          echo("                      <a class=\"text\" href=".$link_temp ."/".ConverteURL2HTML($linha['Arquivo'])." target=\"blank\"> ".$linha['Arquivo']." </a>\n");
        }else{
          echo("                      | <a class=\"text\" href=".$link_temp ."/".ConverteURL2HTML($linha['Arquivo'])." target=\"blank\"> ".$linha['Arquivo']." </a>\n");
        }
      }
    }
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }
  
  echo("                </table>\n");//fim da tabela tabelaMostraMensagem

  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");//fim da tabela tabExterna
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");
?>