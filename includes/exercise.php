<?php

    if ( !defined( "ABSPATH" ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }

    require_once 'includes/exercise_action.php';
    require_once 'includes/exercise_reminder_action.php';
    require_once 'includes/json_handler.php';

    class Exercise {

        private $path;
        private $data;
        private $id;
        private $name;
        private $actionHeading;
        private $showVideoTimecode;
        private $videoStart;
        private $videoEnd;
        private $actions = array();
        private $rewind_action;

        // constructor
        public function __construct( $filePath ) {

            $this->path = $filePath;
            $this->readExercise();

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
        private function readExercise() {

            if ( file_exists( $this->path ) ) {

                $src = file_get_contents( $this->path );

                try {

                    $this->data = JsonHandler::decode( $src, true );
                    $_SESSION['exercise_data'] = $this->data;

                } catch( RuntimeException $e ) {

                    echo '<div class="error">' . $e->getMessage();
                    exit();

                }

                $this->name = getValue( $this->data['exercise']['name'], 'LiveButton+' );
                $this->actionHeading = getValue( $this->data['exercise']['actionHeading'], 'Actions' );
                $this->showVideoTimecode = getValue( $this->data['exercise']['showVideoTimecode'], true );
                $this->videoStart = getValue( $this->data['exercise']['videoStart'], -1 );
                $this->videoEnd = getValue( $this->data['exercise']['videoEnd'], "00:00" );

            } else {

                exit( 'Exercise file is not found.' );

            }

        }

        public function getActions() {

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

            return $this->actions;

        }

        public function getRewindAction() {

            $context = $this->data['exercise']['rewind'];

            $rewind = new ExerciseReminderAction();
            $rewind->id = getValue( $context['id'], "rwd" );
            $rewind->name = getValue( $context['name'], "Rewind" );
            $rewind->icon = getValue( $context['icon'], "spinner" );
            $rewind->limits = getValue( $context['limits'], 5 );
            $rewind->cooldown = getValue( $context['cooldown'], 6);
            $rewind->length = getValue( $context['length'], 3 );
            $rewind->graded = getValue( $context['graded'], true );
            $rewind->enabled = getValue( $context['enabled'], true );

            $this->rewind_action = $rewind;

            return $this->rewind_action;

        }

    }


?>