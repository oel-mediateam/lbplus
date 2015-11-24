<?php

    if ( !isset( $_SESSION ) ) {
        
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }

    require_once 'includes/exercise_action.php';
    require_once 'includes/json_handler.php';
    
    /**
     * A class to manage a exercise.
     */
    class Exercise {

        private $path;
        private $data;
        private $id;
        private $name;
        private $actionHeading;
        private $showVideoTimecode;
        private $displayLimits;
        private $videoStart;
        private $videoEnd;
        private $actions = array();
        private $rewind_action;

        /**
         * Class constructor to set and read the exercise.
         * @param string $filePath The file path to the exercise JSON file
         * @return void
         */
        public function __construct( $filePath ) {

            $this->path = $filePath;
            $this->readExercise();

        }

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

        /**
         * Read and decode the JSON file.
         * @return void
         */
        private function readExercise() {

            if ( file_exists( $this->path ) ) {

                $src = file_get_contents( $this->path );

                try {

                    $this->data = JsonHandler::decode( $src, true );
                    $_SESSION['exercise_data'] = $this->data; // save the decoded data to session

                } catch( RuntimeException $e ) {

                    echo '<div class="error">' . $e->getMessage();
                    exit();

                }

                $this->name = getValue( $this->data['exercise']['name'], 'LiveButton+' );
                $this->actionHeading = getValue( $this->data['exercise']['actionHeading'], 'Actions' );
                $this->showVideoTimecode = getValue( $this->data['exercise']['showVideoTimecode'], true );
                $this->videoStart = getValue( $this->data['exercise']['videoStart'], -1 );
                $this->videoEnd = getValue( $this->data['exercise']['videoEnd'], "00:00" );
                $this->displayLimits = getValue( $this->data['exercise']['displayLimits'], true );

                $this->setActions();
                $this->setRewindAction();

            } else {

                exit( 'Exercise file is not found.' );

            }

        }
        
        /**
         * Set the actions for the exercise.
         * @return void
         */
        private function setActions() {

            $availableActions = $this->data['exercise']['actions'];

            foreach( $availableActions as $item ) {

                $action = new ExerciseAction();
                $action->id = $item['id'];
                $action->name = $item['name'];
                $action->icon = $item['icon'];
                $action->limits = $item['limits'];
                $action->cooldown = $item['cooldown'];

                array_push( $this->actions, $action );

            }

        }
        
        /**
         * Get the actions for the exercise.
         * @return array Return an array of exercise actions.
         */
        public function getActions() {

            return $this->actions;

        }
        
        /**
         * Set the rewind action for the exercise.
         * @return void
         */
        private function setRewindAction() {

            $context = $this->data['exercise']['rewind'];

            $rewind = new ExerciseRewindAction();
            $rewind->id = getValue( $context['id'], "rwd" );
            $rewind->name = getValue( $context['name'], "Rewind" );
            $rewind->icon = getValue( $context['icon'], "rewind" );
            $rewind->limits = getValue( $context['limits'], 5 );
            $rewind->cooldown = getValue( $context['cooldown'], 6);
            $rewind->length = getValue( $context['length'], 3 );
            $rewind->graded = getValue( $context['graded'], true );
            $rewind->enabled = getValue( $context['enabled'], true );

            $this->rewind_action = $rewind;

        }
        
        /**
         * Get the rewind action for the exercise.
         * @return object Return the ExerciseRewindAction object.
         */
        public function getRewindAction() {

            return $this->rewind_action;

        }

    } // end Exercise class

?>