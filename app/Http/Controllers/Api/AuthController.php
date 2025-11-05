<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *  path="/auth/register",
     *  tags={"auth"},
     *  summary="Inscription publique enseignant (compte en attente de validation)",
     *  @OA\RequestBody(required=true,
     *    @OA\JsonContent(
     *      required={"name","email","password","password_confirmation"},
     *      @OA\Property(property="name", type="string"),
     *      @OA\Property(property="email", type="string"),
     *      @OA\Property(property="password", type="string"),
     *      @OA\Property(property="password_confirmation", type="string"),
     *      @OA\Property(property="phone", type="string"),
     *      @OA\Property(property="establishment", type="string"),
     *      @OA\Property(property="subject", type="string"),
     *      @OA\Property(property="classe", type="string"),
     *      @OA\Property(property="ifru", type="string")
     *    )
     *  ),
     *  @OA\Response(response=201, description="Compte créé en attente de validation",
     *    @OA\JsonContent(
     *      @OA\Property(property="message", type="string"),
     *      @OA\Property(property="status", type="string", example="PENDING_VALIDATION")
     *    )
     *  )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
    // Rôle forcé côté serveur pour éviter élévation via payload
    $role = 'enseignant';
    $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'is_validated' => $role !== 'enseignant',
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account' => $data['bank_account'] ?? null,
            'phone' => $data['phone'] ?? null,
            'establishment' => $data['establishment'] ?? null,
            'subject' => $data['subject'] ?? null,
            'classe' => $data['classe'] ?? null,
            'experience_years' => $data['experience_years'] ?? null,
            'ifru' => $data['ifru'] ?? null,
        ]);
        // Assignation du rôle si existant (après seeding) sinon on ignore pour éviter une 500
        try {
            if (\Spatie\Permission\Models\Role::where('name', $role)->exists()) {
                $user->assignRole($role);
            }
        } catch (\Throwable $e) {
            // Log possible mais ne pas bloquer l'inscription
            Log::warning('Assignation rôle échouée: '.$e->getMessage());
        }
        return response()->json([
            'message' => 'Compte créé. En attente de validation par un administrateur.',
            'status' => 'PENDING_VALIDATION'
        ], 201);
    }

    /**
     * @OA\Post(
     *  path="/auth/login",
     *  tags={"auth"},
     *  @OA\RequestBody(required=true,
     *    @OA\JsonContent(
     *      required={"email","password"},
     *      @OA\Property(property="email", type="string"),
     *      @OA\Property(property="password", type="string")
     *    )
     *  ),
     *  @OA\Response(response=200, description="ok")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants invalides.'],
            ]);
        }

        if ($user->role === 'enseignant' && !$user->is_validated) {
            return response()->json(['message' => 'Compte enseignant non validé', 'code' => 'TEACHER_NOT_VALIDATED'], 403);
        }

        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }

    /**
     * @OA\Get(path="/auth/me", tags={"auth"}, security={{"sanctum":{}}}, @OA\Response(response=200, description="ok"))
     */
    public function me(Request $request)
    {
        return $request->user();
    }

    public function updateMe(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $data = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'email' => ['sometimes','email','max:255','unique:users,email,'.$user->id],
            'password' => ['sometimes','string','min:8'],
            'phone' => ['sometimes','nullable','string','max:30'],
            'establishment' => ['sometimes','nullable','string','max:255'],
            'subject' => ['sometimes','nullable','string','max:255'],
            'experience_years' => ['sometimes','nullable','integer','min:0','max:80'],
            'settings' => ['sometimes','array'],
            'settings.notifications' => ['sometimes','array'],
            'settings.notifications.email' => ['sometimes','boolean'],
            'settings.notifications.push' => ['sometimes','boolean'],
            'settings.notifications.sms' => ['sometimes','boolean'],
            'settings.display' => ['sometimes','array'],
            'settings.display.theme' => ['sometimes','string','max:20'],
            'settings.display.lang' => ['sometimes','string','max:10'],
            'settings.display.tz' => ['sometimes','string','max:64'],
        ]);
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        // fusion settings json
        if (isset($data['settings'])) {
            $data['settings'] = array_merge($user->settings ?? [], $data['settings']);
        }
        $user->update($data);
        return $user->refresh();
    }

    /**
     * @OA\Post(path="/auth/logout", tags={"auth"}, security={{"sanctum":{}}}, @OA\Response(response=204, description="logout"))
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->noContent();
    }
}
