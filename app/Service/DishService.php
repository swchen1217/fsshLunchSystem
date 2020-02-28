<?php

namespace App\Service;

use App\Repositories\DishContentRepository;
use App\Repositories\DishRepository;
use App\Repositories\ManufacturerRepository;
use App\Repositories\NutritionRepository;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class DishService
{
    private $dishRepository;
    private $dishContentRepository;
    private $manufacturerRepository;
    private $nutritionRepository;
    private $guzzleHttpClient;

    public function __construct(
        DishRepository $dishRepository,
        DishContentRepository $dishContentRepository,
        ManufacturerRepository $manufacturerRepository,
        NutritionRepository $nutritionRepository,
        Client $client)
    {
        $this->dishRepository = $dishRepository;
        $this->dishContentRepository = $dishContentRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->nutritionRepository = $nutritionRepository;
        $this->guzzleHttpClient = $client;
    }

    public function getDish($id = null)
    {
        if ($id != null) {
            $dish = $this->dishRepository->findById($id);
            if ($dish != null) {
                $manufacturer = $this->manufacturerRepository->findById($dish->manufacturer_id);
                $nutrition = $this->nutritionRepository->findById($dish->nutrition_id)->toArray();
                array_splice($nutrition, 0, 1);
                $dishContent = $this->dishContentRepository->findByDishId($id)->toArray();
                $dish = $dish->toArray();
                array_splice($dish, 3, 1);
                $dishContents = array();
                foreach ($dishContent as $item)
                    $dishContents[] = $item['name'];
                $result = array_merge($dish, array('manufacturer_name' => $manufacturer->name), $nutrition, array('contents' => $dishContents));
                return [$result, Response::HTTP_OK];
            } else {
                //'error' => 'The Dish Not Found' (404)
                return [['error' => 'The Dish Not Found'], Response::HTTP_NOT_FOUND];
            }
        } else {
            $dish = $this->dishRepository->all();
            $result = array();
            foreach ($dish as $item) {
                $manufacturer = $this->manufacturerRepository->findById($item->manufacturer_id);
                $nutrition = $this->nutritionRepository->findById($item->nutrition_id)->toArray();
                array_splice($nutrition, 0, 1);
                $dishContent = $this->dishContentRepository->findByDishId($item->id)->toArray();
                $item = $item->toArray();
                array_splice($item, 3, 1);
                $dishContents = array();
                foreach ($dishContent as $ii)
                    $dishContents[] = $ii['name'];
                $result[] = array_merge($item, array('manufacturer_name' => $manufacturer->name), $nutrition, array('contents' => $dishContents));
            }
            return [$result, Response::HTTP_OK];
        }
    }

    public function newDish(Request $request)
    {
        if (!$request->has('name') || !$request->has('manufacturer_id') || !$request->has('price') || !$request->has('calories') || !$request->has('protein') || !$request->has('fat') || !$request->has('carbohydrate')) {
            return [['error' => 'The request is incomplete'], Response::HTTP_BAD_REQUEST];
        }
        if ($this->manufacturerRepository->findById($request->input('manufacturer_id')) == null) {
            return [['error' => 'The manufacturer_id error'], Response::HTTP_BAD_REQUEST];
        }
        $nutrition_data = $request->only(['calories', 'protein', 'fat', 'carbohydrate']);
        $nutrition = $this->nutritionRepository->caeate($nutrition_data);
        $dish_data = array_merge($request->only(['name', 'manufacturer_id', 'price']), ['nutrition_id' => $nutrition->id, 'photo' => Storage::disk('public')->url('image/dish/default.png')]);
        $dish = $this->dishRepository->caeate($dish_data);
        $contents_data = $request->input('contents');
        foreach ($contents_data as $item)
            $this->dishContentRepository->caeate(['dish_id' => $dish->id, 'name' => $item]);
        return $this->getDish($dish->id);
    }

    public function image(Request $request){
        if(!$request->has('dish_id') || $this->dishRepository->findById($request->input('dish_id'))==null)
            return [['error' => '`dish_id` Not Found'],Response::HTTP_NOT_FOUND];
        $dish_id=$request->input('dish_id');
        if($request->input('type')=='url'){
            $photo_url=$request->input('url');
        }elseif ($request->input('type')=='image'){
            if($request->hasFile('image')){
                $response = $this->guzzleHttpClient->request('POST', 'https://api.imgur.com/3/image', [
                    'headers' => [
                        'authorization' => 'Bearer ' . env('IMGUR_ACCESS_TOKEN'),
                        'content-type' => 'application/x-www-form-urlencoded',
                    ],
                    'form_params' => [
                        'image' => base64_encode(file_get_contents($request->file('image')->path())),
                        'title'=>'dish_'.$dish_id
                    ],
                ]);
                $photo_url=json_decode($response->getBody()->getContents(),true)['data']['link'];
            }else
                return [['error' => 'Image Not Found'],Response::HTTP_BAD_REQUEST0];
        }else{
            return [['error' => 'The type is empty or not supported'],Response::HTTP_BAD_REQUEST0];
        }
        $this->dishRepository->update($dish_id,['photo'=>$photo_url]);
        return [[],Response::HTTP_NO_CONTENT];
    }

    public function editDish(Request $request){
        
    }

    public function removeDish(Request $request){

    }
}
