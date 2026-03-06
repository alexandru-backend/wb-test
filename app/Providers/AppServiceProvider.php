<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route; 

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
      // Regista as rotas da API manualmente
      Route::prefix('api')
          ->middleware('api')
          ->group(base_path('routes/api.php'));
          
      // Regista as rotas Web (se necessário, embora o Laravel costume carregar sozinho)
      Route::middleware('web')
          ->group(base_path('routes/web.php'));
    }
}