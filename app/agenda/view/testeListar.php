<?php

require_once '../controller/AgendaController.php';

$agendaController = new AgendaController();

$resultado = $agendaController->listaAgendas();

$total = count($resultado);

?>
<table border="1" cellspacing="3" cellpadding="0">
    <tr>
        <th>ID</th>
        <th>TITULO</th>
        <th>TEXTO</th>
    </tr>
    <?php if ($total > 0) { 
        for ($i = 0; $i < $total; $i++) {
            $idAgenda = $resultado[$i]['cod_item'];
            $tituloAgenda = $resultado[$i]['titulo'];
            $textoAgenda = $resultado[$i]['texto'];
            ?>
            <tr>
                <td><?php echo $idAgenda; ?></td>
                <td><?php echo $tituloAgenda; ?></td>
                <td><?php echo $textoAgenda; ?></td>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
        <th colspan="3">Total: <?php echo $total; ?></th>
    </tr>
</table>