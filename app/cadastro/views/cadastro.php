<?php
$ferramenta_geral = 'geral';
$ferramenta_admin = 'admin';
$ferramenta_cadastro = 'cadastro';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$view_geral = '../../'.$ferramenta_geral.'/views/';
$view_admin = '../../'.$ferramenta_admin.'/views/';
$model_cadastro = '../../'.$ferramenta_cadastro.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_geral.'inicial.inc';
require_once $view_admin.'topo_tela_inicial.php';

$sock = AcessoSQL::Conectar("");
$lista_escolaridade=Inicial::RetornaListaEscolaridade($sock);


$erro1 = Linguas::RetornaFraseDaLista($lista_frases_configurar,74);
$texto = Linguas::RetornaFraseDaLista($lista_frases,184);
$erro2 = Linguas::RetornaFraseDaLista($lista_frases_configurar,75);
$erro3 = Linguas::RetornaFraseDaLista($lista_frases_configurar,76);
/*
==================
Funcoes JavaScript
==================
*/

echo("<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>");

echo("    <script type=\"text/javascript\">\n");

//numero de reloads da imagem de autentica��o
echo("    var numReload = 0;");
//caminho utilizado no calendario
echo("    var pathToImages = '".$diretorio_jscss."';\n");

echo("    function Iniciar()\n");
echo("    {\n");
echo("      document.formulario.login.focus();\n");
echo("      startList();\n");
echo("    }\n\n");

// Validação do RG:
echo("    function RGValido(numero){\n");
echo("      var arrayNumero = numero.split('');");
echo("      if(numero.replace(/[ ]+/g, \"\").length == 0){\n");
echo("        return false;\n");
echo("      }\n");
echo("      return true;\n");
echo("    }\n");

/* *********************************************************************
 Funcao Verificar - JavaScript. Verifica um a um cada campo do formulario
Entrada: Nenhuma. Funcao espec�fica do formulario desta pagina
Saida:   Boolean, para controle do onSubmit;
true, se nao houver erros no formulario,
false, se houver.
*/
echo("    function verificar()\n");
echo("    {\n");

echo("      nome_usuario = document.formulario.nome_usuario.value;\n");
echo("      data = document.formulario.data.value;\n");
echo("      email = document.formulario.email.value;\n");
echo("      rg = document.formulario.rg.value;\n");
echo("      endereco = document.formulario.endereco.value;\n");
echo("      cidade = document.formulario.cidade.value;\n");
echo("      estado = document.formulario.estado.value;\n");
echo("      pais = document.formulario.pais.value;\n");
echo("      if (nome_usuario == '')\n");
echo("      {\n");
/* 50 - O campo */ /* 32 - Nome */ /* 51 - n�o pode ser vazio */
echo("        alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,50)." ".Linguas::RetornaFraseDaLista($lista_frases_configurar,32)." ".Linguas::RetornaFraseDaLista($lista_frases_configurar,51).".', false);\n");
echo("        document.formulario.nome_usuario.focus();\n");
echo("        return false;\n");
echo("      }\n");
echo("      if (!RGValido(rg) || rg == ''){\n");
/* 50 - O campo *//* 33 - RG parece estar errado */
echo("        alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,50)." ".Linguas::RetornaFraseDaLista($lista_frases_configurar,33).".', false);\n");
echo("        document.formulario.rg.focus();\n");
echo("        return false;\n");
echo("      }\n");

// Verificação da Data.

// Formato da data é valido?
echo("      var DataValida = /^((0[1-9]|[12]\d)\/(0[1-9]|1[0-2])|30\/(0[13-9]|1[0-2])|31\/(0[13578]|1[02]))\/\d{4}$/;\n");
echo("      if (!DataValida.test(data)){\n");
/* 71 - Data Invalida */
echo("        alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,71).".', false);\n");
echo("        document.formulario.data.focus();\n");
echo("        return false;\n");
echo("      }\n");
// Data no futuro?
echo("      else{\n");
echo("        var data = data.split(\"/\");\n");
echo("        var Hoje = new Date();\n");
echo("        var DataNascimento = new Date(data[2], data[1]-1, data[0]);\n");
echo("        if (DataNascimento > Hoje){\n");
/* 80 - Data de Nascimento no Futuro */
echo("          alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,80).".', false);\n");
echo("          document.formulario.data.focus();\n");
echo("          return false;\n");
echo("        }\n");
echo("      }\n");

// Verifica se o e-mail é valido
// valida o e-mail na conformação foo@bar
echo("      var EmailValido =/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+$/;\n");
echo("      if (!EmailValido.test(email)){\n");
/* 52 - O e-mail parece estar errado */
echo("        alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,52).".', false);\n");
echo("        document.formulario.email.focus();\n");
echo("        return false;\n");
echo("      }\n");
echo("      if (endereco == '')\n");
echo("      {\n");
/* 50 - O campo */ /* 40 - Endere�o */ /* 51 - n�o pode ser vazio */
echo("        alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,50)." \" ".Linguas::RetornaFraseDaLista($lista_frases_configurar,40)."\" ".Linguas::RetornaFraseDaLista($lista_frases_configurar,51).".', false);\n");
echo("        document.formulario.endereco.focus();\n");
echo("        return false;\n");
echo("      }\n");
echo("      if (cidade == '')\n");
echo("      {\n");
/* 50 - O campo */ /* 41 - Cidade */ /* 51 - n�o pode ser vazio */
echo("        alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,50)." \" ".Linguas::RetornaFraseDaLista($lista_frases_configurar,41)."\" ".Linguas::RetornaFraseDaLista($lista_frases_configurar,51).".', false);\n");
echo("        document.formulario.cidade.focus();\n");
echo("        return false;\n");
echo("      }\n");
echo("      if (estado == '')\n");
echo("      {\n");
/* 50 - O campo */ /* 42 - Estado */ /* 51 - n�o pode ser vazio */
echo("        alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,50)." \" ".Linguas::RetornaFraseDaLista($lista_frases_configurar,42)."\" ".Linguas::RetornaFraseDaLista($lista_frases_configurar,51).".', false);\n");
echo("        document.formulario.estado.focus();\n");
echo("        return false;\n");
echo("      }\n");
echo("      if (pais == '')\n");
echo("      {\n");
/* 50 - O campo */ /* 43 - Pa�s */ /* 51 - n�o pode ser vazio */
echo("        alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,50)." \" ".Linguas::RetornaFraseDaLista($lista_frases_configurar,43)."\" ".Linguas::RetornaFraseDaLista($lista_frases_configurar,51).".', false);\n");
echo("        document.formulario.pais.focus();\n");
echo("        return false;\n");
echo("      }\n");
echo("      return true;\n");
echo("    }\n");

echo("      function ValidaLogins() \n");
echo("      {\n");
echo("        js_novo_login=document.formulario.login.value;\n");
echo("        if ((js_novo_login==''))\n");
echo("        {\n");
// 59 - O campo de login n�o podem ser vazio. Por favor digite novamente o login desejado.
echo("          alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar, 59)."', false);\n");
echo("          document.formulario.login.focus();\n");
echo("          return false;\n");
echo("        }\n");
echo("        return true;\n");
echo("      }\n");

echo("      function ValidaSenhas() \n");
echo("      {\n");
echo("        senha=document.formulario.senha.value;\n");
echo("        senha2=document.formulario.senha2.value;\n");
echo("        if (senha=='')\n");
echo("        {\n");
// 4 - A nova senha n�o pode ser vazia. Por favor digite a nova senha. ARRUMAR
echo("          alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,4)."', false);\n");
echo("          document.formulario.senha.focus();\n");
echo("          return(false);\n");
echo("        }\n");
echo("        if (senha2=='')\n");
echo("        {\n");
// 4 - A nova senha n�o pode ser vazia. Por favor digite a nova senha.
echo("          alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,4)."', false);\n");
echo("          document.formulario.senha2.focus();\n");
echo("          return(false);\n");
echo("        }\n");
echo("        if (senha!=senha2) \n");
echo("        {\n");
// 5 - As novas senhas digitadas diferem entre si. Por favor redigite-as.
echo("          alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,5)."', false);\n");
echo("          document.formulario.senha.value='';\n");
echo("          document.formulario.senha2.value='';\n");
echo("          document.formulario.senha.focus();\n");
echo("          return(false);\n");
echo("        }\n");
echo("        else \n");
echo("          return(true);\n");
echo("      }\n");

//echo("    function confereDados()\n");
//echo("    {\n");
//echo("		$(document).ready(function(){\n");
//echo("			$('#formulario').submit(\n");
//echo("				function (event) {\n");
//echo("      			if(ValidaLogins() && ValidaSenhas() && verificar())\n");
//echo("						$.post('".$diretorio_models."inicial.inc', $(\"#formulario\").serialize(),\n");
//echo("							function(data) {\n");
//echo("								var flag = $(data)[0].nodeName.toLowerCase();\n");
//echo("									alert(flag);\n");
//echo("						});\n");
//echo("				$.ajax({\n");
//echo("					url: '".$diretorio_models."inicial.inc', //caminho do arquivo a ser executado\n");
//echo("					dataType: 'json', //tipo do retorno\n");
//echo("					type: 'post', //metodo de envio\n");
//echo("					data: {dados: $('#formulario').serialize(), acao: 'CadastraDados'}, //valores enviados ao script\n");
//echo("					complete: function(retorno){\n");
//echo("						trataEnvio(retorno);\n"); //TODO: precisa arrumar essa parte, não estou conseguindo pegar o retorno da função (flag)
//echo("					}\n");
//echo("				});\n");
//echo("			});\n");
//echo("				return event.preventDefault();\n");
//echo("		});\n");	
//echo("    }\n\n");

echo("	$(document).ready(function(){\n");
echo("		$('#formulario').submit(function(){\n");
echo("				if(ValidaLogins() && ValidaSenhas() && verificar()){\n");
echo("					$.post('".$model_cadastro."cadastra_dados_usuario.php', $('#formulario').serialize(),\n");
echo("					function(data) {\n");
echo("						var flag = $.parseJSON(data);\n");
echo("						alert(flag);\n");
//echo("							trataEnvio(flag);\n");
//echo("							if (flag==1){\n");
//echo("								$(\"#span_feedback\").html('".Linguas::RetornaFraseDaLista($lista_frases_configurar,74)."')\n");
//echo("								$(\"#span_feedback\").addClass(\"feedback_error\")\n");
//echo("								$(\"#feedback\").show();\n");
//echo("							}\n");
//echo("							else if (flag==2){\n");
//echo("								alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,75)."');\n");
//echo("							}\n");
//echo("							else if (flag==3){\n");
//echo("								alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,76)."');\n");
//echo("							}\n");
//echo("							alert('".Linguas::RetornaFraseDaLista($lista_frases,184)."');\n");
echo("					});\n");
echo("				}\n");
echo("		});\n");
echo("	});\n");


echo("      function trataEnvio(flag)\n");
echo("      {\n");
echo("        if (flag == '1')\n");
echo("        {\n");
//74 - Login digitado ja existe. Digite outro e tente novamente.
echo("          alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,74)."');\n");
echo("          document.formulario.login.value='';\n");
echo("          document.formulario.login.focus();\n");
echo("        }\n");
echo("        else if(flag == '2')\n");
echo("        {\n");
//75 - E-mail digitado ja existe. Digite outro e tente novamente.
echo("          alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,75)."');\n");
echo("          document.formulario.email.value='';\n");
echo("          document.formulario.email.focus();\n");
echo("        }\n");
echo("        else if(flag == '3')\n");
echo("        {\n");
//76 - Carecteres digitados nao conferem com os da imagem.Tente novamente.
echo("          alert('".Linguas::RetornaFraseDaLista($lista_frases_configurar,76)."');\n");
echo("          var imagem = document.getElementById('imagem');\n");
//   echo("          var src = imagem.src;\n");
//   echo("          imagem.src = '';\n");
echo("          imagem.src = '".$view_geral."imagem.php?reload='+numReload;\n");
echo("          numReload++;");
echo("          document.formulario.resultado.value='';\n");
echo("          document.formulario.resultado.focus();\n");
echo("        }\n");
echo("        else\n");
echo("        {\n");
// 184 - Cadastro efetuado com sucesso.
  echo("          alert('".Linguas::RetornaFraseDaLista($lista_frases,184)."');\n");
//if($acao == "inscricao")
	//echo("xajax_CadastrarLogar(xajax.getFormValues('formulario'),'".$cod_curso."','".$tipo_curso."');\n");
//else
	//echo("          window.location='autentica��o_cadastro.php?acao=emailConfirmacao&atualizacao=true';\n");
echo("        }\n"); 
echo("      }\n\n");

echo("    function VerificaNumero(campo)\n");
echo("    {\n");
echo("      var digits=\"0123456789\";\n");
echo("      var campo_temp;\n");
echo("      for (var i=0;i<campo.value.length;i++)\n");
echo("      {\n");
echo("        campo_temp=campo.value.substring(i,i+1);\n");
echo("        if (digits.indexOf(campo_temp)==-1)\n");
echo("        {\n");
echo("          campo.value = campo.value.substring(0,i);\n");
echo("          break;\n");
echo("        }\n");
echo("      }\n");
echo("    }\n\n");

echo("    </script> \n");

require_once $view_admin.'menu_principal_tela_inicial.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

/*185 - Cadastro*/
echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases,185)."</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".Linguas::RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

echo("          <!-- Tabelao -->\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td>\n");
//echo("                <form name=\"formulario\" id=\"formulario\" action=\"\" method=\"post\" onsubmit=\"return(confereDados());\">\n");
echo("                <form name=\"formulario\" id=\"formulario\" action=\"\" method=\"post\">\n");
//echo("                <form name=\"formulario\" id=\"formulario\" action=\"".$diretorio_ctrlers."realizar_cadastro.php?erro1=".$erro1."&erro2=".$erro2."&erro3=".$erro3."&texto=".$texto."\" method=\"post\" onsubmit=\"return(confereDados());\">\n");
echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
/* 186 - Dados pessoais */
echo("                    <td>".Linguas::RetornaFraseDaLista($lista_frases,186)."</td>\n");
echo("                  </tr>\n");
echo("                  <tr>\n");
echo("                    <td align=\"center\">\n");
/* 73 - Insira seus dados no formulários abaixo e clique em "Cadastrar" para cadastrar-se no ambiente. */
echo("                      <div align=\"left\"><p style=\"text-indent:15px;\">\n");
echo("                         ".Linguas::RetornaFraseDaLista($lista_frases_configurar,73)."\n");
echo("                      </p></div><br />\n");
echo("                      <table>\n");
echo("                          <tr>\n");
/*157 - Login */
echo("                            <td style=\"text-align:right;border:none;\">".Linguas::RetornaFraseDaLista($lista_frases,157).": (*)</td>\n");
echo("                            <td style=\"border:none;text-align:left;\"><input class=\"input\" type=\"text\" name=\"login\" size=\"16\" maxlength=\"16\" /></td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
/* 166 - Digite sua senha: */
echo("                            <td style=\"text-align:right;border:none;\">".Linguas::RetornaFraseDaLista($lista_frases,166).": (*)</td>\n");
echo("                            <td style=\"border:none;text-align:left;\"><input class=\"input\" type=\"password\" name=\"senha\" size=\"16\" maxlength=\"16\" /></td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
// 167 -  Redigite sua senha:
echo("                            <td style=\"text-align:right;border:none;\">".Linguas::RetornaFraseDaLista($lista_frases,167).": (*)</td>\n");
echo("                            <td style=\"border:none;text-align:left;\"><input class=\"input\" type=\"password\" name=\"senha2\" size=\"16\" maxlength=\"16\" /></td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
/* 32 - Nome */
echo("                            <td style=\"border:none; text-align:right;\">\n");

echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,32).": (*)\n");
echo("                            </td>\n");
echo("                            <td width=\"90%\" style=\"border:none;text-align:left;\">\n");
echo("                              <input class=\"input\" type=\"text\" size=\"30\" maxlength=\"128\" name=\"nome_usuario\" value=\"\" />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
/* 72 - RG */
echo("                            <td style=\"border:none;text-align:right;\">\n");
echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,72).": (*)\n");
echo("                            </td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <input class=\"input\" type=\"text\" size=\"11\" maxlength=\"11\" name=\"rg\" value=\"\" />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td style=\"border:none; text-align:right;\">\n");
/* 34 - Data de nascimento */
echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,34).": (*)\n");
echo("                            </td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" id=\"data\" name=\"data_nascimento\" value=\"\" /><img src=\"".$diretorio_imgs."ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById ('data'),'dd/mm/yyyy',this);\" />");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td style=\"border:none; text-align:right;\">\n");
/* 35 - Sexo */
echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,35).":\n");
echo("                            </td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
/* 36 - Masculino */
echo("                              <input type=\"radio\"  name=\"sexo\" value=\"M\" />".Linguas::RetornaFraseDaLista($lista_frases_configurar,36)."\n");
/* 37 - Feminino */
echo("                              <input type=\"radio\"  name=\"sexo\" value=\"F\" />".Linguas::RetornaFraseDaLista($lista_frases_configurar,37)."\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td style=\"border:none; text-align:right;\">\n");
/* 38 - Email */
echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,38).": (*)\n");
echo("                            </td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <input class=\"input\" type=\"text\" size=\"30\" maxlength=\"48\" name=\"email\" value=\"\" />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td style=\"border:none; text-align:right;\">\n");
/* 39 - Telefone */
echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,39).":\n");
echo("                            </td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <input class=\"input\" type=\"text\" size=\"16\" maxlength=\"25\" name=\"telefone\" value=\"\" />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td style=\"border:none; text-align:right;\">\n");
/* 40 - Endere�o */
echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,40).": (*)\n");
echo("                            </td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <input class=\"input\" type=\"text\" size=\"30\" maxlength=\"48\" name=\"endereco\" value=\"\" />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td style=\"border:none; text-align:right;\">\n");
/* 41 - Cidade */
echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,41).": (*)\n");
echo("                            </td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <input class=\"input\" type=\"text\" size=\"20\" maxlength=\"32\" name=\"cidade\" value=\"\" />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td style=\"border:none; text-align:right;\">\n");
/* 42 - Estado */
echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,42).": (*)\n");
echo("                            </td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <input class=\"input\" type=\"text\" size=\"2\" maxlength=\"2\" name=\"estado\" value=\"\" />\n");
/* 43 - Pa�s */
echo("                              &nbsp;&nbsp;&nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,43).": (*)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
echo("                              <input class=\"input\" type=\"text\" size=\"12\" maxlength=\"19\" name=\"pais\" value=\"\" />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td style=\"border:none; text-align:right;\">\n");
/* 44 - Profiss�o */
echo("                                 &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,44).":\n");
echo("                            </td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <input class=\"input\" type=\"text\" size=\"20\" maxlength=\"32\" name=\"profissao\" value=\"\" />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td width=\"50%\" style=\"border:none; text-align:right;\">\n");
/* 45 - Local de trabalho */
echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,45).":\n");
echo("                            </td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <input class=\"input\" type=\"text\" size=\"20\" maxlength=\"32\" name=\"local\" value=\"\" />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td width=\"50%\" style=\"border:none; text-align:right;\">\n");
/* 46 - Escolaridade */
echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,46).":\n");
echo("                            </td>\n");

echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <select class=\"input\" name=\"cod_escolaridade\" size=\"1\">\n");

foreach ($lista_escolaridade as $cod => $linha)
{
		$selecionado="";
	echo("                                <option value='".$linha['cod_escolaridade']."' ".$selecionado.">".Linguas::RetornaFraseDaLista($lista_frases_geral,$linha['cod_texto_escolaridade'])."</option>\n");
}
echo("                              </select>\n");

echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td valign=\"top\" style=\"border:none; text-align:right;\">\n");
/* 47 - Informa��es adicionais */
echo("                              &nbsp;".Linguas::RetornaFraseDaLista($lista_frases_configurar,47).":\n");
echo("                            </td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <textarea class=\"input\" rows=\"5\" cols=\"30\" name=\"informacoes\">\n");
echo("                              </textarea> <br /><br />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td style=\"border:none;\">&nbsp;</td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
/* 66 - (*) Campos Obrigat�rios */
echo("                              ".Linguas::RetornaFraseDaLista($lista_frases_configurar,66)."\n");
echo("                              <br /><br />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
//77 - Digite os cararacteres presentes na imagem ao lado.
//78 - Digite os caracteres.
echo("                            <td align=\"center\" style=\"border:none;text-align:right;\"><img id=\"imagem\" name=\"imagem\" src=\"".$view_geral."imagem.php\" style=\"border: 1px dashed silver;\" /></td>\n");
echo("                            <td align=\"center\" style=\"border:none;text-align:left;\">\n");
echo("                              <input class=\"input\" type=\"text\" name=\"resultado\" size=\"8\" /><br /><small>".Linguas::RetornaFraseDaLista($lista_frases_configurar,77)."</small>\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                          <tr>\n");
echo("                            <td style=\"border:none\">&nbsp;</td>\n");
echo("                            <td style=\"border:none;text-align:left;\">\n");
echo("                              <br /><br /><input type=\"submit\" class=\"input\" name=\"enviar\" value=\"".Linguas::RetornaFraseDaLista($lista_frases,89)."\" id=\"registar_altd\" />\n");
echo("                            </td>\n");
echo("                          </tr>\n");
echo("                        </table>\n");
echo("                    </td>\n");
echo("                  </tr>\n");
echo("                </table>\n");
echo("                </form>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
echo("        </td>\n");
echo("      </tr>\n");
require_once $view_admin.'rodape_tela_inicial.php';
echo("  </body>\n");
echo("</html>\n");

AcessoSQL::Desconectar($sock);

?>





