<?php

namespace App\Service;

use App\Repositories\DishContentRepository;
use App\Repositories\DishRepository;
use App\Repositories\ManufacturerRepository;
use App\Repositories\NutritionRepository;
use Illuminate\Http\Response;

class DishService
{
    private $dishRepository;
    private $dishContentRepository;
    private $manufacturerRepository;
    private $nutritionRepository;

    public function __construct(
        DishRepository $dishRepository,
        DishContentRepository $dishContentRepository,
        ManufacturerRepository $manufacturerRepository,
        NutritionRepository $nutritionRepository)
    {
        $this->dishRepository = $dishRepository;
        $this->dishContentRepository = $dishContentRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->nutritionRepository = $nutritionRepository;
    }

    public function getDish($id = null)
    {
        if ($id != null) {
            $dish = $this->dishRepository->findById($id);
            if ($dish != null) {
                $manufacturer=$this->manufacturerRepository->findById($dish->manufacturer_id);
                $nutrition=$this->nutritionRepository->findById($dish->nutrition_id);
                $dishContent = $this->dishContentRepository->findByDishId($id)->toArray();
                $dish=$dish->toArray();
                array_splice($dish,3,1);
                $dishContents=array();
                foreach ($dishContent as $item)
                    $dishContents[]=$item['name'];
                $result = array_merge($dish,array('manufacturer_name'=>$manufacturer->name),$nutrition->toArray(), array('contents' => $dishContents));
                return [$result, Response::HTTP_OK];
            } else {
                //'error' => 'The Dish Not Found' (404)
                return [['error' => 'The Dish Not Found'], Response::HTTP_NOT_FOUND];
            }
        } else {
            $dish = $this->dishRepository->all();
            $result=array();
            foreach ($dish as $item){
                $manufacturer=$this->manufacturerRepository->findById($item->manufacturer_id);
                $nutrition=$this->nutritionRepository->findById($item->nutrition_id);
                $dishContent = $this->dishContentRepository->findByDishId($item->id)->toArray();
                $item=$item->toArray();
                array_splice($item,3,1);
                $dishContents=array();
                foreach ($dishContent as $ii)
                    $dishContents[]=$ii['name'];
                $result[] = array_merge($item,array('manufacturer_name'=>$manufacturer->name),$nutrition->toArray(), array('contents' => $dishContents));
            }
            return [$result, Response::HTTP_OK];
        }
    }

}
