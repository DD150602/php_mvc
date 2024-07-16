<?php

require_once('../../config/app.php');
require_once('../views/inc/session_start.php');
require_once('../../autoload.php');

use app\controllers\searchController;

// el 'modulo_usuario' es para saber si se esta utilizando el formulario de registrar
if (isset($_POST['modulo_buscador'])) {
  $insBuscador = new searchController();
  if ($_POST['modulo_buscador'] == "buscar") {
    echo $insBuscador->iniciarBuscadorControlador();
  }
  if ($_POST['modulo_buscador'] == "eliminar") {
    echo $insBuscador->eliminarBuscadorControlador();
  }
} else {
  // evitar que se acceda a la ruta 
  session_destroy();
  header("Location: " . APP_URL . "login/");
}
