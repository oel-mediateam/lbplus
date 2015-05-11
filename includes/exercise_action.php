<?php

    if ( !defined( "ABSPATH" ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        exit('Error 404 - Page Not Found');

    }

    class ExerciseAction {

        private $name;
        private $icon;
        private $cooldown;
        private $limits;

        // constructor
        public function __construct() {

        }

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