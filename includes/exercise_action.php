<?php

    if ( !isset( $_SESSION ) ) {
        
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }
    
    /**
     * A class to manage available actions of an exercise.
     */
    class ExerciseAction {

        protected $id;
        protected $name;
        protected $icon;
        protected $cooldown;
        protected $limits;

        /**
         * Get the value of a protected class property.
         * @param string $property The name of the property
         * @return mix Returns the value of the property.
         */
        public function __get( $property ) {

            if ( property_exists( $this, $property ) ) {

                return $this->$property;

            }

        }

        /**
         * Set the value of a protected class property.
         * @param string $property The name of the property
         * @param mix $value The value of the property
         * @return void
         */
        public function __set( $property, $value ) {

            if ( property_exists( $this, $property ) ) {

                $this->$property = $value;

            }

        }

    } // end ExerciseAction class
    
    /**
     * A class that extends the ExerciseAction class to manage a special button.
     */
    class ExerciseRewindAction extends ExerciseAction {

        protected $graded;
        protected $length;
        protected $enabled;

    } // end ExerciseReminderAction class

?>