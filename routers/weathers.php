<?php
    require "models/weather.php";

    function route($method,$urlData,$formData)
    {

        //погода по названию или id 
        //api-weather/weathers/{id/name}
        if($method === 'GET' && count($urlData) === 1)
        {
            $action = new WeatherAction();
            if(!is_numeric($urlData[0]))
            {
                $SityName = $urlData[0];

                $weather = $action->GetWeatherByName($SityName);

                echo $weather;
                return;
            }
            else
            {
               echo json_encode(array(
                   'error'=>'Bad parametrs'
               )); 
               return;
            }
            
        }
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(array(
            'error' => 'Bad Request'
        ));
    }
    

?>