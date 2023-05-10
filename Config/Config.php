<?php
const BASE_URL = 'http://127.0.0.1/sistema-pos-php-mysql-valiadmin/';
date_default_timezone_set('America/Argentina/Buenos_Aires');
const DB_HOST = "127.0.0.1";
const DB_NAME = "softboos";
const DB_USER = "root";
const DB_PASSWORD = "";
const DB_CHARSET = "charset=utf8";
const PSD = ",";
const SPM = ".";
const SMONEY = "$";
//duracion en segundos
//60 segundos = 1 minuto
//600segundos = 10minutos
//60seg*60min = 3600 segundos
//3600seg*8horas = 28800 segundos
//3600seg*12horas = 43200 segundos
const SESSION_EXPIRE = 43200;
?>