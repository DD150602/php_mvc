<?php

namespace app\controllers;

use app\models\mainModel;

class searchController extends mainModel
{
  public function modulosBusquedaControlador($modulo){
    $listaModulos = ['userSearch'];

    if (in_array($modulo, $listaModulos)) {
      return false;
    } else {
      return true;
    }
    
  }
}
