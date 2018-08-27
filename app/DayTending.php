<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27 0027
 * Time: 10:49
 */


namespace App;
use App\Repositories\RecordTending;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DayTending
{
    private $record = null;
    private $day = "";

    public function __construct($day)
    {
        $this->day = $day;
    }

    public function run(){
        $lans = ["","PHP","Vue","JS","C++","C#","JAVA","Python"];
        $day = $this->day;
        foreach ($lans as $val){
            $this->record = new RecordTending($val,$day);
            try{
                $res = $this->record->doRecord();
                if($res){
                    $data = date("Y-m-d H:i:s") . $val . "获取数据成功（".$day."）";
                    $log = new Logger("LOG");
                    $path = '../logs/'. date('Y-m-d-') . "success.log";
                    $log->pushHandler(new StreamHandler($path, Logger::WARNING));
                    $log->error($data);
                }else{
                    $data = date("Y-m-d H:i:s") . $val . "获取数据失败（".$day."）";
                    $log = new Logger("LOG");
                    $path = '../logs/'. date('Y-m-d-') . "error.log";
                    $log->pushHandler(new StreamHandler($path, Logger::WARNING));
                    $log->error($data);
                }
            }catch (\Exception $exception){
                $data = $exception->getMessage();
                $log = new Logger("LOG");
                $path = '../logs/'. date('Y-m-d-') . "error.log";
                $log->pushHandler(new StreamHandler($path, Logger::WARNING));
                $log->error($data);
            }
        }
    }
}
