<?php

use app\models\Redis;


echo"Перевод слова: ";
print_r(Redis::getVal($word));
