<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27 0027
 * Time: 15:16
 */

namespace App\Repositories;
use Predis\Client;
use QL\QueryList;

class RecordTending
{
    private $client = null;
    private $language = "";
    private $day = "";

    public function __construct($language,$day)
    {
        $this->client = new Client([]);
        $this->language = $language;
        $this->day = $day;
    }

    public function getUrl(){
        $url = "https://github.com/trending/";
        $url = $url . $this->language . "?since=" . $this->day;
        return $url;
    }

    public function getData($url){
        $html = file_get_contents($url);
        $ql = QueryList::html($html);

        $data = $ql->find('li[class=col-12 d-block width-full py-4 border-bottom]')->map(function($item){
            $project = substr($item->find('.d-inline-block.col-9.mb-1 a')->href,1);
            $arr = explode('/',$project);
            return [
                "project" => $project,
                "author"  => $arr[0],
                "title"   => $arr[1],
                "description" => trim($item->find(".py-1")->text()),
                "type"        => trim($item->find('span[itemprop=programmingLanguage]')->text()),
                "AllStar"     => trim($item->find("a.muted-link.d-inline-block.mr-3")->eq(0)->text()),
                "forks"       => trim($item->find("a.muted-link.d-inline-block.mr-3")->eq(1)->text()),
                "getStar"     => trim($item->find("span.d-inline-block.float-sm-right")->text()),
                "avator"      => trim($item->find(".avatar.mb-1")->src)
            ];
        });
        return $data->all();
    }

    public function doRecord(){
        date_default_timezone_set("PRC");
        $url = $this->getUrl();
        $data = $this->getData($url);
        $day = $this->day;
        $lan = $this->language;
        $key = $lan ."_".$day;
        $res = $this->client->hset($key,$key,$data);
        $this->client->hset($key,"update_at",date('Y-m-d H:i:s'));
        return $res;
    }
}
