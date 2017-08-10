<?php

class Pipedrive extends Http {
    private $base_uri = "https://api.pipedrive.com/v1/";

    public function __construct($token) 
    {
        parent::set_base_uri( $this->base_uri );
        parent::set_token( "api_token=$token" );      
    }

    public function find_user_by_email ( $email ) {
        return $this->find_by_email("users/find" , $email);   
    }

    public function find_person_by_email( $email ) {
        return $this->find_by_email("persons/find" , $email);
    }

    private function find_by_email ( $resource, $email ) {
        $obj = null;

        if( Utils::email_is_valid( $email ) ) {
            $result = parent::get( $resource, [ "term" => $email, "search_by_email" => 1] );
            if($result->success && count($result->data) > 0) {
                $obj = array_shift($result->data);
            }
        }

        return $obj;
    }
    
    public function create_person( $data ) {
        $person = null;
        
        $result = parent::post( "persons", [
                      "owner_id" => $data->owner_id,
                      "name" => $data->name,
                      "email" => $data->email,
                      "phone" => $data->phone,
                      "visible_to" => 3,
                  ]);

        if($result && $result->success && count($result->data) > 0) {
            $person = $result->data;
        }

        return $person;
    }

    public function create_deal($person, $data) {
        $deal = null;

        $result = parent::post( "deals", [
                "user_id" => $data->owner_id,
                "title" => $person->name,
                "person_id" => $person->id,
                "value" => $data->value, 
                "currency" => $data->currency,
                "visible_to" => 3 
            ]
        );

        if($result && $result->success && count($result->data) > 0) {
            $deal = $result->data;
        }

        return $deal;
    }

    public function create_note($person, $deal, $data) {
        $note = null;

        $note = parent::post( "notes", [
            "content" => "<p>$data->message</p>",
            "deal_id" => $deal->id,
            "person_id" => $person->id,
        ]);

        return $note;
    }

    public function send( $data ) {
        $person = $this->find_person_by_email($data->email);

        if( $person == null ) {
            $person = $this->create_person($data);
        }

        $deal = $this->create_deal($person, $data);
        $note = $this->create_note($person, $deal, $data);

        return json_encode ( 
            [ 
                "person" => $person,
                "deal" => $deal,
                "note" => $note, 
            ]
        );
    }
}
