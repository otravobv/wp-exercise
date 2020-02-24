<?php

if (!class_exists('IataCityAirportsController')) {
    
    class IataCityAirportsController
    {

        public function __construct()
        {
            $this->namespace = 'airports';
            $this->tableName = CreateCityAirportsTable::$tableName;
            $this->registerRoutes();
        }

        // Register API Endpoints
        // GET ~/airports/IATA_CITY_CODE (3 symbols A-z)
        // Returns JSON result with Airports for specified city

        // PUT ~/airports/add
        // Populates database with City-Airports data
        // Supply data as JSON via request body
        // Header application/json
        public function registerRoutes()
        {
            register_rest_route($this->namespace, '/(?P<city>[A-z]{3}$)', array(
                'methods' => 'GET',
                'callback' => array($this, 'getItems'),
            ));
            
            register_rest_route($this->namespace, '/add', array(
                'methods' => 'PUT',
                'callback' => array($this, 'addItems'),
            ));
        }

        // Get all airports associated with specified city
        public function getItems(WP_REST_Request $request)
        {
            global $wpdb;
            $city = $request->get_param('city');
    
            $sql = $this->prepareGetQuery($city);
            $response = $wpdb->get_results($sql);

            //Throw error if no data
            if (empty($response)) {
                return $this->generateError('error_getting_results', 'Could not find specified city: '.$city, 404);
            }

            return rest_ensure_response($response);
        }

        // Add data to database
        public function addItems(WP_REST_Request $request)
        {
            $validatedData = $this->validateAirports($request->get_params());

            // Check for validation errors
            if (is_wp_error($validatedData)) {
                return $validatedData;
            }
            
            global $wpdb;
            $sql = $this->prepareInsertQuery($validatedData);
            $response = $wpdb->query($sql);

            if (!$response) {
                $this->generateError('error_populating_table', 'An error occured while inserting values');
            }

            return new WP_REST_Response('Airports added successfully', 200);
        }

        // Validate airports data before inserting into DB
        public function validateAirports($airports)
        {
            $validatedData = array();

            foreach ($airports as $airport) {
                if (!preg_match('/^[A-z]{3}$/', $airport['code'])) {
                    $validationError = $this->generateError('invalid_airport_code', 'Airport Code must consist of 3 Latin letters [A-Z], given value: '.$airport['code']);
                    break;
                }
                //maybe should use strlen(utf8_decode())
                if (mb_strlen($airport['name'], 'UTF-8') > 200) {
                    $validationError = $this->generateError('invalid_airport_name', 'Airport name too long: '.$airport['name']);
                    break;
                }
                if (!preg_match('/^[A-z]{3}$/', $airport['location']['cityCode'])) {
                    $validationError = $this->generateError('invalid_city_code', 'City Code must consist of 3 Latin letters [A-Z], given value: '.$airport['cityCode']);
                    break;
                }
                if (!preg_match('/^[A-z]{2}$/', $airport['location']['countryCode'])) {
                    $validationError = $this->generateError('invalid_country_code', 'Country Code must consist of 2 Latin letters [A-Z], given value: '.$airport['countryCode']);
                    break;
                }
                if (!preg_match('/^[A-z]{2}$/', $airport['location']['continentCode'])) {
                    $validationError = $this->generateError('invalid_continent_code', 'Continent Code must consist of 2 Latin letters [A-Z], given value: '.$airport['continentCode']);
                    break;
                }

                array_push($validatedData, array(
                    'code' => strtoupper($airport['code']),
                    'name' => $airport['name'],
                    'cityCode' => strtoupper($airport['location']['cityCode']),
                    'countryCode' => strtoupper($airport['location']['countryCode']),
                    'continentCode' => strtoupper($airport['location']['continentCode']),
                ));
            }

            if (isset($validationError)) {
                return $validationError;
            }

            return $validatedData;
        }

        // Prepare airports data for insertion
        public function prepareInsertQuery($airports)
        {
            global $wpdb;
            $tablename = $wpdb->prefix.$this->tableName;

            $values = array();
            $placeholders = array();

            $query = "INSERT INTO $tablename (airport_code, airport_name, city_code, country_code, continent_code) VALUES ";

            foreach ($airports as $airport) {
                array_push($values, $airport['code'], $airport['name'], $airport['cityCode'], $airport['countryCode'], $airport['continentCode']);
                $placeholders[] = "(%s, %s, %s, %s, %s)";
            }

            $query .= implode(', ', $placeholders);
            return $wpdb->prepare($query, $values);
        }

        // Query for airports by city code
        public function prepareGetQuery($city)
        {
            global $wpdb;
            $tablename = $wpdb->prefix.$this->tableName;

            $query = "SELECT airport_code as code, 
                            airport_name as name, 
                            city_code as cityCode, 
                            country_code as countryCode, 
                            continent_code as continentCode 
                            FROM $tablename 
                            WHERE city_code=%s";

            return $wpdb->prepare($query, $city);
        }

        // Generate errors using WP_Error
        public function generateError($code = 'error', $description = 'Error occured', $status = 400)
        {
            return new WP_Error($code, $description, array('status' => $status));
        }
    }
}