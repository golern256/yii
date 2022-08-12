<?php

namespace app\models;

class Word 
{
    private $content = [];
    const RU ='ru_word';
    const EN = 'en_word';

    function __construct($sourceArticle)
    {
       $inputLine =  preg_replace('/\s+/', ' ',$sourceArticle["sourceWord"]);
       $this->content=$this->prepareArticle(explode(" ", $inputLine));

    }

   public function getContent(){

        return $this->content;
    }

   private function prepareArticle($article)
    {   
        $code_match = array('-', '"', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '+', '{', '}', '|', ':', '"', '<', '>', '?', '[', ']', ';', "'", ',', '.', '/', '', '~', '`', '=');
        $article_res =[];
        foreach($article as $key => $word)
        {   
            $word = trim($word);
            $article_res[$key] = str_replace($code_match,'', $word);
        }

    return  $article_res;
    }

}
   
      function rucfirst($str, $e='utf-8'){                                                                                                                                                            
   $fc = mb_strtoupper(mb_substr($str, 0, 1, $e), $e);
   return $fc.mb_substr($str, 1, mb_strlen($str, $e), $e);
}
