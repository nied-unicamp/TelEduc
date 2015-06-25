<?php

require_once '../controller/CursoController.php';

$cursoController = new CursoController();

$resultado = $cursoController->listaCursos();

$total = count($resultado);

?>
<table border="1" cellspacing="3" cellpadding="0">
    <tr>
        <th>CODIGO CURSO</th>
        <th>NOME CURSO</th>
        <th>INSCRICAO INICIO</th>
        <th>INSCRICAO FIM</th>
         <th>CURSO INICIO</th>
        <th>CURSO FIM</th>
         <th>INFORMACOES</th>
        <th>PUBLICO ALVO</th>
        <th>PERMITE INSCRICAO PUBLICA</th>
         <th>NUMERO ALUNOS</th>
        <th>CODIGO COORDENADOR</th>
        <th>PERMITE ACESSO VISITANTE</th>
        <th>CODIGO LINGUA</th>
    </tr>
    <?php if ($total > 0) { 
        for ($i = 0; $i < $total; $i++) {
            $cod_curso = $resultado[$i]['cod_curso'];
            $nome_curso = $resultado[$i]['nome_curso'];
            $inscricao_inicio = $resultado[$i]['inscricao_inicio'];
            $inscricao_fim = $resultado[$i]['inscricao_fim'];
            $curso_inicio = $resultado[$i]['curso_inicio'];
            $curso_fim = $resultado[$i]['curso_fim'];
            $informacoes = $resultado[$i]['informacoes'];
            $publicoalvo = $resultado[$i]['publico_alvo'];
            $permite_inscricao_publica = $resultado[$i]['permite_inscricao_publica'];
            $numeros_alunos = $resultado[$i]['numero_alunos'];
            $cod_coordenador = $resultado[$i]['cod_coordenador'];
            $permite_acesso_visitante = $resultado[$i]['permite_acesso_visitante'];
            $cod_lingua = $resultado[$i]['cod_lingua'];

            ?>
            <tr>

                <td><?php echo $cod_curso; ?></td>
                <td><?php echo $nome_curso; ?></td>
                <td><?php echo $inscricao_inicio; ?></td>
                <td><?php echo $inscricao_fim; ?></td>
                <td><?php echo $curso_inicio; ?></td>
                <td><?php echo $curso_fim; ?></td>
                <td><?php echo $informacoes; ?></td>
                <td><?php echo $publicoalvo; ?></td>
                <td><?php echo $permite_inscricao_publica; ?></td>
                <td><?php echo $numeros_alunos; ?></td>
                <td><?php echo $cod_coordenador; ?></td>
                <td><?php echo $permite_acesso_visitante; ?></td>
                <td><?php echo $cod_lingua; ?></td>
           
            </tr>
            <?php
        }
    }
    ?>
    <tr>
        <th colspan="3">Total: <?php echo $total; ?></th>
    </tr>
</table>