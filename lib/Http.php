<?php

class Http 
{
    private $base_uri = "";
    private $token = "";
    private $curl_opts = [];

    protected function set_token($token = "") {
        if( isset( $token ) && !empty( $token ))
        {
            $this->token = $token;
        }  
    }

    protected function set_base_uri($base_uri) {
        if( isset( $base_uri ) && !empty( $base_uri ))
        {
            $this->base_uri = $base_uri;
        }  
    }
    
    protected function post($resource, $data)
    {
        $url = $this->base_uri . "$resource?" . $this->token;
       
        $this->curl_opts = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data
        ];
        
        return $this->execute();
    }

    protected function get($resource, $filters = array()) 
    {
        $query = http_build_query($filters);
        
        $url = $this->base_uri . "$resource?" . "$query&$this->token";
        
        $this->curl_opts = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => 1,
                
        ];

        return $this->execute();
    }

    private function execute()
    {   
        $curl = curl_init();
        curl_setopt_array($curl, $this->curl_opts);
        $result = curl_exec($curl);  
        curl_close($curl);

        return json_decode($result);
    }
}