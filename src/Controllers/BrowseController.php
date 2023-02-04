<?php
namespace Alibakhshiilani\WebProxy\Controllers;

use Alibakhshiilani\WebProxy\Classes\Proxy;

class BrowseController {

    public function page(){

        include "src/views/browse.php";

    }

    public function browse(){
        $req = $_REQUEST;

        if(isset($req["url"])){
             $proxy = new Proxy();
             echo $proxy->go($req["url"]);
        }
    }
}