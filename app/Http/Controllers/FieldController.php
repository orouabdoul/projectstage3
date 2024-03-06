<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
      /**
     * Liste des champs
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $fields = Field::join('users', 'fields.user_id', '=', 'users.id')
        ->select('fields.id as field_id', 'fields.name', 'fields.location', 'fields.surface','fields.user_id', 'users.id as user_id', 'users.first_name as user_first_name',
         'users.last_name as user_last_name')->get();



        return self::apiResponse(true, "", $fields);
    }

    /**
     * Ajouter un champs
     *
    *@bodyParam name string required nom de champs
     * @bodyParam location string required lieux de champs
     * @bodyParam surface integer required superficie du champs
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'name' => 'required|string',
            'location' => 'required|string',
            'surface' => 'required|integer',
            'user_id' => 'required|integer|exists:users,id',
        ];

        $request->validate($rules);

        $request ->merge([
            'name'=>  ucfirst(strtolower($request->name)),
            'cover'=>  ucfirst(strtolower($request->location)),
        ] );


        // Verify if field already exists
        $exist_field = Field::where('name', $request->name)->orWhere('location', $request->location)->first();


        if($exist_field){
            return self::apiResponse(false, "This field already exists.", [], 400);
        }

        $field = Field::create($request->all());

        if($field) {
            return self::apiResponse(true, "field successfully created.", $field);
        } else {
            return self::apiResponse(false, "field has not been created.", [], 400);
        }
    }

    /**
     * Récupérer les informations d'un champ
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $field = Field::find($id);

        if ($field) {
            return self::apiResponse(true, "", $field);
        } else{
            return self::apiResponse(false, "field not found.", [], 404);
        }
    }

    /**
     * Modifier un champ
     *
     *@bodyParam name string required nom de champs
     * @bodyParam location string required lieux de champs
     * @bodyParam surface integer required superficie du champs
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string',
            'location' => 'required|string',
            'surface' => 'required|integer',
            'user_id' => 'required|integer|exists:users,id',
        ];

        $request->validate($rules);

        $request ->merge([
            'name'=>  ucfirst(strtolower($request->name)),
            'location'=>  ucfirst(strtolower($request->location)),
        ]);

        $field = Field::find($id);

        if ($field) {
            // Verify if the new field already exists
            $exist_field = field::where('name', $request->name)->orWhere('location', $request->location)->first();


            if($exist_field && $exist_field->id != $field->id){
                return self::apiResponse(false, "This field already exists.", [], 400);
            }

            $field->update($request->all());

            return self::apiResponse(true, "field successfully updated.", $field);
        } else{
            return self::apiResponse(false, "field not found.", [], 404);
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
        $field = Field::find($id);

        if ($field) {

            $field->delete();

            return self::apiResponse(true, "field successfully deleted");
        } else {
            return self::apiResponse(false, "field not found", [], 404);
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
