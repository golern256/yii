<?php

namespace app\models;

class Word 
{
    private $content;
    const RU ='ru_word';
    const EN = 'en_word';

    function __construct($sourceArticle)
    {
       $clearSourceLine =  preg_replace('/\s+/', ' ',$sourceArticle["sourceLine"]);
       $this->content=$this->prepareArticle(explode(" ", $clearSourceLine));

    }

   public function getSourceLine(){

        return $this->content;
    }

   private function prepareArticle($article)
    {   
        $code_match = array('-', '"', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '+', '{', '}', '|', ':', '"', '<', '>', '?', '[', ']', ';', "'", ',', '.', '/', '', '~', '`', '=');
        $clearArticle =[];
        foreach($article as $key => $word)
        {   
            $word = trim($word);
            $clearArticle[$key] = str_replace($code_match,'', $word);
        }

    return  $clearArticle;
    }

}


   function rucfirst($str, $e='utf-8'){                                                                                                                                                            
   $fc = mb_strtoupper(mb_substr($str, 0, 1, $e), $e);
   return $fc.mb_substr($str, 1, mb_strlen($str, $e), $e);
}
