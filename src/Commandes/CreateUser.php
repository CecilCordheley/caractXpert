<?php
namespace Commandes;
require __DIR__.'/../../vendor/easyFrameWork/Core/Master/EasyFrameWork.php';

use SQLEntities\AdminEntity;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\EnvParser;
class CreateUser{
    private $nom;
    private $prenom;
    private $mail;
    public function __construct($nom,$prenom,$mail){
        $this->nom=$nom;
        $this->prenom=$prenom;
        $this->mail=$mail;
    }
    public function handle(){
        return AdminEntity::createAdmin($this->nom,$this->prenom,$this->mail);
    }
}