<?php
    require "models/sity.php";
    function route($method,$urlData,$formData)
    {
      /*  if($method === 'GET' && count($urlData)===0)
        {
            $Action = new SityAction();

           $s= $Action->LoadSityInServer();

          
        }*/
        //показать список городов
        //api_weather/sitys/{page_number}/top
        if($method === 'GET' && count($urlData)===2 && $urlData[1] === 'top')
        {
            $action = new SityAction();
            if(is_numeric($urlData[0]))
            {
            $page = intval($urlData[0]);

            $SityList = $action->GETSityListTop($page);

            echo json_encode($SityList);
            }
            else
            echo json_encode(array(
                'error'=>'Bad parametrs'
            ));
            return;
        }
        //поиск городов по названию
        //api_weather/sitys/{text}/search
        if($method==='GET' && count($urlData)===2 && $urlData[1]==='search')
        {
            $action = new SityAction();
            $list = $action->GSearchSitys($urlData[0]);

            echo json_encode($list);
            return;
        }
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(array(
            'error' => 'Bad Request'
        ));
    }
?>