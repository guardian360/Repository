<?php

namespace Guardian360\Repository\Tests;

use Illuminate\Container\Container as App;
use Guardian360\Repository\Tests\Models\User;
use Guardian360\Repository\Contracts\RepositoryContract;
use Guardian360\Repository\Tests\Specifications\UserIsAdmin;
use Guardian360\Repository\Tests\Repositories\UserRepository;

class UserRepositoryTest extends TestCase
{
    /**
     * @var \Guardian360\Repository\Tests\Repositories\UserRepository
     */
    protected $users;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
        $this->migrateTables();

        $this->users = new UserRepository(new App);
    }

    /** @test */
    public function itCanFetchAllUsers(): void
    {
        $this->assertCount(6, $this->users->all());
    }

    /** @test */
    public function itCanFetchAllUsersBySpecifications(): void
    {
        $users = $this->users->pushSpec(new UserIsAdmin)->all();

        $this->assertCount(2, $users);
    }

    /**
     * @test
     * @depends itCanFetchAllUsersBySpecifications
     */
    public function itCanFlushSpecifications($repository): void
    {
        $users = $this->users->flushSpecs()->all();

        $this->assertCount(6, $users);
    }

    /** @test */
    public function itCanFindAUserByPrimaryKey(): void
    {
        $this->assertInstanceOf(User::class, $this->users->find(1));
    }

    /** @test */
    public function itCanFindAUserByAttribute(): void
    {
        $this->assertInstanceOf(
            User::class,
            $this->users->findBy('name', 'Joe')
        );
    }

    /** @test */
    public function itCanFindAllUsersByAttribute(): void
    {
        $this->assertCount(2, $this->users->findAllBy('role', 'admin'));
        $this->assertCount(6, $this->users->findAllBy('role', ['admin', 'user']));
    }

    /** @test */
    public function itCanPaginateUsers(): void
    {
        $users = $this->users->paginate(5, 2);

        $this->assertCount(1, $users->items());
        $this->assertEquals(2, $users->currentPage());
        $this->assertEquals(6, $users->total());
    }

    /** @test */
    public function itCanAddNewUsersToItsCollection(): void
    {
        $this->users->create([
            'name' => 'James',
            'role' => 'user',
        ]);

        $this->assertCount(7, $this->users->all());
    }

    /** @test */
    public function itCanUpdateExistingUsersById(): void
    {
        $user = User::first();

        $this->assertEquals($user->role, 'user');

        $this->users->update(['role' => 'admin'], $user->id);

        $this->assertEquals($user->fresh()->role, 'admin');
    }

    /** @test */
    public function itCanUpdateExistingUsersByModel(): void
    {
        $user = User::first();

        $this->assertEquals($user->role, 'user');

        $this->users->update(['role' => 'admin'], $user);

        $this->assertEquals($user->fresh()->role, 'admin');
    }

    /** @test */
    public function itCanUpdateOrCreateUsers(): void
    {
        $data = [
            'name' => 'Larry',
            'role' => 'user',
        ];

        $this->assertCount(6, $this->users->all());

        $this->users->updateOrCreate($data);
        $this->users->updateOrCreate($data);

        $this->assertCount(7, $this->users->all());
    }

    /** @test */
    public function itCanRemoveUsersFromItsCollection(): void
    {
        $this->users->delete(1);

        $this->assertCount(5, $this->users->all());

        $this->users->delete(User::first());

        $this->assertCount(4, $this->users->all());
    }
}
