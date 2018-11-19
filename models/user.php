<?php
    require "DBConnect.php";
     class UserAction 
    {     
       
        //Информация о пользователе
        public function GETUser($id)
        {
                
                if(isset($id))
                {
                    $user = R::load('TUsers',$id);

                    
                return $user;
              
                }

        }
        //регистрация нового пользователя
        public function SingUp ($Login, $Password, $Name, $Email)
        {
            if(isset($Login) && isset($Password))
            {   
                $user= R::findOne('tusers','login = ? or email = ?',array($Login,$Email));
                if(empty($user))
                {
                
                $user= R::dispense('tusers');
                $user->login = $Login;
                $user->password = password_hash($Password,PASSWORD_DEFAULT);
                $user->name = $Name;
                $user->email = $Email;
                R::store($user);
                header('HTTP/1.0 200 OK');
                return json_encode(array(
                    'answer'=>array(
                        'type'=>'OK',
                        'contain'=>'OK'
                    )
                ));
                }
                else
                {
                    header('HTTP/1.1 409 Conflict');
                    return json_encode(array(
                        'answer'=>array(
                            'type'=>'error',
                            'contant'=>'User with such login or email is already registered'
                        )
                    ));
                }
            }
            else
            {
            header('HTTP/1.1 406 Not Acceptable');
            echo json_encode(array(
                'answer'=>array(
                    'type'=>'error',
                    'contant'=>'login and password fields must not empty'
                )
            ));
        }
        }
        //авторизация  пользователя
        public function SingIn($Login,$Password)
        {
            if(isset($Login) && isset($Password))
            {
                $user = R::findOne('tusers','login = ?',array($Login));   
                if(isset($user))
                {
                    if(password_verify($Password,$user->password))
                    {
                        $_SESSION['key']=password_hash($user->id,PASSWORD_DEFAULT);
                        header('HTTP/1.0 200 OK');
                        return json_encode(array(
                            'answer'=>array(
                                'type'=>'user',
                                'content'=>array(
                            'id'=>$user->id,
                            'key'=>password_hash($user->id,PASSWORD_DEFAULT)
                                )
                            )
                        ));
                        
                    }
                    else
                    header('HTTP/1.0 404 Not Found');
                    return json_encode(array(
                        'answer'=>array(
                            'type'=>'error',
                            'content'=>'password does not match'
                        )
                    ));
                }
                else
                header('HTTP/1.0 404 Not Found');
                return json_encode(array(
                    'answer'=>array(
                        'type'=>'error',
                        'content'=>'user not found'
                    )
                ));

                return;

            }            
        }
        //добавление города в список избранного
        public function AddSityInFavorite($idUser,$idSity)
        {
            if(is_numeric($idUser))
            {
                $idUser = intval($idUser); 
            }
            else{
                header('HTTP/1.1 406 Not Acceptable');
                return json_encode(array(
                    'answer'=>array(
                        'type'=>'error',
                        'content'=>'user_id must be intager'
                )
            ));
            }
            
            if(is_numeric($idSity))
            {
                
            $idSity = intval($idSity);
            
            }
            else
            {
                header('HTTP/1.1 406 Not Acceptable');
            return json_encode(array(
                'answer'=>array(
                    'type'=>'error',
                    'content'=>'sity_id must be intager'
                )
            ));
            }
            
            //
            if(isset($idUser))
            {
                $user = R::load('tusers',$idUser);
            }
            //
            if(empty($user->login))
            {
                header('HTTP/1.0 404 Not Found');
                return json_encode(array(
                'answer'=>array(
                    'type'=>'error',
                    'content'=>'user not found'
                )
            ));
            }
            //
            if(isset($idSity))
            {
                $sity = R::load('sity',$idSity);                
            }
            //
            if(empty($sity->name))
            {
                header('HTTP/1.0 404 Not Found');
                return json_encode(array(
                    'answer'=>array(
                        'type'=>'error',
                        'content'=>'Sity not found'
                    )
                ));
         
            }
            //
            if(isset($user) && isset($sity))
            {
                $user->sharedSityList[] = $sity;
                R::store($user);
                header('HTTP/1.0 200 OK');
                return json_encode(array(
                    'answer'=>array(
                        'type'=>'OK',
                        'content'=>'OK'
                    )
                ));
            }
            
           
            
        }
        //Получение списка избраных городов
        public function GetFavoritSity($user_id)
        {
            $user = R::findOne('tusers','id = ?',array($user_id));
            if(empty($user))
            {
                header('HTTP/1.0 404 Not Found');
                return json_encode(array(
                    'answer'=>array(
                        'type'=>'error',
                        'content'=>'User not found'
                    )
                ));
            }
            else
            {
                $list = $user->sharedSityList;
                header('HTTP/1.0 200 OK');
                return json_encode($list);
            }
        }

        //изменение информации о пользователе 
        public function UpdateUser($user_id,$name,$email)
        {
            $user = R::load('tusers',$user_id);
            if(empty($user->login))
            {
                header('HTTP/1.0 404 Not Found');
                return json_encode(array(
                    'answer'=>array(
                        'type'=>'error',
                        'key'=>'user not found'
                    )
                ));
            }
            else
            {
                $user->name = $name;
                $user->email = $email;
                R::store($user);
                header('HTTP/1.0 200 OK');
                return json_encode(array(
                    'answer'=>array(
                        'type'=>'OK',
                        'key'=>'user updating'
                    )
                ));
            }
        }

        //удаление города из списка избраных
        public function DeletFavoriteSity($user_id,$sity_id)
        {
            $Fsity = R::findOne('sity_tusers','sity_id = ? AND tusers_id = ?',array($sity_id,$user_id));
           

            if(!empty($Fsity->id))
            {
                R::trash($Fsity);
                header('HTTP/1.0 200 OK');
                return  json_encode(array(
                    'answer'=>array(
                        'type'=>'OK',
                        'key'=>'deleted'
                    )
                ));
            }
            else
            header('HTTP/1.0 404 Not Found');
            return json_encode(array(
                'answer'=>array(
                    'type'=>'error',
                    'key'=>'not found'
                )
            ));;
        }
    }
?>