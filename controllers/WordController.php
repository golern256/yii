<?php
namespace app\controllers;
use app\models\Translater;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Request;
use app\models\MyCache;
use app\models\Word;
use yii\web\HttpException;
use Yii;

class WordController extends Controller
{
    public function actionIndex(){

        $request = new Request();
        $sourceArticle=$request->get();
        if((key($sourceArticle)!="sourceLine"))
            throw new HttpException(400);
        $requestURL=$request->absoluteUrl;
        $redis= new MyCache();
        $keyForRequest =$redis->buildKey($requestURL);
        if($redis->exists($keyForRequest))
            {
                $response = Yii::$app->response;
                $response->format = Response::FORMAT_JSON;
                $response->data = $redis->hget($keyForRequest);
                return;
            }
        $sourceArticle = new Word($sourceArticle);
        $translater = new Translater();
        $finalTranslatedLine = $translater->Translate($sourceArticle);
        $this->getResponce($finalTranslatedLine,$keyForRequest,$redis);
    }

    public function getResponce($finalTranslatedLine,$keyForRequest,$redis){

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data = $finalTranslatedLine;
        $redis->hset($keyForRequest,$finalTranslatedLine);  
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
