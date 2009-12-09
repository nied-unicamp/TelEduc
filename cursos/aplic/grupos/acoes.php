<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/grupos/acoes.php

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
  ARQUIVO : cursos/aplic/grupos/acoes.php
  ========================================================== */


  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("grupos.inc");

  $cod_ferramenta=12;
  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,9);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  if ($acao=='apagar_grupo'){

    if (!GruposFechados($sock) || EFormador($sock,$cod_curso,$cod_usuario))
    {
      ExcluirGrupo($sock,$cod_grupo);
      $atualizacao="true";
      
    }else{
      $atualizacao="false";
    }


  }else if($acao=='incluir_no_grupo'){

    

    if (is_array($chk_com_incluir))
    {
      foreach($chk_com_incluir as $cod => $linha)
      {
        InsereUsuarioNoGrupoGU($sock,$cod_grupo, $linha);
        AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$linha);
      }
    }

    if (is_array($chk_sem_incluir)){
      foreach($chk_sem_incluir as $cod => $linha)
      {
        InsereUsuarioNoGrupoGU($sock,$cod_grupo, $linha);
      }
    }
    
    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
    
    echo("<html>\n");
    echo("  <head>\n");
    echo("    <script type=\"text/javascript\">\n");
    echo("      opener.location = 'grupos.php?cod_curso=".$cod_curso."&acao=incluir_no_grupo&atualizacao=true';\n");
    echo("      this.close();\n");
    echo("    </script>\n");
    echo("  </head>\n");
    echo("  <body>\n");
    echo("  </body>\n");
    echo("</html>\n");
    exit;
  }else if($acao=='criar_grupo'){
  
    $cod_grupo = RetornaProximoCodigo($sock,"Grupos");
    InsereGrupoG($sock,$cod_grupo,$novo_nome);

    $rep="#G".$cod_grupo;
    $query="insert into Portfolio_topicos (cod_topico_pai,cod_usuario,cod_grupo,tipo_compartilhamento,data,posicao_topico,topico) values (4,".$cod_usuario.",".$cod_grupo.",'P',".time().",".UltimaPosicaoLivreTopico($sock, 4).",'".$rep."')";
    Enviar($sock,$query);
    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);

    $atualizacao="true";
  }

  Desconectar($sock);
  header("Location:grupos.php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
  
  exit;
?>