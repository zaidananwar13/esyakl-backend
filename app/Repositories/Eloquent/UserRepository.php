<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected User $model;

    /**
     * UserRepository constructor.
     * 
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    /**
     * @inheritDoc
     */
    public function findByToken(string $token)
    {
        return $this->model->where('api_token', $token)->first();
    }

    /**
     * @inheritDoc
     */
    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        $user = new User();
        foreach ($data as $key => $value) {
            $user->{$key} = $value;
        }
        $user->save();
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function update(int $id, array $data): bool
    {
        return $this->model->where('id_user', $id)->update($data) > 0;
    }

    /**
     * Get users by type
     * 
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByType(string $type)
    {
        return $this->model->where('type', $type)->get();
    }

    /**
     * Get user with specific columns
     * 
     * @param int $id
     * @param array $columns
     * @return mixed
     */
    public function findWithColumns(int $id, array $columns = ['*'])
    {
        return $this->model->select($columns)->where('id_user', $id)->first();
    }
}
