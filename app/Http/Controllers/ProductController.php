<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Liste des produits
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::join('users', 'products.user_id', '=', 'users.id')
        ->select('products.id as product_id', 'products.name', 'products.type_product', 'products.quantity', 'products.price_unit', 'products.description', 'products.user_id', 'users.id as user_id', 'users.first_name as user_firstname', 'users.last_name as user_lastname')
        ->get();

        return self::apiResponse(true, "", $products);
    }

    /**
     * Ajouter un produit
     *
     *@bodyParam name string required nom de produit
     * @bodyParam type_product string required type de produit
     * @bodyParam quantity integer required nombre de produit
     * @bodyParam price_unit integer required prix unitaire d'un produit
     * @bodyParam description string required description du produit
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'name' => 'required|string',
            'type_product' => 'required|string',
            'quantity' => 'required|integer',
            'price_unit' => 'required|integer',
            'description' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
        ];

        $request->validate($rules);

        $request ->merge([
            'name'=>  ucfirst(strtolower($request->name)),
            'type_product'=>  ucfirst(strtolower($request->type_product)),
        ] );

        $product = Product::create($request->all());

        if($product) {
            return self::apiResponse(true, "product successfully created.", $product);
        } else {
            return self::apiResponse(false, "product has not been created.", [], 400);
        }
    }

    /**
     * Récupérer les informations d'un produit
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if ($product) {
            return self::apiResponse(true, "", $product);
        } else{
            return self::apiResponse(false, "product not found.", [], 404);
        }
    }

    /**
     * Modifier un produit
     *
     * @bodyParam name string required nom de produit
     * @bodyParam type_product string required type de produit
     * @bodyParam quantity integer required nombre de produit
     * @bodyParam price_unit integer required prix unitaire d'un produit
     * @bodyParam description string required description du produit
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
            'name' => 'required|string',
            'type_product' => 'required|string',
            'quantity' => 'required|integer',
            'price_unit' => 'required|integer',
            'description' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
        ];

        $request->validate($rules);

        $request ->merge([
            'name'=>  ucfirst(strtolower($request->name)),
            'type_product'=>  ucfirst(strtolower($request->type_product)),
        ] );

        $product = Product::find($id);

        if ($product) {
            // Verify if the new product already exists
            $exist_product = Product::where('name', $request->name)->orWhere('type_product', $request->type_product)->first();

            if($exist_product && $exist_product->id != $product->id){
                return self::apiResponse(false, "This product already exists.", [], 400);
            }

            $product->update($request->all());

            return self::apiResponse(true, "product successfully updated.", $product);
        } else{
            return self::apiResponse(false, "product not found.", [], 404);
        }
    }

    /**
     * Supprimer un produit
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if ($product) {

            $product->delete();

            return self::apiResponse(true, "product successfully deleted");
        } else {
            return self::apiResponse(false, "product not found", [], 404);
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
