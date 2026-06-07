<?php
namespace Tests\Feature;

use App\Models\Department;
use Database\Seeders\DepartmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_sixteen_departments(): void
    {
        $this->seed(DepartmentSeeder::class);
        $this->assertEquals(16, Department::count());
        $this->assertDatabaseHas('departments', ['slug' => 'general-medicine']);
    }
}
