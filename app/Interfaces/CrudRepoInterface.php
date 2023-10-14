<?php

namespace App\Interfaces;

interface CrudRepoInterface{
    public function store($data);
    // public function show();
    public function update($id, $request);
    // public function approvedOrders();
}
