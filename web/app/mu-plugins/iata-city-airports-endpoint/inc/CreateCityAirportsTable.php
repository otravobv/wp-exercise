<?php

if (!class_exists('CreateCityAirportsTable')) {

    class CreateCityAirportsTable
    {
        public static $tableName = 'city_airports';

        public function __construct()
        {
            $this->setupDatabase();
        }

        // Create DB table, if successful store key in options to skip creation later
        private function setupDatabase()
        {
            if (get_option('city_airports_table_created') != 1) {
                $result = $this->up();

                if ($result) {
                    update_option('city_airports_table_created', 1);
                }
            }
        }

        // Table migration
        public function up()
        {
            global $wpdb;
            $tableName = $wpdb->prefix . self::$tableName;

            $sql = "CREATE TABLE IF NOT EXISTS $tableName (
                id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
                airport_code CHAR(3) NOT NULL UNIQUE,
                airport_name VARCHAR(200)   
                    CHARACTER SET utf8mb4 
                    COLLATE utf8mb4_general_ci
                    NOT NULL,
                city_code CHAR(3) NOT NULL,
                country_code CHAR(2) NOT NULL,
                continent_code CHAR(2) NOT NULL,
                PRIMARY KEY  (id),
                INDEX (city_code)
            ) DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            return dbDelta($sql);
        }
    }
}