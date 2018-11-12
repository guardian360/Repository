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
    protected function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
        $this->migrateTables();

        $this->users = new UserRepository(new App);
    }

    /** @test */
    public function itCanFetchAllUsers()
    {
        $this->assertCount(6, $this->users->all());
    }

    /** @test */
    public function itCanFetchAllUsersBySpecifications()
    {
        $users = $this->users->pushSpec(new UserIsAdmin)->all();

        $this->assertCount(2, $users);

        return $this->users;
    }

    /**
     * @test
     * @depends itCanFetchAllUsersBySpecifications
     */
    public function itCanFlushSpecifications($repository)
    {
        $users = $this->users->flushSpecs()->all();

        $this->assertCount(6, $users);
    }

    /** @test */
    public function itCanFindAUserByPrimaryKey()
    {
        $this->assertInstanceOf(User::class, $this->users->find(1));
    }

    /** @test */
    public function itCanFindAUserByAttribute()
    {
        $this->assertInstanceOf(
            User::class,
            $this->users->findBy('name', 'Joe')
        );
    }

    /** @test */
    public function itCanFindAllUsersByAttribute()
    {
        $this->assertCount(2, $this->users->findAllBy('role', 'admin'));
        $this->assertCount(6, $this->users->findAllBy('role', ['admin', 'user']));
    }

    /** @test */
    public function itCanPaginateUsers()
    {
        $users = $this->users->paginate(5, 2);

        $this->assertCount(1, $users->items());
        $this->assertEquals(2, $users->currentPage());
        $this->assertEquals(6, $users->total());
    }

    /** @test */
    public function itCanAddNewUsersToItsCollection()
    {
        $this->users->create([
            'name' => 'James',
            'role' => 'user',
        ]);

        $this->assertCount(7, $this->users->all());
    }

    /** @test */
    public function itCanUpdateExistingUsersById()
    {
        $user = User::first();

        $this->assertEquals($user->role, 'user');

        $this->users->update(['role' => 'admin'], $user->id);

        $this->assertEquals($user->fresh()->role, 'admin');
    }

    /** @test */
    public function itCanUpdateExistingUsersByModel()
    {
        $user = User::first();

        $this->assertEquals($user->role, 'user');

        $this->users->update(['role' => 'admin'], $user);

        $this->assertEquals($user->fresh()->role, 'admin');
    }

    /** @test */
    public function itCanRemoveUsersFromItsCollection()
    {
        $this->users->delete(1);

        $this->assertCount(5, $this->users->all());

        $this->users->delete(User::first());

        $this->assertCount(4, $this->users->all());
    }
}
