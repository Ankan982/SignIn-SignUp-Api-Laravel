<?php

namespace App\Services;


use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class ProductService
{
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
    /**
     * Find All User List
     * @return array
     */
    public function findAll($where = [], $limit = 0, $attributes = [])
    {
        $query_data = $this->product;

        // search in Product name & description field
        if (array_key_exists('search', $attributes) && !empty($attributes['search'])) {

            $query_data = $query_data->where(function ($query) use ($attributes) {

                $query->where('product_name', "LIKE", '%' . $attributes['search'] . '%')

                    ->orWhere('product_description', "LIKE", '%' . $attributes['search'] . '%');
            });
        }

        if ( array_key_exists('sort_by',  $attributes) && array_key_exists('order',  $attributes)
             && !empty($attributes['order']) && !empty($attributes['sort_by'])) {

        //sorting by product price
        if ($attributes['sort_by'] == 'product_price') {
            $query_data = $query_data->orderBy('product_price', $attributes['order']);
        } 
         //sorting by product quantity
        else if ($attributes['sort_by'] == 'product_quantity') {
            $query_data = $query_data->orderBy('product_quantity', $attributes['order']);
        }
    }

        

        if (!$limit) {
            $query_data =  $query_data->get();
        } else {
            //dd($query_data->toSql());
            $query_data =  $query_data->paginate($limit);
        }

        return $query_data;
      
    }
    /**
     * Find User Details
     * @return array
     */
    public function findOne($id)
    {
        //return DB::table('users')->where('id', $id)->first();
        return $this->user::with('projects', 'projects.projectDetails')->where('id', $id)->first();
    }
    /**
     * Update User Details
     * @return array
     */
    public function update($attributes, $id)
    {
        $set_users_data = Arr::except($attributes, ['_token', '_method']);
        $user = $this->user->find($id);
        return $user->update($set_users_data);
    }
     /**
     * Update password
     * @return array
     */
    public function updatePassword($password, $email)
    {
        $user = $this->user->where('email',$email);
        return $user->update(['password'=>$password]);
    }
     /**
     * Create User password
     * @return object
     */
   
    public function create($attributes)
    {
        $attributes = Arr::except($attributes, ['_token', '_method']);
        return $this->user->create($attributes);
    }
    /**
     * Find User Details By Email
     * @return object
     */
    public function userDetailsByEmail($email)
    {
        return $this->user::where('email', $email)->first();
    }

   
}
