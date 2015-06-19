<?php

require_once '../controller/UsuarioController.php';

$usuarioController = new UsuarioController();

$resultado = $usuarioController->listaUsuarios();

$total = count($resultado);

?>
<table border="1" cellspacing="3" cellpadding="0">
    <tr>
        <th>ID</th>
        <th>NOME</th>
        <th>LOGIN</th>
        <th>EMAIL</th>
    </tr>
    <?php if ($total > 0) { 
        for ($i = 0; $i < $total; $i++) {
            $idUsuario = $resultado[$i]['cod_usuario'];
            $nome = $resultado[$i]['nome_usuario'];
            $login = $resultado[$i]['login'];
            $email = $resultado[$i]['email'];
            ?>
            <tr>
                <td><?php echo $idUsuario; ?></td>
                <td><?php echo $nome; ?></td>
                <td><?php echo $login; ?></td>
                <td><?php echo $email; ?></td>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
        <th colspan="4">Total: <?php echo $total; ?></th>
    </tr>
</table>