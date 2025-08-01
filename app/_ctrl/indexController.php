<?php
namespace vendor\easyFrameWork\Core\Master\Controller;

use SQLEntities\AgentEntity;
use SQLEntities\PannesEntity;
use vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\HistoryLog;
use vendor\easyFrameWork\Core\Master\ResourceManager;
use vendor\easyFrameWork\Core\Master\EasyTemplate;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use SQLEntities\JournalLicenceEntity;
use SQLEntities\LicenceExceptionEntity;
use SQLEntities\PanneEventEntity;
use SQLEntities\PanneHasEventEntity;
use SQLEntities\Service;
use SQLEntities\TicketEntity;
use SQLEntities\UsersEntity;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Master\TokenManager;
use vendor\easyFrameWork\Core\Utils\Logger;


class indexController extends Controller{
 
    private bool $isConnect;
    private UsersEntity $user;
    public function __construct(){
        parent::__construct();
       /* Logger::init("./include/.ghost.log",true);
        Logger::write("Ceci est un message");
        $e=Logger::getLog();
       var_dump($e->getEntries("2aE53a9"));*/
        $sessionManager=EasyGlobal::createSessionManager();
        //EasyFrameWork::Debug($_SESSION);
       // EasyFrameWork::Debug(AgentTbl::getAll(new SQLFactory()));
       $sqlF=new SQLFactory();
  
        $this->isConnect=(($sessionManager->get("isConnect",SessionManager::PUBLIC_CONTEXT))!=null)?1:0;
            $this->setData("_isConnect",strval($this->isConnect));
        if($this->isConnect=="1"){
            $u=Main::fixObject($sessionManager->get("user",SessionManager::PUBLIC_CONTEXT),"SQLEntities\UsersEntity");
            $this->user=$u;
            $userData=$u->getArray();
            $userData["client"]=$u->getClient($sqlF)->getArray();
        //  EasyFrameWork::Debug($userData);
      // $userData=count())??0;
            $this->setData("user",$userData);
            $this->setData("client",$u->getClient($sqlF)->getArray());
            }
       // EasyFrameWork::Debug($this->service);
       
        }
    public function setMainActity(EasyTemplate &$template){
        $template->remplaceTemplate("MainContent","index.tpl");
        //JS

        //Menu
        $menu=[];
        switch(strtolower($this->user->roleUser)){
            case "agent":{
                $menu[]=["label"=>"identifier une panne","href"=>"#","action"=>"callMainActivity"];
                $menu[]=["label"=>"mon historique","href"=>"#","action"=>"getUserHistory"];
                break;
            }
            case "dev":{
                $menu[]=["label"=>"console","href"=>"#","action"=>"getConsole"];
                $menu[]=["label"=>"trigger","href"=>"#","action"=>"panneEvent"];
                $menu[]=["label"=>"voir les logs","href"=>"#","action"=>"getLogs"];
                break;
            }
            case "admin":{
                $menu[]=["label"=>"exporter","href"=>"export","action"=>""];
                $menu[]=["label"=>"voir les pannes","href"=>"#","action"=>"getPannesData"];
                $menu[]=["label"=>"voir les utilisateurs","href"=>"#","action"=>"getUsers"];
                break;
            }
            case "manager":{
                $menu[]=["label"=>"voir les pannes","href"=>"#","action"=>"getPannesData"];
                $menu[]=["label"=>"voir les utilisateurs","href"=>"#","action"=>"getUsers"];
                break;
            }
        }
        TokenManager::setFileStorage("./include/tokens/tokens.json","./include/tokens/delegate.json");
        $delegate=TokenManager::getDelegate($this->user->uuidUser);
       // $this->user->getPanneHistory(new SQLFactory());
      // EasyFrameWork::Debug($delegate);
        if($delegate!=false){
        switch(strtolower($delegate)){
            case "manager":{
                $menu[]=["label"=>"voir les pannes","href"=>"#","action"=>"getPannesData"];
                break;
            }
        }
    }
        $menu[]=["label"=>"deconnexion","href"=>"./deconnexion","action"=>""];
        $template->setLoop("Menu",$menu);
    }
    public function handleRequest(){
         $config=parse_ini_file("include/config.ini",true)["localhost"];
        $template = new EasyTemplate($config,new ResourceManager());
        $template->getRessourceManager()->addScript("public/js/async.js");
        
        if(isset($this->user) && strtolower($this->user->roleUser)=="dev"){
            $template->getRessourceManager()->addScript("public/js/SysConsole.js");
        }
        $template->addScript("https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js");
        $sessionManager=EasyGlobal::createSessionManager();
        $panneEvent=PannesEntity::getPannesBy(new SQLFactory(),"id",4);
       // $panneEvent->dissociateEvent(new SQLFactory(),1);
        if(isset($_GET["root"])){
            switch($_GET["root"]){
                case "export":{
                    if(empty($_GET["object"]))
                        Main::redirectWithAlert($template,"Object param missing in URL !","MainActivity");
                     $log=new HistoryLog("./include/connexion.log");
                     $log->export("exportConnexion_".date("Y-m-d"),function($data){
                        return array_reduce($data,function($car,$el){
                            $car[]=[$el["date"],explode("-",$el["message"])[0],explode("-",$el["message"])[1]];
                            return $car;
                        },[]);
                     });
                     Main::redirectWithAlert($template,"Le fichier a été exporté","MainActivity");
                    break;
                }
                case "deconnexion":{
                    $sessionManager->clean();
                    $log=new HistoryLog("./include/connexion.log");
                    $log->addEntry($this->user->uuidUser."- deconnexion");
                    $log->commit();
                    header("Location:index.php");
                    break;
                }
                case "firstConnexion":{
                     $template->remplaceTemplate("MainContent","firstConnexion.tpl");
                    break;
                }
            }
        }
       
        
        if($this->isConnect){
            $this->setMainActity($template);
        }
        else
            $template->remplaceTemplate("MainContent","connexion.tpl");
        $template->setVariables($this->getData());
        // Rendre le template
        $sqlfactory=new SQLFactory();
       
        $template->render([], $sqlfactory->getPdo());
    }
}