<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/sala_base.php

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
  ARQUIVO : cursos/aplic/batepapo/sala_base.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

?>

<link rel="stylesheet" href="./templates/sk15_purple/main.css" type="text/css">
<script type="text/javascript" language="javascript">
// Lista as mensagem do sistema Imprimindo-as
// N�O RETIRE ESSAS LINHAS DO TEMPLATE !!!

  var timeout = setTimeout("javascript:delayReload();", "5000");

  function delayReload() {
    if (navigator.userAgent.indexOf("MSIE") != "-1"){
     window.self.history.go(0);
    } else {
     window.self.location.reload();
    }
  }

</script>
<?php
  echo("</head>\n");
  echo("<body bgcolor=\"#666696\" text=\"#000000\" link=\"#000020\" vlink=\"#000020\" alink=\"#000020\" leftmargin=\"10\" topmargin=\"10\" marginwidth=\"10\" marginheight=\"10\">\n");

  session_start();

  if (VerificaRetiradaOnline($sock))
  {
    $removidos=LimpaOnline($sock,$cod_curso, 600);
  }

  Desconectar($sock);
  $sock=Conectar("");

  $query="select * from Batepapo_sessoes_correntes where data > '".$tempo."' AND cod_curso=".$cod_curso." ORDER BY data";

  $query2="select data from Batepapo_sessoes_correntes where cod_curso=".$cod_curso." ORDER BY data DESC limit 1";

  $res=Enviar($sock,$query2);
  $lista=RetornaLinha($res);
  $_SESSION['tempo'] = $lista['data'];

  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  #var_dump($lista);
  $separador = ':';

  $sock = Conectar($cod_curso);

  if (is_array($lista)){
    echo("<script language=\"javascript\" type=\"text/javascript\">\n");

    foreach($lista as $cod=>$linha)
    {
      $linha['mensagem'] = ConverteAspas2Html(LimpaTags(LimpaConteudo($linha['mensagem'])));
      if ($linha['cod_usuario_r'] == $cod_usuario)
      {
        $cor_fundo = 'white';
        $expessura = '1px';
      }
      else if ($linha['cod_usuario'] == $cod_usuario)
      {
        $cor_fundo = 'white';
        $expessura = '1px';
      }
      else 
      {
        $cor_fundo = 'white';
        $expessura = '0px';
      }

      if ($linha['cod_fala'] == 7 || $linha['cod_fala'] == 8)
      {
        $linha['apelido_r'] = '';
        $separador = '';
        if ($linha['cod_fala'] == 8)
        {
          $linha['fala'] = RetornaFraseDaLista($lista_frases,'8');
        }
      }

      $conteudo = "<table width=\"100%\" cellspacing=4 cellpadding=4><tr><td bgcolor=\"#FFFFFF\" style=\"border-style: solid;border-width: ".$expessura.";border-color: #000000\"><font size=\"1\" color=\"black\">(".UnixTime2Hora($linha['data']).") - <b></font><font size=\"2\" color=\"black\">".$linha['apelido']."</b> ".$linha['fala']." <b>".$linha['apelido_r']." </b> ".$separador." ".$linha['mensagem']."</font></td></tr></table>";
      //troca EOL por " "
      $conteudo = str_replace(chr(10), " ", $conteudo);
      $conteudo = str_replace(chr(13), " ", $conteudo);

      if(! (UsuarioOnline($sock, $cod_usuario)) ){
        echo("  clearTimeout(timeout);\n");
        echo("  window.parent.meio.document.write('".RetornaFraseDaLista($lista_frases, 23)."<br>');\n");
        echo("  window.parent.meio.document.write('".RetornaFraseDaLista($lista_frases, 24)."<br>');\n");
      }else{
        echo("  window.parent.meio.document.write('".trim($conteudo)."');\n");
      }

      echo("  window.parent.base.document.formBaixo.mensagem.focus();\n");
    }
    echo("</script>\n");

  }

echo("</body>\n");
echo("</html>\n");
?>
