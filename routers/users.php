<?php
//роутер
    require "models/user.php";
    function route($method,$urlData,$formData)
    {
        //получаем информацию о пользователе
        //GET /users/{iduser}/info
        if($method === 'GET' && count($urlData) === 2 && $urlData[1] === 'info')
        {
            //получаем id пользователя
            $user_id = $urlData[0];
            //Вытаскиваем пользователя из базы данных
            if(!is_numeric($user_id))
            {
                echo json_encode(array(
                    'answer'=>array(
                        'type'=>'error',
                        'content'=>'deb argument'

                    )
                ));
            }
            $action= new UserAction();
           $user= $action->GETUser($user_id);
            //Выводим ответ клиенту          
            if(!empty($user->login))
            {
            echo json_encode(array(
                'answer'=>array(
                    'type'=>'user',
                    'content'=>array(
                        'id'=>$user->id,
                        'login'=>$user->login,
                        'name'=>$user->name,
                        'email'=>$user->email,
                        'key'=>$_SESSION['key']
                    )
                )
            ));
            }
            else
            {
                header('HTTP/1.0 404 Not Found');
                echo json_encode(array(
                    'answer'=>array(
                        'type'=>'error',
                        'content'=>'user not found'
                    )
                ));
            }
            return;
        }

        //возврвт ошибок
        //регистрация пользователя
        //api_weather/users POST: login(str) password(str) name(str) email(str)
        if($method === 'POST' && empty($urlData) && isset($formData['name']) && isset($formData['email']))
        {
            $action = new UserAction();
           
            $count = $action->SingUp($formData['login'],$formData['password'],$formData['name'],$formData['email']);

            //echo $count;
            echo json_encode(array(
                'Method'=>'POST',
                'formData'=>$formData,
                'count'=>$count
            ));
            return;
        }
        //авторезайия пользователя
        //api_weather/users  POST: login(str) password(str)
        if($method ==='POST' && empty($urlData) && isset($formData['login']) && isset($formData['password']) && empty($formData['name']) && empty($formData['email']))
        {

            $action = new UserAction();

            $count = $action->SingIn($formData['login'],$formData['password']);

            
            echo $count;
            return;
        }

        //добавление горада в список избранных
        //api_weather/users    POST: idUser(int),  idSity(int), key(str);
        if($method === 'POST' && empty($urlData) && isset($formData['user_id']) && isset($formData['sity_id']))
        {
            $key = $formData['key'];

            if(!password_verify($key,$_SESSION['key']))            
            {
                header('HTTP/1.0 401 Unauthorized');
                echo json_encode(array(
                    'ansver'=>array(
                        'type'=>'error',
                        'content'=>'key does not fit'
                    )
                ));
                return;
            }
            if(is_numeric($formData['user_id']))
            {
            $idUser = intval($formData['user_id']);
            echo 'user_id проверено'.$idUser.'';
            }
            else
            echo 'user_id не int'.$idUser.'';
            if(is_numeric($formData['sity_id']))
            {
                
            $idSity = intval($formData['sity_id']);
            echo 'sity_id проверено'.$idSity.'';
            }
            else
            echo 'sity_id не int'.$idSity.'';

            $action = new UserAction(); 
            $result = $action->AddSityInFavorite($idUser,$idSity);

            echo $result;
            return;
        }
        //получение списка избраных городов 
        //api_weather/users/{user_id}/{key}/favoritesity
        if($method === 'GET' && count($urlData)===3 && $urlData[2] === 'favoritesity')
        {
            $user_id = $urlData[0];
            $key = $urlData[1];
            if(!password_verify($key,$_SESSION['key']))            
            {
                header('HTTP/1.0 401 Unauthorized');
                echo json_encode(array(
                    'ansver'=>array(
                        'type'=>'error',
                        'content'=>'key does not fit'
                    )
                ));
                return;
            }
            if(is_numeric($user_id))
            {
                $action = new UserAction();
                $user_id = intval($user_id);
                $list = $action->GetFavoritSity($user_id);
                echo $list;
                return;

            }
            else
            {
                echo json_encode(array(
                    'error'=>'id is not integer'
                ));
                return;
            }
        }
        //изменение пользовательской инвормации
        //api_weather/users
        if($method==='UPDATE' && empty($urlData) )
        {
            $user_id = $formData['user_id'];
            $name = $formData['user_name'];
            $email = $formData['user_email'];

            if(!password_verify($key,$_SESSION['key']))
            {
            $action = new UserAction();

            $result = $action->UpdateUser($user_id,$name,$email);

            echo $result;
            return;
            }
            else
            {
                header('HTTP/1.0 401 Unauthorized');
                echo json_encode(array(
                    'error'=>array(
                        'type'=>'Unauthorized',
                        'key'=>'key does not fit'
                    )
                ));
                return;
            }
        }

        //DeletFavoriteSity
        //удаление записи из таблицы избраных городов
        //api_weather/users
        if($method='DELET' && empty($urlData))
        {
            $action = new UserAction();

            $user_id = $formData['user_id'];
            $sity_id = $formData['sity_id'];
            $key = $formData['key'];
            if(password_verify($key,$_SESSION['key']))
            {
            if(is_numeric($user_id) && is_numeric($sity_id))
            {
                $user_id = intval($user_id);
                $sity_id = intval($sity_id);

                $result = $action->DeletFavoriteSity($user_id,$sity_id);

                echo $result;
                return;
            }
            else
{
            header('HTTP/1.1  406 Not Acceptable');
            echo json_encode(array(
                'ansver'=>array(
                    'type'=>'error',
                    'content'=>'arguments must be integer'
                )
            ));
            return;
        }
        }
        else
        {
            header('HTTP/1.0 401 Unauthorized');
            echo json_encode(array(
                'ansver'=>array(
                    'type'=>'error',
                    'content'=>'key does not fit'
                )
            ));
            return;
        }
        }

        header('HTTP/1.0 400 Bad Request');
        echo json_encode(array(
            'ansver'=>array(
                'type'=>'error',
                'content'=>'Bad Request'
            )
        ));
        
    }



?>