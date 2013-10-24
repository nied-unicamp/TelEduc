<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : instalacao/template_instalacao.php

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
  ARQUIVO : instalacao/template_instalacao.php
  ========================================================== */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>TelEduc - Instala&ccedil;&atilde;o do Ambiente</title>
	<meta name="robots" content="follow,index" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="owner" content="" />
	<meta name="copyright" content="" />
	<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
	<link rel="shortcut icon" href="../favicon.ico" />
	<link rel="stylesheet" type="text/css" href="instalacao.css" />
	
	<script type="text/javascript">
		function valida_form(form, etapa) {
			var form_info = new Array();
			switch(etapa) {
				case 1:

					form_info[0] = form.dbname;
					form_info[1] = form.dbnamecurso;
					form_info[2] = form.dbuser;
					form_info[3] = form.dbpwd;
					form_info[4] = form.dbhost;
					form_info[5] = form.dbport;

					break;

				case 2:

					form_info[0] = form.host;
					form_info[1] = form.www;
					form_info[2] = form.arquivos;
					form_info[3] = form.sendmail;

					break;

				case 3:

					form_info[0] = form.admtele_nome;
					form_info[1] = form.admtele_email;
					form_info[2] = form.admtele_senha;

					break;

				default:
					return false;
			}

			for (var i = 0; i < form_info.length; i++ ) {
				if (form_info[i].value == '') {
					alert('Nenhum campo deve ser deixado em branco.');
					form_info[i].focus();
					return false;
				}
			}
			
			return true;
      }
</script>
</head>

<body>
	<div class="container">
		<div id="logo"><img src="../imgs/logo.gif"/></div>
		<div id="header"><h1>Instala&ccedil;&atilde;o do TelEduc <?php echo VERSAO; ?></h1></div>
		<div class="content-header">
			<p><?php echo $content_header;?></p>
		</div>
		<div class="console">
			<p id="console-header"><?php echo($console == "" ? "O progresso da instala&ccedil;&atilde;o ser&aacute; exibido aqui." : "Progresso da Instala&ccedil;&atilde;o"); ?></p>
			<?php echo $console;?>
		</div>
		<div class="content">
			<p><?php echo $content;?></p>
		</div>
		<div class="footer">
			<div id="logo_sampler">
				<a href="http://www.nied.unicamp.br"><img src="../imgs/logoNied.gif" alt="nied" border="0" style="margin-right:8px;" /></a>
				<a href="http://www.ic.unicamp.br"><img src="../imgs/logoInstComp.gif" alt="Instituto de Computa&ccedil;&atilde;o" border="0" style="margin-right:6px;" /></a>
				<a href="http://www.unicamp.br"><img src="../imgs/logoUnicamp.gif" alt="UNICAMP" border="0" /></a>
			</div>
			<div id="signature">
				<p>2013 - TelEduc - Todos os direitos reservados. All rights reserved - NIED - UNICAMP</p>
			</div>
		</div>
	</div>
</body>

</html>
