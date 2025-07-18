<?php
namespace vendor\easyFrameWork\Core\Master;
class ResourceManager {
    private $scripts = [];
    private $stylesheets = [];
    private $implements = [];
    private $microdata = [];

    private $meta=[];
    public function addScript(string $scriptPath) {
        $this->scripts[] = $scriptPath;
    }

    public function addMeta(array $meta){
        $this->meta[]=$meta;
    }
    public function addDirectJs(string $implementation) {
        $this->implements[] = $implementation;
    }

    public function addMicroData(array $microdata) {
        $this->microdata[] = $microdata;
    }

    public function addStylesheet(string $stylesheetPath) {
        $this->stylesheets[] = $stylesheetPath;
    }

    public function renderMeta(string &$content){
        foreach ($this->meta as $script) {
            $key=key($script);
            $content = str_replace("<title>", "<meta $key=\"".$script["$key"]."\" content=\"".$script["content"]."\">\n<title>", $content);
        }
    }
    public function renderScripts(string &$content) {
        foreach ($this->scripts as $script) {
            $content = str_replace("</head>", "<script src=\"$script\"></script>\n</head>", $content);
        }
        foreach ($this->implements as $directCode) {
            $content = str_replace("</head>", "<script>$directCode</script>\n</head>", $content);
        }
    }

    public function renderStylesheets(string &$content) {
        foreach ($this->stylesheets as $style) {
            $content = str_replace("</title>", "</title>\n<link rel=\"stylesheet\" href=\"$style\">", $content);
        }
    }

    // Méthode pour rendre les microdonnées Google dans le contenu
    public function renderMicroData(string &$content) {
        foreach ($this->microdata as $data) {
           
            $json = json_encode($data, JSON_UNESCAPED_SLASHES);
            $content = str_replace("</body>", "<script type=\"application/ld+json\">$json</script>\n</body>", $content);
        }
    }
}
