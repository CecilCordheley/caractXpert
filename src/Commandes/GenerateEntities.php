<?php

namespace Commandes;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\EnvParser;
use vendor\easyFrameWork\Core\Master\SqlEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
EasyFrameWork::INIT(__DIR__."/../../vendor/easyFrameWork/Core/config/config.json");
require __DIR__ . '/../../vendor/easyFrameWork/Core/Master/SQLFactory.php';
require __DIR__ . '/../../vendor/easyFrameWork/Core/Master/SqlEntities.php';
class GenerateEntities
{
    public function handle($table)
    {
        $sqlF = new SQLFactory(null,__DIR__."/../../include/config.ini");
        SqlEntities::$DIRECTORY=__DIR__."/../../SQLEntities";
        SqlEntities::generateEntity($sqlF, $table);
        SqlEntities::TblClassToEntity($sqlF,EasyFrameWork::toCamelCase($table));
    }
}
