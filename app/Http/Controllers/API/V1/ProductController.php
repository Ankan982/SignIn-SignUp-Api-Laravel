<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    protected  $userService;

    public function __construct(ProductService $productService)
    {

        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        try{
            $inputs = $request->all();
            $limit = 5;
        $product_details = $this->productService->findAll([], $limit, $inputs);
        $response = [
            'status' => 200,
            'message' => 'All Product details',
            'data' => $product_details,
            'errors' => [],
            'meta' => new \stdClass(),
            'info' => new \stdClass(),

        ];
        return response()->json($response, $response['status']);
        }
        catch (Exception $e) {
        
            $response = [
                'status' => 500,
                'message' => 'Something went wrong.',
                'data' => new \stdClass(),
                'errors' => [
                    [
                        'field' => 'error',
                        'message' => [$e->getMessage()],
                    ],
                ],
                'meta' => new \stdClass(),
                'info' => new \stdClass(),

            ];
            return response()->json($response, $response['status']);
        }
}    

}
