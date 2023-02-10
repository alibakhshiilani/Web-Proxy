<?php
namespace Alibakhshiilani\WebProxy\Controllers;

use Alibakhshiilani\WebProxy\Classes\Proxy;

class BrowseController {

    public function page(){

        $req = $_REQUEST;

        if(isset($req["wp_url"])){

            $proxy = new Proxy();
            echo $proxy->go($req["wp_url"]);
            return;
        }

        include "src/views/browse.php";

    }

    public function browse(){
        $req = $_REQUEST;

        if(isset($req["url"])){
             $proxy = new Proxy();
             echo $proxy->go($req["url"]);
        }
    }

    public function assets(){
        $req = $_GET;

        if(isset($req["wp_url"])){
            $url = $req["wp_url"];
            $proxy = new Proxy();
//            echo $proxy->go(preg_match("/^(http|https):///",$url) ? $url : $proxy->getBaseUrl().$url);
            echo $proxy->go($url);
        }
    }
}