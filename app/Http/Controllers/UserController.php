<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserIndexRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResponse;
use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(UserIndexRequest $request)
    {
        $validated = $request->validated();

        $users = User::query()
            ->filterAndSort($validated)
            ->paginate($validated['per_page'] ?? 15);

        return UserResponse::collection($users);
    }

    public function show(User $user)
    {
        return new UserResponse($user);
    }

    public function store(UserCreateRequest $request)
    {
        $user = User::create($request->validated());

        return (new UserResponse($user))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update($request->validated());

        return new UserResponse($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
