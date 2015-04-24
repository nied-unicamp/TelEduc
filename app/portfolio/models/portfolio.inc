<?php

class Portfolio{

	/* *********************************************************************
	 StatusFerramentaGrupos - retorna se a ferramenta Grupos esta ativada
	
	PROVISORIO !!! - O LUGAR DESTA FUNCAO NAO EH AQUI
	
	entrada: nenhuma
	saida  : true  - ferramenta grupos estah ativada
	false - ferramenta grupos nao estah ativa
	*/
	static function StatusFerramentaGrupos ($sock)
	{
		$query = "select status from Curso_ferramentas where cod_ferramenta=12";
		$res   = AcessoSQL::Enviar ($sock, $query);
		$linha = AcessoSQL::RetornaLinha ($res);
		return ( $linha ['status'] != 'D' );
	}
	
	/* *********************************************************************
	 ExpulsaVisitante - verifica se o usuario eh visitante e impede seu
	acesso se for
	entrada: $sock        - sock de conexao
	$cod_curso   - codigo do curso atual.
	$cod_usuario - codigo do usuario o qual pretende-se testar o
	acesso a ferramenta correio.
	$eh_popup    - booleano que indica se a pagina que o chamou
	e ou nao um popup. Se nao for, devemos fechar
	as tags html do template comum de uma pagina
	de ferramenta (com rodape, fechando o tabelao,
			etc).
	saida:   nenhuma
	OBS:     Para impedir o acesso, exibe tela com restricao de acesso e
	executa o comando exit
	*/
	static function ExpulsaVisitante($sock, $cod_curso, $cod_usuario, $eh_popup = false) {
		
		$diretorio_imgs = '../../../web-content/imgs/';
		$ferramenta_administracao = 'administracao';
		$view_administracao = '../../'.$ferramenta_administracao.'/views/';
	
		if (Usuarios::EVisitante($sock, $cod_curso, $cod_usuario)) {
	
			//if (!$ehPopup) {
			//  include("../menu_principal.php");
			//  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
			//}
			if ($eh_popup) {
				echo("  </head>\n");
				echo("  <body>\n");
				echo("    <h3 style=\"margin-top:20px;\">".Cursos::NomeCurso($sock,$cod_curso)."</h3>\n");
			}
	
			/* 1 - Portfolio 504 - Area restrita a alunos e formadores */
			echo("          <h4>"._("PORTFOLIO_15")." - "._("RESTRICTED_AREA_STUDENTS_INSTRUCTORS_-1")."</h4>\n");
	
			if (!$eh_popup) {
				echo("          <ul class=\"btsNav\">\n");
				echo("            <li>\n");
				/* 509 - Voltar */
				echo("              <span onclick=\"javascript:history.back(-1);\">\n");
				echo("                &nbsp;&lt;&nbsp;"._("BACK_-1")."&nbsp;\n");
				echo("              </span>\n");
				echo("            </li>\n");
				echo("          </ul>\n");
			}
	
			if (!$eh_popup) {
				echo("          <div id=\"mudarFonte\">\n");
				echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
				echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
				echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
				echo("          </div>\n");
			}
	
			if ($eh_popup) {
				/* 13 - Fechar */
				echo("          <form>\n");
				echo("            <input class=\"input\" type=\"button\" value=\""._("CLOSE_-1")."\" onclick=\"javascript:self.close();\" />\n");
				echo("          </form>\n");
			}
			else {
				/* 23 - Voltar */
				echo("          <form>\n");
				echo("            <input class=\"input\" type=\"button\" value=\""._("BACK_-1")."\" onclick=\"javascript:history.go(-1);\" />\n");
				echo("          </form>\n");
			}
	
			if (!$eh_popup) {
				echo("        </td>\n");
				echo("      </tr>\n");
				require_once $view_administracao.'tela2.php';
			}
	
			echo("  </body>\n");
			echo("</html>\n");
			AcessoSQL::Desconectar($sock);
			exit();
		}
	}
	
	/* *********************************************************************
	 VerificaPortfolioGrupos - Verifica se existem portfolios para todos os grupos aos quais
	o usuario logado pertence
	Entrada: $sock - BASE DO CURSO
	$cod_usuario - codigo do usuario logado
	Saida: nenhuma
	*/
	static function VerificaPortfolioGrupos ($sock, $cod_usuario)
	{
		// lista dos grupos do usuario (excluindo grupos apagados: grupos apagados somente aparecem se foi colocado algo neles qdo estavam ativos)
		$query = "select GU.cod_grupo from Grupos_usuario GU, Grupos G where GU.cod_grupo=G.cod_grupo and GU.cod_usuario=".$cod_usuario." and G.status <> 'X' order by cod_grupo";
		$res   = AcessoSQL::Enviar ($sock, $query);
		$lista = AcessoSQL::RetornaArrayLinhas ($res);
	
		if (is_array ($lista))
		foreach ($lista as $c => $linha)
		{
			$lista_grupos [ $linha['cod_grupo'] ] = true;
		}
	
		// lista dos portfolios de grupo
		$query = "select cod_topico, cod_grupo from Portfolio_topicos where cod_grupo IS NOT NULL order by cod_grupo";
		$res   = AcessoSQL::Enviar ($sock, $query);
		$lista2= AcessoSQL::RetornaArrayLinhas ($res);
	
		if (is_array ($lista2))
		foreach ($lista2 as $c => $linha)
		{
			$lista_portfolios_grupos [ $linha['cod_grupo'] ] = true;
		}
	
		// fazendo o match: pegamos os grupos aos quais o usuario pertence e que n? tem portfolio na lista
		if (is_array ($lista_grupos))
		foreach ($lista_grupos as $cod_grupo => $c)
		{
			if (!isset ($lista_portfolios_grupos [ $cod_grupo ]))
			{
				// Nao existe um portfolio para este grupo. Criamos
				$rep="#G".$cod_grupo;
				$query="insert into Portfolio_topicos (cod_topico_pai,cod_usuario,cod_grupo,tipo_compartilhamento,data,posicao_topico,topico) values (4,".$cod_usuario.",".$cod_grupo.",'P',".time().",".Portfolio::UltimaPosicaoLivre($sock, 4).",'".$rep."')";
				AcessoSQL::Enviar($sock,$query);
			}
		}
	}
	
	/* *********************************************************************
	 UltimaPosicaoLivre - Retorna a prÛxima posiÁ„o livre no topico
	Entrada: $sock - BASE DO CURSO
	$cod_topico - codigo do topico atual
	Saida: posicao
	*/
	static function UltimaPosicaoLivre($sock, $cod_topico)
	{
		$consulta="select max(posicao_item) from Portfolio_itens where cod_topico=".$cod_topico;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$max_posicao_item=AcessoSQL::RetornaLinha($res);
	
		if($max_posicao_item[0] == null) $max_posicao_item=0;
	
		$consulta="select max(posicao_topico) from Portfolio_topicos where cod_topico_pai=".$cod_topico;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$max_posicao_topico=AcessoSQL::RetornaLinha($res);
		if($max_posicao_topico[0] == null) $max_posicao_topico=0;
	
	
		return(max($max_posicao_topico[0], $max_posicao_item[0])+1);
	}
	
	/* *********************************************************************
	 RetornaTopicosBase - retorna topicos raizes (portfolios) de usuarios ou grupos
	Entrada: $sock                       - sock de conexao com a base
	$cod_usuario                - codigo do usuario logado
	$cod_usuario_portfolio      - codigo do usuario dono dos portfolios a exibir
	$eformador                  - booleano, se o cara eh formador
	$acao_portfolio_s           - indica se
	'I' - portfolios individuais ou
	'G' - de grupo
	
	Saida: array com [$cod_topico]['cod_topico_pai']
	[$cod_topico]['cod_usuario']
	[$cod_topico]['cod_grupo']
	[$cod_topico]['topico']
	[$cod_topico]['tipo_compartilhamento']
	[$cod_topico]['data']
	[$cod_topico]['posicao_topico']
	[$cod_topico]['num_itens']
	[$cod_topico]['num_itens_comentados']
	[$cod_topico]['num_itens_nao_comentados']
	[$cod_topico]['figura']                    - nome do arquivo da figura a exibir
	*/
	static function RetornaTopicosBase ($sock, $cod_usuario, $cod_usuario_portfolio, $eformador, $acao_portfolio_s, $cod_curso)
	{
		$lista_retorno = "";
		unset ($lista_retorno);
	
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
	
		/*precisa dessa funÁ„o pois na primeira vez em que uma pessoa entra na ferramenta portfolio
		 eh necessario criar a pasta raiz*/
		
		$cod_grupo = $_GET['cod_grupo_portfolio'];
		Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo);
	
		// lista de usuarios
		$query = "select USUC.cod_usuario, USU.nome, USUC.tipo_usuario, USUC.portfolio
          from ".$dbnamebase.".Usuario USU
          inner join ".$dbnamebase.".Usuario_curso USUC ON ((USUC.cod_usuario_global = USU.cod_usuario) AND (USUC.cod_curso = '".$cod_curso."'))";
	
		$res = AcessoSQL::Enviar ($sock, $query);
		$lista = AcessoSQL::RetornaArrayLinhas ($res);
		if (is_array ($lista))
		foreach ($lista as $c => $usuario)
		{
			$lista_usr[ $usuario['cod_usuario'] ]['portfolio'] = $usuario['portfolio'];
			$lista_usr[ $usuario['cod_usuario'] ]['nome']         = $usuario['nome'];
			$lista_usr[ $usuario['cod_usuario'] ]['tipo_usuario'] = $usuario['tipo_usuario'];
		}
	
		// lista de grupos
		$query = "select * from Grupos order by status";
		$res   = AcessoSQL::Enviar ($sock, $query);
		$lista = AcessoSQL::RetornaArrayLinhas ($res);
		unset ($lista_grupos);
		if (is_array ($lista))
		foreach ($lista as $c => $linha)
		{
			$lista_grupos [$cod_grupo = $linha['cod_grupo']]['nome'] = $linha['nome'];
			$lista_grupos [$cod_grupo]['status'] = $linha['status'];
		}
	
		// lista de grupos aos quais o usuario LOGADO pertence
		$query = "select * from Grupos_usuario where cod_usuario=".$cod_usuario;
		$res   = AcessoSQL::Enviar ($sock, $query);
		$lista = AcessoSQL::RetornaArrayLinhas ($res);
		unset ($grupos_usr);
		if (is_array ($lista))
		foreach ($lista as $c => $linha)
		{
			$grupos_usr[$linha['cod_grupo']] = true;
		}
	
	
		if ($eformador)
		{
			// variavel para escolher compartilhamento dentro da query SQL
			$regra_comp = "(tipo_compartilhamento = 'F' or tipo_compartilhamento = 'T')";
			// variavel para escolher compartilhamento ao contar numero de itens dentro de um portfolio
			$comp = 'F';
		}
		else
		{
			$regra_comp = "tipo_compartilhamento = 'T'";
			$comp = 'T';
		}
	
		/*MONTA MEUS PORTFOLIOS*/
		if ($acao_portfolio_s == 'M')
		{
			// primeiro pego portfolio do usuario
			$query = "select * from Portfolio_topicos where cod_topico_pai=3 and cod_usuario=".$cod_usuario;
			$res   = AcessoSQL::Enviar ($sock, $query);
			$portfolio_pessoal = AcessoSQL::RetornaLinha ($res);
			$lista_retorno [$cod_topico = $portfolio_pessoal['cod_topico']] = $portfolio_pessoal;
			// 76 - Portfolio de
			$lista_retorno [$cod_topico]['topico'] = _("PORTFOLIO_OF_15")." ".$lista_usr [ $cod_usuario ]['nome'];
			$lista_retorno [$cod_topico]['figura'] = "icPasta";
			$lista_retorno [$cod_topico]['num_itens'] = $num_itens = Portfolio::RetornaNumItensPortfolio($sock, $cod_topico, 'P');
			$lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = Portfolio::RetornaNumItensComentadosPortfolio($sock, $cod_topico, 'P');
			$lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
	
			/*GRUPO*/
			if($_SESSION['ferramenta_grupos_s'])/*variavel global*/
			{
				$query = "select * from Portfolio_topicos where cod_topico_pai=4";
				$res   = AcessoSQL::Enviar ($sock, $query);
				$lista = AcessoSQL::RetornaArrayLinhas ($res);
				if (is_array ($lista))
				{
					unset($port_meus_grp);
					foreach ($lista as $c => $linha_port)
					{
						$cod_grupo = $linha_port['cod_grupo'];
						if ($grupos_usr[ $cod_grupo ])                                                                                              {
							$port_meus_grp [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
							// 77 - Portfolio do grupo
							$port_meus_grp [ $cod_topico ]['topico'] = _("PORTFOLIO_OF_GROUP_15")." ".$lista_grupos[$cod_grupo]['nome'];
							// verifica se o grupo ainda existe
							if ($lista_grupos[ $cod_grupo = $linha_port['cod_grupo'] ]['status'] != 'X')
								$port_meus_grp [ $cod_topico ]['figura'] = "icPasta2";
							else
								$port_meus_grp [ $cod_topico ]['figura'] = "icPasta3";
						}
					}
					if (is_array($port_meus_grp))
					{
						uasort($port_meus_grp, CompareArrayStatus);
						foreach ($port_meus_grp as $cod_topico => $portfolio)
						{
							$lista_retorno [ $cod_topico ] = $portfolio;
							$lista_retorno [ $cod_topico ]['num_itens'] = $num_itens = Portfolio::RetornaNumItensPortfolio ($sock, $cod_topico, 'P');
							$lista_retorno [ $cod_topico ]['num_itens_comentados'] = $num_itens_c = Portfolio::RetornaNumItensComentadosPortfolio ($sock, $cod_topico, 'P');
							$lista_retorno [ $cod_topico ]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
						}
					}
				}
			}
		}
		 
		else if ($acao_portfolio_s == 'I')
		{
			// primeiro pego portfolio do usuario
			$query = "select * from Portfolio_topicos where cod_topico_pai=3 and cod_usuario=".$cod_usuario;
			$res   = AcessoSQL::Enviar ($sock, $query);
			$portfolio_pessoal = AcessoSQL::RetornaLinha ($res);
	
			$lista_retorno [$cod_topico = $portfolio_pessoal['cod_topico']] = $portfolio_pessoal;
			// 76 - Portfolio de
			$lista_retorno [$cod_topico]['topico'] = _("PORTFOLIO_OF_15")." ".$lista_usr [ $cod_usuario ]['nome'];
			$lista_retorno [$cod_topico]['figura'] = "icPasta";
			$lista_retorno [$cod_topico]['num_itens'] = $num_itens = Portfolio::RetornaNumItensPortfolio($sock, $cod_topico, 'P');
			$lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = Portfolio::RetornaNumItensComentadosPortfolio($sock, $cod_topico, 'P');
			$lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
			//  $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,'P');
	
			// segundo, pego outros portfolios individuais que estao marcados como visiveis, isto e, o coordenador aprovou a permanencia do portifolio do aluno
	
			$query = "select * from Portfolio_topicos where cod_topico_pai=3 and cod_usuario <> ".$cod_usuario;
			$res   = AcessoSQL::Enviar ($sock, $query);
			$array_ind = AcessoSQL::RetornaArrayLinhas ($res);
	
			if (is_array ($array_ind))
			{
				unset ($port_ind);        // portfolios individuais
				unset ($port_rej);        // portfolios de alunos rejeitados
	
				foreach ($array_ind as $x => $linha_ind)
				{
					$cod_usr = $linha_ind ['cod_usuario'];
					if($lista_usr [ $cod_usr]['portfolio'] == 'ativado'){ // O portfolio esta ativo
						if (($lista_usr [ $cod_usr]['tipo_usuario'] != 'r') && ($lista_usr [ $cod_usr]['tipo_usuario'] != 'f'))   // alguem do curso
						{
							$port_ind [ $cod_topico = $linha_ind ['cod_topico'] ] = $linha_ind;
							// 76 - Portfolio de
							$port_ind [ $cod_topico ]['topico'] = _("PORTFOLIO_OF_15")." ".$lista_usr [ $cod_usr ]['nome'];
							$port_ind [ $cod_topico ]['figura'] = "icPasta2";
						}
						else     // aluno rejeitado ou formador desligado
						{
							$port_rej [ $cod_topico = $linha_ind ['cod_topico'] ] = $linha_ind;
							// 76 - Portfolio de
							$port_rej [ $cod_topico ]['topico'] = _("PORTFOLIO_OF_15")." ".$lista_usr [ $cod_usr ]['nome'];
							$port_rej [ $cod_topico ]['figura'] = "icPasta3";
						}
					}
				}
	
				if (is_array ($port_ind))
				{
					uasort ($port_ind, array('Portfolio', "CompareArrayTopicos"));
					foreach ($port_ind as $cod_topico => $portfolio_individual)
					{
						$num_itens = Portfolio::RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
						// LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
						// if ($num_itens > 0)
						{
							$lista_retorno [ $cod_topico ] = $portfolio_individual;
							$lista_retorno [ $cod_topico ]['num_itens'] = $num_itens;
							$lista_retorno [ $cod_topico ]['num_itens_comentados'] = $num_itens_c = Portfolio::RetornaNumItensComentadosPortfolio($sock, $cod_topico, $comp);
							$lista_retorno [ $cod_topico ]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
							//     $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
						}
					}
				}
	
				if (is_array ($port_rej))
				{
					uasort ($port_rej, array('Portfolio', "CompareArrayTopicos"));
					foreach ($port_rej as $cod_topico => $portfolio_rejeitado)
					{
						$num_itens = Portfolio::RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
						// LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
						// if ($num_itens > 0)
						{
							$lista_retorno [ $cod_topico ] = $portfolio_rejeitado;
							$lista_retorno [ $cod_topico ]['num_itens'] = $num_itens;
							$lista_retorno [ $cod_topico ]['num_itens_comentados'] = $num_itens_c = Portfolio::RetornaNumItensComentadosPortfolio($sock, $cod_topico, $comp);
							$lista_retorno [ $cod_topico ]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
							//     $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
						}
					}
				}
			}
		}
		else if ($acao_portfolio_s == 'G')
		{
			$query = "select * from Portfolio_topicos where cod_topico_pai=4";
			$res   = AcessoSQL::Enviar ($sock, $query);
			$lista = AcessoSQL::RetornaArrayLinhas ($res);
	
			if (is_array ($lista))
			{
				unset ($port_meus_grp);           // portfolios de grupos aos quais pertenco
				unset ($port_outros_grp);         // portfolios de grupos aos quais NAO pertenco
				unset ($port_grupos_x);           // portfolios de grupos apagados
	
				foreach ($lista as $c => $linha_port)
				{
					// SE UM GRUPO FOI APAGADO, O SEU PORTFOLIO SERA EXIBIDO INDEPENDENTE DO TIPO DE COMPARTILHAMENTO QUE ELE TINHA QDO O GRUPO ERA ATIVO
					if ($lista_grupos[ $cod_grupo = $linha_port['cod_grupo'] ]['status'] == 'X') // grupo apagado
					{
						$port_grupos_x [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
						// 77 - Portfolio do grupo
						$port_grupos_x [ $cod_topico ]['topico'] = _("PORTFOLIO_OF_GROUP_15")." ".$lista_grupos[$cod_grupo]['nome'];
						$port_grupos_x [ $cod_topico ]['figura'] = "icPasta3";
						// $port_grupos_x [ $cod_topico ]['linkavel'] = "Y";
					}
					else if ($grupos_usr[ $cod_grupo ])     // portfolio de um grupo ao qual o usuario pertence
					{
						$port_meus_grp [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
						// 77 - Portfolio do grupo
						$port_meus_grp [ $cod_topico ]['topico'] = _("PORTFOLIO_OF_GROUP_15")." ".$lista_grupos[$cod_grupo]['nome'];
						$port_meus_grp [ $cod_topico ]['figura'] = "icPasta";
						//$port_meus_grp [ $cod_topico ]['linkavel'] = "Y";
					}
					else //if ( ($tipo = $linha_port ['tipo_compartilhamento']) == 'T' || $tipo == $comp)
						// portfolio de um grupo ativo ao qual o usuario nao pertence, neste caso somente retornamos se o tipo de compartilhamento permitir
	
						// Rafael - 22/11/2004
					// Cancelei a restricao acima, agora todos portfolios sao retornados
					{
						$port_outros_grp [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
						// 77 - Portfolio do grupo
						$port_outros_grp [ $cod_topico ]['topico'] = _("PORTFOLIO_OF_GROUP_15")." ".$lista_grupos[$cod_grupo]['nome'];
						$port_outros_grp [ $cod_topico ]['figura'] = "icPasta2";
						// $port_outros_grp [ $cod_topico ]['linkavel'] = "Y";
					}
				}
	
				// copiando para o retorno os portfolios de grupos aos quais pertenco: copio todos aqueles aos quais pertenco
				if (is_array ($port_meus_grp))
				{
					uasort ($port_meus_grp, array('Portfolio', "CompareArrayTopicos"));
					foreach ($port_meus_grp as $cod_topico => $portfolio)
					{
						$lista_retorno [ $cod_topico ] = $portfolio;
						$lista_retorno [ $cod_topico ]['num_itens'] = $num_itens = Portfolio::RetornaNumItensPortfolio ($sock, $cod_topico, 'P');
						$lista_retorno [ $cod_topico ]['num_itens_comentados'] = $num_itens_c = Portfolio::RetornaNumItensComentadosPortfolio ($sock, $cod_topico, 'P');
						$lista_retorno [ $cod_topico ]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
						//  $lista_retorno [ $cod_topico ]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,'P');
					}
				}
	
				// copiando para o retorno os portfolios de grupos aos quais eu nao pertenco: copio apenas aqueles que tem algum item dentro
				if (is_array ($port_outros_grp))
				{
					uasort ($port_outros_grp, array('Portfolio', "CompareArrayTopicos"));
					foreach ($port_outros_grp as $cod_topico => $portfolio)
					{
						$num_itens = Portfolio::RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
						// LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
						// if ($num_itens > 0)
						{
							$lista_retorno [$cod_topico] = $portfolio;
							$lista_retorno [$cod_topico]['num_itens'] = $num_itens;
							$lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = Portfolio::RetornaNumItensComentadosPortfolio ($sock, $cod_topico, $comp);
							$lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
							//    $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
						}
					}
				}
				/*
				 // copiando para o retorno os portfolios de grupos apagados
				if (is_array ($port_grupos_x))
				{
				uasort ($port_grupos_x, CompareArrayTopicos);
				foreach ($port_grupos_x as $cod_topico => $portfolio)
				{
				$num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
				// LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
				// if ($num_itens > 0)
				{
				$lista_retorno [$cod_topico] = $portfolio;
				$lista_retorno [$cod_topico]['num_itens'] = $num_itens;
				$lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio ($sock, $cod_topico, $comp);
				$lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
				//     $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
				}
				}
				}
				*/ // Retirado portfolios de grupos apagados da lista de grupos
			}
		}
		else if ($acao_portfolio_s == 'F') {
			$query = "select * from Portfolio_topicos where cod_topico_pai=4";
			$res   = AcessoSQL::Enviar ($sock, $query);
			$lista = AcessoSQL::RetornaArrayLinhas ($res);
	
			if (is_array ($lista))
			{
				unset ($port_meus_grp);           // portfolios de grupos aos quais pertenco
				unset ($port_outros_grp);         // portfolios de grupos aos quais NAO pertenco
				unset ($port_grupos_x);           // portfolios de grupos apagados
	
				foreach ($lista as $c => $linha_port)
				{
	
					// SE UM GRUPO FOI APAGADO, O SEU PORTFOLIO SERA EXIBIDO INDEPENDENTE DO TIPO DE COMPARTILHAMENTO QUE ELE TINHA QDO O GRUPO ERA ATIVO
					if ($lista_grupos[ $cod_grupo = $linha_port['cod_grupo'] ]['status'] == 'X') // grupo apagado
					{
						$port_grupos_x [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
						// 77 - Portfolio do grupo
						$port_grupos_x [ $cod_topico ]['topico'] = _("PORTFOLIO_OF_GROUP_15")." ".$lista_grupos[$cod_grupo]['nome'];
						$port_grupos_x [ $cod_topico ]['figura'] = "icPasta3";
						// $port_grupos_x [ $cod_topico ]['linkavel'] = "Y";
					}
					/*        else if ($grupos_usr[ $cod_grupo ])     // portfolio de um grupo ao qual o usuario pertence
					 {
					$port_meus_grp [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
					// 77 - Portfolio do grupo
					$port_meus_grp [ $cod_topico ]['topico'] = RetornaFraseDaLista ($lista_frases, 77)." ".$lista_grupos[$cod_grupo]['nome'];
					$port_meus_grp [ $cod_topico ]['figura'] = "portfolio_g_p.gif";
					//$port_meus_grp [ $cod_topico ]['linkavel'] = "Y";
					}
					else //if ( ($tipo = $linha_port ['tipo_compartilhamento']) == 'T' || $tipo == $comp)
						// portfolio de um grupo ativo ao qual o usuario nao pertence, neste caso somente retornamos se o tipo de compartilhamento permitir
	
					// Rafael - 22/11/2004
					// Cancelei a restricao acima, agora todos portfolios sao retornados
					{
					$port_outros_grp [ $cod_topico = $linha_port['cod_topico'] ] = $linha_port;
					// 77 - Portfolio do grupo
					$port_outros_grp [ $cod_topico ]['topico'] = RetornaFraseDaLista ($lista_frases, 77)." ".$lista_grupos[$cod_grupo]['nome'];
					$port_outros_grp [ $cod_topico ]['figura'] = "portfolio_g_n.gif";
					// $port_outros_grp [ $cod_topico ]['linkavel'] = "Y";
					}
					*/ // Deixado somente a parte referente aos portfolios encerrados
				}
				/*
				 // copiando para o retorno os portfolios de grupos aos quais pertenco: copio todos aqueles aos quais pertenco
				if (is_array ($port_meus_grp))
				{
				uasort ($port_meus_grp, CompareArrayTopicos);
				foreach ($port_meus_grp as $cod_topico => $portfolio)
				{
				$lista_retorno [ $cod_topico ] = $portfolio;
				$lista_retorno [ $cod_topico ]['num_itens'] = $num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, 'P');
				$lista_retorno [ $cod_topico ]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio ($sock, $cod_topico, 'P');
				$lista_retorno [ $cod_topico ]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
				//  $lista_retorno [ $cod_topico ]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,'P');
				}
				}
	
				// copiando para o retorno os portfolios de grupos aos quais eu nao pertenco: copio apenas aqueles que tem algum item dentro
				if (is_array ($port_outros_grp))
				{
				uasort ($port_outros_grp, CompareArrayTopicos);
				foreach ($port_outros_grp as $cod_topico => $portfolio)
				{
				$num_itens = RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
				// LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
				// if ($num_itens > 0)
				{
				$lista_retorno [$cod_topico] = $portfolio;
				$lista_retorno [$cod_topico]['num_itens'] = $num_itens;
				$lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = RetornaNumItensComentadosPortfolio ($sock, $cod_topico, $comp);
				$lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
				//    $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
				}
				}
				}
				*/ // Deixado somente a parte referente aos portfolios encerrados
				// copiando para o retorno os portfolios de grupos apagados
				if (is_array ($port_grupos_x))
				{
					uasort ($port_grupos_x, array('Portfolio', "CompareArrayTopicos"));
					foreach ($port_grupos_x as $cod_topico => $portfolio)
					{
						$comp='P';
						$num_itens = Portfolio::RetornaNumItensPortfolio ($sock, $cod_topico, $comp);
						// LEONEL - 23/07/2003 - Cancelei esta restricao de somente retornar o portfolio se tiver itens. Agora, todo portfolio aparece no retorno.
						// if ($num_itens > 0)
						{
							$lista_retorno [$cod_topico] = $portfolio;
							$lista_retorno [$cod_topico]['num_itens'] = $num_itens;
							$lista_retorno [$cod_topico]['num_itens_comentados'] = $num_itens_c = Portfolio::RetornaNumItensComentadosPortfolio ($sock, $cod_topico, $comp);
							$lista_retorno [$cod_topico]['num_itens_nao_comentados'] = $num_itens - $num_itens_c;
							//     $lista_retorno [$cod_topico]['num_itens_nao_avaliados'] = $num_itens = RetornaNumItensNaoAvaliados($sock, $cod_topico,$comp);
						}
					}
				}
			}
	
		}
		else
		{
			echo("<big><font color=red>Erro em RetornaTopicosBase: variavel acao_portfolio_s</font></big>");
			exit();
		}
	
		return $lista_retorno;
	}
	
	/* *********************************************************************
	 RetornaPastaRaizUsuario - Retorna o c?igo da pasta raiz do usu?io ou grupo
	Entrada: $sock - BASE DO CURSO
	$cod_usuario - codigo do usuario que est?removendo, para o hist?ico
	$cod_grupo - codigo do grupo (NULL ou "" se portfolio individual)
	Saida: nenhuma
	*/
	static function RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo)
	{
		if ($cod_usuario==-2)
			return 'NULL';
		/* definindo regra de escolha de pasta */
		if ($cod_grupo=="NULL" || $cod_grupo=="")
		{
			$cod_topico_pai=3;
			$regra="(cod_usuario=".$cod_usuario." and cod_grupo IS NULL)";
			$cod_grupo="NULL";
			$rep="#U".$cod_usuario;
		}
		else
		{
			$cod_topico_pai=4;
			$regra="(cod_grupo=".$cod_grupo.")";
			$rep="#G".$cod_grupo;
		}
	
		$query="select cod_topico from Portfolio_topicos where ".$regra." and cod_topico_pai=".$cod_topico_pai;
		$res=AcessoSQL::Enviar($sock,$query);
		if (AcessoSQL::RetornaNumLinhas($res)>0)
		{
			$linha=AcessoSQL::RetornaLinha($res);
			return $linha['cod_topico'];
		}
		else
		{
			$query="insert into Portfolio_topicos (cod_topico_pai,cod_usuario,cod_grupo,tipo_compartilhamento,data,posicao_topico,topico) values (".$cod_topico_pai.",".$cod_usuario.",".$cod_grupo.",'P',".time().",".Portfolio::UltimaPosicaoLivre($sock, $cod_topico_pai).",'".$rep."')";
			AcessoSQL::Enviar($sock,$query);
	
			return ($cod_topico_pai);
		}
	}
	
	/* *********************************************************************
	 RetornaNumItensPortfolio - Retorna o numero de itens em um portf?io
	Entrada: $sock - BASE DO CURSO
	$cod_topico - codigo da pasta base
	$portfolios - 'P' - Todos os itens, 'F' - Itens tipo F e T, 'T' - SÛ tipo T
	Saida: numero de itens
	*/
	static function RetornaNumItensPortfolio($sock, $cod_topico,$portfolios)
	{
		$total=0;
		if ($portfolios=='T')
			$topico="tipo_compartilhamento='T' and ";
		else if ($portfolios=='F')
			$topico="tipo_compartilhamento<>'P' and ";
		else
			$topico="";
	
		$consulta="select cod_topico from Portfolio_topicos where ".$topico." cod_topico_pai=".$cod_topico;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
	
		if (count($lista))
		foreach($lista as $cod => $linha)
			$total = $total + Portfolio::RetornaNumItensPortfolio($sock, $linha['cod_topico'],$portfolios);
	
		$consulta="select count(cod_item) from Portfolio_itens where ".$topico." cod_topico=".$cod_topico;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$count = AcessoSQL::RetornaArrayLinhas($res);
		$total = $total + $count[0][0];
		return $total;
	}
	
	/* *********************************************************************
	 RetornaNumItensPortfolio - Retorna o numero de itens em um portf?io
	Entrada: $sock - BASE DO CURSO
	$cod_topico - codigo da pasta base
	$portfolios - 'P' - Todos os itens, 'F' - Itens tipo F e T, 'T' - S?tipo T
	Saida: numero de itens
	*/
	static function RetornaNumItensComentadosPortfolio($sock, $cod_topico,$portfolios)
	{
		$total=0;
		if ($portfolios=='T')
			$topico="Pi.tipo_compartilhamento='T' and ";
		else if ($portfolios=='F')
			$topico="Pi.tipo_compartilhamento<>'P' and ";
		else
			$topico="";
	
		$consulta="select cod_topico from Portfolio_topicos Pi where ".$topico." Pi.cod_topico_pai=".$cod_topico;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
	
		if (count($lista))
		foreach($lista as $cod => $linha)
			$total = $total + Portfolio::RetornaNumItensComentadosPortfolio($sock, $linha['cod_topico'],$portfolios);
	
		$consulta="select distinct Pi.cod_item from Portfolio_itens Pi, Portfolio_itens_comentarios Pic where ".$topico." Pi.cod_topico=".$cod_topico." and Pi.cod_item=Pic.cod_item and Pic.status<>'X'";
		$res=AcessoSQL::Enviar($sock, $consulta);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
	
		if ((count($lista))&&($lista!=""))
			$total+=count($lista);
	
		return $total;
	}
	
	static function CompareArrayTopicos($ar1, $ar2)
	{
		if ($ar1['topico']<$ar2['topico'])
			return -1;
		else if ($ar1['topico']>$ar2['topico'])
			return 1;
		else
			return 0;
	}
	
	/* *********************************************************************
	 RetornaMaiorDataItemComentario - Retorna a maior data de item ou coment?io
	dentro de um t?ico ou subt?ico
	Entrada: $sock - Conex? com a base interna
	$cod_topico - codigo do topico a ser checado
	$tipo_comp - tipo do compartilhamento (F ou T)
	$data - Data do t?ico
	$cod_usuario - usuario logado
	Saida: maior data
	*/
	static function RetornaMaiorDataItemComentario($sock,$cod_topico,$tipo_comp,$data,$cod_usuario)
	{
		$query="select cod_usuario,cod_grupo from Portfolio_topicos where cod_topico=".$cod_topico;
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		$cod_usuario_portfolio=$linha['cod_usuario'];
		$cod_grupo_portfolio=$linha['cod_grupo'];
	
		if ($cod_grupo_portfolio=="" && $cod_usuario==$cod_usuario_portfolio)
			$tipo_comp="P";
		else
		if (Portfolio::PertenceAoGrupo($sock,$cod_usuario,$cod_grupo_portfolio))
			$tipo_comp="P";
	
		$query="select cod_topico,data from Portfolio_topicos where cod_topico_pai=".$cod_topico;
		if ($tipo_comp=="T")
			$query.=" and tipo_compartilhamento='T'";
		if ($tipo_comp=="F")
			$query.=" and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
	
		$res=AcessoSQL::Enviar($sock,$query);
		$lista_topicos=AcessoSQL::RetornaArrayLinhas($res);
	
		/* Verifica se existem subt?icos */
		if (count($lista_topicos)>0)
		{
			foreach($lista_topicos as $cod=>$linha)
			{
				$data_tmp=Portfolio::RetornaMaiorDataItemComentario($sock,$linha['cod_topico'],$tipo_comp,$linha['data'],$cod_usuario);
				if ($data_tmp>$data)
					$data=$data_tmp;
			}
		}
	
		$query="select max(Pi.data) 'PiData', max(Pic.data) 'PicData' from Portfolio_itens Pi, Portfolio_itens_comentarios Pic where Pi.cod_topico=".$cod_topico." and Pi.cod_item=Pic.cod_item";
		if ($tipo_comp=="T")
			$query.=" and Pi.tipo_compartilhamento='T'";
		if ($tipo_comp=="F")
			$query.=" and (Pi.tipo_compartilhamento='T' or Pi.tipo_compartilhamento='F')";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		if ($linha['PiData']>$data)
			$data=$linha['PiData'];
		if ($linha['PicData']>$data)
			$data=$linha['PicData'];
	
	
		$query="select max(Pi.data) 'PiData' from Portfolio_itens Pi where Pi.cod_topico=".$cod_topico;
		if ($tipo_comp=="T")
			$query.=" and Pi.tipo_compartilhamento='T'";
		if ($tipo_comp=="F")
			$query.=" and (Pi.tipo_compartilhamento='T' or Pi.tipo_compartilhamento='F')";
		$res=AcessoSQL::Enviar($sock,$query);
	
		$linha=AcessoSQL::RetornaLinha($res);
		if ($linha['PiData']>$data)
			$data=$linha['PiData'];
	
		return $data;
	}
	
	/* *********************************************************************
	 PertenceAoGrupo - Retorna trua se o usuario pertence ao grupo e o grupo esta ativo
	Entrada: $sock        - BASE DO CURSO
	$cod_usuario - codigo do usuario
	$cod_grupo   - codigo do grupo
	Saida: True se pertence, false caso contrario
	*/
	static function PertenceAoGrupo($sock, $cod_usuario, $cod_grupo)
	{
		$cod_grupo = ConversorTexto::VerificaNumeroQuery($cod_grupo);
		if ($cod_grupo === '')
			return false;
	
		$query  = "select GU.cod_usuario ";
		$query .= "from Grupos_usuario GU, Grupos G ";
		$query .= "where GU.cod_grupo   = ".$cod_grupo."   and ";
		$query .=       "G.cod_grupo    = ".$cod_grupo."   and ";
		$query .=       "GU.cod_usuario = ".$cod_usuario;
		//$query .=       " and G.status<>'X'";
	
		$res = AcessoSQL::Enviar($sock,$query);
	
		if (AcessoSQL::RetornaNumLinhas($res)>0)
			return true;
		else
			return false;
	}
	
	/* *********************************************************************
	 RetornaDiretorio - Retorna o Diret?io da tabela de diretorios
	Entrada: $sock - BASE EXTERNA
	$item - nome do item a ser buscado
	Saida: string com o diret?io
	*/
	static function RetornaDiretorio($sock,$item)
	{
		$query="select diretorio from Diretorio where item='".$item."'";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		return ($linha[0]);
	}
	
	/* *********************************************************************
	 RetornaStatusPortfolio - retorna informacoes sobre o portfolio e como
	o usuario interage com ele
	entrada:   $sock                     - sock de conexao
	$cod_curso                - codigo do curso
	$cod_usuario              - codigo do usuario logado
	$cod_usuario_portfolio    - codigo do usuario dono do portfolio
	$cod_grupo                - codigo do grupo dono do portfolio
	
	saida:     array com 3 booleanos:
	['portfolio_grupo']   - se o portfolio eh de grupo
	['portfolio_apagado'] - se o portfolio estah apagado (grupo apagadoou aluno rejeitado)
	['dono_portfolio']    - se o usuario tem privilegios de dono do portfolio
	obs:
	- se o portfolio eh apagado, ninguem tem os privilegios de dono
	- se for um formador com visao de aluno, ele tb nao tem estes priviliegios
	- em cursos terminados, ninguem tem privilegio de dono, apenas formadores para seus portfolio
	*/
	static function RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo)
	{
		$retorno = "";
		unset ($retorno);
		$portfolio_grupo = ($cod_grupo !="" && $cod_grupo != "NULL");
	
		if ($portfolio_grupo)
		{
			$portfolio_apagado = ( Portfolio::StatusGrupo ($sock, $cod_grupo) == 'X' );
			$dono_portfolio    = Portfolio::PertenceAoGrupo($sock, $cod_usuario, $cod_grupo);
		}
		else
		{
			$portfolio_apagado = ( Usuarios::RetornaStatusUsuario($sock, $cod_curso, $cod_usuario_portfolio) == 'r' );
			$dono_portfolio    = ( $cod_usuario == $cod_usuario_portfolio );
		}
	
		$dono_portfolio &= ( !$portfolio_apagado && !Usuarios::EFormadorComVisaoDeAluno($sock, $cod_curso, $cod_usuario) );
	
		/* Verifica o t?mino do curso */
		$status_curso=Cursos::RetornaStatusCurso($sock,$cod_curso);
	
		if ($status_curso=='E' && !Usuarios::EFormador($sock, $cod_curso, $cod_usuario) )
			$dono_portfolio=false;
	
		$retorno ['portfolio_grupo']   = $portfolio_grupo;
		$retorno ['portfolio_apagado'] = $portfolio_apagado;
		$retorno ['dono_portfolio']    = $dono_portfolio;
	
		return $retorno;
	}
	
	/* *********************************************************************
	 StatusGrupo - Retorna o estado de um grupo
	Entrada: $sock - BASE DO CURSO
	$cod_grupo - grupo
	Saida: Status - A - Ativo, X - Apagado
	*/
	static function StatusGrupo($sock, $cod_grupo)
	{
		$consulta="select status from Grupos where cod_grupo=".$cod_grupo;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$num=AcessoSQL::RetornaNumLinhas($res);
		$linha=AcessoSQL::RetornaLinha($res);
		if ($linha['status']!="X")
			$linha['status']="A";
		return $linha['status'];
	}
	
	/* *********************************************************************
	 NaoExisteTop - Retorna verdadeiro se nao existir uma pasta com mesmo nome que novo_nome, com mesmo
	codigo_topico_pai e cod_usuario na tabela
	Entrada: $sock - BASE DO CURSO
	$cod_topico_pai - codigo do topico onde se encontra
	$topico - nome da pasta ou codigo da pasta
	$cod_usuario - codigo do usuario
	Saida: true, se nao existir topico com mesmo nome, false caso contrarsio
	*/
	static function NaoExisteTop($sock, $cod_topico_pai, $topico, $cod_usuario)
	{
		if (is_numeric($topico))
		{
			$consulta = "select topico from Portfolio_topicos where cod_topico = ".$topico;
			$res = AcessoSQL::Enviar($sock, $consulta);
			$linha = AcessoSQL::RetornaLinha($res);
			$topico = $linha['topico'];
		}
	
		$consulta = "select topico from Portfolio_topicos where cod_usuario = ".$cod_usuario." and cod_topico_pai = ".$cod_topico_pai." and topico = '".$topico."'";
	
		$res = AcessoSQL::Enviar($sock, $consulta);
		$num = AcessoSQL::RetornaNumLinhas($res);
	
		if ($num > 0)
			return false;
		return true;
	}
	
	/* *********************************************************************
	 RenomearTopico - Renomeia o t?ico
	Entrada: $sock - BASE DO CURSO
	$cod_topico - topico a ser renomear
	$novo_nome - novo nome a ser usado
	Saida: nenhuma
	*/
	static function RenomearTopico ($sock, $cod_topico, $novo_nome)
	{
		$consulta="update Portfolio_topicos set topico='".$novo_nome."' where cod_topico=".$cod_topico;
		$res=AcessoSQL::Enviar($sock, $consulta);
	}
	
	/* *********************************************************************
	 CriarTopico - Cria um novo topico filho do topico dado
	Entrada: $sock - BASE DO CURSO
	$cod_topico_raiz - topico pai
	$novo_nome - nome do novo topico
	$cod_usuario - codigo do usuario atual
	$cod_grupo - codigo do grupo do portfolio (NULL ou "" se individual
			Saida: codigo do topico criado
			*/
	static function CriarTopico ($sock, $cod_topico_raiz, $novo_nome, $cod_usuario, $cod_grupo)
	{
		if ($cod_grupo=="")
			$cod_grupo="NULL";
		$posicao=Portfolio::UltimaPosicaoLivre($sock, $cod_topico_raiz);
	
		$consulta="insert into Portfolio_topicos (cod_topico_pai, cod_usuario, cod_grupo, topico, tipo_compartilhamento, data, posicao_topico) values (".$cod_topico_raiz.", ".$cod_usuario.", ".$cod_grupo.", '".$novo_nome."', 'T', ".time().", ".$posicao.")";
		$res=AcessoSQL::Enviar($sock, $consulta);
	
		$consulta="select cod_topico from Portfolio_topicos where posicao_topico='".$posicao."' and cod_topico_pai='".$cod_topico_raiz."'";
		$res=AcessoSQL::Enviar($sock, $consulta);
		$res=AcessoSQL::RetornaLinha($res);
		$cod_topico=$res[0];
	
		return($cod_topico);
	}
	
	/* *********************************************************************
	 EPaiTopicos - Retorna verdadeiro se algum elemento de v_topicos for descendente de $cod_topico_dest
	Entrada: $sock - BASE DO CURSO
	$cod_topico_dest - codigo do topico pra onde serao movidos os v_topicos
	$v_topicos - codigos do topicos
	Saida: true, se For pai, false caso contrario
	*/
	static function EPaiTopicos($sock, $cod_topico_dest, $v_topicos)
	{
		for($i = 0; $i < count($v_topicos); $i++)
		{
		if(Portfolio::EPai($sock, $v_topicos[$i], $cod_topico_dest))
			return true;
		}
		return false;
	}
	

	/* *********************************************************************
	 EPai - Retorna verdadeiro se $cod_topico for descendente de $cod_topico
	Entrada: $sock - BASE DO CURSO
	$cod_topico_pai - codigo do topico pai
	$cod_topico - codigo do topico
	Saida: true, se For pai, false caso contr?io
	*/
	static function EPai($sock, $cod_topico_pai, $cod_topico)
	{
		if ($cod_topico_pai==$cod_topico)
			return(true);
		else
		{
			if ($cod_topico=="")
				return(false);
			else
			{
				$consulta="select cod_topico_pai from Portfolio_topicos where cod_topico=".cod_topico;
				$res=AcessoSQL::Enviar($sock, $consulta);
				$linha=AcessoSQL::RetornaLinha($res);
				return(Portfolio::EPai($sock, $cod_topico_pai, $linha['cod_topico_pai']));
			}
		}
	}
	
	/* *********************************************************************
	 MoverTopico - Move o topico para outra pasta
	Entrada: $sock - BASE DO CURSO
	$cod_topico - topico a ser movido
	$cod_usuario - usu?io realizando a?o
	$cod_topico_dest - topico destino
	Saida: true se nova posi?o for v?ida
	*/
	static function MoverTopico ($sock, $cod_topico, $cod_usuario, $cod_topico_dest)
	{
		if (Portfolio::EPai($sock, $cod_topico, $cod_topico_dest))
		{
			/* Erro! */
			return false;
		}
		else
		{
			$consulta="select cod_topico_pai, tipo_compartilhamento from Portfolio_topicos where cod_topico=".$cod_topico;
			$res=AcessoSQL::Enviar($sock, $consulta);
			$linha=AcessoSQL::RetornaLinha($res);
			$posicao_topico=Portfolio::UltimaPosicaoLivre($sock, $cod_topico_dest);
			/* Movendo topico */
			$consulta="update Portfolio_topicos set cod_topico_pai=".$cod_topico_dest.", posicao_topico=".$posicao_topico." where cod_topico=".$cod_topico;
			$res=AcessoSQL::Enviar($sock, $consulta);
			Portfolio::AtualizaCompartilhamentoPastas($sock, $linha['cod_topico_pai'], "P");
			Portfolio::AtualizaCompartilhamentoPastas($sock, $cod_topico_dest, $linha['tipo_compartilhamento']);
			return true;
		}
	}
	
	/* *********************************************************************
	 AtualizaCompartilhamentoPastas - Atualiza o compartilhamento das pastas (recursivamente)
	Entrada: $sock - BASE DO CURSO
	$cod_topico - codigo do topico
	$comp - Compartilhamento a ser setado (F ou T);
	Saida: nenhuma
	*/
	static function AtualizaCompartilhamentoPastas ($sock, $cod_topico, $comp)
	{
		if ($cod_topico>4)
		{
			if ($comp=="P")
			{
				// Verificamos se a pasta nao tem nenhum item ou filho compartilhado
				// Entao atualizamos o compartilhamento da pasta e dos pais
	
				$query  = "select tipo_compartilhamento from Portfolio_itens where cod_topico=".$cod_topico." and (tipo_compartilhamento='T' or tipo_compartilhamento='F') order by tipo_compartilhamento desc limit 1";
				$res    = AcessoSQL::Enviar($sock, $query);
				$linha1 = AcessoSQL::RetornaLinha($res);
	
				$query  = "select tipo_compartilhamento from Portfolio_topicos where cod_topico_pai=".$cod_topico." and (tipo_compartilhamento='T' or tipo_compartilhamento='F') order by tipo_compartilhamento desc limit 1";
				$res    = AcessoSQL::Enviar($sock, $query);
				$linha2 = AcessoSQL::RetornaLinha($res);
	
		  $compartilhamento = "";
		  unset ($compartilhamento);
	
		  if (is_array($linha1))
		  {
		  	// Existe um item compartilhado. O compartilhamento da pasta eh o mesmo deste item.
		  	$compartilhamento = $linha1['tipo_compartilhamento'];
		  }
		  if (is_array($linha2))
		  {
		  	// Existe um filho compartilhado. O comp da pasta eh o de maior prioridade entre o filho e o item acima.
		  	if (!isset($compartilhamento) || $compartilhamento != 'T')
		  		$compartilhamento = $linha2['tipo_compartilhamento'];
		  }
		  if (!isset($compartilhamento))
		  	// N? existem itens nem pastas comp. A pasta eh n? comp.
		  	$compartilhamento = 'P';
			}
			else if ($comp=="F")
			{
				// Significa que um item ou filho desta pasta eh comp. com forms.
				// Entao verificamos se esta pasta ou um filho dela nao eh compartilhado com todos
				$consulta="select cod_item from Portfolio_itens where cod_topico=".$cod_topico." and tipo_compartilhamento='T' limit 1";
				$res=AcessoSQL::Enviar($sock, $consulta);
				$num1=AcessoSQL::RetornaNumLinhas($res);
				$consulta="select cod_topico from Portfolio_topicos where cod_topico_pai=".$cod_topico." and tipo_compartilhamento='T' limit 1";
				$res=AcessoSQL::Enviar($sock, $consulta);
				$num2=AcessoSQL::RetornaNumLinhas($res);
	
				if ($num1==0 && $num2==0)
				{
					// Nenhum item ou filho eh compartilhado com todos,
					// Compartilhamos a pasta e seu pai com forms.
					$compartilhamento = 'F';
				}
				else
				{
					// Algum item ou filha eh compartilhado com todos,
					// Compartilhamos a pasta e seu pai com todos
					$compartilhamento = 'T';
				}
			}
			else
			{
				// aqui, $comp == 'T'
				// O que significa que um filho ou um item desta pasta eh compartilhado com todos
				// Entao esta pasta e seu pai devem ser compartilhados com todos
				$compartilhamento = 'T';
			}
	
			// Compartilhando a pasta
			$consulta="update Portfolio_topicos set tipo_compartilhamento='".$compartilhamento."' where cod_topico=".$cod_topico;
			$res=AcessoSQL::Enviar($sock, $consulta);
	
			// Encontrando o pai para atualizar.
			$consulta="select cod_topico_pai from Portfolio_topicos where cod_topico=".$cod_topico;
			$res=AcessoSQL::Enviar($sock, $consulta);
			$linha=AcessoSQL::RetornaLinha($res);
	
			Portfolio::AtualizaCompartilhamentoPastas($sock,  $linha['cod_topico_pai'], $compartilhamento);
		}
	}
	
	/* *********************************************************************
	 ArrumaPosicoes - rearranja as posicoes dos itens/topicos em um topico qdo um item/topico È movido
	entrada: $sock - sock de conexao
	$cod_topico - codigo do topico no qual arrumar as posicoes dos itens
	saida: nenhuma
	*/
	static function ArrumaPosicoes($sock, $cod_topico)
	{
		AcessoSQL::PegaSemaforo($sock, "Portfolio");
	
		$query="select * from Portfolio_itens where cod_topico=".$cod_topico." order by posicao_item asc";
		$res = AcessoSQL::Enviar($sock, $query);
		$lista_itens_ord=AcessoSQL::RetornaArrayLinhas($res);
	
		$query="select * from Portfolio_topicos where cod_topico_pai=".$cod_topico." order by posicao_topico asc";
		$res = AcessoSQL::Enviar($sock, $query);
		$lista_topicos_ord=AcessoSQL::RetornaArrayLinhas($res);
	
		$top_index = 0;
		$itens_index = 0;
	
		for($i=0; $i<((count($lista_topicos_ord))+(count($lista_itens_ord))); $i++){
			if((!isset($lista_topicos_ord[$top_index]['posicao_topico'])) || (isset($lista_itens_ord[$itens_index]['posicao_item']) &&($lista_topicos_ord[$top_index]['posicao_topico'] > $lista_itens_ord[$itens_index]['posicao_item']))) {
				$lista_unificada[$i] = $lista_itens_ord[$itens_index];
				$itens_index++;
			}else{
				//este if È para n„o alterar a estrutura dos portfÛlios antigos
				if((isset($lista_itens_ord[$top_index]['posicao_item'])) && ($lista_topicos_ord[$top_index]['posicao_topico'] == $lista_itens_ord[$itens_index]['posicao_item'])) {
					$lista_itens_ord[$itens_index]['posicao_item']++;
				}
				$lista_unificada[$i] = $lista_topicos_ord[$top_index];
				$top_index++;
			}
		}
	
		foreach($lista_unificada as $cod => $linha){
			//È item
			if(isset($linha['posicao_item'])){
				$update = "update Portfolio_itens set posicao_item=".($cod+1)." where cod_item = ".$linha['cod_item'];
				AcessoSQL::Enviar($sock, $update);
			}
			//È tÛpico
			else if(isset($linha['posicao_topico'])) {
				$update = "update Portfolio_topicos set posicao_topico=".($cod+1)." where cod_topico = ".$linha['cod_topico'];
				AcessoSQL::Enviar($sock, $update);
			}
	
		}
	
		AcessoSQL::LiberaSemaforo($sock, "Portfolio");
	}
	
	/* *********************************************************************
	 RetornaTopicosAncestrais - Retorna array com os t?icos ancestrais do t?ico dado
	Entrada: $sock - BASE DO CURSO
	$cod_topico - Codigo do topico
	Saida: array [] - topicos (em ordem crescente de ancestralidade)
	*/
	static function RetornaTopicosAncestrais ($sock, $cod_topico)
	{
		$cont=0;
		do
		{
			$consulta="select * from Portfolio_topicos where cod_topico=".$cod_topico;
			$res=AcessoSQL::Enviar($sock, $consulta);
			$linha=AcessoSQL::RetornaLinha($res);
			$cod_topico=$linha['cod_topico_pai'];
			$retorno[$cont]=$linha;
			$cont++;
		}
		while ($cod_topico>4); /* SÛ desce atÈ os portfÛlios */
		// 124 - Raiz
		$retorno[$cont - 1]['topico']=_("ROOT_-1");
	
		return($retorno);
	}
	
	/* *********************************************************************
	 NomeGrupo - Retorna o nome do grupo
	Entrada: $sock - BASE DO CURSO
	$cod_grupo - grupo
	Saida: nome do grupo
	*/
	static function NomeGrupo($sock, $cod_grupo)
	{
		$consulta="select nome from Grupos where cod_grupo=".$cod_grupo;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$num=AcessoSQL::RetornaNumLinhas($res);
		$linha=AcessoSQL::RetornaLinha($res);
		return $linha['nome'];
	}
	
	/* *********************************************************************
	 RetornaTopicosDoTopico - Retorna array com os topicos filhos do t?ico dado
	Entrada: $sock - BASE DO CURSO
	$cod_curso - codigo do curso
	$cod_topico - t?ico
	$cod_usuario - codigo do usuario logado
	$eformador - true se o usuario logado for formador
	$cod_usuario_portfolio - codigo do usuario dono do portfolio
	$cod_grupo_portfolio - codigo do grupo dono do portfolio (NULL ou "" se individual
			Saida: array com []['cod_topico']
			[]['cod_topico_pai']
			[]['cod_usuario']
			[]['cod_grupo']
			[]['topico']
			[]['tipo_compartilhamento']
			[]['data']
			[]['posicao_topico']
			*/
	static function RetornaTopicosDoTopico ($sock, $cod_curso, $cod_topico, $cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio)
	{
		if ($cod_usuario==$cod_usuario_portfolio)
			$regra="";
		else if ($cod_grupo_portfolio=="NULL" || $cod_grupo_portfolio=="")
		{
			if ($eformador)
				$regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
			else
				$regra="and tipo_compartilhamento='T'";
		}
		else
		{
			if (Portfolio::PertenceAoGrupo($sock, $cod_usuario, $cod_grupo_portfolio))
				$regra="";
			else if ($eformador)
				$regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
			else
				$regra="and tipo_compartilhamento='T'";
		}
	
		if (Usuarios::EFormadorComVisaoDeAluno($sock, $cod_curso, $cod_usuario))
			$regra="and tipo_compartilhamento='T'";
	
		$consulta="select * from Portfolio_topicos where cod_topico_pai=".$cod_topico." ".$regra." order by posicao_topico";
	
		$res=AcessoSQL::Enviar($sock, $consulta);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		return($lista);
	}
	
	/* *********************************************************************
	 RetornaItensDoTopico - Retorna array com os itens do t?ico dado
	Entrada: $sock - BASE DO CURSO
	$cod_curso - codigo do curso
	$cod_topico - t?ico
	$cod_usuario - codigo do usuario logado
	$eformador - true se o usuario logado for formador
	$cod_usuario_portfolio - codigo do usuario dono do portfolio
	$cod_grupo_portfolio - codigo do grupo dono do portfolio (NULL ou "" se individual
			Saida: array com []['cod_item']
			[]['cod_topico']
			[]['cod_usuario']
			[]['cod_grupo']
			[]['titulo']
			[]['texto']
			[]['tipo_compartilhamento']
			[]['data']
			[]['data_ativo']
			[]['data_inativo']
			[]['posicao_item']
			[]['status']
			[]['inicio_edicao']
			[]['num_comentarios']
			[]['num_comentarios_alunos'] - numero de coment?ios feitos por alunos que n? o usuario
			[]['num_comentarios_formadores'] - numero de coment?ios feitos por formadores que n? o usuario
			[]['num_comentarios_usuario'] - numero de coment?ios feitos pelo usuario
			[]['data_comentarios'] - Maior data de um coment?io
	
			*/
	static function RetornaItensDoTopico ($sock, $cod_curso, $cod_topico, $cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio)
	{
		if ($cod_grupo_portfolio=="NULL" || $cod_grupo_portfolio=="")
		{
			// Rafael - 22/11/2004 - Movi esta condeicao para dentro de portfolios Individuais pois para
			// alguem ver items de grupos nao compartilhados, a pessoal deve ser integrante do grupo
			if ($cod_usuario==$cod_usuario_portfolio)
				$regra="";
			else if ($eformador)
				$regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
			else
				$regra="and tipo_compartilhamento='T'";
		}
		else
		{
			if (Portfolio::PertenceAoGrupo($sock,$cod_usuario, $cod_grupo_portfolio))
				$regra="";
			else if ($eformador)
				$regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
			else
				$regra="and tipo_compartilhamento='T'";
		}
	
		if (Usuarios::EFormadorComVisaoDeAluno($sock, $cod_curso, $cod_usuario))
			$regra="and tipo_compartilhamento='T'";
	
		$consulta="select * from Portfolio_itens where cod_topico=".$cod_topico." ".$regra." order by posicao_item";
		$res=AcessoSQL::Enviar($sock, $consulta);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		if (count($lista))
		foreach($lista as $cod => $linha)
		{
			$tmp=Portfolio::RetornaNumComentariosItem ($sock, $cod_usuario, $linha['cod_item']);
			$lista[$cod]['num_comentarios']=$tmp['num_comentarios'];
			$lista[$cod]['num_comentarios_alunos']=$tmp['num_comentarios_alunos'];
			$lista[$cod]['num_comentarios_formadores']=$tmp['num_comentarios_formadores'];
			$lista[$cod]['num_comentarios_usuario']=$tmp['num_comentarios_usuario'];
			$lista[$cod]['data_comentarios']=$tmp['data_comentarios'];
		}
	
		return($lista);
	}
	
	/* *********************************************************************
	 RetornaNumComentariosItem - retorna o nmero de coment?ios de um item
	Entrada: $sock - BASE DO CURSO
	$cod_usuario - cod. usu?io logado
	$cod_item - item
	Saida: array com ['num_comentarios']
	['num_comentarios_alunos'] - numero de coment?ios feitos por alunos que n? o usuario
	['num_comentarios_formadores'] - numero de coment?ios feitos por formadores que n? o usuario
	['num_comentarios_usuario'] - numero de coment?ios feitos pelo usuario
	['data_comentarios'] - Maior data de um coment?io
	*/
	static function RetornaNumComentariosItem ($sock, $cod_usuario, $cod_item)
	{
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
	
		$query="select Pic.cod_comentarista 'cod', count(*) 'ct', max(Pic.data) 'dt' from Portfolio_itens_comentarios Pic where Pic.cod_item=".$cod_item." and Pic.status='A' group by Pic.cod_comentarista order by Pic.cod_comentarista";
		$res=AcessoSQL::Enviar($sock,$query);
	
		$num_f=0;
		$num_a=0;
		$num_u=0;
		$data=0;
	
		while ($linha=AcessoSQL::RetornaLinha($res))
		{
			$query2="select tipo_usuario 'tu' from ".$dbnamebase.".Usuario_curso where cod_usuario='".$cod_usuario."' and cod_curso='".$_SESSION['cod_curso']."'";
			$res2=AcessoSQL::Enviar($sock,$query2);
			$linha2=AcessoSQL::RetornaLinha($res);
			$linha['tu'] = $linha2['tu'];
	
			if ($linha['cod']==$cod_usuario)
				$num_u+=$linha['ct'];
			else if ($linha['tu']=="F")
				$num_f+=$linha['ct'];
			else
				$num_a+=$linha['ct'];
			if ($data<$linha['dt'])
				$data=$linha['dt'];
		}
		$saida['num_comentarios']=$num_f+$num_a+$num_u;
		$saida['num_comentarios_formadores']=$num_f;
		$saida['num_comentarios_alunos']=$num_a;
		$saida['num_comentarios_usuario']=$num_u;
		$saida['data_comentarios']=$data;
		return $saida;
	}
	
	/* *********************************************************************
	 RetornaMaiorData - Retorna a maior data dentro de um t?ico ou subt?ico
	Entrada: $sock - Conex? com a base interna
	$cod_topico - codigo do topico a ser checado
	$tipo_comp - tipo do compartilhamento (F ou T)
	$data - Data do t?ico
	Saida: maior data
	*/
	static function RetornaMaiorData($sock,$cod_topico,$tipo_comp,$data)
	{
		$query="select cod_topico,data from Portfolio_topicos where cod_topico_pai=".$cod_topico;
		if ($tipo_comp=="T")
			$query.=" and tipo_compartilhamento='T'";
		if ($tipo_comp=="F")
			$query.=" and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
	
		$res=AcessoSQL::Enviar($sock,$query);
		$lista_topicos=AcessoSQL::RetornaArrayLinhas($res);
	
		/* Verifica se existem subt?icos */
		if (count($lista_topicos)>0)
		{
			foreach($lista_topicos as $cod => $linha)
			{
				$data_tmp=Portfolio::RetornaMaiorData($sock,$linha['cod_topico'],$tipo_comp,$linha['data']);
				if ($data_tmp>$data)
					$data=$data_tmp;
			}
		}
	
		$query="select max(data) from Portfolio_itens where cod_topico=".$cod_topico;
		if ($tipo_comp=="T")
			$query.=" and tipo_compartilhamento='T'";
		if ($tipo_comp=="F")
			$query.=" and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		if ($linha[0]>$data)
			$data=$linha[0];
		return $data;
	}
	
	/* *********************************************************************
	 RetornaNumComentariosTopico - Retorna a maior data dentro de um t?ico ou subt?ico
	Entrada: $sock - Conex? com a base interna
	$cod_usuario - usuario
	$cod_topico - codigo do topico a ser checado
	$tipo_comp - tipo do compartilhamento (F ou T)
	$data - Data do t?ico
	Saida: array com ['num_comentarios']
	['num_comentarios_alunos'] - numero de coment?ios feitos por alunos que n? o usuario
	['num_comentarios_formadores'] - numero de coment?ios feitos por formadores que n? o usuario
	['num_comentarios_usuario'] - numero de coment?ios feitos pelo usuario
	['data_comentarios'] - Maior data de um coment?io
	*/
	static function RetornaNumComentariosTopico($sock,$cod_usuario,$cod_topico,$tipo_comp,$data, $cod_curso)
	{
		// 2Session
		$dbnamebase = $_SESSION['dbnamebase'];
	
		$num_f=0;
		$num_a=0;
		$num_u=0;
		$data=0;
	
		$query="select cod_topico,data from Portfolio_topicos where cod_topico_pai=".$cod_topico;
		if ($tipo_comp=="T")
			$query.=" and tipo_compartilhamento='T'";
		if ($tipo_comp=="F")
			$query.=" and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
	
		$res=AcessoSQL::Enviar($sock,$query);
		$lista_topicos=AcessoSQL::RetornaArrayLinhas($res);
	
		/* Verifica se existem subt?icos */
		if (count($lista_topicos)>0)
		{
			foreach($lista_topicos as $cod=>$linha)
			{
				$num_tmp=Portfolio::RetornaNumComentariosTopico($sock,$cod_usuario,$linha['cod_topico'],$tipo_comp,$linha['data'], $cod_curso);
				$num_f += $num_tmp['num_comentarios_formadores'];
				$num_a += $num_tmp['num_comentarios_alunos'];
				$num_u += $num_tmp['num_comentarios_usuario'];
				if ($data < $num_tmp['data_comentarios'])
					$data = $num_tmp['data_comentarios'];
			}
		}
	
		/* checa o nivel atual */
	
		$query="select Pic.cod_comentarista 'cod', UC.tipo_usuario 'tu', count(*) 'ct', max(Pic.data) 'dt'
          from Portfolio_itens_comentarios Pic, Portfolio_itens Pi, ".$dbnamebase.".Usuario_curso UC
          where Pic.cod_comentarista=UC.cod_usuario
          and Pic.cod_item=Pi.cod_item
          and Pi.cod_topico=".$cod_topico."
          and Pic.status='A'
          and UC.cod_curso='".$cod_curso."'";
	
		if ($tipo_comp=="T")
			$query.=" and Pi.tipo_compartilhamento='T'";
		if ($tipo_comp=="F")
			$query.=" and (Pi.tipo_compartilhamento='T' or Pi.tipo_compartilhamento='F')";
		$query.=" group by Pic.cod_comentarista order by Pic.cod_comentarista";
	
		$res=AcessoSQL::Enviar($sock,$query);
	
		while ($linha=AcessoSQL::RetornaLinha($res))
		{
			if ($linha['cod']==$cod_usuario)
				$num_u+=$linha['ct'];
			else if ($linha['tu']=="F")
				$num_f+=$linha['ct'];
			else
				$num_a+=$linha['ct'];
			if ($data<$linha['dt'])
				$data=$linha['dt'];
		}
		$saida['num_comentarios']=$num_f+$num_a+$num_u;
		$saida['num_comentarios_formadores']=$num_f;
		$saida['num_comentarios_alunos']=$num_a;
		$saida['num_comentarios_usuario']=$num_u;
		$saida['data_comentarios']=$data;
		return $saida;
	}
	
	/* *********************************************************************
	 RetornaUltimaPosicaoHistorico - Retorna a ultima ocorr?cia do historico de um item dado
	Entrada: $sock - BASE DO CURSO
	$cod_item - item
	Saida: array ['cod_item']
	['cod_usuario']
	['data']
	['acao']
	*/
	static function RetornaUltimaPosicaoHistorico ($sock, $cod_item)
	{
		$consulta="select * from Portfolio_itens_historicos where cod_item=".$cod_item." order by data desc limit 1";
		$res=AcessoSQL::Enviar($sock, $consulta);
		$linha=AcessoSQL::RetornaLinha($res);
		return($linha);
	}
	
	/* *********************************************************************
	 CancelaEdicao - Cancela a edicao de um item (termina os diret?ios tb).
	Entrada: $sock - BASE DO CURSO
	$cod_item - codigo do item
	$cod_usuario - usuario cancelando a edicao
	$cod_curso - codigo do curso
	$diretorio_arquivos - diretorio dos arquivos definitivos
	$diretorio_temp - diretorio temporario da edicao
	Saida: true se a edi?o foi cancelada corretamente
	*/
	static function CancelaEdicao($sock, $cod_item, $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_associacao,$estava_associado,$manter_avaliacao)
	{
		$dirtemp=$diretorio_temp."/tmp/".$cod_curso."/portfolio/".$cod_item."/";
		if (file_exists($dirtemp))
		{
			if (!Arquivos::RemoveDiretorio($dirtemp))
				return false;
		}
		$linha=Portfolio::RetornaDadosDoItem($sock, $cod_item);
		if ($linha['status']=="C")
		{
			$consulta="delete from Portfolio_itens_enderecos where cod_item=".$cod_item;
			$res=AcessoSQL::Enviar($sock, $consulta);
	
			$consulta="delete from Portfolio_itens_historicos where cod_item=".$cod_item;
			$res=AcessoSQL::Enviar($sock, $consulta);
	
			$consulta="delete from Portfolio_itens where cod_item=".$cod_item;
			$res=AcessoSQL::Enviar($sock, $consulta);
	
			$consulta="delete from Portfolio_itens_avaliacao where cod_item=".$cod_item;
			$res=AcessoSQL::Enviar($sock, $consulta);
	
			return true;
		}
		if ($linha['status']=="E")
		{
			$consulta="delete from Portfolio_itens_enderecos where cod_item=".$cod_item." and status='I'";
			$res=AcessoSQL::Enviar($sock, $consulta);
			$consulta="update Portfolio_itens_enderecos set status='F' where cod_item=".$cod_item;
			$res=AcessoSQL::Enviar($sock, $consulta);
	
			$consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'D')";
			$res=AcessoSQL::Enviar($sock, $consulta);
	
			$consulta="update Portfolio_itens set status='L' where cod_item=".$cod_item;
			$res=AcessoSQL::Enviar($sock, $consulta);
	
			if ($criacao_associacao)
			{
				$consulta="delete from Portfolio_itens_avaliacao where cod_item=".$cod_item;
				$res=AcessoSQL::Enviar($sock, $consulta);
			}
			/* elseif ($estava_associado)
			{
				$consulta="delete from Portfolio_itens_avaliacao where cod_item=".$cod_item;
				$res=AcessoSQL::Enviar($sock, $consulta);
	
				$consulta="insert into Portfolio_itens_avaliacao values (".$cod_item.", ".$manter_avaliacao.",0,'N')";
				$res=Enviar($sock, $consulta);
			} */
	
			return true;
		}
	}
	
	/* *********************************************************************
	 RetornaDadosDoItem - Retorna um array com os dados do item
	Entrada: $sock - BASE DO CURSO
	$cod_topico - t?ico
	Saida: array com ['cod_item']
	['cod_topico']
	['cod_usuario']
	['cod_grupo']
	['titulo']
	['texto']
	['tipo_compartilhamento']
	['data']
	['data_ativo']
	['data_inativo']
	['posicao_item']
	['status']
	['inicio_edicao']
	*/
	static function RetornaDadosDoItem ($sock, $cod_item)
	{
		$consulta="select * from Portfolio_itens where cod_item=".$cod_item;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$linha=AcessoSQL::RetornaLinha($res);
		return($linha);
	}
	
	/* *********************************************************************
	 RetornaListaDeTopicos - retorna array com lista de topicos
	Entrada: $sock - BASE DO CURSO
	$cod_usuario - codigo do usuario logado
	$eformador - true se o usuario logado for formador
	$cod_usuario_portfolio - codigo do usuario dono do portfolio
	$cod_grupo_portfolio - codigo do grupo dono do portfolio (NULL ou "" se individual
			Saida: array []['cod_topico']
			[]['cod_topico_pai']
			[]['cod_usuario']
			[]['cod_grupo']
			[]['topico']
			[]['tipo_compartilhamento']
			[]['data']
			[]['posicao_topico']
			[]['nivel'] - Nivel de recurs? at?atingir o t?ico
			[]['espacos'] - espacos que ser? usados para exibi?o (&nbsp;)
			*/
	static function RetornaListaDeTopicos ($sock,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio)
	{
		/* Pegando a estrutura do usu?io sendo visualizado */
		$cod_raiz=Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario_portfolio,$cod_grupo_portfolio);
	
		$consulta="select * from Portfolio_topicos where cod_topico=".$cod_raiz;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$linha=AcessoSQL::RetornaLinha($res);
		/* 37 - Pasta Raiz */
		$linha['topico']=_("ROOT_FOLDER_-1");
		$retorno[0]=$linha;
		$retorno[0]['nivel']=0;
		$retorno[0]['espacos']="";
		$topicos=Portfolio::RetornaListaDeTopicosRecursao($sock, $linha,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio);
		$i=1;
		if (count($topicos))
		{
			foreach ($topicos as $cod => $topico)
			{
				$retorno[$i]=$topico;
				$retorno[$i]['nivel']++;
				$retorno[$i]['espacos']=$retorno[$i]['espacos']."&nbsp;&nbsp;";
				$i++;
			}
		}
		return($retorno);
	}
	
	/* *********************************************************************
	 RetornaListaDeTopicosRecursao - retorna array com lista de topicos a partir de um topico dado
	Entrada: $sock - BASE DO CURSO
	$topico - array com 1 elemento []['cod_topico'], contendo o c?igo do t?ico
	$cod_usuario - codigo do usuario logado
	$eformador - true se o usuario logado for formador
	$cod_usuario_portfolio - codigo do usuario dono do portfolio
	$cod_grupo_portfolio - codigo do grupo dono do portfolio (NULL ou "" se individual
			Saida: array []['cod_topico']
			[]['cod_topico_pai']
			[]['cod_usuario']
			[]['cod_grupo']
			[]['topico']
			[]['tipo_compartilhamento']
			[]['data']
			[]['posicao_topico']
			[]['nivel'] - Nivel de recurs? at?atingir o t?ico
			[]['espacos'] - espacos que ser? usados para exibi?o (&nbsp;)
			*/
	static function RetornaListaDeTopicosRecursao ($sock, $topico,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio)
	{
		if ($cod_usuario==$cod_usuario_portfolio)
			$regra="";
		else if ($cod_grupo_portfolio=="NULL" || $cod_grupo_portfolio=="")
		{
			if ($eformador)
				$regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
			else
				$regra="and tipo_compartilhamento='T'";
		}
		else
		{
			if (Portfolio::PertenceAoGrupo($sock, $cod_usuario, $cod_grupo_portfolio))
				$regra="";
			else if ($eformador)
				$regra="and (tipo_compartilhamento='T' or tipo_compartilhamento='F')";
			else
				$regra="and tipo_compartilhamento='T'";
		}
	
		$consulta="select * from Portfolio_topicos where cod_topico_pai=".$topico['cod_topico']." ".$regra." order by posicao_topico";
		$res=AcessoSQL::Enviar($sock, $consulta);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		$i=0;
		if (isset($lista_retorno)) reset($lista_retorno);
		$lista_retorno = array();
		if (count($lista))
		foreach ($lista as $cod1 => $linha1)
		{
			$lista_retorno[$i]=$linha1;
			$lista_retorno[$i]['nivel']=0;
			$lista_retorno[$i]['espacos']="";
	
			$i++;
			$lista_filho=Portfolio::RetornaListaDeTopicosRecursao($sock, $linha1,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio);
			if (count($lista_filho)>0)
			foreach ($lista_filho as $cod2 => $linha2)
			{
				$lista_retorno[$i]=$linha2;
				$lista_retorno[$i]['nivel']++;
				$lista_retorno[$i]['espacos']=$lista_retorno[$i]['espacos']."&nbsp;&nbsp;";
	
				$i++;
			}
		}
		return($lista_retorno);
	}
	
	static function RetornaArquivosMaterialVer ($cod_curso, $dirlink)
	{
		return (Arquivos::RetornaArrayDiretorio($dirlink));
	}
	
	/* *********************************************************************
	 MudarCompartilhamentoEAtualiza - Muda o compartilhamento do item dado
	Entrada: $sock - BASE DO CURSO
	$cod_item - item a ser alterado compartilhamento
	$tipo_comp - Tipo do Compartilhamento (F ou T)
	Saida: nenhuma
	*/
	static function MudarCompartilhamentoEAtualiza($dadosForm, $novo_comp){
		$cod_curso=$dadosForm['cod_curso'];
		$sock=AcessoSQL::Conectar($cod_curso);
	
		$cod_grupo = Portfolio::RetornaGrupoPortfolio($sock, $dadosForm['cod_item']);
		$em_grupo = ($cod_grupo != NULL );
		if ($em_grupo){
			Usuarios::AtualizaFerramentasNovaGrupo($sock,"15",$cod_grupo);
			Usuarios::AtualizaFerramentasNova($sock, "15", $dadosForm['tipo_comp']);
		} else {
			Usuarios::AtualizaFerramentasNovaUsuario($sock,"15", $dadosForm['cod_usuario']);
			Usuarios::AtualizaFerramentasNova($sock, "15", $dadosForm['tipo_comp']);
		}
		return (Portfolio::MudarCompartilhamento($dadosForm, $novo_comp));
	}
	
	/* *********************************************************************
	 MudarCompartilhamento - Muda o compartilhamento do item dado
	Entrada: $sock - BASE DO CURSO
	$cod_item - item a ser alterado compartilhamento
	$tipo_comp - Tipo do Compartilhamento (F ou T)
	Saida: nenhuma
	*/
	static function MudarCompartilhamento ($cod_curso, $cod_item, $cod_usuario, $tipo_comp, $novo_comp)
	{
		$sock=AcessoSQL::Conectar($cod_curso);
		$query="select tipo_compartilhamento from Portfolio_itens where cod_item=".$cod_item;
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		if ($linha['tipo_compartilhamento']!=$tipo_comp)
		{
			$consulta="update Portfolio_itens set tipo_compartilhamento='".$tipo_comp."', data=".time()." where cod_item=".$cod_item;
			$res=AcessoSQL::Enviar($sock, $consulta);
			$consulta="select cod_topico from Portfolio_itens where cod_item=".$cod_item;
			$res=AcessoSQL::Enviar($sock, $consulta);
			$linha=AcessoSQL::RetornaLinha($res);
			Portfolio::AtualizaCompartilhamentoPastas($sock, $linha['cod_topico'], $tipo_comp);
		}
	
		AcessoSQL::Desconectar($sock);
	
		// Imprime no div valores do formul?io
		//$objResponse->assign("tr_".$cod_item, "className", "novoitem");
		//$objResponse->assign("comp_".$cod_item, "innerHTML", print_r($novo_comp, true));
		//addEvent n„o suporta o parametro 'event'
		//$objResponse->script("document.getElementById('comp_".$cod_item."').onclick = function(event) { js_cod_item='".$cod_item."'; AtualizaComp('".$tipo_comp."'); MostraLayer(cod_comp,140, event); }" );
		//$objResponse->assign("data_".$cod_item, "innerHTML", Data::UnixTime2Data(time()));
	
		Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 1);
	
		//$objResponse->call("mostraFeedback", $texto, 'true');
	}
	
	/* *********************************************************************
	 AcabaEdicao - Marca no banco de dados o fim da ediÁ„o do item dado, din‚micamente
	Entrada: $cod_curso - cÛdigo do curso, para conectar ao banco de dados
	$cod_item - item ao qual o endereco estar· associado
	$cod_usuario - cÛdigo do usu·rio que est· utilizando
	$acao - 0 se for cancelar a ediÁ„o,
	1 se for finalizar a ediÁ„o.
	
	Saida: nenhuma
	*/
	static function AcabaEdicao($cod_curso, $cod_item, $cod_usuario, $acao){
	
		$sock=AcessoSQL::Conectar($cod_curso);
	
		// como vou precisar atualizar campos de data, preciso saber a data em UnixTime
		$data = "";
		unset ($data);
		$data = time();
	
		//CorreÁ„o feita para n„o acontecer de os hor·rios ficarem iguais no banco e atrapalhar o status do item
		$consulta="select * from Portfolio_itens_historicos where data=".$data." and cod_item=".$cod_item;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$linha = AcessoSQL::RetornaLinha($res);
		if ($linha[0]!="")
		{ 
			$data++; 
		}
	
		$consulta="update Portfolio_itens set status='L', data=".$data." where cod_item=".$cod_item;
		$res=AcessoSQL::Enviar($sock, $consulta);
	
		if($acao){
			$consulta="insert into Portfolio_itens_historicos values ('".$cod_item."', '".$cod_usuario."', '".$data."', 'F')";
		}else{
			$consulta="insert into Portfolio_itens_historicos values ('".$cod_item."', '".$cod_usuario."', '".$data."', 'D')";
		}
	
		$res=AcessoSQL::Enviar($sock, $consulta);
	
		AcessoSQL::Desconectar($sock);
	
		//CorreÁ„o feita para n„o acontecer de os hor·rios ficarem iguais no banco e atrapalhar o status do item
		if ($linha[0]!="")
		{ 
			sleep(1); 
		}
	}
	
	
	/* *********************************************************************
	 * RetornaGrupoPortfolio - Retorna o numero do grupo de dado portifolio
	* Entrada: $sock - Base do Curso
	* 			$cod_item - numero do portfolio
	* Saida: numero do grupo ou NULL (se n„o houver grupo)
	*/
	static function RetornaGrupoPortfolio($sock, $cod_item){
		$query = "select cod_grupo from Portfolio_itens where cod_item = ".$cod_item;
		$res = AcessoSQL::Enviar($sock, $query);
		$ret = AcessoSQL::RetornaLinha($res);
		return($ret['cod_grupo']);
	}
	
	/* *********************************************************************
	 CriaLinkVisualizar - Cria link simb?ico para dinamica e retorna o link
	Entrada: $sock - BASE DO CURSO
	$cod_curso - codigo do curso
	$cod_usuario - codigo do usuario
	$cod_item - c?igo do item
	$diretorio_arquivos - diretorio dos arquivos do TelEduc
	$diretorio_temp - diretorio dos arquivos do TelEduc
	Saida: caminho relativo
	*/
	static function CriaLinkVisualizar($sock,$cod_curso,$cod_usuario,$cod_item,$diretorio_arquivos,$diretorio_temp)
	{
		/* Busca Arquivo a ser mostrado */
		$caminho = "";
		unset ($caminho);
		$dir=$diretorio_arquivos."/".$cod_curso."/portfolio/item/".$cod_item."/";
	
		/* Cria link simb?ico (apaga antigo, se houver)*/
		if ($cod_usuario<0)
			$cod_usuario=0;
		$dirlink=$diretorio_temp."/portfolio_".$cod_curso."_".$cod_item."_".$cod_usuario;
		$dirlinkpath="../../../diretorio/portfolio_".$cod_curso."_".$cod_item."_".$cod_usuario;
	
		if (Arquivos::ExisteArquivo($dirlink))
		{
			Arquivos::RemoveArquivo($dirlink);
		}
	
		Arquivos::CriaLinkSimbolico($dir,$dirlink);
	
		$retorno['diretorio']=$dirlink."/";
		$retorno['link']=$dirlinkpath."/";
	
		return ($retorno);
	}
	
	/* *********************************************************************
	 IniciaCriacao - Inicia a cria?o de um novo item
	Entrada: $sock - BASE DO CURSO
	$cod_topico - topico pai
	$cod_usuario - usuario atual
	$cod_grupo - codigo do grupo (NULL ou "" se individual)
	$cod_curso - codigo do curso
	$diretorio_temp - nome do diret?io temporario, usado durante a edi?o
	Saida: codigo do item criado
	*/
	static function IniciaCriacao ($sock, $cod_topico, $cod_usuario, $cod_grupo, $cod_curso, $diretorio_temp, $titulo)
	{
		// vamos mexer com datas, entao chamo a funcao time() uma vez soh
		$data = time();
	
		if ($cod_grupo=="")
			$cod_grupo="NULL";
		$cod_item=AcessoSQL::RetornaProximoCodigo($sock, "Portfolio_itens");
		$posicao=Portfolio::UltimaPosicaoLivre($sock, $cod_topico);
		$consulta="insert into Portfolio_itens (cod_item, cod_topico, cod_usuario, cod_grupo, titulo, data, posicao_item, status, inicio_edicao, tipo_compartilhamento) values (".$cod_item.", ".$cod_topico.", ".$cod_usuario.", ".$cod_grupo.", '".htmlentities($titulo)."', ".$data.", ".$posicao.", 'L', ".$data.", 'T')";
		$res=AcessoSQL::Enviar($sock, $consulta);
		$consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".$data.", 'C')";
		$res=AcessoSQL::Enviar($sock, $consulta);
	
		if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/"))
			Arquivos::CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/");
		if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/portfolio/"))
			Arquivos::CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/portfolio/");
		if (!file_exists($diretorio_temp."/tmp/".$cod_curso."/portfolio/item/"))
			Arquivos::CriaDiretorio($diretorio_temp."/tmp/".$cod_curso."/portfolio/item/");
	
		$dir=$diretorio_temp."/tmp/".$cod_curso."/portfolio/item/".$cod_item."/";
		Arquivos::CriaDiretorio($dir);
	
		return($cod_item);
	}
	
	/* *********************************************************************
	 ApagarTopico - Apaga o topico selecionado (envia para a lixeira todos os itens das subpastas)
	Entrada: $sock - BASE DO CURSO
	$cod_topico - codigo do topico
	$cod_usuario - codigo do usuario que est?removendo, para o hist?ico
	Saida: nenhuma
	*/
	static function ApagarTopico ($sock, $cod_topico, $cod_usuario)
	{
		/* Recursivamente, apaga os t?icos filhos */
		$query="select cod_topico from Portfolio_topicos where cod_topico_pai=".$cod_topico;
		$res=AcessoSQL::Enviar($sock,$query);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		if (count($lista)>0)
		foreach($lista as $cod=>$linha)
			Portfolio::ApagarTopico($sock,$linha['cod_topico'],$cod_usuario);
	
		/* Apaga os itens do topico */
		$query="select cod_item from Portfolio_itens where cod_topico=".$cod_topico;
		$res=AcessoSQL::Enviar($sock,$query);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		if (count($lista)>0)
		foreach ($lista as $cod=>$linha)
		{
			$query="update Portfolio_itens set cod_topico=2 where cod_item=".$linha['cod_item'];
			AcessoSQL::Enviar($sock,$query);
			$query="insert into Portfolio_itens_historicos values (".$linha['cod_item'].", ".$cod_usuario.", ".time().", 'A')";
			AcessoSQL::Enviar($sock,$query);
		}
	
		/* Apagando o t?ico e rearranjando ordem */
		$query="select cod_topico_pai from Portfolio_topicos where cod_topico=".$cod_topico;
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		$query="delete from Portfolio_topicos where cod_topico=".$cod_topico;
		AcessoSQL::Enviar($sock,$query);
	
		Portfolio::ArrumaPosicoes($sock, $linha['cod_topico_pai']);
		Portfolio::AtualizaCompartilhamentoPastas ($sock, $linha['cod_topico_pai'], 'P');
	}
	
	/* *********************************************************************
	 ApagarItem - Apaga o item selecionado (envia para a lixeira)
	Entrada: $sock - BASE DO CURSO
	$cod_item - codigo do item
	$cod_usuario - codigo do usuario que est?removendo, para o hist?ico
	Saida: nenhuma
	*/
	static function ApagarItem ($sock, $cod_item, $cod_usuario)
	{
		$linha=Portfolio::RetornaDadosDoItem($sock, $cod_item);
		$consulta="update Portfolio_itens set cod_topico=2 where cod_item=".$cod_item;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'A')";
		$res=AcessoSQL::Enviar($sock, $consulta);
		Portfolio::ArrumaPosicoes($sock, $linha['cod_topico']);
		Portfolio::AtualizaCompartilhamentoPastas ($sock, $linha['cod_topico'], 'P');
	}
	
	static function RetornaNumArquivosVisiveis($lista_arq)
	{
		$conta_arq=0;
	
		if (count($lista_arq)>0)
		{
			foreach($lista_arq as $cod => $linha){
				if((!$linha['Status']) &&  ($linha['Arquivo']!="")) {
					$conta_arq++;
				}
			}
	
		}
		return($conta_arq);
	}
	
	/* *********************************************************************
	 RetornaEnderecosMaterial - Retorna um array com os enderecos associados ao item
	Entrada: $sock - BASE DO CURSO
	$cod_item - item
	Saida: array com []['cod_endereco']
	[]['cod_item']
	[]['nome']
	[]['endereco']
	[]['status']
	*/
	static function RetornaEnderecosMaterial ($sock, $cod_item)
	{
		$consulta="select * from Portfolio_itens_enderecos where cod_item=".$cod_item;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		return($lista);
	}
	
	/* *********************************************************************
	 VerificaAnexo - verifica se nome do anexo aprenseta acento ou chars ruins.
	Entrada: $string - com o nome do anexo a ser verificado
	Saida: 1 - nome do anexo está ok!
	0 - nome do anexo apresenta acento ou chars ruins.
	*/
	function VerificaAnexo($str_anexo)
	{
		$string = utf8_decode($str_anexo);
		// Palavras acentuadas
		$bad_chars = "¡·…ÈÕÌ”Û⁄˙";
		$bad_chars.= "√„’ı";
		$bad_chars.= "¿‡»ËÃÏ“ÚŸ˘";
		$bad_chars.= "ƒ‰ÀÎœÔ÷ˆ‹¸";
		$bad_chars.= "¬‚ ÍŒÓ‘Ù€˚";
	
		// Caracteres estranhos para nome de um arquivo anexo
		$bad_chars.= "˜`!@#$%*()+={}[]\\\"';:?/,<>";
		$str_res = strpbrk($string, $bad_chars);
	
		// Se apresentar algum char de $bad_chars, str_res != ""
		if (($str_res == "") || ($str_res == false))
			return 1;
		else
			return 0;
	}
	
	/* *********************************************************************
	 RetornaComentariosDoItem - Retorna um array com os comentarios associados ao item
	Entrada: $sock - BASE DO CURSO
	$cod_item - item
	Saida: array com []['cod_comentario']
	[]['cod_item']
	[]['comentario']
	[]['cod_comentarista']
	[]['data']
	[]['status']
	*/
	static function RetornaComentariosDoItem ($sock, $cod_item)
	{
		$consulta="select * from Portfolio_itens_comentarios where cod_item=".$cod_item." and status='A' order by data desc";
		$res=AcessoSQL::Enviar($sock, $consulta);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		return($lista);
	}

	/* *********************************************************************
	 CriaLinkVisualizarComentar - Cria link simb?ico para arquivos no comentario do portfolio e retorna o link
	Entrada: $sock - BASE DO CURSO
	$cod_curso - codigo do curso
	$cod_usuario - codigo do usuario
	$cod_comentario - c?igo do comentario
	$diretorio_arquivos - diretorio dos arquivos do TelEduc
	$diretorio_temp - diretorio dos arquivos do TelEduc
	Saida: caminho relativo
	*/
	static function CriaLinkVisualizarComentar($sock,$cod_curso,$cod_usuario,$cod_comentario,$diretorio_arquivos,$diretorio_temp)
	{
		/* Busca Arquivo a ser mostrado */
		$caminho = "";
		unset ($caminho);
		$dir=$diretorio_arquivos."/".$cod_curso."/portfolio/comentario/".$cod_comentario."/";
	
		/* Cria link simb?ico (apaga antigo, se houver)*/
		if ($cod_usuario<0)
			$cod_usuario=0;
		$dirlink=$diretorio_temp."/portfolio_".$cod_curso."_comentario_".$cod_comentario."_".$cod_usuario;
		$dirlinkpath="../../../diretorio/portfolio_".$cod_curso."_comentario_".$cod_comentario."_".$cod_usuario;
	
		if (Arquivos::ExisteArquivo($dirlink))
			Arquivos::RemoveArquivo($dirlink);
	
		Arquivos::CriaLinkSimbolico($dir,$dirlink);
	
		$retorno['diretorio']=$dirlink."/";
		$retorno['link']=$dirlinkpath."/";
	
		return ($retorno);
	}
	
	/* *********************************************************************
	 InsereCodComentario - Insere o comentario na tabela e Retorna o cod_comentario
	entrada - $sock - sock de conexao
	Saida: $cod_comentario + 1
	*/
	static function PegaUltimoCodComentario($sock, $cod_item, $cod_comentarista)
	{
		$query="insert into Portfolio_itens_comentarios (cod_item,comentario,cod_comentarista,data,status) values (".$cod_item.",'', ".$cod_comentarista.",".time().",'E')";
		AcessoSQL::Enviar($sock,$query);
	
		$query="select MAX(cod_comentario) from Portfolio_itens_comentarios";
		$res = AcessoSQL::Enviar($sock,$query);
		$linha = AcessoSQL::RetornaLinha($res);
	
		$cod_comentario = $linha[0];
	
		return ($cod_comentario);
	}
	
	/* *********************************************************************
	 InsereComentario - Insere o coment?io no item passado
	Entrada: $sock - BASE DO CURSO
	$cod_item - codigo do item
	$comentario - comentario a ser acrescido
	$cod_comentarista - Pessoa fazendo o comentario
	Saida: Nenhuma
	*/
	static function InsereComentario ($sock, $cod_comentario, $comentario)
	{
		$comentario=ConversorTexto::LimpaConteudo($comentario);
		$query="update Portfolio_itens_comentarios set comentario='".$comentario."', data=".time().", status='A' where cod_comentario='".$cod_comentario."'";
		// (cod_item,comentario,cod_comentarista,data,status) values (".$cod_item.",'".$comentario."',".$cod_comentarista.",".time().",'A')";
		AcessoSQL::Enviar($sock,$query);
	}
	
	/* *********************************************************************
	 RetornaResHistoricoDoItem - Retorna todas as ocorr?cias do historico de um item dado
	Entrada: $sock - BASE DO CURSO
	$cod_item - item
	Saida: array []['cod_item']
	[]['cod_usuario']
	[]['data']
	[]['acao']
	*/
	static function RetornaResHistoricoDoItem ($sock, $cod_item)
	{
		$consulta="select * from Portfolio_itens_historicos where cod_item=".$cod_item." order by data desc";
		$res=AcessoSQL::Enviar($sock, $consulta);
		return($res);
	}
	
	/* *********************************************************************
	 ExcluirItem - Apaga o item selecionado (definitivamente)
	Entrada: $sock - BASE DO CURSO
	$cod_item - codigo do item
	$cod_usuario - codigo do usuario que est?removendo, para o hist?ico
	Saida: nenhuma
	*/
	static function ExcluirItem ($sock, $cod_item, $cod_usuario)
	{
		$linha=Portfolio::RetornaDadosDoItem($sock, $cod_item);
		$consulta="update Portfolio_itens set cod_topico=0 where cod_item=".$cod_item;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'X')";
		$res=AcessoSQL::Enviar($sock, $consulta);
		Portfolio::ArrumaPosicoes($sock, $linha['cod_topico']);
	}
	
	/* *********************************************************************
	 RecuperarItem - Recupera o item selecionado (da lixeira)
	Entrada: $sock - BASE DO CURSO
	$cod_item - codigo do item
	$cod_usuario - codigo do usuario que est?removendo, para o hist?ico
	$cod_grupo - codigo do grupo (NULL ou "" se portfolio individual)
	Saida: nenhuma
	*/
	static function RecuperarItem ($sock, $cod_item, $cod_usuario,$cod_grupo)
	{
		$cod_raiz=Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo);
		$linha=Portfolio::RetornaDadosDoItem($sock, $cod_item);
		$consulta="update Portfolio_itens set cod_topico=".$cod_raiz.", posicao_item=".Portfolio::UltimaPosicaoLivre($sock, $cod_raiz).",tipo_compartilhamento='P' where cod_item=".$cod_item;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$consulta="insert into Portfolio_itens_historicos values (".$cod_item.", ".$cod_usuario.", ".time().", 'R')";
		$res=AcessoSQL::Enviar($sock, $consulta);
	}
	
	/* *********************************************************************
	 RetornaItensDaLixeira - Retorna array com os itens da lixeira
	Entrada: $sock - BASE DO CURSO
	$cod_usuario - codigo do usuario logado
	$cod_usuario_portfolio - codigo do usuario dono do portfolio
	$cod_grupo_portfolio - codigo do grupo dono do portfolio (NULL ou "" se individual
			Saida: array com []['cod_item']
			[]['cod_topico']
			[]['cod_usuario']
			[]['cod_grupo']
			[]['titulo']
			[]['texto']
			[]['tipo_compartilhamento']
			[]['data']
			[]['data_ativo']
			[]['data_inativo']
			[]['posicao_item']
			[]['status']
			[]['inicio_edicao']
			*/
	static function RetornaItensDaLixeira ($sock, $cod_usuario,$cod_usuario_portfolio,$cod_grupo_portfolio)
	{
		$saida=0;
		if ($cod_usuario!=$cod_usuario_portfolio)
		{
			if (!Portfolio::PertenceAoGrupo($sock,$cod_usuario, $cod_grupo_portfolio))
				return $saida;
			else
				$regra="(cod_grupo=".$cod_grupo_portfolio.")";
		}
		else
		{
			if (Portfolio::PertenceAoGrupo($sock,$cod_usuario, $cod_grupo_portfolio))
				$regra="(cod_grupo=".$cod_grupo_portfolio.")";
			else
				$regra="(cod_grupo IS NULL and cod_usuario=".$cod_usuario_portfolio.")";
		}
		$consulta="select * from Portfolio_itens where cod_topico=2 and ".$regra." order by posicao_item";
		$res=AcessoSQL::Enviar($sock, $consulta);
		$lista=AcessoSQL::RetornaArrayLinhas($res);
		return($lista);
	}
	
	/* *********************************************************************
	 MoverItem - Move o item para outra pasta
	Entrada: $sock - BASE DO CURSO
	$cod_item - item a ser movido
	$cod_usuario - usu?io realizando a?o
	$cod_topico - topico destino
	Saida: nenhuma
	*/
	static function MoverItem ($sock, $cod_item, $cod_usuario, $cod_topico)
	{
		$data=time();
		// topico fonte do item
		$consulta="select cod_topico, tipo_compartilhamento from Portfolio_itens where cod_item=".$cod_item;
		$res=AcessoSQL::Enviar($sock, $consulta);
		$linha=AcessoSQL::RetornaLinha($res);
	
		// em que posicao o item aparece no destino
		$posicao_item=Portfolio::UltimaPosicaoLivre($sock, $cod_topico);
	
		/* Movendo item */
		$consulta="update Portfolio_itens set cod_topico=".$cod_topico.", posicao_item=".$posicao_item." where cod_item=".$cod_item;
		$res=AcessoSQL::Enviar($sock, $consulta);
	
		/* Inserindo dados no hist?ico */
		$consulta="insert into Portfolio_itens_historicos (cod_item, cod_usuario, data, acao) values (".$cod_item.", ".$cod_usuario.", ".$data.", 'M')";
		$res=AcessoSQL::Enviar($sock, $consulta);
	
		// atualizando modo de compartilhamento das pastas fonte e destino
		Portfolio::AtualizaCompartilhamentoPastas($sock, $linha['cod_topico'], "P");
		Portfolio::AtualizaCompartilhamentoPastas($sock, $cod_topico, $linha['tipo_compartilhamento']);
	
		return($posicao_item);
	}
	
	static function verificaPastasFixas($sock, $cod_usuario, $cod_topico_pai){
		
		$consultaMaterial = "select cod_topico from Portfolio_topicos where cod_usuario = ".$cod_usuario." and cod_topico_pai = ".$cod_topico_pai." and topico = 'Material de Apoio'";
		$resMaterial=AcessoSQL::Enviar($sock, $consultaMaterial);
	
		
		$consultaLeituras = "select cod_topico from Portfolio_topicos where cod_usuario = ".$cod_usuario." and cod_topico_pai = ".$cod_topico_pai." and topico = 'Leituras'";
		$resLeituras=AcessoSQL::Enviar($sock, $consultaLeituras);
		
		$consultaAtividades = "select cod_topico from Portfolio_topicos where cod_usuario = ".$cod_usuario." and cod_topico_pai = ".$cod_topico_pai." and topico = 'Atividades'";
		$resAtividades=AcessoSQL::Enviar($sock, $consultaLeituras);
		
		$codigos = array();
		$codigos['cod_material'] = AcessoSQL::RetornaLinha($resMaterial);
		$codigos['cod_leituras'] = AcessoSQL::RetornaLinha($resLeituras);
		$codigos['cod_atividades'] = AcessoSQL::RetornaLinha($resAtividades);
		
		return $codigos;
		
	}
}