<?php
 namespace apis\module\asyncModule;

use SQLEntities\TicketEntity;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Main;

use DateTime;
use Exception;
use StreamBucket;

abstract class StatFnc{
    public static function getTicketByType(){
         $sqlF=new SQLFactory(null,"../include/config.ini");
        return TicketEntity::getNbTicket($sqlF,"type");
    }
    public static function getTicketByServiceWithSat(){
        $sqlF=new SQLFactory(null,"../include/config.ini");
        return TicketEntity::getBnTicketWithStat($sqlF,"service");
    }
    public static function getAgentByService(){
         $sqlF=new SQLFactory(null,"../include/config.ini");
    }
    public static function getTicketByService(){
        $sqlF=new SQLFactory(null,"../include/config.ini");
        return TicketEntity::getNbTicket($sqlF,"service");
    }
    public static function getTicketByState(){
        $sqlF=new SQLFactory(null,"../include/config.ini");
        $return =TicketEntity::getNbTicket($sqlF,"state");
       // var_dump($return);
        return $return;
    }
    public static function getTicketByAgent(){
        $sqlF=new SQLFactory(null,"../include/config.ini");
        return TicketEntity::getNbTicket($sqlF,"agent");
    }
}