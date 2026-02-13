<?php

use App\Models\Modality;

test('modality has name', function () {
    $modality = Modality::factory()->create(['name' => 'Yoga']);

    expect($modality->name)->toBe('Yoga');
});

test('modality slug is auto-generated from name', function () {
    $modality = Modality::factory()->create(['name' => 'Power Training']);

    expect($modality->slug)->toBe('power-training');
});

test('modality slug is not overwritten if provided', function () {
    $modality = Modality::factory()->create([
        'name' => 'Power Training',
        'slug' => 'custom-slug',
    ]);

    expect($modality->slug)->toBe('custom-slug');
});

test('modality slug does not update when name changes if slug already exists', function () {
    $modality = Modality::factory()->create(['name' => 'Old Name']);
    expect($modality->slug)->toBe('old-name');

    $modality->update(['name' => 'New Name']);
    // Slug should remain unchanged because it's not empty
    expect($modality->fresh()->slug)->toBe('old-name');
});

test('modality slug updates when name changes if slug is empty', function () {
    $modality = Modality::factory()->create(['name' => 'Old Name']);
    // Slug is auto-generated, not empty
    expect($modality->slug)->toBe('old-name');

    // Manually set slug to empty (simulating a scenario)
    $modality->update(['slug' => '']);
    expect($modality->fresh()->slug)->toBe('');

    // Update name, slug should be auto-generated because it's empty
    $modality->update(['name' => 'New Name']);
    expect($modality->fresh()->slug)->toBe('new-name');
});

test('modality has default active status', function () {
    $modality = Modality::factory()->create();

    expect($modality->is_active)->toBeTrue();
});

test('modality can be deactivated', function () {
    $modality = Modality::factory()->create(['is_active' => false]);

    expect($modality->is_active)->toBeFalse();
});

test('modality has classes relationship', function () {
    $modality = Modality::factory()->create();
    // The relationship is defined but we don't have GymClass factory yet
    // This test ensures the relationship method exists
    expect($modality->classes())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('modality fillable attributes', function () {
    $modality = new Modality();

    expect($modality->getFillable())->toBe([
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'order',
    ]);
});

test('modality casts', function () {
    $modality = new Modality();

    expect($modality->getCasts())->toHaveKeys(['is_active', 'order']);
    expect($modality->getCasts()['is_active'])->toBe('boolean');
    expect($modality->getCasts()['order'])->toBe('integer');
});