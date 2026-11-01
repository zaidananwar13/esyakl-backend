<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    /**
     * Find user by ID
     * 
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    /**
     * Find user by API token
     * 
     * @param string $token
     * @return mixed
     */
    public function findByToken(string $token);

    /**
     * Find user by email
     * 
     * @param string $email
     * @return mixed
     */
    public function findByEmail(string $email);

    /**
     * Create a new user
     * 
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update user
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;
}
