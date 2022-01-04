<?php


use Phinx\Seed\AbstractSeed;

class RecryptPassword extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
         \App\Models\Admin::all();
    }

    protected string $_Key = '$ABCDEFGHIJKLMNOPQRSTUVWXYZzcjg@@itlhopdieu1234567890fhryuqsddvcfgtrqde5%^---___@';

    /**
     * @param $Str
     * @param $EnctyptageKey
     * @return string
     */
    protected function GenerationCle($Str,$EnctyptageKey): string
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
    public  function encrypt(string $str): string
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
        return base64_encode($this->GenerationCle($Tmp,$this->_Key) );
    }

// Decrypter la chaine

    /**
     * @param $Str
     * @return string
     */
    public function decrypt($Str): string
    {
        $Str = $this->GenerationCle(base64_decode($Str), $this->_Key);
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
