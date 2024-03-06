<?php

namespace App\Http\Controllers;

use App\Models\Harvest;
use Illuminate\Http\Request;

class HarvestController extends Controller
{
    /**
     * Liste des recoltes
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $harvests = Harvest::join('fields', 'harvests.field_id', '=', 'fields.id')
        ->select('harvests.id as harvest_id', 'harvests.weight_coton', 'harvests.price_unit', 'harvests.date','harvests.observation','harvests.field_id', 'fields.id as field_id', 'fields.name as field_name', 'fields.location as field_location')
        ->get();

        return self::apiResponse(true, "", $harvests);
    }

    /**
     * Ajouter une recolte
     *
     *@bodyParam weight_coton integer required poids de coton
     * @bodyParam price_unit integer required prix unitaire d'un poid de coton
     * @bodyParam date date required date de recolte
     * @bodyParam observation string required obsertion de recolte
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'weight_coton' => 'required|integer',
            'price_unit' => 'required|integer',
            'date' => 'required|date',
            'observation' => 'required|string',
            'field_id' => 'required|integer|exists:fields,id',
        ];

        $request->validate($rules);



        // Verify if harvest already exists
        $exist_harvest = Harvest::where('weight_coton', $request->weight_coton)->first();


        if($exist_harvest){
            return self::apiResponse(false, "This harvest already exists.", [], 400);
        }

        $harvest = Harvest::create($request->all());

        if($harvest) {
            return self::apiResponse(true, "harvest successfully created.", $harvest);
        } else {
            return self::apiResponse(false, "harvest has not been created.", [], 400);
        }
    }

    /**
     * Récupérer les informations d'une recolte
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $harvest = Harvest::find($id);

        if ($harvest) {
            return self::apiResponse(true, "", $harvest);
        } else{
            return self::apiResponse(false, "harvest not found.", [], 404);
        }
    }

    /**
     * Modifier une recolte
     *
     *@bodyParam name string required nom de champs
     * @bodyParam location string required lieux de champs
     * @bodyParam surface integer required superficie du champs
     * @bodyParam date date required date de recolte
     * @bodyParam observation string required obsertion de recolte
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'weight_coton' => 'required|integer',
            'price_unit' => 'required|integer',
            'date' => 'required|date',
            'observation' => 'required|string',
            'field_id' => 'required|integer|exists:fields,id'
        ];

        $request->validate($rules);


        $harvest = Harvest::find($id);

        if ($harvest) {
            // Verify if the new harvest already exists
            $exist_harvest = Harvest::where('weight_coton', $request->weight_coton)->first();

            if($exist_harvest && $exist_harvest->id != $harvest->id){
                return self::apiResponse(false, "This harvest already exists.", [], 400);
            }

            $harvest->update($request->all());

            return self::apiResponse(true, "harvest successfully updated.", $harvest);
        } else{
            return self::apiResponse(false, "harvest not found.", [], 404);
        }
    }

    /**
     * Supprimer un champ
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $harvest = Harvest::find($id);

        if ($harvest) {

            $harvest->delete();

            return self::apiResponse(true, "harvest successfully deleted");
        } else {
            return self::apiResponse(false, "harvest not found", [], 404);
        }
    }

    public static function apiResponse($success, $message, $data = [], $status = 200)
    {
        $response = response()->json([
            'success' => $success,
            'message' => $message,
            'body' => $data
        ], $status);
        return $response;
    }
}
