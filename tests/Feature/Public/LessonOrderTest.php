<?php

use App\Models\Content\Lesson;
use App\Models\System\Level;
use App\Models\User;

it('lists lessons grouped by level progression, not interleaved by raw order_number', function () {
    $a1 = Level::factory()->create(['code' => 'A1']);
    $b1 = Level::factory()->create(['code' => 'B1']);

    // Same order_number (1) in two different levels used to sort next to
    // each other because the query only ordered by order_number — this
    // locks in that A1 lessons now come as a whole block before B1.
    Lesson::factory()->create(['level_id' => $b1->id, 'order_number' => 1, 'title' => 'B1 Lesson One', 'is_published' => true]);
    Lesson::factory()->create(['level_id' => $a1->id, 'order_number' => 1, 'title' => 'A1 Lesson One', 'is_published' => true]);
    Lesson::factory()->create(['level_id' => $a1->id, 'order_number' => 2, 'title' => 'A1 Lesson Two', 'is_published' => true]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/lessons');
    $response->assertOk();

    $body = $response->getContent();
    $posA1First = strpos($body, 'A1 Lesson One');
    $posA1Second = strpos($body, 'A1 Lesson Two');
    $posB1First = strpos($body, 'B1 Lesson One');

    expect($posA1First)->not->toBeFalse();
    expect($posA1Second)->not->toBeFalse();
    expect($posB1First)->not->toBeFalse();
    expect($posA1First)->toBeLessThan($posA1Second);
    expect($posA1Second)->toBeLessThan($posB1First);
});
