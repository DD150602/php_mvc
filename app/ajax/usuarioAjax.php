<?php

require_once('../../config/app.php');
require_once('../views/inc/session_start.php');
require_once('../../autoload.php');

use app\controllers\userController;

// el 'modulo_usuario' es para saber si se esta utilizando el formulario de registrar
if (isset($_POST['modulo_usuario'])) {
  $insUsuario = new userController();

  if ($_POST['modulo_usuario'] == "registrar") {
    echo $insUsuario->registrarUsuarioControlador();
  }
  if ($_POST['modulo_usuario'] == "eliminar") {
    echo $insUsuario->eliminarUsuarioControlador();
  }
} else {
  // evitar que se acceda a la ruta 
  session_destroy();
  header("Location: " . APP_URL . "login/");
}
