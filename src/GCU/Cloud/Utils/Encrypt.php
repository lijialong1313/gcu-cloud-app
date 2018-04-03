<?php
/**
 * Created by PhpStorm.
 * User: lijia
 * Date: 2018/04/04
 * Time: 01:16
 */
namespace GCU\Cloud\Utils;

class Encrypt
{
    /**
     * 字符串按照字典排序
     * @param array $data
     * @return array
     */
    public static function sortArray(array $data){
        sort($data,SORT_STRING);
        return $data;
    }
    /**
     * 字符串连接
     * @param array $data
     * @return string
     */
    public static function linkArray(array $data){
        return join('',$data);
    }
    /**
     * 字符串加密
     * @param $data
     * @return string
     */
    public static function encryptString($data){
        return sha1($data);
    }
    /**
     * 获取和java中System.currentTimeMillis()一样的结果
     * @return string
     */
    public static function get_millistime()
    {
        $microtime = microtime();
        $comps = explode(' ', $microtime);
        return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }
}