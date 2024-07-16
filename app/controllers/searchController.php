<?php

namespace app\controllers;

use app\models\mainModel;

class searchController extends mainModel
{
  public function modulosBusquedaControlador($modulo)
  {
    $listaModulos = ['userSearch'];

    if (in_array($modulo, $listaModulos)) {
      return false;
    } else {
      return true;
    }
  }

  public function iniciarBuscadorControlador()
  {
    $url = $this->limpiarCadena($_POST['modulo_url']);
    $texto = $this->limpiarCadena($_POST['txt_buscador']);

    if ($this->modulosBusquedaControlador($url)) {
      $alerta = array(
        "tipo" => "simple",
        "titulo" => "Ocurrio un error inesperado",
        "texto" => "No se pude cargar el modulo solicitado",
        "icono" => "error"
      );
      return json_encode($alerta);
      exit();
    }

    if ($texto == "") {
      $alerta = array(
        "tipo" => "simple",
        "titulo" => "Iniciando la busqueda",
        "texto" => "Introduce un texto para realizar la busqueda",
        "icono" => "error"
      );
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}", $texto)) {
      $alerta = array(
        "tipo" => "simple",
        "titulo" => "Formato no Valido",
        "texto" => "El texto no coincide con el formato solicitado",
        "icono" => "error"
      );
      return json_encode($alerta);
      exit();
    }

    $_SESSION[$url] = $texto;
    $alerta = array(
      "tipo" => "redireccionar",
      "url" => APP_URL . $url . "/"
    );
    return json_encode($alerta);
  }

  public function eliminarBuscadorControlador()
  {

    $url = $this->limpiarCadena($_POST['modulo_url']);

    if ($this->modulosBusquedaControlador($url)) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Ocurrió un error inesperado",
        "texto" => "No podemos procesar la petición en este momento",
        "icono" => "error"
      ];
      return json_encode($alerta);
      exit();
    }

    unset($_SESSION[$url]);

    $alerta = [
      "tipo" => "redireccionar",
      "url" => APP_URL . $url . "/"
    ];

    return json_encode($alerta);
  }
}
