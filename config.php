<?php
//
// Назначение модуля: Конфифигурацию сервиса
// Версия: 03102020
// Последние изменения: Перенастроил подключение к рабочему БД-серверу
//
//
//
	$S_CONTENT_ENCODING = "utf-8"; //кодировка страниц
	$S_SITENAME = ""; // название информационной системы
	$S_PAGETITLE = "Главная страница"; // Заголовок страницы (после названия ИС)

	$S_THEME = "Default"; // текущая тема из папки "theme"
	$S_COOKIE = "svodcooks";

	define('DB_SERVER','77.222.63.167'); 		//server
	define('DB_NAME','sed'); 			//database

	define('DB_USERNAME','postgres');		//username
	define('DB_PASSWORD','rfrghjphfxysq'); 	//password
	define('DB_PORT',5432); 			//port

    $string_connect = "host=".DB_SERVER." port=".DB_PORT." dbname=".DB_NAME." user=".DB_USERNAME." password=".DB_PASSWORD;
    $connection = pg_connect($string_connect);



?>