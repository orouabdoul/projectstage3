<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Liste des roles
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();

        return self::apiResponse(true, "", $roles);
    }

    /**
     * Ajouter un role
     *
     * @bodyParam name string required nom de role
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string'
        ];

        $request->validate($rules);

        $request ->merge([
            'name'=>  ucfirst(strtolower($request->name)),
        ] );

        // Verify if role already exists
        $exist_role = Role::where('name', $request->name)->first();

        if($exist_role){
            return self::apiResponse(false, "This role already exists.", [], 400);
        }

        $role = Role::create($request->all());

        if($role) {
            return self::apiResponse(true, "role successfully created.", $role);
        } else {
            return self::apiResponse(false, "role has not been created.", [], 400);
        }
    }

    /**
     * Récupérer les informations d'un role
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = role::find($id);

        if ($role) {
            return self::apiResponse(true, "", $role);
        } else{
            return self::apiResponse(false, "role not found.", [], 404);
        }
    }

    /**
     * Modifier un role
     *
     * @bodyParam name string required nom de role
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
            'name' => 'required|string'
        ];

        $request->validate($rules);

        $request ->merge([
            'name'=>  ucfirst(strtolower($request->name)),
        ] );

        $role = Role::find($id);

        if ($role) {
            // Verify if the new role already exists
            $exist_role = Role::where('name', $request->name)->first();

            if($exist_role && $exist_role->id != $role->id){
                return self::apiResponse(false, "This role already exists.", [], 400);
            }

            $role->update($request->all());

            return self::apiResponse(true, "role successfully updated.", $role);
        } else{
            return self::apiResponse(false, "role not found.", [], 404);
        }
    }

    /**
     * Supprimer un role
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if ($role) {
            $role->delete();

            return self::apiResponse(true, "role successfully deleted.");
        } else{
            return self::apiResponse(false, "role not found.", [], 404);
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
