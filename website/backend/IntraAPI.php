<?php


class IntraAPI
{
   private $login;
   private const BODY = "client_id=c13f0b06ea884a709788739b9fbe297a27316c1fdfe2b87eef4cb8d59dfc418c&client_secret=3bc0c6e91c5686981b21be3ede94cd5b5d4598105f8f50e39e41fd7f3b32f581&grant_type=client_credentials";

   private const URL_AUTORIZE = "https://api.intra.42.fr/oauth/token";

   private const URL_EMAIL = "https://api.intra.42.fr/v2/users/";

   private $token;

   private $email;

   private $id;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }




    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

   //de308731e90aea0b46f7915aacc24254e6836a301537826ba99cb2b8449c46d0
    /**
     * IntraAPI constructor.
     * @param $login
     */
    public function __construct($login)
    {
        $this->login = $login;
    }

    public  function makeRequestToIntra()
    {
        if ($this->autorize() == 0)
            return (0);
        return $this->findEmail();
    }

    private function autorize()
    {
        $request = curl_init();
        curl_setopt_array($request, array(
            CURLOPT_URL => self::URL_AUTORIZE,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => self::BODY
        ));
        $out = curl_exec($request);
        $var = curl_getinfo($request, CURLINFO_HTTP_CODE);
        //echo $var;
        curl_close($request);
        if ($var != 200)
            return (0);
        $json = json_decode($out);
        $this->token =  $json->access_token;
        //echo $this->token. "\n";
        return (1);
    }

    private function findEmail()
    {
        $request = curl_init();
        $adress = self::URL_EMAIL. $this->login;
        $autorization = 'Authorization: Bearer '. $this->token;
        $headers[] = $autorization;
        curl_setopt_array($request, array(
            CURLOPT_URL => $adress,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers
        ));
        $out = curl_exec($request);
        $var = curl_getinfo($request, CURLINFO_HTTP_CODE);
        //echo $var;
        curl_close($request);
        //нет такого пользователя
        if ($var == 404)
            return (-1);
        //ошибка на сервере
        else if ($var != 200)
            return (0);
        $json = json_decode($out);
        $this->email =  $json->email;
        $this->id = $json->id;
        return 1;
    }
}