=== Estado Reparaciones ===
Contributors: etruel
Requires at least: 4.6
Tested up to: 4.6
Stable tag: 1.1
License: All Right reserved

Consulta los estados de reparaciones desde un archivo externo y permite la consulta de estado por número de Orden desde el front-end.

== Description ==
En el admin se lee el archivo y desde una página del front se le agrega el shortcode para la consulta.
El archivo debe encontrarse en dominio.com/ots/ordenesdetrabajo.txt para que lo lea.  (Copiar la carpeta ots del plugin al raiz)
Debe tener un formato similar a:
000002|ENTREGADO|OBS ESTADO ENTREGADO|22/02/2013 13:59|08/03/2013|SILVANA CHITARO|SEC.GAMA MYSTERE 4000||CAMBIO DE CARCAZA DELANTERA|31.00

== Changelog ==

= 1.1 = 21/11/2016
Cambiado el formato del archivo al de la descripción.
Agregada la devolución de los datos por ajax a una tabla.
Agregada protección al archivo txt para que no se pueda acceder directamente.

= 1.0 =
Initial release