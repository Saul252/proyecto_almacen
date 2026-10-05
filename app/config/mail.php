<?php
// app/config/mail.php

return [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'encryption' => 'tls', // o 'ssl' si usas puerto 465
    'username' => 'saulenriquealbatapia252@gmail.com',
    'password' => 'tvbtksegtbcngmzn', // 16 caracteres
    'from' => 'saulenriquealbatapia252@gmail.com',
    'from_name' => 'Sistema CFSistem',
    'charset' => 'UTF-8',
];
// matcasa configuracion mail
//return [
//     'host' => 'mail.fortalezacentro.com.mx',
//     'port' => 587,
//     'encryption' => 'tls', // o 'ssl' si usas puerto 465
//     'username' => 'veronica.gonzaga@fortalezacentro.com.mx',
//     'password' => 'Bfc2612vgt', // 16 caracteres
//     'from' => 'veronica.gonzaga@fortalezacentro.com.mx',
//     'from_name' => 'Sistema CFSistem',
//     'charset' => 'UTF-8',
// ];