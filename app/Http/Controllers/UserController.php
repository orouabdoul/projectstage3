<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Harvest;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
   /**
     * Liste des utilisateurs
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
    try {
        $users = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'users.id as user_id',
                'users.first_name',
                'users.last_name',
                'users.sexe',
                'users.phone',
                'users.department',
                'users.common',
                'users.borough',
                'users.neighborhood',
                'users.password',
                'users.role_id',
                'roles.id as role_id',
                'roles.name as role_name'
            )
            ->get();

        return self::apiResponse(true, "Users retrieved successfully", $users);
    } catch (\Exception $e) {
        return self::apiResponse(false, "Failed to retrieve users: " . $e->getMessage(), null, 500);
    }
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

        $user = User::create($request->all());

        if ($user) {
            return self::apiResponse(true, "User successfully created.", $user);
        } else {
            return self::apiResponse(false, "User has not been created.", [], 400);
        }

    }

    /**
     * Récupérer les informations d'un utilisateur
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

    public function loginUser(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 401);
        }

        if (Auth::attempt($request->all())) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->plainTextToken;

            return response(['token' => $token, 'data' => $user, ], 200);
        }

        return response(['message' => 'Phone or password is wrong'], 401);
    }


    public function user_bilan(Request $request)
    {
        try {
            $users = User::with('products', 'fields.harvests')->get();
            return self::apiResponse(true, "Users retrieved successfully", $users);
        } catch (\Exception $e) {
            return self::apiResponse(false, "Failed to retrieve users: " . $e->getMessage(), null, 500);
        }
        try {
            // Logique pour récupérer les utilisateurs
            return self::apiResponse(true, "Utilisateurs récupérés avec succès", $users);
        } catch (\Exception $e) {
            return self::apiResponse(false, "Échec de la récupération des utilisateurs: " . $e->getMessage(), null, 500);
        }
    }

            public function best_arrond(Request $request)
        {
            try {
                // Récupérer tous les utilisateurs avec leurs champs et récoltes
                $users = User::with('fields.harvests')->get();

                // Grouper les utilisateurs par borough
                $usersByBorough = $users->groupBy('borough');

                // Initialiser le tableau des résultats
                $result = [];

                // Calculer le poids total des récoltes par borough et trier les utilisateurs
                foreach ($usersByBorough as $borough => $users) {
                    $totalWeight = $users->sum(function ($user) {
                        return $user->fields->flatMap->harvests->sum('weight_coton');
                    });

                    // Trier les utilisateurs par le poids de leurs récoltes
                    $topUsers = $users->sortByDesc(function ($user) {
                        return $user->fields->flatMap->harvests->sum('weight_coton');
                    })->take(3);

                    // Ajouter les résultats pour ce borough
                    $result[] = [
                        'borough' => $borough,
                        'totalWeight' => $totalWeight,
                        'topUsers' => $topUsers
                    ];
                }

                // Logique pour récupérer les utilisateurs
                return self::apiResponse(true, "Utilisateurs récupérés avec succès", $result);
            } catch (\Exception $e) {
                return self::apiResponse(false, "Échec de la récupération des utilisateurs: " . $e->getMessage(), null, 500);
            }
        }


        public function best_comm(Request $request)
        {
            try {
                // Récupérer tous les utilisateurs avec leurs champs et récoltes
                $users = User::with('fields.harvests')->get();

                // Grouper les utilisateurs par borough
                $usersBycommon = $users->groupBy('common');

                // Initialiser le tableau des résultats
                $result = [];

                // Calculer le poids total des récoltes par common et trier les utilisateurs
                foreach ($usersBycommon as $common => $users) {
                    $totalWeight = $users->sum(function ($user) {
                        return $user->fields->flatMap->harvests->sum('weight_coton');
                    });

                    // Trier les utilisateurs par le poids de leurs récoltes
                    $topUsers = $users->sortByDesc(function ($user) {
                        return $user->fields->flatMap->harvests->sum('weight_coton');
                    })->take(3);

                    // Ajouter les résultats pour ce common
                    $result[] = [
                        'common' => $common,
                        'totalWeight' => $totalWeight,
                        'topUsers' => $topUsers
                    ];
                }

                // Logique pour récupérer les utilisateurs
                return self::apiResponse(true, "Utilisateurs récupérés avec succès", $result);
            } catch (\Exception $e) {
                return self::apiResponse(false, "Échec de la récupération des utilisateurs: " . $e->getMessage(), null, 500);
            }
        }

        public function best_depart(Request $request)
        {
            try {
                // Récupérer tous les utilisateurs avec leurs champs et récoltes
                $users = User::with('fields.harvests')->get();

                // Grouper les utilisateurs par borough
                $usersBydepartment = $users->groupBy('department');

                // Initialiser le tableau des résultats
                $result = [];

                // Calculer le poids total des récoltes par department et trier les utilisateurs
                foreach ($usersBydepartment as $department => $users) {
                    $totalWeight = $users->sum(function ($user) {
                        return $user->fields->flatMap->harvests->sum('weight_coton');
                    });

                    // Trier les utilisateurs par le poids de leurs récoltes
                    $topUsers = $users->sortByDesc(function ($user) {
                        return $user->fields->flatMap->harvests->sum('weight_coton');
                    })->take(3);

                    // Ajouter les résultats pour ce department
                    $result[] = [
                        'department' => $department,
                        'totalWeight' => $totalWeight,
                        'topUsers' => $topUsers
                    ];
                }

                // Logique pour récupérer les utilisateurs
                return self::apiResponse(true, "Utilisateurs récupérés avec succès", $result);
            } catch (\Exception $e) {
                return self::apiResponse(false, "Échec de la récupération des utilisateurs: " . $e->getMessage(), null, 500);
            }
        }



        public function best_gv(Request $request)
        {
            try {
                // Récupérer tous les utilisateurs avec leurs champs et récoltes
                $users = User::with('fields.harvests')->get();

                // Grouper les utilisateurs par borough
                $usersByneighborhood = $users->groupBy('neighborhood');

                // Initialiser le tableau des résultats
                $result = [];

                // Calculer le poids total des récoltes par neighborhood et trier les utilisateurs
                foreach ($usersByneighborhood as $neighborhood => $users) {
                    $totalWeight = $users->sum(function ($user) {
                        return $user->fields->flatMap->harvests->sum('weight_coton');
                    });

                    // Trier les utilisateurs par le poids de leurs récoltes
                    $topUsers = $users->sortByDesc(function ($user) {
                        return $user->fields->flatMap->harvests->sum('weight_coton');
                    })->take(3);

                    // Ajouter les résultats pour ce neighborhood
                    $result[] = [
                        'neighborhood' => $neighborhood,
                        'totalWeight' => $totalWeight,
                        'topUsers' => $topUsers
                    ];
                }

                // Logique pour récupérer les utilisateurs
                return self::apiResponse(true, "Utilisateurs récupérés avec succès", $result);
            } catch (\Exception $e) {
                return self::apiResponse(false, "Échec de la récupération des utilisateurs: " . $e->getMessage(), null, 500);
            }
        }


      public function calculateTotalWeightAndTopFarmersNational()
        {
            // Récupérer tous les utilisateurs avec leurs champs et récoltes
            $users = User::with('fields.harvests')->get();

            // Calculer le poids total des récoltes de tous les utilisateurs
            $totalWeightAllUsers = $users->sum(function ($user) {
                return $user->fields->flatMap->harvests->sum('weight_coton');
            });

            // Trier les utilisateurs par le poids de leurs récoltes
            $topFarmersNational = $users->sortByDesc(function ($user) {
                return $user->fields->flatMap->harvests->sum('weight_coton');
            })->take(3);

            return [
                'totalWeightAllUsers' => $totalWeightAllUsers,
                'topFarmersNational' => $topFarmersNational
            ];
        }

        
        public function best_general(Request $request)
        {
            try {
                // Calculer le poids total des récoltes de tous les utilisateurs et obtenir les trois meilleurs utilisateurs au niveau national
                $generalStats = $this->calculateTotalWeightAndTopFarmersNational();

                // Retourner les statistiques générales
                return self::apiResponse(true, "Statistiques générales récupérées avec succès", $generalStats);
            } catch (\Exception $e) {
                return self::apiResponse(false, "Échec de la récupération des statistiques générales: " . $e->getMessage(), null, 500);
            }
        }






    /**
     * Get details of authenticated user.
     */
    public function userDetails(): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response(['data' => $user], 200);
        }

        return response(['data' => 'Unauthorized'], 401);
    }

    /**
     * Logout authenticated user.
     */
    public function logout(): Response
    {

        if (Auth::check()) {
            $user = Auth::user();
            $user->currentAccessToken()->delete();
            return response(['data' => 'User logged out successfully.'], 200);
        }

        return response(['data' => 'Unauthorized'], 401);
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
     * Supprimer un utilisateur
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
