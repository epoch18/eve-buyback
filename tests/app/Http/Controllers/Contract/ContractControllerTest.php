<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContractControllerTest extends TestCase {
	use DatabaseMigrations;

	private $headers;

	public function setUp()
	{
		parent::setUp();

		$this->headers = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];

		$user = factory(App\Models\User::class)->make();
		$user->setContractor(true)->save();
		auth()->login($user);
	}

	public function testMustBeLoggedIn()
	{
		auth()->logout();

		$this->get('/contract')->assertResponseStatus(302);
	}

	public function testMustBeContractor()
	{
		$user = auth()->user();
		$user->flags = ~\App\Models\User::CONTRACTOR - \App\Models\User::ADMINISTRATOR;
		$user->save();

		$this->get('/contract')->assertResponseStatus(401);
	}

	public function testUpdateContracts()
	{
		$dispatcher = Mockery::mock(\Illuminate\Bus\Dispatcher::class);
		$this->app->instance(\Illuminate\Bus\Dispatcher::class, $dispatcher);

		$job = Mockery::mock(\App\Jobs\UpdateContractsJob::class);
		$this->app->instance(\App\Jobs\UpdateContractsJob::class, $job);

		$dispatcher->shouldReceive('dispatchNow')->once()->with($job);

		$this->post('/contract/update', [], $this->headers);

		$this->assertResponseStatus(200);

		$this->seeJsonStructure(['result', 'message']);

		$this->seeJson(['result' => true]);
	}
}
