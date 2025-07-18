<?php
 namespace apis\module\asyncModule;
 class SayHello{
    public function handle($i=0){
        return $i*3;
    }
 }