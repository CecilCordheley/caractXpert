<?php
namespace vendor\easyFrameWork\Core\Master;

use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
abstract class SqlToElement
{
    /**
     * @var SQLFactory
*/
    private $SQLF;
    /**
     * @param $sqlfactory SQLFactory
     */
   
    public function __construct($sqlfactory)
    {
        $this->SQLF = $sqlfactory;

    }

    public function getFactory():SQLFactory{return $this->SQLF;}
    /**
     * @param $param array
     */
    public function generate($param): string
    {
        return "";
    }
}
