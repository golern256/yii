<?php
namespace app\models;
use yii\db\ActiveRecord;
use  yii\db\Query;
use yii\web\HttpException;

class Translater extends ActiveRecord{

    public function Translate($sourceArticle){
        if(count($sourceArticle->getSourceLine())<=1)
        {
            return $this->defineLeng($sourceArticle->getSourceLine());
        }
        else{

            return $this->translateArticle($sourceArticle->getSourceLine());
        }
    }

    public function translateArticle($sourceArticle)
    {
        foreach ($sourceArticle as $index => $word)
        {
            if($index == 0)
                $articleLeng = ctype_alpha($word) ? 1 : 0;

            if($articleLeng)
            { 
                $finalLine['LangInit']=Word::EN;
                $finalLine['LangFinal']=Word::RU;
                $query =$this->findTranslateWord(Word::EN,Word::RU,ucfirst($word));
                if(!empty($query))
                    $translateArticle[$index]=$query[0]["translationWord"];
                else
                     $translateArticle[$index]= ucfirst($word);
            }         
            else
            { 
                $finalLine['LangInit']=Word::RU;
                $finalLine['LangFinal']=Word::EN;
                $query =$this->findTranslateWord(Word::RU,Word::EN,rucfirst($word));
                if(!empty($query))
                    $translateArticle[$index]=$query[0]["translationWord"];
                else
                     $translateArticle[$index]= rucfirst($word);
            }
        }
        $finalLine["TextLine"]=implode(" ",$translateArticle);
        return $finalLine;
    }

    private function findTranslateWord($langInit,$langFinal, $sourceWord){
     
        $query=(new Query())->select(["word.$langFinal as translationWord",'type_word.name as typeOfSpeech'])->from('word')->join
            ('INNER JOIN', 'type_word', 'type_word.id_type = word.type')->where(["word.$langInit" => $sourceWord])->all();
        return $query;  
    }

    private function defineLeng($sourceWord)
    {
       $finalTranslatedLine= ctype_alpha($sourceWord[0]) ? $this->createResponse(Word::EN,Word::RU,ucfirst($sourceWord[0])):
       $this->createResponse(Word::RU,Word::EN,rucfirst($sourceWord[0]));
      return $finalTranslatedLine;
    }
 
    private function createResponse($langInit,$langFinal, $sourceWord){

        $finalTranslatedLine['LangInit']=$langInit;
        $finalTranslatedLine['LangFinal']=$langFinal;
        $translatedWord= $this->findTranslateWord($finalTranslatedLine['LangInit'],$finalTranslatedLine['LangFinal'],$sourceWord);
        if (empty($translatedWord))
        throw new HttpException(404);
        $finalTranslatedLine= $finalTranslatedLine + $translatedWord[0];
        return $finalTranslatedLine; 
    }

}
