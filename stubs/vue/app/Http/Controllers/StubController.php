<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stub\DeleteStubRequest;
use App\Http\Requests\Stub\StoreStubRequest;
use App\Http\Requests\Stub\UpdateStubRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Stub;
use Throwable;

class StubController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(): Response
	{
		$this->authorize("viewAny", Stub::class);

		return Inertia::render("Index", [
			"stubs" => Stub::all(),
			"status" => session("status"),
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): Response
	{
		$this->authorize("create", Stub::class);

		return Inertia::render("Create", [
			"status" => session("status"),
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreStubRequest $request): RedirectResponse
	{
		$this->authorize("store", [Stub::class, $request->validated()]);

		$response = redirect()->route("stubs.index");

		try {
			DB::transaction(function () use ($request) {
				Stub::create($request->validated());
			});

			$response->with("message", [
				"severity" => "success",
				"content" => __("Stub has been created successfully !"),
			]);
		} catch (Throwable $th) {
			$response->with("message", [
				"severity" => "error",
				"content" => __("Failed to create stub"),
			]);
		} finally {
			return $response;
		}
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Stub $stub): Response
	{
		$this->authorize("view", $stub);

		return Inertia::render("Show", [
			"stub" => $stub,
			"status" => session("status"),
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Stub $stub): Response
	{
		$this->authorize("edit", $stub);

		return Inertia::render("Edit", [
			"stub" => $stub,
			"status" => session("status"),
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(
		UpdateStubRequest $request,
		Stub $stub
	): RedirectResponse {
		$this->authorize("update", [$stub, $request->validated()]);

		$response = redirect()->route("stubs.index");

		try {
			DB::transaction(function () use ($request, $stub) {
				$stub->fill($request->validated());
				$stub->save();
			});

			$response->with("message", [
				"severity" => "success",
				"content" => __("Stub has been updated successfully !"),
			]);
		} catch (Throwable $th) {
			$response->with("message", [
				"severity" => "error",
				"content" => __("Failed to update stub"),
			]);
		} finally {
			return $response;
		}
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(DeleteStubRequest $request): RedirectResponse
	{
		$this->authorize("delete", [Stub::class, $request->ids]);

		$response = redirect()->route("stubs.index");

		try {
			DB::transaction(function () use ($request) {
				Stub::whereIn("id", $request->ids)->delete();
			});

			$response->with("message", [
				"severity" => "success",
				"content" => __("Stubs have been deleted successfully !"),
			]);
		} catch (Throwable $th) {
			$response->with("message", [
				"severity" => "error",
				"content" => __("Failed to delete stubs"),
			]);
		} finally {
			return $response;
		}
	}
}
