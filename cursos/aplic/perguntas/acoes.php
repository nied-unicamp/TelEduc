<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/acoes.php

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
  ARQUIVO : cursos/aplic/perguntas/acoes.php
  ========================================================== */

// @todo Validar a importacao, redirecionando para a pagina anterior com erro
//		 ou mandando para o proximo passo.
// @todo Realizar a importacao e redirecionar para as perguntas.
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."importar.inc");
  include("perguntas.inc");

  $cod_usuario_global=VerificaAutenticacao($cod_curso);
  $sock=Conectar("");

  $cod_ferramenta = 6;
  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso); 
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  
  if ($acao == "novoAssunto"){
  	
  	list ($assunto_salvo, $cod_assunto) = SalvaAssunto($sock, $cod_assunto_pai, VerificaStringQuery($novo_nome), "");
  	header("Location:editar_assunto.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=".$cod_assunto."&acao=".$acao."&atualizacao=true");
  
  } else if ($acao == "novaPergunta"){
  	
  	/* Pra testar, estou passando pro perguntas.php, o certo será ver_pergunta.php */
  	
  	list ($pergunta_salva, $cod_pergunta) = SalvaPergunta($sock, $cod_assunto_pai, $novo_nome, "");
  	AtualizaFerramentasNova($sock, $cod_ferramenta, 'T');
  	header("Location:perguntas.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=".$cod_assunto_pai."&acao=".$acao."&cod_pergunta=".$cod_pergunta."&atualizacao=true");

  } else if ($acao == "apagarItem"){
  	
  	if (isset($cod_pergunta)){
  		if (!is_array($cod_pergunta)){
			ApagaPergunta($sock, $cod_pergunta);
		} else {
			foreach ($cod_pergunta as $cod_pergunta){
  				ApagaPergunta($sock, $cod_pergunta);
  			}
		}
  	}
  	
  	if (isset($cod_assunto)){
  		if (!is_array($cod_assunto)){
			ApagaAssunto($sock, $cod_assunto);
		} else {
			foreach ($cod_assunto as $cod_assunto){
  				ApagaAssunto($sock, $cod_assunto);
  			}
		}
  	}
  	
  	header("Location:perguntas.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=".$cod_assunto_pai."&acao=".$acao."&atualizacao=true");
  	
  } else if ($acao == "recuperarPergunta"){

  	if (is_array($cod_pergunta)){
  		foreach ($cod_pergunta as $cod_pergunta){
  			MovePergunta($sock, $cod_pergunta, $cod_assunto_dest);	
  		}
  	} else {
  		MovePergunta($sock, $cod_pergunta, $cod_assunto_dest);  
  	}
  	
  	header("Location:perguntas.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=2&acao=".$acao."&atualizacao=true");
  	
  } else if ($acao == "moverItem"){
  	
  	if (isset($cod_pergunta)){
  		if (!is_array($cod_pergunta)){
  			MovePergunta($sock, $cod_pergunta, $cod_assunto_dest);
  		} else {
  			foreach ($cod_pergunta as $cod_pergunta) {
  				MovePergunta($sock, $cod_pergunta,$cod_assunto_dest);
  			}
  		} 
  	}
  	
  	if (isset($cod_assunto)){
		if (!is_array($cod_assunto)){
  			MoveAssunto($sock, $cod_assunto, $cod_assunto_dest);
		} else {
			foreach ($cod_assunto as $cod_assunto){
  				MoveAssunto($sock, $cod_assunto, $cod_assunto_dest);
  			}
		}
  	}
  	
  	header("Location:perguntas.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=".$cod_assunto_dest."&acao=".$acao."&atualizacao=true");
  } else if ($acao == "excluirItem"){
  	ExcluiPergunta($sock, $cod_pergunta);
  	header("Location:perguntas.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=2&acao=".$acao."&atualizacao=true");
  	
  } else if ($acao == "validarImportacao"){
 	$sock = MudarDB($sock, "");	
 	
 	$array = explode(";", $cod_curso_todos);
  	$tipo_curso_origem = $array[0]; 
  	$cod_curso_origem = $array[1];

  	$_SESSION['cod_topico_destino'] = $cod_topico_raiz;
  	$_SESSION['cod_curso_origem'] = $cod_curso_origem;
  	$_SESSION['flag_curso_extraido'] = ($tipo_curso_origem == 'E');
  	if($cod_curso_origem)
  	{
  		$cod_usuario_import = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso_origem);
	  	
	  	if ( FerramentaEstaCompartilhada($sock, $cod_curso_origem, $cod_ferramenta) ){
	  		$_SESSION['flag_curso_compartilhado'] = TRUE;
	  		header("Location:importar_perguntas.php?cod_curso=".$cod_curso."&cod_assunto_pai=1&cod_curso_origem=".$cod_curso_origem);
	  	} else if ( $cod_usuario_import != NULL && EFormadorMesmo($sock,$cod_curso_origem,$cod_usuario_import) ){
	  		$_SESSION['flag_curso_compartilhado'] = FALSE;
	  		header("Location:importar_perguntas.php?cod_curso=".$cod_curso."&cod_assunto_pai=1&cod_curso_origem=".$cod_curso_origem);
	  	} else {
	  		header("Location:importar_curso.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&acao=".$acao."&atualizacao=false");
	  	}
  	}else{
  		header("Location:importar_curso.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&acao=ErroImportacao&atualizacao=false");
  	}
  	
  } else if ($acao == "importarItem"){
  	
  	$cod_curso_destino = $cod_curso;
  	$cod_topico_destino = $_SESSION['cod_topico_destino'];
  	$cod_usuario;
  	$cod_curso_origem = $_SESSION['cod_curso_origem'];
  	$flag_curso_extraido = $_SESSION['flag_curso_extraido'];
  	$flag_curso_compartilhado = $_SESSION['flag_curso_compartilhado'];
  	$array_topicos_origem = $cod_assunto;
  	$array_itens_origem = $cod_pergunta;
  	$dirname = "perguntas";
  	$nome_tabela = "Pergunta";
  	
  	AtualizaFerramentasNova($sock, $cod_ferramenta, 'T');
  	ImportarMateriais($cod_curso_destino, $cod_topico_destino, $cod_usuario,
                      $cod_curso_origem, $flag_curso_extraido, $flag_curso_compartilhado,
                      $array_topicos_origem, $array_itens_origem, $nome_tabela,
                      $dirname, $diretorio_arquivos_destino, $diretorio_arquivos_origem);
    header("Location:perguntas.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=".$cod_topico_destino."&acao=".$acao."&atualizacao=true");
  }
  
  
?>