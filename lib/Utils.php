<?php
class Utils {
    public static function email_is_valid( $email )
    {
         $is_valid = false;

         if( $email )
         {
             $pattern = "/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/";
+            $is_valid = preg_match( $pattern, $email );
         }
         
         return $is_valid;
    } 
}