<?php
// lo que hace la funcion es verificar que el archivo exista en el directorio
// e incluir el codigo de la clase
spl_autoload_register(function ($class) {
  $archivo = __DIR__ . "/" . $class . ".php";
  $archivo = str_replace("\\", "/", $archivo);

  if (is_file($archivo)) {
    require_once($archivo);
  }
});
