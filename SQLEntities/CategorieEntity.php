<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Categorie;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `Categorie`.
* Hérite de `Categorie`. Ajoutez ici vos propres méthodes.
*/
class CategorieEntity extends Categorie
{
   // Ajoutez vos méthodes ici

   public static function getAll($sqlF){
    $arr=Categorie::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(Categorie::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\CategorieEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getCategorieBy($sqlF,$key,$value,$filter=null){
      $arr=Categorie::getCategorieBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\CategorieEntity");
        return $c;
      },[]);
    }else return $arr;
    }else{
      return false;
    }
      }
 }