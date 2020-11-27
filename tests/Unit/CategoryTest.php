<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * @var Category
     */
    private $category;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->category = new Category;
    }

    /**
     * @return void
     */
    public function testFillable()
    {
        $this->assertEquals($this->category->getFillable(), [
            'name',
            'description',
            'is_active'
        ]);
        $this->assertEquals($this->category->getCasts(), ['id' => 'string']);
        $this->assertEquals($this->category->getIncrementing(), false);
    }

    /**
     * @return void
     */
    public function testCasts()
    {
        $this->assertEquals($this->category->getCasts(), ['id' => 'string']);
        $this->assertEquals($this->category->getIncrementing(), false);
    }

    /**
     * @return void
     */
    public function testIncrementing()
    {
        $this->assertEquals($this->category->getIncrementing(), false);
    }

    /**
     * @return void
     */
    public function testDates()
    {
        $dates = ['created_at', 'updated_at', 'deleted_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->category->getDates());
        }
        $this->assertCount(count($dates), $this->category->getDates());
    }

    /**
     * @return void
     */
    public function testIfUseTratis()
    {
        $traits = [SoftDeletes::class, Uuid::class];
        $this->assertEquals($traits, array_keys(class_uses($this->category)));
    }
}
