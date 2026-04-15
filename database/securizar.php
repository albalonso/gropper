<?php
// Limpia un dato de entrada antes de guardarlo o mostrarlo.
function securizar($datos){
    // Quita espacios, barras de escape y convierte caracteres especiales en entidades HTML.
    return htmlspecialchars(stripslashes(trim($datos)));
}
?>
