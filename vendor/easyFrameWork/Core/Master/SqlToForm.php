<?php
namespace Vendor\EasyFrameWork\Core\Master;
use Vendor\EasyFrameWork\Core\Master\SqlToElement;
class SqlToForm extends SqlToElement{
    /**
     * Summary of generate
     * @param array $param ["URI"=>"","METHOD"=>"","table"=>"","ignoreFields"=>[],"label"=>true,"ASSOC_FIELDS"=>[]]
     * @return string
     */
    public function generate($param): string
    {
        $url = $param["URI"];
        $method = $param["METHOD"];
        $result = key_exists("table", $param) ? parent::getFactory()->getColumns($param["table"]) : [];
      //  EasyFrameWork::Debug($result);
        $return = array_reduce($result, function ($carry, $item) use ($param) {
            if (!in_array($item["NAME"], $param["ignoreFields"])) {
                $type = match ($item["TYPE"]) {
                    "varchar" => "text",
                    "enum" => "enum",
                    "date" => "date",
                    "time" => "time",
                    default => "text"
                };
                $label = isset($param["label"]) && $param["label"]==true ? "<label for=\"" . strtolower($item["NAME"]) . "\">" . str_replace("_"," ",ucfirst($item["NAME"])) . "</label>" : "";
    
                if (isset($item["TABLE_ASSOC"])) {
                    $assocField = $param["ASSOC_FIELDS"][$item["NAME"]];
                    $fields = parent::getFactory()->execQuery("SELECT " . $item["NAME"] . ", $assocField FROM " . $item["TABLE_ASSOC"]);
                    $options = array_reduce($fields ?? [], function ($carry, $_item) use ($item, $assocField) {
                        return $carry .= "\n\t<option value=\"" . htmlspecialchars($_item[$item["NAME"]]) . "\">" . htmlspecialchars($_item[$assocField]) . "</option>";
                    }, "");
                    $carry .= "<div class=\"mb-3\">\n
                        $label 
                        <select class=\"form-control\"  id=\"" . strtolower($item["NAME"]) . "\" name=\"" . $item["NAME"] . "\">
                            <option value=\"\">Sélectionner une valeur</option>
                            $options
                        </select></div>";
                }elseif($type=="enum"){
                    $enumString=$item["COL_TYPE"];
                    $options="";
                    if (preg_match_all("/'([^']+)'/", $enumString, $matches)) {
                        $values= $matches[1];
                       foreach($values as $value){
                           $options.="<option value='$value'>$value</option>\n";
                       }
                    } else {
                        echo "Aucune correspondance trouvée.";
                    }
                     $carry .= "<div class=\"mb-3\">
                        $label
                        <select class=\"form-control\" id=\"" . strtolower($item["NAME"]) . "\" name=\"" . $item["NAME"] . "\">
                            <option value=\"\">Sélectionner une valeur</option>
                            $options
                        </select></div>";
                } else {
                    $carry .= "<div class=\"mb-3\">
                        $label
                        <input class=\"form-control\" id=\"" . strtolower($item["NAME"]) . "\" type=\"$type\" name=\"" . $item["NAME"] . "\">
                    </div>";
                }
            }
            return $carry;
        }, "");
    
        return "<form action=\"$url\" method=\"$method\">$return<button type=\"submit\">Envoyer</button></form>";
    }
    
    }