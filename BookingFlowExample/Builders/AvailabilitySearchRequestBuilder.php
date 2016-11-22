<?php

/**
 * AvailabilitySearchCreator short summary.
 *
 * AvailabilitySearchCreator description.
 *
 * @version 1.0
 * @author marius
 */
class AvailabilitySearchRequestBuilder {
	
	public static function build($check_in, $check_out) {
		$availability_request = new PlugAndTravel\Client\Models\HotelAvailabilityRQ();
		
		$availability_request->setCheckIn($check_in);
		$availability_request->setCheckOut($check_out);
		$availability_request->setCurrency("EUR");
		$availability_request->setPassengerCountry("RO");
		$availability_request->setDestinationCode(6958); //Paris

		$occupancy = new PlugAndTravel\Client\Models\Occupancy();
		$occupancy->setAdults(2);
		$occupancy->setChildren(0);

		$availability_request->setOccupancies(array($occupancy));
		return $availability_request;
	}
}
