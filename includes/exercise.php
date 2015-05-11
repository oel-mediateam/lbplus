<?php

    if ( !defined( "ABSPATH" ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        exit('Error 404 - Page Not Found');

    }

    require_once 'includes/exercise_action.php';

    class Exercise {

        private $path;
        private $id;

        // constructor
        public function __construct( $filePath ) {

            $this->path = $filePath;

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

        // read the exercise json file
        public function readExercise() {

            if ( file_exists( $this->path ) ) {

                $src = file_get_contents( $this->path );
                $data = json_decode( $src, true );

                $exerciseId = $data['exercise']['id'];
                $exerciseActions = $data['exercise']['actions'];
                $availableActions = array();

                foreach( $exerciseActions as $action ) {

                    $listedActions = new ExerciseAction();
                    $listedActions->name = $action['name'];
                    $listedActions->icon = $action['icon'];
                    $listedActions->limits = $action['limits'];
                    $listedActions->cooldown = $action['cooldown'];
                    array_push($availableActions, $listedActions);

                }

                return $availableActions;

            } else {

                exit('Exercise file ' . $this->path . ' is not found.');

            }

        }

    }


?>