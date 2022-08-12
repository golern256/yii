<?php

namespace app\models;

use yii\redis\Cache;

class MyCache extends Cache{
    public function setValue($key, $value, $expire)
    {
        if ($expire == 0) {
            return (bool) $this->redis->executeCommand('SET', [$key, $value]);
        }

        $expire = (int) ($expire * 1000);

        return (bool) $this->redis->executeCommand('SET', [$key, $value, 'PX', $expire]);
    }

     function getValue($key)
    {
        $value = $this->getReplica()->executeCommand('GET', [$key]);
        if ($value === null) {
            return false; // Key is not in the cache or expired
        }

        return $value;
    }


public function hset($hkey,$data)
{

    foreach ($data as $key => $value) {
        $this->redis->executeCommand('hmset', ["$hkey","$key","$value"]);
    }
}


public function hget($hkey)
{
    $keys=$this->redis->executeCommand('hkeys',["$hkey"]);
    $json_arr=[];
    foreach ($keys as $key)
    {
        $json_arr[$key]=$this->redis->executeCommand('hget',["$hkey","$key"]);
    }
    return $json_arr;
}

}
