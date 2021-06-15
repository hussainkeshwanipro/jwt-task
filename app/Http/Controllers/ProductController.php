<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    
    public function index(Request $request)
    {
        $token = $request->header('Authorization');
        $user = JWTAuth::authenticate($token);
        if($user)
        {
            $products = Product::all();
            return response()->json(['success' => true,  'products' => $products], 200);
        }
        else
        {
            return response()->json(['error'=>'something went wrong try again'], 200);
        }
    }
    
    public function store(Request $request)
    {
        $token = $request->header('Authorization');
        $user = JWTAuth::authenticate($token);
        if($user)
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'price' => 'required|integer',
                'quantity' => 'required|integer',
                'sku' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 200);
            }

            $product = new Product();
            $product->name = $request->name;
            $product->price = $request->price;
            $product->quantity = $request->quantity;
            $product->sku = $request->sku;
            $product->save();
            if($product->save()) 
            {
                return response()->json(['success'=>true, 'product'=>$product], 200);
            }
            
        }
        else
        {
            return response()->json(['error'=>'something went wrong try again'], 200);
        }
    }

    public function show($id, Request $request)
    {
        $token = $request->header('Authorization');
        $user = JWTAuth::authenticate($token);
        if($user)
        {
            $product = Product::find($id);
            if($product)
            {
                return response()->json(['success'=>true, 'product'=>$product], 200);
            }
            else
            {
                return response()->json(['success'=>false, 'message'=>'Product not found'], 200);
            }
        }
        else
        {
            return response()->json(['error'=>'something went wrong try again'], 200);
        }
    }

    public function destory(Request $request, $id)
    {
        $token = $request->header('Authorization');
        $user = JWTAuth::authenticate($token);
        if($user)
        {
            $product = Product::find($id);
            $product->delete();
            return response()->json(['success'=>true, 'message'=>'product deleted successfully'], 200);
            
        }
        else
        {
            return response()->json(['error'=>'something went wrong try again'], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $token = $request->header('Authorization');
        $user = JWTAuth::authenticate($token);
        if($user)
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'price' => 'required|integer',
                'quantity' => 'required|integer',
                'sku' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 200);
            }
            

            $product = Product::find($id);
            $product->name = $request->name;
            $product->price = $request->price;
            $product->quantity = $request->quantity;
            $product->sku = $request->sku;
            $product->save();
            if($product->save()) 
            {
                return response()->json(['success'=>true, 'message'=>'product updated successfully', 'product'=>$product], 200);
            }
            
        }
        else
        {
            return response()->json(['error'=>'something went wrong try again'], 200);
        }
    }
    
    public function product(Request $request)
    {
        $token = $request->header('Authorization');
        $user = JWTAuth::authenticate($token);
        if($user)
        {
          

            return response()->json([
                'data'=>Product::paginate(2)->items(), 
                'total pages'=>Product::paginate()->total()/2, 
                'total items'=>Product::paginate()->total()
                ]);
        }
        else
        {
            return response()->json(['error'=>'something went wrong try again'], 200);
        }
    }
}
