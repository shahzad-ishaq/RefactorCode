<?php

namespace DTApi\Repository;

use Validator;
use Illuminate\Database\Eloquent\Model;
use DTApi\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BaseRepository
{
    protected $model;
    protected $validationRules = [];

    public function __construct(Model $model = null)
    {
        $this->model = $model;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function query()
    {
        return $this->model->query();
    }

    public function instance(array $attributes = []): Model
    {
        return new $this->model($attributes);
    }

    public function paginate(int $perPage = null)
    {
        return $this->model->paginate($perPage);
    }

    public function where(string $key, $value)
    {
        return $this->model->where($key, $value);
    }

    public function validator(array $data = [], $rules = null, array $messages = [], array $customAttributes = []): \Illuminate\Validation\Validator
    {
        if ($rules === null) {
            $rules = $this->validationRules;
        }

        return Validator::make($data, $rules, $messages, $customAttributes);
    }

    public function validate(array $data = [], $rules = null, array $messages = [], array $customAttributes = []): bool
    {
        $validator = $this->validator($data, $rules, $messages, $customAttributes);
        return $this->_validate($validator);
    }

    public function create(array $data = []): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data = []): Model
    {
        $instance = $this->findOrFail($id);
        $instance->update($data);
        return $instance;
    }

    public function delete(int $id): Model
    {
        $model = $this->findOrFail($id);
        $model->delete();
        return $model;
    }

    protected function _validate(\Illuminate\Validation\Validator $validator): bool
    {
        if (!empty($attributeNames = $this->validatorAttributeNames())) {
            $validator->setAttributeNames($attributeNames);
        }

        if ($validator->fails()) {
            throw (new ValidationException)->setValidator($validator);
        }

        return true;
    }
}
