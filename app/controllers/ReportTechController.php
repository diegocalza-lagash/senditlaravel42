<?php

function generateLinkPhotos($id,$pId,$photo){
 	$Id = substr($id, 0, 8).'-'.substr($id, 8, 4).'-'.substr($id, 12, 4).'-'.substr($id, 16, 4).'-'.substr($id, 20, 32);
	$link = 'https://app.sendit.cl/Files/FormEntry/'.$pId.'-'.$Id.$photo.'';
 	return $link;
 }
class ReportTechController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{

		$collRTech = connectMongo()->RTech;
		$docRTech = $collRTech->find();
		return View::make('RT.index', array("docRTech" => $docRTech));
		//$this->layout->content = View::make('ReportTech.index');
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$aRequest = json_decode(file_get_contents('php://input'),true);
		$m = new MongoClient();//obsoleta desde mongo 1.0.0
		$db = $m->SenditForm;
		$collRTech = $db->RTech;


		$Id = substr($id, 0, 8).'-'.substr($id, 8, 4).'-'.substr($id, 12, 4).'-'.substr($id, 16, 4).'-'.substr($id, 20, 32);
		$photo = 'https://app.sendit.cl/Files/FormEntry/'.$aRequest['ProviderId'].'-'.$Id.$aRequest['Entry']['AnswersJson']['PHOTOS']['PHOTO1'].'';
		$collRTech->insert($aRequest);
		echo "Insertado en RTech Collection";
	}



	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		//echo "hola show ".$id;
		$m = new MongoClient();//obsoleta desde mongo 1.0.0
		$db = $m->SenditForm;
		$collRTech = $db->RTech;
		$rt = $collRTech->findOne(['Entry.Id' => $id]);

		$l = generateLinkPhotos($rt['Entry']['Id'],$rt['ProviderId'],$rt['Entry']['AnswersJson']['state_i']['photo1_i']);
		//echo $l;
		//return Redirect::route('rtech', array("r" => $r));
		return View::make('RT.rtech', array("rt" => $rt));

	}
	public function crearPDF($rt,$vistaurl,$tipo){

		$view =  \View::make("RT.rtech", compact('rt'))->render();
       $pdf = \App::make('dompdf');
       //$pdf = PDF::loadView($view);
        $pdf->loadHTML($view);
        if ($tipo == 1) {
			return $pdf->stream();
			//return View::make('RT.rtech', array("rt" => $rt));
		}else return $pdf->download('reporte.pdf');



	}
	public function toPDF($tipo,$id)
	{
		//
		//echo "hola PDF ".$id;
		//echo "tipo ".$tipo;
		$m = new MongoClient();//obsoleta desde mongo 1.0.0
		$db = $m->SenditForm;
		$collRTech = $db->RTech;
		$rt = $collRTech->findOne(['Entry.Id' => $id]);

		$l = generateLinkPhotos($rt['Entry']['Id'],$rt['ProviderId'],$rt['Entry']['AnswersJson']['state_i']['photo1_i']);
		$vistaurl="RT.rtech";
		//echo $l;
		//return Redirect::route('rtech', array("r" => $r));
		/**/
        return $this->crearPDF($rt, $vistaurl,$tipo);

	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//echo "hola PDF ".$id;
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
