
function setDisabledDays(){
	amDebug('setDisabledDays');
	if ( typeof holidays !== 'undefined' ){
		for( i=0; i< holidays.length; i++){
			// make timestamp to date
			var holiday = new Date(holidays[i]*1000);
			// format day
			var day = ( (holiday.getDate()<10) ? "0" + holiday.getDate()  : holiday.getDate() );
			// format month
			var month = (holiday.getMonth()+1);
			month = ( (month<10) ? "0" + month  : month  );

			// build string : dd.mm.yyyy
			dd = day +"."+month+"."+holiday.getFullYear();
			disabled_days.push( dd );
		}
	}
}

function setDaysOfWeek(){
	amDebug('setDaysOfWeek');
	if ( typeof days_of_publication !== 'undefined' ){
		for( i=0; i<days_of_publication.length; i++){
			if ( days_of_publication[i] == 0 ){
				days_of_week.push(i);
			}
		}
	}
}

function twoWeeksBack( date, today ){
		if ( date < (today-(1*60*60*24*14*1000) ) ){
			return true
		}
		else{
			return false;
		}
 	}


function initDatepicker(){
	/* http://bootstrap-datepicker.readthedocs.org/en/latest/index.html */
	amDebug('initDatepicker');

	setDisabledDays();
	setDaysOfWeek();
	$('.datepicker').datepicker({
		format: 'dd.mm.yyyy',
		language: 'no',
		weekStart: 1,
		datesDisabled: disabled_days,
		daysOfWeekDisabled: days_of_week,
	});

	$('.datepicker-complaint').datepicker({
		format: 'dd.mm.yyyy',
		language: 'no',
		weekStart: 1,
		datesDisabled: disabled_days,
		daysOfWeekDisabled: days_of_week,
		beforeShowDay: function(date){
				var today =  new Date();
				if ( date > today)
					return false;
				else
					return;
      }
	});

	if($('.datepickerFrom').length && $('.datepickerTo').length){
		$('.datepickerFrom').change( function(){
			var fromDatoUtc = $('.datepickerFrom').datepicker('getUTCDate');
			var toDatoUtc = $('.datepickerTo').datepicker('getUTCDate');

			if ( fromDatoUtc > toDatoUtc){
				$('.datepickerTo').datepicker('setDate', $('.datepickerFrom').val() );
			}


		});
	}
}