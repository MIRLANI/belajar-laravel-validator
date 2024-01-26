<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FromControllerTest extends TestCase
{
    
    public function testLoginVailed()
    {
        $response = $this->post("/form/login", [
            "username" => "",
            "password" => ""
        ])
        ;
        $response->assertStatus(400);
    }
    
    public function testLoginSuccess()
    {
        $response = $this->post("/form/login", [
            "username" => "admin",
            "password" => "admin"
        ])
        ;
        $response->assertStatus(200);
    }

    public function testFromLoginVailed()
    {
        $response = $this->post("/form", [
            "username" => "",
            "password" => ""
        ])
        ;
        $response->assertStatus(302);
    }
    
    public function testFromSubmitLoginSuccess()
    {
        $response = $this->post("/form", [
            "username" => "admin",
            "password" => "admin"
        ])
        ;
        $response->assertStatus(200);
    }


}
