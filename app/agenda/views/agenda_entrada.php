<html>
<body>
<pre>

<?php

$diretorio = $_GET['diretorio'];
$entrada = $_GET['entrada'];

if(preg_match('/\.php(\.)*/', $entrada)){
	echo(htmlentities(file_get_contents($diretorio.$entrada)));
}
?>
</pre>
</body>
</html>