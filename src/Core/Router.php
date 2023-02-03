<?php
namespace Alibakhshiilani\WebProxy\Core;

class Router {

    private $routes = [];

    private $parameters = [];

    private $actions = [];

    private $namespace = 'Alibakhshiilani\WebProxy\Controllers\\';

    public function add($route,$action,$middleware=null){

        $action = explode("@",$action);
        $controller = $action[0];
        $method = $action[1];
        /*$info = [];
        list($info['controller'],$info['method']) = explode("@",$action);*/

        $route = preg_replace('/^\//','',$route);
        $route = preg_replace('/\//','\\/',$route);
        $route = preg_replace('/\{([a-z]+)\}/','(?<\1>[a-z0-9-]+)',$route);
        $route = "/".$route."/i";
        array_push($this->routes,['regex'=>$route,'controller'=>$controller,'method'=>$method,'middleware'=>$middleware]);

    }


    public function getRoutes(){
        return $this->routes;
    }


    private function match($uri){
        foreach($this->routes as $key=>$value){
            if(preg_match($value['regex'],$uri,$matches)){
                $this->actions = ['controller'=>$value['controller'],'method'=>$value['method'],'middleware'=>$value['middleware']];
                foreach($matches as $k=>$v){
                    if(is_string($k)){
                        $this->parameters[$k] = $v;
                    }
                }
                return true;
            }
        }
        return false;
    }


    public function dispatch($uri){
        if($this->match($uri)){

            $status=false;

            if(!is_null($this->actions['middleware'])){
                $middleware = 'App\Middlewares\\'.$this->actions['middleware'];
                $middleware_obj = new $middleware();
                $status = call_user_func_array([$middleware_obj,'handle'],[]);
            }

            if(is_null($this->actions['middleware']) || $status){
                $controller = $this->namespace.$this->actions['controller'];
                if(class_exists($controller)){
                    $controller_obj = new $controller();
                    if(is_callable([$controller_obj,$this->actions['method']])){
                        call_user_func_array([$controller_obj,$this->actions['method']],$this->parameters);
                    }else{
                        throw new \Exception("Method ".$this->actions['method']." Doesnt Exist In Class ".$controller);
                    }
                }else{
                    throw new \Exception("Class ".$controller." not Exists");
                }

            }else{
                call_user_func_array([$middleware_obj,'next'],[]);
            }
        }else{
            throw new \Exception("404 | not found");
        }
    }


}