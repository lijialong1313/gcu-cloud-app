<?php

namespace GCU\Cloud\App;

class MCloud
{
    private static $app;
	private $app_id;
	private $secret;

    /**
     * 初始化输入appid和secret
     * @param $app_id
     * @param $secret
     * @return MCloud
     */
	public static function init($app_id,$secret){
	    if(self::$app==null){
	        self::$app=new MCloud();
        }
        self::$app->app_id=$app_id;
        self::$app->secret=$secret;
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
     * 解析出Ticket
     * @param array $param
     * @return mixed
     */
    public function getTicket(array $param){
	    $ticket=$param['ticket'] or '';
	    return $ticket;
    }

    /**
     * 解析出XtUrl
     * @param array $param
     * @return mixed
     */
    public function getXtUrl(array $param){
        $xtUrl=$param['xtUrl'] or '';
        return $xtUrl;
    }

    private $accessToken;
	private $expiresIn;

    /**
     * 解析出AccessToken
     * @param $force
     * @return mixed
     */
    public function getAccessToken($force=false){
        if($force||empty($this->accessToken)||empty($this->expiresIn)||time()-$this->expiresIn>3600*24*7){
            $address='http://msg.gcu.edu.cn/openauth2/api/token';
            $param='?grant_type='.'client_credential'.'&appid='.$this->app_id.'&secret='.$this->secret;
            $curl=curl_init();
            curl_setopt($curl,CURLOPT_URL,$address.$param);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result=curl_exec($curl);
            $json=json_decode($result,true);
            if(array_key_exists('access_token',$json)){
                $this->accessToken=$json['access_token'];
                $this->expiresIn=time();
            }
            else{
                return $json;
            }
        }
        return $this->accessToken;
    }

    /**
     * 解析出用户个人信息
     * @param array $param
     * @param $force
     * @return mixed
     */
    public function getContext(array $param,$force=false){
        $ticket=$this->getTicket($param);
        $token=$this->getAccessToken($force);
        if(is_array($token)){
            return $token;
        }
        $address='http://msg.gcu.edu.cn/openauth2/api/getcontext';
        $param='?ticket='.$ticket.'&access_token='.$token;
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,$address.$param);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result=curl_exec($curl);
        $json=json_decode($result,true);
        return $json;
    }
}