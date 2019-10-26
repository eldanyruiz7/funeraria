function abrirMenu()
{
    href = window.location.href;
    arrayUrl = href.split("/");
    url = arrayUrl[arrayUrl.length - 1];
    aOpcion = $("#nav-list").find($("a[href='"+url+"']"));
    if (aOpcion.length == 1)
    {
        aOpcion.parent().addClass("active");
        aOpcion.parent().addClass("open");
        aOpcion.parent().parent().parent().addClass("active");
        aOpcion.parent().parent().parent().addClass("open");
    }


}
function mensaje(tipo,mensaje)
{
    if (tipo == 'error')
    {
        class_n = 'gritter-error';
        t = '<i class="fa fa-times-circle" aria-hidden="true"></i> Error';
    }
    if (tipo == 'success')
    {
        class_n = 'gritter-success';
        t = '<i class="fa fa-check-circle" aria-hidden="true"></i> Éxito';
    }
    if (tipo == 'info')
    {
        class_n = 'gritter-info';
        t = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Mensaje';
    }
    if (tipo == 'warning')
    {
        class_n = 'gritter-warning gritter-light';
        t = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Alerta';
    }


    $.gritter.add(
    {
        title: t,
        text: mensaje,
        class_name: class_n
    });
}
cargarSpinner = '<i class="fa fa-spinner fa-pulse fa-fw"></i>';
