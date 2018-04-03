<?php
/**
 * Created by PhpStorm.
 * User: lijia
 * Date: 2018/04/04
 * Time: 01:29
 */

namespace GCU\Cloud\App;


use GCU\Cloud\Utils\Encrypt;

class PubAcc
{
    const MSG_TYPE_TEXT=2;
    const MSG_TYPE_LINK=5;
    const MSG_TYPE_ARTICLE=6;
    private static $app;
    private $no;
    private $pub;
    private $pubSecret;

    /**
     * 初始化输入appid和secret
     * @param $pub
     * @param $pubSecret
     * @return MCloud
     */
    public static function init($no,$pub,$pubSecret){
        if(self::$app==null){
            self::$app=new MCloud();
        }
        self::$app->no=$no;
        self::$app->pub=$pub;
        self::$app->pubSecret=$pubSecret;
        return self::$app;
    }
    /**
     * 单例调用
     * @return MCloud
     */
    public static function singleton(){
        if(self::$app==null){
            return null;
        }
        return self::$app;
    }
    /**
     * 建立消息
     * @param $form
     * @param $to
     * @param $type
     * @param $msg
     * @return string
     */
    public function generateMessage($form,$to,$type,$msg){
        $result=array();
        $result['form']=$form;
        $result['to']=$to;
        $result['type']=$type;
        $result['msg']=$msg;
        return json_encode($result);
    }
    /**
     * 发送
     * @param $data
     * @return string
     */
    public function sendData($data){
        $url='http://msg.gcu.edu.cn/pubacc/pubsend';
        $curl=curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result=curl_exec($curl);
        return $result;
    }
    /**
     * 建立form
     * @return array
     */
    public function generateForm(){
        $result=array();
        $result['no']=$this->no;
        $result['pub']=$this->pub;
        $result['time']=Encrypt::get_millistime();
        $result['nonce']=random_int(1,8);
        $result['pubtoken']=Encrypt::encryptString(Encrypt::linkArray(Encrypt::sortArray(array($result['no'],$result['pub'],$this->pubSecret,$result['nonce'],$result['time']))));
        return $result;
    }
    /**
     * 建立接收者
     * @param $data
     * @return array
     */
    public function generateTo($data){
        $result=array();
        foreach($data as $item){
            array_push($result,$item);
        }
        return $result;
    }
    /**
     * 建立接收list
     * @param $no
     * @param array $data
     * @param $code
     * @return array
     */
    public function generateToList($no,array $data,$code=false){
        $result=array();
        $result['no']=$no;
        if($code==true){
            $result['code']='all';
            return $result;
        }
        $user=array();
        foreach($data as $item){
            if(array_key_exists('openid',$item)){
                array_push($user,$item['openid']);
            }
            else if(array_key_exists('mobile',$item)){
                array_push($user,$item['mobile']);
                $result['code']='2';
            }
        }
        $result['user']=$user;
        return $result;
    }
    /**
     * 建立文字msg
     * @param $text
     * @return array
     */
    public function generateMsgText($text){
        $result=array();
        $result['text']=$text;
        return $result;
    }

    /**
     * 建立链接msg
     * @param $text
     * @param $url
     * @param $appid
     * @param $todo
     * @return array
     */
    public function generateMsgLink($text,$url,$appid=null,$todo=1){
        $result=array();
        $result['text']=$text;
        if(!is_null($appid)){
            $result['appid']=$appid;
        }
        else{
            $result['url']=urlencode($url);
        }
        $result['todo']=$todo;
        return $result;
    }
}