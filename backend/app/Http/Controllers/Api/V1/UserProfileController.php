<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;
use App\Models\Score;

class UserProfileController extends Controller
{
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Get authored games
        $authoredGames = Game::where('author_id', $user->id)
            ->when(auth()->id() !== $user->id, function ($query) {
                return $query->whereNotNull('current_version');
            })
            ->get()
            ->map(function ($game) {
                return [
                    'slug' => $game->slug,
                    'title' => $game->title,
                    'description' => $game->description
                ];
            });

        // Get highscores
        $highscores = Score::where('user_id', $user->id)
            ->with('game')
            ->select('game_id', \DB::raw('MAX(score) as score'))
            ->groupBy('game_id')
            ->get()
            ->map(function ($score) {
                return [
                    'game' => [
                        'slug' => $score->game->slug,
                        'title' => $score->game->title,
                        'description' => $score->game->description
                    ],
                    'score' => $score->score,
                    'timestamp' => $score->created_at
                ];
            });

        return response()->json([
            'username' => $user->username,
            'registeredTimestamp' => $user->created_at,
            'authoredGames' => $authoredGames,
            'highscores' => $highscores
        ]);
    }
}
