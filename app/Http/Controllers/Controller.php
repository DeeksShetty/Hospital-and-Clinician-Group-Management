<?php

namespace App\Http\Controllers;
use App\Http\Traits\ResponseTrait;

/**
 * @OA\Info(
 *     title="Hospital and Clinician Group Management API",
 *     version="1.0.0",
 *     description="API documentation for Hospital and Clinician Group Management assessment by EG"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use the token returned from /api/login as: 'Authorization: Bearer {token}'"
 * )
 **/
abstract class Controller
{
     use ResponseTrait;
}
