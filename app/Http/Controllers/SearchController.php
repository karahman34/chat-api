<?php

namespace App\Http\Controllers;

use App\Helpers\Transformer;
use App\Http\Resources\SearchCollection;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Search peoples.
     *
     * @param   Request  $request
     *
     * @return  JsonResponse
     */
    public function searchPeople(Request $request)
    {
        try {
            $users = User::select('id', 'username', 'avatar', 'last_online')
                            ->where('username', 'like', '%' . $request->input('search') . '%')
                            ->where('username', '!=', Auth::user()->username)
                            ->paginate(10);

            return (new SearchCollection($users))
                        ->additional(
                            Transformer::meta(true, 'Success to search people.')
                        );
        } catch (\Throwable $th) {
            return Transformer::failed('Failed to load peoples.');
        }
    }
}
