<?php

namespace Modules\Slider\Repositories\Eloquent;

use Modules\Slider\Repositories\SlideRepository;
use Modules\Core\Icrud\Repositories\Eloquent\EloquentCrudRepository;
use Modules\Slider\Events\SlideWasCreated;

class EloquentSlideRepository extends EloquentCrudRepository implements SlideRepository
{
  /**
   * Filter names to replace
   * @var array
   */
  protected $replaceFilters = [];

  /**
   * Relation names to replace
   * @var array
   */
  protected $replaceSyncModelRelations = [];

  /**
   * Attribute to define default relations
   * all apply to index and show
   * index apply in the getItemsBy
   * show apply in the getItem
   * @var array
   */
  protected $with = [/*'all' => [] */];

  /**
   * Filter query
   *
   * @param $query
   * @param $filter
   * @param $params
   * @return mixed
   */
  public function filterQuery($query, $filter, $params)
  {

    /**
     * Note: Add filter name to replaceFilters attribute before replace it
     *
     * Example filter Query
     * if (isset($filter->status)) $query->where('status', $filter->status);
     *
     */

    //Filter by id
    if (isset($filter->sliderId)) {
      $query->where('slider_id', $filter->sliderId);
    }

    //add filter by search
    if (isset($filter->search)) {
      //find search in columns
      $query->where(function ($query) use ($filter) {
        $query->where('id', 'like', '%' . $filter->search . '%')
          ->orWhere('updated_at', 'like', '%' . $filter->search . '%')
          ->orWhere('created_at', 'like', '%' . $filter->search . '%');
      });
    }

    // ORDER
    if (isset($params->order) && $params->order) {

      $order = is_array($params->order) ? $params->order : [$params->order];

      foreach ($order as $orderObject) {
        if (isset($orderObject->field) && isset($orderObject->way)) {
          if (in_array($orderObject->field, $this->model->translatedAttributes)) {
            $query->join('slider__slide_translations as translations', 'translations.slide_id', '=', 'slider__slides.id');
            $query->orderBy("translations.$orderObject->field", $orderObject->way);
          } else
            $query->orderBy($orderObject->field, $orderObject->way);
        }

      }
    } else {
      //Order by "Sort order"
      if (!isset($filter->search) && !isset($params->filter->order) && (!isset($params->filter->noSortOrder) || !$params->filter->noSortOrder)) {
        $query->orderBy('position', 'asc');//Add order to query
      }
    }

    if (isset($params->setting) && isset($params->setting->fromAdmin) && $params->setting->fromAdmin) {

    } else {
      //Pre filters by default
      $this->defaultPreFilters($query, $params);
    }

    //Response
    return $query;
  }


  public function defaultPreFilters($query, $params)
  {

    //pre-filter status
    $query->whereRaw("id IN (SELECT slide_id from slider__slide_translations where active = 1)");


  }
  /**
   * Method to sync Model Relations
   *
   * @param $model ,$data
   * @return $model
   */
  public function syncModelRelations($model, $data)
  {
    //Get model relations data from attribute of model
    $modelRelationsData = ($model->modelRelations ?? []);

    /**
     * Note: Add relation name to replaceSyncModelRelations attribute before replace it
     *
     * Example to sync relations
     * if (array_key_exists(<relationName>, $data)){
     *    $model->setRelation(<relationName>, $model-><relationName>()->sync($data[<relationName>]));
     * }
     *
     */

    //Response
    return $model;
  }


  public function create($data)
  {
    return parent::create($data); // TODO: Change the autogenerated stub

    event(new SlideWasCreated($slide, $data));
  }
}
