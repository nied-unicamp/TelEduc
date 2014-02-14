<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/resetar_curso2.php

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
  ARQUIVO : administracao/resetar_curso2.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");
  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("}\n");
  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  Desconectar($sock);


  $dir_arquivos=RetornaDiretorio('Arquivos');
  $caminho_mysqldump=RetornaDiretorio('mysqldump');

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  // 245 - Reutiliza��o de Cursos Encerrados 
  echo("          <h4>".RetornaFraseDaLista($lista_frases,245)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("<!-- Tabelao -->\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  echo("<td><ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("<li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("</ul></td></tr>\n");
  echo("<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("<tr><td>\n");

  echo("<form>\n");

  flush();

  $sock = Conectar("");
  $query="select max(cod_curso) as ultimo_codigo from Cursos";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  $novo_cod_curso = $linha['ultimo_codigo']+1;


  // 144 - Criando diret�rio para os arquivos do curso 
  echo(RetornaFraseDaLista($lista_frases,144)."...\n\n");

  CriaDiretorio($dir_arquivos."/".$novo_cod_curso);

  echo("Ok!<p>\n");

  // 145 - Copiando arquivos para novo diret�rio 
  echo(RetornaFraseDaLista($lista_frases,145)."...\n\n");

  flush();

  // copia todo o diretorio do curso selecionado para o novo diretorio no
  // caminho dos cursos atuais (j� com o novo numero do curso) - ou seja,
  // o curso � duplicado

  if (!($fp=popen("cp -r ".$dir_arquivos."/".$cod_curso."/. ".$dir_arquivos."/".$novo_cod_curso,"w")))
    die('Erro interno');
  fputs($fp,$dbpassword);
  pclose($fp);

  echo("Ok!<p>\n");

  // 146 - Atualizando c�digo do curso 
  echo(RetornaFraseDaLista($lista_frases,146)."...\n\n");

  flush();

  // cria "dump" do curso selecionado, para realizar a duplica��o
  // e posterior "reset" do mesmo...
  if (!($fp = popen($caminho_mysqldump." --default-character-set=latin1 -u '".$dbuser."' -p ".$dbnamecurso.$cod_curso." > ".$dir_arquivos."/".$novo_cod_curso."/".$dbnamecurso.$cod_curso.".table ","w")))
    die('Erro interno');
  fputs($fp,$dbpassword);
  pclose($fp);

  flush();

  $comando  = "sed 's/INSERT INTO Cursos VALUES (".$cod_curso.",";
  $comando .=       "/INSERT INTO Cursos VALUES (".$novo_cod_curso.",/' ";
  $comando .= $dir_arquivos."/".$novo_cod_curso."/".$dbnamecurso.$cod_curso.".table > ".$dir_arquivos."/".$novo_cod_curso."/".$dbnamecurso.$novo_cod_curso.".table";

  if (!($fp=popen($comando,"w")))
    die('Erro interno');
  fputs($fp,$dbpassword);
  pclose($fp);

  echo("Ok!<p>\n");


  // 147 - Criando banco de dados... 
  echo(RetornaFraseDaLista($lista_frases,147)."...\n\n");

  // cria o novo banco de dados TelEducCursoX (mysql) para insercao do curso a ser reutilizado
  $query="create database ".$dbnamecurso.$novo_cod_curso;
  $res=Enviar($sock,$query);
  Desconectar($sock);

  echo("Ok!<p>\n");

  // 148 - Inserindo todas as tabelas do curso no banco de dados criado... 
  echo(RetornaFraseDaLista($lista_frases,148)."...\n\n");

  // insere todas as tabelas do curso desejado no novo banco de dados criado
  if (!($fp=popen("mysql -u '".$dbuser."' -p'".$dbpassword."' ".$dbnamecurso.$novo_cod_curso." < ".$dir_arquivos."/".$novo_cod_curso."/".$dbnamecurso.$novo_cod_curso.".table","w")))
    die('Erro interno');
  fputs($fp,$dbpassword);
  pclose($fp);

  echo("Ok!<p>\n");

  $sock=Conectar($novo_cod_curso);

  $query  = "SHOW FIELDS FROM Cursos LIKE 'cod_lingua'";
  $res=Enviar($sock,$query);

  $tem_cod_lingua = RetornaNumLinhas($res);
  Desconectar($sock);
  $sock = Conectar('');

  if ($tem_cod_lingua) // entao o curso a ser reutilizado tem cod_lingua
  {
    $query  = "SELECT nome_curso, inscricao_inicio, inscricao_fim, curso_inicio, ";
    $query .= "curso_fim, informacoes, publico_alvo, tipo_inscricao, num_alunos, ";
    $query .= "cod_coordenador, acesso_visitante, cod_pasta, cod_lingua FROM ";
    $query .= "Cursos WHERE cod_curso = ".$cod_curso;
  }
  else
  {
    $query  = "SELECT nome_curso, inscricao_inicio, inscricao_fim, curso_inicio, ";
    $query .= "curso_fim, informacoes, publico_alvo, tipo_inscricao, num_alunos, ";
    $query .= "cod_coordenador, acesso_visitante, cod_pasta FROM ";
    $query .= "Cursos WHERE cod_curso = ".$cod_curso;
  }

  // 149 - Obtendo informa��es do curso para atualiza��o da tabela Cursos da base externa... 
  echo(RetornaFraseDaLista($lista_frases,149)."...\n\n");

  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  Desconectar($sock);

  echo("Ok!<p>\n");

  if ($tem_cod_lingua) // entao o curso a ser reutilizado tem cod_lingua
  {
    $cod_lingua = $linha['cod_lingua'];
  }
  else
  {
    // lingua padrao do curso � definida como "Portugu�s" (1)
    $cod_lingua = 1;
  }

  if ($linha['cod_pasta'] == "")
  {
    $linha['cod_pasta'] = "NULL";
  }
  else
  {
    $query  = "SELECT cod_pasta FROM ";
    $query .= "Cursos_pastas WHERE cod_pasta = ".$linha['cod_pasta'];

    $sock=Conectar("");
    $res=Enviar($sock,$query);
    Desconectar($sock);
    $existe_cod_pasta = RetornaNumLinhas($res);

    if (! $existe_cod_pasta) // caso o cod_pasta do curso a ser reutilizado ja exista
                           // na tabela "Cursos_pastas" da base de dados "TelEduc"
                           // atual, entao esse codigo � mantido, caso contrario
                           // recebera NULL
    {
      $linha['cod_pasta'] = "NULL";
    }
  }

  // 150 - Atualizando tabela Cursos da base externa com informa��es do curso inserido... 
  echo(RetornaFraseDaLista($lista_frases,150)."...\n\n");

  $query  = "INSERT INTO Cursos (cod_curso, nome_curso, inscricao_inicio, ";
  $query .= "inscricao_fim, curso_inicio, curso_fim, informacoes, publico_alvo, ";
  $query .= "tipo_inscricao, num_alunos, cod_coordenador, acesso_visitante, ";
  $query .= "cod_pasta, cod_lingua) VALUES (";
  $query .= $novo_cod_curso.",'".$linha['nome_curso']."',";
  $query .= "NULL,NULL,NULL,NULL,'";
  $query .= $linha['informacoes']."','".$linha['publico_alvo']."','";
  $query .= $linha['tipo_inscricao']."',".$linha['num_alunos'].",";
  $query .= $linha['cod_coordenador'].",'".$linha['acesso_visitante']."',";
  $query .= $linha['cod_pasta'];
  $query .= ",".$cod_lingua.")";

  $sock=Conectar("");
  $res=Enviar($sock,$query);

  /* Copia o coordenador do curso encerrado para o novo curso */
  $query  = "SELECT cod_coordenador FROM Cursos WHERE cod_curso = ".$cod_curso;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $cod_coordenador = $linha['cod_coordenador'];
 

  /* Obtem o cod_usuario_global do coordenador*/
  $query  = "SELECT cod_usuario_global FROM Usuario_curso WHERE cod_curso = ".$cod_curso." AND cod_usuario= ".$cod_coordenador;
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $cod_coordenador_global = $linha['cod_usuario_global'];
  
  $query  = "SELECT data_inscricao FROM Usuario WHERE cod_usuario = ".$cod_coordenador_global."";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $data_inscricao = $linha['data_inscricao'];
  
  $query = "INSERT INTO Usuario_curso (cod_usuario_global, cod_usuario, cod_curso, tipo_usuario, portfolio, data_inscricao) VALUES (".$cod_coordenador_global.", 1, ".$novo_cod_curso.", 'F', 'ativado', ".$data_inscricao."),(-1, -1, ".$novo_cod_curso.", 'F', 'ativado', ".$data_inscricao.");";
  Enviar($sock,$query);
  
  $query = "INSERT INTO Usuario_config (cod_usuario, cod_curso, notificar_email, notificar_data) VALUES (1, ".$novo_cod_curso.", 0, 0);";
  Enviar($sock,$query);
  
  echo("Ok!<p>\n");

  MudarDB($sock, $novo_cod_curso);

  $query  = "UPDATE Cursos SET ";
  $query .= "inscricao_inicio = NULL, inscricao_fim = NULL, ";
  $query .= "curso_inicio = NULL, curso_fim = NULL, ";
  $query .= "cod_curso = ".$novo_cod_curso;

  // 248 - Eliminando todas as datas do curso 
  echo(RetornaFraseDaLista($lista_frases,248)."...\n\n");

  $res=Enviar($sock,$query);
  Desconectar($sock);
  echo("Ok!<p>\n");

  // apaga os 2 arquivos .table do diretorio do novo curso
  // (arquivos TelEducCursoX.table e TelEducCursoY.table)

  flush();

  // 151 - Apagando arquivos desnecess�rios... 
  echo(RetornaFraseDaLista($lista_frases,151)."...\n\n");

  if (!($fp=popen("rm ".$dir_arquivos."/".$novo_cod_curso."//.table","w")))
    die('Erro interno');
  fputs($fp,$dbpassword);
  pclose($fp);

  echo("Ok!<p>\n");

  // 249 - Configurando / Apagando conte�do das tabelas do antigo curso... 
  echo(RetornaFraseDaLista($lista_frases,249)."...\n\n");

  $sock=Conectar($novo_cod_curso);

  // seta todos os registros da tabela Agenda_itens para situa��o "nao ativo"
  $query = "UPDATE Agenda_itens set situacao='N'";
  $res=Enviar($sock,$query);

  // seta todos os registros da tabela Apoio_itens para compartilhamento apenas com formador
  $query = "UPDATE Apoio_itens set tipo_compartilhamento='F'";
  $res=Enviar($sock,$query);

  // seta todos os registros da tabela Apoio_topicos para compartilhamento apenas com formador
  $query = "UPDATE Apoio_topicos set tipo_compartilhamento='F'";
  $res=Enviar($sock,$query);

  // seta todos os registros da tabela Atividade_itens para compartilhamento apenas com formador
  $query = "UPDATE Atividade_itens set tipo_compartilhamento='F'";
  $res=Enviar($sock,$query);

  // seta todos os registros da tabela Atividade_topicos para compartilhamento apenas com formador
  $query = "UPDATE Atividade_topicos set tipo_compartilhamento='F'";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Batepapo_apelido
  $query = "DELETE from Batepapo_apelido";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Batepapo_assuntos
  $query = "DELETE from Batepapo_assuntos";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Batepapo_conversa
  $query = "DELETE from Batepapo_conversa";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Batepapo_online
  $query = "DELETE from Batepapo_online";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Batepapo_sessoes
  $query = "DELETE from Batepapo_sessoes";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Curso_acessos
  $query = "DELETE from Curso_acessos";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Diario_itens
  $query  = "DELETE FROM Diario_itens";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Diario_comentarios
  $query  = "DELETE FROM Diario_comentarios";
  $res=Enviar($sock,$query);

  // zera o campo data de todos os registros da tabela Ferramentas_nova
  $query = "UPDATE Ferramentas_nova set data=0";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Forum
  $query = "DELETE from Forum";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Forum_mensagens
  $query = "DELETE from Forum_mensagens";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Grupos
  $query = "DELETE from Grupos";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Grupos_usuario
  $query = "DELETE from Grupos_usuario";
  $res=Enviar($sock,$query);

  // seta todos os registros da tabela Leitura_itens para compartilhamento apenas com formador
  $query = "UPDATE Leitura_itens set tipo_compartilhamento='F'";
  $res=Enviar($sock,$query);

  // seta todos os registros da tabela Leitura_topicos para compartilhamento apenas com formador
  $query = "UPDATE Leitura_topicos set tipo_compartilhamento='F'";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Mural
  $query = "DELETE from Mural";
  $res=Enviar($sock,$query);

  // seta todos os registros da tabela Obrigatoria_itens para compartilhamento apenas com formador
  $query = "UPDATE Obrigatoria_itens set tipo_compartilhamento='F'";
  $res=Enviar($sock,$query);

  // seta todos os registros da tabela Obrigatoria_topicos para compartilhamento apenas com formador
  $query = "UPDATE Obrigatoria_topicos set tipo_compartilhamento='F'";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Correio_destinos
  $query = "DELETE from Correio_destinos";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Correio_intermap
  $query = "DELETE from Correio_intermap";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Correio_lista_destinos
  $query = "DELETE from Correio_lista_destinos";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Correio_mensagens
  $query = "DELETE from Correio_mensagens";
  $res=Enviar($sock,$query);

  // Remove todos os arquivos de correio do curso a ser reutilizado
  // (remove todo o diretorio correio e depois cria um novo, sem conteudo
  RemoveDiretorio ($dir_arquivos."/".$novo_cod_curso."/correio");
  CriaDiretorio ($dir_arquivos."/".$novo_cod_curso."/correio");

  // Obtem o codigo do coordenador do curso
  $query  = "SELECT cod_coordenador FROM Cursos WHERE cod_curso = ".$novo_cod_curso;

  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  $cod_coordenador = $linha['cod_coordenador'];

  // apaga os perfis dos usuarios (tabela Perfil_usuarios) cujos c�digos n�o
  // estejam no vetor $usuarios_a_permanecer
  $query  = "DELETE FROM Perfil_usuarios WHERE cod_usuario != ".$cod_coordenador;

  $res=Enviar($sock,$query);


  // Move arquivo com a foto do coordenador para o diretorio inicial do curso,
  // remove todos os arquivos de perfil do curso a ser reutilizado
  // (removendo todo o diretorio correio e depois criando um novo, sem conteudo)
  // e por ultimo move a foto do coordenador de volta para o diretorio perfil
  MoveArquivo ($dir_arquivos."/".$novo_cod_curso."/perfil/cod_usuario_".$cod_coordenador.".jpg",
               $dir_arquivos."/".$novo_cod_curso);
  RemoveDiretorio ($dir_arquivos."/".$novo_cod_curso."/perfil");
  CriaDiretorio ($dir_arquivos."/".$novo_cod_curso."/perfil");
  MoveArquivo ($dir_arquivos."/".$novo_cod_curso."/cod_usuario_".$cod_coordenador.".jpg",
               $dir_arquivos."/".$novo_cod_curso."/perfil");


  // apaga todos os registros da tabela Portfolio_itens
  $query = "DELETE from Portfolio_itens";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Portfolio_itens_comentarios
  $query = "DELETE from Portfolio_itens_comentarios";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Portfolio_itens_enderecos
  $query = "DELETE from Portfolio_itens_enderecos";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Portfolio_itens_historicos
  $query = "DELETE from Portfolio_itens_historicos";
  $res=Enviar($sock,$query);

  // apaga todos os registros da tabela Portfolio_topicos
  $query = "DELETE from Portfolio_topicos";
  $res=Enviar($sock,$query);

  // Remove todos os arquivos do portfolio do curso a ser reutilizado
  // (remove todo o diretorio portfolio e depois cria um novo, sem conteudo
  RemoveDiretorio ($dir_arquivos."/".$novo_cod_curso."/portfolio");
  CriaDiretorio ($dir_arquivos."/".$novo_cod_curso."/portfolio");

  $query = "DELETE from Avaliacao_notas";
  $res=Enviar($sock,$query);

  $query = "DELETE from Portfolio_itens_avaliacao";
  $res=Enviar($sock,$query);
/*
  $query = "UPDATE Exercicios_modelo set aplicado='N'";
  $res=Enviar($sock,$query);

  $query = "DELETE from Exercicios_modelo_historicos where acao!='C'";
  $res=Enviar($sock,$query);

  $query = "DELETE from Exercicios_aplicado";
  $res=Enviar($sock,$query);

  $query = "DELETE from Exercicios_resolucao";
  $res=Enviar($sock,$query);

  $query = "DELETE from Exercicios_resolucao_comentarios";
  $res=Enviar($sock,$query);

  $query = "DELETE from Exercicios_resolucao_historicos";
  $res=Enviar($sock,$query);

  $query = "DELETE from Exercicios_quest_res_dissertativa";
  $res=Enviar($sock,$query);

  $query = "DELETE from Exercicios_quest_res_lacuna";
  $res=Enviar($sock,$query);

  $query = "DELETE from Exercicios_quest_res_objetiva";
  $res=Enviar($sock,$query);

  $query  = "DELETE FROM Usuario WHERE ";
  $query .= " cod_usuario != -1 AND cod_usuario != ".$cod_coordenador;
  $res=Enviar($sock,$query);
 
  $query = "DELETE FROM Usuario_config WHERE ";
  $query .= " cod_usuario != -1 AND cod_usuario != ".$cod_coordenador;
  $res=Enviar($sock,$query);
*/

  $query = "show tables like \"%_sequencia\"";
  $res =  Enviar($sock, $query);
  $tabelas_sequencia = RetornaArrayLinhas($res);

  foreach ($tabelas_sequencia as $tabela_seq)
  {
     $tabela=$tabela_seq[0];
    if($tabela=="Correio_mensagens_sequencia")
      $query = "select MAX(cod_msg) from Correio_mensagens";
    elseif($tabela=="Usuario_sequencia")
      $query = "select MAX(cod_usuario) from Usuario";
    elseif($tabela=="Avaliacao_sequencia")
      $query = "select MAX(cod_avaliacao) from Avaliacao";
    elseif($tabela=="Avaliacao_notas_sequencia")
      $query = "select MAX(cod_nota) from Avaliacao_notas";
    elseif($tabela=="Dinamica_sequencia")
      $query = "select MAX(cod) from Dinamica_sequencia";  
    elseif($tabela=="Grupos_sequencia")
      $query = "select MAX(cod_grupo) from Grupos";
/*
    elseif($tabela=="Exercicios_alternativa_sequencia")
    {
          $query = "select MAX(cod_alternativa) from Exercicios_alternativa_objetiva";
	  $res = Enviar($sock, $query);
	  $maximo_obj = RetornaLinha($res);
          if($maximo_obj[0]=="")
              $maximo_obj=0;


	  $query = "select MAX(cod_alternativa) from Exercicios_resp_dissertativa";
	  $res = Enviar($sock, $query);
	  $maximo_dis = RetornaLinha($res);
	  if($maximo_dis[0]=="")
	    $maximo_dis=0;

	  if($maximo_obj[0]>$maximo_dis[0])
            $maximo=$maximo_obj[0];
	  else
	    $maximo=$maximo_dis[0];

	  if($maximo!="")
          {
              $query1="insert into ".$tabela_seq[0]." (cod,cod_usuario,data) values (".$maximo.",0,1)";
              $query2="delete from ".$tabela_seq[0]." where  cod=".$maximo;
              $res = Enviar($sock, $query1);
	      $res = Enviar($sock, $query2);
	  }
	  continue;

    }
    elseif($tabela=="Exercicios_modelo_sequencia")
      $query = "select MAX(cod_modelo) from Exercicios_modelo";
    elseif($tabela=="Exercicios_questao_sequencia")
      $query = "select MAX(cod_quest) from Exercicios_questao";
    elseif($tabela=="Exercicios_resolucao_sequencia")
      $query = "select MAX(cod_resolucao) from Exercicios_resolucao";
    elseif($tabela=="Exercicios_topico_sequencia")
      $query = "select MAX(cod_topico) from Exercicios_topico";
    elseif($tabela=="Exercicios_alternativa_objetiva_sequencia")
      $query = "select MAX(cod_alternativa) from Exercicios_alternativa_objetiva";
   */
	 else
    {
    $tabela1=explode("_",$tabela_seq[0]);
    $total=count($tabela1);
    $tabela2=$tabela1[0];
    if($total==2)
    {
      $tabela2=$tabela2."_".$tabela1[1];
    }
    if($total>2)
    {
      for($i=1;$i<=($total-2);$i++)
      {
        $tabela2=$tabela2."_".$tabela1[$i];
      }
    }
    $query = "select MAX(cod_item) from ".$tabela2;
    }
    $res = Enviar($sock, $query);
    $maximo = RetornaLinha($res);
    if($maximo[0]!="")
      {
      $query1="insert into ".$tabela_seq[0]." (cod,cod_usuario,data) values (".$maximo[0].",0,1)";
      $query2="delete from ".$tabela_seq[0]." where  cod=".$maximo[0];

      $res = Enviar($sock, $query1);
      $res = Enviar($sock, $query2);
      }
 }

  echo("Ok!<p>\n");

  // 250 - Aten��o : O curso antigo permanece no ambiente e n�o foi alterado...
  echo("<p>".RetornaFraseDaLista($lista_frases,250).". </p>\n\n");

  // 113 -  Opera��o completada com sucesso!
  echo("<b>".RetornaFraseDaLista($lista_frases,113)."</b>\n");

  Desconectar($sock);

  // Enviar e-mail para o coordenador
  $remetente = RetornaConfig('adm_email');

  $sock = Conectar("");
  // Obtem dados do coordenador
  $dados_coordenador = DadosCursoParaEmail($sock,$cod_curso);
  $nome_curso = NomeCurso($sock, $novo_cod_curso);
  Desconectar($sock); 

  $destino = $dados_coordenador['email'];
  $nome_aluno = $dados_coordenador['nome_coordenador'];
  $login = $dados_coordenador['login'];

  $raiz_www=RetornaDiretorio('raiz_www');
  $host=RetornaConfig('host');
  $endereco=$host.$raiz_www;

  // 251 - Informa��es para acesso ao curso reutilizado no TelEduc 
  $assunto = RetornaFraseDaLista($lista_frases,251);

  // 252 - Seu pedido de reutiliza��o do curso 
  // 101 - foi aceito.

  // 253 - Todo o conte�do do curso foi mantido, por�m todas as informa��es 
  // e arquivos dos alunos do antigo curso foram removidos. 

  // 102 - Para acessar o curso, a sua Identifica��o �:
  // 103 - e a sua senha �:
  // 104 - O acesso deve ser feito a partir do endereco:
  // 105 - Atenciosamente, Administra��o do Ambiente TelEduc

  $mensagem =$nome_aluno.",<br><br>";
  $mensagem.=RetornaFraseDaLista($lista_frases,252)." ".$nome_curso." ".RetornaFraseDaLista($lista_frases,101)."<br><br>";
  $mensagem.=RetornaFraseDaLista($lista_frases,253)."<br><br>";
  //$mensagem.=RetornaFraseDaLista($lista_frases,102)."<big><em><strong> ".$login."</strong></em></big> ".RetornaFraseDaLista($lista_frases,103)."<big><em><strong> ".$senha_gerada."</strong></em></big><br><br>";
  $mensagem.=RetornaFraseDaLista($lista_frases,104)."\nhttp://".$endereco."/cursos/aplic/index.php?cod_curso=".$novo_cod_curso."<br><br><p style=\"text-align:right;\">";
  $mensagem.=RetornaFraseDaLista($lista_frases,105).".<br>";

  
  $mensagem_envio =MontaMsg($host, $raiz_www, $novo_cod_curso, $mensagem, $assunto, $cod_usuario_remetente='', $destinatarios='');
 
  
  MandaMsg($remetente,$destino,$assunto,$mensagem_envio);

  echo("</td></tr></table>\n");
  echo("</td></tr></table>\n");

  echo("</form>\n");

  echo("</td></tr>\n");
  include("../rodape_tela_inicial.php");
  echo("</body>\n");
  echo("</html>\n");
?>
