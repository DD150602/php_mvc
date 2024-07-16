<?php

namespace app\controllers;

use app\models\mainModel;

class userController extends mainModel
{
  public function registrarUsuarioControlador()
  {
    $nombre = $this->limpiarCadena($_POST['usuario_nombre']);
    $apellido = $this->limpiarCadena($_POST['usuario_apellido']);

    $usuario = $this->limpiarCadena($_POST['usuario_usuario']);
    $email = $this->limpiarCadena($_POST['usuario_email']);
    $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
    $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);

    if ($nombre == "" || $apellido == "" || $usuario == "" || $clave1 == "" || $clave2 == "") {
      $alerta = array(
        "tipo" => "simple",
        "titulo" => "Campos obligatorios sin rellenar",
        "texto" => "No has llenado todos los campos que son obligatorios",
        "icono" => "error"
      );
      return $alerta;
    }

    if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
      $alerta = array(
        "tipo" => "simple",
        "titulo" => "Formato no Valido",
        "texto" => "El NOMBRE no coincide con el formato solicitado",
        "icono" => "error"
      );
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)) {
      $alerta = array(
        "tipo" => "simple",
        "titulo" => "Formato no Valido",
        "texto" => "El APELLIDO no coincide con el formato solicitado",
        "icono" => "error"
      );
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
      $alerta = array(
        "tipo" => "simple",
        "titulo" => "Formato no Valido",
        "texto" => "El USUARIO no coincide con el formato solicitado",
        "icono" => "error"
      );
      return json_encode($alerta);
      exit();
    }

    if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave2)) {
      $alerta = array(
        "tipo" => "simple",
        "titulo" => "Formato no Valido",
        "texto" => "Las CLAVES no coinciden con el formato solicitado",
        "icono" => "error"
      );
      return json_encode($alerta);
      exit();
    }

    if ($email != "") {
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $check_email = $this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
        if ($check_email->rowCount() > 0) {
          $alerta = array(
            "tipo" => "simple",
            "titulo" => "Correo ya registrado",
            "texto" => "El EMAIL que acaba de ingresar ya se encuentra registrado en el sistema, por favor verifique e intente nuevamente",
            "icono" => "error"
          );
          return json_encode($alerta);
          exit();
        }
      } else {
        $alerta = array(
          "tipo" => "simple",
          "titulo" => "Formato no Valido",
          "texto" => "Ha ingresado un correo electrónico no valido",
          "icono" => "error"
        );
        return json_encode($alerta);
        exit();
      }
    }

    if ($clave1 != $clave2) {
      $alerta = array(
        "tipo" => "simple",
        "titulo" => "Contrasenias no coinciden",
        "texto" => "Las contraseñas que acaba de ingresar no coinciden, por favor verifique e intente nuevamente",
        "icono" => "error"
      );
      return json_encode($alerta);
      exit();
    } else {
      $clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost" => 10]);
    }

    $check_usuario = $this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
    if ($check_usuario->rowCount() > 0) {
      $alerta = array(
        "tipo" => "simple",
        "titulo" => "Usuario ya registrado",
        "texto" => "El USUARIO ingresado ya se encuentra registrado, por favor elija otro",
        "icono" => "error"
      );
      return json_encode($alerta);
      exit();
    }

    $img_dir = "../views/fotos/";

    if ($_FILES['usuario_foto']['name'] != "" && $_FILES['usuario_foto']['size'] > 0) {

      if (!file_exists($img_dir)) {
        if (!mkdir($img_dir, 0777)) {
          $alerta = array(
            "tipo" => "simple",
            "titulo" => "Ocurún un error inesperado",
            "texto" => "Error al crear el directorio",
            "icono" => "error"
          );
          return json_encode($alerta);
          exit();
        }
      }

      if (mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/png") {
        $alerta = array(
          "tipo" => "simple",
          "titulo" => "Formato no Valido",
          "texto" => "La imagen que ha seleccionado es de un formato no permitido",
          "icono" => "error"
        );
        return json_encode($alerta);
        exit();
      }

      if (($_FILES['usuario_foto']['size'] / 1024) > 5120) {
        $alerta = array(
          "tipo" => "simple",
          "titulo" => "Imagen demasiado pesada",
          "texto" => "La imagen que ha seleccionado supera el peso permitido",
          "icono" => "error"
        );
        return json_encode($alerta);
        exit();
      }

      $foto = str_ireplace(" ", "_", $nombre);
      $foto = $foto . "_" . rand(0, 100);

      switch (mime_content_type($_FILES['usuario_foto']['tmp_name'])) {
        case 'image/jpeg':
          $foto = $foto . ".jpg";
          break;
        case 'image/png':
          $foto = $foto . ".png";
          break;
      }

      chmod($img_dir, 0777);

      if (!move_uploaded_file($_FILES['usuario_foto']['tmp_name'], $img_dir . $foto)) {
        $alerta = array(
          "tipo" => "simple",
          "titulo" => "Ocurrió un error inesperado",
          "texto" => "No podemos subir la imagen al sistema en este momento",
          "icono" => "error"
        );
        return json_encode($alerta);
        exit();
      }
    } else {
      $foto = "";
    }

    $usuario_datos_reg = [
      [
        "campo_nombre" => "usuario_nombre",
        "campo_marcador" => ":Nombre",
        "campo_valor" => $nombre
      ],
      [
        "campo_nombre" => "usuario_apellido",
        "campo_marcador" => ":Apellido",
        "campo_valor" => $apellido
      ],
      [
        "campo_nombre" => "usuario_usuario",
        "campo_marcador" => ":Usuario",
        "campo_valor" => $usuario
      ],
      [
        "campo_nombre" => "usuario_email",
        "campo_marcador" => ":Email",
        "campo_valor" => $email
      ],
      [
        "campo_nombre" => "usuario_clave",
        "campo_marcador" => ":Clave",
        "campo_valor" => $clave
      ],
      [
        "campo_nombre" => "usuario_foto",
        "campo_marcador" => ":Foto",
        "campo_valor" => $foto
      ],
      [
        "campo_nombre" => "usuario_creado",
        "campo_marcador" => ":Creado",
        "campo_valor" => date("Y-m-d H:i:s")
      ],
      [
        "campo_nombre" => "usuario_actualizado",
        "campo_marcador" => ":Actualizado",
        "campo_valor" => date("Y-m-d H:i:s")
      ]
    ];

    $registrar_usuario = $this->guardarDatos("usuario", $usuario_datos_reg);

    if ($registrar_usuario->rowCount() == 1) {
      $alerta = array(
        "tipo" => "limpiar",
        "titulo" => "Usuario registrado",
        "texto" => "El usuario " . $nombre . " " . $apellido . " se registro con exito",
        "icono" => "success"
      );
    } else {

      if (is_file($img_dir . $foto)) {
        chmod($img_dir . $foto, 0777);
        unlink($img_dir . $foto);
      }

      $alerta = array(
        "tipo" => "simple",
        "titulo" => "Ocurrió un error inesperado",
        "texto" => "No se pudo registrar el usuario, por favor intente nuevamente",
        "icono" => "error"
      );
    }

    return json_encode($alerta);
  }

  public function listarUsuarioControlador($pagina, $registros, $url, $busqueda)
  {

    $pagina = $this->limpiarCadena($pagina);
    $registros = $this->limpiarCadena($registros);

    $url = $this->limpiarCadena($url);
    $url = APP_URL . $url . "/";

    $busqueda = $this->limpiarCadena($busqueda);
    $tabla = "";

    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
    $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

    if (isset($busqueda) && $busqueda != "") {

      $consulta_datos = "SELECT * FROM usuario WHERE ((usuario_id!='" . $_SESSION['id'] . "' AND usuario_id!='1') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%')) ORDER BY usuario_nombre ASC LIMIT $inicio,$registros";

      $consulta_total = "SELECT COUNT(usuario_id) FROM usuario WHERE ((usuario_id!='" . $_SESSION['id'] . "' AND usuario_id!='1') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%'))";
    } else {

      $consulta_datos = "SELECT * FROM usuario WHERE usuario_id!='" . $_SESSION['id'] . "' AND usuario_id!='1' ORDER BY usuario_nombre ASC LIMIT $inicio,$registros";

      $consulta_total = "SELECT COUNT(usuario_id) FROM usuario WHERE usuario_id!='" . $_SESSION['id'] . "' AND usuario_id!='1'";
    }

    $datos = $this->ejecutarConsulta($consulta_datos);
    $datos = $datos->fetchAll();

    $total = $this->ejecutarConsulta($consulta_total);
    $total = (int) $total->fetchColumn();

    $numeroPaginas = ceil($total / $registros);

    $tabla .= '
        <div class="table-container">
          <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <thead>
              <tr>
                <th class="has-text-centered">#</th>
                <th class="has-text-centered">Nombre</th>
                <th class="has-text-centered">Usuario</th>
                <th class="has-text-centered">Email</th>
                <th class="has-text-centered">Creado</th>
                <th class="has-text-centered">Actualizado</th>
                <th class="has-text-centered" colspan="3">Opciones</th>
              </tr>
            </thead>
          <tbody>
      ';

    if ($total >= 1 && $pagina <= $numeroPaginas) {
      $contador = $inicio + 1;
      $pag_inicio = $inicio + 1;
      foreach ($datos as $rows) {
        $tabla .= '
          <tr class="has-text-centered" >
            <td>' . $contador . '</td>
            <td>' . $rows['usuario_nombre'] . ' ' . $rows['usuario_apellido'] . '</td>
            <td>' . $rows['usuario_usuario'] . '</td>
            <td>' . $rows['usuario_email'] . '</td>
            <td>' . date("d-m-Y  h:i:s A", strtotime($rows['usuario_creado'])) . '</td>
            <td>' . date("d-m-Y  h:i:s A", strtotime($rows['usuario_actualizado'])) . '</td>
            <td>
              <a href="' . APP_URL . 'userPhoto/' . $rows['usuario_id'] . '/" class="button is-info is-rounded is-small">Foto</a>
            </td>
            <td>
              <a href="' . APP_URL . 'userUpdate/' . $rows['usuario_id'] . '/" class="button is-success is-rounded is-small">Actualizar</a>
            </td>
            <td>
              <form class="FormularioAjax" action="' . APP_URL . 'app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >

                <input type="hidden" name="modulo_usuario" value="eliminar">
                <input type="hidden" name="usuario_id" value="' . $rows['usuario_id'] . '">

                <button type="submit" class="button is-danger is-rounded is-small">Eliminar</button>
              </form>
            </td>
          </tr>
        ';
        $contador++;
      }
      $pag_final = $contador - 1;
    } else {
      if ($total >= 1) {
        $tabla .= '
          <tr class="has-text-centered" >
            <td colspan="7">
              <a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
                Haga clic acá para recargar el listado
              </a>
            </td>
          </tr>
        ';
      } else {
        $tabla .= '
        <tr class="has-text-centered" >
          <td colspan="7">
            No hay registros en el sistema
          </td>
        </tr>
        ';
      }
    }

    $tabla .= '</tbody></table></div>';

    if ($total > 0 && $pagina <= $numeroPaginas) {
      $tabla .= '<p class="has-text-right">Mostrando usuarios <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';

      $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
    }

    return $tabla;
  }
}
