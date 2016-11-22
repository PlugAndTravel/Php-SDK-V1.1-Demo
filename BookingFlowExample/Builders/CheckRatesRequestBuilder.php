<?php

/**
 * CheckRatesRequestBuilder short summary.
 *
 * CheckRatesRequestBuilder description.
 *
 * @version 1.0
 * @author marius
 */
class CheckRatesRequestBuilder {

	public static function build($booking_flow_id, $check_in, $check_out, PlugAndTravel\Client\Models\HotelAvailabilityRS $availability_response) {
		$check_rates_request = new PlugAndTravel\Client\Models\CheckHotelRatesRQ();
		$init_array = array(
			"booking_flow_id" => $booking_flow_id,
			"check_in" => $check_in,
			"check_out" => $check_out, 
			"passenger_country" => "RO"
		);
		$check_rates_request->__construct($init_array);

		$room_group = CheckRatesRequestBuilder::create_room_group($availability_response->getHotels()[0]);
		$check_rates_request->setRoomGroup($room_group);
		
		return $check_rates_request;
	}

	private static function  create_room_group(PlugAndTravel\Client\Models\Hotel $hotel) {
		$room_group = new PlugAndTravel\Client\Models\RoomGroupRQ();
		$booked_room_group = $hotel->getRoomGroups()[0];
		$room = $booked_room_group->getRooms()[0];
		$room_group->setCode($booked_room_group->getCode());
		
		$room_request_init_array = array(
			"code" => $room->getCode(),
			"name" => $room->getName(),
			"hotel_code" => $hotel->getCode(), 
			"provider_hotel_code" => $room->getProviderHotelCode(),
			"provider" => $room->getProvider(), 
			"total_price" => array_sum($room->getPrice()->getTotalPriceBreakdown()),
			"currency" => $room->getCurrency(), 
			"meal" => $room->getMeal(),
			"tokens" => $room->getTokens(),
			"occupancy" => $room->getOccupancy()
		);
		$room_request = new PlugAndTravel\Client\Models\RoomRQ($room_request_init_array);
		
		$room_group->setRooms(array($room_request));
		return $room_group;
	}
}
