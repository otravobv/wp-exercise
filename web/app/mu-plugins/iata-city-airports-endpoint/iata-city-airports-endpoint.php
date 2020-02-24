<?php
/*
Plugin Name:  Airports by IATA City code Endpoint
Description:  REST API endpoint which will return a list of airports for specified IATA city code
Version:      1.0.0
*/

require_once "inc/CreateCityAirportsTable.php";
require_once "inc/IataCityAirportsController.php";
 
add_action('rest_api_init', function()
{
    $migration = new CreateCityAirportsTable();
    $controller = new IataCityAirportsController();
});