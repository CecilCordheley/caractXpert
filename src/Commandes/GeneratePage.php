<?php
 namespace Commandes;

 use vendor\easyFrameWork\Core\Master\EasyFrameWork;
 use InvalidArgumentException;
 use RuntimeException;
 EasyFrameWork::INIT(__DIR__."/../../vendor/easyFrameWork/Core/config/config.json");
 class GeneratePage{
   // public static $LIB=["ctrl"=>EasyFrameWork::$Racines["dirAccess"]."/app/_ctrl","main"=>EasyFrameWork::$Racines["dirAccess"]];
    private $page;
    public function __construct($pageName){
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $pageName)) {
            throw new InvalidArgumentException("Invalid page name: only alphanumeric characters and underscores are allowed.");
        }
        $this->page=$pageName;
    }
    private function generateController(){
        $file=EasyFrameWork::$Racines["dirAccess"]."/app/_ctrl"."/{$this->page}Controller.php";
        $content=file_get_contents(EasyFrameWork::$Racines["dirAccess"]."/include/modelCtrl");
        $content=str_replace("[%PageName%]",$this->page,$content);
        if (file_put_contents($file, $content) === false) {
            throw new RuntimeException("Failed to write to {$file}");
        }
        echo "Page {$this->page} generated successfully at {$file}" . PHP_EOL;
    }
    private function generateRouter(){
        $file=EasyFrameWork::$Racines["dirAccess"]."/{$this->page}.php";
        $content=file_get_contents(EasyFrameWork::$Racines["dirAccess"]."/include/modelRoot");
        if (file_put_contents($file, $content) === false) {
            throw new RuntimeException("Failed to write to {$file}");
        }
        echo "Page {$this->page} generated successfully at {$file}" . PHP_EOL;
    }
    public function __invoke(){
       /* $PHPfile = self::$LIB["main"] . "/{$this->page}.php";
        $content = "<?php echo '{$this->page}';";*/
        //Genère le controller
        $this->generateController();
        $this->generateRouter();
    }
 }