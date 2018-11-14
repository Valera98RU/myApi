<?php
require "DBConnect.php";

class WeatherAction
{
    /*public function GetWeatherById($sity_id)
    {
        //http://api.openweathermap.org/data/2.5/weather?q={NameSity}&units=metric&appid=832ed9e040eb19e580da64396831a6c3

        $weather = "http://api.openweathermap.org/data/2.5/weather?id=".$sity_id."&units=metric&appid=832ed9e040eb19e580da64396831a6c3";

        $result = file_get_contents($weather);

        return $result;
    }*/
    public function GetWeatherByName($sity_name)
    {
        //http://api.openweathermap.org/data/2.5/weather?q={NameSity}&units=metric&appid=832ed9e040eb19e580da64396831a6c3

        try{
            $weather = "http://api.openweathermap.org/data/2.5/weather?q=".$sity_name."&units=metric&appid=832ed9e040eb19e580da64396831a6c3";

            $result = file_get_contents($weather);
        }
        catch(Exception $ex)
        {
            $result = $ex->getMessage();
        }
        return $result;
    }
}
?>