<?php
namespace vendor\easyFrameWork\Core\Master;
class AjaxPHPTranspiler {
    private string $baseDir;
    private ?string $file;
    private string $fullPath;
    private bool $multiRequest;
    private string $action;
    public function __construct(string $baseDir, ?string $file = null,$multi=false) {
        $this->baseDir = rtrim($baseDir, '/');
        $this->action="";
        $this->file = $file ?? $_GET['file'] ?? null;
        $this->multiRequest=$multi;
        if (!$this->file) {
            $this->failHttp(400, "Le paramètre 'file' est requis.");
        }

        $this->fullPath = $this->baseDir . '/apis/' . $this->file . '.aphp';
        $this->validateFilePath();
    }
    public function setAction($action){
        $this->action=$action;
    }
    public function run($file=null): void {
        $sourceCode = file_get_contents($this->fullPath);
        $phpCode = "<?php\n// file: {$this->file}\n" . $this->injectHelpers() . "\n" . $this->transpile($sourceCode);
        if($file){
            file_put_contents($file,$phpCode);
        }else{
        $tempFile = tempnam(sys_get_temp_dir(), 'transpile_') . '.php';
        file_put_contents($tempFile, $phpCode);
        require $tempFile;
        unlink($tempFile);
        }
        exit;
    }

    private function validateFilePath(): void {
        if (!file_exists($this->fullPath) || strpos(realpath($this->fullPath), realpath($this->baseDir)) !== 0) {
            $this->failHttp(404, "Fichier introuvable ou chemin non autorisé.");
        }
    }

    private function transpile(string $code): string {
        $code = $this->handleImports($code);
        $code = $this->transformSubRoutines($code);
        $code = ($this->multiRequest)?$this->replaceAllNamedRequests($code) : $this->replaceMainRoutine($code);
        $code = preg_replace('/routine\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*{/', 'function $1() {', $code);
        $code = preg_replace('/success\((.*?)\)\s*;/', 'echo json_encode(["status" => "success", "data" => $1]); exit;', $code);
        $code = preg_replace('/fail\((.*?)\)\s*;/', 'echo json_encode(["status" => "error", "message" => $1]); exit;', $code);

        return $code;
    }

    private function handleImports(string $code): string {
        return preg_replace_callback('/\bimport\s+([a-zA-Z0-9_,\s]+);/', function ($matches) {
            $modules = array_map('trim', explode(',', $matches[1]));
            $output = '';
            foreach ($modules as $module) {
                $output .= "use apis\\module\\AsyncModule\\$module;\n";
                $output .= "require_once \"./apis/module/$module.php\";\n";
            }
            return $output;
        }, $code);
    }

    private function transformSubRoutines(string $code): string {
        $re = '/routine\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*(\([^)]*\))\s*\{((?:[^{}]*|(?R))*)\}/ixm';
        $subst = 'function ${1}${2} { ${3}} ';
        return preg_replace($re, $subst, $code, 1);
    }
 private function replaceAllNamedRequests(string $code): string {
    // Remplace tous les "request name { ... }" par des fonctions __request_name()
    //Get the action 
    if(isset($this->action)){
       $code.= 'global $_MAIN;
        $_MAIN = [
    \'action\' => \''.$this->action.'\'
];';
    }
    $code = preg_replace_callback(
        '/request\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*\{((?:[^{}]*|(?R))*)\}/mi',
        function ($matches) {
            $name = $matches[1];
            $body = $matches[2];
            return "function __request_{$name}() {\n" . $body . "\n}";
        },
        $code
    );

    // Ajoute un routeur à la fin pour exécuter la bonne request
    $code .= <<<'PHP'

if (!isset($_MAIN['action'])) {
    echo json_encode(["status" => "error", "message" => "Paramètre 'action' manquant."]);
    exit;
}
$action = $_MAIN['action'];
$handler = "__request_$action";
if (!function_exists($handler)) {
    echo json_encode(["status" => "error", "message" => "Action '$action' non trouvée."]);
    exit;
}
header("Content-Type: application/json");
$handler();
PHP;

    return $code;
}

    private function replaceMainRoutine(string $code): string {
        $re = '/request\s*\{((?:[^{}]*|(?R))*)\}/mi';
        $subst = "header(\"Content-Type: application/json\");\n$1";
        return preg_replace($re, $subst, $code);
    }

    private function injectHelpers(): string {
        return <<<'PHP'
        
function required_get(string $key): mixed {
    if (!isset($_GET[$key])) {
        echo json_encode(["status" => "error", "message" => "Le paramètre GET '$key' est requis."]);
        exit;
    }
    return $_GET[$key];
}

function required_post(string $key,$null=true,$empty=true): mixed {
$json_data = json_decode(file_get_contents('php://input'), true);
    if (!isset($json_data[$key])) {
        echo json_encode(["status" => "error", "message" => "Le paramètre POST '$key' est requis."]);
        exit;
    }
        if(!$null){
        if (strtoupper($json_data[$key])=="NULL") {
                    echo json_encode(["status" => "error", "message" => "Le paramètre POST '$key' est requis."]);
                     exit;
                }
            }
        
        if(!$empty){
        if (empty($json_data[$key])) {
                    echo json_encode(["status" => "error", "message" => "Le paramètre POST '$key' est requis."]);
                     exit;
                }
            }
        
    return $json_data[$key];
}

function fail(string $message): never {
    echo json_encode(["status" => "error", "message" => $message]);
    exit;
}
PHP;
    }

    private function failHttp(int $code, string $message): never {
        http_response_code($code);
        echo json_encode(["status" => "error", "message" => $message]);
        exit;
    }
}
