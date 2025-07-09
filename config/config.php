<?php
define('MAIL_HOST', 'smtp.ionos.es');
define('MAIL_USER', 'info@aznaitin.es');//este es el servidor de correo que te envia el correo (HAY QUE HACERSE UNA CUENTA DE CORREO PARA PODER ENVIAR CORREOS)
define('MAIL_PASS', 'SanFermin$7_Marisol');
define('DEBUG_MAIL', false);

$host = $_SERVER['HTTP_HOST'];
$serverName = $_SERVER['SERVER_NAME'];


if($host === 'localhost' ||
$host === '127.0.0.1:'||
$serverName ==='localhost' ||
$serverName === '127.0.0.1' ||
strpos($host, 'localhost') === 0 ||
strpos($host, '127.0.0.1:') === 0){
    //hecho 'Entorno de desarrollo local';
    //configuracion para desarrollo

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gimnasio');
define('URL_ADMIN', 'http://localhost/GIMNASIO8DEL7/admin');
define('BASE_URL', '/GIMNASIO8DEL7/');
} else {
    //hecho 'entorno de desarrollo en la nube'
    //configuracion para la produccion

define('DB_HOST', 'db5018152583.hosting-data.io');
 define('DB_USER', 'dbu1105751');
define('DB_PASS', '76065850');
 define('DB_NAME', 'dbs14399129'); 
 define('URL_ADMIN', 'http://www.alumnamarisol.com/GIMNASIO8DEL7/admin');
 define('BASE_URL', '/GIMNASIO8DEL7/');

}
