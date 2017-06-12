<?php

	function turn_dates($date){
		$date = new DateTime($date);
		$date->setTimezone(new DateTimeZone('America/Santiago'));
		return $date->format('j F, Y, g:i a');
	}

			$m = new MongoClient();
			$db = $m->SenditForm;
			$db->seg->drop();
			//$db->seg->insert(iterator_to_array($seg,false));

			//$seg = iterator_to_array($seg,false);


			//$dsr = turn_dates($seg[1]['EQUIPMENT']['WORK']['SUBWORK']['DATE_START_REAL']);


			//$der = turn_dates($seg[1]['EQUIPMENT']['WORK']['SUBWORK']['DATE_END_REAL']);




?>
<head>
	<link rel="stylesheet" href="{{ URL::asset('assets/css/css_rtech.css') }}">
	<link rel="stylesheet" href="{{ URL::asset('assets/css/bootstrap.min.css') }}">

</head>

	<div class="report_seguimiento">
		<div class="itemizado">
			<div class="itemizado_titulo"><h2>Itemizado trabajos</h2></div>
			<div class="container_itemizado">

				<div class="section_left ">
					<div class="divisor"></div>
					<div class="headers">
						<div>N°</div>
						<div>Detalle actividad</div>

					</div>

					<!--<div class="numero">N°</div>
					<div class="detalle_act">Detalle actividad</div>-->
				</div>
				<div class="section_right "></div>
				<div class="trabajos">
					<div class="work">{{ $seg[0]['EQUIPMENT']['WORK']['WORK_NAME'] }}</div>
					<div class="sub_work inline">{{ $seg[0]['EQUIPMENT']['WORK']['SUBWORK']['SUBWORK_NAME'] }}</div>
					<div class="text_center inline">
						<div class="dsr inline">{{ turn_dates($seg[0]['EQUIPMENT']['WORK']['SUBWORK']['DATE_START_REAL'])  }}</div>
						<div class="der inline">{{ turn_dates($seg[0]['EQUIPMENT']['WORK']['SUBWORK']['DATE_END_REAL']) }}</div>
						<div class="poop inline">{{ $seg[0]['EQUIPMENT']['WORK']['SUBWORK']['POOP'] }}</div>
					</div>



					<?php for ($i=1; $i < count($seg) ; $i++) { ?>
						<div class="subwork_iterator">
							<div class="sub_work inline">{{ $seg[$i]['EQUIPMENT']['WORK']['SUBWORK']['SUBWORK_NAME'] }}</div>
							<div class="text_center inline">
								<div class="dsr inline">{{ turn_dates($seg[$i]['EQUIPMENT']['WORK']['SUBWORK']['DATE_START_REAL']) }}</div>
								<div class="der inline">{{ turn_dates($seg[$i]['EQUIPMENT']['WORK']['SUBWORK']['DATE_END_REAL'] )}}</div>
								<div class="poop inline">{{ $seg[$i]['EQUIPMENT']['WORK']['SUBWORK']['POOP'] }}</div>


							</div>

						</div>

					<?php }?>


				</div>
			</div>

		</div>

	</div>

