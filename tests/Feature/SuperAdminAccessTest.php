<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureKikyUser;
use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\EnsurePermissionLike;
use App\Http\Middleware\EnsureRole;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class SuperAdminAccessTest extends TestCase
{
    private function user(string $username): User
    {
        $user = (new User)->forceFill(['id' => 1, 'username' => $username]);
        $user->setRelation('permissions', collect());
        $user->setRelation('roles', collect());
        $user->setRelation('karyawan', (new Karyawan)->forceFill(['nama_lengkap' => 'Staf']));

        return $user;
    }

    public function test_admin_and_kiky_have_identical_access_without_assigned_permissions(): void
    {
        Gate::define('restricted-test', fn () => false);

        foreach (['admin', 'ADMIN', 'kiky', 'KIKY'] as $username) {
            $user = $this->user($username);
            $this->assertTrue($user->isSuperAdmin());
            $this->assertTrue($user->can('master-pelabuhan-view'));
            $this->assertTrue($user->hasPermissionTo('master-pelabuhan-view'));
            $this->assertTrue($user->hasPermissionLike('master-'));
            $this->assertTrue($user->hasPermissionMatch('restricted-test'));
            $this->assertTrue(Gate::forUser($user)->allows('restricted-test'));
            $this->assertTrue(Gate::forUser($user)->allows('unregistered-ability'));
        }
    }

    public function test_all_authorization_middleware_allow_both_accounts(): void
    {
        Route::get('/_test/super-admin', fn () => response('allowed'))->middleware([
            'can:master-pelabuhan-view',
            EnsurePermission::class.':master-pelabuhan-view',
            EnsurePermissionLike::class.':master-',
            EnsureRole::class.':super-admin',
            EnsureKikyUser::class,
        ]);

        foreach (['admin', 'kiky'] as $username) {
            $this->actingAs($this->user($username))->get('/_test/super-admin')->assertOk()->assertSee('allowed');
        }
    }

    public function test_regular_users_do_not_receive_the_bypass(): void
    {
        $user = $this->user('staff');
        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->can('master-pelabuhan-view'));
        $this->assertFalse($user->hasPermissionTo('master-pelabuhan-view'));
        $this->assertFalse($user->hasPermissionLike('master-'));
        $this->assertFalse($user->hasPermissionMatch('restricted-test'));
        $this->assertFalse($user->hasRole('super-admin'));
        $this->assertFalse(Gate::forUser($user)->allows('unregistered-ability'));

        Route::get('/_test/kiky-only', fn () => response('allowed'))->middleware(EnsureKikyUser::class);
        $this->withoutExceptionHandling();
        try {
            $this->actingAs($user)->get('/_test/kiky-only');
            $this->fail('A regular user must be denied access.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }
}
