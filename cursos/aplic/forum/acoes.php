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
  include("forum.inc");
  include("avaliacoes_forum.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun��es em PHP que voc� quer chamar atrav�s do xajax
  $objAjax->register(XAJAX_FUNCTION,"SalvaMensagem");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();
  
  $cod_ferramenta=9;
  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,9);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  $AcessoAvaliacaoF = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);
  
  
  if ($acao=='nova_msg'){

    $atualizacao="true";
    if (!SalvaMensagem($sock, NULL, $cod_forum, $cod_usuario, $msg_titulo, $msg_corpo, $cod_curso))
    {
      /* 18 - Erro na composi�o da mensagem. */
      $atualizacao="false";
    }
    
    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
  }
  if ($acao=='responde_mensagem'){
  
    $atualizacao="true";
    if (!((MensagemExiste($sock, $codRespondeMensagem, $cod_forum)) &&
      (SalvaMensagem($sock, $codRespondeMensagem, $cod_forum, $cod_usuario, $msg_titulo, $msg_corpo, $cod_curso)))){
      /* 30 - Erro ao responder a mensagem. Verifique se a mensagem não foi apagada.. */
      $atualizacao="false";
    }

    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
    AtualizaFerramentasNova($sock,9,'T');
  
  }
  else if ($acao=='avaliar') {
  
  }

  else if ($acao=="excluir")
  {
    $atualizacao="true";

    if (($AcessoAvaliacaoF)&&(ForumEraAvaliacao($sock,$cod_forum)))
    {
      $cod_avaliacao=RetornaCodAvaliacaoDeletada($sock,$cod_forum);
      if (ExcluiAvaliacaoForum($sock, $cod_avaliacao,$cod_usuario))
      {
        if (!ConfiguraForum($sock, $cod_forum, "X")){
          RecuperaAvaliacaoExcluida($sock,$cod_avaliacao,$cod_usuario);
          /* 66 - Erro ao se excluir o fórum. */
          $atualizacao="false";
        }
      }
      else
      {
        /* 88 - Erro ao excluir avaliação do fórum. */
        $atualizacao="false";
        $acao = "excluirAvaliacao";
      }
    }
    elseif (!ConfiguraForum($sock, $cod_forum, "X"))
    {
      /* 66 - Erro ao se excluir o fórum. */
      $atualizacao="false";
    }
    Desconectar($sock);
    header("Location:forum.php?cod_curso=".$cod_curso."&status=D&acao=".$acao."&atualizacao=".$atualizacao);
    exit;
  }
  else if ($acao=="recuperar")
  {
    $atualizacao="true";

    if (($AcessoAvaliacaoF)&&(ForumEraAvaliacao($sock,$cod_forum)))                 //avalia�o foi apagada, tem que recuper�la
    {
      $cod_avaliacao=RetornaCodAvaliacaoDeletada($sock,$cod_forum);
      if (RecuperaAvaliacaoForum($sock,$cod_avaliacao,$cod_usuario))
      {
        if (!ConfiguraForum($sock, $cod_forum, "L"))
        {
          RecuperaAvaliacaoExcluida($sock,$cod_avaliacao,$cod_usuario);
          /* 83 - Erro ao recuperar o f�um. */
          $atualizacao="false";
        }
      }
      else
      {
        /* 89 - Erro ao recuperar avalia�o do f�um. */
        $atualizacao="false";
        $acao="recuperarAvaliacao";
      }
    }
    else if (!ConfiguraForum($sock, $cod_forum, "L"))       //Avalia�o n� est�apagada, ou ja foi recuperada ou foi excluida, entao nao faz nada
    {
      /* 83 - Erro ao recuperar o f�um. */
      $atualizacao="false";
    }
    Desconectar($sock);
    header("Location:forum.php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
    exit;
  }else if ($acao=='apagar'){
  
    $atualizacao="true";
    if (($AcessoAvaliacaoF)&&(ForumEhAvaliacao($sock,$cod_forum)))
    {
      $cod_avaliacao=RetornaCodAvaliacao($sock,$cod_forum);
      if (ApagaAvaliacaoForum($sock, $cod_avaliacao,$cod_usuario))
      {
        if (DeletaForum($sock, $cod_forum))
        {
        /* 38 - F�um apagado com sucesso. */
          $query="delete from Forum_permissoes where cod_forum=".$cod_forum."";
          Enviar($sock,$query);
        }
        else
        {
          /* 39 - Erro ao apagar o f�um. */
          $atualizacao="false";
        }
      }
      else
      {
        /* 87 - Erro ao apagar avaliação do fórum. */
        $atualizacao="false";
      }
    }
    else if (DeletaForum($sock, $cod_forum))
    {
      /* 38 - Fórum apagado com sucesso. */
      $query="delete from Forum_permissoes where cod_forum=".$cod_forum."";
      Enviar($sock,$query);
    }
    else
    {
      /* 39 - Erro ao apagar o fórum. */
      $atualizacao="false";
    }

    Desconectar($sock);
    header("Location:forum.php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
    exit;

  }else if($acao=='configurar_forum'){

    //limpa as permissoes atuais
    $query="DELETE FROM Forum_permissoes WHERE cod_forum=".$cod_forum;
    Enviar($sock,$query);

  //==========================
  // Salvando lista de participantes
  //==========================
  //cria array com a lista de participantes, sem nomes repetidos, expandindo coringas.
  //select_participantes_permissao contem os cod_usuarios dos participantes permitidos e os cod_grupos no formado g<cod_grupo>
    if (is_array($select_participantes_permissao)){
      $select_participantes_permissao_aux = array();
      $select_grupos_permissao_aux = array();
      foreach($select_participantes_permissao as $cont){
        switch ($cont){
          case "F*" :
            $formadores= RetornaTodosFormadores($sock, $cod_curso);
            foreach($formadores as $formador){
              $select_participantes_permissao_aux[]= $formador[0];
              next($select_participantes_permissao_aux);
            }
            break;
          case "A*" :
            $alunos= RetornaTodosAlunos($sock, $cod_curso);
            foreach($alunos as $aluno){
              $select_participantes_permissao_aux[]= $aluno[0];
              next($select_participantes_permissao_aux);
            }
            break;

          case "T*" :
            $todos= RetornaTodos($sock, $cod_curso);
            foreach($todos as $t){
              $select_participantes_permissao_aux[]= $t[0];
              next($select_participantes_permissao_aux);
            }
            break;
  
          case "G*" :
            $grupos = RetornaGrupos($sock);
            foreach($grupos as $g){
              $select_grupos_permissao_aux[]= $g[0];
              next($select_grupos_permissao_aux);
            }
            break;
  
          case "Z*" :
            $colaboradores = RetornaTodosColaboradores($sock, $cod_curso);
            foreach($colaboradores as $c){
              $select_participantes_permissao_aux[]= $c[0];
              next($select_participantes_permissao_aux);
            }
            break;

          case "U*" :
            break;

          default :
            if(!ereg("[g]",$cont)){
              $select_participantes_permissao_aux[]= $cont;
              next($select_participantes_permissao_aux);
            }else{
              $cod_grupo = explode("g",$cont);
              $select_grupos_permissao_aux[]= $cod_grupo[1];
              next($select_grupos_permissao_aux);
            }
        }//fim do switch
      }//fim do foreach
    }
      
    // Armazenando todos os c�igos dos participantes permitidos no array
    // select_participantes_permissao_cod
	  
      $query_inicial="insert ignore into Forum_permissoes (cod_forum, cod_permitido, tipo)";
      $query_final="";
      foreach($select_participantes_permissao_aux as $cod => $cont){
        $cod_usuario_conf= $cont;
        $query_final.=" values (".$cod_forum.", ".$cod_usuario_conf.", 'U')";
        AtualizaFerramentasNovaUsuario($sock, $cod_ferramenta, $cod_usuario_conf);

        if(sizeof($query_final > 100000)){   //garante um tamanho bom de string
          Enviar($sock,$query_inicial.$query_final);
          $query_final="";
        }
      }

      if(sizeof($query_final)>1){ //vazia ela tem tamanho 1
          Enviar($sock,$query_inicial.$query_final);
      }

    // Armazenando todos os códigos dos grupos permitidos no array 
    // select_grupos_permissao_cod  
      $query_final="";
      foreach($select_grupos_permissao_aux as $cont_g){
        $cod_grupo_conf= $cont_g;
        $query_final.=" values (".$cod_forum.", ".$cod_grupo_conf.", 'G')";
        AtualizaFerramentasNovaGrupo($sock, $cod_ferramenta, $cod_grupo_conf);

        if(sizeof($query_final > 100000)){  //garante um tamanho bom de string
          Enviar($sock,$query_inicial.$query_final);
          $query_final="";
        }
      }
      if(sizeof($query_final)>1){
          Enviar($sock,$query_inicial.$query_final);
      }

      //update...
      $query = "update Forum set status='".$status."' where cod_forum=".$cod_forum;
      Enviar($sock,$query);

      echo("<html>\n");
      echo("  <head>\n");
      echo("    <script type=\"text/javascript\">\n");
      echo("      top.opener.document.getElementById('forum_".$cod_forum."').setAttribute('status_conf', '".$status."');\n");
      echo("      top.opener.document.getElementById('forum_leitura_".$cod_forum."').innerHTML='';\n");
      echo("      top.opener.document.getElementById('forum_".$cod_forum."').style.fontWeight='bold';\n");
      echo("      top.opener.mostraFeedback(top.opener.msg_56, true);\n");
      echo("      this.close();\n");
      echo("    </script>\n");
      echo("  </head>\n");
      echo("  <body>\n");
      echo("  </body>\n");
      echo("</html>\n");
      
      Desconectar($sock);      
      exit;

  }
  
  else if($acao=="novo_forum" && !strcmp($avaliacao,'S')){

    $atualizacao="true";

    if(!SalvaForum($sock, $nome)){
      /* 6 - Erro na criação do fórum. */
      $atualizacao="false";
    }

    $cod_atividade = RetornaCodForum($sock,$nome);
    Desconectar($sock);
    header("Location:../avaliacoes/criar_avaliacao_forum.php?cod_curso=".$cod_curso."&cod_atividade=".$cod_atividade);
    exit;
 
  }else if($cancelar_avaliacao=="sim"){
    CancelaEdicaoAvaliacao ($sock, $cod_forum,$cod_usuario);
    Desconectar($sock);
    header("Location:../forum/forum.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta);
    exit;

  }else if($acao=="novo_forum"){

    $atualizacao="true";

    if(!SalvaForum($sock, $nome)){
      /* 6 - Erro na criação do fórum. */
      $atualizacao="false";
    }
    AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario);
    Desconectar($sock);
    header("Location:forum.php?cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);
    exit;
  }

  header("Location:ver_forum.php?cod_forum=".$cod_forum."&cod_curso=".$cod_curso."&acao=".$acao."&atualizacao=".$atualizacao);

  exit;
?>