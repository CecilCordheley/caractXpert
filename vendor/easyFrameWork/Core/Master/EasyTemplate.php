<?php

namespace vendor\easyFrameWork\Core\Master;

use RuntimeException;
use vendor\easyFrameWork\Core\Master\ResourceManager;

class EasyTemplate
{
    private $config;
    private $content;
    private $variables = [];
    private $loops = [];
    private $resourceManager;

    public function __construct(array $config, ResourceManager $resourceManager)
    {
        $this->config = $config;
        $this->resourceManager = $resourceManager;
        $this->loadContent();
    }
   

    public function getRessourceManager():ResourceManager{
        return $this->resourceManager;
    }
    private function loadContent()
    {
        $this->content = file_get_contents($this->config['templateDirectory'] . '/' . $this->config['masterPage']);
    }
    public function changeMaster($newMaster){
        $this->content = file_get_contents($this->config['templateDirectory'] . '/' . $newMaster);
    }
    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }
    public function setRawVariable($key, $value)
{
    $this->content = str_replace("{var:$key}", $value, $this->content);
}
    public function remplaceTemplate($key, $tpl)
    {
        $c = file_get_contents($this->config['templateDirectory'] . "/$tpl");
        $this->content = str_replace("{var:$key}", $c, $this->content);
    }
    public function replaceAppVar(){
        foreach(EasyFrameWork::getVar() as $key=>$v){
             $this->content=str_replace("{app:$key}",$v,$this->content);
        }
    }
    private function hasSQLLoop(){
        
    }
    public function replaceSqlLoops(\PDO $pdo)
{
    $pattern = '/\{LOOP:SQL\("(.+?)"\)\}(.*?)\{\/LOOP\}/is';

    // On traite une seule couche à la fois
    $this->content = preg_replace_callback($pattern, function ($matches) use ($pdo) {
        $query = $matches[1];
        $template = $matches[2];

        try {
            $stmt = $pdo->query($query);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $result = '';
            foreach ($rows as $row) {
                // Copie du sous-template
                $loopContent = $template;

                // Remplacement des variables locales
                foreach ($row as $key => $value) {
                    if($value!=null)
                    $loopContent = str_replace("{#$key#}", htmlspecialchars($value), $loopContent);
                }

                // Récursivité : traiter les boucles imbriquées DANS CE CONTEXTE
               $subTemplate = clone $this; // copier tout l'objet
                $subTemplate->content = $loopContent;
                $subTemplate->replaceSqlLoops($pdo); // appel récursif
                $loopContent = $subTemplate->content;

                $result .= $loopContent;
            }

            return $result;
        } catch (\PDOException $e) {
            return "<!-- SQL ERROR: " . htmlspecialchars($e->getMessage()) . " -->";
        }
    }, $this->content);
}

    public function render(array $customReplacements = [],?\PDO $pdo=null)
    {
        $this->renderStylesheets();
        $this->renderScripts();
        $this->renderMeta();
        $this->replaceAppVar();
        foreach ($this->loops as $key => $loop) {
            $this->replaceLoop($key, $loop);
        }
      
        $this->replaceVariables();
         if ($pdo) {
   
        $this->replaceSqlLoops($pdo);
      
    }
        $this->replaceRootURL();
        $this->replaceImageURL();
        $this->replaceSessionVariables();
        $this->replaceGetVariable();
        $this->replaceCondition();
        // Exécute les méthodes de substitution personnalisées
        // var_dump($customReplacements);
        foreach ($customReplacements as $customReplacement) {
            //var_dump(gettype($customReplacement));
            if (is_callable([$customReplacement])) {

                call_user_func_array($customReplacement, [&$this]);
            }
        }
        $this->clear();
        echo $this->content;
    }
    public function clearView($viewName){
        $this->content=str_replace("{view:$viewName}","",$this->content);
        
    }
    private function clear(){
        $this->content=preg_replace("/\{var:(.*?)\}/i","",$this->content);
        $this->content=preg_replace("/\{\:GET name=(.*?)\}/i","",$this->content);
        $this->content=preg_replace("/\{view\:(.*?)\}/i","",$this->content);
    }
    public function renderMeta(){
        $this->resourceManager->renderMeta($this->content);
    }
    public function addScript($scriptPath)
    {
        $this->resourceManager->addScript($scriptPath);
    }

    public function addStylesheet($stylesheetPath)
    {
        $this->resourceManager->addStylesheet($stylesheetPath);
    }

    public function renderScripts()
    {
        $this->resourceManager->renderScripts($this->content);
    }

    public function renderStylesheets()
    {
        $this->resourceManager->renderStylesheets($this->content);
    }
    public function cancelLoop(string $key,$alt=""){
        $pattern = "\\{LOOP:$key\\}(.*?)\\{\\/LOOP\\}";
        if (preg_match_all("/$pattern/is", $this->content, $matches)) {
            $this->content = str_replace($matches[0][0], $alt, $this->content);
        }
    }
    public function setLoop(string $key, array $a)
    {
        $this->loops[$key] = $a;
    }
    private function replaceLoop(string $key, array $array, bool $UTF8Encode = false)
    {
        $pattern = "\\{LOOP:$key\\}(.*?)\\{\\/LOOP\\}";
        if (preg_match_all("/$pattern/is", $this->content, $matches)) {
            $loopTemplate = $matches[1][0];
            $content = array_reduce($array, function ($html, $lines) use ($loopTemplate, $UTF8Encode) {
                $loopContent = $loopTemplate;
    
                foreach ($lines as $key => $value) {
                    if (is_array($value)) {
                        // Gestion des sous-boucles
                        $subPattern = "\\{LOOP:$key\\}(.*?)\\{\\/LOOP\\}";
                        if (preg_match_all("/$subPattern/is", $loopContent, $subMatches)) {
                            $subTemplate = $subMatches[1][0];
                            $subContent = array_reduce($value, function ($subHtml, $subLines) use ($subTemplate, $UTF8Encode) {
                                $subLoopContent = $subTemplate;
                                foreach ($subLines as $subKey => $subValue) {
                                    if ($UTF8Encode) {
                                        $subLoopContent = str_replace("{#$subKey#}", mb_convert_encoding($subValue, "UTF-8"), $subLoopContent);
                                    } else {
                                        $subLoopContent = str_replace("{#$subKey#}", $subValue, $subLoopContent);
                                    }
                                }
                                return $subHtml . $subLoopContent;
                            }, "");
                            $loopContent = str_replace($subMatches[0][0], $subContent, $loopContent);
                        }
                    } else {
                        // Remplacement simple des variables
                        if ($UTF8Encode) {
                            $loopContent = str_replace("{#$key#}", mb_convert_encoding($value, "UTF-8"), $loopContent);
                        } else {
                            if(gettype($value)=="string")
                                $loopContent = str_replace("{#$key#}", $value, $loopContent);
                            else
                            $loopContent = str_replace("{#$key#}", strval($value), $loopContent);
                        }
                    }
                }
                return $html . $loopContent;
            }, "");
            $this->content = str_replace($matches[0][0], $content, $this->content);
        }
    }
    
    private function replaceGetVariable(){
        foreach($_GET as $key=>$value){
            $this->content = str_replace("{:GET name=$key}", htmlspecialchars($value), $this->content);
        }
    }
    private function replaceVariables()
    {
        foreach ($this->variables as $key => $value) {
            $keys=explode("|",$key);
            if(count($keys)==2){
                
                $k=$keys[0];
                $format=$keys[1];
            }else{
                $k=$key;
            }
            
            $arr=gettype($value);
            if($arr=="string"){
                if(isset($format)){
            //        EasyFrameWork::debug($format);
                    switch($format){
                        case "rawHTML":
                            $replace=$value;
                            break;
                        default:
                            $replace=htmlspecialchars($value);
                            break;
                    }
                //    EasyFrameWork::debug($replace);
                }else
                    $replace=htmlspecialchars($value);
                $this->content = str_replace("{var:$key}", $replace, $this->content);
            }elseif($arr=="int"){
                
                $replace=strval($value);
            }
            else{
                
                foreach($value as $sKey=>$sValue){
                    if(gettype($sValue)=="string" || gettype($sValue)=="integer")
                        $this->content = str_replace("{var:$k.$sKey}", htmlspecialchars($sValue), $this->content);
                }
            }
        }
    }

    private function replaceRootURL()
    {
        if($_SERVER["HTTP_HOST"]!="192.168.1.125")
        $this->content = str_replace("{:racine}", $this->config['racineProject'] . '/', $this->content);
    else
    $this->content = str_replace("{:racine}", $this->config['hostProject'] . '/', $this->content);
       // EasyFrameWork::Debug($_SERVER["HTTP_HOST"]);
    }
    private function replaceCondition()
    {
        $this->content = $this->processConditions($this->content);
    }
    
    private function processConditions($content)
{
    $pattern = "/\{\:IF\s+(.*?)\}(.*?)(\{\:ELSE\:\}(.*?))?\{\:\/IF\}/s";

    while (preg_match($pattern, $content, $matches)) {
        $conditionString = trim($matches[1]);
        $ifContent = $matches[2];
        $elseContent = $matches[4] ?? '';

        // Appel récursif pour traiter les blocs imbriqués
        $ifContent = $this->processConditions($ifContent);
        $elseContent = $this->processConditions($elseContent);

        // Évaluer la condition combinée
        $condition = $this->evaluateConditions($conditionString);

        // Remplacement du contenu basé sur la condition
        $replace = $condition ? $ifContent : $elseContent;
        $content = str_replace($matches[0], $replace, $content);
    }

    return $content;
}

private function evaluateConditions($conditionString)
{
    // Séparer les conditions par des opérateurs logiques (& et |)
    $conditionParts = preg_split("/(&|\|)/", $conditionString, -1, PREG_SPLIT_DELIM_CAPTURE);
    $result = null;
    $currentOperator = null;

    foreach ($conditionParts as $part) {
        $part = trim($part);

        if ($part === "&" || $part === "|") {
            $currentOperator = $part;
        } else {
            // Analyser et évaluer chaque condition individuelle
            $singleCondition = $this->evaluateSingleCondition($part);

            if ($result === null) {
                $result = $singleCondition;
            } else {
                if ($currentOperator === "&") {
                    $result = $result && $singleCondition;
                } elseif ($currentOperator === "|") {
                    $result = $result || $singleCondition;
                }
            }
        }
    }

    return $result;
}

private function evaluateSingleCondition($condition)
{
    $pattern = "/^(.*?)\s*(=|!|>|<|>=|<=)\s*(.*?)$/";
    if (preg_match($pattern, $condition, $matches)) {
        $var1 = $this->evaluateVariable($this->resolveDynamicVariable(trim($matches[1])));
        $var2 = $this->evaluateVariable($this->resolveDynamicVariable(trim($matches[3])));

        switch ($matches[2]) {
            case "=":
                return $var1 == $var2;
            case "!":
                return $var1 != $var2;
            case ">":
                return $var1 > $var2;
            case "<":
                return $var1 < $var2;
            case ">=":
                return $var1 >= $var2;
            case "<=":
                return $var1 <= $var2;
        }
    }

    return false; // Si la condition ne correspond pas au format attendu
}

private function resolveDynamicVariable($var)
{
    // Vérifie et résout les variables de type {var:...}
    if (preg_match("/\{var:(.*?)\}/", $var, $match)) {
        $variableName = $match[1];
        // Retourne la valeur de la variable (remplacez ceci par votre logique)
        return $this->getVariableValue($variableName);
    }
    return $var; // Retourne la valeur brute si ce n'est pas une variable dynamique
}

private function getVariableValue($key)
{
    // Exemple de logique pour obtenir une valeur dynamique (à adapter à votre contexte)
    $variables = [
        'user.TYPE_UTILISATEUR' => 4,
        'activUser' => 1,
        'activSujet' => 0,
    ];
    return $variables[$key] ?? null;
}

    
    private function evaluateVariable($var)
    {
        // Vérifie si le format de la variable est {var:...}
        if(isset($var)){
        if (preg_match('/\{var:(.*?)\}/', $var, $varMatch)) {
            $varName = $varMatch[1];
            // Recherche dans les variables définies
            if (isset($this->variables[$varName])) {
                return $this->variables[$varName];
            }
        }
        return $var;
    }
    }
    
    
    private function replaceImageURL()
    {
        $this->content = str_replace("{:image}", $this->config['imageDirectory'], $this->content);
    }
    public function _view($key, $sqlView, $p)
    {
        $pattern = "{view:$key}";
        $replace = $sqlView->generate($p);
        $this->content = str_replace($pattern, $replace, $this->content);
        return $this;
    }
    private function replaceSessionVariables()
    {
        if (isset($_SESSION)) {
            foreach ($_SESSION as $context => $values) {
                foreach ($values as $name => $value) {
                    if(gettype($value)=="string"){
                    $this->content = str_replace("{:SESSION context=\"$context\" name=\"$name\"}", htmlspecialchars($value), $this->content);
                    $this->content = str_replace("{:SESSION name=\"$name\" context=\"$context\"}", htmlspecialchars($value), $this->content);
                }
            }
            }
        }
    }
}
