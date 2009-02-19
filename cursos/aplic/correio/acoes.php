<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/forum/acoes.php

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

    Nied - Ncleo de Informática Aplicada à Educação
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
  ARQUIVO : cursos/aplic/forum/acoes.php
  ========================================================== */
  
  
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("correio.inc");
  
  $cod_ferramenta=11;

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
 
  if ($acao=='apagar'){
      $estado='A';
      if($status_mensagem == 'R'){  // recebida
        TrocaEstadoDaMensagemRecebidas($sock, $estado, $cod_msg, $cod_usuario);
      }else if($status_mensagem == 'E'){ //enviada
        TrocaEstadoDaMensagemEnviadas($sock, $estado, $cod_msg, $cod_usuario);
      }

      echo("<html>\n");
      echo("  <head>\n");
      echo("    <script type=\"text/javascript\">\n");
      echo("      opener.ApagarOpener('".$cod_msg."', '".$status_mensagem."', 'apa');\n");
      echo("      window.location = 'remove_link_simbolico.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."';\n");
      echo("    </script>\n");
      echo("  </head>\n");
      echo("  <body>\n");
      echo("  </body>\n");
      echo("</html>\n");
  }else if($acao == 'excluir'){
      $estado='X';
      if($status_mensagem == 'L'){  // lixeira
        TrocaEstadoDaMensagemLixeira($sock, $estado, $cod_msg, $cod_usuario, '');
      }

      echo("<html>\n");
      echo("  <head>\n");
      echo("    <script type=\"text/javascript\">\n");
      echo("      opener.ApagarOpener('".$cod_msg."', '".$status_mensagem."', 'exc');\n");
      echo("      window.location = 'remove_link_simbolico.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."';\n");
      echo("    </script>\n");
      echo("  </head>\n");
      echo("  <body>\n");
      echo("  </body>\n");
      echo("</html>\n");
  }else if($acao == 'recuperar'){
      $estado='L';
      if($status_mensagem == 'L'){  // lixeira
        TrocaEstadoDaMensagemLixeira($sock, $estado, $cod_msg, $cod_usuario, 'rec');
      }

      echo("<html>\n");
      echo("  <head>\n");
      echo("    <script type=\"text/javascript\">\n");
      echo("      opener.ApagarOpener('".$cod_msg."', '".$status_mensagem."', 'rec');\n");
      echo("      window.location = 'remove_link_simbolico.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."';\n");
      echo("    </script>\n");
      echo("  </head>\n");
      echo("  <body>\n");
      echo("  </body>\n");
      echo("</html>\n");
  }

  Desconectar($sock);
  exit;
?>