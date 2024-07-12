<?php

namespace app\models;

class viewsModel
{
  protected function obtenerVistasModelo($vista)
  {
    // los nombres de las vistas
    $listaBlanca = ["dashboard", "userNew", "userList", "userSearch", "userUptade", "userPhoto", "logOut"];
    if (in_array($vista, $listaBlanca)) {
      // verificar si esa vista existe en el directorio va a debolver la vista o si no la vista de 404
      if (is_file("./app/views/contents/$vista-view.php")) {
        $contenido = "./app/views/contents/$vista-view.php";
      } else {
        $contenido = "404";
      }
    } elseif ($vista === "login" || $vista === "index") {
      $contenido = "login";
    } else {
      $contenido = "404";
    }

    return $contenido;
  }
}
