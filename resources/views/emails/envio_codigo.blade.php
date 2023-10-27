<h3>Estimado/a {{ $user->name }}</h3><br><br>

<p>Ya tienes acceso a tu área privada dentro de nuestra plataforma de monitorización
energética (Submeter 4.0). A partir de ahora podrás acceder a todos los servicios de
nuestra plataforma, con la programación de informes, alertas y otra documentación
dentro de tu área personal.</p>

<p>Tus dados de acceso son:</p><br>
<p>Email: {!! $user->email !!}</p><br>
<p>Contraseña: {!! $codigo !!}</p><br><br>

<p>Puedes acceder a tu espacio directamente desde nuestra web (<a href="http://www.submeter.es">www.submeter.es</a>) o
hacerlo directamente desde <a href="{!! route('registro.usuario') !!}">aquí</a> para que accedas a tu configuración personal.</p>

<b style="font-size: 14pt;">(ESTE ES UN MENSAJE AUTOMÁTICO DE NUESTRO SERVIDOR, POR FAVOR NO
RESPONDAS AL MISMO. SI TIENES ALGUNA DUDA PONTE EN CONTACTO CON
NOSOTROS A TRAVÉS DEL CORREO ELECTRÓNICO INFO@SUBMETER.ES)</b>

<p>Gracias por tu confianza y un cordial saludo,</p><br>
<p>Equipo Gestor</p><br>
<b>Plataforma Submeter 4.0 | Grupo 3S</b><br>
<a href="#">info@submeter.es</a><br>
<a href="#">info@3seficiencia.com</a><br>
<p>902 00 28 12</p>