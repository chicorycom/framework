<?php
/**
 * Created by PhpStorm.
 * User: Assane
 * Date: 21/06/2016
 * Time: 23:02
 */

namespace App\Utility;


trait security
{

    protected static string $_Key = '$ABCDEFGHIJKLMNOPQRSTUVWXYZzcjg@@itlhopdieu1234567890fhryuqsddvcfgtrqde5%^---___@';

    /**
     * @param $Str
     * @param $EnctyptageKey
     * @return string
     */
   protected static function GenerationCle($Str,$EnctyptageKey): string
   {
        $EnctyptageKey = md5($EnctyptageKey);
        $Count=0;
        $Tmp = "";
        for ($Ctr=0;$Ctr<strlen($Str);$Ctr++)
        {
            if ($Count==strlen($EnctyptageKey))
                $Count=0;
            $Tmp.= substr($Str,$Ctr,1) ^ substr($EnctyptageKey,$Count,1);
            $Count++;
        }
        return $Tmp;
    }

// Crypter la chaine

    /**
     * @param $str
     * @return string
     */
   public static function encrypt(string $str): string
   {
        srand((double)microtime()*1000000);
        $enctyptageKey = md5(rand(0,32000) );
        $Count=0;
        $Tmp = "";
        for ($Ctr=0;$Ctr<strlen($str);$Ctr++)
        {
            if ($Count==strlen($enctyptageKey))
                $Count=0;
            $Tmp.= substr($enctyptageKey,$Count,1).(substr($str,$Ctr,1) ^ substr($enctyptageKey,$Count,1) );
            $Count++;
        }
        return base64_encode(self::GenerationCle($Tmp,self::$_Key) );
    }

// Decrypter la chaine

    /**
     * @param $Str
     * @return string
     */
   public static function decrypt($Str): string
   {
        $Str = self::GenerationCle(base64_decode($Str), self::$_Key);
        $Tmp = "";
        for ($Ctr=0;$Ctr<strlen($Str);$Ctr++)
        {
            $md5 = substr($Str,$Ctr,1);
            $Ctr++;
            $Tmp.= (substr($Str,$Ctr,1) ^ $md5);
        }
        return $Tmp;
    }
}