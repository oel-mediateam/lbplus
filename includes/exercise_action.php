<?php

    if ( !defined( "ABSPATH" ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();

    }

    class ExerciseAction {

        protected $name;
        protected $icon;
        protected $cooldown;
        protected $limits;

        // getter
        public function __get( $property ) {

            if ( property_exists( $this, $property ) ) {

                return $this->$property;

            }

        }

        // setter
        public function __set( $property, $value ) {

            if ( property_exists( $this, $property ) ) {

                $this->$property = $value;

            }

            return $this;

        }

    }

?>