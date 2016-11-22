<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require_once(__DIR__ . './../vendor/autoload.php');

require_once( 'Helpers/SecretKeyEncription.php');
require_once( 'Builders/AvailabilitySearchRequestBuilder.php');
require_once( 'Builders/CheckRatesRequestBuilder.php');
require_once( 'Builders/BookingRequestBuilder.php');

const USERNAME = "Demo";
const SECRET_KEY = "Demo";

$check_in = Date('d-m-Y', strtotime("+90 days"));
$check_out = Date('d-m-Y', strtotime("+93 days"));

// to debug report
print PlugAndTravel\Configuration::toDebugReport();

// to change temp folder path
PlugAndTravel\Configuration::getDefaultConfiguration()->setTempFolderPath('/var/tmp/php/');

// to enable logging
PlugAndTravel\Configuration::getDefaultConfiguration()->setDebug(true);
PlugAndTravel\Configuration::getDefaultConfiguration()->setDebugFile('/var/tmp/php_debug.log');

try {

	// search for availability
	$key = PlugAndTravel\Encrypt\SecretKeyEncription::encrypt(SECRET_KEY);
	$availability_api_client = new PlugAndTravel\Client\Api\AvailabilityApi();

	$availability_request = AvailabilitySearchRequestBuilder::build($check_in, $check_out);
	$serialized_availability_request = $availability_request->__toString();
	$availability_response = $availability_api_client->getHotelAvailability($serialized_availability_request, USERNAME, $key);
	$hotels = $availability_response->getHotels();

	// get booking flow id
	$booking_flow_id = $availability_response->getBookingFlowId();


	// check rates
	$key = PlugAndTravel\Encrypt\SecretKeyEncription::encrypt(SECRET_KEY);
	$check_rates_api_client = new PlugAndTravel\Client\Api\CheckRatesApi();

	$check_rates_request = CheckRatesRequestBuilder::build($booking_flow_id, $check_in, $check_out, $availability_response);
	$check_rates_response = $check_rates_api_client->checkHotelRates($check_rates_request, USERNAME, $key);


	//book
	$key = PlugAndTravel\Encrypt\SecretKeyEncription::encrypt(SECRET_KEY);
	$booking_api_client = new PlugAndTravel\Client\Api\ReservationApi();

	$book_request = BookingRequestBuilder::build($booking_flow_id, $check_in, $check_out, $check_rates_response);
	$book_response = $booking_api_client->book($book_request, USERNAME, $key);

	
	// cancel
	$key = PlugAndTravel\Encrypt\SecretKeyEncription::encrypt(SECRET_KEY);

	$cancel_request = new PlugAndTravel\Client\Models\CancelRQ(array("reservation_id" => $book_response->getReservation()->getReservationRooms()[0]->getId()));
	$cancel_response =  $booking_api_client->cancel($cancel_request, USERNAME, $key);

} catch (PlugAndTravel\ApiException $e) {
	echo 'Caught exception: ', $e->getMessage(), "\n";
	echo 'HTTP response headers: ', json_encode($e->getResponseHeaders()), "\n";
	echo 'HTTP response body: ', json_encode($e->getResponseBody()), "\n";
	echo 'HTTP status code: ', $e->getCode(), "\n";
}