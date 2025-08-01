document.addEventListener('DOMContentLoaded', function() {
    const bookingTypeSelect = document.getElementById('booking_type');
    const detailsFieldsContainer = document.getElementById('booking_details_fields');

    if (bookingTypeSelect) {
        bookingTypeSelect.addEventListener('change', function() {
            const type = this.value;
            let html = '';

            switch (type) {
                case 'flight':
                    html = `
                        <div class="mb-3">
                            <label for="airline" class="form-label">Airline</label>
                            <input type="text" class="form-control" id="airline" name="details[airline]" required>
                        </div>
                        <div class="mb-3">
                            <label for="flight_number" class="form-label">Flight Number</label>
                            <input type="text" class="form-control" id="flight_number" name="details[flight_number]" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="departure_airport" class="form-label">Departure Airport</label>
                                <input type="text" class="form-control" id="departure_airport" name="details[departure_airport]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="arrival_airport" class="form-label">Arrival Airport</label>
                                <input type="text" class="form-control" id="arrival_airport" name="details[arrival_airport]" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="departure_time" class="form-label">Departure Time</label>
                                <input type="datetime-local" class="form-control" id="departure_time" name="details[departure_time]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="arrival_time" class="form-label">Arrival Time</label>
                                <input type="datetime-local" class="form-control" id="arrival_time" name="details[arrival_time]" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="booking_reference" class="form-label">Booking Reference</label>
                            <input type="text" class="form-control" id="booking_reference" name="details[booking_reference]">
                        </div>
                    `;
                    break;
                case 'hotel':
                    html = `
                        <div class="mb-3">
                            <label for="hotel_name" class="form-label">Hotel Name</label>
                            <input type="text" class="form-control" id="hotel_name" name="details[hotel_name]" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="details[address]" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="check_in_date" class="form-label">Check-in Date</label>
                                <input type="date" class="form-control" id="check_in_date" name="details[check_in_date]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="check_out_date" class="form-label">Check-out Date</label>
                                <input type="date" class="form-control" id="check_out_date" name="details[check_out_date]" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="booking_confirmation" class="form-label">Booking Confirmation</label>
                            <input type="text" class="form-control" id="booking_confirmation" name="details[booking_confirmation]">
                        </div>
                    `;
                    break;
                case 'transport':
                    html = `
                        <div class="mb-3">
                            <label for="transport_type" class="form-label">Type (e.g., JR Pass, Bus Ticket)</label>
                            <input type="text" class="form-control" id="transport_type" name="details[transport_type]" required>
                        </div>
                        <div class="mb-3">
                            <label for="transport_details" class="form-label">Details</label>
                            <textarea class="form-control" id="transport_details" name="details[transport_details]" rows="3"></textarea>
                        </div>
                    `;
                    break;
            }

            detailsFieldsContainer.innerHTML = html;
        });
    }
});
