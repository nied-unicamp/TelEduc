<html>
<body>
<pre>

<?php

 if(preg_match('/\.php(\.)*/', $entrada)){
  echo(htmlentities(file_get_contents($diretorio.$entrada)));
}
?>
</pre>
</body>
</html>