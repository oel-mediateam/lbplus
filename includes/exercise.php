<?php

    if ( !defined( "ABSPATH" ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        exit('Error 404 - Page Not Found');

    }

    require_once 'includes/exercise_action.php';
    require_once 'includes/exercise_reminder_action.php';

    class Exercise {

        private $path;
        private $data;
        private $id;
        private $name;
        private $actionHeading;
        private $showVideoTimecode;
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

                $this->data = json_decode( $src, true );
                $this->name = getValue( $this->data['exercise']['name'], 'LiveButton+' );
                $this->actionHeading = getValue( $this->data['exercise']['actionHeading'], 'Actions' );
                $this->showVideoTimecode = getValue( $this->data['exercise']['showVideoTimecode'], true );

            } else {

                exit( 'Exercise file is not found.' );

            }

        }

        public function getActions() {

            $availableActions = $this->data['exercise']['actions'];

            foreach( $availableActions as $item ) {

                $action = new ExerciseAction();
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