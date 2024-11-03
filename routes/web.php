<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;


// projects routes
Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

// Nested resource routes for tasks within projects
Route::resource('projects.tasks', TaskController::class)
    ->except(['create', 'edit', 'show']
);

// Special route for priority updates
Route::patch('projects/{project}/tasks/{task}/priority',
    [TaskController::class, 'updatePriority']
)->name('projects.tasks.priority');

