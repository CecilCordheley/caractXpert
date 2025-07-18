<?php
namespace vendor\easyFrameWork\Core\Master;
class Router {
    private $routes = [];
    private $data=[];

    public function __construct($ctrlDirectory="./app/_ctrl/"){

        foreach(scandir($ctrlDirectory) as $file){
            
            if($file!="." && $file!=".."){
                require_once $ctrlDirectory.$file;
              //  echo $ctrlDirectory.$file;
            }
        }
    }
    public function addRoute($path, $callback) {
        $this->routes[$path] = EasyFrameWork::$Racines["controller"].$callback;
    }
    public function setData($key,$value){
        $this->data[$key]=$value;
    }
    public function route($requestUri,$data=[],$env=null) {
        $arr=explode("/",$requestUri);
        $uri=end($arr);
        
        foreach ($this->routes as $path => $controller) {
            $classe=get_declared_classes();
           // sort($classe);
         //  EasyFrameWork::Debug($controller);
        // echo "$uri = $path";
            if ($uri === $path) {
              //  EasyFrameWork::Debug($controller);
                // Vérifier si le contrôleur existe
                if (class_exists($controller)) {
                
                    // Créer une instance du contrôleur et appeler handleRequest()
                    $controllerInstance = new $controller($env);
                    if(!empty($data)){
                        foreach($data as $key=>$value)
                            $controllerInstance->setData($key,$value);
                    }
                    $controllerInstance->handleRequest();
                    return;
                }
            }
        }
        $this->notFound();
    }

    private function notFound() {
        echo "Page non trouvée";
    }
}
