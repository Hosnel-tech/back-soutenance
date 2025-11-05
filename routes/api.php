<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EpreuveController;
use App\Http\Controllers\Api\TdController;
use App\Http\Controllers\Api\PaiementController;
use App\Http\Controllers\Api\AdminController;

/**
 * @OA\Info(title="TD Manager API", version="1.0.0")
 * @OA\Server(url="/api")
 * @OA\SecurityScheme(
 *   securityScheme="sanctum",
 *   type="apiKey",
 *   in="header",
 *   name="Authorization",
 *   description="Bearer <token>"
 * )
 */

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/me', [AuthController::class, 'updateMe']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Epreuves (enseignants)
    Route::get('/epreuves', [EpreuveController::class, 'index']);
    Route::post('/epreuves', [EpreuveController::class, 'store']);
    Route::get('/epreuves/{epreuve}', [EpreuveController::class, 'show']);
    Route::put('/epreuves/{epreuve}', [EpreuveController::class, 'update']);
    Route::delete('/epreuves/{epreuve}', [EpreuveController::class, 'destroy']);

    // TDs
    Route::get('/tds', [TdController::class, 'index']);
    Route::post('/tds', [TdController::class, 'store']); // admin only via policy
    Route::post('/tds/{td}/terminer', [TdController::class, 'terminer']);
    Route::post('/tds/{td}/payer', [TdController::class, 'marquerPaye']);
    Route::put('/tds/{td}', [TdController::class, 'update']);
    Route::delete('/tds/{td}', [TdController::class, 'destroy']);

    // Teacher stats
    Route::get('/teacher/stats', function (Request $request) {
        $user = $request->user();
        if (!$user->hasRole('enseignant')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $tds = $user->tds()->count();
        $tdsEnCours = $user->tds()->where('statut','en_cours')->count();
        $tdsTermines = $user->tds()->where('statut','termine')->count();
        $tdsPayes = $user->tds()->where('statut','paye')->count();
        $epreuves = $user->epreuves()->count();
        return compact('tds','tdsEnCours','tdsTermines','tdsPayes','epreuves');
    });

    // Paiements
    Route::get('/paiements', [PaiementController::class, 'index']);
    Route::post('/paiements', [PaiementController::class, 'store']);
    Route::get('/paiements/export/pdf', [PaiementController::class, 'exportPdf']);

    // Admin
    Route::get('/admin/enseignants', [AdminController::class, 'enseignants']);
    Route::get('/admin/stats', [AdminController::class, 'stats']);
    Route::post('/admin/enseignants/{user}/valider', [AdminController::class, 'validerEnseignant']);
    Route::delete('/admin/enseignants/{user}', [AdminController::class, 'deleteEnseignant']);
    Route::post('/admin/comptables', [AdminController::class, 'createComptable']);
    Route::put('/admin/profile', [AdminController::class, 'updateProfile']);
});
