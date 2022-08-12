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
            return false; 
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
    $jsonArr=[];
    foreach ($keys as $key)
    {
        $jsonArr[$key]=$this->redis->executeCommand('hget',["$hkey","$key"]);
    }
    return $jsonArr;
}

}
