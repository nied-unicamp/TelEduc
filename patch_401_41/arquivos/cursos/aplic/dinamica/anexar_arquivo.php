<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/dinamica/anexar_arquivo.php

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
  ARQUIVO : cursos/aplic/dinamica/anexar_arquivo.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("dinamica.inc");

        session_register("texto_s");

  /* Recebe o conte�do texto para manter as modifica��es feitas nele. */
  $texto_s = $texto;

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,16);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,16);

  /* Verifica se a pessoa a editar � formador */
  if (!EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("<html>\n");
    /* 1 - Din�mica do Curso */
    echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=dinamica.css>\n");

    echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    /* 1 - Dinamica do Curso */
    /* 5 - �rea restrita ao formador. */
    $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
    $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,5)."</b><br>";
    echo($cabecalho);

    /* 23 - Voltar (gen) */
    echo("<form><input type=button value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");
    echo("</body></html>\n");
    Desconectar($sock);
    exit;
  }

  echo("<html>\n");
  /* 1 - Din�mica do Curso */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=dinamica.css>\n");

  $lista=RetornaArquivosDinamica($cod_curso,$diretorio_temp);

  /*********************
   * Trecho Javascript *
   *********************/
  echo("<script language=javascript>\n");

  echo("  function repetido(path)\n");
  echo("  {\n");
  echo("    file=getfilename(path);\n");

  if (count($lista)>0)
    foreach($lista as $cod => $linha)
    {
      if ($linha['Diretorio']==$diret && $linha['Arquivo']!="")
      {
        echo("    if (file=='".$linha['Arquivo']."')\n");
        echo("      return(true);\n");
      }
    }

  echo("    return(false);\n");
  echo("  }\n");

  /* Funcao que verifica se no nome do arquivo a ser anexado h� caracteres estranhos*/
  echo("  function ArquivoValido(path)\n");
  echo("  {\n");
  echo("    var file=getfilename(path);\n");
  echo("    var n=file.length;\n");
  echo("    for(i=0; i<=n; i++) {\n");
  echo("      if ((file.charAt(i)==\"'\") || (file.charAt(i)==\"#\") || (file.charAt(i)==\"%\") || (file.charAt(i)==\"?\") || (file.charAt(i)==\"/\")) {\n");
  echo("        return(false);\n");
  echo("      }\n");
  echo("    }");
  echo("    return(true);\n");
  echo("  }\n");
  
  echo("  function getfilename(path)\n");
  echo("  {\n");
  echo("    pieces=path.split('\\\\');\n");
  echo("    n=pieces.length;\n");
  echo("    file=pieces[n-1];\n");
  echo("    pieces=file.split('/');\n");
  echo("    n=pieces.length;\n");
  echo("    file=pieces[n-1];\n");
  echo("    return(file);\n");
  echo("  }\n");

  echo("  function validar(formul)\n");
  echo("  {\n");
  echo("    if (!ArquivoValido(formul.arquivo.value)) \n");
  echo("    {\n");
  /* 67 - O nome do arquivo n�o pode conter caracteres como aspas, #, %, ? e /. Por favor renomeie seu arquivo e tente novamente. */
  echo("      alert('".RetornaFraseDaLista($lista_frases_geral, 67)."');\n");
  echo("      return false;\n");
  echo("    }\n");
  
  echo("    if (formul.arquivo.value=='' || formul.arquivo.value==null)\n");
  echo("    {\n");
  /* 17 - Escolha primeiramente um arquivo clicando no bot�o Browse(Procurar). */
  echo("      alert('".RetornaFraseDaLista($lista_frases,17)."');\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
  echo("      if (repetido(formul.arquivo.value)) \n");
  /* 18 - J� existe um arquivo com este nome na pasta destino. */
  /* 19 - Deseja sobrescrever o arquivo existente? */
  echo("        return(confirm('".RetornaFraseDaLista($lista_frases,18)."\\n".RetornaFraseDaLista($lista_frases,19)."'));\n");
  echo("      else\n");
  echo("        return true;\n");
  echo("    }\n");
  echo("  }\n");

  echo("</script>\n");

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus();>\n");
  /* 1 - Din�mica do Curso */
  $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 11 - Anexar Arquivo */
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,11)."</b>";
  echo($cabecalho);

  echo("<br>\n");
  /* 37 - Pasta Raiz */
  echo("<img src=../figuras/pasta.gif border=0> <font class=text><b>".RetornaFraseDaLista($lista_frases_geral,37)."/".$diret."</b></font>");
  echo("<p>\n");
  echo("<form name=anexar action=anexar_arquivo2.php enctype=\"multipart/form-data\" method=post onSubmit=return(validar(this));>\n");
  echo(RetornaSessionIDInput());

  /* 12 - Pressione o bot�o Browse (ou Procurar) abaixo para selecionar o arquivo a ser anexado; em seguida, pressione OK para prosseguir. */
  echo("  <font class=text>".RetornaFraseDaLista($lista_frases,12)."<br>\n");
  /* 13 - (arquivos .ZIP podem ser enviados e descompactados posteriormente) */
  echo("  ".RetornaFraseDaLista($lista_frases,13)."</font><br><br>\n");
  echo("  <input type=file name=arquivo class=text><br>\n");
  /* 18 - Ok (ger) */
  echo("  <input type=submit value='".RetornaFraseDaLista($lista_frases_geral,18)."' class=text>\n");
  /* 2 - Cancelar (ger) */
  echo("  &nbsp;&nbsp;&nbsp;<input type=button value='".RetornaFraseDaLista($lista_frases_geral,2)."' onclick=top.opener.location='editar_dinamica2.php?".RetornaSessionID()."&cod_curso=".$cod_curso."';self.close(); class=text>\n");
  echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("  <input type=hidden name=diret value='".$diret."'>\n");
  echo("</form>\n");

  echo("</body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
