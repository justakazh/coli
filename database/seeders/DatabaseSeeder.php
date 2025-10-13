<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => env('AUTH_NAME'),
            'email' => env('AUTH_EMAIL'),
            'password' => bcrypt(env('AUTH_PASSWORD'))
        ]);
        \App\Models\Workflows::create([
            'name' => 'Simple Scan',
            'slug' => 'simple-scan',
            'type' => 'diagram',
            'description' => '	Perform Subdomain finder, web server check, and port/service check',
            'diagram_data' => '{"drawflow":{"Home":{"data":{"1":{"id":1,"name":"task","data":{"name":"Subdomain Finder","description":"Perform Subdomain Finder using Subfinder","command":"subfinder -d {target} -o {result}","result":"subdomains.txt"},"class":"task","html":"<div class=\"task-node\" style=\"color: #fff; padding: 18px 14px 40px 14px;  position: relative; min-width: 180px; min-height: 78px; box-shadow: 0 2px 12px 0 rgba(30,40,60,.16);\">\n        <div style=\"text-align: center; margin: 0 0 16px 0;\">\n            <span style=\"font-size: 1.1rem; display: inline-flex; align-items: center; gap: 0.30em;\">\n                <i class=\"fas fa-terminal me-1\" style=\"color: #43b993;\"></i>\n                <strong class=\"text-truncate\" style=\"max-width: 135px;display:inline-block;vertical-align:middle;\">subfinder</strong>\n            </span>\n        </div>\n        <div class=\"node-action-btns d-flex gap-2\" style=\"position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%);\">\n            <button type=\"button\" class=\"btn-edit-task btn btn-sm btn-primary px-2 py-1 rounded-circle\" title=\"Edit Task\" data-node-id=\"\" style=\"box-shadow: none;\">\n                <i class=\"fas fa-pencil-alt\"></i>\n            </button>\n            <button type=\"button\" class=\"btn-delete-task btn btn-sm btn-primary px-2 py-1 rounded-circle\" title=\"Delete Task\" data-node-id=\"\" style=\"box-shadow: none;\">\n                <i class=\"fas fa-trash-alt\"></i>\n            </button>\n        </div>\n    </div>","typenode":false,"inputs":{"input_1":{"connections":[]}},"outputs":{"output_1":{"connections":[{"node":"2","output":"input_1"}]}},"pos_x":163,"pos_y":109},"2":{"id":2,"name":"task","data":{"name":"HTTP Check","description":"Looking for Web Server using HTTPX","command":"httpx -l {parent_result} -o {result}","result":"web_server.txt"},"class":"task","html":"<div class=\"task-node\" style=\"color: #fff; padding: 18px 14px 40px 14px;  position: relative; min-width: 180px; min-height: 78px; box-shadow: 0 2px 12px 0 rgba(30,40,60,.16);\">\n        <div style=\"text-align: center; margin: 0 0 16px 0;\">\n            <span style=\"font-size: 1.1rem; display: inline-flex; align-items: center; gap: 0.30em;\">\n                <i class=\"fas fa-terminal me-1\" style=\"color: #43b993;\"></i>\n                <strong class=\"text-truncate\" style=\"max-width: 135px;display:inline-block;vertical-align:middle;\">httpx</strong>\n            </span>\n        </div>\n        <div class=\"node-action-btns d-flex gap-2\" style=\"position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%);\">\n            <button type=\"button\" class=\"btn-edit-task btn btn-sm btn-primary px-2 py-1 rounded-circle\" title=\"Edit Task\" data-node-id=\"\" style=\"box-shadow: none;\">\n                <i class=\"fas fa-pencil-alt\"></i>\n            </button>\n            <button type=\"button\" class=\"btn-delete-task btn btn-sm btn-primary px-2 py-1 rounded-circle\" title=\"Delete Task\" data-node-id=\"\" style=\"box-shadow: none;\">\n                <i class=\"fas fa-trash-alt\"></i>\n            </button>\n        </div>\n    </div>","typenode":false,"inputs":{"input_1":{"connections":[{"node":"1","input":"output_1"}]}},"outputs":{"output_1":{"connections":[]}},"pos_x":813,"pos_y":165},"3":{"id":3,"name":"task","data":{"name":"Port Scanning","description":"Perform Port Scanning using NMAP","command":"nmap -sV {target} -oN {result}","result":"services.txt"},"class":"task","html":"<div class=\"task-node\" style=\"color: #fff; padding: 18px 14px 40px 14px;  position: relative; min-width: 180px; min-height: 78px; box-shadow: 0 2px 12px 0 rgba(30,40,60,.16);\">\n        <div style=\"text-align: center; margin: 0 0 16px 0;\">\n            <span style=\"font-size: 1.1rem; display: inline-flex; align-items: center; gap: 0.30em;\">\n                <i class=\"fas fa-terminal me-1\" style=\"color: #43b993;\"></i>\n                <strong class=\"text-truncate\" style=\"max-width: 135px;display:inline-block;vertical-align:middle;\">nmap</strong>\n            </span>\n        </div>\n        <div class=\"node-action-btns d-flex gap-2\" style=\"position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%);\">\n            <button type=\"button\" class=\"btn-edit-task btn btn-sm btn-primary px-2 py-1 rounded-circle\" title=\"Edit Task\" data-node-id=\"\" style=\"box-shadow: none;\">\n                <i class=\"fas fa-pencil-alt\"></i>\n            </button>\n            <button type=\"button\" class=\"btn-delete-task btn btn-sm btn-primary px-2 py-1 rounded-circle\" title=\"Delete Task\" data-node-id=\"\" style=\"box-shadow: none;\">\n                <i class=\"fas fa-trash-alt\"></i>\n            </button>\n        </div>\n    </div>","typenode":false,"inputs":{"input_1":{"connections":[]}},"outputs":{"output_1":{"connections":[]}},"pos_x":192,"pos_y":354}}}}}',
            'script_path' => env("WORKDIR")."/workflows/simple-scan.json",
        ]);

        \App\Models\Utils::create([
            'name' => 'console',
            'state' => 'inactive',
        ]);


    }
}
