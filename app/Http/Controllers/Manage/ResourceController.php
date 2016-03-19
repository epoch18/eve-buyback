<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\SDE\InvCategory;
use App\Models\SDE\InvGroup;
use App\Models\SDE\InvType;
use Illuminate\Http\Request;

class ResourceController extends Controller {
	/**
	* @var \App\Models\InvCategory
	*/
	private $category_model;

	/**
	* @var \App\Models\InvGroup
	*/
	private $group_model;

	/**
	* @var \App\Models\InvType
	*/
	private $type_model;

	/**
	* @var \Illuminate\Http\Request
	*/
	private $request;

	/**
	* Constructs the class.
	* @param  \App\Models\SDE\InvCategory $category_model
	* @param  \App\Models\SDE\InvGroup    $group_model
	* @param  \App\Models\SDE\InvType     $type_model
	* @param  \Illuminate\Http\Request    $request
	*/
	public function __construct(
		InvCategory $category_model,
		InvGroup    $group_model,
		InvType     $type_model,
		Request     $request
	) {
		$this->category_model = $category_model;
		$this->group_model    = $group_model;
		$this->type_model     = $type_model;
		$this->request        = $request;
	}

	/**
	 * Gets a paginated list of categories.
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCategories()
	{
		$query = htmlspecialchars(strip_tags($this->request->input('query')));

		return $this->category_model
			->where('published', true)
			->where('categoryName', 'LIKE', "%{$query}%")
			->paginate(20);
	}

	/**
	 * Gets a paginated list of groups.
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getGroups()
	{
		$query = htmlspecialchars(strip_tags($this->request->input('query')));

		return $this->group_model
			->where('published', true)
			->where('groupName', 'LIKE', "%{$query}%")
			->paginate(20);
	}

	/**
	 * Gets a paginated list of types.
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getTypes()
	{
		$query = htmlspecialchars(strip_tags($this->request->input('query')));

		return $this->type_model
			->where('published', true)
			->where('typeName', 'LIKE', "%{$query}%")
			->paginate(20);
	}
}
