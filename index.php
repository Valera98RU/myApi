<?php
session_start();
//Определяем метод запроса
$method = $_SERVER['REQUEST_METHOD'];
//Получаем данные из тела запроса
$formData = getFormData($method);
//Получение данных из тела запроса
Function getFormData($method)
{
    if($method === 'GET') return $_GET;
    if($method === 'POST') return $_POST;

    $data = array();
    $exploded = explode('&', file_get_contents('php://input'));
    
    foreach($exploded as $pair)
    {
        $item = explode('=', $pair);
        if(count($item) == 2)
        {
            $data[urldecode($item[0])]=urldecode($item[1]);
        }        
    }
    

    return $data;
}
//Разбираем Url
$url = (isset($_GET['q']))  ? $_GET['q'] : ''; //isset - проверяет существует ли такая переменная
$url = rtrim($url, '/');    //rtrim удаляет пробелы или другие символы из конца строки
$urls = explode('/', $url); //разбивает строку на подстроки при помощи знака разделителя
//определяем роутер и url data
$router = $urls[0];
$urlData = array_slice($urls, 1);
//Подключаем файл-роутер и запускаем главную функцию
include_once 'routers/'.$router.'.php';
route($method,$urlData,$formData);
?>