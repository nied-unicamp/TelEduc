<?php
$ferramenta_geral = 'geral';

$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_geral.'importar.inc';

$cod_categoria=$_POST['cod_categoria'];
$cod_topico_raiz=$_POST['cod_topico_raiz'];
$tipo_curso=$_POST['tipo_curso'];
$cod_ferramenta=$_POST['cod_ferramenta'];
$data_inicio=Data::Data2UnixTime($_POST['data_inicio']);
$data_fim=Data::Data2UnixTime($_POST['data_fim']);
$frase = $_POST['extraido'];

$sock=AcessoSQL::Conectar("");

$todos_cursos = Importar::RetornaTodosCursos($sock, $tipo_curso, $cod_categoria, $data_inicio, $data_fim);

$cursos_compart = Importar::RetornaCursosEncerradosCompart($sock, $cod_ferramenta, $cod_categoria, $data_inicio, $data_fim);

AcessoSQL::Desconectar($sock);

echo json_encode(array(cod_curso_todos => $todos_cursos, cod_cursos_compart => $cursos_compart));
?>