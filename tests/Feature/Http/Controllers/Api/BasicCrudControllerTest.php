<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;
use ReflectionClass;

class BasicCrudControllerTest extends TestCase
{
    /**
     * @var CategoryControllerStub
     */
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub;
    }

    /**
     * @throws \Throwable
     */
    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex()
    {
        $category = CategoryStub::create(['name' => 'Test name', 'description' => 'Teste description']);
        $this->assertEquals([$category->toArray()], $this->controller->index()->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function testInvalidationDataInStore()
    {
        $this->expectException(ValidationException::class);
        $request = $this->mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name' => '']);
        $this->controller->store($request);
    }

    /**
     * @throws ValidationException
     */
    public function testStore()
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name' => 'test name', 'description' => 'teste description']);
        $model = $this->controller->store($request);
        $this->assertEquals(CategoryStub::find(1)->toArray(), $model->toArray());
    }

    public function testIfFindOrFailFetchModel()
    {
        $category = CategoryStub::create(['name' => 'Test name', 'description' => 'Teste description']);
        $reflectClass = new ReflectionClass(BasicCrudController::class);
        $reflectMethod = $reflectClass->getMethod('findOrFail');
        $reflectMethod->setAccessible(true);
        $result = $reflectMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function testIfFindOrFailThrowException()
    {
        $this->expectException(ModelNotFoundException::class);
        $reflectClass = new ReflectionClass(BasicCrudController::class);
        $reflectMethod = $reflectClass->getMethod('findOrFail');
        $reflectMethod->setAccessible(true);
        $result = $reflectMethod->invokeArgs($this->controller, [0]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }
}
