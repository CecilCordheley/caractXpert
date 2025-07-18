<?php
namespace Vendor\EasyFrameWork\Core\Master;
use Vendor\EasyFrameWork\Core\Master\SqlToElement;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
class SQLtoView extends SqlToElement{
    /**
     * @var string
     */
    private $view;
    public function __construct($sqlfactory,$view=""){
        parent::__construct($sqlfactory);
        if($view!="")
            $this->view=file_get_contents($view);
    }
    private function setView($url){
        if(file_exists($url)){  
            $this->view=file_get_contents($url);
        }else
            throw new Exception("$url doesn't exist");
        return $this;
    }
    public function generate($param):string{
        $i=0;
        $factory=parent::getFactory();
        $result=$factory->execQuery($param["query"]);
        if(key_exists("view",$param))
            $this->setView($param["view"]);
        if($this->view==null){
            throw new Exception("No view parameter");
        }else
        $return = array_reduce($result, function ($carry, $item) use($param,&$i) {
            if(key_exists("callback",$param)){
                $carry["str"].=call_user_func_array($param["callback"],array(&$item,$carry["previous"],$this->view));
            }else{
                $carry["str"].=$this->view;
            }
            foreach($item as $key=>$value){
                if(gettype($value)=="string")
                $carry["str"]=str_replace("#$key#",$value,$carry["str"]);
            }
            $carry["previous"]=$item;
           // echo $i.$carry["str"];
            $i++;
            
            return $carry;
        },[
            "str"=>"",
            "previous"=>null
        ]);
        if(key_exists("container",$param)){
            $return["str"]=str_replace("[...]",$return["str"],$param["container"]);
        }
        return $return["str"];
    }
}