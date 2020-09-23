<?php

namespace App\Service;

use App\Exceptions\MyException;
use App\Repositories\DishContentRepository;
use App\Repositories\DishRepository;
use App\Repositories\ManufacturerRepository;
use App\Repositories\NutritionRepository;
use App\Repositories\RatingRepositiry;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DishService
{
    private $dishRepository;
    private $dishContentRepository;
    private $manufacturerRepository;
    private $nutritionRepository;
    private $guzzleHttpClient;
    private $ratingRepositiry;

    public function __construct(
        DishRepository $dishRepository,
        DishContentRepository $dishContentRepository,
        ManufacturerRepository $manufacturerRepository,
        NutritionRepository $nutritionRepository,
        Client $client,
        RatingRepositiry $ratingRepositiry)
    {
        $this->dishRepository = $dishRepository;
        $this->dishContentRepository = $dishContentRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->nutritionRepository = $nutritionRepository;
        $this->guzzleHttpClient = $client;
        $this->ratingRepositiry = $ratingRepositiry;
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
                $result = array_merge($dish, array('manufacturer_name' => $manufacturer->name), $nutrition, array('contents' => $dishContents, 'rating' => $this->ratingRepositiry->getAverageByDishId($id)));
                return [$result, Response::HTTP_OK];
            } else {
                //'error' => 'The Dish Not Found' (404)
                return [['error' => 'The Dish Not Found'], Response::HTTP_NOT_FOUND];
            }
        } else {
            $dish = $this->dishRepository->all();
            $result = array();
            foreach ($dish as $item) {
                $id = $item->id;
                $manufacturer = $this->manufacturerRepository->findById($item->manufacturer_id);
                $nutrition = $this->nutritionRepository->findById($item->nutrition_id)->toArray();
                array_splice($nutrition, 0, 1);
                $dishContent = $this->dishContentRepository->findByDishId($id)->toArray();
                $item = $item->toArray();
                array_splice($item, 3, 1);
                $dishContents = array();
                foreach ($dishContent as $ii)
                    $dishContents[] = $ii['name'];
                $result[] = array_merge($item, array('manufacturer_name' => $manufacturer->name), $nutrition, array('contents' => $dishContents, 'rating' => $this->ratingRepositiry->getAverageByDishId($id)));
            }
            return [$result, Response::HTTP_OK];
        }
    }

    public function newDish(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->has('name') || !$request->has('manufacturer_id') || !$request->has('price') || !$request->has('calories') || !$request->has('protein') || !$request->has('fat') || !$request->has('carbohydrate')) {
                throw new MyException(serialize(['error' => 'The request is incomplete']), Response::HTTP_BAD_REQUEST);
            }
            if ($this->manufacturerRepository->findById($request->input('manufacturer_id')) == null) {
                throw new MyException(serialize(['error' => 'The manufacturer_id error']), Response::HTTP_BAD_REQUEST);
            }
            if ($request->input('price') < 0)
                return [['error' => '`price` must unsigned'], Response::HTTP_BAD_REQUEST];
            $nutrition_data = $request->only(['calories', 'protein', 'fat', 'carbohydrate']);
            $nutrition = $this->nutritionRepository->create($nutrition_data);
            $dish_data = array_merge($request->only(['name', 'manufacturer_id', 'price']), ['nutrition_id' => $nutrition->id, 'photo' => Storage::disk('public')->url('image/dish/default.png')]);
            $dish = $this->dishRepository->create($dish_data);
            $contents_data = $request->input('contents');
            foreach ($contents_data as $item)
                $this->dishContentRepository->create(['dish_id' => $dish->id, 'name' => $item]);
            DB::commit();
            return [$this->getDish($dish->id)[0], Response::HTTP_CREATED];
        } catch (MyException $e) {
            DB::rollback();
            return [unserialize($e->getMessage()), $e->getCode()];
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function image(Request $request, $dish_id)
    {
        Cache::tags('sale')->flush();
        if ($request->input('type') == 'url') {
            $photo_url = $request->input('url');
        } elseif ($request->input('type') == 'image') {
            if ($request->hasFile('image')) {
                $response = $this->guzzleHttpClient->request('POST', 'https://api.imgur.com/3/image', [
                    'headers' => [
                        'authorization' => 'Bearer ' . env('IMGUR_ACCESS_TOKEN'),
                        //'authorization' => 'Client-ID 604fafdd7bce440',
                        'content-type' => 'application/x-www-form-urlencoded',
                    ],
                    'form_params' => [
                        'image' => base64_encode(file_get_contents($request->file('image')->path())),
                        'title' => 'dish_' . $dish_id
                    ],
                ]);
                $photo_url = json_decode($response->getBody()->getContents(), true)['data']['link'];
            } else
                return [['error' => 'Image Not Found'], Response::HTTP_BAD_REQUEST];
        } else {
            return [['error' => 'The type is empty or not supported'], Response::HTTP_BAD_REQUEST];
        }
        $this->dishRepository->update($dish_id, ['photo' => $photo_url]);
        return [[], Response::HTTP_NO_CONTENT];
    }

    public function editDish(Request $request, $dish_id)
    {
        Cache::tags('sale')->flush();
        DB::beginTransaction();
        try {
            $dish = $this->dishRepository->findById($dish_id);
            if ($dish == null)
                throw new MyException(serialize(['error' => 'The Dish Not Found']), Response::HTTP_NOT_FOUND);
            if ($request->has('price') && $request->input('price') < 0)
                return [['error' => '`price` must unsigned'], Response::HTTP_BAD_REQUEST];
            foreach ($request->all() as $key => $value) {
                $data = [$key => $value];
                if ($key == "name" || $key == "manufacturer_id" || $key == "price" || $key == "photo") {
                    $this->dishRepository->update($dish_id, $data);
                } elseif ($key == "calories" || $key == "protein" || $key == "fat" || $key == "carbohydrate") {
                    $this->nutritionRepository->update($dish->nutrition_id, $data);
                } elseif ($key == "contents") {
                    $this->dishContentRepository->deleteByDishId($dish_id);
                    foreach ($value as $item)
                        $this->dishContentRepository->create(['dish_id' => $dish_id, 'name' => $item]);
                } else {
                    throw new MyException(serialize(['error' => 'The attribute cannot be modified']), Response::HTTP_BAD_REQUEST);
                }
            }
            DB::commit();
            return $this->getDish($dish_id);
        } catch (MyException $e) {
            DB::rollback();
            return [unserialize($e->getMessage()), $e->getCode()];
        } catch (\Exception $e) {
            DB::rollback();
            return [['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR];
        }
    }

    public function removeDish(Request $request, $dish_id)
    {
        Cache::tags('sale')->flush();
        $remove = $this->dishRepository->delete($dish_id);
        if ($remove != 0)
            return [[], Response::HTTP_NO_CONTENT];
        else
            return [['error' => 'The Dish Not Found'], Response::HTTP_NOT_FOUND];
    }
}
