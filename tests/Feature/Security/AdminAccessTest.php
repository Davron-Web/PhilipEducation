<?php

use App\Models\User;
use App\Models\User\Role;
use Illuminate\Support\Facades\Route;

function accessAdmin(): User
{
    return User::factory()->create([
        'role_id' => Role::factory()->admin()->create()->id,
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
}

function plainStudent(): User
{
    return User::factory()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
}

/** Все GET-маршруты админки без параметров в адресе. */
function adminGetRoutes(): array
{
    return collect(Route::getRoutes())
        ->filter(fn ($route) => str_starts_with($route->uri(), 'admin')
            && in_array('GET', $route->methods(), true)
            && ! str_contains($route->uri(), '{'))
        ->map(fn ($route) => '/'.$route->uri())
        ->unique()
        ->values()
        ->all();
}

it('каждый маршрут админки закрыт middleware', function () {
    $unprotected = [];

    foreach (Route::getRoutes() as $route) {
        if (! str_starts_with($route->uri(), 'admin')) {
            continue;
        }

        $middleware = implode(',', $route->gatherMiddleware());

        // Одного auth мало: он лишь требует входа, а роль проверяет
        // RoleMiddleware. Нужны оба.
        if (! str_contains($middleware, 'auth') || ! str_contains($middleware, 'role:')) {
            $unprotected[] = $route->uri();
        }
    }

    expect($unprotected)->toBe([]);
});

it('не пускает гостя ни на один экран админки', function () {
    $routes = adminGetRoutes();

    expect($routes)->not->toBeEmpty();

    foreach ($routes as $url) {
        // Гостя auth отправляет на форму входа, роль проверяется уже после.
        $this->get($url)->assertRedirect('/login');
    }
});

it('не пускает обычного ученика по прямой ссылке', function () {
    $student = plainStudent();

    // Проверяем каждый адрес, а не полагаемся на то, что ссылок нет в меню.
    foreach (adminGetRoutes() as $url) {
        $this->actingAs($student)->get($url)->assertStatus(403);
    }
});

it('пускает администратора', function () {
    $admin = accessAdmin();

    $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
});

it('не пускает ученика в изменяющие маршруты админки', function () {
    $student = plainStudent();
    $lesson = App\Models\Content\Lesson::factory()->create();
    $title = App\Models\Gamification\Title::create([
        'code' => 'target', 'name' => 'Цель', 'min_xp' => 0, 'is_active' => true,
    ]);

    $this->actingAs($student)->post('/admin/content/lesson-videos', [
        'lesson_id' => $lesson->id, 'title' => 'взлом', 'url' => 'https://youtu.be/xxxxxxxxxxx',
    ])->assertStatus(403);

    // Существующая запись: иначе 404 от привязки модели скрыл бы, проверяется
    // ли роль вообще.
    $this->actingAs($student)->delete("/admin/gamification/titles/{$title->id}")->assertStatus(403);

    expect(App\Models\Gamification\Title::find($title->id))->not->toBeNull()
        ->and(App\Models\Content\LessonVideo::count())->toBe(0);
});

it('не даёт присвоить себе роль через форму регистрации', function () {
    $adminRole = Role::factory()->admin()->create();
    Role::factory()->student()->create();

    $this->post('/register', [
        'name' => 'Хитрый',
        'email' => 'sneaky.role@gmail.com',
        'password' => 'Tr0ubad0ur-x91k',
        'password_confirmation' => 'Tr0ubad0ur-x91k',
        // Поля, которых в форме нет, но которые есть в $fillable модели.
        'role_id' => $adminRole->id,
        'is_active' => true,
        'points' => 999999,
    ])->assertRedirect();

    $user = User::where('email', 'sneaky.role@gmail.com')->firstOrFail();

    // Контроллер собирает массив полей вручную, поэтому лишнее из запроса
    // до модели не доходит.
    expect($user->role->name)->toBe('student')
        ->and($user->isAdmin())->toBeFalse()
        ->and((int) $user->points)->toBe(0);
});

it('не даёт сменить роль через настройки профиля', function () {
    $student = plainStudent();
    $adminRole = Role::factory()->admin()->create();

    $this->actingAs($student)->put('/profiles', [
        'name' => 'Обычное имя',
        'email' => $student->email,
        'role_id' => $adminRole->id,
        'points' => 100000,
    ]);

    $student->refresh();

    expect($student->role->name)->toBe('student')
        ->and((int) $student->points)->not->toBe(100000);
});

it('ставит заголовки безопасности на каждый ответ', function () {
    $response = $this->get('/login');

    expect($response->headers->get('X-Frame-Options'))->toBe('SAMEORIGIN')
        ->and($response->headers->get('X-Content-Type-Options'))->toBe('nosniff')
        ->and($response->headers->get('Referrer-Policy'))->toBe('strict-origin-when-cross-origin');
});
