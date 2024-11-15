<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 1); // Default page 1
        $size = $request->query('size', 10);
        $sortBy = $request->query('sortBy', 'title');
        $sortDir = $request->query('sortDir', 'asc');

        $gamesQuery = Game::query();

        // Sorting sesuai parameter
        if ($sortBy === 'popular') {
            $gamesQuery->withCount('scores')->orderBy('scores_count', $sortDir);
        } elseif (in_array($sortBy, ['title', 'uploaddate'])) {
            $gamesQuery->orderBy($sortBy, $sortDir);
        }

        // Pagination
        $games = $gamesQuery->paginate($size, ['*'], 'page', $page);

        return response()->json([
            'page' => $page,
            'size' => $size,
            'totalElements' => $games->total(),
            'content' => $games->items(),
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3|max:60',
            'description' => 'required|max:200'
        ]);

        $slug = Str::slug($request->title);
        
        if (Game::where('slug', $slug)->exists()) {
            return response()->json([
                'status' => 'invalid',
                'slug' => 'Game title already exists'
            ], 400);
        }

        $game = Game::create([
            'slug' => $slug,
            'title' => $request->title,
            'description' => $request->description,
            'author_id' => auth()->id()
        ]);

        return response()->json([
            'status' => 'success',
            'slug' => $game->slug
        ], 201);
    }

    public function show($slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        return response()->json([
            'slug' => $game->slug,
            'title' => $game->title,
            'description' => $game->description,
            'thumbnail' => $game->thumbnail,
            'uploadTimestamp' => $game->upload_timestamp,
            'author' => $game->author->username,
            'scoreCount' => $game->scores()->count(),
            'gamePath' => $game->game_path
        ], 200);
    }

    public function update(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        
        if ($game->author_id !== auth()->id()) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the game author'
            ], 403);
        }
        
        $validatedData = $request->validate([
            'title' => 'required|string|min:4|max:60',
            'description' => 'required|string|min:10|max:200',
        ]);

        $newSlug = Str::slug($validatedData['title']);
        
        if (Game::where('slug', $newSlug)->where('id', '!=', $game->id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Title already in use, please choose a different one'
            ], 422);
        }

        $game->update(array_merge($validatedData, ['slug' => $newSlug]));

        return response()->json([
            'status' => 'success',
            'message' => 'Game updated successfully',
            'slug' => $newSlug
        ], 200);

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function destroy($slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        if ($game->author_id !== auth()->id()) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the game author'
            ], 403);
        }

        // Delete game files
        Storage::deleteDirectory("games/{$game->slug}");
        
        // Delete game and related scores
        $game->scores()->delete();
        $game->delete();

        return response()->json(null, 204);
    }

    public function upload(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        if ($game->author_id !== auth()->id()) {
            return response()->text('User is not author of the game', 403);
        }

        if (!$request->hasFile('zipfile')) {
            return response()->text('No zip file uploaded', 400);
        }

        $zip = new ZipArchive;
        $file = $request->file('zipfile');
        
        if ($zip->open($file->path()) !== true) {
            return response()->text('Invalid zip file', 400);
        }

        $newVersion = ($game->current_version ?? 0) + 1;
        $path = "games/{$game->slug}/{$newVersion}";
        
        // Extract zip to storage
        Storage::makeDirectory($path);
        $zip->extractTo(storage_path("app/public/{$path}"));
        $zip->close();

        // Update game
        $game->update([
            'current_version' => $newVersion,
            'game_path' => "/storage/{$path}",
            'upload_timestamp' => now()
        ]);

        // Check for thumbnail
        if (Storage::exists("{$path}/thumbnail.png")) {
            $game->update([
                'thumbnail' => "/storage/{$path}/thumbnail.png"
            ]);
        }

        return response()->json([
            'status' => 'success',
            'version' => $newVersion
        ]);
    }

    public function getScores($slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        $scores = Score::where('game_id', $game->id)
            ->with('user')
            ->select('user_id', \DB::raw('MAX(score) as score'))
            ->groupBy('user_id')
            ->orderByDesc('score')
            ->get()
            ->map(function ($score) {
                return [
                    'username' => $score->user->username,
                    'score' => $score->score,
                    'timestamp' => $score->created_at
                ];
            });

        return response()->json([
            'scores' => $scores
        ]);
    }

    public function storeScore(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        $request->validate([
            'score' => 'required|integer'
        ]);

        Score::create([
            'game_id' => $game->id,
            'user_id' => auth()->id(),
            'score' => $request->score,
            'version' => $game->current_version ?? '1'
        ]);

        return response()->json([
            'status' => 'success'
        ], 201);
    }
}