const formularios_ajax = document.querySelectorAll(".FormularioAjax")

formularios_ajax.forEach(formularios => {
  formularios.addEventListener('submit', function(e){
    e.preventDefault()

    Swal.fire({
      title: 'Estas Seguro?',
      text: "Quieres Realizar la accion seleccionada?",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, Realizar',
      cancelButtonText: 'No, Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        let data  = new FormData(this)
        let method = this.getAttribute('method')
        let action = this.getAttribute('action')
        let encabezados = new Headers()
        let config = {
          method,
          headers: encabezados,
          mode: 'cors',
          cache: 'no-cache',
          body: data
        }

        fetch(action, config)
        .then(response => response.json())
        .then(data => {
          return alertas_ajax(data)
        })
      }
    })

  })
});

function alertas_ajax(alerta){
  if(alerta.tipo === 'simple'){
    Swal.fire({
      icon: alerta.icono,
      title: alerta.titulo,
      text: alerta.texto,
      confirmButtonText: 'Aceptar'
    });
  }else if (alerta.tipo === 'recargar'){
    Swal.fire({
      icon: alerta.icono,
      title: alerta.titulo,
      text: alerta.texto,
      confirmButtonText: 'Aceptar'
    }).then((result) => {
      if(result.isConfirmed){
        location.reload()
      }
    });
  }else if (alerta.tipo === 'limpiar'){
    Swal.fire({
      icon: alerta.icono,
      title: alerta.titulo,
      text: alerta.texto,
      confirmButtonText: 'Aceptar'
    }).then((result) => {
      if(result.isConfirmed){
        document.querySelector('.FormularioAjax').reset()
      }
    });
  }else if(alerta.tipo === 'redireccionar'){
    window.location.href = alerta.url
  }
}

let btn_exit = document.getElementById('btn-exit')
btn_exit.addEventListener('click', function(e){
  e.preventDefault()
  Swal.fire({
    title: 'Quieres salir de la pagina?',
    text: "la sesion actual se cerrara y saldras de la pagina",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Realizar',
    cancelButtonText: 'No, Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      let url = this.getAttribute('href')
      window.location.href = url
    }
  })
})