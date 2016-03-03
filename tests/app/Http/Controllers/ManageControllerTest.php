<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageControllerTest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * @var App\Models\User
	 */
	private $user;

	public function setUp()
	{
		parent::setUp();

		$this->user = factory(App\Models\User::class)->make();
		$this->user->setAdministrator(true);
		$this->user->save();
	}

	public function testPostSystem()
	{
		/*auth()->login($this->user);

		$this->post('/manage/system', [
			'stations' => [30003631, 30003632],
		]);

		$this->assertResponseStatus(302);

		$this->assertRedirectedTo('/manage');*/
	}

	/*public function testUpdateApiKeyWhileNotAuthenticated()
	{
		$this->post('/manage/api-key', [
			'_method' => 'PUT',
			'keyid'   => 12345,
			'vcode'   => 'dsgahnfvJKFLAFfffvZHff',
		]);

		$this->assertResponseStatus(302);

		$this->assertRedirectedToRoute('login');
	}

	public function testUpdateApiKeyWhileAuthenticatedAndWithoutAdministrator()
	{
		$this->user->setAdministrator(false)->save();
		auth()->login($this->user);

		$this->post('/manage/api-key', [
			'_method' => 'PUT',
			'keyid'   => 12345,
			'vcode'   => 'dsgahnfvJKFLAFfffvZHff',
		]);

		$this->assertResponseStatus(401);
	}

	public function testUpdateApiKeyWhileAuthenticatedAndWithAdministrator()
	{
		$this->user->setAdministrator(true)->save();
		auth()->login($this->user);

		$this->post('/manage/api-key', [
			'_method' => 'PUT',
			'keyid'   => 12345,
			'vcode'   => 'dsgahnfvJKFLAFfffvZHff',
		], ['HTTP_REFERER' => route('manage.index')]);

		$this->assertResponseStatus(302);

		$this->assertRedirectedToRoute('manage.index');

		$this->assertSessionHas('success');
	}

	public function testUpdateApiKeyWhileAuthenticatedAndWithAdministratorWithErrors()
	{
		$this->user->setAdministrator(true)->save();
		auth()->login($this->user);

		$this->post('/manage/api-key', [
			'_method' => 'PUT',
		], ['HTTP_REFERER' => route('manage.index')]);

		$this->assertResponseStatus(302);

		$this->assertRedirectedToRoute('manage.index');

		$this->assertSessionHas('errors');
	}*/
}
