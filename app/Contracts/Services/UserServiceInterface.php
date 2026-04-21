<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function paginate(int $perPage = 20): LengthAwarePaginator;

    public function getAuthUser(): User|JsonResponse|Authenticatable;

    public function upgradeUserAccountType(int|string $id): User;

    public function update(User $user, array $data): User;

    public function destroy(User $user): bool;
}
