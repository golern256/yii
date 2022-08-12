<?php
namespace app\models;
use yii\db\ActiveRecord;
use  yii\db\Query;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class Translater extends ActiveRecord{

    public function Translate($sourceArticle){
        if(count($sourceArticle->getContent())<=1)
        {
            return $this->defineLeng($sourceArticle->getContent());
        }
        else{

            return $this->translateArticle($sourceArticle->getContent());
        }
    }

    public function translateArticle($sourceArticle)
    {
        $translateArticle=[];
        $finalLine=[];
        foreach ($sourceArticle as $index => $word)
        {
            if($index == 0)
                $articleLeng = ctype_alpha($word) ? 1 : 0;

            if($articleLeng)
            { 
                $finalLine['LangFinal']=Word::RU;
                $finalLine['LangInit']=Word::EN;
                $query =$this->QueryToDB(Word::EN,Word::RU,ucfirst($word));
                if(!empty($query))
                    $translateArticle[$index]=$query[0]["translationWord"];
                else
                     $translateArticle[$index]= ucfirst($word);
            }         
            else
            { 
                $finalLine['LangFinal']=Word::EN;
                $finalLine['LangInit']=Word::RU;
                $query =$this->QueryToDB(Word::RU,Word::EN,rucfirst($word));
                if(!empty($query))
                    $translateArticle[$index]=$query[0]["translationWord"];
                else
                     $translateArticle[$index]= rucfirst($word);
            }
        }
        $finalLine["TextLine"]=implode(" ",$translateArticle);
        return $finalLine;
    }

    private function QueryToDB($langInit,$langFinal, $sourceWord){
     
        $query=(new Query())->select(["word.$langFinal as translationWord",'type_word.name as typeOfSpeech'])->from('word')->join
            ('INNER JOIN', 'type_word', 'type_word.id_type = word.type')->where(["word.$langInit" => $sourceWord])->all();
        return $query;  
    }

    public function defineLeng($sourceWord)
    {
       $arr= ctype_alpha($sourceWord[0]) ? $this->findTranslate(Word::EN,Word::RU,ucfirst($sourceWord[0])):
       $this->findTranslate(Word::RU,Word::EN,rucfirst($sourceWord[0]));
      return $arr;
    }
 
    public function findTranslate($langInit,$langFinal, $sourceWord){

        $finalLine['LangInit']=$langInit;
        $finalLine['LangFinal']=$langFinal;
        $query = $this->QueryToDB($finalLine['LangInit'],$finalLine['LangFinal'],$sourceWord);
        if (empty($query))
        throw new HttpException(404);
        $finalLine= $finalLine + $query[0];
        return $finalLine; 
    }

}
