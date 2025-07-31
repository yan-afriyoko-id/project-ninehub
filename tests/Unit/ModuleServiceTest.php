<?php

namespace Tests\Unit;

use App\Models\Module;
use App\Services\ModuleService;
use App\Services\Interfaces\ModuleServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModuleServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private ModuleService $moduleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->moduleService = app(ModuleService::class);
    }

    /**
     * Test getting all modules
     */
    public function test_get_all_modules(): void
    {
        Module::factory()->count(3)->create();

        $modules = $this->moduleService->getAllModules();

        $this->assertEquals(3, $modules->count());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $modules);
    }

    /**
     * Test finding module by ID
     */
    public function test_find_module_by_id(): void
    {
        $module = Module::factory()->create();

        $foundModule = $this->moduleService->getModuleById($module->id);
        $this->assertNotNull($foundModule);
        $this->assertEquals($module->id, $foundModule->id);

        $notFoundModule = $this->moduleService->getModuleById(999);
        $this->assertNull($notFoundModule);
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

        $module = $this->moduleService->createModule($moduleData);

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

        $updatedModule = $this->moduleService->updateModule($module->id, $updateData);

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

        $result = $this->moduleService->deleteModule($module->id);

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

        $activeModules = $this->moduleService->getActiveModules();
        $this->assertEquals(3, $activeModules->count());
    }

    /**
     * Test getting public modules
     */
    public function test_get_public_modules(): void
    {
        Module::factory()->count(2)->public()->create();
        Module::factory()->count(3)->private()->create();

        $publicModules = $this->moduleService->getPublicModules();
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

        $orderedModules = $this->moduleService->getModulesByOrder();
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

        $searchResults = $this->moduleService->searchModules('Test');
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

        $modules = $this->moduleService->getModulesBySlug(['module-1', 'module-2']);
        $this->assertEquals(2, $modules->count());
        $this->assertTrue($modules->pluck('slug')->contains('module-1'));
        $this->assertTrue($modules->pluck('slug')->contains('module-2'));
    }

    /**
     * Test syncing module permissions
     */
    public function test_sync_module_permissions(): void
    {
        $module = Module::factory()->create([
            'slug' => 'test-module',
            'permissions' => ['view', 'create', 'edit'],
        ]);

        $result = $this->moduleService->syncModulePermissions($module->id);

        $this->assertTrue($result);

        // Check if permissions were created
        $this->assertDatabaseHas('permissions', [
            'name' => 'test-module.view',
            'guard_name' => 'web',
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'test-module.create',
            'guard_name' => 'web',
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'test-module.edit',
            'guard_name' => 'web',
        ]);
    }

    /**
     * Test module not found exception
     */
    public function test_module_not_found_exception(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->moduleService->updateModule(999, ['name' => 'Test']);
    }
}
