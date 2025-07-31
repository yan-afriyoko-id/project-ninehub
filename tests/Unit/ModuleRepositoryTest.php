<?php

namespace Tests\Unit;

use App\Models\Module;
use App\Repositories\ModuleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModuleRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private ModuleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ModuleRepository(new Module());
    }

    /**
     * Test getting all modules
     */
    public function test_get_all_modules(): void
    {
        Module::factory()->count(3)->create();

        $modules = $this->repository->all();

        $this->assertEquals(3, $modules->count());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $modules);
    }

    /**
     * Test finding module by ID
     */
    public function test_find_module_by_id(): void
    {
        $module = Module::factory()->create();

        $foundModule = $this->repository->find($module->id);
        $this->assertNotNull($foundModule);
        $this->assertEquals($module->id, $foundModule->id);

        $notFoundModule = $this->repository->find(999);
        $this->assertNull($notFoundModule);
    }

    /**
     * Test finding module by ID or fail
     */
    public function test_find_or_fail_module(): void
    {
        $module = Module::factory()->create();

        $foundModule = $this->repository->findOrFail($module->id);
        $this->assertEquals($module->id, $foundModule->id);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->repository->findOrFail(999);
    }

    /**
     * Test creating module
     */
    public function test_create_module(): void
    {
        $moduleData = [
            'name' => 'Test Module',
            'slug' => 'test-module',
            'description' => 'Test description',
            'icon' => 'test-icon',
            'route' => 'test.route',
            'order' => 1,
            'is_active' => true,
            'is_public' => false,
            'permissions' => ['view', 'create', 'edit'],
        ];

        $module = $this->repository->create($moduleData);

        $this->assertNotNull($module);
        $this->assertEquals('Test Module', $module->name);
        $this->assertEquals('test-module', $module->slug);
        $this->assertTrue($module->is_active);
        $this->assertFalse($module->is_public);
    }

    /**
     * Test updating module
     */
    public function test_update_module(): void
    {
        $module = Module::factory()->create();

        $updateData = [
            'name' => 'Updated Module',
            'description' => 'Updated description',
            'is_active' => false,
        ];

        $updatedModule = $this->repository->update($module->id, $updateData);

        $this->assertEquals('Updated Module', $updatedModule->name);
        $this->assertEquals('Updated description', $updatedModule->description);
        $this->assertFalse($updatedModule->is_active);
    }

    /**
     * Test deleting module
     */
    public function test_delete_module(): void
    {
        $module = Module::factory()->create();

        $result = $this->repository->delete($module->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('modules', ['id' => $module->id]);
    }

    /**
     * Test getting active modules
     */
    public function test_get_active_modules(): void
    {
        Module::factory()->count(3)->active()->create();
        Module::factory()->count(2)->inactive()->create();

        $activeModules = $this->repository->getActiveModules();
        $this->assertEquals(3, $activeModules->count());
    }

    /**
     * Test getting public modules
     */
    public function test_get_public_modules(): void
    {
        Module::factory()->count(2)->public()->create();
        Module::factory()->count(3)->private()->create();

        $publicModules = $this->repository->getPublicModules();
        $this->assertEquals(2, $publicModules->count());
    }

    /**
     * Test getting modules by order
     */
    public function test_get_modules_by_order(): void
    {
        Module::factory()->create(['order' => 3]);
        Module::factory()->create(['order' => 1]);
        Module::factory()->create(['order' => 2]);

        $orderedModules = $this->repository->getModulesByOrder();
        $this->assertEquals(1, $orderedModules->first()->order);
        $this->assertEquals(3, $orderedModules->last()->order);
    }

    /**
     * Test searching modules
     */
    public function test_search_modules(): void
    {
        Module::factory()->create([
            'name' => 'Test Module',
            'slug' => 'test-module',
            'description' => 'Test description',
        ]);
        Module::factory()->create([
            'name' => 'Another Module',
            'slug' => 'another-module',
            'description' => 'Another description',
        ]);

        $searchResults = $this->repository->searchModules('Test');
        $this->assertEquals(1, $searchResults->count());
        $this->assertEquals('Test Module', $searchResults->first()->name);
    }

    /**
     * Test getting modules by slugs
     */
    public function test_get_modules_by_slugs(): void
    {
        Module::factory()->create(['slug' => 'module-1']);
        Module::factory()->create(['slug' => 'module-2']);
        Module::factory()->create(['slug' => 'module-3']);

        $modules = $this->repository->getModulesBySlug(['module-1', 'module-2']);
        $this->assertEquals(2, $modules->count());
        $this->assertTrue($modules->pluck('slug')->contains('module-1'));
        $this->assertTrue($modules->pluck('slug')->contains('module-2'));
    }

    /**
     * Test module relationships are loaded
     */
    public function test_module_relationships_loaded(): void
    {
        $module = Module::factory()->create();

        $foundModule = $this->repository->find($module->id);
        $this->assertTrue($foundModule->relationLoaded('tenants'));
    }
}
