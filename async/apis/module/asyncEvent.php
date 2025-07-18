<?php
 namespace apis\module\asyncModule;

use SQLEntities\PanneEventEntity;
use SQLEntities\PannesEntity;
use vendor\easyFrameWork\Core\Master\SQLFactory;


 class AsyncEvent{
    private static function getSQLFactory(){
        return new SQLFactory(null,"../include/config.ini");
    } 
    public static function getPannes($event){
        $sqlF=self::getSQLFactory();
        $event=PanneEventEntity::getPanneEventBy($sqlF,"idEvent",$event);
        if($event==false){
             echo json_encode(["result"=>"error","message"=>"no event found"]);
            exit();
        }
        $pannes=$event->getPannes($sqlF);
        if($pannes!=0){
            return $pannes;
        }else{
            echo json_encode(["result"=>"error","message"=>"no pannes found for this event"]);
            exit();
        }
    }
    public static function associatePanne($idpanne,$idevent){
        $sqlF=self::getSQLFactory();
        $panne=PannesEntity::getPannesBy($sqlF,"id",$idpanne);
        if($panne==false){
             echo json_encode(["result"=>"error","message"=>"no panne found"]);
            exit();
        }
        return $panne->associateEvent($sqlF,$idevent);
    }
     public static function dissociatePanne($idpanne,$idevent){
        $sqlF=self::getSQLFactory();
        $panne=PannesEntity::getPannesBy($sqlF,"id",$idpanne);
        if($panne==false){
             echo json_encode(["result"=>"error","message"=>"no panne found"]);
            exit();
        }
        return $panne->dissociateEvent($sqlF,$idevent);
    }
    public static function getAllEvent(){
        $sqlf=self::getSQLFactory();
        $event=PanneEventEntity::getAll($sqlf);
        if($event==false){
             echo json_encode(["result"=>"error","message"=>"no events found"]);
            exit();
        }else{
            $arr = is_array($event) ? $event : [$event];
            return array_reduce($arr,function($car,$el){
                $car[]=$el->getArray();
                return $car;
            },[]);
        }
    }
 }