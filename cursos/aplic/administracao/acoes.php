<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/acoes.php

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
  ARQUIVO : cursos/aplic/administracao/acoes.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $action = $_POST['action'];
  $action_js = $_POST['action_js'];

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,0);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $host = RetConfig($sock, "host");
  $dir = RetDiretorio($sock, "raiz_www");
  $email_admin = DadosAdministracaoParaEmail($sock);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
    Desconectar($sock);
    exit();
  }

  // É possível que um formador mal intencionado consiga fazer um post para esta
  // página com os parâmetros certos, com a intenção de realizar ações que só o
  // coordenador pode fazer. Precisamos então fazer a verificação abaixo para as
  // ações exclusivas do coordenador.
  $ecoordenador = ECoordenador($sock,$cod_curso,$cod_usuario);

  if($action_ger == "transformar")
  {
    if ($opcao == "formador")
      $tipo_usuario="F";

    else if ($opcao == "aluno")
      $tipo_usuario="A";

    else if ($opcao == "colaborador")
      $tipo_usuario="Z";

    else if ($opcao == "visitante")
      $tipo_usuario="V";

    $confirma = 'false';

    if ($ecoordenador) {
      foreach($cod_usu as $cod => $cod_usuario)
        MudaTipoUsuario($sock,$cod_curso,$cod_usuario,$tipo_usuario);
      $confirma = 'true';
    }

    Desconectar($sock);
    header("Location:gerenciamento_usuarios.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&tipo_usuario=".$tipo_usuario."&acao_fb=".$action_ger."&atualizacao=".$confirma);
  }

  if($action_ger == "trocar_coordenador") {

    $confirma = 'false';

    if ($ecoordenador) {
      TrocaCoordenador($sock, $cod_curso, $cod_usu[0]);
      $confirma = 'true';
    }

    header("Location:gerenciamento_usuarios.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&tipo_usuario=F&acao_fb=".$action_ger."&atualizacao=".$confirma);

  }

  if($action == "inscrever_cadastrado")
  {

    $codigos_usu_global_aux = $_POST['codigos_usu_global'];
    $codigos_usu_global = explode(",", $codigos_usu_global_aux);

    $linha = Array();
    foreach($codigos_usu_global as $cod)
    {
      $linha['cod_usuario_global'] = $cod;
      $linha['tipo_usuario'] = $tipo_usuario;

      //Funcao que utiliza o cadastro ja existente do usuario e cadastra-o no curso
      $sock=CadastrarUsuarioExistente($sock, $cod_curso, $linha, $lista_frases);
    }

    $cod_usu_global = "";

    Desconectar($sock);
    header("Location:gerenciamento_usuarios.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&tipo_usuario=".$tipo_usuario."&acao_fb=".$action."&atualizacao=true");
  }

  if($action == "inscrever")
  {
    for($i=0;$i < count($nome);$i++)
    {
      if($nome[$i] != "")
      {
        $dados_preenchidos_s[$i]['nome']  = $nome[$i];
        $dados_preenchidos_s[$i]['login'] = $login[$i];
        $dados_preenchidos_s[$i]['email'] = $email[$i];
      }
    }

    $logins=RetornaLoginsInscricao($sock);
    $emails=RetornaEmailsInscricao($sock);

    // booleano que indica se um login passado jah estah sendo usado por outro usuario
    $login_existente = false;

    // percorre os dados preenchidos e verifica se estah tentando inscrever usuario com login repetido
    foreach($dados_preenchidos_s as $cod => $linha) {
      if ($logins[strtoupper($linha['login'])]==1 && $emails[strtoupper($linha['email'])]!=1) {
        // se o email nao eh repetido e o login eh repetido, erro pois nao pode ter login jah existente
        $dados_preenchidos_s[$cod]['status_login']=1;
        $dados_preenchidos_s[$cod]['login']=GeraLogin($sock,$linha['email']);
        $login_existente=true;
      }
      if ($logins[strtoupper($linha['login'])]==1 && $emails[strtoupper($linha['email'])]==1) {
        // se o login jah existe e o email tbm, estah inscrevendo usuario jah cadastrado no ambiente.
        $dados_preenchidos_s[$cod]['cadastrado']=1; // flag que indica se usuario jah esta cadastrado no ambiente
      }
    }

    // se o login jah existe, volta para tela de inscricao, exibe msg de erro de inscricao e sugere um login previamente gerado
    if(($login_existente == true)) {
      $_SESSION['array_inscricao']=$dados_preenchidos_s;
      header("Location:inscrever.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=0&tipo_usuario=".$tipo_usuario."&acao=dadosPreenchidosLogin&atualizacao=false");
    } else {
      // percorre os dados preenchidos e inscreve o usuario
      foreach($dados_preenchidos_s as $cod => $linha) {
        $linha['tipo_usuario']=$tipo_usuario;
        $linha['senha']=GeraSenha();
        if($linha['cadastrado']==1) {
          $inscrito = LoginCadastradoCurso($sock, $linha['login'], $cod_curso);
          if(!$inscrito) {
            // se usuario jah estah cadastrado no ambiente e nao estah inscrito no curso, soh faz a inscricao
            $sock = CadastrarUsuarioExistente($sock,$cod_curso,$linha,$lista_frases);
          } else {
            // se usuario jah estah cadastrado no ambiente e tbm no curso, gera msg de erro
            Desconectar($sock);
            header("Location:inscrever.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=0&tipo_usuario=".$tipo_usuario."&acao=erroUsuarioCadastrado&atualizacao=false");
            exit();
          }
        } else {
          // senao, faz cadastro e inscricao
          $sock=CadastrarUsuario($sock,$cod_curso,$linha, $lista_frases, $cod_usuario);
        }
      }

      $dados_preenchidos_s = "";

      Desconectar($sock);
      header("Location:gerenciamento_usuarios.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&tipo_usuario=".$tipo_usuario."&acao_fb=".$action."&atualizacao=true");
    }
  }

  if($action == "enviarSenha")
  {
    if (count($cod_usu)!=0)
    {
      $lista_usuarios=RetornaListaUsuariosSenha($sock,$cod_curso);

      $dados_curso=DadosCursoParaEmail($sock, $cod_curso);

      foreach($cod_usu as $cod => $cod_usuario_senha)
      {
        $senha=GeraSenha();
        $senha_crypt=crypt($senha,"AA");

        /* 138 - Login e senha do TelEduc */
        $assunto=RetornaFraseDaLista($lista_frases,138);

        $mensagem="<font size=\"2\">\n";

        /* 95 - Prezado(a) */
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,95)." <strong>".$lista_usuarios[$cod_usuario_senha]['nome']."</strong>,</p>\n\n";

        /* 140 - Segue abaixo o seu login e senha conforme solicitado para o curso */
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,140)." <strong>".$dados_curso['nome_curso']."</strong></p>";

        /* 67 - Seu login é */
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,67)." <strong><big><em>".$lista_usuarios[$cod_usuario_senha]['login']."</em></big></strong> ";

        /* 68 - e sua senha é */
        $mensagem.=RetornaFraseDaLista($lista_frases,68)." <strong><big><em>".$senha."</em></big></strong></p>\n";

        /* 230 - Acesse o curso através do endereço: */
        $mensagem.="<p>".RetornaFraseDaLista($lista_frases,230)."</p>\n";

        $mensagem.="<p><a href=\"http://".$host.$dir."/cursos/aplic/index.php?cod_curso=".$cod_curso."\">http://".$host.$dir."/cursos/aplic/index.php?cod_curso=".$cod_curso."</a></p><br />\n";

        /* 139 - Atenciosamente, */
        $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,139)."<br /><strong>".$lista_usuarios[$cod_usuario]['nome']."</strong></p><br />\n";

        $mensagem.="</font>\n";

        if ($cod_usuario>=0)
          $remetente=$lista_usuarios[$cod_usuario]['email'];
        else
          $remetente=$email_admin;

        $destino=$lista_usuarios[$cod_usuario_senha]['email'];

        AtualizaSenhaUsuario($sock,$cod_curso,$cod_usuario_senha,$senha_crypt);

        //o montaMsg faz outras conexoes com o BD, não dá pra manter várias simultaneas
        Desconectar($sock);
        $mensagem_envio = MontaMsg($host, $dir, $cod_curso, $mensagem, $assunto, $cod_usuario, $lista_usuarios[$cod_usuario_senha]['nome']);
        $sock=Conectar($cod_curso);

        if (MandaMsg($remetente,$destino,$assunto,$mensagem_envio))
          $confirma='true';
        else //Erro
          $confirma='false';
      }
    }

    Desconectar($sock);
    header("Location:administracao.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&acao=".$action."&atualizacao=".$confirma."");
    exit();
  }

  if($action == "marcarFerramentas")
  {
    if (!MarcaFerramentas($sock, $ferramentas))
    {
    //Erro
     $confirma='false';
    }
    else
    {
      $confirma='true';
      Desconectar($sock);
    }
    header("Location:administracao.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&acao=".$action."&atualizacao=".$confirma."");
  }

  if($action == "escolherFerramentas")
  {
    $query="update Curso_ferramentas set status='D', _timestamp = ".time();
    Enviar($sock,$query);

    foreach($status as $cod_ferramentap => $linha)
    {
      $query="update Curso_ferramentas set status='".$linha."', _timestamp=".time()." where cod_ferramenta=".$cod_ferramentap;
      Enviar($sock,$query);
    }
    /* Marca o menu                    */
    AtualizaFerramentasNova($sock,0,'F');
    $confirma='true';
    Desconectar($sock);
    header("Location:administracao.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&acao=".$action."&atualizacao=".$confirma."");
  }

  if($action == "alterarCronograma")
  {
    $horario = time();
    $cadastro_dentro=CadastraCronograma($sock,$cod_curso,$inscricao_inicio,$inscricao_fim,$curso_inicio,$curso_fim,$horario);
    Desconectar($sock);
    $sock = Conectar("");
    $cadastro_fora=CadastraCronograma($sock,$cod_curso,$inscricao_inicio,$inscricao_fim,$curso_inicio,$curso_fim,$horario);
    Desconectar($sock);

    if (!($cadastro_fora && $cadastro_dentro))
    {
      $confirma='false'; //Erro
    }
    else{
      $confirma='true';
    }
    header("Location:administracao.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&acao=".$action."&atualizacao=".$confirma."");
  }

  if($action == "alterarDadosCurso")
  {
    $completar=CompletarDadosCurso($sock,$cod_curso);
    $horario = time();
    $cadastro_dentro=CadastraDadosCurso($sock,$cod_curso,$nome_curso,$inscricao_inicio,$inscricao_fim,$curso_inicio,$curso_fim,$informacoes,$publico_alvo,$tipo_inscricao,$acesso_visitante,$cod_lin,$horario);
    Desconectar($sock);
    $sock=Conectar("");
    MudancaDeLingua($sock,$cod_lin);
    $cadastro_fora=CadastraDadosCurso($sock,$cod_curso,$nome_curso,$inscricao_inicio,$inscricao_fim,$curso_inicio,$curso_fim,$informacoes,$publico_alvo,$tipo_inscricao,$acesso_visitante,$cod_lin,$horario);


    if (!($cadastro_fora && $cadastro_dentro))
    {
      $confirma='false'; //Erro
    }
    else
    {
      Desconectar($sock);
      $confirma='true';
    }
    header("Location:administracao.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&acao=".$action."&atualizacao=".$confirma."");
  }

  if($action == "compartilharFerramentas")
  {
    Desconectar($sock);
    $sock = Conectar("");

    /*obtem as ferramentas que ja estao compartilhadas*/
    $lista_compart = RetornaFerramCompartilhadas($sock, $cod_curso);

    /*Obs.: o alg. abaixo supoe q os vetores estejam ordenados*/  
    $i=0;$j=0; /*indices para arrays!*/
    while($i<count($ferr_comp)&&$j<count($lista_compart)) /*eqto nao terminar as listas*/
    {
      if(($lista_compart == NULL)||($lista_compart[$j] == "")||($ferr_comp[$i] < $lista_compart[$j]['cod_ferramenta'])) /*se $ferr_comp for menor.. inserir na base*/
      {
        CompartilhaFerramenta($sock, $cod_curso, $ferr_comp[$i]);
        $i++;
      }
      else if($ferr_comp[$i] == $lista_compart[$j]['cod_ferramenta']) /*se for igual, nao faz nada*/
      { 
        $i++; $j++;
      }
      else if(($lista_compart[$j] != "")&&($ferr_comp[$i] > $lista_compart[$j]['cod_ferramenta'])) /*se for maior.. remover $lista_compart da base de dados*/
      {
        DescompartilhaFerramenta($sock, $cod_curso, $lista_compart[$j]['cod_ferramenta']);
        $j++;
      }
    }

    /*insere o resto de $ferr_comp na base de dados*/
    while($i<count($ferr_comp))
    {
      CompartilhaFerramenta($sock, $cod_curso, $ferr_comp[$i]);
      $i++;
    }

    /*remove o resto de $lista_compart da base de dados*/
    while(($lista_compart != NULL)&&($j<count($lista_compart)))
    {
      if($lista_compart != NULL && $lista_compart[$j] != "")
      { 
        DescompartilhaFerramenta($sock, $cod_curso, $lista_compart[$j]['cod_ferramenta']);
        $j++;
      }
    }
    $confirma='true';
    Desconectar($sock);
    header("Location:administracao.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&acao=".$action."&atualizacao=".$confirma."");
  }

  Desconectar($sock);

?>