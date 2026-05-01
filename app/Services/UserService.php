<?php

namespace App\Services;

use App\Contracts\Services\UserServiceInterface;
use App\Enums\AccountType;
use App\Models\User;
use App\Support\ApiMessages;
use App\Traits\FileManager;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserService implements UserServiceInterface
{
    use FileManager;

    /**
     * Create a new class instance.
     *
     * @throws Exception
     */
    private Request $request;

    /** @var User */
    private Authenticatable|User $user;

    public function __construct(Request $request, Authenticatable $user)
    {
        $this->request = $request;
        $this->user = $user;
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        $holder = DB::table('users')
            ->whereNot('account_type', AccountType::ADMIN)
            ->paginate($perPage);

        if ($holder->isEmpty()) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        return $holder;
    }

    /**
     * @throws Exception
     */
    public function getAuthUser(): User|JsonResponse|Authenticatable
    {
        $user = $this->request->user();

        if (! $user) {
            throw new Exception(ApiMessages::AUTH_UNAUTHENTICATED);
        }

        return $user;
    }

    /**
     * @throws Exception
     */
    public function upgradeUserAccountType(int|string $id): User
    {
        // prevent admin from execute action on its account
        if (auth()->user()->id === (int) $id) {
            throw new Exception(ApiMessages::ADMIN_ACTION_RESTRICTED);
        }

        $holder = User::find($id);

        if (! $holder) {
            throw new Exception(ApiMessages::USER_NOT_FOUND);
        }

        // check if user is already a vendor
        if ($holder->account_type === AccountType::VENDOR) {
            throw new Exception(ApiMessages::ACCOUNT_ALREADY_VENDOR);
        }

        $holder->update([
            'account_type' => AccountType::VENDOR,
        ]);

        return $holder;
    }

    /**
     * @throws Exception
     */
    public function update(User $user, array $data): User
    {
        $holder = $user->update($data);

        if (! $holder) {
            throw new Exception(ApiMessages::USER_UPDATE_FAILED);
        }

        return $user->refresh();
    }

    /**
     * @throws Exception
     */
    public function destroy(User $user): bool
    {
        // restrict admin to not delete its account
        if ($user->id === auth()->id()) {
            throw new Exception(ApiMessages::ADMIN_ACTION_RESTRICTED);
        }

        $holder = $user->delete();

        if (! $holder) {
            throw new Exception(ApiMessages::USER_DELETE_FAILED);
        }

        return $holder;
    }

    /**
     * @throws Exception
     */
    public function resetPassword(User $user, mixed $newPassword): bool
    {
        $holder = $user->update([
            'password' => $newPassword,
        ]);

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        return $holder;
    }

    /**
     * @throws Exception
     */
    public function updateUserProfileImage(array $file, string $path): array
    {
        $base = config('filesystems.disks.supabase.url_base');
        $path_dir = str_replace($base, '', $this->user->profile);

        // check if user has already old image and column not null
        // and this works only if user has profile already
        if ($this->user->profile) {
            if (Storage::disk('supabase')->exists($path_dir)) {
                Storage::disk('supabase')->delete($path_dir);
            }
        }

        $processUploadImage = $this->upload($file, $path);

        if (! $processUploadImage) {
            throw new Exception(ApiMessages::FAILED_UPDATE_PROFILE_IMAGE);
        }

        $holder = $this->user->update([
            'profile' => $processUploadImage[0],
        ]);

        if (! $holder) {
            throw new Exception(ApiMessages::FAILED_UPDATE_PROFILE_IMAGE);
        }

        return [
            $processUploadImage,
        ];
    }
}
