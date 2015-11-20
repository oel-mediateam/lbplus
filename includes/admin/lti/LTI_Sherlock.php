<?php
    
    require_once 'LTI_Tool_Provider.php';
    
    class LTI_Sherlock_Outcome extends LTI_Outcome {
        
        // string texts value
        private $texts = NULL;
        
        public function __construct( $sourcedid = NULL, $value = NULL, $texts = NULL ) {
            
            parent::__construct( $sourcedid, $value );
            
            $this->texts = $texts;
            $this->language = 'en-US';
            $this->date = gmdate('Y-m-d\TH:i:s\Z', time());
            $this->type = 'decimal';
        
        }
        
        // get texts value
        public function getTexts() {
        
            return $this->texts;
        
        }
    
        // Set the outcome value
        public function setTexts( $value ) {
        
            $this->texts = $value;
        
        }
        
        // Get the result sourcedid value.
        public function getSourcedid() {
        
            return parent::getSourcedid();
        
        }
        
    }
    
    class LTI_Sherlock_Resource_Link extends LTI_Resource_Link {
        
        public function __construct( $consumer, $id, $current_id = NULL ) {
            
            parent::__construct( $consumer, $id, $current_id );
            
        }
        
        public function doOutcomesService( $action, $lti_outcome ) {
        
            $response = FALSE;
            $this->ext_response = NULL;
            
            // lookup service details from the source resource link appropriate to the user (in case the destination is being shared)
            $source_resource_link = $this;
            $sourcedid = $lti_outcome->getSourcedid();
            
            //Use LTI 1.1 service in preference to extension service if it is available
            
            $urlLTI11 = $source_resource_link->getSetting('lis_outcome_service_url');
            $urlExt = $source_resource_link->getSetting('ext_ims_lis_basic_outcome_url');
            
            //determine action: read, write, or delete
            if ( $urlExt || $urlLTI11 ) {
                
                switch ($action) {
                    
                    case self::EXT_READ:
                    
                        if ( $urlLTI11 && ( $lti_outcome->type == self::EXT_TYPE_DECIMAL ) ) {
                            
                            $do = 'readResult';
                            
                        } else if ( $urlExt ) {
                            
                            $urlLTI11 = NULL;
                            $do = 'basic-lis-readresult';
                            
                        }
                        
                        break;
                        
                    case self::EXT_WRITE:
                    
                        if ( $urlLTI11 && $this->checkValueType( $lti_outcome, array( self::EXT_TYPE_DECIMAL ) ) ) {
                            
                            $do = 'replaceResult';
                            
                        } else if ( $this->checkValueType( $lti_outcome ) ) {
                            
                            $urlLTI11 = NULL;
                            $do = 'basic-lis-updateresult';
                            
                        }
                        
                        break;
                    
                    case self::EXT_DELETE:
                    
                        if ( $urlLTI11 && ( $lti_outcome->type == self::EXT_TYPE_DECIMAL ) ) {
                            
                            $do = 'deleteResult';
                            
                        } else if ( $urlExt ) {
                            
                            $urlLTI11 = NULL;
                            $do = 'basic-lis-deleteresult';
                            
                        }
                        
                        break;
                    
                }
                
            }
            
            if ( isset( $do ) ) {
                
                $value = $lti_outcome->getValue();
                $texts = $lti_outcome->getTexts();
                
                if ( is_null( $value ) ) {
                    
                    $value = '';
                    
                }
                
                if ( is_null( $texts ) ) {
                    
                    $texts = '';
                    
                } else {
                    
                    $texts = '<resultData>
                                    <text>'.$texts.'</text>
                                </resultData>';
                    
                }
                
                if ( $urlLTI11 ) {
                    
                    $xml = '';
                    $sourcedid = htmlentities( $sourcedid );
                    
                    if ( $action == self::EXT_WRITE ) {
                        
                        $xml = '<result>
                                    <resultScore>
                                        <language>'.$lti_outcome->language.'</language>
                                        <textString>'.$value.'</textString>
                                    </resultScore>
                                    '.$texts.'
                                </result>';
                        
                    }
                    
                    $xml = '<resultRecord>
                                <sourcedGUID>
                                    <sourcedId>'.$sourcedid.'</sourcedId>
                                </sourcedGUID>'
                                .$xml.'
                            </resultRecord>';
                    
                    if ( $this->doLTI11Service( $do, $urlLTI11, $xml ) ) {
                        
                        switch ($action) {
                            
                        case self::EXT_READ:
                        
                            if (!isset($this->ext_nodes['imsx_POXBody']["{$do}Response"]['result']['resultScore']['textString'])) {
                                
                                break;
                                
                            } else {
                                
                                $lti_outcome->setValue($this->ext_nodes['imsx_POXBody']["{$do}Response"]['result']['resultScore']['textString']);
                            }
                            
                        case self::EXT_WRITE:
                        case self::EXT_DELETE:
                        
                            $response = TRUE;
                            break;
                        
                        }
                        
                    }
                    
                } else {
                    
                    $params = array();
                    $params['sourcedid'] = $sourcedid;
                    $params['result_resultscore_textstring'] = $value;
                    
                    if ( !empty( $lti_outcome->language ) ) {
                        
                        $params['result_resultscore_language'] = $lti_outcome->language;
                        
                    }
                    
                    if ( !empty( $lti_outcome->status ) ) {
                        
                        $params['result_statusofresult'] = $lti_outcome->status;
                        
                    }
                
                    if ( !empty( $lti_outcome->date ) ) {
                        
                        $params['result_date'] = $lti_outcome->date;
                        
                    }
                    
                    if ( !empty( $lti_outcome->type ) ) {
                        
                        $params['result_resultvaluesourcedid'] = $lti_outcome->type;
                        
                    }
                    
                    if ( !empty( $lti_outcome->data_source ) ) {
                        
                        $params['result_datasource'] = $lti_outcome->data_source;
                    
                    }
                    
                    if ( $this->doService( $do, $urlExt, $params ) ) {
                        
                        switch ( $action ) {
                            
                        case self::EXT_READ:
                        
                            if ( isset( $this->ext_nodes['result']['resultscore']['textstring'] ) ) {
                                
                                $response = $this->ext_nodes['result']['resultscore']['textstring'];
                                
                            }
                            
                            break;
                            
                        case self::EXT_WRITE:
                        case self::EXT_DELETE:
                        
                            $response = TRUE;
                            
                            break;
                        
                        }
                    }
                    
                }
            
                if ( is_array( $response ) && ( count( $response ) <= 0 ) ) {
                
                    $response = '';
                    
                }
                
            }
            
            return $response;
        
        }
        
    }
    
?>