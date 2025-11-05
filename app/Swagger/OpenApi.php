<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="TD Manager API",
 *     version="1.0.0",
 *     description="API pour la gestion des TD (Enseignants, Admins, Comptables)"
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API server"
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="sanctum",
 *   type="apiKey",
 *   in="header",
 *   name="Authorization",
 *   description="Utiliser: Bearer {token}"
 * )
 */
class OpenApi {}
