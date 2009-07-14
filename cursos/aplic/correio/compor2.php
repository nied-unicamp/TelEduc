 <?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic//compor2.php

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
  ARQUIVO : cursos/aplic/correio/compor2.php
  ========================================================== */

/* C�digo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");

  include("correio.inc");

  /* mime_lookup utiliza a fun��o RetornaDiretorio declarada em */
  /* correio.inc                                                */
  include("mime_lookup.inc");

  $cod_ferramenta = 11;
  include("../topo_tela.php");

  Desconectar($sock);

  $cod_curso  = $_POST['cod_curso'];
  $codMsgAnt  = $_POST['codMsgAnt'];
  $acao       = $_POST['acao'];
  $assunto    = $_POST['assunto'];
  $msgCorpo   = $_POST['msg_corpo'];
  $chkF       = $_POST['chkF'];
  $chkA       = $_POST['chkA'];
  $chkG       = $_POST['chkG'];
  $chkC       = $_POST['chkC'];
  $chkArqAnexo = $_POST['chkArqAnexo'];
  $arquivosAnexos = $_FILES['input_files'];
  $nomesArquivosAnexos = $_POST['input_files'];
  $msgExterna = $_POST['msgExterna'];


   /* Obtem o nome do emissor da mensagem. */
  $sock=Conectar("");

  $diretorio_arq=RetornaDiretorio($sock,'Arquivos');
  $dir_curso=$diretorio_arq."/".$cod_curso;

  if ($msgExterna == 1){

    /* Obt�m o hostname para ser colocado no dom�nio do rementente da mensagem */
    /* do sendmail: remetente@host.                                            */
    $host = RetornaConfig($sock, "host");
    $raiz_www = RetornaDiretorio($sock, "raiz_www");

    // $endere�o ser� utilizado para o rodap� do e-mail que notifica o
    // usu�rio de que ele dever� responder a mensagem a partir da ferramenta
    // Correio do TelEduc (endere�o para acesso direto ao TelEduc).
    $endereco = $host.$raiz_www;

    $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

    if (!ExisteArquivo($diretorio_temp."/tmp/".$cod_curso))
      CriaDiretorio($diretorio_temp."/tmp/".$cod_curso);

    $dir_temp=$diretorio_temp."/correio_".$cod_curso."_comp_".$cod_usuario;

    $dir_temp_msg=$diretorio_temp."/tmp/".$cod_curso."/extmsg";
  }


  Desconectar($sock);

  $sock=Conectar($cod_curso);

  ExpulsaConvidadoPassivo($sock, $cod_usuario, $cod_usuario, $lista_frases);

  $userFormador = EFormador($sock,$cod_curso,$cod_usuario);

  $data = time();

  $codMsg = RetornaProximoCodigo($sock, "Correio_mensagens");
  $dirArq=$diretorio_arq."/".$cod_curso."/correio/".$codMsg;


  $msgCorpo = VerificaStringQuery($msgCorpo);
  $assunto = VerificaStringQuery($assunto);
  
  InsereMsgCM($sock, $codMsg, $cod_usuario, $assunto, $data, $msgCorpo, $codMsgAnt);

  $todosUsuarios = RetornaDadosTodosUsuarios($sock,$cod_curso);
  $contDest = 0;
  $destsCorreio;

  if($chkF){
    $numF = count($chkF);
    foreach($chkF as $inf){
      $user = RetornaDadosUsuario($sock, $inf, $cod_curso);
      $destsCorreio[$contDest]['nome'] = $user['nome'];
      $destsCorreio[$contDest]['mail'] = $user['email'];
      $destsCorreio[$contDest]['cod_usuario'] = $inf;

      $listaCod[$contDest]['codDestino'] = $inf;
      $listaCod[$contDest]['categDestino'] = 'U';
      $contDest++;
    }
  }

  if($chkA){
    $numA = count($chkA);
    foreach($chkA as $inf){
      $user = RetornaDadosUsuario($sock, $inf, $cod_curso);
      $destsCorreio[$contDest]['nome'] = $user['nome'];
      $destsCorreio[$contDest]['mail'] = $user['email'];
      $destsCorreio[$contDest]['cod_usuario'] = $inf;

      $listaCod[$contDest]['codDestino'] = $inf;
      $listaCod[$contDest]['categDestino'] = 'U';
      $contDest++;
    }
  }

  if($chkC){
    $numC = count($chkC);
    foreach($chkC as $inf){
      $user = RetornaDadosUsuario($sock, $inf, $cod_curso);
      $destsCorreio[$contDest]['nome'] = $user['nome'];
      $destsCorreio[$contDest]['mail'] = $user['email'];
      $destsCorreio[$contDest]['cod_usuario'] = $inf;

      $listaCod[$contDest]['codDestino'] = $inf;
      $listaCod[$contDest]['categDestino'] = 'U';
      $contDest++;  
    }
  }

  if($chkG){
    $numG = count($chkG); 
    $aux =  $contDest;    
    foreach($chkG as $inf){

    $listaCod[$contDest]['codDestino'] = $inf;
    $listaCod[$contDest]['categDestino'] = 'g';
    }
  }

  InsereMsgCorreioListaDestinos($sock, $codMsg, $destsCorreio, $contDest, $cod_usuario, $listaCod, $cod_curso);

  if((is_array($_FILES) && (count($_FILES) != 0)) || ($_FILES != NULL) || ($chkArqAnexo != NULL)){
    AnexarArquivos($dir_curso, $dirArq, $arquivosAnexos, $chkArqAnexo);
  }

/* Se o usuário selecionou a opção de envio para e-mail externo. */
  if (($userFormador) && ($msgExterna == 1)){

    if (ExisteArquivo($dirArq)){
      if (!ExisteArquivo($dir_temp))
        CriaLinkSimbolico($dirArq,$dir_temp);
    }
    if (!ExisteArquivo($dir_temp_msg)){
      CriaDiretorio($dir_temp_msg);
    }

    for($i = 0; $i < count($destsCorreio); $i++){
      $vetorNomeAux[$i] = $destsCorreio[$i]['nome'];
      $vetorMailAux[$i] = $destsCorreio[$i]['mail'];
    }

    $mnomes = implode(',', $vetorNomeAux);
    $memail = implode(',', $vetorMailAux);

    /* Obt�m os arquivos contidos na pasta. */

    $lista_arq=RetornaArrayDiretorio($dir_temp);

    /* Cria uma lista de arquivos v�lidos. */
    if (count($lista_arq) > 0){
      $j = 0;
      foreach($lista_arq as $cod => $linha){
        if ($linha['Arquivo']!=""){
          $validfiles[$j]['Caminho'] = $linha['Caminho'];
          $validfiles[$j]['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
          $j++;
        }
      }
    }


 /* Limpa as barras indesejadas. */
    /* Ja foi verificado com o VerificaString */
    $assunto = stripslashes($assunto);
    
    /* Elimina \r\n do Conteudo */
    $msgCorpo = str_replace("\\r\\n", "", $msgCorpo);
    $msgCorpo = stripslashes($msgCorpo);


    $remetente = NomeUsuario($sock, $cod_usuario);
    /* Obtem o nome do curso. */
    $nome_curso = NomeCurso($sock,$cod_curso);

    $conteudo_email = $msgCorpo;
    $conteudo_email .= "<font size=\"2\">\n";

    if ((isset($validfiles)) && (count($validfiles) > 0)){
      $conteudo_email .= "<p style=\"margin-top:30px\">\n";
      /* 113 - Arquivos anexados */
      $conteudo_email .= "<strong>".RetornaFraseDaLista($lista_frases,113).":</strong> ";
      foreach($validfiles as $idx => $tupla){
        if($idx > 0) $conteudo_email .= ", ";
          $conteudo_email .= $tupla['Arquivo'];        
      }
      $conteudo_email .= "</p>\n";
    }else{
      $validfiles= array();
    }

    /* Separador para o rodap� da mensagem */
    $conteudo_email .= "<hr style=\"margin: 30px 0 30px 0;\" />";

    $conteudo_email .= "<p>".RetornaFraseDaLista($lista_frases,120)."</p>";

    /* 121 - O acesso deve ser feito a partir do endereco: */
    $conteudo_email .= "<p>".RetornaFraseDaLista($lista_frases,121)."</p>\n";

    $conteudo_email .="<p><a href=\"http://".$endereco."/cursos/aplic/index.php?cod_curso=".$cod_curso."\">http://".$endereco."/cursos/aplic/index.php?cod_curso=".$cod_curso."</a></p><br />\n";
    $conteudo_email .= "</font>\n";

    /* Arquivo de mensagem do sendmail. */
    $nomearquivo = $dir_temp_msg."/sendmail_".$codMsg.".tel".$cod_usuario;


    /* INFORMA��ES PARA O CABE�ALHO (REMETENTE, ASSUNTO) DA MENSAGEM DO SENDMAIL */
    $informacoes['nome'] = $remetente;
    $informacoes['host'] = $host;
    /* 115 - NAO_RESPONDA */
    $informacoes['remetente'] = RetornaFraseDaLista($lista_frases, 115);
    $informacoes['nome_curso'] = $nome_curso;
    /* 1 - Correio */
    $informacoes['nome_ferramenta'] = RetornaFraseDaLista($lista_frases, 1);

    Desconectar($sock);
    $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso, $conteudo_email, $assunto, $cod_usuario, $mnomes); 
    $sock = Conectar($cod_curso);

    if (CriaArquivoSendmail($informacoes, $codMsg, $nomearquivo,
                          $mensagem_envio, $formato, $validfiles) == true){
      MandaMsg("",$memail,"","",$nomearquivo);
    }else{
      /* 114 - N�o foi poss�vel enviar uma c�pia para os e-mail's externos. Por favor entre em contato com o administrador do seu ambiente. */
    echo(RetornaFraseDaLista($lista_frases,114)."<br />\n");
    }

    if (ExisteArquivo($dirArq))
    {
      RemoveArquivo($dir_temp);
    }
    if (ExisteArquivo($dir_temp_msg))
    {
      if ((isset($nomearquivo)) && (ExisteArquivo($nomearquivo)))
        RemoveArquivo($nomearquivo);
      RemoveDiretorio($dir_temp_msg);
    }

  }

  echo("    <script type=text/javascript>\n");
  echo("      function esperaEFecha(){\n");
  echo("       window.setTimeout(\"this.close()\", 2000);\n");
  echo("      }\n");
  echo("    </script>\n");

  echo("  </head>\n");
  echo("  <body onload=\"esperaEFecha()\"><br />\n");
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
  echo("        <td width=\"100%\" style=\"text-align:center\"> \n");
  /* 47 - Mensagem enviada com sucesso. */
  echo("          <p style=\"margin-top:50px; font-weight:bold;\">".RetornaFraseDaLista($lista_frases,47)."</p>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");

  Desconectar($sock);
  echo("  </body>\n");
  echo("</html>\n");

?>