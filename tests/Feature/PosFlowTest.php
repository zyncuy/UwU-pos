<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;

function kasirUser(): User
{
    $role = Role::firstOrCreate(['name' => 'Kasir']);

    return User::factory()->create(['role_id' => $role->id]);
}

it('can create category, product and a transaction', function () {
    $user = kasirUser();
    $this->actingAs($user);

    $this->post(route('categories.store'), ['name' => 'Minuman'])->assertRedirect();
    $cat = Category::first();

    $this->post(route('products.store'), [
        'name' => 'Es Teh',
        'price' => 5000,
        'stock' => 10,
        'category_id' => $cat->id,
    ])->assertRedirect();

    $product = Product::first();

    $this->post(route('transactions.store'), [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
        'pay_amount' => 10000,
    ])->assertRedirect(route('transactions.index'));

    $this->assertDatabaseHas('transactions', [
        'total_price' => 10000,
        'pay_amount' => 10000,
        'change_amount' => 0,
        'user_id' => $user->id,
    ]);
    $this->assertSame(8, $product->fresh()->stock);
    expect(Transaction::first()->details()->count())->toBe(1);

    $this->delete(route('transactions.destroy', Transaction::first()->id))->assertRedirect();
    $this->assertSame(10, $product->fresh()->stock);
});

it('rejects payment below total', function () {
    $user = kasirUser();
    $this->actingAs($user);

    $product = Product::create([
        'user_id' => $user->id,
        'name' => 'A',
        'price' => 5000,
        'stock' => 5,
    ]);

    $this->post(route('transactions.store'), [
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
        'pay_amount' => 1000,
    ])->assertSessionHas('error');

    $this->assertSame(5, $product->fresh()->stock);
    $this->assertDatabaseCount('transactions', 0);
});

it('rejects overselling stock', function () {
    $user = kasirUser();
    $this->actingAs($user);

    $product = Product::create([
        'user_id' => $user->id,
        'name' => 'A',
        'price' => 5000,
        'stock' => 1,
    ]);

    $this->post(route('transactions.store'), [
        'items' => [['product_id' => $product->id, 'quantity' => 3]],
        'pay_amount' => 999999,
    ])->assertSessionHas('error');

    $this->assertSame(1, $product->fresh()->stock);
    $this->assertDatabaseCount('transactions', 0);
});

it('blocks a user from editing another users category', function () {
    $owner = User::factory()->create();
    $attacker = kasirUser();
    $category = Category::create(['user_id' => $owner->id, 'name' => 'Rahasia']);

    $this->actingAs($attacker)
        ->put(route('categories.update', $category->id), ['name' => 'Hacked'])
        ->assertForbidden();

    $this->assertSame('Rahasia', $category->fresh()->name);
});
