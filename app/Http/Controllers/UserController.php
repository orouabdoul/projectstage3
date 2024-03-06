<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
   /**
     * Liste des recoltes
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::join('roles', 'users.role_id', '=', 'roles.id')
        ->select('users.id as user_id', 'users.first_name', 'users.last_name', 'users.sexe', 'users.phone', 'users.department','users.common', 'users.borough','users.neighborhood', 'users.role_id', 'roles.id as role_id', 'roles.name as role_name')
        ->get();

        return self::apiResponse(true, "", $users);
    }

    /**
     * Ajouter un utilisateur
     *
     * @bodyParam first_name string required prenom
     * @bodyParam last_name string required nom
     * @bodyParam sexe required sexe
     * @bodyParam phone integer required numero telephone
     * @bodyParam department string required departement
     * @bodyParam common string required commune
     * @bodyParam borough string required arrondissement
     * @bodyParam neighborhood string required quartier
     * @bodyParam password string required mot de passe
     *
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'sexe' => 'required|string',
            'phone' => 'required|string|max:12',
            'department' => 'required|string',
            'common' => 'required|string',
            'borough' => 'required|string',
            'neighborhood' => 'required|string',
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer|exists:roles,id',
        ];

        $request->validate($rules);

        $request->merge([
            'last_name' => strtoupper($request->last_name),
        ]);

        // Verify if user already exists
        $exist_user = User::where('first_name', $request->first_name)
                        ->orWhere('last_name', $request->last_name)
                        ->first();

        if ($exist_user) {
            return self::apiResponse(false, "This user already exists.", [], 400);
        }

        $user = User::create($request->all());

        if ($user) {
            return self::apiResponse(true, "User successfully created.", $user);
        } else {
            return self::apiResponse(false, "User has not been created.", [], 400);
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
        $user = User::find($id);

        if ($user) {
            return self::apiResponse(true, "", $user);
        } else{
            return self::apiResponse(false, "user not found.", [], 404);
        }
    }

    /**
     * Modifier un utilisateur
     *
     * @bodyParam first_name string required prenom
     * @bodyParam last_name string required nom
     * @bodyParam sexe required sexe
     * @bodyParam phone integer required numero telephone
     * @bodyParam department string required departement
     * @bodyParam common string required commune
     * @bodyParam borough string required arrondissement
     * @bodyParam neighborhood string required quartier
     * @bodyParam password string required mot de passe
     *
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'sexe' => 'required|string',
            'phone' => 'required|integer',
            'department' => 'required|string',
            'common' => 'required|string',
            'borough' => 'required|string',
            'neighborhood' => 'required|string',
            'password' => 'required|string',
            'role_id' => 'required|integer|exists:roles,id',
        ];

        $request->validate($rules);

        $request ->merge([
            'last_name'=> strtoupper($request->last_name)
        ] );

        $user = User::find($id);


        if ($user) {
                   // Verify if user already exists
            $exist_user = User::where('first_name', $request->first_name)->orWhere('last_name', $request->last_name)->first();

            if($exist_user && $exist_user->id != $user->id){
                return self::apiResponse(false, "This user already exists.", [], 400);
            }

            $user->update($request->all());

            return self::apiResponse(true, "user successfully updated.", $user);
        } else{
            return self::apiResponse(false, "user not found.", [], 404);
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
        $user = user::find($id);

        if ($user) {

            $user->delete();

            return self::apiResponse(true, "user successfully deleted");
        } else {
            return self::apiResponse(false, "user not found", [], 404);
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
