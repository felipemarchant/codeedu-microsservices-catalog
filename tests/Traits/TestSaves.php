<?php 

namespace Tests\Traits;

use Exception;
use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves
{
    protected function assertStore(array $sendData, array $testData)
    {
       /** @var TestResponse */
       $response = $this->json('POST', $this->routeStore(), $sendData);
       if ($response->status() !== 201) throw new Exception("Response status must be 201. give {$response->status()}:\n{$response->content()}");
       $model = $this->model();
       $table = (new $model)->getTable();
       $this->assertDatabaseHas($table, $testData + ['id' => $response->json('id')]);
    }
}
