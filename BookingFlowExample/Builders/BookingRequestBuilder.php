<?php

/**
 * BookingRequestBuilder short summary.
 *
 * BookingRequestBuilder description.
 *
 * @version 1.0
 * @author marius
 */
class BookingRequestBuilder {

	public static function build($booking_flow_id, $check_in, $check_out, $check_rates_rs) {
		$room_group = new PlugAndTravel\Client\Models\ResevationRoomGroupRQ(
			array(
				"code" => $check_rates_rs->getRoomGroup()->getCode(), 
				"rooms" => array(BookingRequestBuilder::createReservationRoomRq($check_rates_rs))
			)
		);

		$booking_request = new PlugAndTravel\Client\Models\ReservationRQ(
			array(
				"booking_flow_id" => $booking_flow_id,
				"check_in" => $check_in,
				"check_out" => $check_out,
				"passenger_country" => "RO",
				"room_group" => $room_group
			)
		);

		return $booking_request;
	}

	private static function createReservationRoomRq($check_rates_rs) {
		$room = $check_rates_rs->getRoomGroup()->getRooms()[0];
		$init_rsv_room_rq = array(
			"code" => $room->getCode(),
			"name" => $room->getName(),
			"hotel_code" => $room->getHotelCode(),
			"provider_hotel_code" => $room->getProviderHotelCode(),
			"provider" => $room->getProvider(),
			"total_reservation_price" => array_sum($room->getPrice()->getTotalPriceBreakdown()),
			"currency" => $room->getCurrency(),
			"meal" => $room->getMeal(),
			"tokens" =>$room->getTokens(),
			"occupancy" => $room->getOccupancy(),
			"guest" => new PlugAndTravel\Client\Models\Guest(
				array(
					"first_name" => "John",
					"last_name" => "Snow"
				)
			)
		);
		
		$reservation_room_rq = new PlugAndTravel\Client\Models\ReservationRoomRQ($init_rsv_room_rq);
		return $reservation_room_rq;
	}
}
