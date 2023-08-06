<?php

namespace App\Adapters;

use App\Models\Post;
use App\Exceptions\InvalidFieldException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class PostAdapter implements Adapter{

    /**
     * Default number of posts to be loaded
     */
    const DEFAULT_LIST_AMOUNT = 10;

    /**
     * Get a list of posts, by default 10.
     * @param int $amount Number of posts to be loaded. Set $amount to 0 to retrieve all posts.
     * @param int $offset Ignore $offset first posts.
     * @param string $orderField Name of the field to implements list ordenation
     * @param bool $ascending Sets ordenation ascending or descending
     * @return Collection List of found posts
     * @throws InvalidFieldException
     */
    public static function getList(int $amount = self::DEFAULT_LIST_AMOUNT, int $offset = 0, string $orderField = 'id', bool $ascending = true) : Collection{

        $results = new Collection;
        //Validate all parameters
        $validator = Validator::make(
            [
                "amount" => $amount,
                "offset" => $offset,
                "orderField" => $orderField,
                "ascending" => $ascending,
            ],
            [
                "amount" => ["required","integer","min:0"],
                "offset" => ["required","integer","min:0"],
                "orderField" => ["required","string",Rule::in(\Schema::getColumnListing((new Post)->getTable()))],
                "ascending" => ["required","boolean"]
            ]
        );

        if($validator->fails()){
            throw new InvalidFieldException($validator->messages()->toJson());
        }
               
        //Prepare the query
        $query = Post::query();

        $query->with('author');

        if($amount > 0){
            $query->limit($amount);
        }

        if($offset > 0){
            $query->offset($offset);
        }

        $query->orderBy($orderField, $ascending ? 'asc' : 'desc');

        //Get results
        try{
            $results = $query->get();
        }catch(\Exception $e){
            Log::error($e);
        }

        return $results;
    }

    public static function findById(int $id) : Post{
        return new Post();
    }

    public static function create(array $params) : bool{
        return false;
    }

    /**
     * Get amount of registered posts
     * @return int Number of existing posts, -1 if fails
     */
    public static function getCount() : int{
        $count = -1;
        try{
            $count = Post::count();
        }catch(\Exception $e){
            Log::error($e);
        }
        return $count;
    }
}