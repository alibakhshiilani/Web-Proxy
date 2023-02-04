<?php
namespace Alibakhshiilani\WebProxy\Core;

class Error
{


    public static function errorHandler($level,$message,$file,$line){

        throw new \ErrorException($message,0,$level,$file,$line);

    }


    public static function exceptionHandler($exception){


//        $filename = "../storage/logs/log-".date("Y-m-d").".log";

//        if(!file_exists($filename)){
//            $myfile=fopen($filename,'w') or die("Unable to open file!");
//            fclose($myfile);
//        }
//
//        ini_set('error_log',$filename);

        http_response_code(500);

        $message =  " <h1 style='padding:20px;background-color:darkred;color:#fff;'> Error </h1> ";

        $message .= " <h2> {$exception->getMessage()} </h2> ";

        $message .= " <p> On File {$exception->getFile()}  At Line {$exception->getLine()} </p> ";

        $message .= '<hr>';

        $message .= " <p>Stack Trace : </p> <pre>{$exception->getTraceAsString()}</pre> ";

        error_log(strip_tags($message));

        echo $message;

    }




}