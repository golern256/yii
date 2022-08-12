<?php
namespace app\controllers;
use app\models\Translater;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Request;
use app\models\MyCache;
use app\models\Word;
use Yii;


class WordController extends Controller
{
    public function actionIndex(){

        $request = new Request();
        $sourceArticle=$request->get();
        $requestURL=$request->absoluteUrl;
        $redis= new MyCache();
        $key =$redis->buildKey($requestURL);
        if($redis->exists($key))
            {
                $response = Yii::$app->response;
                $response->format = Response::FORMAT_JSON;
                $response->data = $redis->hget($key);
                return;
            }
        $sourceArticle = new Word($sourceArticle);
        $translater = new Translater();
        $finalContent = $translater->Translate($sourceArticle);
        $this->getResponce($finalContent,$key,$redis);
    }

    public function getResponce($finalContent,$key,$redis){

        $response = Yii::$app->response;
        print_r($finalContent);
        $response->format = Response::FORMAT_JSON;
        $response->data = $finalContent;
        $redis->hset($key,$finalContent);  
    }

    public function actionError()
    {
        $errorMessage = [];
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            $errorMessage["404"]="Word not Found";
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = $errorMessage;
            $response->content = "Word Not Found";
        }

    }
  
}


?>
