<?php

    class Database{
        
        public static $connection;

        public static function setupConnection(){

            //Setting up the DB connection using OOP class. Therefore we can use this class to connect to the DB in any file in this project.
            if(!isset(Database::$connection)){

                Database::$connection = new mysqli('localhost', 'root', '1234', 'vits', '3306');

            }

        }

        //For use insert, update, delete DML operations.
        public static function q($q){

            Database::setupConnection();
            $result = Database::$connection->query($q);
            return $result; //the result is returning after the query execution (Success or Failed).

        }
        
        //For use select DQL operations.
        public static function getValue($q){

            Database::setupConnection();
            $result = Database::$connection->query($q);
            if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                return reset($row); // Dynamically fetch the first column value
            } else {
                return null; // No rows found
            }

        }

    }

?>