<?php
$diretorio_models = "../models/";
$diretorio_ctrlers = "../controllers/";
$diretorio_views = "../views/";
$diretorio_jscss = "../../web-content/js-css/";
$diretorio_imgs  = "../../web-content/imgs/";

require_once $diretorio_models.'TesteModel.php';

echo("<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>");

echo("    <script type=\"text/javascript\">\n");

echo("		$(document).ready(function(){\n");
echo("			$('#formulario').submit(function(){");
echo("				var nome = $('#nome').val;");
//echo("      		if(ValidaLogins() && ValidaSenhas() && verificar())\n");
echo("					$.ajax({\n");
echo("						url: '../models/TesteModel.php', //caminho do arquivo a ser executado\n");
//echo("						dataType: 'html', //tipo do retorno\n");
echo("						type: 'post', //metodo de envio\n");
echo("						data: nome, //valores enviados ao script\n");
echo("						success: function(result){\n");
echo("							alert('Dados enviados com sucesso' + result);\n");
echo("						}\n");
echo("					});\n");
echo("			});\n");
echo("		});\n");
echo("</script>");

echo("<html>");
echo("<form name=\"formulario\" id=\"formulario\" action=\"\" method=\"post\">\n");
echo("	<tr>");
echo("  	<td>Nome:<input type=\"text\" name=\"nome\" id=\"nome\"></input></td>");
echo("	</tr>");
echo("<br /><br /><input type=\"submit\" name=\"enviar\" value=\"Enviar\"/>\n");
echo("	</html>");