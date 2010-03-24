<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/configurar/notificar.php

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

    Nied - Núcleo de Informática Aplicada à Educação
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
  ARQUIVO : cursos/aplic/configurar/notificar.php
  ========================================================== */

  set_time_limit(0);

  $bibliotecas="../cursos/aplic/bibliotecas/";

  session_start();
  include($bibliotecas."teleduc.inc");
  include($bibliotecas."acesso_sql.inc");
  include($bibliotecas."email.inc");
  include($bibliotecas."data.inc");
  include($bibliotecas."conversor_texto.inc");
  include($bibliotecas."usuarios.inc");
  include($bibliotecas."cursos.inc");

  include("notificar.inc");

  $sock = Conectar("");

  // Pegamos todas as linguas disponiveis ja que a notificacao de novidades deve funcionar para cada lingua
  $query = "select cod_lingua from Lingua_textos group by cod_lingua";
  $res = Enviar($sock, $query);
  $linha = RetornaArrayLinhas($res);

  for ($k = 1; $k <= count($linha); $k++) {
     $lista_frases_total[$k] = RetornaListaDeFrases($sock, -8, $k);
  }
  
  //$lista_frases = RetornaListaDeFrases($sock, -8, 1);

  echo("<html>\n");
  echo("  <head>\n");
  // 1 - Notificação de novidades
  echo("    <title>TelEduc - ".RetornaFraseDaLista($lista_frases_total[1], 1)."</title>\n");
  echo("  </head>\n");
  echo("  <body>\n");
  echo("    <pre>\n");

  $L_host = $_SERVER['SERVER_NAME'];
  $R_host = $_SERVER['REMOTE_ADDR'];

  // Verifica se é um IP válido
  if (ereg('^([0-9]{1,3}\.){3}[0-9]{1,3}$', $L_host))
  {
    $L_host = gethostbyaddr($L_host);
  }
  else
  {
    $L_host = gethostbyname($L_host);
  }

  // Impede que outro usuário execute arbitrariamente esse script.
  if (strcmp($L_host, $R_host) != 0)
  {
    // 2 - Este script não pode ser executado remotamente.
    echo("<br><font color=tomato size=+1>".RetornaFraseDaLista($lista_frases_total[1], 2)."</font><br>");
    exit(); // Executado remotamente saí.
  }

  // Verifica se a variável que determina a forma de envio foi setada e se ela é valida. 
  // Note que o teste de validez inclui o teste <= 0, pois para esses usuários o script  
  // não processa.                                                                       
  if ( (!isset($notificar_email)) || ($notificar_email <= 0) || ($notificar_email > 3) )
  {
    // 3 - A variável notificar_email deve ser passada.
    echo("\n".RetornaFraseDaLista($lista_frases_total[1], 3)."\n");
    // 4 - Ex.: notificar.php?&notificar_email=2
    echo(RetornaFraseDaLista($lista_frases_total[1], 4)."\n\n");
    // 5 - 0 - não receber notificações de atualizações
    echo(RetornaFraseDaLista($lista_frases_total[1], 5)."\n");
    // 6 - 1 - resumo diário
    echo(RetornaFraseDaLista($lista_frases_total[1], 6)."\n");
    // 7 - 2 - resumo parcial duas vezes ao dia 
    echo(RetornaFraseDaLista($lista_frases_total[1], 7)."\n");

    exit();
  }

  // Obtém a lista de ferramentas disponíveis no ambiente.
  $lista_ferramentas = RetornaListaFerramentas($sock);

  // Calcula o UnixTime do dia anterior para obter cursos ainda não encerrados.
  $ontem = time() - (24 * 60 * 60);

  // Obtém o hostname da máquina e a raiz_www para criar o link de acesso ao curso
  $query = "select valor from Config where item='host'";
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);
  $host = $linha['valor'];

  unset($linha);
  $query = "select diretorio from Diretorio where item='raiz_www'";
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);
  $raiz_www = $linha['diretorio'];

  unset($linha);

  // Lista os cursos não encerrados
  $query = "select cod_curso from Cursos where curso_fim > ".$ontem;
  $res = Enviar($sock, $query);
  $lista = RetornaArrayLinhas($res);

  $total_cursos = count($lista);

  // Para cada curso lista os usuários e envia o e-mail de notificação se eles o requiseram.
  for ($i = 0; $i < $total_cursos; $i++)
  {
  	
  	// Alterna para base de dados principal
  	MudarDB($sock, "");
  	
    // Obtém dados do usuário e a data do último envio de notificação.
	$query  = "SELECT nome, email, curso.cod_usuario cod_usuario, cod_lingua, config.notificar_email ";
	$query .= "FROM `Usuario` as user, `Usuario_config` as config, `Usuario_curso` as curso ";
	$query .= "WHERE (user.cod_usuario = curso.cod_usuario_global) ";
	$query .= "and (curso.cod_usuario = config.cod_usuario) ";
	$query .= "and (curso.cod_curso = ".$lista[$i]['cod_curso'].") ";
	$query .= "and (config.cod_curso = curso.cod_curso)";
	$query .= "and (config.notificar_email != 0)";
   
    $res = Enviar($sock, $query);
    $linha = RetornaArrayLinhas($res);

    // Alterna para a base de dados do curso.
    MudarDB($sock, $lista[$i]['cod_curso']);
   
    // Obtém os dados do curso para o envio do e-mail.
    $dados_curso = DadosCursoParaEmail($sock, $lista[$i]['cod_curso']);
    
    // 8 - Nome do curso:
    echo(RetornaFraseDaLista($lista_frases_total[1], 8).$dados_curso['nome_curso']."<br>\n");

    // Determina o assunto do e-mail.
    // 1 - Notificação de novidades
    $assunto = "TelEduc: - ".$dados_curso['nome_curso']." - ".RetornaFraseDaLista($lista_frases, 1);

    // Monta a URL de acesso ao curso.
    $url_acesso = "http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$lista[$i]['cod_curso']."\n\n<br /><br />";

    $total_usuarios = count($linha);
    // Para cada usuário lista as novidades nas ferramentas e se estas houver, envia e-mail.

    for ($j = 0; $j < $total_usuarios; $j++)
    {
      $notificar_email_usuario = $linha[$j]['notificar_email'];
      // Caso o usuário não queira ser notificado (notificar_email == 0)
      if (($notificar_email_usuario > 0) && ($notificar_email_usuario < 3))
      {
    	// notificar_email = 2, recebe email 2x por dia (sempre)
    	// notificar_email = 1, recebe 1 email só (no momento em que for passado 1 de parametro)
      	if ((($notificar_email == 1) && ($notificar_email_usuario == 1)) ||
      		($notificar_email_usuario == 2))
      	{
      	
      		$curso_ferramentas = RetornaFerramentasCurso($sock);
      		$novidade_ferramentas = RetornaNovidadeFerramentas($sock, $lista[$i]['cod_curso'], $linha[$j]['cod_usuario']);

	      	// Obtém o timestamp do último acesso ao ambiente (esse timestamp conta o acesso às ferramentas). 
      		$ultimo_acesso = UltimoAcessoAmbiente($sock, $linha[$j]['cod_usuario']);

	      	// Compara o timestamp do último acesso com do último envio de notificações. 
	      	// Adota o maior deles para evitar envio de novidades já listadas em e-mail  
	      	// anterior (notificar_email = 2).                                           
	      	if ($ultimo_acesso > $linha[$j]['notificar_data'])
	      	{
	        	$comp_acesso = $ultimo_acesso;
	      	}
	      	else
	      	{
	        	$comp_acesso = $linha[$j]['notificar_data'];
	      	}

      		// Soma um tempo médio estipulado que o usuário gasta em uma ferramenta para 
      		// determinar se ele ainda se encontra online. Neste caso 25 minutos.   
	      	//TODO : mudar o 0 pra 25 hehe     
	      	$comp_acesso = $ultimo_acesso + (25 * 60);
	
	      	$frase = "";
	      	$novo_flag = false;
	
		    // Se foram retornadas novidades então envia e-mail.
	      	if ((is_array($novidade_ferramentas)) && (is_array($curso_ferramentas)))
	      	{
        		foreach($novidade_ferramentas as $cod_ferr => $dados_ferr)
        		{
		        	// Se o compartilhamento da ferramenta for para formadores e o usuário for um formador
		          	// ou o compartilhamento da ferramenta for para todos e a data de novidades for maior
        		  	// que a data base de comparação e não foi o usuário quem postou a novidade, então     
	        	  	// lista as ferramentas onde há novidades.                                             
          			if ((((($curso_ferramentas[$cod_ferr]['status'] == 'F') || ($cod_ferr == 0)) && ($linha[$j]['tipo_usuario'] == 'F')) ||
                 		($curso_ferramentas[$cod_ferr]['status'] == 'A'))
             			&& ($comp_acesso < $dados_ferr['data']) && ($linha[$j]['cod_usuario'] != $dados_ferr['cod_usuario']))
          			{
            			$frase .= $lista_ferramentas[($linha[$j]['cod_lingua'])][$cod_ferr]['texto']."<br />";

            			$novo_flag = $novo_flag || true;
          			}

        		}

		        // Se houver novidades monta a mensagem e envia ao usuário.
		        if ($novo_flag)
		        {
		        	// 12 - Verificação feita até: 
          			$frase_12 = RetornaFraseDaLista($lista_frases_total[($linha[$j]['cod_lingua'])], 12);

          			// 9 - Curso:
          			$mensagem = "<br />".(str_pad(RetornaFraseDaLista($lista_frases_total[($linha[$j]['cod_lingua'])], 9), strlen($frase_12)))." ".$dados_curso['nome_curso']."<br />";
          			// 12 - Verificação feita até:
          			$mensagem .= ($frase_12)." ".UnixTime2DataHora(time())."<br /><br />";

          			// 10 - Olá  
          			// 11 - , 
          			$mensagem .= RetornaFraseDaLista($lista_frases_total[($linha[$j]['cod_lingua'])], 10)." ".$linha[$j]['nome']." ".RetornaFraseDaLista($lista_frases_total[($linha[$j]['cod_lingua'])], 11)."<br /><br />";


          			// 13 - Há novidades na(s) ferramenta(s):
          			$mensagem .= RetornaFraseDaLista($lista_frases_total[($linha[$j]['cod_lingua'])], 13)."<br /><br />";

          			$mensagem .= $frase;
		            // 14 - Acesse seu curso através do endereço:
        		    $mensagem .= "\n".RetornaFraseDaLista($lista_frases_total[($linha[$j]['cod_lingua'])], 14)."<br />";
          			$mensagem .= $url_acesso."<br />";
		          	// 15 - Para não receber mais notificações do ambiente, entre em seu curso e desative a opção na ferramenta Configurar.
        		  	$mensagem .= RetornaFraseDaLista($lista_frases_total[($linha[$j]['cod_lingua'])], 15);

          			$emissor = $dados_curso['nome_curso']." <NAO_RESPONDA@".$host.">";
		  			echo($mensagem);
          			MandaMsg($emissor, $linha[$j]['email'], $assunto, $mensagem);

          			$query = "update Usuario_config set notificar_data = ".time();
          			//$res = Enviar($sock, $query);
        		} 	// END IF
      		} 	// END IF
    	} 		// END IF
  	  } 	// END IF
    } 		// END FOR - usuarios
  } 	// END FOR - cursos
  Desconectar($sock);

  echo("    </pre>\n");
  echo("  </body>\n");
  echo("</html>\n");
  
?>
