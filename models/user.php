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
                $user= R::dispense('tusers');
                $user->login = $Login;
                $user->password = password_hash($Password,PASSWORD_DEFAULT);
                $user->name = $Name;
                $user->email = $Email;
                R::store($user);
                return true;
            }
            else
            echo json_encode(array(
                'error'=>'Bad parametrs'
            ));
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
                        return json_encode(array(
                            'ansver'=>array(
                                'type'=>'user',
                                'content'=>array(
                            'id'=>$user->id,
                            'key'=>password_hash($user->id,PASSWORD_DEFAULT)
                                )
                            )
                        ));
                        
                    }
                    else
                    return json_encode(array(
                        'answer'=>array(
                            'type'=>'error',
                            'content'=>'password does not match'
                        )
                    ));
                }
                else
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
            //
            if(isset($idUser))
            {
                $user = R::load('tusers',$idUser);
            }
            //
            if(empty($user->login))
            {
                return json_encode(array('message'=> '404 User Not Found'));
            }
            //
            if(isset($idSity))
            {
                $sity = R::load('sity',$idSity);                
            }
            //
            if(empty($sity->name))
            {
            return json_encode(array(
                'message'=>'404 Sity Not Found'
            ));
            }
            //
            if(isset($user) && isset($sity))
            {
                $user->sharedSityList[] = $sity;
                R::store($user);
                return json_encode(array(
                    'message'=>'OK'
                ));
            }
            
           
            
        }
        //Получение списка избраных городов
        public function GetFavoritSity($user_id)
        {
            $user = R::findOne('tusers','id = ?',array($user_id));
            if(empty($user))
            {
                return json_encode(array(
                    'error'=> ' 404 user not found'
                ));
            }
            else
            {
                $list = $user->sharedSityList;
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
                    'ansver'=>array(
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
                return json_encode(array(
                    'ansver'=>array(
                        'type'=>'ok',
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
                return return json_encode(array(
                    'ansver'=>array(
                        'type'=>'ok',
                        'key'=>'deleted'
                    )
                ));
            }
            else
            header('HTTP/1.0 404 Not Found')
            return return json_encode(array(
                'ansver'=>array(
                    'type'=>'error',
                    'key'=>'not found'
                )
            ));;
        }
    }
?>