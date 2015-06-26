<?php

    // if started session data is not true
    if ( !isset( $_SESSION ) ) {
        
        // redirect to 404 page
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }

    require_once 'includes/exercise_action.php';

    class ExerciseReminderAction extends ExerciseAction {

        private $graded;
        private $length;
        private $enabled;

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