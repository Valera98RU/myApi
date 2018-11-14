<?php
require "DBConnect.php";

class SityAction
{
   /* public function LoadSityInServer()
    {
        $file = "city.list.json";
            $json = file_get_contents($file);
            $jsonA = json_decode($json);
            
            
          foreach($jsonA as $sity)
            {
               $s = R::dispense('sity');
               
               $s->name = $sity->name;
               $s->country = $sity->country;
               $s->lon = $sity->coord->lon;
               $s->lat = $sity->coord->lat;
               R::store($s);
               
            }
            
           
    }*/
    //вывести список городов 50
    public function GETSityListTop($numberPage)
    {
        if($numberPage <=1)
        {
        $min = 0;
        $max = 50;
        }
        else
        {
        $min = 50*($numberPage-1);
        $max =50*$numberPage ;
        }
        $listSity = R::getAll('SELECT * FROM sity LIMIT ?,?',array($min,$max));

        return $listSity;
    }
    //поиск городов по названию
    public function GSearchSitys($text)
    {
        $listSity = R::getAll('SELECT * FROM sity WHERE name LIKE ? LIMIT 0,5',array('%'.$text.'%'));

        return $listSity;
    }
}
?>